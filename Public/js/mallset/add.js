$().ready(function(){
    var $infoImgUploading = $('<img/>').attr({'src': baseUrl + 'Public/image/common/progressbar.gif', 'title': '上传中...请稍等'});
    var $infoLoading = $('<img/>').attr({'src': baseUrl + 'Public/image/common/loading.gif', 'title': '处理中...请稍等'});
    $("#addimagesubmit").click(function(){
        var upload_image_file = $('#addimagefile').val();
        if (upload_image_file == '') {
            $('.image_handle_info').fadeIn('fast').html("<span class='f12 red'>还没有选择图片呢</span>").delay(1000).fadeOut('fast');
        } else {
        	$(this).ajaxStart(function(){
            	$('.image_handle_info').fadeIn('fast').html($infoImgUploading);
            }).ajaxComplete(function(){
            	$infoImgUploading.remove();
            	$('.image_handle_info').empty();
            });
        	$.ajaxFileUpload({
        		url: baseUrl + 'mallset/add',
            	secureuri: false,
            	fileElementId: 'addimagefile',
            	dataType: 'json',
            	success: function (msg){
            	    if (msg.status == 'uploaded') {
            	    	$("#image").attr({'value': msg.data});
            	    	$("#uploadedimagefileshow").attr({'src': msg.data});
            	        $(this).hide();
            	        return false;
            	    } else if (msg.status == 'error') {
            	        $('.image_handle_info').fadeIn('fast').html("<span class='f12 red'>" + msg.data + "</span>").delay(1000).fadeOut('fast');
            	        alert(msg.info);
            	    }
            	}
            });
        }
    });
    
	var editor;
	KindEditor.ready(function(K) {
		editor = K.create('textarea[name="detail"]', {
			uploadJson : baseUrl + 'mallset/addupload',
			width : '780px',
			height : '350px',
			items : [
		        'source', '|', 'undo', 'redo', '|', 'preview', 'template', 
		        'plainpaste', 'wordpaste', '|', 'justifyleft', 'justifycenter', 'justifyright',
		        'justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent', 'subscript',
		        'superscript', 'clearhtml', 'quickformat', 'selectall', '|', 'fullscreen', '/',
		        'formatblock', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold',
		        'italic', 'underline', 'strikethrough', 'lineheight', 'removeformat', '|', 'image', 
		        'table', 'hr', 'link', 'unlink']
		});
	});
	
	$("#add_commodity_button").click(function(){
		var image = $("#image").val();
		var mallset_add_name = $("#mallset_add_name").val();
		var mallset_add_price = $("#mallset_add_price").val();
		var mallset_add_good_nums = $("#mallset_add_good_nums").val();
		if (image == '') {
			alert('商品缩略图不能为空');
		} else if (mallset_add_name == '') {
			alert('商品名称不能为空');
		} else if (mallset_add_price == '') {
			alert('商品价格不能为空');
		} else if (isNaN(mallset_add_price)) {
			alert('商品价格必须是整数');
		} else if (mallset_add_good_nums == '') {
			alert('商品数目不能为空');
		} else {
			$("#add_commodity_submit").click();
		}
	});
});
