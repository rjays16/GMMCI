<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org
*
* See the file "copy_notice.txt" for the licence notice
*/
define('LANG_FILE','order.php');
define('NO_2LEVEL_CHK',1);
$local_user='ck_prod_db_user';
require_once($root_path.'include/inc_front_chain_lang.php');
# Create products object
$dbtable='care_config_global'; // Table name for global configurations
$GLOBAL_CONFIG=array();
$new_date_ok=0;
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

$title=$LDPharmacy;
$breakfile=$root_path."modules/cashier/seg-cashier-functions.php".URL_APPEND;
$imgpath=$root_path."pharma/img/";
$thisfile=basename(__FILE__);

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"Cashier::Process billing");

 $smarty->assign('bHideTitleBar',TRUE);
 $smarty->assign('bHideCopyright',TRUE);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('products_db.php','search','$from','$cat')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"Cashier::Process billing");

 # Assign Body Onload javascript code
 $smarty->assign('sOnLoadJs','onLoad=""');

 # Collect javascript code
 ob_start()

?>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/scriptaculous/scriptaculous.js" ></script>
<script type="text/javascript">
	function tabClick(el) {
		if (el.className!='segActiveTab') {
			$('mode').value = el.getAttribute('segSetMode');
			var dList = $(el.parentNode);
			if (dList) {
				var listItems = dList.getElementsByTagName("LI");
				for (var i=0;i<listItems.length;i++) {
					if (listItems[i] != el) {
						listItems[i].className = "";
						if ($(listItems[i].getAttribute('segTab'))) $(listItems[i].getAttribute('segTab')).style.display = "none";
					}
				}
				if ($(el.getAttribute('segTab'))) 
					$(el.getAttribute('segTab')).style.display = "block";
				el.className = "segActiveTab";
			}
		}
	}
	//edited for splitting doctor pf 2/18/2014
	function prepareAdd(id) {
		var details = new Object();
		details.id = $('id_'+id).value;
		details.name = $('name_'+id).value;
		details.desc = "Billing";
		details.qty = 1;
		details.origprice = $('price_'+id).value;
		details.price = $('price_'+id).value;
		details.hospital = $('hospital_'+id).value;
		details.doctor = $('doctor_'+id).value;
		details.ispaid = $('paid_'+id).value;
		details.isdoctor = $('doctor_paid_'+id).value;
		details.calculate= 1;
		details.checked= 1;
		details.showdel= 1;
		details.limit= -1;
		details.doreplace = 1;
		details.src = 'fb';
		details.ref = '0000000000';
		//result = window.parent.addServiceToList(details);
		result = window.parent.addServiceToList1(details);
		if (result) {
			alert("Bill entry added to payment list...");
		}
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
	
	
	function pSearchClose() {
		$('search').disabled = true
		cClick()
		document.forms[0].submit()
	}
	
function myBlindUp(id) {
	var element = 'd'+id;
	element = $(element);
	element.makeClipping();
	element.style.overflowX = 'hidden';
	element.style.overflowY = 'scroll';
	return new Effect.Scale(element, 0,
		Object.extend({ scaleContent: false, 
			scaleX: false, 
			restoreAfterFinish: true,
			afterFinishInternal: function(effect) {
				effect.element.hide().undoClipping();
				$('details-'+id).style.display = 'none';
			} 
		}, arguments[1] || { })
	);
};

function myBlindDown (id) {
	var element = 'd'+id;
	element = $(element);
	$('details-'+id).style.display = '';
	var elementDimensions = element.getDimensions();
	elementDimensions.height = 180;
	return new Effect.Scale(element, 100, Object.extend({ 
		scaleContent: false, 
		scaleX: false,
		scaleFrom: 0,
		scaleMode: {originalHeight: elementDimensions.height, originalWidth: elementDimensions.width},
		restoreAfterFinish: false,
		afterSetup: function(effect) {
			effect.element.makeClipping().setStyle({height: '0px'}).show();
			element.style.overflowX = 'hidden';
			element.style.overflowY = 'scroll';
		},  
		afterFinishInternal: function(effect) {
			effect.element.undoClipping();
			element.style.overflowX = 'hidden';
			element.style.overflowY = 'scroll';
		}
	}, arguments[1] || { }));
};

function toggleDetails(id) {
	if ($('details-'+id)) {
		var show = ($('details-'+id).getAttribute('hideDetails') != 1);
		$('details-'+id).setAttribute('hideDetails',(show ? '1' : ''));
	}
	else { }
	
	var dt, nr;
	dt = $('d'+id);
	nr = $('id_'+id).value;
	if (dt) {
		if (!dt.src) setTimeout("$('d"+id+"').src='seg-cashier-main-billingdetails.php?nr="+nr+"'",300);
		if (show) {
			myBlindDown(id, { duration:0.2 });
		}
		else {
			myBlindUp(id, { duration:0.2 });
		}
		//if ($('expand-'+id)) $('expand-'+id).style.display = show ? "none" : "";
		//if ($('collapse-'+id)) $('collapse-'+id).style.display = show ? "" : "none";
	}
}
</script>

<?php

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);
# Buffer page output
global $db;

