<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

# Default value for the maximum nr of rows per block displayed, define this to the value you wish
# In normal cases this value is derived from the db table "care_config_global" using the "pagin_insurance_list_max_block_rows" element.
define('MAX_BLOCK_ROWS',30); 
#define('LANG_FILE','aufnahme.php');
define('LANG_FILE','social_service.php');

$local_user='aufnahme_user';

require($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'include/inc_date_format_functions.php');

$thisfile=basename(__FILE__);
$toggle=0;

#added by VAN 04-04-08
$breakfile = 'social_service_main.php';
#commented by VAN 04-04-08
#if($HTTP_COOKIE_VARS['ck_login_logged'.$sid]) $breakfile=$root_path.'main/startframe.php'.URL_APPEND;
#	else $breakfile='aufnahme_pass.php'.URL_APPEND.'&target=entry';


# burn added: March 9, 2007
require_once($root_path.'include/care_api_classes/class_department.php');
$dept_obj=new Department;
#$user_dept_info = $dept_obj->getUserDeptInfo($HTTP_SESSION_VARS['sess_user_name']);
	if (!empty($HTTP_SESSION_VARS['sess_login_userid']))
		$seg_user_name = $HTTP_SESSION_VARS['sess_login_userid'];
	else
		$seg_user_name = $HTTP_SESSION_VARS['sess_temp_userid'];
	$user_dept_info = $dept_obj->getUserDeptInfo($seg_user_name);

/*	
//check for user department  
if ($user_dept_info['dept_nr']==150){
	$encounter_type_search='2';   # search under OPD Triage
}elseif($user_dept_info['dept_nr']==149){
	$encounter_type_search='1';   # search under ER Triage
}elseif(($user_dept_info['dept_nr']==148)||($user_dept_info['dept_nr']==151)){
	$encounter_type_search='1,2,3,4';   # search under Admitting Section or Medical Records
}else{
	$encounter_type_search=0;   # User has no permission to use Admission Search
}
*/
$encounter_type_search = '1,2,3,4';

# Set value for the search mask
#$searchprompt=$LDEntryPrompt;   # transferred below

# Special case for direct access from patient listings
# If forward nr ok, use it as searchkey
if(isset($fwd_nr)&&$fwd_nr&&is_numeric($fwd_nr)){
	$searchkey=$fwd_nr;
	$mode='search';
}else{
	if(!isset($searchkey)) $searchkey='';
}

if(!isset($mode)) $mode='';

# Initialize page�s control variables
if($mode=='paginate'){
	$searchkey=$HTTP_SESSION_VARS['sess_searchkey'];
}else{
	# Reset paginator variables
	$pgx=0;
	$totalcount=0;
	$odir='';
	$oitem='';
}

#Load and create paginator object
require_once($root_path.'include/care_api_classes/class_paginator.php');
$pagen=new Paginator($pgx,$thisfile,$HTTP_SESSION_VARS['sess_searchkey'],$root_path);

