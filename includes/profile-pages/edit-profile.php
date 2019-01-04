<?php

$first_name = $current_user->user_firstname;
$last_name = $current_user->user_lastname;
$email = $current_user->user_email;
$description = get_user_meta ($current_user->ID, 'description', true );
$facebook = get_user_meta ($current_user->ID, 'facebook', true );
$twitter = get_user_meta ($current_user->ID, 'twitter', true );
$google = get_user_meta ($current_user->ID, 'google', true );
$linkedin = get_user_meta ($current_user->ID, 'linkedin', true );
$instagram = get_user_meta ($current_user->ID, 'instagram', true );
$cover = get_user_meta ($current_user->ID, 'cover', true );

?>
<h4 class="no-top-margin"><?php _e( 'Edit Profile', 'recipe' ); ?></h4>
<p><?php _e( 'Edit your personal information here', 'recipe' ) ?></p>
<hr />
<form method="post">
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="avatar"><?php _e( 'Avatar', 'recipe' );?> </label>
                <input type="hidden" name="wp-user-avatar" id="avatar" value="">
                <a href="javascript:;" class="image-upload user-avatar"><?php _e( 'Change Avatar', 'recipe' ); ?></a>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="email"><?php _e( 'Email *', 'recipe' ); ?></label>
                <input type="text" name="email" id="email" value="<?php echo esc_attr( $email ) ?>" class="form-control">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="first_name"><?php _e( 'First Name', 'recipe' ); ?></label>
                <input type="text" name="first_name" id="first_name" value="<?php echo esc_attr( $first_name ) ?>" class="form-control">
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="last_name"><?php _e( 'Last Name', 'recipe' ); ?></label>
                <input type="text" name="last_name" id="last_name" value="<?php echo esc_attr( $last_name ) ?>" class="form-control">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="password"><?php _e( 'Password', 'recipe' ); ?> </label>
                <input type="password" name="password" id="password" value="" class="form-control">
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="repeat_password"><?php _e( 'Repeat Password', 'recipe' ); ?></label>
                <input type="password" name="repeat_password" id="repeat_password" value="" class="form-control">
            </div>
        </div>
    </div>   
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="facebook"><?php _e( 'Facebook Link', 'recipe' ); ?> </label>
                <input type="text" name="facebook" id="facebook" value="<?php echo esc_attr( $facebook ) ?>" class="form-control">
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="twitter"><?php _e( 'Twitter Link', 'recipe' ); ?></label>
                <input type="text" name="twitter" id="twitter" value="<?php echo esc_attr( $twitter ) ?>" class="form-control">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="google"><?php _e( 'Google+ Link', 'recipe' ); ?> </label>
                <input type="text" name="google" id="google" value="<?php echo esc_attr( $google ) ?>" class="form-control">
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="linkedin"><?php _e( 'Linkedin Link', 'recipe' ); ?></label>
                <input type="text" name="linkedin" id="linkedin" value="<?php echo esc_attr( $linkedin ) ?>" class="form-control">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="instagram"><?php _e( 'Instagram Link', 'recipe' ); ?></label>
                <input type="text" name="instagram" id="instagram" value="<?php echo esc_attr( $instagram ) ?>" class="form-control">
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="cover"><?php _e( 'Cover Image', 'recipe' ); ?> </label>
                <input type="hidden" name="cover" id="cover" value="">
                <a href="javascript:;" class="image-upload cover"><?php _e( 'Change Cover', 'recipe' ); ?></a>
            </div>
        </div>
    </div>     
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label for="description"><?php _e( 'Something About You', 'recipe' ); ?> </label>
                <?php wp_editor( $description, 'description' ) ?>
            </div>        	

            <input type="hidden" value="update_profile" name="action" />
            <input type="hidden" value="<?php echo esc_attr( $current_user->ID ) ?>" name="user_id" />
            <?php wp_nonce_field('profile','profile_field'); ?>

            <div class="send_result"></div>

            <a href="javascript:;" class="btn submit-form">
                <?php _e( 'Update Profile', 'recipe' ); ?>
            </a>            
        </div>
    </div>
</form>