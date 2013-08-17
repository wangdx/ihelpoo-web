$().ready(function(){
	/*if ("6.0" == $.browser.version) {
    $('.help_comment_reply_form').show();
	}
	if ("7.0" == $.browser.version) {
	    $('.help_comment_reply_form').show();
	}*/

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
        var textareanow = $('#help_content_from_textarea').val() + imgtitlemark;
        $('#help_content_from_textarea').val(textareanow);
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

	/**
	 * reply_emotion
	 */
	$('.reply_emotionbox_icon').click(function(e){
		var positionleft = e.pageX + 10;
    	var positiontop = e.pageY + 10;
    	$replyTextarea = $(this).parent().parent().find('.help_comment_reply_form_textarea');
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
        var textareanow = $replyTextarea.val() + imgtitlemarkin;
        $replyTextarea.val(textareanow);
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
     * del
     */
    $('#del_help_record_btn').click(function(){
        $("#i_shine_hit_in").fadeIn('fast').html("<span id='del_help_record_btn_yes'>确定</span> <span id='del_help_record_btn_no'>取消</span>");
    });
    $('#del_help_record_btn_no').live('click', function(){
        $("#i_shine_hit_in").slideUp('fast');
    });
    $('#del_help_record_btn_yes').live('click', function(){
        var delRecordSid = $('#del_help_record_value').val();
        $.ajax({
             type: "POST",
             url: baseUrl + "item/del",
             data: "delrecord=" + delRecordSid,
             dataType: 'json',
             success:function(msg){
                 $("#i_shine_hit_in").fadeIn('fast').html(msg.info).delay(600).fadeOut('fast');
                 setTimeout('pageToStream()',3000);
             }
         });
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

    /**
     * key up
     */
    $("#help_content_from_textarea").keyup(function(){
    	var textareacontent = $('#help_content_from_textarea').val();
        if (textareacontent.length > 0) {
        	var letterlimit = 222 - textareacontent.length;
        	if (letterlimit > 0) {
        		$('.i_help_comment_textarea_info').html('还能输入<span class="blue">' + letterlimit + '</span>个字');
        		contentOk = 'yes';
        	} else {
        		$('.i_help_comment_textarea_info').html('超出字数限制<span class="red">' + letterlimit + '</span>');
        		contentOk = 'morethenlimit';
        	}
        }
    });

    /**
     * help submit
     */
    $('#help_content_from_btn').click(function(){
    	var $this = $(this);
        var help_content_from_textarea = $('#help_content_from_textarea').val();
        var atpattern = /@[^@]+?(?=[\s:：(),。])/g;
        var atresult = help_content_from_textarea.match(atpattern);
        $('#atusers').val(atresult);
        var re = new RegExp("(@[\\u4E00-\\u9FA5A-Za-z0-9_.]+)", "g");
        var s = "<a class=\"getuserinfo\">$1</a>";
        var textareacontentdata = help_content_from_textarea.replace(re, s);
        $("#textareacontent").val(textareacontentdata);
        if (textareacontentdata == '') {
            $("#i_shine_hit_in").fadeIn('fast').html('帮助内容不能为空').delay(800).fadeOut('fast');
        } else if (textareacontentdata.length > 222) {
        	$("#i_shine_hit_in").fadeIn('fast').html('帮助内容太长了 不能超过222个字符').delay(800).fadeOut('fast');
        } else {
		    $(this).ajaxStart(function(){
		    	$this.html($infoLoading);
            }).ajaxStop(function(){
        	    $infoLoading.remove();
        	    $this.html('我来帮助');
            });
            $.post(baseUrl + "item/helpajax", $("#help_content_from").serialize(), function(msg){
                if (msg.status == 'yes') {
                    $('#help_content_from_textarea').val('');
                    $("#i_shine_hit_in").fadeIn('fast').html('帮助回复成功').delay(800).fadeOut('fast');
                    var helpContent = "<li class='bg_l_yellow'>";
                    helpContent += "<a href='" + baseUrl + "stream/u/" + msg.data.uid + "' target='_blank'>";
                    helpContent += "<img src='" + msg.data.uidicon + "' class='i_c_l_u_li_img' height='50' /></a>";
                    helpContent += "<div class='i_c_l_u_li_div black_l'>";
                    helpContent += "<a href='" + baseUrl + "stream/u/" + msg.data.uid + "' target='_blank'>" + msg.data.uidnickname + "</a>";
                    helpContent += msg.data.content;
                    if (msg.data.image != '') {
                    	helpContent += "<img src='" + msg.data.image + "' width='80' />";
                    }
                    helpContent += "<span class='i_c_l_u_li_div_time f12 gray'>" + msg.data.time + "</span></div></li>";
                    $('.i_comment_list_ul').append(helpContent);
                    var bodyHeight = $("body").height();
                    $('html,body').animate({scrollTop: bodyHeight + 'px'}, 800);
                } else {
                    $("#i_shine_hit_in").fadeIn('fast').html(msg.info).delay(800).fadeOut('fast');
                }
            }, "json");
        }
    });
    //help reply
    $('.help_comment_reply').click(function(){
        $help_comment_reply_form = $(this).parent().parent().parent().find('.help_comment_reply_form');
        $help_comment_reply_form.slideDown('fast');
    });
    //help reply comment
    $('.help_comment_reply_btn').click(function(){
        var help_comment_reply_form_textarea = $(this).parent().find('.help_comment_reply_form_textarea').val();
        var atpattern = /@[^@]+?(?=[\s:：(),。])/g;
        var atresult = help_comment_reply_form_textarea.match(atpattern);
        $(this).parent().find('.help_reply_atusers').val(atresult);
        var re = new RegExp("(@[\\u4E00-\\u9FA5A-Za-z0-9_.]+)", "g");
        var s = "<a class=\"getuserinfo\">$1</a>";
        var textareacontentdata = help_comment_reply_form_textarea.replace(re, s);
        $(this).parent().find('.help_reply_textareacontent').val(textareacontentdata);
        $help_comment_reply_form = $(this).parent();
        if (textareacontentdata == '') {
            $("#i_shine_hit_in").fadeIn('fast').html('追问不能为空').delay(800).fadeOut('fast');
        } else if (textareacontentdata.length > 222) {
        	$("#i_shine_hit_in").fadeIn('fast').html('帮助内容太长了 不能超过222个字符').delay(800).fadeOut('fast');
        } else {
		    $(this).ajaxStart(function(){
        	    $(this).after($infoLoading);
            }).ajaxStop(function(){
        	    $infoLoading.remove();
            });
            $.post(baseUrl + "item/helpajax", $help_comment_reply_form.serialize(), function(msg){
            	if (msg.status == 'yes') {
                    $help_comment_reply_form.slideUp('fast');
                    $help_comment_reply_form.find('.help_comment_reply_form_textarea').val('');
                    $("#i_shine_hit_in").fadeIn('fast').html('追问成功').delay(600).fadeOut('fast');
                    var commentContent = "<li class='bg_l_yellow'>";
                    commentContent += "<span class='i_c_l_u_li_spannum gray'><span class='blue f12 fi'>new</span></span>";
                    commentContent += "<a href='" + baseUrl + "stream/u/" + msg.data.uid + "' target='_blank'>";
                    commentContent += "<img src='" + msg.data.uidicon + "' class='i_c_l_u_li_img' height='50' /></a>";
                    commentContent += "<div class='i_c_l_u_li_div black_l'>";
                    commentContent += "<a href='" + baseUrl + "stream/u/" + msg.data.uid + "' target='_blank'>" + msg.data.uidnickname + "</a>";
                    if (!msg.data.toid == '') {
                        commentContent += "<span class='f12 gray fb'>[追问:" + msg.data.toidnickname + "]</span>";
                    }
                    commentContent += msg.data.content;
                    commentContent += "<span class='i_c_l_u_li_div_time f12 gray'>" + msg.data.time + "</span></div></li>";
                    $('.i_comment_list_ul').append(commentContent);
                    var bodyHeight = $("body").height();
                    $('html,body').animate({scrollTop: bodyHeight + 'px'}, 800);
                } else {
                    $("#i_shine_hit_in").fadeIn('fast').html(msg.info).delay(800).fadeOut('fast');
                }
            }, "json");
        }
    });

    //del help reply
    $('.delete_help_reply_btn').click(function(){
        var deleteReplyValue = $(this).parent().find('.delete_help_reply_value').val();
        $alreadyDeleteHelpreplyLi = $(this).parent().parent().parent();
        $.ajax({
            type: "POST",
            url: baseUrl + "item/del",
            data: "delhelpreply=" + deleteReplyValue,
            dataType: "json",
            beforeSend: function() {
                $alreadyDeleteHelpreplyLi.css("backgroundColor", "#FE6600");
            },
            success:function(msg){
                $("#i_shine_hit_in").fadeIn('fast').html(msg.info).delay(800).fadeOut('fast');
                $alreadyDeleteHelpreplyLi.slideUp('fast');
            }
        });
    });
    $('.help_comment_reply').hide();
    $('.delete_help_reply_btn').hide();
    $('.i_comment_list_ul li').hover(function(){
        $(this).find('.help_comment_reply').show();
        $(this).find('.delete_help_reply_btn').show();
    }, function(){
        $(this).find('.help_comment_reply').hide();
        $(this).find('.delete_help_reply_btn').hide();
    });

    /**
     * helpreply hover li
     */
    $('.i_comment_list_ul li').hover(function(){
        $(this).find('.reply_box_btn').show();
        $(this).find('.reply_delete_btn').show();
    }, function(){
        $(this).find('.reply_box_btn').hide();
        $(this).find('.reply_delete_btn').hide();
    });

    /**
     * diffusion;
     */
    $('.diffusion').click(function(){
        var diffusionSid = $(this).attr('value');
        var $thisDiffusion = $(this);
        $.ajax({
            type: "POST",
            url: baseUrl + "stream/ajax",
            data: "diffusionSid=" + diffusionSid,
            datatype: "html",
            success:function(data){
                $('#i_shine_hit').slideDown('normal').html('<div class="diffusion_list">'+data+'<br /><a class="diffusion_list_sure btn">确定</a></div>');
                if (data != '你已经扩散了这条信息') {
                    $thisDiffusion.append('<span class="red">+1</span>');
                }
            }
        });
    });

    $('.diffusion_list_sure').live("click", function(){
        $('#i_shine_hit').html('<p id="i_shine_hit_in"></p>');
    });

});
function pageToStream(){
    window.location = baseUrl + 'stream';
}