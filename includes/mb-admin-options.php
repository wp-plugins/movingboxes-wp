<?php

	/*******
	* Display the Admin section
	*
	* @package movingboxes-wp
	* 
	**/

	$administration = new AdminPanel;
	
	// Options form has been submitted and the nonce field is valid
	if($_POST['mb_form'] == 'Y' && wp_verify_nonce( $_POST['movingboxes_noncename'], plugin_basename( __FILE__ )) ) :
	 		
		// PROCESS FORM
		$mb_structure = $_POST['mb_structure'];
		update_option('mb_structure', $mb_structure);
				
		$mb_template_name = $_POST['mb_template'];
		update_option('mb_template', $mb_template_name);
		
		$start_panel = $_POST['start_panel'];
		update_option('mb_start_panel', $start_panel);
		
		$width = $_POST['width'];
		update_option('mb_width', $width);
		
		$panel_width = $_POST['panel_width'];
		update_option('mb_panel_width', $panel_width);
		
		$reduced_size = $_POST['reduced_size'];
		update_option('mb_reduced_size', $reduced_size);

		
		$fixed_height = $_POST['fixed_height'];
		update_option('mb_fixed_height', $fixed_height);
		
		
		$speed = $_POST['speed'];
		update_option('mb_speed', $speed);
		
		$hash_tags = $_POST['hash_tags'];
		update_option('mb_hash_tags', $hash_tags);
		
		$wrap = $_POST['wrap'];
		update_option('mb_wrap', $wrap);
		
		$show_nav = $_POST['show_nav'];
		update_option('mb_show_nav', $show_nav);
		
		$nav_format = $_POST['nav_format'];
		update_option('mb_nav_format', $nav_format);
		
		$easing = $_POST['easing'];
		update_option('mb_easing', $easing);
		
		$current_class = $_POST['current_class'];
		update_option('mb_current_class', $current_class);
		
		$tooltip = $_POST['tooltip'];
		update_option('mb_tooltip', $tooltip);
?>
	
	<div class="updated"><p><strong><?php _e('Options saved.' ); ?></strong></p></div>
		
<?php
	else : // GET CURRET ADMIN OPTIONS
		$mb_structure = get_option('mb_structure');
		$mb_template_name = get_option('mb_template');
		
		$start_panel = get_option('mb_start_panel');
		$width = get_option('mb_width');
		$panel_width = get_option('mb_panel_width');
		$reduced_size = get_option('mb_reduced_size');
		$fixed_height = get_option('mb_fixed_height');
		
		$speed = get_option('mb_speed');
		$hash_tags = get_option('mb_hash_tags');
		$wrap = get_option('mb_wrap');
		$show_nav = get_option('mb_show_nav');
		$nav_format = get_option('mb_nav_format');
		$easing = get_option('mb_easing');
		$current_class = get_option('mb_current_class');
		$tooltip = get_option('mb_tooltip');
	endif; 
?>

