<!DOCTYPE html>

<!-- BEGIN html -->
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<!-- An Orman Clark design (http://www.ormanclark.com) - Proudly powered by WordPress (http://wordpress.org) -->

<!-- BEGIN head -->
<head>

	<!-- Meta Tags -->
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
	
	<!-- Title -->
	<title><?php wp_title('|', true, 'right'); ?><?php bloginfo('name'); ?></title>
	
	<!-- Stylesheets -->
	<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
	
	<!-- RSS & Pingbacks -->
	<link rel="alternate" type="application/rss+xml" title="<?php bloginfo( 'name' ); ?> RSS Feed" href="<?php if (get_option('tz_feedburner')) { echo get_option('tz_feedburner'); } else { bloginfo( 'rss2_url' ); } ?>" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
    
    <!-- Theme Hook -->
	<?php wp_head(); ?>
    
    <style type="text/css" media="all">
	html { margin-top: 0px !important; }
	* html body { margin-top: 0px !important; } 
	</style>

<!-- END head -->
</head>

<!-- BEGIN body -->
<body <?php body_class(); ?>>

	<!-- BEGIN #container -->
	<div id="container">
	
		<!-- BEGIN #header -->
		<div id="header">	
			
			<!-- BEGIN .inner -->
			<div class="inner">
                Signup and be one of the first people to join the TripFab private beta: <a href="http://tripfab.com/" target="blank">Signup free here!</a>
			<!-- END .inner -->
			</div>

		<!--END #header-->
		</div>

		<!--BEGIN #content -->
		<div id="content" class="clearfix">
		