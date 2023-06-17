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

# Default value for the maximum nr of rows per block displayed, define this to the value you wish
# In normal cases this value is derived from the db table "care_config_global" using the "pagin_insurance_list_max_block_rows" element.
define('MAX_BLOCK_ROWS',30); 

$lang_tables[]='search.php';
define('LANG_FILE','aufnahme.php');
define('NO_2LEVEL_CHK',1);
# Resolve the local user based on the origin of the script
require_once('include/inc_local_user.php');
require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'include/inc_date_format_functions.php');

require_once($root_path.'include/care_api_classes/class_encounter.php');
$enc_obj=new Encounter();

require_once($root_path.'include/care_api_classes/class_person.php');
$person_obj=new Person();

global $ptype, $allow_patient_register, $allow_newborn_register, $allow_er_user, 
       $allow_opd_user, $allow_ipd_user, $allow_medocs_user, $allow_update,
       $allow_accessOPD, $allow_accessER, $allow_accessIPD;

#echo "b4 serptype = ".$ptype;

if ($HTTP_SESSION_VARS['ptype'])
	$ptype = $HTTP_SESSION_VARS['ptype'];

#echo "<br>Aserptype = ".$ptype;

# Set break file
require('include/inc_breakfile.php');

$toggle=0;

 /* Set color values for the search mask */
$searchmask_bgcolor='#f3f3f3';
$searchprompt=$LDEntryPrompt;
$entry_block_bgcolor='#fff3f3';
$entry_border_bgcolor='#6666ee';
$entry_body_bgcolor='#ffffff';

#echo "searchkey 1 = '".$searchkey."' <br> \n";

if(!isset($searchkey)) $searchkey='';
if(!isset($mode)) $mode='';

# Initialize page�s control variables
if($mode=='paginate'){
	$searchkey=$HTTP_SESSION_VARS['sess_searchkey'];
}else{
	# Reset paginator variables
	$pgx=0;
	$totalcount=0;
	$odir='ASC';
	$oitem='name_last';
}

# burn added: March 13, 2007
require_once($root_path.'include/care_api_classes/class_department.php');
$dept_obj=new Department;
#$user_dept_info = $dept_obj->getUserDeptInfo($HTTP_SESSION_VARS['sess_user_name']);
	if (!empty($HTTP_SESSION_VARS['sess_login_userid']))
		$seg_user_name = $HTTP_SESSION_VARS['sess_login_userid'];
	else
		$seg_user_name = $HTTP_SESSION_VARS['sess_temp_userid'];
	$user_dept_info = $dept_obj->getUserDeptInfo($seg_user_name);

if (($allow_accessER)&&($allow_accessOPD)&&($allow_accessIPD)){
    $sql_type = 'AND (enc.encounter_type IN (1,2,3,4))';
}elseif (($allow_accessER)&&($allow_accessOPD)){
    $sql_type = 'AND (enc.encounter_type IN (1,2))';
}elseif (($allow_accessER)&&($allow_accessIPD)){
    $sql_type = 'AND (enc.encounter_type IN (1,3,4))';
}elseif (($allow_accessOPD)&&($allow_accessIPD)){
    $sql_type = 'AND (enc.encounter_type IN (2,3,4))';    
}elseif ($allow_accessOPD){
	$sql_type = 'AND (enc.encounter_type IN (2))';
}elseif ($allow_accessIPD){
	$sql_type = 'AND (enc.encounter_type IN (3,4))';
}elseif ($allow_accessER){ 
	$sql_type = 'AND (enc.encounter_type IN (1))';
}/*else{
	$sql_type = 'AND (enc.encounter_type NOT IN (1,2,3,4)) AND enc.encounter_type IS NULL';
}*/

/*
if ($allow_accessOPD){
	$encounter_type_search='2';   # search under OPD Triage 
}elseif ($allow_accessER){
	$encounter_type_search='1';   # search under ER Triage
}elseif ($allow_accessIPD){
	$encounter_type_search='3,4';   # search under Admitting Section or Medical Records
}elseif (($allow_accessER)&&($allow_accessOPD)&&($allow_accessIPD)){
	$encounter_type_search='1,2,3,4';   # User has no permission to use Admission Search
	
	if ($user_dept_info['dept_nr'])
		$sql_ext = " AND enc.current_dept_nr='".$user_dept_info['dept_nr']."' ";
}else{
    $encounter_type_search = '';
}
*/

