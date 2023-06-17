<script language="JavaScript">
<!-- Script Begin
function clientPrepareDelete(prid, prentrynum) {
	changeMode("deldetails");
	changeFormValue("editpid",prid);
	changeFormValue("editpentrynum",prentrynum);
	formSubmit();
}

function clientPrepareEdit(prid,prentrynum,prname,prqty,prppk,prpack) {
	changeMode("editdetails");
	changeFormValue("editpid",prid);
	changeFormValue("editpentrynum",prentrynum);
	changeFormValue("editpname",prname);
	changeFormValue("editpqty",prqty);
	changeFormValue("editppk",prppk);
	changeFormValue("editppack",prpack);
	formSubmit();
}

function changeFormValue(id,newvalue) {
	document.getElementById(id).value = newvalue;
}

function formSubmit()
{
	document.forms[0].submit();
}

function changeMode(newmode) {
	document.getElementById("modeval").value = newmode;
}

function validateDetailsSubmit(frm) {
	if (frm.prodid.value=='') {
		alert("Select a product.");
		return false;		
	}
	
	if (isNaN(frm.prodqty.value)||frm.prodqty.value<=0) {
		alert("Invalid product quantity.");
		frm.prodqty.focus();
		return false;
	}
	
	if (isNaN(frm.prodppk.value)||frm.prodppk.value<=0) {
		alert("Invalid product quantity.");
		frm.prodppk.focus();
		return false;
	}
	
	if (frm.produnit.value=='') {
		alert("Enter the package type.");
		frm.produnit.focus();
		return false;		
	}
	return true;
}



function prufform(d){
  if (d.refno.value=='') {
		alert("Enter the reference no.");
		d.refno.focus();
		return false;
	}
	
	if (d.purchasedt.value=='') {
		alert("Enter the date of purchase.");
		d.purchasedt.focus();
		return false;
	}
	
	if (d.pname.value=='') {
		alert("Enter the payer's name.");
		d.pname.focus();
		return false;
	}
	
	return true;
	
	//var nmin="<?php echo $LDAlertInvalidMinorder ?>";
	//var nmax="<?php echo $LDAlertInvalidMaxorder ?>";
	//var noneg="<?php echo $LDAlertNoNegativeOrder ?>";
	
	
	/*
	if(d.mode.value=="search") return true;
	
	if(d.bestellnum.value=="")
	{
		alert("<?php echo $LDAlertNoOrderNr ?>");
		d.bestellnum.focus();
		return false;
	}
	
	if(d.artname.value=="")
	{
		alert("<?php echo $LDAlertNoArticleName ?>");
		d.artname.focus();
		return false;
	}
	if(d.besc.value=="")
	{
		alert("<?php echo $LDAlertNoDescription ?>");
		d.besc.focus();
		 return false;
	}
	//if(d.minorder.value=="") d.minorder.value=0;
	if(isNaN(d.minorder.value))
	{
		alert(nmin);
		d.minorder.focus();
		 return false;
	}
	minO=parseInt(d.minorder.value);
	if(minO<0)
	{
		alert(noneg);
		d.minorder.focus();
		 return false;
	}
	//if(d.maxorder.value=="") d.maxorder.value=0;
	if(isNaN(d.maxorder.value))
	{
		alert(nmax);
		d.maxorder.focus();
		 return false;
	}
	maxO=parseInt(d.maxorder.value);
	if(maxO<0)
	{
		alert(noneg);
		d.maxorder.focus();
		 return false;
	}
	if(minO>maxO)
	{
		alert("<?php echo $LDAlertMinHigherMax ?>");
		d.maxorder.focus();
		 return false;
	}
	if(d.proorder.value==0||isNaN(d.proorder.value))
	{
		alert("<?php echo $LDAlertInvalidProorder ?>");
		d.proorder.focus();
		 return false;
	}
	proO=parseInt(d.proorder.value);
	if(proO<0)
	{
		alert(noneg);
		d.proorder.focus();
		return false;
	}
	
	return true;
	*/
}


function prufformlab(d){
	//alert("prufformlab");
	/*
	if (d.refno.value=='') {
		alert("Enter the reference no.");
		document.getElementById("saverequest").value = "0";
		d.refno.focus();
		return false;
	}
	*/
	
	if (d.purchasedt.value=='') {
		alert("Enter the date of purchase.");
		document.getElementById("saverequest").value = "0";
		d.purchasedt.focus();
		return false;
	}
	
	if (d.pname.value=='') {
		alert("Select the patient.");
		document.getElementById("saverequest").value = "0";
		d.pname.focus();
		return false;
	}
	
	/*
	if (d.dept_nr.value==0) {
		alert("Select the department.");
		d.dept_nr.focus();
		return false;
	}
	*/
	/*
	if (d.parameterselect.value==0) {
		alert("Select a Laboratory Service Group.");
		d.parameterselect.focus();
		return false;
	}*/
	
	return true;
	
}

/*********added by VAS****************/
function resetField(d){
	alert("resetField");
	//alert("$F('refno') = "+$F('refno'));
	//alert("$F('refno') = "+d.refno.value);
	d.refno.value = '';
	d.purchasedt.value==''
	d.pname.value==''
}

