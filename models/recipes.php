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
        'photo_small varchar(255)',
        'rating mediumint(9) not null',
        'slug varchar(255) not null',
        "likes mediumint(9) not null",

        "ingredients text not null",
        "directions text not null",

        "created_at datetime not null",
        "make_time_in_seconds mediumint(9)",
    );


    public function download_recipes() {
        $this->api->recipes();

        foreach($this->api->response as $data) {
            $this->save($data['recipe'], array('recipecan_id' => $data['recipe']['recipecan_id']));
        }

        foreach($this->all() as $recipe) {
            $recipe->tie_to_post();
        }
    }

    public function recipes() {
        return $this->all();
    }




    

}

?>