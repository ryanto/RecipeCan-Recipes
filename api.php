<?php

require 'Zend/Json.php';

class RecipeCan_Api extends RecipeCan_Abstract {

    public $response;

    public function call($verb, $url, $args = array(), $headers = array()) {

        set_time_limit(0);

        $debug = false;

        $api_key = $this->get_option('single_access_token');

        if ($debug) {
            echo "<b>request</b><br/>";
            var_dump($args);
            echo "<br/><br/>";
        }

        $request_url = 'http://' . $this->options['api_server'] . '/api/' .
                $this->options['api_version'] . '/' . $url . '.js';

        if ($api_key != '') {
            $request_url .= '?user_credentials=' . $api_key;

            if ($debug) {
                echo "<b>api key</b><br>";
                var_dump($api_key);
                echo "<br><br>";
            }
            
        }

        if ($debug) {
            echo "<b>request url</b><br>";
            echo $request_url;
            echo "<br><br>";
        }

        $wp_remote_args = array(
            'headers' => $headers,
            'method' => strtoupper($verb),
            'timeout' => 25,
            'body' => $args,
        );


        $response = wp_remote_request($request_url, $wp_remote_args);

        if ($debug) {
            echo "<b>raw response</b><br/>";
            var_dump($response['body']);
            echo "<br/><br/>";
        }

        $json_as_array = Zend_Json::decode($response['body']);

        if ($debug) {
            echo "<b>processed response</b><br/>";
            var_dump($json_as_array);
            echo "<br/><br>";
        }

        $this->response = $json_as_array;
        return $json_as_array;
    }

    public function failed() {
        return array_key_exists('failed', $this->response);
    }

    public function errors() {
        if ($this->failed()) {
            return $this->response['error'];
        } else {
            return null;
        }
    }

    public function success() {
        return!$this->failed();
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

    public function update_recipe($args) {
        return $this->call('put', 'recipes/' . $args['id'], array(
            'recipe' => $args
        ));
    }

    public function create_recipe($args) {
        return $this->call('post', 'recipes', array(
           'recipe' => $args
        ));
    }

    public function create_recipe_photo($args) {

        $handle = fopen($args['filename'], "rb");
        $contents = fread($handle, filesize($args['filename']));
        fclose($handle);

        $headers = array(
            'Content-Type' => 'multipart/form-data; boundary=RecipeCanUploadBound',
        );

        $payload = array(
            "--RecipeCanUploadBound",
            "Content-Disposition: form-data; name=\"photo[photo]\"; filename=\"image.jpg\"",
            "Content-Transfer-Encoding: binary",
            "Content-Type: image/jpeg",
            "",
            $contents,
            "--RecipeCanUploadBound--",
        );

        $this->call(
                'post',
                'recipes/' . $args['recipe_id'] . '/photos',
                implode("\r\n", $payload),
                $headers
        );
    }

    public function recipes() {
        $this->call('get', 'recipes');
    }

}

?>