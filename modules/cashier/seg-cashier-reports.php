<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
define('NO_2LEVEL_CHK',1);
require_once($root_path.'include/inc_front_chain_lang.php');
include_once($root_path."include/care_api_classes/class_cashier_service.php");
$pclass = new SegCashierService($target);

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

$title='Cashier';
if (!$_GET['from'])
	$breakfile=$root_path."modules/cashier/seg-cashier-functions.php".URL_APPEND."&userck=$userck";
else {
	if ($_GET['from']=='CLOSE_WINDOW')
		$breakfile = "javascript:window.parent.cClick();";
	else
		$breakfile = $root_path.'modules/cashier/cashier-pass.php'.URL_APPEND."&userck=$userck&target=".$_GET['from'];
}

$imgpath=$root_path."pharma/img/";
$thisfile='seg-cashier-reports.php';

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"Cashier::Reports");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('products_db.php','search','$from','$cat')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"Cashier Reports");

 # Assign Body Onload javascript code
 $smarty->assign('sOnLoadJs','onLoad=""');

 # Collect javascript code
 ob_start()

?>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery-1.8.2.js"></script>
<script type='text/javascript' src="<?=$root_path?>js/jquery/ui/jquery-ui-1.9.1.js"></script>
<link rel="stylesheet" href="<?= $root_path ?>js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css" /> 
<script type="text/javascript">var $J = jQuery.noConflict();</script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript">
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
	var options_type_daily = document.getElementById("dailycollectionperacct_type");
	var options_type_monthly = document.getElementById("monthlycollectionperacct_type");
	var reportType_daily = options_type_daily.options[options_type_daily.selectedIndex].value;
	var reportType_monthly = options_type_monthly.options[options_type_monthly.selectedIndex].value;
	for (var i=0; i<params.length; i++) {
		if (params[i].getAttribute("segOption") == rep) {
			var mit;
			if (params[i].type=='checkbox') mit=params[i].checked;
			else if (params[i].type=='radio') mit=params[i].checked;
			else mit=params[i].value;
			if (mit) query.push(params[i].getAttribute('paramName')+'='+params[i].value)
		}
	}

	switch(rep){
		case "dailycollectionperacct":
			if(reportType_daily == "mdc"){
				$J('#print_params').dialog('open');
			}else{
				query.push("isPDF"+'='+"0");
				cashierReport(url, query, rep);
			}
			break;
		case "dailycollectionfull":
			if(reportType_daily == "mdc"){
				$J('#print_params').dialog('open');
			}else{
				query.push("isPDF"+'='+"0");
				cashierReport(url, query, rep);
			}
			break;
		case "monthlycollectionperacct":
			if(reportType_monthly == "mdc"){
				$J('#print_params').dialog('open');
			}else{
				query.push("isPDF"+'='+"0");
				cashierReport(url, query, rep);
			}
			break;
	}

		
	
}



function cashierReport(url, query, rep, isPDF=0){
	window.open(url+query.join('&'),rep,"width=800,height=600,menubar=no,resizable=yes,scrollbars=no");
}

$J(function(){
	$J('#print_params').dialog({
		autoOpen:false,
		modal:true,
		width:400,
		title:"Additional Parameters",
		open:function(x,y){

		},
		buttons:{
			Print:function(){

			var rep = $('selreport').options[$('selreport').selectedIndex].value
			var url = 'reports/'+rep+'.php?'
			var query = new Array()
			var params = document.getElementsByName('param')

			for (var i=0; i<params.length; i++) {
				if (params[i].getAttribute("segOption") == rep) {
					var mit;
					if (params[i].type=='checkbox') mit=params[i].checked;
					else if (params[i].type=='radio') mit=params[i].checked;
					else mit=params[i].value;
					if (mit) query.push(params[i].getAttribute('paramName')+'='+params[i].value)
				}
			}

			
			var print_format = document.getElementById("print_format");
			var isPDF = print_format.options[print_format.selectedIndex].value;
			// alert(isPDF);
			if(isPDF == "PDF"){
				query.push("isPDF"+'='+"0");
			}else{
				query.push("isPDF"+'='+"1");
			}
			
			cashierReport(url, query, rep);

			},
			Cancel:function(){
				$J(this).dialog('close');
			}
		}
	});//print params
});

