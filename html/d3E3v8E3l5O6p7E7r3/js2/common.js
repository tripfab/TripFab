$(function(){
	$('input:not(.noclean),textarea:not(.noclean)').ToggleInputValue();
	$.ajax({
		url:'/ajax/getsearchtags',
		success:function(tags){
			$( "#citiesSearch" ).autocomplete({
				source: tags,
				select:function(e, ui){
					window.location = ui.item.url
				},
				appendTo:'#citiesAutocompleteContainer'
			}).data( "autocomplete" )._renderItem = function( ul, item ) {
				return $( "<li></li>" )
				.data( "item.autocomplete", item )
				.append( '<a href="'+item.url+'">' + item.label + "</a>" )
				.appendTo( ul );
			};
		}
	});
	
	$('#header .header-login:not(.not)').click(function(){
		$('.dd', this).toggleClass('show');
		return false;
	});
	
	$('#header .links a.amessages').click(function(){
		$(this).next().toggleClass('show');					
		return false;
	});
	
	$('#header .header-login.not a.user').click(function(){
		$(this).next().toggleClass('show');
		return false;
	});
});

function showAlert($message) {
	hideMessage();
	$('.big-message').addClass('green');
	$('.big-message').removeClass('red');
	$('.big-message .wrapper span').text($message);
	$('.big-message').slideDown('fast');
	clearTimeout();
	setTimeout('hideMessage()', 5000);
}
function hideMessage(){
	$('.big-message').slideUp('fast');
	clearTimeout();
}
function showError($message) {
	hideMessage();
	$('.big-message').removeClass('green');
	$('.big-message').addClass('red');
	$('.big-message .wrapper span').text($message);
	$('.big-message').slideDown('fast');
	clearTimeout();
	setTimeout('hideMessage()', 5000);
}

$(function(){
	$('.right .desc a.remove').click(function(){
		$message = $(this).attr('rel');
		$(this).parents('.desc').fadeOut('slow', function(){
			$(this).remove();
		});
		$.ajax({
			url:'/ajax/hidehelpmessage',
			data:{ msg:$message},
			type:'post'
		});
		return false;
	});
});