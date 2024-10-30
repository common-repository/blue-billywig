<?php
/**
 * Uninstall Blue Billywig
 */

// exit if accessed directly.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'blue-billywig-api-secret' );
delete_option( 'blue-billywig-api-id' );
delete_option( 'blue-billywig-publication' );
delete_option( 'blue-billywig-playout' );
delete_option( 'blue-billywig-embed' );
delete_option( 'blue-billywig-m-status' );
delete_option( 'blue-billywig-page-count' );
