<div class="col span_5_of_8">
	<?php
	$donation_submitted_text = '<p>Donation submitted successfully. Your donation will be taken into account as soon as we receive the payment gateway validation (it can take several minutes).</p><p>Thank you for supporting Earth Innovation Institute. Your donation is helping us to address the most pressing challenges of our time. Sign-up for our mailing list to follow the progress!</p>';

	switch ( $donation->post_status ) :
		case 'publish' :
			print( wp_kses( __( $donation_submitted_text, 'wpdonations' ), array('p' => array() ) ) );
		break;
		case 'pending_payment' :
			print( wp_kses( __( $donation_submitted_text, 'wpdonations' ), array('p' => array() ) ) );
		break;
		case 'pending_off_payment' :
			print( __( 'Donation submitted successfully. Your donation will be taken into account once payment is received. You choose an offline payment method, so please follow the guide below to send us your payment.', 'wpdonations' ) );
			if ( get_option( 'wpdonations_offline_payment_text' ) ) {
				print( '<p>' . get_option( 'wpdonations_offline_payment_text' ) . '</p>' );
			}
		break;
	endswitch;

	do_action( 'wpdonations_donation_submitted_content_' . str_replace( '-', '_', sanitize_title( $donation->post_status ) ), $donation ); ?>
</div>