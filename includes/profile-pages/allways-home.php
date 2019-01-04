<?php
global $wpdb;
$ingredients = $wpdb->get_results('SELECT ID, bezeichnung, immerzuhause FROM ingredients ORDER BY bezeichnung');

$user_set = $wpdb->get_row("SELECT ingredients FROM immer_zuhause WHERE user_id = " . get_current_user_id());
$defaults = empty($user_set) ? true : false;

$precheck = $wpdb->get_var('SELECT ingredients FROM immer_zuhause WHERE user_id = ' . get_current_user_id());
$precheck = maybe_unserialize($precheck);


foreach ($ingredients as $ingredient) {

    $bezeichnung = $ingredient->bezeichnung;
    $bezeichnung = explode('(', $bezeichnung);
    $bezeichnung = trim($bezeichnung[0]);


    $sorter[$bezeichnung][] = $ingredient;
}
foreach ($sorter as $s) {
    $immerzuhause = false;
    foreach ($s as $ing) {
        if ($ing->immerzuhause == 1) $immerzuhause = true;
    }
    if ($immerzuhause) {
        foreach ($s as $ing) {
            $ing->immerzuhause = 1;
        }
    }

}
?>
<h4>Immer Zuhause</h4>
<hr>
<p>Die von dir hier markierten Zutaten werden in den Einkaufslisten immer voraus abgehackt.</p>
<hr>
<div class="row">
    <?php
    foreach ($sorter

    as $bez => $values) {
    if (!$values[0]->immerzuhause) continue;
    $ids = '';
    foreach ($values as $value) {
        $ids .= $value->ID;
        $ids .= ',';
    }
    $ids = rtrim($ids, ',');

    if ($defaults): ?>
    <div class="checkable col-sm-4 checked"
    " data-ids="<?= $ids ?>"><a class="fake-checkbox"><i class="fa fa-check"></i> </a> <?= $bez ?></div>
<? else: ?>
    <div class="checkable col-sm-4 <?= in_array($values[0]->ID, $precheck) ? "checked" : "" ?>"
         data-ids="<?= $ids ?>" data-ids="<?= $ids ?>"><a class="fake-checkbox"><i
                    class="fa fa-check"></i></a> <?= $bez ?></div>
<?
endif;
}
$section = 'A';
$range = range('A', 'Z');
?>
</div>
<hr>
<div class="row">
    <div class="col-xs-12 text-center">
        <ul class="list-inline">
            <?php foreach($range as $letter){
               echo '<li><span class="scrollto" data-scrollTo="'.$letter.'"> ' . $letter . '</a></li>';
            }?>
        </ul>
    </div>
</div>
<hr>
<h5>A</h5>
<div class="row"><?php
    foreach ($sorter as $bez => $values) {
        if ($values[0]->immerzuhause) continue;


        if(mb_substr(mb_strtoupper($bez, 'UTF-8'),0,1) != $section){
            $section = mb_substr(mb_strtoupper($bez, 'UTF-8'),0,1);
            echo '</div><hr><h5 id="scrollTo-'.$section.'">'.$section.'</h5><div class="row">';
        }


        $ids = '';
        foreach ($values as $value) {
            $ids .= $value->ID;
            $ids .= ',';
        }
        $ids = rtrim($ids, ',');
        ?>
        <div class="checkable col-sm-4 <?= in_array($values[0]->ID, $precheck) ? "checked" : "" ?>"
             data-ids="<?= $ids ?>" data-ids="<?= $ids ?>"><a class="fake-checkbox"><i
                        class="fa fa-check"></i></a> <?= $bez ?></div>

        <?
    }
    ?>
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
</style>
<script>
    jQuery(document).ready(function ($) {

        $('.scrollto').click(function () {


            var letter = $(this).data('scrollto');
            var target =  $("#scrollTo-" + letter);
            console.log(target);

            $('html, body').animate({
                scrollTop: $("#scrollTo-" + letter).offset().top -150
            }, 2000);
        });



        update_immerzuhause();

        $('.checkable').click(function () {
            $(this).toggleClass('checked');
            update_immerzuhause();
        });
        function update_immerzuhause() {
            var ids = '';
            $('.checked').each(function () {
                ids += $(this).data('ids');
                ids += ',';
            });
            ids = ids.replace(/,+$/, '');

            setTimeout(function () {
                $.post(
                    ajaxurl,
                    {
                        action: "update_user_immer_zuhause",
                        ids: ids
                    },
                    function (rsp) {
                        console.log(rsp);
                    }
                );
            }, 500);
        }
    })
</script>