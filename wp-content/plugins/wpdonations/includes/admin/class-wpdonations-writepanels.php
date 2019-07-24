<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WPdonations_Writepanels {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_post' ), 1, 2 );
		add_action( 'wpdonations_save_donation', array( $this, 'save_donation_data' ), 1, 2 );
	}

	/**
	 * donation_fields function.
	 *
	 * @access public
	 * @return void
	 */
	public function donation_fields() {
		return apply_filters( 'wpdonations_donation_data_fields', array(
			'_donation_amount' => array(
				'label' => __( 'Donation amount', 'wpdonations' ),
				'placeholder' => __( 'e.g. "20.00"', 'wpdonations' ),
				'description' => __( 'This is the donation amount, do not add currency symbol', 'wpdonations' )
			),
			'_recurrence_period' => array(
				'label' => __( 'Recurrence?', 'wpdonations' ),
				'type'  => 'select',
				'options' => array(
									'oneshot' => __('not recurring', 'wpdonations'),
									'weekly' => __('Weekly', 'wpdonations'),
									'monthly' => __('Monthly', 'wpdonations'),
									'yearly' => __('Yearly', 'wpdonations'),
								)
			),
			'_anonymous_donation' => array(
				'label' => __( 'Anonymous donation?', 'wpdonations' ),
				'type'  => 'checkbox'
			),
			'_donor_firstname' => array(
				'label' => __( 'Donor firstname', 'wpdonations' ),
				'placeholder' => ''
			),
			'_donor_lastname' => array(
				'label' => __( 'Donor lastname', 'wpdonations' ),
				'placeholder' => ''
			),
			'_donor_logo' => array(
				'label' => __( 'Donor profile picture', 'wpdonations' ),
				'placeholder' => __( 'URL to the donor profile picture', 'wpdonations' ),
				'type'  => 'file'
			),
			'_donor_address' => array(
				'label' => __( 'Donor address', 'wpdonations' ),
				'placeholder' => ''
			),
			'_donor_zip' => array(
				'label' => __( 'Donor postcode / Zip', 'wpdonations' ),
				'placeholder' => ''
			),
			'_donor_town' => array(
				'label' => __( 'Donor town', 'wpdonations' ),
				'placeholder' => ''
			),
			'_donor_website' => array(
				'label' => __( 'Donor website', 'wpdonations' ),
				'placeholder' => ''
			)
		) );
	}

	/**
	 * add_meta_boxes function.
	 *
	 * @access public
	 * @return void
	 */
	public function add_meta_boxes() {
		add_meta_box( 'donation_data', __( 'Donation Details', 'wpdonations' ), array( $this, 'donation_data' ), 'donation', 'normal', 'high' );
	}

	/**
	 * input_text function.
	 *
	 * @access private
	 * @param mixed $key
	 * @param mixed $field
	 * @return void
	 */
	private function input_file( $key, $field ) {
		global $thepostid;

		if ( empty( $field['value'] ) )
			$field['value'] = get_post_meta( $thepostid, $key, true );
		?>
		<p class="form-field">
			<label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field['label'] ) ; ?>:</label>
			<input type="text" class="file_url" name="<?php echo esc_attr( $key ); ?>" id="<?php echo esc_attr( $key ); ?>" placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>" value="<?php echo esc_attr( $field['value'] ); ?>" />
			<?php if ( ! empty( $field['description'] ) ) : ?><span class="description"><?php echo $field['description']; ?></span><?php endif; ?> <button class="button upload_image_button" data-uploader_button_text="<?php _e( 'Use as profile picture', 'wpdonations' ); ?>"><?php _e( 'Upload profile picture', 'wpdonations' ); ?></button>
		</p>
		<script type="text/javascript">
			// Uploading files
			var file_frame;
			var file_target_input;

			jQuery('.upload_image_button').live('click', function( event ){

			    event.preventDefault();

			    file_target_input = jQuery( this ).closest('.form-field').find('.file_url');

			    // If the media frame already exists, reopen it.
			    if ( file_frame ) {
					file_frame.open();
					return;
			    }

			    // Create the media frame.
			    file_frame = wp.media.frames.file_frame = wp.media({
					title: jQuery( this ).data( 'uploader_title' ),
					button: {
						text: jQuery( this ).data( 'uploader_button_text' ),
					},
					multiple: false  // Set to true to allow multiple files to be selected
			    });

			    // When an image is selected, run a callback.
			    file_frame.on( 'select', function() {
					// We set multiple to false so only get one image from the uploader
					attachment = file_frame.state().get('selection').first().toJSON();

					jQuery( file_target_input ).val( attachment.url );
			    });

			    // Finally, open the modal
			    file_frame.open();
			});
		</script>
		<?php
	}

	/**
	 * input_text function.
	 *
	 * @access private
	 * @param mixed $key
	 * @param mixed $field
	 * @return void
	 */
	private function input_text( $key, $field ) {
		global $thepostid;

		if ( empty( $field['value'] ) )
			$field['value'] = get_post_meta( $thepostid, $key, true );
		?>
		<p class="form-field">
			<label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field['label'] ) ; ?>:</label>
			<input type="text" name="<?php echo esc_attr( $key ); ?>" id="<?php echo esc_attr( $key ); ?>" placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>" value="<?php echo esc_attr( $field['value'] ); ?>" />
			<?php if ( ! empty( $field['description'] ) ) : ?><span class="description"><?php echo $field['description']; ?></span><?php endif; ?>
		</p>
		<?php
	}

	/**
	 * input_text function.
	 *
	 * @access private
	 * @param mixed $key
	 * @param mixed $field
	 * @return void
	 */
	private function input_textarea( $key, $field ) {
		global $thepostid;

		if ( empty( $field['value'] ) )
			$field['value'] = get_post_meta( $thepostid, $key, true );
		?>
		<p class="form-field">
			<label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field['label'] ) ; ?>:</label>
			<textarea name="<?php echo esc_attr( $key ); ?>" id="<?php echo esc_attr( $key ); ?>" placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>"><?php echo esc_html( $field['value'] ); ?></textarea>
			<?php if ( ! empty( $field['description'] ) ) : ?><span class="description"><?php echo $field['description']; ?></span><?php endif; ?>
		</p>
		<?php
	}
	
	/**
	 * input_select function.
	 *
	 * @param mixed $key
	 * @param mixed $field
	 */
	public function input_select( $key, $field ) {
		global $thepostid;

		if ( empty( $field['value'] ) )
			$field['value'] = get_post_meta( $thepostid, $key, true );
		?>
		<p class="form-field">
			<label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field['label'] ) ; ?>:</label>
			<select name="<?php echo esc_attr( $key ); ?>" id="<?php echo esc_attr( $key ); ?>">
				<?php foreach ( $field['options'] as $key => $value ) : ?>
				<option value="<?php echo esc_attr( $key ); ?>" <?php if ( isset( $field['value'] ) ) selected( $field['value'], $key ); ?>><?php echo esc_html( $value ); ?></option>
				<?php endforeach; ?>
			</select>
			<?php if ( ! empty( $field['description'] ) ) : ?><span class="description"><?php echo $field['description']; ?></span><?php endif; ?>
		</p>
		<?php
	}
	
	/**
	 * input_select function.
	 *
	 * @access private
	 * @param mixed $key
	 * @param mixed $field
	 * @return void
	 */
	private function input_multiselect( $key, $field ) {
		global $thepostid;

		if ( empty( $field['value'] ) )
			$field['value'] = get_post_meta( $thepostid, $key, true );
		?>
		<p class="form-field">
			<label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field['label'] ) ; ?>:</label>
			<select multiple="multiple" name="<?php echo esc_attr( $key ); ?>[]" id="<?php echo esc_attr( $key ); ?>">
				<?php foreach ( $field['options'] as $key => $value ) : ?>
				<option value="<?php echo esc_attr( $key ); ?>" <?php if ( ! empty( $field['value'] ) && is_array( $field['value'] ) ) selected( in_array( $key, $field['value'] ), true ); ?>><?php echo esc_html( $value ); ?></option>
				<?php endforeach; ?>
			</select>
			<?php if ( ! empty( $field['description'] ) ) : ?><span class="description"><?php echo $field['description']; ?></span><?php endif; ?>
		</p>
		<?php
	}

	/**
	 * input_checkbox function.
	 *
	 * @access private
	 * @param mixed $key
	 * @param mixed $field
	 * @return void
	 */
	private function input_checkbox( $key, $field ) {
		global $thepostid;

		if ( empty( $field['value'] ) )
			$field['value'] = get_post_meta( $thepostid, $key, true );
		?>
		<p class="form-field">
			<label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field['label'] ) ; ?></label>
			<input type="checkbox" class="checkbox" name="<?php echo esc_attr( $key ); ?>" id="<?php echo esc_attr( $key ); ?>" value="1" <?php checked( $field['value'], 1 ); ?> />
			<?php if ( ! empty( $field['description'] ) ) : ?><span class="description"><?php echo $field['description']; ?></span><?php endif; ?>
		</p>
		<?php
	}

	/**
	 * donation_data function.
	 *
	 * @access public
	 * @param mixed $post
	 * @return void
	 */
	public function donation_data( $post ) {
		global $post, $thepostid;

		$thepostid = $post->ID;

		echo '<div class="wpdonations_meta_data">';

		wp_nonce_field( 'save_meta_data', 'wpdonations_nonce' );

		do_action( 'wpdonations_donation_data_start', $thepostid );

		foreach ( $this->donation_fields() as $key => $field ) {
			$type = ! empty( $field['type'] ) ? $field['type'] : 'text';

			if ( method_exists( $this, 'input_' . $type ) )
				call_user_func( array( $this, 'input_' . $type ), $key, $field );
			else
				do_action( 'wpdonations_input_' . $type, $key, $field );
		}

		do_action( 'wpdonations_donation_data_end', $thepostid );

		echo '</div>';
	}

	/**
	 * save_post function.
	 *
	 * @access public
	 * @param mixed $post_id
	 * @param mixed $post
	 * @return void
	 */
	public function save_post( $post_id, $post ) {
		if ( empty( $post_id ) || empty( $post ) || empty( $_POST ) ) return;
		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
		if ( is_int( wp_is_post_revision( $post ) ) ) return;
		if ( is_int( wp_is_post_autosave( $post ) ) ) return;
		if ( empty($_POST['wpdonations_nonce']) || ! wp_verify_nonce( $_POST['wpdonations_nonce'], 'save_meta_data' ) ) return;
		if ( ! current_user_can( 'edit_post', $post_id ) ) return;
		if ( $post->post_type != 'donation' ) return;

		do_action( 'wpdonations_save_donation', $post_id, $post );
	}

	/**
	 * save_donation_data function.
	 *
	 * @access public
	 * @param mixed $post_id
	 * @param mixed $post
	 * @return void
	 */
	public function save_donation_data( $post_id, $post ) {
		global $wpdb;

		foreach ( $this->donation_fields() as $key => $field ) {

			if ( isset( $_POST[ $key ] ) ) {
				if ( is_array( $_POST[ $key ] ) ) {
					update_post_meta( $post_id, $key, array_map( 'sanitize_text_field', $_POST[ $key ] ) );
				} else {
					update_post_meta( $post_id, $key, sanitize_text_field( $_POST[ $key ] ) );
				}
			} elseif ( ! empty( $field['type'] ) && $field['type'] == 'checkbox' ) {
				update_post_meta( $post_id, $key, 0 );
			}
		}

	}
}

new WPdonations_Writepanels();