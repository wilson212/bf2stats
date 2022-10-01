# Full Battlefield 2 stack example

This example deploys a stack with `bf2stats` v2. If you prefer `bf2stats` v3, see [here](https://github.com/startersclan/asp).

## Usage

1. First, start the full stack:

```sh
docker-compose up
```

2. If there is error `listen udp4 0.0.0.0:53: bind: address already in use` or something similar, the OS might already have a DNS server running on localhost UDP port `53`, e.g. `systemd-resolved` or docker DNS server.

To get around that, change `coredns` in `docker-compose.yml` to bind to your external interface's IP. Change this

```yaml
    ports:
    - 53:53/udp
```

to

```yaml
    ports:
    - 192.168.1.100:53:53/udp
```

assuming `192.168.1.100` is your machine's external IP address.

3. If there is a similar error for TCP port `80` or `443`, you should may have an existing web server running. Stop the web server first. Run `docker-compose up` again.

4. Wait for the containers to start. You should see something like:

```sh
$ docker-compose up
[+] Running 10/0
 ⠿ Container bf2stats-prmasterserver-1   Running                                                                                                                                            0.0s
 ⠿ Container bf2stats-phpmyadmin-1       Running                                                                                                                                            0.0s
 ⠿ Container bf2stats-bf2-1              Running                                                                                                                                            0.0s
 ⠿ Container bf2stats-traefik-1          Running                                                                                                                                            0.0s
 ⠿ Container bf2stats-init-container-1   Created                                                                                                                                            0.0s
 ⠿ Container bf2stats-db-1               Running                                                                                                                                            0.0s
 ⠿ Container bf2stats-asp-php-1          Running                                                                                                                                            0.0s
 ⠿ Container bf2stats-bf2sclone-php-1    Running                                                                                                                                            0.0s
 ⠿ Container bf2stats-asp-nginx-1        Running                                                                                                                                            0.0s
 ⠿ Container bf2stats-bf2sclone-nginx-1  Running                                                                                                                                            0.0s
Attaching to bf2stats-asp-nginx-1, bf2stats-asp-php-1, bf2stats-bf2-1, bf2stats-bf2sclone-nginx-1, bf2stats-bf2sclone-php-1, bf2stats-coredns-1, bf2stats-db-1, bf2stats-init-container-1, bf2stats-phpmyadmin-1, bf2stats-prmasterserver-1, bf2stats-traefik-1
...
```

5. The full stack is now ready:
- Battlefield 2 1.5 server with `bf2stats` `2.2.0` support available on your external IP address on UDP ports `16567` and `29900` on your external IP address
- Gamespy server [`PRMasterServer`](https://github.com/PRMasterServer) available at your external IP address on TCP ports `29900`, `29901`, `28910`, and UDP ports `27900` and `29910` on your external IP address
- `coredns` available on your external IP address on UDP port `53` on your external IP address
- `traefik` (reverse web proxy) available on port `80` and `443` on your external IP address
- `ASP` available at https://asp.example.com on your external IP address. Login using `$admin_user` and `$admin_pass` defined in its [config file](./config/ASP/config.php)
- `bf2sclone` available at https://bf2sclone.example.com on your external IP address
- `phpmyadmin` available at https://phpmyadmin.example.com on your external IP address. Login using `MARIADB_USER` and `MARIADB_PASSWORD` defined on the `db` service in [docker-compose.yml](./docker-compose.yml). You may also login using user `root` and password `MARIADB_ROOT_PASSWORD`.

Notes:
- Mount the `ASP` [`config.php`](./config/ASP/config.php) with write permissions, or else `ASP` dashboard will throw an error. Use `System > Edit Configuration` as reference to customize the config file.
- If traefik hasn't got a certificate via `ACME`, it will serve the `TRAEFIK DEFAULT CERT`. The browser will show a security issue when visiting https://asp.example.com, https://bf2sclone.example.com, and https://phpmyadmin.example.com. Simply click "visit site anyway" button to get past the security check.
- Setup the DB on the first time you login to the `ASP`, using `$db_host`,`$db_port`,`$db_name`,`$db_user`,`$db_pass` you defined in [`config.php`](./config/ASP/config.php).

6. If you are behind NAT, you will need to forward all of the above TCP and UDP ports to your external IP address, in order for clients to reach your server over the internet.

## Spoofing gamespy DNS for BF2 clients

Problem: The Battlefield 2 client and server binaries are hardcoded with gamespy DNS records, e.g. `bf2web.gamespy.com`. Because gamespy has shut down, the DNS records no longer exist on public DNS servers. In order to keep the game's multiplayer working, we need:
1. A gamespy replacement - solved by `PRMasterServer`
2. DNS resolution for gamespy DNS records - solved by either hex patching the game binaries, spoofing DNS server responses, or spoofing DNS records via `HOSTS` file.

### Option 1: Hex patching game binaries

This is what [`bf2hub.com`](https://bf2hub.com) and many BF2 mods do.

Pros:
- Simple. Patch every client with the new binaries

Cons:
- Difficult to change to another gamespy server
- Difficult to distribute because it requires installation on each client
- Trust issues. Binaries may be patched with malicious code

### Option 2: Spoof DNS at the DNS server

Pros:
- Most scalable. You configure the DNS server via `DHCP`, so that every client that connects to a `DHCP` server (e.g. router) are configured to use the DNS server. No client configuration needed
- Easy to change to another gamespy server

Cons:
- Dangerous. The DNS server may be used as an attack vector against clients to steal cookies and direct clients to malicious websites.

### Option 3: Use DNS records in the local machine

Pros:
- Safest. DNS records only apply to the local machine

Cons:
- Difficult to change to another gamespy server
- Tedious to hand edit. See an example of a hosts file [here](./config/coredns/hosts)
- Requires administrative privileges to update the machine's `hosts` file

Solutions:
- The [`BF2statisticsClientLauncher.exe`](/Tools/Client%20Files) was made to do this
- The [`BF2GamespyRedirector`](https://github.com/BF2Statistics/BF2GamespyRedirector) improves on `BF2statisticsClientLauncher.exe` by allowing users to save IP addresses of their favourite gamespy servers, and easily switch between them. Read more [here](https://bf2statistics.com/threads/bf2statistics-v3-1-0-full-release.3010/)

### Which is the best?

The best solution depends on one's setup. If one often needs to switch between gamespy servers, `3.` is best. If one doesn't want clients to have to install anything but wants things to "just work", use `2.`. If one prefers a single gamespy server run by a trustworthy community, use `1.`.

This example opted for `2.` which is DNS spoofing using `coredns`. It can be used on a single machine and multiple machine setups.

## Scripts

These one-liners may be handy for adminstration of the stack.

```sh
# Start
docker-compose up

# Edit config/coredns/hosts and replace gamespy's DNS records with your machine's external IP address. Save it to immediately apply it
vi config/coredns/hosts

# If you are testing this stack locally, you may need these DNS records in your hosts file
echo '127.0.0.1 asp.example.com' | sudo tee -a /etc/hosts
echo '127.0.0.1 bf2sclone.example.com' | sudo tee -a /etc/hosts
echo '127.0.0.1 phpmyadmin.example.com' | sudo tee -a /etc/hosts

# Dump the DB
docker exec $( docker-compose ps | grep db | awk '{print $1}' ) mysqldump -uroot -padmin bf2stats | gzip > bf2stats.sql.gz

# Restore the DB
zcat bf2stats.sql.gz | docker exec -i $( docker-compose ps | grep db | awk '{print $1}' ) mysql -uroot -padmin bf2stats

# Stop
docker-compose down

# Cleanup. Warning: This destroys the all data!
docker-compose down
docker volume rm bf2stats_prmasterserver-volume
docker volume rm bf2stats_traefik-acme-volume
docker volume rm bf2stats_backups-volume
docker volume rm bf2stats_logs-volume
docker volume rm bf2stats_snapshots-volume
docker volume rm bf2stats_bf2sclone-cache-volume
docker volume rm bf2stats_db-volume
```
