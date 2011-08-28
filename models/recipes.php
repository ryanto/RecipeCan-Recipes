<?php

require_once 'abstract.php';
require_once $recipecan_options['path'] . '/row/recipe.php';

class RecipeCan_Models_Recipes extends RecipeCan_Models_Abstract {

    protected $_name = 'recipe';
    protected $_table_name = 'recipes';

    protected $_table_columns = array(
        'recipecan_id mediumint(9) not null',
        'post_id mediumint(9)',
        'name varchar(255) not null',

        'course varchar(255) not null',
        'cuisine varchar(255) not null',
        'make_time varchar(255) not null',
        'servings varchar(255) not null',
        'tag_list varchar(255) not null',

        'photo_large varchar(255)',
        'photo_medium varchar(255)',
        'photo_small varchar(255)',
        'rating mediumint(9) not null',
        'slug varchar(255) not null',
        "likes mediumint(9) not null",
        "wp_views mediumint(9) not null",

        "ingredients text not null",
        "directions text not null",

        "created_at datetime not null",
        "make_time_in_seconds mediumint(9)",
    );

    public function after_make_table() {
        if ($this->get_option('single_access_token')) {
            $this->download_recipes();
        }
    }

    public function download_recipes() {

        $this->api->recipes();

        foreach($this->api->response as $data) {
            $this->save($data['recipe'], array('recipecan_id' => $data['recipe']['recipecan_id']));
        }

        foreach($this->all() as $recipe) {
            $recipe->tie_to_post();
        }
    }

    public function courses() {
        return array(
            'Breakfast', 'Lunch', 'Snack', 'Appetizer', 
            'Dinner', 'Side', 'Drink', 'Dessert'
        );
    }

    public function search($term) {

        global $wpdb;

        if (trim($term) == "" || $term == null) {
            return array();
        }

        // find tag, ingredient, direction, course, cuisine
        $where = array(
            'name', 'tag_list', 'ingredients', 'directions', 'course', 'cuisine'
        );

        $sql = array();
        foreach ($where as $column) {
            $sql[] = "`" . mysql_real_escape_string($column) . "` like '%%" . mysql_real_escape_string($term) . "%%'";
        }
        $where_string = implode(" or ", $sql);
        
        
        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM `" . mysql_real_escape_string($this->table_name()) . "`" .
                " WHERE " . $where_string . " order by recipecan_id desc"
            ), ARRAY_A
        );


        $all = array();
        foreach ($results as $data) {
            $all[] = $this->make_row($data);
        }
        return $all;
    }

    public function most_viewed() {
        global $wpdb;

        $results = $wpdb->get_results(
                'select * from `' . mysql_real_escape_string($this->table_name()) . '`' .
                ' order by wp_views desc limit 4',
                ARRAY_A
        );

        $all = array();
        foreach ($results as $data) {
            $all[] = $this->make_row($data);
        }
        return $all;
    }

    public function recipes() {
        return $this->all();
    }


}

?>
