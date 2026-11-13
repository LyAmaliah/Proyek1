<?php
/**
 * Form tambah / edit artikel.
 * @var array|null $artikel @var array $kategori
 */
$edit = !empty($artikel);
$action = $edit
    ? BASE_URL . '/admin/artikel/' . (int) $artikel['id_artikel'] . '/edit'
    : BASE_URL . '/admin/artikel/create';
?>
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h1 class="h4 mb-0"><?= $edit ? 'Edit Artikel' : 'Tambah Artikel Baru' ?></h1>
    </div>
    <a href="<?= BASE_URL ?>/admin/artikel" class="btn btn-outline-success btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

<form action="<?= $action ?>" method="post" enctype="multipart/form-data" class="panel-white">
    <?= csrf_field() ?>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="mb-3">
                <label for="judul_artikel" class="form-label fw-semibold">Judul Artikel</label>
                <input type="text" class="form-control" id="judul_artikel" name="judul_artikel"
                       minlength="5" required
                       value="<?= e($artikel['judul_artikel'] ?? ($_POST['judul_artikel'] ?? '')) ?>"
                       placeholder="mis. 7 Kebiasaan Sehat yang Mudah Dijalankan">
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="id_kategori_artikel" class="form-label fw-semibold">Kategori</label>
                    <select class="form-select" id="id_kategori_artikel" name="id_kategori_artikel" required>
                        <option value="">— Pilih Kategori —</option>
                        <?php foreach ($kategori as $k): ?>
                            <option value="<?= (int) $k['id_kategori_artikel'] ?>"
                                <?= (int) ($artikel['id_kategori_artikel'] ?? ($_POST['id_kategori_artikel'] ?? 0))
                                    === (int) $k['id_kategori_artikel'] ? 'selected' : '' ?>>
                                <?= e($k['nama_kategori']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="form-text mb-0">Pilih kategori untuk artikel ini.</div>
                        <button type="button" class="btn btn-sm btn-link text-success p-0"
                                data-bs-toggle="modal" data-bs-target="#modalQuickKategori">
                            <i class="bi bi-plus-lg me-1"></i>Tambah Kategori Baru
                        </button>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="tanggal_terbit" class="form-label fw-semibold">Tanggal Terbit</label>
                    <input type="date" class="form-control" id="tanggal_terbit" name="tanggal_terbit"
                           value="<?= e($artikel['tanggal_terbit'] ?? date('Y-m-d')) ?>">
                </div>
            </div>

            <div class="mb-3">
                <label for="isi_artikel" class="form-label fw-semibold">Isi Artikel</label>
                <textarea class="form-control" id="isi_artikel" name="isi_artikel" rows="12" required
                          placeholder="Tulis isi artikel di sini… (setiap paragraf satu baris)"><?= e($artikel['isi_artikel'] ?? ($_POST['isi_artikel'] ?? '')) ?></textarea>
            </div>
        </div>

        <!-- Sidebar form -->
        <div class="col-lg-4">
            <div class="border rounded-3 p-3 mb-3" style="background:var(--abu);">
                <div class="form-section-title">Foto Artikel</div>

                <?php if ($edit && !empty($artikel['foto_artikel'])): ?>
                    <img src="<?= img_url($artikel['foto_artikel'], 'artikel') ?>" alt=""
                         class="img-fluid rounded-3 mb-2" style="width:100%;height:160px;object-fit:cover;">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="hapus_gambar"
                               id="hapus_gambar" value="1">
                        <label class="form-check-label small text-danger" for="hapus_gambar">
                            Hapus gambar saat ini
                        </label>
                    </div>
                <?php endif; ?>

                <input type="file" class="form-control" name="foto_artikel" accept="image/*">
                <div class="form-text">Format JPG/PNG/WEBP, maks 2 MB.</div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-green fw-semibold">
                    <i class="bi bi-save me-1"></i><?= $edit ? 'Simpan Perubahan' : 'Publikasikan Artikel' ?>
                </button>
                <a href="<?= BASE_URL ?>/admin/artikel" class="btn btn-outline-secondary">Batal</a>
            </div>
        </div>
    </div>
</form>

<!-- ===== Modal Quick Tambah Kategori ===== -->
<div class="modal fade" id="modalQuickKategori" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title mb-0"><i class="bi bi-plus-circle me-2"></i>Tambah Kategori Baru</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form action="<?= BASE_URL.'/admin/artikel/kategori/store' ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-0">
                        <label for="nama_kategori" class="form-label small fw-semibold">Nama Kategori</label>
                        <input type="text" class="form-control" id="nama_kategori" name="nama_kategori"
                               minlength="3" required placeholder="mis. Nutrisi Harian">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-green btn-sm fw-semibold">
                        <i class="bi bi-save me-1"></i>Simpan Kategori
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