if(isset($mode)&&($mode=='search'||$mode=='paginate')&&isset($searchkey)&&($searchkey)){
	

	include_once($root_path.'include/inc_date_format_functions.php');
	
	//$db->debug=true;

	if($mode!='paginate'){
		$HTTP_SESSION_VARS['sess_searchkey']=$searchkey;
	}	
		# convert * and ? to % and &
		$searchkey=strtr($searchkey,'*?','%_');
		
		$GLOBAL_CONFIG=array();

		include_once($root_path.'include/care_api_classes/class_globalconfig.php');
		$glob_obj=new GlobalConfig($GLOBAL_CONFIG);

		# Get the max nr of rows from global config
		$glob_obj->getConfig('pagin_patient_search_max_block_rows');
		if(empty($GLOBAL_CONFIG['pagin_patient_search_max_block_rows'])) $pagen->setMaxCount(MAX_BLOCK_ROWS); # Last resort, use the default defined at the start of this page
			else $pagen->setMaxCount($GLOBAL_CONFIG['pagin_patient_search_max_block_rows']);
		
		$searchkey=trim($searchkey);
		$suchwort=$searchkey;
#echo "key = ".$suchwort;
		if(is_numeric($suchwort)) {

			$suchwort=(int) $suchwort;
			$numeric=1;
			if(empty($oitem)) $oitem='encounter_nr';			
			if(empty($odir)) $odir='DESC'; # default, latest pid at top
			
			$sql2=" WHERE ( enc.encounter_nr='$suchwort' OR enc.encounter_nr $sql_LIKE '%$suchwort' )";
		} else {
				# Try to detect if searchkey is composite of first name + last name
			if(stristr($searchkey,',')){
				$lastnamefirst=TRUE;
			}else{
				$lastnamefirst=FALSE;
			}
			
			$searchkey=strtr($searchkey,',',' ');
			$cbuffer=explode(' ',$searchkey);

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
				#$sql2=" WHERE ( reg.name_last $sql_LIKE '".strtr($ln,'+',' ')."%'
			    #            		AND reg.name_first $sql_LIKE '".strtr($fn,'+',' ')."%')";
				 #edited by VAN 04-04-08
				 $sql2=" WHERE (( reg.name_last $sql_LIKE '".strtr($ln,'+',' ')."%'
			                		AND reg.name_first $sql_LIKE '".strtr($fn,'+',' ')."%')
									OR (reg.name_last $sql_LIKE '%".strtr($suchwort,'+',' ')."%'
			                		OR reg.name_first $sql_LIKE '%".strtr($suchwort,'+',' ')."%'))";
										
				if($bd){ 
					$stddate=formatDate2STD($bd,$date_format);
					if(!empty($stddate)){
						$sql2.=" AND (reg.date_birth = '$stddate' OR reg.date_birth $sql_LIKE '%$bd%')";
					}
				}
					
				if(empty($odir)) $odir='DESC'; # default, latest birth at top
		
			}else{
			
				$sql2=" WHERE (reg.name_last $sql_LIKE '%".strtr($suchwort,'+',' ')."%'
			                		OR reg.name_first $sql_LIKE '%".strtr($suchwort,'+',' ')."%'";
				$bufdate=formatDate2STD($suchwort,$date_format);
				if(!empty($bufdate)){
					$sql2.= " OR reg.date_birth $sql_LIKE '$bufdate'";
				}
				$sql2.=")";
				if(empty($odir)) $odir='ASC'; # default, ascending alphabetic
			}
		}

#			$sql2.=" AND enc.pid=reg.pid
#					  AND enc.encounter_status <> 'cancelled'
#					  AND enc.is_discharged=0
#					  AND enc.status NOT IN ('void','hidden','inactive','deleted')  ORDER BY ";   # burn commented: March 9, 2007
			/*
			$sql2.=" AND enc.pid=reg.pid
						AND enc.encounter_status <> 'cancelled'
						AND enc.is_discharged=0
						AND enc.status NOT IN ('void','hidden','inactive','deleted')  
						AND enc.encounter_type IN ($encounter_type_search)  
						AND sr.region_nr=sp.region_nr AND sp.prov_nr=sm.prov_nr 
						AND sm.mun_nr=sb.mun_nr AND sb.brgy_nr=reg.brgy_nr 					  
						ORDER BY ";   # burn added: March 9, 2007
			*/
			#edited by VAN 04-05-08
			
			$sql2.=" AND enc.encounter_status <> 'cancelled' 
						AND enc.is_discharged=0 
						AND enc.status NOT IN ('deleted','hidden','inactive','void') 
						AND enc.encounter_type IN (1,2,3,4) 
						AND (enc.current_ward_nr IN (SELECT w.nr FROM care_ward AS w WHERE w.name LIKE '%charity%')
						OR enc.current_ward_nr IN(0))
						ORDER BY ";

			# Filter if it is personnel nr
#			if($oitem=='encounter_nr') $sql2.='enc.'.$oitem.' '.$odir;   # burn commented: March 9, 2007
#				else $sql2.='reg.'.$oitem.' '.$odir;   # burn commented: March 9, 2007
			$sql2.=$oitem.' '.$odir;   # burn added: March 9, 2007
