<?php
#  Creates the tabs for the patient registration module
#echo "dept_belong = ".$dept;

if(!isset($notabs)||!$notabs){

	$smarty->assign('bShowTabs',TRUE);

	#
	# Starting at version 2.0.2, the button is named "new patient"
	# It can be reverted to "new person" by defining the ADMISSION_EXT_TABS constant to TRUE
	# at the /include/inc_enviroment_global.php script
	#
	if(defined('ADMISSION_EXT_TABS') && ADMISSION_EXT_TABS){
		#
		# User "register new person" button
		#
		$sNewPatientButton ='register_green.gif';
		$sNewPatientButtonGray ='register_gray.gif';
	}else{
		$sNewPatientButton ='new_patient_green.gif';
		$sNewPatientButtonGray ='admit-gray.gif';
	}

	
	
			# burn added: March 12, 2007
	require_once($root_path.'include/care_api_classes/class_department.php');
	$dept_obj=new Department;

	if (!empty($HTTP_SESSION_VARS['sess_login_userid']))
		$seg_user_name = $HTTP_SESSION_VARS['sess_login_userid'];
	else
		$seg_user_name = $HTTP_SESSION_VARS['sess_temp_userid'];
	$user_dept_info = $dept_obj->getUserDeptInfo($seg_user_name);
#	$user_dept_info = $dept_obj->getUserDeptInfo($HTTP_SESSION_VARS['sess_login_username']);
/*	
	echo "gui_tabs_patreg.php : HTTP_SESSION_VARS['sess_login_username'] = '".$HTTP_SESSION_VARS['sess_login_username']."' <br>"; 
	echo "gui_tabs_patreg.php : HTTP_SESSION_VARS['sess_user_name'] = '".$HTTP_SESSION_VARS['sess_user_name']."' <br>"; 
	echo "gui_tabs_patreg.php : seg_user_name = '$seg_user_name' <br>"; 
	echo "gui_tabs_patreg.php : user_dept_info = <br>"; 
	print_r($user_dept_info);
	echo  "' <br>\n";
*/	

	#if ($user_dept_info['dept_nr']==150){
	if (($allow_opd_user)&&($ptype=='opd')&&(!$allow_only_clinic)){
		$allow_entry=TRUE;   # search under OPD Triage
	#}elseif($user_dept_info['dept_nr']==149){
	}elseif(($allow_er_user)&&($ptype=='er')&&(!$allow_only_clinic)){
		$allow_entry=TRUE;   # search under ER Triage
	#}elseif($user_dept_info['dept_nr']==148){
	}elseif(($allow_ipd_user)&&($ptype=='ipd')&&(!$allow_only_clinic)){
		$allow_entry=TRUE;   # search under Admission Section
	}elseif(($allow_phs_user)&&($ptype=='phs')){
		$allow_entry=FALSE;   # search under PHS Section
	#added by VAN 06-25-08
	#}elseif($user_dept_info['dept_nr']==174){
	#}elseif($user_dept_info['dept_nr']==151){
	}elseif(($allow_medocs_user)&&(!$allow_only_clinic)){
		$allow_entry=TRUE;   # search under BIRTHING SECTION Triage		
	}else{
		$allow_entry=FALSE;   # User has no permission to ADD/REGISTER new entry
	}
#	echo "gui_tabs_patreg.php : allow_entry = '".$allow_entry."' <br> \n";

#echo "<br>allow ipd = ".$allow_ipd_user;
#echo "<br>tab ptype = ".$ptype."<br>";

	if (($ptype=='ipd')||($ptype=='newborn')){
		$redirectMainMenu = $root_path.'modules/ipd/seg-ipd-functions.php';
		$redirectNewBorn = 'patient_register.php'.URL_APPEND.'&target=entry&ptype=newborn';
	}elseif($ptype=='er'){
		$redirectMainMenu = $root_path.'modules/er/seg-er-functions.php';
		$redirectNewBorn = 'patient_register.php'.URL_APPEND.'&target=entry&ptype='.$ptype;
	}elseif($ptype=='opd'){		
		$redirectMainMenu = $root_path.'modules/opd/seg-opd-functions.php';
		$redirectNewBorn = 'patient_register.php'.URL_APPEND.'&target=entry&ptype='.$ptype;
	}
		
	if ($allow_entry){   # burn added: March 12, 2007
		if($target=="entry") $img=$sNewPatientButton; //echo '<img '.createLDImgSrc($root_path,'register_green.gif','0').' alt="'.$LDAdmit.'">';
									else{ $img=$sNewPatientButtonGray;}
		$pbBuffer='<a href="patient_register.php'.URL_APPEND.'&target=entry&ptype='.$ptype.'"><img '.createLDImgSrc($root_path,$img,'0').' alt="'.$LDRegisterNewPerson.'"  title="'.$LDRegisterNewPerson.'"';
		if($cfg['dhtml']) $pbBuffer.='style=filter:alpha(opacity=70) onMouseover=hilite(this,1) onMouseOut=hilite(this,0)';
		$pbBuffer.=' align=middle></a>';
		$smarty->assign('pbNew',$pbBuffer);
		#shortcuts - alt+n 
		$redirectNewPatient = 'patient_register.php'.URL_APPEND.'&target=entry&ptype='.$ptype;
		
	}
	if($target=="search") $img='search_green.gif'; //echo '<img '.createLDImgSrc($root_path,'search_green.gif','0').' alt="'.$LDSearch.'">';
								else{ $img='such-gray.gif';}
	$pbBuffer='<a href="patient_register_search.php'.URL_APPEND.'&target=search&ptype='.$ptype.'"><img '.createLDImgSrc($root_path,$img,'0').' alt="'.$LDSearch.'" title="'.$LDSearch.'"';
	if($cfg['dhtml']) $pbBuffer.='style=filter:alpha(opacity=70) onMouseover=hilite(this,1) onMouseOut=hilite(this,0)';
	$pbBuffer.=' align=middle></a>';
	$smarty->assign('pbSearch',$pbBuffer);
	#shortcut - Alt+r 
	$redirectSearch = 'patient_register_search.php'.URL_APPEND.'&target=search&ptype='.$ptype;
	
	if($target=="archiv") $img='advsearch_green.gif'; //echo '<img '.createLDImgSrc($root_path,'archive_green.gif','0').'  alt="'.$LDArchive.'">';
								else{$img='advsearch_gray.gif'; }
	$pbBuffer='<a href="patient_register_archive.php'.URL_APPEND.'&target=archiv&ptype='.$ptype.'"><img '.createLDImgSrc($root_path,$img,'0').' alt="'.$LDAdvancedSearch.'" title="'.$LDAdvancedSearch.'" ';
	if($cfg['dhtml']) $pbBuffer.='style=filter:alpha(opacity=70) onMouseover=hilite(this,1) onMouseOut=hilite(this,0)'; 
	$pbBuffer.=' align=middle></a>';
	$smarty->assign('pbAdvSearch',$pbBuffer);
	#shortcuts - alt+a
	#edited by VAN 02-11-08
	$redirectAdSearch = 'patient_register_archive.php'.URL_APPEND.'&target=archive&ptype='.$ptype;
	
	#echo "buffer = ".$pbBuffer;
	#-------added by VAN 06-20-08
	if($target=="comprehensive") $img='compsearch_green.gif'; //echo '<img '.createLDImgSrc($root_path,'archive_green.gif','0').'  alt="'.$LDArchive.'">';
								else{$img='compsearch_gray.gif'; }
	$pbBuffer='<a href="patient_register_comprehensive_search.php'.URL_APPEND.'&target=comprehensive&ptype='.$ptype.'"><img '.createLDImgSrc($root_path,$img,'0').' alt="'.$LDComprehensiveSearch.'" title="'.$LDComprehensiveSearch.'" ';
	if($cfg['dhtml']) $pbBuffer.='style=filter:alpha(opacity=70) onMouseover=hilite(this,1) onMouseOut=hilite(this,0)'; 
	$pbBuffer.=' align=middle></a>';
	
	$smarty->assign('pbCompSearch',$pbBuffer);
	#shortcuts - alt+a
	#edited by VAN 02-11-08
	$redirectCompSearch = 'patient_register_comprehensive_search.php'.URL_APPEND.'&target=comprehensive&ptype='.$ptype;
	#-----------------------
	
	$smarty->assign('sHSpacer','<img src="'.$root_path.'gui/img/common/default/pixel.gif" height=1 width=25>');

	#
	# Starting at version 2.0.2, the button is named  "admission" and links to search admission page
	#
	//$pbBuffer='<a href="aufnahme_start.php'.URL_APPEND.'&target=entry"><img '.createLDImgSrc($root_path,'admit-gray.gif','0').' alt="'.$LDAdmit.'"  title="'.$LDAdmit.'" ';
	
	#edited by VAN 02-11-08
	#$pbBuffer='<a href="aufnahme_daten_such.php'.URL_APPEND.'&target=search"><img '.createLDImgSrc($root_path,'ein-gray.gif','0').' alt="'.$LDAdmit.'"  title="'.$LDAdmit.'" ';
	#$pbBuffer='<a href="aufnahme_daten_such.php'.URL_APPEND.'&target=search"><img '.createLDImgSrc($root_path,'consultation-gray.gif','0').' alt="'.$LDAdmit.'"  title="'.$LDAdmit.'" ';
	$pbBuffer='<a href="aufnahme_daten_such.php'.URL_APPEND.'&target=search&ptype='.$ptype.'"><img '.createLDImgSrc($root_path,'consultation-gray.gif','0').' alt="Consultation"  title="Consultation" ';
	if($cfg['dhtml']) $pbBuffer.='style=filter:alpha(opacity=70) onMouseover=hilite(this,1) onMouseOut=hilite(this,0)';
	$pbBuffer.=' align=middle></a>';
	$smarty->assign('pbSwitchMode',$pbBuffer);
	#shortcuts - alt+m
	$redirectAdmission = 'aufnahme_daten_such.php'.URL_APPEND.'&target=search&ptype='.$ptype;
}

