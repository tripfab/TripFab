		<!-- END #content -->
		</div>
			
		<!-- BEGIN #footer -->
		<div id="footer">
		
			<!-- BEGIN #footer-texture -->
			<div id="footer-texture">
			
				<!-- BEGIN #footer-inner -->
				<div id="footer-inner">
					
                    <!-- BEGIN #footer-columns -->
					<div id="footer-columns" class="clearfix">
                    
                        <!-- BEGIN .column -->
                        <div class="column">
                            
                            <?php	/* Widgetised Area */	if ( !function_exists( 'dynamic_sidebar' ) || !dynamic_sidebar('Footer Area 1') ) ?>
                        
                        <!-- END .column -->
                        </div>
                        
                        <!-- BEGIN .column -->
                        <div class="column">
                            
                            <?php	/* Widgetised Area */	if ( !function_exists( 'dynamic_sidebar' ) || !dynamic_sidebar('Footer Area 2') ) ?>
                        
                        <!-- END .column -->
                        </div>
                        
                        <!-- BEGIN .column -->
                        <div class="column">
                            
                            <?php	/* Widgetised Area */	if ( !function_exists( 'dynamic_sidebar' ) || !dynamic_sidebar('Footer Area 3') ) ?>
                        
                        <!-- END .column -->
                        </div>
                        
                        <!-- BEGIN .column -->
                        <div class="column">
                            
                            <?php	/* Widgetised Area */	if ( !function_exists( 'dynamic_sidebar' ) || !dynamic_sidebar('Footer Area 4') ) ?>
                        
                        <!-- END .column -->
                        </div>
                        
                    </div>
					<!-- END #footer-columns -->
                    
					<p class="copyright clear">&copy; <?php echo date( 'Y' ); ?> <a href="<?php echo home_url(); ?>"><?php bloginfo( 'name' ); ?></a></p>
				
				<!-- END #footer-inner -->
				</div>
			
			<!-- END #footer-texture -->
			</div>
		
		<!-- END #footer -->
		</div>
		
	<!-- END #container -->
	</div> 
		
	<!-- Theme Hook -->
	<?php wp_footer(); ?>
			
<!--END body-->
</body>
<!--END html-->
</html>