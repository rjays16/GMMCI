var isLoading=0;
function startLoading() {
	if (!isLoading) {
		isLoading = 1;
		return overlib('Loading items...<br><img src="../../images/ajax_bar.gif">',
			WIDTH,300, TEXTPADDING,5, BORDER,0,
			STICKY, SCROLL, CLOSECLICK, MODAL,
			NOCLOSE, TIMEOUT, 10000, OFFDELAY, 10000,
			CAPTION,'Loading', 
			MIDX,0, MIDY,0,
			STATUS,'Loading');
	}
}

function doneLoading() {
	if (isLoading) {
		setTimeout('cClick()', 500);
		isLoading = 0;
	}
}

//xajax.callback.global.onRequest = startLoading;
//xajax.callback.global.onComplete = doneLoading;

//added by VAN 04-07-08
function hideClassification(){
	//alert('hide');
	document.getElementById('classify').style.display='none';
	document.getElementById('rqlistdiv').style.display='none';
	document.getElementById('rqlisttable').style.display='none';
}

function init() {
	var encounter_nr = $('encounter_nr').value;
	// Define various event handlers for Dialog
	var handleSubmit = function() {
		this.submit();
		this.cancel();
	};
	var handleCancel = function() {
		this.cancel();
	};
	/*
	var handleSuccess = function(o) {
		xajax_PopulateSSC(encounter_nr, 'ssl');
		var response = o.responseText;
		response = response.split("<!")[0];
		document.getElementById("resp").innerHTML = response;
		eval(response);
	};*/
	
	// Instantiate the Dialog for classification
	YAHOO.example.container.dialog1 = new YAHOO.widget.Dialog("dialog1",{ width : "413px", fixedcenter:true,visible : false, 
																  constraintoviewport : true,
																  buttons : [ { text:"Submit", handler:handleSubmit, isDefault:true },
																			  { text:"Cancel", handler:handleCancel } ]
																 } );
	//Instantiate the dialog for Update profile
	YAHOO.example.container.dialog2 = new YAHOO.widget.Dialog("dialog2",{ width:"530px", fixedcenter:true, visible:false,
															  				contraintoviewport:true, 
																			buttons: [ { text:"Submit", handler:handleSubmit, isDefault:true}, 
																					   { text:"Cancel", handler:handleCancel }]
																		});
	
	
	// Validate the entries in the form to required fields
	YAHOO.example.container.dialog1.validate = function(){
		var data  = this.getData();
		//if(data.personal_circumstance == "" || data.community_situation == "" || data.nature_of_disease ==""){
		//if(data.personal_circumstance == 0 || data.community_situation == 0 || data.nature_of_disease == 0){
		if(data.personal_circumstance == 0 && data.community_situation == 0 && data.nature_of_disease == 0){	
			//alert("Please fill all the fields required. Don't leave it blank.");
			//alert("Please fill all the fields required. Don't leave it blank.");
			/*
			if (data.personal_circumstance == 0){
				alert('Please select a modifier for personal circumstances.');	
				$('personal_circumstance').focus();	
			}else if (data.community_situation == 0){
				alert('Please select a modifier for community situations.');	
				$('community_situation').focus();	
			}else if (data.nature_of_disease == 0){
				alert('Please select a modifier for Nature of Illness/Disease.');	
				$('nature_of_disease').focus();	
			} 
			*/
			//alert('data.subservice_code = '+((data.subservice_code=='OT')||(data.subservice_code=='OTHER')));
			//alert('data.subservice_code2 = '+(data.subservice_code2==''));
			//alert('here = '+(data.subservice_code=='OT')&&(data.subservice_code2==''));
			//edited by VAN 07-04-08
			//if ((data.personal_circumstance == 0)&&(data.community_situation == 0)&&(data.nature_of_disease == 0)){	
			if (data.service_code==0){
				alert('Please select classification.');
				$('service_code').focus();	
				return true;	
			}else if ((data.subservice_code==0)&&(data.subc==1)){	
				alert('Please select sub classification.');
				$('subservice_code').focus();	
				return true;	
			//added by VAN 08-05-08
			}else if(((data.subservice_code=='OT')||(data.subservice_code=='OTHER'))&&(data.subservice_code2=='')){
				alert('Please select classification for other.');
				$('subservice_code2').focus();	
				return true;	
			}else if(((data.subservice_code=='SC')||(data.subservice_code=='VET'))&&(data.idnumber=='')){
				alert('Please enter the ID number of the senior citizen or veteran ID.');
				$('idnumber').focus();
				return true;
			}else if (data.withrec==1){
				alert('Please select at least one modifier for the reclassification.');
				if (data.personal_circumstance == 0){
					$('personal_circumstance').focus();	
				}else if (data.community_situation == 0){
					$('community_situation').focus();		
				}else if (data.nature_of_disease == 0){
					$('nature_of_disease').focus();	
				}
			//}
				
				return true;	
			}else{
				xajax_ProcessAddSScForm(data);
				return false;	
			}
		}else{
			xajax_ProcessAddSScForm(data);
			return false;
		}
		
	};
	//xajax_disableReadonlysegSocservPatient();  //Added by Cherry 07-23-10

	// Validate the entries in the form update profile.
	YAHOO.example.container.dialog2.validate = function(){
		var data = this.getData();
		//if(data.resp =="" || data.relation == "" || data.s_income=="" || data.m_income=="" || data.nr_dep == ""){
		//edited by VAN 05-09-08
		 profileShow(0);
		/*if(data.resp =="" || data.relation == "" || data.s_income=="" || data.m_income==""
			|| data.nr_dep == "" || data.hauz_lot == "" || data.food == "" || data.light ==""
			|| data.water == "" || data.transport == "" || data.other == ""){*/
		if(data.resp =="" || data.relation == "" || data.s_income=="" || data.m_income==""
			|| data.nr_dep == ""){
			alert("Fill all the fields.");
			return true;
			
		}else{

				xajax_UpdateProfileForm(data);
				
				$('respondent').innerHTML = data.resp.toUpperCase();
				$('h_respondent').value = data.resp;
				$('relation_patient').innerHTML = data.relation.toUpperCase();
				$('h_relation_patient').value = data.relation;
				$('occupation').innerHTML = $('occupation_select').options[$('occupation_select').selectedIndex].text.toUpperCase();
				$('h_occupation').value = $('occupation_select').value;
				$('source_income').innerHTML = data.s_income.toUpperCase();
				$('h_source_income').value = data.s_income;
				
				//edited by VAN 05-09-08
				$('monthly_income').innerHTML = formatNumber(data.m_income,2);
				$('h_monthly_income').value = formatNumber(data.m_income,2);
				
				$('nrdep').innerHTML = data.nr_dep;	
				$('h_nrdep').value = data.nr_dep;	
				
				//added by VAN 07-25-08
				$('nrchldren').innerHTML = data.nr_chldren;	
				$('h_nrchldren').value = data.nr_chldren;	
				
				$('capita_income').innerHTML = formatNumber(data.m_capita_income,2);
				$('h_capita_income').value = formatNumber(data.m_capita_income,2);
				//---------------
				
				//added by VAN 05-09-08
				var total=0;
				
				total = parseInt(data.hauz_lot) + parseInt(data.food) + parseInt(data.light) + parseInt(data.water) + parseInt(data.transport) + parseInt(data.other);
				$('h_monthly_expenses').value = formatNumber(total,2);
				$('monthly_expenses').innerHTML = formatNumber(total,2);
				
				//alert($('mpid').value);

				$('address').innerHTML = data.address;
				$('h_address').value = data.address;

				xajax_setMSS($('mpid').value);
				
			return false;
		}
	};
	/*
	// Wire up the success and failure handlers
	YAHOO.example.container.dialog2.callback = { success: handleSuccess,
												 failure: handleFailure };
	*/											 
	// Render the Dialog1
	//YAHOO.example.container.dialog1.render();
	//YAHOO.util.Event.addListener("show", "click", YAHOO.example.container.dialog1.show, YAHOO.example.container.dialog1, true);
	YAHOO.util.Event.addListener("show", "click",onClickHandlerButton); 
	
	// Render the Dialog1
	YAHOO.example.container.dialog2.render();
	YAHOO.util.Event.addListener("updateprofile", "click", onClickHandlerProfile);
		
	YAHOO.util.Event.addListener("m_income2", "keypress", keyPressHandler);
	YAHOO.util.Event.addListener("nr_dep", "keypress", keyPressHandler);
	
	//added by VAN 07-25-08
	YAHOO.util.Event.addListener("nr_chldren", "keypress", keyPressHandler);
	
	//added by VAN 05-10-08
	YAHOO.util.Event.addListener("hauz_lot2", "keypress", keyPressHandler);
	YAHOO.util.Event.addListener("food2", "keypress", keyPressHandler);
	YAHOO.util.Event.addListener("light2", "keypress", keyPressHandler);
	YAHOO.util.Event.addListener("water2", "keypress", keyPressHandler);
	YAHOO.util.Event.addListener("transport2", "keypress", keyPressHandler);
	YAHOO.util.Event.addListener("other2", "keypress", keyPressHandler);
	
	//Added by Cherry  07-20-10
	YAHOO.util.Event.addListener("hauz_lot_type", "keypress", keyPressHandler);

	DOM_init();
}// end of function init ()	

