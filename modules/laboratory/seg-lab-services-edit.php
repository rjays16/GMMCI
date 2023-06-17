<?php

define('ROW_MAX',15); # define here the maximum number of rows for displaying the parameters

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

$lang_tables=array('chemlab_groups.php','chemlab_params.php');
define('LANG_FILE','lab.php');
$local_user='ck_lab_user';
require_once($root_path.'include/inc_front_chain_lang.php');

$thisfile=basename(__FILE__);

//$db->debug=true;

# Create lab object
require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
$srv=new SegLab();

#require_once($root_path.'include/care_api_classes/class_department.php');
#$dept_obj=new Department;

require($root_path.'include/inc_labor_param_group.php');

# Load the date formatter */
include_once($root_path.'include/inc_date_format_functions.php');

#$dept = $dept_obj->getDepartmentInfo("name_formal like 'pathology'", "name_formal");
#$smarty->assign('breakfile','javascript:window.parent.cClick();');

$excode=$_GET['nr'];
$groupcode = $_GET['grpcode'];

#echo "code = ".$grpcode;
$_POST['service_code'] = str_replace("'","",stripslashes($_POST['service_code']));
#$_POST['excode'] = str_replace("'","",stripslashes($_POST['excode']));
#$excode = str_replace("'","",stripslashes($excode));

if(isset($_POST['excode'])) $excode=$_POST['excode'];

#added by VAN 12-07-2011
$_POST['submitted'] = $_POST['submit_val'];
#echo "isset = ".isset($_POST['submitted']);
#echo "name = ".$_POST['service_code'];
if (isset($_POST['submitted'])){
	#echo "mode = ".$mode;
	$x = array();
	#$xrow=$_POST['row'];
	#
	$xcode=$_POST['xservice_code'];
	$code=$_POST['service_code'];
	$opd_code = $_POST['service_code_opd'];
    $ipd_code = $_POST['service_code_ipd'];
	$codenum = $_POST['code_num'];
	#$xcode= str_replace("'","",$_POST['service_code']);
	$xname=$_POST['name'];
	$xcash=($_POST['cash']!=''&&isset($_POST['cash']))?$_POST['cash']:'NULL';
	$xcharge=($_POST['charge']!=''&&isset($_POST['cash']))?$_POST['charge']:'NULL';
	#$xstatus=$_POST['status'];
	$xremarks=$_POST['status'];

	if (isset($_POST['is_unavailable']))
		$xstatus = 'unavailable';
	else
		$xstatus = '';

	$with_inventory = (isset($_POST['with_inventory']))?1:0;

	$socialized = (isset($_POST['is_socialized']))?1:0;
	$room_rate = (isset($_POST['rate_changes']))?1:0;
	#echo "socialized = ".$socialized;

	#added by VAN 02-08-2011
	$in_phs = (isset($_POST['in_phs']))?1:0;

	#added by VAN 07-24-08
	$is_ER = (isset($_POST['is_ER']))?1:0;

	#added by VAN 06-02-09
	$is_package = (isset($_POST['is_package']))?1:0;
	$in_lis = (isset($_POST['in_lis']))?1:0;
	$has_param_group = (isset($_POST['has_param_group']))?1:0;

	#added by VAN 10-02-09
	$with_result = (isset($_POST['with_result']))?1:0;
	$female_only = (isset($_POST['female_only']))?1:0;
	$male_only = (isset($_POST['male_only']))?1:0;
    
    #added by VAN 12-06-2012
    $is_profile = (isset($_POST['is_profile']))?1:0;
    $is_serial = (isset($_POST['is_serial']))?1:0;
    $no_serial=$_POST['no_serial'];
    if (!$is_serial)
        $no_serial = 0;
    
	#------------------
	if($mode == 'save')
		$xgid=$_POST['groupcode'];
	else
		$xgid=$_POST['grouplist'];

	#echo "mode = ".$mode."<br>";
	#echo "xgid = ".$xgid."<br>";

	$serv_discount = array();
    
    $data = array(
                'xcode' => $xcode,
                'code' => $code, 
                'codenum' => $codenum,
                'name' => $xname, 
                'cash' => $xcash, 
                'charge' => $xcharge, 
                'remarks' => $xremarks, 
                'grp' => $xgid, 
                'is_socialized' => $socialized, 
                'is_other_rate' => $room_rate,
                'is_ER' => $is_ER, 
                'is_package' => $is_package, 
                'opd_code' => $opd_code, 
                'ipd_code' => $ipd_code, 
                'with_result' => $with_result, 
                'female_only' => $female_only, 
                'male_only' => $male_only,
                'status' => $xstatus,
                'with_inventory' => $with_inventory,
                'in_lis' => $in_lis,
                'in_phs' => $in_phs,
                'has_param_group' => $has_param_group, 
                'is_serial' => $is_serial, 
                'no_serial' => $no_serial,
                'is_profile' => $is_profile
            );

	switch($mode) {
		 case 'save':
							#edited by VAN 03-14-08
							for ($i=2; $i<=$_POST['totalrow']; $i++){
								$name = 'nameselRow'.$i;
								$price = 'dpriceRow'.$i;

								$serv_discount[$i-1]['discount'] = $_POST[$name];
								$serv_discount[$i-1]['price'] = $_POST[$price];
							}

							//added by poliam 03/27/2014
							for ($i=2; $i<=$_POST['totalrowRoom']; $i++){
								$RoomName = 'RoomnameselRow'.$i;
								$CashPrice = 'RpriceRow'.$i;
								$ChargePrice = 'RChargeRow'.$i;

								 $RoomRate[$i-1]['RoomRate'] = $_POST[$RoomName];
								 $RoomRate[$i-1]['price'] = $_POST[$CashPrice];
								 $RoomRate[$i-1]['charge'] = $_POST[$ChargePrice];
							}
							//ended by poliam 03/27/2014

							#edit by VAN 10-17-07
							# 'LB' is for LABORATORY AREA
							if ($socialized){
								$srv->deleteServiceDiscounts($xcode,'LB');
								#$srv->AddServiceDiscounts($serv_discount,$xcode,'LB');
								$srv->AddServiceDiscounts($serv_discount,$code,'LB');
							}else{
								#$srv->deleteServiceDiscounts($xcode,'LB');
								$srv->deleteServiceDiscounts($code,'LB');
							}

							//added by poliam 03/27/2014
							if ($room_rate){
								$srv->deleteRoomRate($xcode,'LB');
								$srv->AddOtherRate($RoomRate,$code,'LB');
							}else{
								$srv->deleteServiceDiscounts($code,'LB');
							}
							//ended by poliam 03/27/2014

							#if ($srv->addLabService($xcode, $xname, $xcash, $xcharge, $xstatus, $xgid, $socialized)) {
                            
							#if ($srv->addLabService($code, $codenum,$xname, $xcash, $xcharge, $xremarks, $xgid, $socialized, $is_ER, $is_package, $opd_code, $ipd_code, $with_result, $female_only, $male_only,$xstatus,$with_inventory,$in_lis,$in_phs,$has_param_group, $is_serial, $no_serial)) {
                            if ($srv->addLabService($data)) {
								#added by VAN 05-22-09
								if ($_POST["items"]!=NULL){
									$bulk_test = array();
									foreach (array_unique($_POST["items"]) as $i=>$v) {
											#------------------service_code-----
											$bulk_test[] = array($_POST["items"][$i]);
									}

									if ($is_package){
										$srv->clearGroupServiceList($code);    #clear seg_encounter_insurance table
										$srv->addGroupService($code,$bulk_test);
									}else{
										$srv->clearGroupServiceList($code);    #clear seg_encounter_insurance table
									}
								}

								if ($_POST["items_res"]!=NULL){
									$bulk_test_res = array();
									foreach (array_unique($_POST["items_res"]) as $i=>$v) {
											#------------------service_code-----
											$bulk_test_res[] = array($_POST["items_res"][$i]);
									}

									if ($has_param_group){
										$srv->clearGroupServiceList_res($code);    #clear seg_encounter_insurance table
										$srv->addGroupService_res($code,$bulk_test_res);
									}else{
										$srv->clearGroupServiceList_res($code);    #clear seg_encounter_insurance table
									}
								}

							#-----------

								echo "<br><div align=\"center\" style=\"font:bold 12px Tahoma; color:#990000; \">Laboratory Service is successfully created.</div><br />";

							}else {
								echo "<br><div align=\"center\" style=\"font:bold 12px Tahoma; color:#990000; \"><strong>Error :</strong> Laboratory Service is not successfully saved.</div><br />";
							}

							break;
		 case 'update':
							#edited by VAN 03-14-08
							#for ($i=1; $i<=$_POST['totalrow']; $i++){
							for ($i=2; $i<=$_POST['totalrow']; $i++){
								$name = 'nameselRow'.$i;
								$price = 'dpriceRow'.$i;

								$serv_discount[$i-1]['discount'] = $_POST[$name];
								$serv_discount[$i-1]['price'] = $_POST[$price];
							}

							//added by poliam 03/27/2014
							for ($i=2; $i<=$_POST['totalrowRoom']; $i++){
								$RoomName = 'RoomnameselRow'.$i;
								$CashPrice = 'RpriceRow'.$i;
								$ChargePrice = 'RChargeRow'.$i;

								 $RoomRate[$i-1]['RoomRate'] = $_POST[$RoomName];
								 $RoomRate[$i-1]['price'] = $_POST[$CashPrice];
								 $RoomRate[$i-1]['charge'] = $_POST[$ChargePrice];
							}
							//ended by poliam 03/27/2014

							#edit by VAN 10-17-07
							# 'LB' is for LABORATORY AREA
							if ($socialized){
								#$srv->deleteServiceDiscounts($xcode,'LB');
								$srv->deleteServiceDiscounts($xcode,'LB');
								#echo "del = ".$srv->sql;
								#$srv->AddServiceDiscounts($serv_discount,$xcode,'LB');
								$srv->AddServiceDiscounts($serv_discount,$code,'LB');
								#echo "add = ".$srv->sql;
							}else{
								$srv->deleteServiceDiscounts($xcode,'LB');
							}

							//added by poliam 03/27/2014
							if ($room_rate){
								$srv->deleteRoomRate($xcode,'LB');
								$srv->AddOtherRate($RoomRate,$code,'LB');
							}else{
								$srv->deleteServiceDiscounts($code,'LB');
							}
							//ended by poliam 03/27/2014

							#if ($srv->updateLabService($_POST['excode'],$xcode, $xname, $xcash, $xcharge, $xstatus, $xgid, $socialized)) {
							#if ($srv->updateLabService($xcode, $code,$codenum, $xname, $xcash, $xcharge, $xremarks, $xgid, $socialized, $is_ER, $is_package, $opd_code, $ipd_code, $with_result, $female_only, $male_only,$xstatus,$with_inventory,$in_lis, $in_phs,$has_param_group, $is_serial, $no_serial)) {
                            if ($srv->updateLabService($data)) {
								#added by VAN 05-22-09
								if ($_POST["items"]!=NULL){
									$bulk_test = array();
									foreach (array_unique($_POST["items"]) as $i=>$v) {
											#------------------service_code-----
											$bulk_test[] = array($_POST["items"][$i]);
									}

									if ($is_package){
										$srv->clearGroupServiceList($code);    #clear seg_encounter_insurance table
										$srv->addGroupService($code,$bulk_test);
									}else{
										$srv->clearGroupServiceList($code);    #clear seg_encounter_insurance table
									}
								}

								#-----------
								if ($_POST["items_res"]!=NULL){
									$bulk_test_res = array();
									foreach (array_unique($_POST["items_res"]) as $i=>$v) {
											#------------------service_code-----
											$bulk_test_res[] = array($_POST["items_res"][$i]);
									}

									if ($has_param_group){
										$srv->clearGroupServiceList_res($code);    #clear seg_encounter_insurance table
										$srv->addGroupService_res($code,$bulk_test_res);
									}else{
										$srv->clearGroupServiceList_res($code);    #clear seg_encounter_insurance table
									}
								}

								echo "<br><div align=\"center\" style=\"font:bold 12px Tahoma; color:#990000; \">Laboratory Service is successfully updated.</div><br />";
							}else {
								echo "<br><div align=\"center\" style=\"font:bold 12px Tahoma; color:#990000; \"><strong>Error :</strong> Laboratory Service is not successfully updated.</div><br />";
								#echo "<br><div align=\"center\" style=\"font:bold 12px Tahoma; color:#990000; \"><strong>Error :</strong> .$srv->sql.</div><br />";
							}

							break;
	}#end of switch statement
} #end of if statement

