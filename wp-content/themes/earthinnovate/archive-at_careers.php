<?php
/*
* Template for displaying careers archive page
*
* @package WordPress
* @subpackage Earth Innovate
* @since Version 1.0
*/

get_header();

	// search for page with same name as current post type
	$cpt_page = get_page_by_title('Careers');
	$post_thumbnail = get_the_post_thumbnail($cpt_page->ID);
	// if exists, use this page info to display featured image and pithy blurb
	if (isset($cpt_page) && ('' != $post_thumbnail) ) :  ?>
		<div class="hero-container">
			<?php $args = array( 'img_id' => get_post_thumbnail_id($cpt_page->ID) );
			at_add_picturefill_img($args);  // featured image

			$blurb = get_post_meta($cpt_page->ID, '_at_page_blurb', true);
			if ($blurb !== '') : // pithy blurb ?>
			<div class="blurb absolute-center">
				<h2><?php echo $blurb; ?></h2>
			</div>
			<?php else : // no blurb ?>
			<h3 class="page-title absolute-center"><span><?php _ex('Careers', 'careers page title', 'earthinnovate'); ?></span></h3>
			<?php endif; ?>
		</div>
	<?php endif; // end hero content display ?>

		<div id="main-content">
			<?php // if (no post thumbnail) or (post thumbnail & blurb)
			if ( ('' == $post_thumbnail) || (('' != $post_thumbnail) && ($blurb != '') ) ) : ?>
			<h3 class="page-title"><span><?php _ex('Careers', 'careers page title', 'earthinnovate'); ?></span></h3>
			<?php endif; ?>

			<?php // custom query
			global $more;
			// this is a quick way to just show all english posts
			// to only show english if there is no translation, more work to be done
			// see https://wpml.org/forums/topic/display-default-language-if-no-translation-on-category-index-page/#post-272732
			global $sitepress;
			// save current language
			$current_lang = $sitepress->get_current_language();
			//get the default language
			$default_lang = $sitepress->get_default_language();
			//fetch posts in default language
			$sitepress->switch_lang($default_lang);
			$base_url = get_post_type_archive_link('at_careers');
			$args = array(
				'post_type' => 'at_careers',
				'posts_per_archive_page' => -1,
				'order' => array('ASC', 'DESC'),
				'orderby' => array('menu_order', 'date')
				);
			$job_query = new WP_Query($args);
			// begin custom loop
			if ($job_query->have_posts()) : ?>
				<div class="pagenav-container">
					<div class="pagenav">
						<h5 class="pagenav-title"><?php _ex('Available Positions', 'careers page', 'earthinnovate'); ?></h5>
						<ul>
						<?php while ($job_query->have_posts()) : $job_query->the_post(); ?>
							<li><a href="<?php echo $base_url .'#job-'.$post->ID; ?>">
								<?php echo $post->post_title; ?>
							</a></li>
						<?php endwhile; ?>
						</ul>
					</div>
				</div>

				<section class="job-container">
				<?php // rewind custom query
				$job_query->rewind_posts();
				while ($job_query->have_posts()) : $job_query->the_post(); ?>
					<div>
						<article id="<?php echo 'job-'. $post->ID; ?>">
							<div class="overview">
								<h4 class="job-title"><?php the_title(); ?></h4>
								<small class="metadata">
								<?php echo __('Posted on','earthinnovate') . ' ' . get_the_date('F j, Y'); ?>
								</small>
							</div>

							<div class="content">
								<?php $more = 1;
								the_content(); ?>
							</div>
						</article>
					</div>
				<?php endwhile; ?>
				</section>

			<?php else : // no posts exists ?>
				<div class="no-content">
					<p><?php _e('There are currently no open positions at Earth Innovation Institute. We appreciate your interest, and encourage you to please check back at a future date.','earthinnovate'); ?></p>
				</div>
			<?php endif; wp_reset_postdata(); // end custom loop ?>
			<?php $sitepress->switch_lang($current_lang); ?>
			<div class="footnotes">
				<h5 class="title"><?php _ex('Organizational Summary', 'careers page', 'earthinnovate'); ?>:</h5>
				<p><?php _ex('The Earth Innovation Institute is a not-for-profit, independent research institute with headquarters in San Francisco and offices in Brazil, Indonesia and Colombia. We pursue our goals of slowing climate change, conserving tropical forests and fisheries, and improving rural livelihoods by promoting sustainable rural development through a blend of research, consensus-building, policy analysis and reform, and private sector engagement.', 'careers page', 'earthinnovate'); ?></p>
				<p><strong><?php _ex('Earth Innovation Institute is an equal opportunity employer. All candidates receive consideration for employment without regard to race, color, religion, sex, or national origin.', 'careers page', 'earthinnovate'); ?></strong></p>
			</div>

		</div><!-- #main-content -->

<?php get_footer(); ?>