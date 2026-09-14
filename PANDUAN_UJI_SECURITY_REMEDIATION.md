# Panduan Uji Hasil Perbaikan Security

Dokumen ini digunakan untuk memvalidasi perbaikan terhadap temuan pada
VUNWEBEXT_ABA Agustus.xlsx. Pengujian dilakukan pada **staging/local** yang
memiliki data uji. Jangan menjalankan fuzzing, brute force, atau active scan ke
production tanpa otorisasi tertulis.

Spreadsheet memiliki 12 baris, tetapi dua baris merupakan duplikasi temuan dan
temuan Laravel tidak berlaku untuk project CodeIgniter 3 ini. Panduan ini
mencakup 10 temuan unik yang diperbaiki, serta satu langkah pembuktian bahwa
temuan Laravel memang tidak relevan.

## 1. Prasyarat dan aturan pengujian

### Tools

Gunakan tools berikut. Tidak semua tools wajib dipakai pada setiap temuan.

| Tool | Kegunaan |
|---|---|
| Chrome/Firefox DevTools | Screenshot halaman, Console, Network, response header, cookie, dan DOM |
| curl | Pengujian HTTP reproducible, status code, redirect, dan header |
| jq, file, openssl | Membaca JSON, memeriksa tipe CAPTCHA, dan memeriksa artefak |
| rg | Static scan source code dan versi library |
| PHP 8.4 + Node.js | Lint dan smoke test lokal |
| Docker | Memeriksa image runtime, network mode, port, dan konfigurasi production |
| Nmap (nmap) | Verifikasi port yang benar-benar terbuka pada host staging |
| Trivy/Grype | Vulnerability scan image dan filesystem |
| OWASP ZAP atau Burp Suite Community | Repeater/Fuzzer dengan request terbatas dan passive scan |
| Wappalyzer/Retire.js | Identifikasi versi komponen dan library JavaScript |

### Menyiapkan target staging

Gunakan URL target staging, bukan URL production. Contoh berikut menggunakan
container lokal:

~~~bash
export PROJECT_ROOT=/home/zackone/Projects/Anilo/MHC_SOS/mhc-sos-cms
export BASE_URL=http://127.0.0.1:1880
export EVIDENCE_DIR=$PROJECT_ROOT/evidence/$(date -u +%Y%m%dT%H%M%SZ)
mkdir -p "$EVIDENCE_DIR"
cd "$PROJECT_ROOT"
~~~

Untuk functional test yang memerlukan database, siapkan akun uji dengan hak
minimum dan database staging. Jangan menyimpan password pada command history:

~~~bash
read -r -s TEST_PASSWORD
export TEST_USERNAME=akun-uji-non-produksi
~~~

Jika memakai deployment script, jalankan dari context yang benar dan gunakan
file variable lokal yang tidak di-commit:

~~~bash
cp deploy/.variables.example deploy/.variables
chmod 600 deploy/.variables
# Isi nilai staging di deploy/.variables, termasuk DB_* dan ENCRYPTION_KEY.
cd deploy
./start.sh
cd ..
~~~

Untuk smoke test tanpa menyalakan service production, build dari direktori
deploy dan jalankan image dengan filesystem aplikasi read-only:

~~~bash
cd "$PROJECT_ROOT/deploy"
docker build -t mhc-sos-cms:security-test .
cd "$PROJECT_ROOT"

docker run --rm --read-only \
  --tmpfs /run:rw,noexec,nosuid,size=16m \
  --tmpfs /tmp:rw,nosuid,size=64m \
  --tmpfs /var/log:rw,nosuid,size=16m \
  --tmpfs /var/lib/nginx:rw,nosuid,size=16m \
  --mount type=tmpfs,destination=/home/app/sessions,tmpfs-mode=0700 \
  --mount type=bind,src="$PROJECT_ROOT",dst=/home/app,readonly \
  mhc-sos-cms:security-test sh -c '
    mkdir -p /var/lib/nginx/logs /var/lib/nginx/tmp/client_body \
      /var/lib/nginx/tmp/proxy /var/lib/nginx/tmp/fastcgi \
      /var/lib/nginx/tmp/uwsgi /var/lib/nginx/tmp/scgi /var/log/nginx &&
    sh /home/app/deploy/security-smoke.sh'
~~~

Hasil yang diharapkan:

~~~text
nginx configuration ... successful
PHP 8.4.x, PHPMailer 7.1.1, and PHPExcel round-trip: OK
nginx sensitive-file controls and response headers: OK
~~~

