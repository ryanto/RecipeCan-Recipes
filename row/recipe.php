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
            'post_category' => array(),
            //'post_name' => $this->data['slug'],
            'post_status' => 'publish',
            'post_title' => $this->data['name'],
            'post_type' => $this->get_post_type_name(),
            'tags_input' => $this->data['tag_list'],
        );

        var_dump('about to create post');

        if (!isset($this->data['post_id']) || $this->data['post_id'] == NULL) {
            $post_id = wp_insert_post($post);
            $this->data['post_id'] = $post_id;
            $this->save();
        } else {
            $post['ID'] = $this->data['id'];
            wp_update_post($post);
        }

    }

}

?>