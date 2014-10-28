<?php

/**
 * Maintains methods for formatting.
 *
 * @since      0.1.0
 *
 * @package    WP_Generous
 * @subpackage WP_Generous/includes
 * @author     Matthew Govaere <matthew@genero.us>
 */
class WP_Generous_Formatter {

	/**
	 * Singleton instance of this class.
	 *
	 * @since    0.1.0
	 * @access   public
	 *
	 * @var      WP_Generous_Formatter     $instance     The instance of this class.
	 */
	public static $instance;

	/**
	 * Properly converts currencies for displaying.
	 *
	 * @since    0.1.0
	 * @access   private
	 *
	 * @var      WP_Generous_Currency      $instance     Maintains methods for currency.
	 */
	private $currency;

	/**
	 * Obtain the original instance that was created.
	 *
	 * @since    0.1.0
	 *
	 * @return   WP_Generous_Formatter                   The instance of this class.
	 */
	public static function obtain() {
		if ( ! self::$instance ) { 
			self::$instance = new WP_Generous_Formatter();
			self::$instance->currency = WP_Generous_Currency::obtain();
		}
	
		return self::$instance; 
	}

	/**
	 * Properly formats specified amount, and  (optional) prepends currency symbol.
	 *
	 * @since    0.1.0
	 *
	 * @param    int|string     $amount     The amount.
	 * @param    string         $curr       The type of currency.
	 * @param    bool           $symbol     Prepend symbol?
	 *
	 * @return   string                     Formatted amount.
	 */
	public function price( $amount, $curr = 'usd', $symbol = true ) {

		$price = $this->currency->amount( $amount, $curr, 'decimal' );

		if( $symbol === true ) {
			$price = $this->currency->symbol( $curr ) . $price;
		}

		return $price;

	}

	/**
	 * Sets format of specified amount as a whole number, and (optional) prepends currency symbol.
	 *
	 * @since    0.1.0
	 *
	 * @param    int|string     $amount     The amount.
	 * @param    string         $curr       The type of currency.
	 * @param    bool           $symbol     Prepend symbol?
	 *
	 * @return   string                     Formatted amount.
	 */
	public function price_whole( $amount, $curr = 'usd', $symbol = true ) {

		$price = $this->currency->amount( $amount, $curr, 'whole' );

		if( $symbol === true ) {
			$price = $this->currency->symbol( $curr ) . $price;
		}

		return $price;

	}

}
