<?php
add_action('wp_ajax_log_toast', 'recipe_log_toast_action');
add_action('wp_ajax_nopriv_log_toast', 'recipe_log_toast_action');

function recipe_log_toast_action($what = ''){

    if(isset($_POST['what'])){
        $what = $_POST['what'];
    }

    global $wpdb;
    $insert = $wpdb->insert(
        'recipe_toast_log',
        [
            'reason' => $what,
            'user'   => get_current_user_id(),
            'post'  => url_to_postid(wp_get_referer()),
        ],
        [
            '%s',
            '%d',
            '%d'
        ]
    );
}
add_action('wp_ajax_latest_toast', 'get_latest_toast');
add_action('wp_ajax_nopriv_latest_toast', 'get_latest_toast');

function get_latest_toast(){

    global $wpdb;
    $results = $wpdb->get_results('SELECT * FROM recipe_toast_log WHERE timestamp >= DATE_SUB(NOW(), INTERVAL 15 SECOND ) LIMIT 1', ARRAY_A);

    $current_user = get_current_user_id();
    if(!empty($results) && $current_user != $results[0]['user']){
        $toast_data = $results[0];
        $user = get_userdata( $toast_data['user'] );
        $username = $user ? $user->user_login : 'Gast';
        switch ($toast_data['reason']){

            case "whatsapp": {
                $reason = '<i class="fa fa-whatsapp"></i> Via Whatsapp geteilt von:  <a href="' . get_author_posts_url( $toast_data['user'] ) . '">' . $username . '</a>' ;
                break;
            }
            case "favortied": {
                $reason = '<i class="fa fa-heart"></i> Favorisiert von:  <a href="' . get_author_posts_url( $toast_data['user'] ) . '">' . $username . '</a>' ;
                break;
            }
            case "liked": {
                $reason = '<i class="fa fa-thumbs-o-up"></i> Geliked von:  <a href="' . get_author_posts_url( $toast_data['user'] ) . '">' . $username . '</a>' ;
                break;
            }
            case "commented": {
            $reason = '<i class="fa fa-file-text"></i> Kommentiert von:  <a href="' . get_author_posts_url( $toast_data['user'] ) . '">' . $username . '</a>' ;
            break;
            }
            case "cooked": {
                $reason = '<i class="fa fa-fire"></i> Gerade Gekocht:  <a href="' . get_author_posts_url( $toast_data['user'] ) . '">' . $username . '</a>' ;
                break;
            }
        }
        $toast = [
            'post_link' => get_the_permalink($toast_data['post']),
            'img' => get_the_post_thumbnail_url($toast_data['post'], 'thumbnail'),
            'title' => html_entity_decode(get_the_title($toast_data['post'])),
            'reason' => $reason,
            'user'   => $user ? $user->user_login : 'Gast',
            'user_link' => get_author_posts_url( $toast_data['user'] )
        ];
    }else{
        $toast = false;
    }
    echo json_encode($toast);
    die();
}
add_action('wp_ajax_nopriv_get_recipe_steps', 'get_recipe_steps');
add_action('wp_ajax_get_recipe_steps', 'get_recipe_steps');
function get_recipe_steps(){
    $recipe_steps = get_post_meta($_POST['id'], 'recipe_steps', true);
    //$recipe_steps = preg_replace( "/\r/", "", $recipe_steps );
    $recipe_steps = explode('--', $recipe_steps);
    //$recipe_steps = array_filter($recipe_steps, create_function('$value', 'return $value !== "";'));

    foreach($recipe_steps as $step){
        $reg_exUrl = "/#[^#]*#/";
        preg_match_all( $reg_exUrl, $step, $matches );
        $title = $matches[0][0];
        $title = str_replace('#','', $title);



        $steps[] = [
            'title'     => $title,
            'content'   => preg_replace($reg_exUrl, '', $step),//apply_filters('the_content',  preg_replace($reg_exUrl, '', $step)),
            'clicked'   => false
            ];
    }
    echo json_encode($steps);
    die();
}
add_action('wp_ajax_nopriv_tryLogin', 'tryLogin');
add_action('wp_ajax_tryLogin', 'tryLogin');

    function tryLogin(){

    $user_signon = wp_signon(['user_login' => $_POST['data']['user'], 'user_password' => $_POST['data']['pw'] ]);
    if ( is_wp_error($user_signon) ){
        echo json_encode(array('loggedin'=>false, 'user' => 0, 'message'=>__('Fehler beim Login. Bitte prüfe deine Eingaben!')));
    } else {
        echo json_encode(array('loggedin'=>true, 'user' => $user_signon->ID, 'message'=>__('Erfolgreich eingelogged...')));
    }
    die();
}


add_action('wp_ajax_add_new_shopping_list', function(){

    $url     = wp_get_referer();
    $post_id = url_to_postid( $url );
    $portions = $_POST['portions'];
    $listName = $_POST['listName'];
    $user = get_current_user_id();

    $recipe = (object) ['recipe' => $post_id, 'portions' => $portions ];

    global $wpdb;

    $insert = $wpdb->insert(
        SHOPPING_LIST_TABLE,
        [
            'user_id' => $user,
            'list_name' => $listName,
            'recipe_count' => 1,
            'recipes_portions' => maybe_serialize([$recipe])
        ],
        [
            '%d',
            '%s',
            '%d',
            '%s'
        ]
    );
    echo $insert;
    die();
});



add_action('wp_ajax_get_widget_shopping_lists', function(){

    $lists = [];
    $user = $_POST['user'];
    global $wpdb;
    $insert = $wpdb->get_results("SELECT * FROM reciepe_shopping_lists WHERE user_id = " . $user);

    if(empty($insert)){
        echo "empty";
        die();
    }

    foreach($insert as $list){
        $lists[] = [
            'id' => $list->ID,
            'created' => $list->created_at,
            'name' => $list->list_name,
            'count' => $list->recipe_count
        ];
    }
    echo json_encode($lists);
    die();
});


add_action('wp_ajax_add_to_shopping_list', function(){

    $url     = wp_get_referer();
    $post_id = url_to_postid( $url );
    $portions = $_POST['portions'];
    $list_id = $_POST['listId'];

    global $wpdb;
    $list = $wpdb->get_row('SELECT * FROM reciepe_shopping_lists WHERE ID = ' . $list_id);
    if(!empty($list)){
        $recipes = maybe_unserialize($list->recipes_portions);
        foreach($recipes as $recipe){
            if($recipe->recipe == $post_id){
                echo json_encode(['added' => 0, 'msg' => "Dieses Rezept ist bereits auf der Liste."]);
                die();
            }
        }


        $recipes[] = (object) ['recipe' => $post_id, 'portions' => $portions ];
        $list->recipe_count++;
        $list->recipes_portions = maybe_serialize($recipes);

        $update = $wpdb->update(
            SHOPPING_LIST_TABLE,
            [
                'recipe_count' => $list->recipe_count,
                'recipes_portions' =>  $list->recipes_portions
            ],
            array( 'ID' => $list_id ),
            [
                '%d',
                '%s'
            ],
            array( '%d' )
        );

        if(is_wp_error($update)){
            echo json_encode(['added' => 0, 'msg' => "Ein Fehler beim Hinzfügen zu dieser Liste ist aufgetreten."]);
            die();
        }else{
            echo json_encode(['added' => 1, 'msg' => "Erfolgreich zur Liste hinzugefügt."]);
            die();
        }

    }else{
        echo json_encode(['added' => 0, 'msg' => "Ein Fehler beim Hinzfügen zu dieser Liste ist aufgetreten."]);
        die();
    }


});