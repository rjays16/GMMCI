<?php
/**
* Created By Genesis D. Ortiz 05-22-2014
*/
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

global $db;

define('NO_2LEVEL_CHK',1);
define('LANG_FILE','products.php');
$local_user='ck_prod_db_user';

require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'modules/billing/ajax/bill-list.common.php');

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


if($_GET['user_origin']){
	 switch($_GET['user_origin']){
	 	case 'billing':
	 		$breakfile=$root_path.'modules/billing/bill-main-menu.php'.URL_APPEND;
	 		break;
	 	case 'ipd':
	 		$breakfile=$root_path.'modules/ipd/seg-ipd-functions.php'.URL_APPEND;
	 		break;
	 	case 'opd':
	 		$breakfile=$root_path.'modules/opd/seg-opd-functions.php'.URL_APPEND;
	 		break;
	 	case 'er':
	 		$breakfile=$root_path.'modules/opd/seg-opd-functions.php'.URL_APPEND;
	 		break;
	 	case 'medocs':
	 		$breakfile=$root_path.'modules/medocs/seg-medocs-functions.php'.URL_APPEND;
	 		break;
	 	case 'nursing':
	 		$breakfile=$root_path.'modules/nursing/nursing.php'.URL_APPEND;
	 		break;
	 	case 'cashier':
	 		$breakfile= $root_path.'modules/cashier/seg-cashier-functions.php'.URL_APPEND;
	 		break;
	 	default:
	 		$breakfile=$root_path.$HTTP_SESSION_VARS['sess_path_referer'].URL_APPEND;
	 }
}else{
	$break_file = $root_path.'main/startframe.php'.URL_APPEND;
}
 
