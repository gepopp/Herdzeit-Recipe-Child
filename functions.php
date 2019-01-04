<?php

define('SHOPPING_LIST_TABLE', 'reciepe_shopping_lists');
define('SHOPPING_LIST_EXTRAS_TABLE', 'shopping_list_extras');


if(file_exists(get_stylesheet_directory() . '/shopping-list/list-actions.php')){
    require_once get_stylesheet_directory() . '/shopping-list/list-actions.php';
}else{
    echo 'fnf';
}

foreach(glob(get_stylesheet_directory() . '/acf/*') as $file){
    if(file_exists($file)){
        require_once $file;
    }else{
        wp_die('FNF ACF');
    }
}


add_image_size( 'frontpage-slider-two-to-one', 1000, 500 );


add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles', 3 );
function theme_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
    wp_register_script('wunderlist', get_stylesheet_directory_uri() . '/js/wunderlist.js');
    if(isset($_GET['page']) && $_GET['page'] == 'edit-ingredient'){

        global $wpdb;
        $user = $wpdb->get_row('SELECT * FROM ' . SHOPPING_LIST_TABLE . ' WHERE ID = ' . $_GET['list-id']);
        if($user->user_id == get_current_user_id()){
            wp_localize_script('wunderlist', 'wunderlist', array('hz_list' => $user->ID, 'wunderlist_id' => $user->wunderlist_id));
        }
        wp_enqueue_script('wunderlist');
    }
    wp_register_script('wunderlist-edit-page', get_stylesheet_directory_uri() . '/js/wunderlist-edit-page.js');
    if(isset($_GET['page']) && $_GET['page'] == 'edit-shopping-list'){

        global $wpdb;
        $user = $wpdb->get_row('SELECT * FROM ' . SHOPPING_LIST_TABLE . ' WHERE ID = ' . $_GET['list-id']);
        if($user->user_id == get_current_user_id()){
            wp_localize_script('wunderlist-edit-page', 'wunderlist', array('hz_list' => $user->ID, 'wunderlist_id' => $user->wunderlist_id));
        }else{
            wp_localize_script('wunderlist-edit-page', 'wunderlist', array('hz_list' => null, 'wunderlist_id' => null));
        }
        wp_enqueue_script('wunderlist-edit-page');
    }


}
add_action('admin_enqueue_scripts', function(){

    wp_enqueue_script('bloodhound', get_stylesheet_directory_uri() . '/js/bloodhound.min.js');
    wp_enqueue_script('typeaheadJQuery', get_stylesheet_directory_uri() . '/js/typeahead.jquery.min.js');
    wp_enqueue_script('typeahead', get_stylesheet_directory_uri() . '/js/typeahead.bundle.min.js');

    wp_register_script('cild-admin-script', get_stylesheet_directory_uri() . '/js/admin.js');
    global $wpdb;
    $results = $wpdb->get_results('SELECT * FROM ingredients', ARRAY_A);
    wp_localize_script('cild-admin-script', 'ingredients', ['all' => $results] );

    wp_enqueue_script('cild-admin-script');


});
function recipe_parse_video_url( $url ){
	$protocol = is_ssl() ? 'https' : 'http';
	if( stristr( $url, 'tube' ) ){
		$temp = explode( '?v=', $url );
		$url = $protocol.'://www.youtube.com/embed/'.$temp[1].'?rel=0';
	}
	else if( stristr( $url, 'daily' ) ){
		$temp = explode( '/', $url );
		$url = $protocol.'://player.vimeo.com/video/'.$temp[1];
	}else if(!stristr($url, 'http') && $url != ''){
            $url = 'https://www.youtube.com/embed/' . $url;
        }
	return $url;
}
add_shortcode('social-share', 'dh_free_social_share_widget');
function dh_free_social_share_widget(){

   if( !is_front_page() && !is_single()) return;

    global $post;
    $id = $post->ID;
    ob_start();
    ?>

    <div class="dh_free_whatsapp_share_button_container">
        <ul >
            <?php if(!is_user_logged_in()): ?>
            <li class="vertical-text visible-xs">
                <a href="#nl-form">
                   <span class="vertical-text" style="background-color: red"> NEWSLETTER </span>
                </a></li>
            <? endif; ?>
<!--            --><?php // if(is_single() || is_front_page()  ): ?>

<!--            <li style="clear: none; text-align: center; width: 25%; " class="">-->
<!--                --><?php //if(is_single() && get_post_type() == 'recipe'): ?>
<!--                <a target="_blank" href="whatsapp://send?text=--><?//= get_the_title() ?><!--%0A%0A%0AZutaten für {{prt}} Portionen:%0A%0A{{zutaten}}%0A%0A--><?//= get_the_permalink() ?><!--" class="fwabtn">-->
<!--                --><?php //else: ?>
<!--                    <a target="_blank" href="whatsapp://send?text=--><?//= get_the_title()?><!-- - --><?//=get_permalink()?><!--" class="fwabtn">-->
<!--                --><?php //endif; ?>
<!--                    <i class="fa fa-whatsapp fa-2x" style="padding-top: 10px;"></i>-->
<!--                </a>-->
<!--            </li>-->
            <li style="clear: none; text-align: center;width: 25%;  ">
                <a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?= get_permalink()?>">
                    <i class="fa fa-facebook  fa-2x" style="padding-top: 10px;"></i>
                </a></li>
            <li style="clear: none; text-align: center;width: 25%;  ">
                <a target="_blank" href="https://twitter.com/intent/tweet?text=<?= get_the_title() ?>&url=<?= get_permalink() ?>">
                    <i class="fa fa-twitter fa-2x" style="padding-top: 10px;"></i>
                </a></li>

            <li style="clear: none; text-align: center;width: 25%;  ">
                <a target="_blank" data-pin-do="buttonBookmark" href="http://pinterest.com/pin/create/button/?url=<?= get_permalink() ?>&media=<?= get_the_post_thumbnail_url(null, 'large')?>&description=<?= get_the_excerpt()?>">
                    <i class="fa fa-pinterest fa-2x pinterest" style="padding-top: 10px;"></i>
                </a></li>
<!--            --><?php //endif; ?>
        </ul>
    </div>
    <?php
    return ob_get_clean();
}

