<?php
/**
 * Menu walkers + header CTA buttons.
 *
 * WP core's default nav walker doesn't add the exact classes the design
 * needs on <a> tags (only on <li>), so these two small walkers render
 * "nav-link"/"drawer-link" + "is-active" exactly where the CSS expects
 * them. Items with children ("menu-item-has-children", added by WP core in
 * wp-includes/nav-menu-template.php) get wrapped so a dropdown can be
 * rendered for their children:
 *
 *  - desktop, level 1: .nav-item.has-dropdown > .nav-dropdown (opens below, on hover/focus)
 *  - desktop, level 2+: .nav-sub.has-flyout > .nav-dropdown.nav-flyout (opens to the side;
 *    site.js flips it to the left when it would leave the viewport)
 *  - mobile drawer, any level: .drawer-item.has-submenu > .drawer-submenu (accordion,
 *    tap the caret; nested accordions indent one step per level, see site.css)
 *
 * Any depth is supported, which large product menus (e.g. cloudsales.ro) need.
 * The same walkers render both the "primary" header menu and the optional
 * "secondary" bar under it (see template-parts/nav.php).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Shared: is this menu item the current page or an ancestor of it? */
function bocp_menu_item_is_active( $item ) {
	$classes = (array) $item->classes;
	foreach ( array( 'current-menu-item', 'current_page_item', 'current-menu-ancestor', 'current-menu-parent', 'current_page_ancestor' ) as $c ) {
		if ( in_array( $c, $classes, true ) ) {
			return true;
		}
	}
	return false;
}

function bocp_menu_item_has_children( $item ) {
	return in_array( 'menu-item-has-children', (array) $item->classes, true );
}

/** Items whose link is "#" or empty only exist to open their submenu. */
function bocp_menu_item_is_placeholder( $item ) {
	$url = trim( (string) $item->url );
	return '' === $url || '#' === $url;
}

class BOCP_Nav_Walker extends Walker_Nav_Menu {
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$has_children = bocp_menu_item_has_children( $item );

		if ( $has_children ) {
			$output .= 0 === $depth ? '<div class="nav-item has-dropdown">' : '<div class="nav-sub has-flyout">';
		}

		$classes = array( 0 === $depth ? 'nav-link' : 'nav-dropdown-link' );
		if ( $has_children && $depth > 0 ) {
			$classes[] = 'has-children';
		}
		if ( bocp_menu_item_is_active( $item ) ) {
			$classes[] = 'is-active';
		}

		$href = bocp_menu_item_is_placeholder( $item ) ? '#' : esc_url( $item->url );
		$output .= '<a class="' . esc_attr( implode( ' ', $classes ) ) . '" href="' . $href . '"' . ( $has_children ? ' aria-haspopup="true"' : '' ) . '>' . esc_html( $item->title );
		if ( $has_children ) {
			$output .= bocp_icon( 'chevron-down', array( 'class' => 'nav-link-caret' ) );
		}
		$output .= '</a>';
	}

	public function end_el( &$output, $item, $depth = 0, $args = null ) {
		if ( bocp_menu_item_has_children( $item ) ) {
			$output .= '</div>';
		}
	}

	public function start_lvl( &$output, $depth = 0, $args = null ) {
		// $depth here is the depth of the *parent* item: 0 = first dropdown, 1+ = flyout.
		$output .= 0 === $depth ? '<div class="nav-dropdown">' : '<div class="nav-dropdown nav-flyout">';
	}

	public function end_lvl( &$output, $depth = 0, $args = null ) {
		$output .= '</div>';
	}
}

class BOCP_Drawer_Walker extends Walker_Nav_Menu {
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$has_children = bocp_menu_item_has_children( $item );

		if ( $has_children ) {
			$output .= '<div class="drawer-item has-submenu depth-' . (int) $depth . '">';
			$output .= '<div class="drawer-item-row">';
		}

		$classes = array( 0 === $depth ? 'drawer-link' : 'drawer-sublink' );
		if ( bocp_menu_item_is_active( $item ) ) {
			$classes[] = 'is-active';
		}
		$placeholder = bocp_menu_item_is_placeholder( $item );
		if ( $placeholder ) {
			$classes[] = 'is-placeholder';
		}
		$href    = $placeholder ? '#' : esc_url( $item->url );
		$output .= '<a class="' . esc_attr( implode( ' ', $classes ) ) . '" href="' . $href . '">' . esc_html( $item->title ) . '</a>';

		if ( $has_children ) {
			$output .= '<button type="button" class="drawer-submenu-toggle" aria-expanded="false" aria-label="' . esc_attr__( 'Deschide submeniul', 'bocp' ) . '">' . bocp_icon( 'chevron-down' ) . '</button>';
			$output .= '</div>';
		}
	}

	public function end_el( &$output, $item, $depth = 0, $args = null ) {
		if ( bocp_menu_item_has_children( $item ) ) {
			$output .= '</div>';
		}
	}

	public function start_lvl( &$output, $depth = 0, $args = null ) {
		$output .= '<div class="drawer-submenu">';
	}

	public function end_lvl( &$output, $depth = 0, $args = null ) {
		$output .= '</div>';
	}
}

/**
 * The two small CTA buttons shown in the header (desktop) and drawer
 * (mobile). Configurable from Aspect > Personalizare > BOCP — Butoane
 * meniu; a button only renders once both its label and URL are set, so a
 * fresh install shows nothing here until the admin configures them.
 */
function bocp_get_nav_cta_buttons( $block = false ) {
	$btn1_label = get_theme_mod( 'bocp_nav_btn1_label' );
	$btn1_url   = get_theme_mod( 'bocp_nav_btn1_url' );
	$btn2_label = get_theme_mod( 'bocp_nav_btn2_label' );
	$btn2_url   = get_theme_mod( 'bocp_nav_btn2_url' );

	$block_class = $block ? ' btn-block' : '';
	$html        = '';

	if ( $btn1_label && $btn1_url ) {
		$html .= '<a class="btn btn-outline btn-sm' . esc_attr( $block_class ) . '" href="' . esc_url( $btn1_url ) . '">' . esc_html( $btn1_label ) . '</a>';
	}
	if ( $btn2_label && $btn2_url ) {
		$html .= '<a class="btn btn-primary btn-sm' . esc_attr( $block_class ) . '" href="' . esc_url( $btn2_url ) . '">' . esc_html( $btn2_label ) . '</a>';
	}

	return $html;
}
