<?php
//error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
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
$lang_tables[]='departments.php';
define('LANG_FILE','nursing.php');
$local_user='ck_pflege_user';
require_once($root_path.'include/inc_front_chain_lang.php');

require($root_path.'modules/nursing/ajax/nursing-station-new-common.php');

/* Load the ward object */
require_once($root_path.'include/care_api_classes/class_ward.php');
$ward_obj=new Ward;
/* Load the dept object */
require_once($root_path.'include/care_api_classes/class_department.php');
$dept=new Department;

$breakfile='nursing-station-manage.php'.URL_APPEND;

if($pday=='') $pday=date('d');
if($pmonth=='') $pmonth=date('m');
if($pyear=='') $pyear=date('Y');
$t_date=$pday.'.'.$pmonth.'.'.$pyear;

if($mode){
	$dbtable='care_ward';
			
	if(!isset($db)||!$db) include($root_path.'include/inc_db_makelink.php');
	if($dblink_ok){
		switch($mode)
		{	
			case 'create': 
			echo "nursing-station-new.php : mode=create : HTTP_POST_VARS <br> "; print_r($HTTP_POST_VARS); echo "<br> \n";
			exit();
			//$db->debug=1;
				/* check if ward already exists */
								if(!$ward_obj->IDExists($ward_id)){				
									if($ergebnis=$ward_obj->saveWard($HTTP_POST_VARS)){
										if($dbtype=='mysql'){
											$ward_nr=$db->Insert_ID();
										}else{
											$ward_nr=$ward_obj->postgre_Insert_ID($dbtable,'nr',$db->Insert_ID());
										}
										$HTTP_POST_VARS['ward_nr']=$ward_nr;
										if($ward_obj_obj->saveWardRoomInfoFromArray($HTTP_POST_VARS)){
											
										}
										header("location:nursing-station-new-createbeds.php?sid=$sid&lang=$lang&ward_nr=$ward_nr");
										exit;
									}else{echo "$sql<br>$LDDbNoSave";}
								}else{ $ward_exists=true;}
								break;
			case 'update':
				$ward_nr = $HTTP_POST_VARS['ward_nr'];
//echo "nursing-station-new.php : mode=update : HTTP_POST_VARS <br> "; print_r($HTTP_POST_VARS); echo "<br> \n";
				header("location:nursing-station.php".URL_REDIRECT_APPEND."&edit=1&ward_nr=$ward_nr&retpath=ward_mng");
				exit;
			break;
		}// end of switch
	}else{echo "$LDDbNoLink<br>";} 
}else{
	$depts=&$dept->getAllMedical();
}

//$ward_nr='13';
	/* Get the ward's data */
	$wardInfo=&$ward_obj->getWardInfo($ward_nr);
echo "nursing-station-new.php : wardInfo ='".$wardInfo."' <br><br> \n";
echo "nursing-station-new.php : wardInfo <br> "; print_r($wardInfo); echo "<br><br> \n";
echo "nursing-station-new.php : WARD INFO : ward_obj->sql ='".$ward_obj->sql."' <br> \n";
	$mode='create';
	if ($wardInfo){
#		$mode='update';
		$rs_room = $ward_obj->getAllActiveRoomsInfo($ward_nr);
echo "nursing-station-new.php : rs_room ='".$rs_room."' <br><br> \n";
echo "nursing-station-new.php : rs_room <br> "; print_r($rs_room); echo "<br><br> \n";
echo "nursing-station-new.php : ROOM INFO : ward_obj->sql ='".$ward_obj->sql."' <br> \n";
		if ($wardInfo['accomodation_type']=='1'){
				# charity accomodation
			$roomInfo = $rs_room->FetchRow();   # only ONE room for every Charity Ward
echo "nursing-station-new.php : roomInfo ='".$roomInfo."' <br><br> \n";
echo "nursing-station-new.php : roomInfo <br> "; print_r($roomInfo); echo "<br><br> \n";
			$room_nr = $roomInfo['room_nr'];
			$info_room = $roomInfo['info'];
			$nr_of_beds = $roomInfo['nr_of_beds'];
			$nr_of_beds = $ward_obj->countBeds($ward_nr);
		}
	}
# Start the smarty templating
 /**
 * LOAD Smarty
 */
 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('nursing');

