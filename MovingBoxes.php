<?php
/**
 * @package movingboxes-wp
 */
 
/*
Plugin Name: movingboxes-wp
Plugin URI: http://www.web-first.co.uk/wordpress/moving-boxes-wordpress-plugin/
Description: Take images from a WordPress post and build a MovingBoxes Slider from them.
Version: 0.4.2
Author: Jon Horner
License: GPL2

Moving Boxes by Chris Coyier http://css-tricks.com/moving-boxes/
Current Version Updated by Mottie: https://github.com/chriscoyier/MovingBoxes

Copyright 2011  Jon Horner  (contact@web-first.co.uk)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


// Define plugin constants
define('MB_VERSION', '2.1.4'); // Version number of MovingBoxes used
define('MB_WP_PLUGIN_VERSION', '0.4.2'); // Version number of this WordPress plugin
define('PLUGIN_NAME', 'MovingBoxes');
define('SHORTCODE_NAME', 'MovingBoxes');
define('CUSTOM_SHORTCODE_NAME', 'CustomMovingBoxes');
define('MB_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . str_replace( basename( __FILE__ ) , "" , plugin_basename(__FILE__) )); // Path to the Plugin folder

  
$gallery = new MovingBoxesSlider; // Instantiate the MovingBoxesSlider class


/* ADMIN SETTINGS */

// Add admin options to the WP settings menu 
add_action('admin_menu', 'movingboxes_admin_menu_init');
add_filter( 'plugin_row_meta', 'movingboxes_links', 10, 2);

function movingboxes_admin_menu_init() {
	$mb_admin_menu = add_options_page(PLUGIN_NAME.' Admin Options', PLUGIN_NAME, 'manage_options', PLUGIN_NAME, 'mb_options');
	
	// ADD THE ADMIN CSS ONLY ON THIS PAGE
	add_action('admin_print_styles-' . $mb_admin_menu, 'add_mb_admin_styles');
}

/**
* Adds links to the plugin on the plugins page.
*
* @param mixed $links
* @param mixed $file
*/
function movingboxes_links($links,$file) {
    $base = plugin_basename(__FILE__);
    if ($file == $base) {
        $links[] = '<a href="/wp-admin/options-general.php?page='.PLUGIN_NAME.'">' . __('Settings') . '</a>';
        $links[] = '<a href="http://www.web-first.co.uk/wordpress/moving-boxes-wordpress-plugin/">' . __('Support') . '</a>';
    }
    return $links;
}


// Function to display the settings on the admin page
function mb_options() {
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
	require_once('includes/mb-admin-options.php'); 
}

// Function to include  admin CSS
// Called from the init function so it only loads on the settings page
function add_mb_admin_styles() {
     wp_enqueue_style( 'mb-admin-style', plugins_url('/includes/css/admin.css', __FILE__), array(), MB_WP_PLUGIN_VERSION, 'screen' );
     
}



/* Add MovingBoxes ustom meta to posts and pages */
$myplugin = new MovingBoxesCustomMeta();
// Add meta box to edit post page
add_action( 'add_meta_boxes', array(&$myplugin, 'movingboxes_add_custom_box') );
// backwards compatible (before WP 3.0)
add_action( 'admin_init', array(&$myplugin, 'movingboxes_add_custom_box'), 1 );
// Do something with the data entered
add_action( 'save_post', array(&$myplugin, 'movingboxes_save_postdata') );

 