### Aturan bukti

Untuk setiap pengujian simpan:

1. Screenshot browser atau terminal yang memperlihatkan URL/perintah, waktu,
   dan hasil.
2. Request/response mentah (curl -D, HAR, atau export request dari ZAP/Burp).
3. Output tool dalam format JSON/XML jika tersedia.
4. Catatan environment: commit/tag image, hostname staging, waktu UTC, dan
   akun/role uji (tanpa password).

Contoh penyimpanan bukti HTTP:

~~~bash
curl -sS -D "$EVIDENCE_DIR/headers.txt" \
  -o "$EVIDENCE_DIR/body.html" \
  -w 'status=%{http_code} size=%{size_download}\n' \
  "$BASE_URL/auth"
date -u +%FT%TZ | tee "$EVIDENCE_DIR/test-time.txt"
~~~

Sebelum membagikan bukti, hapus cookie session, Authorization header, token,
email, username sensitif, isi database, dan password. Screenshot DevTools harus
menutup nilai cookie dan form password.

---

## 2. Temuan: Admin Dashboard Exposed

**Tujuan:** membuktikan halaman admin dan controller terproteksi ketika tidak
ada session, tetapi tetap dapat digunakan oleh user yang sah sesuai ACL.

### Langkah A — akses tanpa session

Buka browser Incognito/Private Window, lalu akses:

~~~text
http://127.0.0.1:1880/meme/me
http://127.0.0.1:1880/meme/me/dashboard
http://127.0.0.1:1880/master/client
~~~

Ekspektasi: request diarahkan ke /auth atau ditolak; tidak boleh terlihat
dashboard, daftar client/member, menu admin, atau data bisnis.

Uji reproducible dengan curl tanpa cookie:

~~~bash
for path in /meme/me /meme/me/dashboard /master/client; do
  safe_name=$(printf '%s' "$path" | tr '/' '_')
  curl -sS -D "$EVIDENCE_DIR/admin-$safe_name.headers" \
    -o "$EVIDENCE_DIR/admin-$safe_name.body" \
    -w "$path status=%{http_code} redirect=%{redirect_url}\n" \
    "$BASE_URL$path"
done | tee "$EVIDENCE_DIR/admin-status.txt"
~~~

Periksa bahwa response tidak berisi data dashboard:

~~~bash
rg -ni 'sos_member|sos_client|claim|dashboard|provider' \
  "$EVIDENCE_DIR"/admin-*.body || true
~~~

### Langkah B — akses file/controller langsung

~~~bash
for path in \
  /application/config/config.php \
  /modules/auth/controllers/Auth.php \
  /system/core/CodeIgniter.php \
  /adminer.php \
  /unexpected.php; do
  curl --path-as-is -sS -D - -o /dev/null "$BASE_URL$path"
done | tee "$EVIDENCE_DIR/admin-direct-paths.headers"
~~~

Ekspektasi: semua path tersebut mengembalikan 404, bukan source code atau
halaman admin.

### Langkah C — akses dengan akun sah dan ACL

1. Login menggunakan akun staging dengan role minimum.
2. Screenshot dashboard setelah login berhasil.
3. Buka satu menu yang memang diizinkan dan satu menu yang tidak diizinkan.
4. Pastikan menu/endpoint yang tidak diizinkan tidak menampilkan data atau
   operasi privileged.
5. Di DevTools Network, simpan request untuk login dan request halaman admin
   (export HAR setelah menghapus cookie/token).

**Bukti lulus:** screenshot Incognito menunjukkan redirect/login, screenshot
akun sah menunjukkan dashboard, dan request direct path menunjukkan 404.

---

## 3. Temuan: Vulnerable JS Library

**Tujuan:** memastikan hanya versi library yang disetujui yang diload dan tidak
ada library lama/berisiko yang masih menjadi bagian dari surface publik.

### Langkah A — verifikasi versi dari browser

Login ke halaman dashboard yang memuat asset utama, buka DevTools Console, lalu
jalankan:

~~~javascript
console.table({
  jquery: window.jQuery && jQuery.fn.jquery,
  jqueryUi: window.jQuery && jQuery.ui && jQuery.ui.version,
  bootstrap: window.jQuery && jQuery.fn.tooltip &&
    jQuery.fn.tooltip.Constructor && jQuery.fn.tooltip.Constructor.VERSION,
  highcharts: window.Highcharts && Highcharts.version
});
~~~

Ekspektasi versi:

~~~text
jQuery      3.7.1
jQuery UI   1.14.1
Bootstrap   3.4.1
Highcharts  13.0.2
~~~

