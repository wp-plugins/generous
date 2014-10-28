<?php

/**
 * Generous Slider
 *
 * Outputs a single slider.
 *
 * Used by:
 * - ../page-slider.php
 *
 * @since      0.1.0
 *
 * @package    WP_Generous
 * @subpackage WP_Generous/generous-templates/partials
 */
?>

<div class="generous-slider">

	<h1>[title]</h1>

	<div class="cover-photo">
		<img src="[cover_photo]" />
	</div>

	<div class="details">

		<div class="suggested-price">
			[currency_symbol][suggested_price_whole]+
		</div>

		<div class="generous-buy">
			<a href="[button_slider_overlay]">Buy</a>
		</div>

	</div>

</div>