/* FRONT END FUNCTIONS */
	if(!is_admin()){ // Don't load this in the admin backend
	  add_action('init', 'register_mb_script'); // Register MovingBoxes JS
	  wp_enqueue_script('jquery','','','',true);
	  add_action('wp_footer', 'print_mb_script'); // Print the MovingBoxes JS in the footer
	  add_action('wp_print_styles', 'add_mb_styles'); // Add main CSS to head
	  add_action('wp_print_styles', 'add_mb_ie_styles'); // Add IE CSS to head-->
	  add_shortcode(SHORTCODE_NAME, "mb_gallery_shortcode_handler"); // Add the shortcode to WordPress	  
	}
	
	/**
	 * function add_mb_styles
	 *
	 * Enqueues the CSS from the current template selected in the admin panel
	 *
	 * @global $gallery
	 * @param string $template_name Name of current template
	 * @param string $plugin_directory Path to plugin's template folder
	 *
	 * @returns nothing
	 */
	function add_mb_styles() {
		global $gallery;
		// Retrieve the selected template name and load the CSS file from it
		$template_name = $gallery->templateName();
		$plugin_directory = basename(dirname(__FILE__));
	    wp_enqueue_style( 'mb-style', WP_PLUGIN_URL.'/'.$plugin_directory.'/templates/'.$template_name.'/css/movingboxes.css', array(), MB_WP_PLUGIN_VERSION, 'screen' );
	}
	
	/**
	 * function add_mb_ie_styles
	 *
	 * Enqueues the IE only CSS from the current template selected in the admin panel for
	 * browsers lower than IE9
	 *
	 * @global $gallery
	 * @global $wp_styles
	 * @param string $template_name Name of current template
	 * @param string $plugin_directory Path to plugin's template folder
	 *
	 * @returns nothing
	 */
	function add_mb_ie_styles() {
		global $gallery,$wp_styles;
		// Retrieve the selected template name and load the CSS file from it
		$template_name = $gallery->templateName();
		$plugin_directory = basename(dirname(__FILE__));
		wp_enqueue_style('mb-ie-style', WP_PLUGIN_URL.'/'.$plugin_directory.'/templates/'.$template_name.'/css/movingboxes-ie.css', array(), MB_WP_PLUGIN_VERSION, 'screen' );
		$wp_styles->add_data( 'mb-ie-style', 'conditional', 'lte IE 9' );
	}
	
	/**
	 * function register_mb_script
	 *
	 * Registers the Moving Boxes JavaScript plugin
	 *
	 * @returns nothing
	 */
	function register_mb_script() {
		wp_register_script('movingboxes-config', plugins_url('/includes/js/jquery.movingboxes.min.js', __FILE__), array('jquery'), MB_WP_PLUGIN_VERSION, true);
	}
	
	/**
	 * function print_mb_script
	 *
	 * Prints the script containing the gallery settings into the page rather than
	 * attaching it to the head or footer. This allows the script to be placed
	 * on the page only when the shortcode is on the page.
	 *
	 * @global $add_mb_script
	 * @param boolean $add_mb_script Controls whether to print the script or not
	 *
	 * @returns nothing
	 */
	function print_mb_script() { 
		global $add_mb_script;
		
	 	// Print the script only when the MB Shortcode is found
		if ( !$add_mb_script ) return;
		wp_print_scripts('movingboxes-config');
	}

/* SHORCODES */

/**
 * function mb_gallery_shortcode_handler
 * 
 * Build the slider from a shortcode. Add a variable to include the JavaScript
 * when the shortcode is used (http://scribu.net/wordpress/optimal-script-loading.html)
 *
 * @author Jon Horner
 *
 * @param $mb_html string, HTML output returned to the page
 * @param $add_mb_script boolean, tested for when attaching the main JavaScript to the footer
 * @param $incomingfrompost array, settings retrieved from the shortcode
 *
 * @return string
 */
function mb_gallery_shortcode_handler($atts, $content = null) {
	// Set variable to include JavaScript when the shortcode is on a page
	global $add_mb_script,$gallery;
	$add_mb_script = true;
  		
 	// Build the HTML for the gallery
	$mb_html = $gallery->mb_create_gallery_function();
  
 	return $mb_html; //send back text to replace shortcode in post
  
} //end mb_gallery_shortcode_handler


 /**
  * MovingBoxes Slider Class
  * 
  * Class to build the MovingBoxes code and extract attached images from the post
  *
  * @author Jon Horner
  *
  */
