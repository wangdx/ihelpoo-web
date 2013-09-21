$(function () {
    // Check if there was a saved application state
    var stateCookie = org.cometd.COOKIE ? org.cometd.COOKIE.get('com.ihelpoo.comet.p2p.state') : null;
    var state = stateCookie ? org.cometd.JSON.fromJSON(stateCookie) : null;
    var state = null;
    var chat = new Chat(state);

    chat.join($('#data_uid').val(), $('#data_touid').val());

    $('#send_message').click(function () {

        //TODO instantly show user his own message, image, and time and then async to server then remote user
        chat.send();
    });


    prepareUI();

    var noActionInterval = 5; // seconds
    $("textarea#send_message_textarea").keypress(function () {
        typing();
    });


    var noTypeTimeout = setTimeout(inActive, noActionInterval * 1000);

    function typing() {
        chat.updateInputStatus('对方正在输入...');
        clearTimeout(noTypeTimeout);
        noTypeTimeout = setTimeout(inActive, noActionInterval * 1000);
    }

    function inActive() {
        chat.updateInputStatus('');
    }

    // restore some values
    if (state) {
        $('#data_uid').val(state.from);
        $('#data_touid').val(state.to);
    }
    
    /**
     * add talklist user 
     */
    $('#add_talklist_user').click(function () {
    	var data_touid = $('#data_touid').val();
    	$.ajax({
            type: "POST",
            url: baseUrl + "talk/add",
            data: {touid: data_touid},
            dataType: "json",
            success: function (msg) {
                if (msg.status == 'ok') {
                    $('#add_talklist_user').html('<span class="icon_right"></span>' + msg.info);
                } else {
                	$('#add_talklist_user').html('<span class="icon_attention"></span>' + msg.info);
                }
            }
        });
    });
    
});

function prepareUI() {

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
    
    /**
     * enter keydown submit
     */
    $(window).keydown(function(e){
    	if(e.keyCode == 13 && e.ctrlKey) {
    		$('#send_message').click();
    		document.body.focus();
    	}
    });

}

