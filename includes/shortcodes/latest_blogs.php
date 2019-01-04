<?php
function recipe_latest_blogs_func( $atts, $content ){
	extract( shortcode_atts( array(
		'icon' => '',
		'title' => '',
		'small_title' => '',
		'number' => '3',
		'ignore_sticky_posts' => true
	), $atts ) );

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
				<a href="<?php echo get_permalink( get_option('page_for_posts') ) ?>" class="btn pull-right"><?php echo $small_title ?></a>
			</div>	
			<div class="row">
				<?php
				$blogs = new WP_Query(array(
					'post_type' => 'post',
					'post_status' => 'publish',
					'posts_per_page' => $number,
				));
				$counter = 0;
				if( $blogs->have_posts() ){
					while( $blogs->have_posts() ){
						$blogs->the_post();
						if( $counter == 3 ){
							echo '</div><div class="row">';
							$counter = 0;
						}
						$counter++;
						$post_format = get_post_format();
						$has_media = recipe_has_media();
						?>
						<div class="col-sm-4">
							<div <?php post_class( 'blog-item white-block' ) ?>>
								<?php if( $has_media ): ?>
									<div class="blog-media">
										<?php
										$image_size = 'box-thumb';
										?>
										<?php include( locate_template( 'media/media'.( !empty( $post_format ) ? '-'.$post_format : '' ).'.php' ) ); ?>
									</div>
								<?php endif; ?>
								<div class="content-inner">

									<div class="blog-title-wrap">
										<a href="<?php the_permalink(); ?>" class="blog-title">
											<h4><?php the_title(); ?></h4>
										</a>
										<p class="post-meta clearfix">
											<?php _e( 'By ', 'recipe' ); ?>
											<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>">
												<?php echo get_the_author_meta('display_name') ?>
											</a>
											<?php _e( ' in ', 'recipe' ) ?>
											<?php echo recipe_the_category(); ?>
											<span class="pull-right">
											<?php the_time(get_option( 'date_format' )); ?>
											</span>
										</p>						
									</div>

									<?php the_excerpt() ?>

									<div class="clearfix">
										<a href="<?php the_permalink(); ?>" class="btn pull-left">
											<?php _e( 'Continue reading', 'recipe' ) ?>
										</a>
									</div>

								</div>							
							</div>
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

add_shortcode( 'latest_blogs', 'recipe_latest_blogs_func' );

function recipe_latest_blogs_params(){
	return array(
		array(
			"type" => "dropdown",
			"holder" => "div",
			"class" => "",
			"heading" => __("Icon","recipe"),
			"param_name" => "icon",
			"value" => recipe_awesome_icons_list(),
			"description" => __("Select icon for the latest blogs.","recipe")
		),
		array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __("Title","recipe"),
			"param_name" => "title",
			"value" => '',
			"description" => __("Input title for the latest blogs block.","recipe")
		),
		array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __("Small Title","recipe"),
			"param_name" => "small_title",
			"value" => '',
			"description" => __("Input title for the button to all latest blogs.","recipe")
		),
		array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __("Number Of Blogs","recipe"),
			"param_name" => "number",
			"value" => '',
			"description" => __("Input number of the latest blogs to show.","recipe")
		),		
	);
}

if( function_exists( 'vc_map' ) ){
	vc_map( array(
	   "name" => __("Latest Blogs", 'recipe'),
	   "base" => "recipe_latest_blogs_func",
	   "category" => __('Content', 'recipe'),
	   "params" => recipe_latest_blogs_params()
	) );
}
?>