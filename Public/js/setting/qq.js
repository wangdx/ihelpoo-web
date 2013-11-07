$().ready(function(){    
    QC.Login({
		btnId:"qqLoginBtn" 
	});
	var paras = {};
	QC.api("get_user_info", paras).success(function(o){
		QC.api("get_info", paras).success(function(s){
			$.ajax({
				type: "POST",
				url: baseUrl + "user/loginqqaajax",
				data: {
					"i_qq_user_id" : s.data.data.openid ,
					"i_qq_user_name" : o.data.nickname
				},
				dataType: 'json',
				success:function(msg){
					$('.bind_info').html('你要绑定的QQ是：' + o.data.nickname);
					$('#qq_user_id').val(s.data.data.openid);
				}
			});
		})
		.error(function(f){
			alert("获取用户信息失败！");
		});
	})
	.error(function(f){
		alert("获取用户信息失败！");
	});
    
    $('#bind_qq_btn').click(function(){
        var qq_user_id = $('#qq_user_id').val();
        if (qq_user_id == '') {
        	alert('请先登录你要绑定的QQ');
        } else {
	        $.ajax({
	            type: "POST",
	            url: baseUrl + "setting/bindqq",
	            data: {
	            	"qq_user_id" : qq_user_id
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