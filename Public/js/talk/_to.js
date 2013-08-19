$(function () {
    // Check if there was a saved application state
    var stateCookie = org.cometd.COOKIE ? org.cometd.COOKIE.get('com.ihelpoo.comet.p2p.state') : null;
    var state = stateCookie ? org.cometd.JSON.fromJSON(stateCookie) : null;
    var state = null;
    var chat = new Chat(state);

    chat.join($('#data_uid').val(), $('#data_touid').val());

    $('#send_message').click(chat.send);




    var noActionInterval = 5; // seconds
    $("textarea#send_message_textarea").keypress(function () {
        typing();
    });


    var noTypeTimeout = setTimeout(inActive, noActionInterval * 1000);

    function typing(){
        chat.updateInputStatus('对方正在输入...');
        clearTimeout(noTypeTimeout);
        noTypeTimeout = setTimeout(inActive, noActionInterval * 1000);
    }

    function inActive(){
        chat.updateInputStatus('');
    }

    // restore some values
    if (state) {
        $('#data_uid').val(state.from);
        $('#data_touid').val(state.to);
    }
});

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
            logLevel: 'debug'
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
        var chat = $('#send_message_textarea').val();
        var image = $('#image_upload_url').val();
        if (!chat || !chat.length) return;
        $.cometd.publish('/service/p2ps', {
            room: '/chat/p2p',
            from: _from,
            to: _to,
            chat: chat,
            status: '',
            image: image
        });
    };

    this.updateInputStatus = function(status){
        $.cometd.publish('/service/p2ps', {
            room: '/chat/p2p',
            from: _from,
            to: _to,
            chat: "",
            image: '',
            status:status

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



        if(!chat || !chat.length) {//update status
            $('#input_status').html(status + '<span class="icon_write"></span>');
            return;
        }


        if (!membership && fromUser == _lastUser) {
            fromUser = '...';
        } else {
            _lastUser = fromUser;
            fromUser += ':';
        }

        if (membership) {
            _lastUser = null;
        }


        if (image != '') {
            var htmlIn = " <span class='f14 gray '>" + fromUser + "</span>"
                + " <span class='f12 gray'>" + time + "</span><br />"
                + chat + "<br />"
                + "<a href='" + image + "' target='_target'><img src='" + imagethumb + "' style='max-width:150px;' title='查看原图' /></a><br /><br />";
        } else {
            var htmlIn = " <span class='f14 gray '>" + fromUser + "</span>"
                + " <span class='f12 gray'>" + time + "</span><br />"
                + chat + "<br /><br />";
        }
        $('#show_message_div').append(htmlIn);
        var boxHeight = $('#show_message_div').height();
        $('#show_message_div_outer').animate({scrollTop: boxHeight}, 800);
        $('#send_message_textarea').val('');
        $('#image_upload_url').val('');
        $('#image_upload_list_ul').empty();
        $('.img_upload_comment_form_div').slideUp('fast');
//        imageNums = 0;
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
                org.cometd.COOKIE.set('com.ihelpoo.comet.p2p.state', org.cometd.JSON.toJSON({
                    from: _from,
                    to: _to
                }), { 'max-age': 5, expires: expires });
            }
        } else {
            $.cometd.disconnect();
        }
    });
};
