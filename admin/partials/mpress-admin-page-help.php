<?php

/**
 * @Author: Debabrata Karfa
 * @Date:   2018-04-28 16:05:39
 * @Last Modified by:   Debabrata Karfa
 * @Last Modified time: 2018-06-27 09:50:42
 */

/**
 * Provide a Admin Help area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://www.mpress.app
 * @since      1.0.0
 *
 * @package    Mpress
 * @subpackage Mpress/admin
 */

?>

<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
<h2><?php esc_html_e( 'Support', 'mpress' ); ?></h2>
<?php
printf(
	esc_html__( '%1$s %2$s', 'mpress' ),
	esc_html__( 'The simplest way to communicate, drop email to', 'mpress' ),
	sprintf(
		'<a href="%s">%s</a>',
		esc_url( 'mailto:support@mpress.app?Subject=Support%20Mpress' ),
		esc_html__( 'support@mpress.app', 'mpress' )
	)
);
?>