$J(function($) {
	$('.select-encoder').find('optgroup.inactive-encoders').hide();
	$('.select-encoder').each(function(i, item) {
		var checkBox = $('<input/>', {
			'type': 'checkbox'
		}).change(function() {
			if (this.checked) {
				$(this).parent().next('select').find('optgroup.inactive-encoders').show();
			} else {
				$(this).parent().next('select').find('optgroup.inactive-encoders').hide();
			}
		});
		var label = $('<label/>', { 'class': 'segInput' }).text('Show inactive encoders').click(function(){ $(this).prev('input[type=checkbox]').click()});
		$('<div/>').css({margin: '2px'}).append(checkBox).append(label).insertBefore($(this));
	});
});
</script>

<?php

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

$types = array();
//$result = $pclass->getAccountTypes(FALSE,NULL);
//if ($result) {
//	while ($row=$result->FetchRow()) $types[] = $row;
//}

$rs = $db->Execute("SELECT id,formal_name `name` FROM seg_pay_accounts ORDER BY `name`");
$types = $rs->GetRows();

$account_type_options = "";
foreach ($types as $type) {
	$account_type_options .= "<option value=\"".$type["id"]."\" $checked>".$type['name']."</option>\n";
}

$sql = "SELECT fn_get_person_name(`person`.pid) `name`,u.login_id,u.personell_nr,a.location_nr,\n".
	"(a.status='deleted' OR (p.date_exit IS NOT NULL AND p.date_exit != '0000-00-00' AND p.date_exit <= DATE(NOW()))) `terminated`\n".
	"FROM care_users AS u\n".
		"LEFT JOIN care_personell AS p ON u.personell_nr=p.nr\n".
		"LEFT JOIN care_person `person` ON `person`.pid=`p`.pid\n".
		"LEFT JOIN care_personell_assignment AS a ON a.personell_nr=p.nr\n".
	"WHERE location_nr=170\n".
	"ORDER BY `name`";
$rs = $db->Execute($sql);
$list = $rs->GetRows();
//$cashier_user_options
$active = array();
$inactive = array();
if ($_SESSION['sess_temp_userid'] == 'admin') {
	$active['admin'] = 'Administrator';
}
foreach ($list as $row) {
	if ($row['terminated'] == 1) {
		$inactive[$row['login_id']] = mb_strtoupper($row['name']);
	} else {
		$active[$row['login_id']] = mb_strtoupper($row['name']);
	}
}

$cashier_user_options = array();
$cashier_user_options = '<option value="">--All encoders--</option>';
$cashier_user_options .= '<optgroup class="active-encoders" label="Active encoders">';
foreach ($active as $id => $name) {
	$cashier_user_options .= sprintf('<option value="%s">%s</option>', htmlentities($id), htmlentities($name));
}
$cashier_user_options .= '</optgroup>';
$cashier_user_options .= '<optgroup class="inactive-encoders" label="Inactive encoders">';
foreach ($inactive as $id => $name) {
	$cashier_user_options .= sprintf('<option value="%s">%s</option>', htmlentities($id), htmlentities($name));
}
$cashier_user_options .= '</optgroup>';

$month_options = '';
for ($i=1;$i<=12;$i++)
	$month_options .= "									<option value=\"$i\">".date("F", strtotime("$i/1/2000"))."</option>\n";

$year_options = '';
for ($i=1980;$i<((int)date("Y")+50);$i++)
	$year_options .= "									<option value=\"$i\" ".($i==date("Y") ? 'selected="selected"' : "").">$i</option>";

$current_date = date($php_date_format);

ob_start();
?>



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
	</table>
</div>

