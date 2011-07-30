<?php

require_once 'abstract.php';

class RecipeCan_Binders_Recipes extends RecipeCan_Binders_Abstract {

    public function run() {
        //add_filter('single_template', array(&$this, 'template'));
        //add_filter('archive_template', array(&$this, 'archive'));
        add_shortcode('recipecan-list-recipes', array(&$this, 'list_recipes'));
        add_filter('the_content', array(&$this, 'content'));
    }

    public function template($single_template) {
        global $post;

        if ($post->post_type == $this->get_option('post_type_name')) {
            var_dump('RECIPECAN PAGE FOUND');
            $single_template = $this->options['path'] . '/templates/recipe-post-type-template.php';
        }
        //return $single_template;
    }

    public function archive($archive_template) {
        global $post;
        // for now lets redirect to the page
    }

    public function list_recipes() {
        $recipes = $this->make_recipes();
        $this->view->set('recipes', $recipes->all_data());
        $this->view->render('recipes/index');
    }

    public function content($content) {
        global $post;

        if (isset($post) && $post->post_type == $this->get_option('post_type_name')) {
            $recipes = $this->make_recipes();
            $recipe = $recipes->find(array('post_id' => $post->ID));

            $this->view->set('recipe', $recipe);
            $this->view->render('recipes/show');
        } else {
            return $content;
        }
    }
    

}

$recipecan_recipes = new RecipeCan_Binders_Recipes();
$recipecan_recipes->options = $recipecan_options;
$recipecan_recipes->start();
?>