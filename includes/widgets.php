<?php

class Custom_Widget_Posts extends WP_Widget {

	function __construct() {
		parent::__construct('custom_posts', __('Recipe Recent posts','recipe'), array('description' =>__('Display recent posts','recipe') ));
	}

	function widget($args, $instance) {
		extract($args);
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '' ) );
		
		$title = esc_attr( $instance['title'] );
		$text = esc_attr( $instance['text'] );		

		$title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Posts', 'recipe' );
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
		$number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 5;

		if ( ! $number ){
			$number = 5;
		}
		
		?>
		<?php echo $before_widget; ?>
		<?php 
		if ( $title ){
			echo $before_title . $title . $after_title; 
		}
		?>
		
		<?php
		$r = new WP_Query( apply_filters( 'widget_posts_args', array(
			'posts_per_page'      => $number,
			'no_found_rows'       => true,
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true
		) ) );

		if ($r->have_posts()):
		?>
			<ul class="list-unstyled">
			<?php 
			while ( $r->have_posts() ) : 
				$r->the_post();
				include( locate_template( 'includes/widget-loop.php' ) );
			endwhile; ?>
			</ul>
		<?php
		endif;
		?>
		
		<?php echo $after_widget; wp_reset_query(); ?>
<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = (int) $new_instance['number'];

		return $instance;
	}

	function form( $instance ) {
		$title     = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$number    = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
?>
		<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:', 'recipe' ); ?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>

		<p><label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"><?php _e( 'Number of posts to show:', 'recipe' ); ?></label>
		<input id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="text" value="<?php echo esc_attr( $number ); ?>" size="3" /></p>
<?php
	}
}


class Custom_Widget_Recent_Comments extends WP_Widget {

	function __construct() {
		parent::__construct('recipe_recent_comments', __('Recipe Recent Comments','recipe'), array('description' =>__('Display recent comments','recipe') ));
	}

	function widget( $args, $instance ) {
		global $comments, $comment;
		extract( $args );
		$output = '';
		$title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Recent Comments', 'recipe' );
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		$number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 5;
		if ( ! $number )
			$number = 5;
		$comments = get_comments( apply_filters( 'widget_comments_args', array(
			'number'      => $number,
			'status'      => 'approve',
			'post_status' => 'publish'
		) ) );

		$output .= $before_widget;
		if ( $title )
			$output .= $before_title . $title . $after_title;

		$output .= '<ul class="list-unstyled no-top-padding">';
		if ( $comments ) {

			foreach ( (array) $comments as $comment) {
				$comment_text = get_comment_text( $comment->comment_ID );
				if( strlen( $comment_text ) > 40 ){
					$comment_text = substr( $comment_text, 0, 40 );
					$comment_text = substr( $comment_text, 0, strripos( $comment_text, " "  ) );
					$comment_text .= "...";
				}
				$url  = recipe_get_avatar_url( get_avatar( $comment, 60 ) );
				
				$output .=  '<li>
								<div class="widget-image-thumb">
									<img src="'.esc_url( $url ).'" class="img-responsive" width="60" height="60" alt=""/>
								</div>
								
								<div class="widget-text">
									'.get_comment_author_link().'
									<a href="' . esc_url( get_comment_link($comment->comment_ID) ) . '" class="grey">' .$comment_text. '</a>
								</div>
								<div class="clearfix"></div>
							</li>';
			}
		}
		$output .= '</ul>';
		$output .= $after_widget;

		echo $output;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = absint( $new_instance['number'] );
		return $instance;
	}

	function form( $instance ) {
		$title  = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$number = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
?>
		<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:', 'recipe' ); ?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>

		<p><label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"><?php _e( 'Number of comments to show:', 'recipe' ); ?></label>
		<input id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="text" value="<?php echo esc_attr( $number ); ?>" size="3" /></p>
<?php
	}
}
class Custom_Top_Authors extends WP_Widget{
	function __construct() {
		parent::__construct('widget_top_author', __('Top Author','recipe'), array('description' =>__('Adds list of top authors.','recipe') ));
	}