Screenshot harus memperlihatkan Console dan URL halaman, tetapi jangan
memperlihatkan cookie.

### Langkah B — verifikasi asset yang dikirim

~~~bash
curl -fsS "$BASE_URL/themes/admin/atlant/js/plugins/jquery/jquery.min.js" \
  | head -c 300 | tee "$EVIDENCE_DIR/jquery-prefix.txt"
curl -fsS "$BASE_URL/assets/js/vendor/highcharts/highcharts.js" \
  | head -c 300 | tee "$EVIDENCE_DIR/highcharts-prefix.txt"

rg -n --hidden --glob '*.js' --glob '*.css' \
  'jQuery v1\.|jquery-1\.|jquery-2\.|Morris|nvd3|rickshaw' \
  assets themes application modules || true
~~~

Path library lama harus tidak ditemukan sebagai asset aktif, atau harus
ditolak 404 oleh nginx. Uji beberapa path legacy:

~~~bash
for path in \
  /themes/admin/atlant/js/plugins/jquery/jquery-1.7.2.min.js \
  /themes/admin/atlant/js/plugins/nvd3/nv.d3.min.js \
  /themes/admin/atlant/js/plugins/filetree/jqueryFileTree.js \
  /themes/admin/atlant/js/plugins/tour/bootstrap-tour-standalone.min.js; do
  curl -sS -o /dev/null -w "$path %{http_code}\n" "$BASE_URL$path"
done | tee "$EVIDENCE_DIR/legacy-assets-status.txt"
~~~

Ekspektasi: asset lama dihapus atau mengembalikan 404.

### Langkah C — SCA dan image scan

Jalankan dari host tester, bukan menambah package manager ke image aplikasi:

~~~bash
retire --path "$PROJECT_ROOT" --outputformat json \
  --outputpath "$EVIDENCE_DIR/retire.json" || true
trivy fs --scanners vuln,secret,misconfig \
  --format json --output "$EVIDENCE_DIR/trivy-fs.json" "$PROJECT_ROOT"
trivy image --scanners vuln,secret,misconfig \
  --format json --output "$EVIDENCE_DIR/trivy-image.json" \
  mhc-sos-cms:security-test
~~~

Review hasil Retire.js/Trivy. Tidak boleh ada temuan High/Critical yang baru
atau tidak memiliki pengecualian tertulis. False positive pada library vendor
harus dicatat lengkap dengan alasan dan versi yang dipakai.

**Bukti lulus:** screenshot tabel versi Console, daftar request asset pada
Network, output legacy asset 404, serta file JSON SCA/image scan.

---

## 4. Temuan: Bruteforceable Login Page

**Tujuan:** memastikan percobaan gagal dibatasi berdasarkan IP dan username,
pesan tidak mengungkap akun valid, dan lockout bekerja server-side.

Gunakan satu akun staging dan password salah. Jangan memakai password akun nyata.
Konfigurasi default yang diuji adalah lima percobaan dalam 15 menit dan lockout
15 menit; konfirmasi nilai aktual dari AUTH_RATE_LIMIT_* deployment.

### Langkah A — login normal dan pesan generik

1. Buka /auth pada Incognito.
2. Isi username uji, password salah, dan CAPTCHA yang terlihat.
3. Kirim form.
4. Ulangi dengan username yang tidak ada.
5. Bandingkan response/status/pesan di Network.

Ekspektasi: pesan untuk username ada dan tidak ada tetap generik; tidak ada
perbedaan user exists, SQL error, stack trace, atau waktu response yang mudah
dipakai untuk enumerasi.

### Langkah B — uji batas percobaan secara aman

Kirim **enam** percobaan gagal secara berurutan, masing-masing dengan CAPTCHA
baru (CAPTCHA dikonsumsi setiap request). Gunakan browser, atau Burp Repeater/
ZAP Fuzzer dengan request terbatas dan nilai CAPTCHA yang diperbarui secara
manual. Jangan melebihi jumlah yang diperlukan.

Pada request setelah batas tercapai, response JSON di Network harus memuat
status 0, pesan rate limit, dan data.retry_after positif:

~~~bash
jq '{status, message, retry_after: .data.retry_after}' \
  "$EVIDENCE_DIR/login-lockout-response.json"
~~~

Jika perlu memeriksa state file pada staging:

~~~bash
find "$PROJECT_ROOT/sessions/auth-rate-limit" -maxdepth 1 -type f -print \
  | tee "$EVIDENCE_DIR/rate-limit-files.txt"
