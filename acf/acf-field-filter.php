<?php
if( function_exists('acf_add_options_page') ) {

    acf_add_options_page(array(
        'page_title' 	=> 'Zutaten',
        'menu_title'	=> 'Zutaten aktualisieren',
        'menu_slug' 	=> 'edit-ing',
        'parent_slug'   => 'edit.php?post_type=recipe',
        'capability'	=> 'edit_posts',
        'redirect'		=> false,
    ));
    acf_add_options_page(array(
        'page_title' 	=> 'Warengruppen',
        'menu_title'	=> 'Warengruppen',
        'menu_slug' 	=> 'warengruppen',
        'parent_slug'   => 'edit.php?post_type=recipe',
        'capability'	=> 'edit_posts',
        'redirect'		=> false,
    ));
}
add_filter('acf/load_field/key=field_5b1a5a98862a5', 'alter_message');
function alter_message($field) {

    ob_start();
    ?>
    <div class="row">
        <div class="col-md-12">
            <button class="btn btn-primary" id="update-ing-table">Zutaten Tabelle aktualisieren</button>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <table class="table" id="update-ing-table-feedback">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Menge</th>
                    <th>Einheit</th>
                    <th>Text</th>
                    <th>Fehler</th>
                    <th>Aktion</th>
                </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                <tr>
                    <td colspan="2"><button id="update-now" disabled="true">jetzt aktualisieren</button></td>
                    <td colspan="4"><p>Fehler in Zeile:</p><p id="error-links"></p></td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <style>
        .active{
            background: lightcyan;
        }
    </style>
    <?php
    $field['instructions'] = ob_get_clean();
    return $field;
}


add_action('wp_ajax_collect_all_ingredients', function(){

    global $wpdb;
    $results = $wpdb->get_results("SELECT m.meta_value, m.post_id FROM wp_postmeta as m WHERE meta_key = \"recipe_ingredient\" AND (SELECT post_status FROM wp_posts WHERE ID = m.post_id) = \"publish\"", 0);


    $saved = $wpdb->get_results("SELECT einheit, bezeichnung FROM ingredients", ARRAY_A);

    $clean_array = [];
    foreach($results as $recipe){




        $edit_link = get_edit_post_link($recipe->post_id);

        $ingredients = explode("\r", $recipe->meta_value);



        foreach($ingredients as $ing){


            $ing = trim($ing);



            $ing = preg_replace('/\s+/', ' ',$ing);



            if(empty($ing) || substr($ing, 0, 1) === '#') continue;

            $split = explode(' ', $ing);

            $menge = array_shift($split);
            $einheit = array_shift($split);
            $bezeichnung = implode( ' ', $split);

            $continue = false;
            foreach($saved as $save){

               if( $save['bezeichnung']  == $bezeichnung){
                    $continue = true;
               }

            }

            if($continue) continue;

                $clean_array[] = [
                    'menge' => $menge,
                    'einheit' => $einheit,
                    'bezeichnung' => $bezeichnung,
                    'link'  => $edit_link
                ];
        }
    }

        usort($clean_array, function($a,$b) {
            return strcmp($a["bezeichnung"], $b["bezeichnung"]);
        });
    echo json_encode($clean_array);
    die();
});




add_action('wp_ajax_update_ing_table', function(){

    $data = $_POST['data'];
    global $wpdb;
    $wpdb->hide_errors();
    $insert = $wpdb->insert(
        'ingredients',
        [
            'einheit' => $data['einheit'],
            'bezeichnung' => $data['bezeichnung']
        ],
        [
            '%s',
            '%s'
        ]
        );
    if(!is_wp_error($insert)){
        echo "eingefüg";
    }else{
        echo 'nicht eingefügt';
    }
    die();

});



