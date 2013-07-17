$().ready(function(){    
    WB2.anyWhere(function(W){
    	W.widget.connectButton({
    		id: "wb_connect_btn",	
    		type:'5,3',
    		callback : {
    			login:function(o){
    			    //登录后的回调函数
    			    //$('#user_info').html("id: " + o.id + " <br />name: " + o.name + " <br />sex: " + o.gender + " <br />description: " + o.description + " <br />online: " + o.online_status);
    		        $('.bind_info').html('你要绑定的微博是：' + o.name);
    		        $('#weibo_user_id').val(o.id);
    			},
    			logout:function(){
    			    //退出后的回调函数
    				alert('logout');
    			}
    		}
    	});
    });
    $('#bind_weibo_btn').click(function(){
        var weibo_user_id = $('#weibo_user_id').val();
        if (weibo_user_id == '') {
        	alert('请先登录你要绑定的微博');
        } else {
	        $.ajax({
	            type: "POST",
	            url: baseUrl + "setting/bind",
	            data: {
	            	"weibo_user_id" : weibo_user_id,
	            },
	            dataType: "json",
	            success:function(msg){
	            	alert(msg.info);
	                if (msg.status == 'ok') {
	                }
	            }
	        });
    	}
    });
});