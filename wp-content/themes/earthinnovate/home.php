<?php
/*
 * Displays archive of blog posts
 *
 * @package WordPress
 * @subpackage Earth Innovate
 * @since Version 2.0
 */


get_header();

	$blog_page_id = (int) get_option('page_for_posts');
	$blog_title = get_the_title( $blog_page_id );

		// Start the Loop.
		$post_thumbnail = get_the_post_thumbnail( $blog_page_id );
		if ('' != $post_thumbnail ) : ?>
		<div class="hero-container">
			<?php /* display hero content */	
	
			
			at_add_picturefill_img( array( 'img_id' => get_post_thumbnail_id( $blog_page_id ) ) );

			/* page pithy blurb */
			$blurb = get_post_meta( $blog_page_id, '_at_page_blurb', true);
			if ($blurb !== '') : ?>
			<div class="blurb absolute-center">
				<h2><?php echo $blurb; ?></h2>
			</div>
			<?php else : ?>
			<h3 class="page-title absolute-center"><span><?php echo $blog_title;  ?></span></h3>
			<?php endif; ?>
		</div>
		<?php endif; // end hero content display ?>


		<div id="main-content">
			<?php if ( ('' == $post_thumbnail) || (('' != $post_thumbnail) && ($blurb != '') ) ) : ?>
			<h3 class="page-title"><span><?php echo $blog_title; ?></span></h3>
			<?php endif; ?>

			<?php if (have_posts()) : ?>

			<div class="blog-container group section">
				<!-- side column -->
				<div class="side-column col span_1_of_4">

					<?php // display categories in sidebar
					 $cats = get_terms('category' );
					if (  !empty($cats) ) : ?>
					<ul class="cat-list">

						<?php $category = get_query_var('category_name');
						foreach ($cats as $cat) :
							if ($category === $cat->slug)
								$class = 'current';
							else
								$class = ''; ?>
						<?php if ($cat->term_id != 123) : ?>
						<li class="cat">
							<a class="max <?php echo $class; ?>" href="<?php echo home_url() . '/blog/category/' . $cat->slug; ?>">
								<?php echo $cat->name; ?>
							</a>
						</li>
						<?php endif; ?>
						<?php endforeach; ?>

					</ul>
					<?php endif;  ?>
				</div>

				<!-- post content -->
				<section class="content col span_3_of_4">
					<?php while ( have_posts()) : the_post(); ?>

					<article class="post-container">
						<!-- <div class="img-container">
							<a class="max img-hover" href="<?php the_permalink(); ?>">
								<?php at_add_picturefill_img( array(
									'sizes' => '(max-width: 48em) 100vw, (max-width: 105em) 75vw, 1260px'
									) ); ?>
							</a>
						</div> -->

						<div class="group section">
							<div class="post-metadata col span_1_of_4">
								<p class="author">
									<?php echo get_avatar( get_the_author_meta('ID') ); ?>
									<span><strong><?php the_author(); ?></strong></span>
								</p>
								<p class="date">
									<strong><?php the_date(); ?></strong>
								</p>
								<p class="categories">
									<strong><?php _ex('Posted in:', 'blog posts page', 'earthinnovate'); ?></strong><br />
									<?php $post_terms = wp_get_post_terms($post->ID, 'category');
									$count = count($post_terms);


									foreach ($post_terms as $index => $term) : ?>
										<strong><a href="<?php echo home_url() . '/blog/category/' . $term->slug; ?>"><?php echo $term->name; ?></a>
										<?php if ((int) $index !== $count -1) echo ', '; ?></strong>
									<?php endforeach; ?>
								</p>
							</div>
							<div class="post-content col span_3_of_4">
								<h4 class="post-title"><?php the_title(); ?></h4>
								<p><?php echo at_get_custom_excerpt(250); ?></p>
								<a class="more-link" href="<?php the_permalink(); ?>"><?php _ex('View Post', 'blog posts page', 'earthinnovate'); ?></a>
							</div>
						</div>

						<hr />
					</article>

					<?php endwhile; ?>
				</section>
			</div><!-- .blog-container -->

			<?php else : ?>
			<div class="no-content">
				<p><?php _e('Sorry, there are no articles to show','earthinnovate'); ?>.</p>
			</div>
			<?php endif; ?>

		</div><!-- .main-content -->

<?php get_footer(); ?>