<?php
/**
 * FatSecretService — Integrasi FatSecret Platform REST API (OAuth 2.0).
 *
 * Menggunakan OAuth 2.0 Client Credentials Grant:
 * 1. Mendapatkan access token dari https://oauth.fatsecret.com/connect/token
 * 2. Mencari makanan via method foods.search
 * 3. Mengambil rincian takaran gizi via food.get.v5 / food.get.v4
 * 4. Menyimpan token di cache file agar tidak request token berulang-ulang
 */
class FatSecretService
{
    private string $clientId;
    private string $clientSecret;
    private string $tokenUrl = 'https://oauth.fatsecret.com/connect/token';
    private string $apiUrl = 'https://platform.fatsecret.com/rest/server.api';
    private string $cacheFile;

    public function __construct(?string $clientId = null, ?string $clientSecret = null)
    {
        $this->clientId     = $clientId     ?? (defined('FATSECRET_CLIENT_ID') ? FATSECRET_CLIENT_ID : '');
        $this->clientSecret = $clientSecret ?? (defined('FATSECRET_CLIENT_SECRET') ? FATSECRET_CLIENT_SECRET : '');
        $this->cacheFile    = sys_get_temp_dir() . '/healthyhabit_fatsecret_token.json';
    }

    /**
     * Memeriksa apakah kredensial FatSecret API sudah dikonfigurasi.
     */
    public function isConfigured(): bool
    {
        return !empty($this->clientId) && !empty($this->clientSecret)
            && $this->clientId !== 'MASUKKAN_CLIENT_ID_DI_SINI'
            && $this->clientSecret !== 'MASUKKAN_CLIENT_SECRET_DI_SINI';
    }

