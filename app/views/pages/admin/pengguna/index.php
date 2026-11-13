<?php /** @var array $pengguna @var string $q */ ?>
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h1 class="h4 mb-1">Kelola Pengguna</h1>
    </div>
    <span class="badge text-bg-success fs-6"><?= count($pengguna) ?> pengguna</span>
</div>

<div class="panel-white mb-4">
    <form action="<?= BASE_URL ?>/admin/pengguna" method="get" class="row g-2">
        <div class="col-md-8">
            <input type="search" class="form-control" name="q"
                   placeholder="Cari nama lengkap atau username…" value="<?= e($q) ?>">
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
                    <th>Nama Lengkap</th>
                    <th>Username</th>
                    <th class="text-end">Berat</th>
                    <th class="text-end">Tinggi</th>
                    <th>J.K.</th>
                    <th class="text-end">Umur</th>
                    <th>Terdaftar</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$pengguna): ?>
                    <tr><td colspan="9" class="text-center text-muted py-4">Tidak ada pengguna ditemukan.</td></tr>
                <?php endif; ?>
                <?php foreach ($pengguna as $p): ?>
                    <tr>
                        <td><?= (int) $p['id_pengguna'] ?></td>
                        <td class="fw-semibold"><?= e($p['nama_lengkap']) ?></td>
                        <td><span class="tag gray">@<?= e($p['username']) ?></span></td>
                        <td class="text-end"><?= number_format((float) $p['berat_badan'], 1) ?> kg</td>
                        <td class="text-end"><?= number_format((float) $p['tinggi_badan'], 1) ?> cm</td>
                        <td>
                            <span class="badge <?= $p['jenis_kelamin'] === 'L' ? 'text-bg-primary' : 'text-bg-danger' ?>">
                                <?= e($p['jenis_kelamin']) ?>
                            </span>
                        </td>
                        <td class="text-end"><?= (int) $p['umur'] ?> th</td>
                        <td class="small text-nowrap"><?= tanggal_indo($p['created_at']) ?></td>
                        <td class="text-end text-nowrap">
                            <a href="<?= BASE_URL ?>/admin/pengguna/<?= (int) $p['id_pengguna'] ?>/edit"
                               class="btn btn-sm btn-soft-green" title="Edit"><i class="bi bi-pencil"></i></a>
                            <form action="<?= BASE_URL ?>/admin/pengguna/<?= (int) $p['id_pengguna'] ?>/delete"
                                  method="post" class="d-inline"
                                  data-confirm="Hapus pengguna '<?= e($p['nama_lengkap']) ?>'? Seluruh riwayat BMI & ulasannya ikut terhapus.">
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
