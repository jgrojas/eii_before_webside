<?php
/**
 * The Header for the theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package WordPress
 * @subpackage Earth Innovate
 * @since Version 1.0
 */ ?>

<!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7 no-js" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8 no-js" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8) ]><!-->
<html class="no-js" <?php language_attributes(); ?>>
<!--><![endif]-->

<head>
<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,300,600,700">
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<?php if(is_search()) { ?><meta name="robots" content="noindex, nofollow" /><?php } ?>

<title><?php if (is_404()) { _e('Page Not Found', 'earthinnovate'); } elseif (is_search()) { _e('Search Results', 'earthinnovate'); } elseif ( is_day() || is_month() || is_year() ) { echo __('Archives', 'earthinnovate') . ': '; wp_title(''); } elseif ( is_front_page()) { bloginfo('name'); } else { wp_title('|',true,'right'); bloginfo('name'); } ?></title>

<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/images/favicon.png" type="image/x-icon" />
<link rel="icon" href="<?php echo get_template_directory_uri(); ?>/images/favicon.png" type="image/x-icon" />
<!--[if lt IE 9]>
<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script>
<![endif]-->

<?php wp_head(); ?>

<script type="text/javascript">
var templateURI = '<?php echo get_template_directory_uri(); ?>';
/* 'our work' page-specific variables */ <?php if (is_page(7)) { ?> var showMap = true, homeURL = '<?php echo home_url('/'); ?>'; <?php } else { ?> var showMap = false, homeURL = null; <?php }  ?>
/* home page slider-specific variable */ <?php if (is_front_page()) { ?> var slider = true; <?php } else { ?> var slider = false; <?php } ?>
/* load non-critical CSS asynchronously */
loadCSS( templateURI+'/style.css');
</script>

</head>

<body <?php body_class("max"); ?>>
	<div id="outer-wrap">
		<div id="container" class="max">
			<!-- JS-disabled message -->
			<noscript>
				<div id="js-disabled" class="no-js clearfix">
					<span class="alert-small"><?php _ex('Warning', 'javascript alert', 'earthinnovate'); ?>: </span>
					<p><?php echo wp_kses( __( 'This site looks and functions best with <strong>Javascript enabled</strong>.', 'earthinnovate' ), array(  'strong' => array( ) ) ); ?>
					</p>
					<a href="http://enable-javascript.com" target="_blank" class="js-how"><?php _ex("Don't know how?", 'javascript alert', 'earthinnovate'); ?></a>
				</div>
			</noscript>

			<div id="topbar" class="max clearfix textured">
				<div class="nav-container clearfix">
					<nav id="second-nav" class="menu clearfix"><?php wp_nav_menu( array( 'theme_location' => 'secondary_nav', 'container' => false ) ); ?></nav>
					
						<div class="language-map-small"></div>
						<?php // setup language selector dropdown
						define('ICL_DONT_LOAD_NAVIGATION_CSS',true);
						define('ICL_DONT_LOAD_LANGUAGE_SELECTOR_CSS',true);
						do_action('icl_language_selector'); ?>
					
					<?php get_search_form(); ?>
					<div class="search-icon"></div>
				</div>
			</div>

			<header id="masthead" class="clearfix">
				<a class="logo" title="Home" href="<?php echo esc_url(home_url('/')); ?>"><span class="logo-small icon"></span><h1><?php bloginfo('name'); ?></h1></a>
				<a class="menu-toggle menu-open-small" href="#mobile-nav"></a>
				<div class="header-extras">
					<a class="donate" href="<?php echo get_page_link(302); ?>"><?php _e('Donate', 'earthinnovate'); ?><div class="r-arrow"></div></a>
				</div>
				<nav id="primary-nav" class="menu clearfix"><?php wp_nav_menu( array( 'theme_location' => 'primary_nav', 'container' => false ) ); ?></nav>
			</header>

			<nav id="mobile-nav" class="textured">
				<a class="menu-toggle menu-close-small" href="#masthead"></a>
				<div class="nav-container">
					<div class="color-bar clearfix">
						<a class="nav-buttons donate max" href="<?php echo get_page_link(302); ?>"><span class="donate-icon nav-icons"></span><?php _e('Donate', 'earthinnovate'); ?></a>
 						<a class="nav-buttons search max" href="#"><span class="search-icon nav-icons"></span><?php _ex('Search', 'noun', 'earthinnovate'); ?></a>
 						<?php get_search_form(); ?>
 					</div>
					<?php wp_nav_menu( array( 'theme_location' => 'mobile_nav', 'container' => false, 'menu_class' => 'menu clearfix' ) ); ?>
					<div class="social-links group section">
						<div class="col span_1_of_2"><a class="facebook-small" target="_blank" href="https://www.facebook.com/Earth.Innovation" title="Facebook"></a></div>
						<div class="col span_1_of_2"><a class="linkedin-small" target="_blank" href="https://www.linkedin.com/company/earth-innovation-institute?trk=biz-companies-cym" title="LinkedIn"></a></div>
					</div>
					<div class="mobile-lang">
						<div class="language-map_light-small"></div>
						<?php do_action('icl_language_selector'); ?>
					</div>
				</div>
			</nav>

			<div id="main-container">