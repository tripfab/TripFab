
$(document).ready(function() {
	var aboveHeight = 153;
	$(window).scroll(function(){
        if ($(window).scrollTop() > aboveHeight){
            $('.sort').addClass('fixed').css('top','0px').next().css('padding-top','60px !important');
        } 
        else {
			
            $('.sort').removeClass('fixed').next().css('padding-top','0px');
        }
    });
});
(function( $ ){
    $.fn.serializeJSON=function() {
        var json = {};
        jQuery.map($(this).serializeArray(), function(n, i){
            json[n['name']] = n['value'];
        });
        return json;
    };
})( jQuery );
$(function(){	
    $.ajax({
        url:'/ajax/getsearchtags',
        success:function(tags){
            $( "#citiesSearch2" ).autocomplete({
                source: tags,
                select:function(e, ui){
                window.location = ui.item.url
                },
                appendTo:'#citiesAutocompleteContainer2'
                }).data( "autocomplete" )._renderItem = function( ul, item ) {
                return $( "<li></li>" )
                .data( "item.autocomplete", item )
                .append( '<a href="'+item.url+'">' + item.label + "</a>" )
                .appendTo( ul );
            };
        }
    });
});

$(function(){	
    var ajax = false;
    var refresh_price = true;
    $('ul.cat-menu li').each(function(index){
        var $cat = $('a', this).data('cat');
        $('a', this).data('category', $cat);
        $('a', this).data('country', $('#hddCountryId').val());
        $('a', this).data('city', $('#hddCityId').val());
    });
});

$(function(){
    $('.results-wrapper .single .been:not(.not)').live('click', function(){
        $('.dd-2').removeClass('show');
        $(this).parent().find('.dd-2').toggleClass('show');
        return false;
    });
    
    $('#tooltipHelpSearch').live('submit',function(){
        if($('input[type=checkbox]', this).is(':checked')) {
            $.cookie('tooltipHelpSearch','yes',{expires:365,path:'/'});
        }
        $('.firstime_tip').fadeOut();
        return false;
    });
	
    $('.dd-2 select').live('change', function(){
        if($(this).val() == 'new'){
            //$(this).next('input').removeClass('hidden');
            $form = $(this).parents('form');
            $listing = $('input[name=listing]', $form).val();
            $('#newtrip form input[name=listing]').val($listing);
            $.fancybox({
                href:'#newtrip',
                padding:0,
                overlayColor:'#fff',
                centerOnScroll: true
            });
      
        } else {
            $(this).next('input').addClass('hidden');
        }
    });
	
    $('input.nodatesyet').change(function(){
        $parent = $(this).parents('.item');
        if($(this).attr('checked') == 'checked'){
            $('input[name=start]', $parent).attr('disabled', 'disabled');
            $('input[name=end]', $parent).attr('disabled', 'disabled');
        } else {
            $('input[name=start]', $parent).removeAttr('disabled');
            $('input[name=end]', $parent).removeAttr('disabled');
        }
    });
	
    $('#newtrip input[name=start]').datepicker({
        minDate:new Date(),
        onSelect:function(date){
            $('#newtrip input[name=end]').datepicker('option', 'minDate', new Date(date));
        },
        dateFormat:'M d yy'
    });
    $('#newtrip input[name=end]').datepicker({
        minDate:new Date(),
        dateFormat:'M d yy'
    });
	
    $('#newtrip form').submit(function(){
        $data = $(this).serializeJSON();
        if($data.listing == ""){
            $.fancybox.close();
            showError('Somehing went wrong please try later');
            return false;
        }
        if($data.title == ""){
            showError('You need to add a name to the trip');
            return false;
        }
        $.ajax({
            url:'/ajax/addtotrip2',
            data:$data,
            type:'post',
            success:function(response){
                if(response.type == 'success'){
                    $.fancybox.close();
                    $('input, select').removeAttr('disabled');
                    $('.dd-2').removeClass('show');
                    showAlert(response.message);
                } else if(response.type == 'newtrip'){
                    $.fancybox.close();
                    $('input, select').removeAttr('disabled');
                    $('.dd-2').removeClass('show');
                    showAlert(response.message);
                    $('.dd-2 select').append('<option value="'+response.tripid+'">'+response.triptitle+'</option>');
                } else {
                    $.fancybox.close();
                    $('input, select').removeAttr('disabled');
                    showError(response.message);
                }
            },
            error:function(){
                $.fancybox.close();
                $('input, select').removeAttr('disabled');
                showError('Somehing went wrong please try later');
            }
        });
        return false;
    });
	
    $('.addtotrip .btn-4').live('click', function(){
        $(this).parents('.dd-2').removeClass('show');
        $('.addtotrip input[type=text]').addClass('hidden');
        $('.addtotrip select option:first').attr('selected','selected');
        return false;
    });
	
    $('.addtotrip').live('submit', function(){
        $form = $(this);
        $data = {
            listing:$('input[name=listing]', $form).val(),
            trip:$('select[name=trip]', $form).val(),
            title:$('input[name=title]', $form).val()
        };
		
        if($data.trip == '')
            return false;
			
        $('input, select').attr('disabled','disabled');
        $.ajax({
            url:'/ajax/addtotrip2',
            data:$data,
            type:'post',
            success:function(response){
                if(response.type == 'success'){
                    $('input, select').removeAttr('disabled');
                    $('.dd-2').removeClass('show');
                    showAlert(response.message);
                } else if(response.type == 'newtrip'){
                    $('input, select').removeAttr('disabled');
                    $('.dd-2').removeClass('show');
                    showAlert(response.message);
                    $('.dd-2 select').append('<option value="'+response.tripid+'">'+response.triptitle+'</option>');
                } else {
                    $('input, select').removeAttr('disabled');
                    showError(response.message);
                }
            },
            error:function(){
                $('input, select').removeAttr('disabled');
                showError('Somehing went wrong please try later');
            }
        });
        return false;
    });
    
    $('#ui-datepicker-div').wrap('<div id="calendarContainer"></div>');
});

