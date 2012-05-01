var $main_cats;

var $appid = '191328617648352';
//var $appid = '370449162976552';

var $token;

$(document).ready(function(){
    
    $('select, input').attr('disabled','disabled');
    
    // Get foursquare categories
    fqrequest('/venues/categories', {}, false, function(response){
        $main_cats = response.response.categories;
    });
    
    // On category change, load subcategories
    $('select[name=type]').change(function(){
        $this = $(this);
        if($this.val() == 0)
            return;
        $cats = getSubCatsOf($this.val());
        $('select[name=cat]').html('<option value="0">Select Subcategory</value>');
        for(i in $cats){
            $('select[name=cat]').append('<option value="'+$cats[i].id+'">'+$cats[i].name+'</option>');
        }
    });
    
    // On submit 
    $('form#searchplacesform').submit(function(){
        $cat = $('select[name=cat] option:selected').val();
        $lat = $('select[name=city] option:selected').data('lat');
        $lng = $('select[name=city] option:selected').data('lng');
        $cit = $('select[name=city] option:selected').text();
        
        $('input, select').attr('disabled','disabled');

        $center = $lat+','+$lng;
        
        $.ajax({
            url:'/ajax/getadminplaces',
            data: {
                cat:$cat,
                center:$center,
                city:$cit
            },
            success:function(html){
                $('.slideshow').html(html);
                $('input, select').removeAttr('disabled');
            },
            error:function(res) {
                $('input, select').removeAttr('disabled');
            }
        });
        
        return false;
    });
    
    $('.slideshow .cont .single ul li a').live('click', function(){
        
        if($(this).hasClass('done') || $(this).hasClass('active'))
            return false;
        
        console.log('asd');
        
        $('.slideshow .cont ul li a').removeClass('active');
        $(this).addClass('active');
        
        $fb = $(this).data('facebook');
        $gl = $(this).data('google');
        
        $('.bottom .explore a.fb').attr('href',$fb);
        $('.bottom .explore a.gl').attr('href',$gl);
        
        return false;
    });
    
    $('#placeFacebookPage').submit(function(){
        $('input, select').attr('disabled','disabled');
        $url = $('input[name=url]',this).val();
        if($url == "") {
            $('input, select').removeAttr('disabled');
            return false;
        }
        
        $id = $('.slideshow .cont .single ul li a.active').attr('href');
        if(typeof $id == "undefined") {
            $('input, select').removeAttr('disabled');
            return false;                
        } if($id == "") {
            $('input, select').removeAttr('disabled');
            return false;
        } if($token == null){
            alert('Missing Facebook Token');
            $('input, select').removeAttr('disabled');
            return false;
        }
        
        $.ajax({
            url:'/ajax/saveplace',
            data:{
                id:$id,
                url:$url,
                city:$('select[name=city] option:selected').val(),
                token:$token
            },
            dataType:'json',
            success:function(res) {
                if(res.status == "success"){
                    $('#placeAdded a').attr('href','/admin/listings/edit/'+res.id);
                    $.fancybox({
                        href:'#placeAdded',
                        overlayColor:'#fff'
                    });
                    $('input, select').removeAttr('disabled');
                    $('#placeFacebookPage input:text').val("");
                    $('.slideshow .cont .single ul li a.active').addClass('done');
                    $('.slideshow .cont .single ul li a.active').removeClass('active');
                } else {
                    alert(res.error);
                    $('input, select').removeAttr('disabled');
                }
            },
            error:function(res){
                alert('Something went wrong');
                $('input, select').removeAttr('disabled');
            }
        });
        
        return false;
    });
    
    $('#placeAdded a').click(function(){
        $.fancybox.close();
        return true;
    });
});

function getSubCatsOf($id)
{
    for(i in $main_cats) {
        if($main_cats[i].id == $id) {
            return $main_cats[i].categories
        }
    }
    return false;
}

function fqrequest(url, data, limit, success){
    data.client_id      = "UAZO1NP00B4BHVHJGONXBEKHMARCCDXUCIBRYIKVXJIYDWQU";
    data.client_secret  = "C5DMAZR3H4PKNSYD4DHGVV0HV2HWBU2R3EW2FHXZPKI0PIRX";
    data.v              = "20120321";
    if(limit == true) {
        data.limit = 50;
    }
    $.ajax({
        url:'https://api.foursquare.com/v2'+url,
		dataType:'json',
        data:data,
        success:success,
        error:function(res){
            alert('Something went wrong with Foursquare');
            console.log(res);
        }
    });
}


// Load the SDK Asynchronously
(function(d){
    var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
    if (d.getElementById(id)) {
        return;
    }
    js = d.createElement('script');
    js.id = id;
    js.async = true;
    js.src = "//connect.facebook.net/en_US/all.js";
    ref.parentNode.insertBefore(js, ref);
}(document));

window.fbAsyncInit = function() {
    FB.init({
        appId      : $appid, // App ID
        channelUrl : '//tripfab.com/places/channel.html', // Channel File
        status     : true, // check login status
        cookie     : true, // enable cookies to allow the server to access the session
        xfbml      : true  // parse XFBML
    });
    
    FB.getLoginStatus(function(response) {
        console.log(response);
        if (response.status === 'connected') {
            $token = response.authResponse.accessToken;
            $('select, input').removeAttr('disabled');
        } else {
            FB.login(function(response) {
                if (!response.authResponse) {
                    alert('You need to enable this application on facebook in order to used');
                    $token = null;
                } else {
                    $token = response.authResponse.accessToken;
                    $('select, input').removeAttr('disabled');
                }
            });
        }
    });
};