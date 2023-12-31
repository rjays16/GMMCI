<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
/**
* CARE2X Integrated Hospital Information System version deployment 1.1 (mysql) 2004-01-11
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* , elpidio@care2x.org
*
* See the file "copy_notice.txt" for the licence notice
*/
//$db->debug=1;
#print_r($HTTP_COOKIE_VARS);
define('SHOW_DOC_2',1);  # Define to 1 to  show the 2nd doctor-on-duty
define('DOC_CHANGE_TIME','7.30'); # Define the time when the doc-on-duty will change in 24 hours H.M format (eg. 3 PM = 15.00, 12 PM = 0.00)

$lang_tables[]='prompt.php';
define('LANG_FILE','nursing.php');
//define('NO_2LEVEL_CHK',1);
$local_user='ck_pflege_user';
define('NO_2LEVEL_CHK',1);
require($root_path.'include/inc_front_chain_lang.php');

#added by VAN 06-18-08
require_once($root_path.'include/care_api_classes/class_encounter.php');
$enc_obj= new Encounter;
#----------------

require_once($root_path.'modules/nursing/ajax/nursing-ward-common.php');



if(empty($HTTP_COOKIE_VARS[$local_user.$sid])){
		$edit=0;
	include($root_path."language/".$lang."/lang_".$lang."_".LANG_FILE);
}
#echo "ward : nursing station = ".$ward_nr." , ".$station;
# Set default values if not available from url
if (!isset($station)||empty($station)) { $station=$HTTP_SESSION_VARS['sess_nursing_station'];} # Default station must be set here !!
if(!isset($pday)||empty($pday)) $pday=date('d');
if(!isset($pmonth)||empty($pmonth)) $pmonth=date('m');
if(!isset($pyear)||empty($pyear)) $pyear=date('Y');
$s_date=$pyear.'-'.$pmonth.'-'.$pday;
if($s_date==date('Y-m-d')) $is_today=true;
	else $is_today=false;

if(!isset($mode)) $mode='';

#$breakfile='nursing.php'.URL_APPEND; # Set default breakfile
#edited by VAN 04-08-08
#$breakfile = 'nursing-wardList.php'.URL_APPEND; # Set default breakfile
$breakfile = 'nursing-wardList.php'.URL_APPEND.'&key='.$_GET['key'].'&pagekey='.$_GET['pagekey']; # Set default breakfile


$thisfile=basename(__FILE__);

if(isset($retpath)){
	switch($retpath)
	{
		case 'quick': $breakfile='nursing-schnellsicht.php'.URL_APPEND;
							break;
		case 'ward_mng':
							#$breakfile='nursing-station-info.php'.URL_APPEND.'&ward_nr='.$ward_nr.'&mode=show';
							$breakfile='nursing-station-info.php'.URL_APPEND;
							break;
		case 'search_patient': $breakfile='nursing-patient-such-start.php'.URL_APPEND;
	}
}

# Create ward object
require_once($root_path.'include/care_api_classes/class_ward.php');
$ward_obj= new Ward;

$ward_nr = $_GET['ward_nr'];
# Load date formatter
require_once($root_path.'include/inc_date_format_functions.php');
require_once($root_path.'global_conf/inc_remoteservers_conf.php');

#echo "ward, date = ".$ward_nr." - ".$is_today;
#$is_today=1;

if(($mode=='')||($mode=='fresh')){
	if($ward_info=&$ward_obj->getWardInfo($ward_nr)){
		#echo "<br>ward = ".$ward_obj->sql;
#		$room_obj=&$ward_obj->getRoomInfo($ward_nr,$ward_info['room_nr_start'],$ward_info['room_nr_end']);   # burn commented : September 27, 2007
		if ($ward_info['accomodation_type']==1)
			#CHARITY
			$area_type = 'ch';
		elseif ($ward_info['accomodation_type']==2)
			#PAYWARD
			$area_type = 'pw';
		#echo 's = '.$area_type;
		$room_obj=&$ward_obj->getRoomsData($ward_nr);
		#echo "<br>room = ".$ward_obj->sql;
		if(is_object($room_obj)) {
			$room_ok=true;
		}else{
			$room_ok=false;
		}
		# GEt the number of beds
		$nr_beds=$ward_obj->countBeds($ward_nr);
		# Get ward patients
#echo "nursing-station.php : is_today = '".$is_today."' <br> \n";
		if($is_today) $patients_obj=&$ward_obj->getDayWardOccupants($ward_nr);
			else $patients_obj=&$ward_obj->getDayWardOccupants($ward_nr,$s_date);
#echo "nursing-station.php : (patients_obj) ward_obj->sql = '".$ward_obj->sql."' <br> \n";
		//echo $ward_obj->getLastQuery();
		//echo $ward_obj->LastRecordCount();

		if(is_object($patients_obj)){
			# Prepare patients data into array matrix
			while($buf=$patients_obj->FetchRow()){
				$patient[$buf['room_nr']][$buf['bed_nr']]=$buf;
			}
			$patients_ok=true;
			$occup='ja';
		}else{
			$patients_ok=false;
		}

		$ward_ok=true;

		# Create the waiting inpatients' list
		$wnr=(isset($w_waitlist)&&$w_waitlist) ? 0 : $ward_nr;
		$waitlist=$ward_obj->createWaitingInpatientList($wnr);
#echo"nursing-station.php : (waitlist) ward_obj->sql ='".$ward_obj->sql."' <br> \n";
		$waitlist_count=$ward_obj->LastRecordCount();

		# Get the doctor's on duty information
		#### Start of routine to fetch doctors on duty
		$elem='duty_1_pnr';
		if(SHOW_DOC_2) $elem.=',duty_2_pnr';

		# Create personnel object
		include_once($root_path.'include/care_api_classes/class_personell.php');
		$pers_obj=new Personell;

		if($result=$pers_obj->getDOCDutyplan($ward_info['dept_nr'],$pyear,$pmonth,$elem)){
			$duty1=&unserialize($result['duty_1_pnr']);
			if(SHOW_DOC_2) $duty2=&unserialize($result['duty_2_pnr']);
					//echo $sql."<br>";
		}
		//echo $sql;

		# Adjust the day index
		$offset_day=$pday-1;
		# Consider the early morning hours to belong to the past day
		if(date('H.i')<DOC_CHANGE_TIME) $offset_day--;
		if($pnr1=$duty1['ha'.$offset_day]){
			$person1=&$pers_obj->getPersonellInfo($pnr1);
		}
		if(SHOW_DOC_2 && ($pnr2=$duty2['hr'.$offset_day])){
			$person2=&$pers_obj->getPersonellInfo($pnr2);
		}
		#### End of routine to fetch doctors on duty
	}else{
		$ward_ok=false;
	}
	#echo "<br>ward = ".$ward_obj->sql;
}elseif($mode=='newdata'){
/*
echo "nursing-station.php : mode = '".$mode."' <br> \n";
echo "nursing-station.php : pn = '".$pn."' <br> \n";
echo "nursing-station.php : ward_nr = '".$ward_nr."' <br> \n";
echo "nursing-station.php : rm = '".$rm."' <br> \n";
echo "nursing-station.php : bd = '".$bd."' <br> \n";
*/
	if(($pn=='lock')||($pn=='unlock')){
		//$db->debug=true;
		if($pn=='lock') $ward_obj->closeBed($ward_nr,$rm,$bd);
			else $ward_obj->openBed($ward_nr,$rm,$bd);

		//header("location:nursing-station.php".URL_REDIRECT_APPEND."&edit=1&mode=&pday=$pday&pmonth=$pmonth&pyear=$pyear&station=$station&ward_nr=$ward_nr");
		//exit;
	}else{
		if($ward_obj->AdmitInWard($pn,$ward_nr,$rm,$bd)){
#echo "nursing-station.php : A (AdmitInWard) ward_obj->sql = '".$ward_obj->sql."'<br> \n";
			//echo "ok";
			#edited by VAN 02-06-08
			#$ward_obj->setAdmittedInWard($pn,$ward_nr,$rm,$bd);
			$ward_obj->setAdmittedInWard($pn,$ward_nr,$rm,$bd,1,1);

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

#			echo "nursing-station.php : (setAdmittedInWard) ward_obj->sql = '".$ward_obj->sql."'<br> \n";
		}
#echo "nursing-station.php : B (AdmitInWard or setAdmittedInWard?) ward_obj->sql = '".$ward_obj->sql."'<br> \n";
		//header("location:nursing-station.php".URL_REDIRECT_APPEND."&edit=1&mode=&pday=$pday&pmonth=$pmonth&pyear=$pyear&station=$station&ward_nr=$ward_nr");
		//exit;
	}
	header("location:nursing-station.php".URL_REDIRECT_APPEND."&edit=1&mode=&pday=$pday&pmonth=$pmonth&pyear=$pyear&station=$station&ward_nr=$ward_nr");
	exit;
}
/*
echo "nursing-station.php : ward_info :<br> \n"; print_r($ward_info); echo"<br>\n";
#echo "nursing-station.php : ward_info['dept_name'] ='".$ward_info['dept_name']."'<br>\n";
#echo "nursing-station.php : ward_info['accomodation_type'] ='".$ward_info['accomodation_type']."'<br>\n";
#echo "nursing-station.php : ward_info['name'] ='".$ward_info['name']."'<br>\n";
echo "nursing-station.php : patient = '".$patient."' <br> \n";
echo "nursing-station.php : patient :<br> \n"; print_r($patient); echo"<br>\n";
*/
# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('nursing');

