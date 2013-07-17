$().ready(function(){
	var $infoLoading = $('<img/>').attr({'src': baseUrl + 'Public/image/common/loading.gif', 'title': '处理中...请稍等'});
	$('.mallset_seller_li_btn_p_reason').hide();
	$('.changeprice_show_outer_span').hide();
	$('.refuse_show_btn').click(function(){
		$(this).next().slideDown('fast');
	});
	
    $('.seller_sure_btn').click(function(){
    	var caid = $(this).attr('value');
    	var $this = $(this);
    	$(this).ajaxStart(function(){
    		$(this).after($infoLoading);
    	}).ajaxComplete(function(){
    		$infoLoading.remove();
    	});
    	$.ajax({
    		type: "POST",
    		dataType: "json",
    		url: baseUrl + "/mallset/seller",
    		data:{caid: caid},
    		success:function(msg){
    			if (msg.status == 'ok') {
    				$this.html('<span class="icon_right"></span>已接受交易');
    				$this.next().hide();
    			} else {
    				$this.html("<span class='icon_attention'></span>" + msg.info);
    			}
    		}
    	});
    });
    
    /**
     * change price
     */
    $('.changeprice_show_btn').click(function(){
		$(this).next().slideDown('fast');
	});
    $('.changeprice_btn').click(function(){
    	var caid = $(this).attr('value');
    	var price = $(this).parent().find('.changeprice_input').val();
    	var $this = $(this);
    	$(this).ajaxStart(function(){
    		$(this).after($infoLoading);
    	}).ajaxComplete(function(){
    		$infoLoading.remove();
    	});
    	$.ajax({
    		type: "POST",
    		dataType: "json",
    		url: baseUrl + "/mallset/seller",
    		data:{caid: caid, price: price},
    		success:function(msg){
    			if (msg.status == 'ok') {
    				$this.html('<span class="icon_right"></span>修改成功');
    				$this.parent().find('.changeprice_input').hide();
    			} else {
    				$this.html("<span class='icon_attention'></span>" + msg.info);
    			}
    		}
    	});
    });
    
    
    $('.mallset_seller_li_btn_p_reason_btn').click(function(){
    	var caid = $(this).attr('value');
    	var refusereason = $(this).parent().find('.mallset_seller_li_btn_p_reason_input').val();
    	var $this = $(this).parent().parent().find('.seller_sure_btn');
    	if ('' == refusereason) {
    		alert('请填写拒绝交易原因');
    	} else {
    		$(this).ajaxStart(function(){
            	$(this).after($infoLoading);
            }).ajaxComplete(function(){
            	$infoLoading.remove();
            });
    		$.ajax({
        		type: "POST",
        		dataType: "json",
        		url: baseUrl + "/mallset/seller",
        		data:{caid: caid, refusereason: refusereason},
        		success:function(msg){
        			if (msg.status == 'ok') {
        				$this.html('<span class="icon_attention"></span>已拒绝交易');
        				$this.next().hide();
        				$this.next().next().hide();
        			} else {
        				$this.html("<span class='icon_wrong'></span>" + msg.info);
        			}
        		}
        	});
    	}
    });
});