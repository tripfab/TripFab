var $main_cats;
$(document).ready(function(){
    
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
                center:$center
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
        if($(this).hasClass('done')) 
            return false;
        
        $('.slideshow .cont ul li a').removeClass('active');
        $(this).addClass('active');
        return false;
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