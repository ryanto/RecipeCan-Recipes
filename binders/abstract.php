<?php

abstract class RecipeCan_Binders_Abstract extends RecipeCan_Abstract {

    abstract function run();

    protected $view;

    public function preRunHook() {
        $this->api = $this->make_api();
        $this->view = new RecipeCan_View();
        $this->view->options = $this->options;
    }

    public function start() {
        $this->preRunHook();
        $this->run();
    }

    public function has_required_settings() {
        $token = $this->get_option('single_access_token');
        return ($token != '');
    }

    public function request($var) {
        if (array_key_exists($var, $this->options['request'])) {
            return $this->options['request'][$var];
        } else {
            return '';
        }
    }

    public function call() {
        return substr($this->request('page'), strlen($this->options['prefix']));
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