# Added for the common header top block

 $smarty->assign('sToolbarTitle',"$LDCreate::$LDNewStation");

 $smarty->assign('pbHelp',"javascript:gethelp('nursing_ward_mng.php','new')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$LDCreate::$LDNewStation");

# Buffer page output

ob_start();

?>
<style type="text/css" name="formstyle">

td.pblock{ font-family: verdana,arial; font-size: 12}

div.box { border: solid; border-width: thin; width: 100% }

div.pcont{ margin-left: 3; }

</style>
<script type="text/javascript" language="javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>

<script language="javascript">
<!-- 
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

function checkWardForm(){
	var a_type = $F('accomodation_type');// given that a_type=2 means Payward Accomodation [in DB]

//alert("checkWardForm : Number("+$F('ward_rate')+") = '"+Number($F('ward_rate'))+"'");
//alert("checkWardForm : a_type = '"+a_type+"' \nisNaN(Number($F('room_nr'))) ='"+isNaN(Number($F('room_nr')))+"'");
alert("checkWardForm : a_type = '"+a_type+"'");
//alert("checkWardForm : isNaN(Number("+$F('room_nr')+")) ='"+isNaN(Number($F('room_nr')))+"'");

	if ($F('name')==""){
		alert("Please enter the name of the ward.");
		$('name').focus();
		return false;
	}else if ($F('ward_id')==""){
		alert("Please enter the ward id.");
		$('ward_id').focus();
		return false;
	}else if ( (a_type=="1")&&($F('dept_nr_temp')=="0") ){
		alert("Please select the department of the ward.");
		$('dept_nr').focus();
		return false;
	}else if ($F('description_temp')==""){
		alert("Please enter the description of the ward.");
		$('description_temp').focus();
		return false;
	}else if ( ($F('ward_rate')=="")||(isNaN(parseFloat($F('ward_rate')))) || (parseFloat($F('ward_rate'))<=0) ){
		$('ward_rate').value = "";
		alert("Please enter the rate of the ward.");
		$('ward_rate').focus();
		return false;
	}else if ( (a_type=="1")&& (($F('room_nr')=="") || (parseInt($F('room_nr'))<=0) ) ){
		alert("Please enter a Room Number.");
		$('room_nr').focus();
		return false;
	}else if ( (a_type=="1")&& (($F('nr_of_beds')=="") || (parseInt($F('nr_of_beds'))<=0)) ){
		$('nr_of_beds').value = "";
		alert("Please enter the number of beds of the ward.");
		$('nr_of_beds').focus();
		return false;
	}

	$('description').value=$F('description_temp');
	$('dept_nr').value=$F('dept_nr_temp');
	if (a_type=="2"){
			// given that a_type=2 means Payward Accomodation [in DB]
		$('dept_nr').value="0";
		var rooms = document.getElementsByName('rooms[]');
alert("checkWardForm : rooms.length ='"+rooms.length+"'\n$('count_room').value='"+$('count_room').value+"'");
		if (rooms.length==0){
			alert("Please enter some rooms for this ward.");
			return false;			
		}
		var beds = document.getElementsByName('beds[]');
		var infos = document.getElementsByName('infos[]');
		for(var i=0;i<rooms.length;i++){
			if ( (isNaN(parseInt(beds[i].value)))  || (parseInt(beds[i].value)<=0) ){
				beds[i].value="";
				alert("Please enter number of beds for room "+rooms[i].value+"!");
				beds[i].focus();
				return false;
			}
			if (infos[i].value==""){
				alert("Please enter the description of a room "+rooms[i].value+".");
				infos[i].focus();
				return false;
			}
		}//end of for loop
	}// end of if-stmt "if (a_type=="2")"
	/*if((d.description.value=="")||(d.dept_nr.value=="")||(d.ward_id=="")||(d.roomprefix.value==""))
	{
		alert("<?php echo $LDAlertIncomplete ?>");
		return false;
	}
	if(parseInt(d.room_nr_start.value)>=parseInt(d.room_nr_end.value)) 
	{
		alert("<?php echo $LDAlertRoomNr ?>");
		return false;
	}*/
//	xajax_saveWardRoom();
	var origInfo = new Array();
	var origInfo2 = new Array();
	origInfo['first'] = "burn";
	origInfo['second'] = "klinch";
	origInfo['last'] = "clarito";
	origInfo2['first'] = "burn";
	origInfo2['second'] = "klinch";
	origInfo2['last'] = "clarito";
	origInfo['array'] = origInfo2;
//	alert(" checkWardForm : origInfo = '"+origInfo+"'");
//	xajax_isWardIDExists($F('ward_id'),origInfo);
//return false;
	var dForm = $('newstat');
	alert(" checkWardForm : dForm = '"+dForm+"'");
	//var dBody=list.getElementsByTagName("tbody")[0];
	//var dRows = dBody.getElementsByTagName("tr");
	var dInputs = dForm.getElementsByTagName("input");
	alert(" checkWardForm : dInputs = '"+dInputs+"' \ndInputs.length='"+dInputs.length+"'");
	//return false;
	var msg ="\ndInputs = '"+dInputs+"' \ndInputs.length='"+dInputs.length+"'";
	var origFormInfo = new Array();
	var key='';
	for (i=0;i<dInputs.length;i++) {
		msg += "\n"+
				 "dInputs["+i+"] = '"+dInputs[i]+"' # "+
				 "dInputs["+i+"].name = '"+dInputs[i].name+"' # "+
				 "dInputs["+i+"].id = '"+dInputs[i].id+"' # "+
				 "dInputs["+i+"].value = '"+dInputs[i].value+"'";
		origFormInfo[dInputs[i].id] = dInputs[i].value;
	//	dRows[i].className = "wardlistrow"+(i%2+1);
	}	
	alert(" checkWardForm :"+msg);
	alert("checkWardForm : origFormInfo.length='"+origFormInfo.length+"' \norigFormInfo : \n"+origFormInfo);
	xajax_saveWardRoom(origFormInfo);
//	xajax_isWardIDExistsTest($F('ward_id'),origFormInfo);

	//	xajax_isWardIDExists($F('ward_id'),rooming);
	return false;
//	return true;
}

