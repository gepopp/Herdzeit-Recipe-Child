<?php



if(isset($_POST['action'])){
   if($_POST['action'] == 'create_shopping_list'){
       create_shopping_list();
   }elseif($_POST['action'] == 'edit_shopping_list'){
        edit_shopping_list();
    }

}


$list_id = isset($_GET['list-id']) ? $_GET['list-id'] : '';

if(is_numeric($list_id)){

    global $wpdb;
    $list = $wpdb->get_row("SELECT  * FROM " . SHOPPING_LIST_TABLE . " WHERE ID  = " . $list_id);
    if($list->user_id != get_current_user_id()){
        wp_redirect(home_url('mein-herdzeit'));
    }

    $extras = $wpdb->get_results('SELECT * FROM ' . SHOPPING_LIST_EXTRAS_TABLE . ' WHERE list_id = ' . $list_id . ' ORDER BY ID');

}else{

    if( $wpdb->get_var('SELECT count(*) FROM ' . SHOPPING_LIST_TABLE . ' WHERE user_id = ' . get_current_user_id()) >= 3 ){
        wp_redirect(home_url('/mein-herdzeit?page=shopping-list'));
    }

    $list = (object)[

            'ID'    => 'new_list',
            'image' =>  "shopping-list.jpg",
            'list_name' => '',
            'recipes_portions' => [],
    ];
    $extras = [];
}
$recipes = maybe_unserialize($list->recipes_portions);



if ($list->image == '') {
    $list->image = "shopping-list.jpg";
}

