---
name: divera-medical-agent
description: Instruksi utama untuk agen AI yang mengerjakan proyek DiVera Medical.
---

## Aturan Utama (Guidelines)

> **PENTING:** Aturan-aturan di bawah ini **HANYA berlaku untuk pembuatan file/kode BARU**. Jangan melakukan perubahan pada file yang sudah ada hanya karena membaca aturan ini. File yang sudah ada dianggap sudah benar dan tidak perlu dimodifikasi untuk menyesuaikan aturan ini.

- **Database & CRUD:** Setiap fungsi CRUD database untuk suatu halaman wajib dipisahkan dan diletakkan di dalam file yang sesuai di folder `config/`.
- **Komentar Kode (Documentation):** Setiap file *function* PHP wajib diberi komentar penjelasan alur secara detail, baik menggunakan *block comment* maupun *inline comment* (contoh: `// Karena user_id di session adalah ID Pengguna, kita perlu ambil ID Pasien dari tabel pasien`). Berikan komentar di setiap baris atau tahapan eksekusi logika fungsi.
- **Assets:** Seluruh asset statis seperti CSS, JS, dan IMG wajib diletakkan di dalam folder `asset/` (misalnya `asset/css/`, `asset/js/`, `asset/img/`).
- **Notifikasi (Flash Session):** Setiap notifikasi (status & message) wajib disimpan ke `$_SESSION['flash']` (flash message), **BUKAN** di URL parameter (`?status=...&message=...`). Di halaman view, ambil flash dari session lalu tampilkan menggunakan SweetAlert2 (`sweetalert2@11` CDN). Contoh set: `$_SESSION['flash'] = ['status' => 'success', 'message' => 'Berhasil!'];`. Contoh ambil: `$flash = $_SESSION['flash']; unset($_SESSION['flash']);`.
- **Clean URL (.htaccess):** Proyek ini menggunakan `.htaccess` untuk URL rewriting. Semua pemanggilan/link file internal **TIDAK BOLEH** menyertakan ekstensi `.php`. Contoh benar: `href="login"`, `header("Location: ../register")`. Contoh salah: `href="login.php"`, `header("Location: ../register.php")`.
