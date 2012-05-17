$(document).ready(function() {
    $('select[name=countries]').change(function(){
        var $id = $(this).val();
        $.ajax({
            url:'/ajax/getindexcities',
            data:{
                id:$id
            },
            success:function(cities){
                $('.home-cities .cities').html(cities);
            }			
        });
    });
});
var __a = 0;
$(document).ready(function() {
    $(".slideshow .cont").cycle({
        timeout: 0,
        fx: 'scrollHorz',
        prev: '.lfarrow',
        next: '.rgarrow'
    });
    $(".side .cont").cycle({
        timeout: 0,
        fx: 'scrollHorz',
        prev: '.prev',
        next: '.next'
    	
    });
    $(".featured .tabs").tabs({
        show:function(){
            $('.lazy').lazyload({ 
                effect : "fadeIn",
                load:function(){
                    $(this).removeClass('lazy');
                }
            });
        }
    });
    
    $('a.lbc').fancybox({
        padding: 0,
        overlayColor: '#FFF',
        overlayOpacity: '0.7',
        centerOnScroll:1
    });
    $('#newsletter form').submit(function(){
        $email = $(this).find('input[name=email]').val();
        if($email == "" || $email == "Enter your e-mail here") {
            $email.focus();
            return false;
        }

        $.ajax({
            type: 'POST',
            url: '/ajax/newssignup',
            data: {
                email:$email
            },
            success: function() {
                google_conversion_id = 956485843;
                google_conversion_label = "wsLjCI2i8gMQ06GLyAM";
                $.getScript('http://www.googleadservices.com/pagead/conversion.js',function(){				
                    $('#newsletter .cont').fadeOut('fast', function(){
                        $('#newsletter .sent').fadeIn('fast');
                    });
                    $.cookie('newsletter','1',{
                        expires: 365
                    })
                });
            }
        });
		
        return false;
    });
});
$(document).ready(function(){
    img_ready($('img.background.high'));
});

function img_ready($obj){
    resizeImg($obj);
    $(window).resize(function() {
        resizeImg($obj);
    });
    $obj.fadeIn();
}
function resizeImg($bgImg) { 
    var imgwidth = 1244;
    var imgheight = 490	;
	
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
    $.ajax({
        url:'/ajax/getsearchtags',
        success:function(tags){
            $( "#tags" ).autocomplete({
                source:tags,
                select:function(e, ui){
                window.location = ui.item.url
                }
                }).data( "autocomplete" )._renderItem = function( ul, item ) {
                return $( "<li></li>" )
                .data( "item.autocomplete", item )
                .append( '<a href="'+item.url+'">' + item.label + "</a>" )
                .appendTo( ul );
            };
        }
    });	
});
$(document).ready(function() {
    $('#tags').one('click', function() {
        $('#note1').fadeIn('slow');
        return false;
    });
    $('#tags').keypress(function() {
        $('#citiesAutocompleteContainer2 ul').css('display', 'block');
    });
    $('#tags').keypress("autocompleteclose", function() {
        $('#note1').fadeOut('fast');
    });
    $( "#tags" ).bind( "autocompleteclose", function() {
        $('#note2').fadeIn('slow');
    });
    $( "#tags" ).bind( "autocompleteopen", function() {
        $('#note2').fadeOut('slow');
    });
    $( "#tags" ).focusout(function() {
        $('#note2').fadeOut('slow');
    });
});
$(function(){
    $('.lazy').lazyload({ 
        effect : "fadeIn",
        container:$('.slideshow .cont'),
        load:function(){
            $(this).removeClass('lazy');
        }
    });
});