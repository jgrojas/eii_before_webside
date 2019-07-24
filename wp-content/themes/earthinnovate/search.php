<?php
/**
 * The template for displaying Search Results pages
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 * @since Twenty Fourteen 1.0
 */

get_header();

	$query = get_search_query();
	// $search_url = home_url('/?s=') . urlencode($query);
	// add search terms to the query
	/*
global $wp;
	$current_url = add_query_arg( $wp->query_string, '', home_url( '/'.$wp->request ) );
*/?>

	<div id="main-content">
		<h3 class="page-title"><?php printf( __( 'Search Results for: "%s"', 'earthinnovate' ), $query ); ?></h1>
		<!--
<div class="filters">
		<span><em>Sort by: </em></span>
		<ul>
		<?php /*
$filters = array(
			'All' => '',
			'Publications' => '&post_type=at_publications',
			'Resources' => '&post_type=at_resources',
			'Staff' => '&post_type=at_staff'
			);
		foreach ($filters as $title => $filter ) :  ?>
			<?php $url = $search_url . $filter;
			if ($current_url === $url) : $class = 'current'; else : $class = ''; endif; ?>
			<li class="filter"><a class="<?php echo $class; ?>" href="<?php echo $url; ?>"><?php echo $title; ?></a></li>
		<?php endforeach;
*/ ?>
		</ul>
		</div>
--><!-- end .filters -->

		<section class="results">
		<?php global $more, $wp_post_types; $more = 0; // pull some global variables, also make sure only excerpts show (not full content)
		if (have_posts()) : while ( have_posts() ) : the_post(); // start loop  ?>
		 	<article>
		 		<!--
<div class="img-container col span_3_of_8">
		 			<?php /*

		 			if (($post_type == 'at_staff') || ($post_type == 'at_board_members')) : $class = 'person'; elseif ($post_type == 'at_publications') : $class = 'publication'; else : $class = ''; endif; ?>
			 		<a class="max img img-hover <?php echo $class; ?>" href="<?php the_permalink(); ?>">
			 			<?php $post_thumbnail = get_the_post_thumbnail();
			 			if ('' != $post_thumbnail) :
			 				$args = array( 'before' => '', 'after' => '', 'align' => 'column' );
			 				at_add_picturefill_img($args);
			 			elseif (('' == $post_thumbnail) && ($post_type == 'at_publications')) :
			 				echo '<img src="'. get_template_directory_uri() .'/images/default-publication.png" />';
			 			elseif (('' == $post_thumbnail) && (($post_type == 'at_staff') || ($post_type == 'at_board_members'))) :
			 				echo '<img src="'. get_template_directory_uri() .'/images/default-headshot.png" />';
			 			else :
			 				echo '<img src="'. get_template_directory_uri() .'/images/default-partner.png" />';
			 			endif;
*/ ?>
			 		</a>
		 		</div>
-->
		 		<div class="content">
		 			<h4 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
		 			<?php $post_type = get_post_type($post->ID);
		 			if ($post_type == 'page') : $permalink = get_permalink($post->post_parent); $title = get_the_title($post->post_parent);
		 			else : $obj = $wp_post_types[$post_type]; $permalink = get_post_type_archive_link($post_type); $title = $obj->labels->name;
		 			endif; ?>
		 			<h5 class="parent-title"><a href="<?php echo esc_attr($permalink); ?>"><?php echo $title; ?></a></h5>
		 			<p>
			 			<?php if( function_exists( 'searchwp_term_highlight_the_excerpt_global' ) ) {
			 				searchwp_term_highlight_the_excerpt_global(); // echo excerpt or content for post (with search terms highlighted)
						} ?>
					</p>
		 		</div>
		 	</article>
		<?php endwhile; ?>

		<?php at_archive_pagination(); ?>

		<?php else :  ?>
		<div class="no-content"><p><?php _e('Sorry, nothing could be found.', 'earthinnovate'); ?></p></div>
		<?php endif; ?>
		</section>

	</div><!-- #main-content -->

<?php get_footer(); ?>
