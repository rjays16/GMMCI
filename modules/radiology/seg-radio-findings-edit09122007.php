<?php

define('ROW_MAX',15); # define here the maximum number of rows for displaying the parameters

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/inc_date_format_functions.php');

$lang_tables=array('chemlab_groups.php','chemlab_params.php');
define('LANG_FILE','lab.php');
$local_user='ck_lab_user';
require_once($root_path.'include/inc_front_chain_lang.php');

$thisfile=basename(__FILE__);

//$db->debug=true;

# Create lab object
#require_once($root_path.'include/care_api_classes/class_radioservices_transaction.php');
#$srv=new SegRadio();

require($root_path.'include/inc_labor_param_group.php');


# Load the date formatter 
include_once($root_path.'include/inc_date_format_functions.php');

# Create address object
include_once($root_path.'include/care_api_classes/class_address.php');
$address_brgy = new Address('barangay');

# Create radiology object
require_once($root_path.'include/care_api_classes/class_radiology.php');
$radio_obj = new SegRadio;

switch($mode){
	case 'save':
#echo "seg-radio-result-edit.php : save mode = '".$mode."' <br> \n";
#echo "seg-radio-result-edit.php : _POST : "; print_r($_POST); echo " <br><br> \n";
#echo "seg-radio-result-edit.php : _POST['findings_date'] = '".formatDate2STD($_POST['findings_date'], $date_format)."' <br> \n";
/*
#			if($radio_obj->saveAFinding($batch_nr,$finding_nr,$findings,$radio_impression,$findings_date,$doctor_id,'Add')){
( [findings] => Findings 1 [radio_impression] => Radiographic Impression 1 [findings_date] => 08/06/2007 [doctor_id] => 100099 [nr] => [sid] => 337a052d89fc5a5957349f6c67fc8010 [lang] => en [excode] => [row] => [findings_nr] => 0 [batch_nr] => 2007000002 [mode] => save [x] => 56 [y] => 18 ) 
*/
/*
			$_POST['findings'][$_POST['findings_nr']] = $_POST['findings'];
			$_POST['findings_date'][$_POST['findings_nr']] = $_POST['findings_date'];
			$_POST['doctor_in_charge'][$_POST['findings_nr']] = $_POST['doctor_in_charge'];
			$_POST['radio_impression'][$_POST['findings_nr']] = $_POST['radio_impression'];
*/
			$_POST['findings'] = array($_POST['findings']);
			$_POST['findings_date'] = formatDate2STD($_POST['findings_date'], $date_format);# reformat FROM mm/dd/yyyy TO yyyy-mm-dd
			$_POST['findings_date'] = array($_POST['findings_date']);
			$_POST['doctor_in_charge'] = array($_POST['doctor_in_charge']);
			$_POST['radio_impression'] = array($_POST['radio_impression']);

			$_POST['findings'] = serialize($_POST['findings']);
			$_POST['findings_date'] = serialize($_POST['findings_date']);
			$_POST['doctor_in_charge'] = serialize($_POST['doctor_in_charge']);
			$_POST['radio_impression'] = serialize($_POST['radio_impression']);
			$_POST['history']="Created : ".date('Y-m-d H:i:s')." = ".$HTTP_SESSION_VARS['sess_user_name']."\n";
#echo "seg-radio-result-edit.php : 2 _POST : "; print_r($_POST); echo " <br><br> \n";

			if($radio_obj->saveRadioFindingInfoFromArray($_POST)){
	     		$errorMsg='<font style="color:#FF0000">Successfully saved!</font>';
				$errorMsg.='<script language="javascript">'.
								'javascript:self.parent.location.href=self.parent.location.href;'.
								'</script>';
			}else{
	     		$errorMsg='<font style="color:#FF0000">'.$radio_obj->getErrorMsg().'</font>';
			}
			break;
	case 'update':
#echo "seg-radio-result-edit.php : update mode = '".$mode."' <br> \n";			
#echo "seg-radio-result-edit.php : _POST : "; print_r($_POST); echo " <br><br> \n";

			$_POST['findings_date'] = formatDate2STD($_POST['findings_date'], $date_format);# reformat FROM mm/dd/yyyy TO yyyy-mm-dd
#			if($radio_obj->saveAFinding($batch_nr,$finding_nr,$findings,$radio_impression,$findings_date,$doctor_id,'Update')){
			if($radio_obj->saveAFinding($_POST['batch_nr'],$_POST['findings_nr'],$_POST['findings'],
						$_POST['radio_impression'],$_POST['findings_date'],$_POST['doctor_in_charge'],'Update')){
	     		$errorMsg='<font style="color:#FF0000">Successfully updated!</font>';
				$errorMsg.='<script language="javascript">'.
								'javascript:self.parent.location.href=self.parent.location.href;'.
								'</script>';
			}else{
	     		$errorMsg='<font style="color:#FF0000">'.$radio_obj->getErrorMsg().'</font>';
			}
			break;
}# end of switch stmt


