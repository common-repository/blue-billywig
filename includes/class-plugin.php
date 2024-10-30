<?php

namespace BlueBillywigPlugin;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use BlueBillywigPlugin\Admin\Admin;
use BlueBillywigPlugin\Admin\Ajax;
use BlueBillywigPlugin\Admin\Filters;
use BlueBillywigPlugin\Admin\Shortcode;

/**
 * Main BlueBillywig class
 */
class Plugin {

	/**
	 * Class constructor
	 */
	public function __construct() {
		// Classes.
		if ( is_admin() ) {
			$admin = new Admin();
			$admin->init();
		}
		( new Shortcode() )->init();
		( new Filters() )->init();

		// Register REST API routes.
		add_action('rest_api_init', array($this, 'register_rest_routes'));
		add_action('rest_api_init', array($this, 'register_custom_rest_routes'));
	}

	/**
	 * Register REST API routes
	 */
	public function register_rest_routes() {
		$admin = new Admin();
		$admin->register_rest_routes();
	}
	public function register_custom_rest_routes(){
		$admin = new Admin();
		$admin->register_custom_rest_routes();
	}

	/**
	 * Called on activation of the plugin
	 */
	public function activate() {
		update_option( 'blue-billywig-api-secret', '' );
		update_option( 'blue-billywig-api-id', '' );
		update_option( 'blue-billywig-publication', '' );
		update_option( 'blue-billywig-playout', 'default' );
		update_option( 'blue-billywig-embed', 'javascript' );
		update_option( 'blue-billywig-m-status', 'published' );
		update_option( 'blue-billywig-page-count', '15' );
	}

	/**
	 * Called on deactivation of plugin
	 */
	public function deactivate() {}
}