#Load and create paginator object
require_once($root_path.'include/care_api_classes/class_paginator.php');
$pagen=new Paginator($pgx,$thisfile,$HTTP_SESSION_VARS['sess_searchkey'],$root_path);
//$db->debug=true;

if(($mode=='search'||$mode=='paginate')&&($searchkey))
{
	$searchkey=strtr($searchkey,'*?','%_');
	# Save the search keyword for eventual pagination routines
	if($mode=='search') $HTTP_SESSION_VARS['sess_searchkey']=$searchkey;
	
		include_once($root_path.'include/care_api_classes/class_globalconfig.php');
        $glob_obj=new GlobalConfig($GLOBAL_CONFIG);
        $glob_obj->getConfig('patient_%');
        $glob_obj->getConfig('pagin_%');   # burn added : July 14, 2007

		# Get the max nr of rows from global config
		//$glob_obj->getConfig('pagin_patient_search_max_block_rows');
		if(empty($GLOBAL_CONFIG['pagin_patient_search_max_block_rows'])) $pagen->setMaxCount(MAX_BLOCK_ROWS); # Last resort, use the default defined at the start of this page
			else $pagen->setMaxCount($GLOBAL_CONFIG['pagin_patient_search_max_block_rows']);

			$suchwort=trim($searchkey);
			#echo "suchwort = ".$suchwort;
			#added by VAN 02-15-08
			if ($suchwort{0}=='T'){
				$suchwort = str_replace('T','',$suchwort);
				$isPid = 1;
			}
			
			$HTTP_SESSION_VARS['key'] = $suchwort;
			if(is_numeric($suchwort))
			{
				#$suchwort=(int) $suchwort;
				$numeric=1;
				//if($suchwort < $patient_inpatient_nr_adder) $suchbuffer=$suchwort+$patient_inpatient_nr_adder; else $suchbuffer=$suchwort;
				$suchbuffer=$suchwort;
				
				if (isset($_POST['option_pid']) && $_POST['option_pid']){
					#edited by VAN 02-29-08
					if($isPid){
                        #edited by VAN 06-08-09
						if (strlen(trim($searchkey))<10)
                            $sql2.=" reg.pid $sql_LIKE '%".addslashes(trim($searchkey))."%'";
                        else
                            $sql2.=" enc.encounter_nr = '".addslashes($suchbuffer)."'";    
                        $isTemp = 1;
					}else{
                        #edited by VAN 06-08-09  
                        if (strlen(trim($searchkey))<10)  
						    $sql2.=" reg.pid = '".addslashes($suchbuffer)."'";
                        else
                            $sql2.=" enc.encounter_nr = '".addslashes($suchbuffer)."'";      
                        $isTemp = 0;
					}
					
				}else{
					#edited by VAN 02-29-08
					$suchbuffer = addslashes($suchbuffer);
					if ($suchbuffer{0}!='T'){
						$sql2.=" enc.encounter_nr = '".addslashes($suchbuffer)."'";
					}
					$isTemp = 0;
				}	
			}
			
			$sql='SELECT enc.encounter_nr,
								enc.encounter_class_nr, 
								enc.encounter_type, 
								enc.is_discharged,
								enc.encounter_date,
								enc.admission_dt,
                                enc.encounter_status,
								IF(enc.encounter_type<3,enc.encounter_date,enc.admission_dt) AS date,
								reg.pid,
								reg.fromtemp,
								reg.name_last, 
								reg.name_first, 
								reg.date_birth, 
								reg.sex,
								reg.death_date, reg.death_encounter_nr '.
					" , (SELECT id FROM care_department AS dept WHERE dept.nr = enc.consulting_dept_nr) AS consulting_dept_name ". # burn added: July 14, 2007
					" , (SELECT id FROM care_department AS dept WHERE dept.nr = enc.current_dept_nr) AS current_dept_name "; # burn added: July 14, 2007
			
			$dbtable ='
			          FROM 	care_person AS reg
					  	  LEFT JOIN	care_encounter AS enc ON enc.pid=reg.pid
					  WHERE  ';

			#----------------------added by VAN 02-19-08
			if(!$numeric){
			if(stristr($searchkey,',')){
				$lastnamefirst=TRUE;
			}else{
				$lastnamefirst=FALSE;
			}
			
			#$searchkey=strtr($searchkey,',',' ');
			
			#$cbuffer=explode(' ',$searchkey);
			$cbuffer=explode(',',$searchkey);
#print_r($cbuffer);
			# Remove empty variables
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
			
			if(empty($oitem)) $oitem='name_last';			
			
			# Check the size of the comp
			if(sizeof($comp)>1){
				$cntlast = sizeof($cbuffer)-1;
				if (sizeof($cbuffer) > 2){
					$sql2.=" (((reg.name_last $sql_LIKE '".strtr($ln,'+',' ')."%' ) AND reg.name_first $sql_LIKE '".strtr($fn,'+',' ')."%') /*OR (reg.name_last $sql_LIKE '%".$searchkey."%' OR reg.name_first $sql_LIKE '%".$searchkey."%')*/)";
					$bd=$comp[sizeof($cbuffer)];				
					#added by VAN 02-29-08
					$sql5 = " (((reg.name_last $sql_LIKE '".strtr($ln,'+',' ')."%' OR reg.name_last $sql_LIKE '".strtr($comp[$cntlast],'+',' ')."%') AND reg.name_first $sql_LIKE '".strtr($fn,'+',' ')."%') /*OR (reg.name_last $sql_LIKE '%".$searchkey."%' OR reg.name_first $sql_LIKE '%".$searchkey."%')*/)";				
				}else{
						$sql2.=" ((reg.name_last $sql_LIKE '".strtr($ln,'+',' ')."%' AND reg.name_first $sql_LIKE '".strtr($fn,'+',' ')."%') /*OR (reg.name_last $sql_LIKE '".$searchkey."%' OR reg.name_first $sql_LIKE '%".$searchkey."%')*/)";
						#added by VAN 02-29-08
						$sql5 = " ((reg.name_last $sql_LIKE '".strtr($ln,'+',' ')."%' AND reg.name_first $sql_LIKE '".strtr($fn,'+',' ')."%') /*OR (reg.name_last $sql_LIKE '".$searchkey."%' OR reg.name_first $sql_LIKE '%".$searchkey."%')*/)";	
				}
			}else{
					$sql2.= "( reg.name_last $sql_LIKE '".addslashes($suchwort)."%'
								/*OR reg.name_first $sql_LIKE '%".addslashes($suchwort)."%'*/)";
					$sql5 = "( reg.name_last $sql_LIKE '".addslashes($suchwort)."%'
								/*OR reg.name_first $sql_LIKE '%".addslashes($suchwort)."%'*/)";			
			}	
			}
			#------------------------------------					
								
			#$sql2.="  AND enc.encounter_status<>'cancelled'
			#		  $sql_type ".
			#		  #AND (enc.in_ward  NOT IN ('',0) OR enc.in_dept NOT IN ('',0))
			#		  "AND enc.status NOT IN ('void','hidden','deleted','inactive') 
			#		   /*AND reg.death_date = '0000-00-00'*/ ";
            
            #edited by VAS  12-20-2011
            //edited by jasper 08/30/2013 -NOT TO INCLUDE WELLBABY OR ENCOUNTER_TYPE=12
            $sql2.=   $sql_type.
                      "AND enc.status NOT IN ('hidden','deleted','inactive')";

		if (isset($_POST['option_icd']))
			$sql2.= "AND enc.encounter_nr NOT IN (SELECT enc_d.encounter_nr FROM care_encounter_diagnosis AS enc_d) ";
		if (isset($_POST['option_icpm']))
			$sql2.= "AND enc.encounter_nr NOT IN (SELECT enc_p.encounter_nr FROM care_encounter_procedure AS enc_p) ";

		#added by VAN 06-10-08
		if (((isset($_POST['option_wicd_discharge']))&&($_POST['option_wicd_discharge']==1)) || ((isset($_POST['option_woicd_discharge']))&&($_POST['option_woicd_discharge']==1))){
			if ((isset($_POST['option_wicd_discharge']))&&($_POST['option_wicd_discharge']==1)){
				#discharge with icd
				#echo "discharged with icd";
				$sql2.= "AND enc.encounter_nr IN (SELECT enc_d.encounter_nr FROM care_encounter_diagnosis AS enc_d) AND is_discharged=1";
			}elseif ((isset($_POST['option_woicd_discharge']))&&($_POST['option_woicd_discharge']==1)){
				#discharge without icd
				#echo "discharged without icd";
				$sql2.= "AND enc.encounter_nr NOT IN (SELECT enc_d.encounter_nr FROM care_encounter_diagnosis AS enc_d) AND is_discharged=1";	
			}	
		}
		
		if (((isset($_POST['option_wicd_admitted']))&&($_POST['option_wicd_admitted']==1)) || ((isset($_POST['option_woicd_admitted']))&&($_POST['option_woicd_admitted']==1))){
			if ((isset($_POST['option_wicd_admitted']))&&($_POST['option_wicd_admitted']==1)){
				#echo "admitted with icd";
				#discharge with icd
				$sql2.= "AND enc.encounter_nr IN (SELECT enc_d.encounter_nr FROM care_encounter_diagnosis AS enc_d) AND is_discharged=0";
			}elseif ((isset($_POST['option_woicd_admitted']))&&($_POST['option_woicd_admitted']==1)){
				#echo "admitted without icd";
				#discharge without icd
				$sql2.= "AND enc.encounter_nr NOT IN (SELECT enc_d.encounter_nr FROM care_encounter_diagnosis AS enc_d) AND is_discharged=0";	
			}	
		}
		#-------------------------
		#edited by VAN 07-28-08
		$sql3=" ORDER BY name_last ASC, name_first ASC, enc.encounter_date DESC";
		
		#added by VAN 02-29-08
		$key = addslashes(trim($searchkey));
		if ($isTemp){
			if($isPid)
				$sql4 = " OR (reg.pid like '".addslashes(trim($searchkey))."%')";
			else
				$sql4 = " OR ((reg.pid like 'T%') AND ".$sql5.")";	
		}else{
			
			#if searckey is not PID
			#wildcard
			if (addslashes(trim($searchkey))=='%')
				$sql4 = " OR (reg.pid like 'T%')";
			#encounter	but the key is temp
			elseif ((is_numeric($key))&&($key{0}=='T'))
				$sql4 = " ";
			#encounter	
			elseif (is_numeric($key)){
				if (isset($_POST['option_pid']) && $_POST['option_pid']){
					$person2 = $person_obj->getPersonInfo($key);
                    ////edited by jasper 08/30/2013 -NOT TO INCLUDE WELLBABY OR ENCOUNTER TYPE 12
					if ($person2['fromtemp'])
						$sql4 = " OR (reg.pid = '".$key."' AND enc.encounter_type <> 12 AND reg.fromtemp=1) ";
					else	
						$sql4 = " ";	
				}else{	
					$person = $enc_obj->getEncounterInfo($key);
					if ($person['fromtemp'])
						$sql4 = " OR (enc.encounter_nr = '".$key."' AND reg.fromtemp=1) ";
				else	
					$sql4 = " ";
				}
				#echo "<br>person ".$person['fromtemp']."<br>";
				
			#character / name
			}elseif (is_string($key)){	
				$person2 = $person_obj->getPersonInfo($key);
				#echo "person = ".$person2['fromtemp'];
				if ($person2['fromtemp'])
					$sql4 = " OR ".$sql5;
				else
					 $sql4 = " ";
			}
		}		

