<?php

/**
 * @CodeAuthor: Debabrata Karfa
 * @Date:   2018-04-28 14:45:39
 * @Last Modified by:   Debabrata Karfa
 * @Last Modified time: 2018-06-27 09:18:46
 */

/**
 * MPress - Convert your WordPress Website to Mobile Apps instantly.
 *
 * @link              http://www.mpress.app/
 * @since             1.0.0
 * @package           Mpress
 *
 * @wordpress-plugin
 * Plugin Name:       MPress
 * Plugin URI:        https://wordpress.org/plugins/mpress/
 * Description:       MPress, convert your WordPress website to Mobile Application.
 * Version:           1.0.0
 * Author:            MPress Lab
 * Author URI:        http://www.mpress.app/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       mpress
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PLUGIN_NAME_VERSION', '1.0.0' );
define( 'MPRESS_REST_API_VERSION', '1' );
define( 'MPRESS_API_NAMESPACE', 'mpress/v' . MPRESS_REST_API_VERSION );
define( 'MPRESS_MAILGUN_API', 'ae65d81a4705ac00ff101fb67624e16e-0470a1f7-b958bfbe' );
define( 'MPRESS_MAILGUN_DOMAIN', 'ems.mpress.app' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-mpress-activator.php
 */
function activate_mpress() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-mpress-activator.php';
	Mpress_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-mpress-deactivator.php
 */
function deactivate_mpress() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-mpress-deactivator.php';
	Mpress_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_mpress' );
register_deactivation_hook( __FILE__, 'deactivate_mpress' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-mpress.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_mpress() {

	$plugin = new Mpress();
	$plugin->run();

}

run_mpress();
