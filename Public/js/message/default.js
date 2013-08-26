$().ready(function(){
	$('.a_view_info').click(function(){
		$(this).parent().parent().removeClass('msg_notread');
	});
	$('.a_view_info_sys').click(function(){
		$(this).parent().parent().removeClass('msg_notread');
	});
});