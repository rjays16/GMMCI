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
define('NO_2LEVEL_CHK',1);
require($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'include/inc_date_format_functions.php');

$thisfile=basename(__FILE__);
$toggle=0;

$breakfile=$root_path."modules/system_admin/edv.php".URL_APPEND;

require_once($root_path.'include/care_api_classes/class_ward.php');
$ward_obj=new Ward;

require_once($root_path.'include/care_api_classes/class_department.php');
$dept_obj=new Department;

	if (!empty($HTTP_SESSION_VARS['sess_login_userid']))
		$seg_user_name = $HTTP_SESSION_VARS['sess_login_userid'];
	else
		$seg_user_name = $HTTP_SESSION_VARS['sess_temp_userid'];
	$user_dept_info = $dept_obj->getUserDeptInfo($seg_user_name);


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

# Initialize pageï¿½s control variables
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

#added by VAN 06-26-08
if (empty($mode)){
	$mode ='search';
}

if(isset($mode)&&($mode=='search'||$mode=='paginate')&&isset($searchkey)&&($searchkey)){

	#added by VAN 06-26-08
	if ($searchkey=='now'){
		$searchkey = ' ';
		$now = 1;
	}else{
		$now = 0;
	}


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

			#$suchwort=(int) $suchwort;
			$numeric=1;
			if(empty($oitem)) $oitem='encounter_nr';
			if(empty($odir)) $odir='DESC'; # default, latest pid at top

			#$sql2=" WHERE ( enc.encounter_nr='$suchwort' OR enc.encounter_nr $sql_LIKE '%$suchwort' )";
			$sql2=" WHERE ( reg.pid = '$suchwort')";
		} else {
				# Try to detect if searchkey is composite of first name + last name
			if(stristr($searchkey,',')){
				$lastnamefirst=TRUE;
			}else{
				$lastnamefirst=FALSE;
			}
			/*
			$searchkey=strtr($searchkey,',',' ');
			$cbuffer=explode(' ',$searchkey);
			*/
			if(stristr($searchkey, ',') === FALSE){
				$cbuffer=explode(' ',$searchkey);
				$newsearchkey = $searchkey;
				$lnameOnly = 1;
				$newquery = " OR name_last $sql_LIKE '".$newsearchkey."%'";
			}else{
				$cbuffer=explode(',',$searchkey);
				$newquery = "";
				$lnameOnly = 0;
				$newquery = "";
			}
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
				 $sql2=" WHERE (( reg.name_last $sql_LIKE '".strtr($ln,'+',' ')."%'
													AND reg.name_first $sql_LIKE '".strtr($fn,'+',' ')."%')
										$newquery
										)";

				if($bd){
					$stddate=formatDate2STD($bd,$date_format);
					if(!empty($stddate)){
						$sql2.=" AND (reg.date_birth = '$stddate' OR reg.date_birth $sql_LIKE '%$bd%')";
					}
				}

				if(empty($odir)) $odir='DESC'; # default, latest birth at top

			}else{

				$sql2=" WHERE (reg.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%'";
				$bufdate=formatDate2STD($suchwort,$date_format);
				if(!empty($bufdate)){
					$sql2.= " OR reg.date_birth $sql_LIKE '$bufdate'";
				}
				$sql2.=")";
				if(empty($odir)) $odir='ASC'; # default, ascending alphabetic
			}
		}

			if ($now)
				$now_cond = " AND (DATE((SELECT encounter_date FROM care_encounter AS enc WHERE reg.pid=enc.pid AND enc.is_discharged=0 AND enc.encounter_status <> 'cancelled' AND enc.status NOT IN ('deleted','hidden','inactive','void') ORDER BY encounter_date DESC LIMIT 1))=DATE(NOW())
								OR DATE((SELECT admission_dt FROM care_encounter AS enc WHERE reg.pid=enc.pid AND enc.is_discharged=0 AND enc.encounter_status <> 'cancelled' AND enc.status NOT IN ('deleted','hidden','inactive','void') ORDER BY encounter_date DESC LIMIT 1))=DATE(NOW())
								OR DATE(reg.date_reg)=DATE(NOW()))
							";


			/**
			* @author Alvin 2010-10-19
			*
			* Small temp fix to allow overriding of requests from a previous referral. Before the fixm a referral would in
			* effect render the requests made from that encounter unviewable as only the current active encounter
			* is shown.
			*
			* The fix displays all encounters associated with the search key so that overriding can be made
			* on encounters prior to the referral.
			*/

			/**
			* @author Alvin 2010-10-19
			*
			* Removed GROUP BY clause, reformatted SQL
			*/
			$sql2.=" AND reg.status NOT IN ('deleted','hidden','inactive','void')\n".

						/*AND (IF(((dep.dependent_pid IS NOT NULL) OR (ps.nr IS NOT NULL)),'PHS','')='PHS')*/
						$now_cond."\n".
						// GROUP BY reg.pid
						"ORDER BY ";

			#$sql2.=$oitem.' '.$odir." , name_first ASC";   # burn added: March 9, 2007
			if ($searchkey)
				$order_cond = " ,encounter_date DESC ";
			else
				$order_cond = "";

			$sql2.= " name_last ASC, name_first ASC".$order_cond;
			#edited by VAN 06-26-08
			$dbtable="FROM  care_person as reg
						LEFT JOIN care_encounter as enc ON enc.pid=reg.pid AND is_discharged=0
							AND encounter_type NOT IN (5,6) AND enc.encounter_status <> 'cancelled' AND enc.status NOT IN ('deleted','hidden','inactive','void')
						LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=reg.brgy_nr
						LEFT JOIN seg_municity AS sm ON sm.mun_nr=reg.mun_nr
						LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr
						LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr
						LEFT JOIN care_ward AS w ON w.nr=enc.current_ward_nr
						LEFT JOIN care_personell AS ps ON reg.pid=ps.pid
								AND ((date_exit NOT IN (DATE(NOW())) AND date_exit > DATE(NOW())) OR date_exit='0000-00-00' OR date_exit IS NULL)
								AND ((contract_end NOT IN (DATE(NOW())) AND contract_end > DATE(NOW()))
								OR contract_end='0000-00-00' OR contract_end IS NULL)

						LEFT JOIN seg_dependents AS dep ON dep.dependent_pid=reg.pid AND dep.status='member'
						 ";

						/**
						* @author Alvin 2010-10-19
						*
						* Removed row-wise maximum code, and return the actual column
						*/

						 $sql="SELECT ps.job_function_title, ps.nr AS personell_nr,
										IF(((dep.dependent_pid IS NOT NULL) OR (ps.nr IS NOT NULL)),'PHS','') AS discountid,
										IF(fn_calculate_age(NOW(),reg.date_birth),fn_get_age(NOW(),reg.date_birth),reg.age) AS age,
										reg.pid, reg.name_last, reg.name_first, reg.name_middle, reg.date_birth,
										reg.addr_zip,reg.sex , sb.brgy_name, reg.date_reg, reg.death_date,
										sm.zipcode, sm.mun_name, sp.prov_name, sr.region_name,\n".

