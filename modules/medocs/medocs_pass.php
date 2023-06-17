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
define('LANG_FILE','stdpass.php');
define('NO_2LEVEL_CHK',1);
require_once($root_path.'include/inc_front_chain_lang.php');

require_once($root_path.'global_conf/areas_allow.php');

$ptype = $_GET['ptype'];
$src = $_GET['from'];
#$allowedarea=&$allow_area['admit'];
#$allowedarea=array('_a_1_medocswrite','_a_1_medocsmedrecicd','_a_1_admissionwrite');
$append=URL_REDIRECT_APPEND.'&from=pass&ptype='.$ptype; 

if(!session_is_registered('sess_user_origin')) session_register('sess_user_origin');

switch($target)
{
	case 'entry':
						$allowedarea=array('_a_1_medocswrite','_a_1_medocsmedrecicd','_a_1_admissionwrite');
						$fileforward='medocs_start.php'.$append; 
						$lognote='Medocs ok';
						break;
	case 'search':
						$allowedarea=array('_a_1_medocswrite','_a_1_medocsmedrecicd','_a_1_admissionwrite');
						$fileforward='medocs_data_search.php'.$append; 
						$lognote='Medocs search ok';
						break;
	case 'archiv':
						$allowedarea=array('_a_1_medocswrite','_a_1_medocsmedrecicd','_a_1_admissionwrite');
						$fileforward='medocs_archive.php'.$append;
						$lognote='Medocs archive ok';
						 break;
	case 'medocs_newbornreg':
						$title="Medical Records::New born registration";
						$userck="ck_opd_user";
						$allowedarea=array('_a_1_medocspatientmanage','_a_2_medocspatientregister');
						$fileforward=$root_path."modules/registration_admission/patient_register.php".URL_APPEND."&ptype=medocs&from=".$src;
						$lognote='Medocs newborn registration ok';
						break;	
						
	case 'medocs_searchpatient':
						$title="Medical Records::Search Patient";
						$userck="ck_opd_user";
						$allowedarea=array('_a_1_ipdpatientadmit','_a_2_ipdpatientview','_a_1_medocspatientmanage');
						$fileforward=$root_path."modules/registration_admission/patient_register_search.php".URL_APPEND."&ptype=medocs&from=".$src;
						$lognote='Medocs newborn registration ok';
						break;	
						
	case 'medocs_searchpatientrec':
						$target='entry';
						$lognote='Medocs ok';
                        $allowedarea=array('_a_1_medocspatientmanage','_a_1_medocswrite','_a_1_medocsmedrecicd');
						$fileforward='medocs_start.php'.URL_APPEND."&ptype=medocs&from=".$src;
						break;											
	
	case "reports":
						$title="Medical Records::Reports";
						$allowedarea=array('_a_1_ipdreports');
						$fileforward=$root_path."modules/repgen/seg_report_generator.php".$append."&ptype=medocs&from=".$src;
						break;
                        
    //added by VAS 05-28-2012
    case "reportgen": 
                        $title="Medical Records::Hospital Reports";
                        $allowedarea=array('_a_1_medocs_report_launcher');
                        #$fileforward=$root_path."cakeapp/repgen";
                        #for medical records
                        $dept_nr = '151';
                        $fileforward=$root_path."modules/reports/report_launcher.php".$append."&ptype=medocs&from=".$src."&dept_nr=".$dept_nr;
                        break;                   					
							 
	default: $target='entry';
						$lognote='Medocs ok';
						$fileforward='medocs_start.php'.$append;
}


$thisfile=basename(__FILE__);
//$breakfile='startframe.php'.URL_APPEND;
$breakfile=$root_path.'main/startframe.php'.URL_APPEND;

$userck='medocs_user';

setcookie($userck.$sid,'');
require($root_path.'include/inc_2level_reset.php'); 
setcookie(ck_2level_sid.$sid,'');

# reset the user origin
$HTTP_SESSION_VARS['sess_user_origin']='';

#added by VAN 02-23-08
#$HTTP_SESSION_VARS['inMedocs']=1;

require($root_path.'include/inc_passcheck_internchk.php');
if ($pass=='check') 	
	include($root_path.'include/inc_passcheck.php');

$errbuf=$LDMedocs;

require($root_path.'include/inc_passcheck_head.php');
?>

<BODY  onLoad="document.passwindow.userid.focus();" bgcolor=<?php echo $cfg['body_bgcolor']; ?>
<?php if (!$cfg['dhtml']){ echo ' link='.$cfg['idx_txtcolor'].' alink='.$cfg['body_alink'].' vlink='.$cfg['idx_txtcolor']; } ?>>

<!---removed, 2007-10-04 FDP
<FONT    SIZE=-1  FACE="Arial">

<P>
-----until here. then added---->
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
	case 'entry':$buf=$LDMedocs; break;
	case 'search':$buf=$LDMedocs; break;
	case 'archiv':$buf=$LDMedocs; break;
	default: $target="entry";$buf=$LDMedocs;
}

echo '
<script language=javascript>
<!--
 if (window.screen.width) 
 { if((window.screen.width)>1000) document.write(\'<img '.createComIcon($root_path,'penpaper.gif','0','top').'><FONT  COLOR="'.$cfg['top_txtcolor'].'"  SIZE=6  FACE="verdana"> <b>'.$buf.'</b></font>\');}
 //-->
 </script>';
 }
 ?>
<!---also added--->
		</td>
	</tr>
</table>
<!----until here---FDP--->
  
<table width=100% border=0 cellpadding="0" cellspacing="0"> 
<tr>
<td colspan=3><?php 
/*---commented out, 2007-10-04 FDP
					if($target=="entry") echo '<img '.createLDImgSrc($root_path,'newdata-b.gif','0').' alt="'.$LDAdmit.'">';
								else{ echo'<a href="medocs_pass.php?sid='.$sid.'&target=entry&lang='.$lang.'"><img '.createLDImgSrc($root_path,'newdata-gray.gif','0').' alt="'.$LDAdmit.'"'; if($cfg['dhtml'])echo'style=filter:alpha(opacity=70) onMouseover=hilite(this,1) onMouseOut=hilite(this,0)'; echo '></a>';}
-----until here only-------FDP----*/
							if($target=="search") echo '<img '.createLDImgSrc($root_path,'such-b.gif','0').' alt="'.$LDSearch.'">';
								else{ echo '<a href="medocs_pass.php?sid='.$sid.'&target=search&lang='.$lang.'"><img '.createLDImgSrc($root_path,'such-gray.gif','0').' alt="'.$LDSearch.'" ';if($cfg['dhtml'])echo'style=filter:alpha(opacity=70) onMouseover=hilite(this,1) onMouseOut=hilite(this,0)'; echo '></a>';}
/*							if($target=="archiv") echo '<img '.createLDImgSrc($root_path,'arch-blu.gif','0').'  alt="'.$LDArchive.'">';
								else{ echo '<a href="medocs_pass.php?sid='.$sid.'&target=archiv&lang='.$lang.'"><img '.createLDImgSrc($root_path,'arch-gray.gif','0').' alt="'.$LDArchive.'" ';if($cfg['dhtml'])echo'style=filter:alpha(opacity=70) onMouseover=hilite(this,1) onMouseOut=hilite(this,0)'; echo '></a>';}
*/						?></td>
</tr>
</table>
<?php require($root_path.'include/inc_passcheck_mask.php') ?>  

<p>
<?php
require($root_path.'include/inc_load_copyrite.php');
?>
</FONT>
</BODY>
</HTML>
