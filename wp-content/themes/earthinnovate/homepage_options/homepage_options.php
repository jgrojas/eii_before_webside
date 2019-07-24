<?php /*
*
* Creates a settings page on the backend to control
* posts featured on the home page
*
*/

/* ========================================================================================================================

	Homepage Featured Content

	======================================================================================================================== */

	/********* Register settings page (under appearance tab) *************/
	add_action('admin_menu', 'at_register_homepage_featured_page');
	function at_register_homepage_featured_page() {
		add_theme_page('Homepage Featured Content', 'Homepage Featured Content', 'edit_posts', 'homepage-featured', 'at_homepage_featured_form');
	}

	/********* Register settings and sections for the newly created page *********/
	add_action('admin_init', 'at_register_homepage_featured_settings');
	function at_register_homepage_featured_settings() {
		// register option which will hold all pertinent data
		register_setting('home_featured', 'home_featured_posts', 'at_home_featured_sanitize');
		/********** Dropdown-specific *************/
		add_settings_section('post_dropdowns', 'Choose Three Posts', 'at_post_dropdowns_callback', 'homepage-featured');
		// find all relevant posts to use for dropdown options (via wp_query)
		$args = array(
			'post_type' => array('at_publications', 'event', 'post', 'at_resources'),
			'posts_per_page' => -1,
			'orderby' => 'post_type title',
			'order' => 'ASC',
			'post_status' => 'publish'
			);
		$post_query = new WP_Query( $args );
		// collect id and title from wp_query above and input into an array
		$array = array();
		//dropdown-specific key/value pairs
		$dropdown_one = array('label_for' => 'homepage_post_one', 'box' => 'box1');
		$dropdown_two = array('label_for' => 'homepage_post_two', 'box' => 'box2');
		$dropdown_three = array('label_for' => 'homepage_post_three', 'box' => 'box3');
		// begin custom loop
		if ($post_query->have_posts()) : while ($post_query->have_posts()) : $post_query->the_post();
			global $post;
			// do some regex and input post type before title for easier visualization
			$post_type = $post->post_type;
			$post_type = str_replace('at_', '', $post_type); // remove prefix
			$post_type = ucwords($post_type); // capitalize
			$post_type = rtrim($post_type, 's');  // make singular
			// add this information to the array
			$array[$post->ID] = '('. $post_type .') '. $post->post_title;
		endwhile; endif; wp_reset_postdata(); // end loop

		// assign separate arrays based upon which dropdown it is
		$array1 = $dropdown_one + $array;
		$array2 = $dropdown_two + $array;
		$array3 = $dropdown_three + $array;

		// register dropdowns which contain all relevant posts
		add_settings_field('homepage_post_one', 'Post One', 'at_homepage_dropdown_callback', 'homepage-featured', 'post_dropdowns', $array1 );
		add_settings_field('homepage_post_two', 'Post Two', 'at_homepage_dropdown_callback', 'homepage-featured', 'post_dropdowns', $array2 );
		add_settings_field('homepage_post_three', 'Post Three', 'at_homepage_dropdown_callback', 'homepage-featured', 'post_dropdowns', $array3 );

		/************** Image-specific ************/
		// register_setting('home_imgs', 'home_img_ids', 'at_home_imgs_sanitize');
		add_settings_section('post_img_overwrites', 'Choose Displayed Images', 'at_img_overwrites_callback', 'homepage-featured');

		// create setting fields for img uploads
		add_settings_field('post_img_one', 'Featured Image One', 'at_choose_home_img', 'homepage-featured', 'post_img_overwrites', array('img' => 'img1', 'label_for' => 'post_img_one' ) );
		add_settings_field('post_img_two', 'Featured Image Two', 'at_choose_home_img', 'homepage-featured', 'post_img_overwrites',  array('img' => 'img2', 'label_for' => 'post_img_two') );
		add_settings_field('post_img_three', 'Featured Image Three', 'at_choose_home_img', 'homepage-featured', 'post_img_overwrites', array('img' => 'img3', 'label_for' => 'post_img_three') );
	}

	/********** Sanitize inputs based upon which button is clicked ***********/
	function at_home_featured_sanitize($input){
		$default_options = array(
			'box1' => false,
			'box2' => false,
			'box3' => false,
			'img1' => false,
			'img2' => false,
			'img3' => false
			);
		$valid_input = $default_options;

		$submit = ! empty($input['submit']) ? true : false;
		$reset = ! empty($input['reset']) ? true : false;

		foreach ($default_options as $type => $value){
			if (($submit) && ($input[$type])){
				$valid_input[$type] = (int) $input[$type];
			} elseif (($submit) && (!$input[$type]) && ('img' == substr($type,0,3))) {
				$number = substr($type, -1);
				$default_img = (int) get_post_thumbnail_id( (int) $input['box'. $number] );
				$valid_input[$type] = ($default_img ? $default_img : false );
			} elseif (($submit) && (!$input[$type]) && ('box' == substr($type,0,3))) {
				$valid_input[$type] = false;
			} elseif ($reset){
				$valid_input[$type] = false;
			}

		}

		return $valid_input;
	}

	/*********** Echos help text for the settings sections **********/
	// Post dropdowns
	function at_post_dropdowns_callback(){
		echo "Required: Choose a post from each dropdown menu below. Posts can be Publications, Events, Blog Posts or Resources. <br /><strong>IMPORTANT: Do not leave any of these blank</strong>.";
	}
	// Image uploads
	function at_img_overwrites_callback(){
		echo "Optional: Choose the image that will display for each post. By default, this image will be the 'featured image' from that post. <br /><strong>IMPORTANT: If any image is horizontally-aligned, please choose a vertically-aligned image instead</strong>.";
	}

	/******** Functions which display the form output **********/
	// Post dropdowns
	function at_homepage_dropdown_callback($args){
		// check if option exists in DB
		$option = (array) get_option('home_featured_posts');
		// pull value based upon the dropdown being called
		$value = ( $option[$args['box']] ? (int) $option[$args['box']] : '' );

		// start creating dropdown
		$dropdown = "<select id='". $args['label_for'] ."' name='home_featured_posts[". $args['box'] ."]'>\n\t";
		// input selected value based upon DB $value
		if ($value)
			$dropdown .= "<option selected value='". $value ."'>". get_the_title($value) ."</option>\n";
		else
			$dropdown .= "<option selected value=''>--</option>\n";
		// run through all posts from input array and add them to dropdown
		foreach ($args as $id => $title) :
			// make sure selected <option> isn't repeated twice
			if (($value !== $id) && ($id !== 'box') && ($id !== 'label_for'))
				$dropdown .= "<option value='". $id ."'>". $title ."</option>\n";
		endforeach;
			$dropdown .= "</select>\n";

		echo $dropdown;
	}
	// Image Uploads
	function at_choose_home_img( $args ){
		// get all post and image options
		$option = (array) get_option('home_featured_posts');

		// decide which image to select
		$img = $args['img'];

		if ( $option[ $img ] )
			$id = (int) $option[ $img ];
		else
			$id = '';


		$output = "<input type='hidden' id='". $args['label_for'] ."' name='home_featured_posts[". $img ."]' value='". $id ."' />\n";
		$output .= "<input id='upload_". $img ."_button' type='button' class='button upload_button' value='Choose Image' />\n";

		// If current image is the default, inform the user
		$number = substr($img, -1);
		$default_img = (int) get_post_thumbnail_id( (int) $option['box'. $number] );

		if (($id === $default_img) && ($default_img))
			$output .= "&nbsp&nbsp&nbsp<em>This is the default image for Post $number.</em>\n";

		// Get url for img preview
		$img_atts = wp_get_attachment_image_src( $id, 'medium');
		if ($img_atts)
			$output .= "<br /><br /><div class='img-preview'><img style='max-width: 480px; height: auto;' src='". esc_url($img_atts[0]) ."' /></div>\n";
		else
			$output .= "<p><em>No image selected</em></p>\n";

		echo $output;
	}

	/********** Load certain scripts and css files which incorporate wp media uploader ***********/
	add_action('admin_enqueue_scripts', 'at_homepage_admin_enqueue_scripts');
	function at_homepage_admin_enqueue_scripts() {
		if ( 'appearance_page_homepage-featured' == get_current_screen()->id ) {
			wp_enqueue_media();
			wp_enqueue_script('at-media-upload', get_template_directory_uri() .'/homepage_options/js/upload.js', array('jquery', 'media-upload', 'thickbox' ) , '1.0');
		}
 	}

	/******* Create html form structure for this page ***********/
	/*function at_homepage_featured_form() { ?>
		<div class="wrap">
			<h2>Homepage Featured Content</h2>
			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php // do sections
				settings_fields('home_featured');
				do_settings_sections('homepage-featured'); ?>

				<p class="submit">
					<input name="home_featured_posts[submit]" id="submit_options_form" type="submit" class="button-primary" value="<?php echo 'Save Changes'; ?>" />
					<input name="home_featured_posts[reset]" id="reset_options_form" type="submit" class="button-secondary" value="<?php echo 'Clear All'; ?>" />
				</p>

			</form>
		</div>
	<? }*/