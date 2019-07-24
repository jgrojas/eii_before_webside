<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WPdonations_Shortcodes class.
 */
class WPdonations_Shortcodes {

	private $donation_dashboard_message = '';

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		add_action( 'wp', array( $this, 'shortcode_action_handler' ) );

		add_shortcode( 'submit_donation_form', 	array( $this, 'submit_donation_form' ) );
		add_shortcode( 'donation_dashboard', 	array( $this, 'donation_dashboard' ) );
		add_shortcode( 'donations', 			array( $this, 'output_donations' ) );
		add_shortcode( 'donation', 				array( $this, 'output_donation' ) );
		add_shortcode( 'donation_summary', 		array( $this, 'output_donation_summary' ) );
		add_shortcode( 'the_funds', 			array( $this, 'output_donation_funds' ) );
		add_shortcode( 'the_campaign_funds', 	array( $this, 'output_campaign_funds' ) );
		add_shortcode( 'the_donation_target', 	array( $this, 'output_donation_target' ) );
		add_shortcode( 'the_donation_progress_bar',	array( $this, 'output_progress_bar' ) );
		add_shortcode( 'the_funds_raised_percentage', 	array( $this, 'output_funds_raised_percentage' ) );
		add_shortcode( 'the_campaign_funds_raised_percentage', 	array( $this, 'output_campaign_funds_raised_percentage' ) );
		add_shortcode( 'the_campaign_target', 	array( $this, 'output_campaign_target' ) );
	}

	/**
	 * Handle actions which need to be run before the shortcode e.g. post actions
	 */
	public function shortcode_action_handler() {
		global $post;

		if ( is_page() && strstr( $post->post_content, '[donation_dashboard' ) ) {
			$this->donation_dashboard_handler();
		}
	}

	/**
	 * Show the donation submission form
	 */
	public function submit_donation_form() {
		
		wp_enqueue_script( 'wpdonations-donation-submission-form' );
		
		return $GLOBALS['wpdonations']->forms->get_form( 'submit-donation' );
	}

	/**
	 * Handles actions on donation dashboard
	 */
	public function donation_dashboard_handler() {
		if ( ! empty( $_REQUEST['action'] ) && ! empty( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'wpdonations_my_donation_actions' ) ) {

			$action      = sanitize_title( $_REQUEST['action'] );
			$donation_id = absint( $_REQUEST['donation_id'] );

			try {
				// Get donation
				$donation    = get_post( $donation_id );

				// Check ownership
				if ( $donation->post_author != get_current_user_id() )
					throw new Exception( __( 'Invalid donation ID', 'wpdonations' ) );

				switch ( $action ) {
					case 'mark_anonymous' :
						// Check status
						if ( $donation->_anonymous_donation == 1 )
							throw new Exception( __( 'This donation is already anonymous', 'wpdonations' ) );

						// Update
						update_post_meta( $donation_id, '_anonymous_donation', 1 );

						// Message
						$this->donation_dashboard_message = '<div class="wpdonations-message">' . sprintf( __( '%s has been marked as anonymous', 'wpdonations' ), $donation->post_title ) . '</div>';
						break;
					case 'mark_public' :
						// Check status
						if ( $donation->_anonymous_donation != 1 )
							throw new Exception( __( 'This donation is already public', 'wpdonations' ) );

						// Update
						update_post_meta( $donation_id, '_anonymous_donation', 0 );

						// Message
						$this->donation_dashboard_message = '<div class="wpdonations-message">' . sprintf( __( '%s has been marked as public', 'wpdonations' ), $donation->post_title ) . '</div>';
						break;
					case 'delete' :
						// Trash it
						wp_trash_post( $donation_id );

						// Message
						$this->donation_dashboard_message = '<div class="wpdonations-message">' . sprintf( __( '%s has been deleted', 'wpdonations' ), $donation->post_title ) . '</div>';

						break;
				}

				do_action( 'wpdonations_my_donation_do_action', $action, $donation_id );

			} catch ( Exception $e ) {
				$this->donation_dashboard_message = '<div class="wpdonations-error">' . $e->getMessage() . '</div>';
			}
		}
	}

	/**
	 * Shortcode which lists the logged in user's donations
	 */
	public function donation_dashboard( $atts ) {
		global $wpdonations;

		if ( ! is_user_logged_in() ) {
			_e( 'You need to be signed in to manage your donations.', 'wpdonations' );
			return;
		}

		wp_enqueue_script( 'wpdonations-donation-dashboard' );

		// If doing an action, show conditional content if needed....
		if ( ! empty( $_REQUEST['action'] ) ) {

			$action      = sanitize_title( $_REQUEST['action'] );
			$donation_id = absint( $_REQUEST['donation_id'] );

			switch ( $action ) {
				case 'edit' :
					return $wpdonations->forms->get_form( 'edit-donation' );
			}
		}

		// ....If not show the donation dashboard
		$args = array(
			'post_type'           => 'donation',
			'post_status'         => array( 'publish', 'pending' ),
			'ignore_sticky_posts' => 1,
			'posts_per_page'      => -1,
			'orderby'             => 'date',
			'order'               => 'desc',
			'author'              => get_current_user_id()
		);

		$donations = get_posts( $args );

		ob_start();

		echo $this->donation_dashboard_message;

		get_wpdonations_template( 'donation-dashboard.php', array( 'donations' => $donations ) );

		return ob_get_clean();
	}

	/**
	 * output_donations function.
	 *
	 * @access public
	 * @param mixed $args
	 * @return void
	 */
	public function output_donations( $atts ) {
		
		global $wpdonations, $donations;
		
		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

		ob_start();
		

		extract( $atts = shortcode_atts( apply_filters( 'wpdonations_output_donations_defaults', array(
			'per_page'        => get_option( 'wpdonations_per_page' ),
			'orderby'         => 'date',
			'order'           => 'desc',
			'categories'      => '',
			'campaigns'        => ''
		) ), $atts ) );

		$categories = array_filter( array_map( 'trim', explode( ',', $categories ) ) );

		$args = array(
			'post_type'           => 'donation',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
			'posts_per_page'      => $per_page,
			'orderby'             => $orderby,
			'order'               => $order,
			'paged' 			  => $paged
		);

		if ( $categories )
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'donation_category',
					'field'    => 'slug',
					'terms'    => $categories
				)
			);

		if ( $campaigns )
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'donation_campaign',
					'field'    => 'slug',
					'terms'    => $campaigns
				)
			);

		if ( get_option( 'wpdonations_hide_anonymous_donations' ) == 1 )
			$args['meta_query'] = array(
				array(
					'key'     => '_anonymous_donation',
					'value'   => '1',
					'compare' => '!='
				)
			);

		$donations = new WP_Query( apply_filters( 'wpdonations_output_donations_args', $args ) );

		if ( $donations->have_posts() ) : ?>

			<ul class="donations">

				<?php while ( $donations->have_posts() ) : $donations->the_post(); ?>

					<?php get_wpdonations_template_part( 'content', 'donation' ); ?>

				<?php endwhile; ?>

			</ul>
			
            <?php if ( $donations->found_posts > $per_page ) : ?>
            
            	<?php get_wpdonations_template_part( 'donations', 'pagination' ); ?>

            <?php endif; ?>

		<?php endif;

		wp_reset_postdata();


		return '<div class="donations">' . ob_get_clean() . '</div>';
	}

	/**
	 * output_donation function.
	 *
	 * @access public
	 * @param array $args
	 * @return string
	 */
	public function output_donation( $atts ) {
		global $wpdonations;

		extract( shortcode_atts( array(
			'id' => '',
		), $atts ) );

		if ( ! $id )
			return;

		ob_start();

		$args = array(
			'post_type'   => 'donation',
			'post_status' => 'publish',
			'p'           => $id
		);

		$donations = new WP_Query( $args );

		if ( $donations->have_posts() ) : ?>

			<?php while ( $donations->have_posts() ) : $donations->the_post(); ?>

				<h1><?php the_title(); ?></h1>

				<?php get_wpdonations_template_part( 'content-single', 'donation' ); ?>

			<?php endwhile; ?>

		<?php endif;

		wp_reset_postdata();

		return '<div class="donation_shortcode single_donation">' . ob_get_clean() . '</div>';
	}

	/**
	 * donation Summary shortcode
	 *
	 * @access public
	 * @param array $args
	 * @return string
	 */
	public function output_donation_summary( $atts ) {
		global $wpdonations;

		extract( shortcode_atts( array(
			'id'    => '',
			'width' => '250px',
			'align' => 'left'
		), $atts ) );

		if ( ! $id )
			return;

		ob_start();

		$args = array(
			'post_type'   => 'donation',
			'post_status' => 'publish',
			'p'           => $id
		);
		
		$donations = new WP_Query( $args );

		if ( $donations->have_posts() ) : ?>

			<?php while ( $donations->have_posts() ) : $donations->the_post(); ?>

				<div class="donation_summary_shortcode align<?php echo $align ?>" style="width: <?php echo $width ? $width : auto; ?>">

					<?php get_wpdonations_template_part( 'content-summary', 'donation' ); ?>

				</div>

			<?php endwhile; ?>

		<?php endif;

		wp_reset_postdata();

		return ob_get_clean();
	}
	
	/**
	 * Donations Funds shortcode
	 *
	 * @access public
	 * @param array $args
	 * @return string
	 */
	public function output_donation_funds( $atts ) {
		global $wpdonations;
		
		extract( shortcode_atts( array(
			'before'    => '',
			'after'    	=> ''
		), $atts ) );

		return the_funds( $before, $after, false );
	}
	
	/**
	 * Campaign Funds shortcode
	 *
	 * @access public
	 * @param array $args
	 * @return string
	 */
	public function output_campaign_funds( $atts ) {
		global $wpdonations;
		
		extract( shortcode_atts( array(
			'id'     => '',
			'before' => '',
			'after'  => ''
		), $atts ) );
		
		if ( ! $id )
			return;

		return the_campaign_funds( $before, $after, false, $id );
	}
	
	/**
	 * Donation Target shortcode
	 *
	 * @access public
	 * @param array $args
	 * @return string
	 */
	public function output_donation_target( $atts ) {
		global $wpdonations;
		
		extract( shortcode_atts( array(
			'before' => '',
			'after'  => ''
		), $atts ) );

		return the_target( $before, $after, false );
	}
	
	/**
	 * All funds raised percentage shortcode
	 *
	 * @access public
	 * @param array $args
	 * @return string
	 */
	public function output_funds_raised_percentage( $atts ) {
		global $wpdonations;
		
		extract( shortcode_atts( array(
			'before' => '',
			'after'  => '%'
		), $atts ) );

		return the_funds_raised_percentage( $before, $after, false );
	}
	
	/**
	 * Progress bar shortcode
	 *
	 * @access public
	 * @param array $args
	 * @return string
	 */
	public function output_progress_bar( $atts ) {
		global $wpdonations;

		extract( shortcode_atts( array(
			'id'    => ''
		), $atts ) );

		if ( $id ) {
			$target = the_campaign_target( '', '', false, $id );
			$funds = the_campaign_funds( '', '', false, $id );
		} else {
			$target = get_option( 'wpdonations_general_target' );
			$funds = the_funds( '', '', false );
		}
		
		if( $funds > 0 && $target > 0 ) {
			$percent = ($funds*100)/$target;
			$percent = number_format( $percent, 2, '.', '' );
		} else {
			$percent = 0;
		}

		ob_start();

		get_wpdonations_template( 'progress-bar.php', array( 'percent' => $percent ) );

		return ob_get_clean();
	}
	
	/**
	 * Campaign funds raised percentage shortcode
	 *
	 * @access public
	 * @param array $args
	 * @return string
	 */
	public function output_campaign_funds_raised_percentage( $atts ) {
		global $wpdonations;
		
		extract( shortcode_atts( array(
			'id'     => '',
			'before' => '',
			'after'  => '%'
		), $atts ) );
		
		if ( ! $id )
			return;

		return the_campaign_funds_raised_percentage( $before, $after, false, $id );
	}
	
	/**
	 * Campaign Target shortcode
	 *
	 * @access public
	 * @param array $args
	 * @return string
	 */
	public function output_campaign_target( $atts ) {
		global $wpdonations;
		
		extract( shortcode_atts( array(
			'id'     => '',
			'before' => '',
			'after'  => ''
		), $atts ) );
		
		if ( ! $id )
			return;

		return the_campaign_target( $before, $after, false, $id );
	}
}

new WPdonations_Shortcodes();