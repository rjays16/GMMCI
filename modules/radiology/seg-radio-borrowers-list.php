<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
#include xajax common
require($root_path.'modules/radiology/ajax/radio-borrowers-list.common.php');

define('LANG_FILE','lab.php');
define('NO_2LEVEL_CHK',1);
define('MAX_BLOCK_ROWS',30); 

#$local_user='ck_prod_db_user';
$local_user='ck_radio_user';   
require_once($root_path.'include/inc_front_chain_lang.php');
//$db->debug=1;
#$append=URL_APPEND."&target=".$target."&noresize=1&user_origin=".$user_origin."&dept_nr=".$dept_nr;   

$append=URL_APPEND."&status=".$status."&target=".$target."&noresize=1&user_origin=".$user_origin."&dept_nr=".$dept_nr;   # burn added: Septmeber 19, 2007
$breakfile="radiolog.php$append";   # burn added: Septmeber 19, 2007

$breakfile=$root_path.'modules/radiology/'.$breakfile;

$append='&status='.$status.'&target='.$target.'&user_origin='.$user_origin."&dept_nr=".$dept_nr;

$thisfile=basename(__FILE__);

$toggle=0;

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"Radiology :: Borrowers List");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"Radiology :: Borrowers List");

 # Assign Body Onload javascript code
# $smarty->assign('sOnLoadJs','onLoad="document.suchform.keyword.select()"');

 # Collect javascript code
 ob_start();
 
echo "<!--Include dojo toolkit -->";
echo "<script type=\"text/javascript\" src=\"".$root_path."js/dojo/dojo.js\"></script>"."\n \r";
echo "<script type=\"text/javascript\" src=\"".$root_path."js/jsprototype/prototype1.5.js\"></script>"."\n \r";
echo "<script type=\"text/javascript\" src=\"js/radio-borrowers-list.js\"></script>";

?>
<!-- Include dojoTab Dependencies -->
<script type="text/javascript">
	dojo.require("dojo.widget.TabContainer");
	dojo.require("dojo.widget.LinkPane");
	dojo.require("dojo.widget.ContentPane");
	dojo.require("dojo.widget.LayoutContainer");
	dojo.require("dojo.event.*");
</script>
<script language="javascript">
	dojo.addOnLoad(evtOnClick);
</script>

<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<!-- Core module and plugins: -->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<!--  Overlib stylesheet -->
<link rel="stylesheet" type="text/css" href="js/overlib-radio-list.css">

<!--  For Dojo -->
<style type="text/css">
	body{font-family : sans-serif;}
	dojoTabPaneWrapper{ padding : 10px 10px 10px;}
</style>

<script type="text/javascript">
<!--
 OLpageDefaults(BGCLASS,'olbg', CGCLASS,'olcg', FGCLASS,'olfg',
 CAPTIONFONTCLASS,'olcap', CLOSEFONTCLASS,'olclo', TEXTFONTCLASS,'oltxt');
//-->
</script>