<br>
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
										<option value="dailycollectionperacct">Daily Collection (Per account)</option>
										<option value="daily_collection_summary">Daily Collection Summary (Per account)</option>
										<option value="dailycollectionfull">Daily Collection Full (Per shift)</option>
										<option value="dailyendorsement">Daily Endorsement (Per shift)</option>
										<option value="dailyorusage">Daily OR Usage</option>
										<option value="acc_or_reports">Accommodation and OR Income Report</option>
									</optgroup>
									<optgroup label="Monthly reports">
										<option value="monthlycollectionperacct">Monthly Collection (Per account)</option>
										<option value="monthlycollectionfull">Monthly Collection Full (Per account)</option>
										<option value="opd_income">OPD Income Report</option>
									</optgroup>
								</select>
							</td>
						</tr>
					</table>

					<hr width="90%" size="1" style="color:rgba(0,0,0,0.2)" />

					<!-- DAILY COLLECTION (PER ACCOUNT) REPORT -->
					<table name="selOptions" width="100%" border="0" cellpadding="2" cellspacing="0" segOption="dailycollectionperacct">
						<tr>
							<td width="30%" align="right">Select account type</td>
							<td>
								<select class="segInput" id="dailycollectionperacct_type" name="param" paramName="type" segOption="dailycollectionperacct">
								<?= $account_type_options ?>
								</select>
							</td>
						</tr>
						<tr>
							<td width="30%" align="right">Select encoder</td>
							<td>
								<select class="segInput select-encoder" name="param" id="dailycollectionperacct_encoder" paramName="encoder" segOption="dailycollectionperacct">
								<?= $cashier_user_options ?>
								</select>
							</td>
						</tr>
						<tr>
							
							<td align="right" >Select date</td>
							<td>
								
								<input class="segInput" name="param" id="dailycollectionperacct_date" type="text" size="12" value="<?= $current_date ?>" paramName="date" segOption="dailycollectionperacct"/>
								<img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_dailycollectionperacct_date" align="absmiddle" style="cursor:pointer;"  />
								<script type="text/javascript">
									Calendar.setup ({
										inputField : "dailycollectionperacct_date", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_dailycollectionperacct_date", singleClick : true, step : 1
									});
								</script>
							</td>
							
						</tr>
						<tr>
							<td align="right">Shift starts/ends</td>
							<td>
									<select class="segInput" name="param" id="dailycollectionperacct_shiftstart" paramName="shiftstart" segOption="dailycollectionperacct">
									<?php
										$am=''; $pm='';
										for ($i=0;$i<12;$i++) {
											$am.= "<option value=\"$i\">".($i==0 ? '12:00mn' : $i.":00am")."</option>\n";
											$pm.= "<option value=\"".($i+12)."\">".($i==0 ? '12:00nn' : $i.":00pm")."</option>\n";
										}
										echo $am.$pm;
									?>
									</select>
										to
									<select class="segInput" name="param" id="dailycollectionperacct_shiftend" paramName="shiftend" segOption="dailycollectionperacct">
									<?php
										$am=''; $pm='';
										for ($i=0;$i<12;$i++) {
											$am.= "<option value=\"$i\">".($i==0 ? '12:00mn' : $i.":00am")."</option>\n";
											$pm.= "<option value=\"".($i+12)."\">".($i==0 ? '12:00nn' : $i.":00pm")."</option>\n";
										}
										echo $am.$pm;
									?>
									</select>
							</td>
						</tr>
						<tr>
							<td align="right">OR# from</td>
							<td nowrap="nowrap">
								<input id="dailycollectionperacct_orfrom" name="param" class="segInput" type="text" size="8" value="" paramName="orfrom" segOption="dailycollectionperacct" /> to
								<input id="dailycollectionperacct_orto" name="param" class="segInput" type="text" size="8" value="" paramName="orto" segOption="dailycollectionperacct" />
							</td>
						</tr>
					</table>

					<!-- DAILY COLLECTION SUMMARY REPORT -->
					<table name="selOptions" width="100%" border="0" cellpadding="2" cellspacing="0" segOption="daily_collection_summary" style="display:none">
						<tr>
							<td width="30%" align="right">Select account type</td>
							<td>
								<select class="segInput" id="daily_collection_summary_type" name="param" paramName="account" segOption="daily_collection_summary">
								<?= $account_type_options ?>
								</select>
							</td>
						</tr>
						<tr>
							<td width="30%" align="right">Select encoder</td>
							<td>
								<select class="segInput select-encoder" name="param" id="daily_collection_summary_encoder" paramName="encoder" segOption="daily_collection_summary">
								<?= $cashier_user_options ?>
								</select>
							</td>
						</tr>
						<tr>
							<td align="right" >Select date</td>
							<td>
								<input class="segInput" name="param" id="daily_collection_summary_date" type="text" size="12" value="<?= $current_date ?>" paramName="date" segOption="daily_collection_summary"/>
								<img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_daily_collection_summary_date" align="absmiddle" style="cursor:pointer;"  />
								<script type="text/javascript">
									Calendar.setup ({
										inputField : "daily_collection_summary_date", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_daily_collection_summary_date", singleClick : true, step : 1
									});
								</script>
							</td>
						</tr>
						<tr>
							<td align="right">OR# from</td>
							<td nowrap="nowrap">
								<input id="daily_collection_summary_orfrom" name="param" class="segInput" type="text" size="8" value="" paramName="orfrom" segOption="daily_collection_summary" /> to
								<input id="daily_collection_summary_orto" name="param" class="segInput" type="text" size="8" value="" paramName="orto" segOption="daily_collection_summary" />
							</td>
						</tr>
					</table>

					<!-- DAILY OR USAGE REPORT -->
					<table name="selOptions" width="100%" border="0" cellpadding="2" cellspacing="0" segOption="dailyorusage" style="display:none">
						<tr>
							<td width="30%" align="right">Select account type</td>
							<td>
								<select class="segInput" id="dailyorusage_type" name="param" paramName="type" segOption="dailyorusage">
									<option value="">-- All accounts --</option>
									<?= $account_type_options ?>
								</select>
							</td>
						</tr>
						<tr>
							<td width="30%" align="right">Select encoder</td>
							<td>
								<select class="segInput select-encoder" name="param" id="dailyorusage_encoder" paramName="encoder" segOption="dailyorusage">
									<?= $cashier_user_options ?>
								</select>
							</td>
						</tr>
