var currentPage=0, lastPage=0;
var FIRST_PAGE=1, PREV_PAGE=2, NEXT_PAGE=3, LAST_PAGE=4, SET_PAGE=0;
var AJAXTimerID=0;
var totalRows=0;
var servCode = new Array();
var modifiedCode = new Array();
var modifiedCash = new Array();
var modifiedCharge = new Array();
var array_len=0;  //number of items currently displayed
var modLen=0;     //number of items that have changes

//edited by jasper 12/05/12
function startAJAXSearch(id, page)
{
    //alert(id);
    var searchEL = $(id);
	var keyword;
    var servType;
    var areaCode;

	if ($('is_edit').value==0) {    //added by jasper
		searchEL = $('searchkey');
        servType = $("inputarea2").value;
        areaCode =  $('area_code2').value;
    }
    else {
        servType = $("inputarea").value;
    }

	if (searchEL.value)
		document.getElementById('key').value = searchEL.value;
	else
		document.getElementById('key').value = '*';

	if (page)
		document.getElementById('pagekey').value = page;
	else
		document.getElementById('pagekey').value = '0';

	keyword = searchEL.value;
	keyword = keyword.replace("'","^");

	//var servType = $("inputarea").value;

	if (searchEL) {
        //edited by jasper 12/05/12
        if ($('is_edit').value==1) {
		    searchEL.style.color = "#0000ff";
		    if (AJAXTimerID) clearTimeout(AJAXTimerID);
		    $("PriceList-body").style.display = "";
		    switch(servType)
		    {
			    case "1": AJAXTimerID = setTimeout("xajax_populateLabServiceList('"+id+"','"+keyword+"',"+page+")",50); break;
			    case "2": AJAXTimerID = setTimeout("xajax_populateRadioServiceList('"+id+"','"+keyword+"',"+page+")",50); break;
			    case "3": AJAXTimerID = setTimeout("xajax_populatePharmaServiceList('"+id+"','"+keyword+"',"+page+")",50); break;
			    case "4": AJAXTimerID = setTimeout("xajax_populateMiscServiceList('"+id+"','"+keyword+"',"+page+")",50); break;
			    case "5": AJAXTimerID = setTimeout("xajax_populateOtherServiceList('"+id+"','"+keyword+"',"+page+")",50); break;
		    }
        }
        else { //added by jasper
          //alert(servType + " " + $('area_code2').value + " " + page + " " + keyword);
          xajax_populatePriceListHistory(areaCode, servType, page, keyword);
        }
		lastSearch = searchEL.value;
	}
}

function startAJAXList(inputID, page) {
		var serv_area = document.getElementById("inputarea").value;
		var listServ = $(inputID);
		if(serv_area!='0')
		{	$("service_name").readOnly = false;
			$("search_serv").disabled = false;
		}else{
			$("service_name").readOnly = true;
			$("search_serv").disabled = true;
		}
}

function clearList(listID) {
		// Search for the source row table element
		var list=$(listID),dRows, dBody;
		if (list) {
				dBody=list.getElementsByTagName("tbody")[0];
				if (dBody) {
						dBody.innerHTML = "";
						return true;    // success
				}
				else return false;    // fail
		}
		else return false;    // fail
}

function endAJAXList(listID) {
		var listEL = $(listID);
		if (listEL) {
				//$("ajax-loading").style.display = "none";
				$("PriceList-body").style.display = "";
				searchEL.style.color = "";
		}
}

