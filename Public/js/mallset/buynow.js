$().ready(function(){
	var $infoLoading = $('<img/>').attr({'src': baseUrl + 'Public/image/common/loading.gif', 'title': '处理中...请稍等'});
	$('.newnewaddressli').hide();
	$('#use_coins_other_box_span').hide();
	$('#showliaddressbtn').click(function(){
		$('.newnewaddressli').slideDown('fast');
	});
	
    /**
	 * add shopping cart data
	 */
    $('#newaddressbtn').click(function(){
        var newaddressinput = $('#newaddressinput').val();
        if (newaddressinput == '') {
        	alert('新增收货信息不能为空.');
        } else {
	        $(this).ajaxStart(function(){
	        	$(this).after($infoLoading);
	        }).ajaxComplete(function(){
	        	$infoLoading.remove();
	        });
	    	$.ajax({
	    	    type: "POST",
	    	    dataType: "json",
	    	    url: baseUrl + "/mallset/buynow",
	    	    data:{newaddress: newaddressinput},
	    	    success:function(msg){
	    	    	if (msg.status == 'ok') {
	    	    		$('.mallset_add_address_ul').append("<li><input type='radio' id='deliveryaddressid' name='deliveryaddressid' value='" + msg.data + "' checked='checked'/>" + msg.info + "</li>");
	    	    		$('#newaddressinput').val('');
	    	    	}
	    	    }
	    	});
        }
    });
    
    /**
     * use pocket coins
     */
    $('#use_coins_other_show').click(function(){
    	$('#use_coins_other_box_span').show('fast');
    });
    $('#use_coins_other_btn').click(function(){
    	var mycoins = $('#my_coins').attr('value');
    	var price = $('#now_price').attr('value');
    	var usecoins = $('#use_coins_other_input').val();
    	var nowprice;
    	var leftcoins;
    	$('#new_use_coins_other_input').val(usecoins);
    	if (parseFloat(mycoins) >= parseFloat(usecoins)) {
    		nowcoins = parseFloat(mycoins) - parseFloat(usecoins);
    		nowprice = parseFloat(price) - parseFloat(usecoins);
    		leftcoins = parseFloat(mycoins) - parseFloat(price);
    		if (parseFloat(price) > parseFloat(usecoins)) {
    			$('#now_price').text(nowprice);
    			$('#my_coins').text(mycoins + ' - ' + usecoins + ' = ' + nowcoins);
    		} else {
    			$('#now_price').text('0');
    			$('#my_coins').text(mycoins + ' - ' + price + ' = ' + leftcoins);
    		}
    	} else {
    		if (parseFloat(price) > parseFloat(mycoins)) {
    			nowprice = parseFloat(price) - parseFloat(mycoins);
        		$('#now_price').text(nowprice);
        		$('#my_coins').text(mycoins + ' - ' + mycoins + ' = 0');
    		} else {
    			alert('圈圈钱包金额不够呢');
    		}
    	}
    });
    
    /**
     * buy button
     */
    $('#buy_button').click(function(){
    	var cid = $('#cid').val();
    	var buynums = $('#buynums').val();
    	var deliveryaddressid = $(":checked").val();
    	var usecoins = $('#new_use_coins_other_input').val();
    	var remarks = $('#remarks').val();
    	if ('undefined' == typeof(deliveryaddressid)) {
    		$('.mallset_add_address_ul').css({background: "#FFFA85", border:"1px solid #C00"});
    		alert('请选择收货信息');
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
        	    data:{cid: cid, buynums: buynums, usecoins: usecoins, deliveryaddressid: deliveryaddressid, remarks: remarks},
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