class MovingBoxesSlider {
	
	/**
	 * function templateName
	 *
	 * @author Jon Horner
	 * @param string $mb_template_name 
	 * @return string the name of the template selected in the admin options
	 *
	 */
	public function templateName() {
		(get_option('mb_template')) ? $mb_template_name = get_option('mb_template') : $mb_template_name = 'default';
		return $mb_template_name;
	}
	
	
	/**
	 * function mb_script_attach
	 *
	 * Builds the JavaScript which sets the options for the slider and which id to
	 * apply the slider to
	 * 
	 * @author Jon Horner
	 *
	 * @param array $options  Array passed to the function containing the user options for MovingBoxes
	 * @param array $mbOptions
	 * @param string $script The JavaScript to be returned
	 * @return string The JavaScript options to initialise the Moving Boxes Slider
	 */
	public function mb_script_attach($options = null, $selector = '#mb-gallery'){

		
		if(empty($options)): //Load default options if none given
			
			$options['slider_id'] = "mb-gallery";
			
			(get_option('mb_nav_format')) ? $options['navformat'] = "function(){ return '".get_option('mb_nav_format')."'; }" : $options['navformat'] = "";
			
			(get_option('mb_easing')) ? $options['easing'] = get_option('mb_easing') : $options['easing'] = 'linear';
			
			
			$mbOptions = array();
				
			// Start Panel
			(get_option('mb_start_panel')) ? $mbOptions['startPanel'] = get_option('mb_start_panel') : $mbOptions['startPanel'] = 2;

			// Slider width
			(get_option('mb_width')) ? $mbOptions['width'] = get_option('mb_width') : $mbOptions['width'] = 800;
			
			// Individual panel width in proportion to the gallery width (0.1-1)
			(get_option('mb_panel_width')) ? $mbOptions['panelWidth'] = get_option('mb_panel_width') : $mbOptions['panelWidth'] = 0.7;
			
			// The size of inactive panels as a proportion of the panel size
			(get_option('mb_reduced_size')) ? $mbOptions['reducedSize'] = get_option('mb_reduced_size') : $mbOptions['reducedSize'] = 0.2;
			
			// If the slider height should be fixed or not
			(get_option('mb_fixed_height')) ? $mbOptions['fixedHeight'] = get_option('mb_fixed_height') : $mbOptions['fixedHeight'] = true;
			
			// speed of the slider animation
			(get_option('mb_speed')) ? $mbOptions['speed'] = get_option('mb_speed') : $mbOptions['speed'] = 800;
			
			// whether to wrap around the image slider or not
			(get_option('mb_wrap')) ? $mbOptions['wrap'] = get_option('mb_wrap') : $mbOptions['wrap'] = true;
			
			// Whether to use hashtags or not
			(get_option('mb_hash_tags')) ? $mbOptions['hashTags'] = get_option('mb_hash_tags') : $mbOptions['hashTags'] = false;
			
			// Whether to display the navigation or not
			(get_option('mb_show_nav')) ? $mbOptions['buildNav'] = get_option('mb_show_nav') : $mbOptions['buildNav'] = true;
						
		else : // Load the options from the provided array
		
			$mbOptions = array(
				'startPanel' => $options['startpanel'],      // start with this panel
				'width'      => $options['width'],           // overall width of movingBoxes
				'panelWidth'  => $options['panelwidth'],     // current panel width adjusted to 50% of overall width
				'reducedSize' => $options['reducedsize'],    // non-current panel size: 80% of panel size
				'fixedHeight'  => $options['fixed'],     // if true, slider height set to max panel height; if false, slider height will auto adjust.
		
				// Behaviour
				'speed'       =>  $options['speed'],       // animation time in milliseconds
				'hashTags'    =>  $options['hash'],      // if true, hash tags are enabled
				'wrap'        =>  $options['wrap'],        // if true, the panel will "wrap" (it really rewinds/fast forwards) at the ends
				'buildNav'    =>  $options['nav']         // if true, navigation links will be added

			);
		
		endif ;
		
		//Put the options into a string
		$MBjsOptions = '';
		foreach($mbOptions as $key => $value)
			$MBjsOptions .= "        $key: $value,\n";
		
		if(!empty($options['navformat']))
			$MBjsOptions .= "        navFormatter: ".$options['navformat'].",\n";
			
		// End the options without a comma at the end to prevent an IE error
		$MBjsOptions .= "        easing: '" . $options['easing'] . "'\n";    
	  	
	  //Echo the script-section
	  $script ="
	  <script type='text/javascript'>

	    jQuery(function() {
	      jQuery('#".$options['slider_id']."').movingBoxes({
	      	".$MBjsOptions."
	      });
	    });
		
	</script>";
	
		return $script;
	}
	
	
	/**
	 * function mb_get_images_from_post
	 *
	 * Returns the images attached to a post 
	 *
	 * @param int $postId
	 * @param array $ids
	 * @param array $args
	 * @param array $attachments
	 * @depreciated depreciated in favour of getImageAttachmentData
	 * @return array
	 */
	function mb_get_images_from_post( $postId ){
		
	  $ids = array();
	  
	  $args = array(
	    'post_type' => 'attachment',
	    'post_parent' => $postId,
		'post_mime_type' => 'image',
		'orderby' => 'menu_order',
		'numberposts' => -1
	  );
	  $attachments = get_posts($args);
	  
	  if ($attachments) 
	    foreach ($attachments as $post) $ids[] = $post->ID;
	
	  return $ids;
	}
	
	
	
