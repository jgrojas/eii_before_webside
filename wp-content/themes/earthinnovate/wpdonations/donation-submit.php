<?php
/**
 * donation Submission Form
 */
if ( ! defined( 'ABSPATH' ) OR ( isset( $_GET['action'] ) && $_GET['action'] == 'edit' ) ) exit;

global $wpdonations;
?>
<div class="form-wrap col span_5_of_8">
	<form action="<?php echo $action; ?>" method="post" id="submit-donation-form" class="wpdonations-form clearfix" enctype="multipart/form-data">

		<?php if ( wpdonations_user_can_post_donation() ) : ?>

			<p class="nonprofit"><strong><?php echo wp_kses( __('Earth Innovation Institute is a 501(c)(3) nonprofit organization. <br />Your contribution is tax-deductible to the extent allowed by law.', 'earthinnovate'), array('br' => array() ) ); ?></strong></p>
			<!-- Donation Information Fields -->
			<?php do_action( 'submit_donation_form_donation_fields_start' ); ?>
			<div class="donation-fields clearfix">
			<?php foreach ( $donation_fields as $key => $field ) : ?>
				<?php $amount = ( $key == 'donation_amount' ? true : false );  ?>
				<fieldset class="fieldset-<?php esc_attr_e( $key ); if (!$amount) echo ' two-column'; ?>">
					<label for="<?php esc_attr_e( $key ); ?>"><?php echo $field['label'] . ( $field['required'] ? '*' : '' ) . ( $amount ? ' ('. get_option( 'wpdonations_currency', false ) .')' : '' ); ?></label>
					<div class="field <?php if ($amount) echo 'group section'; ?>">
						<?php get_wpdonations_template( 'form-fields/' . $field['type'] . '-field.php', array( 'key' => $key, 'field' => $field ) ); ?>
					</div>
				</fieldset>
			<?php endforeach; ?>
			</div>
			<?php do_action( 'submit_donation_form_donation_fields_end' ); ?>

			<!-- Donor Information Fields -->
			<div class="donor-fields clearfix">
				<h4><?php _e( 'Your Information', 'wpdonations' ); ?></h4>
<!--
				<?php if ( apply_filters( 'submit_donation_form_show_signin', true ) ) : ?>

					<?php get_wpdonations_template( 'account-signin.php' ); ?>

				<?php endif; ?>
-->

				<?php do_action( 'submit_donation_form_donor_fields_start' ); ?>

				<?php foreach ( $donor_fields as $key => $field ) : ?>
					<?php $address = ( $key == 'donor_address' ? true : false ); ?>
					<fieldset class="fieldset-<?php esc_attr_e( $key ); if (!$address) echo ' two-column'; ?>">
						<label for="<?php esc_attr_e( $key ); ?>"><?php echo $field['label'] . ( $field['required'] ? '*' : '' ); ?></label>
						<div class="field">
							<?php get_wpdonations_template( 'form-fields/' . $field['type'] . '-field.php', array( 'key' => $key, 'field' => $field ) ); ?>
						</div>
					</fieldset>
				<?php endforeach; ?>
			</div>
			<?php do_action( 'submit_donation_form_donor_fields_end' ); ?>

			<p>
				<?php wp_nonce_field( 'submit_form_posted' ); ?>
				<input type="hidden" name="wpdonations_form" value="<?php echo $form; ?>" />
				<input type="hidden" name="donation_id" value="<?php echo esc_attr( $donation_id ); ?>" />
				<input type="submit" name="submit_donation" class="button more-link" value="<?php esc_attr_e( $submit_button_text ); ?>" />
			</p>

			<!-- PayPal Logo -->
			<table border="0" cellpadding="10" cellspacing="0" align="center" class="paypal-logo">
				<tr>
					<td align="center"></td>
				</tr>
				<tr>
					<td align="center"><a href="https://www.paypal.com/webapps/mpp/paypal-popup" title="How PayPal Works" onclick="javascript:window.open('https://www.paypal.com/webapps/mpp/paypal-popup','WIPaypal','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=1060, height=700'); return false;"><img src="https://www.paypalobjects.com/webstatic/mktg/logo/AM_mc_vs_dc_ae.jpg" border="0" alt="PayPal Acceptance Mark"></a>
					</td>
				</tr>
			</table>
			<!-- PayPal Logo -->

		<?php else : ?>

			<?php do_action( 'submit_donation_form_disabled' ); ?>

		<?php endif; ?>
	</form>

	<div class="pay-by-check">
		<p>
			<?php echo wp_kses( __('To donate by check, please make your check payable to <strong>Earth Innovation Institute</strong> and mail to: <address>Earth Innovation Institute <br />200 Green Street, Suite 1, <br />San Francisco, CA 94111 <br />USA</address>', 'earthinnovate'), array( 'strong' => array(), 'address' => array( 'br' => array() ) ) ); ?>
		</p>
	</div>

</div><!-- end .form-wrap -->