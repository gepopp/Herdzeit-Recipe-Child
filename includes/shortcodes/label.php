<?php
function recipe_label_func( $atts, $content ){
	extract( shortcode_atts( array(
		'text' => '',
		'bg_color' => '',
		'font_color' => '',
	), $atts ) );

	return '<span class="label label-default" style="color: '.esc_attr( $font_color ).'; background-color: '.esc_attr( $bg_color ).'">'.$text.'</span>';
}

add_shortcode( 'label', 'recipe_label_func' );

function recipe_label_params(){
	return array(
		array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __("Text","recipe"),
			"param_name" => "text",
			"value" => '',
			"description" => __("Input label text.","recipe")
		),
		array(
			"type" => "colorpicker",
			"holder" => "div",
			"class" => "",
			"heading" => __("Background Color Color","recipe"),
			"param_name" => "bg_color",
			"value" => '',
			"description" => __("Select background color of the label.","recipe")
		),
		array(
			"type" => "colorpicker",
			"holder" => "div",
			"class" => "",
			"heading" => __("Text Color","recipe"),
			"param_name" => "font_color",
			"value" => '',
			"description" => __("Select font color for the label text.","recipe")
		),

	);
}

if( function_exists( 'vc_map' ) ){
	vc_map( array(
	   "name" => __("Label", 'recipe'),
	   "base" => "label",
	   "category" => __('Content', 'recipe'),
	   "params" => recipe_label_params()
	) );
}

?>