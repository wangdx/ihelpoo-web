$().ready(function(){
	$('.a_view_info').click(function(){
		$(this).parent().parent().removeClass('msg_notread');
	});
	$('.a_view_info_sys').click(function(){
		$(this).parent().parent().removeClass('msg_notread');
	});
	
	$('.c_v_d_b_ul_li_content_reply_btn').toggle(
	    function(){
	    	$(this).parent().parent().find('.comment_view_div_box_replyinner').slideDown('fast');
	    },
	    function(){
	    	$(this).parent().parent().find('.comment_view_div_box_replyinner').slideUp('fast');
	    }
	);
    
    /**
     * comment
     */
    $('.comment_reply_submit').live('click', function(){
    	var $this = $(this);
        var i_comment_textarea = $(this).parent().find('textarea').val();
        if (i_comment_textarea == '') {
            ajaxInfo('写点东西吧，回复不能为空');
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
	                    $('.comment_view_div_box_replyinner_textarea').val('');
	                    $this.parent().find('.comment_reply_verification_stream').hide();
	                    $this.parent().find('.comment_reply_verification_streamcode').val('999');
	                    notice.send('comment', msg.info);
	                    ajaxInfo('回复成功');
	                    $("#ajax_info_div").delay(1000).fadeOut("fast");
	                    $("#ajax_info_div_outer").delay(1000).hide();
	                    $this.parent().parent().removeClass('msg_notread');
	                } else {
	                    ajaxInfo(msg.info);
	                }
	            }
	        });
        }
    });
    
    /**
     * icon reply part 
     */
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