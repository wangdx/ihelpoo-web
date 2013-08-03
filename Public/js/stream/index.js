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
	var mainoffset = $('.main').offset();
    var mainpositionleft = mainoffset.left + 240;
	$("#change_skin_div").css({left : mainpositionleft}).slideDown("fast");
	
	$(".change_skin_select_a").click(function(){
		var $changeheader = $(".header");
		var $changemain = $(".main");
		var $changelay_background = $("#layBackground");
		var $changebody = $("body");
		$val = $(this).attr("value");
		if ($val == '0') {
			$changeheader.removeClass();
			$changemain.removeClass();
			$changelay_background.removeClass();
			$changebody.removeClass();
			$changeheader.addClass("header");
			$changemain.addClass("main");
			$changelay_background.addClass("lay_background");
			$changebody.addClass("body");
		} else if ($val == '1') {
			$changeheader.removeClass();
			$changemain.removeClass();
			$changelay_background.removeClass();
			$changebody.removeClass();
			$changeheader.addClass("header header_1");
			$changemain.addClass("main main_1");
			$changelay_background.addClass("lay_background_1");
			$changebody.addClass("body_1");
		} else if ($val == '2') {
			$changeheader.removeClass();
			$changemain.removeClass();
			$changelay_background.removeClass();
			$changebody.removeClass();
			$changeheader.addClass("header header_2");
			$changemain.addClass("main main_2");
			$changelay_background.addClass("lay_background_2");
			$changebody.addClass("body_2");
		} else if ($val == '3') {
			$changeheader.removeClass();
			$changemain.removeClass();
			$changelay_background.removeClass();
			$changebody.removeClass();
			$changeheader.addClass("header header_3");
			$changemain.addClass("main main_3");
			$changelay_background.addClass("lay_background_3");
			$changebody.addClass("body_3");
		} else if ($val == '4') {
			$changeheader.removeClass();
			$changemain.removeClass();
			$changelay_background.removeClass();
			$changebody.removeClass();
			$changeheader.addClass("header header_4");
			$changemain.addClass("main main_4");
			$changelay_background.addClass("lay_background_4");
			$changebody.addClass("body_4");
		} else if ($val == '5') {
			$changeheader.removeClass();
			$changemain.removeClass();
			$changelay_background.removeClass();
			$changebody.removeClass();
			$changeheader.addClass("header header_5");
			$changemain.addClass("main main_5");
			$changelay_background.addClass("lay_background_5");
			$changebody.addClass("body_5");
		} else if ($val == '6') {
			$changeheader.removeClass();
			$changemain.removeClass();
			$changelay_background.removeClass();
			$changebody.removeClass();
			$changeheader.addClass("header header_6");
			$changemain.addClass("main main_6");
			$changelay_background.addClass("lay_background_6");
			$changebody.addClass("body_6");
		} else if ($val == '7') {
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
    	var val_skin = $val;
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
            		$("#change_skin_save_info").html("<span class='f12'><span class='icon_right'></span>" + msg.info + "</span>").delay(1000).fadeOut("slow");
            	} else {
            		$("#change_skin_save_info").html("<span class='f12'><span class='icon_wrong'></span>" + msg.info + "</span>").delay(1000).fadeOut("slow");
            	}
            }
        });
    });
	
	/**
     * pull message
     */
    mseeageNums();

    $('.stream_top_notice_info .icon_index_wrong').click(function(){
        $(this).parent().slideUp('fast');
    });
    var atpattern = /@[^@]+?(?=[\s:：(),。])/g;
    var contentOk = 'no';
	var imageNums = 0;
    $('.stream_list_ul li').hover(function(){
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
                						"<li><a href='" + baseUrl + "stream/u/" + msg.data[i].uid + "' title='" + msg.data[i].nickname + "' target='_blank'>"
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
    		"<li><a href='" + baseUrl + "stream/u/" + uid + "' title='" + nickname + "' target='_blank'>"
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
        imgtitlemark = '[' + imgtitle + ']';
        var textareanow = $('#s_t_textarea').val() + imgtitlemark;
        $('#s_t_textarea').val(textareanow);
        $('#emotionbox').fadeOut('fast');
        contentOk = 'yes';
        //important here, refuse default explorer action
        return false;
    });
    $('.emotionbox_change_page').click(function(){
    	$(".emotionbox_change_page").removeClass('bg_gray');
    	$(this).addClass('bg_gray');
    	$page = $(this).text();
        $(".emotionbox_show_ul").empty().load(baseUrl + "other/loademotion" + "?page=" + $page);
    });

    /**
     * image part
     */
    $('#textareaimg').toggle(
        function(){
            $('.img_upload_form_div').slideDown('fast');
        },
        function(){
            $('.img_upload_form_div').slideUp('up');
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
        var $infoLoading = $('<img/>').attr({'src': baseUrl + 'Public/image/common/progressbar.gif', 'title': '加载中...请稍等'});
        if (upload_image_file == '') {
            $('.imgajaxloading_span').fadeIn('fast').html("<span class='f12 red_l'>还没有选择图片呢</span>").delay(1000).fadeOut('fast');
        } else {
            if (imageNums > 4) {
                alert('最多一次只能传5张图片');
            } else {
                $(this).ajaxStart(function(){
                	$('.imgajaxloading_span').fadeIn('fast').html($infoLoading);
                }).ajaxComplete(function(){
                	$infoLoading.remove();
                });
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
                	        //alert(msg.info);
                	    }
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
			$('#post_groupmsgpush_li').slideUp('up');
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
        $(this).ajaxStart(function(){
        	$('#infotextareacheck').slideDown("fast").html($infoLoading);
        }).ajaxStop(function(){
        	$infoLoading.remove();
        });
        
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
                        WB2.anyWhere(function(W){
                        	W.parseCMD("/statuses/upload_url_text.json", function(sResult, bStatus){ },{ status : textareacontent, url : uploadImageWeibo },{ method: 'post' });
                        });
                    }
                    window.location = baseUrl + 'stream/index/newreply';
                } else {
                    alert('something wrong');
                }
            }, "json");
        }
    });

    /**
     * diffusion part
     */
    $('.diffusion').click(function(){
        $('.diffusion_view_div_box').slideUp('slow');
        diffusion_view_div_box = $(this).parent().parent().find('.diffusion_view_div_box');
        diffusion_view_div_box.slideDown('fast');
    });
