var currentPage=0, lastPage=0;
var FIRST_PAGE=1, PREV_PAGE=2, NEXT_PAGE=3, LAST_PAGE=4, SET_PAGE=0;

function display(str) {
	if($('ajax_display')) $('ajax_display').innerHTML = str.replace('\n','<br>');
}

function formatNumber(num,dec) {
	var nf = new NumberFormat(num);
	if (isNaN(dec)) dec = nf.NO_ROUNDING;
	nf.setPlaces(dec);
	return nf.toFormatted();
}

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

function setPagination(pageno, lastpage, pagen, total) {
	currentPage=parseInt(pageno);
	lastPage=parseInt(lastpage);	
	firstRec = (parseInt(pageno)*pagen)+1;
	if (currentPage==lastPage)
		lastRec = total;
	else
		lastRec = (parseInt(pageno)+1)*pagen;
	if (parseInt(total))
		$("pageShow").innerHTML = '<span>Showing '+(formatNumber(firstRec))+'-'+(formatNumber(lastRec))+' out of '+(formatNumber(parseInt(total)))+' record(s)</span>'
	else
		$("pageShow").innerHTML = ''
	$("pageFirst").className = (currentPage>0 && lastPage>0) ? "segSimulatedLink" : "segDisabledLink";
	$("pagePrev").className = (currentPage>0 && lastPage>0) ? "segSimulatedLink" : "segDisabledLink";
	$("pageNext").className = (currentPage<lastPage) ? "segSimulatedLink" : "segDisabledLink";
	$("pageLast").className = (currentPage<lastPage) ? "segSimulatedLink" : "segDisabledLink";
}

function jumpToPage(el, jumpType, set) {
	if (el.className=="segDisabledLink") return false;
	if (lastPage==0) return false;
	switch(jumpType) {
		case FIRST_PAGE:
			if (currentPage==0) return false;
			startAJAXSearch('search',0);
		break;
		case PREV_PAGE:
			if (currentPage==0) return false;
			startAJAXSearch('search',currentPage-1);
		break;
		case NEXT_PAGE:
			if (currentPage >= lastPage) return false;
			startAJAXSearch('search',parseInt(currentPage)+1);
		break;
		case LAST_PAGE:
			if (currentPage >= lastPage) return false;
			startAJAXSearch('search',lastPage);
		break;
	}
}

function prepareAdd(id) {
	var details = new Object();
	
	var cash = parseFloatEx($('cash'+id).value),
			charge = parseFloatEx($('charge'+id).value),
			qty=0;
	
	if ( isNaN(cash) || (cash < 0) || isNaN(charge) || (charge < 0) ) {
		alert("Price not set. Cannot add the product to the order yet...")
		return false
	}
	
	details.id = $('id'+id).value
  details.stock=$('stock'+id).value
	details.name = $('name'+id).innerHTML
	details.desc = $('desc'+id).innerHTML
	details.prcCash = parseFloatEx($('cash'+id).value)
	details.prcCharge= parseFloatEx($('charge'+id).value)
	details.prcCashSC= parseFloatEx($('cashsc'+id).value)
	details.prcChargeSC= parseFloatEx($('chargesc'+id).value)
	details.isSocialized= $('soc'+id).value
	details.prcDiscounted= parseFloatEx($('d'+id).value)
    details.classification = $('classification'+id).value
	if ($('noqty'+id).value != '1') {
    /*
		while (qty) {
		}
    */
		while (isNaN(parseFloat(qty)) || parseFloat(qty)<=0) {
			qty = prompt("Enter quantity:")
			if (qty === null) return false;
      if (parseFloat(qty)>parseFloat(details.stock)) {
        alert('Quantity entered exceeds quantity in stock...');
        return false;
      }
		}
	}	
	details.qty = qty;

	var list = window.parent.document.getElementById('supplies-list')
                           
	result = window.parent.appendOrderSupplies(list,details)
	if (result) 
		alert('Item added to order list...');
	else
		alert('Failed to add item...');
	if (window.parent && window.parent.refreshDiscountSupplies) window.parent.refreshDiscountSupplies();
}

function clearList(listID) {
	// Search for the source row table element
	var list=$(listID),dRows, dBody;
	if (list) {
		dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			dBody.innerHTML = "";
			return true;	// success
		}
		else return false;	// fail
	}
	else return false;	// fail
}

