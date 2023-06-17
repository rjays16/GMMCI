var ViewMode = false;

function display(str) {
	document.write(str);
}

var totalDiscount = 0;

function parseFloatEx(x) {

   if (x) {
	var str = x.toString().replace(/\,|\s/,'')
	return parseFloat(str)
   }
   //Added by Omick, December 15, 2008
   //This function should return something if the condition wasn't met
   else {
     return x;
   }
}

function warnClear() {
	var items = document.getElementsByName('items[]');
	if (items.length == 0) return true;
	else return confirm('Performing this action will clear the order tray. Do you wish to continue?');
}

function formatNumber(num,dec) {
	var nf = new NumberFormat(num);
	if (isNaN(dec)) dec = nf.NO_ROUNDING;
	nf.setPlaces(dec);
	return nf.toFormatted();
}

function resetRefNo(newRefNo,error) {
	$("refno").style.color = error ? "#ff0000" : "";
	$("refno").value=newRefNo;
}

function clearEncounter() {
	var iscash = $("iscash1").checked;
	$('ordername').value="";
	$('ordername').readOnly=true;
	$('orderaddress').value="";
	$('orderaddress').readOnly=true;
	$('is_tpl').disabled = !iscash;
	$('pid').value="";
	$('encounter_nr').value="";
	$('clear-enc').disabled = true;
	$('clear-enc').disabled = true;
	$('sw-class').innerHTML = 'None';
	$('encounter_type_show').innerHTML = 'WALK-IN';
	$('encounter_type').value = '';
	//clearCharityDiscounts();
}

function pSearchClose() {
	var nr = $('encounter_nr').value;
	/*
	if (nr) {
		$('btndiscount').disabled = true;
		cClick();
		//xajax_get_charity_discounts(nr);
	}
	*/
	cClick();
}
	
function emptyTraySupplies() {

	clearOrderSupplies($('supplies-list'));
	appendOrderSupplies($('supplies-list'),null);
	refreshDiscountSupplies();
}

function reclassRows(list,startIndex) {
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			var dRows = dBody.getElementsByTagName("tr");
			if (dRows) {
				for (i=startIndex;i<dRows.length;i++) {
					dRows[i].className = "wardlistrow"+(i%2+1);
				}
			}
		}
	}
}

/*
function clearCharityDiscounts() {
	var cNodes = document.getElementsByName("charity[]");
	if (cNodes) {
		for (var i=cNodes.length-1;i>=0;i--) {
			cNodes[i].parentNode.removeChild(cNodes[i]);
		}
	}
}

function addCharityDiscount(discountid, discount) {
	var dsc = document.createElement("INPUT");
	dsc.setAttribute("type","text");
	dsc.setAttribute("id","ch"+discountid);	
	dsc.setAttribute("name","charity[]");	
	dsc.setAttribute("discount",discount);
	dsc.setAttribute("value",discountid);
	$("orderForm").appendChild(dsc);
}
*/

function clearOrderSupplies(list) {	
	if (!list) list = $('supplies-list')
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0]
		if (dBody) {
			trayItems = 0
			dBody.innerHTML = ""
			return true
		}
	}
	return false
}

