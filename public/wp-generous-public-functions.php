<?php

/**
 * Allows looping through categories, and to access each individually.
 *
 * @since    0.1.0
 */
function wp_generous_have_categories() {

	$post = WP_Generous_Public_Posts::obtain();

	return $post->have( 'categories' );

}

/**
 * Allows looping through sliders, and to access each individually.
 *
 * @since    0.1.0
 */
function wp_generous_have_sliders() {

	$post = WP_Generous_Public_Posts::obtain();

	return $post->have( 'sliders' );

}

/**
 * Outputs the title of the current item.
 *
 * @since    0.1.0
 */
function wp_generous_the_title() {

	$post = WP_Generous_Public_Posts::obtain();

	echo $post->get_title();

}

/**
 * Outputs the content of the current item.
 *
 * @since    0.1.0
 */
function wp_generous_the_content() {

	$post = WP_Generous_Public_Posts::obtain();

	echo $post->get_content();

}

/**
 * Outputs the url of the current item.
 *
 * @since    0.1.0
 */
function wp_generous_the_permalink() {

	$post = WP_Generous_Public_Posts::obtain();

	echo $post->get_permalink();

}

/**
 * Outputs the pagination of a specified category.
 *
 * @since    0.1.0
 * @param    string         $prev_arrow   The previous arrow label.
 * @param    string         $prev_arrow   The next arrow label.
 */
function wp_generous_pagination( $prev_arrow = '&larr;', $next_arrow = '&rarr;' ) {

	$post = WP_Generous_Public_Posts::obtain();

	echo $post->get_pagination( $prev_arrow, $next_arrow );

}
