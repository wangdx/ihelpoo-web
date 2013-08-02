$().ready(function(){
    flashPic('.message_shine');

    /**
     * pull message once
     */
    mseeageNumsOnce();
    
    /**
     * nav hover
     */
    $("#header_nav_user").hover(
    	function(){
    		$("#nav_hover_list_div").slideDown("fast");
    		$("#nav_hover_list_div_ul").html("<li><a>同学</a> <a>校园组织</a> <a>周边商家</a></li>");
    	},
      	function(){
    		$("#nav_hover_list_div").hover(function(){
    			$("#header_nav_user:focus");
    			$("#nav_hover_list_div").show();
    		},function(){
    			$("#nav_hover_list_div").slideUp("fast");
    		});
	});

    /**
     * skin part
     */
    showSkin();
    $("#change_skin").click(function(){
    	$('#change_skin_save').remove();
    	$(this).fadeTo("slow",0.1).delay(300).fadeTo("fast",1).after("<a class='radius3 f12 bg_l_yellow' id='change_skin_save'><span class='icon_attention'></span>保存</a>");
    	changeSkin();
    });
    $('#change_skin_save').live("click", function(){
    	var val_skin = $('#change_skin').attr("value");
    	if (val_skin > '5' || val_skin < '0') {
    		val_skin = '0';
    	}
    	var $infoLoading = $('<img/>').attr({'src': baseUrl + 'Public/image/common/ajax_wait.gif', 'title': '提交中...请稍等'});
    	$(this).ajaxStart(function(){
    	    $(this).after($infoLoading);
        }).ajaxStop(function(){
    	    $infoLoading.remove();
        });
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
    });

    /**
     * chage online status
     */
    $('#header_online_status').live("click", function(){
    	var val_online = $(this).attr("value");
    	$.ajax({
            type: "POST",
            url: baseUrl + "ajax/changeonlinestatus",
            data: "val_online=" + val_online,
            dataType: "json",
            success:function(msg){
            	if (msg.status == 'yes') {
            		if (msg.data == 1) {
            			$('#header_online_status').attr({ value : '1', title: '正常，点击切换为潜水状态'}).html('[在线]');
            		} else if (msg.data == 2) {
            			$('#header_online_status').attr({ value : '2', title: '潜水，点击切换为正常在线状态'}).html('[潜水]');
            		}
            	} else {
            		alert(msg.info);
            	}
            }
        });
    });
});
function getStringLength(str) {
	var totalLength = 0;
	var list = str.split("");
	totalLength = list.length;
	return totalLength;
}
function flashPic(name){
    $(name).fadeOut('slow',function(){
        $(this).fadeIn('fast',function(){
            $(this).fadeOut('slow',function(){
                $(this).fadeIn('fast',function(){
                    $(this).fadeOut('slow',function(){
                        $(this).fadeIn('fast',function(){
                            $(this).fadeOut('slow',function(){
                                $(this).fadeIn('fast');
                            });
                        });
                    });
                });
            });
        });
    });
}

function mseeageNumsOnce() {
	$.ajax({
        type: "POST",
        url: baseUrl + "ajax/getmessage",
        global: false,
        data:{acquireseconds: 'default'},
        dataType: "json",
        success:function(msg){
        	if (msg.status == 'ok') {
        		var acquiremilliseconds = msg.data.acquireSeconds;
        		if (msg.data.messageSystemNums != 0) {
        			$('#message_system_nums_a').show();
        			$('#message_system_nums_a').children('span').html('+'+msg.data.messageSystemNums);
        		} else {
         			$('#message_system_nums_a').fadeOut('fast');
         		}
        		if (msg.data.messageCommentNums != 0) {
        			$('#message_comment_nums_a').show();
        			$('#message_comment_nums_a').children('span').html('+'+msg.data.messageCommentNums);
        		} else {
         			$('#message_comment_nums_a').fadeOut('fast');
         		}
        		if (msg.data.messageAtNums != 0) {
        			$('#message_at_nums_a').show();
        			$('#message_at_nums_a').children('span').html('@ +'+msg.data.messageAtNums);
        		} else {
         			$('#message_at_nums_a').fadeOut('fast');
         		}
        		if (msg.data.messageTalkNums != 0) {
        			$('#message_talk_nums_div').fadeIn('fast');
        			$('#message_talk_nums_img_icon').show().attr({'src': msg.data.lastTalkContentUserImg, 'title': msg.data.lastTalkContentUserNickname});
        			$('#message_talk_nums_span_content').html(msg.data.lastTalkContent);
        			$('#message_talk_nums_p_content_info').html('来自'+msg.data.lastTalkContentUserNickname+'...等的 <span class="f12 fb blue"> '+msg.data.messageTalkNums+'</span>条悄悄话');
        			$('.message_talk_to_url').attr({ href: baseUrl + "talk/to/" + msg.data.lastTalkContentUserId });
        		} else {
        			$('#message_talk_nums_div').fadeOut('fast');
        		}
        	} else {
        		$("#change_skin_save").html("<span class='f12'><span class='icon_wrong'></span>" + msg.info + "</span>").delay(1000).fadeOut("slow");
        	}
        },
        timeout: 10000,
        error: function() {
        	$('#message_talk_nums_div').fadeIn('fast');
        	$('#message_talk_nums_img_icon').hide();
			$('#message_talk_nums_span_content').html('<span class="red_l f12">圈圈亲，系统检测到您断网了!</span>');
			$('#message_talk_nums_p_content_info').html('');
			$('.message_talk_to_url').attr({ href: "", 'title': '与我帮圈圈服务器失去连接 :(' });
            setTimeout('mseeageNumsOnce()', 1000);
        }
    });
	$('#message_talk_nums_span_close').click(function(){
		$('#message_talk_nums_div').fadeOut('fast');
	});
}

