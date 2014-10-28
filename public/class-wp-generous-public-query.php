<?php

/**
 * Maintains methods to create phantom Wordpress queries.
 *
 * @since      0.1.0
 *
 * @package    WP_Generous
 * @subpackage WP_Generous/public
 * @author     Matthew Govaere <matthew@genero.us>
 */
class WP_Generous_Public_Query {

	/**
	 * Prepares and outputs data to html.
	 *
	 * @since    0.1.0
	 * @access   private
	 *
	 * @var      WP_Generous_Public_Output    $output             Maintains methods to convert data to html templates.
	 */
	private $output;

	/**
	 * Contains the general settings for the plugin specified by the user.
	 *
	 * @since    0.1.0
	 * @access   private
	 *
	 * @var      array                        $options            The settings of the plugin.
	 */
	private $options;

	/**
	 * The root slug for the permalink url.
	 *
	 * @since    0.1.0
	 * @access   private
	 *
	 * @var      array                        $root_slug          The root slug of the permalink.
	 */
	private $root_slug;

	/**
	 * The default page title, set to account name.
	 *
	 * @since    0.1.0
	 * @access   private
	 *
	 * @var      array                        $title              The default page title.
	 */
	private $title;

	/**
	 * Wordpress's $wp global variable.
	 *
	 * @since    0.1.0
	 * @access   private
	 *
	 * @var      WP                           $wp                 Global variable $wp.
	 */
	private $wp;

	/**
	 * Wordpress's $wp_query global variable.
	 *
	 * @since    0.1.0
	 * @access   private
	 *
	 * @var      WP_Query                     $wp_query           Global variable $wp_query.
	 */
	private $wp_query;

	/**
	 * The current `the_permalink` of post currently being looped.
	 *
	 * @since    0.1.0
	 * @access   private
	 *
	 * @var      string                       $current_link       The current link of post being looped.
	 */
	private $current_link;

	/**
	 * Initialize the class, set its properties.
	 *
	 * @since    0.1.0
	 *
	 * @param    WP_Generous_Public_Output    $output             Maintains methods to convert data to html templates.
	 * @param    array                        $options            The settings of the plugin.
	 */
	public function __construct( $output, $options ) {

		$this->output = $output;
		$this->options = $options;

		$this->root_slug = $options['permalink'];
		$this->title = isset( $options['title'] ) ? $options['title'] : 'Store';

	}
	
	/**
	 * Generates data and content, and creates phantom Wordpress queries/posts.
	 *
	 * @since    0.1.0
	 *
	 * @param    array          $type         The requested page type.
	 * @param    array|false    $id           The specified id.
	 * @param    array|false    $data         The retrieved data.
	 *
	 * @return   array                        The updated Wordpress posts.
	 */
	public function run( $type, $id = false, $data = false ) {

		global $wp, $wp_query;

		$this->wp = $wp;
		$this->wp_query = $wp_query;

		$this->unset_404();
		remove_filter('the_content', 'wpautop');

		if ( $type === 'default' ) {

			return $this->default_page();

		} else if ( false === $data ) {

			return $this->set_404();

		} else {

			if ( $type == 'generous_page' ) {

				if ( isset( $data['sliders'] ) ) {
					return $this->category( $data );
				} else {
					return $this->slider( $data );
				}

			} else if ( $type == 'generous_category' ) {

				return $this->category( $data );

			} else if ( $type == 'generous_slider' ) {

				return $this->slider( $data );

			}

		}

	}

	/**
	 * Register the url for the_permalink.
	 *
	 * @since    0.1.0
	 *
	 * @param    array          $url          The current url.
	 *
	 * @return   string                       The url of the item being looped.
	 */
	public function custom_the_permalink( $url ) {
		return $this->current_link;
	}

	/**
	 * Generates post data and content for specified category.
	 *
	 * @since    0.1.0
	 * @access   private
	 *
	 * @param    array|false    $data         The retrieved category data.
	 *
	 * @return   array                        The posts.
	 */
	private function category( $data ) {

		$posts = NULL;

		if ( isset( $data['sliders'] ) ) {

			// Set missing data for default/featured category
			if ( ! isset( $data['slug'], $data['title'] ) ) {
				$data['title'] = $this->options['title'];
				$data['slug'] = '';
			}

			$taxonomy_query = $this->create_taxonomy_query(array(
				'term_id'  => 0,
				'name'     => $data['title'],
				'slug'     => $data['slug'],
				'taxonomy' => 'generous_category',
			));

			$this->wp_query->queried_object = $taxonomy_query;

			foreach ( $data['sliders'] as $slider ) {

				$link = new WP_Generous_Link();
				$link->permalink = $this->options['permalink'];
				$link->query_var = 'generous_slider';
				$link->query_val = $slider['slug'];

				if( true === $this->options['enable_overlay'] ) {
					$this->current_link = $slider['short_url'];
				} else {
					$this->current_link = $link->get();
				}

				add_filter('the_permalink', array( $this, 'custom_the_permalink') );

				$post = $this->create_wp_post( $slider, 'slider-item' );
				$posts[] = $post;

			}
			
			$this->wp_query->max_num_pages = $data['page']['total'];

		}

		$this->wp_query->is_page = true;
		$this->wp_query->is_singular = false;
		$this->wp_query->is_category = false;
		$this->wp_query->is_archive = false;
		$this->wp_query->is_home = false;

		return $posts;

	}

