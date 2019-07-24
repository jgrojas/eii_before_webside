<?php
/**
 * WPdonations_Emails class.
 */
class WPdonations_Emails {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'send-donor-email', array( $this, 'send_donor_email' ) );
	}
	
	/**
	 * Send an alert
	 */
	public function send_donor_email( $donation_id ) {
		$donation = get_post( $donation_id );

		if ( ! $donation )
			return;

		$user  = get_user_by( 'id', $donation->post_author );
		$email = $this->format_email( $user, $donation_id );

		add_filter( 'wp_mail_from_name', array( $this, 'mail_from_name' ) );

		if ( $email )
			wp_mail( $user->user_email, apply_filters( 'wpdonations_donor_email_subject', sprintf( __( 'Your Donation "%s"', 'wpdonations' ), $donation->post_title ), $donation ), $email );

		remove_filter( 'wp_mail_from_name', array( $this, 'mail_from_name' ) );

	}

	/**
	 * Return the default email content for alerts
	 */
	public function get_default_email() {
		return "Dear {display_name},

Thank you for your generous donation of {donation_amount} on {donation_date}. Your willingness to help us is deeply appreciated and we would like to thank you very much for your contribution.

Frankly, we could not do this without you. We, and those we serve, deeply appreciate your generosity.

Sincerely,

Our Team";
	}
	
	/**
	 * Format the email
	 */
	public function format_email( $user, $donation_id ) {
	
		$donation = get_post( $donation_id );

		$template = get_option( 'wpdonations_donor_email_template' );

		if ( ! $template ) {
			$template = $this->get_default_email();
		}

		ob_start();
		get_wpdonations_template( 'content-donor_email.php', array(), 'wpdonations', WPDONATIONS_PLUGIN_DIR . '/templates/' );
		$donation_details = ob_get_clean();

		$replacements = array(
			'{display_name}'     => $user->display_name,
			'{donation_amount}'  => get_the_donation_formatted_amount( $donation ),
			'{donation_date}'    => date_i18n( get_option('date_format'), strtotime( $donation->post_date ) ),
			'{donation_details}' => $donation_details
		);

		$template = str_replace( array_keys( $replacements ), array_values( $replacements ), $template );

		return apply_filters( 'wpdonations_donor_email_template', $template );
	}

	/**
	 * From name
	 */
	public function mail_from_name( $name ) {
	    return get_bloginfo( 'name' );
	}


}

$GLOBALS['wpdonations_emails'] = new WPdonations_Emails();
