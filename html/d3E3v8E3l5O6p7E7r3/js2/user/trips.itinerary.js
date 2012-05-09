$(document).ready(function() {
    
    var assigned_listings = 0;
    $('.itinerary-wrapper .slider .slide .itinerary-items ul li:not(.empty)').each(function(){
        assigned_listings++;
    });
    
    console.log(assigned_listings);
    
    $('a.lb').fancybox({
        padding:0,
        overlayColor:'#fff',
        centerOnScroll:1,
        showCloseButton: true
    });
    $('.lbox .btn-2, .lbox .btn-11').click(function(){
        $(this).parents('form').submit();
    });
    
    $('.activities-tabs > ul > li').each(function(){
        $('em',this).text('('+$('ul li', $('a', this).attr('href')).length+')');
    });

    $fancybox = true;
    $drop     = false;
    
    $('.activities-tabs').tabs({
        select:function(event, ui){
            $ca  = $('.jcarousel-list',ui.panel);
            $num = $('> li', $ca).length;
            if($num == 1) {
                $prev = $('.jcarousel-prev',ui.panel);
                $prev.click();
            }
        }
    });
    
    $('.slider').cycle({
        fx:     'fade', 
        speed:  'fast', 
        timeout: 0,
        next:   '.next', 
        prev:   '.prev'
    });
	
    $( ".tab ul li" ).each(function(){
        var $values = {
            id		: $(this).data('id'),
            country	: $(this).data('country'),
            city	: $(this).data('city'),
            type	: $(this).data('type'),
            clas	: $(this).data('class'),
            trip	: $(this).data('trip')
        };
        $(this).data('values',$values);
    });
    
    $( ".itinerary-items .item ul li" ).each(function(){
        var $values = {
            id		: $(this).data('id'),
            country	: $(this).data('country'),
            city	: $(this).data('city'),
            type	: $(this).data('type'),
            clas	: $(this).data('class'),
            trip	: $(this).data('trip')
        };
        $(this).data('values',$values);
    });
    
    $( ".itinerary-items .item").each(function(){
        var $values = {
            time : $(this).data('time'),
            day  : $(this).data('day'),
            date : $(this).data('date')
        }
        $(this).data('values', $values);
    });

    $( ".tab ul li" ).draggable({
        revert: 'invalid',
        helper: "clone",
        cursor:'move',
        start:function(){
            $clip = $(this).parents('div.jcarousel-clip');
            $clip.css({
                width:$clip.width(),
                height:$clip.height()
            });
            $clip.addClass('unhidden');
            $fancybox = false;
        },
        stop:function(){
            $clip = $(this).parents('div.jcarousel-clip');
            $clip.removeClass('unhidden');
            if(!$drop)
                $fancybox = true;
        }
    });
	
    $( ".itinerary-items .item ul li" ).draggable({
        revert:'invalid'
    });
    
    $( ".itinerary-items .item ul li" ).sortable({
        revert: true,
        placeholder: "ui-state-highlight"
    });
	
    $( ".itinerary-items .item:not(.stay)" ).droppable({
        accept:':not(.cat-hotel)',
        activeClass: "ui-state-default",
        hoverClass: "ui-state-hover",
        drop: function( event, ui ) {
            $drop	  = true;
            $fancybox     = false;
            var values 	  = ui.draggable.data('values');
            var li    	  = $("<li></li>");
            var $dropable = $(this).find('ul');
            var daytimevalues = $(this).data('values');
			
            var $data = {
                trip    : values.trip,
                listing : values.id,
                day     : daytimevalues.day,
                date    : daytimevalues.date,
                time    : daytimevalues.time
            }
			
            var $empty = $( this ).find('.empty');
			
            $.ajax({
                url:'/ajax/updatetriplisting',
                data:$data,
                type:'post',
                success:function(response){
                    li.data('values',values);
                    li.addClass(values.clas);
                    li.html(ui.draggable.html());
                    li.append('<a href="" class="delete">Delete</a>');
					
                    $empty.remove();
                    li.appendTo($dropable);
                    ui.draggable.remove();
					
                    $( ".itinerary-items .item ul li" ).draggable({
                        revert: 'invalid'
                    });
                    
                    $( ".itinerary-items .item ul li" ).sortable({
                        revert: true,
                        placeholder: "ui-state-highlight",
                        update: function(){
                            updateSort('onSort not stay');
                        }
                    });
                    updateSort('onDrop not stay');
                    $fancybox = true;
                    $drop     = false;
                    $('#tripprice').text('$'+response);
                    
                    if(assigned_listings == 0) {
                        $('.tocheckout').removeClass('deny');
                    }
                    
                    assigned_listings++;
                    
                    console.log(assigned_listings);
                    
                },
                error:function(){
                    showError('Something went wrong');
                    $fancybox = true;
                    $drop     = false;
                }
            });
        }
    });
    
    $( ".itinerary-items .item.stay" ).droppable({
        accept:'.cat-hotel',
        activeClass: "ui-state-default",
        hoverClass: "ui-state-hover",
        drop: function( event, ui ) {
            $drop     = true;
            $fancybox = false;
            var values 	  = ui.draggable.data('values');                
            var li    	  = $("<li></li>");
            var $dropable = $(this).find('ul');
            var daytimevalues = $(this).data('values');
            
            if($dropable.data('hashotel') == "1") {
                showError('There is one hotel for this night');
                return;
            }
			
            var $data = {
                trip    : values.trip,
                listing : values.id,
                day     : daytimevalues.day,
                date    : daytimevalues.date,
                time    : daytimevalues.time
            }
			
            var $empty = $( this ).find('.empty');

            $.ajax({
                url:'/ajax/updatetriplisting',
                data:$data,
                type:'post',
                success:function(response){
                    
                    ui.draggable.data('hashotel', 1);
                    
                    li.data('values',values);
                    li.addClass(values.clas);
                    li.html(ui.draggable.html());
                    li.append('<a href="" class="delete">Delete</a>');
					
                    $empty.remove();
                    li.appendTo($dropable);
                    //ui.draggable.remove();
                    
                    $dropable.data('hashotel', "1");
					
                    $( ".itinerary-items .item ul li" ).draggable({
                        revert: 'invalid'
                    });
                    $( ".itinerary-items .item ul li" ).sortable({
                        revert: true,
                        placeholder: "ui-state-highlight",
                        update: function(){
                            updateSort('onSort stay');
                        }
                    });
                    updateSort('onDrop stay');
                    $fancybox = true;
                    $drop     = false;
                    $('#tripprice').text('$'+response);
                    
                    if(assigned_listings == 0) {
                        $('.tocheckout').removeClass('deny');
                    }
                    
                    assigned_listings++;
                    
                    console.log(assigned_listings);
                },
                error:function(){
                    showError('Something went wrong');
                    $fancybox = true;
                    $drop     = false;
                }
            });
        }
    });
    
    $('.itinerary-items .item:not(.stay) a.delete').live('click', function(){
        var $element = $(this).parents('li');
        var values   = $element.data('values');
        $.ajax({
            url:'/ajax/removefromtrip',
            type:'post',
            data:{
                listing : values.id,
                trip    : values.trip
            },
            success:function(response){
                $element.fadeOut('normal', function(){
                    $ul = $('#mycarousel-1');
                    switch(values.clas){
                        case 'cat-activity':
                            $ul = $('#mycarousel-1');
                            break;
                        case 'cat-entertainment':
                            $ul = $('#mycarousel-2');
                            break;
                        case 'cat-sight':
                            $ul = $('#mycarousel-3');
                            break;
                        case 'cat-restaurant':
                            $ul = $('#mycarousel-4');
                            break;
                        case 'cat-hotel':
                            $ul = $('#mycarousel-5');
                            break;
                    }
                    $li = $('<li></li>');
                    $li.data('values', values);
                    $li.addClass(values.clas);
                    $li.html($element.html());
                    
                    $('a.delete', $li).remove();
                    
                    $li.appendTo($ul);
                    
                    $element.remove();
                    
                    $li.draggable({
                        revert:'invalid',
                        helper: "clone",
                        cursor:'move',
                        start:function(){
                            $clip = $(this).parents('div.jcarousel-clip');
                            $clip.css({
                                width:$clip.width(),
                                height:$clip.height()
                            });
                            $clip.addClass('unhidden');
                        },
                        stop:function(){
                            $clip = $(this).parents('div.jcarousel-clip');
                            $clip.removeClass('unhidden');
                        }						
                    });
                });
                $('#tripprice').text('$'+response);
                    
                assigned_listings--;
                
                if(assigned_listings == 0) {
                    $('.tocheckout').addClass('deny');
                }

                console.log(assigned_listings);
            }
        });		
        return false;
    });
    
    $('.itinerary-items .item.stay a.delete').live('click', function(){
        var $element = $(this).parents('li');
        var $dropable = $element.parents('ul');
        var values   = $element.data('values');
        $.ajax({
            url:'/ajax/removefromtrip',
            type:'post',
            data:{
                listing : values.id,
                trip    : values.trip
            },
            success:function(response){
                $dropable.data('hashotel',"0");
                $element.fadeOut('normal', function(){
                    $element.remove();
                });
                $('#tripprice').text('$'+response);
                    
                assigned_listings--;
                
                if(assigned_listings == 0) {
                    $('.tocheckout').addClass('deny');
                }

                console.log(assigned_listings);
            }
        });		
        return false;
    });
	
    function updateSort(caller){
        var sorts = new Array();
        $( ".itinerary-items .item ul li:not(.empty)").each(function(index){
            var values = $(this).data('values');
            var aux = {
                listingid : values.id,
                sort      : index,
                trip      : values.trip
            }
            sorts.push(aux);
        });
        $.ajax({
            url:'/ajax/updatesorting',
            type:'post',
            data:{
                data:sorts
            }
        });
    }
	
    $('.activities-tabs .tab ul li img').live('click', function(){
        if($fancybox) {
            $values = $(this).parent().data('values')
            alert($values.id);
        }
    });
    
    $('#mycarousel-1, #mycarousel-2, #mycarousel-3, #mycarousel-4, #mycarousel-5').jcarousel({
        start:1,
        wrap:'both'
    });	
    
    $('#tooltipHelpItn').submit(function(){
        if($('input[type=checkbox]', this).is(':checked')) {
            $.cookie('tooltipHelpItn','yes',{expires:365});
        }
        $('.firstime_tip').fadeOut();
        return false;
    });
    $('#start, #end').datepicker({
    	dayNamesMin: ['S', 'M', 'T', 'W', 'T', 'F', 'S'],
    	showOtherMonths: true
    });
    $(".w3_itinerary .tocheckout.deny").live('hover', function() {
    	$(".w3_itinerary .warning").toggleClass('hidden');
    	return false
    });
    
    $(".bottom .tocheckout.deny").live('hover', function() {
    	$(".bottom .warning").toggleClass('hidden');
    	return false
    });
    
});