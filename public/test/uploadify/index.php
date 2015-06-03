<?php
ini_set('upload_max_filesize', '40M');
ini_set('post_max_size', '40M');
$fileSize = ini_get('upload_max_filesize');
echo $fileSize;
?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>UploadiFive Test</title>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
<script src="jquery.uploadify.min.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="uploadify.css">
<style type="text/css">
body {
	font: 13px Arial, Helvetica, Sans-serif;
}
</style>
</head>

<body>
	<h1>Uploadify Demo</h1>
	<form>
		<div id="queue"></div>
        <select name="upload_tp">
       		<option>hymn</option><option>lectionary</option><option>image</option>
        </select>
		<input id="file_upload" name="file_upload" type="file" multiple="true">
	</form>

	<script type="text/javascript">
		<?php $timestamp = time();?>
		$(function() {
			$('#file_upload').uploadify({
				'formData'     : {
					'timestamp' : '<?php echo $timestamp;?>'
					, 'token'     : '<?php echo md5('unique_salt' . $timestamp);?>'
					, 'upload_tp' : $('select[name=upload_tp]').val()
				}
                                , 'fileSizeLimit' : '50000KB'
				, 'swf'      : 'uploadify.swf'
				, 'uploader' : 'uploadify.php'
				, 'onUploadSuccess' : function(file, data, response) {
					$('#queue').html(data);
				} 
			});
		});
	</script>
</body>
</html>