function viewPriceList(listID, code, name, priceCash, priceCharge)
{
		var list=$(listID), dRows, dBody, rowSrc, divSrc;
		var i;
		var classified, mode, editlink;
		//var newval1,newval2;
		//alert("parsed: "+parseInt(priceCash)+" "+parseInt(priceCharge));
		if (list) {
				dBody=list.getElementsByTagName("tbody")[0];
				dRows=dBody.getElementsByTagName("tr");
				if (code) {
						alt = (dRows.length%2)+1;
						rowSrc = '<tr class="wardlistrow'+alt+'" id="row'+addslashes(code)+'" value="'+code+'">'+
														'<td>&nbsp;</td>'+
														'<td width="20%" align="left"><span style="font:bold 11px Arial;color:#660000">'+name+'</span></td>'+
														'<td width="15%" align="left" id="'+code+'">'+code+'</td>'+
														'<td width="15%" align="center">'+'<input type="text" size="10" id="priceCash'+code+'" value='+priceCash+' onblur="callAlert(this.value,this.id); format_number(this.value,this.id,2); save_partly(\''+code+'\');"></input><input type="hidden" id="priceCash_orig'+code+'" value='+priceCash+'></input></td>'+
														'<td width="15%" align="center">'+'<input type="text" size="10" id="priceCharge'+code+'" value='+priceCharge+' onblur="callAlert(this.value,this.id); format_number(this.value,this.id,2); save_partly(\''+code+'\');"></input><input type="hidden" id="priceCharge_orig'+code+'" value='+priceCharge+'></input></td>'+
														'<input type="hidden" name="service_codes" id="service_code'+code+'" value="'+code+'"/>'+
											'</tr>';
						divSrc = '<input type="hidden" id="service_code'+code+'" name="service_code" value="'+code+'"/>';
				}
				else {
						rowSrc = '<tr><td colspan="6" style="">No service area selected...</td></tr>';
				}
				dBody.innerHTML += rowSrc;
				$('service_codes').innerHTML += divSrc;
		}
}
//modified by jasper 12/05/2012
function setPagination(pageno, lastpage, pagen, total, mode) {
		currentPage=parseInt(pageno);
		lastPage=parseInt(lastpage);
		firstRec=(parseInt(pageno)*pagen)+1;
		totalRows=total;
        var pF = "pageFirst";
        var pP = "pagePrev";
        var pN = "pageNext";
        var pL = "pageLast";

		if(mode=="edit_price")
		{
			show="pageShow1";
		}else if(mode=="view_history"){
			show="pageShow2";
            pF = pF + '2';
            pP = pP + '2';
            pN = pN + '2';
            pL = pL + '2';
        }

		if (currentPage==lastPage)
				lastRec = total;
		else
				lastRec = (parseInt(pageno)+1)*pagen;

		if (parseInt(total)==0)
		{
				$(show).innerHTML = '<span>Showing '+(lastRec)+'-'+(lastRec)+' out of '+(parseInt(total))+' record(s).</span>';
		}
		else if(parseInt(total)>0)
		{
				$(show).innerHTML = '<span>Showing '+(firstRec)+'-'+(lastRec)+' out of '+(parseInt(total))+' record(s).</span>';
                //alert('CP:' + currentPage + ' LP:' + lastPage + ' T:' + total);
                //$("pageFirst2").className = (currentPage>0 && lastPage>0 && total>10) ? "segSimulatedLink" : "segDisabledLink";
				//$("pagePrev2").className = (currentPage>0 && lastPage>0 && total>10) ? "segSimulatedLink" : "segDisabledLink";
				//$("pageNext2").className = (currentPage<lastPage && total>10) ? "segSimulatedLink" : "segDisabledLink";
				//$("pageLast2").className = (currentPage<lastPage && total>10) ? "segSimulatedLink" : "segDisabledLink";
                $(pF).className = (currentPage>0 && lastPage>0 && total>10) ? "segSimulatedLink" : "segDisabledLink";
                $(pP).className = (currentPage>0 && lastPage>0 && total>10) ? "segSimulatedLink" : "segDisabledLink";
                $(pN).className = (currentPage<lastPage && total>10) ? "segSimulatedLink" : "segDisabledLink";
                $(pL).className = (currentPage<lastPage && total>10) ? "segSimulatedLink" : "segDisabledLink";
		}
		else
		{
				 $(show).innerHTML = '<span>Showing 0 out of 0 record(s).</span>';
		}
}