#added by VAN 02-29-08
$sql_fin = $sql.$dbtable.'('.$sql2.')'.$sql4.$sql3;
		#edited by VAN 02-29-08
		#if($ergebnis=$db->SelectLimit($sql.$dbtable.$sql2.$sql3,$pagen->MaxCount(),$pagen->BlockStartIndex())){
	#echo "sql = ".$sql_fin;	
		if($ergebnis=$db->SelectLimit($sql_fin,$pagen->MaxCount(),$pagen->BlockStartIndex())){
				if ($linecount=$ergebnis->RecordCount())
				{ 
					if(($linecount==1)&&$numeric&&$mode=='search')
					{
						$zeile=$ergebnis->FetchRow();
							# burn added : May 18, 2007
						if($zeile['encounter_type']==1 || $zeile['encounter_type']==2 || $zeile['encounter_type']==12) 
							$tabs=0; //default table -> care_encounter_diagnosis & care_encounter_procedure
						else $tabs=1; // final diagnosis & procedure -> seg_encounter_icd & seg_encounter_icp
#echo "zeile : <br>"; print_r($zeile);  echo "<br> \n";
						#added by VAN 02-29-08
						
						if (empty($zeile['encounter_nr'])){
							$zeile['encounter_nr'] = 0;
							unset($HTTP_SESSION_VARS['sess_full_en']);
							unset($HTTP_SESSION_VARS['sess_en']);
						}	
						
						if (empty($zeile['encounter_type']))
							$zeile['encounter_type'] = 0;
						
						if (empty($zeile['encounter_class_nr']))
							$zeile['encounter_class_nr'] = 0;	
							#echo "here";
						header('location:show_medocs.php'.URL_APPEND.'&from=such&pid='.$zeile['pid'].'&encounter_nr='.$zeile['encounter_nr'].'&target=entry&tabs='.$tabs.'&encounter_type='.$zeile['encounter_type'].'&encounter_class_nr='.$zeile['encounter_class_nr'].'fromtemp='.$zeile['fromtemp']);

						#header("location:aufnahme_daten_zeigen.php".URL_REDIRECT_APPEND."&from=such&target=search&pid=".$zeile['pid']."&encounter_nr=".$zeile['encounter_nr']."&sem=".(!$zeile['is_discharged']));   # burn commented : May 18, 2007
						exit;
					}
					
					$pagen->setTotalBlockCount($linecount);
					
					# If more than one count all available
					if(isset($totalcount)&&$totalcount){
						$pagen->setTotalDataCount($totalcount);
					}else{
						# Count total available data
						#edited by VAN 02-29-08
						#$sql='SELECT COUNT(enc.encounter_nr) AS maxnr '.$dbtable.$sql2;
						$sql='SELECT COUNT(reg.pid) AS maxnr '.$dbtable.'('.$sql2.')'.$sql4;
						//$sql='SELECT enc.encounter_nr '.$dbtable.$sql2;
						#echo $sql;
						if($result=$db->Execute($sql)){
							if ($result->RecordCount()) {
								$rescount=$result->FetchRow();
    								$totalcount=$rescount['maxnr'];
    							}
							//$totalcount=$result->RecordCount();
						}
						$pagen->setTotalDataCount($totalcount);
						//echo $totalcount;
					}
					# Set the sort parameters
					$pagen->setSortItem($oitem);
					$pagen->setSortDirection($odir);
				}
			}
			 else {echo "<p>".$sql."<p>$LDDbNoRead";};
}else{
	$mode='';
}

