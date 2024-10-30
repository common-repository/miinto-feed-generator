<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Plugin Name: Miinto Feed Generator
 * Plugin URI: http://therightsw.com/
 * Description: Miinto Feed Generator is a WooCommerce plugin use to download products information in tsv format for miinto feeds. This is only for Simple products.
 * Version: 1.0
 * Author: therightsw
 * Author URI: http://therightsw.com/contact/
 * Tested up to: 4.7.3
 * License: GPL2
 **/
/*
|--------------------------------------------------------------------------
| CHECK CLASS EXISTS OR NOT
|--------------------------------------------------------------------------
*/
if ( !class_exists( 'Mfp_Miinto_Feeds' )){
/*
|--------------------------------------------------------------------------
| START PLUGIN CLASS Name Miinto Feeds
|--------------------------------------------------------------------------
*/
class Mfp_Miinto_Feeds{

		function __construct(){
/*
|--------------------------------------------------------------------------
| APPLY ACTIONS & FILTERS IS WOOCOMMERCE IS ACTIVE
|--------------------------------------------------------------------------
*/
		/********** action to menu  *********/

            add_action('add_meta_boxes', array(&$this, 'Mfp_Miinto_Feed_Meta'));
            add_action( 'save_post', array(&$this, 'Mfp_Save_Miintofeeds_Data'));
	        add_action('admin_menu',  array(&$this,'Mfp_Miinto_Feeds_Menu'));
		}
/*
|--------------------------------------------------------------------------
| START PLUGIN FUNCTIONS
|--------------------------------------------------------------------------
*/
/* Adding Submenu page in woocommercemeun */
    function Mfp_Miinto_Feeds_Menu() {
	    add_submenu_page('woocommerce', 'Miinto Feeds','Miinto Feeds', 'manage_options', 'mfp_miinto_feed', array(&$this, 'Mfp_Miinto_Feed'));
    }

    function Mfp_Save_Miintofeeds_Data( $post_id )
    {

        $post_info=$_POST;
        $color=$post_info['color'];
        $size=$post_info['size'];

        $trs_color= sanitize_text_field($color);
        $trs_size= sanitize_text_field($size);

        $trs_miintofeed_attributes=array($trs_size,$trs_color);
        update_post_meta($post_id, 'mfp_miinto_feed', $trs_miintofeed_attributes);
    }

    function Mfp_Miinto_Feed_Meta( $post_type ){
        $post_types = array('product');
        if ( in_array( $post_type, $post_types )) {
            add_meta_box('mpf_miinto_meta_box', 'Add Size and Color', array( &$this, 'Mfp_Miintofeeds_Meta_Box_Content' ), 'product', 'side', 'default');
        }
    }

    function Mfp_Miintofeeds_Meta_Box_Content() {

        global $post_id;
        global $wpdb;

        $sql = "SELECT ID FROM $wpdb->posts WHERE post_parent = ".$post_id." AND post_type = 'product_variation' ORDER BY ID ASC";
        $trs_meta_val = $wpdb->get_col($sql);
        $trs_prod_color='';
        $trs_prod_size = '';

        if (empty($trs_meta_val)) {

            $trscustom_val = get_post_meta( $post_id, 'mfp_miinto_feed', true );

            if(!empty($trscustom_val)){
                $trs_prod_color= esc_html($trscustom_val[0]);
                $trs_prod_size= esc_html($trscustom_val[1]);
            }
            ?>

           Color: <input type="text" style="width:6em;" placeholder="color" name="<?php echo 'color'.'"'; if($trs_prod_size!= ''){echo 'value="'.$trs_prod_size.'"';}?>>
           Size: <input type="text" style="width:6em;" placeholder="size" name="<?php echo 'size'.'"'; if($trs_prod_color != ''){echo 'value="'.$trs_prod_color.'"';}?>>
    <?php
        }
    }

    function Mfp_Miinto_Feed(){

			    include(plugin_dir_path( __FILE__ ).'admin/mfp-create-feed.php');
                $plugin_data = get_plugin_data(plugin_dir_path(__FILE__ )."mfp-miinto-feeds.php");

    ?>

    <style>
	 	.rating span:before {
            content: "\2605";
            position: absolute;
            text-decoration: underline;
        }

	 </style>
	<?php echo "<br/><br/><br/><br/><br/><br/><div>Note: Contact Us for Variable and Composite Products Miinto Feeds Plugin </div> <div class='content-down'><div style=\"float:left;\"> Developed by <a href=\"".$plugin_data["PluginURI"]."\" style=\"text-decoration: none;\" target=\"_blank\" >The Right Software </a><span>|</span><a href=\"".$plugin_data["AuthorURI"]."\" style=\"text-decoration: none;\" target=\"_blank\"> Contact Support</a> </div>
	<div style=\"float:right;\">Give us a Reveiw <a  href=\"link\" style=\"text-decoration: none;\"  class='rating' target=\"_blank\">
<span>☆<span>☆</span><span>☆</span><span>☆</span><span>☆</span></a> | TRS-MF ".$plugin_data["Version"]."</div></div>";

    }
 } /*Class End */
} //class exists ends
$mfp_miinto_feed = new Mfp_Miinto_Feeds();