<?php
/*
	Template Name: My Account
*/
if( !is_user_logged_in() ){
	$register_link = recipe_get_permalink_by_tpl( 'page-tpl_register_login' );
	wp_redirect( $register_link );
}
get_header();
the_post();
$current_user = wp_get_current_user();
$permalink = recipe_get_permalink_by_tpl( 'page-tpl_my_account' );
$page = isset( $_GET['page'] ) ? $_GET['page'] : '';
?>

<section>
	<div class="container">
		<div class="row">
			<div class="col-sm-3">
				<div class="white-block">
					<div class="my-sidebar">
						<?php include( locate_template( 'includes/profile-pages/sidebar-avatar.php' ) ) ?>
						<ul class="list-unstyled my-menu">
							<li class="<?php echo empty( $page ) ? 'active' : '' ?>">
								<a href="<?php echo esc_url( $permalink ) ?>">
									<i class="fa fa-dashboard"></i> <?php _e( 'Dashboard', 'recipe' ) ?>
								</a>
							</li>						
							<li class="<?php echo $page == 'my_recipes' || $page == 'edit_recipe' ? esc_attr( 'active' ) : '' ?>">
								<a href="<?php echo esc_url( add_query_arg( array( 'page' => 'my_recipes' ), $permalink ) ) ?>">
									<i class="fa fa-book"></i> <?php _e( 'My Recipes', 'recipe' ) ?>
								</a>
							</li>
							<li class="<?php echo $page == 'my_favourites' ? esc_attr( 'active' ) : '' ?>">
								<a href="<?php echo esc_url( add_query_arg( array( 'page' => 'my_favourites' ), $permalink ) ) ?>">
									<i class="fa fa-heart"></i> <?php _e( 'My Favourite Recipes', 'recipe' ) ?>
								</a>
							</li>
                            <li class="<?php echo $page == 'shopping-list' ? esc_attr( 'active' ) : '' ?>">
                                <a href="<?php echo esc_url( add_query_arg( array( 'page' => 'shopping-list' ), $permalink ) ) ?>">
                                    <i class="fa fa-list"></i> <?php _e( 'Meine Einkaufslisten', 'recipe' ) ?>
                                </a>
                            </li>
							<li class="<?php echo $page == 'edit_profile' ? esc_attr( 'active' ) : '' ?>">
								<a href="<?php echo esc_url( add_query_arg( array( 'page' => 'edit_profile' ), $permalink ) ) ?>">
									<i class="fa fa-cog"></i> <?php _e( 'Edit Profile', 'recipe' ) ?>
								</a>
							</li>
							<li class="<?php echo $page == 'new_recipe' ? esc_attr( 'active' ) : '' ?>">
								<a href="#<?php //echo esc_url( add_query_arg( array( 'page' => 'new_recipe' ), $permalink ) ) ?>" disabled="true">
									<i class="fa fa-plus-circle"></i>
                                    <?php _e( 'Add New Recipe', 'recipe' ) ?>
                                    <img src="<?= get_stylesheet_directory_uri() ?>/images/comming-soon.png" width="45px" />
								</a>
							</li>
						</ul>
					</div>
				</div>
			</div>
			<div class="col-sm-9">
				<div class="white-block">
					<div class="content-inner">
						<?php
						switch( $page ){
							case 'edit_profile': include( locate_template( 'includes/profile-pages/edit-profile.php' ) ); break;
							case 'my_favourites': include( locate_template( 'includes/profile-pages/my-favourites.php' ) ); break;
							case 'new_recipe': include( locate_template( 'includes/profile-pages/new-recipe.php' ) ); break;
							case 'edit_recipe': include( locate_template( 'includes/profile-pages/new-recipe.php' ) ); break;
							case 'my_recipes': include( locate_template( 'includes/profile-pages/recipe-list.php' ) ); break;
							case 'shopping-list': include( locate_template( 'includes/profile-pages/shopping-list.php' ) ); break;
							case 'edit-shopping-list': include( locate_template( 'includes/profile-pages/edit-shopping-list.php' ) ); break;
							case 'new-shopping-list': include( locate_template( 'includes/profile-pages/edit-shopping-list.php' ) ); break;
							case 'edit-ingredient': include( locate_template( 'includes/profile-pages/ingredient-list.php' ) ); break;
							case 'allways-home': include( locate_template( 'includes/profile-pages/allways-home.php' ) ); break;
							default: include( locate_template( 'includes/profile-pages/dashboard.php' ) ); break;
						}
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<?php get_footer(); ?>