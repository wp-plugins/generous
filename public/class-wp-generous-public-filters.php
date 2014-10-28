<?php

/**
 * Maintains methods to convert filters to data.
 *
 * @since      0.1.0
 *
 * @package    WP_Generous
 * @subpackage WP_Generous/public
 * @author     Matthew Govaere <matthew@genero.us>
 */
class WP_Generous_Public_Filters {

	/**
	 * Used for formatting prices.
	 *
	 * @since    0.1.0
	 * @access   private
	 *
	 * @var      WP_Generous_Formatter    $formatter         Maintains methods for formatting.
	 */
	private $formatter;

	/**
	 * Properly converts currencies for displaying.
	 *
	 * @since    0.1.0
	 * @access   private
	 *
	 * @var      WP_Generous_Currency     $currency          Maintains methods for currency.
	 */
	private $currency;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.1.0
	 */
	public function __construct() {
		$this->formatter = WP_Generous_Formatter::obtain();
		$this->currency = WP_Generous_Currency::obtain();
	}

	/**
	 * Converts slider filters to slider data.
	 *
	 * @since    0.1.0
	 *
	 * @param    array    $data         Data to replace the filter with.
	 * @param    array    $content      Content to search within.
	 *
	 * @return   string                 New content with replaced filters.
	 */
	public function slider( $data, $content ) {

		$filters = array();

		// [slider_id]
		if ( isset( $data['id'] ) ) {
			$filters['slider_id'] = $data['id'];
		}

		// [title]
		if ( isset( $data['title'] ) ) {
			$filters['title'] = $data['title'];
		}

		// [cover_photo]
		if ( isset( $data['cover_photo'], $data['default_photo']['small'] ) ) {
			$filters['cover_photo'] = $data['default_photo']['small'];
		}

		// [suggested_price] & [suggested_price_whole]
		if ( isset( $data['suggested_price'] ) ) {
			$filters['suggested_price'] = $this->formatter->price( $data['suggested_price'], $data['currency'], false );
			$filters['suggested_price_whole'] = $this->formatter->price_whole( $data['suggested_price'], $data['currency'], false );
		}
		
		// [currency_symbol]
		if ( isset( $data['currency'] ) ) {
			$filters['currency_symbol'] = $this->currency->symbol( $data['currency'] );
		}

		// [additional_info]
		if ( isset( $data['additional_info'] ) ) {
			$filters['additional_info'] = $data['additional_info'];
		}

		// [charity_percentage]
		if ( isset( $data['charity_percentage'] ) ) {
			$filters['charity_percentage'] = $data['charity_percentage'];
		}

		// [button_slider_overlay]
		if ( isset( $data['short_url'] ) ) {
			$filters['button_slider_overlay'] = $data['short_url'];
		}

		if ( isset( $data['items'] ) && is_array( $data['items'] ) ) {

			// [item_total]
			$filters['item_total'] = count( $data['items'] );

			// [minimum_price] & [minimum_price_whole]
			if ( isset( $data['items'][0], $data['items'][0]['minimum_price'] ) ) {
				$filters['minimum_price'] = $this->formatter->price( $data['items'][0]['minimum_price'], $data['currency'], false );
				$filters['minimum_price_whole'] = $this->formatter->price_whole( $data['items'][0]['minimum_price'], $data['currency'], false );
			}

			// [item_total_label]
			if( $filters['item_total'] === 1 ) {
				$filters['item_total_label'] = _x( 'Item', '1 Item' );
			} else {
				$filters['item_total_label'] = _x( 'Items', '2 Items' );
			}

		}

		foreach ( $filters as $filter => $replacement ) {
			$content = $this->convert( $filter, $replacement, $content );
		}

		return $content;

	}

	/**
	 * Converts category filters to category data.
	 *
	 * @since    0.1.0
	 *
	 * @param    array    $data         Data to replace the filter with.
	 * @param    array    $content      Content to search within.
	 *
	 * @return   string                 New content with replaced filters.
	 */
	public function category( $data, $content ) {

		$filters = array();

		if ( isset( $data['title'] ) ) {
			$filters['title'] = $data['title'];
		}

		foreach ( $filters as $filter => $replacement ) {
			$content = $this->convert( $filter, $replacement, $content );
		}

		return $content;

	}

	/**
	 * Replaces the specified filter with the proper data.
	 *
	 * @since    0.1.0
	 *
	 * @param    array    $filter       Filtert to search for.
	 * @param    array    $data         Data to replace the filter with.
	 * @param    array    $content      Content to search within.
	 *
	 * @return   string                 New content with replace filters.
	 */
	private function convert( $filter, $data, $content ) {
		return str_replace( '[' . $filter.  ']', $data, $content );
	}

}
