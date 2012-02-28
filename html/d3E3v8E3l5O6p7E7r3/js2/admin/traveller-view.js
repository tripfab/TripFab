function panel(uid, tab, page){
	if(typeof page == 'undefined')
		var url = '/admin/users/data/traveller/'+ uid +'/' + tab;
	else
		var url = '/admin/users/data/partner/'+ uid +'/' + tab;
	
	$("#nav1").removeClass('current');
	$("#nav2").removeClass('current');
	$("#nav3").removeClass('current');
	$("#nav4").removeClass('current');
	$("#nav" + tab).addClass('current');
	$.get(url, function(data) {
		$('#content').html(data);
	});
}