$().ready(function(){
    var $infoLoading = $('<img/>').attr({'src': baseUrl + 'Public/image/common/progressbar.gif', 'title': '加载中...请稍等'});
    $("#fillaccountsubmit").click(function(){
        var fillaccountemail = $("#fillaccountemail").val();
        var password = $("#password").val();
        var passwordrepeat = $("#passwordrepeat").val();
        var passwordLength = getStringLength(password);
        $(this).ajaxStart(function(){
    	    $('#ajaxprogressbar').html($infoLoading);
        }).ajaxStop(function(){
    	    $infoLoading.remove();
        });
        if (fillaccountemail == "") {
            showWrongInfo('邮箱不能为空');
        } else if (passwordLength < 6) {
            showWrongInfo('密码不能少于6位');
        } else if (password == "") {
            showWrongInfo('新密码始密码不能为空');
        } else if (password != passwordrepeat) {
            showWrongInfo('两次密码不一致');
        } else {
            $.post(baseUrl + "support/fillaccount", $("#fillaccountform").serialize(), function(data){
                if (data.status == "yes") {
                    $("#ajaxprogressbar").html("<p id='infopsupdateok'><span class='icon_right'></span> 完善账号成功，关闭此页面，登录去吧...</p>");
                    $("#infopsupdateok").slideDown('normal').delay(1500);
                } else if (data.status == "wrong") {
                    showWrongInfo(data.info);
                }
            }, "json");
        }
    });
});
function showWrongInfo(info) {
	$("#ajaxprogressbar").html("<p id='infopsupdateok'><span class='icon_wrong'></span> " + info + "</p>");
	$("#infopsupdateok").slideDown('normal').delay(1500).slideUp('fast');
}