function Chat(state) {
    var _self = this;
    var _wasConnected = false;
    var _connected = false;
    var _from;
    var _to;
    var _lastUser;
    var _disconnecting;
    var _chatSubscription;
    var _membersSubscription;

    this.join = function (from, to) {
        _disconnecting = false;
        _from = from;
        _to = to;
        if (!_from) {
            alert('还未登录');
            return;
        }

//        var cometdURL = location.protocol + "//" + location.host + config.contextPath + "/cometd";
        var cometdURL = location.protocol + "//" + "comet.ihelpoo.com/c1/cometd";

        $.cometd.websocketEnabled = true;
        $.cometd.configure({
            url: cometdURL,
            logLevel: 'info'
        });
        $.cometd.handshake();
        $('#send_message_textarea').focus();
    };

    this.leave = function () {
        $.cometd.batch(function () {
            $.cometd.publish('/chat/p2p', {
                from: _from,
                membership: 'leave',
                chat: _from + ' has left'
            });
            _unsubscribe();
        });
        $.cometd.disconnect();

        _from = null;
        _lastUser = null;
        _disconnecting = true;
    };

    this.send = function () {
        $('#input_status').html('');
        var chat = $('#send_message_textarea').val();
        var image = $('#image_upload_url').val();

        if ((!image || !image.length) && (!chat || !chat.length)) {
            var htmlIn = "<span class='red_l f12'>发送内容不能为空</span>";
            $('#input_status').html(htmlIn);
        }
        $.cometd.publish('/service/p2ps', {
            room: '/chat/p2p',
            from: _from,
            to: _to,
            chat: chat,
            status: '',
            image: image
        });
        notice(_from, _to, chat);
    };

    function notice(from, to, content) {
        var chat = '1'; // 1. chat
        if ((!content || !content.length)) return;
        $.cometd.publish('/service/notice', {
            room: '/notice/p2p',
            from: from,
            to: to,
            chat: chat,
            image: content //use image as the proxy
        });
    };

    this.updateInputStatus = function (status) {
        $.cometd.publish('/service/p2ps', {
            room: '/chat/p2p',
            from: _from,
            to: _to,
            chat: "",
            image: '',
            status: status

        });
    };

    this.receive = function (message) {
        var fromUser = message.data.fromUser;
        var toUser = message.data.toUser;
        var from = message.data.from;
        var to = message.data.to;
        var membership = message.data.membership;
        var chat = message.data.chat;
        var image = message.data.image;
        var imageThumb = message.data.imageThumb;
        var time = message.data.time;
        var status = message.data.status;


        var curTo = $('#data_touid').val();
        var curFrom = $('#data_uid').val();

        if (curTo != from && curFrom != from) {//not the person chatting with, not my own page
            return;
        }


        if ((!image || !image.length) && (!chat || !chat.length)) {//update status
            $('#input_status').html(status + '<span class="icon_write"></span>');
            return;
        }

        if (!fromUser) return; // from might be there

//        if (!membership && fromUser == _lastUser) {
//            fromUser = '...';
//        } else {
//            _lastUser = fromUser;
//            fromUser += ':';
//        }
//
//        if (membership) {
//            _lastUser = null;
//        }


        fromUser += ' ';

        if (image && image != '') {
            var htmlIn = " <span class='f14 gray '>" + fromUser + "</span>"
                + " <span class='f12 gray'>" + time + "</span><br>"
                + chat + "<br>"
                + "<a href='" + image + "' target='_target'><img src='" + imageThumb + "' style='max-width:150px;' title='查看原图' /></a><br><br>";
        } else {
            var htmlIn = " <span class='f14 gray '>" + fromUser + "</span>"
                + " <span class='f12 gray'>" + time + "</span><br>"
                + chat + "<br>";
        }

        $('#show_message_div').append(htmlIn);
        var boxHeight = $('#show_message_div').height();
        $('#show_message_div_outer').animate({scrollTop: boxHeight}, 800);
        $('#send_message_textarea').val('');
        $('#image_upload_url').val('');
        $('#image_upload_list_ul').empty();
        $('.img_upload_comment_form_div').slideUp('fast');
    };


    this.pull = function (message) {
        var fromUser = message.data.fromUser;
        var toUser = message.data.toUser;
        var from = message.data.from;
        var to = message.data.to;
        var membership = message.data.membership;
        var chat = message.data.chat;
        var image = message.data.image;
        var imageThumb = message.data.imageThumb;
        var time = message.data.time;


        var curTo = $('#data_touid').val();

//        if (curTo == from) {
//            return;
//        } else {
            if (chat == '4') {
                if (from == 'at') {
                    var num = $('#message_at_nums_a').data(from);
                    num = num ? $('#message_at_nums_a').data(from) : 0;
                    $('#message_at_nums_a').data(from, num + 1)
                    $('#message_at_nums_a').show();
                    $('#message_at_nums_a').children('span').html('+' + (num + 1));
                } else if (from == 'comment') {
                    var num = $('#message_comment_nums_a').data(from);
                    num = num ? $('#message_comment_nums_a').data(from) : 0;
                    $('#message_comment_nums_a').data(from, num + 1)
                    $('#message_comment_nums_a').show();
                    $('#message_comment_nums_a').children('span').html('+' + (num + 1));

                } else {
                    var num = $('#message_system_nums_a').data(from);
                    num = num ? $('#message_system_nums_a').data(from) : 0;
                    $('#message_system_nums_a').data(from, num + 1)
                    $('#message_system_nums_a').show();
                    $('#message_system_nums_a').children('span').html('+' + (num + 1));
                }
            } else if (chat == '1') {
                $('#message_talk_nums_div').fadeIn('fast');
                $('#message_talk_nums_img_icon').show().attr({'src': 'http://ihelpoo.b0.upaiyun.com/useralbum/' + from + '/' + imageThumb + '_m.jpg', 'title': fromUser})
                    .error(function () {
                        $(this).unbind("error").attr("src", "/Public/image/common/0.jpg");
                    });
                $('#message_talk_nums_span_content').html(' ' + image);
                $('#message_talk_nums_p_content_info').html('来自' + fromUser + '的悄悄话');
                $('.message_talk_to_url').attr({ href: baseUrl + "talk/to/" + from });
            }
//        }
    };

    /**
     * Updates the members list.
     * This function is called when a message arrives on channel /chat/members
     */
    this.members = function (message) {
        var list = '';
        $.each(message.data, function () {
            list += this + '<br />';
        });
        $('#members').html(list);
    };

    function _unsubscribe() {
        if (_chatSubscription) {
            $.cometd.unsubscribe(_chatSubscription);
        }
        _chatSubscription = null;
        if (_membersSubscription) {
            $.cometd.unsubscribe(_membersSubscription);
        }
        _membersSubscription = null;
    }

    function _subscribe() {
        _chatSubscription = $.cometd.subscribe('/chat/p2p', _self.receive);
        _membersSubscription = $.cometd.subscribe('/members/p2p', _self.members);
        _chatSubscription = $.cometd.subscribe('/notice/p2p', _self.pull);
    }

    function _connectionInitialized() {
        // first time connection for this client, so subscribe tell everybody.
        $.cometd.batch(function () {
            _subscribe();
            $.cometd.publish('/chat/p2p', {   //TODO this should be a system service
                from: _from,
                membership: 'join',
                chat: _from + ' has joined'
            });
        });
    }

    function _connectionEstablished() {
        // connection establish (maybe not for first time), so just
        // tell local user and update membership
//        _self.receive({
//            data: {
//                from: 'system',
//                chat: 'Connection to Server Opened'
//            }
//        });
        $.cometd.publish('/service/members', {
            from: _from,
            room: '/chat/p2p'
        });
    }

    function _connectionBroken() {
        _self.receive({
            data: {
                user: 'system',
                chat: 'Connection to Server Broken'
            }
        });
        toDeliver();
        $('#members').empty();
    }

    function toDeliver() {
        $.ajax({
                type: "POST",
                url: baseUrl + "ajax/todeliver",
                global: false,
                data: {to: $('#data_uid').val()},
                dataType: "json",
                success: function (msg) {
                },
                error: function () {
                }
            }
        );
    }

    function _connectionClosed() {
        _self.receive({
            data: {
                user: 'system',
                chat: 'Connection to Server Closed'
            }
        });
    }

    function _metaConnect(message) {
        if (_disconnecting) {
            _connected = false;
            _connectionClosed();
        }
        else {
            _wasConnected = _connected;
            _connected = message.successful === true;
            if (!_wasConnected && _connected) {
                _connectionEstablished();
            }
            else if (_wasConnected && !_connected) {
                _connectionBroken();
            }
        }
        $.post(baseUrl + "ajax/updatestatus");
    }

    function _metaHandshake(message) {
        if (message.successful) {
            _connectionInitialized();
        }
    }

    $.cometd.addListener('/meta/handshake', _metaHandshake);
    $.cometd.addListener('/meta/connect', _metaConnect);

// Restore the state, if present
    if (state) {
        setTimeout(function () {
            // This will perform the handshake
            _self.join(state.from, state.to);
        }, 0);
    }

    $(window).bind('unload', function () {
        toDeliver();
        if ($.cometd.reload) {
            $.cometd.reload();
            // Save the application state only if the user was chatting
            if (_wasConnected && _from) {
                var expires = new Date();
                expires.setTime(expires.getTime() + 5 * 1000);
                org.cometd.COOKIE.set('com.ihelpoo.comet.p2p.state', org.cometd.JSON.toJSON({
                    from: _from,
                    to: _to
                }), { 'max-age': 5, expires: expires });
            }
        } else {
            $.cometd.disconnect();
        }
    });
    $(window).bind('beforeunload', function () {

        //return "alert" + _from + _to;
    });
};
