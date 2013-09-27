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
});