<?php
	# Start Smarty templating here
 /**
 * LOAD Smarty
 */
	
 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme
 	error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
	require('./roots.php');
	

/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org, 
*
* See the file "copy_notice.txt" for the licence notice
*/
#	define('LANG_FILE','lab.php');
	$lang_tables[] = 'departments.php';
	define('LANG_FILE','konsil.php');
	define('NO_2LEVEL_CHK',1);
	$local_user='ck_radio_user';
	require($root_path.'include/inc_environment_global.php');
	require_once($root_path.'include/inc_front_chain_lang.php');
	require($root_path.'modules/radiology/ajax/radio-patient-common.php');

	# Create global config object
	require_once($root_path.'include/care_api_classes/class_globalconfig.php');
	require_once($root_path.'include/inc_date_format_functions.php');

	$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
	$glob_obj->getConfig('refno_%');
	if($glob_obj->getConfig('date_format')) $date_format=$GLOBAL_CONFIG['date_format'];
	$date_format=$GLOBAL_CONFIG['date_format'];

	$phpfd=$date_format;
	$phpfd=str_replace("dd", "%d", strtolower($phpfd));
	$phpfd=str_replace("mm", "%m", strtolower($phpfd));
	$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
	#$phpfd=str_replace("yy","%y", strtolower($phpfd));

$title=$LDRadiology;
#$breakfile=$root_path.'modules/radiology/'.$breakfile;   # burn added: August 29, 2007
#$breakfile  = $root_path.'modules/radiology/radiolog.php'.URL_APPEND;   # burn added: September 8, 2007
$breakfile  = $root_path.'modules/radiology/radiology/seg-radio-borrowers-list.php'.URL_APPEND;   # burn added: November 14, 2007
$breakfile .= "&noresize=1&user_origin=radio&target=radio_borrow&dept_nr=158&checkintern=1";   # burn added: November 14, 2007

$breakfile  = $root_path.'modules/laboratory/labor_test_request_pass.php'.URL_APPEND.'&target=radio_borrow&user_origin=radio&dept_nr=158';   # burn added: November 14, 2007
$thisfile=basename(__FILE__);

	
	require_once($root_path.'gui/smarty_template/smarty_care.class.php');
	$smarty = new smarty_care('common');


include_once($root_path.'include/care_api_classes/class_personell.php');
$personell_obj=new Personell;

if (isset($_GET['borrower_id']) && $_GET['borrower_id']){
	$borrower_id = $_GET['borrower_id'];
}
if (isset($_POST['borrower_id']) && $_POST['borrower_id']){
	$borrower_id = $_POST['borrower_id'];
}

#echo "seg-radio-borrow.php : _GET['borrower_id'] ='".$_GET['borrower_id']."' <br> \n";
#echo "seg-radio-borrow.php : _POST['borrower_id'] ='".$_POST['borrower_id']."' <br> \n";
#echo "seg-radio-borrow.php : borrower_id ='".$borrower_id."' <br> \n";
if ($borrower_id){
	if ($personellInfo = $personell_obj->getPersonellInfo($borrower_id)){
#echo "seg-radio-borrow.php : personell_obj->sql ='".$personell_obj->sql."' <br> \n";
#echo "seg-radio-borrow.php : personellInfo['pid'] ='".$personellInfo['pid']."' <br> \n";
#echo "seg-radio-borrow.php :: personellInfo : <br> \n"; print_r($personellInfo); echo "<br>\n";
		$dept_name = $personellInfo['dept_name'];
		$pid=$personellInfo['pid'];
	}else{
		echo "<em class='warn'> No informatin of employment found. <br> \n Sorry but the page cannot be displayed!</em>";
		exit();	
	}
}else{
	echo '<em class="warn">Sorry but the page cannot be displayed! <br> Invalid Personnel ID!</em>';
	exit();
}

include_once($root_path.'include/care_api_classes/class_person.php');
$person_obj=new Person;

if ($pid){
	if (!($basicInfo=$person_obj->getAllInfoArray($pid))){
		echo '<em class="warn"> Sorry but the page cannot be displayed!</em>';
		exit();
	}
#	echo "basicInfo : <br> \n"; print_r($basicInfo); echo "<br>\n";
	extract($basicInfo);
}else{
	echo '<em class="warn">Sorry but the page cannot be displayed! <br> Invalid HRN!</em>';
	exit();
}
#echo "seg-radio-patient.php : basicInfo='".$basicInfo."' <br> \n";			
#echo "seg-radio-patient.php : basicInfo : <br> \n"; print_r($basicInfo); echo" <br> \n";			

