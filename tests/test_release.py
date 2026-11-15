import importlib.util
from pathlib import Path
import tarfile
import tempfile
import unittest

ROOT = Path(__file__).resolve().parents[1]
spec = importlib.util.spec_from_file_location('package_release', ROOT / 'scripts/package_release.py')
package = importlib.util.module_from_spec(spec)
spec.loader.exec_module(package)


class ReleaseTests(unittest.TestCase):
    def test_archive_contains_only_backend_and_clean_configuration(self):
        with tempfile.TemporaryDirectory() as folder:
            output = Path(folder)
            archive = package.build(ROOT, output)
            first = archive.read_bytes()
            self.assertEqual(first, package.build(ROOT, output).read_bytes())
            with tarfile.open(archive) as tar:
                prefix = archive.name.removesuffix('.tar.gz') + '/'
                names = {name.removeprefix(prefix) for name in tar.getnames()}
                self.assertIn('Web Files/ASP/.htaccess', names)
                self.assertIn('Web Files/ASP/system/framework/Unlocks.php', names)
                self.assertIn('Web Files/ASP/system/snapshots/unprocessed', names)
                self.assertFalse(any('bf2sclone' in name or 'devtest' in name for name in names))
                self.assertFalse(any(name.endswith(('.log', '.bak', '.cache', '.pyc')) for name in names))
                self.assertFalse(any('/._' in name or '/.git' in name for name in names))
                self.assertEqual(tar.extractfile(prefix + 'Server Files/python/bf2/stats/providerID.sec').read(), b'0 CHANGE_ME\n')
                config = tar.extractfile(prefix + 'Web Files/ASP/system/config/config.php').read()
                self.assertEqual(config, (ROOT / 'Web Files/ASP/system/config/config.example.php').read_bytes())

    def test_mismatched_tag_is_rejected(self):
        with tempfile.TemporaryDirectory() as folder:
            with self.assertRaises(ValueError):
                package.build(ROOT, Path(folder), 'refs/tags/v0.0.0')


if __name__ == '__main__':
    unittest.main()
