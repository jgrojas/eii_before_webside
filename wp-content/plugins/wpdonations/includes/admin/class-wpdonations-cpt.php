<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WPdonations_CPT class.
 */
class WPdonations_CPT {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		add_filter( 'enter_title_here', array( $this, 'enter_title_here' ), 1, 2 );
		add_filter( 'manage_edit-donation_columns', array( $this, 'columns' ) );
		add_action( 'manage_donation_posts_custom_column', array( $this, 'custom_columns' ), 2 );
		add_filter( 'post_updated_messages', array( $this, 'post_updated_messages' ) );
		add_action( 'admin_footer-edit.php', array( $this, 'add_bulk_actions' ) );
		add_action( 'load-edit.php', array( $this, 'do_bulk_actions' ) );
		add_action( 'admin_init', array( $this, 'approve_donation' ) );
		add_action( 'admin_init', array( $this, 'approve_payment' ) );
		add_action( 'admin_notices', array( $this, 'approved_notice' ) );

		if ( get_option( 'wpdonations_enable_categories' ) )
			add_action( "restrict_manage_posts", array( $this, "donations_by_category" ) );
			
		if ( get_option( 'wpdonations_enable_campaigns' ) )
			add_action( "restrict_manage_posts", array( $this, "donations_by_campaign" ) );

