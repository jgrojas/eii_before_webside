<?php
/**
 * Template Functions
 *
 * Template functions specifically created for donations
 *
 * @author 		Remi Corson
 * @category 	Core
 * @package 	Easy WordPress Donations 2/Template
 * @version     2.0.0
 */

/**
 * Get and include template files.
 *
 * @param mixed $template_name
 * @param array $args (default: array())
 * @param string $template_path (default: '')
 * @param string $default_path (default: '')
 * @return void
 */
function get_wpdonations_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	if ( $args && is_array($args) )
		extract( $args );

	include( locate_wpdonations_template( $template_name, $template_path, $default_path ) );
}

/**
 * Locate a template and return the path for inclusion.
 *
 * This is the load order:
 *
 *		yourtheme		/	$template_path	/	$template_name
 *		yourtheme		/	$template_name
 *		$default_path	/	$template_name
 *
 * @param mixed $template_name
 * @param string $template_path (default: '')
 * @param string $default_path (default: '')
 * @return string
 */
function locate_wpdonations_template( $template_name, $template_path = '', $default_path = '' ) {
	if ( ! $template_path )
		$template_path = 'wpdonations';
	if ( ! $default_path )
		$default_path = WPDONATIONS_PLUGIN_DIR . '/templates/';

	// Look within passed path within the theme - this is priority
	$template = locate_template(
		array(
			trailingslashit( $template_path ) . $template_name,
			$template_name
		)
	);

	// Get default template
	if ( ! $template )
		$template = $default_path . $template_name;

	// Return what we found
	return apply_filters( 'wpdonations_locate_template', $template, $template_name, $template_path );
}

/**
 * Get template part (for templates in loops).
 *
 * @param mixed $slug
 * @param string $name (default: '')
 * @return void
 */
function get_wpdonations_template_part( $slug, $name = '' ) {
	$template = '';

	// Look in yourtheme/slug-name.php and yourtheme/wpdonations/slug-name.php
	if ( $name )
		$template = locate_template( array ( "{$slug}-{$name}.php", "wpdonations/{$slug}-{$name}.php" ) );

	// Get default slug-name.php
	if ( ! $template && $name && file_exists( WPDONATIONS_PLUGIN_DIR . "/templates/{$slug}-{$name}.php" ) )
		$template = WPDONATIONS_PLUGIN_DIR . "/templates/{$slug}-{$name}.php";

	// If template file doesn't exist, look in yourtheme/slug.php and yourtheme/wpdonations/slug.php
	if ( ! $template )
		$template = locate_template( array ( "{$slug}.php", "wpdonations/{$slug}.php" ) );

	if ( $template )
		load_template( $template, false );
}

/**
 * True if an the user can post a donation. If accounts are required, and reg is enabled, users can post (they signup at the same time).
 *
 * @return bool
 */
function wpdonations_user_can_post_donation() {
	$can_post = true;

	if ( ! is_user_logged_in() ) {
		if ( wpdonations_user_requires_account() && ! wpdonations_enable_registration() ) {
			$can_post = false;
		}
	}

	return apply_filters( 'wpdonations_user_can_post_donation', $can_post );
}

/**
 * True if registration is enabled.
 *
 * @return bool
 */
function wpdonations_enable_registration() {
	return apply_filters( 'wpdonations_enable_registration', get_option( 'wpdonations_enable_registration' ) == 1 ? true : false );
}

/**
 * True if an account is required to post a donation.
 *
 * @return bool
 */
function wpdonations_user_requires_account() {
	return apply_filters( 'wpdonations_user_requires_account', get_option( 'wpdonations_user_requires_account' ) == 1 ? true : false );
}

/* ------------------------------ */
/*     THE DONATION DETAILS          */
/* ------------------------------ */

/**
 * get_the_donation_amount function.
 *
 * @access public
 * @param int $post (default: null)
 * @return void
 */
function get_the_donation_amount( $post = null ) {
	$post = get_post( $post );
	if ( $post->post_type !== 'donation' )
		return;

	return apply_filters( 'the_donation_amount', number_format( $post->_donation_amount, 2, '.', '' ), $post );
}

/**
 * Display or retrieve the current donation amount with optional content.
 *
 * @access public
 * @param mixed $id (default: null)
 * @return void
 */
