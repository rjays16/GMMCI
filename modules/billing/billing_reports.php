<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
define('NO_2LEVEL_CHK',1);
require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'modules/billing/ajax/billing.common.php');//added by nick 2/1/14
require_once($root_path.'include/care_api_classes/class_acl.php');//added by Nick 2/4/14
require_once($root_path.'include/care_api_classes/class_insurance.php');
require_once($root_path.'include/care_api_classes/class_personell.php');
//added by Nick 2/1/14

$objAcl = new Acl($_SESSION['sess_temp_userid']);
$TransmittalReportPermission = $objAcl->checkPermissionRaw('_a_1_transmittalHistoryReport');

if(!(isset($_GET['jasperPrint']) && $_GET['jasperPrint']==1)){
	$AcrStyle = "display:none";//added by Nick 2/22/2014
	$TransmittalHistory = "display:none";
	$ClaimsStyle = "display:none";//added by Genz
	$btnJasperPrint = "display:none;";
}else{
	if($TransmittalReportPermission != 1)
		$TransmittalHistory = "display:none";
	$btnPrint = "display:none";
	$format = "display:none";
}
//end nick

$title='Billing::Reports';
if (!$_GET['from'])
	$breakfile=$root_path."modules/billing/bill-main-menu.php".URL_APPEND."&userck=$userck";
else {
	if ($_GET['from']=='CLOSE_WINDOW')
		$breakfile = "javascript:window.parent.cClick();";
	else
		$breakfile = $root_path.'modules/billing/bill-pass.php'.URL_APPEND."&userck=$userck&target=".$_GET['from'];
}

$thisfile='seg_billing_reports.php';

# Create products object
$dbtable='care_config_global'; // Table name for global configurations
$GLOBAL_CONFIG=array();
# Create global config object
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/inc_date_format_functions.php');

$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
if($glob_obj->getConfig('date_format')) $date_format=$GLOBAL_CONFIG['date_format'];
$date_format=$GLOBAL_CONFIG['date_format'];
$phpfd=$date_format;
$phpfd=str_replace("dd", "%d", strtolower($phpfd));
$phpfd=str_replace("mm", "%m", strtolower($phpfd));
$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
$phpfd=str_replace("yy","%y", strtolower($phpfd));

$php_date_format = strtolower($date_format);
$php_date_format = str_replace("dd","d",$php_date_format);
$php_date_format = str_replace("mm","m",$php_date_format);
$php_date_format = str_replace("yyyy","Y",$php_date_format);
$php_date_format = str_replace("yy","y",$php_date_format);

# Start Smarty templating here
/**
* LOAD Smarty
*/

# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme

require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

# Title in the title bar
$smarty->assign('sToolbarTitle', $title);

# href for the back button
// $smarty->assign('pbBack',$returnfile);

# href for the help button
$smarty->assign('pbHelp',"javascript:gethelp('products_db.php','search','$from','$cat')");

# href for the close button
$smarty->assign('breakfile',$breakfile);

# Window bar title
$smarty->assign('sWindowTitle', $title);

# Assign Body Onload javascript code
$smarty->assign('sOnLoadJs','onLoad=""');

# Collect javascript code
ob_start()
?>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>

<link rel="stylesheet" href="<?= $root_path ?>js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css" />
<script type='text/javascript' src="<?=$root_path?>js/jquery/jquery-1.8.2.js"></script>
<script type='text/javascript' src="<?=$root_path?>js/jquery/ui/jquery-ui-1.9.1.js"></script>
<?php   $xajax->printJavascript($root_path.'classes/xajax_0.5');  ?>

<!-- added by nick 2/1/2014 -->
<div id="print_params" style="display:none;">
	<table width="100%">
		<tr>
			<td width="25%">Print Format:</td>
			<td width="75%">
				<select id="print_format">
					<option id="pdf">PDF</option>
					<option id="excel">Excel</option>
				</select>
			</td>
		</tr>
		<tr id = "personnel_tr">
			<td width="25%">Billing Personnel:</td>
			<td width="75%">
				<select id="personnel">
					<!-- <options id="" selected>- Select a personnel -</options> -->
				</select>
			</td>
		</tr>
	</table>
</div>
<!-- end nick -->

<script type="text/javascript">

var $j = jQuery.noConflict();

var clerks;
function setClerks(data){
	clerks = data;
	addClerks();
}

function addClerks(){
	for(i=0; i<=clerks.length; i++){
		$j('#personnel').append('<option value="'+clerks[i][0]+'">'+clerks[i][1]+'</option>');
	}
}

function debug(data){
	$j('<div style="width:100%;height:100%;""></div>')
	.html('<textarea style="width:100%;height:100%;">'+data+'</textarea>')
	.dialog();
}

function openJasperReport(){
    $j('#print_params').dialog('open');
}

