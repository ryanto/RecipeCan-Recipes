<?php

require 'Zend/Json.php';

class RecipeCan_Api {

    public $options;

    public $response;

    public function call($verb, $url, $args = array()) {

        $api_key = get_option('recipecan_single_access_token');

        echo "<b>request</b><br/>";
        var_dump($args);
        echo "<br/><br/>";

        $request_url = 'http://' . $this->options['api_server'] . '/api/' .
                $this->options['api_version'] . '/' . $url . '.js';

        if ($api_key != '') {
            $request_url .= '?user_credentials=' . $api_key;

            echo "<b>api key</b><br>";
            var_dump($api_key);
            echo "<br><br>";
        }

        $wp_remote_args = array(
            'method' => strtoupper($verb),
            'timeout' => 25,
            'body' => $args,
        );

        $response = wp_remote_request($request_url, $wp_remote_args);


        echo "<b>raw response</b><br/>";
        var_dump($response['body']);
        echo "<br/><br/>";

        $json_as_array = Zend_Json::decode($response['body']);

        echo "<b>processed response</b><br/>";
        var_dump($json_as_array);
        echo "<br/><br>";

        $this->response = $json_as_array;
        return $json_as_array;
    }
    
    public function failed() {
        return array_key_exists('failed', $this->response);
    }

    public function success() {
        return !$this->failed();
    }

    public function login($args) {
        $send_args = array(
            'user_session[login]' => $args['email'],
            'user_session[password]' => $args['password']
        );
        return $this->call('post', 'users/verify', $send_args);
    }

    public function create_account($args) {
        return $this->call('post', 'users', $args);
    }

    public function user() {
        return $this->call('get', 'users', array());
    }

    public function create_outside_blog($args) {
        return $this->call('post', 'outside_blogs', $args);
    }

    public function update_outside_blog($args) {
        return $this->call('put', 'outside_blogs/' . $args['id'], $args);
    }

    public function recipes() {
        $this->call('get', 'recipes');
    }

}

?>