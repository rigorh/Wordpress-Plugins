<?php
/*
Plugin Name: SEO By BibData
Plugin URI: https://bibdata.com/index.php/seo-by-bibdata/
Description: Experience SEO optimization made simple and efficient with SEO by BibData. No complicated settings are required. Let SEO by BibData's algorithms handle the job for you.
Version: 1.0.0
Author: Rigo
Author URI: https://www.bibdata.com
Text Domain: SEO by BibData
Requires at Least: 4.0
Requires PHP: 7.0
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
GitHub Plugin URI: https://github.com/rigorh/Wordpress-Plugins/tree/main
GitHub Branch: main
*/

//require_once('/var/www/html/bibdata/wp-load.php');

function bibdata_seo_meta_box() {
    add_meta_box(
        'my-seo-meta-box',
        'Custom Meta Description',
        'bibdata_seo_meta_box_content',
        array('post', 'page'),
        'normal',
        'high'
    );
}

add_action('add_meta_boxes', 'bibdata_seo_meta_box');

function bibdata_seo_meta_box_content($post) {        
    $meta_description = get_post_meta($post->ID, '_bibdata_meta_description'.$post->ID, true);   
    ?>
    <label for="custom_meta_description">Enter a custom meta description:</label>
    <textarea id="custom_meta_description" name="custom_meta_description" rows="3" style="width: 100%;"><?php echo esc_attr($meta_description); ?></textarea>
    <?php
}

function bibdata_save_seo_meta($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) 
        return $post_id;    
    $post_type = get_post_type($post_id);

    if (($post_type == 'page' || $post_type == 'post') && !current_user_can('edit_'.$post_type, $post_id))
        return $post_id;
    $meta_description = sanitize_text_field($_POST['custom_meta_description']);  
    if ($meta_description=="") {
     //AI values   
     $meta_description = bibdata_get_metacontent();
     update_post_meta($post_id, '_bibdata_meta_description'.$post_id, $meta_description);     
    }  else {
     update_post_meta($post_id, '_bibdata_meta_description'.$post_id, $meta_description);   
    }    
    
}
add_action('save_post', 'bibdata_save_seo_meta');

//Get values
function bibdata_get_meta_value(){   
    global $post;
    //$post_id = $post->ID;
    $post_id = $post->ID ?? '0';
      


    global $wpdb;
    $meta_key = "_bibdata_meta_description".$post_id;
    $value = $wpdb->get_var( $wpdb->prepare("SELECT meta_value FROM $wpdb->postmeta WHERE meta_key = %s LIMIT 1" , $meta_key) );   
    bdta_get_additional_tags($value);

}
add_action('wp_head', 'bibdata_get_meta_value',4);

function bibdata_array_tostring($valor){
    //$array = array("value1", "value2", "value3", "value4");
    // Convert the array to a string with values separated by colons
    $string = implode(", ", $valor);
   
    // Output the resulting string
    return $string;
}

function bibdata_get_metacontent(){
    global $post;
    $post_id = $post->ID;
    $content = $post->post_content;
    $content = strip_tags($content);
    $content = trim($content);
    $meta_description = substr($content, 0, 160); 
    //$string = "This is a sample string with spaces";
    $lastSpacePos = strrpos($meta_description, ' ');
    
    if ($lastSpacePos !== false) {
        $substring = substr($meta_description, 0, $lastSpacePos)." ...";
    } else {
        // No space found, so use the entire string
        $substring = $meta_description;
    }
    
    return $substring;
    
}

function bdta_get_additional_tags($value){
    if ($value==""){
        //$value="Welcome to SimbyShop - Where Elegance Meets Exceptional Craftsmanship! Gold and sterling silver jewelry.";
        //$value = esc_html(get_bloginfo('blogname'));
        $value = get_bloginfo('description');
    }
    echo '<meta name="description" content="' . esc_attr($value) . '"/>';  
    echo "\n"; 
    echo '<meta name="robots" content="index, follow">';
    echo "\n"; 
    $region = get_option('geo_region');
    if ($region !=''){
        echo '<meta name="geo.region" content="'.$region.'"/>';  
    }
    echo "\n";
    $placename = get_option('geo_placename');
    if ($placename !=''){
        echo '<meta name="geo.placename" content="'.$placename.'"/>';  
    }
    echo "\n";
    $language = get_option('WPLANG');
    echo '<meta name="language" content="'.$language.'"/>'; 
    echo "\n";
    $site_title = get_bloginfo('name');
    echo '<meta property="og:title" content="'.$site_title.'"/>'; 
    echo "\n";
    echo '<meta property="og:description" content="' . esc_attr($value) . '"/>';
    echo "\n";
    $site_url = get_bloginfo('url');
    echo '<meta property="og:url" content="' . $site_url . '"/>';
    echo "\n";
    $site_name = get_bloginfo('name');
    echo '<meta property="og:site_name" content="' . $site_name . '"/>';
    echo "\n";
 
}

/////////////////////////////////////////////////////////////////////////
//                          Settings                                   //
////////////////////////////////////////////////////////////////////////



