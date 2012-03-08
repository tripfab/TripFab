/* initialize the external events
-----------------------------------------------------------------*/

$('.mycarousel-1 li, .mycarousel-2 li, .mycarousel-3 li, .mycarousel-4 li, .mycarousel-5 li').each(function() {

	//assigns colors based on the category
	var colorcito = '#000000';
	switch($(this).attr('class')){
		case 'cat-activity'		: colorcito = '#8dc63f'; break;
		case 'cat-entertainment': colorcito = '#a865a8'; break;
		case 'cat-sight'		: colorcito = '#7bc9c5'; break;
		case 'cat-restaurant'	: colorcito = '#f5911c'; break;
		default					: colorcito = '#f37a89'; break;
	}

	// create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
	// it doesn't need to have a start or end
	var eventObject = {
		title: $.trim($(this).text()), // use the element's text as the event title
		className: $(this).attr('class'),
		color: colorcito
	};
	
	// store the Event Object in the DOM element so we can get to it later
	$(this).data('eventObject', eventObject);
	
	// make the event draggable using jQuery UI
	$(this).draggable({
		zIndex: 999,
		revert: true,      // will cause the event to go back to its
		revertDuration: 0,  //  original position after the drag
		refreshPositions: true,
		helper: "clone",
		cursor: "move"
	});
	
	$(this).click(function(){
		alert('asd');
	});
	
});


/* initialize the calendar
-----------------------------------------------------------------*/

jQuery('#calendar').fullCalendar({
	header: {
		left: 'prev,next today',
		center: 'title'
	},
	axisFormat: 'hh:mm tt',
	defaultView: 'agendaWeek',
	editable: true,
	allDaySlot:false,
	droppable: true, // this allows things to be dropped onto the calendar !!!
	year:$year,
	month:$month,
	firstDay:$firstday,
	date:$date,
	events:$listings,
	drop: function(date, allDay) { // this function is called when something is dropped
		
		// retrieve the dropped element's stored Event Object
		var originalEventObject = $(this).data('eventObject');
		
		// we need to copy it, so that multiple events don't have a reference to the same object
		var copiedEventObject = $.extend({}, originalEventObject);
		
		// assign it the date that was reported
		copiedEventObject.start = date;
		copiedEventObject.allDay = false;
		copiedEventObject.listing = $(this).attr('data-id');
		copiedEventObject.city = $(this).attr('data-city');
		copiedEventObject.country = $(this).attr('data-country');
		
		var $start = date.getFullYear() + "-" + (date.getMonth() + 1) + "-" + date.getDate() + " " + date.getHours() + ":" + date.getMinutes() + ":" + date.getSeconds();
		
		$.ajax({
			url:'/ajax/addtotrip',
			data:{
				trip:$trip,
				token:$token,
				listing:copiedEventObject.listing,
				start:$start,
				city:copiedEventObject.city,
				country:copiedEventObject.country
			},
			type:'post',
			success:function(a,b,c,d){
				$('#calendar').fullCalendar('renderEvent', copiedEventObject, true);
			},
			error:function(a,b,c,d){
				copiedEventObject.itemid = 5,
				$('#calendar').fullCalendar('renderEvent', copiedEventObject, true);
			}
		});
	},
	eventDrop:function(e, dayDelta, minuteDelta, allDay, revertFunc, jsEvent, ui, view){
		
		var $start = e.start.getFullYear() + "-" + e.start.getMonth() + "-" + e.start.getDate() + " " + e.start.getHours() + ":" + 
					 e.start.getMinutes() + ":" + e.start.getSeconds();
		
		if(e.end == null){
			var $end = "false";
		} else {
			var $end = e.end.getFullYear() + "-" + e.end.getMonth() + "-" + e.end.getDate() + " " + e.end.getHours() + ":" + 
					   e.end.getMinutes() + ":" + e.end.getSeconds();
		}
		
		$.ajax({
			url:'/ajax/updatetriplisting',
			data:{
				trip:$trip,
				token:$token,
				listing:e.listing,
				start:$start,
				city:e.city,
				country:e.country,
				end:$end,
				itemid:e.itemid
			},
			type:'post',
			success:function(a,b,c,d){
			},
			error:function(a,b,c,d){
			}
		});
	},
	eventResize:function(e, dayDelta, minuteDelta, revertFunc, jsEvent, ui, view){
		var $start = e.start.getFullYear() + "-" + e.start.getMonth() + "-" + e.start.getDate() + " " + e.start.getHours() + ":" + 
					 e.start.getMinutes() + ":" + e.start.getSeconds();
		
		if(e.end == null){
			var $end = "false";
		} else {
			var $end = e.end.getFullYear() + "-" + e.end.getMonth() + "-" + e.end.getDate() + " " + e.end.getHours() + ":" + 
					   e.end.getMinutes() + ":" + e.end.getSeconds();
		}
		
		$.ajax({
			url:'/ajax/updatetriplisting',
			data:{
				trip:$trip,
				token:$token,
				listing:e.listing,
				start:$start,
				city:e.city,
				country:e.country,
				end:$end,
				itemid:e.itemid
			},
			type:'post',
			success:function(a,b,c,d){
			},
			error:function(a,b,c,d){
			}
		});
	}
});

$(window).load(function(){
	var events = jQuery('#calendar').fullCalendar( 'clientEvents');
});