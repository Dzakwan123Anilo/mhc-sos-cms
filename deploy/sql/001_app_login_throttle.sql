-- Throttle percobaan login (per-IP & per-username, key = sha256).
-- Tabel sudah ada di DB berjalan; file ini untuk deployment baru.
CREATE TABLE IF NOT EXISTS app_login_throttle (
  scope_type varchar(16) CHARACTER SET ascii NOT NULL,
  scope_key char(64) CHARACTER SET ascii NOT NULL,
  failure_count int unsigned NOT NULL DEFAULT '0',
  window_started_at datetime NOT NULL,
  last_attempt_at datetime NOT NULL,
  blocked_until datetime DEFAULT NULL,
  PRIMARY KEY (scope_type, scope_key),
  KEY idx_login_throttle_blocked (blocked_until),
  KEY idx_login_throttle_last_attempt (last_attempt_at)
) ENGINE=InnoDB DEFAULT CHARSET=ascii;
