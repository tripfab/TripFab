$(function(){
    
    var ajax = false;
    var refresh_price = true;
    var page = 1;
    
    var globalData;
    
    var ajax2 = false;
    var proceed = true;
    
    $max = $('#slider-1').data('max');
    
    $('.slider-wrapper').each(function(index){
        $(this).data('country', $('#hddCountryId').val());
    });
    
    $('#slider-1').slider({
        step  : 50,
        range : true,
        min   : 0,
        max   : $max,
        values: [0, $max],
        slide : function(event, ui ) {
            $('#value-1').text( '$' + ui.values[ 0 ] + ' - $' + ui.values[ 1 ] );
        },
        change:function(event, ui){
            if(refresh_price) {
                var $days = $('#slider-2').slider('option','values');
                var $people = $('#slider-3').slider('option','value');
                var $data = {
                    pricemin:ui.values[0],
                    pricemax:ui.values[1],
                    daymin:$days[0],
                    daymax:$days[1],
                    people:$people
                };

                $.address.queryString($.param($data, true));
            }
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
            if(refresh_price) {
                var $prices = $('#slider-1').slider('option','values');
                var $people = $('#slider-3').slider('option','value');
                var $data = {
                    pricemin:$prices[0],
                    pricemax:$prices[1],
                    daymin:ui.values[0],
                    daymax:ui.values[1],
                    people:$people
                };

                $.address.queryString($.param($data, true));
            }
        }
    });
    $('#value-2').text($( '#slider-2' ).slider( 'values', 0 ) + ' days - ' + $( '#slider-2' ).slider( 'values', 1 ) + ' days');
    
    $('#slider-3').slider({
        value: 0,
        min: 0,
        max: 10,	  	
        slide: function( event, ui ) {
            $('#value-3').text((ui.value > 0) ? ui.value : 'Any');	  	
        },	  	
        change:function(event, ui){
            if(refresh_price) {	  	
                var $prices = $('#slider-1').slider('option','values');	  	
                var $days = $('#slider-2').slider('option','values');	  	
                var $data = {	  	
                    pricemin:$prices[0],	  	
                    pricemax:$prices[1],	  	
                    daymin:$days[0],	  	
                    daymax:$days[1],	  	
                    people:ui.value	  	
                };
                $.address.queryString($.param($data, true));            
            }
        }
    });
    
    $('#value-3').text(($( '#slider-3' ).slider('value') > 0) ? $( '#slider-3' ).slider('value') : 'Any' );
    //$('.scrollContainer').scrollElement();
    
    $.address.change(function($ev){
        $rel = '#/'+$ev.pathNames[0];
        
        if($rel == "#/undefined") {
            document.location.hash = "#/all";
            return;
        }
        
        var $price  = $('#slider-1');
        var $days   = $('#slider-2');
        var $people = $('#slider-3');
        
        var $a = $('ul.cats-menu li a[href="'+$rel+'"]');
        var $data = $ev.parameters;
        
        $data.category = $a.data('category');
        $data.country  = $price.parents('.slider-wrapper').data('country');
        
        refresh_price = false;
        
        if($ev.queryString == "") {
            $price.slider('values',[0,$max]);
            $days.slider('values',[0,30]);
            $people.slider('value',0);
        } else {
            $price.slider('values',[$data.pricemin, $data.pricemax]);
            $days.slider('values',[$data.daymin, $data.daymax]);
            $people.slider('value',$data.people);
        }
        
        globalData = $data;
        
        proceed = true;
        page = 1;
        
        refresh_price = true;
        
        if(ajax != false)
            ajax.abort();
        
        $price.slider('disable');
        $days.slider('disable');
        $people.slider('disable');
        
        $('.loading').show();
        
        ajax = $.ajax({
            url:'/ajax/gettrips',
            data:$data,
            success:function(response){
                $price.slider('enable');
                $days.slider('enable');
                $people.slider('enable');
                $('.wrapper.results .content').html(response);
                
                $("img.lazy").lazyload({ 
                    effect : "fadeIn",
                    load:function(){
                        $(this).removeClass('lazy');
                    }
                });
            },
            error:function(){
                
                $( '#slider-3' ).slider('enable');
                $('.loading').hide();
            }
        });	
    });
    
    $(window).scroll(function(){
        $top = $('#footer').offset();
        $top = $top.top - 1500;
        
        $wtop = $(window).scrollTop();
        
        if($wtop > $top)
            loadMore(globalData);
    });
    
    function loadMore($data){
        if(proceed) {
            if(!ajax2) {
                page++;
                $data.page = page;
                ajax2 = $.ajax({
                    url:'/ajax/gettrips2',
                    data:$data,
                    success:function(results){
                        if(results != "") {
                            $('.wrapper.results .content').append(results);
                            $("img.lazy").lazyload({ 
                                effect : "fadeIn",
                                load:function(){
                                    $(this).removeClass('lazy');
                                }
                            });
                        } else {
                            proceed = false;
                        }
                        ajax2 = false;
                    }
                });
            }
        }
    }
});