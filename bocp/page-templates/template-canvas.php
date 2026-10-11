<?php
/**
 * Template Name: BOCP — Pagină (conținut vizual)
 *
 * The entire page is just the_content() — whatever the admin writes/pastes
 * in the Classic Editor (Visual or Text tab). No meta boxes, no PHP-driven
 * sections: what you see in the editor is what renders on the front end.
 * Hero, cards, grids etc. are ordinary HTML using the theme's CSS classes
 * (documented in the BOCP Setup admin screen), typed or pasted directly
 * into the editor like any classic WordPress page.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();
	the_content();
endwhile;

get_footer();