function the_donation_amount( $before = '', $after = '', $echo = true, $post = null ) {
	$donation_amount = get_the_donation_amount( $post );

	if ( strlen( $donation_amount ) == 0 )
		return;

	$donation_amount = esc_attr( strip_tags( $donation_amount ) );
	$donation_amount = $before . $donation_amount . $after;

	if ( $echo )
		echo $donation_amount;
	else
		return $donation_amount;
}

/**
 * get_the_donation_formatted_amount function.
 *
 * @access public
 * @param int $post (default: null)
 * @return void
 */
function get_the_donation_formatted_amount( $post = null ) {
	$post = get_post( $post );
	if ( $post->post_type !== 'donation' )
		return;

	return apply_filters( 'the_donation_formatted_amount', get_option( 'wpdonations_currency', false ) . ' ' . number_format( $post->_donation_amount, 2, '.', '' ), $post );
}

/**
 * Display or retrieve the current donation amount with currency code with optional content.
 *
 * @access public
 * @param mixed $id (default: null)
 * @return void
 */
function the_donation_formatted_amount( $before = '', $after = '', $echo = true, $post = null ) {
	$donation_amount = get_the_donation_formatted_amount( $post );

	if ( strlen( $donation_amount ) == 0 )
		return;

	$donation_amount = esc_attr( strip_tags( $donation_amount ) );
	$donation_amount = $before . $donation_amount . $after;

	if ( $echo )
		echo $donation_amount;
	else
		return $donation_amount;
}

/**
 * Outputs the donations status
 *
 * @return void
 */
function the_donation_status( $post = null ) {
	echo get_the_donation_status( $post );
}

/**
 * Gets the donations status
 *
 * @return string
 */
function get_the_donation_status( $post = null ) {
	$post = get_post( $post );

	$status = $post->post_status;

	if ( $status == 'publish' )
		$status = __( 'Validated', 'wpdonations' );
	elseif ( $status == 'pending' )
		$status = __( 'Pending', 'wpdonations' );
	else
		$status = __( 'Pending Payment', 'wpdonations' );

	return apply_filters( 'the_donation_status', $status, $post );
}

/**
 * Return whether or not the donation has been marked as anonymous
 *
 * @param  object $post
 * @return boolean
 */
function is_anonymous_donation( $post = null ) {
	$post = get_post( $post );

	return $post->_anonymous_donation ? true : false;
}

/**
 * Return whether or not the donation is recurring
 *
 * @param  object $post
 * @return boolean
 */
function is_recurring_donation( $post = null ) {
	$post = get_post( $post );

	return ( ($post->_recurrence_period && $post->_recurrence_period != 'oneshot' ) ? true : false );
}

/**
 * the_recurrence_period function.
 *
 * @access public
 * @return void
 */
function the_recurrence_period( $post = null ) {
	echo get_the_recurrence_period( $post );
}

/**
 * get_the_recurrence_period function.
 *
 * @access public
 * @return void
 */
function get_the_recurrence_period( $post = null ) {
	
	$recurrence_period = get_post_meta( $post->ID, '_recurrence_period', true );
	
	switch ( $recurrence_period ) {
		case 'weekly' :
			$_recurrence_period = __( 'Weekly', 'wpdonations' );
		break;
		case 'monthly' :
			$_recurrence_period = __( 'Monthly', 'wpdonations' );
		break;
		case 'yearly' :
			$_recurrence_period = __( 'Yearly', 'wpdonations' );
		break;
		default:
			$_recurrence_period = '';
	}

	
	return ( $recurrence_period != '' ) ? $_recurrence_period : '';
}

/**
 * Check if payment is made online
 *
 * @param  object $post
 * @return boolean
 */
function is_online_payment( $post_id ) {
	
	$payment_type = get_post_meta( $post_id, '_donation_payment_type', true );

	return ( $payment_type == 'online' ) ? true : false;
}

/**
 * the_donation_permalink function.
 *
 * @access public
 * @return void
 */
function the_donation_permalink( $post = null ) {
	echo get_the_donation_permalink( $post );
}

/**
 * get_the_donation_permalink function.
 *
 * @access public
 * @param mixed $post (default: null)
 * @return string
 */
function get_the_donation_permalink( $post = null ) {
	$post = get_post( $post );
	$link = get_permalink( $post );

	return apply_filters( 'the_donation_permalink', $link, $post );
}


/**
 * the_donation_campaign function.
 *
 * @access public
 * @return void
 */
function the_donation_campaign( $post = null ) {
	if ( $donation_campaign = get_the_donation_campaign( $post ) )
		echo $donation_campaign->name;
}

