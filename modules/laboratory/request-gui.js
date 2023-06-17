var totalDiscount = 0;

function formatNumber(num,dec) {
	var nf = new NumberFormat(num);
	if (isNaN(dec)) dec = nf.NO_ROUNDING;
	nf.setPlaces(dec);
	return nf.toFormatted();
}

function calculateSubTotal() {
	
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

function clearOrder(list) {	
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0];
		$('socialServiceNotes').style.display='none';
		if (dBody) {
			trayItems = 0;
			dBody.innerHTML = "";
			return true;
		}
	}
	return false;
}

function appendOrder(list,details) {
	//alert('details 1 = '+details);
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0];
		//alert('dBody = '+dBody.innerHTML);
		//alert('str = '+str);
		//alert(str.match("Request list is currently empty"));
		//alert('details 2 = '+details);
		
		if (dBody) {
			var isCash = $("iscash1").checked;
			var person_discountid = $("discountid").value;
			var totalCash, totalCharge;
			var src, toolTipText, adjust_amount, discount_percentage;
			var nonSocialized, toolTipTextHandler, valprice;
			var lastRowNum = null,
					items = document.getElementsByName('items[]');
					dRows = dBody.getElementsByTagName("tr");
			var nf = new NumberFormat();
			var cashprice, forwarding;
			nf.setPlaces(2);
			//alert('details = '+details);
			
			if (details) {
				var id = details.id,
					prcCash = parseFloat(details.prcCash),
					prcCashC1 = parseFloat(details.price_C1),
					prcCashC2 = parseFloat(details.price_C2),
					prcCashC3 = parseFloat(details.price_C3),
					prcCharge = parseFloat(details.prcCharge),
					totalCash, tot_price;
					
					if (person_discountid=="C1")
						totalCash = prcCashC1;
					else if (person_discountid=="C2")
						totalCash = prcCashC2;	
					else if (person_discountid=="C3")
						totalCash = prcCashC3;		
					else
						totalCash = prcCash;
					
					totalCharge = prcCharge;
				
				var info = details.clinicInfo;
				
				toolTipText = "Requesting doctor: <br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"+details.requestDocName+" <br>"+
								  "Department: <br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"+details.requestDeptName+" <br>"+	
								  "Clinical Impression: <br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"+info.toUpperCase();
				//alert('dBody.innerHTML = '+dBody.innerHTML);
				//added by VAN 03-08-08
				/*
				var bodyinner = dBody.innerHTML;
				if (bodyinner.match("Request list is currently empty")!=null){
					for (var i=0;i<items.length;i++) {
						items[i].value = null;
					}
					items = new Array(0);
					refreshDiscount();
				}
				*/
				if (items) {
					//alert('true = '+items.length);
					for (var i=0;i<items.length;i++) {
						//alert(items[i].value+" = "+details.id)	
						if (items[i].value == details.id) {
							var itemRow = $('row'+items[i].value);//,
							
							nf.setNumber(prcCash);
							nf.setNumber(prcCashC1);
							nf.setNumber(prcCashC2);
							nf.setNumber(prcCashC3);
							
							totalCash = nf.toFormatted();
							nf.setNumber(prcCharge);
							totalCharge = nf.toFormatted();
							
							if (isCash) {
								prc=prcCash;
								tot=totalCash;
							}
							else {
								prc=prcCharge;
								tot=totalCharge;
							}
							
							//itemRow.childNodes[10].innerHTML = tot;
							nf.setPlaces(nf.NO_ROUNDING);
							
							var notallowedchar = new Array("+", "-", "*","/","%");
							var i, code, ssdcnt;
							code = id;
							for(i=0;i<notallowedchar.length;i++){
								code = code.replace(notallowedchar[i], "");
							}
														
							document.getElementById('toolTipText'+id).value = toolTipText;
							
							//document.getElementById('toolTipText'+code).value = toolTipText;
							
							//document.getElementById('rowPrcCash'+id).value = details.prcCash;
							//document.getElementById('rowPrcCharge'+id).value = details.prcCharge;
							document.getElementById('rowDoc'+id).value = details.requestDoc;
							document.getElementById('rowDocName'+id).value = details.requestDocName;
							document.getElementById('rowDept'+id).value = details.requestDept;
							document.getElementById('rowDeptName'+id).value = details.requestDeptName;
							document.getElementById('rowHouse'+id).value = details.is_in_house;
							document.getElementById('rowInfo'+id).value = details.clinicInfo;
							//alert('details.name = '+details.name);
							// van 03-03-08
							document.getElementById('name'+id).innnerHTML = details.name;
							// van 03-03-08
							document.getElementById('prc'+id).innerHTML = formatNumber(prc,2);
							//alert("tot = "+tot);
							//document.getElementById('tot'+id).innerHTML = formatNumber(tot,2);
							if ((details.sservice==1) && ((person_discountid) && (person_discountid!=" ")) && ((person_discountid=="C1")||(person_discountid=="C2")||(person_discountid=="C3"))){
								if (tot==0){
									if (person_discountid=="C1")
										ssdcnt = prcCashC1;
									else if (person_discountid=="C2")	
										ssdcnt = prcCashC2;
									else if (person_discountid=="C3")
										ssdcnt = prcCashC3;
									//valprice = formatNumber(prc,2);
									//tot_price = '<input type="text" id="totprice'+id+'" name="totprice'+id+'" value=0.00 size="5" style="text-align:right" onKeyDown="keyEnter(event, this,\''+id+'\');" onBlur="getSocialPrice(\''+id+'\'); formatNumber(this.value,2); refreshDiscount();">';
									
									if (isNaN(ssdcnt)){
										tot_price = prc - (prc * parseFloat($F('discount')));
										tot_price = formatNumber(tot_price,2);
									}else{
										tot_price = '<input type="text" id="totprice'+id+'" name="totprice'+id+'" value="'+formatNumber(prc,2)+'" size="10" style="text-align:right" onKeyDown="keyEnter(event, this,\''+id+'\');" onBlur="getSocialPrice(\''+id+'\'); formatNumber(this.value,2); refreshDiscount();">';	
									}
									//tot_price = '<input type="text" id="totprice'+id+'" name="totprice'+id+'" value='+formatNumber(prc,2)+' size="5" style="text-align:right" onKeyDown="keyEnter(event, this,\''+id+'\');" onBlur="getSocialPrice(\''+id+'\'); formatNumber(this.value,2); refreshDiscount();">';
								}else{
									//tot_price = formatNumber(tot,2);
									tot_price = tot;
								}
							}else if ((details.sservice==1) && ((person_discountid) && (person_discountid!=" ")) &&  ((person_discountid!="C1")||(person_discountid!="C2")||(person_discountid!="C3"))){
								if (tot!=0.00){
									tot_price = tot - (tot * parseFloat($F('discount')));
								}else{
									tot_price =  prc - ( prc * parseFloat($F('discount')));
								}
								//tot_price = formatNumber(tot_price,2);
								//tot_price = tot_price;
								
							}else{
								//tot_price = formatNumber(prc,2);
								//commented by VAN 10-27-08
                                if (($F('orig_discountid')=='DMC') || ($F('orig_discountid')=='DMCDep'))
                                    tot_price = 0; 
                                else
                                   tot_price = prc;      
                                
							};
							
							if (isNaN(tot_price)){
								//alert('true');
								prcvalue = prc;
								tot_price2 = tot_price;
							}else{
								//prcvalue = tot_price.replace(",","");
								//prcvalue = formatNumber(tot_price,2);
								prcvalue = tot_price;
								tot_price2 = formatNumber(tot_price,2);
							}
							//comment by VAN 11-15-07
							//document.getElementById('rowPrcCash'+id).value = tot_price.replace(",","");	
							//edited by van 03-03-08
							//document.getElementById('tot'+id).innerHTML = tot_price;
							//document.getElementById('tot'+id).innerHTML = formatNumber(tot_price,2);
							document.getElementById('tot'+id).innerHTML = tot_price2;
							
							//document.getElementById('rowPrcCash'+id).value = tot_price.toFixed(2);
							
			
							//document.getElementById('rowPrcCash'+id).value = tot_price;
							document.getElementById('rowPrcCash'+id).value = prcvalue;
							
							document.getElementById('rowPrcCharge'+id).value = details.prcCharge;
							document.getElementById('rowPrcCashorig'+id).value = details.prcCash;
							document.getElementById('rowpriceC1'+id).value = details.price_C1;
							document.getElementById('rowpriceC2'+id).value = details.price_C2;
							document.getElementById('rowpriceC3'+id).value = details.price_C3;
							
							document.getElementById('rowpriceC1orig'+id).value = details.price_C1;
							document.getElementById('rowpriceC2orig'+id).value = details.price_C2;
							document.getElementById('rowpriceC3orig'+id).value = details.price_C3;
							
							//forwarding = '<input type="checkbox" id="is_forward'+id+'" name="is_forward'+id+'" value="1" />';
							forwarding = '<input type="checkbox" id="is_forward'+id+'" name="is_forward'+id+'" value="1" />'+
							             '<input type="hidden" name="group'+id+'" id="group'+id+'" value="'+details.group+'">';
							
							//document.getElementById('is_forward'+id).checked = false;
							document.getElementById('is_forward-row'+id).innerHTML = forwarding;
														
							var name_serv = details.name;
							alert('"'+name_serv.toUpperCase()+'" is already in the list & has been UPDATED!');
							
							return true;
						}
					}
					if (items.length == 0)
	 					clearOrder(list);
				}

				alt = (dRows.length%2)+1;
				nf.setPlaces(nf.NO_ROUNDING);

				nf.setPlaces(2);
				//edited by VAN 03-06-08
				nf.setNumber(prcCash);
				//prcCash = isNaN(prcCash) ? 'N/A' : nf.toFormatted();
				prcCash = isNaN(prcCash) ? 'N/A' : prcCash.toFixed(2);
				
				//---- added by VAN 10-17-07
				nf.setNumber(prcCashC1);
				//prcCashC1 = isNaN(prcCashC1) ? 'N/A' : nf.toFormatted();
				prcCashC1 = isNaN(prcCashC1) ? 'N/A' : prcCashC1.toFixed(2);
				
				nf.setNumber(prcCashC2);
				//prcCashC2 = isNaN(prcCashC2) ? 'N/A' : nf.toFormatted();
				prcCashC2 = isNaN(prcCashC2) ? 'N/A' : prcCashC2.toFixed(2);
				
				nf.setNumber(prcCashC3);
				//prcCashC3 = isNaN(prcCashC3) ? 'N/A' : nf.toFormatted();
				prcCashC3 = isNaN(prcCashC3) ? 'N/A' : prcCashC3.toFixed(2);
				//----------------------------------
				nf.setNumber(totalCash);
				//totalCash = isNaN(totalCash) ? 'N/A' : nf.toFormatted();
				totalCash = isNaN(totalCash) ? 'N/A' : totalCash.toFixed(2);
				
				nf.setNumber(prcCharge);
				//prcCharge = isNaN(prcCharge) ? 'N/A' : nf.toFormatted();				
				prcCharge = isNaN(prcCharge) ? 'N/A' : prcCharge.toFixed(2);				
				nf.setNumber(totalCharge);
				//totalCharge = isNaN(totalCharge) ? 'N/A' : nf.toFormatted();
				totalCharge = isNaN(totalCharge) ? 'N/A' : totalCharge.toFixed(2);
				
				if (isCash) {
					prc=prcCash;
					tot=totalCash;
				}
				else {
					prc=prcCharge;
					tot=totalCharge;
				}
				
				/*
				if (details.sservice==0){
					nonSocialized = '<img src="../../images/btn_nonsocialized.gif" border="0" align="absmiddle"/>';
				}
				*/
				//if ($F('view_from')!='')
					toolTipTextHandler = ' onMouseOver="return overlib($(\'toolTipText'+id+'\').value, CAPTION,\'Details\',  '+
												'  TEXTPADDING, 8, CAPTIONPADDING, 4, TEXTFONTCLASS, \'oltxt\', CAPTIONFONTCLASS, \'olcap\', '+
												'  WIDTH, 250,FGCLASS,\'olfgjustify\',FGCOLOR, \'#bbddff\');" onmouseout="nd();"';
					nonSocialized='';
				
				if (details.sservice==0){
					nonSocialized='<img src="../../images/btn_nonsocialized.gif" border="0" onClick=""'+
									  ' onMouseOver="return overlib(\'This is a non-socialized service which means..secret!\', CAPTION,\'Non-socialized Service\',  '+
								     '  TEXTPADDING, 8, CAPTIONPADDING, 4, TEXTFONTCLASS, \'oltxt\', CAPTIONFONTCLASS, \'olcap\', '+
								     '  WIDTH, 250,FGCLASS,\'olfgjustify\',FGCOLOR, \'#bbddff\');" onmouseout="nd();">';
				   $('socialServiceNotes').style.display='';
			   }
				
				if ($F('discount')!=""){
					discount_percentage = $F('discount');
				}else{
					discount_percentage = 0;
				}
				
				adjust_amount = tot - (tot * parseFloat(discount_percentage));
				
				if ((details.sservice==1) && ((person_discountid) && (person_discountid!=" ")) && ((person_discountid=="C1")||(person_discountid=="C2")||(person_discountid=="C3"))){
					if (tot==0){
						valprice = formatNumber(prc,2);
						if ((document.getElementById('hasPaid').value==1)||(document.getElementById('view_from').value=='ssview')){
							//tot_price = '<input type="text" id="totprice'+id+'" name="totprice'+id+'" value=0.00 size="5" style="text-align:right" readonly onKeyDown="keyEnter(event, this,\''+id+'\');" onBlur="getSocialPrice(\''+id+'\'); formatNumber(this.value,2); refreshDiscount();">';
							tot_price = '<input type="text" id="totprice'+id+'" name="totprice'+id+'" value='+formatNumber(prc,2)+' size="10" style="text-align:right" readonly onKeyDown="keyEnter(event, this,\''+id+'\');" onBlur="getSocialPrice(\''+id+'\'); formatNumber(this.value,2); refreshDiscount();">';
						}else{
							//tot_price = '<input type="text" id="totprice'+id+'" name="totprice'+id+'" value=0.00 size="5" style="text-align:right" onKeyDown="keyEnter(event, this,\''+id+'\');" onBlur="getSocialPrice(\''+id+'\'); formatNumber(this.value,2); refreshDiscount();">';
							tot_price = '<input type="text" id="totprice'+id+'" name="totprice'+id+'" value='+formatNumber(prc,2)+' size="10" style="text-align:right" onKeyDown="keyEnter(event, this,\''+id+'\');" onBlur="getSocialPrice(\''+id+'\'); formatNumber(this.value,2); refreshDiscount();">';
							details.price_C1 = prc.replace(",","");
							details.price_C2 = prc.replace(",","");
							details.price_C3 = prc.replace(",","");
						}
					}else{
						//tot_price = formatNumber(tot,2);
						//tot_price = tot.toFixed(2);
						//alert('here1 = '+tot);
						if (tot!='N/A'){
							tot_price = tot;
						}else{
							tot_price = prc - (prc * parseFloat(discount_percentage));
							tot_price = formatNumber(tot_price,2);
						}
					}
					
				}else if ((details.sservice==1) && ((person_discountid) && (person_discountid!=" ")) &&  ((person_discountid!="C1")||(person_discountid!="C2")||(person_discountid!="C3"))){
					tot_price = tot - (tot * parseFloat(discount_percentage));
					tot_price = formatNumber(tot_price,2);
					//tot_price = tot_price.toFixed(2);
				}else{
					//tot_price = tot.toFixed(2);
					//commented by VAN 10-28-08
                    if (($F('orig_discountid')=='DMC') || ($F('orig_discountid')=='DMCDep')){
                       // alert('here');
                        tot_price = 0.00;
                        tot_price = formatNumber(tot_price,2);
                    }else{
                        tot_price = tot;
                        tot_price = formatNumber(tot,2);
                    }    
				}
				
				//alert('here2 = '+tot);
				//alert(details.prcCash);
				//alert("tot_price = "+tot_price+" - "+parseInt(tot_price).toFixed(2));
				//getSocialPrice(id);
				/*
				src = 
					'<tr class="wardlistrow'+alt+'" id="row'+id+'">'+
					'<input type="hidden" name="toolTipText'+id+'" id="toolTipText'+id+'" value="'+toolTipText+'" />'+
					'<input type="hidden" name="sservice[]" id="rowsservice'+id+'" value="'+details.sservice+'" />'+
					'<input type="hidden" name="price_C1[]" id="rowpriceC1'+id+'" value="'+details.price_C1+'" />'+
					'<input type="hidden" name="price_C2[]" id="rowpriceC2'+id+'" value="'+details.price_C2+'" />'+
					'<input type="hidden" name="price_C3[]" id="rowpriceC3'+id+'" value="'+details.price_C3+'" />'+
					'<input type="hidden" name="price_C1orig[]" id="rowpriceC1orig'+id+'" value="'+details.price_C1+'" />'+
					'<input type="hidden" name="price_C2orig[]" id="rowpriceC2orig'+id+'" value="'+details.price_C2+'" />'+
					'<input type="hidden" name="price_C3orig[]" id="rowpriceC3orig'+id+'" value="'+details.price_C3+'" />'+
					'<input type="text" name="pcash[]" id="rowPrcCash'+id+'" value="'+details.prcCash+'" />'+
					'<input type="hidden" name="pcashorig[]" id="rowPrcCashorig'+id+'" value="'+details.prcCash+'" />'+
					'<input type="hidden" name="pcharge[]" id="rowPrcCharge'+id+'" value="'+details.prcCharge+'" />'+
					'<input type="hidden" name="items[]" id="rowID'+id+'" value="'+id+'" />'+
					'<input type="hidden" name="requestDoc[]" id="rowDoc'+id+'" value="'+details.requestDoc+'" />'+
					'<input type="hidden" name="requestDocName[]" id="rowDocName'+id+'" value="'+details.requestDocName+'" />'+
					'<input type="hidden" name="requestDept[]" id="rowDept'+id+'" value="'+details.requestDept+'" />'+
					'<input type="hidden" name="requestDeptName[]" id="rowDeptName'+id+'" value="'+details.requestDeptName+'" />'+
					'<input type="hidden" name="isInHouse[]" id="rowHouse'+id+'" value="'+details.is_in_house+'" />'+
					'<input type="hidden" name="clinicInfo[]" id="rowInfo'+id+'" value="'+details.clinicInfo+'" />'+
					'<td class="centerAlign" ><a href="javascript: nd(); removeItem(\''+id+'\')"><img src="../../images/btn_delitem.gif" border="0"/></a></td>'+
					'<td align="centerAlign">'+nonSocialized+'</td>'+
					'<td width="15%" id="id'+id+'" '+toolTipTextHandler+'>'+id+'</td>'+
					'<td width="*" id="name'+id+'" '+toolTipTextHandler+'>'+details.name+'</td>'+
					'<td class="rightAlign" id="prc'+id+'" width="15%">'+formatNumber(prc,2)+'</td>'+
					'<td class="rightAlign" id="tot'+id+'" width="17%">'+tot_price+'</td>'+
				'</tr>';
				*/
				if (isNaN(tot_price)){
					prcvalue = prc;
					
				}else{
					//prcvalue = tot_price.replace(",","");
					//prcvalue = formatNumber(tot_price,2);
					prcvalue = tot_price;
				}
				//forwarding = '<input type="checkbox" id="is_forward'+id+'" name="is_forward[]" value="1" />';
				//forwarding = '<input type="checkbox" id="withsampleID'+id+'" name="withsampleID'+id+'" value="1" />';
				forwarding = '<input type="checkbox" id="withsampleID'+id+'" name="withsampleID'+id+'" disabled value="1" />'+
				             '<input type="hidden" name="group'+id+'" id="group'+id+'" value="'+details.group+'">';

				src = 
					'<tr class="wardlistrow'+alt+'" id="row'+id+'">'+
					'<input type="hidden" name="toolTipText'+id+'" id="toolTipText'+id+'" value="'+toolTipText+'" />'+
					'<input type="hidden" name="sservice[]" id="rowsservice'+id+'" value="'+details.sservice+'" />'+
					'<input type="hidden" name="price_C1[]" id="rowpriceC1'+id+'" value="'+details.price_C1+'" />'+
					'<input type="hidden" name="price_C2[]" id="rowpriceC2'+id+'" value="'+details.price_C2+'" />'+
					'<input type="hidden" name="price_C3[]" id="rowpriceC3'+id+'" value="'+details.price_C3+'" />'+
					'<input type="hidden" name="price_C1orig[]" id="rowpriceC1orig'+id+'" value="'+details.price_C1+'" />'+
					'<input type="hidden" name="price_C2orig[]" id="rowpriceC2orig'+id+'" value="'+details.price_C2+'" />'+
					'<input type="hidden" name="price_C3orig[]" id="rowpriceC3orig'+id+'" value="'+details.price_C3+'" />'+
					'<input type="hidden" name="pcash[]" id="rowPrcCash'+id+'" value="'+prcvalue+'" />'+
					'<input type="hidden" name="pcashorig[]" id="rowPrcCashorig'+id+'" value="'+details.prcCash+'" />'+
					'<input type="hidden" name="pcharge[]" id="rowPrcCharge'+id+'" value="'+details.prcCharge+'" />'+
					'<input type="hidden" name="items[]" id="rowID'+id+'" value="'+id+'" />'+
					'<input type="hidden" name="requestDoc[]" id="rowDoc'+id+'" value="'+details.requestDoc+'" />'+
					'<input type="hidden" name="requestDocName[]" id="rowDocName'+id+'" value="'+details.requestDocName+'" />'+
					'<input type="hidden" name="requestDept[]" id="rowDept'+id+'" value="'+details.requestDept+'" />'+
					'<input type="hidden" name="requestDeptName[]" id="rowDeptName'+id+'" value="'+details.requestDeptName+'" />'+
					'<input type="hidden" name="isInHouse[]" id="rowHouse'+id+'" value="'+details.is_in_house+'" />'+
					'<input type="hidden" name="clinicInfo[]" id="rowInfo'+id+'" value="'+details.clinicInfo+'" />'+
					'<td class="centerAlign" ><a href="javascript: nd(); removeItem(\''+id+'\')"><img src="../../images/btn_delitem.gif" border="0"/></a></td>'+
					'<td align="centerAlign">'+nonSocialized+'</td>'+
					'<td width="15%" id="id'+id+'" '+toolTipTextHandler+'>'+id+'</td>'+
					'<td width="*" id="name'+id+'" '+toolTipTextHandler+'>'+details.name+'</td>'+
					'<td width="5%" id="is_forward-row'+id+'" '+toolTipTextHandler+' align="center">'+forwarding+'</td>'+
					'<td class="rightAlign" id="prc'+id+'" width="15%">'+formatNumber(prc,2)+'</td>'+
					'<td class="rightAlign" id="tot'+id+'" width="17%">'+tot_price+'</td>'+
				'</tr>';
				trayItems++;
			}
			else {
				src = "<tr><td colspan=\"7\">Request list is currently empty...</td></tr>";	
			}
			//alert(src);
			dBody.innerHTML += src;
			
			//comment by VAN 11-15-07
			//document.getElementById('rowPrcCash'+id).value = tot_price;
			
			//alert("document.getElementById('rowPrcCash'+id).value = "+document.getElementById('rowPrcCash'+id).value);
			
			return true;
		}
	}
	return false;
}

