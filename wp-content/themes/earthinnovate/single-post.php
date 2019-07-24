<?php /*
 *
 * Template for displaying a single blog post
 *
 * @package WordPress
 * @subpackage Earth Innovate
 * @since Version 1.0
 */

 get_header(); ?>

 	<div id="main-content">

 	<?php if ( have_posts()) : while ( have_posts()) : the_post(); ?>

	 	<h3 class="post-title"><?php the_title(); ?></h3>
	 	<div class="post-metadata">
		 	<div class="author">
			 	<span><?php echo get_avatar( get_the_author_meta('ID') ); ?></span>
			 	<span><?php the_author(); ?></span>
		 	</div>
		 	<div class="date"><?php the_date(); ?></div>
		 	<div class="categories">
		 		<span><strong><?php _ex('Posted in', 'for blog post categories', 'earthinnovate'); ?>:</strong></span><br />
		 		<?php $post_terms = wp_get_post_terms($post->ID, 'category');
				$count = count($post_terms);
				foreach ($post_terms as $index => $term) : ?>
					<strong><a href="<?php echo home_url() . '/blog/category/' . $term->slug; ?>"><?php echo $term->name; ?></a>
					<?php if ((int) $index !== $count -1) echo ', '; ?></strong>
				<?php endforeach; ?>
		 	</div>
	 	</div>

		<hr />

	 	<!-- <div class="img-container">
		 	<?php at_add_picturefill_img( array(
			 	'sizes' => '(max-width: 75em) 100vw, 1200px',
			 	'before' => '',
			 	'after' => ''
			 	) ); ?>
	 	</div> -->


	 	<?php the_content(); ?>

	 	<?php $cat = get_the_category(get_the_ID()); ?>

	 	<?php if ($cat[0]->slug == 'news') : ?>

	 		<div class="back"><a href="<?php echo home_url(). '/blog/category/news/'; ?>"><div class="l-arrow"></div><?php _ex('Back to News', 'button text', 'earthinnovate'); ?></a></div>

	 	<?php else : ?>

	 		<div class="back"><a href="<?php echo home_url(). '/blog'; ?>"><div class="l-arrow"></div><?php _ex('Back to Blog', 'button text', 'earthinnovate'); ?></a></div>

	 	<?php endif; ?>

	 <?php endwhile; else : ?>

		<div class="no-content">
			<p><?php _e('Sorry, there is nothing to display right now','earthinnovate'); ?>.</p>
		</div>

	 <?php endif; ?>

 	</div><!-- #main-content -->

 <?php get_footer(); ?>