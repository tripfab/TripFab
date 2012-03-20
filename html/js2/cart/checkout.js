$(document).ready(function() {
    $('input, textarea').ToggleInputValue();
    $('textarea').elastic();
	
    $('input[name=method]').click(function(){
        $('.card-details').addClass('hidden');
        if ($('input.other').is(':checked')) {
            $('.card-details').removeClass('hidden');
        }
    });
    $('select[name=ctype]').change(function(){
        if($(this).val() != ""){
            if(!($('input.other').is(':checked'))) {
                $('input[name=method]').removeAttr('checked');
                $('input.other').attr('checked', 'checked');
                $('.card-details').removeClass('hidden');
            }
        }
    });
	
    function isCreditCardActive(){
        return $('input.other').is(':checked');
    }
	
    $('#checkout').validate({
        rules:{
            method	:'required',
            specs	:'required',
            location:'required',
            phone	:'required',
            terms	:'required',
            ctype	:{
                required:isCreditCardActive
            },
            cnumber	:{
                required:isCreditCardActive,
                creditcard:true
            },
            cname	:{
                required:isCreditCardActive
            },
            cmonth	:{
                required:isCreditCardActive
            },
            cyear	:{
                required:isCreditCardActive
            },
            ccode	:{
                required:isCreditCardActive,
                minlength:3,
                maxlength:4
            },
            street1	:{
                required:isCreditCardActive
            },
            street2	:{
                required:isCreditCardActive
            },
            country	:{
                required:isCreditCardActive
            },
            city	:{
                required:isCreditCardActive
            }
        },
        errorPlacement:function(error, element){
            error.appendTo(element.parent('.card-item'));
            error.appendTo(element.parents('.section-item'));
        }
    });
});