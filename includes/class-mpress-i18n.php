<?php

/**
 * @Author: Debabrata Karfa
 * @Date:   2018-04-28 14:45:39
 * @Last Modified by:   Debabrata Karfa
 * @Last Modified time: 2018-06-27 09:50:21
 */

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://www.mpress.app
 * @since      1.0.0
 *
 * @package    Mpress
 * @subpackage Mpress/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Mpress
 * @subpackage Mpress/includes
 * @author     Debabrata Karfa <debabrata.karfa@ctrl.biz>
 */
class Mpress_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'mpress',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