if ($ward_info['accomodation_type']==1){
	$add_dept_name = "\n<br>\n &nbsp;".$ward_info['dept_name'];
}
# Title in toolbar
 $smarty->assign('sToolbarTitle', "$LDStation  ".$ward_info['name']." $LDOccupancy (".formatDate2Local($s_date,$date_format,'','',$null='').")".$add_dept_name);

	# hide back button
 $smarty->assign('pbBack',FALSE);

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('nursing_station.php','$mode','$occup','$station','$LDStation')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$LDStation  ".$ward_info['name']." $LDOccupancy (".formatDate2Local($s_date,$date_format,'','',$null='').")");

 # Collect extra javascript code

#echo "key = ".$_GET['key'];
#echo "<br>pagekey = ".$_GET['pagekey'];

 ob_start();


	$xajax->printJavascript($root_path.'classes/xajax');
 
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


<script language="javascript">
<!--
	var urlholder;

// edited by VAN 01-29-08
//function getinfo(pn){
function getinfo(pn, pid){

<?php /* if($edit)*/
	/*
	{ echo '
	urlholder="nursing-station-patientdaten.php'.URL_REDIRECT_APPEND;
	echo '&pn=" + pn + "';
	echo "&pday=$pday&pmonth=$pmonth&pyear=$pyear&edit=$edit&station=".$ward_info['name'];
	echo '";';
	echo '
	patientwin=window.open(urlholder,pn,"width=700,height=600,menubar=no,resizable=yes,scrollbars=yes");
	';
	}
	*/
	{ echo '
	urlholder="nursing-station-patientdaten.php'.URL_REDIRECT_APPEND;
	echo '&pn=" + pn + "&pid=" + pid + "';
	echo "&pday=$pday&pmonth=$pmonth&pyear=$pyear&edit=$edit&station=".$ward_info['name'];
	echo '";';
	echo '
	patientwin=window.open(urlholder,pn,"width=700,height=600,menubar=no,resizable=yes,scrollbars=yes");
	';
	}
	/*else echo '
	window.location.href=\'nursing-station-pass.php'.URL_APPEND.'&rt=pflege&edit=1&station='.$station.'\'';*/
?>

	}

function getrem(pn){
	//urlholder="nursing-station-remarks.php<?php echo URL_REDIRECT_APPEND; ?>&pn="+pn+"<?php echo "&dept_nr=$ward_nr&location_nr=$ward_nr&pday=$pday&pmonth=$pmonth&pyear=$pyear&station=$station"; ?>";
	//patientwin=window.open(urlholder,pn,"width=700,height=500,menubar=no,resizable=yes,scrollbars=yes,left=120,top=100");

	return overlib(
					OLiframeContent('<?php echo $root_path ?>modules/nursing/nursing-station-remarks.php<?php echo URL_REDIRECT_APPEND ?>&dept_nr=<?echo $ward_nr?>&location_nr=<?echo $ward_nr?>&pday=<?echo $pday?>&pmonth=<?echo $pmonth?>&pyear=<?echo $pyear?>&station=<?echo $station?>&pn='+pn,
																	850, 450, 'fGroupTray', 0, 'auto'),
																	WIDTH,850, TEXTPADDING,0, BORDER,0,
																		STICKY, SCROLL, CLOSECLICK, MODAL,
																		CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0 onClick="pSearchClose();">',
																 CAPTIONPADDING,2, CAPTION,'Notes',
																 MIDX,0, MIDY,0,
																 STATUS,'Notes');
}

function indata(room,bed)
{
	urlholder="nursing-station-bettbelegen.php<?php echo URL_REDIRECT_APPEND; ?>&rm="+room+"&bd="+bed+"<?php echo "&py=".$pyear."&pm=".$pmonth."&pd=".$pday."&tb=".str_replace("#","",$cfg['top_bgcolor'])."&tt=".str_replace("#","",$cfg['top_txtcolor'])."&bb=".str_replace("#","",$cfg['body_bgcolor'])."&d=".$cfg['dhtml']; ?>&s=<?php echo $station; ?>&wnr=<?php echo $ward_nr; ?>";
	indatawin=window.open(urlholder,"bedroom","width=700,height=450,menubar=no,resizable=yes,scrollbars=yes");
}
function release(room,bed,pid)
{
	urlholder="nursing-station-patient-release.php<?php echo URL_REDIRECT_APPEND; ?>&rm="+room+"&bd="+bed+"&pn="+pid+"<?php echo "&pyear=".$pyear."&pmonth=".$pmonth."&pday=".$pday."&tb=".str_replace("#","",$cfg['top_bgcolor'])."&tt=".str_replace("#","",$cfg['top_txtcolor'])."&bb=".str_replace("#","",$cfg['body_bgcolor'])."&d=".$cfg['dhtml']; ?>&station=<?php echo $station; ?>&ward_nr=<?php echo $ward_nr; ?>";
	//indatawin=window.open(urlholder,"bedroom","width=700,height=450,menubar=no,resizable=yes,scrollbars=yes"
	window.location.href=urlholder;
}

