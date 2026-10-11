<?php
/**
 * BOCP Child theme bootstrap.
 *
 * The parent theme (bocp) already registers its own styles/scripts with
 * `bocp-*` handles from inc/enqueue.php. This file loads the parent's main
 * stylesheet (required for any child theme) plus this child's own
 * style.css right after it, so anything added here wins.
 *
 * Add your own hooks/functions below — nothing here is touched by
 * updates to the parent theme.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function bocp_child_enqueue_styles() {
	wp_enqueue_style(
		'bocp-parent-style',
		get_template_directory_uri() . '/style.css',
		array(),
		wp_get_theme( get_template() )->get( 'Version' )
	);

	wp_enqueue_style(
		'bocp-child-style',
		get_stylesheet_directory_uri() . '/style.css',
		array( 'bocp-parent-style', 'bocp-site' ),
		wp_get_theme()->get( 'Version' )
	);
}
add_action( 'wp_enqueue_scripts', 'bocp_child_enqueue_styles', 20 );