$sNames=array("Service Code","Service Code #", "Service Name", "Price(Cash)", "Price(Charge)","Status");
$sItems=array('service_code','code_num','name','price_cash','price_charge','status');

# Get the laboratory service values
#echo "nr, nr2 = ".$nr." - ".addslashes(urlencode($nr));

#$xcode = str_replace("'","",$xcode);

#if (empty($nr))
#	$nr = $xcode;
if (empty($excode))
	$nr = $_POST['service_code'];
else
	$nr = $excode;
#echo "nr = ".$nr;
#if($tsrv=&$srv->getLabServicesInfo("(s.service_code='".addslashes(urlencode($nr))."' OR s.service_code ='".addslashes($nr)."') AND s.group_code = sg.group_code")){
if($tsrv=&$srv->getLabServicesInfo("(s.service_code='".urlencode($nr)."' OR s.service_code ='".$nr."') AND s.group_code = sg.group_code")){
	#echo "sql = ".$srv->sql;
	$ts=$tsrv->FetchRow();
}else{
	$ts=false;
}

#------ Added by Cherry 11-13-10 -------#
$lgroup = $srv->getLabServiceGroups2();
if($lgroup){
 while($lg = $lgroup->FetchRow()){
	$listgroup[$lg['group_code']]  = $lg['name'];
 }
}
#print_r($listgroup);

#-------------- End Cherry -------------#

?>
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 3.0//EN" "html.dtd">
<?php html_rtl($lang); ?>
<HEAD>
<?php echo setCharSet(); ?>
 <TITLE>Edit Laboratory Service</TITLE>

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>

<link rel="stylesheet" href="<?= $root_path ?>js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css" />
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/ui/jquery-ui.js"></script>
 
 
<script language="javascript" name="j1">
<!--

var $J = jQuery.noConflict();

function editParam(nr)
{
	urlholder="labor_test_param_edit?sid=<?php echo "$sid&lang=$lang" ?>&nr="+encodeURIComponent(nr);
	editparam_<?php echo $sid ?>=window.open(urlholder,"editparam_<?php echo $sid ?>","width=500,height=600,menubar=no,resizable=yes,scrollbars=yes");
}

function validate_discount(){
	var dname = document.getElementById("discount_name");
	var dprice = document.getElementById("discount_price");
	var bol, cnt;

	//alert(document.getElementById('discount_table').innerHTML);

	if(dname.value==0){
		alert("Please select a discount classification.");
		dname.focus();
		bol = false;
	}else if ((isNaN(dprice.value))||(dprice.value=="")) {
		alert("Enter the discounted price.");
		dprice.focus();
		bol = false;
	}else{
		bol = true;
	}

	if((dname.value!=0)&&((!isNaN(dprice.value))||(dprice.value!=""))){
		var tbl = document.getElementById('discount_table');
		rows_len = tbl.rows.length;
		var tablecontent = document.getElementById('discount_tbody').innerHTML;
		var bol2, i;
		//alert('table = '+tablecontent);

		bol2 = tablecontent.match('No such laboratory service\'s discounts available...');
		//alert('bol = '+bol);
		//alert(tbl.innerHTML);
		//alert('rows_len1 = '+rows_len);
		if (bol2){
			document.getElementById('id0').style.display='none';
		}else{
		}

		//for(i=1; i < rows_len; i++){
		for(i=2; i < rows_len; i++){
			var disc_name = 'nameselRow'+i;
			var disc_price = 'dpriceRow'+i;
			if (document.getElementById('discount_name').value == document.getElementById(disc_name).value){
				alert("The laboratory service is already in the discount table. If you want to edit the price, just edit the price in the textbox");
				//document.getElementById('discount_name').focus();
				//added by VAN 03-17-08
				document.getElementById(disc_price).focus();
				document.getElementById("discount_name").value = 0;
				document.getElementById("discount_price").value = " ";
				bol = false;
				break;
			}
		}
	}
	return bol;
}


 function addRow(id){
	//alert('id = '+id);
	var tbl = document.getElementById(id);
	//alert(tbl.innerHTML);
	var lastRow = tbl.rows.length;
			// if there's no header row in the table, then iteration = lastRow + 1
		var iteration = lastRow;
		var row = tbl.insertRow(lastRow);
	row.id = 'drow'+iteration;
 /*
		// left cell
		var cellLeft = row.insertCell(0);
	//cellLeft.id = 'rowno' + iteration;
		var textNode = document.createTextNode(iteration);
		cellLeft.appendChild(textNode);
	 //alert(cellLeft.innerHTML);
*/

		// right cell
	//	var cellRight = row.insertCell(1);
		var cellRight = row.insertCell(0);
		var sel = document.createElement('select');
		sel.name = 'nameselRow' + iteration;
	sel.id = 'nameselRow' + iteration;

	<?php
		$discountObj = &$srv->getDiscountList("discountdesc");

		if(is_object($discountObj)){
			$i=0;
			while($result=$discountObj->FetchRow()){
	?>
				//echo "<option value=\"".$result['discountid']."\">".$result['discountdesc']." \n";
				sel.options[<?=$i?>] = new Option('<?=addslashes($result['discountdesc'])?>', '<?=addslashes($result['discountid'])?>');
	 <?
				$i=$i+1;
			}
		}
	?>

		//el.onkeypress = keyPressTest;
	sel.value = document.getElementById("discount_name").value;
		cellRight.appendChild(sel);

		// select cell
		//var cellRightSel = row.insertCell(2);
	var cellRightSel = row.insertCell(1);
		var el = document.createElement('input');
		el.type = 'text';
		el.name = 'dpriceRow' + iteration;
		el.id = 'dpriceRow' + iteration;
		el.size = 10;

	var fprice ;
	var price = document.getElementById("discount_price").value;

	if (isNaN(price))
		fprice="N/A";
	else {
		fprice=price-0;
		fprice=fprice.toFixed(2);
	}

	el.value = fprice;
	el.setAttribute("onBlur", "formatprice(this);");
	cellRightSel.appendChild(el);

	//var cellRight2 = row.insertCell(3);
	var cellRight2 = row.insertCell(2);
		var img = document.createElement('img');
		img.src = "../../gui/img/common/default/nopmuser.gif";
	//img.id = 'delbuttonRow' + iteration;
	img.id = iteration;
	img.name = iteration;
	img.setAttribute("alt", "Delete Discount");
	img.setAttribute("onClick", "removeDiscount(this);");
	//img.onclick = removeDiscount;
	img.setAttribute("style", "cursor:pointer");
	cellRight2.appendChild(img);

	// reset value
	document.getElementById("discount_name").value = 0;
	document.getElementById("discount_price").value = " ";
	document.getElementById("totalrow").value = document.getElementById('discount_table').rows.length-1;
 }

 //added by poliam 03/26/2014
