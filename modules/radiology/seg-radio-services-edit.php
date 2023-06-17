<?php
#edited by VAN 03-15-08
define('ROW_MAX',15); # define here the maximum number of rows for displaying the parameters

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

$lang_tables=array('chemlab_groups.php','chemlab_params.php');
define('LANG_FILE','lab.php');
#$local_user='ck_lab_user';
$local_user='ck_radio_user';   # burn added : October 24, 2007
require_once($root_path.'include/inc_front_chain_lang.php');

$thisfile=basename(__FILE__);

//$db->debug=true;

# Create lab object
require_once($root_path.'include/care_api_classes/class_radiology.php');
$srv=new SegRadio();

require_once($root_path.'include/care_api_classes/class_ward.php');
$ward=new Ward();

require_once($root_path.'include/care_api_classes/class_department.php');
$dept_obj=new Department;

#$dept = $dept_obj->getDepartmentInfo("name_formal like 'pathology'", "name_formal");

require($root_path.'include/inc_labor_param_group.php');

# Load the date formatter */
include_once($root_path.'include/inc_date_format_functions.php');

#$excode=urlencode($_GET['nr']);
#echo "url = ".$_GET['nr'];
#$excode=$_GET['nr'];
#$grpcode =$_GET['grpcode'];
$groupcode =$_GET['grpcode'];
$deptnr = $_GET['deptnr'];

$_POST['service_code'] = str_replace("'","",stripslashes($_POST['service_code']));
$_POST['excode'] = str_replace("'","",stripslashes($_POST['excode']));
$excode = str_replace("'","",stripslashes($excode));


if(isset($_POST['excode'])) $excode=$_POST['excode'];

$_POST['submitted'] = $_POST['submit_val']; 
#if($mode=='save'){
	# Save the nr
