<?php

/*-----------------------------------------------------------------------------------

	Theme Shortcodes

-----------------------------------------------------------------------------------*/


/*-----------------------------------------------------------------------------------*/
/*	Column Shortcodes
/*-----------------------------------------------------------------------------------*/

function tz_one_third( $atts, $content = null ) {
   return '<div class="one_third">' . do_shortcode($content) . '</div>';
}

add_shortcode('one_third', 'tz_one_third');

function tz_one_third_last( $atts, $content = null ) {
   return '<div class="one_third column-last">' . do_shortcode($content) . '</div><div class="clear"></div>';
}

add_shortcode('one_third_last', 'tz_one_third_last');

function tz_two_third( $atts, $content = null ) {
   return '<div class="two_third">' . do_shortcode($content) . '</div>';
}

add_shortcode('two_third', 'tz_two_third');

function tz_two_third_last( $atts, $content = null ) {
   return '<div class="two_third column-last">' . do_shortcode($content) . '</div><div class="clear"></div>';
}

add_shortcode('two_third_last', 'tz_two_third_last');

function tz_one_half( $atts, $content = null ) {
   return '<div class="one_half">' . do_shortcode($content) . '</div>';
}

add_shortcode('one_half', 'tz_one_half');

function tz_one_half_last( $atts, $content = null ) {
   return '<div class="one_half column-last">' . do_shortcode($content) . '</div><div class="clear"></div>';
}

add_shortcode('one_half_last', 'tz_one_half_last');

function tz_one_fourth( $atts, $content = null ) {
   return '<div class="one_fourth">' . do_shortcode($content) . '</div>';
}

add_shortcode('one_fourth', 'tz_one_fourth');

function tz_one_fourth_last( $atts, $content = null ) {
   return '<div class="one_fourth column-last">' . do_shortcode($content) . '</div><div class="clear"></div>';
}

add_shortcode('one_fourth_last', 'tz_one_fourth_last');

function tz_three_fourth( $atts, $content = null ) {
   return '<div class="three_fourth">' . do_shortcode($content) . '</div>';
}

add_shortcode('three_fourth', 'tz_three_fourth');

function tz_three_fourth_last( $atts, $content = null ) {
   return '<div class="three_fourth column-last">' . do_shortcode($content) . '</div><div class="clear"></div>';
}

add_shortcode('three_fourth_last', 'tz_three_fourth_last');

function tz_one_fifth( $atts, $content = null ) {
   return '<div class="one_fifth">' . do_shortcode($content) . '</div>';
}

add_shortcode('one_fifth', 'tz_one_fifth');

function tz_one_fifth_last( $atts, $content = null ) {
   return '<div class="one_fifth column-last">' . do_shortcode($content) . '</div><div class="clear"></div>';
}

add_shortcode('one_fifth_last', 'tz_one_fifth_last');

function tz_two_fifth( $atts, $content = null ) {
   return '<div class="two_fifth">' . do_shortcode($content) . '</div>';
}

add_shortcode('two_fifth', 'tz_two_fifth');

function tz_two_fifth_last( $atts, $content = null ) {
   return '<div class="two_fifth column-last">' . do_shortcode($content) . '</div><div class="clear"></div>';
}
add_shortcode('two_fifth_last', 'tz_two_fifth_last');

function tz_three_fifth( $atts, $content = null ) {
   return '<div class="three_fifth">' . do_shortcode($content) . '</div>';
}

add_shortcode('three_fifth', 'tz_three_fifth');

function tz_three_fifth_last( $atts, $content = null ) {
   return '<div class="three_fifth column-last">' . do_shortcode($content) . '</div><div class="clear"></div>';
}

add_shortcode('three_fifth_last', 'tz_three_fifth_last');

function tz_four_fifth( $atts, $content = null ) {
   return '<div class="four_fifth">' . do_shortcode($content) . '</div>';
}

add_shortcode('four_fifth', 'tz_four_fifth');

function tz_four_fifth_last( $atts, $content = null ) {
   return '<div class="four_fifth column-last">' . do_shortcode($content) . '</div><div class="clear"></div>';
}

