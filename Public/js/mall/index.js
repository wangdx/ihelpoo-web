$().ready(function(){
	//图片轮换
    count = $("#banner_list a").length;
    n = count;
    $("#banner_list a:not(:first-child)").hide();
    $("#banner_info").html($("#banner_list a:first-child").find("img").attr('alt'));
    $("#banner_info").click(function(){window.open($("#banner_list a:first-child").attr('href'), "_blank")});
    $("#banner li").click(function() {
    	var i = $(this).text() - 1;//获取Li元素内的值，即1，2，3，4
    	n = i;
    	if (i >= count) return;
    	$("#banner_info").html($("#banner_list a").eq(i).find("img").attr('alt'));
    	$("#banner_info").unbind().click(function(){window.open($("#banner_list a").eq(i).attr('href'), "_blank")
    })
    	$("#banner_list a").filter(":visible").fadeOut(500).parent().children().eq(i).fadeIn(1000);
    	document.getElementById("banner").style.background="";
    	$(this).toggleClass("on");
    	$(this).siblings().removeAttr("class");
    });
    t = setInterval("showAuto()", 3300);
	$("#banner").hover(function(){clearInterval(t)},
		function(){t = setInterval("showAuto()", 3300);
	});
});

//图片轮换
function showAuto(){
	n = n >=(count - 1) ? 0 : ++n;
	$("#banner li").eq(n).trigger('click');
}
