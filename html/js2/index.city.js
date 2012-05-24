$(document).ready(function() {
    var aboveHeight = 0;
    $(window).scroll(function(){
        var stickyBar = $('#search_result .content .sort');
        if(stickyBar.length == 0)
            return;
        
        if(aboveHeight == 0)
            aboveHeight = stickyBar.offset().top;

        if ($(window).scrollTop() > aboveHeight){

            stickyBar.addClass('fixed').css('top','0px');
            $('.results-wrapper').css('margin-top', '81px');
        } 
        else {
            stickyBar.removeClass('fixed');
            $('.results-wrapper').css('margin-top', '30px');
        }
    });
    
    $('.js-new-trip').live('click',function(){
        
        $listing = $(this).data('listing');
        $('#newtrip form input[name=listing]').val($listing);
        
        $.fancybox({
            href:'#newtrip',
            padding:0,
            overlayColor:'#fff',
            showCloseButton:false,
            modal:true,
            centerOnScroll:true
        });
        
        return false;
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
        $form = $(this);
        $data = {
            listing:$form.data('listing')
        };
	
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
                $(window).trigger('addtotrip');
            },
            error:function(){
                $('input, select').removeAttr('disabled');
                showError('Somehing went wrong please try later');
            }
        });
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
	$('.lbc, .js-index-cities').fancybox({
	    overlayColor:'#fff',
	    overlayOpacity:0.7,
	    centerOnScroll:1,
	    padding: 0
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
	
    $('.addtotrip .btn-4').live('click', function(){
        $(this).parents('.dd-2').removeClass('show');
        $('.addtotrip input[type=text]').addClass('hidden');
        $('.addtotrip select option:first').attr('selected','selected');
        return false;
    });
	
    $('.addtotrip').live('submit', function(){
        
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
            $slider.slider('values',[0,1500]);
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
                
                if( $('.results-wrapper .no_city').length) {
                	$('.firstime_tip').css({top:88});
                }
                
                $("img.lazy").lazyload({ 
                    effect : "fadeIn",
                    load:function(){
                        $(this).removeClass('lazy');
                    }
                });
                
                $('.single > h4').each(function(){
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
        max: 1500,
        values: [ 0, 1500 ],
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
        
                $sort = $('.js-search-sort').val();
				
                var $data = {
                    pricemin :ui.values[0],
                    pricemax :ui.values[1],
                    cats	  :$cats,
                    sort     :$sort
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
        
        $sort = $('.js-search-sort').val();
        
        // Init data to send over ajax request
        var $data = {
            pricemin :$price[0],
            pricemax :$price[1],
            cats	  :$cats,
            sort     :$sort
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
    
    $('.js-search-sort').live('change',function(){
        
        $price  = $('#slider-4').slider('values');
        $('#slider-4').slider('disable');
        
        $rel  = '#/'+$.address.pathNames()[0];
        $cats = new Array();
        $widget = $('.widget[rel="'+$rel+'"]');
        $('input[type=checkbox]:checked', $widget).each(function(){
            $cats.push($(this).val());
        });
        
        $sort = $(this).val();
        
        var $data = {
            pricemin :$price[0],
            pricemax :$price[1],
            cats     :$cats,
            sort     :$sort
        };
        
        $.address.queryString($.param($data, true));
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
    $('.js-filter-cities').live('click',function(){
        $href = $(this).attr('href');
        $.fancybox({
            href:$href,
            padding:0,
            overlayColor:'#fff',
            centerOnScroll:1
        });
    });
})
$(document).ready(function() {
	
	$('#citiesSearch2').one('click', function() {
		$('#note1').fadeIn('slow');
		return false;
	});
	$('#citiesSearch2').keypress(function() {
		$('#citiesAutocompleteContainer2 ul').css('display', 'block');
	});
	$('#citiesSearch2').keypress("autocompleteclose", function() {
		$('#note1').fadeOut('fast');
	});
	$( "#citiesSearch2" ).bind( "autocompleteclose", function() {
		$('#note2').fadeIn('slow');
	});
	$( "#citiesSearch2" ).bind( "autocompleteopen", function() {
		$('#note2').fadeOut('slow');
	});
	$( "#citiesSearch2" ).focusout(function() {
		$('#note2').fadeOut('slow');
		$('#note1').fadeOut('slow');
	});
});




$(document).ready(function() {
    $(".single").live('hover',function() {
        $(".image .add", this).text('Add to my Trip');
        $(".image .add",this).animate({
            width: "108px"
        }, 200);
    }).live('mouseleave', function(){
        $(".image .add", this).text('');
        $(".image .add",this).animate({
            width: "23px"
        }, 200);
    });
});

$(document).ready(function() {
	if( $('.results-wrapper').has(".no_city") ) {
		$('.firstime_tip').css({top:88});
		
	}
});

















