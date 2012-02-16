$(document).ready(function() {
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

    $('#checkout').submit(function(){
        Stripe.setPublishableKey('pk_HkcJUP3pJLpO1G6AeHNKGmDHF9Ahh');
        $data = {
            number:    $('.cnum', this).val(),
            cvc: 	   $('.ccode', this).val(),
            exp_month: $('.cmonth', this).val(),
            exp_year:  $('.cyear', this).val()
        };
        $('input, select', this).attr('disabled', 'disabled');
        var amount = $('body').data('secretAmmount') * 100; //amount you want to charge in cents
        Stripe.createToken($data, amount, stripeResponseHandler);
        // prevent the form from submitting with the default action
        return false;
    });
    $ammount = $('input[name=ammount]').val();
    $('body').data('secretAmmount', $ammount);
});

function stripeResponseHandler(status, response) {
    if (response.error) {
        //show the errors on the form
        var $form = $("#checkout");
        $('input, select', $form).removeAttr('disabled');
        showError(response.error.message);
    } else {
        var $form = $("#checkout");
        // token contains id, last4, and card type
        var token = response['id'];
		
        $('input, select', $form).removeAttr('disabled');
        // insert the token into the form so it gets submitted to the server
        $form.append("<input type='hidden' name='stripeToken' value='" + token + "'/>");
        // and submit
        $form.get(0).submit();
    }
}
