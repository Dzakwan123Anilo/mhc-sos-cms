# Evidence Koding Perbaikan Temuan Security

Dokumen ini adalah evidence dari sisi source code untuk perubahan setelah audit
terhadap VUNWEBEXT_ABA Agustus.xlsx.

| Item | Nilai |
|---|---|
| Target | MHC SOS CMS |
| Framework | CodeIgniter 3.1.13 dengan HMVC MX |
| Baseline before | Git commit 318ea6b (HEAD sebelum perubahan working tree) |
| After | Working tree saat dokumen ini dibuat |
| Lingkup | 10 kategori temuan non-Laravel yang tersisa |
| Referensi pengujian runtime | PANDUAN_UJI_SECURITY_REMEDIATION.md |

## Cara membaca evidence

- Before adalah potongan kode dari git show 318ea6b:<path>.
- After adalah potongan kode yang saat ini berada di working tree.
- Potongan dibuat ringkas agar fokus pada perubahan keamanan; bagian yang tidak
  relevan ditandai komentar.
- Nilai password, database, SMTP, token, dan encryption key sengaja diganti
  menjadi [REDACTED]. Baris secret tidak direproduksi secara byte-for-byte.
- Dua baris spreadsheet untuk Admin Dashboard Exposed memakai bukti kode yang
  sama. Dua baris Software Component Version Leaked juga memakai bukti yang
  sama.
- Evidence source code tidak menggantikan verifikasi host staging/production.
  Hasil HTTP, screenshot, dan command pengujian ada di panduan terpisah.

## Ringkasan perubahan

| # | Temuan | Severity | Ref ID spreadsheet | Status evidence kode | Perubahan utama |
|---:|---|---|---|---|---|
| 1 | Admin Dashboard Exposed | High, Medium | 0bf9ef4c-e8af-4882-a383-e0a183e63417; 98b418c5-9dee-4528-b7a6-ec7fad2f08f5 | Diperkuat; guard login sudah ada di baseline | Route admin tetap melalui AdminController, route debug/generator dimatikan, hanya front controller yang boleh dieksekusi nginx |
| 2 | Vulnerable JS Library | Medium | 233cbbd1-7f40-4bca-9138-ab209d4fddf6 | Diperbaiki | Library utama diperbarui, dependency dashboard dipersempit, asset development lama tidak dilayani publik |
| 3 | Bruteforceable Login Page | Medium | 386ecff6-9516-4a25-ab09-ac130fa140ee | Diperbaiki | Rate limit atomik per IP dan username, lockout, pesan generik, dummy password verification |
| 4 | Weak Password Policy Implementation | Medium | 4811ff2b-c7cc-48fe-9281-c17c6c511ae7 | Diperbaiki | Panjang 12–128, kompleksitas, common-password/repeated-character check, confirmation server-side, Argon2id |
| 5 | Outdated Software and Exposed Services Leading to Potential RCE and Unauthorized Access | High | 48127084-e22e-44da-be09-4f7a6a09ca3b | Diperbaiki pada image/runtime; firewall perlu diverifikasi | PHP 8.4 Alpine 3.21, image minimal, host network dihapus, port dipublish eksplisit |
| 6 | .htaccess Information Leak | Medium | 911e8330-8ca5-49c9-bfce-ddccfc49f6c0 | Diperbaiki | nginx menolak dotfiles, source directory, file konfigurasi, dump, dan dokumen root |
| 7 | No Captcha Protection | Medium | abd869bf-8acd-45a8-9846-8df2364e77e2 | Diperbaiki | CAPTCHA dibuat dan diverifikasi server-side, one-time, TTL, terikat endpoint login POST |
| 8 | External URL Redirect | Medium | c9ee8af0-acc0-4360-a833-8df2364e77e2 | Diperbaiki | Redirect server dan navigasi JavaScript hanya menerima relative URL atau origin aplikasi |
| 9 | Software Component Version Leaked | Medium | d3b4c0f3-368a-4af8-bbc6-09bcf85d45c5; f7dd3209-ad4f-48f2-887b-42d35c69c4b4 | Diperbaiki pada konfigurasi runtime | server_tokens off, X-Powered-By disembunyikan, PHP error display dimatikan, image diperbarui |
| 10 | Dangerous JS Functions | Medium | dc4f6e12-bf10-4295-8596-46da6808d894 | Diperbaiki pada kode first-party | eval diganti JSON.parse, output memakai text, inline onclick diganti data attribute + allowlist |
| N/A | Laravel Debug Mode Enabled in Production (Ignition Exposed) | Medium | b939a393-fd5f-4a09-9267-9c0523c87130 | Tidak berlaku | Project adalah CodeIgniter 3; tidak ada route/package Laravel atau Ignition |

---

## 1. Admin Dashboard Exposed

### Konteks temuan

Temuan ini bersifat generik. Pemeriksaan source menunjukkan guard login untuk
controller admin sudah ada pada baseline. Karena itu tidak tepat mengklaim guard
tersebut baru dibuat. Perubahan yang dilakukan adalah menutup route
debug/generator yang dapat mengekspos data atau membuat akun terprediksi, serta
mempersempit surface nginx. Verifikasi tanpa session dan dengan akun ber-ACL
tetap wajib dilakukan.

### Before — guard admin dan route maintenance pada baseline

Berkas: application/libraries/AdminController.php dan modules/meme/controllers/Me.php

~~~php
// AdminController pada baseline: guard sudah ada
if (!isset($this->jCfg['is_login']) || $this->jCfg['is_login'] != 1) {
    $this->setReferer($this->uri->uri_string);
    redirect(site_url("auth"));
}

