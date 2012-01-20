<?php
/*
  Plugin Name: RecipeCan Recipes
  Plugin URI: http://www.recipecan.com/howto/recipe-wordpress-plugin
  Description: A WordPress plugin that organizes recipes on your blog.
  Version: 0.2.2
  Author: Ryan (ryanto)
  Author URI: http://www.recipecan.com
 */

define('RECIPECAN_VERSION', '0.2.2');

$check_development = true;

if ($check_development && getenv('APPLICATION_ENV') == 'development') {
    $recipecan_hostname = 'www.recipecan.dev';
} else {
    $recipecan_hostname = 'www.recipecan.com';
}

$recipecan_options = array(
    'plugin_url' => plugins_url() . "/recipecan-recipes/",
    'plugin_version' => RECIPECAN_VERSION,
    'path' => dirname(__FILE__),
    'request' => $_REQUEST,
    'api_server' => $recipecan_hostname,
    'image_server' => $recipecan_hostname,
    'api_version' => 'v1',
    'prefix' => 'recipecan_',
    'recipe_post_type_names' => array('recipes', 'recipecan', 'recipecans'),
    'recipe_page_names' => array('myrecipes', 'recipespage', 'recipecanpage'),  
);

require_once 'abstract.php';

require_once $recipecan_options['path'] . '/binders/setup.php';
require_once $recipecan_options['path'] . '/binders/recipes.php';

if (is_admin()) {
    require_once $recipecan_options['path'] . '/binders/admin.php';
    require_once $recipecan_options['path'] . '/binders/editor.php';
}


?>