add_filter('acf/load_field/key=field_5b1a9d64bbd76', function($field){
    ob_start();
    global $wpdb;
    $ing = $wpdb->get_results('SELECT * FROM ingredients');
    $wg = get_field('field_5b1a9c0a9199d', 'option');
    $runner = 0;
    ?>
    <table class="widefat">
        <? foreach($ing as $i): ?>
        <tr class="<?= $runner % 2 == 0 ? 'alternate' : '' ?>">
            <td><?= $i->einheit ?></td>
            <td><?= $i->bezeichnung ?></td>
            <td>
                <select class="wg-select" data-id="<?= $i->ID ?>">
                    <option>Bitte wählen</option>
                    <?
                    foreach($wg as $g){
                        echo '<option value="'.$g['warengruppenbezeichung'].'" ';
                        echo  $i->warengruppe == $g['warengruppenbezeichung'] ? 'selected="true"': '';
                        echo '>'.$g['warengruppenbezeichung'].'</option>';
                    }?>
                </select>
            </td>
            <td><input type="checkbox" class="immerzuhause" <?= $i->immerzuhause ? 'checked="true"' : ''?> data-id="<?= $i->ID ?>" > Immerzuhause (<?= $i->immerzuhause ?>)</td>
            <td><span class="spinner"></span></td>
        </tr>
        <? $runner++; endforeach; ?>
    </table>
    <?php
    $field['instructions'] = ob_get_clean();
    return $field;
});



add_action('wp_ajax_update_warengruppe', function(){
    global $wpdb;
    echo $wpdb->update(
        'ingredients',
        [
            'warengruppe' => $_POST['gruppe']
        ],
        [
            'ID' => $_POST['id']
        ]
    );
});




add_action('wp_ajax_update_immerzuhause', function(){
    global $wpdb;
    echo $wpdb->update(
        'ingredients',
        [
            'immerzuhause' => $_POST['immerzuhause'] == 'true' ? 1 : 0
        ],
        [
            'ID' => $_POST['id']
        ]
    );
});
function prevent_save_acf_value($value, $post_id, $field) {
    return get_field($field['name'], $post_id);
}
add_filter('acf/update_value/key=field_5b1a5746d3319', 'prevent_save_acf_value', 10, 3);




add_action('wp_ajax_update_user_immer_zuhause', function(){



    $ids = explode(',', $_POST['ids']);
    global $wpdb;
    $exists = $wpdb->get_var("SELECT ID FROM immer_zuhause WHERE user_id = " . get_current_user_id());

    if($exists){
       echo $wpdb->update(
            'immer_zuhause',
            [
                'ingredients' => maybe_serialize($ids)
            ],
            [
                'user_id' => get_current_user_id()
            ]
        );
    }else{
        echo $wpdb->insert(
                'immer_zuhause',
                [
                        'user_id' => get_current_user_id(),
                        'ingredients' => maybe_serialize($ids)
                ],
                [
                        '%d',
                        '%s'
                ]
        );
    }

    die();


});




add_action('wp_ajax_update_shoppinglist_progress', function (){




    global $wpdb;
    $exists = $wpdb->get_var("SELECT ID FROM shoppingliste_status WHERE list_id = " . $_POST['list_id']);

    if(!empty($exists)){

      echo  $wpdb->update(
          'shoppingliste_status',
        [
                'checked' => maybe_serialize($_POST['checked'])
        ],
          [
                  'list_id' => $_POST['list_id']
          ]
        );


    }else{

      echo  $wpdb->insert(
            'shoppingliste_status',
            [
                'list_id' => $_POST['list_id'],
                'checked' => maybe_serialize($_POST['checked'])
            ],
            [
                    '%d',
                    '%s'
            ]
        );

    }

    if(isset($_POST['wunderlist'])){
        if (!class_exists('WunderlistConnection')) require_once get_stylesheet_directory() . '/includes/classes/WunderlistConnection.php';
        $wunderlist = new WunderlistConnection();
        $wunderlist->update_single_item($_POST['wunderlist']);
    }

    die();


});