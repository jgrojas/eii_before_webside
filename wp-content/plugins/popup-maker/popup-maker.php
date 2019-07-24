<?php
/**
<<<<<<< HEAD
 * Plugin Name:  Popup Maker
 * Plugin URI:   https://wppopupmaker.com/?utm_campaign=PluginInfo&utm_source=plugin-header&utm_medium=plugin-uri
 * Description:  Easily create & style popups with any content. Theme editor to quickly style your popups. Add forms, social media boxes, videos & more.
 * Version:      1.8.6
 * Author:       WP Popup Maker
 * Author URI:   https://wppopupmaker.com/?utm_campaign=PluginInfo&utm_source=plugin-header&utm_medium=author-uri
 * License:      GPL2 or later
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:  popup-maker
 * Domain Path:  /languages/
=======
 * Plugin Name: Popup Maker
 * Plugin URI: https://wppopupmaker.com/?utm_campaign=PluginInfo&utm_source=plugin-header&utm_medium=plugin-uri
 * Description: Easily create & style popups with any content. Theme editor to quickly style your popups. Add forms, social media boxes, videos & more.
 * Author: WP Popup Maker
 * Version: 1.7.30
 * Author URI: https://wppopupmaker.com/?utm_campaign=PluginInfo&utm_source=plugin-header&utm_medium=author-uri
 * Text Domain: popup-maker
>>>>>>> fb4f61eb64cfee2e2fbc0b1d7acd4491a9e6a9bd
 *
 * @package     POPMAKE
 * @category    Core
 * @author      Daniel Iser
<<<<<<< HEAD
 * @copyright   Copyright (c) 2018, Wizard Internet Solutions
=======
 * @copyright   Copyright (c) 2016, Wizard Internet Solutions
>>>>>>> fb4f61eb64cfee2e2fbc0b1d7acd4491a9e6a9bd
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

<<<<<<< HEAD
=======

>>>>>>> fb4f61eb64cfee2e2fbc0b1d7acd4491a9e6a9bd
/**
 * Class Autoloader
 *
 * @param $class
 */
function pum_autoloader( $class ) {

	if ( strncmp( 'PUM_Newsletter_', $class, strlen( 'PUM_Newsletter_' ) ) === 0 && class_exists( 'PUM_MCI' ) && ! empty( PUM_MCI::$VER ) && version_compare( PUM_MCI::$VER, '1.3.0', '<' ) ) {
		return;
	}

	$pum_autoloaders = apply_filters( 'pum_autoloaders', array(
		array(
			'prefix' => 'PUM_',
			'dir'    => dirname( __FILE__ ) . '/classes/',
		),
	) );

	foreach ( $pum_autoloaders as $autoloader ) {
		$autoloader = wp_parse_args( $autoloader, array(
			'prefix'  => 'PUM_',
			'dir'     => dirname( __FILE__ ) . '/classes/',
			'search'  => '_',
			'replace' => '/',
		) );

		// project-specific namespace prefix
		$prefix = $autoloader['prefix'];

		// does the class use the namespace prefix?
		$len = strlen( $prefix );
		if ( strncmp( $prefix, $class, $len ) !== 0 ) {
			// no, move to the next registered autoloader
			continue;
		}

		// get the relative class name
		$relative_class = substr( $class, $len );

		// replace the namespace prefix with the base directory, replace namespace
		// separators with directory separators in the relative class name, append
		// with .php
		$file = $autoloader['dir'] . str_replace( $autoloader['search'], $autoloader['replace'], $relative_class ) . '.php';

		// if the file exists, require it
		if ( file_exists( $file ) ) {
			require_once $file;
		}
	}
}

if ( ! function_exists( 'spl_autoload_register' ) ) {
	include 'includes/compat.php';
}

spl_autoload_register( 'pum_autoloader' ); // Register autoloader

/**
 * Main Popup_Maker Class
 *
 * @since 1.0
 */
class Popup_Maker {

	/**
	 * @var string Plugin Name
	 */
	public static $NAME = 'Popup Maker';

	/**
	 * @var string Plugin Version
	 */
<<<<<<< HEAD
	public static $VER = '1.8.6';
=======
	public static $VER = '1.7.30';
>>>>>>> fb4f61eb64cfee2e2fbc0b1d7acd4491a9e6a9bd

