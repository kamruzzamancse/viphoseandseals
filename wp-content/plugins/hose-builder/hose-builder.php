<?php
/*
Plugin Name: Hose Builder
Description: Custom Hose Build System for WooCommerce with the shortcode [hose_builder]
Version: 1.0
Author: Md. Kamruzzaman
*/

if (!defined('ABSPATH')) exit;

// Define constants
define('HB_PATH', plugin_dir_path(__FILE__));
define('HB_URL', plugin_dir_url(__FILE__));

// Include files
require_once HB_PATH . 'includes/form-handler.php';
require_once HB_PATH . 'includes/display.php';
require_once HB_PATH . 'includes/ajax-handler.php'; // Add this line

// Enqueue assets
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style('hb-style', HB_URL . 'assets/css/style.css');
    wp_enqueue_script('hb-script', HB_URL . 'assets/js/script.js', ['jquery'], null, true);
    wp_localize_script('hb-script', 'hb_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));
});

// Shortcode
add_shortcode('hose_builder', function() {
    ob_start();
    include HB_PATH . 'templates/form.php';
    return ob_get_clean();
});