/**
 * get_the_donation_campaign function.
 *
 * @access public
 * @param mixed $post (default: null)
 * @return void
 */
function get_the_donation_campaign( $post = null ) {
	$post = get_post( $post );
	if ( $post->post_type !== 'donation' )
		return;

	$types = wp_get_post_terms( $post->ID, 'donation_campaign' );

	if ( $types )
		$type = current( $types );
	else
		$type = false;

	return apply_filters( 'the_donation_campaign', $type, $post );
}

/* ------------------------------ */
/*     THE CAMPAIGN DETAILS        */
/* ------------------------------ */

/**
 * Display or retrieve the campaign collected funds.
 *
 * @access public
 * @param mixed $id (default: null)
 * @return void
 */
function the_campaign_funds( $before = '', $after = '', $echo = true, $campaign_id ) {
	
	// Create the Query
	$posts_per_page = -1;
	$post_type 		= 'donation';
	$post_status 	= 'publish'; 
				
	$query = new WP_Query( array ( 
								'post_type'      => $post_type,
								'posts_per_page' => $posts_per_page,
								'post_status'    => $post_status,
								'no_found_rows'  => 1,
								'tax_query' => array(
										array(
											'taxonomy' => 'donation_campaign',
											'field'    => 'id',
											'terms'    => array( $campaign_id )
										)
								),
								) 
						);
	
	//Get post type count
	$post_count = $query->post_count;
	$i = 1;
	
	$campaign_funds = '';
	
	// Displays info
	if( $post_count > 0) :
	
		// Loop
		while ($query->have_posts()) : $query->the_post();
			
			$donation_id     = get_the_ID();
			$donation_amount = get_post_meta($donation_id, '_donation_amount', true);
			
			$campaign_funds 	+= $donation_amount;
			
		endwhile;
		
		$campaign_funds = esc_attr( strip_tags( $campaign_funds ) );
		if( $campaign_funds > 0 ) {
			$campaign_funds = apply_filters( 'the_campaign_funds', number_format( $campaign_funds, 2, '.', '' ) );
			$campaign_funds = $before . $campaign_funds . $after;
		} else {
			$campaign_funds = $before . '0' . $after;
		}
	
		if ( $echo )
			echo $campaign_funds;
		else
			return $campaign_funds;
		
	endif;
}

/**
 * Display or retrieve the campaign target.
 *
 * @access public
 * @param mixed $id (default: null)
 * @return void
 */
function the_campaign_target( $before = '', $after = '', $echo = true, $campaign_id ) {

    $term_data = get_option( "wpdonations_taxonomy_$campaign_id", 'campaign_target' );
    if( is_array($term_data) ) {
    	if( isset( $term_data['campaign_target'] ) && $term_data['campaign_target'] != '') {
    		$campaign_target = apply_filters( 'the_campaign_target', number_format( $term_data['campaign_target'], 2, '.', '' ) );
    	} else {
	    	$campaign_target = __( 'None', 'wpdonations' );
		}
    } 
        
	if ( $echo )
		echo $before . $campaign_target . $after;
	else
		return $before . $campaign_target . $after;
}

/**
 * Display or retrieve the campaign raised funds percentage.
 *
 * @access public
 * @param mixed $id (default: null)
 * @return void
 */
function the_campaign_funds_raised_percentage( $before = '', $after = '', $echo = true, $campaign_id ) {

	$target = the_campaign_target( '', '', false, $campaign_id );
	$funds = the_campaign_funds( '', '', false, $campaign_id );
   
	if( !$funds > 0 OR !$target > 0 )
		return;
   
   	$percent = ($funds*100)/$target;
   	$percent = number_format( $percent, 2, '.', '' );
        
	if ( $echo )
		echo $before . $percent . $after;
	else
		return $before . $percent . $after;
}

/* ------------------------------ */
/*     GET THE GLOBAL TARGET      */
/* ------------------------------ */

/**
 * Display or retrieve the global target.
 *
 * @access public
 * @param mixed $id (default: null)
 * @return void
 */
function the_target( $before = '', $after = '', $echo = true ) {

    
	$target = get_option( 'wpdonations_general_target' );

	$target = $before . $target . $after;
    
	if ( $echo )
		echo $target;
	else
		return $target;
}

/* ------------------------------ */
/*     GET ALL COLLECTED FUNDS    */
/* ------------------------------ */

/**
 * Display or retrieve all collected funds.
 *
 * @access public
 * @param mixed $id (default: null)
 * @return void
 */
