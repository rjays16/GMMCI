<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path."modules/or/ajax/op-request-new.common.php");
require($root_path.'include/inc_environment_global.php');
$xajax->printJavascript($root_path.'classes/xajax-0.2.5'); 
/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org, 
*
* See the file "copy_notice.txt" for the licence notice
*/
define('LANG_FILE','lab.php');
define('NO_2LEVEL_CHK',1);
$local_user='ck_prod_db_user';
require_once($root_path.'include/inc_front_chain_lang.php');

$thisfile=basename(__FILE__);

$title=$LDLab;
$breakfile=$root_path."modules/laboratory/seg-close-window.php".URL_APPEND."&userck=$userck";
#$imgpath=$root_path."pharma/img/";
														
# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

 $smarty->assign('bHideTitleBar',TRUE);
 $smarty->assign('bHideCopyright',TRUE);

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"$title $LDLabDb $LDSearch");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$title $LDLabDb $LDSearch");
	$rowid = $_GET['id']; 
 # Assign Body Onload javascript code
 $smarty->assign('sOnLoadJs','onLoad="preSet(\''.$rowid.'\');"');

 # Collect javascript code
/*$rowid = $_GET['id'];
$srvname = explode(",",$_GET['srvname']);
$srvid = explode(",",$_GET['srvid']);
$srvqty = explode(",",$_GET['srvqty']);
$srvcash = explode(",",$_GET['srvCash']);
$srvcharge = explode(",",$_GET['srvCharge']);
echo "rowid=".$rowid."<br><br>";
print_r($srvname);
echo "<br><br>";
print_r($srvid);
echo "<br><br>";
print_r($srvqty);
echo "<br><br>";
print_r($srvcash);
echo "<br><br>";
print_r($srvcharge);
echo "<br><br>"; */
 
 ob_start(); 
?>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>modules/or/js/or-anesthesia-tray.js?t=<?=time()?>"></script> 
<link rel="stylesheet" type="text/css" media="all" href="<?=$root_path?>js/jscalendar/calendar-win2k-cold-1.css">
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<script language="javascript" > 

function preSet(rowid)
{
	if(rowid)
	{ 
		document.getElementById('empty_anesthetic_row').style.display="none";
	}
	else
	{
		alert('no anesthetics added');
	}
}

function removeAnesthetic(rowid)
{  
	var rowId = "anesthrow"+rowid;
	document.getElementById(rowId).innerHTML = "";
	window.parent.document.getElementById('list_anesthetic_'+rowid).innerHTML = "";
	var anesth_cnt = document.getElementById('anesthetic_count').value;
	anesth_cnt=anesth_cnt-1;
	document.getElementById('anesthetic_count').value=anesth_cnt;
	if(anesth_cnt==0)
	{
		 document.getElementById('empty_anesthetic_row').style.display="";
		 var prId = "view-anesth"+"<?php echo $_GET['id']?>";
		 var spacerId = "rowspacer"+"<?php echo $_GET['id']?>";
		 window.parent.document.getElementById(prId).style.display="none";
		 window.parent.document.getElementById(spacerId).style.display="";
	}
	alert("Anesthetic removed");
}

function emptyList()
{
	var len = document.getElementById('anesthetic_count').value;
	var data = new Array();
	<?
		 $srvid = explode(",",$_GET['srvid']);
		 $cnt = count($srvid);
		 for($i=0;$i<$cnt;$i++)
		 {
			 ?>
			 data[<?echo $i?>] = '<?echo $srvid[$i]?>';
			 <?
		 }
	?>
	for(i=0;i<len;i++)
	{
			window.parent.document.getElementById('list_anesthetic_'+data[i]).innerHTML = ""; 
	}
	var prId = "view-anesth"+"<?php echo $_GET['id']?>";
	var spacerId = "rowspacer"+"<?php echo $_GET['id']?>";
	window.parent.document.getElementById(prId).style.display="none";
	window.parent.document.getElementById(spacerId).style.display="";
	document.getElementById('or_anesthetic_table-body').innerHTML="";
	document.getElementById('or_anesthetic_table-body').innerHTML="<tr id='empty_anesthetic_row'><td colspan='7'>No anesthetics added...</td></tr>"; 
	alert("List empty")	;
}
</script>
<?php
$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

# Buffer page output

ob_start();