#echo "seg-radio-patient.php : mode='".$mode."' <br> \n";			
#echo "seg-radio-patient.php : refNoInfo : "; print_r($refNoInfo); echo " <br><br> \n";

	# Create radiology object
require_once($root_path.'include/care_api_classes/class_radiology.php');
$objRadio = new SegRadio;

	if ($borrower_id){
		$recordBorrowObj = $objRadio->getBorrowerBorrowedFilms($borrower_id);
		if (is_object($recordBorrowObj)){
#echo "seg-radio-borrow.php :: lastestRecordBorrowInfo : <br> \n"; print_r($lastestRecordBorrowInfo); echo "<br>\n";
		}else{
#			echo '<em class="warn"> Sorry but the page cannot be displayed!</em>';
#			exit();
		}
	}else{
		echo '<em class="warn">Sorry but the page cannot be displayed! <br> Invalid Batch Number!</em>';
		exit();
	}
#echo "seg-radio-borrow.php :: recordBorrowObj='".$recordBorrowObj."' <br> \n";

#echo $objRadio->sql;

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"$LDRadiology::Borrower's Records");

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

	# CLOSE button for pop-ups
#$smarty->assign('breakfile','javascript:window.parent.pSearchClose();');
$smarty->assign('breakfile',$breakfile);
$smarty->assign('pbBack','');


 # Window bar title
# $smarty->assign('sWindowTitle',"$LDRadiology::$LDDiagnosticTest");
 $smarty->assign('sWindowTitle',"$LDRadiology::Borrower's Records");

 # Assign Body Onload javascript code
# $onLoadJS='onLoad="preSet();"';
 #echo "onLoadJS = ".$onLoadJS;
 $smarty->assign('sOnLoadJs',$onLoadJS);

 # Collect extra javascript code
 ob_start();
?>
<script type="text/javascript" language="javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript">
	function printRadioBorrowerReport(){
		
		var w=window.screen.width;
		var h=window.screen.height;
		var ww=500;
		var wh=500;
//		var pid=$F('pid');
//		var borrower_id=$F('borrower_id');
		var pid=document.getElementById('pid').value;
		var borrower_id=document.getElementById('borrower_id').value;
//		var seg_URL_APPEND=document.getElementById('seg_URL_APPEND').value;
		var rpath=document.getElementById('rpath').value;
		
//		var batch_nr=$F('batch_nr');
//		var rpath=$F('rpath');
//		var seg_URL_APPEND=$F('seg_URL_APPEND');
		
//		alert("printRadioReport :: pid = '"+pid+"' \nborrower_id= '"+borrower_id+"'");
//		urlholder=rpath+"modules/radiology/certificates/seg-radio-borrower-report-pdf.php"+seg_URL_APPEND+"&pid="+pid+"&borrower_id="+borrower_id+"&batch_nr="+batch_nr;
		urlholder=rpath+"modules/radiology/certificates/seg-radio-borrower-report-pdf.php?pid="+pid+"&borrower_id="+borrower_id;
//		alert("printRadioReport :: urlholder = '"+urlholder+"'");
//		var fso = new ActiveXObject("Scripting.FileSystemObject");
//		fileBool = fso.FileExists(rpath+"radiology/modules/certificates/seg-radio-report-pdf.php");
//		alert("printRadioReport :: urlholder = '"+urlholder+"' \nfileBool = '"+fileBool+"'");
//		alert("printRadioReport :: ROENTGENOLOGICAL REPORT in pdf format");

		if (window.showModalDialog){  //for IE
			window.showModalDialog(urlholder,"width=" + ww + ",height=" + wh + ",menubar=no,resizable=yes,scrollbars=yes,dependent=yes");
		}else{
			
//			window.open("createCampus.php?i="+id,"createCampus","modal, width=480,height=320,menubar=no,resizable=no,scrollbars=no");
			popWindowEditFinding=window.open(urlholder,"Print Report","width=" + ww + ",height=" + wh + ",top=150, left=200, menubar=no,resizable=yes,scrollbars=yes,dependent=yes");
			//window.popWindowEditFinding.moveTo((w/2)+80,(h/2)-(wh/2));
		}
	}//end of function printRadioBorrowerReport