if (isset($_POST['submitted'])){	
	$x = array();
	#$xrow=$_POST['row'];
	$xcode=$_POST['service_code'];
	$xname=$_POST['name'];
	$xcash=($_POST['cash']!=''&&isset($_POST['cash']))?$_POST['cash']:'NULL';
	$xcharge=($_POST['charge']!=''&&isset($_POST['cash']))?$_POST['charge']:'NULL';
	$xremarks=$_POST['status'];  
  
  if (isset($_POST['is_unavailable']))
    $xstatus = 'unavailable';
  else
    $xstatus = '';
	
	$socialized = (isset($_POST['is_socialized']))?1:0;
	
	$is_ER = (isset($_POST['is_ER']))?1:0;
	
	$xgid=$_POST['groupcode'];
	
	$serv_discount = array();
	
	switch($mode){
		case 'save':
							#for ($i=1; $i<=$_POST['totalrow']; $i++){
							for ($i=2; $i<=$_POST['totalrow']; $i++){
								$name = 'nameselRow'.$i;
								$price = 'dpriceRow'.$i;
		
								$serv_discount[$i-1]['discount'] = $_POST[$name];
								$serv_discount[$i-1]['price'] = $_POST[$price];
							}
							
							//added by ken 3/27/2014
							for($i=2; $i<=$_POST['totalroom']; $i++){
								$room_id = 'rnameselRow' .$i;
								$room_price = 'rpriceRow' .$i;
								$room_charge = 'rchargeRow' .$i;

								$serv_room[$i-1]['id'] = $_POST[$room_id];
								$serv_room[$i-1]['price'] = $_POST[$room_price];
								$serv_room[$i-1]['charge'] = $_POST[$room_charge];
							}
							//ended by ken 3/27/2014

							if ($socialized){
								#$srv->deleteServiceDiscounts($xcode,$dept_nr);
								$srv->deleteServiceDiscounts($xcode,'RD');
								#$srv->AddServiceDiscounts($serv_discount,$xcode,$dept_nr);
								$srv->AddServiceDiscounts($serv_discount,$xcode,'RD');
							}else{
								$srv->deleteServiceDiscounts($xcode,'RD');
							}	
							
							#if ($srv->addRadioService($_POST['excode'],$xcode, $xname, $xcash, $xcharge, $xstatus, $xgid, $socialized)) {
							if ($srv->addRadioService($xcode, $xname, $xcash, $xcharge, $xremarks, $xgid, $socialized, $is_ER,$xstatus)) {
								//added by ken 3/27/2014
								$srv->deleteRoomRate($xcode, 'RD');

								$srv->addRoomRates($serv_room, $xcode, 'RD', 'add');
								//ended by ken 3/27/2014
								echo "<br><div align=\"center\" style=\"font:bold 12px Tahoma; color:#990000; \">Radiological Service is successfully created.</div><br />";
							}else{
								echo "<br><div align=\"center\" style=\"font:bold 12px Tahoma; color:#990000; \"><strong>Error :</strong> Radiological Service is not successfully saved.</div><br />";
							} 	
							break;
		case 'update':	
							#for ($i=1; $i<=$_POST['totalrow']; $i++){
							for ($i=2; $i<=$_POST['totalrow']; $i++){
								$name = 'nameselRow'.$i;
								$price = 'dpriceRow'.$i;
		
								$serv_discount[$i-1]['discount'] = $_POST[$name];
								$serv_discount[$i-1]['price'] = $_POST[$price];
							}
							
							//added by ken 3/27/2014
							for($i=2; $i<=$_POST['totalroom']; $i++){
								$room_id = 'rnameselRow' .$i;
								$room_price = 'rpriceRow' .$i;
								$room_charge = 'rchargeRow' .$i;

								$serv_room[$i-1]['id'] = $_POST[$room_id];
								$serv_room[$i-1]['price'] = $_POST[$room_price];
								$serv_room[$i-1]['charge'] = $_POST[$room_charge];
							}
							//ended by ken 3/27/2014

							if ($_POST['prev_nr']){
								$_POST['excode'] = $_POST['prev_nr'];
							}
							
							if ($socialized){
								#$srv->deleteServiceDiscounts($xcode,$dept_nr);
								#edited by VAN 04-29-08
								if ($_POST['excode']==$xcode){
									$srv->deleteServiceDiscounts($xcode,'RD');
									#$srv->AddServiceDiscounts($serv_discount,$xcode,$dept_nr);
									$srv->AddServiceDiscounts($serv_discount,$xcode,'RD');
								}else{
									$srv->deleteServiceDiscounts($_POST['excode'],'RD');
									#$srv->AddServiceDiscounts($serv_discount,$xcode,$dept_nr);
									$srv->AddServiceDiscounts($serv_discount,$xcode,'RD');
								}	
							}else{
								if ($_POST['excode']==$xcode){
									$srv->deleteServiceDiscounts($xcode,'RD');
								}else{
									$srv->deleteServiceDiscounts($_POST['excode'],'RD');
								}	
							}	
							
							if(!$xgid){
								$temp_grp = $srv->getServiceGroupCode($xcode);
								$xgid = $temp_grp['group_code'];
							}

							if ($srv->updateRadioService($_POST['excode'],$xcode, $xname, $xcash, $xcharge, $xremarks, $xgid, $socialized, $is_ER,$xstatus)) {
								#echo "sql = ".$srv->sql;
								//added by ken 3/27/2014
								if ($_POST['excode']==$xcode){
									$srv->deleteRoomRate($xcode, 'RD');

									$srv->addRoomRates($serv_room, $xcode, 'RD', 'update');
							}
							else{
									$srv->deleteRoomRate($_POST['excode'], 'RD');

									$srv->addRoomRates($serv_room, $xcode, 'RD', 'update');
  								}
								//ended by ken 3/27/2014
								echo "<br><div align=\"center\" style=\"font:bold 12px Tahoma; color:#990000; \">Radiological Service is successfully updated.</div><br />";
							}else{
								echo "<br><div align=\"center\" style=\"font:bold 12px Tahoma; color:#990000; \"><strong>Error :</strong> Radiological Service is not successfully updated.</div><br />";
							}				
							
							break;
		} #end of switch statement
	} #end of if statement						
							
$sNames=array("Service Code", "Service Name", "Price(Cash)", "Price(Charge)","Status");
$sItems=array('service_code','name','price_cash','price_charge','status');

#print_r($sNames);

# Get the radiology service values
#echo 'nr = '.$xcode;
#if (empty($nr))
if (empty($xcode))
	#$nr = $xcode;
	$nr = $nr;
else
	$nr = $xcode;

#echo '<br>nr excode = '.$nr;

#if($tsrv=&$srv->getRadioServicesInfo("service_code='".addslashes(urlencode($nr))."' AND s.group_code = sg.group_code")){
if($tsrv=&$srv->getRadioServicesInfo("(service_code='".urlencode($nr)."' OR service_code = '".$nr."') AND s.group_code = sg.group_code")){
	$ts=$tsrv->FetchRow();
	
}else{
	$ts=false;
}
#echo "sql = ".$srv->sql;	
?>
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 3.0//EN" "html.dtd">
<?php html_rtl($lang); ?>
<HEAD>
<?php echo setCharSet(); ?>
 <TITLE>Edit Radiological Service</TITLE>

