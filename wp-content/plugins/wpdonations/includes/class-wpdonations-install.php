<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WPdonations_Install
 */
class WPdonations_Install {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		
		$this->is_installed();
		$this->init_user_roles();
		$this->default_terms();
	}
	
	/**
	 * is_installed function.
	 *
	 * @access public
	 * @return void
	 */
	public function is_installed() {
		if ( get_option( 'wpdonations_is_installed' ) == 1 )
			return;

		update_option( 'wpdonations_is_installed', -1 );
	}

	/**
	 * Init user roles
	 *
	 * @access public
	 * @return void
	 */
	public function init_user_roles() {
		global $wp_roles;

		if ( class_exists( 'WP_Roles' ) && ! isset( $wp_roles ) )
			$wp_roles = new WP_Roles();

		if ( is_object( $wp_roles ) ) {
			$wp_roles->add_cap( 'administrator', 'manage_donations' );
		}
	}

	/**
	 * default_terms function.
	 *
	 * @access public
	 * @return void
	 */
	public function default_terms() {
		if ( get_option( 'wpdonations_installed_terms' ) == 1 )
			return;

		$taxonomies = array(
			'donation_campaign' => array()
		);

		foreach ( $taxonomies as $taxonomy => $terms ) {
			foreach ( $terms as $term ) {
				if ( ! get_term_by( 'slug', sanitize_title( $term ), $taxonomy ) ) {
					wp_insert_term( $term, $taxonomy );
				}
			}
		}

		update_option( 'wpdonations_installed_terms', 1 );
	}

}

new WPdonations_Install();