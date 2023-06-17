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

$lang_tables[]='search.php';
define('LANG_FILE','nursing.php');
define('NO_2LEVEL_CHK',1);
require_once($root_path.'include/inc_front_chain_lang.php');

define(INSURANCE_NO_LEN, 12);

$breakfile='nursing.php'.URL_APPEND;

/* Load the date formatter */
require_once($root_path.'include/inc_date_format_functions.php');
include_once($root_path.'include/care_api_classes/class_globalconfig.php');
$GLOBAL_CONFIG;
$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
$glob_obj->getConfig('patient_%');

# added by VAN 04-26-2010
 #print_r($HTTP_SESSION_VARS);
 #echo $HTTP_SESSION_VARS['sess_login_personell_nr'];
	require_once($root_path.'include/care_api_classes/class_personell.php');
	$pers_obj = new Personell();
 #get ward area of the user who login
 $personell_nr = $HTTP_SESSION_VARS['sess_login_personell_nr'];
 $rowPers = $pers_obj->get_Personell_info($personell_nr);
 #echo $pers_obj->sql;
 $ward_nr = $rowPers['ward_nr'];
 #echo 's = '.$ward_nr;
 $job = substr($rowPers['short_id'],0,1);
 $is_reliever = $rowPers['is_reliever'];

 $admin_permission = array('System_Admin', '_a_0_all');

 for ($i=0; $i<sizeof($admin_permission);$i++){
		if (ereg($admin_permission[$i],$HTTP_SESSION_VARS['sess_permission'])){
			$allow_all = 1;
			break;
		}else
			$allow_all = 0;
 }

