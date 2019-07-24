<?php /*
*
* Custom loop used for AJAX Filtering (via FacetWP plugin)
*
* @package WordPress
* @subpackage Earth Innovate
* @since Version 1.0
*
*/

while (have_posts()) : the_post(); ?>
	<article class="col span_1_of_5">

		<a class="max img-hover img" href="<?php the_permalink(); ?>">
			<?php $post_thumbnail = get_the_post_thumbnail(); 
			if ( '' != $post_thumbnail )
				the_post_thumbnail('medium'); 
			else 
				echo '<img src="'.get_template_directory_uri().'/images/default-publication.png" />';  ?>
			
			<?php $img_credits = get_post_field('post_content', get_post_thumbnail_id() );
			if (($img_credits) && ('' != $post_thumbnail)){ ?>
			<span class="photo-credits"><?php echo $img_credits; ?></span>
			<?php } ?>
		</a>		
		<div class="excerpt">
			<div>
			
				<h6 class="title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h6>
				
				<?php $subheading = get_the_time('Y'); 
				if ($subheading) { ?>
				<h6 class="subheading"><?php echo $subheading; ?></h6>
				<?php } ?>
			
			</div>
		</div><!-- .excerpt -->
		
	</article>
<?php endwhile; ?>
