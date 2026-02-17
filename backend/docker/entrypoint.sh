#!/usr/bin/env sh
set -e
mkdir -p /etc/ssl
if [ -n "$MYSQL_ATTR_SSL_CA_CONTENT" ]; then
  echo "$MYSQL_ATTR_SSL_CA_CONTENT" > /etc/ssl/aiven-ca.pem
  export MYSQL_ATTR_SSL_CA=/etc/ssl/aiven-ca.pem
fi
php artisan optimize || true
php artisan storage:link || true
if [ -n "$MIGRATIONS_PATH" ]; then
  php artisan migrate --force --path="$MIGRATIONS_PATH"
fi
exec /usr/bin/supervisord -n -c /etc/supervisor/conf.d/supervisord.conf
