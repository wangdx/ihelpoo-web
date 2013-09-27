$().ready(function(){
	
    $(".reply_show_a").click(function(){
    	var $this = $(this);
    	$this.parent().parent().find('.suggestion_reply_div').show();
    });
    
    $(".reply_submit").click(function(){
    	var $this = $(this);
    	var replyid = $this.parent().attr('suggestionid');
    	var replycontent = $this.parent().find('.textarea_style').val();
    	$.ajax({
            type: "POST",
            url: baseUrl + "rooter/suggestion",
            data:{replyid: replyid, replycontent: replycontent},
            dataType: "json",
            success:function(msg){
            	if (msg.status == 'yes') {
            		$this.parent().find(".reply_submit_info").html("<span class='f12'><span class='icon_right'></span>" + msg.info + "</span>");
            	} else {
            		alert(msg.info);
            	}
            }
        });
    });
    
    $('.deletesuggestion').live('click', function(){
    	var deleteid = $(this).parent().parent().find('.suggestion_content_div').find('.suggestion_reply_div').attr("suggestionid");
    	$deleteLi = $(this).parent().parent();
    	$deleteLi.css("backgroundColor", "#FFFA85");
    	var infohtml = "<p>确定删除？</p> <a class='btn_sure' id='delete_comment' value='"+deleteid+"'>确定</a><a class='btn_cancel'>取消</a>";
    	ajaxInfo(infohtml);
    });
    
    $('#delete_comment').live('click', function(){
    	var deleteid = $(this).attr("value");
    	$.ajax({
            type: "POST",
            url: baseUrl + "rooter/suggestion",
            data: "suredel=" + deleteid,
            dataType: "json",
            success:function(msg){
            	if (msg.status = 'yes') {
	            	$deleteLi.slideUp('fast');
	                $("#ajax_info_div").fadeOut("fast");
	        		$("#ajax_info_div_outer").hide();
            	}
            }
        });
    });
    
    $('.btn_cancel').live('click', function(){
    	$("#ajax_info_div").fadeOut("fast");
		$("#ajax_info_div_outer").hide();
		$deleteLi.css("backgroundColor", "#FFF");
    });
    
});