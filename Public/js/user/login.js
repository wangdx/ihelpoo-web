var firstVisitUrl = location.href;
if (firstVisitUrl == 'http://ihelpoo.sinaapp.com/user/login' || firstVisitUrl == 'http://ihelpoo.sinaapp.com/user/login/') {
	alert('请访问 www.ihelpoo.com 我帮圈圈这个域名 :)，点击确定后页面自动跳转...');
	window.location = 'http://www.ihelpoo.com/';
}

$().ready(function(){
    var $infoLoading = $('<img/>').attr({'src': baseUrl + 'Public/image/common/ajax_wait_login.gif', 'title': '检测中...请稍等'});
    emailok = 'no';
    passwordok = 'no';
    $('#email').blur(function(){
        emailok = 'no';
        var emailcheck = $('#email').val();
        $(this).ajaxStart(function(){
        	$('#emailinfo').html($infoLoading);
        }).ajaxStop(function(){
        	$infoLoading.remove();
        });
        if (emailcheck == '') {
            $('#logininfo').html('<span class="icon_index_wrong"></span>邮箱不能为空');
        } else {
            $.ajax({
                type: 'POST',
                url: baseUrl + 'user/ajaxcheckemail',
                dataType: 'json',
                data: { email: emailcheck },
                success: function(msg) {
                    if ('ok' == msg.status) {
                        $('#logininfo').html('<span class="icon_index_wrong"></span>用户不存在');
                    } else if ('exist' == msg.status) {
                        $('#emailinfo').html('<span class="icon_right"></span>');
                        emailok = 'ok';
                    } else if ('wrong' == msg.status) {
                        $('#logininfo').html('<span class="icon_index_wrong"></span>' + msg.info);
                    }
                }
            });
        }
    });

    /**
     * click submit
     */
    $('#login_submit').click(function(){
    	var emailcheck = $('#email').val();
    	var passwordcheck = $('#password').val();
        var passwordlegth = passwordcheck.length;
        if (emailcheck == '') {
        	$('#logininfo').html('<span class="icon_index_wrong"></span>邮箱不能为空');
        } else if (passwordcheck == '') {
            $('#logininfo').html('<span class="icon_index_wrong"></span>密码不能为空');
        } else if (passwordlegth < 6) {
            $('#logininfo').html('<span class="icon_index_wrong"></span>密码最短不能少于6个字符');
        } else {
            $('#passwordinfo').html('<span class="icon_right"></span>');
            $("form").submit();
        }
    });

    /**
     * enter keydown submit
     */
    $(window).keydown(function(e){
    	if(e.which == 13) {
    		$('#login_submit').click();
    		document.body.focus();
    	}
    })

    /**
     * change login status
     */
    $('#login_status').click(function(){
    	var login_status_value = $(this).attr('value');
    	if (login_status_value == 1) {
    		$(this).html('<span class="login_status_hidden"></span> 潜水登录');
    		$(this).attr({ value : '2'});
    		$('#login_status_input').attr({ value : '2'});
    	} else {
    		$(this).html('<span class="login_status_online"></span> 正常登录');
    		$(this).attr({ value : '1'});
    		$('#login_status_input').attr({ value : '1'});
    	}
    });
});

