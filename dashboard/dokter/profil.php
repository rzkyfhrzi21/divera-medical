<?php
require_once '../../config/koneksi.php';
$id_pengguna = $_SESSION['user_id'];

// Ambil data pengguna
$query_pengguna = mysqli_query($koneksi, "SELECT * FROM pengguna WHERE id='$id_pengguna'");
$data_pengguna = mysqli_fetch_assoc($query_pengguna);

// Ambil data dokter
$query_dokter = mysqli_query($koneksi, "SELECT * FROM dokter WHERE id_pengguna='$id_pengguna'");
$data_dokter = mysqli_fetch_assoc($query_dokter);

// Jika belum ada record dokter, buatkan default
if (!$data_dokter) {
    mysqli_query($koneksi, "INSERT INTO dokter (id_pengguna) VALUES ('$id_pengguna')");
    $query_dokter = mysqli_query($koneksi, "SELECT * FROM dokter WHERE id_pengguna='$id_pengguna'");
    $data_dokter = mysqli_fetch_assoc($query_dokter);
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

    <!-- Card Profil Dokter (Tambahan) -->
    <div class="col-12">
        <div class="content-card border-0 shadow-sm p-4">
            <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                <div>
                    <h5 class="fw-bold mb-1">Profil Dokter Profesional</h5>
                    <p class="text-muted small mb-0">Kelola data profesional Anda sebagai dokter (Spesialisasi, SIP, Biaya, dll)</p>
                </div>
                <button type="button" class="btn btn-outline-primary-custom px-4" data-bs-toggle="modal" data-bs-target="#modalEditDokter">
                    <i class="fa-solid fa-user-doctor me-2"></i>Ubah Data Dokter
                </button>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <span class="d-block text-muted small fw-bold mb-1">Spesialisasi</span>
                    <span class="fs-6"><?= !empty($data_dokter['spesialisasi']) ? htmlspecialchars($data_dokter['spesialisasi']) : '<i class="text-muted small">Belum diatur</i>' ?></span>
                </div>
                <div class="col-md-6 mb-3">
                    <span class="d-block text-muted small fw-bold mb-1">Tahun Pengalaman</span>
                    <span class="fs-6"><?= !empty($data_dokter['tahun_pengalaman']) ? $data_dokter['tahun_pengalaman'] . ' Tahun' : '<i class="text-muted small">Belum diatur</i>' ?></span>
                </div>
                <div class="col-md-6 mb-3">
                    <span class="d-block text-muted small fw-bold mb-1">Biaya Konsultasi (Rp)</span>
                    <span class="fs-6 text-primary-custom fw-bold">Rp <?= number_format($data_dokter['biaya'], 0, ',', '.') ?></span>
                </div>
                <div class="col-12 mb-3">
                    <span class="d-block text-muted small fw-bold mb-1">Biografi</span>
                    <span class="fs-6"><?= !empty($data_dokter['biografi']) ? nl2br(htmlspecialchars($data_dokter['biografi'])) : '<i class="text-muted small">Belum diatur</i>' ?></span>
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

<!-- Modal Edit Data Dokter -->
<div class="modal fade" id="modalEditDokter" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="../../config/function_auth.php" method="POST">
            <input type="hidden" name="btn_update_profil_dokter" value="1">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Ubah Data Dokter</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Spesialisasi</label>
                            <input type="text" name="spesialisasi" class="form-control" value="<?= htmlspecialchars($data_dokter['spesialisasi']) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Tahun Pengalaman</label>
                            <input type="number" name="tahun_pengalaman" class="form-control" value="<?= $data_dokter['tahun_pengalaman'] ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Biaya Konsultasi (Rp)</label>
                            <input type="number" name="biaya" class="form-control" value="<?= $data_dokter['biaya'] ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Biografi</label>
                            <textarea name="biografi" class="form-control" rows="4"><?= htmlspecialchars($data_dokter['biografi']) ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-custom px-4">Simpan Profil Dokter</button>
                </div>
            </div>
        </form>
    </div>
</div>
