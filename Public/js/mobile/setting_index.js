$().ready(function(){
    var globalPattern = /[&$%\/\\\*<>\'\`]/;
    var $infoLoading = $('<img/>').attr({'src': baseUrl + 'Public/image/common/progressbar.gif', 'title': '加载中...请稍等'});
    var nicknameOk = "yes";
    var introductionOk = "yes";
    $("#nickname").blur(function(){
        var nickname = $("#nickname").attr("value");
        var nicknameLength = nickname.length;
        if (nickname == '') {
            $("#nicknameinfo").html('<span class="red f12"><span class="icon_wrong"></span> 昵称不能为空</span>');
            nicknameOk = "no";
        } else if (globalPattern.test(nickname)){
            $("#nicknameinfo").html('<span class="red f12"><span class="icon_wrong"></span> 不能包含& $ % \/ \\ \* < > \' `等特殊字符</span>');
            nicknameOk = "no";
        } else if (nicknameLength < 2) {
            $("#nicknameinfo").html('<span class="red f12"><span class="icon_wrong"></span> 昵称太短了哦</span>');
            nicknameOk = "no";
        } else if (nicknameLength > 20) {
            $("#nicknameinfo").html('<span class="red f12"><span class="icon_wrong"></span> 昵称太长了哦</span>');
            nicknameOk = "no";
        } else {
            $("#nicknameinfo").html('<span class="icon_right"></span>');
            nicknameOk = "yes";
        }
    });
    $("#introduction").keyup(function(){
        var introduction = $("#introduction").val();
        var introductionLength = introduction.length;
        var letterLeft = 140 - introductionLength;
        if (introductionLength > 140) {
            $("#introductioninfo").html("<span class='red'> 太长了饿</span>");
            introductionOk = "no";
        } else {
            $("#introductioninfo").html("<span class='icon_write'></span>还可以写" + letterLeft + "个字");
            introductionOk = "yes";
        }
    });
    $("#academy").click(function(){
        var academy = $("#academy").val();
        alert(academy);
        $('#ajaxprogressbar').html($infoLoading);
        $.ajax({
            type: "POST",
            url: baseUrl+"setting/ajax",
            data: "selectAcademy=" + academy,
            datatype: "text",
            success:function(specialty){
                $("#specialty").replaceWith(specialty);
                $('#ajaxprogressbar').html('');
            }
        });
    });
    $("#set_submit").click(function(){
        if (nicknameOk == "yes" && introductionOk == "yes") {
        	$('#ajaxprogressbar').html($infoLoading);
            $.post(baseUrl + "setting/index", $("#settingform").serialize(), function(data){
                if (data.status == "yes") {
                    $("#ajaxprogressbar").html("<p id='infoupdateok'><span class='icon_right'></span> 更新成功</p>");
                    $("#dormitoryinfo").empty();
                    $("#infoupdateok").slideDown('normal').delay(1000);
                    $("#infoupdateok").fadeOut('slow');
                } else {
                	$("#ajaxprogressbar").html("<p id='infoupdateok'><span class='icon_wrong'></span> " + data.info + "</p>");
                    $("#dormitoryinfo").empty();
                    $("#infoupdateok").slideDown('normal').delay(1000);
                    $("#infoupdateok").fadeOut('slow');
                }
            }, "json");
        }
    });
    $("#selectschool").click(function(){
        $('#ajaxprogressbar').html($infoLoading);
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
    $("#setting_school_close_span").click(function(){
        $(this).parent().fadeOut('fast');
    });
});