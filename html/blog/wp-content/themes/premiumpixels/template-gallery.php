<?php
/*
Template Name: Gallery
*/
?>


<?php get_header(); ?>

			<!-- BEGIN #content-wrap -->
			<div id="content-wrap" class="clearfix">
			
				<div id="content-top">&nbsp;</div>
			
			<!--BEGIN #primary .hfeed-->
			<div id="primary" class="hfeed">
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

				<!--BEGIN .hentry-->
				<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
					<h1 class="entry-title"><?php the_title(); ?></h1>

					<!--BEGIN .entry-content -->
					<div class="entry-content">
						<?php the_content(); ?>
					<!--END .entry-content -->
					</div>

				<!--END .hentry-->
				</div>

				<?php endwhile; endif; ?>
				
				<!-- BEGIN .gallery-wrap -->
				<div class="gallery-wrap">
					
					<?php 
					
                    $query = new WP_Query();
                    $query->query('posts_per_page=-1');					
                    while ($query->have_posts()) : $query->the_post(); 
					
                    ?>
                
                        <!--BEGIN .hentry -->
                        <div <?php post_class(); ?> id="post-<?php the_ID(); ?>">
							
							<?php /* if the post has a WP 2.9+ Thumbnail */
							if ( (function_exists('has_post_thumbnail')) && (has_post_thumbnail()) ) : ?>
							<div class="post-thumb post-lead">
								<a title="<?php printf(__('Permanent Link to %s', 'framework'), get_the_title()); ?>" href="<?php the_permalink(); ?>"><?php the_post_thumbnail('gallery-thumb'); /* post thumbnail settings configured in functions.php */ ?></a>
							</div>
							<?php endif; ?>
                        
                        <!--END .hentry-->  
                        </div>
                    
                	<?php endwhile; wp_reset_query(); ?>
					
				<!-- END .gallery-wrap -->
				</div>
				
			
			<!--END #primary .hfeed-->
			</div>
			
			<div id="content-btm">&nbsp;</div>
			
			<!-- END #content-wrap -->
			</div>
			
<?php get_sidebar(); ?>

<?php get_footer(); ?>