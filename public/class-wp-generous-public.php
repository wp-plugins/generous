<?php

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and hooks.
 *
 * @since      0.1.0
 *
 * @package    WP_Generous
 * @subpackage WP_Generous/public
 * @author     Matthew Govaere <matthew@genero.us>
 */
class WP_Generous_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 *
	 * @var      string                           $name          The ID of this plugin.
	 */
	private $name;

	/**
	 * The version of this plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 *
	 * @var      string                           $version       The current version of this plugin.
	 */
	private $version;

	/**
	 * Contains the general settings for the plugin specified by the user.
	 *
	 * @since    0.1.0
	 * @access   private
	 *
	 * @var      array                            $options       The settings of the plugin.
	 */
	private $options;

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 *
	 * @var      WP_Generous_Loader               $loader        Maintains and registers all hooks for the plugin.
	 */
	private $loader;

	/**
	 * Requests data from the Generous API.
	 *
	 * @since    0.1.0
	 * @access   private
	 *
	 * @var      WP_Generous_Api                  $api           Maintains all Generous API requests.
	 */
	private $api;

	/**
	 * Prepares and outputs data to html.
	 *
	 * @since    0.1.0
	 * @access   private
	 *
	 * @var      WP_Generous_Public_Output        $output        Maintains methods to convert data to html templates.
	 */
	private $output;

	/**
	 * Responsible for maintaining callbacks and parameters for the shortcodes.
	 *
	 * @since    0.1.0
	 * @access   private
	 *
	 * @var      WP_Generous_Public_Shortcodes    $shortcodes    Maintains callbacks for the shortcodes.
	 */
	private $shortcodes;

	/**
	 * Saves and retrieves data, generally from the Api.
	 *
	 * @since    0.1.0
	 * @access   private
	 *
	 * @var      WP_Generous_Public_Data          $data          Saves and retrieves data.
	 */
	private $data;

	/**
	 * Runs phantom queries which generate content and post-like Wordpress data.
	 *
	 * @since    0.1.0
	 * @access   private
	 *
	 * @var      WP_Generous_Public_Query         $query         Maintains methods to create phantom Wordpress queries.
	 */
	private $query;

	/**
	 * Loads templates.
	 *
	 * @since    0.1.0
	 * @access   private
	 *
	 * @var      WP_Generous_Public_Templates      $templates     Loads user or default templates.
	 */
	private $templates;

	/**
	 * Initialize the class, set its properties, and load depenencies.
	 *
	 * @since    0.1.0
	 *
	 * @param    string                           $name          The name of the plugin.
	 * @param    string                           $version       The version of this plugin.
	 * @param    array                            $options       The settings of the plugin.
	 * @param    WP_Generous_Loader               $loader        Register all actions and filters for the plugin.
	 * @param    WP_Generous_Api                  $api           Maintains requests to the Generous Api.
	 */
	public function __construct( $name, $version, $options, $loader, $api ) {

		$this->name = $name;
		$this->version = $version;
		$this->options = $options;
		$this->loader = $loader;
		$this->api = $api;

		$this->load_dependencies();

	}

	/**
	 * Load the required dependencies for the public.
	 *
	 * Include the following files that make up the admin:
	 *
	 * - WP_Generous_Public_Shortcodes
	 * - WP_Generous_Public_Post
	 * - WP_Generous_Public_Posts
	 * - WP_Generous_Public_Templates
	 * - WP_Generous_Public_Output
	 * - WP_Generous_Public_Filters
	 * - WP_Generous_Public_Data
	 * - WP_Generous_Public_Query
	 * - Public functions
	 *
	 * Create an instance of output which will be used to generate (html) content.
	 * Create an instance of shortcodes which will be used for callbacks.
	 * Create an instance of data which will be used to retrieve saved Api data.
	 * Create an instance of query which will be used to create phantom Wordpress posts.
	 *
	 * Create an instance of posts which will be used to manage and run public functions.
	 * Set plugin options for posts.
	 *
	 * @since    0.1.0
	 * @access   private
	 */
	private function load_dependencies() {

		require_once plugin_dir_path( __FILE__ ) . 'class-wp-generous-public-shortcodes.php';
		require_once plugin_dir_path( __FILE__ ) . 'class-wp-generous-public-post.php';
		require_once plugin_dir_path( __FILE__ ) . 'class-wp-generous-public-posts.php';
		require_once plugin_dir_path( __FILE__ ) . 'class-wp-generous-public-templates.php';
		require_once plugin_dir_path( __FILE__ ) . 'class-wp-generous-public-output.php';
		require_once plugin_dir_path( __FILE__ ) . 'class-wp-generous-public-filters.php';
		require_once plugin_dir_path( __FILE__ ) . 'class-wp-generous-public-data.php';
		require_once plugin_dir_path( __FILE__ ) . 'class-wp-generous-public-query.php';
		require_once plugin_dir_path( __FILE__ ) . 'wp-generous-public-functions.php';

		$this->templates = new WP_Generous_Public_Templates();
		$this->output = new WP_Generous_Public_Output( $this->options, $this->templates );
		$this->shortcodes = new WP_Generous_Public_Shortcodes( $this->options, $this->api, $this->output );
		$this->data = new WP_Generous_Public_Data();
		$this->query = new WP_Generous_Public_Query( $this->output, $this->options );

		$posts = WP_Generous_Public_Posts::obtain();
		$posts->set_options( $this->options );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    0.1.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->name, plugin_dir_url( dirname( __FILE__ ) ) . 'assets/css/wp-generous.css', array(), NULL, 'all' );
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    0.1.0
	 */
	public function enqueue_scripts() {

		wp_register_script( $this->name, plugin_dir_url( dirname( __FILE__ ) ) . 'assets/js/wp-generous.js', array( 'jquery' ), NULL, false );
		wp_enqueue_script( $this->name );

		$protocol = is_ssl() ? 'https' : 'http';
		
		$url = "$protocol://js.genero.us/";
		$url_params = "";

		if ( $this->options['enable_cart'] ) {

			$url_params .= "?enableCart=true";

			if ( $this->options['cart_auto_open'] ) {
				$url_params .= "&cartAutoOpen=true";
			}

			if ( $this->options['cart_color_primary'] ) {
				$url_params .= "&cartColorPrimary={$this->options['cart_color_primary']}";
			}

			if ( $this->options['cart_color_secondary'] ) {
				$url_params .= "&cartColorSecondary={$this->options['cart_color_secondary']}";
			}

			if ( $this->options['cart_color_accent'] ) {
				$url_params .= "&cartColorAccent={$this->options['cart_color_accent']}";
			}

		}

		if ( $this->options['js_v1_disable_overlay'] ) {

			if ( substr( $url_params, 0, 1 ) !== "?" ) {
				$url_params .= "?";
			} else {
				$url_params .= "&";
			}

			$url_params .= "enableOverlay=false";

		}

		$url .= $url_params;

		wp_register_script( "{$this->name}-js", $url, array(), NULL, false );
		wp_enqueue_script( "{$this->name}-js" );

	}

	/**
	 * Register the shortcodes for the public-facing side of the site.
	 *
	 * @since    0.1.0
	 */
	public function add_shortcodes() {
		add_shortcode( 'generous', array( $this->shortcodes, 'load' ) ) ;
	}

	/**
	 * Register the rewrite rules for the public-facing side of the site.
	 *
	 * @since    0.1.0
	 */
	public function add_rewrite_rules() {

		add_rewrite_rule(
			'^' . $this->options['permalink'] . '/page/([^/]*)/?',
			'index.php?generous_category=featured&paged=$matches[1]',
			'top'
		);

		add_rewrite_rule(
			'^' . $this->options['permalink'] . '/([^/]*)/page/([^/]*)/?',
			'index.php?generous_category=$matches[1]&paged=$matches[2]',
			'top'
		);

		add_rewrite_rule(
			'^' . $this->options['permalink'] . '/([^/]*)/?',
			'index.php?generous_page=$matches[1]&generous_category=$matches[1]&generous_slider=$matches[1]',
			'top'
		);

		add_rewrite_rule(
			'^'  .$this->options['permalink'] . '/?',
			'index.php?generous_category=featured',
			'top'
		);

	}

	/**
	 * Register the rewrite tags for the public-facing side of the site.
	 *
	 * @since    0.1.0
	 */
	public function add_rewrite_tags() {
		$this->set_rewrite_tag( 'generous_page' );
		$this->set_rewrite_tag( 'generous_category' );
		$this->set_rewrite_tag( 'generous_slider' );
	}

	/**
	 * Register the taxonomies for the public-facing side of the site.
	 *
	 * @since    0.1.0
	 */
	public function add_taxonomies() {
		$this->register_taxonomy( 'generous_category', 'page' );
		$this->register_taxonomy( 'generous_slider', 'page' );
	}

	/**
	 * Register the rewrite endpoints for the public-facing side of the site.
	 *
	 * @since    0.1.0
	 */
	public function add_rewrite_endpoints() {
		add_rewrite_endpoint( $this->options['permalink'], EP_PERMALINK | EP_PAGES );
	}

	/**
	 * Register the custom templates for the public-facing side of the site.
	 *
	 * @since    0.1.0
	 *
	 * @param    array     $template   The original template.
	 *
	 * @return   string                The replaced (or original) template.
	 */
	public function add_custom_templates( $template ) {

		if ( $id = get_query_var( 'generous_page' ) ) {

			if( false !== $this->data->get( $id ) ) {

				$data = $this->data->get( $id );

				if ( isset( $data['sliders'] ) ) {
					$template = $this->templates->load('page-category');
				} else {
					$template = $this->templates->load('page-slider');
				}

			}

		} else if ( $id = get_query_var( 'generous_category' ) ) {

			if ( false !== $this->data->get( $id ) ) {
				if ( 'featured' === $id ) {
					$template = $this->templates->load('page-default');
				} else {
					$template = $this->templates->load('page-category');
				}
			}

		} else if ( $id = get_query_var( 'generous_slider' ) ) {

			if( false !== $this->data->get( $id ) ) {
				$template = $this->templates->load('page-slider');
			}

		} else if ( $this->is_default() ) {

			$template = $this->templates->load('page-default');

		}

		return $template;
		
	}

	/**
	 * Register custom pages for the public-facing side of the site.
	 *
	 * @since    0.1.0
	 *
	 * @param    array     $posts      The original Wordpress posts.
	 *
	 * @return   array                 The updated (or original) Wordpress posts.
	 */
	public function add_custom_page( $posts ) {

		if( count( $posts ) === 0 ) {

			// Check unknown query
			if ( $id = get_query_var( 'generous_page' ) ) {

				$query_var = 'generous_page';
				$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : false;

				$data = $this->get_data( $query_var, $id, $paged);

				return $this->query->run( $query_var, $id, $data );

			// Check category query
			} else if ( $id = get_query_var( 'generous_category' ) ) {

				$query_var = 'generous_category';
				$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : false;

				$data = $this->get_data( $query_var, $id, $paged);

				return $this->query->run( $query_var, $id, $data );

			// Check slider query
			} else if ( $id = get_query_var( 'generous_slider' ) ) {

				$query_var = 'generous_slider';

				$data = $this->get_data( $query_var, $id);

				return $this->query->run( $query_var, $id, $data );

			// Check default page
			} else if ( $this->is_default() ) {

				$id = 'featured';
				$query_var = 'generous_category';
				$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : false;

				$data = $this->get_data( $query_var, $id, $paged);

				return $this->query->run( $query_var, $id, $data );

			} else {

				// Not a Generous query

			}

		}

		return $posts;  

	}

	/**
	 * Checks for required plugin options, and retrieves and saves data from Api.
	 *
	 * @since    0.1.0
	 *
	 * @param    array     $query_var  The requested query variable.
	 * @param    string    $id         The value of the query variable.
	 * @param    int|bool  $paged      The requested page number.
	 *
	 * @return   array                 The retrieved data.
	 */
	public function get_data( $query_var, $id, $paged = false ) {

		if ( true === $this->has_required_options() ) {

			switch( $query_var ) {

				case 'generous_page':

					$data = $this->api->get_unknown( $id, $paged );

				break;

				case 'generous_category':

					if ( 'featured' === $id ) {
						$data = $this->api->get_store_default( $paged );
					} else {
						$data = $this->api->get_category( $id, $paged );
					}

				break;

				case 'generous_slider':

					$data = $this->api->get_unknown( $id );

				break;

				case 'default':

					$data = $this->api->get_store_default();

				break;
				
			}

			$this->data->add( $id, $data );

			return $this->data->get( $id );

		} else {

			return false;

		}

	}

	/**
	 * Register the filter to replace the spacing on titles for taxonomy requested pages.
	 *
	 * @since    0.1.0
	 *
	 * @param    string     $title     The original title.
	 * @param    string     $sep       The separator string.
	 * @param    string     $sep_loc   The location of the separator.
	 *
	 * @return   string                The replaced (or original) title.
	 */
	public function remove_tax_name_from_title( $title, $sep = '', $sep_loc = 'left' ) {

		if ( is_tax() ) {

			$term_title = single_term_title( '', false );

			if ( 'right' == $sep_loc ) {
				$title = $term_title . "$sep";
			} else {
				$title = "$sep" . $term_title;
			}

		}

		return $title;

	}

	/**
	 * Actually registers custom rewrite tag and permalink structures with Wordpress.
	 *
	 * @since    0.1.0
	 * @access   private
	 *
	 * @param    string     $tag      The tag to add.
	 */
	private function set_rewrite_tag( $tag ) {

		$regex_slug = '^[a-z0-9]+(?:-[a-z0-9]+)*$';

		add_rewrite_tag( "%{$tag}%", $regex_slug );
		add_permastruct( $tag, "/{$this->options['permalink']}/%{$tag}%" );

	}

	/**
	 * Actually registers taxonomy based on specified data  with Wordpress.
	 *
	 * @since    0.1.0
	 * @access   private
	 *
	 * @param    string     $slug     The taxonomy to add.
	 * @param    string     $type     The type of Wordpress post.
	 */
	private function register_taxonomy( $slug, $type ) {

		$labels = array(
			'name'              => '',
			'singular_name'     => ''
		);

		$args = array(
			'hierarchical'      => false,
			'show_ui'           => false,
			'show_admin_column' => false,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => $this->options['permalink'] ),
		);

		register_taxonomy( $slug, $type, $args );

	}

	/**
	 * Checks if the current page is the default page.
	 *
	 * @since    0.1.0
	 * @access   private
	 *
	 * @return   bool                 True if yes, false if no.
	 */
	private function is_default() {

		global $wp, $wp_query;

		$slug = $this->options['permalink'];

		$has_options = $this->has_required_options();
		$has_no_posts = count( $wp_query->posts == 0 );
		$is_request_slug = strtolower( $wp->request ) == $slug;

		$is_query_slug_page_id = ( isset( $wp->query_vars['page_id'] ) && $wp->query_vars['page_id'] == $slug );
		$is_query_slug_p = ( isset( $wp->query_vars['p'] ) && $wp->query_vars['p'] == $slug );
		$is_query_slug = ( $is_query_slug_page_id || $is_query_slug_p );

		$is_slug = ( $is_request_slug || $is_query_slug );

		return ( $has_options && $has_no_posts && $is_slug ) ? true : false;

	}

	/**
	 * Checks if the required options are set.
	 *
	 * @since    0.1.0
	 * @access   private
	 *
	 * @return   bool                 True if yes, false if no.
	 */
	private function has_required_options() {
		return ( isset( $this->options['username'], $this->options['permalink'] ) ) ? true : false;
	}

}
