<?php
function recipe_bg_gallery_func( $atts, $content ){
	extract( shortcode_atts( array(
		'images' => '',
		'thumb_image_size' => 'post-thumbnail',
		'columns' => '3',
	), $atts ) );

	ob_start();
	echo do_shortcode( '[gallery columns="'.esc_attr( $columns ).'" ids="'.esc_attr( $images ).'" size="'.esc_attr( $thumb_image_size ).'"]' );
	$output = ob_get_contents();
	ob_end_clean();
	return $output;
}

add_shortcode( 'bg_gallery', 'recipe_bg_gallery_func' );

function recipe_get_image_sizes(){
	$sizes = get_intermediate_image_sizes();
	$sizes_right = array();
	foreach( $sizes as $size ){
		$sizes_right[$size] = $size;
	}

	return $sizes_right;
}

function recipe_bg_gallery_params(){
	return array(
		array(
			"type" => "attach_images",
			"holder" => "div",
			"class" => "",
			"heading" => __("Select Images","recipe"),
			"param_name" => "images",
			"value" => '',
			"description" => __("Select images for the gallery.","recipe")
		),
		array(
			"type" => "dropdown",
			"holder" => "div",
			"class" => "",
			"heading" => __("Thumbnail Image Size","recipe"),
			"param_name" => "thumb_image_size",
			"value" => recipe_get_image_sizes(),
			"description" => __("Select image size you want to display for the thumbnails.","recipe")
		),
		array(
			"type" => "dropdown",
			"holder" => "div",
			"class" => "",
			"heading" => __("Columns","recipe"),
			"param_name" => "columns",
			"value" => array(
				'1' => '1',
				'2' => '2',
				'3' => '3',
				'4' => '4',
				'5' => '5',
				'6' => '6',
				'7' => '7',
				'8' => '8',
				'9' => '9',
				'10' => '10',
			),
			"description" => __("Select number of columns for the thumbnails.","recipe")
		),
	);
}
if( function_exists( 'vc_map' ) ){
	vc_map( array(
	   "name" => __("Gallery", 'recipe'),
	   "base" => "bg_gallery",
	   "category" => __('Content', 'recipe'),
	   "params" => recipe_bg_gallery_params()
	) );
}

?>