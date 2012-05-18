(function( $ ){
    $.fn.serializeJSON=function() {
        var json = {};
        jQuery.map($(this).serializeArray(), function(n, i){
            json[n['name']] = n['value'];
        });
        return json;
    };
})( jQuery );
$(document).ready(function() {
    $('#mapcanvas').data('lat', $('input[name=listlat]').val());
    $('#mapcanvas').data('lng', $('input[name=listlng]').val());
    $('body').data('listing_title', $('input[name=listtitle]').val());
	
    $('.tabs-wrapper-2, .reviews-wrapper').tabs();
    $('textarea').elastic(); 
	
    var $lat = $('#mapcanvas').data('lat');
    var $lng = $('#mapcanvas').data('lng');
	
    var $title = $('body').data('listing_title');
    
    var latlng, myOptions, map, marker;
    
    if($lat != 'none' && $lng != 'none'){
        latlng = new google.maps.LatLng($lat,$lng);
		
        myOptions = {
            zoom: 12,
            center: latlng,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
		
        map = new google.maps.Map(document.getElementById("mapcanvas"),myOptions);
		
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
});

$(function(){
	$(".photos").jcarousel({
	    scroll: 1
	});
	$('a.lbc').fancybox({
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
    $(".single a").click(function() {
    	$id = $(this).attr('href');
    	$(".single a").removeClass('active');
    	$(this).addClass('active');
    	$(".slideshow img" ).removeClass('show');
    	$($id).addClass('show');
    	return false
    });
});

$(function(){
    $('.qa .qa-box .questions a.btn-3').click(function(){
        $(this).parent().parent().parent().find('div.message').toggleClass('show');
        return false;
    });
});

$(function(){
    $('.addToTripBtn:not(.not)').live('click', function(){
        $('.addTrip').removeClass('show');
        $form = $(this).next('form');
        $('.addTrip', $form).toggleClass('show');
        return false;
    });
    
    $('.js-new-trip').live('click',function(){
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
	
    $('.addTrip select').live('change', function(){
        if($(this).val() == 'new'){
            $form = $(this).parents('form');
            $listing = $('input[name=listing]', $form).val();
            $('#newtrip form input[name=listing]').val($listing);
            $.fancybox({
                href:'#newtrip',
                padding:0,
                overlayColor:'#fff',
                showCloseButton:false,
                modal:true,
                centerOnScroll:true
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
                    $('.addTrip').removeClass('show');
                    showAlert(response.message);
                } else if(response.type == 'newtrip'){
                    $.fancybox.close();
                    $('input, select').removeAttr('disabled');
                    $('.addTrip').removeClass('show');
                    showAlert(response.message);
                    $('.addTrip select').append('<option value="'+response.tripid+'">'+response.triptitle+'</option>');
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
	
    $('.addtotrip .btn-10').live('click', function(){
        $(this).parents('.addTrip').removeClass('show');
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
                    $('.addTrip').removeClass('show');
                    showAlert(response.message);
                } else if(response.type == 'newtrip'){
                    $('input, select').removeAttr('disabled');
                    $('.addTrip').removeClass('show');
                    showAlert(response.message);
                    $('.addTrip select').append('<option value="'+response.tripid+'">'+response.triptitle+'</option>');
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
});

$(function(){	
    $('#showQuestionForm').click(function(){
        $('#askQuestion').removeClass('hidden');
        return false;
    });
	
    $('#askQuestionForm .btn-4').click(function(){
        $('#askQuestion').addClass('hidden');
        return false;
    });
	
    $('#askQuestionForm').submit(function(){
        $form = $(this);
        $data = {
            question : $('textarea[name=question]', $form).val(),
            listing  : $('input[name=listing]', $form).val()
        };
        if($data.question == ''){
            showError('Write your question');
            return false;
        }
        $.ajax({
            url:'/ajax/postquestion',
            type:'post',
            data:$data,
            success:function(response){
                if(response.type == 'success'){
                    showAlert(response.message);
                    $('textarea[name=question]', $form).val('');
                    $('#askQuestion').parents('.qa').after(response.html);
                    $('#askQuestion').addClass('hidden');
                } else {
                    showError(response.message);
                }
            },
            error:function(){
                showError('Something went worng, Please try later');
            }
        });
		
        return false;
    });
	
    $('#ui-datepicker-div').wrap('<div id="calendarContainer"></div>');
    
    $('#tooltipHelpList').submit(function(){
        if($('input[type=checkbox]', this).is(':checked')) {
            $.cookie('tooltipHelpList','yes',{expires:365,path:'/'});
        }
        $('.firstime_tip').fadeOut();
        return false;        
    });
});