add_action('wp_enqueue_scripts', function(){

    global $post;
    wp_register_script('app', get_stylesheet_directory_uri() . '/js/main.js', array('jquery'), time(), true);


   // if(is_single() && get_post_type() == 'recipe'){
        $recipe_ingredient = get_post_meta( get_the_ID(), 'recipe_ingredient', true );
        $ingredient_array = explode("\n", $recipe_ingredient);
        $ingredients = [];
        $portions = get_post_meta(get_the_ID(), 'recipe_servings', true);
        $row = 0;
        foreach($ingredient_array as $ing){
            $ing = preg_replace('/\s+/', ' ',$ing);//remove double white space
            if(preg_match("/^[0-9]/", $ing)){
                $split = explode(' ', $ing);
                $menge = str_replace(',', '.', $split[0]);
                $ingredients[]  = ['menge' => (float) ($menge / $portions), 'einheit' => strtolower($split[1]), 'zeile' => $row, 'checked' => false, 'text' => $ing, 'headline' => false ];
            }elseif (preg_match("/^#/", $ing)){
                $ingredients[]  = ['menge' => null, 'einheit' => null, 'zeile' => $row, 'checked' => false, 'text' => str_replace('#', '', $ing), 'headline' => true ];
            }else{
                $ingredients[]  = ['menge' => null, 'einheit' => null, 'zeile' => $row, 'checked' => false, 'text' =>  $ing, 'headline' => false ];
            }
            $row++;
        }

        wp_localize_script('app', 'calc_ingredients', $ingredients);
        wp_enqueue_script('app');

   // }


});

