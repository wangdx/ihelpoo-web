$().ready(function(){
	
	/**
     * delete image
     */
    $('#delete_image').live('click', function(){
    	var deleteid = $(this).attr('imageid');
    	var infohtml = "确定删除？";
    	ajaxInfo(infohtml, 'album', deleteid);
    });
    $('#delete_btn_yes').live('click', function(){
    	var deleteId = $(this).attr("value");
    	$.mobile.showPageLoadingMsg();
    	$.ajax({
    		type: "POST",
    		url: baseUrl + "wo/album",
    		data:{imageid: deleteId},
    		dataType: "json",
    		success:function(msg){
    			if (msg.status == 'ok') {
    				$("#ajax_info_div").fadeOut("fast");
    				$("#ajax_info_div_outer").fadeOut("fast");
     				$('#i_shine_hit_in').fadeIn('normal').html(msg.info).delay(1500).fadeOut('normal');
     				setTimeout('pageToWoAlbum()',3000);
     			} else if (msg.status == 'existsay') {
    				if (msg.data.say_type == '0') {
    					var $htmlIn = "<div><span class='icon_pump'></span><a href='"+baseUrl+"item/say/"+msg.data.sid+"' target='_self' class='delete_more_info'>详情</a></div>";
    				} else if (msg.data.say_type == '1') {
    					var $htmlIn = "<div><span class='icon_pump'></span><a href='"+baseUrl+"item/help/"+msg.data.sid+"' target='_self' class='delete_more_info'>详情</a></div>";
    				}
    				ajaxInfo(msg.info + $htmlIn, 0, 0);
     			} else if (msg.status == 'existcomment') {
     				var $htmlIn = "<div><span class='icon_pump'></span><a href='"+baseUrl+"item/say/"+msg.data.sid+"' target='_self' class='delete_more_info'>详情</a></div>";
     				ajaxInfo(msg.info + $htmlIn, 0, 0);
     			} else if (msg.status == 'existhelpreply') {
    				var $htmlIn = "<div><span class='icon_pump'></span><a href='"+baseUrl+"item/help/"+msg.data.sid+"' target='_self' class='delete_more_info'>详情</a></div>";
     				ajaxInfo(msg.info + $htmlIn, 0, 0);
     			} else if (msg.status == 'existcommodity') {
    				var $htmlIn = "<div><span class='icon_pump'></span><a href='"+baseUrl+"mall/item/"+msg.data.cid+"' target='_self' class='delete_more_info'>详情</a></div>";
     				ajaxInfo(msg.info + $htmlIn, 0, 0);
    			} else {
    				ajaxInfo(msg.info, 0, 0);
    			}
    			$.mobile.hidePageLoadingMsg();
            }
        });
    });
    $('.delete_more_info').live('click', function(){
    	$("#ajax_info_div").fadeOut("fast");
		$("#ajax_info_div_outer").hide();
    });
});

function pageToWoAlbum(){
    window.location = baseUrl + 'wo/album';
}