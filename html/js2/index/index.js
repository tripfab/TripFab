$(document).ready(function() {
	$('input, textarea').ToggleInputValue();
	//$('select').jqTransSelect();
	$('select[name=countries]').change(function(){
		var $id = $(this).val();
		$.ajax({
			url:'/ajax/getindexcities',
			data:{id:$id},
			success:function(cities){
				$('.home-cities .cities').html(cities);
			}			
		});
	});
});
$(function(){
	$.ajax({
		url:'/ajax/getsearchtags',
		success:function(tags){
			$( "#tags" ).autocomplete({
				source:tags,
				select:function(e, ui){
					window.location = ui.item.url
				}
			}).data( "autocomplete" )._renderItem = function( ul, item ) {
				return $( "<li></li>" )
				.data( "item.autocomplete", item )
				.append( '<a href="'+item.url+'">' + item.label + "</a>" )
				.appendTo( ul );
			};
		}
	});	
});