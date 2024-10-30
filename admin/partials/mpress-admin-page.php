<?php

/**
 * @Author: Debabrata Karfa
 * @Date:   2018-04-28 15:40:39
 * @Last Modified by:   Debabrata Karfa
 * @Last Modified time: 2018-06-27 09:50:40
 */
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://www.mpress.app
 * @since      1.0.0
 *
 * @package    Mpress
 * @subpackage Mpress/admin/partials
 */
?>
<div class="wrap">
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
	<form action="options.php" method="post">
		<?php
			settings_fields( $this->plugin_name );
			do_settings_sections( $this->plugin_name );
			submit_button();
		?>
	</form>
</div>
