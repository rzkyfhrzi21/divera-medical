<?php
require_once '../../config/koneksi.php';
$id_pengguna = $_SESSION['user_id'];

// Ambil data pengguna
$query_pengguna = mysqli_query($koneksi, "SELECT * FROM pengguna WHERE id='$id_pengguna'");
$data_pengguna = mysqli_fetch_assoc($query_pengguna);

// Ambil data pasien
$query_pasien = mysqli_query($koneksi, "SELECT * FROM pasien WHERE id_pengguna='$id_pengguna'");
$data_pasien = mysqli_fetch_assoc($query_pasien);

// Jika belum ada record pasien, buatkan default
if (!$data_pasien) {
    mysqli_query($koneksi, "INSERT INTO pasien (id_pengguna) VALUES ('$id_pengguna')");
    $query_pasien = mysqli_query($koneksi, "SELECT * FROM pasien WHERE id_pengguna='$id_pengguna'");
    $data_pasien = mysqli_fetch_assoc($query_pasien);
}
?>

<div class="row g-4">
    <!-- Card Profil Pengguna (Akun Utama) -->
    <div class="col-12">
        <div class="content-card border-0 shadow-sm p-4">
            <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                <div>
                    <h5 class="fw-bold mb-1">Informasi Akun Utama</h5>
                    <p class="text-muted small mb-0">Kelola informasi dasar akun (Nama, Email, Password, No Telepon)</p>
                </div>
                <button type="button" class="btn btn-primary-custom px-4" data-bs-toggle="modal" data-bs-target="#modalEditPengguna">
                    <i class="fa-solid fa-user-pen me-2"></i>Ubah Profil
                </button>
            </div>
            
            <div class="row align-items-center">
                <div class="col-md-3 text-center mb-4 mb-md-0 border-end">
                    <?php $foto = !empty($data_pengguna['foto_profil']) ? 'profil/'.$data_pengguna['foto_profil'] : 'female-doctor.png'; ?>
                    <img src="../../asset/img/<?= $foto ?>" class="rounded-circle object-fit-cover shadow-sm mb-3 border" width="130" height="130" alt="Profile">
                    <h6 class="fw-bold mb-0"><?= htmlspecialchars($data_pengguna['nama']) ?></h6>
                    <p class="text-muted small text-uppercase"><?= htmlspecialchars($data_pengguna['role']) ?></p>
                </div>
                <div class="col-md-9 ps-md-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <span class="d-block text-muted small fw-bold mb-1">Nama Lengkap</span>
                            <span class="fs-6"><?= htmlspecialchars($data_pengguna['nama']) ?></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <span class="d-block text-muted small fw-bold mb-1">Email</span>
                            <span class="fs-6"><?= htmlspecialchars($data_pengguna['email']) ?></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <span class="d-block text-muted small fw-bold mb-1">Nomor Telepon</span>
                            <span class="fs-6"><?= !empty($data_pengguna['telpon']) ? htmlspecialchars($data_pengguna['telpon']) : '-' ?></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <span class="d-block text-muted small fw-bold mb-1">Tanggal Daftar</span>
                            <span class="fs-6"><?= date('d M Y', strtotime($data_pengguna['tgl_daftar'])) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Profil Pasien (Tambahan) -->
    <div class="col-12">
        <div class="content-card border-0 shadow-sm p-4">
            <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                <div>
                    <h5 class="fw-bold mb-1">Rekam Data Pasien</h5>
                    <p class="text-muted small mb-0">Kelola data fisik dan demografi untuk keperluan rekam medis</p>
                </div>
                <button type="button" class="btn btn-outline-primary-custom px-4" data-bs-toggle="modal" data-bs-target="#modalEditPasien">
                    <i class="fa-solid fa-notes-medical me-2"></i>Ubah Data Pasien
                </button>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <span class="d-block text-muted small fw-bold mb-1">Tanggal Lahir</span>
                    <span class="fs-6"><?= !empty($data_pasien['tanggal_lahir']) ? date('d F Y', strtotime($data_pasien['tanggal_lahir'])) : '<i class="text-muted small">Belum diatur</i>' ?></span>
                </div>
                <div class="col-md-6 mb-3">
                    <span class="d-block text-muted small fw-bold mb-1">Jenis Kelamin</span>
                    <span class="fs-6 text-capitalize"><?= !empty($data_pasien['jenis_kelamin']) ? htmlspecialchars($data_pasien['jenis_kelamin']) : '<i class="text-muted small">Belum diatur</i>' ?></span>
                </div>
                <div class="col-md-4 mb-3">
                    <span class="d-block text-muted small fw-bold mb-1">Golongan Darah</span>
                    <span class="fs-6"><?= !empty($data_pasien['golongan_darah']) ? htmlspecialchars($data_pasien['golongan_darah']) : '<i class="text-muted small">Belum diatur</i>' ?></span>
                </div>
                <div class="col-md-4 mb-3">
                    <span class="d-block text-muted small fw-bold mb-1">Tinggi Badan (cm)</span>
                    <span class="fs-6"><?= !empty($data_pasien['tinggi_badan']) ? $data_pasien['tinggi_badan'] : '<i class="text-muted small">Belum diatur</i>' ?></span>
                </div>
                <div class="col-md-4 mb-3">
                    <span class="d-block text-muted small fw-bold mb-1">Berat Badan (kg)</span>
                    <span class="fs-6"><?= !empty($data_pasien['berat_badan']) ? $data_pasien['berat_badan'] : '<i class="text-muted small">Belum diatur</i>' ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Profil Pengguna -->