// Me.php pada baseline: endpoint internal masih menjalankan operasi
function te(){
    debugCode($this->jCfg['user']);
}

function update_user(){
    // query provider dan update user secara massal
    // ...
}

function generate_userxxxxx(){
    // membuat akun provider dengan password default
    $password = "[REDACTED: default password]";
    // ...
}
~~~

Berkas: deploy/nginx/default.conf.example

~~~nginx
# Baseline menerima file/path PHP melalui try_files dan regex PHP umum
location / {
    try_files $uri $uri/ /index.php?$query_string;
}

location ~ .php$ {
    fastcgi_pass unix:/var/run/php-fpm.sock;
    fastcgi_param SCRIPT_FILENAME /home/app$fastcgi_script_name;
}
~~~

### After — guard dipertahankan dan route berisiko dinonaktifkan

Berkas: modules/meme/controllers/Me.php

~~~php
// Endpoint debug/generator tidak lagi dapat dipanggil melalui URL
function te(){
    show_404();
}

function update_user(){
    show_404();
}

function generate_userxxxxx(){
    show_404();
}
~~~

Berkas: deploy/nginx/default.conf.example

~~~nginx
# Semua request aplikasi masuk ke front controller
location / {
    rewrite ^ /index.php last;
}

# Hanya index.php yang boleh diproses PHP
location = /index.php {
    include fastcgi_params;
    fastcgi_pass unix:/var/run/php-fpm.sock;
    fastcgi_param SCRIPT_FILENAME /home/app/index.php;
    fastcgi_param SCRIPT_NAME /index.php;
}

location ~ \.php(?:/|$) {
    return 404;
}
~~~

### Mengapa perubahan ini relevan

1. AdminController tetap memaksa pengguna tidak terautentikasi kembali ke auth;
   ACL tetap dievaluasi setelah login.
2. Endpoint debug tidak lagi mem-print session atau data internal.
3. Generator akun dengan default password tidak lagi tersedia sebagai route.
4. nginx tidak mengeksekusi file PHP arbitrary di application, modules,
   system, atau directory lain.
5. Ini bukan implementasi 2FA atau IP allowlist. Kontrol tersebut masih dapat
   ditambahkan bila diwajibkan risk owner.

Referensi kode after:
application/libraries/AdminController.php:37-40,
modules/meme/controllers/Me.php:14-28,
deploy/nginx/default.conf.example:63-80.

---

## 2. Vulnerable JS Library

### Before — versi lama dan library dashboard berlebih

Berkas: asset jQuery/Bootstrap baseline, modules/meme/controllers/Me.php,
themes/admin/atlant/dashboard.php

~~~text
/*! jQuery v2.1.1 ... */
/* Bootstrap v3.3.1 ... */

$this->js_plugins = array(
    'plugins/bootstrap/bootstrap-timepicker.min.js',
    'plugins/moment.min.js',
    'plugins/daterangepicker/daterangepicker.js',
    'plugins/knob/jquery.knob.min.js',
    'plugins/morris/raphael-min.js',
    'plugins/morris/morris.min.js'
);

// dashboard baseline
Morris.Bar({
    element: 'transaction-chart',
    data: data,
    xkey: 0,
    ykeys: [1, 2, 3]
});
~~~

Masalahnya bukan hanya nomor versi. Setiap dependency yang diload menjadi
bagian dari attack surface, sedangkan library chart lama tidak diperlukan oleh
dashboard.

### After — versi dipin dan dependency yang digunakan dipersempit

Berkas: asset jQuery/Bootstrap/jQuery UI after, modules/meme/controllers/Me.php,
themes/admin/atlant/dashboard.php

~~~text
/*! jQuery v3.7.1 ... */
/* Bootstrap v3.4.1 ... */
/*! jQuery UI - v1.14.1 ... */

$this->js_plugins = array(
    'plugins/bootstrap/bootstrap-select.js',
    'plugins/bootstrap/bootstrap-datepicker.js',
    'plugins/bootstrap/bootstrap-timepicker.min.js'
);

<script src="<?php echo base_url();?>assets/js/vendor/highcharts/highcharts.js"></script>
~~~

~~~javascript
var transactionData = [];
try {
    var parsedTransactionData = JSON.parse(transactionResponse);
    transactionData = Array.isArray(parsedTransactionData)
        ? parsedTransactionData : [];
} catch (error) {
    transactionData = [];
}

if (window.Highcharts) {
    Highcharts.chart('transaction-chart', {
        chart: { type: 'column' },
        series: [
            { name: 'Week', data: transactionData.map(function(row) {
                return Number(row[1]) || 0;
            }) }
        ]
    });
}
~~~

Morris, NVD3, Rickshaw, dan CKEditor sample tidak lagi dipakai oleh flow
dashboard; asset development yang masih ada di source diberi aturan 404 oleh
nginx. Bukti versi yang tampil di browser tetap harus diambil pada staging
karena file dapat berubah saat deployment.

Referensi kode after:
modules/meme/controllers/Me.php:31-49,92-102,156-166,
themes/admin/atlant/dashboard.php:297-347,
deploy/nginx/default.conf.example:24-28.

---

## 3. Bruteforceable Login Page

### Before — pencatatan log bukan rate limiter atomik

Berkas: modules/auth/controllers/Auth.php