function custom_excerpt_length( $length ) {
    return 20;
}
add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );
function new_excerpt_more( $more ) {
    return ' ...';
}
add_filter('excerpt_more', 'new_excerpt_more');

/* Display custom column */
function display_posts_stickiness( $column, $post_id ) {
    if ($column == 'related-recipe'){
        $args = array(
            'post_type' => 'recipe',
            'post_status' => 'publish',
            'posts_per_page' => 3,
            'meta_query' => array(
                array(
                    'key'     => 'related_posts',
                    'value'   => get_the_ID(),
                    'compare' => 'LIKE',
                ),
            ),
        );
        $recipes = new WP_Query( $args );
        if($recipes->have_posts()){
            echo '<ol>';
            while($recipes->have_posts()){
                $recipes->the_post();
                echo '<li>' . get_the_title() . '<br><small><a href="'. get_the_permalink() . '" target="_blank">ansehen</a> 
                            - <a href="' . get_edit_post_link() . '" target="_blank">bearbeiten</a> </small></li>';
            }
            echo '</ol>';
        }
        wp_reset_postdata();
    }
}
add_action( 'manage_posts_custom_column' , 'display_posts_stickiness', 20, 5 );

/* Add custom column to post list */
function add_sticky_column( $columns ) {
    return array_merge( $columns,
        array( 'related-recipe' => 'Bei Rezept' ) );
}
add_filter( 'manage_posts_columns' , 'add_sticky_column' );

add_filter('manage_recipe_posts_columns', function($columns){
    unset($columns['related-recipe']);
    return array_merge($columns, array('related-posts' => 'Beiträge'));
});
add_action( 'manage_recipe_posts_custom_column' , function($column, $post_id){
    if($column == 'related-posts'){
        $posts = get_post_meta($post_id, 'related_posts', true);
        foreach(explode(',', $posts) as $post_id){
            echo '<li><a href="' . get_edit_post_link($post_id) . '" target="_blank">' . get_the_title($post_id) . '</a></li>';
        }
    }
}, 20, 5 );

if ( ! function_exists( 'season' ) ) {

// Register Custom Taxonomy
    function season() {

        $labels = array(
            'name'                       => _x( 'Saisonen', 'Taxonomy General Name', 'recipe' ),
            'singular_name'              => _x( 'Saison', 'Taxonomy Singular Name', 'recipe' ),
            'menu_name'                  => __( 'Saisonen', 'recipe' ),
            'all_items'                  => __( 'Alle Saisonen', 'recipe' ),
            'parent_item'                => __( 'Übergeordnete Saisonen', 'recipe' ),
            'parent_item_colon'          => __( 'Übergeordnete Saison', 'recipe' ),
            'new_item_name'              => __( 'Neue Saison', 'recipe' ),
            'add_new_item'               => __( 'Saison hinzufügen', 'recipe' ),
            'edit_item'                  => __( 'Saison bearbeiten', 'recipe' ),
            'update_item'                => __( 'Saison speichern', 'recipe' ),
            'view_item'                  => __( 'Saison ansehen', 'recipe' ),
            'separate_items_with_commas' => __( 'Durch Kommas trennen', 'recipe' ),
            'add_or_remove_items'        => __( 'Siason hinzufügen oder entfernen', 'recipe' ),
            'choose_from_most_used'      => __( 'Meist verwendet', 'recipe' ),
            'popular_items'              => __( 'Belibete', 'recipe' ),
            'search_items'               => __( 'Suchen', 'recipe' ),
            'not_found'                  => __( 'Keine gefunden', 'recipe' ),
            'no_terms'                   => __( 'Nichts gefunden', 'recipe' ),
            'items_list'                 => __( 'Saison Liste', 'recipe' ),
            'items_list_navigation'      => __( 'Navigation', 'recipe' ),
        );
        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => true,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
            'show_in_rest'              => true,
        );
        register_taxonomy( 'season', array( 'recipe', 'post' ), $args );

    }
    add_action( 'init', 'season', 0 );

}
foreach ( glob( dirname(__FILE__).DIRECTORY_SEPARATOR."includes".DIRECTORY_SEPARATOR ."shortcodes".DIRECTORY_SEPARATOR ."*.php" ) as $filename ){
    require_once( locate_template( 'includes/shortcodes/'.basename( $filename ) ) );
}

