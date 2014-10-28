<?php

/**
 * Saves and retrieves data, generally from the Api.
 *
 * @since      0.1.0
 *
 * @package    WP_Generous
 * @subpackage WP_Generous/public
 * @author     Matthew Govaere <matthew@genero.us>
 */
class WP_Generous_Public_Data {

	/**
	 * Contains data retrieved from the Api.
	 *
	 * @since    0.1.0
	 * @access   public
	 *
	 * @var      array          $data     Response data with keys set as the slug id.
	 */
	public $data = array();

	/**
	 * Saves specified data.
	 *
	 * @since    0.1.0
	 *
	 * @param    array          $key      The key used for retrieving data.
	 * @param    array          $val      The data to save.
	 */
	public function add( $key, $val ) {
		if ( isset( $val ) && ! isset( $val['error'] ) ) {
			$this->data[ $key ] = $val;
		} else {
			$this->data[ $key ] = false;
		}
	}

	/**
	 * Get data based on the specified key/id.
	 *
	 * @since    0.1.0
	 *
	 * @param    array          $key      The key to retrieve, (usually) based on a slug or id.
	 *
	 * @return   array|false              The reqested data.
	 */
	public function get( $key ) {
		return ( isset( $this->data[ $key ] ) ) ? $this->data[ $key ] : false;
	}

}
