<?php

	/**

	 * Functions and definitions

	 *

 	 * @package 	WordPress

 	 * @subpackage 	Earth Innovate

 	 * @since 		Version 1.0

	 */



	 require_once 'meta-boxes.php';

	 require_once 'homepage_options/homepage_options.php';



	/* ======================================================================================================================



	Actions and Filters



	======================================================================================================================== */



	add_action('after_setup_theme', 'at_theme_settings');

	add_action( 'wp_enqueue_scripts', 'at_add_scripts' ); // add scripts

	add_action( 'init', 'at_add_cpts'); // add custom post types

	add_action('init', 'at_add_taxonomies'); // add custom taxonomies

	add_action( 'init', 'at_add_excerpts_to_pages' ); // add excerpts to pages

	add_action('after_switch_theme', 'at_convert_pages_to_categories'); // custom function to add terms based on page titles

	add_action( 'wpdonations_update_donation_data', 'at_add_donation_fields_save', 10, 2 ); // add 'country' field to donation form

// 	add_action('pre_get_posts', 'at_modify_pubs_archive_query'); // modify main query for publication archive page (frontend only)



	add_filter( 'the_content', 'at_remove_empty_p', 20, 1 ); // better formatting within WP editor

	add_filter('mce_buttons_2', 'at_my_mce_buttons_2'); // add subscript/superscript buttons

	add_filter( 'submit_donation_form_fields', 'at_customize_donation_form_fields' ); // customize donation form fields

	add_filter( 'donation_available_amounts', 'at_custom_donation_amounts' ); // modify preset donation amounts

	add_filter( 'searchwp_th_auto_filter_excerpt', '__return_false' ); // remove default searchwp excerpt behavior

	add_filter('facetwp_facet_html','at_modify_search_html', 10, 2);

	add_filter( 'body_class', 'at_add_body_classes' );

// 	add_filter('dynamic_sidebar_params','at_bottom_sidebar_params');



	/* ========================================================================================================================



	Theme-specific settings



	======================================================================================================================== */



	/************ Register basic theme settings ************************/

	function at_theme_settings(){

		/****** Add support for featured mages & html5 ******/

		add_theme_support('post-thumbnails');

		add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption' ) );

		/****** Add image size for media uploads *******/

		add_image_size('x-large', 1440);

		/******* Navigation menus ******/

		register_nav_menus( array(

			'mobile_nav' => 'Mobile Navigation',

			'primary_nav' => 'Primary Navigation',

			'secondary_nav' => 'Secondary Navigation'

			) );

		/****** Footer Widget areas *******/

		register_sidebars( 3, array(

			'name' => 'Footer Widget %d',

			'id' => 'footer-widget',

			'class' => 'footer-widget',

			'before_widget' => '<div class="footer-widget bit">',

			'after_widget' => '</div>',

			'before_title' => '<h6 class="widget-title">',

			'after_title' => '</h6>'

			) );

		register_sidebar( array(

			'name' => __('Homepage Right Sidebar','earthinnovate'),

			'id' => 'homepage-right-sidebar',

			'description' => 'The right side sidebar on homepage', 

			));

	}



	/********* Add a second featured image (via Multiple Featured Images plugin) ********/

	if( class_exists( 'kdMultipleFeaturedImages' ) ) {

		$args = array(

			'id' => 'thumbnail-image',

			'post_type' => 'page',      // post or page

			'labels' => array(

				'name' => 'Thumbnail Image',

				'set' => 'Set Thumbnail Image',

				'remove' => 'Remove Thumbnail Image',

				'use' => 'Use as Thumbnail Image'

				)

			);

		new kdMultipleFeaturedImages( $args );

	}



