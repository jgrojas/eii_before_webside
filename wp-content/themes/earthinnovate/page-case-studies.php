<?php 
/**
 * Displays a 'case studies' archive
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
			
			<?php // custom query arguments
			$paged = get_query_var('paged') ? get_query_var('paged') : 1;
			$args = array(
				'post_type' => 'page',
				'post_parent' => $post->ID,
				'posts_per_page' => -1,
				'paged' => $paged,
				'orderby' => 'menu_order', 
				'order' => 'ASC'
				); 
			$page_query = new WP_Query($args); 
			
			if ($page_query->have_posts()) : ?>
			<section class="group section">
			
			<?php while ($page_query->have_posts()) : $page_query->the_post(); ?>
				<article class="col span_1_of_3">
					<a class="max img-hover img" href="<?php the_permalink(); ?>">
						<?php $post_thumbnail = get_the_post_thumbnail(); 

						if ( '' != $post_thumbnail ) : 
							$args = array(
								'before' => '', 
								'after' => '',
								'sizes' => 'column-fw'
							);
							at_add_picturefill_img($args); 
						else : 
							echo '<img src="'.get_template_directory_uri().'/images/default-partner.png" />'; 
						endif; ?>
					</a>
					
					<h5 class="title">
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					</h5>
							
				</article>
			<?php endwhile; ?>
			
			<?php at_archive_pagination( array( 'total' => $page_query->max_num_pages ) ); ?>
			
			</section>
			<?php endif; wp_reset_postdata(); // end custom loop ?>
			
		</div><!--#main-content-->
			
	<?php endwhile; endif; // end loop 
			
 get_footer(); ?>