$().ready(function(){
	var isqqlogin = QC.Login.check();
	if (!isqqlogin) {
		QC.Login({
			btnId:"qqLoginBtn" 
		});
		var paras = {};
		QC.api("get_user_info", paras).success(function(o){
			alert(o.nickname);
			QC.api("get_info", paras).success(function(s){
				/*$.ajax({
					type: "POST",
					url: baseUrl + "user/loginqqaajax",
					data: {
						"i_qq_user_id" : s.data.data.openid ,
						"i_qq_user_name" : o.nickname ,
						"i_qq_user_sex" : s.data.data.sex ,
						"i_qq_user_birth_day" : s.data.data.birth_day ,
						"i_qq_user_birth_month" : s.data.data.birth_month ,
						"i_qq_user_birth_year" : s.data.data.birth_year
					},
					dataType: 'json',
					success:function(msg){
						if (msg.status == 'ok') {
							window.location = baseUrl + msg.data;
						} else if (msg.status == 'step') {
							alert(msg.info);
							window.location = baseUrl + msg.data;
						} else if (msg.status == 'wrong') {
							alert(msg.info);
						}
					}
				});*/
			})
			.error(function(f){
				alert("获取用户信息失败！");
			});
		})
		.error(function(f){
			alert("获取用户信息失败！");
		});
	} else {
		QC.Login({
			btnId:"qqLoginBtn" 
		});
		var paras = {};
		QC.api("get_info", paras).success(function(s){
			$.ajax({
				type: "POST",
				url: baseUrl + "user/loginqqaajax",
				data: {
					"i_qq_user_id" : s.data.data.openid ,
					"i_qq_user_name" : s.data.data.nickname ,
					"i_qq_user_sex" : s.data.data.sex ,
					"i_qq_user_birth_day" : s.data.data.birth_day ,
					"i_qq_user_birth_month" : s.data.data.birth_month ,
					"i_qq_user_birth_year" : s.data.data.birth_year
				},
				dataType: 'json',
				success:function(msg){
					if (msg.status == 'step') {
						alert(msg.info);
					} else if (msg.status == 'wrong') {
						alert(msg.info);
					}
					$(".loginbox_email_bg").hide();
    				$(".loginbox_password_bg").hide();
    				$("#wb_connect_btn").hide();
    				$("#qqLoginBtn").css({'left':'0px'});
    				$(".loginbox_more").hide();
    				$(".loginbox_select").html("你已经通过QQ登录，<a href='/stream' class='f14'>进入首页</a>");
    				$(".loginbox_submit").slideUp('fast');
    				$(".loginbox_weibo_qq").slideDown('fast');
				}
			});
		})
		.error(function(f){
			alert("获取用户信息失败！");
		});
	}
});