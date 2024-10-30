<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class Mfp_Selected_Products
{

    // Function to fetch record

    public static function Mfp_Export_Miinto_Feed()
    {
        global $wpdb;

        // SQL Query to fetch record

          $sql = 'SELECT P.ID, P.post_name,P.post_title, P.post_excerpt, PM1.meta_value As price, PM5.meta_value As sale_price,  PM4.meta_value As sku,  PM2.meta_value,P2.guid as product_pic, PM3.meta_value As availability,
                  PM6.meta_value As stock_level
                  FROM '.$wpdb->prefix.'posts AS P 
                  LEFT JOIN '.$wpdb->prefix.'postmeta as PM ON P.ID = PM.post_id 
                  LEFT JOIN '.$wpdb->prefix.'postmeta as PM1 ON P.ID = PM1.post_id 
                  LEFT JOIN '.$wpdb->prefix.'postmeta as PM2 ON P.ID = PM2.post_id 
                  LEFT JOIN '.$wpdb->prefix.'posts   as P2 ON P2.ID = PM2.meta_value 
                  LEFT JOIN '.$wpdb->prefix.'postmeta as PM3 ON P.ID = PM3.post_id
                  LEFT JOIN '.$wpdb->prefix.'postmeta as PM4 ON P.ID = PM4.post_id 
                  LEFT JOIN '.$wpdb->prefix.'postmeta as PM5 ON P.ID = PM5.post_id 
                  LEFT JOIN '.$wpdb->prefix.'postmeta as PM6 ON P.ID = PM6.post_id 
                  WHERE P.post_type = %s  AND P.post_status= %s AND P2.post_type != %s   AND PM1.meta_key= %s AND PM4.meta_key= %s AND PM5.meta_key= %s  AND PM2.meta_key = %s AND PM3.meta_key= %s AND PM6.meta_key= %s  
                  GROUP BY P.ID';

                    // Query params
                    $post_product		    = 'product';
                    $post_publish		    = 'publish';
                    $post_variation		    = 'product_variation';
                    $meta_key_price	        = '_price';
                    $meta_key_sku	        = '_sku';
                    $meta_key_sale_price    = '_sale_price';
                    $meta_key_thumbnail	    = '_thumbnail_id';
                    $meta_key_stock_status  = '_stock_status';
                    $meta_key_stock	        = '_stock';

                     // Prepare and Get Result

                     $products_in_ = $wpdb->get_results($wpdb->prepare( $sql,$post_product,$post_publish,$post_variation,$meta_key_price,$meta_key_sku,$meta_key_sale_price,$meta_key_thumbnail,$meta_key_stock_status,$meta_key_stock));

                     //to show attributes name
                    $mfp_header = array('id','item_group_id','title','description','price','sale_price','brand','category','gender','gtin','color','size','availability','image_link','additional_image_link','c:season_tag:string','c:stock_level:integer','c:style_id:string');

                    $timestamp=time();
                    $create_name =  'miintofeeds'.$timestamp;
                    $filename=$create_name.'.tsv';
                    $handle = fopen($filename, 'wb');

                    fwrite($handle, implode("\t", $mfp_header). PHP_EOL);

                    foreach ($products_in_ as $values_in_table) {

                        $color= '';
                        $size = '';
                        $dev_color_size= get_post_meta( $values_in_table->ID, 'mfp_miinto_feed', true );

                        if(!empty($dev_color_size[1])){
                        $color= $dev_color_size[1];
                        }
                                if(!empty($dev_color_size[0])){
                                    $size= $dev_color_size[0];
                                }

            $product_cats = wp_get_post_terms($values_in_table->ID, 'product_cat'); // get catergory name to show product type
            $categoryname = '';
            foreach ($product_cats as $category) {
                     $categoryname = $categoryname.'>'.$category->name;
            }
             $categoryname = substr($categoryname, 1);

            $ce_check = strip_tags($values_in_table->post_excerpt); // description of product
            $ce_encoded = str_replace("&nbsp;", "", $ce_check);
            $ce_encoded = str_replace("&amp;", "", $ce_encoded);
            $desc_ce = trim(preg_replace('/\s\s+/', ' ', $ce_encoded));
            if($values_in_table->sale_price == ''){
                $values_in_table->sale_price =  $values_in_table->price;
            }
            $price_data = html_entity_decode(get_woocommerce_currency_symbol() . " " . $values_in_table->price); // regular price
            $sale_price = html_entity_decode(get_woocommerce_currency_symbol() . " " . $values_in_table->sale_price); // sale price

            //stock status
            if ($values_in_table->availability == 'instock')
                $availability = "in stock";
            else
                $availability = $values_in_table->availability;

            //to write data on tsv
            $mfp_header_content = array($values_in_table->ID,$values_in_table->ID,$values_in_table->post_title,$desc_ce,$price_data,$sale_price,' ',$categoryname,' ',$values_in_table->sku,$color,$size,$availability,$values_in_table->product_pic,' ','',$values_in_table->stock_level,'');
            fwrite($handle, implode("\t", $mfp_header_content). PHP_EOL);
        }

        fclose($handle);

        if (file_exists($filename)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($filename).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filename));

            ob_clean();
            flush();

            readfile($filename);
        }
    }
}