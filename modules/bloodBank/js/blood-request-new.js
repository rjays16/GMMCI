var totalDiscount = 0, totalDiscountedAmount=0, totalNet=0, totalNONSocializedAmount=0;
var HSM = "HOSPITAL SPONSORED MEMBER";
var NBB = "SPONSORED MEMBER";

function formatNumber(num,dec) {
	var nf = new NumberFormat(num);
	if (isNaN(dec)) dec = nf.NO_ROUNDING;
	nf.setPlaces(dec);
	return nf.toFormatted();
}

function clearEncounter() {
	var iscash = $("iscash1").checked;
	$('ordername').value="";
	$('orderaddress').value="";
	$('pid').value="";
	$('encounter_nr').value="";
	$('clear-enc').disabled = true;
	$('discount').value = '';
	$('discountid').value = '';

	$('refno').value = '';
	$('mode').value="save";

	$('btndiscount').disabled = false;
	$('sw-class').innerHTML = '';
	$('patient_enctype').innerHTML = '';
	$('patient_location').innerHTML = '';
	$('patient_medico_legal').innerHTML = '';

	$('current_att_dr_nr').value = '';
	$('current_dept_nr').value = '';
	$('impression').value = '';
	$('hrn').innerHTML = '';
	$('dob').innerHTML = '';
	$('sex').innerHTML = '';
	$('age').innerHTML = '';
	$('is_walkin').checked = false;

	if (iscash==true)
		$('is_cash').value = 1;
	else
		$('is_cash').value = 0;

	$('btnHistory').style.display = "none";
    $('btn-coverage').style.display = "none";
	$('ptype').value = "";
	$('orig_discountid').value = "";
	$('discount2').value = "";

	$('ic_row').style.display = "none";
	$('is_charge2comp').checked = false;
	$('compName').value = "";
	$('compID').value = "";
	//$('source_req').value = "";
    
    $('iscash1').checked = true; 
    $('iscash0').checked = false; 
    $('is_cash').value = 1;
    $('grant_type').value = "";
    $('type_charge').style.display='none';
    $('for_manual').disabled = false;
    $('btn-coverage').style.display = "none";
    $('cov_type').update('');
    $('cov_amount').update('');
    $('coverage').setAttribute('value',-1);
    $('phic_ajax').hide();
    
    $('warningcaption').innerHTML = ''; 
    
    
    if (!iscash) {
        updateCoverage(['']);
    }

	setPriority(0);
}

function pSearchClose() {
    var nr = $('encounter_nr').value;
    //updatePHICCoverage([nr]);
    if (!$("iscash1").checked) {
        updateCoverage([nr]);
    }    
	cClick();  //function in 'overlibmws.js'
}

