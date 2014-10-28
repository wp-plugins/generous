<?php

/**
 * The public shortcodes functionality.
 *
 * Defines and sorts shortcodes.
 *
 * @since      0.1.0
 *
 * @package    WP_Generous
 * @subpackage WP_Generous/public
 * @author     Matthew Govaere <matthew@genero.us>
 */
class WP_Generous_Public_Shortcodes {

	/**
	 * Requests data from the Generous API.
	 *
	 * @since    0.1.0
	 * @access   private
	 *
	 * @var      WP_Generous_Api              $api        Maintains requests to the Generous API.
	 */
	private $api;

	/**
	 * Prepares and outputs data to html.
	 *
	 * @since    0.1.0
	 * @access   private
	 *
	 * @var      WP_Generous_Public_Output    $output     Maintains methods to convert data to html templates.
	 */
	private $output;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.1.0
	 *
	 * @param    array    $options    The settings of the plugin.
	 * @param    array    $api        Maintains all Generous API requests.
	 */
	public function __construct( $options, $api, $output ) {

		$this->api = $api;
		$this->output = $output;

	}

	/**
	 * Load the specified shortcode attributes.
	 *
	 * @since    0.1.0
	 *
	 * @param    array    $atts       Specified shortcode attributes.
	 *
	 * @return   string               The rendered html to output.
	 */
	public function load( $atts ) {

		$shortcodes = array(
			'store'      => array($this, 'store'),
			'categories' => array($this, 'categories'),
			'category'   => array($this, 'category'),
			'slider'     => array($this, 'slider'),
			'page'       => array($this, 'page'),
		);

		if ( isset( $atts ) && is_array( $atts ) ) {

			$is_assoc = (bool)count( array_filter( array_keys( $atts ), 'is_string' ) );

			if ( ! $is_assoc ) {
				foreach ( $atts as $key => $value) {
					foreach ( $shortcodes as $code => $func ) {
						if ( $value == $code ) {
							return $this->wrapper( $func, $atts );
						}
					}
				}
			} else {
				foreach ( $shortcodes as $code => $func ) {
					if ( isset( $atts[ $code ] ) ) {
						return $this->wrapper( $func, $atts );
					}
				}
			}

		} else {

			return '';
			
		}

	}

	/**
	 * Wraps the shortcode with any necessary elements, and checks for shortcodes.
	 *
	 * @since    0.1.0
	 * @access   private
	 *
	 * @return   string              The rendered html to output.
	 */
	private function wrapper( $func, $atts ) {

		$html = call_user_func_array( $func, $atts );

		return do_shortcode( $html );

	}

	/**
	 * Output store shortcode.
	 *
	 * @since    0.1.0
	 * @access   private
	 *
	 * @return   string              The rendered html to output.
	 */
	private function store($atts) {

		return $this->output->shortcode_store();

	}

	/**
	 * Output categories shortcode.
	 *
	 * @since    0.1.0
	 * @access   private
	 *
	 * @return   string              The rendered html to output.
	 */
	private function categories() {

		$data = $this->api->get_categories();

		if ( false !== $data ) {
			return $this->output->shortcode_categories( $data );
		} else {
			return '';
		}

	}

	/**
	 * Output category or slider shortcode.
	 *
	 * @since    0.1.0
	 * @access   private
	 *
	 * @param    string    $id       Specified category id.
	 *
	 * @return   string              The rendered html to output.
	 */
	private function page( $id ) {

		$data = $this->api->get_unknown( $id );

		if ( false !== $data ) {
			if ( isset( $data['sliders'] ) ) {
				return $this->output->shortcode_category( $data );
			} else if ( isset( $data['slider'] ) ) {
				return $this->output->slider( $data['slider'] );
			}
		}

	}

	/**
	 * Output category shortcode.
	 *
	 * @since    0.1.0
	 * @access   private
	 *
	 * @param    string    $id       Specified category id.
	 *
	 * @return   string              The rendered html to output.
	 */
	private function category( $id ) {

		if ( 'featured' === $id ) {
			$data = $this->api->get_store_default();
		} else {
			$data = $this->api->get_category( $id );
		}

		if ( false !== $data ) {
			return $this->output->shortcode_category( $data );  
		}

	}

	/**
	 * Output slider shortcode.
	 *
	 * @since    0.1.0
	 * @access   private
	 *
	 * @param    string    $id       Specified category id.
	 *
	 * @return   string              The rendered html to output.
	 */
	private function slider( $id ) {

		$data = $this->api->get_store_slider( $id );

		if ( false !== $data ) {
			return $this->output->slider( $data['slider'] );
		}   

	}

}
