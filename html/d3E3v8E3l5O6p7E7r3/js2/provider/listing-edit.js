$(function(){
	$('#returnEqual').click(function(){
		if($(this).attr('checked') == 'checked'){
			$('#country3').attr('disabled','disabled');
			$('#cities3').attr('disabled','disabled');
		} else {
			$('#country3').removeAttr('disabled');
			$('#cities3').removeAttr('disabled');
		}
	});
	
	$country2 = $('#country2').val();
	$('#country2').change(function(){
		$parent = $(this).val();
		if($parent == $country2)
			return;
		
		$country2 = $parent;
		
		$.ajax({
			url:'/ajax/getplacesoptions',
			data:{ parent_id:$parent },
			type:'post',
			success:function($options){
				$('#cities2').html($options);
			}
		});
	});
	
	$country3 = $('#country3').val();
	$('#country3').change(function(){
		$parent = $(this).val();
		if($parent == $country3)
			return;
		
		$country3 = $parent;
		
		$.ajax({
			url:'/ajax/getplacesoptions',
			data:{ parent_id:$parent },
			type:'post',
			success:function($options){
				$('#cities3').html($options);
			}
		});
	});
	
	$idss = 1;
	$('#addSchedule').click(function(){
		$item = $('.single-schedule.newsch').html();
		$item = $('<div class="single-schedule">'+$item+'</div>');
		$('select[name=start_hour]', $item).attr('name','sch['+$idss+'][start_hour]');
		$('select[name=start_time]', $item).attr('name','sch['+$idss+'][start_time]');
		$('select[name=end_hour]', $item).attr('name','sch['+$idss+'][end_hour]');
		$('select[name=end_time]', $item).attr('name','sch['+$idss+'][end_time]');
		$('select[name=duration]', $item).attr('name','sch['+$idss+'][duration]');
		$('select[name=duration_lb]', $item).attr('name','sch['+$idss+'][duration_lb]');
		$('input[name=name]', $item).addClass('required');
		$('input[name=name]', $item).removeAttr('disabled');
		$('input[name=name]', $item).attr('name','sch['+$idss+'][name]');
		$('input[name=same]', $item).attr('id','sameday'+$idss);
		$('input[name=same]', $item).attr('name','sch['+$idss+'][same]');
		$('label[for=sameday1]').attr('for','sameday'+$idss);
		$('.single-schedule.newsch').before($item);
		$idss++;
		return false;
	});
	
	$('a.remove').live('click', function(){
		$item = $(this).parents('.single-schedule');
		$item.fadeOut('fast', function(){
			$item.remove();
		});
		return false;
	});
	
	$('input#sameSch').live('change',function(){
		if($(this).attr('checked') == 'checked')
		{
			$sch = $(this).parents('.item');
			$open = {
				hour : $('select.start_hour', $sch).val(),
				time : $('select.start_time', $sch).val()};
			$close = {
				hour : $('select.end_hour', $sch).val(),
				time : $('select.end_time', $sch).val()};
			
			$('select.start_hour').val($open.hour);
			$('select.start_time').val($open.time);
			$('select.end_hour').val($close.hour);
			$('select.end_time').val($close.time);
		}
	});
	$('input.closeDay').live('change',function(){
		$sch = $(this).parents('.item');
		if($(this).attr('checked') == 'checked')
		{
			$('select.start_hour', $sch).attr('disabled', 'disabled');
			$('select.start_time', $sch).attr('disabled', 'disabled');
			$('input.start_min', $sch).attr('disabled', 'disabled');
			$('select.end_hour', $sch).attr('disabled', 'disabled');
			$('select.end_time', $sch).attr('disabled', 'disabled');
			$('input.end_min', $sch).attr('disabled', 'disabled');
		}
		else
		{
			$('select.start_hour', $sch).removeAttr('disabled');
			$('select.start_time', $sch).removeAttr('disabled');
			$('input.start_min', $sch).removeAttr('disabled');
			$('select.end_hour', $sch).removeAttr('disabled');
			$('select.end_time', $sch).removeAttr('disabled');
			$('input.end_min', $sch).removeAttr('disabled');
		}
	});
	$('input.sameday').live('change',function(){
		if($(this).attr('checked') == 'checked'){
			$(this).prev('select').attr('disabled','disabled');
		} else {
			$(this).prev('select').removeAttr('disabled');
		}
	});
	
	var $contact;
	$.ajax({
		url:'/ajax/getcontact',
		type:'get',
		success:function(result){
			$contact = result;
		},
		error:function(){
			$contact = {phone:'',website:'',email:''};
		},
	});
	var $old_contact = {
		phone:$('input[name=phone]').val(),
		website:$('input[name=website]').val(),
		email:$('input[name=email]').val(),
	};
	
	$('input[name=contactEqual]').live('change',function(){
		if($(this).attr('checked') == 'checked'){
			$old_contact.phone 	 = $('input[name=phone]').val();
			$old_contact.website = $('input[name=website]').val();
			$old_contact.email   = $('input[name=email]').val();
			
			$('input[name=phone]').attr('disabled','disabled');
			$('input[name=website]').attr('disabled','disabled');
			$('input[name=email]').attr('disabled','disabled');
			
			$('input[name=phone]').val($contact.phone);
			$('input[name=website]').val($contact.website);
			$('input[name=email]').val($contact.email);
		} else {
			$('input[name=phone]').removeAttr('disabled');
			$('input[name=website]').removeAttr('disabled');
			$('input[name=email]').removeAttr('disabled');
			
			$('input[name=phone]').val($old_contact.phone);
			$('input[name=website]').val($old_contact.website);
			$('input[name=email]').val($old_contact.email);
		}
	});
	$('input[name=schedules]').live('change',function(){
		if($(this).hasClass('flex')){
			$('.custom_schedules').fadeOut('fast', function(){
				$('input', this).attr('disabled','disabled');
			});
		} else {
			$('.custom_schedules input:not:(.hidden input)').removeAttr('disabled');
			$('.custom_schedules').fadeIn('fast');
		}
	});
	
	/*
	var $lat = $('input[name=lat]').val();
	var $lng = $('input[name=lng]').val();
	
	var $title = $('input[name=title]').val();
	
	if($lat != '' && $lng != ''){
		var latlng = new google.maps.LatLng($lat,$lng);
		
		var myOptions = {
		  zoom: 12,
		  center: latlng,
		  mapTypeId: google.maps.MapTypeId.ROADMAP,
		};
		
		//var map = new google.maps.Map(document.getElementById("mapcanvas"),myOptions);
		
		var marker = new google.maps.Marker({
			position: latlng,
			title:$title,
			draggable:true
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
		
		var marker = new google.maps.Marker({
			map: map,
			draggable:true
		});
	}
	
	var geocoder = new google.maps.Geocoder();
	
	 google.maps.event.addListener(marker,'dragend', function(){
		var position = marker.getPosition();
		$('input[name=lat]').val(position.lat());
		$('input[name=lng]').val(position.lng());
		$('input, textarea').attr('disabled','disabled');
		geocoder.geocode({'latLng':position}, function(results, status){
			if (status == google.maps.GeocoderStatus.OK){
				$('input, textarea').removeAttr('disabled');
				$('input[name=address]').val(results[0].formatted_address);
			} else {
				$('input, textarea').removeAttr('disabled');
				console.log(status);
			}
		});
	});
	
	$('input#setLocation').click(function(){
		$address =$('input[name=address]').val();
		
		$('input, textarea').attr('disabled','disabled');
		
		// Geo Code the Address
		geocoder.geocode({'address':$address}, function(results, status){
			if (status == google.maps.GeocoderStatus.OK){
				// Enabling form back
				$('input, textarea').removeAttr('disabled');
				// Remove the last result if any
				$('#maplocations ul').html('');
				// Add the new reults to the results menu
				for(i in results){
					var $li = $('<li><a href="'+i+'">'+results[i].formatted_address+'</a></li>');
					$('#maplocations ul').append($li);
				}
				// Bind click event on results menu
				$('#maplocations ul li a').click(function(){
					var target = $(this).attr('href');
					map.setCenter(results[target].geometry.location);
					marker.setPosition(results[target].geometry.location);
					map.setZoom(15);
					$('input[name=address]').val(results[target].formatted_address);
					var position = marker.getPosition();
					$('input[name=lat]').val(position.lat());
					$('input[name=lng]').val(position.lng());
					return false;
				});
			} else {
				// Enabling form back
				$('input, textarea').removeAttr('disabled');
				console.log(status);
			}
		});
		return false;
	});
	*/
});