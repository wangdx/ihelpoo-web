$().ready(function(){
    /**
     * enlarge image
     */
    $('.s_li_p_content_image img').live('click', function(){
    	var totalImageNums = $(this).parent().find("img").size();
    	if (totalImageNums > 1) {
    		
    		/**
    		 * more than one image , should list next page
    		 */
    		var enlargeSwitch = $(this).attr('enlargeswitch');
	    	if (enlargeSwitch != 'on') {
		    	var imageurl = $(this).attr('src');
		    	if (imageurl.match("ihelpoo-public") != '') {
		    		var reg = new RegExp("thumb_","g");
		    		var imageurllarge = imageurl.replace(reg,"");
		    	}
		    	imageTempContent = $(this).parent().html();
		    	$(this).parent().html('<p class="f12 s_li_p_content_image_title"><a href="'+imageurllarge+'" target="_blank"><span class="icon_plus"></span>查看原图</a> <a class="s_li_p_content_image_title_up"><span class="icon_up"></span>收起</a></p><img src="'+imageurllarge+'" width="395" enlargeswitch="on" title="点击缩小" /></p>');
	    	} else {
	    		$(this).parent().html(imageTempContent);
	    	}
    	} else {
	    	var enlargeSwitch = $(this).attr('enlargeswitch');
	    	if (enlargeSwitch != 'on') {
		    	var imageurl = $(this).attr('src');
		    	if (imageurl.match("ihelpoo-public") != '') {
		    		var reg = new RegExp("thumb_","g");
		    		var imageurllarge = imageurl.replace(reg,"");
		    	}
		    	imageTempContent = $(this).parent().html();
		    	$(this).parent().html('<p class="f12 s_li_p_content_image_title"><a href="'+imageurllarge+'" target="_blank"><span class="icon_plus"></span>查看原图</a> <a class="s_li_p_content_image_title_up"><span class="icon_up"></span>收起</a></p><img src="'+imageurllarge+'" width="395" enlargeswitch="on" title="点击缩小" /></p>');
	    	} else {
	    		$(this).parent().html(imageTempContent);
	    	}
    	}
    });
    
    $('.s_li_p_content_image_title_up').live('click', function(){
	    $(this).parent().parent().html(imageTempContent);
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
                  $region.html('('+msg.data+')');
            }
        });
    });
    
    var t_plus;
    $(".plus_button").mouseenter(function(e){
    	$this = $(this);
    	t=setTimeout(function(){
    		var sidString = $this.attr('value');
    		var positionleft = e.pageX + 10;
        	var positiontop = e.pageY + 10;
    		$.ajax({
                type: "POST",
        		dataType: "html",
        		url: baseUrl + "stream/plusView",
        		data:{sidString: sidString},
        		success:function(data){
                	$('.record_plus_div').css({ position: "absolute", left: positionleft, top: positiontop }).fadeIn('fast').html(data);
                }
            });
    	},1000);
    }).mouseleave(function(){
    	clearTimeout(t_plus);
    	$('.record_plus_div').hover(function(){},
    	function(){
    		$(this).fadeOut("fast");
    	});
    });

});