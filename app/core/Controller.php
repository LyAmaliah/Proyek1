<?php
/**
 * Base Controller — menangani rendering view + layout.
 *
 * Alur: content view (app/views/pages/...) di-render lebih dulu ke buffer,
 * lalu dibungkus layout (app/views/layouts/...) yang memuat
 * includes/header.php dan includes/footer.php.
 */
abstract class Controller
{
    /**
     * Render view.
     *
     * @param string $view   Jalur view relatif terhadap app/views/pages (mis. 'admin/artikel/index')
     * @param array  $data   Variabel yang diekstrak ke dalam view
     * @param string $layout Layout utama: 'main' (situs), 'admin' (dashboard), 'guest' (login)
     */
    protected function view(string $view, array $data = [], string $layout = 'main'): void
    {
        $contentFile = VIEWS_PATH . '/pages/' . $view . '.php';
        if (!is_file($contentFile)) {
            throw new RuntimeException('View tidak ditemukan: ' . $view);
        }

        extract($data, EXTR_SKIP);

        ob_start();
        require $contentFile;
        $content = ob_get_clean();

        $layoutFile = VIEWS_PATH . '/layouts/' . $layout . '.php';
        if (!is_file($layoutFile)) {
            throw new RuntimeException('Layout tidak ditemukan: ' . $layout);
        }
        require $layoutFile;
    }

    protected function redirect(string $url): void
    {
        redirect($url);
    }

    /** Redirect kembali ke halaman sebelumnya (HTTP_REFERER). */
    protected function back(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? BASE_URL . '/';
        redirect($referer);
    }

    /** Respons JSON (untuk endpoint AJAX opsional). */
    protected function json(array $payload, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /** Nilai input POST/GET aman (string, sudah trim). */
    protected function input(string $key, string $default = ''): string
    {
        $value = $_POST[$key] ?? $_GET[$key] ?? $default;
        return is_string($value) ? trim($value) : $default;
    }

    /** Input numerik sebagai float. */
    protected function inputFloat(string $key, float $default = 0.0): float
    {
        $value = $_POST[$key] ?? $_GET[$key] ?? $default;
        return is_numeric($value) ? (float) $value : $default;
    }
}
