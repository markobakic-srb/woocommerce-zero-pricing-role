<?php
/**
 * Plugin Name: WooCommerce Zero Pricing Role
 * Plugin URI:  https://yourwebsite.com
 * Description: Creates a "Zero Pricing Role" that makes all products, shipping, and taxes free for users with that role.
 * Version:     1.0.0
 * Author:      Marko Bakic
 * License:     GPL v2 or later
 * Text Domain: wc-zero-role
 * Domain Path: /languages
 * WC requires at least: 6.0
 */

if (!defined('ABSPATH')) exit;

define('WZR_PLUGIN_PATH', plugin_dir_path(__FILE__));

// Include activator class BEFORE activation hook
require_once WZR_PLUGIN_PATH . 'includes/class-activator.php';

register_activation_hook(__FILE__, ['WZR_Activator', 'activate']);

// Check WooCommerce active
function wzr_check_woocommerce() {
    if (!class_exists('WooCommerce')) {
        add_action('admin_notices', function() {
            echo '<div class="error"><p>WooCommerce Zero Pricing Role requires WooCommerce.</p></div>';
        });
        return false;
    }
    return true;
}

// Load plugin
function wzr_init() {
    if (!wzr_check_woocommerce()) return;
    load_plugin_textdomain('wc-zero-role', false, dirname(plugin_basename(__FILE__)) . '/languages');
    require_once WZR_PLUGIN_PATH . 'includes/class-core.php';
    new WZR_Core();
}
add_action('plugins_loaded', 'wzr_init');