//edited by jasper 12/05/12
function jumpToPage(el, jumpType, set) {
    //alert("jasper");
    var searchID;
	if (el.className=="segDisabledLink") return false;

	if (lastPage==0) return false;

    if ($('is_edit').value==1) {   //added by jasper
       searchID = 'service_name';
    }
    else {
       searchID = 'searchkey';
    }
    //alert(searchID);
    switch(jumpType) {
		case FIRST_PAGE:
			if (currentPage==0) return false;
	        		//startAJAXList('inputarea',0);
			        //startAJAXSearch('service_name',0);
                    startAJAXSearch(searchID,0);
					document.getElementById('pagekey').value=0;
                    break;
		case PREV_PAGE:
			if (currentPage==0) return false;
					//startAJAXList('inputarea',parseInt(currentPage)-1);
					//startAJAXSearch('service_name', parseInt(currentPage)-1);
                    startAJAXSearch(searchID, parseInt(currentPage)-1);
		            document.getElementById('pagekey').value=currentPage-1;
     				break;
		case NEXT_PAGE:
	    	if (currentPage >= lastPage) return false;
					//startAJAXList('inputarea',parseInt(currentPage)+1);
					//startAJAXSearch('service_name', parseInt(currentPage)+1);
                    startAJAXSearch(searchID, parseInt(currentPage)+1);
					document.getElementById('pagekey').value=parseInt(currentPage)+1;
		            break;
		case LAST_PAGE:
			if (currentPage >= lastPage) return false;
					//startAJAXList('inputarea',parseInt(lastPage));
					//startAJAXSearch('service_name', parseInt(lastPage));
                    startAJAXSearch(searchID, parseInt(lastPage));
		            document.getElementById('pagekey').value=parseInt(lastPage);
				    break;
		}
}

function addslashes(str) {
		str=str.replace("'","\\'");
		return str;
}


function startAJAXSave(id, page,modCode,modCash,modCharge,max)
{
			var area = $("area_code").value;
			var servType = $("inputarea").value;
			//var refno_array = new Array();

			if(area==0)
			{
				alert("No area selected.");
			}
			else if(servType==0)
			{
				alert("No department/section selected.");
			}
			else
			{

				//add codes here to populate modifiedList array
				//to get all the prices that have changes
				var cnt=0,index=0;
				var temprowid="";
				while(cnt<max)
				{
					modifiedCode[cnt]=modCode[cnt];
					modifiedCash[cnt]=modCash[cnt];
					modifiedCharge[cnt]=modCharge[cnt];
					//refno_array[cnt]=getRefNo();
					cnt++;
				}
				cnt=0;
				while(cnt<array_len)
				{
					//temprowid=document.getElementById(servCode[cnt]).innerHTML;
					temprowid=$('service_code'+servCode[cnt]).value;
					var pcashid="priceCash"+servCode[cnt];
					var pcashorigid="priceCash_orig"+servCode[cnt];
					var pchargeid="priceCharge"+servCode[cnt];
					var pchargeorigid="priceCharge_orig"+servCode[cnt];

					temp_pricecash=parseFloat(document.getElementById(pcashid).value);
					orig_pricecash=parseFloat(document.getElementById(pcashorigid).value);
					temp_pricecharge=parseFloat(document.getElementById(pchargeid).value);
					orig_pricecharge=parseFloat(document.getElementById(pchargeorigid).value);

					if((temprowid==servCode[cnt]) && ((temp_pricecash!=orig_pricecash) || (temp_pricecharge!=orig_pricecharge)))
					{
						 modifiedCode[modLen]=temprowid;
						 modifiedCash[modLen]=document.getElementById(pcashid).value;
						 modifiedCharge[modLen]=document.getElementById(pchargeid).value;
						 modLen++;
					}
					cnt++;
				}

				if(modLen==0)
				{
					alert("No changes to price list.");
				}
				else
				{

					var modifiedList = {"serviceCode":modifiedCode, "priceCash":modifiedCash, "priceCharge":modifiedCharge};
					cnt=0;
					while(cnt<modLen)
					{
						alert("code="+modifiedList['serviceCode'][cnt]+" cash="+modifiedList['priceCash'][cnt]+" charge="+modifiedList['priceCharge'][cnt]);
						cnt++;
					}

				}
			}
}