function onClickHandlerButton(){
	//uncommented by VAN 11-14-09
		
	/*if($('h_respondent').value =='' && $('h_relation_patient').value == '' &&
			$('h_occupation').value == '' && $('h_source_income').value == '' &&
			($('h_monthly_income').value == '0.00' || $('h_monthly_income').value == '') && $('h_nrdep').value == '' ){
	*/
	//edited by VAN 07-19-2010
	//if ($('mssno').value==''){
	if($('can_classify').value==0){
		alert("Please update patient profile before you give classification.");
	}else{
		YAHOO.example.container.dialog1.render();
		YAHOO.example.container.dialog1.show();	
	}
	//commented by VAN 11-14-09
	//YAHOO.example.container.dialog1.render();
	//YAHOO.example.container.dialog1.show();
}

//Added by Jarel 05/29/13
function onClickHandlerProfile(){
	
	
		YAHOO.example.container.dialog2.render();
		YAHOO.example.container.dialog2.show();	
	
}

function keyPressHandler(event){
	//alert('here')
	var key = YAHOO.util.Event.getCharCode(event);		
	//var regex = /^[0-9]*$/;  use this for masking only 
	//var format = ' ';
	//var ch = String.fromCharCode(key);
	//var el = $('m_income');
	//var str = el.value + ch;
	//var pos = str.length;
	
	if(key > 31 && (key <48 || key > 57)){
		/*
		if(regex.test(ch)){ 
			if ( format.charAt(pos - 1) != ' ' ) {
                  str = el.value + format.charAt(pos - 1) + ch;
            }
			el.value = str;
		}*/
		Event.stop(event);
	}
	
	return true;
}


	/*	Use to close the pop-up dialog
	 *	burn added: October 12, 2007
	 */
function pSearchClose() {
	cClick();  //function in 'overlibmws.js'
	window.location.reload();
}

//added by VAN 06-25-08
function refreshWindow(){
	rlst.reload();
	//window.location.href=window.location.href;
}

function showClassificationDetails(id) {
	var mod1Code = $('cf_mod1_code'+id).value,
			mod1Text = $('cf_mod1_text'+id).value,
			mod2Text = $('cf_mod2_text'+id).value,
			mod2Text = $('cf_mod2_text'+id).value,
			mod3Text = $('cf_mod3_text'+id).value,
			mod3Text = $('cf_mod3_text'+id).value;
	var sHTML = '<ul class="segItemizedList">';
	if (mod1Text) sHTML += '<li><span>'+mod1Text+'</span></li>';
	if (mod2Text) sHTML += '<li><span>'+mod2Text+'</span></li>';
	if (mod3Text) sHTML += '<li><span>'+mod3Text+'</span></li>';
	sHTML += '</ul>';
	
	return overlib( sHTML,
		WIDTH, 380, TEXTPADDING, 0, BORDER, 0, CLOSECLICK,
		TEXTPADDING,3, TEXTFONTCLASS,'oltxt', 
		CLOSETEXT, '<img src=../../images/close_red.gif border=0 onClick=refreshWindow();>',
		CAPTION, 'Classification Modifiers',
		HAUTO, VAUTO, 
		FGCLASS,'olfgPopup',
		STATUS,'Classification Modifiers'
	);
}

