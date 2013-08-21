$().ready(function(){
    var $infoLoading = $('<img/>').attr({'src': baseUrl + 'Public/image/common/ajax_wait_login.gif', 'title': '检测中...请稍等'});
    emailok = 'no';
    passwordok = 'no';
    nicknameok = 'no';
    $('#email').blur(function(){
        emailok = 'no';
        var emailcheck = $('#email').val();
        $('#emailinfo').html($infoLoading);
        if (emailcheck == '') {
            $('#emailinfo').html('× 邮箱不能为空');
        } else {
            $.ajax({
                type: 'POST',
                url: baseUrl + 'user/ajaxcheckemail',
                global: false,
                dataType: 'json',
                data: { email: emailcheck },
                success: function(msg) {
                    if ('ok' == msg.status) {
                        $('#emailinfo').html('<span class="icon_right"></span>');
                        emailok = 'ok';
                    } else if ('exist' == msg.status) {
                        $('#emailinfo').html('× ' + msg.info);
                    } else if ('wrong' == msg.status) {
                        $('#emailinfo').html('× ' + msg.info);
                    }
                }
            });
        }
    });
    
    $('#password').blur(function(){
        var passwordcheck = $('#password').val();
        var passwordlegth = passwordcheck.length;
        if (passwordcheck == '') {
            $('#passwordinfo').html('× 密码不能为空');
        } else if (passwordlegth < 6) {
            $('#passwordinfo').html('× 密码不能少于6个字符');
        } else {
            $('#passwordinfo').html('<span class="icon_right"></span>');
        }
    });
    $('#passwordrepeat').blur(function(){
        passwordok = 'no';
        var passwordcheck = $('#password').val(),
            passwordrepeatcheck = $('#passwordrepeat').val();
        if (passwordrepeatcheck == '') {
            $('#passwordrepeatinfo').html('× 请重复密码');
        } else if (passwordrepeatcheck != passwordcheck) {
            $('#passwordrepeatinfo').html('× 两次密码不一致');
        } else {
            $('#passwordrepeatinfo').html('<span class="icon_right"></span>');
            passwordok = 'ok';
        }
    });
    $('#nickname').blur(function(){
    	nicknameok = 'no';
        var nicknamecheck = $('#nickname').val();
        var nicknamelength = nicknamecheck.length;
        $('#nicknameinfo').html($infoLoading);
        if (nicknamecheck == '') {
            $('#nicknameinfo').html('× 昵称不能为空');
        } else if (nicknamelength > 25) {
            $('#nicknameinfo').html('× 昵称太长了');
        } else {
        	$.ajax({
                type: 'POST',
                url: baseUrl + 'user/ajaxchecknickname',
                global: false,
                dataType: 'json',
                data: { nickname: nicknamecheck },
                success: function(msg) {
                    if ('ok' == msg.status) {
                        $('#nicknameinfo').html('<span class="icon_right"></span><span class="f12 gray">系统忽略特殊字符</span>');
                        nicknameok = 'ok';
                    } else if ('exist' == msg.status) {
                        $('#nicknameinfo').html('× 昵称已经存在');
                    }
                }
            });
        }
    });
    
    /**
     * user type
     */
    $('.user_type').click(function(){
    	var usertype = $(this).attr('value');
    	var isclick = $(this).attr('isclick');
    	if (isclick == 'false') {
    		$('.user_type').attr({isclick:'false'});
	    	$(this).attr({isclick:'true'});
	    	$('#input_user_type').val(usertype);
	    	$('.user_type').find('.icon_right').remove();
	    	$(this).append('<span class="icon_right"></span>');
    	} else if (isclick == 'true') {
    		$('.user_type').attr({isclick:'false'});
	    	$('#input_user_type').val('default');
	    	$('.user_type').find('.icon_right').remove();
    	}
    });
    
    /**
     * click submit
     */
    $('#registe_submit').click(function(){
        if ('ok' == emailok && 'ok' == passwordok && 'ok' == nicknameok) {
        	$("form").submit();
        } else {
            $('#registeinfo').html('× 出错了! 请检测填写项');
            if (emailok == 'no') {
            	$('#emailinfo').html('<span class="icon_attention"></span> 请重新输入');
            }
            if (passwordok == 'no') {
            	$('#passwordinfo').html('<span class="icon_attention"></span> 请重新输入');
            }
            if (nicknameok == 'no') {
            	$('#nicknameinfo').html('<span class="icon_attention"></span> 请重新输入');
            }
        }
    });
    
    /**
     * enter keydown submit
     */
    $(window).keydown(function(e){
    	if(e.which == 13) { 
    		$('#registe_submit').click();
    		document.body.focus();
    	}         
    })
    
    var $infoLoadingBar = $('<img/>').attr({'src': baseUrl + 'Public/image/common/progressbar.gif', 'title': '加载中...请稍等'});
    $(".academyselectschool").click(function(){
        $('#ajaxprogressbar').html($infoLoadingBar);
        $.ajax({
            type: "POST",
            url: baseUrl + "user/register",
            data: "getschoollist='get'",
            datatype: "text",
            success:function(list){
                $("#ajaxprogressbar").html(list);
            }
        });
    });
    $("#setting_school_close_span").live('click', function(){
        $(this).parent().fadeOut('fast');
    });
});