function appendOrderSupplies(list, details, disabled) {
      alert('here');  
	if (!list) list = $('supplies-list');
	if (list) {
		var dBody=list.select("tbody")[0];
		if (dBody) {
			var discount = parseFloatEx($("discount").value);
            //var discount = '';
			//var isCash = $("iscash1").checked;
            
            var isCash = ($('iscash1') == null) ? parseInt($('transaction_type').value) : $('iscash1').checked;
            
            
			var isSC = parseInt($("issc").value);  // Senior Citizen checking
            
			var totalCash, totalCharge;
			var src;
			var lastRowNum = null,
					items = document.getElementsByName('items[]');
					dRows = dBody.getElementsByTagName("tr");
			if (details) {
				var id = details.id,
          dosage=details.dosage,
					qty = parseFloatEx(details.qty),					
					prcCash = parseFloatEx(details.prcCash),
					prcCharge = parseFloatEx(details.prcCharge),
					prcCashSC = parseFloatEx(details.prcCashSC),
					prcChargeSC = parseFloatEx(details.prcChargeSC),
					totalCash, totalCharge;
				netPrice = isCash ? prcCash : prcCharge;
				orig = netPrice;
				if (isSC)	{
					seniorPrice = parseFloatEx(isCash ? details.prcCashSC : prcChargeSC);
					if (seniorPrice > 0)
						netPrice = seniorPrice
				}

				// Check if item is socialized and discount is of effect
				if (parseInt(details.isSocialized)==1 && isCash) {
					if (discount==1.0)
						netPrice=0;
					else {
						if (parseFloatEx(details.prcDiscounted) < netPrice) {
							netPrice = parseFloatEx(details.prcDiscounted)
							if (netPrice == 0) netPrice = orig;
						}
					}
				}
				if (details.forcePrice) netPrice = details.forcePrice;
				tot = netPrice*qty;
				orig = isNaN(orig) ? '<span style="margin-right:5px">-</span>' : formatNumber(orig,2);
				if (items) {
					if ($('row'+id)) {
						var itemRow = $('row'+id),
								itemQty = $('rowQty'+id)
						itemQty.value = parseFloatEx(itemQty.value) + parseFloatEx(details.qty)
						itemQty.setAttribute('prevValue',itemQty.value)
                        
                        /** Added by omick for some sponge thingy **/
                          if ($('sponge_qty'+id)) {
                            var sponge_qty = $('sponge_qty'+id);
                            sponge_qty.value = itemQty.value;
                          }
                        /** End **/
						qty = parseFloatEx(itemQty.value)
						tot = netPrice*qty
						$('rowPrcCashSC'+id).value		= prcCashSC
						$('rowPrcChargeSC'+id).value	= prcChargeSC
						$('rowPrcCash'+id).value			= details.prcCash
						$('rowPrcCharge'+id).value		= details.prcCharge
						$('rowPrc'+id).setAttribute("prevValue",orig)
						//$('qty'+id).innerHTML 				= isNaN(qty) ? '<span style="margin-right:5px">-</span>' : 'x'+formatNumber(qty,null)						
						$('rowPrc'+id).value 					= isNaN(netPrice) ? '<span style="margin-right:5px">-</span>' : formatNumber(netPrice,2)
						$('tot'+id).innerHTML 				= isNaN(tot) ? '<span style="margin-right:5px">-</span>' : formatNumber(tot,2)
						return true
					}
					if (!items.length) clearOrderSupplies(list)
				}

				alt = (dRows.length%2>0) ? ' class="alt"':''
				qty = isNaN(qty) ? '<span style="margin-right:5px">-</span>;' : 'x'+formatNumber(qty,null)
				prc = isNaN(netPrice) ? '<span style="margin-right:5px">-</span>' : formatNumber(netPrice,2)
				tot = isNaN(tot) ? '<span style="margin-right:5px">-</span>' : formatNumber(tot,2)			
				
				var disabledAttrib = disabled ? 'disabled="disabled"' : ""
				
				src = '<tr'+alt+' id="row'+id+'">';				
				if (disabled)
					src+='<td style="height:30px" class="centerAlign"><img class="disabled" src="../../../images/close_small.gif" border="0" style="opacity:0.4"/></td>'
				else
					src+='<td style="height:30px" class="centerAlign"><img class="segSimulatedLink" src="../../../images/close_small.gif" border="0" onclick="removeItemSupplies(\''+id+'\')"/></td>'
				

				src+=
					'<td>'+id+
            '<input type="hidden" name="soc[]" id="rowSoc'+id+'" value="'+details.isSocialized+'" />'+
            '<input type="hidden" name="pdisc[]" id="rowPrcDiscounted'+id+'" value="'+details.prcDiscounted+'" />'+
            '<input type="hidden" name="pcashsc[]" id="rowPrcCashSC'+id+'" value="'+prcCashSC+'" />'+
            '<input type="hidden" name="pchargesc[]" id="rowPrcChargeSC'+id+'" value="'+prcChargeSC+'" />'+
            '<input type="hidden" name="pcash[]" id="rowPrcCash'+id+'" value="'+details.prcCash+'" />'+
            '<input type="hidden" name="pcharge[]" id="rowPrcCharge'+id+'" value="'+details.prcCharge+'" />'+
            '<input type="hidden" name="items[]" id="rowID'+id+'" value="'+id+'" />'+
          '</td>'+
					'<td><span style="color:#660000">'+details.name+'</span></td>'+
					'<td class="centerAlign"><input type="checkbox" name="consigned[]" value="'+id+'" '+(parseInt(details.isConsigned)==1 ? 'checked="checked"' : '')+' '+(disabled ? 'disabled="disabled"' : '')+'></td>'+
					'<td class="centerAlign" id="qty'+id+'">'+
						'<input type="text" class="segInput" name="qty[]" id="rowQty'+id+'" classificationID="'+details.classification+'" itemID="'+id+'" value="'+details.qty+'" prevValue="'+details.qty+'" style="width:80%;text-align:right"'+(disabled ? ' disabled="disabled"' : '')+' onfocus="this.value=this.getAttribute(\'prevValue\')" onblur="adjustQty(this)"/>'+
					'</td>'+
					'<td class="rightAlign" id="prc'+id+'">'+orig+'</td>'+
					'<td class="rightAlign">'
				
				if	(disabled || (parseFloatEx(details.prcDiscounted)>0 && (!isSC || (isSC && parseFloatEx(seniorPrice)>0))))
					src+= '<input type="text" class="segClearInput" name="prc[]" id="rowPrc'+id+'" value="'+prc+'" style="width:95%;text-align:right" itemID="'+id+'" prevValue="'+netPrice+'" readonly="readonly"/>'
				else
					src+= '<input type="text" class="segInput" name="prc[]" id="rowPrc'+id+'" value="'+prc+'" style="width:95%;text-align:right" itemID="'+id+'" prevValue="'+netPrice+'" onfocus="this.value=this.getAttribute(\'prevValue\')" onblur="adjustPrice(this)"/>'

				src+=	'</td>'+
					'<td class="rightAlign" id="tot'+id+'">'+tot+'</td>'+
          '<td class="centerAlign"><input id="dosage'+id+'" name="dosage[]" type="text" class="segInput" style="width:95%" '+(disabled ? 'disabled="disabled"' : '')+'/></td>'+
				'</tr>';
				trayItems++;
                
                if ($('is_in_or_main_post')) {
                  if (details.classification=='Sponge') {
                    add_to_sponge(details);
                  }
               }
			}
			else {
				src = "<tr><td colspan=\"9\">Order list is currently empty...</td></tr>";	
			}
			
      dBody.insert(src);
      
      if (dosage) $('dosage'+id).value=dosage;
     
        
          
			return true;
		}
	}
	return false;
}

