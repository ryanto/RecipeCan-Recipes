<?php

require_once 'abstract.php';

class RecipeCan_Binders_Editor extends RecipeCan_Binders_Abstract {

    public function run() {
        add_filter('mce_external_plugins', array(&$this, 'plugin'));
        add_filter('mce_buttons', array(&$this, 'button'));

        add_action('wp_ajax_recipecan', array(&$this, 'select_recipe'));
    }

    public function plugin($plugins) {
        $plugins['recipecan'] = $this->options['plugin_url'] . 'javascripts/tinymce/recipecan.js';
        return $plugins;
    }

    public function button($buttons) {
        array_push($buttons, "separator", "recipecan");
        return $buttons;
    }

    public function select_recipe() {
        $recipes = $this->make_recipes();

        $this->view->set('recipes', $recipes->all_data());
        $this->view->render('editor/index');
        die();
    }

}

$recipecan_setup = new RecipeCan_Binders_Editor();
$recipecan_setup->options = $recipecan_options;
$recipecan_setup->start();
?>