// Add a link to display the settings page in the Plugin screen
function bdta_add_settings_link($links) {
    $settings_link = '<a href="options-general.php?page=bdta-custom-plugin-settings">Settings</a>';
    array_push($links, $settings_link);
    return $links;
}
$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'bdta_add_settings_link');

// Create the settings page
function bdta_custom_plugin_settings_page() {
    ?>

<form method="post" action="">
<input type="hidden" id="_wpnonce" name="_wpnonce" value="f3846eb80a" />
<input type="hidden" name="_wpnonce_my-plugin-nonce" value="f3846eb80a" />   
<?php
// Output a nonce field for security
wp_nonce_field('_wpnonce', '_wpnonce_my-plugin-nonce');

?>
    <div class="wrap" style ="font-size: medium;">
        <h2>SEO by BibData</h2>
        Experience SEO optimization made simple and efficient with <b>SEO by BibData</b>. 
        No complicated settings are required. Let SEO by BibData's algorithms handle the job for you.
        <br/><br/>
        <!--<img src="<?php echo plugin_dir_url(__FILE__) . 'logo.png'; ?>" alt="Logo" width="100">-->
        </b><br/><br/>

        <?php 
            $geo_region = get_option('geo_region');
            $geo_placename = get_option('geo_placename');
        ?>  
        Region (Optional). Ex US-NY, CA-ON, ...<br/>      
        <input type="text" name="region" value="<?php echo $geo_region?>" placeholder="Enter region name. Ex. US-NY or CA-ON" style="width:300px;font-size: medium;"><br/><br/>
        City (Optional). Ex. New York, Toronto, ...<br/>
        <input type="text" name="placename" value="<?php echo $geo_placename?>" placeholder="Enter placename name. Ex. New York or Toronto" style="width:300px;font-size: medium;"><br/></br>
        <button type="submit" name="save_textbox" style="width:300px;font-size: medium;height: 40px;">Save</button>
        

        <br/><br/><br/><br/><br/>
        <hr style='border: 1px solid green;'></hr>
        <b>SEO By BibData</b> has been successfully installed upon activation. The information provided above is optional. Related meta tags will be automatically included if this information is supplied.<br/>
        The sitemap.xml file will be automatically generated or updated each time you edit, update, or create a page.        
        <hr style='border: 1px solid green;'></hr>
        <h3>Support Us</h3>
        If you find this plugin helpful, please consider making a donation to support our work. Thank You for Your Support!!!
        <br/><br/>
        <a class="button-primary" href="https://www.bibdata.com/index.php/donate">Donate</a>

        
    </div>
</form>

<?php
function bdta_salva_geo_valores($valor1, $valor2) {
            //echo "PHP function executed!".$valor;
            update_option('geo_region', $valor1);                        
            // Set geo.placename
            update_option('geo_placename', $valor2);
            // Set geo.position
            //update_option('geo_position', '40.7128;-74.0060');
            
        }

?>

 <?php
// Handle form submission
if (isset($_POST['save_textbox']) && check_admin_referer('_wpnonce', '_wpnonce_my-plugin-nonce')) {
    $textbox_value1 = sanitize_text_field($_POST['region']);
    $textbox_value2 = sanitize_text_field($_POST['placename']);
    //update_option('geo_region', $textbox_value);
    bdta_salva_geo_valores($textbox_value1,$textbox_value2);
    ?>
        <script>
        alert('Geo-location meta tags have been added.')    
        location.reload();
        </script>
    <?php
}
?>  

    <?php
}

// Register the settings page
function bdta_custom_plugin_register_settings() {
    add_menu_page(
        'BDTA Custom Plugin Settings',
        'SEO by BibData',
        'manage_options',
        'bdta-custom-plugin-settings',
        'bdta_custom_plugin_settings_page'
    );
}

////////////////////////////////////////////////////
function generate_sitemap() {
    $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
    $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
   
    // Query WordPress posts and pages
    $args = array(
        'post_type' => array('post', 'page'),
        'post_status' => 'publish',
        'posts_per_page' => -1,
    );

    $posts = get_posts($args);
    $site_url = get_bloginfo('url');

    foreach ($posts as $post) {
        $post_url = get_permalink($post->ID);
        $post_date = get_the_time('c', $post->ID);        
        $sitemap .= '<url>';
        $sitemap .= '<loc>' . esc_url($post_url) . '</loc>';
        $sitemap .= '<lastmod>' . $post_date . '</lastmod>';
        $sitemap .= '<changefreq>monthly</changefreq>';
        $sitemap .= '<priority>0.8</priority>';
        $sitemap .= '';
        $sitemap .= '</url>';
    }

    $sitemap .= '<url>';
    $sitemap .= '<loc>' . $site_url . '/sitemap.xml</loc>';
    $sitemap .= '</url>';
    $sitemap .= '</urlset>';

    $file = ABSPATH . 'sitemap.xml';
    file_put_contents($file, $sitemap);
}

register_activation_hook(__FILE__, 'generate_sitemap');

// Hook into WordPress to regenerate the sitemap when posts are updated or added
add_action('save_post', 'generate_sitemap');


////////////////////////////////////////////////////
add_action('admin_menu', 'bdta_custom_plugin_register_settings');




