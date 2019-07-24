<?php
/**
 * Registering meta boxes
 *
 * For more information, please visit: @link http://www.deluxeblogtips.com/meta-box/
 */

 	/* ==============================================================================================================

	 Custom Filter (via Meta Box plugin)

	 =============================================================================================================== */

	add_filter( 'rwmb_meta_boxes', 'at_register_publication_mbs' );
	add_filter('rwmb_meta_boxes', 'at_register_resource_mbs');
	add_filter('rwmb_meta_boxes', 'at_register_people_mbs');
	add_filter('rwmb_meta_boxes', 'at_register_page_mbs');
	add_filter('rwmb_meta_boxes', 'at_register_slide_mbs');
	

	/* ============================================================================================================

	Custom functions for adding meta boxes (via Meta Box plugin)

	============================================================================================================== */

	// register custom fields for publication pages
	function at_register_publication_mbs( $meta_boxes ){
		/**
		 * prefix of meta keys (optional)
		 * Use underscore (_) at the beginning to make keys hidden
		 * Alt.: You also can make prefix empty to disable it
		 */
		// Better has an underscore as last sign
		$prefix = '_at_pubs_';

		$meta_boxes[] = array(
			'id' => "{$prefix}basic",
			'title' => __( 'Other Information', 'meta-box' ),
			'pages' => array( 'at_publications' ),
			'context' => 'normal',
			'priority' => 'high',
			'autosave' => true,
			'fields' => array(
				// Publication attachment (file upload)
				array(
					'name' => __( 'Select/Upload Publication File', 'meta-box' ),
					'id'   => "{$prefix}upload_pub",
					'type' => 'file_advanced',
					'max_file_uploads' => 1
				),
				// External publication link (text)
				array(
					'name' => __('Link to External Publication','meta-box'),
					'id' => "{$prefix}external_link",
					'desc' => __('Use in place of an uploaded document', 'meta-box'),
					'type' => 'text',
					'size' => 40
				),
				// Summary (textarea)
				array(
					'name' => __( 'Summary', 'meta-box' ),
					'id'   => "{$prefix}summary",
					'desc' => __('Used as a subheading', 'meta-box'),
					'type' => 'textarea'
				),
				// Authors (textarea)
				array(
					'name'  => __( 'Author(s)', 'meta-box' ),
					'id'    => "{$prefix}authors",
					'desc'  => __( 'Use a comma-separated list for multiple authors', 'meta-box' ),
					'type'  => 'textarea'
				),
				// Citation (textarea)
				array(
					'name' => __( 'Citation', 'meta-box' ),
					'id'   => "{$prefix}citation",
					'type' => 'textarea'
				)
			),
			'validation' => array(
				'rules' => array(
					"{$prefix}password" => array(
						'required'  => true,
						'minlength' => 7,
					),
				),
				// optional override of default jquery.validate messages
				'messages' => array(
					"{$prefix}password" => array(
						'required'  => __( 'Password is required', 'meta-box' ),
						'minlength' => __( 'Password must be at least 7 characters', 'meta-box' ),
					),
				)
			)
		);

		return $meta_boxes;
	}

	// register custom fields for resource pages
	function at_register_resource_mbs( $meta_boxes ){
		/**
		 * prefix of meta keys (optional)
		 * Use underscore (_) at the beginning to make keys hidden
		 * Alt.: You also can make prefix empty to disable it
		 */
		// Better has an underscore as last sign
		$prefix = '_at_resource_';

		$meta_boxes[] = array(
			'id' => "{$prefix}fields",
			'title' => __( 'Additional Information', 'meta-box' ),
			'pages' => array( 'at_resources' ),
			'context' => 'normal',
			'priority' => 'high',
			'autosave' => true,
			'fields' => array(
				// Link to Image/document (text)
				array(
					'name' => __( wp_kses('Link to image/document file <br /><strong>(Not applicable for videos)</strong>', array('br' => array(), 'strong' => array())), 'meta-box'),
					'id'   => "{$prefix}href",
					'type' => 'text',
					'desc' => __( 'For internal links, please do not include the base path of the URL', 'meta-box') . ' i.e. "wp-content/uploads/2015/5/sample_file.pdf"',
					'size' => 40
				),
				// External link? (checkbox)
				array(
					'name' => __( wp_kses('External Link?<br /><strong>(Not applicable for videos)</strong>', array('br' => array(), 'strong' => array())), 'meta-box'),
					'id'   => "{$prefix}external",
					'type' => 'checkbox'
				),
				// Video embed (oembed)
				array(
					'name' => __( wp_kses('Embed URL <br /><strong>(Videos only)</strong>', array('br' => array(), 'strong' => array())), 'meta-box'),
					'id'   => "{$prefix}oembed",
					'type' => 'oembed'
				),
				// Map Source (text)
				array(
					'name' => __(wp_kses('Data Source <br /><strong>(Maps only)</strong>', array('br' => array(), 'strong' => array())), 'meta-box'),
					'id'   => "{$prefix}source",
					'type' => 'text',
					'size' => 40
				),
				// Presented at: (text)
				array(
					'name' => __( wp_kses('Event Name <br /><strong>(Presentations only)</strong>', array('br' => array(), 'strong' => array())), 'meta-box'),
					'id'   => "{$prefix}presentation",
					'type' => 'text',
					'size' => 40
				),
				// Location (text)
				array(
					'name' => __( wp_kses('Event Location <br /><strong>(Presentations only)</strong>', array('br' => array(), 'strong' => array())), 'meta-box'),
					'id'   => "{$prefix}venue",
					'type' => 'text',
					'size' => 40
				),
				// Date (date picker)
				array(
					'name' => __( wp_kses('Event Date <br /><strong>(Presentations only)</strong>', array('br' => array(), 'strong' => array())), 'meta-box'),
					'id'   => "{$prefix}date",
					'type' => 'date',
					'size' => 40,
					'js_options' => array( 'dateFormat' => 'MM d, yy' )
				)
			),
			'validation' => array(
				'rules' => array(
					"{$prefix}password" => array(
						'required'  => true,
						'minlength' => 7,
					),
				),
				// optional override of default jquery.validate messages
				'messages' => array(
					"{$prefix}password" => array(
						'required'  => __( 'Password is required', 'meta-box' ),
						'minlength' => __( 'Password must be at least 7 characters', 'meta-box' ),
					),
				)
			)
		);

		return $meta_boxes;
	}

	// register custom fields for board member/staff pages
	function at_register_people_mbs( $meta_boxes = null ){
		$prefix = '_at_people_';

		$meta_boxes[] = array(
			'id' => "{$prefix}credentials",
			'title' => __( 'Credentials', 'meta-box' ),
			'pages' => array( 'at_board_members', 'at_staff', 'at_advisory_council' ),
			'context' => 'normal',
			'priority' => 'high',
			'autosave' => true,
			'fields' => array(
				// Title/Position (text)
				array(
					'name' => __( 'Job Title/Position', 'meta-box' ),
					'id'   => "{$prefix}job_title",
					'type' => 'text'
				),
				// Resume/CV attachment (file upload)
				array(
					'name' => __( 'Attach CV/Resume', 'meta-box' ),
					'id'   => "{$prefix}resume",
					'type' => 'file_advanced',
					'max_file_uploads' => 1
				),
			),
			'validation' => array(
				'rules' => array(
					"{$prefix}password" => array(
						'required'  => true,
						'minlength' => 7,
					),
				),
				// optional override of default jquery.validate messages
				'messages' => array(
					"{$prefix}password" => array(
						'required'  => __( 'Password is required', 'meta-box' ),
						'minlength' => __( 'Password must be at least 7 characters', 'meta-box' ),
					),
				)
			)
		);

		return $meta_boxes;
	}

	function at_register_page_mbs($meta_boxes = null){
		$prefix = '_at_page_';

		$meta_boxes[] = array(
			'id' => "{$prefix}additional",
			'title' => __( 'Additional Fields', 'meta-box' ),
			'pages' => array( 'page' ),
			'context' => 'normal',
			'priority' => 'high',
			'autosave' => true,
			'fields' => array(
				// Case Studies (text)
				array(
					'name' => __( 'Relevant Case Studies', 'meta-box' ),
					'id'   => "{$prefix}case_studies",
					'type' => 'text',
					'desc' => __('Use a comma-separated list of IDs', 'meta-box')
				),
				// Related Work (text)
				array(
					'name' => __( 'Related Work', 'meta-box' ),
					'id'   => "{$prefix}related_work",
					'type' => 'text',
					'desc' => __('Use a comma-separated list of IDs', 'meta-box')
				),
				// Pithy Blurb (textarea)
				array(
					'name' => __( 'Pithy Blurb', 'meta-box' ),
					'id'   => "{$prefix}blurb",
					'type' => 'textarea',
					'rows' => 3
				),
				// Homepage Hero Image (image)
				array(
					'name' => __( 'Hero Image', 'meta-box' ),
					'id'   => "{$prefix}hero_image",
					'type' => 'image_advanced',
					'desc' => __('Hero Image - Only used on Homepage - 2000x810px', 'meta-box')
				),
				// Homepage Image 1 (image)
				array(
					'name' => __( 'Publications', 'meta-box' ),
					'id'   => "{$prefix}publications_image",
					'type' => 'image_advanced',
					'desc' => __('Only used on Homepage - 768x432px', 'meta-box')
				),
				// Homepage Image 2 (image)
				array(
					'name' => __( 'Resources', 'meta-box' ),
					'id'   => "{$prefix}resources_image",
					'type' => 'image_advanced',
					'desc' => __('Only used on Homepage - 768x432px', 'meta-box')
				),
				// Homepage Image 3 (image)
				array(
					'name' => __( 'Blog', 'meta-box' ),
					'id'   => "{$prefix}blog_image",
					'type' => 'image_advanced',
					'desc' => __('Only used on Homepage - 768x432px', 'meta-box')
				),
			),
			'validation' => array(
				'rules' => array(
					"{$prefix}password" => array(
						'required'  => true,
						'minlength' => 7,
					),
				),
				// optional override of default jquery.validate messages
				'messages' => array(
					"{$prefix}password" => array(
						'required'  => __( 'Password is required', 'meta-box' ),
						'minlength' => __( 'Password must be at least 7 characters', 'meta-box' ),
					),
				)
			)
		);

		return $meta_boxes;
	}

	// register custom fields for home slides cpt
	function at_register_slide_mbs( $meta_boxes = null ){
		$prefix = '_at_slide_';

		$meta_boxes[] = array(
			'id' => "{$prefix}link",
			'title' => __( 'URL', 'meta-box' ),
			'pages' => array( 'at_slides' ),
			'context' => 'normal',
			'priority' => 'low',
			'autosave' => true,
			'fields' => array(
				// Slide Link (text)
				array(
					'name' => home_url('/...'),
					'id'   => "{$prefix}href",
					'type' => 'text',
					'desc' => __('Please do not include the base path of the URL', 'meta-box'),
					'size' => 40
				)
			),
			'validation' => array(
				'rules' => array(
					"{$prefix}password" => array(
						'required'  => true,
						'minlength' => 7,
					),
				),
				// optional override of default jquery.validate messages
				'messages' => array(
					"{$prefix}password" => array(
						'required'  => __( 'Password is required', 'meta-box' ),
						'minlength' => __( 'Password must be at least 7 characters', 'meta-box' ),
					),
				)
			)
		);

		return $meta_boxes;
	}