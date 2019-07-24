<?php
switch ( $donation->post_status ) :
	case 'publish' :
		printf( __( 'Donation validated successfully. To view your donation <a href="%s">click here</a>.', 'wpdonations' ), get_permalink( $donation->ID ) );
	break;
	case 'pending_payment' :
		print( __( 'Donation submitted successfully. Your donation will be taken into account once as soon as we receive the payment gateway validation (it can take several minutes).', 'wpdonations' ) );
	break;
	case 'pending_off_payment' :
		print( __( 'Donation submitted successfully. Your donation will be taken into account once payment is received. You choose an offline payment method, so please follow the guide below to send us your payment.', 'wpdonations' ) );
		if ( get_option( 'wpdonations_offline_payment_text' ) ) {
			print( '<p>' . get_option( 'wpdonations_offline_payment_text' ) . '</p>' ); 
		}
	break;
endswitch;

do_action( 'wpdonations_donation_submitted_content_' . str_replace( '-', '_', sanitize_title( $donation->post_status ) ), $donation );