<?php
function recipe_accordion_func( $atts, $content ){
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

	$rnd = recipe_random_string();

	$html = '';

	if( !empty( $titles ) ){
		for( $i=0; $i<sizeof( $titles ); $i++ ){
			if( !empty( $titles[$i] ) ){
				$html .= '
				  <div class="panel panel-default">
				    <div class="panel-heading" role="tab" id="heading_'.esc_attr( $i ).'">
				      <div class="panel-title">
				        <a class="'.( $i !== 0 ? 'collapsed' : '' ).'" data-toggle="collapse" data-parent="#accordion_'.esc_attr( $rnd ).'" href="#coll_'.esc_attr( $i ).'_'.esc_attr( $rnd ).'" aria-expanded="true" aria-controls="coll_'.esc_attr( $i ).'_'.esc_attr( $rnd ).'">
				        	'.$titles[$i].'
				        	<i class="fa fa-chevron-circle-down animation"></i>
				        </a>
				      </div>
				    </div>
				    <div id="coll_'.esc_attr( $i ).'_'.esc_attr( $rnd ).'" class="panel-collapse collapse '.( $i == 0 ? 'in' : '' ).'" role="tabpanel" aria-labelledby="heading_'.esc_attr( $i).'">
				      <div class="panel-body">
				        '.( !empty( $contents[$i] ) ? apply_filters( 'the_content', $contents[$i] ) : '' ).'
				      </div>
				    </div>
				  </div>
				';
			}
		}
	}

	return '
		<div class="panel-group" id="accordion_'.esc_attr( $rnd ).'" role="tablist" aria-multiselectable="true">
		'.$html.'
		</div>';
}

add_shortcode( 'accordion', 'recipe_accordion_func' );

function recipe_accordion_params(){
	return array(
		array(
			"type" => "textarea",
			"holder" => "div",
			"class" => "",
			"heading" => __("Titles","recipe"),
			"param_name" => "titles",
			"value" => '',
			"description" => __("Input accordion titles separated by /n/.","recipe")
		),
		array(
			"type" => "textarea_raw_html",
			"holder" => "div",
			"class" => "",
			"heading" => __("Contents","recipe"),
			"param_name" => "contents",
			"value" => '',
			"description" => __("Input accordion contents separated by /n/.","recipe")
		),

	);
}

if( function_exists( 'vc_map' ) ){
	vc_map( array(
	   "name" => __("Accordion", 'recipe'),
	   "base" => "accordion",
	   "category" => __('Content', 'recipe'),
	   "params" => recipe_accordion_params()
	) );
}
?>