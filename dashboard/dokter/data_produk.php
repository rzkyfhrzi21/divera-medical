<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0 text-primary-custom">Data Produk (Apotek)</h4>
    <button type="button" class="btn btn-primary-custom px-4" data-bs-toggle="modal" data-bs-target="#modalAddProduk">
        <i class="fa-solid fa-plus me-2"></i>Tambah Produk
    </button>
</div>

<div class="content-card">
    <div class="table-responsive">
        <table id="tableProduk" class="table table-borderless align-middle" style="width:100%">
            <thead>
                <tr style="border-bottom: 2px solid #f0f0f0;">
                    <th width="5%" class="text-muted fw-bold pb-3">No</th>
                    <th width="10%" class="text-muted fw-bold pb-3">Gambar</th>
                    <th width="20%" class="text-muted fw-bold pb-3">Nama Produk</th>
                    <th width="15%" class="text-muted fw-bold pb-3">Kategori</th>
                    <th width="15%" class="text-muted fw-bold pb-3">Harga</th>
                    <th width="10%" class="text-muted fw-bold pb-3">Stok</th>
                    <th width="25%" class="text-muted fw-bold pb-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                require_once '../../config/koneksi.php';
                $query = mysqli_query($koneksi, "SELECT * FROM produk ORDER BY id DESC");
                $no = 1;
                while ($row = mysqli_fetch_assoc($query)):
                ?>
                <tr style="border-bottom: 1px solid #f0f0f0;">
                    <td class="py-3"><?= $no++ ?></td>
                    <td class="py-3">
                        <?php if(!empty($row['url_gambar']) && file_exists('../../asset/img/produk/'.$row['url_gambar'])): ?>
                            <img src="../../asset/img/produk/<?= $row['url_gambar'] ?>" width="50" height="50" class="rounded object-fit-cover" alt="<?= $row['nama_produk'] ?>">
                        <?php else: ?>
                            <img src="../../asset/img/600x400.jpg" width="50" height="50" class="rounded object-fit-cover" alt="No Image">
                        <?php endif; ?>
                    </td>
                    <td class="fw-bold py-3"><?= htmlspecialchars($row['nama_produk']) ?></td>
                    <td class="py-3"><span class="badge bg-light text-dark border rounded-pill px-3 py-2 fw-semibold" style="font-size: 11px;"><?= htmlspecialchars($row['kategori']) ?></span></td>
                    <td class="py-3">Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                    <td class="py-3">
                        <?php if($row['stok'] > 0): ?>
                            <span class="badge bg-success rounded-pill px-3 py-2 fw-semibold" style="font-size: 11px;"><?= $row['stok'] ?></span>
                        <?php else: ?>
                            <span class="badge bg-danger rounded-pill px-3 py-2 fw-semibold" style="font-size: 11px;">Habis</span>
                        <?php endif; ?>
                    </td>
                    <td class="py-3">
                        <button class="btn btn-sm btn-outline-primary mb-1 rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#modalEditProduk<?= $row['id'] ?>">Edit</button>
                        <button class="btn btn-sm btn-danger mb-1 rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#modalDeleteProduk<?= $row['id'] ?>">Hapus</button>
                    </td>
                </tr>

                <!-- Modal Edit -->
                <div class="modal fade" id="modalEditProduk<?= $row['id'] ?>" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <form action="../../config/function_product.php" method="POST" enctype="multipart/form-data">
                            <div class="modal-content">
                                <div class="modal-header border-0 pb-0">
                                    <h5 class="modal-title fw-bold">Edit Produk</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="id_produk" value="<?= $row['id'] ?>">
                                    <input type="hidden" name="foto_lama" value="<?= $row['url_gambar'] ?>">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold">Nama Produk</label>
                                            <input type="text" name="nama_produk" class="form-control" value="<?= htmlspecialchars($row['nama_produk']) ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold">Kategori</label>
                                            <select name="kategori" class="form-select" required>
                                                <option value="Vitamin & Suplemen" <?= $row['kategori'] == 'Vitamin & Suplemen' ? 'selected' : '' ?>>Vitamin & Suplemen</option>
                                                <option value="Obat Flu & Batuk" <?= $row['kategori'] == 'Obat Flu & Batuk' ? 'selected' : '' ?>>Obat Flu & Batuk</option>
                                                <option value="Pereda Nyeri" <?= $row['kategori'] == 'Pereda Nyeri' ? 'selected' : '' ?>>Pereda Nyeri</option>
                                                <option value="Ibu & Anak" <?= $row['kategori'] == 'Ibu & Anak' ? 'selected' : '' ?>>Ibu & Anak</option>
                                                <option value="Kesehatan" <?= $row['kategori'] == 'Kesehatan' ? 'selected' : '' ?>>Kesehatan</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold">Harga (Rp)</label>
                                            <input type="number" name="harga" class="form-control" value="<?= $row['harga'] ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold">Stok</label>
                                            <input type="number" name="stok" class="form-control" value="<?= $row['stok'] ?>" required>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label small fw-bold">Deskripsi</label>
                                            <textarea name="deskripsi" class="form-control" rows="3"><?= htmlspecialchars($row['deskripsi']) ?></textarea>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label small fw-bold">Update Gambar Produk (Opsional)</label>
                                            <input type="file" name="gambar_produk" class="form-control" accept="image/*">
                                            <div class="form-text">Biarkan kosong jika tidak ingin mengubah gambar.</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer border-0 pt-0">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" name="btn_edit_produk" class="btn btn-primary-custom">Simpan Perubahan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Modal Delete -->
                <div class="modal fade" id="modalDeleteProduk<?= $row['id'] ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <form action="../../config/function_product.php" method="POST">
                            <div class="modal-content">
                                <div class="modal-header border-0 pb-0">
                                    <h5 class="modal-title fw-bold text-danger">Hapus Produk</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body text-center py-4">
                                    <input type="hidden" name="id_produk" value="<?= $row['id'] ?>">
                                    <input type="hidden" name="foto_lama" value="<?= $row['url_gambar'] ?>">
                                    <i class="fa-solid fa-triangle-exclamation fa-3x text-warning mb-3"></i>
                                    <p class="mb-0">Apakah Anda yakin ingin menghapus produk <strong><?= htmlspecialchars($row['nama_produk']) ?></strong>?</p>
                                    <p class="small text-muted">Data yang dihapus tidak dapat dikembalikan.</p>
                                </div>
                                <div class="modal-footer border-0 pt-0 justify-content-center">
                                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" name="btn_delete_produk" class="btn btn-danger px-4">Ya, Hapus</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Add -->
