function panel(uid, tab, page){
	
	if(typeof page == 'undefined')
		var url = '/ajax/traveller/?user='+ uid +'&page=' + tab;
	else
		var url = '/ajax/partner/?user='+ uid +'&page=' + tab;

	$("#nav1").removeClass('current');
	$("#nav2").removeClass('current');
	$("#nav3").removeClass('current');
	$("#nav4").removeClass('current');
	$("#nav" + tab).addClass('current');
	$.get(url, function(data) {
		$('#content').html(data);
	});
}