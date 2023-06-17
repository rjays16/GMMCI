function chkDecimal(obj,id){
	var objValue = obj.value;
	//alert("chkDecimal : \nobj ='"+obj+"' \nobjValue='"+objValue+"' \nid='"+id+"'");

	if (objValue=="")
		return false;
	if ( (isNaN(parseInt(objValue))) || (parseInt(objValue)<0) ){
		alert("Invalid charge for "+$F('code'+id));
		obj.value="0.00";
		obj.focus();
		return false;
	}
	var nf = new NumberFormat();
	nf.setPlaces(2);
	nf.setNumber(objValue);
//		obj.value = parseFloat(objValue);
	obj.value = nf.toFormatted();
	return true;
}// end of function chkDecimal

function genChkDecimal(obj, n){
	var objValue = obj.value;

	if (objValue=="")
		return false;
		
	if (isNaN(objValue)) {
		alert("Invalid amount!");
		obj.value="0.00";
		obj.focus();
		return false;
	}
		
//	if ( (isNaN(parseInt(objValue))) || (parseInt(objValue)<0) ){
//		alert("Invalid amount!");
//		obj.value="0.00";
//		obj.focus();
//		return false;
//	}

	n = n || 4;

	var nf = new NumberFormat();
	nf.setPlaces(n);
	nf.setNumber(objValue);

	obj.value = nf.toFormatted();
	return true;
}// end of function genChkDecimal

function chkDecimalnPlaces(obj,id,n){
	var objValue = obj.value;
	//alert("chkDecimal : \nobj ='"+obj+"' \nobjValue='"+objValue+"' \nid='"+id+"'");

	if (objValue=="")
		return false;
	if ( (isNaN(parseInt(objValue))) || (parseInt(objValue)<0) ){
		alert("Invalid amount/number for "+$F('code'+id));
		obj.value=formatNumber(0,n);
		obj.focus();
		return false;
	}
	var nf = new NumberFormat();
	nf.setPlaces(n);
	nf.setNumber(objValue);
//		obj.value = parseFloat(objValue);
	obj.value = nf.toFormatted();
	return true;
}// end of function chkDecimalnPlaces

function formatNumber(num,dec) {
	var nf = new NumberFormat(num);
	if (isNaN(dec)) dec = nf.NO_ROUNDING;
	nf.setPlaces(dec);
	return nf.toFormatted();
}

/*	
		This will trim the string i.e. no whitespaces in the
		beginning and end of a string AND only a single
		whitespace appears in between tokens/words 
		input: object
		output: object (string) value is trimmed
*/
function trimString(objct){
	objct.value = objct.value.replace(/^\s+|\s+$/g,"");
	objct.value = objct.value.replace(/\s+/g," "); 
}/* end of function trimString */

function jsClearList(tagId){
	$(tagId).innerHTML = '';
}

function js_AddOptions(tagId, text, value, bselected){
	var elTarget = $(tagId);
	
	if (!bselected) bselected = false;
	
	if(elTarget){
		var opt = new Option(text, value, bselected, bselected);
		//var opt = new Option(value, value);
		opt.id = value;
		elTarget.appendChild(opt);
	}
//	var optionsList = elTarget.getElementsByTagName('OPTION');
}//end of function js_AddOption

function js_ClearOptions(tagId){
	var optionsList, el=$(tagId);
	if(el){
		optionsList = el.getElementsByTagName('OPTION');
		for(var i=optionsList.length-1; i >=0 ; i--){
			optionsList[i].parentNode.removeChild(optionsList[i]);	
		}
	}
}//end of function js_ClearOptions