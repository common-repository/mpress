<?php

/**
 * @Author: Debabrata Karfa
 * @Date:   2018-04-28 16:05:39
 * @Last Modified by:   Debabrata Karfa
 * @Last Modified time: 2018-06-27 10:22:02
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
use Mailgun\Mailgun;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Mpress_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The options name to be used in this plugin
	 *
	 * @since   1.0.0
	 * @access  private
	 * @var     string      $option_name    Option name of this plugin
	 */
	private $option_name = 'mpress_panel';

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		add_action( 'admin_menu', array( &$this, 'add_mpress_dashboard' ) );
		add_action( 'admin_init', array( &$this, 'register_setting' ) );
		add_action( 'add_option' . $this->option_name, array( &$this, 'action_added_option_mpress_panel' ), 10, 2 );
		// add_action( 'update_option', array( &$this, 'action_added_option_mpress_panel' ), 10, 3 );
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Mpress_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Mpress_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/mpress-admin.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name . '-font-awesome', 'https://use.fontawesome.com/releases/v5.0.13/css/all.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Mpress_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Mpress_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/mpress-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Run on
	 *
	 * @method   action_added_option_mpress_panel
	 *
	 * @param    [type] $option_name Options Name
	 * @param    [type] $new_data      Options Data
	 * @param    [type] $old_data      Old Options Data
	 *
	 * @return   void              Will send email.
	 *
	 * @author  dkarfa <Debabrata Karfa>
	 *
	 * @since       version
	 *
	 * @modified time
	 */
	public function action_added_option_mpress_panel( $option_name, $new_data, $old_data ) {

		$mg = Mailgun::create( MPRESS_MAILGUN_API );

		$mg->messages()->send(
			MPRESS_MAILGUN_DOMAIN, [
				'from'    => 'MPRESS <users@mpress.app>',
				'to'      => 'debabrata.karfa@armentum.co',
				'subject' => 'New MPRESS Plugin Install at ' . get_site_url(),
				'text'    => 'New Site activate at ' . get_site_url() . ', kindly set up mobile application.',
			]
		);
	}


	/**
	 * Adds a settings page link to a menu
	 *
	 * @link        https://codex.wordpress.org/Administration_Menus
	 * @since       1.0.0
	 * @return      void
	 */
	public function add_mpress_dashboard() {

		add_menu_page(
			__( 'MPress Dashboard', 'mpress' ),
			__( 'MPress', 'mpress' ),
			'manage_options',
			$this->plugin_name,
			array( &$this, 'mpress_admin_page_callback' ),
			plugins_url( 'mpress/admin/svg/icon-gray.svg' ),
			6
		);

		add_submenu_page(
			'mpress',
			apply_filters( $this->plugin_name . '-settings-page-title', esc_html__( 'Help Center FAQs', 'rippling-helpcenter' ) ),
			apply_filters( $this->plugin_name . '-settings-menu-title', esc_html__( 'Help', 'rippling-helpcenter' ) ),
			'manage_options',
			$this->plugin_name . '-help',
			array( $this, 'mpress_admin_page_help_callback' )
		);

	} // add_mpress_dashboard()

	public function mpress_admin_page_callback() {
		include_once 'partials/mpress-admin-page.php';
	}

	public function mpress_admin_page_help_callback() {
		include_once 'partials/mpress-admin-page-help.php';
	}

	/**
	 * Register all related settings of this plugin
	 *
	 * @since  1.0.0
	 */
	public function register_setting() {
		add_settings_section(
			$this->option_name . '_general',
			__( 'General Setting', 'mpress' ),
			array( $this, $this->option_name . '_general_md' ),
			$this->plugin_name
		);

		add_settings_field(
			$this->option_name . '_theme_color',
			__( 'Theme Color', 'mpress' ),
			array( $this, $this->option_name . '_theme_color_md' ),
			$this->plugin_name,
			$this->option_name . '_general',
			array( 'label_for' => $this->option_name . '_theme_color_md' )
		);

		add_settings_field(
			$this->option_name . '_button_color',
			__( 'Button Color', 'mpress' ),
			array( $this, $this->option_name . '_button_color_md' ),
			$this->plugin_name,
			$this->option_name . '_general',
			array( 'label_for' => $this->option_name . '_button_color_md' )
		);

		add_settings_field(
			$this->option_name . '_theme_color',
			__( 'Theme Color', 'mpress' ),
			array( $this, $this->option_name . '_theme_color_md' ),
			$this->plugin_name,
			$this->option_name . '_general',
			array( 'label_for' => $this->option_name . '_theme_color_md' )
		);

		add_settings_field(
			$this->option_name . '_app_name',
			__( 'App Name', 'mpress' ),
			array( $this, $this->option_name . '_app_name_md' ),
			$this->plugin_name,
			$this->option_name . '_general',
			array( 'label_for' => $this->option_name . '_app_name_md' )
		);

		add_settings_field(
			$this->option_name . '_app_slogan',
			__( 'App Slogan', 'mpress' ),
			array( $this, $this->option_name . '_app_slogan_md' ),
			$this->plugin_name,
			$this->option_name . '_general',
			array( 'label_for' => $this->option_name . '_app_slogan_md' )
		);

		add_settings_section(
			$this->option_name . '_api',
			__( 'API Setting', 'mpress' ),
			array( $this, $this->option_name . '_api_md' ),
			$this->plugin_name
		);

		add_settings_field(
			$this->option_name . '_mpress_app_id',
			__( 'MPress API', 'mpress' ),
			array( $this, $this->option_name . '_mpress_app_id_md' ),
			$this->plugin_name,
			$this->option_name . '_api',
			array( 'label_for' => $this->option_name . '_mpress_app_id_md' )
		);

		add_settings_field(
			$this->option_name . '_one_signal_app_id',
			__( 'OneSignal API ID', 'mpress' ),
			array( $this, $this->option_name . '_one_signal_app_id_md' ),
			$this->plugin_name,
			$this->option_name . '_api',
			array( 'label_for' => $this->option_name . '_one_signal_app_id_md' )
		);

		add_settings_field(
			$this->option_name . '_one_signal_rest_api_key',
			__( 'OneSignal API Key', 'mpress' ),
			array( $this, $this->option_name . '_one_signal_rest_api_key_md' ),
			$this->plugin_name,
			$this->option_name . '_api',
			array( 'label_for' => $this->option_name . '_one_signal_rest_api_key_md' )
		);

		add_settings_field(
			$this->option_name . '_google_adsense_publisher_id',
			__( 'Google Adsense Publisher ID', 'mpress' ),
			array( $this, $this->option_name . '_google_adsense_publisher_id_md' ),
			$this->plugin_name,
			$this->option_name . '_api',
			array( 'label_for' => $this->option_name . '_google_adsense_publisher_id_md' )
		);

		register_setting( $this->plugin_name, $this->option_name . '_blog_logo', array( &$this, 'blog_logo_handle_file_upload' ) );

		register_setting(
			$this->plugin_name, $this->option_name . '_theme_color', array(
				'type'    => 'string',
				'default' => 'public',
			)
		);

		register_setting(
			$this->plugin_name, $this->option_name . '_button_color', array(
				'type'    => 'string',
				'default' => 'public',
			)
		);

		register_setting(
			$this->plugin_name, $this->option_name . '_app_name', array(
				'type'    => 'string',
				'default' => 'public',
			)
		);

		register_setting(
			$this->plugin_name, $this->option_name . '_app_slogan', array(
				'type'    => 'string',
				'default' => 'public',
			)
		);

		register_setting(
			$this->plugin_name, $this->option_name . '_mpress_app_id', array(
				'type'    => 'string',
				'default' => 'public',
			)
		);

		register_setting(
			$this->plugin_name, $this->option_name . '_one_signal_app_id', array(
				'type'    => 'string',
				'default' => 'public',
			)
		);

		register_setting(
			$this->plugin_name, $this->option_name . '_one_signal_rest_api_key', array(
				'type'    => 'string',
				'default' => 'public',
			)
		);

		register_setting(
			$this->plugin_name, $this->option_name . '_google_adsense_publisher_id', array(
				'type'    => 'string',
				'default' => 'public',
			)
		);
	}

	/**
	 * Render the text for the general section
	 *
	 * @since  1.0.0
	 */
	public function mpress_panel_general_md() {
		echo '<p>' . __( 'Please change the settings accordingly.', 'mpress' ) . '</p>';
	}

	/**
	 * Render the text for the general section
	 *
	 * @since  1.0.0
	 */
	public function mpress_panel_api_md() {
		echo '<p>' . __( 'Please change the API settings accordingly.', 'mpress' ) . '</p>';
	}

	/**
	 * Render the treshold day input for this plugin
	 *
	 * @since  1.0.0
	 */
	public function mpress_panel_blog_logo_md() { ?>
		<input type="file" name="mpress-app-logo" />
		<?php echo get_option( 'mpress-app-logo' ); ?>
					<?php
	}

	/**
	 * Render the treshold day input for this plugin
	 *
	 * @since  1.0.0
	 */
	public function mpress_panel_theme_color_md() {
		$theme_color = get_option( $this->option_name . '_theme_color' );
		echo '<input type="text" name="' . $this->option_name . '_theme_color' . '" id="' . $this->option_name . '_theme_color' . '" value="' . $theme_color . '"> ' . __( 'Insert Theme Color', 'mpress' );
	}

	/**
	 * Render the treshold day input for this plugin
	 *
	 * @since  1.0.0
	 */
	public function mpress_panel_button_color_md() {
		$button_color = get_option( $this->option_name . '_button_color' );
		echo '<input type="text" name="' . $this->option_name . '_button_color' . '" id="' . $this->option_name . '_button_color' . '" value="' . $button_color . '"> ' . __( 'Insert Button Color', 'mpress' );
	}

	/**
	 * Render the treshold day input for this plugin
	 *
	 * @since  1.0.0
	 */
	public function mpress_panel_app_name_md() {
		$app_name = get_option( $this->option_name . '_app_name' );
		echo '<input type="text" name="' . $this->option_name . '_app_name' . '" id="' . $this->option_name . '_app_name' . '" value="' . $app_name . '"> ' . __( 'Set Application Name', 'mpress' );
	}

	/**
	 * Render the treshold day input for this plugin
	 *
	 * @since  1.0.0
	 */
	public function mpress_panel_app_slogan_md() {
		$app_slogan = get_option( $this->option_name . '_app_slogan' );
		echo '<input type="text" name="' . $this->option_name . '_app_slogan' . '" id="' . $this->option_name . '_app_slogan' . '" value="' . $app_slogan . '"> ' . __( 'Set Application Slogan', 'mpress' );
	}

	/**
	 * Render the treshold day input for this plugin
	 *
	 * @since  1.0.0
	 */
	public function mpress_panel_mpress_app_id_md() {
		$mpress_app_id = get_option( $this->option_name . '_mpress_app_id' );
		echo '<input type="text" name="' . $this->option_name . '_mpress_app_id' . '" id="' . $this->option_name . '_mpress_app_id' . '" value="' . $mpress_app_id . '"> ' . __( 'Set MPress API ID', 'mpress' );
	}

	/**
	 * Render the treshold day input for this plugin
	 *
	 * @since  1.0.0
	 */
	public function mpress_panel_one_signal_app_id_md() {
		$one_signal_app_id = get_option( $this->option_name . '_one_signal_app_id' );
		echo '<input type="text" name="' . $this->option_name . '_one_signal_app_id' . '" id="' . $this->option_name . '_one_signal_app_id' . '" value="' . $one_signal_app_id . '"> ' . __( 'Set OneSignal API ID', 'mpress' );
	}

	/**
	 * Render the treshold day input for this plugin
	 *
	 * @since  1.0.0
	 */
	public function mpress_panel_one_signal_rest_api_key_md() {
		$one_signal_rest_api_key = get_option( $this->option_name . '_one_signal_rest_api_key' );
		echo '<input type="text" name="' . $this->option_name . '_one_signal_rest_api_key' . '" id="' . $this->option_name . '_one_signal_rest_api_key' . '" value="' . $one_signal_rest_api_key . '"> ' . __( 'Set OneSignal API Key', 'mpress' );
	}

	/**
	 * Render the treshold day input for this plugin
	 *
	 * @since  1.0.0
	 */
	public function mpress_panel_google_adsense_publisher_id_md() {
		$google_adsense_publisher_id = get_option( $this->option_name . '_google_adsense_publisher_id' );
		echo '<input type="text" name="' . $this->option_name . '_google_adsense_publisher_id' . '" id="' . $this->option_name . '_google_adsense_publisher_id' . '" value="' . $google_adsense_publisher_id . '" disabled> ' . __( 'Set Google Adsense Publisher ID', 'mpress' );
	}

	/**
	 * Sanitize the text position value before being saved to database
	 *
	 * @param  string $position $_POST value
	 * @since  1.0.0
	 * @return string           Sanitized value
	 */
	public function mpress_panel_sanitize_position( $position ) {
		if ( in_array( $position, array( 'before', 'after' ), true ) ) {
			return $position;
		}
	}
}
