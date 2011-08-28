jQuery(document).ready(function() {

    var clear_classes = function() {
        jQuery('body').removeClass('recipecan_print_recipe_body_wrapper');
        jQuery('body').removeClass('recipecan_print_ingredients_body_wrapper');
    };

    var print_recipe = function(id) {

        jQuery('.recipecan_recipe_show_full').each(function(i, elm) {
            var recipe = jQuery(elm);
            var check_recipe = recipe.hasClass('recipecan_recipe_' + id);

            if (check_recipe) {
                append_to_print_area(recipe);
                window.print();
                return 1; // exit
            }
        });
    };

    var append_to_print_area = function(recipe) {
        if (jQuery('#recipecan_print_area').length == 0) {
            jQuery('<div id="recipecan_print_area" class="recipecan_hidden"></div>').appendTo('body');
        }

        var print_area = jQuery('#recipecan_print_area');
        print_area.html(recipe.html());
    };

    jQuery(".recipecan_print_button").click(function() {
        clear_classes();
        jQuery('body').addClass('recipecan_print_recipe_body_wrapper');
        print_recipe(jQuery(this).data('recipe-id'));
        return false;
    });

    jQuery(".recipecan_print_ingredients_button").click(function() {
        clear_classes();
        jQuery('body').addClass('recipecan_print_ingredients_body_wrapper');
        print_recipe(jQuery(this).data('recipe-id'));
        return false;
    });
});
