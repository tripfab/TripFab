<?php
header("HTTP/1.1 404 Not Found");
header("Status: 404 Not Found");
?>
<?php get_header(); ?>
			
			<!-- BEGIN #content-wrap -->
			<div id="content-wrap" class="clearfix">
			
				<div id="content-top">&nbsp;</div>
				
				<!--BEGIN #primary .hfeed-->
				<div id="primary" class="hfeed">
				
					<!--BEGIN #post-0-->
					<div id="post-0" <?php post_class() ?>>
						
						<h1 class="entry-title"><?php _e('Error 404 - Not Found', 'framework') ?></h1>
						
						<!--BEGIN .entry-content-->
						<div class="entry-content">
							<p><?php _e("Sorry, couldn't find that one!", "framework") ?></p>
							<?php get_search_form(); ?>
						<!--END .entry-content-->
						</div>
						
					<!--END #post-0-->
					</div>
					
				<!--END #primary .hfeed-->
				</div>
			
				<div id="content-btm">&nbsp;</div>
			
			<!-- END #content-wrap -->
			</div>
 
<?php get_sidebar(); ?>

<?php get_footer(); ?>