~~~php
function act_auth(){
    if (isset($_POST['login'])) {
        $client_ip = $this->input->ip_address();
        $lockout_window = date("Y-m-d H:i:s", strtotime("-15 minutes"));

        $attempts = $this->db->where("log_ip", $client_ip)
            ->where("log_class", "auth")
            ->where("log_function", "act_auth")
            ->where("log_date >=", $lockout_window)
            ->count_all_results("app_log");

        $max_attempts = 10;
        if ($attempts > $max_attempts) {
            die(json_encode(array(
                "status" => 0,
                "message" => "Terlalu banyak percobaan login..."
            )));
        }

        // password check dilanjutkan tanpa CAPTCHA dan tanpa state lock
    }
}
~~~

Pola tersebut bergantung pada tabel activity log, tidak mengunci update
counter, menghitung dengan operator > (attempt ke-10 belum diblokir), dan tidak
membatasi username tertentu.

### After — rate limit per IP dan username dengan file lock

Berkas: modules/auth/controllers/Auth.php

~~~php
function act_auth()
{
    if (!$this->input->post('login')) {
        show_404();
        return;
    }

    $username = trim((string) $this->input->post('username'));
    $password = (string) $this->input->post('password');
    $captcha = trim((string) $this->input->post('captcha'));
    $client_ip = $this->input->ip_address();

    $retry_after = $this->_rate_limit_retry_after($client_ip, $username);
    if ($retry_after > 0) {
        $this->_issue_captcha();
        $this->_json_response(0, 'Terlalu banyak percobaan login...', array(
            'retry_after' => $retry_after,
            'captcha_url' => $this->_captcha_url()
        ));
    }

    // Semua kegagalan memakai jalur yang sama dan mencatat state
    if (!$this->_verify_captcha($captcha)) {
        $this->_login_failure($client_ip, $username,
            'Kode verifikasi tidak valid atau sudah kedaluwarsa.');
    }

    // password_verify/Argon2id dilakukan setelah kontrol anti-otomasi
    // ...
}
~~~

~~~php
private function _rate_limit_state($subject, $action = 'read')
{
    $file = $directory.'/'.hash('sha256', $subject).'.json';
    $handle = fopen($file, 'c+');

    if ($handle === false || !flock($handle, LOCK_EX)) {
        return array('retry_after' => (int) cfg('auth_rate_limit_lockout'));
    }

    // window, attempts, dan blocked_until dibaca/diperbarui
    if ($action === 'record' && $state['blocked_until'] <= $now) {
        $state['attempts']++;
        if ($state['attempts'] >= $max_attempts) {
            $state['blocked_until'] = $now + $lockout;
        }
    }

    // ftruncate + fwrite + flush dilakukan sebelum unlock
    // ...
}
~~~

Berkas: config.php

~~~php
$config['auth_rate_limit_attempts'] = max(1, (int)
    (getenv('AUTH_RATE_LIMIT_ATTEMPTS') ?: 5));
$config['auth_rate_limit_window'] = max(60, (int)
    (getenv('AUTH_RATE_LIMIT_WINDOW') ?: 900));
$config['auth_rate_limit_lockout'] = max(60, (int)
    (getenv('AUTH_RATE_LIMIT_LOCKOUT') ?: 900));
~~~

Perbaikan memakai pesan kegagalan generik dan menjalankan dummy password_verify
ketika username tidak ditemukan. Default efektif adalah 5 percobaan dalam
window 900 detik dan lockout 900 detik; nilainya dapat diubah melalui
environment deployment.

Referensi kode after:
modules/auth/controllers/Auth.php:57-123,218-307,
config.php:33-37.

---

## 4. Weak Password Policy Implementation

### Before — validasi terlalu pendek dan invalid password tetap dapat diteruskan

Berkas: application/helpers/core_helper.php dan modules/meme/controllers/User.php

~~~php
function validate_password_strength($password) {
    if (strlen($password) < 8) {
        return 'Password minimal 8 karakter';
    }
    if (!preg_match('/[A-Z]/', $password)) {
        return 'Password harus mengandung huruf kapital';
    }
    if (!preg_match('/[a-z]/', $password)) {
        return 'Password harus mengandung huruf kecil';
    }
    if (!preg_match('/[0-9]/', $password)) {
        return 'Password harus mengandung angka';
    }
    if (!preg_match('/[^a-zA-Z0-9]/', $password)) {
        return 'Password harus mengandung karakter spesial';
    }
    return true;
}

// User::save baseline: jika validation gagal, save tetap diteruskan
if (isset($_POST['user_password']) && trim($_POST['user_password']) != '') {
    $raw_pass = dbClean($_POST['user_password']);
    $validation = validate_password_strength($raw_pass);
    if ($validation === true) {
        $data['user_password'] = password_hash($raw_pass, PASSWORD_ARGON2ID);
    }
}
~~~

Kelemahannya: batas maksimum tidak ada, password umum/repeated character tidak
ditolak, confirmation tidak diverifikasi ulang di server, dan flow create user
tidak menolak password kosong secara eksplisit.

### After — policy terpusat dan enforcement server-side

Berkas: application/helpers/core_helper.php

~~~php
function validate_password_strength($password) {
    if (strlen($password) < 12) {
        return 'Password minimal 12 karakter';
    }
    if (strlen($password) > 128) {
        return 'Password maksimal 128 karakter';
    }
    if (!preg_match('/[A-Z]/', $password)) {
        return 'Password harus mengandung huruf kapital';
    }
    if (!preg_match('/[a-z]/', $password)) {
        return 'Password harus mengandung huruf kecil';
    }
    if (!preg_match('/[0-9]/', $password)) {
        return 'Password harus mengandung angka';
    }
    if (!preg_match('/[^a-zA-Z0-9]/', $password)) {
        return 'Password harus mengandung karakter spesial';
    }

    $common_passwords = array(
        'password', 'password123', 'password123!', 'qwerty', 'qwerty123',
        'admin', 'admin123', 'welcome', 'welcome123', '123456', '12345678'
    );
    if (in_array(strtolower($password), $common_passwords, true)) {
        return 'Password terlalu umum dan mudah ditebak';
    }
    // repeated-character pattern juga ditolak pada baris berikutnya
    // ...

    return true;
}
~~~

