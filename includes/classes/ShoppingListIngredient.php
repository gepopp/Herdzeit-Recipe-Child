<?php
/**
 * Created by PhpStorm.
 * User: offic
 * Date: 09.06.2018
 * Time: 15:23
 */

class ShoppingListIngredient
{

    public $menge;
    public $berechnete_menge;
    public $einheit;
    public $bezeichnung;
    public $recipe_id;
    public $recipe_base_postions;
    public $portions;
    public $ing_table_id;
    public $wg;
    public $precheck = false;
    public $wunderlist_id;

    function __construct($props, $recipe_id, $portions)
    {

        $this->recipe_id = $recipe_id;
        $this->split_props($props);
        $this->get_ingredient_table_id_and_wg();
        $this->get_base_portion();
        $this->portions = $portions;
        $this->calculate_value($portions);
        $this->set_precheck();

    }

    function get_wunderlist_id($list_id){

     if($this->ing_table_id){
         global $wpdb;
         $this->wunderlist_id = $wpdb->get_var('SELECT wunderlist_task FROM wunderlist_id_map WHERE list_id = ' . $list_id . ' AND ingredient_id = ' . $this->ing_table_id );
     }
    }



    function set_precheck(){

        global $wpdb;

        $checked = $wpdb->get_var("SELECT ingredients FROM immer_zuhause WHERE user_id = " . get_current_user_id());
        if(empty($checked)){
            $checked = $wpdb->get_col('SELECT ID FROM ingredients WHERE immerzuhause = 1');
        }
        $checked = maybe_unserialize($checked);



        if(in_array($this->ing_table_id, $checked)){
            $this->precheck = true;
        }
    }



    function get_base_portion(){

        $this->recipe_base_postions = get_post_meta($this->recipe_id, 'recipe_servings', true);
    }

    function get_ingredient_table_id_and_wg(){

        global $wpdb;

        $query = 'SELECT ID, warengruppe FROM ingredients WHERE einheit = "' . $this->einheit . '" AND bezeichnung = "' . $this->bezeichnung . '";';

        $result = $wpdb->get_row( $query );

        if($result){


            $this->ing_table_id = $result->ID;
            $this->wg = $result->warengruppe;
        }
    }

    function split_props($props){
        $props = explode(' ', $props);
        $this->menge = (float) trim(str_replace(',', '.', array_shift($props)));
        $this->einheit = trim(array_shift($props));
        $this->bezeichnung = trim(implode(' ', $props));
    }


    function calculate_value(){

            $menge = number_format(((float)($this->menge / $this->recipe_base_postions) * $this->portions), 2, ',', '.');

            switch ($this->einheit){
                case 'EL':
                case 'Prise':
                case 'g':
                    $this->berechnete_menge = (int) round( $menge );
                    break;
                case 'TL':
                    $this->berechnete_menge = round( $menge, 1 );
                    break;
                default:
                    $this->berechnete_menge = $menge;
            }

    }


}