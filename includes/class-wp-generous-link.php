<?php

/**
 * Maintains hooks to generate links.
 *
 * @since      0.1.0
 *
 * @package    WP_Generous
 * @subpackage WP_Generous/includes
 * @author     Matthew Govaere <matthew@genero.us>
 */
class WP_Generous_Link {

    /**
     * Used to output the proper query variable.
     *
     * @since    0.1.0
     * @access   private
     *
     * @var      array    $query_vars      A list of query variables with relatable IDs.
     */
    private $query_vars = array(
        'generous_category' => 'generous_category',
        'category' => 'generous_category',
        'categories' => 'generous_category',
        'generous_slider' => 'generous_slider',
        'slider' => 'generous_slider',
        'sliders' => 'generous_slider',
    );

    /**
     * Used to determine to output a permalink file structure or not.
     *
     * @since    0.1.0
     * @access   private
     *
     * @var      string    $permalinks      Whether or not permalinks are active.
     */
    private $permalinks;

    /**
     * Used to define the initial path of the permalink.
     *
     * @since    0.1.0
     * @access   public
     *
     * @var      string    $permalink       The endpoint specified within the plugin options.
     */
    public $permalink;

    /**
     * Used to define the name of the query parameter.
     *
     * @since    0.1.0
     * @access   public
     *
     * @var      string    $query_var       The variable name of the query parameter.
     */
    public $query_var;

    /**
     * Used to define the value of the query parameter.
     *
     * @since    0.1.0
     * @access   public
     *
     * @var      string    $query_val       The value of the query parameter.
     */
    public $query_val;

    /**
     * Initialize the class and set its properties.
     *
     * @since    0.1.0
     * @access   public
     *
     * @var      string    $query_val       Whether or not permalinks are active.
     */
    public function __construct( $permalinks = NULL ) {

        if ( NULL === $permalinks ) {

            // Checks if Wordpress permalinks are enabled
            $this->permalinks = ( get_option( 'permalink_structure' ) != '' ) ? true : false;

        } else {

            $this->permalinks = $permalinks;
            
        }

    }

    /**
     * Returns a url.
     *
     * @since    0.1.0
     *
     * @return   string                     The url to output.
     */
    public function get() {
        if ( true === $this->permalinks ) {
            return get_bloginfo( 'url' ) . "/{$this->permalink}/{$this->query_val}/";
        } else {
            return get_bloginfo( 'url' ) . "/?{$this->query_vars[ $this->query_var ]}={$this->query_val}";
        }
    }

    /**
     * Returns a url.
     *
     * @since    0.1.0
     *
     * @param    string    $post_link       Current link. Invalid, do not use.
     * @param    string    $post            Current post. Invalid, do not use.
     *
     * @return   string                     The url to output.
     */
    public function filter_the_permalink( $post_link, $post ) {
        return $this->get();
    }

}
