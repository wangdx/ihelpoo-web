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
    //$("#i_shine_hit_in").delay(300).fadeIn(400).delay(600).fadeOut(500);
    //$("#i_shine_hit").delay(300).animate({top:"130px"}, 400).delay(600).animate({top:"110px"}, 500);
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
    	$(".emotionbox_change_page").removeClass('bg_gray');
    	$(this).addClass('bg_gray');
    	$page = $(this).text();
        $(".emotionbox_show_ul").empty().load(baseUrl + "other/loademotion" + "?page=" + $page);
    });

	//reply_emotion
	$('.reply_emotionbox_icon').click(function(e){
    	var positionleft = e.pageX - 10;
        var positiontop = e.pageY + 10;
	    $('#reply_emotionbox').css({ position: "absolute", left: positionleft, top: positiontop }).fadeIn('fast');
	    $(".reply_emotionbox_show_ul").load(baseUrl + "other/loademotion");
		$replyTextarea = $(this).parent().parent().find('.comment_reply_textarea');
		return false;
    });
	$('#reply_emotionbox_close').click(function(){
        $('#reply_emotionbox').slideUp('fast');
    });
	$('.reply_emotionbox_show_ul img').live("click", function(){
        var imgreplytitle = $(this).attr('title');
        var imgreplytitlemark = '[' + imgreplytitle + ']';
        var replytextareanow = $replyTextarea.val() + imgreplytitlemark;
        $replyTextarea.val(replytextareanow);
        $('#reply_emotionbox').fadeOut('fast');
        //important here, refuse default explorer action
        return false;
    });
	$('.reply_emotionbox_change_page').click(function(){
    	$(".reply_emotionbox_change_page").removeClass('bg_gray');
    	$(this).addClass('bg_gray');
    	$page = $(this).text();
        $(".reply_emotionbox_show_ul").empty().load(baseUrl + "other/loademotion" + "?page=" + $page);
    });

    //i fav
    $('#fav_record_btn').click(function(){
        var favSid = $('#recordsid').val();
        $.ajax({
            type: "POST",
            url: baseUrl + "stream/fav",
            data: "sid=" + favSid,
            dataType: "json",
            success:function(msg){
                $("#i_shine_hit_in").fadeIn('fast').html(msg.info).delay(800).fadeOut('fast');
            }
        });
    });

    //i del
    $('#del_record_btn').click(function(){
        $("#i_shine_hit_in").fadeIn('fast').html("<span id='del_record_btn_yes'>确定</span> <span id='del_record_btn_no'>取消</span>");
    });
    $('#del_record_btn_no').live('click', function(){
        $("#i_shine_hit_in").slideUp('fast');
    });
    $('#del_record_btn_yes').live('click', function(){
        var delRecordSid = $('#recordsid').val();
        $.ajax({
            type: "POST",
            url: baseUrl + "item/del",
            data: "delrecord=" + delRecordSid,
            dataType: "json",
            success:function(msg){
                $("#i_shine_hit_in").fadeIn('fast').html(msg.info).delay(800).fadeOut('fast');
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
     * at part
     *
     * load textarea tool
     */
    if ("6.0" == $.browser.version || "7.0" == $.browser.version || "8.0" == $.browser.version) {
    	//key check
	    $("#i_comment_textarea").keyup(function(){
	    	var textareacontent = $('#i_comment_textarea').val();
	        if (textareacontent.length > 0) {
	        	var letterlimit = 222 - textareacontent.length;
	        	if (letterlimit > 0) {
	        		$('.i_comment_textarea_info').html('还能输入<span class="blue">' + letterlimit + '</span>个字');
	        		contentOk = 'yes';
	        	} else {
	        		$('.i_comment_textarea_info').html('超出字数限制<span class="red">' + letterlimit + '</span>');
	        		contentOk = 'morethenlimit';
	        	}
	        }
	    });
    } else {
	    var elem = document.getElementById("i_comment_textarea");
	    var focus = document.getElementById("cursorfocus");
	    var input = function () {
	    	var textareaoffset = $(".main").offset();
	    	var addleftoffset = textareaoffset.left + 175;
	    	var pos = textareaTools.getInputOffset(elem, true, addleftoffset);
	    	focus.style.left = (pos.left - addleftoffset) + 'px';
	    	focus.style.top = (pos.top - 100) + 'px';
	    	focus.style.display = 'block';
	    };
	    textareaTools._addEvent(elem, 'input', input);
	    textareaTools._addEvent(elem, 'propertychange', input);
	    textareaTools._addEvent(elem, 'click', input);

	    //key check
	    var atswitch = 'off';
	    $("#i_comment_textarea").keyup(function(){
	    	var textareacontent = $('#i_comment_textarea').val();
	    	var currentcontent = $(this).insertAtCaret();
	    	var contentlength = currentcontent.length;
	        lastletter = currentcontent.substr(contentlength - 1);
	        if (atswitch == 'on') {
	            if (lastletter == ' ') {
	                atswitch = 'off';
	                $('.auto_load_div').slideUp('normal');
	            } else {
	            	$.ajax({
	            		type: "POST",
	            		dataType: "json",
	            		url: baseUrl + "ajax/at",
	            		data:{autofillatcontent: currentcontent},
	            		success:function(msg){
	            			if (msg.status == 'ok') {
	            				$('.auto_load_div').slideDown('normal');
	            				$('.auto_load_div').css({ position: "absolute", left: focus.style.left, top: focus.style.top });
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
				$('.auto_load_div').css({ position: "absolute", left: focus.style.left, top: focus.style.top });
	        	$('.auto_load_div').html('<ul class="at_auto_match_ul f12 gray"><li>输入用户昵称,空格结束</li></ul>');
	        	atswitch = 'on';
	        }

	        if (textareacontent.length > 0) {
	        	var letterlimit = 222 - textareacontent.length;
	        	if (letterlimit > 0) {
	        		$('.i_comment_textarea_info').html('还能输入<span class="blue">' + letterlimit + '</span>个字');
	        		contentOk = 'yes';
	        	} else {
	        		$('.i_comment_textarea_info').html('超出字数限制<span class="red">' + letterlimit + '</span>');
	        		contentOk = 'morethenlimit';
	        	}
	        }
	    });

	    //at chose
	    $('.at_auto_match_ul_li').live("click", function(){
	    	var nickname = $(this).text();
	    	atswitch = 'off';
	    	var currentcontent = $("#i_comment_textarea").insertAtCaret();
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
	    		$("#i_comment_textarea").val(handleatcontent);
	    	} else {
	    		AddOnPos("i_comment_textarea",handleatcontent);
	    	}
	    	$('.auto_load_div').slideUp('fast');
	    });
    }

    //comment
    $('#i_c_b_submit').click(function(){
    	var $this = $(this);
        var i_comment_textarea = $('#i_comment_textarea').val();
        var verificationcode = $('#verificationcode').val();
        if (i_comment_textarea == '') {
            $("#i_shine_hit_in").fadeIn('fast').html('评论不能为空').delay(800).fadeOut('fast');
        } else if (verificationcode == '') {
            $("#i_shine_hit_in").fadeIn('fast').html('验证码不能为空').delay(800).fadeOut('fast');
        } else if (i_comment_textarea.length > 222) {
        	$("#i_shine_hit_in").fadeIn('fast').html('回复内容太长了 不能超过222个字符').delay(800).fadeOut('fast');
        } else {
        	i_comment_textarea = i_comment_textarea + ' ';
	        var atpattern = /@[^@]+?(?=[\s:：(),。])/g;
	        var atresult = i_comment_textarea.match(atpattern);
	        $('#atusers').val(atresult);
	        var re = new RegExp("(@[\\u4E00-\\u9FA5A-Za-z0-9_.]+)", "g");
	        var s = "<a class=\"getuserinfo\">$1</a>";
	        var textareacontentdata = i_comment_textarea.replace(re, s);
	        $("#textareacontent").val(textareacontentdata);
		    $(this).ajaxStart(function(){
		    	$this.html($infoLoading);
            }).ajaxStop(function(){
        	    $infoLoading.remove();
        	    $this.html('评论');
            });
            $.post(baseUrl + "item/sayajax", $("#i_c_b_form").serialize(), function(msg){
            	if (msg.status == 'verifi') {
            		$("#i_shine_hit_in").fadeIn('fast').html("<span class='icon_attention'></span>请输入验证码").delay(800).fadeOut('fast');
            		$('.i_c_b_verification').fadeIn('fast');
            		$('#i_c_b_verification_code_img').attr({'src': baseUrl + 'other/verifi' });
            		$('#verificationcode').val('');
            	} else if (msg.status == 'yes') {
                    $('#i_comment_textarea').val('');
                    $("#i_shine_hit_in").fadeIn('fast').html('评论成功').delay(800).fadeOut('fast');
                    var commentContent = "<li class='bg_l_yellow'>";
                    commentContent += "<span class='i_c_l_u_li_spannum gray'><span class='blue f12 fi'>new</span></span>";
                    commentContent += "<a href='" + baseUrl + "stream/u/" + msg.data.uid + "' target='_blank'>";
                    commentContent += "<img src='" + msg.data.uidicon + "' class='i_c_l_u_li_img' height='50' /></a>";
                    commentContent += "<div class='i_c_l_u_li_div black_l'>";
                    commentContent += "<a href='" + baseUrl + "stream/u/" + msg.data.uid + "' target='_blank'>" + msg.data.uidnickname + "</a>";
                    commentContent += msg.data.content;
                    if (msg.data.image != '') {
                    	commentContent += "<img src='" + msg.data.image + "' width='80' />";
                    }
                    commentContent += "<span class=\'i_c_l_u_li_div_time f12 gray\'>" + msg.data.time + "</span></div></li>";
                    $('.i_comment_list_ul').append(commentContent);
                    var bodyHeight = $("body").height();
                    $('html,body').animate({scrollTop: bodyHeight + 'px'}, 800);
                    
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
                    $("#i_shine_hit_in").fadeIn('fast').html(msg.info).delay(800).fadeOut('fast');
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
            $("#i_shine_hit_in").fadeIn('fast').html('回复不能为空').delay(800).fadeOut('fast');
        } else if (i_comment_textarea.length > 200) {
        	$("#i_shine_hit_in").fadeIn('fast').html('回复内容太长了 不能超过200个字符').delay(800).fadeOut('fast');
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
            		$("#i_shine_hit_in").fadeIn('fast').html("<span class='icon_attention'></span>请输入验证码").delay(800).fadeOut('fast');
            		$this.parent().find('.comment_reply_verification').fadeIn('fast');
            		$this.parent().find('.comment_reply_verification_code_img').attr({'src': baseUrl + 'other/verifi' });
            		$this.parent().find('.comment_reply_verificationcode').val('');
            	} else if (msg.status == 'yes') {
                    $comment_reply_div_box.slideUp('fast');
                    $comment_reply_form.find('.comment_reply_textarea').val('');
                    $("#i_shine_hit_in").fadeIn('fast').html('回复成功').delay(800).fadeOut('fast');
                    var commentContent = "<li class='bg_l_yellow'>";
                    commentContent += "<span class='i_c_l_u_li_spannum gray'><span class='blue f12 fi'>new</span></span>";
                    commentContent += "<a href='" + baseUrl + "stream/u/" + msg.data.uid + "' target='_blank'>";
                    commentContent += "<img src='" + msg.data.uidicon + "' class='i_c_l_u_li_img' height='50' /></a>";
                    commentContent += "<div class='i_c_l_u_li_div black_l'>";
                    commentContent += "<a href='" + baseUrl + "stream/u/" + msg.data.uid + "' target='_blank'>" + msg.data.uidnickname + "</a>";
                    if (msg.data.toid != '') {
                        commentContent += "<span class='f12 gray fb'>[回复:" + msg.data.toidnickname + "]</span>";
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

    //delete reply
    $('.reply_delete_btn').click(function(){
    	var delReplySid = $(this).parent().find('.reply_delete_cid').val();
        $alreadyDeleteLi = $(this).parent().parent().parent();
        $alreadyDeleteLi.css("backgroundColor", "#FFFA85");
        $("#i_shine_hit_in").fadeIn('fast').html("<span id='del_recordcomment_btn_yes' value='" + delReplySid + "'>确定</span> <span id='del_recordcomment_btn_no'>取消</span>");
    });
    $('#del_recordcomment_btn_no').live('click', function(){
        $("#i_shine_hit_in").slideUp('fast');
        $alreadyDeleteLi.css("backgroundColor", "#FFF");
    });
    $('#del_recordcomment_btn_yes').live('click', function(){
        var delReplySid = $(this).attr('value');
        $.ajax({
            type: "POST",
            url: baseUrl + "item/del",
            data: "delcomment=" + delReplySid,
            dataType: "json",
            success:function(msg){
                $("#i_shine_hit_in").fadeIn('fast').html(msg.info).delay(800).fadeOut('fast');
                $alreadyDeleteLi.slideUp('fast');
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