<?php

$title = '';
$description = '';
$featured_image = '';
$excerpt = '';
$recipe_video = '';
$recipe_images = array();
$recipe_yield = '';
$recipe_servings = '';
$recipe_prep_time = '';
$recipe_cook_time = '';
$recipe_ready_in = '';
$recipe_ingredient = '';
$recipe_nutritions = '';
$recipe_difficulty = '';
$recipe_steps = '';
$recipe_category = array();
$recipe_cuisine = array();
$recipe_tags = array();


if( $page == 'edit_recipe' ){
    $recipe_id = isset( $_GET['recipe_id'] ) ? $_GET['recipe_id'] : '';
    $recipe = get_post( $recipe_id );
    $title = $recipe->post_title;
    $description = $recipe->post_content;
    $featured_image = get_post_thumbnail_id( $recipe_id );
    $excerpt = $recipe->post_excerpt;
    $recipe_video = get_post_meta( $recipe_id, 'recipe_video', true );
    $recipe_images = recipe_smeta_images( 'recipe_images', $recipe_id, array() );
    $recipe_yield = get_post_meta( $recipe_id, 'recipe_yield', true );
    $recipe_servings = get_post_meta( $recipe_id, 'recipe_servings', true );
    $recipe_prep_time = get_post_meta( $recipe_id, 'recipe_prep_time', true );
    $recipe_cook_time = get_post_meta( $recipe_id, 'recipe_cook_time', true );
    $recipe_ready_in = get_post_meta( $recipe_id, 'recipe_ready_in', true );
    $recipe_ingredient = get_post_meta( $recipe_id, 'recipe_ingredient', true );
    $recipe_nutritions = get_post_meta( $recipe_id, 'recipe_nutritions', true );
    $recipe_difficulty = get_post_meta( $recipe_id, 'recipe_difficulty', true );
    $recipe_steps = get_post_meta( $recipe_id, 'recipe_steps', true );
    $recipe_category = get_the_terms( $recipe_id, 'recipe-category' );
    $recipe_category = recipe_get_deepest_taxonomy_term( $recipe_category );
    $recipe_category = $recipe_category->term_id;
    $recipe_cuisine = get_the_terms( $recipe_id, 'recipe-cuisine' );
    $recipe_cuisine = recipe_get_deepest_taxonomy_term( $recipe_cuisine );
    $recipe_cuisine = $recipe_cuisine->term_id;
    $recipe_tags_terms = get_the_terms( $recipe_id, 'recipe-tag' );
    if( !empty( $recipe_tags_terms ) ){
        foreach( $recipe_tags_terms as $tag ){
            $recipe_tags[] = $tag->slug;        
        }
    }
}