	/**
	 * @var int DB Version
	 */
	public static $DB_VER = 8;

	/**
	 * @var string License API URL
	 */
	public static $API_URL = 'https://wppopupmaker.com';

	/**
	 * @var string
	 */
	public static $MIN_PHP_VER = '5.2.17';

	/**
	 * @var string
	 */
	public static $MIN_WP_VER = '3.6';

	/**
	 * @var string Plugin URL
	 */
	public static $URL;

	/**
	 * @var string Plugin Directory
	 */
	public static $DIR;

	/**
	 * @var string Plugin FILE
	 */
	public static $FILE;

	/**
	 * Used to test if debug_mode is enabled.
	 *
	 * @var bool
	 */
	public static $DEBUG_MODE = false;

	/**
<<<<<<< HEAD
	 * @var PUM_Utils_Cron
	 */
	public $cron;

	/**
	 * @var PUM_Repository_Popups
	 */
	public $popups;

	/**
	 * @var PUM_Repository_Themes
	 */
	public $themes;

	/**
	 * @var null|PUM_Model_Popup
	 */
	public $current_popup;

	/**
	 * @var null|PUM_Model_Theme
	 */
	public $current_theme;

	/**
=======
>>>>>>> fb4f61eb64cfee2e2fbc0b1d7acd4491a9e6a9bd
	 * @var Popup_Maker The one true Popup_Maker
	 */
	private static $instance;

