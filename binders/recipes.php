<?php

require_once 'abstract.php';

class RecipeCan_Binders_Recipes extends RecipeCan_Binders_Abstract {

    private $_recipes;

    public function run() {
        add_shortcode('recipecan-list-recipes', array(&$this, 'list_recipes'));
        add_shortcode('recipecan-show-recipe', array(&$this, 'insert'));
        add_shortcode('your-recipe-will-show-here', array(&$this, 'insert'));
        add_filter('the_content', array(&$this, 'page'));
        add_action('init', array(&$this, 'stylesheet'));
        add_action('wp_enqueue_scripts', array(&$this, 'javascript'));
    }

    public function stylesheet() {
        $stylesheet_path = $this->options['plugin_url'] . 'stylesheets/';
        wp_register_style('recipecan', $stylesheet_path . 'recipecan.css', false, $this->options['plugin_version'], 'all');
        wp_register_style('recipecan_print', $stylesheet_path . 'print.css', false, $this->options['plugin_version'], 'print');
        wp_enqueue_style('recipecan');
        wp_enqueue_style('recipecan_print');
    }

    public function javascript() {
        $javascript_path = $this->options['plugin_url'] . '/javascripts/';
        wp_register_script('recipecan_print', $javascript_path . '/printer.js');
        wp_enqueue_script('recipecan_print');
    }

    public function list_recipes() {

        $this->_recipes = $this->make_recipes();
        $to_display = array();

        if ($this->request('search') != '') {
            $to_display[] = $this->search();
        } else {
            $to_display[] = $this->recent();
            $to_display[] = $this->most_viewed();
        }

        $this->view->set('to_display', $to_display);

        $page_link = get_page_link($this->get_option('page_name'));
        $this->view->set('page_link', $page_link);

        // all
        $all_link = add_query_arg('search', 'all', $page_link);
        $this->view->set('all_link', $all_link);

        // courses
        $course_links = array();
        foreach ($this->_recipes->courses() as $course) {
            $course_links[] = array(
                'name' => $course,
                'link' => add_query_arg('search', strtolower($course), $page_link)
            );
        }

        $this->view->set('courses', $course_links);

        return $this->view->mustache('recipes/index');
    }

    public function recent() {
        return array(
            'name' => 'recent',
            'title' => 'Recent Recipes',
            'recipes' => $this->_recipes->all(4)
        );
    }

    public function most_viewed() {
        return array(
            'name' => 'popular',
            'title' => 'Most Popular Recipes',
            'recipes' => $this->_recipes->most_viewed()
        );
    }

    public function search() {

        $term = $this->request('search');

        if ($term == 'all') {
            return array(
                'name' => 'all',
                'title' => 'All Recipes',
                'recipes' => $this->_recipes->all()
            );
        } else {
            return array(
                'name' => 'search',
                'title' => ucfirst($term) . " Recipes",
                'recipes' => $this->_recipes->search($term)
            );
        }
    }

    public function insert($attrs) {
        $recipes = $this->make_recipes();
        $recipe = $recipes->find(array('id' => $attrs[1]));

        if ($recipe) {
            $recipe->viewed();

            $this->view->set('recipe', $recipe);
            return $this->view->mustache('recipes/_recipe');
        }
    }

    public function page($content) {
        global $post;

        if (isset($post) && $post->post_type == $this->get_option('post_type_name')) {
            $recipes = $this->make_recipes();
            $recipe = $recipes->find(array('post_id' => $post->ID));

            if ($recipe) {
                $recipe->viewed();

                $this->view->set('recipe', $recipe);
                return $this->view->mustache('recipes/_recipe');
            }
        } else {
            return $content;
        }
    }

}

$recipecan_recipes = new RecipeCan_Binders_Recipes();
$recipecan_recipes->options = $recipecan_options;
$recipecan_recipes->start();
?>
