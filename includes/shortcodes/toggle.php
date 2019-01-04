<?php
function recipe_toggle_func( $atts, $content ){
	extract( shortcode_atts( array(
		'title' => '',
		'toggle_content' => '',
		'state' => '',
	), $atts ) );

	$contents = !empty( $toggle_content ) ? $toggle_content : $content; 

	$rnd = recipe_random_string();

	return '
		<div class="panel-group" id="accordion_'.esc_attr( $rnd ).'" role="tablist" aria-multiselectable="true">
		  <div class="panel panel-default">
		    <div class="panel-heading" role="tab" id="heading_'.esc_attr( $rnd ).'">
		      <div class="panel-title">
		        <a class="'.( $state == 'in' ? '' : esc_attr( 'collapsed' ) ).'" data-toggle="collapse" data-parent="#accordion_'.esc_attr( $rnd ).'" href="#coll_'.esc_attr( $rnd ).'" aria-expanded="true" aria-controls="coll_'.esc_attr( $rnd ).'">
		        	'.$title.'
		        	<i class="fa fa-chevron-circle-down animation"></i>
		        </a>
		      </div>
		    </div>
		    <div id="coll_'.esc_attr( $rnd ).'" class="panel-collapse collapse '.esc_attr( $state ).'" role="tabpanel" aria-labelledby="heading_'.esc_attr( $rnd ).'">
		      <div class="panel-body">
		        '.apply_filters( 'the_content', $contents ).'
		      </div>
		    </div>
		  </div>
		</div>';
}

add_shortcode( 'toggle', 'recipe_toggle_func' );

function recipe_toggle_params(){
	return array(
		array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __("Title","recipe"),
			"param_name" => "title",
			"value" => '',
			"description" => __("Input toggle title.","recipe")
		),
		array(
			"type" => "textarea_raw_html",
			"holder" => "div",
			"class" => "",
			"heading" => __("Content","recipe"),
			"param_name" => "toggle_content",
			"value" => '',
			"description" => __("Input toggle title.","recipe")
		),
		array(
			"type" => "dropdown",
			"holder" => "div",
			"class" => "",
			"heading" => __("Default State","recipe"),
			"param_name" => "state",
			"value" => array(
				__( 'Closed', 'recipe' ) => '',
				__( 'Opened', 'recipe' ) => 'in',
			),
			"description" => __("Select default toggle state.","recipe")
		),

	);
}

if( function_exists( 'vc_map' ) ){
	vc_map( array(
	   "name" => __("Toggle", 'recipe'),
	   "base" => "toggle",
	   "category" => __('Content', 'recipe'),
	   "params" => recipe_toggle_params()
	) );
}

?>