function removeItemSupplies(id) {
	var destTable, destRows;
	var table = $('supplies-list');
	var rmvRow=$('row'+id);
	if (table && rmvRow) {
		var rndx = rmvRow.rowIndex-1;    
		rmvRow.remove();    
		if (!document.getElementsByName("items[]") || document.getElementsByName("items[]").length <= 0)
			appendOrderSupplies(table, null);
		reclassRows(table,rndx);
	}
	refreshTotalSupplies();
    remove_sponge(id); //added by Omick, March 03, 2009
}

function seniorCitizen() {
	//var iscash = $("iscash1").checked
    var iscash = ($('iscash1') == null) ? parseInt($('transaction_type').value) : $('iscash1').checked;
	var issc = parseInt($("issc").value);
	var discount = parseFloatEx($("discount").value)
	var pdisc = document.getElementsByName('pdisc[]')
	var soc = document.getElementsByName('soc[]')
	var items = document.getElementsByName('items[]')
	var cash = document.getElementsByName('pcash[]')
	var charge = document.getElementsByName('pcharge[]')
	var cashsc = document.getElementsByName('pcashsc[]')
	var chargesc = document.getElementsByName('pchargesc[]')
	var prc = document.getElementsByName('prc[]')
	//var isCash = $("iscash1").checked
    var isCash = ($('iscash1') == null) ? parseInt($('transaction_type').value) : $('iscash1').checked;
	var newPrice, discountPrice, seniorPrice, cashPrice, chargePrice,
			cashSc, chargeSc

	for (var i=0;i<items.length;i++) {
		priceCash = parseFloatEx(cash[i].value)
		priceCharge = parseFloatEx(charge[i].value)
		newPrice = iscash ?  priceCash : priceCharge
		discountPrice = newPrice
		if (parseInt(soc[i].value)==1 && iscash) {
			if (discount==1.0)	newPrice = 0
			else {
				discountPrice = parseFloatEx(pdisc[i].value)
				if (discountPrice > 0) newPrice = discountPrice
			}
		}
		
		seniorPrice = 1.0
		if (issc) {
			cashSc = parseFloatEx(cashsc[i].value)
			chargeSc = parseFloatEx(chargesc[i].value)
			seniorPrice = Math.min(newPrice, iscash ? cashSc : chargeSc)
			if (seniorPrice > 0) newPrice = seniorPrice
		}
		
		// disabled flag
		disabledFlag = false
		//alert('issc:'+issc+'\ndsc:'+discountPrice+'\nsprc:'+seniorPrice)
		if (disabledFlag || (discountPrice >0 && (!issc || (issc && seniorPrice>0)))) {
			prc[i].className = "segClearInput"
			prc[i].value = formatNumber(newPrice,2)
			prc[i].readOnly = true
			prc[i].setAttribute("prevValue", newPrice)
			prc[i].setAttribute("onfocus", "")
			prc[i].setAttribute("onblur", "")
		}
		else {
			prc[i].className = "segInput"
			prc[i].readOnly = false
			prc[i].value = formatNumber(newPrice,2)
			prc[i].setAttribute("prevValue", newPrice)
			prc[i].setAttribute("onfocus", "this.value=this.getAttribute(\'prevValue\')")
			prc[i].setAttribute("onblur", "adjustPrice(this)")
		}
	}
	refreshDiscountSupplies()
}

