$().ready(function(){
	/**
     * quan && quan cancel
     */
    $(".do_quanta_wo").live('click', function(){
    	$this = $(this);
    	var userid = $this.attr('userid');
    	var $infoLoading = $('<img/>').attr({'src': baseUrl + 'Public/image/common/ajax_wait.gif', 'title': '提交中...请稍等'});
    	var quaned_nums = $('#quaned_nums').html();
    	$this.html($infoLoading);
    	$.ajax({
    		type: "POST",
    		dataType: "json",
    		url: baseUrl + "ajax/quanta",
    		data:{uid: userid},
    		success:function(msg){
    			if (msg.status == 'ok') {
    				$this.removeClass().addClass("btn_quaned do_quantacancel_wo").html("已圈ta");
    				notice.send('system', userid);
    				var new_quaned_nums = parseInt(quaned_nums) + parseInt('1');
    				$('#quaned_nums').html(new_quaned_nums);
    			} else {
    				ajaxInfo(msg.info);
    				$this.html("<span class='icon_plus'></span>圈ta");
    			}
    		}
    	});
    });
    
    $(".do_quantacancel_wo").live('click', function(){
    	$this = $(this);
    	var userid = $this.attr('userid');
    	var $infoLoading = $('<img/>').attr({'src': baseUrl + 'Public/image/common/ajax_wait.gif', 'title': '提交中...请稍等'});
    	var quaned_nums = $('#quaned_nums').html();
    	$this.html($infoLoading);
    	$.ajax({
    		type: "POST",
    		dataType: "json",
    		url: baseUrl + "ajax/quantacancel",
    		data:{uid: userid},
    		success:function(msg){
    			if (msg.status == 'ok') {
    				$this.removeClass().addClass("btn_quan do_quanta_wo").html("<span class='icon_plus'></span>圈ta");
    				var new_quaned_nums = parseInt(quaned_nums) - parseInt('1');
    				$('#quaned_nums').html(new_quaned_nums);
    			} else {
    				ajaxInfo(msg.info);
    				$this.html("取消圈ta");
    			}
    		}
    	});
    });
});