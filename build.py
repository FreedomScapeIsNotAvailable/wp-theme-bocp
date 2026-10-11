#!/usr/bin/env python3
"""
Build dist/bocp.zip and dist/manifest.json from the bocp/ theme folder.

Release procedure:
  1. Edit the theme. Bump `Version:` in bocp/style.css AND BOCP_VERSION in bocp/functions.php.
  2. Add a line under the new version in CHANGELOG.md.
  3. python build.py
  4. git add -A && git commit -m "Release x.y.z" && git push
Sites running the theme install the new version automatically within about 12 hours,
or immediately through the connector's check_updates + update_theme.
"""
import json
import re
import zipfile
from pathlib import Path

ROOT = Path(__file__).resolve().parent
THEME = ROOT / "bocp"
DIST = ROOT / "dist"
RAW_BASE = "https://raw.githubusercontent.com/FreedomScapeIsNotAvailable/wp-theme-bocp/main/dist/"
SKIP_DIRS = {".git", "__pycache__", "node_modules", ".idea"}
SKIP_FILES = {".DS_Store", "Thumbs.db"}

style = (THEME / "style.css").read_text(encoding="utf-8")
functions = (THEME / "functions.php").read_text(encoding="utf-8")


def header(name: str, default: str = "") -> str:
    m = re.search(rf"^\s*{re.escape(name)}:\s*(.+?)\s*$", style, re.M)
    return m.group(1) if m else default


version = header("Version")
const = re.search(r"define\(\s*'BOCP_VERSION',\s*'([^']+)'", functions).group(1)
if version != const:
    raise SystemExit(f"Version mismatch: style.css says {version}, BOCP_VERSION says {const}")

changelog = ""
cl = ROOT / "CHANGELOG.md"
if cl.exists():
    m = re.search(rf"^## {re.escape(version)}.*?$(.*?)(?=^## |\Z)", cl.read_text(encoding="utf-8"), re.M | re.S)
    if m:
        items = [ln.strip()[2:] for ln in m.group(1).splitlines() if ln.strip().startswith("- ")]
        changelog = f"<h4>{version}</h4><ul>" + "".join(f"<li>{i}</li>" for i in items) + "</ul>"

DIST.mkdir(exist_ok=True)
zip_path = DIST / "bocp.zip"
count = 0
with zipfile.ZipFile(zip_path, "w", zipfile.ZIP_DEFLATED) as z:
    for path in sorted(THEME.rglob("*")):
        if path.is_dir() or path.name in SKIP_FILES or any(part in SKIP_DIRS for part in path.relative_to(THEME).parts):
            continue
        z.write(path, "bocp/" + path.relative_to(THEME).as_posix())
        count += 1

# Versioned copy: the manifest points at it, so GitHub's CDN can never serve a stale zip for a new version.
versioned = DIST / f"bocp-{version}.zip"
versioned.write_bytes(zip_path.read_bytes())
for old_zip in sorted(DIST.glob("bocp-*.zip"))[:-3]:
    old_zip.unlink()

manifest = {
    "name": "BOCP",
    "slug": "bocp",
    "version": version,
    "package": RAW_BASE + versioned.name,
    "requires": header("Requires at least", "6.0"),
    "requires_php": header("Requires PHP", "7.4"),
    "tested": header("Tested up to", ""),
    "changelog": changelog,
}
(DIST / "manifest.json").write_text(json.dumps(manifest, indent=2) + "\n", encoding="utf-8")
print(f"built {zip_path.name} ({count} files, {zip_path.stat().st_size // 1024} KB) and manifest.json for version {version}")
