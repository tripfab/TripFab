$(document).ready(function() {
    $('textarea').elastic();
    	
    $('.messages-wrapper .s-message').click(function () {
        if($(this).hasClass('current'))
            return;
		
        $message = $(this);
        $('.messages-wrapper .s-message').removeClass('current');
        $message.addClass('current');
        
        var id = $message.find('input.message_id').val();
        
        $('.loading').text('Loading...').show();
		
        $('body').data('current', id);
		
        $.ajax({
            type:'get',
            url:'/ajax/getconversation/'+id,
            success:function(data){
                refreshConversation(data);
                if($message.hasClass('new')) {
                    $message.removeClass('new');
                    refreshNewsCounter();
                }
                $('.loading').hide();
                $('.waiting').hide();
                $('.conversation').show();
            },
            error:function(){
                $('.loading').text('Something went wrong, Please try later');
            }
        });
    });
	
    function refreshConversation(data){
        var $ul = $('.conversation ul');
        $ul.html('');
        for(i=0; i<data.messages.length; i++){
            var _class  = (i == (data.messages.length - 1)) ? 'last' : '';
            var $li     = $('<li></li>');
            var $div    = $('<div class="info"></div>');
            var $img    = $('<img class="user" width="47" height="47" />');
            var $span   = $('<h4></h4>');
            var $p      = $('<p></p>');
            var $date   = $('<span></span>');
            
            $span.text(data.messages[i].sender);
            $p.html(data.messages[i].text);
            $date.text(data.messages[i].date);
            if(data.messages[i].image != "") 
                $img.attr('src', data.messages[i].image);
            else
                $img.attr('src', "/images/default-profile.gif");
            
            $div.append($span);
            $div.append($p);
            $div.append($date);
            
            $li.append($img);
            $li.append($div);
            
            $li.addClass(_class);
            $ul.append($li);
        }
    }
	
    function refreshNewsCounter(){
        var $news = $('.user-messages .menu li:first a span');		
        if($news.text() == '1')
            $news.remove();
        else{
            var num = parseInt($news.text());
            $news.text(num - 1);
        }
    }
	
    $('#sendmessage').live('click', function(){
        if($('textarea[name=message]').val() == '')
            return false;
		
        $('.loading').text('Sending Message...').show();
		
        $.ajax({
            type:'post',
            url:'/ajax/postmessage',
            data:{
                conversation : $('body').data('current'),
                text         : $('textarea[name=message]').val()
            },
            success:function(data){
                refreshConversation(data);
                $('textarea[name=message]').val('');
                $('textarea[name=message]').css({height:'36px'});
                $('.loading').hide();
                $('.conversation').show();
                $('.waiting').hide();
				
                var count = $('.s-message.current span.number'), aux = parseInt(count.text());
                count.text(aux + 1);
            },
            error:function(){
                $('.loading').text('Something went wrong');
            }
        });
        
        return false;
    });
    
    
    $putFixLimit = $('.user-messages .left').offset().top;
    
    $left = $('.user-messages .right').offset().left;
    
    $(window).scroll(function(e){
        $top = $(window).scrollTop();
        if($top >= $putFixLimit){
            $('.user-messages .right').css({
                position:'fixed',
                left:$left,
                top:0
            });            
        } else {
            $('.user-messages .right').css({
                position:'static'
            });
        }
        resizeConversation();
    });
    
    $(window).resize(resizeConversation);
    
    function resizeConversation(){
        $maxheight = $(window).height();
        $right     = $('.user-messages .right');
        $top       = $(window).scrollTop();
        
        $foot      = $('#footer').offset().top - 14;
        
        $sum = ($top + $maxheight);
        $diff = ($sum >= $foot) ? ($sum - $foot) + 8 : 0;
        
        $form = 144;
        
        //alert($('.user-messages').height());
        
        if($right.css('position') == 'fixed') {
            $height = $maxheight - 28 - $diff;
            $('.waiting, .conversation', $right).css({
                height:$height
            });
            $('.conversation ul', $right).css({
                height:$height - $form
            });
            if($('.user-messages').height() < ($height + 100)) {
                $('.user-messages').css({
                    height:($height + 100)
                });
            }
        } else {
            $height = $maxheight - $putFixLimit - 28 + $top - $diff;
            $('.waiting, .conversation', $right).css({
                height:$height
            });
            $('.conversation ul', $right).css({
                height:$height - $form
            });
            if($('.user-messages').height() < ($height + 100)) {
                $('.user-messages').css({
                    height:($height + 100)
                });
            }
        }
    }
    
    resizeConversation();
    
    $('a.selectAll').click(function(){
        if($(this).hasClass('checked')) {
            $('input.message_id').removeAttr('checked');
            $(this).removeClass('checked');
            $(this).text('Select All');
        } else {
            $('input.message_id').attr('checked', 'checked');
            $(this).addClass('checked');
            $(this).text('Unselect All');
        }
        return false;
    });
    
    $convs = new Array();
    $('a.removeSelected').click(function(){
        $messages = $('input.message_id:checked');
        if($messages.length > 0) {
            if(confirm("Are you sure?")){
                $messages.each(function(){
                    $this = $(this);
                    $msg  = $this.parents('.s-message');
                    $ids   = $this.val();
                    
                    $convs[$ids] = $msg;
                    
                    $.ajax({
                        type:'post',
                        url:'/ajax/removemessages',
                        data:{
                            message:$this.val()
                        },
                        success:function($id){
                            $convs[$id].remove();
                        }
                    })
                });
            }
        }
        return false;
    });
    
    
});