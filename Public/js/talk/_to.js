$(function(){
    "use strict";

    var socket = $.atmosphere;
    var chatSocket;
    var transport = 'websocket';


    // We are now ready to cut the request
    var request = {
        url: "http://comet.ihelpoo.com/c1/chat/10000-12419",
        logLevel: 'debug',
        transport: 'websocket',
        fallbackTransport: 'long-polling',
        callback: call,
        enableXDR: true,
        dropAtmosphereHeaders: true,
        readResponsesHeaders: false,
        attachHeadersAsQueryString: true
    };


    request.onOpen = function(response) {
        console.log("onOpen");
    };


    request.onMessage = function (response) {
        console.log("onmessage");
    };

    chatSocket = socket.subscribe(request);



//    chatSocket = $.atmosphere.subscribe("http://comet.ihelpoo.com/c1/chat/10000-12419", globalCallback, $.atmosphere.request = {
//        logLevel: 'debug',
//        transport: 'websocket',
//        fallbackTransport: 'long-polling',
//        callback: call,
//        enableXDR: true,
//        dropAtmosphereHeaders: true,
//        readResponsesHeaders: false,
//        attachHeadersAsQueryString: true
//    });

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

    function getDataFromResponse(response) {
        var detectedTransport = response.transport;
        console.log("[DEBUG] Real transport is <" + detectedTransport + ">");
        if (response.transport != 'polling' && response.state != 'connected' && response.state != 'closed') {
            if (response.status == 200) {
                return response.responseBody;
            }
        }
        return null;
    }

    /**
     * send messgae
     */
    $('#send_message').click(function () {
        chatSocket.push({data: 'message=hello, world'});
        console.log(chatSocket);
        return false;
    });


});