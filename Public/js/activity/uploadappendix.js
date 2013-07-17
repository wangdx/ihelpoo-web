$().ready(function(){
    var $infoImgUploading = $('<img/>').attr({'src': baseUrl + 'Public/image/common/progressbar.gif', 'title': '上传中...请稍等'});

	/**
     * click submit
     */
    $("#appendix_submit").click(function(){
        var upload_image_file = $('#upload_appendix_file').val();
        var activity_id = $('#activity_id').val();
        if (upload_image_file == '') {
            $('#appendix_submit_info').fadeIn('fast').html("<span class='f12 red_l'>还没有选择文件呢</span>").delay(1000).fadeOut('fast');
        } else {
        	$(this).ajaxStart(function(){
            	$('#appendix_submit_info').fadeIn('fast').html($infoImgUploading);
            }).ajaxComplete(function(){
            	$infoImgUploading.remove();
            	$('#appendix_submit_info').fadeOut();
            });
        	$.ajaxFileUpload({
        		url: baseUrl + 'activity/ajaxuploadappendix',
            	secureuri: false,
            	fileElementId: 'upload_appendix_file',
            	dataType: 'json',
            	success: function (msg){
            	    if (msg.status == 'uploaded') {
            	    	$("#appendix_submit_info").fadeIn('fast').html("<span class='icon_right'></span> 处理中...");
            	    	$.ajax({
            	    	    type: "POST",
            	    	    dataType: "json",
            	    	    url: baseUrl + "activity/ajaxuploadappendix",
            	    	    data:{activityid: activity_id, appendixurl: msg.data},
            	    	    success:function(msgin){
            	    	    	if (msgin.status == 'ok') {
            	    	    		$("#appendix_submit_info").fadeIn('fast').html("<span class='icon_right'></span> 上传成功");
            	    	    		window.location = baseUrl + 'activity/uploadappendix/' + activity_id;
            	    	    	} else {
            	    	    		$('#appendix_submit_info').fadeIn('fast').html("<span class='icon_attention'></span>出错啦");
            	    	    	}
            	    	    }
            	    	});
            	    } else if (msg.status == 'error') {
            	        $('#appendix_submit_info').fadeIn('fast').html("<span class='f12 red_l'>" + msg.data + "</span>").delay(1000).fadeOut('fast');
            	        alert(msg.info);
            	    }
            	}
            });
        }
    });
});
