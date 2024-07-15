<?php
/*
Plugin Name: New Plugin
Description: this plugin will work with woocommerce
Author:fizzajavaid
Version: 1.0


*/

// function to include/connect js and css files 
function new_plugin_files(){


    wp_enqueue_style ('new-plugin-css', plugin_dir_url( __FILE__) . '/css/style.css');
    wp_enqueue_script('new-plugin-js', plugin_dir_url(__FILE__) . '/js/script.js', array('jquery'));
    wp_enqueue_script('jquery');
//AJAX
// here we defined ajax-object, and load_products_nonce on our own. can name anything other than that
    wp_localize_script('new-plugin-js', 'ajax_object',array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('load_products_nonce')
    ));
}

add_action('wp_enqueue_scripts', 'new_plugin_files');

//function to fetch categories from woocommerce_categories and display them

function display_woocommerce_categories(){

      // Check if WooCommerce is active
      if ( ! class_exists( 'WooCommerce' ) ) {
        return 'WooCommerce not available';
    }

    //it will fetch all categories from woocommerce
$product_categories = get_terms(
array(
    'taxonomy' => 'product_cat',
    'orderby' => 'name',
    'order' => 'ASC',
    //means even if category contains no product it will stil show them . if set true categories containing
    //no produts wont be visible
    'hide_empty' => False,
)
);

//if categories is not empty(means if categories exist) it will show those categories in an html structure on the page
if(!empty($product_categories)){
        $output = '<div class="woocommerce_categories">';
                //this loop will run for each category
                foreach($product_categories as $category){

                    //jo category iterate ho rhi uski base par thumbnail idd fetch ki. then us thumbnail id sa image url 
                    $thumbnail_id = get_term_meta($category->term_id, 'thumbnail_id', true);
                    // category ka thumbnail ka url
                    $image_url = wp_get_attachment_url($thumbnail_id);
                    $category_name = $category->name;// category name
                    $category_url = get_term_link($category);

                    //this div is to show each category separately
                    $output.= '<div class="single_category">';
                        $output .= '<h5>'. $category_name . '</h5>';
                        //alt = "'..'"
                        $output .= '<img src="' .$image_url. '" alt = "'.$category_name.'"/>';
                        // $output .= '<a href= "'. $category_url .'">Read More</a>';
                         $output .= '<a href="#" category_id= "'. $category->term_id.'">Read More</a>';
                    $output .= '</div>';//closing second div

                }
        $output .= '</div>';//closing first div
        return $output;// // Return output for shortcode. this is necessary when we have to use the code for shortcode. we are returning 
        //value for this main function display_woocommerce_categories
    }
    return 'No categories found'; // Return message if no categories

}
//here ecommerce_categories is the name of our shortcode which we can use in any page to get the complete
//result of this function. means to fetch catgeoreis and siaplay them on any page
add_shortcode('ecommerce_categories', 'display_woocommerce_categories' );

function load_products_by_category(){

    check_ajax_referer('load_products_nonce', 'nonce');
    //ya id js sa ayi ha
    $category_id= ($_POST['category_id']);
   
    $term= get_term_by('id', $category_id, 'product_cat');
    $category_slug = $term->slug;
    $product_html = do_shortcode('[products category="'. $category_slug .'"]'); 

    wp_send_json_success($product_html);

}

add_action('wp_ajax_load_products_by_category', 'load_products_by_category' );//logged in users
add_action('wp_ajax_nopriv_load_products_by_category' , 'load_products_by_category');// non-logged in users
?>