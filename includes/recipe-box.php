<div class="recipe-box white-block">
	<div class="blog-media">
		<?php
		$image_size = 'box-thumb';
		include( locate_template( 'media/media.php' ) );
		$post_id = get_the_ID();
		?>
		<div class="ratings">
			<?php echo recipe_calculate_ratings(); ?>
		</div>
	</div>
	<div class="content-inner">
		<a href="<?php the_permalink() ?>" class="blog-title">
			<h4><?php the_title() ?></h4>
		</a>

		<?php the_excerpt(); ?>
        <?php
        if(is_page_template('page-tpl_search.php')){
            $related_posts = get_post_meta( get_the_ID(), 'related_posts', true );
        ?><div class="infocorner"><?php
            if( !empty( $related_posts ) ):
            $posts = explode(',', $related_posts);
            if( !empty($posts) ):
            ?>

        <h5 class="widget-title" style="margin: 15px 0">
            <?php _e( 'Herdzeit\'s Infoecke', 'recipe' ) ?>
        </h5>
        <ul class="list-unstyled similar-recipes" >
            <?php
            foreach($posts as $post){
                $related_post_id = trim($post);

                ?>
                <li style="text-align: left;margin-bottom: 15px;">
                    <a href="<?= get_the_permalink($related_post_id) ?>" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: inherit; max-width: 100%">
                        <?= get_the_title($related_post_id); ?>
                    </a>
                </li>
                <?php
            }
            ?>
        </ul>
        <?php endif;
        else:
            ?>
            <h5 class="widget-title" style="margin: 15px 0">
            <?php _e( 'Wusstest du schon?', 'recipe' ) ?>
            </h5>
            <div class="clearfix"></div>
                Du kannst jetzt auch deine eigenen Einkaufslisten auf Herdzeit.de erstellen.
                <?php if(!is_user_logged_in()):?>
                        <span class="col-xs-12 text-center">
                            <a href="/login-register" class="btn btn-block" type="submit" style="margin-top:10px">Zum Login</a>
                        </span>
                <?php else: ?>
                        <span class="col-xs-12 text-center">
                             <a href="/my-account/?page=shopping-list" class="btn btn-block    " type="submit" style="margin-top:10px; word-break:inherit">Einkaufsliste erstellen</a>
                        </span>
        <div class="clearfix"></div>
                <?php endif; ?>
        <?php
        endif;
        //wp_reset_postdata();
        ?></div><?php
        }
        ?>
        <div class="clearfix"></div>
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
	</div>
	<div class="content-footer">
		<div class="content-inner">
			<ul class="list-unstyled list-inline recipe-meta clearfix">
				<li>
					<?php recipe_difficulty_level(); ?>
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