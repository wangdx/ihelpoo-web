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
                if (totalmsgnums != 0) {
                	$('#footer_mobile_message').find('.ui-btn-text').append(" <span class='reb_back'>("+msg.data.messageSystemNums+")</span>");
                }
                if (msg.data.messageSystemNums != 0) {
                    $('#message_system').find('.ui-btn-text').append(" <span class='reb_back'>("+msg.data.messageSystemNums+")</span>");
                }
                if (msg.data.messageCommentNums != 0) {
                	$('#message_comment').find('.ui-btn-text').append(" <span class='reb_back'>("+msg.data.messageCommentNums+")</span>");
                }
                if (msg.data.messageAtNums != 0) {
                	$('#message_at').find('.ui-btn-text').append(" <span class='reb_back'>("+msg.data.messageAtNums+")</span>");
                }
                if (msg.data.messageTalkNums != 0) {
                	$('#message_talk').find('.ui-btn-text').append(" <span class='reb_back'>("+msg.data.messageTalkNums+")</span>");
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