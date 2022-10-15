# bf2stats

[![github-actions](https://github.com/startersclan/bf2stats/workflows/ci-master-pr/badge.svg)](https://github.com/startersclan/bf2stats/actions)
[![github-release](https://img.shields.io/github/v/release/startersclan/bf2stats?style=flat-square)](https://github.com/startersclan/bf2stats/releases/)
[![docker-image-size](https://img.shields.io/docker/image-size/startersclan/bf2stats/asp-nginx)](https://hub.docker.com/r/startersclan/bf2stats)

BF2Statistics [`2.x.x`](https://code.google.com/archive/p/bf2stats/) with docker support.

Although BF2Statistics [`3.1.0`](https://github.com/BF2Statistics/ASP) has been released, it is not backward compatible with `<= 2.x.x`. Hence, this project is to help those who want to retain their `2.x.x` stats system, and to ease deployment of the stack since support is scarce. It runs on PHP 7.4 with nginx.

## Usage

```sh
docker pull startersclan/bf2stats:2.3.1-asp-nginx
docker pull startersclan/bf2stats:2.3.1-asp-php
docker pull startersclan/bf2stats:2.3.1-bf2sclone-nginx
docker pull startersclan/bf2stats:2.3.1-bf2sclone-php
```

See [this](docs/full-bf2-stack-example) example showing how to deploy [Battlefield 2 1.5 server](https://github.com/startersclan/docker-bf2/), the [gamespy emulator](https://github.com/startersclan/PRMasterServer), and `bf2stats` using `docker-compose`.

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
docker build -t startersclan/bf2stats:asp-nginx -f Dockerfile.asp-nginx.prod .
docker build -t startersclan/bf2stats:asp-php -f Dockerfile.asp-php.prod .
docker build -t startersclan/bf2stats:bf2sclone-nginx -f Dockerfile.bf2sclone-nginx.prod .
docker build -t startersclan/bf2stats:bf2sclone-php -f Dockerfile.bf2sclone-php.prod .

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

A: This is caused by a bug where the UI fails to handle an invalid response from the backend. A `PHP_ERROR` `Warning: file_put_contents(/src/ASP/system/config/config.php): failed to open stream: Permission denied in /src/ASP/system/core/Config.php on line 165` is output before the JSON response causing invalid JSON. You can see the error in the `/src/ASP/system/logs/php_errors.log`.

Grant ASP `php`'s `www-data` user write permission for `config.php`.

```sh
chmod 666 ./config/ASP/config.php
docker-compose restart asp-php
```

### Q: `Warning: file_put_contents(/src/ASP/system/config/config.php): failed to open stream: Permission denied in /src/ASP/system/core/Config.php on line 165` appearing in ASP dashboard

A: Grant ASP `php`'s `www-data` user write permission for `config.php`.

```sh
chmod 666 ./config/ASP/config.php
docker-compose restart asp-php
```

### Q: `There was an error testing the system. Please refresh the page and try again.` when using `System > Test System` in ASP

A: This is means the UI received an invalid JSON response from the backend. If you know how to, you can examine the payload of the `POST` response. You may also check for errors in the `/src/ASP/system/logs/php_errors.log`.

### Q: `BF2Statistics Processing Check: Fail` or ` Gamespy (.aspx) Basic Response: Fail` or `Gamespy (.aspx) Advanced (1) Response: Fail` when using `System > Test System` in ASP

A: DNS resolution problem. The `HOST` used in the test to test those Gamespy endpoints is the same host you see in your browser. For instance, if you are accessing the `ASP` using `http://localhost`, the `ASP` `php` container runs tests against `http://localhost/ASP/*.aspx`, which will fail, because the request is not going through `ASP` `nginx`.

If you see this in a development environment, simply ignore the errors. There is an integration test using [docker-compose.test.yml](docker-compose.test.yml) to test those endpoints to ensure they work.

If you are seeing this in a production environment, use a fully qualified domain name (FQDN) so that `php` can resolve to its external DNS name to test against its external web endpoint.

### Q: `Importing Logs Failed!` when using `Server Admin > Import Logs` in ASP

A: DNS resolution problem. The `HOST` used in the test to test those Gamespy endpoints is the same host you see in your browser. For instance, if you are accessing the `ASP` using `http://localhost`, the `ASP` `php` container runs tests against `http://localhost/ASP/*.aspx`, which will fail, because the request is not going through `ASP` `nginx`.

If you see this in a development environment, simply ignore the errors. There is an integration test using [docker-compose.test.yml](docker-compose.test.yml) to test those endpoints to ensure they work.

If you are seeing this in a production environment, use a fully qualified domain name (FQDN) so that `php` can resolve to its external DNS name to test against its external web endpoint.

### Q: `Table (army) *NOT* Backed Up: [1045] Access denied for user 'admin'@'%' (using password: YES)` when using `System > Backup Database` in ASP

A: The `db` user does not have the `FILE` privilege. Add a grant manually. But note that even if you did, you still won't be able to backup without major security issues. See [here](#q-table-army-not-backed-up-1-cant-createwrite-to-file-when-using-system--backup-database-in-asp).

### Q: `Table (army) *NOT* Backed Up: [1] Can't create/write to file` when using `System > Backup Database` in ASP

The `backupdb` module uses [`SELECT * INTO OUTFILE`](https://mariadb.com/kb/en/select-into-outfile/), but the `src` files are not in the db container, `mariadb` cannot find the path to export the files. In the past, the `apache`, `php` and `mysql` ran on the same machine with write access to the same filesystem, but with `docker`, each container has its own filesystem. The only workaround is to mount the `backups-volume` inside the `db` container at the same path as it is mounted in the `ASP` `php` container `/src/ASP/system/database/backups/`, with write permissions for `php`'s user `82` and `mariadb`'s user `999` which menas the directory needs `777` permissions (world writeable), which is very bad from the point of view of security.

It is better to backup the DB on a `cron` schedule using `mysqldump` from another container linked to the `db` container:

```sh
# Dump a DB at host `db`, user `root`, database `bf2stats`
mysqldump -hdb -uroot -p<password> <database>
```

### Q: `Xdebug: [Step Debug] Could not connect to debugging client. Tried: host.docker.internal:9000 (through xdebug.client_host/xdebug.client_port)` appears in the php logs

A: The debugger is not running. Press `F5` in `vscode` to start the `php` `xdebug` debugger. If you stopped the debugger, it is safe to ignore this message.