function checkFormRoom(f){
	var i;
	var v;
	var ret=true;
	var rooms = document.getElementsByName('rooms[]');
	
//	for(i=<?php echo $ward['room_nr_start']; ?>;i<=<?php echo $ward['room_nr_end']; ?>;i++){
	for(i=0;i<=rooms.length;i++){
		eval("v=f.beds"+i+".value;");
		if(isNaN(v)||v==""||v==" "||v=="  "||v<1||!v){
			ret=false;
			break;
		}
	}
	if(ret){
		return true;
	}else{
		alert("<?php echo $LDNrOfBedsRoom.' '.$ward['roomprefix']; ?> "+i+" <?php echo $LDIsNotANumber; ?>!");
		eval("f.beds"+i+".focus();");
		eval("f.beds"+i+".select();");
		return false;
	}
}
	function getElementsByClass(searchClass,node,tag) {
		var classElements = new Array();
		if ( node == null )
			node = document;
		if ( tag == null )
			tag = '*';
		var els = node.getElementsByTagName(tag);
		var elsLen = els.length;
		var pattern = new RegExp("(^|\\s)"+searchClass+"(\\s|$)");
		for (i = 0, j = 0; i < elsLen; i++) {
			if ( pattern.test(els[i].className) ) {
				classElements[j] = els[i];
				j++;
			}
		}
		return classElements;
	}

	function jsShowDetails(tr_className,showDetails){
		var personDetails = getElementsByClass(tr_className);
		for (var i=0; i< personDetails.length; i++){
			personDetails[i].style.display = (showDetails) ? '' : 'none';
		}
	}