# Start Smarty templating here
 /**
 * LOAD Smarty
 */
 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

if($parent_admit) $sTitleNr= ($HTTP_SESSION_VARS['sess_full_en']);
	else $sTitleNr = ($HTTP_SESSION_VARS['sess_full_pid']);

# Title in the toolbar
 $smarty->assign('sToolbarTitle',"Medocs :: $LDSearch ");

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('medocs_search.php')");

 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('title',"Medocs :: $LDSearch ");

 # Onload Javascript code
 $smarty->assign('sOnLoadJs','onLoad="document.searchform.searchkey.select()"');

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('medocs_entry.php')");

  # hide return button
 $smarty->assign('pbBack',FALSE);

# Load tabs

$target='search';
require('./gui_bridge/default/gui_tabs_medocs.php');

# Buffer page output

ob_start();

?>

<ul>
	<table border=0 cellpadding=10 bgcolor="<?php echo $entry_border_bgcolor ?>">
		<tr>
			<td>
				<?php
					$seg_show_ICD_ICPM_options = true;   # burn added : May 3, 2007
						include($root_path.'include/inc_patient_searchmask.php');
					$seg_show_ICD_ICPM_options = false;   # burn added : May 3, 2007

				?>
		</td>
     </tr>
   </table>

<p>
<a href="<?php echo $breakfile; ?>"><img <?php echo createLDImgSrc($root_path,'cancel.gif','0') ?>></a>
<p>

