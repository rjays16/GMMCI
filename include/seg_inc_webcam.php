<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Take Photo</title>
<?php 
	require_once('inc_init_main.php');
	require_once('roots.php');
?>
</head>
<BODY BGCOLOR="000000" onfocus="setImgFileName(); setFTPParams();">
<script language ="javascript">
<!--
	function closeWindow() {
		tmpwin = window.open("blank.html", "_self");
	}
	
	function setImgFileName() {		
		if ('<?php echo $HTTP_SESSION_VARS['sess_login_userid']; ?>' == '')
			s_imgfile = '<?php echo $HTTP_SESSION_VARS['sess_temp_userid']; ?><?php echo date("YmdHis"); ?>';
		else
			s_imgfile = '<?php echo $HTTP_SESSION_VARS['sess_login_userid']; ?><?php echo date("YmdHis"); ?>';
				
		document.webapplet.setFileName(s_imgfile);
	}
	
	function setFTPParams() {
		document.webapplet.setFTPParams('<?php echo $fotoserver_ip; ?>', '<?php echo $ftp_userid; ?>', '<?php echo $ftp_passwrd; ?>', '<?php echo $image_path; ?>');	
	}
	
	function assignPhotoName(sPhotoNM) {	
		mywin=parent.window.opener;
		mywin.document.aufnahmeform.photo_filename.value = sPhotoNM;
		mywin.document.images.headpic.src="<?php echo $root_path.$foto_path ?>" + sPhotoNM;
	}
	
//-->
</script>
<CENTER>
<APPLET name="webapplet"
	codebase="../classes/webcam/"
    code="WebCamFinal.class"
	width	= "450"
	height	= "300"
  MAYSCRIPT>
</APPLET>
</CENTER>
</BODY>
</html>
