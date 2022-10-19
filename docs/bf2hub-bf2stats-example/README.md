## BF2hub with bf2stats example

This example deploys a stack with a BF2 1.5 server, using BF2Hub as the master server, and `bf2stats` v2 as the private ranking stats web server. If you prefer `bf2stats` v3, see [here](https://github.com/startersclan/asp).

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

### 3. Wait for the containers to start

You should see something like:

```sh
$ docker-compose up
[+] Running 10/0
 ⠿ Container bf2stats-phpmyadmin-1       Running                                                                                                                                            0.0s
 ⠿ Container bf2stats-bf2-1              Running                                                                                                                                            0.0s
 ⠿ Container bf2stats-traefik-1          Running                                                                                                                                            0.0s
 ⠿ Container bf2stats-init-container-1   Created                                                                                                                                            0.0s
 ⠿ Container bf2stats-db-1               Running                                                                                                                                            0.0s
 ⠿ Container bf2stats-asp-php-1          Running                                                                                                                                            0.0s
 ⠿ Container bf2stats-bf2sclone-php-1    Running                                                                                                                                            0.0s
 ⠿ Container bf2stats-asp-nginx-1        Running                                                                                                                                            0.0s
 ⠿ Container bf2stats-bf2sclone-nginx-1  Running                                                                                                                                            0.0s
Attaching to bf2stats-asp-nginx-1, bf2stats-asp-php-1, bf2stats-bf2-1, bf2stats-bf2sclone-nginx-1, bf2stats-bf2sclone-php-1, bf2stats-coredns-1, bf2stats-db-1, bf2stats-init-container-1, bf2stats-phpmyadmin-1, bf2stats-traefik-1
```

The stack is now running:

- Battlefield 2 1.5 server with `bf2stats` `2.3.2` support available on your external IP address on UDP ports `16567` and `29900` on your external IP address
- Gamespy server is BF2Hub. See https://www.bf2hub.com/servers/unranked to see your server listed (unranked because we are not an official BF2Hub "EA trusted partner")
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

### 5. Play

Install BF2 1.5, download and install the [BF2Hub client](https://www.bf2hub.com/home/downloads.php), checking all the boxes during the installation. Laucnh the `BF2Hub Client`, and `Register Account` to sign up if you don't yet have an account. Now, to ensure our private ranking system is used, click each of the two `Basic Settings > Ranking to be used for your ingame BFHQ ...` buttons and select `Custom` and enter `asp.example.com` (or your FQDN) in the box. Then click `PLAY BATTLEFIELD2` to start the game. 

Login to your account, and click `MULTIPLAYER > JOIN INTERNET` and in the server list, you should see your server listed by the BF2Hub master server. Join your server. 

At the end of the first game, you should see your stats updated at https://bf2sclone.example.com.

### Cheat sheet

- Visit https://asp.example.com/ASP to adminstrate your stats database and gamespy server. Login using `$admin_user` and `$admin_pass` defined in its [config file](./config/ASP/config.php).
- Visit https://bf2sclone.example.com to view your stats over the web. It's a nice pretty web interface. Your stats will be updated at the end of each gameserver round.
- Visit https://phpmyadmin.example.com if you want to self-manage your DB (if you know how). Login using user `root` and password `MARIADB_ROOT_PASSWORD` (or `MARIADB_USER` and `MARIADB_PASSWORD`) defined on the `db` service in [docker-compose.yml](./docker-compose.yml)
- This example includes all the configuration files for each stack component. Customize them to suit your needs.
- Mount the `ASP` [`config.php`](./config/ASP/config.php) with write permissions, or else `ASP` dashboard will throw an error. Use `System > Edit Configuration` as reference to customize the config file.
- In a production setup, you want to make sure:
  - to use a custom domain name (FQDN)
  - to configure `traefik` to be issued an ACME certificate for HTTPS to work for the web endpoints
  - to use stronger authentication in front of the `ASP` and `phpmyadmin`, which don't have in-built strong authentication
  - to use strong passwords for the `ASP` admin user in [config file](./config/ASP/config.php)
  - to use strong password for the `db` users in `MARIADB_ROOT_PASSWORD`, `MARIADB_USER`, and `MARIADB_PASSWORD`
  - to use internal networks for the `db` which doesn't need egress traffic

## Scripts

These one-liners may be handy for adminstration of the stack.

```sh
# Start
docker-compose up

# Only if you are running the BF2 server, or traefik on host networking, you may need these iptables rules
# BF2 server
iptables -A INPUT -p udp -m udp -m conntrack --ctstate NEW --dport 16567 -j ACCEPT
iptables -A INPUT -p udp -m udp -m conntrack --ctstate NEW --dport 29900 -j ACCEPT
# traefik
iptables -A INPUT -p udp -m udp -m conntrack --ctstate NEW --dport 80 -j ACCEPT
iptables -A INPUT -p udp -m udp -m conntrack --ctstate NEW --dport 443 -j ACCEPT

# Attach to the bf2 server console
docker attach bf2stats_bf2_1

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
