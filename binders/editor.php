<?php

require_once 'abstract.php';

class RecipeCan_Binders_Editor extends RecipeCan_Binders_Abstract {

    public function run() {
        add_filter('mce_external_plugins', array(&$this, 'plugin'));
        add_filter('mce_buttons', array(&$this, 'button'));

        add_action('wp_ajax_recipecan', array(&$this, 'select_recipe'));
        add_action('wp_ajax_recipecan_create_recipe', array(&$this, 'create_recipe'));
        add_action('init', array(&$this, 'form_plugin'));
    }

    public function form_plugin() {
        wp_enqueue_script('jquery-form');
    }

    public function plugin($plugins) {
        $plugins['recipecan'] = $this->options['plugin_url'] . 'javascripts/tinymce/recipecan.js';
        return $plugins;
    }

    public function button($buttons) {
        array_push($buttons, "separator", "recipecan");
        return $buttons;
    }

    public function select_recipe() {
        if ($this->has_required_settings()) {
            $recipes = $this->make_recipes();

            $this->view->set('recipes', $recipes->all());

            // for creating new recipes
            $this->view->set('from_show_photo_field', true);
            $this->view->set('form_submit_url', 'admin-ajax.php?action=recipecan_create_recipe');
            
            $this->view->render('editor/index');
        } else {
            $this->view->render('editor/signup');
        }
        die();
    }

    public function create_recipe() {

        $data = $this->request('recipe');

        $this->api->create_recipe($data);

        if ($this->api->failed()) {
            $this->view->errors($this->api->response['error']);
            $this->view->set('recipe', $this->request('recipe'));

            $this->view->set('from_show_photo_field', true);
            $this->view->set('form_submit_url', 'admin-ajax.php?action=recipecan_create_recipe');

            $this->view->render('admin/recipes/_form');
        } else {
            // save the recipe locally
            $recipes = $this->make_recipes();
            $recipes->save($this->api->response['recipe']);

            // create a post
            $recipecan_id = $this->api->response['recipe']['recipecan_id'];
            $recipe = $recipes->find(array('recipecan_id' => $recipecan_id));
            $recipe->tie_to_post();

            // upload photo if the user submitted one
            if (isset($_FILES['photo']['tmp_name']) && is_string($_FILES['photo']['tmp_name'])) {
                $this->api->create_recipe_photo(array(
                    'recipe_id' => $recipecan_id,
                    'filename' => $_FILES['photo']['tmp_name']
                ));

                if ($this->api->success()) {
                    // save the photo urls
                    $recipes->save($this->api->response['recipe'], array('id' => $recipe->data['id']));
                }
            }

            $this->view->set('recipe', $recipe);
            $this->view->render('admin/recipes/ajax/saved');
        }

        die();
    }

}

$recipecan_setup = new RecipeCan_Binders_Editor();
$recipecan_setup->options = $recipecan_options;
$recipecan_setup->start();
?>
