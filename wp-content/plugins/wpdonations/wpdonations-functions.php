<?php
if ( ! function_exists( 'get_donations' ) ) :
/**
 * Queries donations with certain criteria and returns them
 *
 * @access public
 * @return void
 */
function get_donations( $args = array() ) {
	global $wpdb;

	$args = wp_parse_args( $args, array(
		'search_location'   => '',
		'search_keywords'   => '',
		'search_categories' => array(),
		'search_campaigns'   => array(),
		'offset'            => '',
		'posts_per_page'    => '-1',
		'orderby'           => 'date',
		'order'             => 'DESC'
	) );

	$query_args = array(
		'post_type'           => 'donation',
		'post_status'         => 'publish',
		'ignore_sticky_posts' => 1,
		'offset'              => absint( $args['offset'] ),
		'posts_per_page'      => intval( $args['posts_per_page'] ),
		'orderby'             => $args['orderby'],
		'order'               => $args['order'],
		'tax_query'           => array(),
		'meta_query'          => array()
	);

	if ( ! empty( $args['search_campaigns'] ) )
		$query_args['tax_query'][] = array(
			'taxonomy' => 'donation_campaign',
			'field'    => 'slug',
			'terms'    => $args['donation_campaigns']
		);

	if ( ! empty( $args['search_categories'] ) )
		$query_args['tax_query'][] = array(
			'taxonomy' => 'donation_category',
			'field'    => 'slug',
			'terms'    => $args['search_categories']
		);

	if ( get_option( 'wpdonations_hide_anonymous_donations' ) == 1 )
		$query_args['meta_query'][] = array(
			'key'     => '_anonymous_donation',
			'value'   => '1',
			'compare' => '!='
		);

	if ( $args['search_location'] )
		$query_args['meta_query'][] = array(
			'key'     => '_donation_location',
			'value'   => $args['search_location'],
			'compare' => 'LIKE'
		);

	// Keyword search - search meta as well as post content
	if ( $args['search_keywords'] ) {
		$post_ids = $wpdb->get_col( $wpdb->prepare( "
		    SELECT DISTINCT post_id FROM {$wpdb->postmeta}
		    WHERE meta_value LIKE '%%%s%%'
		", $args['search_keywords'] ) );

		$post_ids = $post_ids + $wpdb->get_col( $wpdb->prepare( "
		    SELECT DISTINCT ID FROM {$wpdb->posts}
		    WHERE post_title LIKE '%%%s%%'
		    OR post_content LIKE '%%%s%%'
		", $args['search_keywords'], $args['search_keywords'] ) );

		$query_args['post__in'] = $post_ids + array( 0 );
	}

	$query_args = apply_filters( 'wpdonations_get_donations', $query_args );

	if ( empty( $query_args['meta_query'] ) )
		unset( $query_args['meta_query'] );

	if ( empty( $query_args['tax_query'] ) )
		unset( $query_args['tax_query'] );

	return new WP_Query( $query_args );
}
endif;

if ( ! function_exists( 'get_donation_campaigns' ) ) :
/**
 * Outputs a form to submit a new donation to the site from the frontend.
 *
 * @access public
 * @return void
 */
function get_donation_campaigns() {
	if ( ! get_option( 'wpdonations_enable_campaigns' ) )
		return array();

	return get_terms( "donation_campaign", array(
		'orderby'       => 'name',
	    'order'         => 'ASC',
	    'hide_empty'    => false,
	) );
}
endif;

if ( ! function_exists( 'get_donation_categories' ) ) :
/**
 * Outputs a form to submit a new donation to the site from the frontend.
 *
 * @access public
 * @return array
 */
function get_donation_categories() {
	if ( ! get_option( 'wpdonations_enable_categories' ) )
		return array();

	return get_terms( "donation_category", array(
		'orderby'       => 'name',
	    'order'         => 'ASC',
	    'hide_empty'    => false,
	) );
}
endif;

if ( ! function_exists( 'get_donation_rss_link' ) ) :
/**
 * Get the donation RSS link
 *
 * @return string
 */
function get_donation_rss_link( $args = array() ) {
	$rss_link = add_query_arg( array_merge( array( 'feed' => 'donation_feed' ), $args ), home_url() );

	return $rss_link;
}
endif;

if ( ! function_exists( 'wpdonations_create_account' ) ) :
/**
 * Handle account creation.
 *
 * @param  [type] $account_email
 * @return WP_error | bool was an account created?
 */
function WPdonations_create_account( $account_email ) {
	global  $current_user;

	$user_email = apply_filters( 'user_registration_email', sanitize_email( $account_email ) );

	if ( empty( $user_email ) )
		return false;

	if ( ! is_email( $user_email ) )
		return new WP_Error( 'validation-error', __( 'Your email address isn&#8217;t correct.', 'wpdonations' ) );

	if ( email_exists( $user_email ) )
		return new WP_Error( 'validation-error', __( 'This email is already registered, please choose another one.', 'wpdonations' ) );

	// Email is good to go - use it to create a user name
	$username = sanitize_user( current( explode( '@', $user_email ) ) );
	$password = wp_generate_password();

	// Ensure username is unique
	$append     = 1;
	$o_username = $username;

	while( username_exists( $username ) ) {
		$username = $o_username . $append;
		$append ++;
	}

	// Final error check
	$reg_errors = new WP_Error();
	do_action( 'register_post', $username, $user_email, $reg_errors );
	$reg_errors = apply_filters( 'registration_errors', $reg_errors, $username, $user_email );

	if ( $reg_errors->get_error_code() )
		return $reg_errors;

	// Create account
	$new_user = array(
    	'user_login' => $username,
    	'user_pass'  => $password,
    	'user_email' => $user_email
    );

    $user_id = wp_insert_user( apply_filters( 'wpdonations_create_account_data', $new_user ) );

    if ( is_wp_error( $user_id ) )
    	return $user_id;

    // Notify
    wp_new_user_notification( $user_id, $password );

	// Login
    wp_set_auth_cookie( $user_id, true, is_ssl() );
    $current_user = get_user_by( 'id', $user_id );

    return true;
}
endif;