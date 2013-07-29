var firstVisitUrl = location.href;

$().ready(function(){
	
	/**
	 * css style image background
	 */
	loadBackground();
	$(window).resize(function(){
		loadBackground();
	});
	
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
            $('#emailinfo').html('<span class="icon_index_wrong"></span>邮箱不能为空');
        } else {
            $.ajax({
                type: 'POST',
                url: baseUrl + 'user/ajaxcheckemail',
                dataType: 'json',
                data: { email: emailcheck },
                success: function(msg) {
                    if ('ok' == msg.status) {
                        $('#emailinfo').html('<span class="icon_index_wrong"></span>用户不存在');
                    } else if ('exist' == msg.status) {
                        $('#emailinfo').html('<span class="icon_right"></span>');
                        emailok = 'ok';
                    } else if ('movedata' == msg.status) {
                        $('#emailinfo').html('<span class="icon_right"></span>');
                        alert(msg.info);
                        window.location = baseUrl;
                    } else if ('wrong' == msg.status) {
                        $('#emailinfo').html('<span class="icon_index_wrong"></span>' + msg.info);
                    }
                }
            });
        }
    });

    /**
     * click submit
     */
    $('#submit').click(function(){
    	var emailcheck = $('#email').val();
    	var passwordcheck = $('#password').val();
        var passwordlegth = passwordcheck.length;
        if (emailcheck == '') {
        	$('#emailinfo').html('<span class="icon_index_wrong"></span>邮箱不能为空');
        } else if (passwordcheck == '') {
            $('#passwordinfo').html('<span class="icon_index_wrong"></span>密码不能为空');
        } else if (passwordlegth < 6) {
            $('#passwordinfo').html('<span class="icon_index_wrong"></span>密码最短不能少于6个字符');
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
    		$('#submit').click();
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
	$('#lay_bg_img').fadeOut('fast').fadeIn('slow');
	$('#lay_bg_img').css({ width: bgwidth, height: bgheight});
	$('.texture_background').css({ width: bgwidth, height: bgheight});
}