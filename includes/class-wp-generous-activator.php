<?php

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      0.1.0
 *
 * @package    WP_Generous
 * @subpackage WP_Generous/includes
 * @author     Matthew Govaere <matthew@genero.us>
 */
class WP_Generous_Activator {

	/**
	 * Initiate activation.
	 *
	 * @since    0.1.0
	 */
	public static function activate() {
        flush_rewrite_rules();
	}

}
