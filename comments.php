<?php
	/**********************************************************************
	***********************************************************************
	RECIPE COMMENTS
	**********************************************************************/	
	if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
		die ( 'Please do not load this page directly. Thanks!' );
	if ( post_password_required() ) {
		return;
	}

	/* THIS IS CHANGED IF WE ARE ON RECIPE SINGLE AND USER HAS ALREADY REVIEWED IT */
	global $can_review;
	$can_review = true;
?>

<?php if ( comments_open() ) :?>
	<?php if( get_comments_number() > 0 ): ?>
		<div class="white-block">
			<div class="content-inner">			
				<!-- title -->
				<div class="widget-title-wrap">
					<h5 class="widget-title">
                        <?php comments_number( __( 'No Comments', 'recipe' ), __( '1 Comment', 'recipe' ), __( '% Comments', 'recipe' ) ); ?>
					</h5>
				</div>
				<!--.title -->
			
				<!-- comments -->
				<div class="comment-content comments">
					<?php if( have_comments() ):?>
						<?php wp_list_comments( array(
							'type' => 'comment',
							'callback' => 'recipe_comments',
							'end-callback' => 'recipe_end_comments',
							'style' => 'div'
						)); ?>
					<?php endif; ?>
				</div>
				<!-- .comments -->
			
				<!-- comments pagination -->
				<?php
					$comment_links = paginate_comments_links( 
						array(
							'echo' => false,
							'type' => 'array',
							'prev_next' => false,
							'separator' => ' ',
						) 
					);
					if( !empty( $comment_links ) ):
				?>
					<div class="comments-pagination-wrap">
						<div class="pagination">
							<?php _e( 'Comment page: ', 'recipe');  echo recipe_format_pagination( $comment_links ); ?>
						</div>
					</div>
				<?php endif; ?>
				<!-- .comments pagination -->
			</div>	
		</div>
	<?php endif; ?>
	<?php
	global $post;
	$my_post = false;
	if( $post->post_author == get_current_user_id() && !isset( $_GET['replytocom'] ) ){
		$my_post = true;
		$can_review = false;
	}
	?>
	<?php if( $can_review ): ?>
		<div class="white-block">
			<div class="content-inner">	
				<!-- leave comment form -->
				<!-- title -->
				<div class="widget-title-wrap">
					<h5 class="widget-title">
						<?php _e( 'Leave Comment', 'recipe' ); ?>
					</h5>
				</div>
				<!--.title -->
				<?php
				$ratings = '';
				if( is_singular( 'recipe' ) && !$my_post && !isset( $_GET['replytocom'] ) ){
					$ratings = '<p class="comment-review">
			    		<label>'.__( 'Add Review', 'recipe' ).'</label>
			    		<input type="hidden" id="review" name="review" value=""/>
			    		<span class="bottom-ratings">
			    			<span class="icon icon_rate"></span>
			    			<span class="icon icon_rate"></span>
			    			<span class="icon icon_rate"></span>
			    			<span class="icon icon_rate"></span>
			    			<span class="icon icon_rate"></span>
			    		</span>
			    	</p>';
				}
				else{
					$ratings = '<input type="hidden" id="review" name="review" value="-1"/>';
				}
				?>
				<div id="contact_form">
					<?php
						$comments_args = array(
							'id_form'		=> 'comment-form',
							'label_submit'	=>	__( 'Send Comment', 'recipe' ),
							'title_reply'	=>	'',
							'fields'		=>	apply_filters( 'comment_form_default_fields', array(
													'author' => '<div class="form-group has-feedback">
																	<label for="name">'.__( 'Your Name', 'recipe' ).'</label>
																	<input type="text" class="form-control" id="name" name="author">
																</div>',
													'email'	 => '<div class="form-group has-feedback">
																	<label for="name">'.__( 'Your Email', 'recipe' ).'</label>
																	<input type="email" class="form-control" id="email" name="email">
																</div>',
//                                                    'captcha' => '<script src=\'https://www.google.com/recaptcha/api.js\'></script>
//												    <div class="g-recaptcha" data-sitekey="6LfIRGAUAAAAAOI2iwHMJqkw75LqZofH6B-SpJYN"></div>	'
												)),
							'comment_field'	=>	'<div class="form-group has-feedback">
													'.$ratings.'
													<label for="name">'.__( 'Your Comment', 'recipe' ).'</label>
													<textarea rows="10" cols="100" class="form-control" id="message" name="comment"></textarea>	
												</div>',
							'cancel_reply_link' => __( 'or cancel reply', 'recipe' ),
							'comment_notes_after' => '',
							'comment_notes_before' => '',
							'must_log_in' => '<p class="must-log-in">'.esc_html__( 'You must be', 'recipe').' <a href="'.esc_url( add_query_arg( array( 'redirect' => urlencode( get_permalink() ) ), recipe_get_permalink_by_tpl( 'page-tpl_register_login' ) ) ).'">'.esc_html__( 'logged in', 'recipe' ).'</a> '.esc_html__( 'to post a comment', 'recipe' ).'</p>'
						);
						comment_form( $comments_args );	
					?>
				</div>
				<!-- content -->
				<!-- .leave comment form -->
			</div>
		</div>
	<?php elseif( $my_post ): ?>
		<div class="white-block">
			<div class="content-inner">	
				<?php _e( 'You can not rate your own recipe.', 'recipe' ) ?>
			</div>
		</div>		
	<?php else: ?>
		<div class="white-block">
			<div class="content-inner">	
				<?php _e( 'You have already rated this recipe.', 'recipe' ) ?>
			</div>
		</div>
	<?php endif; ?>

<?php endif; ?>