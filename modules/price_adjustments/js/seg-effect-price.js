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

function startAJAXSearch(id, page)
{
	var searchEL = $(id);
	var keyword;

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

	var servType = $("inputarea").value;

	if (searchEL) {
		searchEL.style.color = "#0000ff";
		if (AJAXTimerID) clearTimeout(AJAXTimerID);
		$("PriceList-body").style.display = "";
		//$("ajax-loading").style.display = "";
		//alert("page="+page);
		switch(servType)
		{
			case "1": AJAXTimerID = setTimeout("xajax_populateLabServiceList('"+id+"','"+keyword+"',"+page+")",50); break;
			case "2": AJAXTimerID = setTimeout("xajax_populateRadioServiceList('"+id+"','"+keyword+"',"+page+")",50); break;
			case "3": AJAXTimerID = setTimeout("xajax_populatePharmaServiceList('"+id+"','"+keyword+"',"+page+")",50); break;
			case "4": AJAXTimerID = setTimeout("xajax_populateMiscServiceList('"+id+"','"+keyword+"',"+page+")",50); break;
			case "5": AJAXTimerID = setTimeout("xajax_populateOtherServiceList('"+id+"','"+keyword+"',"+page+")",50); break;
		}

		lastSearch = searchEL.value;
	}
}

function startAJAXList(inputID, page) {
		var serv_area = document.getElementById("inputarea").value;
		var listServ = $(inputID);
		//added by cha, may 15,2010
		if(serv_area!='0')
		{	$("service_name").readOnly = false;
			$("search_serv").disabled = false;
		}else{
			$("service_name").readOnly = true;
			$("search_serv").disabled = true;
		}
		//end cha

		/*if (page)
				document.getElementById('pagekey').value = page;
		else
				document.getElementById('pagekey').value = '0';

		if (listServ) {
				if (AJAXTimerID) clearTimeout(AJAXTimerID);
				$("PriceList-body").style.display = "";
				AJAXTimerID = setTimeout("xajax_populateHospitalService('"+serv_area+"',"+page+")",50);
				lastServ = listServ.value;
		}*/
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
						//newval1 = xajax_numFormat(priceCash,'priceCash'+code);
					//newval2 = xajax_numFormat(priceCash,'priceCash'+code);
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

function setPagination(pageno, lastpage, pagen, total, mode) {
		currentPage=parseInt(pageno);
		lastPage=parseInt(lastpage);
		firstRec=(parseInt(pageno)*pagen)+1;
		totalRows=total;
		if(mode=="edit_price")
		{
			show="pageShow1";
		}else if(mode=="view_history"){
			show="pageShow2";
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

				$("pageFirst").className = (currentPage>0 && lastPage>0 && total>10) ? "segSimulatedLink" : "segDisabledLink";
				$("pagePrev").className = (currentPage>0 && lastPage>0 && total>10) ? "segSimulatedLink" : "segDisabledLink";
				$("pageNext").className = (currentPage<lastPage && total>10) ? "segSimulatedLink" : "segDisabledLink";
				$("pageLast").className = (currentPage<lastPage && total>10) ? "segSimulatedLink" : "segDisabledLink";
		}
		else
		{
				 $(show).innerHTML = '<span>Showing 0 out of 0 record(s).</span>';
		}
}

function jumpToPage(el, jumpType, set) {
		if (el.className=="segDisabledLink") return false;
		if (lastPage==0) return false;
		switch(jumpType) {
				case FIRST_PAGE:
						if (currentPage==0) return false;
						//startAJAXList('inputarea',0);
						startAJAXSearch('service_name',0);
						document.getElementById('pagekey').value=0;
				break;
				case PREV_PAGE:
						if (currentPage==0) return false;
						//startAJAXList('inputarea',parseInt(currentPage)-1);
						startAJAXSearch('service_name', parseInt(currentPage)-1);
						document.getElementById('pagekey').value=currentPage-1;
				break;
				case NEXT_PAGE:
						if (currentPage >= lastPage) return false;
						//startAJAXList('inputarea',parseInt(currentPage)+1);
						startAJAXSearch('service_name', parseInt(currentPage)+1);
						document.getElementById('pagekey').value=parseInt(currentPage)+1;
				break;
				case LAST_PAGE:
						if (currentPage >= lastPage) return false;
						//startAJAXList('inputarea',parseInt(lastPage));
						startAJAXSearch('service_name', parseInt(lastPage));
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
			var date = $("effectiveDate").value;
			var servType = $("inputarea").value;
			var refno_array = new Array();

			var date_sel = $("effectiveDate").value;
			var currentTime = new Date();
			var month = currentTime.getMonth() + 1;
			var day = currentTime.getDate();
			var year = currentTime.getFullYear();
			var datenow=''+year+'-'+month+'-'+day;

			if(servType==0)
			{
				alert("No area selected.");
			}
			else if(!date)
			{
				alert("No date selected.");
			}
			else if(date_sel>=datenow)
			 {
						 alert("Effective date must be current date or later.");
						 //return false;
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
					refno_array[cnt]=getRefNo();
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
						 //alert("code="+modifiedCode[modLen]+" cash="+modifiedCash[modLen]+" charge="+modifiedCharge[modLen]);
						 //refno_array[modLen]=getRefNo();
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
					//var modifiedList = {"serviceCode":modifiedCode, "priceCash":modifiedCash, "priceCharge":modifiedCharge, "RefNo":refno_array};
					var modifiedList = {"serviceCode":modifiedCode, "priceCash":modifiedCash, "priceCharge":modifiedCharge};
					cnt=0;
					while(cnt<modLen)
					{
						alert("code="+modifiedList['serviceCode'][cnt]+" cash="+modifiedList['priceCash'][cnt]+" charge="+modifiedList['priceCharge'][cnt]);
						cnt++;
					}

					//xajax_savePriceAdjustments(date,servType,modifiedList,modLen);
				}
			}
}

function OutputResponse(bool)
{
		if(bool) alert("changes saved!");
		else alert("not saved!");
}

function getRefNo()
{
		var d = new Date();
		var year = d.getFullYear();
		var rand=Math.floor(Math.random()*11000000);
		var string=""+year+""+rand;
		return string;

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
		document.getElementById('effectiveDate').value="";
		document.getElementById('selDate').value="";
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
function printAjax(id,name,code,pcash,pcharge,date,refno,status,refsource)
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
								'<img class="segSimulatedLink" src="../../images/cashier_edit.gif" onclick="editPriceAdjustment(\''+refno+'\'); return false;" onmouseover="tooltip(\''+text1+'\');" onMouseout="return nd();"/>&nbsp;'+
								'<img class="segSimulatedLink" src="../../images/cashier_delete.gif" onclick="deletePriceAdjustment(\''+refno+'\'); return false;" onmouseover="tooltip(\''+text2+'\');" onMouseout="return nd();"/> '+
																'</td>'+
																'<td align="left" width="1%"></td>'+
																'<td align="left" width="1%"></td>'+
											'</tr>';
						 }

				}
				else {
						rowSrc = '<tr><td colspan="10" style="">No service price(s) changed on this date...</td></tr>';
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
										var date = document.getElementById("selDate").value;
										xajax_populatePriceHistory(date,0);
}

