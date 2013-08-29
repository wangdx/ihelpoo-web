$().ready(function(){
	$("#province").click(function(){
        var province = $("#province").attr("value");
        $.ajax({
            type: "POST",
            url: baseUrl + "rooter/schoolinfo",
            data: "provinceAjax=" + province,
            datatype: "text",
            success:function(list){
                $("#city").replaceWith(list);
            }
        });
    });
	$("#city").live('click', function(){
        var city = $("#city").attr("value");
        $(this).after(city + ' - ');
    });
});