function emptyTray() {
	var items = document.getElementsByName('items[]');
	var id;

	for (var i=0;i<items.length;i++) {
		id = items[i].value;
		$('rowID'+id).parentNode.removeChild($('rowID'+id));
		$('rowPrcCash'+id).parentNode.removeChild($('rowPrcCash'+id));
		$('rowPrcCharge'+id).parentNode.removeChild($('rowPrcCharge'+id));
		$('rowPrcNet'+id).parentNode.removeChild($('rowPrcNet'+id));
		$('rowQty'+id).parentNode.removeChild($('rowQty'+id));
		$('sservice'+id).parentNode.removeChild($('sservice'+id));
	}

	clearOrder($('order-list'));
	appendOrder($('order-list'),null);
	refreshDiscount();
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

function showSocialNotes() {
	var isShow='none';
	var sservice = document.getElementsByName('sservice[]');
	for (var i=0;i<sservice.length;i++) {
		if (sservice[i].value == 0) {
			isShow='';   //there is still a nonsocialized item in the list
		}
	}
	$('socialServiceNotes').style.display=isShow;
}

function appendOrder(list,details) {
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0];

		if (dBody) {
			var isCash = $("iscash1").checked;

			var totalNetCash;
			var src, toolTipText;
			var btnicon;
			var paidcnt = 0;
			var lastRowNum = null,
					items = document.getElementsByName('items[]');
					dRows = dBody.getElementsByTagName("tr");
			var nf = new NumberFormat();
			var ptype = $('ptype').value;
			var sample;

			nf.setPlaces(2);
			//alert('details = '+details);
			if (details) {
				var id = details.id,
					idGrp = details.idGrp,
					qty = parseFloat(details.qty),
					prcCash = parseFloat(details.prcCash),
					prcCharge = parseFloat(details.prcCharge);
                    net_price = parseFloat(details.net_price);

					totalNetCash = net_price*qty;
					//alert('1 totalNetCash = '+totalNetCash);
					alt = (dRows.length%2)+1;
					nf.setNumber(qty);
					nf.setPlaces(nf.NO_ROUNDING);
					qty = isNaN(qty) ? '0' : ''+nf.toFormatted();

					nf.setPlaces(2);
					nf.setNumber(prcCash);
					prcCash = isNaN(prcCash) ? 'N/A' : nf.toFormatted();
					nf.setNumber(prcCharge);
					prcCharge = isNaN(prcCharge) ? 'N/A' : nf.toFormatted();

					nf.setNumber(totalNetCash);
					totalNetCash = isNaN(totalNetCash) ? 'N/A' : nf.toFormatted();

				if (isCash) {
					prc=prcCash;
				}
				else {
					prc=prcCharge;
				}
				discountedprc = net_price;
				nf.setNumber(discountedprc);
				discountedprc = isNaN(discountedprc) ? 'N/A' : nf.toFormatted();
				tot = totalNetCash;
				//alert('js= '+tot);
				//var person_discountid = $("discountid").value;

				toolTipText = "Requesting doctor: <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"+details.requestDocName+" <br>"+
									"Clinical Impression: <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"+details.clinicInfo;
                                    
                if (details.withsample=='NO SAMPLE'){
                     rec = '<font color="#000066"><b>NO SAMPLE</b></font>';
                     toolTipTextHandler2 = '';
                }else{
                     rec = '<font color="#FF0000"><b>'+details.withsample+'</b></font>';

                     toolTipTextHandler2 = ' onMouseOver="return overlib($(\'toolTipText2'+id+'\').value, CAPTION,\'Details of Received Sample\',  '+
                                '  TEXTPADDING, 8, CAPTIONPADDING, 4, TEXTFONTCLASS, \'oltxt\', CAPTIONFONTCLASS, \'olcap\', '+
                                '  WIDTH, 300,FGCLASS,\'olfgjustify\',FGCOLOR, \'#bbddff\');" onmouseout="nd();"';
                }
                toolTipText2 = details.details_rec;                    

                //added by VAS 03-21-2012
                // Check coverage limit
                    if($('grant_type').value=="phic") {
                        
                        //var coverageLimit = parseFloatEx($('cov_amount').innerHTML);
                        var coverageLimit = parseFloatEx($('coverage').value); 
                        total= parseFloatEx(total) + parseFloatEx(tot.replace(",","")); 
                        if (coverageLimit!=-1) {
                            if (coverageLimit < total) {
                                alert("Coverage limit exceeded for this item...");
                                return true;
                            }
                        }
                    }
                                    
				if (items) {
					for (var i=0;i<items.length;i++) {
						if (items[i].value == details.id) {
							$('toolTipText'+id).value = toolTipText;
							$('rowPrcCash'+id).value = details.prcCash;
							$('rowPrcCharge'+id).value = details.prcCharge;
							$('rowPrcNet'+id).value = details.net_price;
							$('rowDoc'+id).value = details.requestDoc;
							$('rowDocName'+id).value = details.requestDocName;
							$('rowDept'+id).value = details.requestDept;
							$('rowHouse'+id).value = details.is_in_house;
							$('rowInfo'+id).value = details.clinicInfo;
							$('rowQty'+id).value = qty;
							document.getElementById('idGrp'+id).innerHTML = id;
							document.getElementById('name'+id).innerHTML = details.name;
							document.getElementById('prc'+id).innerHTML = prc;
							document.getElementById('discountedprc'+id).innerHTML = discountedprc;
							document.getElementById('tot'+id).innerHTML = tot;
							//alert('update = '+tot);
							var name_serv = details.name;
							alert('"'+name_serv.toUpperCase()+'" is already in the list & has been UPDATED!');
							return true;
						}
					}
					if (items.length == 0)
						clearOrder(list);
				}

			delitemImg = '<a href="javascript: nd(); removeItem(\''+id+'\');">'+
							 '	<img src="../../images/btn_delitem.gif" border="0"/></a>';
			paiditemImg = '<img src="../../images/btn_paiditem.gif" border="0" onClick="">';
			unpaiditemImg = '<img src="../../images/btn_unpaiditem.gif" border="0" onClick="">';

			charityImg = '<img src="../../images/btn_charity.gif" border="0" onClick="">';
			cmapImg = '<img src="../../images/btn_cmap.gif" border="0" onClick="">';
			lingapImg = '<img src="../../images/btn_lingap.gif" border="0" onClick="">';
			missionImg = '<img src="../../images/btn_mission.gif" border="0" onClick="">';

			// added by VAN 01-15-08
			repeatitemImg = '<img src="../../images/btn_repeat.gif" border="0" onClick="">';

			refno_hasPaid = $F('hasPaid');
			view_mode = 0;
			if ($F('view_from')!='')
				view_mode = 1;
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
            
			if (view_mode==1)
				btnicon = ((details.hasPaid==1)?paiditemImg:unpaiditemImg);
			else{
				 if ((($('parent_refno').value!=null)||($('parent_refno').value!=""))&&($('repeat').checked)){
						btnicon = repeatitemImg;
				 }else if ($('is_cash').value==1){
						if (($('hasPaid').value==1)||(details.hasPaid)){
							if (details.pay_type!=""){
								if (details.pay_type=='paid')
									 btnicon = paiditemImg;
								else if ((details.pay_type=='lingap') || (details.pay_type=='cmap')
													|| (details.pay_type=='mission') || (details.pay_type=='charity'))
									btnicon = '<img src="../../images/btn_'+details.pay_type+'.gif" border="0" onClick="">';
								else
									btnicon = delitemImg;

								paidcnt =+ 1;
								disabled = "";
							}else{
									if (paidcnt>=1)
										btnicon = unpaiditemImg;
									else
										btnicon = delitemImg;
									disabled = "disabled";
							}

						}else{
								btnicon = delitemImg;
								disabled = "disabled";
						}
				}else{   
					if ($('grant_type').value!=""){
						if ($('mode').value=='update'){
							//btnicon = '<img src="../../images/btn_'+$('grant_type').value+'.gif" border="0" onClick="">';
							if ((details.pay_type=='lingap') || (details.pay_type=='cmap')
													|| (details.pay_type=='mission') || (details.pay_type=='charity'))
									btnicon = '<img src="../../images/btn_'+details.pay_type+'.gif" border="0" onClick="">';
							else
									btnicon = delitemImg;

							disabled = "";
						}else{
							btnicon = delitemImg;
							disabled = "disabled";
						}
					}else{
						btnicon = delitemImg;
						disabled = "";
					}
				}
			}
            
            var enabledBTN = 0;

			if (($F('view_from')=='ssview') || ($F('view_from')=='override')){
				readonly = 'readonly = "readonly"';
                enabledBTN = 0
			}else{
				readonly = "";
                enabledBTN = 1;   
            }    

			if (($("iscash0").checked)||($('hasPaid').value==1)){
				readonly_sample = "";
                enabledBTN = 1;
			}else{
				readonly_sample = "readonly";
                enabledBTN = 0;
            }    

			var received_qty = '';

			if (details.qty_received)
				received_qty = details.qty_received;
            
            var refno = $('refno').value;    
            
			//sample = '<input type="text" '+readonly_sample+' class="jedInput" name="rowSample'+id+'" id="rowSample'+id+'" size="3" maxlength="2" value="'+received_qty+'" style="width:80%;text-align:right" onblur="validateReceived(this.value,'+details.qty+',\''+id+'\')" onKeyDown="keyEnterValidateReceived(event, this.value, '+details.qty+',\''+id+'\');" />';
            //if (enabledBTN==1)
            if ($('mode').value=='update')
                if (enabledBTN==1)
                    sample = '<img border="0" id="received'+id+'" name="received'+id+'"  onclick="viewReceivedSample(\''+id+'\',\''+refno+'\')" src="../../images/cashier_edit_3.gif" style="cursor:pointer">';
                else
                    sample = '<img border="0" id="received'+id+'" name="received'+id+'"  onclick="alert(\'Not yet PAID...\');" src="../../images/cashier_edit_3.gif" style="cursor:default">';    
                
            else{
                if (enabledBTN==1)
                    msg = 'Data is not yet SAVED...';
                else
                    msg = 'Not yet PAID...';    
                    
                sample = '<img border="0" id="received'+id+'" name="received'+id+'"  onclick="alert(\''+msg+'\');" src="../../images/cashier_edit_3.gif" style="cursor:default">';    
            }
			src =
					'<tr class="wardlistrow'+alt+'" id="row'+id+'"> '+
					'<input type="hidden" name="toolTipText'+id+'" id="toolTipText'+id+'" value="'+toolTipText+'" />'+
                    '<input type="hidden" name="toolTipText2'+id+'" id="toolTipText2'+id+'" value="'+toolTipText2+'" />'+
					'<input type="hidden" name="sservice[]" id="sservice'+id+'" value="'+details.sservice+'" />'+
					'<input type="hidden" name="pcash[]" id="rowPrcCash'+id+'" value="'+details.prcCash+'" />'+
					'<input type="hidden" name="pcharge[]" id="rowPrcCharge'+id+'" value="'+details.prcCharge+'" />'+
					'<input type="hidden" name="items[]" id="rowID'+id+'" value="'+id+'" />'+
					'<input type="hidden" name="requestDoc[]" id="rowDoc'+id+'" value="'+details.requestDoc+'" />'+
					'<input type="hidden" name="requestDept[]" id="rowDept'+id+'" value="'+details.requestDept+'" />'+
					'<input type="hidden" name="requestDocName[]" id="rowDocName'+id+'" value="'+details.requestDocName+'" />'+
					'<input type="hidden" name="isInHouse[]" id="rowHouse'+id+'" value="'+details.is_in_house+'" />'+
					'<input type="hidden" name="clinicInfo[]" id="rowInfo'+id+'" value="'+details.clinicInfo+'" />'+
					'<input type="hidden" name="pnet[]" id="rowPrcNet'+id+'" value="'+details.net_price+'" />'+
					'<input type="hidden" name="pnetbc[]" id="rowPrcNetbc'+id+'" value="'+details.net_price+'" />'+
					'<td class="centerAlign">'+
					btnicon
					+'</td>'+
					'<td align="centerAlign">'+nonSocialized+'</td>'+
					'<td id="idGrp'+id+'"'+toolTipTextHandler+'>'+id+'</td>'+
					'<td id="name'+id+'"'+toolTipTextHandler+'>'+details.name+'</td>'+
					'<td class="centerAlign" id="qty'+id+'">'+
						'<input type="text" '+readonly+' class="jedInput" name="qty[]" id="rowQty'+id+'" itemID="'+id+'" value="'+details.qty+'" prevValue="'+details.qty+'" style="width:80%;text-align:right" onblur="adjustQty(this)" onKeyDown="keyEnter(event, this);"/>'+
					'</td>'+
					'<td class="centerAlign" id="sample'+id+'" '+toolTipTextHandler2+'>'+sample+'</td>'+
					'<td class="rightAlign" id="prc'+id+'">'+prc+'</td>'+
					'<td class="rightAlign" id="discountedprc'+id+'">'+discountedprc+'</td>'+
					'<td class="rightAlign" id="tot'+id+'">'+tot+'</td>'+
					'</tr>';
				trayItems++;
			}
			else {
				src = "<tr><td colspan=\"9\">Request list is currently empty...</td></tr>";
			}
			dBody.innerHTML += src;
			document.getElementById('counter').innerHTML = items.length;
			return true;
		}
	}
	return false;
}

