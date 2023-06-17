<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org, 
*
* See the file "copy_notice.txt" for the licence notice
*/
define('LANG_FILE','editor.php');
$local_user='ck_editor_user';
require_once($root_path.'include/inc_front_chain_lang.php');

/* Load the date formatter */
require_once($root_path.'include/inc_date_format_functions.php');

$default_file_return='newscolumns.php';

$title=$HTTP_SESSION_VARS['sess_title'];
#print_r($HTTP_SESSION_VARS);

$returnfile='editor-4plus1-select-art.php'.URL_APPEND;
$breakfile='newscolumns.php'.URL_APPEND;

//$HTTP_SESSION_VARS['sess_file_return']=basename(__FILE__);
/*
# Load the javascript editor form checker 
require_once($root_path.'include/inc_js_editor_chkform.php');

# Load the dates js values
require($root_path.'include/inc_checkdate_lang.php'); 
*/

?>
<?php html_rtl($lang); ?>
<head>
<?php echo setCharSet(); ?>
<title></title>

<script language="javascript">
<!-- 
function showpic(d)
{
	if(d.value) document.images.headpic.src=d.value;
}

<?php 
# Load the javascript editor form checker 
require_once($root_path.'include/inc_js_editor_chkform.php');

# Load the dates js values
require($root_path.'include/inc_checkdate_lang.php'); 
?>

<!--  Root path for the html WYSIWYG editor -->
//var _editor_url="<?php echo $root_path.'js/html_editor/'; ?>";
var _editor_url="<?php echo $root_path ?>js/html_editor/";

// -->
 </script>
<!-- load html editor scripts -->
<!--
<script language="javascript"  type="text/javascript" src="<?php echo $root_path.'js/html_editor/'; ?>htmlarea.js"></script>
<script language="javascript"  type="text/javascript" src="<?php echo $root_path.'js/html_editor/'; ?>lang/en.js"></script>
<script language="javascript"  type="text/javascript" src="<?php echo $root_path.'js/html_editor/'; ?>dialog.js"></script>
-->
<script language="javascript"  type="text/javascript" src="<?php echo $root_path ?>js/html_editor/htmlarea.js"></script>
<script language="javascript"  type="text/javascript" src="<?php echo $root_path ?>js/html_editor/lang/en.js"></script>
<script language="javascript"  type="text/javascript" src="<?php echo $root_path ?>js/html_editor/dialog.js"></script>

<!--
<style type="text/css">
/*@import url("<?php echo $root_path.'js/html_editor/'; ?>htmlarea.css")*/
</style>
-->
<!-- edited by VAN 04-10-08-->
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/html_editor/htmlarea.css" />
<!------------------------->

<!--  Load validators -->
<script language="javascript" src="<?php echo $root_path ?>js/checkdate.js" type="text/javascript"></script>
<script language="javascript" src="<?php echo $root_path ?>js/setdatetime.js"></script>
<script language="javascript" src="<?php echo $root_path; ?>js/dtpick_care2x.js"></script>

<?php 

	require($root_path.'include/inc_css_a_hilitebu.php'); 
	
	#added by VAN 04-10-08
	echo '<link rel="stylesheet" type="text/css" media="all" href="' .$root_path.'js/jscalendar/calendar-win2k-cold-1.css">';
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar.js"></script>';
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/lang/calendar-en.js"></script>';
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar-setup_3.js"></script>';
	
	$phpfd=$date_format;
	
	$phpfd=str_replace("dd", "%d", strtolower($phpfd));
	$phpfd=str_replace("mm", "%m", strtolower($phpfd));
	$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
	#-----------------------------
?>

</head>
<body onLoad="HTMLArea.replace('newsbody');document.selectform.newstitle.focus()">

<form ENCTYPE="multipart/form-data" name="selectform"  id="selectform" method="post" action="editor-4plus1-save.php" onSubmit="return chkForm()">
<!--<FONT  SIZE=6 COLOR="#cc6600">-->

