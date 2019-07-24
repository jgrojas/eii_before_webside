<?php global $wpdonations; ?>
<div class="single_donation">

	<?php if ( is_anonymous_donation( $post ) && $post->post_status != 'preview' && $post->post_author != get_current_user_id() ) : ?>

		<div class="wpdonations-info"><?php _e( 'This donation is anonymous', 'wpdonations' ); ?></div>

	<?php else : ?>

		<ul class="meta">
			<li class="donation-amount"><?php the_donation_amount( get_option( 'wpdonations_currency', false ) . ' ', '' ); ?></li>

			<?php if( get_the_donation_campaign() ) : ?>
			<li class="donation-campaign <?php echo get_the_donation_campaign() ? sanitize_title( get_the_donation_campaign()->slug ) : ''; ?>"><?php the_donation_campaign(); ?></li>
			<?php endif; ?>

			<li class="date-posted"><?php _e( 'Received', 'wpdonations' ); ?> <date><?php echo human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) . ' ' . __( 'ago', 'wpdonations' ); ?></date></li>
			
			<?php if( get_the_recurrence_period( $post ) != '' ) : ?>
				<li class="donation-recurrence"><?php echo get_the_recurrence_period( $post ); ?></li>
			<?php endif; ?>

		</ul>
		
		<?php if( get_the_content() != '' ) : ?>
			<h3><?php echo apply_filters( 'the_donation_message_title', __( "Donor's message", 'wpdonations' ) ); ?></h3>
			<?php echo apply_filters( 'the_donation_message', get_the_content() ); ?>
		<?php endif; ?>

		<div class="donor" itemscope itemtype="http://data-vocabulary.org/Organization">
			<?php the_donor_logo(); ?>

			<p class="name">
				<?php _e( 'Donor', 'wpdonations' ); ?>: 
				<a href="<?php echo get_the_donor_website(); ?>" itemprop="url">
				<?php the_donor_firstname( '<strong itemprop="name">', '</strong>' ); ?> <?php the_donor_lastname( '<strong itemprop="name">', '</strong>' ); ?>
				</a>
			</p>
		</div>

	<?php endif; ?>
</div>