require_once dirname(__FILE__) .DIRECTORY_SEPARATOR . 'toast-logger/ajax.php';


if( !function_exists('recipe_mark_favourite') ){
    function recipe_mark_favourite(){
        $post_id = esc_sql( $_POST['recipe_id'] );
        if( is_user_logged_in() ){
            if( recipe_is_user_liked( $post_id ) ){
                delete_post_meta( $post_id, 'favourite_for', get_current_user_id() );
                $favourited = get_post_meta( $post_id, 'favourited', true );
                $favourited -= 1;
                if( $favourited < 0 ){
                    $favourited = 0;
                }
                update_post_meta( $post_id, 'favourited', $favourited );

                $status = 'deleted';
                $message = $favourited;
            }
            else{
                update_post_meta( $post_id, 'favourite_for', get_current_user_id() );
                $favourited = get_post_meta( $post_id, 'favourited', true );
                $favourited += 1;
                update_post_meta( $post_id, 'favourited', $favourited );
                recipe_log_toast_action('favortied');
                $status = 'added';
                $message = $favourited;
            }

        }
        else{
            $status = 'error';
            $message = __( 'Please log in to add to favourites', 'recipe' );
        }
        echo json_encode(array(
            'status' => $status,
            'message' => $message
        ));
        die();
    }
    add_action('wp_ajax_favourite', 'recipe_mark_favourite');
    add_action('wp_ajax_nopriv_favourite', 'recipe_mark_favourite');
}
if( !function_exists('recipe_increase_views_likes') ){
    function recipe_increase_views_likes(){
        $meta = $_POST['meta'];
        $post_id = $_POST['post_id'];
        if( $meta == 'views' ){
            $post_meta = get_post_meta( $post_id, 'views', true );
            $count = 1;
            if( !empty( $post_meta ) ){
                $count = $post_meta + 1;
            }

            update_post_meta( $post_id, 'views', $count );
        }
        else{
            global $wpdb;
            $can_increment = true;
            $post_id = $_POST['post_id'];
            $meta_key = 'likes';
            $ip_address = $_SERVER['REMOTE_ADDR'];
            $post_meta = get_post_meta( $post_id, 'ip_likes' );
            $query = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT * FROM {$wpdb->prefix}postmeta AS postmeta WHERE meta_value = %s AND post_id = %d",
                    $ip_address,
                    $post_id
                )
            );
            if( !empty( $query ) ){
                $can_increment = false;
            }
            else{
                $echo = true;
                update_post_meta( $post_id, 'ip_likes', $ip_address );
            }
            if( $can_increment == true ){
                $post_meta = get_post_meta( $post_id, 'likes', true );
                $count = 1;
                if( !empty( $post_meta ) ){
                    $count = $post_meta + 1;
                }
                recipe_log_toast_action('liked');
                update_post_meta( $post_id, 'likes', $count );
                $response = array( 'count' => $count );
            }
            else{
                $response = array(
                    "error" => __( 'Du hast dieses Rezept bereits geliked', 'recipe' ),
                );
            }

            echo json_encode( $response );
            die();
        }

    }
    add_action('wp_ajax_likes_views', 'recipe_increase_views_likes');
    add_action('wp_ajax_nopriv_likes_views', 'recipe_increase_views_likes');
}

function preprocess_comment_handler( $commentdata ) {
    //some code

    recipe_log_toast_action('commented');

    return $commentdata;
}
add_filter( 'preprocess_comment' , 'preprocess_comment_handler' );