//added by nick 2/1/2014
$j(function(){
	xajax_getClerks();
	$j('#print_params').dialog({
		autoOpen:false,
		modal:true,
		width:400,
		title:"Additional Parameters",
		open:function(x,y){
            switch ($('selreport').value) {
                case 'transmittal_history':
                case 'Billing_Transmittal_Based_On_PHIC_Category':
                    $('personnel_tr').style.display = 'none';
                    break;
                default:
                    $('personnel_tr').style.display = '';
                    break;
            }
		},
		buttons:{
			Print:function(){

					var rep_script = '';
					var report_type = '';
					var nleft = (screen.width - 680)/2;
				    var ntop = (screen.height - 520)/2;
				    var cancel = false;

				    var selected_report = $('selreport').value;
				    var report_dtype = $('daily_bills_rendered_Dtype').value;
				    var clerk = $('personnel').value;

				    var date = "";
				    var month = "";
				    var year = "";

				    var params = "";

				    var type = "";

				    if (selected_report == 'daily_bills_rendered'){
				    	date = $('daily_bills_rendered_date').value
				    	params = "date=" + date;
				    	rep_script = "bills_jasper.php";
				    }else if (selected_report == 'monthly_bills_rendered'){
				    	month = $('monthly_bills_rendered_month').value;
				    	year = $('monthly_bills_rendered_year').value;
				    	params = "month=" + month + "&year=" + year;
				    	rep_script = "bills_jasper.php";
				    //added by nick 2/22/2014
				    }else if(selected_report == 'acr_daily'){
				    	report_dtype = $('acr_daily_dtype').value;
				    	rep_script = 'acr_jasper.php';
				    	params = "date=" + $('acr_daily_date').value;
				    }else if(selected_report == 'acr_monthly'){
				    	report_dtype = $('acr_monthly_dtype').value;
				    	month = $('acr_month').value;
				    	year = $('acr_year').value;
				    	params = "month=" + month + "&year=" + year;
				    	rep_script = "acr_jasper.php";
				    //end nick
				    }else if(selected_report == "promissory_note"){
				    	params = "&date_from="+$('promi_from').value+"&date_to="+$('promi_to').value;
				    	rep_script = "promissory_note_summary.php";
				    }
				    //Added by Genz
				    else if(selected_report == 'claim_paid'){
				    	report_dtype = $('claim_paid_type').value;
				    	from = $('claim_paid_from').value;
				    	to = $('claim_paid_to').value;
				    	params = "from=" + from + "&to=" + to;
				    	rep_script = "claims_paid_jasper.php";
				    }else if(selected_report == 'claim_unpaid'){
				    	report_dtype = $('claim_unpaid_type').value;
				    	from = $('claim_unpaid_from').value;
				    	to = $('claim_unpaid_to').value;
				    	params = "from=" + from + "&to=" + to;
				    	rep_script = "claims_unpaid_jasper.php";
				    }else if(selected_report == 'claim_paid_dr'){
				    	report_dtype = $('claim_paid_dr_type').value;
				    	from = $('claim_paid_dr_from').value;
				    	to = $('claim_paid_dr_to').value;
				    	dr_nr = $('claim_paid_dr').value;
				    	is_senior = $('is_senior_claim').value;
				    	params = "from=" + from + "&to=" + to +"&dr_nr="+ dr_nr + "&is_senior="+is_senior;
				    	rep_script = "claims_paid_dr_jasper.php";
				    }else if(selected_report == 'pf_report'){
				    	to = $('pf_to').value;
				    	from = $('pf_from').value;
				    	dr_nr = $('pf_dr_nr').value;
				    	is_senior = $('is_senior_pf').value;
				    	params = "from=" + from + "&to=" + to +"&dr_nr="+ dr_nr +"&is_senior=" + is_senior;
				    	rep_script = "pf_report.php";
				    }else if(selected_report == 'pf_excess_report'){
				    	to = $('pf_to_ex').value;
				    	from = $('pf_from_ex').value;
				    	dr_nr = $('pf_dr_nr_ex').value;
				    	is_senior = $('is_senior_pf_ex').value;
				    	type = 1;
				    	params = "from=" + from + "&to=" + to +"&dr_nr="+ dr_nr +"&is_senior=" + is_senior+"&type="+type;
				    	rep_script = "pf_excess_reports.php";
				    }else if(selected_report == 'pf_monthly_excess_report'){
				    	to = $('pfs_to_ex').value;
				    	from = $('pfs_from_ex').value;
				    	dr_nr = $('pfs_dr_nr_ex').value;
				    	is_senior = $('is_senior_pfs_ex').value;
				    	type = 2;
				    	params = "from=" + from + "&to=" + to +"&dr_nr="+ dr_nr +"&is_senior=" + is_senior+"&type="+type;
				    	rep_script = "pf_excess_reports.php";
				    } else if(selected_report == 'claim_denied'){
				    	report_dtype = $('claim_denied_type').value;
				    	from = $('claim_denied_from').value;
				    	to = $('claim_denied_to').value;
				    	hcare_id = $('claim_denied_type').value;
				    	params = "fromdte=" + from + "&todte=" + to + "&hcare_id=" + hcare_id + "&status=denied";
				    	rep_script = "eclaims_report_claims.php";
				    }else if(selected_report == 'claim_returned'){
				    	report_dtype = $('claim_returned_type').value;
				    	from = $('claim_returned_from').value;
				    	to = $('claim_returned_to').value;
				    	hcare_id = $('claim_returned_type').value;
				    	params = "fromdte=" + from + "&todte=" + to + "&hcare_id=" + hcare_id + "&status=returned";
				    	rep_script = "eclaims_report_claims.php";
                    } else if (selected_report === 'Billing_Transmittal_Based_On_PHIC_Category') {
                        month_from = $('Billing_Transmittal_Based_On_PHIC_Category_Month_From').value;
                        month_to = $('Billing_Transmittal_Based_On_PHIC_Category_Month_To').value;
                        year = $('Billing_Transmittal_Based_On_PHIC_Category_Year').value;
                        params = "month_from=" + month_from + "&month_to=" + month_to + "&year=" + year;
                        rep_script = "Billing_Transmittal_Based_On_Phic_Category.php";
                    }
				    //Ended by Genz
				    else{
				    	month = $('acr_month').value;
				    	year = $('acr_year').value;
				    	params = "month=" + month + "&year=" + year;
				    	rep_script = "acr_jasper.php";
				    }

				    url = "reports/"+rep_script+"?report="+selected_report
				                                 +"&dtype="+report_dtype
				                                 +"&"+params
				                                 +"&personnel="+clerk
				                                 +"&reportFormat="+$('print_format').value;
					window.open(url, "Transmittal Report", "toolbar=no, status=no, menubar=no, width=700, height=500, location=center, dependent=yes, resizable=yes, scrollbars=yes, top=" + ntop + ",left=" + nleft);
			},
			Cancel:function(){
				$j(this).dialog('close');
			}
		}
	});//print params
});
//end nick