if( $page == 'new_recipe' ){
    $extra_fields = '<input type="hidden" value="new" name="subaction">';
?>
    <h4 class="no-top-margin"><?php _e( 'Submit New Recipe', 'recipe' ); ?></h4>
    <p><?php _e( 'Populate fields below and after review you will receive response', 'recipe' ) ?></p>
<?php
}
else{
    $extra_fields = '<input type="hidden" value="edit" name="subaction"><input type="hidden" name="recipe_id" value="'.esc_attr( $recipe_id ).'">';
?>
    <h4 class="no-top-margin"><?php _e( 'Edit Recipe: ', 'recipe' ); echo get_the_title( $recipe_id ); ?></h4>
    <p><?php _e( 'Edit recipe details which will be approved or rejected after the review', 'recipe' ) ?></p>
<?php
}
?>
<hr />
<form method="post">
    <div class="form-group">
        <label for="title"><?php _e( 'Title *', 'recipe' ); ?></label>
        <input type="text" name="title" id="title" value="<?php echo esc_attr( $title ) ?>" class="form-control">
    </div>

    <div class="form-group">
        <label for="desciption"><?php _e( 'Description *', 'recipe' ); ?></label>
        <?php wp_editor( $description, 'description' ) ?>
    </div>

    <div class="form-group">
        <label for="featured_image"><?php _e( 'Featured Image *', 'recipe' ); ?></label>
        <input type="hidden" name="featured_image" id="featured_image" value="<?php echo esc_attr( $featured_image ) ?>">
        <div class="upload-image-wrap featured-image-wrap">
            <?php echo wp_get_attachment_image( $featured_image, 'thumbnail' ); ?>
        </div>
        <a href="javascript:;" class="image-upload featured-image"><?php _e( 'Select Image', 'recipe' ) ?></a>
    </div>

    <div class="form-group">
        <label for="excerpt"><?php _e( 'Recipe Excerpt *', 'recipe' ); ?></label>
        <textarea class="form-control" name="excerpt" id="excerpt"><?php echo $excerpt; ?></textarea>
    </div>

    <div class="form-group">
        <label for="recipe_video"><?php _e( 'Video URL', 'recipe' ); ?></label>
        <input type="text" name="recipe_video" id="recipe_video" value="<?php echo esc_attr( $recipe_video ) != '' ? esc_attr( $recipe_video ) : 'https://www.youtube.com?watch=' ?>123" class="form-control">
    </div> 

    <div class="form-group">
        <label for="recipe_images"><?php _e( 'Recipe Images', 'recipe' ); ?></label>
        <input type="hidden" name="recipe_images" id="recipe_images" value="<?php echo esc_attr( join( ",", $recipe_images ) ) ?>">
        <div class="recipe-images-wrap">
            <?php
            if( !empty( $recipe_images ) ){
                foreach( $recipe_images as $image_id ){
                    if( !empty( $image_id ) ){
                        echo '<div class="upload-image-wrap recipe-image-wrap" data-image_id="'.esc_attr( $image_id ).'">'.wp_get_attachment_image( $image_id, 'thumbnail' ).'<a href="javascript:;" class="remove-recipe-image"><i class="fa fa-close"></i></a></div>';
                    }
                }
            }
            ?>
        </div>
        <a href="javascript:;" class="image-upload recipe-images"><?php _e( 'Select Images', 'recipe' ) ?></a>
    </div>

    <div class="form-group">
        <label for="recipe_yield"><?php _e( 'Yield *', 'recipe' ); ?></label>
        <input type="text" name="recipe_yield" id="recipe_yield" value="<?php echo esc_attr( $recipe_yield ) ?>" class="form-control">
    </div>

    <div class="form-group">
        <label for="recipe_servings"><?php _e( 'Servings *', 'recipe' ); ?></label>
        <input type="text" name="recipe_servings" id="recipe_servings" value="<?php echo esc_attr( $recipe_servings ) ?>" class="form-control">
    </div>

    <div class="form-group">
        <label for="recipe_prep_time"><?php _e( 'Time To Prepare *', 'recipe' ); ?></label>
        <?php recipe_select_time( $recipe_prep_time, 'recipe_prep_time' ); ?>
    </div>

    <div class="form-group">
        <label for="recipe_cook_time"><?php _e( 'Time To Cook *', 'recipe' ); ?></label>
        <?php recipe_select_time( $recipe_cook_time, 'recipe_cook_time' ); ?>
    </div>

    <div class="form-group">
        <label for="recipe_ready_in"><?php _e( 'Ready Time *', 'recipe' ); ?></label>
        <?php recipe_select_time( $recipe_ready_in, 'recipe_ready_in' ); ?>
    </div>

    <div class="form-group">
        <label for="recipe_ingredient"><?php _e( 'Ingredients. To add a title wrap it in # for example #For Pudding# *', 'recipe' ); ?></label>
        <textarea class="form-control" name="recipe_ingredient" id="recipe_ingredient" rows="10"><?php echo $recipe_ingredient; ?></textarea>
    </div>

    <div class="form-group">
        <label for="recipe_nutritions"><?php _e( 'Input nutritions one per line where title and value are separated with : ( Calories:3400ckal )', 'recipe' ); ?></label>
        <textarea class="form-control" name="recipe_nutritions" id="recipe_nutritions"><?php echo $recipe_nutritions; ?></textarea>
    </div>    

    <div class="form-group">
        <label for="recipe_steps"><?php _e( 'Steps ( separated with -- To add another block of steps separate with title wrapped in # like #For Cream# ) *', 'recipe' ); ?></label>
        <?php wp_editor( $recipe_steps, 'recipe_steps' ) ?>
    </div>

    <div class="form-group">
        <label for="recipe_difficulty"><?php _e( 'Recipe Difficulty *', 'recipe' ); ?></label>
        <select name="recipe_difficulty" id="recipe_difficulty" class="form-control">
            <option value=""><?php _e( '- Select -', 'recipe' ) ?></option>
            <option value="easy" <?php echo $recipe_difficulty == 'easy' ? esc_attr( 'selected="selected"' ) : '' ?>><?php _e( 'Easy', 'recipe' ) ?></option>
            <option value="medium" <?php echo $recipe_difficulty == 'medium' ? esc_attr( 'selected="selected"' ) : '' ?>><?php _e( 'Medium', 'recipe' ) ?></option>
            <option value="advanced" <?php echo $recipe_difficulty == 'advanced' ? esc_attr( 'selected="selected"' ) : '' ?>><?php _e( 'Advanced', 'recipe' ) ?></option>
        </select>
    </div>

    <div class="form-group">
        <label for="recipe_category"><?php _e( 'Recipe Category *', 'recipe' ); ?></label>
        <select name="recipe_category" id="recipe_category" class="form-control">
            <option value=""><?php _e( '- Select -', 'recipe' ) ?></option>
            <?php
            $categories = recipe_get_organized( 'recipe-category', false );
            if( !empty( $categories ) ){
                foreach( $categories as $category ){
                    recipe_display_select_tree( $category, $recipe_category );
                }
            }
            ?>
        </select>
    </div>

    <div class="form-group">
        <label for="recipe_cuisine"><?php _e( 'Recipe Cuisine *', 'recipe' ); ?></label>
        <select name="recipe_cuisine" id="recipe_cuisine" class="form-control">
            <option value=""><?php _e( '- Select -', 'recipe' ) ?></option>
            <?php
            $cuisines = recipe_get_organized( 'recipe-cuisine', false );
            if( !empty( $cuisines ) ){
                foreach( $cuisines as $cuisine ){
                    recipe_display_select_tree( $cuisine, $recipe_cuisine );
                }
            }
            ?>
        </select>
    </div>

    <div class="form-group">
        <label for="recipe_tags"><?php _e( 'Tags ( comma separated )', 'recipe' ); ?></label>
        <input type="text" name="recipe_tags" id="recipe_tags" value="<?php echo esc_attr( join( ",", $recipe_tags ) ) ?>" class="form-control">
    </div>    

    <?php echo $extra_fields; ?>
    <input type="hidden" value="save_recipe" name="action" />
    <input type="hidden" value="<?php echo esc_attr( $current_user->ID ) ?>" name="user_id" />
    <?php wp_nonce_field('recipe','recipe_field'); ?>

    <div class="send_result"></div>

    <a href="javascript:;" class="btn submit-form">
        <?php 
        if( $page == 'new_recipe' ){
            _e( 'Submit Recipe', 'recipe' ); 
        }
        else{
            _e( 'Update Recipe', 'recipe' );    
        }
        ?>
    </a>
</form>