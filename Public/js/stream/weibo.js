$().ready(function(){
    $('.weibo_sdk_message').hide();
    var weibo_status = 0;
    var weibo_follower = 0;
    var weibo_cmt = 0;
    var weibo_dm = 0;
    var weibo_at = 0;
    WB2.anyWhere(function(W){
    	W.parseCMD("/remind/unread_count.json", function(sResult, bStatus){
    	    if(bStatus == true) {
    	    	weibo_status = sResult.status;
    	    	weibo_follower = sResult.follower;
    	    	weibo_cmt = sResult.cmt;
    	    	weibo_dm = sResult.dm;
    	    	//weibo_dm = 7;
    	    	weibo_at = sResult.mention_status + sResult.mention_cmt;
    	    	//weibo_at = 1;
    	    	//alert("新微博未读数: " + sResult.status + " <br />follower: " + sResult.follower + " <br />新评论数: " + sResult.cmt + " <br />新私信数: " + sResult.dm + " <br />新提及我的微博数: " + sResult.mention_status + " <br />新提及我的评论数: " + sResult.mention_cmt);
				if (weibo_at != '') {
					$('.weibo_sdk_message').show().append("<li><a href='http://weibo.com/at/weibo' class='black_l' target='_blank'>@提到我的: " + weibo_at + "</a></li>");
				}
				if (weibo_cmt != '') {
					$('.weibo_sdk_message').show().append("<li><a href='http://weibo.com/comment/inbox' class='black_l' target='_blank'>新评论数: " + weibo_cmt + "</a></li>");
				}
				if (weibo_dm != '') {
					$('.weibo_sdk_message').show().append("<li><a href='http://weibo.com/messages' class='black_l' target='_blank'>新私信数: " + weibo_dm + "</a></li>");
				}
				if (weibo_follower != '') {
					$('.weibo_sdk_message').show().append("<li><a href='http://weibo.com/' class='black_l' target='_blank'>新粉丝数: " + weibo_follower + "</a></li>");
				}
    	    }
    	},{
    	},{
    		method: 'get'
    	});
    });
    $('.weibo_sdk_message .icon_quit').click(function(){
        $(this).parent().slideUp('fast');
    });
    $('#weibo_sdk_btn').toggle(
        function(){
            $(this).addClass("btn_weibo_select");
            $('#weibo_publish_li').slideDown('fast');
            $('#weibo_is_publish').val('on');
            $.ajax({
                type: "POST",
                url: baseUrl + "ajax/weiboswitch",
                data: "changeswitch=true",
                dataType: "json",
                success:function(msg){

                }
            });
        },
        function(){
            $(this).removeClass("btn_weibo_select").addClass("btn_weibo");
            $('#weibo_publish_li').slideUp('fast');
            $('#weibo_is_publish').val('');
            $.ajax({
                type: "POST",
                url: baseUrl + "ajax/weiboswitch",
                data: "changeswitch=false",
                dataType: "json",
                success:function(msg){

                }
            });
        }
    );
    WB2.anyWhere(function(W){
    	W.widget.publish({
    		id : 'standardSelector'
    	});
    });
});