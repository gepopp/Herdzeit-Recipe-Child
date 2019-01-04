<?php
$recipes = new WP_Query(array(
	'post_type' => 'recipe',
	'post_status' => 'publish',
	'posts_per_page' => -1,
	'meta_query' => array(
		array(
			'key' => 'favourite_for',
			'value' => $current_user->ID,
			'compare' => '='
		)
	)
));

?>
<h4 class="no-top-margin"><?php _e( 'My favourite recipes', 'recipe' ); ?></h4>
<p><?php _e( 'Here you will find all your favourite recipes', 'recipe' ) ?></p>
<hr />
<p class="pretable-loading"><?php _e( 'Loading...', 'recipe' ) ?></p>
<div class="bt-table">
	<table data-toggle="table" data-search-align="left" data-search="true" data-classes="table table-striped">
		<thead>
		    <tr>
		        <th data-field="image">
		        	<?php _e( 'Image', 'recipe' ); ?>
		        </th>
		        <th data-field="name" data-sortable="true">
		            <?php _e( 'Name', 'recipe' ); ?>
		        </th>
		        <th data-field="status" data-sortable="true">
		            <?php _e( 'Status', 'recipe' ); ?>
		        </th>
		        <th data-field="ratings">
		            <?php _e( 'Ratings', 'recipe' ); ?>
		        </th>		        
		        <th data-field="views" data-sortable="true">
		            <?php _e( 'Views', 'recipe' ); ?>
		        </th>
		        <th data-field="action" data-sortable="true">
		            <?php _e( 'Action', 'recipe' ); ?>
		        </th>	        
		    </tr>
		</thead>
		<?php
		if( $recipes->have_posts() ){
			?>
			<tbody>
			<?php
			while( $recipes->have_posts() ){
				$recipes->the_post();
				?>
				<tr>
					<td>
						<?php
						if( has_post_thumbnail() ){
							the_post_thumbnail( 'thumbnail' );
						}
						?>
					</td>
					<td>
						<a href="<?php the_permalink(); ?>" target="_blank">
							<?php the_title(); ?>
						</a>
					</td>
					<td>
						<?php
						$recipe_status = get_post_status();
						switch( $recipe_status ){
							case 'draft': _e( 'Pending Review', 'recipe' ); break;
							case 'publish': 
								$children = get_children(array(
									'post_type' => 'recipe',
									'post_parent' => get_the_ID()
								));
								if( count( $children ) == 0 ){
									_e( 'Published', 'recipe' ); break;
								}
								else{
									_e( 'Update Review', 'recipe' ); break;	
								}
						}
						?>
					</td>
					<td>
						<div class="td-ratings">
							<?php echo recipe_calculate_ratings(); ?>
						</div>
					</td>
					<td>
						<?php
						$views = get_post_meta( get_the_ID(), 'views', true );
						if( empty( $views ) ){
							echo '0';
						}
						else{
							echo $views;
						}
						?>
					</td>					
					<td class="action">
						<a href="javascript:;" class="recipe-favourite" data-recipe_id="<?php the_ID(); ?>">
							<i class="fa fa-times"></i>
						</a>						
					</td>
				</tr>
				<?php
			}
			?>
			</tbody>
			<?php		
		}
		wp_reset_query();
		?>
	</table>
</div>