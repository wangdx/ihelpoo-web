$().ready(function(){
	var editor;
	KindEditor.ready(function(K) {
		editor = K.create('textarea[name="content"]', {
			uploadJson : baseUrl + 'activitytest/addupload',
			width : '800px',
			height : '350px',
			items : [
		        'source', '|', 'undo', 'redo', '|', 'preview', 'template', 
		        'plainpaste', 'wordpaste', '|', 'justifyleft', 'justifycenter', 'justifyright',
		        'justifyfull', 'insertorderedlist', 'insertunorderedlist','|', 'fullscreen', '/',
		        'formatblock', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold',
		        'italic', 'underline', 'removeformat', '|', 'image', 
		        'table', 'link', 'unlink']
		});
	});
	
	$("#activity_submit_btn_a").click(function(){
		var subject = $("#subject").val();
		var activity_ti = $("#activity_ti").val();
		if (subject == '') {
			alert('活动主题不能为空');
		} else if (activity_ti == '') {
			alert('活动时间不能为空');
		} else {
			$("#activity_submit_btn").click();
		}
	});
});
