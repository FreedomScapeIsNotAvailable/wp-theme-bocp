<?php
/**
 * Style/script registration. Every handle is prefixed `bocp-` so a child
 * theme can safely wp_dequeue_style()/wp_dequeue_script() any one of them
 * and enqueue its own replacement, without touching this file.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function bocp_enqueue_assets() {
	$tokens = array( 'colors', 'typography', 'spacing', 'base' );

	$font_choice = get_theme_mod( 'bocp_font_choice', 'plex' );
	if ( 'plex' === $font_choice ) {
		wp_enqueue_style( 'bocp-fonts', BOCP_URI . '/assets/css/tokens/fonts.css', array(), BOCP_VERSION );
	}

	$deps = array();
	if ( 'plex' === $font_choice ) {
		$deps[] = 'bocp-fonts';
	}

	foreach ( $tokens as $token ) {
		$handle = 'bocp-token-' . $token;
		wp_enqueue_style( $handle, BOCP_URI . '/assets/css/tokens/' . $token . '.css', $deps, BOCP_VERSION );
		$deps = array( $handle );
	}

	// Depends on wp-block-library so our overrides for core blocks (e.g. the
	// Columns block used for hero/feature images) always load after — and
	// win the cascade over — core's own block styles.
	$deps[] = 'wp-block-library';
	// site.css changes far more often than the rest of the theme during
	// active work on it — version by file mtime (not the static
	// BOCP_VERSION) so every edit automatically busts browser/host caching,
	// instead of requiring a manual cache purge to see CSS changes live.
	$site_css_path = BOCP_DIR . '/assets/css/site.css';
	$site_css_ver  = file_exists( $site_css_path ) ? filemtime( $site_css_path ) : BOCP_VERSION;
	wp_enqueue_style( 'bocp-site', BOCP_URI . '/assets/css/site.css', $deps, $site_css_ver );

	wp_add_inline_style( 'bocp-token-colors', bocp_customizer_inline_css() );

	// Mobile fixes for the CookieAdmin plugin's consent banner, which has no
	// admin settings for this. Depending on 'cookieadmin-style' (registered
	// by the plugin) makes WordPress print this after the plugin's own CSS
	// regardless of plugin updates, so our rules win the cascade without a
	// pile of !important — and it's a no-op if that plugin is ever removed.
	$cookieadmin_css_path = BOCP_DIR . '/assets/css/cookieadmin-override.css';
	$cookieadmin_css_ver  = file_exists( $cookieadmin_css_path ) ? filemtime( $cookieadmin_css_path ) : BOCP_VERSION;
	wp_enqueue_style( 'bocp-cookieadmin-override', BOCP_URI . '/assets/css/cookieadmin-override.css', array( 'cookieadmin-style' ), $cookieadmin_css_ver );

	$site_js_path = BOCP_DIR . '/assets/js/site.js';
	$site_js_ver  = file_exists( $site_js_path ) ? filemtime( $site_js_path ) : BOCP_VERSION;
	wp_enqueue_script( 'bocp-site', BOCP_URI . '/assets/js/site.js', array(), $site_js_ver, true );
}
add_action( 'wp_enqueue_scripts', 'bocp_enqueue_assets' );

/**
 * Google tag from the Customizer (section Google tag). Same markup and ids
 * as Site Kit's snippet (google_gtagjs / google_gtagjs-inline). With the
 * CookieAdmin plugin active and no analytics consent yet, both tags are
 * printed already blocked (type text/plain, category analytics): CookieAdmin
 * runs them when the visitor accepts, without needing a cookie scan first.
 */