<!--						<tr>
							<td align="right">Shift starts</td>
							<td nowrap="nowrap">
									<input class="segInput" name="param" id="dailyorusage_datestart" type="text" size="12" value="<?php $current_date ?>" paramName="datestart" segOption="dailyorusage"/>
									<img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_dailyorusage_datestart" align="absmiddle" style="cursor:pointer;"  />
									<script type="text/javascript">
										Calendar.setup ({
											inputField : "dailyorusage_datestart", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_dailyorusage_datestart", singleClick : true, step : 1
										});
									</script>

									<select class="segInput" name="param" id="dailyorusage_timestart" paramName="timestart" segOption="dailyorusage">
										<option value="">Unspecified</option>
<?php
	$am=''; $pm='';
	for ($i=0;$i<12;$i++) {
		$am.= "                    <option value=\"".str_pad($i,2,'0',STR_PAD_LEFT)."0000\">".($i==0 ? '12:00mn' : $i.":00am")."</option>\n";
		$pm.= "                    <option value=\"".($i+12)."0000\">".($i==0 ? '12:00nn' : $i.":00pm")."</option>\n";
	}
	echo $am.$pm;
?>
									</select>
							</td>
						</tr>
						<tr>
							<td align="right">Shift ends</td>
							<td>
									<input class="segInput" name="param" id="dailyorusage_dateend" type="text" size="12" value="<?php $current_date ?>" paramName="dateend" segOption="dailyorusage"/>
									<img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_dailyorusage_dateend" align="absmiddle" style="cursor:pointer;"  />
									<script type="text/javascript">
										Calendar.setup ({
											inputField : "dailyorusage_dateend", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_dailyorusage_dateend", singleClick : true, step : 1
										});
									</script>

									<select class="segInput" name="param" id="dailyorusage_timeend" paramName="timeend" segOption="dailyorusage">
										<option value="">Unspecified</option>