if($mode=='such'||$mode=='paginate')
{
	$tb_person='care_person';
	$tb_encounter='care_encounter';
	$tb_location='care_encounter_location';
	$tb_ward='care_ward';

	# Initialize page´s control variables
	if($mode=='paginate'){
		$searchkey=$HTTP_SESSION_VARS['sess_searchkey'];
	}else{
		# Reset paginator variables
		$pgx=0;
		$totalcount=0;
		$HTTP_SESSION_VARS['sess_searchkey']=$searchkey;
		$oitem='';
		$odir='';
	}

	$keyword = $searchkey;
	# convert * and ? to % and &
	$searchkey=strtr($searchkey,'*?','%_');

	#Load and create paginator object
	include_once($root_path.'include/care_api_classes/class_paginator.php');
	$pagen=new Paginator($pgx,$thisfile,$HTTP_SESSION_VARS['sess_searchkey'],$root_path);

	$GLOBAL_CONFIG=array();
	include_once($root_path.'include/care_api_classes/class_globalconfig.php');
	$glob_obj=new GlobalConfig($GLOBAL_CONFIG);

	# Get the max nr of rows from global config
	$glob_obj->getConfig('pagin_patient_search_max_block_rows');
	if(empty($GLOBAL_CONFIG['pagin_patient_search_max_block_rows'])) $pagen->setMaxCount(MAX_BLOCK_ROWS); # Last resort, use the default defined at the start of this page
		else $pagen->setMaxCount($GLOBAL_CONFIG['pagin_patient_search_max_block_rows']);

	# Work around
	//$searchkey=$searchkey;
	$srcword=trim($searchkey);
	//prepare the seach word detect several types
	#edited by VAN 01-08-10
	if(is_numeric($srcword)){
		$usenum=true;

		#if($srcword>$GLOBAL_CONFIG['patient_inpatient_nr_adder']){
		if (strlen($srcword) == INSURANCE_NO_LEN) {
			$cond.=" p.pid IN (SELECT pi.pid AS pid
														FROM care_person_insurance AS pi
														INNER JOIN care_person AS cp ON cp.pid=pi.pid
														WHERE pi.insurance_nr='".$srcword."'   AND pi.hcare_id='27' AND pi.is_void=0
														UNION
															SELECT dep.dependent_pid AS pid
															FROM care_person_insurance AS pi
														INNER JOIN care_person AS cp ON cp.pid=pi.pid
														LEFT JOIN seg_dependents AS dep ON dep.parent_pid=pi.pid
														WHERE pi.insurance_nr='".$srcword."'  AND pi.hcare_id='27' AND pi.is_void=0)";
		}elseif (strlen(trim($srcword))<11){
			$cond.=" p.pid='$srcword'";
		}else{
			$cond.=" e.encounter_nr='$srcword'";
		}
	}else{
		 if(stristr($srcword,',')){
				$lastnamefirst=TRUE;
		 }else{
				$lastnamefirst=FALSE;
		 }

		 if(stristr($srcword, ',') === FALSE){
				 $cbuffer=explode(' ',$srcword);
				 $lnameOnly = 1;
		 }else{
				 $cbuffer=explode(',',$srcword);
				 $newquery = "";
				 $lnameOnly = 0;
		 }

		 for($x=0;$x<sizeof($cbuffer);$x++){
				 $cbuffer[$x]=trim($cbuffer[$x]);
				 if($cbuffer[$x]!='') $comp[]=$cbuffer[$x];
		 }

			# Arrange the values, ln= lastname, fn=first name, bd = birthday
			if($lastnamefirst){
				 $fn=$comp[1];
				 $ln=$comp[0];
				 $bd=$comp[2];
			}else{
				 $fn=$comp[0];
				 $ln=$comp[1];
				 $bd=$comp[2];
			}

			if(sizeof($comp)>1){
				$cntlast = sizeof($cbuffer)-1;
				if (sizeof($cbuffer) > 2){
					if ($lnameOnly)
						 $cond.=" (name_last $sql_LIKE '".$srcword."%'))";
					else
						 $cond.=" (name_last $sql_LIKE '".strtr($ln,'+',' ')."%' AND name_first $sql_LIKE '".strtr($fn,'+',' ')."%'))";

					 $bd=$comp[sizeof($cbuffer)];
				}else{
						if ($lnameOnly)
								$cond.=" ((name_last $sql_LIKE '".$srcword."%'))";
						else
								$cond.=" ((name_last $sql_LIKE '".strtr($ln,'+',' ')."%' AND name_first $sql_LIKE '".strtr($fn,'+',' ')."%'))";

						if(!empty($bd)){
								 $DOB=@formatDate2STD($bd,$date_format);
								 if($DOB=='') {
											$sql2.=" date_birth $sql_LIKE '$bd%' ";
								 }else{
											$sql2.=" date_birth = '$DOB' ";
								 }
						}
				}
		}else{
				$cond.= "( name_last $sql_LIKE '".addslashes($srcword)."%')";
		}

	}

	#edited by VAN 01-28-08

	/*$gbuf="l.location_nr,p.name_last, p.name_first,p.date_birth,
			 e.is_discharged, e.discharge_date, e.discharge_time,
			 e.encounter_nr, e.encounter_class_nr,e.in_ward,
			 w.name,w.roomprefix,
			 l.date_from,
			 r.location_nr";
	 */
	if(!isset($db)||!$db)include($root_path.'include/inc_db_makelink.php');
	if($dblink_ok){

		#edited by VAN 05-19-2010
		$sqlselect="SELECT DISTINCT p.pid, p.name_last, p.name_first,
									p.date_birth, p.title,
									p.sex, p.photo_filename, e.insurance_class_nr, e.encounter_type, e.is_discharged, e.in_ward,
									e.discharge_date, e.discharge_time, e.admission_dt, e.encounter_nr, e.current_ward_nr AS ward_nr,
									e.current_room_nr AS room_nr, b.date_from AS ward_date, b.date_to,
									w.roomprefix, w.name AS ward_name, b.location_nr AS bed_nr,
									b.nr AS bed_loc_nr,
									i.name AS insurance_name,
									i.LD_var AS \"insurance_LDvar\", n.nr AS ward_notes";

		$date_now = date("Y-m-d");
		#echo "cond = ".$cond;
		#edited by VAN 04-09-08
		#edited by VAN 01-08-10
		$sqlfrom.=" FROM care_person AS p
								INNER JOIN care_encounter AS e ON e.encounter_nr=(
						SELECT encounter_nr FROM care_encounter AS enc
						WHERE p.pid=enc.pid AND enc.is_discharged=0 AND enc.encounter_status <> 'cancelled' AND enc.status NOT IN ('deleted','hidden','inactive','void')
						ORDER BY enc.encounter_date DESC LIMIT 1)
						LEFT JOIN care_encounter_location AS r ON e.encounter_nr=r.encounter_nr
						LEFT JOIN care_encounter_location AS b
								ON (r.encounter_nr=b.encounter_nr AND r.group_nr=b.group_nr
							AND b.type_nr=5 AND b.status NOT IN ('discharged','closed','deleted','hidden','inactive','void')
							AND b.date_from<='$date_now'
							AND ('$date_now'<=b.date_to OR b.date_to ='0000-00-00') )
						LEFT JOIN care_encounter_location AS c
							ON (r.encounter_nr=c.encounter_nr AND r.group_nr=c.group_nr
							AND c.type_nr=2 AND c.status NOT IN ('discharged','closed','deleted','hidden','inactive','void')
							AND c.date_from<='$date_now'
							AND ('$date_now'<=c.date_to OR c.date_to ='0000-00-00') )
						LEFT JOIN care_ward AS w ON w.nr=c.location_nr
						LEFT JOIN care_class_insurance AS i ON e.insurance_class_nr=i.class_nr
						LEFT JOIN care_encounter_notes AS n ON b.encounter_nr=n.encounter_nr AND n.type_nr=6
						WHERE r.type_nr=4 AND r.status NOT IN ('discharged','closed','deleted','hidden','inactive','void')
						AND ('$date_now'<=r.date_to OR r.date_to ='0000-00-00')
						AND $cond
						AND encounter_type IN (3,4)
						GROUP BY encounter_nr";

		if(!empty($oitem)){

			#Filter the sort item
			switch($oitem){
				case 'ward_nr':
				{
					$itembuf='location_nr';
					$prep='l';
					break;
				}
				case 'ward_date':
				{
					$itembuf='date_from';
					$prep='l';
					break;
				}
				case 'room_nr':
				{
					$itembuf='location_nr';
					$prep='r';
					break;
				}
				case 'encounter_nr':
				{
					$itembuf='encounter_nr';
					$prep='e';
					break;
				}
				default:
					$itembuf=$oitem;
					$prep='p';
			}
			 $sql=$sqlselect.$sqlfrom." ORDER BY $prep.$itembuf $odir";
		}else{
			$sql=$sqlselect.$sqlfrom;
		}
		#echo $sql."<p>mode==such";
		#echo "nursing/nursing-patient-such-start = ".$sql;
		#exit();
		if($ergebnis=$db->SelectLimit($sql,$pagen->MaxCount(),$pagen->BlockStartIndex())){
		#echo "er = ".$ergebnis."<br>";
		#print_r($ergebnis);
		#echo "<br>";
			$rows=$ergebnis->RecordCount();
			#$result=$ergebnis->FetchRow();
			if($rows==1){
			#if(($rows==1)&&($ward_nr==$result2['ward_nr'])){
				$result=$ergebnis->FetchRow();
				if ((!$is_reliever)&&($job=='N')){
					$ward_row = $pers_obj->get_Nurse_Ward_Area_Assign($personell_nr, $result['ward_nr']);
					#echo $pers_obj->sql;
					$assigned_ward =$pers_obj->count;
				}elseif ((($is_reliever)&&($job=='N'))||($allow_all)){
					$assigned_ward =1;
				}else{
					$assigned_ward =0;
				}
				#----------------
				#echo "s = ".$assigned_ward;
				if ($assigned_ward){
				#if (($HTTP_SESSION_VARS['sess_login_personell_nr'])&&){
					header("location:nursing-station-pass.php?rt=pflege&sid=".$sid."&lang=".$lang."&fwd_nr=&edit=1&retpath=search_patient&checkintern=1&pid=".$result['pid']."&ward_nr=".$result['ward_nr']."&station=".addslashes($result['ward_name']));
					exit;
				}else{
					$ergebnis=$db->SelectLimit($sql,$pagen->MaxCount(),$pagen->BlockStartIndex());
				}
			}else{

					$pagen->setTotalBlockCount($rows);

					# If more than one count all available
					if(isset($totalcount)&&$totalcount){
						$pagen->setTotalDataCount($totalcount);
					}else{
						# Count total available data
						#$sql='SELECT e.encounter_nr '.$sqlfrom;
						#echo  "<br> sql2 = ".$sql;
						$totalcount=0;
						if($result=$db->Execute($sql)){
							$totalcount=$result->RecordCount();
						}

						$pagen->setTotalDataCount($totalcount);
					}
					# Set the sort parameters
					$pagen->setSortItem($oitem);
					$pagen->setSortDirection($odir);
			}
		}else{echo "$sql<br>$LDDbNoRead";}
	}else { echo "$LDDbNoLink<br>"; }
}

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('nursing');

