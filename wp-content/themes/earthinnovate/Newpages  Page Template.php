<?php
/*
 * Template Name: New Pages
 *
 * @package WordPress
 * @subpackage Earth Innovate
 * @since Version 1.0
 */

get_header();

global $sitepress;
$current_lang = $sitepress->get_current_language();
//get the default language
$default_lang = $sitepress->get_default_language();
//fetch posts in default language
$sitepress->switch_lang($default_lang);

if (have_posts()) : while (have_posts()) : the_post(); ?>

	<div id="main-content">
		<style>
			.bm-headshot img {
				width: 130px;
			}
		</style>
		<h3 class="page-title"><span><?php the_title();  ?></span></h3>
		<section class="">
			<div class="content group section">	
		
									
					<?php the_content(); ?>
				
			</div>
		</section>
			


	</div><!--#main-content-->

<?php endwhile; endif;
$sitepress->switch_lang($current_lang);
get_footer(); ?>
