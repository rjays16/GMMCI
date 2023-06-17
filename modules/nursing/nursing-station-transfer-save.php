<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org,
*
* See the file "copy_notice.txt" for the licence notice
*/

define('LANG_FILE','prompt.php');
define('NO_2LEVEL_CHK',1);
$local_user='ck_pflege_user';
require_once($root_path.'include/inc_front_chain_lang.php');

if(empty($HTTP_COOKIE_VARS[$local_user.$sid])){
		$edit=0;
	include($root_path."language/".$lang."/lang_".$lang."_".LANG_FILE);
}
/**
* Set default values if not available from url
*/
if (!isset($station)||empty($station)) { $station=$HTTP_SESSION_VARS['sess_nursing_station'];} # Default station must be set here !!
if(!isset($pday)||empty($pday)) $pday=date('d');
if(!isset($pmonth)||empty($pmonth)) $pmonth=date('m');
if(!isset($pyear)||empty($pyear)) $pyear=date('Y');
$s_date=$pyear.'-'.$pmonth.'-'.$pday;
if($s_date==date('Y-m-d')) $is_today=true;
	else $is_today=false;

$fileappend="&edit=1&mode=&pday=$pday&pmonth=$pmonth&pyear=$pyear&station=$station&ward_nr=$ward_nr";
#echo "fileappend = ".$fileappend;
$breakfile="location:nursing-station.php".URL_APPEND.$fileappend;
$forwardfile="location:nursing-station.php".URL_REDIRECT_APPEND.$fileappend;
# Create ward object
require_once($root_path.'include/care_api_classes/class_ward.php');
$ward_obj= new Ward;

#added by VAN 01-23-08
require_once($root_path.'include/care_api_classes/class_encounter.php');
$enc_obj= new Encounter;

if (!$ward_nr)
	$ward_nr='';

$source = 'NURSING';

	if ($_GET['datefrom']){
		$date = date('Y-m-d',strtotime($_GET['datefrom']));
	}else
		$date = date('Y-m-d');

	if ($_GET['timefrom']){
		$time = $_GET['timefrom'].":00 ".$_GET['selAMPM'];
		$time = date('H:i:s',strtotime($time));
	}else
		$time = date('H:i:s');
	#-------------

