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
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<style>
.c_links_home_class{
    
}

.c_links_home_class li {
	position: relative;
	transition: 1s opacity; 
	list-style-type: none;
	display: none;	
	opacity: 0;
	top: 0;
}

.active_link {	
	display: initial !important;
	transition: 1s opacity; 
	opacity: 1 !important;

}

.link_a{
	color:rgb(244,132,21) !important;	
}

.link_a:hover{
	color:rgb(211, 113, 16) !important;	
}


</style>

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
		<div class="section group">
			<div class="featured-wrap">	
				<div class="headlines" style="text-align: center;"><b>TRENDING NOW : </b></div>	
				<div class="headlines" style="text-align: center; margin-bottom: 30px; font-size: 30px; margin-top: 0px;">
					<ul id="c_links_home" class="c_links_home_class"> 
						<li><a class="link_a" href="https://earthinnovation.org/state-of-jurisdictional-sustainability/">State of Jurisdictional Sustainability</a></li>
						<li><a target="a_blank" href="http://gcfimpact.org/">GCFImpact.org helping companies find green jurisdictions</a></li>
<<<<<<< HEAD
						<li><a class="link_a"  href="https://earthinnovation.org/2018/11/letter-from-scientists-in-support-of-california-tropical-forest-standard/">Letter from Scientists in Support of California Tropical Forest Standard</a></li>
=======
						<li><a class="link_a"  href="https://earthinnovation.org/jerrybrowntropicalforests/">Scientists Urge Governor Brown to Add Tropical Forests to California's Climate Solutions</a></li>
>>>>>>> fb4f61eb64cfee2e2fbc0b1d7acd4491a9e6a9bd
					</ul>
				</div>
			</div>
			<script type="text/javascript">
				var timer = 4000;
				var i = 0;
				var max = $('#c_links_home > li').length;

				$("#c_links_home > li").eq(i).addClass('active_link')
				

				setInterval(function(){

					$("#c_links_home > li").removeClass('active_link');
					$("#c_links_home > li").eq(i).css('transition-delay','0.5s');						

					if (i < (max-1)) {
						i = i+1; 
					}

					else { 
						i = 0; 
					}  

					$("#c_links_home > li").eq(i).css('left','0').addClass('active_link').css('transition-delay','1.25s');
					
				}, timer);

			</script>
		</div>

		<div id="featured" class="section group" style="padding-top: 0 !important">
				
			
			<article class="col span_1_of_3">
				<div class="featured-wrap">
					<?php echo wp_get_attachment_image($pubs_image_id, 'large'); ?>
					<?php $icl_object_id = icl_object_id(12, 'page', true); ?>
					<!-- <a href="<?php echo get_permalink($icl_object_id); ?>" alt="Earth Innovation Institute Publications"> -->
					<a href=<?php if (get_locale() == 'es_ES') {echo "/publications/?lang=es";}elseif(get_locale() == 'pt_BR'){echo "/publications/?lang=pt-br";}elseif(get_locale() == 'id_ID'){echo "/publications/?lang=id";}elseif(get_locale() == 'fr_FR'){echo "/publications/?lang=fr";}else{ echo "/publications/";} ?> alt="Earth Innovation Institute Publications">
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
					<a href=<?php if (get_locale() == 'es_ES') {echo "/blog/category/blog-es/?lang=es";}elseif(get_locale() == 'pt_BR'){echo "/blog/category/blog-pt-br/?lang=pt-br";}elseif(get_locale() == 'id_ID'){echo "/blog/category/blog-id/?lang=id";}else{ echo "/blog/category/blog/";} ?> alt="Earth Innovations Institute Blog">
						<button class="featured-button blue"><h5><?php _e('Blog','earthinnovate'); ?></h5></button>
					</a>
				</div>
			</article>
		</div><!-- #featured -->
		
		
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