~~~

Jangan mempublikasikan nama file hash atau data session yang tidak diperlukan.

### Langkah C — bypass check

Dengan session yang sama, kirim request langsung tanpa melalui tombol UI:

~~~bash
curl -sS -b "$EVIDENCE_DIR/test.cookies" -c "$EVIDENCE_DIR/test.cookies" \
  -d 'login=1' \
  --data-urlencode 'username=akun-uji-non-produksi' \
  --data-urlencode 'password=wrong-password-only-for-staging' \
  --data-urlencode 'captcha=' \
  "$BASE_URL/auth/act_auth" \
  | jq '{status, message, data: {retry_after: .data.retry_after}}'
~~~

Ekspektasi: tetap ditolak dan tetap tercatat sebagai kegagalan; menghapus
JavaScript atau mengirim request langsung tidak boleh melewati kontrol.

**Bukti lulus:** screenshot CAPTCHA/login, screenshot response lockout dengan
retry_after, request ZAP/Burp yang sudah disanitasi, dan catatan bahwa request
keenam diblokir.

---

## 5. Temuan: Weak Password Policy Implementation

**Tujuan:** memastikan kebijakan password ditegakkan di server, bukan hanya oleh
validasi JavaScript, dan password baru tersimpan sebagai Argon2id.

### Langkah A — uji matriks password melalui UI

Gunakan menu pembuatan user atau /meme/user/add dengan akun admin staging.

| Nilai uji | Hasil yang diharapkan |
|---|---|
| Short1! | Ditolak: kurang dari 12 karakter |
| alllowercase123! | Ditolak: tidak ada huruf kapital |
| ALLUPPERCASE123! | Ditolak: tidak ada huruf kecil |
| NoNumberHere! | Ditolak: tidak ada angka |
| NoSpecial12345 | Ditolak: tidak ada karakter spesial |
| aaaaaPassword1! | Ditolak: karakter berulang |
| Password123! | Ditolak sebagai password umum |
| password >128 karakter | Ditolak |
| password valid 12–128 karakter dengan empat kategori | Diterima |
| konfirmasi berbeda | Ditolak |

Contoh password uji valid (jangan digunakan di production):
V3rify-Only-Stage!.

Untuk setiap baris, screenshot harus menunjukkan field yang diuji dan pesan
validasi. Jangan tampilkan nilai password; gunakan crop atau tutup field.

### Langkah B — bypass validasi client

Intercept form dengan Burp/ZAP atau kirim POST dari terminal dengan password
lemah. Hasil harus tetap ditolak walaupun class validasi HTML/JavaScript
dihapus. Simpan request tanpa field password atau gunakan masking sebelum
export.

Uji juga /meme/me/change_password dengan:

1. Password lama salah.
2. Password baru lemah.
3. Konfirmasi berbeda.
4. Password baru sama dengan password lama.
5. Password baru valid.

### Langkah C — verifikasi hash

Dengan akses read-only ke database staging, periksa prefix hash user uji; jangan
mengekspor hash lengkap:

~~~sql
SELECT user_name, LEFT(user_password, 12) AS hash_prefix
FROM app_user
WHERE user_name = 'akun-uji-non-produksi';
~~~

Ekspektasi: hash_prefix diawali $argon2id$, bukan MD5 32 karakter. Jika
staging memiliki akun legacy MD5, login sekali dengan password uji lalu ulangi
query; hash harus berubah menjadi Argon2id setelah migrasi opportunistic.

**Bukti lulus:** tabel hasil matriks (tanpa password), screenshot error server-
side, dan output query yang hanya menampilkan prefix $argon2id$.

---

## 6. Temuan: Outdated Software and Exposed Services

**Tujuan:** memastikan image runtime memakai baseline yang ditetapkan, tidak
memuat tool development yang tidak diperlukan, container tidak memakai host
network, dan port development/database tidak terbuka dari jaringan luar.

### Langkah A — inspect image runtime

~~~bash
docker image inspect mhc-sos-cms:security-test \
  --format '{{.Id}} {{.Created}}' \
  | tee "$EVIDENCE_DIR/image-id.txt"
docker run --rm mhc-sos-cms:security-test php -v \
  | tee "$EVIDENCE_DIR/php-version.txt"
docker run --rm mhc-sos-cms:security-test nginx -v \
  2> "$EVIDENCE_DIR/nginx-version.txt" || true