	function widget($args, $instance) {
		global $wpdb;
		/** This filter is documented in wp-includes/default-widgets.php */
		$instance['title'] = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		$instance['count'] = apply_filters( 'widget_title', empty( $instance['count'] ) ? '5' : $instance['count'], $instance, $this->id_base );

		echo $args['before_widget'];
		
		$authors = $wpdb->get_results( "SELECT users.ID, COUNT( posts.ID ) AS post_count FROM {$wpdb->users} AS users RIGHT JOIN {$wpdb->posts} AS posts ON posts.post_author = users.ID WHERE posts.post_type='post' AND posts.post_status='publish' GROUP BY users.ID ORDER BY post_count DESC LIMIT {$instance['count']}" );
		
		if ( !empty($instance['title']) ){
			echo $args['before_title'] . $instance['title'] . $args['after_title'];
		}
		if( !empty( $authors ) ){
			echo '<ul class="list-unstyled no-top-padding">';
			foreach( $authors as $author ){
				$url = recipe_get_avatar_url( get_avatar( $author->ID, 60 ) );
				echo    '<li class="top-authors">
							<div class="widget-image-thumb">
								<img src="'.esc_url( $url ).'" class="img-responsive" width="60" height="60" alt=""/>
							</div>
							
							<div class="widget-text">
								<a href="'.esc_url( get_author_posts_url( $author->ID ) ).'">
									'.get_the_author_meta( 'display_name', $author->ID ).'
								</a>
								<p class="grey">'.__( 'Wrote ', 'recipe' ).' '.$author->post_count.' '.__( 'posts', 'recipe' ).'</p>
							</div>
							<div class="clearfix"></div>
						</li>';				
			}
			echo '</ul>';
		}
		echo $args['after_widget'];
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( stripslashes($new_instance['title']) );
		$instance['count'] = strip_tags( stripslashes($new_instance['count']) );
		return $instance;
	}

	function form( $instance ) {
		$title = isset( $instance['title'] ) ? $instance['title'] : '';
		$count = isset( $instance['count'] ) ? $instance['count'] : '';
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id('title') ); ?>"><?php _e('Title:', 'recipe') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id('title') ); ?>" name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id('count') ); ?>"><?php _e('Count:', 'recipe') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id('count') ); ?>" name="<?php echo esc_attr( $this->get_field_name('count') ); ?>" value="<?php echo esc_attr( $count ); ?>" />
		</p>		
		<?php
	}
}

class Custom_Social extends WP_Widget{
	function __construct() {
		parent::__construct('widget_social', __('Social Follow','recipe'), array('description' =>__('Adds list of the social icons.','recipe') ));
	}