function addRowRoomType(id){
	var tbl = document.getElementById(id);
	var lastRow = tbl.rows.length;
			// if there's no header row in the table, then iteration = lastRow + 1
	var iteration = lastRow;
	var row = tbl.insertRow(lastRow);
	row.id = 'drow'+iteration;
 
		var cellRight = row.insertCell(0);
		var sel = document.createElement('select');
		sel.name = 'RoomnameselRow' + iteration;
	sel.id = 'RoomnameselRow' + iteration;

	<?php
		$discountObj = &$srv->GetRoomType();
		if(is_object($discountObj)){
			$i=0;
			while($result=$discountObj->FetchRow()){
	?>
				//echo "<option value=\"".$result['discountid']."\">".$result['discountdesc']." \n";
				sel.options[<?=$i?>] = new Option('<?=addslashes($result['name'])?>', '<?=addslashes($result['nr'])?>');
	 <?
				$i=$i+1;
			}
		}
	?>

		//el.onkeypress = keyPressTest;
	sel.value = document.getElementById("RoomTypeName").value;
		cellRight.appendChild(sel);

		// select cell
		//var cellRightSel = row.insertCell(2);
	var cellRightSel = row.insertCell(1);
		var el = document.createElement('input');
		el.type = 'text';
		el.name = 'RpriceRow' + iteration;
		el.id = 'RpriceRow' + iteration;
		el.size = 10;

	var fprice ;
	var price = document.getElementById("RoomTypePrice").value;

	if (isNaN(price))
		fprice="N/A";
	else {
		fprice=price-0;
		fprice=fprice.toFixed(2);
	}

	el.value = fprice;
	el.setAttribute("onBlur", "formatprice(this);");
	cellRightSel.appendChild(el);

	var cellRightSel = row.insertCell(2);
		var el = document.createElement('input');
		el.type = 'text';
		el.name = 'RChargeRow' + iteration;
		el.id = 'RChargeRow' + iteration;
		el.size = 10;

	var fprice ;
	var price = document.getElementById("RoomTypeCharge").value;

	if (isNaN(price))
		fprice="N/A";
	else {
		fprice=price-0;
		fprice=fprice.toFixed(2);
	}

	el.value = fprice;
	el.setAttribute("onBlur", "formatprice(this);");
	cellRightSel.appendChild(el);

	var cellRight2 = row.insertCell(3);
		var img = document.createElement('img');
		img.src = "../../gui/img/common/default/nopmuser.gif";
	//img.id = 'delbuttonRow' + iteration;
	img.id = iteration;
	img.name = iteration;
	img.setAttribute("alt", "Delete Discount");
	img.setAttribute("onClick", "RomoveRoomType(this);");
	//img.onclick = removeDiscount;
	img.setAttribute("style", "cursor:pointer");
	cellRight2.appendChild(img);

	// reset value
	document.getElementById("RoomTypeName").value = 0;
	document.getElementById("RoomTypePrice").value = " ";
	document.getElementById("RoomTypeCharge").value = " ";
	document.getElementById("totalrowRoom").value = document.getElementById('RoomTypeTable').rows.length-1;

 }
function RomoveRoomType(obj){
	 var tbl = document.getElementById('RoomTypeTable');
	 var objId = obj.id;
	 var rowname = 'drow'+objId;
	
	 index = parseInt(rowname.substr(4,1));
	 
	 tbl.deleteRow(document.getElementById(rowname).rowIndex);

	 rows_len = tbl.rows.length;
	
	 document.getElementById("totalrowRoom").value = rows_len - 1;

	 if (document.getElementById('discount_tbody').rows.length==2){
		document.getElementById('discount_tbody').innerHTML = '<tr>'+
																	'<th> Discount Classification<br /> </th>'+
																	'<th> Discounted Price<br /> </th>'+
																	'<th></th>'+
																'</tr>'+
																'<tr id="id0">'+
																	'<td colspan="4">No such laboratory service\'s discounts available...</td>'+
																'</tr>';
	 }
	 //alert('index = '+index+" < "+rows_len);
	 for (i=index; i< rows_len; i++){
		 //var cnt = i+1;
		//alert('i = '+i);
		var cnt = i+1;
		var ncnt = i;
		var discount_id = 'nameselRow'+cnt;
		var price = 'RpriceRow'+cnt;
		var img = cnt;

		//document.getElementById('discount_table').rows[i].id = 'drow'+ncnt;
		tbl.rows[i].id = 'drow'+ncnt;
		document.getElementById(discount_id).id = 'nameselRow'+ncnt;

		var discount_id_new = 'nameselRow'+ncnt;
		document.getElementById(discount_id_new).name = 'nameselRow'+ncnt;

		document.getElementById(price).id = 'RpriceRow'+ncnt;

		var price_new = 'RpriceRow'+ncnt;
		document.getElementById(price_new).name = 'RpriceRow'+ncnt;

		document.getElementById(img).id = ncnt;
	 }

//	 alert(tbl.innerHTML);
}

function validate_Room_Type(){
	var dname = document.getElementById("RoomTypeName");
	var dprice = document.getElementById("RoomTypePrice");
	var bol, cnt;

	if(dname.value==0){
		alert("Please select a Room Type.");
		dname.focus();
		bol = false;
	}else if ((isNaN(dprice.value))||(dprice.value=="")) {
		alert("Enter the Room Type price.");
		dprice.focus();
		bol = false;
	}else{
		bol = true;
	}
	return bol;
}

 //ended by poliam 03/26/2014

 function formatprice(obj){
	var rowname = obj.id;
	var fprice ;
	var price = document.getElementById(rowname).value;

	pprice = price.replace(",","");
	//alert("pprice = "+pprice);
	if (isNaN(pprice))
		fprice="N/A";
	else {
		fprice=pprice-0;
		fprice=fprice.toFixed(2);
	}

	document.getElementById(rowname).value = fprice;
}


function trimString(objct){
	objct.value = objct.value.replace(/^\s+|\s+$/g,"");
	objct.value = objct.value.replace(/\s+/g,"");
	objct.value = objct.value.replace(/,/,"");
}/* end of function trimString */


