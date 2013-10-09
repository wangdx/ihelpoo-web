$().ready(function(){
	$.ajax({
        type: "POST",
        url: baseUrl + "ajax/getmessage",
        global: false,
        data: {acquireseconds: 'default'},
        dataType: "json",
        success: function (msg) {
            if (msg.status == 'ok') {
                var acquiremilliseconds = msg.data.acquireSeconds;
                var totalmsgnums = parseInt(msg.data.messageSystemNums) + parseInt(msg.data.messageCommentNums) + parseInt(msg.data.messageAtNums) + parseInt(msg.data.messageTalkNums);
                alert(totalmsgnums);
                if (msg.data.messageSystemNums != 0) {
                    $('#message_system').find('.ui-btn-text').append( "<span class='red_l f12'>("+msg.data.messageSystemNums+")</span>");
                }
                if (msg.data.messageCommentNums != 0) {
                	$('#message_comment').append(msg.data.messageSystemNums);
                }
                if (msg.data.messageAtNums != 0) {
                	$('#message_at').append(msg.data.messageSystemNums);
                }
                if (msg.data.messageTalkNums != 0) {
                	$('#message_talk').append(msg.data.messageSystemNums);
                }
            } else {
            	alert(msg.info);
            }
        },
        timeout: 10000,
        error: function () {
            alert('圈圈亲，系统检测到您断网了!');
            setTimeout('mseeageNumsOnce()', 1000);
        }
    });
});