# Full Battlefield 2 stack example

This example deploys a stack with `bf2stats` v2. If you prefer `bf2stats` v3, see [here](https://github.com/startersclan/asp).

## Usage

In this example, we will use the domain name  `example.com`. In production, you should use your own domain name for `traefik` (our TLS-terminating load balancer) to be able to serve HTTPS for the web endpoints: `ASP`, `bf2sclone`, and `phpmyadmin`.

### 1. Setup hosts file

Add a couple of hostnames to the `hosts` file for the web endpoints:

```sh
# Since we are testing this stack locally, we need these DNS records in the hosts file
echo '127.0.0.1 asp.example.com' | sudo tee -a /etc/hosts
echo '127.0.0.1 bf2sclone.example.com' | sudo tee -a /etc/hosts
echo '127.0.0.1 phpmyadmin.example.com' | sudo tee -a /etc/hosts
```

### 2. Start the full stack

Run:

```sh
docker-compose up
```

If there is error `listen udp4 0.0.0.0:53: bind: address already in use` or something similar, the OS might already have a DNS server running on localhost UDP port `53`, e.g. `systemd-resolved` or docker DNS server. To get around that, change `coredns` in `docker-compose.yml` to bind to your external interface's IP. Change this

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

If there is a similar error for TCP port `80` or `443`, you might have an existing web server running. Stop the web server first. Run `docker-compose up` again.

### 3. Wait for the containers to start

You should see something like:

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
```

The full stack is now running:

- Battlefield 2 1.5 server with `bf2stats` `2.3.1` support available on your external IP address on UDP ports `16567` and `29900` on your external IP address
- Gamespy server [`PRMasterServer`](https://github.com/PRMasterServer) available at your external IP address on TCP ports `29900`, `29901`, `28910`, and UDP ports `27900` and `29910` on your external IP address
- `coredns` available on your external IP address on UDP port `53` on your external IP address
- `traefik` (TLS-terminated reverse web proxy) available on port `80` and `443` on your external IP address
- `ASP` available at https://asp.example.com on your external IP address.
- `bf2sclone` available at https://bf2sclone.example.com on your external IP address.
- `phpmyadmin` available at https://phpmyadmin.example.com on your external IP address.

> If you are behind NAT, you will need to forward all of the above TCP and UDP ports to your external IP address, in order for clients to reach your gameserver and webserver over the internet.

### 4. Setup the stats DB

Visit https://asp.example.com/ASP and login using `$admin_user` and `$admin_pass` defined in its [config file](./config/ASP/config.php).

> Since traefik hasn't got a valid TLS certificate via `ACME`, it will serve the `TRAEFIK DEFAULT CERT`. The browser will show a security issue when visiting https://asp.example.com, https://bf2sclone.example.com, and https://phpmyadmin.example.com. Simply click "visit site anyway" button to get past the security check.

Click on `System > Install Database` and install the DB using `$db_host`,`$db_port`,`$db_name`,`$db_user`,`$db_pass` you defined in [`config.php`](./config/ASP/config.php). Click `System > Test System` and `Run System Tests` and all tests should be green, except for the `BF2Statistics Processing` test and the four `.aspx` tests, because we still don't have a Fully Qualified Domain Name (FQDN) with a public DNS record.

### 5. Setup DNS server

Configure `coredns` to spoof all gamespy DNS in [config/coredns/hosts](config/coredns/hosts), replacing the IP addresses with your machine's external IP address which you specified in Step `2.`. Assuming your external IP is `192.168.1.100`, it should look like:

```txt
192.168.1.100 eapusher.dice.se
192.168.1.100 battlefield2.available.gamespy.com
192.168.1.100 battlefield2.master.gamespy.com
192.168.1.100 battlefield2.ms14.gamespy.com
192.168.1.100 gamestats.gamespy.com
192.168.1.100 master.gamespy.com
192.168.1.100 motd.gamespy.com
192.168.1.100 gpsp.gamespy.com
192.168.1.100 gpcm.gamespy.com
192.168.1.100 gamespy.com
192.168.1.100 bf2web.gamespy.com
```

Save the file. `coredns` immediately reads the changed file and serves the updated DNS records.

### 6. Use the DNS server in BF2 clients

Configure your BF2 client machine (and your friends' machines) to use the DNS server (i.e. your local machine) you configured in Step `2.`(E.g. `192.168.1.100`).

If you are on different networks, you may use any of [these methods](#background-keeping-battlefield-2-working). Option `3.` is recommended.

If you are all on the same LAN network, configuring DNS via DHCP is the easiest. Configure router's DHCP server's DNS setting to your machine's IP address, which will automatically configure all client's machines to use your machine as the DNS server (i.e. `coredns`). Then ensure `coredns` forwards all unmatched DNS (all non-gamespy DNS) back to your router so that your LAN DNS remains fully intact. To do so, in [`config/coredns/Corefile`](config/coredns/Corefile) change this line:

```conf
    forward . 192.168.0.1:53
```

to your router's IP address (e.g. `192.168.1.1`):

```conf
    forward . 192.168.1.1:53
```

Restart `coredns` and it will be ready:

```sh
docker-compose restart coredns
```

### 6. Play

BF2 1.5 clients should be able to connect to your gameserver. Start BF2, click `Create Account`, and once you've logged in, click `CONNECT TO IP`, and in the `IP ADDRESS` box enter the external IP address of the machine you used in Step `2.` (E.g. `192.168.1.100`). Join the server.

> You may see your server listed under `MULTIPLAYER > JOIN INTERNET`, when clicking `UPDATE SERVER LIST`, but with a wrong IP address, for instance,`172.16.x.x` instead of being your external IP address `192.168.1.100`. This happens because in our example `docker-compose.yml`, the `bf2` server and `prmasterserver` are linked using a `gamespy-network`, such that `bf2` container registered itself with `prmasterserver` container over `gamespy-network`, so `prmasterserver` listed the `bf2` server's container's IP address instead of the machine's external IP address. To solve this, run both the `bf2` server and `prmasterserver` on host networking, so that they talk to each other over the machine's external interface instead of a docker network.

At the end of the first game, you should see your stats updated at https://bf2sclone.example.com.

### Cheat sheet

- Visit https://asp.example.com/ASP to adminstrate your stats database and gamespy server. Login using `$admin_user` and `$admin_pass` defined in its [config file](./config/ASP/config.php).
- Visit https://bf2sclone.example.com to view your stats over the web. It's a nice pretty web interface. Your stats will be updated at the end of each gameserver round.
- Visit https://phpmyadmin.example.com if you want to self-manage your DB (if you know how). Login using user `root` and password `MARIADB_ROOT_PASSWORD` (or `MARIADB_USER` and `MARIADB_PASSWORD`) defined on the `db` service in [docker-compose.yml](./docker-compose.yml)
- This example includes all the configuration files for each stack component. Customize them to suit your needs.
- Mount the `ASP` [`config.php`](./config/ASP/config.php) with write permissions, or else `ASP` dashboard will throw an error. Use `System > Edit Configuration` as reference to customize the config file.
- In a production setup, you want to make sure:
  - to use a custom domain name
  - to configure `traefik` to be issued an ACME certificate for HTTPS to work for the web endpoints
  - to run `traefik` on `--network host` or to use the PROXY protocol, so that it preserves client IP addresses,
  - to run the `bf2` server and `prmasterserver` on `--network host` so that they: 1) talk to each other over the machine's external interface 2) preserve client IP addresses
  - to use stronger authentication in front of the `ASP` and `phpmyadmin`, which don't have in-built strong authentication
  - to use strong passwords for the `ASP` admin user in [config file](./config/ASP/config.php)
  - to use strong password for the `db` users in `MARIADB_ROOT_PASSWORD`, `MARIADB_USER`, and `MARIADB_PASSWORD`
  - to use internal networks for the `db` which don't need egress traffic

## Scripts

These one-liners may be handy for adminstration of the stack.

```sh
# Start
docker-compose up

# If you are running the BF2 server, PRMasterServer, or traefik on host networking, you may need these iptables rules
# BF2 server
iptables -A INPUT -p udp -m udp -m conntrack --ctstate NEW --dport 16567 -j ACCEPT
iptables -A INPUT -p udp -m udp -m conntrack --ctstate NEW --dport 29900 -j ACCEPT
# PRMasterServer
iptables -A INPUT -p tcp -m tcp -m conntrack --ctstate NEW --dport 29900 -j ACCEPT
iptables -A INPUT -p tcp -m tcp -m conntrack --ctstate NEW --dport 29901 -j ACCEPT
iptables -A INPUT -p tcp -m tcp -m conntrack --ctstate NEW --dport 28910 -j ACCEPT
iptables -A INPUT -p udp -m udp -m conntrack --ctstate NEW --dport 27900 -j ACCEPT
iptables -A INPUT -p udp -m udp -m conntrack --ctstate NEW --dport 29910 -j ACCEPT
# traefik
iptables -A INPUT -p udp -m udp -m conntrack --ctstate NEW --dport 80 -j ACCEPT
iptables -A INPUT -p udp -m udp -m conntrack --ctstate NEW --dport 443 -j ACCEPT

# Attach to the bf2 server console
docker attach bf2stats_bf2_1

# View bf2 server logs
docker exec -it bf2stats_bf2_1 bash
cd python/bf2/logs
cat *.log
exit

# Copy logs from bf2 server to this folder
docker cp bf2stats_bf2_1:/server/bf2/python/bf2/logs .

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

## Background: Keeping Battlefield 2 working

Problem: The Battlefield 2 client and server binaries are hardcoded with gamespy DNS records, e.g. `bf2web.gamespy.com`. Because gamespy has shut down, the DNS records no longer exist on public DNS servers. In order to keep the game's multiplayer working, we need:

- A gamespy replacement - solved by `PRMasterServer`
- DNS resolution for gamespy DNS records - solved by either by: `1.` hex patching the game binaries; `2.` spoofing DNS server responses; `3.` spoofing DNS records via `hosts` file

This example opted for `2.` which is DNS spoofing via `coredns`, because it can be done on a single machine and multiple machine setups.

### Option 1: Hex patching game binaries

This is what [`bf2hub.com`](https://bf2hub.com) and many BF2 mods do.

Pros:

- Simple. Patch every client with the new binaries

Cons:

- Difficult to change to another gamespy server
- Difficult to distribute because it requires installation on each client
- Binaries may be patched with malicious code

### Option 2: Spoof DNS at the DNS server

Pros:

- Most scalable. You configure the DNS server via `DHCP`, so that every client that connects to a `DHCP` server (e.g. router) are configured to use the DNS server. No client configuration needed
- Easy to change to another gamespy server

Cons:

- Dangerous. The DNS server may be used as an attack vector against clients to steal cookies and direct clients to malicious websites.

### Option 3: Use DNS records via `hosts` file

Pros:

- Safest. DNS records only apply to the local machine

Cons:

- Difficult to change to another gamespy server
- Tedious to hand edit. See an example of a hosts file [here](./config/coredns/hosts)
- Requires administrative privileges to update the machine's `hosts` file

For clients:

- The [`BF2statisticsClientLauncher.exe`](/Tools/Client%20Files) was made to do this
- The [`BF2GamespyRedirector`](https://github.com/BF2Statistics/BF2GamespyRedirector) improves on `BF2statisticsClientLauncher.exe` by allowing clients to save IP addresses of their favourite gamespy servers, and easily switch between them. Read more [here](https://bf2statistics.com/threads/bf2statistics-v3-1-0-full-release.3010/)

### Which is the best?

For servers:

- Servers environments should be stable, so `1.` or `2.` is preferred.

For clients:

- The best solution depends on one's setup. If one often needs to switch between gamespy servers, `3.` is best. If one doesn't want clients to have to install anything but wants things to "just work", use `2.`. If one prefers a single gamespy server run by a trustworthy community, use `1.`.
