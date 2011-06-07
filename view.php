<?php

class RecipeCan_View {

    public $file;
    public $value = array();
    public $options;

    public function render($file) {
        require $this->options['path'] . '/views/' . $file . '.phtml';
    }

    public function set($name, $value) {
        $this->value[$name] = $value;
    }

    public function get($name) {
        return $this->value[$name];
    }
}

?>