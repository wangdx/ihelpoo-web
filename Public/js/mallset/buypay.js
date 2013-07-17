$().ready(function(){
	var $infoLoading = $('<img/>').attr({'src': baseUrl + 'Public/image/common/loading.gif', 'title': '处理中...请稍等'});
	
    /**
     * buy button
     */
    $('#pay_btn').click(function(){
    	var buyway = $(":checked").val();
    	var caid = $('#commodityassessid').val();
    	if ('undefined' == typeof(buyway)) {
    		alert('请选择交易方式');
    	} else {
    		$(this).ajaxStart(function(){
            	$(this).after($infoLoading);
            }).ajaxComplete(function(){
            	$infoLoading.remove();
            });
        	$.ajax({
        	    type: "POST",
        	    dataType: "json",
        	    url: baseUrl + "/mallset/buypay",
        	    data:{buyway: buyway, caid: caid},
        	    success:function(msg){
        	    	if (msg.status == 'ok') {
        	    		window.location = baseUrl + msg.data;
        	    	} else {
        	    		$('#buy_button').html("<span class='icon_attention'></span>" + msg.info);
        	    	}
        	    }
        	});
    	}
    });
});