function bocp_print_google_tag() {
	$tag_id = bocp_sanitize_google_tag_id( get_theme_mod( 'bocp_google_tag_id', '' ) );
	if ( '' === $tag_id || is_user_logged_in() ) {
		return;
	}
	$blocked = defined( 'COOKIEADMIN_VERSION' ) && ! bocp_has_analytics_consent();
	$extra   = $blocked ? array( 'type' => 'text/plain', 'data-cookieadmin-category' => 'analytics' ) : array();
	wp_print_script_tag( array_merge( $extra, array(
		'async' => true,
		'id'    => 'google_gtagjs',
		'src'   => 'https://www.googletagmanager.com/gtag/js?id=' . rawurlencode( $tag_id ),
	) ) );
	wp_print_inline_script_tag(
		"window.dataLayer = window.dataLayer || [];function gtag(){dataLayer.push(arguments);}gtag('js', new Date());gtag('config', '" . esc_js( $tag_id ) . "');",
		array_merge( $extra, array( 'id' => 'google_gtagjs-inline' ) )
	);
}

/** True when CookieAdmin's consent cookie accepts all or the analytics category. */
function bocp_has_analytics_consent() {
	if ( empty( $_COOKIE['cookieadmin_consent'] ) ) {
		return false;
	}
	$consent = json_decode( wp_unslash( $_COOKIE['cookieadmin_consent'] ), true );
	if ( ! is_array( $consent ) || ( isset( $consent['reject'] ) && 'true' === $consent['reject'] ) ) {
		return false;
	}
	return ( isset( $consent['accept'] ) && 'true' === $consent['accept'] ) || ( isset( $consent['analytics'] ) && 'true' === $consent['analytics'] );
}
add_action( 'wp_head', 'bocp_print_google_tag', 5 );

/**
 * The page HTML differs by CookieAdmin consent (blocked or live tag), so
 * LiteSpeed Cache must keep a separate copy per consent cookie value.
 */
function bocp_google_tag_vary_cookies( $cookies ) {
	if ( defined( 'COOKIEADMIN_VERSION' ) && '' !== bocp_sanitize_google_tag_id( get_theme_mod( 'bocp_google_tag_id', '' ) ) ) {
		$cookies[] = 'cookieadmin_consent';
	}
	return $cookies;
}
add_filter( 'litespeed_vary_curr_cookies', 'bocp_google_tag_vary_cookies' );

/**
 * Microsoft UET consent mode driven by CookieAdmin. Printed first in the head,
 * before any UET tag (e.g. the Microsoft Advertising UET plugin), it sets
 * ad_storage to denied until the visitor accepts all cookies or the marketing
 * category, and sends an update when the choice changes on the page. Decided in
 * the browser, so cached pages need no consent variant. Harmless without UET.
 */
function bocp_print_uet_consent() {
	if ( ! defined( 'COOKIEADMIN_VERSION' ) || is_admin() ) {
		return;
	}
	$js = "(function(w,d){w.uetq=w.uetq||[];"
		. "function s(){var m=d.cookie.match(/(?:^|;\\s*)cookieadmin_consent=([^;]*)/),c;if(!m){return null;}"
		. "try{c=JSON.parse(decodeURIComponent(m[1]));}catch(e){return null;}"
		. "if(!c||c.reject==='true'){return 'denied';}"
		. "return(c.accept==='true'||c.marketing==='true')?'granted':'denied';}"
		. "var l=s()||'denied';w.uetq.push('consent','default',{'ad_storage':l});"
		. "function u(){var n=s();if(n&&n!==l){l=n;w.uetq.push('consent','update',{'ad_storage':n});}}"
		. "d.addEventListener('click',function(e){var t=e.target;"
		. "if(t&&t.closest&&t.closest('.cookieadmin_accept_btn,.cookieadmin_reject_btn,.cookieadmin_save_btn')){setTimeout(u,300);}},true);"
		. "})(window,document);";
	wp_print_inline_script_tag( $js, array( 'id' => 'bocp-uet-consent', 'data-no-optimize' => '1' ) );
}
add_action( 'wp_head', 'bocp_print_uet_consent', 0 );

