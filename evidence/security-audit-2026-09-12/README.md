# MHC SOS Security Remediation Evidence

Tanggal pengujian: 12 September 2026 (Asia/Jakarta)
Browser: Orca embedded browser 1.4.198
Target: `http://127.0.0.1:1881/` pada container pengujian sementara
Source: workspace dipasang read-only; container aplikasi lama pada port 1880 tidak diubah.

## Ringkasan

| No. | Temuan | Status | Evidence |
|---:|---|---|---|
| 1 | Vulnerable JavaScript Library | PASS | `04-runtime-security-checks.png`, `06-obsolete-ckeditor-removed-404.png`, `static-validation.txt` |
| 2 | Bruteforceable Login Page | PASS (safe-limit) | `02-invalid-captcha-rejected.png`, `static-validation.txt` |
| 3 | Weak Password Policy | PASS | `03-forced-password-policy.png`, `04-runtime-security-checks.png` |
| 4 | `.htaccess` Disclosure | PASS | `05-htaccess-protected-404.png` |
| 5 | No CAPTCHA | PASS | `01-login-captcha.png`, `02-invalid-captcha-rejected.png`, `07-login-captcha-fixed.png` |
| 6 | External URL Redirect | PASS | `04-runtime-security-checks.png`, `browser-test-results.json` |
| 7 | Dangerous JavaScript Functions | PASS | `04-runtime-security-checks.png`, `browser-test-results.json` |

## Daftar screenshot

1. `01-login-captcha.png` — kondisi awal sebelum perbaikan layout; CAPTCHA PNG dan tombol refresh termuat, tetapi komponennya saling berhimpitan.
2. `02-invalid-captcha-rejected.png` — CAPTCHA invalid ditolak dengan pesan yang sesuai.
3. `03-forced-password-policy.png` — akun dengan password lama diarahkan ke halaman wajib ganti password dan aturan kompleksitas ditampilkan.
4. `04-runtime-security-checks.png` — hasil evaluasi langsung di browser untuk versi library, redirect allowlist, DOM-XSS, guard password, dan penolakan password lemah.
5. `05-htaccess-protected-404.png` — akses ke `/.htaccess` menghasilkan `404 Not Found` tanpa versi nginx.
6. `06-obsolete-ckeditor-removed-404.png` — aset CKEditor lama tidak lagi dapat diakses.
7. `07-login-captcha-fixed.png` — kondisi sesudah perbaikan; gambar dan tombol refresh tersusun dalam satu baris, sedangkan input kode berada penuh di baris berikutnya.

## Verifikasi lanjutan UI CAPTCHA

Dilakukan pada 12 September 2026 pukul 02:51 WIB setelah laporan awal:

- Desktop 1281×1047: gambar 180×56 px, tombol 48×58 px, input 330×42 px; tidak ada overlap dan seluruh elemen berada di dalam form.
- Emulasi iPhone 12 390×844 (DPR 3): gambar tetap 180×56 px, tombol 48×58 px, input 300×42 px; tidak ada overlap dan seluruh elemen berada di dalam viewport/form.
- Tombol refresh mengganti URL gambar CAPTCHA dengan cache-buster, memuat ulang gambar berukuran 180×56 px, serta mengosongkan nilai input.
- Browser console: 0 error dan 0 warning.

## Catatan metodologi

- Screenshot 01–03 dan 07 merupakan tampilan aplikasi langsung.
- Screenshot 04 memakai panel evidence yang diinjeksi hanya ke DOM browser setelah evaluasi dijalankan. Panel tidak mengubah source aplikasi.
- Screenshot 05–06 memakai anotasi DOM untuk menampilkan URL target karena screenshot viewport Orca tidak menyertakan address bar. Respons `404 Not Found` di belakang anotasi adalah respons aktual.
- Password dan nilai CAPTCHA aktif tidak disimpan dalam laporan terstruktur. CAPTCHA pada screenshot telah kedaluwarsa setelah sesi pengujian dibersihkan.
- Password akun tidak diubah.
- Ambang lockout penuh tidak sengaja dihabiskan agar akun/IP pengguna tidak terkunci. Implementasi pembatas akun/IP dan rate limit nginx diverifikasi secara statis; satu respons penolakan CAPTCHA diverifikasi melalui browser.
- Pengujian CRUD/dashboard penuh berhenti pada guard wajib ganti password, sesuai perilaku keamanan yang diharapkan.

Gunakan `SHA256SUMS` untuk memverifikasi integritas screenshot.
