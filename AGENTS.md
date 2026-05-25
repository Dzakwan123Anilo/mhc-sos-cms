# AGENTS.md

## Overview

CodeIgniter 3.0.4 CMS for MHC SOS (International SOS) — a claims management system used by Indonesian hospitals to verify insured members and submit Inpatient / Outpatient / Dental / Emergency claims. Intended for **PHP 8.4**.

---

## Specification

- **PHP**: 8.4 with FPM (Alpine 3.21)
- **Web server**: nginx, port `1880`
- **DB**: MySQL / MariaDB via `mysqli` driver, configured through env vars
- **Process manager**: supervisord (nginx + php-fpm)
- **No composer**, no npm — all third-party libs vendored manually

---

## Libraries

| Library | Version | Path |
|---------|---------|------|
| CodeIgniter | 3.0.4 | `system/` |
| MX (HMVC) | 5.5 | `application/third_party/MX/` |
| PHPMailer | 5.x | `application/libraries/phpmailer5/` |
| PHPExcel | — | `application/libraries/PHPExcel/` |
| Adminer | 4.8.1 | `/adminer.php` |
| CKEditor | vendored | `assets/js/ckeditor/` |
| jQuery | 1.7.2 | `assets/js/` |

There is no package manager — add libraries by copying files.

---

## Architecture

### Directory Layout

```
index.php                    →  entry point (front controller), ENVIRONMENT=production
config.php                   →  root config: DB env vars, HMVC module paths, theme, base URL, encryption key
application/core/            →  MY_Controller, MY_Router (MX override), MY_Loader, globalModel
application/libraries/       →  AdminController, FrontController (base controller classes)
modules/                     →  HMVC modules (auth, meme, master, reference, setting, ajax, api, web)
themes/admin/atlant/         →  admin panel views
themes/web/{name}/           →  public website views (template-driven)
assets/                      →  static CSS/JS/images
assets/collections/          →  uploaded files (gitignored)
deploy/                      →  Dockerfile, nginx, fpm, supervisor configs
```

### Module Inventory

| Module | Purpose |
|--------|---------|
| `auth` | Login/logout |
| `meme` | Dashboard, profile, claim submission, Excel exports |
| `master` | CRUD for clients, members, providers, claims, fraud cases |
| `reference` | Lookup tables (plans, service types, dental limits, cities, etc.) |
| `setting` | Badges, ranks, general settings |
| `ajax` | AJAX endpoints (data retrieval, import) |
| `api` | REST API |
| `web` | Public-facing website controllers |

### Class Hierarchy

```
CI_Controller                       (system/core/Controller.php)
  └─ MY_Controller                  (application/core/MY_Controller.php)
       ├─ FrontController           (application/libraries/FrontController.php)
       │    └─ Auth                 (modules/auth/controllers/Auth.php) — login page
       │    └─ Web\*                (modules/web/controllers/) — public site
       └─ AdminController           (application/libraries/AdminController.php)
            └─ Me                   (modules/meme/controllers/Me.php)
            └─ Client, Member, ...  (modules/master/controllers/)
            └─ Benefit, Plan, ...   (modules/reference/controllers/)
            └─ *                    (all other admin module controllers)
```

**Auth** extends `FrontController` (not `AdminController`) because it has no login guard — it renders the login page and handles `act_auth`.

---

## How MVC Works

### Request Lifecycle (example: `GET /master/client`)

```
1. nginx → index.php → system/core/CodeIgniter.php
2. MX Router locates modules/master/controllers/Client.php
3. Constructor chain: CI_Controller → MY_Controller → AdminController → Client
4. AdminController.__construct() checks $this->jCfg['is_login'] (line 37)
   → if not logged in: redirect('auth')
   → if logged in: build ACL menu from app_acl_group + app_acl_accesses tables (line 52)
5. Client.__construct() (modules/master/controllers/Client.php line 3):
   → sets $this->DATA->table = "sos_client" (line 10)
   → loads model: $this->load->model("mdl_master","M") (line 41)
   → calls _set_action() for permissions
6. Client::index() is called:
   → builds search params into $this->jCfg['search']
   → calls $this->M->client($param) → Mdl_master::client() in modules/master/models/Mdl_master.php
   → model reads $this->jCfg['search'] for filters, sorts, limit/offset
   → model runs $this->db->get("sos_client") and returns data + total count
7. Controller renders view: $this->_v("master/index", $data)
   → AdminController::_v() loads themes/admin/atlant/{top|left}/header.php
   → loads themes/admin/atlant/master/index.php (the specific view)
   → loads themes/admin/atlant/{top|left}/footer.php
```

### URL-to-Controller Mapping

