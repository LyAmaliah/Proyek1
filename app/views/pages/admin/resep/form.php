<?php
/**
 * Form tambah / edit resep.
 * @var array|null $resep
 */
$edit = !empty($resep);
$action = $edit
    ? BASE_URL . '/admin/resep/' . (int) $resep['id_resep'] . '/edit'
    : BASE_URL . '/admin/resep/create';
$val = fn(string $k, $default = '') => $resep[$k] ?? ($_POST[$k] ?? $default);
?>
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h1 class="h4 mb-0"><?= $edit ? 'Edit Resep' : 'Tambah Resep Baru' ?></h1>
    </div>
    <a href="<?= BASE_URL ?>/admin/resep" class="btn btn-outline-success btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

<form action="<?= $action ?>" method="post" enctype="multipart/form-data" class="panel-white">
    <?= csrf_field() ?>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="mb-3">
                <label for="judul_resep" class="form-label fw-semibold">Judul Resep</label>
                <input type="text" class="form-control" id="judul_resep" name="judul_resep"
                       minlength="4" required value="<?= e($val('judul_resep')) ?>"
                       placeholder="mis. Salad Quinoa Ayam Panggang">
            </div>

            <div class="mb-3">
                <label for="deskripsi" class="form-label fw-semibold">Deskripsi Singkat</label>
                <input type="text" class="form-control" id="deskripsi" name="deskripsi" required
                       maxlength="255" value="<?= e($val('deskripsi')) ?>"
                       placeholder="Ringkasan resep yang menggugah selera…">
            </div>

            <div class="mb-3">
                <label for="bahan_bahan" class="form-label fw-semibold">Bahan-Bahan</label>
                <textarea class="form-control" id="bahan_bahan" name="bahan_bahan" rows="7" required
                          placeholder="Satu bahan per baris, mis:&#10;100 g quinoa&#10;150 dada ayam&#10;1 sdm minyak zaitun"><?= e($val('bahan_bahan')) ?></textarea>
                <div class="form-text">Tulis satu bahan di setiap baris.</div>
            </div>

            <div class="mb-3">
                <label for="langkah_pembuatan" class="form-label fw-semibold">Langkah Pembuatan</label>
                <textarea class="form-control" id="langkah_pembuatan" name="langkah_pembuatan" rows="8" required
                          placeholder="Satu langkah per baris, nomor urut otomatis dibuat sistem."><?= e($val('langkah_pembuatan')) ?></textarea>
                <div class="form-text">Tulis satu langkah di setiap baris.</div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="border rounded-3 p-3 mb-3" style="background:var(--abu);">
                <div class="form-section-title">Pengaturan Resep</div>

                <div class="row g-3">
                    <div class="col-6">
                        <label for="waktu_masak" class="form-label small fw-semibold">Waktu Masak (menit)</label>
                        <input type="number" min="1" class="form-control" id="waktu_masak"
                               name="waktu_masak" required value="<?= e($val('waktu_masak', 30)) ?>">
                    </div>
                    <div class="col-6">
                        <label for="total_kalori" class="form-label small fw-semibold">Total Kalori</label>
                        <input type="number" min="0" class="form-control" id="total_kalori"
                               name="total_kalori" required value="<?= e($val('total_kalori')) ?>">
                    </div>
                    <div class="col-12">
                        <label for="status_publikasi" class="form-label small fw-semibold">Status Publikasi</label>
                        <select class="form-select" id="status_publikasi" name="status_publikasi">
                            <?php $status = $val('status_publikasi', 'dipublikasikan'); ?>
                            <option value="dipublikasikan" <?= $status === 'dipublikasikan' ? 'selected' : '' ?>>
                                Dipublikasikan</option>
                            <option value="draft" <?= $status === 'draft' ? 'selected' : '' ?>>Draft</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="border rounded-3 p-3 mb-3" style="background:var(--abu);">
                <div class="form-section-title">Foto Resep</div>
                <?php if ($edit && !empty($resep['foto_resep'])): ?>
                    <img src="<?= img_url($resep['foto_resep'], 'resep') ?>" alt=""
                         class="img-fluid rounded-3 mb-2" style="width:100%;height:150px;object-fit:cover;">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="hapus_gambar"
                               id="hapus_gambar" value="1">
                        <label class="form-check-label small text-danger" for="hapus_gambar">
                            Hapus gambar saat ini
                        </label>
                    </div>
                <?php endif; ?>
                <input type="file" class="form-control" name="foto_resep" accept="image/*">
                <div class="form-text">JPG/PNG/WEBP, maks 2 MB.</div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-green fw-semibold">
                    <i class="bi bi-save me-1"></i><?= $edit ? 'Simpan Perubahan' : 'Simpan Resep' ?>
                </button>
                <a href="<?= BASE_URL ?>/admin/resep" class="btn btn-outline-secondary">Batal</a>
            </div>
        </div>
    </div>
</form>
