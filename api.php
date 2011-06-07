<?php

class RecipeCan_Api {

    public $options;

    public function call($verb, $url, $args) {
        $wp_remote_args = array(
          'method' => strtoupper($verb),
          'timeout' => 15,
          'body' => $args,
        );
        
        $response = wp_remote_request(
                        'http://' . $this->options['api_server'] . '/api/' . $this->options['api_version'] . '/' . $url,
                        $wp_remote_args
        );

        return $response;
    }

    public function login($args) {
        return $this->call('post', 'users/verify', $args);
    }

}
?>