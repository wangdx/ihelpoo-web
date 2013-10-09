$().ready(function(){
	
	$(".wo_quan_cancel_a").click(function(){
		var deluserid = $(this).attr('value');
		var infohtml = "确定取消圈ta ？";
    	ajaxInfo(infohtml);
    	ajaxInfo(infohtml, 'quan', deluserid);
    	$cancelLi = $(this).parent();
    });
	
	$("#sure_cancel_quan").live('click', function(){
		var deleteId = $(this).attr("value");
        var delInfoType = $(this).attr("infotype");
		if (delInfoType == 'quan') {
			$.mobile.showPageLoadingMsg();
	    	$.ajax({
	    		type: "POST",
	    		dataType: "json",
	    		url: baseUrl + "ajax/quantacancel",
	    		data:{uid: deleteId},
	    		success:function(msg){
	    			if (msg.status == 'ok') {
	    				$cancelLi.css("backgroundColor", "#FFFA85");
	    				$cancelLi.slideUp("fast");
	    				$("#ajax_info_div").fadeOut("fast");
	    				$("#ajax_info_div_outer").hide();
	    			} else {
	    				ajaxInfo(msg.info);
	    			}
	    			$.mobile.hidePageLoadingMsg();
	    		}
	    	});
		}
	});
});