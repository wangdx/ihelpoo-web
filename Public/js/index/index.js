$().ready(function(){
	
	/**
	 * css style image background
	 */
	var bgheight,bgwidth;
	var windowheight = $(window).height();
	var windowwidth = $(window).width();
	var documentheight = '620';
	var documentwidth = $(document.body).width();
	if (documentheight < windowheight) {
		bgheight = windowheight;
	} else {
		bgheight = documentheight;
	}
	if (windowwidth < documentwidth) {
		bgwidth = documentwidth;
	} else {
		bgwidth = windowwidth;
	}
	$('#lay_bg').css({ width: bgwidth, height: bgheight});
	$('#lay_bg_img').css({ width: bgwidth, height: bgheight}).fadeIn('fast');
	$('.texture_background').css({ width: bgwidth, height: bgheight});
	$(window).resize(function(){
		loadBackground();
	});
	
	$('#email').focus(function(){
		var emailvalue = $('#email').val();
		if (emailvalue == '邮箱/手机号') {
			$('#email').val('');
		}
	});
	
    var $infoLoading = $('<img/>').attr({'src': baseUrl + 'Public/image/common/ajax_wait_login.gif', 'title': '检测中...请稍等'});
    $('#email').blur(function(){
        var emailcheck = $('#email').val();
        $('#logininfo').html($infoLoading);
        if (emailcheck == '') {
            $('#logininfo').html('× 邮箱不能为空');
        } else {
            $.ajax({
                type: 'POST',
                url: baseUrl + 'user/ajaxcheckemail',
                dataType: 'json',
                data: { email: emailcheck },
                success: function(msg) {
                    if ('ok' == msg.status) {
                        $('#logininfo').fadeIn().html('× 用户不存在');
                    } else if ('exist' == msg.status) {
                        $('#logininfo').fadeOut();
                    } else if ('wrong' == msg.status) {
                        $('#logininfo').fadeIn().html('× ' + msg.info);
                    } else if ('mobile' == msg.status) {
                    	$('#logininfo').fadeOut();
                    }
                }
            });
        }
    });
    
    /**
     * app show title info 
     */
    $('.app_show_title').click(function(){
    	var titleinfo = $(this).attr('title');
    	$('.app_show_title_info_p').slideDown('fast').html('<i class="icon_words"></i> ' + titleinfo).delay('2000').slideUp('fast');
    });
    
    /**
     * click submit
     */
    $('#submit').click(function(){
    	var emailcheck = $('#email').val();
    	var passwordcheck = $('#password').val();
        var passwordlegth = passwordcheck.length;
        if (emailcheck == '') {
        	$('#logininfo').fadeIn().html('× 邮箱不能为空');
        } else if (passwordcheck == '') {
            $('#logininfo').fadeIn().html('× 密码不能为空');
        } else if (passwordlegth < 6) {
            $('#logininfo').fadeIn().html('× 密码最短不能少于6个字符');
        } else {
            $('#logininfo').fadeOut();
            $.ajax({
                type: 'POST',
                url: baseUrl + 'user/ajaxcheckpassword',
                dataType: 'json',
                data: { email: emailcheck, password: passwordcheck },
                success: function(msg) {
                	if (msg.status == 'ok') {
                		$("form").submit();
                	} else {
                		$('#logininfo').fadeIn().html('× ' + msg.info);
                	}
                }
            });
        }
    });

    /**
     * enter keydown submit
     */
    $(window).keydown(function(e){
    	if(e.keyCode == 13) {
    		$('#submit').click();
    		document.body.focus();
    	}
    });
    
    /**
     * switch login
     */
    $('#login_weibo_qq_switch').click(function(){
    	var isclick = $(this).attr('isclick');
    	if (isclick == 'false') {
    		$('.loginbox_submit').slideUp('fast');
    		$('.loginbox_weibo_qq').slideDown('fast');
    		$(this).attr({'isclick':'true'});
    		$(this).text('正常登录');
    	} else {
    		$('.loginbox_submit').slideDown('fast');
    		$('.loginbox_weibo_qq').slideUp('fast');
    		$(this).attr({'isclick':'false'});
    		$(this).text('微博、QQ登录');
    	}
    });
    
    /**
     * weibo
     */
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
									window.location = baseUrl + 'stream';
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
	    			            if (msg.status == 'step') {
									alert(msg.info);
								} else if (msg.status == 'wrong') {
									alert(msg.info);
								}
	    			        }
	    			    });
	    				$(".loginbox_email_bg").hide();
	    				$(".loginbox_password_bg").hide();
	    				$("#qqLoginBtn").hide();
	    				$(".loginbox_more").hide();
	    				$(".loginbox_select").html("你已经通过微博登录，<a href='/stream' class='f14'>进入首页</a>");
	    				$(".loginbox_submit").slideUp('fast');
	    				$(".loginbox_weibo_qq").slideDown('fast');
	    			},
	    			logout:function(){
	    			    //退出后的回调函数
	    				window.location = baseUrl + 'user/quit';
	    			}
	    		}
	    	});
	    });
	}
	
	/**
	 * qq
	 */
	var isqqlogin = QC.Login.check();
	if (!isqqlogin) {
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
						"i_qq_user_name" : o.data.nickname ,
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
				});
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

function loadBackground()
{
	var bgheight,bgwidth;
	var windowheight = $(window).height();
	var windowwidth = $(window).width();
	var documentheight = '620';
	var documentwidth = $(document.body).width();
	if (documentheight < windowheight) {
		bgheight = windowheight;
	} else {
		bgheight = documentheight;
	}
	if (windowwidth < documentwidth) {
		bgwidth = documentwidth;
	} else {
		bgwidth = windowwidth;
	}
	$('#lay_bg').css({ width: bgwidth, height: bgheight});
	$('#lay_bg_img').css({ width: bgwidth, height: bgheight});
	$('.texture_background').css({ width: bgwidth, height: bgheight});
}