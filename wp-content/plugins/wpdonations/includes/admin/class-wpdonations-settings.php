<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WPdonations_Settings class.
 */
class WPdonations_Settings {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		$this->settings_group = 'wpdonations';
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * init_settings function.
	 *
	 * @access protected
	 * @return void
	 */
	protected function init_settings() {
	
		$emails = new WPdonations_Emails();
		
		$this->settings = apply_filters( 'wpdonations_settings',
			array(
				'donations' => array(
					__( 'General', 'wpdonations' ),
					array(
						array(
							'name'        => 'wpdonations_per_page',
							'std'         => '10',
							'placeholder' => '',
							'label'       => __( 'Donations per page', 'wpdonations' ),
							'desc'        => __( 'How many donations should be shown per page by default?', 'wpdonations' ),
							'attributes'  => array()
						),
						array(
							'name'        => 'wpdonations_general_target',
							'std'         => '',
							'placeholder' => '',
							'label'       => __( 'Global Target', 'wpdonations' ),
							'desc'        => __( 'The amount of money you would like to collect.', 'wpdonations' ),
							'attributes'  => array()
						),
						array(
							'name'       => 'wpdonations_hide_anonymous_donations',
							'std'        => '0',
							'label'      => __( 'Anonymous donations', 'wpdonations' ),
							'cb_label'   => __( 'Hide anonymous donations', 'wpdonations' ),
							'desc'       => __( 'If enabled, anonymous donations will be hidden from the donations list.', 'wpdonations' ),
							'type'       => 'checkbox',
							'attributes' => array()
						),
						array(
							'name'       => 'wpdonations_enable_categories',
							'std'        => '0',
							'label'      => __( 'Donation categories', 'wpdonations' ),
							'cb_label'   => __( 'Enable donation categories', 'wpdonations' ),
							'desc'       => __( 'Choose whether to enable donation categories. Categories must be setup by an admin for users to choose during donation submission.', 'wpdonations' ),
							'type'       => 'checkbox',
							'attributes' => array()
						),
						array(
							'name'       => 'wpdonations_enable_campaigns',
							'std'        => '0',
							'label'      => __( 'Donation campaigns', 'wpdonations' ),
							'cb_label'   => __( 'Enable donation campaigns', 'wpdonations' ),
							'desc'       => __( 'Choose whether to enable donation campaigns. campaigns must be setup by an admin for users to choose during donation submission.', 'wpdonations' ),
							'type'       => 'checkbox',
							'attributes' => array()
						),
					),
				),
				'donation_submission' => array(
					__( 'Donation Submission', 'wpdonations' ),
					array(
						array(
							'name'       => 'wpdonations_enable_registration',
							'std'        => '1',
							'label'      => __( 'Account creation', 'wpdonations' ),
							'cb_label'   => __( 'Allow account creation', 'wpdonations' ),
							'desc'       => __( 'If enabled, non-logged in users will be able to create an account by entering their email address on the donation submission form.', 'wpdonations' ),
							'type'       => 'checkbox',
							'attributes' => array()
						),
						array(
							'name'       => 'wpdonations_user_requires_account',
							'std'        => '1',
							'label'      => __( 'Account required', 'wpdonations' ),
							'cb_label'   => __( 'Donation submission requires an account', 'wpdonations' ),
							'desc'       => __( 'If disabled, non-logged in users will be able to submit donations without creating an account.', 'wpdonations' ),
							'type'       => 'checkbox',
							'attributes' => array()
						),
/*
						array(
							'name'       => 'wpdonations_submission_requires_approval',
							'std'        => '1',
							'label'      => __( 'Approval Required', 'wpdonations' ),
							'cb_label'   => __( 'New submissions require admin approval', 'wpdonations' ),
							'desc'       => __( 'If enabled, new submissions will be inactive, pending admin approval.', 'wpdonations' ),
							'type'       => 'checkbox',
							'attributes' => array()
						),
*/
					)
				),
				'donation_emails' => array(
					__( 'Emails', 'wpdonations' ),
					array(
						array(
							'name' 		=> 'wpdonations_donor_email_template',
							'std' 		=> $emails->get_default_email(),
							'label' 	=> __( 'Donor Email Content', 'wpdonations' ),
							'desc'		=> __( 'Enter the content for the email sent to donors. Leave blank to use the default message. The following tags can be used to insert data dynamically:', 'wpdonations' ) . '<br/>' .
								'<code>{display_name}</code>' . ' - ' . __( 'The users display name in WP', 'wpdonations' ) . '<br/>' .
								'<code>{donation_date}</code>' . ' - ' . __( 'The donation date', 'wpdonations' ) . '<br/>' .
								'<code>{donation_amount}</code>' . ' - ' . __( 'The donation amount', 'wpdonations' ) . '<br/>' .
								'',
							'type'      => 'textarea',
							'required'  => true
						),
					)
				),
			)
		);
	}

