<?php
/**
 * Editor enhancements shared by both the Block Editor (used for the BOCP
 * pages) and the Classic Editor (still available for regular posts via the
 * Classic Editor plugin's own settings).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Makes the editor preview look like the real front end (fonts, colors,
 * buttons, cards) by loading the same design-system CSS inside the editor.
 * Works for both the Block Editor iframe (needs 'editor-styles' theme
 * support, added in inc/setup.php) and the classic TinyMCE iframe.
 *
 * IMPORTANT: pass each stylesheet as its own array entry, NOT one file that
 * @imports the others. add_editor_style() reads the file(s) and inlines
 * their content into a <style> tag inside the editor iframe — relative
 * @import statements inside that inlined content do not reliably resolve
 * there (confirmed live: the whole design system silently failed to load,
 * leaving the Block Editor canvas as plain unstyled text). Passing the
 * actual files directly avoids @import entirely.
 */
function bocp_editor_style() {
	add_editor_style( array(
		'assets/css/tokens/fonts.css',
		'assets/css/tokens/colors.css',
		'assets/css/tokens/typography.css',
		'assets/css/tokens/spacing.css',
		'assets/css/tokens/base.css',
		'assets/css/site.css',
		'assets/css/editor-style.css',
	) );
}
add_action( 'after_setup_theme', 'bocp_editor_style' );

/**
 * Classic-Editor-only extras below: a "Stiluri" (Styles) dropdown with the
 * BOCP design-system classes, for any post still edited with TinyMCE (e.g.
 * a regular blog post, if the Classic Editor plugin is set to that mode).
 * Harmless no-ops on pages using the Block Editor.
 */
function bocp_mce_buttons_2( $buttons ) {
	array_unshift( $buttons, 'styleselect' );
	return $buttons;
}
add_filter( 'mce_buttons_2', 'bocp_mce_buttons_2' );

function bocp_mce_before_init( $settings ) {
	$settings['style_formats'] = wp_json_encode( array(
		array( 'title' => __( 'Kicker (eticheta mică, roșie)', 'bocp' ), 'selector' => 'span,p', 'classes' => 'kicker', 'wrapper' => true ),
		array( 'title' => __( 'Titlu mare (H1 hero)', 'bocp' ), 'block' => 'h1', 'classes' => 'h-display' ),
		array( 'title' => __( 'Titlu secțiune (H2)', 'bocp' ), 'block' => 'h2', 'classes' => 'h-2' ),
		array( 'title' => __( 'Text introductiv (lead)', 'bocp' ), 'block' => 'p', 'classes' => 'lead' ),
		array( 'title' => __( 'Text mai șters (muted)', 'bocp' ), 'selector' => 'p,span', 'classes' => 'muted' ),
		array( 'title' => __( 'Buton — Primary (roșu)', 'bocp' ), 'selector' => 'a', 'classes' => 'btn btn-primary btn-lg' ),
		array( 'title' => __( 'Buton — Outline', 'bocp' ), 'selector' => 'a', 'classes' => 'btn btn-outline btn-lg' ),
		array( 'title' => __( 'Buton — Secondary (negru)', 'bocp' ), 'selector' => 'a', 'classes' => 'btn btn-secondary' ),
		array( 'title' => __( 'Card (chenar + umbră)', 'bocp' ), 'block' => 'div', 'classes' => 'card card-pad', 'wrapper' => true ),
		array( 'title' => __( 'Etichetă (tag)', 'bocp' ), 'selector' => 'span', 'classes' => 'tag' ),
		array( 'title' => __( 'Secțiune — fundal gri', 'bocp' ), 'block' => 'section', 'classes' => 'section section-sunken', 'wrapper' => true ),
		array( 'title' => __( 'Secțiune — fundal negru', 'bocp' ), 'block' => 'section', 'classes' => 'section section-dark', 'wrapper' => true ),
		array( 'title' => __( 'Grid — 2 coloane', 'bocp' ), 'block' => 'div', 'classes' => 'grid g-2', 'wrapper' => true ),
		array( 'title' => __( 'Grid — 3 coloane', 'bocp' ), 'block' => 'div', 'classes' => 'grid g-3', 'wrapper' => true ),
		array( 'title' => __( 'Grid — 4 coloane', 'bocp' ), 'block' => 'div', 'classes' => 'grid g-4', 'wrapper' => true ),
	) );
	return $settings;
}
add_filter( 'tiny_mce_before_init', 'bocp_mce_before_init' );
