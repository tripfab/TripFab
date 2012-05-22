$(document).ready(function() {
    $('#mapcanvas').data('lat', $('input[name=listlat]').val());
    $('#mapcanvas').data('lng', $('input[name=listlng]').val());
    $('body').data('listing_title', $('input[name=listtitle]').val());
    $('body').data('listingid', $('input[name=listingids]').val());
	
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
    $(function(){
        
    	$('#tooltipHelpList').submit(function(){
    		if($('input[type=checkbox]', this).is(':checked')) {
                $.cookie('tooltipHelpList','yes',{expires:365,path:'/'});
            }
            $('.firstime_tip').fadeOut();
            return false;        
        });
        
        $('.imgs').cycle({
            timeout: 0,
            fx: 'scrollHorz',
            prev: '.lf-arrow',
            next: '.rg-arrow'
        });
//        $( ".single a" ).click(function() {
//            $id = $(this).attr('href');
//            $(".single a").removeClass('active')
//            $(this).addClass('active');
//            $(".slideshow img" ).removeClass('show');
//            $($id).addClass('show');
//            return false;
//        });
    });
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
});
$(function(){
    $('.listing-gallery ul li a').click(function(){
        if($(this).hasClass('active'))
            return false;
		
        $('.listing-gallery ul li.active').removeClass('active');
        $(this).parent().addClass('active');
		
        $('.listing-gallery .img-wrapper.active').removeClass('active');
        $($(this).attr('href'), '.listing-gallery').addClass('active');
		
        return false;
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
        $data = {
            listing:$(this).data('listing')
        };
			
        $('input, select').attr('disabled','disabled');
        
        $.ajax({
            url:'/ajax/addtotrip2',
            data:$data,
            type:'post',
            success:function(response){
                console.log(response);
                if(response.type == 'success'){
                    $('input, select').removeAttr('disabled');
                    $('.addTrip').removeClass('show');
                    $.fancybox.close();
                    showAlert(response.message);
                } else if(response.type == 'newtrip'){
                    $('input, select').removeAttr('disabled');
                    $('.addTrip').removeClass('show');
                    $.fancybox.close();
                    showAlert(response.message);
                    $('.addTrip select').append('<option value="'+response.tripid+'">'+response.triptitle+'</option>');
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
        $.fancybox.close();
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
    $('a.lb').fancybox({
        padding:0,
        overlayColor:'#000',
        centerOnScroll:1
    });
	
    $('#sendMessageForm').submit(function(){
        $form = $(this);
        $data = {
            message:$('textarea[name=message]', $form).val(),
            vendor:$('input[name=vendor]', $form).val()
        };
        if($data.message == ''){
            alert('Enter a message');
            return false;
        }
		
        $.ajax({
            url:'/ajax/sendmsgvendor',
            data:$data,
            type:'post',
            success:function(response){
                $.fancybox.close();
                if(response.type == 'success'){
                    showAlert(response.message);
                    $('textarea[name=message]', $form).val('');
                } else {
                    showError(response.message);
                    $('textarea[name=message]', $form).val('');
                }
            },
            error:function(){
                $.fancybox.close();
                showError('Something went wrong, Please try again later');
                $('textarea[name=message]', $form).val('');
            }
        });
        return false;
    });
	
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
});

$(function(){
    $('.phonecall').click(function(){
        $href = $(this).attr('href');
        $.fancybox({		
            padding:0,
            overlayColor:'#fff',
            centerOnScroll:1,
            showCloseButton:0,
            href:$href
        });
        return false;
        
    });
});

$(document).ready(function() {
    $( ".flag" ).click(function() {
        $( ".all_flags" ).toggleClass('show');
        return false;
    });
    
    $("#all_flags").simplyScroll({
        customClass: 'vert',
        orientation: 'vertical',
        auto: false,
        manualMode: 'loop',
        frameRate: 20,
        speed: 100
    });
    
    $('.number-display .all_flags a').click(function(){
        
        $img  = $('img', this).attr('src');
        $code = $(this).data('code');
        
        $('.flag img').attr('src', $img);
        $('input[name=code]').val($code);
        
        $( ".all_flags" ).removeClass('show');
        
        return false;
        
    });
    
    $('#dialpad .middle .numbers a').click(function(){
        $num = $(this).data('num');
        if($num != "*" && $num != "#") {
            $number = $('.number-display input[name=number]');
            $number.val($number.val()+''+$num);
            $number.focus();
        } 
        return false;
    });
    
    $('#start_call input[name=start]').click(function(){        
        $code = $('input[name=code]').val();
        $numb = $('input[name=number]').val();        
        if($numb == '') {
            showError('Please enter your phone number');
            return false;
        }
        
        $.ajax({
            url:'/phone/call',
            type:'post',
            data:{
                listing:$('body').data('listingid'),
                number:'+'+$code+''+$numb
            },
            success:function(res){
                if(res.type == "ok") {
                    $.fancybox.close();
                    showAlert('You will receive a call from TRIPFAB in the next 5 minutes. Please wait');
                } else {
                    $.fancybox.close();
                    showError('Sorry: '+res.message);
                }
            },
            error:function(res){
                
            }
        });
        
        return false;
    });
    
    $('#tooltipHelpList').submit(function(){
        if($('input[type=checkbox]', this).is(':checked')) {
            $.cookie('tooltipHelpList','yes',{expires:365,path:'/'});
        }
        $('.firstime_tip').fadeOut();
        return false;        
    });
});

function move(id,spd){
    var obj=document.getElementById(id),max=-obj.offsetHeight+obj.parentNode.offsetHeight,top=parseInt(obj.style.top);
    if ((spd>0&&top<=0)||(spd<0&&top>=max)){
        obj.style.top=top+spd+"px";
        move.to=setTimeout(function(){
            move(id,spd);
        },20);
    }
    else {
        obj.style.top=(spd>0?0:max)+"px";
    }
}

function translate($element) {
    $text = $element.data('text');
    $.ajax({
        url:'/ajax/translate',
        data:{text:$text},
        success:function(res){
            $element.text(res);
        }
    });
}

$(function(){
    $('.js-translate').each(function(){
        translate($(this));
    });
});