	/**
	 * Generates post data and content for specified slider.
	 *
	 * @since    0.1.0
	 * @access   private
	 *
	 * @param    array          $data         The retrieved slider data.
	 *
	 * @return   array                        The posts.
	 */
	private function slider( $data ) {

		$taxonomy_query = $this->create_taxonomy_query(array(
			'name'     => $data['slider']['title'],
			'slug'     => $data['slider']['slug'],
			'taxonomy' => 'generous_slider',
		));

		$this->wp_query->queried_object = $taxonomy_query;

		$post = $this->create_wp_post( $data['slider'], 'slider' );

		if ( false !== $post ) {

			$post->post_type = 'page';
			$posts = NULL;
			$posts[] = $post;

			$this->wp_query->is_page = true;
			$this->wp_query->is_singular = false;
			$this->wp_query->is_category = false;
			$this->wp_query->is_archive = false;
			$this->wp_query->is_home = false;

		}

		return $posts;
 
	}

	/**
	 * Generates post data for default page.
	 *
	 * @since    0.1.0
	 * @access   private
	 *
	 * @return   array                        The posts.
	 */
	private function default_page() {

		$post = $this->create_post(array(
			'id'           => -1,
			'post_title'   => $this->title,
			'post_content' => "[generous store]",
			'post_type'    => 'page',
			'post_name'    => $this->root_slug . '/',
			'guid'         => get_bloginfo('wpurl' . '/' . $this->root_slug),
		));

		$posts = NULL;
		$posts[] = $post;

		$this->wp_query->is_page = true;
		$this->wp_query->is_singular = true;
		$this->wp_query->is_archive = false;
		$this->wp_query->is_category = false;
		$this->wp_query->is_home = false;

		return $posts;
 
	}

	/**
	 * Force Wordpress to use a 404 page.
	 *
	 * @since    0.1.0
	 * @access   private
	 *
	 * @param    array          $message    The message to output, if allowed.
	 *
	 * @return   array                      Empty posts.
	 */
	private function set_404( $message = 'Page not found' ) {

		$this->wp_query->set_404();
		$this->wp_query->query_vars['error'] = $message;
		$this->wp_query->is_404 = true;

		return array();

	}

	/**
	 * Unset 404 settings in Wordpress query.
	 *
	 * @since    0.1.0
	 * @access   private
	 */
	private function unset_404() {
		unset($this->wp_query->query['error']);
		$this->wp_query->query_vars['error'] = '';
		$this->wp_query->is_404 = false;
	}

	/**
	 * Creates a temporary post-like object.
	 *
	 * @since    0.1.0
	 * @access   private
	 *
	 * @param    array          $data       The data to pull info from.
	 * @param    array          $type       The type of post.
	 *
	 * @return   WP_Post|false              The phantom Wordpress Post object.
	 */
	private function create_wp_post( $data, $type ) {

		if ( isset( $data ) ) {

			if( 'slider-item' === $type ) {

				$post_title = $data['title'];
				$post_slug = $data['slug'];
				$output = $this->output->slider_item( $data );
				$url = get_bloginfo( 'url' ) . "/?generous_slider={$post_slug}";

			} else if( 'slider' === $type ) {

				$post_title = $data['title'];
				$post_slug = $data['slug'];
				$output = $this->output->slider( $data );
				$url = get_bloginfo( 'url' ) . "/?generous_slider={$post_slug}";

			}

			$post = $this->create_post(array(
				'id'           => 0,
				'post_title'   => $post_title,
				'post_content' => $output,
				'post_type'    => 'page',
				'post_name'    => $this->root_slug . '/' .  $post_slug,
				'guid'         => $url,
			));

			$post = new WP_Post($post);

		} else {

			$post = false;

		}

		return $post;

	}

	/**
	 * Creates a temporary object to mimic a Wordpress (database) post query result.
	 *
	 * @since    0.1.0
	 * @access   private
	 *
	 * @param    array          $params     Contains data to set.
	 *
	 * @return   object                     The post data object.
	 */
	private function create_post( $params ) {

		$post = new stdClass;

		$post->id = $params['id'];
		$post->post_title = $params['post_title'];
		$post->post_content = $params['post_content'];
		$post->post_type = $params['post_type'];
		$post->post_name = $params['post_name'];
		$post->guid = $params['guid'];

		$post->post_author = 1;
		$post->post_status = 'static';
		$post->comment_status = 'closed';
		$post->ping_status = 'closed';
		$post->comment_count = 0;
		$post->post_date = current_time('mysql');
		$post->post_date_gmt = current_time('mysql',1);
		$post->filter = 'raw';

		return $post;

	}

	/**
	 * Creates a temporary object to mimic a Wordpress (database) taxonomy query result.
	 *
	 * @since    0.1.0
	 * @access   private
	 *
	 * @param    array          $params     Contains data to set.
	 *
	 * @return   object                     The taxonomy data object.
	 */
	private function create_taxonomy_query( $params ) {

		$query = new stdClass;

		$query->term_id = ( isset( $params['term_id'] ) ) ? $params['term_id'] : 0;
		$query->name = $params['name'];
		$query->slug = $params['slug'];
		$query->taxonomy = $params['taxonomy'];
		$query->nice_name = $params['slug'];

		return $query;

	}

}
