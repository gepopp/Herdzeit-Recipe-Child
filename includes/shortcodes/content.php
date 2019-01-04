<?php
function recipe_content_func( $atts, $content ){
	extract( shortcode_atts( array(
		'padding' => ''
	), $atts ) );

	$style = '';
	if( !empty( $padding ) ){
		$style = 'padding: '.$padding.';';
	}
	return '<div class="white-block">
				<div class="content-inner" style="'.$style.'">
					'.do_shortcode( $content ).'
				</div>
			</div>';
}

add_shortcode( 'content', 'recipe_content_func' );

function recipe_content_params(){
	return array(
		array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __("Content Padding","recipe"),
			"param_name" => "padding",
			"value" => '',
			"description" => __("Input Padding for the content box in for TOP RIGHT BOTTOM LEFT ( For example 10px 5px 0px 5px ).","recipe")
		),
	);
}
?>