#!/usr/bin/env python3
"""Lint PHP sources without executing deployment code.

The .tpl files contain custom {variable} substitutions inside PHP expressions
and must be rendered by View before PHP can parse them. HTTP tests cover those.
"""
from pathlib import Path
import subprocess

root = Path(__file__).resolve().parents[1]
files = sorted(p for p in root.rglob('*.php')
               if not any(part.startswith('.') for part in p.relative_to(root).parts)
               and 'bf2sclone' not in p.parts)
failures = []
for path in files:
    result = subprocess.run(['php', '-l', str(path)], capture_output=True, text=True)
    if result.returncode:
        failures.append(result.stdout + result.stderr)
if failures:
    raise SystemExit('\n'.join(failures))
print(f'PHP lint passed: {len(files)} files')
