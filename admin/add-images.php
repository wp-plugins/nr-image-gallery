<?php
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

global $wpdb;
global $nr_image_gallery_table;
global $nr_image_gallery_image_table;

$imageResults = null;

$galleryResults = $wpdb->get_results( "SELECT * FROM $nr_image_gallery_table" );

//Select gallery
if(isset($_POST['select_gallery']) || isset($_POST['galleryId'])) {
	if(check_admin_referer('wpnri_gallery','wpnri_gallery')) {
	  $gid = intval((isset($_POST['select_gallery'])) ? esc_sql($_POST['select_gallery']) : esc_sql($_POST['galleryId']));
	  $imageResults = $wpdb->get_results( "SELECT * FROM $nr_image_gallery_image_table WHERE gid = $gid ORDER BY sortOrder ASC" );
	  $gallery = $wpdb->get_row( "SELECT * FROM $nr_image_gallery_table WHERE Id = $gid" );
	}
}

//Add image
if(isset($_POST['galleryId']) && !isset($_POST['switch'])) {
	if(check_admin_referer('wpnri_gallery','wpnri_gallery')) {
	  $gid = intval($_POST['galleryId']);
	  $imagePath = esc_url($_POST['upload_image']);
	  $imageTitle = sanitize_text_field($_POST['image_title']);
	  $imageDescription = sanitize_text_field($_POST['image_description']);
	  $sortOrder = intval($_POST['image_sortOrder']);
	  $imageAdded = $wpdb->insert( $nr_image_gallery_image_table, array( 'gid' => $gid, 'imagePath' => $imagePath, 'title' => $imageTitle, 'description' => $imageDescription, 'sortOrder' => $sortOrder ) );

	  if($imageAdded) {
	  ?>
		  <div class="updated"><p><strong><?php _e('Image saved.' ); ?></strong></p></div>
	  <?php }
	  //Reload images
	  $imageResults = $wpdb->get_results( "SELECT * FROM $nr_image_gallery_image_table WHERE gid = $gid ORDER BY sortOrder ASC" );
	}
}

//Edit/Delete Images
if(isset($_POST['editing_images'])) {
	if(check_admin_referer('wpnri_gallery','wpnri_gallery')) {
		$editImageIds = $_POST['edit_imageId'];
        $imagePaths = $_POST['edit_imagePath'];
        for($san_index = 0; $san_index < count($imagePaths); $san_index++) $imagePaths[$san_index] = esc_url($imagePaths[$san_index]);
        $imageTitles = $_POST['edit_imageTitle'];
        for($san_index = 0; $san_index < count($imageTitles); $san_index++) $imageTitles[$san_index] = sanitize_text_field($imageTitles[$san_index]);
		$imageDescriptions = $_POST['edit_imageDescription'];
        for($san_index = 0; $san_index < count($imageDescriptions); $san_index++) $imageDescriptions[$san_index] = sanitize_text_field($imageDescriptions[$san_index]);
        $sortOrders = $_POST['edit_imageSort'];
		$imagesToDelete = isset($_POST['edit_imageDelete']) ? $_POST['edit_imageDelete'] : array();

		$i = 0;
		foreach($editImageIds as $editImageId) {
			if(in_array($editImageId, $imagesToDelete)) {
				$wpdb->query( "DELETE FROM $nr_image_gallery_image_table WHERE Id = '".$editImageId."'" );
				echo "Deleted: ".$imageTitles[$i];
			}
			else {
				$imageEdited = $wpdb->update( $nr_image_gallery_image_table, array( 'imagePath' => $imagePaths[$i], 'title' => $imageTitles[$i], 'description' => $imageDescriptions[$i], 'sortOrder' => $sortOrders[$i] ), array( 'Id' => $editImageId ) );
			}
			$i++;
		}
	  ?>
	  <div class="updated"><p><strong><?php _e('Images have been edited.' ); ?></strong></p></div>
	  <?php
	}
}
if(isset($_POST['editing_gid'])) {
	if(check_admin_referer('wpnri_gallery','wpnri_gallery')) {
	  $gid = intval($_POST['editing_gid']);
	  $imageResults = $wpdb->get_results( "SELECT * FROM $nr_image_gallery_image_table WHERE gid = $gid ORDER BY sortOrder ASC" );
	  $gallery = $wpdb->get_row( "SELECT * FROM $nr_image_gallery_table WHERE Id = $gid" );
	}
}

?>

