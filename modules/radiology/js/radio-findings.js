
	function fSubmit(id) {
		if ($(id).submit)
			$(id).submit();
	}

	function pSearchClose() {
	//	alert("radio-finding.js : pSearchClose : ");
		cClick();  //function in 'overlibmws.js'
	}

	function checkFindingsForm(){

//		alert("$F('save') = '"+$F('save')+"'");
		if ($F('save')=='0'){
//			alert("false : $F('save') = '"+$F('save')+"'");
			// if the button clicked is not the for Referral, SAVE or SAVE&DONE buttons
			return false;
		}
		if ($F('mode') != 'referral'){
			if (($F('count_find')=='0') || ($F('count_find')=='')){
				alert("Please add a finding first.");
				$('addButton').focus();
				return false;
			}else if($F('service_date') == ''){
				alert("Please indicate the date of service.");
				$('service_date').focus();
				return false;
			}
		}
		return true;
	}

		function refreshWindow(){
				window.location.href=window.location.href;
		}


	function referralHandler(){
//	alert("referralHandler : 1 F('batch_nr')='"+$F('batch_nr')+" \nF('service_date')='"+$F('service_date')+"'");
		//added by VAN 03-05-08
		var mod;
		if ($('service_date2').value)
			mod = 1;
		else
			mod = 0;

		$('mode').value = 'referral';
		$('status').value = 'referral';
//	alert("referralHandler : 2 F('batch_nr')='"+$F('batch_nr')+" \nF('service_date')='"+$F('service_date')+"'");
		//edited by VAN 03-05-08
		//xajax_referralRadioFinding($F('batch_nr'),$F('service_date'));
		xajax_referralRadioFinding($F('batch_nr'),$F('service_date'),mod);
//		return false;
//		fSubmit('form_test_findings');
	}

	//added by VAN 03-05-08
	function updateServiceDate(){
		$('service_date2').value = $('service_date').value;
	}

	function saveOnly(){
		//alert('date = '+$('service_date').value);
		//added by VAN 03-05-08
		var mod;
		if ($('service_date2').value)
			mod = 1;
		else
			mod = 0;

		if (checkFindingsForm()){
			$('mode').value = 'save';
//alert("saveOnly : F('batch_nr')='"+$F('batch_nr')+" \nF('service_date')='"+$F('service_date')+"'");
		//edited by VAN 03-05-08
		//xajax_saveOnlyRadioFinding($F('batch_nr'),$F('service_date'));
		xajax_saveOnlyRadioFinding($F('batch_nr'),$F('service_date'),mod);
//			fSubmit('form_test_findings');
		}
//		return false;
	}

	function saveAndDone(){
		//added by VAN 03-05-08
		var mod;
		if ($('service_date2').value)
			mod = 1;
		else
			mod = 0;

		if (checkFindingsForm()){
			$('mode').value = 'save';
			$('status').value = 'done';
//alert("saveAndDone : F('batch_nr')='"+$F('batch_nr')+" \nF('service_date')='"+$F('service_date')+"'");
		//edited by VAN 03-05-08
		//xajax_saveAndDoneRadioFinding($F('batch_nr'),$F('service_date'));
		xajax_saveAndDoneRadioFinding($F('batch_nr'),$F('service_date'),mod);
//			fSubmit('form_test_findings');
		}
//		return false;
	}

	function msgPopUp(msg){
		alert(msg);
	}

	function deleteFinding(batch_nr,nr){
		var answer = confirm("You are about to delete finding #"+(nr+1)+". Are you sure?");
		//alert("answer = '"+answer+"'");
		if (answer){
			$('mode').value = 'delete';
			$('finding_nr').value = nr;
//			fSubmit('form_test_findings');
			xajax_deleteRadioFinding(batch_nr,nr);
//			refreshFindingList();
		}
	}
