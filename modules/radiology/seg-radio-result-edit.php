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
require_once($root_path.'include/care_api_classes/class_radioservices_transaction.php');
$srv=new SegRadio();

require($root_path.'include/inc_labor_param_group.php');


# Load the date formatter 
include_once($root_path.'include/inc_date_format_functions.php');

# Create person object
include_once($root_path.'include/care_api_classes/class_person.php');
$person_obj = new Person($pid);

# Create doctor object
require_once($root_path.'include/care_api_classes/class_personell.php');
$personell_obj=new Personell;
echo "seg-radio-result-edit.php : dept_nr = '".$dept_nr."' <br> \n";
$doctors=&$personell_obj->getDoctorsOfDept($dept_nr);

# Create address object
include_once($root_path.'include/care_api_classes/class_address.php');
$address_brgy = new Address('barangay');

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

if($pid){
	if(!($basicInfo = $person_obj->getAllInfoArray($pid))){
		echo '<em class="warn">Sorry but the page cannot be displayed!</em> <br>';
		echo "<em class='warn'> intval(pid) = '".intval($pid)."' </em> <br> \n";
		echo "<em class='warn'> person_obj->sql = '".$person_obj->sql."' </em> <br> \n";
		exit();
	}
	extract($basicInfo);
	$brgy_info = $address_brgy->getAddressInfo($brgy_nr,TRUE);
	if($brgy_info){
		$brgy_row = $brgy_info->FetchRow();
	}
}else{
	echo '<em class="warn"> Sorry but the page cannot be displayed! <br> Invalid HRN!</em>';
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

if($mode=='save'){
	# Save the nr
	
	$x = array();
	$xrow=$_POST['row'];
	$xcode=$_POST['service_code'];
	$xname=$_POST['name'];
	$xcash=($_POST['cash']!=''&&isset($_POST['cash']))?$_POST['cash']:'NULL';
	$xcharge=($_POST['charge']!=''&&isset($_POST['cash']))?$_POST['charge']:'NULL';
	$xstatus=$_POST['status'];
	
	$xgid=$_POST['groupcode'];

	if ($srv->updateRadioService($_POST['excode'],$xcode, $xname, $xcash, $xcharge, $xstatus, $xgid)) {
		# xrow(rowno, code, name, cash, charge)
		$cd=$_POST['service_code']?$_POST['service_code']:'';
		$nm=$_POST['name']?$_POST['name']:'';
		$csh=$_POST['cash']?$_POST['cash']:'null';
		$chrg=$_POST['charge']?$_POST['charge']:'null';
		$xrowArg = $_POST['row'].",'$cd','$nm',$csh,$chrg";
?>

<script language="JavaScript">
<!-- Script Begin
window.opener.xrow(<?= $xrowArg ?>);
//alert("xrow"+<?=$xrowArg ?>);
window.close();
//  Script End -->
</script>

<?php
		exit;
	}
	else {
		echo $srv->sql;
	}
# end of if(mode==save)
} 	

$sNames=array("Service Code", "Service Name", "Price(Cash)", "Price(Charge)","Status");
$sItems=array('service_code','name','price_cash','price_charge','status');

#print_r($sNames);

# Get the radiology service values

if($tsrv=&$srv->getRadioServicesInfo("service_code='".addslashes($nr)."' AND s.group_code = sg.group_code")){
	$ts=$tsrv->FetchRow();
	#echo "sql = ".$srv->sql;
}else{
	$ts=false;
}
	
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

</HEAD>

<BODY topmargin=0 leftmargin=0 marginwidth=0 marginheight=0 
<?php
/*if($newid) echo ' onLoad="document.datain.test_date.focus();" ';*/
 if (!$cfg['dhtml']){ echo 'link='.$cfg['body_txtcolor'].' alink='.$cfg['body_alink'].' vlink='.$cfg['body_txtcolor']; } 
 ?>>

<table width=100% border=0 cellspacing=0 cellpadding=0>
	<tr>
		<td bgcolor="<?php echo $cfg['top_bgcolor']; ?>" >
			<FONT  COLOR="<?php echo $cfg['top_txtcolor']; ?>"  SIZE=+2  FACE="Arial">
				<STRONG> &nbsp;
<?php 	
	echo "					".$ts['group_name'];
?>
				</STRONG>
			</FONT>
		</td>
		<td bgcolor="<?php echo $cfg['top_bgcolor']; ?>" height="10" align=right>
			<nobr>
			<a href="javascript:gethelp('lab_param_edit.php')">
				<img <?php echo createLDImgSrc($root_path,'hilfe-r.gif','0'); ?>  <?php if($cfg['dhtml']) echo'style=filter:alpha(opacity=70) onMouseover=hilite(this,1) onMouseOut=hilite(this,0)>';?>>
			</a>
			<a href="javascript:window.close()" ><img <?php echo createLDImgSrc($root_path,'close2.gif','0') ?>  <?php if($cfg['dhtml'])echo'style=filter:alpha(opacity=70) onMouseover=hilite(this,1) onMouseOut=hilite(this,0)>';?>></a>
			</nobr>
		</td>
	</tr>
	<tr align="center">
		<td  bgcolor=#dde1ec colspan=2>
			<FONT SIZE=-1 FACE="Arial">
			<form action="<?php echo $thisfile; ?>" method="post" name="paramedit">
				<table border=0 bgcolor=#ffdddd cellspacing=1 cellpadding=1 width="100%">
					<tr>
						<td bgcolor=#ff0000 colspan=2>
							<FONT SIZE=2 FACE="Verdana,Arial" color="#ffffff">
								<b>
<?php 
		echo "									".$ts['group_code']; #echo $parametergruppe[$ts['group_id']]; 
?>
								</b>
						</td>
					</tr>
					<tr>
						<td colspan=2>
							<table border="0" cellpadding=2 cellspacing=1>
<?php 
$toggle=0;

if($ts){
?>
								<tr>
									<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">Service Code</td>
									<td bgcolor="#ffffee" class="a12_b">
										<?= $ts['service_code'] ?>
									</td>
								</tr>
								<tr>
									<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">Service Name</td>
									<td bgcolor="#ffffee" class="a12_b">
										<?= $ts['service_name'] ?>
									</td>
								</tr>
								<tr>
									<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">Clinical Impression</td>
									<td bgcolor="#ffffee" class="a12_b">
										<?= $ts['clinical_info'] ?>
									</td>
								</tr>
								<tr>
									<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">Date of Service</td>
									<td bgcolor="#ffffee" class="a12_b">
<?php
	$phpfd=$date_format;
	$phpfd=str_replace("dd", "%d", strtolower($phpfd));
	$phpfd=str_replace("mm", "%m", strtolower($phpfd));
	$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
				
	if (($service_date!='0000-00-00')  && ($service_date!=""))
							$service_date = @formatDate2Local($service_date,$date_format);
						else
							$service_date='';
					
						$sServiceDate= '<input name="service_date" type="text" size="15" maxlength=10 value="'.$findings_date.'"'. 
									'onFocus="this.select();"  
									id = "service_date" 
									onBlur="IsValidDate(this,\'MM/dd/yyyy\'); "
									onChange="IsValidDate(this,\'MM/dd/yyyy\'); "
									onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">
									<img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="service_date_trigger" style="cursor:pointer" >
									<font size=3>['; 			
						ob_start();
					?>
                                    <script type="text/javascript">
						Calendar.setup ({
								inputField : "service_date", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "service_date_trigger", singleClick : true, step : 1
						});
					          </script>
                                    <?php
						$calendarSetup = ob_get_contents();
						ob_end_clean();
				
						$sServiceDate .= $calendarSetup;
						/**/
						$dfbuffer="LD_".strtr($date_format,".-/","phs");
						$sServiceDate = $sServiceDate.$$dfbuffer.']';
?>
										<?= $sServiceDate ?>
									</td>
								</tr>
								<tr>
									<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">Findings</td>
									<td bgcolor="#ffffee" class="a12_b">
										<textarea name="findings" id="findings" cols="35" rows="8" style="width:100%"><?= $ts['findings'] ?></textarea>
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
										<select name="doctor_id" id="doctor_id">
											<option value='0'>-Select a doctor-</option>
<?php
		# list all doctors under radiology department
	while ($doc = $doctors->FetchRow()){
	  	$doc_name = $doc["name_first"]." ".$doc["name_2"]." ".$doc["name_last"];
		$doc_name = "Dr. ".ucwords(strtolower($doc_name));
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
                     <input type=hidden name="groupcode" value="<?= $ts['group_code']  ?>">							
							<input type=hidden name="batch_nr" value="<?= $batch_nr ?>">
							<input type=hidden name="mode" value="<?= $mode ?>">
<!--
                     <input type="image" <?php echo createLDImgSrc($root_path,'savedisc.gif','0') ?>>
-->
<?php
						if ($mode=="save"){
							$image_show = "createLDImgSrc($root_path,'savedisc.gif','0')";
						}else{
							$image_show = "createLDImgSrc($root_path,'update.gif','0')";
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
