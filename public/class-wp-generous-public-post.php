<?php

/**
 * Maintains methods and data for public posts.
 *
 * @since      0.1.0
 *
 * @package    WP_Generous
 * @subpackage WP_Generous/public
 * @author     Matthew Govaere <matthew@genero.us>
 */
class WP_Generous_Public_Post {

	/**
	 * The type of data.
	 *
	 * @since    0.1.0
	 * @access   public
	 *
	 * @var      string   $type         Current key being looped. ('categories' || 'sliders')
	 */
	public $type;

	/**
	 * The data of the specified type.
	 *
	 * @since    0.1.0
	 * @access   public
	 *
	 * @var      array    $data         Current key being looped.
	 */
	public $data;

	/**
	 * The current index being looped.
	 *
	 * @since    0.1.0
	 * @access   public
	 *
	 * @var      int      $index        Current key being looped.
	 */
	public $index;

	/**
	 * The total items being looped.
	 *
	 * @since    0.1.0
	 * @access   public
	 *
	 * @var      int      $total        The total items.
	 */
	public $total;

	/**
	 * The root permalink of the data.
	 *
	 * @since    0.1.0
	 * @access   public
	 *
	 * @var      string   $permalink    The root permalink of the url.
	 */
	public $permalink;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.1.0
	 *
	 * @param    string   $type         The type of data. ('categories' || 'sliders')
	 * @param    array    $data         The data of the specified type.
	 * @param    array    $permalink    The root permalink of the url.
	 */
	public function __construct( $type, $data, $permalink ) {

		$this->type = $type;
		$this->data = $data;
		$this->index = -1;
		$this->total = count($this->data);
		$this->permalink = $permalink;

	}

	/**
	 * Resets the loop.
	 *
	 * @since    0.1.0
	 */
	public function reset() {

		$this->data = false;
		$this->index = -1;
		$this->total = false;

	}

	/**
	 * Gets the current data.
	 *
	 * @since    0.1.0
	 *
	 * @return   array                  The current data.
	 */
	public function get_data() {
		return $this->data[ $this->index ];
	}

	/**
	 * Gets the title.
	 *
	 * @since    0.1.0
	 *
	 * @return   string                 The title to output.
	 */
	public function get_title() {

		$data = $this->get_data();

		if ( isset( $data['title'] ) ) {
			return $data['title'];
		} else if( isset( $data['name'] ) ) {
			return $data['name'];
		}

	}

	/**
	 * Gets the permalink.
	 *
	 * @since    0.1.0
	 *
	 * @param    array    $options      The settings of the plugin.
	 *
	 * @return   string                 The url to output.
	 */
	public function get_permalink( $options = false) {

		$data = $this->get_data();

		$link = new WP_Generous_Link();
		$link->permalink = $this->permalink;
		$link->query_var = $this->type;
		$link->query_val = $data['slug'];

		if( false !== $options && $this->type === 'sliders' ) {
			if( true === $options['enable_overlay'] ) {
				return $data['short_url'];
			}
		}

		return $link->get();

	}

	/**
	 * Gets the content.
	 *
	 * @since    0.1.0
	 *
	 * @return   html                   The html of the content to output.
	 */
	public function get_content() {

		$output = new WP_Generous_Public_Output();

		$data = $this->get_data();

		switch($this->type) {

			case 'sliders':
				return $output->slider_item( $data );
			break;

			case 'categories':
				return $output->categories_item( $data );
			break;

		}

	}

}