<div class='wrap wp-nr-image-gallery-admin'>
	<h2>NR Image Gallery</h2>
    <p>Add new images to gallery</p>
	<?php if(!isset($_POST['select_gallery']) && !isset($_POST['galleryId']) && !isset($_POST['editing_images'])) { ?>
    <p>Select a galley</p>
    <form name="gallery" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
    	<?php wp_nonce_field('wpnri_gallery','wpnri_gallery'); ?>
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
    <?php } else if(isset($_POST['select_gallery']) || isset($_POST['galleryId']) || isset($_POST['editing_images'])) { ?>
    <h3>Gallery: <?php _e($gallery->name); ?></h3>

    <div style="Clear: both;"></div>

    <form name="add_image_form" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>" method="post">
    <input type="hidden" name="galleryId" value="<?php _e($gallery->Id); ?>" />
    <?php wp_nonce_field('wpnri_gallery','wpnri_gallery'); ?>
    <table class="widefat post fixed nri-table">
    	<thead>
        <tr>
            <th class="nri-cell-spacer-500">Image Src</th>
            <th class="nri-cell-spacer-300">Image Title</th>
            <th>Image Description</th>
            <th class="nri-cell-spacer-115"></th>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <th>Image Src</th>
            <th>Image Title</th>
            <th>Image Description</th>
            <th></th>
        </tr>
        </tfoot>
        <tbody>
        	<tr>
            	<td><input id="upload_image" type="text" size="36" name="upload_image" value="" />
					<input id="upload_image_button" type="button" value="Upload" /></td>
                <td><input type="text" name="image_title" size="50" value="" /></td>
                <td><textarea cols="50" name="image_description"></textarea></td>
                <td class="major-publishing-actions">
                <?php
                $imagesorts = $wpdb->get_results( "SELECT * FROM $nr_image_gallery_image_table ORDER BY sortOrder ASC" );
                $last_sort_index = 0;
                foreach($imagesorts as $imagesort) {
                    $last_sort_index = $imagesort->sortOrder;
                }
                $last_sort_index++;
                ?>
                <input type="hidden" name="image_sortOrder" size="10" value="<?php _e($last_sort_index); ?>" />
                <input type="submit" name="Submit" class="button-primary" value="Add Image" /></td>
            </tr>
        </tbody>
     </table>
     </form>
     <?php } ?>
     <?php
	 if(count($imageResults) > 0) {
	 ?>
     <br />
     <hr />
     <p>Edit existing images in this gallery</p>
     <form name="edit_image_form" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>" method="post">
    <table class="widefat post fixed nri-table">
    	<thead>
        <tr>
            <th class="nri-cell-spacer-80">Image Preview</th>
            <th class="nri-cell-spacer-200">Image Src</th>
            <th class="nri-cell-spacer-300">Image Title</th>
            <th>Image Description</th>
            <th class="nri-cell-spacer-115">Delete</th>
        </tr>
        </thead>
        <tbody id="gallerysort" class="ui-sortable">

<input type="hidden" name="editing_gid" value="<?php _e($gallery->Id); ?>" />
<input type="hidden" name="editing_images" value="true" />
<?php wp_nonce_field('wpnri_gallery', 'wpnri_gallery'); ?>
        	<?php foreach($imageResults as $image) { ?>
            <tr>
            	<td>

                    <a href="<?php _e($image->imagePath); ?>?TB_iframe=true" class="thickbox" style="cursor: pointer;"><img src="<?php _e($image->imagePath); ?>" width="75" height="75" alt="<?php _e($image->title); ?>" /></a><br /><i><?php _e('Click to preview', 'nr-image-gallery'); ?></i>
                </td>
                <td>
                	<input type="hidden" name="edit_gId[]" value="<?php _e($image->gid); ?>" />
					<input type="hidden" name="edit_imageId[]" value="<?php _e($image->Id); ?>" />
                    <input type="hidden" class="edit_imageSort" name="edit_imageSort[]" size="10" value="<?php _e($image->sortOrder); ?>" />
                    <input type="text" name="edit_imagePath[]" size="50" value="<?php _e($image->imagePath); ?>" />
                </td>
                <td><input type="text" name="edit_imageTitle[]" size="50" value="<?php _e($image->title); ?>" /></td>
                <td><textarea cols="50" name="edit_imageDescription[]"><?php _e($image->description); ?></textarea></td>
                <td><input type="checkbox" name="edit_imageDelete[]" value="<?php _e($image->Id); ?>" /></td>
            </tr>
			<?php } ?>
        </tbody>
     </table>
	 <p class="major-publishing-actions left-float nri-right-margin"><input type="submit" name="Submit" class="button-primary" value="Save Changes" /></p>
     </form>
	 <div style="clear:both;"></div>
     <?php } ?>

</div>