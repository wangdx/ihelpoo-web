$().ready(function(){
    flashPic('.message_shine');

    /**
     * ajax info div position
     */
    var mainoffset = $('.mainmall').offset();
    if (mainoffset != null) {
        var mainpositionleft = mainoffset.left + 330;
        $("#ajax_info_div").css({left: mainpositionleft});
        $("#ajax_info_div_close").live("click", function () {
            $("#ajax_info_div").fadeOut("fast");
            $("#ajax_info_div_outer").fadeOut("fast");
        });
    }
    
    $('.btn_cancel').live('click', function(){
    	$("#ajax_info_div").fadeOut("fast");
		$("#ajax_info_div_outer").fadeOut("fast");
    });
    
    /**
     * pull message once
     */
    mseeageNumsOnce();
});
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

function ajaxInfo(htmlobj) {
    $("#ajax_info_div_outer").fadeIn('fast');
    $("#ajax_info_div").fadeIn('fast');
    $("#ajax_info_div_msg").fadeIn('fast').html(htmlobj);
}
