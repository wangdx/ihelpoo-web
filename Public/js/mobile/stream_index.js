$().ready(function(){
	
	/**
	 * choose view type
	 */
	
	$('#stream_index_view_select').click(function(){
        $('.stream_list_type_out_div').slideDown('fast');
    });
	
	/**
     * plus part
     */
    $('.plus_button').click(function(){
        var $thisButton = $(this);
        var $region = $('#plus_view_region_'+$(this).attr('value'));
        $.ajax({
            type: "POST",
            url: baseUrl+"stream/plusToggle",
            data: {'plusSid':$(this).attr('value')},
            dataType: "json",
            success:function(msg){
            	if (msg.status != 'error') {
            		$region.html('('+msg.data+')');
            	}
            }
        });
    });
	
	/**
     * enlarge image
     */
    var imageTempContent = '';
    $('.s_li_p_content_image img').live('click', function(){
    	/**
    	 * var totalImageNums = $(this).parent().find("img").size();
    	 * totalImageNums > 1
    	 * more than one image , should list next page
    	 */
    	var $slipContentImagePart = $(this).parent();
    	var imagelodingmarginheight = $slipContentImagePart.height();
    	var enlargeSwitch = $(this).attr('enlargeswitch');
    	if (enlargeSwitch != 'on') {
	    	var imageurl = $(this).attr('src');
	    	if (imageurl.match("ihelpoo-public") != '') {
	    		var reg = new RegExp("thumb_","g");
	    		var imageurllarge = imageurl.replace(reg,"");
	    	}
	    	var imageTempContent = $(this).parent().html();
	    	$(this).parent().val(imageTempContent);
	    	var $imageobjectpart = $(this).parent().html('<p class="f12 s_li_p_content_image_title"><a href="'+imageurllarge+'" target="_blank"><span class="icon_plus"></span>查看原图</a> <a class="s_li_p_content_image_title_up"><span class="icon_up"></span>收起</a></p><img class="enlargeimg" src="'+baseUrl+'Public/image/common/ajax_wait_login.gif" enlargeswitch="on" title="点击缩小" />');
	    	var $imageobject = $imageobjectpart.find('.enlargeimg');
	    	$slipContentImagePart.attr({'style': 'min-height:'+imagelodingmarginheight+'px'});
	    	$imageobject.attr({'src':imageurllarge});
	    	$imageobject.ready(function(){
	    		$imageobject.attr({'src':imageurllarge});
			});
    	} else {
    		var imageTempContentBack = $(this).parent().val();
    		$(this).parent().html(imageTempContentBack);
    	}
    });
    $('.s_li_p_content_image_title_up').live('click', function(){
    	var imageTempContentBack = $(this).parent().parent().val();
		$(this).parent().parent().html(imageTempContentBack);
	});

    /**
     * video play
     */
    $('.s_li_p_content_mv_img_p').click(function(){
    	$(this).hide();
    	$(this).parent().find('.s_li_p_content_mv_object_p').slideDown('fast');
    });

    $('.s_li_p_content_mv_object_up').click(function(){
    	$(this).parent().hide();
    	$('.s_li_p_content_mv_img_p').show();
    });
});