<?php
function recipe_tabs_func( $atts, $content ){
	extract( shortcode_atts( array(
		'titles' => '',
		'contents' => ''
	), $atts ) );

	$titles = explode( "/n/", $titles );
	if( !empty( $contents ) ){
		$contents = explode( "/n/", $contents );
	}
	else{
		$contents = explode( "/n/", $content );	
	}

	$titles_html = '';
	$contents_html = '';

	$random = recipe_random_string();

	if( !empty( $titles ) ){
		for( $i=0; $i<sizeof( $titles ); $i++ ){
			$titles_html .= '<li role="presentation" class="'.( $i == 0 ? esc_attr( 'active' ) : '' ).'"><a href="#tab_'.esc_attr( $i ).'_'.esc_attr( $random ).'" role="tab" data-toggle="tab">'.$titles[$i].'</a></li>';
			$contents_html .= '<div role="tabpanel" class="tab-pane fade '.( $i == 0 ? esc_attr( 'in active' ) : '' ).'" id="tab_'.esc_attr( $i ).'_'.esc_attr( $random ).'">'.( !empty( $contents[$i] ) ? apply_filters( 'the_content', $contents[$i] ) : '' ).'</div>';
		}
	}

	return '
	<!-- Nav tabs -->
	<ul class="nav nav-tabs" role="tablist">
	  '.$titles_html.'
	</ul>

	<!-- Tab panes -->
	<div class="tab-content">
	  '.$contents_html.'
	</div>';
}

add_shortcode( 'tabs', 'recipe_tabs_func' );

function recipe_tabs_params(){
	return array(
		array(
			"type" => "textarea",
			"holder" => "div",
			"class" => "",
			"heading" => __("Titles","recipe"),
			"param_name" => "titles",
			"value" => '',
			"description" => __("Input tab titles separated by /n/.","recipe")
		),
		array(
			"type" => "textarea_raw_html",
			"holder" => "div",
			"class" => "",
			"heading" => __("Contents","recipe"),
			"param_name" => "contents",
			"value" => '',
			"description" => __("Input tab contents separated by /n/.","recipe")
		),

	);
}

if( function_exists( 'vc_map' ) ){
	vc_map( array(
	   "name" => __("Tabs", 'recipe'),
	   "base" => "tabs",
	   "category" => __('Content', 'recipe'),
	   "params" => recipe_tabs_params()
	) );
}

?>