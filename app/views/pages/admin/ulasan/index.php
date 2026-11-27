<?php
/** @var array $ulasan */
if (!function_exists('bintang_tampil')) {
    function bintang_tampil($rating): string
    {
        $html = '';
        $r = (int) round((float) $rating);
        for ($i = 1; $i <= 5; $i++) {
            $html .= $i <= $r
                ? '<i class="bi bi-star-fill"></i>'
                : '<i class="bi bi-star muted"></i>';
        }
        return $html;
    }
}
?>
<div class="panel-white">
    <h5 class="mb-3">
        <i class="bi bi-chat-left-text me-2 text-success"></i>Kelola Ulasan
        <span class="small text-muted fw-normal">(<?= count($ulasan) ?> ulasan)</span>
    </h5>

    <?php if (!$ulasan): ?>
        <p class="text-muted small mb-0">Belum ada ulasan.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Pengguna</th>
                        <th>Tempat</th>
                        <th>Rating</th>
                        <th>Komentar</th>
                        <th>Tanggal</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($ulasan as $u): ?>
                    <tr>
                        <td>
                            <div class="fw-semibold"><?= e($u['nama_lengkap']) ?></div>
                            <small class="text-muted">@<?= e($u['username']) ?></small>
                        </td>
                        <td><?= e($u['nama_tempat']) ?></td>
                        <td class="stars small text-nowrap"><?= bintang_tampil($u['rating']) ?></td>
                        <td class="small"><?= nl2br(e($u['komentar'])) ?></td>
                        <td class="small text-nowrap"><?= tanggal_indo($u['tanggal_ulasan']) ?></td>
                        <td class="text-end">
                            <form action="<?= BASE_URL ?>/admin/ulasan/<?= (int) $u['id_usulan_rating'] ?>/delete"
                                  method="post"
                                  onsubmit="return confirm('Hapus ulasan ini? Tindakan tidak bisa dibatalkan.')">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash me-1"></i>Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>