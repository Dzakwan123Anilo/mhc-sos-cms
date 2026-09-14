#!/bin/sh
set -eu

echo "Step 2/2: Running container..."

if [ ! -f .variables ]; then
  echo "Missing deploy/.variables. Copy .variables.example and set its secrets." >&2
  exit 1
fi

set -a
. ./.variables
set +a

if [ -z "${IMAGE_NAME:-}" ] || [ -z "${IMAGE_TAG:-}" ] || [ -z "${CONTAINER_NAME:-}" ]; then
  echo "IMAGE_NAME, IMAGE_TAG, and CONTAINER_NAME are required." >&2
  exit 1
fi

if [ -z "${APP_BASE_URL:-}" ] || [ -z "${DB_HOST:-}" ] || [ -z "${DB_PORT:-}" ] \
  || [ -z "${DB_USERNAME:-}" ] || [ -z "${DB_PASSWORD:-}" ] || [ -z "${DB_NAME:-}" ] \
  || [ -z "${DB_DRIVER:-}" ]; then
  echo "APP_BASE_URL and all DB_* variables are required." >&2
  exit 1
fi

if [ -z "${ENCRYPTION_KEY:-}" ] || [ "${#ENCRYPTION_KEY}" -lt 32 ]; then
  echo "ENCRYPTION_KEY must contain at least 32 characters." >&2
  exit 1
fi

case "${CMS_SOURCE_PATH:-}" in
  /*) ;;
  *) echo "CMS_SOURCE_PATH must be an absolute path." >&2; exit 1 ;;
esac

if [ ! -f "$CMS_SOURCE_PATH/index.php" ]; then
  echo "CMS_SOURCE_PATH does not point to the CMS project." >&2
  exit 1
fi

if docker container inspect "$CONTAINER_NAME" >/dev/null 2>&1; then
  docker rm -f "$CONTAINER_NAME"
fi

docker run -d --restart=always \
  -p "${APP_PORT:-1880}:1880" \
  --add-host=host.docker.internal:host-gateway \
  -v /etc/timezone:/etc/timezone:ro \
  -v /etc/localtime:/etc/localtime:ro \
  -v "$CMS_SOURCE_PATH:/home/app" \
  --mount "type=volume,source=${CONTAINER_NAME}_sessions,target=/home/app/sessions" \
  -e APP_BASE_URL \
  -e TRUSTED_PROXY_IPS \
  -e DB_HOST \
  -e DB_PORT \
  -e DB_USERNAME \
  -e DB_PASSWORD \
  -e DB_NAME \
  -e DB_DRIVER \
  -e ENCRYPTION_KEY \
  -e SMTP_HOST \
  -e SMTP_PORT \
  -e SMTP_USERNAME \
  -e SMTP_PASSWORD \
  -e SMTP_SECURE \
  -e SMTP_FROM \
  -e SMTP_FROM_NAME \
  -e AUTH_RATE_LIMIT_ATTEMPTS \
  -e AUTH_RATE_LIMIT_WINDOW \
  -e AUTH_RATE_LIMIT_LOCKOUT \
  -e AUTH_CAPTCHA_TTL \
  --name "$CONTAINER_NAME" \
  "$IMAGE_NAME:$IMAGE_TAG"
