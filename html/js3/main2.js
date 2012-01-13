$.fn.ToggleInputValue = function(){
    return $(this).each(function(){
        var Input = $(this);
        var default_value = Input.val();

        Input.focus(function() {
           if(Input.val() == default_value) Input.val("");
        }).blur(function(){
            if(Input.val().length == 0) Input.val(default_value);
        });
    });
}


$(document).ready(function() {
	
	$('input.send').click(function() {	
		if ($('.email-form').valid()) {
			var email = $('.email-form').serialize();
			$.ajax({
			  type: 'POST',
			  url: 'update.php',
			  data: email,
			  success: function() {
			    $('.email-form').fadeOut('slow', function(){
					$('.message-sent').fadeIn('slow');
				});
			  }
			});
			return false;
		} else {
			$('input.email').focus();
			return false;
		}
	});
	
	$('#footer').hover(function(){
		$('.popUp').css({
			opacity:0,
			display:'block',
		});
		$('.popUp').animate({
			left:'95px',
			opacity:1,
		},100,'linear');
	}, function(){
		$('.popUp').animate({
			left:'390px',
			opacity:0,
		},100,'linear',function(){
			$(this).css({left:'-200px'})
			$('.popUp').css({
				opacity:0,
				display:'none',
			});
		});
	});
	
	$('input').ToggleInputValue();
	
	$('a[href^=http]').click( function() {
		window.open(this.href);
		return false;
	});
	  
});