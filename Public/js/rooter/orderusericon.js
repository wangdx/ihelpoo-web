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
    
    /**$('#change_skin_save').live("click", function(){
    	$.ajax({
            type: "POST",
            url: baseUrl + "ajax/saveskin",
            data: "skin_value=" + val_skin,
            dataType: "json",
            success:function(msg){
            	if (msg.status == 'yes') {
            		$("#change_skin_save").html("<span class='f12'><span class='icon_right'></span>" + msg.info + "</span>").delay(1000).fadeOut("slow");
            	} else {
            		$("#change_skin_save").html("<span class='f12'><span class='icon_wrong'></span>" + msg.info + "</span>").delay(1000).fadeOut("slow");
            	}
        		
            }
        });
    });*/
});