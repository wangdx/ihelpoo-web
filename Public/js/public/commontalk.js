var baseUrl = "http://42.62.50.238/";
$().ready(function(){
    flashPic('.message_shine');
    showSkin();
    mseeageNums();
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

function mseeageNums() {
	var data_uid = $('#data_uid').val();
	var data_touid = $('#data_touid').val();
	$.ajax({
		type: "POST",
		url: baseUrl + "ajax/gettalkmessage",
		data: {uid: data_touid, touid: data_uid, way: 'status_require_message'},
		dataType: "json",
		success: function(msg){
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

				/**
				 * talk content
				 */
				if (msg.data.isinput == '1') {
					$('#input_status').html('对方正在输入...<span class="icon_write"></span>');
				} else {
					$('#input_status').text('');
				}

				if (msg.data.newmessage != '') {
					$('.flash_icon').fadeIn('fast');
					var user_flash_position = $('#' + msg.data.newmessage).parent().parent().position();
					if (user_flash_position.top != '5') {
						$('.user_list_ul').animate({scrollTop: user_flash_position.top - 5}, 800);
					}
					$('#' + msg.data.newmessage).fadeOut('slow',function(){
						$(this).fadeIn('fast',function(){
							$(this).fadeOut('slow',function(){
								$(this).fadeIn('fast').addClass('flash_icon');
							});
						});
					});
				}
				if (msg.data.content != '' || msg.data.image != '') {
		    		if (msg.data.image != '')  {
		    			var htmlIn =" <span class='f14 gray '>" + msg.data.nickname + "</span>"
		    			+ " <span class='f12 gray'>" + msg.data.time + "</span><br />"
		    			+ msg.data.content + "<br />"
		    			+ "<a href='" + msg.data.image + "' target='_target'><img src='" + msg.data.imagethumb + "' style='max-width:150px;' title='查看原图' /></a><br /><br />";
		    		} else {
		    			var htmlIn =" <span class='f14 gray '>" + msg.data.nickname + "</span>"
		    			+ " <span class='f12 gray'>" + msg.data.time + "</span><br />"
		    			+ msg.data.content + "<br /><br />"
		    		}
					$('#show_message_div').append(htmlIn);
					var boxHeight = $('#show_message_div').height();
					$('#show_message_div_outer').animate({scrollTop: boxHeight}, 800);
				}
				setTimeout('mseeageNums()', acquiremilliseconds);
			} else {
				alert(msg.data.info);
			}
		},
		timeout: 10000,
		error: function() {
			setTimeout('mseeageNums()', 1000);
		}
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

