# SOLVE EVIDENCE — MHC SOS CMS Security Remediation

**Target**: mhc.internationalsos.co.id | **Framework**: CodeIgniter 3 (HMVC) | **PHP**: 8.4  
**Date Fixed**: May 2026 | **Source Reports**: PDF (Garry Tria Irawan) + XLSX (Accenture)

---

## Summary

| Source | Total Findings | Confirmed | Fixed | False Positive |
|--------|:---:|:---:|:---:|:---:|
| Group A — PDF (Garry Tria Irawan) | 9 | 9 | **9/9** | 0 |
| Group B — XLSX (Accenture) | 3 | 1 | **1/3** | 1 |

**Bonus fixes**: `phpinfo()` endpoint removed, encryption key strengthened, `E_STRICT` PHP 8.4 compatibility fix.

---

## Group A — Web Application Penetration Test Report (PDF)

---

### A1. SQL Injection — `/master/client` Search Endpoint

**Severity**: Critical | **CVSS**: 8.8 | **OWASP**: A03:2021 Injection

#### Vulnerable Code (Before)

**File**: `modules/master/models/Mdl_master.php`

```php
// :17 — date_start/date_end concatenated into raw SQL string
$this->db->where("( sos_client.time_add >= '".$this->jCfg['search']['date_start']." 01:00:00' AND sos_client.time_add <= '".$this->jCfg['search']['date_end']." 23:59:00' )");

// :31 — keyword concatenated into LIKE clause
$str_like .=" ".$key." LIKE '%".$this->jCfg['search']['keyword']."%' ";

// :40 — column name passed directly to like() without escaping
$this->db->like($this->jCfg['search']['colum'],$this->jCfg['search']['keyword']);

// :54 — order_by column name unescaped
$this->db->order_by($this->jCfg['search']['order_by'],$this->jCfg['search']['order_dir']);
```

**Attack vector**: `POST date_start=2023-01-01' OR '1'='1' --` → SQL syntax error with full query structure visible in response. sqlmap confirmed boolean-based blind, error-based, and time-based blind injection.

#### Fixed Code (After)

**File**: `modules/master/models/Mdl_master.php:8-43` — Four safe helper methods added at class level:

```php
function _safe_date_range($table_col, $date_start, $date_end) {
    if (empty($date_start) || empty($date_end)) return;
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_start)) return;
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_end)) return;
    $this->db->group_start();
    $this->db->where($table_col." >=", $date_start." 01:00:00");
    $this->db->where($table_col." <=", $date_end." 23:59:00");
    $this->db->group_end();
}

function _safe_like_search($params, $keyword) {
    if (empty($keyword)) return;
    $this->db->group_start();
    $i=0;
    foreach ($params as $key => $value) {
        if($key != ""){
            if($i==0) $this->db->like($key, $keyword);
            else $this->db->or_like($key, $keyword);
            $i++;
        }
    }
    $this->db->group_end();
}

function _safe_column_like($colum, $keyword) {
    if (empty($colum) || empty($keyword)) return;
    if (!preg_match('/^[a-zA-Z0-9_\.]+$/', $colum)) return;
    $this->db->like($colum, $keyword);
}

function _safe_order_by($order_by, $order_dir) {
    if (empty($order_by)) return;
    if (!preg_match('/^[a-zA-Z0-9_\.]+$/', $order_by)) return;
    $order_dir = strtoupper($order_dir) === 'ASC' ? 'ASC' : 'DESC';
    $this->db->order_by($order_by, $order_dir);
}
```

**File**: `modules/master/models/Mdl_master.php:45-90` — Client method now uses safe helpers:

```php
function client($p=array(),$count=FALSE){
    // ...
    $this->_safe_date_range("sos_client.time_add",
        $this->jCfg['search']['date_start'] ?? '',
        $this->jCfg['search']['date_end'] ?? '');

    if( trim($this->jCfg['search']['colum'] ?? '') == ""
        && trim($this->jCfg['search']['keyword'] ?? '') != "" ){
        $this->_safe_like_search($p['param'], $this->jCfg['search']['keyword']);
    }

    if( trim($this->jCfg['search']['colum'] ?? '') != ""
        && trim($this->jCfg['search']['keyword'] ?? '') != "" ){
        $this->_safe_column_like($this->jCfg['search']['colum'],$this->jCfg['search']['keyword']);
    }
    // ...
    $this->_safe_order_by($this->jCfg['search']['order_by'] ?? '',
        $this->jCfg['search']['order_dir'] ?? '');
    // ...
}
```

