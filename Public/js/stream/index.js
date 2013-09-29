(function($){
	$.fn.extend({
		insertAtCaret: function(){
			var $t=$(this)[0];
			var content;
			if (document.selection) {
				this.focus();
				content = $(this).val();
				this.focus();
			} else if ($t.selectionStart || $t.selectionStart == '0') {
				var startPos = $t.selectionStart;
				var endPos = $t.selectionEnd;
				var scrollTop = $t.scrollTop;
				content = $t.value.substring(0, startPos);
			} else {
				content = this.value;
				this.focus();
			}
			return content;
		}
	})
})(jQuery);

$().ready(function(){

	/**
	 * skin
	 */
	$("#change_skin").click(function(){
		var mainoffset = $('.main').offset();
	    var mainpositionleft = mainoffset.left + 240;
		$("#change_skin_div").css({left : mainpositionleft}).slideDown("fast");
	});
	
	$("#change_skin_close").click(function(){
		$("#change_skin_div").slideUp("fast");
	});
	
	$(".change_skin_select_a").click(function(){
		var $changeheader = $(".header");
		var $changemain = $(".main");
		var $changelay_background = $("#layBackground");
		var $changebody = $("body");
		$valofskin = $(this).attr("value");
		if ($valofskin == '0') {
			$changeheader.removeClass();
			$changemain.removeClass();
			$changelay_background.removeClass();
			$changebody.removeClass();
			$changeheader.addClass("header");
			$changemain.addClass("main");
			$changelay_background.addClass("lay_background");
			$changebody.addClass("body");
		} else if ($valofskin == '1') {
			$changeheader.removeClass();
			$changemain.removeClass();
			$changelay_background.removeClass();
			$changebody.removeClass();
			$changeheader.addClass("header header_1");
			$changemain.addClass("main main_1");
			$changelay_background.addClass("lay_background_1");
			$changebody.addClass("body_1");
		} else if ($valofskin == '2') {
			$changeheader.removeClass();
			$changemain.removeClass();
			$changelay_background.removeClass();
			$changebody.removeClass();
			$changeheader.addClass("header header_2");
			$changemain.addClass("main main_2");
			$changelay_background.addClass("lay_background_2");
			$changebody.addClass("body_2");
		} else if ($valofskin == '3') {
			$changeheader.removeClass();
			$changemain.removeClass();
			$changelay_background.removeClass();
			$changebody.removeClass();
			$changeheader.addClass("header header_3");
			$changemain.addClass("main main_3");
			$changelay_background.addClass("lay_background_3");
			$changebody.addClass("body_3");
		} else if ($valofskin == '4') {
			$changeheader.removeClass();
			$changemain.removeClass();
			$changelay_background.removeClass();
			$changebody.removeClass();
			$changeheader.addClass("header header_4");
			$changemain.addClass("main main_4");
			$changelay_background.addClass("lay_background_4");
			$changebody.addClass("body_4");
		} else if ($valofskin == '5') {
			$changeheader.removeClass();
			$changemain.removeClass();
			$changelay_background.removeClass();
			$changebody.removeClass();
			$changeheader.addClass("header header_5");
			$changemain.addClass("main main_5");
			$changelay_background.addClass("lay_background_5");
			$changebody.addClass("body_5");
		} else if ($valofskin == '6') {
			$changeheader.removeClass();
			$changemain.removeClass();
			$changelay_background.removeClass();
			$changebody.removeClass();
			$changeheader.addClass("header header_6");
			$changemain.addClass("main main_6");
			$changelay_background.addClass("lay_background_6");
			$changebody.addClass("body_6");
		} else if ($valofskin == '7') {
			$changeheader.removeClass();
			$changemain.removeClass();
			$changelay_background.removeClass();
			$changebody.removeClass();
			$changeheader.addClass("header header_7");
			$changemain.addClass("main main_7");
			$changelay_background.addClass("lay_background_7");
			$changebody.addClass("body_7");
		}
    });
    $('#change_skin_save_btn').live("click", function(){
    	var val_skin = $valofskin;
    	if (val_skin > '7' || val_skin < '0') {
    		val_skin = '0';
    	}
    	var $infoLoading = $('<img/>').attr({'src': baseUrl + 'Public/image/common/ajax_wait.gif', 'title': '提交中...请稍等'});
    	$("#change_skin_save_info").show().html($infoLoading);
    	$.ajax({
            type: "POST",
            url: baseUrl + "ajax/saveskin",
            data: "skin_value=" + val_skin,
            dataType: "json",
            success:function(msg){
            	if (msg.status == 'yes') {
            		$("#change_skin_save_info").fadeIn("fast").html("<span class='f12'><span class='icon_right'></span> " + msg.info + "</span>").delay(1000).fadeOut("slow");
            		window.location = baseUrl + "stream";
            	} else {
            		$("#change_skin_save_info").fadeIn("fast").html("<span class='f12'><span class='icon_wrong'></span> " + msg.info + "</span>").delay(1000).fadeOut("slow");
            	}
            }
        });
    });
	
	/**
	 * TODO
     * pull message
     */
    mseeageNums();

    $('.stream_top_notice_info .icon_index_wrong').click(function(){
        $(this).parent().slideUp('fast');
    });
    var atpattern = /@[^@]+?(?=[\s:：(),。])/g;
    var contentOk = 'no';
	var imageNums = 0;
    $('.stream_list_ul_li').hover(function(){
        $(this).find('.priority_shield').show();
    }, function(){
        $(this).find('.priority_shield').hide();
    });


    /**
     * at part
     *
     * load textarea tool
     */
	if ("6.0" == $.browser.version || "7.0" == $.browser.version || "8.0" == $.browser.version) {

	} else {
	    var elem = document.getElementById("s_t_textarea");
	    var focus = document.getElementById("cursorfocus");
	    var input = function () {
	    	var textareaoffset = $(".main").offset();
	    	var addleftoffset = textareaoffset.left + 170;
	    	var pos = textareaTools.getInputOffset(elem, true, addleftoffset);
	    	focus.style.left = (pos.left - addleftoffset) + 'px';
	    	focus.style.top = (pos.top - 100) + 'px';
	    	focus.style.display = 'block';
	    };
	    textareaTools._addEvent(elem, 'input', input);
	    textareaTools._addEvent(elem, 'propertychange', input);
	    textareaTools._addEvent(elem, 'click', input);
    }

    //key check
    var atswitch = 'off';
    $("#s_t_textarea").keyup(function(){
    	var textareacontent = $('#s_t_textarea').val();
    	var currentcontent = $(this).insertAtCaret();
    	var contentlength = currentcontent.length;
        lastletter = currentcontent.substr(contentlength - 1);
        if (atswitch == 'on') {

            /**
             *
             * 1.@[^\s:：，,.。@]*(?=[\s:：，,.。])
             * 2./@[^@]+?(?=[\s:：(),.。])/g
             * 3.(@[\\u4E00-\\u9FA5A-Za-z0-9_]+)
             *
             * var re = new RegExp("(@[\\u4E00-\\u9FA5A-Za-z0-9_]+)", "g");
             * var s = "<span class=\"fb red_l\">$1<\/span>";
             * data = currentcontent.replace(re, s);
             */
            if (lastletter == ' ') {
            	var result = textareacontent.match(atpattern);
                if (result != null){
                	atswitch = 'off';
                	$('.auto_load_div').slideUp('normal');
                	$.ajax({
                		type: "POST",
                		dataType: "json",
                		url: baseUrl + "ajax/at",
                		data:{autospacefillatcontent: result},
                		success:function(msg){
                			if (msg.status == 'ok') {
                				$('#s_t_textarea_div_at').slideDown('normal').empty();
                				for(var i = 0; i < msg.data.length; i++) {
                					$('#s_t_textarea_div_at').append(
                						"<li><a href='" + baseUrl + "wo/" + msg.data[i].uid + "' title='" + msg.data[i].nickname + "' target='_blank'>"
                						+ "<img src='" + msg.data[i].icon_url + "' height='25' class='radius3' /></a></li>");
                				}
                			}
                		}
                	});
                }
            } else {
            	$.ajax({
            		type: "POST",
            		dataType: "json",
            		url: baseUrl + "ajax/at",
            		data:{autofillatcontent: currentcontent},
            		success:function(msg){
            			if (msg.status == 'ok') {
            				$('.auto_load_div').slideDown('normal');
            				if ("6.0" == $.browser.version || "7.0" == $.browser.version || "8.0" == $.browser.version) {
            					$('.auto_load_div').css({ position: "absolute", left: "878px", top: "115px" });
            				} else {
            					$('.auto_load_div').css({ position: "absolute", left: focus.style.left, top: focus.style.top });
            				}
            				$('.auto_load_div').html('<ul class="at_auto_match_ul"></ul>');
            				for(var i = 0; i < msg.data.length; i++) {
                	    		$('.at_auto_match_ul').append(
                	    			"<li class='at_auto_match_ul_li' value='" + msg.data[i].uid + "' url='" + msg.data[i].icon_url + "'><a>" + msg.data[i].nickname + "</a></li>"
                	    	    );
                	    	}
            			}
            		}
            	});
            }
        } else {
        	$('.auto_load_div').slideUp('normal');
        }

        if (lastletter == "@") {
        	$('.auto_load_div').slideDown('normal');
        	if ("6.0" == $.browser.version || "7.0" == $.browser.version || "8.0" == $.browser.version) {
        		$('.auto_load_div').css({ position: "absolute", left: "878px", top: "115px" });
        	} else {
				$('.auto_load_div').css({ position: "absolute", left: focus.style.left, top: focus.style.top });
			}
        	$('.auto_load_div').html('<ul class="at_auto_match_ul f12 gray"><li>输入用户昵称,空格结束</li></ul>');
        	atswitch = 'on';
        }

        if (textareacontent.length > 0) {
        	var letterlimit = 222 - textareacontent.length;
        	if (letterlimit > 0) {
        		$('.s_l_textarea_info').html('还能输入<span class="blue">' + letterlimit + '</span>个字');
        		contentOk = 'yes';
        	} else {
        		$('.s_l_textarea_info').html('超出字数限制<span class="red">' + letterlimit + '</span>');
        		contentOk = 'morethenlimit';
        	}
        }
    });

    //at chose
    $('.at_auto_match_ul_li').live("click", function(){
    	var iconurl = $(this).attr('url');
    	var nickname = $(this).text();
    	var uid = $(this).attr('value');
    	$('#s_t_textarea_div_at').fadeIn('fast').append(
    		"<li><a href='" + baseUrl + "wo/" + uid + "' title='" + nickname + "' target='_blank'>"
    		+ "<img src='" + iconurl + "' height='25' class='radius3' /></a></li>");
    	$('.auto_load_div').slideUp('normal');
    	atswitch = 'off';

    	var currentcontent = $("#s_t_textarea").insertAtCaret();
    	var splitcurrentcontent = currentcontent.split("@");
    	var handleatcontent = '';
    	for(var i = 0; i < splitcurrentcontent.length; i++) {
    		if (i == splitcurrentcontent.length - 1) {
    			handleatcontent += '@' + nickname + ' ';
    		} else if (i >= 1) {
    			handleatcontent += '@' + splitcurrentcontent[i];
    		} else {
    			handleatcontent += splitcurrentcontent[i];
    		}
    	}

    	// IE temp support
    	if (document.selection) {
    		$("#s_t_textarea").val(handleatcontent);
    	} else {
    		AddOnPos("s_t_textarea",handleatcontent);
    	}
    });

    /**
     * icon part
     */
    $('#textareaicon').click(function(){
        $('#emotionbox').fadeIn('fast');
        $(".emotionbox_show_ul").load(baseUrl + "other/loademotion");
        return false;
    });
    $('#emotionbox_close').click(function(){
        $('#emotionbox').slideUp('fast');
    });
    $('.emotionbox_show_ul img').live("click", function(){
        var imgtitle = $(this).attr('title');
        var imgtitlemark = '[' + imgtitle + ']';
        var textareanow = $('#s_t_textarea').val() + imgtitlemark;
        $('#s_t_textarea').val(textareanow);
        $('#emotionbox').fadeOut('fast');
        contentOk = 'yes';
        //important here, refuse default explorer action
        return false;
    });
    $('.emotionbox_change_page').click(function(){
    	$(".emotionbox_change_page").removeClass('bg_emotionbox_page_select');
    	$(this).addClass('bg_emotionbox_page_select');
    	$page = $(this).attr("value");
        $(".emotionbox_show_ul").empty().load(baseUrl + "other/loademotion" + "?page=" + $page);
    });
    
    /**
     * icon reply part
     */
    $(".comment_textareaicon_reply").live('click', function(e){
        var positionleft = e.pageX + 10;
    	var positiontop = e.pageY + 10;
    	$replytextarea = $(this).parent().find('.textarea_style');
        var emotionboxhtml = '<p class="emotionbox_close_p">'
		+ '<a class="emotionbox_change_page bg_emotionbox_page_select" title="基本表情" value="1">基本表情</a>'
		+ '<a class="emotionbox_change_page" title="微博" value="2">微博</a>'
		+ '<a class="emotionbox_change_page" title="兔斯基" value="3">兔斯基</a>'
		+ '<span class="replyemotionbox_close close_x" title="关闭">×</span>'
		+ '</p><ul class="emotionbox_show_ul_inner"></ul>';
        $(".replyemotionbox").fadeIn("fast").css({ position: "absolute", left: positionleft, top: positiontop }).html(emotionboxhtml);
        $(".emotionbox_show_ul_inner").load(baseUrl + "other/loademotion");
        return false;
    });
    $(".comment_textareaicon_replyinner").live('click', function(e){
        var positionleft = e.pageX + 10;
    	var positiontop = e.pageY + 10;
    	$replytextarea = $(this).parent().find('.textarea_style');
        var emotionboxhtml = '<p class="emotionbox_close_p">'
		+ '<a class="emotionbox_change_page bg_emotionbox_page_select" title="基本表情" value="1">基本表情</a>'
		+ '<a class="emotionbox_change_page" title="微博" value="2">微博</a>'
		+ '<a class="emotionbox_change_page" title="兔斯基" value="3">兔斯基</a>'
		+ '<span class="replyemotionbox_close close_x" title="关闭">×</span>'
		+ '</p><ul class="emotionbox_show_ul_inner"></ul>';
        $(".replyemotionbox").fadeIn("fast").css({ position: "absolute", left: positionleft, top: positiontop }).html(emotionboxhtml);
        $(".emotionbox_show_ul_inner").load(baseUrl + "other/loademotion");
        return false;
    });
    $(".replyemotionbox_close").live('click', function(){
    	 $(".replyemotionbox").slideUp('fast');
    	 return false;
    });
    $('.emotionbox_show_ul_inner img').live("click", function(){
        var imgtitle = $(this).attr('title');
        var imgtitlemarkin = '[' + imgtitle + ']';
        var textareanow = $replytextarea.val() + imgtitlemarkin;
        $replytextarea.val(textareanow);
        $(".replyemotionbox").fadeOut('fast');
        return false;
    });
    $('.emotionbox_change_page').live('click', function(){
    	$(".emotionbox_change_page").removeClass('bg_emotionbox_page_select');
    	$(this).addClass('bg_emotionbox_page_select');
    	$page = $(this).attr("value");
        $(".emotionbox_show_ul_inner").empty().load(baseUrl + "other/loademotion" + "?page=" + $page);
    });
    
    /**
     * image part
     */
    $('#textareaimg').toggle(
        function(){
            $('.img_upload_form_div').slideDown('fast');
        },
        function(){
            $('.img_upload_form_div').slideUp('fast');
        }
    );

    //from the internet
    $('#img_upload_net_btn').click(function(){
        var netImgUrl = $('#img_upload_net').val();
        if (netImgUrl == '') {
            $('.imgajaxloading_span').fadeIn('fast').html("<span class='f12 red_l'>还没有输入图片地址呢</span>").delay(1000).fadeOut('fast');
        } else {
            if (imageNums > 4) {
                alert('最多一次只能传5张图片');
            } else {
                var uploadNetImgList = "<li class='upload_img_list' url='" + netImgUrl + "'><img src='" + netImgUrl +"' width='80'/><a href='" + netImgUrl +"' target='_blank' class='f12'><span class='icon_search' title='看大图'></span>大图</a> <span class='icon_index_wrong' title='删除'></span></li>";
                $('#image_upload_list_ul').append(uploadNetImgList);
                imageNums++;
            }
        }
    });
    $('.upload_img_list .icon_index_wrong').live('click', function(){
        imageNums--;
        $(this).parent().remove();
    });

    $("#img_upload_btn").click(function(){
        var upload_image_file = $('#upload_form_img_file').val();
        var $infoLoading = $('<img/>').attr({'src': baseUrl + 'Public/image/common/progressbar.gif', 'title': '上传中...请稍等'});
        if (upload_image_file == '') {
            $('.imgajaxloading_span').fadeIn('fast').html("<span class='f12 red_l'>还没有选择图片呢</span>").delay(1000).fadeOut('fast');
        } else {
            if (imageNums > 4) {
                alert('最多一次只能传5张图片');
            } else {
                $('.imgajaxloading_span').fadeIn('fast').html($infoLoading);
                $.ajaxFileUpload({
                	url: baseUrl + 'ajax/imgupload',
                	secureuri: false,
                	fileElementId: 'upload_form_img_file',
                	dataType: 'json',
                	success: function (msg){
                	    if (msg.status == 'uploaded') {
                	        var uploadImgList = "<li class='upload_img_list' url='" + msg.data + "'><img src='" + msg.data +"' width='80'/><a href='" + msg.data +"' target='_blank' class='f12'><span class='icon_search' title='看大图'></span>大图</a> <span class='icon_index_wrong' title='删除'></span></li>";
                	        $('#image_upload_list_ul').append(uploadImgList);
                	        imageNums++;
                	    } else if (msg.status == 'error') {
                	        $('.imgajaxloading_span').fadeIn('fast').html("<span class='f12 red_l'>" + msg.info + "</span>").delay(3000).fadeOut('fast');
                	    }
                	    $('.imgajaxloading_span').fadeOut();
                	}
                });
            }
        }
    });

    /**
     * video part
     */
    $('#textarealink').toggle(
    	function(){
    		$('#post_link_li').slideDown('fast');
    		$('#videourl').val('支持优酷、土豆、酷六、56、乐视、搜狐');
    	},
    	function(){
    		$('#post_link_li').slideUp('fast');
    		$('#videourl').val('');
    	}
    );

    $('#videourl').focus(function(){
    	var videourlvalue = $('#videourl').val();
    	if (videourlvalue == '支持优酷、土豆、酷六、56、乐视、搜狐') {
    		$(this).val('');
    	}
    });

    $('#url_video_btn').click(function(){
    	var videourlvalue = $('#videourl').val();
    	$('.post_link_li_info').slideDown('fast').html('<span class="gray">视频加载中...</span>');
    	if (videourlvalue == '支持优酷、土豆、酷六、56、乐视、搜狐' || videourlvalue == '') {
    		$('.post_link_li_info').slideDown('fast').html('<span class="red_l">视频地址不能为空</span>');
    	} else {
    		$.ajax({
                type: "POST",
        		dataType: "json",
        		url: baseUrl + "ajax/videourl",
        		data:{videourlvalue: videourlvalue},
        		success:function(msg){
                	if (!msg.status) {
                		$('.post_link_li_info').slideDown('fast').html('<span class="red_l">暂不支持该视频地址</span>');
    					return false;
    				} else {
    					var inhtml = "<div style='border:1px #CCC solid; padding:3px; display: inline-block; background:#FFF; border-radius:3px; padding:5px;'>"
    						+ "		<a class='open_vedio' href='javascript:;' alt='"+msg.data.title+"'>"
    						+ "		<img width='136' height='104' src='"+msg.data.img+"' /></a>"
    						+ "		<div class='vedio' style='display:none;'>"
    						+ "			<div>"
    						+ "				<a style='float:right' target='_new' href='"+msg.data.url+"'>"+msg.data.title+"</a>"
    						+ "				<a class='close_vedio' href='javascript:;'>关闭</a>"
    						+ "			</div>"
    						+ msg.data.object
    						+ "		</div>"
    						+ "</div>";
    					$('.post_link_li_info').slideDown('fast').html(inhtml);
    					return false;
    				}
                }
            });
    	}
    });
    $('.open_vedio').live('click', function(){
		$(this).hide();
		$(this).next('.vedio').show();
	});
	$('.close_vedio').live('click', function(){
		$(this).parent().parent().hide();
		$(this).parent().parent().prev('.open_vedio').show();
	});

    /**
     * help part
     */
    $('#textareahelp').toggle(
        function(){
            $(this).addClass("helpleaf_select");
            $('#help_is_input').val('1');
            $('#post_help_reword').slideDown('fast');

            //group msg push close
            $('#post_groupmsgpush_li').slideUp('up');
            $('#groupmsgpush_system').removeAttr('checked');
            $('#groupmsgpush_mail').removeAttr('checked');
        },
        function(){
            $(this).removeClass("helpleaf_select");
            $('#help_is_input').val('0');
            $('#post_help_reword').slideUp('fast');
        }
    );

    //calculate left coins
	$('#reward_coins').click(function(){
	    var total_coins = $('#left_coins_calculate').attr('value');
	    var use_coins = $(this).val();
	    var left_coins = total_coins - use_coins;
	    if (left_coins >= 0) {
	        $('#left_coins_calculate').html("活跃剩余: " + total_coins + '-' + use_coins + '= <span class="fb black_l">' + left_coins + '</span>');
	    } else {
	        $('#left_coins_calculate').html("<span class='red_l'>活跃不够了</span>");
	    }
	});

	/**
	 * group msg push part
	 */
	$('#textareagroupmsgpush').toggle(
		function(){
			$('#post_groupmsgpush_li').slideDown('fast');
			$('#groupmsgpush_system').attr('checked','true');
			$('#groupmsgpush_mail').attr('checked','true');

			//help close
			$('#textareahelp').removeClass("helpleaf_select");
			$('#help_is_input').val('0');
			$('#post_help_reword').slideUp('fast');
		},
		function(){
			$('#post_groupmsgpush_li').slideUp('fast');
			$('#groupmsgpush_system').removeAttr('checked');
			$('#groupmsgpush_mail').removeAttr('checked');
		}
	);

    /**
     * submit
     */
    $("#s_t_submit").click(function(){
        var verification_code_value = $('#verification_code').val();
        var textareacontent = $('#s_t_textarea').val();
        var help_is_input = $('#help_is_input').val();
        var weibo_is_publish = $('#weibo_is_publish').val();

        //store image data in input dom
        var upload_img_url_data = attrListImgValue();
        $('#imageurls').val(upload_img_url_data);
        var atresult = textareacontent.match(atpattern);
        $('#atusers').val(atresult);
        var re = new RegExp("(@[\\u4E00-\\u9FA5A-Za-z0-9_.]+)", "g");
        var s = "<a class=\"getuserinfo\">$1</a>";
        var textareacontentdata = textareacontent.replace(re, s);
        $("#textareacontent").val(textareacontentdata);

        var $infoLoading = $('<img/>').attr({'src': baseUrl + 'Public/image/common/progressbar.gif', 'title': '提交中...请稍等'});
        $('#infotextareacheck').slideDown("fast").html($infoLoading);
        
        if (textareacontent.length > 0) {
        	var letterlimits = 222 - textareacontent.length;
        	if (letterlimits > 0) {
        		contentOk = 'yes';
        	}
        }
        
        if (contentOk == "no") {
            $('#infotextareacheck').slideDown("fast").html("<span class='icon_attention'></span> 发布内容不能为空").delay(1000).slideUp("fast");
        } else if (contentOk == "morethenlimit") {
            $('#infotextareacheck').slideDown("fast").html("<span class='icon_attention'></span> 超出字数限制，精简一下:)").delay(1000).slideUp("fast");
        } else if (verification_code_value == "") {
            $('#infotextareacheck').slideDown("fast").html("<span class='icon_attention'></span> 验证码不能为空").delay(1000).slideUp("fast");
        } else if (contentOk == "yes") {
            $.post(baseUrl + "stream", $("#s_t_form").serialize(), function(msg){
            	if (msg.status == "verifi") {
            		$('#infotextareacheck').slideDown("fast").html("<span class='icon_attention'></span>请输入验证码").delay(1000).slideUp("fast");
            		$('.verification_code_p').fadeIn('fast');
            		$('#verification_code_img').attr({'src': baseUrl + 'other/verifi' });
            		$('#verification_code').val('');
            	} else if (msg.status == "error") {
            		$('#infotextareacheck').slideDown("fast").html("<span class='icon_wrong'></span>" + msg.info).delay(1000).slideUp("fast");
            	} else if (msg.status == "ok") {
                    //weibo publish
                    if (weibo_is_publish == 'on') {
                    	var uploadImageWeibo = $('.upload_img_list:eq(0)').attr('url');
                    	if (uploadImageWeibo != '') {
	                        WB2.anyWhere(function(W){
	                        	W.parseCMD("/statuses/upload_url_text.json", function(sResult, bStatus){ },{ status : textareacontent, url : uploadImageWeibo },{ method: 'post' });
	                        });
                    	} else {
                    		WB2.anyWhere(function(W){
	                        	W.parseCMD("/statuses/update.json", function(sResult, bStatus){ },{ status : textareacontent },{ method: 'post' });
	                        });
                    	}
                    }
                    if (help_is_input == '1') {
                    	var uploadImageWeibo = $('.upload_img_list:eq(0)').attr('url');
                    	textareacontent = "#求助#" + textareacontent + " http://www.ihelpoo.com/item/help/" + msg.info;
                    	if (uploadImageWeibo != '') {
	                        WB2.anyWhere(function(W){
	                        	W.parseCMD("/statuses/upload_url_text.json", function(sResult, bStatus){ },{ status : textareacontent, url : uploadImageWeibo },{ method: 'post' });
	                        });
                    	} else {
                    		WB2.anyWhere(function(W){
	                        	W.parseCMD("/statuses/update.json", function(sResult, bStatus){ },{ status : textareacontent },{ method: 'post' });
	                        });
                    	}
                    }
                    window.location = baseUrl + 'stream/index/newreply';
                    if (help_is_input == '1') {
                    	notice.send('system', msg.data);
                    }
                } else {
                    alert('something wrong');
                }
            }, "json");
        }
    });
    
    /**
     * enter keydown submit
     */
    $(window).keydown(function(e){
    	if(e.keyCode == 13 && e.ctrlKey) {
    		$('#s_t_submit').click();
    		document.body.focus();
    	}
    });

    /**
     * plus part
     */
    $('.plus_button').click(function(){
        var $thisButton = $(this);
        var $region = $('#plus_view_region_'+$(this).attr('value'));
        $.ajax({
            type: "POST",
            url: baseUrl+"stream/plusToggle",
            data: {'plusSid':$(this).attr('value')},
            dataType: "json",
            success:function(msg){
            	if (msg.status == 'add') {
            		notice.send('system', msg.info);
            	}
            	if (msg.status != 'error') {
            		$region.html('('+msg.data+')');
            	}
            }
        });
    });
    
    var t_plus;
    $(".plus_button").mouseenter(function(e){
    	$this = $(this);
    	t=setTimeout(function(){
    		var sidString = $this.attr('value');
    		var positionleft = e.pageX + 10;
        	var positiontop = e.pageY + 10;
    		$.ajax({
                type: "POST",
        		dataType: "json",
        		url: baseUrl + "ajax/plusview",
        		data:{sidString: sidString},
        		success:function(result){
        			if (result.status == 'yes') {
        				$('.record_plus_div').css({ position: "absolute", left: positionleft, top: positiontop }).fadeIn('fast').html(result.data);
        			} else {
        				$('.record_plus_div').css({ position: "absolute", left: positionleft, top: positiontop }).fadeIn('fast').html(result.info);
        			}
                }
            });
    	},1000);
    }).mouseleave(function(){
    	clearTimeout(t_plus);
    	$('.record_plus_div').hover(function(){},
    	function(){
    		$(this).fadeOut("fast");
    	});
    });
    
    /**
     * diffusion part
     */
    $('.diffusion').toggle(
        function(){
        	$diffusionRecordObj = $(this);
            $(this).parent().parent().find('.diffusion_view_div_box').slideDown('fast');
            if ($commentViewDivBox != '') {
            	$commentViewDivBox.slideUp("fast");
            	$(this).parent().find('.comment_button').attr({isclick: 'false'});
            }
        },
        function(){
        	$(this).parent().parent().find('.diffusion_view_div_box').slideUp('fast');
        }
    );
    
    $('.diffusion_view_btn').click(function(){
        var $diffusion_view = $(this).parent().find('.diffusion_view_textarea').val();
        if ($diffusion_view == '说点什么吧...') {
        	$diffusion_view = '';
        }
        var diffusionSid = $diffusionRecordObj.attr('value');
        $.ajax({
            type: "POST",
            url: baseUrl + "stream/diffuseIt",
            data: {'diffusionSid':diffusionSid, 'diffusionView':$diffusion_view},
            dataType: "json",
            success:function(result){
            	var infohtml = "<p align='left'>" + result.info + "</p> <a class='btn_cancel'>确定</a>";
                notice.send('system', result.data);
            	ajaxInfo(infohtml);
                if (result.info != '你已经扩散了这条信息') {
                	$diffusionRecordObj.append('<span class="red_l">+1</span>');
                }
            }
        });
        $('.diffusion_view_div_box').slideUp('slow');
    });

    $('.diffusion_list_sure').live("click", function(){
        $('#infotextareacheck').slideUp('normal');
    });
    
    $('.diffusion_view_textarea').focus(function(){
    	$(this).next().text('扩散');
    	$(this).css({width: '350px', height: '30px'});
    	var textareaValue = $(this).val();
    	if (textareaValue == '说点什么吧...') {
    		$(this).val('');
    	}
    });
    $('.diffusion_view_textarea').focusout(function(){
    	var textareaValue = $(this).val();
    	if (textareaValue == '') {
    		$(this).val('说点什么吧...');
    		$(this).next().text('直接扩散');
    		$(this).css({width: '200px', height: '18px'});
    	}
    });
    
    /**
     * ajax comment part
     */
    $('.comment_button').click(function(){
    	var $this = $(this);
    	$commentViewDivBox = $this.parent().parent().find(".comment_view_div_box");
    	var commmentSid = $this.attr('value');
    	var commentBtnIsClick = $this.attr('isclick');
    	if (commentBtnIsClick == 'false') {
    		$this.attr({isclick: 'true'});
    		$commentViewDivBox.slideDown("fast").html("<img src='/Public/image/common/ajax_wait.gif' /> <span class='f12 gray'>正在加载中，请稍等...</span>");
    		$.ajax({
	            type: "POST",
	            url: baseUrl + "stream/ajaxcomment",
	            data: {'commentSid':commmentSid},
	            dataType: "html",
	            success:function(data){
	            	$commentViewDivBox.slideDown("fast").html(data);
	            }
	        });
    	} else {
    		$this.attr({isclick: 'false'});
    		$commentViewDivBox.slideUp("fast");
    	}
    });
    
    $('.c_v_d_b_ul_li_content_reply_btn').live('click',function(){
    	var $commentViewDivBoxReply = $(this).parent().parent().find('.comment_view_div_box_replyinner');
    	$commentViewDivBoxReply.slideDown('fast');
    });
    
    /**
     * comment
     */
    $('.comment_reply_submit').live('click', function(){
    	var $this = $(this);
        var i_comment_textarea = $(this).parent().find('textarea').val();
        if (i_comment_textarea == '') {
            ajaxInfo('写点东西吧，评论不能为空');
        } else if (i_comment_textarea.length > 200) {
            ajaxInfo('内容太长了 不能超过200个字符');
        } else {
        	i_comment_textarea = i_comment_textarea + ' ';
	        var atpattern = /@[^@]+?(?=[\s:：(),。])/g;
	        var atresult = i_comment_textarea.match(atpattern);
	        var re = new RegExp("(@[\\u4E00-\\u9FA5A-Za-z0-9_.]+)", "g");
	        var s = "<a class=\"getuserinfo\">$1</a>";
	        var textareacontentdata = i_comment_textarea.replace(re, s);
	        
	        var sid = $this.parent().attr("sid");
	        var cid = $this.parent().attr("cid");
	        var toid = $this.parent().attr("toid");
	        var textareacontent = textareacontentdata;
	        var imageurl = '';
	        var verificationcode = $this.parent().find(".comment_reply_verification_streamcode").attr("value");
	        var atusers = atresult;
	        $.ajax({
	            type: "POST",
	            url: baseUrl + "item/sayajax",
	            data: {'sid' : sid , 'cid' : cid , 'toid' : toid , 'textareacontent' : textareacontent , 'imageurl' : imageurl , 'verificationcode' : verificationcode , 'atusers' : atusers},
	            dataType: "json",
	            success:function(msg){
	            	if (msg.status == 'verifi') {
	            		$this.parent().find('.comment_reply_verification_stream').fadeIn('fast');
	            		$this.parent().find('.comment_reply_verification_stream_code_img').attr({'src': baseUrl + 'other/verifi?imageid=' + Math.random() });
	            		$this.parent().find('.comment_reply_verification_streamcode').val('');
	            	} else if (msg.status == 'yes') {
	                    $('.comment_view_div_box_replyinner').slideUp('fast');
	                    $('.comment_view_div_box_reply_textarea').val('');
	                    $('.comment_view_div_box_replyinner_textarea').val('');
	                    
	                    var commentContent = '<li>'
	                    + '<a href="/wo/' + msg.data.uid + '" class="getuserinfo c_v_d_b_ul_li_icon" userid="' + msg.data.uid + '"><img src="' + msg.data.uidicon + '" height="30" class="radius3" /></a>'
	                    + '<p class="c_v_d_b_ul_li_content">'
	                    + '<a href="/wo/' + msg.data.uid + '" class="getuserinfo" userid="' + msg.data.uid + '">' + msg.data.uidnickname + ':</a> '
	                    + '<span class="gray fb">';
	                    if (msg.data.toid != '') {
	                        commentContent += '[回复:' + msg.data.toidnickname + ']';
	                    }
	                    commentContent += '</span>'
	                    + msg.data.content
	                    + ' <span class="gray">(' + msg.data.time + ')</span>'
	                    + '</p>'
	                    + '<span class="c_v_d_b_ul_li_content_reply">'
	                    + '<a class="c_v_d_b_ul_li_content_del gray" value="' + msg.data.cid + '">删除</a>'
	    		    	+ '</span>';
	                    $commentViewDivBox.find('.comment_view_div_box_ul').prepend(commentContent);
	                    $this.parent().find('.comment_reply_verification_stream').hide();
	                    $this.parent().find('.comment_reply_verification_streamcode').val('999');
	                    notice.send('comment', msg.info);
	                } else {
	                    ajaxInfo(msg.info);
	                }
	            }
	        });
        }
    });
    
    /**
     * commment delete
     */
    $('.c_v_d_b_ul_li_content_del').live('click', function(){
    	var deletecid = $(this).attr("value");
    	$deleteCommentLi = $(this).parent().parent();
    	$deleteCommentLi.css("backgroundColor", "#FFFA85");
    	var infohtml = "<p>确定删除？</p> <a class='btn_sure' id='delete_comment' value='"+deletecid+"'>确定</a><a class='btn_cancel'>取消</a>";
    	ajaxInfo(infohtml);
    });
    
    $('#delete_comment').live('click', function(){
    	var deletecid = $(this).attr("value");
    	$.ajax({
            type: "POST",
            url: baseUrl + "item/del",
            data: "delcomment=" + deletecid,
            dataType: "json",
            success:function(msg){
                $deleteCommentLi.slideUp('fast');
                $("#ajax_info_div").fadeOut("fast");
        		$("#ajax_info_div_outer").hide();
            }
        });
    });
    
    $('.btn_cancel').live('click', function(){
    	$("#ajax_info_div").fadeOut("fast");
		$("#ajax_info_div_outer").hide();
		$deleteCommentLi.css("backgroundColor", "#FFF");
    });
    
    /**
     * enlarge image
     */
    var imageTempContent = '';
    $('.s_li_p_content_image img').live('click', function(){
    	/**
    	 * var totalImageNums = $(this).parent().find("img").size();
    	 * totalImageNums > 1
    	 * more than one image , should list next page
    	 */
    	var $slipContentImagePart = $(this).parent();
    	var imagelodingmarginheight = $slipContentImagePart.height();
    	var enlargeSwitch = $(this).attr('enlargeswitch');
    	if (enlargeSwitch != 'on') {
	    	var imageurl = $(this).attr('src');
	    	if (imageurl.match("ihelpoo-public") != '') {
	    		var reg = new RegExp("thumb_","g");
	    		var imageurllarge = imageurl.replace(reg,"");
	    	}
	    	var imageTempContent = $(this).parent().html();
	    	$(this).parent().val(imageTempContent);
	    	var $imageobjectpart = $(this).parent().html('<p class="f12 s_li_p_content_image_title"><a href="'+imageurllarge+'" target="_blank"><span class="icon_plus"></span>查看原图</a> <a class="s_li_p_content_image_title_up"><span class="icon_up"></span>收起</a></p><img class="enlargeimg" src="'+baseUrl+'Public/image/common/ajax_wait_login.gif" enlargeswitch="on" title="点击缩小" />');
	    	var $imageobject = $imageobjectpart.find('.enlargeimg');
	    	var imagelenlargeheight = $imageobject.height();
	    	if (imagelenlargeheight > imagelodingmarginheight) {
	    		$slipContentImagePart.attr({'style': 'min-height:'+imagelodingmarginheight+'px'});
	    	} else {
	    		$slipContentImagePart.attr({'style': 'min-height:'+imagelenlargeheight+'px'});
	    	}
	    	$imageobject.attr({'src':imageurllarge});
	    	$imageobject.ready(function(){
	    		$imageobject.attr({'src':imageurllarge});
			});
    	} else {
    		var imageTempContentBack = $(this).parent().val();
    		$(this).parent().html(imageTempContentBack);
    	}
    });
    $('.s_li_p_content_image_title_up').live('click', function(){
    	var imageTempContentBack = $(this).parent().parent().val();
		$(this).parent().parent().html(imageTempContentBack);
	});

    /**
     * video play
     */
    $('.s_li_p_content_mv_img_p').click(function(){
    	$(this).hide();
    	$(this).parent().find('.s_li_p_content_mv_object_p').slideDown('fast');
    });

    $('.s_li_p_content_mv_object_up').click(function(){
    	$(this).parent().hide();
    	$('.s_li_p_content_mv_img_p').show();
    });

    /**
	 * scroll part
	 */
	var scrollTopSwitch = 'off';
	$(window).scroll(function() {
		var bodyHeight = $('body').height();
		var scrollHeight = $(this).scrollTop();
		var pageHeight = $(this).height() - 60;
		var mainoffset = $('.main').offset();
	    var mainpositionleft = mainoffset.left - 35;
	    $(".scroll_float_div").css({right : mainpositionleft});
		if (bodyHeight == scrollHeight + pageHeight) {
			//
		}
		if (scrollHeight > 500 && scrollTopSwitch == 'off') {
			$('.scroll_float_div').fadeIn('fast');
			scrollTopSwitch = 'on';
		} else if (scrollHeight < 500 && scrollTopSwitch == 'on') {
			$('.scroll_float_div').fadeOut('normal');
			scrollTopSwitch = 'off';
		}
	});
	$('#scroll_top_btn').click(function(){
		$("html,body").animate({scrollTop: 0}, 800);
		scrollTopSwitch = 'off';
	});

	/**
	 * hide repate li
	 */
	if ("6.0" != $.browser.version) {
		$('.steam_repate_li').before('<a class="steam_repate_li_show" title="查看更多 同一用户连续信息"><span class="icon_plus"></span></a>')
		$('.steam_repate_li_show').live('click', function(){
		    $(this).next().slideDown('fast');
		    $(this).hide();
		});
	}
});

