<?php
/**
 * Self-update from the public GitHub repository.
 *
 * style.css declares `Update URI: https://github.com/FreedomScapeIsNotAvailable/wp-theme-bocp`,
 * so WordPress asks this theme about updates through the update_themes_github.com filter
 * (WordPress 5.8+). We answer by reading dist/manifest.json from the repository and, when
 * it lists a newer version, hand WordPress the zip from the same repository. The normal
 * "update available" flow then applies, including automatic updates (enrolled below) and
 * the AI WP Site Manager connector's update_theme tool.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BOCP_REPO_URL', 'https://github.com/FreedomScapeIsNotAvailable/wp-theme-bocp' );
define( 'BOCP_MANIFEST_URL', 'https://raw.githubusercontent.com/FreedomScapeIsNotAvailable/wp-theme-bocp/main/dist/manifest.json' );
define( 'BOCP_PACKAGE_PREFIX', 'https://raw.githubusercontent.com/FreedomScapeIsNotAvailable/wp-theme-bocp/' );

add_filter( 'update_themes_github.com', 'bocp_check_theme_update', 10, 4 );
add_action( 'init', 'bocp_enable_theme_auto_update_once' );
add_action( 'upgrader_process_complete', 'bocp_purge_after_theme_update', 10, 2 );
// The AI WP Site Manager connector fires this before a forced update check: forget the cached manifest.
add_action( 'ai_wp_before_update_check', function () {
	delete_site_transient( 'bocp_theme_manifest' );
} );

/** Fetch and cache the release manifest for one hour. */
function bocp_fetch_manifest( $force = false ) {
	$cached = get_site_transient( 'bocp_theme_manifest' );
	if ( ! $force && is_array( $cached ) ) {
		return $cached;
	}
	$response = wp_remote_get( BOCP_MANIFEST_URL, array(
		'timeout' => 10,
		'headers' => array( 'Accept' => 'application/json', 'Cache-Control' => 'no-cache' ),
	) );
	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
		return false;
	}
	$manifest = json_decode( wp_remote_retrieve_body( $response ), true );
	if ( ! is_array( $manifest ) || empty( $manifest['version'] ) || empty( $manifest['package'] ) ) {
		return false;
	}
	if ( 0 !== strpos( $manifest['package'], BOCP_PACKAGE_PREFIX ) ) {
		return false; // only ever download from the repository itself
	}
	if ( isset( $manifest['sha256'] ) && ! preg_match( '/^[a-f0-9]{64}$/i', (string) $manifest['sha256'] ) ) {
		return false; // a malformed checksum is never trusted
	}
	set_site_transient( 'bocp_theme_manifest', $manifest, HOUR_IN_SECONDS );
	return $manifest;
}

/**
 * Verify the release zip against the sha256 in the manifest before WordPress unpacks it. A manifest without a checksum
 * (older releases) is accepted as before. It catches a corrupted or swapped zip and makes a tampered release visible in the
 * repository history; it cannot stop someone who controls both the manifest and the zip, so keep 2FA and branch protection on.
 */
add_filter( 'upgrader_pre_download', 'bocp_verify_package_hash', 10, 2 );
function bocp_verify_package_hash( $reply, $package ) {
	if ( false !== $reply || ! is_string( $package ) || 0 !== strpos( $package, BOCP_PACKAGE_PREFIX ) ) {
		return $reply;
	}
	$manifest = bocp_fetch_manifest();
	if ( ! $manifest || empty( $manifest['sha256'] ) || $package !== $manifest['package'] ) {
		return $reply;
	}
	if ( ! function_exists( 'download_url' ) ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
	}
	$file = download_url( $package, 300 );
	if ( is_wp_error( $file ) ) {
		return $file;
	}
	if ( ! hash_equals( strtolower( (string) $manifest['sha256'] ), hash_file( 'sha256', $file ) ) ) {
		wp_delete_file( $file );
		return new WP_Error( 'bocp_package_checksum', 'The downloaded theme package does not match the checksum in the release manifest; update refused.' );
	}
	return $file;
}

function bocp_check_theme_update( $update, $theme_data, $theme_stylesheet, $locales ) {
	if ( 'bocp' !== $theme_stylesheet ) {
		return $update; // another theme hosted on github.com
	}
	// The connector's update check clears this transient first; wp-admin's "Check again" does not,
	// so a brand-new release can take up to six hours to show there.
	$manifest = bocp_fetch_manifest();
	if ( ! $manifest || version_compare( $manifest['version'], BOCP_VERSION, '<=' ) ) {
		return $update;
	}
	return array(
		'theme'        => $theme_stylesheet,
		'new_version'  => $manifest['version'],
		'url'          => BOCP_REPO_URL,
		'package'      => $manifest['package'],
		'requires'     => isset( $manifest['requires'] ) ? $manifest['requires'] : '6.0',
		'requires_php' => isset( $manifest['requires_php'] ) ? $manifest['requires_php'] : '7.4',
	);
}

/** Opt the theme into WordPress automatic updates once; Administrators can switch it off under Appearance -> Themes. */
function bocp_enable_theme_auto_update_once() {
	if ( get_site_option( 'bocp_theme_autoupdate_opted' ) ) {
		return;
	}
	$list = (array) get_site_option( 'auto_update_themes', array() );
	if ( ! in_array( 'bocp', $list, true ) ) {
		$list[] = 'bocp';
		update_site_option( 'auto_update_themes', array_values( array_unique( $list ) ) );
	}
	update_site_option( 'bocp_theme_autoupdate_opted', 1 );
}

/** After this theme updates, flush LiteSpeed's page cache so every page picks up new CSS/markup. */
function bocp_purge_after_theme_update( $upgrader, $hook_extra ) {
	if ( empty( $hook_extra['type'] ) || 'theme' !== $hook_extra['type'] ) {
		return;
	}
	$themes = isset( $hook_extra['themes'] ) ? (array) $hook_extra['themes'] : array();
	$mine   = in_array( 'bocp', $themes, true );
	if ( ! $mine && isset( $upgrader->result['destination_name'] ) && 'bocp' === $upgrader->result['destination_name'] ) {
		$mine = true; // zip re-upload over the installed copy arrives as an install
	}
	if ( $mine ) {
		delete_site_transient( 'bocp_theme_manifest' );
		do_action( 'litespeed_purge_all' );
	}
}
