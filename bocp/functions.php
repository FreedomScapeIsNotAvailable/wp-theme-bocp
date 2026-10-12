<?php
/**
 * BOCP theme bootstrap.
 *
 * Everything lives in inc/ so a child theme (or this theme on a future
 * update) never has to touch this file to override behaviour — each
 * concern hooks itself in independently.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BOCP_VERSION', '1.2.6' );
define( 'BOCP_DIR', get_template_directory() );
define( 'BOCP_URI', get_template_directory_uri() );

require_once BOCP_DIR . '/inc/setup.php';
require_once BOCP_DIR . '/inc/icons.php';
require_once BOCP_DIR . '/inc/nav-walkers.php';
require_once BOCP_DIR . '/inc/enqueue.php';
require_once BOCP_DIR . '/inc/customizer.php';
require_once BOCP_DIR . '/inc/editor.php';
require_once BOCP_DIR . '/inc/shortcodes.php';
require_once BOCP_DIR . '/inc/cf7-compat.php';
require_once BOCP_DIR . '/inc/admin-setup.php';
require_once BOCP_DIR . '/inc/patterns.php';
require_once BOCP_DIR . '/inc/self-update.php';
