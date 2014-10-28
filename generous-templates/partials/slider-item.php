<?php

/**
 * Generous Slider Item
 *
 * Outputs the slider item.
 *
 * Used by:
 * - ../page-default.php
 * - ../page-category.php
 * - ../shortcode-category.php
 *
 * @since      0.1.0
 *
 * @package    WP_Generous
 * @subpackage WP_Generous/generous-templates/partials
 */
?>

<div class="generous-slider-item">

	<a href="<?php the_permalink(); ?>">

		<div class="cover-photo">
			<img src="[cover_photo]" />
		</div>

		<div class="details">

			<div class="title">
				[title]
			</div>

			<div class="suggested-price">
				[currency_symbol][suggested_price_whole]+
			</div>

		</div>

	</a>

</div>