function validateReceived(received, ordered, id){
	// # of quantity received <= # of quantity ordered
	if (isNaN(received)) {
		//obj.value = obj.getAttribute("prevValue");
		$('rowSample'+id).value = '';
		$('rowSample'+id).focus();
		return false;
	}

	if (parseFloatEx(received) > parseFloatEx(ordered)) {
		alert("Number of received unit should be less than or equal than the ordered unit.");
		$('rowSample'+id).value = '';
		$('rowSample'+id).focus();
		return false;
	}else
	//obj.setAttribute("prevValue",parseFloatEx(obj.value));
		return true;
}

function keyEnterValidateReceived(e,received, ordered, id){
	if (e.keyCode == 13){
		validateReceived(received, ordered);
	}else{
		return false;
	}
}

function parseFloatEx(x) {
	var str = x.toString().replace(/\,|\s/,'')
	return parseFloat(str)
}


function removeItem(id) {
	var destTable, destRows;
	var table = $('order-list');
	var rmvRow=document.getElementById("row"+id);
	if (table && rmvRow) {
		$('rowID'+id).parentNode.removeChild($('rowID'+id));
		$('rowPrcCash'+id).parentNode.removeChild($('rowPrcCash'+id));
		$('rowPrcCharge'+id).parentNode.removeChild($('rowPrcCharge'+id));
		$('rowPrcNet'+id).parentNode.removeChild($('rowPrcNet'+id));
		$('rowQty'+id).parentNode.removeChild($('rowQty'+id));
		$('sservice'+id).parentNode.removeChild($('sservice'+id));
		var rndx = rmvRow.rowIndex-1;
		table.deleteRow(rmvRow.rowIndex);
		reclassRows(table,rndx);
	}

	var items = document.getElementsByName('items[]');
	if (items.length == 0){
		emptyIntialRequestList();
	}

	document.getElementById('counter').innerHTML = items.length;
	showSocialNotes();
	refreshDiscount();
}

function changeTransactionType() {
	var iscash = $("iscash1").checked;
	var prcList, id, total=0;
	var pid = $('pid').value;
	var encounter_nr = $('encounter_nr').value;
	//clearEncounter();
    
    var mgh = $('is_maygohome').value;
    var bill_nr = $('bill_nr').value;
    var warning = $('warningcaption').innerHTML;

	if ((pid)&&(!encounter_nr)&&(!iscash)){
		alert('Charging is only allowed for current hospital patients...');
		$("iscash1").checked = true;
		iscash = true;
    }else if ((mgh==1) && (bill_nr!='') &&(!iscash)){
        //mgh or have save billing
        alert('Charging is NOT allowed to this patient. '+warning);
        $("iscash1").checked = true;
        iscash = true;    
	}else{
		if (iscash){
			$('sw-class').innerHTML = $F('discountid');
			prcList = document.getElementsByName("pcash[]");
			$('is_walkin').checked = false;
			$('is_walkin').disabled = false;
		}else{
			$('sw-class').innerHTML = 'None';
			prcList = document.getElementsByName("pcharge[]");
			$('is_walkin').checked = false;
			$('is_walkin').disabled = true;
		}
        
		if (iscash==true){
			$('is_cash').value = 1;
			$('type_charge').style.display='none';

			$('for_manual').disabled = false;
            
            $('btn-coverage').style.display = "none";
        
		}else{
			$('is_cash').value = 0;
			$('type_charge').style.display='';

			$('for_manual').disabled = true;
			$('for_manual').checked = false;
            
            $('btn-coverage').style.display = "";
			//added by VAN 06-02-2011
			setManualPayment();

		}
		//$('type_charge').style.display='';

		for (var i=0;i<prcList.length;i++) {
			if (iscash)
				id = prcList[i].id.substring(10);
			else
				id = prcList[i].id.substring(12);
			$('prc'+id).innerHTML = formatNumber(prcList[i].value,2);
			$('tot'+id).innerHTML = formatNumber(parseFloat($('rowQty'+id).value)*parseFloat(prcList[i].value),2);
		}
        
        if ($('encounter_nr').value && !$("iscash1").checked){ 
            updateCoverage([$('encounter_nr').value])
        }else{
            $('cov_type').update('');
            $('cov_amount').update('');
            $('coverage').setAttribute('value',-1);
            $('phic_ajax').hide();
        }    
    
		refreshDiscount();
	}
}

function refreshDiscount() {
	//var nodes = $("discount");
	totalDiscount = 0;
	totalNet = 0;
	totalDiscountedAmount = 0;
	totalNONSocializedAmount = 0;

	var items = document.getElementsByName('items[]');
	var cash = document.getElementsByName('pcash[]');
	var charge = document.getElementsByName('pcharge[]');
	var net = document.getElementsByName('pnet[]');

	var sservice = document.getElementsByName('sservice[]');
	var prcCash, prcCharge, prcNet, id, isCash = $("iscash1").checked;
	var qty = document.getElementsByName('qty[]');
	//var person_discountid = $("discountid").value;

	for (var i=0;i<items.length;i++) {
		id = items[i].value;
		prcCash = parseFloat(cash[i].value);
		//totalCash = prcCash*parseFloat(qty[i].value);
		prcCharge = parseFloat(charge[i].value);
		//totalCharge = prcCharge*parseFloat(qty[i].value);
		prcNet = parseFloat(net[i].value);

		if (isCash)
			totalPrice = prcCash*parseFloat(qty[i].value);
		else
			totalPrice = prcCharge*parseFloat(qty[i].value);

		totalNet = prcNet*parseFloat(qty[i].value);
		totalDiscount = totalPrice - totalNet;
		totalDiscountedAmount += totalDiscount;
	}
	//alert('totalDiscountedAmount = '+totalDiscountedAmount);
	refreshTotal();
}

