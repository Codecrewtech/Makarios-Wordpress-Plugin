<?php
/*
 * Plugin Name: Code crew 
 * Plugin Class Name: Code crew 
 * Description: Block access to your website for underage visitors.
 * Author: DiWave Coders
 * Version: 1.1
 * Plugin URI: http://codecrew.tech
 * Support URI: http://codecrew.tech
 * Author URI: http://codecrew.tech
 * Text Domain: codecrew
*/
define('DIAGEVE_DIR',plugin_dir_path(__FILE__));
$diageve_url = plugin_dir_url(__FILE__);
if(is_ssl())
	$diageve_url = str_replace('http://', 'https://', $diageve_url);

define('DIAGEVE_URL', $diageve_url);
define('DIAGEVE_PERMISSION_ERR', __('You don\'t have permission for this action.', 'diageve'));

/** Textdomain Registration */
add_action('init', 'diageve_load_plugin_textdomain');
function diageve_load_plugin_textdomain() {
    $locale = apply_filters('plugin_locale', get_locale(), 'diageve');
    load_plugin_textdomain('diageve', FALSE, basename( dirname( __FILE__ ) ) . '/languages/');
}
//Init components
require_once DIAGEVE_DIR."/inc/tools.php";
require_once DIAGEVE_DIR."/inc/settings.php";
require_once DIAGEVE_DIR."/inc/hooks.php";

class Diwave_AgeVerificator {
    function __construct() {
        new diageve_hooks();
    }
}
//Finally, initialize plugin
function diageve_initialize() {
    new Diwave_AgeVerificator();
}
add_action('init', 'diageve_initialize');