#!/usr/bin/env bash
set -euo pipefail

cat > /etc/popcorn-server.env <<EOF
APP_ENV=${APP_ENV:-dev}
APP_SECRET=${APP_SECRET:-local-docker-popcorn-server-secret}
DATABASE_URL=${DATABASE_URL:-}
ELASTICSEARCH_URL=${ELASTICSEARCH_URL:-}
ENQUEUE_DSN=${ENQUEUE_DSN:-}
PROM_METRICS_DSN=${PROM_METRICS_DSN:-}
TOR_PROXY=${TOR_PROXY:-}
TMDB_API_KEY=${TMDB_API_KEY:-}
TRAKT_KEY=${TRAKT_KEY:-}
SENTRY_DSN=${SENTRY_DSN:-}
EOF

cat > /usr/local/bin/popcorn-server-cron-command <<'EOF'
#!/usr/bin/env bash
set -euo pipefail
set -a
source /etc/popcorn-server.env
set +a
cd /app
exec "$@"
EOF
chmod +x /usr/local/bin/popcorn-server-cron-command

exec cron -f