/* ========================================================================================================================



	Custom functions



	======================================================================================================================== */



	/********* Convert pages to categories for filtering purposes ********/

	function at_convert_pages_to_categories() {

		//define variables & arrays

		$pages = array();

		$parent_ids = array();

		$cat = '';

		$page_parent = '';

		$cat_parent = '';



		// build multi-dimensional array

		$multi_page_array = array(

			get_pages( array( 'sort_column' => 'menu_order', 'include' => '2,5,7' )),

			get_pages( array( 'sort_column' => 'menu_order', 'child_of' => 2, 'post_status' => 'publish')),

			get_pages( array( 'sort_column' => 'menu_order', 'child_of' => 5, 'post_status' => 'publish')),

			get_pages( array( 'sort_column' => 'menu_order', 'child_of' => 7, 'post_status' => 'publish'))

			);

		// flatten array

		array_walk_recursive($multi_page_array, function($values) use (&$pages) { $pages[ ] = $values; });

		//start loop for each page

		foreach ($pages as $page) :

			// if term already exists, skip to next page

			$cat = get_post_meta( $page->ID, '_cat_id', true );

			if ( $cat ) break 1;



			// if term doesn't exist, create term

			// but first, determine if parent term exists

			$page_parent = $page->post_parent;

			if ($page_parent !== 0 && isset($parent_ids[$page_parent])) :

				$cat_parent = $parent_ids[$page_parent];

			else :  $cat_parent = 0;

			endif;



			// create term

			$cat = wp_insert_term( $page->post_title, 'at_filters', array(

				'parent' => $cat_parent,

				'slug' => $page->post_name

				) );

			// if valid category was created, store page & associated category IDs

			if ( ! is_wp_error( $cat )) :

				$cat_id = (int)$cat['term_id'];

				$parent_ids[ $page->ID ] = $cat_id;

				// also add post meta to store this information

				add_post_meta($page->ID, '_cat_id', $cat_id, true);

			else:

				$error = $cat->get_error_message();

				var_dump($error);

			endif;

		endforeach;

	} // end 'at_convert_pages_to_categories()'



	/********* Add a responsive image via picture element **********/

	$img_sizes = array('medium', 'large', 'x-large', 'full');

	$img_sizes = array_reverse($img_sizes);



	function at_add_picturefill_img( $args = '' ) {

		global $img_sizes;

		$defaults = array(

			'img_id' => get_post_thumbnail_id(),

			'before' => '<div class="hero-content trans-center">',

			'after' => '</div>',

			'sizes' => '',

			'echo' => true

			);

		$args = wp_parse_args($args, $defaults);

		extract($args);

		// determine appropriate size based on circumstances

		$img_fallback = wp_get_attachment_image_src($img_id, $img_sizes[3]);

		if ($img_fallback) : // if image exists

			$img_alt = get_post_meta($img_id, '_wp_attachment_image_alt', true);

			$img_credits = get_post_field('post_content', $img_id);



		if ($before)

			$image = $before . "\n\t";



		if ($sizes) {

			if ($sizes == 'column')

				$breakpoints = '(max-width: 48em) 100vw, (max-width: 75em) 33.333vw, 525px';

			elseif ($sizes == 'column-fw')

				$breakpoints = '(max-width: 48em) 100vw, 33.333vw';

			elseif ($sizes == 'sidebar')

				$breakpoints = '(max-width: 48em) 100vw, (max-width: 75em) 36vw, 480px';

			else

				$breakpoints = $sizes;



			$image .= "<img sizes='". $breakpoints ."' srcset='";

		} else {

			$image .= "<picture>\n\t";

			$image .= "<!--[if IE 9]><video style='display: none;'><![endif]-->\n";

		}



		$length = count($img_sizes);

		for( $i=0; $i < $length; ++$i) {

			$current = current($img_sizes);

			$next = next($img_sizes);

			$current_src = wp_get_attachment_image_src($img_id, $current);



			if ($next === false) : $next_src = null; $next_src_w = '';

			else: $next_src = wp_get_attachment_image_src($img_id, $next);

				$next_src_w = "media='(min-width: ".($next_src[1]+1) ."px)'";

			endif;



			if ($current_src){

				if ($sizes){

					$image .= $current_src[0] ." ". $current_src[1] ."w";

					$image .= ($next === false ? "" : ", " );

				} else {

				$image .= "<source srcset='". $current_src[0] . " ".$current_src[1]."w' ".$next_src_w.">\n";

				}

			}



			if ($i == $length - 1) reset($img_sizes);

		} // end for loop



		if ($sizes) {

			$image .= "' "; // end srcset attr

		} else {

			$image .= "<!--[if IE 9]></video><![endif]-->\n";

			$image .= "<img srcset='". $img_fallback[0] ."' ";

		}



		if (count($img_alt))

			$image .= "alt='". $img_alt ."' ";



			$image .= "/>\n";



		if (!$sizes)

			$image .= "</picture>\n";



		if ($img_credits)

			$image .= "<span class='photo-credits'>".$img_credits."</span>\n";



		if ($after)

			$image .= $after . "\n";



		if ( !$echo )

			return $image;



			echo $image;

		endif; // end image display



		return false; // if no image exists, do nothing



	} // end 'at_add_picturefill_img()'



	/*********** Add dynamic content articles via custom taxonomy query **********/

	function at_add_dynamic_content( $args = '' ){

		$defaults = array(

			'filter_id' => '',

			'post_type' => '',

			'ppp' => 3,

			'order' => 'DESC',

			'orderby' => 'date'

			);

		$args = wp_parse_args($args, $defaults);

		extract($args);



		$my_query = new WP_Query( array(

			'post_type' => $post_type,

			'order' => $order,

			'orderby' => $orderby,

			'posts_per_page' => $ppp,

			'tax_query' => array(

				array(

					'taxonomy' => 'at_filters',

					'terms' => $filter_id,

					'include_children' => false

				) )

			) );

		if ($my_query->have_posts()) :

			$article = "<div class='group section'>\n\t";



		while ($my_query->have_posts()) : $my_query->the_post();

			if ($post_type == 'at_publications' && $post_status=='publish') : $class = ' span_1_of_8'; $link_before = '<a class="max img-hover" href="'.get_permalink().'">'; $link_after = '</a>'; $sizes = '(max-width: 30em) 49vw, (max-width: 48em) 32vw, (max-width: 75em) 23vw, (max-width: 105em) 15vw, 10vw';

			else : $class = ' span_1_of_6'; $link_before = ''; $link_after = ''; $sizes = '(max-width: 30em) 49vw, (max-width: 60em) 32vw, (max-width: 90em) 23vw, 15vw';

			endif;



			$article .= "<article class='col".$class."'>\n\t";

		$args = array(

			'before' => $link_before,

			'after' => $link_after,

			'sizes' => $sizes,

			'echo' => false

			);

		$img = at_add_picturefill_img( $args );



		if ( !$img && $post_type == 'at_publications' && $post_status=='publish')

			$article .= $link_before."<img src='".get_template_directory_uri()."/images/default-publication.png' />".$link_after."\n";

		elseif (!$img && $post_type == 'at_partners')

			$article .= "<img src='".get_template_directory_uri()."/images/default-partner.png' />\n";

		else

			$article .= $img;



			$article .= "<h6>". $link_before . get_the_title() . $link_after . "</h6>\n";

			$article .= "</article>\n";

		endwhile;

			$article .= "</div>\n";



		else : $article = false;



		endif; wp_reset_postdata();



		return $article;



	} // end 'at_add_dynamic_content()'



	/********** Add excerpt to page post type ***********/

	function at_add_excerpts_to_pages() {

		add_post_type_support( 'page', 'excerpt' );

	}



	/*********** Get excerpt of custom length **********/

	function at_get_custom_excerpt($count){

	if (has_excerpt())

		$excerpt = get_the_excerpt();

	else

		$excerpt = get_the_content();



	$excerpt = strip_shortcodes($excerpt);



	$excerpt = preg_replace('/\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|$!:,.;]*[A-Z0-9+&@#\/%=~_|$]/i', '', $excerpt);



	if ($excerpt) :

		$excerpt = preg_replace(" (\[.*?\])",'',$excerpt);

		$excerpt = strip_shortcodes($excerpt);

		$excerpt = strip_tags($excerpt);

		$excerpt = substr($excerpt, 0, $count);

		$excerpt = substr($excerpt, 0, strripos($excerpt, " "));

		$excerpt = trim(preg_replace( '/\s+/', ' ', $excerpt));

		$excerpt = $excerpt.' ...';

		return $excerpt;

	else :

		return false;

	endif;

		} // end 'at_get_custom_excerpt()'



	/************ Removes empty p tags from content ************/

	 function at_remove_empty_p( $content ){

	$content = preg_replace( array(

		'#<p>\s*<(div|aside|section|article|header|footer)#',

		'#</(div|aside|section|article|header|footer)>\s*</p>#',

		'#</(div|aside|section|article|header|footer)>\s*<br ?/?>#',

		'#<(div|aside|section|article|header|footer)(.*?)>\s*</p>#',

		'#<p>\s*</(div|aside|section|article|header|footer)#',

	), array(

		'<$1',

		'</$1>',

		'</$1>',

		'<$1$2>',

		'</$1',

	), $content );



	return preg_replace('#<p>(\s|&nbsp;)*+(<br\s*/*>)*(\s|&nbsp;)*</p>#i', '', $content);

}



	/*********** Add sub/sup buttons to WP editor ************/

	function at_my_mce_buttons_2($buttons) {

		$buttons[] = 'superscript';

		$buttons[] = 'subscript';



		return $buttons;

	}



	/********* Filter fields on donation form **************/

	function at_customize_donation_form_fields( $fields ) {

		// remove unwanted fields

		unset($fields['donation']['donation_payment_type']);

		unset($fields['donation']['donation_campaign']);

		unset($fields['donation']['donation_message']);

		unset($fields['donor']['donor_website']);

		unset($fields['donor']['donor_logo']);



		// modify certain fields

		$fields['donation']['donation_amount']['label'] = __('Select Your Contribution Amount','wpdonations');

		$fields['donation']['donation_amount']['placeholder'] = __('Enter amount. Please enter numbers only (no periods, commas or symbols)','wpdonations');

		$fields['donation']['recurring_donation']['label'] = __('Frequency','wpdonations');

		$fields['donation']['recurring_donation']['description'] = '';

		$fields['donor']['donor_firstname']['label'] = __('First Name','wpdonations');

		$fields['donor']['donor_firstname']['placeholder'] = '';

		$fields['donor']['donor_lastname']['label'] = __('Last Name','wpdonations');

		$fields['donor']['donor_lastname']['placeholder'] = '';

		$fields['donor']['donor_address']['required'] = true;

		$fields['donor']['donor_address']['placeholder'] = '';

		$fields['donor']['donor_town']['label'] = __('City, State','wpdonations');

		$fields['donor']['donor_town']['required'] = true;

		$fields['donor']['donor_town']['placeholder'] = '';

		$fields['donor']['donor_town']['priority'] = 4;

		$fields['donor']['donor_zip']['label'] = __('Postal Code','wpdonations');

		$fields['donor']['donor_zip']['required'] = true;

		$fields['donor']['donor_zip']['priority'] = 5;

		$fields['donor']['donor_zip']['placeholder'] = '';



		// add field for donor country

		$fields['donor']['donor_country'] = array(

			'label' => __('Country', 'wpdonations'),

			'placeholder' => '',

			'type' => 'text',

			'required' => true,

			'priority' => 6

		);



		return $fields;

	}



	// Save field(s) data

	function at_add_donation_fields_save( $donation_id, $values ) {

		update_post_meta( $donation_id, '_donor_country', $values['donor']['donor_country'] );

	}



	/********* Modify preset donation amounts ***********/

	function at_custom_donation_amounts() {

		$options = array(

			'50' => '50',

			'100' => '100',

			'250' => '250',

			'500' => '500',

			'1000' => '1000',

			'2500' => '2500'

		);



		return $options;

	}

	/********* Add class for resource teaser page **********/

	function at_add_body_classes( $classes ) {

		if (is_page('Resources'))

			$classes[] = 'teaser';



		elseif ( is_post_type_archive( array('at_staff', 'at_board_members')))

			$classes[] = 'post-type-archive-at_people';



		elseif ( is_singular( array( 'at_staff','at_board_members' )))

			$classes[] = 'single-at_people';



		return $classes;

	}



	# ==================================================== #

	# ==== Get terms for use within resources archive ==== #

	# ==================================================== #

	function at_get_resource_types( $args = '' ){

		// merge defaults and function arguments

		$defaults = array(

			'orderby' => 'name',

			'order' => 'ASC',

			'hide_empty' => false,

			'fields' => 'all',

			'sidebar' => true,

			'echo' => false

			);

		$args = wp_parse_args($args, $defaults);

		extract($args);



		// get resource terms to display

		$terms = get_terms('at_types',  array(

			'orderby' => $orderby,

			'order' => $order,

			'hide_empty' => $hide_empty,

			'fields' => $fields

			) );



		// set classes based upon whether $sidebar is true/false

		if ( ! $sidebar ) {

			$class = ' col span_1_of_3 ';

			$heading = 'h4';

		} else {

			$class = ' ';

			$heading = 'h5';

		}



		// get current query var for use in foreach loop

		$type_query_var = get_query_var('type');

		$output = '';

		foreach ($terms as $term) :

			// add to $class variable only if current page matches term

			$current = $type_query_var === $term->slug ? 'current ' : ' ';



			// begin html output

			$output .=  "<a class='resource-buttons max". $class . $current ."' href='". get_post_type_archive_link('at_resources') . "?type=". $term->slug ."' >\n\t";



			// create link to appropriate icon, using term slug

			$icon_class = rtrim($term->slug, 's');

			if ( ! $sidebar )

				$icon_class .= '-small';

			else

				$icon_class .= '_thumb-small';



			$output .= "<div>\n\t";

			$output .= "<div class='icon ". $icon_class ."'></div>\n";

			$output .= "<". $heading ." class='resource-title' >". $term->name ."</". $heading .">\n";

			$output .= "</div>\n</a>";

		endforeach;



		// add .current class if query var is blank

		$current = $type_query_var == '' ? 'current ' : ' ';

		$output .= "<a class='resource-buttons max". $class . $current ."last' href='". get_post_type_archive_link('at_resources') ."' >\n\t";

		$output .= "<div>\n\t";

		$output .= "<". $heading ." class='resource-title last' >". __('All','earthinnovate') ."</". $heading .">\n";

		$output .= "</div>\n</a>";



		if ( $echo )

			echo $output;

		else

			return  $output;

	}



	# ===================================================================== #

	# ==== Modify html for publications archive (search functionality) ==== #

	# ===================================================================== #

	function at_modify_search_html($output, $params){

		if ('search' == $params['facet']['name']){

			$output = "<div class='facetwp-facet facetwp-facet-search facetwp-type-autocomplete' data-name='search' data-type='autocomplete'>\n\t";

			$output .= "<input type='search' class='facetwp-autocomplete' value='' placeholder='". _x('Author, Keyword, etc.', 'publications search text', 'earthinnovate') . "' autocomplete='off' />\n";

			$output .=  "<input type='button' class='facetwp-autocomplete-update' value='>' />\n";

			$output .= "</div>\n";

		}



		return $output;

	}



	/****** Add paging functions to archive and search pages **********/

	function at_archive_pagination($args = '') {

		$defaults = array(

			'total' => $GLOBALS['wp_query']->max_num_pages

			);

		$args = wp_parse_args($args, $defaults);

		extract($args);



		// Don't print empty markup if there's only one page.

		if ( $total < 2 ) {

			return;

		}



		$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;

		$pagenum_link = html_entity_decode( get_pagenum_link() );

		$query_args   = array();

		$url_parts    = explode( '?', $pagenum_link );



		if ( isset( $url_parts[1] ) ) {

			wp_parse_str( $url_parts[1], $query_args );

		}



		$pagenum_link = remove_query_arg( array_keys( $query_args ), $pagenum_link );

		$pagenum_link = trailingslashit( $pagenum_link ) . '%_%';



		$format  = $GLOBALS['wp_rewrite']->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';

		$format .= $GLOBALS['wp_rewrite']->using_permalinks() ? user_trailingslashit( 'page/%#%', 'paged' ) : '?paged=%#%';



		// Set up paginated links.

		$links = paginate_links( array(

			'base'     => $pagenum_link,

			'format'   => $format,

			'total'    => $total,

			'current'  => $paged,

			'mid_size' => 1,

			'add_args' => array_map( 'urlencode', $query_args ),

			'prev_text' => _x('Previous', 'page navigation', 'earthinnovate'),

			'next_text' => _x('Next', 'page navigation', 'earthinnovate')

		) );



		if ( $links ) : ?>

		<nav class="navigation paging-navigation" role="navigation">

			<h1 class="screen-reader-text"><?php _e( 'Posts navigation', 'earthinnovate' ); ?></h1>

			<div class="pagination loop-pagination">

				<?php echo $links; ?>

			</div><!-- .pagination -->

		</nav><!-- .navigation -->

		<?php endif;

	}



	/* ====================================================================================================================



	Shortcodes



	======================================================================================================================*/



	/********** Create one-page "sections" ************/

	$nav_title = ''; //assign nav title as global variable

	add_shortcode('section', 'at_section_shortcode');

	function at_section_shortcode ($atts, $content = null) {

		global $nav_title;

		global $post;



		extract(shortcode_atts( array(

			'style' => '',

			'nav_title' => '',

			'map_id' => ''

			), $atts, 'section'));



		if (has_shortcode($content, 'one_third') || has_shortcode($content, 'one_half') || has_shortcode($content, 'responsive_img')) :

			$column = 'group section';

		else : $column = ''; endif;

		if ($style == 'dark'){ $class = 'dark textured'; } else { $class = ''; }

			$content = wpautop($content);

			$content = do_shortcode($content);



		if ($nav_title || $map_id) :

		$pages = wp_list_pages( array(

			'sort_column' => 'menu_order',

			'child_of' => get_the_ID(),

			'exclude' => '237',

			'depth' => 2,

			'title_li' => null,

			'echo' => false

			) );

			$section .= "<section class='page-overview page-section'>\n\t<div class='content section group'>\n\t";

			$section .= "<div class='content col span_5_of_8'>" . $content . "</div>\n";

			$section .= "<div class='pagenav col span_3_of_8'>\n\t";

		if ($map_id) :

			$map_id = (int) $map_id;

			$args = array(

				'img_id' => $map_id,

				'before' => '<div class="map">',

				'after' => '</div>',

				'sizes' => 'sidebar',

				'echo' => false

				);



			$section .= at_add_picturefill_img($args);

			if ($pages) : $section .= "<h5 class='pagenav-title'>" . __('Where We Work in ','earthinnovate') . $post->post_title . "</h5>\n"; endif;

		elseif ($nav_title && $pages) : $section .= "<h5 class='pagenav-title'>". $nav_title . "</h5>\n";

		else : return;

		endif;

			if ($pages) :

				$section .= "<ul>\n\t";

				$section .= $pages;

				$section .= "</ul>\n";

			endif;

			$section .= "</div>\n";

			$section .= "</div></section>\n";

		else :

			$section .= "<section class='" . $class . " page-section' >\n\t<div class='content ". $column ."'>\n\t" . $content . "</div></section>";

		endif;



		return $section;

	} // end 'section' shortcode



	/********** Add a highlights/quick facts section ********/

	add_shortcode('highlights', 'at_highlights_shortcode');

	function at_highlights_shortcode($atts, $content = null){

		$count = substr_count( $content, '[highlight');

		$content = do_shortcode($content);



		return '<div class="highlights-'.$count.'"><ul class="group section">'.$content.'</ul></div>';

	} // end 'highlights' shortcode



	/******** Add an individual highlight to highlights module ********/

	add_shortcode('highlight', 'at_single_highlight_shortcode');

	function at_single_highlight_shortcode($att, $content = null){

		$highlight = "<li class='col span_1_of_3'>\n\t";

		$highlight .= "<div class='table circle'>\n\t";

		$highlight .= "<div class='table-cell'>\n\t";

		$highlight .= "<p class='table-content'>".$content."</p>\n";

		$highlight .= "</div>\n</div>\n</li>\n";



		return $highlight;

	} // end 'highlight' shortcode



	/********* Add custom user-defined button **********/

	add_shortcode('button', 'at_add_button');

	function at_add_button($args) {

		extract(shortcode_atts(array(

			'label' 	=> __('Read More','earthinnovate'),

			'id' 		=> '1',

			'url'		=> '',

			'target' 	=> '_parent',

			'color' => 'teal'

		), $args ));



		$link = $url ? esc_url($url) : get_permalink($id);



		return '<a href="'.$link.'" target="'.$target.'" class="more-link btn-'.$color.'">'.$label.'</a>';

	} // end 'button' shortcode



	/********* Add column layouts *********/

	/* One-third */

	add_shortcode('one_third', 'at_one_third');

	function at_one_third( $atts, $content = null ) {

		return '<div class="span_1_of_3 col">' . do_shortcode(wpautop($content)) . '</div>';

	}

	/* Two-third */

	add_shortcode('two_third', 'at_two_third');

	function at_two_third( $atts, $content = null ) {

		return '<div class="span_2_of_3 col">' . do_shortcode(wpautop($content)) . '</div>';

	}

	/* One-half */

	add_shortcode('one_half', 'at_one_half');

	function at_one_half( $atts, $content = null ) {

		return '<div class="span_1_of_2 col">' . do_shortcode(wpautop($content)) . '</div>';

	}



	/* ========================================================================================================================



	Custom Post Types & Taxonomies



	======================================================================================================================== */



	/****** CPTs ******/

	function at_add_cpts() {

		/* Annual report */

		$args = array(

			'labels' => array(

				'name' => 'Reports',

				'singular_name' => 'Report',

				'all_items' => 'All Reports',

				'add_new_item' => 'Add New Report',

				'edit_item' => 'Edit Report',

				'new_item' => 'New Report',

				'view_item' => 'View Report',

				'search_items' => 'Search All Reports',

				'not_found' => 'No Reports found',

				'not_found_in_trash' => 'No Reportss found in Trash'

				),

			'public' => true,

			'menu_position' => 20,

			'menu_icon' => 'dashicons-format-aside',

			'supports' => array( 'title', 'editor', 'thumbnail', 'page-attributes', 'revisions'),

			'has_archive' => true,

			'rewrite' => array( 'slug' => 'reports', 'feeds' => false ),

			'query_var' => 'reports'

		);

		register_post_type( 'at_reports', $args);

		/* Publications */

		$args = array(

			'labels' => array(

				'name' => 'Publications',

				'singular_name' => 'Publication',

				'all_items' => 'All Publications',

				'add_new_item' => 'Add New Publication',

				'edit_item' => 'Edit Publication',

				'new_item' => 'New Publication',

				'view_item' => 'View Publication',

				'search_items' => 'Search All Publications',

				'not_found' => 'No Publications found',

				'not_found_in_trash' => 'No Publications found in Trash'

				),

			'public' => true,

			'menu_position' => 20,

			'menu_icon' => 'dashicons-format-aside',

			'supports' => array( 'title', 'editor', 'thumbnail', 'page-attributes', 'revisions'),

			'has_archive' => true,

			'rewrite' => array( 'slug' => 'publications', 'feeds' => false ),

			'query_var' => 'publication'

		);

		register_post_type( 'at_publications', $args);

		/* Resources */

		$args = array(

			'labels' => array(

				'name' => 'Resources',

				'singular_name' => 'Resource',

				'all_items' => 'All Resources',

				'add_new_item' => 'Add New Resource',

				'edit_item' => 'Edit Resource',

				'new_item' => 'New Resource',

				'view_item' => 'View Resource',

				'search_items' => 'Search All Resources',

				'not_found' => 'No Resources found',

				'not_found_in_trash' => 'No Resources found in Trash'

				),

			'public' => true,

			'menu_position' => 20,

			'menu_icon' => 'dashicons-share',

			'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions'),

			'has_archive' => true,

			'rewrite' => array( 'slug' => 'resources/browse', 'feeds' => false ),

			'query_var' => 'resource'

		);

		register_post_type( 'at_resources', $args);

		/* Board Members */

		$args = array(

			'labels' => array(

				'name' => 'Board Members',

				'singular_name' => 'Board Member',

				'all_items' => 'All Board Members',

				'add_new_item' => 'Add New Board Member',

				'edit_item' => 'Edit Board Member',

				'new_item' => 'New Board Member',

				'view_item' => 'View Board Member',

				'search_items' => 'Search All Board Members',

				'not_found' => 'No Board Members found',

				'not_found_in_trash' => 'No Board Members found in Trash'

				),

			'public' => true,

			'menu_position' => 65,

			'menu_icon' => 'dashicons-businessman',

			'supports' => array( 'title', 'editor', 'thumbnail', 'page-attributes', 'revisions'),

			'has_archive' => true,

			'rewrite' => array( 'slug' => 'about/board-members', 'feeds' => false ),

			'query_var' => 'board_member'

		);

		register_post_type( 'at_board_members', $args);

		/* Advisory Council */

		$args = array(

			'labels' => array(

				'name' => 'Advisory Council Members',

				'singular_name' => 'Advisory Council Member',

				'all_items' => 'All Advisory Council Members',

				'add_new_item' => 'Add New Advisory Council Member',

				'edit_item' => 'Edit Advisory Council Member',

				'new_item' => 'New Advisory Council Member',

				'view_item' => 'View Advisory Council Member',

				'search_items' => 'Search All Advisory Council Members',

				'not_found' => 'No Advisory Council Members found',

				'not_found_in_trash' => 'No Advisory Council Members found in Trash'

				),

			'public' => true,

			'menu_position' => 65,

			'menu_icon' => 'dashicons-businessman',

			'supports' => array( 'title', 'editor', 'thumbnail', 'page-attributes', 'revisions'),

			'has_archive' => true,

			'rewrite' => array( 'slug' => 'about/advisory-council-member', 'feeds' => false ),

			'query_var' => 'advisory_council'

		);

		register_post_type( 'at_advisory_council', $args);

		/* Staff */

		$args = array(

			'labels' => array(

				'name' => 'Staff',

				'all_items' => 'All Staff',

				'add_new_item' => 'Add New Staff',

				'edit_item' => 'Edit Staff',

				'new_item' => 'New Staff',

				'view_item' => 'View Staff',

				'search_items' => 'Search All Staff',

				'not_found' => 'No Staff found',

				'not_found_in_trash' => 'No Staff found in Trash'

				),

			'public' => true,

			'menu_position' => 65,

			'menu_icon' => 'dashicons-id',

			'supports' => array( 'title', 'editor', 'thumbnail', 'page-attributes', 'revisions'),

			'has_archive' => true,

			'rewrite' => array( 'slug' => 'about/staff', 'feeds' => false ),

			'query_var' => 'staff'

		);

		register_post_type( 'at_staff', $args);

		/* Partners */

		$args = array(

			'labels' => array(

				'name' => 'Partners',

				'singular_name' => 'Partner',

				'all_items' => 'All Partners',

				'add_new_item' => 'Add New Partner',

				'edit_item' => 'Edit Partner',

				'new_item' => 'New Partner',

				'view_item' => 'View Partner',

				'search_items' => 'Search All Partners',

				'not_found' => 'No Partners found',

				'not_found_in_trash' => 'No Partners found in Trash'

				),

			'public' => true,

			'exclude_from_search' => true,

			'menu_position' => 65,

			'menu_icon' => 'dashicons-groups',

			'supports' => array( 'title', 'editor', 'thumbnail', 'revisions'),

			'has_archive' => true,

			'rewrite' => array( 'slug' => 'partners', 'feeds' => false ),

			'query_var' => 'partner'

		);

		register_post_type('at_partners', $args);

		/* Careers */

		$args = array(

			'labels' => array(

				'name' => 'Careers',

				'singular_name' => 'Job Posting',

				'all_items' => 'All Job Postings',

				'add_new_item' => 'Add New Job Posting',

				'edit_item' => 'Edit Job Posting',

				'new_item' => 'New Job Posting',

				'view_item' => 'View Job Posting',

				'search_items' => 'Search All Job Postings',

				'not_found' => 'No Job Postings found',

				'not_found_in_trash' => 'No Job Postings found in Trash'

				),

			'public' => true,

			'menu_position' => 65,

			'menu_icon' => 'dashicons-welcome-learn-more',

			'supports' => array( 'title', 'editor', 'page-attributes', 'revisions'),

			'has_archive' => true,

			'rewrite' => array( 'slug' => 'about/careers', 'feeds' => false ),

			'query_var' => 'job'

		);

		register_post_type( 'at_careers', $args);

		/* Home Slider */

		$args = array(

			'labels' => array(

				'name' => 'Home Slides',

				'singular_name' => 'Slide',

				'all_items' => 'All Slides',

				'add_new_item' => 'Add New Slide',

				'edit_item' => 'Edit Slide',

				'new_item' => 'New Slide',

				'view_item' => 'View Slide',

				'search_items' => 'Search All Slides',

				'not_found' => 'No Slides found',

				'not_found_in_trash' => 'No Slides found in Trash'

				),

			'public' => false,

			'show_ui' => true,

			'menu_position' => 100,

			'menu_icon' => 'dashicons-images-alt2',

			'supports' => array( 'title', 'thumbnail', 'excerpt', 'page-attributes', 'revisions'),

			'query_var' => false

		);

		register_post_type( 'at_slides', $args);

	}



	/********* Taxonomies *********/

	function at_add_taxonomies(){

		/* Resource Types (hierarchical) */

		$args = array(

			'labels' => array(

				'name' => 'Types',

				'singular_name' => 'Type',

				'all_items' => 'All Types',

				'edit_item' => 'Edit Type',

				'view_item' => 'View Type',

				'update_item' => 'Update Type',

				'add_new_item' => 'Add New Type',

				'new_item_name' => 'New Type Name',

				'parent_item' => 'Parent Type',

				'parent_item_colon' => 'Parent Type:',

				'search_items' => 'Search Types'

			),

			'show_admin_column' => true,

			'hierarchical' => true,

			'query_var' => 'type',

			'rewrite' => array('slug' => 'type', 'hierarchical' => true),

			'sort' => true

		);

		register_taxonomy('at_types', 'at_resources', $args);

		/* Page Filters (hierarchical) */

		$args = array(

			'labels' => array(

				'name' => 'Page Filters',

				'singular_name' => 'Page Filter',

				'all_items' => 'All Page Filters',

				'edit_item' => 'Edit Page Filter',

				'view_item' => 'View Page Filter',

				'update_item' => 'Update Page Filter',

				'add_new_item' => 'Add New Page Filter',

				'new_item_name' => 'New Page Filter Name',

				'parent_item' => 'Parent Page Filter',

				'parent_item_colon' => 'Parent Page Filter:',

				'search_items' => 'Search Page Filters'

			),

			'show_admin_column' => true,

			'hierarchical' => true,

			'query_var' => 'page_filter',

			'rewrite' => array('slug' => 'page-filter', 'hierarchical' => true),

			'sort' => true

		);

		register_taxonomy('at_filters', array('at_resources', 'at_partners', 'at_publications'), $args);

		/* Publication Categories (hierarchical) */

		$args = array(

			'labels' => array(

				'name' => 'Publication Categories',

				'singular_name' => 'Publication Category',

				'all_items' => 'All Publication Categories',

				'edit_item' => 'Edit Publication Category',

				'view_item' => 'View Publication Category',

				'update_item' => 'Update Publication Category',

				'add_new_item' => 'Add New Publication Category',

				'new_item_name' => 'New Publication Category Name',

				'parent_item' => 'Parent Publication Category',

				'parent_item_colon' => 'Parent Publication Category:',

				'search_items' => 'Search Publication Categories'

			),

			'show_in_nav_menus' => false,

			'show_tagcloud' => false,

			'show_admin_column' => true,

			'hierarchical' => true,

			'query_var' => 'pub_cat',

			'rewrite' => array('slug' => 'pub-category', 'hierarchical' => true),

			'sort' => true

		);

		register_taxonomy('at_pub_cats', 'at_publications', $args);


		/* Reports Categories (hierarchical) */

		$args = array(

			'labels' => array(

				'name' => 'Report Categories',

				'singular_name' => 'Report Category',

				'all_items' => 'All Report Categories',

				'edit_item' => 'Edit Report Category',

				'view_item' => 'View Report Category',

				'update_item' => 'Update Report Category',

				'add_new_item' => 'Add New Report Category',

				'new_item_name' => 'New Report Category Name',

				'parent_item' => 'Parent Report Category',

				'parent_item_colon' => 'Parent Report Category:',

				'search_items' => 'Search Report Categories'

			),

			'show_in_nav_menus' => false,

			'show_tagcloud' => false,

			'show_admin_column' => true,

			'hierarchical' => true,

			'query_var' => 'rep_cat',

			'rewrite' => array('slug' => 'rep-category', 'hierarchical' => true),

			'sort' => true

		);

		register_taxonomy('at_rep_cats', 'at_reports', $args);

	}



	//* if on blog page - don't show news posts

	add_action( 'pre_get_posts', 'exclude_category' );

	function exclude_category( $query ) {

	    if ($query->query_vars['pagename'] == 'blog' && !is_admin()) {

	        $query->set( 'cat', '-123' );

	    }

	}



	/* ========================================================================================================================



	Enqueued Scripts



	======================================================================================================================== */



	/**

	 * Add scripts via wp_head()

	 *

	 * @return void

	 * @author Keir Whitaker

	 */



	function at_add_scripts() {

		wp_enqueue_style( 'critical-css', get_stylesheet_directory_uri().'/css/critical.min.css', '', '1.0', 'screen' );

		wp_enqueue_script('critical-js', get_template_directory_uri().'/js/critical.min.js', '', '1.0');

	if (is_page(7)) {

		wp_enqueue_script('maps-js', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyDerznNG3emO7uL4KQwXgPG8cFouI5y_EY&sensor=false', '', '1.0', true);

		}

		wp_enqueue_script('production-js', get_template_directory_uri().'/js/production.min.js', array('jquery', 'critical-js'), '1.0', true);

	}





	//* https://searchwp.com/docs/hooks/searchwp_big_selects/

	add_filter( 'searchwp_big_selects', '__return_true' );





	/* ========================================================================================================================



	Developer Helpers



	======================================================================================================================== */



	function dump( $var, $txt='' )

	{

	    print "<pre><b>$txt</b> = ";

	    print_r( $var );

	    print "</pre>";

	}