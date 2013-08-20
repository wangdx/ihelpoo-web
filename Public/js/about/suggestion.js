$().ready(function(){
    var $infoLoading = $('<img/>').attr({'src': baseUrl + 'Public/image/common/ajax_wait.gif', 'title': '提交中...请稍等'});
    $("#submit").click(function(){
        var connection = $("#connection").val();
        var content = $("#content").val();
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
                } else if (data.status == "wrong") {
                	$("#submit_info").html(data.info);
                }
            }, "json");
        }
    });
});
