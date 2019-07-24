<?php
/**
 * WPdonations_Taxonomies class.
 */
class WPdonations_Taxonomies {

	/**
	 * Constructor
	 */
	public function __construct() {
	
		add_action( 'donation_campaign_edit_form_fields', array( $this, 'edit_taxonomies_donation_campaign_fields' ), 10, 2 );
		add_action( 'donation_campaign_add_form_fields', array( $this, 'add_taxonomies_donation_campaign_fields' ), 10, 2 );
		add_action( 'create_donation_campaign', array( $this, 'save_taxonomies_donation_campaign_fields' ), 10, 2 );
		add_action( 'edited_donation_campaign', array( $this, 'save_taxonomies_donation_campaign_fields' ), 10, 2 );
		add_action( 'quick_edit_custom_box', array( $this, 'add_quick_edit_campaign_target' ), 10, 3);
		
		add_filter("manage_edit-donation_campaign_columns", array( $this, 'donation_campaign_columns') );
		add_filter("manage_donation_campaign_custom_column", array( $this, 'manage_donation_campaign_columns' ), 10, 3);
		add_filter("admin_footer", array( $this, 'quick_edit_javascript' ), 10, 3);

	}
	
	/**
	 * Add custom column to dontion_campaign taxonomy
	 *
	 * Adds a new column
	 *
	 * @access      private
	 * @since       1.0
	 * @return      void
	*/
	function donation_campaign_columns($theme_columns) {
	    $new_columns = array(
			'cb' 				=> '<input type  = "checkbox" />',
			'campaign_id'       => __('ID'),
			'name'              => __('Name'),
			'posts'             => __('Donations', 'wpdonations'),
			'target'            => __('Target', 'wpdonations'),
			'funds'             => __('Raised funds', 'wpdonations'),
			'raised_percent'     => __('% Raised', 'wpdonations')
	        );
	    return $new_columns;
	}
	
	/**
	 * Populate new taxonomy's columns
	 *
	 * Populate the campaign column
	 *
	 * @access      private
	 * @since       1.0
	 * @return      void
	*/
	function manage_donation_campaign_columns($out, $column_name, $term_id) {
		
	    switch ($column_name) {
	        case 'campaign_id': 
	            $out = $term_id;
	            break;
	        case 'target': 
	            $term_data = get_option( "wpdonations_taxonomy_$term_id", 'campaign_target' );
	            if( is_array($term_data) )
	            	if($term_data['campaign_target'] != '')
	            		$out = apply_filters( 'the_campaign_target', number_format( $term_data['campaign_target'], 2, '.', '' ) );
	            break;
	        case 'funds': 
	            $out = the_campaign_funds( '', '', false, $term_id);
	            break;
	        case 'raised_percent': 
	            $out = the_campaign_funds_raised_percentage( '', '%', false, $term_id );
	            break;
	 
	        default:
	            break;
	    }
	    return $out;    
	}
	
	/**
	 * Adds the "target" field to the campaign quick edit box
	 *
	 * Add target field
	 *
	 * @access      private
	 * @since       1.0
	 * @return      void
	*/
	function add_quick_edit_campaign_target($column_name, $screen, $tag)
	{ 
		if ( $column_name != 'target' || $tag != 'donation_campaign' )
			return;
		
	?>
	    <fieldset>
	        <div id="campaign-target-content" class="inline-edit-col">
	            <label>
	                <span class="title"><?php _e('Target', 'wpdonations'); ?></span>
	                <span class="input-text-wrap"><input type="text" name="term_meta[campaign_target]" class="ptitle target" value=""></span>
	            </label>
	        </div>
	    </fieldset>
	<?php 
	}
	
	/**
	 * Populates target fields dynamically using jQuery
	 *
	 * Populates taxo custom fields
	 *
	 * @access      private
	 * @since       1.0
	 * @return      void
	*/
	function quick_edit_javascript() {
	
		global $pagenow;
		
		if($pagenow == 'edit-tags.php' && (isset($_GET['taxonomy']) && $_GET['taxonomy'] == 'donation_campaign') && !isset($_GET['action'])) {
		?>
		<script type="text/javascript">

			jQuery(document).ready(function(){
			    //jQuery('.editinline').live('click', function(){
			    jQuery('.editinline').click( function(){
			    	
			        var tag_id = jQuery(this).parents('tr').attr('id');
			        var starget = jQuery('.target', '#'+tag_id).text();

			        jQuery(':input[name="term_meta[campaign_target]"]', '.inline-edit-row').val(starget);
			        //return false;
			    });
			});
		</script>
		<?php
		}
	}


	/**
	 * Edit Taxonomies Custom Fields page
	 *
	 * Add new fields to custom taxonomies edit page.
	 *
	 * @access      private
	 * @since       1.0
	 * @return      void
	*/
	function edit_taxonomies_donation_campaign_fields($tag) {
	
	    $t_id      = ( isset($tag) ? $tag->term_id : '' );
	    $term_meta = ( $t_id != '' ? get_option( "wpdonations_taxonomy_$t_id") : ''); 
	    ?>
	    <tr class="form-field">
	    <th scope="row" valign="top"><label for="donation_campaign_target"><?php _e( 'Campaign Target', 'wpdonations' ); ?></label></th>
	        <td>
	            <input type="text" name="term_meta[campaign_target]" id="term_meta[campaign_target]" value="<?php echo esc_attr( $term_meta['campaign_target'] ) ? esc_attr( $term_meta['campaign_target'] ) : ''; ?>">
	            <p class="description"><?php _e( 'Define the campaign target in your currency (ex: 5000).', 'wpdonations' ); ?></p>
	        </td>
	    </tr>
	<?php
	}
	
	/**
	 * Add Taxonomies Custom Fields
	 *
	 * Add new fields to custom taxonomies add page.
	 *
	 * @access      private
	 * @since       1.0
	 * @return      void
	*/
	function add_taxonomies_donation_campaign_fields() {
	
	    ?>
	    <div class="form-field">
	        <label for="campaign_target"><?php _e( 'Campaign Target', 'wpdonations' ); ?></label>
	        <input type="text" name="term_meta[campaign_target]" id="term_meta[campaign_target]" value="">
	        <p class="description"><?php _e( 'Define the campaign target in your currency (ex: 5000).', 'wpdonations' ); ?></p>
	    </div>
	<?php
	}
	
	/**
	 * Save Taxonomies Custom Fields
	 *
	 * Save custom taxonomies fields
	 *
	 * @access      private
	 * @since       1.0
	 * @return      void
	*/
	function save_taxonomies_donation_campaign_fields( $term_id ) {
	
	    if ( isset( $_POST['term_meta'] ) ) {
	    
	        $t_id      = $term_id;
	        $term_meta = get_option( "taxonomy_$t_id" );
	        $cat_keys  = array_keys( $_POST['term_meta'] );
	        
	        foreach ( $cat_keys as $key ) {
	            if ( isset ( $_POST['term_meta'][$key] ) ) {
	                $term_meta[$key] = $_POST['term_meta'][$key];
	            }
	        }
	        // Save the option array.
	        update_option( "wpdonations_taxonomy_$t_id", $term_meta );
	    } // end if
	} 

}

new WPdonations_Taxonomies();