include($root_path."include/care_api_classes/class_cashier.php");
$cClass = new SegCashier();

// include($root_path."include/care_api_classes/class_person.php"); //added by Jarel 09/09/2014
// $person_obj = new Person(); //added by Jarel

// include($root_path."include/care_api_classes/class_pf.php");
// $pf_obj = new Pf();

# Resolve current page (pagination)
$current_page = $_REQUEST['page'];
if (!$current_page) $current_page = 0;
$list_rows = 15;
switch (strtolower($_REQUEST['jump'])) {
	case 'last':
		$current_page = $_REQUEST['lastpage'];
	break;
	case 'prev':
		if ($current_page > 0) $current_page--;
	break;
	case 'next':
		if ($current_page < $_REQUEST['lastpage']) $current_page++;
	break;
	case 'first': default:
		$current_page=0;
	break;
}	

$count=0;
$rows = "";
$last_page = 0;
if ($_POST['submitted'] || $_REQUEST['patient']) {
	$patient = $_REQUEST['patient'];
	$result = $cClass->GetPatientBillingEncounter($patient, $_GET['or'],$list_rows * $current_page, $list_rows);
	if ($result) {			
		$rows_found = $cClass->FoundRows();
		if ($rows_found) {
			$last_page = floor($rows_found / $list_rows);
			$first_item = $current_page * $list_rows + 1;
			$last_item = ($current_page+1) * $list_rows;
			if ($last_item > $rows_found) $last_item = $rows_found;
			$nav_caption = "Showing ".number_format($first_item)."-".number_format($last_item)." out of ".number_format($rows_found)." record(s)";
		}
		while ($row=$result->FetchRow()) {
			$class = (($count%2)==0)?"":"alt";
			#foreach ($items as $i=>$v) {
			#	$items[$i] = "<span style=\"white-space:nowrap;color:".stringToColor($v)."\">$v</span>";
			#}
			
//			$detailsArray = array(
//				'acc'=>'Accommodation',
//				'med'=>'Medicines',
//				'sup'=>'Supplies',
//				'srv'=>'Services',
//				'ops'=>'Procedures',
//				'doc'=>'Doctor\'s Fees',
//				'msc'=>'Miscellaneous'
//			);
//			
//			$grand_total = 0;
//			$discount = (float) $row['discount'];
//			if ($discount > 1) $discount = 1;
//			foreach ($detailsArray as $i => $details) {
//				$total = (float) $row['total_'.$i.'_charge'];
//				$total = $total * (1-$discount);
//				$total = $total - (float) $row['total_'.$i.'_coverage'];
//				$grand_total += $total;
//			}
			//edited some functions to spilt doctor and hospital bill -ken 2/18/2014 
			$coverage = $cClass->getCoverageTotal($row['bill_nr']);
			$discount = $cClass->getDiscountTotal($row['bill_nr']);
			$hbDiscount = $cClass->getHospitalDiscount($row['bill_nr']);
			$prof_coverage = $cClass->getProfCoverageTotal($row['bill_nr']);
			$prof_discount = $cClass->getProfDiscountTotal($row['bill_nr']);
			$deposit = ($cClass->getDepositTotal2($row['bill_nr']) / 2);

			$hospital = $row['total'] - ($coverage + $discount + $hbDiscount + $deposit);
			$doctor = $row['doctor'] - ($prof_coverage + $prof_discount + $deposit);

			$amount_hf = $person_obj->getPersonOutstandingBill($row["bill_nr"],'(HF)');
			$amount_pf = $person_obj->getPersonOutstandingBill($row["bill_nr"],'(PF)');
			
			$hospital = $hospital + (( $amount_hf ) ? $amount_hf : 0 );
			$doctor = $doctor + (( $amount_pf ) ? $amount_pf : 0 );

			if($doctor < 0)
				$doctor = 0;
			if($hospital < 0)
				$hospital = 0;

			//paid amount from tagging
			$paid_dr = 0;
			$paid_dr = $pf_obj->sumPf($row['encounter_nr']);
			$doctor = $doctor - $paid_dr;

			if($doctor <= 0){
				$row['doctor_flag'] = "paid";
			}

			if($hospital <= 0){
				$row['request_flag'] = "paid";
			}
			//

			$total = $hospital + $doctor;


			if($row['request_flag']=='paid' && $row['doctor_flag']=='paid'){
				$request_flag = 'paid';
			}elseif(!$row['request_flag'] && !$row['doctor_flag']){
				$request_flag = NULL;
			}else{
				$request_flag = 'partial';
			}
			
			$bill_date = date("Y-m-d h:ia",strtotime($row["bill_dte"]));
			$from_date = date("Y-m-d h:ia",strtotime($row["bill_frmdte"]));
			$name = $row["fullname"];
			ob_start();
?>
			<tr class="<?= $class ?>">
				<td style="white-space:nowrap">
					<input type="hidden" id="id_<?= $count ?>" value="<?= $row['bill_nr'] ?>" />
					<span class="segSimulatedLink" onclick="toggleDetails('<?= $count ?>')" style="cursor:pointer;color:#600000">
					<?= $row["bill_nr"] ?>
					<img src="<?= $root_path ?>images/msgarrow.gif" align=\"absmiddle\"></span>
				</td>
				<td style="color:#000080" class="centerAlign"><?= $bill_date ?></td>
				<td style="color:#000080" class="centerAlign"><?= $from_date ?></td>
				<td style="color:#008000" ><?= $row["encounter_nr"] ?></td>
				<td>
					<input type="hidden" id="name_<?= $count ?>" value="<?= "$name ($bill_date)" ?>" />
					<span><?= $name ?></span>
				</td>
				<td style="color:#008000" align="right">
					<input type="hidden" id="price_<?= $count ?>" value="<?= $total ?>"/>
					<input type="hidden" id="hospital_<?= $count ?>" value="<?= $hospital ?>"/>
					<input type="hidden" id="doctor_<?= $count ?>" value="<?= $doctor ?>"/>
					<?= number_format((float)$total,2) ?>
				</td>
				<td align="center">
					<input type="hidden" id="paid_<?= $count ?>" value="<?= ($row['request_flag'] == "paid" ? "1" : "") ?>"/>
					<input type="hidden" id="doctor_paid_<?= $count ?>" value="<?= ($row['doctor_flag']  == "paid" ? "1" : "") ?>"/>
						<?= ($request_flag ? ("<img src=\"".$root_path."images/flag_{$request_flag}.gif\" align=\"absmiddle\" />") : ""); ?>
				</td>
				<td class="centerAlign"><input class="segButton" type="button" style="color:#000060" value=">" onclick="prepareAdd('<?= $count ?>')" /></td>
			</tr>
			<tr id="details-<?= $count ?>" style="display:none;">
				<td id="detailstd-<?= $count ?>" style="padding:3px;background-color:#003366" class="plain" colspan="8" align="center">
					<div>
						<iframe id="d<?= $count ?>" frameborder="0" scrolling="yes" style="height:160px;overflow-x:hidden;overflow-y:scroll;width:100%;background-color:white;display:block"></iframe>
					</div>
				</td>
			</tr>
<?php
			$rows.=ob_get_contents();
			ob_end_clean();
			$count++;
		}
		if (!$rows)
			$rows = '		<tr><td colspan="10">No billing records found for this patient...</td></tr>';
	}
	else
		$rows = '		<tr><td colspan="10">'.$cClass->sql.'</td></tr>';
}