var URL_FORWARD = "<?= URL_APPEND."&clear_ck_sid=$clear_ck_sid" ?>";

function pSearchClose() {
	cClick();
}

function selOnChange() {
	var optSelected = $('selreport').options[$('selreport').selectedIndex];
	var spans = $$('[name=selOptions]');
	for (var i=0; i<spans.length; i++) {
		if (optSelected) {
			if (spans[i].getAttribute("segOption") == optSelected.value) {
				spans[i].style.display = "";
			}
			else
				spans[i].style.display = "none";
		}
	}
}

function openReport() {
	var rep = $('selreport').options[$('selreport').selectedIndex].value
	var url = 'reports/'+rep+'.php?'
	var query = new Array()
	var params = document.getElementsByName('param')
	var paramsD = $('daily_bills_rendered_Dtype').options[$('selreport').selectedIndex].value
	for (var i=0; i<params.length; i++) {
		if (params[i].getAttribute("segOption") == rep) {
			var mit;
			if (params[i].type=='checkbox') mit=params[i].checked;
			else if (params[i].type=='radio') mit=params[i].checked;
			else mit=params[i].value;
			if (mit) query.push(params[i].getAttribute('paramName')+'='+params[i].value)
		}
	}
	// alert(url+query.join('&'))
	window.open(url+query.join('&'),rep,"width=800,height=600,menubar=no,resizable=yes,scrollbars=no");
	return false;
}

</script>

<?php

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);


$month_options = '';
for ($i=1;$i<=12;$i++)
	$month_options .= "									<option value=\"$i\">".date("F", strtotime("$i/1/2000"))."</option>\n";

$year_options = '';
for ($i=1980;$i<((int)date("Y")+50);$i++)
	$year_options .= "									<option value=\"$i\" ".($i==date("Y") ? 'selected="selected"' : "").">$i</option>";

$current_date = date($php_date_format);

ob_start();

//insurances
$insurance_obj = new Insurance();
$insurances = $insurance_obj->getAllActiveFirmsInfo();
$option_insurance .= "<option value='all'>-ALL-</option>";
$option_insurance .= "<option value='non-phic'>NON-PHIC</option>";
if($insurances){
	while($row = $insurances->FetchRow()){
		$option_insurance .= "<option value='".$row['hcare_id']."'>".$row['name']."</option>";
	}
}

//doctors
$doctor_obj = new Personell();
$doctors_res = $doctor_obj->getDoctors();
while($row = $doctors_res->FetchRow()){
	$dr_name = $row["name_last"].", ".$row["name_first"]." ".$row["name_middle"];
	$option_dr .= "<option value='".$row['personell_nr']."'>".$dr_name."</option>";
}

?>



<br>


