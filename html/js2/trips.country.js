$(function(){
	var ajax = false;
	$('.slider-wrapper').each(function(index){
		$(this).data('country', $('#hddCountryId').val());
	});
	$( '#slider-1' ).slider({
		step  : 50,
		range : true,
		min   : 0,
		max   : 3000,
		values: [0, 3000],
		slide : function(event, ui ) {
			$('#value-1').text( '$' + ui.values[ 0 ] + ' - $' + ui.values[ 1 ] );
		},
		change:function(event, ui){
			$('.loading').show();
			$( '#slider-1' ).slider('disable');
			
			var $days = $('#slider-2').slider('option','values');
			var $people = $('#slider-3').slider('option','value');
			var $data = {
				country:$('#slider-1').parents('.slider-wrapper').data('country'),
				pricemin:ui.values[0],
				pricemax:ui.values[1],
				daymin:$days[0],
				daymax:$days[1],
				people:$people
			};
			if(ajax != false)
				ajax.abort();
			
			ajax = $.ajax({
				url:'/ajax/gettrips',
				data:$data,
				success:function(response){
					$( '#slider-1' ).slider('enable');
					$('.wrapper.results .content').html(response);
				},
				error:function(){
					$( '#slider-1' ).slider('enable');
					$('.loading').hide();
				}
			});	
		}
	});				
	$('#value-1').text( '$' + $( '#slider-1' ).slider( 'values', 0 ) + ' - $' + $( '#slider-1' ).slider( 'values', 1 ) );
	
	$( '#slider-2' ).slider({
		range: true,
		min: 0,
		max: 30,
		values: [0,30],
		slide: function( event, ui ) {
			$('#value-2').text(ui.values[ 0 ] + ' days - ' + ui.values[ 1 ] + ' days');
		},
		change:function(event, ui){
			$('.loading').show();
			$( '#slider-2' ).slider('disable');
			
			var $prices = $('#slider-1').slider('option','values');
			var $people = $('#slider-3').slider('option','value');
			var $data = {
				country:$('#slider-2').parents('.slider-wrapper').data('country'),
				pricemin:$prices[0],
				pricemax:$prices[1],
				daymin:ui.values[0],
				daymax:ui.values[1],
				people:$people
			};
			if(ajax != false)
				ajax.abort();
			
			ajax = $.ajax({
				url:'/ajax/gettrips',
				data:$data,
				success:function(response){
					$( '#slider-2' ).slider('enable');
					$('.wrapper.results .content').html(response);
				},
				error:function(){
					$( '#slider-2' ).slider('enable');
					$('.loading').hide();
				}
			});	
		}
	});
	$('#value-2').text($( '#slider-2' ).slider( 'values', 0 ) + ' days - ' + $( '#slider-2' ).slider( 'values', 1 ) + ' days');
	
	$( '#slider-3' ).slider({
		value: 0,
		min: 0,
		max: 10,
		slide: function( event, ui ) {
			$('#value-3').text((ui.value > 0) ? ui.value : 'Undefined');
		},
		change:function(event, ui){
			$('.loading').show();
			$( '#slider-3' ).slider('disable');
			
			var $prices = $('#slider-1').slider('option','values');
			var $days = $('#slider-2').slider('option','values');
			
			var $data = {
				country:$('#slider-3').parents('.slider-wrapper').data('country'),
				pricemin:$prices[0],
				pricemax:$prices[1],
				daymin:$days[0],
				daymax:$days[1],
				people:ui.value
			};
			if(ajax != false)
				ajax.abort();
			
			ajax = $.ajax({
				url:'/ajax/gettrips',
				data:$data,
				success:function(response){
					$( '#slider-3' ).slider('enable');
					$('.wrapper.results .content').html(response);
				},
				error:function(){
					$( '#slider-3' ).slider('enable');
					$('.loading').hide();
				}
			});	
		}
	});
	$('#value-3').text(($( '#slider-3' ).slider('value') > 0) ? $( '#slider-3' ).slider('value') : 'Undefined' );
	
	$('.scrollContainer').scrollElement();
});