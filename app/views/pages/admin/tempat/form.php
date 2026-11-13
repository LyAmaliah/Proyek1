<?php
/**
 * Form tambah / edit tempat sehat — FR-05.
 * @var array|null $tempat
 */
$edit = !empty($tempat);
$action = $edit
    ? BASE_URL . '/admin/tempat/' . (int) $tempat['id_tempat'] . '/edit'
    : BASE_URL . '/admin/tempat/create';
$val = fn(string $k, $default = '') => $tempat[$k] ?? ($_POST[$k] ?? $default);
?>
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h1 class="h4 mb-1"><?= $edit ? 'Edit Tempat Sehat' : 'Tambah Tempat Sehat' ?></h1>
    </div>
    <a href="<?= BASE_URL ?>/admin/tempat" class="btn btn-outline-success btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

<form action="<?= $action ?>" method="post" enctype="multipart/form-data" class="panel-white">
    <?= csrf_field() ?>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="mb-3">
                <label for="nama_tempat" class="form-label fw-semibold">Nama Tempat</label>
                <input type="text" class="form-control" id="nama_tempat" name="nama_tempat"
                       minlength="3" required value="<?= e($val('nama_tempat')) ?>"
                       placeholder="mis. RealJuice Healthy Café">
            </div>

            <div class="mb-3">
                <label for="alamat" class="form-label fw-semibold">Alamat</label>
                <textarea class="form-control" id="alamat" name="alamat" rows="2" required
                          placeholder="Jalan…, Kecamatan…, Kota…"><?= e($val('alamat')) ?></textarea>
            </div>

            <div class="mb-3">
                <label for="deskripsi" class="form-label fw-semibold">Deskripsi</label>
                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4"
                          placeholder="Gambaran singkat tempat & konsep kulinernya…"><?= e($val('deskripsi')) ?></textarea>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="kontak" class="form-label fw-semibold">Kontak (Telepon/WA)</label>
                    <input type="text" class="form-control" id="kontak" name="kontak"
                           value="<?= e($val('kontak')) ?>" placeholder="mis. 0812-3456-7890">
                </div>
                <div class="col-md-6">
                    <label for="link_maps" class="form-label fw-semibold">Link Google Maps</label>
                    <input type="url" class="form-control" id="link_maps" name="link_maps"
                           value="<?= e($val('link_maps')) ?>" placeholder="https://maps.app.goo.gl/…">
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-4">
                    <label for="link_instagram" class="form-label fw-semibold">Link Instagram</label>
                    <input type="url" class="form-control" id="link_instagram" name="link_instagram"
                           value="<?= e($val('link_instagram')) ?>" placeholder="https://instagram.com/…">
                </div>
                <div class="col-md-4">
                    <label for="link_grabfood" class="form-label fw-semibold">Link GrabFood</label>
                    <input type="url" class="form-control" id="link_grabfood" name="link_grabfood"
                           value="<?= e($val('link_grabfood')) ?>" placeholder="https://grab.com/…">
                </div>
                <div class="col-md-4">
                    <label for="link_shopeefood" class="form-label fw-semibold">Link ShopeeFood</label>
                    <input type="url" class="form-control" id="link_shopeefood" name="link_shopeefood"
                           value="<?= e($val('link_shopeefood')) ?>" placeholder="https://shopee.co.id/…">
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <?php
            $adaFotoTempat = $edit && !empty($tempat['foto_tempat']);
            $fileFotoAda   = $adaFotoTempat
                && is_file(UPLOAD_PATH . '/tempat/' . basename($tempat['foto_tempat']));
            ?>
            <div class="border rounded-3 p-3 mb-3" style="background:var(--abu);">
                <div class="form-section-title">Foto Tempat</div>

                <div class="position-relative mb-2">
                    <img id="previewFotoTempat"
                         src="<?= $adaFotoTempat ? img_url($tempat['foto_tempat'], 'tempat') : BASE_URL . '/assets/img/placeholder.svg' ?>"
                         alt="Pratinjau foto tempat"
                         class="rounded-3"
                         style="width:100%;height:150px;object-fit:cover;background:#fff;border:1px solid #e3e8e4;">
                    <?php if ($adaFotoTempat): ?>
                        <span class="badge text-bg-light border position-absolute top-0 end-0 m-2"
                              style="font-size:.68rem;">
                            <i class="bi bi-image me-1"></i>Foto saat ini
                        </span>
                    <?php endif; ?>
                </div>

                <input type="file" class="form-control" id="foto_tempat" name="foto_tempat"
                       accept="image/jpeg,image/png,image/webp" data-preview="#previewFotoTempat"
                       data-preview-placeholder="<?= BASE_URL ?>/assets/img/placeholder.svg">
                <div class="form-text">
                    <?= $adaFotoTempat
                        ? 'Pilih file baru untuk mengganti foto lama.'
                        : 'Belum ada foto. Pilih JPG, PNG, atau WEBP (maks. 2 MB).' ?>
                </div>

                <?php if ($adaFotoTempat && !$fileFotoAda): ?>
                    <div class="alert alert-warning py-2 px-3 small mt-2 mb-0">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Nama file tercatat di database, tetapi filenya tidak ditemukan di server.
                        Unggah ulang foto atau centang opsi hapus di bawah.
                    </div>
                <?php endif; ?>

                <?php if ($adaFotoTempat): ?>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" value="1"
                               id="hapus_foto_tempat" name="hapus_foto_tempat">
                        <label class="form-check-label small text-danger" for="hapus_foto_tempat">
                            <i class="bi bi-trash me-1"></i>Hapus foto saat ini
                        </label>
                    </div>
                <?php endif; ?>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-green fw-semibold">
                    <i class="bi bi-save me-1"></i><?= $edit ? 'Simpan Perubahan' : 'Tambah Tempat' ?>
                </button>
                <?php if ($edit): ?>
                    <a href="<?= BASE_URL ?>/admin/tempat/<?= (int) $tempat['id_tempat'] ?>/menu"
                       class="btn btn-soft-green fw-semibold">
                        <i class="bi bi-bookmark-check me-1"></i>Kelola Menu
                    </a>
                <?php endif; ?>
                <a href="<?= BASE_URL ?>/admin/tempat" class="btn btn-outline-secondary">Batal</a>
            </div>
        </div>
    </div>
</form>
