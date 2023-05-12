<?php
/*
 * Plugin Name: Custom Articles
 * Description: Custom articles is custom post with show the locations.
 * Author: Codal Team
 * Version: 1.0
 * Author URI:http://localhost/wordpress
 * Text Domain: cust-art
 * Domain Path: /includes/languages
*/
if (! defined('ABSPATH')) {
    die();
}

if (!defined('CUSTOM_POST_FOLDER')) {
    define('CUSTOM_POST_FOLDER', 'custom-article');
}
if (!defined('POST_ROOT_DIR')) {
    define('POST_ROOT_DIR', plugin_dir_path(__FILE__));
}
if (!defined('POST_ROOT_PATH')) {
    define('POST_ROOT_PATH', plugin_dir_url(__FILE__));
}

require_once(POST_ROOT_DIR.'/includes/classes/class-custom-article.php');
$custom_post = new CUSTOM_POST();