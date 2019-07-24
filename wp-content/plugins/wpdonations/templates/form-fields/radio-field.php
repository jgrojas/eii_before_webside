<?php echo get_option( 'wpdonations_currency', false ); ?> 
<?php foreach ( $field['options'] as $key => $value ) : ?>
	<input type="radio" class="input-radio" value="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>" id="<?php echo esc_attr( $key ); ?>" <?php checked( $field['value'], 1 ); ?>/> <?php echo apply_filters( 'the_donation_available_amounts', number_format( esc_attr( $key ), 2, '.', '' ) ); ?>
<?php endforeach; ?>
<?php if ( ! empty( $field['description'] ) ) : ?><small class="checkbox-description"><?php echo $field['description']; ?></small><?php endif; ?>