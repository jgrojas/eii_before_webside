<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * WPdonations_Gateway class.
 */
abstract class WPdonations_Gateway {

	protected $settings     = array();
	protected $gateway_id   = '';
	protected $gateway_name = '';

	/**
	 * __construct function.
	 */
	public function __construct() {
		add_filter( 'wpdonations_gateways', array( $this, 'add_gateway' ) );
		add_filter( 'wpdonations_payments_settings', array( $this, 'add_settings' ) );
	}

	/**
	 * Add gateway to settings page
	 *
	 * @param array $gateways
	 */
	public function add_gateway( $gateways ) {
		$gateways[ $this->gateway_id ] = $this->gateway_name;
		return $gateways;
	}

	/**
	 * Add settings for the gateway
	 *
	 * @param array $settings
	 */
	public function add_settings( $settings ) {
		return array_merge( $settings, $this->settings );
	}

	/**
	 * Handle API calls (optional - used for IPN)
	 */
	public function api_handler() {}

	/**
	 * Handle the return page (optional - used for getting return values from gateways if posted)
	 */
	public function return_handler() {}

	/**
	 * Pay for a donations action
	 */
	public function pay_for_donation( $donation_id ) {
		return false;
	}

	/**
	 * Payment is complete - update donation
	 *
	 * @param  int $donations_id
	 */
	public function payment_complete( $donation_id ) {
	
		$notification = new WPdonations_Emails();
		$notification->send_donor_email( $donation_id );
		
		$donation = get_post( $donation_id );
		if ( $donation->post_status == 'pending_payment' ) {
			$update_donation                = array();
			$update_donation['ID']          = $donation_id;
			$update_donation['post_status'] = 'publish';
			wp_update_post( $update_donation );
		}
	}

	/**
	 * Send a message to admin about payment
	 *
	 * @param  int $donations_id
	 * @param  string $message
	 */
	public function send_admin_email( $donation_id, $message ) {
		$message = "Hi,\n\n" . $message . "\n\nView this donation: " . admin_url( 'post.php?post=' . $donation_id . '&action=edit' );

		wp_mail( get_option( 'admin_email'), sprintf( __('Donation #%d Payment Update', 'donations' ), $donation_id ), $message );
	}
}