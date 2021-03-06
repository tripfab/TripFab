$(document).ready(function() {

    function countLines() {
	
        var num  = 25;
        var num2  = 35;
        var num3  = 32;
        var ttl  = $(".ttl h2").height();
        var ttl2 = $(".qz").height();
        var ttl3 = $(".dscript h3").height();
			
        if(ttl > num) {
            $(".ttl h2").parent().addClass("two");
        }
        if(ttl2 > num2) {
            $(".qz").addClass("two");
        }
        if(ttl3 > num3) {
            $(".location").css('margin-bottom', '7px');
        }
    }
		
    countLines();

    $('a.lbc').fancybox({
        padding: 0,
        overlayColor: '#FFF',
        overlayOpacity: '0.7',
        showCloseButton: false,
        centerOnScroll: true,
        titlePosition: 'inside',
        showNavArrows: true
    });
    $('#fancybox-title').addClass('tittle');
    $('#fancybox-left-ico').addClass('lfarrow');
    $('#fancybox-right-ico').addClass('rgarrow');

    $('#mapcanvas').data('lat', $('input[name=listlat]').val());
    $('#mapcanvas').data('lng', $('input[name=listlng]').val());
    $('body').data('listing_title', $('input[name=listtitle]').val());
	
    var disableddates = $('input[name=diableddates]').val().split(',');
    $('body').data('disableddates', disableddates);
	
    $('body').data('listingid', $('input[name=listingids]').val());
    $('body').data('listingtoken', $('input[name=listingstoken]').val());
    $('body').data('listingprice', $('input[name=listingprice]').val());
    
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
	
    $("#accordion").accordion({
        autoHeight: false,
        collapsible: true,
        icons: false,
        animated: 'bounceslide'
    });
	
    var $disableddates = $('body').data('disableddates');
    
    $( "#calendar").datepicker({
        dayNamesMin: ['S', 'M', 'T', 'W', 'T', 'F', 'S'],
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
            $('#accordion').accordion('activate',1);
            $('#inputCheckin').val($date);
            refreshPrice();
        }
    });
	
    $('input[name=option]').click(function(){
        $('#accordion').accordion('activate',3);
        refreshPrice();
    });
    $('input[name=capacity]').click(function(){
        $('#accordion').accordion('activate',2);
        
        $('select[name=adults].ocp').html('');
        $('select[name=kids].ocp').html('');
        
        var min = parseInt($(this).data('min'));
        var max = parseInt($(this).data('max'));
        var kids = parseInt($(this).data('kids'));
        if(kids == 1) {
            for(i=min;i<=max;i++) {
                $('select[name=adults].ocp').append('<option value="'+i+'">'+i+'</option>');
            }
            if($('select[name=kids].ocp').length == 0) {
                $('select[name=adults].ocp').parent().after('<label>Children<select class="ocp kid" name="kids"></select></label>');
            }
            for(i=0;i<=max;i++) {
                $('select[name=kids].ocp').append('<option value="'+i+'">'+i+'</option>');
            }
        } else {
            for(i=min;i<=max;i++) {
                $('select[name=adults].ocp').append('<option value="'+i+'">'+i+'</option>');
            }
            $('select[name=kids].ocp').parent().remove();
        }
        for(i=min;i<=max;i++) {
            $('select[name=adults].ocp').append('<option value="'+i+'">'+i+'</option>');
        }
        refreshPrice();
    });
    $('select[name=kids].ocp').live('change',function(){
        refreshPrice();
    });
    $('select[name=adults].ocp').live('change', function(){
        var min = parseInt($(this).val());
        var max = parseInt($('option:last',this).text());
        
        $('select[name=kids].ocp').html('');
        for(i=0;i<=(max-min);i++) {
            $('select[name=kids].ocp').append('<option value="'+i+'">'+i+'</option>');
        }
        
        refreshPrice();
    });
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
        adults	 : $('select[name=adults].ocp').val(),
        kids	 : $('select[name=kids].ocp').val(),
        id		 : $('body').data('listingid'),
        token	 : $('body').data('listingtoken'),
        price	 : $('body').data('listingprice')
    };
	
    $.ajax({
        url:'/ajax/getquote',
        data:$data,
        success:function(price){
            $('#sumaryPrice').text(price);
        }
    });
}

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
        overlayColor:'#fff',
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
        
        $('span.code').text('(+'+$code+')');
        
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
        
        //$.fancybox.close();
        
        $.fancybox({		
            padding:0,
            overlayColor:'#fff',
            centerOnScroll:1,
            showCloseButton:0,
            href:'#load_ph',
            onComplete:function(){
                $.ajax({
                    url:'/phone/call',
                    type:'post',
                    data:{
                        listing:$('body').data('listingid'),
                        number:'+'+$code+''+$numb
                    },
                    success:function(res){
                        if(res.type == "ok") {
                            $.fancybox({
                                padding:0,
                                overlayColor:'#fff',
                                centerOnScroll:1,
                                showCloseButton:0,
                                href:'#errorC'
                            });
                        } else {
                            $.fancybox({
                                padding:0,
                                overlayColor:'#fff',
                                centerOnScroll:1,
                                showCloseButton:0,
                                href:'#errorB'
                            });
                        }
                    },
                    error:function(res){
                        $.fancybox({
                            padding:0,
                            overlayColor:'#fff',
                            centerOnScroll:1,
                            showCloseButton:0,
                            href:'#errorA'
                        });
                    }
                });
            }
        });
        
        return false;
    });
    
    $('#tooltipHelpList').submit(function(){
        if($('input[type=checkbox]', this).is(':checked')) {
            $.cookie('tooltipHelpList','yes',{
                expires:365,
                path:'/'
            });
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

var shown = false;

$(function(){
    $('#lstng_htl .carousel ul li').hover(function(){
        $('.bigImages .hover').hide();
        $($('a',this).data('tip')).show();
        $('.bigImages').show();
        shown=true;
    }, function(){
        shown=false;
    });

    $('.bigImages').hover(function(){
        shown=true;
    }, function(){
        shown=false;
    });

    setInterval('hideTooltip()', 100);
});

function hideTooltip() {
    if(!shown) {
        $('.bigImages').hide('slow', function(){
            $('.bigImages .hover').hide();
        });
    }
}

$(document).ready(function() {
    var Cntrl = 31;
    var aboveHeight = Cntrl + $("#header").outerHeight();
    $(window).scroll(function(){
        if ($(window).scrollTop() > aboveHeight){
            $('#lstng_header').addClass('fixed').css('top','-23');
            $('#wp_content').css('padding-top', '107px');
        } 
        else {
            $('#lstng_header').removeClass('fixed');
            $('#wp_content').css('padding-top', '0');
        }
    });


    $(".imgs").jcarousel({
        scroll: 1
    });
		
    
});

$(document).ready(function(){
    img_ready($('img.background'));
});

function img_ready($obj){
    resizeImg($obj);
    $(window).resize(function() {
        resizeImg($obj);
    });
    $obj.fadeIn();
}
function resizeImg($bgImg) { 
    var imgwidth = 1400;
    var imgheight = 223;

    var winwidth = $(window).width();
    var winheight = $(window).height();

    var winCenter = winwidth / 2;
    var imgCenter = imgwidth / 2;

    var left = (winwidth > imgwidth) ? 0 : (winCenter - imgCenter);

    var width = (winwidth > imgwidth) ? winwidth : imgwidth;

    var height = (winwidth > imgwidth) ? ((imgheight * winwidth) / imgwidth) : height;

    var winCenter2 = imgheight / 2;
    var imgCenter2 = height / 2;

    var top = (winwidth > imgwidth) ? (winCenter2 - imgCenter2) : 0;

    $bgImg.css('left',left);
    $bgImg.css('width',width);
    $bgImg.css('height',height);
    $bgImg.css('top',top);
}

$(function(){
    $('.js-book').click(function(){
        $href = $(this).attr('href');
        $.fancybox({
            href:$href,
            overlayColor:'#fff',
            padding:0,
            centerOnScroll:1,
            onStart:function(){
                $('#fancybox-outer').addClass('noshadow');
            },
            onCleanup:function(){
                $('#fancybox-outer').removeClass('noshadow');
            }
        });
        return false;
    });
    
    $('.js-submit-bookin').click(function(){
        $.fancybox.close();
        $('#booking').submit();
        return false;
    });
});