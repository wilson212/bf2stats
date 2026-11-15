# BF2Stats 4.0.1

Backend security and compatibility fixes for OpenBF2. Database schema remains 4.0.0.

- Ignore spoofable client-IP headers; require admin credentials on loopback too.
- Reject empty/default admin passwords, rotate login sessions, protect admin POST requests with CSRF tokens, and block GET dispatch to POST actions and the development controller.
- Preserve numeric passwords and safely quote configuration arrays when saving settings.
- Serialize unlock selections per player, enforce prerequisites, and share the badge/rank budget with the unlock-list endpoint. Multiple badge levels count once.
- Send the missing `createplayer.aspx` success response using the existing GameSpy framing.
- Reject malformed/oversized snapshots, avoid database writes during unauthenticated parsing, and keep tokens and exception traces out of responses.
- Protect private files before applying the public asset allowlist, preserve transaction exceptions, and restrict the v2 migration script to CLI.
- Use `player.isAIPlayer()` to skip AI rank requests, allowing human accounts with profile IDs below or equal to 2000 to load their ranks.
- Load saved ranks on unranked Internet servers by using `getUseGlobalUnlocks()` in the rank script. Rank loading now follows global unlock availability instead of `sv.useGlobalRank`. See [deployment instructions](README.md#rank-display-on-unranked-servers); this does not enable statistics submission or progression on unranked servers.

## Upgrade

Back up the database and configuration. Preserve `ASP/system/config/config.php`, the runtime directories, and your server's provider credentials when updating an existing deployment. The package contains a blank admin password and database password: these are installation examples, not replacements for live configuration.

Set a strong admin password before using the panel. The old `admin` password and localhost automatic login no longer work. Browser forms and AJAX requests carry CSRF tokens automatically; custom admin clients must fetch a session token and submit it in `csrf_token` or `X-CSRF-Token`.

Behind a reverse proxy, configure trusted-proxy handling in the web server so `REMOTE_ADDR` contains the verified peer address. The application deliberately ignores `Client-IP` and forwarding headers. Configure HTTPS at that layer too so secure session cookies are used. Admin and provider IP allowlists otherwise see the proxy's address.

Deploy the Python files as an overlay on the complete BF2 server Python directory, retaining all vanilla files. Set `providerID.sec` to the provider ID and token configured in the admin panel. The archive contains a placeholder. The old batch installers, original Python backup, and unused bf2sclone frontend are excluded.

## Compatibility and limits

The `pid`/`id` query parameters, HTTP POST used by OpenBF2 unlock selection, and GameSpy response framing are preserved. The client sends no proof of player identity with unlock selection; enforcing ownership requires a coordinated client/GameSpy/backend protocol change. These fixes enforce the budget and prerequisites, but do not authenticate the supplied player ID.

CI runs PHP 8.2–8.5 with Apache and MariaDB, including concurrent selection requests. Actual BF2 gameplay, Windows batch installation, and the historical v2 data migration still require separate staging validation. This change adds a CLI guard to the migration; it does not certify its data transformations. bf2sclone is outside this review.