function OutputResponse(bool)
{
		if(bool) alert("changes saved!");
		else alert("not saved!");
}

function saveServiceCode(serviceCodeArray)
{
	//alert(servCode)
		var cnt=0;
		var result=false;
		while(cnt<array_len)
		{
				if(servCode[cnt]==serviceCodeArray)
				{
						result=true;
				}
				//alert("codejs="+servCode[cnt]);
				cnt++;
		}
		if(result==false)
		{
				servCode[array_len]=serviceCodeArray;
				array_len++;
		}
}

function initialize(modCode,modCash,modCharge,max)
{
		//dapat pag active ng function na ito
		//macheck na nya agad kung may changes then save un lahat
		//sa temporary variable muna
		try
		{
				var cnt=0;
				while(cnt<max)
				{
						modifiedCode[cnt]=modCode[cnt];
						modifiedCash[cnt]=modCash[cnt];
						modifiedCharge[cnt]=modCharge[cnt];
						cnt++;
				}
				cnt=0;
				while(cnt<array_len)
				{
						//temprowid=document.getElementById(servCode[cnt]).innerHTML;
						temprowid=$('service_code'+servCode[cnt]).value;
						var pcashid="priceCash"+servCode[cnt];
						var pcashorigid="priceCash_orig"+servCode[cnt];
						var pchargeid="priceCharge"+servCode[cnt];
						var pchargeorigid="priceCharge_orig"+servCode[cnt];

						temp_pricecash = parseFloat(document.getElementById(pcashid).value);
						orig_pricecash = parseFloat(document.getElementById(pcashorigid).value);
						temp_pricecharge = parseFloat(document.getElementById(pchargeid).value);
						orig_pricecharge = parseFloat(document.getElementById(pchargeorigid).value);

						if((temprowid==servCode[cnt]) && ((temp_pricecash!=orig_pricecash) || (temp_pricecharge!=orig_pricecharge)))
						{
							 modifiedCode[modLen]=temprowid;
							 modifiedCash[modLen]=document.getElementById(pcashid).value;
							 modifiedCharge[modLen]=document.getElementById(pchargeid).value;
							 //alert("1 code="+modifiedCode[modLen]+" cash="+modifiedCash[modLen]+" charge="+modifiedCharge[modLen]);
							 modLen++;
						}
						cnt++;
				}

				cnt=0;
				//alert(modLen);
				//modLen=modLen+max;
				while(cnt<modLen)
				{
						//alert("code="+modifiedCode[cnt]+" cash="+modifiedCash[cnt]+" charge="+modifiedCharge[cnt]);
						cnt++;
				}
				array_len=0;
		}
		catch(err)
		{
				initializeTempArray();
		}
}

function initializeTempArray()
{
		servCode = new Array();
		modifiedCode = new Array();
		modifiedCash = new Array();
		modifiedCharge = new Array();
		array_len=0;
		modLen=0;
}

function errorTrap(id)
{
	 if(id=='save')
	 {
			if(document.getElementById('effectiveDate').value=='')
			{
					alert('Please provide the effectivity date.');
					return true;
			}
			else return false;
	 }
}

function clearHeader(searchID)
{
		document.getElementById('optionList').value='0';
		document.getElementById('optionList').innerHTML="-Select an area-";

}

function callAlert(servValue,origCode)
{
	var x=isInteger(servValue);
	var y=parseFloat(servValue);
	//alert('x='+x+' y='+y+' servValue='+servValue);
	var input=document.getElementById(origCode);
	//input.innerHTML = format_number(servValue,2);
		if((!x && !y) || !(IsNumeric(servValue)))
		{

			//var input=document.getElementById(origCode);
			input.style.color='#ff0000';
			alert('Invalid price number format.');
		}
		else
		{
			//var input=document.getElementById(origCode);
			input.style.color='#000000';
		}

		//alert("or value="+orValue+"orID="+orID);
}