Berkas: modules/meme/controllers/User.php

~~~php
$user_id = trim((string) $this->input->post('user_id'));
$raw_pass = (string) $this->input->post('user_password');
$pass_confirmation = (string) $this->input->post('user_password2');

if ($user_id === '' && $raw_pass === '') {
    redirect($this->own_link.'?msg='.rawurlencode(
        'Password wajib diisi untuk pengguna baru').'&type_msg=error');
    return;
}

if ($raw_pass !== '') {
    $validation = validate_password_strength($raw_pass);
    if ($validation !== true) {
        redirect($this->own_link.'?msg='.rawurlencode($validation).'&type_msg=error');
        return;
    }
    if (!hash_equals($raw_pass, $pass_confirmation)) {
        redirect($this->own_link.'?msg='.rawurlencode(
            'Konfirmasi password tidak sama').'&type_msg=error');
        return;
    }

    $data['user_password'] = password_hash($raw_pass, PASSWORD_ARGON2ID);
}
~~~

Berkas: modules/meme/controllers/Me.php

~~~php
$validation = validate_password_strength($new_pass);
if ($validation !== true) {
    $pesan = $validation;
} elseif (!hash_equals($new_pass, $new_pass_confirmation)) {
    $pesan = 'Konfirmasi password baru tidak sama';
} elseif (hash_equals($old_pass, $new_pass)) {
    $pesan = 'Password baru harus berbeda dari password lama';
} else {
    $pass_baru = password_hash($new_pass, PASSWORD_ARGON2ID);
    // update app_user dengan hash baru
}
~~~

Form juga menampilkan maxlength 128 dan rule length[12,128], tetapi kontrol
server-side di atas tetap menjadi enforcement utama. Fallback MD5 pada
login/change-password hanya dibaca untuk migrasi akun lama yang berhasil
login; tidak ada writer MD5 baru.

Referensi kode after:
application/helpers/core_helper.php:404-443,
modules/meme/controllers/User.php:179-216,
modules/meme/controllers/Me.php:951-1000,
themes/admin/atlant/meme/user_form.php:132-149.

---

## 5. Outdated Software and Exposed Services Leading to Potential RCE and Unauthorized Access

### Batasan pemetaan

Temuan spreadsheet menyebut nginx/PHP lama, komponen Node/NestJS, dan beberapa
port publik. Source ini adalah aplikasi PHP CodeIgniter; tidak ada runtime
Node/NestJS pada Dockerfile yang diperbaiki. Yang dapat dibuktikan dari kode
adalah pemutakhiran image PHP/nginx, pengurangan paket runtime, dan penghapusan
host networking. Port firewall dan service di luar container tetap harus
diverifikasi dengan Nmap pada staging/production.

### Before — image lama dan host network

Berkas: deploy/Dockerfile dan deploy/run.sh

~~~dockerfile
FROM php:8.3.6-fpm-alpine3.18

RUN apk update
RUN apk --no-cache add \
    freetype freetype-dev zlib zlib-dev libpng libpng-dev \
    libjpeg-turbo libjpeg-turbo-dev libzip libzip-dev \
    oniguruma-dev libxml2-dev wget git supervisor bash nano composer

RUN docker-php-ext-install -j$(nproc) gd fileinfo dom mbstring zip pdo_mysql mysqli
~~~

~~~sh
# Baseline deployment
docker run -tid --restart=always \
  --net=host \
  -v $CMS_SOURCE_PATH:/home/app \
  --name=$NAME \
  $IMAGE:$TAG
~~~

--net=host membuat semua listener container ikut terlihat pada network namespace
host. Paket git, wget, nano, dan composer juga tidak diperlukan untuk runtime
production.

### After — runtime minimal, versi target, dan port eksplisit

Berkas: deploy/Dockerfile

~~~dockerfile
FROM php:8.4-fpm-alpine3.21

RUN apk add --no-cache \
        bash ca-certificates freetype libjpeg-turbo libpng libxml2 \
        libzip nginx oniguruma supervisor \
    && apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS freetype-dev libjpeg-turbo-dev libpng-dev \
        libxml2-dev libzip-dev oniguruma-dev zlib-dev \
    && docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install -j"$(nproc)" gd dom mbstring zip pdo_mysql mysqli \
    && apk del .build-deps

COPY fpm/security.ini /usr/local/etc/php/conf.d/zz-security.ini

RUN mkdir -p /home/app/sessions /run/nginx \
    && chown -R www-data:www-data /home/app/sessions \
    && chmod 0700 /home/app/sessions

WORKDIR /home/app
EXPOSE 1880
~~~

Berkas: deploy/run.sh

~~~sh
set -eu

# Konfigurasi wajib divalidasi sebelum container berjalan
if [ ! -f .variables ]; then
  echo "Missing deploy/.variables..." >&2
  exit 1
fi

# Deployment mempublish port aplikasi saja; tidak memakai --net=host
docker run -d --restart=always \
  -p "${APP_PORT:-1880}:1880" \
  -v "$CMS_SOURCE_PATH:/home/app" \
  --mount "type=volume,source=${CONTAINER_NAME}_sessions,target=/home/app/sessions" \
  -e DB_HOST -e DB_PORT -e DB_USERNAME -e DB_PASSWORD \
  -e DB_NAME -e DB_DRIVER -e ENCRYPTION_KEY \
  "$IMAGE_NAME:$IMAGE_TAG"