$images = glob(get_stylesheet_directory() . '/images/shopping-list/*');
?>
<form action="#" method="POST" id="delete-list-<?= $list->ID ?>">
    <input type="hidden" name="action" value="<?= is_numeric($list_id) ? 'edit_shopping_list' : 'create_shopping_list' ?>">
    <input type="hidden" name="list_id" value="<?= $list->ID ?>">
    <?= wp_nonce_field('edit_shopping_list', 'edit_shopping_list_nonce') ?>

    <? if(is_numeric($list_id)):?>
    <h4>Liste bearbeiten</h4>
    <? else: ?>
    <h4>Neue Liste</h4>
    <? endif; ?>
    <hr>
    <div class="row">
        <?php
        foreach ($images as $image): ?>
            <div class="col-md-2 col-xs-3">
                <img class="img-responsive img-thumbnail list-image <?= pathinfo($image)['basename'] != $list->image ?: "active" ?>"
                     src="<?= get_stylesheet_directory_uri() ?>/images/shopping-list/<?= pathinfo($image)['basename'] ?>">
            </div>
        <?php endforeach; ?>
        <input type="hidden" name="image" value="<?= $list->image ?>">
    </div>
    <hr>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label for="list_name">Listenname</label>
                <input type="text" class="form-control" id="list_name" name="list_name" value="<?= $list->list_name ?>"
                       required="true">
            </div>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-12">
            <h4>Rezepte</h4>
            <div id="remote" class="input-group" style="width: 100%; position: relative">
                <input class="typeahead form-control" type="text" placeholder="Rezept suchen und hinzufügen">
                <div class="input-group-append">
                    <span class="input-group-text" id="basic-addon2" style="position: absolute; top: 5px; right: 5px; z-index: 999; display: none;">
                        <i class="fa fa-circle-o-notch fa-spin" style="font-size:24px"></i>
                    </span>
                </div>
            </div>
            <table class="table table-striped" id="recipe-table">
                <thead>
                <tr>
                    <th class="hidden-xs" scope="col">
                        Bild
                    </th>
                    <th scope="col">
                        Rezept
                    </th>
                    <th scope="col">
                        Port.
                    </th>
                    <th scope="col">

                    </th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($recipes

                               as $recipe):
                    $id = $recipe->recipe ?>
                    <tr>
                        <td class="hidden-xs"><?= get_the_post_thumbnail($id, 'slider-thumb') ?></td>
                        <td><a href="<?= get_the_permalink($id) ?>"><?= get_the_title($id) ?></a></td>
                        <td>
                            <div class="col-xs-12">
                                <div class="row">
                                    <div class="col-md-8">
                                        <input type="number" class="form-control" id="portions_<?= $recipe->recipe ?>"
                                               name="portions[<?= $recipe->recipe ?>]" value="<?= $recipe->portions ?>"
                                               style="text-align: center">
                                        <input type="hidden" name="recipes[]" value="<?= $recipe->recipe ?>" min="1">
                                    </div>
                                    <div class="col-md-4">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <a class="btn btn-primary" id="decrease_<?= $recipe->recipe ?>"
                                                   data-id="<?= $recipe->recipe ?>"><i class="fa fa-minus"></i></a>
                                            </div>
                                            <div class="col-sm-6">
                                                <a class="btn btn-primary increase" id="increase_<?= $recipe->recipe ?>"
                                                   data-id="<?= $recipe->recipe ?>"><i
                                                            class="fa fa-plus"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="btn btn-fake"><i style="font-size: 1.5em" class="fa fa-trash"
                                                          id="delete_<?= $recipe->recipe ?>"></i></span>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if(count($recipes) == 0): ?>
                    <tr>
                        <td colspan="4" class="recipe-empty-row">
                            Keine Rezepte gefunden! Achtung, leere Einkaufslisten werden nach 14 Tagen gelöscht!
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
    <hr>
    <div class="row">
        <div class="col-md-12">
            <h4>Extraposten</h4>

            <table class="table table-striped" id="extra-positions">
                <thead>
                <tr>
                    <th scope="col">
                        Bezeichnung
                    </th>
                    <th scope="col">
                        Einheit
                    </th>
                    <th scope="col">
                        Menge
                    </th>
                    <th scope="col">
                        <i class="fa fa-minus"></i>
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php $runner = 1;
                foreach ($extras as $extra): ?>
                    <tr>
                        <td><?= $extra->bezeichnung ?><input type="hidden" name="extra[<?= $runner ?>][bez]"
                                                             value="<?= $extra->bezeichnung ?>"/></td>
                        <td><?= $extra->einheit ?><input type="hidden" name="extra[<?= $runner ?>][einh]"
                                                         value="<?= $extra->einheit ?>"/></td>
                        <td><?= $extra->menge ?><input type="hidden" name="extra[<?= $runner ?>][menge]"
                                                       value="<?= $extra->menge ?>"/></td>
                        <td><a class="remove-extra"><i class="fa fa-minus"></i></a></td>
                    </tr>
                    <?php $runner++; endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <hr>
    <h5>Posten hinzufügen</h5>
    <div class="row">
        <div class="col-sm-4">
            <label>Bezeichnung</label>
            <input type="text" id="extra-name" class="form-control">
            <small class="text-danger" id="extra-feedback"></small>
        </div>

        <div class="col-sm-4">
            <label>Einheit</label>
            <select class="form-control" id="extra-type">
                <option value="Stk.">Stk.</option>
                <option value="Pkg.">Pkg.</option>
                <option value="Fl.">Fl.</option>
                <option value="Kilo">Kilo</option>
                <option value="Gramm">Gramm</option>
                <option value="Liter">Liter</option>
            </select>
        </div>

        <div class="col-sm-2">
            <label>Menge</label>
            <input type="number" id="extra-amount" class="form-control">

        </div>
        <div class="col-sm-2">
            <label><i class="fa fa-plus" style="visibility: hidden"></i></label>
            <a class="btn btn-primary form-control" id="add-extra-position"><i class="fa fa-plus"></i></a>

        </div>
    </div>
    <hr>
    <input type="hidden" name="wunderlist_id" value="<?= $list->wunderlist_id ?>">
    <input type="hidden" name="wunderlist_connection" value="0" id="wunderlist_connection">
    <div class="row">
        <div class="col-md-12">
            <button class="btn btn-primary">speichern</button>
            <span id="wunderlist-feedback"></span>
            <a href="<?= add_query_arg('list-id', $list_id, home_url('mein-herdzeit?page=edit-ingredient')) ?>" class="btn btn-primary pull-right">
                <i class="fa fa-list"></i> Zutatenliste</a>
        </div>
    </div>

