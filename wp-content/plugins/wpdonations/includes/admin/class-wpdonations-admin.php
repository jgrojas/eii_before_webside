<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WPdonations_Admin class.
 */
class WPdonations_Admin {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		include_once( 'class-wpdonations-cpt.php' );
		include_once( 'class-wpdonations-settings.php' );
		include_once( 'class-wpdonations-writepanels.php' );
		include_once( 'class-wpdonations-admin-welcome.php' );

		$this->settings_page = new WPdonations_Settings();

		add_action( 'admin_menu', array( $this, 'admin_menu' ), 12 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
	}

	/**
	 * admin_enqueue_scripts function.
	 *
	 * @access public
	 * @return void
	 */
	public function admin_enqueue_scripts() {
		global $wpdonations;

		wp_enqueue_style( 'wpdonations_admin_css', WPDONATIONS_PLUGIN_URL . '/assets/css/admin.css' );
		wp_register_script( 'jquery-tiptip', WPDONATIONS_PLUGIN_URL. '/assets/js/jquery-tiptip/jquery.tipTip.js', array( 'jquery' ), WPDONATIONS_VERSION, true );
		wp_enqueue_script( 'wpdonations_admin_js', WPDONATIONS_PLUGIN_URL. '/assets/js/admin.js', array( 'jquery', 'jquery-tiptip' ), WPDONATIONS_VERSION, true );
	}

	/**
	 * admin_menu function.
	 *
	 * @access public
	 * @return void
	 */
	public function admin_menu() {
		add_submenu_page( 'edit.php?post_type=donation', __( 'Settings', 'wpdonations' ), __( 'Settings', 'wpdonations' ), 'manage_options', 'wpdonations-settings', array( $this->settings_page, 'output' ) );
		
		if ( apply_filters( 'wpdonations_show_addons_page', true ) )
			add_submenu_page(  'edit.php?post_type=donation', __( 'WPdonations Add-ons', 'wpdonations' ),  __( 'Add-ons', 'wpdonations' ) , 'manage_options', 'wpdonations-addons', array( $this, 'addons_page' ) );

	}

	/**
	 * Output addons page
	 */
	public function addons_page() {
		$addons = include( 'class-wpdonations-addons.php' );
		$addons->output();
	}
}

new WPdonations_Admin();