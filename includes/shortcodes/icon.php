<?php
function recipe_icon_func( $atts, $content ){
	extract( shortcode_atts( array(
		'icon' => '',
		'color' => '',
		'size' => '',
	), $atts ) );

	return '<span class="fa fa-'.esc_attr( $icon ).'" style="color: '.esc_attr( $color ).'; font-size: '.esc_attr( $size ).'; margin: 0px 2px;"></span>';
}

add_shortcode( 'icon', 'recipe_icon_func' );

function recipe_icon_params(){
	return array(
		array(
			"type" => "dropdown",
			"holder" => "div",
			"class" => "",
			"heading" => __("Select Icon","recipe"),
			"param_name" => "icon",
			"value" => recipe_awesome_icons_list(),
			"description" => __("Select an icon you want to display.","recipe")
		),
		array(
			"type" => "colorpicker",
			"holder" => "div",
			"class" => "",
			"heading" => __("Icon Color","recipe"),
			"param_name" => "color",
			"value" => '',
			"description" => __("Select color of the icon.","recipe")
		),
		array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __("Icon Size","recipe"),
			"param_name" => "size",
			"value" => '',
			"description" => __("Input size of the icon.","recipe")
		),

	);
}

if( function_exists( 'vc_map' ) ){
	vc_map( array(
	   "name" => __("Icon", 'recipe'),
	   "base" => "icon",
	   "category" => __('Content', 'recipe'),
	   "params" => recipe_icon_params()
	) );
}

?>