function IsNumeric(sText)
{
	 var ValidChars = "0123456789.";
	 var IsNumber=true;
	 var Char;
	 for (i = 0; i < sText.length && IsNumber == true; i++)
			{
			Char = sText.charAt(i);
			if (ValidChars.indexOf(Char) == -1)
				 {
				 IsNumber = false;
				 }
			}
	 return IsNumber;

}


function format_number(pnumber,id,decimals){
	if (isNaN(pnumber)) { return 0};
	if (pnumber=='') { return 0};

	var snum = new String(pnumber);
	var sec = snum.split('.');
	var whole = parseFloat(sec[0]);
	var result = '';

	if(sec.length > 1){
		var dec = new String(sec[1]);
		dec = String(parseFloat(sec[1])/Math.pow(10,(dec.length - decimals)));
		dec = String(whole + Math.round(parseFloat(dec))/Math.pow(10,decimals));
		var dot = dec.indexOf('.');
		if(dot == -1){
			dec += '.';
			dot = dec.indexOf('.');
		}
		while(dec.length <= dot + decimals) { dec += '0'; }
		result = dec;
	} else{
		var dot;
		var dec = new String(whole);
		dec += '.';
		dot = dec.indexOf('.');
		while(dec.length <= dot + decimals) { dec += '0'; }
		result = dec;
	}
	//alert(result);
	var input=document.getElementById(id);
	input.value = result;
	//alert(input.value);
	return result;
}

function isInteger (s)
{
	var i;

	if (isEmpty(s))
	if (isInteger.arguments.length == 1) return 0;
	else return (isInteger.arguments[1] == true);

	for (i = 0; i < s.length; i++)
	{
		var c = s.charAt(i);

		if (!isDigit(c)) return false;
	}

	return true;
}

function isEmpty(s)
{
	return ((s == null) || (s.length == 0))
}

function isDigit (c)
{
	return ((c >= "0") && (c <= "9"))
}

//added by CHA 09-21-09
function printAjax(id,name,area,code,pcash,pcharge,date,status,refsource)
{
  //alert("print: "+name+" "+code+" "+pcash+" "+pcharge+" "+date);
		var list=$(id), dRows, dBody, rowSrc;
		var i;
		var classified, mode, editlink;
		if (list) {
				dBody=list.getElementsByTagName("tbody")[0];
				dRows=dBody.getElementsByTagName("tr");
				if (code) {
						alt = (dRows.length%2)+1;
						text1="Edit Price Adjustment";
						text2="Delete Price Adjustment";
						 //alert(status);
						var source="";
						var refno = code+''+area;

						switch(refsource)
						{
							case "LB": source = "Laboratory"; break;
							case "RD": source = "Radiology"; break;
							case "PH": source = "Pharmacy"; break;
							case "MS": source = "Miscellaenous"; break;
							case "O": source = "Others"; break;
						}
						 if(status!='')
						 {
							rowSrc = '<tr class="wardlistrow'+alt+'" id="row'+addslashes(code)+'" value="'+code+'">'+
														'<td>&nbsp;</td>'+
														'<td width="20%" align="left"><span style="font:bold 11px Arial;color:#660000">'+name+'</span></td>'+
														'<td width="10%" align="left" id="'+code+'">'+code+'</td>'+
														'<td width="5%" align="right" id="pcash'+refno+'">'+pcash+'</td>'+
														'<td width="5%" align="right" id="pcharge'+refno+'">'+pcharge+'</td>'+
														'<td width="12%" align="center" >'+date+'</td>'+
														'<td width="10%" align="center" >'+source+'</td>'+
														'<td align="center" width="2%" style="white-space:nowrap">'+
														'<img class="segSimulatedLink" src="../../images/cashier_edit.gif" disabled="" onmouseover="tooltip(\''+text1+'\');" onMouseout="return nd();"/>&nbsp;'+
														'<img class="segSimulatedLink" src="../../images/cashier_delete.gif" disabled="" onmouseover="tooltip(\''+text2+'\');" onMouseout="return nd();"/> '+
																'</td>'+
																 '<td align="left" width="1%">Updated</td>'+
																	'<td align="left" width="1%"></td>'+
											'</tr>';
						 }
						 else
						 {
						 rowSrc = '<tr class="wardlistrow'+alt+'" id="row'+addslashes(code)+'" value="'+code+'">'+
														'<td>&nbsp;</td>'+
														'<td width="20%" align="left"><span style="font:bold 11px Arial;color:#660000">'+name+'</span></td>'+
														'<td width="10%" align="left" id="'+code+'">'+code+'</td>'+
														'<td width="5%" align="right" id="pcash'+refno+'">'+pcash+'</td>'+
														'<td width="5%" align="right" id="pcharge'+refno+'">'+pcharge+'</td>'+
														'<td width="12%" align="center">'+date+'</td>'+
														'<td width="10%" align="center">'+source+'</td>'+
														'<td align="center" width="2%" style="white-space:nowrap">'+
														'<img class="segSimulatedLink" src="../../images/cashier_edit.gif" onclick="editPriceList(\''+code+'\',\''+refsource+'\',\''+area+'\'); return false;" onmouseover="tooltip(\''+text1+'\');" onMouseout="return nd();"/>&nbsp;'+
														'<img class="segSimulatedLink" src="../../images/cashier_delete.gif" onclick="deletePriceList(\''+code+'\',\''+refsource+'\',\''+area+'\'); return false;" onmouseover="tooltip(\''+text2+'\');" onMouseout="return nd();"/> '+
																'</td>'+
																'<td align="left" width="1%"></td>'+
																'<td align="left" width="1%"></td>'+
											'</tr>';
						 }

				}
				else {
						rowSrc = '<tr><td colspan="10" style="">No service price(s) list...</td></tr>';
				}
				dBody.innerHTML += rowSrc;
				//alert(rowSrc);
}
}
//end CHA

