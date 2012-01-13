<!--BEGIN #searchform-->
<form method="get" id="searchform" action="<?php echo home_url(); ?>/">
	<label class="hidden" for="s"><?php _e('Search for:', 'framework'); ?></label>
	<input type="text" value="To search type and hit enter" onfocus="if(this.value=='To search type and hit enter')this.value='';" 
onblur="if(this.value=='')this.value='To search type and hit enter';" name="s" id="s" />
	<input class="hidden" type="submit" id="searchsubmit" value="Search" />
<!--END #searchform-->
</form>