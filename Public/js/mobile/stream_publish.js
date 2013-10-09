$().ready(function(){
	
	var atpattern = /@[^@]+?(?=[\s:：(),。])/g;
    var contentOk = 'no';
	var imageNums = 0;

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
    $('.emotionbox_show_ul img').live('click', function(){
        var imgtitle = $(this).attr('title');
        var textareavalue = $('#s_t_textarea').val();
        var imgtitlemark = '[' + imgtitle + ']';
        var textareanow = textareavalue + imgtitlemark;
        $('#s_t_textarea').val(textareanow);
        $('#emotionbox').fadeOut('fast');
        contentOk = 'yes';
    });
    $('.emotionbox_change_page').click(function(){
    	$(".emotionbox_change_page").removeClass('bg_emotionbox_page_select');
    	$(this).addClass('bg_emotionbox_page_select');
    	$page = $(this).attr("value");
        $(".emotionbox_show_ul").empty().load(baseUrl + "other/loademotion" + "?page=" + $page);
    });
    
    /**
     * image part
     */
    $('#textareaimg').click(function(){
        $('.img_upload_form_div').slideDown('fast');
    });

    $('.upload_img_list .upload_image_delete').live('click', function(){
        imageNums--;
        $(this).parent().remove();
    });

    $("#img_upload_btn").click(function(){
        var upload_image_file = $('#upload_form_img_file').val();
        if (upload_image_file == '') {
            $('.imgajaxloading_span').fadeIn('fast').html("<span class='f12 red_l'>还没有选择图片呢</span>").delay(1000).fadeOut('fast');
        } else {
            if (imageNums > 4) {
                alert('最多一次只能传5张图片');
            } else {
            	$.mobile.showPageLoadingMsg();
                $.ajaxFileUpload({
                	url: baseUrl + 'ajax/imgupload',
                	secureuri: false,
                	fileElementId: 'upload_form_img_file',
                	dataType: 'json',
                	success: function (msg){
                	    if (msg.status == 'uploaded') {
                	        var uploadImgList = "<li class='upload_img_list' url='" + msg.data + "'><a href='" + msg.data +"' target='_blank' class='f12'><img src='" + msg.data +"' width='100' /></a> <span class='upload_image_delete'>[删除]</span></li>";
                	        $('#image_upload_list_ul').append(uploadImgList);
                	        imageNums++;
                	    } else if (msg.status == 'error') {
                	        $('.imgajaxloading_span').fadeIn('fast').html("<span class='f12 red_l'>" + msg.info + "</span>").delay(3000).fadeOut('fast');
                	    }
                	    $.mobile.hidePageLoadingMsg();
                	}
                });
            }
        }
    });
    
    $('#image_upload_close').click(function(){
        $('.img_upload_form_div').slideUp('fast');
    });

    /**
     * help part
     */
    $('#textareahelp').click(function(){
        $(this).addClass("helpleaf_select");
        $('#help_is_input').val('1');
        $('#post_help_reword').slideDown('fast');
    });

    //calculate left coins
	$('#reward_coins').click(function(){
	    var total_coins = $('#left_coins_calculate').attr('value');
	    var use_coins = $(this).val();
	    var left_coins = total_coins - use_coins;
	    if (left_coins >= 0) {
	        $('#left_coins_calculate').html("剩余: " + total_coins + '-' + use_coins + '= <span class="fb black_l">' + left_coins + '</span>');
	    } else {
	        $('#left_coins_calculate').html("<span class='red_l'>活跃不够了</span>");
	    }
	});
	
	$('#post_help_reword_close').click(function(){
        $('#post_help_reword').slideUp('fast');
        $('#textareahelp').removeClass("helpleaf_select");
        $('#help_is_input').val('0');
        $('#post_help_reword').slideUp('fast');
    });

    /**
     * submit
     */
    $("#s_t_submit").click(function(){
        var verification_code_value = $('#verification_code').val();
        var textareacontent = $('#s_t_textarea').val() + ' ';
        var help_is_input = $('#help_is_input').val();

        //store image data in input dom
        var upload_img_url_data = attrListImgValue();
        $('#imageurls').val(upload_img_url_data);
        var atresult = textareacontent.match(atpattern);
        $('#atusers').val(atresult);
        var re = new RegExp("(@[\\u4E00-\\u9FA5A-Za-z0-9_.]+)", "g");
        var s = "<a class=\"getuserinfo\">$1</a>";
        var textareacontentdata = textareacontent.replace(re, s);
        $("#textareacontent").val(textareacontentdata);

        if (textareacontent.length > 0) {
        	var letterlimits = 222 - textareacontent.length;
        	if (letterlimits > 0) {
        		contentOk = 'yes';
        	}
        }
        
        if (contentOk == "no") {
            alert("发布内容不能为空");
        } else if (contentOk == "morethenlimit") {
        	alert("超出字数限制，精简一下:)");
        } else if (verification_code_value == "") {
        	alert("验证码不能为空");
        } else if (contentOk == "yes") {
        	$.mobile.showPageLoadingMsg();
            $.post(baseUrl + "stream", $("#s_t_form").serialize(), function(msg){
            	if (msg.status == "verifi") {
            		alert("请输入验证码");
            		$('.verification_code_p').fadeIn('fast');
            		$('#verification_code_img').attr({'src': baseUrl + 'other/verifi' });
            		$('#verification_code').val('');
            	} else if (msg.status == "error") {
            		alert(msg.info);
            	} else if (msg.status == "ok") {
                    window.location = baseUrl + 'stream';
                } else {
                    alert('something wrong');
                }
            	$.mobile.hidePageLoadingMsg();
            }, "json");
        }
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