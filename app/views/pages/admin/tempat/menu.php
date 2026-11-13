<?php
/**
 * Kelola submenu makanan/minuman 1 tempat.
 * @var array $tempat @var array $menu @var array|null $menuEdit
 */
$val = fn(string $k, $default = '') => $menuEdit[$k] ?? ($_POST[$k] ?? $default);
$formAction = $menuEdit
    ? BASE_URL . '/admin/tempat/' . (int) $tempat['id_tempat'] . '/menu/' . (int) $menuEdit['id_menu'] . '/edit'
    : BASE_URL . '/admin/tempat/' . (int) $tempat['id_tempat'] . '/menu/create';
?>
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h1 class="h4 mb-0">Kelola Menu — <?= e($tempat['nama_tempat']) ?></h1>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= BASE_URL ?>/admin/tempat/<?= (int) $tempat['id_tempat'] ?>/edit"
           class="btn btn-outline-success btn-sm"><i class="bi bi-pencil me-1"></i>Edit Tempat</a>
        <a href="<?= BASE_URL ?>/admin/tempat" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- ====== DAFTAR MENU ====== -->
    <div class="col-lg-7">
        <div class="panel-white">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0"><i class="bi bi-list-ul me-2 text-success"></i>Daftar Menu (<?= count($menu) ?>)</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-custom align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th>Menu</th>
                            <th class="text-end">Harga</th>
                            <th class="text-end">Kalori</th>
                            <th class="text-end">P/K/L (g)</th>
                            <th>Waktu</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!$menu): ?>
                            <tr><td colspan="7" class="text-center text-muted py-4">Belum ada menu.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($menu as $m): ?>
                            <tr>
                                <td>
                                    <img src="<?= img_url($m['foto_menu'], 'menu') ?>" alt=""
                                         style="width:58px;height:42px;object-fit:cover;border-radius:6px;">
                                </td>
                                <td>
                                    <span class="fw-semibold"><?= e($m['nama_menu']) ?></span><br>
                                    <small class="text-muted"><?= e(excerpt($m['deskripsi'], 45)) ?></small>
                                </td>
                                <td class="text-end text-nowrap"><?= rupiah($m['harga']) ?></td>
                                <td class="text-end text-nowrap"><?= number_format((float) $m['kalori'], 1) ?> kkal</td>
                                <td class="text-end small text-nowrap">
                                    <?= number_format((float) $m['protein'], 1) ?> /
                                    <?= number_format((float) $m['karbohidrat'], 1) ?> /
                                    <?= number_format((float) $m['lemak'], 1) ?>
                                </td>
                                <td class="small text-nowrap"><?= (int) $m['estimasi_waktu'] ?> mnt</td>
                                <td class="text-end text-nowrap">
                                    <a href="<?= BASE_URL ?>/admin/tempat/<?= (int) $tempat['id_tempat'] ?>/menu?edit=<?= (int) $m['id_menu'] ?>"
                                       class="btn btn-sm btn-soft-green" title="Edit"><i class="bi bi-pencil"></i></a>
                                    <form action="<?= BASE_URL ?>/admin/tempat/<?= (int) $tempat['id_tempat'] ?>/menu/<?= (int) $m['id_menu'] ?>/delete"
                                          method="post" class="d-inline"
                                          data-confirm="Hapus menu '<?= e($m['nama_menu']) ?>'?">
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
    </div>

    <!-- ====== FORM MENU ====== -->
    <div class="col-lg-5">
        <div class="panel-white">
            <h6 class="mb-3">
                <i class="bi <?= $menuEdit ? 'bi-pencil-square' : 'bi-plus-circle' ?> me-2 text-success"></i>
                <?= $menuEdit ? 'Edit Menu: ' . e($menuEdit['nama_menu']) : 'Tambah Menu Baru' ?>
            </h6>

            <form action="<?= $formAction ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label for="nama_menu" class="form-label small fw-semibold">Nama Menu</label>
                    <input type="text" class="form-control" id="nama_menu" name="nama_menu"
                           minlength="2" required value="<?= e($val('nama_menu')) ?>"
                           placeholder="mis. Jus Alpukat Madu">
                </div>

                <div class="mb-3">
                    <label for="deskripsi" class="form-label small fw-semibold">Deskripsi</label>
                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="2"
                              placeholder="Deskripsi singkat menu…"><?= e($val('deskripsi')) ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="id_kategori_menu" class="form-label small fw-semibold">Kategori Menu</label>
                    <select class="form-select" id="id_kategori_menu" name="id_kategori_menu">
                        <option value="">— Tanpa Kategori —</option>
                        <?php foreach ($kategori as $k): ?>
                            <option value="<?= (int) $k['id_kategori_menu'] ?>"
                                <?= (int) ($val('id_kategori_menu', 0)) === (int) $k['id_kategori_menu'] ? 'selected' : '' ?>>
                                <?= e($k['nama_kategori']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-text">Pilih kategori atau tambah kategori baru langsung dari sini.</div>
                    <button type="button" class="btn btn-sm btn-soft-green mt-2"
                            data-bs-toggle="modal" data-bs-target="#modalQuickKategori">
                        <i class="bi bi-plus-lg me-1"></i>Tambah Kategori Baru
                    </button>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label for="harga" class="form-label small fw-semibold">Harga (Rp)</label>
                        <input type="number" min="0" class="form-control" id="harga" name="harga"
                               required value="<?= e($val('harga')) ?>" placeholder="25000">
                    </div>
                    <div class="col-6">
                        <label for="kalori" class="form-label small fw-semibold">Kalori (kkal)</label>
                        <input type="number" step="0.1" min="0" class="form-control" id="kalori"
                               name="kalori" value="<?= e($val('kalori', 0)) ?>">
                    </div>
                    <div class="col-12">
                        <label for="estimasi_waktu" class="form-label small fw-semibold">Estimasi Waktu (mnt)</label>
                        <input type="number" min="0" class="form-control" id="estimasi_waktu"
                               name="estimasi_waktu" value="<?= e($val('estimasi_waktu', 0)) ?>">
                    </div>
                    <div class="col-4">
                        <label for="protein" class="form-label small fw-semibold">Protein (g)</label>
                        <input type="number" step="0.1" min="0" class="form-control" id="protein"
                               name="protein" required value="<?= e($val('protein')) ?>">
                    </div>
                    <div class="col-4">
                        <label for="karbohidrat" class="form-label small fw-semibold">Karbo (g)</label>
                        <input type="number" step="0.1" min="0" class="form-control" id="karbohidrat"
                               name="karbohidrat" required value="<?= e($val('karbohidrat')) ?>">
                    </div>
                    <div class="col-4">
                        <label for="lemak" class="form-label small fw-semibold">Lemak (g)</label>
                        <input type="number" step="0.1" min="0" class="form-control" id="lemak"
                               name="lemak" required value="<?= e($val('lemak')) ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="foto_menu" class="form-label small fw-semibold">Foto Menu</label>
                    <?php $adaFotoMenu = $menuEdit && !empty($menuEdit['foto_menu']); ?>
                    <?php if ($adaFotoMenu): ?>
                        <div class="position-relative mb-2">
                            <img src="<?= img_url($menuEdit['foto_menu'], 'menu') ?>" alt="Foto menu"
                                 id="previewFotoMenu" class="img-fluid rounded-3"
                                 style="width:100%;height:150px;object-fit:cover;">
                            <span class="badge text-bg-light border position-absolute top-0 end-0 m-2"
                                  style="font-size:.68rem;">
                                <i class="bi bi-image me-1"></i>Foto saat ini
                            </span>
                        </div>
                    <?php else: ?>
                        <div class="position-relative mb-2">
                            <img src="<?= BASE_URL ?>/assets/img/placeholder.svg" alt="Preview foto menu"
                                 id="previewFotoMenu" class="img-fluid rounded-3"
                                 style="width:100%;height:150px;object-fit:cover;">
                        </div>
                    <?php endif; ?>

                    <input type="file" class="form-control" id="foto_menu" name="foto_menu"
                           accept="image/jpeg,image/png,image/webp" data-preview="#previewFotoMenu"
                           data-preview-placeholder="<?= BASE_URL ?>/assets/img/placeholder.svg">
                    <div class="form-text">
                        <?= $adaFotoMenu
                            ? 'Pilih file baru untuk mengganti foto lama.'
                            : 'Belum ada foto. Pilih JPG, PNG, atau WEBP (maks. 2 MB).' ?>
                    </div>

                    <?php if ($adaFotoMenu): ?>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" value="1"
                                   id="hapus_foto_menu" name="hapus_foto_menu">
                            <label class="form-check-label small text-danger" for="hapus_foto_menu">
                                <i class="bi bi-trash me-1"></i>Hapus foto saat ini
                            </label>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-green fw-semibold flex-fill">
                        <i class="bi bi-save me-1"></i><?= $menuEdit ? 'Simpan' : 'Tambah Menu' ?>
                    </button>
                    <?php if ($menuEdit): ?>
                        <a href="<?= BASE_URL ?>/admin/tempat/<?= (int) $tempat['id_tempat'] ?>/menu"
                           class="btn btn-outline-secondary">Batal</a>
                    <?php endif; ?>
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
                        <form action="<?= BASE_URL ?>/admin/tempat/kategori/store" method="post">
                            <?= csrf_field() ?>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="nama_kategori" class="form-label small fw-semibold">Nama Kategori</label>
                                    <input type="text" class="form-control" id="nama_kategori" name="nama_kategori"
                                           minlength="2" required placeholder="mis. Dessert Sehat">
                                </div>
                                <div class="mb-0">
                                    <label for="deskripsi" class="form-label small fw-semibold">Deskripsi</label>
                                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="2"
                                              placeholder="Deskripsi singkat kategori…"></textarea>
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
        </div>
    </div>
</div>
