<?php
/**
 * Kelola Artikel Kesehatan + Kategori Artikel.
 * @var array $artikel @var string $q @var array $kategori
 */
?>
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h1 class="h4 mb-0">Kelola Artikel Kesehatan</h1>
    </div>
    <a href="<?= BASE_URL ?>/admin/artikel/create" class="btn btn-green fw-semibold">
        <i class="bi bi-plus-lg me-1"></i>Tambah Artikel
    </a>
</div>

<!-- ===== Section Kelola Kategori Artikel ===== -->
<div class="panel-white mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="mb-0"><i class="bi bi-tags me-2 text-success"></i>Kategori Artikel</h6>
        <button class="btn btn-sm btn-soft-green fw-semibold" data-bs-toggle="modal"
                data-bs-target="#modalKategori">
            <i class="bi bi-plus-lg me-1"></i>Tambah Kategori
        </button>
    </div>
    <?php if (!$kategori): ?>
        <p class="text-muted small mb-0">Belum ada kategori.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-custom align-middle mb-0">
                <thead>
                    <tr><th>#</th><th>Nama Kategori</th><th class="text-end">Aksi</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($kategori as $k): ?>
                        <tr>
                            <td><?= (int) $k['id_kategori_artikel'] ?></td>
                            <td class="fw-semibold"><?= e($k['nama_kategori']) ?></td>
                            <td class="text-end text-nowrap">
                                <button class="btn btn-sm btn-soft-green" data-bs-toggle="modal"
                                        data-bs-target="#modalKategori"
                                        data-id="<?= (int) $k['id_kategori_artikel'] ?>"
                                        data-nama="<?= e($k['nama_kategori']) ?>">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="<?= BASE_URL.'/admin/artikel/kategori/'.(int)$k['id_kategori_artikel'].'/delete' ?>"
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

<!-- ===== Pencarian artikel ===== -->
<div class="panel-white mb-4">
    <form action="<?= BASE_URL.'/admin/artikel' ?>" method="get" class="row g-2">
        <div class="col-md-8">
            <input type="search" class="form-control" name="q" placeholder="Cari judul atau isi artikel…"
                   value="<?= e($q) ?>">
        </div>
        <div class="col-md-4 d-grid">
            <button class="btn btn-soft-green fw-semibold"><i class="bi bi-search me-1"></i>Cari</button>
        </div>
    </form>
</div>

<!-- ===== Daftar artikel ===== -->
<div class="panel-white">
    <div class="table-responsive">
        <table class="table table-custom align-middle mb-0">
            <thead>
                <tr>
                    <th>#</th><th>Foto</th><th>Judul</th><th>Kategori</th><th>Penulis</th><th>Tanggal Terbit</th><th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$artikel): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">Belum ada artikel.</td></tr>
                <?php endif; ?>
                <?php foreach ($artikel as $a): ?>
                    <tr>
                        <td><?= (int) $a['id_artikel'] ?></td>
                        <td>
                            <img src="<?= img_url($a['foto_artikel'], 'artikel') ?>" alt=""
                                 style="width:64px;height:40px;object-fit:cover;border-radius:6px;">
                        </td>
                        <td class="text-truncate" style="max-width:280px;">
                            <span class="fw-semibold"><?= e($a['judul_artikel']) ?></span><br>
                            <small class="text-muted"><?= e(excerpt($a['isi_artikel'], 60)) ?></small>
                        </td>
                        <td><span class="tag"><?= e($a['nama_kategori'] ?? '-') ?></span></td>
                        <td><?= e($a['penulis'] ?? '-') ?></td>
                        <td class="text-nowrap small"><?= tanggal_indo($a['tanggal_terbit']) ?></td>
                        <td class="text-end text-nowrap">
                            <a href="<?= BASE_URL.'/artikel/'.(int)$a['id_artikel'] ?>" target="_blank"
                               class="btn btn-sm btn-outline-secondary" title="Lihat"><i class="bi bi-eye"></i></a>
                            <a href="<?= BASE_URL.'/admin/artikel/'.(int)$a['id_artikel'].'/edit' ?>"
                               class="btn btn-sm btn-soft-green" title="Edit"><i class="bi bi-pencil"></i></a>
                            <form action="<?= BASE_URL.'/admin/artikel/'.(int)$a['id_artikel'].'/delete' ?>"
                                  method="post" class="d-inline"
                                  data-confirm="Hapus artikel '<?= e($a['judul_artikel']) ?>'?">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
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

<!-- ===== Modal Tambah/Edit Kategori Artikel ===== -->
<div class="modal fade" id="modalKategori" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title mb-0" id="modalKategoriTitle">Tambah Kategori Artikel</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form action="<?= BASE_URL.'/admin/artikel/kategori/store' ?>" method="post" id="formKategori">
                <?= csrf_field() ?>
                <input type="hidden" name="kategori_id" id="kategoriId" value="">
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
        document.getElementById('modalKategoriTitle').textContent = id ? 'Edit Kategori Artikel' : 'Tambah Kategori Artikel';
        document.getElementById('formKategori').action = id
            ? '<?= BASE_URL.'/admin/artikel/kategori/' ?>' + id + '/update'
            : '<?= BASE_URL.'/admin/artikel/kategori/store' ?>';
    });
</script>