docker run --rm mhc-sos-cms:security-test sh -c \
  'for x in node npm yarn composer git gcc make nano; do command -v "$x" 2>/dev/null && echo "FOUND $x"; done' \
  | tee "$EVIDENCE_DIR/runtime-tools.txt"
~~~

Ekspektasi: PHP 8.4.x, nginx/Alpine sesuai baseline deployment, dan tidak ada
Node.js, npm, Composer, Git, compiler, atau editor runtime yang tidak diperlukan.

Scan image:

~~~bash
trivy image --severity HIGH,CRITICAL --ignore-unfixed \
  --format table mhc-sos-cms:security-test \
  | tee "$EVIDENCE_DIR/trivy-runtime.txt"
~~~

Review semua exception; --ignore-unfixed bukan alasan untuk mengabaikan package
yang sudah memiliki patch.

### Langkah B — verifikasi network mode dan publish port

Jalankan container staging, kemudian periksa:

~~~bash
docker inspect "$CONTAINER_NAME" \
  --format 'network={{.HostConfig.NetworkMode}} ports={{json .HostConfig.PortBindings}}' \
  | tee "$EVIDENCE_DIR/docker-network.txt"
docker port "$CONTAINER_NAME" | tee "$EVIDENCE_DIR/docker-port.txt"
~~~

Ekspektasi: network mode bukan host dan hanya port aplikasi yang dipublish
(default internal 1880). Tidak boleh ada binding untuk 3000, 8080, 33060,
atau 13088.

### Langkah C — scan dari host staging

Jalankan hanya terhadap IP/hostname staging yang disetujui:

~~~bash
export STAGING_HOST=staging.example.internal
ss -ltnp | tee "$EVIDENCE_DIR/host-listeners.txt"
nmap -Pn -sT -sV --version-light \
  -p 1880,3000,8080,33060,13088 "$STAGING_HOST" \
  -oA "$EVIDENCE_DIR/nmap-services"
~~~

Ekspektasi: port development/database tersebut closed atau filtered. Jika 1880
hanya untuk reverse proxy internal, dokumentasikan segment jaringan yang
diizinkan. Verifikasi juga firewall/security group dan reverse proxy, karena
repository tidak dapat membuktikan aturan jaringan host secara penuh.

**Bukti lulus:** output PHP/nginx/image scan, docker inspect, Nmap XML/normal
output, dan screenshot firewall/security group yang sudah menghapus alamat
internal sensitif.

---

## 7. Temuan: .htaccess Information Leak

**Tujuan:** memastikan .htaccess, dotfile, metadata repository, dan file
konfigurasi tidak dapat dibaca dari HTTP.

### Langkah A — request file sensitif

~~~bash
for path in \
  '/.htaccess' \
  '/.env' \
  '/.git/HEAD' \
  '/.git/config' \
  '/config.php' \
  '/adminer.php' \
  '/sos_db.sql' \
  '/deploy/.variables' \
  '/application/config/config.php' \
  '/modules/auth/controllers/Auth.php' \
  '/system/core/CodeIgniter.php'; do
  curl --path-as-is -sS -D - -o /dev/null \
    -w "path=$path status=%{http_code} bytes=%{size_download}\n" \
    "$BASE_URL$path"
done | tee "$EVIDENCE_DIR/sensitive-path-status.txt"
~~~

Uji variasi URL encoded yang lazim dipakai untuk bypass:

~~~bash
for path in '/%2ehtaccess' '/.%68taccess' '/.git%2fHEAD' '/%2eenv'; do
  curl --path-as-is -sS -o /dev/null \
    -w "$path status=%{http_code}\n" "$BASE_URL$path"
done | tee "$EVIDENCE_DIR/encoded-sensitive-path-status.txt"
~~~

Ekspektasi: 404 dan tidak ada isi source/config pada body. 404 dipilih agar
keberadaan file tidak terungkap melalui perbedaan 403/200.

### Langkah B — bukti konfigurasi

~~~bash
nginx -t 2>&1 | tee "$EVIDENCE_DIR/nginx-test.txt"
rg -n 'location ~ /\.|application|system|modules|deploy|adminer|config' \
  deploy/nginx/default.conf.example \
  | tee "$EVIDENCE_DIR/nginx-sensitive-rules.txt"
~~~

Screenshot browser 404 untuk /.htaccess dan screenshot Network response
merupakan bukti utama; konfigurasi nginx menjadi bukti pendukung.

---

## 8. Temuan: No Captcha Protection

**Tujuan:** memastikan CAPTCHA dibuat server-side, memiliki masa berlaku, tidak
disimpan di client, dan tidak dapat dilewati dengan request langsung.

