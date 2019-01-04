<div class="col-md-3">
	<div class="widget white-block clearfix">
		<ul class="list-unstyled single-nutritions">
			<li class="recipe-avatar">
				<?php
				$avatar_url = recipe_get_avatar_url( get_avatar( get_the_author_meta('ID'), 150 ) );
				if( !empty( $avatar_url ) ):
				?>
					<img src="<?php echo esc_url( $avatar_url ) ?>" class="img-responsive" alt="author"/>
				<?php
				endif;
				?>						
				<?php _e( 'By ', 'recipe' ) ?><a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>"> <?php echo get_the_author_meta('display_name'); ?></a>
			</li>

			<?php 
			$recipe_prep_time = recipe_parse_time( $recipe_prep_time );
			if( !empty( $recipe_prep_time ) ):
			?>
				<li>
					<?php _e( 'Preparation Time:', 'recipe' ) ?> <span class="value"><?php echo $recipe_prep_time ?></span>
				</li>
			<?php endif; ?>

			<?php 
			$recipe_cook_time = recipe_parse_time( $recipe_cook_time );
			if( !empty( $recipe_cook_time ) ):
			?>			
			<li>
				<?php _e( 'Cook Time:', 'recipe' ) ?> <span class="value"><?php echo $recipe_cook_time; ?></span>
			</li>
			<?php endif; ?>

			<?php 
			$recipe_ready_in = recipe_parse_time( $recipe_ready_in );
			if( !empty( $recipe_ready_in ) ):
			?>
			<li>
				<?php _e( 'Ready Time:', 'recipe' ) ?> <span class="value"><?php echo $recipe_ready_in; ?></span>
			</li>
			<?php endif; ?>

			<?php
			$recipe_yield = get_post_meta( get_the_ID(), 'recipe_yield', true );
			if( !empty( $recipe_yield ) ):
			?>
			<li>
				<?php _e( 'Yield:', 'recipe' ) ?> <span class="value"><?php echo $recipe_yield;?></span>
			</li>
			<?php endif; ?>

			<?php
			$recipe_servings = get_post_meta( get_the_ID(), 'recipe_servings', true );
			if( !empty( $recipe_servings ) ):
			?>
			<li>
				<?php _e( 'Servings:', 'recipe' ) ?> <span class="value"><?php echo $recipe_servings;?></span>
			</li>
			<?php endif; ?>

			<?php
			$recipe_cuisines = get_the_terms( get_the_ID(), 'recipe-cuisine' );
			if( !empty( $recipe_cuisines ) ):
			?>
				<li class="flex-list">
					<?php
				    $cuisines_list = array();
			    	foreach( $recipe_cuisines as $recipe_cuisine ){
			    		if( $recipe_cuisine->parent == 0 ){
			    			$cuisines_list[] = '<a href="'.esc_url( add_query_arg( array( $recipe_slugs['recipe-cuisine'] => $recipe_cuisine->slug ), $permalink ) ) .'">'.$recipe_cuisine->name.'</a>';
			    		}
			    	}
					?>
					<?php _e( 'Cuisine:', 'recipe' ) ?> 
					<span class="value">
						<?php echo join( '', $cuisines_list ); ?>
					</span>
				</li>
			<?php endif; ?>

			<?php
			$recipe_categories = get_the_terms( get_the_ID(), 'recipe-category' );
			if( !empty( $recipe_categories ) ):
			?>
				<li class="flex-list">
					<?php
				    $categories_list = array();
			    	foreach( $recipe_categories as $recipe_category ){
			    		if( $recipe_category->parent == 0 ){
			    			$categories_list[] = '<a href="'.esc_url( add_query_arg( array( $recipe_slugs['recipe-category'] => $recipe_category->slug ), $permalink ) ) .'">'.$recipe_category->name.'</a>';
			    		}
			    	}
					?>
					<?php _e( 'Category:', 'recipe' ) ?>
					<span class="value">
						<?php echo join( '', $categories_list ); ?>
					</span>
				</li>
			<?php endif; ?>

			<li>
				<?php _e( 'Difficulty Level:', 'recipe' ) ?>
				<span class="value"><?php recipe_difficulty_level() ?></span>
			</li>

			<li>
				<?php _e( 'Ratings:', 'recipe' ) ?>
				<span class="value"><?php echo recipe_calculate_ratings(); ?></span>
			</li>

			<li>
				<?php _e( 'Created:', 'recipe' ) ?>
				<span class="value"><?php the_time(get_option( 'date_format' )) ?></span>
			</li>						
		</ul>
        <hr>
        <ul class="list-unstyled recipe-actions list-inline">
            <li class="tip animation" data-title="<?php echo esc_attr( recipe_get_post_extra( 'likes' ) );?>">
                <a href="javascript:;" class="post-like" data-post_id="<?php the_ID(); ?>">
                    <i class="fa fa-thumbs-o-up fa-fw"></i>
                </a>
            </li>
            <li class="tip animation" data-title="<?php echo recipe_get_post_extra( 'views' ); ?>">
                <i class="fa fa-eye fa-fw"></i>
            </li>
            <?php if(get_option('users_can_register')): ?>
                <?php
                $favourited = get_post_meta( get_the_ID(), 'favourited', true );
                if( empty( $favourited ) ){
                    $favourited = 0;
                }
                ?>
                <li class="tip animation" data-title="<?php echo esc_attr( $favourited );?>">
                    <a href="javascript:;" class="recipe-favourite" data-recipe_id="<?php the_ID(); ?>">
                        <?php if( recipe_is_user_liked() ): ?>
                            <i class="fa fa-heart fa-fw"></i>
                        <?php else: ?>
                            <i class="fa fa-heart-o fa-fw"></i>
                        <?php endif; ?>
                    </a>
                </li>
            <?php endif; ?>
            <li class="tip animation" data-title="<?php esc_attr_e( 'Print recipe', 'recipe' ) ?>">
                <a href="javascript:;" class="print-recipe" data-confirm="<?php esc_attr_e( 'Print Image Also?', 'recipe' ) ?>">
                    <i class="fa fa-print fa-fw"></i>
                </a>
            </li>

        </ul>
	</div>

	<?php
	$recipe_nutritions = get_post_meta( get_the_ID(), 'recipe_nutritions', true );
	if( !empty( $recipe_nutritions ) ):
	?>
		<div class="widget white-block clearfix">
			<div class="widget-title-wrap">
				<h5 class="widget-title">
					<?php _e( 'Nutrition Facts', 'recipe' ) ?>
				</h5>
			</div>
			<ul class="list-unstyled single-nutritions">
				<?php
				$recipe_nutritions = explode( "\n", $recipe_nutritions );
				foreach( $recipe_nutritions as $nutrition ){
					$temp = explode( ":", $nutrition );
					echo '<li>'.$temp[0].':<span class="value">'.$temp[1].'</span></li>';
				}
				?>
			</ul>

		</div>
	<?php
	endif;
	?>
    <div class="widget white-block clearfix" id="nl-form">
        <div class="widget-title-wrap">
            <h5 class="widget-title">
                <?php _e( 'Newsletter', 'recipe' ) ?>
            </h5>
        </div>
        <form id="subForm" class="js-cm-form" action="https://www.createsend.com/t/subscribeerror?description=" method="post" data-id="30FEA77E7D0A9B8D7616376B90063231C5792E80AE964688F9498716ADBAE2C6B17D14328577DFB59E3910E1DB7682A18AAE8395E724FC9BAF10C292766C89E1">

            <p>
                <label for="fieldName">Name</label><br />
                <input id="fieldName" name="cm-name" type="text" class="form-control"/>
            </p>
            <p>
                <label for="fieldEmail">E-Mail</label><br />
                <input id="fieldEmail" class="js-cm-email-input form-control" name="cm-cjdilt-cjdilt" type="email" required />
            </p>
            <p>
                <div class="checkbox">
                	<label>
                		<input type="checkbox" value="" id="agb">
                		Ich akzeptiere die Bedingugnen unserer <a href="/datenschutzerklaerung">Datenschutzerklärung</a> .
                	</label>
                </div>
            </p>
            <p>
                <button class="js-cm-submit-button btn btn-block" type="submit">senden</button>
            </p>
        </form>
        <script type="text/javascript" src="https://js.createsend1.com/javascript/copypastesubscribeformlogic.js"></script>
    </div>
	<?php
	$related_posts = get_post_meta( get_the_ID(), 'related_posts', true );
	if( !empty( $related_posts ) ):
		$posts = explode(',', $related_posts);
		if( !empty($posts) ):
		?>
			<div class="widget white-block clearfix hidden-xs">
				<div class="widget-title-wrap">
					<h5 class="widget-title">
						<?php _e( 'Herdzeit\'s Infoecke', 'recipe' ) ?>
					</h5>
				</div>
				<ul class="list-unstyled similar-recipes">
					<?php
					foreach($posts as $post){
						$post_id = trim($post);
						?>
						<li>
							<a href="<?php the_permalink() ?>" class="no-margin">
								<div class="embed-responsive embed-responsive-16by9">
									<?= get_the_post_thumbnail( $post_id, 'post-thumbnail', array( 'class' => 'embed-responsive-item' ) ); ?>
								</div>
							</a>
							<a href="<?= get_the_permalink($post_id) ?>">
								<?= get_the_title($post_id); ?>
							</a>									
						</li>
						<?php
					}
					?>
				</ul>
			</div>
		<?php endif; 
	endif;
	wp_reset_postdata();
	?>
    <?php
    $similar_recipes_num = recipe_get_option( 'similar_recipes_num' );
    if( !empty( $similar_recipes_num ) && $similar_recipes_num > 0 && !empty( $recipe_category ) ):
        $similar = new WP_Query(array(
            'post_type' => 'recipe',
            'posts_per_page' => $similar_recipes_num,
            'post__not_in' => array( get_the_ID() ),
            'post_status' => 'publish',
            'tax_query' => array(
                array(
                    'taxonomy' => 'recipe-category',
                    'field' => 'slug',
                    'terms' => array( $recipe_category->slug )
                )
            )
        ));
        if( $similar->have_posts() ):
            ?>
            <div class="widget white-block clearfix">
                <div class="widget-title-wrap">
                    <h5 class="widget-title">
                        <?php _e( 'Similar Recipes', 'recipe' ) ?>
                    </h5>
                </div>
                <ul class="list-unstyled similar-recipes">
                    <?php
                    while( $similar->have_posts() ){
                        $similar->the_post();
                        ?>
                        <li>
                            <a href="<?php the_permalink() ?>" class="no-margin">
                                <div class="embed-responsive embed-responsive-16by9">
                                    <?php the_post_thumbnail( 'post-thumbnail', array( 'class' => 'embed-responsive-item' ) ); ?>
                                </div>
                            </a>
                            <a href="<?php the_permalink() ?>">
                                <?php the_title(); ?>
                            </a>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
            </div>
        <?php endif;
    endif;
    wp_reset_postdata();
    ?>

	<?php get_sidebar( 'recipe' ); ?>
</div>