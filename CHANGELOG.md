# Changelog

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
