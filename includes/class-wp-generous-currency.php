<?php

/**
 * Maintains methods for currency.
 *
 * @since      0.1.0
 *
 * @package    WP_Generous
 * @subpackage WP_Generous/includes
 * @author     Matthew Govaere <matthew@genero.us>
 */
class WP_Generous_Currency {

	/**
	 * Singleton instance of this class.
	 *
	 * @since    0.1.0
	 * @access   public
	 *
	 * @var      WP_Generous_Currency      $instance     The instance of this class.
	 */
	public static $instance;

	/**
	 * Obtain the original instance that was created.
	 *
	 * @since    0.1.0
	 *
	 * @return   WP_Generous_Currency                    The instance of this class.
	 */
	public static function obtain() {
		if ( ! self::$instance ) { 
			self::$instance = new WP_Generous_Currency();
		}
	
		return self::$instance; 
	}

	/**
	 * Output the specified amount to the proper formatting.
	 *
	 * @since    0.1.0
	 *
	 * @param    int|float       $value     The value to format.
	 * @param    string          $cur       The currency.
	 * @param    string          $type      The type of conversion ('decimal' | 'whole').
	 *
	 * @return   int|float                  The updated value.
	 */
	public function amount( $value, $curr = 'usd', $type = 'decimal' ) {

		$curr = strtolower( $curr );

		switch ( $curr ) {

			case 'usd':
				if ( 'whole' == $type ) {
					return $this->whole( $value, 2 );
				} else {
					return $this->decimal( $value, 2 );
				}
			break;

			default:
				if ( 'whole' == $type ) {
					return $this->whole( $value, 2 );
				} else {
					return $this->decimal( $value, 2 );
				}
			break;

		}

	}

	/**
	 * Format specified value as decimal.
	 *
	 * @since    0.1.0
	 *
	 * @param    int            $value      The value to format.
	 * @param    int            $dec_place  The decimal place.
	 *
	 * @return   string                     The updated value.
	 */
	public function decimal( $value, $dec_place = 2 ) {
        return number_format( $this->round( $value ), $dec_place );
    }

	/**
	 * Round to whole number, if (technically) allowed.
	 *
	 * @since    0.1.0
	 *
	 * @param    int            $value      The value to convert.
	 * @param    int            $dec_place  The decimal place to use.
	 *
	 * @return   int|float                  The updated value.
	 */
	public function whole( $value, $dec_place = 2 ) {

		if ( $value < 10 ) {
			if( is_float( $this->round( $value ) ) ) {
				return round( $this->decimal( $value, $dec_place ) );
			} else {
				return $this->decimal( $value, $dec_place );

			}
		} else {
			return round( $this->decimal( $value, $dec_place ) );
		}

	}

	/**
	 * (Temporary) Ensure rounding is correct.
	 *
	 * @since    0.1.0
	 *
	 * @param    int|float      $value      The value to convert.
	 *
	 * @return   int|float                  The updated value.
	 */
	public function round( $value ) {

		if ( $value < 10 ) {
			$value = round( $value * 2, 0 ) / 2;
		} else {
			$value = round ( $value );
		}

		return $value;
	}

	/**
	 * Get the symbol for a curency.
	 *
	 * @since    0.1.0
	 *
	 * @param    string         $curr       The type of currency.
	 *
	 * @return   string                     The symbol.
	 */
	public function symbol( $curr = 'usd' ) {

		$curr = strtolower( $curr );

		$symbols = array(
			'usd' => '$',
			'gbp' => '£',
		);

		if ( ! isset ( $symbols[ $curr ] ) ) {
			$curr = 'usd';
		}

		return $symbols[ $curr ];

	}

}
