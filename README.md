# bf2stats

[![github-actions](https://github.com/leojonathanoh/bf2stats/workflows/ci-master-pr/badge.svg)](https://github.com/leojonathanoh/bf2stats/actions)
[![github-release](https://img.shields.io/github/v/release/leojonathanoh/bf2stats?style=flat-square)](https://github.com/leojonathanoh/bf2stats/releases/)
[![docker-image-size](https://img.shields.io/docker/image-size/leojonathanoh/bf2stats/asp-nginx)](https://hub.docker.com/r/leojonathanoh/bf2stats)

[`bf2statistics` `2.2.0`](https://code.google.com/archive/p/bf2stats/) with docker support.

Although [`bf2statistics` `3.1.0`](https://github.com/BF2Statistics/ASP) has been released, it is not backward compatible with `<= 2.2.0`. Hence, this project is to help those who want to retain their `2.2.0` stats system, and to ease deployment of the stack since support is scarce.

## Development

```sh
# Start
docker-compose up --build
# ASP available at http://localhost:8081. Username: admin, password admin. See ./config/ASP/config.php config file
# bf2sclone available at http://localhost:8082.
# phpmyadmin available at http://localhost:8083. Username: admin, password: admin. See ./config/ASP/config.php config file

# If xdebug is not working, iptables INPUT chain may be set to DROP on the docker bridge.
# Execute this to allow php to reach the host machine via the docker0 bridge
sudo iptables -A INPUT -i br+ -j ACCEPT

# Test routes
docker-compose -f docker-compose.test.yml up

# Test production builds locally
docker build -t leojonathanoh/bf2stats:asp-nginx -f Dockerfile.asp-nginx.prod .
docker build -t leojonathanoh/bf2stats:asp-php -f Dockerfile.asp-php.prod .
docker build -t leojonathanoh/bf2stats:bf2sclone-nginx -f Dockerfile.bf2sclone-nginx.prod .
docker build -t leojonathanoh/bf2stats:bf2sclone-php -f Dockerfile.bf2sclone-php.prod .
docker-compose -f docker-compose.example.yml up

# Dump the DB
docker exec $( docker-compose ps | grep db | awk '{print $1}' ) mysqldump -uroot -padmin bf2stats | gzip > bf2stats.sql.gz

# Restore the DB
zcat bf2stats.sql.gz | docker exec -i $( docker-compose ps | grep db | awk '{print $1}' ) mysql -uroot -padmin bf2stats

# Stop
docker-compose down

# Cleanup
docker-compose down
docker volume rm bf2stats_backups-volume
docker volume rm bf2stats_logs-volume
docker volume rm bf2stats_snapshots-volume
docker volume rm bf2stats_bf2sclone-cache-volume
docker volume rm bf2stats_db-volume
```

## FAQ

### Q: ASP installer never completes the first time

A: Grant ASP `php`'s `www-data` user write permission for `config.php`.

```sh
chmod 666 ./config/ASP/config.php
docker-compose restart asp-php
```

When hitting the `Install` button, a `POST` is made to `http://localhost:8081/ASP/index.php?task=installdb`, and an error `Warning: file_put_contents(/src/ASP/system/config/config.php): failed to open stream: Permission denied in /src/ASP/system/core/Config.php on line 165` is output before the JSON. This results in invalid JSON which is not properly handled by the UI and hence it appears to never complete.

### Q: `Warning: file_put_contents(/src/ASP/system/config/config.php): failed to open stream: Permission denied in /src/ASP/system/core/Config.php on line 165` appearing in ASP dashboard

A: Grant ASP `php`'s `www-data` user write permission for `config.php`.

```sh
chmod 666 ./config/ASP/config.php
docker-compose restart asp-php
```

### Q: `Xdebug: [Step Debug] Could not connect to debugging client. Tried: host.docker.internal:9000 (through xdebug.client_host/xdebug.client_port)` appears in the php logs

A: The debugger is not running. Press `F5` in `vscode` to start the `php` `xdebug` debugger. If you stopped the debugger, it is safe to ignore this message.
