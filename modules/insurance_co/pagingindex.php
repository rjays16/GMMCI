<?
/*
INDEX FILE
PLEASE DON'T CHANGE ANY FILENAMES, REFERENCES OTHER THAN WHAT IS INSTRUCTED THANK YOU
1. Please refer to config.php for initial configuration
2. set your preference (whether to use ajax or simple href link) in refresher.php file

-I am sharing this under a GPL distribution
-for any questions regarding usage, problems, questions and/or suggestions please email me at janpex@gmail.com

*/
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once('./roots.php');
require($root_path."modules/insurance_co/ajax/hcplan-admin.common.php");
require_once($root_path.'include/inc_environment_global.php');

define('LANG_FILE','finance.php');
$local_user='aufnahme_user';
require_once($root_path.'include/inc_front_chain_lang.php');

$breakfile=$root_path.'main/spediens.php'.URL_APPEND;
$thisfile=basename(__FILE__);

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('system_admin');
 
 # Title in toolbar
 $smarty->assign('sToolbarTitle',"$LDInsuranceCo :: $LDManager");

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('insurance_list.php')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$LDInsuranceCo :: $LDListAll");
 
 ob_start();

/*
echo '<script type="text/javascript" src="'.$root_path.'js/pagingajax.js"></script>'."\r\n";
*/
echo '<script type="text/javascript" src="'.$root_path.'modules/insurance_co/js/hcplan-listing-functions.js"></script>'."\r\n";
$xajax->printJavascript($root_path.'classes/xajax');

	$sTemp = ob_get_contents();
	ob_end_clean();
	$sTemp.="
<script type='text/javascript'>
	var init=false;
	var userid='".$_SESSION['sess_temp_userid']."';
</script>";

$smarty->append('JavaScript',$sTemp);


/*
echo '<style>
a.button2:link, a.button2:visited {
 border-bottom:solid 1px #999999;
 border-right:solid 1px #999999;
 text-align:center;
 color:#000000;
 width:3px;
 padding:0px 3px 0px 3px;
 margin-right:2px;
 }

a.button2:active {
 border-bottom:solid 1px #D3D3D3;
 border-right:solid 1px #D3D3D3;
 }

.button2_active {
 background-color:#E0E0E0;
 border-bottom:solid 1px #999999;
 border-right:solid 1px #999999;
 text-align:center;
 color:#000000;
 width:3px;
 padding:0px 3px 0px 3px;
 margin-right:2px;
 }

p, div 
{
 font-family:Verdana, Geneva, sans-serif;
 font-size:10px;
 }
</style>'."\r\n";
*/

ob_start();
?>
This is a demonstration of Ajax Paging!
<br><br>

<table border="1" width="80%" cellpadding="0" cellspacing="0">
	<tr>
		<td align="center" style="padding:1px">
			<div style="width:100%;height:304px;overflow:hidden;border:1px solid black;">
			<div style="width:100%;height:320px;overflow:scroll;">
			<table  id="my_table" class="segList" width="100%" border="0" cellpadding="0" cellspacing="1">		
				<thead>
					<tr class="wardlistrow1" id="my_table_header">
						<th align="center" width="30%" nowrap>Person ID</th>
						<th align="center" width="70%" nowrap>Name</th>
					</tr>					
				</thead>
				<tbody>
				</tbody>
			</table>
			</div></div>
		</td>
	</tr>
	
</table>
<br><br>
<table align="center" width="80%">
<tr>
<td><input type="button" onclick="prevPage();" value="<< Previous"></td>
<td align="right"><input type="button" onclick="nextPage();" value="Next >>"></td>
</tr>
</table>
<?php

$sTemp = ob_get_contents();
ob_end_clean();

# Assign page output to the mainframe template

$smarty->assign('sMainFrameBlockData',$sTemp);
 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');

?>