function js_showDetails(refno, dept){
	var sid, rpath, param ='';
	
	sid = $('sid').value;
	rpath = $('root_path').value;
	
	//added by VAN 06-24-08
	var discountid;
	var encounter_nr = $('encounter_nr').value;
	var pid = $('pidNr').value;


	if ($('discountId').value)
		discountid = $('discountId').value;
	else
		discountid = $('discountId2').value;

	//alert('discountid = '+discountid);
	//alert(discountid);
	switch(dept){
		case 'LB':			
			return overlib(OLiframeContent(rpath+'modules/laboratory/seg-lab-request-new.php'+sid+'&user_origin=lab&encounter_nr='+encounter_nr+'&pid='+pid+'&popUp=1&refno='+refno+'&view_from=ssview&discountid='+discountid, 820,400, 'frad-request', 0, 'auto'),
						WIDTH , 820, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL , CLOSETEXT, 
						'<img src=../../images/close_red.gif border=0 onClick=refreshWindow();>', CAPTION, 'Laboratory Request',
						MIDX, 0, MIDY, 0, STATUS,'Laboratory Request');
		
		break;

		case 'BB':
						return overlib(OLiframeContent(rpath+'modules/bloodBank/seg-blood-request-new.php'+sid+'&local_user=ck_radio_user&user_origin=blood&encounter_nr='+encounter_nr+'&pid='+pid+'&popUp=1&refno='+refno+'&view_from=ssview&discountid='+discountid, 820,400, 'frad-request', 0, 'auto'),
												WIDTH , 820, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL, CLOSETEXT,
												'<img src=../../images/close_red.gif border=0 onClick=refreshWindow();>', CAPTION, 'Blood Bank Request',
												MIDX, 0, MIDY, 0, STATUS,'Blood Bank Request');

						break;

		case 'SPL':
						return overlib(OLiframeContent(rpath+'modules/special_lab/seg-splab-request-new.php'+sid+'&local_user=ck_radio_user&user_origin=splab&encounter_nr='+encounter_nr+'&pid='+pid+'&popUp=1&refno='+refno+'&view_from=ssview&discountid='+discountid, 820,400, 'frad-request', 0, 'auto'),
												WIDTH , 820, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL, CLOSETEXT,
												'<img src=../../images/close_red.gif border=0 onClick=refreshWindow();>', CAPTION, 'Special Laboratory Request',
												MIDX, 0, MIDY, 0, STATUS,'Special Laboratory Request');

						break;

		case 'RD':
			//edited by VAN 04-07-08
			/*
			return overlib(OLiframeContent(rpath+'modules/nursing/nursing-station-radio-request-new.php'+sid+'&local_user=ck_radio_user&popUp=1&refno='+refno+'&view_from=ssview', 780,450, 'frad-request', 1, 'auto'),
						WIDTH , 780, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL , CLOSETEXT, 
						'<img src=../../images/close.gif border=0>', CAPTIONPADDING, 4, CAPTION, 'Radiology Request',
						MIDX, 0, MIDY, 0, STATUS,'Radiology Request');	
			*/
			return overlib(OLiframeContent(rpath+'modules/radiology/seg-radio-request-new.php'+sid+'&local_user=ck_radio_user&encounter_nr='+encounter_nr+'&pid='+pid+'&popUp=1&refno='+refno+'&view_from=ssview&discountid='+discountid, 820,400, 'frad-request', 0, 'auto'),
						WIDTH , 820, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL, CLOSETEXT, 
						'<img src=../../images/close_red.gif border=0 onClick=refreshWindow();>', CAPTION, 'Radiology Request',
						MIDX, 0, MIDY, 0, STATUS,'Radiology Request');	
		break;
		case 'P':
			return overlib(OLiframeContent(rpath+'modules/pharmacy/seg-pharma-order.php'+sid+'&target=edit&ref='+refno+'&viewonly=1&view_from=ssview&from=CLOSE_WINDOW', 820,400, 'frad-request', 0, 'auto'),
						WIDTH , 820, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL , CLOSETEXT, 
						'<img src=../../images/close_red.gif border=0 onClick=refreshWindow();>', CAPTION, 'Pharmacy Request',
						MIDX, 0, MIDY, 0, STATUS,'Pharmacy Request');	
		break;
        //Added by Jarel 02/08/13
        case 'M':  
            return overlib(OLiframeContent(rpath+'/modules/dialysis/seg-misc-request-new.php'+sid+'&encounter_nr='+encounter_nr+'&pid='+pid+'&mode=edit&refno='+refno+'&viewonly=1&view_from=ssview&discountid='+discountid+'&from=CLOSE_WINDOW', 820,400, 'frad-request', 0, 'auto'),
                        WIDTH , 820, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL , CLOSETEXT, 
                        '<img src=../../images/close_red.gif border=0 onClick=refreshWindow();>', CAPTION, 'Miscellaneous Request',
                        MIDX, 0, MIDY, 0, STATUS,'Miscellaneous Request');    
        break;
	}
	
}

function js_addDefaultRow(tableId){
	var dTable=$(tableId);
	if(dTable){
		rowSrc = '<tr><td style="" colspan="6">No requests available at this time.</td></tr>';
		document.getElementById('rqlisttbody').innerHTML = rowSrc;
	}
}

// this function use for xajax 
function js_addRow(tableId, code, note, clsfby, grant_dte, listname, personal_circumstance, com_situation, nature_of_illness){
	var dTable=$(tableId), dBody, dRows, rowSrc;
	var rowno, dept='';
	
	if(dTable){
		dBody = dTable.getElementsByTagName('tbody')[0];
		dRows = dBody.getElementsByTagName('tr');
						
		if(code){
			switch(listname) {		
				case 'ssl':
					//if(dRows.length > 0) rowno=dRows[dRows.length-1].id.replace("ssl","" ); 
					//rowno = isNaN(rowno)?0:(rowno-0)+1;
					
					/*rowSrc = '<tr id="ssl'+rowno+'">'+
								'<td class="adm_item">'+
									'<input type="hidden" id="nr'+code+'" value="'+code+'">'+
									'<span style="color:#660000">'+code+'</span><br>'+
									'<table id="tbl'+rowno+'" width="100%" border="1" cellpadding="0" cellspacing="0" align="left">'+
										'<tbody>'+
											'<tr>'+
												'<td width="10%">Personal circumstances:&nbsp;</td>'+
												'<td><span>'+personal_circumstance+'</span></td>'+
											'</tr>'+
											'<tr>'+
												'<td width="10%">Community situations:&nbsp;</td>'+
												'<td><span>'+com_situation+'</span></td>'+
											'</tr>'+
											'<tr>'+
												'<td width="10%">Nature of Illness/Disease:&nbsp;<span></span></td>'+
												'<td><span>'+nature_of_illness+'</span></td>'+
											'</tr>'+
										'</tbody>'+
									'</table>'+
								'</td>'+
								'<td align="center" class="adm_item"><span>'+clsfby+'</span></td>'+
								'<td align="center" class="adm_item"><span>'+grant_dte+'</span></td>'+
							 '</tr>';
							*/
						
				break;
				
				//if case lcr = (refno, date_request, price_cash, discount)		
				//call("js_addRow","rqlisttable",$row['refno'],$row['date_request'],$row['total_charge'],$row['dept'], 'lcr');	
				case 'lcr':
					var inputbtn, hddn=''; var dept ='' ;
					if(dRows.length > 0) rowno = dRows[dRows.length-1].id.replace("lcr","" ); 
					rowno = isNaN(rowno)?0:(rowno-0)+1;
										
					switch(grant_dte){
						case 'LB':
							dept = 'Laboratory';
						break;
						case 'BB':
							dept = 'Blood Bank';
							break;
						case 'SPL':
							dept = 'Special Lab';
							break;
						case 'RD':
							dept = 'Radiology';
						break;
						case 'P':
							dept = 'Pharmacy';
						break;
					}
										
					//dept = grant_dte;
					//inputbtn ='<button id="btn'+rowno+grant_dte+'" onclick="js_showDetails(\''+code+'\',\''+dept+'\')">Show Details</button>';
					inputbtn ='<button id="btn'+rowno+grant_dte+'" onclick="javascript:void(0);js_showDetails(\''+code+'\', \''+grant_dte+'\');">Show Details</button>';
					
					rowSrc = '<tr id="lcr'+rowno+'">'+
								'<td align="center"><span style="color:#660000">'+code+'</span></td>'+    //refno
								'<td align="center"><span>'+note+'</span></td>'+    		// date_request
								'<td align="center"><span>'+dept+'</span></td>'+   		//price_cash
								'<td align="right"><span>'+clsfby+'</span></td>'+  
								'<td align="right"><span>&nbsp;</span></td>'+ 
								'<td align="center">'+inputbtn+'</td>'+
							 '</tr>';
					
				break;
			}
		}else{
			rowSrc = '<tr><td style="">No classification yet..</td></tr>';
		}	
		dBody.innerHTML += rowSrc;
	}
} // end of function js_addRow


function js_clearRow(tableId){
	// Search for the source row table element
	var list=$(tableId),dRows, dBody;
	if (list) {
		dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			dBody.innerHTML = "";
			return true;	// success
		}
		else return false;	// fail
	}
	else return false;	// fail
}// end of fucntion js_clearRow()


function js_AddOptions(tagId,text, value, type){
	var elTarget = $(tagId);
	if(elTarget){
		//var opt = new Option(text, value);
		switch (type){
			case 'a':
				var opt = new Option(value, value);
				opt.id = value;	
			break;
			case 'b':	
				var opt = new Option(text, value);
				opt.id = value;
			break;
		}
		elTarget.appendChild(opt);
	}
	var optionsList = elTarget.getElementsByTagName('OPTION');
}//end of function js_AddOption

