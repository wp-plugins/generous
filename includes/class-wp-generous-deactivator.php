<?php

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      0.1.0
 *
 * @package    WP_Generous
 * @subpackage WP_Generous/includes
 * @author     Matthew Govaere <matthew@genero.us>
 */
class WP_Generous_Deactivator {

	/**
	 * Initiate deactivation.
	 *
	 * @since    0.1.0
	 */
	public static function deactivate() {
        flush_rewrite_rules();
	}

}
