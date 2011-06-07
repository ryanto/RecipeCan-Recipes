<?php

require 'abstract.php';

class RecipeCan_Admin extends RecipeCan_Abstract {

    public function run() {
        add_action('admin_menu', array(&$this, 'admin_menu'));
    }

    public function has_required_settings() {
        return false;
    }

    public function admin_menu() {

        if (current_user_can('manage_options')) {

            $pages = array(
                'recipecan_settings' => array(
                    'title' => 'Settings',
                    'call' => 'settings'
                )
            );

            $noshow_pages = array(
                'recipecan_create_account' => array(
                    'title' => 'Create Account',
                    'call' => 'create_account'
                ),
                'recipecan_login' => array(
                    'title' => 'Login to Account',
                    'call' => 'login'
                )
            );

            add_menu_page(
                    'RecipeCan_Menu',
                    'Recipes',
                    'manage_options',
                    'recipecan_recipes',
                    array(&$this, 'router'),
                    $this->options['plugin_url'] . '/images/icon.png'
            );

            foreach ($noshow_pages as $slug => $data) {
                add_submenu_page(
                        'RecipeCan_NoShow_Menu',
                        $data['title'] . ' | RecipeCan Recipes',
                        'no-show-menu',
                        'manage_options',
                        $slug,
                        array($this, 'router')
                );
            }

            foreach ($pages as $slug => $data) {
                add_submenu_page(
                        'recipecan_recipes',
                        $data['title'] . " | RecipeCan Recipes",
                        $data['title'],
                        'manage_options',
                        $slug,
                        array(&$this, 'router')
                );
            }
        }
    }

    public function settings() {
        echo 'settings page';
    }

    public function setup() {
        $this->view->render('admin/setup');
    }

    public function login() {
        $this->view->render('admin/login');
    }

    public function login_post() {
        // try to login to recipecan
        $login = $this->api->login(array(
            'email' => $this->request('login'),
            'password' => $this->request('password')
        ));

        // if fail redirect back show message
        // get access key
        // save it to db
        //
    }

    public function create_account() {
        echo 'called';
    }

    public function recipes() {
        if ($this->has_required_settings()) {
            echo 'hello world';
        } else {
            $this->setup();
        }
    }

}

$recipecan_admin = new RecipeCan_Admin();
$recipecan_admin->options = $recipecan_options;
$recipecan_admin->start();
?>