function formatDiscount(valamount){
	document.getElementById('show-discount').value = formatNumber(valamount,2)
}

function removeItem(id) {
	var destTable, destRows;
	var table = $('order-list');
	var rmvRow=document.getElementById("row"+id);
	if (table && rmvRow) {
		$('rowID'+id).parentNode.removeChild($('rowID'+id));
		$('rowPrcCash'+id).parentNode.removeChild($('rowPrcCash'+id));
		$('rowPrcCharge'+id).parentNode.removeChild($('rowPrcCharge'+id));		
		$('rowsservice'+id).parentNode.removeChild($('rowsservice'+id));
		$('rowpriceC1'+id).parentNode.removeChild($('rowpriceC1'+id));		
		$('rowpriceC2'+id).parentNode.removeChild($('rowpriceC2'+id));		
		$('rowpriceC3'+id).parentNode.removeChild($('rowpriceC3'+id));
		$('rowPrcCashorig'+id).parentNode.removeChild($('rowPrcCashorig'+id));
		
		$('rowpriceC1orig'+id).parentNode.removeChild($('rowpriceC1orig'+id));
		$('rowpriceC2orig'+id).parentNode.removeChild($('rowpriceC2orig'+id));
		$('rowpriceC3orig'+id).parentNode.removeChild($('rowpriceC3orig'+id));
		
		var rndx = rmvRow.rowIndex;
		table.deleteRow(rmvRow.rowIndex);
		reclassRows(table,rndx);
	}
	
	var items = document.getElementsByName('items[]');
	if (items.length == 0){
		emptyIntialRequestList();
	}
	showSocialNotes();
	refreshDiscount();
}

