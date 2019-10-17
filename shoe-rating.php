<?php
/*
 * Plugin Name: Shoe Rating - Hot or Not?
 * Description: The Shoe Rating Plugin allows users of sneaker blog site to rate sneaker releases as hot or not. 
 * 				Would you buy these shoes? Rate them as Hot or Not!
 * Author: Naomi Black
 * Author URI: chxwithsole.com
 * Version: 1.0
*/

/**
 * Get some constants ready for paths when your plugin grows 
 * 
 */


define( 'shoe_rating_version', '1.0' );
define('shoe_rating_url', WP_PLUGIN_URL."/".dirname( plugin_basename( __FILE__ ) ) );
define('shoe_rating_path', WP_PLUGIN_DIR."/".dirname( plugin_basename( __FILE__ ) ) );



	// Register activation and deactivation hooks
	register_activation_hook( __FILE__, 'shoe_rating_activate_callback' );
	register_deactivation_hook( __FILE__, 'shoe_rating_deactivate_callback' );

if  ( ! function_exists( 'shoe_rating_init' ) ): 
    
	function shoe_rating_init() {
	
		load_plugin_textdomain( 'shoe-rating', false, basename( dirname( __FILE__ ) ) );
	}
	add_action('plugins_loaded', 'shoe_rating_init');


	//Text of Content
	define('shoe_rating_up_text', __('Hot','shoe-rating'));
	define('shoe_rating_down_text', __('Not','shoe-rating'));

endif;	
	

	
	 // Adding JavaScript scripts
	 // Loading existing scripts from wp-includes or adding custom ones
	
if  ( ! function_exists( 'shoe_rating_scripts' ) ):
	
	function shoe_rating_scripts()
	{
		wp_enqueue_script('shoe_rating_scripts', shoe_rating_url . '/js/general.js', array('jquery'));
		wp_localize_script( 'shoe_rating_scripts', 'shoe_rating_ajax', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
	}
	add_action('wp_enqueue_scripts', shoe_rating_scripts);

endif;

	
	 // Add CSS styles  for up/down vote

if  ( ! function_exists( 'shoe_rating_styles' ) ): 
	
	function shoe_rating_styles()  
	{ 
	   
	    wp_register_style( "shoe_rating_styles",  shoe_rating_url . '/css/style.css' , "", "1.0.0");
	    wp_enqueue_style( 'shoe_rating_styles' );
	}
	add_action('wp_enqueue_scripts', 'shoe_rating_styles');	

endif;


// Add the shoe up/down links to the content posts

if  ( ! function_exists( 'shoe_rating_getlink' ) ): 

	function shoe_rating_getlink($post_ID = '')
	{
		$shoe_rating_link = "";
		
		if( $post_ID == '' ) $post_ID = get_the_ID();
		
		$shoe_rating_up_count = get_post_meta($post_ID, '_shoe_rating_up', true) != '' ? get_post_meta($post_ID, '_shoe_rating_up', true) : '0';
		$shoe_rating_down_count = get_post_meta($post_ID, '_shoe_rating_down', true) != '' ? get_post_meta($post_ID, '_shoe_rating_down', true) : '0';
		$link_up = '<span class="shoe-rating-up" onclick="shoe_rating_vote(' . $post_ID . ', 1);" data-text="' . shoe_rating_up_text . '"> +' . $shoe_rating_up_count . '</span>';
		 $link_down = '<span class="shoe-rating-down" onclick="shoe_rating_vote(' . $post_ID . ', 0);" data-text="' . shoe_rating_down_text . '"> -' . $shoe_rating_down_count . '</span>';
		$shoe_rating_link = '<div  class="shoe-rating-container" id="shoe-rating-'.$post_ID.'">';
		$shoe_rating_link .= $link_up;
		$shoe_rating_link .= ' - ';
		$shoe_rating_link .= $link_down;
		$shoe_rating_link .= '</div>';
		
		return $shoe_rating_link;
	}
	
endif;

// Print the Shoe Rating links to the_content  */

if  ( ! function_exists( 'shoe_rating_print' ) ): 

	function shoe_rating_print($content)
	{
		return $content.shoe_rating_getlink();
	}
	add_filter('the_content', shoe_rating_print);

endif;


// Handle the request to vote up or down 

if  ( ! function_exists( 'shoe_rating_add_vote_callback' ) ): 

	function shoe_rating_add_vote_callback()
	{
	
		global $wpdb;
		
		// Get the POST values
		
		$post_ID = intval( $_POST['postid'] );
		$type_of_vote = intval( $_POST['type'] );
		
		
		if ( $type_of_vote == 0 ){
		
			$meta_name = "_shoe_rating_down";
			
		}elseif( $type_of_vote == 1){
		
			$meta_name = "_shoe_rating_up";
		
		}
	
		// Retrieve from the DB
		
		$shoe_rating_count = get_post_meta($post_ID, $meta_name, true) != '' ? get_post_meta($post_ID, $meta_name, true) : '0';		
		$shoe_rating_count = $shoe_rating_count + 1;
		
		// Update 
		
		update_post_meta($post_ID, $meta_name, $shoe_rating_count);
		
		// Return the count without links
		// I strip the tags so I don't show the <a> again, to prevent them to vote.
		// We might implement HTML5 local storage in a near future.
					
		$results = strip_tags( shoe_rating_getlink($post_ID), '<div><span>');
	}

	add_action( 'wp_ajax_shoe_rating_add_vote', 'shoe_rating_add_vote_callback' );


endif;
	

/**
 * Register activation hook
 *
 */
function shoe_rating_activate_callback() {
	// do something on activation
}

/**
 * Register deactivation hook
 *
 */
function shoe_rating_deactivate_callback() {
	// do something when deactivated
}
