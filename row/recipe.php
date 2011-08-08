<?php

require_once 'abstract.php';

class RecipeCan_Row_Recipe extends RecipeCan_Row_Abstract {

    protected $_name = 'recipes';

    public function tie_to_post() {
        global $user_ID;

        $post = array(
            'comment_status' => 'open',
            'ping_status' => 'closed',
            'post_author' => $user_ID,
            'post_content' => '',
            //'post_category' => array(0),
            //'post_name' => $this->data['slug'],
            'post_status' => 'publish',
            'post_title' => $this->data['name'],
            'post_type' => $this->get_post_type_name(),
            'tags_input' => $this->data['tag_list'],
        );

        if (!isset($this->data['post_id']) || $this->data['post_id'] == NULL) {
            $post_id = wp_insert_post($post);
            $this->data['post_id'] = $post_id;
            $this->save();
        } else {
            $post['ID'] = $this->data['post_id'];
            wp_update_post($post);
        }

    }

    public function safe_name() {
        return str_replace("\"", "", $this->get('name'));
    }

    public function link() {
        return get_permalink($this->data['post_id']);
    }

    public function has_image() {
        return ($this->get('photo_large') != '');
    }

    public function image($size = 'large') {
        return "http://" . $this->options['image_server'] . $this->get('photo_' . $size);
    }

    public function ingredients() {
        return $this->data_to_a('ingredients');
    }

    public function directions() {
        return $this->data_to_a('directions');
    }

    public function data_to_a($field) {
        $text = $this->get($field);
        $text = preg_replace("/[\r\n]{2,}/", "\n", $text);
        $as_a = split("\n", $text);

        return $as_a;
    }

}

?>