<form action="<?= $thisfile.URL_APPEND."&target=list&clear_ck_sid=".$clear_ck_sid ?>" method="post" name="suchform" onSubmit="return validate()">
<div style="width:500px">
	<table width="100%" border="0" style="font-size:12px; margin-top:5px" cellspacing="2" cellpadding="2">
		<tbody>
			<tr>
				<td align="left" class="segPanelHeader" ><strong>Report options</strong></td>
			</tr>
			<tr>
				<td nowrap="nowrap" align="right" class="segPanel">
					<table width="100%" border="0" cellpadding="2" cellspacing="0">
						<tr>
							<td width="20%" align="center" nowrap="nowrap"><strong>Select Report Type</strong>
								<select class="segInput" name="selreport" id="selreport" onchange="selOnChange()"/>
									<optgroup label="Daily reports">
										<option value="daily_bills_rendered">Daily Summary of Bills Rendered</option>
									</optgroup>
									<optgroup label="Monthly reports">
										<option value="monthly_bills_rendered">Monthly Summary of Bills Rendered</option>
									</optgroup>
									<optgroup label="Transmittal History" style="<?=$TransmittalHistory?>">
										<option value="transmittal_history" style="<?=$TransmittalHistory?>">Transmittal History</option>
                                        <option value="Billing_Transmittal_Based_On_PHIC_Category">Total no. of Transmittal Based on PhilHealth Category</option>
									</optgroup>
									<optgroup label="ACR" style="<?=$AcrStyle?>">
										<option value="acr_daily" style="<?=$AcrStyle?>">ACR Census Daily</option>
										<option value="acr_monthly" style="<?=$AcrStyle?>">ACR Census Monthly</option>
									</optgroup>
									<!-- Added by Genz for Claims -->
									<optgroup label="Claims Reports" style="<?=$ClaimsStyle?>">
										<option value="claim_paid" style="<?=$ClaimsStyle?>">Claims Paid(Hosp)</option>
										<option value="claim_paid_dr" style="<?=$ClaimsStyle?>">Claims Paid(Doctor)</option>
										<option value="claim_unpaid" style="<?=$ClaimsStyle?>">Claims Unpaid(Hosp)</option>
										<option value="claim_returned" style="<?=$ClaimsStyle?>">Claims Returned</option>
										<option value="claim_denied" style="<?=$ClaimsStyle?>">Claims Denied</option>
									</optgroup>
									<optgroup label="Promissory Note">
										<option value="promissory_note">Promissory Note Summary</option>
									</optgroup>
									<optgroup label="Doctors">
										<option value="pf_report">PF Report</option>
										<option value="pf_excess_report">PF Excess Report</option>
										<option value="pf_monthly_excess_report">PF Monthly Excess Report</option>
									</optgroup>
								</select>
							</td>
						</tr>
					</table>


					<hr width="90%" size="1" style="color:rgba(0,0,0,0.1)"/>

					<!-- ACR - Added by Nick 2/22/2014 -->
					<!-- Daily -->
					<table name="selOptions" width="100%" border="0" cellpadding="2" cellspacing="0" segOption="acr_daily" style="display:none">
						<tr>
							<td align="right" >Delete format</td>
							<td nowrap="nowrap">
								<select class="segInput" id="acr_daily_dtype" name="param" paramName="formatD" segOption="acr_daily">
									<option value="SA">Show All</option>
									<option value="DB">Deleted Bill</option>
									<option value="FB">Final Bill</option>
								</select>
							</td>
						</tr>
							<td align="right" >Select date</td>
							<td nowrap="nowrap">
								<input class="segInput" name="param" id="acr_daily_date" type="text" size="12" value="" paramName="date" segOption="acr_daily"/>
								<img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_acr_daily" align="absmiddle" style="cursor:pointer;"  />
								<script type="text/javascript">
									Calendar.setup ({
										inputField : "acr_daily_date", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_acr_daily", singleClick : true, step : 1
									});
								</script>
							</td>
						</tr>
					</table>
					<!-- Monthly -->
					<table name="selOptions" width="100%" border="0" cellpadding="2" cellspacing="0" segOption="acr_monthly" style="display:none">
						<tr>
							<td align="right" >Delete format</td>
							<td nowrap="nowrap">
								<select class="segInput" id="acr_monthly_dtype" name="param" paramName="formatD" segOption="acr_monthly">
									<option value="SA">Show All</option>
									<option value="DB">Deleted Bill</option>
									<option value="FB">Final Bill</option>
								</select>
							</td>
						</tr>
						<tr>
							<td align="right" >Select month/year</td>
							<td nowrap="nowrap">
								<select class="segInput" id="acr_month" name="param" paramName="month" segOption="acr_monthly">
									<?= $month_options ?>
								</select>
								<select class="segInput" id="acr_year" name="param" paramName="year" segOption="acr_monthly">
									<?= $year_options ?>
								</select>
							</td>
						</tr>
					</table>
					<!-- end ACR -->
					<!-- end nick -->

					<!-- DAILY BILLS RENDERED -->
					<table name="selOptions" width="100%" border="0" cellpadding="2" cellspacing="0" segOption="daily_bills_rendered" style="">
						<tr style="<?=$format?>">
							<td align="right" >Select report format</td>
							<td nowrap="nowrap">
								<select class="segInput" id="daily_bills_rendered_type" name="param" paramName="format" segOption="daily_bills_rendered">
									<option value="CSV">CSV</option>
									<!-- <option value="Excel5" disabled="disabled">Excel</option> -->
								</select>
							</td>
						</tr>
						<tr>
							<td align="right" >Delete format</td>
							<td nowrap="nowrap">
								<select class="segInput" id="daily_bills_rendered_Dtype" name="param" paramName="formatD" segOption="daily_bills_rendered">
									<option value="SA">Show All</option>
									<option value="DB">Deleted Bill</option>
									<option value="FB">Final Bill</option>
									<!-- <option value="Excel5" disabled="disabled">Excel</option> -->
								</select>
							</td>
						</tr>
							<td align="right" >Select date</td>
							<td nowrap="nowrap">
								<input class="segInput" name="param" id="daily_bills_rendered_date" type="text" size="12" value="" paramName="date" segOption="daily_bills_rendered"/>
								<img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_daily_bills_rendered_date" align="absmiddle" style="cursor:pointer;"  />
								<script type="text/javascript">
									Calendar.setup ({
										inputField : "daily_bills_rendered_date", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_daily_bills_rendered_date", singleClick : true, step : 1
									});
								</script>
							</td>
						</tr>
					</table>


					<!-- MONTHLY PER ACCOUNT -->
					<table name="selOptions" width="100%" border="0" cellpadding="2" cellspacing="0" segOption="monthly_bills_rendered" style="display:none">
						<tr>
							<td align="right" >Select report format</td>
							<td nowrap="nowrap">
								<select class="segInput" id="monthly_bills_rendered_type" name="param" paramName="format" segOption="monthly_bills_rendered">
									<option value="CSV">CSV</option>
									<!-- <option value="Excel5" disabled="disabled">Excel</option> -->
								</select>
							</td>
						<tr>
						<tr>
							<td align="right" >Delete format</td>
							<td nowrap="nowrap">
								<select class="segInput" id="monthly_bills_rendered_Dtype" name="param" paramName="formatD" segOption="monthly_bills_rendered">
									<option value="SA">Show All</option>
									<option value="DB">Deleted Bill</option>
									<option value="FB">Final Bill</option>
									<!-- <option value="Excel5" disabled="disabled">Excel</option> -->
								</select>
							</td>
						</tr>
							<td align="right" >Select month/year</td>
							<td nowrap="nowrap">
								<select class="segInput" id="monthly_bills_rendered_month" name="param" paramName="month" segOption="monthly_bills_rendered">
