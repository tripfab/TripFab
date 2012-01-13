$(function(){
	
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
		
		var map = new google.maps.Map(document.getElementById("mapcanvas"),myOptions);
		
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
				$('ul#maplocations').html('');
				// Add the new reults to the results menu
				for(i in results){
					var $li = $('<li><a href="'+i+'">'+results[i].formatted_address+'</a></li>');
					$('ul#maplocations').append($li);
				}
				$('.w-m').fadeIn('slow');
				// Bind click event on results menu
				$('ul#maplocations li a').click(function(){
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
});