<?php

require_once 'external/Mustache.php';

class RecipeCan_View {

    public $value = array();
    public $options;

    public function render($file) {
        echo $this->read($file);
    }

    public function mustache($file) {
        $m = new Mustache();
        $template = file_get_contents($this->options['path'] . '/views/' . $file . '.mustache');
        return $m->render($template, $this->value, array(
            'recipe_preview' => file_get_contents($this->options['path'] . '/views/recipes/_preview.mustache'),
            'recipe_preview_group' => file_get_contents($this->options['path'] . '/views/recipes/_preview_group.mustache'),
            'recipes' => file_get_contents($this->options['path'] . '/views/recipes/_recipes.mustache')
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
