<?php get_header(); ?>

			<!-- BEGIN #content-wrap -->
			<div id="content-wrap" class="clearfix">
			
				<div id="content-top">&nbsp;</div>
			
				<!--BEGIN #primary .hfeed-->
				<div id="primary" class="hfeed">
				<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
					
					<!--BEGIN .hentry -->
					<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
						<h1 class="entry-title"><?php the_title(); ?></h1>
						
						<!--BEGIN .entry-meta .entry-header-->
						<div class="entry-meta entry-header">
							<span><?php _e('by', 'framework') ?></span> <?php the_author(); ?> <span><?php _e('on', 'framework') ?></span> <?php the_time( get_option('date_format') ); ?> <span><?php _e('in', 'framework') ?></span> <?php the_category(', ') ?> <span><?php _e('with', 'framework') ?></span> <?php comments_popup_link(__('No comments', 'framework'), __('1 Comment', 'framework'), __('% Comments', 'framework')); ?> <?php edit_post_link( __('edit', 'framework'), '<span class="edit-post">[', ']</span>' ); ?><a href="http://twitter.com/share" class="twitter-share-button" data-url="<?php the_permalink(); ?>" data-text="<?php bloginfo('name'); ?>: <?php the_title(); ?>," data-count="horizontal">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
						<!--END .entry-meta entry-header -->
						</div>
						
						<?php /* if the post has a WP 2.9+ Thumbnail */
							if ( (function_exists('has_post_thumbnail')) && (has_post_thumbnail()) && get_option('tz_post_img') == 'true' ) : ?>
							<div class="post-thumb post-lead">
								<?php the_post_thumbnail('archive-thumb'); /* post thumbnail settings configured in functions.php */ ?>
							</div>
							<?php endif; ?>
						
						<!--BEGIN .entry-content -->
						<div class="entry-content">
							<?php the_content(__('Read more...', 'framework')); ?>
							<?php wp_link_pages(array('before' => '<p><strong>'.__('Pages:', 'framework').'</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
						<!--END .entry-content -->
						</div>
						
                        <?php if(get_option('tz_author_bio') == 'true') : ?>
                        
						<!--BEGIN .author-bio-->
						<div class="author-bio">
							
							<!-- BEGIN .author-inner -->
							<div class="author-inner clearfix">

								<!-- BEGIN .author-wrap -->
								<div class="author-wrap clearfix">
								
									<?php echo get_avatar( get_the_author_meta('email'), '70' ); ?>
									
									<div class="author-info">
										<div class="author-title"><?php the_author_link(); ?></div>
										<div class="author-description"><?php the_author_meta('description'); ?></div>
									</div>
								
								<!-- END .author-wrap -->
								</div>
							
							<!-- END .author-inner -->
							</div>
							
						<!--END .author-bio-->
						</div>
						
                        <?php endif; ?>
	
						<!--BEGIN .entry-meta .entry-footer-->
						<div id="single-tags" class="entry-meta entry-footer">
	                        <span><?php _e('Tagged:', 'framework') ?></span> <?php the_tags('',', ',''); ?>
						<!--END .entry-meta .entry-footer-->
						</div>
	                
	                <!--END .hentry-->  
					</div>
	
					<?php comments_template('', true); ?>
					
					<!--BEGIN .navigation .single-page-navigation -->
					<div class="navigation single-page-navigation clearfix">
						<div class="nav-previous"><?php previous_post_link('&larr; %link') ?></div>
						<div class="nav-next"><?php next_post_link('%link &rarr;') ?></div>
					<!--END .navigation .single-page-navigation -->
					</div>
	
					<?php endwhile; else: ?>
	
					<!--BEGIN #post-0-->
					<div id="post-0" <?php post_class() ?>>
					
						<h1 class="entry-title"><?php _e('Error 404 - Not Found', 'framework') ?></h1>
					
						<!--BEGIN .entry-content-->
						<div class="entry-content">
							<p><?php _e("Sorry, but you are looking for something that isn't here.", "framework") ?></p>
							<?php get_search_form(); ?>
						<!--END .entry-content-->
						</div>
					
					<!--END #post-0-->
					</div>
	
				<?php endif; ?>
				<!--END #primary .hfeed-->
				</div>
			
			<div id="content-btm">&nbsp;</div>
			
			<!-- END #content-wrap -->
			</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>