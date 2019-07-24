<?php /*
*
* Template for displaying any single resource
*
* @package WordPress
* @subpackage Earth Innovate
* @since Version 1.0
*
*/

get_header();

	// Start Loop
	if (have_posts()) : while (have_posts()) : the_post();

		// store term object for later conditional use
		$terms = wp_get_post_terms($post->ID, 'at_types');
		if ( $terms && ! is_wp_error($terms) ){
			$type = $terms[0];
			$type_name = rtrim($type->name, 's'); // singular name
			$type_slug = rtrim($type->slug, 's'); // singular slug
		} else {
			$type = null;
			$type_name = '';
			$type_slug = '';
		} ?>

		<div id="main-content">

		<?php // specific layout for videos
		if( $type->name == 'Videos' ) : ?>

			<div class="piece video">
				<div class="content">
					<h3 class="post-title"><?php the_title(); ?></h3>

					<?php // check if embed url exists, and display it
					$embed = rwmb_meta('_at_resource_oembed');
					if ($embed){

					$embed_html = wp_oembed_get( esc_url($embed) );
					echo $embed_html;

					} // end video display ?>
				</div>
			</div>

			<div class="piece">
				<div class="content">
					<?php the_content(); ?>
				</div>
			</div>

		<?php else :  // every other type of resource ?>

			<div class="piece">
				<div class="content group section">

					<!-- sidebar column -->
					<div class="side-column col span_3_of_8">

						<?php $post_thumbnail = get_the_post_thumbnail($post->ID, 'medium'); ?>
						<div class="img-container <?php if ('' == $post_thumbnail) echo 'fallback'; ?>">
							<div class="img">
								<?php if ('' != $post_thumbnail) { // echo featured img and photo credits if they exists
									echo $post_thumbnail;

									// photo credits
									$img_credits = get_post_field('post_content', get_post_thumbnail_id() );
									if ($img_credits) { ?>
									<span class="photo-credits"><?php echo $img_credits; ?></span>
									<?php }

								} else { // if no featured image exists, use image fallbacks
									if ( isset($type) ) // check which image to display based on resource type (category)
										$icon_class = $type_slug . '_thumb-large icon absolute-center';
									else
										$icon_class = 'no-icon'; ?>
									<div class="<?php echo $icon_class; ?>"></div>

								<?php } // end featured image display ?>
							</div>
						</div>

					<?php // show image/document link if category != video and a link exists
					$file_link = rwmb_meta('_at_resource_href');
					$external = rwmb_meta('_at_resource_external');

					if ( $file_link ) :
						// set up file link url (first check if it's external or not)
						if ($external){
							$url = esc_url( $file_link );
						}
						else {
							$url = ltrim($file_link, '/');
							$url = esc_url( home_url('/') . $url );
						} ?>

						<div class="more">
							<a class="more-link" target="_blank" href="<?php echo $url; ?>"><?php echo _x('View','verb','earthinnovate') . ' ' . $type_name; ?></a>
						</div>

					<?php endif; // end display for non-videos ?>

						<div class="extra-info">

						<?php // display extra information fields based upon resource type (category)
						if ($type->name == 'Maps'){

							// display image source (Maps only)
							$source = rwmb_meta('_at_resource_source');
							if ($source){ ?>
								<p><strong><?php _ex('Source','noun','earthinnovate'); ?>:</strong><br /><?php echo $source; ?></p>
							<?php }

						}

						if ($type->name == 'Presentations'){

							// display event info (Presentations only)
							$sections = array(
								_x('Presented At', 'for presentations/events', 'earthinnovate') => 'presentation',
								__('Location', 'earthinnovate') => 'venue',
								__('Date', 'earthinnovate') => 'date'
								);
							$prefix = '_at_resource_';

							foreach ($sections as $title => $field) :

								// display information if post meta exists
								$meta = rwmb_meta("{$prefix}{$field}");
								if ($meta){ ?>
									<p><strong><?php echo $title; ?>:</strong><br /><?php echo $meta; ?></p>
								<?php }

							endforeach;

						} // end category-specific display ?>

						</div><!-- .extra-info -->

					</div><!-- .side-column -->

					<!-- main post content -->
					<div class="post-content col span_5_of_8">

						<h3 class="post-title"><?php the_title(); ?></h3>
						<div class="content">
							<?php the_content(); ?>
						</div>

					</div><!-- .post-content -->

				</div><!-- .content -->
			</div><!-- .piece -->

		<?php endif; // end resource-specific display ?>

		<?php // next/previous arrows
		$previous = get_previous_post(true, null, 'at_types');
		$next = get_next_post(true, null, 'at_types');

		$classes = array('piece', 'page-navigation');

		if ( !$previous && $next )
			$classes[] = 'push-right';
		elseif ( !$next && $previous )
			$classes[] = 'push-left';

		$classes = implode(' ', $classes); ?>

		<section class="<?php echo $classes; ?>">
			<div class="buttons clearfix">

				<?php previous_post_link('<div class="previous-link">%link</div>', 'View Previous <div class="l-arrow"></div>', true, null, 'at_types'); ?>

				<div class="goto-top"><a href="#masthead"><?php _e('Back to Top','earthinnovate'); ?></a></div>

				<?php next_post_link('<div class="next-link">%link</div>', 'View Next <div class="r-arrow"></div>', true, null, 'at_types'); ?>

			</div>
		</section><!-- .page-navigation -->

		</div><!-- end #main-content -->

	<?php endwhile;

	else : ?>

	<div id="main-content">
		<p><?php _e('Sorry, there is nothing to display right now.', 'earthinnovate'); ?></p>
	</div>

	<?php endif;

get_footer(); ?>
<script type="text/javascript">
(function($) {
        $('.hero-content').css("min-height","300px");
		$('.hero-content').css("background","white");
    })(jQuery);

</script>