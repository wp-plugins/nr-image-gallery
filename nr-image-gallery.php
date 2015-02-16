<?php
	/*
	Plugin Name: NR Image Gallery
	Plugin URI: http://nazmurrahman.com/nr-image-gallery-wordpress-plugin/
	Description: Simple responsive image gallery
	Author: Nazmur Rahman
	Version: 1.0
	Author URI: http://nazmurrahman.com/
	*/

	/* ==================================================================================
	 * Create custom database table
	 * ==================================================================================
	 */

	global $wpdb;
	global $nr_image_gallery_table;
	global $nr_image_gallery_image_table;
	global $nr_image_gallery_db_version;
	$nr_image_gallery_table = $wpdb->prefix . 'nr_image_gallery';
	$nr_image_gallery_image_table = $wpdb->prefix . 'nr_image_gallery_images';
	$nr_image_gallery_db_version = '1.0';

	register_activation_hook( __FILE__,  'nr_image_gallery_install' );

	function nr_image_gallery_install() {
	  global $wpdb;
	  global $nr_image_gallery_table;
	  global $nr_image_gallery_image_table;
	  global $nr_image_gallery_db_version;

	  if ( $wpdb->get_var( "show tables like '$nr_image_gallery_table'" ) != $nr_image_gallery_table ) {

		$sql = "CREATE TABLE $nr_image_gallery_table (".
			"Id INT NOT NULL AUTO_INCREMENT, ".
			"name VARCHAR( 30 ) NOT NULL, ".
			"slug VARCHAR( 30 ) NOT NULL, ".
			"description TEXT NOT NULL, ".
			"gallerywidth INT, ".
			"galleryheight INT, ".
            "imageresize VARCHAR( 10 ) NOT NULL, ".
            "animeffect VARCHAR( 10 ) NOT NULL, ".
            "animspeed INT, ".
            "slidercontrols VARCHAR( 10 ) NOT NULL, ".
            "slidermarkers VARCHAR( 10 ) NOT NULL, ".
            "slidercaptions VARCHAR( 10 ) NOT NULL, ".
            "sliderresponsive VARCHAR( 10 ) NOT NULL, ".
			"PRIMARY KEY Id (Id) ".
			")";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta($sql);

		$sql = "CREATE TABLE $nr_image_gallery_image_table (".
				"Id INT NOT NULL AUTO_INCREMENT, ".
				"gid INT NOT NULL, ".
				"imagePath LONGTEXT NOT NULL, ".
				"title VARCHAR( 50 ) NOT NULL, ".
				"description LONGTEXT NOT NULL, ".
				"sortOrder INT NOT NULL, ".
				"PRIMARY KEY Id (Id) ".
				")";


		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

		add_option( "nr_image_gallery_db_version", $nr_image_gallery_db_version );
	  }
	}

	/* ==================================================================================
	 * Include JS File in Header
	 * ==================================================================================
	 */

	 function define_options() {
		 if(!get_option('wp_nr_image_gallery_defaults')) {
				$gallery_options = array(
                /*
                'version'		   => 'free',
					'thumbnail_width'  => 'auto',
					'thumbnail_height' => 'auto',
					'hide_overlay'	   => 'false',
					'hide_social'	   => 'false',*/
					'custom_style'	   => ''
					//'use_default_style'=> 'true'
				);

				add_option('wp_nr_image_gallery_defaults', $gallery_options);
			}
			else {
				$wpNRImageGalleryOptions	= get_option('wp_nr_image_gallery_defaults');
				$keys = array_keys($wpNRImageGalleryOptions);
                /*
				if (!in_array('version', $keys)) {
					$wpNRImageGalleryOptions['version'] = $this->plugin_version;
				}
				if (!in_array('hide_overlay', $keys)) {
					$wpNRImageGalleryOptions['hide_overlay'] = "false";
				}
				if (!in_array('hide_social', $keys)) {
					$wpNRImageGalleryOptions['hide_social'] = "false";
				}
                */
				if (!in_array('custom_style', $keys)) {
					$wpNRImageGalleryOptions['custom_style'] = "";
				}
                /*
				if (!in_array('use_default_style', $keys)) {
					$wpNRImageGalleryOptions['use_default_style'] = "true";
				}
				if (!in_array('thumbnail_height', $keys)) {
					$wpNRImageGalleryOptions['thumbnail_height'] = $wpNRImageGalleryOptions['thunbnail_height'];
					unset($wpNRImageGalleryOptions['thunbnail_height']);
				}
                */
				update_option('wp_nr_image_gallery_defaults', $wpNRImageGalleryOptions);
			}
	 }
	 add_action('init', 'define_options');

	 function wp_custom_style() {
		$styles = get_option('wp_nr_image_gallery_defaults');
		echo "<style id='nr-image-gallery-custom-style'>".$styles['custom_style']."</style>";
	}
	add_action('wp_head', 'wp_custom_style');

	function attach_NRImageGallery_scripts() {
		$wpNRImageGalleryOptions = get_option('wp_nr_image_gallery_defaults');
         wp_enqueue_script('jquery');
		wp_register_script('bjqs', WP_PLUGIN_URL.'/nr-image-gallery/js/bjqs-1.3.min.js', array('jquery'));
		wp_register_script('NRImageGalleryLoader', WP_PLUGIN_URL.'/nr-image-gallery/js/nr-image-gallery.js', array('bjqs', 'jquery'));
        wp_enqueue_script('bjqs');
		wp_enqueue_script('NRImageGalleryLoader');
		wp_register_style( 'bjqs_stylesheet', WP_PLUGIN_URL.'/nr-image-gallery/css/bjqs.css');
		wp_enqueue_style('bjqs_stylesheet');
			wp_register_style('nr-image-gallery-style', WP_PLUGIN_URL.'/nr-image-gallery/css/nr-image-gallery.css');
	  		wp_enqueue_style('nr-image-gallery-style');

	}

	add_action('wp_enqueue_scripts', 'attach_NRImageGallery_scripts');

	function attach_NR_Image_Gallery_JS()
	{
		if ( ! defined( 'NRIGALLERY_PLUGIN_BASENAME' ) )
		define( 'NRIGALLERY_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

		if ( ! defined( 'NRIGALLERY_PLUGIN_NAME' ) )
			define( 'NRIGALLERY_PLUGIN_NAME', trim( dirname( NRIGALLERY_PLUGIN_NAME ), '/' ) );

		if ( ! defined( 'NRIGALLERY_PLUGIN_DIR' ) )
			define( 'NRIGALLERY_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . NRIGALLERY_PLUGIN_NAME );
	}

	add_action ('wp_head', 'attach_NR_Image_Gallery_JS');

	function nr_image_gallery_admin_scripts() {
     wp_enqueue_script('jquery');

	  wp_enqueue_script('media-upload');
      wp_enqueue_script('thickbox');
	  wp_register_script('nr-image-gallery-uploader', WP_PLUGIN_URL.'/nr-image-gallery/js/image-uploader.js', array('jquery','media-upload','thickbox'));
	  wp_enqueue_script('nr-image-gallery-uploader');
      wp_enqueue_script('jquery-ui-sortable');
      wp_register_script('NRImageGalleryLoaderAdmin', WP_PLUGIN_URL.'/nr-image-gallery/js/nr-image-gallery-admin.js', array('thickbox', 'jquery-ui-sortable'));
      wp_enqueue_script('NRImageGalleryLoaderAdmin');
	}

	function nr_image_gallery_admin_styles() {
         wp_enqueue_style('thickbox.css', '/'.WPINC.'/js/thickbox/thickbox.css', null, '1.0');
	}

	if (isset($_GET['page']) && ($_GET['page'] == 'add-gallery' || $_GET['page'] == 'add-images' || $_GET['page'] == 'edit-gallery')) {
	  add_action('admin_enqueue_scripts', 'nr_image_gallery_admin_scripts');
	  add_action('admin_print_styles', 'nr_image_gallery_admin_styles');
	}

	// Create Admin Panel
	function add_nri_menu()
	{
		add_menu_page(__('NR Image Gallery','menu-nri'), __('NR Image Gallery','menu-nri'), 'manage_options', 'nri-admin', 'showNRIMenu' );

		// Add a submenu to the custom top-level menu:
		add_submenu_page('nri-admin', __('NR Image Gallery >> Add Gallery','menu-nri'), __('Add Gallery','menu-nri'), 'manage_options', 'add-gallery', 'add_gallery');

		// Add a submenu to the custom top-level menu:
		add_submenu_page('nri-admin', __('NR Image Gallery >> Edit Gallery','menu-nri'), __('Edit Gallery','menu-nri'), 'manage_options', 'edit-gallery', 'edit_gallery');

		// Add a second submenu to the custom top-level menu:
		add_submenu_page('nri-admin', __('NR Image Gallery >> Add Images','menu-nri'), __('Add Images','menu-nri'), 'manage_options', 'add-images', 'add_images');

		// Add a second submenu to the custom top-level menu:
		add_submenu_page('nri-admin', __('NR Image Gallery >> Custom CSS','menu-nri'), __('Custom CSS','menu-nri'), 'manage_options', 'custom-css', 'customcss');

		wp_register_style('nr-image-gallery-admin-style', WP_PLUGIN_URL.'/nr-image-gallery/css/nr-image-gallery-admin.css');
	  	wp_enqueue_style('nr-image-gallery-admin-style');

	}

	add_action( 'admin_menu', 'add_nri_menu' );

	function showNRIMenu()
	{
		include("admin/overview.php");
	}

	function add_gallery()
	{
		include("admin/add-gallery.php");
	}

	function edit_gallery()
	{
		include("admin/edit-gallery.php");
	}

	function add_images()
	{
		include("admin/add-images.php");
		attach_NRImageGallery_scripts();
	}

	function customcss()
	{
		include("admin/custom-css.php");
	}


	/* ==================================================================================
	 * Gallery Creation Filter
	 * ==================================================================================
	 */

	// function creates the gallery
	function createNRImageGallery($galleryName, $id)
	{
		global $wpdb;
		global $nr_image_gallery_table;
		global $nr_image_gallery_image_table;

		if ($id != "-1") {
			$gallery = $wpdb->get_row( "SELECT * FROM $nr_image_gallery_table WHERE Id = '$id'" );
		}
		else {
			$gallery = $wpdb->get_row( "SELECT * FROM $nr_image_gallery_table WHERE slug = '$galleryName'" );
		}
		$imageResults = $wpdb->get_results( "SELECT * FROM $nr_image_gallery_image_table WHERE gid = $gallery->Id ORDER BY sortOrder ASC" );

		$images = array();
		$descriptions = array();
		$titles = array();
		$i = 0;
        $gallery_code = "";
        $gallery_code .= "<div id='nrig-$id'><ul class='bjqs'>";
		foreach($imageResults as $image)
		{
			$images[$i] = "'".$image->imagePath."'";
			$descriptions[$i] = "'".$image->description."'";
			$titles[$i] = "'".$image->title."'";
			$i++;
        if($gallery->imageresize) {
         include_once('includes/mr-image-resize.php');
    $image_resized = mr_image_resize($image->imagePath, $gallery->gallerywidth, $gallery->galleryheight, true, '', false);

        $gallery_code .= "<li><img src='".$image_resized."' title='".$image->title."' width='".$gallery->gallerywidth."' height='".$gallery->galleryheight."' /></li>";
        }
        else $gallery_code .= "<li><img src='".$image->imagePath."' title='".$image->title."' /></li>";
		}

        $gallery_code .= "</ul></div><br/><br/><br/>";
        $gallery_code .= "<script>
        jQuery(function($) {

          $('#nrig-$id').bjqs({
            height      : ".$gallery->galleryheight.",
            width       : ".$gallery->gallerywidth.",
            animtype    : '".$gallery->animeffect."',
            animspeed   : ".$gallery->animspeed.",";
         if($gallery->slidercontrols){
          $gallery_code .= "
          showcontrols : true,";
         }
         else{
          $gallery_code .= "
          showcontrols : false,";
         }

         if($gallery->slidermarkers){
          $gallery_code .= "
          showmarkers : true,";
         }
         else{
          $gallery_code .= "
          showmarkers : false,";
         }

          if($gallery->slidercaptions){
          $gallery_code .= "
          usecaptions : true,";
         }
         else{
          $gallery_code .= "
          usecaptions : false,";
         }

           if($gallery->sliderresponsive){
          $gallery_code .= "
          responsive : true,";
         }
         else{
          $gallery_code .= "
          responsive : false,";
         }

        $gallery_code .= "
          });

        });
        </script>";
        return $gallery_code;
	}

	function NRImageGallery_Handler($atts) {
	  $atts = shortcode_atts( array( 'id' => '-1', 'id' => '-1'), $atts );
	  return createNRImageGallery($atts['id'], $atts['id']);
  }
  add_shortcode('NRImageGallery', 'NRImageGallery_Handler');
?>