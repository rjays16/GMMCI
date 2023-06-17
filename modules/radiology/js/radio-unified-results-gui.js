
function jsRadioNoFoundRequest(sub_dept_nr){
		var dTable,dTBody,rowSrc;

		if (dTable=document.getElementById('Ttab'+sub_dept_nr)) {
				dTBody=dTable.getElementsByTagName("tbody")[0];
				rowSrc = '<tr><td colspan="6" align="center" bgcolor="#FFFFFF" style="color:#FF0000; font-family:"Arial", Courier, mono; font-style:Bold; font-weight:bold; font-size:12px;">NO MATCHING PENDING REQUEST FOUND</td></tr>';
				dTBody.innerHTML += rowSrc;
//alert("jsRadioNoFoundRequest : dTBody.innerHTML : \n"+dTBody.innerHTML);
		}
}

function jsRadioRequest(sub_dept_nr,No,batchNo,refno,dateRq,sub_dept_name,pid,rid,sex,lName,fName,bDate,rStatus,rPriority,pType,service_code){
		var dTable,dTBody,dRows,rowSrc,sid,lang,radio_details_link;
		var patType;

		if (dTable=document.getElementById('Ttab'+sub_dept_nr)) {

				dTBody=dTable.getElementsByTagName("tbody")[0];

				sid = $F('sid');
				lang = $F('lang');
				radio_details_link = '<a href="javascript:void(0);" onclick="UnifiedBatch(\''+sid+'\',\''+lang+'\',\''+rid+'\',\''+pid+'\');"><img src="../../images/findings.gif" border="0"></a>';

				if (pType==1)
						patType = "ERPx";
				else if (pType==2)
						patType = "OPDPx";
				else if ((pType==3)||(pType==4))
						patType = "INPx";
				else
						patType = "Walkin";

				if(rid){
//alert("jsRadioRequest :  if(batchNo) is true 1 : rowSrc="+rowSrc);
						rowSrc = '<tr>'+
										'<td align="center">'+rid+'</td>'+
										'<td>'+lName+'</td>'+
										'<td>'+fName+'</td>'+
										'<td>'+patType+'</td>'+
										'<td align="center">'+sub_dept_name+'</td>'+
										'<td align="center">'+radio_details_link+'</td>'+
										'</tr>';
				}else{
						rowSrc = '<tr><td colspan="6" style="">No such record exists...</td></tr>';
				}
				dTBody.innerHTML += rowSrc;
//alert("jsRadioRequest : dTBody.innerHTML : \n"+dTBody.innerHTML);
		}
}

function UnifiedBatch(sid, lang, rid, pid){
		return overlib(
				OLiframeContent('seg-radio-unified-batch.php?sid='+sid+'&lang='+lang+
										'&user_origin=radio&rid='+rid+'&pid='+pid+'&target=radio_unified_results',
																	820, 380, 'fDiagnosis', 1, 'auto'),
																	WIDTH,380, TEXTPADDING,0, BORDER,0,
																		STICKY, SCROLL, CLOSECLICK, MODAL,
																		CLOSETEXT, '<img src=../../images/close.gif border=0 onClick="">',
																 CAPTIONPADDING,4, CAPTION,'List of Batch Requests',
																 MIDX,0, MIDY,0,
																 STATUS,'List of Batch Requests');
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

//alert("jsSortHandler : you selected  "+ tab + "\n key="+key+ "\n pgx ="+pgx+ "\n rpath="+rpath+"\n mode="+mode+ "\n sub_dept_nr="+ sub_dept_nr+"\n oitem="+oitem+"\n odir="+odir);
		//ColHeaderRadioRequest($tbId, $tbody, $searchkey,$sub_dept_nr,$pgx, $thisfile, $rpath,$mode)

		xajax_PopulateRadioUndoneRequest('T'+tab, key, sub_dept_nr, pgx, thisfile, rpath, mode,oitem,odir);
		//xajax_ColHeaderRadioRequest('T'+tab, 'TBody'+tab, key, sub_dept_nr, pgx, thisfile, rpath, mode,oitem,odir);
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
//    objct.value = objct.value.replace(/^\s+|\s+$/g,"");
//    objct.value = objct.value.replace(/\s+/g," ");
		$('searchkey').value = $('searchkey').value.replace(/^\s+|\s+$/g,"");
		$('searchkey').value = $('searchkey').value.replace(/\s+/g," ");
}/* end of function trimString */

function chkSearch(d){
				$('skey').value=$F('searchkey');
				handleOnclick();
}

function handleOnclick(){
//     var tab = dojo.widget.byId('tbContainer').selectedChild;
		 var tab;
		var key, pgx, thisfile, rpath, sub_dept_nr, mode;

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
//    alert("handleOnclick: tab="+ tab + "\n key="+key+ "\n pgx ="+pgx+ "\n rpath="+rpath+"\n mode="+mode+ "\n sub_dept_nr="+ sub_dept_nr+"\n oitem="+oitem+"\n odir="+odir);
//alert('here ='+key);
	if (key=="")
		key = 'null';
		xajax_PopulateRadioUndoneRequest('T'+tab, key, sub_dept_nr, pgx, thisfile, rpath, mode,oitem,odir);
//    xajax_ColHeaderRadioRequest('T'+tab, 'TBody'+tab, key, sub_dept_nr, pgx, thisfile, rpath, mode,oitem,odir);
 }

function eventOnClick(){
				//alert('wait');
//    dojo.event.connect(dojo.widget.byId('demo').tablist, "onSelectChild","handleOnclick");
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

		//if(characterCode == 13){ //if generated character code is equal to ascii 13 (if enter key)
		//    $('skey').value=$('search-refno').value; chkSearch();
		//}else{
				return true;
		//}
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
