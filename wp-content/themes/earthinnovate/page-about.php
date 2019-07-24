<?php
/*
* About Us Page Template
*
* @package WordPress
* @subpackage Earth Innovate
* @since Version 1.0
*/

get_header();

if (have_posts()) : while (have_posts()) : the_post(); ?>
	<?php $post_thumbnail = get_the_post_thumbnail();
	if ('' != $post_thumbnail) : ?>
	<div class="hero-container">
		<?php /* display hero content */
		at_add_picturefill_img( );

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
	<?php endif; ?>

	<div id="main-content">
		<?php if ( ('' == $post_thumbnail) || (('' != $post_thumbnail) && ($blurb != '') ) ) : ?>
		<h3 class="page-title"><span><?php the_title();  ?></span></h3>
		<?php endif; ?>

		<?php the_content(); ?>

		<?php // ids = { board: 2357, staff: 334, history: 341, careers: 1845, contact: 336 }
		$page_query = new WP_Query(array( 'post_type' => 'page', 'post__in' => array(2357, 334, 1845, 341, 336), 'orderby' => 'menu_order', 'order' => 'ASC' ) );
		if ($page_query->have_posts()) : ?>
		<section class="previews">
			<div class="group section">
				<?php while ($page_query->have_posts()) : $page_query->the_post(); ?>
				<?php $page_id = get_the_ID();
				if (($page_id === 341) || ($page_id === 336)) : continue; endif; ?>
				<article class="col span_1_of_3">
					<a href="<?php echo get_the_permalink(get_the_ID()); ?>">
					<h4><?php the_title(); ?></h4>
					</a>
				</article>
				
				<?php endwhile; ?>
			</div>
		</section>
		<?php endif; ?>

		<section class="history textured">
			<h4><?php _ex('Our History', 'about us page', 'earthinnovate'); ?></h4>
			<?php $page_query->rewind_posts();
			if ($page_query->have_posts()) : while ($page_query->have_posts()) : $page_query->the_post();
			$page_id = get_the_ID();
			if ($page_id === 341) : ?>
			<div class="content"><p><?php global $more;
			$more = 0;
			$content = get_the_content('Read More');
			$content = strip_shortcodes($content);
			echo $content; ?></p></div>
			<?php endif; endwhile; endif; ?>
			<div class="seedling-large"></div>
		</section>

		<section class="contact page-section trans-center">
			<?php $page_query->rewind_posts();
			if ($page_query->have_posts()) : while ($page_query->have_posts()) : $page_query->the_post();
			$page_id = get_the_ID();
			if ($page_id === 336) :
			$thumb_img_id = get_post_meta( $page_id, 'kd_thumbnail-image_page_id', true);
			$args = array(
				'img_id' => $thumb_img_id,
				'before' => '',
				'after' => ''
				);
			at_add_picturefill_img( $args ); ?>
			<div class="content absolute-center">
				<h4 class="page-title"><?php _ex('Contact Us', 'about us page', 'earthinnovate'); ?></h4>
				<a class="more-link" title="Contact Us" href="<?php the_permalink(); ?>"><?php _ex('contact us', 'earthinnovate'); ?></a>
			</div>
			<?php endif;
			endwhile; endif; wp_reset_postdata(); ?>
		</section>

	</div><!--#main-content-->

<?php endwhile; endif;

get_footer(); ?>

