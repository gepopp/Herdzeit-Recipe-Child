<?php
function recipe_button_func( $atts, $content ){
	extract( shortcode_atts( array(
		'text' => '',
		'link' => '',
		'target' => '',
		'bg_color' => '',
		'bg_color_hvr' => '',
		'border_radius' => '',
		'icon' => '',
		'font_color' => '',
		'font_color_hvr' => '',
		'size' => 'normal',
		'align' => '',
		'btn_width' => 'normal',
		'inline' => 'no',
		'margin' => ''
	), $atts ) );

	$rnd = recipe_random_string();

	$style_css = '
	<style>
		a.'.$rnd.', a.'.$rnd.':active, a.'.$rnd.':visited, a.'.$rnd.':focus{
			display: '.( $btn_width == 'normal' ? 'inline-block' : 'block' ).';
			'.( !empty( $bg_color ) ? 'background-color: '.$bg_color.';' : '' ).'
			'.( !empty( $font_color ) ? 'color: '.$font_color.';' : '' ).'
			'.( !empty( $border_radius ) ? 'border-radius: '.$border_radius : '' ).'
		}
		a.'.$rnd.':hover{
			display: '.( $btn_width == 'normal' ? 'inline-block' : 'block' ).';
			'.( !empty( $bg_color_hvr ) ? 'background-color: '.$bg_color_hvr.';' : '' ).'
			'.( !empty( $font_color_hvr ) ? 'color: '.$font_color_hvr.';' : '' ).'
		}		
	</style>
	';

	return recipe_shortcode_style( $style_css ).'
	<div class="btn-wrap" style="margin: '.esc_attr( $margin ).'; text-align: '.esc_attr( $align ).'; '.( $inline == 'yes' ? esc_attr( 'display: inline-block;' ) : '' ).' '.( $inline == 'yes' && $align == 'right' ? esc_attr( 'float: right;' ) : '' ).'">
		<a href="'.esc_url( $link ).'" class="btn btn-default '.esc_attr( $size ).' '.esc_attr( $rnd ).' '.( $link != '#' && $link[0] == '#' ? esc_attr( 'slideTo' ) : '' ).'" target="'.esc_attr( $target ).'">
			'.( $icon != 'No Icon' && $icon != '' ? '<i class="fa fa-'.esc_attr( $icon ).' '.( empty( $text ) ? esc_attr( 'no-margin' ) : '' ).'"></i>' : '' ).'
			'.$text.'
		</a>
	</div>';
}

add_shortcode( 'button', 'recipe_button_func' );

function recipe_button_params(){
	return array(
		array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __("Button Text","recipe"),
			"param_name" => "text",
			"value" => '',
			"description" => __("Input button text.","recipe")
		),
		array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __("Button Link","recipe"),
			"param_name" => "link",
			"value" => '',
			"description" => __("Input button link.","recipe")
		),
		array(
			"type" => "dropdown",
			"holder" => "div",
			"class" => "",
			"heading" => __("Select Window","recipe"),
			"param_name" => "target",
			"value" => array(
				__( 'Same Window', 'recipe' ) => '_self',
				__( 'New Window', 'recipe' ) => '_blank',
			),
			"description" => __("Select window where to open the link.","recipe")
		),
		array(
			"type" => "colorpicker",
			"holder" => "div",
			"class" => "",
			"heading" => __("Background Color","recipe"),
			"param_name" => "bg_color",
			"value" => '',
			"description" => __("Select button background color.","recipe")
		),
		array(
			"type" => "colorpicker",
			"holder" => "div",
			"class" => "",
			"heading" => __("Background Color On Hover","recipe"),
			"param_name" => "bg_color_hvr",
			"value" => '',
			"description" => __("Select button background color on hover.","recipe")
		),
		array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __("Button Border Radius","recipe"),
			"param_name" => "border_radius",
			"value" => '',
			"description" => __("Input button border radius. For example 5px or 5ox 9px 0px 0px or 50% or 50% 50% 20% 10%.","recipe")
		),
		array(
			"type" => "dropdown",
			"holder" => "div",
			"class" => "",
			"heading" => __("Select Icon","recipe"),
			"param_name" => "icon",
			"value" => recipe_awesome_icons_list(),
			"description" => __("Select an icon you want to display.","recipe")
		),
		array(
			"type" => "colorpicker",
			"holder" => "div",
			"class" => "",
			"heading" => __("Font Color","recipe"),
			"param_name" => "font_color",
			"value" => '',
			"description" => __("Select button font color.","recipe")
		),
		array(
			"type" => "colorpicker",
			"holder" => "div",
			"class" => "",
			"heading" => __("Font Color On Hover","recipe"),
			"param_name" => "font_color_hvr",
			"value" => '',
			"description" => __("Select button font color on hover.","recipe")
		),
		array(
			"type" => "dropdown",
			"holder" => "div",
			"class" => "",
			"heading" => __("Button Size","recipe"),
			"param_name" => "size",
			"value" => array(
				__( 'Normal', 'recipe' ) => '',
				__( 'Medium', 'recipe' ) => 'medium',
				__( 'Large', 'recipe' ) => 'large',
			),
			"description" => __("Select button size.","recipe")
		),
		array(
			"type" => "dropdown",
			"holder" => "div",
			"class" => "",
			"heading" => __("Button Align","recipe"),
			"param_name" => "align",
			"value" => array(
				__( 'Left', 'recipe' ) => 'left',
				__( 'Center', 'recipe' ) => 'center',
				__( 'Right', 'recipe' ) => 'right',
			),
			"description" => __("Select button align.","recipe")
		),
		array(
			"type" => "dropdown",
			"holder" => "div",
			"class" => "",
			"heading" => __("Select Button Width","recipe"),
			"param_name" => "btn_width",
			"value" => array(
				__( 'Normal', 'recipe' ) => 'normal',
				__( 'Full Width', 'recipe' ) => 'full',
			),
			"description" => __("Select button width.","recipe")
		),
		array(
			"type" => "dropdown",
			"holder" => "div",
			"class" => "",
			"heading" => __("Display Inline","recipe"),
			"param_name" => "inline",
			"value" => array(
				__( 'No', 'recipe' ) => 'no',
				__( 'Yes', 'recipe' ) => 'yes',
			),
			"description" => __("Display button inline.","recipe")
		),
		array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __("Button Margins","recipe"),
			"param_name" => "margin",
			"value" => '',
			"description" => __("Add button margins.","recipe")
		),
	);
}

if( function_exists( 'vc_map' ) ){
	vc_map( array(
	   "name" => __("Button", 'recipe'),
	   "base" => "button",
	   "category" => __('Content', 'recipe'),
	   "params" => recipe_button_params()
	) );
}

?>