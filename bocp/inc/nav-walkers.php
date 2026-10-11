<?php
/**
 * Menu walkers + header CTA buttons.
 *
 * WP core's default nav walker doesn't add the exact classes the design
 * needs on <a> tags (only on <li>), so these two small walkers render
 * "nav-link"/"drawer-link" + "is-active" exactly where the CSS expects
 * them. Top-level items with children ("menu-item-has-children", added by
 * WP core in wp-includes/nav-menu-template.php) get wrapped so a dropdown
 * (desktop: hover, see .nav-dropdown in site.css) or an accordion panel
 * (mobile drawer: tap toggle, see initDrawerSubmenus() in site.js) can be
 * rendered for their children. Only one level of children is supported —
 * BOCP-style marketing menus don't need deeper nesting.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BOCP_Nav_Walker extends Walker_Nav_Menu {
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$is_active    = in_array( 'current-menu-item', $item->classes, true ) || in_array( 'current_page_item', $item->classes, true );
		$has_children = 0 === $depth && in_array( 'menu-item-has-children', $item->classes, true );

		if ( $has_children ) {
			$output .= '<div class="nav-item has-dropdown">';
		}

		$classes = array( 0 === $depth ? 'nav-link' : 'nav-dropdown-link' );
		if ( $is_active ) {
			$classes[] = 'is-active';
		}

		$output .= '<a class="' . esc_attr( implode( ' ', $classes ) ) . '" href="' . esc_url( $item->url ) . '">' . esc_html( $item->title );
		if ( $has_children ) {
			$output .= bocp_icon( 'chevron-down', array( 'class' => 'nav-link-caret' ) );
		}
		$output .= '</a>';
	}

	public function end_el( &$output, $item, $depth = 0, $args = null ) {
		if ( 0 === $depth && in_array( 'menu-item-has-children', $item->classes, true ) ) {
			$output .= '</div>';
		}
	}

	public function start_lvl( &$output, $depth = 0, $args = null ) {
		$output .= '<div class="nav-dropdown">';
	}

	public function end_lvl( &$output, $depth = 0, $args = null ) {
		$output .= '</div>';
	}
}

class BOCP_Drawer_Walker extends Walker_Nav_Menu {
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$is_active    = in_array( 'current-menu-item', $item->classes, true ) || in_array( 'current_page_item', $item->classes, true );
		$has_children = 0 === $depth && in_array( 'menu-item-has-children', $item->classes, true );

		if ( $has_children ) {
			$output .= '<div class="drawer-item has-submenu">';
			$output .= '<div class="drawer-item-row">';
		}

		$classes = array( 0 === $depth ? 'drawer-link' : 'drawer-sublink' );
		if ( $is_active ) {
			$classes[] = 'is-active';
		}
		$output .= '<a class="' . esc_attr( implode( ' ', $classes ) ) . '" href="' . esc_url( $item->url ) . '">' . esc_html( $item->title ) . '</a>';

		if ( $has_children ) {
			$output .= '<button type="button" class="drawer-submenu-toggle" aria-expanded="false" aria-label="' . esc_attr__( 'Deschide submeniul', 'bocp' ) . '">' . bocp_icon( 'chevron-down' ) . '</button>';
			$output .= '</div>';
		}
	}

	public function end_el( &$output, $item, $depth = 0, $args = null ) {
		if ( 0 === $depth && in_array( 'menu-item-has-children', $item->classes, true ) ) {
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
