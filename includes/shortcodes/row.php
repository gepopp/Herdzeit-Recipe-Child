<?php
function recipe_row_func( $atts, $content ){

	return '<div class="row">'.do_shortcode( $content ).'</div>';
}

add_shortcode( 'row', 'recipe_row_func' );

function recipe_row_params(){
	return array();
}
?>