	function widget($args, $instance) {
		/** This filter is documented in wp-includes/default-widgets.php */
		$instance['title'] = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		$facebook = !empty( $instance['facebook'] ) ? '<a href="'.esc_url( $instance['facebook'] ).'" target="_blank" class="btn"><span class="fa fa-facebook"></span></a>' : '';
		$twitter = !empty( $instance['twitter'] ) ? '<a href="'.esc_url( $instance['twitter'] ).'" target="_blank" class="btn"><span class="fa fa-twitter"></span></a>' : '';
		$google = !empty( $instance['google'] ) ? '<a href="'.esc_url( $instance['google'] ).'" target="_blank" class="btn"><span class="fa fa-google"></span></a>' : '';
		$linkedin = !empty( $instance['linkedin'] ) ? '<a href="'.esc_url( $instance['linkedin'] ).'" target="_blank" class="btn"><span class="fa fa-linkedin"></span></a>' : '';
		$pinterest = !empty( $instance['pinterest'] ) ? '<a href="'.esc_url( $instance['pinterest'] ).'" target="_blank" class="btn"><span class="fa fa-pinterest"></span></a>' : '';
		$youtube = !empty( $instance['youtube'] ) ? '<a href="'.esc_url( $instance['youtube'] ).'" target="_blank" class="btn"><span class="fa fa-youtube"></span></a>' : '';
		$flickr = !empty( $instance['flickr'] ) ? '<a href="'.esc_url( $instance['flickr'] ).'" target="_blank" class="btn"><span class="fa fa-flickr"></span></a>' : '';
		$behance = !empty( $instance['behance'] ) ? '<a href="'.esc_url( $instance['behance'] ).'" target="_blank" class="btn"><span class="fa fa-behance"></span></a>' : '';
		$instagram = !empty( $instance['instagram'] ) ? '<a href="'.esc_url( $instance['instagram'] ).'" target="_blank" class="btn"><span class="fa fa-instagram"></span></a>' : '';

		echo $args['before_widget'];
		
		if ( !empty($instance['title']) ){
			echo $args['before_title'] . $instance['title'] . $args['after_title'];
		}
		echo '<div class="widget-social">';
			echo $facebook.$twitter.$google.$linkedin.$pinterest.$youtube.$flickr.$behance.$instagram;
		echo '</div>';
		echo $args['after_widget'];
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( stripslashes($new_instance['title']) );
		$instance['facebook'] = strip_tags( stripslashes($new_instance['facebook']) );
		$instance['twitter'] = strip_tags( stripslashes($new_instance['twitter']) );
		$instance['google'] = strip_tags( stripslashes($new_instance['google']) );
		$instance['linkedin'] = strip_tags( stripslashes($new_instance['linkedin']) );
		$instance['pinterest'] = strip_tags( stripslashes($new_instance['pinterest']) );
		$instance['youtube'] = strip_tags( stripslashes($new_instance['youtube']) );
		$instance['flickr'] = strip_tags( stripslashes($new_instance['flickr']) );
		$instance['behance'] = strip_tags( stripslashes($new_instance['behance']) );
		$instance['instagram'] = strip_tags( stripslashes($new_instance['instagram']) );
		return $instance;
	}

	function form( $instance ) {
		$title = isset( $instance['title'] ) ? $instance['title'] : '';
		$facebook = isset( $instance['facebook'] ) ? $instance['facebook'] : '';
		$twitter = isset( $instance['twitter'] ) ? $instance['twitter'] : '';
		$google = isset( $instance['google'] ) ? $instance['google'] : '';
		$linkedin = isset( $instance['linkedin'] ) ? $instance['linkedin'] : '';
		$pinterest = isset( $instance['pinterest'] ) ? $instance['pinterest'] : '';
		$youtube = isset( $instance['youtube'] ) ? $instance['youtube'] : '';
		$flickr = isset( $instance['flickr'] ) ? $instance['flickr'] : '';
		$behance = isset( $instance['behance'] ) ? $instance['behance'] : '';
		$instagram = isset( $instance['instagram'] ) ? $instance['instagram'] : '';
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id('title') ); ?>"><?php _e('Title:', 'recipe') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id('title') ); ?>" name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id('facebook') ); ?>"><?php _e('Facebook:', 'recipe') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id('facebook') ); ?>" name="<?php echo esc_attr( $this->get_field_name('facebook') ); ?>" value="<?php echo esc_url( $facebook ); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id('twitter') ); ?>"><?php _e('Twitter:', 'recipe') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id('twitter') ); ?>" name="<?php echo esc_attr( $this->get_field_name('twitter') ); ?>" value="<?php echo esc_url( $twitter ); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id('google') ); ?>"><?php _e('Google +:', 'recipe') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id('google') ); ?>" name="<?php echo esc_attr( $this->get_field_name('google') ); ?>" value="<?php echo esc_url( $google ); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id('linkedin') ); ?>"><?php _e('Linkedin:', 'recipe') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id('linkedin') ); ?>" name="<?php echo esc_attr( $this->get_field_name('linkedin') ); ?>" value="<?php echo esc_url( $linkedin ); ?>" />
		</p>			
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id('youtube') ); ?>"><?php _e('YouTube:', 'recipe') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id('youtube') ); ?>" name="<?php echo esc_attr( $this->get_field_name('youtube') ); ?>" value="<?php echo esc_url( $youtube ); ?>" />
		</p>		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id('pinterest') ); ?>"><?php _e('Pinterest:', 'recipe') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id('pinterest') ); ?>" name="<?php echo esc_attr( $this->get_field_name('pinterest') ); ?>" value="<?php echo esc_attr( $pinterest ); ?>" />
		</p>		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id('flickr') ); ?>"><?php _e('Flickr:', 'recipe') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id('flickr') ); ?>" name="<?php echo esc_attr( $this->get_field_name('flickr') ); ?>" value="<?php echo esc_attr( $flickr ); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id('behance') ); ?>"><?php _e('Behance:', 'recipe') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id('behance') ); ?>" name="<?php echo esc_attr( $this->get_field_name('behance') ); ?>" value="<?php echo esc_attr( $behance ); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id('instagram') ); ?>"><?php _e('Instagram:', 'recipe') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id('instagram') ); ?>" name="<?php echo esc_attr( $this->get_field_name('instagram') ); ?>" value="<?php echo esc_attr( $instagram ); ?>" />
		</p>
		<?php
	}
}

