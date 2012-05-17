$(document).ready(function() {
	
	function onAfter(curr, next, opts, fwd) {
	var index = opts.currSlide;
	//get the height of the current slide
	var $ht = $(this).height();
	//set the container's height to that of the current slide
	$(this).parent().animate({
		height: $ht
	});
	}
	
	$("a.lb, a.lbc").fancybox({
	    padding: 0,
	    showCloseButton: true,
	    centerOnScroll: true,
	    overlayOpacity: '0.7',
	    titlePosition: 'inside',
	    overlayColor: '#FFF'
	});
	$('#fancybox-title').addClass('tittle');
	$('#fancybox-left-ico').addClass('lfarrow').css('display', 'block');
	$('#fancybox-right-ico').addClass('rgarrow');
	
    $('#slider-1').cycle({
    	fx:     'cover',
    	next: 	'#next-1',
    	prev: 	'#prev-1',
    	pager:	'#cycle-pager-1',
    	timeout:0,
    	after: onAfter
    });
    $('#slider-2').cycle({
    	fx:     'cover',
    	next: 	'#next-2',
    	prev: 	'#prev-2',
    	pager:	'#cycle-pager-2',
    	timeout:0,
    	after: onAfter
    });
    $('#slider-3').cycle({
    	fx:     'cover',
    	next: 	'#next-3',
    	prev: 	'#prev-3',
    	pager:	'#cycle-pager-3',
    	timeout:0,
    	after: onAfter
    });
    $('#slider-4').cycle({
    	fx:     'cover',
    	next: 	'#next-4',
    	prev: 	'#prev-4',
    	pager:	'#cycle-pager-4',
    	timeout:0,
    	after: onAfter
    });
    $('#slider-5').cycle({
    	fx:     'cover',
    	next: 	'#next-5',
    	prev: 	'#prev-5',
    	pager:	'#cycle-pager-5',
    	timeout:0,
    	after: onAfter
    });
    
    $('.days-cycle').cycle({
    	fx: 'cover',
    	next: '.next',
    	prev: '.prev',
    	timeout: 0,
    	after: onAfter
    });
    
    $('.trip-gallery').cycle({
        pager: '#pager'
    });
    
    $('.tabs-wrapper').tabs({
    	autoHeight: true
    });
    $('ul.days').jcarousel({ 
        scroll: 1,
        buttonPrevHTML: '<div><span>Previous Day</span></div><div><span>Previous Day</span></div>',
        buttonNextHTML: '<div><span>Next Day</span></div><div><span>Next Day</span></div>'
    });
    
    var aux = true;
    
    function showMap($el) {
        $canvas  = $el.attr('href');
        var $lat = $el.data('lat');
        var $lng = $el.data('lng');
        var $title = $el.data('title');

        var latlng, myOptions, map, marker;

        if($lat != 'none' && $lng != 'none'){
            latlng = new google.maps.LatLng($lat,$lng);

            myOptions = {
                zoom: 12,
                center: latlng,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };

            map = new google.maps.Map(document.getElementById($canvas),myOptions);

            marker = new google.maps.Marker({
                position: latlng,
                title:$title
            }); 
            marker.setMap(map);
        } else {
            latlng = new google.maps.LatLng(40,0);

            myOptions = {
                zoom: 2,
                center: latlng,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };
            map = new google.maps.Map(document.getElementById("mapcanvas"),myOptions);
        }
        $el.data('done',true);
    }
    
    $aaa = false;
    $('.box.days .day .day-section .left > a').live('click', function(){
        $('.mapCanvas').removeClass('hidden');
        $(this).parent().find('.mapCanvas').toggleClass('show');
        $aaa = $(this).data('aaa');
        if(!$aaa) {
            $(this).html('Hide Map <span>&darr;</span>');
            $(this).data('aaa', true);
            if(!$(this).data('done')) {
                showMap($(this));
            }
        } else {
            $(this).html('Show Map <span>&uarr;</span>');
            $(this).data('aaa', false);
        }
        return false;       	
    });
    
    
   function selectDays(lastDay) {
   	
   	var days = parseInt($('#tripDates').val());
   	var aux2;
   	for(var u = 0; u < days; u++) {
   		if (u == 0 ) {
   			aux2 = lastDay;
   			lastDay = lastDay.next();
   			if (lastDay.length == 0) {
   				lastDay = aux2.parents('tr').next().children().eq(0);
   			}
   		}else {
   			$('a',lastDay).addClass('selected');
   			aux2 = lastDay;
   			lastDay = lastDay.next();
   			if (lastDay.length == 0) {
   				lastDay = aux2.parents('tr').next().children().eq(0);
   			}
   		}
   	}
   }
    
   
    
    
    
        
    $('#datepicker').wrap('<div id="calendar" class="trips"></div>');
    
    $("#datepicker").DatePicker({
        flat: true,
        date: '2008-07-31',
        calendars: 1,
        mode:'range',
        starts: 1,
        onChange:function(date){
            var days = parseInt($('#tripDates').val());
            var Start = new Date(date[0]);
            var End   = new Date(Start.getTime() + ((days + 1) * 24 * 60 * 60 * 1000));
            
            var dates = new Array();
            dates[0] = date[0];
            dates[1] = End;
            
            $('input[name=checkin]').val(date[0]);
            
            $('#datepicker').DatePickerSetDate(dates);
        }
    });
    
    
    
    
    $( "#datepickers" ).datepicker({ 
        beforeShowDay:function(date){
            if(aux)
            {
                $date = $.datepicker.formatDate('D M d yy', $("#datepicker").datepicker('getDate'));
                $('input[name=checkin]').val($date);
            }
            aux = false;
            return [true, ''];
        },
        dateFormat:'D M d yy',
        showOtherMonths: true,
        minDate:new Date(),
        onSelect:function(date, inst){
            $('input[name=checkin]').val(date);
            inst.inline = false;
            $(".ui-datepicker-calendar tbody a").removeClass('selected');
            $(".ui-datepicker-calendar tbody a").each(function(){
              if ($(this).text() == inst.selectedDay) {
                selectDays($(this).parent());
              }
            });
        }
        
    });
        
    $(".cont").jcarousel({
            scroll: 1
    });
	
    $('select[name=adults]').change(function(){
        var $limit = $('option:last',this).val() - $(this).val();
        $('select[name=kids]').html('');
        $('select[name=kids]').append('<option value="0">None</option>');
        for(i=1; i<=$limit; i++)
            $('select[name=kids]').append('<option value="'+i+'">'+i+'</option>');
		
    });
	
	
	
	
	
});