function moveToWaitingList(enr)
{	
	var dType = 4;
	if(confirm("Are you sure you want to move this patient to the waiting list?")){
		
		xajax_moveToWaitingList(enr);
		
	}
}

function unlock(b,r)
{
<?php
	echo '
	urlholder="nursing-station.php'.URL_REDIRECT_APPEND.'&mode=newdata&pn=unlock&rm="+r+"&bd="+b+"&pyear='.$pyear.'&pmonth='.$pmonth.'&pday='.$pday.'&station='.$station.'&ward_nr='.$ward_nr.'";
	';
?>
	if(confirm('<?php echo $LDConfirmUnlock ?>'))
	{
		window.location.replace(urlholder);
	}
}

function popinfo(l,d)
{
//	urlholder="<?php echo $root_path ?>modules/doctors/doctors-dienstplan-popinfo.php<?php echo URL_REDIRECT_APPEND ?>&nr="+l+"&dept_nr="+d+"&user=<?php echo $aufnahme_user.'"' ?>;
	urlholder="<?php echo $root_path ?>modules/doctors/doctors-dienstplan-popinfo.php<?php echo URL_REDIRECT_APPEND ?>&nr="+l+"&dept_nr="+d+"&user=<?php echo $aufnahme_user ?>";

	infowin=window.open(urlholder,"dienstinfo","width=400,height=450,menubar=no,resizable=yes,scrollbars=yes");

}

function pSearchClose() {
	cClick();  //function in 'overlibmws.js'
	//added by VAN 04-12-08
	refreshWindow();
}

function refreshWindow(){
	//alert('refresh = '+window.location.href);
	window.location.href=window.location.href;
}

function assignWaiting(pn,wd,pw, station,ward_nr)
{
	//urlholder="nursing-station-assignwaiting.php<?php echo URL_REDIRECT_APPEND ?>&waiting=1&pn="+pn+"&pat_station="+pw+"&ward_nr=<?php echo $ward_nr ?>&station=<?php echo $station ?>";
	//edited by VAN 06-07-2010
	//urlholder="nursing-station-assignwaiting.php<?php echo URL_REDIRECT_APPEND ?>&pn="+pn+"&pat_station="+pw+"&ward_nr=<?php echo $ward_nr ?>&station=<?php echo $station ?>&waiting=1";
	//asswin<?php echo $sid ?>=window.open(urlholder,"asswind<?php echo $sid ?>","width=650,height=600,menubar=no,resizable=yes,scrollbars=yes");
	return overlib(
					OLiframeContent('<?php echo $root_path ?>modules/nursing/nursing-waitlist.php<?php echo URL_REDIRECT_APPEND ?>&ptype=nursing&popUp=1&from=such&encounter_nr='+pn+'&target=search&fromnurse=1&ward='+wd+'&area_type='+pw+'&station='+station+'&ward_nr='+ward_nr,
																	855, 450, 'fGroupTray', 0, 'auto'),
																	WIDTH,855, TEXTPADDING,0, BORDER,0,
																		STICKY, SCROLL, CLOSECLICK, MODAL,
																		CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0 onClick="pSearchClose();">',
																 CAPTIONPADDING,2, CAPTION,'Patient Details',
																 MIDX,0, MIDY,0,
																 STATUS,'Patient Details');
}
function Transfer(pn,pw)
{
	//if(confirm("<?php echo $LDSureTransferPatient ?>")){
	if(confirm("Do you really have to transfer this patient?")){
		urlholder="nursing-station-transfer-select.php<?php echo URL_REDIRECT_APPEND ?>&pn="+pn+"&pat_station="+pw+"&ward_nr=<?php echo $ward_nr ?>&station=<?php echo $station ?>";
		transwin<?php echo $sid ?>=window.open(urlholder,"transwin<?php echo $sid ?>","width=650,height=600,menubar=no,resizable=yes,scrollbars=yes, left=150,top=60");
		/*return overlib(
					OLiframeContent('<?php echo $root_path ?>modules/nursing/nursing-station-transfer-select.php<?php echo URL_REDIRECT_APPEND ?>&pn='+pn+'&pat_station='+pw+'&ward_nr=<? echo $ward_nr ?>&station=<? echo $station ?>',
																	850, 450, 'fGroupTray', 0, 'auto'),
																	WIDTH,850, TEXTPADDING,0, BORDER,0,
																		STICKY, SCROLL, CLOSECLICK, MODAL,
																		CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0 onClick="pSearchClose();">',
																 CAPTIONPADDING,2, CAPTION,'Transfer Patient',
																 MIDX,0, MIDY,0,
																 STATUS,'Transfer Patient');*/
	}

}

// added by VAN 01-25-08
function getPatientAdmissionInfo(pn,area_type){
	//alert("pn = "+pn);
	//urlholder="<?php echo $root_path ?>modules/registration_admission/aufnahme_daten_zeigen.php<?php echo URL_REDIRECT_APPEND ?>&ptype=nursing&popUp=1&from=such&encounter_nr="+pn+"&target=search&fromnurse=1&area_type="+area_type;
	//modified by CHA, 05072010
	//adminwin<?php echo $sid ?>=window.open(urlholder,"adminwin<?php echo $sid ?>","width=950,height=570,menubar=no,left=60,top=55,resizable=yes,scrollbars=yes");
	//modified by VAN 08-19-2010
	return overlib(
					OLiframeContent('<?php echo $root_path ?>modules/registration_admission/aufnahme_daten_zeigen.php<?php echo URL_REDIRECT_APPEND ?>&ptype=nursing&popUp=1&from=such&encounter_nr='+pn+'&target=search&fromnurse=1&area_type='+area_type,
																	850, 450, 'fGroupTray', 0, 'auto'),
																	WIDTH,850, TEXTPADDING,0, BORDER,0,
																		STICKY, SCROLL, CLOSECLICK, MODAL,
																		CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0 onClick="">',
																 CAPTIONPADDING,2, CAPTION,'Patient Admission Data',
																 MIDX,0, MIDY,0,
																 STATUS,'Patient Admission Data');
}

function getPatientInfo(pid){
	//alert("pid = "+pid);
	urlholder="<?php echo $root_path ?>modules/registration_admission/patient_register_show.php<?php echo URL_REDIRECT_APPEND ?>&pid="+pid+"&fromnurse=1";
	patwin<?php echo $sid ?>=window.open(urlholder,"patwin<?php echo $sid ?>","width=850,height=570,menubar=no,resizable=yes,scrollbars=yes");
}


<?php
require($root_path.'include/inc_checkdate_lang.php');
?>
// -->
</script>