URL segment convention: `{module}/{controller}/{method}/{param}`

```
/meme/me              → modules/meme/controllers/Me.php :: index()
/meme/me/dashboard    → modules/meme/controllers/Me.php :: dashboard()
/master/client        → modules/master/controllers/Client.php :: index()
/master/client/add    → modules/master/controllers/Client.php :: add()
/master/client/edit/5 → modules/master/controllers/Client.php :: edit(5)
/auth                 → modules/auth/controllers/Auth.php :: index()
```

If a controller is not found in `modules/`, MX Router falls back to `application/controllers/`. The default route `$route['default_controller'] = 'auth'` (returns login page).

### Model Pattern

Module-level models live in `modules/{name}/models/Mdl_{name}.php`. Example:

```
modules/master/models/Mdl_master.php → class Mdl_master extends CI_Model
modules/meme/models/Mdl_meme.php     → class Mdl_meme extends CI_Model
modules/reference/models/Mdl_reference.php → class Mdl_reference extends CI_Model
modules/api/models/Mdl_api.php       → class Mdl_api extends CI_Model
```

Models are loaded in controller constructors as: `$this->load->model("mdl_name","M")`, then accessed via `$this->M->methodName()`.

Models do NOT directly read `$_POST` or `$_GET` — they read shared state from `$this->jCfg['search']` (set by the controller beforehand).

Model query methods follow a dual-mode signature: `$count = FALSE` for data, `$count = TRUE` for row count. They recursively call themselves with `$count=TRUE` to get the total:

```php
function client($p=array(), $count=FALSE) {
    // ... build query (select, join, where, order_by, limit) ...
    $qry = $this->db->get("sos_client");
    if ($count == FALSE) {
        $total = $this->client($p, TRUE);  // recursive for count
        return array("data" => $qry->result(), "start" => $start, "total" => $total);
    } else {
        return $qry->num_rows();
    }
}
```

### globalModel CRUD Helper

`application/core/globalModel.php` is a standalone class (not a CI model) instantiated as `$this->DATA` in `MY_Controller`. It provides generic CRUD with auto-tracking:

- `$this->DATA->table = "sos_client"` — set target table
- `$this->DATA->_add($data)` — auto-sets `user_add`, `time_add`, `user_update`, `time_update`, `app_id` if the columns exist
- `$this->DATA->_update($where, $data)` — auto-sets `user_update`, `time_update`
- `$this->DATA->_delete($where)` — soft-delete (`is_trash=1`) with `user_delete`, `time_delete`; pass `$remove=TRUE` for hard delete
- `$this->DATA->_cek($where)` — count rows by condition
- `$this->DATA->data_id($where)` — single row
- `$this->DATA->_getall($where)` — all matching rows

Used in `AdminController::_save_master()` (line 181 of `AdminController.php`).

### View Rendering

**Admin views** (from `AdminController::_v()`):
- Theme path: `themes/admin/{template_admin}/` (`themes/admin/atlant/` by default)
- Header: `themes/admin/atlant/{top|left}/header.php`
- Footer: `themes/admin/atlant/{top|left}/footer.php`
- View file convention: `master/index`, `master/form` etc.
- `$this->jCfg['theme_setting']['menu']` controls header layout: `"top"` or `"left"`

**Public views** (from `FrontController::_v()`):
- Theme path: `themes/web/{template_name}/`
- Template name comes from `app_site_template` joined through `app_site`
- `$single=true` (default) renders just the view; `$single=false` wraps with header/footer

### Key Helpers (autoloaded)

Auto-loaded helpers (`application/config/autoload.php` line 86): `url`, `file`, `security`, `text`, `core`, `sos`

| Helper | File | Path |
|--------|------|------|
| `cfg()` | `core_helper.php:79` | Reads `$CI->config->item()` wrapper |
| `getCI()` | `core_helper.php:2` | Returns singleton CI instance |
| `dbClean()` | `core_helper.php:175` | Input sanitization (xss_clean + escaping) |
| `_encrypt()` / `_decrypt()` | `core_helper.php:401/429` | String encryption using `encryption_key` |
| `_ac('action')` | `core_helper.php:352` | Checks if current user has ACL action permission |
| `get_name()` | `sos_helper.php` | Fetches a single column value from any table by condition |

### Database

- Driver: `mysqli`
- Config loaded from env vars in root `config.php` (lines 10-21): `DB_HOST`, `DB_PORT`, `DB_USERNAME`, `DB_PASSWORD`, `DB_NAME`, `DB_DRIVER`
- SQL dump: `sos_db.sql` (4128 lines)
- **Migrations are disabled** (`application/config/migration.php` line 10: `$config['migration_enabled'] = FALSE`)
- Table prefix: none

