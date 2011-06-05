<?php
/*
Plugin Name: RecipeCan Recipes
Plugin URI: http://www.recipecan.com
Description: A wordpress plugin to that organizes recipes on for blog.
Version: 0.1
Author: Ryan (ryanto)
Author URI: http://www.recipecan.com
*/

define('RECIPECAN_VERSION', '0.1');

$recipecan_options = array(
  'plugin_url' => plugin_dir_url( __FILE__ ),
  'path' => dirname(__FILE__),
  'request' => $_REQUEST,
);

if (is_admin()) {
    require_once dirname( __FILE__ ) . '/admin.php';
}

?>


