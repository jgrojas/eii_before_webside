<?php
/**
 * The front page
 *
 * @package 	WordPress
 * @subpackage 	Earth Innovate
 * @since 		2.0
 */

get_header(); ?>
<?php 
	GLOBAL $post;
	$hero_image_id = get_post_meta($post->ID, '_at_page_hero_image')[0];
	$pubs_image_id = get_post_meta($post->ID, '_at_page_publications_image')[0];
	$resc_image_id = get_post_meta($post->ID, '_at_page_resources_image')[0];
	$blog_image_id = get_post_meta($post->ID, '_at_page_blog_image')[0];
?>
	<div class="home-hero">
		<!-- Ajuste de la imagen del home -->
		<?php echo wp_get_attachment_image($hero_image_id, 'full'); ?>
		<!-- Fin de ajuste de la imagen del home -->
		<div class="home-hero-caption">
			<div class="wrap">
				<h1 class="title"><?php _e('Science and collaboration for a healthy planet','earthinnovate'); ?></h1>
			</div>
		</div>
	</div>
	
	<div id="main-content">
		<div id="featured" class="section group">
			<article class="col span_1_of_3">
				<div class="featured-wrap">
					<?php echo wp_get_attachment_image($pubs_image_id, 'large'); ?>
					<?php $icl_object_id = icl_object_id(12, 'page', true); ?>
					<!-- <a href="<?php echo get_permalink($icl_object_id); ?>" alt="Earth Innovation Institute Publications"> -->
					<a href="/publications/" alt="Earth Innovation Institute Publications">
						<button class="featured-button orange"><h5><?php _e('Publications','earthinnovate'); ?></h5></button>
					</a>
				</div>
			</article>
			<article class="col span_1_of_3">
				<div class="featured-wrap">
					<?php echo wp_get_attachment_image($resc_image_id, 'large'); ?>
					<?php $icl_object_id = icl_object_id(3469, 'page', true); ?>
					<!-- <a href="<?php echo get_permalink($icl_object_id); ?>" alt="Earth Innovation Institute Resources"> -->
					<a href="/resources/" alt="Earth Innovation Institute Resources">
						<button class="featured-button yellow"><h5><?php _e('Resources','earthinnovate'); ?></h5></button>
					</a>
				</div>
			</article>
			<article class="col span_1_of_3">	
				<div class="featured-wrap">
					<?php echo wp_get_attachment_image($blog_image_id, 'large'); ?>
					<a href="/blog/" alt="Earth Innovations Institute Blog">
						<button class="featured-button blue"><h5><?php _e('Blog','earthinnovate'); ?></h5></button>
					</a>
				</div>
			</article>
		</div><!-- #featured -->
		<!--<div style="text-align: center; margin-bottom: 20px; font-size: 30px; margin-top: -50px;">
			<a href="https://earthinnovation.org/state-of-jurisdictional-sustainability/">Visit our new report: State of Jurisdictional Sustainability</a>
		</div>-->
		
		<div id="news">
			<div class="section group">
				<div class="col span_1_of_2">
					<h4><?php _e('News','earthinnovate'); ?></h4>
					<?php
						global $sitepress;
						// save current language
						$current_lang = $sitepress->get_current_language();
						//get the default language
						$default_lang = $sitepress->get_default_language();
						//fetch posts in default language
						$sitepress->switch_lang($default_lang); 
					?>
					<?php $args = array('posts_per_page' => 3, 'cat' => 123); ?>
					<?php $new_query = new WP_Query($args); ?>
					<?php if ($new_query->have_posts()) : ?>
					<?php while ($new_query->have_posts()) : $new_query->the_post(); // begin custom loop ?>
						<div class="entry">
							<h6><a href="<?php the_permalink(); ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h6>
							<h6><?php the_time('l, F j, Y'); ?></h6>
		 					<?php echo wp_trim_words(get_the_excerpt(),30,''); ?>
	 					</div>
					<?php endwhile; endif; wp_reset_query(); $sitepress->switch_lang($current_lang); ?>
					<div style="margin-top: 20px;">
						<h6><a href="<?php echo home_url(). '/blog/category/news/'; ?>" alt="All Earth Innovation Instituted news"><?php _e('See All News','earthinnovate'); ?></a></h6>
					</div>
				</div>
				<div class="col span_1_of_2">
					<h4><?php _e('Twitter','earthinnovate'); ?></h4>
					<!-- <h4><?php _e('Tweets','earthinnovate'); ?></h4> -->
					<?php if ( is_active_sidebar( 'homepage-right-sidebar' ) ) : ?>
					<ul id="right-sidebar">
						<?php dynamic_sidebar( 'homepage-right-sidebar' ); ?>
					</ul>
				<?php endif; ?>
				</div>
			</div>
		</div>
	</div><!-- #main-content -->

<?php get_footer(); 	?>
