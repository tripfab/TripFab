var $main_cats;
var $token;

function start(){
    fqrequest('/venues/categories', {}, false, function(response){
        $main_cats = response.response.categories;
    });
    
    $('select[name=type]').change(typeChanged);
    
    $('select[name=cat]').change(catChanged);
    
    $('form#fbpage').live('submit', fbpageSubmit);
    
    $('form#newlisting').live('submit', newlistingSubmit);
    
    $('form#fbpage input[type=button]').live('click',function(){
        if($(this).hasClass('continue')) {
            if($('form#fbpage input.fbpage').val() == '') {
                alert('Include the facebook page before continue');                
                return false;
            }
            $(this).addClass('active');
            $('form#fbpage').submit();
        } else {
            if(confirm('Are you sure?')){
                $(this).addClass('active');
                $('form#fbpage').submit();
            }
        }
    });
}

function newlistingSubmit()
{
    $data = $(this).serialize();
    $.ajax({
        url:'//tripfab.com/places/ajax/save',
        data:$data,
        type:'post',
        success:function(res){
            $('.results .form').html(res);
        }
    })
    return false;
}

function typeChanged(){
    $this = $(this);
    if($this.val() == 0)
        return;

    $cats = getSubCatsOf($this.val());

    $('select[name=cat]').html('<option value="0">Select Subcategory</value>');

    for(i in $cats){
        $('select[name=cat]').append('<option value="'+$cats[i].id+'">'+$cats[i].name+'</option>');
    }
}

function catChanged(){
    $this = $(this);
    if($this.val() == 0)
        return;

    $cat = $this.val();
    $lat = $('select[name=city] option:selected').data('lat');
    $lng = $('select[name=city] option:selected').data('lng');
    $cit = $('select[name=city] option:selected').text();

    $center = $lat+','+$lng;

    fqrequest('/venues/search', {
        ll:$center,
        intent:'browse',
        radius:4000,
        categoryId:$cat
    }, true, function(res){
        $ul = $('.results .places ul');
        $ul.html('');
        for(i in res.response.venues) {
            $venue = res.response.venues[i];
            $ul.append('<li><a href="'+$venue.id+'">'+$venue.name+'</a></li>');
            $('li a', $ul).click(function(){
                $('li a', $ul).removeClass('active');
                $(this).addClass('active');
                getForm($(this).attr('href'));
                return false;
            });
        }
    })
}

function getSubCatsOf($id)
{
    for(i in $main_cats) {
        if($main_cats[i].id == $id) {
            return $main_cats[i].categories
        }
    }
    return false;
}

function getForm($id)
{
    $('.loading').show();
    $.ajax({
        url:'//tripfab.com/places/ajax/form',
        data:{
            id:$id,
            city:$('select[name=city]').val(),
            cityname:$('select[name=city] option:selected').text(),
            type:$('select[name=type] option:selected').data('type')
        },
        success:function(res){
            $('.results .form').html(res);
        }
    });
}

function fbpageSubmit(ev)
{    
    $('.loading').show();
    $data = {
        token:$token,
        id:$('input[name=id]', this).val(),
        task:$('input.active', this).val(),
        url:$('input[name=page]', this).val()
    }
    
    console.log($data);
    
    $.ajax({
        url:'//tripfab.com/places/ajax/form2',
        data:$data,
        success:function(res) {
            $('.results .form').html(res);
        }
    });
    
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
        data:data,
        success:success
    });
}

$(document).ready(start);

window.fbAsyncInit = function() {
    FB.init({
        appId      : '370449162976552', // App ID
        channelUrl : '//tripfab.com/places/channel.html', // Channel File
        status     : true, // check login status
        cookie     : true, // enable cookies to allow the server to access the session
        xfbml      : true  // parse XFBML
    });
    
    FB.getLoginStatus(function(response) {
        if (response.status === 'connected') {
            $token = response.authResponse.accessToken;
            console.log($token);
        } else {
            FB.login(function(response) {
                if (!response.authResponse) {
                    alert('You need to enable this application on facebook in order to used');
                    $token = null;
                }
            });
        }
    });
};
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