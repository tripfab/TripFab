$(document).ready(function() {
	$('textarea').elastic();				
	$('input').ToggleInputValue();
	$('form select').jqTransSelect();
	
	$('img.star').click(function () {
		$(this).toggleClass('enabled');
	});
	
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
				$('.conversation').show();
				$('.waiting').hide();
			},
			error:function(){
				$('.loading').hide();
				alert('error');
			}
		});
	});
	
	function refreshConversation(data){
		var $ul = $('.conversation ul'); $ul.html('');
		for(i=0; i<data.messages.length; i++){
			var _class = (i == (data.messages.length - 1)) ? 'last' : '';
			var $li = $('<li></li>'); var $span = $('<strong></strong>'); var $p = $('<p></p>'); var $date = $('<span></span>');
			$span.text(data.messages[i].sender); $p.html(data.messages[i].text); $date.text(data.messages[i].date);
			$li.append($span); $li.append($p); $li.addClass(_class); $li.append($date);
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
				text		 : $('textarea[name=message]').val(),
			},
			success:function(data){
				refreshConversation(data);
				$('textarea[name=message]').val('');
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
	});
});