</script>

<?php
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);


			# burn added : March 26, 2007
			if($date_birth){
				$sBdayBuffer = @formatDate2Local($date_birth,$date_format);			
				if (!($age = $person_obj->getAge($sBdayBuffer))){
					$age = '';
					$sBdayBuffer = 'Not Available';
				}else{
					$smarty->assign('sAge','<span class="vi_data">'.$age.' </span> year(s) old');
				}
			}
	if ($sex=='f'){
		$gender = "female";
	}else if($sex=='m'){
		$gender = "male";	
	}
	$sAddress = trim($street_name);
	if (!empty($sAddress) && !empty($brgy_name))
		$sAddress= trim($sAddress.", ".$brgy_name);
	else
		$sAddress = trim($sAddress." ".$brgy_name);
	if (!empty($sAddress) && !empty($mun_name))
		$sAddress= trim($sAddress.", ".$mun_name);
	else
		$sAddress = trim($sAddress." ".$mun_name);
	if (!empty($zipcode))
		$sAddress= trim($sAddress." ".$zipcode);
	if (!empty($sAddress) && !empty($prov_name))
		$sAddress= trim($sAddress.", ".$prov_name);
	else
		$sAddress = trim($sAddress." ".$prov_name);

$smarty->assign('sPanelHeader','Roentgenological Account of :: '.$name_last.', '.$name_first.' <span style="font-style:italic;">'.$name_middle.'</span> ');
#$smarty->assign('sPID',$pid.'<input type="hidden" name="pid" id="pid" value="'.($pid? $pid:"0").'">');
$smarty->assign('sPersonnelID',$borrower_id.'<input type="hidden" name="borrower_id" id="borrower_id" value="'.($borrower_id? $borrower_id:"0").'" '.
											'<input type="hidden" name="pid" id="pid" value="'.($pid? $pid:"0").'">');
$smarty->assign('sDeptName',$dept_name);
$smarty->assign('sBirthdate',$sBdayBuffer);
$smarty->assign('sGender',$gender);
$smarty->assign('sAddress',trim($sAddress));

#$smarty->assign('sPrintIcon',"<img src=".$root_path."images/print_icon_red.gif onClick='printRadioBorrowerReport()' alt='Print' border=0 style='cursor:pointer'>");
$smarty->assign('sPrintIcon','<img '.createLDImgSrc($root_path,'viewpdf.gif','0','center').' alt="View PDF" onClick="printRadioBorrowerReport()" style="cursor:pointer">');

if (is_object($recordBorrowObj)){
	$sTemp = '';
	$smarty->assign('sPanelHeadersBorrowRecordHistory','List of Borrowed Radiological Materials');
	ob_start();
	$myCount=1;
	$totalGrossPrice=0;
	while($recordHistory=$recordBorrowObj->FetchRow()){
			# FORMATTING of Date Borrowed
		$date_borrowed = $recordHistory['date_borrowed'];
		if (($date_borrowed!='0000-00-00')  && ($date_borrowed!=""))
			$date_borrowed = @formatDate2Local($date_borrowed,$date_format);
		else
			$date_borrowed='';
		$patient_name=$recordHistory['name_last'].', '.$recordHistory['name_first'];
		if (!empty($recordHistory['name_middle'])){
			$patient_name .= ' <font style="font-style:italic; color:#FF0000">'.$recordHistory['name_middle'].'</font>';
		}
		echo'
		<tr>
			<td align="left">'.$myCount++.'</td>
			<td align="left">'.$recordHistory['rid'].'</td>
			<td align="left">'.$recordHistory['refno'].'</td>
			<td align="left">'.$recordHistory['batch_nr'].'</td>
			<td align="left">'.$date_borrowed.'</td>
			<td align="left">'.$patient_name.'</td>
			<td align="left">'.$recordHistory['service_code'].'</td>
			<td align="right" style="font:\'Courier New\',Courier, mono;">'.
				number_format($recordHistory['price_gross'], 2, '.', ',').
			'</td>
		</tr>'."\n";
		#echo "recordHistory : <br>\n "; print_r($recordHistory); echo"<br>\n";
		$totalGrossPrice += $recordHistory['price_gross'];
	}
	$sTemp = ob_get_contents();
	ob_end_clean();
	
	$smarty->assign('sBorrowRecordHistory',$sTemp);
	$smarty->assign('sTotalGrossPrice','Php '.number_format($totalGrossPrice, 2, '.', ','));
	
	$penalty = $totalGrossPrice * 0.30;
	$smarty->assign('sPenalty','Php '.number_format($penalty, 2, '.', ','));
}# end of if stmt 'if (is_object($recordBorrowObj))'

 $smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.'" method="POST" name="inputform" onSubmit="return false;">');
 $smarty->assign('sFormEnd','</form>');

