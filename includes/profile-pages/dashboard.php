<?php

$published_recipes = recipe_count_custom_post( 'recipe', array(
	'author' => $current_user->ID
));

$favourite_recipes = recipe_count_custom_post( 'recipe', array(
	'meta_query' => array(
		array(
			'key' => 'favourite_for',
			'value' => $current_user->ID,
			'compare' => '='
		)
	)
));

$approval_recipes = recipe_count_custom_post( 'recipe', array(
		'author' => $current_user->ID
	),
	'draft' 
);

$update_recipes = recipe_count_custom_post( 'recipe', array(
		'author' => $current_user->ID
	),
	'pending' 
);

?>
<h4 class="no-top-margin"><?php _e( 'My dashboard', 'recipe' ); ?></h4>
<p><?php _e( 'Hi ', 'recipe' ); echo $current_user->display_name; _e( '. Here is a quick overview of your stats.', 'recipe' ); ?></p>
<hr />
<div class="row">
	<div class="col-sm-6">
		<div class="dashboard-item clearfix">
			<div class="pull-left">
				<i class="fa fa-book"></i>
				<?php _e( 'Published Recipes: ', 'recipe' ); ?>
			</div>

			<div class="pull-right badge">
				<?php echo $published_recipes;  ?>
			</div>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="dashboard-item clearfix">
			<div class="pull-left">
				<i class="fa fa-repeat"></i>
				<?php _e( 'Waiting For Update Recipes: ', 'recipe' ); ?>
			</div>

			<div class="pull-right badge">
				<?php echo $update_recipes;  ?>
			</div>
		</div>
	</div>	
</div>
<div class="row">
	<div class="col-sm-6">
		<div class="dashboard-item clearfix">
			<div class="pull-left">
				<i class="fa fa-reply"></i>
				<?php _e( 'Waiting For Approval Recipes: ', 'recipe' ); ?>
			</div>

			<div class="pull-right badge">
				<?php echo $approval_recipes;  ?>
			</div>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="dashboard-item clearfix">
			<div class="pull-left">
				<i class="fa fa-heart"></i>
				<?php _e( 'Favourite Recipes: ', 'recipe' ); ?>
			</div>

			<div class="pull-right badge">
				<?php echo $favourite_recipes;  ?>
			</div>
		</div>
	</div>	
</div>
<div class="row">
	<div class="col-sm-6">
		<div class="dashboard-item clearfix">
			<div class="pull-left">
				<i class="fa fa-star"></i>
				<?php _e( 'Ratings: ', 'recipe' ); ?>
			</div>

			<div class="pull-right">
				<?php echo recipe_user_rating( $current_user->ID );  ?>
			</div>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="dashboard-item clearfix">
			<div class="pull-left">
				<i class="fa fa-cutlery"></i>
				<?php _e( 'Cooking Level: ', 'recipe' ); ?>
			</div>

			<div class="pull-right badge">
				<?php echo recipe_cooking_level( $current_user->ID, $published_recipes );  ?>
			</div>
		</div>
	</div>	
</div>