<div class="modal fade" id="modalEditPengguna" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="../../config/function_auth.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="btn_update_profil_pengguna" value="1">
            <input type="hidden" name="foto_lama" value="<?= $data_pengguna['foto_profil'] ?>">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Ubah Informasi Akun</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($data_pengguna['nama']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Email</label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($data_pengguna['email']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nomor Telepon</label>
                            <input type="text" name="telpon" class="form-control" value="<?= htmlspecialchars($data_pengguna['telpon']) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Password Baru (Opsional)</label>
                            <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak diubah">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Ubah Foto Profil</label>
                            <input type="file" name="foto_profil" class="form-control" accept="image/*">
                            <div class="form-text">Maksimal 1MB. Format: JPG, PNG.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-custom px-4">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Data Pasien -->
<div class="modal fade" id="modalEditPasien" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="../../config/function_auth.php" method="POST">
            <input type="hidden" name="btn_update_profil_pasien" value="1">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Ubah Data Pasien</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control" value="<?= $data_pasien['tanggal_lahir'] ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select">
                                <option value="laki-laki" <?= $data_pasien['jenis_kelamin'] == 'laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                                <option value="perempuan" <?= $data_pasien['jenis_kelamin'] == 'perempuan' ? 'selected' : '' ?>>Perempuan</option>
                                <option value="lainnya" <?= $data_pasien['jenis_kelamin'] == 'lainnya' ? 'selected' : '' ?>>Lainnya</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Golongan Darah</label>
                            <select name="golongan_darah" class="form-select">
                                <option value="" disabled selected>Pilih...</option>
                                <option value="A" <?= $data_pasien['golongan_darah'] == 'A' ? 'selected' : '' ?>>A</option>
                                <option value="B" <?= $data_pasien['golongan_darah'] == 'B' ? 'selected' : '' ?>>B</option>
                                <option value="AB" <?= $data_pasien['golongan_darah'] == 'AB' ? 'selected' : '' ?>>AB</option>
                                <option value="O" <?= $data_pasien['golongan_darah'] == 'O' ? 'selected' : '' ?>>O</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Tinggi Badan (cm)</label>
                            <input type="number" step="0.1" name="tinggi_badan" class="form-control" value="<?= $data_pasien['tinggi_badan'] ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Berat Badan (kg)</label>
                            <input type="number" step="0.1" name="berat_badan" class="form-control" value="<?= $data_pasien['berat_badan'] ?>">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-custom px-4">Simpan Rekam Medis</button>
                </div>
            </div>
        </form>
    </div>
</div>
