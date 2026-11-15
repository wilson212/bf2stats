#!/usr/bin/env bash
set -euo pipefail

# Only run inside the disposable test image. The repository is mounted read-only.
test -x /usr/local/bin/docker-php-entrypoint
cat > /usr/local/etc/php/conf.d/test-database.ini <<'INI'
pdo_mysql.default_socket=/run/mysqld/mysqld.sock
mysqli.default_socket=/run/mysqld/mysqld.sock
INI
cp -a '/workspace/Web Files/ASP' /var/www/html/ASP
cp /workspace/migrate_v2_to_v4.php /var/www/html/migrate_v2_to_v4.php
mkdir -p /run/mysqld
chown mysql:mysql /run/mysqld
mariadbd --user=mysql --skip-networking --socket=/run/mysqld/mysqld.sock > /tmp/mariadb.log 2>&1 &
db_pid=$!
trap 'kill "$db_pid" 2>/dev/null || true' EXIT
for attempt in {1..60}; do
    if mariadb-admin ping --silent >/dev/null 2>&1; then break; fi
    sleep 1
done
mariadb-admin ping --silent
mariadb -e "CREATE DATABASE bf2stats_test; CREATE USER 'bf2stats_test'@'localhost' IDENTIFIED BY 'test-only'; GRANT ALL ON bf2stats_test.* TO 'bf2stats_test'@'localhost';"
mariadb bf2stats_test < '/workspace/Web Files/ASP/system/sql/schema.sql'
mariadb bf2stats_test < '/workspace/Web Files/ASP/system/sql/data.sql'
cat >> /var/www/html/ASP/system/config/config.php <<'PHP'

$db_host = 'localhost';
$db_user = 'bf2stats_test';
$db_pass = 'test-only';
$db_name = 'bf2stats_test';
$admin_pass = 'test-admin-password';
$game_unlocks = 0;
$stats_aspx_cache_time = 0;
PHP
mkdir -p /var/www/html/ASP/system/{logs,cache,backups,snapshots/{unauthorized,failed,unprocessed,processed}}
chown -R www-data:www-data /var/www/html
export BF2STATS_TEST_ASP=/var/www/html/ASP
python3 /workspace/scripts/lint.py
php /workspace/tests/regression.php
chown -R www-data:www-data /var/www/html
apache2-foreground > /tmp/apache.log 2>&1 &
apache_pid=$!
trap 'kill "$apache_pid" "$db_pid" 2>/dev/null || true' EXIT
python3 /workspace/tests/http_regression.py