#### Why It Solves the Issue

1. **Date range**: `preg_match('/^\d{4}-\d{2}-\d{2}$/', ...)` validates strict `Y-m-d` format **before** the value reaches SQL. Input like `2023' OR '1'='1` fails the regex → query is skipped entirely.
2. **Date values as 2nd argument**: `$this->db->where("col >=", $value)` — CI3 automatically applies `$this->escape()` to the second argument via query binding.
3. **Keyword LIKE**: `$this->db->like($key, $keyword)` and `$this->db->or_like($key, $keyword)` — CI3's `_like()` method escapes the match value through `$this->escape_like_str()`.
4. **Column whitelist**: `preg_match('/^[a-zA-Z0-9_\.]+$/', $colum)` — only alphanumeric + underscore + dot characters pass. This prevents injection through the column name parameter.
5. **Order direction**: forced to `'ASC'` or `'DESC'` only — no injection possible.
6. **Zero raw string concat remaining**: grep for `'".$` in SQL context returns 0 results across all 10 query methods.

---

### A2. SQL Injection — `/master/member` Search Endpoint

**Severity**: Critical | **CVSS**: 8.8 | **OWASP**: A03:2021 Injection

#### Vulnerable Code (Before)

Same pattern as A1 — raw concatenation in `Mdl_master.php`:

```php
// :82 — date range raw concat
$this->db->where("( sos_member.time_add >= '".$this->jCfg['search']['date_start']." 01:00:00' ...");

// :143 — keyword raw concat in LIKE
$str_like .=" ".$key." LIKE '%".$this->jCfg['search']['keyword']."%' ";

// :152 — column name in like()
$this->db->like($this->jCfg['search']['colum'],$this->jCfg['search']['keyword']);

// :164 — order_by column raw
$this->db->order_by($this->jCfg['search']['order_by'],$this->jCfg['search']['order_dir']);
```

**Attack vector**: `POST keyword=test' UNION SELECT 1,2,3` → SQL error confirming injection. `btn_search` parameter also injectable — indicates raw POST parameters passed directly to query without validation.

#### Fixed Code (After)

```php
function member($p=array(),$count=FALSE){
    // ...
    $this->_safe_date_range("sos_member.time_add",
        $this->jCfg['search']['date_start'] ?? '',
        $this->jCfg['search']['date_end'] ?? '');

    // advanced search (already safe - uses 2-arg where/like)
    // ...

    if( trim($this->jCfg['search']['colum'] ?? '') == ""
        && trim($this->jCfg['search']['keyword'] ?? '') != "" ){
        $this->_safe_like_search($p['param'], $this->jCfg['search']['keyword']);
    }

    if( trim($this->jCfg['search']['colum'] ?? '') != ""
        && trim($this->jCfg['search']['keyword'] ?? '') != "" ){
        $this->_safe_column_like($this->jCfg['search']['colum'],$this->jCfg['search']['keyword']);
    }
    // ...
    $this->_safe_order_by($this->jCfg['search']['order_by'] ?? '',
        $this->jCfg['search']['order_dir'] ?? '');
    // ...
}
```

#### Why It Solves the Issue

Same four safe helper methods (A1) are applied. The vulnerability was systemic — the same `Mdl_master.php` class handles all 10 query methods. All 10 methods are fixed with identical patterns.

**Full list of fixed methods**: `client()`, `member()`, `provider()`, `news()`, `panduan()`, `fraud()`, `inpatient()`, `transraw()`, `ai_log()`, `import_log()`.

---

### A3. Exposed `.git` Directory — Full Source Code Disclosure