#			$dbtable='FROM care_encounter as enc,care_person as reg ';   # burn commented: March 9, 2007
			/*
			$dbtable=" FROM care_encounter as enc,care_person as reg ".
						" , seg_barangays AS sb, seg_municity AS sm, ".
						" seg_provinces AS sp, seg_regions AS sr ";   # burn added: March 9, 2007
			*/
			#edited by VAN 04-05-08
			$dbtable="FROM care_encounter as enc
						 INNER JOIN care_person as reg ON enc.pid=reg.pid
						 INNER JOIN seg_barangays AS sb ON sb.brgy_nr=reg.brgy_nr
						 INNER JOIN seg_municity AS sm ON sm.mun_nr=sb.mun_nr
						 INNER JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr
						 INNER JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr
						 LEFT JOIN care_ward AS w ON w.nr=enc.current_ward_nr"; 
						
#			$sql='SELECT enc.encounter_nr, enc.encounter_class_nr, enc.is_discharged,
#								reg.name_last, reg.name_first, reg.date_birth, reg.addr_zip,reg.sex '.$dbtable.$sql2;   # burn commented: March 9, 2007
			$sql=" SELECT reg.pid, enc.encounter_nr, enc.encounter_date, enc.encounter_class_nr, enc.encounter_type, ".
				  " enc.admission_dt, enc.is_discharged, reg.name_last, reg.name_first, reg.date_birth, reg.addr_zip,reg.sex ".
				  " , sb.brgy_name, sm.zipcode, sm.mun_name, sp.prov_name, sr.region_name ".$dbtable.$sql2;   # burn added: March 9, 2007
			//echo $sql;
/*
SELECT enc.encounter_nr, enc.encounter_class_nr, enc.is_discharged, 
	reg.name_last, reg.name_first, reg.date_birth, reg.addr_zip,reg.sex 
	, sb.brgy_name, sm.zipcode, sm.mun_name, sp.prov_name, sr.region_name 
FROM care_encounter as enc,care_person as reg 
	, seg_barangays AS sb, seg_municity AS sm, 
	seg_provinces AS sp, seg_regions AS sr
WHERE (reg.name_last LIKE '%%' OR reg.name_first LIKE '%%') AND enc.pid=reg.pid 
	AND enc.encounter_status <> 'cancelled' AND enc.is_discharged=0 
	AND enc.status NOT IN ('void','hidden','inactive','deleted')
	AND sr.region_nr=sp.region_nr AND sp.prov_nr=sm.prov_nr 
	AND sm.mun_nr=sb.mun_nr AND sb.brgy_nr=reg.brgy_nr 
ORDER BY name_last ASC
*/
#echo "sql = '".$sql."' <br> \n";
#exit();
#echo "LDAmbulant = '".$LDAmbulant."' <br> \n";
			if($ergebnis=$db->SelectLimit($sql,$pagen->MaxCount(),$pagen->BlockStartIndex()))
       		{
				if ($linecount=$ergebnis->RecordCount()) 
				{
					if(($linecount==1)&&$numeric&&$mode=='search')
					{
						$zeile=$ergebnis->FetchRow();
						//header('Location:aufnahme_daten_zeigen.php'.URL_REDIRECT_APPEND.'&from=such&encounter_nr='.$zeile['encounter_nr'].'&target=search');
						  header('Location:social_service_show.php'.URL_REDIRECT_APPEND.'&from=such&encounter_nr='.$zeile['encounter_nr'].'&target=search');
						exit;
					}
					
					$pagen->setTotalBlockCount($linecount);
					
					# If more than one count all available
					if(isset($totalcount)&&$totalcount){
						$pagen->setTotalDataCount($totalcount);
					}else{
						# Count total available data
						if($dbtype=='mysql'){
							$sql='SELECT COUNT(enc.encounter_nr) AS "count" '.$dbtable.$sql2;
						}else{
							$sql='SELECT * '.$dbtable.$sql2;
						}

						if($result=$db->Execute($sql)){
							if ($totalcount=$result->RecordCount()) {
								if($dbtype=='mysql'){
									$rescount=$result->FetchRow();
    									$totalcount=$rescount['count'];
								}
    							}
						}
						$pagen->setTotalDataCount($totalcount);
					}
					# Set the sort parameters
					$pagen->setSortItem($oitem);
					$pagen->setSortDirection($odir);
				}
				
			}
			 else {echo "<p>".$sql."<p>$LDDbNoRead";};
}

