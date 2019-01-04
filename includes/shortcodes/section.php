<?php
function recipe_section_func( $atts, $content ){
	return '<section>
				<div class="container">
					'.do_shortcode( $content ).'
				</div>
			</section>';
}

add_shortcode( 'section', 'recipe_section_func' );

function recipe_section_params(){
	return array();
}
?>