~~~

### Additional dependency change — PHPMailer

Berkas: application/libraries/MyPHPMailer.php

~~~php
// Before: adapter menunjuk ke PHPMailer 5 yang legacy
require_once('phpmailer5/class.phpmailer.php');

class MyPHPMailer extends PHPMailer {
    function __construct() {
        parent::__construct();
    }
}
~~~

~~~php
// After: adapter menunjuk ke source PHPMailer 7.1.1 yang divendor
require_once __DIR__.'/phpmailer/src/Exception.php';
require_once __DIR__.'/phpmailer/src/SMTP.php';
require_once __DIR__.'/phpmailer/src/PHPMailer.php';

class MyPHPMailer extends \PHPMailer\PHPMailer\PHPMailer {
    public function __construct($exceptions = true) {
        parent::__construct($exceptions);
    }
}
~~~

Konfigurasi SMTP sekarang dibaca dari environment dan SMTP debug dipaksa
nonaktif di MY_Controller::send_email. Ini mengurangi dependency legacy dan
mencegah credential/server detail tertanam di source.


Validasi tambahan menolak encryption key pendek, path source relatif, dan
deployment tanpa DB variable. Perubahan ini mengurangi service exposure dari
container; firewall/security group harus tetap membatasi port 3000, 8080,
33060, 13088, atau port lain yang tidak diperlukan.

Referensi kode after:
deploy/Dockerfile:1-40, deploy/run.sh:1-74.

---

## 6. .htaccess Information Leak

### Before — tidak ada aturan umum untuk semua dotfile

Berkas: deploy/nginx/default.conf.example

~~~nginx
# Baseline hanya memblokir beberapa nama tertentu
location ~ /\.git {
    deny all;
    return 403;
}
location ~ /\.env {
    deny all;
    return 403;
}
location = /adminer.php {
    deny all;
    return 403;
}

location / {
    try_files $uri $uri/ /index.php?$query_string;
}
~~~

.htaccess tidak termasuk rule tersebut dan fallback try_files dapat mencoba
menyajikan file yang berada di document root.

### After — dotfiles dan artefak repository ditolak sebelum routing aplikasi

~~~nginx
# Menolak .htaccess, .git, .env, dan dotfile lain.
location ~ /\.(?!well-known(?:/|$)) {
    return 404;
}

# Source dan state internal tidak menjadi file publik.
location ~* ^/(?:application|system|modules|deploy|sessions)(?:/|$) {
    return 404;
}

location ~* \.(?:bak|dist|ini|log|sh|sql|swp)$ {
    return 404;
}

# Dokumen/report di root juga tidak disajikan.
location ~* ^/[^/]+\.(?:csv|docx?|md|pdf|txt|xlsx?)$ {
    return 404;
}
~~~

Penggunaan status 404 menghindari pemberian sinyal bahwa file memang ada.
Aturan nginx bersifat server-side; permission file dan isi .htaccess pada host
tetap perlu diaudit.

Referensi kode after:
deploy/nginx/default.conf.example:17-41.

---

## 7. No Captcha Protection

### Before — form dan request login tidak membawa CAPTCHA

Berkas: themes/admin/atlant/login.php, themes/admin/atlant/js/login.meme.js,
modules/auth/controllers/Auth.php

~~~html
<form action="javascript:void(0)" class="form-horizontal">
    <input type="text" id="login_username" placeholder="Username"/>
    <input type="password" id="login_pass" placeholder="Password"/>
    <button class="btn btn-primary" id="btn_login">Log In</button>
</form>
~~~

~~~javascript
data: {
    username: $('#login_username').val(),
    password: $('#login_pass').val(),
    login: true
}
~~~

~~~php
// Auth::act_auth baseline: hanya username dan password diperiksa
$u = $this->input->post('username');
$p = $this->input->post('password');
// tidak ada _verify_captcha()
~~~

### After — CAPTCHA dibuat dan diverifikasi server-side

Berkas: themes/admin/atlant/login.php

~~~html
<form action="<?php echo site_url('auth/act_auth');?>"
      method="post" id="login_form" class="form-horizontal">
    <input type="hidden" name="login" value="1" />
    <!-- username dan password ... -->

    <img id="captcha_image"
         src="<?php echo site_url('auth/captcha_image');?>"
         width="180" height="54" alt="Kode verifikasi" />
    <input type="text" id="login_captcha" name="captcha"
           maxlength="6" autocomplete="off" />
    <button type="submit" id="btn_login">Log In</button>
</form>
~~~

Berkas: modules/auth/controllers/Auth.php

~~~php
function index()
{
    $this->_issue_captcha();
    $this->_v('login', array('message' => ''));
}

private function _issue_captcha()
{
    $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $code = '';
    for ($i = 0; $i < 6; $i++) {
        $code .= $alphabet[random_int(0, strlen($alphabet) - 1)];
    }

    $captcha = array(
        'code' => $code,
        'expires_at' => time() + (int) cfg('auth_captcha_ttl')
    );
    $this->session->set_userdata('auth_captcha', $captcha);
    return $captcha;
}

private function _verify_captcha($submitted)
{
    $captcha = $this->session->userdata('auth_captcha');
    $this->session->unset_userdata('auth_captcha');

    return is_array($captcha)
        && !empty($captcha['code'])
        && !empty($captcha['expires_at'])
        && $captcha['expires_at'] >= time()
        && hash_equals($captcha['code'], strtoupper($submitted));
}
~~~