function removeDiscount(obj){
	 var tbl = document.getElementById('discount_table');
	 var objId = obj.id;
	 var rowname = 'drow'+objId;
	 //alert(objId+" - "+rowname);
	 //alert(tbl.innerHTML);
	 index = parseInt(rowname.substr(4,1));
	 //alert("index = "+index);
	 //alert('rowtotal = '+document.getElementById("totalrow").value);
	 //alert('tbl.rows.length = '+tbl.rows.length);
	 //alert('tbl.rows.length = '+tbl.rows.length);
	 tbl.deleteRow(document.getElementById(rowname).rowIndex);

	 //rows_len = document.getElementById('discount_table').rows.length;
	 //edited by VAN 03-14-08
	 //rows_len = tbl.rows.length;
	 rows_len = tbl.rows.length;
	 //alert('rows_len = '+rows_len);
	 document.getElementById("totalrow").value = rows_len - 1;

	 //alert('rowtotal2 = '+document.getElementById("totalrow").value);
	 //alert('rows_len2 = '+rows_len);
	 //alert(tbl.innerHTML);
	 //alert('len = '+document.getElementById('discount_tbody').rows.length);
	 //alert('inner = '+document.getElementById('discount_tbody').innerHTML);

	 if (document.getElementById('discount_tbody').rows.length==2){
		document.getElementById('discount_tbody').innerHTML = '<tr>'+
																					'<th> Discount Classification<br /> </th>'+
																							'<th> Discounted Price<br /> </th>'+
																					'<th></th>'+
																				'</tr>'+
																				'<tr id="id0">'+
																					'<td colspan="3">No such laboratory service\'s discounts available...</td>'+
																				'</tr>';
	 }
	 //alert('index = '+index+" < "+rows_len);
	 for (i=index; i< rows_len; i++){
		 //var cnt = i+1;
		//alert('i = '+i);
		var cnt = i+1;
		var ncnt = i;
		var discount_id = 'nameselRow'+cnt;
		var price = 'dpriceRow'+cnt;
		var img = cnt;

		//document.getElementById('discount_table').rows[i].id = 'drow'+ncnt;
		tbl.rows[i].id = 'drow'+ncnt;
		document.getElementById(discount_id).id = 'nameselRow'+ncnt;

		var discount_id_new = 'nameselRow'+ncnt;
		document.getElementById(discount_id_new).name = 'nameselRow'+ncnt;

		document.getElementById(price).id = 'dpriceRow'+ncnt;

		var price_new = 'dpriceRow'+ncnt;
		document.getElementById(price_new).name = 'dpriceRow'+ncnt;

		document.getElementById(img).id = ncnt;
	 }

//	 alert(tbl.innerHTML);
}

function preLoad(){
	var mode = '<?=$mode;?>';
	document.getElementById("totalrow").value = document.getElementById('discount_table').rows.length-1;
	document.getElementById("totalrowRoom").value = document.getElementById('RoomTypeTable').rows.length-1;
	if(mode == 'save'){
		document.getElementById("grouplist").disabled = true;
	}else{
		document.getElementById("grouplist").disabled = false;
	}
}

function social_status(){

	if (document.getElementById('is_socialized').checked)
		//alert("socialized service");
		document.getElementById('social_service').style.display = '';
	else
		//alert("non-socialized service");
		document.getElementById('social_service').style.display = 'none';

}
//added by poliam 03/26/2014
function rate_status(){
	if (document.getElementById('rate_changes').checked)
		document.getElementById('rate_other').style.display = '';
	else
		document.getElementById('rate_other').style.display = 'none';
}
//ended by poliam 03/26/2014
//added by VAN 06-02-09
var trayItems = 0;
function appendOrder(list,details,is_param) {

		if (list) {
				var dBody=list.getElementsByTagName("tbody")[0];
				var add_label = '';
				if (is_param==1){
					add_label = '_res';
				}
				if (dBody) {
						var src;
						var lastRowNum = null,
										items = document.getElementsByName('items'+add_label+'[]');
										dRows = dBody.getElementsByTagName("tr");

						if (details) {
								var id = details.id;
								if (items) {

										for (var i=0;i<items.length;i++) {
												if (items[i].value == details.id) {
														var itemRow = $('row'+add_label+''+items[i].value);
														document.getElementById('name'+add_label+''+id).innnerHTML = details.name;
														document.getElementById('id'+add_label+''+id).innerHTML = details.id;
														document.getElementById('cash'+add_label+''+id).innerHTML = details.prcCash;
														document.getElementById('charge'+add_label+''+id).innerHTML = details.prcCharge;

														var name_serv = details.name;
														alert('"'+name_serv.toUpperCase()+'" is already in the list & has been UPDATED!');
														return true;
												}
										}
										if (items.length == 0)
												 clearOrder(list);
								}

								alt = (dRows.length%2)+1;
								//alert(details.accre_nr);
								src =
										'<tr class="wardlistrow'+alt+'" id="row'+add_label+''+id+'">' +
												'<input type="hidden" name="items'+add_label+'[]" id="rowID'+add_label+''+id+'" value="'+id+'" />'+
												'<td width="*" id="name'+add_label+''+id+'">'+details.name+'</td>'+
												'<td width="10%" align="left" id="id'+add_label+''+id+'">'+details.id+'</td>'+
												'<td width="15%" align="right" id="cash'+add_label+''+id+'">'+details.prcCash+'</td>'+
												'<td width="15%" align="right" id="charge'+add_label+''+id+'">'+details.prcCharge+'</td>'+
												'<td class="centerAlign" width="5%"><a href="javascript:removeItem(\''+id+'\',\''+is_param+'\')"><img src="../../images/btn_delitem.gif" border="0"/></a></td>'+
										'</tr>';
																//'<td width="1">'+id+'</td>'+
								//alert(src);
								trayItems++;
						}
						else {
								src = "<tr><td colspan=\"6\">No such laboratory service exists......</td></tr>";
						}

						dBody.innerHTML += src;

						return true;
				}
		}
		return false;
}

function reclassRows(list,startIndex) {
		if (list) {
				var dBody=list.getElementsByTagName("tbody")[0];
				if (dBody) {
						var dRows = dBody.getElementsByTagName("tr");
						if (dRows) {
								for (i=startIndex;i<dRows.length;i++) {
										dRows[i].className = "wardlistrow"+(i%2+1);
								}
						}
				}
		}
}

function clearOrder(list) {
		if (list) {
				var dBody=list.getElementsByTagName("tbody")[0];
				if (dBody) {
						trayItems = 0;
						dBody.innerHTML = "";
						return true;
				}
		}
		return false;
}

function removeItem(id,is_param) {
		var destTable, destRows;
		if (is_param==1){
			table_name = 'order-list_res';
			add_label = '_res';
		}else{
			table_name = 'order-list';
			add_label = '';
		}

		var table = $(table_name);
		var rmvRow=document.getElementById('row'+add_label+''+id);
		if (table && rmvRow) {
				$('rowID'+add_label+''+id).parentNode.removeChild($('rowID'+add_label+''+id));
				var rndx = rmvRow.rowIndex;
				table.deleteRow(rmvRow.rowIndex);
				reclassRows(table,rndx);
		}
		var items = document.getElementsByName('items'+add_label+'[]');
		if (items.length == 0){
				emptyIntialRequestList(is_param);
		}
}

function emptyIntialRequestList(is_param){
		if (is_param==1){
			table_name = 'order-list_res';
			add_label = '_res';
		}else{
			table_name = 'order-list';
			add_label = '';
		}

		clearOrder($(table_name));
		appendOrder($(table_name),null);
}

function emptyTray(is_param) {
		if (is_param==1){
			table_name = 'order-list_res';
			add_label = '_res';
		}else{
			table_name = 'order-list';
			add_label = '';
		}

		clearOrder($(table_name));
		appendOrder($(table_name),null);
		refreshDiscount();
}

function Enable_package(){
		if (document.getElementById('is_package').checked){
				document.getElementById('package_row').style.display = '';
				//document.getElementById('is_package').disabled = 0;
				//document.getElementById('has_param_group').disabled = 1;
		}else{
				document.getElementById('package_row').style.display = 'none';
				//document.getElementById('is_package').disabled = 1;
				//document.getElementById('has_param_group').disabled = 0;
		}
		preEnable();
}

function Enable_param_group(){
		if (document.getElementById('has_param_group').checked){
				document.getElementById('param_group_row').style.display = '';
				//document.getElementById('is_package').disabled = 1;
				//document.getElementById('has_param_group').disabled = 0;
		}else{
				document.getElementById('param_group_row').style.display = 'none';
				//document.getElementById('is_package').disabled = 0;
				//document.getElementById('has_param_group').disabled = 1;

		}
		preEnable();
}

function preEnable(){
	if ((document.getElementById('has_param_group').checked)&&(document.getElementById('is_package').checked==0)){
		document.getElementById('is_package').disabled = 1;
		document.getElementById('has_param_group').disabled = 0;
	}else if ((document.getElementById('has_param_group').checked==0)&&(document.getElementById('is_package').checked)){
		document.getElementById('is_package').disabled = 0;
		document.getElementById('has_param_group').disabled = 1;
	}else if ((document.getElementById('has_param_group').checked==0)&&(document.getElementById('is_package').checked==0)){
		document.getElementById('is_package').disabled = 0;
		document.getElementById('has_param_group').disabled = 0;
	}
}
//--------------------

