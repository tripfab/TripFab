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
	
	
	
    $('#slider-1').cycle({
    	fx:     'scrollHorz',
    	next: 	'#next-1',
    	prev: 	'#prev-1',
    	pager:	'#cycle-pager-1',
    	timeout:0,
    	after: onAfter
    });
    $('#slider-2').cycle({
    	fx:     'scrollHorz',
    	next: 	'#next-2',
    	prev: 	'#prev-2',
    	pager:	'#cycle-pager-2',
    	timeout:0,
    	after: onAfter
    });
    $('#slider-3').cycle({
    	fx:     'scrollHorz',
    	next: 	'#next-3',
    	prev: 	'#prev-3',
    	pager:	'#cycle-pager-3',
    	timeout:0,
    	after: onAfter
    });
    $('#slider-4').cycle({
    	fx:     'scrollHorz',
    	next: 	'#next-4',
    	prev: 	'#prev-4',
    	pager:	'#cycle-pager-4',
    	timeout:0,
    	after: onAfter
    });
    $('#slider-5').cycle({
    	fx:     'scrollHorz',
    	next: 	'#next-5',
    	prev: 	'#prev-5',
    	pager:	'#cycle-pager-5',
    	timeout:0,
    	after: onAfter
    });
    
    $('.days-cycle').cycle({
    	fx: 'scrollHorz',
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
            
            //console.log(dates);
            
            $('#datepicker').DatePickerSetDate(dates);
        }
    });
    
    
    
    /**
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
        
    });*/
	$(".slideshow .cont").jcarousel({
		scroll: 1
	});
	
	$('a.lbc, a.lb').fancybox({
		padding: 0,
		overlayColor: '#FFF',
		overlayOpacity: '0.7',
		showCloseButton: 'false',
		centerOnScroll: 'true',
		titlePosition: 'inside'
	});
	
	$('#fancybox-title').addClass('tittle');
	$('#fancybox-left-ico').addClass('lfarrow');
	$('#fancybox-right-ico').addClass('rgarrow');
	
    $('select[name=adults]').change(function(){
        var $limit = $('option:last',this).val() - $(this).val();
        $('select[name=kids]').html('');
        $('select[name=kids]').append('<option value="0">None</option>');
        for(i=1; i<=$limit; i++)
            $('select[name=kids]').append('<option value="'+i+'">'+i+'</option>');
		
    });
	
	
	
	
	
});







