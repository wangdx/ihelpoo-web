$().ready(function(){
    /**
     * enlarge image
     */
    var imageTempContent = '';
    $('.s_li_p_content_image img').live('click', function(){
    	var enlargeSwitch = $(this).attr('enlargeswitch');
    	if (enlargeSwitch != 'on') {
	    	var imageurl = $(this).attr('src');
	    	if (imageurl.match("ihelpoo-public") != '') {
	    		var reg = new RegExp("thumb_","g");
	    		var imageurllarge = imageurl.replace(reg,"");
	    	}
	    	imageTempContent = $(this).parent().html();
	    	$(this).parent().html('<p class="f12 s_li_p_content_image_title"><a href="'+imageurllarge+'" target="_blank"><span class="icon_plus"></span>查看原图</a> <a class="s_li_p_content_image_title_up"><span class="icon_up"></span>收起</a></p><img src="'+imageurllarge+'" class="s_li_p_content_image_p_img" enlargeswitch="on"/></p>');
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

});