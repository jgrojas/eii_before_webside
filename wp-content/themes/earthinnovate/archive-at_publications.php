<?php 
/*
* Special template for displaying a publications archive
* 
* @package WordPress
* @subpackage Earth Innovate
* @since Version 1.0
*/

//* custom body class
add_filter('body_class', function($classes) {
    $classes[] = 'publications-archive';
    return $classes;
});

get_header(); 
	
	$cpt_page = get_page_by_title('Publications'); // search for publications page
	$post_thumbnail = get_the_post_thumbnail($cpt_page->ID); 
	
	if (isset($cpt_page) && ('' != $post_thumbnail) ) : // if exists, use this page info to display featured image and pithy blurb ?>
		<div class="hero-container" >
			<?php $args = array( 'img_id' => get_post_thumbnail_id($cpt_page->ID) ); 
			at_add_picturefill_img($args);  // featured image
			
			$blurb = get_post_meta($cpt_page->ID, '_at_page_blurb', true); 
			if ($blurb !== '') : // pithy blurb ?>
			<div class="blurb absolute-center">
				<h2><?php echo $blurb; ?></h2>
			</div>
			<?php else : ?>
			<h3 class="page-title absolute-center"><span><?php echo __($cpt_page->post_title, 'earthinnovate'); ?></span></h3>
			<?php endif; ?>
		</div>
	<?php endif; // end hero content display ?>

		<div id="main-content">
			<?php if ( ('' == $post_thumbnail) || (('' != $post_thumbnail) && ($blurb != '') ) ) : ?>
			<h3 class="page-title"><span><?php echo $cpt_page->post_title; ?></span></h3>
			<?php endif; ?>

			<div class="pubs-container group section">			
				<?php if (have_posts()) : ?>
				<!-- sidebar with publication filters -->
				<div class="side-column col span_1_of_4">
					<h4 class="filters-title"><?php _e('Publication Filters','earthinnovate'); ?></h4>
	
					<?php // display all facets (filters) in the sidebar ?>
					<h5 class="filter-title"><?php _e('Search','earthinnovate'); ?></h5>
					<?php echo facetwp_display('facet', 'search'); ?>
					
					<h5 class="filter-title"><?php _e('Country/Region','earthinnovate'); ?></h5> 
					<?php echo facetwp_display('facet', 'country-region'); ?>
					
					<h5 class="filter-title"><?php _e('Program','earthinnovate'); ?></h5>
					<?php echo facetwp_display('facet', 'program'); ?>
					
					
					<?php echo facetwp_display('facet', 'language');  ?>
					
					<button type="reset" class="clear-all"><?php _e('Clear All','earthinnovate'); ?></button>
				</div>
				
				<!-- display publications -->
				<div class="content col span_3_of_4">
										
					<?php echo facetwp_display('template', 'publications'); // display custom filter-able loop ?>
					
					<?php echo facetwp_display('pager'); // display custom loop pagination ?>
						
					<?php // default pagination
					/* at_archive_pagination( array( 'total' => $archive_query->max_num_pages ) ); */ ?>
					
				</div><!-- end publication display -->
			
				<?php else: // if no publications ?>
				<div class="no-content">
					<p><?php _e('Sorry, there are no articles to show','earthinnovate'); ?>.</p>
				</div>
				<?php endif; ?>
		
			</div><!-- .pubs-container -->
		</div><!-- #main-content -->
	<script type="text/javascript">(function($) {        $('.hero-content').css("min-height","300px");		$('.hero-content').css("background","white");    })(jQuery);</script>
<?php get_footer(); ?>