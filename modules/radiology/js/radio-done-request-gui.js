function jsRadioNoFoundRequest(sub_dept_nr){
	var dTable,dTBody,rowSrc;

	if (dTable=document.getElementById('Ttab'+sub_dept_nr)) {
		dTBody=dTable.getElementsByTagName("tbody")[0];
		rowSrc = '<tr><td colspan="20" align="center" bgcolor="#FFFFFF" style="color:#FF0000; font-family:"Arial", Courier, mono; font-style:Bold; font-weight:bold; font-size:12px;">NO MATCHING DONE REQUEST FOUND</td></tr>';
		dTBody.innerHTML += rowSrc;
//alert("jsRadioNoFoundRequest : dTBody.innerHTML : \n"+dTBody.innerHTML);
	}
}

function viewResult(pid,batchnr){
	window.open("../../modules/radiology/certificates/seg-radio-report-pdf.php?pid="+pid+"&batch_nr_grp="+batchnr+"&showBrowser=1","viewRadiologyResult","width=950,height=700,fullscreen=yes,menubar=no,resizable=yes,scrollbars=yes");
}

function jsRadioRequest(sub_dept_nr,No,batchNo,refno,dateRq,sub_dept_name,pid,rid,sex,lName,fName,bDate,rStatus,rPriority,rPType,loc,service_code, is_perpatient, borrow, borrow_details){
	var dTable,dTBody,dRows,rowSrc,sid,lang,radio_findings_link;
	var PatType;

	if (dTable=document.getElementById('Ttab'+sub_dept_nr)) {

		dTBody=dTable.getElementsByTagName("tbody")[0];

		sid = $F('sid');
		lang = $F('lang');
//alert("jsRadioRequest : before radio_findings_link ");
		if (is_perpatient==1){
			radio_findings_link = '<a href=javascript:void(0) onClick="viewResult(\''+pid+'\',\''+batchNo+'\')">'+
					'<img src="../../images/findings.gif" border="0"></a>';
		}else{
			radio_findings_link = '<a href=seg-radio-findings.php?sid='+sid+'&lang='+lang+
					'&user_origin=radio&batch_nr='+batchNo+'&pid='+pid+'&target=radio_done>'+
					'<img src="../../images/findings.gif" border="0"></a>';
		}
//alert("jsRadioRequest :  radio_findings_link ='"+radio_findings_link+"'");
		/*
		if (rPType==1)
			PatType = "ERPx";
		else if (rPType==2)
			PatType = "OPDPx";
		else if ((rPType==3)||(rPType==4))
			PatType = "INPx";	
		*/
		
		//added by VAN 02-15-10
		toolTipTextHandler = ' onMouseOver="return overlib($(\'toolTipText'+batchNo+'\').value, CAPTION,\'Details\',  '+
												 '  TEXTPADDING, 8, CAPTIONPADDING, 4, TEXTFONTCLASS, \'oltxt\', CAPTIONFONTCLASS, \'olcap\', '+
												 '  WIDTH, 250,FGCLASS,\'olfgjustify\',FGCOLOR, \'#bbddff\');" onmouseout="nd();"';
																
		var borrowstat;
		//alert(borrow_details);
		if (borrow==1)
			 borrowstat ='<img src="../../images/borrowed.gif" border="0" >';
		else
			 borrowstat ='<img src="../../images/available.gif" border="0" >';   
		//------
		
		if(batchNo){
//alert("jsRadioRequest :  if(batchNo) is true 1 : rowSrc="+rowSrc);
			if (is_perpatient==1){
					rowSrc = '<tr>'+
							'<td>'+No+'</td>'+
							'<td>'+batchNo+'</td>'+
							'<td>'+refno+'</td>'+
							'<td>'+dateRq+'</td>'+
							'<td>'+sub_dept_name+'</td>'+
							'<td>'+service_code+'</td>'+
							'<td>'+rStatus+'</td>'+
							'<td>'+rPriority+'</td>'+
							'<td align="center">'+radio_findings_link+'</td>'+
							'</tr>';
			}else{
					rowSrc = '<tr>'+
							'<td>'+No+'</td>'+
							'<td>'+batchNo+'</td>'+
							'<td>'+refno+'</td>'+
							'<td>'+dateRq+'</td>'+
							'<td>'+sub_dept_name+'</td>'+
							'<td>'+service_code+'</td>'+
							'<td>'+rid+'</td>'+
							'<td>'+sex+'</td>'+
							'<td>'+lName+'</td>'+
							'<td>'+fName+'</td>'+
							'<td>'+bDate+'</td>'+
							'<input type="hidden" name="toolTipText'+batchNo+'" id="toolTipText'+batchNo+'" value="'+borrow_details+'" />'+
							'<td align="center" '+toolTipTextHandler+'>'+rStatus+' '+borrowstat+'</td>'+
							'<td>'+rPriority+'</td>'+
							'<td><span style="font-size:10px">'+rPType+'</span></td>'+
							'<td><span style="font-size:10px">'+loc+'</span></td>'+
							'<td align="center">'+radio_findings_link+'</td>'+
							'</tr>';
			}
//alert("jsRadioRequest :  if(batchNo) is true 2 : rowSrc="+rowSrc);
		}else{
			rowSrc = '<tr><td colspan="16" style="">No such record exists...</td></tr>';
		}
		dTBody.innerHTML += rowSrc;
//alert("jsRadioRequest : dTBody.innerHTML : \n"+dTBody.innerHTML);
	}
} 

