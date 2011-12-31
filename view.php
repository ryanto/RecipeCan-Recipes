<?php

require_once 'external/Mustache.php';

class RecipeCan_View {

    public $value = array();
    public $options;

    public function render_old($file) {
        echo $this->read($file);
    }

    public function render($file) {
        echo $this->mustache($file);
    }

    public function mustache($file) {
        $m = new Mustache();

        $this->value['options'] = $this->options;

        $template = file_get_contents($this->options['path'] . '/views/' . $file . '.mustache');
        return $m->render($template, $this->value, array(
            'recipe_preview' => file_get_contents($this->options['path'] . '/views/recipes/_preview.mustache'),
            'recipe_preview_group' => file_get_contents($this->options['path'] . '/views/recipes/_preview_group.mustache'),
            'form_errors' => file_get_contents($this->options['path'] . '/views/shared/_form_errors.mustache')
        ));
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

    public function set_data($name, $data) {
        foreach ($data as $attribute => $value) {
            $this->set($name . "[" . $attribute . "]", $value);
        }
        $this->set($name, $data);
    }

    // helpers

    public function p($str) {
        echo htmlentities($str);
    }

    /**
     * Displays Recipes, pass in array
     *  title
     *  recipes
     *
     */
    public function display_recipes($data = array()) {
        $this->set('display', $data);
        $this->render('recipes/_recipes');
    }

    


}

?>
