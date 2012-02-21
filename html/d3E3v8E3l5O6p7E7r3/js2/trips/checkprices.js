$(document).ready(function() {
    $('input, textarea').ToggleInputValue();
    $('textarea').elastic();
    $('.widget.summary ul a').click(function(){
        $('.trip-item .bottom').removeClass('show');
        $('.widget.summary ul li').removeClass('active');
        var title = $(this).attr('href');
        $('.trip-items .left').find(title).find('.bottom').addClass('show');
        $(this).parent().addClass('active');
        $.scrollTo(title, 700);
        return false;
    });
    $("a.lb").fancybox({
        padding: 0,
        showCloseButton: false,
        overlayOpacity: 0.8,
        overlayColor: '#FFFFFF'
    });
    //$('.trip-items .right').scrollElement();
    $( ".calendar_start" ).datepicker({ 
        showOtherMonths: true,
        minDate:new Date(),
        dateFormat:'D M d yy',
        defaultDate:new Date($( ".calendar_start" ).val())
    });
    $('select[name=adults]').change(function(){
        var $limit = $('option:last',this).val() - $(this).val();
        $('select[name=kids]').html('');
        $('select[name=kids]').append('<option value="0">None</option>');
        for(i=1; i<=$limit; i++)
            $('select[name=kids]').append('<option value="'+i+'">'+i+'</option>');
		
    });
    
    $form = $('#checkprices_form');
    
    $proceed = true;
    
    $form.submit(function(){
        if($proceed) {
            $terms = $('.termscheck', this);
            $pass  = true;
            $terms.each(function(){
                if($pass) {
                    if(!$(this).is(':checked')) {
                        $pass = false;
                        return;
                    }
                }
            });
            
            if(!$pass) {
                showError('Please accept the terms of each listing');
                return false;
            } else {
                return true;
            }
        } else {
            return true;
        }
        return false;
    });
    
    $('#custom_checkout .right_block .btn-1').click(function(){
        $form.submit();
        return false;
    });
    
    $('input[type=radio], select', $form).change(function(){
        if($proceed) {
            $('input.proceed').attr('disabled','disabled');
            $('input.recalculate').removeAttr('disabled');
            $proceed = false;
        }
    });
    
    
});