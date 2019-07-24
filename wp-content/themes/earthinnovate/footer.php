<?php
/**
 * The template for displaying the footer.
 *
 * Contains footer content and the closing of the
 * #main and #container div elements.
 *
 * @package WordPress
 * @subpackage Earth Innovate
 * @since Version 1.0
 */
$project
?>
		</div><!-- #main-container -->

		<div id="footer">
			<div id="footer-widgets" class="group section textured">
				
					<div class="footer-widget social-links">
						<div class="section">
							<div class="list-container col span_1_of_2">
								<ul>
									<li><h6 class="widget-title"><?php _ex('Follow Us', 'social media','earthinnovate'); ?></h6></li>
									<?php $soc_media = array(
										__('Facebook', 'earthinnovate') => 'https://www.facebook.com/Earth.Innovation',
										__('LinkedIn', 'earthinnovate') => 'https://www.linkedin.com/company/earth-innovation-institute?trk=biz-companies-cym',
										__('Twitter', 'earthinnovate') => 'https://twitter.com/EarthInnovate'
										);
									foreach ( $soc_media as $name => $url ) : ?>
									<li>
										<a class="<?php echo strtolower($name) . '-small'; ?> social-icon" target="_blank" href="<?php echo esc_url($url); ?>"></a>
										<a href="<?php echo esc_url($url); ?>" target="_blank"><?php echo $name; ?></a>
									</li>

									<?php endforeach; ?>
								</ul>
							</div>
							<div class="newsletter col span_1_of_2">
								<a target="_blank" href="https://earthinnovation.org/stay-connected/" alt="Sign up for the Earth Innovation Institute Newsletter">
									<button class="blue newsletter-title"><?php _ex('Stay Connected', 'newsletter signup', 'earthinnovate'); ?></button>
								</a>
							</div>
						</div>
					</div><!-- .social-links -->
				<div class="addresses">
					<div class="section">
						<div class="col span_1_of_2 office">
							<address class="vcard">
								<span class="adr"><strong><?php _e('Office Address','earthinnovate'); ?>:</strong></span>
								<span class="street-address">98 Battery Street, Suite 250, San Francisco, CA 94111</span>
							</address>
							</div>
					</div>
				</div>
			</div>
		</div><!-- #footer -->

	</div><!--#container-->
</div><!--#outer-wrap-->

<!-- load style.css in non-js environments -->
<noscript>
<link rel='stylesheet' id='style-css'  href='<?php echo get_template_directory_uri() ;?>/style.css' type='text/css' media='screen' />
</noscript>

<?php wp_footer(); ?>

<script type="text/javascript">
	(function($) {
		$(document).on('click', '#mobile-nav .lang_sel_sel.icl-en', function(e) {
			e.preventDefault();
			$('#lang_sel_click ul:not(:first-child)').toggleClass('mobile-lang-active');
		});
	})(jQuery);
</script>
<script type="text/javascript">
	<?php if (is_page(302)) { ?>
	var form = document.getElementById("donation_preview");
	if (form !== null ){ form.setAttribute("class","col span_5_of_8");  }
	<?php } ?>
</script>

<?php if (is_page(302)) { ?>
	<style>
		/****** Donation preview ******/
		/* Buttons */
		.page-id-302 #main-content .donate #donation_preview input[type="submit"] {
			width: 10em;
			height: 3em;
			background: #f58220;
			border: 0.125em solid #f58220;
			color: #fff;
			font-weight: bold;
			font-size: 1em;
			-webkit-transition: color 300ms ease, background 300ms ease;
			-moz-transition: color 300ms ease, background 300ms ease;
			-o-transition: color 300ms ease, background 300ms ease;
			transition: color 300ms ease, background 300ms ease;
			}
		.page-id-302 #main-content .donate #donation_preview input[type="submit"]:hover,
		.page-id-302 #main-content .donate #donation_preview input[type="submit"]:active,
		.page-id-302 #main-content .donate #donation_preview input[type="submit"]:focus {
			color: #807764;
			background: transparent;
			}
		/* Details */
		.page-id-302 #main-content .donate #donation_preview .donation_preview { margin: 2em 0 0; }
			.page-id-302 #main-content .donate #donation_preview .donation_preview > h1 { font-size: 2.369em; margin: 0 0 0.5em; }
			.page-id-302 #main-content .donate #donation_preview .donation_preview .single_donation { margin: 0 auto; }
			.page-id-302 #main-content .donate #donation_preview .single_donation > .meta,
			.page-id-302 #main-content .donate #donation_preview .single_donation > .donor {
				display: inline-block;
				width: 48.5%;
				margin: 0 -0.25em 2em 3%;
				vertical-align: top;
				text-align: left;
				padding: 0 0;
				}
			.page-id-302 #main-content .donate #donation_preview .single_donation > .meta { margin: 0 -0.25em 2em 0; text-align: center; }
				.page-id-302 #main-content .donate #donation_preview .single_donation > .meta > li { float: none; }
				.page-id-302 #main-content .donate #donation_preview .single_donation > .meta > li:nth-of-type(2),
				.page-id-302 #main-content .donate #donation_preview .single_donation > .meta > li:nth-of-type(3) { display: none; }
		/* Side Column */
		.page-id-302 #main-content .donate #donation_preview + .span_3_of_8 { margin: 0 -0.25em 3em 3%; }

	</style>
<?php } // end donate page styles ?>

</body>
</html>