<div class="wrap">
	
	<?php  echo "<h2>" . __( 'Usage', 'mb_gallery' ) . "</h2>"; ?>
	<p>To add a MovingBoxes gallery to your WordPress site follow the following steps:</p>
	<ol>
	  <li>Upload your images using the <a href="media-new.php">media upload page</a>.</li>
	  <li>In the <a href="upload.php">media library</a> you can attach each image to the post or page you want the slider to be on.</li>
	  <li>In your post (or page) add the <code>[MovingBoxes]</code> shortcode.</li>
	  <li>Use the media gallery options (via "Add image" -&gt; Gallery) to order your images</li>
	</ol>
	
	<?php  echo "<h2>" . __( 'MovingBoxes Options', 'mb_gallery' ) . "</h2>"; ?>
	<p>Defaults shown in brackets.</p>
	<ul>
		<li><strong>Gallery Structure</strong> controls whether to display the gallery with divs or as a list (div).</li>
		<li><strong>Width</strong> overall width of the slider (800).</li>
		<li><strong>Start Panel</strong> which panel should be centered and enlarged to begin with (2).</li>
		<li><strong>Panel Width</strong> the size of the current (centered) panel as a proportion of the overrall width (0.55).</li>
		<li><strong>Reduced Size</strong> the reduced size of the panels as a proportion of the panel size (0.4).</li>
		<li><strong>Fixed Height</strong> ff true the height of the slider will be fixed to the height of the tallest panel (true).</li>
		<li><strong>Speed</strong> speed of the animation between images in milliseconds (800).</li>
		<li><strong>Hash Tags</strong> if true hash tags will be used allowing you to link to specific panels in the gallery (false).</li>
		<li><strong>Wrap Around</strong> whether the slider fast forwards/rewinds when the last or first image is reached (true).</li>
		<li><strong>Show Navigation</strong> if true navigation links are displayed below the gallery (true).</li>
		<li><strong>Navigation Format</strong> specify what should be used as the navigation. Defaults to numbered navigation.</li>
		<li><strong>Animation Easing</strong> linear or swing (linear).</li>
	</ul>
	<form name="webfirst_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
		<?php
		// Add nonce field to the form for security
		wp_nonce_field( plugin_basename( __FILE__ ), 'movingboxes_noncename' );
		?>
		<input type="hidden" name="mb_form" value="Y">
		<hr/>
		<h3><?php _e('Gallery Structure: ', 'mb_gallery'  ); ?></h3>
		<p><?php _e('Choose whether to output your gallery using divs or as an unordered list (ul): ', 'mb_gallery'  ); ?></p>
		<p><?php _e('Div: ', 'mb_gallery'  ); ?>
			<input type=radio group=structure value="div" <?php if($mb_structure=="div") echo "checked='checked'"; ?> name="mb_structure">
			<?php _e('List: ', 'mb_gallery'  ); ?>
			<input type=radio group=structure value="ul"
			<?php if($mb_structure=="ul") echo "checked='checked'"; ?> name="mb_structure">
			
		</p>
		
		<p>
			<label>Template: </label>
			<?php $administration->mb_display_templates($mb_template_name); ?>	
		</p>
		<hr/>
		<div>
			<span style="width:auto;margin-right:100px;vertical-align:top;display:inline-block">
				<h3><?php _e('Appearance: ', 'mb_gallery'  ); ?></h3>
				
				<p>
				<label for="start_panel"><?php _e('Start Panel: ', 'mb_gallery'  ); ?></label>
					<input type="text" name="start_panel" value="<?php echo $start_panel; ?>" size="2">
				</p>
				
				<p>
				<label for="width"><?php _e('Width: ', 'mb_gallery'  ); ?></label>
					<input type="text" name="width" value="<?php echo $width; ?>" size="2">
				</p>
				
				<p>
				<label for="panel_width"><?php _e('Panel Width: ', 'mb_gallery'  ); ?></label>
					<input type="text" name="panel_width" value="<?php echo $panel_width; ?>" size="2">
				</p>
				
				<p>
				<label for="reduced_size"><?php _e('Reduced Size: ', 'mb_gallery'  ); ?></label>
					<input type="text" name="reduced_size" value="<?php echo $reduced_size; ?>" size="2">
				</p>
				
				<p>
				<label for="fixed_height"><?php _e('Fixed Height: ', 'mb_gallery'  ); ?></label>
					<select name="fixed_height">
						<option value="1" <?php if($fixed_height==1) echo "selected"; ?>>True</option>
						<option value="0" <?php if($fixed_height==0) echo "selected"; ?>>False</option>
					
					</select>
				</p>
			</span>
			
			<span style="width:40%;vertical-align:top;display:inline-block">
				<h3><?php _e('Bahaviour: ', 'mb_gallery'  ); ?></h3>
				
				<p>
				<label for="speed"><?php _e('Speed: ', 'mb_gallery'  ); ?></label>
					<input type="text" name="speed" value="<?php echo $speed; ?>" size="2">
				</p>
				
				<p>
				<label for="hash_tags"><?php _e('Hash Tags: ', 'mb_gallery'  ); ?></label>
					<select name="hash_tags">
						<option value="true" <?php if($hash_tags=="true") echo "selected"; ?>>True</option>
						<option value="false" <?php if($hash_tags=="false") echo "selected"; ?>>False</option>
					</select>
				</p>
				
				<p>
				<label for="wrap"><?php _e('Wrap Around: ', 'mb_gallery'  ); ?></label>
					<select name="wrap">
						<option value="true" <?php if($wrap=="true") echo "selected"; ?>>True</option>
						<option value="false" <?php if($wrap=="false") echo "selected"; ?>>False</option>
					</select>
				</p>
				
				<p>
				<label for="show_nav"><?php _e('Show Navigation: ', 'mb_gallery'  ); ?></label>
					<select name="show_nav">
						<option value="true" <?php if($show_nav=="true") echo "selected"; ?>>True</option>
						<option value="false" <?php if($show_nav=="false") echo "selected"; ?>>False</option>
					</select>
				</p>
				
				<p>
				<label for="nav_format"><?php _e('Navigation Format: ', 'mb_gallery'  ); ?></label>
					<input type="text" name="nav_format" value="<?php echo $nav_format; ?>" > 
				</p>
				
				<p>
				<label for="easing"><?php _e('Animation Easing: ', 'mb_gallery'  ); ?></label>
					<select name="easing">
						<option value="linear" <?php if($easing=='linear') echo "selected"; ?>>Linear</option>
						<option value="swing" <?php if($easing=='swing') echo "selected"; ?>>Swing</option>
					</select>
				</p>
			</span>
		</div>
		<hr/>
		
		<p class="submit">
		<input type="submit" name="Submit" class="button-primary" value="<?php _e('Update Options', 'mb_gallery' ) ?>" />
		</p>
	</form>
</div>