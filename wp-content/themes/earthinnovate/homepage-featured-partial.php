<div id="featured" class="section group">
			<?php // check if option is stored in DB
			$featured = get_option('home_featured_posts');
			// if so, then create a wp_query using the post IDs
			if ($featured) :
				global $post;

				for ($i = 1; $i < 4; $i++){
					$box = 'box'.(string) $i;
					$img = 'img'.(string) $i;

					$post = get_post( (int) $featured[$box] );
					if ($post) :
						setup_postdata($post); ?>
						<article class="col span_1_of_3">
							<div class="image">
								<?php $args = array(
									'img_id' => (int) $featured[$img],
									'before' => '<a class="max img-hover" href="'. get_permalink() .'">',
									'after' => '</a>',
									'sizes' => 'column'
									);
								at_add_picturefill_img($args); ?>
							</div>
							<div class="excerpt">
								<div>
									<h5 class="title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
									<p><?php echo at_get_custom_excerpt(250); ?></p>
									<a class="more-link" href="<?php the_permalink(); ?>"><?php _e('Read More', 'earthinnovate'); ?></a>
								</div>
							</div>
						</article>
				 	<?php endif; // end post display
				} // end for loop

				wp_reset_postdata();
			endif; // end featured post display ?>
		</div><!-- #featured -->