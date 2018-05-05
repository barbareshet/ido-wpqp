<?php
/*
Plugin Name: WordPress Quiz Plugin
Plugin URI:
Description: create your own quizzes using  the power of WordPress and React (based on https://www.ibenic.com/quiz-wordpress-rest-api-react-scripts-tool/)
Version: 1.0.0
Author: Ido Barnea @ barbareshet
Author URI: https://www.barbareshet.co.il
License: GPLv2 or later
Text Domain: ido_wpqp
*/

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}
class WPQP {
	// ...
	public function __construct() {
		$this->ido_quiz_init();
	}

	/**
	 * Load everything
	 * @return void
	 */
	public function ido_quiz_init() {
		add_action( 'init', array( $this, 'load_cpts' ) );
		add_action( 'rest_api_init', array( 'wpqp_REST_API', 'register_routes' ) );

		// Adding our shortcode
		add_shortcode( 'wpqp', array( $this, 'ido_quiz_shortcode' ) );

		// Hooking our enqueue method.
		add_action( 'wp_enqueue_scripts', array( $this, 'ido_quiz_scripts' ) );

		if ( is_admin() ) {
			add_action( 'add_meta_boxes', array( $this, 'register_metaboxes' ) );
			add_action( 'save_post', array( $this, 'save_metaboxes' ), 20, 2 );
		}
	}

	/**
	 * WPQP shortcode
	 * @return string
	 */
	public function ido_quiz_shortcode(){

		return '<div id="wpqp" class="wpqp"></div>';
	}

	public function ido_quiz_scripts(){

		if ( is_singular( array( 'post', 'page' ) ) ) {
			global $post;

			// If current page/post has our shortcode wpqp, proceed.
			if ( has_shortcode( $post->post_content, 'wpqp' ) ) {

				require 'inc/react-wp/react-wp-scripts.php';
				\WPQP\enqueue_assets( plugin_dir_path( __FILE__ ) .'inc/react-wp', array(
					'handle' => 'wpqp',
					// Production URL.
					'base_url' => plugin_dir_url( __FILE__ ) . 'inc/react-wp/',
				) );

				wp_localize_script( 'wpqp', 'wpqp', array(
					'nonce' => wp_create_nonce( 'wp_rest' ),
					'rest_url' => get_rest_url(),
				) );
			}
		}
	}
	public function load_cpts(){

	}
}
new WPQP();