<?php
	$am=''; $pm='';
	for ($i=0;$i<12;$i++) {
		$am.= "                    <option value=\"".str_pad($i,2,'0',STR_PAD_LEFT)."0000\">".($i==0 ? '12:00mn' : $i.":00am")."</option>\n";
		$pm.= "                    <option value=\"".($i+12)."000\">".($i==0 ? '12:00nn' : $i.":00pm")."</option>\n";
	}
	echo $am.$pm;
?>
									</select>
							</td>
						</tr>-->
						<tr>
							<td align="right">OR# from</td>
							<td nowrap="nowrap">
								<input id="dailyorusage_orfrom" name="param" class="segInput" type="text" size="8" value="" paramName="orfrom" segOption="dailyorusage" /> to
								<input id="dailyorusage_orto" name="param" class="segInput" type="text" size="8" value="" paramName="orto" segOption="dailyorusage" />
							</td>
						</tr>
					</table>


					<!-- DAILY COLLECTION FULL -->
					<table name="selOptions" width="100%" border="0" cellpadding="2" cellspacing="0" segOption="dailycollectionfull" style="display:none">
						<tr>
							<td width="30%" align="right">Select account type</td>
							<td>
								<select class="segInput" id="dailycollectionfull_type" name="param" paramName="type" segOption="dailycollectionfull">
									<?= $account_type_options ?>
								</select>
							</td>
						</tr>
						<tr>
							<td width="30%" align="right">Select encoder</td>
							<td>
								<select class="segInput select-encoder" name="param" id="dailycollectionfull_encoder" paramName="encoder" segOption="dailycollectionfull">
									<?= $cashier_user_options ?>
								</select>
							</td>
						</tr>
						<tr>
							<td align="right">Shift starts</td>
							<td nowrap="nowrap">
									<input class="segInput" name="param" id="dailycollectionfull_datestart" type="text" size="12" value="<?= $current_date ?>" paramName="datestart" segOption="dailycollectionfull"/>
									<img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_dailycollectionfull_datestart" align="absmiddle" style="cursor:pointer;"  />
									<script type="text/javascript">
										Calendar.setup ({
											inputField : "dailycollectionfull_datestart", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_dailycollectionfull_datestart", singleClick : true, step : 1
										});
									</script>

									<select class="segInput" name="param" id="dailycollectionfull_timestart" paramName="timestart" segOption="dailycollectionfull">
										<option value="">Unspecified</option>
<?php
	$am=''; $pm='';
	for ($i=0;$i<12;$i++) {
		$am.= "                    <option value=\"".str_pad($i,2,'0',STR_PAD_LEFT)."0000\">".($i==0 ? '12:00mn' : $i.":00am")."</option>\n";
		$pm.= "                    <option value=\"".($i+12)."0000\">".($i==0 ? '12:00nn' : $i.":00pm")."</option>\n";
	}
	echo $am.$pm;
?>
									</select>
							</td>
						</tr>
						<tr>
							<td align="right">Shift ends</td>
							<td>
									<input class="segInput" name="param" id="dailycollectionfull_dateend" type="text" size="12" value="<?= $current_date ?>" paramName="dateend" segOption="dailycollectionfull"/>
									<img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_dailycollectionfull_dateend" align="absmiddle" style="cursor:pointer;"  />
									<script type="text/javascript">
										Calendar.setup ({
											inputField : "dailycollectionfull_dateend", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_dailycollectionfull_dateend", singleClick : true, step : 1
										});
									</script>

									<select class="segInput" name="param" id="dailycollectionfull_timeend" paramName="timeend" segOption="dailycollectionfull">
										<option value="">Unspecified</option>
<?php
	$am=''; $pm='';
	for ($i=0;$i<12;$i++) {
		$am.= "                    <option value=\"".str_pad($i,2,'0',STR_PAD_LEFT)."0000\">".($i==0 ? '12:00mn' : $i.":00am")."</option>\n";
		$pm.= "                    <option value=\"".($i+12)."000\">".($i==0 ? '12:00nn' : $i.":00pm")."</option>\n";
	}
	echo $am.$pm;
