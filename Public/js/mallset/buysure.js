$().ready(function(){
	var $infoLoading = $('<img/>').attr({'src': baseUrl + 'Public/image/common/loading.gif', 'title': '处理中...请稍等'});
	
    /**
     * buy button
     */
    $('#sure_btn').click(function(){
    	var caid = $(this).attr('value');
    	$(this).ajaxStart(function(){
    		$(this).after($infoLoading);
    	}).ajaxComplete(function(){
    		$infoLoading.remove();
    	});
    	$.ajax({
    		type: "POST",
    		dataType: "json",
    		url: baseUrl + "/mallset/buysure",
    		data:{caid: caid},
    		success:function(msg){
    			if (msg.status == 'ok') {
    				window.location = baseUrl + msg.data;
    			} else {
    				$('#sure_btn').html("<span class='icon_attention'></span>" + msg.info);
    			}
    		}
    	});
    });
});