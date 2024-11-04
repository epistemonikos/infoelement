<?php
/*
Plugin name: infoelement
Description: This is a plugin that will display the information of a Framework.
Author: epistemonikos
*/

require_once plugin_dir_path(__FILE__) . 'infoelement-functions.php';

add_shortcode( 'infoelement', 'infoelement_shortcode' );

wp_register_style( 'infoelement', plugins_url( 'infoelement.css', __FILE__ ) );
wp_enqueue_style( 'infoelement' );