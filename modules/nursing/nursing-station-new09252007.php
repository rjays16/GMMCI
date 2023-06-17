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
/* Load the ward object */
require_once($root_path.'include/care_api_classes/class_ward.php');
$ward=new Ward;
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
			echo "nursing-station-new.php : HTTP_POST_VARS <br> "; print_r($HTTP_POST_VARS); echo "<br> \n";
			//$db->debug=1;
				/* check if ward already exists */
								if(!$ward->IDExists($ward_id)){				
									if($ergebnis=$ward->saveWard($HTTP_POST_VARS)){
										if($dbtype=='mysql'){
											$ward_nr=$db->Insert_ID();
										}else{
											$ward_nr=$ward->postgre_Insert_ID($dbtable,'nr',$db->Insert_ID());
										}
										header("location:nursing-station-new-createbeds.php?sid=$sid&lang=$lang&ward_nr=$ward_nr");
										exit;
									}else{echo "$sql<br>$LDDbNoSave";}
								}else{ $ward_exists=true;}
								break;
		}// end of switch
	}else{echo "$LDDbNoLink<br>";} 
}else{
	$depts=&$dept->getAllMedical();
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

function check(d)
{
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

	function appendRoom(list,details) {
//alert("appendRoom : list : \n"+list);
		if (list) {
			var dBody=list.getElementsByTagName("tbody")[0];
//alert("appendRoom : dBody : \n"+dBody);
			if (dBody) {
				var src;
				var rooms = document.getElementsByName('rooms[]'),
						dRows = dBody.getElementsByTagName("tr");
			
				if (details) {
					var id = details.no;
					if (rooms) {
						for (var i=0;i<rooms.length;i++) {
							if (rooms[i].value == details.no) {
								alert('Room No. is already existing!');
								return true;
							}
						}
						if (rooms.length == 0)
							clearRooms(list);
					}
	
					alt = (dRows.length%2)+1;
					
					src = 
						'<tr class="wardlistrow'+alt+'" id="row'+id+'" style="font-weight:bold;padding:0px"> '+
						'	<input type="hidden" name="rooms[]" id="rowID'+id+'" value="'+id+'" />'+
						'	<td align="center"><b> '+(parseInt(id)+1)+'</b></td> '+
						'	<td id="docName'+id+'"><b> '+details.docName+' </b></td> '+
						'	<td id="finding'+id+'"><b> '+details.finding+' </b></td> '+
						'	<td id="r_impress'+id+'"><b> '+details.r_impression+' </b></td> '+
						'	<td id="f_date'+id+'"><b> '+details.f_date+' </b></td> ';
					if (details.status!='done'){
						var editImg = 'src="../../gui/img/control/default/en/en_edit_icon_06.gif" border=0 width="20" height="21"';
						var deleteImg = 'src="../../gui/img/control/default/en/en_trash_06.gif" border=0 width="20" height="21"';
						
						src +=
							'	<td align="center"> '+
							'		<a href="javascript:void(0);" '+
							'			onclick="return overlib( '+
							'				OLiframeContent(\''+details.f_link+'\', 500, 475, \'if1\', 1, \'auto\'), '+
							'					WIDTH,500, TEXTPADDING,0, BORDER,0,  '+
							'					STICKY, SCROLL, CLOSECLICK, MODAL, DRAGGABLE, '+
							'					CLOSETEXT, \'<img src=../../images/x.gif border=0>\', '+
							'					CAPTIONPADDING,4, CAPTION,\'Update findings\', MIDX,0, MIDY,0,  '+
							'					STATUS,\'Update findings\');" '+
							'			onmouseout="nd();"> '+
							'			<img name="edit'+details.no+'" id="edit'+details.no+'" '+editImg+'> '+
							'		</a> '+
							'	</td> '+
							'	<td align="center"> '+
							'		<img name="delete'+id+'" id="delete'+id+'" '+deleteImg+' onClick="deleteFinding('+details.batch_nr+','+id+');"> '+
							'	</td> ';
/*
							'					CLOSETEXT, \'<img src=<?= $root_path ?>images/x.gif border=0>\', '+
							'			<img name="edit'+details.no+'" id="edit'+details.no+'" <?= createLDImgSrc($root_path,'edit_icon_06.gif','0') ?>> '+
							'		<img name="delete'+id+'" id="delete'+id+'" <?= createLDImgSrc($root_path,'trash_06.gif','0') ?> onClick="deleteFinding('+details.batch_nr+','+id+');"> '+
						$('referralButton').style.display = '';
						$('saveButton1').style.display = '';
						$('saveButton2').style.display = '';
						$('saveDoneButton').style.display = '';
*/						
					}//end of if-stmt "if (details.status!='done')"
					src +='</tr>';
					$('count_room').value = parseInt($('count_room').value) + 1;
				}// end of if-stmt 'if (details)'
				else {
//					src = "<tr><td colspan=\"7\">List of findings is currently empty...</td></tr>";	
					src = "									<tr> "+
							"											<td colspan=\"7\" align=\"center\" bgcolor=\"#FFFFFF\" style=\"color:#FF0000; font-family:'Arial', Courier, mono; font-style:Bold; font-weight:bold; font-size:12px;\"> "+
							"												List of findings is currently empty... "+
							"											</td> "+
							"										</tr> ";
				}
//alert("appendFinding : src : \n"+src);
				dBody.innerHTML += src;
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
	alert("addRoom : \n"+msg);
	if (!(room_no==null)){
		//OK button is clicked
		if (isNaN(parseInt(room_no))){
			alert("Room number is should be a NUMBER.");
			return false;
		}
		var details = object;
		alert("addRoom : OK button is clicked & VALID room number!");
	}else{
		alert("addRoom : Cancel button is clicked");
	}
}

function emptyIntialRoomList(){
	clearOrder($('room-list'));
	appendOrder($('room-list'),null);
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
	return;
	var destTable, destRows;
	var table = $('room-list');
	var rmvRow=document.getElementById("row"+id);
	if (table && rmvRow) {
		$('rowID'+id).parentNode.removeChild($('rowID'+id));
		$('rowPrcCash'+id).parentNode.removeChild($('rowPrcCash'+id));
		$('rowPrcCharge'+id).parentNode.removeChild($('rowPrcCharge'+id));		
		$('rowQty'+id).parentNode.removeChild($('rowQty'+id));
		var rndx = rmvRow.rowIndex-1;
		table.deleteRow(rmvRow.rowIndex);
		reclassRows(table,rndx);
	}
		//burn added : September 13, 2007
	var rooms = document.getElementsByName('rooms[]');
	if (rooms.length == 0){
		emptyIntialRoomList();
	}
	refreshTotal();
}




// -->
</script>

<?php

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
$accomodation_list = $ward->getAllAccomodationTypeDataObject();
#echo "nursing-station-new.php : ward->sql = '".$ward->sql."'<br> \n";
#echo "nursing-station-new.php : accomodation_list : <br>"; print_r($accomodation_list); echo "<br> \n";
$seg_temp = '';
if ($accomodation_list){
	$a_type = $wardInfo['accomodation_type']? $wardInfo['accomodation_type']:'1';
	while($result = $accomodation_list->FetchRow()){	
		$isChecked = '';
		if ($a_type==$result['accomodation_nr'])
			$isChecked = ' checked';
		$seg_temp .= '		<input type="radio"'.$isChecked.' id="accomodation_type" name="accomodation_type" value="'.$result['accomodation_nr'].'" onclick="isPayward('.$result['accomodation_nr'].')"><b>'.strtoupper($result['accomodation_name']).'</b> &nbsp;&nbsp;&nbsp;'."\n";
	}#end of while loop
}
#$smarty->assign('sAccTypeRadio1','<input type="radio" id="accomodation_type" name="accomodation_type" value="2" onclick="isPayward(this)"/><b>PAYWARD</b>');
#$smarty->assign('sAccTypeRadio2','<input type="radio" id="accomodation_type" name="accomodation_type" value="1" onclick="isPayward(this)" /><b>CHARITY</b>');
$smarty->assign('sAccTypeRadio',$seg_temp);
$smarty->assign('isPayWard', TRUE);

# Assign form items
$smarty->assign('LDStation',$LDStation);
$smarty->assign('LDWard_ID',$LDWard_ID);
$smarty->assign('LDDept',$LDDept);
$smarty->assign('LDPlsSelect',$LDPlsSelect);
$smarty->assign('LDNoSpecChars',$LDNoSpecChars);
$smarty->assign('LDDescription',$LDDescription);
$smarty->assign('LDRoom1Nr',$LDRoom1Nr);
$smarty->assign('LDRoom2Nr',$LDRoom2Nr);
$smarty->assign('LDRoomPrefix',$LDRoomPrefix);
$smarty->assign('sSelectIcon','<img '.createComIcon($root_path,'l_arrowgrnsm.gif','0').'>');

# Assign input values
$smarty->assign('name',$name);
$smarty->assign('ward_id',$ward_id);
$smarty->assign('description',$description);
$smarty->assign('room_nr_start',$room_nr_start);
$smarty->assign('room_nr_end',$room_nr_end);
$smarty->assign('roomprefix',$roomprefix);

# Create department select box
$sTemp = '<select name="dept_nr">
			<option value=""> </option>';

if($depts&&is_array($depts)){
	while(list($x,$v)=each($depts)){
		$sTemp = $sTemp.'	
		<option value="'.$v['nr'].'"';
		if($v['nr']==$dept_nr) $sTemp = $sTemp.' selected';
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
$smarty->assign('sSaveButton','<input type="hidden" name="sid" value="'.$sid.'">
<input type="hidden" name="mode" value="create">
<input type="hidden" name="edit" value="'.$edit.'">
<input type="hidden" name="lang" value="'.$lang.'">
<input type="hidden" name="count_room" id="count_room" value="'.($count_room?$count_room:0).'">
<input type="submit" value="'.$LDCreateStation.'">');
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
		</script>';

$smarty->assign('segInitialization',$sTemp);
$sTemp  = '<img '.createLDImgSrc($root_path,'add_room.gif').' onClick="addRoom();" alt="Add room">';
$smarty->assign('segAddRoom',$sTemp);

$sTemp  = '<img '.createLDImgSrc($root_path,'trash_06.gif').' onClick="removeRoom(0);" alt="Delete">';
$smarty->assign('segDeleteRoom',$sTemp);

$smarty->assign('sMainBlockIncludeFile','nursing/ward_create_form.tpl');

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');

?>
