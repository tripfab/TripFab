<?php
/*
Template Name: Fullwidth
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
						<?php wp_link_pages(array('before' => '<p><strong>'.__('Pages:', 'framework').'</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
					<!--END .entry-content -->
					</div>

				<!--END .hentry-->
				</div>
				
				<?php comments_template('', true); ?>

				<?php endwhile; endif; ?>
			
			<!--END #primary .hfeed-->
			</div>
			
			<div id="content-btm">&nbsp;</div>
			
			<!-- END #content-wrap -->
			</div>

<?php get_footer(); ?>