function changeTransactionType() {
	//clearEncounter();
	refreshDiscountSupplies();
}

function adjustPrice(obj) {
	var id = obj.getAttribute("itemID");
	if (isNaN(obj.value)) {
		obj.value = formatNumber(obj.getAttribute("prevValue"),2);
		return false;
	}
	if (parseFloatEx(obj.value) != parseFloatEx(obj.getAttribute("prevValue"))) {
		$('tot'+id).innerHTML = formatNumber(obj.value*parseFloatEx($('rowQty'+id).value),2);
		refreshDiscountSupplies();
	}
	obj.setAttribute("prevValue",parseFloatEx(obj.value));
	obj.value = formatNumber(obj.value,2);
	return true;
}

function adjustQty(obj) {
	var id = obj.getAttribute("itemID");
    var classification = obj.getAttribute('classificationID');
    
	if (isNaN(obj.value)) {
		obj.value = obj.getAttribute("prevValue");
		return false;
	}
	if (parseFloatEx(obj.value) != parseFloatEx(obj.getAttribute("prevValue"))) {
		$('tot'+id).innerHTML = formatNumber(parseFloatEx($('rowPrc'+id).value)*parseFloatEx($('rowQty'+id).value),2);
		refreshDiscountSupplies();
	}
	obj.setAttribute("prevValue",parseFloatEx(obj.value));
    if (classification=='Sponge') {
      adjust_sponge_quantity(id, parseFloatEx(obj.value));
    }
	//obj.value = formatNumber(obj.value,2);
	return true;
}

function refreshDiscountSupplies() {

	var nodes;
	var nr = $('encounter_nr').value;
	if (nr)
		nodes = document.getElementsByName("charity[]");
	else
		nodes=document.getElementsByName("discount[]");
	totalDiscount = 0;
	if (nodes) {
		for (var i=0;i<nodes.length;i++) {
			if (nodes[i].value) totalDiscount += parseFloatEx(nodes[i].getAttribute('discount'));
		}
	}
	var dItem = $("show-discount");
	if (dItem) {
		dItem.value = parseFloatEx(totalDiscount * 100).toFixed(2);
	}
	refreshTotalSupplies();
}

function refreshTotalSupplies() {
       
	var items = document.getElementsByName('items[]');
	var cash = document.getElementsByName('pcash[]');
	var charge = document.getElementsByName('pcharge[]');
	var qty = document.getElementsByName('qty[]');
	var prc = document.getElementsByName('prc[]');
	//var isCash = $("iscash1").checked;
    var isCash = ($('iscash1') == null) ? parseInt($('transaction_type').value) : $('iscash1').checked;
	var total = 0.0, orig = 0.0;
	var id
	for (var i=0;i<items.length;i++) {
		id = items[i].value
		orig+=parseFloatEx(isCash ? cash[i].value : charge[i].value)*parseFloatEx(qty[i].value);
		val = parseFloatEx(prc[i].value)*parseFloatEx(qty[i].value)
		total+=val;
		$('tot'+id).innerHTML = formatNumber(val,2)
	}

	var subTotal = $("show-sub-total");
	var discountTotal = $("show-discount-total");
	var netTotal = $("show-net-total");
	
	subTotal.innerHTML = formatNumber(orig.toFixed(2),2);
	disc = total-orig;
	if (disc <= 0) {
		discountTotal.style.color = "#006600";
		discountTotal.innerHTML = "("+formatNumber(Math.abs(disc),2)+")";
	}
	else {
		discountTotal.style.color = "red";
		discountTotal.innerHTML = formatNumber(Math.abs(disc),2);
	}
	netTotal.innerHTML = formatNumber(total.toFixed(2),2);
}