# Title in toolbar
 #$smarty->assign('sToolbarTitle', "$LDNursing : $LDSearchPatient");
 $smarty->assign('sToolbarTitle', "$LDNursing :: Search a Patient");

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('nursing_how2search.php','$mode','$rows','search')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # OnLoad Javascript code
 #$smarty->assign('sOnLoadJs','onLoad="if (window.focus) window.focus(); document.suchlogbuch.searchkey.select();"');
 $smarty->assign('sOnLoadJs','onLoad="DisabledSearch();"');
 
 # Window bar title
 #$smarty->assign('title',"$LDNursing - $LDSearchPatient");
 $smarty->assign('title',"$LDNursing :: Search a Patient");

 # Collect extra javascript code

 ob_start();
?>

<script language="javascript">
<!--
var urlholder;

	function gotoWard(ward_nr,st,pid,y,m,d){
<?php
	if($cfg['dhtml'])
	{
	echo 'w=window.parent.screen.width; h=window.parent.screen.height;';
	}
	else echo 'w=800;
					h=600;';
?>
	winspecs="menubar=no,resizable=yes,scrollbars=yes,width=" + (w-15) + ", height=" + (h-60);

	urlholder="nursing-station-pass.php?rt=pflege&sid=<?php echo "$sid&lang=$lang"; ?>&pday="+d+"&pmonth="+m+"&pyear="+y+"&edit=1&retpath=search_patient&ward_nr="+ward_nr+"&station="+st+"&pid="+pid;
	window.location.href=urlholder;
}

