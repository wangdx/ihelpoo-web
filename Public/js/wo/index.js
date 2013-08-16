$().ready(function(){
    /**
     * enlarge image
     */
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
                  $region.html('('+msg.data+')');
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
        		dataType: "html",
        		url: baseUrl + "stream/plusView",
        		data:{sidString: sidString},
        		success:function(data){
                	$('.record_plus_div').css({ position: "absolute", left: positionleft, top: positiontop }).fadeIn('fast').html(data);
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
            url: baseUrl + "stream/ajax",
            data: {'diffusionSid':diffusionSid, 'diffusionView':$diffusion_view},
            datatype: "html",
            success:function(data){
            	var infohtml = "<p align='left'>" + data + "</p> <a class='btn_cancel'>确定</a>";
            	ajaxInfo(infohtml);
                if (data != '你已经扩散了这条信息') {
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
    
    $('.c_v_d_b_ul_li_content_reply a').live('click',function(){
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
	            		$this.parent().find('.comment_reply_verification_stream_code_img').attr({'src': baseUrl + 'other/verifi' });
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
        		$("#ajax_info_div_outer").fadeOut("fast");
            }
        });
    });
    
    $('.btn_cancel').live('click', function(){
    	$("#ajax_info_div").fadeOut("fast");
		$("#ajax_info_div_outer").fadeOut("fast");
		$deleteCommentLi.css("backgroundColor", "#FFF");
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

});