		foreach ( array( 'post', 'post-new' ) as $hook )
			add_action( "admin_footer-{$hook}.php", array( $this,'extend_submitdiv_post_status' ) );
	}

	/**
	 * Edit bulk actions
	 */
	public function add_bulk_actions() {
		global $post_type;

		if ( $post_type == 'donation' ) {
			?>
			<script type="text/javascript">
		      jQuery(document).ready(function() {
		        jQuery('<option>').val('approve_payments').text('<?php _e( 'Approve payment', 'wpdonations' )?>').appendTo("select[name='action']");
		      });
		    </script>
		    <?php
		}
	}

	/**
	 * Do custom bulk actions
	 */
	public function do_bulk_actions() {
		$wp_list_table = _get_list_table( 'WP_Posts_List_Table' );
		$action        = $wp_list_table->current_action();

		switch( $action ) {
			case 'approve_payments' :
				check_admin_referer( 'bulk-posts' );

				$post_ids      = array_map( 'absint', array_filter( (array) $_GET['post'] ) );
				$approved_payments = array();

				if ( ! empty( $post_ids ) )
					foreach( $post_ids as $post_id ) {
						$donation_data = array(
							'ID'          => $post_id,
							'post_status' => 'publish'
						);
						if ( ( get_post_status( $post_id ) == 'pending_payment' OR get_post_status( $post_id ) == 'pending_off_payment' ) && wp_update_post( $donation_data ) )
							$approved_payments[] = $post_id;
					}

				wp_redirect( remove_query_arg( 'approve_payments', add_query_arg( 'approved_payments', $approved_payments, admin_url( 'edit.php?post_type=donation' ) ) ) );
				exit;
			break;
		}

		return;
	}

	/**
	 * Approve a single donation
	 */
	public function approve_donation() {
		if ( ! empty( $_GET['approve_donation'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'approve_donation' ) && current_user_can( 'edit_post', $_GET['approve_donation'] ) ) {
			$post_id = absint( $_GET['approve_donation'] );
			$donation_data = array(
				'ID'          => $post_id,
				'post_status' => 'publish'
			);
			wp_update_post( $donation_data );
			wp_redirect( remove_query_arg( 'approve_donation', add_query_arg( 'approved_donations', $post_id, admin_url( 'edit.php?post_type=donation' ) ) ) );
			exit;
		}
	}
	
	/**
	 * Approve a single payment
	 */
	public function approve_payment() {
		if ( ! empty( $_GET['approve_payment'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'approve_payment' ) && current_user_can( 'edit_post', $_GET['approve_payment'] ) ) {
			$post_id = absint( $_GET['approve_payment'] );
			$donation_data = array(
				'ID'          => $post_id,
				'post_status' => 'publish'
			);
			wp_update_post( $donation_data );
			wp_redirect( remove_query_arg( 'approve_payment', add_query_arg( 'approved_payments', $post_id, admin_url( 'edit.php?post_type=donation' ) ) ) );
			exit;
		}
	}

	/**
	 * Show a notice if we did a bulk action or approval
	 */
	public function approved_notice() {
		 global $post_type, $pagenow;
		
		if ( $pagenow == 'edit.php' && $post_type == 'donation' && ! empty( $_REQUEST['approved_payments'] ) ) {
			$approved_payments = $_REQUEST['approved_payments'];
			if ( is_array( $approved_payments ) ) {
				$approved_payments = array_map( 'absint', $approved_payments );
				$titles = array();
				foreach ( $approved_payments as $donation_id )
					$titles[] = get_the_title( $donation_id );
				echo '<div class="updated"><p>' . sprintf( __( '%s payment(s) approved', 'wpdonations' ), '&quot;' . implode( '&quot;, &quot;', $titles ) . '&quot;' ) . '</p></div>';
			} else {
				echo '<div class="updated"><p>' . sprintf( __( '%s payment approved', 'wpdonations' ), '&quot;' . get_the_title( $approved_payments ) . '&quot;' ) . '</p></div>';
			}
		}
		
	}
	
	

	/**
	 * donations_by_category function.
	 *
	 * @access public
	 * @param int $show_counts (default: 1)
	 * @param int $hierarchical (default: 1)
	 * @param int $show_uncategorized (default: 1)
	 * @param string $orderby (default: '')
	 * @return void
	 */
	public function donations_by_category( $show_counts = 1, $hierarchical = 1, $show_uncategorized = 1, $orderby = '' ) {
		global $typenow, $wp_query;

	    if ( $typenow != 'donation' || ! taxonomy_exists( 'donation_category' ) )
	    	return;

		include_once( 'class-wpdonations-category-walker.php' );

		$r = array();
		$r['pad_counts'] 	= 1;
		$r['hierarchical'] 	= $hierarchical;
		$r['hide_empty'] 	= 1;
		$r['show_count'] 	= $show_counts;
		$r['selected'] 		= ( isset( $wp_query->query['donation_category'] ) ) ? $wp_query->query['donation_category'] : '';

		$r['menu_order'] = false;

		if ( $orderby == 'order' )
			$r['menu_order'] = 'asc';
		elseif ( $orderby )
			$r['orderby'] = $orderby;

		$terms = get_terms( 'donation_category', $r );

		if ( ! $terms )
			return;

		$output  = "<select name='donation_category' id='dropdown_donation_category'>";
		$output .= '<option value="" ' .  selected( isset( $_GET['donation_category'] ) ? $_GET['donation_category'] : '', '', false ) . '>'.__( 'Select a category', "wpdonations" ).'</option>';
		$output .= $this->walk_category_dropdown_tree( $terms, 0, $r );
		$output .="</select>";

		echo $output;
	}

	/**
	 * Walk the Product Categories.
	 *
	 * @access public
	 * @return void
	 */
	private function walk_category_dropdown_tree() {
		$args = func_get_args();

		// the user's options are the third parameter
		if ( empty($args[2]['walker']) || !is_a($args[2]['walker'], 'Walker') )
			$walker = new WPdonations_Category_Walker;
		else
			$walker = $args[2]['walker'];

		return call_user_func_array( array( $walker, 'walk' ), $args );
	}
	
	/**
	 * donations_by_campaign function.
	 *
	 * @access public
	 * @param int $show_counts (default: 1)
	 * @param int $hierarchical (default: 1)
	 * @param int $show_uncategorized (default: 1)
	 * @param string $orderby (default: '')
	 * @return void
	 */
	public function donations_by_campaign( $show_counts = 1, $hierarchical = 1, $show_uncategorized = 1, $orderby = '' ) {
		global $typenow, $wp_query;

	    if ( $typenow != 'donation' || ! taxonomy_exists( 'donation_campaign' ) )
	    	return;

		include_once( 'class-wpdonations-category-walker.php' );

		$r = array();
		$r['pad_counts'] 	= 1;
		$r['hierarchical'] 	= $hierarchical;
		$r['hide_empty'] 	= 1;
		$r['show_count'] 	= $show_counts;
		$r['selected'] 		= ( isset( $wp_query->query['donation_campaign'] ) ) ? $wp_query->query['donation_campaign'] : '';

		$r['menu_order'] = false;

		if ( $orderby == 'order' )
			$r['menu_order'] = 'asc';
		elseif ( $orderby )
			$r['orderby'] = $orderby;

		$terms = get_terms( 'donation_campaign', $r );

		if ( ! $terms )
			return;

		$output  = "<select name='donation_campaign' id='dropdown_donation_campaign'>";
		$output .= '<option value="" ' .  selected( isset( $_GET['donation_campaign'] ) ? $_GET['donation_campaign'] : '', '', false ) . '>'.__( 'Select a campaign', "wpdonations" ).'</option>';
		$output .= $this->walk_category_dropdown_tree( $terms, 0, $r );
		$output .="</select>";

		echo $output;
	}

	/**
	 * enter_title_here function.
	 *
	 * @access public
	 * @return void
	 */
	public function enter_title_here( $text, $post ) {
		if ( $post->post_type == 'donation' )
			return __( 'Enter Donation Title Here', "wpdonations" );
		return $text;
	}

	/**
	 * post_updated_messages function.
	 *
	 * @access public
	 * @param mixed $messages
	 * @return void
	 */
	public function post_updated_messages( $messages ) {
		global $post, $post_ID;

		$messages['donation'] = array(
			0 => '',
			1 => sprintf( __( 'Donation updated. <a href="%s">View donation</a>', "wpdonations" ), esc_url( get_permalink( $post_ID ) ) ),
			2 => __( 'Custom field updated.', "wpdonations" ),
			3 => __( 'Custom field deleted.', "wpdonations" ),
			4 => __( 'Donation updated.', "wpdonations" ),
			5 => isset( $_GET['revision'] ) ? sprintf( __( 'Donation restored to revision from %s', "wpdonations" ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => sprintf( __( 'Donation published. <a href="%s">View donation</a>', "wpdonations" ), esc_url( get_permalink( $post_ID ) ) ),
			7 => __('Donation saved.', "wpdonations"),
			8 => sprintf( __( 'Donation submitted. <a target="_blank" href="%s">Preview donation</a>', "wpdonations" ), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
			9 => sprintf( __( 'Donation scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Donation</a>', "wpdonations" ),
			  date_i18n( __( 'M j, Y @ G:i', "wpdonations" ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) ) ),
			10 => sprintf( __( 'Donation draft updated. <a target="_blank" href="%s">Preview Donation</a>', "wpdonations" ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
		);

		return $messages;
	}

	/**
	 * columns function.
	 *
	 * @access public
	 * @param mixed $columns
	 * @return void
	 */
	public function columns( $columns ) {
		$columns = array();

		$columns["cb"]               = "<input type = \"checkbox\" />";
		$columns["donation_title"]   = __( "Title", "wpdonations" );
		if ( get_option( 'wpdonations_enable_campaigns' ) )
			$columns["donation_campaign"] = __( "Campaign", "wpdonations" );
		$columns["donor"]            = __( "Donor", "wpdonations" );
		$columns["donation_posted"]  = __( "Date", "wpdonations" );
		$columns["donation_amount"]  = __( "Amount", "wpdonations" );
		$columns['recurring']        = '<span class="tips recurring" data-tip=" ' . __( "is recurring?", "wpdonations" ) . ' "></span>';
		$columns['anonymous']        = '<span class="tips anonymous" data-tip=" ' . __( "is anonymous?", "wpdonations" ) . ' "></span>';
		$columns['status']           = '<span class="tips status" data-tip=" ' . __( "Donation Status", "wpdonations" ) . ' "></span>';
		$columns["donation_actions"] = __( "Actions", "wpdonations" );

		return $columns;
	}

	/**
	 * custom_columns function.
	 *
	 * @access public
	 * @param mixed $column
	 * @return void
	 */
	public function custom_columns( $column ) {
		global $post, $wpdonations;

		switch ( $column ) {
			case "donation_campaign" :
				$campaign = get_the_donation_campaign( $post );
				if( $campaign )
					echo '<a href="edit-tags.php?action=edit&taxonomy=donation_campaign&tag_ID=' . $campaign->term_id . '&post_type=donation">' . $campaign->name . '</a>';
			break;
			case "donation_title" :
				edit_post_link( $post->post_title, '<strong>', '</strong>', $post->ID );
			break;
			case "donor" :
				the_donor_logo();
				echo '<strong><a href="' . get_edit_user_link( $post->post_author ) . '">' . get_the_donor_firstname() . ' ' . get_the_donor_lastname() . '</a></strong>';
			break;
			case "status" :
				echo '<span class="donation-status ' . strtolower( get_the_donation_status( $post ) ) . '">' . get_the_donation_status( $post ) . '</span>';
			break;
			case "anonymous" :
				if ( is_anonymous_donation( $post ) ) { echo '<span class="tips _sortable_columns is_anonymous" data-tip=" ' . __( "This donation is anonymous", "wpdonations" ) . ' "></span>';  } else  { echo '&ndash;'; }
			break;
			case "recurring" :
				if ( is_recurring_donation( $post ) && get_the_recurrence_period( $post ) != '' ) { echo get_the_recurrence_period( $post ); } else { echo '&ndash;'; }
			break;
			case "donation_posted" :
				echo date_i18n( get_option( 'date_format' ), strtotime( $post->post_date ) ) . '<span> ';
			break;
			case "donation_amount" :
				if ( $post->_donation_amount ) {
					the_donation_formatted_amount( '<strong>', '</strong>' );
				} else {
					echo '&ndash;';
				}
			break;
			case "donation_actions" :
				$admin_actions           = array();
				if ( $post->post_status == 'pending' ) {
					$admin_actions['approve']   = array(
						'action'  => 'approve',
						'name'    => __( 'Approve', 'wpdonations' ),
						'url'     =>  wp_nonce_url( add_query_arg( 'approve_donation', $post->ID ), 'approve_donation' )
					);
				}
				if ( $post->post_status == 'pending_payment' || $post->post_status == 'pending_off_payment' ) {
					$admin_actions['approve_payment']   = array(
						'action'  => 'approve_payment',
						'name'    => __( 'Approve Payment', 'wpdonations' ),
						'url'     =>  wp_nonce_url( add_query_arg( 'approve_payment', $post->ID ), 'approve_payment' )
					);
				}
				if ( $post->post_status !== 'trash' ) {
					$admin_actions['view']   = array(
						'action'  => 'view',
						'name'    => __( 'View', 'wpdonations' ),
						'url'     => get_permalink( $post->ID )
					);
					$admin_actions['edit']   = array(
						'action'  => 'edit',
						'name'    => __( 'Edit', 'wpdonations' ),
						'url'     => get_edit_post_link( $post->ID )
					);
					$admin_actions['delete'] = array(
						'action'  => 'delete',
						'name'    => __( 'Delete', 'wpdonations' ),
						'url'     => get_delete_post_link( $post->ID )
					);
				}

				$admin_actions = apply_filters( 'wpdonations_admin_actions', $admin_actions, $post );

				foreach ( $admin_actions as $action ) {
					printf( '<a class="button tips ' . $action['action'] . '" href="%s" data-tip="%s"></a>', esc_url( $action['url'] ), esc_attr( $action['name'] ), esc_attr( $action['name'] ) );
				}

			break;
		}
	}

    /**
	 * Adds post status to the "submitdiv" Meta Box and post type WP List Table screens. Based on https://gist.github.com/franz-josef-kaiser/2930190
	 *
	 * @return void
	 */
	public function extend_submitdiv_post_status() {
		global $wp_post_statuses, $post, $post_type;

		// Abort if we're on the wrong post type, but only if we got a restriction
		if ( 'donation' !== $post_type ) {
			return;
		}

		// Get all non-builtin post status and add them as <option>
		$options = $display = '';
		foreach ( $wp_post_statuses as $status )
		{
			if ( ! $status->_builtin ) {
				// Match against the current posts status
				$selected = selected( $post->post_status, $status->name, false );

				// If we one of our custom post status is selected, remember it
				$selected AND $display = $status->label;

				// Build the options
				$options .= "<option{$selected} value='{$status->name}'>{$status->label}</option>";
			}
		}
		?>
		<script type="text/javascript">
			jQuery( document ).ready( function($)
			{
				<?php
				// Add the selected post status label to the "Status: [Name] (Edit)"
				if ( ! empty( $display ) ) :
				?>
					$( '#post-status-display' ).html( '<?php echo $display; ?>' )
				<?php
				endif;

				// Add the options to the <select> element
				?>
				var select = $( '#post-status-select' ).find( 'select' );
				$( select ).append( "<?php echo $options; ?>" );
			} );
		</script>
		<?php
	}
}

new WPdonations_CPT();