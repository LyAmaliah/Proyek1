<?php /** @var array $daftar @var string $q */ ?>
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h1 class="h4 mb-1">Kelola Kandungan Gizi &amp; Kalori</h1>
    </div>
    <a href="<?= BASE_URL ?>/admin/gizi/create" class="btn btn-green fw-semibold">
        <i class="bi bi-plus-lg me-1"></i>Tambah Data
    </a>
</div>

<div class="panel-white mb-4">
    <form action="<?= BASE_URL ?>/admin/gizi" method="get" class="row g-2 align-items-center">
        <!-- Kolom Pencarian Nama -->
        <div class="col-md-6">
            <input type="search" class="form-control" name="q" placeholder="Cari nama makanan/minuman…"
                   value="<?= e($q) ?>">
        </div>
        <!-- Dropdown Pilihan Kategori (Makanan / Minuman) -->
        <div class="col-md-4">
            <select name="kategori" class="form-select">
                <option value="">— Semua Kategori —</option>
                <?php foreach ($kategori as $kat): ?>
                    <option value="<?= e($kat) ?>" <?= ($kategoriAktif ?? '') === $kat ? 'selected' : '' ?>>
                        <?= e($kat) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <!-- Tombol Filter -->
        <div class="col-md-2 d-grid">
            <button class="btn btn-soft-green fw-semibold" type="submit">
                <i class="bi bi-funnel me-1"></i>Filter
            </button>
        </div>
    </form>
</div>

<div class="panel-white">
    <div class="table-responsive">
        <table class="table table-custom align-middle mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Makanan/Minuman</th>
                    <th>Kategori</th>
                    <th>Porsi Acuan</th>
                    <th class="text-end">Kalori</th>
                    <th class="text-end">Karbo</th>
                    <th class="text-end">Protein</th>
                    <th class="text-end">Lemak</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$daftar): ?>
                    <tr><td colspan="9" class="text-center text-muted py-4">Belum ada data gizi.</td></tr>
                <?php endif; ?>
                <?php foreach ($daftar as $g): ?>
                    <tr>
                        <td><?= (int) $g['id_gizi'] ?></td>
                        <td class="fw-semibold"><?= e($g['nama_makanan_minuman']) ?></td>
                        <td><span class="tag <?= $g['kategori'] === 'Minuman' ? 'orange' : '' ?>"><?= e($g['kategori']) ?></span></td>
                        <td><span class="tag gray"><?= e($g['porsi']) ?></span></td>
                        <td class="text-end"><?= number_format((float) $g['kalori'], 1) ?></td>
                        <td class="text-end"><?= number_format((float) $g['karbohidrat'], 1) ?> g</td>
                        <td class="text-end"><?= number_format((float) $g['protein'], 1) ?> g</td>
                        <td class="text-end"><?= number_format((float) $g['lemak'], 1) ?> g</td>
                        <td class="text-end text-nowrap">
                            <a href="<?= BASE_URL ?>/admin/gizi/<?= (int) $g['id_gizi'] ?>/edit"
                               class="btn btn-sm btn-soft-green" title="Edit"><i class="bi bi-pencil"></i></a>
                            <form action="<?= BASE_URL ?>/admin/gizi/<?= (int) $g['id_gizi'] ?>/delete"
                                  method="post" class="d-inline"
                                  data-confirm="Hapus data '<?= e($g['nama_makanan_minuman']) ?>'?">
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
