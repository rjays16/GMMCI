<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
/**
* CARE2X Integrated Hospital Information System beta 2.0.1 - 2004-07-04
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org, 
*
* See the file "copy_notice.txt" for the licence notice
*/
$lang_tables=array('person.php','actions.php');
define('LANG_FILE','stdpass.php');
define('NO_2LEVEL_CHK',1);
require_once($root_path.'include/inc_front_chain_lang.php');

require_once($root_path.'global_conf/areas_allow.php');

#$allowedarea=&$allow_area['admit'];
$allowedarea=&$allow_area['register'];
$append=URL_REDIRECT_APPEND; 
#echo "target = '".$target."' <br> \n";
#die();
if (empty($target))
	$target='search';

switch($target)
{
	case 'entry':$fileforward='patient_register_search.php'.$append.'&origin=pass&target=entry'; 
						$lognote='Patient register ok';
						break;
	case 'search':$fileforward='patient_register_search.php'.$append.'&origin=pass&target=search'; 
						$lognote='Patient register search ok';
						break;
	case 'archiv':$fileforward='patient_register_archive.php'.$append.'&origin=pass';
						$lognote='Patient register archive ok';
						 break;
	
	#added by VAN 06-20-08
	case 'comprehensive':$fileforward='patient_register_comprehensive_search.php'.$append.'&origin=pass&target=comprehensive'; 
						$lognote='Patient register comprehensive search ok';
						break;
	#------------------------
						 
	default: 
				$target='entry';
				$lognote='Patient register ok';
				$fileforward='patient_register.php'.$append;
}

#echo "target 2= '".$target."' <br> \n";
#echo "fileforward = '".$fileforward."' <br> \n";
			# burn added: March 12, 2007
	require_once($root_path.'include/care_api_classes/class_department.php');
	$dept_obj=new Department;
#	$user_dept_info = $dept_obj->getUserDeptInfo($HTTP_SESSION_VARS['sess_login_username']);
	if (!empty($HTTP_SESSION_VARS['sess_login_userid']))
		$seg_user_name = $HTTP_SESSION_VARS['sess_login_userid'];
	else
		$seg_user_name = $HTTP_SESSION_VARS['sess_temp_userid'];
	$user_dept_info = $dept_obj->getUserDeptInfo($seg_user_name);
	
/*	
	echo "patient_register_pass.php.php : user_dept_info = <br>"; 
	print_r($user_dept_info);
	echo  "' <br>\n";
*/	
#echo "dept = ".$user_dept_info['dept_nr'];
	#if ($user_dept_info['dept_nr']==150){
	if ($allow_opd_user){
		$allow_entry=TRUE;   # search under OPD Triage
	#}elseif($user_dept_info['dept_nr']==149){
	}elseif($allow_er_user){
		$allow_entry=TRUE;   # search under ER Triage
		
	#added by VAN 06-25-08
	#}elseif($user_dept_info['dept_nr']==174){
	#}elseif($user_dept_info['dept_nr']==151){
	}elseif($allow_medocs_user){
		$allow_entry=TRUE;   # search under BIRTHING SECTION Triage	
	}else{
		$allow_entry=FALSE;   # User has no permission to ADD/REGISTER new entry
	}
#echo "allow = ".$allow_opd_user;
#echo "allow_entry = '$allow_entry' <br> \n";
#die();
	if (!$allow_entry){   # burn added: March 12, 2007
#echo "allow_entry is false! <br> \n";
		$fileforward='patient_register_search.php'.$append.'&origin=pass&target=search'; 
						$lognote='Patient register search ok';
	}
	
#echo "fileforward = ".$fileforward;	
/*
	echo "patient_register_pass.php.php : HTTP_SESSION_VARS['sess_login_username'] = '".$HTTP_SESSION_VARS['sess_login_username']."' <br> \n";
	echo "patient_register_pass.php.php : HTTP_SESSION_VARS['sess_user_name'] = '".$HTTP_SESSION_VARS['sess_user_name']."' <br> \n";
	echo "patient_register_pass.php.php : HTTP_SESSION_VARS['sess_user_id'] = '".$HTTP_SESSION_VARS['sess_user_id']."' <br> \n";
	echo "patient_register_pass.php.php : user_dept_info['dept_nr'] = '".$user_dept_info['dept_nr']."' <br> \n";
	echo "patient_register_pass.php.php : allow_entry = '".$allow_entry."' <br> \n";
	echo "patient_register_pass.php.php : fileforward = '".$fileforward."' <br> \n";
*/
$thisfile=basename(__FILE__);
$breakfile='patient.php'.URL_APPEND;

$userck='aufnahme_user';
//reset cookie;
// reset all 2nd level lock cookies
setcookie($userck.$sid,'',0,'/');
require($root_path.'include/inc_2level_reset.php'); setcookie(ck_2level_sid.$sid,'',0,'/');

require($root_path.'include/inc_passcheck_internchk.php');
#die("pass = ".$pass);
if ($pass=='check') 
	#echo "sulod";
	include($root_path.'include/inc_passcheck.php');

$errbuf=$LDAdmission;

require($root_path.'include/inc_passcheck_head.php');
?>

<BODY  onLoad="document.passwindow.userid.focus();" bgcolor=<?php echo $cfg['body_bgcolor']; ?>
<?php if (!$cfg['dhtml']){ echo ' link='.$cfg['idx_txtcolor'].' alink='.$cfg['body_alink'].' vlink='.$cfg['idx_txtcolor']; } ?>>
<P>

