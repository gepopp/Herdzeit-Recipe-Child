<?php
function recipe_func( $atts, $content ){
	extract( shortcode_atts( array(
		'icon' => '',
		'title' => '',
		'small_title' => '',
		'number' => '3',
		'style' => '1',
		'source' => '',
		'category' => '',
		'cuisine' => '',
        'season'  => '',
	), $atts ) );
	$permalink = recipe_get_permalink_by_tpl( 'page-tpl_search' );

	$args = array(
		'tax_query' => array()
	);

	$no_title = false;
	if( empty( $title ) || empty( $icon ) || $icon == 'No Icon' || empty( $small_title ) ){
		$no_title = true;
	}	

	switch( $source ){
		case 'latest' :
			$permalink = add_query_arg( array( 'sort' => 'date-desc' ), $permalink );
			break;
		case 'top-rated' :
			$permalink = add_query_arg( array( 'sort' => 'ratings-desc' ), $permalink );
			$args = array(
				'orderby' => 'meta_value_num',
				'order' => 'DESC',
				'meta_key' => 'average_review'
			);
			break;
		case 'most-favourited' :
			$permalink = add_query_arg( array( 'sort' => 'favourited-desc' ), $permalink );
			$args = array(
				'orderby' => 'meta_value_num',
				'order' => 'DESC',
				'meta_key' => 'favourited'
			);			
			break;
		case 'most-liked' :
			$permalink = add_query_arg( array( 'sort' => 'likes-desc' ), $permalink );
			$args = array(
				'orderby' => 'meta_value_num',
				'order' => 'DESC',
				'meta_key' => 'likes'
			);			
			break;
		case 'most-viewed' :
			$permalink = add_query_arg( array( 'sort' => 'views-desc' ), $permalink );
			$args = array(
				'orderby' => 'meta_value_num',
				'order' => 'DESC',
				'meta_key' => 'views'
			);
			break;			
	}

	if( !empty( $category ) ){
		$category = explode( ',', $category );
		$args['tax_query'][] = array(
			array(
				'taxonomy' => 'recipe-category',
				'field'    => 'slug',
				'terms'    => $category,
			),
		);
	}

	if( !empty( $cuisine ) ){
		$cuisine = explode( ',', $cuisine );
		$args['tax_query'][] = array(
			array(
				'taxonomy' => 'recipe-cuisine',
				'field'    => 'slug',
				'terms'    => $cuisine,
			),
		);
	}
    if( !empty( $season ) ){
        $permalink = get_term_link($season, 'season');
        $season = explode( ',', $season );
        $args['tax_query'][] = array(
            array(
                'taxonomy' => 'season',
                'field'    => 'slug',
                'terms'    => $season,
            ),
        );
    }

    ob_start();

	?>
	<section class="<?php echo $no_title ? 'no-title' : '' ?>">
		<div class="container">
			<?php if( !$no_title ): ?>
				<div class="section-title clearfix">
					<h3 class="pull-left">
						<?php if( $icon !== 'No Icon' ): ?>
							<i class="fa fa-<?php echo esc_attr( $icon ); ?>"></i>
						<?php endif; ?>
						<?php echo $title; ?>
					</h3>
					<?php if( !empty( $small_title ) ): ?>
						<a href="<?php echo esc_url( $permalink ) ?>" class="btn pull-right"><?php echo $small_title; ?></a>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<div class="row">
				<?php
				$args['post_type'] = 'recipe';
				$args['post_status'] = 'publish';
				$args['posts_per_page'] = $number;
				$recipes = new WP_Query( $args );
				$counter = 0;
				if( $recipes->have_posts() ){
					while( $recipes->have_posts() ){
						$recipes->the_post();
						if( ( $counter == 3 && $style == 1 ) || ( $counter == 2 && $style == 2 ) ){
							echo '</div><div class="row">';
							$counter = 0;
						}
						$counter++;
						?>
						<div class="col-sm-<?php echo $style == '1' ? esc_attr( '4' ) : esc_attr( '6' ) ?>">
							<?php include( locate_template( 'includes/recipe-box'.( $style == '1' ? '' : '-alt' ).'.php' ) ) ?>
						</div>
						<?php
					}
				}
				wp_reset_postdata();
				?>
			</div>
		</div>
	</section>	
	<?php
	$content = ob_get_contents();
	ob_end_clean();

	return $content;
}

add_shortcode( 'recipes', 'recipe_func' );

function recipe_recipes_params(){
	return array(
		array(
			"type" => "dropdown",
			"holder" => "div",
			"class" => "",
			"heading" => __("Icon","recipe"),
			"param_name" => "icon",
			"value" => recipe_awesome_icons_list(),
			"description" => __("Select icon for the latest recipes.","recipe")
		),
		array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __("Title","recipe"),
			"param_name" => "title",
			"value" => '',
			"description" => __("Input title for the latest recipes block.","recipe")
		),
		array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __("Small Title","recipe"),
			"param_name" => "small_title",
			"value" => '',
			"description" => __("Input title for the small right button.","recipe")
		),
		array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __("Number Of Recipes","recipe"),
			"param_name" => "number",
			"value" => '',
			"description" => __("Input number of the latest recipes to show.","recipe")
		),
		array(
			"type" => "dropdown",
			"holder" => "div",
			"class" => "",
			"heading" => __("Style","recipe"),
			"param_name" => "style",
			"value" => array(
				__( 'Top Media', 'recipe' ) => '1',
				__( 'Side Media', 'recipe' ) => '2'
			),
			"description" => __("Select recipe box style.","recipe")
		),
		array(
			"type" => "dropdown",
			"holder" => "div",
			"class" => "",
			"heading" => __("Recipes Source","recipe"),
			"param_name" => "source",
			"value" => array(
				__( 'Latest', 'recipe' ) => 'latest',
				__( 'Top Rated', 'recipe' ) => 'top-rated',
				__( 'Most Favourited', 'recipe' ) => 'most-favourited',
				__( 'Most Liked', 'recipe' ) => 'most-liked',
				__( 'Most Viewed', 'recipe' ) => 'most-viewed',
			),
			"description" => __("Select recipe source.","recipe")
		),
		array(
			"type" => "multidropdown",
			"holder" => "div",
			"class" => "",
			"heading" => __("Recipe Categories","recipe"),
			"param_name" => "category",
			"value" => 'recipe-category',
			"field" => 'slug',
			"description" => __("Select recipe category.","recipe")
		),
		array(
			"type" => "multidropdown",
			"holder" => "div",
			"class" => "",
			"heading" => __("Recipe Cuisine","recipe"),
			"param_name" => "cuisine",
			"value" => 'recipe-cuisine',
			"field" => 'slug',
			"description" => __("Select recipe cuisine.","recipe")
		),
	);
}

if( function_exists( 'vc_map' ) ){
	vc_map( array(
	   "name" => __("Recipes", 'recipe'),
	   "base" => "recipes",
	   "category" => __('Content', 'recipe'),
	   "params" => recipe_recipes_params()
	) );
}
?>