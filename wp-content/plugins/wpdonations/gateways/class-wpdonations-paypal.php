<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * WPdonations_PayPal
 */
class WPdonations_PayPal extends WPdonations_Gateway {

	private $liveurl = 'https://www.paypal.com/cgi-bin/webscr';
	private $testurl = 'https://www.sandbox.paypal.com/cgi-bin/webscr';

	/**
	 * __construct function.
	 */
	public function __construct() {
		$this->gateway_id   = 'paypal';
		$this->gateway_name = __( 'PayPal Standard', 'wpdonations' );
		$this->settings     = array(
			array(
				'name' 		=> 'wpdonations_paypal_email',
				'std' 		=> '',
				'label' 	=> __( 'PayPal Email', 'wpdonations' ),
				'desc'		=> __( 'Your PayPal email address.', 'wpdonations' ),
				'type'      => 'input',
				'class'     => 'gateway-settings gateway-settings-paypal'
			),
			array(
				'name' 		=> 'wpdonations_paypal_identity_token',
				'std' 		=> '',
				'label' 	=> __( 'PayPal Identity Token', 'wpdonations' ),
				'desc'		=> __( 'Optionally enable "Payment Data Transfer" (Profile > Website Payment Preferences) and then copy your identity token here. This will allow payments to be verified without the need for PayPal IPN.', 'wpdonations' ),
				'type'      => 'input',
				'class'     => 'gateway-settings gateway-settings-paypal'
			),
			array(
				'name' 		=> 'wpdonations_paypal_sandbox',
				'std' 		=> 'no',
				'label' 	=> __( 'PayPal Sandbox', 'wpdonations' ),
				'desc'		=> __( 'Enable PayPal Sandbox (used for testing)', 'wpdonations' ),
				'options'   => array(
					'yes' => __( 'Yes', 'wpdonations' ),
					'no' => __( 'No', 'wpdonations' ),
				),
				'type'      => 'select',
				'class'     => 'gateway-settings gateway-settings-paypal'
			)
		);
		parent::__construct();
		
	}

	/**
	 * Pay for a donation action
	 */
	public function pay_for_donation( $donation_id ) {
	
		if( is_recurring_donation( $donation_id ) )
			add_filter( 'wpdonations_paypal_args', array( $this, 'paypal_recurring_args' ) );
			
		$payment_link = $this->get_paypal_payment_link( $donation_id );
		wp_redirect( $payment_link );
		exit;
	}

	/**
	 * Get PayPal payment link
	 * @return string
	 */
	private function get_paypal_payment_link( $donation_id ) {
	
		$paypal_args = apply_filters( 'wpdonations_paypal_args', array(
			'cmd'           => '_cart',
			'business'      => get_option( 'wpdonations_paypal_email' ),
			'currency_code' => get_option( 'wpdonations_currency' ),
			'charset'       => 'UTF-8',
			'rm'            => 2,
			'upload'        => 1,
			'no_note'       => 1,
			'return'        => add_query_arg( array( 'success' => 'true', 'donation_id' => $donation_id, 'step' => $_REQUEST['step'] + 1 ), get_permalink() ),
			'cancel_return' => add_query_arg( array( 'cancel' => 'true', 'donation_id' => $donation_id, 'step' => $_REQUEST['step'] ), get_permalink() ),
			'invoice'       => strtoupper( str_replace( ' ', '-', get_bloginfo( 'name' ) ) ) . '-DONATION-' . $donation_id,
			'custom'        => $donation_id,
			'notify_url'    => add_query_arg( array( 'wpdonations-api' => 'WPdonations_Payments', 'gateway' => $this->gateway_id ), home_url() ),
			'no_shipping'   => 1,
			'item_name_1'   => get_the_title( $donation_id ),
			'quantity_1'    => 1,
			'amount_1'      => number_format( get_the_donation_amount( $donation_id ), 2, '.', '' )
		) );

		$paypal_args = http_build_query( $paypal_args, '', '&' );

		if ( get_option( 'wpdonations_paypal_sandbox' ) == 'yes' )
			$paypal_adr = $this->testurl . '?test_ipn=1&';
		else
			$paypal_adr = $this->liveurl . '?';

		return $paypal_adr . $paypal_args;
	}

	/**
	 * Get PayPal recurring arguments
	 * @return string
	 */
	public function paypal_recurring_args( $paypal_args ) {
	
		global $post;
		
		$donation_id = $_POST['donation_id'];
		
		unset( $paypal_args['amount_1'] );
		unset( $paypal_args['item_name_1'] );
		
		$paypal_args['cmd'] = '_xclick-subscriptions';
		$paypal_args['item_name'] = __( 'New recurring donation', 'wpdonations' ) . ' &quot;' . get_the_title( $donation_id ) . '&quot;';
		$paypal_args['src'] = 1;
		$paypal_args['sra'] = 1;
		$paypal_args['a3'] = number_format( get_the_donation_amount( $donation_id ), 2, '.', '' );
		$paypal_args['p3'] = 1;
		$paypal_args['t3'] = $this->get_the_paypal_recurrence_period( $donation_id );
		
		return $paypal_args;
	}
	
