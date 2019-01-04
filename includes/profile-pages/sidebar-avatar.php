<?php
if( is_author() ){
	$user = get_user_by( 'slug', get_query_var( 'author_name' ) );
	$user_id = $user->ID;
	$nice_name = !empty( $user->display_name ) ? $user->display_name : $user->user_login;
}
else{
	$user_id = $current_user->ID;
	$nice_name = !empty( $current_user->display_name ) ? $current_user->display_name : $current_user->user_login;
}
$facebook = get_user_meta ($user_id, 'facebook', true );
$twitter = get_user_meta ($user_id, 'twitter', true );
$google = get_user_meta ($user_id, 'google', true );
$linkedin = get_user_meta ($user_id, 'linkedin', true );
$instagram = get_user_meta ($user_id, 'instagram', true );
$cover = get_user_meta ($user_id, 'cover', true );
$cover_data = wp_get_attachment_image_src( $cover, 'full' );
$user = get_user_by( 'slug', get_query_var( 'author_name' ) );
?>
<div class="my-avatar <?php echo !empty( $cover_data ) ? 'has-cover' : '' ?>" style="background: url( '<?php echo !empty( $cover_data ) ? $cover_data[0] : '' ?>' );">
	<?php
	$avatar_url = recipe_get_avatar_url( get_avatar( $user_id, 150 ) );
	if( !empty( $avatar_url ) ):
	?>
		<img src="<?php echo esc_url( $avatar_url ) ?>" class="img-responsive" alt="author"/>
	<?php
	endif;
	?>
	<h4><?php echo $nice_name; ?></h4>

	<ul class="list-unstyled list-inline post-share">
		<?php if( !empty( $facebook ) ): ?>
			<li>
				<a href="<?php echo esc_url( $facebook ) ?>" class="share facebook" target="_blank">
					<i class="fa fa-facebook"></i>
				</a>
			</li>
		<?php endif; ?>
		<?php if( !empty( $twitter ) ): ?>
			<li>
				<a href="<?php echo esc_url( $twitter ) ?>" class="share twitter" target="_blank">
					<i class="fa fa-twitter"></i>
				</a>
			</li>
		<?php endif; ?>
		<?php if( !empty( $google ) ): ?>
			<li>
				<a href="<?php echo esc_url( $google ) ?>" class="share google" target="_blank">
					<i class="fa fa-google-plus"></i>
				</a>
			</li>
		<?php endif; ?>
		<?php if( !empty( $linkedin ) ): ?>
			<li>
				<a href="<?php echo esc_url( $linkedin ) ?>" class="share linkedin" target="_blank">
					<i class="fa fa-linkedin"></i>
				</a>
			</li>
		<?php endif; ?>
		<?php if( !empty( $instagram ) ): ?>
			<li>
				<a href="<?php echo esc_url( $instagram ) ?>" class="share instagram" target="_blank">
					<i class="fa fa-instagram"></i>
				</a>
			</li>
		<?php endif; ?>
	</ul>							
</div>