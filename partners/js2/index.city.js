$(document).ready(function() {
	$('input').ToggleInputValue();
});

$(function(){	
	$.ajax({
		url:'/ajax/getsearchtags',
		success:function(tags){
			$( "#citiesSearch2" ).autocomplete({
				source: tags,
				select:function(e, ui){
					window.location = ui.item.url
				},
				appendTo:'#citiesAutocompleteContainer2'
			}).data( "autocomplete" )._renderItem = function( ul, item ) {
				return $( "<li></li>" )
				.data( "item.autocomplete", item )
				.append( '<a href="'+item.url+'">' + item.label + "</a>" )
				.appendTo( ul );
			};
		}
	});
});

$(function(){	
	var ajax = false;
	var refresh_price = true;
	
	$('ul.cat-menu li').each(function(index){
		var $cat = $('a', this).data('cat');
		$('a', this).data('category', $cat);
		$('a', this).data('country', $('#hddCountryId').val());
		$('a', this).data('city', $('#hddCityId').val());
	});
	
	$('ul.cat-menu li a').live('click', function(){
		
		$rel = $(this).attr('href');
		$('.widget.destinations').hide();
		$('.widget[rel='+$rel+']').show();
		
		var $a = $(this);
		var $data = {category :$(this).data('category'),
					 country  :$(this).data('country'),
					 city	  :$(this).data('city')};
			
		if($a.parents('li').hasClass('active'))
			return false;
		
		if(ajax != false)
			ajax.abort();
			
		$('.results-wrapper .loading').show();
		ajax = $.ajax({
			url:'/ajax/getlistings',
			data:$data,
			success:function(results){
				//refresh_price = false;
				$('.results-wrapper .loading').hide();
				$('ul.cat-menu li.active').removeClass('active');
				$a.parents('li').addClass('active');
				$('.results-wrapper').html(results);
				$( '#slider-4' ).slider('values',[0,3000]);
				$('#value-4').text( '$' + $( '#slider-4' ).slider( 'values', 0 ) + ' - $' + $( '#slider-4' ).slider( 'values', 1 ) );
				//refresh_price = true;
			},
			error:function(){
				$('.results-wrapper .loading').hide();
			}
		});
		
		return false;
	});
	
	//$('.scrollcontainer').scrollElement();
	
	$( '#slider-4' ).slider({
		step: 50,
		range: true,
		min: 0,
		max: 3000,
		values: [ 0, 3000 ],
		slide: function( event, ui ) {
			$('#value-4').text( '$' + ui.values[ 0 ] + ' - $' + ui.values[ 1 ] );
		},
		change:function(event, ui) {
			var $a = $('ul.cat-menu li.active a');
			var $data = {category :$a.data('category'),
						 country  :$a.data('country'),
						 city	  :$a.data('city'),
						 pricemin :ui.values[0],
						 pricemax :ui.values[1]};
			
			if(ajax != false)
				ajax.abort();
				
			$('.results-wrapper .loading').show();
			$( '#slider-4' ).slider('disable');
			ajax = $.ajax({
				url:'/ajax/getlistings',
				data:$data,
				success:function(results){
					$('.results-wrapper .loading').hide();
					$( '#slider-4' ).slider('enable');
					$('ul.cat-menu li.active').removeClass('active');
					$a.parents('li').addClass('active');
					$('.results-wrapper').html(results);
				},
				error:function(){
					$('.results-wrapper .loading').hide();
					$( '#slider-4' ).slider('enable');
				}
			});
		}
	});
	$('#value-4').text( '$' + $( '#slider-4' ).slider( 'values', 0 ) + ' - $' + $( '#slider-4' ).slider( 'values', 1 ) );
});

$(function(){
	$('.result .img-wrapper .been').live('click', function(){
		$('.dd-2').removeClass('show');
		$(this).parent().find('.dd-2').toggleClass('show');
		return false;
	});
	
	$('.dd-2 select').live('change', function(){
		if($(this).val() == 'new'){
			$(this).next('input').removeClass('hidden');
		} else {
			$(this).next('input').addClass('hidden');
		}
	});
	
	$('.addtotrip .btn-4').live('click', function(){
		$(this).parents('.dd-2').removeClass('show');
		$('.addtotrip input[type=text]').addClass('hidden');
		$('.addtotrip select option:first').attr('selected','selected');
		return false;
	});
	
	$('.addtotrip').live('submit', function(){
		$form = $(this);
		$data = {
			listing:$('input[name=listing]', $form).val(),
			trip:$('select[name=trip]', $form).val(),
			title:$('input[name=title]', $form).val(),
		};
		
		if($data.trip == '')
			return false;
			
		$('input, select').attr('disabled','disabled');
		$.ajax({
			url:'/ajax/addtotrip2',
			data:$data,
			type:'post',
			success:function(response){
				if(response.type == 'success'){
					$('input, select').removeAttr('disabled');
					$('.dd-2').removeClass('show');
					showAlert(response.message);
				} else if(response.type == 'newtrip'){
					$('input, select').removeAttr('disabled');
					$('.dd-2').removeClass('show');
					showAlert(response.message);
					$('.dd-2 select').append('<option value="'+response.tripid+'">'+response.triptitle+'</option>');
				} else {
					$('input, select').removeAttr('disabled');
					showError(response.message);
				}
			},
			error:function(){
				$('input, select').removeAttr('disabled');
				showError('Somehing went wrong please try later');
			}
		});
		return false;
	});
});