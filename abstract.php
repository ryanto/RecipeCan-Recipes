<?php

abstract class RecipeCan_Abstract {

    public $options;

    public function render($file) {
        require $this->options['path'] . '/views/' . $file . '.phtml';
    }

    public function request($var) {
        if (array_key_exists($var, $this->options['request'])) {
            return $this->options['request'][$var];
        } else {
            return '';
        }
    }

    public function call() {
        $prefix = 'recipecan_';
        return substr($this->request('page'), strlen($prefix));
    }

    public function method() {
        $method = $this->request('method');
        if ($method == '') {
            return 'get';
        } else {
            return $method;
        }
    }

    public function router() {
        $call = $this->call();
        $method = $this->method();

        if ($method == 'get') {
            $post_func_call = '';
        } else {
            $post_func_call = "_" . $method;
        }
        
        $this->{$call . $post_func_call}();
    }

}


?>