<style type="text/css">
<!--
.olbg {
	background-image:url("<?= $root_path ?>images/bar_05.gif");
	background-color:#0000ff;
	border:1px solid #4d4d4d;
}
.olcg {
	background-color:#aa00aa; 
	background-image:url("<?= $root_path ?>images/bar_05.gif");
	text-align:center;
}
.olcgif {background-color:#333399; text-align:center;}
.olfg {
	background-color:#ffffcc; 
	text-align:center;
}
.olfgif {background-color:#bbddff; text-align:center;}
.olcap {
	font-family:Arial; font-size:13px; 
	font-weight:bold; 
	color:#708088;
}
a.olclo {font-family:Verdana; font-size:11px; font-weight:bold; color:#ddddff;}
a.olclo:hover {color:#ffffff;}
.oltxt {font-family:Arial; font-size:12px; color:#000000;}
.olfgright {text-align: right;}
.olfgjustify {background-color:#cceecc; text-align: justify;}

a {color:#338855;font-weight:bold;}
a:hover {color:#FF00FF;}
.text12 {font-family:Verdana,Arial,sans-serif; font-size:12px;}
.text14 {font-family:Verdana,Arial,sans-serif; font-size:14px;}
.text16 {font-family:Verdana,Arial,sans-serif; font-size:16px;}
.text18 {font-family:Verdana,Arial,sans-serif; font-size:18px;}

.myHeader {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:22px;}
.mySubHead {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:18px;}
.mySpacer {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:4px;}
.myText {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:13px;color:#000000;}
.snip {font-family:Verdana,Arial,Helvetica;font-size:10px;}
.purple14 {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:14px;color:purple;
 font-weight:bold;}
.purple18 {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:18px;color:purple;
 font-weight:bold;font-style:italic;}
.yellow {color:#ffff00;}
.red {color:#cc0000;}
.blue {color:#0000cc;}
-->
</style> 


<?php
//Print xajax script
$xajax->printJavascript($root_path.'classes/xajax-0.2.5');

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

ob_start();
?>
<a name="pagetop"></a>
<br>
<div align="center">
			<table border=0 cellspacing=5 cellpadding=5>			
				<tr bgcolor="#f3f3f3">
					<td>
						&nbsp;<br>												
						<!--<font SIZE=2 FACE="Arial">Enter the search key</font><br>-->
						Enter the search key
						<form name="searchform" onSubmit="return false;">
							<!--<input type="text" name="searchkey" id="searchkey" size=40 maxlength=40 onChange="trimStringSearchMask(this);" onKeyUp="if (this.value.length >= 3){$('skey').value=$('searchkey').value; jsOnClick();}" value="">-->
							<input type="text" name="searchkey" id="searchkey" size=40 maxlength=40 onChange="trimStringSearchMask(this);"  value="">
							<br>
							<span style="font-family:Arial, Helvetica, sans-serif; font-size:11px">
								(Borrower/Employee's ID/Name, Patient's HRN/RID/Name, Service Code, Date Borrowed, Releaser's Name).<br>
								 Enter dates in <font style="color:#0000FF; font-weight:bold">MM/DD/YYYY</font> format
							</span>
							<p>
							<input type="image" src="<?=$root_path?>images/his_searchbtn.gif" align="absmiddle" onClick="$('skey').value=$('searchkey').value; jsOnClick();">
						</form>
					</td>
				</tr>				
			</table>
</div>

	<input type="hidden" name="sid" id="sid" value="<?php echo $sid?>">
	<input type="hidden" name="lang" id="lang" value="<?php echo $lang?>">
	<input type="hidden" name="cat" value="<?php echo $cat?>">
	<input type="hidden" name="userck" value="<?php echo $userck ?>">
	<input type="hidden" name="mode" value="search">

<br>
<span id='textResult' style="text-align:left"></span>
<br><br>
<!--  Tab Container for radiology request list -->
<div id="rlistContainer"  dojoType="TabContainer" style="width:90%; height:28.5em;" align="center">
	<div dojoType="ContentPane" widgetId="tab0" label="All" style="display:none;overflow:auto; border:1px solid #8cadc0;">
		<!--  Table:list of borrowers -->
		<table id="Ttab0" class="segList" border="0" cellpadding="0" cellspacing="0">
			<!-- List of all radiology borrowers -->
		</table>
		<img id="ajax-loading" src="<?= $root_path ?>images/loading6.gif" align="absmiddle" border="0" style="display:none"/>
	</div>
	<!-- tabcontent for radiology sub-department -->
<?php

if ($allow_accessSysAd){
     $allow_accessCT = 1; 
     $allow_accessUTZ = 1;
     $allow_accessOBGYNEUTZ = 1;
     $allow_accessMRI = 1;
     $allow_accessXRAY = 1;
  }
  
  $dept_nr_list = "";
  $waccess = 0;
  if ($allow_accessCT){
     $dept_nr_list .= ",'167',"; 
     $waccess = $waccess + 1;
  }   
                  
  if ($allow_accessUTZ){
     $dept_nr_list .= ",'165',";
     $waccess = $waccess + 1;
  }
  
  if ($allow_accessOBGYNEUTZ){
     $dept_nr_list .= ",'209',";
     $waccess = $waccess + 1;
  }
  
  if ($allow_accessMRI){
     $dept_nr_list .= ",'208',";
     $waccess = $waccess + 1;
  }   
                     
  if ($allow_accessXRAY){
     $dept_nr_list .= ",'164', '166',";      
     $waccess = $waccess + 1;
  }   
   
  
  if (($dept_belong['dept_nr'])&&($waccess==0)){
    $dept_nr_list = "'".$dept_belong['dept_nr']."'";
    $waccess = $waccess + 1;
    #die('h - '.$dept_belong['dept_nr']);
  }
      
  $dept_nr_list = substr($dept_nr_list,1,strlen($dept_nr_list)-2);
  $dept_nr_list = str_replace(",,",",",$dept_nr_list);
  
#Department object
include_once($root_path.'include/care_api_classes/class_department.php');
$dept_obj = new Department;

#$radio_sub_dept=$dept_obj->getSubDept($dept_nr);
$radio_sub_dept=$dept_obj->getSubDept2($dept_nr,$dept_nr_list);

if($dept_obj->rec_count){
	$dept_counter=2;
	while ($rowSubDept = $radio_sub_dept->FetchRow()){
		if (trim($rowSubDept['name_short'])!=''){		
			$text_name = trim($rowSubDept['name_short']);
		}elseif (trim($rowSubDept['id'])!=''){
			$text_name = trim($rowSubDept['id']);
		}else{
			$text_name = trim($rowSubDept['name_formal']);
		}
?>		
<div dojoType="ContentPane" widgetId="tab<?=$rowSubDept['nr']?>" label="<?=$text_name?>" style="display:none;overflow:auto; border:1px solid #8cadc0;" >
   	<table id="Ttab<?=$rowSubDept['nr']?>" cellpadding="0" cellspacing="0" class="segList">
   		<!-- List of Radiology Requests  -->
   	</table>
   	<img id="ajax-loading" src="<?= $root_path ?>images/loading6.gif" align="absmiddle" border="0" style="display:none"/>
</div>
<?php 
		$dept_counter++;
	} # end of while loop
}   # end of if-stmt 'if ($dept_obj->rec_count)'
?>
</div>
<br />
<!-- commented by VAN 07-31-08 -->
<!--
<img id="btnView" name="btnView" src="../../images/unreturned.gif" style="cursor:pointer" onclick="viewUnreturned();" />
-->

<!--
	<input type="hidden" name="skey" id="skey" value="<?php echo $HTTP_SESSION_VARS['sess_searchkey']; ?>"> 
-->
	<input type="hidden" name="skey" id="skey" value="*"> 
	<input type="hidden" name="smode" id="smode" value="<?php echo $mode; ?>">
	<input type="hidden" name="starget" id="starget" value="<?php echo $target; ?>">
	<input type="hidden" name="thisfile" id="thisfile" value="<?php echo $thisfile; ?>">
	<input type="hidden" name="rpath" id="rpath" value="<?php echo $root_path; ?>">
	<input type="hidden" name="pgx" id="pgx" value="<?php echo $pgx; ?>">
	<input type="hidden" name="oitem" id="oitem" value="<?= $oitem? $oitem:'create_dt' ?>">
	<input type="hidden" name="odir" id="odir" value="<?= $odir? $odir:'ASC' ?>">
	<input type="hidden" name="totalcount" id="totalcount" value="<?php echo $totalcount; ?>">

<script language="javascript">
	document.getElementById('skey').value = 'null';
	jsOnClick();
</script>

<br />
<hr>

<?php

# Workaround to force display of results  form
$bShowThisForm = TRUE;

# If smarty object is not available create one
if(!isset($smarty)){
	/**
 * LOAD Smarty
 * param 2 = FALSE = dont initialize
 * param 3 = FALSE = show no copyright
 * param 4 = FALSE = load no javascript code
 */
	include_once($root_path.'gui/smarty_template/smarty_care.class.php');
	$smarty = new smarty_care('common',FALSE,FALSE,FALSE);
	
	# Set a flag to display this page as standalone
	$bShowThisForm=TRUE;
}

?>

<form action="<?php echo $breakfile?>" method="post">
	<input type="hidden" name="searchkey" id="searchkey" onchange="trimStringSearchMask(this)" >
	<input type="hidden" name="sid" value="<?php echo $sid ?>">
	<input type="hidden" name="lang" value="<?php echo $lang ?>">
	<input type="hidden" name="userck" value="<?php echo $userck ?>">
	<input type="hidden" name="mode" id="mode" value="search">
	
</form> 


<?php

$sTemp = ob_get_contents();
ob_end_clean();

# Assign the form template to mainframe

 $smarty->assign('sMainFrameBlockData',$sTemp);

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');
?>