Key tables (production data prefixes):
- `app_user`, `app_user_group`, `app_acl_group`, `app_acl_accesses`, `app_acl_actions`, `app_acl_group_accesses`, `app_acl_access_actions` → authentication/authorization
- `app_config` → key-value settings
- `app_log` → user activity logs (class, method, URL, IP, POST/GET data)
- `app_site`, `app_site_template` → multisite/template
- `sos_client`, `sos_member`, `sos_provider` → business entities
- `sos_ttrans_claim` → claim transactions
- `sos_ref_*` → reference/lookup tables
- `sos_tref_*` → association tables
- `app_propinsi`, `app_kabupaten`, `app_city` → Indonesian geography

---

## Flow (User Perspective)

1. User opens the web application → lands on `/auth` (login page)
2. On success, `Auth::act_auth()` looks up `app_user` by username + MD5 password → checks `app_user_group` for roles → checks `app_acl_group_accesses` for permissions → loads provider group info → stores all in `$this->session` → redirects to `meme/me/welcome`
3. Dashboard (`meme/me`): shows member verification form — user enters card number → searches `sos_member` + `sos_client` + `sos_ref_plan_type` → displays member profile and claim history
4. Claim submission (`meme/me/save_claim`): enters claim data → fraud detection (>2 same-service claims/day = flagged) → sends email notification for inpatient / fraud
5. Admin modules: CRUD for clients, members, providers, reference data, users, groups, ACL

---

## Conventions

### Configuration

- **Two config layers** for base URL: `application/config/config.php` sets `localhost:3004` (line 26); root `config.php` overrides to `https://mhc.internationalsos.co.id/` (line 40). The root value wins because `application/config/config.php` includes it at the end.
- All app-specific settings live in root `config.php:30-129` — add new keys there and read them with `cfg('key_name')`.
- The `$config['subclass_prefix']` is `MY_` (the CI default). Do not change it.

### Controllers MUST extend AdminController or FrontController

Never extend `CI_Controller` or `MY_Controller` directly for module controllers. Use `AdminController` for admin pages (it handles login check and ACL), `FrontController` for public pages (it handles site info), and only `Auth` is the exception that extends `FrontController` with an admin theme override.

### Shared State via Session, Not Request Data

Search/filter state flows through `$this->jCfg['search']` which **persists in session**. Controller methods write to `$this->jCfg['search']` array and call `$this->_releaseSession()`. Model methods read from `$this->jCfg['search']`. Do NOT pass search params through URL query strings directly (except for sort/per_page/status via redirects).

The session backend is **file-based** (`sessions/` directory, gitignored). The `$config['sess_save_path']` is set to `getcwd() . '/sessions'` in the root config.

### Model Access Pattern

Modules typically load ONE model: `$this->load->model("mdl_{module}","M")`. The `$this->M` property is defined at the top of `AdminController` (line 3: `var $M;`). Additional models are loaded with unique aliases.

### Naming Conventions

- Module model: `Mdl_{name}.php` in `modules/{name}/models/`
- Module controller: `{Name}.php` in `modules/{name}/controllers/`
- Class name = filename (PascalCase for controllers, `Mdl_` prefix for models)
- View folder inside theme uses the module name: `themes/admin/atlant/master/`, `themes/admin/atlant/reference/`

### ACID and Logging

- **ACTIVITY LOGGING IS ENABLED** (`activeLog=true` in root config). Every request writes to `app_log` table (class, method, URL, user, IP, POST/GET data). Methods in the exclusion list (line 479-482 of `MY_Controller.php`) are skipped — update this list when adding high-volume AJAX endpoints.
- Chat feature is disabled (`activeChat=false`).

### Security Quirks

- Passwords are **MD5** hashed — do not change the hash algorithm without updating the auth flow, user management forms, and the `generate_user` scripts.
- CSRF protection is **disabled**. Enabling it will break the existing AJAX and form submissions unless all views are updated with CSRF tokens.
- `adminer.php` is publicly accessible at the root. No built-in web auth. Consider moving it or protecting it at nginx level in production.

### Ad-Hoc Helpers

Many utility functions in `core_helper.php` and `sos_helper.php` reference hardcoded values from the old "Honda Community" name (e.g., email subjects with "Hondacommunity"). These are cosmetic but worth auditing.

### Docker

```
deploy/start.sh        →  build + run (passes DB env vars via --env, uses --net=host)
deploy/build.sh        →  reads .variables, runs docker build
deploy/run.sh          →  reads .variables, runs docker run; mounts cwd as /home/app
```

The `install.sh` runs `composer install` but there is no `composer.json` — this step will fail and is harmless. The container works without it.
