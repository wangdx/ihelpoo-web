$().ready(function(){
	$(".wo_quan_cancel_a").click(function(){
		var deluserid = $(this).attr('userid');
		var infohtml = "<p>确定取消圈？</p> <a class='btn_sure' id='sure_cancel_quan' value='"+deluserid+"'>确定</a><a class='btn_cancel'>取消</a>";
    	ajaxInfo(infohtml);
    });
	
	$("#sure_cancel_quan").live('click', function(){
		var $this = $(this);
		var userid = $this.attr('userid');
		var $infoLoading = $('<img/>').attr({'src': baseUrl + 'Public/image/common/ajax_wait.gif', 'title': '提交中...请稍等'});
    	$this.html($infoLoading);
    	$.ajax({
    		type: "POST",
    		dataType: "json",
    		url: baseUrl + "ajax/quantacancel",
    		data:{uid: userid},
    		success:function(msg){
    			if (msg.status == 'ok') {
    				$this.parent().slideUp("fast");
    			} else {
    				ajaxInfo(msg.info);
    			}
    		}
    	});
	});
});