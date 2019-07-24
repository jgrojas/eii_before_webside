<?php
/*
Plugin Name: WPdonations
Plugin URI: http://remicorson.com/wpdonations
Description: The easiest way to collect funds using WordPress
Version: 2.0.1
Author: Remi Corson
Author URI: http://remicorson.com
Requires at least: 3.5
Tested up to: 3.9.1
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * WPdonations class.
 */
class WPdonations {

	/**
	 * __construct function.
	 */
	public function __construct() {
		// Define constants
		define( 'WPDONATIONS_VERSION', '2.0.1' );
		define( 'WPDONATIONS_PLUGIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
		define( 'WPDONATIONS_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );

		// Includes
		if ( is_admin() ) {
			include( 'includes/admin/class-wpdonations-admin.php' );
		}
		include( 'wpdonations-functions.php' );
		include( 'wpdonations-template.php' );
		include( 'includes/class-wpdonations-post-types.php' );
		include( 'includes/class-wpdonations-taxonomies.php' );
		include( 'includes/class-wpdonations-shortcodes.php' );
		include( 'includes/class-wpdonations-api.php' );
		include( 'includes/class-wpdonations-forms.php' );
		include( 'includes/class-wpdonations-emails.php' );
		include( 'includes/class-wpdonations-payments.php' );

		// Init classes
		$this->forms      = new WPdonations_Forms();
		$this->post_types = new WPdonations_Post_Types();
		$this->emails     = new WPdonations_Emails();

		// Activation - works with symlinks
		register_activation_hook( basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ), array( $this, 'plugin_activate' ) );
		register_activation_hook( basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ), array( $this->post_types, 'register_post_types' ), 10 );
		register_activation_hook( basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ), create_function( "", "include( 'includes/class-wpdonations-install.php' );" ), 10 );
		register_activation_hook( basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ), 'flush_rewrite_rules', 15 );
		
		// Deactivation
		register_deactivation_hook( basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ), create_function( "", "include( 'uninstall.php' );" ), 10 );

		// Actions
		add_action('admin_init', array( $this, 'plugin_redirect' ) );
		add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'action_links' ) );
		add_action( 'switch_theme', array( $this->post_types, 'register_post_types' ), 10 );
		add_action( 'switch_theme', 'flush_rewrite_rules', 15 );
		add_action( 'widgets_init', create_function( "", "include_once( 'includes/class-wpdonations-widgets.php' );" ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );
		add_action( 'admin_notices', array( $this, 'configuration_admin_notice' ) );
	}
	
	/**
	 * Run on plugin activation
	 *
	 * @access private
	 * @return void
	 */
	public function plugin_activate() {
	    add_option('do_wpdonations_activation_redirect', true);
	}
	
	/**
	 * Redirect to welcome page after activation
	 *
	 * @access private
	 * @return void
	 */
	public function plugin_redirect() {
	    if ( get_option('do_wpdonations_activation_redirect', false) ) {
	        delete_option('do_wpdonations_activation_redirect');
	        wp_redirect( admin_url( 'index.php?page=wpdonations-about' ) );
	        exit;
	    }
	}

	/**
	 * Localisation
	 *
	 * @access private
	 * @return void
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'wpdonations', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
	
	/**
	 * Show action links on the plugin screen
	 *
	 * @param mixed $links
	 * @return array
	 */
	public function action_links( $links ) {
		return array_merge( array(
			'<a href="' . admin_url( 'edit.php?post_type=donation&page=wpdonations-settings' ) . '">' . __( 'Settings', 'wpdonations' ) . '</a>',
			'<a href="' . esc_url( apply_filters( 'wpdonations_docs_url', 'http://docs.remicorson.com', 'wpdonations' ) ) . '" target="_blank">' . __( 'Documentation', 'wpdonations' ) . '</a>',
		), $links );
	}
	

	/**
	 * frontend_scripts function.
	 *
	 * @access public
	 * @return void
	 */
	public function frontend_scripts() {
		
		// Register Scripts
		wp_register_script( 'wpdonations-donation-dashboard', WPDONATIONS_PLUGIN_URL . '/assets/js/donation-dashboard.js', array( 'jquery' ), WPDONATIONS_VERSION, true );
		
		// Submission form Scripts
		wp_register_script( 'wpdonations-donation-submission-form', WPDONATIONS_PLUGIN_URL . '/assets/js/frontend.js', array( 'jquery' ), WPDONATIONS_VERSION, true );

		// Localize script
		wp_localize_script( 'wpdonations-donation-dashboard', 'wpdonations_donation_dashboard', array(
			'i18n_confirm_delete' => __( 'Are you sure you want to delete this donation?', 'wpdonations' )
		) );

		// Enqueue styles
		wp_enqueue_style( 'wpdonations-frontend', WPDONATIONS_PLUGIN_URL . '/assets/css/frontend.css' );
	}
	
	/**
	 * Display an admin notice, if not on the settings screen and if settings not set.
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function configuration_admin_notice () {
		if ( ( isset( $_GET['page'] ) && 'wpdonations-settings' == $_GET['page'] ) ) return;

		$wpdonations_installed = get_option( 'wpdonations_is_installed' );

		if ( -1 == $wpdonations_installed ) {
			$url = add_query_arg( 'post_type', 'donation', admin_url( 'edit.php' ) );
			$url = add_query_arg( 'page', 'wpdonations-settings', $url );
			echo '<div class="updated fade"><p>' . sprintf( __( '%sWPdonations is almost ready.%s To get started, %sconfigure your settings%s.', 'wpdonations' ), '<strong>', '</strong>', '<a href="' . esc_url( $url ) . '">', '</a>' ) . '</p></div>' . "\n";
		}
	}
}

$GLOBALS['wpdonations'] = new WPdonations();