	/**
	 * Main instance
	 *
	 * @return Popup_Maker
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Popup_Maker ) ) {
			self::$instance = new Popup_Maker;
			self::$instance->setup_constants();
			self::$instance->includes();
<<<<<<< HEAD
			add_action( 'init', array( self::$instance, 'load_textdomain' ) );
=======
			self::$instance->load_textdomain();
>>>>>>> fb4f61eb64cfee2e2fbc0b1d7acd4491a9e6a9bd
			self::$instance->init();
		}

		return self::$instance;
	}

	/**
	 * Setup plugin constants
	 */
	private function setup_constants() {

		self::$DIR  = plugin_dir_path( __FILE__ );
		self::$URL  = plugins_url( '/', __FILE__ );
		self::$FILE = __FILE__;

<<<<<<< HEAD
		if ( isset( $_GET['pum_debug'] ) || PUM_Utils_Options::get( 'debug_mode', false ) ) {
=======
		if ( isset( $_GET['pum_debug'] ) || PUM_Options::get( 'debug_mode', false ) ) {
>>>>>>> fb4f61eb64cfee2e2fbc0b1d7acd4491a9e6a9bd
			self::$DEBUG_MODE = true;
		}

		if ( ! defined( 'POPMAKE' ) ) {
			define( 'POPMAKE', self::$FILE );
		}

		if ( ! defined( 'POPMAKE_NAME' ) ) {
			define( 'POPMAKE_NAME', self::$NAME );
		}

		if ( ! defined( 'POPMAKE_SLUG' ) ) {
			define( 'POPMAKE_SLUG', trim( dirname( plugin_basename( __FILE__ ) ), '/' ) );
		}

		if ( ! defined( 'POPMAKE_DIR' ) ) {
			define( 'POPMAKE_DIR', self::$DIR );
		}

		if ( ! defined( 'POPMAKE_URL' ) ) {
			define( 'POPMAKE_URL', self::$URL );
		}

		if ( ! defined( 'POPMAKE_NONCE' ) ) {
			define( 'POPMAKE_NONCE', 'popmake_nonce' );
		}

		if ( ! defined( 'POPMAKE_VERSION' ) ) {
			define( 'POPMAKE_VERSION', self::$VER );
		}

		if ( ! defined( 'POPMAKE_DB_VERSION' ) ) {
			define( 'POPMAKE_DB_VERSION', self::$DB_VER );
		}

		if ( ! defined( 'POPMAKE_API_URL' ) ) {
			define( 'POPMAKE_API_URL', self::$API_URL );
		}
	}

	/**
	 * Include required files
	 */
	private function includes() {

		require_once self::$DIR . 'includes/compat.php';

		// Initialize global options
<<<<<<< HEAD
		PUM_Utils_Options::init();

		/** Loads most of our core functions */
		require_once self::$DIR . 'includes/functions.php';

		/** Deprecated functionality */
		require_once self::$DIR . 'includes/functions-backcompat.php';
		require_once self::$DIR . 'includes/functions-deprecated.php';
		require_once self::$DIR . 'includes/deprecated-classes.php';
		require_once self::$DIR . 'includes/deprecated-filters.php';
		require_once self::$DIR . 'includes/integrations.php';

		// Old Stuff.
		require_once self::$DIR . 'includes/defaults.php';
		require_once self::$DIR . 'includes/input-options.php';

		require_once self::$DIR . 'includes/importer/easy-modal-v2.php';
=======
		PUM_Options::init();

		/** @deprecated 1.7.0 */
		require_once self::$DIR . 'includes/admin/settings/register-settings.php';

		/** General Functions */
		require_once self::$DIR . 'includes/functions/cache.php';
		require_once self::$DIR . 'includes/functions/options.php';
		require_once self::$DIR . 'includes/functions/upgrades.php';
		require_once self::$DIR . 'includes/functions/developers.php';
		require_once self::$DIR . 'includes/migrations.php';

		// TODO Find another place for these admin functions so this can be put in its correct place.
		require_once self::$DIR . 'includes/admin/admin-pages.php';

		require_once self::$DIR . 'includes/actions.php';
		require_once self::$DIR . 'includes/class-popmake-cron.php';
		require_once self::$DIR . 'includes/defaults.php';
		require_once self::$DIR . 'includes/google-fonts.php';
		require_once self::$DIR . 'includes/general-functions.php';
		require_once self::$DIR . 'includes/extensions-functions.php';
		require_once self::$DIR . 'includes/input-options.php';
		require_once self::$DIR . 'includes/theme-functions.php';
		require_once self::$DIR . 'includes/misc-functions.php';
		require_once self::$DIR . 'includes/css-functions.php';
		require_once self::$DIR . 'includes/ajax-calls.php';

		require_once self::$DIR . 'includes/importer/easy-modal-v2.php';
		require_once self::$DIR . 'includes/integrations/google-fonts.php';

		require_once self::$DIR . 'includes/templates.php';
		require_once self::$DIR . 'includes/load-popups.php';
		require_once self::$DIR . 'includes/license-handler.php';
>>>>>>> fb4f61eb64cfee2e2fbc0b1d7acd4491a9e6a9bd

		// Phasing Out
		require_once self::$DIR . 'includes/class-popmake-fields.php';
		require_once self::$DIR . 'includes/class-popmake-popup-fields.php';
<<<<<<< HEAD
=======
		require_once self::$DIR . 'includes/class-popmake-popup-theme-fields.php';
		require_once self::$DIR . 'includes/popup-functions.php';

>>>>>>> fb4f61eb64cfee2e2fbc0b1d7acd4491a9e6a9bd

		/**
		 * v1.4 Additions
		 */
<<<<<<< HEAD
		require_once self::$DIR . 'includes/class-pum-fields.php';
		require_once self::$DIR . 'includes/class-pum-form.php';

=======
		require_once self::$DIR . 'includes/class-pum.php';
		require_once self::$DIR . 'includes/class-pum-popup-query.php';
		require_once self::$DIR . 'includes/class-pum-fields.php';
		require_once self::$DIR . 'includes/class-pum-form.php';

		// Functions
		require_once self::$DIR . 'includes/pum-popup-functions.php';
		require_once self::$DIR . 'includes/pum-template-functions.php';
		require_once self::$DIR . 'includes/pum-general-functions.php';
		require_once self::$DIR . 'includes/pum-misc-functions.php';
		require_once self::$DIR . 'includes/pum-template-hooks.php';

>>>>>>> fb4f61eb64cfee2e2fbc0b1d7acd4491a9e6a9bd
		// Modules
		require_once self::$DIR . 'includes/modules/menus.php';
		require_once self::$DIR . 'includes/modules/admin-bar.php';
		require_once self::$DIR . 'includes/modules/reviews.php';

<<<<<<< HEAD
		require_once self::$DIR . 'includes/pum-install-functions.php';
=======
		// Upgrades
		if ( is_admin() ) {
			//require_once self::$DIR . 'includes/admin/class-pum-admin-upgrades.php';
		}

		// Deprecated Code
		require_once self::$DIR . 'includes/pum-deprecated.php';
		require_once self::$DIR . 'includes/pum-deprecated-v1.4.php';
		require_once self::$DIR . 'includes/pum-deprecated-v1.7.php';

		if ( is_admin() ) {
			require_once self::$DIR . 'includes/admin/admin-setup.php';
			require_once self::$DIR . 'includes/admin/admin-functions.php';
			require_once self::$DIR . 'includes/admin/themes/metabox.php';
			require_once self::$DIR . 'includes/admin/themes/metabox-close-fields.php';
			require_once self::$DIR . 'includes/admin/themes/metabox-container-fields.php';
			require_once self::$DIR . 'includes/admin/themes/metabox-content-fields.php';
			require_once self::$DIR . 'includes/admin/themes/metabox-overlay-fields.php';
			require_once self::$DIR . 'includes/admin/themes/metabox-title-fields.php';
			require_once self::$DIR . 'includes/admin/themes/metabox-preview.php';
			require_once self::$DIR . 'includes/admin/extensions/extensions-page.php';
			require_once self::$DIR . 'includes/admin/pages/support.php';
			require_once self::$DIR . 'includes/admin/metabox-support.php';
		}

		require_once self::$DIR . 'includes/integrations/class-pum-woocommerce-integration.php';
		require_once self::$DIR . 'includes/integrations/class-pum-buddypress-integration.php';

		// Ninja Forms Integration
		require_once self::$DIR . 'includes/integrations/class-pum-ninja-forms.php';
		// CF7 Forms Integration
		require_once self::$DIR . 'includes/integrations/class-pum-cf7.php';
		// Gravity Forms Integration
		require_once self::$DIR . 'includes/integrations/class-pum-gravity-forms.php';
		// WPML Integration
		require_once self::$DIR . 'includes/integrations/class-pum-wpml.php';

		require_once self::$DIR . 'includes/pum-install-functions.php';
		require_once self::$DIR . 'includes/install.php';
>>>>>>> fb4f61eb64cfee2e2fbc0b1d7acd4491a9e6a9bd
	}

	/**
	 * Loads the plugin language files
	 */
	public function load_textdomain() {
		// Set filter for plugin's languages directory
<<<<<<< HEAD
		$lang_dir = apply_filters( 'pum_lang_dir', dirname( plugin_basename( POPMAKE ) ) . '/languages/' );
		$lang_dir = apply_filters( 'popmake_languages_directory', $lang_dir );

		// Try to load Langpacks first, if they are not available fallback to local files.
		if ( ! load_plugin_textdomain( 'popup-maker', false, $lang_dir ) ) {
			// Traditional WordPress plugin locale filter
			$locale = apply_filters( 'plugin_locale', get_locale(), 'popup-maker' );
			$mofile = sprintf( '%1$s-%2$s.mo', 'popup-maker', $locale );

			// Setup paths to current locale file
			$mofile_local  = $lang_dir . $mofile;
			$mofile_global = WP_LANG_DIR . '/popup-maker/' . $mofile;

			if ( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/popup-maker folder
				load_textdomain( 'popup-maker', $mofile_global );
			} elseif ( file_exists( $mofile_local ) ) {
				// Look in local /wp-content/plugins/popup-maker/languages/ folder
				load_textdomain( 'popup-maker', $mofile_local );
			}
=======
		$popmake_lang_dir = dirname( plugin_basename( POPMAKE ) ) . '/languages/';
		$popmake_lang_dir = apply_filters( 'popmake_languages_directory', $popmake_lang_dir );

		// Traditional WordPress plugin locale filter
		$locale = apply_filters( 'plugin_locale', get_locale(), 'popup-maker' );
		$mofile = sprintf( '%1$s-%2$s.mo', 'popup-maker', $locale );

		// Setup paths to current locale file
		$mofile_local  = $popmake_lang_dir . $mofile;
		$mofile_global = WP_LANG_DIR . '/popup-maker/' . $mofile;

		if ( file_exists( $mofile_global ) ) {
			// Look in global /wp-content/languages/popup-maker folder
			load_textdomain( 'popup-maker', $mofile_global );
		} elseif ( file_exists( $mofile_local ) ) {
			// Look in local /wp-content/plugins/popup-maker/languages/ folder
			load_textdomain( 'popup-maker', $mofile_local );
		} else {
			// Load the default language files
			load_plugin_textdomain( 'popup-maker', false, $popmake_lang_dir );
>>>>>>> fb4f61eb64cfee2e2fbc0b1d7acd4491a9e6a9bd
		}
	}

	public function init() {
<<<<<<< HEAD
		$this->cron   = new PUM_Utils_Cron;
		$this->popups = new PUM_Repository_Popups();
		$this->themes = new PUM_Repository_Themes();

=======
>>>>>>> fb4f61eb64cfee2e2fbc0b1d7acd4491a9e6a9bd
		PUM_Types::init();
		PUM_AssetCache::init();
		PUM_Site::init();
		PUM_Admin::init();
<<<<<<< HEAD
		PUM_Utils_Upgrades::instance();
=======
		PUM_Upgrades::instance();
>>>>>>> fb4f61eb64cfee2e2fbc0b1d7acd4491a9e6a9bd
		PUM_Newsletters::init();
		PUM_Previews::init();
		PUM_Integrations::init();
		PUM_Privacy::init();

<<<<<<< HEAD
		PUM_Utils_Alerts::init();

=======
>>>>>>> fb4f61eb64cfee2e2fbc0b1d7acd4491a9e6a9bd
		PUM_Shortcode_Popup::init();
		PUM_Shortcode_PopupTrigger::init();
		PUM_Shortcode_PopupClose::init();

		/**
		 * Here we check for previous FS optin.
		 * If no test has been performed we initialize Freemius one last time to check optin status.
		 */
		$has_opted_in = get_option( 'pum_previously_opted_using_freemius' );
<<<<<<< HEAD

=======
>>>>>>> fb4f61eb64cfee2e2fbc0b1d7acd4491a9e6a9bd
		if ( false === $has_opted_in ) {
			PUM_Freemius::instance();
			update_option( 'pum_previously_opted_using_freemius', PUM_Freemius::instance()->fs()->is_registered() ? 1 : 0 );
		} else if ( 1 === $has_opted_in ) {
			/**
			 * The user has previously opted via Freemius. Lets show custom messages in the new optin requests.
			 */
		} else {
			/**
			 * The user never opted via Freemius. Show default optin request.
			 */
		}
	}

	/**
	 * Returns true when debug mode is enabled.
	 *
	 * @return bool
	 */
	public static function debug_mode() {
		return true === self::$DEBUG_MODE;
	}

}

/**
 * Initialize the plugin.
 */
Popup_Maker::instance();

/**
 * The code that runs during plugin activation.
 * This action is documented in classes/Activator.php
 */
register_activation_hook( __FILE__, array( 'PUM_Activator', 'activate' ) );

/**
 * The code that runs during plugin deactivation.
 * This action is documented in classes/Deactivator.php
 */
register_deactivation_hook( __FILE__, array( 'PUM_Deactivator', 'deactivate' ) );

/**
 * @deprecated 1.7.0
 */
function popmake_initialize() {
	// Disable Unlimited Themes extension if active.
	remove_action( 'popmake_initialize', 'popmake_ut_initialize' );

	// Initialize old PUM extensions
	do_action( 'pum_initialize' );
	do_action( 'popmake_initialize' );
}

add_action( 'plugins_loaded', 'popmake_initialize' );

/**
 * The main function responsible for returning the one true Popup_Maker
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $popmake = PopMake(); ?>
 *
 * @since      1.0
 * @deprecated 1.7.0
 *
 * @return object The one true Popup_Maker Instance
 */
<<<<<<< HEAD
function PopMake() {
	return Popup_Maker::instance();
}

/**
 * The main function responsible for returning the one true Popup_Maker
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * @since      1.8.0
 *
 * @return Popup_Maker
 */
function pum() {
=======

function PopMake() {
>>>>>>> fb4f61eb64cfee2e2fbc0b1d7acd4491a9e6a9bd
	return Popup_Maker::instance();
}
