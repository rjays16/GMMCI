<script language="javascript">

function getHTTPObject(){
	if (window.ActiveXObject)
		return new ActiveXObject("Microsoft.XMLHTTP");
	else if (window.XMLHttpRequest)
		return new XMLHttpRequest();
	else {
		alert("Your browser does not support AJAX.");
		return null;
	}
}

function has_patient_ledger(pid){
	httpObject=getHTTPObject();

	if(httpObject!=null){
		httpObject.open("GET", '<?php echo $root_path;?>'+"modules/billing/seg_patient_ledger_prompt.php?patient_pid="+pid ,true);
		httpObject.send(null);
		httpObject.onreadystatechange=
			function (){

				if(httpObject.readyState==4){

					var strVal = httpObject.responseText;
					var arrVal = strVal.split("Param:");

					var has_occur_ledger = arrVal[1];
					var total_prev_rem = arrVal[2];
					var has_occur_abs = arrVal[3];

					var strMsg = "";
					if(has_occur_ledger>0 && total_prev_rem>0 )
						strMsg = "The patient has previous remaining total payment of Php "+total_prev_rem+"  \n";
					if(has_occur_abs>0)
						strMsg = strMsg + "The patient was previously absconded!! \n";
					if(strMsg>""){
						strMsg = strMsg + "";
						alert(strMsg);
					}
				}
			}
	}

}


function has_prompt_ledger(){
	var counter = 0;
	var pid = $('pid').value;
	var isCharge = true;

	if(($('counter').id)!=null){
		if($('counter').innerHTML=="")
			$('counter').innerHTML = 0;
		counter = parseInt($('counter').innerHTML);
	}


	if($("iscash1").id!=null)
		if(!($("iscash1").checked))
			isCharge = true;
		else
			isCharge = false;

	if(counter==0 && pid !="" && isCharge==true){
		has_patient_ledger(pid);
	}

}

</script>