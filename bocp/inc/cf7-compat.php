<?php
/**
 * Contact Form 7 visual integration — style-only. The theme never requires
 * CF7 to activate; this file simply makes CF7's own markup match the BOCP
 * design system when the plugin happens to be active, without touching the
 * plugin itself.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function bocp_cf7_is_active() {
	return function_exists( 'wpcf7' ) || class_exists( 'WPCF7_ContactForm' );
}

function bocp_cf7_enqueue_style() {
	if ( bocp_cf7_is_active() ) {
		wp_enqueue_style( 'bocp-cf7', BOCP_URI . '/assets/css/cf7.css', array( 'bocp-site' ), BOCP_VERSION );
	}
}
add_action( 'wp_enqueue_scripts', 'bocp_cf7_enqueue_style', 20 );