function addProductToList(listID, details ) {

	// ,id, name, desc, cash, charge, cashsc, chargesc, d, soc
	var list=$(listID), dRows, dBody, rowSrc;
	var i;
	if (list) {
		dBody=list.getElementsByTagName("tbody")[0];
		dRows=dBody.getElementsByTagName("tr");

		// get the last row id and extract the current row no.
        
			
		if (typeof(details)=="object") {
			var id = details.id,
				name = details.name,
				desc = details.desc,
				cash = details.cash,
				charge = details.charge,
				cashsc = details.cashsc,
				chargesc = details.chargesc,
        restricted = details.restricted,
        expiry = details.expiry,
				d = details.d,
				soc = details.soc,
				noqty = details.noqty,
                classification = details.classification;
				
			var cashHTML, chargeHTML;
			var cashSeniorHTML, chargeSeniorHTML;
      var noSelect=false;
			var stock=0;
      
      // parse expiration dates      
      if (expiry.length) {
        expDates = expiry.split('\n');        
        expiryHTML='<select class="segInput" id="expiry'+id+'" onchange="change">';
        for (i=0;i<expDates.length;i++) {          
          exp = expDates[i].split(':');
          stock+=parseInt(exp[1]);
        }
        expiryHTML+='</select>';
      }
      else {
        expiryHTML = "-";
        qtyHTML = '<input type id="expiry'+id+'" value="0">'+'<span>0</span>';
        noSelect = true;
      }
      
      
			//if (d>=0)			
			rowSrc = "<tr"+(restricted=="1"?' class="red"':"")+">"+
									'<td>'+
                    (restricted=="1" ? 
                      ('<div style="margin-top:7px; padding:2px;float:left"><img title="Restricted item" src="../../gui/img/common/default/warn2.gif" align="absmiddle" border="0"></div>'):'')+
                    '<div style="float:left; padding:2px">'+
										  '<span id="name'+id+'" style="font:bold 12px Arial;color:'+(restricted=='1'?'#c00000':'#000066')+'">'+name+'</span><br />'+
										  '<div id="desc'+id+'" style="font:normal 11px Arial; color:'+(restricted=='1'?'#c00000':'#404040')+'">'+desc+'</div>'+
                      '<input id="soc'+id+'" type="hidden" value="'+soc+'"/>'+
                      '<input id="id'+id+'" type="hidden" value="'+id+'"/>'+
                    '</div>'+
									'</td>'+
									'<td align="right" '+(cash<=0 ? '' : '')+'>'+
										'<input id="noqty'+id+'" type="hidden" value="'+(noqty ? '1' : '0')+'"/>'+
										'<input id="d'+id+'" type="hidden" value="'+d+'"/>'+
                                        '<input id="classification'+id+'" type="hidden" value="'+classification+'"/>'+
										'<input id="cash'+id+'" type="hidden" value="'+cash+'"/>'+
											(d!=cash ? '<span style="color:#008000">' : '')+
											(d>0 ? 
											 	formatNumber(d,2) : formatNumber(cash,2))+
											(d!=cash ? '</span>' : '')+
									'</td>'+
									'<td align="right">'+
										'<input id="charge'+id+'" type="hidden" value="'+charge+'"/>'+(charge>0 ? formatNumber(charge,2) : '-')+'</td>'+
									'<td align="right">'+
										'<input id="cashsc'+id+'" type="hidden" value="'+cashsc+'"/>'+(cashsc>0 ? formatNumber(cashsc,2) : '-')+'</td>'+
									'<td align="right">'+
										'<input id="chargesc'+id+'" type="hidden" value="'+chargesc+'"/>'+(chargesc>0 ? formatNumber(chargesc,2) : '-')+
									'</td>'+
                  '<td align="center">'+
                    '<input id="stock'+id+'" type="hidden" value="'+stock+'"/>'+
                    '<span style="color:#008000">'+stock+'</span>'+
                  '</td>'+
                  /*
                  '<td align="center">'+
                    '<input class="segInput" id="qty'+id+'" type="text" style="text-align:right;width:30px" value="" '+(noqty ? 'disabled="disabled"' : '')+' style="text-align:right" onblur="this.value = isNaN(parseFloatEx(this.value))?\'\':parseFloatEx(this.value)"/>'+
                  '</td>'+
                   */
/*									'<td align="center">'+
										'<input class="jedInput" id="qty'+id+'" type="text" style="text-align:right;width:30px" value="" '+(noqty ? 'disabled="disabled"' : '')+' style="text-align:right" onblur="this.value = isNaN(parseFloatEx(this.value))?\'\':parseFloatEx(this.value)"/>'+
									'</td>'+ */
									'<td align="left">'+
										'<input id="select'+id+'" class="segButton" type="button" value=">" style="color:#000066"'+
                      ((parseInt(stock)<=0)?' disabled="disabled"':'')+
											' onclick="prepareAdd(\''+id+'\')" '+
										'/>'+
									'</td>'+
								'</tr>';
		}
		else {
			rowSrc = '<tr><td colspan="8" style="">No such product exists...</td></tr>';
		}
		dBody.innerHTML += rowSrc;
	}
}