<?php
/*
	Template Name: Search Page
*/
get_header();
the_post();
global $recipe_slugs;

$recipe_category = isset( $_GET[$recipe_slugs['recipe-category']] ) ? esc_sql( $_GET[$recipe_slugs['recipe-category']] ) : '';
$recipe_cuisine = isset( $_GET[$recipe_slugs['recipe-cuisine']] ) ? esc_sql( $_GET[$recipe_slugs['recipe-cuisine']] ) : '';
$recipe_tag = isset( $_GET[$recipe_slugs['recipe-tag']] ) ? esc_sql( $_GET[$recipe_slugs['recipe-tag']] ) : '';
$recipe_ingredients = isset( $_GET[$recipe_slugs['ingredients']] ) ? esc_sql( $_GET[$recipe_slugs['ingredients']] ) : '';
$keyword = isset( $_GET[$recipe_slugs['keyword']] ) ? esc_sql( $_GET[$recipe_slugs['keyword']] ) : '';
$sort = isset( $_GET[$recipe_slugs['sort']] ) ? esc_sql( $_GET[$recipe_slugs['sort']] ) : '';

$args = array(
	'post_type' => 'recipe',
	'post_status' => 'publish',
	'tax_query' => array(
		'relation' => 'AND'
	),
	'meta_query' => array(
		'relation' => 'OR'
	)
);

if( !empty( $recipe_category ) ){
	$args['tax_query'][] = array(
		'taxonomy' => 'recipe-category',
		'field'    => 'slug',
		'terms'    => $recipe_category,
	);
}

if( !empty( $recipe_cuisine ) ){
	$args['tax_query'][] = array(
		'taxonomy' => 'recipe-cuisine',
		'field'    => 'slug',
		'terms'    => $recipe_cuisine,
	);
}

if( !empty( $recipe_tag ) ){
	$args['tax_query'][] = array(
		'taxonomy' => 'recipe-tag',
		'field'    => 'slug',
		'terms'    => $recipe_tag,
	);
}

if( !empty( $recipe_ingredients ) ){
	$ingredients = explode( " ", $recipe_ingredients );
	foreach( $ingredients as $ingredient ){
		$args['meta_query'][] = array(
			'key' => 'recipe_ingredient',
			'value' => $ingredient,
			'compare' => 'LIKE'
		);
	}
}

if( !empty( $keyword ) ){
	$args['s'] = $keyword;
}

if( !empty( $sort ) ){
	$sort_array = explode( "-", $sort );
	$args['order'] = $sort_array[1];
	if( $sort_array[0] == 'title' || $sort_array[0] == 'date' ){
		$args['orderby'] = $sort_array[0];
	}
	else{
		$args['orderby'] = 'meta_value_num';
		$args['meta_key'] = $sort_array[0];
		if( $sort_array[0] == 'ratings' ){	
			$args['meta_key'] = 'average_review';
		}
		$args['meta_query'][] = array(
			'relation' => 'OR',
			array(
				'key' => $args['meta_key'],
				'compare' => 'EXISTS'
			),
			array(
				'key' => $args['meta_key'],
				'compare' => 'NOT EXISTS'
			)			
		);
	}
}
$cur_page = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1; //get curent page
if( is_front_page() ){
	$cur_page = ( get_query_var( 'page' ) ) ? get_query_var( 'page' ) : 1;
}
$args['paged'] = $cur_page;
$args['posts_per_page'] = 8;
$recipes = new WP_Query( $args );
$page_links_total =  $recipes->max_num_pages;
$pagination = paginate_links( 
	array(
		'prev_next' => true,
		'end_size' => 2,
		'mid_size' => 2,
		'total' => $page_links_total,
		'current' => $cur_page,	
		'prev_next' => false
	)
);	

?>