//added by VAN 05-15-08
function mouseOver(tagId, value){
	var modifier;
	var elTarget = $(tagId);
	if(elTarget){
		
		idname = $(tagId).id+value;
		
		if ($(tagId).id=='personal_circumstance')
			modifier = "Personal Circumstances";
		else if ($(tagId).id=='community_situation')
			modifier = "Community Situations";
		else if ($(tagId).id=='nature_of_disease')
			modifier = "Nature of Illness/Disease";	

		return overlib( $(idname).value, CAPTION,modifier, BORDER,0,
			TEXTPADDING,5, TEXTFONTCLASS,'oltxt', CAPTIONFONTCLASS,'olcap',
			WIDTH,400, FGCLASS,'olfgPopup', FIXX,10, FIXY,10
		);
	}
}

function mouseOut(){
	return nd();	
}

function js_AddOptions2(tagId,text, value, desc){
	//alert(desc);
	var elTarget = $(tagId);
	if(elTarget){
		var opt = new Option(text, value);
		opt.id = value;
		
		if (desc!=0){
			opt.setAttribute("onMouseover", "mouseOver("+tagId+","+value+");");
			opt.setAttribute("onMouseout", "mouseOut();");
		}
		
		elTarget.appendChild(opt);
	}
	var optionsList = elTarget.getElementsByTagName('OPTION');
	
	//var el = document.createElement('TEXTAREA');
	var el = document.createElement('input');
  	el.type = 'hidden';
  	el.name = $(tagId).id+value;
  	el.id = $(tagId).id+value;
	el.setAttribute("value", desc);
	//el.value = desc;
	//el.cols = "35";
	//el.rows = "5";
	//el.setAttribute("wrap", "hard");
	//el.setAttribute("style", "display:none");
	elTarget.appendChild(el);
	//alert(elTarget.innerHTML);
}//end of function js_AddOption

//---------------------------

function js_SetOptionDesc(tagId,value){
	$(tagId).innerHTML = value;
}

function setOption_a(tagId, value){
	//alert( "tagid ="+ tagId + "\n value = " + value);
	$(tagId).value = value;	
}

function setOption_b(tagId, value){
	$(tagId).value = value;	
}


function js_SetMssPatient(mss_no){
	//alert(mss_no);
	$('smss_no').innerHTML = mss_no;	
}

function setProfile(admitDiagnosis,resp, rel, occ , s_income, m_income, nr_dep, hauz_lot, food, light, water, transport, other, mss_no, per_capita_income, nr_children) {
	var expenses;
	//Profile view 	
	//alert('hello set');
	if (admitDiagnosis)
		$('admitting_diagnosis').innerHTML = admitDiagnosis;
	else 
		$('admitting_diagnosis').innerHTML = '<span style="font-style:italic;color:#400000">No diagnosis available...</span>';
		
	$('respondent').innerHTML = resp.toUpperCase();
	$('h_respondent').value = resp;
	
	//alert(mss_no);
	$('smss_no').innerHTML = mss_no;
	
	$('relation_patient').innerHTML = rel.toUpperCase();
	$('h_relation_patient').value = rel;
	
	$('occupation').innerHTML = occ.toUpperCase();
	$('h_occupation').value = occ;
	
	$('source_income').innerHTML = s_income.toUpperCase();
	$('h_source_income').value = s_income;
	
	$('monthly_income').innerHTML = formatNumber(m_income,2);
	$('h_monthly_income').value = formatNumber(m_income,2);
	
	$('nrdep').innerHTML = nr_dep;
	$('h_nrdep').value = nr_dep;
		
	$('nrchldren').innerHTML = nr_children;
	$('h_nrchldren').value = nr_children;
	
	$('capita_income').innerHTML = formatNumber(per_capita_income,2);
	$('h_capita_income').value = per_capita_income;
	
	expenses = parseInt(hauz_lot) + parseInt(food) + parseInt(light) + parseInt(water) + parseInt(transport) + parseInt(other);
	
	$('monthly_expenses').innerHTML = formatNumber(expenses,2);
	$('h_monthly_expenses').value = formatNumber(expenses,2);
	
	//update profile view 
	$('resp').value = resp;
	$('relation').value = rel;
	$('s_income').value = s_income;
	$('nr_dep').value = nr_dep;
	
	$('nr_chldren').value = nr_children;
	$('m_capita_income').value = formatNumber(per_capita_income,2);
	$('m_cincome').value = per_capita_income;
	
	
	//added by VAN
	$('m_income2').value = formatNumber(m_income,2);
	$('m_income').value = m_income;
	$('hauz_lot2').value = formatNumber(hauz_lot,2);
	$('hauz_lot').value = hauz_lot;
	$('food2').value = formatNumber(food,2);
	$('food').value = food;
	$('light2').value = formatNumber(light,2);
	$('light').value = light;
	$('water2').value =  formatNumber(water,2);
	$('water').value =  water;
	$('transport2').value = formatNumber(transport,2);
	$('transport').value = transport;
	$('other2').value = formatNumber(other,2);
	$('other').value = other;
	
	$('m_expenses').value = formatNumber(expenses,2);
	
}// end of funcion setProfile

//clear ajax Options social service classification
function js_ClearOptions(tagId){
	var optionsList, el =$(tagId);
	if(el){
		optionsList = el.getElementsByTagName('OPTION');
		for(var i=optionsList.length-1; i >=0 ; i--){
			optionsList[i].parentNode.removeChild(optionsList[i]);	
		}
	}
}//end of function js_ClearOptions

//added by VAN 05-08-08
function formatValue(num,dec){
	var nf = new NumberFormat(num.value);
	if (isNaN(dec)) dec = nf.NO_ROUNDING;
	nf.setPlaces(dec);
	num.value = nf.toFormatted();
}

function formatNumber(num,dec){
	var nf = new NumberFormat(num);
	if (isNaN(dec)) dec = nf.NO_ROUNDING;
	nf.setPlaces(dec);
	return nf.toFormatted();
}

function assignHauz(){
	document.getElementById('hauz_lot').value=document.getElementById('hauz_lot2').value.replace(',','');
}

function assignFood(){
	document.getElementById('food').value=document.getElementById('food2').value.replace(',','');
}

function assignLight(){
	document.getElementById('light').value=document.getElementById('light2').value.replace(',','');
}

function assignWater(){
	document.getElementById('water').value=document.getElementById('water2').value.replace(',','');
}

function assignTransport(){
	document.getElementById('transport').value=document.getElementById('transport2').value.replace(',','');
}

function assignOther(){
	document.getElementById('other').value=document.getElementById('other2').value.replace(',','');
}

function assignM_income(){
	document.getElementById('m_income').value=document.getElementById('m_income2').value.replace(',','');
}


function computeTotal(){
	var total=0, hauz_lot=0, food=0, light = 0;
	var water=0, transport=0, other=0;
	//alert($F('hauz_lot2'));
	if ($F('hauz_lot'))
		hauz_lot = parseInt($F('hauz_lot'));
		
	if ($F('food'))
		food = parseInt($F('food'));
	
	if (($F('light')))
		light = parseInt($F('light'));
	
	if (($F('water')))
		water = parseInt($F('water'));
	
	if ($F('transport'))
	  transport = parseInt($F('transport'));
		
	if ($F('other'))	
		other = parseInt($F('other'));
		
	total = hauz_lot + food + light + water + transport + other;
	
	document.getElementById('m_expenses').value = formatNumber(total,2);
}

