<?php

define('LAB_MAX_DAY_DISPLAY',7); # define the max number or days displayed at one time

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
$lang_tables=array('chemlab_groups.php','chemlab_params.php','prompt.php');
define('LANG_FILE','lab.php');
define('NO_2LEVEL_CHK',1);
require_once($root_path.'include/inc_front_chain_lang.php');
if(!isset($user_origin)) $user_origin='';

if($user_origin=='lab'||$user_origin=='lab_mgmt'){
  	$local_user='ck_lab_user';
  	if(isset($from)&&$from=='input') $breakfile=$root_path.'modules/laboratory/labor_datainput.php'.URL_APPEND.'&encounter_nr='.$encounter_nr.'&job_id='.$job_id.'&parameterselect='.$parameterselect.'&allow_update='.$allow_update.'&user_origin='.$user_origin;
		else $breakfile=$root_path.'modules/laboratory/labor_data_patient_such.php'.URL_APPEND;
}else{
  	$local_user='ck_pflege_user';
  	#edited by VAN 01-29-08
	#$breakfile=$root_path.'modules/nursing/nursing-station-patientdaten.php'.URL_APPEND.'&pn='.$pn.'&edit='.$edit;
	$breakfile=$root_path.'modules/nursing/nursing-station-patientdaten.php'.URL_APPEND.'&pn='.$pn.'&pid='.$pid.'&edit='.$edit;
	$encounter_nr=$pn;
}
if(!$HTTP_COOKIE_VARS[$local_user.$sid]) {header("Location:".$root_path."language/".$lang."/lang_".$lang."_invalid-access-warning.php"); exit;}; 

if(!$encounter_nr) header("location:".$root_path."modules/laboratory/labor_data_patient_such.php?sid=$sid&lang=$lang");

$thisfile=basename(__FILE__);

//$db->debug=1;

/* Create encounter object */
require_once($root_path.'include/care_api_classes/class_lab.php');
$enc_obj= new Encounter($encounter_nr);
$lab_obj=new Lab($encounter_nr);

#added by VAN 01-29-08
require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
$srvObj=new SegLab();

#echo "lab pid, enc = ".$pid." , ".$encounter_nr;

$cache='';

if($nostat) $ret=$root_path."modules/laboratory/labor_data_patient_such.php?sid=$sid&lang=$lang&versand=1&keyword=$encounter_nr";
	#edited by VAN 01-29-08
	#else $ret=$root_path."modules/nursing/nursing-station-patientdaten.php?sid=$sid&lang=$lang&station=$station&pn=$encounter_nr";
	$breakfile=$root_path.'modules/nursing/nursing-station-patientdaten.php'.URL_APPEND.'&pn='.$pn.'&pid='.$pid.'&edit='.$edit;
	
# Load the date formatter */
require_once($root_path.'include/inc_date_format_functions.php');

#$enc_obj->setWhereCondition("encounter_nr='$encounter_nr'");
#echo "sql = ".$enc_obj->sql;

if($encounter=&$enc_obj->getBasic4Data($encounter_nr)) {
	
	$patient=$encounter->FetchRow();
	
	$recs=&$srvObj->getPatientLabResults($pid);
	#echo "<br>count = ".$srvObj->rec_count;
	if ($rows=$srvObj->LastRecordCount()){
		#edited by VAN 01-31-08
		$records=array();
		
		while($buffer=&$recs->FetchRow()){
			$records[] = $buffer;
		}
	
	}else{
		if($nostat) header("location:".$root_path."modules/laboratory/labor-nodatafound.php".URL_REDIRECT_APPEND."&user_origin=$user_origin&ln=".strtr($patient['name_last'],' ','+')."&fn=".strtr($patient['name_first'],' ','+')."&bd=".formatDate2Local($patient['date_birth'],$date_format)."&encounter_nr=$encounter_nr&nodoc=labor&job_id=$job_id&parameterselect=$parameterselect&allow_update=$allow_update&from=$from");
		 	else header("location:".$root_path."modules/nursing/nursing-station-patientdaten-nolabreport.php?sid=$sid&lang=$lang&edit=$edit&station=$station&pn=$encounter_nr&nodoc=labor&user_origin=$user_origin");
			exit;
	}

}else{
	echo "<p>".$lab_obj->getLastQuery()."sql$LDDbNoRead";exit;
}

