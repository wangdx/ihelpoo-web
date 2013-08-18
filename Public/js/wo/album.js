$().ready(function(){
    $('.privious_photo_part').hover(function(){
    	$('.privious_img').fadeIn('fast');
    }, function(){
    	$('.privious_img').fadeOut('fast');
    });
    $('.next_photo_part').hover(function(){
    	$('.next_img').fadeIn('fast');
    }, function(){
    	$('.next_img').fadeOut('fast');
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
    	$('#del_image_btn_yes').html($infoLoading);
    	$.ajax({
    		type: "POST",
    		url: baseUrl + "wo/album",
    		data:{imageid: delImageid},
    		dataType: "json",
    		success:function(msg){
    			 if (msg.status == 'ok') {
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
    			$('#del_image_btn_yes').html('确定');
            }
        });
    });
    $('.delete_more_info').live('click', function(){
    	$("#i_shine_hit_in").fadeOut('fast');
    });
    $('.btn_cancel').live('click', function(){
    	$("#ajax_info_div").fadeOut("fast");
		$("#ajax_info_div_outer").hide();
    });
    
});
function pageToWoAlbum(){
    window.location = baseUrl + 'wo/album';
}