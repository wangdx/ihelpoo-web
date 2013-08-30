$().ready(function(){
    var $infoLoading = $('<img/>').attr({'src': baseUrl + 'Public/image/common/loading.gif', 'title': '处理中...请稍等'});
    
    /**
     *  buy now
     */
    $('#buynow').click(function(){
        var item_id = $('#shop_item_id').val();
        var item_buy_nums = $('.shop_item_buynums_input').val();
        $(this).ajaxStart(function(){
        	$(this).after($infoLoading);
        }).ajaxComplete(function(){
        	$infoLoading.remove();
        });
    	$.ajax({
    	    type: "POST",
    	    dataType: "json",
    	    url: baseUrl + "mall/itemajax",
    	    data:{buynowcid: item_id, buynownums: item_buy_nums},
    	    success:function(msg){
    	    	if (msg.status == 'ok') {
    	    		window.location = baseUrl + msg.data;
    	    	} else {
    	    		$('#buynow').html("<span class='icon_attention'></span>" + msg.info);
    	    	}
    	    }
    	});
    });
    
    /**
	 * add shopping cart data
	 */
    $('#addshoppingcart').click(function(){
        var item_id = $('#shop_item_id').val();
        $(this).ajaxStart(function(){
        	$(this).after($infoLoading);
        }).ajaxComplete(function(){
        	$infoLoading.remove();
        });
    	$.ajax({
    	    type: "POST",
    	    dataType: "json",
    	    url: baseUrl + "mall/itemajax",
    	    data:{addshopcartcid: item_id},
    	    success:function(msg){
    	    	$('#addshoppingcart').html("<span class='icon_right'></span>" + msg.info);
    	    }
    	});
    });
    
    /**
     * commodity detail
     */
    $("#postdetail_btn").click(function(){
    	$(this).addClass('bg_red');
    	$("#postassess_btn").removeClass('bg_red');
    	$("#postsalesrecord_btn").removeClass('bg_red');
    	var item_id = $('#shop_item_id').val();
    	$(this).ajaxStart(function(){
        	$('.shop_item_detail').html($infoLoading);
        }).ajaxComplete(function(){
        	$infoLoading.remove();
        });
    	$.ajax({
    	    type: "POST",
    	    dataType: "json",
    	    url: baseUrl + "mall/itemajax",
    	    data:{salesdetail:item_id},
    	    success:function(msg){
    	    	$('.shop_item_detail').html(msg.data);
    	    }
    	});
    });
    
    /**
     * assess
     */
    $("#postassess_btn").click(function(){
    	$(this).addClass('bg_red');
    	$("#postdetail_btn").removeClass('bg_red');
    	$("#postsalesrecord_btn").removeClass('bg_red');
    	var item_id = $('#shop_item_id').val();
    	$(this).ajaxStart(function(){
        	$('.shop_item_detail').html($infoLoading);
        }).ajaxComplete(function(){
        	$infoLoading.remove();
        });
    	$.ajax({
    	    type: "POST",
    	    dataType: "json",
    	    url: baseUrl + "mall/itemajax",
    	    data:{assess:item_id},
    	    success:function(msg){
    	    	$('.shop_item_detail').html('<ul class="item_assess_show_list"></ul>');
    	    	$('.shop_item_detail').prepend('<div class="item_assess_show_info_div f12 black_l"><span class="icon_pump"></span> 关于评价分数说明:1~5分 1分最差,5分最好 满分! (差评:1~2分; 中评:3分; 好评:4~5分)</div>');
    	    	for(var i = 0; i < msg.data.length; i++) {
    	    		$('.item_assess_show_list').append(
    	    			"<li class='black_l'><a href='" + baseUrl + "/wo/" + msg.data[i].uid + "' target='_blank'>"
    	    	    	+ "<img src='" + msg.data[i].icon_url + "' width='50' class='radius3' />"
    	    	    	+ "</a> <a href='" + baseUrl + "/wo/" + msg.data[i].uid + "' target='_blank' class='f14'>"
    	    	    	+ msg.data[i].nickname
    	    	    	+ "</a> "
    	    	    	+ msg.data[i].content
    	    	    	+ " <span class='f14 red fb'>"+ msg.data[i].score + "分</span>"
    	    	    	+ " <span class='gray f12'>" + msg.data[i].assess_ti + "</span>"
    	    	    	+ " <span class='gray f12' title='完成交易花费总时间'>花费总时间:" + msg.data[i].timegap + "</span>"
    	    	    	+ "</li>"
    	    	    );
    	    	}
    	    }
    	});
    });
    
    /**
     * salesrecord
     */
    $("#postsalesrecord_btn").click(function(){
    	$(this).addClass('bg_red');
    	$("#postdetail_btn").removeClass('bg_red');
    	$("#postassess_btn").removeClass('bg_red');
    	var item_id = $('#shop_item_id').val();
    	$(this).ajaxStart(function(){
        	$('.shop_item_detail').html($infoLoading);
        }).ajaxComplete(function(){
        	$infoLoading.remove();
        });
    	$.ajax({
    	    type: "POST",
    	    dataType: "json",
    	    url: baseUrl + "mall/itemajax",
    	    data:{salesrecord:item_id},
    	    success:function(msg){
    	    	$('.shop_item_detail').html('<ul class="item_assess_show_list"></ul>');
    	    	for(var i = 0; i < msg.data.length; i++) {
    	    		if (null == msg.data[i].end_ti) {
	    	    		$('.item_assess_show_list').append(
	    	    			"<li class='black_l'><a href='" + baseUrl + "/wo/" + msg.data[i].uid + "' target='_blank'>"
	    	    			+ "<img src='" + msg.data[i].icon_url + "' width='50' class='radius3' />"
	    	    			+ "</a> <a href='" + baseUrl + "/wo/" + msg.data[i].uid + "' target='_blank'>"
	    	    			+ msg.data[i].nickname
	    	    			+ "</a> "
	    	    			+ " 购买数量:<span class='black_l fb'>" + msg.data[i].buynums + "</span>"
	    	    			+ " 价格:<span class='red fb'>" + msg.data[i].buyprice + "</span>"
	    	    			+ " <span class='f12'>交易方式:" + msg.data[i].buyway + "</span>"
	    	    			+ " <span class='f12 gray'>" + msg.data[i].start_ti + "</span>"
	    	    			+ " <span class='f12 gray' title='交易中" + msg.data[i].end_ti + "'><span class='icon_pump'></span>交易中</span>"
	    	    			+ "</li>"
	    	    		);
    	    		} else {
    	    			if ('' == msg.data[i].refusereason) {
	    	    			var finishwords = " <span class='f12 gray' title='完成交易时间" + msg.data[i].end_ti + "'><span class='icon_right'></span>已完成</span>";
	    	    		} else {
	    	    			var finishwords = " <span class='f12 gray' title='完成交易时间" + msg.data[i].end_ti + "'><span class='icon_attention'></span>已完成 拒绝交易,原因:" + msg.data[i].refusereason + "</span>";
	    	    		}
    	    			$('.item_assess_show_list').append(
    	    	    		"<li class='black_l'><a href='" + baseUrl + "/wo/" + msg.data[i].uid + "' target='_blank'>"
    	    	    		+ "<img src='" + msg.data[i].icon_url + "' width='50' class='radius3' />"
    	    	    		+ "</a> <a href='" + baseUrl + "/wo/" + msg.data[i].uid + "' target='_blank'>"
    	    	    		+ msg.data[i].nickname
    	    	    		+ "</a> "
    	    	    		+ " 购买数量:<span class='black_l fb'>" + msg.data[i].buynums + "</span>"
    	    	    		+ " 价格:<span class='red fb'>" + msg.data[i].buyprice + "</span>"
    	    	    		+ " <span class='f12'>交易方式:" + msg.data[i].buyway + "</span>"
    	    	    		+ " <span class='f12 gray'>" + msg.data[i].start_ti + "</span>"
    	    	    		+ finishwords
    	    	    		+ "</li>"
    	    	    	);
    	    		}
    	    	}
    	    }
    	});
    });
});
