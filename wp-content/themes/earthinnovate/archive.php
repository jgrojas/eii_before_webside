<?php
/*
* Default template for displaying archives for CPTs (i.e. partners, staff, resources, etc.)
*
* @package WordPress
* @subpackage Earth Innovate
* @since Version 1.0
*/

get_header();

$cpt = get_post_type();
if (isset($cpt)) :
	$page = str_replace('at_', '', $cpt);
	$page = str_replace('_', ' ', $page);
	$page = str_replace('event','events',$page);

	$cpt_page = get_page_by_title($page); // search for page with same name as current post type
	$post_thumbnail = get_the_post_thumbnail($cpt_page->ID);
	if (isset($cpt_page) && ('' != $post_thumbnail) ) : // if exists, use this page info to display featured image and pithy blurb ?>
		<div class="hero-container">
			<?php $args = array( 'img_id' => get_post_thumbnail_id($cpt_page->ID) );
			at_add_picturefill_img($args);  // featured image

			$blurb = get_post_meta($cpt_page->ID, '_at_page_blurb', true);
			if ($blurb !== '') : // pithy blurb ?>
			<div class="blurb absolute-center">
				<h2><?php echo $blurb; ?></h2>
			</div>
			<?php else : ?>
			<h3 class="page-title absolute-center"><span><?php echo $cpt_page->post_title; ?></span></h3>
			<?php endif; ?>
		</div>
	<?php endif; // end hero content display ?>

		<div id="main-content">
			<?php if ( ('' == $post_thumbnail) || (('' != $post_thumbnail) && ($blurb != '') ) ) : ?>
			<h3 class="page-title"><span><?php echo $cpt_page->post_title; ?></span></h3>
			<?php endif; ?>

			<?php // begin custom query (based on which cpt is the current archive)
			if ($cpt == 'event') { $ppp = 20; $order = 'DESC'; $orderby = 'date'; } // resource-specific
			elseif ($cpt == 'at_board_members' || $cpt == 'at_staff'){ $ppp = -1; $order = 'ASC'; $orderby = 'menu_order'; } // staff-specific
			else { $ppp = 20; $order = 'ASC'; $orderby = 'title'; } // partner-specific? (not currently being used)

			$paged = get_query_var('paged') ? get_query_var('paged') : 1;
			$args = array(
				'post_type' => $cpt,
				'posts_per_archive_page' => $ppp,
				'paged' => $paged,
				'order' => $order,
				'orderby' => $orderby
				);
			$archive_query = new WP_Query($args);

			if ($archive_query->have_posts()) : ?>
			<section class="group section">
			<?php while ($archive_query->have_posts()) : $archive_query->the_post(); ?>
				<?php // set # of grid columns based on archive
				if (($cpt == 'at_staff') || ($cpt == 'at_board_members'))  // staff-specific
					$class = 'span_1_of_4';
				else // event-specific
					$class = 'span_1_of_3';  ?>

				<article class="col <?php echo $class; ?>">
				<?php if (($cpt == 'at_staff') || ($cpt == 'at_board_members')) : $link_before = '<div class="img-container">'; $link_after = '</div>';
				else : $link_before = ''; $link_after = '';
				endif; ?>

					<?php echo $link_before; ?>
						<a class="max img-hover img" href="<?php the_permalink(); ?>">
							<?php $post_thumbnail = get_the_post_thumbnail();
							if ( '' != $post_thumbnail ) : // if post thumbnail exists
								if ($cpt == 'event') :
									$args = array(
										'before' => '',
										'after' => '',
										'sizes' => 'column-fw'
									);
									at_add_picturefill_img($args);
								else : the_post_thumbnail('medium');
								endif;
								// photo credits
								$img_credits = get_post_field('post_content', get_post_thumbnail_id() );
								if ($img_credits) :  ?>
								<span class="photo-credits"><?php echo $img_credits; ?></span>
								<?php endif;
							// if no thumbnail exists, use fallback images
							elseif (('' == $post_thumbnail) && (($cpt == 'at_staff') || ($cpt == 'at_board_members'))) :
								echo '<img src="'.get_template_directory_uri().'/images/default-headshot.png" />';
							else : // last priority image fallback
								echo '<img src="'.get_template_directory_uri().'/images/default-partner.png" />';
							endif; ?>
						</a>
					<?php echo $link_after; ?>

					<div class="excerpt">
						<div>
						<h5 class="title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>

						<?php if (($cpt == 'at_staff') || ($cpt == 'at_board_members')) : $subheading = get_post_meta($post->ID, '_at_people_job_title', true); // staff specific
						else : // event specific
							//Format date/time according to whether its an all day event.
							//Use microdata http://support.google.com/webmasters/bin/answer.py?hl=en&answer=176035
	 						if( eo_is_all_day() ){
								$format = 'l, F j, Y';
								$microformat = 'Y-m-d';
							} else {
								$format = 'l, F j, Y – '.get_option('time_format');
								$microformat = 'c';
							}
							$subheading = '<time itemprop="startDate" datetime="'. eo_get_the_start($microformat) .'">'. eo_get_the_start($format) .'</time>';
						endif; ?>

						<?php if ($subheading) { ?><h6 class="subheading"><?php echo $subheading; ?></h6><?php } ?>

						<?php if ($cpt == 'event') : ?>
							<p><?php echo at_get_custom_excerpt(200); ?></p>
							<a class="more-link" href="<?php the_permalink(); ?>"><?php _e('Learn More','earthinnovate');  ?></a>
						<?php endif; ?>

						</div>
					</div><!-- .excerpt -->

				</article>
			<?php endwhile; ?>

			<?php at_archive_pagination( array( 'total' => $archive_query->max_num_pages ) ); ?>

			</section>

		<?php else: ?>
			<div class="no-content">
				<p><?php _e('Sorry, there are no articles to show.','earthinnovate'); ?></p>
			</div>
		<?php endif; wp_reset_postdata(); ?>

		</div><!-- #main-content -->

<?php endif; // end custom template for CPTs

get_footer(); ?>