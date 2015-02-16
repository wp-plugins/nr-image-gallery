<?php
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

$galleryName = '';
$galleryDescription = '';
$slug = '';
$gallerywidth = '';
$galleryheight = '';
$imageresize = '';
$animeffect = '';
$animspeed = '';
$slidercontrols = '';
$slidermarkers = '';
$slidercaptions = '';
$sliderresponsive = '';



$galleryAdded = false;

	if(isset($_POST['nri_add_gallery']))
	{
		if(check_admin_referer('wpnri_add_gallery','wpnri_add_gallery')) {
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

			global $wpdb;
			global $nr_image_gallery_table;

			$gallery = $wpdb->get_row( "SELECT * FROM $nr_image_gallery_table WHERE slug = '".$slug."'" );

			if (count($gallery) > 0) {
				$slug = $slug."-".count($gallery);
			}

			$galleryAdded = $wpdb->insert( $nr_image_gallery_table, array( 'name' => $galleryName, 'slug' => $slug, 'description' => $galleryDescription, 'gallerywidth' => $gallerywidth, 'galleryheight' => $galleryheight, 'imageresize' => $imageresize, 'animeffect' => $animeffect, 'animspeed' => $animspeed, 'slidercontrols' => $slidercontrols, 'slidermarkers' => $slidermarkers, 'slidercaptions' => $slidercaptions, 'sliderresponsive' => $sliderresponsive ) );

			if($galleryAdded) {
			  $gallery = $wpdb->get_row( "SELECT * FROM $nr_image_gallery_table WHERE slug = '".$slug."'" );
			?>
			<div class="updated"><p><strong><?php _e('Gallery Added.' ); ?></strong></p></div>
			<?php
			}
		  }
		  else {
			  ?>
			<div class="error"><p><strong><?php _e('Please enter a gallery name.' ); ?></strong></p></div>
			<?php
		  }
		}
	}
?>
<div class='wrap nr-image-gallery-admin'>
	<h2>NR Image Gallery - Add New Gallery</h2><br><br>
    <?php
	if($galleryAdded) {
	?>
    <div class="updated"><p>Copy and paste this code into the page or post that you would like to display the gallery.</p>
    <p><input type="text" name="galleryCode" value="[NRImageGallery id='<?php _e($gallery->Id); ?>']" size="40" readonly /></p></div>
    <?php } ?>
    <div style="Clear: both;"></div>
    <?php if(!$galleryAdded){ ?>
    <form name="nri_add_gallery_form" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>" method="post">
    <input type="hidden" name="nri_add_gallery" value="true" />
    <?php wp_nonce_field('wpnri_add_gallery','wpnri_add_gallery'); ?>
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
                <td><input type="text" size="30" name="galleryName" value="<?php _e($galleryName); ?>" /> (Please avoid non-letter characters such as ', ", *, etc.)</td>
            </tr>
            <tr>
            	<td><strong>Gallery Description:</strong></td>
                <td><textarea name="galleryDescription" cols="30"><?php _e($galleryDescription); ?></textarea></td>
            </tr>
            <tr>
            	<td><strong>Gallery Width:</strong></td>
                <td><input type="text" size="10" name="gallerywidth" value="<?php if(!$gallerywidth) echo "620"; else _e($gallerywidth); ?>" />px</td>
            </tr>
            <tr>
            	<td><strong>Gallery Height:</strong></td>
                <td><input type="text" size="10" name="galleryheight" value="<?php if(!$galleryheight) echo "320"; else _e($galleryheight); ?>" />px</td>
            </tr>
            <tr>
            	<td><strong>Resize gallery images to fit gallery size:</strong></td>
                <td><input type="checkbox" id="nri-imageresize" name="imageresize" <?php if($imageresize == 'true') echo "checked"; ?> value="<?php _e($imageresize); ?>" /></td>
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
                <td><select id="nri-animeffect" name="animeffect"><option value="fade" <?php if($animeffect == 'fade') echo "selected"; ?> >Fade</option><option value="slide" <?php if($animeffect == 'slide') echo "selected"; ?> >Slide</option></select></td>
            </tr>
            <tr>
            	<td><strong>Animation Speed:</strong></td>
                <td><input type="text" size="10" id="nri-animspeed" name="animspeed" value="<?php if(!$animspeed) echo "4000"; else _e($animspeed); ?>" />ms</td>
            </tr>
            <tr>
            	<td><strong>Show Controls:</strong></td>
                <td><input type="checkbox" id="nri-slidercontrols" name="slidercontrols" checked value="true" /></td>
            </tr>
            <tr>
            	<td><strong>Show Markers:</strong></td>
                <td><input type="checkbox" id="nri-slidermarkers" name="slidermarkers" checked value="true" /></td>
            </tr>
            <tr>
            	<td><strong>Show Captions:</strong></td>
                <td><input type="checkbox" id="nri-slidercaptions" name="slidercaptions" checked value="true" /></td>
            </tr>
             <tr>
            	<td><strong>Enable Responsive:</strong></td>
                <td><input type="checkbox" id="nri-sliderresponsive" name="sliderresponsive" checked value="true" /></td>
            </tr>
        </tbody>
	</table>
    <br /> <br />
    <input type="submit" name="Submit" class="button-primary" value="Add Gallery" />
    </form>
    <?php } ?>
<br />
</div>