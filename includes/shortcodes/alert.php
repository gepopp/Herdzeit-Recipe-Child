<?php
function recipe_alert_func( $atts, $content ){
	extract( shortcode_atts( array(
		'text' => '',
		'border_color' => '',
		'bg_color' => '',
		'font_color' => '',
		'icon' => '',
		'closeable' => 'no',
		'close_icon_color' => '',
		'close_icon_color_hvr' => '',
	), $atts ) );

	$rnd = recipe_random_string();

	$style_css = '
		<style>
			.'.$rnd.'.alert .close{
				color: '.$close_icon_color.';
			}
			.'.$rnd.'.alert .close:hover{
				color: '.$close_icon_color_hvr.';
			}
		</style>
	';

	return recipe_shortcode_style( $style_css ).'
	<div class="alert '.esc_attr( $rnd ).' alert-default '.( $closeable == 'yes' ? esc_attr( 'alert-dismissible' ) : '' ).'" role="alert" style=" color: '.esc_attr( $font_color ).'; border-color: '.esc_attr( $border_color ).'; background-color: '.esc_attr( $bg_color ).';">
		'.( !empty( $icon ) && $icon !== 'No Icon' ? '<i class="fa fa-'.esc_attr( $icon ).'"></i>' : '' ).'
		'.$text.'
		'.( $closeable == 'yes' ? '<button type="button" class="close" data-dismiss="alert"> <span aria-hidden="true">×</span> <span class="sr-only">'.__( 'Close', 'recipe' ).'</span> </button>' : '' ).'
	</div>';
}

add_shortcode( 'alert', 'recipe_alert_func' );

function recipe_alert_params(){
	return array(
		array(
			"type" => "textarea",
			"holder" => "div",
			"class" => "",
			"heading" => __("Text","recipe"),
			"param_name" => "text",
			"value" => '',
			"description" => __("Input alert text.","recipe")
		),
		array(
			"type" => "colorpicker",
			"holder" => "div",
			"class" => "",
			"heading" => __("Border Color","recipe"),
			"param_name" => "border_color",
			"value" => '',
			"description" => __("Select border color for the alert box.","recipe")
		),
		array(
			"type" => "colorpicker",
			"holder" => "div",
			"class" => "",
			"heading" => __("Background Color Color","recipe"),
			"param_name" => "bg_color",
			"value" => '',
			"description" => __("Select background color of the alert box.","recipe")
		),
		array(
			"type" => "colorpicker",
			"holder" => "div",
			"class" => "",
			"heading" => __("Text Color","recipe"),
			"param_name" => "font_color",
			"value" => '',
			"description" => __("Select font color for the alert box text.","recipe")
		),
		array(
			"type" => "dropdown",
			"holder" => "div",
			"class" => "",
			"heading" => __("Icon","recipe"),
			"param_name" => "icon",
			"value" => recipe_awesome_icons_list(),
			"description" => __("Select icon.","recipe")
		),
		array(
			"type" => "dropdown",
			"holder" => "div",
			"class" => "",
			"heading" => __("Closeable","recipe"),
			"param_name" => "closeable",
			"value" => array(
				__( 'No', 'recipe' ) => 'no',
				__( 'Yes', 'recipe' ) => 'yes'
			),
			"description" => __("Enable or disable alert closing.","recipe")
		),
		array(
			"type" => "colorpicker",
			"holder" => "div",
			"class" => "",
			"heading" => __("Close Icon Color","recipe"),
			"param_name" => "close_icon_color",
			"value" => '',
			"description" => __("Select color for the close icon.","recipe")
		),
		array(
			"type" => "colorpicker",
			"holder" => "div",
			"class" => "",
			"heading" => __("Close Icon Color On Hover","recipe"),
			"param_name" => "close_icon_color_hvr",
			"value" => '',
			"description" => __("Select color for the close icon on hover.","recipe")
		),
	);
}

if( function_exists( 'vc_map' ) ){
	vc_map( array(
	   "name" => __("Alert", 'recipe'),
	   "base" => "alert",
	   "category" => __('Content', 'recipe'),
	   "params" => recipe_alert_params()
	) );
}
?>