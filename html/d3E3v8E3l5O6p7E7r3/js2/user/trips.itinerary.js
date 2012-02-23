$(document).ready(function() {
    
    $('a.lb').fancybox({
        padding:0,
        overlayColor:'#fff',
        centerOnScroll:1,
        showCloseButton:0
    });
    
    $('.lbox .btn-2, .lbox .btn-11').click(function(){
        $(this).parents('form').submit();
    });
    
    $('.activities-tabs > ul > li').each(function(){
        $class = '.tab > ul > .cat-'+$(this).attr('class');
        $('> a > em',this).text('('+$($class).length+')');
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
        start:function(){
            $clip = $(this).parents('div.jcarousel-clip');
            $clip.css({
                width:$clip.width(),
                height:$clip.height()
            });
            $clip.addClass('unhidden');
            $fancybox = false;
            console.log('drag start event');
        },
        stop:function(){
            $clip = $(this).parents('div.jcarousel-clip');
            $clip.removeClass('unhidden');
            if(!$drop)
                $fancybox = true;
				
            console.log('drag stop event');
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
            console.log('drop event start');
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
                    
                    console.log('drop event stop');
                },
                error:function(){
                    showError('Something went wrong');
                    $fancybox = true;
                    $drop     = false;
                    
                    console.log('drop event stop');
                }
            });
        }
    });
    
    $( ".itinerary-items .item.stay" ).droppable({
        accept:'.cat-hotel',
        activeClass: "ui-state-default",
        hoverClass: "ui-state-hover",
        drop: function( event, ui ) {
            console.log('drop event start hotels');
            $drop     = true;
            $fancybox = false;
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
                            updateSort('onSort stay');
                        }
                    });
                    updateSort('onDrop stay');
                    $fancybox = true;
                    $drop     = false;
                    $('#tripprice').text('$'+response);
                    
                    console.log('drop event stop hotels'); 
                },
                error:function(){
                    showError('Something went wrong');
                    $fancybox = true;
                    $drop     = false;
                    
                    console.log('drop event stop hotels'); 
                }
            });
        }
    });
    
    $('.itinerary-items .item a.delete').live('click', function(){
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
                    
                    console.log(values.clas);
                    
                    $li.draggable({
                        revert:'invalid',
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
});