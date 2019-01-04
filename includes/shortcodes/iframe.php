<?php
function recipe_iframe_func( $atts, $content ){
	extract( shortcode_atts( array(
		'link' => '',
		'proportion' => '',
	), $atts ) );

	$random = recipe_random_string();

	return '
		<div class="embed-responsive embed-responsive-'.esc_attr( $proportion ).'">
		  <iframe class="embed-responsive-item" src="'.esc_url( $link ).'"></iframe>
		</div>';
}

add_shortcode( 'iframe', 'recipe_iframe_func' );

function recipe_iframe_params(){
	return array(
		array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __("Iframe link","recipe"),
			"param_name" => "link",
			"value" => '',
			"description" => __("Input link you want to embed.","recipe")
		),
		array(
			"type" => "dropdown",
			"holder" => "div",
			"class" => "",
			"heading" => __("Iframe Proportion","recipe"),
			"param_name" => "proportion",
			"value" => array(
				__( '4 by 3', 'recipe' ) => '4by3',
				__( '16 by 9', 'recipe' ) => '16by9',
			),
			"description" => __("Select iframe proportion.","recipe")
		),

	);
}

if( function_exists( 'vc_map' ) ){
	vc_map( array(
	   "name" => __("Iframe", 'recipe'),
	   "base" => "iframe",
	   "category" => __('Content', 'recipe'),
	   "params" => recipe_iframe_params()
	) );
}

?>