// -->

//added by VAN 01-05-2011
function isValidSearch(key) {

        if (typeof(key)=='undefined') return false;
        var s=key.toUpperCase();
        return (
                        /^[A-Z0-9Ñ\-\.]{2}[A-Z0-9Ñ\-\. ]*\s*,\s*[A-Z0-9Ñ\-\.]{2}[A-Z0-9Ñ\-\. ]*$/.test(s) ||
                        /^\d{1,2}\/\d{1,2}\/\d{4}$/.test(s) ||
                        /^\d{1,2}\-\d{1,2}\-\d{4}$/.test(s) ||
                        /^\d+$/.test(s)
        );
}

function DisabledSearch(){
        var b=isValidSearch(document.getElementById('searchkey').value);
        document.getElementById("searchButton").style.cursor=(b?"pointer":"default");
        document.getElementById("searchButton").disabled = !b;
}

function chkSearch(d){
            
            if (!isValidSearch(d.searchkey.value)) { 
                d.searchkey.focus();
                return false;
            }else  {
                return true;
            }
            
            //return true;
}
//---------------

</script>

<?php

$sTemp = ob_get_contents();

ob_end_clean();

$smarty->append('JavaScript',$sTemp);

ob_start();

?>
<!--
<ul>
-->

<form action="nursing-patient-such-start.php" method="get" name="suchlogbuch" id="suchlogbuch" onSubmit="return chkSearch(this)">
<table border=0 align="center" cellpadding=2 class="reg_searchmask_border">
	<tr>
		<td>
			<table align="center" cellpadding="5" cellspacing="5" class="reg_searchmask">
			<tbody>
				<tr>
					<!--<td class="prompt"><?php #echo $LDSrcKeyword ?>:<br>-->
					<td>Enter search keyword: e.g. PID, Case No., first name, family name<br>
						<!--<input type="text" name="searchkey" size=40 maxlength=100 value="<?php if ($srcword!='') echo $srcword; ?>">-->
						<input type="text" name="searchkey" id="searchkey" size=40 maxlength=100 value="" onBlur="DisabledSearch();" onKeyUp="DisabledSearch(); if ((event.keyCode == 13)&&(isValidSearch(document.getElementById('searchkey').value))) ; ">
						<input type="hidden" name="sid" value="<?php echo $sid; ?>">
						<input type="hidden" name="lang" value="<?php echo $lang; ?>">
						<input type="hidden" name="mode" value="such"><br>
						<font size=2>
						<input type="checkbox" name="arch" value="1" <?php if($arch) echo "checked"; ?>> <?php echo $LDSearchArchive ?>
						</font>
					</td>
				</tr>
				<tr>
					<td align=right>
					<!--<input type="submit" value="<?php echo $LDSearch ?>" align="right">-->
					<input name="searchButton" id="searchButton" type="image" src="../../gui/img/control/default/en/en_searchlamp.gif" border=0 align="absmiddle" width="72" height="23">
					</td>
				</tr>

			</tbody>
			</table>
		</td>
	</tr>
