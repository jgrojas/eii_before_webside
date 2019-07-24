<?php
/**
 * The main template file
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file 
 *
 *
 * @package 	WordPress
 * @subpackage 	Earth Innovate
 * @since 		1.0
 */
get_header(); 	 

if (have_posts()) : while (have_posts()) : the_post(); ?>

	<div class="content aligncenter">
		<?php the_content(); ?>
	</div>
	
<?php endwhile; endif; 

get_footer(); 	?>