	/**
	 * Function mb_create_gallery_function
	 * 
	 * Build the HTML from the selected template and images attached to the post/page
	 *
	 * @author Jon Horner
	 *
	 * @global array $post The current WordPress post details
	 *
	 * @param string $mb_template_name Name of user selected template
	 * @param array $images List of images which are attached to the current post/page
	 * @param string $slider_html The HTML to create the slider
	 * @param string $mb_template The content of the imported template file
	 * @param string $mb_process_template Variable used to process the template
	 * @param string $mb_template_name Name of the template select in the user preferences
	 *
	 * @return string
	 */
	function mb_create_gallery_function($atts=null) {
		global $post;
		$mb_images_toshow = array();
		$images = array();
		$slider_html = '';
		(get_option('mb_template')) ? $mb_template_name = get_option('mb_template') : $mb_template_name = 'default';
		(get_option('mb_structure')) ? $mb_structure = get_option('mb_structure') : $mb_structure = 'div';
		
		// Collect images attached to the current post
		$images = $this->getImageAttachmentData( $post->ID, 'full', 0 );
		
		// If there are images produce the gallery
		if(!empty($images)) :
			// Check if all images should be used
			$mb_all_images = get_post_meta($post->ID, 'mb_all_images', true);
			
			// If attributes are not provided use the default div id if not use the user supplied id
			(empty($atts)) ? $slider_html = '<div id="mb-gallery">' : $slider_html = '<div id="'.$atts['slider_id'].'">';
			
			// Import images template file
			$mb_template = file_get_contents(MB_PLUGIN_DIR.'templates/'.$mb_template_name.'/template-'.$mb_structure.'.tpl.php');
			
			
			if( count($images)==1 ) : // if only one image is attached
				
				$slider_html .= $this->process_template($images, $mb_template);
				
				$slider_html .= '</div>';
				$slider_html .= $this->mb_script_attach($atts);
				
				return $slider_html;
			endif ;

			// if all attached images should be used output the HTML
			if($mb_all_images=='show_all') :
				// Replace placeholders in the template with the image info
				foreach($images as $image) :
					$slider_html .= $this->process_template($image, $mb_template);
				endforeach;
				
			else :
				// Only use the images selected from the post meta
				$mb_images_toshow = get_post_meta($post->ID, 'mb_active_images',true);
				if(!empty($mb_images_toshow)) :
					// Replace placeholders in the template with the image info
					foreach($images as $image) :
						if(in_array($image->ID.'_active', $mb_images_toshow))
							$slider_html .= $this->process_template($image, $mb_template);

					endforeach;
				endif ;
				
			endif ;
			 
			 $slider_html .= '</div>';
			 
			 // Add the JavaScript to initialise the slider	using the shortcode attibutes
			 $slider_html .= $this->mb_script_attach($atts);
		 else :
		 	//$slider_html = '<p>There doesn\'t appear to be any images attached to this post/page.</p>';
		 endif ;
		 
		 return $slider_html;
	}
	
	
	function process_template($images, $mb_template){
		$mb_process_template = str_replace('{image_src}', $images->properties['url'], $mb_template );
		$mb_process_template = str_replace('{image_alt}', $images->alt, $mb_process_template );
		$mb_process_template = str_replace('{image_title}', $images->title, $mb_process_template );
		$mb_process_template = str_replace('{image_description}', $images->caption, $mb_process_template );
		$mb_process_template = str_replace('{image_link}', $images->description, $mb_process_template );
				
		return $mb_process_template;
		
		
	}
	
	
	/**
	 * function getImageAttachmentData
	 *
	 * Retrieves the attachment data such as Title, Caption, Alt Text, Description for the specified post
	 *
	 * @author Jon Horner
	 *
	 * @param int $post_id the ID of the Post, Page, or Custom Post Type
	 * @param array $objMeta Contains the details of any attachments for the post
	 * @param String $size The desired image size, e.g. thumbnail, medium, large, full, or a  custom size
	 * @param array $attachments Contains the attachments for the post
	 * @param array $args Contains the criteria for retrieving any attachments
	 * @param array $props Contains the properties for any image attachments
 	 * @return stdClass If there is only one result, this method returns a generic
	 * stdClass object representing each of the image's properties, and an array if otherwise.
	 */
	function getImageAttachmentData( $post_id, $size = 'thumbnail', $count = 0 ){
		$objMeta = array();
		$meta;// (stdClass)
	
		$args = array(
			'numberposts' => $count,
			'post_parent' => $post_id,
			'post_type' => 'attachment',
			'nopaging' => false,
			'post_mime_type' => 'image',
			'order' => 'ASC', // change this to reverse the order
			'orderby' => 'menu_order ID', // select which type of sorting
			'post_status' => 'any'
		);
		
		$attachments = & get_children($args);
		
		if( $attachments )	{
			foreach( $attachments as $attachment ){
				$meta = new stdClass();
				$meta->ID = $attachment->ID;
				$meta->title = $attachment->post_title;
				$meta->caption = $attachment->post_excerpt;
				$meta->description = $attachment->post_content;
				$meta->link = wp_get_attachment_url($attachment->ID);
				$meta->alt = get_post_meta($attachment->ID, '_wp_attachment_image_alt', true);
				
				// Image properties
				$props = wp_get_attachment_image_src( $attachment->ID, $size, false );
				
				$meta->properties['url'] = $props[0];
				$meta->properties['width'] = $props[1];
				$meta->properties['height'] = $props[2];
				
				$objMeta[] = $meta;
			}
			
			return ( count( $attachments ) == 1 ) ? $meta : $objMeta;
		}
	}
	
} // end MovingBoxesSlider class

