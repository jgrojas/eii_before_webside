<?php 
	echo get_option( 'wpdonations_currency', false );
	$field_name = esc_attr( $key );
?> 
<?php foreach ( $field['options'] as $key => $value ) : ?>
	<input type="radio" class="input-radio" value="<?php echo esc_attr( $key ); ?>" name="<?php echo $field_name; ?>" id="<?php echo esc_attr( $key ); ?>" <?php if( isset( $field['value'] ) ) checked( $field['value'], 1 ); ?>/> <?php echo apply_filters( 'the_donation_available_amounts', number_format( esc_attr( $key ), 2, '.', '' ) ); ?>
<?php endforeach; ?>
<input type="text" class="input-text" name="<?php echo $field_name; ?>" id="<?php echo esc_attr( $key ); ?>" placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>" value="<?php echo isset( $field['value'] ) ? esc_attr( $field['value'] ) : ''; ?>" maxlength="<?php echo ! empty( $field['maxlength'] ) ? $field['maxlength'] : ''; ?>" />
<?php if ( ! empty( $field['description'] ) ) : ?><small class="checkbox-description"><?php echo $field['description']; ?></small><?php endif; ?>