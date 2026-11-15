#!/usr/bin/env python3
"""Build the backend distribution, excluding live data and deployment secrets."""
import argparse
import gzip
import hashlib
import io
from pathlib import Path
import re
import tarfile

ROOT = Path(__file__).resolve().parents[1]
PRIVATE_DIRS = {'logs', 'cache', 'backups', 'snapshots'}
RUNTIME_DIRS = [f'Web Files/ASP/system/{name}' for name in ('logs', 'cache', 'backups')]
RUNTIME_DIRS += [f'Web Files/ASP/system/snapshots/{name}' for name in
                 ('processed', 'unprocessed', 'failed', 'unauthorized')]


def release_files(root):
    for directory in ['Web Files/ASP', 'Server Files/python']:
        for path in sorted((root / directory).rglob('*')):
            relative = path.relative_to(root)
            if path.is_symlink():
                raise ValueError(f'Release inputs must not be symlinks: {relative}')
            if not path.is_file() or any(part in PRIVATE_DIRS for part in relative.parts):
                continue
            if any(part.startswith('.') and part != '.htaccess' for part in relative.parts):
                continue
            if path.name in ('config.php', 'providerID.sec') or path.suffix in ('.bak', '.log', '.cache', '.sec', '.pyc'):
                continue
            # Do not ship the web-callable development/debug controller.
            if 'devtest' in relative.parts:
                continue
            yield relative.as_posix(), path.read_bytes()
    for name in ['README.md', 'migrate_v2_to_v4.php', 'RELEASE_NOTES.md']:
        yield name, (root / name).read_bytes()
    yield 'Web Files/ASP/system/config/config.php', (root / 'Web Files/ASP/system/config/config.example.php').read_bytes()
    yield 'Server Files/python/bf2/stats/providerID.sec', b'0 CHANGE_ME\n'


def build(root, output, ref=''):
    source = (root / 'Web Files/ASP/index.php').read_text()
    version = re.search(r"define\('CODE_VERSION', '([0-9]+\.[0-9]+\.[0-9]+)'\)", source).group(1)
    if ref.startswith('refs/tags/') and ref != f'refs/tags/v{version}':
        raise ValueError(f'Release tag must match CODE_VERSION: v{version}')
    output.mkdir(parents=True, exist_ok=True)
    prefix = f'bf2stats-{version}'
    archive = output / f'{prefix}.tar.gz'
    # Stable timestamps, modes, order and gzip headers make repeated builds match.
    with archive.open('wb') as raw, gzip.GzipFile(filename='', fileobj=raw, mode='wb', mtime=0) as compressed:
        with tarfile.open(fileobj=compressed, mode='w') as tar:
            for name in sorted(RUNTIME_DIRS):
                info = tarfile.TarInfo(f'{prefix}/{name}')
                info.type = tarfile.DIRTYPE
                info.mode = 0o755
                tar.addfile(info)
            for name, data in sorted(release_files(root)):
                info = tarfile.TarInfo(f'{prefix}/{name}')
                info.mode = 0o644
                info.size = len(data)
                tar.addfile(info, io.BytesIO(data))
    checksum = hashlib.sha256(archive.read_bytes()).hexdigest()
    (output / 'SHA256SUMS').write_text(f'{checksum}  {archive.name}\n')
    (output / 'RELEASE_NOTES.md').write_bytes((root / 'RELEASE_NOTES.md').read_bytes())
    return archive


if __name__ == '__main__':
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument('--ref', default='')
    parser.add_argument('--output', type=Path, default=ROOT / 'dist')
    args = parser.parse_args()
    print(build(ROOT, args.output, args.ref))
