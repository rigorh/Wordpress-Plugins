<?php
//////get-geo-info////////////
function get_geo_info() {
    $geo_info = array(
        'geo.region' => get_option('geo_region'), // Replace 'geo_region' with your option name
        'geo.placename' => get_option('geo_placename'), // Replace 'geo_placename' with your option name
        'geo.position' => get_option('geo_position') // Replace 'geo_position' with your option name
    );

    return $geo_info;
}

// Add a shortcode to display the geo information on a page or post
function display_geo_info() {
    $geo_info = get_geo_info();
    $output = '';

    if (!empty($geo_info['geo.region'])) {
        $output .= 'Geo Region: ' . $geo_info['geo.region'] . '<br>';
    }

    if (!empty($geo_info['geo.placename'])) {
        $output .= 'Geo Placename: ' . $geo_info['geo.placename'] . '<br>';
    }

    if (!empty($geo_info['geo.position'])) {
        $output .= 'Geo Position: ' . $geo_info['geo.position'] . '<br>';
    }

    return $output;
}

add_shortcode('display_geo_info', 'display_geo_info');


//////end get-geo-info//////
?>