#!/bin/sh
set -eu

echo "Step 1/2: Building image..."

if [ ! -f .variables ]; then
  echo "Missing deploy/.variables. Copy .variables.example and set its secrets." >&2
  exit 1
fi

set -a
. ./.variables
set +a

docker build -t "$IMAGE_NAME:$IMAGE_TAG" .