function showAllSS(){
	//alert('showAll');	
	if (document.getElementById('checkShow').checked==true){
		//alert('showAll');	
		//document.getElementById('classification_prev').style.display='';
		document.getElementById('classification').style.display='';
	}else{
		//alert('hide');	
		//document.getElementById('classification_prev').style.display='none';
		document.getElementById('classification').style.display='none';
	}
}

//added by VAN 07-25-08
function computeCapita(){
	var percapita;
	var nodep = document.getElementById('nr_dep').value;
	var mincome = document.getElementById('m_income').value;
	if (((mincome)&&(mincome!=0)) && ((nodep)&&(nodep)!=0)){
		percapita = parseInt(mincome) / parseInt(nodep);
		document.getElementById('m_capita_income').value = formatNumber(percapita,2);
		document.getElementById('m_cincome').value = percapita;
	}else{
		if ((mincome)&&(mincome!=0)){
			document.getElementById('m_capita_income').value = formatNumber(mincome,2);
			document.getElementById('m_cincome').value = mincome;
		}else{
		document.getElementById('m_capita_income').value = "";
		document.getElementById('m_cincome').value = 0;
	}
}
}

//added by VAN 08-15-08
function js_addDefaultRow_bill(tableId){
	var dTable=$(tableId);
	if(dTable){
		rowSrc = '<tr><td style="" colspan="6">No billing requests available at this time.</td></tr>';
		document.getElementById('rqbillisttbody').innerHTML = rowSrc;
	}
}

function js_showBillDetails(){
	xajax_isForNewBilling($('encNr').value);
}

function setValue(data){
	var enc = $('encNr').value;
	var pid = $('pidNr').value;
	var bill_dt = $('bill_dt').value;
    var frm_dte = $('from_dt').value;
    var bill_nr = $('bill_nr').value;
    var deathdate = $('deathdate').value;//Added by Jarel 05/24/2013

	var detailed;
	detailed = 1;
	//http://localhost/hisspmc4dev-nick/modules/billing_new/bill-pdf-summary_new.php?ntid=false&lang=en&pid=1143527&encounter_nr=2013000632&from_dt=0&bill_dt=1388943856&nr=2014000172&IsDetailed=0&deathdate=
	var path = "";
	if(data!=0)
		path = "billing_new/bill-pdf-summary_new.php";
	else
		path = "billing/bill-pdf-summary.php";

	urlholder = '../../modules/'+path+'?rcalc=1&pid='+pid+'&encounter_nr='+enc+'&from_dt='+(getDateFromFormat(frm_dte, 'yyyy-MM-dd HH:mm:ss')/1000)+'&bill_dt='+(getDateFromFormat(bill_dt, 'yyyy-MM-dd HH:mm:ss')/1000)+'&nr='+bill_nr+'&IsDetailed='+detailed+'&deathdate='+deathdate;
	nleft = (screen.width - 680)/2;
	ntop = (screen.height - 520)/2;
	printwin = window.open(urlholder, "Print Billing", "toolbar=no, status=no, menubar=no, width=700, height=500, location=center, dependent=yes, resizable=yes, scrollbars=yes, top=" + ntop + ",left=" + nleft);
}


//Added by Jarel 05/06/2013
function showBillWithDiscount(){

	var enc = $('encNr').value;
	var pid = $('pidNr').value;
	var bill_dt = $('bill_dt').value;
    var frm_dte = $('from_dt').value;
    var bill_nr = $('bill_nr').value;
    var deathdate = $('deathdate').value;
	
	var detailed;
	detailed = 0;
	urlholder = '../../modules/social_service/seg-social-billing-summary.php?rcalc=1&pid='+pid+'&encounter_nr='+enc+'&from_dt='+(getDateFromFormat(frm_dte, 'yyyy-MM-dd HH:mm:ss')/1000)+'&bill_dt='+(getDateFromFormat(bill_dt, 'yyyy-MM-dd HH:mm:ss')/1000)+'&nr='+bill_nr+'&IsDetailed='+detailed+'&deathdate='+deathdate;
	
	nleft = (screen.width - 680)/2;
	ntop = (screen.height - 520)/2;
	printwin = window.open(urlholder, "Print Billing", "toolbar=no, status=no, menubar=no, width=700, height=500, location=center, dependent=yes, resizable=yes, scrollbars=yes, top=" + ntop + ",left=" + nleft);				

}

function js_showSSCert(){
		//var rpath = $('root_path').value;
		var enc = $('encNr').value;
		var pid = $('pidNr').value;

		urlholder = '../../modules/social_service/social_service_cert.php?pid='+pid+'&encounter_nr='+enc;

		nleft = (screen.width - 680)/2;
		ntop = (screen.height - 520)/2;
		printwin = window.open(urlholder, "Print SS Certificate", "toolbar=no, status=no, menubar=no, width=700, height=500, location=center, dependent=yes, resizable=yes, scrollbars=yes, top=" + ntop + ",left=" + nleft);
}

function js_addRow_bill(tableId){
	var dTable=$(tableId);
	if(dTable){
		inputbtn ='<button id="btnbill" onclick="javascript:void(0);js_showBillDetails();">Show Billing Details</button>';
					
		rowSrc = '<tr>'+
						'<td align="left" colspan="5"><span>Billing Statement</span></td>'+  
						'<td align="right">'+inputbtn+'</td>'+
				  '</tr>';
				  
		document.getElementById('rqbillisttbody').innerHTML = rowSrc;
	}
}
//-----------
function assignValue(details){
	//alert('hdhjdhjshf='+details.address);
	$('address').innerHTML = details.address;
}