function showSocialNotes() {	
	var isShow='none';
	var sservice = document.getElementsByName('sservice[]');
	var items = document.getElementsByName('items[]');
	for (var i=0;i<sservice.length;i++) {
		if (sservice[i].value == 0) {
			isShow='';   //there is still a nonsocialized item in the list
		}
	}
	
	$('socialServiceNotes').style.display=isShow;
}

function emptyIntialRequestList(){
	clearOrder($('order-list'));
	appendOrder($('order-list'),null);
}

function clearEncounter() {
	/*
	var iscash = $("iscash1").checked;
	$('ordername').value="";
	//$('ordername').readOnly=!iscash;
	$('orderaddress').value="";
	//$('orderaddress').readOnly=!iscash;
	$('pid').value="";
	$('encounter_nr').value="";
	$('discountid').value="";
	$('discount').value="";
	$('clear-enc').disabled = true;
	*/
	var iscash = $("iscash1").checked;
	$('ordername').value="";
	//$('ordername').readOnly=!iscash;
	$('orderaddress').value="";
	//$('orderaddress').readOnly=!iscash;
	//$('is_tpl').disabled = !iscash;
	$('pid').value="";
	$('encounter_nr').value="";
	$('clear-enc').disabled = true;
	//$('clear-enc').disabled = true;
	$('btndiscount').disabled = false;
	//$('sw-class').innerHTML = 'None';
	
	//added by VAN 07-02-08
	$('sw-class').innerHTML = '';
	$('patient_enctype').innerHTML = '';
	$('patient_location').innerHTML = '';
	$('patient_medico_legal').innerHTML = '';
	//refreshDiscount();
	//-----------------------
	
}

