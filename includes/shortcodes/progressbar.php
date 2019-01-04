<?php
function recipe_progressbar_func( $atts, $content ){
	extract( shortcode_atts( array(
		'label' => '',
		'value' => '',
		'color' => '',
		'bgcolor' => '',
		'label_color' => '',
		'height' => '',
		'font_size' => '',
		'icon' => '',
		'border_radius' => '',
		'style' => ''
	), $atts ) );

	$rnd = recipe_random_string();

	$style_css = '
	<style>
		.'.$rnd.'{
			'.( !empty( $label_color ) ? 'color: '.$label_color.';' : '' ).'
			'.( !empty( $border_radius ) ? 'border-radius: '.$border_radius.';' : '' ).'
			'.( !empty( $height ) ? 'height: '.$height.';' : '' ).'
			'.( !empty( $bgcolor ) ? 'background-color: '.$bgcolor.';' : '' ).'
		}

		.'.$rnd.' .progress-bar{
			'.( !empty( $border_radius ) ? 'border-radius: '.$border_radius.';' : '' ).'
			'.( !empty( $font_size ) ? 'font-size: '.$font_size.';' : '' ).'
			'.( !empty( $height ) ? 'line-height: '.$height.';' : '' ).'
			'.( !empty( $color ) ? 'background-color: '.$color.';' : '' ).'
		}

		.'.$rnd.' .progress-bar-value{
			'.( !empty( $color ) ? 'background-color: '.$color.';' : '' ).'
			'.( !empty( $label_color ) ? 'color: '.$label_color.';' : '' ).'
		}

		.'.$rnd.' .progress-bar-value:after{
			'.( !empty( $color ) ? 'border-color: '.$color.' transparent;' : '' ).'
		}
	</style>
	';

	return recipe_shortcode_style( $style_css ).'
	<div class="progress '.esc_attr( $rnd ).'">
	  <div class="progress-bar '.esc_attr( $style ).'" style="width: '.esc_attr( $value ).'%" role="progressbar" aria-valuenow="'.esc_attr( $value ).'" aria-valuemin="0" aria-valuemax="100">
	  		<div class="progress-bar-value">'.$value.'%</div>
	  		'.( !empty( $icon ) ? '<i class="fa fa-'.esc_attr( $icon ).'"></i>' : '' ).''.esc_attr( $label ).'
	  </div>
	</div>';
}

add_shortcode( 'progressbar', 'recipe_progressbar_func' );

function recipe_progressbar_params(){
	return array(
		array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __("Label","recipe"),
			"param_name" => "label",
			"value" => '',
			"description" => __("Input progress bar label.","recipe")
		),
		array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __("Label Font Size","recipe"),
			"param_name" => "font_size",
			"value" => '',
			"description" => __("Input label font size.","recipe")
		),
		array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __("Value","recipe"),
			"param_name" => "value",
			"value" => '',
			"description" => __("Input progress bar value. Input number only unit is in percentage.","recipe")
		),
		array(
			"type" => "colorpicker",
			"holder" => "div",
			"class" => "",
			"heading" => __("Progress Bar Color","recipe"),
			"param_name" => "color",
			"value" => '',
			"description" => __("Select progress bar color.","recipe")
		),
		array(
			"type" => "colorpicker",
			"holder" => "div",
			"class" => "",
			"heading" => __("Progress Bar Background Color","recipe"),
			"param_name" => "bgcolor",
			"value" => '',
			"description" => __("Select progress bar background color.","recipe")
		),
		array(
			"type" => "colorpicker",
			"holder" => "div",
			"class" => "",
			"heading" => __("Progress Bar Label Color","recipe"),
			"param_name" => "label_color",
			"value" => '',
			"description" => __("Select progress bar label color.","recipe")
		),
		array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __("Progress Bar Height","recipe"),
			"param_name" => "height",
			"value" => '',
			"description" => __("Input progress bar height.","recipe")
		),
		array(
			"type" => "dropdown",
			"holder" => "div",
			"class" => "",
			"heading" => __("Progress Bar Label Icon","recipe"),
			"param_name" => "icon",
			"value" => recipe_awesome_icons_list(),
			"description" => __("Select icon for the label.","recipe")
		),
		array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __("Progress Bar Border Radius","recipe"),
			"param_name" => "border_radius",
			"value" => '',
			"description" => __("Input progress bar border radius.","recipe")
		),
		array(
			"type" => "dropdown",
			"holder" => "div",
			"class" => "",
			"heading" => __("Progress Bar Style","recipe"),
			"param_name" => "style",
			"value" => array(
				__( 'Normal', 'recipe' ) => '',
				__( 'Stripes', 'recipe' ) => 'progress-bar-striped',
				__( 'Active Stripes', 'recipe' ) => 'progress-bar-striped active',
			),
			"description" => __("Select progress bar style.","recipe")
		),
	);
}

if( function_exists( 'vc_map' ) ){
	vc_map( array(
	   "name" => __("Progress Bar", 'recipe'),
	   "base" => "progressbar",
	   "category" => __('Content', 'recipe'),
	   "params" => recipe_progressbar_params()
	) );
}

?>