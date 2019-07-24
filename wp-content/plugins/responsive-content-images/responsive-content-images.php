<?php
/**
* Plugin Name: Responsive Content Images
* Description: Automatically chooses the best image based on the user device, bandwidth, etc. 
* Version: 1.0
* Author: Abel Thomas
* Author URI: http://abelthomas.com
*/
 
// pulls alt attribute for img
function at_get_img_alt( $image ) {
	$img_alt = trim( strip_tags( get_post_meta( $image, '_wp_attachment_image_alt', true ) ) );
	return $img_alt;
}

// gets all sizes of the image
function at_get_picture_srcs( $image, $mappings, $sizes ) {
	$arr = array();
	
	foreach ( $mappings as $size => $type ) {
		$image_src = wp_get_attachment_image_src( $image, $type );
		
		if ($image_src){
			if ($sizes)
				$arr[] = $image_src[0] .' '. $image_src[1] .'w';   
			else 
				$arr[] = '<source srcset="'. $image_src[0] . '" media="(min-width: '. $size .'px)">';    
		}
		
	} // end foreach
	
	$arr = array_reverse($arr); 
	
	if ($sizes)
		return implode(', ', $arr); 
	
	return implode( $arr );
}

// shortcode can be implemented for any image on a post or page
add_shortcode( 'responsive_img', 'at_responsive_img_shortcode' );
function at_responsive_img_shortcode( $atts ) {
	extract( shortcode_atts( array(
		'imageid'    => 1,
		// You can add more sizes for your shortcodes here
		'size1' => 0,
		'size2' => 481,
		'size3' => 961,
		'size4' => 1441, 
		'sizes' => ''
	), $atts ) );
	
	$mappings = array(
	$size1 => 'medium',
	$size2 => 'large',
	$size3 => 'x-large', 
	$size4 => 'full'
	);
	
	if (get_permalink($imageid)){
		$fallback = wp_get_attachment_image_src($imageid, 'medium');
		
		if ($sizes) { 
			
			if ($sizes == 'column')
				$breakpoints = '(max-width: 48em) 100vw, (max-width: 75em) 33.333vw, 525px';
			elseif ($sizes == 'column-fw')
				$breakpoints = '(max-width: 48em) 100vw, 33.333vw';
			elseif ($sizes == 'sidebar')
					$breakpoints = '(max-width: 48em) 100vw, (max-width: 75em) 37vw, 532px'; 
			else 
				$breakpoints = $sizes; 
				
			$img = "<img sizes='". $breakpoints ."' srcset='"; 
			
		} else { 
			$img = "<picture>\n\t"; 
			$img .= "<!--[if IE 9]><video style='display: none;'><![endif]-->\n"; 
		}
		
		$img .= at_get_picture_srcs( $imageid, $mappings, $sizes); 
		
		if ($sizes){ 
			$img .= "' "; // end srcset attr
		} else { 
			$img .= "<!--[if IE 9]></video><![endif]-->\n"; 
			$img .= "<img srcset='". $fallback[0] ."' ";  
		}
			
		$img .= "alt='". at_get_img_alt( $imageid ) ."' />\n"; 
		
		if ($sizes)
			return $img; 
		
		$img .= "</picture>\n";
		return $img; 
	} // end image display
	
	return false; 
}

// automatically creates shortcode when uploading image via media uploader
function at_responsive_insert_image($html, $id, $caption, $title, $align, $url) {
	
	if ($caption) : $figcaption = '<figcaption>'.$caption.'</figcaption>'; else : $figcaption = ''; endif; 
	
	if ($align !== 'none') $class = 'class="align'.$align.'"'; else $class = ''; 
	if ( ($align == 'left') || ($align == 'right') ) $sizes = 'sidebar'; else $sizes = ''; 
	
	if ($url) : $link_before = '<a class="max" href="'.$url.'">'; $link_after = '</a>'; else : $link_before = ''; $link_after = ''; endif; 
	
  return 
  	'<figure '.$class.'>'.$link_before.'[responsive_img imageid="'.$id.'" size1="0" size2="481" size3="961" size4="1441" sizes="'.$sizes.'"]'.$link_after.$figcaption.'</figure>';
}
add_filter('image_send_to_editor', 'at_responsive_insert_image', 10, 9);