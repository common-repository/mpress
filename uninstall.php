<?php

/**
 * @Author: Debabrata Karfa
 * @Date:   2018-04-28 14:45:39
 * @Last Modified by:   Debabrata Karfa
 * @Last Modified time: 2018-06-27 09:48:37
 */
/**
 * Action will run during Uninstall of MPRESS Plugin.
 *
 * @link       http://www.mpress.app
 * @since      1.0.0
 *
 * @package    Mpress
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}
