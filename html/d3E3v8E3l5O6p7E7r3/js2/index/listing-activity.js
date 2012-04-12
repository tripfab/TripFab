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
	
    var disableddates = $('input[name=diableddates]').val().split(',');
    $('body').data('disableddates', disableddates);
	
    $('body').data('listingid', $('input[name=listingids]').val());
    $('body').data('listingtoken', $('input[name=listingstoken]').val());
    $('body').data('listingprice', $('input[name=listingprice]').val());
    
    console.log('good');
    
    //$('.tabs').tabs({selected: 0});
	
    $('.tabs').tabs({
        selected:0,
        show:function(a,b){
            if(b.tab.text == 'How to Get There'){
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
            }
        }
    });
    $('textarea').elastic();
    //$('select').jqTransSelect();
	
    $('li#acordion').accordion({ 
        autoHeight: false,
        animated:false
    });
	
    var $disableddates = $('body').data('disableddates');
	
    $( "#datepicker").datepicker({
        showOtherMonths: true,
        beforeShowDay:function(date){
            date = $.datepicker.formatDate('mm-dd-yy', date);
            return ($.inArray(date,$disableddates) != -1) ? [false] : [true]; 
        },
        minDate:new Date(),
        maxDate:'+1y',
        onSelect:function(date, inst){
            var aux = new Date(date), aux2 = new Date(aux.getTime() + 86400000);
            var $date = $.datepicker.formatDate('M d, yy', new Date(date));
            $('li#acordion').accordion('activate',1);
            $('#checkinlabel').text($date);
            $('#inputCheckin').val($date);
            refreshPrice();
        }
    });
	
    $('input[name=option]').click(refreshPrice);
    $('input[name=capacity]').click(refreshPrice);
    $('select[name=kids]').change(refreshPrice);
    $('select[name=adults]').change(refreshPrice);
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

function refreshPrice()
{
    var $data = {
        checkin	 : $('#inputCheckin').val(),
        option	 : $('input[name=option]:checked').val(),
        capacity : $('input[name=capacity]:checked').val(),
        adults	 : $('select[name=adults]').val(),
        kids	 : $('select[name=kids]').val(),
        id		 : $('body').data('listingid'),
        token	 : $('body').data('listingtoken'),
        price	 : $('body').data('listingprice')
    };
	
    $.ajax({
        url:'/ajax/getquote',
        data:$data,
        success:function(price){
            $('.summary span strong').text(price);
        }
    });
}

$(function(){
    $('.listing-gallery .listing-ttl .been').live('click', function(){
        $(this).parent().find('.dd-2').toggleClass('show');
        return false;
    });
	
    $('.dd-2 select').live('change', function(){
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
	
    $('#booking').submit(function(){
        $ready = false;
        $vals = {
            checkin:$('input[name=checkin]', $(this)).val(),
            option:$('input[name=option]:checked', $(this)).val()
        };
		
        if($vals.checkin != '' && $vals.option != undefined)
            $ready = true;
		
        if(!$ready){
            showError('Please Fill out the form first');
            return false;
        }
        return true;
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
                //console.log(res);
            }
        });
        
        return false;
    });
    
    $('#tooltipHelpList').submit(function(){
        if($('input[type=checkbox]', this).is(':checked')) {
            $.cookie('tooltipHelpList','yes',{expires:365});
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

$(function(){
    $('.livechat').popupWindow({ 
        height:500, 
        width:400, 
        top:50, 
        left:50 
    }); 
})
