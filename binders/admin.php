<?php

require_once 'abstract.php';

class RecipeCan_Binders_Admin extends RecipeCan_Binders_Abstract {

    public function run() {
        add_action('admin_menu', array(&$this, 'admin_menu'));
    }

    public function admin_menu() {

        if (current_user_can('manage_options')) {

            $pages = array(
                'recipecan_new_recipe' => array(
                    'title' => 'Add Recipe',
                    'call' => 'new_recipe'
                ),
                /*
                  'recipecan_settings' => array(
                  'title' => 'Settings',
                  'call' => 'settings'
                  ),
                 *
                 */
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
                    'title' => 'Recipe',
                    'call' => 'recipe'
                ),
                'recipecan_edit_recipe' => array(
                    'title' => 'Recipe',
                    'call' => 'edit_recipe'
                ),
                'recipecan_delete_recipe' => array(
                    'title' => 'Delete Recipe',
                    'call' => 'delete_recipe'
                ),
                'recipecan_recipe_photo' => array(
                    'title' => 'Recipe Photo',
                    'call' => 'recipe_photo'
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
            $this->view->errors($this->api->errors());
            $this->view->render('admin/setup/login');
        } else {
            // it worked
            // log the token to the settings table
            if ($this->get_option('single_access_token') === false) {
                $this->add_option('single_access_token', $this->api->response['user']['single_access_token']);
            } else {
                $this->update_option('single_access_token', $this->api->response['user']['single_access_token']);
            }
            $this->view->render('admin/setup/complete');
        }
    }

    public function create_account() {
        $single_access_token = $this->get_option('single_access_token');

        if ($single_access_token != '') {
            $this->view->set('message', 'You already have an account.');
            $this->view->render('admin/error');
        } else {
            $this->view->set('blog', array(
                'name' => get_option('blogname'),
                'url' => get_option('siteurl')
            ));
            $this->view->set('user', array(
                'email' => get_option('admin_email')
            ));
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
                $this->view->errors($this->api->errors());

                $this->view->set('user', $this->request('user'));
                $this->view->set('blog', $this->request('outside_blog'));

                $this->view->render('admin/setup/create');
            } else {
                // success
                // set token
                if ($this->get_option('single_access_token') === false) {
                    $this->add_option('single_access_token', $this->api->response['user']['single_access_token']);
                } else {
                    $this->update_option('single_access_token', $this->api->response['user']['single_access_token']);
                }

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
            $this->view->set('user', $this->api->response['user']);
            $this->view->render('admin/user/index');
        }
    }

    public function recipes() {
        if (!$this->has_required_settings()) {
            $this->setup();
        } else {
            $recipes = $this->make_recipes();
            $recipes->download_recipes();

            $this->view->set('recipes', $recipes->all());
            $this->view->render('admin/recipes/index');
        }
    }

    public function new_recipe() {
        if (!$this->has_required_settings()) {
            $this->setup();
        } else {
            $this->view->set('form_submit_url', '?page=recipecan_recipes&method=post');
            $this->view->set('form_show_photo_field', true);
            $this->view->render('admin/recipes/new');
        }
    }

    /**
     * Does 2 round trips - change to 1
     *
     * First is to post the recipe, just text+validation
     * Second is to upload the photo.
     */
    public function recipes_post() {
        $data = $this->request('recipe');

        $this->api->create_recipe($data);

        if ($this->api->failed()) {
            $this->view->errors($this->api->response['error']);
            $this->view->set('recipe', $this->request('recipe'));
            $this->new_recipe();
        } else {
            // save the recipe locally
            $recipes = $this->make_recipes();
            $recipes->save($this->api->response['recipe']);

            // create a post
            $recipecan_id = $this->api->response['recipe']['recipecan_id'];
            $recipe = $recipes->find(array('recipecan_id' => $recipecan_id));

            $recipe->tie_to_post();

            // upload photo if the user submitted one
            if (isset($_FILES['photo']['tmp_name']) && $_FILES['photo']['tmp_name'] != "") {
                $this->api->create_recipe_photo(array(
                    'recipe_id' => $recipecan_id,
                    'filename' => $_FILES['photo']['tmp_name']
                ));

                if ($this->api->success()) {
                    // save the photo urls
                    $recipes->save($this->api->response['recipe'], array('id' => $recipe->data['id']));
                }
            }

            // show saved
            $this->view->set('saved', true);
            $this->view->render('admin/recipes/saved');
        }
    }

    public function edit_recipe() {
        $recipes = $this->make_recipes();
        $recipe = $recipes->find_by_id($this->request('id'));

        $this->view->set('form_submit_url', '?page=recipecan_recipe&id=' . $recipe->get('id') . '&method=put');
        $this->view->set('form_show_photo_field', false);

        if (!$recipe) {
            $this->view->set('message', 'Recipe not found.');
            $this->view->render('admin/error');
        } else {
            if ($this->view->get('recipe') == NULL) {
                $this->view->set('recipe', $recipe);
            }

            $this->view->render('admin/recipes/edit');
        }
    }

    public function delete_recipe() {
        $recipes = $this->make_recipes();
        $recipe = $recipes->find_by_id($this->request('id'));

        if (!$recipe) {
            $this->view->set('message', 'Recipe not found.');
            $this->view->render('admin/error');
        } else {
            // encap this in the model!
            $this->api->delete_recipe(array(
                'id' => $recipe->get('recipecan_id')
            ));

            $recipe->delete();
            $this->view->render('admin/recipes/delete');
        }
    }

    public function recipe_put() {
        $recipes = $this->make_recipes();
        $recipe = $recipes->find_by_id($this->request('id'));

        $recipe->set_array($this->request('recipe'));

        // encap this in the model!
        $data = $this->request('recipe');
        $data['id'] = $recipe->get('recipecan_id');
        $this->api->update_recipe($data);


        if ($this->api->failed()) {
            $this->view->errors($this->api->response['error']); 
            $this->view->set('recipe', $recipe);
            $this->edit_recipe();
        } else {
            // it worked
            // shouldnt we save self?
            $recipes->save($this->api->response['recipe'], array('id' => $this->request('id')));
            $this->view->render('admin/recipes/saved');
        }
    }

    public function recipe_photo() {
        $recipes = $this->make_recipes();
        $recipe = $recipes->find_by_id($this->request('id'));

        if (!$recipe) {
            $this->view->set('message', 'Recipe not found.');
            $this->view->render('admin/error');
        } else {
            $this->view->set('recipe', $recipe);
            $this->view->render('admin/recipe_photo/show');
        }
    }

    public function recipe_photo_post() {

        $recipes = $this->make_recipes();
        $recipe = $recipes->find_by_id($this->request('id'));

        $this->api->create_recipe_photo(array(
            'recipe_id' => $recipe->get('recipecan_id'),
            'filename' => $_FILES['recipecan_file']['tmp_name']
        ));

        if ($this->api->failed()) {
            $this->view->set('error', $this->api->response['error']);
        } else {
            // save
            $recipes->save($this->api->response['recipe'], array('id' => $this->request('id')));
            // reload
            $recipe = $recipes->find_by_id($this->request('id'));
            
            $this->view->set('saved', true);
        }

        $this->view->set('recipe', $recipe);
        $this->view->render('admin/recipe_photo/show');
    }

    public function settings() {
        if (!$this->has_required_settings()) {
            $this->setup();
        } else {
            echo 'settings page';
        }
    }

}

$recipecan_admin = new RecipeCan_Binders_Admin();
$recipecan_admin->options = $recipecan_options;
$recipecan_admin->start();
?>
