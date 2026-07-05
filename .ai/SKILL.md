---
name: log-skill
description: Wajib dijalankan untuk mencatat log setiap kali selesai melakukan perubahan kode.
license: MIT
compatibility: opencode
metadata:
  audience: maintainers
---

## What I do

- Membaca dan menambahkan log aktivitas yang baru saja dikerjakan ke dalam file `config/log.txt`.

## When to use me

- WAJIB (ALWAYS) digunakan tepat setelah selesai membuat file, melakukan refactor, menjalankan task, atau memperbaiki bug/error.
- WAJIB digunakan setiap kali ada modifikasi sekecil apapun pada source code.

## How I do it

1. Cek apakah file `../config/log.txt` ada. Jika tidak ada, buat file tersebut.
2. Read `../config/log.txt` untuk melihat log terakhir.
3. Append (tambahkan) log entri baru di baris paling bawah file dan beri tanda "[TERBARU]" pada Awal log terakhir.
4. Grup log berdasarkan tanggal hari ini.
5. Format penulisan: Aksi ('Modified', 'Created', 'Fixed', 'Deleted') + 'nama_file beserta path' + deskripsi singkat.
6. Jangan Gunakan Petik "" tapi Gunakan Petik ''

## Log format

```text
## 2026-05-23

### Kategori task
- [TERBARU] Modified 'components/navbar.blade.php' - tambah font size text lg
- Created 'components/keranjang.blade.php' - components untuk wadah keranjang
- Fixed Bug di 'dashboard.blade.php' - memperbaiki bug data yang tidak dapat terload
```

**PENTING:** Selalu append, jangan overwritter file yang sudah ada.
