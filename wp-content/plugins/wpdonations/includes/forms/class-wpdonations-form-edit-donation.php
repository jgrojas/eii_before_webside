<?php

include_once( 'class-wpdonations-form-submit-donation.php' );

/**
 * WPdonations_Form_Edit_donation class.
 */
class WPdonations_Form_Edit_Donation extends WPdonations_Form_Submit_Donation {

	public static $form_name = 'edit-donation';

	/**
	 * Constructor
	 */
	public static function init() {
		self::$donation_id = ! empty( $_REQUEST['donation_id'] ) ? absint( $_REQUEST[ 'donation_id' ] ) : 0;
	}

	/**
	 * output function.
	 *
	 * @access public
	 * @return void
	 */
	public static function output() {
		self::submit_handler();
		self::submit();
	}

	/**
	 * Submit Step
	 */
	public static function submit() {
		global $wpdonations, $post;

		$donation = get_post( self::$donation_id );

		if ( empty( self::$donation_id  ) || $donation->post_status !== 'publish' ) {
			echo wpautop( __( 'Invalid donation', 'wpdonations' ) );
			return;
		}

		self::init_fields();

		foreach ( self::$fields as $group_key => $fields ) {
			foreach ( $fields as $key => $field ) {
				switch ( $key ) {
/*
					case 'donation_title' :
						if ( ! isset( self::$fields[ $group_key ][ $key ]['value'] ) )
							self::$fields[ $group_key ][ $key ]['value'] = $donation->post_title;
					break;
*/
					case 'donation_message' :
						if ( ! isset( self::$fields[ $group_key ][ $key ]['value'] ) )
							self::$fields[ $group_key ][ $key ]['value'] = $donation->post_content;
					break;
					case 'donation_campaign' :
						if ( ! isset( self::$fields[ $group_key ][ $key ]['value'] ) )
							self::$fields[ $group_key ][ $key ]['value'] = current( wp_get_object_terms( $donation->ID, 'donation_campaign', array( 'fields' => 'slugs' ) ) );
					break;
					case 'donation_category' :
						if ( ! isset( self::$fields[ $group_key ][ $key ]['value'] ) )
							self::$fields[ $group_key ][ $key ]['value'] = current( wp_get_object_terms( $donation->ID, 'donation_category', array( 'fields' => 'slugs' ) ) );
					break;
					default:
						if ( ! isset( self::$fields[ $group_key ][ $key ]['value'] ) )
							self::$fields[ $group_key ][ $key ]['value'] = get_post_meta( $donation->ID, '_' . $key, true );
					break;
				}
			}
		}

		self::$fields = apply_filters( 'submit_donation_form_fields_get_donation_data', self::$fields, $donation );

		get_wpdonations_template( 'donation-submit.php', array(
			'form'               => self::$form_name,
			'donation_id'        => self::get_donation_id(),
			'action'             => self::get_action(),
			'donation_fields'    => self::get_fields( 'donation' ),
			'donor_fields'       => self::get_fields( 'donor' ),
			'submit_button_text' => __( 'Update donation', 'wpdonations' )
			) );
	}

	/**
	 * Submit Step is posted
	 */
	public static function submit_handler() {
		if ( empty( $_POST['submit_donation'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'submit_form_posted' ) )
			return;

		try {

			// Get posted values
			$values = self::get_posted_fields();

			// Validate required
			if ( is_wp_error( ( $return = self::validate_fields( $values ) ) ) )
				throw new Exception( $return->get_error_message() );

			// Update the donation
			self::save_donation( $values['donation']['donation_description'], 'publish' );
			self::update_donation_data( $values );

			// Successful
			echo '<div class="wpdonations-message">' . __( 'Your changes have been saved.', 'wpdonations' ), ' <a href="' . get_permalink( self::$donation_id ) . '">' . __( 'View donation &rarr;', 'wpdonations' ) . '</a>' . '</div>';

		} catch ( Exception $e ) {
			echo '<div class="wpdonations-error">' . $e->getMessage() . '</div>';
			return;
		}
	}
}