### Langkah A — CAPTCHA ditampilkan dan tidak di-cache

~~~bash
rm -f "$EVIDENCE_DIR/captcha.cookies"
curl -sS -c "$EVIDENCE_DIR/captcha.cookies" \
  -D "$EVIDENCE_DIR/login.headers" \
  -o "$EVIDENCE_DIR/login.html" "$BASE_URL/auth"
curl -sS -b "$EVIDENCE_DIR/captcha.cookies" \
  -D "$EVIDENCE_DIR/captcha.headers" \
  -o "$EVIDENCE_DIR/captcha.png" "$BASE_URL/auth/captcha_image"

file "$EVIDENCE_DIR/captcha.png" | tee "$EVIDENCE_DIR/captcha.file.txt"
sha256sum "$EVIDENCE_DIR/captcha.png" | tee "$EVIDENCE_DIR/captcha.sha256"
rg -i 'cache-control|pragma|content-type' \
  "$EVIDENCE_DIR/captcha.headers"
~~~

Ekspektasi: file menyatakan PNG, Content-Type: image/png, dan header
Cache-Control: no-store/no-cache. Buka /auth di browser dan screenshot gambar
CAPTCHA, field input, serta tombol refresh.

### Langkah B — CAPTCHA wajib pada server

Dengan akun staging, gunakan DevTools Network atau Burp Repeater untuk mengirim
request berikut tanpa CAPTCHA:

~~~bash
curl -sS -b "$EVIDENCE_DIR/captcha.cookies" \
  -d 'login=1' \
  --data-urlencode 'username=akun-uji-non-produksi' \
  --data-urlencode 'password=wrong-password-only-for-staging' \
  --data-urlencode 'captcha=' \
  "$BASE_URL/auth/act_auth" \
  | jq '{status, message, captcha_url: .data.captcha_url}'
~~~

Ekspektasi: status 0, request ditolak, dan CAPTCHA baru diterbitkan. Menghapus
JavaScript atau memanggil endpoint langsung tidak boleh melewati kontrol.

### Langkah C — one-time dan expiry

1. Masukkan CAPTCHA yang benar dengan password salah; catat response gagal.
2. Kirim ulang kode CAPTCHA yang sama; harus ditolak sebagai invalid/expired.
3. Pada environment test dengan AUTH_CAPTCHA_TTL pendek, tunggu expiry lalu
   kirim kode lama; harus ditolak.
4. Refresh image dan pastikan nilai image berubah atau token session berubah.

Jangan tampilkan kode CAPTCHA atau cookie dalam screenshot yang dibagikan.

**Bukti lulus:** screenshot form CAPTCHA, PNG/headers, response request tanpa
CAPTCHA, dan response reuse/expired yang sudah disanitasi.

---

## 9. Temuan Laravel Debug Mode Enabled (tidak berlaku)

Temuan ini sudah dieliminasi dari scope perbaikan karena project adalah
CodeIgniter 3/HMVC. Langkah berikut dipakai sebagai bukti klasifikasi, bukan
sebagai exploit test:

~~~bash
test -f system/core/CodeIgniter.php && echo 'CodeIgniter bootstrap: present'
test ! -f artisan && echo 'Laravel artisan: absent'
test ! -d vendor/laravel && echo 'Laravel vendor tree: absent'

rg -n -i --glob '!AGENTS.md' --glob '!SOLVE_EVIDENCE*' \
  'Illuminate\\|Ignition|APP_DEBUG|Laravel|artisan' \
  index.php config.php application modules deploy system \
  || true
~~~

Ekspektasi: bootstrap CodeIgniter ada, tidak ada artisan/Laravel vendor tree,
dan tidak ada endpoint Ignition/debug Laravel. Screenshot terminal ini cukup;
tidak perlu mencoba /_ignition atau endpoint Laravel pada project yang bukan
Laravel.

---

## 10. Temuan: External URL Redirect

**Tujuan:** memastikan parameter next hanya menerima path lokal atau absolute URL
pada origin aplikasi, dan tidak dapat dipakai untuk phishing/open redirect.

Uji endpoint yang memakai redirect server-side (sort, per_page, atau set_status)
dengan akun staging yang sudah login. Contoh menggunakan /meme/me/sort:

