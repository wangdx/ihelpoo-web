$().ready(function(){
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
});