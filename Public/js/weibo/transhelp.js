$().ready(function(){
	$('#transhelpsubmit').click(function(){
		var weibourl = $('#transhelpweibourl').val();
		var weibourlarray = weibourl.split("/");
		var weibourlstring = weibourlarray[4];
		var weibourlstringhandled = weibourlstring.substr(0 ,9);
		WB2.anyWhere(function(W){
	    	W.parseCMD("/statuses/queryid.json", function(sResult, bStatus){
	    	    if(bStatus == true) {
	    	    	var weibo_id  = sResult.id;
	    	    	WB2.anyWhere(function(W){
	    	        	W.parseCMD("/statuses/show.json", function(sResultShow, bStatusShow){
	    	        	    if(bStatusShow == true) {
	    	        	    	var weibo_text = sResultShow.text;
	    	        	    	var weibo_source = sResultShow.source;
	    	        	    	var weibo_originalpic  = sResultShow.original_pic;
	    	        	    	var weibo_nickname = sResultShow.user.screen_name;
	    	        	    	var posttext = '#微博求助# via @' + weibo_nickname + ' : ' + weibo_text;
	    	        	    	$.ajax({
	    	                        type: "POST",
	    	                		dataType: "json",
	    	                		url: baseUrl + "weibo/transhelp",
	    	                		data:{commenttext: posttext, weiboid: weibo_id, weibopic: weibo_originalpic},
	    	                		success:function(msg){
	    	                			if (msg.status == 'ok') {
	    	                				var newitemsayurl = baseUrl + msg.data;
	    	                				WB2.anyWhere(function(W){
	    		    	        	        	W.parseCMD("/comments/create.json", function(sResultCreate, bStatusCreate){
	    		    	        	        		if(bStatusCreate == true) {
	    		    	        	        			$('#transhelpweibourl').val('');
	    		    	        	        			alert('移动成功');
	    		    	        	        	    }
	    		    	        	        	},{
	    		    	        	        		id : weibo_id,
	    		    	        	        		comment : '我们将你的求助放到了我帮圈圈社区，希望你能快速得到帮助，请移步:'+newitemsayurl,
	    		    	        	        		comment_ori : '1'
	    		    	        	        	},{
	    		    	        	        		method: 'post'
	    		    	        	        	});
	    		    	        	        });
	    	                			}
	    	                		}
	    	        	    	});
	    	        	    }
	    	        	},{
	    	        		id : weibo_id,
	    	        	},{
	    	        		method: 'get'
	    	        	});
	    	        });
	    	    }
	    	},{
	    		mid : weibourlstringhandled,
	    		type : '1',
	    		isBase62 : '1'
	    	},{
	    		method: 'get'
	    	});
	    });
    });
	/**
    WB2.anyWhere(function(W){
    	W.parseCMD("/statuses/queryid.json", function(sResult, bStatus){
    	    if(bStatus == true) {
    	    	weibo_id = sResult.id;
    	    	alert(weibo_id);
    	    }
    	},{
    		mid : '',
    		type  : '1'
    	},{
    		method: 'get'
    	});
    });
    
    
    WB2.anyWhere(function(W){
    	W.parseCMD("/statuses/show.json", function(sResult, bStatus){
    	    if(bStatus == true) {
    	    	weibo_text = sResult.text;
    	    	weibo_source = sResult.source;
    	    	weibo_originalpic  = sResult.original_pic ;
    	    	weibo_nickname = sResult.user.screen_name ;
    	    }
    	},{
    		id  : '',
    	},{
    		method: 'get'
    	});
    });
    
    WB2.anyWhere(function(W){
    	W.parseCMD("/comments/create.json", function(sResult, bStatus){
    		if(bStatus == true) {
    			
    	    }
    	},{
    		id : '',
    		comment : '',
    		comment_ori : '1'
    	},{
    		method: 'post'
    	});
    });*/
});