<?php
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

global $wpdb;
global $nr_image_gallery_table;
global $nr_image_gallery_image_table;

$galleryResults = $wpdb->get_results( "SELECT * FROM $nr_image_gallery_table" );

//Select gallery
if(isset($_POST['select_gallery']) || isset($_POST['galleryId'])) {
	if(check_admin_referer('wpnri_select_gallery','wpnri_select_gallery')) {
	  $gid = intval((isset($_POST['select_gallery'])) ? esc_sql($_POST['select_gallery']) : esc_sql($_POST['galleryId']));
	  $imageResults = $wpdb->get_results( "SELECT * FROM $nr_image_gallery_image_table WHERE gid = $gid ORDER BY sortOrder ASC" );
	  $gallery = $wpdb->get_row( "SELECT * FROM $nr_image_gallery_table WHERE Id = $gid" );
	}
}

if(isset($_POST['nri_edit_gallery']))
{
	if(check_admin_referer('wpnri_edit_gallery','wpnri_edit_gallery')) {
	  if(sanitize_text_field($_POST['galleryName']) != "") {
		$galleryName = sanitize_text_field($_POST['galleryName']);
		$galleryDescription = sanitize_text_field($_POST['galleryDescription']);
		$slug = mb_convert_case(str_replace(" ", "", $_POST['galleryName']), MB_CASE_LOWER, "UTF-8");
		$gallerywidth = sanitize_text_field($_POST['gallerywidth']);
		$galleryheight = sanitize_text_field($_POST['galleryheight']);
        $imageresize = $_POST['imageresize'];
        $animeffect = $_POST['animeffect'];
         $animspeed = sanitize_text_field($_POST['animspeed']);
         $slidercontrols = $_POST['slidercontrols'];
         $slidermarkers = $_POST['slidermarkers'];
         $slidercaptions = $_POST['slidercaptions'];
         $sliderresponsive = $_POST['sliderresponsive'];

		if(isset($_POST['nri_edit_gallery'])) {
			$imageEdited = $wpdb->update( $nr_image_gallery_table, array( 'name' => $galleryName, 'slug' => $slug, 'description' => $galleryDescription, 'gallerywidth' => $gallerywidth, 'galleryheight' => $galleryheight, 'imageresize' => $imageresize, 'animeffect' => $animeffect, 'animspeed' => $animspeed, 'slidercontrols' => $slidercontrols, 'slidermarkers' => $slidermarkers, 'slidercaptions' => $slidercaptions, 'sliderresponsive' => $sliderresponsive ), array( 'Id' => intval($_POST['nri_edit_gallery']) ) );

				?>
				<div class="updated"><p><strong><?php _e('Gallery has been edited.' ); ?></strong></p></div>
				<?php
		}
	  }
	}
}
if(isset($_POST['nri_edit_gallery'])) {
	if(check_admin_referer('wpnri_edit_gallery','wpnri_edit_gallery')) {
	  $gid = intval($_POST['nri_edit_gallery']);
	  $imageResults = $wpdb->get_results( "SELECT * FROM $nr_image_gallery_image_table WHERE gid = $gid ORDER BY sortOrder ASC" );
	  $gallery = $wpdb->get_row( "SELECT * FROM $nr_image_gallery_table WHERE Id = $gid" );
	}
}
?>
<div class='wrap wp-nr-image-gallery-admin'>
	<h2>NR Image Gallery - Edit Gallery</h2>
    <?php if(!isset($_POST['select_gallery']) && !isset($_POST['galleryId']) && !isset($_POST['nri_edit_gallery'])) { ?>
    <p>Select a galley</p>
    <form name="gallery" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
    	<?php wp_nonce_field('wpnri_select_gallery','wpnri_select_gallery'); ?>
        <select name="select_gallery" onchange="gallery.submit()">
        	<option> - SELECT A GALLERY - </option>
			<?php
				foreach($galleryResults as $gallery) {
					?><option value="<?php _e($gallery->Id); ?>"><?php _e($gallery->name); ?></option>
                <?php
				}
			?>
        </select>
    </form>
    <?php } else if(isset($_POST['select_gallery']) || isset($_POST['galleryId']) || isset($_POST['nri_edit_gallery'])) { ?>
    <h3>Gallery: <?php _e($gallery->name); ?></h3>

    <p>This is where you can edit existing galleries.</p>

 <div style="Clear: both;"></div>
    <form name="nri_add_gallery_form" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>" method="post">
    <input type="hidden" name="nri_edit_gallery" value="<?php _e($gid); ?>" />
    <?php wp_nonce_field('wpnri_edit_gallery', 'wpnri_edit_gallery'); ?>
    <h3>Basic Settings</h3>
    <table class="widefat post fixed nri-table">
       	<thead>
        <tr>
        	<th class="nri-cell-spacer-250">Field Name</th>
            <th style="width: 100%">Field Input</th>

        </tr>
        </thead>
        <tfoot>
        <tr>
        	<th>Field Name</th>
            <th>Field Input</th>

        </tr>
        </tfoot>
        <tbody>

            <tr>
            	<td><strong>Gallery Name:</strong></td>
                <td><input type="text" size="30" name="galleryName" value="<?php _e($gallery->name); ?>" /> (Please avoid non-letter characters such as ', ", *, etc.)</td>
            </tr>
             <tr>
            	<td><strong>Gallery Description:</strong></td>
                <td>
                <textarea name="galleryDescription" cols="30"><?php _e($gallery->description) ?></textarea>
                </td>
            </tr>
             <tr>
            	<td><strong>Gallery Width:</strong></td>
                <td><input type="text" size="10" name="gallerywidth" value="<?php if(!$gallery->gallerywidth) echo "620"; else _e($gallery->gallerywidth); ?>" />px (default 620)</td>
            </tr>
            <tr>
            	<td><strong>Gallery Height:</strong></td>
                <td><input type="text" size="10" name="galleryheight" value="<?php if(!$gallery->galleryheight) echo "320"; else _e($gallery->galleryheight); ?>" />px (default 320)</td>
            </tr>
               <tr>
            	<td><strong>Resize gallery images to fit gallery size:</strong></td>
                <td><input type="checkbox" id="nri-imageresize" name="imageresize" <?php if($gallery->imageresize == 'true') echo "checked"; ?> value="<?php _e($gallery->imageresize); ?>" /></td>
            </tr>
        </tbody>
	</table>
    <h3 style="display: inline-block; margin-right: 10px;">Advanced Settings</h3> <a class="show-hide-nri-advanced" href="#">Show</a>
    <table class="widefat post fixed nri-table nri-advanced">
    	<thead>
        <tr>
        	<th class="nri-cell-spacer-250">Field Name</th>
            <th style="width: 100%">Field Input</th>

        </tr>
        </thead>
        <tfoot>
        <tr>
        	<th>Field Name</th>
            <th>Field Input</th>

        </tr>
        </tfoot>
        <tbody>
        	<tr>
            	<td><strong>Animation Effect:</strong></td>
                <td><select id="nri-animeffect" name="animeffect"><option value="fade" <?php if($gallery->animeffect == 'fade') echo "selected"; ?> >Fade</option><option value="slide" <?php if($gallery->animeffect == 'slide') echo "selected"; ?> >Slide</option></select></td>
            </tr>
            <tr>
            	<td><strong>Animation Speed:</strong></td>
                <td><input type="text" size="10" id="nri-animspeed" name="animspeed" value="<?php if(!$gallery->animspeed) echo "4000"; else _e($gallery->animspeed); ?>" />ms</td>
            </tr>
            <tr>
            	<td><strong>Show Controls:</strong></td>
                <td><input type="checkbox" id="nri-slidercontrols" name="slidercontrols" <?php if($gallery->slidercontrols == 'true') echo "checked"; ?> value="<?php _e($gallery->slidercontrols); ?>" /></td>
            </tr>
            <tr>
            	<td><strong>Show Markers:</strong></td>
                <td><input type="checkbox" id="nri-slidermarkers" name="slidermarkers" <?php if($gallery->slidermarkers == 'true') echo "checked"; ?> value="<?php _e($gallery->slidermarkers); ?>" /></td>
            </tr>
            <tr>
            	<td><strong>Show Captions:</strong></td>
                <td><input type="checkbox" id="nri-slidercaptions" name="slidercaptions" <?php if($gallery->slidercaptions == 'true') echo "checked"; ?> value="<?php _e($gallery->slidercaptions); ?>" /></td>
            </tr>
             <tr>
            	<td><strong>Enable Responsive:</strong></td>
                <td><input type="checkbox" id="nri-sliderresponsive" name="sliderresponsive" <?php if($gallery->sliderresponsive == 'true') echo "checked"; ?> value="<?php _e($gallery->sliderresponsive); ?>" /></td>
            </tr>
        </tbody>
	</table>
     <br /> <br />
    <input type="submit" name="Submit" class="button-primary" value="Save Changes" />
    </form>
    <?php } ?>

</div>