<?php
function recipe_gap_func( $atts, $content ){
	extract( shortcode_atts( array(
		'height' => '',
	), $atts ) );

	return '<span style="height: '.esc_attr( $height ).'; display: block;"></span>';
}

add_shortcode( 'gap', 'recipe_gap_func' );

function recipe_gap_params(){
	return array(
		array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __("Gap Height","recipe"),
			"param_name" => "height",
			"value" => '',
			"description" => __("Input gap height.","recipe")
		),
	);
}

if( function_exists( 'vc_map' ) ){
	vc_map( array(
	   "name" => __("Gap", 'recipe'),
	   "base" => "gap",
	   "category" => __('Content', 'recipe'),
	   "params" => recipe_gap_params()
	) );
}
?>