<?php

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

require_once($root_path.'include/care_api_classes/class_department.php');
$dept_obj=new Department;

#$dept = $dept_obj->getDepartmentInfo("name_formal like 'pathology'", "name_formal");

require($root_path.'include/inc_labor_param_group.php');

# Load the date formatter */
include_once($root_path.'include/inc_date_format_functions.php');

$excode=urlencode($_GET['nr']);
$grpcode =$_GET['grpcode'];
$deptnr = $_GET['deptnr'];
#echo "grpcode = ".$grpcode;
#echo "<br>deptnr = ".$deptnr;
#echo "<br>groupid = ".$_POST['groupid'];
#echo "xcode encode= '".urlencode($_GET['nr'])."'<br>";
#echo "xcode decode= '".urldecode($_GET['nr'])."'<br>";
#echo "xcode orig= '".$_GET['nr']."'<br>";

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
	
	$socialized = (isset($_POST['is_socialized']))?1:0;
	
	$xgid=$_POST['groupcode'];
	
	#-------------
	#echo "rowname = ".$_POST['nameselRow1'];
	#echo "<br>rowprice = ".$_POST['dpriceRow1'];
	#echo "row = ".$_POST['totalrow'];
	
	$serv_discount = array();
	for ($i=1; $i<=$_POST['totalrow']; $i++){
		$name = 'nameselRow'.$i;
		$price = 'dpriceRow'.$i;
		#echo "<br>rowname$i : $name = ".$_POST[$name];
		#echo "<br>rowprice$i : $price = ".$_POST[$price];
		
		$serv_discount[$i-1]['discount'] = $_POST[$name];
		$serv_discount[$i-1]['price'] = $_POST[$price];
	}
	
	#print_r($dept);
	#print_r($serv_discount);
	#echo "dept = ".$dept['nr'];
	$srv->deleteServiceDiscounts($xcode,$dept_nr);
	#echo "<br>sql delete = ".$srv->sql;
	$srv->AddServiceDiscounts($serv_discount,$xcode,$dept_nr);
	#echo "<br>sql add = ".$srv->sql;
	#--------------


	if ($srv->updateRadioService($_POST['excode'],$xcode, $xname, $xcash, $xcharge, $xstatus, $xgid, $socialized)) {
		# xrow(rowno, code, name, cash, charge)
		$cd=$_POST['service_code']?$_POST['service_code']:'';
		$nm=$_POST['name']?$_POST['name']:'';
		$csh=$_POST['cash']?$_POST['cash']:'null';
		$chrg=$_POST['charge']?$_POST['charge']:'null';
		$dpt=$dept_nr?$dept_nr:'null';
		#$social = (isset($_POST['is_socialized']))?1:0;
		$xrowArg = $_POST['row'].",'$cd','$nm',$csh,$chrg,$dpt";
?>

<script language="JavaScript">
<!-- Script Begin
window.opener.xrow(<?= $xrowArg ?>);
//alert("xrow"+<?=$xrowArg ?>);
window.close();  //commented by van
//  Script End -->
</script>

<?php
		exit; #commented by van
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

if($tsrv=&$srv->getRadioServicesInfo("service_code='".addslashes(urlencode($nr))."' AND s.group_code = sg.group_code")){
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
		
		for(i=1; i < rows_len; i++){
			var disc_name = 'nameselRow'+i;
			var disc_price = 'dpriceRow'+i;
			//alert(document.getElementById('discount_name').value + "==" + document.getElementById(disc_name).value);
			if (document.getElementById('discount_name').value == document.getElementById(disc_name).value){
				alert("The laboratory service is already in the discount table. If you want to edit the price, just edit the price in the textbox");
				document.getElementById(disc_price).focus();
				// reset 
				document.getElementById("discount_name").value = 0;  
				document.getElementById("discount_price").value = " ";
				bol = false;
				break;
			}
		}
	}
	
	//alert("bol = "+bol);

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
	 
//	 alert(tbl.innerHTML);
}

function preLoad(){
	//alert("preLoad");
	document.getElementById("totalrow").value = document.getElementById('discount_table').rows.length-1;
	document.getElementById("dept_nr").value = <?=$deptnr;?>;
}

function social_status(){
   if (document.getElementById('is_socialized').checked)
		//alert("socialized service");
		document.getElementById('social_service').style.display = '';
	else	
		//alert("non-socialized service");
		document.getElementById('social_service').style.display = 'none';
}

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

<form action="<?php echo $thisfile; ?>" method="post" name="paramedit">

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
	</tr>
	<tr align="center">
		<td  bgcolor=#dde1ec colspan=2>
			<FONT SIZE=-1 FACE="Arial">
				<table border=0 bgcolor=#ffdddd cellspacing=1 cellpadding=1 width="100%">
					<tr>
						<td bgcolor=#ff0000 colspan=2>
							<FONT SIZE=2 FACE="Verdana,Arial" color="#ffffff">
							<b><?php echo $ts['grpname']; #echo $parametergruppe[$ts['group_id']]; ?></b>
							</font>
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
										<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">Service code</td>
										<td bgcolor="#ffffee" class="a12_b">
											<input type="text" name="service_code" id="service_code" size=35 maxlength=10 style="width:100%" value="<?= $ts['service_code'] ?>">
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
											<input type="text" name="cash" id="cash" size=35 maxlength=30 style="width:100%" onBlur="formatprice(this);" value="<?= $ts['price_cash'] ?>">
										</td>
									</tr>
									<tr>
										<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">Price(Charge)</td>
										<td bgcolor="#efefef" class="a12_b">
											<input type="text" name="charge" id="charge" size=35 maxlength=30 style="width:100%" onBlur="formatprice(this);" value="<?= $ts['price_charge'] ?>">
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
										$serv_disc = &$srv->getServiceDiscount("service_code='".addslashes($nr)."' AND dept_nr = '".addslashes($dept)."'","discountid");
										#echo "sql = ".$srv->sql;	
											
										if(is_object($serv_disc)){
											$i=1;
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
										}#end of if-stmt 'if(is_object($serv_disc))'
?>
												</tbody>
											</table>
										</td>	
									</tr>
									</tbody>
									<tr>
										<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">Status</td>
										<td bgcolor="#ffffee" class="a12_b">
											<textarea name="status" id="status" cols="35" rows="2" style="width:100%" wrap="hard"><?= $ts['status'] ?></textarea>
										</td>
									</tr>
<?php
}#end of if-stmt 'if($ts)'
?>
								</table>
								<input type=hidden name="nr" value="<?php echo $nr; ?>">
								<input type=hidden name="sid" value="<?php echo $sid; ?>">
								<input type=hidden name="lang" value="<?php echo $lang; ?>">
								<input type=hidden name="mode" value="save">
								<input type=hidden name="excode" value="<?= $excode ?>">
								<input type=hidden name="row" value="<?= $row ?>">
								<input type=hidden name="groupcode" value="<?= $ts['group_code']  ?>">
								<input type="hidden" name="totalrow" id="totalrow" value="<?=$totalrow?>">
								<input type="hidden" name="dept_nr" id="dept_nr" value="<?= $dept_nr; ?>">
								<input  type="image" <?php echo createLDImgSrc($root_path,'savedisc.gif','0') ?>> 
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
