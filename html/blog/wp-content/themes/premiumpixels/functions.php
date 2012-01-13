<?php

/* 
	Here we have all the custom functions for the theme
	Please be extremely cautious editing this file,
	When things go wrong, they tend to go wrong in a big way.
	You have been warned!
*/

/*-----------------------------------------------------------------------------------*/
/*	Theme Localisation
/*-----------------------------------------------------------------------------------*/

load_theme_textdomain ('framework');


/*-----------------------------------------------------------------------------------*/
/*	Register WP3.0+ Menus
/*-----------------------------------------------------------------------------------*/

function register_menu() {
	register_nav_menu('primary-menu', __('Primary Menu'));
}
add_action('init', 'register_menu');


/*-----------------------------------------------------------------------------------*/
/*	Sidebars
/*-----------------------------------------------------------------------------------*/

if ( function_exists('register_sidebar') ) {
	register_sidebar(array(
		'name' => 'Main Sidebar',
		'before_widget' => '<div id="%1$s" class="widget %2$s clearfix">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	));
	register_sidebar(array(
		'name' => 'Page Sidebar',
		'before_widget' => '<div id="%1$s" class="widget %2$s clearfix">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	));
	
	register_sidebar(array(
		'name' => 'Footer Area 1',
		'before_widget' => '<div id="%1$s" class="widget %2$s clearfix">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	));
	
	register_sidebar(array(
		'name' => 'Footer Area 2',
		'before_widget' => '<div id="%1$s" class="widget %2$s clearfix">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	));
	
	register_sidebar(array(
		'name' => 'Footer Area 3',
		'before_widget' => '<div id="%1$s" class="widget %2$s clearfix">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	));
	
	register_sidebar(array(
		'name' => 'Footer Area 4',
		'before_widget' => '<div id="%1$s" class="widget %2$s clearfix">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	));
}


/*-----------------------------------------------------------------------------------*/
/*	Thumbnails
/*-----------------------------------------------------------------------------------*/

if ( function_exists( 'add_theme_support' ) ) { // Added in 2.9
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 52, 52, true ); // Normal post thumbnails
	add_image_size( 'archive-thumb', 570, 300, true ); // Post thumbs
	add_image_size( 'gallery-thumb', 166, 140, true ); // Gallery thumbs
}

/*-----------------------------------------------------------------------------------*/
/*	Excerpt Length
/*-----------------------------------------------------------------------------------*/

function tz_excerpt_length($length) {
return 55; }
add_filter('excerpt_length', 'tz_excerpt_length');


/*-----------------------------------------------------------------------------------*/
/*	Exerpt More
/*-----------------------------------------------------------------------------------*/

function tz_excerpt_more($excerpt) {
return str_replace('[...]', '...', $excerpt); }
add_filter('wp_trim_excerpt', 'tz_excerpt_more');


/*-----------------------------------------------------------------------------------*/
/*	Scripts
/*-----------------------------------------------------------------------------------*/

function tz_google_jquery() {
	if (!is_admin()) {
		// comment out the next two lines to load the local copy of jQuery
		wp_deregister_script('jquery');
		wp_register_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js', false, '1.4.2');
		wp_register_script('validation', 'http://ajax.microsoft.com/ajax/jquery.validate/1.7/jquery.validate.min.js', 'jquery');
		wp_register_script('color', get_template_directory_uri().'/js/jquery.color.js', 'jquery');
		wp_register_script('tz_custom', get_template_directory_uri().'/js/jquery.custom.js', 'jquery', true);
		wp_register_script('superfish', get_template_directory_uri().'/js/superfish.js', 'jquery');
		wp_register_script('tabbed', get_template_directory_uri().'/js/jquery.tabbed-widget.js', array('jquery-ui-tabs'));
		wp_register_script('jquery-ui-custom', get_template_directory_uri() . '/js/jquery-ui-1.8.5.custom.min.js', 'jquery');
	
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-custom');
		wp_enqueue_script('color');
		wp_enqueue_script('superfish');
		wp_enqueue_script('tz_custom');
	}
}
add_action('init', 'tz_google_jquery');


// load custom script which shows on the contact page.
function tz_contact_js() {
	if (is_page_template('template-contact.php') ) wp_enqueue_script('validation');
}
add_action('wp_print_scripts', 'tz_contact_js');

// load single scripts only on single pages
function tz_single_scripts() {
	if(is_singular()) wp_enqueue_script( 'comment-reply' ); // loads the javascript required for threaded comments 
}
add_action('wp_print_scripts', 'tz_single_scripts');


// load validation js for contact form template
function tz_contact_validate() {
	if (is_page_template('template-contact.php')) { ?>
		<script type="text/javascript">
			jQuery(document).ready(function(){
				jQuery("#contactForm").validate();
			});
		</script>
	<?php }
}
add_action('wp_head', 'tz_contact_validate');


/*-----------------------------------------------------------------------------------*/
/*	Body Class
/*-----------------------------------------------------------------------------------*/

add_filter('body_class','tz_browser_body_class');
function tz_browser_body_class($classes) {
	global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone;

	if($is_lynx) $classes[] = 'lynx';
	elseif($is_gecko) $classes[] = 'gecko';
	elseif($is_opera) $classes[] = 'opera';
	elseif($is_NS4) $classes[] = 'ns4';
	elseif($is_safari) $classes[] = 'safari';
	elseif($is_chrome) $classes[] = 'chrome';
	elseif($is_IE) $classes[] = 'ie';
	else $classes[] = 'unknown';

	if($is_iphone) $classes[] = 'iphone';
	return $classes;
}


// Output the styling for the seperated Pings
function tz_list_pings($comment, $args, $depth) {
       $GLOBALS['comment'] = $comment; ?>
<li id="comment-<?php comment_ID(); ?>"><?php comment_author_link(); ?>
<?php }


/*-----------------------------------------------------------------------------------*/
/*	Comments
/*-----------------------------------------------------------------------------------*/

function tz_comment($comment, $args, $depth) {
   $GLOBALS['comment'] = $comment; ?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
		<div id="comment-<?php comment_ID(); ?>" class="comment-body">
			<?php echo get_avatar($comment,$size='50'); ?>
			<div class="comment-author vcard clearfix">
				<?php printf(__('<cite class="fn">%s</cite> <span class="says">says:</span>'), get_comment_author_link()) ?>
			</div>			
			<div class="comment-meta commentmetadata"><a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>"><?php printf(__('%1$s at %2$s'), get_comment_date(),  get_comment_time()) ?></a><?php edit_comment_link(__('(Edit)'),'  ','') ?>
			</div>
			
			<div class="comment-text clear">
				<?php if ($comment->comment_approved == '0') : ?>
				<span class="moderation"><?php _e('Your comment is awaiting moderation.') ?></em></span>
				<?php endif; ?>
				<?php comment_text() ?>
			</div>		
			
			<?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'reply_text' => 'Reply &rarr;', 'max_depth' => $args['max_depth']))) ?>	
		</div>
<?php
        }

/*-----------------------------------------------------------------------------------*/
/*	Load Widgets & Shortcodes
/*-----------------------------------------------------------------------------------*/

// Add the 125x125 Ad Block Custom Widget
include("functions/widget-ad125.php");

// Add the 300x250 Ad Block Custom Widget
include("functions/widget-ad250x250.php");

// Add the Latest Tweets Custom Widget
include("functions/widget-tweets.php");

// Add the Flickr Photos Custom Widget
include("functions/widget-flickr.php");

// Add the Custom Video Widget
include("functions/widget-video.php");

// Add the Tabbed Content Widget
include("functions/widget-tabbed.php");

// Add the Theme Shortcodes
include("functions/theme-shortcodes.php");


/*-----------------------------------------------------------------------------------*/
/*	Filters that allow shortcodes in Text Widgets
/*-----------------------------------------------------------------------------------*/

add_filter('widget_text', 'shortcode_unautop');
add_filter('widget_text', 'do_shortcode');

/*-----------------------------------------------------------------------------------*/
/*	Load Theme Options
/*-----------------------------------------------------------------------------------*/

define('TZ_FILEPATH', TEMPLATEPATH);
define('TZ_DIRECTORY', get_template_directory_uri());

require_once (TZ_FILEPATH . '/admin/admin-functions.php');
require_once (TZ_FILEPATH . '/admin/admin-interface.php');
require_once (TZ_FILEPATH . '/functions/theme-options.php');
require_once (TZ_FILEPATH . '/functions/theme-functions.php');
require_once (TZ_FILEPATH . '/tinymce/tinymce.loader.php');

function string_getInsertedString($long_string,$short_string,$is_html=false){
  if($short_string>=strlen($long_string))return false;
  $insertion_length=strlen($long_string)-strlen($short_string);
  for($i=0;$i<strlen ($short_string);++$i){
    if($long_string[$i]!=$short_string[$i])break;
  }
  $inserted_string=substr($long_string,$i,$insertion_length);
  if($is_html && $inserted_string[$insertion_length-1]=='<'){
    $inserted_string='<'.substr($inserted_string,0,$insertion_length-1);
  }
  return $inserted_string;
}

function DOMElement_getOuterHTML($document,$element){
  $html=$document->saveHTML();
  //$element->parentNode->removeChild($element);
  $html2=$document->saveHTML();
  return string_getInsertedString($html,$html2,true);
  
  
}

function getFollowers($username){

  	$memcached = new Memcache;
	$memcached->connect('10.178.192.201','11211') or die('Memcached Error');
  	$twitter_followers = $memcached->get('twitter_followers');
	if($twitter_followers === false){
	  	$ch = curl_init('http://twitter.com/users/show/tripfab.xml');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$twitter_api = curl_exec($ch);
	//var_dump($twitter_api); die;
		$text_output = '<followers_count>'; $end = '</followers_count>';
		$page = $twitter_api;
		$explode_content = explode($text_output,$page);
		$page = $explode_content[1];
		$explode_content = explode($end,$page);
		$twitter_followers = $explode_content[0];
		if($twitter_followers == '') { $twitter_followers = '0'; }
		
		$memcached->add('twitter_followers',$twitter_followers,false,'86400');
		
	} else {
		$twitter_followers;
	}
	return $twitter_followers;
}

function res_fromemail($email) {
    $wpfrom = 'hello@tripfab.com';
    return $wpfrom;
}

function res_fromname($email){
    $wpfrom = 'Tripfab';
    return $wpfrom;
}

add_filter('wp_mail_from', 'res_fromemail');
add_filter('wp_mail_from_name', 'res_fromname');

?>