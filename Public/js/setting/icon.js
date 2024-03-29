$().ready(function(){
    $('.stream_top_notice_info .icon_index_wrong').click(function(){
        $(this).parent().slideUp('fast');
    });
    var $infoImgUploading = $('<img/>').attr({'src': baseUrl + 'Public/image/common/progressbar.gif', 'title': '上传中...请稍等'});
    var $infoLoading = $('<img/>').attr({'src': baseUrl + 'Public/image/common/loading.gif', 'title': '处理中...请稍等'});
    var urlcheckpattern = /(http:\/\/)?[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*/;
    
    /**
     * image from internet
     */
    $('#image_from_internet').toggle(
        function(){
        	$('.image_from_internet_div').slideDown('fast');
        },
        function(){
        	$('.image_from_internet_div').slideUp('fast');
        }
    );
    
    $('#image_from_internet_submit').live('click', function(){
    	var album_image_url_internet = $('#image_from_internet_input').val();
        if (urlcheckpattern.test(album_image_url_internet) && album_image_url_internet != '0') {
        	$('#usericontarget').attr({'src': album_image_url_internet});
            $('#preview').attr({'src': album_image_url_internet});
            $('#img_temp_path').val(album_image_url_internet);
            $('.image_upload_div').slideUp('slow');
            $('.image_cut_div').slideDown('fast');
            //icon cut
            jQuery('#usericontarget').Jcrop({
                minSize: [200, 150],
                setSelect: [10, 8, 480, 360],
                bgColor: "#000",
                bgOpacity: 0.6,
                onChange: updatePreview,
                onSelect: updateCoords,
                aspectRatio: 4/3
            });
        } else {
        	$('.icon_handle_info').fadeIn('fast').html("<span class='f12 red'>图片地址格式错误</span>").delay(1000).fadeOut('fast');
        }
	});
    
    /**
     * new icon from url
     */
    var album_image_url = $('#image_album').val();
    if (urlcheckpattern.test(album_image_url) && album_image_url != '0') {
    	$('#usericontarget').attr({'src': album_image_url});
        $('#preview').attr({'src': album_image_url});
        $('#img_temp_path').val(album_image_url);
        $('.image_upload_div').slideUp('slow');
        $('.image_cut_div').slideDown('fast');
        //icon cut
        jQuery('#usericontarget').Jcrop({
            minSize: [200, 150],
            setSelect: [10, 8, 480, 360],
            bgColor: "#000",
            bgOpacity: 0.6,
            onChange: updatePreview,
            onSelect: updateCoords,
            aspectRatio: 4/3
        });
    }
    
    $('#file_upload').uploadify({
    	'formData' : {'userloginid': $("#sessionuserloginid").val()},
		'fileObjName'      : 'uploadedimg',
		'fileSizeLimit' : '1500KB',
		'fileTypeDesc' : 'Image Files',
        'fileTypeExts' : '*.gif; *.jpg; *.jpeg; *.png',
		'swf'      : '/Public/js/public/uploadify.swf',
		'uploader' :  baseUrl + '/other/iconupload',
		'buttonText' : '上传图片',
		'width' : '90',
		'height' : '25',
		'queueSizeLimit' : '1',
		'onUploadSuccess' : function(file, data, response) {
			var msg = JSON.parse(data);
			if (msg.status == 'uploaded') {
    	        $('#usericontarget').attr({'src': msg.data});
    	        $('#preview').attr({'src': msg.data});
    	        $('#img_temp_path').val(msg.data);
    	        $('.image_upload_div').slideUp('slow');
    	        $('.image_cut_div').slideDown('fast');
    	        //icon cut
    	        jQuery('#usericontarget').Jcrop({
    	            minSize: [200, 150],
    	            setSelect: [10, 8, 480, 360],
    	            bgColor: "#000",
    	            bgOpacity: 0.6,
    	            onChange: updatePreview,
    	            onSelect: updateCoords,
    	            aspectRatio: 4/3
    	        });
    	        $('.icon_handle_info').html('');
    	        return false;
    	    } else if (msg.status == 'error') {
    	        $('.icon_handle_info').fadeIn('fast').html("<span class='f12 red'>" + msg.info + "</span>").delay(1000).fadeOut('fast');
    	    }
        }
	});
    
    //icon cut submit
    $('#icon_cut_btn').click(function(){
        if (parseInt($('#img_w').val())) {
            var icon_x = $('#img_x').val();
            var icon_y = $('#img_y').val();
            var icon_w = $('#img_w').val();
            var icon_h = $('#img_h').val();
            var img_temp_path = $('#img_temp_path').val();
            $('.icon_save_info').fadeIn('fast').html($infoLoading);
            $.ajax({
                type: "POST",
                url: baseUrl + "setting/icon",
                data: { iconx: icon_x, icony: icon_y, iconw: icon_w, iconh: icon_h , icontemppath: img_temp_path },
                dataType: "json",
                success: function(msg) {
                    if (msg.status == "ok") {
                        $('.image_cut_div').slideUp('slow');
                        $('#img_temp_path').val('');
                        $('.image_new_div').fadeIn('slow');
                        $('.image_new_large').attr({'src': msg.data});
                        $('.image_new_middle').attr({'src': msg.data});
                        $('.image_new_small').attr({'src': msg.data});
                        $('.icon_save_info').html('');
                    }
                }
            });
        } else {
            alert('请先选择裁剪图像区域 :)');
        }
    });
});

//icon cut function
function updateCoords(c){
	$('#img_x').val(c.x);
	$('#img_y').val(c.y);
	$('#img_w').val(c.w);
	$('#img_h').val(c.h);
};
function updatePreview(c){
    if (parseInt(c.w) > 0) {
        var rx = 230 / c.w;
        var ry = 172 / c.h;
        $('#preview').css({
            width: Math.round(rx * $('#usericontarget').width()) + 'px',
            height: Math.round(ry * $('#usericontarget').height()) + 'px',
            marginLeft: '-' + Math.round(rx * c.x) + 'px',
            marginTop: '-' + Math.round(ry * c.y) + 'px'
        });
    }
};

