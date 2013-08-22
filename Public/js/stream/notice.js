notice = null;

$(function () {
    // Check if there was a saved application state
    var stateCookie = org.cometd.COOKIE ? org.cometd.COOKIE.get('com.ihelpoo.comet.notice.state') : null;
    var state = stateCookie ? org.cometd.JSON.fromJSON(stateCookie) : null;
    var state = null;
    notice = new Notice(state);
    notice.join($('#data_uid').val(), $('#data_touid').val());
    // restore some values
    if (state) {
        $('#data_uid').val(state.from);
        $('#data_touid').val(state.to);
    }
});

function Notice(state) {
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
    };

    this.leave = function () {
        $.cometd.batch(function () {
            $.cometd.publish('/notice/notice', {
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

    this.send = function (from, to) {
        var chat = '4';
        var image = '无';
        if (!chat || !chat.length) return;
        $.cometd.publish('/service/notice', {
            room: '/notice/p2p',
            from: from,
            to: to,
            chat: chat,
            image: image
        });
    };

    this.updateInputStatus = function(status){
        $.cometd.publish('/service/notice', {
            room: '/notice/p2p',
            from: _from,
            to: _to,
            chat: "",
            image: status
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

        if(chat == '4'){
            if(from == 'at'){
                var num = $('#message_at_nums_a').data(from);
                num = num ? $('#message_at_nums_a').data(from) : 0;
                $('#message_at_nums_a').data(from, num + 1)
                $('#message_at_nums_a').show();
                $('#message_at_nums_a').children('span').html('+' + (num + 1));
            }else  if(from == 'comment'){
                var num = $('#message_comment_nums_a').data(from);
                num = num ? $('#message_comment_nums_a').data(from) : 0;
                $('#message_comment_nums_a').data(from, num + 1)
                $('#message_comment_nums_a').show();
                $('#message_comment_nums_a').children('span').html('+' + (num + 1));

            }else{
                var num = $('#message_system_nums_a').data(from);
                num = num ? $('#message_system_nums_a').data(from) : 0;
                $('#message_system_nums_a').data(from, num + 1)
                $('#message_system_nums_a').show();
                $('#message_system_nums_a').children('span').html('+' + (num + 1));
            }
        } else if (chat == '1') {
            $('#message_talk_nums_div').fadeIn('fast');
            $('#message_talk_nums_img_icon').show().attr({'src': 'http://ihelpoo.b0.upaiyun.com/useralbum/'+from+'/'+imageThumb+'_m.jpg', 'title': fromUser})
                .error(function(){$(this).unbind("error").attr("src", "/Public/image/common/0.jpg");});
            $('#message_talk_nums_span_content').html(' ' + image);
            $('#message_talk_nums_p_content_info').html('来自' + fromUser+ '的悄悄话');
            $('.message_talk_to_url').attr({ href: baseUrl + "talk/to/" + from });
        }
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
        _chatSubscription = $.cometd.subscribe('/notice/p2p', _self.receive);
        _membersSubscription = $.cometd.subscribe('/users/p2p', _self.members);
    }

    function _connectionInitialized() {
        // first time connection for this client, so subscribe tell everybody.
        $.cometd.batch(function () {
            _subscribe();
            $.cometd.publish('/notice/p2p', {   //TODO this should be a system service
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
        $.cometd.publish('/service/users', {
            from: _to,
            room: '/notice/p2p'
        });
    }

    function _connectionBroken() {
        _self.receive({
            data: {
                user: 'system',
                chat: 'Connection to Server Broken'
            }
        });
        $('#members').empty();
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
        console.log("+-+_+_+_+_+_+_+");
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

    $(window).unload(function () {
        if ($.cometd.reload) {
            $.cometd.reload();
            // Save the application state only if the user was chatting
            if (_wasConnected && _from) {
                var expires = new Date();
                expires.setTime(expires.getTime() + 5 * 1000);
                org.cometd.COOKIE.set('com.ihelpoo.comet.notice.state', org.cometd.JSON.toJSON({
                    from: _from,
                    to: _to
                }), { 'max-age': 5, expires: expires });
            }
        } else {
            $.cometd.disconnect();
        }
    });
};
