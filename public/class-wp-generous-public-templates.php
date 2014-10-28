<?php

/**
 * Maintains methods to load plugin templates.
 *
 * @since      0.1.0
 *
 * @package    WP_Generous
 * @subpackage WP_Generous/public
 * @author     Matthew Govaere <matthew@genero.us>
 */
class WP_Generous_Public_Templates {

	/**
	 * Checks if user has a custom template, or load the default provided by the plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 *
	 * @param    string     $id       The id of the template to load.
	 *
	 * @return   string               The file path to the template.
	 */
	public function load( $id ) {

		$templates = array(
			'page-default'         => 'page-default.php',
			'page-category'        => 'page-category.php',
			'page-slider'          => 'page-slider.php',
			'shortcode-categories' => 'shortcode-categories.php',
			'shortcode-category'   => 'shortcode-category.php',
			'shortcode-store'      => 'shortcode-store.php',
			'categories-item'      => 'partials/categories-item.php',
			'slider-item'          => 'partials/slider-item.php',
			'slider'               => 'partials/slider.php',
		);

		$template = locate_template( array("generous-templates/{$templates[ $id ]}") );

		if( !$template ) {
			$template = plugin_dir_path( dirname( __FILE__ ) ) . "generous-templates/{$templates[ $id ]}";
		}

		return $template;

	}

}