//added by VAN 07-19-2010
function editProfile(encounter_nr, current_encounter_nr){
		//alert('encounter_nr = '+encounter_nr);
		//alert('current encounter_nr= '+current_encounter_nr);

	//alert('sss = ('+encounter_nr+" == "+current_encounter_nr+") => "+(encounter_nr == current_encounter_nr));
	if(encounter_nr == current_encounter_nr){
		profileShow(0);
	//alert('same ang encounter');
		xajax_ViewSocServPatient(encounter_nr);
			 // Validate the entries in the form update profile.
	YAHOO.example.container.dialog2.validate = function(){
		var data = this.getData();

		//if(data.resp =="" || data.relation == "" || data.s_income=="" || data.m_income=="" || data.nr_dep == ""){
		//edited by VAN 05-09-08

		/*if(data.resp =="" || data.relation == "" || data.s_income=="" || data.m_income==""
			|| data.nr_dep == "" || data.hauz_lot == "" || data.food == "" || data.light ==""
			|| data.water == "" || data.transport == "" || data.other == ""){*/
		if(data.resp =="" || data.relation == "" || data.s_income=="" || data.m_income==""
			|| data.nr_dep == ""){
		//if($('respondent') =="" || $('relation_patient')=="" || $('occupation')=="" || $('s_income')=='0'
		//	|| $('monthly_income') == "" || $('nrdep')== "" || $('nrchldren')=="" || $('capita_income')) {
			alert("Fill all the fields.");
			return true;

		}else{
				//alert(data.resp);
				xajax_UpdateProfileForm(data);


				$('respondent').innerHTML = data.resp.toUpperCase();
				$('h_respondent').value = data.resp;
				$('relation_patient').innerHTML = data.relation.toUpperCase();
				$('h_relation_patient').value = data.relation;
				$('occupation').innerHTML = $('occupation_select').options[$('occupation_select').selectedIndex].text.toUpperCase();
				$('h_occupation').value = $('occupation_select').value;
				$('source_income').innerHTML = data.s_income.toUpperCase();
				$('h_source_income').value = data.s_income;

				//edited by VAN 05-09-08
				$('monthly_income').innerHTML = formatNumber(data.m_income,2);
				$('h_monthly_income').value = formatNumber(data.m_income,2);

				$('nrdep').innerHTML = data.nr_dep;
				$('h_nrdep').value = data.nr_dep;

				//added by VAN 07-25-08
				$('nrchldren').innerHTML = data.nr_chldren;
				$('h_nrchldren').value = data.nr_chldren;

				$('capita_income').innerHTML = formatNumber(data.m_capita_income,2);
				$('h_capita_income').value = formatNumber(data.m_capita_income,2);
				//---------------

				//added by VAN 05-09-08
				var total=0;

				total = parseInt(data.hauz_lot) + parseInt(data.food) + parseInt(data.light) + parseInt(data.water) + parseInt(data.transport) + parseInt(data.other);
				$('h_monthly_expenses').value = formatNumber(total,2);
				$('monthly_expenses').innerHTML = formatNumber(total,2);

				//alert($('mpid').value);
				xajax_setMSS($('mpid').value);

			return false;
		}
	};

		// Render the Dialog1
		YAHOO.example.container.dialog2.render();
		YAHOO.example.container.dialog2.show();

	}
	else{
	//alert('nisulod sa else '+encounter_nr);
	xajax_ViewSocServPatient(encounter_nr);


		// Validate the entries in the form update profile.
	YAHOO.example.container.dialog2.validate = function(){
		var data = this.getData();
		//edited by VAN 05-09-08

		if(data.resp =="" || data.relation == "" || data.s_income=="" || data.m_income==""
			|| data.nr_dep == ""){
			alert("Fill all the fields.");
			return true;

		}else{

				xajax_UpdateProfileForm(data);

				$('respondent').innerHTML = data.resp.toUpperCase();
				$('h_respondent').value = data.resp;
				$('relation_patient').innerHTML = data.relation.toUpperCase();
				$('h_relation_patient').value = data.relation;
				$('occupation').innerHTML = $('occupation_select').options[$('occupation_select').selectedIndex].text.toUpperCase();
				$('h_occupation').value = $('occupation_select').value;
				$('source_income').innerHTML = data.s_income.toUpperCase();
				$('h_source_income').value = data.s_income;

				//edited by VAN 05-09-08
				$('monthly_income').innerHTML = formatNumber(data.m_income,2);
				$('h_monthly_income').value = formatNumber(data.m_income,2);

				$('nrdep').innerHTML = data.nr_dep;
				$('h_nrdep').value = data.nr_dep;

				//added by VAN 07-25-08
				$('nrchldren').innerHTML = data.nr_chldren;
				$('h_nrchldren').value = data.nr_chldren;

				$('capita_income').innerHTML = formatNumber(data.m_capita_income,2);
				$('h_capita_income').value = formatNumber(data.m_capita_income,2);
				//---------------

				//added by VAN 05-09-08
				/*var total=0;
				var for_food;
				var for_hauz_lot;
				var for_light;
				var for_water;
				var for_transport;
				var for_other;

				if(data.hauz_lot=="")
					for_hauz_lot = data.hauz_lot2;
				else
					for_hauz_lot = data.hauz_lot;  */

				total = parseInt(data.hauz_lot) + parseInt(data.food) + parseInt(data.light) + parseInt(data.water) + parseInt(data.transport) + parseInt(data.other);
				$('h_monthly_expenses').value = formatNumber(total,2);
				$('monthly_expenses').innerHTML = formatNumber(total,2);

				//alert($('mpid').value);
				xajax_setMSS($('mpid').value);

			return false;
		}
	};

			 // Render the Dialog1
			 YAHOO.example.container.dialog2.render();
			 YAHOO.example.container.dialog2.show();

                        //edit by VAN 02/09/2012 need to change and trace
             profileShow(1);

	}




}

function profileShow(is_readonly){
	var bool;
	if (is_readonly)
		bool = true;
	else
		bool = false;

	$('resp').readOnly = bool;
	$('address').readOnly = bool;
	$('relation').readOnly = bool;
	$('nr_dep').readOnly = bool;
	$('nr_chldren').readOnly = bool;
	$('s_income').readOnly = bool;
	$('m_income2').readOnly = bool;
	//$('occupation_select').emptyOption = true;
	$('food2').readOnly = bool;
	$('light2').readOnly = bool;
	$('water2').readOnly = bool;
	$('transport2').readOnly = bool;
	$('other2').readOnly = bool;
	$('hauz_lot2').readOnly = bool;
	//$('hauz_lot').onselect = false;

	var temp = document.getElementsByName('hauz_lot_type');
	 for (var i=temp.length-1; i > -1; i--) {
		temp[i].disabled = bool;
	 }

	$('occupation_select').disabled = bool;

}

function deleteProfile(encounter_nr){
		//alert('delete profile = '+encounter_nr);
		var del = confirm("Are you sure you want to delete this data?");
		if(del==true){
			xajax_RemoveSocServPatient(encounter_nr);
		}
}
//---------

//Added by Cherry 07-20-2010
function reportProfile(encounter_nr, pid){
		//alert('encounter nr= '+encounter_nr+' pid= '+pid);
		urlholder = '../../modules/social_service/seg-socserv-mswd_form3.php?pid='+pid+'&encounter_nr='+encounter_nr;

		nleft = (screen.width - 680)/2;
		ntop = (screen.height - 520)/2;
		printwin = window.open(urlholder, "Print MSWD Form 3", "toolbar=no, status=no, menubar=no, width=700, height=500, location=center, dependent=yes, resizable=yes, scrollbars=yes, top=" + ntop + ",left=" + nleft);
}
//End Cherry