?>
									</select>
							</td>
						</tr>
						<tr>
							<td align="right">OR# from</td>
							<td nowrap="nowrap">
								<input id="dailycollectionfull_orfrom" name="param" class="segInput" type="text" size="8" value="" paramName="orfrom" segOption="dailycollectionfull" /> to
								<input id="dailycollectionfull_orto" name="param" class="segInput" type="text" size="8" value="" paramName="orto" segOption="dailycollectionfull" />
							</td>
						</tr>
					</table>

					<!--  Daily endorsement -->
					<table name="selOptions" width="100%" border="0" cellpadding="2" cellspacing="0" segOption="dailyendorsement" style="font:bold 12px Arial; display:none">
						<tr>
							<td width="30%" align="right">Select endorser</td>
							<td>
								<select class="segInput" name="param" id="dailyendorsement_encoder" paramName="encoder" segOption="dailyendorsement">
									<?= $cashier_user_options ?>
								</select>
							</td>
						</tr>
						<tr>
							<td align="right">OR# from</td>
							<td nowrap="nowrap">
								<input id="dailyendorsement_orfrom" name="param" class="segInput" type="text" size="8" value="" paramName="orfrom" segOption="dailyendorsement" /> to
								<input id="dailyendorsement_orto" name="param" class="segInput" type="text" size="8" value="" paramName="orto" segOption="dailyendorsement" />
							</td>
						</tr>
					</table>


					<!-- MONTHLY PER ACCOUNT -->
					<table name="selOptions" width="100%" border="0" cellpadding="2" cellspacing="0" segOption="monthlycollectionperacct" style="font:bold 12px Arial; display:none">
						<tr>
							<td width="30%" align="right">Select account type</td>
							<td>
								<select class="segInput" id="monthlycollectionperacct_type" name="param" paramName="type" segOption="monthlycollectionperacct">
									<?= $account_type_options ?>
								</select>
							</td>
						</tr>
						<tr>
							<td width="30%" align="right">Select encoder</td>
							<td>
								<select class="segInput select-encoder" name="param" id="monthlycollectionperacct_encoder" paramName="encoder" segOption="monthlycollection">
									<?= $cashier_user_options ?>
								</select>
							</td>
						</tr>
						<tr>
							<td align="right" >Select month/year</td>
							<td nowrap="nowrap">
								<select class="segInput" id="monthlycollectionperacct_month" name="param" paramName="month" segOption="monthlycollectionperacct">
									<?= $month_options ?>
								</select>
								<select class="segInput" id="monthlycollectionperacct_year" name="param" paramName="year" segOption="monthlycollectionperacct">
									<?= $year_options ?>
								</select>
							</td>
						</tr>
					</table>

					<!-- MONTHLY COLLECTION FULL -->
					<table name="selOptions" width="100%" border="0" cellpadding="2" cellspacing="0" segOption="monthlycollectionfull" style="display:none">
						<tr>
							<td width="30%" align="right">Select account type</td>
							<td>
								<select class="segInput" id="dailycollectionfull_type" name="param" paramName="type" segOption="monthlycollectionfull">
									<?= $account_type_options ?>
								</select>
							</td>
						</tr>
						<tr>
							<td width="30%" align="right">Select encoder</td>
							<td>
								<select class="segInput select-encoder" name="param" id="dailycollectionfull_encoder" paramName="encoder" segOption="monthlycollectionfull">
									<?= $cashier_user_options ?>
								</select>
							</td>
						</tr>
						<tr>
							<td align="right">Shift starts</td>
							<td nowrap="nowrap">
									<input class="segInput" name="param" id="monthlycollectionfull_datestart" type="text" size="12" value="<?= $current_date ?>" paramName="datestart" segOption="monthlycollectionfull"/>
									<img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_monthlycollectionfull_datestart" align="absmiddle" style="cursor:pointer;"  />
									<script type="text/javascript">
										Calendar.setup ({
											inputField : "monthlycollectionfull_datestart", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_monthlycollectionfull_datestart", singleClick : true, step : 1
										});
									</script>

									<select class="segInput" name="param" id="monthlycollectionfull_timestart" paramName="timestart" segOption="monthlycollectionfull">
										<option value="">Unspecified</option>