function refreshTotal() {
	var items = document.getElementsByName('items[]');
	var cash = document.getElementsByName('pcash[]');
	var charge = document.getElementsByName('pcharge[]');
	var qty = document.getElementsByName('qty[]');
	var isCash = $("iscash1").checked;
	var nf = new NumberFormat();
	var NetTotal = 0;

	total = 0.0;
	for (var i=0;i<items.length;i++) {
		if (isCash)
			total+=parseFloat(cash[i].value)*parseFloat(qty[i].value);
		else
			total+=parseFloat(charge[i].value)*parseFloat(qty[i].value);
	}

	var subTotal = $("show-sub-total");
	var discountTotal = $("show-discount-total");
	var netTotal = $("show-net-total");

	//var dAdjAmount = $("show-discount");
	NetTotal =  total - totalDiscountedAmount;

	subTotal.innerHTML = formatNumber(total.toFixed(2),2);
	discountTotal.innerHTML = "-"+formatNumber(totalDiscountedAmount.toFixed(2),2);
	netTotal.innerHTML = formatNumber(NetTotal.toFixed(2),2);
        
   if ($('coverage').value!=-1 && !$("iscash1").checked) {
        var coverage=parseFloatEx($('coverage').value)
        if($('mem_category').innerHTML == HSM){
			$('cov_amount').update('HSM');
		}else if ($('mem_category').innerHTML == NBB){
			$('cov_amount').update('NBB');
		} else{
			$('cov_amount').update(formatNumber(coverage-total,2));
		}
    }     
}

Array.prototype.max = function() {
var max = this[0];
var len = this.length;
for (var i = 1; i < len; i++) if (this[i] > max) max = this[i];
return max;
}

Array.prototype.min = function() {
var min = this[0];
var len = this.length;
for (var i = 1; i < len; i++) if (this[i] < min) min = this[i];
return min;
}

function preset(iscash){
	//var view_from = window.parent.$('view_from');

	//var source = $('source').value;
	var popup = $('popUp').value;
	var ptype = $('ptype').value;
	//if (view_from)
		//$('view_from').value =  view_from.value;
	//alert($F('view_from'));

	$("iscash1").focus();

	if ($('discountid').value=='SC')
		$('issc').checked = true;

	if (($('view_from').value=='ssview') || ($('view_from').value=='override'))
		$('btndiscount').style.display = "";
	else
		$('btndiscount').style.display = "none";

	iscash = $("iscash1").checked;

	if (iscash==true){
		$('is_cash').value = 1;
		$('type_charge').style.display='none';
	}else{
		$('is_cash').value = 0;
		$('type_charge').style.display='';
	}
	//$('type_charge').style.display='';

	if ($F('ordername')){
		$('clear-enc').disabled = false;
		$('btnHistory').style.display = "";
        
        if (iscash==true){
            $('btn-coverage').style.display = "none";
        }else{
            $('btn-coverage').style.display = "";
        }    
		//$('btnOther').style.display = "";
	}else{
		$('clear-enc').disabled = true;
		$('btnHistory').style.display = "none";
        $('btn-coverage').style.display = "none";
		//$('btnOther').style.display = "none";
	}

	if (($F('view_from')=='ssview')||($F('view_from')=='override')){
		$('btndiscount').style.display='';

		if ($F('view_from')=='override')
			$('override_row').style.display = "";
		else
			$('override_row').style.display = "none";
	}else{
		$('override_row').style.display = "none";
		//enableSubmitButton(1);
	}

	if (($F('hasPaid')==1)||($('repeat').checked)||($F('view_from')=='ssview')||($F('view_from')=='override')){
		$('ordername').readOnly=true;
		$('orderaddress').readOnly=true;
		$('ordername').readOnly=true;

		$('select-enc').setAttribute("onclick","");
		$('select-enc').setAttribute("class","disabled");
		$('select-enc').style.cursor='default';

		$('clear-enc').disabled = true;
		$('clear-enc').style.cursor='default';

		$('btnAdd').setAttribute("onclick","");
		$('btnAdd').setAttribute("class","disabled");
		$('btnAdd').style.cursor='default';

		$('btnEmpty').setAttribute("onclick","");
		$('btnEmpty').setAttribute("class","disabled");
		$('btnEmpty').style.cursor='default';

		$('btndiscount').disabled = true;

		//$('btnCancel').setAttribute("onclick","");
		//$('btnCancel').setAttribute("class","disabled");
		//$('btnCancel').style.cursor='default';

		$('iscash0').disabled=true;
		$('iscash1').disabled=true;

		$('comments').readOnly=true;

		document.getElementsByName('btnRefreshDiscount').disabled = true;
		document.getElementsByName('btnRefreshTotal').disabled = true;
	}

	var refno = document.getElementById('refno').value;
	var area_type = $('area_type').value;

	if ($('mode').value=='save'){
		// er patient and payward patient
		if ((area_type=='pw')||(ptype==1)||($('is_charge2comp').checked)||($('source_req').value=='RDU')){
			$("iscash1").checked = false;
			$("iscash0").checked = true;
			$('type_charge').style.display='';
		}else if (area_type=='ch'){
			$("iscash1").checked = true;
			$("iscash0").checked = false;
		}else{
			$("iscash1").checked = true;
			$("iscash0").checked = false;
		}

		setPriority(0);
	}

	if (($F('view_from')=='ssview')||($F('view_from')=='override')){
		enableSubmitButton(0);
	}else{
		enableSubmitButton(1);
	}
	//for Industrial Clinic
	if ($('source_req').value=='IC'){
		 if ($('is_charge2comp').checked){
				$('ic_row').style.display = '';
		 }else
				$('ic_row').style.display = 'none';
	}else{
		 $('ic_row').style.display = 'none';
	}

	CheckRepeatInfo();

	//added by VAN 06-02-2011
	setManualPayment();

	//alert('refno = '+refno);
	xajax_checkwithDonor(refno);
}

function CheckRepeatInfo(id){
		var isrepeat = '<?=$_GET["repeat"];?>';
		if (($('repeat').checked)||(isrepeat==1)){
			$('grant_type').value="";
			document.getElementById('repeatinfo').style.display = '';
		}else	{
			document.getElementById('repeat').disabled = true;
			document.getElementById('repeatinfo').style.display = 'none';
		}
	}

function emptyIntialRequestList(){
	clearOrder($('order-list'));
	appendOrder($('order-list'),null);
}