<script language="javascript" name="j1">
<!--        
 function editParam(nr){
	urlholder="labor_test_param_edit?sid=<?php echo "$sid&lang=$lang" ?>&nr="+encodeURIComponent(nr);
	editparam_<?php echo $sid ?>=window.open(urlholder,"editparam_<?php echo $sid ?>","width=500,height=600,menubar=no,resizable=yes,scrollbars=yes");
 }

 function formatprice(obj){
 	//alert("formatprice");
	var objID = obj.id;
	var fprice ;
	var price = document.getElementById(objID).value;
	
	if (isNaN(price))
		fprice="N/A";
	else {
		fprice=price-0;
		fprice=fprice.toFixed(2);
	}

	document.getElementById(objID).value = fprice;
 }

 function validate_discount(){
	var dname = document.getElementById("discount_name");
	var dprice = document.getElementById("discount_price");
	var bol;
	
	//alert(document.getElementById('discount_table').innerHTML);
	
	if(dname.value==0){
		alert("Please select a discount classification.");
		dname.focus();
		//return false;
		bol = false;
	}else if ((isNaN(dprice.value))||(dprice.value=="")) {	
		alert("Enter the discount price.");
		dprice.focus();
		//return false;
		bol = false;
	}else{
		bol = true;
	}
	
	if((dname.value!=0)&&((!isNaN(dprice.value))||(dprice.value!=""))){
		var tbl = document.getElementById('discount_table');
		rows_len = tbl.rows.length;	
		var tablecontent = document.getElementById('discount_tbody').innerHTML;
		var bol2, i;
		
		bol2 = tablecontent.match('No such radiological service\'s discounts available...');
		
		if (bol2){
			document.getElementById('id0').style.display='none';
		}else{
			//nothing to do
		}
		
		//for(i=1; i < rows_len; i++){
		for(i=2; i < rows_len; i++){
			var disc_name = 'nameselRow'+i;
			var disc_price = 'dpriceRow'+i;
			//alert(document.getElementById('discount_name').value + "==" + document.getElementById(disc_name).value);
			if (document.getElementById('discount_name').value == document.getElementById(disc_name).value){
				alert("The radiological service is already in the discount table. If you want to edit the price, just edit the price in the textbox");
				document.getElementById(disc_price).focus();
				// reset 
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
 	
	var tbl = document.getElementById(id);
	//alert(tbl.innerHTML);
 	var lastRow = tbl.rows.length;
  		// if there's no header row in the table, then iteration = lastRow + 1
  	var iteration = lastRow;
  	var row = tbl.insertRow(lastRow);
 	row.id = 'drow'+iteration;
  
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
				sel.options[<?=$i?>] = new Option('<?=$result['discountdesc']?>', '<?=$result['discountid']?>');
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
 
 function removeDiscount(obj){
	 var tbl = document.getElementById('discount_table');
	 var objId = obj.id;
	 var rowname = 'drow'+objId;
	 
	 index = parseInt(rowname.substr(4,1));
	 //alert("index = "+index);
	 tbl.deleteRow(document.getElementById(rowname).rowIndex);
	 
	 //rows_len = document.getElementById('discount_table').rows.length;
	 rows_len = tbl.rows.length;
	 document.getElementById("totalrow").value = rows_len - 1;
	 
	 //alert(tbl.innerHTML);
	 if (document.getElementById('discount_tbody').rows.length==2){
	 	document.getElementById('discount_tbody').innerHTML = '<tr>'+
																					'<th> Discount Classification<br /> </th>'+
																            	'<th> Discounted Price<br /> </th>'+
																					'<th></th>'+
																				'</tr>'+
																				'<tr id="id0">'+
																					'<td colspan="3">No such radiological service\'s discounts available...</td>'+
																				'</tr>';
	 }
	 
	 for (i=index; i< rows_len; i++){
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
 }

 function preLoad(){
	//alert("preLoad");
	document.getElementById("totalrow").value = document.getElementById('discount_table').rows.length-1;
	//document.getElementById("dept_nr").value = <?=$deptnr;?>;
 }

 function social_status(){
   if (document.getElementById('is_socialized').checked)
		//alert("socialized service");
		document.getElementById('social_service').style.display = '';
	else	
		//alert("non-socialized service");
		document.getElementById('social_service').style.display = 'none';
 }
 
//added by VAN
//number only and decimal point is allowed
function keyPressHandler(e){
	var unicode=e.charCode? e.charCode : e.keyCode
	if (unicode>31 && (unicode<46 || unicode == 47 ||unicode>57)) //if not a number
	//if (unicode>31 && (unicode<48 || unicode>57)) //if not a number
		return false //disable key press
}

function checkForm(d){
	if (d.service_code.value==""){
		alert('Pls. type the code of the service.');
		d.service_code.focus();
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

//added by ken 3/27/2014
 function validate_room(){
 	var dname = document.getElementById("room_name");
	var dprice = document.getElementById("room_price");
	var dcharge = document.getElementById("room_charge");
	var bol1;
	
	//alert(document.getElementById('discount_table').innerHTML);
	
	if(dname.value==0){
		alert("Please select a Ward.");
		dname.focus();
		//return false;
		bol1 = false;
	}else if ((isNaN(dprice.value))||(dprice.value=="")) {	
		alert("Enter the room rate price.");
		dprice.focus();
		//return false;
		bol1 = false;
	}else if ((isNaN(dcharge.value))||(dcharge.value=="")) {	
		alert("Enter the room rate charge.");
		dcharge.focus();
		//return false;
		bol1 = false;
	}else{
		bol1 = true;
	}
	
	if((dname.value!=0)&&((!isNaN(dprice.value))||(dprice.value!=""))&&((!isNaN(dcharge.value))||(dcharge.value!=""))){
		var tbl = document.getElementById('room_table');
		rows_len = tbl.rows.length;	
		var tablecontent = document.getElementById('room_tbody').innerHTML;
		var bol2, i;
		
		bol2 = tablecontent.match('No such other room rates available...');
		
		if (bol2){
			document.getElementById('rid0').style.display='none';
		}else{
			//nothing to do
		}
		
		//for(i=1; i < rows_len; i++){
		for(i=2; i < rows_len; i++){
			var room_name = 'rnameselRow'+i;
			var room_price = 'rpriceRow'+i;
			var room_charge = 'rchargeRow'+i;
			//alert(document.getElementById('discount_name').value + "==" + document.getElementById(disc_name).value);
			if (document.getElementById('room_name').value == document.getElementById(room_name).value){
				alert("The radiological service is already in the other room rate table. If you want to edit the price, just edit the price in the textbox");
				document.getElementById(room_price).focus();
				// reset 
				document.getElementById("room_name").value = 0;  
				document.getElementById("room_price").value = " ";
				document.getElementById("room_charge").value = " ";
				bol1 = false;
				break;
			}
		}
	}
	return bol1;
 }

function addRow1(id){
 	
	var tbl = document.getElementById(id);
	//alert(tbl.innerHTML);
 	var lastRow = tbl.rows.length;
  		// if there's no header row in the table, then iteration = lastRow + 1
  	var iteration = lastRow;
  	var row = tbl.insertRow(lastRow);
 	row.id = 'rrow'+iteration;
  
  	// right cell
  //	var cellRight = row.insertCell(1);
  	var cellRight = row.insertCell(0);
  	var sel = document.createElement('select');
  	sel.name = 'rnameselRow' + iteration;
	sel.id = 'rnameselRow' + iteration;
  	
	<?php
		$wardObj = &$ward->getAllRoomType();
		if($wardObj){
			$i = 0;
			while ($row=$wardObj->FetchRow()){
		?>	
				//echo "<option value=\"".$result['discountid']."\">".$result['discountdesc']." \n";
				sel.options[<?=$i?>] = new Option('<?=addslashes($row['name'])?>', '<?=$row['nr']?>');
   <?php
	      $i=$i+1;	
			}
		}
	?>
	  
  	//el.onkeypress = keyPressTest;
	sel.value = document.getElementById("room_name").value;
  	cellRight.appendChild(sel);
  
  	// select cell
  	//var cellRightSel = row.insertCell(2);
	var cellRightSel = row.insertCell(1);
  	var el = document.createElement('input');
  	el.type = 'text';
  	el.name = 'rpriceRow' + iteration;
  	el.id = 'rpriceRow' + iteration;
  	el.size = 10;
	
	var fprice ;
	var price = document.getElementById("room_price").value;
	
	if (isNaN(price))
		fprice="N/A";
	else {
		fprice=price-0;
		fprice=fprice.toFixed(2);
	}
	
	el.value = fprice;
	el.setAttribute("onBlur", "formatprice(this);");
	cellRightSel.appendChild(el);
	

	var cellRightSel2 = row.insertCell(2);
  	var el1 = document.createElement('input');
  	el1.type = 'text';
  	el1.name = 'rchargeRow' + iteration;
  	el1.id = 'rchargeRow' + iteration;
  	el1.size = 10;
	
	var fcharge ;
	var charge = document.getElementById("room_charge").value;
	
	if (isNaN(charge))
		fcharge="N/A";
	else {
		fcharge=charge-0;
		fcharge=fcharge.toFixed(2);
	}
	
	el1.value = fcharge;
	el1.setAttribute("onBlur", "formatprice(this);");
	cellRightSel2.appendChild(el1)
	//var cellRight2 = row.insertCell(3);
	var cellRight2 = row.insertCell(3);
  	var img = document.createElement('img');
  	img.src = "../../gui/img/common/default/nopmuser.gif";
	//img.id = 'delbuttonRow' + iteration;
	img.id = iteration;
	img.name = iteration;
	img.setAttribute("alt", "Delete Room Rate");
	img.setAttribute("onClick", "removeRoom(this);");
	//img.onclick = removeDiscount;
	img.setAttribute("style", "cursor:pointer");
	cellRight2.appendChild(img);
	
	// reset value
	document.getElementById("room_name").value = 0;  
	document.getElementById("room_price").value = " "; 
	document.getElementById("room_charge").value = " ";
	document.getElementById("totalroom").value = document.getElementById('room_table').rows.length-1;
 }

 function removeRoom(obj){
	 var tbl = document.getElementById('room_table');
	 var objId = obj.id;
	 var rowname = 'rrow'+objId;
	 
	 index = parseInt(rowname.substr(4,1));
	 //alert("index = "+index);
	 tbl.deleteRow(document.getElementById(rowname).rowIndex);
	 
	 //rows_len = document.getElementById('discount_table').rows.length;
	 rows_len = tbl.rows.length;
	 document.getElementById("totalroom").value = rows_len - 1;
	 
	 //alert(tbl.innerHTML);
	 if (document.getElementById('room_tbody').rows.length==2){
	 	document.getElementById('room_tbody').innerHTML = '<tr>'+
															'<th> Other Room Selection<br /> </th>'+
															'<th> Other Room Price<br /> </th>'+
															'<th> Other Room Charge<br /> </th>'+
															'<th></th>'+
														'</tr>'+
														'<tr id="rid0">'+
															'<td colspan="3">No such other room rates available...</td>'+
														'</tr>';
	 }
	 
	 for (i=index; i< rows_len; i++){
	   var cnt = i+1;
		var ncnt = i;
		var room_id = 'rnameselRow'+cnt;
		var price_room = 'rpriceRow'+cnt;
		var charge_room = 'rchargeRow'+cnt;
		var img = cnt;
	 	
		//document.getElementById('discount_table').rows[i].id = 'drow'+ncnt;
		tbl.rows[i].id = 'rrow'+ncnt;
		document.getElementById(room_id).id = 'rnameselRow'+ncnt;
		
		var room_id_new = 'rnameselRow'+ncnt;
		document.getElementById(room_id_new).name = 'rnameselRow'+ncnt;
		
		document.getElementById(price_room).id = 'rpriceRow'+ncnt;
		
		var price_new = 'rpriceRow'+ncnt;
		document.getElementById(price_new).name = 'rpriceRow'+ncnt;

		document.getElementById(charge_room).id = 'rchargeRow'+ncnt;
		
		var charge_new = 'rchargeRow'+ncnt;
		document.getElementById(charge_new).name = 'rchargeRow'+ncnt;
		
		document.getElementById(img).id = ncnt;
	 }
 }

 //ended by ken 3/27/2014
//-----------------------
/*
function tableRows() {
 alert("tableRows");	
 alert(document.getElementById('discount_table').innerHTML);
}*/

/*
function removeDiscount(e, obj){
	 var tbl = document.getElementById('discount_table');
    
	 alert(tbl.innerHTML);
	 var key;
	 
    if(window.event) {
      key = window.event.keyCode; 
    }
    else if(e.which) {
      key = e.which;
    }
	
	 var objId;
    if (obj != null) {
      objId = obj.id;
    } else {
      objId = this.id;
    }
	
	var rowname = 'drow'+objId;
	tbl.deleteRow(document.getElementById(rowname).rowIndex);
	//rowno = parseInt(objId);
	document.getElementById("totalrow").value = document.getElementById('discount_table').rows.length-1;
}
*/


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
 ?> onLoad="preLoad();social_status();">

<form action="<?php echo $thisfile; ?>" method="post" name="paramedit" onSubmit="return checkForm(this);">

<table width=100% border=0 cellspacing=0 cellpadding=0>
	<tr>
		<td bgcolor="<?php echo $cfg['top_bgcolor']; ?>" >
			<FONT COLOR="<?php echo $cfg['top_txtcolor']; ?>"  SIZE=+2  FACE="Arial">
				<STRONG> &nbsp;
				<?php 	
					echo $ts['name'];
				?>
				</STRONG>
			</FONT>
		</td>
		<!--commented by VAN 03-17-08 -->
		<!--
		<td bgcolor="<?php echo $cfg['top_bgcolor']; ?>" height="10" align=right >
			<nobr>
			<a href="javascript:gethelp('lab_param_edit.php')">
				<img <?php echo createLDImgSrc($root_path,'hilfe-r.gif','0') ?>  <?php if($cfg['dhtml'])echo'style=filter:alpha(opacity=70) onMouseover=hilite(this,1) onMouseOut=hilite(this,0)';?> >
			</a>
			<a href="javascript:window.close()" >
				<img <?php echo createLDImgSrc($root_path,'close2.gif','0') ?>  <?php if($cfg['dhtml'])echo'style=filter:alpha(opacity=70) onMouseover=hilite(this,1) onMouseOut=hilite(this,0)';?>>
			</a>
			</nobr>
		</td>
		-->
	</tr>
	<tr align="center">
		<td  bgcolor=#dde1ec colspan=2>
			<FONT SIZE=-1 FACE="Arial">
				<table border=0 bgcolor=#b6caf1 cellspacing=1 cellpadding=1 width="100%">
					<tr>
						<td bgcolor=#16446f colspan=2>
							<FONT SIZE=2 FACE="Verdana,Arial" color="#ffffff">
							<b><?php 
										#echo $ts['grpname']; #echo $parametergruppe[$ts['group_id']]; 
										if($ts)
											echo $ts['grpname']; #echo $parametergruppe[$ts['group_id']];
										else{	
											if (empty($dept_nr))
												$dept_nr = $deptnr;
												
											$groupInfo = $srv->getAllRadioGroupInfo($groupcode,$dept_nr);
											echo strtoupper($groupInfo['name']);
										}	
								?></b>
							</font>
						</td>
					</tr>
					<tr>
						<td colspan=2>
								<table border="0" cellpadding=2 cellspacing=1>
								<?php 
								$toggle=0;
								#commented by VAN 03-17-08
								#if($ts){
								if ($ts['service_code'])
									$readonly = "readonly";
								else
									$readonly = "";	
								
								?>
									<tr>
										<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px" width="20%">Service code</td>
										<td bgcolor="#ffffee" class="a12_b">
											<!--<input type="text" name="service_code" id="service_code" size=35 maxlength=10 style="width:100%" <?=($ts)?'readonly':''?> value="<?= $ts['service_code'] ?>">-->
											<input type="text" name="service_code" id="service_code" <?=$readonly?> size=35 maxlength=10 style="width:100%" value="<?= $ts['service_code'] ?>">
										</td>
									</tr>
									<tr>
										<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">Name</td>
										<td bgcolor="#efefef" class="a12_b">
											<input type="text" name="name" id="name" size=35 maxlength=35 style="width:100%" value="<?= $ts['name'] ?>">
										</td>
									</tr>
									<tr>
										<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">Price(Cash)</td>
										<td bgcolor="#ffffee" class="a12_b">
											<input type="text" name="cash" id="cash" size=35 maxlength=30 style="width:100%" onBlur="formatprice(this);" onKeyPress="return keyPressHandler(event);" value="<?= $ts['price_cash'] ?>">
										</td>
									</tr>
									<tr>
										<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">Price(Charge)</td>
										<td bgcolor="#efefef" class="a12_b">
											<input type="text" name="charge" id="charge" size=35 maxlength=30 style="width:100%" onBlur="formatprice(this);" onKeyPress="return keyPressHandler(event);" value="<?= $ts['price_charge'] ?>">
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
											Is included in the limited service for ER?&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										</td>
										<td bgcolor="#efefef" class="a12_b">
											<?php if ($ts['is_ER']==1){ ?>
												<input type="checkbox" name="is_ER" id="is_ER" value="1" checked>
											<?php }else{ ?>
												<input type="checkbox" name="is_ER" id="is_ER" value="1">
											<?php } ?>
										</td>
									</tr>
									<tr>
										<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">
											Is Socialized?&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										</td>
										<td bgcolor="#efefef" class="a12_b">
											<?php if ($ts['is_socialized']==1){ ?>
												<input type="checkbox" name="is_socialized" id="is_socialized" value="1" checked onClick="social_status();">
											<?php }else{ ?>
												<input type="checkbox" name="is_socialized" id="is_socialized" value="1" onClick="social_status();">
											<?php } ?>
										</td>
									</tr>
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
																#if ($result['discountid']==$ts['discountid']){
																	#echo "<option value=\"".$result['discountid']."\" selected>".$result['discountdesc']." \n";
																#}else{
																	  echo "<option value=\"".$result['discountid']."\">".$result['discountdesc']." \n";
																#}
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
														<th> Discount Price<br /> </th>
														<th></th>
													</tr>
													<tr id="id0" style="display:none ">
														<td colspan="3">No such radiological service's discounts available...</td>
													</tr>
<?php
										#------------
										#get the list of discounts of a certain service
										#echo "<br>d1 _nr".$dept_nr;
										#echo "<br>d2 nr".$deptnr;
													
										if ($deptnr==NULL)
											$dept = $dept_nr;
										else	
											$dept = $deptnr;
										#echo "dept = ".$dept;	
										#$serv_disc = &$srv->getServiceDiscount("service_code='".addslashes($nr)."' AND dept_nr = '".addslashes($dept)."'","discountid");
										#$serv_disc = &$srv->getServiceDiscount("service_code='".$nr."' AND dept_nr = '".addslashes($dept)."'","discountid");
										#edited by VAN 03-17-08
										$serv_disc = &$srv->getServiceDiscount("service_code='".$nr."' AND service_area = 'RD'","discountid");
										#echo "sql = ".$srv->sql;	
											
										if(is_object($serv_disc)){
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
<?
												$i++;
											}# end of while-loop 'while($result=$serv_disc->FetchRow())'
										}else{
											?>
												<script type="text/javascript">
													document.getElementById('id0').style.display='';
												</script>	
											<?php		
										}#end of if-stmt 'if(is_object($serv_disc))'
?>
												</tbody>
											</table>
										</td>	
									</tr>
									</tbody>
<!-- added by ken 3/27/2014 -->
								<tbody id="other_price">
									<tr>
										<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">Other Room Selection</td>
										<td bgcolor="#efefef" class="a12_b">
											<select name="room_name" id="room_name">
												<option value="0">-Select Room Type-</option>
													<?php
														$wardObj = &$ward->getAllRoomType();
														if($wardObj){
															while($row=$wardObj-> FetchRow()){						
																echo "<option value=\"".$row['nr']."\">".$row['name']." \n";
															}
														}
													 ?>
											</select>
											<input type="button" name="add_other" id="add_other" value="Add Room Rate" style="cursor:pointer" onClick="javascript: var bol=validate_room(); if (bol) {addRow1('room_table');}">
											
										</td>
									</tr>
									<tr>
										<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">Room Rates Price</td>
										<td bgcolor="#efefef" class="a12_b">
											<input type="text" name="room_price" id="room_price" size=35 maxlength=30 style="width:100%" onBlur="formatprice(this);" onKeyPress="return keyPressHandler(event);" value="">
										</td>
									</tr>
									<tr>
										<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">Room Rates Charge</td>
										<td bgcolor="#efefef" class="a12_b">
											<input type="text" name="room_charge" id="room_charge" size=35 maxlength=30 style="width:100%" onBlur="formatprice(this);" onKeyPress="return keyPressHandler(event);" value="">
										</td>
									</tr>
									<tr>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
									</tr>
									<tr>
										<td>&nbsp;</td>
										<td>
											<table id="room_table" border="1" cellspacing="0" width="300">
												<tbody id="room_tbody">
													<tr>													
														<th> Ward<br /> </th>
														<th> Room Rate Price<br /> </th>
														<th> Room Rate Charge<br /> </th>
														<th></th>
													</tr>
													<tr id="rid0" style="display:none ">
														<td colspan="3">No such other room rates available...</td>
													</tr>
<?php
										#------------
										#get the list of discounts of a certain service
										#echo "<br>d1 _nr".$dept_nr;
										#echo "<br>d2 nr".$deptnr;
													
										if ($deptnr==NULL)
											$dept = $dept_nr;
										else	
											$dept = $deptnr;
										#echo "dept = ".$dept;	
										#$serv_disc = &$srv->getServiceDiscount("service_code='".addslashes($nr)."' AND dept_nr = '".addslashes($dept)."'","discountid");
										#$serv_disc = &$srv->getServiceDiscount("service_code='".$nr."' AND dept_nr = '".addslashes($dept)."'","discountid");
										#edited by VAN 03-17-08
										$filterTLSPINE = "T/L SPINE";
										$filterTSPINE = "T SPINE";
										if($nr == $filterTLSPINE){
											$nr = $filterTLSPINE." ";
										}else if($nr == $filterTSPINE){
											$nr = $filterTSPINE." ";
										}
										$serv_rates = &$srv->getRoomRate("service_code='".$nr."' AND source = 'RD'","room_type");
										// echo "sql = ".$srv->sql;	
										if(is_object($serv_rates)){
											#$i=1;
											$i=2;
											while($result=$serv_rates->FetchRow()){
												
?>
													<tr id="rrow<?=$i?>">
														<td>
															<select name="rnameselRow<?=$i?>" id="rnameselRow<?=$i?>">
																		<?php
																			$wardObj = &$ward->getAllRoomType();
																			if($wardObj){
																				$totalroom = $wardObj->RecordCount();
																				while ($row=$wardObj->FetchRow()){
																					if ($row['nr']==$result['room_type']){
																						echo "<option value=\"".$row['nr']."\" selected>".$row['name']." \n";
																					}else{
																					  echo "<option value=\"".$row['nr']."\">".$row['name']." \n";
																					}
																				}
																			}
																		?>
															</select>	
														</td>									
														<td>
															<input type="text" id="rpriceRow<?=$i?>" name="rpriceRow<?=$i?>" value="<?=$result['cash_price']?>" onBlur="formatprice(this);" size="10">
														</td>
														<td>
															<input type="text" id="rchargeRow<?=$i?>" name="rchargeRow<?=$i?>" value="<?=$result['charge_price']?>" onBlur="formatprice(this);" size="10">
														</td>	 
														<td>
															<img name="<?=$i?>" id="<?=$i?>" src="../../gui/img/common/default/nopmuser.gif" onClick="removeRoom(this);" style="cursor:pointer" alt="Delete Room Rate">															
														</td>
													</tr>
<?
												$i++;
											}# end of while-loop 'while($result=$serv_disc->FetchRow())'
										}else{
											?>
												<script type="text/javascript">
													document.getElementById('rid0').style.display='';
												</script>	
											<?php		
										}#end of if-stmt 'if(is_object($serv_disc))'
?>
												</tbody>
											</table>
										</td>	
									</tr>
									</tbody>
									<!-- ended by ken 3/27/2014 -->
									<tr>
										<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">Remarks</td>
										<td bgcolor="#ffffee" class="a12_b">
											<textarea name="status" id="status" cols="35" rows="2" style="width:100%" wrap="hard"><?= $ts['remarks'] ?></textarea>
										</td>
									</tr>
<?php
#commented by VAN 03-17-08
#}#end of if-stmt 'if($ts)'
?>
								</table>
								<!--<input type=hidden name="nr" value="<?php echo $nr; ?>">-->
								<input type="hidden" name="nr" id="nr" value="<?= ($nr)?$nr:$xcode; ?>">
								<input type="hidden" name="prev_nr" id="prev_nr" value="<?= $ts['service_code']; ?>">
								<input type=hidden name="sid" value="<?php echo $sid; ?>">
								<input type=hidden name="lang" value="<?php echo $lang; ?>">
								<input type=hidden name="mode" value="save">
								<!--<input type=hidden name="excode" value="<?= $excode ?>">-->
								<input type="hidden" name="excode" value="<?= ($excode)?$excode:$xcode; ?>">
								<!--<input type=hidden name="row" value="<?= $row ?>">-->
								<!--<input type=hidden name="groupcode" value="<?= $ts['group_code']  ?>">-->
								<input type="hidden" name="groupcode" id="groupcode" value="<?= ($groupcode)?$groupcode:$_POST['groupcode'] ?>">
								<input type="hidden" name="totalrow" id="totalrow" value="<?=$totalrow?>">
								<!--added by ken 3/27/2014 -->
								<input type="hidden" name="totalroom" id="totalroom" value="<?=$totalroom?>">
								<input type="hidden" name="submit_val" id="submit_val" value="<?= $submit_val?>">
								<!--<input type="hidden" name="dept_nr" id="dept_nr" value="<?= $dept_nr; ?>">-->
								<input type="hidden" name="dept_nr" id="dept_nr" value="<?= ($deptnr)?$deptnr:$dept_nr; ?>">
								<!--<input  type="image" <?php echo createLDImgSrc($root_path,'savedisc.gif','0') ?>> -->
								<br>
						<?php if ($ts){ ?>
                            <input type="image" id="submitted" name="submitted" value="1" style="cursor:pointer" onclick="$('submit_val').value = this.value;" <?php echo createLDImgSrc($root_path,'update.gif','0') ?> >
                        <?php }else{ ?>
                            <input type="image" id="submitted" name="submitted" value="1" style="cursor:pointer" onclick="$('submit_val').value = this.value;" <?php echo createLDImgSrc($root_path,'savedisc.gif','0') ?> >
                        <?php } ?>
						<a onClick="document.paramedit.reset(); return false;" href="#">
							<img id="cancel" name="cancel" src="../../gui/img/control/default/en/en_cancel.gif" border=0 alt="Cancel" title="Cancel" style="cursor:pointer">
						</a>
						<input type="hidden" name="mode" value="<?= ($ts)?'update':'save' ?>">
						</td>
					</tr>
				</table>
		</FONT>
			<p>
		</td>
	</tr>
</table>        
</form>
</BODY>
</HTML>
