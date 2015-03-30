<?php

/**
 * The settings functionality of the plugin.
 *
 * Maintains general callbacks for the admin settings.
 *
 * @since      0.1.0
 *
 * @package    WP_Generous
 * @subpackage WP_Generous/admin
 * @author     Matthew Govaere <matthew@genero.us>
 */
class WP_Generous_Admin_Settings {

	/**
	 * The option group for the settings.
	 *
	 * @since    0.1.0
	 * @access   public
	 * @var      string                $option_group  The name of the option group.
	 */
	public $option_group = 'generous_settings';
	
	/**
	 * The ID of this plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string                $name          The ID of this plugin.
	 */
	private $name;

	/**
	 * The version of this plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string                $version       The current version of this plugin.
	 */
	private $version;

	/**
	 * Contains the general settings for the plugin specified by the user.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      array                 $options     The settings of the plugin.
	 */
	private $options;

	/**
	 * Requests data from the Generous API.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      WP_Generous_Api       $api           Maintains all Generous API requests.
	 */
	private $api;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.1.0
	 * @var      string    $name       The name of this plugin.
	 * @var      string    $version    The version of this plugin.
	 * @var      array     $version    The options of this plugin.
	 */
	public function __construct( $name, $version, $options, $api ) {

		$this->name = $name;
		$this->version = $version;
		$this->api = $api;
		$this->options = $options;

	}