add_shortcode('wunderlist', function () {
    if (!class_exists('WunderlistConnection')) require_once get_stylesheet_directory() . '/includes/classes/WunderlistConnection.php';

    $wunderlist = new WunderlistConnection();
    if($wunderlist->get_access_token()){
        return '<h5> Wunderbar, du bist jetzt mir Wunderlist verbunden!</h5><p>Du kansst dieses Fenster jetzt schliessen.</p>';

    }else{
        return '<h5>Uuups, da ist etwas schief gleaufen!</h5>';
    }


});

add_action('wp_ajax_wunderlist_sync', function (){

    if (!class_exists('WunderlistConnection')) require_once get_stylesheet_directory() . '/includes/classes/WunderlistConnection.php';
    $wunderlist = new WunderlistConnection($_POST['hz_list']);
    echo json_encode( $wunderlist->test_access_token());
    die();



});
add_action('wp_ajax_begin_wunderlist_sync', function (){


    if (!class_exists('WunderlistConnection')) require_once get_stylesheet_directory() . '/includes/classes/WunderlistConnection.php';
    $wunderlist = new WunderlistConnection($_POST['hz_list']);
    echo json_encode( $wunderlist->check_if_list_exists_or_insert());
    die();



});


add_action('wp_ajax_wunderlist_add_tasks', function (){


    if (!class_exists('WunderlistConnection')) require_once get_stylesheet_directory() . '/includes/classes/WunderlistConnection.php';
    $wunderlist = new WunderlistConnection($_POST['hz_list']);
    echo json_encode( $wunderlist->add_tasks($_POST['listid'], $_POST['tasks']));
    die();



});
add_action('wp_ajax_upload_to_wunderlist', function (){


    if (!class_exists('WunderlistConnection')) require_once get_stylesheet_directory() . '/includes/classes/WunderlistConnection.php';
    $wunderlist = new WunderlistConnection($_POST['hz_list']);

    $list =  $wunderlist->check_if_list_exists_or_insert();
    echo json_encode( $wunderlist->add_tasks( $_POST['tasks'] ));
    die();



});
add_action('wp_ajax_download_from_wunderlist', function (){


    if (!class_exists('WunderlistConnection')) require_once get_stylesheet_directory() . '/includes/classes/WunderlistConnection.php';
    $wunderlist = new WunderlistConnection($_POST['hz_list']);
    echo json_encode( $wunderlist->get_all_items());
    die();



});

add_shortcode("br", "br_tag");

function br_tag(){
    return("<br/>");
}
function location_posts( $query ) {
    if( is_tax( 'season' ) ) {
        $query->set('posts_per_page', '6');
    }
    return $query;
}
add_filter('pre_get_posts', 'location_posts');


add_filter('ampforwp_after_post_content', function($content){

    if($content->post->post_type != 'recipe') return;

    $recipe_ingredient = get_post_meta(get_the_ID(), 'recipe_ingredient', true);
    $recipe_ingredients_ld = $recipe_ingredient;
    $recipe_ingredients_ld = str_replace(array("#", "\r"), "", $recipe_ingredients_ld);
    $recipe_ingredients_ld = explode("\n", $recipe_ingredients_ld);
    echo '<h3>Zutaten</h3>';
    $recipe_ingredients = explode("\n", $recipe_ingredient);
    foreach($recipe_ingredients as $ingredient){
        echo $ingredient . '<br>';
    }
    $recipe_steps = get_post_meta( get_the_ID(), 'recipe_steps', true );
    $recipe_steps = recipe_prepare_steps( $recipe_steps );
    $recipe_steps = explode( "--", $recipe_steps );
    $counter = 1;
    echo '<ul>';
    if( !empty( $recipe_steps ) ){
        foreach( $recipe_steps as $step ){
            if( !empty( $step ) ){
                if( stristr( $step, 'step-title' ) ){
                    $counter = 1;
                    echo '<li>'.$step.'</li>';
                }
                else{
                    echo '<li>';
                    echo  $step;
                    echo '</li>';
                    $counter++;
                }
            }
        }
    }
    echo '</ul>';
});
