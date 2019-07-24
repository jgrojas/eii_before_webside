<li <?php donation_class(); ?>>
	<a href="<?php the_donation_permalink(); ?>">
		<?php the_donor_logo(); ?>
		<div class="donation">
			<h3><?php _e( 'By', 'wpdonations' ); ?> 
				<?php if( is_anonymous_donation() ) : ?>
					<?php _e( 'Anonymous', 'wpdonations' ); ?>
				<?php else : ?>
					<?php the_donor_firstname( '<strong>', '</strong> ' ); ?>
					<?php the_donor_lastname( '<strong>', '</strong> ' ); ?>
				<?php endif; ?>
			</h3>
			<?php the_donation_campaign(); ?>
		</div>
		<div class="amount">
			<?php the_donation_amount( '<strong>' . get_option( 'wpdonations_currency', false ), '</strong>'); ?>
		</div>
		<ul class="meta">
			<li class="date"><date><?php echo human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) . ' ' . __( 'ago', 'wpdonations' ); ?></date></li>
		</ul>
	</a>
</li>