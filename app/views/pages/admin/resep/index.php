<?php /** @var array $resep @var string $q */ ?>
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h1 class="h4 mb-1">Kelola Resep Sehat</h1>
    </div>
    <a href="<?= BASE_URL ?>/admin/resep/create" class="btn btn-green fw-semibold">
        <i class="bi bi-plus-lg me-1"></i>Tambah Resep
    </a>
</div>

<div class="panel-white mb-4">
    <form action="<?= BASE_URL ?>/admin/resep" method="get" class="row g-2">
        <div class="col-md-8">
            <input type="search" class="form-control" name="q" placeholder="Cari judul/bahan resep…"
                   value="<?= e($q) ?>">
        </div>
        <div class="col-md-4 d-grid">
            <button class="btn btn-soft-green fw-semibold"><i class="bi bi-search me-1"></i>Cari</button>
        </div>
    </form>
</div>

<div class="panel-white">
    <div class="table-responsive">
        <table class="table table-custom align-middle mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Foto</th>
                    <th>Judul Resep</th>
                    <th>Waktu Masak</th>
                    <th class="text-end">Total Kalori</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$resep): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">Belum ada resep.</td></tr>
                <?php endif; ?>
                <?php foreach ($resep as $r): ?>
                    <tr>
                        <td><?= (int) $r['id_resep'] ?></td>
                        <td>
                            <img src="<?= img_url($r['foto_resep'], 'resep') ?>" alt=""
                                 style="width:64px;height:44px;object-fit:cover;border-radius:6px;">
                        </td>
                        <td class="text-truncate" style="max-width:260px;">
                            <span class="fw-semibold"><?= e($r['judul_resep']) ?></span><br>
                            <small class="text-muted"><?= e(excerpt($r['deskripsi'], 55)) ?></small>
                        </td>
                        <td class="text-nowrap"><i class="bi bi-clock me-1"></i><?= (int) $r['waktu_masak'] ?> mnt</td>
                        <td class="text-end fw-semibold"><?= number_format((int) $r['total_kalori']) ?> kkal</td>
                        <td>
                            <?php if ($r['status_publikasi'] === 'dipublikasikan'): ?>
                                <span class="tag"><i class="bi bi-check-circle me-1"></i>Dipublikasikan</span>
                            <?php else: ?>
                                <span class="tag gray"><i class="bi bi-pencil me-1"></i>Draft</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end text-nowrap">
                            <a href="<?= BASE_URL ?>/resep/<?= (int) $r['id_resep'] ?>" target="_blank"
                               class="btn btn-sm btn-outline-secondary" title="Lihat"><i class="bi bi-eye"></i></a>
                            <a href="<?= BASE_URL ?>/admin/resep/<?= (int) $r['id_resep'] ?>/edit"
                               class="btn btn-sm btn-soft-green" title="Edit"><i class="bi bi-pencil"></i></a>
                            <form action="<?= BASE_URL ?>/admin/resep/<?= (int) $r['id_resep'] ?>/delete"
                                  method="post" class="d-inline"
                                  data-confirm="Hapus resep '<?= e($r['judul_resep']) ?>'?">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-outline-danger" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