function isPayward(type){
	alert("isPayward : type = '"+type+"' \n$('count_room').value='"+$('count_room').value+"'");
	$('accomodation_type').value=type;
	if(type == "2"){
			// given that accomodation_type=2 means Payward Accomodation [in DB]
		jsShowDetails('paywardOnly',true);
		jsShowDetails('charityOnly',false);
	}else{
		jsShowDetails('charityOnly',true);
		jsShowDetails('paywardOnly',false);
	}
}

	function clearRooms(list) {	
		if (list) {
			var dBody=list.getElementsByTagName("tbody")[0];
			if (dBody) {
				$('count_room').value = 0;
				dBody.innerHTML = "";
				return true;
			}
		}
		return false;
	}

	function reloadRoomInfo(beds_orig, infos_orig){
		var beds = document.getElementsByName('beds[]'),
			 infos = document.getElementsByName('infos[]');

		var msg = '';
alert("reloadRoomInfo : beds_orig ='"+beds_orig+"' \ninfos_orig ='"+infos_orig+"' \nbeds_orig.length ='"+beds_orig.length+"'");	
		for(var i=0;i<beds_orig.length;i++){
			msg += "beds_orig["+i+"] ='"+beds_orig[i]+"' # " +
					"infos_orig["+i+"]='"+infos_orig[i]+"' \n";
			beds[i].value = beds_orig[i];
			infos[i].value = infos_orig[i];
		}//end of for loop
		alert("reloadRoomInfo : "+msg);
	}

	function saveTempRoomInfo(wardInfo){
		var origInfo = new Array();
		var msg='';

alert("saveTempRoomInfo : wardInfo ='"+wardInfo+"' \nwardInfo.length ='"+wardInfo.length+"'");	
		for(var i=0;i<wardInfo.length;i++){
			alert("saveTempRoomInfo : inside for(wardInfo.length) : \n wardInfo["+i+"].value ='"+wardInfo[i].value+"'");
			msg += "wardInfo["+i+"].value ='"+wardInfo[i].value+"'";
			origInfo[i] = wardInfo[i].value;
		}//end of for loop
		alert("saveTempRoomInfo : \n"+msg);
		return origInfo;
	}

	function appendRoom(list,details) {
alert("appendRoom : list : \n"+list);
		if (list) {
			var dBody=list.getElementsByTagName("tbody")[0];
alert("appendRoom : dBody : \n"+dBody);
			var beds = document.getElementsByName('beds[]'),
				 infos = document.getElementsByName('infos[]');
			var beds_orig = new Array(), 
				 infos_orig = new Array();
				 beds_orig = saveTempRoomInfo(beds); 
		 		 infos_orig = saveTempRoomInfo(infos);
		var msg = '';
		msg += "beds ='"+beds+"' # beds.length ='"+beds.length+"' \n"+
				"infos ='"+infos+"' # infos.length ='"+infos.length+"' \n";
		for(var i=0;i<beds.length;i++){
//			msg += "beds_orig["+i+"].value ='"+beds_orig[i].value+"' # " +
//					"info_orig["+i+"].value='"+info_orig[i].value+"' \n";
			msg += "beds["+i+"].value ='"+beds[i].value+"' # " +
					"infos["+i+"].value ='"+infos[i].value+"' \n";
//			beds[i].value = beds_orig[i].value;
//			info[i].value = info_orig[i].value;
		}//end of for loop
alert("appendRoom : beds&infos : \n"+msg);			

		msg = "beds_orig ='"+beds_orig+"' # beds_orig.length ='"+beds_orig.length+"' \n"+
				"infos_orig ='"+infos_orig+"' # infos_orig.length ='"+infos_orig.length+"' \n";
		for(var i=0;i<beds_orig.length;i++){
			msg += "beds_orig["+i+"] ='"+beds_orig[i]+"' # " +
					"infos_orig["+i+"] ='"+infos_orig[i]+"' \n";
		}//end of for loop
alert("appendRoom : beds_orig&infos_orig : \n"+msg);			
			
			if (dBody) {
				var src;
				var rooms = document.getElementsByName('rooms[]'),
						dRows = dBody.getElementsByTagName("tr");
			
				if (details) {
					var id = details.no;
					if (rooms) {
						for (var i=0;i<rooms.length;i++) {
							if (rooms[i].value == details.no) {
								alert("Room No. "+id+" is already on the list!");
								return true;
							}
						}
						if (rooms.length == 0)
							clearRooms(list);
					}
	
					alt = (dRows.length%2)+1;
					var deleteImg = 'src="../../gui/img/control/default/en/en_trash_06.gif" border=0 width="20" height="21"';
					
					src = "\n"+
						'<tr class="wardlistrow'+alt+'" id="row'+id+'" style="font-weight:bold;padding:0px"> '+
						'	<input type="hidden" id="rowID'+id+'" value="'+id+'">'+
						'	<td><font face="verdana,arial" size="2" > '+
						'		<input type="hidden" name="rooms[]" id="rooms'+id+'" value="'+id+'">'+id+
						'	</td> '+
						'	<td align="center"> '+
						'		<input type="text" name="beds[]" id="beds'+id+'" onBlur="trimString(this);" size="8" maxlength="3" value=""> '+
						'	</td> '+
						'	<td> '+
						'		<input type="text" name="infos[]" id="infos'+id+'" onBlur="trimString(this);" size=50 maxlength=100 value=""> '+
						'	</td> '+
						'	<td align="center"> '+
						'		<img name="delete'+id+'" id="delete'+id+'" '+deleteImg+' onClick="removeRoom('+id+');" alt="Delete"> '+
						'	</td> ';
					src +='</tr>';
					$('count_room').value = parseInt($('count_room').value) + 1;
				}// end of if-stmt 'if (details)'
				else {
					src = "									<tr> "+
							"											<td colspan=\"4\" align=\"center\" bgcolor=\"#FFFFFF\" style=\"color:#FF0000; font-family:'Arial', Courier, mono; font-style:Bold; font-weight:bold; font-size:12px;\"> "+
							"												List of rooms is currently empty... "+
							"											</td> "+
							"										</tr> ";
				}
alert("appendRoom : src : \n"+src+" \n$('count_room').value='"+$('count_room').value+"'");
				dBody.innerHTML += src;
alert("appendRoom : dBody.innerHTML : \n"+dBody.innerHTML+"'");
				reloadRoomInfo(beds_orig, infos_orig);   //restore the room info values
				return true;
			}
		}
		return false;
	}

