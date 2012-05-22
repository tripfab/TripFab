/**
 *  Jquery Resize Height Plugin v 0.1
 *
 *	@params
 *	{
 *		head 	  - Jquery selector
 *		foot 	  - Jquery selector
 *		minheight - Integer
 *	}
 *
 *	@author Cristian Araya cristian@tripfab.com
 */

// JavaScript Document
(function( $ ) {
	$.fn.resizeHeight = function(opt) {
		// Reference to this element
		var $this = this;
		
		// Getting the head and foot options
		var $header = $(opt.head);
		var $footer = $(opt.foot);
		
		// Getting the height of the head and foot elements
		var header_height = $header.outerHeight();
		var footer_height = $footer.outerHeight();
		
		function resize(){
			// Getting the window height
			var window_height = $(window).height();
			
			// Calculating the total height not to overflow the min height
			// defined in the options
			var $total = header_height + footer_height + opt.min_height;
			
			// If total is equal or grater than the window height
			if($total >= window_height) {
				// Assign the min height a do not resize the container
				$this.css({
					height:opt.min_height
				});
			} else {
				// Get the appropiate height and resize the container
				var $padding_top = parseInt($this.css("padding-top"),10);
				var $padding_bot = parseInt($this.css("padding-bottom"),10);
				var $height = window_height - ($padding_top + $padding_bot) - (header_height + footer_height);
				
				$this.css({
					height:$height
				});
			}
		}
		
		// Assign a event listener for the window resize event
		$(window).resize(function(){
			resize();
		});
		
		resize();
		
	};
})(jQuery);