$().ready(function(){
	$('.a_view_info').click(function(){
		$(this).parent().parent().removeClass('msg_notread');
	});
	$('.a_view_info_sys').click(function(){
		$(this).parent().parent().removeClass('msg_notread');
	});

    $('.msggetusers').click(function(e){
		var messagesystemid = $(this).attr('value');
		var positionleft = e.pageX + 10;
    	var positiontop = e.pageY + 10;
		$.ajax({
            type: "POST",
    		dataType: "json",
    		url: baseUrl + "ajax/msggetusers",
    		data:{messagesystemid: messagesystemid},
    		success:function(msg){
            	if (!msg.status) {
            		var inhtml = "<span class='red_l'>"+ msg.info + "</span>";
            		$('.user_info_div').css({ position: "absolute", left: positionleft, top: positiontop }).fadeIn('fast').html(inhtml);
				} else {
					var inhtml = "<ul class='msggetusers_ul'></ul>";
					$('.user_info_div').css({ position: "absolute", left: positionleft, top: positiontop }).fadeIn('fast').html(inhtml);
					for(var i = 0; i < msg.data.length; i++) {
			    	    $('.msggetusers_ul').append(
			    	    	"<li class='black_l'><a href='" + baseUrl + "/stream/u/" + msg.data[i].uid + "' target='_blank'>"
			    	    	+ "<img src='" + msg.data[i].icon + "' width='50' class='radius3' />"
			    	    	+ "</a> <a href='" + baseUrl + "/stream/u/" + msg.data[i].uid + "' target='_blank'>"
			    	    	+ msg.data[i].nickname
			    	    	+ "</a> "
			    	    	+ "</li>"
			    	    );
		    	    } 
					return false;
				}
            }
        });
	});
});