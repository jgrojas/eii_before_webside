<?php $args = array(
	'post_type' => 'at_slides',
	'posts_per_page' => -1,
	'order' => 'ASC',
	'orderby' => 'menu_order'
	);
$slide_query = new WP_Query($args);
if ($slide_query->have_posts()) : ?>
<div class="sequence-container">
	<div id="sequence">
		<ul class="sequence-canvas">
		<?php while ($slide_query->have_posts()) : $slide_query->the_post(); // begin custom loop ?>
			<li <?php if ($key == 0) echo 'class="animate-in"'; ?>>
				<?php $link = get_post_meta( $post->ID, '_at_slide_href', true );
				if ($link != '') {
					$link = ltrim($link, '/');
					$link = esc_url( home_url('/') . $link );
				}
				$args = array(
					'before' => '<a class="max img trans-center" href="'.$link.'">',
					'after' => '</a>'
					);
				at_add_picturefill_img($args); ?>
				<div class="teaser">
					<a class="max" href="<?php echo $link; ?>">
						<h2><?php the_title(); ?></h2>
						<p><?php the_excerpt(); ?></p>
					</a>
				</div>
			</li>
		<?php $post_count = $slide_query->found_posts; // tally post count for use below
		endwhile; // end custom loop ?>
		</ul>
		<ul class="sequence-buttons">
			<li class="arrow-left slider-arrow-left-small"><button class="sequence-prev max" type="button"><?php _ex('Previous', 'slider navigation', 'earthinnovate'); ?></button></li>
			<li class="arrow-right slider-arrow-right-small"><button class="sequence-next max" type="button"><?php _ex('Next', 'slider navigation', 'earthinnovate'); ?></button></li>
		</ul>
		<!-- meta slider fallback <?php echo do_shortcode("[metaslider id=164]"); ?> -->
	</div>
	<ul class="sequence-pagination">
	<?php for ($i = 0; $i < $post_count; $i++){
		printf('<li><button type="button">Frame %d</button></li>', $i+1);
	 } ?>
	</ul>
</div><!-- .sequence-container -->
<?php endif; wp_reset_postdata(); ?>