	/**
	 * Output the html for the settings page.
	 *
	 * @since    0.1.0
	 */
	public function output_page() {

		$page = array(
			'option_group' => $this->option_group,
			'options' => $this->options
		);

		include plugin_dir_path( __FILE__ ) . 'partials/wp-generous-admin-display.php';

		// Hacky workaround to make sure rewrite rules are refreshed.
		if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] ) {
			flush_rewrite_rules();
		}

	}

	/**
	 * Output the input for username.
	 *
	 * @since    0.1.0
	 */
	public function output_input_username() {

		$value = '';

		if( isset( $this->options['username'] ) ) {
			$value = $this->options['username'];
		}

		echo "<input name=\"{$this->option_group}[username]\" size=\"20\" type=\"text\" value=\"{$value}\" /> ";
		echo "<span class=\"description\">Your Generous username.</span>";

	}

	/**
	 * Output the input for permalink.
	 *
	 * @since    0.1.0
	 */
	public function output_input_permalink() {

		echo "<input name=\"{$this->option_group}[permalink]\" size=\"20\" type=\"text\" value=\"{$this->options['permalink']}\" /> ";
		echo "<span class=\"description\">Entering 'store' translates to yoursite.com/store.</span>";

	}

	/**
	 * Output the input for enabling overlay.
	 *
	 * @since    0.1.0
	 */
	public function output_input_enable_overlay() {

		if( true === $this->options['enable_overlay'] ) {
			$value = 'checked ';
		} else {
			$value = '';
		}

		echo "<input name=\"{$this->option_group}[enable_overlay]\" type=\"checkbox\" value=\"true\" {$value}/> ";
		echo "<span class=\"description\">Load a slider overlay on click instead of loading a separate page.</span>";

	}

	/**
	 * Output the input for enabling load more.
	 *
	 * @since    0.1.0
	 */
	public function output_input_enable_load_more() {

		if( true === $this->options['enable_load_more'] ) {
			$value = 'checked ';
		} else {
			$value = '';
		}

		echo "<input name=\"{$this->option_group}[enable_load_more]\" type=\"checkbox\" value=\"true\" {$value}/> ";
		echo "<span class=\"description\">Replaces previous/next pagination with an ajax-based \"Load More\" button.</span>";

	}

	/**
	 * Output the input for sliders per page.
	 *
	 * @since    0.1.0
	 */
	public function output_input_sliders_per_page() {

		echo "<input name=\"{$this->option_group}[sliders_per_page]\" size=\"20\" type=\"text\" value=\"{$this->options['sliders_per_page']}\" /> ";
		echo "<span class=\"description\">Max. 50</span>";

	}

	/**
	 * Output the input for enabling cart.
	 *
	 * @since    0.1.3
	 */
	public function output_input_enable_cart() {

		if( true === $this->options['enable_cart'] ) {
			$value = 'checked ';
		} else {
			$value = '';
		}

		echo "<input name=\"{$this->option_group}[enable_cart]\" type=\"checkbox\" value=\"true\" {$value}/> ";
		echo "<span class=\"description\">Allow users to add sliders to a cart and checkout multiple purchases.</span>";

	}

	/**
	 * Output the input for automatically opening cart.
	 *
	 * @since    0.1.3
	 */
	public function output_input_cart_auto_open() {

		if( true === $this->options['cart_auto_open'] ) {
			$value = 'checked ';
		} else {
			$value = '';
		}

		echo "<input name=\"{$this->option_group}[cart_auto_open]\" type=\"checkbox\" value=\"true\" {$value}/> ";
		echo "<span class=\"description\">Automatically open the cart when a slider is added.</span>";

	}

	/**
	 * Output the input for cart primary color.
	 *
	 * @since    0.1.3
	 */
	public function output_input_cart_color_primary() {

		$value = '';

		if( isset( $this->options['cart_color_primary'] ) ) {
			$value = $this->options['cart_color_primary'];
		}

		echo "<input name=\"{$this->option_group}[cart_color_primary]\" size=\"20\" type=\"text\" value=\"{$value}\" /> ";
		echo "<span class=\"description\">Color hex code used for background elements.</span>";

	}

	/**
	 * Output the input for cart secondary color.
	 *
	 * @since    0.1.3
	 */
	public function output_input_cart_color_secondary() {

		$value = '';

		if( isset( $this->options['cart_color_secondary'] ) ) {
			$value = $this->options['cart_color_secondary'];
		}

		echo "<input name=\"{$this->option_group}[cart_color_secondary]\" size=\"20\" type=\"text\" value=\"{$value}\" /> ";
		echo "<span class=\"description\">Color hex code used for foreground and text elements.</span>";

	}

	/**
	 * Output the input for cart accent color.
	 *
	 * @since    0.1.3
	 */
	public function output_input_cart_color_accent() {

		$value = '';

		if( isset( $this->options['cart_color_accent'] ) ) {
			$value = $this->options['cart_color_accent'];
		}

		echo "<input name=\"{$this->option_group}[cart_color_accent]\" size=\"20\" type=\"text\" value=\"{$value}\" /> ";
		echo "<span class=\"description\">Color hex code used for highlighting elements.</span>";

	}

	/**
	 * Output the input for cart secondary color.
	 *
	 * @since    0.1.3
	 */
	public function output_input_advanced_disable_overlay() {

		if( true === $this->options['js_v1_disable_overlay'] ) {
			$value = 'checked ';
		} else {
			$value = '';
		}

		echo "<input name=\"{$this->option_group}[js_v1_disable_overlay]\" type=\"checkbox\" value=\"true\" {$value}/> ";
		echo "<span class=\"description\">Disables v1 slider overlay.</span>";

	}

	/**
	 * Sanitize and validate fields.
	 *
	 * @since    0.1.0
	 */
	public function sanitize( $input ) {

		$results = array();

		if ( isset( $input['username'] ) ) {

			if ( $input['username'] !== '' ) {

				$data = $this->api->get_account( $input['username'] );

				if ( false !== $data ) {

					$results['username'] = $input['username'];

					if ( isset( $data['name'] ) ) {
						$results['title'] = $data['name'];
					} else if ( isset( $data['title'] ) ) {
						$results['title'] = $data['title'];
					}

				}

			}

		}

		if ( isset( $input['permalink'] ) ) {
			$results['permalink'] = $input['permalink'];
		}

		if ( isset( $input['enable_overlay'] ) ) {
			$results['enable_overlay'] = true;
		} else {
			$results['enable_overlay'] = false;
		}

		if ( isset( $input['sliders_per_page'] ) ) {
			$results['sliders_per_page'] = ( $input['sliders_per_page'] <= 50 ) ? $input['sliders_per_page'] : 50;
		}

		if ( isset( $input['enable_load_more'] ) ) {
			$results['enable_load_more'] = true;
		} else {
			$results['enable_load_more'] = false;
		}

		if ( isset( $input['enable_cart'] ) ) {
			$results['enable_cart'] = true;
		} else {
			$results['enable_cart'] = false;
		}

		if ( isset( $input['cart_auto_open'] ) ) {
			$results['cart_auto_open'] = true;
		} else {
			$results['cart_auto_open'] = false;
		}

		if ( isset( $input['cart_color_primary'] ) ) {
			$results['cart_color_primary'] = str_replace(array('#', ' '), '', $input['cart_color_primary']);
		}

		if ( isset( $input['cart_color_secondary'] ) ) {
			$results['cart_color_secondary'] = str_replace(array('#', ' '), '', $input['cart_color_secondary']);
		}

		if ( isset( $input['cart_color_accent'] ) ) {
			$results['cart_color_accent'] = str_replace(array('#', ' '), '', $input['cart_color_accent']);
		}

		if ( isset( $input['js_v1_disable_overlay'] ) ) {
			$results['js_v1_disable_overlay'] = true;
		} else {
			$results['js_v1_disable_overlay'] = false;
		}

		return $results;

	}
}