function pSearchClose() {
	//alert("lab request-gui.js : pSearchClose : ");
	cClick();  //function in 'overlibmws.js'
}

function emptyTray() {
	var items = document.getElementsByName('items[]');
	var id, i;
	//alert(items.length);
	//alert($('order-list').innerHTML);
	//for (i=0;i<items.length;i++){
		
	for (i=items.length-1; i>=0;i--){	
		//alert(i);
		id = items[i].value;
		//alert(i+" - "+id);
		
		//$('rowID'+id).parentNode.removeChild($('rowID'+id));
		//items[i].parentNode.removeChild(items[i]);
		//alert(i+' - '+'rowID'+id);
		$('rowID'+id).parentNode.removeChild($('rowID'+id));
		//document.getElementById('rowID'+id).parentNode.removeChild(document.getElementById('rowID'+id));
		
		$('rowPrcCash'+id).parentNode.removeChild($('rowPrcCash'+id));
		$('rowPrcCharge'+id).parentNode.removeChild($('rowPrcCharge'+id));		
		$('rowsservice'+id).parentNode.removeChild($('rowsservice'+id));
		$('rowpriceC1'+id).parentNode.removeChild($('rowpriceC1'+id));		
		$('rowpriceC2'+id).parentNode.removeChild($('rowpriceC2'+id));		
		$('rowpriceC3'+id).parentNode.removeChild($('rowpriceC3'+id));
		$('rowPrcCashorig'+id).parentNode.removeChild($('rowPrcCashorig'+id));
		
		$('rowpriceC1orig'+id).parentNode.removeChild($('rowpriceC1orig'+id));
		$('rowpriceC2orig'+id).parentNode.removeChild($('rowpriceC2orig'+id));
		$('rowpriceC3orig'+id).parentNode.removeChild($('rowpriceC3orig'+id));
		//alert("id after = "+i+" - "+id);
	}
	/*
	alert('after = '+items.length);
	//alert($('order-list').innerHTML);
	for (i=items.length;i>0;i--){	
		id = items[i].value;
		//alert(i+" - "+id);
		//$('rowID'+id).parentNode.removeChild($('rowID'+id));
	}
	*/
	
	clearOrder($('order-list'));
	appendOrder($('order-list'),null);
	refreshDiscount();
}


