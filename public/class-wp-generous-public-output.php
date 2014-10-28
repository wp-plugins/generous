<?php

/**
 * Maintains methods to convert data to public html templates.
 *
 * @since      0.1.0
 *
 * @package    WP_Generous
 * @subpackage WP_Generous/public
 * @author     Matthew Govaere <matthew@genero.us>
 */
class WP_Generous_Public_Output {

	/**
	 * The permalink endpoint specified within the plugin options.
	 *
	 * @since    0.1.0
	 * @access   private
	 *
	 * @var      string                               $permalink         The root endpoint of the permalink structure.
	 */
	private static $permalink;

	/**
	 * Converts filters to specified data.
	 *
	 * @since    0.1.0
	 * @access   private
	 *
	 * @var      WP_Generous_Public_Output_Filters    $filters           Maintains methods to convert filters to data.
	 */
	private static $filters;

	/**
	 * Loads templates.
	 *
	 * @since    0.1.0
	 * @access   private
	 *
	 * @var      WP_Generous_Public_Templates         $templates        Loads user or default templates.
	 */
	private static $templates;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.1.0
	 *
	 * @param    array                                $options          The settings of the plugin.
	 */
	public function __construct( $options = false, $templates = false ) {
		if ( false !== $options ) {
			self::$permalink = $options['permalink'];
			self::$templates = $templates;
			self::$filters = new WP_Generous_Public_Filters();
		}
	}

	/**
	 * Outputs a store.
	 *
	 * @since    0.1.0
	 *
	 * @return   string                      The gathered html to output.
	 */
	public function shortcode_store() {

		ob_start();

		include self::$templates->load('shortcode-store');

		return ob_get_clean();

	}

	/**
	 * Outputs a single slider.
	 *
	 * @since    0.1.0
	 *
	 * @param    array    $data              Data from the specified slider.
	 *
	 * @return   string                      The gathered html to output.
	 */
	public function slider( $data ) {

		ob_start();

		include self::$templates->load('slider');

		return self::$filters->slider( $data, ob_get_clean() );

	}

	/**
	 * Outputs sliders from a category.
	 *
	 * @since    0.1.0
	 *
	 * @param    array    $data              Data from the specified category.
	 *
	 * @return   string                      The gathered html to output.
	 */
	public function shortcode_category( $data ) {

		$post = WP_Generous_Public_Posts::obtain( 'sliders', $data['sliders'] );

		ob_start();

		include self::$templates->load('shortcode-category');

		return ob_get_clean();

	}

	/**
	 * Outputs a slider item.
	 *
	 * @since    0.1.0
	 *
	 * @param    array    $data              Data from the specified slider.
	 *
	 * @return   string                      The gathered html to output.
	 */
	public function slider_item( $data ) {

		ob_start();

		include self::$templates->load('slider-item');

		return self::$filters->slider( $data, ob_get_clean() );

	}

	/**
	 * Outputs the list of categories.
	 *
	 * @since    0.1.0
	 *
	 * @param    array    $data              Data from the specified slider.
	 *
	 * @return   string                      The gathered html to output.
	 */
	public function shortcode_categories( $data ) {

		$post = WP_Generous_Public_Posts::obtain( 'categories', $data );

		ob_start();

		include self::$templates->load('shortcode-categories');

		return self::$filters->category( $data, ob_get_clean() );

	}

	/**
	 * Outputs a category item.
	 *
	 * @since    0.1.0
	 *
	 * @param    array    $data              Data from the specified slider.
	 *
	 * @return   string                      The gathered html to output.
	 */
	public function categories_item( $data ) {

		ob_start();

		include self::$templates->load('categories-item');

		return self::$filters->category( $data, ob_get_clean() );

	}

}