	/**
	 * get_the_paypal_recurrence_period function.
	 *
	 * @access public
	 * @return void
	 */
	public function get_the_paypal_recurrence_period( $donation_id = null ) {
		
		$recurrence_period = get_post_meta( $donation_id, '_recurrence_period', true );
		
		switch ( $recurrence_period ) {
			case 'weekly' :
				$_recurrence_period = 'W';
			break;
			case 'monthly' :
				$_recurrence_period = 'M';
			break;
			case 'yearly' :
				$_recurrence_period = 'Y';
			break;
		}
	
		return ( $recurrence_period != '' ) ? $_recurrence_period : '';
	}

	/**
	 * API Handler
	 */
	public function api_handler() {
	
		if ( get_option( 'wpdonations_paypal_sandbox' ) == 'yes' )
			$paypal_adr = $this->testurl . '?test_ipn=1&';
		else
			$paypal_adr = $this->liveurl . '?';

    	// Get recieved values from post data
		$received_values = array( 'cmd' => '_notify-validate' );
		$received_values += stripslashes_deep( $_POST );

        // Send back post vars to paypal
        $params = array(
        	'body' 			=> $received_values,
        	'sslverify' 	=> false,
        	'timeout' 		=> 60,
        	'user-agent'	=> 'WPdonations',
        	'httpversion'   => '1.1',
        	'headers'       => array( 'host' => 'www.paypal.com' )
        );

		// Post back to get a response
        $response = wp_remote_post( $paypal_adr, $params );

        // check to see if the request was valid
        if ( ! is_wp_error( $response ) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 && ( strcmp( $response['body'], "VERIFIED" ) == 0 ) ) {

        	$this->valid_paypal_ipn_request();
        }
    }

    /**
     * Return handler
     *
     * Alternative to IPN
     */
    public function return_handler() {
		$posted = stripslashes_deep( $_REQUEST );

	    if ( ! empty( $posted['cm'] ) ) {

	    	$donation_id = absint( $posted['cm'] );
	    	$donation    = get_post( $donation_id );

	    	$posted['st'] = strtolower( $posted['st'] );

			switch ( $posted['st'] ) {
            	case 'completed' :

            		// Only complete pending_payment donations
            		if ( $donation->post_status != 'pending_payment' )
            			return false;

				    // Validate transaction
				    if ( get_option( 'wpdonations_paypal_sandbox' ) == 'yes' )
						$paypal_adr = $this->testurl;
					else
						$paypal_adr = $this->liveurl;

			        $pdt = array(
			        	'body' 			=> array(
			        		'cmd' => '_notify-synch',
			        		'tx'  => $posted['tx'],
			        		'at'  => get_option( 'wpdonations_paypal_identity_token' )
			        	),
			        	'sslverify' 	=> false,
			        	'timeout' 		=> 60,
			        	'user-agent'	=> 'WPdonations',
			        	'httpversion'   => '1.1',
        				'headers'       => array( 'host' => 'www.paypal.com' )
			        );

					// Post back to get a response
			        $response = wp_remote_post( $paypal_adr, $pdt );

			        if ( is_wp_error( $response ) )
			        	return false;

			        if ( ! strpos( $response['body'], "SUCCESS" ) === 0 )
			        	return false;

					// Store PP Details
					update_post_meta( $donation_id, 'Transaction ID', $posted['tx'] );

	                // Change the donation status
	               	$this->payment_complete( $donation_id );
	               	
					// Notify admin
					$this->send_admin_email( $donation_id, sprintf( __( "Payment has been received in full for donation #%d - this donation has been published.", 'wpdonations' ), $donation_id ) );

	            break;
	        }
        }
    }

    /**
     * Triggered when a valid IPN request comes in
     */
    public function valid_paypal_ipn_request() {
		$posted = stripslashes_deep( $_POST );
		
	    if ( ! empty( $posted['custom'] ) ) {

	    	$donation_id = absint( $posted['custom'] );
	    	$donation    = get_post( $donation_id );

	    	$posted['payment_status'] 	= strtolower( $posted['payment_status'] );

	    	if ( $posted['test_ipn'] == 1 && $posted['payment_status'] == 'pending' )
        		$posted['payment_status'] = 'completed';

			switch ( $posted['payment_status'] ) {
            	case 'completed' :

            		// Only complete pending_payment donations
            		if ( $donation->post_status != 'pending_payment' )
            			return false;

					// Store PP Details
	            	update_post_meta( $donation_id, 'Payer PayPal address', $posted['payer_email'] );
					update_post_meta( $donation_id, 'Transaction ID', $posted['txn_id'] );

					// Notify admin
					$this->send_admin_email( $donation_id, sprintf( __( "Payment has been received in full for donation #%d - this donation has been published.", 'wpdonations' ), $donation_id ) );

	                // Change the donation status
	               	$this->gateway->payment_complete( $donation_id );

	            break;
	            case 'pending' :

	            	$this->send_admin_email( $donation_id, sprintf( __( "PayPal payment is pending for donation #%d - this donation has *not* been automatically approved.", 'wpdonations' ), $donation_id ) );

	            break;
	            case "reversed" :
	            case "chargeback" :

	            	$this->send_admin_email( $donation_id, sprintf( __( "The payment for donation #%d was reversed. Please check this donation/payment and change the status manually if need be.", 'wpdonations' ), $donation_id ) );

	            break;
	        }
        }
		exit;
    }
}

return new WPdonations_PayPal();