$(document).ready(function() {
	$('#mapcanvas').data('lat', $('input[name=listlat]').val());
	$('#mapcanvas').data('lng', $('input[name=listlng]').val());
	$('body').data('listing_title', $('input[name=listtitle]').val());
	
	var disableddates = $('input[name=diableddates]').val().split(',');
	$('body').data('disableddates', disableddates);
	
	$('body').data('listingid', $('input[name=listingids]').val());
	$('body').data('listingtoken', $('input[name=listingstoken]').val());
	$('body').data('listingprice', $('input[name=listingprice]').val());
	
	$('input, textarea').ToggleInputValue();
	$('.tabs-wrapper-2, .reviews-wrapper').tabs({
		selected:0,
		show:function(a,b){
			if(b.tab.text == 'How to Get There'){
				var $lat = $('#mapcanvas').data('lat');
				var $lng = $('#mapcanvas').data('lng');
				
				var $title = $('body').data('listing_title');
				
				if($lat != 'none' && $lng != 'none'){
					var latlng = new google.maps.LatLng($lat,$lng);
					
					var myOptions = {
					  zoom: 12,
					  center: latlng,
					  mapTypeId: google.maps.MapTypeId.ROADMAP,
					};
					
					var map = new google.maps.Map(document.getElementById("mapcanvas"),myOptions);
					
					var marker = new google.maps.Marker({
						position: latlng,
						title:$title,
					}); 
					marker.setMap(map);
				} else {
					var latlng = new google.maps.LatLng(40,0);
					
					var myOptions = {
					  zoom: 2,
					  center: latlng,
					  mapTypeId: google.maps.MapTypeId.ROADMAP,
					};
					var map = new google.maps.Map(document.getElementById("mapcanvas"),myOptions);
				}
			}
		}
	});
	$('textarea').elastic();
	//$('select').jqTransSelect();
	
	$('li#acordion').accordion({ 
		autoHeight: false,
		animated:false
	});
	
	var $disableddates = $('body').data('disableddates');
	
	$( "#datepicker").datepicker({
		showOtherMonths: true,
		beforeShowDay:function(date){
			var date = $.datepicker.formatDate('mm-dd-yy', date);
			return ($.inArray(date,$disableddates) != -1) ? [false] : [true]; 
		},
		minDate:new Date(),
		maxDate:'+1y',
		onSelect:function(date, inst){
			var aux = new Date(date), aux2 = new Date(aux.getTime() + 86400000);
			var $date = $.datepicker.formatDate('M d, yy', new Date(date));
			$('li#acordion').accordion('activate',1);
			$('#checkinlabel').text($date);
			$('#inputCheckin').val($date);
			refreshPrice();
		}
	});
	
	$('input[name=option]').click(refreshPrice);
	$('select[name=kids]').change(refreshPrice);
	$('select[name=adults]').change(refreshPrice);
});

$(function(){
	$('.listing-gallery ul li a').click(function(){
		if($(this).hasClass('active'))
			return false;
		
		$('.listing-gallery ul li.active').removeClass('active');
		$(this).parent().addClass('active');
		
		$('.listing-gallery .img-wrapper.active').removeClass('active');
		$($(this).attr('href'), '.listing-gallery').addClass('active');
		
		return false;
	});
});

$(function(){
	$('.qa .qa-box .questions a.btn-3').click(function(){
		$(this).parent().parent().parent().find('div.message').toggleClass('show');
		return false;
	});
});

function refreshPrice()
{
	var $data = {
		checkin	 : $('#inputCheckin').val(),
		option	 : $('input[name=option]:checked').val(),
		adults	 : $('select[name=adults]').val(),
		kids	 : $('select[name=kids]').val(),
		id		 : $('body').data('listingid'),
		token	 : $('body').data('listingtoken'),
		price	 : $('body').data('listingprice'),
	};
	
	$.ajax({
		url:'/ajax/getquote',
		data:$data,
		success:function(price){
			$('.summary span strong').text(price);
		}
	});
}

$(function(){
	$('.listing-gallery .listing-ttl .been').live('click', function(){
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

$(function(){
	$('a.lb').fancybox({
		padding:0,
		overlayColor:'#000',
		centerOnScroll:1,
	});
	
	$('#sendMessageForm').submit(function(){
		$form = $(this);
		$data = {
			message:$('textarea[name=message]', $form).val(),
			vendor:$('input[name=vendor]', $form).val(),
		};
		if($data.message == ''){
			alert('Enter a message');
			return false;
		}
		
		$.ajax({
			url:'/ajax/sendmsgvendor',
			data:$data,
			type:'post',
			success:function(response){
				$.fancybox.close();
				if(response.type == 'success'){
					showAlert(response.message);
					$('textarea[name=message]', $form).val('');
				} else {
					showError(response.message);
					$('textarea[name=message]', $form).val('');
				}
			},
			error:function(){
				$.fancybox.close();
				showError('Something went wrong, Please try again later');
				$('textarea[name=message]', $form).val('');
			}
		});
		return false;
	});
	
	$('#showQuestionForm').click(function(){
		$('#askQuestion').removeClass('hidden');
		return false;
	});
	
	$('#askQuestionForm .btn-4').click(function(){
		$('#askQuestion').addClass('hidden');
		return false;
	});
	
	$('#askQuestionForm').submit(function(){
		$form = $(this);
		$data = {
			question : $('textarea[name=question]', $form).val(),
			listing  : $('input[name=listing]', $form).val()
		};
		if($data.question == ''){
			showError('Write your question');
			return false;
		}
		$.ajax({
			url:'/ajax/postquestion',
			type:'post',
			data:$data,
			success:function(response){
				if(response.type == 'success'){
					showAlert(response.message);
					$('textarea[name=question]', $form).val('');
					$('#askQuestion').parents('.qa').after(response.html);
					$('#askQuestion').addClass('hidden');
				} else {
					showError(response.message);
				}
			},
			error:function(){
				showError('Something went worng, Please try later');
			}
		});
		
		return false;
	});
	
	$('#booking').submit(function(){
		$ready = false;
		$vals = {
			checkin:$('input[name=checkin]', $(this)).val(),
			option:$('input[name=option]:checked', $(this)).val(),
		};
		
		if($vals.checkin != '' && $vals.option != undefined)
			$ready = true;
		
		if(!$ready){
			showError('Please Fill out the form first');
			return false;
		}
	});
	
});
