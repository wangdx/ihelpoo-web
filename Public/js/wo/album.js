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
    $('#delete_image').click(function(){
        $("#i_shine_hit_in").fadeIn('fast').html("<span id='del_image_btn_yes'>确定</span> <span id='del_image_btn_no'>取消</span>");
    });
    $('#del_image_btn_no').live('click', function(){
        $("#i_shine_hit_in").slideUp('fast');
    });
    $('#del_image_btn_yes').live('click', function(){
    	var $infoLoading = $('<img/>').attr({'src': baseUrl + 'Public/image/common/ajax_wait.gif', 'title': '提交中...请稍等'});
    	var delImageid = $('#delete_image').attr('imageid');
    	$(this).ajaxStart(function(){
    		$('#del_image_btn_yes').html($infoLoading);
        }).ajaxStop(function(){
    	    $infoLoading.remove();
    	    $('#del_image_btn_yes').html('确定');
        });
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
    				$('#i_shine_hit_in').fadeIn('normal').html(msg.info + $htmlIn);
     			} else if (msg.status == 'existcomment') {
     				var $htmlIn = "<div><span class='icon_pump'></span><a href='"+baseUrl+"item/say/"+msg.data.sid+"' target='_blank' class='delete_more_info'>详情</a></div>";
     				$('#i_shine_hit_in').fadeIn('normal').html(msg.info + $htmlIn);
     			} else if (msg.status == 'existhelpreply') {
    				var $htmlIn = "<div><span class='icon_pump'></span><a href='"+baseUrl+"item/help/"+msg.data.sid+"' target='_blank' class='delete_more_info'>详情</a></div>";
     				$('#i_shine_hit_in').fadeIn('normal').html(msg.info + $htmlIn);
     			} else if (msg.status == 'existcommodity') {
    				var $htmlIn = "<div><span class='icon_pump'></span><a href='"+baseUrl+"mall/item/"+msg.data.cid+"' target='_blank' class='delete_more_info'>详情</a></div>";
     				$('#i_shine_hit_in').fadeIn('normal').html(msg.info + $htmlIn);
    			} else {
    				$('#i_shine_hit_in').fadeIn('normal').html(msg.info).delay(800).fadeOut('normal');
    			}
            }
        });
    });
    $('.delete_more_info').live('click', function(){
    	$("#i_shine_hit_in").fadeOut('fast');
    });
});
function pageToWoAlbum(){
    window.location = baseUrl + 'wo/album';
}