/*
PARAMETER needed : 
	[1] batch_nr (encounter_nr?)
	[2] row number

SELECT enc.pid,
	r_request.batch_nr, r_request.encounter_nr, r_request.clinical_info, 
	r_request.service_code, r_request.service_date,	r_request.if_in_house, 
	r_request.request_doctor, r_request.request_date, r_request.status,
	r_request.encoder AS request_encoder,
	r_findings.findings, r_findings.findings_date, r_findings.doctor_in_charge,
	r_findings.encoder AS findings_encoder,
	r_services.name AS service_name, r_services.price_cash, r_services.price_charge,
	r_serv_group.group_code AS group_code, r_serv_group.name AS group_name, r_serv_group.other_name,
	dept.name_formal AS service_dept_name
FROM care_test_request_radio AS r_request
	LEFT JOIN care_encounter AS enc ON enc.encounter_nr = r_request.encounter_nr
	LEFT JOIN care_test_findings_radio AS r_findings ON r_request.batch_nr = r_findings.batch_nr
	LEFT JOIN seg_radio_services AS r_services ON r_request.service_code = r_services.service_code
		LEFT JOIN seg_radio_service_groups AS r_serv_group ON r_services.group_code = r_serv_group.group_code
			LEFT JOIN care_department AS dept ON r_serv_group.department_nr = dept.nr
WHERE r_request.batch_nr='2007000005' AND r_request.encounter_nr='2007000005'
*/


if ($radio_obj->batchNrHasRadioFindings($batch_nr)){
	$mode ='update';
}else{
	$mode ='save';
}

$radioRequestInfo = $radio_obj->getAllRadioInfoByBatch($batch_nr);
/*
echo "seg-radio-result-edit.php : batch_nr = '".$batch_nr."' <br> \n";
echo "seg-radio-result-edit.php : findings_nr = '".$findings_nr."' <br> \n";
echo "seg-radio-result-edit.php : mode = '".$mode."' <br> \n";

echo "seg-radio-result-edit.php : radioRequestInfo : "; print_r($radioRequestInfo); echo " <br><br> \n";
*/
if ($radioRequestInfo){
	$pid = $radioRequestInfo['pid'];
	$t_findings =	unserialize($radioRequestInfo['findings']);
	$count_findings = 0;
	if (is_array($t_findings)){
		$count_findings = count($t_findings);
	}
#echo "seg-radio-result-edit.php : count_findings = '".$count_findings."' <br> \n";
	$t_radio_impression =	unserialize($radioRequestInfo['radio_impression']);
	$t_date = unserialize($radioRequestInfo['findings_date']);
	$t_doc =	unserialize($radioRequestInfo['doctor_in_charge']);
	$doctor_in_charge = $t_doc[$findings_nr];
	$findings_date = $t_date[$findings_nr];
	$radio_impression = $t_radio_impression[$findings_nr];
	$findings = $t_findings[$findings_nr];
/*
echo "seg-radio-result-edit.php : doctor_in_charge= '".$doctor_in_charge."' <br> \n";
echo "seg-radio-result-edit.php : findings_date = '".$findings_date."' <br> \n";
echo "seg-radio-result-edit.php : radio_impression = '".$radio_impression."' <br> \n";
echo "seg-radio-result-edit.php : findings = '".$findings."' <br> \n";
*/
}

# Create person object
include_once($root_path.'include/care_api_classes/class_person.php');
$person_obj = new Person($pid);

# Create doctor object
require_once($root_path.'include/care_api_classes/class_personell.php');
$personell_obj=new Personell;

#echo "seg-radio-result-edit.php : dept_nr = '".$dept_nr."' <br> \n";
#echo "seg-radio-result-edit.php : radioRequestInfo['service_dept_nr'] = '".$radioRequestInfo['service_dept_nr']."' <br> \n";

