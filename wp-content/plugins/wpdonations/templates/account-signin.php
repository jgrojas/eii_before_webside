<?php if ( is_user_logged_in() ) : ?>

	<fieldset>
		<label><?php _e( 'Your account', 'wpdonations' ); ?></label>
		<div class="field account-sign-in">
			<?php
				$user = wp_get_current_user();
				printf( __( 'You are currently signed in as <strong>%s</strong>.', 'wpdonations' ), $user->user_login );
			?>

			<a class="button" href="<?php echo apply_filters( 'submit_donation_form_logout_url', wp_logout_url( get_permalink() ) ); ?>"><?php _e( 'Sign out', 'wpdonations' ); ?></a>
		</div>
	</fieldset>

<?php else :

	$account_required     = wpdonations_user_requires_account();
	$registration_enabled = wpdonations_enable_registration();
	?>
	<fieldset>
		<label><?php _e( 'Have an account?', 'wpdonations' ); ?></label>
		<div class="field account-sign-in">
			<a class="button" href="<?php echo apply_filters( 'submit_donation_form_login_url', wp_login_url( get_permalink() ) ); ?>"><?php _e( 'Sign in', 'wpdonations' ); ?></a>

			<?php if ( $registration_enabled ) : ?>

				<?php printf( __( 'If you don&lsquo;t have an account you can %screate one below by entering your email address. A password will be automatically emailed to you.', 'wpdonations' ), $account_required ? '' : __( 'optionally', 'wpdonations' ) . ' ' ); ?>

			<?php elseif ( $account_required ) : ?>

				<?php echo apply_filters( 'submit_donation_form_login_required_message',  __('You must sign in to create a new donation.', 'wpdonations' ) ); ?>

			<?php endif; ?>
		</div>
	</fieldset>
	<?php if ( $registration_enabled ) : ?>
		<fieldset>
			<label><?php _e( 'Your email', 'wpdonations' ); ?> <?php if ( ! $account_required ) echo '<small>' . __( '(optional)', 'wpdonations' ) . '</small>'; ?></label>
			<div class="field">
				<input type="email" class="input-text" name="create_account_email" id="account_email" placeholder="<?php _e( 'you@yourdomain.com', 'wpdonations' ); ?>" value="<?php if ( ! empty( $_POST['create_account_email'] ) ) echo sanitize_text_field( stripslashes( $_POST['create_account_email'] ) ); ?>" />
			</div>
		</fieldset>
	<?php endif; ?>

<?php endif; ?>