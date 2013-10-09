$().ready(function(){

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
              	if (msg.status != 'error') {
              		$region.html('('+msg.data+')');
              	}
            }
        });
    });

    //i delete & delete reply
    $('#del_record_btn').click(function(){
        var deletesid = $('#recordsid').val();
    	var infohtml = "确定删除记录？";
    	ajaxInfo(infohtml, 'record', deletesid);
    });
    
    $('.reply_delete_btn').click(function(){
        var delReplySid = $(this).parent().find('.reply_delete_cid').val();
    	var infohtml = "确定删除评论？";
    	ajaxInfo(infohtml, 'comment' ,delReplySid);
    });
    
    $('#delete_btn_yes').live('click', function(){
        var delRecordSid = $(this).attr("value");
        var delInfoType = $(this).attr("infotype");
        ajaxInfo('删除成功', 0, 0);
        $("#ajax_info_div").delay(1000).fadeOut("fast");
		$("#ajax_info_div_outer").delay(1000).fadeOut("fast");
    	/*$.ajax({
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
        
        delete_comment_btn_yes
        $.ajax({
            type: "POST",
            url: baseUrl + "item/del",
            data: "delcomment=" + delReplySid,
            dataType: "json",
            success:function(msg){
                $("#ajax_info_div").fadeOut("fast");
        		$("#ajax_info_div_outer").hide();
            }
        });
        */
    });
    
    //comment
    $('#i_c_b_submit').click(function(){
    	var $this = $(this);
        var i_comment_textarea = $('#i_comment_textarea').val();
        var verificationcode = $('#verificationcode').val();
        if (i_comment_textarea == '') {
            ajaxInfo('评论不能为空',0,0);
        } else if (verificationcode == '') {
            ajaxInfo('验证码不能为空',0,0);
        } else if (i_comment_textarea.length > 222) {
        	ajaxInfo('评论内容太长了 不能超过222个字符',0,0);
        } else {
        	i_comment_textarea = i_comment_textarea + ' ';
	        var atpattern = /@[^@]+?(?=[\s:：(),。])/g;
	        var atresult = i_comment_textarea.match(atpattern);
	        $('#atusers').val(atresult);
	        var re = new RegExp("(@[\\u4E00-\\u9FA5A-Za-z0-9_.]+)", "g");
	        var s = "<a class=\"getuserinfo\">$1</a>";
	        var textareacontentdata = i_comment_textarea.replace(re, s);
	        $("#textareacontent").val(textareacontentdata);
	        
	        $.mobile.showPageLoadingMsg();
            $.post(baseUrl + "item/sayajax", $("#i_c_b_form").serialize(), function(msg){
            	if (msg.status == 'verifi') {
            		ajaxInfo('请输入验证码',0,0);
            		$('.i_c_b_verification').fadeIn('fast');
            		$('#i_c_b_verification_code_img').attr({'src': baseUrl + 'other/verifi?imageid=' + Math.random()});
            		$('#verificationcode').val('');
            	} else if (msg.status == 'yes') {
                    $('#i_comment_textarea').val('');
                    $("#i_shine_hit_in").fadeIn('fast').html('评论成功').delay(800).fadeOut('fast');
                    var commentContent = "<li class='bg_l_yellow'>";
                    commentContent += "<a href='" + baseUrl + "wo/" + msg.data.uid + "' target='_self'>";
                    commentContent += "<img src='" + msg.data.uidicon + "' class='i_c_l_u_li_img' height='50' /></a>";
                    commentContent += "<div class='i_c_l_u_li_div black_l'>";
                    commentContent += "<a href='" + baseUrl + "wo/" + msg.data.uid + "' target='_self'>" + msg.data.uidnickname + "</a>";
                    commentContent += msg.data.content;
                    if (msg.data.image != '') {
                    	commentContent += "<img src='" + msg.data.image + "' width='80' />";
                    }
                    commentContent += "<span class=\'i_c_l_u_li_div_time f12 gray\'>" + msg.data.time + "</span></div></li>";
                    $('.i_comment_list_ul').prepend(commentContent);
                    $('.i_c_b_verification').hide();
                    $('#verificationcode').val('999');
                } else {
                    ajaxInfo(msg.info,0,0);
                }
            	$.mobile.hidePageLoadingMsg();
            }, "json");
        }
    });
    
    //reply
    $('.reply_box_btn').click(function(){
        $comment_reply_div_box = $(this).parent().parent().parent().find('.comment_reply_div_box');
        $comment_reply_div_box.slideDown('fast');
    });
    
    //reply comment
    $('.comment_reply_btn').click(function(){
    	var $this = $(this);
        var i_comment_textarea = $(this).parent().parent().find('.comment_reply_textarea').val();
        if (i_comment_textarea == '') {
            ajaxInfo('回复不能为空',0,0);
        } else if (i_comment_textarea.length > 200) {
        	ajaxInfo('回复内容太长了 不能超过200个字符',0,0);
        } else {
        	i_comment_textarea = i_comment_textarea + ' ';
	        var atpattern = /@[^@]+?(?=[\s:：(),。])/g;
	        var atresult = i_comment_textarea.match(atpattern);
	        $(this).parent().find('.reply_atusers').val(atresult);
	        var re = new RegExp("(@[\\u4E00-\\u9FA5A-Za-z0-9_.]+)", "g");
	        var s = "<a class=\"getuserinfo\">$1</a>";
	        var textareacontentdata = i_comment_textarea.replace(re, s);
	        $(this).parent().parent().find('.reply_textareacontent').val(textareacontentdata);
	        $comment_reply_form = $(this).parent().parent();
		    
		    $.mobile.showPageLoadingMsg();
            $.post(baseUrl + "item/sayajax", $comment_reply_form.serialize(), function(msg){
            	if (msg.status == 'verifi') {
            		ajaxInfo('请输入验证码',0,0);
            		$this.parent().parent().find('.comment_reply_verification').fadeIn('fast');
            		$this.parent().parent().find('.comment_reply_verification_code_img').attr({'src': baseUrl + 'other/verifi?imageid=' + Math.random() });
            		$this.parent().parent().find('.comment_reply_verificationcode').val('');
            	} else if (msg.status == 'yes') {
                    $comment_reply_div_box.slideUp('fast');
                    $comment_reply_form.find('.comment_reply_textarea').val('');
                    $("#i_shine_hit_in").fadeIn('fast').html('回复成功').delay(800).fadeOut('fast');
                    var commentContent = "<li class='bg_l_yellow'>";
                    commentContent += "<a href='" + baseUrl + "wo/" + msg.data.uid + "' target='_self'>";
                    commentContent += "<img src='" + msg.data.uidicon + "' class='i_c_l_u_li_img' height='50' /></a>";
                    commentContent += "<div class='i_c_l_u_li_div black_l'>";
                    commentContent += "<a href='" + baseUrl + "wo/" + msg.data.uid + "' target='_self'>" + msg.data.uidnickname + "</a>";
                    if (msg.data.toid != '') {
                        commentContent += "<span class='f12 gray fb'>[回复:" + msg.data.toidnickname + "]</span>";
                    }
                    commentContent += msg.data.content;
                    commentContent += "<span class='i_c_l_u_li_div_time f12 gray'>" + msg.data.time + "</span></div></li>";
                    $('.i_comment_list_ul').prepend(commentContent);
                    $this.parent().parent().find('.comment_reply_verification').hide();
                    $this.parent().parent().find('.comment_reply_verificationcode').val('999');
                } else {
                    ajaxInfo(msg.info,0,0);
                }
            	$.mobile.hidePageLoadingMsg();
            }, "json");
        }
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
            	var infohtml = "<p align='left'>" + result.info + "</p>";
            	ajaxInfo(infohtml, 0, 0);
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
