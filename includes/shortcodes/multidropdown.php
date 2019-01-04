<?php
if( function_exists( 'vc_map' ) ){
	add_shortcode_param( 'multidropdown', 'recipe_multidropdown', get_template_directory_uri().'/js/multidropdown.js' );
}

function recipe_multidropdown( $settings, $value ) {
	$dependency = '';
	if( function_exists('vc_generate_dependencies_attributes') ){
		$dependency = vc_generate_dependencies_attributes( $settings );
	}

	$select_options = '';

	if( !empty( $settings['value'] ) ){

        $terms = recipe_get_organized( $settings['value'] );
        if( !empty( $terms ) ){
        	ob_start();
            foreach( $terms as $term ){
                recipe_display_select_tree( $term, "", 0, false, $settings['field'] );
            }
	        $select_options = ob_get_contents();
	        ob_end_clean();
        }

	}

	return '
		<div class="multidropdown-param">
			<input type="hidden" name="'.esc_attr( $settings['param_name'] ).'" class="wpb_vc_param_value wpb-textinput '.esc_attr( $settings['param_name'] ).' '.esc_attr( $settings['type'] ).'_field" value="'.esc_attr( $value ).'" ' .esc_attr( $dependency). '/>
			<select name="'.esc_attr( $settings['param_name'] ).''.( function_exists('vc_generate_dependencies_attributes') ? '[]' : '' ).'" multiple class="shortcode-field">
				'.$select_options.'
			</select>
		</div>
	';	
}
?>