//    $('.diffusion_view_div_box').mouseout(function(){
//        $(this).slideUp('slow');
//    });

    $('.plus_button').click(function(){
        var $thisButton = $(this);
        var $region = $('#plus_view_region_'+$(this).attr('value'));
        $.ajax({
            type: "POST",
            url: baseUrl+"stream/plusToggle",
            data: {'plusSid':$(this).attr('value')},
//            datatype: "html",
            success:function(data){
//                console.log($(this).parent().find('.plus_count_region').val());
//                $(this).parent().find('.plus_count_region').text(data);
//                alert($(this).attr('class'));
                $region.text('('+data+')');
//                $thisButton.append('<span class="red_l">data</span>');
            }
        });
    });
    $('.diffusion_view_btn').click(function(){

        var $diffusion_view = $(this).parent().find('.diffusion_view_textarea').val();

        var diffusionSid = $('.diffusion').attr('value');
        var $thisDiffusion = $('.diffusion');
        $.ajax({
            type: "POST",
            url: baseUrl + "stream/ajax",
            data: {'diffusionSid':diffusionSid, 'diffusionView':$diffusion_view},
            datatype: "html",
            success:function(data){
                $('#infotextareacheck').slideDown('normal').html('<div class="diffusion_list">'+data+'<br /><a class="diffusion_list_sure btn">确定</a></div>');
                if (data != '你已经扩散了这条信息') {
                    $thisDiffusion.append('<span class="red_l">+1</span>');
                }
            }
        });
        $('.diffusion_view_div_box').slideUp('slow');
    });

    $('.diffusion_list_sure').live("click", function(){
        $('#infotextareacheck').slideUp('normal');
    });

    /**
     * enlarge image
     */
    var imageTempContent = '';
    $('.s_li_p_content_image img').live('click', function(){
    	var totalImageNums = $(this).parent().find("img").size();
    	if (totalImageNums > 1) {
    		
    		/**
    		 * more than one image , should list next page
    		 */
    		var enlargeSwitch = $(this).attr('enlargeswitch');
	    	if (enlargeSwitch != 'on') {
		    	var imageurl = $(this).attr('src');
		    	if (imageurl.match("ihelpoo-public") != '') {
		    		var reg = new RegExp("thumb_","g");
		    		var imageurllarge = imageurl.replace(reg,"");
		    	}
		    	imageTempContent = $(this).parent().html();
		    	$(this).parent().html('<p class="f12 s_li_p_content_image_title"><a href="'+imageurllarge+'" target="_blank"><span class="icon_plus"></span>查看原图</a> <a class="s_li_p_content_image_title_up"><span class="icon_up"></span>收起</a></p><img src="'+imageurllarge+'" width="395" enlargeswitch="on" title="点击缩小" /></p>');
	    	} else {
	    		$(this).parent().html(imageTempContent);
	    	}
    	} else {
	    	var enlargeSwitch = $(this).attr('enlargeswitch');
	    	if (enlargeSwitch != 'on') {
		    	var imageurl = $(this).attr('src');
		    	if (imageurl.match("ihelpoo-public") != '') {
		    		var reg = new RegExp("thumb_","g");
		    		var imageurllarge = imageurl.replace(reg,"");
		    	}
		    	imageTempContent = $(this).parent().html();
		    	$(this).parent().html('<p class="f12 s_li_p_content_image_title"><a href="'+imageurllarge+'" target="_blank"><span class="icon_plus"></span>查看原图</a> <a class="s_li_p_content_image_title_up"><span class="icon_up"></span>收起</a></p><img src="'+imageurllarge+'" width="395" enlargeswitch="on" title="点击缩小" /></p>');
	    	} else {
	    		$(this).parent().html(imageTempContent);
	    	}
    	}
    });
    $('.s_li_p_content_image_title_up').live('click', function(){
	    $(this).parent().parent().html(imageTempContent);
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
     * get userinfo
     */
    var t;
    $(".getuserinfo").mouseenter(function(e){
    	$this = $(this);
    	t=setTimeout(function(){
    		var userid = $this.attr('userid');
    		var usernickname = $this.text();
    		var positionleft = e.pageX + 10;
        	var positiontop = e.pageY + 10;
    		$.ajax({
                type: "POST",
        		dataType: "json",
        		url: baseUrl + "ajax/getuserinfo",
        		data:{userid: userid, usernickname: usernickname},
        		success:function(msg){
                	if (!msg.status) {
                		var inhtml = "<span class='red_l'>"+ msg.info + "</span>";
                		$('.user_info_div').css({ position: "absolute", left: positionleft, top: positiontop }).fadeIn('fast').html(inhtml);
    				} else {
    					if (msg.data.sex == 1) {
    						if (msg.data.user_relation == 'priority') {
        						var relationhtml = "<a href='"+baseUrl+"mutual/priority/"+msg.data.uid+"?del' target='_blank' class='btn_quaned' title='取消圈他'>已圈他</a>";
        					} else if (msg.data.user_relation == 'shield') {
        						var relationhtml = "<a href='"+baseUrl+"stream/u/"+msg.data.uid+" class='red_l' title='取消屏蔽' target='_blank'>已经屏蔽了他</a>";
        					} else {
        						var relationhtml = "<a href='"+baseUrl+"mutual/priority/"+msg.data.uid+"' target='_blank' class='btn_quan'>圈他</a>";
        					}
    					} else {
    						if (msg.data.user_relation == 'priority') {
        						var relationhtml = "<span href='"+baseUrl+"mutual/priority/"+msg.data.uid+"?del' class='btn_quaned' title='取消圈她'>已圈她</span>";
        					} else if (msg.data.user_relation == 'shield') {
        						var relationhtml = "<a href='"+baseUrl+"stream/u/"+msg.data.uid+" class='red_l' title='取消屏蔽' target='_blank'>已经屏蔽了她</a>";
        					} else {
        						var relationhtml = "<a href='"+baseUrl+"mutual/priority/"+msg.data.uid+"' target='_blank' class='btn_quan'>圈她</a>";
        					}
    					}
    					if (msg.data.remark != null) {
    						var userremarkhtml = "<a class='f12 black_l' id='user_remark_set' title='点击修改备注'>"+msg.data.remark+"</a>";
    					} else {
    						var userremarkhtml = "<a class='f12 black_l' id='user_remark_set' title='点击设置备注'>备注</a>";
    					}
    					if (msg.data.schoolname != null) {
    						var inhtml = "<div class='user_info_top_div' userid='"+msg.data.uid+"'>"
    						+ "		  <a class='user_info_top_div_img_a' href='"+baseUrl+"stream/u/"+msg.data.uid+"' target='_blank'>"
    						+ "		    <img width='60' height='45' src='"+msg.data.icon_url+"' />"
    						+ "		    <span class='online"+msg.data.online+"'></span></a>"
    						+ "		  <p class='user_info_top_div_nickname_p'><a href='"+baseUrl+"wo/"+msg.data.uid+"' class='f14 fb' target='_blank'>"+msg.data.nickname+"</a> "+userremarkhtml+" <span class='gray'>("+msg.data.type+")</span> <span class='level"+msg.data.degree+"'></span></p>"
    						+ "       <p class='user_info_top_div_quan_p black_l'>圈的:<span class='fb f14'>"+msg.data.follow+"</span> 圈子:<span class='fb f14'>"+msg.data.fans+"</span> "+msg.data.constellation+"<span class='sex"+msg.data.sex+"'></span> "+relationhtml+"</p>"
    						+ "		</div>"
    						+ "		<div class='user_info_main_div'>"
    						+ "			<ul>"
    						+ "             <li>学院: <a target='_blank' href='"+msg.data.domain+"index/mate?w=academy&n="+msg.data.academy_id+"'>"+msg.data.academy+"</a> (<a target='_blank' href='"+msg.data.domain+"'>"+msg.data.schoolname+"</a>)</li>"
    						+ "             <li>专业: <a target='_blank' href='"+msg.data.domain+"index/mate?w=academy&n="+msg.data.academy_id+"&specialty="+msg.data.specialty_id+"'>"+msg.data.specialty+"</a></li>"
    						+ "             <li>寝室: <a target='_blank' href='"+msg.data.domain+"index/mate?w=dormitory&n="+msg.data.dormitory_id+"'>"+msg.data.dormitory+"</a></li>"
    						+ "             <li>"+msg.data.introduction+"</li>"
    						+ "			</ul>"
    						+ "		</div>";
    					} else {
    						var inhtml = "<div class='user_info_top_div' userid='"+msg.data.uid+"'>"
        						+ "		  <a class='user_info_top_div_img_a' href='"+baseUrl+"stream/u/"+msg.data.uid+"' target='_blank'>"
        						+ "		    <img width='60' height='45' src='"+msg.data.icon_url+"' />"
        						+ "		    <span class='online"+msg.data.online+"'></span></a>"
        						+ "		  <p class='user_info_top_div_nickname_p'><a href='"+baseUrl+"wo/"+msg.data.uid+"' class='f14 fb' target='_blank'>"+msg.data.nickname+"</a> "+userremarkhtml+" <span class='gray'>("+msg.data.type+")</span> <span class='level"+msg.data.degree+"'></span></p>"
        						+ "       <p class='user_info_top_div_quan_p black_l'>圈的:<span class='fb f14'>"+msg.data.follow+"</span> 圈子:<span class='fb f14'>"+msg.data.fans+"</span> "+msg.data.constellation+"<span class='sex"+msg.data.sex+"'></span> "+relationhtml+"</p>"
        						+ "		</div>"
        						+ "		<div class='user_info_main_div'>"
        						+ "			<ul>"
        						+ "             <li>学院: <a target='_blank' href='"+msg.data.domain+"index/mate?w=academy&n="+msg.data.academy_id+"'>"+msg.data.academy+"</a></li>"
        						+ "             <li>专业: <a target='_blank' href='"+msg.data.domain+"index/mate?w=academy&n="+msg.data.academy_id+"&specialty="+msg.data.specialty_id+"'>"+msg.data.specialty+"</a></li>"
        						+ "             <li>寝室: <a target='_blank' href='"+msg.data.domain+"index/mate?w=dormitory&n="+msg.data.dormitory_id+"'>"+msg.data.dormitory+"</a></li>"
        						+ "             <li>"+msg.data.introduction+"</li>"
        						+ "			</ul>"
        						+ "		</div>";	
    					}
    					$('.user_info_div').css({ position: "absolute", left: positionleft, top: positiontop }).fadeIn('fast').html(inhtml);
    					return false;
    				}
                }
            });
    	},1000);
    }).mouseleave(function(){
    	clearTimeout(t);
    	$('.user_info_div').hover(function(){},
    	function(){
    		$(this).fadeOut("slow");
    	});
    });
    
    /**
     * remark
     */
    $('#user_remark_set').live('click', function(){
    	var inputremarkhtml = "<p class='newremarkname_p'><input type='text' id='newremarkname' /> <a id='user_remark_submit' class='btn f12'>确定</a></p>";
	    $('.user_info_main_div').html(inputremarkhtml);
	});
    $('#user_remark_submit').live('click', function(){
    	var newremarkname = $('#newremarkname').val();
    	var newuserid = $('.user_info_top_div').attr('userid');
    	$.ajax({
            type: "POST",
    		dataType: "json",
    		url: baseUrl + "ajax/newremark",
    		data:{newuserid: newuserid, newremarkname: newremarkname},
    		success:function(msg){
    			if (msg.status == '1') {
    				$('.user_info_main_div').html("<p class='newremarkname_p'><span class='icon_right'></span>更新备注成功</p>");
    			} else if (msg.status == '2') {
    				$('.user_info_main_div').html("<p class='newremarkname_p'><span class='icon_right'></span>备注成功</p>");
    			} else {
    				$('.user_info_main_div').html("<p class='newremarkname_p'><span class='icon_wrong'></span>备注失败 稍后再试</p>");
    			}
    		}
    	});
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
	$('.steam_repate_li').before('<a class="steam_repate_li_show" title="查看更多 同一用户连续信息"><span class="icon_plus"></span></a>')
	$('.steam_repate_li').hide();
	$('.steam_repate_li_show').live('click', function(){
	    $(this).next().slideDown('fast');
	    $(this).hide();
	});
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
        		setTimeout('mseeageNums()', acquiremilliseconds);
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
            setTimeout('mseeageNums()', 1000);
        }
    });
	$('#message_talk_nums_span_close').click(function(){
		$('#message_talk_nums_div').fadeOut('fast');
	});
}