function jsSortHandler(items,oitem,dir,sub_dept_nr){
	var tab = dojo.widget.byId('tbContainer').selectedChild;
	var key, pgx, thisfile, rpath, mode;		
	
	setOItem(items);
	setODir(dir);

	mode = document.getElementById('smode').value;
	rpath = document.getElementById('rpath').value;
	pgx = document.getElementById('pgx').value;
	key = document.getElementById('skey').value;
	thisfile = document.getElementById('thisfile').value; 
	oitem = document.getElementById('oitem').value; 
	odir = document.getElementById('odir').value; 
	
	var is_doctor = $F('is_doctor');
	var encounter_nr = $F('encounter_nr');
	
//	alert("jsSortHandler : you selected  "+ tab + "\n key="+key+ "\n pgx ="+pgx+ "\n rpath="+rpath+"\n mode="+mode+ "\n sub_dept_nr="+ sub_dept_nr+"\n oitem="+oitem+"\n odir="+odir);
	//ColHeaderRadioRequest($tbId, $tbody, $searchkey,$sub_dept_nr,$pgx, $thisfile, $rpath,$mode)
	xajax_PopulateRadioDoneRequest('T'+tab, key, sub_dept_nr, pgx, thisfile, rpath, mode,oitem,odir,encounter_nr, is_doctor);
}//end of function jsSortHandler
	
function setTotalCount(val){
	$('totalcount').value=val;
}

function setPgx(val){
	$('pgx').value=val;
}

function setOItem(val){
	$('oitem').value=val;
}

function setODir(val){
	$('odir').value=val;
}

	/*	
		This will trim the string i.e. no whitespaces in the
		beginning and end of a string AND only a single
		whitespace appears in between tokens/words 
		input: object
		output: object (string) value is trimmed
	*/
//function trimStringSearchMask(objct){
function trimStringSearchMask(){
//	objct.value = objct.value.replace(/^\s+|\s+$/g,"");
//	objct.value = objct.value.replace(/\s+/g," "); 
	$('searchkey').value = $('searchkey').value.replace(/^\s+|\s+$/g,"");
	$('searchkey').value = $('searchkey').value.replace(/\s+/g," "); 
}/* end of function trimString */
				