#  Horizontal  line below the tabs
	 #Include yahoo scripts
	ob_start();
	include_once($root_path.'modules/registration_admission/include/yh_script.php');
	$temp1 = ob_get_contents();
	ob_end_clean();
	$smarty->assign('yhScript',$temp1);
	//include yahoo shortcuts.. 
	ob_start();
		include_once($root_path.'modules/registration_admission/include/yh_tabs.php');
		$temp2 = ob_get_contents();
	ob_end_clean();
	$smarty->assign('yhShortcuts',$temp2);

//if($tab_bot_line) $sDivClass = $tab_bot_line; else $sDivClass = '#333399';

if($tab_bot_line) $sDivClass = 'class="reg_div"'; else $sDivClass =  'class="adm_div"';

$smarty->assign('sRegDividerClass',$sDivClass);

if(!empty($subtitle)) $smarty->assign('sSubTitle','<font color="#000099" SIZE=3  FACE="verdana,Arial"><b>:: '.$subtitle.'</b></font>');

	include_once($root_path.'include/care_api_classes/class_encounter.php');
	$enc_obj=new Encounter;

   	# burn added : May 11, 2007
	if (	$current_encounter && 
			($gui_tabs_patreg_encounter_type = $enc_obj->EncounterType($current_encounter))){
		#echo "gui_tabs_patreg.php : gui_tabs_patreg_encounter_type = '".$gui_tabs_patreg_encounter_type."' <br> \n";
		if ($gui_tabs_patreg_encounter_type==1){
			$segPersonIsStatus="This person is currently on ER Consultation.";
		}elseif($gui_tabs_patreg_encounter_type==2){
			$segPersonIsStatus="This person is currently on OPD Consultation.";
		}elseif(($gui_tabs_patreg_encounter_type==3) || ($gui_tabs_patreg_encounter_type==4)){
			$segPersonIsStatus="This person is currently admitted.";
		}else{
			$segPersonIsStatus="";
		}
	}
		
#if(isset($current_encounter)&&$current_encounter) $smarty->assign('sWarnText','<font size=2 FACE="verdana,Arial"> <img '.createComIcon($root_path,'warn.gif','0','absmiddle').'> '.$LDPersonIsAdmitted.'</font>');   # burn commented : May 11, 2007
if(isset($current_encounter)&&$current_encounter) $smarty->assign('sWarnText','<font size=2 FACE="verdana,Arial"> <img '.createComIcon($root_path,'warn.gif','0','absmiddle').'> '.$segPersonIsStatus.'</font>');   # burn added : May 11, 2007

?>