# Start Smarty templating here
 /**
 * LOAD Smarty
 */
 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

# Title in the toolbar
 //$smarty->assign('sToolbarTitle',$LDPatientSearch);
/*if (($user_dept_info['dept_nr']==150) || ($user_dept_info['dept_nr']==149)){
	# search under ER or OPD Triage
 	$smarty->assign('sToolbarTitle',"$LDConsultation :: $LDSearch");   # burn added : May 15, 2007
}else{
 	$smarty->assign('sToolbarTitle',"$LDAdmission :: $LDSearch");   # burn added : May 15, 2007
}*/
# $smarty->assign('sToolbarTitle',"$LDAdmission :: $LDSearch");   # burn commented : May 15, 2007
$smarty->assign('sToolbarTitle',"$swSocialService :: $swSearch");


 $smarty->assign('breakfile',$breakfile);

 # Window bar title
// $smarty->assign('title',$LDPatientSearch);
 #$smarty->assign('title',$swSocialService);
 $smarty->assign('sWindowTitle',"$swSocialService :: $swSearch");		
	
 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('admission_how2search.php','$from')");

  # Onload Javascript code
 $smarty->assign('sOnLoadJs','onLoad="if(window.focus) window.focus();document.searchform.searchkey.select();"');

 # Hide the return button
 $smarty->assign('pbBack',FALSE);

#
# Load the tabs
#
$target='search';
$parent_admit = TRUE;
#commented by VAN 04-04-08
#include('./gui_bridge/default/gui_tabs_patadmit.php');

