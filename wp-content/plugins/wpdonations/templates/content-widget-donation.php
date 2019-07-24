<li <?php donation_class(); ?>>
	<a href="<?php the_donation_permalink(); ?>">
		<div class="donation">
			<h3><?php the_title(); ?></h3>
		</div>
		<ul class="meta">
			<li class="donor"><?php the_donor_firstname(); ?></span>
			<li class="donation-campaign <?php echo get_the_donation_campaign() ? sanitize_title( get_the_donation_campaign()->slug ) : ''; ?>"><?php the_donation_campaign(); ?></li>
		</ul>
	</a>
</li>