</form>
<script src="<?= get_stylesheet_directory_uri() ?>/js/bloodhound.min.js"></script>
<script src="<?= get_stylesheet_directory_uri() ?>/js/typeahead.bundle.min.js"></script>
<script src="<?= get_stylesheet_directory_uri() ?>/js/typeahead.jquery.min.js"></script>
<script>

    jQuery(document).ready(function ($) {





        var bestPictures = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {
                url: '/wp-json/wp/v2/recipe?search=%QUERY&_embed',
                wildcard: '%QUERY'
            }
        });
        typeahead = $("#remote .typeahead").typeahead(null, {
            name: 'best-pictures',
            display: function (data) {
                var txt = document.createElement("textarea");
                txt.innerHTML = data.title.rendered;
                return txt.value;
            },
            source: bestPictures,
            templates: {
                suggestion: function (data) {
                    return '<p style="padding:15px; border-bottom:1px solid lightgray;"><strong>' + data.title.rendered + '</strong></p>';
                }
            },
        });
        $('.typeahead').on('typeahead:asyncrequest', function (e, datum) {

            $('#basic-addon2').show();

        });
        $('.typeahead').on('typeahead:asyncreceive', function (e, datum) {

            $('#basic-addon2').hide();

        });




        $('.typeahead').on('typeahead:selected', function (e, datum) {



            if (!check_for_duplicates(datum.id)) {
                alert('Dieses Rezept ist bereits in der Liste.');
                $('.typeahead').typeahead('val', '');
                return false;
            }


            $('.recipe-empty-row').hide();


            $('#recipe-table').append('' +
                '<tr class="recipe-tr">\n' +
                '                        <td class="hidden-xs"><img src="' + datum._embedded['wp:featuredmedia'][0].media_details.sizes['widget-thumb'].source_url + '" </td>\n' +
                '                        <td><a href="' + datum.guid.rendered + '">' + datum.title.rendered + '</a></td>\n' +
                '                        <td>\n' +
                '                            <div class="col-xs-12">\n' +
                '                                <div class="row">\n' +
                '                                    <div class="col-md-8">\n' +
                '                                        <input type="number" class="form-control" id="portions_' + datum.id + '"\n' +
                '                                               name="portions[' + datum.id + ']" value="4" style="text-align: center">\n' +
                '                                        <input type="hidden" name="recipes[]" value="' + datum.id + '" min="1">\n' +
                '                                    </div>\n' +
                '                                    <div class="col-md-4">\n' +
                '                                        <div class="row">\n' +
                '                                            <div class="col-sm-6">\n' +
                '                                                <a class="btn btn-primary" id="decrease_' + datum.id + '"\n' +
                '                                                                        data-id="' + datum.id + '"><i class="fa fa-minus"></i></a>\n' +
                '                                            </div>\n' +
                '                                            <div class="col-sm-6">\n' +
                '                                                 <a class="btn btn-primary increase" id="increase_' + datum.id + '"\n' +
                '                                                       data-id="' + datum.id + '"><i\n' +
                '                                                             class="fa fa-plus"></i></a>\n' +
                '                                            </div>\n' +
                '                                        </div>\n' +
                '                                    </div>\n' +
                '                                </div>\n' +
                '                            </div>\n' +
                '                        </td>\n' +
                '                        <td>\n' +
                '                            <span class="btn btn-fake"><i style="font-size: 1.5em" class="fa fa-trash" id="delete_' + datum.id + '"></i></span>\n' +
                '                        </td>\n' +
                '                    </tr>');
            $('.typeahead').typeahead('val', '');
            check_for_duplicates(datum.id);
        });

        function check_for_duplicates(id) {
            ids = [];
            $('.increase').each(function (index, value) {
                ids.push($(value).data('id'))
            });
            if ($.inArray(id, ids) != -1) {

                return false;
            }
            return true;
        }

