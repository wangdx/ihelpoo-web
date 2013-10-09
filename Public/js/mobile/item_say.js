$().ready(function(){
	var $infoLoading = $('<img/>').attr({'src': baseUrl + 'Public/image/common/ajax_wait.gif', 'title': '提交中...请稍等'});

    /**
     * icon emotion part
     */
    $('#textareaicon').click(function(){
        $('#emotionbox_item').fadeIn('fast');
        $(".emotionbox_show_ul").load(baseUrl + "other/loademotion");
        return false;
    });
    $('#emotionbox_close').click(function(){
        $('#emotionbox_item').slideUp('fast');
    });
    $('.emotionbox_show_ul img').live("click", function(){
        var imgtitle = $(this).attr('title');
        imgtitlemark = '[' + imgtitle + ']';
        var textareanow = $('#i_comment_textarea').val() + imgtitlemark;
        $('#i_comment_textarea').val(textareanow);
        $('#emotionbox_item').fadeOut('fast');
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

	//reply_emotion
	$('.reply_emotionbox_icon').click(function(e){
		var positionleft = e.pageX + 10;
    	var positiontop = e.pageY + 10;
    	$replytextarea = $(this).parent().parent().find('.comment_reply_textarea');
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

    //i del
    $('#del_record_btn').click(function(){
        var deletesid = $('#recordsid').val();
    	var infohtml = "确定删除？";
    	ajaxInfo(infohtml, deletesid);
    });
    
    $('#del_record_btn_yes').live('click', function(){
        var delRecordSid = $(this).attr("value");
    	$.ajax({
            type: "POST",
            url: baseUrl + "item/del",
            data: "delrecord=" + delRecordSid,
            dataType: "json",
            success:function(msg){
                $("#ajax_info_div").fadeOut("fast");
        		$("#ajax_info_div_outer").hide();
        		$("#i_shine_hit_in").fadeIn('fast').html(msg.info).delay(800).fadeOut('fast');
                setTimeout('pageToStream()',3000);
            }
        });
    });
    
    $('.btn_cancel').live('click', function(){
    	$("#ajax_info_div").fadeOut("fast");
		$("#ajax_info_div_outer").hide();
    });
    
    /**
     * image part
     */
    $('#textareaimg').toggle(
        function(){
            $('.img_upload_comment_form_div').slideDown('fast');
        },
        function(){
            $('.img_upload_comment_form_div').slideUp('up');
        }
    );
    var imageNums = 0;
    $("#img_upload_btn").click(function(){
        var upload_image_file = $('#upload_form_img_file').val();
        var $infoLoading = $('<img/>').attr({'src': baseUrl + 'Public/image/common/progressbar.gif', 'title': '加载中...请稍等'});
        if (upload_image_file == '') {
            $('.imgajaxloading_span').fadeIn('fast').html("<span class='f12 red_l'>还没有选择图片呢</span>").delay(1000).fadeOut('fast');
        } else {
            if (imageNums > 0) {
                alert('只能传1张图片');
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
                	        var uploadImgList = "<li class='upload_img_list' url='" + msg.data + "'><img src='" + msg.data +"' width='80'/><a href='" + msg.data +"' target='_blank' class='f12'><span class='icon_search' title='看大图'></span>大图</a> <a class='re_upload_img'><span class='icon_recycle'></span>重传</a></li>";
                	        $('#imageurl').val(msg.data);
                	        $('#image_upload_list_ul').append(uploadImgList);
                	        $('#img_upload_form').hide();
                	        imageNums++;
                	    } else if (msg.status == 'error') {
                	        $('.imgajaxloading_span').fadeIn('fast').html("<span class='f12 red_l'>" + msg.info + "</span>").delay(1000).fadeOut('fast');
                	    }
                	}
                });
            }
        }
    });

    $('.re_upload_img').live('click', function(){
    	$('#imageurl').val('');
    	$('#image_upload_list_ul').empty();
    	$('#img_upload_form').slideDown('fast');
    	imageNums = 0;
    });

    //comment
    $('#i_c_b_submit').click(function(){
    	var $this = $(this);
        var i_comment_textarea = $('#i_comment_textarea').val();
        var verificationcode = $('#verificationcode').val();
        if (i_comment_textarea == '') {
            ajaxInfo('评论不能为空');
        } else if (verificationcode == '') {
            ajaxInfo('验证码不能为空');
        } else if (i_comment_textarea.length > 222) {
        	ajaxInfo('评论内容太长了 不能超过222个字符');
        } else {
        	i_comment_textarea = i_comment_textarea + ' ';
	        var atpattern = /@[^@]+?(?=[\s:：(),。])/g;
	        var atresult = i_comment_textarea.match(atpattern);
	        $('#atusers').val(atresult);
	        var re = new RegExp("(@[\\u4E00-\\u9FA5A-Za-z0-9_.]+)", "g");
	        var s = "<a class=\"getuserinfo\">$1</a>";
	        var textareacontentdata = i_comment_textarea.replace(re, s);
	        $("#textareacontent").val(textareacontentdata);
		    $this.html($infoLoading);
            $.post(baseUrl + "item/sayajax", $("#i_c_b_form").serialize(), function(msg){
            	if (msg.status == 'verifi') {
            		ajaxInfo('请输入验证码');
            		$('.i_c_b_verification').fadeIn('fast');
            		$('#i_c_b_verification_code_img').attr({'src': baseUrl + 'other/verifi?imageid=' + Math.random()});
            		$('#verificationcode').val('');
            		$this.html('评论');
            	} else if (msg.status == 'yes') {
                    $('#i_comment_textarea').val('');
                    $("#i_shine_hit_in").fadeIn('fast').html('评论成功').delay(800).fadeOut('fast');
                    var commentContent = "<li class='bg_l_yellow'>";
                    commentContent += "<a href='" + baseUrl + "wo/" + msg.data.uid + "' target='_blank'>";
                    commentContent += "<img src='" + msg.data.uidicon + "' class='i_c_l_u_li_img' height='50' /></a>";
                    commentContent += "<div class='i_c_l_u_li_div black_l'>";
                    commentContent += "<a href='" + baseUrl + "wo/" + msg.data.uid + "' target='_blank'>" + msg.data.uidnickname + "</a>";
                    commentContent += msg.data.content;
                    if (msg.data.image != '') {
                    	commentContent += "<img src='" + msg.data.image + "' width='80' />";
                    }
                    commentContent += "<span class=\'i_c_l_u_li_div_time f12 gray\'>" + msg.data.time + "</span></div></li>";
                    $('.i_comment_list_ul').prepend(commentContent);
                    $this.html('评论');
                    $('.i_c_b_verification').hide();
                    $('#verificationcode').val('999');
                    notice.send('comment', msg.info);
                    
                    /**
                     * 
                     */
                    var weiboswitch = $('#weiboswitchjs').val();
                    var sayid = $('#sayid').val();
                    if (weiboswitch == 'on') {
                    	var sayweiboid = $('#weiboswitchjs').attr('weiboid');
                    	var newitemsayurl = baseUrl + 'item/say/' + sayid;
        				WB2.anyWhere(function(W){
	        	        	W.parseCMD("/comments/create.json", function(sResultCreate, bStatusCreate){
	        	        		if(bStatusCreate == true) {
	        	        	    }
	        	        	},{
	        	        		id : sayweiboid,
	        	        		comment : msg.data.uidnickname + ' : ' + msg.data.content + newitemsayurl,
	        	        		comment_ori : '1'
	        	        	},{
	        	        		method: 'post'
	        	        	});
	        	        });
                    }
                } else {
                    ajaxInfo(msg.info);
                    $this.html('评论');
                }
            }, "json");
        }
    });
    
    //reply
    $('.reply_box_btn').click(function(){
        $comment_reply_div_box = $(this).parent().parent().parent().find('.comment_reply_div_box');
        $comment_reply_div_box.slideDown('fast');
    });
    
    //reply auto show 
    $comment_reply_auto_show_li = $('.now_reply_this_id');
    if ($comment_reply_auto_show_li != null) {
    	$comment_reply_div_box = $comment_reply_auto_show_li.find('.comment_reply_div_box');
    	$comment_reply_div_box.slideDown('fast');
    	var reply_auto_offset = $comment_reply_auto_show_li.offset();
    	if (reply_auto_offset != null) {
    		$("html,body").animate({scrollTop: reply_auto_offset.top - 60}, 200);
    	}
    }
    
    //reply comment
    $('.comment_reply_btn').click(function(){
    	var $this = $(this);
        var i_comment_textarea = $(this).parent().find('.comment_reply_textarea').val();
        if (i_comment_textarea == '') {
            ajaxInfo('回复不能为空');
        } else if (i_comment_textarea.length > 200) {
        	ajaxInfo('回复内容太长了 不能超过200个字符');
        } else {
        	i_comment_textarea = i_comment_textarea + ' ';
	        var atpattern = /@[^@]+?(?=[\s:：(),。])/g;
	        var atresult = i_comment_textarea.match(atpattern);
	        $(this).parent().find('.reply_atusers').val(atresult);
	        var re = new RegExp("(@[\\u4E00-\\u9FA5A-Za-z0-9_.]+)", "g");
	        var s = "<a class=\"getuserinfo\">$1</a>";
	        var textareacontentdata = i_comment_textarea.replace(re, s);
	        $(this).parent().find('.reply_textareacontent').val(textareacontentdata);
	        $comment_reply_form = $(this).parent();
		    $(this).ajaxStart(function(){
        	    $(this).after($infoLoading);
            }).ajaxStop(function(){
        	    $infoLoading.remove();
            });
            $.post(baseUrl + "item/sayajax", $comment_reply_form.serialize(), function(msg){
            	if (msg.status == 'verifi') {
            		ajaxInfo('请输入验证码');
            		$this.parent().find('.comment_reply_verification').fadeIn('fast');
            		$this.parent().find('.comment_reply_verification_code_img').attr({'src': baseUrl + 'other/verifi?imageid=' + Math.random() });
            		$this.parent().find('.comment_reply_verificationcode').val('');
            	} else if (msg.status == 'yes') {
                    $comment_reply_div_box.slideUp('fast');
                    $comment_reply_form.find('.comment_reply_textarea').val('');
                    $("#i_shine_hit_in").fadeIn('fast').html('回复成功').delay(800).fadeOut('fast');
                    var commentContent = "<li class='bg_l_yellow'>";
                    commentContent += "<a href='" + baseUrl + "wo/" + msg.data.uid + "' target='_blank'>";
                    commentContent += "<img src='" + msg.data.uidicon + "' class='i_c_l_u_li_img' height='50' /></a>";
                    commentContent += "<div class='i_c_l_u_li_div black_l'>";
                    commentContent += "<a href='" + baseUrl + "wo/" + msg.data.uid + "' target='_blank'>" + msg.data.uidnickname + "</a>";
                    if (msg.data.toid != '') {
                        commentContent += "<span class='f12 gray fb'>[回复:" + msg.data.toidnickname + "]</span>";
                    }
                    commentContent += msg.data.content;
                    commentContent += "<span class='i_c_l_u_li_div_time f12 gray'>" + msg.data.time + "</span></div></li>";
                    $('.i_comment_list_ul').prepend(commentContent);
                    $this.parent().find('.comment_reply_verification').hide();
                    $this.parent().find('.comment_reply_verificationcode').val('999');
                    notice.send('comment', msg.info);
                } else {
                    ajaxInfo(msg.info);
                }
            }, "json");
        }
    });

    //delete reply
    $('.reply_delete_btn').click(function(){
        var delReplySid = $(this).parent().find('.reply_delete_cid').val();
        $alreadyDeleteLi = $(this).parent().parent().parent();
        $alreadyDeleteLi.css("backgroundColor", "#FFFA85");
    	var infohtml = "<p>确定删除评论？</p> <a class='btn_sure' id='delete_comment_btn_yes' value='"+delReplySid+"'>确定</a><a class='btn_cancel'>取消</a>";
    	ajaxInfo(infohtml);
    });
    $('#delete_comment_btn_yes').live('click', function(){
        var delReplySid = $(this).attr('value');
        $.ajax({
            type: "POST",
            url: baseUrl + "item/del",
            data: "delcomment=" + delReplySid,
            dataType: "json",
            success:function(msg){
                $alreadyDeleteLi.slideUp('fast');
                $("#ajax_info_div").fadeOut("fast");
        		$("#ajax_info_div_outer").hide();
            }
        });
    });
    
    $('.reply_box_btn').hide();
    $('.reply_delete_btn').hide();
    $('.i_comment_list_ul li').hover(function(){
        $(this).find('.reply_box_btn').show();
        $(this).find('.reply_delete_btn').show();
    }, function(){
        $(this).find('.reply_box_btn').hide();
        $(this).find('.reply_delete_btn').hide();
    });

    //diffusion;
    $('.diffusion').click(function(){
        var diffusionSid = $(this).attr('value');
        var $thisDiffusion = $(this);
        $.ajax({
            type: "POST",
            url: baseUrl + "stream/diffuseIt",
            data: "diffusionSid=" + diffusionSid,
            dataType: "json",
            success:function(result){
            	var infohtml = "<p align='left'>" + result.info + "</p> <a class='btn_cancel'>确定</a>";
            	notice.send('system', result.data);
            	ajaxInfo(infohtml);
                if (result.info != '你已经扩散了这条信息') {
                    $thisDiffusion.append('<span class="red">+1</span>');
                }
            }
        });
    });
});
function pageToStream(){
    window.location = baseUrl + 'stream';
}