function tabClick(el)
{
		if (el.className!='segActiveTab') {
			$('mode').value = el.getAttribute('segSetMode');
			var dList = $(el.parentNode);
			if (dList) {
				var listItems = dList.getElementsByTagName("LI");
				for (var i=0;i<listItems.length;i++) {
					if (listItems[i] != el) {
						listItems[i].className = "";
						if ($(listItems[i].getAttribute('segTab'))) $(listItems[i].getAttribute('segTab')).style.display = "none";
					}
				}
				if ($(el.getAttribute('segTab')))
					$(el.getAttribute('segTab')).style.display = "block";
				el.className = "segActiveTab";
			}
		}
}

function callAjax()
{
        var area;
		var source;
		if ($('is_edit').value==1){
			area = document.getElementById("area_code").value;
			source = document.getElementById("inputarea").value;
		}else{
			area = document.getElementById("area_code2").value;
			source = document.getElementById("inputarea2").value;
		}

		xajax_populatePriceListHistory(area,source,0,"");
}

function tooltip(text)
{
				return overlib('<span style="font:bold 11px Tahoma">'+text+'</span>',
						TEXTPADDING,4, BORDER,0,
						VAUTO, WRAP);
}

function deletePriceList(code,refsource,area)
{
		var reply = confirm("Are you sure you want to this price list?");
		if(reply)
		{
			 xajax_deletePriceList(code,refsource,area);
		}
}

function editPriceList(code,refsource,area)
{
		 return overlib(
					OLiframeContent('seg_pricelist_edit_tray.php?target=edit&code='+code+'&refsource='+refsource+'&area='+area, 440, 220, 'fOrderTray', 0, 'no'),
																	WIDTH,440, TEXTPADDING,0, BORDER,0,
																		STICKY, SCROLL, CLOSECLICK, MODAL,
																	CLOSETEXT, '<img src="../../images/close_red.gif" border=0>',
																 CAPTIONPADDING,4, CAPTION,'Edit Pricelist',
																 MIDX,0, MIDY,0,
																 STATUS,'Edit Pricelist');
}

function startAJAXEditPrice(code,refsource,area)
{
	var pcash = $('new_price_cash').value;
	var pcharge = $('new_price_charge').value;

	//var history = $('history').value;
	//alert('pcash: '+pcash+' pcharge: '+pcharge+' effect_date: '+effect_date);
	xajax_updatePriceList(code,refsource,area,pcash,pcharge);
	//window.parent.$('pcash'+refno).innerHTML = pcash;
	//window.parent.$('pcharge'+refno).innerHTML = pcharge;
}

