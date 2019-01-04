<?php
function recipe_column_func( $atts, $content ){
	extract( shortcode_atts( array(
		'md' => '12'
	), $atts ) );

	return '<div class="col-md-'.esc_attr( $md ).'">'.do_shortcode( $content ).'</div>';
}

add_shortcode( 'column', 'recipe_column_func' );

function recipe_column_params(){
	return array(
		array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __("Column Size","recipe"),
			"param_name" => "md",
			"value" => '12',
			"description" => __("Input column size. min 1 max 12. Sum of these numbers in a row must be 12.","recipe")
		),
	);
}
?>