function attrListImgValue(){
    var uploadImage1 = $('.upload_img_list:eq(0)').attr('url');
    var uploadImage2 = $('.upload_img_list:eq(1)').attr('url');
    var uploadImage3 = $('.upload_img_list:eq(2)').attr('url');
    var uploadImage4 = $('.upload_img_list:eq(3)').attr('url');
    var uploadImage5 = $('.upload_img_list:eq(4)').attr('url');
    var uploadImageAll = uploadImage1 + "---" + uploadImage2 + "---" + uploadImage3 + "---" + uploadImage4 + "---" + uploadImage5;
    return uploadImageAll;
}

function AddOnPos(FieldId, myValue)
{
	var myField = document.getElementById(FieldId);

	if (myField.selectionStart || myField.selectionStart == '0')  {

		//MOZILLA/NETSCAPE support
		var startPos = myField.selectionStart;
		var endPos = myField.selectionEnd;

		// save scrollTop before insert
		var restoreTop = myField.scrollTop;
		myField.value = myValue + myField.value.substring(endPos,myField.value.length);
		if (restoreTop > 0) {
			// restore previous scrollTop
			myField.scrollTop = restoreTop;
		}
		myField.focus();
		myField.selectionStart = startPos + myValue.length;
		myField.selectionEnd = startPos + myValue.length;
	} else {
		myField.value = myValue;
		myField.focus();
	}
}

function mseeageNums() {
	$.ajax({
        type: "POST",
        url: baseUrl + "ajax/getmessage",
        global: false,
        data:{acquireseconds: 'default'},
        dataType: "json",
        success:function(msg){
        	if (msg.status == 'ok') {
        		var acquiremilliseconds = msg.data.acquireSeconds;
        		if (msg.data.messageSystemNums > 0) {
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
        		//setTimeout('mseeageNums()', acquiremilliseconds);
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
//            setTimeout('mseeageNums()', 1000);
        }
    });
	$('#message_talk_nums_span_close').click(function(){
		$('#message_talk_nums_div').fadeOut('fast');
	});
}