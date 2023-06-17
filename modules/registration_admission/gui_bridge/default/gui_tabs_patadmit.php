<?php
/* Creates the tabs for the patient registration module  */
if(!isset($notabs)||!$notabs){

	$smarty->assign('bShowTabs',TRUE);

	#
	# Starting at version 2.0.2, the "new patient" button is hidden. It can be shown by defining the ADMISSION_EXT_TABS constant to TRUE
	# at the /include/inc_enviroment_global.php script
	#
	if(defined('ADMISSION_EXT_TABS') && ADMISSION_EXT_TABS){
		if($target=="entry") $img='admit-blue.gif';
									else{ $img='admit-gray.gif';}
		$pbBuffer='<a href="aufnahme_start.php'.URL_APPEND.'&target=entry&ptype='.$ptype.'"><img '.createLDImgSrc($root_path,$img,'0').' alt="'.$LDAdmit.'"  title="'.$LDAdmit.'"';
		if($cfg['dhtml']) $pbBuffer.='style=filter:alpha(opacity=70) onMouseover=hilite(this,1) onMouseOut=hilite(this,0)';
		$pbBuffer.=' align=middle></a>';
		$smarty->assign('pbNew',$pbBuffer);
		#
		# User "register new person" button
		#
		$sNewPersonButton ='register_gray.gif';
	}else{
		$sNewPersonButton ='admit-gray.gif';
	}

			# burn added: March 27, 2007
	require_once($root_path.'include/care_api_classes/class_department.php');
	$dept_obj=new Department;
	if (!empty($HTTP_SESSION_VARS['sess_login_userid']))
		$seg_user_name = $HTTP_SESSION_VARS['sess_login_userid'];
	else
		$seg_user_name = $HTTP_SESSION_VARS['sess_temp_userid'];
	$user_dept_info = $dept_obj->getUserDeptInfo($seg_user_name);
#	$user_dept_info = $dept_obj->getUserDeptInfo($HTTP_SESSION_VARS['sess_login_username']);
/*	
	echo "gui_tabs_patadmit.php : HTTP_SESSION_VARS['sess_login_username'] = '".$HTTP_SESSION_VARS['sess_login_username']."' <br>"; 
	echo "gui_tabs_patadmit.php : HTTP_SESSION_VARS['sess_user_name'] = '".$HTTP_SESSION_VARS['sess_user_name']."' <br>"; 
	echo "gui_tabs_patadmit.php : seg_user_name = '$seg_user_name' <br>"; 
	echo "gui_tabs_patadmit.php : user_dept_info = <br>"; 
	print_r($user_dept_info);
	echo  "' <br>\n";
*/	
	if (($allow_opd_user)&&($ptype=='opd')&&(!$allow_only_clinic)){
		$allow_entry=TRUE;   # search under OPD Triage
	}elseif(($allow_er_user)&&($ptype=='er')&&(!$allow_only_clinic)){
		$allow_entry=TRUE;   # search under ER Triage
	}elseif(($allow_ipd_user)&&($ptype=='ipd')&&(!$allow_only_clinic)){
		$allow_entry=TRUE;   # search under IPD Triage
	}elseif(($allow_phs_user)&&($ptype=='phs')){
		$allow_entry=FALSE;   # User has no permission to ADD/REGISTER new entry
	}else
		$allow_entry=FALSE;   # User has no permission to ADD/REGISTER new entry
#	echo "gui_tabs_patadmit.php : allow_entry = '".$allow_entry."' <br> \n";

	
	if($target=="search") $img='such-b.gif'; //echo '<img '.createLDImgSrc($root_path,'search_green.gif','0').' alt="'.$LDSearch.'">';
								else{ $img='such-gray.gif';}
	$pbBuffer='<a href="aufnahme_daten_such.php'.URL_APPEND.'&target=search&ptype='.$ptype.'"><img '.createLDImgSrc($root_path,$img,'0').' alt="'.$LDSearch.'" title="'.$LDSearch.'"';
	if($cfg['dhtml']) $pbBuffer.='style=filter:alpha(opacity=70) onMouseover=hilite(this,1) onMouseOut=hilite(this,0)';
	$pbBuffer.=' align=middle></a>';
	$smarty->assign('pbSearch',$pbBuffer);
	#shorcuts alt+r
	$redirectSearch = 'aufnahme_daten_such.php'.URL_APPEND.'&target=search&ptype='.$ptype;
	
	#edited by VAN 02-22-08
	#if($target=="archiv") $img='arch-blu.gif'; //echo '<img '.createLDImgSrc($root_path,'archive_green.gif','0').'  alt="'.$LDArchive.'">';
	#							else{$img='arch-gray.gif'; }
	if($target=="archiv") $img='advsearch_blue.gif'; //echo '<img '.createLDImgSrc($root_path,'archive_green.gif','0').'  alt="'.$LDArchive.'">';
								else{$img='advsearch_gray.gif'; }
								
	$pbBuffer='<a href="aufnahme_list.php'.URL_APPEND.'&target=archiv&ptype='.$ptype.'"><img '.createLDImgSrc($root_path,$img,'0').' alt="'.$LDArchive.'" title="'.$LDArchive.'" ';
	if($cfg['dhtml']) $pbBuffer.='style=filter:alpha(opacity=70) onMouseover=hilite(this,1) onMouseOut=hilite(this,0)';
	$pbBuffer.=' align=middle></a>';
	$smarty->assign('pbAdvSearch',$pbBuffer);
	#shortcuts alt+a
	$redirectAdSearch = 'aufnahme_list.php'.URL_APPEND.'&target=archive&ptype='.$ptype;
	
	if ($allow_entry){   # burn added: March 27, 2007
		$smarty->assign('sHSpacer','<img src="'.$root_path.'gui/img/common/default/pixel.gif" height=1 width=25>');
	//	$pbBuffer='<a href="patient_register.php'.URL_APPEND.'&target=entry"><img '.createLDImgSrc($root_path,'register_gray.gif','0').' alt="'.$LDRegisterNewPerson.'"  title="'.$LDRegisterNewPerson.'" ';
		$pbBuffer='<a href="patient_register.php'.URL_APPEND.'&target=entry&ptype='.$ptype.'"><img '.createLDImgSrc($root_path,$sNewPersonButton,'0').' alt="'.$LDRegisterNewPerson.'"  title="'.$LDRegisterNewPerson.'" ';
		if($cfg['dhtml']) $pbBuffer.='style=filter:alpha(opacity=70) onMouseover=hilite(this,1) onMouseOut=hilite(this,0)';
		$pbBuffer.=' align=middle></a>';
		$smarty->assign('pbSwitchMode',$pbBuffer);
		#shortcuts - alt+n 
		$redirectNewPatient = 'patient_register.php'.URL_APPEND.'&target=entry&ptype='.$ptype;
	}
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

if($parent_admit) $sDivClass =  'class="adm_div"'; else $sDivClass = 'class="reg_div"';

$smarty->assign('sRegDividerClass',$sDivClass);

if(!empty($subtitle)) $smarty->assign('sSubTitle',":: $subtitle");

?>

