<?php

/**
 * Template Name: Generous Page - Slider
 *
 * Outputs specified slider..
 *
 * Dependencies:
 * - partials/slider.php
 *
 * @since      0.1.0
 *
 * @package    WP_Generous
 * @subpackage WP_Generous/generous-templates
 */
?>
<?php get_header(); ?>

	<main role="generous-store">

		<section>

			<?php if (have_posts()): while (have_posts()) : the_post(); ?>

			<?php the_content(); ?>

			<?php endwhile; endif; ?>
		
		</section>

	</main>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