$doctors=&$personell_obj->getDoctorsOfDept($radioRequestInfo['service_dept_nr']);

#echo "seg-radio-result-edit.php : personell_obj->sql = '".$personell_obj->sql."' <br> \n";
#echo "seg-radio-result-edit.php : doctors : "; print_r($doctors); echo " <br> \n";

if($pid){
	if(!($basicInfo = $person_obj->getAllInfoArray($pid))){
		echo '<em class="warn">Sorry but the page cannot be displayed!</em> <br>';
#		echo "<em class='warn'> intval(pid) = '".intval($pid)."' </em> <br> \n";
#		echo "<em class='warn'> person_obj->sql = '".$person_obj->sql."' </em> <br> \n";
		exit();
	}
	extract($basicInfo);
	$brgy_info = $address_brgy->getAddressInfo($brgy_nr,TRUE);
	if($brgy_info){
		$brgy_row = $brgy_info->FetchRow();
	}
}else{
	echo '<em class="warn"> Sorry but the page cannot be displayed! <br> Invalid PID!</em>';
	exit();
}
/*
	radioInfo = 
*/

$excode=$_GET['nr'];
$grpcode =$_GET['grpcode'];
#echo "grpcode = ".$grpcode;
#echo "<br>groupid = ".$_POST['groupid'];
if(isset($_POST['excode'])) $excode=$_POST['excode'];

$sNames=array("Service Code", "Service Name", "Price(Cash)", "Price(Charge)","Status");
$sItems=array('service_code','name','price_cash','price_charge','status');

#print_r($sNames);

# Get the radiology service values
/*
if($tsrv=&$srv->getRadioServicesInfo("service_code='".addslashes($nr)."' AND s.group_code = sg.group_code")){
	$ts=$tsrv->FetchRow();
	#echo "sql = ".$srv->sql;
}else{
	$ts=false;
}
*/	
?>
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 3.0//EN" "html.dtd">
<?php html_rtl($lang); ?>
<HEAD>
<?php echo setCharSet(); ?>
 <TITLE>Edit Radiology Service</TITLE>

<script language="javascript" name="j1">
<!--        
function editParam(nr)
{
	urlholder="labor_test_param_edit?sid=<?php echo "$sid&lang=$lang" ?>&nr="+encodeURIComponent(nr);
	editparam_<?php echo $sid ?>=window.open(urlholder,"editparam_<?php echo $sid ?>","width=500,height=600,menubar=no,resizable=yes,scrollbars=yes");
}
// -->
</script>

<script language="javascript">
		/*	
			This will trim the string i.e. no whitespaces in the
			beginning and end of a string AND only a single
			whitespace appears in between tokens/words 
			input: object
			output: object (string) value is trimmed
		*/
	function trimString(objct){
		objct.value = objct.value.replace(/^\s+|\s+$/g,"");
		objct.value = objct.value.replace(/\s+/g," "); 
	}/* end of function trimString */

	function checkFindingForm(){
		if ($F('findings')==''){
			alert('Please write the Findings.');
			$('findings').focus();
			return false;
		}else if ($F('radio_impression')==''){
			alert('Please write the Radiographic Impression.');
			$('radio_impression').focus();
			return false;
		}else if ($F('findings_date')==''){
			alert('Please enter the date.');
			$('findings_date').focus();
			return false;
		}else if ($F('doctor_in_charge')==0){
			alert('Please select a doctor.');
			$('doctor_in_charge').focus();
			return false;
		}
		return true;
	}