/*
	function popEditFinding(batch_nr,nr){

		var w=window.screen.width;
		var h=window.screen.height;
		var ww=500;
		var wh=475;
		urlholder="seg-radio-findings-edit.php<?= URL_APPEND ?>&batch_nr="+batch_nr+"&findings_nr="+nr;

		if (window.showModalDialog){  //for IE
			window.showModalDialog(urlholder,"width=" + ww + ",height=" + wh + ",menubar=no,resizable=yes,scrollbars=yes,dependent=yes");
		}else{
//			window.open("createCampus.php?i="+id,"createCampus","modal, width=480,height=320,menubar=no,resizable=no,scrollbars=no");
			popWindowEditFinding=window.open(urlholder,"EditFinding","width=" + ww + ",height=" + wh + ",menubar=no,resizable=yes,scrollbars=yes,dependent=yes");
			window.popWindowEditFinding.moveTo((w/2)+80,(h/2)-(wh/2));
		}
	}
*/
	function clearFindings(list) {
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

	function columnHeader(status) {
		var list = document.getElementById('findings-list');
//alert("columnHeader : list : \n"+list);
		if (list) {
			var dBody=list.getElementsByTagName("thead")[0];
//alert("columnHeader : dBody : \n"+dBody);
			if (dBody) {
				//'						<td width="5%"><b> Status </b></td> '+
				src =
					'					<tr id="findings-list-header" class="reg_list_titlebar" style="font-weight:bold;padding:0px;" align="center"> '+
					'						<td width="1%"><b> No. </b></td> '+
					'						<td width="18%"><b> Resident In-Charge </b></td> '+
					'						<td width="*"><b> Findings </b></td> '+
					'						<td width="1%"><b>&nbsp;</b></td> '+
					'						<td width="25%"><b> Impression </b></td> '+
					'						<td width="5%"><b> Date </b></td> ' +
										'                        <td width="3%"><b> Edit </b></td> '+
										'                        <td width="3%"><b> Delete </b></td> ';
				if (status!='done'){
					//commented by VAN 10-187-2008
										/*
										src +=
					'						<td width="3%"><b> Edit </b></td> '+
					'						<td width="3%"><b> Delete </b></td> ';
										*/
//					$('referralButton').style.display = '';   //October 9, 2007
					$('saveButton1').style.display = '';
					$('saveButton2').style.display = '';
					$('saveDoneButton').style.display = '';
					$('printReport').style.display = '';
				}//end of if-stmt "if (status!='done')"
				else{
						// status=='done'
					$('printReport').style.display = '';

					$('saveButton1').style.display = 'none';
					$('saveButton2').style.display = 'none';
										$('saveDoneButton').style.display = 'none';
					$('saveDoneButton').style.display = 'none';
					$('referralButton').style.display = 'none';
					$('addButton').style.display = 'none';
				}
				src +='</tr>';
//alert("columnHeader : status = '"+status+"'");
//alert("columnHeader : src : \n"+src);
				dBody.innerHTML = src;
//alert("columnHeader : src : \n"+src);
				return true;
			}//end of if-stmt "if (dBody)"
		}//end of if-stmt "if (list)"
		return false;
	}

	function appendFinding(list,details) {
//alert("appendFinding : list : \n"+list);
		if (list) {
			var dBody=list.getElementsByTagName("tbody")[0];
//alert("appendFinding : dBody : \n"+dBody);
			if (dBody) {
				var src;
				var items = document.getElementsByName('items[]');
						dRows = dBody.getElementsByTagName("tr");

				if (details) {
					var id = details.no;
					if (items) {
						for (var i=0;i<items.length;i++) {
							if (items[i].value == details.no) {
								$('docName'+id).innerHTML = details.docName;
								$('finding'+id).innerHTML = details.finding;
								$('r_impress'+id).innerHTML = details.r_impression;
								$('f_date'+id).innerHTML = details.f_date;

								//added by VAN 07-11-08
								$('r_status'+id).innerHTML = details.r_status;

								return true;
							}
						}
						if (items.length == 0)
							clearFindings(list);
					}

					alt = (dRows.length%2)+1;
					//'	<td id="r_status'+id+'" style="font-size:11px"><b> '+details.r_status+' </b></td> '+
					src =
						'<tr class="wardlistrow'+alt+'" id="row'+id+'" style="font-weight:bold;padding:0px"> '+
						'	<input type="hidden" name="items[]" id="rowID'+id+'" value="'+id+'" />'+
						'	<td valign="top" align="center"><b> '+(parseInt(id)+1)+'</b></td> '+
						'	<td id="docName'+id+'" valign="top" align="left" style="font-size:11px"><b> '+details.docName+' </b></td> '+
						'	<td id="finding'+id+'" style="font-size:11px"><b> '+details.finding+' </b></td> '+
						'	<td>&nbsp;</td> '+
						'	<td id="r_impress'+id+'" style="font-size:11px"><b> '+details.r_impression+' </b></td> '+
						'	<td id="f_date'+id+'" valign="top" align="left" ><b> '+details.f_date+' </b></td> ';

										//commented for the meantime.. requested by sir romy
										//if (details.status!='done'){
						var editImg = 'src="../../gui/img/control/default/en/en_edit_icon_06.gif" border=0 width="20" height="21"';
						var deleteImg = 'src="../../gui/img/control/default/en/en_trash_06.gif" border=0 width="20" height="21"';

						var deleteClick = (details.target == 'radio_done' ? '' : 'onClick="deleteFinding('+details.batch_nr+','+id+');"');
						src +=
							'	<td valign="top" align="center"> '+
							'		<a href="javascript:void(0);" '+
							'			onclick="return overlib( '+
							'				OLiframeContent(\''+details.f_link+'&mode=update'+'\', 800, 450, \'if1\', 1, \'auto\'), '+
							'					WIDTH,500, TEXTPADDING,0, BORDER,0,  '+
							'					STICKY, SCROLL, CLOSECLICK, MODAL, DRAGGABLE, '+
							'					CLOSETEXT, \'<img src=../../images/x.gif border=0 onClick=refreshWindow();>\', '+
							'					CAPTIONPADDING,4, CAPTION,\'Update findings\', MIDX,0, MIDY,0,  '+
							'					STATUS,\'Update findings\');" '+
							'			onmouseout="nd();"> '+
							'			<img name="edit'+details.no+'" id="edit'+details.no+'" '+editImg+'> '+
							'		</a> '+
							'	</td> '+
							'	<td valign="top" align="center"> '+
							'		<img name="delete'+id+'" id="delete'+id+'" '+deleteClick+' '+deleteImg+'> '+
							'	</td> ';
							$('referralButton').style.display = '';   //October 9, 2007: Assured that there is at least a finding entry prior to referral

					src +='</tr>';
					$('count_find').value = parseInt($('count_find').value) + 1;
				}// end of if-stmt 'if (details)'
				else {
//					src = "<tr><td colspan=\"7\">List of findings is currently empty...</td></tr>";
					src = "									<tr> "+
							"											<td colspan=\"8\" align=\"center\" bgcolor=\"#FFFFFF\" style=\"color:#FF0000; font-family:'Arial', Courier, mono; font-style:Bold; font-weight:bold; font-size:12px;\"> "+
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


function refreshFindingsList(){
	var items = document.getElementsByName('items[]');
	if (items.length == 0){
		$('count_find').value = 0;
		$('referralButton').style.display = 'none';
	}
}
/*
		burn added : September 13, 2007
*/
function emptyIntialFindings(showEmptyMsg){
//alert("emptyIntialFindings : 1 showEmptyMsg='"+showEmptyMsg+"'");
	clearFindings($('findings-list'));
//alert("emptyIntialFindings : 2 showEmptyMsg='"+showEmptyMsg+"'");
	if (showEmptyMsg=='1'){
//alert("emptyIntialFindings : 3 showEmptyMsg='"+showEmptyMsg+"'");
		appendFinding($('findings-list'),null);
	}
}

//function initialFindingsList(batch_nr,f_nr,findings,radio_impression,status_result,f_date,docName,status,seg_URL_APPEND) {
function initialFindingsList(batch_nr,f_nr,findings,radio_impression,f_date,docName,status,seg_URL_APPEND,target) {
	var details = new Object();
	var pid = $('pid').value;
	var refno = $('refno').value;
	// alert("hello"+refno);
		details.batch_nr = batch_nr;
		details.no = f_nr;
		details.finding = findings;
		details.r_impression = radio_impression;
		details.f_date = f_date;
		details.docName = docName;
		details.status = status;
		details.target = target;
		//added by VAN 07-11-08
		//details.r_status = status_result;

		details.f_link="seg-radio-findings-edit.php"+seg_URL_APPEND+"&batch_nr="+batch_nr+"&refno="+refno+"&pid="+pid+"&findings_nr="+f_nr;
		var msg = "details.status='"+details.status+"'\ndetails.batch_nr='"+details.batch_nr+
					 "\ndetails.no='"+details.no+"'\ndetails.finding='"+details.finding+
					 "'\ndetails.r_impression='"+details.r_impression+
					 "'\ndetails.f_date='"+details.f_date+"'\ndetails.docName='"+details.docName+"'"+
					 "'\nseg_URL_APPEND='"+seg_URL_APPEND+"'\n";
//alert("initialFindingsList : "+msg);
		var list =document.getElementById('findings-list');
//alert("initialFindingsList : list : "+list);
		result = appendFinding(list,details);
}

	function printRadioReport(){

		var w=window.screen.width;
		var h=window.screen.height;
		var ww=500;
		var wh=500;
		var rpath=$F('rpath');
		var pid=$F('pid');
		var batch_nr=$F('batch_nr');
		var seg_URL_APPEND=$F('seg_URL_APPEND');
		var refno=$F('refno');

//alert("printRadioReport :: parseInt($F('count_find')) = '"+parseInt($F('count_find'))+"'");
		var count_find = parseInt($F('count_find'));
//alert("printRadioReport :: count_find = '"+count_find+"'");
		if (count_find < 1){
			alert("There is no finding reported yet.");
			return;
		}

		urlholder=rpath+"modules/radiology/seg-radio-findingsseg-radio-findings-select-batchNr.php"+seg_URL_APPEND+"&pid="+pid+"&batch_nr="+batch_nr+"&refno="+refno;

		if (window.showModalDialog){  //for IE
			window.showModalDialog(urlholder,"width=" + ww + ",height=" + wh + ",menubar=no,resizable=yes,scrollbars=yes,dependent=yes");
		}else{
//			window.open("createCampus.php?i="+id,"createCampus","modal, width=480,height=320,menubar=no,resizable=no,scrollbars=no");
			popWindowEditFinding=window.open(urlholder,"Print Report","width=" + ww + ",height=" + wh + ",menubar=no,resizable=yes,scrollbars=yes,dependent=yes");
			window.popWindowEditFinding.moveTo((w/2)+80,(h/2)-(wh/2));
		}
return;

		urlholder=rpath+"modules/radiology/certificates/seg-radio-report-pdf.php"+seg_URL_APPEND+"&pid="+pid+"&batch_nr="+batch_nr;

		if (window.showModalDialog){  //for IE
			window.showModalDialog(urlholder,"width=" + ww + ",height=" + wh + ",menubar=no,resizable=yes,scrollbars=yes,dependent=yes");
		}else{
//			window.open("createCampus.php?i="+id,"createCampus","modal, width=480,height=320,menubar=no,resizable=no,scrollbars=no");
			popWindowEditFinding=window.open(urlholder,"Print Report","width=" + ww + ",height=" + wh + ",menubar=no,resizable=yes,scrollbars=yes,dependent=yes");
			window.popWindowEditFinding.moveTo((w/2)+80,(h/2)-(wh/2));
		}
	}

//added by VAN 07-31-08
function viewRadioReport(pid,batch_nr){
	//alert('pid = '+pid);
	//alert('batch_nr = '+batch_nr);
	var rpath=$F('rpath');
	var seg_URL_APPEND=$F('seg_URL_APPEND');

	window.open(rpath+"modules/radiology/certificates/seg-radio-report-pdf.php"+seg_URL_APPEND+"&pid="+pid+"&batch_nr_grp="+batch_nr+"&showBrowser=1","viewPatientReport","width=620,height=440,top=150,left=200,menubar=no,resizable=yes,scrollbars=yes")
}