Berkas: themes/admin/atlant/js/login.meme.js

~~~javascript
data: {
    username: $('#login_username').val(),
    password: $('#login_pass').val(),
    captcha: $('#login_captcha').val(),
    login: true
},
dataType: 'json'
~~~

CAPTCHA bukan hanya dekorasi UI: session value dihapus setelah satu kali
verifikasi, memiliki TTL, dan kegagalan masuk rate limiter. Request langsung ke
endpoint tidak dapat melewati kontrol dengan mengabaikan browser.

Referensi kode after:
modules/auth/controllers/Auth.php:14-55,185-223,
themes/admin/atlant/login.php:41-88,
themes/admin/atlant/js/login.meme.js:15-65.

---

## 8. External URL Redirect

### Before — destination berasal dari request/response tanpa allowlist origin

Berkas: application/libraries/AdminController.php,
modules/meme/controllers/Me.php, themes/admin/atlant/js/login.meme.js

~~~php
// AdminController baseline
$next = $this->input->get('next')
    && trim($this->input->get('next')) != ''
    ? $this->input->get('next') : site_url();
redirect($next);

// Me::set_theme baseline
$go = isset($_GET['next'])
    ? $this->input->get('next') : site_url('meme/me');
redirect($go);
~~~

~~~javascript
// login.meme.js baseline
document.location = obj.data.go_to;
~~~

Pola serupa dipakai helper pagination/status melalui document.location dengan
nilai next yang dibentuk dari request/current URL.

### After — validasi origin dipusatkan di server dan browser

Berkas: application/core/MY_Controller.php

~~~php
protected function _safe_redirect_url($candidate, $fallback = '')
{
    $fallback = $fallback !== '' ? $fallback : site_url();
    $candidate = trim((string) $candidate);

    if ($candidate === ''
        || preg_match('/[\x00-\x1F\x7F\\\\]/', $candidate)
        || strpos($candidate, '//') === 0) {
        return $fallback;
    }

    $target = @parse_url($candidate);
    if ($target === false || isset($target['user']) || isset($target['pass'])) {
        return $fallback;
    }

    if (isset($target['scheme']) || isset($target['host'])) {
        $origin = @parse_url(base_url());
        if (/* scheme, host, or port differs from configured origin */) {
            return $fallback;
        }
    }

    return $candidate;
}
~~~

Berkas: application/libraries/AdminController.php dan modules/meme/controllers/Me.php

~~~php
redirect($this->_safe_redirect_url($this->input->get('next'), site_url()));
// dipakai pada sort(), per_page(), dan set_status()

redirect($this->_safe_redirect_url(
    $this->input->get('next'), site_url('meme/me')
));
~~~

Berkas: themes/admin/atlant/js/mhc-security.js

~~~javascript
window.MHC.sameOriginUrl = function (value) {
    if (typeof value !== 'string' || value === '') {
        return null;
    }

    try {
        var target = new URL(value, window.location.href);
        if ((target.protocol !== 'http:' && target.protocol !== 'https:')
            || target.origin !== window.location.origin
            || target.username
            || target.password) {
            return null;
        }
        return target.href;
    } catch (error) {
        return null;
    }
};

window.MHC.navigate = function (value) {
    var target = window.MHC.sameOriginUrl(value);
    if (!target) {
        return false;
    }
    window.location.assign(target);
    return true;
};
~~~

Login response memakai pemeriksaan origin yang sama sebelum
window.location.assign. URL query dibentuk dengan encodeURIComponent, dan helper
PHP mengirim nilai ke JavaScript dengan json_encode sehingga karakter pengendali
tidak menjadi JavaScript source.

Referensi kode after:
application/core/MY_Controller.php:142-169,
application/libraries/AdminController.php:210-226,
modules/meme/controllers/Me.php:24-29,
themes/admin/atlant/js/mhc-security.js:18-44,
themes/admin/atlant/js/login.meme.js:40-80.

---

## 9. Software Component Version Leaked

### Before — version header dan PHP execution surface tidak dipersempit

Berkas: deploy/nginx/nginx.conf.example,
deploy/nginx/default.conf.example, deploy/Dockerfile

~~~nginx
# Baseline nginx.conf tidak menonaktifkan server_tokens
sendfile on;
keepalive_timeout 65;

# Baseline default.conf memakai handler PHP regex umum
location ~ .php$ {
    fastcgi_pass unix:/var/run/php-fpm.sock;
    fastcgi_param SCRIPT_FILENAME /home/app$fastcgi_script_name;
}
~~~

~~~dockerfile
FROM php:8.3.6-fpm-alpine3.18
~~~

Tanpa pengaturan FPM, header X-Powered-By dan detail error PHP dapat
memberikan petunjuk versi/stack.

### After — header dan error detail disembunyikan

Berkas: deploy/nginx/nginx.conf.example,
deploy/nginx/default.conf.example, deploy/fpm/security.ini

~~~nginx
http {
    sendfile on;
    server_tokens off;
    limit_req_zone $binary_remote_addr zone=auth_limit:10m rate=10r/m;
}

server {
    fastcgi_hide_header X-Powered-By;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    add_header Permissions-Policy "camera=(), microphone=(), geolocation=()" always;
    add_header Content-Security-Policy "object-src 'none'; base-uri 'self'; frame-ancestors 'self'" always;
}
~~~

~~~ini
expose_php = Off
display_errors = Off
display_startup_errors = Off
log_errors = On
~~~

