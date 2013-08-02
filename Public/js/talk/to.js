
var connectedEndpointJob1 = null;
$().ready(function () {

    var data_uid = $('#data_uid').val();
    var data_touid = $('#data_touid').val();
    connectedEndpointJob1 = $.atmosphere.subscribe("http://comet.ihelpoo.com/comet/atmosphere/subscribe/10000-12419", globalCallback, $.atmosphere.request = {
        logLevel: 'debug',
        transport: 'long-polling',
        callback: call,
        enableXDR: true,
        dropAtmosphereHeaders: true,
        readResponsesHeaders: false,
        attachHeadersAsQueryString: true
    });

    function globalCallback(response) {
        console.log("++++");
        if (response.state != "messageReceived") {
            return;
        }
    }

    function call(response) {
        console.log("Call to callbackJob2");
        if (response.state != "messageReceived") {
            return;
        }
        var data = getDataFromResponse(response);
        if (data != null) {
            console.log("---" + data);
        }
    }

    $('#' + data_touid).parent().parent().css({borderBottom: '3px solid #CCC'});
    var user_position = $('#' + data_touid).parent().parent().position();
    if (user_position != null) {
        $('.user_list_ul').animate({scrollTop: user_position.top - 5}, 0);
    }

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
        $(".emotionbox_change_page").removeClass('bg_gray');
        $(this).addClass('bg_gray');
        $page = $(this).text();
        $(".emotionbox_show_ul").empty().load(baseUrl + "other/loademotion" + "?page=" + $page);
    });

    /**
     * image part
     */
    $('#textareaimg').click(function () {
        $('.img_upload_comment_form_div').fadeIn('fast');
    });

    var imageNums = 0;
    $("#img_upload_btn").click(function () {
        var upload_image_file = $('#upload_form_img_file').val();
        var $infoLoading = $('<img/>').attr({'src': baseUrl + 'Public/image/common/progressbar.gif', 'title': '上传中...请稍等'});
        if (upload_image_file == '') {
            $('.imgajaxloading_span').fadeIn('fast').html("<span class='f12 red_l'>还没有选择图片呢</span>").delay(1000).fadeOut('fast');
        } else {
            if (imageNums > 0) {
                alert('只能传1张图片');
            } else {
                $(this).ajaxStart(function () {
                    $('.imgajaxloading_span').fadeIn('fast').html($infoLoading);
                }).ajaxComplete(function () {
                        $infoLoading.remove();
                    });
                $.ajaxFileUpload({
                    url: baseUrl + 'ajax/imgtalkupload',
                    secureuri: false,
                    fileElementId: 'upload_form_img_file',
                    dataType: 'json',
                    success: function (msg) {
                        if (msg.status == 'uploaded') {
                            var uploadImgList = "<li class='upload_img_list' url='" + msg.data + "'><img src='" + msg.data + "' width='80'/><a href='" + msg.data + "' target='_blank' class='f12'><span class='icon_search' title='看大图'></span>大图</a> <a class='re_upload_img'><span class='icon_recycle'></span>重传</a></li>";
                            $('#image_upload_url').val(msg.data);
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

    $('.re_upload_img').live('click', function () {
        $('#image_upload_url').val('');
        $('#image_upload_list_ul').empty();
        $('#img_upload_form').slideDown('fast');
        imageNums = 0;
    });

    $('#img_upload_comment_form_div_close').live('click', function () {
        $('.img_upload_comment_form_div').fadeOut('fast');
    });

    /**
     * send messgae
     */
    $('#send_message').click(function () {  connectedEndpointJob1.push({data: 'hello, world'});
        console.log(connectedEndpointJob1);
        return false;

        var newcontent = $('#send_message_textarea').val();
        var image_url = $('#image_upload_url').val();
        if (newcontent != '' || image_url != '') {
            $.ajax({
                type: "POST",
                url: baseUrl + "talk/toajax",
                data: {uid: data_uid, touid: data_touid, content: newcontent, image: image_url},
                dataType: "json",
                success: function (msg) {
                    if (msg.status == 'ok') {
                        if (msg.data.image != '') {
                            var htmlIn = " <span class='f14 gray '>" + msg.data.nickname + "</span>"
                                + " <span class='f12 gray'>" + msg.data.time + "</span><br />"
                                + msg.data.content + "<br />"
                                + "<a href='" + msg.data.image + "' target='_target'><img src='" + msg.data.imagethumb + "' style='max-width:150px;' title='查看原图' /></a><br /><br />";
                        } else {
                            var htmlIn = " <span class='f14 gray '>" + msg.data.nickname + "</span>"
                                + " <span class='f12 gray'>" + msg.data.time + "</span><br />"
                                + msg.data.content + "<br /><br />";
                        }
                        $('#show_message_div').append(htmlIn);
                        var boxHeight = $('#show_message_div').height();
                        $('#show_message_div_outer').animate({scrollTop: boxHeight}, 800);
                        $('#send_message_textarea').val('');
                        $('#image_upload_url').val('');
                        $('#image_upload_list_ul').empty();
                        $('.img_upload_comment_form_div').slideUp('fast');
                        imageNums = 0;
                    }
                },
                timeout: 8000,
                error: function () {
                    var htmlIn = "<span class='red_l f12'>由于网络原因，您刚刚没有发送出去</span>";
                    $('#show_message_div').append(htmlIn);
                }
            });
        } else {
            var htmlIn = "<span class='red_l f12'>发送内容不能为空</span>";
            $('#input_status').html(htmlIn);
        }
    });

    /**
     * send messgae keydown submit
     */
    $(window).keydown(function (e) {
        if (e.ctrlKey && e.which == 13 || e.which == 10) {
            $('#send_message').click();
            document.body.focus();
        } else if (e.shiftKey && e.which == 13 || e.which == 10) {
            $('#send_message').click();
            document.body.focus();
        }
    })

    /**
     * update input status
     */
    var flagTimes = 'notadd';
    $("#send_message_textarea").keyup(function () {
        today = new Date();
        var seconds = today.getSeconds();
        if (((seconds + 1) % 2) == '0') {
            flagTimes = 'notadd';
        }
        if ((seconds % 2) == '0' && flagTimes == 'notadd') {
            updateInputCtatus();
            flagTimes = 'add';
        }
        ;
    });

    flashPic('.flash_icon');
});
function updateInputCtatus() {
    var data_uid = $('#data_uid').val();
    var data_touid = $('#data_touid').val();
    $.ajax({
        type: "POST",
        dataType: "text",
        url: baseUrl + "talk/toajax",
        data: {uid: data_uid, touid: data_touid, content: 'status_input'}
    });
}