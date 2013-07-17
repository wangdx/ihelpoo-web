$().ready(function(){
	var $infoLoading = $('<img/>').attr({'src': baseUrl + 'Public/image/common/loading.gif', 'title': '处理中...请稍等'});
	
    /**
     * buy button
     */
    $('#buyassess_btn').click(function(){
    	var caid = $(this).attr('value');
    	var starresult = $('#result').text();
    	var anonymous = $(':checked').val();
    	var assesstextarea = $('#buyassess_textarea').val();
    	if (assesstextarea =='') {
    		alert('你还没有填评价内容呢');
    	} else if ('?' == starresult) {
    		alert('打个分吧:)');
    	} else {
    		$(this).ajaxStart(function(){
        		$(this).after($infoLoading);
        	}).ajaxComplete(function(){
        		$infoLoading.remove();
        	});
        	$.ajax({
        		type: "POST",
        		dataType: "json",
        		url: baseUrl + "/mallset/buyassess",
        		data:{caid: caid, starresult: starresult, anonymous: anonymous, assesstextarea: assesstextarea},
        		success:function(msg){
        			if (msg.status == 'ok') {
        				window.location = baseUrl + msg.data;
        			} else {
        				$('#sure_btn').html("<span class='icon_attention'></span>" + msg.info);
        			}
        		}
        	});
    	}
    });
    
    /**
     * score star
     */
    var star = document.getElementById("star");
    var star_li = star.getElementsByTagName("li");
    var star_word = document.getElementById("star_word");
    var result = document.getElementById("result");
    var i=0;
    var j=0;
    var len = star_li.length;
    var word = ['差评! 很差','差评! 差','中评! 一般',"好评! 好","好评! 非常好"]
    for(i=0; i<len; i++){
    	star_li[i].index = i;
    	star_li[i].onmouseover = function(){
    		star_word.style.display = "inline-block";
    		star_word.innerHTML = word[this.index];
    		for(i=0; i<=this.index; i++){
    			star_li[i].className = "act_orange";
    		}
    		for(i=this.index+1; i<=5; i++){
    			star_li[i].className = "";
    		}
    	}
    	star_li[i].onmouseout = function(){
    		star_word.style.display = "none";
    		for(i=this.index+1; i<=5; i++){
    			star_li[i].className = "";
    		}
    	}
    	star_li[i].onclick = function(){
    		result.innerHTML = (this.index+1);
    		for(i=0; i<=this.index; i++){
    			star_li[i].className = "act";
    		}
    		for(i=this.index+1; i<=5; i++){
    			star_li[i].className = "";
    		}
    	}
    }
});