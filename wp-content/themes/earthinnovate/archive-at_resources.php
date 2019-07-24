<?php
/*
* Special template for displaying the resources archive
*
* @package WordPress
* @subpackage Earth Innovate
* @since Version 1.0
*/

/* *
   * 	DEVELOPMENT
   * 	Page ID = 612
   * 	Archive ID = 614
   *
   *
   * 	PRODUCTION
   * 	Page ID = 3469
   * 	Archive ID = 3471
 */

get_header();

	$cpt_page = get_page_by_title('Resources'); // search for resources page
	$post_thumbnail = get_the_post_thumbnail($cpt_page->ID);
	
	if (isset($cpt_page)) {  // does 'resources' page exist?
		if ('' != $post_thumbnail ) : // does featured image for this page exist?  ?>
		<div class="hero-container">
			<?php $args = array( 'img_id' => get_post_thumbnail_id($cpt_page->ID) );
			at_add_picturefill_img($args);  // featured image ?>

			<h3 class="page-title absolute-center"><span><?php _e('View All Resources','earthinnovate'); ?></span></h3>
		</div>
		<?php endif; // end featured image display
	} ?>

		<div id="main-content">
			
			<?php if ( '' == $post_thumbnail ) { ?>
			<h3 class="page-title"><span><?php _e('View All Resources','earthinnovate');  ?></span></h3>
			<?php } ?>
			
			<?php // custom loop based on query vars
			// make pagination work
			global $wp_query;
			$paged = get_query_var('paged') ? get_query_var('paged') : 1;
			// basic args
			$args = array(
				'post_type' => 'at_resources',
				'post_status' => 'publish',
				'posts_per_page' => 5,
				'paged' =>  $paged,
				'order' => 'DESC',
				'orderby' => 'date'
				);

			// add taxonomy query, based upon 'type' query var
			$type_query_var = get_query_var('type');
			if ( !empty($type_query_var) )
				$taxonomy = array(
					'tax_query' => array(
						array(
							'taxonomy' => 'at_types',
							'field' => 'slug',
							'terms' => $type_query_var,
							'include_children' => false
							)
						)
					);
			else
				$taxonomy = array();

			// combine two arrays for final custom query
			$args = $args + $taxonomy;

			// get custom query
			$resource_query = new WP_Query( $args );

			if ($resource_query->have_posts()) : ?>
			<div class="resource-container group section">
				<!-- sidebar with publication filters -->
				
				<div class="side-column col span_1_of_4">
					<?php $args = array(
						'sidebar' => true,
						'echo' => true
						);
					at_get_resource_types($args); ?>
				</div>

				<!-- display publications -->
				
				<div class="content col span_3_of_4">
					<?php while ($resource_query->have_posts()) : $resource_query->the_post(); ?>
					<div class="resource group section">

						<div class="thumbnail col span_1_of_4">
							<?php $featured_img = get_the_post_thumbnail();
							if ('' != $featured_img) :
								at_add_picturefill_img( array(
									'before' => '<a href="'. get_permalink() .'" class="max img-hover">',
									'after' => '</a>',
									'sizes' => 'sidebar'
									) );
							else :
								$type = wp_get_post_terms($post->ID, 'at_types');
								if (!is_wp_error($type))
									$icon_class = rtrim($type[0]->slug, 's') . '_thumb-large';
								else
									$icon_class = 'no-icon'; ?>
								<a href="<?php the_permalink(); ?>" class="max fallback-icon"><div class="<?php echo $icon_class; ?> absolute-center"></div></a>
							<?php endif; ?>
						</div>

						<div class="excerpt col span_3_of_4">
							<h4 class="title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
							<p><?php echo at_get_custom_excerpt(250); ?> <a href="<?php the_permalink(); ?>"><?php _e('Read More','earthinnovate'); ?> &gt;&gt;</a></p>
						</div>
					</div>

					<?php endwhile; ?>

					<?php // default pagination
					at_archive_pagination( array( 'total' => $resource_query->max_num_pages ) ); ?>

				</div><!-- end resource display -->
			</div><!-- .resource-container -->

			<?php else: // if no resources ?>
			<div class="no-content">
				<p><?php _e('Sorry, there are no resources to display right now.','earthinnovate'); ?>.</p>
			</div>
			<?php endif; wp_reset_postdata(); ?>
		
		</div><!-- #main-content -->
		
<script type="text/javascript">
(function($) {
        $('.hero-content').css("min-height","300px");
		$('.hero-content').css("background","white");
    })(jQuery);

console.log('sdfsfd')

</script>
<?php get_footer(); ?>