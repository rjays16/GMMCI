<?php
/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org, 
*
* See the file "copy_notice.txt" for the licence notice
*/

define('LANG_FILE','products.php');
$local_user='ck_prod_db_user';
require_once($root_path.'include/inc_front_chain_lang.php');

//$db->debug=1;

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"Pharmacy::Reports");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('products_db.php','search','$from','$cat')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"Pharmacy::Reports");

 # Assign Body Onload javascript code
 $smarty->assign('sOnLoadJs','onLoad=""');

 # Collect javascript code
 ob_start()

?>
<!-- OLiframeContent(src, width, height) script:
 (include WIDTH with its parameter equal to width, and TEXTPADDING,0, in the overlib call)
-->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>

<!-- Core module and plugins:
-->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>

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
	background-color:#ffffff;
	border:1px outset #3d3d3d;
}
.olcg {
	background-color:#ffffff; 
	background-image:url("<?= $root_path ?>images/bar_05.gif");
	text-align:center;
}
.olcgif {background-color:#333399; text-align:center;}
.olfg {
	background-color:#ffffff; 
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

.tabFrame {
	margin:5px;
}
-->
</style> 

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script language="javascript" type="text/javascript">
<!--
	var URL_FORWARD = "<?= URL_APPEND."&clear_ck_sid=$clear_ck_sid" ?>";

	function pSearchClose() {
		cClick();
	}
	
	function selOnChange() {
		var optSelected = $('selreport').options[$('selreport').selectedIndex];
		var spans = document.getElementsByName('selOptions');
		for (var i=0; i<spans.length; i++) {
			if (optSelected) {
				if (spans[i].getAttribute("segOption") == optSelected.value) {
					spans[i].style.display = "";
				}
				else
					spans[i].style.display = "none";
			}
		}
	}

	
	function openReport() {
		var rep = $('selreport').options[$('selreport').selectedIndex].value
		var url = 'seg-pharma-report-'+rep+'.php?'
		var query = new Array()
		var params = document.getElementsByName('param')
		for (var i=0; i<params.length; i++) {
			if (params[i].getAttribute("segOption") == rep) {
				query.push(params[i].getAttribute('paramName')+'='+params[i].value)
			}
		}
		//alert(url+query.join('&'))
		window.open(url+query.join('&'),null,"width=800,height=600,menubar=no,resizable=yes,scrollbars=no");
	}

-->
</script>

<?php

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);
# Buffer page output
include($root_path."include/care_api_classes/class_order.php");
$order = new SegOrder('pharma');

ob_start();
?>

<br>

<form action="<?= $thisfile.URL_APPEND."&target=list&clear_ck_sid=".$clear_ck_sid ?>" method="post" name="suchform" onSubmit="return validate()">
<div style="margin:5px;font-weight:bold;color:#660000"><?= $sWarning ?></div>
<div style="width:60%">
	<table width="100%" border="0" style="font-size: 12px; margin-top:5px" cellspacing="2" cellpadding="2">	
		<tbody>
			<tr>
				<td align="left" class="jedPanelHeader" ><strong>Report options</strong></td>
			</tr>
			<tr>
				<td nowrap="nowrap" align="right" class="jedPanel">
					<table width="100%" border="0" cellpadding="2" cellspacing="0">
						<tr>
							<td width="20%" align="right" nowrap="nowrap"><strong>Select report type</strong></td>
							<td>
								<select class="jedInput" name="selreport" id="selreport" onchange="selChange()"/>
									<option value="issuance">Daily Issuances</option>
								</select>								
							</td>
						</tr>
						<tr name="selOptions" segOption="issuance">
							<td align="right">Pharmacy area</td>
							<td>
																<select class="jedInput" name="param" id="selarea" onchange="" paramName="area" segOption="issuance">
																	<option value="" style="font-weight:bold">All areas</option>
<?php
	require_once($root_path.'include/care_api_classes/class_product.php');
	$prod_obj=new Product;
	$prod=$prod_obj->getAllPharmaAreas();
	while($row=$prod->FetchRow()){
		$checked=strtolower($row['area_code'])==strtolower($_REQUEST['selarea']) ? 'selected="selected"' : "";
		echo "									<option value=\"".$row['area_code']."\" $checked>".$row['area_name']."</option>\n";
	}
?>								
								</select>
							</td>
						</tr>
						<tr name="selOptions" segOption="issuance">
							<td align="right" >Select date</td>
							<td>
									<input class="jedInput" name="param" id="issuance" type="text" size="12" value="" paramName="date" segOption="issuance"/>
									<img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_issuance" align="absmiddle" style="cursor:pointer;"  />
									<script type="text/javascript">
										Calendar.setup ({
											inputField : "issuance", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_issuance", singleClick : true, step : 1
										});
									</script>
							</td>
						</tr>
						<tr>
							<td></td>
							<td colspan="2">
								<input type="button" value="View report"  class="jedButton" onclick="openReport()"/>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
</div>

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

<input type="hidden" name="sid" value="<?php echo $sid ?>">
<input type="hidden" name="lang" value="<?php echo $lang ?>">
<input type="hidden" name="userck" value="<?php echo $userck ?>">
<input type="hidden" name="cat" value="<?php echo $cat?>">
<input type="hidden" name="userck" value="<?php echo $userck?>">  
<input type="hidden" name="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>">
<input type="hidden" name="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>">
<input type="hidden" name="lockflag" value="<?php echo  $lockflag?>">

<input type="hidden" id="delete" name="delete" value="" />
<input type="hidden" id="page" name="page" value="<?= $current_page ?>" />
<input type="hidden" id="lastpage" name="lastpage"  value="<?= $last_page ?>" />
<input type="hidden" id="jump" name="jump">



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