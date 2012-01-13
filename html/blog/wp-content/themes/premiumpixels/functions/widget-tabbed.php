<?php
/*
 * Plugin Name: Custom Tabbed Widget
 * Plugin URI: http://www.ormanclark.com
 * Description: A widget that display popular posts, recent posts, recent comments and tags
 * Version: 1.0
 * Author: Orman Clark
 * Author URI: http://www.ormanclark.com
 */

/*
 * tabd function to widgets_init that'll lotab our widget.
 */
add_action( 'widgets_init', 'tz_tab_widgets' );

/*
 * Register widget.
 */
function tz_tab_widgets() {
	register_widget( 'TZ_Tab_Widget' );
}

function enqueue(){
if (is_active_widget(false, false, 'tz_tab_widget') && (!is_admin())){
		wp_enqueue_script('jquery-ui-core');		
		wp_enqueue_script('jquery-ui-tabs');
		wp_register_script('tabbed', get_template_directory_uri().'/js/tabbed-widget.js', array('jquery-ui-tabs'));
		wp_enqueue_script('tabbed');
	}
}

add_action('init', 'enqueue');

/*
 * Widget class.
 */
class tz_tab_widget extends WP_Widget {

	/* ---------------------------- */
	/* -------- Widget setup -------- */
	/* ---------------------------- */
	
	function TZ_tab_Widget() {
	
		/* Widget settings */
		$widget_ops = array( 'classname' => 'tz_tab_widget', 'description' => __('A tabbed widget that display popular posts, recent posts, comments and tags.', 'framework') );

		/* Widget control settings */
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'tz_tab_widget' );

