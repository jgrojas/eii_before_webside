<?php /*
*
* Template for displaying any single post page
*
* @package WordPress
* @subpackage Earth Innovate
* @since Version 1.0
*
*/

get_header();
	// Define some variables
	$cpt = get_post_type();
	if (($cpt == 'at_staff') || ($cpt == 'at_board_members')) : $people = 1; $pubs = 0; $events = 0;
	elseif ($cpt == 'at_publications') : $pubs = 1; $people = 0; $events = 0;
	else : $events = 1; $people = 0; $pubs = 0;
	endif;
	// Start Loop
	if (have_posts()) : while (have_posts()) : the_post(); ?>

	<div id="main-content" class="group section">
		<!-- sidebar column -->
		<div class="side-column col span_3_of_8">
			<div class="img-container">
				<div class="img">
					<?php $post_thumbnail = get_the_post_thumbnail();
					if ('' != $post_thumbnail) :
						if (is_singular('event')) :
							$args = array(
								'before' => '',
								'after' => '',
								'sizes' => 'sidebar'
							);
							at_add_picturefill_img($args);
						else : the_post_thumbnail('medium');
						endif;
						// photo credits
						$img_credits = get_post_field('post_content', get_post_thumbnail_id() );
						if ($img_credits) : ?>
						<span class="photo-credits"><?php echo $img_credits; ?></span>
						<?php endif;
					// if no featured image exists, use image fallbacks
					elseif (('' == $post_thumbnail) && ($pubs)) : echo '<img src="'.get_template_directory_uri().'/images/default-publication.png" />';
					else : echo '<img src="'.get_template_directory_uri().'/images/default-headshot.png" />';
					endif; ?>
				</div>
			</div>

		<?php if ($people) : // board/staff specific ?>
			<h3 class="post-title"><?php the_title(); ?></h3>
			<?php $job_title = get_post_meta($post->ID, '_at_people_job_title', true);
			if ($job_title != '') : ?>
			<h4 class="subheading"><?php echo $job_title; ?></h4>
			<?php endif;
		elseif ($events) : eo_get_template_part('event-meta','event-single');
		else : // publication-specific
			$file = rwmb_meta('_at_pubs_upload_pub', 'type=file_advanced' );
			$external = rwmb_meta('_at_pubs_external_link', 'type=text');
			if ($file) : $file = array_values($file); $url = $file[0]['url'];
			elseif ( !$file && $external) : $url = esc_url($external);
			else : return; endif; ?>
			<?php if ($file || $external): ?>
			<div class="more"><a class="more-link" target="_blank" href="<?php echo $url; ?>"><?php _ex('View Publication', 'button text', 'earthinnovate'); ?></a></div>
			<?php endif;
		endif; // end side-column display ?>
		</div>

		<!-- main post content -->
		<div class="post-content col span_5_of_8">
		<?php if (!$people) : // publication-specific ?>
			<h3 class="post-title"><?php the_title(); ?></h3>
		<?php endif; ?>

		<?php if ($pubs) : // publication-specific
			$summary = rwmb_meta('_at_pubs_summary', 'type=textarea');
			$authors = rwmb_meta('_at_pubs_authors', 'type=textarea');
			if ($summary != '') : ?>
			<h4 class="subheading"><?php echo $summary; ?></h4>
			<?php endif; ?>

			<?php if ($authors != '') : ?>
			<div class="authors">
				<h5><?php _ex('Authors', 'for publications', 'earthinnovate'); ?></h5>
				<p><?php echo $authors; ?></p>
			</div>
			<?php endif; ?>

			<div class="abstract">
				<h5><?php _ex('Abstract', 'for publications', 'earthinnovate'); ?></h5>
				<?php the_content(); ?>
			</div>
		<?php else : // board/staff/events specific ?>
			<div class="content">
				<?php the_content(); ?>
			</div>
		<?php endif; ?>

		<?php if ($people) :
			$resume = rwmb_meta('_at_people_resume', 'type=file_advanced');
			if ($resume) : $resume = array_values($resume); $url = $resume[0]['url']; ?>
			<div class="more"><a class="more-link" target="_blank" href="<?php echo $url; ?>"><?php _ex('View C.V.', 'button text', 'earthinnovate'); ?></a></div>
			<?php endif;
		endif; // end board/staff specific ?>

		</div><!-- end main post content -->
	</div><!-- end #main-content -->

	<?php endwhile; endif;

get_footer(); ?>
