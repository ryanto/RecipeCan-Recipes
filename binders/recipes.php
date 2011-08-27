<?php

require_once 'abstract.php';

class RecipeCan_Binders_Recipes extends RecipeCan_Binders_Abstract {

    private $_recipes;

    public function run() {
        //add_filter('single_template', array(&$this, 'template'));
        //add_filter('archive_template', array(&$this, 'archive'));
        add_shortcode('recipecan-list-recipes', array(&$this, 'list_recipes'));
        add_shortcode('recipecan-show-recipe', array(&$this, 'insert'));
        add_shortcode('your-recipe-will-show-here', array(&$this, 'insert'));
        add_filter('the_content', array(&$this, 'page'));
        add_action('init', array(&$this, 'stylesheet'));
    }

    public function stylesheet() {
        $stylesheet = $this->options['plugin_url'] . '/stylesheets/recipecan.css';
        wp_register_style('recipecan', $stylesheet);
        wp_enqueue_style('recipecan');
    }

    public function template($single_template) {
        global $post;

        if ($post->post_type == $this->get_option('post_type_name')) {
            $single_template = $this->options['path'] . '/templates/recipe-post-type-template.php';
        }
        //return $single_template;
    }

    public function archive($archive_template) {
        global $post;
        // for now lets redirect to the page
    }

    public function list_recipes() {

        $this->_recipes = $this->make_recipes();

        if ($this->request('search') != '') {
            $display = $this->search();
        } else {
            $display = $this->index();
        }

        $recipes = $this->make_recipes();

        $this->view->set('recipes', $recipes);
        $this->view->set('page_name', $this->get_option('page_name'));
        $this->view->set('display', $display);

        return $this->view->render('recipes/index');
    }

    public function index() {
        return array(
            'title' => 'Recent Recipes',
            'recipes' => $this->_recipes->all(4)
        );
    }

    public function search() {

        $term = $this->request('search');

        return array(
            'title' => ucfirst($term) . " Recipes",
            'recipes' => $this->_recipes->search($term)
        );
    }

    public function insert($attrs) {
        $recipes = $this->make_recipes();
        $recipe = $recipes->find(array('id' => $attrs[1]));

        $this->view->set('recipe', $recipe);
        return $this->view->read('recipes/insert');
    }

    public function page($content) {
        global $post;

        if (isset($post) && $post->post_type == $this->get_option('post_type_name')) {
            $recipes = $this->make_recipes();
            $recipe = $recipes->find(array('post_id' => $post->ID));

            $this->view->set('recipe', $recipe);
            return $this->view->render('recipes/page');
        } else {
            return $content;
        }
    }

}

$recipecan_recipes = new RecipeCan_Binders_Recipes();
$recipecan_recipes->options = $recipecan_options;
$recipecan_recipes->start();
?>
