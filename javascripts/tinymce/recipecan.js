(function() {

    tinymce.create('tinymce.plugins.Recipecan', {

        init: function(ed, url) {

            var root_url = url + "/../..";

            ed.addCommand('add_recipe', function() {
                tb_show("Insert Recipe", "admin-ajax.php?action=recipecan&height=300&width=300");
                //tb_show("Insert Recipe", "admin-ajax.php?action=recipecan&height=300&width=300");
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
        jQuery('.recipecan_insert_recipe').live('click', function() {
            var shortcode = '[your-recipe-will-show-here "' + jQuery(this).data('recipe-name') + '" ' + jQuery(this).data('recipe-id') + ']';

            tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
            tb_remove();

            return false;
        });
    })

    tinymce.PluginManager.add('recipecan', tinymce.plugins.Recipecan);

})();