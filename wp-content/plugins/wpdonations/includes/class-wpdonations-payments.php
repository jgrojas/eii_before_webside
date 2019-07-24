<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * WPdonations_payments class.
 */
class WPdonations_Payments {

	private $donation_id  = '';

	/**
	 * __construct function.
	 */
	public function __construct() {

		$this->plugin_slug 	= basename( dirname( __FILE__ ) );
		$this->donation_id  = ! empty( $_REQUEST['donation_id'] ) ? absint( $_REQUEST[ 'donation_id' ] ) : 0;
		
		$this->step   		= ! empty( $_REQUEST['step'] ) ? max( absint( $_REQUEST['step'] ), 0 ) : 0;

		add_action( 'init', array( $this, 'init' ), 12 );
		
		add_filter( 'the_donation_status', array( $this, 'the_donation_status' ), 10, 2 );
		add_filter( 'wpdonations_valid_submit_donation_statuses', array( $this, 'valid_submit_donation_statuses' ) );

		add_filter( 'submit_donation_steps', array( $this, 'submit_donation_steps' ), 10 );
		add_filter( 'submit_donation_step_preview_submit_text', array( $this, 'submit_button_text' ), 10 );
		add_action( 'wpdonations_donation_submitted_content_pending_payment', array( $this, 'donation_submitted' ), 10 );
			
		add_filter( 'wpdonations_settings', array( $this, 'settings' ) );
		add_action( 'wpdonations_api_' . get_class( $this ), array( $this, 'api_handler' ) );


		// Get the gateway we are using
		$this->gateway = $this->get_gateway();
	}

	/**
	 * Filter donation status name
	 *
	 * @param  string $nice_status
	 * @param  string $status
	 * @return string
	 */
	public function the_donation_status( $status, $donation ) {
		if ( $donation->post_status == 'pending_payment' )
			$status = __( 'Pending Payment', 'wpdonations' );
			
		if ( $donation->post_status == 'pending_off_payment' )
			$status = __( 'Pending Offline Payment', 'wpdonations' );
		return $status;
	}

	/**
	 * Ensure the submit form lets us continue to edit/process a donation with the pending_payment status
	 * @return array
	 */
	public function valid_submit_donation_statuses( $status ) {
		$status[] = 'pending_payment';
		$status[] = 'pending_off_payment';

		return $status;
	}

	/**
	 * Change the steps during the submission process
	 *
	 * @param  array $steps
	 * @return array
	 */
	public function submit_donation_steps( $steps ) {
		// We need to hijack the preview submission so we can take a payment
		$steps['preview']['handler'] = array( $this, 'preview_handler' );
		return $steps;
	}

	public function payment_result_page() {
		get_wpdonations_template( 'donation-submitted.php', array( 'donation' => $donation ) );
	}

	/**
	 * Handle the form when the preview page is submitted
	 */
	public function preview_handler() {
		if ( ! $_POST )
			return;

		// Edit = show submit form again
		if ( ! empty( $_POST['edit_donation'] ) ) {
			WPdonations_Form_Submit_donation::previous_step();
		}

		// Continue = Take Payment
		if ( ! empty( $_POST['continue'] ) ) {

			$donation = get_post( $this->donation_id );

			if ( $donation->post_status == 'preview' ) {
				$update_donation                = array();
				$update_donation['ID']          = $donation->ID;
				if( $donation->_donation_payment_type != 'offline' ) {
					$update_donation['post_status'] = 'pending_payment';
				} else {
					$update_donation['post_status'] = 'pending_off_payment';
				}
				wp_update_post( $update_donation );
			}

			
			if ( $donation->_donation_payment_type != 'offline' && $this->gateway->pay_for_donation( $this->donation_id ) ) {
				// If pay for donation returns true we can proceed, otherwise stay in preview mode
				WPdonations_Form_Submit_donation::next_step();
			} else {
				wp_redirect( add_query_arg( array( 'success' => 'true', 'donation_id' => $donation->ID, 'step' => $_REQUEST['step'] + 1 ), get_permalink() ) );
			}
		}

	}

	/**
	 * Change submit button text
	 * @return string
	 */
	public function submit_button_text() {

		$donation = get_post( $this->donation_id );
		
		if ( $donation->_donation_payment_type == 'online' ) {
			return __( 'Proceed to payment &rarr;', 'wpdonations' );
		} else {
			return __( 'Submit donation &rarr;', 'wpdonations' );
		}
		
		return __( 'Proceed to payment &rarr;', 'wpdonations' );
	}

