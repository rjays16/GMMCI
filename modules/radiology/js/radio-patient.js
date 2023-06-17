
function pSearchClose() {
	cClick();  //function in 'overlibmws.js'
}

	function checkAll(parent, flag) {
		var p=$(parent);
		var cList=p.getElementsByTagName('input');		
		for (var i=0;i<cList.length;i++) {
			if (cList[i].type=="checkbox")
				cList[i].checked=flag;
		}
	}

	function countSelected(parent) {
		var count=0;
		var p=$(parent);
		var cList=p.getElementsByTagName('input');		
		for (var i=0;i<cList.length;i++) {
			if (cList[i].type=="checkbox") {
				if (cList[i].checked&&cList[i].id!='chkall') count++;
			}
		}
		return count;
	}

function jsRadioNoFoundRequest(sub_dept_nr){
	var dTable,dTBody,rowSrc;

	if (dTable=document.getElementById('Ttab'+sub_dept_nr)) {
		dTBody=dTable.getElementsByTagName("tbody")[0];
		rowSrc = '<tr><td colspan="12" align="center" bgcolor="#FFFFFF" style="color:#FF0000; font-family:"Arial", Courier, mono; font-style:Bold; font-weight:bold; font-size:12px;">No such record exists...</td></tr>';
		dTBody.innerHTML += rowSrc;
//alert("jsRadioNoFoundRequest : dTBody.innerHTML : \n"+dTBody.innerHTML);
	}
}

//added by VAN 07-24-08
function refreshWindow(){
	window.location.href=window.location.href;
}

function jsRadioRequest(sub_dept_nr,No,batchNo,serviceCode,serviceName,r_doc,dt_request,dt_service,available, borrower, date_borrowed, date_returned){
	var dTable,dTBody,rowSrc,sid,lang,rpath,pid,rid,borrowImg,checkBox;
msg =" batchNo ='"+batchNo+"'"+
		"\nserviceName ='"+serviceName+"'"+
		"\ndt_request ='"+dt_request+"'"+
		"\ndt_service ='"+dt_service+"'";
//alert("jsRadioRequest :: "+msg);
	if (dTable=document.getElementById('Ttab'+sub_dept_nr)) {
//alert("jsRadioRequest :: dTable ='"+dTable+"'");
		dTBody=dTable.getElementsByTagName("tbody")[0];

		sid = $F('sid');
//alert("jsRadioRequest :: 1 dTBody ='"+dTBody+"' \n available='"+available+"'");
		lang = $F('lang');
		pid = $F('pid');
		rid = $F('rid');
//alert("jsRadioRequest :: 2 dTBody ='"+dTBody+"' \n available='"+available+"'");
		rpath = document.getElementById('rpath').value;
//alert("jsRadioRequest :: 3 dTBody ='"+dTBody+"' \n available='"+available+"'");
//alert("jsRadioRequest : before radio_findings_link ");
/*
		radio_findings_link = '<a href=seg-radio-findings.php?sid='+sid+'&lang='+lang+
					'&user_origin=lab&batch_nr='+batchNo+'&pid='+pid+'>'+
					'<img src="../../images/findings.gif" border="0"></a>';
*/
/*
			borrowImg ='onclick="return overlib(OLiframeContent(\''+rpath+'modules/radiology/seg-radio-borrow.php?sid='+sid+'&lang='+lang+'&pid='+pid+'&rid='+rid+'&batchNo='+batchNo+'&available='+available+'\', 700, 500, \'fradio-list\', 1, \'auto\'), ' +
						'WIDTH, 700, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL, DRAGGABLE, CLOSETEXT, \'<img src=../../images/close.gif border=0>\', '+
						'CAPTIONPADDING, 4, CAPTION, \'Borrow Patient`s Record\', MIDX, 0, MIDY, 0, STATUS, \'Borrow Patient`s Record\');">';
*/
			borrowImg ='onclick="return overlib(OLiframeContent(\''+rpath+'modules/radiology/seg-radio-borrow.php?sid='+sid+'&lang='+lang+'&pid='+pid+'&rid='+rid+'&batchNo='+batchNo+'&available='+available+'\', 700, 450, \'fradio-list\', 1, \'auto\'), ' +
						'WIDTH, 700, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL, CLOSETEXT, \'<img src=../../images/close.gif onClick=refreshWindow(); border=0>\', '+
						'CAPTIONPADDING, 4, CAPTION, \'Borrow Patient`s Record\', MIDX, 0, MIDY, 0, STATUS, \'Borrow Patient`s Record\');">';

		if (available==1){
//alert("jsRadioRequest :  if (available) is TRUE 1 : available='"+available+"'");
//			status='Available';
			status='<a href="javascript:void(0);" '+borrowImg+'<img src="../../images/available.gif" border="0"></a>';
//			checkBox='<input id="'+batchNo+'" name="chk['+batchNo+']" type="checkbox" onclick="$(\'selectedcount\').innerHTML=countSelected(\'Ttab'+sub_dept_nr+'\')">';
		}else{
//alert("jsRadioRequest :  if (available) is FALSE 1 : available='"+available+"'");
//			borrowImg ='onclick="return overlib(OLiframeContent(\''+rpath+'modules/radiology/seg-radio-borrow.php?sid='+sid+'&lang='+lang+'&pid='+pid+'&rid='+rid+'&batchNo='+batchNo+'\', 600, 500, \'fradio-list\', 1, \'auto\'), ' +
//						'WIDTH, 600, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL, DRAGGABLE, CLOSETEXT, \'<img src=../../images/close.gif border=0>\', '+
//						'CAPTIONPADDING, 4, CAPTION, \'Radiology :: Borrow Patient`s Record\', MIDX, 0, MIDY, 0, STATUS, \'Radiology :: Borrow Patient`s Record\');">';
//alert("jsRadioRequest :  borrowImg ='"+borrowImg+"'");
			status='<a href="javascript:void(0);" '+borrowImg+'<img src="../../images/borrowed.gif" border="0"></a>';
//			checkBox='&nbsp;';
		}
//alert("jsRadioRequest :  status ='"+status+"'");
		if(batchNo){
//alert("jsRadioRequest :  if(batchNo) is TRUE 1 : rowSrc="+rowSrc);
//					'<td>'+checkBox+'</td>'+

			rowSrc = '<tr>'+
					'<td>'+No+'</td>'+
					'<td>'+batchNo+'</td>'+
					'<td>'+serviceCode+'</td>'+
					'<td>'+serviceName+'</td>'+
					'<td>'+r_doc+'</td>'+
					'<td>'+dt_request+'</td>'+
					'<td>'+dt_service+'</td>'+
					'<td>'+borrower+'</td>'+
					'<td>'+date_borrowed+'</td>'+
					'<td>'+date_returned+'</td>'+
					'<td align="center">'+status+'</td>'+
					'</tr>';
//alert("jsRadioRequest :  if(batchNo) is TRUE 2 : rowSrc="+rowSrc);
		}else{
//			rowSrc = '<tr><td colspan="8" style="">No such record exists...</td></tr>';
			rowSrc = '<tr><td colspan="12" align="center" bgcolor="#FFFFFF" style="color:#FF0000; font-family:"Arial", Courier, mono; font-style:Bold; font-weight:bold; font-size:12px;">No such record exists...</td></tr>';
//alert("jsRadioRequest :  if(batchNo) is FALSE : rowSrc="+rowSrc);
		}
		dTBody.innerHTML += rowSrc;
//alert("jsRadioRequest : dTBody.innerHTML : \n"+dTBody.innerHTML);
	}
} 

