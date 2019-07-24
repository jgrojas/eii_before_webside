<?php
/**
 * Template Name: Overview Page
 * Description: Template for automatically displaying child pages
 *
 * @package WordPress
 * @subpackage Earth Innovate
 * @since Version 1.0
 */

get_header();  ?>
	<?php if (is_page(7)) : ?>
	<div id="map-container"><div id="map-canvas"></div></div>
	<?php endif; ?>

	<?php global $nav_title;
	// Start the Loop.
	if (have_posts()) : while ( have_posts() ) : the_post(); ?>

	<?php $post_thumbnail = get_the_post_thumbnail();
	if ( !is_page(7) && ('' != $post_thumbnail )) : ?>
		<div class="hero-container">
			<?php /* display hero content */
			at_add_picturefill_img( );

			/* page pithy blurb */
			$blurb = get_post_meta($post->ID, '_at_page_blurb', true);
			if ($blurb !== '') : ?>
			<div class="blurb absolute-center">
				<h2><?php echo $blurb; ?></h2>
			</div>
			<?php else : // page title ?>
			<h3 class="page-title absolute-center"><span><?php the_title();  ?></span></h3>
			<?php endif; ?>
		</div>
	<?php endif; ?>

		<div id="main-content">
			<?php if ( ('' == $post_thumbnail) || (('' != $post_thumbnail) && ($blurb != '') ) || (is_page(7))) : ?>
			<h3 class="page-title"><span><?php the_title();  ?></span></h3>
			<?php endif;
			the_content();

		/* display child page callouts in gallery form */
		$my_query = new WP_Query(array(
			'post_type' => 'page',
			'post_parent' => get_the_ID(),
			'post__not_in' => array(237),
			'orderby' => 'menu_order',
			'order' => 'ASC'
			));
		if ( $my_query && $my_query->have_posts() ) : ?>
			<section class="thumbnail-gallery page-section">
				<?php if (is_page(7)) : $group = 'group section'; $col = 'col span_1_of_3'; else : $group = ''; $col = ''; endif; ?>
				<ul class="<?php echo $group; ?>">
		<?php while ( $my_query->have_posts() ) : $my_query->the_post(); //begin custom loop ?>
					<li class="<?php echo $col; ?>">
						<h4 class="overlay-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
					<?php if (class_exists('kdMultipleFeaturedImages')) : // check if multiple featured images plugin is activated ?>
						<div class="thumbnail trans-center">
							<div class="thumb-overlay">
								<div class="table">
									<div class="table-cell">
										<div class="table-content">
											<p><?php // get content excerpt
											global $more;
											$more = 0;
											echo at_get_custom_excerpt(225);  ?></p>
											<a class="more-link" href="<?php the_permalink(); ?>">
												<?php if (is_page(18)) echo __('Learn More','earthinnovate'); else echo __('Read More','earthinnovate'); ?>
												<div class="r-arrow"></div>
											</a>
										</div>
									</div>
								</div>
							</div>

							<div class="thumb-img">
								<?php // get picturefill img id
								$page_id = get_the_ID();
								
								//$thumb_img_id = get_post_meta( $page_id, 'kd_thumbnail-image_page_id', true);
								$thumb_img_id = get_post_meta( $page_id, '_kdmfi_thumbnail-image', true);

								$sizes = '(max-width: 75em) 100vw, 1200px';
								if (is_page(7))
									$sizes = 'column-fw';

								$args = array(
									'img_id' => $thumb_img_id,
									'before' => '',
									'after' => '',
									'sizes' => $sizes
									);
									
								at_add_picturefill_img( $args ); ?>
							</div>
						</div>
					<?php endif; // end thumbnail image display ?>
					</li>
		<?php endwhile; ?>
				</ul>
			</section>
		<?php else:
			_e('Sorry, there is nothing to display', 'earthinnovate');
		endif; wp_reset_postdata(); //end custom loop & reset global $post variable ?>

		</div><!--#main-content-->

	<?php endwhile; endif; /* end default loop */?>

<?php get_footer(); ?>