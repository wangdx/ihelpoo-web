$().ready(function(){
    var $infoLoading = $('<img/>').attr({'src': baseUrl + 'Public/image/common/ajax_wait.gif', 'title': '提交中...请稍等'});
    $("#submit").click(function(){
        var question1 = $("#question1").val();
        var question2 = $("#question2").val();
        var question3 = $("#question3").val();
        var question4 = $("#question4").val();
        var question5 = $("#question5").val();
        var question6 = $("#question6").val();
        var question7 = $("#question7").val();
        var question8 = $("#question8").val();
        var question9 = $("#question9").val();
        var question10 = $("#question10").val();
    	 $("#submit_info").html($infoLoading);
        if (question1 == "" || question2 == "" || question3 == "" || question4 == "" || question5 == "" || question6 == "" || question7 == "" || question8 == "" || question9 == "" || question10 == "") {
        	$("#submit_info").html('10个问题都必填，请详细的回答每个问题:)');
        } else {
            $.post(baseUrl + "about/snsapply", $("#applyverifyform").serialize(), function(data){
                if (data.status == "yes") {
                    $("#submit_info").html("<span class='icon_right'></span> 提交成功");
                    window.location = baseUrl + 'about/snsapply?succ=ok';
                } else if (data.status == "wrong") {
                	$("#submit_info").html(data.info);
                }
            }, "json");
        }
    });
});
