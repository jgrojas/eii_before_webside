<?php

/*

* Template for Contact Us, Donate, and general pages

*

* @package WordPress

* @subpackage Earth Innovate

* @since Version 1.0

*/



/*

	#336 = 'CONTACT US'

	#302 = 'DONATE'

	#612 = 'RESOURCES' ----- BE SURE TO CHANGE THIS ID BEFORE PUBLISHING

*/

global $post;

$contactUsPageIDs = array(336, 5518, 5520, 5522); // english and translations

$isContactPage = (in_array($post->ID, $contactUsPageIDs)) ? true : false;



if ($isContactPage) :

	add_filter('body_class', function($classes) {

		$classes[] = 'contact-us';

		return $classes;

	});

endif;

	
if (is_page(8698)): // Our Accountability 
	//* custom body class

endif;



get_header();



if (have_posts()) : while (have_posts()) : the_post(); ?>

	<?php if ($isContactPage) : ?>

	<h3 class="page-title"><span><?php the_title();  ?></span></h3>

	<div class="hq-details">

		<address class="vcard">

			<p class="adr">

				<strong><?php _e("Office Address",'earthinnovate'); ?>:</strong>

			</p>

			<p class="adr">

				<span class="street-address"><?php _e('98 Battery Street, Suite 250', 'earthinnovate'); ?></span><br />

				<span class="locality"><?php _e('San Francisco','earthinnovate'); ?></span>, <abbr class="region" title="California"><?php _ex('CA','state','earthinnovate'); ?></abbr><br />

				<span class="postal-code">94111</span> <abbr class="country" title="United States of America"><?php _ex('USA', 'country', 'earthinnovate'); ?></abbr><br />

				<span class="">(415) 449-9900</span>

			</p>

		</address>



	</div>

	<?php endif; ?>

	<?php $post_thumbnail = get_the_post_thumbnail();

	if ('' != $post_thumbnail) : ?>

	<div class="hero-container">

		<?php /* display hero content */

		at_add_picturefill_img( );



		/* page pithy blurb */

		$blurb = get_post_meta($post->ID, '_at_page_blurb', true);

		if ($blurb !== '') : ?>

		<div class="blurb absolute-center">

			<h2><?php echo $blurb; ?></h2>

		</div>

		<?php elseif (($blurb == '') && (!is_page(336))) : ?>

		<h3 class="page-title absolute-center"><span><?php the_title();  ?></span></h3>

		<?php endif; ?>



		<?php if ($isContactPage) : ?>

		<div class="hq-details">

			<h4><?php _e('San Francisco Office', 'earthinnovate'); ?></h4>

			<address class="vcard">

				<p class="tel"><strong><?php _e('Phone', 'earthinnovate'); ?></strong><br />

					<span class="value">+1.415.449.9900</span>

				</p>

				<p class="tel"><span class="type"><strong><?php _e('Fax','earthinnovate'); ?></strong></span><br />

					<span class="value">+1.415.626.1775</span>

				</p>

				<p class="adr"><strong><?php _ex('Address', 'noun', 'earthinnovate'); ?></strong><br />

					<span class="street-address"><?php _e('3180 18th St, Suite 205', 'earthinnovate'); ?></span><br />

					<span class="locality"><?php _e('San Francisco','earthinnovate'); ?></span>, <abbr class="region" title="California"><?php _ex('CA','state','earthinnovate'); ?></abbr><br />

					<span class="postal-code">94110-3655</span> <abbr class="country" title="United States of America"><?php _ex('USA', 'country', 'earthinnovate'); ?></abbr>

				</p>

			</address>

		</div>

		<?php endif; // end contact us details ?>

	</div>

	<?php endif; // end featured image display ?>



	<div id="main-content">

		<?php if ( (!$isContactPage) && ('' == $post_thumbnail) || (('' != $post_thumbnail) && ($blurb != '') ) ) : ?>

		<h3 class="page-title"><span><?php the_title();  ?></span></h3>

		<?php endif; ?>



		<?php if ($isContactPage) : // add contact form

			$thumb_img_id = get_post_meta( 336, 'kd_thumbnail-image_page_id', true); ?>

		<section class="contact-form <?php if ($thumb_img_id) echo 'trans-center'; ?>">

			<?php $args = array(

				'img_id' => $thumb_img_id,

				'before' => '',

				'after' => ''

				);

			at_add_picturefill_img($args); ?>

			<h3><?php _e('Get in Touch', 'earthinnovate'); ?></h3>

			<?php echo do_shortcode('[contact-form-7 id="1452" title="Contact Us"]'); /* local id="511" */ ?>

		</section>

		<?php endif; // end contact form display ?>



		<?php if (is_page(302)): // if donate page ?>

			<section class="donate group section">

			<div class="col span_5_of_8">

				<?php the_content(); ?>

			</div>

			<div class="col span_3_of_8">

				<div class="text-center table-cell vert-middle a-box">

					<h5><?php _e('We Work Smart to <br />Make the Most of Your Money','earthinnovate'); ?></h5>

					<p class="text-left"><?php _e('Working through lean, agile regional teams means that the bulk of our funding goes straight to projects on the ground.','earthinnovate'); ?></p>

				</div>

				<figure><picture>

					<!--[if IE 9]><video style='display: none;'><![endif]-->

				<img srcset="http://earthinnovation.org/wp-content/uploads/2014/07/expenses-pie.png" alt="">

				</picture>

				</figure>

				

				<br />

				<div class="text-center table-cell vert-middle a-box">

					<h5><?php _ex("Other Ways to Give", 'donate page', 'earthinnovate'); ?></h5>

					<p style="text-align: center;"><?php _ex("Please <a href='mailto:fundraising@earthinnovation.org'>contact us</a> for information on making a legacy, stock or corporate matching gift.", 'donate page', 'earthinnovate'); ?></p>

				</div>

			</div>
		</section>
		<?php else : ?>

			<?php the_content(); ?>

		<?php endif; ?>


		<!-- end .donate section end donate page content-->
		

	

		<?php if (is_page(8883)): // Our Accountability (8698 on website) ?>

			<!-- -------------------- -->

			<section class="">
				<div class="content group section">						
				

					<!-- display publications -->
					<?php the_content(); ?>

					<div>
						<!-- 2017 year -->					
						<div class="facetwp-template group section" data-name="publications" style="text-align: center;">
							<hr>
							<br>
							<h3 class="page-title absolute-center" ><span>2017</span></h3>
							<article class="col span_1_of_5">
								<a class="max img-hover img" href="http://earthinnovation.org/wp-content/uploads/2018/12/eii annual report_2017_FINAL_online.pdf">
									<img width="373" height="480" src="http://earthinnovation.org/wp-content/uploads/2018/12/anualreport2017.jpg">			
								</a>
								<div class="excerpt">
									<div>			
										<h6 class="title"><a href="http://earthinnovation.org/wp-content/uploads/2018/12/eii annual report_2017_FINAL_online.pdf">2016-2017 Annual Report</a></h6>
										<h6 class="subheading">2016/17</h6>							
									</div>
								</div><!-- .excerpt -->		
							</article>
							<article class="col span_1_of_5">
								<a class="max img-hover img" href="http://earthinnovation.org/wp-content/uploads/2018/12/EII_Audited_Financial_Statements_2017.pdf">
									<img width="373" height="480" src="http://earthinnovation.org/wp-content/uploads/2018/12/Financial_Statment.jpg">			
								</a>
								<div class="excerpt">
									<div>			
										<h6 class="title"><a href="http://earthinnovation.org/wp-content/uploads/2018/12/EII_Audited_Financial_Statements_2017.pdf">2017 Audited Financial Statements</a></h6>
										<h6 class="subheading">2017</h6>							
									</div>
								</div><!-- .excerpt -->		
							</article>
							<article class="col span_1_of_5">
								<a class="max img-hover img" href="http://earthinnovation.org/wp-content/uploads/2018/12/EII_Tax_Return_2017_(Public_Disclosure_Copy).pdf">
									<img width="373" height="480" src="http://earthinnovation.org/wp-content/uploads/2018/12/2017-990.jpg">			
								</a>
								<div class="excerpt">
									<div>			
										<h6 class="title"><a href="http://earthinnovation.org/wp-content/uploads/2018/12/EII_Tax_Return_2017_(Public_Disclosure_Copy).pdf">2017 990</a></h6>
										<h6 class="subheading">2017</h6>							
									</div>
								</div><!-- .excerpt -->		
							</article>
						</div>
						<!-- 2016 year -->
						<div class="facetwp-template group section" data-name="publications"  style="text-align: center;">		<hr>
							<br>
							<h3 class="page-title absolute-center" ><span>2016</span></h3>		
							<!-- <article class="col span_1_of_5">
								<a class="max img-hover img" href="http://earthinnovation.org/wp-content/uploads/2015/12/eii-annual-report_2015-online.pdf">
									<img width="373" height="480" src="http://earthinnovation.org/wp-content/uploads/2018/12/anualreport2015.jpg">			
								</a>
								<div class="excerpt">
									<div>			
										<h6 class="title"><a href="http://earthinnovation.org/wp-content/uploads/2015/12/eii-annual-report_2015-online.pdf">2015 Annual Report</a></h6>
										<h6 class="subheading">2015</h6>							
									</div>
								</div>  	
							</article> -->
							<article class="col span_1_of_5">
								<a class="max img-hover img" href="http://earthinnovation.org/wp-content/uploads/2014/09/EII-Audited-Financial-Statements-063015.pdf">
									<img width="373" height="480" src="http://earthinnovation.org/wp-content/uploads/2018/12/Financial_Statment.jpg">			
								</a>
								<div class="excerpt">
									<div>			
										<h6 class="title"><a href="http://earthinnovation.org/wp-content/uploads/2019/01/EII-Audited-Financial-Statements-December-31-2016.pdf">2016 Audited Financial Statements</a></h6>
										<h6 class="subheading">2016</h6>							
									</div>
								</div><!-- .excerpt -->		
							</article>
							<article class="col span_1_of_5">
								<a class="max img-hover img" href="http://earthinnovation.org/wp-content/uploads/2019/01/EII - Form 990 Public Disclosure - Tax Year 2016 (FYE 12-31-2016).pdf">
									<img width="373" height="480" src="http://earthinnovation.org/wp-content/uploads/2019/01/2016-990.jpg">			
								</a>
								<div class="excerpt">
									<div>			
										<h6 class="title"><a href="http://earthinnovation.org/wp-content/uploads/2019/01/EII - Form 990 Public Disclosure - Tax Year 2016 (FYE 12-31-2016).pdf">2016 990</a></h6>
										<h6 class="subheading">2016</h6>							
									</div>
								</div>	
							</article>
						</div>
						<!-- 2015 year -->
						<div class="facetwp-template group section" data-name="publications"  style="text-align: center;">		<hr>
							<br>
							<h3 class="page-title absolute-center" ><span>2015</span></h3>		
							<article class="col span_1_of_5">
								<a class="max img-hover img" href="http://earthinnovation.org/wp-content/uploads/2015/12/eii-annual-report_2015-online.pdf">
									<img width="373" height="480" src="http://earthinnovation.org/wp-content/uploads/2018/12/anualreport2015.jpg">			
								</a>
								<div class="excerpt">
									<div>			
										<h6 class="title"><a href="http://earthinnovation.org/wp-content/uploads/2015/12/eii-annual-report_2015-online.pdf">2015 Annual Report</a></h6>
										<h6 class="subheading">2015</h6>							
									</div>
								</div><!-- .excerpt -->		
							</article>
							<article class="col span_1_of_5">
								<a class="max img-hover img" href="http://earthinnovation.org/wp-content/uploads/2014/09/EII-Audited-Financial-Statements-063015.pdf">
									<img width="373" height="480" src="http://earthinnovation.org/wp-content/uploads/2018/12/Financial_Statment.jpg">			
								</a>
								<div class="excerpt">
									<div>			
										<h6 class="title"><a href="http://earthinnovation.org/wp-content/uploads/2014/09/EII-Audited-Financial-Statements-063015.pdf">2015 Audited Financial Statements</a></h6>
										<h6 class="subheading">2015</h6>							
									</div>
								</div><!-- .excerpt -->		
							</article>
							<article class="col span_1_of_5">
								<a class="max img-hover img" href="http://earthinnovation.org/wp-content/uploads/2019/01/EII - Form 990 Public Disclosure - Tax Stub Year 2015 (FYE 12-31-2015).pdf">
									<img width="373" height="480" src="http://earthinnovation.org/wp-content/uploads/2019/01/2015-990.jpg">			
								</a>
								<div class="excerpt">
									<div>			
										<h6 class="title"><a href="http://earthinnovation.org/wp-content/uploads/2019/01/EII - Form 990 Public Disclosure - Tax Stub Year 2015 (FYE 12-31-2015).pdf">2015 990</a></h6>
										<h6 class="subheading">2015</h6>							
									</div>
								</div>	
							</article>
						</div>
						<!-- 2014 year -->
						<div class="facetwp-template group section" data-name="publications"  style="text-align: center;">
							<hr>
							<br>
							<h3 class="page-title absolute-center" ><span>2014</span></h3>
							<article class="col span_1_of_5">
								<a class="max img-hover img" href="http://earthinnovation.org/wp-content/uploads/2015/01/2013-2014_Annual_report-final.pdf">
									<img width="373" height="480" src="http://earthinnovation.org/wp-content/uploads/2018/12/anualreport2014.jpg">			
								</a>
								<div class="excerpt">
									<div>			
										<h6 class="title"><a href="http://earthinnovation.org/wp-content/uploads/2015/01/2013-2014_Annual_report-final.pdf">2014/13 Annual Report</a></h6>
										<h6 class="subheading">2014/13</h6>							
									</div>
								</div><!-- .excerpt -->		
							</article>
							<article class="col span_1_of_5">
								<a class="max img-hover img" href="http://earthinnovation.org/wp-content/uploads/2014/09/EII-Audited-Financial-Statements-June-30-2014.pdf">
									<img width="373" height="480" src="http://earthinnovation.org/wp-content/uploads/2018/12/Financial_Statment.jpg">			
								</a>
								<div class="excerpt">
									<div>			
										<h6 class="title"><a href="http://earthinnovation.org/wp-content/uploads/2014/09/EII-Audited-Financial-Statements-June-30-2014.pdf">2014 Audited Financial Statements</a></h6>
										<h6 class="subheading">2014</h6>							
									</div>
								</div><!-- .excerpt -->		
							</article>
							<article class="col span_1_of_5">
								<a class="max img-hover img" href="http://earthinnovation.org/wp-content/uploads/2019/01/EII - Form 990 Public Disclosure - Tax Year 2014 (FYE 06-30-2015).pdf">
									<img width="373" height="480" src="http://earthinnovation.org/wp-content/uploads/2018/12/2014-990.jpg">			
								</a>
								<div class="excerpt">
									<div>			
										<h6 class="title"><a href="http://earthinnovation.org/wp-content/uploads/2019/01/EII - Form 990 Public Disclosure - Tax Year 2014 (FYE 06-30-2015).pdf">2014-990</a></h6>
										<h6 class="subheading">2014</h6>							
									</div>
								</div><!-- .excerpt -->		
							</article>
						</div>
						<!-- 2013 year -->
						<div class="facetwp-template group section" data-name="publications"  style="text-align: center;">
							<hr>
							<br>
							<h3 class="page-title absolute-center" ><span>2013</span></h3>
							<article class="col span_1_of_5">
								<a class="max img-hover img" href="http://earthinnovation.org/wp-content/uploads/2014/09/EII-Form-990-063014-Public-Disclosure-Copy_web.pdf">
									<img width="373" height="480" src="http://earthinnovation.org/wp-content/uploads/2018/12/2013-990.jpg">			
								</a>
								<div class="excerpt">
									<div>			
										<h6 class="title"><a href="http://earthinnovation.org/wp-content/uploads/2014/09/EII-Form-990-063014-Public-Disclosure-Copy_web.pdf">2013-990</a></h6>
										<h6 class="subheading">2013</h6>							
									</div>
								</div><!-- .excerpt -->		
							</article>
						</div>
					</div><!-- end publication display -->
				</div>
			</section><!-- .pubs-container -->


			<!-- --------------- -->

		<?php else : ?>

			

		<?php endif; ?>  

		<!-- end Our Accountability page content --> 



		<?php if (is_page('Resources')) : ?>

			<div class="resource-container">

				<div class="group section">



					<?php $args = array(

						'sidebar' => false,

						'echo' => true

						);

					at_get_resource_types($args); ?>



				</div>

			</div>

		<?php endif; // end resources page display ?>



	</div><!--#main-content-->



<?php endwhile; endif;







get_footer(); ?>