/**
  * MovingBoxesCustomMeta Class
  * 
  * Class to add custom meta to pages and posts
  *
  * @author Jon Horner
  *
  */
class MovingBoxesCustomMeta {

	/**
	 * Function movingboxes_add_custom_box
	 *
	 * Adds a box to the main column on the Post and Page edit screens
	 */
	function movingboxes_add_custom_box() {
	  
	  	// Add to pages
	    add_meta_box(
	        'movingboxes_sectionid',
	        __( 'Moving Boxes', 'mb_gallery' ), 
	        array(&$this, 'movingboxes_inner_custom_box'),
	        'page', 'advanced', 'high'
	    );
	    // Add to posts
	    add_meta_box(
	        'movingboxes_sectionid',
	        __( 'Moving Boxes', 'mb_gallery' ), 
	        array(&$this, 'movingboxes_inner_custom_box'),
	        'post', 'advanced', 'high'
	    );
	}
	
	/**
	 * movingboxes_inner_custom_box function.
	 * 
	 * Prints the box content
	 *
	 * @access public
	 * @param mixed $post
	 * @param string $mb_all_images Whether all images should be used or not
	 * @param array $mb_images List of image attachements to use
	 * @param array $images List of all attachements for current post
	 * @return void
	 */
	function movingboxes_inner_custom_box( $post ) {
		// Use nonce for verification
		wp_nonce_field( plugin_basename( __FILE__ ), 'movingboxes_noncename' );
		$mb_images = array();
		$images = array();
		$slider = new MovingBoxesSlider;
		
		$images = $slider->getImageAttachmentData($post->ID);
		
		
		if(!empty($images)) :
			// Get current post meta
			$mb_all_images = get_post_meta($post->ID, 'mb_all_images', true); // setting to specify if all images should be used
			
			// If all images option is not selected get list of images currently selected 
			if(empty($mb_all_images)) $mb_images = get_post_meta($post->ID, 'mb_active_images', true); 

			// Display the custom meta content
			echo $this->show_all_images_box($mb_all_images);
			echo $this->print_attachments($images, $mb_all_images, $mb_images);
			
		else :
			_e('You have no images attached to this post/page. You can upload and/or attach your images <a href="/wp-admin/upload.php">here</a>.', 'mb_gallery');
		endif ;
	}
	
	
	/**
	 * show_all_images_box function.
	 * 
	 * @access public
	 * @param mixed $mb_all_images
	 * @param string $html
	 * @return string
	 */
	function show_all_images_box($mb_all_images){
		$html = '<label for=mb_all_images >';
		$html.=	__("Use all attached images: ", 'mb_gallery' );
		$html.=	'</label> ';
		$html.=	'<input type=checkbox id=mb_all_images name=mb_all_images value=show_all ';
			if($mb_all_images=='show_all') $html.=	' checked="checked" ';
		$html.=	 '/>';
		
		return $html;
	}
	
	
	/**
	 * print_attachments function.
	 * 
	 * @access public
	 * @param mixed $images
	 * @param mixed $mb_all_images
	 * @param mixed $mb_images
	 * @param string html
	 * @return string
	 */
	function print_attachments($images, $mb_all_images, $mb_images){
		
		$html = '<p>';
		$html.=	__("Check all the images that you would like to appear in your gallery.", 'mb_gallery' );
		$html.= '</p>';
	
		if(count($images)==1) : // if there is only one image cast it as an array and process it
			// decide if the checkbox should be checked
			(in_array($images->ID.'_active',$mb_images)) ? $checked = ' checked="checked" ' : $checked = '';
			// get thumbnail
			$thumb = wp_get_attachment_image_src( $images->ID,'thumbnail', false );
			
			// The actual fields for data entry
			$html.= '<span style="width:auto;display:inline-block;margin:0 20px 10px 0"><img src="'.$thumb[0].'" width="80" height="80"/><br/>'; // show thumbnail
			$html.= '<label for=mb_active_images >';
			$html.= $images->title; // show image title
			$html.= '</label> ';
			$html.= '<input type=checkbox id=mb_active_images name=mb_active_images[] value="'.$images->ID.'_active" '.$checked.'/><br/>	</span>';
		
		else : // if there is more than one image loop through them
			foreach($images as $image) :
				// decide if the checkbox should be checked
				if(!empty($mb_images) && in_array($image->ID.'_active',$mb_images)) :
					$checked = ' checked="checked" ';
				else : $checked = '';
				endif ;
				// get thumbnail
				$thumb = wp_get_attachment_image_src( $image->ID,'thumbnail', false );
				
				// The actual fields for data entry
				$html.= '<span style="width:auto;display:inline-block;margin:0 20px 10px 0"><img src="'.$thumb[0].'" width="80" height="80"/><br/>'; // show thumbnail
				$html.= '<label for=mb_active_images >';
			    $html.= $image->title; // show image title
				$html.= '</label> ';
				$html.= '<input type=checkbox id=mb_active_images name=mb_active_images[] value="'.$image->ID.'_active" '.$checked.'/><br/>	</span>';
			endforeach ;
			
		endif ;
	
		return $html;
	}
	
