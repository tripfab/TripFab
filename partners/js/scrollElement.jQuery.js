(function($){
	$.fn.scrollElement = function(){
		var $sidebar   = this,
			$window    = $(window),
			offset     = $sidebar.offset(),
			topspace = 0;
	    
	    $window.scroll(function() {
	    	if ($window.scrollTop() > offset.top) {
				$sidebar.css({
					position:'fixed',
					left:offset.left,
					top:0
				});
	        }else{
	        	$sidebar.css({
					position:'static',
					left:offset.left,
					top:offset.top
				});
	        }
	    });
	};
})(jQuery);