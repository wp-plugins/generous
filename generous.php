<?php
/**
 * Plugin Name:       Generous
 * Plugin URI:        https://github.com/generous/generous-wordpress
 * Description:       The official Generous plugin that allows you to easily generate a store.
 * Version:           0.1.3
 * Author:            Generous
 * Author URI:        https://genero.us
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       generous
 * Domain Path:       /languages
 *
 * @since             0.1.0
 *
 * @package           WP_Generous
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The core WP Generous class.
 *
 * @since      0.1.0
 *
 * @package    WP_Generous
 * @author     Matthew Govaere <matthew@genero.us>
 */
class WP_Generous {

	/**
	 * The current version of the plugin.
	 *
	 * @since    0.1.0
	 * @access   protected
	 *
	 * @var      string                $version         The current version of the plugin.
	 */
	protected $version = '0.1.3';

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    0.1.0
	 * @access   protected
	 * @var      string                $WP_Generous     The string used to uniquely identify this plugin.
	 */
	protected $WP_Generous = 'generous';

	/**
	 * The id of the options which contains the plugin settings.
	 *
	 * @since    0.1.0
	 * @access   protected
	 *
	 * @var      string                $options_id      The id of the options.
	 */
	protected $options_id = 'generous_settings';

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    0.1.0
	 * @access   protected
	 *
	 * @var      WP_Generous_Loader    $loader          Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * Requests data from the Generous API.
	 *
	 * @since    0.1.0
	 * @access   protected
	 *
	 * @var      WP_Generous_Api       $api             Maintains all Generous API requests.
	 */
	protected $api;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the Dashboard and
	 * the public-facing side of the site.
	 *
	 * @since    0.1.0
	 */
	public function __construct() {
		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->run();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - WP_Generous_Loader. Orchestrates the hooks of the plugin.
	 * - WP_Generous_i18n. Defines internationalization functionality.
	 * - WP_Generous_Api. Sets endpoints for Generous API requests.
	 * - WP_Generous_Formatter. Formatting methods.
	 * - WP_Generous_Admin. Defines all hooks for the dashboard.
	 * - WP_Generous_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 * Create an instance of the api which will be used for API requests.
	 *
	 * @since    0.1.0
	 * @access   private
	 */
	private function load_dependencies() {

		// Load Generous SDK
		if ( ! class_exists( 'Generous' ) ) {
			require_once plugin_dir_path( __FILE__ ) . 'generous-sdk-php/src/Generous.php';
		}

		// Load initial dependencies
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-generous-activator.php';
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-generous-deactivator.php';

		register_activation_hook( __FILE__, array( 'WP_Generous_Activator', 'activate' ) );
		register_deactivation_hook( __FILE__, array( 'WP_Generous_Deactivator', 'deactivate' ) );

		// Load additional dependencies
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-generous-loader.php';
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-generous-i18n.php';
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-generous-api.php';
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-generous-formatter.php';
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-generous-currency.php';
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-generous-link.php';
		require_once plugin_dir_path( __FILE__ ) . 'admin/class-wp-generous-admin.php';
		require_once plugin_dir_path( __FILE__ ) . 'public/class-wp-generous-public.php';

		$this->loader = new WP_Generous_Loader();
		$this->api = WP_Generous_Api::obtain( $this->get_options() );

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the WP_Generous_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    0.1.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new WP_Generous_i18n();
		$plugin_i18n->set_domain( $this->get_Generous() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the dashboard functionality
	 * of the plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new WP_Generous_Admin( $this->get_Generous(), $this->get_version(), $this->get_options(), $this->api );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_settings_menu' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_settings_default' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new WP_Generous_Public( $this->get_Generous(), $this->get_version(), $this->get_options(), $this->loader, $this->api );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'init', $plugin_public, 'add_shortcodes' );
		$this->loader->add_action( 'init', $plugin_public, 'add_rewrite_rules' );
		$this->loader->add_action( 'init', $plugin_public, 'add_rewrite_tags' );
		$this->loader->add_action( 'init', $plugin_public, 'add_taxonomies' );
		$this->loader->add_action( 'init', $plugin_public, 'add_rewrite_endpoints' );
		$this->loader->add_action( 'template_include', $plugin_public, 'add_custom_templates' );
		$this->loader->add_filter( 'the_posts', $plugin_public, 'add_custom_page');
		$this->loader->add_filter( 'wp_title', $plugin_public, 'remove_tax_name_from_title');

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    0.1.0
	 */
	private function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     0.1.0
	 *
	 * @return    string    The name of the plugin.
	 */
	public function get_Generous() {
		return $this->WP_Generous;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     0.1.0
	 *
	 * @return    WP_Generous_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     0.1.0
	 *
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Retrieve the options of the plugin.
	 *
	 * @since     0.1.0
	 *
	 * @return    array     The options of the plugin.
	 */
	public function get_options() {

		$defaults = array(
			'permalink' => 'store',
			'enable_overlay' => true,
			'sliders_per_page' => 20,
			'enable_load_more' => false
		);

		return wp_parse_args( get_option( $this->options_id ), $defaults );
	}

}

/**
 * Begins execution of the plugin.
 *
 * @since    0.1.0
 */
function wp_generous_init() {
	global $wp_plugin_generous;
	$wp_plugin_generous = new WP_Generous();
}

wp_generous_init();