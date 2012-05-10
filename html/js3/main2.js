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
	
    $('.w3').hover(function(){
        $('.popUp').css({
            opacity:0,
            display:'block'
        });
        $('.popUp').animate({
            left:'300px',
            opacity:1
        },100,'linear');
    }, function(){
        $('.popUp').animate({
            left:'390px',
            opacity:0
        },100,'linear',function(){
            $(this).css({
                left:'-200px'
            })
            $('.popUp').css({
                opacity:0,
                display:'none'
            });
        });
    });
	
    $('input').ToggleInputValue();
	
    $('a[href^=http]').click( function() {
        window.open(this.href);
        return false;
    });
    
    $endtime   = new Date(2012,4,21,10,0,0,0);
	$today	   = new Date();
	
    $starttime = (($endtime.getTime() - $today.getTime()));
	$days1 = $starttime / 86400000;
	$days  = Math.floor($days1);
	
	$starttime = ($starttime - ($days * 86400000));
	$hours1 = $starttime / 3600000;
	$hours  = Math.floor($hours1);
	
	$starttime = ($starttime - ($hours * 3600000));
	$minutes1 = $starttime / 60000;
	$minutes  = Math.floor($minutes1);
	
	$starttime = ($starttime - ($minutes * 60000));
	$seconds1 = $starttime / 1000;
	$seconds  = Math.floor($seconds1);
	
	$days = ($days > 9) ? $days : '0'+$days;
	$hours = ($hours > 9) ? $hours : '0'+$hours;
	$minutes = ($minutes > 9) ? $minutes : '0'+$minutes;
	$seconds = ($seconds > 9) ? $seconds : '0'+$seconds;
	
	$starttime = $days+':'+$hours+':'+$minutes+':'+$seconds;
	
    $('.counter').countdown({
        image: '/images/digits.png',
        startTime: $starttime
    });
});