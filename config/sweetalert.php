<script>
  (() => {
    const params = new URLSearchParams(window.location.search);
    const status = params.get("status");
    const action = params.get("action");
    const ket = params.get("ket");

    if (!status) return;

    const footerText = "@ Sistem Informasi Peringkat Kafe Kopi Bandar Lampung";

    /* =====================================================
       SUCCESS
    ===================================================== */
    if (status === "success") {

      // ================= AUTH / LOGIN =================
      if (action === "login") {
        Swal.fire({
          icon: "success",
          title: "Login Berhasil",
          text: "Selamat datang di dashboard admin ☕",
          footer: footerText,
          timer: 3000,
          showConfirmButton: false,
        });
      }

      // ================= PROFIL ADMIN =================
      else if (action === "update_profile") {
        Swal.fire({
          icon: "success",
          title: "Berhasil",
          text: "Profil admin berhasil diperbarui",
          footer: footerText,
          timer: 3000,
          showConfirmButton: false,
        });
      } else if (action === "password") {
        Swal.fire({
          icon: "success",
          title: "Berhasil",
          text: "Password admin berhasil diperbarui",
          footer: footerText,
          timer: 3000,
          showConfirmButton: false,
        });
      }
      // ================= RIWAYAT CLUSTERING =================
      else if (action === "riwayat_clustering" && ket === "deleted") {
        Swal.fire({
          icon: "success",
          title: "Berhasil",
          text: "Riwayat clustering berhasil dihapus beserta hasil clustering terkait",
          footer: footerText,
          timer: 3500,
          showConfirmButton: false,
        });
      }


      // ================= DATA KAFE =================
      else if (action === "addkafe") {
        Swal.fire({
          icon: "success",
          title: "Berhasil",
          text: "Data kafe berhasil ditambahkan",
          footer: footerText,
          timer: 3000,
          showConfirmButton: false,
        });
      } else if (action === "editkafe") {
        Swal.fire({
          icon: "success",
          title: "Berhasil",
          text: "Data kafe berhasil diperbarui",
          footer: footerText,
          timer: 3000,
          showConfirmButton: false,
        });
      } else if (action === "deletekafe") {
        Swal.fire({
          icon: "success",
          title: "Berhasil",
          text: "Data kafe berhasil dihapus",
          footer: footerText,
          timer: 3000,
          showConfirmButton: false,
        });
      }

      // ================= CLUSTERING =================
      else if (action === "clustering") {
        Swal.fire({
          icon: "success",
          title: "Clustering Selesai",
          text: "Proses clustering kafe berhasil dilakukan",
          footer: footerText,
          timer: 3500,
          showConfirmButton: false,
        });
      }
    }

    /* =====================================================
       WARNING
    ===================================================== */
    else if (status === "warning") {

      // ================= AUTH =================
      if (action === "auth" && ket === "belumlogin") {
        Swal.fire({
          icon: "warning",
          title: "Akses Ditolak",
          text: "Silakan login terlebih dahulu",
          footer: footerText,
          timer: 3000,
          showConfirmButton: false,
        });
      }

      // ================= PROFIL =================
      else if (action === "update_profile") {
        Swal.fire({
          icon: "warning",
          title: "Peringatan",
          text: "Username sudah digunakan admin lain",
          footer: footerText,
          timer: 3000,
          showConfirmButton: false,
        });
      } else if (action === "password" && ket === "notmatch") {
        Swal.fire({
          icon: "warning",
          title: "Peringatan",
          text: "Konfirmasi password tidak sesuai",
          footer: footerText,
          timer: 3000,
          showConfirmButton: false,
        });
      }

      // ================= DATA KAFE =================
      else if (action === "deletekafe") {
        Swal.fire({
          icon: "warning",
          title: "Gagal Menghapus",
          text: "Kafe masih memiliki data kuisioner",
          footer: footerText,
          timer: 3500,
          showConfirmButton: false,
        });
      }

      // ================= CLUSTERING =================
      else if (action === "clustering") {

        let message = "Terjadi peringatan pada proses clustering";

        if (ket === "file_missing") {
          message = "File dataset belum dipilih";
        } else if (ket === "invalid_column_count") {
          message = "Jumlah kolom CSV tidak sesuai template";
        } else if (ket === "invalid_header") {
          message = "Header CSV tidak sesuai format yang ditentukan";
        }
        // ================= RIWAYAT CLUSTERING =================
        else if (action === "riwayat_clustering" && ket === "blocked") {
          message: "Riwayat clustering terbaru tidak dapat dihapus";
        }

        Swal.fire({
          icon: "warning",
          title: "Peringatan",
          text: message,
          footer: footerText,
          timer: 4000,
          showConfirmButton: false,
        });
      }
    }

    /* =====================================================
       ERROR
    ===================================================== */
    else if (status === "error") {

      let message = "Terjadi kesalahan pada sistem";

      // ================= LOGIN =================
      if (action === "login") {
        message = "Username atau password salah";
      }

      // ================= CLUSTERING =================
      else if (action === "clustering") {
        if (ket === "invalid_request") {
          message = "Permintaan clustering tidak valid";
        } else if (ket === "upload_failed") {
          message = "Gagal mengunggah file dataset";
        } else if (ket === "file_unreadable") {
          message = "File dataset tidak dapat dibaca";
        } else if (ket === "python_error") {
          message = "Terjadi kesalahan saat menjalankan mesin clustering";
        } else if (ket === "invalid_python_output") {
          message = "Hasil clustering tidak valid";
        }
      } // ================= RIWAYAT CLUSTERING =================
      else if (action === "riwayat_clustering") {
        let message = "Terjadi kesalahan pada riwayat clustering";

        if (ket === "invalid_request") {
          message = "Permintaan tidak valid";
        } else if (ket === "not_found") {
          message = "Data riwayat clustering tidak ditemukan";
        } else if (ket === "file_missing") {
          message = "File CSV clustering tidak ditemukan di server";
        }
      }

      Swal.fire({
        icon: "error",
        title: "Gagal",
        text: message,
        footer: footerText,
        timer: 4000,
        showConfirmButton: false,
      });
    }

  })();
</script>