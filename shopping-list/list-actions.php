<?php
add_action('admin_post_delete_shopping_list', function () {


    if (wp_verify_nonce($_REQUEST['delete_shopping_list_nonce'], 'delete_shopping_list')) {

        $list_id = $_POST['list_id'];
        if (!$list_id) {
            wp_redirect(home_url());
        }

        global $wpdb;
        $list = $wpdb->get_var("SELECT  user_id FROM " . SHOPPING_LIST_TABLE . " WHERE ID  = " . $list_id);
        if (!$list || $list != get_current_user_id()) {
            wp_redirect(home_url());
        }
        $wpdb->delete(SHOPPING_LIST_TABLE, ['ID' => $list_id]);
        $wpdb->delete('shoppingliste_status', ['list_id' => $list_id]);
        $wpdb->delete('wunderlist_id_map', ['list_id' => $list_id]);
        wp_redirect(wp_get_referer());

    } else {

        wp_redirect(home_url());
    }


});

function edit_shopping_list() {


    if (wp_verify_nonce($_REQUEST['edit_shopping_list_nonce'], 'edit_shopping_list_nonce')) {
        wp_redirect(home_url());
    }

    if (!isset($_POST['list_id']) || !is_numeric($_POST['list_id'])) {

        wp_redirect(wp_get_referer());
        exit();
    }

    $list_id = $_POST['list_id'];


    global $wpdb;
    $list_user = $wpdb->get_var('SELECT user_id FROM ' . SHOPPING_LIST_TABLE . ' WHERE ID = ' . $list_id);

    if ($list_user != get_current_user_id()) {
        wp_redirect(wp_get_referer());
    }

    $recipes_portions = [];
    if (isset($_POST['recipes'])) {

        foreach ($_POST['recipes'] as $recipe_id) {
            $recipes_portions[] = (object)['recipe' => $recipe_id, 'portions' => $_POST['portions'][$recipe_id]];
        }
    }

        $recipe_count = count($recipes_portions);
        $recipes_portions = maybe_serialize($recipes_portions);

        $wpdb->update(
            SHOPPING_LIST_TABLE,
            [
                'list_name' => $_POST['list_name'],
                'image' => pathinfo($_POST['image'])['basename'],
                'recipe_count' => $recipe_count,
                'recipes_portions' => $recipes_portions
            ],
            ['ID' => $list_id],
            [
                '%s',
                '%s',
                '%d',
                '%s',
            ]);


    $wpdb->delete('shoppingliste_status', ['list_id' => $list_id]);
    $wpdb->delete(
        SHOPPING_LIST_EXTRAS_TABLE,
        [
            'list_id' => $list_id
        ]
    );

    if (isset($_POST['extra'])) {
        foreach ($_POST['extra'] as $extra) {
            $wpdb->insert(
                SHOPPING_LIST_EXTRAS_TABLE,
                [
                    'list_id' => $list_id,
                    'bezeichnung' => $extra['bez'],
                    'einheit' => $extra['einh'],
                    'menge' => $extra['menge']
                ]
            );
        }
    }

    if ($_POST['wunderlist_connection'] == 1) {
        update_wunderlist_from_edit($_POST['wunderlist_id'], $list_id);
    }


    wp_redirect(add_query_arg('list-id', $list_id, $_POST['_wp_http_referer']));
}


function create_shopping_list () {


    if (wp_verify_nonce($_REQUEST['edit_shopping_list_nonce'], 'edit_shopping_list_nonce')) {
        wp_redirect(home_url());
    }


    global $wpdb;


    $recipes_portions = [];
    foreach ($_POST['recipes'] as $recipe_id) {
        $recipes_portions[] = (object)['recipe' => $recipe_id, 'portions' => $_POST['portions'][$recipe_id]];
    }
    $recipe_count = count($recipes_portions);
    $recipes_portions = maybe_serialize($recipes_portions);

    $wpdb->insert(
        SHOPPING_LIST_TABLE,
        [
            'list_name' => $_POST['list_name'],
            'image' => pathinfo($_POST['image'])['basename'],
            'recipe_count' => $recipe_count,
            'user_id' => get_current_user_id(),
            'recipes_portions' => $recipes_portions
        ],
        [
            '%s',
            '%s',
            '%d',
            '%d',
            '%s',
        ]);

    $list_id = $wpdb->insert_id;

    foreach ($_POST['extra'] as $extra) {
        $wpdb->insert(
            SHOPPING_LIST_EXTRAS_TABLE,
            [
                'list_id' => $list_id,
                'bezeichnung' => $extra['bez'],
                'einheit' => $extra['einh'],
                'menge' => $extra['menge']
            ]
        );
    }
    if ($_POST['wunderlist_connection'] == 1) {
        update_wunderlist_from_edit($_POST['wunderlist_id'], $list_id);
    }

    wp_redirect(add_query_arg('list-id', $list_id, $_POST['_wp_http_referer']));
}


function update_wunderlist_from_edit($wunderlist_id, $list_id)
{

    if (!class_exists('WunderlistConnection')) require_once get_stylesheet_directory() . '/includes/classes/WunderlistConnection.php';
    $wunderlist = new WunderlistConnection($list_id);


    if (!class_exists('ShoppingListIncCollection'))
        require_once get_stylesheet_directory() . '/includes/classes/ShoppingListIncCollection.php';


    global $wpdb;
    $list = $wpdb->get_row("SELECT * FROM " . SHOPPING_LIST_TABLE . " WHERE ID = {$list_id}");

    $wunderlist->check_if_list_exists_or_insert();


    $done = $wpdb->get_var('SELECT checked FROM shoppingliste_status WHERE list_id = ' . $list_id);

    $done = maybe_unserialize($done);

    $recipes = maybe_unserialize($list->recipes_portions);
    $ing_combined = [];

    if (!empty($recipes)) {
        $collection = new ShoppingListIncCollection($recipes, $list_id);
        $collection->combine();
        $collection->sort_by_wg();
        $ing_combined = $collection->get_combined_items_assoc_array();
    }


    if (!empty($done)) {
        foreach ($ing_combined as $key => $ing) {
            $ing_combined[$key]['precheck'] = in_array($ing['ing_table_id'], $done);
        }
    }

    $extras = $wpdb->get_results('SELECT * FROM ' . SHOPPING_LIST_EXTRAS_TABLE . ' as ss WHERE list_id = ' . $list_id, ARRAY_A);

    if(!empty($extras)){
        foreach ($extras as $extra) {
            $extra->wunderlist_id = $wpdb->get_var('SELECT wunderlist_task FROM wunderlist_id_map WHERE list_id = ' . $list_id . ' AND ingredient_id = "e-' . $extra->ID . '"');
        }
    }
    $all = array_merge($ing_combined, $extras);


    foreach ($all as $task) {

        if(array_key_exists('ing_table_id', $task)){
            $id  = $task['ing_table_id'];
        }elseif(array_key_exists('ID', $task)){
            $id  = 'e-' . $task['ID'];
        }
        if(array_key_exists('berechnete_menge', $task)){
            $menge = $task['berechnete_menge'];
        }elseif(array_key_exists('menge', $task)){
            $menge  =  $task['menge'];
        }

        $tasks[] = [
            'text'  => $menge . ' ' . $task['einheit'] . ' ' . $task['bezeichnung'] ,
            'checked' => $task['precheck'],
            'herdzeit_id' => $id
        ];

    }
    $wunderlist->add_tasks($tasks);

}








