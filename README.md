# BF2Stats

BF2Stats 4.0.1 provides the statistics backend and server scripts used with [OpenBF2](https://github.com/kr4uzi/OpenBF2). The database schema remains 4.0.0. Run the game server in official ranked mode; statistics use the native GameSpy connection.

See [release notes](RELEASE_NOTES.md) for fixes, upgrade requirements and compatibility limits.

## Deployment

Use Apache 2.4 with `mod_rewrite`, PHP with PDO MySQL, and MySQL/MariaDB with InnoDB. CI targets PHP 8.2–8.5. Enable `.htaccess` processing (`AllowOverride All`) for the ASP directory. Deploy it at `/ASP`; its assets use that path. Other web servers must implement equivalent routing and deny access to `system/`, direct `aspx/` scripts and hidden files.

For a new installation:

1. Copy `Web Files/ASP` to the web root. Copy `system/config/config.example.php` to `system/config/config.php`, then configure the database credentials, a strong `admin_pass`, and the `admin_hosts` IP/CIDR allowlist. Loopback may access the login page, but credentials are always required. Empty and default `admin` passwords are rejected.
2. Create an empty database and load `system/sql/schema.sql` and then `system/sql/data.sql`, or use the authenticated installation panel. The database user needs the permissions required for installation/maintenance; never use database root credentials in a public deployment.
3. Create writable `system/logs`, `system/cache`, `system/backups` and `system/snapshots/{processed,unprocessed,failed,unauthorized}` directories. Allow configuration writes if using the panel to save settings. These directories must remain inaccessible over HTTP.
4. Configure the provider and authorized game-server addresses in the admin panel. The shipped sample provider is disabled; generate a new token before authorizing it.
5. Overlay `Server Files/python` onto the existing BF2 server Python directory. Keep the vanilla files. Populate `python/bf2/stats/providerID.sec` with the provider's auth ID and token separated by a space.

Use HTTPS for the admin panel. With a reverse proxy, configure trusted address rewriting in Apache; request headers alone never establish a trusted client IP. Restrict access to the admin panel with its allowlist and deployment access controls.

Set `display_errors=Off` and `log_errors=On` in PHP's server configuration, including PHP-FPM deployments. The supplied `.htaccess` also disables displayed startup errors under mod_php so they cannot corrupt GameSpy responses.

Keep existing configuration, provider credentials and runtime data during upgrades. The v2 migration is a command-line maintenance tool; back up and test it on copies of your databases first.

## Checks

The test image contains PHP, Apache and MariaDB. Tests run against a disposable database and a copy of the application, with the repository mounted read-only and external networking disabled:

```sh
docker build -t bf2stats-test -f tests/Containerfile tests
docker run --rm --network=none -v "$PWD:/workspace:ro" bf2stats-test bash tests/container-run.sh
python3 -m unittest discover -s tests -p 'test_*.py'
```

`podman` can replace `docker`. Use `--build-arg PHP_VERSION=8.4` to test another PHP version. For syntax checks alone, run `python3 scripts/lint.py` with PHP on PATH. Templates use custom substitutions and are checked through rendered HTTP responses rather than raw `php -l`.

## Release preparation

The **Backend checks** GitHub workflow runs on pushes and pull requests. It tests PHP 8.2, 8.3, 8.4 and 8.5 against Apache/MariaDB and checks the release package.

Run **Prepare release** manually to produce an archive and SHA-256 checksums as a workflow artifact. After updating `CODE_VERSION`, its date and `RELEASE_NOTES.md`, push a matching tag (for example `v4.0.1`) to run the checks, build artifacts and create a **draft** GitHub release. Review the draft before publishing it. A mismatched tag fails packaging.

To prepare the same archive locally:

```sh
python3 scripts/package_release.py
cd dist
sha256sum -c SHA256SUMS
```

The archive contains the ASP backend, the server Python overlay, migration script and documentation. It retains `.htaccess` and creates empty runtime directories. It excludes logs, caches, snapshots, backups, development scripts and bf2sclone, and substitutes installation defaults for deployment configuration and provider credentials. The existing batch installers are not included; deploy the overlay as described above.