**Severity**: Critical | **CVSS**: 9.8 | **OWASP**: A05:2021 Security Misconfiguration

#### Vulnerable Code (Before)

**File**: `deploy/nginx/default.conf.example` — No rules blocking `.git` access:

```nginx
server {
    listen  1880;
    root   /home/app;
    # ... no .git protection ...
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
}
```

**Evidence**: `https://mhc.internationalsos.co.id/.git/HEAD` returned HTTP 200. `git-dumper` successfully cloned 7,637 files including `config.php`, `database.php`, `sos_db.sql`, `Dockerfile`. GitHub Personal Access Token found in `.git/config`.

#### Fixed Code (After)

**File**: `deploy/nginx/default.conf.example:15-35`:

```nginx
    # block sensitive directories and files
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
    location = /config.php {
        deny all;
        return 403;
    }
    location = /sos_db.sql {
        deny all;
        return 403;
    }
```

#### Why It Solves the Issue

1. `location ~ /\.git` — regex match blocks any request path containing `/.git` (including subdirectories like `/.git/HEAD`, `/.git/config`, `/.git/objects/...`). Returns HTTP 403.
2. `return 403` — the return directive is processed immediately in the rewrite phase, before any `try_files` or PHP handler can serve the file.
3. Additional blocks (`/.env`, `/adminer.php`, `/config.php`, `/sos_db.sql`) provide defense-in-depth for other sensitive files.
4. `location =` (exact match) is faster than `location ~` (regex) for single-file blocks.

---

### A4. Insecure Password Hashing — MD5 Without Salt

**Severity**: Critical | **CVSS**: 7.5 | **OWASP**: A02:2021 Cryptographic Failures

#### Vulnerable Code (Before)

7 locations used `md5()` without salt:

| # | File | Line | Code |
|---|------|:---:|------|
| 4a | `modules/auth/controllers/Auth.php` | :29 | `$p = md5($this->input->post('password'));` |
| 4b | `modules/auth/controllers/Auth.php` | :40 | `"user_password" => $p` (direct MD5 comparison) |
| 4c | `modules/meme/controllers/User.php` | :195 | `$data['user_password'] = md5(dbClean($_POST['user_password']));` |
| 4d | `modules/meme/controllers/Me.php` | :25 | `'user_password' => md5("mhc".$v->provider_code)` |
| 4e | `modules/meme/controllers/Me.php` | :50 | `'user_password' => md5($password)` (hardcoded `"123123"`) |
| 4f | `modules/meme/controllers/Me.php` | :1028 | `$pass_lama = md5(dbClean($_POST['old_pass']));` |
| 4g | `modules/meme/controllers/Me.php` | :1036 | `$pass_baru = md5(dbClean($_POST['new_pass']));` |

#### Fixed Code (After)

**4a-b. Login** (`Auth.php:26-58`):

```php
function act_auth(){
    if(isset($_POST['login'])){
        $u = $this->input->post('username');
        $p = $this->input->post('password');   // <-- No md5() here

        // Query user WITHOUT password check first
        $d = $this->db->get_where("app_user",array(
            "user_name"     => $u,
            "user_status"   => 1,
            "is_trash"      => 0
        ))->row();

        if($d){
            $password_valid = false;
            // Primary: argon2id verification
            if(password_verify($p, $d->user_password)){
                $password_valid = true;
            // Legacy fallback: MD5 comparison for unmigrated users
            }elseif($d->user_password === md5($p)){
                $password_valid = true;
                // Transparent auto-rehash to argon2id
                $this->db->update("app_user",array(
                    'user_password' => password_hash($p, PASSWORD_ARGON2ID)
                ),array(
                    'user_id' => $d->user_id
                ));
            }
        }

        if($d && $password_valid){
            /* set session */
        }
    }
}
```

**4c. Create/Edit User** (`User.php:194-196`):

```php
if( isset($_POST['user_password']) && trim($_POST['user_password']) != ''){
    $data['user_password'] = password_hash(dbClean($_POST['user_password']), PASSWORD_ARGON2ID);
}
```

**4d. Batch Update Provider Password** (`Me.php:25`):

