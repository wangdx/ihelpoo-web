$().ready(function(){
    var $infoLoading = $('<img/>').attr({'src': baseUrl + 'Public/image/common/progressbar.gif', 'title': '加载中...请稍等'});
    $("#pssubmit").click(function(){
        var groupName = $("#group_name").val();
        var groupDesc = $("#group_desc").val();
        $(this).ajaxStart(function(){
    	    $('#ajaxprogressbar').html($infoLoading);
        }).ajaxStop(function(){
    	    $infoLoading.remove();
        });
        if (groupName == "") {
            showWrongInfo('请输入分组名');
        } else {
            $.post(baseUrl + "setting/group", $("#psform").serialize(), function(data){
                if (data.status == "yes") {
                    $("#ajaxprogressbar").html("<p id='infopsupdateok'><span class='icon_right'></span> 创建分组成功</p>");
                    $("#infopsupdateok").slideDown('normal').delay(1000);
                    $("#infopsupdateok").fadeOut('slow');
                } else if (data.status == "wrong") {
                    showWrongInfo(data.info);
                }
                setTimeout(function(){location.reload();},2000);
            }, "json");
        }
    });



    $(".groupSubmit").click(function(){
        var id = $(this).attr('value');
        var values = "";
        $.each($("input[name='groups']:checked"), function() {
            values += $(this).val()+',';
        });

        $.post(baseUrl + "setting/groupme", { "id":id, "gids":values },
            function(data){
                if (data.status == "yes") {
                    $("#ajaxprogressbar").html("<p id='infopsupdateok'><span class='icon_right'></span> 分组成功</p>");
                    $("#infopsupdateok").slideDown('normal').delay(1000);
                    $("#infopsupdateok").fadeOut('slow');
                } else if (data.status == "wrong") {
                    showWrongInfo(data.info);
                }
            }, "json");

    });


    $("#groupDelete").click(function(){
          if(confirm("确定？")){

              $.post(baseUrl + "setting/groupDelete", $("#groupUpdateForm").serialize(), function(data){
                  if (data.status == "yes") {
                      $("#ajaxprogressbar").html("<p id='infopsupdateok'><span class='icon_right'></span> 删除分组成功</p>");
                      $("#infopsupdateok").slideDown('normal').delay(1000);
                      $("#infopsupdateok").fadeOut('slow');
                  } else if (data.status == "wrong") {
                      showWrongInfo(data.info);
                  }
                  setTimeout(function(){location.reload();},2000);
              }, "json");
          }

    });
    $("#groupUpdate").click(function(){
        var groupName = $("#group_name").val();
        var groupDesc = $("#group_desc").val();
        $(this).ajaxStart(function(){
            $('#ajaxprogressbar').html($infoLoading);
        }).ajaxStop(function(){
                $infoLoading.remove();
            });
        if (groupName == "") {
            showWrongInfo('请输入分组名');
        } else {
            $.post(baseUrl + "setting/groupUpdate", $("#groupUpdateForm").serialize(), function(data){
                if (data.status == "yes") {
                    $("#ajaxprogressbar").html("<p id='infopsupdateok'><span class='icon_right'></span> 更新分组成功</p>");
                    $("#infopsupdateok").slideDown('normal').delay(1000);
                    $("#infopsupdateok").fadeOut('slow');
                } else if (data.status == "wrong") {
                    showWrongInfo(data.info);
                }
                setTimeout(function(){location.reload();},2000);
            }, "json");
        }
    });


});
function showWrongInfo(info) {
	$("#ajaxprogressbar").html("<p id='infopsupdateok'><span class='icon_wrong'></span> " + info + "</p>");
	$("#infopsupdateok").slideDown('normal').delay(1000).slideUp('fast');
}