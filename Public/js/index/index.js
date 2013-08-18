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
	$('#lay_bg_img').css({ width: bgwidth, height: bgheight}).fadeOut().fadeIn('fast');
	$('.texture_background').css({ width: bgwidth, height: bgheight});
	$(window).resize(function(){
		loadBackground();
	});
	
    var $infoLoading = $('<img/>').attr({'src': baseUrl + 'Public/image/common/ajax_wait_login.gif', 'title': '检测中...请稍等'});
    $('#email').blur(function(){
        var emailcheck = $('#email').val();
        $('#logininfo').html($infoLoading);
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
                        $('#logininfo').fadeIn().html('× 用户不存在');
                    } else if ('exist' == msg.status) {
                        $('#logininfo').fadeOut();
                    } else if ('wrong' == msg.status) {
                        $('#logininfo').fadeIn().html('× ' + msg.info);
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
    	if(e.which == 13) {
    		$('#submit').click();
    		document.body.focus();
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
	$('#lay_bg_img').css({ width: bgwidth, height: bgheight});
	$('.texture_background').css({ width: bgwidth, height: bgheight});
}