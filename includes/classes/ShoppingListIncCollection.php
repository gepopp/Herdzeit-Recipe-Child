<?php
/**
 * Created by PhpStorm.
 * User: offic
 * Date: 09.06.2018
 * Time: 16:19
 */
if (!class_exists('ShoppingListIngredient')) require_once get_stylesheet_directory() . '/includes/classes/ShoppingListIngredient.php';

class ShoppingListIncCollection
{

    public $items;
    public $combined_items = [];


    function __construct($recipes_posrtions, $list_id = null)
    {
        foreach ($recipes_posrtions as $recipes_portion) {

            $ingredients = get_post_meta($recipes_portion->recipe, 'recipe_ingredient', true);
            $ingredients = explode("\n", $ingredients);
            foreach ($ingredients as $ingredient) {
                if (preg_match('/^#/', $ingredient)) continue;
                $item = new ShoppingListIngredient($ingredient, $recipes_portion->recipe, $recipes_portion->portions);
                $item->get_wunderlist_id($list_id);
                $this->items[] = $item;
            }
        }
    }

    function combine(){


        foreach ($this->items as $item){

            $combined = false;

            foreach($this->combined_items as $citem){

                if($citem->ing_table_id == $item->ing_table_id){
                   $citem->berechnete_menge += $item->berechnete_menge;


                   $citem->berechnete_menge = round($citem->berechnete_menge,2);
                   if($citem->berechnete_menge % 1 == 0 || strtolower($citem->einheit ) == 'g' || strtolower( $citem->einheit ) == 'ml'){
                       $citem->berechnete_menge = (int) $citem->berechnete_menge;
                    }else{
                       $citem->berechnete_menge = number_format($citem->berechnete_menge, 2, ',', '.');
                   }



                   $combined = true;
                }

            }

            if(!$combined)
            $this->combined_items[] = $item;
        }
    }


    function sort_by_wg(){

        $wgs = get_field('field_5b1a9c0a9199d', 'option');
        foreach ($wgs as $wg){
            $wg_list[] = $wg['warengruppenbezeichung'];
        }
        foreach ($this->combined_items as $item){

            $sorted[$item->wg ? $item->wg : 'unsorted' ][] = $item;

        }
        $properOrderedArray = array_merge(array_flip($wg_list), $sorted);
        $this->combined_items = [];
        foreach ($properOrderedArray as $item) {
            if(is_array($item)){

                usort($item, function ($a, $b){
                   return strcmp($a->bezeichnung, $b->bezeichnung);
                });

                foreach ($item as $ing){
                    $this->combined_items[] = $ing;
                }
            }

        }
    }


    function get_items_assoc_array()
    {
        foreach($this->items as $item){
            $return[] = get_object_vars($item);
        }
        return $return;
    }



    function get_combined_items_assoc_array()
    {
        foreach($this->combined_items as $item){
            $return[] = get_object_vars($item);
        }
        return $return;
    }
}