<?php get_header(); ?>
			
			<!-- BEGIN #content-wrap -->
			<div id="content-wrap" class="clearfix">
			
				<div id="content-top">&nbsp;</div>
				
				<!--BEGIN #primary .hfeed-->
				<div id="primary" class="hfeed">
				
					<?php if (have_posts()) : ?>			
		
					<?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
                    <?php /* If this is a category archive */ if (is_category()) { ?>
                        <h1 class="page-title"><?php printf(__('All posts in %s', 'framework'), single_cat_title('',false)); ?></h1>
                    <?php /* If this is a tag archive */ } elseif( is_tag() ) { ?>
                        <h1 class="page-title"><?php printf(__('All posts tagged %s', 'framework'), single_tag_title('',false)); ?></h1>
                    <?php /* If this is a daily archive */ } elseif (is_day()) { ?>
                        <h1 class="page-title"><?php _e('Archive for', 'framework') ?> <?php the_time('F jS, Y'); ?></h1>
                     <?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
                        <h1 class="page-title"><?php _e('Archive for', 'framework') ?> <?php the_time('F, Y'); ?></h1>
                    <?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
                        <h1 class="page-title"><?php _e('Archive for', 'framework') ?> <?php the_time('Y'); ?></h1>
                    <?php /* If this is an author archive */ } elseif (is_author()) { ?>
                        <h1 class="page-title"><?php _e('All posts by', 'framework') ?> <?php echo $curauth->display_name; ?></h1>
                    <?php /* If this is a category archive */} elseif (is_search()) { ?>
                    	<h1 class="page-title"><?php _e('Search Results for: ', 'framework'); echo '"'.$_GET['s'].'"'; ?></h1>
                     <?php } ?>
	
					<?php while (have_posts()) : the_post(); ?>
						
						<!--BEGIN .hentry -->
						<div <?php post_class(); ?> id="post-<?php the_ID(); ?>">				
							<h2 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php printf(__('Permanent Link to %s', 'framework'), get_the_title()); ?>"> <?php the_title(); ?></a></h2>
											
							<!--BEGIN .entry-meta .entry-header-->
							<div class="entry-meta entry-header">
								<span><?php _e('by', 'framework') ?></span> <?php the_author(); ?> <span><?php _e('on', 'framework') ?></span> <?php the_time( get_option('date_format') ); ?> <span><?php _e('in', 'framework') ?></span> <?php the_category(', ') ?> <span><?php _e('with', 'framework') ?></span> <?php comments_popup_link(__('No comments', 'framework'), __('1 Comment', 'framework'), __('% Comments', 'framework')); ?> <?php edit_post_link( __('edit', 'framework'), '<span class="edit-post">[', ']</span>' ); ?><a href="http://twitter.com/share" class="twitter-share-button" data-url="<?php the_permalink(); ?>" data-text="<?php bloginfo('name'); ?>: <?php the_title(); ?>," data-count="horizontal">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
							<!--END .entry-meta entry-header -->
							</div>
							
							<?php /* if the post has a WP 2.9+ Thumbnail */
							if ( (function_exists('has_post_thumbnail')) && (has_post_thumbnail()) ) : ?>
							<div class="post-thumb post-lead">
								<a title="<?php printf(__('Permanent Link to %s', 'framework'), get_the_title()); ?>" href="<?php the_permalink(); ?>"><?php the_post_thumbnail('archive-thumb'); /* post thumbnail settings configured in functions.php */ ?></a>
							</div>
							<?php endif; ?>
		
							<!--BEGIN .entry-content -->
							<div class="entry-content">
								<?php the_content(__('Continue reading &rarr;', 'framework')); ?>
							<!--END .entry-content -->
							</div>
		                
						<!--END .hentry-->  
						</div>
		
						<?php endwhile; ?>
		
					<!--BEGIN .navigation .page-navigation -->
					<div class="navigation page-navigation clearfix">
						<?php if(function_exists('wp_pagenavi')) { wp_pagenavi(); } else { ?>
						<div class="nav-next"><?php next_posts_link(__('&larr; Older Entries', 'framework')) ?></div>
						<div class="nav-previous"><?php previous_posts_link(__('Newer Entries &rarr;', 'framework')) ?></div>
						<?php } ?>
					<!--END .navigation ,page-navigation -->
					</div>
		
					<?php else : ?>
		
					<!--BEGIN #post-0-->
					<div id="post-0" <?php post_class(); ?>>
					
						<h2 class="entry-title"><?php _e('Error 404 - Not Found', 'framework') ?></h2>
					
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