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
                var $people = $('select.js-people').val();
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
                var $people = $('select.js-people').val();
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
    
    $('select.js-people').change(function(){
        if(refresh_price) {
            $this = $(this);
            var $prices = $('#slider-1').slider('option','values');
            var $days = $('#slider-2').slider('option','values');
            var $data = {
                pricemin:$prices[0],
                pricemax:$prices[1],
                daymin:$days[0],
                daymax:$days[1],
                people:$this.val()
            };

            $.address.queryString($.param($data, true));
            
            $('#value-3').text(($this.val() > 0) ? $this.val() : 'Any');
        }
    });
    
    $('#value-3').text(($('select.js-people').val() > 0) ? $('select.js-people').val() : 'Any' );
	
    //$('.scrollContainer').scrollElement();
    
    $.address.change(function($ev){
        $rel = '#/'+$ev.pathNames[0];
        
        if($rel == "#/undefined") {
            document.location.hash = "#/all";
            return;
        }
        
        var $price  = $('#slider-1');
        var $days   = $('#slider-2');
        var $people = $('select.js-people');
        
        var $a = $('ul.cats-menu li a[href="'+$rel+'"]');
        var $data = $ev.parameters;
        
        $data.category = $a.data('category');
        $data.country  = $price.parents('.slider-wrapper').data('country');
        
        refresh_price = false;
        
        if($ev.queryString == "") {
            $price.slider('values',[0,$max]);
            $days.slider('values',[0,30]);
            $people.val('0');
        } else {
            $price.slider('values',[$data.pricemin, $data.pricemax]);
            $days.slider('values',[$data.daymin, $data.daymax]);
            $people.val($data.people); 
            $('#value-3').text(($people.val() > 0) ? $people.val() : 'Any');
        }
        
        globalData = $data;
        
        proceed = true;
        page = 1;
        
        refresh_price = true;
        
        if(ajax != false)
            ajax.abort();
        
        $price.slider('disable');
        $days.slider('disable');
        $people.attr('disabled','disabled');
        
        $('.loading').show();
        
        ajax = $.ajax({
            url:'/ajax/gettrips',
            data:$data,
            success:function(response){
                $price.slider('enable');
                $days.slider('enable');
                $people.removeAttr('disabled');
                $('.wrapper.results .content').html(response);
                
                $("img.lazy").lazyload({ 
                    effect : "fadeIn",
                    load:function(){
                        $(this).removeClass('lazy');
                    }
                });
            },
            error:function(){
                
                $people.removeAttr('disabled');
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