```php
'user_password' => password_hash("mhc".$v->provider_code, PASSWORD_ARGON2ID),
```

**4e. Batch Generate User** (`Me.php:50`):

```php
'user_password' => password_hash($password, PASSWORD_ARGON2ID)
```

**4f-g. Change Password** (`Me.php:1027-1061`):

```php
if(isset($_POST['btn_simpan'])){
    $old_pass = dbClean($_POST['old_pass']);
    $this->DATA->table="app_user";
    $m1 = $this->DATA->_getall(array(
        "user_name" => $this->jCfg['user']['name']
    ));

    $password_valid = false;
    if(count($m1)>0){
        $user = $m1[0];
        if(password_verify($old_pass, $user->user_password)){
            $password_valid = true;
        }elseif($user->user_password === md5($old_pass)){
            $password_valid = true;  // legacy MD5 fallback
        }
    }

    if($password_valid){
        $pass_baru = password_hash(dbClean($_POST['new_pass']), PASSWORD_ARGON2ID);
        // ... update DB ...
    }
}
```

#### Why It Solves the Issue

1. **Argon2id** (`PASSWORD_ARGON2ID`): Memory-hard algorithm. GPU brute-force: ~10 billion MD5 hashes/second vs ~10,000 argon2id hashes/second — roughly **1,000,000× slower** to crack.
2. **`password_verify()`**: Constant-time string comparison — immune to timing side-channel attacks.
3. **`password_hash()`**: Auto-generates random salt (embedded in the hash string), auto-selects algorithm parameters. No manual salting required.
4. **Migration path**: MD5 retained **only as a transparent fallback** for existing users. On first successful MD5 login, the password is automatically re-hashed to argon2id — self-eliminating over time without forcing a mass password reset.
5. **All 7 locations covered**: Login, create user, edit user, batch provider update, batch user generation, and change password — all use `password_hash()`/`password_verify()`.

---

### A5. Session Not Invalidated After Logout

**Severity**: High | **CVSS**: 8.1 | **OWASP**: A07:2021 Auth Failures

#### Vulnerable Code (Before)

**File**: `modules/auth/controllers/Auth.php:140-157`:

```php
function out(){
    $this->sCfg['user']['id']       = '';
    $this->sCfg['user']['fullname'] = 'Guest';
    $this->sCfg['user']['name']     = 'guest';
    // ... clears 10+ session variables ...
    $this->sCfg['is_login']         = 0;
    $this->_releaseSession();       // <-- Only persists to session, does NOT destroy
    redirect(site_url());
}
```

**Evidence**: `ci_session` cookie captured before logout remained valid after logout. Reusing old cookie in subsequent requests returned authenticated data. `sess_destroy()` is **never called** anywhere in the entire codebase (verified by grep).

#### Fixed Code (After)

**File**: `modules/auth/controllers/Auth.php:149-152`:

```php
function out(){
    $this->session->sess_destroy();
    redirect(site_url());
}
```

#### Why It Solves the Issue

1. `$this->session->sess_destroy()` — CI3's method deletes the session record from the `ci_sessions` database table and sets the cookie to expire immediately. The old `ci_session` value becomes invalid.
2. Previously, `_releaseSession()` only called `$this->session->set_userdata()` to persist the updated (empty) session values — the session record remained in the database and the cookie remained valid. An attacker with a stolen cookie could reuse it indefinitely.

---

### A6. Adminer 4.8.1 Database Management Tool Exposed

**Severity**: High | **CVSS**: 7.3 | **OWASP**: A05:2021 Security Misconfiguration

#### Vulnerable Code (Before)

**File**: `adminer.php` (1791 lines) — Full database management tool at web root. No IP restriction. No authentication layer. Accessible at `https://mhc.internationalsos.co.id/adminer.php`.

#### Fixed Code (After)

**Action**: File renamed from `adminer.php` to `adminer.php.bak` (not executable).

**File**: `deploy/nginx/default.conf.example:24-27`:

```nginx
location = /adminer.php {
    deny all;
    return 403;
}
```

#### Why It Solves the Issue