//Added by Cherry 07-19-10
//edited by VAN 07-19-2010
function js_addRow_Profile(details) {
//alert('HOY!'+details["FLAG"]);
	var btn;
	list = $("prof");
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			var lastRowNum = null,
					//id = details["encounter_nr"]+details["pid"]+details["timestamp"];
					id = details["encounter_nr"];
					//items = document.getElementsByName('prof_items[]');
					dRows = dBody.getElementsByTagName("tr");
			if (details["FLAG"]=="1") {
				alt = (dRows.length%2)+1

				//btn = '<a href="javascript:void(0);" onclick="editProfile('+details["encounter_nr"]+');"><img src="../../images/cashier_edit.gif" border="0"></a>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="deleteProfile('+details["encounter_nr"]+');"><img src="../../images/close_small.gif" border="0"></a>';
				
				//updated by jane 10/17/2013
				//btn = '<a href="javascript:void(0);" onclick="editProfile('+details["encounter_nr"]+','+$('encounter_nr').value+');"><img border="0" src="../../images/cashier_view.png"></a>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="showProfileForm('+details["encounter_nr"]+');"><img src="../../images/cashier_edit.gif" border="0"></a>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="deleteProfile('+details["encounter_nr"]+');"><img src="../../images/close_small.gif" border="0"></a>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="reportProfile(\''+details['encounter_nr']+'\', \''+details['pid']+'\');"><img src="../../images/cashier_reports.gif" border="0"></a>';
				btn = '<a href="javascript:void(0);" onclick="showProfileForm('+details["encounter_nr"]+');"><img src="../../images/cashier_edit.gif" border="0"></a>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="deleteProfile('+details["encounter_nr"]+');"><img src="../../images/close_small.gif" border="0"></a>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="reportProfile(\''+details['encounter_nr']+'\', \''+details['pid']+'\');"><img src="../../images/cashier_reports.gif" border="0"></a>';

				src =
					'<td class="centerAlign" style="color:#006000">'+details["create_date"]+'</td>'+
					'<td style="color:#000066; white-space:nowrap">'+details["encounter_nr"]+'</td>'+
					'<td>'+details["encoder"]+'</td>'+
					'<td align="center">'+details["discountid"]+'</td>'+
					'<td class="centerAlign">'+btn+'</td>'+
				'</tr>';
			}
			else {
				src = "<tr><td colspan=\"8\">List is currently empty...</td></tr>";
			}
			dBody.innerHTML += src;
			return true;
		}
	}
	return false;
}
//End Cherry

function js_addRow_Classification(details) {
	list = $("cf");
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			var lastRowNum = null,
					id = details["encounter_nr"]+details["pid"]+details["timestamp"];
					items = document.getElementsByName('cf_items[]');
					dRows = dBody.getElementsByTagName("tr");
			if (details["FLAG"]=="1") {
				alt = (dRows.length%2)+1
				src = 
					'<tr'+((dRows.length%2>0)?' class="alt"':'')+'>' +
					'<input type="hidden" name="cf_encounter_nr[]" id="cf_encounter_nr'+id+'" value="'+details["encounter_nr"]+'" />'+
					'<input type="hidden" name="cf_pid[]" id="cf_pid'+id+'" value="'+details["pid"]+'" />'+
					'<input type="hidden" name="cf_timestamp[]" id="cf_timestamp'+id+'" value="'+details["timestamp"]+'" />'+
					'<input type="hidden" name="cf_grant_date[]" id="cf_grant_date'+id+'" value="'+details["grant_date"]+'" />'+
					'<input type="hidden" name="cf_discount[]" id="cf_discount'+id+'" value="'+details["discount"]+'" />'+
					'<input type="hidden" name="cf_personnel[]" id="cf_personnel'+id+'" value="'+details["personnel"]+'" />'+
					'<input type="hidden" id="cf_mod1_code'+id+'" value="'+details["modifier1_code"]+'" />'+
					'<input type="hidden" id="cf_mod2_code'+id+'" value="'+details["modifier2_code"]+'" />'+
					'<input type="hidden" id="cf_mod3_code'+id+'" value="'+details["modifier3_code"]+'" />'+
					'<input type="hidden" id="cf_mod1_text'+id+'" value="'+details["modifier1_text"]+'" />'+
					'<input type="hidden" id="cf_mod2_text'+id+'" value="'+details["modifier2_text"]+'" />'+
					'<input type="hidden" id="cf_mod3_text'+id+'" value="'+details["modifier3_text"]+'" />'+
					'<input type="hidden" name="cf_items[]" id="row'+id+'" value="'+id+'" />';
				if (details['modifier1_text'] || details['modifier2_text'] || details['modifier3_text']) {
					btn = '<img src="../../images/cashier_view.gif" border="0" align="absmiddle" style="cursor:default" onmouseover="showClassificationDetails(\''+id+'\')" onmouseout="nd()" />';
				}
				else
					btn = '';
				src+=
					'<td class="centerAlign" style="color:#006000">'+details["discount"]+'</td>'+
					'<td style="color:#000066; white-space:nowrap">'+details["grant_date"]+'</td>'+
					'<td>'+details["personnel"]+'</td>'+
					'<td class="centerAlign">'+btn+'</td>'+
				'</tr>';
			}
			else {
				src = "<tr><td colspan=\"8\">List is currently empty...</td></tr>";	
			}
			dBody.innerHTML += src;
			return true;
		}
	}
	return false;
}

function js_addRow_Request(details) {
	list = $("rlst");
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			var lastRowNum = null,
					id = details["ref_no"];
					dRows = dBody.getElementsByTagName("tr");
			if (details["FLAG"]=="1") {
				alt = (dRows.length%2)+1
				inputbtn ='<button class="jedInput" id="btn'+id+'" onclick="javascript:void(0);js_showDetails(\''+details['ref_no']+'\', \''+details['dept']+'\');" style="color:#000060">Apply</button>';
				switch(details["dept"]){
					case 'LB':
						dept = 'LAB';
					break;

					case 'BB':
						dept = 'BLOOD';
						break;

					case 'SPL':
						dept = 'SPLAB';
						break;

					case 'RD':
						dept = 'RADIO';
					break;
					case 'P':
						dept = 'PHARMA';
					break;
                    case 'M':
                        dept = 'MISC';
                    break;
				}				
				src = 
				'<tr'+((dRows.length%2>0)?' class="alt"':'')+'>' +
				/*
					'<td class="centerAlign" style="color:#000060">'+details["discount"]+'</td>'+
					'<td style="color:#660000; white-space:nowrap">'+details["grant_date"]+'</td>'+
					'<td>'+details["personnel"]+'</td>'+
					'<td class="centerAlign"><img src="../../images/cashier_view.gif"></td>'+
				*/	
					
					'<td class="centerAlign" style="color:#660000">'+details["ref_no"]+'</td>'+
					'<td class="centerAlign">'+details["request_date"]+'</td>'+
					'<td class="centerAlign">'+dept+'</td>'+
					'<td class="rightAlign">'+details["total_charge"]+'</td>'+ 
					'<td class="centerAlign">'+inputbtn+'</td>'+
				'</tr>';
			}
			else {
				src = "<tr><td colspan=\"8\">List is currently empty...</td></tr>";	
			}
			dBody.innerHTML += src;
			return true;
		}
	}
	return false;
}

function applyBillDiscount(){
	var discount_amount;
	var encounter_nr = $('encounter_nr').value;
	var bill_dt = $('bill_dt').value;
	var frm_dte = Date();

	frm_dte = getDateFromFormat(frm_dte, 'yyyy-MM-dd HH:mm:ss')/1000;
	bill_dt = getDateFromFormat(bill_dt, 'yyyy-MM-dd HH:mm:ss')/1000;

	while (isNaN(parseFloat(discount_amount)) || parseFloat(discount_amount)<0) {
			discount_amount = prompt("Enter the amount to be paid in the Cashier:")
			if (discount_amount === null) return false;
	}
	//alert(frm_dte+" - "+bill_dt);
	xajax_ajaxApplyBillDiscount(encounter_nr, discount_amount, frm_dte, bill_dt);
}

function discardBillDiscount(){
    if (confirm('Will omit the fixed discount. Do you wish to continue?')){
        var encounter_nr = $('encounter_nr').value;

        xajax_ajaxDiscardBillDiscount(encounter_nr);
    }    
}

