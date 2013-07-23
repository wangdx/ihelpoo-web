$().ready(function(){
    var globalPattern = /[&$%\/\\\*<>\'\`]/;
    var $infoLoading = $('<img/>').attr({'src': baseUrl + 'Public/image/common/progressbar.gif', 'title': '加载中...请稍等'});
    var nicknameOk = "yes";
    var introductionOk = "yes";
    $("#nickname").blur(function(){
        var nickname = $("#nickname").attr("value");
        var nicknameLength = getStringLength(nickname);
        if (nickname == '') {
            $("#nicknameinfo").html('<span class="red f12"><span class="icon_wrong"></span> 昵称不能为空</span>');
            nicknameOk = "no";
        } else if (globalPattern.test(nickname)){
            $("#nicknameinfo").html('<span class="red f12"><span class="icon_wrong"></span> 不能包含& $ % \/ \\ \* < > \' `等特殊字符</span>');
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
        var introductionLength = getStringLength(introduction);
        var letterLeft = 140 - introductionLength;
        if (introductionLength > 140) {
            $("#introductioninfo").html("<span class='red'> 太长了饿</span>");
            introductionOk = "no";
        } else {
            $("#introductioninfo").html("<span class='icon_write'></span>还可以写" + letterLeft + "个字");
            introductionOk = "yes";
        }
    });
    $("#school").click(function(){
        var school = $("#school").attr("value");
        $(this).ajaxStart(function(){
        	$('#ajaxprogressbar').html($infoLoading);
        }).ajaxStop(function(){
        	$infoLoading.remove();
        });
        $.ajax({
            type: "POST",
            url: baseUrl+"setting/ajax",
            data: "selectSchool=" + school,
            datatype: "text",
            success:function(academy){
                $("#academy").replaceWith(academy);
            }
        });
    });
    $("#academy").live("click", function(){
        var academy = $("#academy").attr("value");
        $(this).ajaxStart(function(){
        	$('#ajaxprogressbar').html($infoLoading);
        }).ajaxStop(function(){
        	$infoLoading.remove();
        });
        $.ajax({
            type: "POST",
            url: baseUrl+"setting/ajax",
            data: "selectAcademy=" + academy,
            datatype: "text",
            success:function(specialty){
                $("#specialty").replaceWith(specialty);
            }
        });
    });
    $("#dormitorytype").click(function(){
        var dormitorytype = $("#dormitorytype").attr("value");
        $(this).ajaxStart(function(){
        	$('#ajaxprogressbar').html($infoLoading);
        }).ajaxStop(function(){
        	$infoLoading.remove();
        });
        $.ajax({
            type: "POST",
            url: baseUrl+"setting/ajax",
            data: "selectDormitory=" + dormitorytype,
            datatype: "text",
            success:function(dormitory){
                $("#dormitory").replaceWith(dormitory);
            }
        });
    });
    $("#month").click(function(){
        var month = $("#month").attr("value");
        var year = $("#year").attr("value");
        $.ajax({
            type: "POST",
            url: baseUrl + "setting/ajax",
            data: "monthAjax=" + month + "&yearAjax=" + year,
            datatype: "text",
            success:function(list){
                $("#day").replaceWith(list);
            }
        })
    });
    $("#year").click(function(){
        var month = $("#month").attr("value");
        var year = $("#year").attr("value");
        $.ajax({
            type: "POST",
            url: baseUrl + "setting/ajax",
            data: "monthAjax=" + month + "&yearAjax=" + year,
            datatype: "text",
            success:function(list){
                $("#day").replaceWith(list);
            }
        })
    });
    $("#province").click(function(){
        var province = $("#province").attr("value");
        $(this).ajaxStart(function(){
        	$('#ajaxprogressbar').html($infoLoading);
        }).ajaxStop(function(){
        	$infoLoading.remove();
        });
        $.ajax({
            type: "POST",
            url: baseUrl + "setting/ajax",
            data: "provinceAjax=" + province,
            datatype: "text",
            success:function(list){
                $("#city").replaceWith(list);
            }
        });
    });
    $("#set_submit").click(function(){
        $(this).ajaxStart(function(){
        	$('#ajaxprogressbar').html($infoLoading);
        }).ajaxStop(function(){
        	$infoLoading.remove();
        });
        if (nicknameOk == "yes" && introductionOk == "yes") {
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
});