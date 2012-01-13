<?php

add_action('init','tz_options');

if (!function_exists('tz_options')) {
function tz_options(){
	
// VARIABLES
$themename = get_theme_data(STYLESHEETPATH . '/style.css');
$themename = $themename['Name'];
$shortname = "tz";

// Populate option in array for use in theme
global $tz_options;
$tz_options = get_option('tz_options');

$GLOBALS['template_path'] = TZ_DIRECTORY;

//Access the WordPress Categories via an Array
$tz_categories = array();  
$tz_categories_obj = get_categories('hide_empty=0');
foreach ($tz_categories_obj as $tz_cat) {
    $tz_categories[$tz_cat->cat_ID] = $tz_cat->cat_name;}
$categories_tmp = array_unshift($tz_categories, "Select a category:");    
       
//Access the WordPress Pages via an Array
$tz_pages = array();
$tz_pages_obj = get_pages('sort_column=post_parent,menu_order');    
foreach ($tz_pages_obj as $tz_page) {
    $tz_pages[$tz_page->ID] = $tz_page->post_name; }
$tz_pages_tmp = array_unshift($tz_pages, "Select a page:");       

// Image Alignment radio box
$options_thumb_align = array("alignleft" => "Left","alignright" => "Right","aligncenter" => "Center"); 

// Image Links to Options
$options_image_link_to = array("image" => "The Image","post" => "The Post"); 

//Testing 
$options_select = array("one","two","three","four","five"); 
$options_radio = array("one" => "One","two" => "Two","three" => "Three","four" => "Four","five" => "Five"); 

//Stylesheets Reader
$alt_stylesheet_path = TZ_FILEPATH . '/css/';
$alt_stylesheets = array();

if ( is_dir($alt_stylesheet_path) ) {
    if ($alt_stylesheet_dir = opendir($alt_stylesheet_path) ) { 
        while ( ($alt_stylesheet_file = readdir($alt_stylesheet_dir)) !== false ) {
            if(stristr($alt_stylesheet_file, ".css") !== false) {
                $alt_stylesheets[] = $alt_stylesheet_file;
            }
        }    
    }
}

//More Options
$uploads_arr = wp_upload_dir();
$all_uploads_path = $uploads_arr['path'];
$all_uploads = get_option('tz_uploads');
$other_entries = array("Select a number:","1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16","17","18","19");
$body_repeat = array("no-repeat","repeat-x","repeat-y","repeat");
$body_pos = array("top left","top center","top right","center left","center center","center right","bottom left","bottom center","bottom right");

// Set the Options Array
$options = array();




$options[] = array( "name" => __('General Settings','framework'),
                    "type" => "heading");
                    
$options[] = array( "name" => "",
					"message" => __('Control and configure the general setup of your theme. Upload your preferred logo, setup your feeds and insert your analytics tracking code.','framework'),
					"type" => "intro");
                    
$options[] = array( "name" => __('Enable Plain Text Logo','framework'),
					"desc" => __('Check this to enable a plain text logo rather than an image.','framework'),
					"id" => $shortname."_plain_logo",
					"std" => "false",
					"type" => "checkbox");

$options[] = array( "name" => __('Custom Logo','framework'),
					"desc" => __('Upload a logo for your theme, or specify the image address of your online logo. (http://example.com/logo.png)','framework'),
					"id" => $shortname."_logo",
					"std" => "",
					"type" => "upload");
					
$options[] = array( "name" => __('Welcome Message','framework'),
					"desc" => __('This text will appear at the top of yor theme.','framework'),
					"id" => $shortname."_welcome_message",
					"std" => "",
					"type" => "text");

$options[] = array( "name" => __('Custom Favicon','framework'),
					"desc" => __('Upload a 16px x 16px Png/Gif image that will represent your website\'s favicon.','framework'),
					"id" => $shortname."_custom_favicon",
					"std" => "",
					"type" => "upload");
					
$options[] = array( "name" => __('Contact Form Email Address','framework'),
					"desc" => __('Enter the email address where you\'d like to receive emails from the contact form, or leave blank to use admin email.','framework'),
					"id" => $shortname."_email",
					"std" => "",
					"type" => "text");
					
$options[] = array( "name" => __('FeedBurner URL','framework'),
					"desc" => __('Enter your full FeedBurner URL (or any other preferred feed URL) if you wish to use FeedBurner over the standard WordPress Feed e.g. http://feeds.feedburner.com/yoururlhere','framework'),
					"id" => $shortname."_feedburner",
					"std" => "",
					"type" => "text");

$options[] = array( "name" => __('Tracking Code','framework'),
					"desc" => __('Paste your Google Analytics (or other) tracking code here. It will be inserted before the closing body tag of your theme.','framework'),
					"id" => $shortname."_google_analytics",
					"std" => "",
					"type" => "textarea");                                                    
					
					
					
					
$options[] = array( "name" => __('Styling Options','framework'),
					"type" => "heading");
					
$options[] = array( "name" => "",
					"message" => __('Configure the visual appearance of your theme by selecting a stylesheet if applicable, choosing your overall layout and inserting any custom CSS necessary.','framework'),
					"type" => "intro");

	
$url = TZ_DIRECTORY . '/admin/images/';
$options[] = array( "name" => __('Main Layout','framework'),
					"desc" => __('Select main content and sidebar alignment.','framework'),
					"id" => $shortname."_layout",
					"std" => "layout-2cr",
					"type" => "images",
					"options" => array(
						'layout-2cr' => $url . '2cr.png',
						'layout-2cl' => $url . '2cl.png')
					);
					
$options[] = array( "name" => __('Custom CSS','framework'),
                    "desc" => __('Quickly add some CSS to your theme by adding it to this block.','framework'),
                    "id" => $shortname."_custom_css",
                    "std" => "",
                    "type" => "textarea");
                    


$options[] = array( "name" => __('Menu Options','framework'),
					"type" => "heading");
					
$options[] = array( "name" => "",
					"message" => __('The following options allow you to configure your menus.','framework'),
					"type" => "intro");
					
$options[] = array( "name" => "",
					"message" => __('The navigation settings below will be overwritten if you use WordPress 3.0 custom menus.','framework'),
					"type" => "note");
                    
$options[] = array( "name" => __('Exclude From Primary Navigation','framework'),
					"desc" => __('Enter a comma-separated list of any Category IDs you wish to exclude from the navigation (e.g. 1,5,6,)','framework'),
					"id" => $shortname."_primary_nav_exclude",
					"std" => "",
					"type" => "text");
					
					
					
$options[] = array( "name" => __('Post Options','framework'),
					"type" => "heading");
					
$options[] = array( "name" => "",
					"message" => __('Here you can configure how you would like your posts to function.','framework'),
					"type" => "intro");
					
$options[] = array( "name" => __('Show Featured Image','framework'),
					"desc" => __('Check this to show the featured image at the beginning of the post.','framework'),
					"id" => $shortname."_post_img",
					"std" => "false",
					"type" => "checkbox");
					
$options[] = array( "name" => __('Show Author Bios','framework'),
					"desc" => __('Check this to show an author bio panel on each post page.','framework'),
					"id" => $shortname."_author_bio",
					"std" => "false",
					"type" => "checkbox");
					

update_option('tz_template',$options); 					  
update_option('tz_themename',$themename);   
update_option('tz_shortname',$shortname);

}
}
?>