function save_partly(id)
{
	var cur_code = id;
	var cur_pcash = parseFloat($('priceCash'+id).value);
	var cur_pcashOrig = parseFloat($('priceCash_orig'+id).value);
	var cur_pcharge = parseFloat($('priceCharge'+id).value);
	var cur_pchargeOrig = parseFloat($('priceCharge_orig'+id).value);
	var divSrc="";

	if(cur_pcash!=cur_pcashOrig || cur_pcharge!=cur_pchargeOrig)
	{
		 var saved_el = document.getElementsByName('servCode');
		 var exists = true;
		 if(saved_el.length>0)
		 {
				for(j=0;j<saved_el.length;j++)
				{
					//alert("saved="+saved_el[j].value+" current="+cur_code);
					if(cur_code==saved_el[j].value)
					{
						//alert("change");
						$('servCash'+cur_code).value = cur_pcash;
						$('servCharge'+cur_code).value = cur_pcharge;
						exists = true;
					}else{
						exists = false;
					}
				}
				if(exists==false)
				{
					//alert("add")
					divSrc = "<tr>"+
					"<input type='hidden' id='servCode"+cur_code+"' name='servCode' value='"+cur_code+"'/>"+
					"<input type='hidden' id='servCash"+cur_code+"' name='servCash' value='"+cur_pcash+"'/>"+
					"<input type='hidden' id='servCharge"+cur_code+"' name='servCharge' value='"+cur_pcharge+"'/>"+
					"</tr>";
				}
		 }else
			{
				//alert("new")
				divSrc = "<tr>"+
					"<input type='hidden' id='servCode"+cur_code+"' name='servCode' value='"+cur_code+"'/>"+
					"<input type='hidden' id='servCash"+cur_code+"' name='servCash' value='"+cur_pcash+"'/>"+
					"<input type='hidden' id='servCharge"+cur_code+"' name='servCharge' value='"+cur_pcharge+"'/>"+
					"</tr>";
			}
			$('service_changes').innerHTML+=divSrc;
	}
}

function save_changes()
{
	var servType = $("inputarea").value;
	var area = $("area_code").value;

	if(area==0)
	{
		alert("No area selected.");
		return false;
	}
	else if(servType==0)
	{
		alert("No department/section selected.");
		return false;
	}
	else
	{
		var el_code = document.getElementsByName('servCode');
		var el_cash = document.getElementsByName('servCash');
		var el_charge = document.getElementsByName('servCharge');
		var len = el_code.length;
		if(len>0)
		{
			//alert("final changes")
			var modifiedCode = new Array();
			var modifiedCash = new Array();
			var modifiedCharge = new Array();
			for(i=0;i<len;i++)
			{
				//alert(""+el_code[i].value+" "+el_cash[i].value+" "+el_charge[i].value)
				modifiedCode[i]=el_code[i].value;
				modifiedCash[i]=el_cash[i].value;
				modifiedCharge[i]=el_charge[i].value;
			}
			var modifiedList = {"serviceCode":modifiedCode, "priceCash":modifiedCash, "priceCharge":modifiedCharge};
			/*cnt=0;
			while(cnt<len)
			{
				alert("code="+modifiedList['serviceCode'][cnt]+" cash="+modifiedList['priceCash'][cnt]+" charge="+modifiedList['priceCharge'][cnt]);
				cnt++;
			} */
			xajax_savePriceList(servType,area,modifiedList,len);
		}
		else
		{
			alert("No changes to selected price list.");
			return false;
		}
	}
}

function showOutputResponse(rep)
{
	alert(rep);
	window.location.reload();
}

function showUpdateOutputResponse(rep)
{
	alert(rep);
	window.parent.location.reload();
}

//added by VAN 07-14-2010
function ClearText(){
	var text_name = $('service_name').value='';
}

function assignValue(value){
	$('is_edit').value= value;
}

//-----------