//
        function makeid() {
            var text = "";
            var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

            for (var i = 0; i < 10; i++)
                text += possible.charAt(Math.floor(Math.random() * possible.length));

            return text;
        }


        $('.remove-extra').live('click', function () {

            cell = $(this)[0];
            $(cell).closest('tr').remove();

        });

        $('.list-image').click(function () {

            $('.list-image').each(function () {
                $(this).removeClass('active');
            });
            $('input[name="image"]').val($(this).attr('src'));
            $(this).addClass('active');

        });

        $('[id^=increase_]').live('click', function () {
            var id = $(this).data('id');
            var value = $('#portions_' + id).val();
            value++;
            $('#portions_' + id).val(value);
        });
        $('[id^=decrease_]').live('click', function () {
            var id = $(this).data('id');
            var value = $('#portions_' + id).val();
            if (value > 1) {
                value--;
                $('#portions_' + id).val(value);
            }

        });
        $('[id^=delete_]').live('click', function () {

            if (confirm("Wirklich entfernen?"))
                $(this).closest('tr').remove();
        });

        $('#extra-amount').keydown(function (e) {

            var code = e.which; // recommended to use e.which, it's normalized across browsers
            if (code == 13) {
                e.preventDefault();
                add_extra();
                $('#extra-name').focus();
                return false;
            }
        });
        $('#extra-name').keydown(function (e) {
            var code = e.which; // recommended to use e.which, it's normalized across browsers
            if (code == 13) {
                e.preventDefault();
                $('#extra-type').focus();
                return false;
            }
        });
        $('#extra-type').keydown(function (e) {
            var code = e.which; // recommended to use e.which, it's normalized across browsers
            if (code == 13) {
                e.preventDefault();
                $('#extra-amount').focus();
                return false;
            }
        });


        $('#add-extra-position').click(function () {
            add_extra();
        });


        function add_extra() {

            var bez, einh, menge;
            bez = $('#extra-name').val();
            einh = $('#extra-type').val();
            menge = $('#extra-amount').val();

            var row = makeid();

            if (bez == '' || einh == '' || menge == '') {
                $('#extra-feedback').text("Bitte fülle alle Felder aus");
                setTimeout(function () {
                    $('#extra-feedback').text('')
                }, 2000);
                return false;
            }
            $('#extra-positions tbody').append('' +
                '<tr>' +
                '<td>' + bez + '<input type="hidden" name="extra[' + row + '][bez]" value="' + bez + '"/></td>' +
                '<td>' + einh + '<input type="hidden" name="extra[' + row + '][einh]" value="' + einh + '"/></td>' +
                '<td>' + menge + '<input type="hidden" name="extra[' + row + '][menge]" value="' + menge + '"/></td>' +
                '<td><a class="remove-extra"><i class="fa fa-minus"></i></a></td>' +
                '</tr>');

            $('#extra-name').val('');
            $('#extra-type options').first().prop('selected', true);
            $('#extra-amount').val('');


        }
    });
</script>
<style>
    .active {
        background: lightblue;
    }

    .list-image:hover {
        background: lightgray;
    }

    .btn-fake {
        background: transparent;
        border: none;
        color: red;
    }

    @media screen and (max-width: 768px) {
        .increase {
            margin-top: 15px;
        }
    }

    .tt-dataset {
        width: 100%;
    }

    .tt-sugestion {
        z-index: 9999;
    }
    .tt-selectable:hover {
        background: #e4e4e4;
        cursor: pointer;
    }

    .tt-menu {
        background: white;
        width: 100%;
    }

    .twitter-typeahead {
        width: 100%;
    }
</style>