<div class="modal fade" id="modalAddProduk" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="../../config/function_product.php" method="POST" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Tambah Produk Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nama Produk <span class="text-danger">*</span></label>
                            <input type="text" name="nama_produk" class="form-control" required placeholder="Contoh: Vitamin C 1000mg">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Kategori <span class="text-danger">*</span></label>
                            <select name="kategori" class="form-select" required>
                                <option value="" disabled selected>Pilih Kategori</option>
                                <option value="Vitamin & Suplemen">Vitamin & Suplemen</option>
                                <option value="Obat Flu & Batuk">Obat Flu & Batuk</option>
                                <option value="Pereda Nyeri">Pereda Nyeri</option>
                                <option value="Ibu & Anak">Ibu & Anak</option>
                                <option value="Kesehatan">Kesehatan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Harga (Rp) <span class="text-danger">*</span></label>
                            <input type="number" name="harga" class="form-control" required placeholder="Contoh: 45000">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Stok Awal <span class="text-danger">*</span></label>
                            <input type="number" name="stok" class="form-control" required value="0">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" rows="3" placeholder="Jelaskan fungsi atau detail produk..."></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Gambar Produk (Opsional)</label>
                            <input type="file" name="gambar_produk" class="form-control" accept="image/*">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="btn_add_produk" class="btn btn-primary-custom">Simpan Produk</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#tableProduk').DataTable({
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
            }
        });
    });
</script>