//$db->debug=1;

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme
 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

 if ($_GET["src"]) {
	 $smarty->assign('bHideTitleBar',TRUE);
	 $smarty->assign('bHideCopyright',TRUE);

	 $src_link = "&src=".$_GET["src"];
	 if (isset($_GET['hid'])) $_SESSION["current_hcare_id"] = $_GET['hid'];
 }
 else {
	 # Title in the title bar
	 $smarty->assign('sToolbarTitle',"List of Patients");

	 $src_link = "";
 }

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
# $smarty->assign('pbHelp',"javascript:gethelp('products_db.php','search','$from','$cat')");
 $smarty->assign('pbHelp',"javascript:gethelp('billing_main.php')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"List of Patients");


 # Collect javascript code
 ob_start()

?>
<!-- OLiframeContent(src, width, height) script:
 (include WIDTH with its parameter equal to width, and TEXTPADDING,0, in the overlib call)
-->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>

<!-- Core module and plugins:
-->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="js/billing-list.js"></script>
<script language="javascript" type="text/javascript">
<!--
function selrecordOnChange() {
				var optSelected = $('selrecord').options[$('selrecord').selectedIndex];
				var spans = document.getElementsByName('selrecordoptions');

				for (var i=0; i<spans.length; i++) {
						if (optSelected) {
								if (spans[i].getAttribute("segOption") == optSelected.value) {
										spans[i].style.display = $('chkspecific').checked ? "" : "none";
								}
								else
										spans[i].style.display = "none";
						}
				}

//				disableNav()
		}


		function isValidSearch(key) {
		if (typeof(key)=='undefined') return false;
		var s=key.toUpperCase();
		var skey =$('inpatient').value;
		var skey1 = $('erpatient').value;
		//var skey2 = $('bill_number').value;
		var skey3 = $('outpatient').value;
		if (skey=='' && skey2=='' && skey3=='') 
		{return (
		/^\d{1,2}\/\d{1,2}\/\d{4}$/.test(s) ||
		/^\d{1,2}\-\d{1,2}\-\d{4}$/.test(s) ||
		/^\d{10,}$/.test(s)
		);}
		else if (skey=='' && skey1=='' && skey3=='') 
		{ return (
		/^\d{1,2}\/\d{1,2}\/\d{4}$/.test(s) ||
		/^\d{1,2}\-\d{1,2}\-\d{4}$/.test(s) ||
		/^\d{5,}$/.test(s)
		);}
		else if (skey=='' && skey1=='' && skey2=='') 
		{ return (
		/^\d{1,2}\/\d{1,2}\/\d{4}$/.test(s) ||
		/^\d{1,2}\-\d{1,2}\-\d{4}$/.test(s) ||
		/^\d{7,}$/.test(s)
		);}
		return (
		/^[A-Z0-9,A-Z0-9?\-\.]{3,}$/.test(s)
		);
		}

		function disableSearch(){
        	if( $('chkspecific').checked ){
				if ($('inpatient').value != '' || $('bill_number').value != '' || $('erpatient').value != '' || $('outpatient').value != '')
				 	{
					// if ($('bill_number').value != '')
					// 	b=isValidSearch(document.getElementById('bill_number').value);
					if ($('erpatient').value != '')
						b=isValidSearch(document.getElementById('erpatient').value);
					else if ($('outpatient').value != '')
						b=isValidSearch(document.getElementById('outpatient').value);
                     else
                     	b=isValidSearch(document.getElementById('inpatient').value);
			   		document.getElementById("search-btn").disabled = b?false:true;
		            }
               else
               		document.getElementById("search-btn").disabled = true;
			}
			else if($('chkdate').checked && !$('chkspecific').checked)
       	     	document.getElementById("search-btn").disabled = false;
        	/*else
             	document.getElementById("search-btn").disabled = true;*/
		}

		function emptier(){
			document.getElementById('inpatient').value='';
			document.getElementById('erpatient').value='';
			document.getElementById('outpatient').value='';
		}

	function openWindow(url, query)
	{
		if (!query)
			query = [];
		window.open(url+query.join('&'),'pdf',"width=800,height=600,menubar=no,resizable=yes,scrollbars=yes");
	}


	function pSearchClose() {
		cClick();
	}

	function disableNav() {
		with ($('pageFirst')) {
			className = 'segDisabledLink'
			setAttribute('onclick','')
		}
		with ($('pagePrev')) {
			className = 'segDisabledLink'
			setAttribute('onclick','')
		}
		with ($('pageNext')) {
			className = 'segDisabledLink'
			setAttribute('onclick','')
		}
		with ($('pageLast')) {
			className = 'segDisabledLink'
			setAttribute('onclick','')
		}
	}

	var djConfig = { isDebug: true };
	var FIRST_PAGE=1, PREV_PAGE=2, NEXT_PAGE=3, LAST_PAGE=4, SET_PAGE=0;

	function jumpToPage(jumptype, page) {
		var form1 = document.forms[0];

		switch (jumptype) {
			case FIRST_PAGE:
				$('jump').value = 'first';
			break;
			case PREV_PAGE:
				$('jump').value = 'prev';
			break;
			case NEXT_PAGE:
				$('jump').value = 'next';
			break;
			case LAST_PAGE:
				$('jump').value = 'last';
			break;
			case SET_PAGE:
				$('jump').value = page;
			break;
		}

		form1.submit();
	}

	function validate() {
		$('page').value = 0;
		return true;
	}

	function keepFilters(noption) {
		var filter = '';

		if (noption == 0) {
			if ($('chkspecific').checked) {
				var opt = $('selrecord').options[$('selrecord').selectedIndex];
				filter = $(opt.value).value;
				xajax_updateFilterOption(0, true);
				xajax_updateFilterTrackers($('selrecord').value, filter);
			}
			else
				xajax_updateFilterOption(0, false);
		}
		else {
			if ($('chkdate').checked) {
				if ($('seldate').value == 'specificdate') {
					filter = $('specificdate').value;
				}
				if ($('seldate').value == 'between') {
					filter = new Array($('between1').value, $('between2').value);
				}

				xajax_updateFilterOption(1, true);
				xajax_updateFilterTrackers($('seldate').value, filter);
			}
			else
				xajax_updateFilterOption(1, false);
		}
		clearPageTracker();
	}

	function keepPage() {
		var pg = $('page').value;
		xajax_updatePageTracker(pg);
	}

	function clearPageTracker() {
		xajax_clearPageTracker();
	}

	function prepareSelect(enc_nr) {
//		xajax_addSelectedEncounter(enc_nr);
				var list, dBody, tmp;

				var elemRow = document.getElementById("row_"+enc_nr);
				if (elemRow) {
						removeEncounterNr(enc_nr);
						xajax_noteSelectedEncounter(enc_nr);
				}
				else {
						 if (!list) list = $('cases');
						 if (list) {
								dBody=list.getElementsByTagName("tbody")[0];
								tmp = '<tr id="row_'+enc_nr+'"><td><input type="hidden" name="cases_added[]" value="'+enc_nr+'" /></td></tr>';
								dBody.innerHTML += tmp;

								xajax_noteSelectedEncounter(enc_nr);
						 }
				}
		}

		function removeEncounterNr(enc_nr) {
				var table = $('cases');
				var rmvRow=document.getElementById("row_"+enc_nr);
				if (table && rmvRow)
						table.deleteRow(rmvRow.rowIndex);
				else
						alert(table+' and '+rmvRow);
		}

		function openInfo(encounter_nr, ptype){
			window.location.href = '<?=$root_path?>modules/registration_admission/aufnahme_daten_zeigen.php?ntid=false&lang=en&encounter_nr='+encounter_nr+'&origin=cashier&ptype='+ptype;
		}

		function setCoverage(encounter_nr){
			return overlib(
         		 OLiframeContent('../../modules/registration_admission/manual_coverage.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&popUp=1&is_doctor=1&pid=<?=$pid?>&encounter_nr='+encounter_nr, 
		  						300, 440, 'fGroupTray', 0, 'auto'),
          						WIDTH,800, TEXTPADDING,0, BORDER,0, 
									STICKY, SCROLL, CLOSECLICK, MODAL, 
									CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0 onClick="">',
						         CAPTIONPADDING,2, CAPTION,'set coverage',
						         MIDX,0, MIDY,0, 
						         STATUS,'set coverage');	
		}

		function lock(encounter_nr, islock){
			var lock = (islock == 0 ? "unlock" : "lock");
		
			var ans = confirm("Are you sure you want to "+lock+ " charging for this patient's transactions?");
			
			if(ans){
				xajax_lockCharging(encounter_nr, islock);	
			}
			
		}

		function refreshPage(){
			location.reload();
		}
-->
</script>

<?php

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

$xajax->printJavascript($root_path.'classes/xajax');

# Buffer page output
include($root_path."include/care_api_classes/billing/class_bill_patients.php");

$objpatients = new BillPatients();

if (!$_POST["applied"]) {
		if (isset($_SESSION["filteroption"])) {
				if (isset($_SESSION["filteroption"][0])) $_POST["chkspecific_"] = strcmp($_SESSION["filteroption"][0], 'true') == 0;
				if (isset($_SESSION["filteroption"][1])) $_POST["chkdate"] = strcmp($_SESSION["filteroption"][1], 'true') == 0;
		}

		if (isset($_SESSION["filtertype"])) {
				switch (strtolower($_SESSION["filtertype"])) {
						case "inpatient":
						case "erpatient":
						case "outpatient";
						case "mgh";

						default:
								$_POST["seldate"] = $_SESSION["filtertype"];
								if (is_array($_SESSION["filter"])) {
										$_POST["between1"] = $_SESSION["filter"][0];
										$_POST["between2"] = $_SESSION["filter"][1];
								}
								else
										if ($_SESSION["filter"] != "")
												$_POST["specificdate"] = $_SESSION["filter"];
				}
		}
		else {
				if (is_null($_SESSION["filteroption"])) {
					$_POST['chkspecific_'] = true;
				}

				$_POST["selrecord"] = "inpatient";
		}
}

if (isset($_SESSION["current_page"])) {
		$_POST['page'] = $_SESSION["current_page"];
}


if(!$_GET['refresh']){
	$_POST['chkspecific_'] = true;
	$_POST["selrecord"] = "inpatient";
}

$title_sufx = (!$_GET['src']) ? 'Patient' : 'Claims to Transmit';

if ($_POST['chkdate']) {
		switch(strtolower($_POST["seldate"])) {
				case "today":
						$search_title = "Today's $title_sufx";
						$filters['DATETODAY'] = "";
				break;
				case "thisweek":
						$search_title = "This Week's $title_sufx";
						$filters['DATETHISWEEK'] = "";
				break;
				case "thismonth":
						$search_title = "This Month's $title_sufx";
						$filters['DATETHISMONTH'] = "";
				break;
				case "specificdate":
						$search_title = "$title_sufx On " . date("F j, Y",strtotime($_POST["specificdate"]));
						$dDate = date("Y-m-d",strtotime($_POST["specificdate"]));
						$filters['DATE'] = $dDate;
				break;
				case "between":
						$search_title = "$title_sufx From " . date("F j, Y",strtotime($_POST["between1"])) . " To " . date("F j, Y",strtotime($_POST["between2"]));
						$dDate1 = date("Y-m-d",strtotime($_POST["between1"]));
						$dDate2 = date("Y-m-d",strtotime($_POST["between2"]));
						$filters['DATEBETWEEN'] = array($dDate1,$dDate2);
				break;
		}
}

if ($_POST['chkspecific_']) {
		switch(strtolower($_POST["selrecord"])) {
				case "inpatient":
						$filters["INPATIENT"] = $_POST["inpatient"];
				break;
				case "erpatient":
						$filters["ERPATIENT"] = $_POST["erpatient"];
				break;
				case "outpatient":
						$filters["OUTPATIENT"] = $_POST["outpatient"];
				break;
				case "mgh":
						$filters["MGH"] = $_POST["mgh"];
				break;
		}
}

/*added by mai 08/06/2014*/

if($_POST['keyword']){
	$keyword = $_POST['keyword'];
}else{
	$keyword = "";
}

if ($_POST['chkspecific']) {
		switch(strtolower($_POST["selrecordby"])) {
				case "name":
						$filters["name"] = $_POST["name"];
				break;
				case "pid":
						$filters["pid"] = $_POST["pid"];
				break;
				case "encounter_nr":
						$filters["encounter_nr"] = $_POST["encounter_nr"];
				break;
		}
}
/*end added by mai*/


$current_page = $_POST['page'];
if (!$current_page) $current_page = 0;
$list_rows = 15;
switch (strtolower($_POST['jump'])) {
		case 'last':
				$current_page = $_POST['lastpage'];
		break;
		case 'prev':
				if ($current_page > 0) $current_page--;
		break;
		case 'next':
				if ($current_page < $_POST['lastpage']) $current_page++;
		break;
		case 'first':
				$current_page=0;
		break;
}

$_SESSION["current_page"] = $current_page;

if (!$_GET['src'])
		$result = $objpatients->getSavedPatients($filters, $list_rows * $current_page, $list_rows, $keyword);
else
		$result = $objpatients->getSavedPatients($filters, $list_rows * $current_page, $list_rows, $keyword);
		//$result = $objpatients->getSavedPatientsForTransmittal($filters, $list_rows * $current_page, $list_rows, $_SESSION["current_hcare_id"]);


$rows = "";
$last_page = 0;
$count=0;
if ($result) {
		$rows_found = $objpatients->FoundRows();
		if ($rows_found) {
				$last_page = floor($rows_found / $list_rows);
				$first_item = $current_page * $list_rows + 1;
				$last_item = ($current_page+1) * $list_rows;
				if ($last_item > $rows_found) $last_item = $rows_found;
				$nav_caption = "Showing ".number_format($first_item)."-".number_format($last_item)." out of ".number_format($rows_found)." record(s)";
		}

		while ($row = $result->FetchRow()) {
			if(!$_GET['src'])	
				$spatient = $row['inpatient'];
			else
				$spatient = $objpatients->concatname($row["name_last"], $row["name_first"], $row["name_middle"]);

				$billingType = $objpatients->GetTypeBilling($row['bill_nr']);
				$hospital_discount = $objpatients->gethospitalDC($row['bill_nr']);
				$records_found = TRUE;
				if ($src_link == "") {
						$n_bill   = 0;

						/**
						* Fix for incorrect billing printout, 10-02-2010
						*/
						$query = "SELECT e.pid, b.bill_nr, b.encounter_nr, b.bill_frmdte, b.bill_dte\n".
							"FROM seg_billing_encounter b ORDER BY b.bill_dte\n".
							"INNER JOIN care_encounter e ON e.encounter_nr=b.encounter_nr\n".
							"WHERE b.bill_nr=".$db->qstr($row['bill_nr']). " and b.is_deleted IS NULL";
						$billRow = $db->GetRow( $query );
                       
                        $n_bill = $row['net'] - $hospital_discount;
                        if($n_bill < '0')
                        	$n_bill = 0;


						$btns = "<td align=\"right\" nowrap=\"nowrap\">";

                        global $allowedarea;
                            $allowedarea = array('_a_3_billDeleteBtn');
                         if (validarea($_SESSION['sess_permission'],1)) {
                                 $canDelete = "<a title=\"Delete\" href=\"#\">
                                                            <img class=\"segSimulatedLink\" src=\"".$root_path."images/cashier_delete.gif\" border=\"0\" align=\"absmiddle\" onclick=\"if (confirm('Delete this billing?')) deleteItem('".$row["bill_nr"]."','".$row["encounter_nr"]."','".$row["is_discharged"]."')\"/>
                                                    </a></td>";
                         } else {
                                 $canDelete  = "<a title=\"Delete\" href=\"#\">
                                                         <img class=\"disabled\" src=\"".$root_path."images/cashier_delete.gif\" border=\"0\" align=\"absmiddle\" onclick=\"onclick=\"return false;\" style=\"opacity:0.2\"/>
                                                  </a></td>";

                                                "<a style=\"display:none\" title=\"Print *fixed* Billing Statement\" href=\"javascript:openWindow('bill-pdf-summary.php".URL_APPEND."&userck=$userck&pid={$billRow['pid']}&encounter_nr={$billRow['encounter_nr']}&from_dt=".strtotime($billRow['bill_frmdte'])."&bill_dt=".strtotime($billRow['bill_dte'])."&nr={$row['bill_nr']}&IsDetailed=0&fix=1')\">
													<img class=\"segSimulatedLink\" src=\"".$root_path."images/cashier_print.gif\" border=\"0\" align=\"absmiddle\" />
												</a>
												<a style=\"display:none\" title=\"Printout\" href=\"javascript:openWindow('form2.php".URL_APPEND."&userck=$userck&encounter_nr={$billRow['encounter_nr']}&id=18&claim=0')\">
													<img title=\"Print *fixed* Form 2\" class=\"segSimulatedLink\" src=\"".$root_path."images/cashier_print2.gif\" border=\"0\" align=\"absmiddle\" />
												</a>";



                         } $allowedarea = array('_a_3_billViewBtn');
                          
                         if($billingType){	
                         	if (validarea($_SESSION['sess_permission'],1)) {
                          		  $canView = "<a title=\"View\" href=\"../billing_new/billing-main-new.php".URL_APPEND."&userck=$userck&nr=".$row["bill_nr"]."&from=billing-list\">
                                           <img class=\"segSimulatedLink\" src=\"".$root_path."images/cashier_edit.gif\" border=\"0\" align=\"absmiddle\" />
                                        </a>";
                         		}else {

                            		$canView = " <a title=\"View\" href=\"../billing_new/billing-main-new.php".URL_APPEND."&userck=$userck&nr=".$row["bill_nr"]."&from=billing-list\">
                                           <img class=\"disabled\" src=\"".$root_path."images/cashier_edit.gif\" onclick=\"return false;\" border=\"0\" align=\"absmiddle\" style=\"opacity:0.2\"/>
                                        </a>";
                        		}
                        }else{
                        	if (validarea($_SESSION['sess_permission'],1)) {

                        	    $canView = "<a title=\"View\" href=\"billing-main.php".URL_APPEND."&userck=$userck&nr=".$row["bill_nr"]."&from=billing-list\">
                                           <img class=\"segSimulatedLink\" src=\"".$root_path."images/cashier_edit.gif\" border=\"0\" align=\"absmiddle\" />
                                        </a>";
                         	}else {

                            	$canView = " <a title=\"View\" href=\"billing-main.php".URL_APPEND."&userck=$userck&nr=".$row["bill_nr"]."&from=billing-list\">
                                           <img class=\"disabled\" src=\"".$root_path."images/cashier_edit.gif\" onclick=\"return false;\" border=\"0\" align=\"absmiddle\" style=\"opacity:0.2\"/>
                                        </a>";
                        	}
                    	}

                       $patient_type = $row["encounter_type"];

                       $ptype = 'ipd';
                       if($patient_type == 3 OR $patient_type == 4){
                       		$patient_type = 'Inpatient';
                       }
                       else if($patient_type == 1){
                       		$patient_type = 'ER patient';
                       		$ptype = 'er';
                       }
                       else if($patient_type == 2){
                       		$patient_type = 'Out Patient';
                       		$ptype = 'opd';
                       }
                       else if($patient_type == 12)
                       		$patient_type = 'Well baby';
                       else{
                       	 $patient_type = 'Walk-in patient';
                       }

						$rows .= "<tr class=\"$class\">

													<td>".strftime("%Y-%m-%d %I:%M %p", strtotime($row["encounter_date"]))."</td>
													<td>".$row["encounter_nr"]."</td>
													<td>".$row["name_last"].", ".$row["name_first"]." ".$row["name_middle"].($row['is_maygohome']==1 ? "<span style='color:red'>- MGH</span>" : "")."</td>
													<td>".$patient_type."</td>
													<td>".$row['ward_name']."</td>";
													
													
													$rows .="<td>".$row['isphic']."</td>";

													$rows .= "<td align='center'><img onclick='openInfo(".'"'.$row['encounter_nr'].'","'.$ptype.'"'.");' style='cursor: pointer;' src = '".$root_path."images/cashier_edit.gif'></td>";
						$count++;

				}
				else {
						if (!(isset($_SESSION['cases'][$row["encounter_nr"]]) && ($_SESSION['cases'][$row["encounter_nr"]]))) {
								$btns = '<td align="center"><input type="checkbox" id="cases[]" name="cases[]" style="cursor:pointer" value="'.$row["encounter_nr"].'" '.
												' onclick="prepareSelect(\''.$row["encounter_nr"].'\')" /></td>';

								$rows .= "<tr class=\"$class\">
															<td width=\"10%\">".$row["insurance_nr"]."</td>
															<td width=\"10%\">".$row["categ_desc"]."</td>
															<td width=\"34%\">".$row["confine_period"]."</td>
															<td width=\"8%\">".$row["encounter_nr"]."</td>
															<td width=\"23%\">".$spatient."</td>
															<td width=\"12%\" align=\"right\">".number_format(round($row["this_coverage"], 0), 2, '.', ',')."</td>".$btns."</tr>\n";

								$count++;
						}
				}
		}
}
else {
//		print_r($result);
		$rows .= '        <tr><td colspan="10">No claims found ...</td></tr>';
		$sWarning = $objpatients->error_msg;
}

if (!$rows) {
		$records_found = FALSE;
		$rows .= '        <tr><td colspan="10">'.(!$_GET['src'] ? 'No patient admitted at this time ...' : 'No claims found ...').'</td></tr>';
}
ob_start();
?>
<form action="<?= $thisfile.URL_APPEND.'&sid=$sid&target=seg_billing_list_patients&refresh=true&user_origin='.$_GET['user_origin'] ?>" method="post" name="suchform" onSubmit="return validate()">
<div style="margin:5px;font-weight:bold;color:#660000"><?= $sWarning ?></div>
<div style="width:70%">
		<table width="100%" border="0" style="font-size: 12px; margin-top:5px" cellspacing="2" cellpadding="2">
				<tbody>
						<tr>
								<td align="left" class="jedPanelHeader" ><strong>Search options</strong></td>
						</tr>
						<tr>
								<td nowrap="nowrap" align="left" class="jedPanel">
										<table width="100%" border="0" cellpadding="2" cellspacing="0">
												<tr>
														<td width="50" align="right">
															<input type="checkbox" id="chkspecific_" name="chkspecific_" onclick="selrecordOnChange(); keepFilters(0);emptier(); disableSearch();" <?= ($_POST['chkspecific_'] ? 'checked' : '') ?>/>
														</td>
														<td width="5%" align="left" nowrap="nowrap">Patient Type</td>
														<td>

																<select class="jedInput" name="selrecord" id="selrecord" onchange="selrecordOnChange(); keepFilters(0);emptier();disableSearch();"/>
																		<option value="inpatient" <?= $_POST["selrecord"]=="inpatient" ? 'selected="selected"' : '' ?>>Inpatient</option>
																		<option value="mgh" <?= $_POST["selrecord"]=="mgh" ? 'selected="selected"' : '' ?>>Inpatient - MGH</option>
																		<option value="erpatient" <?= $_POST["selrecord"]=="erpatient" ? 'selected="selected"' : '' ?>>ER Patient</option>
																		<option value="outpatient" <?= $_POST["selrecord"]=="outpatient" ? 'selected="selected"' : '' ?>>Out Patient</option>
                                                                        
																</select>
														</td>
												</tr>
												<!-- added by mai 08/06/2014 -->
												<tr>
													<td width="50" align="right">
																<input type="checkbox" id="chkspecific" name="chkspecific" onclick="selrecordOnChange(); keepFilters(0);emptier(); disableSearch();" <?= ($_POST['chkspecific'] ? 'checked' : '') ?>/>
														</td>
														<td width="5%" align="left" nowrap="nowrap">Search By</td>
														<td>

																<select class="jedInput" name="selrecordby" id="selrecordby" onchange="selrecordOnChange(); keepFilters(0);emptier();disableSearch();"/>
																		<option value="name" <?= $_POST["selrecordby"]=="name" ? 'selected="selected"' : '' ?>>Name</option>
																		<option value="pid" <?= $_POST["selrecordby"]=="pid" ? 'selected="selected"' : '' ?>>HRN</option>
																		<option value="encounter_nr" <?= $_POST["selrecordby"]=="encounter_nr" ? 'selected="selected"' : '' ?>>Case No.</option>
                                                                        
																</select>
																<td>
																<span>
																		<input class="jedInput" name="keyword" id="keyword" onblur="keepFilters(0);" onkeyup="disableSearch();" type="text" size="30" value="<?= $_POST['keyword'] ?>"/>
																</span>	
														</td>
												</tr>
												<!-- end added by mai -->
												<tr>
														<td width="5%" align="right"><input type="checkbox" id="chkdate" name="chkdate" <?= ($_POST['chkdate'] ? 'checked' : '') ?> onclick="seldateOnChange();keepFilters(1);disableSearch();"/></td>
														<td width="15%" nowrap="nowrap" align="left"><?= ($_GET["src"]) ? 'Discharge' : 'Admission' ?> date</td>
														<td width="20%" align="left">
<script language="javascript" type="text/javascript">
<!--
		function seldateOnChange() {
				var filter = '';

				var optSelected = $('seldate').options[$('seldate').selectedIndex]
				var spans = document.getElementsByName('seldateoptions')
				for (var i=0; i<spans.length; i++) {
						if (optSelected) {
								if (spans[i].getAttribute("segOption") == optSelected.value) {
										spans[i].style.display = $('chkdate').checked ? "" : "none";

										if (optSelected.value == "specificdate")
												filter = $(optSelected.value).value
										else
												filter = new Array($('between1').value, $('between2').value);
								}
								else
										spans[i].style.display = "none"
						}
				}

//				disableNav()
		}
-->
</script>
																<select class="jedInput" id="seldate" name="seldate" onchange="seldateOnChange(); keepFilters(1);">
																		<option value="today" <?= $_POST["seldate"]=="today" ? 'selected="selected"' : '' ?>>Today</option>
																		<option value="thisweek" <?= $_POST["seldate"]=="thisweek" ? 'selected="selected"' : '' ?>>This week</option>
																		<option value="thismonth" <?= $_POST["seldate"]=="thismonth" ? 'selected="selected"' : '' ?>>This month</option>
																		<option value="specificdate" <?= $_POST["seldate"]=="specificdate" ? 'selected="selected"' : '' ?>>Specific date</option>
																		<option value="between" <?= $_POST["seldate"]=="between" ? 'selected="selected"' : '' ?>>Between</option>
																</select>
																</td>
																<td>
																<span name="seldateoptions" segOption="specificdate" <?= ($_POST["seldate"]=="specificdate") && $_POST['chkdate'] ? '' : 'style="display:none"' ?>>
																		<input onchange="keepFilters(1);" class="jedInput" name="specificdate" id="specificdate" type="text" size="8" value="<?= $_POST['specificdate'] ?>"/>
																		<img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_specificdate" align="absmiddle" style="cursor:pointer"  />
																		<script type="text/javascript">
																				Calendar.setup ({
																						inputField : "specificdate", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_specificdate", singleClick : true, step : 1
																				});
																		</script>
																</span>
																<span name="seldateoptions" segOption="between" <?= ($_POST["seldate"]=="between") && $_POST['chkdate'] ? '' : 'style="display:none"' ?>>
																		<input onchange="keepFilters(1);" class="jedInput" name="between1" id="between1" type="text" size="8" value="<?= $_POST['between1'] ?>"/>
																		<img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_between1" align="absmiddle" style="cursor:pointer;"  />
																		<script type="text/javascript">
																				Calendar.setup ({
																						inputField : "between1", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_between1", singleClick : true, step : 1
																				});
																		</script>
																		to
																		<input onchange="keepFilters(1);" class="jedInput" name="between2" id="between2" type="text" size="8" value="<?= $_POST['between2'] ?>"/>
																		<img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_between2" align="absmiddle" style="cursor:pointer"  />
																		<script type="text/javascript">
																				Calendar.setup ({
																						inputField : "between2", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_between2", singleClick : true, step : 1
																				});
																		</script>
																</span>
														</td>
												</tr>
												<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
												<tr>
														<td></td>
														<td colspan="2">
																<input type="submit" id="search-btn" style="cursor:pointer" value="Search"  class="jedButton"/>
														</td>
												</tr>
										</table>
								</td>
						</tr>
				</tbody>
		</table>
</div>

<div style="width:<?= (!$_GET['src']) ? '90' : '100' ?>%">
		<table width="100%" class="segContentPaneHeader" style="margin-top:10px">
		<tr><td>
				<h1>
						Search result:
<?php
		echo $search_title;  ?></h1></td>
<?php if ($_GET['src']) { ?>
				<td align="right"><img src="<?= $root_path ?>images/btn_submitorder.gif" align="center" onclick="assignEncNrsBilled();$('fill_up').value = '1';document.forms[0].submit();" style="cursor:pointer" /></td>
				<?php } ?>
		</tr>
		</table>
		<div class="segContentPane">
				<table id="" class="jedList" width="100%" border="0" cellpadding="0" cellspacing="0">
						<thead>
								<tr class="nav">
										<th colspan="9">
												<div id="pageFirst" class="<?= ($current_page > 0) ? 'segSimulatedLink' : 'segDisabledLink' ?>" style="float:left" onclick="jumpToPage(FIRST_PAGE)">
														<img title="First" src="<?= $root_path ?>images/start.gif" border="0" align="absmiddle"/>
														<span title="First">First</span>
												</div>
												<div id="pagePrev" class="<?= ($current_page > 0) ? 'segSimulatedLink' : 'segDisabledLink' ?>" style="float:left" onclick="jumpToPage(PREV_PAGE)">
														<img title="Previous" src="<?= $root_path ?>images/previous.gif" border="0" align="absmiddle"/>
														<span title="Previous">Previous</span>
												</div>
												<div id="pageShow" style="float:left; margin-left:10px">
														<span><?= $nav_caption ?></span>
												</div>
												<div id="pageLast" class="<?= ($current_page < $last_page) ? 'segSimulatedLink' : 'segDisabledLink' ?>" style="float:right" onclick="jumpToPage(LAST_PAGE)">
														<span title="Last">Last</span>
														<img title="Last" src="<?= $root_path ?>images/end.gif" border="0" align="absmiddle"/>
												</div>
												<div id="pageNext" class="<?= ($current_page < $last_page) ? 'segSimulatedLink' : 'segDisabledLink' ?>" style="float:right" onclick="jumpToPage(NEXT_PAGE)">
														<span title="Next">Next</span>
														<img title="Next" src="<?= $root_path ?>images/next.gif" border="0" align="absmiddle"/>
												</div>
										</th>
								</tr>
								<tr>
										 <th width="20%">Date</th>
										 <th width="20%">Case No.</th>
										 <th width="*">Patient</th>
										 <th width="5%">Admission Type</th>
										 <th width="20%">Ward</th>
										 <th width="5%">PHIC</th>
										 <th width="1%">Action</th>
										 <?php if($_GET['user_origin'] == 'billing') { ?>
										 	<th width="5%">Details</th>
										 <?php } ?>
								</tr>
						</thead>
						<tbody>
						<?= $rows ?>
						</tbody>
				</table>
				<br />
		</div>
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

<input type="hidden" name="sid" value="<?php echo $sid ?>">
<input type="hidden" name="lang" value="<?php echo $lang ?>">
<input type="hidden" name="userck" value="<?php echo $userck ?>">
<input type="hidden" name="cat" value="<?php echo $cat?>">
<input type="hidden" name="userck" value="<?php echo $userck?>">
<input type="hidden" name="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>">
<input type="hidden" name="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>">
<input type="hidden" name="lockflag" value="<?php echo  $lockflag?>">

<input type="hidden" id="delete" name="delete" value="" />

<input type="hidden" id="enc_nr" name="enc_nr" value="" />
<input type="hidden" id="is_discharged" value="" />
<input type="hidden" id="page" name="page" value="<?= $current_page ?>" />
<input type="hidden" id="lastpage" name="lastpage"  value="<?= $last_page ?>" />
<input type="hidden" id="jump" name="jump">
<input type="hidden" id="applied" name="applied" value="1">
<input type="hidden" id="root_path" name="root_path" value="<?php echo $root_path ?>" />
<input type="hidden" id="fill_up" name="fill_up" value="">
<div style="display:none" id="cases_selected">
		<table id="cases">
				<tbody>
				</tbody>
		</table>
</div>
<div style="display:none" id="cases_list"></div>
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