<?php
	$am=''; $pm='';
	for ($i=0;$i<12;$i++) {
		$am.= "                    <option value=\"".str_pad($i,2,'0',STR_PAD_LEFT)."0000\">".($i==0 ? '12:00mn' : $i.":00am")."</option>\n";
		$pm.= "                    <option value=\"".($i+12)."0000\">".($i==0 ? '12:00nn' : $i.":00pm")."</option>\n";
	}
	echo $am.$pm;
?>
									</select>
							</td>
						</tr>
						<tr>
							<td align="right">Shift ends</td>
							<td>
									<input class="segInput" name="param" id="monthlycollectionfull_dateend" type="text" size="12" value="<?= $current_date ?>" paramName="dateend" segOption="monthlycollectionfull"/>
									<img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_monthlycollectionfull_dateend" align="absmiddle" style="cursor:pointer;"  />
									<script type="text/javascript">
										Calendar.setup ({
											inputField : "monthlycollectionfull_dateend", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_monthlycollectionfull_dateend", singleClick : true, step : 1
										});
									</script>

									<select class="segInput" name="param" id="monthlycollectionfull_timeend" paramName="timeend" segOption="monthlycollectionfull">
										<option value="">Unspecified</option>
<?php
	$am=''; $pm='';
	for ($i=0;$i<12;$i++) {
		$am.= "                    <option value=\"".str_pad($i,2,'0',STR_PAD_LEFT)."0000\">".($i==0 ? '12:00mn' : $i.":00am")."</option>\n";
		$pm.= "                    <option value=\"".($i+12)."000\">".($i==0 ? '12:00nn' : $i.":00pm")."</option>\n";
	}
	echo $am.$pm;
?>
									</select>
							</td>
						</tr>
						<tr>
							<td align="right">OR# from</td>
							<td nowrap="nowrap">
								<input id="monthlycollectionfull_orfrom" name="param" class="segInput" type="text" size="8" value="" paramName="orfrom" segOption="monthlycollectionfull" /> to
								<input id="monthlycollectionfull_orto" name="param" class="segInput" type="text" size="8" value="" paramName="orto" segOption="monthlycollectionfull" />
							</td>
						</tr>
					</table>

					<!-- OPD INCOME REPORT -->
					<table name="selOptions" width="100%" border="0" cellpadding="2" cellspacing="0" segOption="opd_income" style="display:none">
						<tr>
							<td width="30%" align="right">Select account type</td>
							<td>
								<select class="segInput" id="opd_income_type" name="param" paramName="type" segOption="opd_income">
									<?= $account_type_options ?>
								</select>
							</td>
						</tr>
						<tr>
							<td width="30%" align="right">Select encoder</td>
							<td>
								<select class="segInput select-encoder" name="param" id="opd_income_encoder" paramName="encoder" segOption="opd_income">
									<?= $cashier_user_options ?>
								</select>
							</td>
						</tr>
						<tr>
							<td align="right">Shift starts</td>
							<td nowrap="nowrap">
									<input class="segInput" name="param" id="opd_income_datestart" type="text" size="12" value="<?= $current_date ?>" paramName="datestart" segOption="opd_income"/>
									<img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_opd_income_datestart" align="absmiddle" style="cursor:pointer;"  />
									<script type="text/javascript">
										Calendar.setup ({
											inputField : "opd_income_datestart", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_opd_income_datestart", singleClick : true, step : 1
										});
									</script>

									<select class="segInput" name="param" id="opd_income_timestart" paramName="timestart" segOption="opd_income">
										<option value="">Unspecified</option>
<?php
	$am=''; $pm='';
	for ($i=0;$i<12;$i++) {
		$am.= "                    <option value=\"".str_pad($i,2,'0',STR_PAD_LEFT)."0000\">".($i==0 ? '12:00mn' : $i.":00am")."</option>\n";
		$pm.= "                    <option value=\"".($i+12)."0000\">".($i==0 ? '12:00nn' : $i.":00pm")."</option>\n";
	}
	echo $am.$pm;