function tooltip(text)
{
				return overlib('<span style="font:bold 11px Tahoma">'+text+'</span>',
						TEXTPADDING,4, BORDER,0,
						VAUTO, WRAP);
}

function deletePriceAdjustment(delID)
{
		var reply = confirm("Are you sure you want to this price adjustment?");
		if(reply)
		{
			 xajax_deletePriceAdjustment(delID);
		}
}

function editPriceAdjustment(editID)
{
		 return overlib(
					OLiframeContent('seg_effectivity_editadjustment_tray.php?target=edit&editID='+editID, 450, 200, 'fOrderTray', 0, 'no'),
																	WIDTH,440, TEXTPADDING,0, BORDER,0,
																		STICKY, SCROLL, CLOSECLICK, MODAL,
																	CLOSETEXT, '<img src="../../images/close_red.gif" border=0>',
																 CAPTIONPADDING,4, CAPTION,'Edit Price Adjustments',
																 MIDX,0, MIDY,0,
																 STATUS,'Edit Price Adjustments');
}

function startAJAXEditPrice(refno)
{
	var pcash = $('new_price_cash').value;
	var pcharge = $('new_price_charge').value;
	var effect_date = $('new_effectivity_date').innerHTML;
	var history = $('history').value;
	//alert('pcash: '+pcash+' pcharge: '+pcharge+' effect_date: '+effect_date);
	xajax_updatePriceAdjustment(refno,pcash,pcharge,effect_date,history);
	//window.parent.$('pcash'+refno).innerHTML = pcash;
	//window.parent.$('pcharge'+refno).innerHTML = pcharge;
}

function checkDate(id)
{
	var date_sel = $(id).value;
	var currentTime = new Date();
	var month = currentTime.getMonth() + 1;
	var day = currentTime.getDate();
	var year = currentTime.getFullYear();
	var datenow=''+year+'-'+month+'-'+day;

	if(ValidDate(date_sel))
	{
				if(date_sel>=datenow)
				{
				 alert("Effective date must be current date or later.");
				 }
	}

}

function ValidDate(datefield)
{
		//if field is empty display - warning and return false
		//declare valid variable with all valid characters: digits from 0 to 9 and backslash
				 var valid = "0123456789-";
		//declare variable for counting number of slashes
				 var slashcount = 0;
		//checking date length. If it not equals 10 - display warning and return false
				 if (datefield.length!=10)
				 {
					alert("Invalid date! The correct date format is 'YYYY-mm-dd'.")
					//return false;
				 }
		//check each character in the date field, one at a time
				 for (var i=0; i < datefield.length; i++)
				 {
					 temp = "" + datefield.substring(i, i+1);
					 //alert('temp='+temp);
					 //if character is backslash- count it
					 if (temp == "-")
					 slashcount++;
			//if character in temp does not exist in valid character string, display warning
					 if (valid.indexOf(temp) == "-1")
					 {
						alert("Invalid characters in your date.")
						//return false;
					 }
			//if number of slashes not equals 2 display warning
				 }
				 if (slashcount != 2)
				 {
					alert("Invalid Date! The correct date format is 'YYYY-mm-dd'.")
					//return false;
				 }
		//check position of slashes in date string. It should be 2 and 5
		// Because the first character position is 0 not one
				 if((datefield.charAt(4)!= '-')||( datefield.charAt(7) != '-'))
				 {
					alert("Invalid date! The correct date format is 'YYYY-mm-dd'.")
					//return false
				 }
	 // return true
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
	var date = $("effectiveDate").value;
	var servType = $("inputarea").value;

	var date_sel = $("effectiveDate").value;
	var currentTime = new Date();
	var month = currentTime.getMonth() + 1;
	var day = currentTime.getDate();
	var year = currentTime.getFullYear();
	var datenow=''+year+'-'+month+'-'+day;

	if(servType==0)
	{
		alert("No area selected.");
		return false;
	}
	else if(!date)
	{
		alert("No date selected.");
		return false;
	}
	else if(date_sel>=datenow)
	{
			 alert("Effective date must be current date or later.");
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
			xajax_savePriceAdjustments(date,servType,modifiedList,len);
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