function changeTransactionType(mod) {

	var iscash = $("iscash1").checked;
	var prcList, id, total=0;
	var pid = $('pid').value;
	var person_discountid = $("discountid").value;
	var cash = document.getElementsByName("pcash[]");
	var cashorig = document.getElementsByName("pcashorig[]");
	var chargeorig = document.getElementsByName("pcharge[]");
	
	var cash_C1 = document.getElementsByName("price_C1orig[]");
	var cash_C2 = document.getElementsByName("price_C2orig[]");
	var cash_C3 = document.getElementsByName("price_C3orig[]");
	
	var ssClass = document.getElementsByName("sservice[]");
	
	var netprice, prcSS;
	//alert(document.getElementById('order-list').innerHTML);
	//if (mod)
		//clearEncounter();
	//$('sw-class').innerHTML = 'None';
	//alert($F('discountid'));
	
	if ($F('discount')!=""){
		discount_percentage = $F('discount');
	}else{
		discount_percentage = 0;
	}
	
	if (iscash){
		//alert("cash");
		//if (person_discountid){
		//added by VAN 06-01-08
		//$('tplrow').style.display = '';
		$('sw-class').innerHTML = $F('discountid');
		//$('type_charge').style.display='none';
		$('type_charge').style.display='';
		
		if ((person_discountid) && (person_discountid!=" ")){	
			if (person_discountid=="C1"){
				prcList = document.getElementsByName("price_C1[]");
			}else if (person_discountid=="C2"){
				prcList = document.getElementsByName("price_C2[]");	
			}else if (person_discountid=="C3"){
				prcList = document.getElementsByName("price_C3[]");		
			}else{
				// edit : original - (original * discount)
				prcList = document.getElementsByName("pcash[]");
				//prcList = document.getElementsByName("cashorig[]");
			}
		}else{
			prcList = document.getElementsByName("pcash[]");
			//prcList = document.getElementsByName("pcashorig[]");
		}
		document.getElementById('is_cash').value = 1;
	}else{
		//$('sw-class').innerHTML = 'None';	
		//$('tplrow').style.display = 'none';
		$('type_charge').style.display='';
		//$('type_charge').style.display='none';
		//document.getElementById('is_tpl').checked = false;
		
		//alert("charge");	
		prcList = document.getElementsByName("pcharge[]");
		document.getElementById('is_cash').value = 0;
		//document.getElementById('priority').value = 1;
	}
	//alert("price = "+prcList);
	if ($F('discount')!=""){
		discount_percentage = $F('discount');
	}else{
		discount_percentage = 0;
	}
	//alert("person_discountid = "+person_discountid);	
	for (var i=0;i<prcList.length;i++) {
		if (iscash){
			//if ((person_discountid) && (person_discountid!=" "))
			id = prcList[i].id.substring(10);
			//else
				//id = prcList[i].id.substring(14);
			
			price_orig = formatNumber(parseFloat(cashorig[i].value),2);
			
			if ((person_discountid) && (person_discountid!=" ")){	
				if (person_discountid=="C1"){
					prcSS = cash_C1[i].value;
				}else if (person_discountid=="C2"){
					prcSS = cash_C2[i].value;	
				}else if (person_discountid=="C3"){
					prcSS = cash_C3[i].value;		
				}else{
					// edit : original - (original * discount)
					prcSS = cashorig[i].value;
					
					//prcSS = parseFloat(cashorig[i].value) - (parseFloat(cashorig[i].value) * parseFloat(discount_percentage));
				}
			}else{
				prcSS = cashorig[i].value;
			}
		}else{
			id = prcList[i].id.substring(12);
			price_orig = formatNumber(parseFloat(chargeorig[i].value),2);
			prcSS = chargeorig[i].value;
		}
			
		/*
		if (parseFloat(prcList[i].value)==0){	
			netprice = '<input type="text" id="totprice'+id+'" name="totprice'+id+'" value="'+formatNumber(parseFloat(prcList[i].value),2)+'" size="5" style="text-align:right" onBlur="getSocialPrice(\''+id+'\'); formatNumber(this.value,2); refreshTotal();">';
		}else{
			netprice = formatNumber(parseFloat(prcList[i].value),2);
		}
		*/
		//alert("prcSS = "+prcSS);
		//((person_discountid) && (person_discountid!=" "))
		//if ((ssClass[i].value==1) && ((person_discountid)||(person_discountid)) && ((person_discountid=="C1")||(person_discountid=="C2")||(person_discountid=="C3"))){
		if ((ssClass[i].value==1) && ((person_discountid) && (person_discountid!=" ")) && ((person_discountid=="C1")||(person_discountid=="C2")||(person_discountid=="C3"))){
			
			if (prcSS==0){	
				//valprice = formatNumber(parseFloat(cashorig[i].value),2);
				if ((document.getElementById('hasPaid').value==1)||(document.getElementById('view_from').value=='ssview')){
					netprice = '<input type="text" id="totprice'+id+'" name="totprice'+id+'" value="'+formatNumber(parseFloat(prcList[i].value),2)+'" size="5" style="text-align:right" readonly onKeyDown="keyEnter(event, this,\''+id+'\');" onBlur="getSocialPrice(\''+id+'\'); formatNumber(this.value,2); refreshDiscount();">';
					//netprice = '<input type="text" id="totprice'+id+'" name="totprice'+id+'" size="5" style="text-align:right" readonly onKeyDown="keyEnter(event, this,\''+id+'\');" onBlur="getSocialPrice(\''+id+'\'); formatNumber(this.value,2); refreshDiscount();">';
				}else{
					netprice = '<input type="text" id="totprice'+id+'" name="totprice'+id+'" value="'+formatNumber(parseFloat(prcList[i].value),2)+'" size="5" style="text-align:right" onKeyDown="keyEnter(event, this,\''+id+'\');" onBlur="getSocialPrice(\''+id+'\'); formatNumber(this.value,2); refreshDiscount();">';
					//netprice = '<input type="text" id="totprice'+id+'" name="totprice'+id+'" value=0.00 size="5" style="text-align:right" onKeyDown="keyEnter(event, this,\''+id+'\');" onBlur="getSocialPrice(\''+id+'\'); formatNumber(this.value,2); refreshDiscount();">';
				}
				
			}else{
				netprice = formatNumber(parseFloat(prcList[i].value),2);
			}
		//}else if ((ssClass[i].value==1) && (person_discountid) &&  ((person_discountid!="C1")||(person_discountid!="C2")||(person_discountid!="C3"))){
		}else if ((ssClass[i].value==1) && ((person_discountid) && (person_discountid!=" ")) &&  ((person_discountid!="C1")||(person_discountid!="C2")||(person_discountid!="C3"))){	
			netprice = parseFloat(prcList[i].value) - (parseFloat(prcList[i].value) * parseFloat(discount_percentage));
			//document.getElementById('rowPrcCash'+id).value = netprice;
			netprice = formatNumber(netprice,2);
		}else{
			if (iscash){
				//if (person_discountid)
				if ((person_discountid) && (person_discountid!=" ")){
					netprice = formatNumber(parseFloat(prcList[i].value),2);
				}else{
					netprice = formatNumber(parseFloat(cashorig[i].value),2);
				}
			}else{
				netprice = formatNumber(parseFloat(chargeorig[i].value),2);
			}
		}
		//alert("prc = "+prcList[i].value);
		// comment 12-19-07
		//document.getElementById('rowPrcCash'+id).value = prcList[i].value;
		document.getElementById('rowPrcCash'+id).value = netprice;
		
		$('prc'+id).innerHTML = price_orig;
		$('tot'+id).innerHTML = netprice;
	}
	refreshDiscount();
}