</table>
</form>

<?php

if($rows){

?>

	<table border=0>
		<tr>
			<td><img <?php echo createMascot($root_path,'mascot1_r.gif','0','bottom') ?> align="absmiddle"></td>
			<td class="prompt">
				<?php echo "$LDSearchKeyword <font color=#0000ff>\"$keyword\"</font> ".str_replace("~rows~",$totalcount,$LDWasFound).' '.$LDShowing.' '.$pagen->BlockStartNr().' '.$LDTo.' '.$pagen->BlockEndNr().'.'; ?> <br>
				<?php echo $LDPlsClk ?>
			</td>
		</tr>
	</table>
<br>
	<table border=0 cellpadding=0 cellspacing=0 width="100%">
		<tr class="adm_item">

<?php

	$bgimg='tableHeaderbg3.gif';
	//$bgimg='tableHeader_gr.gif';
	$tbg= 'background="'.$root_path.'gui/img/common/'.$theme_com_icon.'/'.$bgimg.'"';

	$append="&usenum=$usenum&arch=$arch";

	if($usenum){

?>
			<td><b>
<?php
				#edited by VAN 04-09-08
				#echo $pagen->makeSortLink($LDAdm_Nr,'encounter_nr',$oitem,$odir,$append);
				#echo $pagen->makeSortLink('Case No.','encounter_nr',$oitem,$odir,$append);
                echo "Case No";
?>
			</b>
			</td>
<?php
	}
?>
			 <td><b>
<?php
				#echo $pagen->makeSortLink($LDLastName,'name_last',$oitem,$odir,$append);
                echo $LDLastName;
?>
				</b>
			</td>
			<td><b>
<?php
		#echo $pagen->makeSortLink($LDName,'name_first',$oitem,$odir,$append);
        echo $LDName;
?>			</b>
			</td>
			<td><b>
<?php
		#echo $pagen->makeSortLink($LDBirthDate,'date_birth',$oitem,$odir,$append);
        echo $LDBirthDate;
 ?>
				</b>
			</td>

<?php
	if(!$usenum){
?>
			 <td><b>
<?php
		#echo $pagen->makeSortLink($LDAdm_Nr,'encounter_nr',$oitem,$odir,$append);
		#echo $pagen->makeSortLink('Case No.','encounter_nr',$oitem,$odir,$append);
        echo 'Case No.';
?>
				</b>
			</td>
<?php
	}
?>
			 <td><b>
<?php
		#echo $pagen->makeSortLink($LDStation,'ward_nr',$oitem,$odir,$append);
        echo $LDStation;
?>
				</b>
			</td>
			<td><b>
<?php
		#echo $pagen->makeSortLink($LDRoom,'room_nr',$oitem,$odir,$append);
        echo $LDRoom;
?>
				</b>
			</td>
			<td><b>
<?php
		#edited by VAN 01-28-08
		#echo $pagen->makeSortLink($LDDate,'ward_date',$oitem,$odir,$append);
        echo $LDDate;
?>
				</b>
			</td>

			<td><b>&nbsp; <?php echo $LDStatus ?></b></td>
		</tr>

<?php

	$toggle=0;
	while($result=$ergebnis->FetchRow()){

/*	if($result['encounter_class_nr']==2) $full_enr=$result['encounter_nr']+$GLOBAL_CONFIG['patient_outpatient_nr_adder'];
		else  $full_enr=$result['encounter_nr']+$GLOBAL_CONFIG['patient_inpatient_nr_adder'];
*/
		if ((!$is_reliever)&&($job=='N')){
			$ward_row = $pers_obj->get_Nurse_Ward_Area_Assign($personell_nr, $result['ward_nr']);
			#echo $pers_obj->sql;
			$assigned_ward =$pers_obj->count;
		}elseif ((($is_reliever)&&($job=='N'))||($allow_all)){
			$assigned_ward =1;
		}else{
			$assigned_ward =0;
		}

		$full_enr=$result['encounter_nr'];
	echo'
		<tr ';
		if($toggle){
			echo "bgcolor=#efefef";
		$toggle=0;
	}else{
		echo "bgcolor=#ffffff";
		$toggle=1;
	}

	#echo "<br>";
	#print_r($result);
	#echo "date = ".$result['ward_date'];
	#echo "<br>date = ".$result['encounter_nr'];

	#edited by VAN 02-05-08
	#if($result['in_ward']) $result['ward_date']=date('Y-m-d');
	if($result['is_discharged']!=1) $result['ward_date']=date('Y-m-d');

	list($pyear,$pmonth,$pday)=explode('-',$result['ward_date']);

	//$buf="nursing-station.php".URL_APPEND."&station=".$result['ward_name']."&ward_nr=".$result['ward_nr'];
	//$buf="nursing-station.php".URL_APPEND."&ward_nr=".$result['ward_nr']."&pyear=$pyear&pmonth=$pmonth&pday=$pday";

	$buf="javascript:gotoWard('".$result['ward_nr']."','".addslashes($result['ward_name'])."','".$result['pid']."','$pyear','$pmonth','$pday')";

	echo '>';
/*  echo '
		<td>&nbsp; &nbsp;<a href="'.$buf.'" title="'.$LDClk2Show.'">';
	if($result['s_date'] <> (date('Y-m-d'))) echo '<img '.createComIcon($root_path,'bul_arrowblusm.gif','0').'>';
		else echo '<img '.createComIcon($root_path,'r_arrowgrnsm.gif','0').'>';
	echo'
	</a></td>';
*/

	if($usenum){
		if ($result['is_discharged']){
			echo '
				<td>&nbsp; &nbsp;'.$full_enr.'&nbsp;</td>';
		}else{
			echo '
				<td>&nbsp; &nbsp;<a href="'.$buf.'" title="'.$LDClk2Show.'">'.$full_enr.'</a>&nbsp;</td>';
		}
	}

	#if ($result['is_discharged']){
	#if (($result['is_discharged'])||(($ward_nr!=$result['ward_nr'])&&($ward_nr!=0))){
	if (($result['is_discharged'])||(!$assigned_ward)){
		echo '
		 <td>&nbsp; &nbsp;'.$result['name_last'].'&nbsp;</td>
			<td>&nbsp; &nbsp;'.$result['name_first'].'&nbsp;</td>
		 <td>&nbsp;'.formatDate2Local($result['date_birth'],$date_format).'</td>';
	}else{
		echo '
		 <td>&nbsp; &nbsp;<a href="'.$buf.'" title="'.$LDClk2Show.'">'.$result['name_last'].'</a>&nbsp;</td>
			<td>&nbsp; &nbsp;<a href="'.$buf.'" title="'.$LDClk2Show.'">'.$result['name_first'].'</a>&nbsp;</td>
		 <td>&nbsp;'.formatDate2Local($result['date_birth'],$date_format).'</td>';
	}

	if(!$usenum){
	echo '
		<td>&nbsp; &nbsp;'.$full_enr.'&nbsp;</td>';
	}

	#if ($result['is_discharged']){
	#if (($result['is_discharged'])||(($ward_nr!=$result['ward_nr'])&&($ward_nr!=0))){
	if (($result['is_discharged'])||(!$assigned_ward)){
		echo '
			<td>&nbsp; &nbsp;'.$result['ward_name'].'&nbsp;</td>
			<td>&nbsp; &nbsp;';
	}else{
		echo '
			<td>&nbsp; &nbsp;<a href="'.$buf.'" title="'.$LDClk2Show.'">'.$result['ward_name'].'</a>&nbsp;</td>
		 <td>&nbsp; &nbsp;';
	}
	if($result['room_nr']) echo $result['roomprefix'].' '.$result['room_nr'];
	echo '&nbsp;</td>
		<td>&nbsp; '.formatDate2Local($result['ward_date'],$date_format).'</td>
		<td>&nbsp; ';
	#commented by VAN 01-28-08
	#if($result['in_ward']) echo $LDInWard;
	#echo "is_discharged : ".$result['is_discharged'];

	if($result['is_discharged'])
		echo "Discharged";
	else
		echo $LDInWard;

	echo '</td>
	</tr>
	<tr bgcolor=#0000ff>
	<td colspan=8 height=1><img '.createComIcon($root_path,'pixel.gif','0','absmiddle').'></td>
	</tr>';
	}

	echo '
		<tr><td colspan=7>'.$pagen->makePrevLink($LDPrevious,$append).'</td>
		<td align=right>'.$pagen->makeNextLink($LDNext,$append).'</td>
		</tr>';
 ?>
</table>
<p>
<hr>
<?php
}else{
	if($mode=='such') echo str_replace('~nr~','0',$LDSearchFound);
}
?>

<!--</ul>-->
<!--
<p>
<ul>

<b><?php echo $LDMoreFunctions ?>:</b><br>
<img <?php echo createComIcon($root_path,'varrow.gif','0') ?>> <a href="nursing-station-archiv.php?sid=<?php echo "$sid&lang=$lang";?>&user=<?php echo str_replace(" ","+",$user);?>"><?php echo $LDArchive ?></a><br>
<img <?php echo createComIcon($root_path,'varrow.gif','0') ?>> <a href="javascript:gethelp('nursing_how2search.php','<?php echo $mode ?>','<?php echo $rows ?>','search')"><?php echo $LDHow2Search ?></a><br>

<p>
<a href="nursing.php<?php echo URL_APPEND; ?>"><img <?php echo createLDImgSrc($root_path,'cancel.gif','0') ?>  alt="<?php echo $LDCancel ?>"></a>
</ul>
-->
<?php

$sTemp = ob_get_contents();
ob_end_clean();

# Assign the page output to the mainframe center block

 $smarty->assign('sMainFrameBlockData',$sTemp);

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');

 ?>