	/**
	 * Function movingboxes_save_postdata
	 *
	 * When the post is saved, saves our custom data
	 */
	function movingboxes_save_postdata( $post_id ) {
	  // verify if this is an auto save routine. 
	  // If it is our form has not been submitted, so we dont want to do anything
	  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
	      return;
	
	  // verify this came from the our screen and with proper authorization,
	  // because save_post can be triggered at other times
	
	  if ( !wp_verify_nonce( $_POST['movingboxes_noncename'], plugin_basename( __FILE__ ) ) )
	      return;
	
	  
	  // Check permissions
	  if ( 'page' == $_POST['post_type'] ) 
	  {
	    if ( !current_user_can( 'edit_page', $post_id ) )
	        return;
	  }
	  else
	  {
	    if ( !current_user_can( 'edit_post', $post_id ) )
	        return;
	  }
	
	  $mydata = $_POST['mb_active_images'];
	  $allimg = $_POST['mb_all_images'];
		
	  update_post_meta($post_id, 'mb_active_images', $mydata);
	  update_post_meta($post_id, 'mb_all_images', $allimg);
	}

}



/**
 * MovingBoxes Plugin Admin Class
 * 
 *
 * @author Jon Horner
 * 
 */
class AdminPanel{
	
	/**
	 * function mb_display_templates
	 *
	 * Build HTML for a select list for choosing a template in the admin panel.
	 * 
	 * @param array $templates Holds an array of any installed templates
	 * @param string $tempSelect Contains HTML for the selectbox
	 * @param string $selected Controls which select option should be selected by default
	 * @param string $template Name of each template
	 * @return nothing
	 */
	function mb_display_templates($current_template){
		$templates = $this->mb_get_templates();
		//$option =  "<option value=\"%s\">%s</option>\n";
		
				  
		//$tempSelect = '<select id="mb_template" name="mb_options[template]" size="1">';
		$tempSelect = '<select id="mb_template" name="mb_template" size="1">';
		
		// Add an option for each template
		foreach($templates as $template) :
			($current_template==$template) ? $selected =  'selected' : $selected =  '';
			$tempSelect .= "<option value='".$template."' ".$selected.">".$template."</option>\n";
		endforeach;
			
		$tempSelect .= '</select>';
				
		echo $tempSelect;
	}
	
	/**
	 * function mb_get_templates
	 *
	 * Gets a list of all templates within the templates folder
	 * 
	 * @param array $templates List of all templates
	 * @param string $templateDir The path to the plugins template folder
	 * @param array $ignore List of files to ignore when getting the template list
	 *
	 * @return array
	 */
	function mb_get_templates(){
		$templates = array();
		$templateDir = dirname(__FILE__) . '/templates';
		$dir = opendir($templateDir);
		$ignore = array(".", "..", "index.html", 'readme.txt');
		
		// Add subfolders to array
		// Each subfolder should be a template
		if($dir) while (false !== ($subdir = readdir($dir))) {
			if( !in_array($subdir, $ignore) ) $templates[] = $subdir;
		}

		return $templates;
	}
}

?>