function keyEnter(e,d,id){  
	if (e.keyCode == 13){
		getSocialPrice(id); 
		formatNumber(d.value,2); 
		refreshDiscount();
	}else{
		return false;
	}	
	
}

/*
function refreshDiscount() {
	
	var nodes = document.getElementsByName("discount[]");
	totalDiscount = 0;
	for (var i=0;i<nodes.length;i++) {
		if (nodes[i].value) totalDiscount += parseFloat(nodes[i].getAttribute('discount'));
	}
	
	var dItem = $("show-discount");
	if (dItem) {
		dItem.value = parseFloat(totalDiscount * 100).toFixed(2);
	}
	
	refreshTotal();
}
*/

function prufform(){
  /*
  if ($('refno').value=='') {
		alert("Enter the reference no.");
		d.refno.focus();
		return false;
	}
	*/
	if ($('orderdate').value=='') {
		alert("Enter the date of request.");
		$('orderdate').focus();
		return false;
	}
	
	if ($('ordername').value=='') {
		alert("Enter the patient's name.");
		$('ordername').focus();
		return false;
	}
	/*
	if ($('orderaddress').value=='') {
		alert("Enter the patient's address.");
		$('orderaddress').focus();
		return false;
	}
	*/
	
	// added by VAN 01-10-08
	if (document.getElementById('repeat').checked){
		if ($('remarks').value=='') {
			alert("Enter a remarks why the request should be repeated.");
			$('remarks').focus();
			return false;
		}
		
		if ($('head').value=='') {
			alert("Enter a name who approved .");
			$('head').focus();
			return false;
		}
		
		if ($('headID').value=='') {
			alert("Enter a user ID who approved .");
			$('headID').focus();
			return false;
		}
		
		if ($('headpasswd').value=='') {
			alert("Enter a password who approved .");
			$('headpasswd').focus();
			return false;
		}
	}
	
	$('inputform').submit();
	return true;
}

function getSocialPrice(id){
	var formatprice = parseFloat(document.getElementById('totprice'+id).value);
	var person_discountid = $("discountid").value;
	
	if (person_discountid=="C1"){
		document.getElementById('rowpriceC1'+id).value = parseFloat(document.getElementById('totprice'+id).value);
	}else if (person_discountid=="C2"){
		document.getElementById('rowpriceC2'+id).value = parseFloat(document.getElementById('totprice'+id).value);
	}else if (person_discountid=="C3"){
		document.getElementById('rowpriceC3'+id).value = parseFloat(document.getElementById('totprice'+id).value);
	}
	
	document.getElementById('rowPrcCash'+id).value = parseFloat(document.getElementById('totprice'+id).value);
	document.getElementById('totprice'+id).value = formatNumber(formatprice.toFixed(2),2);
}

function checkCharityAmount(totalNONSocializedAmount){
	var dAdjAmount = $("show-discount"), adjusted_amount=0,msg='';
	//alert('here');
	if (dAdjAmount) {
		adjusted_amount=dAdjAmount.value;
	}
 if (($F('orig_discountid')=='DMC') || ($F('orig_discountid')=='DMCDep')){
    //do nothing
 }else{
	if ((totalNet-totalNONSocializedAmount) - parseFloat(adjusted_amount) < 0){
		//the adjusted amount is MORE than the Net Total (OR payable amount)
		
        msg="ERROR :: One of these occurred, \n"+
				"  [1] The Adjusted Amount is MORE than the Net Total (OR payable amount). \n"+
				"  [2] Non-socialized services are not covered in the Adjusted Amount.";
		//alert(msg);
		dAdjAmount.value = $F('latest_valid_show-discount');
		return false;
	}else{
		totalDiscountedAmount = parseFloat(totalDiscountedAmount) + parseFloat(adjusted_amount);
		totalNet = totalNet - parseFloat(adjusted_amount);	
		return true;
	}
 }  
}

