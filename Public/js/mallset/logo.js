$().ready(function(){
    var $infoImgUploading = $('<img/>').attr({'src': baseUrl + 'Public/image/common/progressbar.gif', 'title': '上传中...请稍等'});

	/**
     * click submit
     */
    $("#logo_submit").click(function(){
        var upload_image_file = $('#upload_logo_file').val();
        if (upload_image_file == '') {
            $('#logo_submit_info').fadeIn('fast').html("<span class='f12 red_l'>还没有选择图片呢</span>").delay(1000).fadeOut('fast');
        } else {
        	$(this).ajaxStart(function(){
            	$('#logo_submit_info').fadeIn('fast').html($infoImgUploading);
            }).ajaxComplete(function(){
            	$infoImgUploading.remove();
            	$('#logo_submit_info').empty();
            });
        	$.ajaxFileUpload({
        		url: baseUrl + 'mallset/logo',
            	secureuri: false,
            	fileElementId: 'upload_logo_file',
            	dataType: 'json',
            	success: function (msg){
            	    if (msg.status == 'uploaded') {
            	    	$("#logo_submit_info").fadeIn('fast').html("<span class='icon_right'></span> 修改成功");
            	    	window.location = baseUrl + 'mallset/logo';
            	    } else if (msg.status == 'error') {
            	        $('#logo_submit_info').fadeIn('fast').html("<span class='f12 red_l'>" + msg.data + "</span>").delay(1000).fadeOut('fast');
            	        alert(msg.info);
            	    }
            	}
            });
        }
    });
});
