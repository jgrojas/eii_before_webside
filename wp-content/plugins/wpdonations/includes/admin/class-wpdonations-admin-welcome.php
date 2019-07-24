<?php
/**
 * Welcome Page Class
 *
 * Shows a feature overview for the new version (major) and credits.
 *
 * Adapted from code in EDD (Copyright (c) 2012, Pippin Williamson) and WP.
 *
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WPdonations_Admin_Welcome class.
 */
class WPdonations_Admin_Welcome {

	private $plugin;

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		$this->plugin             = 'wpdonations/wpdonations.php';

		add_action( 'admin_menu', array( $this, 'admin_menus') );
		add_action( 'admin_head', array( $this, 'admin_head' ) );
		add_action( 'admin_init', array( $this, 'welcome'    ) );
	}

	/**
	 * Add admin menus/screens
	 *
	 * @access public
	 * @return void
	 */
	public function admin_menus() {
		if ( empty( $_GET['page'] ) ) {
			return;
		}

		$welcome_page_name  = __( 'About WPdonations', 'wpdonations' );
		$welcome_page_title = __( 'Welcome to WPdonations', 'wpdonations' );

		switch ( $_GET['page'] ) {
			case 'wpdonations-about' :
				$page = add_dashboard_page( $welcome_page_title, $welcome_page_name, 'manage_options', 'wpdonations-about', array( $this, 'about_screen' ) );
				add_action( 'admin_print_styles-'. $page, array( $this, 'admin_css' ) );
			break;
		}
	}

	/**
	 * admin_css function.
	 *
	 * @access public
	 * @return void
	 */
	public function admin_css() {
		wp_enqueue_style( 'wpdonations-activation', WPDONATIONS_PLUGIN_URL . '/assets/css/admin.css', array(), WPDONATIONS_VERSION );
	}

	/**
	 * Add styles just for this page, and remove dashboard page links.
	 *
	 * @access public
	 * @return void
	 */
	public function admin_head() {
		remove_submenu_page( 'index.php', 'wpdonations-about' );

		?>
		<style type="text/css">
			/*<![CDATA[*/
			.about-wrap .wpdonations-feature {
				overflow: visible !important;
				*zoom:1;
			}
			.about-wrap .wpdonations-feature:before,
			.about-wrap .wpdonations-feature:after {
				content: " ";
				display: table;
			}
			.about-wrap .wpdonations-feature:after {
				clear: both;
			}
			.about-wrap .feature-rest div {
				width: 50% !important;
				padding-left: 100px;
				-moz-box-sizing: border-box;
				box-sizing: border-box;
				margin: 0 !important;
			}
			.about-wrap .feature-rest div.last-feature {
				padding-left: 100px;
				padding-right: 0;
			}
			.about-wrap div.icon {
				width: 0 !important;
				padding: 0;
				margin: 0;
			}
			.about-wrap .feature-rest div.icon:before { 
				font-family: wpdonations !important;
				font-weight: normal;
				width: 100%;
				font-size: 170px;
				line-height: 125px;
				color: #9c5d90;
				display: inline-block;
				position: relative;
				text-align: center;
				speak: none;
				margin: 0 0 0 -100px;
				content: "\e01d";
				-webkit-font-smoothing: antialiased;
				-moz-osx-font-smoothing: grayscale;
			}
			.about-integrations {
				background: #fff;
				margin: 20px 0;
				padding: 1px 20px 10px;
			}
			/*]]>*/
		</style>
		<?php
	}

	/**
	 * Into text/links shown on all about pages.
	 *
	 * @access private
	 * @return void
	 */
	private function intro() {

		?>
		<h1><?php printf( __( 'Welcome to WPdonations %s', 'wpdonations' ), WPDONATIONS_VERSION ); ?></h1>

		<div class="about-text wpdonations-about-text">
			<?php
				$message = __( 'Thanks for installing!', 'wpdonations' );

				printf( __( '%s WPdonations %s brings you many new features to raise funds easily. Do not forget to visit the documentation site <a href="http://docs.remicorson.com/category/plugins/wpdonations/" target="_blank">here</a>!', 'wpdonations' ), $message, WPDONATIONS_VERSION );
			?>
		</div>

		<h2 class="nav-tab-wrapper">
			<a class="nav-tab <?php if ( $_GET['page'] == 'wpdonations-about' ) echo 'nav-tab-active'; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'wpdonations-about' ), 'index.php' ) ) ); ?>">
				<?php _e( "What's New", 'wpdonations' ); ?>
			</a>
			<a class="nav-tab" href="http://docs.remicorson.com/category/plugins/wpdonations/" target="_blank">
				<?php _e( "Documentation", 'wpdonations' ); ?>
			</a>
		</h2>
		<?php
	}

	/**
	 * Output the about screen.
	 */
	public function about_screen() {
		?>
		<div class="wrap about-wrap">

			<?php $this->intro(); ?>

			<div class="changelog">

				<div class="feature-section col three-col">
					<div>
						<h4><?php _e( 'Multiple Payment Gateways', 'wpdonations' ); ?></h4>
						<p><?php _e( 'WPdonations support multiple payment gateways as extra add-ons. Make sure you check the add-ons page to find the one you are looking for.', 'wpdonations' ); ?></p>
					</div>

					<div>
						<h4><?php _e( 'Offline Payment', 'wpdonations' ); ?></h4>
						<p><?php _e( 'Your donors can now pay their donation by check of bank transfer. And you manually validate donations when payment is received.', 'wpdonations' ); ?></p>
					</div>

					<div class="last-feature">
						<h4><?php _e( 'Anonymous Donations', 'wpdonations' ); ?></h4>
						<p><?php _e( 'Donors can choose to hide their name in the donations list. But hopefully you can see donors details in the administration', 'wpdonations' ); ?></p>
					</div>
				</div>
				<div class="feature-section col three-col">

					<div>
						<h4><?php _e( 'Custom Templates', 'wpdonations' ); ?></h4>
						<p><?php _e( 'You can now edit every single element displayed on the frontend using custom templates.', 'wpdonations' ); ?></p>
					</div>

					<div>
						<h4><?php _e( 'Fields Customization', 'wpdonations' ); ?></h4>
						<p><?php _e( 'WPdonations provides a great new feature: you can now edit/add/delete donation submission form fields easily.', 'wpdonations' ); ?></p>
					</div>

					<div class="last-feature">
						<h4><?php _e( 'Unlimited Campaigns', 'wpdonations' ); ?></h4>
						<p><?php _e( 'During the donation process, your donors can choose to give for a specific campaign or at general level.', 'wpdonations' ); ?></p>
					</div>

				</div>
				<div class="feature-section col three-col">

					<div>
						<h4><?php _e( 'Donations Dashboard', 'wpdonations' ); ?></h4>
						<p><?php _e( 'Your donors can now access their donations on a donations dashboard page.', 'wpdonations' ); ?></p>
					</div>

					<div>
						<h4><?php _e( 'Recurring Payment', 'wpdonations' ); ?></h4>
						<p><?php _e( 'Donors can choose to create a recurring payment for their donations: every week, month or year.', 'wpdonations' ); ?></p>
					</div>

					<div class="last-feature">
						<h4><?php _e( 'Campaigns Goals', 'wpdonations' ); ?></h4>
						<p><?php _e( 'You can now define a goal for each campaign!', 'wpdonations' ); ?></p>
					</div>

				</div>
			</div>

			<div class="return-to-dashboard">
				<a href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'wpdonations-settings' ), 'admin.php' ) ) ); ?>"><?php _e( 'Go to WPdonations Settings', 'wpdonations' ); ?></a>
			</div>
		</div>
		<?php
	}


	/**
	 * Sends user to the welcome page on first activation
	 */
	public function welcome() {

		// Bail if no activation redirect transient is set
	    if ( ! get_transient( '_wpdonations_activation_redirect' ) )
			return;

		// Delete the redirect transient
		delete_transient( '_wpdonations_activation_redirect' );


		wp_redirect( admin_url( 'index.php?page=wponations-about' ) );
		exit;
	}
}

new WPdonations_Admin_Welcome();