</script>
<?php 
require($root_path.'include/inc_js_gethelp.php'); 
require($root_path.'include/inc_css_a_hilitebu.php');
?>
<style type="text/css" name="1">
.va12_n{font-family:verdana,arial; font-size:12; color:#000099}
.a10_b{font-family:arial; font-size:10; color:#000000}
.a12_b{font-family:arial; font-size:12; color:#000000}
.a10_n{font-family:arial; font-size:10; color:#000099}
</style>

<script language="javascript">
<?php
	require_once($root_path.'include/inc_checkdate_lang.php'); 
?>
</script>

<?php
	echo '<script type="text/javascript" language="javascript" src="'.$root_path.'js/jsprototype/prototype.js"></script>'."\n";

	echo '<link rel="stylesheet" type="text/css" media="all" href="' .$root_path.'js/jscalendar/calendar-win2k-cold-1.css">'."\n";
/*	
	echo '<script type="text/javascript" src="'.$root_path.'js/fat/fat.js"></script>'."\r\n";
*/
	echo '<script language="javascript" src="'.$root_path.'js/setdatetime.js"></script>'."\n";
	echo '<script language="javascript" src="'.$root_path.'js/checkdate.js"></script>'."\n";
	echo '<script language="javascript" src="'.$root_path.'js/dtpick_care2x.js"></script>'."\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar.js"></script>'."\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar.js"></script>'."\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/lang/calendar-en.js"></script>'."\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar-setup_3.js"></script>'."\n";
?>
</HEAD>

<BODY topmargin=0 leftmargin=0 marginwidth=0 marginheight=0 

<?php
#echo 'onUnload="javascript:self.opener.location.href=self.opener.location.href; "';
/*if($newid) echo ' onLoad="document.datain.test_date.focus();" ';*/
 if (!$cfg['dhtml']){ echo 'link='.$cfg['body_txtcolor'].' alink='.$cfg['body_alink'].' vlink='.$cfg['body_txtcolor']; } 
 ?>>

<?= $errorMsg ?>

<table width=100% border=0 cellspacing=0 cellpadding=0>
	<tr>
		<td bgcolor="<?php echo $cfg['top_bgcolor']; ?>" >
			<FONT  COLOR="<?php echo $cfg['top_txtcolor']; ?>"  SIZE=+1  FACE="Arial">
				<STRONG> &nbsp;
<?php 	
	echo "					".$radioRequestInfo['group_name'];
?>
				</STRONG>
			</FONT>
		</td>
		<td bgcolor="<?php echo $cfg['top_bgcolor']; ?>" height="10" align=right>
			<nobr>
<!--
			<a href="javascript:gethelp('lab_param_edit.php')">
				<img <?php echo createLDImgSrc($root_path,'hilfe-r.gif','0'); ?>  <?php if($cfg['dhtml']) echo'style=filter:alpha(opacity=70) onMouseover=hilite(this,1) onMouseOut=hilite(this,0)>';?>>
			</a>
			<a href="javascript:window.close()" >
				<img <?php echo createLDImgSrc($root_path,'close2.gif','0') ?>  <?php if($cfg['dhtml'])echo'style=filter:alpha(opacity=70) onMouseover=hilite(this,1) onMouseOut=hilite(this,0)>';?>>
			</a>

			<a href="javascript:window.close()">
				<img <?php echo createLDImgSrc($root_path,'close2.gif','0') ?> >
			</a>
-->
			</nobr>
		</td>
	</tr>
	<tr align="center">
		<td  bgcolor=#dde1ec colspan=2>
			<FONT SIZE=-1 FACE="Arial">
			<form action="<?php echo $thisfile; ?>" method="post" name="paramedit" onSubmit="return checkFindingForm()">
				<table border=0 bgcolor=#ffdddd cellspacing=1 cellpadding=1 width="100%">
					<tr>
						<td bgcolor=#ff0000 colspan=2>
							<FONT SIZE=4 FACE="Verdana,Arial" color="#ffffff">
								<b>
<?php 
		echo "									".$radioRequestInfo['group_code']; #echo $parametergruppe[$ts['group_id']]; 
?>
								</b>
						</td>
					</tr>
					<tr>
						<td colspan=2>
							<table border="0" cellpadding=2 cellspacing=1>
<?php 
$toggle=0;

if($radioRequestInfo){
?>

								<tr>
									<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">Finding No.</td>
									<td bgcolor="#ffffee" class="a12_b">
										<?php echo $findings_nr+1; ?>
									</td>
								</tr>
								<tr>
									<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">Service Code</td>
									<td bgcolor="#ffffee" class="a12_b">
										<?= $radioRequestInfo['service_code'] ?>
									</td>
								</tr>
								<tr>
									<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">Service Name</td>
									<td bgcolor="#ffffee" class="a12_b">
										<?= $radioRequestInfo['service_name'] ?>
									</td>
								</tr>
								<tr>
									<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">Clinical Impression</td>
									<td bgcolor="#ffffee" class="a12_b">
										<?= $radioRequestInfo['clinical_info'] ?>
									</td>
								</tr>
								<tr>
									<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">Date of Service</td>
									<td bgcolor="#ffffee" class="a12_b">
<?php	
	if (($service_date!='0000-00-00')  && ($service_date!=""))
		$service_date = @formatDate2Local($service_date,$date_format);
	else
		$service_date='';
	echo '										'.$service_date;
?>
									</td>
								</tr>
								<tr>
									<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">Findings</td>
									<td bgcolor="#ffffee" class="a12_b">
										<textarea name="findings" id="findings" cols="35" rows="5" style="width:100%" onChange="trimString(this)"><?= $findings ?></textarea>
									</td>
								</tr>
								<tr>
									<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">Radiographic Impression</td>
									<td bgcolor="#ffffee" class="a12_b">
										<textarea name="radio_impression" id="radio_impression" cols="35" rows="5" style="width:100%" onChange="trimString(this)"><?= $radio_impression ?></textarea>
									</td>
								</tr>
								<tr>
									<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">Date</td>
									<td bgcolor="#ffffee" class="a12_b">
<?php
	$phpfd=$date_format;
	$phpfd=str_replace("dd", "%d", strtolower($phpfd));
	$phpfd=str_replace("mm", "%m", strtolower($phpfd));
	$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
				
	if (($findings_date!='0000-00-00')  && ($findings_date!=""))
		$findings_date = @formatDate2Local($findings_date,$date_format);
	else
		$findings_date='';
					
						$sFindingsDate= '<input name="findings_date" type="text" size="15" maxlength=10 value="'.$findings_date.'"'. 
									'onFocus="this.select();"  
									id = "findings_date" 
									onBlur="IsValidDate(this,\'MM/dd/yyyy\'); "
									onChange="IsValidDate(this,\'MM/dd/yyyy\'); "
									onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">
									<img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="findings_date_trigger" style="cursor:pointer" >
									<font size=3>['; 			
						ob_start();
					?>
                                    <script type="text/javascript">
						Calendar.setup ({
								inputField : "findings_date", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "findings_date_trigger", singleClick : true, step : 1
						});
					          </script>
                                    <?php
						$calendarSetup = ob_get_contents();
						ob_end_clean();
				
						$sFindingsDate .= $calendarSetup;
						/**/
						$dfbuffer="LD_".strtr($date_format,".-/","phs");
						$sFindingsDate = $sFindingsDate.$$dfbuffer.']';
?>
										<?= $sFindingsDate ?>
									</td>
								</tr>
								<tr>
									<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">Reporting Doctor</td>
									<td bgcolor="#ffffee" class="a12_b">
										<select name="doctor_in_charge" id="doctor_in_charge">
											<option value='0'>-Select a doctor-</option>
<?php
		# list all doctors under radiology department
	while ($doc = $doctors->FetchRow()){
	  	$doc_name = $doc["name_first"]." ".$doc["name_2"]." ".$doc["name_last"];
		$doc_name = "Dr. ".ucwords(strtolower($doc_name));
		if ($doc['personell_nr']==$doctor_in_charge)
			echo "											<option value='".$doc['personell_nr']."' selected>".$doc_name."</option> \n";
		else
			echo "											<option value='".$doc['personell_nr']."'>".$doc_name."</option> \n";
	}
?>
										</select>
									</td>
								</tr>
<?php
 }
?>
							</table>
							<input type=hidden name="nr" value="<?php echo $nr; ?>">
							<input type=hidden name="sid" value="<?php echo $sid; ?>">
							<input type=hidden name="lang" value="<?php echo $lang; ?>">
							<input type=hidden name="excode" value="<?= $excode ?>">
							<input type=hidden name="row" value="<?= $row ?>">

                     <input type=hidden name="findings_nr" value="<?= $findings_nr  ?>">							
							<input type=hidden name="batch_nr" value="<?= $batch_nr ?>">
							<input type=hidden name="mode" value="<?= $mode ?>">
<!--
                     <input type="image" <?php echo createLDImgSrc($root_path,'savedisc.gif','0') ?>>
-->
<?php
/*
						if ($mode=="save"){
							$image_show = createLDImgSrc($root_path,'savedisc.gif','0');
						}else{
							$image_show = createLDImgSrc($root_path,'update.gif','0');
						}
*/
						if ($findings_nr<$count_findings){
							# update a finding
							$image_show = createLDImgSrc($root_path,'update.gif','0');
						}else{
							# add a new finding
							$image_show = createLDImgSrc($root_path,'savedisc.gif','0');
						}
?>
                     <input type="image" <?= $image_show ?>>
						</td>
					</tr>
				</table>
			</form>
			</FONT>
			<p>
		</td>
	</tr>
</table>        

</BODY>
</HTML>