function refreshDiscount() {
	//alert("refreshDiscount");
	
	totalNet = 0;
	totalDiscountedAmount = 0;
	totalNONSocializedAmount = 0;
	total = 0;
	discount_amount = 0;

	var items = document.getElementsByName('items[]');
	var cash = document.getElementsByName('pcashorig[]');
	var charge = document.getElementsByName('pcharge[]');
	//var net = document.getElementsByName('pcash[]');
	var sservice = document.getElementsByName('sservice[]');
	var prcCash, prcCharge, id, isCash = $("iscash1").checked;
	var person_discountid = $("discountid").value;
	var net, ssclass;
	//alert('item = '+sservice);
	/*
	var list = $('order-list');
	var dBody=list.getElementsByTagName("tbody")[0];
	var str = dBody.innerHTML;
	alert('prepareAdd refreshDiscount = '+str);
	if (str.match("Request list is currently empty")!=null){
		items = 0;
		cash = 0;
		charge = 0;
		sservice = 0;
		items.length = 0;
	}
	*/
	if (person_discountid=="C1")
		net = document.getElementsByName('price_C1[]');
	else if (person_discountid=="C2")
		net = document.getElementsByName('price_C2[]');
	else if (person_discountid=="C3"){
		net = document.getElementsByName('price_C3[]');
	}else{
		net = document.getElementsByName('pcash[]');
	}
	
	orig = document.getElementsByName('pcash[]');
	//alert('items.length = '+items.length);
	for (var i=0;i<items.length;i++) {
		//alert('item = '+sservice[i].value);
		id = items[i].value;
		
		//alert('id = '+id);
		prcCash = parseFloat(cash[i].value);
		totalCash = prcCash;
		prcCharge = parseFloat(charge[i].value);
		totalCharge = prcCharge;
		prcCashNET = parseFloat(net[i].value);
		totalCashNET = prcCashNET;
		
		
		if (isCash){
			tot = totalCash;
			total +=parseFloat(tot); 
		}else{
			tot = totalCharge;
			total +=parseFloat(tot); 
		}
		
		
		if ((isCash)&&(sservice[i].value=='1')&&((person_discountid) && (person_discountid!=" "))){
				//if socialized service & type of transaction is cash
			if (parseFloat(net[i].value)){
				//alert(parseFloat(net[i].value));
				nettotalCash = parseFloat(net[i].value);
				ssclass = 0;
			}else{
				nettotalCash = 0;
				if (isNaN(parseFloat(net[i].value)))
					ssclass = 0;
				else
					ssclass = 1;
			}
			//alert($F('discount'));	
			//alert(items[i].value+" = "+nettotalCash);
			if ($F('discount')!=""){
				//added by VAN 07-02-08
				//if ($F('discountid')!=$F('old_discountid')){
				//	discount_percentage = $F('old_discount');
				//}else
				//--------------
					discount_percentage = $F('discount');
			}else{
               discount_percentage = 0;
			}
			
			if (((person_discountid) && (person_discountid!=" ")) && ((person_discountid=="C1")||(person_discountid=="C2")||(person_discountid=="C3"))){
				//alert("1 = "+totalCash+" - "+nettotalCash+" - "+ssclass);
				if (nettotalCash)	{
					discount_amount = totalCash - nettotalCash;
				}else{
					if (ssclass){
						discount_amount = totalCash - totalCash;
					}else{
						//alert("ds = "+discount_percentage);	
						if ($F(view_from))
							discount_amount = totalCash - (totalCash - (totalCash * parseFloat(discount_percentage)));
						else{
							if (isNaN(parseFloat(net[i].value)))	
								discount_amount = totalCash - parseFloat(orig[i].value);
							else
								discount_amount = totalCash - nettotalCash;
						}
					}
				}
			}else if (((person_discountid) && (person_discountid!=" ")) && ((person_discountid!="C1")||(person_discountid!="C2")||(person_discountid!="C3"))){
				//alert("2 = "+totalCash+" - ("+totalCash+" * "+parseFloat(discount_percentage)+")");
				discount_amount = totalCash - (totalCash - (totalCash * parseFloat(discount_percentage)));
			}
			//alert("discount_amount = "+discount_amount+" = "+totalCash+" - "+nettotalCash);
			totalDiscountedAmount+=discount_amount;
			//alert(totalDiscountedAmount);
		}
		if ((isCash)&&(sservice[i].value=='0')){
				//if NON-socialized service & type of transaction is cash
			//alert('totalNONSocializedAmount = ' +totalNONSocializedAmount);
            //totalNONSocializedAmount += parseFloat(tot);
            //alert($F('discountid'));
           // alert('here');
            if (($F('orig_discountid')=='DMC') || ($F('orig_discountid')=='DMCDep')){
              //  alert('here');
                //totalNONSocializedAmount = totalCash - (totalCash * parseFloat(discount_percentage));
                totalNONSocializedAmount = totalCash - (totalCash - (totalCash * parseFloat($F('discount'))));    
                //alert('non = '+totalNONSocializedAmount);
                //alert(totalDiscountedAmount);
                totalDiscountedAmount+=  totalNONSocializedAmount;
            }else {
                totalNONSocializedAmount = totalCash - (totalCash - (totalCash * parseFloat($F('discount'))));    
            //alert('totalCash = '+totalCash);
            //alert('discount_percentage = '+discount_percentage);
            //alert(totalNONSocializedAmount);    
		        totalNONSocializedAmount += parseFloat(tot); 
            } 
            
            //alert(totalNONSocializedAmount); 
        }
		//alert("total : totalDiscountedAmount = "+total+" - "+totalDiscountedAmount);
		totalNet = parseFloat(total) - parseFloat(totalDiscountedAmount);
	}

  //if (($F('orig_discountid')=='DMC') || ($F('orig_discountid')=='DMCDep')){
       //do nothing
  //}else
	var correctCharity=checkCharityAmount(totalNONSocializedAmount);   //adjustments from Social Service
	
    refreshTotal();

	return correctCharity;

}

function refreshTotal() {
	//alert("refreshTotal");
	var items = document.getElementsByName('items[]');
	var netcash = document.getElementsByName('pcash[]');
	var cash = document.getElementsByName('pcashorig[]');
	var charge = document.getElementsByName('pcharge[]');
	var isCash = $("iscash1").checked;
	var nf = new NumberFormat();
	/*
	var list = $('order-list');
	var dBody=list.getElementsByTagName("tbody")[0];
	var str = dBody.innerHTML;
	alert('prepareAdd refreshTotal = '+str);
	if (str.match("Request list is currently empty")!=null){
		items = 0;
		cash = 0;
		charge = 0;
		netcash = 0;
		items.length = 0;
	}
	*/
	total = 0.0;
	for (var i=0;i<items.length;i++) {
		if (isCash){
			total+=parseFloat(cash[i].value);
		}else{
			total+=parseFloat(charge[i].value);
		}
	}
	
	var subTotal = $("show-sub-total");
	var discountTotal = $("show-discount-total");
	var netTotal = $("show-net-total");
	var totalamt;
	
	//alert("refresh total");
	// added by VAN 01-10-08
	if (document.getElementById('repeat').checked){
		subTotal.innerHTML = formatNumber(total.toFixed(2),2);
		discountTotal.innerHTML = "-&nbsp;&nbsp;"+formatNumber(total.toFixed(2),2);
		totalamt = parseInt(subTotal.innerHTML) - (parseInt(discountTotal.innerHTML) + parseInt($('show-discount').value));
		netTotal.innerHTML = formatNumber(totalamt.toFixed(2),2);
	}else{
		subTotal.innerHTML = formatNumber(total.toFixed(2),2);
		discountTotal.innerHTML = "-&nbsp;&nbsp;"+formatNumber(totalDiscountedAmount.toFixed(2),2);
		netTotal.innerHTML = formatNumber(totalNet.toFixed(2),2);
	}
	/*
	subTotal.innerHTML = formatNumber(total.toFixed(2),2);
	discountTotal.innerHTML = "-&nbsp;&nbsp;"+formatNumber(totalDiscountedAmount.toFixed(2),2);
	netTotal.innerHTML = formatNumber(totalNet.toFixed(2),2);
	*/
}