$enc_obj->startTrans();
if(isset($mode)&&($mode=='transferbed'||$mode=='transferward')){

	#$date=date('Y-m-d');
	#$time=date('H:i:s');
	#edited by VAN 06-08-10

	# Determine the reason for temporary discharge
	if($mode=='transferward'){
		$dis_type=4; # transfer of ward
	}else{
		$dis_type=6; # transfer of bed
	}


	# First, discharge the patient from the current assignment
	#if($ward_obj->DischargeFromWard($pn,$dis_type,$date,$time)){
	#edited by VAN 01-23-08
	if($ok=$enc_obj->DischargeFromWard($pn,$dis_type,$date,$time)){
		switch($mode){
				case 'transferbed' :
			{
				# Assign to ward,room and bed
				if($ok=$ward_obj->AdmitInWard($pn,$ward_nr,$rm,$bd,$source,$date,$time)){

				#edited by VAN 01-23-08
				#if($enc_obj->AdmitInWard($pn,$ward_nr,$rm,$bd)){
					#echo "ok";
					#edited by VAN 02-06-08
					#$ward_obj->setAdmittedInWard($pn,$ward_nr,$rm,$bd,1);
					if($ok) $ok=$ward_obj->setAdmittedInWard($pn,$ward_nr,$rm,$bd,1,1);

					#added by VAN 06-18-08
					$roomInfo = $ward_obj->getRoomRate($rm, $ward_nr);
					$rate = $roomInfo['room_rate'];

					$location = $enc_obj->getLatestLocNr($pn);
					$loc_enc_nr = $location['nr'];

					$enc_obj->isExistInLocationRate($loc_enc_nr,$pn);
					$rec = $enc_obj->count;

					if ($rec){
						$enc_obj->setTransferredLocation($loc_enc_nr,$pn);
						$enc_obj->setPatientRoomRate($loc_enc_nr,$pn,$ward_nr,$rm,$bd,$rate,'');
					}else{
						$enc_obj->setPatientRoomRate($loc_enc_nr,$pn,$ward_nr,$rm,$bd,$rate,'');
					}
					#-------------------------

					#edited by VAN 01-23-08
					#$enc_obj->setAdmittedInWard($pn,$ward_nr,$rm,$bd);
					#$enc_obj->setAdmittedInWard($pn,$ward_nr,$rm,$bd,1);
					if (!$ok) $db->FailTrans();
						//echo '<script type="javascript">alert("Patient is assigned to bed")</script>';
						$enc_obj->CompleteTrans();

					header($forwardfile);
					exit;
				}
				break;
			}
			case 'transferward':
			{
				#commented by VAN 01-25-08
				#if($ward_obj->ReplaceWard($pn,$trwd)){
				/*
				if($enc_obj->->ReplaceWard($pn,$trwd)){
					header($forwardfile);
					exit;
				}
				*/

				#edited by VAN 01-23-08
				if($ok=$ward_obj->AdmitInWard($pn,$ward_nr,$rm,$bd,$source,$date,$time)){
					#edited by VAN 02-06-08
					#$ward_obj->setAdmittedInWard($pn,$ward_nr,$rm,$bd,0);
					if($ok) $ok=$ward_obj->setAdmittedInWard($pn,$ward_nr,$rm,$bd,0,1);

					#added by VAN 06-18-08
					$roomInfo = $ward_obj->getRoomRate($rm, $ward_nr);
					$rate = $roomInfo['room_rate'];

					$location = $enc_obj->getLatestLocNr($pn);
					$loc_enc_nr = $location['nr'];

					$enc_obj->isExistInLocationRate($loc_enc_nr,$pn);
					$rec = $enc_obj->count;

					if ($rec){
						$enc_obj->setTransferredLocation($loc_enc_nr,$pn);
						$enc_obj->setPatientRoomRate($loc_enc_nr,$pn,$ward_nr,$rm,$bd,$rate,'');
					}else{
						$enc_obj->setPatientRoomRate($loc_enc_nr,$pn,$ward_nr,$rm,$bd,$rate,'');
					}
					#-------------------------
					if (!$ok) $db->FailTrans();
						$enc_obj->CompleteTrans();

					header($forwardfile);
					exit;
				}
				break;
			}
		}
	}
}elseif($mode=='correct'){

				$ok=$enc_obj->deleteRecentRoomLocationIfCorrection($pn);
				if($ok=$ward_obj->AdmitInWard($pn,$ward_nr,$rm,$bd,$source,$date,$time)){
					#edited by VAN 02-06-08
					#$ward_obj->setAdmittedInWard($pn,$ward_nr,$rm,$bd,0);
					if($ok) $ok=$ward_obj->setAdmittedInWard($pn,$ward_nr,$rm,$bd,0,1);

					#added by VAN 06-18-08
					$roomInfo = $ward_obj->getRoomRate($rm, $ward_nr);
					$rate = $roomInfo['room_rate'];

					$location = $enc_obj->getLatestLocNr($pn);
					$loc_enc_nr = $location['nr'];

					$enc_obj->isExistInLocationRate($loc_enc_nr,$pn);
					$rec = $enc_obj->count;

					if ($rec){
						$enc_obj->setTransferredLocation($loc_enc_nr,$pn);
						$enc_obj->setPatientRoomRate($loc_enc_nr,$pn,$ward_nr,$rm,$bd,$rate,'');
					}else{
						$enc_obj->setPatientRoomRate($loc_enc_nr,$pn,$ward_nr,$rm,$bd,$rate,'');
					}
				}
					#-------------------------
	if (!$ok) $db->FailTrans();
		$enc_obj->CompleteTrans();

	header($forwardfile);
	exit;
}elseif ($mode=='assignBedwaiting'){

	#$ok=$enc_obj->updateassignRoomwaiting($pn, $ward_nr, $rm, $source);
	#$ok=$enc_obj->updateassignBedwaiting($pn, $ward_nr, $bd, $source);
	$ok=$enc_obj->updateassignWardwaiting($pn, $ward_nr, $source, $date, $time);
	$ok=$enc_obj->updateassignRoomwaiting($pn, $ward_nr, $rm, $source, $date, $time,1);
	$ok=$enc_obj->updateassignBedwaiting($pn, $ward_nr, $bd, $source, $date, $time,1);
	if($ok) $ok=$ward_obj->setAdmittedInWard($pn,$ward_nr,$rm,$bd,0,1);

	$roomInfo = $ward_obj->getRoomRate($rm, $ward_nr);
	$rate = $roomInfo['room_rate'];

	$location = $enc_obj->getLatestLocNr($pn);
	$loc_enc_nr = $location['nr'];

	if($ok) $ok=$enc_obj->updateLocateRatewaiting($pn,$ward_nr,$rm,$bd,$rate,$loc_enc_nr);
	#echo $enc_obj->sql;
	if (!$ok) $db->FailTrans();
		$enc_obj->CompleteTrans();

	header($forwardfile);
	exit;
/*}elseif ($mode=='assignWardwaiting'){

	$ok=$enc_obj->updateassignWardwaiting($pn, $ward_nr, $source);
	$ok=$enc_obj->updateassignRoomwaiting($pn, $ward_nr, $rm, $source,1);
	$ok=$enc_obj->updateassignBedwaiting($pn, $ward_nr, $bd, $source,1);
	if($ok) $ok=$ward_obj->setAdmittedInWard($pn,$ward_nr,$rm,$bd,0,1);

	$roomInfo = $ward_obj->getRoomRate($rm, $ward_nr);
	$rate = $roomInfo['room_rate'];

	$location = $enc_obj->getLatestLocNr($pn);
	$loc_enc_nr = $location['nr'];

	if($ok) $ok=$enc_obj->updateLocateRatewaiting($pn,$ward_nr,$rm,$bd,$rate,$loc_enc_nr);
	#echo $enc_obj->sql;
	if (!$ok) $db->FailTrans();
		$enc_obj->CompleteTrans();

	header($forwardfile);
	exit;*/
}else{
	if (!$ok) $db->FailTrans();
			$enc_obj->CompleteTrans();
	header($forwardfile);
	exit;
}


?>
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 3.0//EN" "html.dtd">
<?php html_rtl($lang); ?>
<HEAD>
<?php

echo setCharSet();

require($root_path.'include/inc_js_gethelp.php');
require($root_path.'include/inc_css_a_hilitebu.php');
?>

<style type="text/css" name="s2">
td.vn { font-family:verdana,arial; color:#000088; font-size:10}

</style>
</HEAD>

<BODY bgcolor=<?php echo $cfg['body_bgcolor']; ?> topmargin=0 leftmargin=0 marginwidth=0 marginheight=0
<?php if (!$cfg['dhtml']){ echo 'link='.$cfg['idx_txtcolor'].' alink='.$cfg['body_alink'].' vlink='.$cfg['idx_txtcolor']; } ?>>

<table border=0>
	<tr>
		<td><img <?php 	echo createMascot($root_path,'mascot2_r.gif','0'); ?>></td>
		<td><FONT SIZE=3  FACE="Arial" color="maroon"><?php 	echo $LDErrorOccured.'<br>'.$LDTryOrNotifyEDP; ?></td>
	</tr>
</table>

<p>
<?php
require($root_path.'include/inc_load_copyrite.php');
?>
</BODY>
</HTML>
