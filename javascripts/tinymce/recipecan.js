(function() {

    tinymce.create('tinymce.plugins.Recipecan', {

        init: function(ed, url) {

            var root_url = url + "/../..";

            ed.addCommand('add_recipe', function() {
                tb_show("Insert Recipe", "admin-ajax.php?action=recipecan");
            });

            ed.addButton('recipecan', {
                title : 'Insert Recipes',
                cmd : 'add_recipe',
                image : root_url + '/images/icon_20.png'
            });
        },

        createControl : function(n, cm) {
            return null;
        },

        getInfo : function() {
            return {
                longname : 'RecipeCan Plugin',
                author : 'RecipeCan.com',
                authorurl : 'http://www.recipecan.com',
                infourl : 'http://www.recipecan.com',
                version : "1.0"
            };
        }
    });

    jQuery(document).ready(function() {

        jQuery('#recipecan_add_new_recipe a').live('click', function() {
            jQuery('#recipecan_add_new_recipe').toggle();
            jQuery('#recipecan_add_new_recipe_box').toggle();
            jQuery('#recipecan_insert_existing_recipe_box').toggle();

            jQuery("#TB_ajaxContent").css("width", "auto");
            jQuery("#TB_ajaxContent").css("height", jQuery('#TB_window').height() - 50);

            return false;
        });

        jQuery('.recipecan_insert_recipe').live('click', function() {
            var shortcode = '[your-recipe-will-show-here "' + jQuery(this).data('recipe-name') + '" ' + jQuery(this).data('recipe-id') + ']';

            tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
            tb_remove();

            return false;
        });

        jQuery('#recipecan_new_recipe_form').live('submit', function() {

            var form = jQuery(this);
            
            jQuery("#recipecan_loading").show();
            jQuery("#recipecan_form_wrapper").hide();

            jQuery(form).ajaxSubmit({
                target: '#recipecan_form_submit_div',
                iframe: true,
                success: function(response, textStatus, xhr) {
                    jQuery("#TB_ajaxContent").css("width", "auto");
                    jQuery("#TB_ajaxContent").css("height", jQuery('#TB_window').height() - 50);
                }

            });

            return false;
        });

    })

    tinymce.PluginManager.add('recipecan', tinymce.plugins.Recipecan);

})();