#
# Prepare the javascript validator
#
if(!isset($searchform_count) || !$searchform_count){
	$smarty->assign('sJSFormCheck','<script language="javascript">
	<!--
		function chkSearch(d){
			if((d.searchkey.value=="") || (d.searchkey.value==" ")){
				d.searchkey.focus();
				return false;
			}else	{
				return true;
			}
		}
	// -->
	</script>');
}

#
# Prepare the form params
#
# Set value for the search mask
#$searchprompt=$LDEntryPrompt;   # transferred from above; burn commented : May 18, 2007
#$searchprompt="Enter the search keyword. For example: encounter number, or lastname, or firstname, or date of birth, etc.";   # burn added : May 18, 2007
#edited by VAN 04-04-08
$searchprompt="<span>Enter the search keyword (case number, family name, given name, or date of birth)
               <br>Enter dates in <font color=\"#0000FF\"><b>MM.DD.YYYY</b></font> format.
								Enter asterisk (<b>*</b>) to show all data.<br>
					</span>";   # burn added : May 18, 2007

$sTemp = 'method="post" name="searchform';
if($searchform_count) $sTemp = $sTemp."_".$searchform_count;
$sTemp = $sTemp.'" onSubmit="return chkSearch(this)"';
if(isset($search_script) && $search_script!='') $sTemp = $sTemp.' action="'.$search_script.'"';
$smarty->assign('sFormParams',$sTemp);
$smarty->assign('searchprompt',$searchprompt);

#
# Prepare the hidden inputs
#
$smarty->assign('sHiddenInputs','<input type="image" '.createLDImgSrc($root_path,'searchlamp.gif','0','absmiddle').'>
		<input type="hidden" name="sid" value="'.$sid.'">
		<input type="hidden" name="lang" value="'.$lang.'">
		<input type="hidden" name="noresize" value="'.$noresize.'">
		<input type="hidden" name="target" value="'.$target.'">
		<input type="hidden" name="user_origin" value="'.$user_origin.'">
		<input type="hidden" name="origin" value="'.$origin.'">
		<input type="hidden" name="retpath" value="'.$retpath.'">
		<input type="hidden" name="aux1" value="'.$aux1.'">
		<input type="hidden" name="ipath" value="'.$ipath.'">
		<input type="hidden" name="mode" value="search">');

//cancel button 
#$smarty->assign('sCancelButton','<a href="patient.php'.URL_APPEND.'&target=search"><img '.createLDImgSrc($root_path,'cancel.gif','0').'></a>');

if($mode=='search'||$mode=='paginate'){
	
	if ($linecount) $smarty->assign('LDSearchFound',str_replace("~no.~",$totalcount,$LDSearchFound).' '.$LDShowing.' '.$pagen->BlockStartNr().' '.$LDTo.' '.$pagen->BlockEndNr().'.');
		else $smarty->assign('LDSearchFound',str_replace('~no.~','0',$LDSearchFound));

	if ($linecount) {

		$smarty->assign('bShowResult',TRUE);

		# Load the common icons and images
		$img_options=createComIcon($root_path,'pdata.gif','0');
		$img_male=createComIcon($root_path,'spm.gif','0');
		$img_female=createComIcon($root_path,'spf.gif','0');
#echo "name = ".$LDLastName;

		$smarty->assign('LDCaseNr',$pagen->makeSortLink($LDCaseNr,'encounter_nr',$oitem,$odir,$targetappend));
		$smarty->assign('segEncDate',$pagen->makeSortLink("Encounter Date",'encounter_date',$oitem,$odir,$targetappend));   # burn added: May 11,, 2007
		$smarty->assign('LDSex',$pagen->makeSortLink($LDSex,'sex',$oitem,$odir,$targetappend));
		$smarty->assign('LDLastName',$pagen->makeSortLink($LDLastName,'name_last',$oitem,$odir,$targetappend));
		$smarty->assign('LDFirstName',$pagen->makeSortLink($LDFirstName,'name_first',$oitem,$odir,$targetappend));
		$smarty->assign('LDBday',$pagen->makeSortLink($LDBday,'date_birth',$oitem,$odir,$targetappend));
		$smarty->assign('segBrgy',$pagen->makeSortLink("Barangay",'brgy_name',$oitem,$odir,$targetappend));   # burn added: March 9, 2007
		$smarty->assign('segMuni',$pagen->makeSortLink("Muni/City",'mun_name',$oitem,$odir,$targetappend));   # burn added: March 9, 2007
#		$smarty->assign('LDZipCode',$pagen->makeSortLink($LDZipCode,'addr_zip',$oitem,$odir,$targetappend));   # burn commented: March 9, 2007
#		$smarty->assign('LDZipCode',$pagen->makeSortLink($LDZipCode,'zipcode',$oitem,$odir,$targetappend));   # burn added: March 9, 2007
		$smarty->assign('LDOptions',$LDOptions);

		$sTemp = '';
		while($zeile=$ergebnis->FetchRow()){

			$full_en=$zeile['encounter_nr'];

			$smarty->assign('toggle',$toggle);
			$toggle = !$toggle;

			$smarty->assign('sCaseNr',$full_en);
/*				# burn commented: March 13, 2007
			if($zeile['encounter_class_nr']==2){
				$smarty->assign('sOutpatientIcon','<img '.createComIcon($root_path,'redflag.gif').'>');
				$smarty->assign('LDAmbulant',$LDAmbulant);
			}else{
				$smarty->assign('sOutpatientIcon','');
				$smarty->assign('LDAmbulant','');
			}
*/

				# burn added: May 11,, 2007
			if (($zeile['encounter_type']==1)||($zeile['encounter_type']==2))
				$smarty->assign('sEncDate',@formatDate2Local($zeile['encounter_date'],$date_format,1)); 
			else
				$smarty->assign('sEncDate',@formatDate2Local($zeile['admission_dt'],$date_format,1));

				# burn added: March 13, 2007
			if($zeile['encounter_type']==1){
				$smarty->assign('sOutpatientIcon','<img '.createComIcon($root_path,'flag_red.gif').'>');
				$smarty->assign('LDAmbulant','<font size=1 color="red">ER</font>');
			}elseif($zeile['encounter_type']==2){
				$smarty->assign('sOutpatientIcon','<img '.createComIcon($root_path,'flag_blue.gif').'>');
				$smarty->assign('LDAmbulant','<font size=1 color="blue">Outpatient</font>');
			}else{
				$smarty->assign('sOutpatientIcon','<img '.createComIcon($root_path,'flag_green.gif').'>');
				$smarty->assign('LDAmbulant','<font size=1 color="green">Inpatient</font>');
			}

			switch(strtolower($zeile['sex'])){
				case 'f': $smarty->assign('sSex','<img '.$img_female.'>'); break;
				case 'm': $smarty->assign('sSex','<img '.$img_male.'>'); break;
				default: $smarty->assign('sSex','&nbsp;'); break;
			}
			$smarty->assign('sLastName',ucfirst($zeile['name_last']));
			$smarty->assign('sFirstName',ucfirst($zeile['name_first']));

			#
			# If person is dead show a black cross
			#
			if($zeile['death_date']&&$zeile['death_date']!=$dbf_nodate) $smarty->assign('sCrossIcon','<img '.createComIcon($root_path,'blackcross_sm.gif','0','absmiddle').'>');
				else $smarty->assign('sCrossIcon','');

				# burn added: March 27, 2007
			$date_birth = @formatDate2Local($zeile['date_birth'],$date_format);			
			$bdateMonth = substr($date_birth,0,2);
			$bdateDay = substr($date_birth,3,2);
			$bdateYear = substr($date_birth,6,4);
			if (!checkdate($bdateMonth, $bdateDay, $bdateYear)){
				# invalid birthdate
				$date_birth='';
			}

#			$smarty->assign('sBday',formatDate2Local($zeile['date_birth'],$date_format));   # burn commented: March 27, 2007
			$smarty->assign('sBday',$date_birth);   # burn added: March 27, 2007

			$smarty->assign('sBrgy',$zeile['brgy_name']);   # burn added: March 9, 2007
			$smarty->assign('sMuni',$zeile['mun_name']);   # burn added: March 9, 2007

#			$smarty->assign('sZipCode',$zeile['addr_zip']);   # burn commented: March 9, 2007
#			$smarty->assign('sZipCode',$zeile['zipcode']);   # burn added: March 9, 2007
			if(isset($mode) && ($mode != '')) $mode = 'show'; 		
			
			//$sTarget = "<a href=\"aufnahme_daten_zeigen.php".URL_APPEND."&from=such&encounter_nr=$full_en&target=search\">";
			#social_service_show.php 
			//$sTarget = "<a href=./social_service_show.php".URL_APPEND."&from=such&pid=".$zeile['pid']."&encounter_nr=$full_en&target=search&mode=$mode>";
			#show_social_service
			$sTarget = "<a href=./show_social_service.php".URL_APPEND."&from=such&pid=".$zeile['pid']."&encounter_nr=$full_en&target=search&mode=$mode>";

			$sTarget=$sTarget.'<img '.$img_options.' title="'.$LDShowData.'"></a>';
			$smarty->assign('sOptions',$sTarget);

			if(!file_exists($root_path.'cache/barcodes/en_'.$full_en.'.png')){
				$smarty->assign('sHiddenBarcode',"<img src='".$root_path."classes/barcode/image.php?code=".$full_en."&style=68&type=I25&width=180&height=50&xres=2&font=5&label=2' border=0 width=0 height=0>");
			}
			#
			# Generate the row in buffer and append as string
			#
			ob_start();
				$smarty->display('social_service/social_search_list_row.tpl');
				$sTemp = $sTemp.ob_get_contents();
			ob_end_clean();
		}

		#
		# Assign the rows string to template
		#
		#echo "<br>temp = ".$sTemp;
		$smarty->assign('sResultListRows',$sTemp);

		$smarty->assign('sPreviousPage',$pagen->makePrevLink($LDPrevious));
		$smarty->assign('sNextPage',$pagen->makeNextLink($LDNext));
	}
}
/*
$smarty->assign('sPostText','<a href="aufnahme_start.php'.URL_APPEND.'&mode=?">'.$LDAdmWantEntry.'</a><br>
	<a href="aufnahme_list.php'.URL_APPEND.'">'.$LDAdmWantArchive.'</a>');
*/
$smarty->assign('sPostText','<a href="aufnahme_list.php'.URL_APPEND.'">'.$LDAdmWantArchive.'</a>');

# Stop buffering, assign contents and display template

#$smarty->assign('sMainIncludeFile','registration_admission/admit_search_main.tpl');
$smarty->assign('sMainIncludeFile','social_service/social_search_main.tpl');

$smarty->assign('sMainBlockIncludeFile','registration_admission/admit_plain.tpl');

$smarty->display('common/mainframe.tpl');

?>