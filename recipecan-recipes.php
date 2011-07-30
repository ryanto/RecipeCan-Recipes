<?php
/*
  Plugin Name: RecipeCan Recipes
  Plugin URI: http://www.recipecan.com
  Description: A wordpress plugin to that organizes recipes for your blog.
  Version: 0.1
  Author: Ryan (ryanto)
  Author URI: http://www.recipecan.com
 */

define('RECIPECAN_VERSION', '0.1');


$recipecan_options = array(
    'plugin_url' => plugin_dir_url(__FILE__),
    'plugin_version' => RECIPECAN_VERSION,
    'path' => dirname(__FILE__),
    'request' => $_REQUEST,
    'api_server' => 'www.recipecan.dev',
    'image_server' => 'www.recipecan.dev',
    'api_version' => 'v1',
    'prefix' => 'recipecan_',
    'register_global_names' => array('recipes', 'myrecipes', 'recipecan')
);

require_once 'abstract.php';

require_once $recipecan_options['path'] . '/binders/setup.php';
require_once $recipecan_options['path'] . '/binders/recipes.php';

if (is_admin()) {
    require_once $recipecan_options['path'] . '/binders/admin.php';
    require_once $recipecan_options['path'] . '/binders/editor.php';
}


?>