//										(SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.encounter_nr)),20)) AS encounter_nr,
//										 MAX(enc.encounter_date) AS encounter_date,
//										(SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.encounter_class_nr)),20)) AS encounter_class_nr,
//										(SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.admission_dt)),20)) AS admission_dt,
//										(SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.encounter_type)),20)) AS encounter_type,
//										(SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.is_discharged)),20)) AS is_discharged,
//										(SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.discharge_date)),20)) AS discharge_date,
//										(SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.current_dept_nr)),20)) AS current_dept_nr,
//										(SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.current_ward_nr)),20)) AS current_ward_nr,
//										(SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.current_room_nr)),20)) AS current_room_nr,
//										(SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.current_dept_nr)),20)) AS current_dept_nr


										"enc.encounter_nr,\n".
										"enc.encounter_date,\n".
										"enc.encounter_class_nr,\n".
										"enc.admission_dt,\n".
										"enc.encounter_type,\n".
										"enc.is_discharged,\n".
										"enc.discharge_date,\n".
										"enc.current_dept_nr,\n".
										"enc.current_ward_nr,\n".
										"enc.current_room_nr,\n".
										"enc.current_dept_nr\n"

								 .$dbtable.$sql2;

			#echo $sql;

			if($ergebnis=$db->SelectLimit($sql,$pagen->MaxCount(),$pagen->BlockStartIndex()))
					{
				if ($linecount=$ergebnis->RecordCount())
				{
					if(($linecount==1)&&$numeric&&$mode=='search')
					{
						$zeile=$ergebnis->FetchRow();
						header('Location:seg_override.php'.URL_REDIRECT_APPEND.'&from=such&pid='.$zeile['pid'].'&encounter_nr='.$zeile['encounter_nr'].'&origin=patreg_reg&target=adminoverride_show&mode=$mode');
						exit;
					}

					$pagen->setTotalBlockCount($linecount);

					# If more than one count all available
					if(isset($totalcount)&&$totalcount){
						$pagen->setTotalDataCount($totalcount);
					}else{
						# Count total available data
						if($dbtype=='mysql'){
							#$sql='SELECT COUNT(enc.encounter_nr) AS "count" '.$dbtable.$sql2;
							$sql='SELECT COUNT(reg.pid) AS "count" '.$dbtable.$sql2;
						}else{
							$sql='SELECT * '.$dbtable.$sql2;
						}
#echo "sql = ".$sql;

						if($result=$db->Execute($sql)){
							if ($totalcount=$result->RecordCount()) {
							#echo "totalcount = ".$totalcount;
								if($dbtype=='mysql'){
									$rescount=$result->FetchRow();
											#$totalcount=$rescount['count'];
										$totalcount=$result->RecordCount();
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

 $title = 'Override Test Request';

 # Title in the toolbar
 $smarty->assign('sToolbarTitle',"$title :: $swSearch");

 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$title :: $swSearch");

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('admission_how2search.php','$from')");

	# Onload Javascript code
 $smarty->assign('sOnLoadJs','onLoad="if(window.focus) window.focus();document.searchform.searchkey.select();DisabledSearch();"');

 # Hide the return button
 $smarty->assign('pbBack',FALSE);

#
# Load the tabs
#
$target='search';
$parent_admit = TRUE;

# Prepare the javascript validator
#
if(!isset($searchform_count) || !$searchform_count){
	$smarty->assign('sJSFormCheck','<script language="javascript">

		function isValidSearch(key) {

					if (typeof(key)==\'undefined\') return false;
					var s=key.toUpperCase();
					return (
						/^[A-Z0-9Ñ\-\.]{2}[A-Z0-9Ñ\-\. ]*\s*,\s*[A-Z0-9Ñ\-\.]{2}[A-Z0-9Ñ\-\. ]*$/.test(s) ||
						/^\d{1,2}\/\d{1,2}\/\d{4}$/.test(s) ||
						/^\d{1,2}\-\d{1,2}\-\d{4}$/.test(s) ||
						/^\d+$/.test(s)
					);
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

		function DisabledSearch(){
					var b=isValidSearch(document.getElementById(\'searchkey\').value);
					document.getElementById("searchButton").style.cursor=(b?"pointer":"default");
					document.getElementById("searchButton").disabled = !b;
		}
	//
	</script>');
}

$searchprompt="<span>Enter the search keyword (case number, family name, or date of birth)
							 <br>Enter dates in <font color=\"#0000FF\"><b>MM/DD/YYYY</b></font> format.
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
$smarty->assign('sHiddenInputs','<input name="searchButton" id="searchButton" type="image" '.createLDImgSrc($root_path,'searchlamp.gif','0','absmiddle').'>
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

if($mode=='search'||$mode=='paginate'){
	if ($linecount) $smarty->assign('LDSearchFound',str_replace("~no.~",$totalcount,$LDSearchFound).' '.$LDShowing.' '.$pagen->BlockStartNr().' '.$LDTo.' '.$pagen->BlockEndNr().'.');
		else $smarty->assign('LDSearchFound',str_replace('~no.~','0',$LDSearchFound));

	if ($linecount) {

		$smarty->assign('bShowResult',TRUE);

		# Load the common icons and images
		$img_options=createComIcon($root_path,'pdata.gif','0');
		$img_male=createComIcon($root_path,'spm.gif','0');
		$img_female=createComIcon($root_path,'spf.gif','0');

		$smarty->assign('LDPID',$pagen->makeSortLink('HRN','pid',$oitem,$odir,$targetappend));

		$smarty->assign('LDCaseNr',$pagen->makeSortLink("Case No.",'encounter_nr',$oitem,$odir,$targetappend));
		$smarty->assign('LDSex',$pagen->makeSortLink($LDSex,'sex',$oitem,$odir,$targetappend));

		$smarty->assign('LDAge',"Age");

		$smarty->assign('LDLastName',$pagen->makeSortLink($LDLastName,'name_last',$oitem,$odir,$targetappend));
		$smarty->assign('LDFirstName',$pagen->makeSortLink($LDFirstName,'name_first',$oitem,$odir,$targetappend));
		$smarty->assign('LDMiddleName',$pagen->makeSortLink("Middle Name",'name_middle',$oitem,$odir,$targetappend));

		$smarty->assign('LDAdmissionDate',"Consultation Date");
		$smarty->assign('LDPersonnelNr',"Personnel No.");
		$smarty->assign('LDJobPosition',"Job Position");
		$smarty->assign('LDDepartment',"Department");

		$smarty->assign('LDOptions',$LDOptions);

		$sTemp = '';
		while($zeile=$ergebnis->FetchRow()){
			$smarty->assign('sPID',$zeile['pid']);

			$full_en=$zeile['encounter_nr'];

			$smarty->assign('toggle',$toggle);
			$toggle = !$toggle;

			$smarty->assign('sCaseNr',$full_en);

			if (($zeile['encounter_type']==1)||($zeile['encounter_type']==2)){
				$smarty->assign('sAdmissionDate',@formatDate2Local($zeile['encounter_date'],$date_format,1));

				if ($zeile['encounter_type']==1)
					$location = "ER";
				elseif ($zeile['encounter_type']==2){
					$dept = $dept_obj->getDeptAllInfo($zeile['current_dept_nr']);
					$location = $dept['id'];
				}

			}elseif (($zeile['encounter_type']==3)||($zeile['encounter_type']==4)){
				$smarty->assign('sAdmissionDate',@formatDate2Local($zeile['admission_dt'],$date_format,1));

				$ward = $ward_obj->getWardInfo($zeile['current_ward_nr']);
				$location = $ward['ward_id']." : Rm.#".$zeile['current_room_nr'];
			}else{
				$smarty->assign('sAdmissionDate','<font size=1 color="magenta">N/A</font>');
				$location = '<font size=1 color="magenta">N/A</font>';
			}

			$smarty->assign('sPersonnelNr',$zeile['personell_nr']);

			$smarty->assign('sJobPosition',$zeile['job_function_title']);

			$smarty->assign('sDepartment',$location);

			#-----------------------

				# burn added: March 13, 2007
			if($zeile['encounter_type']==1){
				$smarty->assign('sOutpatientIcon','<img '.createComIcon($root_path,'flag_red.gif').'>');
				$smarty->assign('LDAmbulant','<font size=1 color="red">ER</font>');
			}elseif($zeile['encounter_type']==2){
				$smarty->assign('sOutpatientIcon','<img '.createComIcon($root_path,'flag_blue.gif').'>');
				$smarty->assign('LDAmbulant','<font size=1 color="blue">OPD</font>');
			}elseif(($zeile['encounter_type']==3)||($zeile['encounter_type']==4)){
				$smarty->assign('sOutpatientIcon','<img '.createComIcon($root_path,'flag_green.gif').'>');
				$smarty->assign('LDAmbulant','<font size=1 color="green">IPD</font>');
			}else{
				$smarty->assign('sOutpatientIcon','<img '.createComIcon($root_path,'high_pri.gif').'>');
				$smarty->assign('LDAmbulant','<font size=1 color="magenta">No Transaction</font>');
			}

			switch(strtolower($zeile['sex'])){
				case 'f': $smarty->assign('sSex','<img '.$img_female.'>'); break;
				case 'm': $smarty->assign('sSex','<img '.$img_male.'>'); break;
				default: $smarty->assign('sSex','&nbsp;'); break;
			}
			$smarty->assign('sLastName',ucfirst($zeile['name_last']));
			$smarty->assign('sFirstName',ucfirst($zeile['name_first']));
			$smarty->assign('sMiddleName',ucfirst($zeile['name_middle']));

			#added by VAN 07-28-08
			if ($zeile['age']){
				#$age = "<font color='#FF0000'>".$zeile['age']."<font>";
				$age = $zeile['age'];
			}else
				$age = "unknown";

			$smarty->assign('sAge',$age);

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

			if(isset($mode) && ($mode != '')) $mode = 'entry';

			$sTarget = "<a href=./seg_override.php".URL_APPEND."&from=such&pid=".$zeile['pid']."&encounter_nr=$full_en&origin=patreg_reg&target=adminoverride_show&mode=$mode>";

			$sTarget=$sTarget.'<img '.$img_options.' title="'.$LDShowData.'"></a>';
			$smarty->assign('sOptions',$sTarget);

			if(!file_exists($root_path.'cache/barcodes/en_'.$full_en.'.png')){
				$smarty->assign('sHiddenBarcode',"<img src='".$root_path."classes/barcode/image.php?code=".$full_en."&style=68&type=I25&width=180&height=50&xres=2&font=5&label=2' border=0 width=0 height=0>");
			}
			#
			# Generate the row in buffer and append as string
			#
			ob_start();
				$smarty->display('system_admin/override/seg_override_search_list_row.tpl');
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

$smarty->assign('sPostText','<a href="aufnahme_list.php'.URL_APPEND.'">'.$LDAdmWantArchive.'</a>');

# Stop buffering, assign contents and display template

#$smarty->assign('sMainIncludeFile','registration_admission/admit_search_main.tpl');
$smarty->assign('sMainIncludeFile','system_admin/override/seg_override_search_main.tpl');

$smarty->assign('sMainBlockIncludeFile','registration_admission/admit_plain.tpl');

$smarty->display('common/mainframe.tpl');

?>