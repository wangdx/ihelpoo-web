$().ready(function(){
    var $infoLoading = $('<img/>').attr({'src': baseUrl + 'Public/image/common/ajax_wait.gif', 'title': '提交中...请稍等'});
    $("#submit").click(function(){
        var connection = $("#connection").val();
        var content = $("#content").val();
        var verificationcode = $("#verification_code").val();
    	 $("#submit_info").html($infoLoading);
    	 if (connection == "") {
         	$("#submit_info").html('联系方式不能为空哦');
         } else if (content == "") {
         	$("#submit_info").html('意见建议还没填呢');
         } else {
            $.post(baseUrl + "about/suggestion", $("#applyverifyform").serialize(), function(data){
                if (data.status == "yes") {
                    $("#submit_info").html("<span class='icon_right'></span> 提交成功");
                    window.location = baseUrl + 'about/suggestion?succ=ok';
                } else if (data.status == "error") {
                	$("#submit_info").html("<span class='icon_attention'></span>"+data.info);
                } else if (data.status == "verifi") {
                	$("#submit_info").html("请输入验证码");
            		$(".verification_code_p").fadeIn('fast');
            		$("#verification_code_img").attr({'src': baseUrl + 'other/verifi' });
            	}
            }, "json");
        }
    });
});
