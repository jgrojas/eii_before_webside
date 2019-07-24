<?php
/*
 * Template Name: Board Members
 *
 * @package WordPress
 * @subpackage Earth Innovate
 * @since Version 1.0
 */

get_header();

global $sitepress;
$current_lang = $sitepress->get_current_language();
//get the default language
$default_lang = $sitepress->get_default_language();
//fetch posts in default language
$sitepress->switch_lang($default_lang);

if (have_posts()) : while (have_posts()) : the_post(); ?>

	<div id="main-content">
		<style>
			.bm-headshot img {
				width: 130px;
			}
		</style>
		<h3 class="page-title"><span><?php the_title();  ?></span></h3>
			
			<?php $args = array(
				'post_type' => 'at_board_members',
				'posts_per_page' => -1,
				'post_status' => 'publish',
				); ?>
			<?php $query = new WP_Query($args); ?>
			<?php if ($query->have_posts()) : while ($query->have_posts()) : $query->the_post(); ?>

				<?php //check if a translation exist ?>
				<?php $t_post_id = icl_object_id($post->ID, 'post', false, $current_lang); ?>

				<?php if (! is_null($t_post_id)) : ?>

					<section class="">
						<div class="content group section">
							
							<div class="bm-headshot span_1_of_5 col">
								<img width="117" height="122" alt="<?php _e('Image 
of','earthinnovate'); ?> <?php echo get_the_title($t_post_id); ?>" src="<?php echo 
wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'thumbnail')[0]; ?>" class="alignright wp-post-image">
							</div>
							
							<div class="bm-content span_3_of_4 col">
								<strong><?php echo get_the_title($t_post_id); 
?></strong><br />
								<strong><i><?php echo get_post_meta($t_post_id, 
'_at_people_job_title')[0]; ?></i></strong><br />
								<?php the_content(); ?>
							</div>

						</div>
					</section>

				<?php else : ?>
									<section class="">
					<div class="content group section">
						
						<div class="bm-headshot span_1_of_5 col">
							<?php the_post_thumbnail('thumbnail', array( 'class' => 
'alignright' )); ?>
						</div>
						
						<div class="bm-content span_3_of_4 col">
							<strong><?php the_title(); ?></strong><br />
							<strong><i><?php echo get_post_meta(get_the_ID(), 
'_at_people_job_title')[0]; ?></i></strong><br />
							<?php the_content(); ?>
						</div>

					</div>
				</section>

			<?php endif; ?>

			<?php endwhile; endif; ?>

			<?php wp_reset_query(); ?>


	</div><!--#main-content-->

<?php endwhile; endif;
$sitepress->switch_lang($current_lang);
get_footer(); ?>