/*
function refreshTotal(){
	var items = document.getElementsByName('items[]');
	var cash = document.getElementsByName('pcash[]');
	var charge = document.getElementsByName('pcharge[]');
	var cashorig = document.getElementsByName("pcashorig[]");
	
	var price_C1 = document.getElementsByName('price_C1[]');
	var price_C2 = document.getElementsByName('price_C2[]');
	var price_C3 = document.getElementsByName('price_C3[]');
	
	var socialized = document.getElementsByName('sservice[]');
	var netprice;
	var person_discountid = $("discountid").value;
	var isCash = $("iscash1").checked;
	var nf = new NumberFormat();
   
	total = 0.0;
	totalnet = 0.0;
	
	for (var i=0;i<items.length;i++) {
		if (isCash){
			total+=parseFloat(cashorig[i].value);
			//if ((person_discountid) && (socialized[i].value==1)){	
			if (((person_discountid) && (person_discountid!=" ")) && (socialized[i].value==1)){	
				if (person_discountid=="C1"){
					totalnet+=parseFloat(price_C1[i].value);
				}else if (person_discountid=="C2"){
					totalnet+=parseFloat(price_C2[i].value);
				}else if (person_discountid=="C3"){
					totalnet+=parseFloat(price_C3[i].value);
				}else{
					netprice = parseFloat(cash[i].value) - (parseFloat(cash[i].value) * parseFloat($F('discount')));
					totalnet+=netprice;
					
				}
			}else{
				if (person_discountid)
					totalnet+=parseFloat(cash[i].value);
				else
					totalnet+=parseFloat(cashorig[i].value);
			}
		}else{
			totalnet+=parseFloat(charge[i].value);
			total+=parseFloat(charge[i].value);
		}
	}

	var subTotal = $("show-sub-total");
	var discountTotal = $("show-discount-total");
	var netTotal = $("show-net-total");
	
	//subTotal.innerHTML = formatNumber(total.toFixed(2),2);
	subTotal.innerHTML = formatNumber(total.toFixed(2),2);
	//discountTotal.innerHTML = "-"+formatNumber((total * totalDiscount).toFixed(2),2);
	//discountTotal.innerHTML = "-&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"+formatNumber(parseFloat(document.getElementById('show-discount').value),2);
	var discnt = document.getElementById('show-discount').value;
	discnt = discnt.replace(",","");
	var totaldiscount = parseFloat(discnt) + (total-totalnet);
	discountTotal.innerHTML = "-&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"+formatNumber(totaldiscount,2);
	//netTotal.innerHTML = formatNumber((total - (total * totalDiscount)).toFixed(2),2);
	//netTotal.innerHTML = formatNumber((total - totaldiscount).toFixed(2),2);
	netTotal.innerHTML = formatNumber(totalnet.toFixed(2),2);
}
*/

function viewPatientRequest(is_cash,pid,refno){
	//alert("viewPatientRequest is_cash,pid,refno = "+is_cash+" - "+pid+" - "+refno);
	var no_of_group = document.getElementById('no_of_group').value;
	var ispaid = document.getElementById('ispaid').value;
	var withclaimstub = document.getElementById('withclaimstub').value;
    
	//window.open("seg-lab-request.php?is_cash="+is_cash+"&pid="+pid+"&refno="+refno+"&ispaid="+ispaid+"&showBrowser=1","viewPatientRequest","width=620,height=440,menubar=no,resizable=yes,scrollbars=yes");
	//if (no_of_group>1)
		window.open("seg-lab-request2.php?is_cash="+is_cash+"&pid="+pid+"&refno="+refno+"&ispaid="+ispaid+"&withclaimstub="+withclaimstub+"&showBrowser=1","viewPatientRequest2","left=150, top=100, width=620,height=440,menubar=no,resizable=yes,scrollbars=yes");
}

//added by VAN 10-09-08
function viewClaimStub(is_cash,refno){
   // alert("viewPatientRequest refno = "+refno);
    
    window.open("seg-claimstub.php?refno="+refno+"&is_cash="+is_cash+"&showBrowser=1","viewClaimStab","width=620,height=440,menubar=no,resizable=yes,scrollbars=yes");
  
}
//------------------------

//function viewPatientResult(refno, code, pid){
function viewPatientResult(refno, code){	
	var status = document.getElementById('billstatus').value;
	//alert("viewPatientRequest is_cash,pid,refno = "+is_cash+" - "+pid+" - "+refno);
	//window.open("seg-lab-request-result-pdf.php?refno="+refno+"&service_code="+code+"&pid="+pid+"&showBrowser=1","viewPatientResult","width=620,height=440,menubar=no,resizable=yes,scrollbars=yes")
	window.open("seg-lab-request-result-pdf.php?refno="+refno+"&service_code="+code+"&status="+status+"&showBrowser=1","viewPatientResult","width=620,height=440,menubar=no,resizable=yes,scrollbars=yes")
}

//function viewPatientResult_Summary(refno, code, pid){
function viewPatientResult_Summary(refno, code){	
	var status = document.getElementById('billstatus').value;
	//alert("viewPatientRequest is_cash,pid,refno = "+is_cash+" - "+pid+" - "+refno);
	//window.open("seg-lab-request-result-summary-pdf.php?refno="+refno+"&service_code="+code+"&pid="+pid+"&showBrowser=1","viewPatientResult_Summary","width=620,height=440,menubar=no,resizable=yes,scrollbars=yes")
	window.open("seg-lab-request-result-summary-pdf.php?refno="+refno+"&service_code="+code+"&status="+status+"&showBrowser=1","viewPatientResult_Summary","width=620,height=440,menubar=no,resizable=yes,scrollbars=yes")
}

function getBill_Status(status){
	document.getElementById('billstatus').value = status;
}

function warnClear() {
	var items = document.getElementsByName('items[]');
	if (items.length == 0) return true;
	else return confirm('Performing this action will clear the order tray. Do you wish to continue?');
}

function seniorCitizen(){
	
}

//commented by VAN 03-07-08
/*
function preSet(refno, service_code){
	//alert("alert message = "+refno+" - "+service_code);	
	xajax_checkIfalreadyPaid_Granted(refno, service_code);
}
*/
// added by VAN 12-20-07
/*
function HasLabResult(oldrefno){
	xajax_checkIfhasResult(refno);
}

function ValidatehasResult(response){
	if (response == false) {
		alert("The reference no. of the request has no result yet. It can be requested again.");
		$('parent_refno').value="";
		$('parent_refno').focus();
		//return false;
	}
	
	//return true;
}
*/
