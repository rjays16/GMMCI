function pSearchClose() {
	cClick();  //function in 'overlibmws.js'
}

function jsOPNoFoundRequest(dept_nr){
	var dTable,dTBody,rowSrc;

	if (dTable=document.getElementById('Ttab'+dept_nr)) {
		dTBody=dTable.getElementsByTagName("tbody")[0];
		rowSrc = '<tr><td colspan="10" align="center" bgcolor="#FFFFFF" style="color:#FF0000; font-family:"Arial", Courier, mono; font-style:Bold; font-weight:bold; font-size:12px;">NO MATCHING OR REQUEST FOUND</td></tr>';
		dTBody.innerHTML += rowSrc;
//alert("jsOPNoFoundRequest : dTBody.innerHTML : \n"+dTBody.innerHTML);
	}
}

function jsOPRequest(dept_nr,No,op_request_nr,dateRq,dept_name,pid,sex,lName,fName,bDate){
	var dTable,dTBody,rowSrc,sid,lang,rpath;

	if (dTable=document.getElementById('Ttab'+dept_nr)) {

		dTBody=dTable.getElementsByTagName("tbody")[0];

		sid = $F('sid');
		lang = $F('lang');
		rpath = $F('rpath');
		/*
		detailsImg ='onclick="return overlib(OLiframeContent(\'seg-op-request-new.php?sid='+sid+'&lang='+lang+'&popUp='+1+'&op_request_nr='+op_request_nr+'\', 900, 425, \'fradio-list\', 1, \'auto\'), ' +
					'WIDTH, 900, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL, DRAGGABLE, CLOSETEXT, \'<img src='+rpath+'images/close.gif border=0>\', '+
					'CAPTIONPADDING, 4, CAPTION, \'ICPM Encoding\', MIDX, 0, MIDY, 0, STATUS, \'ICPM Encoding\');">';
		*/
		//edited by VAN 02-07-08
		detailsImg ='onclick="return overlib(OLiframeContent(\'seg-op-request-new.php?sid='+sid+'&lang='+lang+'&popUp='+1+'&op_request_nr='+op_request_nr+'\', 850, 425, \'fradio-list\', 1, \'auto\'), ' +
					'WIDTH, 850, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL, CLOSETEXT, \'<img src='+rpath+'images/close.gif border=0>\', '+
					'CAPTIONPADDING, 4, CAPTION, \'ICPM Encoding\', MIDX, 0, MIDY, 0, STATUS, \'ICPM Encoding\');">';
//alert("jsOPRequest E : No='"+No+"'");
		if(op_request_nr){
//alert("jsOPRequest :  if(op_request_nr) is true 1 : rowSrc="+rowSrc);
			rowSrc = '<tr>'+
					'<td>'+No+'</td>'+
					'<td>'+op_request_nr+'</td>'+
					'<td>'+dateRq+'</td>'+
					'<td>'+dept_name+'</td>'+
					'<td>'+pid+'</td>'+
					'<td>'+sex+'</td>'+
					'<td>'+lName+'</td>'+
					'<td>'+fName+'</td>'+
					'<td>'+bDate+'</td>'+
					'<td align="center"><a href="javascript:void(0);" '+detailsImg+'<img src="'+rpath+'images/edit.gif" border="0"></a></td>'+
					'</tr>';
//alert("jsOPRequest :  if(op_request_nr) is true 2 : rowSrc="+rowSrc);
		}else{
//			rowSrc = '<tr><td colspan="9" style="">No such record exists...</td></tr>';
			rowSrc = '<tr><td colspan="10" align="center" bgcolor="#FFFFFF" style="color:#FF0000; font-family:"Arial", Courier, mono; font-style:Bold; font-weight:bold; font-size:12px;">NO MATCHING OR REQUEST FOUND</td></tr>';
		}
		dTBody.innerHTML += rowSrc;
//alert("jsOPRequest : dTBody.innerHTML : \n"+dTBody.innerHTML);
	}
} 

function jsSortHandler(items,oitem,dir,dept_nr){
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

//	alert("jsSortHandler : you selected  "+ tab + "\n key="+key+ "\n pgx ="+pgx+ "\n rpath="+rpath+"\n mode="+mode+ "\n dept_nr="+ dept_nr+"\n oitem="+oitem+"\n odir="+odir);
	//ColHeaderORRequest($tbId, $tbody, $searchkey,$dept_nr,$pgx, $thisfile, $rpath,$mode)
//	xajax_ColHeaderORRequest('T'+tab, 'TBody'+tab, key, dept_nr, pgx, thisfile, rpath, mode,oitem,odir);
	xajax_PopulateORRequest('T'+tab, key, dept_nr, pgx, thisfile, rpath, mode,oitem,odir);
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
// 	var tab = dojo.widget.byId('tbContainer').selectedChild;
 	var tab;
	var key, pgx, thisfile, rpath, dept_nr, mode;		

 	try{
		tab = dojo.widget.byId('tbContainer').selectedChild;
	}catch(e){
		//alert("e.message = "+e.message);
		tab = 'tab0';   // use in initial loading
	}
//alert("handleOnclick : tab="+tab+"'");
//return;
 	mode = document.getElementById('smode').value;
 	rpath = document.getElementById('rpath').value;
	setPgx(0);   // resets to the first page every time a tab is clicked
 	pgx = document.getElementById('pgx').value;
	key = document.getElementById('skey').value;
 	thisfile = document.getElementById('thisfile').value; 
 	oitem = 'create_time'; 
	odir = 'ASC'; 
	dept_nr = tab.substr(3);
//	alert("handleOnclick: tab="+ tab + "\n key="+key+ "\n pgx ="+pgx+ "\n rpath="+rpath+"\n mode="+mode+ "\n dept_nr="+ dept_nr+"\n oitem="+oitem+"\n odir="+odir);
//alert("key = "+key);
//	xajax_ColHeaderORRequest('T'+tab, 'TBody'+tab, key, dept_nr, pgx, thisfile, rpath, mode,oitem,odir);
	xajax_PopulateORRequest('T'+tab, key, dept_nr, pgx, thisfile, rpath, mode,oitem,odir);
 }

function eventOnClick(){
//	dojo.event.connect(dojo.widget.byId('demo').tablist, "onSelectChild","handleOnclick");
	dojo.event.connect(dojo.widget.byId('tbContainer').tablist, "onButtonClick","handleOnclick");

}


//added by VAN 06-24-08
function checkEnter(e,searchID){
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
		chkSearch();
	}else{
		return true;
	}		
}
//-------------