# Start Smarty templating here
 /**
 * LOAD Smarty
 */
 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

# Title in toolbar
 $smarty->assign('sToolbarTitle',"$LDLabReport $station");

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('lab_list.php','','','','$LDLabReport')");

 # hide return  button
 $smarty->assign('pbBack',FALSE);

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$LDLabReport $station");

 # collect extra javascript code
 ob_start();
?>

<style type="text/css" name="1">
.va12_n{font-family:verdana,arial; font-size:12; color:#000099}
.a10_b{font-family:arial; font-size:10; color:#000000}
.a10_n{font-family:arial; font-size:10; color:#000099}
.a12_b{font-family:arial; font-size:12; color:#000000}
.j{font-family:verdana; font-size:12; color:#000000}
</style>

<script language="javascript">
<!-- Script Begin
var toggle=true;
function selectall(){

	d=document.labdata;
	var t=d.ptk.value;
	
	if(t==1){
		if(toggle==true){ d.tk.checked=true;}
	}else{
		for(i=0;i<t;i++){
			if(toggle==true){d.tk[i].checked=true; }
		}
	}
	if(toggle==false){ 
		d.reset();
	}
	toggle=(!toggle);

}

function prep2submit(){
	d=document.labdata;
	var j=false;
	var t=d.ptk.value;
	var n=false;
	for(i=0;i<t;i++)
	{
		if(t==1) {
			n=d.tk;
			v=d.tk.value;
		}else{
			n=d.tk[i];
			v=d.tk[i].value;
		}
		if(n.checked==true){
			if(j){
				d.params.value=d.params.value +"~"+v;
			}else{ 
				d.params.value=v;	
				j=1;
			}
		 }
	}
	if(d.params.value!=''){
		d.submit();
	}else{
		alert("<?php echo $LDCheckParamFirst ?>");
	}
}

// added by VAN 01-31-08
function viewPatientResult(refno, code){	
	//alert("refno , code = "+refno+" - "+code);
	window.open("seg-lab-request-result-pdf.php?refno="+refno+"&service_code="+code+"&status="+status+"&showBrowser=1","viewPatientResult","width=620,height=440,menubar=no,resizable=yes,scrollbars=yes")
}

function viewPatientResult_Summary(refno, code){	
	//alert("refno , code = "+refno+" - "+code);
	window.open("seg-lab-request-result-summary-pdf.php?refno="+refno+"&service_code="+code+"&status="+status+"&showBrowser=1","viewPatientResult_Summary","width=620,height=440,menubar=no,resizable=yes,scrollbars=yes")
}


//  Script End -->
</script>

<?php 

$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

# Assign patient basic elements
$smarty->assign('LDCaseNr',$LDCaseNr);

#added by VAN 01-29-08
$smarty->assign('LDHospNr','Hospital No.');

$smarty->assign('LDLastName',$LDLastName);
$smarty->assign('LDName',$LDName);
$smarty->assign('LDBday',$LDBday);

# Assign patient basic data
$smarty->assign('encounter_nr',$encounter_nr);

#added by VAN 01-29-08
$smarty->assign('pid',$pid);

$smarty->assign('sLastName',$patient['name_last']);
$smarty->assign('sName',$patient['name_first']);
$smarty->assign('sBday',formatDate2Local($patient['date_birth'],$date_format));

# Assign link  to generate graphic display of results
#echo "LDClk2Graph = ".$LDClk2Graph;
$smarty->assign('sMakeGraphButton', '<img '.createComIcon($root_path,'chart.gif','0','absmiddle').'> '.$LDClk2Graph);

# Buffer page output

ob_start();

echo '
<form action="labor-data-makegraph.php" method="post" name="labdata">
<table border=0 cellpadding=0 cellspacing=1>';

if(empty($cache)){

	# Get the number of colums
	$cols=sizeof($records);
	
	#edited by VAN 01-30-08
	/*
	$cache= '
		<tr bgcolor="#dd0000" >
		<td class="va12_n"><font color="#ffffff"> &nbsp;<b>'.$LDParameter.'</b>
		</td>
		<td  class="j"><font color="#ffffff">&nbsp;<b>'.$LDNormalValue.'</b>&nbsp;</td>
		<td  class="j"><font color="#ffffff">&nbsp;<b>'.$LDMsrUnit.'</b>&nbsp;</td>
		';
	*/
	
	$cache= '
		<tr bgcolor="#dd0000" >
		<td class="va12_n"><font color="#ffffff"> &nbsp;<b>Reference No.</b></td>
		<td  class="j"><font color="#ffffff">&nbsp;<b>Request Date</b>&nbsp;</td>
		<td  class="j"><font color="#ffffff">&nbsp;<b>Service Requested</b>&nbsp;</td>
		<td  class="j"><font color="#ffffff">&nbsp;<b>Results (Detailed)</b>&nbsp;</td>
		<td  class="j"><font color="#ffffff">&nbsp;<b>Results (Summary)</b>&nbsp;</td>
		';
	
		/*
	while(list($x,$v)=each($tdate)){
		$cache.= '
		<td class="a12_b"><font color="#ffffff">&nbsp;<b>'.formatDate2Local($v,$date_format).'<br>'.$x.'</b>&nbsp;</td>';
	}
*/
	$cache.= '
		<tr bgcolor="#ffddee" >
		<td class="va12_n"><font color="#ffffff"> &nbsp;
		</td>
		<td class="va12_n"><font color="#ffffff"> &nbsp;
		</td>
		<td class="va12_n"><font color="#ffffff"> &nbsp;
		</td>
		<td  class="j"><font color="#ffffff">&nbsp;</td>';
/*

	while(list($x,$v)=each($ttime)){
		$cache.= '
		<td class="a12_b"><font color="#0000cc">&nbsp;<b>'.convertTimeToLocal($v).'</b> '.$LDOClock.'&nbsp;</td>';
	}
*/	
	# Reset array
	reset($ttime);
	/*
	$cache.= '
		<td>&nbsp;<a href="javascript:selectall()"><img '.createComIcon($root_path,'dwnarrowgrnlrg.gif','0','absmiddle',TRUE).' alt="'.$LDClk2SelectAll.'"></a>
		</tr>';
	*/
	$cache.= '
		<td>&nbsp;</td>
		</tr>';
	
	#commented by VAN 01-31-08
	# Display the values
	/*
	$tracker=0;
	$ptrack=0;

	while(list($group_id,$param_group)=each($paralistarray)){
	
		$grpflag=true;

		while(list($param,$pname)=each($param_group)){

			$flag=false;

			$txt='';

			# Reset the array
			reset($tdate);
			while(list($jid,$xval)=each($tdate)){

				$txt.= '
				<td class="j">&nbsp;';
				if(!empty($records[$jid][$group_id][$param])) {
					if($tp[$param]['hi_bound']&&$records[$jid][$group_id][$param]>$tp[$param]['hi_bound']){
						$txt.='<img '.createComIcon($root_path,'arrow_red_up_sm.gif','0','',TRUE).'> <font color="red">'.htmlspecialchars($records[$jid][$group_id][$param]).'</font>';
					}elseif($records[$jid][$group_id][$param]<$tp[$param]['lo_bound']){
						$txt.='<img '.createComIcon($root_path,'arrow_red_dwn_sm.gif','0','',TRUE).'> <font color="red">'.htmlspecialchars($records[$jid][$group_id][$param]).'</font>';
					}else{
						$txt.=htmlspecialchars($records[$jid][$group_id][$param]);
					}
					$flag=true;
				}
				$txt.='&nbsp;</td>';
			}
			# If a value exist, display the row
			if($flag){

				# If parameters info not yet loaded, load now
				if($grpflag){
					$tparams=&$lab_obj->TestParams($group_id);
					$grpflag=false;
					while($tpbuf=&$tparams->FetchRow())	$tp[$tpbuf['id']]=&$tpbuf;
				}

				# Create the front colum boxes
				//$txx='<tr bgcolor=';
				//if($toggle) { $txx.= '"#ffdddd"';}else { $txx.= '"#ffeeee"';}
				$txx='<tr class=';
				if($toggle) { $txx.= '"wardlistrow1"';}else { $txx.= '"wardlistrow2"';}
				$txx.= '>
				<td class="va12_n"> &nbsp;<nobr><a href="#">'.$pname.'</a></nobr>
				</td>
				<td class="a10_b" >&nbsp;';
				if($tp[$param]['lo_bound']&&$tp[$param]['hi_bound']) $txx.=$tp[$param]['lo_bound'].' - '.$tp[$param]['hi_bound'];
				$txx.='</td>
				<td class="a10_b" >&nbsp;'.$tp[$param]['msr_unit'].'</td>';
				# Print the final row

				$cache.=$txx.$txt.'<td>
				<input type="checkbox" name="tk" value="'.$tracker.'">
				</td></tr>';


				$ptrack++;
				$toggle=!$toggle;
			}
			$tracker++;
		}
	}
	$cache.='
		<input type="hidden" name="colsize" value="'.$cols.'">
		<input type="hidden" name="params" value="">
		<input type="hidden" name="ptk" value="'.$ptrack.'">
		';
	# Delete old cache data first
	$lab_obj->deleteDBCache('chemlabs_result_'.$encounter_nr.'_%');
	# Save new cache data
	$lab_obj->saveDBCache('chemlabs_result_'.$encounter_nr.'_'.$modtime,$cache);
	*/
	
	
	foreach ($records as $i=>$buffer) {
		if ($buffer) {
			$count++;
			$alt = ($count%2)+1;
			
			$rdetailed = '<a href="javascript: viewPatientResult(\''.$buffer['ref'].'\', \''.stripslashes($buffer['code']).'\')"><img src="../../images/findings.gif" border="0"/></a>';
			$rsummary = '<a href="javascript: viewPatientResult_Summary(\''.$buffer['ref'].'\', \''.stripslashes($buffer['code']).'\')"><img src="../../images/findings2.gif" border="0"/></a>';
			
			$cache .= 
					'<tr class="wardlistrow'.$alt.'" id="row'.$buffer['code'].'">
						<td class="centerAlign">'.$buffer['ref'].'</td>
						<td class="centerAlign">'.date("m-d-Y",strtotime($buffer['serv_dt'])).'</td>
						<td class="centerAlign">'.$buffer['service_name'].'</td>
						<td class="centerAlign">'.$rdetailed.'</td>
						<td class="centerAlign">'.$rsummary.'</td>
					</tr>
			';    #number_format($prc, 2)
		}	
		#$smarty->assign('sLabResultsTable',$sTemp);
	}
}

# Show the lab results table from the cache

echo $cache;

echo '</table>';

echo '
<input type="hidden" name="sid" value="'.$sid.'">
<input type="hidden" name="from" value="'.$from.'">
<input type="hidden" name="encounter_nr" value="'.$encounter_nr.'">
<input type="hidden" name="edit" value="'.$edit.'">
<input type="hidden" name="lang" value="'.$lang.'">';

if($from=='input'){
	echo '
<input type="hidden" name="parameterselect" value="'.$parameterselect.'">
<input type="hidden" name="job_id" value="'.$job_id.'">
<input type="hidden" name="allow_update" value="'.$allow_update.'">';
}

echo '
<input type="hidden" name="user_origin" value="'.$user_origin.'">
</form>';

$sTemp = ob_get_contents();
ob_end_clean();

$smarty->assign('sLabResultsTable',$sTemp);

$smarty->assign('sClose','<a href="'.$breakfile.'"><img '.createLDImgSrc($root_path,'close2.gif','0','absmiddle').' alt="'.$LDClose.'"></a>');

# Assign the include file to main frame template

 $smarty->assign('sMainBlockIncludeFile','laboratory/chemlab_data_results_show.tpl');

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');

?>
