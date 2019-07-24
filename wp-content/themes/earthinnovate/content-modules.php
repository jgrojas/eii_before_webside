<?php
/**
 * Template Name: Content Modules
 * Description: Template to display dynamic content modules
 *
 * @package WordPress
 * @subpackage Earth Innovate
 * @since Version 1.0
 */

get_header();

	// Start the Loop.
	if (have_posts()) : while ( have_posts() ) : the_post();
		$post_thumbnail = get_the_post_thumbnail(); ?>
		<?php if ('' != $post_thumbnail ) : ?>
		<div class="hero-container">
			<?php /* display hero content */
			at_add_picturefill_img();

			/* page pithy blurb */
			$blurb = get_post_meta($post->ID, '_at_page_blurb', true);
			if ($blurb !== '') : ?>
			<div class="blurb absolute-center">
				<h2><?php echo $blurb; ?></h2>
			</div>
			<?php else : ?>
			<h3 class="page-title absolute-center"><span><?php the_title();  ?></span></h3>
			<?php endif; ?>
		</div>
		<?php endif; // end hero content display ?>

		<div id="main-content">
			<?php if ( ('' == $post_thumbnail) || (('' != $post_thumbnail) && ($blurb != '') ) ) : ?>
			<h3 class="page-title"><span><?php the_title();  ?></span></h3>
			<?php endif; ?>

			<?php /* main content */
			the_content('Read More'); ?>

			<?php // search for 'case studies' and 'related work' modules (via custom fields)
			$dynamic_array = array();
			$cs = get_post_meta($post->ID, '_at_page_case_studies', true);
			$rw = get_post_meta($post->ID, '_at_page_related_work', true);
			if ($cs) $dynamic_array[__('Case Studies', 'earthinnovate')] = $cs;
			if ($rw) $dynamic_array[__('Related Work', 'earthinnovate')] = $rw;

			foreach ($dynamic_array as $key => $value) :
				$page_array = str_replace(' ', '', $value);
				$page_array = explode(',', $page_array);
				$page_array = array_slice($page_array, 0, 3); ?>
				<section class="content-module page-section">
					<h4 class="page-title"><?php echo $key; ?></h4>
					<div class="group section">
					<?php foreach ($page_array as $page) :
						$id = icl_object_id((int) $page, 'page', false, ICL_LANGUAGE_CODE);
						$permalink = get_permalink($id); ?>
						<article class="col span_1_of_3">
						<?php $args = array(
							'img_id' => get_post_thumbnail_id($id),
							'before' => '<a class="max img-hover" href="' . $permalink . '">',
							'after' => '</a>',
							'sizes' => 'column-fw',
							'echo' => true
							);
							at_add_picturefill_img( $args ); ?>
							<h5><a href="<?php echo $permalink; ?>"><?php echo get_the_title($id); ?></a></h5>
						</article>
					<?php endforeach; ?>
					</div>
				</section>
			<?php endforeach; // end custom field modules (case studies/related work)

			$filter_id = (int) get_post_meta($post->ID, '_cat_id', true);
			echo "<script>x=". $filter_id . ";</script>";
			
			if ($filter_id) :
				$modules = array(
					__('Publications', 'earthinnovate') => 'at_publications',
					__('Partners', 'earthinnovate') => 'at_partners'
					);
				
				foreach ($modules as $key => $value) :
					// set up arguments based upon which module is the current one
					if ($value == 'at_partners') : $ppp = -1; $order = 'ASC'; $orderby = 'title';
					else : $ppp = 6; $order = 'DESC'; $orderby = 'date';
					endif;
					// check for dynamic content
					$args = array(
						'filter_id' => $filter_id,
						'post_type' => $value,
						'ppp' => $ppp,
						'order' => $order,
						'orderby' => $orderby
						);
					$articles = at_add_dynamic_content( $args );

					if ($articles) : ?>
						<section class="content-module page-section">
							<h4 class="page-title"><?php echo $key; ?></h4>
							<?php echo $articles; ?>

							<?php if ($value !== 'at_partners') : ?>
								<div class="more"><a href="<?php echo esc_url( home_url('/publications')); ?>" class="max more-link"><?php echo __('View All', 'earthinnovate') . ' ' . $key; ?></a></div>
							<?php endif; ?>

						</section>
					<?php endif;
				endforeach;
			endif; // end dynamic content display ?>

			<?php // next/previous arrows

			// only applies to 'mission' and 'strategy' child pages
			$post_parent = $post->post_parent;
			if ($post_parent === 2 || $post_parent === 5) :

				// menu order of current page (if greater than 0, the default)
				$current_page_order = ( (int) $post->menu_order > 0 ? (int) $post->menu_order : false );

				// custom loop containing all child pages
				$args = array(
					'post_type' => 'page',
					'post_parent' => $post_parent,
					'posts_per_page' => -1,
					'post_status' => 'publish',
					'order' => 'ASC',
					'orderby' => 'menu_order',
					);
				$page_query = new WP_Query($args);

				// only display buttons if there is more than one post
				$post_count = (int) $page_query->post_count;
				if ( $page_query->have_posts() && $post_count > 1 ) :

					// determine which buttons to show/hide, based on current page menu order
					$previous = ( $current_page_order === 1 ? false : true );
					$next = ( $current_page_order === $post_count ? false : true ); ?>

						<section class="page-navigation <?php if ( !$previous ) echo 'push-right'; elseif ( !$next ) echo 'push-left'; ?>">
							<div class="buttons clearfix">

					<?php while ($page_query->have_posts()) : $page_query->the_post(); ?>

						<?php $current_post = (int) $page_query->current_post + 1;

						// previous button
						if ( $previous && $current_post === $current_page_order - 1){ ?>
							<div class="previous-link"><a href="<?php the_permalink(); ?>"><?php _ex('Previous','page navigation','earthinnovate'); ?> <div class="l-arrow"></div></a></div>
						<?php } ?>

						<?php //  back to top button
						if ( ($next || $previous) && ($current_post === $current_page_order) ){ ?>
							<div class="goto-top"><a href="#masthead"><?php _e('Back to Top','earthinnovate'); ?></a></div>
						<?php } ?>

						<?php // next button
						if ( $next && $current_post === $current_page_order + 1){ ?>
							<div class="next-link"><a href="<?php the_permalink(); ?>"><?php _ex('Next','page navigation','earthinnovate'); ?> <div class="r-arrow"></div></a></div>
						<?php } ?>

					<?php endwhile; ?>

							</div>
						</section>

				<?php endif; wp_reset_postdata(); // end custom loop

			endif; // end next/previous arrow display ?>

		</div><!--#main-content-->

	<?php endwhile; endif; // end loop

 get_footer(); ?>