<?= $month_options ?>
								</select>
								<select class="segInput" id="monthly_bills_rendered_year" name="param" paramName="year" segOption="monthly_bills_rendered">
<?= $year_options ?>
								</select>
							</td>
						</tr>
					</table>


					<!-- added by nick 2/1/2014 -->
					<!-- TRANSMITTAL -->
					<table name="selOptions" width="100%" border="0" cellpadding="2" cellspacing="0" segOption="transmittal_history" style="display:none">
						<tr>
							<td align="right" >Select month/year</td>
							<td nowrap="nowrap">
								<select class="segInput" id="transmittal_history_month" name="param" paramName="month" segOption="transmittal_history">
									<?= $month_options ?>
								</select>
								<select class="segInput" id="transmittal_history_year" name="param" paramName="year" segOption="transmittal_history">
									<?= $year_options ?>
								</select>
							</td>
						</tr>
					</table>
					<!-- end nick -->

                    <table name="selOptions" width="100%" border="0" cellpadding="2" cellspacing="0" segOption="Billing_Transmittal_Based_On_PHIC_Category" style="display:none">
                        <tr>
                            <td align="right" >Select month/year</td>
                            <td nowrap="nowrap">
                                <select class="segInput" id="Billing_Transmittal_Based_On_PHIC_Category_Month_From" name="param" paramName="month_from" segOption="Billing_Transmittal_Based_On_PHIC_Category">
                                    <?= $month_options ?>
                                </select>
                                <select class="segInput" id="Billing_Transmittal_Based_On_PHIC_Category_Month_To" name="param" paramName="month_to" segOption="Billing_Transmittal_Based_On_PHIC_Category">
                                    <?= $month_options ?>
                                </select>
                                <select class="segInput" id="Billing_Transmittal_Based_On_PHIC_Category_Year" name="param" paramName="year" segOption="Billing_Transmittal_Based_On_PHIC_Category">
                                    <?= $year_options ?>
                                </select>
                            </td>
                        </tr>
                    </table>

					<!--For Claims Reports PAID (Added by Genz)-->
					<table name="selOptions" width="100%" border="0" cellpadding="2" cellspacing="0" segOption="claim_paid" style="display:none">
						<tr>
							<td align="right" >Insurances</td>
							<td nowrap="nowrap">
								<select class="segInput" id="claim_paid_type" name="param" paramName="formatD" segOption="claim_paid_type">
									<?php echo $option_insurance; ?>
								</select>
							</td>
						</tr>

						<tr>
							<td align="right" >From: </td>
							<td nowrap="nowrap">
								<input class="segInput" name="param" id="claim_paid_from" type="text" size="12" value="" paramName="date" segOption="claim_paid_from"/>
								<img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_claim_paid_from" align="absmiddle" style="cursor:pointer;"  />
								<script type="text/javascript">
									Calendar.setup ({
										inputField : "claim_paid_from", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_claim_paid_from", singleClick : true, step : 1
									});
								</script>
							</td>
							<td align="right" >To: </td>
							<td nowrap="nowrap">
								<input class="segInput" name="param" id="claim_paid_to" type="text" size="12" value="" paramName="date" segOption="claim_paid_to"/>
								<img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_claim_paid_to" align="absmiddle" style="cursor:pointer;"  />
								<script type="text/javascript">
									Calendar.setup ({
										inputField : "claim_paid_to", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_claim_paid_to", singleClick : true, step : 1
									});
								</script>
							</td>
						</tr>
					</table>
					<!-- Claims Denied -->
					<table name="selOptions" width="100%" border="0" cellpadding="2" cellspacing="0" segOption="claim_denied" style="display:none">
						<tr>
							<td align="right" >Insurances</td>
							<td nowrap="nowrap">
								<select class="segInput" id="claim_denied_type" name="param" paramName="formatD" segOption="claim_denied_type">
									<?php echo $option_insurance; ?>
								</select>
							</td>
						</tr>

						<tr>
							<td align="right" >From: </td>
							<td nowrap="nowrap">
								<input class="segInput" name="param" id="claim_denied_from" type="text" size="12" value="" paramName="date" segOption="claim_denied_from"/>
								<img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_claim_denied_from" align="absmiddle" style="cursor:pointer;"  />
								<script type="text/javascript">
									Calendar.setup ({
										inputField : "claim_denied_from", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_claim_denied_from", singleClick : true, step : 1
									});
								</script>
							</td>
							<td align="right" >To: </td>
							<td nowrap="nowrap">
								<input class="segInput" name="param" id="claim_denied_to" type="text" size="12" value="" paramName="date" segOption="claim_denied_to"/>
								<img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_claim_denied_to" align="absmiddle" style="cursor:pointer;"  />
								<script type="text/javascript">
									Calendar.setup ({
										inputField : "claim_denied_to", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_claim_denied_to", singleClick : true, step : 1
									});
								</script>
							</td>
						</tr>
					</table>
					<!-- Claims Returned -->
					<table name="selOptions" width="100%" border="0" cellpadding="2" cellspacing="0" segOption="claim_returned" style="display:none">
						<tr>
							<td align="right" >Insurances</td>
							<td nowrap="nowrap">
								<select class="segInput" id="claim_returned_type" name="param" paramName="formatD" segOption="claim_returned_type">
									<?php echo $option_insurance; ?>
								</select>
							</td>
						</tr>

						<tr>
							<td align="right" >From: </td>
							<td nowrap="nowrap">
								<input class="segInput" name="param" id="claim_returned_from" type="text" size="12" value="" paramName="date" segOption="claim_returned_from"/>
								<img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_claim_returned_from" align="absmiddle" style="cursor:pointer;"  />
								<script type="text/javascript">
									Calendar.setup ({
										inputField : "claim_returned_from", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_claim_returned_from", singleClick : true, step : 1
									});
								</script>
							</td>
							<td align="right" >To: </td>
							<td nowrap="nowrap">
								<input class="segInput" name="param" id="claim_returned_to" type="text" size="12" value="" paramName="date" segOption="claim_returned_to"/>
								<img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_claim_returned_to" align="absmiddle" style="cursor:pointer;"  />
								<script type="text/javascript">
									Calendar.setup ({
										inputField : "claim_returned_to", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_claim_returned_to", singleClick : true, step : 1
									});
								</script>
							</td>
						</tr>
					</table>
					<!--For Claims Reports PAID DR-->
					<table name="selOptions" width="100%" border="0" cellpadding="2" cellspacing="0" segOption="claim_paid_dr" style="display:none">
						<tr>
							<td align="right" >Insurances</td>
							<td nowrap="nowrap">
								<select class="segInput" id="claim_paid_dr_type" name="param" paramName="formatD" segOption="claim_paid_dr_type">
									<?php echo $option_insurance; ?>
								</select>
							</td>
						</tr>
						<tr>
							<td align="right" >Doctor</td>
							<td nowrap="nowrap">
								<select class="segInput" id="claim_paid_dr" name="param" paramName="formatD" segOption="claim_paid_dr">
									<?php echo $option_dr; ?>
								</select>
							</td>
						</tr>
						<tr>
							<td align="right" >Patients</td>
							<td nowrap="nowrap">
								<select class="segInput" id="is_senior_claim" name="param" paramName="formatD" segOption="claim_paid_dr">
									<option value="all">All</option>
									<option value="senior">Senior</option>
									<option value="non-senior">Non-senior</option>
								</select>
							</td>
						</tr>
						<tr>
							<td align="right" >From: </td>
							<td nowrap="nowrap">
								<input class="segInput" name="param" id="claim_paid_dr_from" type="text" size="12" value="" paramName="date" segOption="claim_paid_dr_from"/>
								<img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_claim_paid_dr_from" align="absmiddle" style="cursor:pointer;"  />
								<script type="text/javascript">
									Calendar.setup ({
										inputField : "claim_paid_dr_from", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_claim_paid_dr_from", singleClick : true, step : 1
									});
								</script>
							</td>
							<td align="right" >To: </td>
							<td nowrap="nowrap">
								<input class="segInput" name="param" id="claim_paid_dr_to" type="text" size="12" value="" paramName="date" segOption="claim_paid_dr_to"/>
								<img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_claim_paid_dr_to" align="absmiddle" style="cursor:pointer;"  />
								<script type="text/javascript">
									Calendar.setup ({
										inputField : "claim_paid_dr_to", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_claim_paid_dr_to", singleClick : true, step : 1
									});
								</script>
							</td>
						</tr>
					</table>
					<!-- Promissory Note Maimai-->
					<table name="selOptions" width="100%" border="0" cellpadding="2" cellspacing="0" segOption="promissory_note" style="display:none">
						<tr>
							<td align="right">Select Date</td>
							<td nowrap="nowrap">
								<input class="segInput" name="param" id="promi_from" type="text" size="12" value="" paramName="date" segOption="promissory_note"/>
								<img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_promi_from" align="absmiddle" style="cursor:pointer;"  />
								<script type="text/javascript">
									Calendar.setup ({
										inputField : "promi_from", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_promi_from", singleClick : true, step : 1
									});
								</script>

								to

								<input class="segInput" name="param" id="promi_to" type="text" size="12" value="" paramName="date" segOption="promissory_note"/>
								<img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_promi_to" align="absmiddle" style="cursor:pointer;"  />
								<script type="text/javascript">
									Calendar.setup ({
										inputField : "promi_to", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_promi_to", singleClick : true, step : 1
									});
								</script>
							</td>
						</tr>
					</table>
					<!--  -->
					<!-- PF Report-->
					<table name="selOptions" width="100%" border="0" cellpadding="2" cellspacing="0" segOption="pf_report" style="display:none">
						<tr>
							<td align="right" >Doctor</td>
							<td nowrap="nowrap">
								<select class="segInput" id="pf_dr_nr" name="param" paramName="formatD" segOption="pf_report">
									<?php echo $option_dr; ?>
								</select>
							</td>
						</tr>
						<tr>
							<td align="right" >Patients</td>
							<td nowrap="nowrap">
								<select class="segInput" id="is_senior_pf" name="param" paramName="formatD" segOption="pf_report">
									<option value="all">All</option>
									<option value="senior">Senior</option>
									<option value="non-senior">Non-senior</option>
								</select>
							</td>
						</tr>
						<tr>
							<td align="right">Date of Payment</td>
							<td nowrap="nowrap">
								<input class="segInput" name="param" id="pf_from" type="text" size="12" value="" paramName="date" segOption="pf_report"/>
								<img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_pf_from" align="absmiddle" style="cursor:pointer;"  />
								<script type="text/javascript">
									Calendar.setup ({
										inputField : "pf_from", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_pf_from", singleClick : true, step : 1
									});
								</script>

								to

								<input class="segInput" name="param" id="pf_to" type="text" size="12" value="" paramName="date" segOption="pf_report"/>
								<img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_pf_to" align="absmiddle" style="cursor:pointer;"  />
								<script type="text/javascript">
									Calendar.setup ({
										inputField : "pf_to", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_pf_to", singleClick : true, step : 1
									});
								</script>
							</td>
						</tr>
					</table>
					<!--  -->
					<!-- PF EXCESS Report-->
					<table name="selOptions" width="100%" border="0" cellpadding="2" cellspacing="0" segOption="pf_excess_report" style="display:none">
						<tr>
							<td align="right" >Doctor</td>
							<td nowrap="nowrap">
								<select class="segInput" id="pf_dr_nr_ex" name="param" paramName="formatD" segOption="pf_excess_report">
									<option value="0">ALL</option>
									<?php echo $option_dr; ?>
								</select>
							</td>
						</tr>
						<tr>
							<td align="right" >Patients</td>
							<td nowrap="nowrap">
								<select class="segInput" id="is_senior_pf_ex" name="param" paramName="formatD" segOption="pf_excess_report">
									<option value="all">All</option>
									<option value="senior">Senior</option>
									<option value="non-senior">Non-senior</option>
								</select>
							</td>
						</tr>
						<tr>
							<td align="right">Date</td>
							<td nowrap="nowrap">
								<input class="segInput" name="param" id="pf_from_ex" type="text" size="12" value="" paramName="date" segOption="pf_excess_report"/>
								<img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_pf_from_ex" align="absmiddle" style="cursor:pointer;"  />
								<script type="text/javascript">
									Calendar.setup ({
										inputField : "pf_from_ex", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_pf_from_ex", singleClick : true, step : 1
									});
								</script>

								to

								<input class="segInput" name="param" id="pf_to_ex" type="text" size="12" value="" paramName="date" segOption="pf_excess_report"/>
								<img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_pf_to_ex" align="absmiddle" style="cursor:pointer;"  />
								<script type="text/javascript">
									Calendar.setup ({
										inputField : "pf_to_ex", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_pf_to_ex", singleClick : true, step : 1
									});
								</script>
							</td>
						</tr>
					</table>
					<!--  -->
					<!-- PF Monthly EXCESS Report  -->
						<table name="selOptions" width="100%" border="0" cellpadding="2" cellspacing="0" segOption="pf_monthly_excess_report" style="display:none">
						<tr>
							<td align="right" >Doctor</td>
							<td nowrap="nowrap">
								<select class="segInput" id="pfs_dr_nr_ex" name="param" paramName="formatD" segOption="pf_monthly_excess_report">
									<option value="0">ALL</option>
									<?php echo $option_dr; ?>
								</select>
							</td>
						</tr>
						<tr>
							<td align="right" >Patients</td>
							<td nowrap="nowrap">
								<select class="segInput" id="is_senior_pfs_ex" name="param" paramName="formatD" segOption="pf_monthly_excess_report">
									<option value="all">All</option>
									<option value="senior">Senior</option>
									<option value="non-senior">Non-senior</option>
								</select>
							</td>
						</tr>
						<tr>
							<td align="right">Date</td>
							<td nowrap="nowrap">
								<input class="segInput" name="param" id="pfs_from_ex" type="text" size="12" value="" paramName="date" segOption="pf_monthly_excess_report"/>
								<img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_pfs_from_ex" align="absmiddle" style="cursor:pointer;"  />
								<script type="text/javascript">
									Calendar.setup ({
										inputField : "pfs_from_ex", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_pfs_from_ex", singleClick : true, step : 1
									});
								</script>

								to

								<input class="segInput" name="param" id="pfs_to_ex" type="text" size="12" value="" paramName="date" segOption="pf_monthly_excess_report"/>
								<img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_pfs_to_ex" align="absmiddle" style="cursor:pointer;"  />
								<script type="text/javascript">
									Calendar.setup ({
										inputField : "pfs_to_ex", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_pfs_to_ex", singleClick : true, step : 1
									});
								</script>
							</td>
						</tr>
					</table>
					<!-- end -->
					<!--For Claims Reports UNPAID (Added by Genz)-->
					<table name="selOptions" width="100%" border="0" cellpadding="2" cellspacing="0" segOption="claim_unpaid" style="display:none">
						<tr>
							<td align="right" >Insurances</td>
							<td nowrap="nowrap">
								<select class="segInput" id="claim_unpaid_type" name="param" paramName="formatD" segOption="claim_upaid_type">
								<!-- 	<option value="ALL">ALL</option>
									<option value="PHIC">PHIC</option>
									<option value="HMO">HMO</option> -->
									<?php echo $option_insurance; ?>
								</select>
							</td>
						</tr>

						<tr>
							<td align="right" >From: </td>
							<td nowrap="nowrap">
								<input class="segInput" name="param" id="claim_unpaid_from" type="text" size="12" value="" paramName="date" segOption="claim_unpaid_from"/>
								<img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_claim_unpaid_from" align="absmiddle" style="cursor:pointer;"  />
								<script type="text/javascript">
									Calendar.setup ({
										inputField : "claim_unpaid_from", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_claim_unpaid_from", singleClick : true, step : 1
									});
								</script>
							</td>
							<td align="right" >To: </td>
							<td nowrap="nowrap">
								<input class="segInput" name="param" id="claim_unpaid_to" type="text" size="12" value="" paramName="date" segOption="claim_unpaid_to"/>
								<img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_claim_unpaid_to" align="absmiddle" style="cursor:pointer;"  />
								<script type="text/javascript">
									Calendar.setup ({
										inputField : "claim_unpaid_to", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_claim_unpaid_to", singleClick : true, step : 1
									});
								</script>
							</td>
						</tr>
					</table>

					<table width="100%" border="0" cellpadding="2" cellspacing="0" style="font:normal 12px Tahoma;margin-top:5px">
						<tr>
							<td width="30%"></td>
							<td>
								<button class="segButton" onclick="openReport(); return false;" style="<?=$btnPrint?>"><img src="<?= $root_path ?>gui/img/common/default/report.png" /> View Report </button>
								<button class="segButton" onclick="openJasperReport(); return false;" style="<?=$btnJasperPrint?>"><img src="<?= $root_path ?>gui/img/common/default/report.png" /> View Report  </button>
							</td>
						</tr>
					</table>
					<br />
				</td>
			</tr>
		</tbody>
	</table>
