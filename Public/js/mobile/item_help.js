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
    
    /**
     * del
     */
    $('#del_help_record_btn').click(function(){
        var deletesid = $('#del_help_record_value').val();
    	var infohtml = "<p>确定删除？</p> <a class='btn_sure' id='del_record_btn_yes' value='"+deletesid+"'>确定</a><a class='btn_cancel'>取消</a>";
    	ajaxInfo(infohtml);
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
        	ajaxInfo('帮助内容不能为空');
        } else if (textareacontentdata.length > 222) {
        	ajaxInfo('帮助内容太长了 不能超过222个字符');
        } else {
		    $this.html($infoLoading);
            $.post(baseUrl + "item/helpajax", $("#help_content_from").serialize(), function(msg){
                if (msg.status == 'yes') {
                    $('#help_content_from_textarea').val('');
                    $("#i_shine_hit_in").fadeIn('fast').html('帮助回复成功').delay(800).fadeOut('fast');
                    var helpContent = "<li class='bg_l_yellow'>";
                    helpContent += "<a href='" + baseUrl + "wo/" + msg.data.uid + "' target='_blank'>";
                    helpContent += "<img src='" + msg.data.uidicon + "' class='i_c_l_u_li_img' height='50' /></a>";
                    helpContent += "<div class='i_c_l_u_li_div black_l'>";
                    helpContent += "<a href='" + baseUrl + "wo/" + msg.data.uid + "' target='_blank'>" + msg.data.uidnickname + "</a>";
                    helpContent += msg.data.content;
                    if (msg.data.image != '') {
                    	helpContent += "<img src='" + msg.data.image + "' width='80' />";
                    }
                    helpContent += "<span class='i_c_l_u_li_div_time f12 gray'>" + msg.data.time + "</span></div></li>";
                    $('.i_comment_list_ul').append(helpContent);
                    var bodyHeight = $("body").height();
                    $('html,body').animate({scrollTop: bodyHeight + 'px'}, 800);
                    notice.send('system', msg.info);
                } else {
                    ajaxInfo(msg.info);
                }
                $this.html('我来帮助');
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
        	ajaxInfo('追问不能为空');
        } else if (textareacontentdata.length > 222) {
        	ajaxInfo('帮助内容太长了 不能超过222个字符');
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
                    commentContent += "<a href='" + baseUrl + "wo/" + msg.data.uid + "' target='_blank'>";
                    commentContent += "<img src='" + msg.data.uidicon + "' class='i_c_l_u_li_img' height='50' /></a>";
                    commentContent += "<div class='i_c_l_u_li_div black_l'>";
                    commentContent += "<a href='" + baseUrl + "wo/" + msg.data.uid + "' target='_blank'>" + msg.data.uidnickname + "</a>";
                    if (!msg.data.toid == '') {
                        commentContent += "<span class='f12 gray fb'>[追问:" + msg.data.toidnickname + "]</span>";
                    }
                    commentContent += msg.data.content;
                    commentContent += "<span class='i_c_l_u_li_div_time f12 gray'>" + msg.data.time + "</span></div></li>";
                    $('.i_comment_list_ul').append(commentContent);
                    var bodyHeight = $("body").height();
                    $('html,body').animate({scrollTop: bodyHeight + 'px'}, 800);
                    notice.send('system', msg.info);
                } else {
                    ajaxInfo(msg.info);
                }
            }, "json");
        }
    });

    //del help reply
    $('.delete_help_reply_btn').click(function(){
        var deleteReplyValue = $(this).attr('value');
    	var infohtml = "<p>确定删除追问？</p> <a class='btn_sure' id='del_record_helpreply_btn_yes' value='"+deleteReplyValue+"'>确定</a><a class='btn_cancel'>取消</a>";
    	$alreadyDeleteHelpreplyLi = $(this).parent().parent().parent();
    	$alreadyDeleteHelpreplyLi.css("backgroundColor", "#FFFA85");
    	ajaxInfo(infohtml);
    });
    
    $('#del_record_helpreply_btn_yes').live('click', function(){
        var delRecordHelpreplyid = $(this).attr("value");
    	$.ajax({
            type: "POST",
            url: baseUrl + "item/del",
            data: "delhelpreply=" + delRecordHelpreplyid,
            dataType: "json",
            success:function(msg){
                $("#ajax_info_div").fadeOut("fast");
        		$("#ajax_info_div_outer").hide();
        		$("#i_shine_hit_in").fadeIn('fast').html(msg.info).delay(800).fadeOut('fast');
        		$alreadyDeleteHelpreplyLi.slideUp('fast');
            }
        });
    });
    
    /**
     * diffusion;
     */
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