<FONT  SIZE=6 color="#0066FF">
<b><?php echo $title ?></b></FONT>
<?php if (empty($title)){ ?>
	<font size=3 color="#0000CC"><b> <?php echo $LDArticleTxt ?> #<?php echo $artopt ?></b></font>
<?php }else{ ?>	
	<font size=3 color="#0000CC"> <?php echo $LDArticleTxt ?> #<?php echo $artopt ?></font>
<?php } ?>	
<hr>
<table border=0>
  <tr >

    <td valign=top><img <?php echo createLDImgSrc($root_path,'x-blank.gif','0') ?>  id="headpic"><br>
  </td>

    <td class="submenu" colspan=2><FONT color="#0000cc" size=3><b><?php echo $LDTitleTag ?>:</b><br>
	<font size=1><?php echo $LDTitleMaxNote ?><br>
	<input type="text" name="newstitle" size=50 maxlength=255><br>
	<FONT color="#0000cc" size=3><b><?php echo $LDHeader ?>:</b><br>
	<font size=1><?php echo $LDHeaderMaxNote ?><br>
	
	<textarea name="preface" cols=50 rows=5 wrap="physical" id="preface"></textarea><br>
	
	<FONT color="#0000cc" size=3><b><?php echo $LDNews ?>:</b><br>
	
	<textarea name="newsbody" cols=50 rows=14 wrap="physical" id="newsbody"></textarea><br>
	
  	<FONT color="#0000cc" size=2><b><?php echo $LDPicFile ?>:</b><br>
	<input type="file" name="pic" onChange="showpic(this)" ><br>
	<!--commented by VAN 04-10-08 -->
     <!--<input type="button" value="<?php echo $LDPreviewPic ?>" onClick="showpic(document.selectform.pic)"><br>-->
 	<FONT color="#0000cc" size=2><b><?php echo $LDAuthor ?>:</b></font><br>
	<input type="text" name="author" size=30 maxlength=40><br>
  	<FONT color="#0000cc" size=2><b><?php echo $LDPublishDate ?>:</b></font><br>
	<!--
	<input type="text" name="publishdate" size=10 maxlength=10 onBlur="IsValidDate(this,'<?php echo $date_format ?>')" onKeyUp="setDate(this,'<?php echo $date_format ?>','<?php echo $lang ?>')">
  	<a href="javascript:show_calendar('selectform.publishdate','<?php echo $date_format ?>')">
	<img <?php echo createComIcon($root_path,'show-calendar.gif','0','absmiddle',TRUE); ?>></a>
	-->
<!-- 	<input type="text" name="publishdate" size=10 maxlength=10 onKeyUp="setDate(this)">
 
 -->
 <!--
   [ <?php   
 $dfbuffer="LD_".strtr($date_format,".-/","phs");
  echo $$dfbuffer; 
 ?> ]
 -->
 
 <!--added by VAN 04-10-08 -->
 <input name="publishdate" id="publishdate"  type="text" size=10 maxlength=10  value="<?php if(!empty($date_start)) echo @formatDate2Local($date_start,$date_format);  ?>"  onBlur="IsValidDate(this,'<?php echo $date_format ?>')" onKeyUp="setDate(this,'<?php echo $date_format ?>','<?php echo $lang ?>')">
			<!--<a href="javascript:show_calendar('aufnahmeform.date_start','<?php echo $date_format ?>')">-->
			<img <?php echo createComIcon($root_path,'show-calendar.gif','0','absmiddle'); ?> id="publishdate_trigger" style="cursor:pointer "><font size=1>[<?php
			$dfbuffer="LD_".strtr($date_format,".-/","phs");
			echo $$dfbuffer;
		?>] </font>
		
			<!--EDITED: SEGWORKS -->
	<script type="text/javascript">
	Calendar.setup ({
		inputField : "publishdate", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "publishdate_trigger", singleClick : true, step : 1
	
	});
</script>
<!--------------------------------->
 </td>


  </tr>
  <tr>

    <td align=right >
	<a href="<?php echo $returnfile ?>"><img <?php echo createLDImgSrc($root_path,'back2.gif','0') ?>></a>
  </td>

     <td >
<input type="image" <?php echo createLDImgSrc($root_path,'continue.gif','0') ?>>
  </td>
    <td align=right >
	<a href="<?php echo $breakfile ?>"><img <?php echo createLDImgSrc($root_path,'cancel.gif','0') ?>></a>
  </td>
  </tr>
</table>
<input type="hidden" name="sid" value="<?php echo $sid ?>">
<input type="hidden" name="target" value="<?php echo $target ?>">
<input type="hidden" name="artnum" value="<?php echo $artopt ?>">
<input type="hidden" name="title" value="<?php echo strtr($title," ","+") ?>">
<input type="hidden" name="lang" value="<?php echo $lang ?>">
<input type="hidden" name="user_origin" value="<?php echo $user_origin ?>">
<input type="hidden" name="mode" value="save">
<INPUT TYPE="hidden" name="MAX_FILE_SIZE" value="1000000">

</form>
</body>
</html>
