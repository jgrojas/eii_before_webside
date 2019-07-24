<?php
/**
 * donation Submission Form
 */
if ( ! defined( 'ABSPATH' ) OR ( isset( $_GET['action'] ) && $_GET['action'] == 'edit' ) ) exit;

global $wpdonations;
?>
<form action="<?php echo $action; ?>" method="post" id="submit-donation-form" class="wpdonations-form" enctype="multipart/form-data">

	<?php if ( wpdonations_user_can_post_donation() ) : ?>

		<!-- Donation Information Fields -->
		<h2><?php _e( 'Donation & payment details', 'wpdonations' ); ?></h2>
		
		<?php do_action( 'submit_donation_form_donation_fields_start' ); ?>

		<?php foreach ( $donation_fields as $key => $field ) : ?>
			<fieldset class="fieldset-<?php esc_attr_e( $key ); ?>">
				<label for="<?php esc_attr_e( $key ); ?>"><?php echo $field['label'] . ( $field['required'] ? '' : ' <small>' . __( '(optional)', 'wpdonations' ) . '</small>' ); ?></label>
				<div class="field">
					<?php get_wpdonations_template( 'form-fields/' . $field['type'] . '-field.php', array( 'key' => $key, 'field' => $field ) ); ?>
				</div>
			</fieldset>
		<?php endforeach; ?>

		<?php do_action( 'submit_donation_form_donation_fields_end' ); ?>

		<!-- Donor Information Fields -->
		<h2><?php _e( 'Donor details', 'wpdonations' ); ?></h2>
		
		<?php if ( apply_filters( 'submit_donation_form_show_signin', true ) ) : ?>
	
			<?php get_wpdonations_template( 'account-signin.php' ); ?>
	
		<?php endif; ?>

		<?php do_action( 'submit_donation_form_donor_fields_start' ); ?>

		<?php foreach ( $donor_fields as $key => $field ) : ?>
			<fieldset class="fieldset-<?php esc_attr_e( $key ); ?>">
				<label for="<?php esc_attr_e( $key ); ?>"><?php echo $field['label'] . ( $field['required'] ? '' : ' <small>' . __( '(optional)', 'wpdonations' ) . '</small>' ); ?></label>
				<div class="field">
					<?php get_wpdonations_template( 'form-fields/' . $field['type'] . '-field.php', array( 'key' => $key, 'field' => $field ) ); ?>
				</div>
			</fieldset>
		<?php endforeach; ?>

		<?php do_action( 'submit_donation_form_donor_fields_end' ); ?>

		<p>
			<?php wp_nonce_field( 'submit_form_posted' ); ?>
			<input type="hidden" name="wpdonations_form" value="<?php echo $form; ?>" />
			<input type="hidden" name="donation_id" value="<?php echo esc_attr( $donation_id ); ?>" />
			<input type="submit" name="submit_donation" class="button" value="<?php esc_attr_e( $submit_button_text ); ?>" />
		</p>

	<?php else : ?>

		<?php do_action( 'submit_donation_form_disabled' ); ?>

	<?php endif; ?>
</form>