~~~bash
for payload in \
  '/meme/me' \
  'https://mhc.example.test/meme/me' \
  'https://evil.example/' \
  '//evil.example/' \
  'javascript:alert(1)' \
  'https://mhc.example.test@evil.example/' \
  '%2f%2fevil.example%2f'; do
  printf '\nPAYLOAD=%s\n' "$payload"
  curl -sS -G -b "$EVIDENCE_DIR/auth.cookies" -D - -o /dev/null \
    --data-urlencode 'sort_by=user_id' \
    --data-urlencode 'sort_dir=asc' \
    --data-urlencode "next=$payload" \
    "$BASE_URL/meme/me/sort"
done | tee "$EVIDENCE_DIR/redirect-tests.headers"
~~~

Untuk setiap payload, baca header Location. Ekspektasi:

- path relatif aplikasi boleh diterima;
- absolute URL hanya boleh diterima jika scheme, host, dan port sama dengan
  APP_BASE_URL;
- evil.example, javascript:, scheme-relative //, backslash, dan userinfo
  harus diarahkan ke fallback lokal;
- tidak boleh ada Location menuju domain eksternal.

### Uji sisi browser

Pada halaman login, buka Console dan jalankan:

~~~javascript
[
  '/meme/me',
  'https://evil.example/',
  '//evil.example/',
  'javascript:alert(1)',
  'https://mhc.example.test@evil.example/'
].map(function (value) {
  return {value: value, accepted: login.sameOriginUrl(value)};
});
~~~

Ekspektasi: hanya URL lokal/same-origin menghasilkan URL; payload eksternal
menghasilkan null. Screenshot Console dan Network setelah login harus
memperlihatkan navigasi tetap pada origin aplikasi.

**Bukti lulus:** file header dengan semua payload, screenshot Console, dan
HAR/Burp request yang sudah menghapus cookie.

---

## 11. Temuan: Software Component Version Leaked

**Tujuan:** memastikan response tidak membocorkan nomor versi nginx/PHP atau
header framework yang tidak diperlukan.

### Langkah A — periksa seluruh tipe response

~~~bash
for path in /auth /not-found \
  /themes/admin/atlant/js/plugins/jquery/jquery.min.js \
  /application/config/config.php; do
  echo "=== $path ==="
  curl -sS -D - -o /dev/null "$BASE_URL$path"
done | tee "$EVIDENCE_DIR/version-headers.txt"

if rg -ni 'server: .*[/ ][0-9]|x-powered-by:|php/[0-9]|nginx/[0-9]' \
  "$EVIDENCE_DIR/version-headers.txt"; then
  echo 'Version disclosure detected' >&2
  exit 1
fi
~~~

Ekspektasi: tidak ada Server: nginx/1.x, X-Powered-By: PHP/x, atau header
serupa pada 200, 404, dan response error.

### Langkah B — verifikasi konfigurasi runtime

~~~bash
docker run --rm mhc-sos-cms:security-test php -i \
  | rg -i 'expose_php|display_errors|error_reporting' \
  | tee "$EVIDENCE_DIR/php-security-ini.txt"
docker run --rm mhc-sos-cms:security-test nginx -T 2>&1 \
  | rg -n 'server_tokens|fastcgi_hide_header' \
  | tee "$EVIDENCE_DIR/nginx-version-rules.txt"
~~~

Ekspektasi: expose_php => Off, display_errors => Off, server_tokens off, dan
fastcgi_hide_header X-Powered-By aktif.

### Langkah C — browser/ZAP

1. Buka /auth, satu route valid, dan satu route 404.
2. Di Network, screenshot Response Headers.
3. Jalankan ZAP passive scan atau Wappalyzer pada staging.
4. Simpan report dan catat setiap teknologi yang terdeteksi; nomor versi
   spesifik tidak boleh berasal dari response header/error page.

**Bukti lulus:** header text untuk beberapa status, screenshot DevTools, dan
passive-scan report.

---

## 12. Temuan: Dangerous JS Functions

**Tujuan:** memastikan code aplikasi tidak menggunakan primitive dynamic code
execution atau menyisipkan response server sebagai HTML mentah.

### Langkah A — static scan first-party

Jalankan scan pada JavaScript aplikasi, tidak termasuk vendor yang sudah
ditangani oleh SCA. Folder jstree/intro.js dan outro.js adalah fragment vendor
yang memang bukan file executable mandiri dan harus dicatat jika muncul.

~~~bash
rg -n --glob '*.js' \
  --glob '!themes/admin/atlant/js/plugins/**' \
  --glob '!assets/js/vendor/**' \
  --glob '!deploy/graphify-out/**' \
  --glob '!modules/graphify-out/**' \
  'eval[[:space:]]*\(|new[[:space:]]+Function|document\.write[[:space:]]*\(|\.innerHTML[[:space:]]*=' \
  application modules themes/admin/atlant/js \
  | tee "$EVIDENCE_DIR/dangerous-js-scan.txt"
