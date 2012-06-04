$(function(){
    
    $('.js-cart, .js-cart-B').hide();
    
    var ajax = false;
    var refresh_price = true;
    var page = 1;
    
    var globalData;
    
    var ajax2 = false;
    var proceed = true;
    
    var $people = $('#js-people');
    var $days   = $('#js-days');
    var $price  = $('#js-price');
    
    $price.change(function(){
        if(!refresh_price)
            return false;
        
        var $data = {
            pricemin:0,
            pricemax:$(this).val(),
            daymin:$days.val(),
            daymax:30,
            people:$people.val()
        };

        $.address.queryString($.param($data, true));
    });
    
    $days.change(function(){
        if(!refresh_price)
            return false;
        
        var $data = {
            pricemin:0,
            pricemax:$price.val(),
            daymin:$(this).val(),
            daymax:30,
            people:$people.val()
        };

        $.address.queryString($.param($data, true));
    });
    
    $people.change(function(){
        if(!refresh_price)
            return false;
        
        var $data = {
            pricemin:0,
            pricemax:$price.val(),
            daymin:$days.val(),
            daymax:30,
            people:$(this).val()
        };

        $.address.queryString($.param($data, true));
    });
    
    $.address.change(function($ev){
        $rel = '#/'+$ev.pathNames[0];
        
        if($rel == "#/undefined"){
            document.location.hash = "#/all";
            return;
        }
        
        var $data = $ev.parameters;
        
        refresh_price = false;
        
        if($ev.queryString == "") {
            $price.val(6000);
            $days.val(0)
            $people.val(0);
        } else {
            $price.val($data.pricemax);
            $days.val($data.daymin);
            $people.val($data.people);
        }
        
        switch($rel){
            case '#/honeymoon':
                $data.category = 1;
                $('#trips1 .head h2').text('Honeymoon Packages in Costa Rica');
                $('ul.breadcrump li:last a').text('Honeymoon');
                $('ul.breadcrump li:last a').attr('href','/l1/trips/costa_rica/#/honeymoon');
            break;
            case '#/relaxation':
                $data.category = 2;
                $('#trips1 .head h2').text('Relaxation Packages in Costa Rica');
                $('ul.breadcrump li:last a').text('Relaxation');
                $('ul.breadcrump li:last a').attr('href','/l1/trips/costa_rica/#/relaxation');
            break;
            case '#/family':
                $data.category = 3;
                $('#trips1 .head h2').text('Family Packages in Costa Rica');
                $('ul.breadcrump li:last a').text('Family');
                $('ul.breadcrump li:last a').attr('href','/l1/trips/costa_rica/#/family');
            break;
            case '#/diving':
                $data.category = 4;
                $('#trips1 .head h2').text('Diving Packages in Costa Rica');
                $('ul.breadcrump li:last a').text('Diving');
                $('ul.breadcrump li:last a').attr('href','/l1/trips/costa_rica/#/diving');
            break;
            case '#/romance':
                $data.category = 5;
                $('#trips1 .head h2').text('Romance Packages in Costa Rica');
                $('ul.breadcrump li:last a').text('Romance');
                $('ul.breadcrump li:last a').attr('href','/l1/trips/costa_rica/#/romance');
            break;
            case '#/surfing':
                $data.category = 6;
                $('#trips1 .head h2').text('Surfing Packages in Costa Rica');
                $('ul.breadcrump li:last a').text('Surfing');
                $('ul.breadcrump li:last a').attr('href','/l1/trips/costa_rica/#/surfing');
            break;
            case '#/holiday':
                $data.category = 7;
                $('#trips1 .head h2').text('Holiday Packages in Costa Rica');
                $('ul.breadcrump li:last a').text('Holiday');
                $('ul.breadcrump li:last a').attr('href','/l1/trips/costa_rica/#/holiday');
            break;
            case '#/student_travel':
                $data.category = 8;
                $('#trips1 .head h2').text('Student Travel Packages in Costa Rica');
                $('ul.breadcrump li:last a').text('Student Travel');
                $('ul.breadcrump li:last a').attr('href','/l1/trips/costa_rica/#/student_travel');
            break;
            case '#/getaway':
                $data.category = 9;
                $('#trips1 .head h2').text('Getaway Packages in Costa Rica');
                $('ul.breadcrump li:last a').text('Getaway');
                $('ul.breadcrump li:last a').attr('href','/l1/trips/costa_rica/#/getaway');
            break;
            case '#/birdwatching':
                $data.category = 10;
                $('#trips1 .head h2').text('Birdwatching Packages in Costa Rica');
                $('ul.breadcrump li:last a').text('Birdwatching');
                $('ul.breadcrump li:last a').attr('href','/l1/trips/costa_rica/#/birdwatching');
            break;
            case '#/eco_adventure':
                $data.category = 11;
                $('#trips1 .head h2').text('Eco Adventure Packages in Costa Rica');
                $('ul.breadcrump li:last a').text('Eco Adventure');
                $('ul.breadcrump li:last a').attr('href','/l1/trips/costa_rica/#/eco_adventure');
            break;
            case '#/adventure':
                $data.category = 12;
                $('#trips1 .head h2').text('Adventure Packages in Costa Rica');
                $('ul.breadcrump li:last a').text('Adventure');
                $('ul.breadcrump li:last a').attr('href','/l1/trips/costa_rica/#/adventure');
            break;
            case '#/gay-friendly':
                $data.category = 13;
                $('#trips1 .head h2').text('Gay-friendly Packages in Costa Rica');
                $('ul.breadcrump li:last a').text('Gay-friendly');
                $('ul.breadcrump li:last a').attr('href','/l1/trips/costa_rica/#/gay-friendly');
            break;
            case '#/golf':
                $data.category = 14;
                $('#trips1 .head h2').text('Golf Packages in Costa Rica');
                $('ul.breadcrump li:last a').text('Golf');
                $('ul.breadcrump li:last a').attr('href','/l1/trips/costa_rica/#/golf');
            break;
            case '#/sports_fishing':
                $data.category = 15;
                $('#trips1 .head h2').text('Sports fishing Packages in Costa Rica');
                $('ul.breadcrump li:last a').text('Sports fishing');
                $('ul.breadcrump li:last a').attr('href','/l1/trips/costa_rica/#/sports_fishing');
            break;
            case '#/scuba_diving':
                $data.category = 16;
                $('#trips1 .head h2').text('Scuba diving Packages in Costa Rica');
                $('ul.breadcrump li:last a').text('Scuba diving');
                $('ul.breadcrump li:last a').attr('href','/l1/trips/costa_rica/#/scuba_diving');
            break;
            case '#/luxury':
                $data.category = 17;
                $('#trips1 .head h2').text('Luxury Packages in Costa Rica');
                $('ul.breadcrump li:last a').text('Luxury');
                $('ul.breadcrump li:last a').attr('href','/l1/trips/costa_rica/#/luxury');
            break;
            case '#/all':
                $data.category = 'all';
                $('#trips1 .head h2').text('Preplanned Packages in Costa Rica');
                $('ul.breadcrump li:last a').text('All');
                $('ul.breadcrump li:last a').attr('href','/l1/trips/costa_rica/#/all');
            break;
            default:
                alert($rel);
                document.location.hash = "#/all";
                return;
            break;
        }
        
        globalData = $data;
        
        proceed = true;
        page = 1;
        
        refresh_price = true;
        
        if(ajax != false)
            ajax.abort();
        
        $('select').attr('disabled','disabled');
        
        $('.js-loading').show();
        
        ajax = $.ajax({
            url:'/ajax/gettrips3',
            data:$data,
            success:function(response){ 
                $('select').removeAttr('disabled');                
                $('.js-search-results').html(response);                
                $("img.lazy").lazyload({ 
                    effect : "fadeIn",
                    load:function(){
                        $(this).removeClass('lazy');
                    }
                });
            },
            error:function(){
                $('select').removeAttr('disabled');
                $('.js-loading').hide();
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

    $(window).scroll(function(){
        $height = $(window).height();
        $top = $(window).scrollTop() + $height - 60;
        
        if($top >= $('.js-footer-fixed').offset().top) {
            $('#wp_footer').css('bottom','0');
            $('#wp_content').css('padding-bottom', '150px');
        } else {
            $('#wp_footer').css('bottom','45px');
            $('#wp_content').css('padding-bottom', '195px');
        }
    });
    
    function loadMore($data){
        if(proceed) {
            if(!ajax2) {
                page++;
                $data.page = page;
                ajax2 = $.ajax({
                    url:'/ajax/gettrips4',
                    data:$data,
                    success:function(results){
                        if(results != "") {
                            $('.js-search-results').append(results);
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

