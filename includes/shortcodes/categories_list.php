<?php
function recipe_categories_func( $atts, $content ){
	global $recipe_slugs;
	extract( shortcode_atts( array(
		'icon' => '',
		'title' => '',
		'small_title' => '',
		'categories' => '',
	), $atts ) );
	$permalink = recipe_get_permalink_by_tpl( 'page-tpl_search' );
	if( empty( $categories ) ){
		$categories = get_terms( 'recipe-category', array( 'parent' => 0 ) );
	}
	else{
		$categories = explode(",", $categories);
		$categories = get_terms( 'recipe-category', array( 'include' => $categories ) );
	}

	ob_start();
	?>
	<section>
		<div class="container">
			<div class="section-title clearfix">
				<h3 class="pull-left">
					<?php if( $icon !== 'No Icon' ): ?>
						<i class="fa fa-<?php echo esc_attr( $icon ) ?>"></i>
					<?php endif; ?>
					<?php echo $title; ?>
				</h3>
				<a href="<?php echo esc_url( $permalink ) ?>" class="btn pull-right"><?php echo $small_title ?></a>
			</div>			
			<div class="row category-list">
				<?php
				foreach( $categories as $category ){
					$term_meta = get_option( "taxonomy_".$category->term_id );
					$value = !empty( $term_meta['category_icon'] ) ? $term_meta['category_icon'] : '';						
					echo '<div class="col-sm-4"><a href="'.esc_url( add_query_arg( array( $recipe_slugs['recipe-category'] => $category->slug ), $permalink ) ).'"><span class="icon '.esc_attr( $value ).'"></span> '.$category->name.'</a></div>';
				}
				?>
			</div>
		</div>
	</section>
	<?php
	$content = ob_get_contents();
	ob_end_clean();

	return $content;
}

add_shortcode( 'categories_list', 'recipe_categories_func' );

function recipe_categories_list_params(){
	return array(
		array(
			"type" => "dropdown",
			"holder" => "div",
			"class" => "",
			"heading" => __("Icon","recipe"),
			"param_name" => "icon",
			"value" => recipe_awesome_icons_list(),
			"description" => __("Select icon for the category list.","recipe")
		),
		array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __("Title","recipe"),
			"param_name" => "title",
			"value" => '',
			"description" => __("Input title for the categories block.","recipe")
		),
		array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __("Small Title","recipe"),
			"param_name" => "small_title",
			"value" => '',
			"description" => __("Input title for the button to all categories.","recipe")
		),
		array(
			"type" => "multidropdown",
			"holder" => "div",
			"class" => "",
			"heading" => __("Categories","recipe"),
			"param_name" => "categories",
			"value" => 'recipe-category',
			"field" => 'term_id',
			"description" => __("Select categories to show.","recipe")
		),		
	);
}

if( function_exists( 'vc_map' ) ){
	vc_map( array(
	   "name" => __("Recipe Categories", 'recipe'),
	   "base" => "categories_list",
	   "category" => __('Content', 'recipe'),
	   "params" => recipe_categories_list_params()
	) );
}
?>