ob_start();
$sTemp='';
?>
<!--
	<input type="hidden" name="submit" value="1">
	<input type="hidden" name="sid" id="sid" value="<?php echo $sid?>">
	<input type="hidden" name="lang" id="lang" value="<?php echo $lang?>">
	<input type="hidden" name="cat" value="<?php echo $cat?>">
	<input type="hidden" name="userck" value="<?php echo $userck?>">  
-->
<!--  
	<input type="hidden" name="mode" id="modeval" value="<?php if($saveok) echo "update"; else echo "save"; ?>">
-->
<!--
	<input type="hidden" name="encoder" value="<?php echo  str_replace(" ","+",$HTTP_COOKIES_VARS[$local_user.$sid])?>">
	<input type="hidden" name="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>">
	<input type="hidden" name="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>">
	<input type="hidden" name="lockflag" value="<?php echo  $lockflag?>">
	<input type="hidden" name="update" value="<?php if($saveok) echo "1"; else echo $update;?>">

	<input type="hidden" name="is_cash" id="is_cash" value="<?=$is_cash?>" >
	<input type="hidden" name="encounter_nr" id="encounter_nr" value="<?=$encounter_nr?>">
	<input type="hidden" name="mode" id="mode" value="<?=$mode?$mode:'save'?>">
	<input type="hidden" name="popUp" id="popUp" value="<?=$popUp?$popUp:'0'?>">
	<input type="hidden" name="hasPaid" id="hasPaid" value="<?=$hasPaid?$hasPaid:'0'?>">
	<input type="hidden" name="view_from" id="view_from" value="<?=$view_from?$view_from:''?>">
	<input type="hidden" name="encoder_id" id="encoder_id" value="<?php echo $HTTP_SESSION_VARS['sess_login_personell_nr']; ?>">

	<input type="hidden" name="skey" id="skey" value="*"> 
	<input type="hidden" name="smode" id="smode" value="<?=$mode?$mode:'save'?>">
	<input type="hidden" name="starget" id="starget" value="<?php echo $target; ?>">
	<input type="hidden" name="thisfile" id="thisfile" value="<?php echo $thisfile; ?>">
	<input type="hidden" name="pgx" id="pgx" value="<?php echo $pgx; ?>">
	<input type="hidden" name="oitem" id="oitem" value="<?= $oitem? $oitem:'batch_nr' ?>">
	<input type="hidden" name="odir" id="odir" value="<?= $odir? $odir:'ASC' ?>">
-->
	<input type="hidden" name="rpath" id="rpath" value="<?php echo $root_path; ?>">
<?php 

$sTemp = ob_get_contents();
ob_end_clean();

$smarty->assign('sHiddenInputs',$sTemp);
#$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','center').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');
#$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','center').' alt="'.$LDBack2Menu.'" onclick="$(\'mode\').value=\'cancel\';alert($F(\'mode\'));" onsubmit="return false;" style="cursor:pointer">');
#$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','center').' alt="'.$LDBack2Menu.'" onclick="$(\'mode\').value=\'cancel\';alert($F(\'mode\'));" style="cursor:pointer">');
if (($mode=="update") && ($popUp!='1')){
	$sBreakImg ='cancel.gif';
	$smarty->assign('sBreakButton','<input type="image" '.createLDImgSrc($root_path,$sBreakImg,'0','center').' align="center" alt="'.$LDBack2Menu.'" onclick="$(\'mode\').value=\'cancel\';" style="cursor:pointer">');
}elseif ($popUp!='1'){
	$sBreakImg ='close2.gif';	
	$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','center').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');
}
$smarty->assign('sContinueButton','<input type="image" name="btnSubmit" id="btnSubmit" src="'.$root_path.'images/btn_submitrequest.gif" align="center">');

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','radiology/radio-borrower-record.tpl');
$smarty->display('common/mainframe.tpl');
?>