<section class="search-filter">
	<div class="container">
		<form method="get" action="<?php echo esc_url( recipe_get_permalink_by_tpl( 'page-tpl_search' ) ); ?>">
			<div class="white-block">
				<div class="content-inner">
					<div class="row">
						<div class="col-sm-4">
				            <div class="form-group">
				                <label for="recipe-category"><?php _e( 'Recipe Category', 'recipe' );?> </label>
				                <select name="<?php echo esc_attr( $recipe_slugs['recipe-category'] ) ?>" id="recipe-category" class="form-control">
									<option value=""><?php _e( '- Select -', 'recipe' ) ?></option>
						            <?php
						            $categories = recipe_get_organized( 'recipe-category', false );
						            if( !empty( $categories ) ){
						                foreach( $categories as $category ){
						                    recipe_display_select_tree( $category, $recipe_category, 0, false, 'slug' );
						                }
						            }
						            ?>
				                </select>
				            </div>
						</div>
						<div class="col-sm-4">
				            <div class="form-group">
				                <label for="recipe-cuisine"><?php _e( 'Recipe Cuisine', 'recipe' );?> </label>
				                <select name="<?php echo esc_attr( $recipe_slugs['recipe-cuisine'] ); ?>" id="recipe-cuisine" class="form-control">
				                	<option value=""><?php _e( '- Select -', 'recipe' ) ?></option>
						            <?php
						            $cuisines = recipe_get_organized( 'recipe-cuisine', false );
						            if( !empty( $cuisines ) ){
						                foreach( $cuisines as $cuisine ){
						                    recipe_display_select_tree( $cuisine, $recipe_cuisine, 0, false, 'slug' );
						                }
						            }
						            ?>
				                </select>
				            </div>
						</div>
						<div class="col-sm-4">
				            <div class="form-group">
				                <label for="sort"><?php _e( 'Sort Recipes', 'recipe' );?> </label>
				                <select name="<?php echo esc_attr( $recipe_slugs['sort'] ); ?>" id="sort" class="form-control">
				                	<option value=""><?php _e( '- Select -', 'recipe' ) ?></option>
				                	<option value="ratings-desc" <?php echo $sort == 'ratings-desc' ? esc_attr( 'selected="selected"' ) : '' ?>><?php _e( 'Ratings (highest first)', 'recipe' ) ?></option>
				                	<option value="ratings-asc" <?php echo $sort == 'ratings-asc' ? esc_attr( 'selected="selected"' ) : '' ?>><?php _e( 'Ratings (lowest first)', 'recipe' ) ?></option>
				                	<option value="date-desc" <?php echo $sort == 'date-desc' ? esc_attr( 'selected="selected"' ) : '' ?>><?php _e( 'Date (newest first)', 'recipe' ) ?></option>
				                	<option value="date-asc" <?php echo $sort == 'date-asc' ? esc_attr( 'selected="selected"' ) : '' ?>><?php _e( 'Date (oldest first)', 'recipe' ) ?></option>
				                	<option value="title-desc" <?php echo $sort == 'title-desc' ? esc_attr( 'selected="selected"' ) : '' ?>><?php _e( 'Title (descending)', 'recipe' ) ?></option>
				                	<option value="title-asc" <?php echo $sort == 'title-asc' ? esc_attr( 'selected="selected"' ) : '' ?>><?php _e( 'Title (ascending)', 'recipe' ) ?></option>
				                	<option value="likes-desc" <?php echo $sort == 'likes-desc' ? esc_attr( 'selected="selected"' ) : '' ?>><?php _e( 'Likes (most liked first)', 'recipe' ) ?></option>
				                	<option value="likes-asc" <?php echo $sort == 'likes-asc' ? esc_attr( 'selected="selected"' ) : '' ?>><?php _e( 'Likes (most liked last)', 'recipe' ) ?></option>
				                	<option value="views-desc" <?php echo $sort == 'views-desc' ? esc_attr( 'selected="selected"' ) : '' ?>><?php _e( 'Views (most viewed first)', 'recipe' ) ?></option>
				                	<option value="views-asc" <?php echo $sort == 'views-asc' ? esc_attr( 'selected="selected"' ) : '' ?>><?php _e( 'Views (most viewed last)', 'recipe' ) ?></option>
				                	<option value="favourited-desc" <?php echo $sort == 'favourited-desc' ? esc_attr( 'selected="selected"' ) : '' ?>><?php _e( 'Favourites (most favourited first)', 'recipe' ) ?></option>
				                	<option value="favourited-asc" <?php echo $sort == 'favourited-asc' ? esc_attr( 'selected="selected"' ) : '' ?>><?php _e( 'Favourites (most favourited last)', 'recipe' ) ?></option>				                	
				                </select>
				            </div>
						</div>							
					</div>
					<div class="row">
						<div class="col-sm-4">
				            <div class="form-group">
				                <label for="ingredients"><?php _e( 'Ingredients', 'recipe' );?> </label>
				                <input type="text" name="<?php echo esc_attr( $recipe_slugs['ingredients'] ); ?>" id="ingredients" class="form-control" value="<?php echo esc_attr( $recipe_ingredients ) ?>" />
				            </div>
						</div>
						<div class="col-sm-4">
				            <div class="form-group">
				                <label for="keyword"><?php _e( 'Keyword', 'recipe' );?> </label>
				                <input type="text" name="<?php echo esc_attr( $recipe_slugs['keyword'] ); ?>" id="keyword" class="form-control" value="<?php echo esc_attr( $keyword ) ?>" />
				            </div>
						</div>
						<div class="col-sm-4">
							<label>&nbsp;</label>
							<button type="submit" class="hidden"></button>
							<a href="javascript:;" class="btn submit-live-form"><?php _e( 'Search For Recipes', 'recipe' ) ?></a>	
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</section>

<?php if( $recipes->have_posts() ): ?>
	<section>
		<div class="container">
			<div class="row">
				<?php
				$counter = 0;
				$counterAll = 1;
				while( $recipes->have_posts() ){
					$recipes->the_post();
					if( $counter == 3 ){
						echo '</div><div class="row">';
						$counter = 0;
					}
					$counter++;

					    if($counterAll == 5){
                            echo '<div class="col-sm-4">';
                            include( locate_template( 'includes/recipe-ads-box.php' ) );
                            echo '</div>';
                            $counter++;

                        }
                            echo '<div class="col-sm-4">';
                            include( locate_template( 'includes/recipe-box.php' ) );
					        echo '</div>';
                    $counterAll++;
				}
				?>
			</div>
			<?php if( !empty( $pagination ) ): ?>
				<div class="pagination">
					<?php echo $pagination; ?>
				</div>	
			<?php endif; ?>
		</div>
	</section>
<?php else: ?>
	<section>
		<div class="container">
			<div class="white-block">
				<div class="content-inner">
					<?php esc_html_e( 'No recipes found matching your criteria', 'recipe' ) ?>
				</div>
			</div>
		</div>
	</section>
<?php endif; ?>
<?php get_footer(); ?>