<?php
if (!class_exists('ShoppingListIncCollection')) require_once get_stylesheet_directory() . '/includes/classes/ShoppingListIncCollection.php';

if (!isset($_GET['list-id']) || !is_numeric($_GET['list-id'])) {
    wp_redirect(home_url('mein-herdzeit'));
    exit();
}
$list_id = $_GET['list-id'];


global $wpdb;
$list = $wpdb->get_row("SELECT * FROM " . SHOPPING_LIST_TABLE . " WHERE ID = {$list_id}");

$done = $wpdb->get_var('SELECT checked FROM shoppingliste_status WHERE list_id = ' . $list_id);
$done = maybe_unserialize($done);

$recipes = maybe_unserialize($list->recipes_portions);
$ingredients = '';

$collection = new ShoppingListIncCollection($recipes, $list_id);
$collection->combine();
$collection->sort_by_wg();



$ing_combined = $collection->get_combined_items_assoc_array();

if (!empty($done)) {
    foreach ($ing_combined as $key => $ing) {
        $ing_combined[$key]['precheck'] = in_array($ing['ing_table_id'], $done);
    }
}

$extras = $wpdb->get_results('SELECT * FROM ' . SHOPPING_LIST_EXTRAS_TABLE . ' as ss WHERE list_id = ' . $list_id );
foreach($extras as $extra){
    $extra->wunderlist_id = $wpdb->get_var('SELECT wunderlist_task FROM wunderlist_id_map WHERE list_id = ' . $list_id . ' AND ingredient_id = "e-'.$extra->ID .'"');

}

?>
<h4>Zutaten für <a
            href="<?= add_query_arg('list-id', $list_id, home_url('mein-herdzeit?page=edit-shopping-list')) ?>">"<?= $list->list_name ?>
        "</a></h4>
<hr>
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<div class="pull-left col-sm-6">
    <a href="<?= wp_is_mobile() ? 'wunderlist://lists/inbox' : 'https://www.wunderlist.com/#/lists/inbox' ?>" target="_blank">
        <img width="39px" height="39px" src="<?= get_stylesheet_directory_uri() ?>/images/wunderlist.jpg"/>
    </a>
    <span class="large-text" id="wunderlist-feedback">Mit Wunderlist verbinden</span>
</div>
<span class="pull-right">
        <span class="large-text">Einkaufsmodus</span>  <input type="checkbox" id="shoppingmode"
                                                              data-toggle="toggle"></span>
<div class="clearfix"></div>
<hr>
<h5><?= $ing_combined[0]['wg'] ?></h5>
<div class="row">

    <?
    $gruppe = $ing_combined[0]['wg'];
    foreach ($ing_combined as $ing): ?>

        <?php
        if ($gruppe != $ing['wg']) {
            echo '</div><hr><h5>' . $ing['wg'] . '</h5><div  class="row">';
        }
        $gruppe = $ing['wg'];
        ?>

        <div class="col-sm-6 combined <?= $ing['precheck'] ? 'checked' : '' ?>" data-id="<?= $ing['ing_table_id'] ?>"
             data-wunderlist="<?= $ing['wunderlist_id'] ?>">
            <div style="padding-bottom: 10px"><a class="fake-checkbox"><i
                            class="fa fa-check"></i></a> <?= $ing['berechnete_menge'] ?>
                <strong><?= $ing['einheit'] ?> <?= $ing['bezeichnung'] ?></strong></div>
        </div>

    <? endforeach; ?>
</div>
<hr>
<h5>Extraposten</h5>
<hr>
<div class="row">
    <?php foreach ($extras as $extra): ?>
        <div class="col-sm-6 combined" data-id="e-<?= $extra->ID ?>" data-wunderlist="<?= $extra->wunderlist_id ?>">
            <div style="padding-bottom: 10px"><a class="fake-checkbox"><i
                            class="fa fa-check"></i></a> <?= number_format($extra->menge, '2', ',', '.') ?>
                <strong><?= $extra->einheit ?> <?= $extra->bezeichnung ?></strong></div>
        </div>
    <? endforeach; ?>
</div>
<hr>
<div class="row">
    <div class="col-xs-6">
        <a class="btn btn-warning" id="reset-list">Zurücksetzen</a>
    </div>
    <div class="col-xs-6">
        <a href="<?= add_query_arg('list-id', $list_id, home_url('mein-herdzeit?page=edit-shopping-list')) ?>" class="pull-right">Liste bearbeiten</a>
    </div>
</div>
<style>
    .checked {
        color: #aaa;
        text-decoration: line-through;
    }

    .checked .fake-checkbox {
        color: #ffffff;
        background-color: #6ba72b;
    }

    .element {
        height: 35px;
        width: 35px;
        margin: 0 auto;
        background-color: red;
        animation-name: stretch;
        animation-duration: 1.5s;
        animation-timing-function: ease-out;
        animation-delay: 0;
        animation-direction: alternate;
        animation-iteration-count: infinite;
        animation-fill-mode: none;
        animation-play-state: running;
    }

    @keyframes stretch {
        0% {
            transform: scale(.3);
            background-color: red;
            border-radius: 100%;
        }
        50% {
            background-color: orange;
        }
        100% {
            transform: scale(1.5);
            background-color: yellow;
        }
    }

</style>


<script>

    function update_checked() {

        var ids = [];
        $('.checked').each(function () {

            ids.push($(this).data('id'));
        });

        $.post(
            ajaxurl,
            {
                action: "update_shoppinglist_progress",
                list_id: wunderlist.hz_list,
                checked: ids,
                wunderlist: wunderlist.wunderlist_id
            },
            function (rsp) {
                console.log(rsp);
            }
        );


    }



    jQuery(document).ready(function ($) {


        $('#reset-list').click(function () {
            $('.checked').each(function () {
                $(this).removeClass('checked');
            });
            update_checked();
        });

        $('.combined').click(function () {
            $(this).toggleClass('checked');

            update_checked($(this).data('wunderlist'));
        });

        $('#shoppingmode').change(function () {

            $('.white-block').parent('.col-sm-3').toggle();
            $('.top-bar').toggle();
            $('.footer_widget_section').toggle();

            if ($(this).prop('checked')) {
                $(this).parents().find('.col-sm-9').first().addClass('col-sm-12').removeClass('col-sm-9');
            } else {
                $(this).parents().find('.col-sm-12').first().addClass('col-sm-9').removeClass('col-sm-12');
            }
        });

    });


</script>