function chkSearch(d){
//	alert("chkSearch : $F('searchkey') = '"+$F('searchkey')+"'");
//	if((d.searchkey.value=="") || (d.searchkey.value==" ")){
//		d.searchkey.focus();
	var is_perpatient = document.getElementById('is_perpatient').value;
	
	if(($F('searchkey')=="") || ($F('searchkey')==" ")){
		$('searchkey').focus();
		return false;
	}else{
//		alert("chkSearch : $F('searchkey') = '"+$F('searchkey')+"'; TRUE");
		$('skey').value=$F('searchkey');
		handleOnclick();
//		return true;
	}
}

function handleOnclick(){
// 	var tab = dojo.widget.byId('tbContainer').selectedChild;
	var tab;
	var key, pgx, thisfile, rpath, sub_dept_nr, mode;
	var is_perpatient = document.getElementById('is_perpatient').value;

	try{
		tab = dojo.widget.byId('tbContainer').selectedChild;
	}catch(e){
		//alert("e.message = "+e.message);
		tab = 'tab0';   // use in initial loading
	}

	mode = document.getElementById('smode').value;
	rpath = document.getElementById('rpath').value;
	setPgx(0);   // resets to the first page every time a tab is clicked
	pgx = document.getElementById('pgx').value;
	key = document.getElementById('skey').value;
	thisfile = document.getElementById('thisfile').value; 
	oitem = 'create_dt'; 
	odir = 'ASC'; 
	sub_dept_nr = tab.substr(3);
//	alert("handleOnclick: tab="+ tab + "\n key="+key+ "\n pgx ="+pgx+ "\n rpath="+rpath+"\n mode="+mode+ "\n sub_dept_nr="+ sub_dept_nr+"\n oitem="+oitem+"\n odir="+odir);
	var is_doctor = $F('is_doctor');
	var encounter_nr = $F('encounter_nr');
	var pid;
	
	if (is_perpatient==1){
		pid = document.getElementById('pid').value;
	}else
		pid = '';
	
	xajax_PopulateRadioDoneRequest('T'+tab, key, sub_dept_nr, pgx, thisfile, rpath, mode,oitem,odir,encounter_nr, is_doctor, pid, is_perpatient);
 }

function eventOnClick(){
//	dojo.event.connect(dojo.widget.byId('demo').tablist, "onSelectChild","handleOnclick");
	dojo.event.connect(dojo.widget.byId('tbContainer').tablist, "onButtonClick","handleOnclick");

}

function checkEnter(e){
	//alert('e = '+e);	
	var characterCode; //literal character code will be stored in this variable

	if(e && e.which){ //if which property of event object is supported (NN4)
		e = e;
		characterCode = e.which; //character code is contained in NN4's which property
	}else{
		//e = event;
		characterCode = e.keyCode; //character code is contained in IE's keyCode property
	}

	if(characterCode == 13){ //if generated character code is equal to ascii 13 (if enter key)
		$('skey').value=$('search-refno').value; chkSearch();
	}else{
		return true;
	}		
}

//added by VAN 01-29-10
function isValidSearch(key) {          

		if (typeof(key)=='undefined') return false;
		var s=key.toUpperCase();
		return (
						/^[A-Z0-9Ñ\-\.]{2}[A-Z0-9Ñ\-\. ]*\s*,\s*[A-Z0-9Ñ\-\.]{2}[A-Z0-9Ñ\-\. ]*$/.test(s) ||
						/^\d{1,2}\/\d{1,2}\/\d{4}$/.test(s) ||
						/^\d{1,2}\-\d{1,2}\-\d{4}$/.test(s) ||
						/^\d+$/.test(s)
		);
}
		 
function DisabledSearch(){
	var b=isValidSearch(document.getElementById('searchkey').value);
		document.getElementById("search-btn").style.cursor=(b?"pointer":"default");
		document.getElementById("search-btn").disabled = !b;
}

//--------------------------