function the_funds( $before = '', $after = '', $echo = true ) {
	
	// Create the Query
	$posts_per_page = -1;
	$post_type 		= 'donation';
	$post_status 	= 'publish'; 
				
	$query = new WP_Query( array ( 
								'post_type'      => $post_type,
								'posts_per_page' => $posts_per_page,
								'post_status'    => $post_status,
								'no_found_rows'  => 1
								) 
						);
	
	//Get post type count
	$post_count = $query->post_count;
	$i = 1;
	
	$the_funds = '';
	
	// Displays info
	if( $post_count > 0) :
	
		// Loop
		while ($query->have_posts()) : $query->the_post();
			
			$donation_id     = get_the_ID();
			$donation_amount = get_post_meta( $donation_id, '_donation_amount', true );
			
			$the_funds += $donation_amount;
			
		endwhile;
		
		$the_funds = esc_attr( strip_tags( $the_funds ) );
		if( $the_funds > 0 ) {
			$the_funds = apply_filters( 'the_funds', number_format( $the_funds, 2, '.', '' ) );
			$the_funds = $before . $the_funds . $after;
		} else {
			$the_funds = $before . '0' . $after;
		}
	
		if ( $echo )
			echo $the_funds;
		else
			return $the_funds;
		
	endif;
}

/**
 * Display or retrieve the general raised funds percentage.
 *
 * @access public
 * @param mixed $id (default: null)
 * @return void
 */
function the_funds_raised_percentage( $before = '', $after = '', $echo = true ) {

	$target = the_target( '', '', false );
	$funds = the_funds( '', '', false );
   
	if( !$funds > 0 && !$target > 0 )
		return;
   
   	$percent = ($funds*100)/$target;
   	$percent = number_format( $percent, 2, '.', '' );
        
	if ( $echo )
		echo $before . $percent . $after;
	else
		return $before . $percent . $after;
}


/* ------------------------------ */
/*     THE DONOR DETAILS          */
/* ------------------------------ */

/**
 * Display or retrieve the current donor firstname with optional content.
 *
 * @access public
 * @param mixed $id (default: null)
 * @return void
 */
function the_donor_firstname( $before = '', $after = '', $echo = true, $post = null ) {
	$donor_firstname = get_the_donor_firstname( $post );

	if ( strlen( $donor_firstname ) == 0 )
		return;

	$donor_firstname = esc_attr( strip_tags( $donor_firstname ) );
	$donor_firstname = $before . $donor_firstname . $after;

	if ( $echo )
		echo $donor_firstname;
	else
		return $donor_firstname;
}

/**
 * get_the_donor_firstname function.
 *
 * @access public
 * @param int $post (default: null)
 * @return void
 */
function get_the_donor_firstname( $post = null ) {
	$post = get_post( $post );
	if ( $post->post_type !== 'donation' )
		return;

	return apply_filters( 'the_donor_firstname', $post->_donor_firstname, $post );
}

/**
 * Display or retrieve the current donor lastname with optional content.
 *
 * @access public
 * @param mixed $id (default: null)
 * @return void
 */
function the_donor_lastname( $before = '', $after = '', $echo = true, $post = null ) {
	$donor_lastname = get_the_donor_lastname( $post );

	if ( strlen( $donor_lastname ) == 0 )
		return;

	$donor_lastname = esc_attr( strip_tags( $donor_lastname ) );
	$donor_lastname = $before . $donor_lastname . $after;

	if ( $echo )
		echo $donor_lastname;
	else
		return $donor_lastname;
}

/**
 * get_the_donor_lastname function.
 *
 * @access public
 * @param int $post (default: null)
 * @return void
 */
function get_the_donor_lastname( $post = null ) {
	$post = get_post( $post );
	if ( $post->post_type !== 'donation' )
		return;

	return apply_filters( 'the_donor_lastname', $post->_donor_lastname, $post );
}

/**
 * Display or retrieve the current donor address with optional content.
 *
 * @access public
 * @param mixed $id (default: null)
 * @return void
 */
function the_donor_address( $before = '', $after = '', $echo = true, $post = null ) {
	$donor_address = get_the_donor_address( $post );

	if ( strlen( $donor_address ) == 0 )
		return;

	$donor_address = esc_attr( strip_tags( $donor_address ) );
	$donor_address = $before . $donor_address . $after;

	if ( $echo )
		echo $donor_address;
	else
		return $donor_address;
}

/**
 * get_the_donor_address function.
 *
 * @access public
 * @param int $post (default: 0)
 * @return void
 */