?>
									</select>
							</td>
						</tr>
						<tr>
							<td align="right">Shift ends</td>
							<td>
									<input class="segInput" name="param" id="opd_income_dateend" type="text" size="12" value="<?= $current_date ?>" paramName="dateend" segOption="opd_income"/>
									<img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_opd_income_dateend" align="absmiddle" style="cursor:pointer;"  />
									<script type="text/javascript">
										Calendar.setup ({
											inputField : "opd_income_dateend", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_opd_income_dateend", singleClick : true, step : 1
										});
									</script>

									<select class="segInput" name="param" id="opd_income_timeend" paramName="timeend" segOption="opd_income">
										<option value="">Unspecified</option>
<?php
	$am=''; $pm='';
	for ($i=0;$i<12;$i++) {
		$am.= "                    <option value=\"".str_pad($i,2,'0',STR_PAD_LEFT)."0000\">".($i==0 ? '12:00mn' : $i.":00am")."</option>\n";
		$pm.= "                    <option value=\"".($i+12)."000\">".($i==0 ? '12:00nn' : $i.":00pm")."</option>\n";
	}
	echo $am.$pm;
?>
									</select>
							</td>
						</tr>
						<tr>
							<td align="right">OR# from</td>
							<td nowrap="nowrap">
								<input id="opd_income_orfrom" name="param" class="segInput" type="text" size="8" value="" paramName="orfrom" segOption="opd_income" /> to
								<input id="opd_income_orto" name="param" class="segInput" type="text" size="8" value="" paramName="orto" segOption="opd_income" />
							</td>
						</tr>
					</table>

						<!-- reports for accomodation and or income-->
						<!-- added by daryl -->
					<table name="selOptions" width="100%" border="0" cellpadding="2" cellspacing="0" segOption="acc_or_reports" style="display:none">
						<tr>
							<td width="30%" align="right">Select Report type</td>
							<td>
								<select class="segInput" id="acc_or_types" name="param" paramName="type" segOption="acc_or_reports">
										<option value="0">--SELECT--</option>
										<option value="acc">ACCOMODATION</option>
										<option value="or">O.R.</option>
								</select>
							</td>
						</tr>
						<tr>
							<td width="30%" align="right">Select encoder</td>
							<td>
								<select class="segInput select-encoder" name="param" id="acc_or_encoder" paramName="encoder" segOption="acc_or_reports">
								<?= $cashier_user_options ?>
								</select>
							</td>
						</tr>
						<tr>
							<td align="right">Date Start</td>
							<td nowrap="nowrap">
									<input class="segInput" name="param" id="acc_or_datestart" type="text" size="12" value="<?= $current_date ?>" paramName="datestart" segOption="acc_or_reports"/>
									<img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_acc_or_datestart" align="absmiddle" style="cursor:pointer;"  />
									<script type="text/javascript">
										Calendar.setup ({
											inputField : "acc_or_datestart", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_acc_or_datestart", singleClick : true, step : 1
										});
									</script>
							</td>
						</tr>
						<tr>
							<td align="right">Date End</td>
							<td>
									<input class="segInput" name="param" id="acc_or_dateend" type="text" size="12" value="<?= $current_date ?>" paramName="dateend" segOption="acc_or_reports"/>
									<img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_acc_or_dateend" align="absmiddle" style="cursor:pointer;"  />
									<script type="text/javascript">
										Calendar.setup ({
											inputField : "acc_or_dateend", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_acc_or_dateend", singleClick : true, step : 1
										});
									</script>
							</td>
						</tr>
					</table>

					<table width="100%" border="0" cellpadding="2" cellspacing="0" style="font:normal 12px Tahoma;margin-top:5px">
						<tr>
							<td width="30%"></td>
							<td>
								<button class="segButton" onclick="openReport();"><img src="<?= $root_path ?>gui/img/common/default/report.png" /> View Report </button>
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

$sTemp = ob_get_contents();
ob_end_clean();

# Assign the form template to mainframe
$smarty->assign('sMainFrameBlockData',$sTemp);

/**
 * show Template
 */
$smarty->display('common/mainframe.tpl');
?>