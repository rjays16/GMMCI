
//added by VAN 07-09-08

function checkProcessForm(){
	var reagentcnt = $F('reagentcnt');
	//alert(filmcnt);
	var chkbox_error = "false";
	
	for (i=1;i<=reagentcnt;i++){
		if (document.getElementById('amount'+i).value!=""){
			chkbox_error = true;
			break;
		}	
	}
	//alert(chkbox_error);
	if (chkbox_error=="false"){
		alert("Please type an amount of reagent that was used for the request.");
		$('amount1').focus();
		return false;		
	}
	
	return true;
}


function saveProcessReagents(mode){
	var refno, $perpc;
	var reagentcnt = $F('reagentcnt');
	var reagents = new Array();
	var service_code = $F('service_code');
	//var nooffilms = new Array();

	if (checkProcessForm()==false){
		return;
	}
	refno = $F('refno'); 
	//no_film_used
	for (i=1;i<=reagentcnt;i++){
		if (document.getElementById('amount'+i).value!=""){
			if (document.getElementById('perpc'+i).checked)
				$perpc = 1;
			else
				$perpc = 0;
				
			reagents[i-1] = Array(document.getElementById('reagent_code'+i).value, document.getElementById('amount'+i).value,  document.getElementById('unit'+i).value,  $perpc);
		}	
	}

	xajax_saveProcessRequest('save',refno, service_code,reagents);
	
}

function msgPopUp(msg){
	alert(msg);
}

/*
function enableNoSize(index){
	var wfilm  = $('size'+index).checked;
	if (wfilm){
			$('amount'+index).readOnly = 0;
	}else{
			$('amount'+index).readOnly = 1;
	}
	$('amount'+index).value = "";
}
*/
//------------------