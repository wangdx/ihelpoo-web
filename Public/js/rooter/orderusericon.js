$().ready(function(){
	
    $(".usericon_up").click(function(){
    	$this = $(this);
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
        });
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