	/**
	 * register_settings function.
	 *
	 * @access public
	 * @return void
	 */
	public function register_settings() {
		$this->init_settings();

		foreach ( $this->settings as $section ) {
			foreach ( $section[1] as $option ) {
				if ( isset( $option['std'] ) )
					add_option( $option['name'], $option['std'] );
				register_setting( $this->settings_group, $option['name'] );
			}
		}
	}

	/**
	 * output function.
	 *
	 * @access public
	 * @return void
	 */
	public function output() {
		$this->init_settings();
		?>
		<div class="wrap">
			<form method="post" action="options.php">

				<?php settings_fields( $this->settings_group ); ?>
				<?php screen_icon(); ?>

			    <h2 class="nav-tab-wrapper">
			    	<?php
			    		foreach ( $this->settings as $section ) {
			    			echo '<a href="#settings-' . sanitize_title( $section[0] ) . '" class="nav-tab">' . esc_html( $section[0] ) . '</a>';
			    		}
			    	?>
			    </h2><br/>

				<?php
					if ( ! empty( $_GET['settings-updated'] ) ) {
						flush_rewrite_rules();
						echo '<div class="updated fade"><p>' . __( 'Settings successfully saved', 'wpdonations' ) . '</p></div>';
						
						if ( -1 == get_option( 'wpdonations_is_installed' ) )
							update_option( 'wpdonations_is_installed', 1 );
					}

					foreach ( $this->settings as $section ) {

						echo '<div id="settings-' . sanitize_title( $section[0] ) . '" class="settings_panel">';

						echo '<table class="form-table">';

						foreach ( $section[1] as $option ) {

							$placeholder    = ( ! empty( $option['placeholder'] ) ) ? 'placeholder="' . $option['placeholder'] . '"' : '';
							$class          = ! empty( $option['class'] ) ? $option['class'] : '';
							$value          = get_option( $option['name'] );
							$option['type'] = ! empty( $option['type'] ) ? $option['type'] : '';
							$attributes     = array();

							if ( ! empty( $option['attributes'] ) && is_array( $option['attributes'] ) )
								foreach ( $option['attributes'] as $attribute_name => $attribute_value )
									$attributes[] = esc_attr( $attribute_name ) . '="' . esc_attr( $attribute_value ) . '"';

							echo '<tr valign="top" class="' . $class . '"><th scope="row"><label for="setting-' . $option['name'] . '">' . $option['label'] . '</a></th><td>';

							switch ( $option['type'] ) {

								case "checkbox" :

									?><label><input id="setting-<?php echo $option['name']; ?>" name="<?php echo $option['name']; ?>" type="checkbox" value="1" <?php echo implode( ' ', $attributes ); ?> <?php checked( '1', $value ); ?> /> <?php echo $option['cb_label']; ?></label><?php

									if ( $option['desc'] )
										echo ' <p class="description">' . $option['desc'] . '</p>';

								break;
								case "textarea" :

									?><textarea id="setting-<?php echo $option['name']; ?>" class="large-text" cols="50" rows="3" name="<?php echo $option['name']; ?>" <?php echo implode( ' ', $attributes ); ?> <?php echo $placeholder; ?>><?php echo esc_textarea( $value ); ?></textarea><?php

									if ( $option['desc'] )
										echo ' <p class="description">' . $option['desc'] . '</p>';

								break;
								case "select" :

									?><select id="setting-<?php echo $option['name']; ?>" class="regular-text" name="<?php echo $option['name']; ?>" <?php echo implode( ' ', $attributes ); ?>><?php
										foreach( $option['options'] as $key => $name )
											echo '<option value="' . esc_attr( $key ) . '" ' . selected( $value, $key, false ) . '>' . esc_html( $name ) . '</option>';
									?></select><?php

									if ( $option['desc'] )
										echo ' <p class="description">' . $option['desc'] . '</p>';

								break;
								default :

									?><input id="setting-<?php echo $option['name']; ?>" class="regular-text" type="text" name="<?php echo $option['name']; ?>" value="<?php esc_attr_e( $value ); ?>" <?php echo implode( ' ', $attributes ); ?> <?php echo $placeholder; ?> /><?php

									if ( $option['desc'] )
										echo ' <p class="description">' . $option['desc'] . '</p>';

								break;

							}

							echo '</td></tr>';
						}

						echo '</table></div>';

					}
				?>
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'wpdonations' ); ?>" />
				</p>
		    </form>
		</div>
		<script type="text/javascript">
			jQuery('.nav-tab-wrapper a').click(function() {
				jQuery('.settings_panel').hide();
				jQuery('.nav-tab-active').removeClass('nav-tab-active');
				jQuery( jQuery(this).attr('href') ).show();
				jQuery(this).addClass('nav-tab-active');
				return false;
			});

			jQuery('.nav-tab-wrapper a:first').click();
		</script>
		<?php
	}
}