function changeSkin(){
	$val = $("#change_skin").attr("value");
	if ($val == '0') {
		$(".header_logo").addClass("header_logo1");
		$("#change_skin").attr({ value: "1"});
		$(".header").addClass("header_pink");
		$("body").addClass("body_pink");
		$(".main").addClass("main_pink");
		$(".footer").addClass("footer_pink");
	} else if ($val == '1') {
		$(".header_logo").addClass("header_logo2");
		$("#change_skin").attr({ value: "2"});
		$(".header").removeClass("header_pink");
		$(".header").addClass("header_yellow");
		$("body").removeClass("body_pink");
		$("body").addClass("body_yellow");
		$(".main").removeClass("main_pink");
		$(".main").addClass("main_yellow");
		$(".footer").removeClass("footer_pink");
		$(".footer").addClass("footer_yellow");
	} else if ($val == '2') {
		$(".header_logo").addClass("header_logo3");
		$("#change_skin").attr({ value: "3"});
		$(".header").removeClass("header_yellow");
		$(".header").addClass("header_purple");
		$("body").removeClass("body_yellow");
		$("body").addClass("body_purple");
		$(".main").removeClass("main_yellow");
		$(".main").addClass("main_purple");
		$(".footer").removeClass("footer_yellow");
		$(".footer").addClass("footer_purple");
	} else if ($val == '3') {
		$(".header_logo").addClass("header_logo4");
		$("#change_skin").attr({ value: "4"});
		$(".header").removeClass("header_purple");
		$(".header").addClass("header_black");
		$("body").removeClass("body_purple");
		$("body").addClass("body_black");
		$(".main").removeClass("main_purple");
		$(".main").addClass("main_black");
		$(".footer").removeClass("footer_purple");
		$(".footer").addClass("footer_black");
	} else if ($val == '4') {
		$("#change_skin").attr({ value: "5"});
		$("body").removeClass("body_black");
		$(".main").removeClass("main_black");
		$(".footer").removeClass("footer_black");
	} else if ($val == '5') {
		$(".header_logo").removeClass("header_logo1");
		$(".header_logo").removeClass("header_logo2");
		$(".header_logo").removeClass("header_logo3");
		$(".header_logo").removeClass("header_logo4");
		$(".header_logo").addClass("header_logo");
		$("#change_skin").attr({ value: "0"});
		$(".header").removeClass("header_black");
		$("body").removeClass("body_black");
		$(".main").removeClass("main_black");
		$(".footer").removeClass("footer_black");
	}
}
function showSkin(){
	$val = $("#change_skin").attr("value");
	if ($val == '1') {
		$(".header_logo").addClass("header_logo1");
		$(".header").addClass("header_pink");
		$("body").addClass("body_pink");
		$(".main").addClass("main_pink");
		$(".footer").addClass("footer_pink");
	} else if ($val == '2') {
		$(".header_logo").addClass("header_logo2");
		$(".header").addClass("header_yellow");
		$("body").addClass("body_yellow");
		$(".main").addClass("main_yellow");
		$(".footer").addClass("footer_yellow");
	} else if ($val == '3') {
		$(".header_logo").addClass("header_logo3");
		$(".header").addClass("header_purple");
		$("body").addClass("body_purple");
		$(".main").addClass("main_purple");
		$(".footer").addClass("footer_purple");
	} else if ($val == '4') {
		$(".header_logo").addClass("header_logo4");
		$(".header").addClass("header_black");
		$("body").addClass("body_black");
		$(".main").addClass("main_black");
		$(".footer").addClass("footer_black");
	} else if ($val == '5') {
		$(".header_logo").addClass("header_logo4");
		$(".header").addClass("header_black");
	}
}