Image sekarang menggunakan PHP 8.4 FPM Alpine 3.21 dan paket runtime minimal.
Konfigurasi mengurangi disclosure, tetapi bukti final harus berasal dari
response header host deployment, bukan hanya source.

Referensi kode after:
deploy/nginx/nginx.conf.example:23-34,
deploy/nginx/default.conf.example:8-15,67-80,
deploy/fpm/security.ini:1-4,
deploy/Dockerfile:1-40.

---

## 10. Dangerous JS Functions

### Before — eval, HTML sink, inline handler, dan redirect mentah

Berkas: themes/admin/atlant/js/export-data.js dan modules/ajax/controllers/Data.php

~~~javascript
// export-data.js baseline
obj = eval('(' + o + ')');
$('#info_export').html(obj.data.message);
document.location = obj.data.url_download;
~~~

~~~php
// Data.php baseline: data database menjadi JavaScript/HTML inline
$html .= '<a href="javascript:void(0)" onclick="EXPORT_DATA.activeMemberExport(0,\'\',\''
    . $r->client_id . '\',\'' . $r->gen_date . '\',0)">'
    . $r->main_insured . '</a>';
~~~

Inline handler dan string yang dirakit dari data database membuat batas antara
data dan executable code menjadi kabur. html() juga tidak aman bila pesan tidak
sepenuhnya trusted.

### After — JSON parser, text sink, structured data, dan method allowlist

Berkas: themes/admin/atlant/js/export-data.js

~~~javascript
obj = JSON.parse(o);
$('#info_export').text(obj.data.message);
EXPORT_DATA.download(obj.data.url_download);

$(document).on('click', '.js-export-link', function(event){
    event.preventDefault();

    var method = $(this).attr('data-export-method');
    var rawArgs = $(this).attr('data-export-args');

    if (!/^[A-Za-z][A-Za-z0-9]*Export$/.test(method || '')
        || typeof EXPORT_DATA[method] !== 'function') {
        $('#info_export').text('Aksi export tidak valid.');
        return;
    }

    var args;
    try {
        args = JSON.parse(rawArgs || '[]');
    } catch (error) {
        $('#info_export').text('Parameter export tidak valid.');
        return;
    }

    if (!Array.isArray(args)) {
        $('#info_export').text('Parameter export tidak valid.');
        return;
    }

    EXPORT_DATA[method].apply(EXPORT_DATA, args);
});
~~~

Berkas: modules/ajax/controllers/Data.php

