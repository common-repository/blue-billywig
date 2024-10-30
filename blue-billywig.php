<?php
/**
 * Blue Billywig
 *
 * @since             1.0.0
 * @package           BlueBillywigPlugin
 *
 * Plugin Name:       Blue Billywig
 * Description:       A plugin for managing video content.
 * Version:           1.0.10
 * Tested up to:      6.6.2
 * Requires PHP:      8.1.0
 * Author:            Blue Billywig
 * Author URI:        https://www.bluebillywig.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       blue-billywig-plugin
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants.
define( 'BLUE_BILLYWIG_PLUGIN_VERSION', '1.0.3' );
define( 'BLUE_BILLYWIG_PLUGIN_FILE', __FILE__ );
define( 'BLUE_BILLYWIG_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'BLUE_BILLYWIG_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

// Include files.
require plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

// Initilize main plugin class.
$bluebillywig = new BlueBillywigPlugin\Plugin();

// Register activation + deactivation hooks.
register_activation_hook( __FILE__, array( $bluebillywig, 'activate' ) );
register_deactivation_hook( __FILE__, array( $bluebillywig, 'deactivate' ) );