class Custom_Subscribe extends WP_Widget{
	function __construct() {
		parent::__construct('widget_subscribe', __('Subscribe','recipe'), array('description' =>__('Adds subscribe form in the sidebar.','recipe') ));
	}

	function widget($args, $instance) {
		/** This filter is documented in wp-includes/default-widgets.php */
		$instance['title'] = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

		echo $args['before_widget'];
		
		if ( !empty($instance['title']) ){
			echo $args['before_title'] . $instance['title'] . $args['after_title'];
		}
		echo '<div class="subscribe-form">
				<div class="recipe-form">
					<input type="text" class="form-control email" placeholder="'.esc_attr__( 'Input email here...', 'recipe' ).'">
					<a href="javascript:;" class="btn btn-default subscribe"><i class="fa fa-rss"></i></a>
				</div>
				<div class="sub_result"></div>
			  </div>';
		echo $args['after_widget'];
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( stripslashes($new_instance['title']) );
		return $instance;
	}

	function form( $instance ) {
		$title = isset( $instance['title'] ) ? $instance['title'] : '';
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id('title') ); ?>"><?php _e('Title:', 'recipe') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id('title') ); ?>" name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" value="<?php echo esc_attr( $title ); ?>" />
		</p>	
		<?php
	}
}

class Shortcode_Text extends WP_Widget{
	function __construct() {
		parent::__construct('widget_shortcode', __('Recipe Shortcode Text','recipe'), array('description' =>__('Text widget which can render shortcode.','recipe') ));
	}

	function widget($args, $instance) {
		/** This filter is documented in wp-includes/default-widgets.php */
		$instance['title'] = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		$instance['text'] = $instance['text'];

		echo $args['before_widget'];
		
		if ( !empty($instance['title']) ){
			echo $args['before_title'] . $instance['title'] . $args['after_title'];
		}
		echo do_shortcode( $instance['text'] );
		echo $args['after_widget'];
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( stripslashes($new_instance['title']) );
		$instance['text'] = $new_instance['text'];
		return $instance;
	}

