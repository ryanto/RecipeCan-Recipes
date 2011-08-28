<?php

require_once 'abstract.php';

class RecipeCan_Binders_Setup extends RecipeCan_Binders_Abstract {

    public function run() {
        add_action('init', array(&$this, 'post_type'));
        add_action('init', array(&$this, 'create_page'));
        add_action('init', array(&$this, 'no_name_clashes'));
        // ensure tables
        $recipes = $this->make_recipes();
        $recipes->ensure_table();
    }

    /**
     * Some older versions of the plugin create a page and
     * a posttype with the same name.  This aims to correct that.
     */
    public function no_name_clashes() {

        $page_name = $this->get_page_name();
        $post_type_name = $this->get_post_type_name();

        if ($page_name == $post_type_name && $page_name != null && $this->get_option('created_page')) {

            // delete the page
            global $wpdb;
            $sql = "delete from " . $wpdb->posts . " where post_name = '" . 
                mysql_real_escape_string($page_name) . "'";

            $wpdb->query($sql); 

            // unset the page
            $this->delete_option('page_name');
            $this->delete_option('created_page');
            
            // create a new page
            $this->create_page();
        }

    }

    public function post_type() {
        $labels = array(
            'name' => 'Recipes',
            'singular_name' => 'Recipe',
            'add_new' => 'Add New',
            'add_new_item' => 'Add New Recipe',
            'edit_item' => 'Edit Recipe',
            'new_item' => 'New Recipe',
            'all_items' => 'All Recipes',
            'view_item' => 'View Recipe',
            'search_items' => 'Search Recipes',
            'not_found' => 'No recipes found',
            'not_found_in_trash' => 'No recipes found in Trash',
            'parent_item_colon' => '',
            'menu_name' => 'Recipes'
        );
        $args = array(
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => false,
            'show_in_menu' => false,
            'query_var' => true,
            'rewrite' => true,
            'capability_type' => 'post',
            'has_archive' => true,
            'hierarchical' => false,
            'menu_position' => null,
            'supports' => array('tags', 'comments')
        );

        register_post_type($this->get_post_type_name(), $args);

        flush_rewrite_rules();
    }

    public function generate_page_name($postfix = null) {
        foreach ($this->options['recipe_page_names'] as $key => $name) {
            $try_name = $name . $postfix;
            if (!get_page_by_path($try_name) && $try_name != $this->get_post_type_name()) {
                return $try_name;
            }
        }
        // out of names to try
        return $this->generate_page_name(rand(0, 100));
    }

    public function get_page_name() {
        $option = $this->get_option('page_name');
        if (!$option) {
            $option = $this->generate_page_name();
            $this->add_option('page_name', $option);
        }
        return $option;
    }

    public function create_page() {

        $already_created_page = $this->get_option('created_page');

        if (!$already_created_page) {
            global $wpdb, $user_ID;

            $wpdb->query(
                    "
                    INSERT INTO " . $wpdb->posts . "
                        ( post_author, post_date, post_date_gmt, post_content, post_title,
                        post_excerpt, post_status, comment_status, ping_status, post_password,
                        post_name, to_ping, pinged, post_modified, post_modified_gmt,
                        post_content_filtered, post_parent, guid, menu_order, post_type,
                        post_mime_type, comment_count )
                    VALUES (
                        '" . $user_ID . "', '" . current_time('mysql') . "', '" . current_time('mysql') .
                    "', '[recipecan-list-recipes]', 'Recipes', '', 'publish', 'closed', 'closed', '', '" . $this->get_page_name() . "', '', '', '" .
                    current_time('mysql') . "', '" . current_time('mysql') . "', '', 0, '', 0, 'page',
                        '', 0 )"
            );

            $this->add_option('created_page', true);
        }
    }

}

$recipecan_setup = new RecipeCan_Binders_Setup();
$recipecan_setup->options = $recipecan_options;
$recipecan_setup->start();
?>