~~~

Ekspektasi: tidak ada hit pada code first-party aktif. Jika ada hit pada
komponen vendor, masukkan ke Retire/SCA dan pastikan file tersebut tidak
digunakan oleh halaman aktif atau diblokir nginx.

Periksa syntax semua JavaScript aktif:

~~~bash
find application modules themes assets -type f -name '*.js' \
  ! -path '*/plugins/jstree/intro.js' \
  ! -path '*/plugins/jstree/outro.js' \
  ! -path '*/graphify-out/*' -print0 \
  | while IFS= read -r -d '' file; do node --check "$file" || exit 1; done \
  | tee "$EVIDENCE_DIR/node-check.txt"
~~~

### Langkah B — dynamic DOM/XSS regression test

Siapkan record uji di staging dengan marker harmless berikut pada field yang
ditampilkan di dashboard (misalnya nama member):

~~~text
SECURITY-MARKER <img src=x onerror=alert(1)>
~~~

1. Buka halaman yang menampilkan record tersebut.
2. Pastikan tidak ada alert, request tidak diinginkan, atau perubahan DOM yang
   mengeksekusi tag.
3. Inspect element; nilai harus berupa text node/escaped markup.
4. Ambil screenshot tabel dan DOM inspector, lalu hapus record uji.

Gunakan data staging saja. Jangan memasukkan marker ke production.

### Langkah C — export handler dan response AJAX

1. Buka halaman dashboard yang memiliki export.
2. Di Network, simpan response AJAX; nilai export harus muncul sebagai data
   terstruktur, bukan onclick="javascript:...".
3. Klik export dan pastikan hanya method export yang diizinkan dijalankan.
4. Secara lokal, ubah data-export-method menjadi eval atau constructor melalui
   DevTools lalu klik; handler harus menolak tanpa eksekusi.

**Bukti lulus:** output static scan kosong, node-check sukses, screenshot marker
yang tampil sebagai teks, dan HAR/DOM inspector tanpa eksekusi script.

---

## 13. Pengujian penutupan (regression gate)

Setelah semua kasus di atas selesai, jalankan gate berikut dan simpan output:

~~~bash
git diff --check | tee "$EVIDENCE_DIR/git-diff-check.txt"

php_ok=0
php_fail=0
while IFS= read -r -d '' file; do
  if php -l "$file" >/dev/null 2>&1; then
    php_ok=$((php_ok + 1))
  else
    php_fail=$((php_fail + 1))
  fi
done < <(find . -type f -name '*.php' -not -path './.git/*' -print0)
printf 'PHP lint: ok=%s failed=%s\n' "$php_ok" "$php_fail" \
  | tee "$EVIDENCE_DIR/php-lint-summary.txt"

sh -n deploy/*.sh \
  | tee "$EVIDENCE_DIR/shell-lint.txt"
~~~

Checklist penerimaan:

| Area | Kriteria lulus |
|---|---|
| Authentication | Protected route redirect, CAPTCHA wajib, rate limit/lockout aktif |
| Authorization | Role tanpa ACL tidak dapat melihat/mengubah resource privileged |
| Password | Password lemah ditolak server-side; hash baru $argon2id$ |
| Redirect | Tidak ada Location ke origin eksternal |
| HTTP hardening | File sensitif 404; header keamanan hadir; versi tidak bocor |
| Dependencies | Versi approved; tidak ada vulnerability High/Critical tanpa exception |
| Runtime/network | PHP 8.4; no host network; port development/database tertutup |
| JavaScript | Tidak ada dynamic-code sink first-party; data AJAX tidak dieksekusi |

Struktur evidence yang disarankan:

~~~text
evidence/<UTC-timestamp>/
├── screenshots/
├── headers.txt
├── sensitive-path-status.txt
├── redirect-tests.headers
├── nmap-services.nmap
├── nmap-services.xml
├── trivy-image.json
├── trivy-fs.json
├── retire.json
└── test-summary.md
~~~

test-summary.md harus menyebutkan build/image ID, tanggal UTC, tester, target
staging, hasil setiap temuan, dan link ke bukti. Jangan menganggap audit
production selesai sebelum image/config yang diuji benar-benar dideploy lalu
external re-scan dilakukan dari jaringan yang sebelumnya melihat temuan.
