<?php
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

if (isset($_POST['defaultSettings'])) {
	if(check_admin_referer('nri_settings','nri_settings')) {
	  /* $temp_defaults = get_option('wp_nr_image_gallery_defaults');
	  $temp_defaults['hide_overlay'] = isset($_POST['hide_overlay']) ? $_POST['hide_overlay'] : 'false';
	  $temp_defaults['hide_social'] = isset($_POST['hide_social']) ? $_POST['hide_social'] : 'false';
	  $temp_defaults['use_default_style'] = isset($_POST['use_default_style']) ? $_POST['use_default_style'] : 'false'; */
	  $temp_defaults['custom_style'] = isset($_POST['custom_style']) ? $_POST['custom_style'] : '';
	  //$temp_defaults['drop_shadow'] = isset($_POST['drop_shadow']) ? $_POST['drop_shadow'] : 'false';

	  update_option('wp_nr_image_gallery_defaults', $temp_defaults);

	  ?>
	  <div class="updated"><p><strong>Options saved.</strong></p></div>
	  <?php
	}
}
$default_options = get_option('wp_nr_image_gallery_defaults');

?>
<div class='wrap nr-image-gallery-admin'>
	<h2>NR Image Gallery - Custom CSS</h2>
    <div style="Clear: both;"></div>
    <form name="save_default_settings" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">

    <?php wp_nonce_field('nri_settings','nri_settings'); ?>
    <br/><textarea name="custom_style" id="custom_style" rows="8" cols="50"><?php _e($default_options['custom_style']); ?></textarea>
    	<input type="hidden" name="defaultSettings" value="true" />
        <p><input type="submit" name="Submit" class="button-primary" value="Save" /></p>
    </form>

</div>