<?php global $wpdonations; ?>

<a href="<?php the_permalink(); ?>">

	<div class="donation_summary_content">

		<h1><?php the_donation_formatted_amount( '', '', true, $post ) ?></h1>
		
		<?php if ( ! is_anonymous_donation( $post ) ) : ?>
			<h3><?php _e( 'by', 'wpdonations' ); ?> <?php the_donor_firstname(); ?> <?php the_donor_lastname(); ?></h3>
		<?php endif; ?>

		<p class="meta"><date><?php echo human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) . ' ' . __( 'ago', 'wpdonations' ); ?></date></p>

	</div>
</a>