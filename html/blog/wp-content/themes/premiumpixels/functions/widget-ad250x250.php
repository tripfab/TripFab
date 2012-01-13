<?php

/*-----------------------------------------------------------------------------------

	Plugin Name: Custom 250x250 Ad Unit
	Plugin URI: http://www.premiumpixels.com
	Description: A widget that allows the selection and configuration of a single 250x250 Banner
	Version: 1.0
	Author: Orman Clark
	Author URI: http://www.premiumpixels.com

-----------------------------------------------------------------------------------*/


// Add function to widgets_init that'll load our widget
add_action( 'widgets_init', 'tz_ad250_widgets' );

// Register widget
function tz_ad250_widgets() {
	register_widget( 'tz_Ad250_Widget' );
}

// Widget class
class tz_ad250_widget extends WP_Widget {


/*-----------------------------------------------------------------------------------*/
/*	Widget Setup
/*-----------------------------------------------------------------------------------*/
	
function tz_Ad250_Widget() {

	// Widget settings
	$widget_ops = array(
		'classname' => 'tz_ad250_widget',
		'description' => __('A widget that allows the display and configuration of of a single 250x250 Banner.', 'framework')
	);

	// Widget control settings
	$control_ops = array(
		'width' => 300,
		'height' => 350,
		'id_base' => 'tz_ad250_widget'
	);

	// Create the widget
	$this->WP_Widget( 'tz_ad250_widget', __('Custom 250x250 Ad', 'framework'), $widget_ops, $control_ops );
	
}


/*-----------------------------------------------------------------------------------*/
/*	Display Widget
/*-----------------------------------------------------------------------------------*/
	
function widget( $args, $instance ) {
	extract( $args );

	// Our variables from the widget settings
	$title = apply_filters('widget_title', $instance['title'] );
	$ad = $instance['ad'];
	$link = $instance['link'];

	// Before widget (defined by theme functions file)
	echo $before_widget;

	// Display the widget title if one was input
	if ( $title )
		echo $before_title . $title . $after_title;
		
	// Display a containing div
	echo '<div class="ads-250">';

	// Display Ad
	if ( $link )
		echo '<a href="' . $link . '"><img src="' . $ad . '" width="250" height="250" alt="" /></a>';
		
	elseif ( $ad )
	 	echo '<img src="' . $ad . '" width="250" height="250" alt="" />';
		
	echo '</div>';

	// After widget (defined by theme functions file)
	echo $after_widget;
}


/*-----------------------------------------------------------------------------------*/
/*	Update Widget
/*-----------------------------------------------------------------------------------*/
	
function update( $new_instance, $old_instance ) {
	$instance = $old_instance;

	// Strip tags to remove HTML (important for text inputs)
	$instance['title'] = strip_tags( $new_instance['title'] );

	// No need to strip tags
	$instance['ad'] = $new_instance['ad'];
	$instance['link'] = $new_instance['link'];

	return $instance;
}


/*-----------------------------------------------------------------------------------*/
/*	Widget Settings (Displays the widget settings controls on the widget panel)
/*-----------------------------------------------------------------------------------*/
	
function form( $instance ) {

	// Set up some default widget settings
	$defaults = array(
		'title' => '',
		'ad' => get_template_directory_uri()."/images/banner-250x250.gif",
		'link' => 'http://www.premiumpixels.com',
	);
	
	$instance = wp_parse_args( (array) $instance, $defaults ); ?>

	<!-- Widget Title: Text Input -->
	<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'framework') ?></label>
		<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
	</p>

	<!-- Ad image url: Text Input -->
	<p>
		<label for="<?php echo $this->get_field_id( 'ad' ); ?>"><?php _e('Ad image url:', 'framework') ?></label>
		<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'ad' ); ?>" name="<?php echo $this->get_field_name( 'ad' ); ?>" value="<?php echo $instance['ad']; ?>" />
	</p>
	
	<!-- Ad link url: Text Input -->
	<p>
		<label for="<?php echo $this->get_field_id( 'link' ); ?>"><?php _e('Ad link url:', 'framework') ?></label>
		<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'link' ); ?>" name="<?php echo $this->get_field_name( 'link' ); ?>" value="<?php echo $instance['link']; ?>" />
	</p>
	
	<?php
	}
}
?>