# BOCP WordPress theme

The shared theme behind bocp.ro and the other Real Life S.R.L. sites. Classic PHP templates with
the block editor for content, a sticky header with a WordPress-menu-driven navigation and mobile
drawer, four footer widget areas, Customizer-driven colours, fonts, logo and company data, and
self-hosted IBM Plex fonts. No page builder required.

The theme folder is `bocp/`. `bocp-child/` is a minimal child theme to copy and rename per site
(for example `cloudsales-child`) for site-specific CSS, template overrides and hooks. The rest of
the repository is release tooling. Only `bocp/` is packaged and self-updates; child themes are
installed once per site and never touched by updates.

## Install on a site

1. Download [dist/bocp.zip](dist/bocp.zip).
2. wp-admin: Appearance, Themes, Add New Theme, Upload Theme, choose the zip, Install Now.
   If an older copy is already active, WordPress offers "Replace active with uploaded"; accept.
   Customizer settings, menus and widgets are kept, because they are stored per theme name.
3. Activate if it is not active yet, then set up Appearance, Customize and Appearance, Menus.

Site-specific customisation belongs in a child theme or in the Customizer, never in `bocp/`,
so that updates never overwrite it.

## Child themes per site

`dist/children/<site>-child.zip` holds the small per-site child themes (two files each: style.css
and functions.php) so the connector can install them remotely. They never self-update; edit them
here, rebuild the zip, and re-install with overwrite.

## Updates

The theme updates itself from this repository. `style.css` declares an `Update URI`, and
`inc/self-update.php` reads `dist/manifest.json` to learn about new versions. WordPress then
shows the update under Appearance, Themes and Dashboard, Updates, installs it automatically
within about 12 hours (the theme enrols itself in auto-updates on first run), and the AI WP
Site Manager connector can trigger it immediately with `check_updates` and `update_theme`.

## Releasing a new version

See the docstring in `build.py`: bump the version in `bocp/style.css` and `BOCP_VERSION` in
`bocp/functions.php`, add a changelog entry, run `python build.py`, commit, push.

## Notes for other sites

- `inc/mirror-sync.php` mirrors one cloudsales.ro page into bocp.ro hourly. It is specific to
  bocp.ro and must be disabled or reconfigured before the theme runs elsewhere.
- `inc/admin-setup.php` contains a BOCP setup assistant with bocp.ro-specific links.
