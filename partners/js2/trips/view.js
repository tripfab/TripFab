$(document).ready(function() {
	$('input').ToggleInputValue();
	$('.trip-gallery').cycle({ pager: '#pager'});
	$('ul.days').jcarousel({ 
		scroll: 1,
		buttonPrevHTML: '<div><span>Previous Day</span></div><div><span>Previous Day</span></div>',
		buttonNextHTML: '<div><span>Next Day</span></div><div><span>Next Day</span></div>'
	});
	$('.tabs-wrapper').tabs();
	var aux = true;
	$( "#datepicker" ).datepicker({ 
		beforeShowDay:function(date){
			if(aux){
				$date = $.datepicker.formatDate('D M d yy', $("#datepicker").datepicker('getDate'));
				$('input[name=date]').val($date);
			} aux = false;
			return [true, ''];
		},
		dateFormat:'D M d yy',
		showOtherMonths: true,
		minDate:new Date(),
		onSelect:function(date){
			console.log(date);
			$('input[name=date]').val(date);
		},
	});
	$("a.lb").fancybox({
		padding: 0,
		showCloseButton: false,
		overlayOpacity: 0.8,
		overlayColor: '#FFFFFF',
		centerOnScroll:true
	});
	
	$('select[name=adults]').change(function(){
		var $limit = $('option:last',this).val() - $(this).val();
		$('select[name=kids]').html('');
		$('select[name=kids]').append('<option value="0">None</option>');
		for(i=1; i<=$limit; i++)
			$('select[name=kids]').append('<option value="'+i+'">'+i+'</option>');
		
	});
	
});