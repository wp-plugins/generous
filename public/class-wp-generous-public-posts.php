<?php

/**
 * Maintains methods and data for public functions.
 *
 * @since      0.1.0
 *
 * @package    WP_Generous
 * @subpackage WP_Generous/public
 * @author     Matthew Govaere <matthew@genero.us>
 */
class WP_Generous_Public_Posts {

	/**
	 * Singleton instance of this class.
	 *
	 * @since    0.1.0
	 * @access   public
	 *
	 * @var      WP_Generous_Public_Posts         $instance     The instance of this class.
	 */
	public static $instance;

	/**
	 * The data stored to loop.
	 *
	 * @since    0.1.0
	 * @access   private
	 *
	 * @var      string                           $data         The key-stored data to loop.
	 */
	public $data = array();

	/**
	 * The current key being looped.
	 *
	 * @since    0.1.0
	 * @access   private
	 *
	 * @var      string                           $current      Current key being looped.
	 */
	public $current;

	/**
	 * Contains the general settings for the plugin specified by the user.
	 *
	 * @since    0.1.0
	 * @access   private
	 *
	 * @var      array                            $options      The settings of the plugin.
	 */
	private $options;

	/**
	 * Obtain the original instance that was created.
	 *
	 * @since     0.1.0
	 *
	 * @param    string                           $type         The key to obtain. ('categories' || 'sliders')
	 * @param    array                            $data         Array of items to be looped.
	 *
	 * @return    WP_Generous_Public_Posts                      The instance of this class.
	 */
	public static function obtain( $type = NULL, $data = NULL ) {

		if ( !self::$instance ) { 
			self::$instance = new WP_Generous_Public_Posts();
		}

		if( NULL !== $type && NULL != $data ) {
			self::$instance->add( $type, $data );
		}

		return self::$instance; 

	}

	/**
	 * Adds data that needs to be looped.
	 *
	 * @since    0.1.0
	 * @access   private
	 *
	 * @param    string    $type     The key to add. ('categories' || 'sliders')
	 * @param    array     $data     Array of items to be looped.
	 */
	private function add( $type, $data ) {
		$this->data[ $type ] = new WP_Generous_Public_Post( $type, $data, $this->options['permalink'] );
	}

	/**
	 * Checks to see if there's more data to access.
	 *
	 * @since    0.1.0
	 *
	 * @param    string    $type     The key to check: 'categories' || 'sliders'
	 *
	 * @return   bool                True if yes, false if no.
	 */
	public function have( $type ) {

		if( $this->data[ $type ]->index < $this->data[ $type ]->total - 1 ) {

			add_filter('the_permalink', array( $this, 'custom_the_permalink') );

			$this->data[ $type ]->index++;
			$this->current = $type;

			return true;

		} else {

			$this->data[ $type ]->reset();

			return false;

		}

	}

	/**
	 * Outputs the title of the current item.
	 *
	 * @since    0.1.0
	 */
	public function get_title() {
		return $this->data[ $this->current ]->get_title();
	}

	/**
	 * Outputs the permalink of the current item.
	 *
	 * @since    0.1.0
	 */
	public function get_permalink() {
		return $this->data[ $this->current ]->get_permalink( $this->options );
	}

	/**
	 * Outputs the content of the current item.
	 *
	 * @since    0.1.0
	 */
	public function get_content() {
		return $this->data[ $this->current ]->get_content();
	}

	/**
	 * Outputs the pagination of the current category.
	 *
	 * @since    0.1.0
	 *
	 * @param    string         $prev_arrow   The previous arrow label.
	 * @param    string         $prev_arrow   The next arrow label.
	 *
	 * @return   string                       The html content of pagination.
	 */
	public function get_pagination( $prev_arrow = '&larr;', $next_arrow = '&rarr;' ) {

		global $wp_query;

		$total = $wp_query->max_num_pages;

		if( $total > 1 )  {

			if( ! $current_page = get_query_var('paged') ) {
				$current_page = 1;
			}

			if( get_option('permalink_structure') ) {
				$format = 'page/%#%/';
			} else {
				$format = '&paged=%#%';
			}

			$next_page = $current_page + 1;

			$pagination = "\n<div class=\"generous-pagination\">\n";

			if( false !== $this->options['enable_load_more'] ) {

				if( $total > 1 && $current_page < $total ) {

					$url = esc_url( get_pagenum_link( $next_page ) );

					$pagination .= "<a class=\"generous-load-more\" href=\"{$url}\">Load More</a>";

				}

			} else {

				$big = 999999999;

				$pagination .= paginate_links(array(
					'base'          => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
					'format'        => $format,
					'current'       => max( 1, get_query_var('paged') ),
					'total'         => $total,
					'mid_size'      => 3,
					'type'          => 'list',
					'prev_text'     => $prev_arrow,
					'next_text'     => $next_arrow,
				) );

			}

			$pagination .= '</div>';

			return $pagination;

		} else {

			return '';

		}

	}

	/**
	 * Sets the plugin options.
	 *
	 * @since    0.1.0
	 *
	 * @param    array          $options      The settings of the plugin.
	 */
	public function set_options( $options ) {
		$this->options = $options;
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
		return $this->get_permalink();
	}

}