<script language="javascript" src="<?php echo $root_path; ?>js/setdatetime.js"></script>

<script language="javascript" src="<?php echo $root_path; ?>js/checkdate.js"></script>

<style type="text/css" name="s2">
td.vn { font-family:verdana,arial; color:#000088; font-size:10}
</style>

<?php

$sTemp = ob_get_contents();

ob_end_clean();

$smarty->append('JavaScript',$sTemp);

if(($occup=='template')&&(!$mode)&&(!isset($list)||!$list)){

	$smarty->assign('sWarningPrompt'.$LDNoListYet.'<br>
			 <form action="nursing-station.php" method=post>
			<input type="hidden" name="sid" value="'.$sid.'">
				<input type="hidden" name="lang" value="'.$lang.'">
				<input type="hidden" name="pyear" value="'.$pyear.'">
			<input type="hidden" name="pmonth" value="'.$pmonth.'">
				<input type="hidden" name="pday" value="'.$pday.'">
			<input type="hidden" name="station" value="'.$station.'">
			<input type="hidden" name="ward_nr" value="'.$ward_nr.'">
			<input type="hidden" name="mode" value="getlast">
			<input type="hidden" name="c" value="1">
			<input type="hidden" name="edit" value="'.$edit.'">
				<input type="submit" value="'.$LDShowLastList.'" >
			</form>');

}elseif($mode=="getlast"){

	$sWarnBuffer = $LDLastList;
			if($c>2) $sWarnBuffer = $sWarnBuffer.'<font color=red><b>'.$LDNotToday.'</b></font><br>'.str_replace("~nr~",$c,$LDListFrom);
				else $sWarnBuffer = $sWarnBuffer.'<font color=red><b>'.$LDFromYesterday.'</b></font><br>
				';
			$sWarnBuffer = $sWarnBuffer.'
			<form action="nursing-station.php" method=post>
			<input type="hidden" name="sid" value="'.$sid.'">
				<input type="hidden" name="lang" value="'.$lang.'">
				<input type="hidden" name="pyear" value="'.$pyear.'">
			<input type="hidden" name="pmonth" value="'.$pmonth.'">
				<input type="hidden" name="pday" value="'.$pday.'">
			<input type="hidden" name="station" value="'.$station.'">
			<input type="hidden" name="ward_nr" value="'.$ward_nr.'">
			<input type="hidden" name="mode" value="copylast">&nbsp;&nbsp;&nbsp;';
			if($c>2) $sWarnBuffer = $sWarnBuffer.'<input type="submit" value="'.$LDCopyAnyway.'">';
				else $sWarnBuffer = $sWarnBuffer.'
						<input type="submit" value="'.$LDTakeoverList.'" >';
			$sWarnBuffer = $sWarnBuffer.'
			&nbsp;&nbsp;&nbsp;<input type="button" value="'.$LDDoNotCopy.'" onClick="javascript:window.location.href=\'nursing-station.php?sid='.$sid.'&edit=1&list=1&station='.$station.'&mode=fresh\'">
			</form>';

	$smarty->assign('sWarningPrompt',$sWarnBuffer);
}

# If ward exists, show the occupancy list

if($ward_ok){

	if($pyear.$pmonth.$pday<date('Ymd')){
		$smarty->assign('sWarningPrompt','
		<img '.createComIcon($root_path,'warn.gif','0','absmiddle',TRUE).'> <font color="#ff0000"><b>'.$LDAttention.'</font> '.$LDOldList.'</b>');

		# Prevent adding new patients to the list  if list is old
		$edit=FALSE;
	}

	# Start here, create the occupancy list
	# Assign the column  names
#echo "name = ".$LDLastName;
	$smarty->assign('LDRoom',$LDRoom);
	$smarty->assign('LDBed',$LDPatListElements[1]);
	#$smarty->assign('LDFamilyName',$LDLastName);
	$smarty->assign('LDFamilyName','Last Name');
	#$smarty->assign('LDName',$LDName);
	$smarty->assign('LDName','Given Name');
	$smarty->assign('LDBirthDate',$LDBirthDate);
	$smarty->assign('LDPatNr',$LDPatListElements[4]);
	#$smarty->assign('LDInsuranceType',$LDPatListElements[5]);
	$smarty->assign('LDCaseNo',$LDPatListElements[5]);
	$smarty->assign('LDOptions',$LDPatListElements[6]);

	# Initialize help flags
	$toggle=1;
	$room_info=array();
	# Set occupied bed counter
	$occ_beds=0;
	$lock_beds=0;
	$males=0;
	$females=0;
	$cflag=$ward_info['room_nr_start'];

	# Initialize list rows container string
	$sListRows='';

	# Loop trough the ward rooms

#	for ($i=$ward_info['room_nr_start'];$i<=$ward_info['room_nr_end'];$i++){
	$wardRoom = $ward_obj->getRoomsData($ward_nr);
	#echo $ward_obj->sql;
/*
echo "nursing-station.php : (wardRoom) ward_obj->sql = '".$ward_obj->sql."' <br> \n";
echo "nursing-station.php : room_ok = '".$room_ok."' <br> \n";
echo "nursing-station.php : room_obj = '".$room_obj."' <br> \n";
#echo "nursing-station.php : room_obj : <br>\n"; print_r($room_obj); echo"'<br> \n";
#$room_info=$room_obj->FetchRow();
echo "nursing-station.php : room_info : <br>\n"; print_r($room_info); echo"'<br> \n";
reset($room_obj);
*/
	while ($perRoom = $wardRoom->FetchRow()){
		$i = $perRoom['room_nr'];

		if($room_ok){
			$room_info=$room_obj->FetchRow();
		}else{
			$room_info['nr_of_beds']=1;
			$edit=false;
		}

		// Scan the patients object if the patient is assigned to the bed & room
		# Loop through room beds
		$max_bed_nr = $room_info['nr_of_beds'];

		for($j=1;$j<=$room_info['nr_of_beds'];$j++){

			# Reset elements
			#commented by VAN 04-08-08
			#$smarty->assign('sMiniColorBars','');
			$smarty->assign('sRoom','');
			$smarty->assign('sBed','');
			$smarty->assign('sBedIcon','');
			$smarty->assign('cComma','');
			$smarty->assign('sFamilyName','');
			$smarty->assign('sName','');
			$smarty->assign('sTitle','');
			$smarty->assign('sBirthDate','');
			$smarty->assign('sPatNr','');
			$smarty->assign('sCaseNo','');
			$smarty->assign('sAdmitDataIcon','');
			#commented by VAN 04-08-08
			#$smarty->assign('sChartFolderIcon','');
			$smarty->assign('sNotesIcon','');
			$smarty->assign('sTransferIcon','');
			#commented by VAN 04-08-08
			#uncomment by VAN 07-02-08
			$smarty->assign('sDischargeIcon','');

			$sAstart='';
			$sAend='';
			$sFamNameBuffer='';
			$sNameBuffer='';

			if($patients_ok){

				if(isset($patient[$i][$j])){
					$bed=&$patient[$i][$j];
						$is_patient=true;
					# Increase occupied bed nr
					$occ_beds++;
				}else{
					$is_patient=false;
					$bed=NULL;
				}
			}
			# set room nr change flag , toggle row color
			if($cflag!=$i){
				$toggle=!$toggle;
				$cflag=$i;
			}

			# set row color/class

			if ($toggle){
				$smarty->assign('bToggleRowClass',TRUE);
				$class_label = "wardlistrow1";
				if (($pid)&&($bed['pid']==$pid))
						$class_label = "hilite";
				$smarty->assign('class_label',$class_label);
			}else{
				$smarty->assign('bToggleRowClass',FALSE);
				$class_label = "wardlistrow2";
				if (($pid)&&($bed['pid']==$pid))
						$class_label = "hilite";
				$smarty->assign('class_label',$class_label);
			}

			# Check if bed is locked
			if(stristr($room_info['closed_beds'],$j.'/')){
				$bed_locked=true;
				$lock_beds++;
				# Consider locked bed as occupied so increase occupied bed counter
				$occ_bed++;
			}else{
				$bed_locked=false;
			}

			# If patient and edit show small color bars
			#commented by VAN 04-08-08
			#uncommented by VAN 09-06-08

			if($is_patient&&$edit){
				#edited by VAN 01-29-08
				$smarty->assign('sMiniColorBars','<a href="javascript:getinfo(\''.$bed['encounter_nr'].'\',\''.$bed['pid'].'\')">
					<img src="'.$root_path.'main/imgcreator/imgcreate_colorbar_small.php'.URL_APPEND.'&pn='.$bed['encounter_nr'].'" alt="'.$LDSetColorRider.'" align="absmiddle" border=0 width=80 height=18>
					</a>');
			}else{
				$smarty->assign('sMiniColorBars','');
			}


			# If bed nr  is 1, show the room number
			if($j==1){
			#commented by VAN 06-26-09
				#$smarty->assign('sRoom',strtoupper($ward_info['roomprefix']).$cnt);
				if ($ward_info['roomprefix'])
					$prefix = $ward_info['roomprefix']." ".$i;
				else
					$prefix = "Rm.".$i;
				$smarty->assign('sRoom',strtoupper($prefix));
			} else{
				$smarty->assign('sRoom','');
				$babyRoom = ''; //added by CHA, 05072010
			}

			$addBedNumber='';
			#if (intval(($j-1)/26)){
			#	$addBedNumber = chr((intval(($j-1)/26))+96);
			#}
			#$bedNumber = $addBedNumber."".chr((($j-1)%26)+97);
			$bedNumber = $j;
			$smarty->assign('sBed',strtoupper($bedNumber));
#			$smarty->assign('sBed',strtoupper(chr($j+96)));   # burn commented : September 27, 2007

			# If patient, show images by sex
			if($is_patient){
				$sBuffer = '<a href="javascript:popPic(\''.$bed['pid'].'\')">';
				switch(strtolower($bed['sex'])){
					case 'f':
						$smarty->assign('sBedIcon',$sBuffer.'<img '.createComIcon($root_path,'spf.gif','0','',TRUE).'></a>');
						$females++;
						break;
					case 'm':
						$smarty->assign('sBedIcon',$sBuffer.'<img '.createComIcon($root_path,'spm.gif','0','',TRUE).'></a>');
						$males++;
						break;
					default:
						$smarty->assign('sBedIcon',$sBuffer.'<img '.createComIcon($root_path,'bn.gif','0','',TRUE).'></a>');
				}

			}elseif($bed_locked){
				$smarty->assign('sBedIcon','<img '.createComIcon($root_path,'delete2.gif','0','',TRUE).'>');
			}
			elseif($edit){ // Else show the image link to assign bed to patient
				$space = "";
				if (strlen($j)==1){
					for ($cnt=1;$cnt<=strlen($max_bed_nr);$cnt++){
						$space .= "&nbsp;";
					}
				}

				$smarty->assign('sBedIcon','<a href="javascript:indata(\''.$i.'\',\''.$j.'\')">'.$space.'<img '.createComIcon($root_path,'plus2.gif','0','',TRUE).' alt="'.$LDClk2Occupy.'"></a>');
			}

			# Show the patients name with link to open charts
			if($edit){

				$sAstart = '<a href="';
				if(!$bed_locked){
					#commented by VAN 01-25-08
					#$sAstart = $sAstart.$root_path.'modules/registration_admission/aufnahme_pass.php'.URL_APPEND.'&target=search&fwd_nr='.$bed['encounter_nr'].'" title="'.$LDClk2Show.'">';
					#edited by VAN 01-25-08
					#echo "pid = ".$bed['pid'];
					#$sAstart = $sAstart.$root_path.'modules/registration_admission/aufnahme_daten_zeigen.php'.URL_APPEND.'&from=such&encounter_nr='.$bed['encounter_nr'].'&target=search&fromnurse=1" title="'.$LDClk2Show.'">';
					#$sAstart = $sAstart.'javascript:getPatientAdmissionInfo(\''.$bed['encounter_nr'].'\')" title="'.$LDClk2Show.'">';
					$sAstart = $sAstart.'javascript:getPatientInfo(\''.$bed['pid'].'\')" title="'.$LDClk2Show.'">';
				}else{
					$sAstart = $sAstart.'javascript:unlock(\''.strtoupper($j).'\',\''.$i.'\')" title="'.$LDInfoUnlock.'">'.$LDLocked; //$j=bed   $i=room number
				}
			}else{
				if($bed_locked){
					$smarty->assign('cComma','');
				}
			}

			if($is_patient&&($bed['encounter_nr']!="")){
				#commented by VAN 04-08-08
				#$smarty->assign('sTitle',ucfirst($bed['title']));

				if(isset($sln)&&$sln) $sFamNameBuffer = eregi_replace($sln,'<span style="background:yellow">'.ucfirst($sln).'</span>',ucfirst($bed['name_last']));
					else $sFamNameBuffer = ucfirst($bed['name_last']);

				if($bed['name_last']) $smarty->assign('cComma',',');
					else $smarty->assign('cComma','');

				if(isset($sfn)&&$sfn) $sNameBuffer = eregi_replace($sfn,'<span style="background:yellow">'.ucfirst($sln).'</span>',ucfirst($bed['name_first']));
					else $sNameBuffer = ucfirst($bed['name_first']);

			}else{
				$smarty->assign('sFamilyName','');
				$smarty->assign('sName','');
				$smarty->assign('cComma','');
			}

			if($edit) $sAend ='</a>';
				else $sAend='';

			# Assign the family and first names together with the <a href></a> tags
			#edited by VAN 02-06-08 add ucwords(strtolower)
			if($bed_locked){
				$smarty->assign('sFamilyName',$sAstart.$sAend);
			}else{
				$smarty->assign('sFamilyName',$sAstart.ucwords(strtolower($sFamNameBuffer)).$sAend);
				$smarty->assign('sName',$sAstart.ucwords(strtolower($sNameBuffer)).$sAend);
			}
#echo $bed['date_birth'];
			if(($bed['date_birth'])&&($bed['date_birth']!='0000-00-00')){

				if(isset($sg)&&$sg) $smarty->assign('sBirthDate',eregi_replace($sg,"<font color=#ff0000><b>".ucfirst($sg)."</b></font>",formatDate2Local($bed['date_birth'],$date_format)));
					else $smarty->assign('sBirthDate',formatDate2Local($bed['date_birth'],$date_format));
			}else{
				if (empty($bed['date_birth']))
					$smarty->assign('sBirthDate','');
				else
					$smarty->assign('sBirthDate','unknown');
			}
			#echo $bed['pid']." - ".$bed['encounter_nr']."<br>";

			#if ($bed['encounter_nr']) $smarty->assign('sPatNr',$bed['encounter_nr']);
			if ($bed['pid']) $smarty->assign('sPatNr',$bed['pid']);

			if ($bed['encounter_nr']) $smarty->assign('sCaseNo',$bed['encounter_nr']);

		/*
			$sBuffer = '';
			if($bed['insurance_class_nr']!=2) $sBuffer = $sBuffer.'<font color="#ff0000">';

			if(isset($$bed['insurance_LDvar'])&&!empty($$bed['insurance_LDvar']))  $sBuffer = $sBuffer.$$bed['insurance_LDvar'];
				else  $sBuffer = $sBuffer.$bed['insurance_name'];

			$smarty->assign('sInsuranceType',$sBuffer);
	*/
			if($edit){
				if(($is_patient)&&!empty($bed['encounter_nr'])){
					#Edited by Cherry 10-25-10

					#commented by VAN 01-25-08
					#$smarty->assign('sAdmitDataIcon','<a href="'.$root_path.'modules/registration_admission/aufnahme_pass.php'.URL_APPEND.'&target=search&fwd_nr='.$bed['encounter_nr'].'" title="'.$LDAdmissionData.' : '.$LDClk2Show.'"><img '.createComIcon($root_path,'pdata.gif','0','',TRUE).' alt="'.$LDAdmissionData.' : '.$LDClk2Show.'"></a>');
					#edited by VAN 01-25-08
					#$smarty->assign('sAdmitDataIcon','<a href="'.$root_path.'modules/registration_admission/aufnahme_daten_zeigen.php'.URL_APPEND.'&from=such&encounter_nr='.$bed['encounter_nr'].'&target=search&fromnurse=1" title="'.$LDAdmissionData.' : '.$LDClk2Show.'"><img '.createComIcon($root_path,'pdata.gif','0','',TRUE).' alt="'.$LDAdmissionData.' : '.$LDClk2Show.'"></a>');
					#$sAstart = $sAstart.'javascript:getPatientAdmissionInfo(\''.$bed['encounter_nr'].'\')" title="'.$LDClk2Show.'">';
					#$smarty->assign('sAdmitDataIcon','<a href="javascript:getPatientAdmissionInfo(\''.$bed['encounter_nr'].'\',\''.$area_type.'\')" title="'.$LDAdmissionData.' : '.$LDClk2Show.'"><img '.createComIcon($root_path,'pdata.gif','0','',TRUE).' alt="'.$LDAdmissionData.' : '.$LDClk2Show.'"></a>');
					$smarty->assign('sAdmitDataIcon','<a href="javascript:void(0);" onClick="getPatientAdmissionInfo(\''.$bed['encounter_nr'].'\',\''.$area_type.'\')" title="'.$LDAdmissionData.' : '.$LDClk2Show.'"><img '.createComIcon($root_path,'pdata.gif','0','',TRUE).' alt="'.$LDAdmissionData.' : '.$LDClk2Show.'"></a>');

					#edited by VAN 01-29-08
					#$smarty->assign('sChartFolderIcon','<a href="javascript:getinfo(\''.$bed['encounter_nr'].'\')" title="'.$LDShowPatData.'"><img '.createComIcon($root_path,'open.gif','0','',TRUE).' alt="'.$LDShowPatData.'"></a>');
					#commented by VAN 04-08-08
					#$smarty->assign('sChartFolderIcon','<a href="javascript:getinfo(\''.$bed['encounter_nr'].'\',\''.$bed['pid'].'\')" title="'.$LDShowPatData.'"><img '.createComIcon($root_path,'open.gif','0','',TRUE).' alt="'.$LDShowPatData.'"></a>');

					$sBuffer = '<a href="javascript:void(0);" onClick="getrem(\''.$bed['encounter_nr'].'\')" title="'.$LDNoticeRW.'"><img ';
					if($bed['ward_notes']) $sBuffer = $sBuffer.createComIcon($root_path,'bubble3.gif','0','',TRUE);
						else $sBuffer = $sBuffer.createComIcon($root_path,'bubble2.gif','0','',TRUE);
					$sBuffer = $sBuffer.' alt="'.$LDNoticeRW.'"></a>';

					$smarty->assign('sNotesIcon',$sBuffer);

					$smarty->assign('sTransferIcon','<a href="javascript:void(0);" onClick="Transfer(\''.$bed['encounter_nr'].'\')" title="'.$LDTransferPatient.'"><img '.createComIcon($root_path,'xchange.gif','0','',TRUE).' alt="'.$LDTransferPatient.'"></a>');

					#commented by VAN 04-08-08
					#uncomment by VAN 07-02-08
					//$smarty->assign('sDischargeIcon','<a href="javascript:void(0);" onClick="release(\''.$bed['room_nr'].'\',\''.$bed['bed_nr'].'\',\''.$bed['encounter_nr'].'\')" title="'.$LDReleasePatient.'"><img '.createComIcon($root_path,'bestell.gif','0','',TRUE).' alt="'.$LDReleasePatient.'"></a>');
					$smarty->assign('sDischargeIcon','<a href="javascript:void(0);" onClick="moveToWaitingList(\''.$bed['encounter_nr'].'\')" title="Move patient to waiting list"><img '.createComIcon($root_path,'user_go.png','0','',TRUE).' alt="Move patient to waiting list"></a>');
				}
			}

			#---added by CHA, Jan 15, 2010----
			if($bed['encounter_nr'])
			{
				global $db;
				$sql = "select cp.name_last,cp.name_first,cp.date_birth,cp.sex,cp.pid, ce.encounter_nr from care_person cp ".
					"left join care_neonatal cn on cp.pid=cn.pid ".
					"left join care_encounter as ce on cp.pid=ce.pid ".
					"where cn.parent_encounter_nr=".$db->qstr($bed['encounter_nr']).
					" and ce.encounter_status not in ('cancelled') and ce.is_discharged='0'";
				//echo $sql."<br>";
				if($result=$db->Execute($sql))
				{
					if($db->Affected_Rows())
					{
						$smarty->assign('isBaby',TRUE);
						$rowSrc='';
						while($row=$result->FetchRow())
						{
							#$smarty->assign('sBabyIcon','<img src="../../gui/img/common/default/user.png" border="0" align="absmiddle"/>');
							#$smarty->assign('sBabyFamilyName',ucfirst($row['name_last']));
							#$smarty->assign('sBabyName',ucfirst($row['name_first']));
							if(($row['date_birth'])&&($row['date_birth']!='0000-00-00'))
							{
								if(isset($sg)&&$sg) $smarty->assign('sBabyBirthDate',eregi_replace($sg,"<font color=#ff0000><b>".ucfirst($sg).
									"</b></font>",formatDate2Local($row['date_birth'],$date_format)));
								else $smarty->assign('sBabyBirthDate',formatDate2Local($row['date_birth'],$date_format));
								$babyBirthdate = formatDate2Local($row['date_birth'],$date_format);
							}
							else
							{
								if (empty($row['date_birth']))
								{
									$smarty->assign('sBabyBirthDate','');
									$babyBirthdate = '';
								}else{
									$smarty->assign('sBabyBirthDate','unknown');
									$babyBirthdate = 'unknown';
								}
							}
							#$smarty->assign('sBabyPatNr',$row['pid']);
							$smarty->assign('sBabyBed','');
							$sBuffer = '<a href="javascript:popPic(\''.$row['pid'].'\')">';
							switch(strtolower($row['sex']))
							{
								case 'f':
									#$smarty->assign('sBabyBedIcon',$sBuffer.'<img '.createComIcon($root_path,'spf.gif','0','',TRUE).'></a>');
									$babyBedIcon=$sBuffer.'<img '.createComIcon($root_path,'spf.gif','0','',TRUE).'></a>';
									$females++;
									break;
								case 'm':
									#$smarty->assign('sBabyBedIcon',$sBuffer.'<img '.createComIcon($root_path,'spm.gif','0','',TRUE).'></a>');
									$babyBedIcon=$sBuffer.'<img '.createComIcon($root_path,'spm.gif','0','',TRUE).'></a>';
									$males++;
									break;
								default:
									#$smarty->assign('sBabyBedIcon',$sBuffer.'<img '.createComIcon($root_path,'bn.gif','0','',TRUE).'></a>');
									$babyBedIcon=$sBuffer.'<img '.createComIcon($root_path,'bn.gif','0','',TRUE).'></a>';
							}

							if($row['encounter_nr'])
							{
								$babyAdmitIcon = '<a href="javascript:getPatientAdmissionInfo(\''.$row['encounter_nr'].'\',\''.$area_type.'\')" title="'.$LDAdmissionData.' : '.$LDClk2Show.'"><img '.createComIcon($root_path,'pdata.gif','0','',TRUE).' alt="'.$LDAdmissionData.' : '.$LDClk2Show.'"></a>';
							}
							else
								$babyAdmitIcon = "";

							#---added by CHA, Jan 25, 2010-----
							$sBuffer = '<a href="javascript:getrem(\''.$row['encounter_nr'].'\')" title="'.$LDNoticeRW.'"><img ';
							if($bed['ward_notes']) $sBuffer = $sBuffer.createComIcon($root_path,'bubble3.gif','0','',TRUE);
								else $sBuffer = $sBuffer.createComIcon($root_path,'bubble2.gif','0','',TRUE);
							$sBuffer = $sBuffer.' alt="'.$LDNoticeRW.'"></a>';

							$babyNotesIcon = $sBuffer;
							$babyTransferIcon = '<a href="javascript:Transfer(\''.$row['encounter_nr'].'\')" title="'.$LDTransferPatient.'"><img '.createComIcon($root_path,'xchange.gif','0','',TRUE).' alt="'.$LDTransferPatient.'"></a>';
							$babyDischargeIcon = '<a href="javascript:release(\''.$bed['room_nr'].'\',\''.$bed['bed_nr'].'\',\''.$row['encounter_nr'].'\')" title="'.$LDReleasePatient.'"><img '.createComIcon($root_path,'bestell.gif','0','',TRUE).' alt="'.$LDReleasePatient.'"></a>';
							#---end CHA------------------------

							$sAstart = '<a href="javascript:getPatientInfo(\''.$row['pid'].'\')" title="'.$LDClk2Show.'">';
							$rowSrc.= '<tr class="wardlistrow1">'.
							'<td></td>'.
							'<td style="font-size:x-small">'.$babyRoom.'</td>'.
							'<td style="font-size:x-small ">&nbsp;'.strtoupper($bedNumber).'&nbsp;'.$babyBedIcon.'</td>'.
							'<td><img src="../../gui/img/common/default/user.png" border="0" align="absmiddle"/>'.
							' '.$sAstart.ucfirst($row['name_last']).', '.ucfirst($row['name_first']).'</a></td>'.
							'<td style="font-size:x-small ">'.$babyBirthdate.'</td>'.
							'<td style="font-size:x-small ">&nbsp;'.$row['pid'].'</td>'.
							'<td style="font-size:x-small ">'.$row['encounter_nr'].'</td>'.
							'<td>&nbsp;'.$babyAdmitIcon.' '.$babyNotesIcon.' '.$babyTransferIcon.' '.$babyDischargeIcon.'</td>'.
							'</tr>';
						}
						$smarty->assign('BabyRows', $rowSrc);
					}
					else $smarty->assign('isBaby',FALSE);
				}else $smarty->assign('isBaby',FALSE);
			}
			else $smarty->assign('isBaby',FALSE);
			#end CHA--------------------------------------------------

			# Create the rows using ward_occupancy_list_row.tpl template
			ob_start();
				$smarty->display('nursing/ward_occupancy_list_row.tpl');
				$sListRows = $sListRows.ob_get_contents();
			ob_end_clean();

		} // end of bed loop

		# Append the new row to the previous row in string

		$smarty->assign('sOccListRows',$sListRows);
	} // end of ward loop


	# Final occupancy list line

	# Prepare the stations quick info data
	# Occupancy in percent
	$occ_percent=ceil(($occ_beds/$nr_beds)*100);
	# Nr of vacant beds
	$vac_beds=$nr_beds-$occ_beds;

	# Declare template items
	$TP_DOC1_BLOCK='';
	$TP_DOC2_BLOCK='';
	$TP_ICON1='';
	$TP_ICON2='';
	$TP_Legend1_BLOCK='';

	//$buf1='<img '.createComIcon($root_path,'powdot.gif','0','absmiddle').'>';
	# Create waiting list block
	if($waitlist_count&&$edit){
		while($waitpatient=$waitlist->FetchRow()){
			$buf2='';
			//if($waitpatient['current_ward_nr']!=$ward_nr) $buf2='<nobr>'.$waitpatient['ward_id'].'::';
			if($waitpatient['current_ward_nr']!=$ward_nr) $buf2=createComIcon($root_path,'red_dot.gif','0','',TRUE);
				else  $buf2=createComIcon($root_path,'green_dot.gif','0','',TRUE);
			#$TP_WLIST_BLOCK.='<nobr><img '.$buf2.'><a href="javascript:assignWaiting(\''.$waitpatient['encounter_nr'].'\',\''.$waitpatient['ward_id'].'\')">';
			if (($pid)&&($waitpatient['pid']==$pid))
				$class_label = "hilite";
			else
				$class_label = "";

			$TP_WLIST_BLOCK.='<span class='.$class_label.'><nobr><img '.$buf2.'><a href="javascript:void(0);" onClick="assignWaiting(\''.$waitpatient['encounter_nr'].'\',\''.$waitpatient['ward_id'].'\',\''.$area_type.'\',\''.$station.'\',\''.$ward_nr.'\')">';
			$TP_WLIST_BLOCK.='&nbsp;'.ucwords(strtolower($waitpatient['name_last'])).', '.ucwords(strtolower($waitpatient['name_first'])).' '.formatDate2Local($waitpatient['date_birth'],$date_format).'</nobr></span></a><br>';
		}
	}else{
		$TP_WLIST_BLOCK='&nbsp;';
	}
	if($edit){
		$wlist_url=$thisfile.URL_APPEND.'&ward_nr='.$ward_nr.'&edit='.$edit.'&station='.$station;
		if($w_waitlist){
			$TP_WLIST_OPT =	'[<a href="'.$wlist_url.'&w_waitlist=0">'.$LDShowWardOnly.'</a>]';
		}else{
			$TP_WLIST_OPT=	'[<a href="'.$wlist_url.'&w_waitlist=1">'.$LDShowAll.'</a>]';
		}
	}
	# Create doctors-on-duty block

	if(isset($person1)){
		$TP_DOC1_BLOCK='<a href="javascript:popinfo(\''.$pnr1.'\',\''.$dept_nr.'\')" title="'.$LDClk4Phone.'">'.$person1['name_last'].', '.$person1['name_first'].'</a>';
		$TP_ICON1='<img '.createComIcon($root_path,'violet_phone.gif','0','absmiddle',TRUE).'>';
	}
	if(isset($person2)){
		$TP_DOC2_BLOCK='<a href="javascript:popinfo(\''.$pnr2.'\',\''.$dept_nr.'\')" title="'.$LDClk4Phone.'">'.$person2['name_last'].', '.$person2['name_first'].'</a>';
		$TP_ICON2=$TP_ICON1;
	}

	# Create the legend block
	$TP_Legend1_BLOCK.='
	&nbsp;<img '.createComIcon($root_path,'green_dot.gif','0','absmiddle',TRUE).'>&nbsp;<b>'.$LDOwnPatient.'</b><br>
	&nbsp;<img '.createComIcon($root_path,'red_dot.gif','0','absmiddle',TRUE).'> <b>'.$LDNonOwnPatient.'</b><br>
	&nbsp;<img '.createComIcon($root_path,'plus2.gif','0','absmiddle',TRUE).'> <b>'.$LDFreeOccupy.'</b><br>
	&nbsp;<img '.createComIcon($root_path,'delete2.gif','0','absmiddle',TRUE).'> <b>'.$LDLocked.'</b><br>
	';

	if($edit&&$patients_ok){
		#edited by VAN 04-08-08
		/*
		$TP_Legend1_BLOCK.= '&nbsp;<img '.createComIcon($root_path,'pdata.gif','0','absmiddle',TRUE).'> <b>'.$LDAdmissionData.'</b><br>
		&nbsp;<img '.createComIcon($root_path,'open.gif','0','absmiddle',TRUE).'> <b>'.$LDOpenFile.'</b><br>
		&nbsp;<img '.createComIcon($root_path,'bubble2.gif','0','absmiddle',TRUE).'> <b>'.$LDNotesEmpty.'</b><br>
		&nbsp;<img '.createComIcon($root_path,'bubble3.gif','0','absmiddle',TRUE).'> <b>'.$LDNotes.'</b><br>
		&nbsp;<nobr><img '.createComIcon($root_path,'xchange.gif','0','absmiddle',TRUE).'> <b>'.$LDTransferPatient.'</b></nobr><br>
		&nbsp;<img '.createComIcon($root_path,'bestell.gif','0','absmiddle',TRUE).'> <b>'.$LDRelease.'</b><br>
		';
		*/
		$TP_Legend1_BLOCK.= '&nbsp;<img '.createComIcon($root_path,'pdata.gif','0','absmiddle',TRUE).'> <b>'.$LDAdmissionData.'</b><br>
		&nbsp;<img '.createComIcon($root_path,'bubble2.gif','0','absmiddle',TRUE).'> <b>'.$LDNotesEmpty.'</b><br>
		&nbsp;<img '.createComIcon($root_path,'bubble3.gif','0','absmiddle',TRUE).'> <b>'.$LDNotes.'</b><br>
		&nbsp;<nobr><img '.createComIcon($root_path,'xchange.gif','0','absmiddle',TRUE).'> <b>'.$LDTransferPatient.'</b></nobr><br>
		';

		$TP_Legend2_BLOCK= '
		&nbsp;<img '.createComIcon($root_path,'spf.gif','0','absmiddle',TRUE).'> <b>'.$LDFemale.'</b><br>
		&nbsp;<img '.createComIcon($root_path,'spm.gif','0','absmiddle',TRUE).'> <b>'.$LDMale.'</b><br>';
	}
	# Load the quick info block template
	$tp=$TP_obj->load('nursing/tp_ward_quickinfo.htm');

	# Buffer orig template output
	ob_start();
		eval("echo $tp;");
		$sTemp = ob_get_contents();
	ob_end_clean();
	 # Assign to page template object
	$smarty->assign('sSubMenuBlock',$sTemp);

}else{

	$smarty->assign('sNewWardLink','<ul><div class="prompt"><img '.createMascot($root_path,'mascot1_r.gif','0','absmiddle').'>
			'.str_replace("~station~",strtoupper($station),$LDNoInit).'</b></font><br>
			<a href="nursing-station-new.php'.URL_APPEND.'&station='.$station.'&edit='.$edit.'">'.$LDIfInit.' <img '.createComIcon($root_path,'bul_arrowgrnlrg.gif','0','',TRUE).'></a><p>
			</div></ul>');
} // end of if ward_ok

if($pday.$pmonth.$pyear<>date('dmY'))

	$smarty->assign('sToArchiveLink','<p>
			<a href="nursing-station-archiv.php'.URL_APPEND.'">'.$LDClk2Archive.' <img '.createComIcon($root_path,'bul_arrowgrnlrg.gif','0','',TRUE).'></a>
			<p>');

$smarty->assign('pbClose','<a href="'.$breakfile.'"><img '.createLDImgSrc($root_path,'close2.gif','0','absmiddle').'></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp');

if(!$edit){
	$smarty->assign('sOpenWardMngmt','<a href="nursing-station-pass.php'.URL_APPEND.'&edit=1&rt=pflege&ward_nr='.$ward_nr.'&station='.$ward_info['name'].'"><img '.createComIcon($root_path,'uparrowgrnlrg.gif','0','absmiddle',TRUE).'> '.$LDOpenWardManagement.'</a>');
}

# Assign the submenu to the mainframe center block

 $smarty->assign('sMainBlockIncludeFile','nursing/ward_occupancy.tpl');

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');

 ?>
