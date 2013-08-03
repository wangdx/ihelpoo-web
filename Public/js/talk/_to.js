$(function () {
    "use strict";

    var socket = $.atmosphere;
    var chatSocket;
    var transport = 'websocket';

    /**
     * send messgae
     */
    $('#send_message').click(function () {
        var newContent = $('#send_message_textarea').val();
        chatSocket.push({data: 'message=' + newContent});
        return false;
    });


    // We are now ready to cut the request
    var request = {
        url: "http://comet.ihelpoo.com/c1/chat/10000-12419",
        logLevel: 'debug',
        transport: 'long-polling',
        fallbackTransport: 'long-polling',
        callback: call,
        enableXDR: true,
        dropAtmosphereHeaders: true,
        readResponsesHeaders: false,
        attachHeadersAsQueryString: true
    };


    request.onMessage = function (response) {
//        if(console)console.log("onmessage");

        var msg = response.responseBody;

        var htmlIn = " <span class='f14 gray '>" + msg + "</span>"
            + " <span class='f12 gray'>" + msg + "</span><br />"
            + msg + "<br /><br />";

        $('#show_message_div').append(htmlIn);
        var boxHeight = $('#show_message_div').height();
        $('#show_message_div_outer').animate({scrollTop: boxHeight}, 800);
        $('#send_message_textarea').val('');
        $('#image_upload_url').val('');
        $('#image_upload_list_ul').empty();
        $('.img_upload_comment_form_div').slideUp('fast');
    };


    request.onOpen = function (response) {
//        if (console) {
//            console.log("[INFO]: opening to talk");
//        }
        transport = response.transport;
    };


    request.onReopen = function (response) {
//        if (console) {
//            console.log("[INFO]: reopen to talk");
//        }
    };

    request.onTransportFailure = function (errorMsg, request) {
        if (window.EventSource) {
            request.fallbackTransport = "sse";
        }
    };

    chatSocket = socket.subscribe(request);


    function call(response) {
        //if(console)console.log("Call to callbackJob2");
        if (response.state != "messageReceived") {
            return;
        }
        var data = getDataFromResponse(response);
        if (data != null) {
            //if(console)console.log("---" + data);
        }
    }

    function getDataFromResponse(response) {
        var detectedTransport = response.transport;
        //if(console)console.log("[DEBUG] Real transport is <" + detectedTransport + ">");
        if (response.transport != 'polling' && response.state != 'connected' && response.state != 'closed') {
            if (response.status == 200) {
                return response.responseBody;
            }
        }
        return null;
    }


});