<?php

/**
 * Maintains requests to the Generous Api.
 *
 * @since      0.1.0
 *
 * @package    WP_Generous
 * @subpackage WP_Generous/includes
 * @author     Matthew Govaere <matthew@genero.us>
 */
class WP_Generous_Api {

	/**
	 * Singleton instance of this class.
	 *
	 * @since    0.1.0
	 * @access   public
	 *
	 * @var      WP_Generous_Api     $instance           The instance of this class.
	 */
	public static $instance;

	/**
	 * Contains the general settings for the plugin specified by the user.
	 *
	 * @since    0.1.0
	 * @access   private
	 *
	 * @var      array               $plugin_options     The settings of the plugin.
	 */
	private $plugin_options;

	/**
	 * Obtain the original instance that was created.
	 *
	 * @since     0.1.0
	 *
	 * @return    WP_Generous_Api                        The instance of this class.
	 */
	public static function obtain( $plugin_options = false ) {

		if ( ! self::$instance ) { 
			self::$instance = new WP_Generous_Api( $plugin_options );

			if ( false !== $plugin_options ) {
				self::$instance->plugin_options = $plugin_options; 
			}
		}
	
		return self::$instance; 

	}

	/**
	 * Handles the Api request and returns the response.
	 *
	 * @since     0.1.0
	 * @access    private
	 *
	 * @return    array|false             The Api response.
	 */
	public function request( $method, $endpoint, $params = array() ) {

		$data = Generous::customRequest( $method, $endpoint, $params );

		if ( $data && !isset($data['error'] ) ) {
			return $data;
		} else {
			return false;
		}

	}

	/**
	 * Get the account name for the specified account.
	 *
	 * @since     0.1.0
	 *
	 * @return    array|false             Account data.
	 */
	public function get_account( $id = false ) {

		if ( false === $id ) {
			$id = $this->plugin_options['username'];
		}

		return $this->request( 'GET', "accounts/{$id}" );

	}

	/**
	 * Get the default store sliders.
	 *
	 * @since     0.1.0
	 *
	 * @param     int|bool       $paged   The requested page number.
	 *
	 * @return    array|false             Default store data.
	 */
	public function get_store_default( $paged = false ) {

		$params = array();
		$params['limit'] = $this->plugin_options['sliders_per_page'];

		if(false !== $paged) {
			$params['page'] = $paged;
		}

		$data = $this->request( 'GET', "accounts/{$this->plugin_options['username']}/store/default", $params );

		if( is_array( $data['sliders'] ) && count( $data['sliders'] ) > 0 ) {
			return $data;
		} else {
			return false;
		}

	}

	/**
	 * Get the list of categories for the specified account.
	 *
	 * @since     0.1.0
	 *
	 * @return    array|false             Categories data.
	 */
	public function get_categories() {
		return $this->request( 'GET', "accounts/{$this->plugin_options['username']}/store/categories" );
	}

	/**
	 * Get the category data for the specified id.
	 *
	 * @since     0.1.0
	 *
	 * @param     string         $id      Category id.
	 * @param     int|bool       $paged   The requested page number.
	 *
	 * @return    array|false             Category data.
	 */
	public function get_category( $id, $paged = false ) {

		$params = array();
		$params['limit'] = $this->plugin_options['sliders_per_page'];

		if(false !== $paged) {
			$params['page'] = $paged;
		}

		$data = $this->request( 'GET', "accounts/{$this->plugin_options['username']}/store/categories/{$id}", $params );

		if( is_array( $data['sliders'] ) && count( $data['sliders'] ) > 0 ) {
			return $data;
		} else {
			return false;
		}

	}

	/**
	 * Get the slider data for the specified id.
	 *
	 * @since     0.1.0
	 *
	 * @param     string         $id      Slider id.
	 *
	 * @return    array                   Category data.
	 */
	public function get_store_slider( $id ) {
		return $this->request( 'GET', "accounts/{$this->plugin_options['username']}/store/slider/{$id}" );
	}

	/**
	 * Get the slider data for the specified id.
	 *
	 * @since     0.1.0
	 *
	 * @param     string         $id      Slider id.
	 *
	 * @return    array                   Category data.
	 */
	public function get_slider( $id ) {
		return $this->request( 'GET', "sliders/{$id}" );
	}

	/**
	 * Get the (uknown) data for the specified (slug) id.
	 *
	 * @since     0.1.0
	 *
	 * @param     string         $id      Slug (category or slider).
	 * @param     int|bool       $paged   The requested page number.
	 *
	 * @return    array                   Unknown data.
	 */
	public function get_unknown( $id, $paged = false ) {

		$params = array();
		$params['limit'] = $this->plugin_options['sliders_per_page'];

		if( false !== $paged ) {
			$params['page'] = $paged;
		}

		return $this->request( 'GET', "accounts/{$this->plugin_options['username']}/store/verify/{$id}", $params );

	}

}
