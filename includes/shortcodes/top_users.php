<?php
function recipe_top_users_func( $atts, $content ){
	extract( shortcode_atts( array(
		'icon' => '',
		'title' => '',
		'small_title' => '',
		'number' => '3',
	), $atts ) );
	$permalink = recipe_get_permalink_by_tpl( 'page-tpl_members' );
	ob_start();
	?>
	<section>
		<div class="container">
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
			<div class="row">
				<?php
				$top_users = new WP_User_Query(array(
					'orderby' => 'meta_value',
					'order' => 'DESC',
					'number' => $number,	
					'meta_query' => array( 
						array( 
							'key' => 'average_rating', 
							'type' => 'UNSIGNED' 
						) 
					)
				));
				if( !empty( $top_users->results ) ){
					foreach( $top_users->results as $user ){
						?>
						<div class="col-sm-2 col-xs-4">
							<div class="user-block">
								<?php $avatar_url = recipe_get_avatar_url( get_avatar( $user->ID, 150 ) ); ?>
								<a href="<?php echo esc_url( get_author_posts_url( $user->ID ) ) ?>" data-title="<?php echo esc_attr( $user->display_name ) ?>" class="tip">
									<img src="<?php echo esc_url( $avatar_url ) ?>" class="img-responsive" alt="author" width="150" height="150"/>
									<span class="user-block-overlay animation">
										<i class="fa fa-plus-circle animation"></i>
									</span>								
								</a>
							</div>
						</div>
						<?php
					}
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

add_shortcode( 'top_users', 'recipe_top_users_func' );

function recipe_top_users_params(){
	return array(
		array(
			"type" => "dropdown",
			"holder" => "div",
			"class" => "",
			"heading" => __("Icon","recipe"),
			"param_name" => "icon",
			"value" => recipe_awesome_icons_list(),
			"description" => __("Select icon for the top users.","recipe")
		),
		array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __("Title","recipe"),
			"param_name" => "title",
			"value" => '',
			"description" => __("Input title for the top users block.","recipe")
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
			"heading" => __("Number Of Users","recipe"),
			"param_name" => "number",
			"value" => '',
			"description" => __("Input number of the top users to show.","recipe")
		),		
	);
}

if( function_exists( 'vc_map' ) ){
	vc_map( array(
	   "name" => __("Top Rated Users", 'recipe'),
	   "base" => "top_users",
	   "category" => __('Content', 'recipe'),
	   "params" => recipe_top_users_params()
	) );
}
?>