function addRoom(){
//	do{
		var room_no = prompt("Please enter the room number/name.",'default');
//	}while(isNaN(parseInt(room_no))||!(room_no==null));
	var msg ="room_no ='"+room_no+"' \n"+
				"parseInt("+room_no+")='"+parseInt(room_no)+"'\n"+
				"isNaN(parseInt("+room_no+"))='"+isNaN(parseInt(room_no))+"'\n"+
				"(room_no==null) : "+(room_no==null)+"'\n"+
				"!(room_no==null) : "+!(room_no==null)+"'\n";
//	alert("addRoom : \n"+msg);
	if (!(room_no==null)){
		//OK button is clicked
		if ( (isNaN(parseInt(room_no))) || (parseInt(room_no)<=0) ){
			alert("Room number is should be a NUMBER.");
			return false;
		}
//		alert("addRoom : OK button is clicked & VALID room number!");
		var details = new Object;
		details.no = room_no;
		appendRoom($('room-list'),details);	
	}else{
		alert("addRoom : Cancel button is clicked");
	}
}

function emptyIntialRoomList(){
	clearRooms($('room-list'));
	appendRoom($('room-list'),null);
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

function removeRoom(id) {
	alert("removeRoom : id ='"+id+"'");
//	return;
	var destTable, destRows;
	var table = $('room-list');
	var rmvRow=document.getElementById("row"+id);
	if (table && rmvRow) {
		$('rowID'+id).parentNode.removeChild($('rowID'+id));
//		$('rooms'+id).parentNode.removeChild($('rooms'+id));
//		$('beds'+id).parentNode.removeChild($('beds'+id));		
//		$('info'+id).parentNode.removeChild($('infos'+id));
		var rndx = rmvRow.rowIndex-1;
		table.deleteRow(rmvRow.rowIndex);
		reclassRows(table,rndx);
		$('count_room').value = parseInt($('count_room').value) - 1;
	}
	var rooms = document.getElementsByName('rooms[]');
	alert("removeRoom : rooms.length ='"+rooms.length+"'\n$('count_room').value='"+$('count_room').value+"'");
	if (rooms.length == 0){
		emptyIntialRoomList();
	}
}

function wardIdExists(){
	alert("Ward ID is already existing!");
	$('ward_id').select();
}

function changeMode(newMode,ward_nr){
	if (newMode=='create'){
		$('createButton').style.display = '';
		$('updateButton').style.display = 'none';
	}
	if (newMode=='update'){
		$('ward_nr').value=ward_nr;
		if ($('viewstat').submit)
			$('viewstat').submit();
		$('updateButton').style.display = '';
		$('createButton').style.display = 'none';
	}
	alert("changeMode : newMode ='"+newMode+"' \n$F('ward_nr') = '"+$F('ward_nr')+"'");
}

// -->
</script>

<?php

$xajax->printJavascript($root_path.'classes/xajax-0.2.5');

$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

# Assign prompt elements
if($rows){
	$smarty->assign('sMascotImg','<img '.createMascot($root_path,'mascot1_r.gif','0','bottom').' align="absmiddle">');
	$smarty->assign('sStationExists',str_replace("~station~",strtoupper($station),$LDStationExists));
}

$smarty->assign('LDEnterAllFields',$LDEnterAllFields);

#Assign Radio button Selection for Payward  & charity
$accomodation_list = $ward_obj->getAllAccomodationTypeDataObject();
#echo "nursing-station-new.php : ward_obj->sql = '".$ward_obj->sql."'<br> \n";
#echo "nursing-station-new.php : accomodation_list : <br>"; print_r($accomodation_list); echo "<br> \n";
$seg_temp = '';
if ($accomodation_list){
	$a_type = $wardInfo['accomodation_type']? $wardInfo['accomodation_type']:'1';
	while($result = $accomodation_list->FetchRow()){	
		$isChecked = '';
		if ($a_type==$result['accomodation_nr'])
			$isChecked = ' checked';
		$seg_temp .= '		<input type="radio"'.$isChecked.' id="accomodation_type_temp" name="accomodation_type_temp" value="'.$result['accomodation_nr'].'" onclick="isPayward('.$result['accomodation_nr'].')"><b>'.strtoupper($result['accomodation_name']).'</b> &nbsp;&nbsp;&nbsp;'."\n";
	}#end of while loop
}
#$smarty->assign('sAccTypeRadio1','<input type="radio" id="accomodation_type" name="accomodation_type" value="2" onclick="isPayward(this)"/><b>PAYWARD</b>');
#$smarty->assign('sAccTypeRadio2','<input type="radio" id="accomodation_type" name="accomodation_type" value="1" onclick="isPayward(this)" /><b>CHARITY</b>');
$smarty->assign('sAccTypeRadio',$seg_temp);
$smarty->assign('isPayWard', TRUE);
$required_fld = "&nbsp;<font color=#ff0000><b>*</b></font>&nbsp;";

# Assign form items
$smarty->assign('LDAccomodationType',$required_fld."Accomodation type ");
$smarty->assign('LDStation',$required_fld.$LDStation);
$smarty->assign('LDWard_ID',$required_fld.$LDWard_ID);
$smarty->assign('LDDept',$required_fld.$LDDept);
$smarty->assign('LDPlsSelect',$LDPlsSelect);
$smarty->assign('LDNoSpecChars',$LDNoSpecChars);
$smarty->assign('LDDescription',$required_fld.$LDDescription);
$smarty->assign('LDRoom1Nr',$LDRoom1Nr);
$smarty->assign('LDRoom2Nr',$LDRoom2Nr);
$smarty->assign('LDWardRate',$required_fld."Ward Rate");
$smarty->assign('LDNoOfBeds',$required_fld."No. of Beds");
$smarty->assign('LDRoomPrefix',$LDRoomPrefix);
$smarty->assign('sSelectIcon','<img '.createComIcon($root_path,'l_arrowgrnsm.gif','0').'>');
$smarty->assign('LDRoomNr',$required_fld.'Room Number');
$smarty->assign('LDRoomInfo','Room Description');

# Assign input values
$smarty->assign('segName','<input type="text" name="name" id="name" size=20 maxlength=40 value="'.$wardInfo['name'].'">');
$smarty->assign('segWardID','<input type="text" name="ward_id" id="ward_id" size=20 maxlength=40 value="'.$wardInfo['ward_id'].'"> [A-Z0-9,1-0]');
$smarty->assign('segDescription','<textarea name="description_temp" id="description_temp" onChange="trimString(this);" onBlur="trimString(this);" cols=40 rows=5 wrap="physical">'.$wardInfo['description'].'</textarea>');
$smarty->assign('segWardRate','<input type="text" name="ward_rate" id="ward_rate" onBlur="trimString(this); if(isNaN(Number(this.value))) this.value=\'\';" size="6" maxlength="6" value="'.$wardInfo['ward_rate'].'">');
$smarty->assign('segNrOfBeds','<input type="text" name="nr_of_beds" id="nr_of_beds" onBlur="trimString(this); if( (isNaN(Number(this.value))) || (this.value==\'\')) this.value=\'\'; else this.value=parseInt(this.value);" size=4 maxlength=4 value="'.$nr_of_beds.'">');
$smarty->assign('segRoomPrefix','<input type="text" name="roomprefix" id="roomprefix" onBlur="trimString(this);" size=4 maxlength=4 value="'.$wardInfo['roomprefix'].'">');
$smarty->assign('segRoomNr','<input type="text" name="room_nr" id="room_nr" onBlur="trimString(this); if( (isNaN(Number(this.value))) || (this.value==\'\')) this.value=\'\'; else this.value=parseInt(this.value);" size=4 maxlength=4 value="'.$room_nr.'">');
$smarty->assign('segRoomInfo','<input type="text" name="info_room" id="info_room" onBlur="trimString(this);" size=50 maxlength=100 value="'.$info_room.'">');
/*
$smarty->assign('name',$wardInfo['name']);
$smarty->assign('ward_id',$wardInfo['ward_id']);
$smarty->assign('description',$wardInfo['description']);
$smarty->assign('room_nr_start',$wardInfo['room_nr_start']);
$smarty->assign('room_nr_end',$wardInfo['room_nr_end']);
$smarty->assign('roomprefix',$wardInfo['roomprefix']);
*/
# Create department select box
$sTemp = '<select name="dept_nr_temp" id="dept_nr_temp">
			<option value="0">--Select Department--</option>';

if($depts&&is_array($depts)){
	while(list($x,$v)=each($depts)){
		$sTemp = $sTemp.'	
		<option value="'.$v['nr'].'"';
		if($v['nr']==$wardInfo['dept_nr']) $sTemp = $sTemp.' selected';
		$sTemp = $sTemp.'>';
		if(isset($$v['LD_var'])&&$$v['LD_var']) $sTemp = $sTemp.$$v['LD_var'];
			else $sTemp = $sTemp.$v['name_formal'];
		$sTemp = $sTemp.'</option>';
	}
}
$sTemp = $sTemp.'
	</select>';

	$smarty->assign('sDeptSelectBox',$sTemp);

$smarty->assign('sCancel','<a href="javascript:history.back()"><img '.createLDImgSrc($root_path,'cancel.gif','0').' border="0"></a>');
$smarty->assign('sSaveButton','<input type="hidden" name="sid" id="sid" value="'.$sid.'">
<input type="hidden" name="mode" id="mode" value="'.($mode?$mode:'create').'">
<input type="hidden" name="edit" id="edit" value="'.$edit.'">
<input type="hidden" name="lang" id="lang" value="'.$lang.'">
<input type="hidden" name="dept_nr" id="dept_nr" value="'.$wardInfo['dept_nr'].'">
<input type="hidden" name="description" id="description" value="'.$wardInfo['description'].'">
<input type="hidden" name="count_room" id="count_room" value="'.($count_room?$count_room:0).'">
<input type="hidden" name="accomodation_type" id="accomodation_type" value="'.($wardInfo['accomodation_type']? $wardInfo['accomodation_type']:'1').'">
<input type="submit" name="createButton" id="createButton" value="'.$LDCreateStation.'">
<input type="submit" name="updateButton" id="updateButton" value="Update Ward">
');
/*
$sTemp='
		<script language="javascript">
			isPayward('.<?= $wardInfo['accomodation_type']? $wardInfo['accomodation_type']:'1' ?>.');
		</script>';
*/
$a_type = $wardInfo['accomodation_type']? $wardInfo['accomodation_type']:'1';
$sTemp='
		<script language="javascript">
			isPayward('.$a_type.');
			changeMode("'.($mode?$mode:'create').'");
		</script>';

$smarty->assign('segInitialization',$sTemp);
$sTemp  = '<img '.createLDImgSrc($root_path,'add_room.gif').' onClick="addRoom();" alt="Add room">';
$smarty->assign('segAddRoom',$sTemp);

$smarty->assign('sMainBlockIncludeFile','nursing/ward_create_form.tpl');

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');

?>
