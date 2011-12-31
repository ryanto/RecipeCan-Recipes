<?php

require_once 'external/Mustache.php';

class RecipeCan_View {

    public $value = array();
    public $options;

    public function render($file) {
        echo $this->mustache($file);
    }

    public function mustache($file) {
        $m = new Mustache();

        $this->helpers();

        $template = file_get_contents($this->options['path'] . '/views/' . $file . '.mustache');
        return $m->render($template, $this->value, array(
            'recipe_preview' => file_get_contents($this->options['path'] . '/views/recipes/_preview.mustache'),
            'recipe_preview_group' => file_get_contents($this->options['path'] . '/views/recipes/_preview_group.mustache'),
            'form_errors' => file_get_contents($this->options['path'] . '/views/shared/_form_errors.mustache'),
            'recipe_form' => file_get_contents($this->options['path'] . '/views/admin/recipes/_form.mustache')
        ));
    }

    /**
     * Sets the view up with a bunch of helper data, used mainly for mustache
     */
    public function helpers() {
        $this->value['options'] = $this->options;

        $this->value['helpers'] = array(
            'courses' => array(
                "Breakfast", "Lunch", "Snack", "Appetizer", "Dinner", "Side",
                "Drink", "Dessert"
            ),
            'cuisines' => array(
                "American", "Cajun", "Chinese", "Cuban", "English", "Filipino",
                "French", "German", "Greek", "Indian", "Irish", "Italian",
                "Japanese", "Jewish", "Korean", "Mediterranean", "Mexican",
                "Middle-Eastern", "Moroccan", "Polish", "Russian", "Southern",
                "Spanish", "Thai", "Vietnamese"
            )
        );
    }

    public function read($file) {
        ob_start();
        require $this->options['path'] . '/views/' . $file . '.phtml';
        return ob_get_clean();
    }

    public function set($name, $value) {
        $this->value[$name] = $value;
    }

    public function get($name) {
        if (array_key_exists($name, $this->value)) {
            return $this->value[$name];
        } else {
            return '';
        }
    }

    /**
     * Takes an API error and puts it into an array
     * that can be read by mustache
     */
    public function errors($api_error) {
        $errors = array();
        foreach ($api_error as $field => $error) {
            $errors[] = array(
                'field' => ucfirst($field),
                'errors' => $error
            );
        }
        $this->set('display_errors', true);
        $this->set('errors', $errors);
    }


}

?>
