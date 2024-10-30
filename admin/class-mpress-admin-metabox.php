<?php

/**
 * @Author: Debabrata Karfa
 * @Date:   2018-06-18 23:34:24
 * @Last Modified by:   Debabrata Karfa
 * @Last Modified time: 2018-06-27 09:50:29
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://www.mpress.app
 * @since      1.0.0
 *
 * @package    Mpress
 * @subpackage Mpress/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Mpress
 * @subpackage Mpress/admin
 * @author     Debabrata Karfa <debabrata.karfa@ctrl.biz>
 */
class Mpress_Admin_Metabox {

	/**
	 * The options name to be used in this plugin
	 *
	 * @since   1.0.0
	 * @access  private
	 * @var     string      $option_name    Option name of this plugin
	 */
	private $option_name = 'mpress_panel';

	/**
	 * The post meta data
	 *
	 * @since       1.0.0
	 * @access      private
	 * @var         string          $meta               The post meta data.
	 */
	private $meta;

	/**
	 * The ID of this plugin.
	 *
	 * @since       1.0.0
	 * @access      private
	 * @var         string          $plugin_name        The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since       1.0.0
	 * @access      private
	 * @var         string          $version            The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string $plugin_name The name of this plugin.
	 * @param    string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		// Action and Filters to be fired
		add_action( 'post_submitbox_misc_actions', array( &$this, 'display_custom_meta_box' ) );
		add_action( 'save_post', array( &$this, 'mpress_push_notification_callback' ) );
		add_action( 'update_post', array( &$this, 'mpress_push_notification_callback' ) );
	}

	/**
	 * Add Custom Meta Box for Post Type
	 *
	 * @method   display_custom_meta_box
	 *
	 * @return   void Add Meta box.
	 *
	 * @author   dkarfa <Debabrata Karfa>
	 * @since    1.0.0
	 */
	public function display_custom_meta_box() {
		$post_id = get_the_ID();

		if ( get_post_type( $post_id ) != 'post' ) {
			return;
		}

		$value = get_post_meta( $post_id, '_mpress_push_notification', true );
		wp_nonce_field( 'mpress_push_notification_nonce_' . $post_id, 'mpress_push_notification_nonce' );
		?>
			<div class="misc-pub-section misc-pub-section-last">
				<label class="mpress_push_notification"><input type="checkbox" value="1" <?php checked( $value, true, true ); ?> name="_mpress_push_notification" /><?php _e( 'Send Push Notification', 'mpress' ); ?><i class="fas fa-bell"></i></label>
			</div>
		<?php
	}

	/**
	 * Send Push Notification
	 *
	 * @method   mpress_push_notification_callback
	 *
	 * @return   array Send Push Notification.
	 *
	 * @author   dkarfa <Debabrata Karfa>
	 * @since    1.0.0
	 */
	public function mpress_push_notification_callback( $post ) {

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post ) ) {
			return;
		}

		if ( isset( $_POST['_mpress_push_notification'] ) ) {

			if ( ! get_post_meta( $post_id, '_mpress_push_notification', true ) ) {
				add_post_meta( $post, '_mpress_push_notification', 1 );
			} else {
				update_post_meta( $post, '_mpress_push_notification', 1 );
			}

			$response               = $this->send_notification( $post );
			$return['allresponses'] = $response;
			$return                 = json_encode( $return );

		} else {
			$return['error_message'] = 'Push Notification not send';
			$return                  = json_encode( $return );
		}

	}

	/**
	 * Send Push Notification using One Signal
	 *
	 * @method   send_notification
	 *
	 * @return   array Return response after sending push notification.
	 *
	 * @author   dkarfa <Debabrata Karfa>
	 * @since    1.0.0
	 */
	public function send_notification( $post_id ) {

		$one_signal_app_id       = get_option( $this->option_name . '_one_signal_app_id' );
		$one_signal_rest_api_key = get_option( $this->option_name . '_one_signal_rest_api_key' );

		$content = array(
			'en' => 'New Post publish',
		);

		$fields = array(
			'app_id'            => $one_signal_app_id,
			'included_segments' => array(
				'All',
			),
			'data'              => array(
				'post_id' => $post_id,
			),
			'contents'          => $content,
		);

		$fields = json_encode( $fields );

		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, 'https://onesignal.com/api/v1/notifications' );
		curl_setopt(
			$ch, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json; charset=utf-8',
				'Authorization: Basic ' . $one_signal_rest_api_key,
			)
		);
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_HEADER, false );
		curl_setopt( $ch, CURLOPT_POST, true );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $fields );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );

		$response = curl_exec( $ch );
		curl_close( $ch );
		return $response;
	}
}
