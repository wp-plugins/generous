<?php

/**
 * (Shortcode) Generous Category
 *
 * Outputs sliders from the specified category.
 *
 * Usage: [generous category=<id>]
 *
 * Dependencies:
 * - partials/sliders-slider.php
 *
 * @since      0.1.0
 *
 * @package    WP_Generous
 * @subpackage WP_Generous/generous-templates
 */
?>

<div class="generous-sliders">

<?php while( wp_generous_have_sliders() ): ?>

	<?php wp_generous_the_content(); ?>

<?php endwhile; ?>

</div>

<?php wp_generous_pagination( '<', '>' ); ?>