</div>

<?php

# Workaround to force display of results  form
$bShowThisForm = TRUE;

# If smarty object is not available create one
if(!isset($smarty)){
	/**
	 * LOAD Smarty
	 * param 2 = FALSE = dont initialize
	 * param 3 = FALSE = show no copyright
	 * param 4 = FALSE = load no javascript code
	 */
	include_once($root_path.'gui/smarty_template/smarty_care.class.php');
	$smarty = new smarty_care('common',FALSE,FALSE,FALSE);
	# Set a flag to display this page as standalone
	$bShowThisForm=TRUE;
}

?>

<input type="hidden" name="lang" value="<?php echo $lang ?>" />
<input type="hidden" name="userck" value="<?php echo $userck ?>" />
<input type="hidden" name="userck" value="<?php echo $userck?>">
<input type="hidden" name="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>" />
<input type="hidden" name="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>" />
<input type="hidden" id="delete" name="delete" value="" />
<input type="hidden" id="page" name="page" value="<?= $current_page ?>" />
<input type="hidden" id="lastpage" name="lastpage"  value="<?= $last_page ?>" />
<input type="hidden" id="jump" name="jump" />


</form>
<?php

$sTemp = ob_get_contents();
ob_end_clean();

# Assign the form template to mainframe
$smarty->assign('sMainFrameBlockData',$sTemp);

/**
 * show Template
 */
$smarty->display('common/mainframe.tpl');
?>
