$().ready(function(){
	var islogin = WB2.checkLogin();
	if (!islogin) {
	    WB2.anyWhere(function(W){
	    	W.widget.connectButton({
	    		id: "wb_connect_btn",
	    		type:'3,3',
	    		callback : {
	    			login:function(o){
	    			    //登录后的回调函数
	    			    $.ajax({
	    			        type: "POST",
	    			        url: baseUrl + "user/loginweibo",
	    			        data: {
	    			        	"i_w_user_id" : o.id ,
	    			        	"i_w_user_name" : o.name ,
	    			        	"i_w_user_sex" : o.gender ,
	    			        	"i_w_user_description" : o.description
	    			        },
	    			        dataType: 'json',
	    			        success:function(msg){
	    			            if (msg.status == 'ok') {
									window.location = baseUrl + '/stream';
								} else if (msg.status == 'step') {
									alert(msg.info);
									window.location = baseUrl + msg.data;
								} else if (msg.status == 'wrong') {
									alert(msg.info);
								}
	    			        }
	    			    });
	    			},
	    			logout:function(){
	    			    //退出后的回调函数
	    				window.location = baseUrl + 'user/quit';
	    			}
	    		}
	    	});
	    });
	} else {
		WB2.anyWhere(function(W){
	    	W.widget.connectButton({
	    		id: "wb_connect_btn",
	    		type:'3,3',
	    		callback : {
	    			login:function(o){
	    				$(".loginbox_password_bg").hide();
	    				$(".loginbox_password_bg").hide();
	    				$(".loginbox_select").html("<a href='/stream'>进入首页</a>");
	    				$(".loginbox_submit").slideUp('fast');
	    				$(".loginbox_weibo_qq").slideDown('fast');
	    				alert("你已经通过微博登录");
	    			},
	    			logout:function(){
	    			    //退出后的回调函数
	    				window.location = baseUrl + 'user/quit';
	    			}
	    		}
	    	});
	    });
	}
});