?>
<form action="<?= $thisfile.URL_APPEND."&clear_ck_sid=".$clear_ck_sid ?>" method="post" name="suchform"> 
		<div style="padding:10px;width:95%;border:0px solid black">
		<font class="warnprompt"><br></font> 
		 <table class="segList" width="100%" id="or_anesthetic_table" align="center">
		 <thead>
			<tr>
				<th width='5%'></th>
				<th width='20%' align='center'>Anesthetic</th>
				<th width='20%' align='center'>Quantity</th>
				<th width='20%' align='center'>Price Cash</th>
				<th width='20%' align='center'>Price Charge</th>
			</tr>
			</thead>
			<tbody id="or_anesthetic_table-body">
			<tr id="empty_anesthetic_row"><td colspan="7">No anesthetics added...</td></tr>
			<?
			global $db;
				if($_GET['srvid']!='')
				{
						//$srvname = explode(",",$_GET['srvname']);
						$srvid = explode(",",$_GET['srvid']);
						$srvqty = explode(",",$_GET['srvqty']);
						$srvcash = explode(",",$_GET['srvCash']);
						$srvcharge = explode(",",$_GET['srvCharge']);
						?>
						<input type="hidden" name="anesthetic_count"  id="anesthetic_count" value="<?php echo count($srvid)?>"/>
						<?
						for($i=0;$i<count($srvid);$i++)
						{
							$query = "select artikelname from care_pharma_products_main where bestellnum=".$db->qstr($srvid[$i]);
							$srvname = $db->GetOne($query);
							if($_GET['mode']=='view')
							{
								 echo "<tr class='wardlistrow' id='anesthrow".$srvid[$i]."'>".
									"<td width='5%' align='center'></td>".
									//"<td width='20%' align='center'>".$srvname[$i]."</td>".
									"<td width='20%' align='center'>".$srvname."</td>".
									"<td width='20%' align='center'>".$srvqty[$i]."</td>".
									"<td width='20%' align='center'>".number_format($srvcash[$i],2)."</td>".
									"<td width='20%' align='center'>".number_format($srvcharge[$i],2)."</td>".
									"</tr>";
							}
							else
							{
								echo "<tr class='wardlistrow' id='anesthrow".$srvid[$i]."'>".
									"<td width='5%' align='center'><img src='../../../images/btn_delitem.gif' style='cursor: pointer;' onclick='removeAnesthetic(\"".$srvid[$i]."\");'/></td>".
									//"<td width='20%' align='center'>".$srvname[$i]."</td>".
									"<td width='20%' align='center'>".$srvname."</td>".
									"<td width='20%' align='center'>".$srvqty[$i]."</td>".
									"<td width='20%' align='center'>".number_format($srvcash[$i],2)."</td>".
									"<td width='20%' align='center'>".number_format($srvcharge[$i],2)."</td>".
									"</tr>";
							}
						}
				}
				else
				{
					echo "<tr id='empty_anesthetic_row'><td colspan='7'>No anesthetics added...</td></tr> ";
				}
			?>
			</tbody>
		</table>
		<table>
			<tbody>
				<tr>
					<td width="87%"></td>
					<?php
					if($_GET['mode']=='view')
					{?>
					<td></td>
					<?
					}
					else
					{?>
					<td><img class='segSimulatedLink' id='clear_items' name='clear_items' src='../../../images/btn_emptylist.gif' border=0 alt='empty items' align='absmiddle' onclick='emptyList();'/></td>
					<?
					}?>
				</tr>
			</tbody>
		</table>	 
</div>
</form>
	
		<input type="hidden" name="sid" value="<?php echo $sid?>">
		<input type="hidden" name="lang" value="<?php echo $lang?>">
		<input type="hidden" name="cat" value="<?php echo $cat?>">
		<input type="hidden" id="userck" name="userck" value="<?php echo $userck ?>">
		<input type="hidden" name="mode" value="search">
		<input type="hidden" name="key" id="key">
		<input type="hidden" name="pagekey" id="pagekey">
	


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
		<input type="hidden" name="sid" value="<?php echo $sid ?>">
		<input type="hidden" name="lang" value="<?php echo $lang ?>">
		<input type="hidden" name="userck" value="<?php echo $userck ?>">
</form>

</div>
<?php

$sTemp = ob_get_contents();
ob_end_clean();
 $smarty->assign('sHiddenInputs',$sTemp);
# Assign the form template to mainframe

 $smarty->assign('sMainFrameBlockData',$sTemp);

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');
?>
