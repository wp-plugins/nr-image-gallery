<?php
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

global $wpdb;
global $nr_image_gallery_table;
global $nr_image_gallery_image_table;

if(isset($_POST['galleryId'])) {
	if(check_admin_referer('wpnri_delete_gallery','wpnri_delete_gallery')) {
	  $wpdb->query( "DELETE FROM $nr_image_gallery_table WHERE Id = '".intval($_POST['galleryId'])."'" );

	  ?>
	  <div class="updated"><p><strong><?php _e('Gallery has been deleted.' ); ?></strong></p></div>
	  <?php
	}
}

$galleryResults = $wpdb->get_results( "SELECT * FROM $nr_image_gallery_table" );

if (isset($_POST['defaultSettings'])) {
	if(check_admin_referer('wpnri_settings','wpnri_settings')) {
	  $temp_defaults = get_option('nri_gallery_defaults');
	  $temp_defaults['hide_social'] = isset($_POST['hide_social']) ? $_POST['hide_social'] : 'false';

	  update_option('nri_gallery_defaults', $temp_defaults);

	  ?>
	  <div class="updated"><p><strong><?php _e('Options saved.', 'nri-gallery'); ?></strong></p></div>
	  <?php
	}
}
$default_options = get_option('nri_gallery_defaults');
?>
<div class='wrap nr-image-gallery-admin'>
	<h2>NR Image Gallery - All Galleries</h2><br><br>
    <div style="Clear: both;"></div>
    <table class="widefat post fixed nri-table">
    	<thead>
        <tr>
        	<th>Gallery Name</th>
            <th>Gallery Short Code</th>
            <th>Description</th>
            <th>Images</th>
            <th class="nri-cell-spacer-235"></th>
        </tr>
        </thead>
        <tfoot>
        <tr>
        	<th>Gallery Name</th>
            <th>Gallery Short Code</th>
            <th>Description</th>
            <th>Images</th>
            <th></th>
        </tr>
        </tfoot>
        <tbody>
        	<?php foreach($galleryResults as $gallery) { ?>
            <?php $imageResults = $wpdb->get_results( "SELECT * FROM $nr_image_gallery_image_table WHERE gid = ".$gallery->Id." ORDER BY sortOrder ASC" );  ?>
            <tr>
            	<td><?php _e($gallery->name); ?></td>
                <td><input type="text" size="40" value="[NRImageGallery id='<?php _e($gallery->Id); ?>']" readonly /></td>
                <td><?php _e($gallery->description); ?></td>
                <td><?php echo count($imageResults); ?></td>
                <td class="major-publishing-actions">
                <form class="left-float" style="display: inline-block; margin-left: 10px;" name="gallery" method="post" action="?page=edit-gallery">
                <?php wp_nonce_field('wpnri_select_gallery','wpnri_select_gallery'); ?>
                <input type="hidden" name="select_gallery" value="<?php _e($gallery->Id); ?>" />
                <input type="submit" name="Submit" class="button-primary" value="Edit" />
                </form>
                <form class="left-float" style="display: inline-block; margin-left: 10px;" name="delete_gallery_<?php _e($gallery->Id); ?>" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
                	<input type="hidden" name="galleryId" value="<?php _e($gallery->Id); ?>" />
                    <?php wp_nonce_field('wpnri_delete_gallery', 'wpnri_delete_gallery'); ?>
                    <input type="submit" name="Submit" class="button-primary" value="Delete" />
                </form>
                <div class="clear"></div>
                </td>
            </tr>
			<?php } ?>
        </tbody>
     </table>
</div>