    /**
     * Mendapatkan Access Token (OAuth 2.0 Client Credentials Grant) dengan caching.
     *
     * @throws RuntimeException Jika autentikasi gagal
     */
    public function getAccessToken(): string
    {
        if (!$this->isConfigured()) {
            throw new RuntimeException('Kredensial FatSecret API belum dikonfigurasi di config.php (FATSECRET_CLIENT_ID & FATSECRET_CLIENT_SECRET).');
        }

        // 1. Cek token di cache file jika masih berlaku (dengan buffer 60 detik)
        if (is_file($this->cacheFile)) {
            $cached = json_decode(@file_get_contents($this->cacheFile), true);
            if (is_array($cached) && !empty($cached['access_token']) && !empty($cached['expires_at'])) {
                if (time() < ($cached['expires_at'] - 60)) {
                    return $cached['access_token'];
                }
            }
        }

        // 2. Request token baru ke FatSecret via cURL
        $ch = curl_init($this->tokenUrl);
        $basicAuth = base64_encode($this->clientId . ':' . $this->clientSecret);

        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Basic ' . $basicAuth,
                'Content-Type: application/x-www-form-urlencoded',
            ],
            CURLOPT_POSTFIELDS     => http_build_query([
                'grant_type' => 'client_credentials',
                'scope'      => 'basic',
            ]),
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            throw new RuntimeException('Gagal terhubung ke OAuth FatSecret: ' . $curlError);
        }

        $data = json_decode((string) $response, true);

        if ($httpCode !== 200 || empty($data['access_token'])) {
            $errorMsg = $data['error_description'] ?? $data['error'] ?? 'Autentikasi OAuth 2.0 gagal (HTTP ' . $httpCode . ')';
            if (stripos($errorMsg, 'unauthorized') !== false || $httpCode === 401 || $httpCode === 400) {
                $errorMsg .= '. Pastikan Client ID & Secret benar, dan alamat IP Anda sudah didaftarkan pada OAuth 2.0 IP Allowlist di dashboard FatSecret.';
            }
            throw new RuntimeException($errorMsg);
        }

        // 3. Simpan token ke cache file
        $expiresIn = (int) ($data['expires_in'] ?? 86400);
        $cacheData = [
            'access_token' => $data['access_token'],
            'expires_at'   => time() + $expiresIn,
        ];
        @file_put_contents($this->cacheFile, json_encode($cacheData));

        return $data['access_token'];
    }

    /**
     * Mencari makanan berdasarkan kata kunci menggunakan foods.search.
     *
     * @param string $query       Nama makanan (mis. "nasi goreng", "apel")
     * @param int    $pageNumber  Halaman hasil (mulai dari 0)
     * @param int    $maxResults  Jumlah hasil per halaman (maks 50)
     * @return array              Daftar makanan yang sudah dinormalisasi
     */
    public function searchFoods(string $query, int $pageNumber = 0, int $maxResults = 20): array
    {
        $query = trim($query);
        if ($query === '') {
            return ['total' => 0, 'page' => 0, 'foods' => []];
        }

        $token = $this->getAccessToken();

        $params = [
            'method'            => 'foods.search',
            'search_expression' => $query,
            'page_number'       => $pageNumber,
            'max_results'       => $maxResults,
            'format'            => 'json',
        ];

        $url = $this->apiUrl . '?' . http_build_query($params);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_HTTPGET        => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json',
            ],
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            throw new RuntimeException('Gagal memanggil API foods.search: ' . $curlError);
        }

        $data = json_decode((string) $response, true);

        if ($httpCode !== 200 || !isset($data['foods'])) {
            $msg = $data['error']['message'] ?? 'Respons tidak valid dari FatSecret API (HTTP ' . $httpCode . ')';
            throw new RuntimeException($msg);
        }

        $foodsContainer = $data['foods'];
        $totalResults   = (int) ($foodsContainer['total_results'] ?? 0);
        $page           = (int) ($foodsContainer['page_number'] ?? 0);
        $rawFoodList    = $foodsContainer['food'] ?? [];

        // Normalisasi: jika hanya 1 makanan, FatSecret mengembalikan objek tunggal (bukan array of objek)
        if (isset($rawFoodList['food_id'])) {
            $rawFoodList = [$rawFoodList];
        } elseif (!is_array($rawFoodList)) {
            $rawFoodList = [];
        }

        $normalizedFoods = [];
        foreach ($rawFoodList as $item) {
            $parsed = $this->parseFoodDescription($item['food_description'] ?? '');

            $normalizedFoods[] = [
                'food_id'          => $item['food_id'] ?? '',
                'food_name'        => $item['food_name'] ?? '',
                'food_type'        => $item['food_type'] ?? 'Generic',
                'brand_name'       => $item['brand_name'] ?? null,
                'food_url'         => $item['food_url'] ?? '',
                'food_description' => $item['food_description'] ?? '',
                // Nilai nutrisi hasil ekstraksi cepat:
                'portion'          => $parsed['portion'],
                'kalori'           => $parsed['calories'],
                'lemak'            => $parsed['fat'],
                'karbohidrat'      => $parsed['carbs'],
                'protein'          => $parsed['protein'],
            ];
        }

        return [
            'total' => $totalResults,
            'page'  => $page,
            'foods' => $normalizedFoods,
        ];
    }

    /**
     * Mengambil detail lengkap makanan beserta takaran porsi (servings).
     * Menggunakan food.get.v5 (atau food.get.v4).
     *
     * @param string|int $foodId
     * @return array
     */
    public function getFood(string|int $foodId): array
    {
        $token = $this->getAccessToken();

        $params = [
            'method'  => 'food.get.v5',
            'food_id' => (string) $foodId,
            'format'  => 'json',
        ];

        $url = $this->apiUrl . '?' . http_build_query($params);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_HTTPGET        => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json',
            ],
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            throw new RuntimeException('Gagal memanggil API food.get: ' . $curlError);
        }

        $data = json_decode((string) $response, true);

        if ($httpCode !== 200 || !isset($data['food'])) {
            $msg = $data['error']['message'] ?? 'Gagal mengambil detail makanan (HTTP ' . $httpCode . ')';
            throw new RuntimeException($msg);
        }

        $food = $data['food'];
        $rawServings = $food['servings']['serving'] ?? [];

        // Normalisasi: jika hanya 1 serving, jadikan array
        if (isset($rawServings['serving_id'])) {
            $rawServings = [$rawServings];
        } elseif (!is_array($rawServings)) {
            $rawServings = [];
        }

        $servings = [];
        foreach ($rawServings as $srv) {
            $servings[] = [
                'serving_id'            => $srv['serving_id'] ?? '',
                'serving_description'   => $srv['serving_description'] ?? '1 porsi',
                'metric_serving_amount' => isset($srv['metric_serving_amount']) ? (float) $srv['metric_serving_amount'] : null,
                'metric_serving_unit'   => $srv['metric_serving_unit'] ?? 'g',
                'number_of_units'       => isset($srv['number_of_units']) ? (float) $srv['number_of_units'] : 1.0,
                'measurement_description' => $srv['measurement_description'] ?? '',
                'is_default'            => !empty($srv['is_default']),
                'calories'              => (float) ($srv['calories'] ?? 0),
                'carbohydrate'          => (float) ($srv['carbohydrate'] ?? 0),
                'protein'               => (float) ($srv['protein'] ?? 0),
                'fat'                   => (float) ($srv['fat'] ?? 0),
                'fiber'                 => (float) ($srv['fiber'] ?? 0),
                'sugar'                 => (float) ($srv['sugar'] ?? 0),
                'sodium'                => (float) ($srv['sodium'] ?? 0),
            ];
        }

        return [
            'food_id'    => $food['food_id'] ?? '',
            'food_name'  => $food['food_name'] ?? '',
            'food_type'  => $food['food_type'] ?? 'Generic',
            'brand_name' => $food['brand_name'] ?? null,
            'food_url'   => $food['food_url'] ?? '',
            'servings'   => $servings,
        ];
    }

    /**
     * Menghitung total nutrisi berdasarkan takaran serving dan porsi/gram yang dikonsumsi.
     *
     * @param array $serving Array serving dari getFood()
     * @param float $multiplier Faktor pengali (mis. 1.5 porsi, atau gram_input / metric_serving_amount)
     * @return array
     */
    public static function hitungNutrisi(array $serving, float $multiplier = 1.0): array
    {
        $multiplier = max(0.0, $multiplier);
        return [
            'kalori'      => round(($serving['calories'] ?? 0) * $multiplier, 1),
            'karbohidrat' => round(($serving['carbohydrate'] ?? 0) * $multiplier, 1),
            'protein'     => round(($serving['protein'] ?? 0) * $multiplier, 1),
            'lemak'       => round(($serving['fat'] ?? 0) * $multiplier, 1),
            'serat'       => round(($serving['fiber'] ?? 0) * $multiplier, 1),
            'gula'        => round(($serving['sugar'] ?? 0) * $multiplier, 1),
            'sodium'      => round(($serving['sodium'] ?? 0) * $multiplier, 1),
        ];
    }

    /**
     * Parsing deskripsi bawaan FatSecret dan menormalisasikannya ke nilai dasar per 100 gram.
     * Contoh: "Per 1122g - Calories: 1850kcal | Fat: 65.44g | Carbs: 240.17g | Protein: 69.07g"
     * -> Dikonversi menjadi nilai per 100 gram: ~165 kkal, 5.8g lemak, 21.4g karbo, 6.2g protein!
     */
    private function parseFoodDescription(string $desc): array
    {
        $result = [
            'portion'     => '100 gram',
            'raw_portion' => '100g',
            'calories'    => 0.0,
            'fat'         => 0.0,
            'carbs'       => 0.0,
            'protein'     => 0.0,
        ];

        if (empty($desc)) {
            return $result;
        }

        $baseGrams = 100.0;
        if (preg_match('/^(?:Per\s+)?([^-\|]+)\s*-\s*/i', $desc, $mPortion)) {
            $rawPortion = trim($mPortion[1]);
            $result['raw_portion'] = $rawPortion;

            // Cek apakah ada angka gram atau ml (mis. 1122g, 300g, 150ml, dsb)
            if (preg_match('/([0-9\.]+)\s*(?:g|gram)\b/i', $rawPortion, $mG)) {
                $baseGrams = (float) $mG[1];
            } elseif (preg_match('/([0-9\.]+)\s*ml\b/i', $rawPortion, $mMl)) {
                $baseGrams = (float) $mMl[1];
            }
        }

        $rawCalories = 0.0;
        if (preg_match('/Calories:\s*([0-9\.]+)\s*kcal/i', $desc, $mCal)) {
            $rawCalories = (float) $mCal[1];
        }

        $rawFat = 0.0;
        if (preg_match('/Fat:\s*([0-9\.]+)\s*g/i', $desc, $mFat)) {
            $rawFat = (float) $mFat[1];
        }

        $rawCarb = 0.0;
        if (preg_match('/Carbs:\s*([0-9\.]+)\s*g/i', $desc, $mCarb)) {
            $rawCarb = (float) $mCarb[1];
        }

        $rawProt = 0.0;
        if (preg_match('/Protein:\s*([0-9\.]+)\s*g/i', $desc, $mProt)) {
            $rawProt = (float) $mProt[1];
        }

        // Normalisasi ke per 100 gram jika takaran dasar bukan 100g
        if ($baseGrams > 0 && abs($baseGrams - 100.0) > 0.01) {
            $scale = 100.0 / $baseGrams;
            $result['calories'] = round($rawCalories * $scale, 1);
            $result['fat']      = round($rawFat * $scale, 1);
            $result['carbs']    = round($rawCarb * $scale, 1);
            $result['protein']  = round($rawProt * $scale, 1);
        } else {
            $result['calories'] = $rawCalories;
            $result['fat']      = $rawFat;
            $result['carbs']    = $rawCarb;
            $result['protein']  = $rawProt;
        }

        return $result;
    }
}
