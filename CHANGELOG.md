# Changelog

## 1.2.13
- Posts page (Settings › Reading › Posts page): new `home.php` prints the posts page title as the single H1 (with its excerpt as lead) above the post cards; before, the fallback `index.php` had no heading.

## 1.2.12
- Google tag: LiteSpeed Cache keeps a separate page copy per CookieAdmin consent cookie, so a page cached for a visitor who accepted is never served to one who did not.

## 1.2.11
- Google tag: with the CookieAdmin plugin active, the tag is printed already blocked (analytics category) until the visitor accepts analytics cookies, so it never loads before consent, even on a site where no cookie scan has been run.

## 1.2.10
- Customizer: the Google tag section is named just "Google tag".

## 1.2.9
- Customizer: optional Google tag ID (BOCP, Google tag). When set, the theme prints the gtag.js snippet in the head for visitors who are not logged in, with the same ids as Site Kit's snippet so CookieAdmin blocks it until analytics consent. Leave it empty on sites where Site Kit already prints the tag.

## 1.2.8
- Menus: the "CSS Classes" field of a menu item now reaches the link in the header and the mobile drawer. New class `nav-pill` shows the item as a primary-colour pill button (e.g. "Demo" to the contact page), unaffected by the menu-link colour option.

## 1.2.7
- Default page template: pages whose content is a placeholder for generated sections ({INCLUDE:...} or {bocp_...}), such as the price page, use the full site width instead of the 820px reading column.

## 1.2.6
- Security: the release manifest carries a sha256 of the theme zip, and the self-updater refuses a package that does not match it. Release builds are cleaner: build.py zips only tracked theme files.

## 1.2.5
- Menus: sub-submenus (level 3) open again. The first-level dropdown had overflow-y:auto, which clipped the side flyouts; dropdowns that contain flyouts no longer scroll or clip, and only the flyout under the pointer stays open (keyboard focus no longer keeps a second one open). Chevrons of level 2+ items keep pointing right when the top item is hovered, and a small hover bridge covers the gap before a flyout.
- Long menu titles wrap instead of stretching a dropdown across the page (max width 360px).
- Default page template: pages built from block layouts, tables, raw HTML, shortcodes or Elementor use the full site width; plain text pages keep the 820px reading column (filter bocp_page_wide). A static front page prints its content only. Child themes no longer need their own page.php for this.
- Customizer: optional colour for the header menu links (BOCP, Culori brand), so a site no longer needs CSS in a child theme for it.
- Statistic numbers scale down on narrow screens instead of being cut off.

## 1.2.4
- Elementor buttons without custom colours use the theme primary button style.

## 1.2.3
- Removed the mirror-sync module (bocp.ro copying a cloudsales.ro page hourly); both sites now include the live price script directly. Its leftover cron event is unscheduled once on update.

## 1.2.2
- Generic block content styling (buttons, quotes, tables, columns, media, separators) in the theme palette, for sites whose pages use standard Gutenberg blocks or Elementor rather than the BOCP section classes.
- Header and drawer buttons keep white text even when a page builder sets a global link colour.

## 1.2.1
- Release manifest cached one hour instead of six, and refreshed on demand by the connector (ai_wp_before_update_check).

## 1.2.0
- Second menu location "Meniu secundar (bară sub header)": renders a bar under the header, and in the mobile drawer, only when a menu is assigned.
- Dropdowns of any depth: level 2+ open as side flyouts on desktop (flip left at the viewport edge) and as nested accordions in the drawer; "#" parent items open their submenu instead of jumping.
- Parent items of the current page are highlighted; the drawer menu list scrolls when long.
- Breadcrumbs on plain page and post templates come from Yoast SEO when it is active, with a Home > Title fallback.
- Mirror sync only runs on bocp.ro (filter bocp_mirror_enabled to opt in elsewhere); no cron event is created on other sites.

## 1.1.0
- Self-update from the public GitHub repository (Update URI + manifest), auto-update enrolment, LiteSpeed purge after update.
- Mirror sync: rewritten contact links point to /contact/ instead of the retired /new-contact/.

## 1.0.0
- Initial theme as deployed on bocp.ro.
