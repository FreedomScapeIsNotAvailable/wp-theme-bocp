<?php
/**
 * Theme supports, nav menus.
 *
 * The BOCP pages use the Block Editor (Gutenberg) so sections/cards can be
 * moved, duplicated, and images replaced natively — Elementor/Pagelayer
 * style page builders are still not used anywhere. Regular posts can still
 * use the Classic Editor if the Classic Editor plugin (already installed)
 * is configured to do so from Settings > Writing; nothing here forces one
 * editor over the other anymore.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function bocp_setup() {
	load_theme_textdomain( 'bocp', BOCP_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'custom-logo', array(
		'height'      => 48,
		'width'       => 200,
		'flex-height' => true,
		'flex-width'  => true,
	) );

	// Needed so add_editor_style() (inc/editor.php) also applies inside the
	// Block Editor's iframe, not just the old TinyMCE-only editor.
	add_theme_support( 'editor-styles' );
	add_theme_support( 'wp-block-styles' );

	// Turns on the native "Spacing" panel (padding/margin) in the block
	// Inspector sidebar for Group/Columns/Image/etc. — lets an admin add or
	// adjust spacing on any block straight from the editor UI, no CSS
	// needed. 'appearance-tools' is the modern one-flag way to get this
	// (plus border/link-color controls) on a classic theme without a full
	// theme.json; 'custom-spacing' is kept too as an older, narrower
	// fallback for padding specifically.
	add_theme_support( 'appearance-tools' );
	add_theme_support( 'custom-spacing' );

	// Still deliberately NOT added: full block-template/theme.json support —
	// this stays a classic PHP-template theme, just with the block editor
	// available for writing page content.

	// Only the header uses a real nav menu — the footer's 4 content columns
	// are widget areas instead (registered below), so they're customized
	// from Aspect > Widgets: drag any widget (menu, text, custom HTML) into
	// a column, reorder/add/remove freely, no theme code involved.
	register_nav_menus( array(
		'primary' => __( 'Meniu principal (header)', 'bocp' ),
	) );
}
add_action( 'after_setup_theme', 'bocp_setup' );

function bocp_widgets_init() {
	$common = array(
		'before_title'  => '<h5>',
		'after_title'   => '</h5>',
		'before_widget' => '<div class="footer-col %2$s">',
		'after_widget'  => '</div>',
	);

	// Falls back to the Customizer's logo/note fields (see footer-columns.php)
	// as long as this sidebar has no widgets, so nothing goes blank until it's set up.
	register_sidebar( array_merge( $common, array(
		'name'        => __( 'Footer — Coloana 1 (Logo & descriere)', 'bocp' ),
		'id'          => 'footer-brand',
		'description' => __( 'Prima coloană din footer, cea cu logo-ul. Adaugă un widget "Imagine" pentru logo și un widget "Text" pentru descriere, dacă vrei să le editezi de aici în loc de Customizer.', 'bocp' ),
	) ) );

	register_sidebar( array_merge( $common, array(
		'name'        => __( 'Footer — Coloana 2 (ex. Module)', 'bocp' ),
		'id'          => 'footer-col-1',
		'description' => __( 'A doua coloană de link-uri din footer. Adaugă un widget "Meniu de navigare" sau "Text" cu link-urile tale.', 'bocp' ),
	) ) );

	register_sidebar( array_merge( $common, array(
		'name'        => __( 'Footer — Coloana 3 (ex. Companie)', 'bocp' ),
		'id'          => 'footer-col-2',
		'description' => __( 'A treia coloană de link-uri din footer.', 'bocp' ),
	) ) );

	register_sidebar( array_merge( $common, array(
		'name'        => __( 'Footer — Coloana 4 (Contact)', 'bocp' ),
		'id'          => 'footer-contact',
		'description' => __( 'Coloana de contact din footer. Poți pune un widget "HTML personalizat" cu shortcode-ul [bocp_contact_info], sau propriul text.', 'bocp' ),
	) ) );
}
add_action( 'widgets_init', 'bocp_widgets_init' );