//added by VAN
//number only and decimal point is allowed
function keyPressHandler(e){
	var unicode=e.charCode? e.charCode : e.keyCode
	if (unicode>31 && (unicode<46 || unicode == 47 ||unicode>57)) //if not a number
	//if (unicode>31 && (unicode<48 || unicode>57)) //if not a number
		return false //disable key press
}

function keyPressHandler2(e){
	var unicode=e.charCode? e.charCode : e.keyCode
	//if (unicode>31 && (unicode<46 || unicode == 47 ||unicode>57)) //if not a number
	if (unicode>31 && (unicode<48 || unicode>57)) //if not a number
		return false //disable key press
}

function checkForm(d){
	if (d.service_code.value==""){
		alert('Pls. type the code of the service for inpatient.');
		d.service_code.focus();
		return false;
	}else if (d.service_code_opd.value==""){
		alert('Pls. type the code of the service for outpatient.');
		d.service_code_opd.focus();
		return false;
	}else if (d.code_num.value==""){
		alert('Pls. type the number code of the service.');
		d.code_num.focus();
		return false;
	}else if (d.name.value==""){
		alert('Pls. type the name of the service.');
		d.name.focus();
		return false;
	}else if (d.cash.value==""){
		alert('Pls type the cash price of the service.');
		d.cash.focus();
		return false;
	}else if (d.charge.value==""){
		alert('Pls type the charge price of the service.');
		d.charge.focus();
		return false;
	}
	return true;
}

//added by VAN 12-06-2012
function visibleSerialInfo(){
   if ($J('#is_serial').attr('checked')){
       //$J('#serial_row').attr('style.display', '');    
       $('serial_row').style.display = "";
   }else{
       //$J('#serial_row').attr('style.display', 'none');    
       $('serial_row').style.display = "none";
   } 
}
//---------------------
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

<!---------added by VAN----------->
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

<script type="text/javascript">
<!--
OLpageDefaults(BGCLASS,'olbg', CGCLASS,'olcg', FGCLASS,'olfg',
 CAPTIONFONTCLASS,'olcap', CLOSEFONTCLASS,'olclo', TEXTFONTCLASS,'oltxt');
//-->
</script>

