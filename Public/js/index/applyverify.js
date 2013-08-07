$().ready(function(){
    var $infoLoading = $('<img/>').attr({'src': baseUrl + 'Public/image/common/ajax_wait.gif', 'title': '提交中...请稍等'});
    $("#submit").click(function(){
        var applyverifyName = $("#applyverify_name").val();
        var applyverifyMobile = $("#applyverify_mobile").val();
    	$("#submit_info").html($infoLoading);
        if (applyverifyName == "") {
        	$("#submit_info").html('姓名不能为空');
        } else if (applyverifyMobile == "") {
        	$("#submit_info").html('手机不能为空');
        } else {
            $.post(baseUrl + "index/applyverify", $("#applyverifyform").serialize(), function(data){
                if (data.status == "yes") {
                    $("#submit_info").html("<span class='icon_right'></span> 提交成功");
                    window.location = baseUrl + 'index/applyverify?succ=ok';
                } else if (data.status == "wrong") {
                	$("#submit_info").html(data.info);
                }
            }, "json");
        }
    });
});
