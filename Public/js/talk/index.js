$().ready(function(){
    flashPicCon('.flash_icon');
    $('#random_talk').live("click", function(){
        $('.random_user_show_box').fadeOut(0);
        $('.talk_index_left').fadeTo(800, 0.2);
        var x2 = 0;
        var y2 = 6;
        var rand2 = parseInt(Math.random() * (x2 - y2 + 1) + y2);
        if (rand2 == 1) {
            $('#random_user_show').css({background:"#6ccacd"});
        }
        if (rand2 == 2) {
            $('#random_user_show').css({background:"#9C0"});
        }
        if (rand2 == 3) {
            $('#random_user_show').css({background:"#F60"});
        }
        if (rand2 == 4) {
            $('#random_user_show').css({background:"#FC0"});
        }
        if (rand2 == 5) {
            $('#random_user_show').css({background:"#999"});
        }
        
    	$.ajax({
    	    type: "POST",
    	    dataType: "json",
    	    url: baseUrl + "/talk",
    	    data:{random_talk: 'start'},
    	    success:function(msg){
    	        if (msg.data.sex == '1') {
    	            var sex = '帅哥';
    	        } else {
    	            var sex = '美女';
    	        }
    	        if (msg.data.constellation != '') {
    	            var constellation = msg.data.constellation;
    	        } else {
    	            var constellation = '+';
    	        }
    	        $('.random_user_show_box').fadeIn(500);
    	        $('#random_user_show').html(
    	            '<a href="' + baseUrl + 'wo/' + msg.data.uid + '"><img src="' + msg.data.image + '" height="51" class="radius3"/></a>'+
    	            '<p class="ajax_random_u_s_text">和来自 <a href="http://' + msg.data.domain + '">' + msg.data.school + '</a>' + msg.data.academy + '的 ' + constellation + sex + 
    	            ' <a href="' + baseUrl + 'wo/' + msg.data.uid + '" class="fb">' + msg.data.nickname + '</a>('+ msg.data.grade +') 聊天<p>'+
    	            '<p><a href="' + baseUrl + 'talk/to/' + msg.data.uid + '" class="fb">开始聊天？</a></p>'
    	        );
    	    }
    	});
    });
    $('#random_talk_show_close').live("click", function(){
        $('.talk_index_left').fadeTo(800, 1);
        $('.random_user_show_box').slideUp(800);
    });
});
function flashPicCon(name){
	$(name).fadeOut('slow',function(){
		$(this).fadeIn('fast');
		flashPicCon(name);
	});
}