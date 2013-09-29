$().ready(function(){
    
	/**
	 * show next previous image
	 */
	$('#privious_image').live('click', function(){
    	var thisimageid = $('.album_image_content_p').attr('thisimageid');
    	var thisuserid = $('.album_image_content_p').attr('thisuserid');
    	$.ajax({
    		type: "POST",
    		url: baseUrl + "wo/album",
    		data:{changeway: 'privious', thisimageid: thisimageid, thisuserid: thisuserid},
    		dataType: "json",
    		success:function(msg){
    			if (msg.status == 'ok') {
    				$('.album_image_content_p').attr({'thisimageid':msg.data.id});
    				$('.album_image_content_img').attr({'src':msg.data.url});
    				$('#this_image_upload_url').attr({'href':msg.data.url});
    				$('#this_image_upload_time').html(msg.data.time);
    				$('#this_image_upload_size').html(msg.data.size);
    				$('#this_image_upload_hit').html(msg.data.hit);
    				if (msg.data.empty == 'true') {
    					ajaxInfo('已经是第一张了');
    					$('#privious_image').addClass('gray').html('已经是第一张了');
    				}
    			} else {
    				ajaxInfo(msg.info);
    			}
            }
        });
    });
	
    /**
     * delete image
     */
    $('#delete_image').live('click', function(){
    	var deleteid = $(this).attr('imageid');
    	var infohtml = "<p>确定删除？</p> <a class='btn_sure' id='delete_image_btn' value='"+deleteid+"'>确定</a><a class='btn_cancel'>取消</a>";
    	ajaxInfo(infohtml);
    });
    $('#delete_image_btn').live('click', function(){
    	var delImageid = $(this).attr("value");
    	var $infoLoading = $('<img/>').attr({'src': baseUrl + 'Public/image/common/ajax_wait.gif', 'title': '删除中...请稍等'});
    	$('#delete_image_btn').html($infoLoading);
    	$.ajax({
    		type: "POST",
    		url: baseUrl + "wo/album",
    		data:{imageid: delImageid},
    		dataType: "json",
    		success:function(msg){
    			if (msg.status == 'ok') {
    				$("#ajax_info_div").fadeOut("fast");
    				$("#ajax_info_div_outer").hide();
     				$('#i_shine_hit_in').fadeIn('normal').html(msg.info).delay(800).fadeOut('normal');
     				setTimeout('pageToWoAlbum()',3000);
     			} else if (msg.status == 'existsay') {
    				if (msg.data.say_type == '0') {
    					var $htmlIn = "<div><span class='icon_pump'></span><a href='"+baseUrl+"item/say/"+msg.data.sid+"' target='_blank' class='delete_more_info'>详情</a></div>";
    				} else if (msg.data.say_type == '1') {
    					var $htmlIn = "<div><span class='icon_pump'></span><a href='"+baseUrl+"item/help/"+msg.data.sid+"' target='_blank' class='delete_more_info'>详情</a></div>";
    				}
    				ajaxInfo(msg.info + $htmlIn);
     			} else if (msg.status == 'existcomment') {
     				var $htmlIn = "<div><span class='icon_pump'></span><a href='"+baseUrl+"item/say/"+msg.data.sid+"' target='_blank' class='delete_more_info'>详情</a></div>";
     				ajaxInfo(msg.info + $htmlIn);
     			} else if (msg.status == 'existhelpreply') {
    				var $htmlIn = "<div><span class='icon_pump'></span><a href='"+baseUrl+"item/help/"+msg.data.sid+"' target='_blank' class='delete_more_info'>详情</a></div>";
     				ajaxInfo(msg.info + $htmlIn);
     			} else if (msg.status == 'existcommodity') {
    				var $htmlIn = "<div><span class='icon_pump'></span><a href='"+baseUrl+"mall/item/"+msg.data.cid+"' target='_blank' class='delete_more_info'>详情</a></div>";
     				ajaxInfo(msg.info + $htmlIn);
    			} else {
    				ajaxInfo(msg.info);
    			}
    			$('#delete_image_btn').html('确定');
            }
        });
    });
    $('.delete_more_info').live('click', function(){
    	$("#ajax_info_div").fadeOut("fast");
		$("#ajax_info_div_outer").hide();
    });
    $('.btn_cancel').live('click', function(){
    	$("#ajax_info_div").fadeOut("fast");
		$("#ajax_info_div_outer").hide();
    });
    
});
function pageToWoAlbum(){
    window.location = baseUrl + 'wo/album';
}