<style type="text/css">
<!--
.olbg {
		background-image:url("<?= $root_path ?>images/bar_05.gif");
		background-color:#0000ff;
		border:1px solid #4d4d4d;
}
.olcg {
		background-color:#aa00aa;
		background-image:url("<?= $root_path ?>images/bar_05.gif");
		text-align:center;
}
.olcgif {background-color:#333399; text-align:center;}
.olfg {
		background-color:#ffffcc;
		text-align:center;
}
.olfgif {background-color:#bbddff; text-align:center;}
.olcap {
		font-family:Arial; font-size:13px;
		font-weight:bold;
		color:#708088;
}
a.olclo {font-family:Verdana; font-size:11px; font-weight:bold; color:#ddddff;}
a.olclo:hover {color:#ffffff;}
.oltxt {font-family:Arial; font-size:12px; color:#000000;}

a {color:#338855;font-weight:bold;}
a:hover {color:#FF00FF;}
.text12 {font-family:Verdana,Arial,sans-serif; font-size:12px;}
.text14 {font-family:Verdana,Arial,sans-serif; font-size:14px;}
.text16 {font-family:Verdana,Arial,sans-serif; font-size:16px;}
.text18 {font-family:Verdana,Arial,sans-serif; font-size:18px;}

.myHeader {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:22px;}
.mySubHead {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:18px;}
.mySpacer {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:4px;}
.myText {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:13px;color:#000000;}
.snip {font-family:Verdana,Arial,Helvetica;font-size:10px;}
.purple14 {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:14px;color:purple;
 font-weight:bold;}
.purple18 {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:18px;color:purple;
 font-weight:bold;font-style:italic;}
.yellow {color:#ffff00;}
.red {color:#cc0000;}
.blue {color:#0000cc;}
-->
</style>

</HEAD>

<BODY topmargin=0 leftmargin=0 marginwidth=0 marginheight=0
<?php

/*if($newid) echo ' onLoad="document.datain.test_date.focus();" ';*/
 if (!$cfg['dhtml']){ echo 'link='.$cfg['body_txtcolor'].' alink='.$cfg['body_alink'].' vlink='.$cfg['body_txtcolor']; }
 ?> onLoad="preLoad();social_status();Enable_package();Enable_param_group();preEnable();visibleSerialInfo();rate_status();">

<table width=100% border=0 cellspacing=0 cellpadding=0>

	<tr>
		<td bgcolor="<?php echo $cfg['top_bgcolor']; ?>" >
			<FONT  COLOR="<?php echo $cfg['top_txtcolor']; ?>"  SIZE=+2  FACE="Arial"><STRONG> &nbsp;
			<?php
				echo $ts['name'];
			 ?>
			 </STRONG></FONT>
		</td>
		<!--<td bgcolor="<?php echo $cfg['top_bgcolor']; ?>" height="10" align=right ><nobr><a href="javascript:gethelp('lab_param_edit.php')"><img <?php echo createLDImgSrc($root_path,'hilfe-r.gif','0') ?>  <?php if($cfg['dhtml'])echo'style=filter:alpha(opacity=70) onMouseover=hilite(this,1) onMouseOut=hilite(this,0)>';?></a><a href="javascript:window.parent.cClick();" ><img <?php echo createLDImgSrc($root_path,'close2.gif','0') ?>  <?php if($cfg['dhtml'])echo'style=filter:alpha(opacity=70) onMouseover=hilite(this,1) onMouseOut=hilite(this,0)>';?></a></nobr></td>-->
		<!--<td bgcolor="<?php echo $cfg['top_bgcolor']; ?>" height="10" align=right ><nobr><a href="javascript:gethelp('lab_param_edit.php')"><img <?php echo createLDImgSrc($root_path,'hilfe-r.gif','0') ?>  <?php if($cfg['dhtml'])echo'style=filter:alpha(opacity=70) onMouseover=hilite(this,1) onMouseOut=hilite(this,0)>';?></a></nobr></td>-->
	</tr>
	<tr align="center">
		<td  bgcolor=#dde1ec colspan=2>

			<FONT    SIZE=-1  FACE="Arial">

		<form action="<?php echo $thisfile; ?>" method="post" name="paramedit" id="paramedit" onSubmit="return checkForm(this);">
			<table border=0 bgcolor=#ffdddd cellspacing=1 cellpadding=1 width="100%">
				<tr>
					<td  bgcolor=#ff0000 colspan=2><FONT SIZE=2  FACE="Verdana,Arial" color="#ffffff">
						<b>
						<?php
						#edited by VAN 03-14-08
						#echo $ts['grpname']; #echo $parametergruppe[$ts['group_id']];
						if($ts)
							echo $ts['grpname']; #echo $parametergruppe[$ts['group_id']];
						else{
							$groupInfo = $srv->getAllLabGroupInfo($groupcode);
							echo strtoupper($groupInfo['name']);
						}
						?>
						</b>
					</td>
				</tr>
				<tr>
					<td  colspan=2>

						<!--<form action="<?php echo $thisfile; ?>" method="post" name="paramedit" id="paramedit">-->

								<table border="0" cellpadding=2 cellspacing=1>

								<?php

									$toggle=0;

									#commented by VAN 03-14-08
									#if($ts){


								?>
									<tr>
										<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px" width="20%">Group Code</td>
										<td bgcolor="#ffffee" class="a12_b">
										<select name="grouplist" id="grouplist">
											<?
												if(empty($xgid)){
													$check = $groupcode;
												}else{
													$check = $xgid;
												}
                                                
                                                if ($ts['group_code'])
                                                    $check = $ts['group_code'];

												$listgroup = array_unique($listgroup);

											/*if (empty($medcertDriverInfo['exam_physician']))
													$medcertDriverInfo['exam_physician'] = 0;   */

												foreach($listgroup as $key=>$value){

													#echo "key = ".$key;
													#echo "<br>val = ".$value;
													if ($check==$key){
														echo "				<option value='".$key."' selected=\"selected\">".$value."</option> \n";
													}else{
														echo "				<option value='".$key."'>".$value."</option> \n";
													}
												}

											?>
										</select>

										</td>
									</tr>
									<tr>
										<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px" width="20%">Service Code</td>
										<td bgcolor="#ffffee" class="a12_b">
											<!--<input type="text" name="service_code" id="service_code" size=35 maxlength=10 style="width:100%" <?=($ts)?'readonly':''?> value="<?= $ts['service_code'] ?>">-->
											<input type="hidden" name="xservice_code" id="xservice_code" value="<?= $ts['service_code'] ?>">
											<input type="text" name="service_code" id="service_code" size=35 maxlength=6 style="width:100%" <?=($ts['service_code']?'readonly':'')?> value="<?= $ts['service_code'] ?>">
										</td>
									</tr>
                                    <tr>
                                        <td class="a12_b" bgcolor="#fefefe" style="padding-left:4px" width="20%">Service Code (IPD)</td>
                                        <td bgcolor="#ffffee" class="a12_b">
                                            <input type="text" name="service_code_ipd" id="service_code_ipd" size=35 maxlength=6 style="width:100%" value="<?= $ts['ipdservice_code'] ?>">
                                        </td>
                                    </tr>
									<tr>
										<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px" width="20%">Service Code (OPD)</td>
										<td bgcolor="#ffffee" class="a12_b">
											<input type="text" name="service_code_opd" id="service_code_opd" size=35 maxlength=6 style="width:100%" value="<?= $ts['oservice_code'] ?>">
										</td>
									</tr>
									<tr>
										<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px" width="20%">Service Code #</td>
										<td bgcolor="#ffffee" class="a12_b">
											<!--<input type="text" name="code_num" id="code_num" size=35 maxlength=10 style="width:100%" <?=($ts)?'readonly':''?> value="<?= $ts['service_code'] ?>">-->

											<input type="text" name="code_num" id="code_num" size=35 maxlength=4  onkeypress="return keyPressHandler2(event);" style="width:100%" value="<?= $ts['code_num']?$ts['code_num']:0 ?>">
										</td>
									</tr>
									<tr>
										<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">Name</td>
										<td bgcolor="#efefef" class="a12_b">
											<input type="text" name="name" id="name" size=35 maxlength="25" style="width:100%" value="<?= $ts['name'] ?>">
										</td>
									</tr>
									<tr>
										<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">Price(Cash)</td>
										<td bgcolor="#ffffee" class="a12_b">
											<input type="text" name="cash" id="cash" size=35 maxlength=30 style="width:100%" onBlur="formatprice(this);" onKeyPress="return keyPressHandler(event);" value="<?= number_format($ts['price_cash'],2,".","") ?>">
											<!--<input type="text" name="cash" id="cash" size=35 maxlength=30 style="width:100%" value="<?= $ts['price_cash'] ?>">-->
										</td>
									</tr>
									<tr>
										<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">Price(Charge)</td>
										<td bgcolor="#efefef" class="a12_b">
											<input type="text" name="charge" id="charge" size=35 maxlength=30 style="width:100%" onBlur="formatprice(this);" onKeyPress="return keyPressHandler(event);" value="<?= number_format($ts['price_charge'] ,2,".","")?>">
											<!--<input type="text" name="charge" id="charge" size=35 maxlength=30 style="width:100%" value="<?= $ts['price_charge'] ?>">-->
										</td>
									</tr>

									<!-- added by VAN 05-14-2010 -->
									<tr>
										<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">
											Is it temporarily unavailable?&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										</td>
										<?php
												if ($ts['status']=='unavailable')
														$is_unavailable = 1;
												else
														$is_unavailable = 0;
										?>
										<td bgcolor="#efefef" class="a12_b">
											<input type="checkbox" name="is_unavailable" id="is_unavailable" value="1" <?=($is_unavailable==1)?'checked="checked" ':''?>>
										</td>
									</tr>
									<!-- -->
                                    <tr>
                                        <td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">
                                            Is it a Profile Test (different from package test)?&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        </td>
                                        <td bgcolor="#efefef" class="a12_b">
                                            <input type="checkbox" name="is_profile" id="is_profile" value="1" <?=($ts['is_profile']==1)?'checked="checked" ':''?>>
                                        </td>
                                    </tr>
									<tr>
										<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">
												Is it a package?&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										</td>
										<td bgcolor="#efefef" class="a12_b">
												<input type="checkbox" name="is_package" id="is_package" value="1" onClick="Enable_package();" <?=($ts['is_package']==1)?'checked="checked" ':''?>>
										</td>
									</tr>

									<tr style="display:none" id="package_row">
										<td colspan="2" class="a12_b" bgcolor="#fefefe" style="padding-left:4px">
											<table width="100%" border="0">
												<tr class="adm_item">
													<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px" width="19%">&nbsp;</td>
													<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px" colspan=3 width="*" align="right">
																<a href="javascript:void(0);"
																		onclick="return overlib(
																		OLiframeContent('<?= $root_path ?>modules/laboratory/seg-service-tray.php?grpcode='+$('groupcode').value, 600, 400, 'fOrderTray', 1, 'auto'),
																		WIDTH,600, TEXTPADDING,0, BORDER,0,
																		STICKY, SCROLL, CLOSECLICK, MODAL,
																		CLOSETEXT, '<img src=<?= $root_path ?>images/close.gif border=0 >',
																		CAPTIONPADDING,4,
																		CAPTION,'Add lab test tray',
																		MIDX,0, MIDY,0,
																		STATUS,'Add lab test tray');"
																		onmouseout="nd();">
																		<img name="btnitem" id="btnitem" src="<?= $root_path ?>images/btn_additems.gif" border="0"></a>
													</td>
												</tr>
												<tr class="a12_b" bgcolor="#fefefe" style="padding-left:4px">
													<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px" width="19%">Lab Test Included : </td>
													<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px" width="*">

														<table id="order-list" class="segList" border="0" cellpadding="0" cellspacing="0" width="100%">
															<thead>
																	<tr id="order-list-header">
																			<th width="*" align="left">&nbsp;Name/Description</th>
																			<th width="10%" align="left">&nbsp;&nbsp;Code</th>
																			<th style="font-size:11px" width="15%" align="center">&nbsp;&nbsp;Cash</th>
																			<th style="font-size:11px" width="15%" align="center">&nbsp;&nbsp;Charge</th>
																			<th class="centerAlign" width="5%"></th>
																	</tr>
															<thead>
														<tbody>
														<!--<tr><td colspan=6>No such laboratory service exists...</td></tr>-->
															<?php

																		$result = $srv->get_LabServiceGroupPackage($ts['service_code']);
																		$count =  $srv->count;
																		#echo "c = ".$count;
																		if ($count==0){
																			echo '<tr><td colspan=6>No such laboratory service exists...</td></tr>';
																		}else{
																			$rows=array();
																			while ($row=$result->FetchRow()) {
																				$rows[] = $row;
																			}
																			foreach ($rows as $i=>$row) {
																				if ($row) {
																					$count++;
																					$alt = ($count%2)+1;

																					$src .= '
																											<tr class="wardlistrow'.$alt.'" id="row'.$row['service_code_child'].'">
																													<input type="hidden" name="items[]" id="rowID'.$row['service_code_child'].'" value="'.$row['service_code_child'].'" />
																													<td width="*" id="name'.$row['service_code_child'].'">'.$row['child_name'].'</td>
																													<td width="10%" align="left" id="id'.$row['service_code_child'].'">'.$row['service_code_child'].'</td>
																													<td width="15%" align="right" id="cash'.$row['service_code_child'].'">'.$row['child_cash'].'</td>
																													<td width="15%" align="right" id="charge'.$row['service_code_child'].'">'.$row['child_charge'].'</td>
																													<td class="centerAlign" width="5%"><a href="javascript:removeItem(\''.$row['service_code_child'].'\',0)"><img src="../../images/btn_delitem.gif" border="0"/></a></td>
																											</tr>
																										';
																					}
																				}
																				echo $src;

																		}
														?>

													</tbody>
												</table>
											</td>

										</tr>
											</table>
										</td>
									</tr>

									<tr>
										<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">
											Has a parameter group for result (1 Test Only)?&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										</td>
										<td bgcolor="#efefef" class="a12_b">
											<input type="checkbox" name="has_param_group" id="has_param_group" value="1" onClick="Enable_param_group();" <?=($ts['has_param_group']==1)?'checked="checked" ':''?>>
										</td>
									</tr>
									<tr style="display:none" id="param_group_row">
										<td colspan="2" class="a12_b" bgcolor="#fefefe" style="padding-left:4px">
											<table width="100%" border="0">
												<tr class="adm_item">
													<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px" width="19%">&nbsp;</td>
													<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px" colspan=3 width="*" align="right">
																<a href="javascript:void(0);"
																		onclick="return overlib(
																		OLiframeContent('<?= $root_path ?>modules/laboratory/seg-service-tray.php?is_param=1&grpcode='+$('groupcode').value, 600, 400, 'fOrderTray', 1, 'auto'),
																		WIDTH,600, TEXTPADDING,0, BORDER,0,
																		STICKY, SCROLL, CLOSECLICK, MODAL,
																		CLOSETEXT, '<img src=<?= $root_path ?>images/close.gif border=0 >',
																		CAPTIONPADDING,4,
																		CAPTION,'Add lab test tray',
																		MIDX,0, MIDY,0,
																		STATUS,'Add lab test tray');"
																		onmouseout="nd();">
																		<img name="btnitem2" id="btnitem2" src="<?= $root_path ?>images/btn_additems.gif" border="0"></a>
													</td>
												</tr>
												<tr class="a12_b" bgcolor="#fefefe" style="padding-left:4px">
													<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px" width="19%">Lab Test Included : </td>
													<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px" width="*">

														<table id="order-list_res" class="segList" border="0" cellpadding="0" cellspacing="0" width="100%">
															<thead>
																	<tr id="order-list-header_res">
																			<th width="*" align="left">&nbsp;Name/Description</th>
																			<th width="10%" align="left">&nbsp;&nbsp;Code</th>
																			<th style="font-size:11px" width="15%" align="center">&nbsp;&nbsp;Cash</th>
																			<th style="font-size:11px" width="15%" align="center">&nbsp;&nbsp;Charge</th>
																			<th class="centerAlign" width="5%"></th>
																	</tr>
															<thead>
														<tbody>
														<!--<tr><td colspan=6>No such laboratory service exists...</td></tr>-->
															<?php

																		$result2 = $srv->get_LabServiceGroupPackage_Result($ts['service_code']);
																		$count =  $srv->count;
																		#echo "c = ".$count;
																		if ($count==0){
																			echo '<tr><td colspan=6>No such laboratory service exists...</td></tr>';
																		}else{
																			$rows2=array();
																			while ($row2=$result2->FetchRow()) {
																				$rows2[] = $row2;
																			}
																			foreach ($rows2 as $i=>$row2) {
																				if ($row2) {
																					$count++;
																					$alt = ($count%2)+1;

																					$src .= '
																											<tr class="wardlistrow'.$alt.'" id="row_res'.$row2['service_code_child'].'">
																													<input type="hidden" name="items_res[]" id="rowID_res'.$row2['service_code_child'].'" value="'.$row2['service_code_child'].'" />
																													<td width="*" id="name'.$row2['service_code_child'].'">'.$row2['child_name'].'</td>
																													<td width="10%" align="right" id="id'.$row2['service_code_child'].'">'.$row2['service_code_child'].'</td>
																													<td width="15%" align="right" id="cash'.$row['service_code_child'].'">'.$row['child_cash'].'</td>
																													<td width="15%" align="right" id="charge'.$row['service_code_child'].'">'.$row['child_charge'].'</td>
																													<td class="centerAlign" width="5%"><a href="javascript:removeItem(\''.$row2['service_code_child'].'\',1)"><img src="../../images/btn_delitem.gif" border="0"/></a></td>
																											</tr>
																										';
																					}
																				}
																				echo $src;

																		}
														?>

													</tbody>
												</table>
											</td>

										</tr>
											</table>
										</td>
									</tr>
                                    
                                    <!-- added by VAN 12-06-2012-->
                                    <tr>
                                        <td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">
                                            Is it a SERIAL Test?&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        </td>
                                        <td bgcolor="#efefef" class="a12_b">
                                            <input type="checkbox" name="is_serial" id="is_serial" onchange="visibleSerialInfo()" value="1" <?=($ts['is_serial']==1)?'checked="checked" ':''?>>
                                        </td>
                                    </tr>
                                    <tr id="serial_row" style="display: none;">
                                        <td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">
                                            Number of Takes&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        </td>
                                        <td bgcolor="#efefef" class="a12_b">
                                            <select name="no_serial" id="no_serial" style="text-align:left;">
                                                <!--<option value="0">-Select-</option>-->
                                                <?php 
                                                  $max = 10;  
                                                  #$ts['no_serial'] = 4;
                                                  for ($i=2;$i<=$max;$i++){
                                                      if ($ts['no_serial']==$i)
                                                        $selected = "selected";
                                                      else
                                                        $selected = "";
                                                ?>
                                                    <option value="<?=$i?>" <?=$selected?>><?=$i?></option>
                                                <?php 
                                                  }
                                                ?>
                                            </select>
                                        </td>
                                    </tr>


									<!-- added by VAN 07-24-08 -->
									<tr>
										<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">
											Is it included in the limited service for ER?&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										</td>
										<td bgcolor="#efefef" class="a12_b">
											<input type="checkbox" name="is_ER" id="is_ER" value="1" <?=($ts['is_ER']==1)?'checked="checked" ':''?>>
										</td>
									</tr>
									<!--added by VAN 10-02-09 -->
									<tr>
										<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">
											Is it applicable for female?&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										</td>
										<td bgcolor="#efefef" class="a12_b">
											<input type="checkbox" name="female_only" id="female_only" value="1" <?=($ts['female_only']==1)?'checked="checked" ':''?>>
										</td>
									</tr>
									<tr>
										<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">
											Is it applicable for male?&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										</td>
										<td bgcolor="#efefef" class="a12_b">
											<input type="checkbox" name="male_only" id="male_only" value="1" <?=($ts['male_only']==1)?'checked="checked" ':''?>>
										</td>
									</tr>
									<tr>
										<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">
											With result?&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										</td>
										<td bgcolor="#efefef" class="a12_b">
											<input type="checkbox" name="with_result" id="with_result" value="1" <?=($ts['with_result']==1)?'checked="checked" ':''?>>
										</td>
									</tr>

									<tr>
										<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">
											With inventory related?&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										</td>
										<td bgcolor="#efefef" class="a12_b">
											<input type="checkbox" name="with_inventory" id="with_inventory" value="1" <?=($ts['with_inventory']==1)?'checked="checked" ':''?>>
										</td>
									</tr>

									<tr>
										<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">
											Is it included in existing LIS?&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										</td>
										<td bgcolor="#efefef" class="a12_b">
											<input type="checkbox" name="in_lis" id="in_lis" value="1" <?=($ts['in_lis']==1)?'checked="checked" ':''?>>
										</td>
									</tr>
									<!-- added by VAN 02-08-2011 -->
									<tr>
										<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">
											Available in PHS free of charge?&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										</td>
										<td bgcolor="#efefef" class="a12_b">
											<input type="checkbox" name="in_phs" id="in_phs" value="1" <?=($ts['in_phs']==1)?'checked="checked" ':''?>>
										</td>
									</tr>
									<!-- -->
									<tr>
										<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">
											Is Socialized?&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										</td>
										<td bgcolor="#efefef" class="a12_b">
											<input type="checkbox" name="is_socialized" id="is_socialized" value="1" onClick="social_status();" <?=($ts['is_socialized']==1)?'checked="checked" ':''?>>
										</td>
									</tr>
									<!-- added by poliam 03/26/2014 -->
									<tr>
										<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">
											has other rates?&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										</td>
										<td bgcolor="#efefef" class="a12_b">
											<input type="checkbox" name="rate_changes" id="rate_changes" value="1" onClick="rate_status();" <?=($ts['is_other_rate']==1)?'checked="checked" ':''?>>
										</td>
									</tr>
									<!-- ended by poliam 03/26/2014 -->
									<tbody id="social_service" style="display:none ">
										<tr>
												<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">Discount Classification</td>
												<td bgcolor="#efefef" class="a12_b">
													<select name="discount_name" id="discount_name">
													<option value="0">Select Discount Classification</option>
															 <?php
														$discountObj = &$srv->getDiscountList("discountdesc");
														if(is_object($discountObj)){
															while($result=$discountObj->FetchRow()){
																				echo "<option value=\"".$result['discountid']."\">".$result['discountdesc']." \n";
															}
														}

																?>
													</select>
													<input type="button" name="add_dcount" id="add_dcount" value="Add Discount" style="cursor:pointer" onClick="javascript: var bol=validate_discount(); if (bol) {addRow('discount_table');}">

												</td>
											</tr>
											<tr>
												<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">Discounted Price</td>
												<td bgcolor="#efefef" class="a12_b">
													<input type="text" name="discount_price" id="discount_price" size=35 maxlength=30 style="width:100%" value="">

												</td>

											</tr>

											<tr>
												<td>&nbsp;</td>
												<td>&nbsp;</td>
											</tr>
											<tr>
												<td>&nbsp;</td>
												<td>
														<table id="discount_table" border="1" cellspacing="0" width="300">
															<tbody id="discount_tbody">
																<tr>

																	<th> Discount Classification<br /> </th>
																		 <th> Discounted Price<br /> </th>
																	<th></th>
																</tr>
																<tr id="id0" style="display:none ">
																	<td colspan="3">No such laboratory service's discounts available...</td>
																</tr>
																<?php
																	#------------
																	#get the list of discounts of a certain service

																	#$serv_disc = &$srv->getServiceDiscount("service_code='".addslashes($nr)."' AND dept_nr = '".addslashes($dept['nr'])."'","discountid");
																	#edit by VAN 10-17-07
																	#$serv_disc = &$srv->getServiceDiscount("service_code='".addslashes($nr)."' AND service_area = 'LB'","discountid");
																	$serv_disc = &$srv->getServiceDiscount("(service_code='".$nr."' OR service_code='".urlencode($nr)."') AND service_area = 'LB'","discountid");
																	#echo "code = ".$xcode;
																	#echo "<br>sql = ".$srv->sql;

																	if(is_object($serv_disc)){
																	#edited by VAN 03-14-08
																	#$i=1;
																	$i=2;
																		while($result=$serv_disc->FetchRow()){
																		?>
																			<tr id="drow<?=$i?>">
																				<td>
																						 <select name="nameselRow<?=$i?>" id="nameselRow<?=$i?>">
																						<?php
																							$discountObj = &$srv->getDiscountList("discountdesc");
																							if(is_object($discountObj)){
																								while($result_sel=$discountObj->FetchRow()){
																									if ($result_sel['discountid']==$result['discountid']){
																										echo "<option value=\"".$result_sel['discountid']."\" selected>".$result_sel['discountdesc']." \n";
																													}else{
																															echo "<option value=\"".$result_sel['discountid']."\">".$result_sel['discountdesc']." \n";
																												 }
																								 }
																							 }

																									?>
																						</select>
																					</td>

																					<td>
																						<input type="text" id="dpriceRow<?=$i?>" name="dpriceRow<?=$i?>" value="<?=$result['price']?>" onBlur="formatprice(this);" size="10">
																					 </td>

																					<td>
																						<img name="<?=$i?>" id="<?=$i?>" src="../../gui/img/common/default/nopmuser.gif" onClick="removeDiscount(this);" style="cursor:pointer" alt="Delete Discount">

																					 </td>
																				</tr>
																			<?php
																				$i++;
																			}
																		}else{
																			?>
																				<!--
																				<tr id="id0">
																					<td colspan="3">No such laboratory service's discounts available...</td>
																				</tr>
																				-->
																				<script type="text/javascript">
																					document.getElementById('id0').style.display='';
																				</script>
																		<?php
																		}
																			?>
																</tbody>
														</table>
													</td>

												</tr>
									</tbody>
									<!-- added by poliam 03/26/2014 -->
									<tbody id="rate_other" style="display:none ">
										<tr>
											<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">Room Type</td>
											<td bgcolor="#efefef" class="a12_b">
												<select name="RoomTypeName" id="RoomTypeName">
												<option value="0">Select Room Type</option>
													<?php
														$RoomType = &$srv->GetRoomType($type="select");
														if(is_object($RoomType)){
															while($Room=$RoomType->FetchRow()){
																echo "<option value=\"".$Room['nr']."\">".$Room['name']." \n";
															}
														}
													?>
												</select>																						
												<input type="button" name="add_dcount" id="add_dcount" value="Add Rate" style="cursor:pointer" onClick="javascript: var bol=validate_Room_Type(); if (bol) {addRowRoomType('RoomTypeTable');}">
											</td>
										</tr>
											<tr>
												<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">Cash</td>
												<td bgcolor="#efefef" class="a12_b">
													<input type="text" name="RoomTypePrice" id="RoomTypePrice" size=35 maxlength=30 style="width:100%" value="">
												</td>
											</tr>
											<tr>
												<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">Charge</td>
												<td bgcolor="#efefef" class="a12_b">
													<input type="text" name="RoomTypeCharge" id="RoomTypeCharge" size=35 maxlength=30 style="width:100%" value="">
												</td>
											</tr>
											<tr>
												<td>&nbsp;</td>
												<td>&nbsp;</td>
											</tr>
											<tr>
												<td>&nbsp;</td>
												<td>
													<table id="RoomTypeTable" border="1" cellspacing="0" width="300">
														<tbody id="discount_tbody">
															<tr>
																<th> Room Type<br /> </th>
																<th> Room Type Price<br /> </th>
																<th> Room Type Charge<br /> </th>
																<th></th>
															</tr>
															<tr id="id0" style="display:none ">
																<td colspan="4">No such laboratory service's discounts available...</td>
															</tr>
																<?php
																
																	$serv_disc2 = &$srv->GetRoomType2("(service_code='".$nr."')");
																	if(is_object($serv_disc2)){
																	$i=2;
																		while($result=$serv_disc2->FetchRow()){
																?>
																			<tr id="drow<?=$i?>">
																				<td>
																					<select name="RoomnameselRow<?=$i?>" id="RoomnameselRow<?=$i?>">
																						<?php
																							$RoomTypeObj = &$srv->GetRoomType();
																							if(is_object($RoomTypeObj)){
																								while($Room_sel=$RoomTypeObj->FetchRow()){
																									if ($Room_sel['nr']==$result['room_type']){
																										echo "<option value=\"".$Room_sel['nr']."\" selected>".$Room_sel['name']." \n";
																									}else{
																										echo "<option value=\"".$Room_sel['nr']."\">".$Room_sel['name']." \n";
																									}
																								 }
																							 }
																						?>
																						</select>
																					</td>
																					<td>
																						<input type="text" id="RpriceRow<?=$i?>" name="RpriceRow<?=$i?>" value="<?=$result['cash_price']?>" onBlur="formatprice(this);" size="10">
																					 </td>
																					 <td>
																						<input type="text" id="RChargeRow<?=$i?>" name="RChargeRow<?=$i?>" value="<?=$result['charge_price']?>" onBlur="formatprice(this);" size="10">
																					 </td>
																				<td>
																						<img name="<?=$i?>" id="<?=$i?>" src="../../gui/img/common/default/nopmuser.gif" onClick="RomoveRoomType(this);" style="cursor:pointer" alt="Delete Discount">
																				</td>
																			</tr>
																			<?php
																				$i++;
																			}
																		}else{
																			?>
																				<script type="text/javascript">
																					document.getElementById('id0').style.display='';
																				</script>
																		<?php
																		}
																			?>
																</tbody>
														</table>
													</td>

												</tr>
									</tbody>
									<!-- ended by poliam 03/26/2014 -->
									<tr>
										<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">Remarks</td>
										<td bgcolor="#ffffee" class="a12_b">
											<textarea name="status" id="status" cols="35" rows="2" style="width:100%" wrap="hard"><?= $ts['remarks'] ?></textarea>

										</td>
									</tr>

							<?php
							#commented by VAN 03-14-08
							# }
							?>
						</table>

						<!--<input type="text" name="nr" value="<?php echo $nr; ?>">-->
						<input type="hidden" name="nr" id="nr" value="<?= ($nr)?$nr:$xcode; ?>">
						<input type="hidden" name="sid" value="<?php echo $sid; ?>">
						<input type="hidden" name="lang" value="<?php echo $lang; ?>">

						<!--<input type="text" name="excode" value="<?= $excode ?>">-->
						<!--<input type="hidden" name="excode" value="<?= ($excode)?$excode:$xcode; ?>">-->
						<!--<input type="hidden" name="row" value="<?= $row ?>">-->
						<!--<input type="hidden" name="groupcode" value="<?= $ts['group_code']  ?>">-->
						<input type="hidden" name="groupcode" id="groupcode" value="<?= ($groupcode)?$groupcode:$_POST['groupcode'] ?>">
						<input type="hidden" name="totalrow" id="totalrow" value="<?=$totalrow?>">
						<input type="hidden" name="totalrowRoom" id="totalrowRoom" value="<?=$totalrowRoom?>">
						<input type="hidden" name="submit_val" id="submit_val" value="<?= $submit_val?>">
                        <!--<input onClick="tableRows();" type="image" style="cursor:pointer" <?php echo createLDImgSrc($root_path,'savedisc.gif','0') ?> > -->
						<br>
						<?php if ($ts){ ?>
							<input type="image" id="submitted" name="submitted" value="1" style="cursor:pointer" onclick="$('submit_val').value = this.value;" <?php echo createLDImgSrc($root_path,'update.gif','0') ?> >
						<?php }else{ ?>
							<input type="image" id="submitted" name="submitted" value="1" style="cursor:pointer" onclick="$('submit_val').value = this.value;" <?php echo createLDImgSrc($root_path,'savedisc.gif','0') ?> >
						<?php } ?>
						<!--
						<a onClick="document.paramedit.reset(); return false;" href="#">
							<img id="cancel" name="cancel" src="../../gui/img/control/default/en/en_cancel.gif" border=0 alt="Cancel" title="Cancel" style="cursor:pointer">
						</a>
						-->
						<img id="cancel" name="cancel" src="../../gui/img/control/default/en/en_cancel.gif" border=0 alt="Cancel" title="Cancel" onClick="javascript:window.parent.cClick();" style="cursor:pointer">

						<input type="hidden" name="mode" value="<?= ($ts)?'update':'save' ?>">
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