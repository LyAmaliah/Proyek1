<?php
/**
 * Kelola Tempat & Menu Sehat + Kategori Menu — FR-05.
 * @var array $tempat @var string $q @var array $kategoriMenu
 */
?>
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h1 class="h4 mb-1">Kelola Tempat &amp; Menu Sehat</h1>
    </div>
    <a href="<?= BASE_URL ?>/admin/tempat/create" class="btn btn-green fw-semibold">
        <i class="bi bi-plus-lg me-1"></i>Tambah Tempat
    </a>
</div>

<!-- ===== Section Kelola Kategori Menu ===== -->
<div class="panel-white mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="mb-0"><i class="bi bi-tags me-2 text-success"></i>Kategori Menu</h6>
        <button class="btn btn-sm btn-soft-green fw-semibold" data-bs-toggle="modal"
                data-bs-target="#modalKategori">
            <i class="bi bi-plus-lg me-1"></i>Tambah Kategori
        </button>
    </div>
    <?php if (!$kategoriMenu): ?>
        <p class="text-muted small mb-0">Belum ada kategori menu.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-custom align-middle mb-0">
                <thead>
                    <tr><th>#</th><th>Nama Kategori</th><th>Deskripsi</th><th class="text-end">Aksi</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($kategoriMenu as $k): ?>
                        <tr>
                            <td><?= (int) $k['id_kategori_menu'] ?></td>
                            <td class="fw-semibold"><?= e($k['nama_kategori']) ?></td>
                            <td class="small text-muted"><?= e($k['deskripsi'] ?? '-') ?></td>
                            <td class="text-end text-nowrap">
                                <button class="btn btn-sm btn-soft-green" data-bs-toggle="modal"
                                        data-bs-target="#modalKategori"
                                        data-id="<?= (int) $k['id_kategori_menu'] ?>"
                                        data-nama="<?= e($k['nama_kategori']) ?>"
                                        data-deskripsi="<?= e($k['deskripsi'] ?? '') ?>">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="<?= BASE_URL.'/admin/tempat/kategori/'.(int)$k['id_kategori_menu'].'/delete' ?>"
                                      method="post" class="d-inline"
                                      data-confirm="Hapus kategori '<?= e($k['nama_kategori']) ?>'?">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-sm btn-outline-danger" type="submit"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- ===== Pencarian tempat ===== -->
<div class="panel-white mb-4">
    <form action="<?= BASE_URL ?>/admin/tempat" method="get" class="row g-2">
        <div class="col-md-8">
            <input type="search" class="form-control" name="q" placeholder="Cari nama/alamat tempat…"
                   value="<?= e($q) ?>">
        </div>
        <div class="col-md-4 d-grid">
            <button class="btn btn-soft-green fw-semibold"><i class="bi bi-search me-1"></i>Cari</button>
        </div>
    </form>
</div>

<!-- ===== Daftar tempat ===== -->
<div class="row g-4">
    <?php if (!$tempat): ?>
        <div class="col-12">
            <div class="alert alert-info border-0 text-center mb-0">
                <i class="bi bi-shop fs-3 d-block mb-1"></i>Belum ada tempat terdaftar.
            </div>
        </div>
    <?php endif; ?>

    <?php foreach ($tempat as $t): ?>
        <div class="col-md-6 col-xl-4">
            <div class="card-soft h-100">
                <div class="ratio ratio-16x9">
                    <img src="<?= img_url($t['foto_tempat'], 'tempat') ?>" alt="" class="img-cover">
                </div>
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="mb-0"><?= e($t['nama_tempat']) ?></h6>
                        <span class="tag orange"><?= (int) $t['jumlah_ulasan'] ?> ulasan</span>
                    </div>
                    <p class="small text-muted mb-1">
                        <i class="bi bi-geo-alt me-1"></i><?= e(excerpt($t['alamat'], 55)) ?>
                    </p>
                    <p class="small text-muted flex-grow-1"><?= e(excerpt($t['deskripsi'], 65)) ?></p>

                    <?php if ($t['link_maps']): ?>
                        <a href="<?= e($t['link_maps']) ?>" target="_blank" rel="noopener"
                           class="small text-success mb-2"><i class="bi bi-map me-1"></i>Buka Google Maps</a>
                    <?php endif; ?>

                    <div class="d-flex flex-wrap gap-1 mt-auto pt-2 border-top">
                        <a href="<?= BASE_URL ?>/admin/tempat/<?= (int) $t['id_tempat'] ?>/menu"
                           class="btn btn-sm btn-green">
                            <i class="bi bi-bookmark-check me-1"></i>Menu (<?= count(Menu::byTempat((int) $t['id_tempat'])) ?>)
                        </a>
                        <a href="<?= BASE_URL ?>/admin/tempat/<?= (int) $t['id_tempat'] ?>/edit"
                           class="btn btn-sm btn-soft-green"><i class="bi bi-pencil"></i></a>
                        <form action="<?= BASE_URL ?>/admin/tempat/<?= (int) $t['id_tempat'] ?>/delete"
                              method="post" class="d-inline"
                              data-confirm="Hapus tempat '<?= e($t['nama_tempat']) ?>' beserta seluruh menunya?">
                            <?= csrf_field() ?>
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                        <a href="<?= BASE_URL ?>/tempat/<?= (int) $t['id_tempat'] ?>" target="_blank"
                           class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- ===== Modal Tambah/Edit Kategori Menu ===== -->
<div class="modal fade" id="modalKategori" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title mb-0" id="modalKategoriTitle">Tambah Kategori Menu</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form action="<?= BASE_URL ?>/admin/tempat/kategori/store" method="post" id="formKategori">
                <?= csrf_field() ?>
                <input type="hidden" name="kategori_id" id="kategoriId" value="">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nama_kategori" class="form-label small fw-semibold">Nama Kategori</label>
                        <input type="text" class="form-control" id="nama_kategori" name="nama_kategori"
                               minlength="2" required placeholder="mis. Jus, Smoothies">
                    </div>
                    <div class="mb-0">
                        <label for="deskripsi" class="form-label small fw-semibold">Deskripsi Kategori</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"
                                  placeholder="Deskripsi singkat kategori…"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-green btn-sm fw-semibold">
                        <i class="bi bi-save me-1"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('modalKategori').addEventListener('show.bs.modal', function (e) {
        const btn = e.relatedTarget;
        const id = btn?.dataset?.id || '';
        document.getElementById('kategoriId').value = id;
        document.getElementById('nama_kategori').value = btn?.dataset?.nama || '';
        document.getElementById('deskripsi').value = btn?.dataset?.deskripsi || '';
        document.getElementById('modalKategoriTitle').textContent = id ? 'Edit Kategori Menu' : 'Tambah Kategori Menu';
        document.getElementById('formKategori').action = id
            ? '<?= BASE_URL ?>/admin/tempat/kategori/' + id + '/update'
            : '<?= BASE_URL ?>/admin/tempat/kategori/store';
    });
</script>