function initialRequestList(serv_code,grp_code,name,c_info,r_doc,r_doc_name,n_house,cash,charge,hasPaid,sservice,head,remarks,qty,qty_received,discounted_price,doc_dept,pay_type,withsample, details_rec) {
	var details = new Object();
	var withpaid = 0;
	var isrepeat = '<?=$_GET["repeat"];?>';

		details.requestDoc= r_doc;
		details.requestDocName= r_doc_name;
		details.is_in_house= n_house;
		details.clinicInfo= c_info;
		details.idGrp = grp_code;
		details.id = serv_code;
		details.qty = qty;
		details.name = name;

		details.dept = grp_code;

		details.prcCash = cash;
		details.prcCharge= charge;
		details.hasPaid = hasPaid;
		details.sservice = sservice;
		details.discounted_price = discounted_price;

		details.requestDept = doc_dept;
		details.pay_type = pay_type;
		details.net_price = discounted_price;
		details.pay_type = pay_type;
		//alert(details.pay_type);
		//details.parent_batch = parent_batch;
		details.head = head;
		details.remarks = remarks;

		details.qty_received = qty_received;
        
        details.withsample = withsample;
        details.details_rec = details_rec;

		if (($('repeat').checked)||(isrepeat==1)){
			details.discounted_price = 0;
			details.net_price = 0;
		}

		var list = document.getElementById('order-list');

		result = appendOrder(list,details);
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


function checkRequestForm(){
	var items = document.getElementsByName('items[]');
	var iscash = $("iscash1").checked;
	var ptype = $('ptype').value;

	if (iscash)
		$('is_cash').value=1;
	else
		$('is_cash').value=0;


	if (items.length==0){
		alert("Please add a request first.");
		$('btnAdd').focus();
		return false;
	}else if($F('ordername') == ''){
		alert("Please indicate the patient's name's.");
		if (iscash)
			$('ordername').focus();
		else
			$('select-enc').focus();
		return false;
	}else if($F('orderdate') == ''){
		alert("Please indicate the date of request.");
		$('orderdate').focus();
		return false;
	}else if (($('priority1').checked)&&(!$('priority0').checked)&&($('comments').value=='')){
		alert("Enter a remarks why the request should be a stat case or urgent.");
		$('comments').focus();
		return false;
	}

	if ($('repeat').checked){
		if ($('remarks').value=='') {
			alert("Enter a remarks why the request should be repeated.");
			$('remarks').focus();
			return false;
		}else if ($('approved_by_head').value=='') {
			alert("Enter a name who approved .");
			$('approved_by_head').focus();
			return false;
		}else if ($('headID').value=='') {
			alert("Enter a user ID who approved .");
			$('headID').focus();
			return false;
		}else if ($('headpasswd').value=='') {
			alert("Enter a password who approved .");
			$('headpasswd').focus();
			return false;
		}
	}

	//added by VAN 06-02-2011
	if ($('for_manual').checked){
		var btntype = valButton("for_manual_type");

		//if (btntype == null){
		if ((!$('for_manual_type1').checked)&&(!$('for_manual_type2').checked)&&
				(!$('for_manual_type3').checked)&&(!$('for_manual_type4').checked)){
			alert("Select the grant type.");
			$('for_manual_type1').focus();
			return false;
		}else if ($('manual_control_no').value=='') {
			alert("Enter the control numberm, OR number or PHIC insurance number.");
			$('manual_control_no').focus();
			return false;
		}else if ($('manual_approved').value=='') {
			alert("Enter a name who approved.");
			$('manual_approved').focus();
			return false;
		}else if ($('manual_reason').value=='') {
			alert("Enter a reason why the request payment should be manually encoded.");
			$('manual_reason').focus();
			return false;
		}
	}
	//-----------------

	$('inputform').submit();
	return true;
}

function warnClear() {
	var items = document.getElementsByName('items[]');
	if (items.length == 0) return true;
	else return confirm('Performing this action will clear the order tray. Do you wish to continue?');
}

function viewClaimStub(is_cash,refno){
		window.open("seg-claimstub.php?refno="+refno+"&is_cash="+is_cash+"&showBrowser=1","viewClaimStab","width=620,height=440,menubar=no,resizable=yes,scrollbars=yes");
}

//added by VAN 10-16-09
function viewHistory(pid,encounter_nr){
	window.open("seg-blood-request-history.php?pid="+pid+"&encounter_nr="+encounter_nr+"&ref_source=BB&showBrowser=1","viewRequestHistory","width=620,height=440,menubar=no,resizable=yes,scrollbars=yes");
}


//added by VAN 01-11-10
//only digit is allowed
function key_check(e, value){
	if((e.keyCode>=48 && e.keyCode<=57) || (e.keyCode==8) || ((e.keyCode==110)||(e.keyCode==190)) || (e.keyCode>=96 && e.keyCode<=105)){
		return true;
	}else
		return false;
}

function checkIfWalkin(){
	if (warnClear()) {
		emptyTray();
		if ($('is_walkin').checked){
			$('discount2').value = $('discount').value;
			$('orig_discountid').value = $('discountid').value;
			$('discountid').value = '';

			if ($('issc').checked){
				$('discountid').value = 'SC';
				//should be taken from the database temporary only
				$('discount').value = 0.20;
			}else
				$('discount').value = 0;
			refreshDiscount();
		}else{
			$('discount').value = $('discount2').value;
			$('discountid').value = $('orig_discountid').value;
		}
	}
}

function resetValue(){
	var items = document.getElementsByName('items[]');
	var net = document.getElementsByName('pnet[]');
	var netbc = document.getElementsByName('pnetbc[]');

	nettotal = 0;
	if ($('show-discount').value!=""){
		$('show-discount').value = formatNumber(nettotal,2);
		for (var i=0;i<items.length;i++) {
			id = items[i].value;
			net[i].value = netbc[i].value;
			amount = $('rowPrcNetbc'+id).value;
			$('tot'+id).innerHTML = formatNumber(Math.round(amount).toFixed(2),2);
		}
		refreshDiscount();
	}
}

function clearValue(){
	$('show-discount').value= "";
	$('is_free').checked = false;

	resetValue();
}

function computeDiscount(discount_amt){
	 var items = document.getElementsByName('items[]');
	 var net = document.getElementsByName('pnet[]');
	 var netbc = document.getElementsByName('pnetbc[]');
	 var qty = document.getElementsByName('qty[]');
	 var val, nettotal, price_per_service, final_net, netamt, pricelist, discount_given, amount;
	 var no_item=0;

	 if (!($('is_free').checked)&&(($('show-discount').value=="")||($('show-discount').value==0.00))){
		$('show-discount').value = formatNumber(nettotal,2);
		for (var i=0;i<items.length;i++) {
			id = items[i].value;
			net[i].value = netbc[i].value;
			amount = $('rowPrcNetbc'+id).value;
			$('tot'+id).innerHTML = formatNumber(Math.round(amount).toFixed(2),2);
		}
		refreshDiscount();
	}else{
		 discount_given = discount_amt.replace(",","");
		 discount_given = parseFloat(discount_given);
		 nettotal = $('show-net-total').innerHTML;
		 nettotal = parseFloat(nettotal);

		 if (items){
			 pricelist = new Array();
			 netamt = 0;
			 for (var i=0;i<items.length;i++) {
					pricelist[i] = parseFloat(netbc[i].value);

					if (netbc[i].value > 0){
						no_item += 1;
						netamt = netamt + (parseFloat(netbc[i].value)*qty[i].value);
					}
			 }

			 netamt = parseFloat(netamt);

			 if (discount_given > netamt){
					 alert('The discount given is MORE than the Net Total (OR payable amount)');
					 for (var i=0;i<items.length;i++) {
							id = items[i].value;
							net[i].value = netbc[i].value;
							$('discountedprc'+id).innerHTML = formatNumber(Math.round(netbc[i].value).toFixed(2),2);
							amount = $('rowPrcNetbc'+id).value * qty[i].value;
							$('tot'+id).innerHTML = formatNumber(Math.round(amount).toFixed(2),2);
					 }
					 $('show-discount').value = '0.00';
					 $('show-discount').focus();
					 refreshDiscount();
			 }else{
					 if (discount_given > 0){
							 price_per_service =  (netamt - discount_given/parseInt(no_item));

							 if (price_per_service > pricelist.min()){
									final_net = (netamt- discount_given) / netamt;
									withdis = 1;
							 }else{
									withdis = 0;
							 }

							 for (var i=0;i<items.length;i++) {
									 id = items[i].value;

									 if (withdis==0){
											//price_per_service = Math.round(parseFloat(price_per_service)*100)/100;
											price_per_service = Math.round(parseFloat(price_per_service));
											amount = price_per_service * parseFloat($('rowQty'+id).value);
											price_per_service = formatNumber(price_per_service.toFixed(2),2);
											net[i].value = price_per_service.replace(",","");
											$('discountedprc'+id).innerHTML = price_per_service;
											//amount = Math.round(amount*100)/100;
											amount = Math.round(amount);
											$('tot'+id).innerHTML = formatNumber(amount.toFixed(2),2);
									 }else{
											discountprice = netbc[i].value * final_net;
											//discountprice = Math.round(parseFloat(discountprice)*100)/100;
											discountprice = Math.round(parseFloat(discountprice));
											amount = discountprice * parseFloat($('rowQty'+id).value);
											discountprice = formatNumber(discountprice.toFixed(2),2);
											net[i].value = discountprice.replace(",","");
											$('discountedprc'+id).innerHTML = discountprice;
											//amount = Math.round(amount*100)/100;
											amount = Math.round(amount);
											$('tot'+id).innerHTML = formatNumber(amount.toFixed(2),2);
									 }
							 }
							 refreshDiscount();
					 }
			 }
		 }

		 //refreshDiscount();
	}
}

function formatDiscount(valamount){
		document.getElementById('show-discount').value = formatNumber(valamount,2);
}

function setDiscount(){
	var nettotal=0;
	var items = document.getElementsByName('items[]');
	var net = document.getElementsByName('pnet[]');
	var netbc = document.getElementsByName('pnetbc[]');
	var qty = document.getElementsByName('qty[]');

	if ($('is_free').checked){
		for (var i=0;i<items.length;i++) {
			id = items[i].value;
			nettotal = nettotal +  (parseFloat(netbc[i].value)*qty[i].value);
		}

		$('show-discount').value = nettotal;
		computeDiscount($('show-discount').value);
		formatDiscount($('show-discount').value);
	}else{
		nettotal = 0;
		$('show-discount').value = formatNumber(nettotal,2);
		resetValue();
	}
}

function keyEnter(e,d){
	if (e.keyCode == 13){
		adjustQty(d);
	}else{
		return false;
	}
}

function enableSubmitButton(isenable){
		if (isenable){
			$('btnSubmit').setAttribute("class","");
			$('btnSubmit').style.cursor='pointer';
			$('btnSubmit').setAttribute("onclick","if (confirm(\'Process this request?\')) if (checkRequestForm()) document.inputform.submit()");
		}else{
			$('btnSubmit').setAttribute("class","disabled");
			$('btnSubmit').style.cursor='default';
			$('btnSubmit').setAttribute("onclick","");
		}
}

function setPriority(isUrgent){
		if (isUrgent){
			 $('priority1').checked = true;
			 $('priority0').checked = false;
		}else{
			 $('priority1').checked = false;
			 $('priority0').checked = true;
		}
}

function adjustQty(obj) {
	var id = obj.getAttribute("itemID");
	if (isNaN(obj.value)) {
		obj.value = obj.getAttribute("prevValue");
		return false;
	}
	if (parseFloatEx(obj.value) != parseFloatEx(obj.getAttribute("prevValue"))) {
		$('tot'+id).innerHTML = formatNumber(parseFloatEx($('rowPrcNet'+id).value)*parseFloatEx($('rowQty'+id).value),2);
		refreshDiscount();
	}
	obj.setAttribute("prevValue",parseFloatEx(obj.value));
	return true;
}

function checkPriority(){
	var area_type = $('area_type').value;

	if (area_type!='pw'){
		if (warnClear()) {
			emptyTray();
		}
	}
}

function definePriority(){
	 is_rdu = $('is_rdu').checked;
	 var currenttime = $('currenttime').value;

	 if ((parseInt(currenttime) <= 5)||(parseInt(currenttime) >= 23)){
		 if (is_rdu)
				setPriority(0);
		 else
				setPriority(1);
	 }
}

//added by VAN 06-02-2011
// for temporary workaround
function enablePhic(){
	if ($('is_rdu').checked){
		$('for_manual_type4').disabled = false;
	}else{
		$('for_manual_type4').disabled = true;
		$('for_manual_type4').checked = false;
	}
}

function setManualPayment(){

	if ($('for_manual').checked){
		$('manual').style.display = '';
		$('for_manual_payment').value = 1;
	}else{
		$('manual').style.display = 'none';
		$('for_manual_payment').value = 0;
	}

	if ($('iscash0').checked){
		$('for_manual').disabled = true;
	}else
		$('for_manual').disabled = false;

	enablePhic();
}

function setLabel(){
	 var or_label = "OR Number";
	 var control_label = "Control Number";
	 var phic_label = "PHIC Number";


	 if ($('for_manual_type1').checked)
			$('label_manual').innerHTML = or_label;
	 else if ($('for_manual_type2').checked)
			$('label_manual').innerHTML = control_label;
	 else if ($('for_manual_type3').checked)
			$('label_manual').innerHTML = control_label;
	 else if ($('for_manual_type4').checked)
			$('label_manual').innerHTML = phic_label;
	 else
			$('label_manual').innerHTML = control_label;
}

function valButton(btn) {
	var cnt = -1;
	var temp = document.getElementsByName(btn);
	if (!$(btn))	{
		return null;
	}

	for (var i=temp.length-1; i > -1; i--) {
		if (temp[i].checked) {
			cnt = i;
			i = -1;
		}
		}

	if (cnt > -1) return temp[cnt].value;
		else return null;
}      

//--------------------------

//added by VAS 03-21-2012
function changeChargeType() {
    if (!$("iscash1").checked) {
        updateCoverage([$('encounter_nr').value]);
    }    
    
    refreshDiscount();
}

function changeHact(){
    // flag the hact patient
    if ($(grant_type).value=='hact')
        $('is_hact').checked = true;
    else
        $('is_hact').checked = false;    
}

function updateCoverage( param ) {
    if (!param[0]) {
        //$('cov_type').update('Coverage:');
        $('cov_amount').update('');
        $('coverage').setAttribute('value',-1);
        return false;
    }
    
    var ctype = $('grant_type').value;
    var nr = $('refno').value;
    param.push(ctype);
    param.push(nr);
    
    if (ctype=='phic') {  //phic
        $('cov_type').hide();
        $('cov_amount').hide();
        $('phic_ajax').show();
        $('cov_type').update('PHIC Coverage:');
        xajax.call('updateCoverage', {
            parameters : param,
            onError: function(transport) {
                $('phic_ajax').hide();
                $('cov_type').show();
                $('cov_amount').show();
            },
            onSuccess : function(transport) {
                $('phic_ajax').hide();
                $('cov_type').show();
                $('cov_amount').show();
            }
        });
    }
    else {  //other charge type 
        $('cov_type').update('');
        $('cov_amount').update('');
        $('coverage').setAttribute('value',-1);
        $('phic_ajax').hide();
        //$('cov_type').hide();
        //$('cov_amount').hide();
    }
}

function updatePHICCoverage( param ) {
    $('phic_cov').hide();
    $('phic_ajax').show();
    xajax.call('updatePHICCoverage', {
        parameters : param,
        onError: function(transport) {
            $('phic_ajax').hide();
            $('phic_cov').show();
        },
        onSuccess : function(transport) {
            $('phic_ajax').hide();
            $('phic_cov').show();
        }
    });
}

function openCoverages() {
        var enc_nr = $('encounter_nr').value;
        var userck = '<?= $userck; ?>';
        if (enc_nr) {
            var url = '../../modules/insurance_co/seg_coverage_editor.php?userck='+userck+'&encounter_nr='+enc_nr+'&from=CLOSE_WINDOW&force=1';
            overlib(
                OLiframeContent(url, 740, 400, 'fCoverages', 0, 'auto'),
                WIDTH,600, TEXTPADDING,0, BORDER,0,
                STICKY, SCROLL, CLOSECLICK, MODAL,
                CLOSETEXT, '<img src=../../images/close_red.gif border=0 >',
                CAPTIONPADDING,2,
                CAPTION,'Insurance coverages',
                MIDX,0, MIDY,0,
                STATUS,'Insurance coverages');
        }
        else {
            alert('No patient with confinement case selected...');
        }
        return false
    }
//-------------

function refreshWindow(){
    window.location.reload();    
}

//added by Nick, 1/15/2014
var pending_info = "";
var old_db_data = "";
var new_db_data = "";
var old_ui_data = "";
var new_ui_data = "";
var req_info;

function getWarnings(_old,_new,log){
    var pendings = 0;

    if(_old!=null)
        old_val = getDataInArray(_old);
    if(_new!=null)
        new_val = getDataInArray(_new);

    for(x=0;x<=old_val.length-1;x++){
        if(old_val[x][0]=="true"){
        	//serial
            /*if(old_val[x][1]=="PENDING" && new_val[x][1]=="PENDING"){
            	if(src=="db")
            		pending_info += "\nUNSUBMITTED - LINE "+(parseInt(x)+1)+":SERIAL";
            	else
            		pending_info += "\nUNSAVED CHANGES - LINE "+(parseInt(x)+1)+":SERIAL";
                pendings++;
            }*/
            //component
            if(old_val[x][2]=="PENDING" && new_val[x][2]=="PENDING"){
            	if(log)
            		pending_info += "\nUNSUBMITTED - LINE "+(parseInt(x)+1)+":COMPONENT";
                pendings++;
			}
            //date received
            if(old_val[x][3]=="PENDING" && new_val[x][3]=="PENDING"){
            	if(log)
            		pending_info += "\nUNSUBMITTED - LINE "+(parseInt(x)+1)+":DATE RECEIVED";
                pendings++;
            }
            //date received - date done
            if(old_val[x][3]!="PENDING" && new_val[x][4]=="PENDING"){
            	if(log)
            		pending_info += "\nUNSUBMITTED - LINE "+(parseInt(x)+1)+":DATE DONE";
                pendings++;
            }
            //date done - date issuance
            if(old_val[x][4]!="PENDING" && new_val[x][5]=="PENDING"){
            	if(log)
            		pending_info += "\nUNSUBMITTED - LINE "+(parseInt(x)+1)+":DATE ISSUANCE";
                pendings++;
            }
            //date issuance - date consumed
            if(old_val[x][5]!="PENDING"/* && new_val[x][8]=="PENDING"*/){

            	//date returned - date reissue
            	if(old_val[x][6]!="PENDING" && new_val[x][7]=="PENDING"){
            		if(log)
            			pending_info += "\nUNSUBMITTED - LINE "+(parseInt(x)+1)+":DATE REISSUE";
            		pendings++;
            	}else if(old_val[x][7]!="PENDING" && new_val[x][8]=="PENDING"){
	        		if(log)
						pending_info += "\nUNSUBMITTED - LINE "+(parseInt(x)+1)+":DATE CONSUMED";
					pendings++;
            	}else if((old_val[x][6]=="PENDING" && new_val[x][6]=="PENDING") && new_val[x][8]=="PENDING"){
            		if(log){
            			pending_info += "\nUNSUBMITTED - LINE "+(parseInt(x)+1)+":DATE CONSUMED";
                	}
                	pendings++;
            	}

            }
            //date issuance - date consumed
            /*if(old_val[x][5]!="PENDING" && new_val[x][8]=="PENDING"){
            	pending_info += "\nUNSUBMITTED - LINE "+(parseInt(x)+1)+":DATE CONSUMED";
                pendings++;
            }*/
            //date returned - date reissue
            /*if(old_val[x][6]!="PENDING" && new_val[x][7]=="PENDING"){
            	pending_info += "\nUNSUBMITTED - LINE "+(parseInt(x)+1)+":DATE REISSUE";
                pendings++;
            }*/

        }
    }
    console.log("PENDINGS: " + pendings + pending_info);
    // return pendings;
    return (pendings > 0) ? true:false;
}

function getDataInArray(str){
    var str_arr = new Array();
    var temp = "";

    str_arr = str.split("\n");

    for(i=0;i<=str_arr.length-1;i++){
        try{
            temp = str_arr[i];
            str_arr[i] = temp.split(",");
        }catch(err){
            temp = "";
        }
    }
    return str_arr;
}

function set_old_db_data(data){
	old_db_data = data;
}

function set_new_db_data(data){
	new_db_data = data;
}

function get_old_ui_data(){

	old_ui_data = "";

	var i = 1;
	while(i <= req_info.qty){

		old_ui_data += $J("#bbsamples_frame").contents().find("#is_received"+req_info.testcode+i).is(":checked") + ",";
		old_ui_data += $J("#bbsamples_frame").contents().find("#serial"+req_info.testcode+i).val() + ",";
		old_ui_data += $J("#bbsamples_frame").contents().find("#component"+req_info.testcode+i).val() + ",";
		old_ui_data += "\n";

		i++;
	
	}

	return old_ui_data;
}

function get_ui_data(){

	var data = "";

	var i = 1;
	while(i <= req_info.qty){

		var is_received = $J("#bbsamples_frame").contents().find("#is_received"+req_info.testcode+i).is(":checked");
		var serial = $J("#bbsamples_frame").contents().find("#serial"+req_info.testcode+i).val();
		var component = $J("#bbsamples_frame").contents().find("#component"+req_info.testcode+i).val();
		data += is_received + ",";
		data += ((serial == "") ? "PENDING":serial) + ",";
		data += ((component == "") ? "PENDING":component) + ",";

		/* RECEIVED DATE */
		if( $J("#bbsamples_frame").contents().find("#date_received"+req_info.testcode+i).val() != "" )
			rec_dt = $J("#bbsamples_frame").contents().find("#date_received"+req_info.testcode+i).val();
		else
			rec_dt = "00/00/0000";
		if( $J("#bbsamples_frame").contents().find("#time_received"+req_info.testcode+i).val() != "" )
			rec_tm = $J("#bbsamples_frame").contents().find("#time_received"+req_info.testcode+i).val();
		else
			rec_tm = "00:00";
		rec_md = $J("#bbsamples_frame").contents().find("#meridian"+req_info.testcode+i).val();
		data += getDateTime(rec_dt,rec_tm,rec_md) + ",";
		/* DONE DATE */
		if( $J("#bbsamples_frame").contents().find("#date_done"+req_info.testcode+i).val() != "" )
			done_dt = $J("#bbsamples_frame").contents().find("#date_done"+req_info.testcode+i).val();
		else
			done_dt = "00/00/0000";
		if( $J("#bbsamples_frame").contents().find("#time_done"+req_info.testcode+i).val() != "" )
			done_tm = $J("#bbsamples_frame").contents().find("#time_done"+req_info.testcode+i).val();
		else
			done_tm = "00:00";
		done_md = $J("#bbsamples_frame").contents().find("#done_meridian"+req_info.testcode+i).val();
		data += getDateTime(done_dt,done_tm,done_md) + ",";
		/* ISSUANCE DATE */
		if( $J("#bbsamples_frame").contents().find("#date_issuance"+req_info.testcode+i).val() != "" )
			issuance_dt = $J("#bbsamples_frame").contents().find("#date_issuance"+req_info.testcode+i).val();
		else
			issuance_dt = "00/00/0000";
		if( $J("#bbsamples_frame").contents().find("#time_issuance"+req_info.testcode+i).val() != "" )
			issuance_tm = $J("#bbsamples_frame").contents().find("#time_issuance"+req_info.testcode+i).val();
		else
			issuance_tm = "00:00";
		issuance_md = $J("#bbsamples_frame").contents().find("#issuance_meridian"+req_info.testcode+i).val();
		data += getDateTime(issuance_dt,issuance_tm,issuance_md) + ",";
		/* RETURN DATE */
		if( $J("#bbsamples_frame").contents().find("#date_returned"+req_info.testcode+i).val() != "" )
			return_dt = $J("#bbsamples_frame").contents().find("#date_returned"+req_info.testcode+i).val();
		else
			return_dt = "00/00/0000";
		if( $J("#bbsamples_frame").contents().find("#time_returned"+req_info.testcode+i).val() != "" )
			return_tm = $J("#bbsamples_frame").contents().find("#time_returned"+req_info.testcode+i).val();
		else
			return_tm = "00:00";
		return_md = $J("#bbsamples_frame").contents().find("#returned_meridian"+req_info.testcode+i).val();
		data += getDateTime(return_dt,return_tm,return_md) + ",";
		/* REISSUE DATE */
		if( $J("#bbsamples_frame").contents().find("#date_reissue"+req_info.testcode+i).val() != "" )
			reissue_dt = $J("#bbsamples_frame").contents().find("#date_reissue"+req_info.testcode+i).val();
		else
			reissue_dt = "00/00/0000";
		if( $J("#bbsamples_frame").contents().find("#time_reissue"+req_info.testcode+i).val() != "" )
			reissue_tm = $J("#bbsamples_frame").contents().find("#time_reissue"+req_info.testcode+i).val();
		else
			reissue_tm = "00:00";
		reissue_md = $J("#bbsamples_frame").contents().find("#reissue_meridian"+req_info.testcode+i).val();
		data += getDateTime(reissue_dt,reissue_tm,reissue_md) + ",";
		/* CONSUMED DATE */
		if( $J("#bbsamples_frame").contents().find("#date_consumed"+req_info.testcode+i).val() != "" )
			consumed_dt = $J("#bbsamples_frame").contents().find("#date_consumed"+req_info.testcode+i).val();
		else
			consumed_dt = "00/00/0000";
		if( $J("#bbsamples_frame").contents().find("#time_consumed"+req_info.testcode+i).val() != "" )
			consumed_tm = $J("#bbsamples_frame").contents().find("#time_consumed"+req_info.testcode+i).val();
		else
			consumed_tm = "00:00";
		consumed_md = $J("#bbsamples_frame").contents().find("#consumed_meridian"+req_info.testcode+i).val();
		data += getDateTime(consumed_dt,consumed_tm,consumed_md) + ",";

		data += "\n";

		i++;
	}

	return data;
}

function getDateTime(date,time,meridian){
	var date_array = date.split("/");
	var time_array = time.split(":");
	var month,day,year,hour,minute,seconds,datetime;

	month = date_array[0];
	day = date_array[1];
	year = date_array[2];

	hour = (meridian == "PM") ? parseInt(time_array[0])+12:time_array[0];
	minute = time_array[1];
	seconds = "00";

	datetime = year + "-" + month + "-" + day + " " + hour + ":" + minute + ":" + seconds;

	if(datetime == "0000-00-00 00:00:00")
		datetime = "PENDING";

	return datetime;
}

function getRequestInfo(qty,code){
	var data = new Object();

	data.qty = qty;
	data.testcode = code;

	req_info = data;
}
//end nick

var fromWarning = false;
var bbsamples_dialog;

function viewReceivedSample(service_code, refno){
	//commented by Nick, 1/14/2014
    /*return overlib(
                    OLiframeContent("seg-blood-received-sample.php?user_origin=blood&popUp=1&viewonly=1&refno="+refno+"&service_code="+service_code, 850, 400, "fOrderTray", 1, "auto"),
                                    WIDTH,850, TEXTPADDING,0, BORDER,0,
                                    STICKY, SCROLL, CLOSECLICK, MODAL,
                                    CLOSETEXT, "<img src=../../images/close.gif border=0 onClick=refreshWindow();>",
                                    CAPTIONPADDING,4, CAPTION,"Blood Sample Received",
                                    MIDX,0, MIDY,0,
                                    STATUS,"Blood Sample Received");*/
	
	//added by Nick, 1/14/2014
	var url = "seg-blood-received-sample.php?user_origin=blood&popUp=1&viewonly=1&refno="+refno+"&service_code="+service_code;	
	bbsamples_dialog = $J('<div id="bbsamples_dialog"></div>')
		.html('<iframe id="bbsamples_frame" style="width:100%;height:100%" src="'+url+'"></iframe>')
		.dialog({
			autoOpen:true,
			title:"Blood Sample Received",
			open:function(){
				fromWarning = false;
				xajax_setDbData(refno,service_code,0);
				$J("#bbsamples_frame").load(function(){
					// new_ui_data = get_ui_data();
				});
			},
			width:"95%",
			height:400,
			modal:true,
			beforeClose:function(){
				if(fromWarning == false)
					xajax_setDbData(refno,service_code,1);
				// old_ui_data = get_ui_data();
				return fromWarning;
			}//b4close
			
		});
		//end Nick
}

//added by Nick, 1/17/2014
function getPendings(){
	pending_info = "";
 	db_warnings = getWarnings(old_db_data,new_db_data,true);
	// ui_warnings = getWarnings(new_ui_data,new_ui_data,false);

	// console.log("db_warnings: " + db_warnings + "\n" + 
	// 	        "ui_warnings: " + ui_warnings);

	if(db_warnings/* || ui_warnings*/)
		showWarning(true);
	else
		showWarning(false);
}
//end nick

//added by Nick, 1/14/2014
function showWarning(hasPending){
	
	console.log("DBOLD:\n" + old_db_data + "\n\nDBNEW:\n" + new_db_data);
	
	if(hasPending && !fromWarning){
		var alert_string = '<p align="center" style="color:red;font:14"><b>DATA NOT SUBMITTED<br><span id="info" title="PENDINGS:'+pending_info+'">SUBMIT!</span><br><br></b><span style="color:#000000" align="center">Click \"Continue\" else \"Cancel\"</span></p>';
		var alertbox = $J('<div id="alertbox"></div>')
		.html(alert_string)
		.dialog({
			autoOpen:true,
			title:"WARNING!",
			modal:true,
			buttons:{
				Continue:function(){
					fromWarning = true;
					$J(this).dialog("close");
					bbsamples_dialog.dialog("close");
				},
				Cancel:function(){
					$J(this).dialog("close");
				}
			}

		});
	}else{
		fromWarning = true;
		$J(this).dialog("close");
		bbsamples_dialog.dialog("close");
	}
}
//end nick
//added by VAN 03-13-2013
//fixed for bug id 110
function validatePHIC(){
    //only on blood unit products and blood test is not included
    /*if (!$("iscash1").checked) {
        if($J('#grant_type').val()=="phic") {
            var phic_nr = $J('#phic_nr').html();
            phic_nr = phic_nr.replace(/-/g,'');
            
            //if phic is temporarary or not the right format of phic number which is PHIC nr has a 16 digit format
            //if ((phic_nr.toLowerCase().match('temp')=='temp') || (phic_nr.length!=12)){
            if (phic_nr.toLowerCase().match('temp')=='temp'){
                return false;
            }else
                return true;
        }else{
            return true;
        }
    }else{
        return true;                    
    }*/ 
    return true;   
}