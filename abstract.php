<?php

require 'view.php';
require 'api.php';

abstract class RecipeCan_Abstract {

    abstract function run();

    public $options;

    protected $view;
    protected $api;

    public function preRunHook() {
        $this->api = new RecipeCan_Api();
        $this->api->options = $this->options;

        $this->view = new RecipeCan_View();
        $this->view->options = $this->options;
    }

    public function start() {
        $this->preRunHook();
        $this->run();
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

        $this->{$call . $post_func_call} ();
    }

}

?>