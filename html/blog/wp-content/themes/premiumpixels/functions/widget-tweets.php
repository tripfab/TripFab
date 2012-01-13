<?php
/*
 * Plugin Name: Custom Latest Tweets
 * Plugin URI: http://www.premiumpixels.com
 * Description: A widget that displays your latest tweets
 * Version: 2.0
 * Author: Orman Clark
 * Author URI: http://www.premiumpixels.com
 */

/*
 * Add function to widgets_init that'll load our widget.
 */
add_action( 'widgets_init', 'tz_tweets_widgets' );

/*
 * Register widget.
 */
function tz_tweets_widgets() {
	register_widget( 'TZ_Tweet_Widget' );
}

function tz_twitter_js($tz_twitter_username, $tz_twitter_postcount) {
        return '<script type="text/javascript" src="http://twitter.com/javascripts/blogger.js"></script>
		<script type="text/javascript" src="http://twitter.com/statuses/user_timeline/'.$tz_twitter_username.'.json?callback=twitterCallback2&amp;count='.$tz_twitter_postcount.'"></script>';
}


/*
 * Widget class.
 */
class tz_tweet_widget extends WP_Widget {

	/* ---------------------------- */
	/* -------- Widget setup -------- */
	/* ---------------------------- */
	
	function TZ_Tweet_Widget() {
	
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'tz_tweet_widget', 'description' => __('A widget that displays your latest tweets.', 'framework') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'tz_tweet_widget' );

		/* Create the widget. */
		$this->WP_Widget( 'tz_tweet_widget', __('Custom Latest Tweets','framework'), $widget_ops, $control_ops );
	}

	/* ---------------------------- */
	/* ------- Display Widget -------- */
	/* ---------------------------- */
	
	function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		
		global $tz_twitter_username, $tz_twitter_postcount;
		
		$tz_twitter_username = $instance['username'];
		$tz_twitter_postcount = $instance['postcount'];
		$tweettext = $instance['tweettext'];
		
		function echo_tweets_js() {
			global $tz_twitter_username, $tz_twitter_postcount;
			echo tz_twitter_js($tz_twitter_username, $tz_twitter_postcount);
		}
		
		add_action('wp_footer', 'echo_tweets_js');

		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;

		/* Display Latest Tweets */
		 ?>

            <ul id="twitter_update_list">
                <li><p></p></li>
            </ul>
            <a href="http://twitter.com/<?php echo $tz_twitter_username; ?>" class="twitter-link"><?php echo $tweettext; ?></a>
		
		<?php 

		/* After widget (defined by themes). */
		echo $after_widget;
	}

	/* ---------------------------- */
	/* ------- Update Widget -------- */
	/* ---------------------------- */
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['username'] = strip_tags( $new_instance['username'] );
		$instance['postcount'] = strip_tags( $new_instance['postcount'] );
		$instance['tweettext'] = strip_tags( $new_instance['tweettext'] );

		/* No need to strip tags for.. */

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
		'title' => 'Latest Tweets',
		'username' => 'ormanclark',
		'postcount' => '5',
		'tweettext' => 'Follow on Twitter',
		);
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		<p>


			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'framework') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
		</p>

		<!-- Username: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'username' ); ?>"><?php _e('Twitter Username e.g. ormanclark', 'framework') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'username' ); ?>" name="<?php echo $this->get_field_name( 'username' ); ?>" value="<?php echo $instance['username']; ?>" />
		</p>
		
		<!-- Postcount: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'postcount' ); ?>"><?php _e('Number of tweets (max 20)', 'framework') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'postcount' ); ?>" name="<?php echo $this->get_field_name( 'postcount' ); ?>" value="<?php echo $instance['postcount']; ?>" />
		</p>
		
		<!-- Tweettext: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'tweettext' ); ?>"><?php _e('Follow Text e.g. Follow me on Twitter', 'framework') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'tweettext' ); ?>" name="<?php echo $this->get_field_name( 'tweettext' ); ?>" value="<?php echo $instance['tweettext']; ?>" />
		</p>
		
	<?php
	}
}

?>