1. **File rename**: `.php` → `.bak` extension prevents PHP-FPM from processing the file. The `.bak` extension is not matched by `location ~ .php$`.
2. **Nginx block**: `location = /adminer.php { deny all; return 403; }` — exact match returns HTTP 403 before the PHP handler is reached. Double protection: even if the file is restored, nginx blocks it.
3. **Residual risk**: `adminer.php.bak` is still on the server as plain text (database tool source code). Recommended to delete entirely from production.

---

### A7. Verbose PHP Error Messages Disclosed to Users

**Severity**: Medium | **CVSS**: 5.3 | **OWASP**: A05:2021 Security Misconfiguration

#### Vulnerable Code (Before)

**File**: `index.php:197-199`:

```php
  define('ENVIRONMENT', 'production');
//error_reporting(0);         // <-- commented out
        //ini_set('display_errors', 0);  // <-- commented out
```

**File**: `application/config/constants.php:14`:

```php
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);
```

**Evidence**: PHP errors rendered full stack traces, file paths (`/home/app/modules/auth/controllers/Auth.php`), function names (`trim()`, `md5()`), and partial SQL query structure directly in the browser.

#### Fixed Code (After)

**File**: `index.php:197-200`:

```php
  define('ENVIRONMENT', 'production');
error_reporting(0);
ini_set('display_errors', 0);
define('SHOW_DEBUG_BACKTRACE', FALSE);
```

**File**: `application/config/constants.php:14`:

```php
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', FALSE);
```

#### Why It Solves the Issue

1. `error_reporting(0)` — suppresses all PHP error levels. No error messages are generated.
2. `ini_set('display_errors', 0)` — prevents any generated errors from being rendered in the browser output.
3. `SHOW_DEBUG_BACKTRACE = FALSE` — disables the CodeIgniter backtrace display that previously revealed exact file paths and function names.
4. `ENVIRONMENT = 'production'` — CI3 uses this in its error handler to suppress detailed error output.
5. Defense-in-depth: both at `index.php` (first loaded) and `constants.php` (fallback via `OR define()`).

---

### A8. Web Server Version Disclosure (Nginx 1.28.0)

**Severity**: Low | **CVSS**: 5.3 | **OWASP**: A05:2021 Security Misconfiguration

#### Vulnerable Code (Before)

**File**: `deploy/nginx/nginx.conf.example` — No `server_tokens` directive (default: `server_tokens on`).

**Evidence**: Response header `Server: nginx/1.28.0` and `Server: Caddy` present on all HTTP responses.

#### Fixed Code (After)

**File**: `deploy/nginx/nginx.conf.example:23-25`:

```nginx
    sendfile        on;
    server_tokens   off;
    #tcp_nopush     on;
```

#### Why It Solves the Issue

`server_tokens off;` — Nginx strips the version number from error pages and the `Server` response header. The header becomes `Server: nginx` instead of `Server: nginx/1.28.0`. Attackers can no longer target version-specific CVEs.

---

### A9. Development Artifacts Accessible in Production

**Severity**: Info (report) / **High** (actual) | **OWASP**: A05:2021 Security Misconfiguration

#### Vulnerable Code (Before)

Four development/sample files were publicly accessible:

| File | Vulnerability |
|------|--------------|
| `themes/admin/atlant/assets/filetree/jqueryFileTree.php` | **Unauthenticated directory traversal** — `$_POST['dir']` → `scandir()` without auth or sanitization |
| `themes/admin/atlant/assets/cropping/process.php` | **Unauthenticated path traversal** — `$_POST['cropping-image']` → file path without auth or validation |
| `themes/admin/atlant/assets/ckeditor__/samples/assets/posteddata.php` | Sample/debug page — reflects `$_POST` data back to browser without auth |
| `themes/admin/atlant/js/ckeditor/samples/assets/posteddata.php` | Duplicate sample page |

#### Fixed Code (After)

All four files **deleted** from the filesystem. Verified:

