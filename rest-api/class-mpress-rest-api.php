<?php

/**
 * @Author: Debabrata Karfa
 * @Date:   2018-04-28 15:32:39
 * @Last Modified by:   Debabrata Karfa
 * @Last Modified time: 2018-06-27 09:50:15
 */

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.mpress.app
 * @since      1.0.0
 *
 * @package    Mpress
 * @subpackage Mpress/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Mpress
 * @subpackage Mpress/public
 * @author     Debabrata Karfa <debabrata.karfa@ctrl.biz>
 */

if ( ! defined( 'ABSPATH' ) ) {
	 exit; // Exit if accessed directly
}

class Mpress_RestAPI extends WP_REST_Controller {

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

	private $posts_per_page = 5;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		/**
		 * Register the routes for the objects of the controller.
		 */
		add_action( 'rest_api_init', array( &$this, 'mpress_register_api_endpoints' ) );

	}

	/**
	 * MPress :: Register custom route
	 *
	 * @method      wpc_register_wp_api_endpoints
	 *
	 * @return      [type] [description]
	 *
	 * @author      dkarfa <Debabrata Karfa>
	 * @since       1.0.0
	 */
	public function mpress_register_api_endpoints() {

		/**
		 * Register route for Config API
		 */
		register_rest_route(
			MPRESS_API_NAMESPACE, '/config', array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( &$this, 'mpress_config_callback' ),
				'permission_callback' => array( $this, 'get_public_permissions_check' ),
			)
		);

		/**
		 * Register route for List of Content [List]
		 */
		register_rest_route(
			MPRESS_API_NAMESPACE, '/content/page/(?P<page>[1-9]{1,6})', array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( &$this, 'mpress_get_list_of_content_callback' ),
				'args'                => array(
					'page' => array(
						'required' => true,
					),
				),
				'permission_callback' => array( $this, 'get_public_permissions_check' ),
			)
		);

		/**
		 * Register route for Content [Single]
		 */
		register_rest_route(
			MPRESS_API_NAMESPACE, '/content/(?P<id>[1-9]{1,6})', array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( &$this, 'mpress_get_content_callback' ),
				'args'                => array(
					'id' => array(
						'required' => true,
					),
				),
				'permission_callback' => array( $this, 'get_public_permissions_check' ),
			)
		);

		/**
		 * Register route for Related Content [List - Maximum 5 Related Articles]
		 */
		register_rest_route(
			MPRESS_API_NAMESPACE, '/content/(?P<id>\d+)/related', array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( &$this, 'mpress_content_related_callback' ),
				'args'                => array(
					'id' => array(
						'required' => true,
					),
				),
				'permission_callback' => array( $this, 'get_public_permissions_check' ),
			)
		);

		/**
		 * Register route for Category [List]
		 */
		register_rest_route(
			MPRESS_API_NAMESPACE, '/category', array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( &$this, 'mpress_category_callback' ),
				'permission_callback' => array( $this, 'get_public_permissions_check' ),
			)
		);

		/**
		 * Register route for Category's Content [List]
		 */
		register_rest_route(
			MPRESS_API_NAMESPACE, '/category/(?P<id>\d+)/content/(?P<page>[1-9]{1,3})', array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( &$this, 'mpress_category_content_callback' ),
				'args'                => array(
					'id'   => array(
						'required' => true,
					),
					'page' => array(
						'required' => true,
					),
				),
				'permission_callback' => array( $this, 'get_public_permissions_check' ),
			)
		);

		/**
		 * Register route for Author details [Single]
		 */
		register_rest_route(
			MPRESS_API_NAMESPACE, '/author/(?P<id>\d+)', [
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( &$this, 'mpress_author_callback' ),
				'permission_callback' => array( $this, 'get_public_permissions_check' ),
				'args'                => array(
					'id' => array(
						'required' => true,
					),
				),
			]
		);

		/**
		 * Register route for Author publish content [List]
		 */
		register_rest_route(
			MPRESS_API_NAMESPACE, '/author/(?P<id>\d+)/content/(?P<page>[1-9]{1,3})', array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( &$this, 'mpress_author_content_callback' ),
				'args'                => array(
					'id'   => array(
						'required' => true,
					),
					'page' => array(
						'required' => true,
					),
				),
				'permission_callback' => array( $this, 'get_public_permissions_check' ),
			)
		);

	}

	/**
	 * MPress :: Get configuration/setting detail
	 *
	 * @method      mpress_config_callback
	 *
	 * @return      array   Return JSON response for MPress Config file
	 *
	 * @author      dkarfa <Debabrata Karfa>
	 * @since       1.0.0
	 */
	public function mpress_config_callback() {

		$config_data['blog_url']                    = get_site_url();
		$config_data['blog_logo']                   = get_site_url();
		$config_data['mpress_api']                  = get_option( $this->option_name . '_mpress_app_id' );
		$config_data['theme_color']                 = get_option( $this->option_name . '_theme_color' );
		$config_data['button_color']                = get_option( $this->option_name . '_button_color' );
		$config_data['app_name']                    = get_option( $this->option_name . '_app_name' );
		$config_data['app_slogan']                  = get_option( $this->option_name . '_app_slogan' );
		$config_data['one_signal_app_id']           = get_option( $this->option_name . '_one_signal_app_id' );
		$config_data['one_signal_rest_api_key']     = get_option( $this->option_name . '_one_signal_rest_api_key' );
		$config_data['google_adsense_publisher_id'] = get_option( $this->option_name . '_google_adsense_publisher_id' );
		$config_data['response_status']             = 'success';
		$config_data['response_message']            = 'Website Config API response working';

		$response = new WP_REST_Response( $config_data );
		$response->header( 'X-Mpress-Activate', true );
		$response->set_status( 200 );
		return $response;

	}

	/**
	 * MPress :: Get list of contents
	 *
	 * @method      mpress_get_list_of_content_callback
	 *
	 * @return      array   Return list of 10 content
	 *
	 * @author      dkarfa <Debabrata Karfa>
	 * @since       1.0.0
	 */
	public function mpress_get_list_of_content_callback( $request_data ) {

		$paged = $request_data['page'] ? $request_data['page'] : 1;

		$posts = get_posts(
			array(
				'posts_per_page' => $this->posts_per_page,
				'post_type'      => 'post',
				'post_status'    => 'publish',
				'paged'          => $paged,
			)
		);

		$count_posts    = wp_count_posts( 'post' );
		$mp_total_pages = round( $count_posts->publish / $this->posts_per_page );

		if ( $posts ) {
			$content = array();
			foreach ( $posts as $post ) :
				setup_postdata( $post );

				// Get Categories
				$post_categories = wp_get_post_categories( $post->ID );
				$cats            = array();
				foreach ( $post_categories as $c ) {
					$cat    = get_category( $c );
					$cats[] = array(
						'name' => $cat->name,
						'slug' => $cat->slug,
					);
				}

				$content_data['ID']           = $post->ID;
				$content_data['title']        = $post->post_title;
				$content_data['date']         = $post->post_date;
				$content_data['date_gmt']     = $post->post_date_gmt;
				$content_data['content']      = $post->post_content;
				$content_data['excerpt']      = $post->post_excerpt;
				$content_data['categories']   = $cats;
				$content_data['thumbnail']    = esc_url( get_the_post_thumbnail_url( $post->ID, 'full' ) );
				$content_data['author']       = $post->post_author;
				$content_data['author_name']  = get_the_author_meta( 'display_name', $post->post_author );
				$content_data['author_image'] = esc_url( get_avatar_url( $post->post_author ) );
				$content_data['url']          = esc_url( get_permalink( $post->ID ) );
				$content[]                    = $content_data;

			endforeach;
			wp_reset_postdata();

			$response = new WP_REST_Response( $content );
			$response->header( 'X-MP-Total-Count', $count_posts->publish );
			$response->header( 'X-MP-Total-Page', $mp_total_pages );
			$response->set_status( 200 );
			return $response;

		} else {

			$content_data['response_status']  = 'fail';
			$content_data['response_message'] = 'No Content available';

			$response = new WP_REST_Response( $content_data );
			$response->set_status( 404 );
			return $response;

		}

	}

	/**
	 * Get single post ID content
	 *
	 * @method   mpress_get_content_callback
	 *
	 * @param    array $request_data Data of Post ID
	 *
	 * @return   array               Content data
	 *
	 * @author  dkarfa <Debabrata Karfa>
	 * @since   1.0.0
	 */
	public function mpress_get_content_callback( $request_data ) {
		if ( $request_data['id'] ) {
			$post = get_post( $request_data['id'] );

			if ( $post ) {
				$content = array();

				// Get Categories
				$post_categories = wp_get_post_categories( $post->ID );

				$cats = array();
				foreach ( $post_categories as $c ) {
					$cat    = get_category( $c );
					$cats[] = array(
						'name' => $cat->name,
						'slug' => $cat->slug,
					);
				}

				$content_data['ID']           = $post->ID;
				$content_data['title']        = $post->post_title;
				$content_data['date']         = $post->post_date;
				$content_data['date_gmt']     = $post->post_date_gmt;
				$content_data['content']      = $post->post_content;
				$content_data['excerpt']      = $post->post_excerpt;
				$content_data['categories']   = $cats;
				$content_data['thumbnail']    = esc_url( get_the_post_thumbnail_url( $post->ID, 'full' ) );
				$content_data['author']       = $post->post_author;
				$content_data['author_name']  = get_the_author_meta( 'display_name', $post->post_author );
				$content_data['author_image'] = esc_url( get_avatar_url( $post->post_author ) );
				$content_data['url']          = esc_url( get_permalink( $post->ID ) );
				$content[]                    = $content_data;

				wp_reset_postdata();

				$response = new WP_REST_Response( $content );
				$response->set_status( 200 );
				return $response;

			}
		} else {

			$content_data['response_status']  = 'fail';
			$content_data['response_message'] = 'No Related Content available';

			$response = new WP_REST_Response( $content_data );
			$response->set_status( 404 );
			return $response;

		}
	}
	/**
	 * MPress :: Get list of contents related to present content
	 *
	 * @method      mpress_content_related_callback
	 *
	 * @param       array $request_data   Particular content ID
	 * @return      array                 Return list of 5 contents which is related to particular content
	 *
	 * @author      dkarfa <Debabrata Karfa>
	 * @since       1.0.0
	 */
	public function mpress_content_related_callback( $request_data ) {

		$categories = get_the_category( $request_data['id'] );

		$list_of_categories = array();
		$cats               = array();

		foreach ( $categories as $category ) {
			$list_of_categories[] = $category->term_id;
			$cats[]               = array(
				'name' => $category->name,
				'slug' => $category->slug,
			);
		}

		$args = new WP_Query(
			array(
				'posts_per_page' => 5,
				'post_type'      => 'post',
				'post_status'    => 'publish',
				'exclude'        => $request_data['id'],
				'category__and'  => $list_of_categories,
			)
		);

		$related_posts = new WP_Query( $args );
		if ( $related_posts->have_posts() ) {
			while ( $related_posts->have_posts() ) :
				$related_posts->the_post();
					$content_data['ID']           = $related_posts->post->ID;
					$content_data['title']        = $related_posts->post->post_title;
					$content_data['date']         = $related_posts->post->post_date;
					$content_data['date_gmt']     = $related_posts->post->post_date_gmt;
					$content_data['content']      = $related_posts->post->post_content;
					$content_data['excerpt']      = $related_posts->post->post_excerpt;
					$content_data['author']       = $related_posts->post->post_author;
					$content_data['author_name']  = get_the_author_meta( 'display_name', $related_posts->post->post_author );
					$content_data['author_image'] = esc_url( get_avatar_url( $related_posts->post->post_author ) );
					$content_data['url']          = esc_url( get_permalink( $related_posts->post->ID ) );
					$content_data['thumbnail']    = esc_url( get_the_post_thumbnail_url( $related_posts->post->ID, 'full' ) );

					$content_data['categories'] = $cats;
					$content[]                  = $content_data;
			endwhile;

			$response = new WP_REST_Response( $content );
			$response->set_status( 200 );
			return $response;

		} else {

			$content_data['response_status']  = 'fail';
			$content_data['response_message'] = 'No Related Content available';

			$response = new WP_REST_Response( $content_data );
			$response->set_status( 404 );
			return $response;

		}
	}

	/**
	 * MPress :: Get list of categories from publish post's content
	 *
	 * @method      mpress_category_callback
	 *
	 * @return      array   Return list of categories
	 *
	 * @author      dkarfa <Debabrata Karfa>
	 * @since       1.0.0
	 */
	public function mpress_category_callback() {

		$categories = get_categories(
			array(
				'orderby' => 'name',
				'order'   => 'ASC',
			)
		);

		if ( $categories ) {
			$content = array();

			foreach ( $categories as $category ) {
				$category_data['ID']                 = $category->cat_ID;
				$category_data['name']               = $category->cat_name;
				$category_data['description']        = $category->category_description;
				$category_data['slug']               = $category->category_nicename;
				$category_data['parent_category_id'] = $category->category_parent;
				$category_data['post_count']         = $category->category_count;
				$category_data['taxonomy']           = $category->taxonomy;
				$content[]                           = $category_data;
			}

			$response = new WP_REST_Response( $content );
			$response->set_status( 200 );
			return $response;

		} else {

			$content_data['response_status']  = 'fail';
			$content_data['response_message'] = 'No Category is available';

			$response = new WP_REST_Response( $content_data );
			$response->set_status( 404 );
			return $response;

		}

	}

	/**
	 * MPress :: Get list of contents from particular category
	 *
	 * @method      mpress_category_content_callback
	 *
	 * @return      array   Return list of category's content
	 *
	 * @author      dkarfa <Debabrata Karfa>
	 * @since       1.0.0
	 */
	public function mpress_category_content_callback( $request_data ) {

		$paged = $request_data['page'] ? $request_data['page'] : 1;

		$posts = get_posts(
			array(
				'posts_per_page' => $this->posts_per_page,
				'category'       => $request_data['id'],
				'post_type'      => 'post',
				'post_status'    => 'publish',
				'paged'          => $paged,
			)
		);

		$count_posts    = get_term( $request_data['id'] );
		$mp_total_pages = round( $count_posts->count / $this->posts_per_page );

		if ( $posts ) {
			$content = array();
			foreach ( $posts as $post ) :
				setup_postdata( $post );

				// Get Categories
				$post_categories = wp_get_post_categories( $post->ID );
				$cats            = array();
				foreach ( $post_categories as $c ) {
					$cat    = get_category( $c );
					$cats[] = array(
						'name' => $cat->name,
						'slug' => $cat->slug,
					);
				}

				$content_data['ID']           = $post->ID;
				$content_data['title']        = $post->post_title;
				$content_data['date']         = $post->post_date;
				$content_data['date_gmt']     = $post->post_date_gmt;
				$content_data['content']      = $post->post_content;
				$content_data['excerpt']      = $post->post_excerpt;
				$content_data['categories']   = $cats;
				$content_data['thumbnail']    = esc_url( get_the_post_thumbnail_url( $post->ID, 'full' ) );
				$content_data['author']       = $post->post_author;
				$content_data['author_name']  = get_the_author_meta( 'display_name', $post->post_author );
				$content_data['author_image'] = esc_url( get_avatar_url( $post->post_author ) );
				$content_data['url']          = esc_url( get_permalink( $post->ID ) );
				$content[]                    = $content_data;
			endforeach;
			wp_reset_postdata();

			$response = new WP_REST_Response( $content );
			$response->header( 'X-MP-Total-Count', $count_posts->count );
			$response->header( 'X-MP-Total-Page', $mp_total_pages );
			$response->set_status( 200 );
			return $response;

		} else {

			$content_data['response_status']  = 'fail';
			$content_data['response_message'] = 'No Related Content available';

			$response = new WP_REST_Response( $content_data );
			$response->set_status( 404 );
			return $response;

		}
	}

	/**
	 * MPress :: Get datails of particular author
	 *
	 * @method      mpress_author_callback
	 *
	 * @return      array   Return details of particular Author's
	 *
	 * @author      dkarfa <Debabrata Karfa>
	 * @since       1.0.0
	 */
	public function mpress_author_callback( $request_data ) {

		$user_info = get_userdata( $request_data['id'] );

		if ( $user_info ) {
			$user_data['ID']              = $user_info->ID;
			$user_data['first_name']      = $user_info->first_name;
			$user_data['last_name']       = $user_info->last_name;
			$user_data['display_name']    = $user_info->display_name;
			$user_data['user_email']      = $user_info->user_email;
			$user_data['description']     = $user_info->description;
			$user_data['author_image']    = esc_url( get_avatar_url( $user_info->ID ) );
			$user_data['user_url']        = esc_url( get_author_posts_url( $user_info->ID ) );
			$user_data['user_registered'] = $user_info->user_registered;

			$response = new WP_REST_Response( $user_data );
			$response->set_status( 200 );
			return $response;
		} else {

			$user_data['response_status']  = 'fail';
			$user_data['response_message'] = 'User info not available';

			$response = new WP_REST_Response( $user_data );
			$response->set_status( 404 );
			return $response;

		}
	}

	/**
	 * MPress :: Get list of contents from particular author
	 *
	 * @method      mpress_author_content_callback
	 *
	 * @return      array   Return list of content for particular Author's
	 *
	 * @author      dkarfa <Debabrata Karfa>
	 * @since       1.0.0
	 */
	public function mpress_author_content_callback( $request_data ) {

		$paged = $request_data['page'] ? $request_data['page'] : 1;

		$posts = get_posts(
			array(
				'posts_per_page' => $this->posts_per_page,
				'post_type'      => 'post',
				'author'         => $request_data['id'],
				'post_status'    => 'publish',
				'paged'          => $paged,
			)
		);

		$count_posts    = count_user_posts( $request_data['id'] );
		$mp_total_pages = round( $count_posts / $this->posts_per_page );

		if ( $posts ) {
			$content = array();
			foreach ( $posts as $post ) :
				setup_postdata( $post );

				// Get Categories
				$post_categories = wp_get_post_categories( $post->ID );
				$cats            = array();
				foreach ( $post_categories as $c ) {
					$cat    = get_category( $c );
					$cats[] = array(
						'name' => $cat->name,
						'slug' => $cat->slug,
					);
				}

				$content_data['ID']           = $post->ID;
				$content_data['title']        = $post->post_title;
				$content_data['date']         = $post->post_date;
				$content_data['date_gmt']     = $post->post_date_gmt;
				$content_data['content']      = $post->post_content;
				$content_data['excerpt']      = $post->post_excerpt;
				$content_data['categories']   = $cats;
				$content_data['thumbnail']    = esc_url( get_the_post_thumbnail_url( $post->ID, 'full' ) );
				$content_data['author']       = $post->post_author;
				$content_data['author_name']  = get_the_author_meta( 'display_name', $post->post_author );
				$content_data['author_image'] = esc_url( get_avatar_url( $post->post_author ) );
				$content_data['url']          = esc_url( get_permalink( $post->ID ) );
				$content[]                    = $content_data;
			endforeach;
			wp_reset_postdata();

			$response = new WP_REST_Response( $content );
			$response->set_status( 200 );
			$response->header( 'X-MP-Total-Count', $count_posts );
			$response->header( 'X-MP-Total-Page', $mp_total_pages );
			return $response;

		} else {

			$content_data['response_status']  = 'fail';
			$content_data['response_message'] = 'No Related Content available';

			$response = new WP_REST_Response( $content_data );
			$response->set_status( 404 );
			return $response;

		}

	}

	/**
	 * Check if a given request has access to get content data
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|bool
	 */
	public function get_public_permissions_check( $request ) {
		return true;
	}

	/**
	 * Return post count per category
	 *
	 * @method   wp_get_cat_postcount
	 *
	 * @param    int $id Term or Category ID.
	 *
	 * @return   int     Number of posts.
	 *
	 * @author   dkarfa <Debabrata Karfa>
	 * @since    1.0.0
	 *
	 * @modified 2018-06-17 19:39
	 */
	public function wp_get_cat_postcount( $id ) {

		$cat      = get_category( $id );
		$count    = (int) $cat->count;
		$taxonomy = 'category';

		$args = array(
			'child_of' => $id,
		);

		$tax_terms = get_terms( $taxonomy, $args );

		foreach ( $tax_terms as $tax_term ) {
			$count += $tax_term->count;
		}

		return $count;

	}

}

