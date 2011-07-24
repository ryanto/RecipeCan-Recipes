<?php

require_once 'abstract.php';

class RecipeCan_Binders_Recipes extends RecipeCan_Binders_Abstract {

    public function run() {
        //add_filter('single_template', array(&$this, 'recipecan_recipes_template'));
        //add_filter('archive_template', array(&$this, 'recipecan_recipes_archive'));
        add_shortcode('recipecan-list-recipes', array(&$this, 'list_recipes'));
    }

    public function recipecan_recipes_template($single_template) {
        global $post;

        var_dump('single template');

        if ($post->post_type == 'recipecan') {
            var_dump('RECIPECAN PAGE FOUND');
            $single_template = dirname(__FILE__) . '/post-type-template.php';
        }
        //return $single_template;
    }

    public function recipecan_recipes_archive($archive_template) {
        global $post;
        // for now lets redirect to the page
    }

    public function list_recipes() {
        return "listing recipes";
    }

}

$recipecan_recipes = new RecipeCan_Binders_Recipes();
$recipecan_recipes->options = $recipecan_options;
$recipecan_recipes->start();
?>