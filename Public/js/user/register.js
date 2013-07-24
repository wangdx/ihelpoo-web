$().ready(function(){
    var $infoLoading = $('<img/>').attr({'src': baseUrl + 'Public/image/common/ajax_wait_login.gif', 'title': '检测中...请稍等'});
    emailok = 'no';
    passwordok = 'no';
    nicknameok = 'no';
    $('#email').blur(function(){
        emailok = 'no';
        var emailcheck = $('#email').val();
        $(this).ajaxStart(function(){
        	$('#emailinfo').html($infoLoading);
        }).ajaxStop(function(){
        	$infoLoading.remove();
        });
        if (emailcheck == '') {
            $('#emailinfo').html('<span class="icon_index_wrong"></span>邮箱不能为空');
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
                    } else if ('movedata' == msg.status) {
                        $('#emailinfo').html('<span class="icon_right"></span>');
                        alert(msg.info);
                        window.location = baseUrl;
                    } else if ('exist' == msg.status) {
                        $('#emailinfo').html('<span class="icon_index_wrong"></span>' + msg.info);
                    } else if ('wrong' == msg.status) {
                        $('#emailinfo').html('<span class="icon_index_wrong"></span>' + msg.info);
                    }
                }
            });
        }
    });
    
    $('#password').blur(function(){
        var passwordcheck = $('#password').val();
        var passwordlegth = passwordcheck.length;
        if (passwordcheck == '') {
            $('#passwordinfo').html('<span class="icon_index_wrong"></span>密码不能为空');
        } else if (passwordlegth < 6) {
            $('#passwordinfo').html('<span class="icon_index_wrong"></span>密码最短不能少于6个字符');
        } else {
            $('#passwordinfo').html('<span class="icon_right"></span>');
        }
    });
    $('#passwordrepeat').blur(function(){
        passwordok = 'no';
        var passwordcheck = $('#password').val(),
            passwordrepeatcheck = $('#passwordrepeat').val();
        if (passwordrepeatcheck == '') {
            $('#passwordrepeatinfo').html('<span class="icon_index_wrong"></span>请重复密码');
        } else if (passwordrepeatcheck != passwordcheck) {
            $('#passwordrepeatinfo').html('<span class="icon_index_wrong"></span>两次密码不一致');
        } else {
            $('#passwordrepeatinfo').html('<span class="icon_right"></span>');
            passwordok = 'ok';
        }
    });
    $('#nickname').blur(function(){
        nicknameok = 'no';
        var nicknamecheck = $('#nickname').val();
        var nicknamelength = getStringLength(nicknamecheck);
        if (nicknamecheck == '') {
            $('#nicknameinfo').html('<span class="icon_index_wrong"></span>昵称不能为空');
        } else if (nicknamelength > 25) {
            $('#nicknameinfo').html('<span class="icon_index_wrong"></span>昵称太长了');
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
                        $('#nicknameinfo').html('<span class="icon_index_wrong"></span>昵称已经存在');
                    }
                }
            });
        }
    });
    
    /**
     * click submit
     */
    $('#registe_submit').click(function(){
        if ('ok' == emailok && 'ok' == passwordok && 'ok' == nicknameok) {
        	$("form").submit();
        } else {
            $('#registeinfo').html('<span class="icon_index_wrong"></span>出错了! 请检测填写项');
            if (emailok == 'no') {
            	$('#emailinfo').html('<span class="icon_attention"></span>');
            }
            if (passwordok == 'no') {
            	$('#passwordinfo').html('<span class="icon_attention"></span>');
            }
            if (nicknameok == 'no') {
            	$('#nicknameinfo').html('<span class="icon_attention"></span>');
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
    
    var $infoLoading = $('<img/>').attr({'src': baseUrl + 'Public/image/common/progressbar.gif', 'title': '加载中...请稍等'});
    $("#selectschool").click(function(){
        $(this).ajaxStart(function(){
        	$('#ajaxprogressbar').html($infoLoading);
        }).ajaxStop(function(){
        	$infoLoading.remove();
        });
        $.ajax({
            type: "POST",
            url: baseUrl + "setting/ajax",
            data: "getschoollist='get'",
            datatype: "text",
            success:function(list){
                $("#ajaxprogressbar").html(list);
            }
        });
    });
});