<!---added, 2007-10-04 FDP--->
<table cellspacing="0"  class="titlebar" border=0>
	<tr valign=top  class="titlebar" >
  		<td bgcolor="#e4e9f4" valign="bottom">
		    &nbsp;&nbsp;
<!---until here--->
<?php

if($cfg['dhtml'])
 {
 switch($target)
   {
	case 'entry':$buf=$LDPatient.' :: '.$LDRegistration; break;
	case 'search':$buf=$LDPatient.' :: '.$LDSearch; break;
	case 'archiv':$buf=$LDPatient.' :: '.$LDAdvancedSearch; break;
	#added by VAN 06-20-08
	case 'comprehensive':$buf=$LDPatient.' :: '.$LDComprehensiveSearch; break;
	#-------------------------
	default: $target='entry';$buf=$LDPatient.' :: '.$LDRegistration;
   }
/*---replaced this, for uniformity-----2007-10-04 FDP
echo '
<script language=javascript>
<!--
 if (window.screen.width) 
 { if((window.screen.width)>1000) document.write(\'<img '.createComIcon($root_path,'smiley.gif','0','top').'><FONT  COLOR="'.$cfg['top_txtcolor'].'"  SIZE=4  FACE="verdana"> <b>'.$buf.'</b></font>\');}
 //-->
 </script>';
 }
 ?>
----with this-----*/
	//echo "$buf";
	echo '<img '.createComIcon($root_path,'smiley.gif','0','absmiddle').'><font color="'.$cfg['top_txtcolor'].'"  size=6  face="verdana"> <b>'.$buf.'</b></font>';
 }
 ?>
			</b></font>			
		</td>
	</tr>
</table>
<!---until here---> 
 
<table width=100% border=0 cellpadding="0" cellspacing="0"> 
<tr>
	<td colspan=3>
<?php

#
# Starting at version 2.0.2, the "new person" button is "new patient". 
# It can be reverted to "new person"  by defining the ADMISSION_EXT_TABS constant to TRUE
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
	
	//if($target=="entry") echo '<img '.createLDImgSrc($root_path,'register_green.gif','0').' alt="'.$LDNewPerson.'" title="'.$LDNewPerson.'">';
	//	else{ echo'<a href="patient_register_pass.php?sid='.$sid.'&target=entry&lang='.$lang.'"><img '.createLDImgSrc($root_path,'register_gray.gif','0').' alt="'.$LDNewPerson.'" title="'.$LDNewPerson.'" '; if($cfg['dhtml'])echo'style=filter:alpha(opacity=70) onMouseover=hilite(this,1) onMouseOut=hilite(this,0)'; echo '></a>';}
	if($target=="entry") echo '<img '.createLDImgSrc($root_path,$sNewPatientButton,'0').' alt="'.$LDNewPerson.'" title="'.$LDNewPerson.'">';
		else{ echo'<a href="patient_register_pass.php?sid='.$sid.'&target=entry&lang='.$lang.'"><img '.createLDImgSrc($root_path,$sNewPatientButtonGray,'0').' alt="'.$LDNewPerson.'" title="'.$LDNewPerson.'" '; if($cfg['dhtml'])echo'style=filter:alpha(opacity=70) onMouseover=hilite(this,1) onMouseOut=hilite(this,0)'; echo '></a>';}
	if($target=="search") echo '<img '.createLDImgSrc($root_path,'search_green.gif','0').' alt="'.$LDSearch.'" title="'.$LDSearch.'">';
		else{ echo '<a href="patient_register_pass.php?sid='.$sid.'&target=search&lang='.$lang.'"><img '.createLDImgSrc($root_path,'such-gray.gif','0').' alt="'.$LDSearch.'"  title="'.$LDSearch.'" ';if($cfg['dhtml'])echo'style=filter:alpha(opacity=70) onMouseover=hilite(this,1) onMouseOut=hilite(this,0)'; echo '></a>';}
	if($target=="archiv") echo '<img '.createLDImgSrc($root_path,'advsearch_green.gif','0').'  alt="'.$LDAdvancedSearch.'" title="'.$LDAdvancedSearch.'">';
		else{ echo '<a href="patient_register_pass.php?sid='.$sid.'&target=archiv&lang='.$lang.'"><img '.createLDImgSrc($root_path,'advsearch_gray.gif','0').' alt="'.$LDAdvancedSearch.'"  title="'.$LDAdvancedSearch.'" ';if($cfg['dhtml'])echo'style=filter:alpha(opacity=70) onMouseover=hilite(this,1) onMouseOut=hilite(this,0)'; echo '></a>';}
		
	#added by VAN 06-20-08
	if($target=="comprehensive") echo '<img '.createLDImgSrc($root_path,'compsearch_green.gif','0').'  alt="'.$LDComprehensiveSearch.'" title="'.$LDComprehensiveSearch.'">';
		else{ echo '<a href="patient_register_pass.php?sid='.$sid.'&target=comprehensive&lang='.$lang.'"><img '.createLDImgSrc($root_path,'compsearch_gray.gif','0').' alt="'.$LDComprehensiveSearch.'"  title="'.$LDComprehensiveSearch.'" ';if($cfg['dhtml'])echo'style=filter:alpha(opacity=70) onMouseover=hilite(this,1) onMouseOut=hilite(this,0)'; echo '></a>';}
	#------------------------	
?>
	</td>
</tr>

<?php 
$maskBorderColor='#66ee66';
require($root_path.'include/inc_passcheck_mask.php'); 
require($root_path.'include/inc_load_copyrite.php');
?>