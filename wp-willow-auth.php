<?php
/**
 * Plugin Name: Willow Auth
 * Version: 1.3.2
 * Plugin URI: https://github.com/BenjaminMedia/wp-willow-auth
 * Description: A plugin for integrating with willow auth
 * Author: Bonnier Publications - Alf Henderson
 * License: GPL v3
 */

// Do not access this file directly
if (!defined('ABSPATH')) {
    exit;
}

function loadWillowAuth()
{
    return \Bonnier\WP\WillowAuth\WillowAuth::instance();
}

register_activation_hook(__FILE__, function () {});

add_action('plugins_loaded', 'loadWillowAuth');
