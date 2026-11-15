#!/usr/bin/env python3
"""Exercise real Apache rewrites, sessions and GameSpy endpoints in the test image."""
import concurrent.futures
import http.cookiejar
import json
from pathlib import Path
import re
import time
import urllib.error
import urllib.parse
import urllib.request

BASE = 'http://127.0.0.1'
checks = 0


def request(path, data=None, headers=None, opener=None):
    if isinstance(data, dict):
        data = urllib.parse.urlencode(data).encode()
    req = urllib.request.Request(BASE + path, data=data, headers=headers or {})
    try:
        with (opener or urllib.request.build_opener()).open(req, timeout=20) as res:
            return res.status, res.read().decode(), res.headers
    except urllib.error.HTTPError as err:
        return err.code, err.read().decode(), err.headers


def check(condition, message):
    global checks
    if not condition:
        raise AssertionError(message)
    checks += 1


def framed(body):
    data, trailer = body.rsplit('\n', 1)
    size = len(data.replace('\t', '').replace('\n', '').encode())
    return trailer == f'$\t{size}\t$'


for _ in range(60):
    try:
        request('/ASP/')
        break
    except urllib.error.URLError:
        time.sleep(0.25)
else:
    raise RuntimeError('Apache did not start')

jar = http.cookiejar.CookieJar()
browser = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(jar))
status, body, headers = request('/ASP/', opener=browser)
check(status == 200 and 'name="password"' in body, 'Localhost bypassed admin login: ' + body[:300])
token = re.search(r'name="csrf_token" value="([a-f0-9]+)"', body).group(1)
check('HttpOnly' in headers.get('Set-Cookie', '') and 'SameSite=Strict' in headers.get('Set-Cookie', ''),
      'Session cookie protection missing')
check(request('/ASP/', {'action': 'login', 'username': 'admin', 'password': 'test-admin-password'},
              opener=browser)[0] == 403, 'Login CSRF accepted')
status, body, _ = request('/ASP/', {'action': 'login', 'username': 'admin',
    'password': 'test-admin-password', 'csrf_token': token}, opener=browser)
check(status == 200 and 'name="csrf-token"' in body, 'Admin login failed: ' + body[:300])
token = re.search(r'name="csrf-token" content="([a-f0-9]+)"', body).group(1)
check(request('/ASP/players/postDelete', opener=browser)[0] == 405, 'GET invoked POST-only action')
check(request('/ASP/players/delete', {'id': '1001'}, opener=browser)[0] == 403, 'Admin mutation accepted without CSRF')
check(request('/ASP/devtest/phpInfo', opener=browser)[0] == 404, 'Development controller publicly routed')
check('name="csrf-token"' in request('/ASP/?action=logout', opener=browser)[1], 'GET logged out admin')
status, body, _ = request('/ASP/', {'action': 'logout', 'csrf_token': token}, opener=browser)
check(status == 200 and 'name="password"' in body, 'POST logout failed')
token = re.search(r'name="csrf_token" value="([a-f0-9]+)"', body).group(1)
check(request('/ASP/', {'action': 'login', 'username': 'admin',
      'password': 'test-admin-password', 'csrf_token': token}, opener=browser)[0] == 200,
      'Login after logout failed')

Path('/var/www/html/ASP/system/logs/private.css').write_text('private-data')
for path in ['/ASP/system/logs/private.css', '/ASP/system/config/config.php.bak',
             '/ASP/.gitignore', '/ASP/aspx/selectunlock.php', '/migrate_v2_to_v4.php']:
    check(request(path)[0] == 403, 'Private/admin script exposed: ' + path)
check(request('/ASP/frontend/js/csrf.js')[0] == 200, 'Public admin assets blocked')

for path in ['/ASP/createplayer.aspx?pid=1001', '/ASP/getunlocksinfo.aspx?pid=1001',
             '/ASP/getrankinfo.aspx?pid=1001', '/ASP/getplayerinfo.aspx?pid=1001&info=per*,cmb*',
             '/ASP/searchforplayers.aspx?nick=test-player&where=x']:
    status, body, _ = request(path)
    check(status == 200 and body.startswith('O\n') and framed(body), 'Invalid GameSpy response: ' + path + '\n' + body[:300])
for path in ['/ASP/selectunlock.aspx?pid[]=1001&id=11', '/ASP/getplayerinfo.aspx?info[]=pid&pid=1001',
             '/ASP/selectunlock.aspx?pid=-1&id=11', '/ASP/selectunlock.aspx?pid=1001&id=65535']:
    status, body, _ = request(path, b'')
    check(status == 200 and body.startswith('E') and framed(body), 'Malformed API input accepted: ' + path)

# Two simultaneous choices must consume at most the player's single point.
with concurrent.futures.ThreadPoolExecutor(max_workers=2) as pool:
    results = list(pool.map(lambda unlock: request(f'/ASP/selectunlock.aspx?pid=1003&id={unlock}', b'')[1], [11, 22]))
check(sum(body.startswith('O\n') for body in results) == 1
      and sum('No Unlocks Available!' in body for body in results) == 1,
      'Concurrent selections overspent budget: ' + repr(results))

snapshot_headers = {'User-Agent': 'GameSpyHTTP/1.0', 'Content-Type': 'application/json'}
for raw in [b'null', b'1', b'true', b'[]', b'"x"', b'{bad', b'{"players":[1]}']:
    _, body, _ = request('/ASP/bf2statistics.php', raw, snapshot_headers)
    check(body.startswith('E\nH\tresponse\nD\tSNAPSHOT Data'), 'Malformed snapshot crashed: ' + body[:300])
check(request('/ASP/bf2statistics.php', b'{}', {'User-Agent': ''})[0] == 403, 'Missing user agent accepted')
oversized = request('/ASP/bf2statistics.php', b' ' * (8 * 1024 * 1024 + 1), snapshot_headers)
check(oversized[0] == 413, 'Oversized snapshot response: ' + repr(oversized[:2]))
check('Stack trace' not in request('/ASP/bf2statistics.php', json.dumps({'mapId': 99}).encode(), snapshot_headers)[1],
      'Snapshot leaked exception trace')
print(f'Apache/HTTP regression checks passed: {checks}')
