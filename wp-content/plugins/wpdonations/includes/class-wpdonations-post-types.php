<?php
/**
 * WPdonations_Content class.
 */
class WPdonations_Post_Types {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_post_types' ) );
		add_filter( 'the_content', array( $this, 'donation_content' ) );

		add_filter( 'the_donation_message', 'wptexturize'        );
		add_filter( 'the_donation_message', 'convert_smilies'    );
		add_filter( 'the_donation_message', 'convert_chars'      );
		add_filter( 'the_donation_message', 'wpautop'            );
		add_filter( 'the_donation_message', 'shortcode_unautop'  );
		add_filter( 'the_donation_message', 'prepend_attachment' );
	}

	/**
	 * register_post_types function.
	 *
	 * @access public
	 * @return void
	 */
	public function register_post_types() {

		if ( post_type_exists( "donation" ) )
			return;

		$admin_capability = 'manage_donations';

		/**
		 * Taxonomies
		 */
		
		// Donations Category
		if ( get_option( 'wpdonations_enable_categories' ) ) {
			$singular  = __( 'Category', 'wpdonations' );
			$plural    = __( 'Categories', 'wpdonations' );

			if ( current_theme_supports( 'wpdonations-templates' ) ) {
				$rewrite     = array(
					'slug'         => _x( 'donation-category', 'Donation category slug - resave permalinks after changing this', 'wpdonations' ),
					'with_front'   => false,
					'hierarchical' => false
				);
			} else {
				$rewrite = false;
			}

			register_taxonomy( "donation_category",
		        array( "donation" ),
		        array(
		            'hierarchical' 			=> true,
		            'update_count_callback' => '_update_post_term_count',
		            'label' 				=> $plural,
		            'labels' => array(
	                    'name' 				=> $plural,
	                    'singular_name' 	=> $singular,
	                    'search_items' 		=> sprintf( __( 'Search %s', 'wpdonations' ), $plural ),
	                    'all_items' 		=> sprintf( __( 'All %s', 'wpdonations' ), $plural ),
	                    'parent_item' 		=> sprintf( __( 'Parent %s', 'wpdonations' ), $singular ),
	                    'parent_item_colon' => sprintf( __( 'Parent %s:', 'wpdonations' ), $singular ),
	                    'edit_item' 		=> sprintf( __( 'Edit %s', 'wpdonations' ), $singular ),
	                    'update_item' 		=> sprintf( __( 'Update %s', 'wpdonations' ), $singular ),
	                    'add_new_item' 		=> sprintf( __( 'Add New %s', 'wpdonations' ), $singular ),
	                    'new_item_name' 	=> sprintf( __( 'New %s Name', 'wpdonations' ),  $singular )
	            	),
		            'show_ui' 				=> true,
		            'query_var' 			=> true,
		            'capabilities'			=> array(
		            	'manage_terms' 		=> $admin_capability,
		            	'edit_terms' 		=> $admin_capability,
		            	'delete_terms' 		=> $admin_capability,
		            	'assign_terms' 		=> $admin_capability,
		            ),
		            'rewrite' 				=> $rewrite,
		        )
		    );
		}

		// Donations Campaign
	    if ( get_option( 'wpdonations_enable_campaigns' ) ) {
		    $singular  = __( 'Campaign', 'wpdonations' );
			$plural    = __( 'Campaigns', 'wpdonations' );
	
			if ( current_theme_supports( 'wpdonations-templates' ) ) {
				$rewrite     = array(
					'slug'         => _x( 'donation-campaign', 'Donation type slug - resave permalinks after changing this', 'wpdonations' ),
					'with_front'   => false,
					'hierarchical' => false
				);
			} else {
				$rewrite = false;
			}
	
			register_taxonomy( "donation_campaign",
		        array( "donation" ),
		        array(
		            'hierarchical' 			=> true,
		            'label' 				=> $plural,
		            'labels' => array(
	                    'name' 				=> $plural,
	                    'singular_name' 	=> $singular,
	                    'search_items' 		=> sprintf( __( 'Search %s', 'wpdonations' ), $plural ),
	                    'all_items' 		=> sprintf( __( 'All %s', 'wpdonations' ), $plural ),
	                    'parent_item' 		=> sprintf( __( 'Parent %s', 'wpdonations' ), $singular ),
	                    'parent_item_colon' => sprintf( __( 'Parent %s:', 'wpdonations' ), $singular ),
	                    'edit_item' 		=> sprintf( __( 'Edit %s', 'wpdonations' ), $singular ),
	                    'update_item' 		=> sprintf( __( 'Update %s', 'wpdonations' ), $singular ),
	                    'add_new_item' 		=> sprintf( __( 'Add New %s', 'wpdonations' ), $singular ),
	                    'new_item_name' 	=> sprintf( __( 'New %s Name', 'wpdonations' ),  $singular )
	            	),
		            'show_ui' 				=> true,
		            'query_var' 			=> true,
		            'capabilities'			=> array(
		            	'manage_terms' 		=> $admin_capability,
		            	'edit_terms' 		=> $admin_capability,
		            	'delete_terms' 		=> $admin_capability,
		            	'assign_terms' 		=> $admin_capability,
		            ),
		           'rewrite' 				=> $rewrite,
		        )
		    );
		}

	    /**
		 * Post types
		 */
		$singular  = __( 'Donation', 'wpdonations' );
		$plural    = __( 'Donations', 'wpdonations' );

		if ( current_theme_supports( 'wpdonations-templates' ) ) {
			$has_archive = _x( 'donations', 'Post type archive slug - resave permalinks after changing this', 'wpdonations' );
		} else {
			$has_archive = false;
		}

		$rewrite     = array(
			'slug'       => _x( 'donation', 'Donation permalink - resave permalinks after changing this', 'wpdonations' ),
			'with_front' => false,
			'feeds'      => true,
			'pages'      => false
		);

		register_post_type( "donation",
			apply_filters( "register_post_type_donation", array(
				'labels' => array(
					'name' 					=> $plural,
					'singular_name' 		=> $singular,
					'menu_name'             => $plural,
					'all_items'             => sprintf( __( 'All %s', 'wpdonations' ), $plural ),
					'add_new' 				=> __( 'Add New', 'wpdonations' ),
					'add_new_item' 			=> sprintf( __( 'Add %s', 'wpdonations' ), $singular ),
					'edit' 					=> __( 'Edit', 'wpdonations' ),
					'edit_item' 			=> sprintf( __( 'Edit %s', 'wpdonations' ), $singular ),
					'new_item' 				=> sprintf( __( 'New %s', 'wpdonations' ), $singular ),
					'view' 					=> sprintf( __( 'View %s', 'wpdonations' ), $singular ),
					'view_item' 			=> sprintf( __( 'View %s', 'wpdonations' ), $singular ),
					'search_items' 			=> sprintf( __( 'Search %s', 'wpdonations' ), $plural ),
					'not_found' 			=> sprintf( __( 'No %s found', 'wpdonations' ), $plural ),
					'not_found_in_trash' 	=> sprintf( __( 'No %s found in trash', 'wpdonations' ), $plural ),
					'parent' 				=> sprintf( __( 'Parent %s', 'wpdonations' ), $singular )
				),
				'description' => __( 'This is where you can create and manage donations.', 'wpdonations' ),
				'public' 				=> true,
				'show_ui' 				=> true,
				'capability_type' 		=> 'post',
				'capabilities' => array(
					'publish_posts' 		=> $admin_capability,
					'edit_posts' 			=> $admin_capability,
					'edit_others_posts' 	=> $admin_capability,
					'delete_posts' 			=> $admin_capability,
					'delete_others_posts'	=> $admin_capability,
					'read_private_posts'	=> $admin_capability,
					'edit_post' 			=> $admin_capability,
					'delete_post' 			=> $admin_capability,
					'read_post' 			=> $admin_capability
				),
				'publicly_queryable' 	=> true,
				'exclude_from_search' 	=> false,
				'hierarchical' 			=> false,
				'rewrite' 				=> $rewrite,
				'query_var' 			=> true,
				'supports' 				=> array( 'title', 'editor', 'custom-fields' ),
				'has_archive' 			=> $has_archive,
				'show_in_nav_menus' 	=> false
			) )
		);

		/**
		 * Feeds
		 */
		add_feed( 'donation_feed', array( $this, 'donation_feed' ) );

	}

	/**
	 * Add extra content when showing donation content
	 */
	public function donation_content( $content ) {
		global $post, $wpdonations;

		if ( ! is_singular( 'donation' ) )
			return $content;

		remove_filter( 'the_content', array( $this, 'donation_content' ) );

		if ( $post->post_type == 'donation' ) {
			ob_start();

			get_wpdonations_template_part( 'content-single', 'donation' );

			$content = ob_get_clean();
		}

		add_filter( 'the_content', array( $this, 'donation_content' ) );

		return $content;
	}

	/**
	 * donations feeds
	 */
	public function donation_feed() {
		$args = array(
			'post_type'           => 'donation',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
			'posts_per_page'      => 10,
			's'                   => isset( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '',
			'meta_query'          => array(),
			'tax_query'           => array()
		);

		if ( ! empty( $_GET['location'] ) ) {
			$args['meta_query'][] = array(
				'key'     => '_donation_location',
				'value'   => sanitize_text_field( $_GET['location'] ),
				'compare' => 'LIKE'
			);
		}

		if ( ! empty( $_GET['type'] ) ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'donation_campaign',
				'field'    => 'slug',
				'terms'    => explode( ',', sanitize_text_field( $_GET['type'] ) ) + array( 0 )
			);
		}

		if ( ! empty( $_GET['donation_categories'] ) ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'donation_category',
				'field'    => 'slug',
				'terms'    => explode( ',', sanitize_text_field( $_GET['donation_categories'] ) ) + array( 0 )
			);
		}

		query_posts( apply_filters( 'donation_feed_args', $args ) );

		do_feed_rss2( false );
	}

}