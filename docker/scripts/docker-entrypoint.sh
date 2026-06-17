#!/usr/bin/env bash
set -euo pipefail

cd /app

mkdir -p var/cache var/log

if [ ! -f vendor/autoload.php ]; then
    composer install --prefer-dist --no-interaction
fi

exec "$@"