if (!$rows)
	$rows = '		<tr><td colspan="10">Select patient record and click Search to begin query...</td></tr>';


ob_start();
?>

<form action="<?= $thisfile.URL_APPEND."&clear_ck_sid=".$clear_ck_sid ?>" method="post" name="suchform" onSubmit="return validate()">

<input type="hidden" name="submitted" value="1">
<input type="hidden" name="sid" value="<?php echo $sid?>">
<input type="hidden" name="lang" value="<?php echo $lang?>">
<input type="hidden" name="cat" value="<?php echo $cat?>">
<input type="hidden" name="userck" value="<?php echo $userck ?>">
<input type="hidden" id="mode" name="mode" value="<?= $_REQUEST['mode'] ?>">

<div style="width:98%; margin:0px; margin-top:10px" align="center">
	<div  style="width:100%; margin:0px" align="center">
		<table class="jedList" width="100%" border="0" cellpadding="0" cellspacing="0">
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
					<th width="10%" nowrap="nowrap">Bill Nr.</th>
					<th width="12%" nowrap="nowrap">Bill Date</th>
					<th width="12%" nowrap="nowrap">Bill From</th>
					<th width="10%" nowrap="nowrap"><?= $LDEncounterNr ?></th>
					<th width="*" nowrap="nowrap">Patient Name</th>
					<th width="15%" nowrap="nowrap">Amount Due</th>
					<th width="10%" nowrap="nowrap">Status</th>
					<th width="1%">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
<?= $rows ?>
			</tbody>
		</table>
	</div>
</div>
<br />

<input type="hidden" id="page" name="page" value="<?= $current_page ?>" />
<input type="hidden" id="lastpage" name="lastpage"  value="<?= $last_page ?>" />
<input type="hidden" id="jump" name="jump">

</form>
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