	function form( $instance ) {
		$title = isset( $instance['title'] ) ? $instance['title'] : '';
		$text = isset( $instance['text'] ) ? $instance['text'] : '';
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id('title') ); ?>"><?php _e('Title:', 'recipe') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id('title') ); ?>" name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="shortcode-select"><?php _e('Shortcode:', 'recipe') ?></label>
			<select id="shortcode-select" name="shortcode-select" class="shortcode-add">
				<option value=""><?php _e( '-Select-', 'recipe' ) ?></option>
				<option value="accordion"><?php _e( 'Accordion', 'recipe' ) ?></option>
				<option value="alert"><?php _e( 'Alert', 'recipe' ) ?></option>
				<option value="button"><?php _e( 'Button', 'recipe' ) ?></option>
				<option value="bg_gallery"><?php _e( 'Gallery', 'recipe' ) ?></option>
				<option value="icon"><?php _e( 'Icon', 'recipe' ) ?></option>
				<option value="iframe"><?php _e( 'Iframe', 'recipe' ) ?></option>
				<option value="label"><?php _e( 'Label', 'recipe' ) ?></option>
				<option value="progressbar"><?php _e( 'Progress Bar', 'recipe' ) ?></option>
				<option value="tabs"><?php _e( 'Tabs', 'recipe' ) ?></option>
				<option value="toggle"><?php _e( 'Toggle', 'recipe' ) ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id('text') ); ?>"><?php _e('Text:', 'recipe') ?></label>
			<textarea type="text" class="widefat shortcode-input" id="<?php echo esc_attr( $this->get_field_id('text') ); ?>" name="<?php echo esc_attr( $this->get_field_name('text') ); ?>" ><?php echo esc_textarea( $text ); ?></textarea>
		</p>
		<?php
	}
}

class Recipes extends WP_Widget{
	function __construct() {
		parent::__construct('widget_recipe', __('Recipes','recipe'), array('description' =>__('Add recipes to the widget.','recipe') ));
	}

	function widget($args, $instance) {
		/** This filter is documented in wp-includes/default-widgets.php */
		$instance['title'] = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		$instance['number'] = empty( $instance['number'] ) ? 5 : $instance['number'];
		$instance['orderby'] = $instance['orderby'];

		$recipe_args = array(
			'post_type' => 'recipe',
			'post_status' => 'publish',
			'posts_per_page' => $instance['number'],
			'order' => 'DESC',
		);

		if( !empty( $instance['orderby'] ) ){
			if( $instance['orderby'] == 'title' ){
				$recipe_args['orderby'] = 'title';
			}
			else{
				$recipe_args['orderby'] = 'meta_value_num';
				$recipe_args['meta_key'] = $instance['orderby'];
			}
		}

		echo $args['before_widget'];
		
		if ( !empty($instance['title']) ){
			echo $args['before_title'] . $instance['title'] . $args['after_title'];
		}
		$recipes = new WP_Query( $recipe_args );
		if( $recipes->have_posts() ){
			echo '<ul class="list-unstyled no-top-padding">';
			while( $recipes->have_posts() ){
				$recipes->the_post();
				$image = wp_get_attachment_image( get_post_thumbnail_id( get_the_ID() ), 'widget-thumb', false, array( 'class' => 'img-responsive' ) );
				switch( $instance['orderby'] ){
					case '': $subtitle = '<i class="fa fa-calendar-o tip" data-title="'.__( 'Create date', 'recipe' ).'"></i>'.get_the_time(get_option( 'date_format' )); break;
					case 'average_review': 
						ob_start();
						recipe_calculate_ratings();
						$subtitle = ob_get_contents();
						ob_end_clean();
						break;
					case 'title': $subtitle = '<i class="fa fa-calendar-o tip" data-title="'.__( 'Create date', 'recipe' ).'"></i>'.get_the_time(get_option( 'date_format' )); break;
					case 'favourited': 
						$favourited = get_post_meta( get_the_ID(), 'favourited', true );
						if( empty( $favourited ) ){
							$favourited = 0;
						}
						$subtitle = '<i class="fa fa-heart-o tip" data-title="'.__( 'Favourites', 'recipe' ).'"></i>'.$favourited; break;
					case 'likes': 
						$likes = get_post_meta( get_the_ID(), 'likes', true );
						if( empty( $likes ) ){
							$likes = 0;
						}
						$subtitle = '<i class="fa fa-thumbs-o-up tip" data-title="'.__( 'Likes', 'recipe' ).'"></i>'.$likes; break;
					case 'views': 
						$views = get_post_meta( get_the_ID(), 'views', true );
						if( empty( $views ) ){
							$views = 0;
						}
						$subtitle = '<i class="fa fa-eye tip" data-title="'.__( 'Views', 'recipe' ).'"></i>'.$views; break;
				}
				echo    '<li class="top-authors">
							<div class="widget-image-thumb">
								'.$image.'
							</div>
							
							<div class="widget-text">
								<a href="'.get_permalink( get_the_ID() ).'">
									'.get_the_title().'
								</a>
								<p class="grey">'.$subtitle.'</p>
							</div>
							<div class="clearfix"></div>
						</li>';				
			}
			echo '</ul>';
		}
		wp_reset_query();
		echo $args['after_widget'];
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( stripslashes($new_instance['title']) );
		$instance['number'] = $new_instance['number'];
		$instance['orderby'] = $new_instance['orderby'];
		return $instance;
	}

