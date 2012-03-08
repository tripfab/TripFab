$(document).ready(function() {	
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
    var creditCard = $('#checkout .cnum');
    creditCard.cardcheck({
        iconLocation: 'ul.cards',
        iconDir:'/images/'
    });

    $('#checkout').submit(function(){
        if($('input[name=account]:checked').val() == '') {
            showError('Select an account');
            return false;
        } else if(!$('input[name=terms]').is(':checked')) {
            showError('Please accept the terms and conditions');
            return false;
        } else if($('input[name=account]:checked').val() == 'new') { 
            
            if($('.cnum', this).val() == '') {
                showError('Credit Card cannot be empty');
                return false;
            } else if($('.ccode', this).val() == '') {
                showError('Security Code cannot be empty');
                return false;
            } else if($('.cmonth', this).val() == '') {
                showError('Please select the expiration month');
                return false;
            } else if($('.cyear', this).val() == '') {
                showError('Please select the expiration year');
                return false;
            } else {
                Stripe.setPublishableKey(PUBLIC_KEY);
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
            }
        } else 
            return true;
    });
    
    $ammount = $('input[name=ammount]').val();
    $('body').data('secretAmmount', $ammount);
    
    $('input, select','#checkout .new-card').focus(function(){
        if(!$('.new-card input[name=account]').is(':checked')) {
            $('input[name=account]').removeAttr('checked');
            $('.new-card input[name=account]').attr('checked','checked');
        }
    });
    
    if(typeof $.address.parameter('error') != 'undefined') {
        showError($.address.parameter('error'));
    }    
});

function stripeResponseHandler(status, response) {
    var $form = $("#checkout");
    if (response.error) {
        //show the errors on the form
        $('input, select', $form).removeAttr('disabled');
        showError(response.error.message);
    } else {
        // token contains id, last4, and card type
        var token = response['id'];
		
        $('input, select', $form).removeAttr('disabled');
        // insert the token into the form so it gets submitted to the server
        $form.append("<input type='hidden' name='stripeToken' value='" + token + "'/>");
        // and submit
        $form.get(0).submit();
    }
}
