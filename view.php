<?php

class RecipeCan_View {

    public $value = array();
    public $options;

    public function render($file) {
        require $this->options['path'] . '/views/' . $file . '.phtml';
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

    public function p($str) {
        echo htmlentities($str);
    }


}

?>