$(function(){
    var ajax = false;
    var refresh_price = true;
    var page = 1;
    
    var globalData;
    
    var ajax2 = false;
    var proceed = true;
    
    $.address.change(function($ev){
        // Get main category from the url
        //console.dir($ev);
        $rel = '#/'+$ev.pathNames[0];
        $validHashes = new Array("#/all","#/activities","#/entertaiment","#/tourist-sights","#/restaurants","#/hotels");
        if(!$rel == "#/undefined") {
            document.location.hash = "#/all";
            return;
        }
        if($.inArray($rel, $validHashes) == -1){
            document.location.hash = "#/all";
            return;
        }
		
        var $a = $('ul.cat-menu li a[href="'+$rel+'"]');
        var $data = $ev.parameters;
        $data.category = $a.data('category');
        $data.country  = $a.data('country');
        $data.city 	   = $a.data('city');
		
        if(typeof $data.cats != "object"){
            $data.cats = [$data.cats];
        }
		
		
        refresh_price = false;
        $widget = $('.widget[rel="'+$rel+'"]');
        $slider = $('#slider-4');
        $('input[type=checkbox]', $widget).removeAttr('checked');
        $('.widget.destinations').hide();
        
        if($rel != "#/activities" && $rel != '#/hotels' && $rel != '#/all') {
            $('.widget.slider').hide();
        } else {
            $('.widget.slider').show();
        }
		
        if($ev.queryString == "") {
            $slider.slider('values',[0,3000]);
        } else {
            $slider.slider('values',[$data.pricemin,$data.pricemax]);
            if(typeof $data.cats == "object") {
                $.each($data.cats, function(index, val){
                    $('input[value='+val+']', $widget).attr('checked',1);
                });
            } else {
                $('input[value='+$data.cats+']', $widget).attr('checked',1);
            }
        }
        $widget.show();
        refresh_price = true;
		
        if(ajax != false)
            ajax.abort();
			
        $('.results-wrapper .loading').show();
        $('input[type=checkbox]', $widget).attr('disabled','1');
        $('#slider-4').slider('disable');
        
        globalData = $data;
        
        proceed = true;
        page = 1;
        
        ajax = $.ajax({
            url:'/ajax/getlistings',
            data:$data,
            success:function(results){
                $('.results-wrapper .loading').hide();
                $('input[type=checkbox]', $widget).removeAttr('disabled');
                $('#slider-4').slider('enable');
                refresh_price = true;
				
                $('ul.cat-menu li.active').removeClass('active');
                $a.parents('li').addClass('active');
                
                $('#search_result .content').html(results);
                
                $("img.lazy").lazyload({ 
                    effect : "fadeIn",
                    load:function(){
                        $(this).removeClass('lazy');
                    }
                });
                
                $('.single > h4').each(function(){
                    console.log();
                    $h = $(this).outerHeight();
                    $single = $(this).parents('.single');
                    if($single.hasClass('activity-1')) {
                        if($h > 20) {
                            $single.addClass('activity-2');
                            $single.removeClass('activity-1');
                        }
                    } else if($single.hasClass('hotel-1')) {
                        if($h > 20) {
                            $single.addClass('hotel-2');
                            $single.removeClass('hotel-1');
                        }
                    } else {
                        if($h <= 20) {
                            $single.addClass('gen-2');
                            $single.removeClass('gen-1');
                        }
                    }
                });
                
                //loadMore($data);
            },
            error:function(){
                $('.results-wrapper .loading').hide();
                $('input[type=checkbox]', $widget).removeAttr('disabled');
                $('#slider-4').slider('enable');
            }
        });
    });
	
    $('#slider-4').slider({
        step: 50,
        range: true,
        min: 0,
        max: 3000,
        values: [ 0, 3000 ],
        slide: function( event, ui ) {
            $('#value-4').text( '$' + ui.values[ 0 ] + ' - $' + ui.values[ 1 ] );
        },
        change:function(event, ui) {
            if(refresh_price) {
                // Get selected subcategories
                $rel  = '#/'+$.address.pathNames()[0];
                $cats = new Array();
                $widget = $('.widget[rel="'+$rel+'"]');
                $('input[type=checkbox]:checked', $widget).each(function(){
                    $cats.push($(this).val());
                });
				
                var $data = {
                    pricemin :ui.values[0],
                    pricemax :ui.values[1],
                    cats	  :$cats
                };
				
                $.address.queryString($.param($data, true));
            }
        }
    });
    $('#value-4').text( '$' + $( '#slider-4' ).slider( 'values', 0 ) + ' - $' + $( '#slider-4' ).slider( 'values', 1 ) );
	
    $('.widget.cats input[type=checkbox]').live('click',function(){
        // Get selected subcategories
        $widget = $(this).parents('.widget.cats');
        $cats = new Array();
        $('input[type=checkbox]:checked', $widget).each(function(){
            $cats.push($(this).val());
        });
		
        // Get Price slider values
        $price  = $('#slider-4').slider('values');
        $('#slider-4').slider('disable');
		
        // Init data to send over ajax request
        var $data = {
            pricemin :$price[0],
            pricemax :$price[1],
            cats	  :$cats
        };
		
        $.address.queryString($.param($data, true));
    });
	
    $('ul.cat-menu li a').live('click', function(){
        
        if($(this).parents('li').hasClass('active'))
            return false;
		
        refresh_price = false;
		
        $widget = $('.widget[rel="'+$(this).attr('href')+'"]');
        $('input[type=checkbox]',$widget).removeAttr('checked');
        
        $('ul.cat-menu li.active').removeClass('active');
        $(this).parents('li').addClass('active');
        
        document.location.hash = $(this).attr('href');
        return false;
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
                console.log(page);
                $data.page = page;
                ajax2 = $.ajax({
                    url:'/ajax/getlistings2',
                    data:$data,
                    success:function(results){
                        if(results != "") {
                            $('#search_result .content .results-wrapper').append(results);
                            $("img.lazy").lazyload({ 
                                effect : "fadeIn",
                                load:function(){
                                    $(this).removeClass('lazy');
                                }
                            });
                            $('.single > h4').each(function(){
                                console.log();
                                $h = $(this).outerHeight();
                                $single = $(this).parents('.single');
                                if($single.hasClass('activity-1')) {
                                    if($h > 20) {
                                        $single.addClass('activity-2');
                                        $single.removeClass('activity-1');
                                    }
                                } else if($single.hasClass('hotel-1')) {
                                    if($h > 20) {
                                        $single.addClass('hotel-2');
                                        $single.removeClass('hotel-1');
                                    }
                                } else {
                                    if($h <= 20) {
                                        $single.addClass('gen-2');
                                        $single.removeClass('gen-1');
                                    }
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

$(function(){
    $(window).bind('login_success', function(){
        alert('asd');
    });
})

$(document).ready(function() {
	$('#citiesSearch2').click(function() {
		$('.note1').fadeIn('slow');
		return false;
	});
	
	
	$('#citiesSearch2').keypress(function() {
		$('.note1').fadeOut('slow');
	});
	
	
	
//	function remenber() {
//		if( $('ul.ui-autocomplete').css('display' == 'none')  ) {
//			$('.note1').fadeIn('slow');
//			alert("Hello");
//		}
//	}
//	remenber();
});



