function jsSaveRequest(){
	document.getElementById("saverequest").value = "1";
}

function jsGetServices(){
	var aGroup_id = $F('parameterselect');
	var refno = $F('refno');
	var aIsCash;
	if ($F('is_cash') == 1)
		aIsCash = 1;
	else
		aIsCash = 0;	
		//xajax_populateServiceGroups(aGroup_id);
		xajax_populateServices(aGroup_id,aIsCash,refno,"none");
		//get_request();
} 

/*
function get_request(){

 	var aGroup_id = "<?= $parameterselect?>";
	var refno = "<?= $refno?>";
	
	if (aGroup_id!=null){
		
		<?php 
			global $db;
			$sql = "SELECT sd.* FROM
				      seg_lab_servdetails as sd,
              		seg_lab_services as ss
            	  WHERE ss.service_code = sd.service_code
				 	 AND ss.group_code = '$parameterselect'
              	AND refno = '$refno'";
	  		 #$rsObj=$db->Execute($sql);
			 #while($result=$rsObj->FetchRow()) {
			 	#	$code = $result['service_code'];
			# }
		?>
	
		var rsObj = "<?= $db->Execute($sql)?>";
		//alert("db = "+<?= $db?>);
	}	

	document.inputform.serviceArray_prev.value = aGroup_id;
}
*/

function ajxGetPrevReq(mod,servlist) {
	//var c_value = "";
	//alert("ajxGetPrevReq mod = "+mod);		
	
	if (mod == 1){	
		document.inputform.serviceArray_prev.value = servlist.substr(0,servlist.length-1);
	}else{
		document.inputform.serviceArray_prev.value = "";
		document.inputform.serviceArray.value = "";	
	}	

}		

function jsViewServices(){
	jsGetServices(); 
	
	document.getElementById("saverequest").value = "0";
	document.inputform.submit();
	//document.inputform.discount.value=document.inputform.discount2.value;	
	//document.inputform.countchk.value = "0";
}

function resetSave(){
	document.getElementById("saverequest").value = "0";
	//document.inputform.serviceArray.value = "";
}


function getTopCheck(parent, groupID) {
	
	var count=0;
	var parent2 = 'grp'+groupID;
	
	var p=document.inputform.parent2;
	
	var cList=p.getElementsByTagName('input');		
	var cList=parent.getElementsByTagName('input');		
	
	for (var i=0;i<cList.length;i++) {
		if (cList[i].type=="checkbox") {
			if (cList[i].checked&&cList[i].id!='chk_all_'+groupID) {
				return cList[i].id;
			}
		}
	}
	return null;
	
}

function fSubmit(id) {	
	if ($(id).submit)
		$(id).submit();
}

function get_check_value(){
	var cnt_chk = 0;
	var c_value = "";
	
	if (document.inputform.svcChk0.length > 1){
		for (var i=0; i < document.inputform.svcChk0.length; i++){
			if (document.inputform.svcChk0[i].checked){
		   	c_value = c_value + document.inputform.svcChk0[i].value + ",";
				cnt_chk +=1;
			}
   	}
	
		document.inputform.serviceArray.value = c_value.substr(0,c_value.length-1);
	}else{
		if (document.inputform.svcChk0.checked)
			document.inputform.serviceArray.value = document.inputform.svcChk0.value;
	}	
	
}

function jsgetDate(){
	if (document.inputform.purchasedt2.value == "")
		document.inputform.purchasedt.value=document.inputform.curdate.value;
	else
		document.inputform.purchasedt.value=document.inputform.purchasedt2.value;	
}

function jsrefresh(){
	self.opener.location.href=self.opener.location.href;
}


function viewPatientRequest(is_cash){
	var pid = $F('payer_id');
	var refno = $F('refno');
	window.open("seg-lab-request.php?pid="+pid+"&is_cash="+is_cash+"&refno="+refno+"&showBrowser=1","viewPatientRequest","width=620,height=440,menubar=no,resizable=yes,scrollbars=no")
}

function fetchServList(ms) {
	//alert("fetchServList : serv_code = "+serv_code);
	
	var serv_code = $F('searchserv');
	//alert("fetchServList : serv_code = "+serv_code);
	var aGroup_id = $F('parameterselect');
	var refno = $F('refno');
	var aIsCash;
	if ($F('is_cash') == 1)
		aIsCash = 1;
	else
		aIsCash = 0;
		
	if (timeoutHandle) {
		clearTimeout(timeoutHandle);
		timeoutHandle=0;
	}
	if (serv_code) {
		//timeoutHandle=setTimeout("xajax_populateServices('"+serv_code+"')",ms);
		//document.inputform.submit();
		timeoutHandle=setTimeout("xajax_populateServices('"+aGroup_id+"','"+aIsCash+"','"+refno+"','"+serv_code+"')",ms);
	}
	
	//document.getElementById("saverequest").value = "0";
	//document.inputform.submit();
}

function clearText(){
	document.inputform.searchserv.value="";
}
//------------------------

//  Script End -->
</script>