function get_the_donor_address( $post = null ) {
	$post = get_post( $post );

	if ( $post->post_type !== 'donation' )
		return;

	return apply_filters( 'the_donor_address', $post->_donor_address, $post );
}

/**
 * Display or retrieve the current donor zip with optional content.
 *
 * @access public
 * @param mixed $id (default: null)
 * @return void
 */
function the_donor_zip( $before = '', $after = '', $echo = true, $post = null ) {
	$donor_zip = get_the_donor_zip( $post );

	if ( strlen( $donor_zip ) == 0 )
		return;

	$donor_zip = esc_attr( strip_tags( $donor_zip ) );
	$donor_zip = $before . $donor_zip . $after;

	if ( $echo )
		echo $donor_zip;
	else
		return $donor_zip;
}

/**
 * get_the_donor_zip function.
 *
 * @access public
 * @param int $post (default: 0)
 * @return void
 */
function get_the_donor_zip( $post = null ) {
	$post = get_post( $post );

	if ( $post->post_type !== 'donation' )
		return;

	return apply_filters( 'the_donor_zip', $post->_donor_zip, $post );
}

/**
 * the_donor_town function.
 * @param  boolean $map_link whether or not to link to the map on google maps
 * @return [type]
 */
function the_donor_town( $map_link = true, $post = null ) {
	$donor_town = get_the_donor_town( $post );

	if ( $donor_town ) {
		if ( $map_link )
			echo '<a class="google_map_link" href="http://maps.google.com/maps?q=' . urlencode( $donor_town ) . '&zoom=14&size=512x512&maptype=roadmap&sensor=false">' . $donor_town . '</a>';
		else
			echo $donor_town;
	} else {
		echo apply_filters( 'the_donor_town_anywhere_text', '&ndash;' );
	}
}

/**
 * get_the_donor_town function.
 *
 * @access public
 * @param mixed $post (default: null)
 * @return void
 */
function get_the_donor_town( $post = null ) {
	$post = get_post( $post );
	if ( $post->post_type !== 'donation' )
		return;

	return apply_filters( 'the_donor_town', $post->_donor_town, $post );
}

/**
 * get_the_donor_website function.
 *
 * @access public
 * @param int $post (default: null)
 * @return void
 */
function get_the_donor_website( $post = null ) {
	$post = get_post( $post );
	if ( $post->post_type !== 'donation' )
		return;

	return apply_filters( 'the_donor_website', $post->_donor_website, $post );
}

/**
 * the_donor_logo function.
 *
 * @access public
 * @param string $size (default: 'thumbnail')
 * @param mixed $default (default: null)
 * @return void
 */
function the_donor_logo( $size = 'thumbnail', $default = null, $post = null ) {
	global $wpdonations;

	$logo = get_the_donor_logo( $post );
	if ( $logo )
		echo '<img src="' . $logo . '" alt="Logo" />';
	elseif ( $default )
		echo '<img src="' . $default . '" alt="Logo" />';
	else
		echo '<img src="' . WPDONATIONS_PLUGIN_URL . '/assets/images/donor.png' . '" alt="Logo" />';
}

/**
 * get_the_donor_logo function.
 *
 * @access public
 * @param mixed $post (default: null)
 * @return string
 */
function get_the_donor_logo( $post = null ) {
	$post = get_post( $post );
	if ( $post->post_type !== 'donation' )
		return;

	return apply_filters( 'the_donor_logo', $post->_donor_logo, $post );
}



/**
 * donation_class function.
 *
 * @access public
 * @param string $class (default: '')
 * @param mixed $post_id (default: null)
 * @return void
 */
function donation_class( $class = '', $post_id = null ) {
	// Separates classes with a single space, collates classes for post DIV
	echo 'class="' . join( ' ', get_donation_class( $class, $post_id ) ) . '"';
}

/**
 * get_donation_class function.
 *
 * @access public
 * @return array
 */
function get_donation_class( $class = '', $post_id = null ) {
	$post = get_post( $post_id );
	if ( $post->post_type !== 'donation' )
		return array();

	$classes = array();

	if ( empty( $post ) )
		return $classes;

	$classes[] = 'donation';
	if( get_the_donation_campaign() != '')
		$classes[] = 'donation-campaign-' . sanitize_title( get_the_donation_campaign()->name );

	if ( is_anonymous_donation( $post ) )
		$classes[] = 'anonymous_donation';

	return get_post_class( $classes, $post->ID );
}