	function form( $instance ) {
		$title = isset( $instance['title'] ) ? $instance['title'] : '';
		$number = isset( $instance['number'] ) ? $instance['number'] : '';
		$orderby = isset( $instance['orderby'] ) ? $instance['orderby'] : '';
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id('title') ); ?>"><?php _e('Title:', 'recipe') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id('title') ); ?>" name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id('number') ); ?>"><?php _e('Number:', 'recipe') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id('number') ); ?>" name="<?php echo esc_attr( $this->get_field_name('number') ); ?>" value="<?php echo esc_attr( $number ); ?>" />
		</p>		
		<p>
			<label for="source"><?php _e('Order By:', 'recipe') ?></label>
			<select id="<?php echo esc_attr( $this->get_field_id('orderby') ); ?>" name="<?php echo esc_attr( $this->get_field_name('orderby') ); ?>" class="widefat">
				<option value="" <?php echo empty( $orderby ) ? esc_attr( 'selected="selected"' ) : '' ?>><?php _e( 'Latest', 'recipe' ) ?></option>
				<option value="average_review" <?php echo $orderby == 'average_review' ? esc_attr( 'selected="selected"' ) : '' ?>><?php _e( 'Ratings', 'recipe' ) ?></option>
				<option value="title" <?php echo $orderby == 'title' ? esc_attr( 'selected="selected"' ) : '' ?>><?php _e( 'Title', 'recipe' ) ?></option>
				<option value="favourited" <?php echo $orderby == 'favourited' ? esc_attr( 'selected="selected"' ) : '' ?>><?php _e( 'Favourited', 'recipe' ) ?></option>
				<option value="likes" <?php echo $orderby == 'likes' ? esc_attr( 'selected="selected"' ) : '' ?>><?php _e( 'Likes', 'recipe' ) ?></option>
				<option value="views" <?php echo $orderby == 'views' ? esc_attr( 'selected="selected"' ) : '' ?>><?php _e( 'Views', 'recipe' ) ?></option>
			</select>
		</p>
		<?php
	}
}

class Top_Users extends WP_Widget{
	function __construct() {
		parent::__construct('widget_top_users', __('Top Users','recipe'), array('description' =>__('Display Top Users.','recipe') ));
	}

