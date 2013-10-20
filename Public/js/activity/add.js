$().ready(function(){
	var editor;
	KindEditor.ready(function(K) {
		editor = K.create('textarea[name="content"]', {
			uploadJson : baseUrl + 'activitytest/addupload',
			width : '780px',
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
});