	/**
	 * Show a message if pending payment when the done step is reached
	 */
	public function donation_submitted( $donation ) {

		$this->gateway->return_handler();

	}

	/**
	 * API Handler
	 * @return [type]
	 */
	function api_handler() {
		if ( ! empty( $_GET['gateway'] ) ) {
			$gateway = $this->get_gateway( $_GET['gateway'] );
			$gateway->api_handler();
		}
	}

	/**
	 * Localisation
	 *
	 * @access private
	 * @return void
	 */
	public function init() {
		global $wpdonations;

		load_plugin_textdomain( 'wpdonations', false, dirname( plugin_basename( __FILE__ ) ) );

		register_post_status( 'pending_payment', array(
			'label'                     => _x( 'Pending Payment', 'donation_status', 'wpdonations' ),
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Pending Payment <span class="count">(%s)</span>', 'Pending Payment <span class="count">(%s)</span>', 'wpdonations' ),
		) );
		
		register_post_status( 'pending_off_payment', array(
			'label'                     => _x( 'Pending Offline Payment', 'donation_status', 'wpdonations' ),
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Pending Offline Payment <span class="count">(%s)</span>', 'Pending Offline Payment <span class="count">(%s)</span>', 'wpdonations' ),
		) );

	}

	/**
	 * Get configured gateway
	 *
	 * @return class Gateway
	 */
	public function get_gateway( $gateway = '' ) {

		if ( ! $gateway )
			$gateway   = get_option( 'wpdonations_gateway', 'paypal' );
		$gateway_class = apply_filters( 'wpdonations_gateway_class', 'WPdonations_' . $gateway );

		include_once( WPDONATIONS_PLUGIN_DIR . '/gateways/abstract-class-wpdonations-gateways.php' );

		if ( ! class_exists( $gateway_class ) )
			return include( WPDONATIONS_PLUGIN_DIR . '/gateways/class-' . str_replace( '_', '-', strtolower( $gateway_class ) ) . '.php' );

		return new $gateway_class;
	}

	/**
	 * Include gateways
	 */
	public function include_gateways() {
		include_once( WPDONATIONS_PLUGIN_DIR . '/gateways/abstract-class-wpdonations-gateways.php' );
		include_once( WPDONATIONS_PLUGIN_DIR . '/gateways/class-wpdonations-paypal.php' );
	}

	/**
	 * Add Settings
	 * @param  array $settings
	 * @return array
	 */
	public function settings( $settings = array() ) {
		$this->include_gateways();

		add_action( 'admin_footer', array( $this, 'settings_js' ) );

		$settings['payment_gateways'] = array(
			__( 'Payment Gateways', 'wpdonations' ),
			apply_filters(
				'wpdonations_payments_settings',
				array(
					array(
						'name' 		=> 'wpdonations_currency',
						'std' 		=> 'USD',
						'label' 	=> __( 'Currency Code', 'wpdonations' ),
						'desc'		=> __( 'Enter the currency code you wish to use. E.g. for US dollars enter <code>USD</code>. Your gateway must support your input currency for payments to work.', 'wpdonations' ),
						'type'      => 'input'
					),
					array(
						'name' 		=> 'wpdonations_offline_payment_text',
						'std' 		=> '',
						'label' 	=> __( 'Offline Payment Instructions', 'wpdonations' ),
						'desc'		=> __( 'Please enter the instructions for your donors to send you the offline payments (BACS, cheque etc...)', 'wpdonations' ),
						'type'      => 'textarea'
					),
					array(
						'name' 		=> 'wpdonations_gateway',
						'std' 		=> 'paypal',
						'label' 	=> __( 'Payment Gateway', 'wpdonations' ),
						'desc'		=> __( 'Choose the gateway to use for collecting funds. Depending on the gateway you choose you should ensure your Submit donation page is served over HTTPS. You can use <a href="http://wordpress.org/plugins/wordpress-https/">WordPress HTTPS</a> to do this.', 'wpdonations' ),
						'options'   => apply_filters( 'wpdonations_gateways', array() ),
						'type'      => 'select'
					)
				)
			)
		);
		return $settings;
	}

	/**
	 * After settings
	 */
	public function settings_js() {
		?>
		<script type="text/javascript">
			jQuery('select#setting-wpdonations_gateway').change(function() {
				jQuery(this).closest('form').find( 'tr.gateway-settings' ).hide();
				jQuery(this).closest('form').find( 'tr.gateway-settings-' + jQuery(this).val() ).show();
			}).change();

			jQuery('.nav-tab-wrapper a:first').click();
		</script>
		<?php
	}

}

new WPdonations_Payments();