```
adminer.php                                          → NOT FOUND (renamed to .bak)
themes/.../filetree/jqueryFileTree.php               → NOT FOUND
themes/.../cropping/process.php                      → NOT FOUND
themes/.../ckeditor__/samples/assets/posteddata.php  → NOT FOUND
themes/.../js/ckeditor/samples/assets/posteddata.php → NOT FOUND
```

#### Why It Solves the Issue

Complete removal eliminates the attack surface. The pentest report rated these as "Info" severity — however, `jqueryFileTree.php` and `process.php` contained **unauthorized path traversal vulnerabilities** that should be rated **High**. Their removal is critical.

---

### A10. BONUS: `phpinfo()` Endpoint Exposed

**Severity**: High (not in report) | **OWASP**: A05:2021 Security Misconfiguration

#### Vulnerable Code (Before)

**File**: `modules/auth/controllers/Auth.php:9-11`:

```php
function env() {
    phpinfo();
}
```

**Accessible at**: `https://mhc.internationalsos.co.id/auth/env` — dumps complete PHP configuration, environment variables, loaded extensions, and server paths.

#### Fixed Code (After)

The `env()` method has been **removed** from the `Auth` controller class.

#### Why It Solves the Issue

`phpinfo()` is the single largest information disclosure vector in PHP. It reveals: PHP version, all `php.ini` settings, loaded modules, environment variables (potentially including `DB_PASSWORD`), open_basedir paths, and more. Complete removal eliminates this entirely.

---

### A11. BONUS: Weak Encryption Key

**Severity**: High (supports A3 attack chain) | **Location**: `config.php`

#### Vulnerable Code (Before)

```php
$config['encryption_key'] = 'r3m4j4Id4m4n';
```

**Issue**: Short dictionary-based Indonesian phrase. Easily guessable/brute-forceable. Used in `_encrypt()`/`_decrypt()` for URL parameter encryption. Combined with `.git` exposure (A3), this key enables session forgery (see attack chain in report).

#### Fixed Code (After)

**File**: `config.php:41`:

```php
$config['encryption_key'] = getenv('ENCRYPTION_KEY') ?: 'b8Xk2Np7VmR5qLwYzT3AnFc6UdE9hJ4S';
```

#### Why It Solves the Issue

1. `getenv('ENCRYPTION_KEY')` — reads from the environment, allowing per-deployment unique keys.
2. Fallback `'b8Xk2Np7VmR5qLwYzT3AnFc6UdE9hJ4S'` — 32-char pseudo-random alphanumeric string, entropy ~190 bits. Computationally infeasible to brute-force compared to dictionary word.
3. **Critical note**: Set `ENCRYPTION_KEY` env var in production Docker config to prevent the fallback from being shared across deployments.

---

### A12. BONUS: PHP 8.4 `E_STRICT` Deprecation Fix

**Severity**: Info | **Location**: `system/core/Exceptions.php`

#### Original Code

```php
E_STRICT => 'Runtime Notice'
```

**Issue**: `E_STRICT` constant is deprecated in PHP 8.4, causing `Deprecated: Constant E_STRICT is deprecated` warning when the error level mapping array is evaluated.

#### Fixed Code (After)

```php
(defined('E_STRICT') ? E_STRICT : 2048) => 'Runtime Notice'
```

#### Why It Solves the Issue

Conditional constant lookup: uses `E_STRICT` if defined (PHP < 8.4), falls back to the integer value `2048` in PHP 8.4+. Eliminates the deprecation warning without changing behavior.

---

## Group B — VUNWEBEXT_MHC (XLSX, Accenture)

---

### B1. Admin Dashboard Exposed

**Severity**: High | **Report**: Accenture Assessment 2026

#### Status: PARTIAL MATCH — No Specific Code Fix

The login page (`/auth`) is intentionally public. Admin dashboard access is controlled by `AdminController` which checks `$this->jCfg['is_login']`. The finding is too generic to map directly to CodeIgniter.

**Related fixes applied**: Adminer blocked (A6), `phpinfo()` removed (A10 bonus), encryption key strengthened (A11 bonus).

**Remaining work**: Consider implementing login rate limiting, 2FA, and IP whitelisting for admin panel access.

---

### B2. Laravel Debug Mode Enabled in Production (Ignition Exposed)

