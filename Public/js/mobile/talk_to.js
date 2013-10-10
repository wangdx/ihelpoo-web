$().ready(function(){
	
	/**
     * icon emotion part
     */
    $('#textareaicon').click(function () {
        $('#emotionbox_item').fadeIn('fast');
        $(".emotionbox_show_ul").load(baseUrl + "other/loademotion");
        return false;
    });
    $('#emotionbox_close').click(function () {
        $('#emotionbox_item').slideUp('fast');
    });
    $('.emotionbox_show_ul img').live("click", function () {
        var imgtitle = $(this).attr('title');
        imgtitlemark = '[' + imgtitle + ']';
        var textareanow = $('#send_message_textarea').val() + imgtitlemark;
        $('#send_message_textarea').val(textareanow);
        $('#emotionbox_item').fadeOut('fast');
        //important here, refuse default explorer action
        return false;
    });
    $('.emotionbox_change_page').click(function () {
        $(".emotionbox_change_page").removeClass('bg_emotionbox_page_select');
        $(this).addClass('bg_emotionbox_page_select');
        $page = $(this).attr("value");
        $(".emotionbox_show_ul").empty().load(baseUrl + "other/loademotion" + "?page=" + $page);
    });

    /**
     * image part
     */
    $('#textareaimg').click(function () {
        $('.img_upload_comment_form_div').fadeIn('fast');
        $('#img_upload_form').show();
    });

    $("#img_upload_btn").click(function () {
        var upload_image_file = $('#upload_form_img_file').val();
        var $infoLoading = $('<img/>').attr({'src': baseUrl + 'Public/image/common/progressbar.gif', 'title': '上传中...请稍等'});
        if (upload_image_file == '') {
            $('.imgajaxloading_span').fadeIn('fast').html("<span class='f12 red_l'>还没有选择图片呢</span>").delay(1000).fadeOut('fast');
        } else {
            $('.imgajaxloading_span').fadeIn('fast').html($infoLoading);
            $.ajaxFileUpload({
                url: baseUrl + 'ajax/imgtalkupload',
                secureuri: false,
                fileElementId: 'upload_form_img_file',
                dataType: 'json',
                success: function (msg) {
                    $infoLoading.remove();
                    if (msg.status == 'uploaded') {
                        var uploadImgList = "<li class='upload_img_list' url='" + msg.data + "'><img src='" + msg.data + "' width='80'/><a href='" + msg.data + "' target='_blank' class='f12'><span class='icon_search' title='看大图'></span>大图</a> <a class='re_upload_img'><span class='icon_recycle'></span>重传</a></li>";
                        $('#image_upload_url').val(msg.data);
                        $('#image_upload_list_ul').empty().append(uploadImgList);
                        $('#img_upload_form').hide();
                    } else if (msg.status == 'error') {
                        $('.imgajaxloading_span').fadeIn('fast').html("<span class='f12 red_l'>" + msg.info + "</span>").delay(1000).fadeOut('fast');
                    }
                }
            });
        }
    });

    $('.re_upload_img').live('click', function () {
        $('#image_upload_url').val('');
        $('#image_upload_list_ul').empty();
        $('#img_upload_form').slideDown('fast');
    });

    $('#img_upload_comment_form_div_close').live('click', function () {
        $('.img_upload_comment_form_div').fadeOut('fast');
    });
});