#!/bin/sh
set -e

APP_DIR="/home/app"

# Session directory (CodeIgniter writes session files to {docroot}/sessions)
mkdir -p "${APP_DIR}/sessions"
chmod 777 "${APP_DIR}/sessions"

# Upload target used by the CMS
mkdir -p "${APP_DIR}/assets/collections"
chmod 777 "${APP_DIR}/assets/collections"

exec "$@"