~~~php
private function _html($value){
    return htmlspecialchars((string)$value,
        ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

private function _export_link($method, array $args, $value){
    $json = json_encode(array_values($args),
        JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);

    return '<a href="#" class="js-export-link" data-export-method="'
        . $this->_html($method) . '" data-export-args="'
        . $this->_html($json) . '">'
        . $this->_html($value) . '</a>';
}

private function _numeric_or_link($value, $method, array $args){
    $value = (int)$value;
    return $value > 0
        ? $this->_export_link($method, $args, $value)
        : $this->_html($value);
}
~~~

Semua response export yang dinavigasikan melewati EXPORT_DATA.download() lalu
window.MHC.navigate(), sehingga validasi same-origin tetap berlaku. Perubahan
ini juga meng-escape nama client/provider, label status, option kota, dan nilai
lain dari endpoint AJAX.

Referensi kode after:
themes/admin/atlant/js/export-data.js:1-42,353-382,
modules/ajax/controllers/Data.php:11-35,152-170,216-236.

---

## 11. Laravel Debug Mode Enabled in Production (Ignition Exposed) — Tidak Berlaku

### Before — label scanner tidak cocok dengan bootstrap aplikasi

Temuan mengasumsikan Laravel APP_DEBUG, artisan, dan Ignition. Baseline source
justru memuat CodeIgniter:

~~~php
// index.php / bootstrap baseline
define('ENVIRONMENT', 'production');
require_once BASEPATH.'core/CodeIgniter.php';
~~~

Tidak ada bukti Illuminate, route _ignition, atau package Laravel menjadi bagian
dari aplikasi aktif. Karena itu tidak ada before-code Laravel yang dapat
diperbaiki.

### After — verifikasi framework dan kontrol debug yang relevan

~~~text
system/core/CodeIgniter.php              exists
modules/*/controllers                     CodeIgniter HMVC controllers
artisan                                   absent
vendor/laravel                            absent
Illuminate or Ignition in active source  absent
~~~

Kekhawatiran umum tentang error detail tetap ditangani kontrol runtime:

~~~ini
expose_php = Off
display_errors = Off
display_startup_errors = Off
log_errors = On
~~~

Kesimpulan: temuan Laravel dieliminasi dari daftar perbaikan karena false
positive atau mismatch framework. Tidak ada route Ignition yang perlu ditutup.

---

## 12. Perubahan pendukung lintas temuan

Bagian ini bukan kategori tambahan pada spreadsheet, tetapi perubahan berikut
memperkuat temuan redirect, version disclosure, dan data exposure.

### 12.1 Secret configuration dipindah dari source ke environment

Berkas: config.php

~~~php
// Before (secret literal disamarkan)
$username = "[REDACTED]";
$password = "[REDACTED]";
$config['encryption_key'] = "[REDACTED]";

// After
$username = getenv('DB_USERNAME') ?: '';
$password = getenv('DB_PASSWORD') ?: '';
$config['encryption_key'] = getenv('ENCRYPTION_KEY') ?: '';
$config['smtp_password'] = getenv('SMTP_PASSWORD') ?: '';
~~~

deploy/run.sh memvalidasi environment wajib tersedia dan encryption key minimal
32 karakter. File deploy/.variables tidak dibagikan di evidence.

### 12.2 Token URL memakai authenticated encryption

Berkas: application/helpers/core_helper.php

~~~php
// Before: fixed key + AES-CBC tanpa authentication tag
$key = "[REDACTED: fixed key]";
$cipher = "AES-256-CBC";
$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher));
$encrypted = openssl_encrypt($data, $cipher, $key, 0, $iv);
return rawurlencode(base64_encode($iv . $encrypted));

// After: AES-256-GCM dengan random IV, tag, version, dan strict decode
$cipher = 'aes-256-gcm';
$iv = random_bytes(12);
$tag = '';
$encrypted = openssl_encrypt(
    (string) ($data ?? ''), $cipher, _application_crypto_key(),
    OPENSSL_RAW_DATA, $iv, $tag, 'mhc-sos-url-token', 16
);
$payload = "\x01".$iv.$tag.$encrypted;
return rtrim(strtr(base64_encode($payload), '+/', '-_'), '=');
~~~

_decrypt() menolak karakter non-URL-safe, payload tidak canonical, versi tidak
dikenal, dan tag GCM yang gagal diverifikasi. Bookmark token lama perlu
diterbitkan ulang setelah format/key berubah.

### 12.3 Activity log tidak lagi menyimpan credential

Berkas: application/core/MY_Controller.php

~~~php
// Before
$POST = isset($_POST) ? json_encode($_POST) : "";
$GET  = isset($_GET) ? json_encode($_GET) : "";

// After
$POST = json_encode($this->_redact_log_values(
    isset($_POST) ? $_POST : array()
));
$GET = json_encode($this->_redact_log_values(
    isset($_GET) ? $_GET : array()
));

protected function _redact_log_values($values){
    foreach ((array) $values as $key => $value) {
        if (preg_match('/(?:pass(?:word)?|secret|token|captcha|authorization|cookie|api[_-]?key|\bkey\b)/i',
            (string) $key)) {
            $result[$key] = '[REDACTED]';
        }
        // nested array diproses rekursif
    }
    return $result;
}
~~~

---

## 13. Traceability file dan simbol

| Temuan | File/simbol utama after |
|---|---|
| Admin Dashboard Exposed | AdminController constructor, Me::te, Me::update_user, Me::generate_userxxxxx, nginx front-controller locations |
| Vulnerable JS Library | Me asset lists, dashboard Highcharts, vendored asset headers |
| Bruteforceable Login Page | Auth::act_auth, _rate_limit_state, config auth settings |
| Weak Password Policy | validate_password_strength, User::save, Me::change_password |
| Outdated Software / Exposed Services | deploy/Dockerfile, deploy/run.sh, application/libraries/MyPHPMailer.php, deploy/supervisor/* |
| .htaccess Information Leak | nginx dotfile/source/file rules |
| No Captcha Protection | Auth::index, captcha_image, _issue_captcha, _verify_captcha, login view/JS |
| External URL Redirect | MY_Controller::_safe_redirect_url, admin redirect helpers, MHC.sameOriginUrl |
| Software Component Version Leaked | nginx configs, deploy/fpm/security.ini, Docker base image |
| Dangerous JS Functions | export-data.js, Data::_html, Data::_export_link, delegated export handler |
| Laravel Debug Mode | index.php, system/core/CodeIgniter.php, absence of Laravel artifacts |

---

## 14. Bukti verifikasi source code

Perintah berikut dijalankan dari root repository dan tidak mengubah source code.

### Diff dan whitespace

~~~bash
git diff --check
git diff 318ea6b --stat
git diff 318ea6b -- config.php modules/auth/controllers/Auth.php \
  modules/meme/controllers/User.php modules/meme/controllers/Me.php \
  application/helpers/core_helper.php application/core/MY_Controller.php
~~~

### Static scan fungsi berbahaya dan redirect

~~~bash
rg -n "\beval\s*\(|setTimeout\s*\(\s*['\"]|document\.location\s*=" \
  modules themes/admin/atlant application
rg -n "javascript:[[:space:]]*void|onclick=\"[^\"]*EXPORT_DATA" \
  modules themes/admin/atlant
rg -n "redirect\s*\(\s*\$[^,)]*(next|go)|document\.location\s*=" \
  application modules themes/admin/atlant
~~~

Expected result: tidak ada penggunaan first-party yang masih memakai eval,
string timer, inline export handler, atau redirect mentah yang menjadi jalur
user-controlled. Occurrence pada vendor/archive harus dinilai terpisah.

### Syntax checks yang sudah dijalankan

- PHP: 614/614 file lolos php -l.
- JavaScript: 410/410 file lolos node --check (fragmen jstree intro/outro
  tidak diperlakukan sebagai file program lengkap).
- Shell: 6/6 script lolos sh -n.
- git diff --check: bersih.
- Smoke image: nginx config valid, PHP 8.4, PHPMailer 7.1.1, PHPExcel
  round-trip, header, dan sensitive-file controls lulus.

### Bukti runtime

Gunakan langkah dan format artefak pada
PANDUAN_UJI_SECURITY_REMEDIATION.md. Minimal simpan:

1. Screenshot atau terminal capture yang memuat URL/perintah, timestamp, dan
   status.
2. Raw HTTP request/response atau HAR.
3. Header response, cookie flags, dan output scanner.
4. Commit/image tag serta akun/role uji tanpa password.

Jangan masukkan credential, cookie, Authorization header, database export, atau
isi deploy/.variables ke evidence. Deployment production belum dijalankan oleh
dokumen ini; re-scan eksternal setelah konfigurasi production diterapkan tetap
diperlukan.
