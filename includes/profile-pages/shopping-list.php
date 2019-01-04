<?php

global $wpdb;
$lists = $wpdb->get_results('SELECT * FROM ' . SHOPPING_LIST_TABLE . ' WHERE user_id = ' . get_current_user_id());

foreach($lists as $list){
    if(!$list->image){
        $list->image = "shopping-list.jpg";
    }
}



function count_list_positions($recipe_postions)
{
    $position_counter = 0;
    foreach ($recipe_postions as $recipe) {
        $recipe_id = $recipe->recipe;
        $recipe_ingredient = get_post_meta($recipe_id, 'recipe_ingredient', true);
        $recipe_ingredients_ld = $recipe_ingredient;
        $recipe_ingredients_ld = str_replace(array("#", "\r"), "", $recipe_ingredients_ld);
        $recipe_ingredients_ld = explode("\n", $recipe_ingredients_ld);
        $recipe_ingredients = explode("\n", $recipe_ingredient);
        foreach ($recipe_ingredients as $index => $ingredient) {
            if (!strpos($ingredient, '#')) {
                $position_counter++;
            }
        }
    }
    return $position_counter;
}

?>
<h4>Deine Einkaufslisten</h4>
<hr>
<span class="pull-right"><a href="<?= add_query_arg('page', 'allways-home', home_url('mein-herdzeit')) ?>" class="btn btn-primary"><i class="fa fa-home"></i> Immer Zuhause</a></span>
<div class="clearfix"></div>
<hr>
<? if(count($lists) < 3 ): ?>
<a href="<?= home_url('/mein-herdzeit/?page=edit-shopping-list') ?>" class="btn btn-primary"><i class="fa fa-plus"></i> Neue Liste erstellen</a>
<hr>
<?endif;?>

<div class="bt-table">
    <table data-toggle="table" class="table table-striped">
        <thead>
        <tr>
            <th class="hidden-xs">
                Bild
            </th>
            <th data-field="image">
                Liste
            </th>
            <th class="hidden-xs">
                Erstellt
            </th class="hidden-xs">
            <th class="hidden-xs">
                Rezepte
            </th>
            <th class="hidden-xs">
                Posten
            </th>
            <th data-field="action" data-sortable="true">
                Aktionen
            </th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($lists as $list): ?>

            <tr>
                <td class="hidden-xs"><img src="<?= get_stylesheet_directory_uri() ?>/images/shopping-list/<?= $list->image ?>"></td>
                <td style="padding-top: 20px"><span style="padding-top: 20px"><?= $list->list_name ?></span></td>
                <td class="hidden-xs"><?= date('d.m.Y', strtotime($list->created_at)) ?></td>
                <td class="hidden-xs"><?= $list->recipe_count ?></td>
                <td class="hidden-xs"><?= count_list_positions(maybe_unserialize($list->recipes_portions)) ?></td>
                <td>
                    <ul class="list-unstyled list-inline">
                        <li class="tip" data-title="Liste bearbeiten" style="margin-top: 15px;">
                            <a href="<?php echo esc_url(add_query_arg(array('page' => 'edit-shopping-list', 'list-id' => $list->ID), $permalink)) ?>">
                                <i style="font-size:1.5em" class="fa fa-pencil"></i>
                            </a>
                        </li>
                        <? if( $list->recipe_count != 0 || count_list_positions(maybe_unserialize($list->recipes_portions)) != 0): ?>
                        <li class="tip mt-2" data-title="Zutatenliste">
                            <a href="<?= add_query_arg('list-id', $list->ID,  home_url('mein-herdzeit?page=edit-ingredient')) ?>"><i class="fa fa-list"></i></a></li>
                        <? endif; ?>
                        <li class="tip mt-2" data-title="Liste löschen"  style="margin-top: 15px;">
                            <form action="<?= admin_url('admin-post.php') ?>" method="POST"
                                  id="delete-list-<?= $list->ID ?>">
                                <input type="hidden" name="action" value="delete_shopping_list">
                                <input type="hidden" name="list_id" value="<?= $list->ID ?>">
                                <?= wp_nonce_field('delete_shopping_list', 'delete_shopping_list_nonce') ?>
                                <i style="font-size:1.5em" class="fa fa-trash"
                                   onclick="
                                           if(confirm('Wirklich löschen?'))
                                           document.getElementById('delete-list-<?= $list->ID ?>').submit()
                                           "></i>
                            </form>
                        </li>
                    </ul>
                </td>
            </tr>

        <?php endforeach; ?>
        </tbody>
        <tfoot></tfoot>
    </table>
</div>