	function widget($args, $instance) {
		/** This filter is documented in wp-includes/default-widgets.php */
		$instance['title'] = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		$instance['number'] = empty( $instance['number'] ) ? 5 : $instance['number'];

		$top_users = new WP_User_Query(array(
			'orderby' => 'meta_value_num',
			'meta_key' => 'average_rating',
			'order' => 'DESC',
			'number' => $instance['number']	
		));

		echo $args['before_widget'];
		
		if ( !empty($instance['title']) ){
			echo $args['before_title'] . $instance['title'] . $args['after_title'];
		}
		if( !empty( $top_users->results ) ){
			echo '<ul class="list-unstyled no-top-padding">';
			foreach( $top_users->results as $user ){
				$url = recipe_get_avatar_url( get_avatar( $user->ID, 60 ) );
				$count = recipe_count_custom_post( 'recipe', array(
					'author' => $user->ID
				));				
				echo    '<li class="top-authors">
							<div class="widget-image-thumb">
								<img src="'.esc_url( $url ).'" class="img-responsive" width="60" height="60" alt=""/>
							</div>
							
							<div class="widget-text">
								<a href="'.get_author_posts_url( $user->ID ).'">
									'.get_the_author_meta( 'display_name', $user->ID ).'
								</a>
								<p class="grey">'.__( 'Wrote ', 'recipe' ).' '.$count.' '.__( 'recipes', 'recipe' ).'</p>
							</div>
							<div class="clearfix"></div>
						</li>';				
			}
			echo '</ul>';
		}
		wp_reset_query();
		echo $args['after_widget'];
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( stripslashes($new_instance['title']) );
		$instance['number'] = $new_instance['number'];
		return $instance;
	}

	function form( $instance ) {
		$title = isset( $instance['title'] ) ? $instance['title'] : '';
		$number = isset( $instance['number'] ) ? $instance['number'] : '';
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id('title') ); ?>"><?php _e('Title:', 'recipe') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id('title') ); ?>" name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id('number') ); ?>"><?php _e('Number:', 'recipe') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id('number') ); ?>" name="<?php echo esc_attr( $this->get_field_name('number') ); ?>" value="<?php echo esc_attr( $number ); ?>" />
		</p>
		<?php
	}
}

class Recipe_Categories extends WP_Widget{
	function __construct() {
		parent::__construct('widget_recipe_categories', __('Recipe Categories','recipe'), array('description' =>__('Display Recipe Categories.','recipe') ));
	}

	function widget($args, $instance) {
		global $recipe_slugs;
		/** This filter is documented in wp-includes/default-widgets.php */
		$instance['title'] = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		$instance['categories'] = empty( $instance['categories'] ) ? array() : (array)$instance['categories'];

		echo $args['before_widget'];
		$permalink = recipe_get_permalink_by_tpl( 'page-tpl_search' );
		
		if ( !empty($instance['title']) ){
			echo $args['before_title'] . $instance['title'] . $args['after_title'];
		}
		if( !empty( $instance['categories'] ) ){
			echo '<ul class="list-unstyled category-list">';
			foreach( $instance['categories'] as $category_id ){
				$term_meta = get_option( "taxonomy_$category_id" );
				$value = !empty( $term_meta['category_icon'] ) ? $term_meta['category_icon'] : '';		
				$term = get_term_by( 'id', $category_id, 'recipe-category' );
				if( $term ){
					echo '<li>
							<span class="icon '.esc_attr( $value ).'"></span>
							<a href="'.( esc_url( add_query_arg( array( $recipe_slugs['recipe-category'] => $term->slug ), $permalink ) ) ).'">
								'.$term->name.'
							</a>
						  </li>';				
				}
			}
			echo '</ul>';
		}
		wp_reset_query();
		echo $args['after_widget'];
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( stripslashes($new_instance['title']) );
		$instance['categories'] = $new_instance['categories'];
		return $instance;
	}

	function form( $instance ) {
		$title = isset( $instance['title'] ) ? $instance['title'] : '';
		$categories = isset( $instance['categories'] ) ? (array)$instance['categories'] : array();
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id('title') ); ?>"><?php _e('Title:', 'recipe') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id('title') ); ?>" name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id('categories') ); ?>"><?php _e('Categories:', 'recipe') ?></label>
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id('categories') ); ?>" name="<?php echo esc_attr( $this->get_field_name('categories') ); ?>[]" multiple>
				<?php
				$recipe_categories = get_terms( 'recipe-category', array( 'parent' => 0 ) );
				if( !empty( $recipe_categories ) ){
					foreach( $recipe_categories as $recipe_category ){
						echo '<option value="'.esc_attr( $recipe_category->term_id ).'" '.( in_array( $recipe_category->term_id, $categories ) ? 'selected="selected"' : '' ).'>'.$recipe_category->name.'</option>';
					}
				}
				?>
			</select>
		</p>
		<?php
	}
}

