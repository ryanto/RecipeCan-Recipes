<?php

require_once 'abstract.php';
require_once 'models/recipes.php';

class RecipeCan_Admin extends RecipeCan_Abstract {

    public function run() {
        add_action('admin_menu', array(&$this, 'admin_menu'));
    }

    public function has_required_settings() {
        $token = $this->get_option('single_access_token');
        return ($token != '');
    }

    public function admin_menu() {

        if (current_user_can('manage_options')) {

            $pages = array(
                'recipecan_settings' => array(
                    'title' => 'Settings',
                    'call' => 'settings'
                ),
                'recipecan_user_info' => array(
                    'title' => 'User Info',
                    'call' => 'user_info'
                ),
            );

            $noshow_pages = array(
                'recipecan_create_account' => array(
                    'title' => 'Create Account',
                    'call' => 'create_account'
                ),
                'recipecan_login' => array(
                    'title' => 'Login to Account',
                    'call' => 'login'
                ),
                'recipecan_recipe' => array(
                    'title' => 'Edit Recipe',
                    'call' => 'recipe'
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

    public function setup() {
        $this->view->render('admin/setup/index');
    }

    public function login() {
        $this->view->render('admin/setup/login');
    }

    public function login_post() {
        // try to login to recipecan
        $this->api->login(array(
            'email' => $this->request('email'),
            'password' => $this->request('password')
        ));

        if ($this->api->failed()) {
            // failed
            $this->view->set('error', $this->api->response['message']);
            $this->view->render('admin/setup/login');
        } else {
            // it worked
            // log the token to the settings table
            $this->add_option('single_access_token', $this->api->response['single_access_token']);
            $this->view->render('admin/setup/complete');
        }
    }

    public function create_account() {
        $single_access_token = $this->get_option('single_access_token');

        if ($single_access_token != '') {
            $this->view->set('message', 'You already have an account.');
            $this->view->render('admin/error');
        } else {
            $this->view->set('name', get_option('blogname'));
            $this->view->set('url', get_option('siteurl'));
            $this->view->render('admin/setup/create');
        }
    }

    public function create_account_post() {
        $single_access_token = $this->get_option('single_access_token');

        if ($single_access_token != '') {
            $this->view->set('message', 'You already have an account.');
            $this->view->render('admin/error');
        } else {

            // try to create
            $this->api->create_account(array(
                'user' => $this->request('user')
            ));

            if ($this->api->failed()) {
                // if fail, reprint with set options
                $this->view->set('errors', $this->api->response['errors']);

                $forms = array(
                    'user' => array(
                        'first_name', 'last_name', 'email', 'login',
                        'password', 'password_confirmation',
                    ),
                    'outside_blog' => array(
                        'name', 'url'
                    )
                );

                foreach ($forms as $form_name => $form_fields) {
                    $submitted_form = $this->request($form_name);
                    foreach ($form_fields as $field) {
                        $this->view->set($field, $submitted_form[$field]);
                    }
                }

                $this->view->render('admin/setup/create');
            } else {
                // success
                // set token
                $this->add_option('single_access_token', $this->api->response['single_access_token']);

                // submit blog info
                $this->api->create_outside_blog(array(
                    'outside_blog' => $this->request('outside_blog')
                ));

                $this->view->render('admin/setup/complete');
            }
        }
    }

    public function user_info() {
        if (!$this->has_required_settings()) {
            $this->setup();
        } else {
            $this->api->user();
        }
    }

    public function recipes() {
        if (!$this->has_required_settings()) {
            $this->setup();
        } else {
            $recipes = new RecipeCan_Models_Recipes();
            $recipes->options = $this->options;
            $recipes->api = $this->api;

            $recipes->download_recipes();

            $this->view->set('recipes', $recipes->recipes());
            $this->view->render('admin/recipes/index');
        }
    }

    public function recipe() {
        $recipes = new RecipeCan_Models_Recipes();
        $recipes->options = $this->options;
        $recipes->api = $this->api;
        
        $recipe = $recipes->find_by_id($this->request('id'));

        if (!$recipe) {
            $this->view->set('message', 'Recipe not found.');
            $this->view->render('admin/error');
        } else {
            $this->view->set_data('recipe', $recipe);
            $this->view->render('admin/recipes/edit');
        }
    }

    public function recipe_put() {
        echo "you are trying to save " . $this->request('id') . "<br><br>";

        $recipes = $this->make_recipes();
        $recipe = $recipes->find_by_id($this->request('id'));

        $data = $this->request('recipe');
        $data['id'] = $recipe['recipecan_id'];

        // try to save via api
        $this->api->update_recipe($data);
        
        // if that works update the db
        if ($this->api->failed()) {
            $this->view->set('error', 'error blah blah');
            // set view from request
            $this->view->set_data('recipe', $this->request('recipe'));
            $this->view->render('admin/recipes/edit');
        } else {

        }

        // reshow form with errors


    }

    public function settings() {
        if (!$this->has_required_settings()) {
            $this->setup();
        } else {
            echo 'settings page';
        }
    }

}

$recipecan_admin = new RecipeCan_Admin();
$recipecan_admin->options = $recipecan_options;
$recipecan_admin->start();
?>