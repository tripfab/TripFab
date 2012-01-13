$(function(){
	$('input').ToggleInputValue();
	$('.lb').fancybox({
		overlayColor:'#000',
		overlayOpacity:0.3,
		centerOnScroll:1
	});
	$.ajax({
		url:'/ajax/gettripsearchtags',
		success:function(tags){
			$( "#tripsSearch" ).autocomplete({
				source: tags,
				select:function(e, ui){
					window.location = ui.item.url
				},
				appendTo:'#tripsAutocompleteContainerTop'
			}).data( "autocomplete" )._renderItem = function( ul, item ) {
				return $( "<li></li>" )
				.data( "item.autocomplete", item )
				.append('<a href="'+item.url+'">' + item.label + "</a>")
				.appendTo(ul);
			};
		}
	});	
});