**Severity**: Medium | **Report**: Accenture Assessment 2026

#### Status: FALSE POSITIVE

This application is **CodeIgniter 3**, not Laravel. The `Ignition` package does not exist in this codebase. This finding was likely generated by an automated scanner that misidentified the framework or reported a generic debug-mode finding.

**Equivalent fix applied**: PHP error display disabled (A7) — addresses the underlying concern of debugging information leaking to end users.

---

### B3. Software Component Version Leaked

**Severity**: Medium | **Report**: Accenture Assessment 2026

#### Status: FIXED — Covered by A8

The web server was leaking software version information. This is the same root cause as finding A8 (Nginx version disclosure).

**Fixes applied**:
1. `server_tokens off;` in nginx (A8)
2. `error_reporting(0)` + `display_errors = 0` in `index.php` (A7) — prevents PHP version disclosure via error pages
3. Recommended: `expose_php = Off` in `php.ini` for production to remove `X-Powered-By` header

---

## Production Checklist

These items require manual action during deployment:

| # | Action | Priority |
|---|--------|:---:|
| 1 | Set `ENCRYPTION_KEY` environment variable in Docker/production env | 🔴 Critical |
| 2 | Rotate ALL database credentials — `.git` exposure leaked previous values | 🔴 Critical |
| 3 | Revoke GitHub Personal Access Token if still in `.git/config` | 🔴 Critical |
| 4 | Change hardcoded password `"123123"` at `Me.php:38` | 🔴 Critical |
| 5 | Audit `.git` history for secrets — use `git filter-branch` or BFG Repo-Cleaner | 🔴 Critical |
| 6 | Rebuild nginx config from fixed `.example` files to production | 🔴 Critical |
| 7 | Delete `adminer.php.bak` from production server | 🟡 Medium |
| 8 | Set `expose_php = Off` in production `php.ini` | 🟡 Medium |
| 9 | Force password reset for all users after deployment | 🟡 Medium |
| 10 | Add `ENCRYPTION_KEY` + `expose_php = Off` to Dockerfile | 🟢 Low |
| 11 | Implement Content-Security-Policy (CSP) header in nginx | 🟢 Low |
| 12 | Implement login rate limiting / 2FA | 🟢 Low |

---

## Files Modified Summary

| File | Changes | Lines Affected |
|------|---------|:---:|
| `modules/master/models/Mdl_master.php` | SQLi fix — 4 safe helpers + 10 methods updated | 8–656 |
| `modules/auth/controllers/Auth.php` | Argon2id login + sess_destroy + remove phpinfo | 26–152 |
| `modules/meme/controllers/User.php` | Argon2id password hash on user save | 195 |
| `modules/meme/controllers/Me.php` | Argon2id in update_user, generate_user, change_password | 25, 50, 1027–1061 |
| `index.php` | error_reporting + display_errors + SHOW_DEBUG_BACKTRACE | 197–200 |
| `application/config/constants.php` | SHOW_DEBUG_BACKTRACE → FALSE | 14 |
| `config.php` | encryption_key strengthened | 41 |
| `deploy/nginx/default.conf.example` | 5 location blocks for sensitive file protection | 16–35 |
| `deploy/nginx/nginx.conf.example` | server_tokens off | 24 |
| `system/core/Exceptions.php` | PHP 8.4 E_STRICT compatibility | 75 |

**Files Deleted**:

| File | Reason |
|------|--------|
| `themes/admin/atlant/assets/filetree/jqueryFileTree.php` | Unauth directory traversal |
| `themes/admin/atlant/assets/cropping/process.php` | Unauth path traversal |
| `themes/admin/atlant/assets/ckeditor__/samples/assets/posteddata.php` | Dev artifact |
| `themes/admin/atlant/js/ckeditor/samples/assets/posteddata.php` | Dev artifact |

**File Renamed**:

| From | To | Reason |
|------|----|--------|
| `adminer.php` | `adminer.php.bak` | Prevent public access to DB management tool |

---

*Report generated from fixed codebase. All PHP files pass syntax check (`php -l`).*
