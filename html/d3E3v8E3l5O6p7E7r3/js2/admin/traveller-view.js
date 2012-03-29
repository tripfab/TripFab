function panel(uid, tab, page){
	var url;
	if(typeof page == 'undefined')
		url = '/ajax/traveller/?user='+ uid +'&page=' + tab;
	else
		url = '/ajax/partner/?user='+ uid +'&page=' + tab;

	$("#nav1").removeClass('current');
	$("#nav2").removeClass('current');
	$("#nav3").removeClass('current');
	$("#nav4").removeClass('current');
	$("#nav5").removeClass('current');
	$("#nav" + tab).addClass('current');
	$.get(url, function(data) {
		$('#content').html(data);
	});
}