add_shortcode('four_fifth_last', 'tz_four_fifth_last');

function tz_one_sixth( $atts, $content = null ) {
   return '<div class="one_sixth">' . do_shortcode($content) . '</div>';
}

add_shortcode('one_sixth', 'tz_one_sixth');

function tz_one_sixth_last( $atts, $content = null ) {
   return '<div class="one_sixth column-last">' . do_shortcode($content) . '</div><div class="clear"></div>';
}

add_shortcode('one_sixth_last', 'tz_one_sixth_last');

function tz_five_sixth( $atts, $content = null ) {
   return '<div class="five_sixth">' . do_shortcode($content) . '</div>';
}

add_shortcode('five_sixth', 'tz_five_sixth');

function tz_five_sixth_last( $atts, $content = null ) {
   return '<div class="five_sixth column-last">' . do_shortcode($content) . '</div><div class="clear"></div>';
}

add_shortcode('five_sixth_last', 'tz_five_sixth_last');


/*-----------------------------------------------------------------------------------*/
/*	Buttons
/*-----------------------------------------------------------------------------------*/


function tz_button( $atts, $content = null ) {
	
	extract(shortcode_atts(array(
		'url'     	 => '#',
		'target'     => '_self',
		'style'   => 'white',
		'size'	=> 'small'
    ), $atts));
	
   return '<a class="button '.$size.' '.$style.'" href="'.$url.'">' . do_shortcode($content) . '</a>';
}

add_shortcode('button', 'tz_button');


/*-----------------------------------------------------------------------------------*/
/*	Alerts
/*-----------------------------------------------------------------------------------*/


function tz_alert( $atts, $content = null ) {
	
	extract(shortcode_atts(array(
		'style'   => 'white'
    ), $atts));
	
   return '<div class="alert '.$style.'">' . do_shortcode($content) . '</div>';
}

add_shortcode('alert', 'tz_alert');


/*-----------------------------------------------------------------------------------*/
/*	Toggle Shortcodes
/*-----------------------------------------------------------------------------------*/

function tz_toggle( $atts, $content = null ) {
	
    extract(shortcode_atts(array(
		'title'    	 => 'Title goes here',
		'state'		 => 'open'
    ), $atts));
	
	$out = '';
	
	$out .= "<div data-id='".$state."' class=\"toggle\"><h4>".$title."</h4><div class=\"toggle-inner\">".do_shortcode($content)."</div></div>";
	
    return $out;
	
}

add_shortcode('toggle', 'tz_toggle');


/*-----------------------------------------------------------------------------------*/
/*	Tabs Shortcodes
/*-----------------------------------------------------------------------------------*/

global $tabs_array, $tabs_count;

$tabs_count = 0;

function tz_tabs( $atts, $content = null )
{
	global $tabs_array, $tabs_count;
	
	do_shortcode( $content );
	
	$output = '';
	
	if( is_array( $tabs_array ) )
	{
		$i = 0;
		$x = 0;
		
		$output .= '<div class="tabs"><div class="tab-inner">';
		$output .= '<ul class="nav clearfix">';
		
		foreach( $tabs_array as $tab )
		{
			$i++;
			$output .= '<li class="' . ( ( $i == 1 ) ? 'first' : '' ) . '"><a title="' . $tab['title'] . '" href="#tab-' . $i . '">' . $tab['title'] . '</a></li>';
		}
		
		$output .= '</ul>';
		
		foreach( $tabs_array as $tab )
		{
			$x++;
			$output .= '<div class="tab" id="tab-' . $x . '">' . do_shortcode( $tab['content'] ) .'</div>';
		}
		
		$output .= '</div></div>';
		
		return $output;
	}
}
add_shortcode('tabs', 'tz_tabs');


function tz_tabs_panes( $atts, $content = null )
{
	global $tabs_array, $tabs_count;
	
	extract(shortcode_atts(array(
		'title' => 'Title goes here'
	), $atts));
	
	$tabs_array[] = array(
		'title' => $title,
		'content' => do_shortcode( $content )
	);
	
	$tabs_count++;
}
add_shortcode('tab', 'tz_tabs_panes');

?>