//added by VAN 11-19-09
function openLingapReport(pid,discountId,encounter_nr,encoder){
		//seg-report-patrequest-for-lingap.php?pid='.$_GET['pid'].'&encounter_nr='.$_GET['encounter_nr'].'&discountid='.$_GET['discountid'].'&encoder='.$_SESSION['sess_temp_userid']
		window.open("../../modules/social_service/seg-report-patrequest-for-lingap.php?pid="+pid+"&encounter_nr="+encounter_nr+"&discountid="+discountId+"&encoder="+encoder+"&showBrowser=1","viewClinicalForm","width=800,height=700,menubar=no,resizable=yes,scrollbars=yes");
}

function showLingap(is_show){
	//alert('showLingap = '+is_show);
		if (is_show==1)
				$('lingap_row').style.display='';
		else
				$('lingap_row').style.display='none';
}

//added by VAN 12-04-2012
function showBillBtn(is_show){
    if (is_show==1)
        $('applybill_row').style.display='';
    else
        $('applybill_row').style.display='none';
}

//Added by Cherry 07-21-10
function checkHouse(val){
		//alert('hello ='+val);
		if (val==3 || val==4 || val==5){
			//show doctor as signatory
			document.getElementById('hauz_lot2').style.display = '';
		}else{
			//hide doctor as signatory
			document.getElementById('hauz_lot2').style.display = 'none';
		}
	}

function setDataValues(details){

	//alert('set = '+details.hauz_lot_type);
	$('address').innerHTML = details.address;
	$('resp').value = details.resp;
	$('relation').value = details.relation;
	$('occupation_select').value = details.occupation_select;
	$('hauz_lot2').value = formatNumber(details.hauz_lot2,2);
	$('hauz_lot').value = details.hauz_lot2;
	$('food2').value = formatNumber(details.food2,2);
	$('food').value = details.food2;
	$('nr_dep').value = details.nr_dep;
	$('nr_chldren').value = details.nr_chldren;
	$('water2').value = formatNumber(details.water2,2);
	$('water').value = details.water2;
	$('s_income').value = details.s_income;
	$('transport2').value = formatNumber(details.transport2,2);
	$('transport').value = details.transport2;
	$('m_income2').value = formatNumber(details.m_income2,2);
	$('m_income').value = details.m_income2;
	$('other2').value = formatNumber(details.other2,2);
	$('other').value = details.other2;
	$('m_capita_income').value = formatNumber(details.m_capita_income,2);
	$('light2').value = formatNumber(details.light2,2);
	$('light').value = details.light2;
	$('m_expenses').value = formatNumber(details.m_expenses,2);

	//$('occupation_select').disabled = true;
	 /*var temp = document.getElementsByName('hauz_lot_type');
	 for (var i=temp.length-1; i > -1; i--) {
		temp[i].disabled = true
		}  */
	 /*
	 var button = document.getElementsByTagName('button');
	 button.style.display ='none';
	 //alert('button  = '+button);
	 //button.style
	 YAHOO.example.container.dialog2.hide(); */
	 /*
	 if(details.hauz_lot_type=='1'){
		alert('Im here!');
			$('hauz_lot_type1') .checked = true;
			$('hauz_lot_type2') .checked = false;
			$('hauz_lot_type3') .checked = false;
			$('hauz_lot_type4') .checked = false;
			$('hauz_lot_type5') .checked = false;
	 }else if(details.hauz_lot_type=='2'){
			$('hauz_lot_type1') .checked = false;
			$('hauz_lot_type2') .checked = true;
			$('hauz_lot_type3') .checked = false;
			$('hauz_lot_type4') .checked = false;
			$('hauz_lot_type5') .checked = false;
	 }else if(details.hauz_lot_type=='3'){
			$('hauz_lot_type1') .checked = false;
			$('hauz_lot_type2') .checked = false;
			$('hauz_lot_type3') .checked = true;
			$('hauz_lot_type4') .checked = false;
			$('hauz_lot_type5') .checked = false;
	 }else if(details.hauz_lot_type=='4'){
			$('hauz_lot_type1') .checked = false;
			$('hauz_lot_type2') .checked = false;
			$('hauz_lot_type3') .checked = false;
			$('hauz_lot_type4') .checked = true;
			$('hauz_lot_type5') .checked = false;
	 }else if(details.hauz_lot_type=='5'){
			$('hauz_lot_type1') .checked = false;
			$('hauz_lot_type2') .checked = false;
			$('hauz_lot_type3') .checked = false;
			$('hauz_lot_type4') .checked = false;
			$('hauz_lot_type5') .checked = true;
	 }   */
	// alert('Im here = '+details.hauz_lot_type);
	switch(details.hauz_lot_type){
		case '1'  :
								document.getElementById('hauz_lot_type1').checked = true;
								break;
		case '2'  :

								document.getElementById('hauz_lot_type2').checked = true;
								break;
		case '3'	:
								document.getElementById('hauz_lot_type3').checked = true;
								break;
		case '4'	:

								document.getElementById('hauz_lot_type4').checked = true;
								break;
		case '5'	:

								document.getElementById('hauz_lot_type5').checked = true;
								break;
		default : 	document.getElementById('hauz_lot_type1').checked = false;
								document.getElementById('hauz_lot_type2').checked = false;
								document.getElementById('hauz_lot_type3').checked = false;
								document.getElementById('hauz_lot_type4').checked = false;
								document.getElementById('hauz_lot_type5').checked = false;
								break;
	} 
}

function showProfileForm(enc){
    var pid = $('pid').value;
    var encounter_nr; 
    var mode;
    var parent_Enc = $('encounter_nr').value;
    
    if(enc || enc=='0'){
        encounter_nr = enc;
        mode = 'update';    
    }else{
        encounter_nr = $('encounter_nr').value;
        mode = 'new'; 
    }
    
                                                       
    return overlib(OLiframeContent('social_service_intake.php?pid='+pid+'&encounter_nr='+encounter_nr+'&mode='+mode+'&parent_enc='+parent_Enc, 850,480, 'frad-request', 0, 'auto'),
                        WIDTH , 830, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL , CLOSETEXT,
                        '<img src=../../images/close_red.gif border=0 onClick=refreshWindow2();>', CAPTION, 'Patient Intake',
                        MIDX, 0, MIDY, 0, STATUS,'Patient Intake');
}

/*function checktrans(){
	if($('encounter_nr').value!='0'){
		showProfileForm();
	}else{
		alert("This patient has no transaction or already discharged.");
		return false;
	}
}*/

//Added by Jarel 06/14/2013
function applyConsultation(){
	var sw_nr = jQuery('#encoder_id').val();
	var pid = jQuery('#pid').val();
	var stat = $('consultation').value;
	var amount;
	
	if(stat!=0){
		xajax_applyConsultation(pid,sw_nr,stat);
	}else{
		jQuery( "#consultation_dialog" ).dialog({
        autoOpen: true,
        modal:true,
        show: "blind",
        hide: "explode",
        title: "Consultation",
        position: "top",
        buttons: {
                YES: function() {
            		xajax_applyConsultation(pid,sw_nr,stat);
            		jQuery(this).dialog( "close" );  
				},
                NO: function(){
            		while (isNaN(amount) || amount===null) {
							amount = prompt("Enter the amount to be paid in the Cashier:");
							if (amount === null)
									return false;
						
					xajax_applyConsultationWithAmount(amount,pid);
					}
                    jQuery(this).dialog( "close" );
                }
        },
        close: function(){
        jQuery(this).dialog( "close" );
        }
    });
	}

}
//---------------------