<?php
/**
 * Form tambah / edit data gizi.
 * @var array|null $gizi
 */
$edit = !empty($gizi);
$action = $edit
    ? BASE_URL . '/admin/gizi/' . (int) $gizi['id_gizi'] . '/edit'
    : BASE_URL . '/admin/gizi/create';
$val = fn(string $k, $default = '') => $gizi[$k] ?? ($_POST[$k] ?? $default);
?>
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h1 class="h4 mb-0"><?= $edit ? 'Edit Data Gizi' : 'Tambah Data Gizi' ?></h1>
    </div>
    <a href="<?= BASE_URL ?>/admin/gizi" class="btn btn-outline-success btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

<form action="<?= $action ?>" method="post" class="panel-white">
    <?= csrf_field() ?>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="mb-3">
                <label for="nama_makanan_minuman" class="form-label fw-semibold">Nama Makanan / Minuman</label>
                <input type="text" class="form-control" id="nama_makanan_minuman"
                       name="nama_makanan_minuman" required
                       value="<?= e($val('nama_makanan_minuman')) ?>"
                       placeholder="mis. Nasi Putih Matang">
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="kategori" class="form-label fw-semibold">Kategori</label>
                    <select class="form-select" id="kategori" name="kategori" required>
                        <option value="Makanan" <?= $val('kategori') === 'Makanan' ? 'selected' : '' ?>>Makanan</option>
                        <option value="Minuman" <?= $val('kategori') === 'Minuman' ? 'selected' : '' ?>>Minuman</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="porsi" class="form-label fw-semibold">Porsi Acuan</label>
                    <input type="text" class="form-control" id="porsi" name="porsi"
                           value="<?= e($val('porsi', '100 gram')) ?>" placeholder="100 gram">
                    <div class="form-text">Nilai acuan selalu per 100 gram.</div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="border rounded-3 p-3" style="background:var(--hijau-muda);">
                <div class="form-section-title">Nilai Gizi (per 100 g)</div>
                <div class="row g-3">
                    <div class="col-6">
                        <label for="kalori" class="form-label small fw-semibold">Kalori (kkal)</label>
                        <input type="number" step="0.1" min="0" class="form-control" id="kalori"
                               name="kalori" required value="<?= e($val('kalori')) ?>">
                    </div>
                    <div class="col-6">
                        <label for="karbohidrat" class="form-label small fw-semibold">Karbohidrat (g)</label>
                        <input type="number" step="0.1" min="0" class="form-control" id="karbohidrat"
                               name="karbohidrat" required value="<?= e($val('karbohidrat')) ?>">
                    </div>
                    <div class="col-6">
                        <label for="protein" class="form-label small fw-semibold">Protein (g)</label>
                        <input type="number" step="0.1" min="0" class="form-control" id="protein"
                               name="protein" required value="<?= e($val('protein')) ?>">
                    </div>
                    <div class="col-6">
                        <label for="lemak" class="form-label small fw-semibold">Lemak (g)</label>
                        <input type="number" step="0.1" min="0" class="form-control" id="lemak"
                               name="lemak" required value="<?= e($val('lemak')) ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2 mt-4">
        <button type="submit" class="btn btn-green fw-semibold px-4">
            <i class="bi bi-save me-1"></i><?= $edit ? 'Simpan Perubahan' : 'Tambah Data' ?>
        </button>
        <a href="<?= BASE_URL ?>/admin/gizi" class="btn btn-outline-secondary">Batal</a>
    </div>
</form>