		/* Create the widget */
		$this->WP_Widget( 'tz_tab_widget', __('Custom Tabbed Widget', 'framework'), $widget_ops, $control_ops );
	}

	/* ---------------------------- */
	/* ------- Display Widget -------- */
	/* ---------------------------- */
	
	function widget( $args, $instance ) {
		global $wpdb;
		extract( $args );

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		$tab1 = $instance['tab1'];
		$tab2 = $instance['tab2'];
		$tab3 = $instance['tab3'];
		$tab4 = $instance['tab4'];
	

		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;

		//Randomize tab order in a new array
		$tab = array();
			
		/* Display a containing div */
		echo '<div id="tabs">';
			echo '<ul id="tab-items">';
				echo '<li><a href="#tabs-1"><span>'.$tab1.'</span></a></li>';
				echo '<li><a href="#tabs-2"><span>'.$tab2.'</span></a></li>';
				echo '<li><a href="#tabs-3"><span>'.$tab3.'</span></a></li>';
				echo '<li><a href="#tabs-4"><span>'.$tab4.'</span></a></li>';
			echo '</ul>';
			
			echo '<div class="tabs-inner">';
		
			// Popular posts tab
			echo '<div id="tabs-1" class="tab tab-popular">';
				echo '<ul>';
					
					$popPosts = new WP_Query();
					$popPosts->query('showposts=5&orderby=comment_count');
					while ($popPosts->have_posts()) : $popPosts->the_post(); ?>
						
						<li class="clearfix">
							<?php if (  (function_exists('has_post_thumbnail')) && (has_post_thumbnail())  ) { ?>
							<div class="tab-thumb">
								<a href="<?php the_permalink();?>" class="thumb"><?php the_post_thumbnail(); ?></a>
							</div>
							<?php } ?>
							<h3 class="entry-title"><a href="<?php the_permalink(); ?>" class="title"><?php the_title();?></a></h3>
							<div class="entry-meta entry-header">
								<span class="published"><?php the_time( get_option('date_format') ); ?></span>
								<span class="meta-sep">&middot;</span>
								<span class="comment-count"><?php comments_popup_link(__('No comments', 'framework'), __('1 Comment', 'framework'), __('% Comments', 'framework')); ?></span>
							</div>
						</li>
							
					<?php endwhile; 
					wp_reset_query();
					
					
				echo '</ul>';
			echo '</div><!-- #tabs-1 -->';

			//Recent posts tabs
			echo '<div id="tabs-2" class="tab tab-recent">';
				echo '<ul>';
					
						$recentPosts = new WP_Query();
						$recentPosts->query('showposts=5');
						while ($recentPosts->have_posts()) : $recentPosts->the_post(); ?>
							<li class="clearfix">
								<?php if (  (function_exists('has_post_thumbnail')) && (has_post_thumbnail())  ) { ?>
								<div class="tab-thumb">
									<a href="<?php the_permalink();?>" class="thumb"><?php the_post_thumbnail(); ?></a>
								</div>
								<?php } ?>
								<h3 class="entry-title"><a href="<?php the_permalink(); ?>" class="title"><?php the_title();?></a></h3>
								<div class="entry-meta entry-header">
									<span class="published"><?php the_time( get_option('date_format') ); ?></span>
									<span class="meta-sep">&middot;</span>
									<span class="comment-count"><?php comments_popup_link(__('No comments', 'framework'), __('1 Comment', 'framework'), __('% Comments', 'framework')); ?></span>
							</div>
							</li>
						<?php endwhile;
						
				echo '</ul>';
			echo '</div><!-- #tabs-2 -->';

			//Recent comments tabs
			echo '<div id="tabs-3" class="tab tab-comments">';
				
				$sql = "SELECT DISTINCT ID, post_title, post_password, comment_ID, comment_post_ID, comment_author, comment_author_email, comment_date_gmt, comment_approved, comment_type, comment_author_url, SUBSTRING(comment_content,1,70) AS com_excerpt FROM $wpdb->comments LEFT OUTER JOIN $wpdb->posts ON ($wpdb->comments.comment_post_ID = $wpdb->posts.ID) WHERE comment_approved = '1' AND comment_type = '' AND post_password = '' ORDER BY comment_date_gmt DESC LIMIT 5";
				$comments = $wpdb->get_results($sql);
				echo '<ul>';
					foreach ($comments as $comment) { ?>
					
					<li class="clearfix">
			    
					    <a href="<?php echo get_permalink($comment->ID); ?>#comment-<?php echo $comment->comment_ID; ?>" title="<?php echo strip_tags($comment->comment_author); ?> <?php _e('on ', 'framework'); ?><?php echo $comment->post_title; ?>"><?php echo get_avatar( $comment, '52' ); ?></a>
					
						<span><a href="<?php echo get_permalink($comment->ID); ?>#comment-<?php echo $comment->comment_ID; ?>" title="<?php echo strip_tags($comment->comment_author); ?> <?php _e('on ', 'framework'); ?><?php echo $comment->post_title; ?>"><?php echo strip_tags($comment->comment_author); ?>: <?php echo strip_tags($comment->com_excerpt); ?>...</a></span>
			
					</li>
					<?php }			
							
				echo '</ul>';
			echo '</div><!-- #tabs-3 -->';

			//Tags tab
			echo '<div id="tabs-4" class="tab tab-tags clearfix">';
			wp_tag_cloud('largest=12&smallest=12&unit=px');
			echo '</div><!-- #tabs-4 -->';

		echo '</div><!-- .tabs-inner -->';
		
		echo '</div><!-- #tabs -->';

		/* After widget (defined by themes). */
		echo $after_widget;
	}

	/* ---------------------------- */
	/* ------- Update Widget -------- */
	/* ---------------------------- */
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );

		/* No need to strip tags */
		$instance['tab1'] = $new_instance['tab1'];
		$instance['tab2'] = $new_instance['tab2'];
		$instance['tab3'] = $new_instance['tab3'];
		$instance['tab4'] = $new_instance['tab4'];
		
		return $instance;
	}
	
	/* ---------------------------- */
	/* ------- Widget Settings ------- */
	/* ---------------------------- */
	
	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	
	function form( $instance ) {
	
		/* Set up some default widget settings. */
		$defaults = array(
		'title' => '',
		'tab1' => 'Popular',
		'tab2' => 'Recent',
		'tab3' => 'Comments',
		'tab4' => 'Tags',
		);
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'framework') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
		</p>

		<!-- tab 1 title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'tab1' ); ?>"><?php _e('Tab 1 Title:', 'framework') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'tab1' ); ?>" name="<?php echo $this->get_field_name( 'tab1' ); ?>" value="<?php echo $instance['tab1']; ?>" />
		</p>
		
		<!-- tab 2 title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'link1' ); ?>"><?php _e('Tab 2 Title:', 'framework') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'tab2' ); ?>" name="<?php echo $this->get_field_name( 'tab2' ); ?>" value="<?php echo $instance['tab2']; ?>" />
		</p>
		
		<!-- tab 3 title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'tab2' ); ?>"><?php _e('Tab 3 Title:', 'framework') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'tab3' ); ?>" name="<?php echo $this->get_field_name( 'tab3' ); ?>" value="<?php echo $instance['tab3']; ?>" />
		</p>
		
		<!-- tab 4 title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'link2' ); ?>"><?php _e('Tab 4 Title:', 'framework') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'tab4' ); ?>" name="<?php echo $this->get_field_name( 'tab4' ); ?>" value="<?php echo $instance['tab4']; ?>" />
		</p>
		
	
	<?php
	}
}
?>