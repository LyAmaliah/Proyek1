<?php
/**
 * Controller tempat sehat + ulasan & rating (FR-13).
 */
class TempatController extends Controller
{
    public function index(): void
    {
        $q = $this->input('q');

        $this->view('tempat/index', [
            'pageTitle' => 'Tempat Sehat',
            'activeNav' => 'tempat',
            'tempat'    => Tempat::denganRating($q ?: null),
            'q'         => $q,
        ]);
    }

    public function detail(string $id): void
    {
        $tempat = Tempat::detail((int) $id);
        if (!$tempat) {
            set_flash('danger', 'Tempat tidak ditemukan.');
            $this->redirect(BASE_URL . '/tempat');
        }

        $idPengguna  = Auth::userId();
        $sebaran     = Ulasan::sebaran((int) $tempat['id_tempat']);
        $totalUlasan = array_sum($sebaran);

        $this->view('tempat/detail', [
            'pageTitle'    => $tempat['nama_tempat'],
            'activeNav'    => 'tempat',
            'tempat'       => $tempat,
            'menu'         => Menu::byTempat((int) $tempat['id_tempat']),
            'ulasan'       => Ulasan::byTempat((int) $tempat['id_tempat']),
            'rataRata'     => Ulasan::rataRata((int) $tempat['id_tempat']),
            'sebaran'      => $sebaran,
            'totalUlasan'  => $totalUlasan,
            'sudahUlas'    => $idPengguna ? Ulasan::sudahMengulas($idPengguna, (int) $tempat['id_tempat']) : false,
        ]);
    }

    /** Kirim ulasan & rating (wajib login — FR-13). */
    public function ulasan(string $id): void
    {
        Auth::requireUser();

        $tempat = Tempat::find((int) $id);
        if (!$tempat) {
            set_flash('danger', 'Tempat tidak ditemukan.');
            $this->redirect(BASE_URL . '/tempat');
        }

        $rating   = (int) $this->input('rating');
        $komentar = $this->input('komentar');
        $idPengguna = Auth::userId();

        if ($rating < 1 || $rating > 5) {
            set_flash('danger', 'Pilih rating 1–5 bintang.');
            $this->redirect(BASE_URL . '/tempat/' . $tempat['id_tempat']);
        }
        if ($komentar === '' || mb_strlen($komentar) < 5) {
            set_flash('danger', 'Tulis ulasan minimal 5 karakter.');
            $this->redirect(BASE_URL . '/tempat/' . $tempat['id_tempat']);
        }
        if (Ulasan::sudahMengulas((int) $idPengguna, (int) $tempat['id_tempat'])) {
            set_flash('warning', 'Anda sudah memberikan ulasan untuk tempat ini.');
            $this->redirect(BASE_URL . '/tempat/' . $tempat['id_tempat']);
        }

        Ulasan::insert([
            'id_pengguna'   => $idPengguna,
            'id_tempat'     => $tempat['id_tempat'],
            'rating'        => $rating,
            'komentar'      => $komentar,
            'tanggal_ulasan' => date('Y-m-d H:i:s'),
        ]);

        set_flash('success', 'Terima kasih, ulasan Anda berhasil dikirim!');
        $this->redirect(BASE_URL . '/tempat/' . $tempat['id_tempat']);
    }
}
