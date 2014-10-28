<?php

/**
 * (Shortcode) Generous Categories
 *
 * Outputs a list of categories.
 *
 * Usage: [generous categories]
 *
 * Dependencies
 * - partials/categories-item.php
 *
 * @since      0.1.0
 *
 * @package    WP_Generous
 * @subpackage WP_Generous/generous-templates
 */
?>

<ul class="generous-categories">

<?php while( wp_generous_have_categories() ): ?>

	<?php wp_generous_the_content(); ?>

<?php endwhile; ?>

</ul>