/**
 * Builds the small :root override block driven by Customizer values
 * (brand colors + optional system-font fallback). Everything else in the
 * design system (spacing, radius, shadows, neutrals) is left untouched.
 */
function bocp_customizer_inline_css() {
	$primary = sanitize_hex_color( get_theme_mod( 'bocp_color_primary', '#dc2626' ) );
	$dark    = sanitize_hex_color( get_theme_mod( 'bocp_color_dark', '#000000' ) );

	if ( ! $primary ) {
		$primary = '#dc2626';
	}
	if ( ! $dark ) {
		$dark = '#000000';
	}

	$primary_hover  = bocp_shade_color( $primary, -0.15 );
	$primary_active = bocp_shade_color( $primary, -0.3 );
	$primary_soft   = bocp_tint_color( $primary, 0.94 );
	$primary_border = bocp_tint_color( $primary, 0.75 );

	$css = ":root{";
	$css .= "--bocp-red:{$primary};";
	$css .= "--bocp-red-hover:{$primary_hover};";
	$css .= "--bocp-red-active:{$primary_active};";
	$css .= "--bocp-red-soft:{$primary_soft};";
	$css .= "--bocp-red-border:{$primary_border};";
	$css .= "--bocp-black:{$dark};";
	$css .= "--focus-ring:" . bocp_rgba_from_hex( $primary, 0.35 ) . ';';
	$css .= '}';

	// Optional colour for the header menu links (Customizer: BOCP — Culori brand). Empty = theme default.
	$nav_link = sanitize_hex_color( get_theme_mod( 'bocp_nav_link_color', '' ) );
	if ( $nav_link ) {
		$css .= ".nav-links .nav-link{color:{$nav_link}}.nav-secondary .nav-link{color:var(--text-body)}";
	}

	$font_choice = get_theme_mod( 'bocp_font_choice', 'plex' );
	if ( 'system' === $font_choice ) {
		$css .= ':root{--font-sans: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif; --font-mono: ui-monospace, "SFMono-Regular", Menlo, monospace;}';
	}

	return $css;
}

/**
 * Tiny hex color helpers (no dependency on any color library) used to
 * derive hover/active/soft/border shades from a single "Primary color"
 * Customizer control, so admins only ever pick one brand color.
 */
function bocp_hex_to_rgb( $hex ) {
	$hex = ltrim( $hex, '#' );
	if ( strlen( $hex ) === 3 ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}
	return array(
		hexdec( substr( $hex, 0, 2 ) ),
		hexdec( substr( $hex, 2, 2 ) ),
		hexdec( substr( $hex, 4, 2 ) ),
	);
}

function bocp_rgb_to_hex( $r, $g, $b ) {
	return sprintf( '#%02x%02x%02x', max( 0, min( 255, round( $r ) ) ), max( 0, min( 255, round( $g ) ) ), max( 0, min( 255, round( $b ) ) ) );
}

/** Darkens a hex color by $amount (0..1). Negative amount lightens. */
function bocp_shade_color( $hex, $amount ) {
	list( $r, $g, $b ) = bocp_hex_to_rgb( $hex );
	$factor = 1 + $amount;
	return bocp_rgb_to_hex( $r * $factor, $g * $factor, $b * $factor );
}

/** Mixes a hex color toward white by $amount (0..1, 1 = pure white). */
function bocp_tint_color( $hex, $amount ) {
	list( $r, $g, $b ) = bocp_hex_to_rgb( $hex );
	$r = $r + ( 255 - $r ) * $amount;
	$g = $g + ( 255 - $g ) * $amount;
	$b = $b + ( 255 - $b ) * $amount;
	return bocp_rgb_to_hex( $r, $g, $b );
}

function bocp_rgba_from_hex( $hex, $alpha ) {
	list( $r, $g, $b ) = bocp_hex_to_rgb( $hex );
	return "rgba({$r}, {$g}, {$b}, {$alpha})";
}
