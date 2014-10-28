<?php

/**
 * Template Name: Generous Page - Category
 *
 * Outputs the name and sliders of the specified category.
 *
 * Dependencies:
 * - partials/slider-item.php
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

			<h1><?php single_cat_title(); ?></h1>

		<?php if (have_posts()): ?>

			<div class="generous-sliders">

			<?php while (have_posts()) : the_post(); ?>

				<?php the_content(); ?>

			<?php endwhile; ?>

			</div>

			<?php wp_generous_pagination( '<', '>' ); ?>

		<?php endif; ?>

		</section>

	</main>

<?php get_sidebar(); ?>

<?php get_footer(); ?>