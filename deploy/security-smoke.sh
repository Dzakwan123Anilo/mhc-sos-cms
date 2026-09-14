#!/bin/sh
set -eu

project_root="${CMS_TEST_ROOT:-/home/app}"
cd "$project_root"

nginx -t

for build_tool in gcc make git composer nano; do
    if command -v "$build_tool" >/dev/null 2>&1; then
        echo "Unexpected build tool in runtime image: $build_tool" >&2
        exit 1
    fi
done

php <<'PHP'
<?php
$required_extensions = array('dom', 'fileinfo', 'gd', 'mbstring', 'mysqli', 'zip');
foreach ($required_extensions as $extension) {
    if (!extension_loaded($extension)) {
        throw new RuntimeException('Missing PHP extension: '.$extension);
    }
}

if (PHP_MAJOR_VERSION !== 8 || PHP_MINOR_VERSION !== 4) {
    throw new RuntimeException('The runtime must use PHP 8.4.');
}
if (filter_var(ini_get('expose_php'), FILTER_VALIDATE_BOOLEAN)) {
    throw new RuntimeException('expose_php must be disabled.');
}
if (filter_var(ini_get('display_errors'), FILTER_VALIDATE_BOOLEAN)) {
    throw new RuntimeException('display_errors must be disabled.');
}
if (!defined('PASSWORD_ARGON2ID')) {
    throw new RuntimeException('Argon2id password hashing is unavailable.');
}

define('BASEPATH', __DIR__);
require_once 'application/libraries/MyPHPMailer.php';
if (PHPMailer\PHPMailer\PHPMailer::VERSION !== '7.1.1') {
    throw new RuntimeException('Unexpected PHPMailer version.');
}

// PHPExcel is retained for legacy exports; verify its critical round trip on PHP 8.4.
error_reporting(E_ERROR | E_PARSE);
ini_set('log_errors', '0');
require_once 'application/libraries/PHPExcel.php';
$workbook = new PHPExcel();
$workbook->getActiveSheet()->setCellValue('A1', 'security-smoke');
$spreadsheet = sys_get_temp_dir().'/mhc-security-smoke-'.bin2hex(random_bytes(6)).'.xlsx';
try {
    PHPExcel_IOFactory::createWriter($workbook, 'Excel2007')->save($spreadsheet);
    $loaded = PHPExcel_IOFactory::load($spreadsheet);
    if ($loaded->getActiveSheet()->getCell('A1')->getValue() !== 'security-smoke') {
        throw new RuntimeException('PHPExcel round-trip failed.');
    }
} finally {
    if (is_file($spreadsheet)) {
        unlink($spreadsheet);
    }
}

echo 'PHP '.PHP_VERSION.", PHPMailer 7.1.1, and PHPExcel round-trip: OK\n";
PHP

/usr/bin/supervisord -c /etc/supervisord.conf >/tmp/mhc-supervisord.log 2>&1 &
supervisor_pid=$!
cleanup() {
    kill "$supervisor_pid" >/dev/null 2>&1 || true
    wait "$supervisor_pid" >/dev/null 2>&1 || true
}
trap cleanup EXIT INT TERM

attempt=0
until php -r '$socket = @fsockopen("127.0.0.1", 1880, $error_number, $error_message, 1); if (!$socket) { exit(1); } fclose($socket);'; do
    attempt=$((attempt + 1))
    if [ "$attempt" -ge 40 ]; then
        cat /tmp/mhc-supervisord.log >&2
        exit 1
    fi
    sleep 0.25
done

php <<'PHP'
<?php
function http_request($path) {
    $context = stream_context_create(array('http' => array(
        'ignore_errors' => true,
        'timeout' => 3
    )));
    $body = file_get_contents('http://127.0.0.1:1880'.$path, false, $context);
    $response_headers = isset($http_response_header) ? $http_response_header : array();
    if (!isset($response_headers[0]) || !preg_match('/\s([0-9]{3})\s/', $response_headers[0], $match)) {
        throw new RuntimeException('Missing HTTP status for '.$path);
    }

    $headers = array();
    foreach ($response_headers as $header) {
        if (strpos($header, ':') !== false) {
            list($name, $value) = explode(':', $header, 2);
            $headers[strtolower(trim($name))] = trim($value);
        }
    }

    return array('status' => (int) $match[1], 'headers' => $headers, 'body' => $body);
}

$static = http_request('/themes/admin/atlant/js/plugins/jquery/jquery.min.js');
if ($static['status'] !== 200 || strpos($static['body'], 'jQuery v3.7.1') === false) {
    throw new RuntimeException('Approved jQuery asset was not served.');
}

$required_headers = array(
    'content-security-policy',
    'permissions-policy',
    'referrer-policy',
    'strict-transport-security',
    'x-content-type-options',
    'x-frame-options'
);
foreach ($required_headers as $header) {
    if (!isset($static['headers'][$header])) {
        throw new RuntimeException('Missing security header: '.$header);
    }
}
if (isset($static['headers']['x-powered-by'])) {
    throw new RuntimeException('X-Powered-By leaked by nginx.');
}
if (isset($static['headers']['server']) && preg_match('/\/[0-9]/', $static['headers']['server'])) {
    throw new RuntimeException('The Server header leaked a version.');
}

$blocked_paths = array(
    '/.htaccess',
    '/adminer.php',
    '/config.php',
    '/system/core/CodeIgniter.php',
    '/modules/auth/controllers/Auth.php',
    '/deploy/.variables',
    '/unexpected.php',
    '/themes/admin/atlant/js/plugins/tableexport/jspdf/jspdf.js',
    '/themes/admin/atlant/js/plugins/tour/bootstrap-tour-standalone.min.js',
    '/VUNWEBEXT_ABA%20Agustus.xlsx'
);
foreach ($blocked_paths as $path) {
    $response = http_request($path);
    if ($response['status'] !== 404) {
        throw new RuntimeException($path.' returned '.$response['status'].' instead of 404.');
    }
}

echo "nginx sensitive-file controls and response headers: OK\n";
PHP
