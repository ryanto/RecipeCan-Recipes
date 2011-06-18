<?php

require 'Zend/Json.php';

class RecipeCan_Api {

    public $options;

    public function call($verb, $url, $args) {

        echo "<b>request</b><br/>";
        var_dump($args);
        echo "<br/><br/>";

        $wp_remote_args = array(
            'method' => strtoupper($verb),
            'timeout' => 25,
            'body' => $args,
        );

        $response = wp_remote_request(
                        'http://' . $this->options['api_server'] . '/api/' .
                        $this->options['api_version'] . '/' . $url . '.js' .
                        '?user_credentials=' . get_option('recipecan_single_access_token'),
                        $wp_remote_args
        );


        echo "<b>raw response</b><br/>";
        var_dump($response['body']);
        echo "<br/><br/>";

        $json_as_array = Zend_Json::decode($response['body']);

        echo "<b>processed response</b><br/>";
        var_dump($json_as_array);
        echo "<br/><br>";

        return $json_as_array;
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

}

?>