class Recipe_Single extends WP_Widget{
	function __construct() {
		parent::__construct('widget_recipe_single', __('Recipe Single','recipe'), array('description' =>__('Adds one recipe to the widget.','recipe') ));
	}

	function widget($args, $instance) {
		/** This filter is documented in wp-includes/default-widgets.php */
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		$recipes = empty( $instance['recipes'] ) ? array() : $instance['recipes'];

		if( !empty( $recipes ) ){

			$recipe_args = array(
				'post_type' => 'recipe',
				'post_status' => 'publish',
				'posts_per_page' => -1,
				'post__in' => $recipes,
				'orderby' => 'post__in'
			);

			echo $args['before_widget'];
			
			if ( !empty($instance['title']) ){
				echo $args['before_title'] . $instance['title'] . $args['after_title'];
			}
			$recipes = new WP_Query( $recipe_args );
			if( $recipes->have_posts() ){
				echo '<ul class="list-unstyled similar-recipes">';
				while( $recipes->have_posts() ){
					$recipes->the_post();
					$image = wp_get_attachment_image( get_post_thumbnail_id( get_the_ID() ), 'box-thumb', false, array( 'class' => 'img-responsive' ) );
					echo    '<li>
								<a href="'.get_permalink().'" class="no-margin">
									<div class="embed-responsive embed-responsive-16by9">
										'.$image.'
									</div>
								</a>
								<a href="'.get_permalink().'">
									'.get_the_title().'
								</a>									
							</li>';				
				}
				echo '</ul>';
			}
			wp_reset_postdata();
			echo $args['after_widget'];
		}
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( stripslashes($new_instance['title']) );
		$instance['recipes'] = $new_instance['recipes'];
		return $instance;
	}

	function form( $instance ) {
		$title = isset( $instance['title'] ) ? $instance['title'] : '';
		$recipes = isset( $instance['recipes'] ) ? $instance['recipes'] : array();
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id('title') ); ?>"><?php _e('Title:', 'recipe') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id('title') ); ?>" name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" value="<?php echo esc_attr( $title ); ?>" />
		</p>	
		<p>
			<label for="recipes"><?php _e('Recipes To Show:', 'recipe') ?></label>
			<select id="<?php echo esc_attr( $this->get_field_id('recipes') ); ?>" name="<?php echo esc_attr( $this->get_field_name('recipes') ); ?>[]" class="widefat" multiple>
				<?php
				$recipes_list = get_posts(array(
					'post_per_page' => -1,
					'post_type' => 'recipe',
					'post_status' => 'publish',
				));
				if( !empty( $recipes_list ) ){
					foreach( $recipes_list as $recipe ){
						echo '<option value="'.esc_attr( $recipe->ID ).'" '.( in_array( $recipe->ID, $recipes ) ? 'selected="selected"' : '' ).'>'.$recipe->post_title.'</option>';
					}
				}
				?>
			</select>
		</p>
		<?php
	}
}

function custom_widgets_init() {
	if ( !is_blog_installed() ){
		return;
	}	
	/* register new ones */
	register_widget('Custom_Widget_Posts');
	register_widget('Custom_Widget_Recent_Comments');
	register_widget('Custom_Top_Authors');
	register_widget('Custom_Social');
	register_widget('Custom_Subscribe');
	register_widget('Shortcode_Text');
	register_widget('Recipes');
	register_widget('Top_Users');
	register_widget('Recipe_Categories');
	register_widget('Recipe_Single');
}

add_action('widgets_init', 'custom_widgets_init', 1);
?>