<div class="recipe-box recipe-box-alt white-block">
	<div class="row">
		<div class="col-sm-6">
			<div class="blog-media">
				<?php
				$image_size = 'box-thumb-alt';
				include( locate_template( 'media/media.php' ) );
				$post_id = get_the_ID();
				?>
				<div class="ratings">
					<?php echo recipe_calculate_ratings(); ?>
				</div>
			</div>		
		</div>
		<div class="col-sm-6">
			<div class="content-inner content-inner-alt">
				<a href="<?php the_permalink() ?>" class="blog-title">
					<h5><?php the_title() ?></h5>
				</a>

				<?php
				$excerpt = get_the_excerpt();
				if( strlen( $excerpt ) > 62 ){
					$excerpt = substr( $excerpt, 0, 62 );
					$excerpt .= '...';
				}
				echo apply_filters( 'the_excerpt', $excerpt );
				?>
			</div>		
		</div>
	</div>
	<div class="content-footer">
		<div class="content-inner">
			<ul class="list-unstyled list-inline recipe-meta clearfix">
				<li>
					<?php recipe_difficulty_level(); ?>
				</li>
				<li>
					<div class="avatar">
						<?php
						$avatar_url = recipe_get_avatar_url( get_avatar( get_the_author_meta('ID'), 25 ) );
						if( !empty( $avatar_url ) ):
						?>
							<img src="<?php echo esc_url( $avatar_url ) ?>" alt="author" width="25" height="25"/>
						<?php endif; ?>	

						<?php _e( 'By ', 'recipe' ); ?>
						<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) ?>">
							<?php
							$display_name = get_the_author_meta( 'display_name' );
							if( empty( $display_name ) ){
								$display_name = get_the_author_meta( 'login' );
							}

							echo $display_name;
							?>						
						</a>
					</div>				
				</li>
				<?php
				$recipe_yield = get_post_meta( $post_id, 'recipe_yield', true );
				if( !empty( $recipe_yield ) ):
				?>
					<li class="tip" data-title="<?php esc_attr_e( 'Yield', 'recipe' ) ?>">
						<i class="fa fa-table"></i>
						<?php echo $recipe_yield; ?>
					</li>
				<?php endif; ?>

				<?php
				$recipe_servings = get_post_meta( $post_id, 'recipe_servings', true );
				if( !empty( $recipe_servings ) ):
				?>
					<li class="tip" data-title="<?php esc_attr_e( 'Servings', 'recipe' ) ?>">
						<i class="fa fa-users"></i>
						<?php echo $recipe_servings; ?>
					</li>
				<?php endif; ?>

				<?php
				$recipe_cook_time = get_post_meta( $post_id, 'recipe_cook_time', true );
				$recipe_cook_time = recipe_parse_time( $recipe_cook_time );
				if( !empty( $recipe_cook_time ) ):
				?>
					<li class="tip" data-title="<?php esc_attr_e( 'Cook Time', 'recipe' ) ?>">
						<i class="fa fa-clock-o"></i>
						<?php echo $recipe_cook_time; ?>
					</li>
				<?php endif; ?>
			</ul>
		</div>
	</div>
</div>