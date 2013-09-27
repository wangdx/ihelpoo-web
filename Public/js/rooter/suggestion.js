$().ready(function(){
	
    $(".reply_show_a").click(function(){
    	$this = $(this);
    	$this.parent().parent().find('.suggestion_reply_div').show();
    	/**
    	var userid = $(this).attr('uid');
    	$.ajax({
            type: "POST",
            url: baseUrl + "rooter/orderusericon",
            data:{userid: userid, way: 'up'},
            dataType: "json",
            success:function(msg){
            	if (msg.status == 'yes') {
            		$this.parent().find(".usericon_ordered_info").append("<span class='f12'><span class='icon_up'></span>" + msg.info + "</span>");
            	} else {
            		alert(msg.info);
            	}
        		
            }
        });*/
    });
    
    $(".reply_change_show_a").click(function(){
    	$this = $(this);
    	$this.parent().parent().parent().find('.suggestion_reply_div').show();
    });
    
    $(".usericon_down").click(function(){
    	$this = $(this);
    	var userid = $(this).attr('uid');
    	$.ajax({
            type: "POST",
            url: baseUrl + "rooter/orderusericon",
            data:{userid: userid, way: 'down'},
            dataType: "json",
            success:function(msg){
            	if (msg.status == 'yes') {
            		$this.parent().find(".usericon_ordered_info").append("<span class='f12'><span class='icon_download'></span>" + msg.info + "</span>");
            	} else {
            		alert(msg.info);
            	}
        		
            }
        });
    });
});