		<!--BEGIN #sidebar .aside-->
		<div id="sidebar" class="aside">
		
			<!-- BEGIN #logo -->
			<div id="logo">
				<?php /*
				If "plain text logo" is set in theme options then use text
				if a logo url has been set in theme options then use that
				if none of the above then use the default logo.png */
				if (get_option('tz_plain_logo') == 'true') { ?>
				<h1><a href="<?php echo home_url(); ?>"><?php bloginfo( 'name' ); ?></a></h1>
				<p id="tagline"><?php bloginfo( 'description' ); ?></p>
				<?php } elseif (get_option('tz_logo')) { ?>
				<a class="logo-link" href="<?php echo home_url(); ?>"><img src="<?php echo get_option('tz_logo'); ?>" alt="<?php bloginfo( 'name' ); ?>"/></a>
				<?php } else { ?>
				<a class="logo-link" href="<?php echo home_url(); ?>"><img src="<?php echo get_template_directory_uri(); ?>/images/logo.png" alt="<?php bloginfo( 'name' ); ?>" width="190" height="190" /></a>
				<?php } ?>
			<!-- END #logo -->
			</div>
			
			<div class="widget clearfix social">
				<ul>
					<li>
						<a href="https://twitter.com/#!/TripFab" target="blank"><img src="<?php echo get_template_directory_uri(); ?>/images/ico-1.png" alt="" /></a>
						<span><?php echo getFollowers("tripfab"); ?> </span>
						<strong>Followers</strong>
					</li>
					<li>
						<a href="https://www.facebook.com/TripFab" target="blank"><img src="<?php echo get_template_directory_uri(); ?>/images/ico-2.png" alt="" /></a>
						<span>
							<?php
								$info = json_decode(file_get_contents('http://graph.facebook.com/tripfab'));
								echo $info->likes;
							 ?>
						</span>
						<strong>Fans</strong>
					</li>
					<li class="last">
						<a href="http://feeds.feedburner.com/tripfab" target="blank"><img src="<?php echo get_template_directory_uri(); ?>/images/ico-3.png" alt="" /></a>
						<span>
							<img src="http://feeds.feedburner.com/~fc/tripfab?bg=99CCFF&amp;fg=444444&amp;anim=0&amp;label=listeners" height="26" width="88" style="border:0" alt="" />
						</span>
					</li>
				</ul>
			</div>
			<!-- closes widget -->
            
            <?php if(!is_page()) : ?>
			<?php	/* Widgetised Area */	if ( !function_exists( 'dynamic_sidebar' ) || !dynamic_sidebar()) ?>
			<?php else: ?>
            <?php	/* Widgetised Area */	if ( !function_exists( 'dynamic_sidebar' ) || !dynamic_sidebar('Page sidebar')) ?>
            <?php endif; ?>
		
		<!--END #sidebar .aside-->
		</div>