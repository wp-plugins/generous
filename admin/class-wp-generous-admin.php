<?php

/**
 * The dashboard-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and hooks for styles/js and the menu.
 *
 * @since      0.1.0
 *
 * @package    WP_Generous
 * @subpackage WP_Generous/admin
 * @author     Matthew Govaere <matthew@genero.us>
 */
class WP_Generous_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string    $name    The ID of this plugin.
	 */
	private $name;

	/**
	 * The version of this plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Contains the general settings for the plugin specified by the user.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      array                         $options     The settings of the plugin.
	 */
	private $options;

	/**
	 * Requests data from the Generous API.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      WP_Generous_Api               $api         Maintains all Generous API requests.
	 */
	private $api;

	/**
	 * The settings are responsible for maintaining callbacks for the admin settings.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      WP_Generous_Admin_Settings    $settings    Maintains callbacks for the admin settings.
	 */
	private $settings;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.1.0
	 * @var      string    $name       The name of this plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $name, $version, $options, $api ) {

		$this->name = $name;
		$this->version = $version;
		$this->api = $api;
		$this->options = $options;

		$this->load_dependencies();

	}

	/**
	 * Load the required dependencies for the admin.
	 *
	 * Include the following files that make up the admin:
	 *
	 * - WP_Generous_Admin_Settings. Handles callbacks for the settings page.
	 *
	 * Create an instance of settings which will be used for callbacks.
	 *
	 * @since    0.1.0
	 * @access   private
	 */
	private function load_dependencies() {

		require_once plugin_dir_path( __FILE__ ) . 'class-wp-generous-admin-settings.php';

		$this->settings = new WP_Generous_Admin_Settings( $this->name, $this->version, $this->options, $this->api );

	}

	/**
	 * Register the stylesheets for the dashboard.
	 *
	 * @since    0.1.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->name, plugin_dir_url( __FILE__ ) . 'css/generous-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the dashboard.
	 *
	 * @since    0.1.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->name, plugin_dir_url( __FILE__ ) . 'js/generous-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Register the settings menu for the dashboard.
	 *
	 * @since    0.1.0
	 */
	public function add_settings_menu() {

		add_options_page(
			'Generous', 
			'Generous', 
			'manage_options', 
			$this->name, 
			array( $this->settings, 'output_page' )
		);

	}

	/**
	 * Register the default options settings for the dashboard.
	 *
	 * @since    0.1.0
	 */
	public function register_settings_default() {

		register_setting(
			$this->settings->option_group,
			$this->settings->option_group,
			array( $this->settings, 'sanitize' )
		);

		// Sections
		add_settings_section(
			'section_general',
			'General',
			'',
			$this->settings->option_group
		);

		add_settings_section(
			'section_sliders',
			'Slider',
			'',
			$this->settings->option_group
		);

		add_settings_section(
			'section_cart',
			'Cart',
			'',
			$this->settings->option_group
		);

		add_settings_section(
			'section_advanced',
			'Advanced',
			'',
			$this->settings->option_group
		);

		// General Settings
		add_settings_field(
			'username',
			'Username',
			array( $this->settings, 'output_input_username' ),
			$this->settings->option_group,
			'section_general'
		);

		add_settings_field(
			'permalink',
			'Permalink Slug',
			array( $this->settings, 'output_input_permalink' ),
			$this->settings->option_group,
			'section_general'
		);

		// Slider Settings
		add_settings_field(
			'enable_overlay',
			'Enable Overlay',
			array( $this->settings, 'output_input_enable_overlay' ),
			$this->settings->option_group,
			'section_sliders'
		);

		add_settings_field(
			'enable_load_more',
			'Enable Load More',
			array( $this->settings, 'output_input_enable_load_more' ),
			$this->settings->option_group,
			'section_sliders'
		);

		add_settings_field(
			'sliders_per_page',
			'Sliders Per Page',
			array( $this->settings, 'output_input_sliders_per_page' ),
			$this->settings->option_group,
			'section_sliders'
		);

		// Cart Settings
		add_settings_field(
			'enable_cart',
			'Enable Cart',
			array( $this->settings, 'output_input_enable_cart' ),
			$this->settings->option_group,
			'section_cart'
		);

		add_settings_field(
			'cart_auto_open',
			'Enable Auto Open',
			array( $this->settings, 'output_input_cart_auto_open' ),
			$this->settings->option_group,
			'section_cart'
		);

		add_settings_field(
			'cart_color_primary',
			'Primary Color',
			array( $this->settings, 'output_input_cart_color_primary' ),
			$this->settings->option_group,
			'section_cart'
		);

		add_settings_field(
			'cart_color_secondary',
			'Secondary Color',
			array( $this->settings, 'output_input_cart_color_secondary' ),
			$this->settings->option_group,
			'section_cart'
		);

		add_settings_field(
			'cart_color_accent',
			'Accent Color',
			array( $this->settings, 'output_input_cart_color_accent' ),
			$this->settings->option_group,
			'section_cart'
		);

		// Advanced Settings
		add_settings_field(
			'js_v1_disable_overlay',
			'Force v0 Slider Overlay',
			array( $this->settings, 'output_input_advanced_disable_overlay' ),
			$this->settings->option_group,
			'section_advanced'
		);

	}

}