function jsSortHandler(items,oitem,dir,sub_dept_nr){
	var tab = dojo.widget.byId('rlistContainer').selectedChild;
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
//alert("jsSortHandler : 3 tab ='"+ tab +"'");
	pid = document.getElementById('pid').value;

//	alert("jsSortHandler : you selected  tab ='"+ tab +"'\n key="+key+ "\n pgx ="+pgx+ "\n rpath="+rpath+"\n mode="+mode+ "\n sub_dept_nr="+ sub_dept_nr+"\n oitem="+oitem+"\n odir="+odir);
//return;
//ColHeaderRadioRequest($tbId, $tbody, $searchkey,$sub_dept_nr,$pgx, $thisfile, $rpath,$mode)
//	xajax_ColHeaderRadioRequest('T'+tab, 'TBody'+tab, key, sub_dept_nr, pgx, thisfile, rpath, mode,oitem,odir);
   xajax_populateRadioPatientRecords('T'+tab, pid, key, sub_dept_nr, pgx, thisfile, rpath, mode, oitem, odir);
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
 	var tab;
	var key, pgx, thisfile, rpath, sub_dept_nr, mode, pid;		

	try{
		tab = dojo.widget.byId('rlistContainer').selectedChild;
	}catch(e){
		//alert("e.message = "+e.message);
		tab = 'tab0';   // use in initial loading
	}

 	mode = document.getElementById('smode').value;
 	rpath = document.getElementById('rpath').value;
	setPgx(0);   // resets to the first page every time a tab is clicked
 	pgx = document.getElementById('pgx').value;
	key = document.getElementById('skey').value;
	pid = document.getElementById('pid').value;
 	thisfile = document.getElementById('thisfile').value; 
 	oitem = 'batch_nr'; 
	odir = 'ASC'; 
	sub_dept_nr = tab.substr(3);

//alert("handleOnclick: tab="+ tab + "\n pid="+pid+ "\n key="+key+ "\n pgx ="+pgx+ "\n rpath="+rpath+"\n mode="+mode+ "\n sub_dept_nr="+ sub_dept_nr+"\n oitem="+oitem+"\n odir="+odir);

//	xajax_ColHeaderRadioRequest('T'+tab, 'TBody'+tab, key, sub_dept_nr, pgx, thisfile, rpath, mode,oitem,odir);
   xajax_populateRadioPatientRecords('T'+tab, pid, key, sub_dept_nr, pgx, thisfile, rpath, mode, oitem, odir);
 }

function evtOnClick(){
	dojo.event.connect(dojo.widget.byId('rlistContainer').tablist, "onSelectChild", "handleOnclick");
}
/*
function eventOnClick(){
//	dojo.event.connect(dojo.widget.byId('demo').tablist, "onSelectChild","handleOnclick");
	dojo.event.connect(dojo.widget.byId('tbContainer').tablist, "onButtonClick","handleOnclick");

}
*/

