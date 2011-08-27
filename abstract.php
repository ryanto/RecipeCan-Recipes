<?php

// boot order
require_once 'api.php';
require_once 'view.php';

// models
require_once $recipecan_options['path'] . '/models/recipes.php';

// top most class
abstract class RecipeCan_Abstract {

    public $options;
    public $api;

    public function add_option($name, $value) {
        add_option($this->options['prefix'] . $name, $value);
    }

    public function update_option($name, $value) {
        return update_option($this->options['prefix'] . $name, $value);
    }

    public function get_option($name) {
        return get_option($this->options['prefix'] . $name);
    }



    public function get_post_type_name() {
        $option = $this->get_option('post_type_name');
        if (!$option) {
            $option = $this->generate_post_type_name();
            $this->add_option('post_type_name', $option);
        }
        return $option;
    }

    public function generate_post_type_name($postfix = null) {
        foreach ($this->options['register_global_names'] as $name) {
            $try_name = $name . $postfix;
            if (!post_type_exists($try_name)) {
                return $try_name;
            }
        }
        // out of names to try
        return $this->generate_post_type_name(rand(0, 100));
    }

    public function make_recipes() {
        $recipes = new RecipeCan_Models_Recipes();
        $recipes->options = $this->options;
        $recipes->api = $this->make_api();
        $recipes->ensure_table();

        return $recipes;
    }

    public function make_api() {
        $api = new RecipeCan_Api();
        $api->options = $this->options;
        return $api;
    }

}

?>
