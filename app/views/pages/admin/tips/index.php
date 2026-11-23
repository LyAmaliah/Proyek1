<?php 
/** @var array $daftar @var int $hariIni */ 
$kategoriPilihan = ['Nutrisi', 'Olahraga', 'Diet Sehat', 'Kebugaran', 'Istirahat', 'Pola Makan', 'Pencernaan', 'Mental', 'Hidrasi', 'Higienis', 'Motivasi', 'Umum'];
?>
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h1 class="h4 mb-1">Kelola 31 Tips Sehat Harian</h1>
        <small class="text-muted">Tips berganti otomatis di footer sesuai tanggal kalender (1–31).</small>
    </div>
</div>

<div class="panel-white">
    <div class="table-responsive">
        <table class="table table-custom align-middle mb-0">
            <thead>
                <tr>
                    <th style="width:90px;" class="text-center">Hari Ke-</th>
                    <th style="width:130px;">Kategori</th>
                    <th style="width:200px;">Judul Tips</th>
                    <th>Isi Tips Harian</th>
                    <th style="width:80px;" class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($daftar as $t): ?>
                    <tr class="<?= (int)$t['hari_ke'] === $hariIni ? 'table-success' : '' ?>">
                        <td class="text-center fw-bold">
                            <span class="badge <?= (int)$t['hari_ke'] === $hariIni ? 'bg-success' : 'bg-secondary' ?> rounded-pill">
                                <?= (int)$t['hari_ke'] ?>
                            </span>
                            <?php if ((int)$t['hari_ke'] === $hariIni): ?>
                                <div class="small text-success mt-1" style="font-size:0.75rem;">Hari Ini</div>
                            <?php endif; ?>
                        </td>
                        <td><span class="tag"><?= e($t['kategori']) ?></span></td>
                        <td class="fw-semibold"><?= e($t['judul']) ?></td>
                        <td class="small text-muted"><?= e($t['isi_tips']) ?></td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-soft-green" data-bs-toggle="modal" data-bs-target="#editModal<?= (int)$t['id_tips'] ?>" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ======================= MODAL-MODAL EDIT (DITEMPATKAN DI LUAR TABEL) ======================= -->
<?php foreach ($daftar as $t): ?>
    <div class="modal fade" id="editModal<?= (int)$t['id_tips'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="<?= BASE_URL ?>/admin/tips/<?= (int)$t['id_tips'] ?>/edit" method="post">
                    <?= csrf_field() ?>
                    <div class="modal-header">
                        <h5 class="modal-title h6">Edit Tips Hari Ke-<?= (int)$t['hari_ke'] ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Judul Tips</label>
                            <input type="text" name="judul" class="form-control" value="<?= e($t['judul']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Kategori</label>
                            <select name="kategori" class="form-select" required>
                                <?php foreach ($kategoriPilihan as $kp): ?>
                                    <option value="<?= $kp ?>" <?= $t['kategori'] === $kp ? 'selected' : '' ?>>
                                        <?= $kp ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Isi Tips Harian</label>
                            <textarea name="isi_tips" class="form-control" rows="4" required><?= e($t['isi_tips']) ?></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-green btn-sm fw-semibold">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; ?>