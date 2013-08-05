$().ready(function(){
    showtime();
    secondShine('#secondpass');
    
    /**
     * quan && quan cancel
     */
    $(".do_quanta_wo").live('click', function(){
    	$this = $(this);
    	var userid = $this.attr('userid');
    	var $infoLoading = $('<img/>').attr({'src': baseUrl + 'Public/image/common/ajax_wait.gif', 'title': '提交中...请稍等'});
    	$this.html($infoLoading);
    	$.ajax({
    		type: "POST",
    		dataType: "json",
    		url: baseUrl + "ajax/quanta",
    		data:{uid: userid},
    		success:function(msg){
    			if (msg.status == 'ok') {
    				$this.removeClass().addClass("btn_quaned do_quantacancel_wo").html("已圈ta");
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
    	$this.html($infoLoading);
    	$.ajax({
    		type: "POST",
    		dataType: "json",
    		url: baseUrl + "ajax/quantacancel",
    		data:{uid: userid},
    		success:function(msg){
    			if (msg.status == 'ok') {
    				$this.removeClass().addClass("btn_quan do_quanta_wo").html("<span class='icon_plus'></span>圈ta");
    			} else {
    				ajaxInfo(msg.info);
    				$this.html("取消圈ta");
    			}
    		}
    	});
    });
    
    /**
     * remark
     */
    $(".remark_wo_top_a").click(function(){
		var userid = $(this).attr('userid');
		var infohtml = "<p align='left'>请输入新备注名字: <br /><br /> <input class='input_style' id='wo_top_new_remark' /> <br /><br /></p><a class='btn_sure' id='wo_top_sure_remark' value='"+userid+"'>确定</a><a class='btn_cancel'>取消</a>";
		ajaxInfo(infohtml);
    });
    
    $("#wo_top_sure_remark").live('click', function(){
    	$("#ajax_info_div").fadeOut("fast");
		$("#ajax_info_div_outer").fadeOut("fast");
		var newuserid = $(this).attr("value");
		var newremarkname = $("#wo_top_new_remark").attr("value");
		$.ajax({
			type: "POST",
			dataType: "json",
			url: baseUrl + "ajax/newremark",
			data:{newuserid: newuserid, newremarkname: newremarkname},
			success:function(msg){
				if (msg.status == '1') {
					$('.remark_wo_top_a').html("("+newremarkname+")");
				} else if (msg.status == '2') {
					$('.remark_wo_top_a').html("("+newremarkname+")");
				} else {
					ajaxInfo("备注失败 稍后再试");
				}
			}
		});
    });
    
    $(".btn_cancel").live('click', function(){
    	$("#ajax_info_div").fadeOut("fast");
		$("#ajax_info_div_outer").fadeOut("fast");
    });
    
});
function showtime(){
	today = new Date(); 
	var hours = today.getHours(); 
	var minutes = today.getMinutes(); 
	var seconds = today.getSeconds();
	var timeValue = hours;
	timeValue += ((minutes < 10) ? ":0" : ":") + minutes+""; 
	timeValue += ((seconds < 10) ? ":0" : ":") + seconds+"";
	$("#timenow").html(timeValue);
	setTimeout('showtime()',1000);
}
function secondShine(name){
	$(name).fadeOut(400,function(){
	    var x2 = 0;
	    var y2 = 6;
	    var rand2 = parseInt(Math.random() * (x2 - y2 + 1) + y2);
	    if (rand2 == 1) {
	        $(name).css({color:"#F30"});
	    }
	    if (rand2 == 2) {
	        $(name).css({color:"#9C0"});
	    }
	    if (rand2 == 3) {
	        $(name).css({color:"#09F"});
	    }
	    if (rand2 == 4) {
	        $(name).css({color:"#FC0"});
	    }
	    if (rand2 == 5) {
	        $(name).css({color:"#999"});
	    }
	    var x1 = -8;
	    var y1 = 8;
	    var rand1 = parseInt(Math.random() * (x1 - y1 + 1) + y1);
	    
	    var x = 5;
	    var y = 25;
	    var rand = parseInt(Math.random() * (x - y + 1) + y);
	    
	    var x3 = 5;
	    var y3 = 18;
	    var rand3 = parseInt(Math.random() * (x3 - y3 + 1) + y3);
	    $(name).css({fontSize:"16px"});
	    $(name).css({marginLeft:"0"});
	    $(name).css({top:"0"});
		$(name).fadeIn(300);
		$(name).animate({marginLeft: rand + "px", fontSize: rand3 + "px", top: rand1 + "px"}, 300);
		secondShine(name);
	});
}