<?php
if($mode=='search'||$mode=='paginate'){
	
	if ($linecount) echo '<hr width=80% align=left>'.str_replace("~nr~",$totalcount,$LDSearchFound).' '.$LDShowing.' '.$pagen->BlockStartNr().' '.$LDTo.' '.$pagen->BlockEndNr().'.';
		else echo str_replace('~nr~','0',$LDSearchFound); 
	
    echo "<br>Legend : <span><b><font color=\"#ff9966\">cancelled transaction</font></b></span>";  
	if ($linecount) { 

		# Load the common icons
		$img_options=createComIcon($root_path,'statbel2.gif','0');
	 	$img_male=createComIcon($root_path,'spm.gif','0');
		$img_female=createComIcon($root_path,'spf.gif','0');
		#undefined
		$img_undefined=createComIcon($root_path,'notepad.gif','0');
		$bgimg='tableHeaderbg3.gif';
		$tbg= 'background="'.$root_path.'gui/img/common/'.$theme_com_icon.'/'.$bgimg.'"';

		echo '
			<table border=0 cellpadding=2 cellspacing=1>
			<tr>
				<td colspan=8>'.$pagen->makePrevLink($LDPrevious).'</td>
				<td align=right>'.$pagen->makeNextLink($LDNext).' &nbsp;&nbsp;</td>
			</tr>
			<tr class="adm_list_titlebar">';
			
?>
		<!--added by VAN 02-29-08 -->
        <!-- edited by VAN 12-20-2011 -->
	    <td><b> Patient ID</b></td>
		<td width="10%"><b><?php echo $LDCaseNr ?></b></td>
		<td><b>Status</font></b></td>
		<td width="10%"><b>Encounter Date</b></td>
		<td><b>Clinic/Department</b></td>
		<td><b> <?php echo $LDSex;  ?></b></td>
		<td><b> <?php echo $LDLastName;  ?></b></td>
		<td><b> <?php echo $LDFirstName;  ?></b></td>
		<td><b> <?php echo $LDBday;  ?></b></td>
		<td ><b><font color="#000066"><?php echo $LDSelect; ?></font></b></td>

<?php
					echo"</tr>";

					while($zeile=$ergebnis->FetchRow())
					{	
                        $encounter_status = $zeile['encounter_status']; 
						#added by VAN 02-29-08
						if (empty($zeile['encounter_nr'])){
							$zeile['encounter_nr'] = 0;
							unset($HTTP_SESSION_VARS['sess_full_en']);
							unset($HTTP_SESSION_VARS['sess_en']);
						}	
						if (empty($zeile['encounter_type']))
							$zeile['encounter_type'] = 0;
						if (empty($zeile['encounter_class_nr']))
							$zeile['encounter_class_nr'] = 0;	
							
						$full_en=$zeile['encounter_nr'];
						
						if ($encounter_status=='cancelled')
                            $fontcolor = '#ff9966';
						else
                            $fontcolor = '#ffffff';
                            
						echo "
							<tr bgcolor=";
						#if($toggle) { echo "#efefef>"; $toggle=0;} else {echo "#ffffff>"; $toggle=1;};
                        #if($toggle) { echo "#efefef>"; $toggle=0;} else {echo "$fontcolor>"; $toggle=1;};
						echo "$fontcolor>";
						#added by VAN 02-29-08
						echo "<td>";	
						echo "&nbsp;".$zeile['pid'];
                  echo "</td>";

						#edited by VAN 02-29-08
						if ($full_en==0)
							$full_en = 'No Transaction';
							
						echo "<td>";	
						echo "&nbsp;".$full_en;
                  echo "</td>";
						
						echo "<td>";	
						#commented by VAN 02-29-08
						#if($zeile['encounter_class_nr']==2) 
							#echo ' <img '.createComIcon($root_path,'redflag.gif').'> <font size=1 color="red">'.$LDAmbulant.'</font>';   # burn commented: March 13, 2007
						if($zeile['encounter_type']==1)   # burn added: March 13, 2007
							echo ' <img '.createComIcon($root_path,'redflag.gif').'> <font size=1 color="red">ER</font>';
						elseif($zeile['encounter_type']==2) 
							echo ' <img '.createComIcon($root_path,'redflag.gif').'> <font size=1 color="blue">OPD</font>';
						#edited by VAN 02-29-08
						elseif(($zeile['encounter_type']==3)||($zeile['encounter_type']==4))
							echo ' <img '.createComIcon($root_path,'redflag.gif').'> <font size=1 color="green">IPD</font>';
						else	
							echo ' <img '.createComIcon($root_path,'redflag.gif').'> <font size=1 color="orange">TMP</font>';	
						echo "</td>";
						
						if (empty($zeile['date']))
							$encdate = 'No Transaction';
						else
							$encdate = formatDate2Local($zeile['date'],$date_format,1);	
						echo "<td>";
						echo "&nbsp;".$encdate;
                  echo "</td>";	
						
						if (empty($zeile['current_dept_name']))
							$dept = 'No Transaction';
						else
							$dept = $zeile['current_dept_name'];	
						echo"<td>";
						echo "&nbsp;".$dept;
                  echo "</td>";	
						#notepad
						echo"<td>";
						switch($zeile['sex']){
							case 'f': echo '<img '.$img_female.'>'; break;
							case 'm': echo '<img '.$img_male.'>'; break;
							default: echo '<img '.$img_undefined.'>'; break;
							
							#default: echo '&nbsp;'; break;
						}	
						#echo "name '".$zeile['name_last']."'";
						if (empty($zeile['name_last']))
							$lastname = 'No Name';
						else	
							$lastname = ucfirst($zeile['name_last']);
						echo"</td><td>";
						echo "&nbsp;".$lastname;
                        echo "</td>";	
						
						if (empty($zeile['name_first']))
							$firstname = 'No Name';
						else	
							$firstname = ucfirst($zeile['name_first']);
									
						echo"<td>";
						echo "&nbsp;".$firstname;

						# If person is dead show a black cross
						#if($zeile['death_date']&&$zeile['death_date']!=$dbf_nodate) 
						#edited by VAN 04-12-09
						if($zeile['death_date']&&$zeile['death_date']!=$dbf_nodate && ($zeile['encounter_nr']==$zeile['death_encounter_nr'])) 
							echo '&nbsp;<img '.createComIcon($root_path,'blackcross_sm.gif','0','absmiddle').'>';						
                  echo "</td>";	

						if ($zeile['date_birth']=='0000-00-00'){
							$date_birth='No Date';
						}else{	
							# burn added: July 14 2007
							$date_birth = @formatDate2Local($zeile['date_birth'],$date_format);			
							$bdateMonth = substr($date_birth,0,2);
							$bdateDay = substr($date_birth,3,2);
							$bdateYear = substr($date_birth,6,4);
							if (!checkdate($bdateMonth, $bdateDay, $bdateYear)){
								# invalid birthdate
								$date_birth='';
							}
						}
						echo"<td>";
#						echo "&nbsp;".formatDate2Local($zeile['date_birth'],$date_format);
						echo "&nbsp;".$date_birth;
                  echo "</td>";	
						
                        // mark added: March 24, 2007
                        //1-ER,2-OPD , 3 & 4 IPD
                        if($zeile['encounter_type']==1 || $zeile['encounter_type']==2 ) 
                        	$tabs=0; //default table -> care_encounter_diagnosis & care_encounter_procedure
                        else $tabs=1; // final diagnosis & procedure -> seg_encounter_icd & seg_encounter_icp
                                                
					    if($HTTP_COOKIE_VARS[$local_user.$sid]) echo '
						<td>&nbsp;
							<a href=show_medocs.php'.URL_APPEND.'&from=such&pid='.$zeile['pid'].'&encounter_nr='.$zeile['encounter_nr'].'&target=entry&tabs='.$tabs.'&encounter_type='.$zeile['encounter_type'].'&encounter_class_nr='.$zeile['encounter_class_nr'].'&fromtemp='.$zeile['fromtemp'].'>
							<img '.$img_options.' alt="'.$LDShowData.'"></a>&nbsp;';
							
                       if(!file_exists($root_path.'cache/barcodes/en_'.$full_en.'.png'))
	      		       {
			               echo "<img src='".$root_path."classes/barcode/image.php?code=".$full_en."&style=68&type=I25&width=180&height=50&xres=2&font=5&label=2&form_file=en' border=0 width=0 height=0>";
		               }
						echo '</td></tr>';

					}
					echo '
							<tr>
								<td colspan=8>'.$pagen->makePrevLink($LDPrevious).'</td>
								<td align=right>'.$pagen->makeNextLink($LDNext).'&nbsp;&nbsp;</td>
							</tr>
						</table>';
					if($linecount>$pagen->MaxCount())
					{
					    /* Set the appending nr for the searchform */
					    $searchform_count=2;
					?>
			<p>
		 <table border=0 cellpadding=10 bgcolor="<?php echo $entry_border_bgcolor ?>">
     <tr>
       <td>
	   <?php
		   $seg_show_ICD_ICPM_options = true;   # burn added : May 3, 2007
            include($root_path.'include/inc_patient_searchmask.php');
		   $seg_show_ICD_ICPM_options = false;   # burn added : May 3, 2007
	   ?>
		</td>
     </tr>
   </table>
</ul>
<?php
					}
	}
}

$sTemp = ob_get_contents();
ob_end_clean();

$smarty->assign('sMainDataBlock',$sTemp);

$smarty->assign('sMainBlockIncludeFile','medocs/main_plain.tpl');

$smarty->display('common/mainframe.tpl');
?>
