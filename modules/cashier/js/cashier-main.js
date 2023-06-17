var isLoading=0;
var vatable;
var vat_amount = 0;
function startLoading() {
	if (!isLoading) {
		isLoading = 1;
		return overlib('Loading items...<br><img src="../../images/ajax_bar.gif">',
			WIDTH,300, TEXTPADDING,5, BORDER,0,
			STICKY, SCROLL, CLOSECLICK, MODAL,
			NOCLOSE, TIMEOUT, 10000, OFFDELAY, 10000,
			CAPTION,'Loading',
			MIDX,0, MIDY,0,
			STATUS,'Loading');
	}
}

function doneLoading() {
	if (isLoading) {
		setTimeout('cClick()', 500);
		isLoading = 0;
	}
}

function warn(msg, state) {
	$('orno').setAttribute('orOk',state);
	$('orno').style.color = (state==1) ? '#000080' : '#f00000';
	$('warnicon').style.display = (state==1) ? 'none' : '';
	$('okicon').style.display = (state==1) ? '' : 'none';
	$('warn-text').value = msg;
	setTimeout("$('orno').readOnly = false",400);
}

function showWarning() {
	if (($('warn-text').value)) {
		return overlib('<span style="font:bold 11px Tahoma;color:#252223">'+($('warn-text').value)+'</span>',WRAP,0, REFX,6, REF,'warn-icon', REFP,'UL', REFC,'UR', HAUTO,VAUTO,FGCOLOR,'#f7dd11',BGCOLOR,'#b75d13',FGCLASS,'',BGCLASS,'');
	}
	return false;
}

function showOk() {
	if (($('warn-text').value)) {
		return overlib('<span style="font:bold 11px Tahoma;color:#ffffff">'+($('warn-text').value)+'</span>',WRAP,0, REFX,6, REF,'warn-icon', REFP,'UL', REFC,'UR', HAUTO,VAUTO,FGCOLOR,'#2fd800',BGCOLOR,'#134e07',FGCLASS,'',BGCLASS,'');
	}
	return false;
}

function clearRequests() {
	$('request_dashlet').update();
	var nodes = $$('[name="requests[]"]');
	if (nodes) {
		nodes.each (
			function(x) {
				var id, src, nr;

				if ('undefined' !== typeof x.value)
					id=x.value;
				else
					id = x.id;

				src=id.substr(0,2);
				switch (src) {
					case 'fb','pp':
						nr=id.substr(2);
						clearList(src,nr)
					break;
					case 'ot':
						src=id.substr(0,5);
						nr=id.substr(5);
						clearList(src,nr)
					break;
					default:
					break;
				}
			}
		);
	}
}

/* Controls for Deposit */
function openDeposit() {
	var id = $('pid').value;
	if (!id) {
		alert('No patient ID selected...');
		return false;
	}else{
	details = new Object;
	details.id = 'DEPOSIT';
	details.name = 'Deposit:Hospital Fees';
	details.desc = 'Deposit';
	details.qty = 1;
	price=0;
	while (isNaN(parseFloat(price)) || parseFloat(price)<=0) {
		price = prompt("Set amount for partial payment:")
		if (price === null) return false;
	}
	details.origprice = price;
	details.price = price;
	details.ispaid = 0;
	details.checked= 1;
	details.showdel= 1;
	details.calculate= 1;
	details.doreplace = 1;
	details.limit= -1;
	details.src = 'pp';
	details.ref = '0000000000';
	result = addServiceToList(details);
	return result;
}
}

//added by jasper 05/29/2013 FIX FOR OBANNEX co-payments BUG#279
function openOBAnnexCharge() {
    var id = $('pid').value;
	if (!id) {
		alert('No patient ID selected...');
		return false;
	}else{
	    details = new Object;
	    details.id = 'OBANNEX';
	    details.name = 'OB Annex:Hospital Fees';
	    details.desc = 'OB Annex Charge';
	    details.qty = 1;
	    price=0;
	    while (isNaN(parseFloat(price)) || parseFloat(price)<=0) {
	        price = prompt("Set amount for OB Annex Co-payment:")
	        if (price === null) return false;
	    }
	    details.origprice = price;
	    details.price = price;
	    details.ispaid = 0;
	    details.checked= 1;
	    details.showdel= 1;
	    details.calculate= 1;
	    details.doreplace = 1;
	    details.limit= -1;
	    details.src = 'pp';
	    details.ref = '0000000000';
	    result = addServiceToList(details);
	    return result;
	}
}
//added by jasper 05/29/2013 FIX FOR OBANNEX co-payments BUG#279

/* Controls for Other hospital services */
function openServices(nr) {
	var id = $('pid').value;
	if (!id && nr) {
		alert('No patient ID selected...');
		return false;
	}else{
	if (!nr) nr='';
	return overlib(OLiframeContent('seg-cashier-hospital-services.php?type='+nr, 600, 340, 'fMiscFees', 0, 'no'),
		WIDTH,600, TEXTPADDING,0, BORDER,0,
		STICKY, SCROLL, CLOSECLICK, MODAL,
		CLOSETEXT, '<img src=../../images/close_red.gif border=0 >',
		CAPTION,'Add Hospital Service',
		MIDX,0, MIDY,0,
		STATUS,'Other hospital services');
}
}

/* Controls for Billing */
function openBilling() {
	var id = $('pid').value;
	if (!id) {
		alert('No patient ID selected...');
		return false;
	}
	else {
		return overlib(OLiframeContent('seg-cashier-main-billinglist.php?or='+theORNo+'&patient='+id, 640, 380, 'fMiscFees', 0, 'auto'),
			WIDTH,600, TEXTPADDING,0, BORDER,0,
			STICKY, SCROLL, CLOSECLICK, MODAL,
			CLOSETEXT, '<img src=../../images/close_red.gif border=0 >',
			CAPTION,'Select Billing Encounter',
			MIDX,0, MIDY,0,
			STATUS,'Select Billing Encounter');
	}
}

/* Controls for Billing */
function openRequest() {
	var id = $('pid').value;
	var isvat = $('isvat').value;
	if (!id) {
		alert('No patient ID selected...');
		return false;
	}else{
	var prid='', prname='';
	if ($('pid')) prid = $('pid').value;
	if ($('orname')) prname = $('orname').value;
	return overlib(OLiframeContent('seg-cashier-requests.php?or='+(theORNo ? theORNo : '')+'&mode=payorrequest&prid='+escape(prid)+'&prname='+escape(prname)+'&is_vatable='+isvat, 760, 420, 'fMiscFees', 0, 'auto'),
		WIDTH,600, TEXTPADDING,0, BORDER,0,
		STICKY, SCROLL, CLOSECLICK, MODAL,
		CLOSETEXT, '<img src=../../images/close_red.gif border=0 >',
		CAPTION,'Select Request',
		MIDX,0, MIDY,0,
		STATUS,'Select Request');
}
}

/* Controls for Billing */
function openRequestFromSocial() {
	var id = $('pid').value;
	if (!id) {
		alert('No patient ID selected...');
		return false;
	}else{
		var prid='', prname='';
		if ($('pid')) prid = $('pid').value;
		if ($('orname')) prname = $('orname').value;
		return overlib(OLiframeContent('seg-cashier-requests.php?or='+(theORNo ? theORNo : '')+'&mode=payorrequest&prid='+escape(prid)+'&prname='+escape(prname)+'&fromsocial=1', 760, 420, 'fMiscFees', 0, 'auto'),
			WIDTH,600, TEXTPADDING,0, BORDER,0,
			STICKY, SCROLL, CLOSECLICK, MODAL,
			CLOSETEXT, '<img src=../../images/close_red.gif border=0 >',
			CAPTION,'Select Request',
			MIDX,0, MIDY,0,
			STATUS,'Select Request');
	}
}

function clearEncounter() {
	if ($('orname')) {
			$('orname').value="";
		//$('orname').readOnly=false;
	}
	if ($('oraddress')) {
		$('oraddress').value="";
		//$('oraddress').readOnly=false;
	}
	if ($('pid'))
		$('pid').value="";
	if ($('encounter_nr'))
		$('encounter_nr').value="";
	if ($('clear-enc')) {
		$('clear-enc').disabled = true;
		$('clear-enc').disabled = true;
	}
	if ($('select-enc'))
		$('select-enc').className = 'link';
	if ($('sw-class'))
		$('sw-class').innerHTML = 'None';

	if ($('encounter_type_show'))
		$('encounter_type_show').update('NONE');
	if ($('encounter_type')) $('encounter_type').value = '';

	clearRequests();
	refreshTotal();
}

function addSlashes(str) {
	var ret = str.replace('"','\\"');
	return ret.replace("'","\\'");
}

function parseFloatEx(x) {
	var str = x.toString().replace(/\,|\s/,'')
	return parseFloat(str)
}

function formatNumber(num,dec) {
	var nf = new NumberFormat(num);
	if (isNaN(dec)) dec = nf.NO_ROUNDING;
	nf.setPlaces(dec);
	return nf.toFormatted();
}

function reclassRows(list, startIndex) {
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

function addPartialPayment() {
	var id = $('pid').value;
	if (!id) {
		alert('No patient ID selected...');
		return false;
	}else{
	details = new Object;
	details.id = 'PARTIAL';
	details.name = 'Partial payment';
	details.desc = 'Deposit';
	details.qty = 1;
	price=0;
	while (isNaN(parseFloat(price)) || parseFloat(price)<=0) {
		price = prompt("Set amount for partial payment:")
		if (price === null) return false;
	}
	details.origprice = price;
	details.price = price;
	details.ispaid = 0;
	details.checked= 1;
	details.showdel= 1;
	details.calculate= 1;
	details.doreplace = 1;
	details.limit= -1;
	details.src = 'pp';
	details.ref = '0000000000';
	result = addServiceToList(details);
	return result;
}
}

function flagCheckBoxesByName(name, flag) {
	var items = document.getElementsByName(name);
	var node, disabled;
	for (var i=0; i<items.length; i++)
		if (items[i].type.toLowerCase()=='checkbox') {
			node = $('row_'+items[i].id);
			if (!items[i].disabled){
				items[i].checked = flag;
				//Added by Jarel 11/12/2013 set element to disabled
				if(flag==true){
					disabled = false;
				}else{
					disabled = true;
				}
				disableChildrenInputs(node,disabled);
				//End Jarel
			}
	
		}
	refreshTotal();
}

function removeReference(src, ref) {
	var node=$(src+ref);
	if (node) {
		node.parentNode.removeChild(node);
		return true;
	}
	return false;
}

function emptyTray(src, ref) {
	clearList(src,ref);
	addServiceToList(src,ref,null);
	refreshTotal()
	refreshAmountChange()
}

function clearList(src, ref) {
	// Search for the source row table element
	var listID = 'list_'+src+ref;
	var list=$(listID),dRows, dBody;
	if (list) {
		dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			dBody.innerHTML = "";
			details = new Object;
			details.src = src;
			details.ref = ref;
			addServiceToList(details);
			return true;	// success
		}
		else return false;	// fail
	}
	else return false;	// fail
}

function removeServiceFromList(src, ref, id) {
	var destTable, destRows;
	var table = $('list_'+src+ref);
	var rmvRow=document.getElementById("row_"+src+ref+id);
	if (table && rmvRow) {
		var rndx = rmvRow.rowIndex-1;
		table.deleteRow(rmvRow.rowIndex);
		if (!document.getElementsByName(src+ref+"[]") || document.getElementsByName(src+ref+"[]").length <= 0) {
			details = new Object;
			details.src = src;
			details.ref = ref;
			addServiceToList(details);
		}
		reclassRows(table,rndx);
	}
	refreshTotal();
}
//added by ken for splitting doctor pf 2/18/2014
function addServiceToList1(details) {
	details = Object.extend({
		src: '',
		ref: '',
		id:'',
		name: '',
		desc: '',
		price: 0,
		hospital: 0,
		doctor: 0,
		qty: 0,
		flag: '',
		dflag: '',
		origprice: 0,
		limit: -1,
		checked: 0,
		showdel: 0,
		calculate: 0,
		replace: 0
	}, details || {});
	var src = details.src;
	var ref = details.ref;
	var i, srcRef = src.toLowerCase()+ref;
	var list=$('list_'+srcRef), dRows, dBody, rowSrc;
	var temp_id = '';
	for(var count = 0; count <= 1; count++){
		if (list) {
			dBody=list.getElementsByTagName("tbody")[0];
			dRows=dBody.getElementsByTagName("tr");

			if (details.id) {
				var _id = details.id,
	                _origID = details.id, //added by jasper 05/29/2013
						_name = details.name,
						_desc = details.desc,
						_price = details.price,
						_hospital = details.hospital,
						_doctor = details.doctor,
						_qty = details.qty,
						_total = parseFloatEx(_price) * parseFloatEx(_qty),
						_flag = details.ispaid,
						_dflag = details.isdoctor,
						_origprice = details.origprice,
						_origtotal = parseFloatEx(_origprice) * parseFloatEx(_qty),
						_limit = details.limit,
						_checked = (details.checked==1) ? true : false,
						_showdel = (details.showdel==1) ? true : false;
						_calculate = (details.calculate==1) ? true : false;
						_replace = (details.doreplace==1) ? true : false;

				var items = $$('[name="'+srcRef+'[]"]');
				if(_flag == '1')
					_flag = 'paid';
				if(_dflag == '1')
					_dflag = 'paid';
				if(count == 1)
					temp_id = 'h'+_id;
				else
					temp_id = 'd'+_id;
				var srcRefItem=srcRef+temp_id;
				// Adjust item ID for other services
				if (_replace || !$(srcRefItem)) {
					doReplace = $(srcRefItem) ? 1 : 0;
					var _class=((items.length%2)>0) ? 'alt':'';
					var _disabled = (parseFloat(_limit) < 0) ? '' : 'onclick="return false"';
					var _item_no_color = _flag ? "#999999" : "#660000";
					var _item_name_color = _flag ? "#999999" : "0";
					var _d_item_no_color = _dflag ? "#999999" : "#660000";
					var _d_item_name_color = _dflag ? "#999999" : "0";

					if (!doReplace){
						rowSrc = '<tr id="row_'+srcRefItem+'" class="'+_class+'">';
					}
					else{
						rowSrc = '';
					}
					rowSrc+= '<td class="centerAlign" style="padding:0">';


					if(count == 1){
						if (_flag != 0 || parseFloatEx(_price) == 0) {
							rowSrc += '&nbsp;<input id="'+srcRefItem+'" name="'+srcRef+'[]" type="hidden" srcDept="'+src+'" refNo="'+ref+'" itemID="'+_id+'" disabled="disabled" value=""/>';
						}
						else{
							if (parseFloat(_limit) >= 0)
								rowSrc += '<input id="'+srcRefItem+'" name="'+srcRef+'[]" type="hidden" srcDept="'+src+'" refNo="'+ref+'" itemID="'+temp_id+'" value="'+id+'"/><input id="'+srcRefItem+'" type="checkbox" checked="checked" '+_disabled+' value="'+id+'"/>';
							else
								rowSrc += '<input id="'+srcRefItem+'" name="'+srcRef+'[]" type="checkbox" srcDept="'+src+'" refNo="'+ref+'" itemID="'+temp_id+'" '+(_checked==1 ? 'checked="checked"' : '')+' '+_disabled+' value="'+_id+'" onchange="calcSubTotal(\''+src+'\',\''+ref+'\'); refreshTotal(); disableChildrenInputs($(\'row_'+srcRefItem+'\'),!this.checked)"/>';
						}
						desc = _name;
						origdesc = desc;
						if (!desc) desc = "[Unknown item]";
						if (desc.length > 25) desc = desc.substr(0,22)+'...';
						if (_desc) desc += '<br><span style="font:normal 10px Tahoma;color:#404040;">Hospital Bill</span>';
						desc = '<span onmouseover="return overlib(\''+addSlashes(origdesc)+'\',WRAP,0,HAUTO,VAUTO, BGCLASS,\'olTooltipBG\', FGCLASS,\'olTooltipFG\', TEXTFONTCLASS,\'olTooltipTxt\', SHADOW,0, SHADOWX,2, SHADOWY,2, SHADOWOPACITY, 25);" onmouseout="nd();">'+desc+'</span>';
					
						rowSrc += '</td>'+
							'<td align="left"><span id="id_'+srcRefItem+'" style="font:bold 11px Arial;color:'+_item_no_color+'">'+_id+'</span></td>'+
							'<td align="left" style="overflow:hidden"  nowrap="nowrap">'+
							'<span id="desc_'+srcRefItem+'" style="font:bold 11px Tahoma;color:'+_item_name_color+'">'+desc+'</span></td>';

						var _adjColor = (_price < _origprice) ? "#006000" : "#000000";

						if (_flag!=0) {
							rowSrc += '<td align="right" colspan="4">'+
								'<input id="price_'+srcRefItem+'" srcDept="'+src+'" refNo="'+ref+'" itemID="'+temp_id+'" type="hidden" disabled="disabled"  value="0"/>'+
								'<input id="qty_'+srcRefItem+'" srcDept="'+src+'" refNo="'+ref+'" itemID="'+temp_id+'" type="hidden" disabled="disabled" value="0"/>'+
								'<input id="total_'+srcRefItem+'" srcDept="'+src+'" refNo="'+ref+'" itemID='+temp_id+'" type="hidden" disabled="disabled" value="0"/>'+
								'<img alt="'+_flag+'" src="../../images/flag_'+_flag+'.gif" align="absmiddle" />'+
							'</td>'+
							'<td align="right"><img alt="'+_flag+'" src="../../images/flag_'+_flag+'.gif" align="absmiddle" /></td>';
						}
						else {
							var disabledTag = '';
							if (parseFloatEx(_price) == 0) {
								disabledTag = ' disabled="disabled"';
							}
							
							if (parseFloat(_limit) < 0) {
								rowSrc += '				<td align="right">'+
									'<input id="priceorig_'+srcRefItem+'" name="priceorig_'+srcRef+'[]" srcDept="'+src+'" refNo="'+ref+'" itemID="'+temp_id+'" type="hidden" '+disabledTag+' value="'+_hospital+'"/>'+formatNumber(_hospital,2)+
								'</td>'+
								'<td align="right" style="color:'+_adjColor+'">'+
									'<input id="price_'+srcRefItem+'" name="price_'+srcRef+'[]" srcDept="'+src+'" refNo="'+src+'" itemID="'+temp_id+'" type="hidden" '+disabledTag+' value="'+_hospital+'"/>'+formatNumber(_hospital,2)+
								'</td>';
								rowSrc+='<td align="center">'+
									'<input id="qty_'+srcRefItem+'" name="qty_'+srcRef+'[]" srcDept="'+src+'" refNo="'+ref+'" itemID="'+temp_id+'" type="hidden" '+disabledTag+' value="'+_qty+'"/>'+(_qty>1 ? (_qty) : (src=="ph" ? "1" : "&nbsp;"))+
											'</td>';
								rowSrc+='<td align="right">'+
									'<input id="totalorig_'+srcRefItem+'" name="totalorig_'+srcRef+'[]" srcDept="'+src+'" refNo="'+ref+'" itemID="'+temp_id+'" type="hidden" '+disabledTag+' value="'+_hospital+'"/>'+formatNumber(_hospital,2)+
								'</td>'+
								'<td align="right" style="color:'+_adjColor+'">'+
									'<input id="source_'+srcRefItem+'" name="source_'+srcRef+'[]" srcDept="'+src+'" refNo="'+ref+'" itemID="'+temp_id+'" type="hidden" '+disabledTag+' value="HF"/>'+
									'<input id="total_'+srcRefItem+'" name="total_'+srcRef+'[]" srcDept="'+src+'" refNo="'+ref+'" itemID="'+temp_id+'" type="hidden" '+disabledTag+' value="'+_hospital+'"/>'+formatNumber(_hospital,2)+
								'</td>';
							}
						}
						rowSrc +=	'<td align="center">'+
									(_showdel ?
									'<img class="segSimulatedLink" src="../../images/cashier_delete_small.gif" align="absmiddle" border="0" onclick="if (confirm(\'Delete this item?\')) removeServiceFromList(\''+src+'\',\''+ref+'\',\''+temp_id+'\')" />' :
									'')+
									'</td>';
					}
					else {
						if (_dflag != 0 || parseFloatEx(_price) == 0) {
							rowSrc += '&nbsp;<input id="'+srcRefItem+'" name="'+srcRef+'[]" type="hidden" srcDept="'+src+'" refNo="'+ref+'" itemID="'+_id+'" disabled="disabled" value=""/>';
						}
						else{
							if (parseFloat(_limit) >= 0)
								rowSrc += '<input id="'+srcRefItem+'" name="'+srcRef+'[]" type="hidden" srcDept="'+src+'" refNo="'+ref+'" itemID="'+temp_id+'" value="'+id+'"/><input id="'+srcRefItem+'" type="checkbox" checked="checked" '+_disabled+' value="'+id+'"/>';
							else
								rowSrc += '<input id="'+srcRefItem+'" name="'+srcRef+'[]" type="checkbox" srcDept="'+src+'" refNo="'+ref+'" itemID="'+temp_id+'" '+(_checked==1 ? 'checked="checked"' : '')+' '+_disabled+' value="'+_id+'" onchange="calcSubTotal(\''+src+'\',\''+ref+'\'); refreshTotal(); disableChildrenInputs($(\'row_'+srcRefItem+'\'),!this.checked)"/>';
						}
						desc = _name;
						origdesc = desc;
						if (!desc) desc = "[Unknown item]";
						if (desc.length > 25) desc = desc.substr(0,22)+'...';
						if (_desc) desc += '<br><span style="font:normal 10px Tahoma;color:#404040;">Doctor Bill</span>';
						desc = '<span onmouseover="return overlib(\''+addSlashes(origdesc)+'\',WRAP,0,HAUTO,VAUTO, BGCLASS,\'olTooltipBG\', FGCLASS,\'olTooltipFG\', TEXTFONTCLASS,\'olTooltipTxt\', SHADOW,0, SHADOWX,2, SHADOWY,2, SHADOWOPACITY, 25);" onmouseout="nd();">'+desc+'</span>';
					
						rowSrc +='</td>'+
								'<td align="left"><span id="id_'+srcRefItem+'" style="font:bold 11px Arial;color:'+_d_item_no_color+'">'+_id+'</span></td>'+
								'<td align="left" style="overflow:hidden"  nowrap="nowrap">'+
								'<span id="desc_'+srcRefItem+'" style="font:bold 11px Tahoma;color:'+_d_item_name_color+'">'+desc+'</span></td>';

						var _adjColor = (_price < _origprice) ? "#006000" : "#000000";

						if (_dflag!=0) {
							rowSrc += '<td align="right" colspan="4">'+
								'<input id="price_'+srcRefItem+'" srcDept="'+src+'" refNo="'+ref+'" itemID="'+temp_id+'" type="hidden" disabled="disabled"  value="0"/>'+
								'<input id="qty_'+srcRefItem+'" srcDept="'+src+'" refNo="'+ref+'" itemID="'+temp_id+'" type="hidden" disabled="disabled" value="0"/>'+
								'<input id="total_'+srcRefItem+'" srcDept="'+src+'" refNo="'+ref+'" itemID='+temp_id+'" type="hidden" disabled="disabled" value="0"/>'+
								'<img alt="'+_dflag+'" src="../../images/flag_'+_dflag+'.gif" align="absmiddle" />'+
							'</td>'+
							'<td align="right"><img alt="'+_dflag+'" src="../../images/flag_'+_dflag+'.gif" align="absmiddle" /></td>';
						}
						else {
							var disabledTag = '';
							if (parseFloatEx(_price) == 0) {
								disabledTag = ' disabled="disabled"';
							}
							
							if (parseFloat(_limit) < 0) {
									rowSrc += '				<td align="right">'+
										'<input id="priceorig_'+srcRefItem+'" name="priceorig_'+srcRef+'[]" srcDept="'+src+'" refNo="'+ref+'" itemID="'+temp_id+'" type="hidden" '+disabledTag+' value="'+_doctor+'"/>'+formatNumber(_doctor,2)+
									'</td>'+
									'<td align="right" style="color:'+_adjColor+'">'+
										'<input id="price_'+srcRefItem+'" name="price_'+srcRef+'[]" srcDept="'+src+'" refNo="'+src+'" itemID="'+temp_id+'" type="hidden" '+disabledTag+' value="'+_doctor+'"/>'+formatNumber(_doctor,2)+
									'</td>';
									rowSrc+='<td align="center">'+
										'<input id="qty_'+srcRefItem+'" name="qty_'+srcRef+'[]" srcDept="'+src+'" refNo="'+ref+'" itemID="'+temp_id+'" type="hidden" '+disabledTag+' value="'+_qty+'"/>'+(_qty>1 ? (_qty) : (src=="ph" ? "1" : "&nbsp;"))+
												'</td>';
									rowSrc+='<td align="right">'+
										'<input id="totalorig_'+srcRefItem+'" name="totalorig_'+srcRef+'[]" srcDept="'+src+'" refNo="'+ref+'" itemID="'+temp_id+'" type="hidden" '+disabledTag+' value="'+_doctor+'"/>'+formatNumber(_doctor,2)+
									'</td>'+
									'<td align="right" style="color:'+_adjColor+'">'+
										'<input id="source_'+srcRefItem+'" name="source_'+srcRef+'[]" srcDept="'+src+'" refNo="'+ref+'" itemID="'+temp_id+'" type="hidden" '+disabledTag+' value="PF"/>'+
										'<input id="total_'+srcRefItem+'" name="total_'+srcRef+'[]" srcDept="'+src+'" refNo="'+ref+'" itemID="'+temp_id+'" type="hidden" '+disabledTag+' value="'+_doctor+'"/>'+formatNumber(_doctor,2)+
									'</td>';
								}
						}
							rowSrc +=	'<td align="center">'+
									(_showdel ?
										'<img class="segSimulatedLink" src="../../images/cashier_delete_small.gif" align="absmiddle" border="0" onclick="if (confirm(\'Delete this item?\')) removeServiceFromList(\''+src+'\',\''+ref+'\',\''+temp_id+'\')" />' :
										'')+
								'</td>';
					}
					
					if (!doReplace) rowSrc += '</tr>';

					if (items.length==0){
						dBody.innerHTML = rowSrc;
					}
					else {
						if (doReplace) {
							var node = $('row_'+srcRefItem);
							node.innerHTML = rowSrc;
						}
						else
							dBody.innerHTML += rowSrc;
					}

					if (_calculate) {
						calcSubTotal(src, ref)
						refreshTotal()
					}
					// var ok = true;
				}
				else  { // Row already exists
					return false;
				}
			}
			else {
				rowSrc = "<tr><td colspan=\"10\">List is currently empty...</td></tr>";
				dBody.innerHTML = rowSrc;
				calcSubTotal(src, ref)
				refreshTotal()
			}
		}
	}
	return true;
}
//ended by ken

function addServiceToList(details) {
	details = Object.extend({
		src: '',
		ref: '',
		id:'',
		name: '',
		desc: '',
		price: 0,
		qty: 0,
		flag: '',
		origprice: 0,
		limit: -1,
		is_vat: 0,
		checked: 0,
		showdel: 0,
		calculate: 0,
		replace: 0
	}, details || {});
	var src = details.src;
	var ref = details.ref;
	var i, srcRef=src.toLowerCase()+ref;
	var list=$('list_'+srcRef), dRows, dBody, rowSrc;
	if (list) {
		dBody=list.getElementsByTagName("tbody")[0];
		dRows=dBody.getElementsByTagName("tr");

		if (details.id) {
			var _id = details.id,
                _origID = details.id, //added by jasper 05/29/2013
					_name = details.name,
					_desc = details.desc,
					_price = details.price,
					_qty = details.qty,
					_total = parseFloatEx(_price) * parseFloatEx(_qty),
					_flag = details.flag,
					_origprice = details.origprice,
					_origtotal = parseFloatEx(_origprice) * parseFloatEx(_qty),
					_limit = details.limit,
					_is_vat = (details.is_vat==1) ? true : false,
					_checked = (details.checked==1) ? true : false,
					_showdel = (details.showdel==1) ? true : false;
					_calculate = (details.calculate==1) ? true : false;
					_replace = (details.doreplace==1) ? true : false;
             if(src=="misc"){
                 _total = parseFloatEx(_price);
                 _price = parseFloatEx(_total/_qty);
             }else{
                 _total = parseFloatEx(_price) * parseFloatEx(_qty); 
             }
			// Clean index append on OTHER items ID
			if (src == 'other') {
				_id = _id.replace(/\D/,'');
			}

			var items = $$('[name="'+srcRef+'[]"]');
			var srcRefItem=srcRef+_id;
			// Adjust item ID for other services
			if (src == 'other') {
				suffix_index = 0;
				suffix = String.fromCharCode(suffix_index+'a'.charCodeAt(0));
				//alert('row_'+srcRefItem+suffix);
				while ($('row_'+srcRefItem+suffix)) {
					suffix_index++;
					suffix = String.fromCharCode(suffix_index+'a'.charCodeAt(0));
				}

				srcRefItem = srcRefItem.concat(suffix);
				_id = _id.concat(suffix);
			}

			if (_replace || !$(srcRefItem)) {
				doReplace = $(srcRefItem) ? 1 : 0;
				var _class=((items.length%2)>0) ? 'alt':'';
				var _disabled = (parseFloat(_limit) < 0) ? '' : 'onclick="return false"';
				var _item_no_color = _flag ? "#999999" : "#660000";
				var _item_name_color = _flag ? "#999999" : "0";
				if (!doReplace)
					rowSrc = '<tr id="row_'+srcRefItem+'" class="'+_class+'">';
				else
					rowSrc = '';
				rowSrc+= '<td class="centerAlign" style="padding:0">';
				if (_flag || parseFloatEx(_price) == 0) {
					rowSrc += '&nbsp;<input id="'+srcRefItem+'" name="'+srcRef+'[]" type="hidden" srcDept="'+src+'" refNo="'+ref+'" itemID="'+_id+'" disabled="disabled" value=""/>';
				}
				else {
					if (parseFloat(_limit) >= 0)
						rowSrc += '<input id="'+srcRefItem+'" name="'+srcRef+'[]" type="hidden" srcDept="'+src+'" refNo="'+ref+'" itemID="'+_id+'" value="'+id+'"/><input id="'+srcRefItem+'" type="checkbox" checked="checked" '+_disabled+' value="'+id+'"/>';
					else
						rowSrc += '<input id="'+srcRefItem+'" name="'+srcRef+'[]" type="checkbox" srcDept="'+src+'" refNo="'+ref+'" itemID="'+_id+'" '+(_checked==1 ? 'checked="checked"' : '')+' '+_disabled+' value="'+_id+'" onchange="calcSubTotal(\''+src+'\',\''+ref+'\','+_is_vat+'); refreshTotal(); disableChildrenInputs($(\'row_'+srcRefItem+'\'),!this.checked)"/>';
				}

				desc = _name;
				origdesc = desc;
				if (!desc) desc = "[Unknown item]";
				if (desc.length > 25) desc = desc.substr(0,22)+'...';
				if (_desc) desc += '<br><span style="font:normal 10px Tahoma;color:#404040;">'+_desc+'</span>';
				desc = '<span onmouseover="return overlib(\''+addSlashes(origdesc)+'\',WRAP,0,HAUTO,VAUTO, BGCLASS,\'olTooltipBG\', FGCLASS,\'olTooltipFG\', TEXTFONTCLASS,\'olTooltipTxt\', SHADOW,0, SHADOWX,2, SHADOWY,2, SHADOWOPACITY, 25);" onmouseout="nd();">'+desc+'</span>';

				rowSrc +=
				'</td>'+
				'<td align="left"><span id="id_'+srcRefItem+'" style="font:bold 11px Arial;color:'+_item_no_color+'">'+_id+'</span></td>'+
				'<td align="left" style="overflow:hidden"  nowrap="nowrap">'+
					'<span id="desc_'+srcRefItem+'" style="font:bold 11px Tahoma;color:'+_item_name_color+'">'+desc+'</span></td>';

				var _adjColor = (_price < _origprice) ? "#006000" : "#000000";

				if (_flag) {
					rowSrc += '<td align="right" colspan="4">'+
						'<input id="price_'+srcRefItem+'" srcDept="'+src+'" refNo="'+ref+'" itemID="'+_id+'" type="hidden" disabled="disabled"  value="0"/>'+
						'<input id="qty_'+srcRefItem+'" srcDept="'+src+'" refNo="'+ref+'" itemID="'+_id+'" type="hidden" disabled="disabled" value="0"/>'+
						'<input id="total_'+srcRefItem+'" srcDept="'+src+'" refNo="'+ref+'" itemID='+_id+'" type="hidden" disabled="disabled" value="0"/>'+
						'<img alt="'+_flag+'" src="../../images/flag_'+_flag+'.gif" align="absmiddle" />'+
					'</td>'+
					'<td align="right"><img alt="'+_flag+'" src="../../images/flag_'+_flag+'.gif" align="absmiddle" /></td>';
				}
				else {
					var disabledTag = '';
					if (parseFloatEx(_price) == 0) {
						disabledTag = ' disabled="disabled"';
					}
					
					if (parseFloat(_limit) < 0) {
						rowSrc += '				<td align="right">'+
							'<input id="priceorig_'+srcRefItem+'" name="priceorig_'+srcRef+'[]" srcDept="'+src+'" refNo="'+ref+'" itemID="'+_id+'" type="hidden" '+disabledTag+' value="'+_origprice+'"/>'+formatNumber(_origprice,2)+
						'</td>'+
						'<td align="right" style="color:'+_adjColor+'">'+
							'<input id="price_'+srcRefItem+'" name="price_'+srcRef+'[]" srcDept="'+src+'" refNo="'+src+'" itemID="'+_id+'" type="hidden" '+disabledTag+' value="'+_price+'"/>'+formatNumber(_price,2)+
						'</td>';
						if (src=="misc") {
							 rowSrc+='<td align="center">'+
								'<input id="qty_'+srcRefItem+'" name="qty_'+srcRef+'[]" srcDept="'+src+'" refNo="'+ref+'" itemID="'+_id+'" type="hidden" '+disabledTag+' value="'+_qty+'"/>'+_qty+"&nbsp;"+
										'</td>';
						} else {
							rowSrc+='<td align="center">'+
								'<input id="qty_'+srcRefItem+'" name="qty_'+srcRef+'[]" srcDept="'+src+'" refNo="'+ref+'" itemID="'+_id+'" type="hidden" '+disabledTag+' value="'+_qty+'"/>'+(_qty>1 ? (_qty) : (src=="ph" ? "1" : "&nbsp;"))+
										'</td>';
						}
						/*'<td align="center">'+
							'<input id="qty_'+srcRefItem+'" name="qty_'+srcRef+'[]" srcDept="'+src+'" refNo="'+ref+'" itemID="'+_id+'" type="hidden" value="'+_qty+'"/>'+(_qty>1 ? _qty : (src=="ph" ? "1" : "&nbsp;"))+
						'</td>'+*/
						rowSrc+='<td align="right">'+
							'<input id="totalorig_'+srcRefItem+'" name="totalorig_'+srcRef+'[]" srcDept="'+src+'" refNo="'+ref+'" itemID="'+_id+'" type="hidden" '+disabledTag+' value="'+_origtotal+'"/>'+formatNumber(_origtotal,2)+
						'</td>'+
						'<td align="right" style="color:'+_adjColor+'">'+
							'<input id="total_'+srcRefItem+'" name="total_'+srcRef+'[]" srcDept="'+src+'" refNo="'+ref+'" itemID="'+_id+'" type="hidden" '+disabledTag+' value="'+_total+'"/>'+formatNumber(_total,2)+
						'</td>';
					}
					else {
						rowSrc += '				<td align="right">'+
							'<input id="priceorig_'+srcRefItem+'" name="priceorig_'+srcRef+'[]" srcDept="'+src+'" refNo="'+ref+'" itemID="'+_id+'" type="hidden" '+disabledTag+' value="'+_origprice+'"/>'+formatNumber(_origprice,2)+
						'</td>'+
						'<td align="right" style="color:'+_adjColor+'">'+
							'<input id="price_'+srcRefItem+'" name="price_'+srcRef+'[]" srcDept="'+src+'" refNo="'+ref+'" itemID="'+_id+'" type="hidden" '+disabledTag+' value="'+_price+'"/>'+
							'<img src="../../images/charity.gif" align="absmiddle" />'+
						'</td>';
						if(src=="misc")
						{
							 rowSrc+='<td align="center">'+
								'<input id="qty_'+srcRefItem+'" name="qty_'+srcRef+'[]" srcDept="'+src+'" refNo="'+ref+'" itemID="'+_id+'" type="hidden" '+disabledTag+' value="'+_qty+'"/>'+_qty+"&nbsp;"+
										'</td>';
						}
						else
						{
							rowSrc+='<td align="center">'+
								'<input id="qty_'+srcRefItem+'" name="qty_'+srcRef+'[]" srcDept="'+src+'" refNo="'+ref+'" itemID="'+_id+'" type="hidden" '+disabledTag+' value="'+_qty+'"/>'+(_qty>1 ? (_qty) : (src=="ph" ? "1" : "&nbsp;"))+
										'</td>';
						}
						rowSrc+='<td align="right">'+
							'<input id="totalorig_'+srcRefItem+'" name="totalorig_'+srcRef+'[]" srcDept="'+src+'" refNo="'+ref+'" itemID="'+_id+'" type="hidden" '+disabledTag+' value="'+_origtotal+'"/>'+formatNumber(_origtotal,2)+
						'</td>'+
						'<td align="right" style="color:'+_adjColor+'">'+
							'<input id="total_'+srcRefItem+'" name="total_'+srcRef+'[]" srcDept="'+src+'" refNo="'+ref+'" itemID="'+_id+'" type="hidden" '+disabledTag+' value="'+_total+'"/>'+
							'<img src="../../images/charity.gif" align="absmiddle" />'+
						'</td>';
					}
				}

//				if (_ispaid) {
//					rowSrc += '<td align="right" colspan="4">'+
//						'<input id="price_'+srcRefItem+'" srcDept="'+src+'" refNo="'+ref+'" itemID="'+_id+'" type="hidden" disabled="disabled"  value="0"/>'+
//						'<input id="qty_'+srcRefItem+'" srcDept="'+src+'" refNo="'+ref+'" itemID="'+_id+'" type="hidden" disabled="disabled" value="0"/>'+
//						'<input id="total_'+srcRefItem+'" srcDept="'+src+'" refNo="'+ref+'" itemID='+_id+'" type="hidden" disabled="disabled" value="0"/>'+
//						'<img src="../../images/paid_item.gif" align="absmiddle" />'+
//					'</td>'+
//					'<td align="right"><img src="../../images/paid_item.gif" align="absmiddle" /></td>';
//				}
//				else if (parseFloatEx(_price) == 0) {
//					rowSrc += '<td align="right" colspan="4">'+
//						'<input id="price_'+srcRefItem+'" srcDept="'+src+'" refNo="'+ref+'" itemID="'+_id+'" type="hidden" disabled="disabled"  value="0"/>'+
//						'<input id="qty_'+srcRefItem+'" srcDept="'+src+'" refNo="'+ref+'" itemID="'+_id+'" type="hidden" disabled="disabled" value="0"/>'+
//						'<input id="total_'+srcRefItem+'" srcDept="'+src+'" refNo="'+ref+'" itemID='+_id+'" type="hidden" disabled="disabled" value="0"/>'+
//						'<img src="../../images/charity_item.gif" align="absmiddle" />'+
//					'</td>'+
//					'<td align="right"><img src="../../images/charity_item.gif" align="absmiddle" /></td>';
//				}
//				else {
//					if (parseFloat(_limit) < 0) {
//						rowSrc += '				<td align="right">'+
//							'<input id="priceorig_'+srcRefItem+'" name="priceorig_'+srcRef+'[]" srcDept="'+src+'" refNo="'+ref+'" itemID="'+_id+'" type="hidden" value="'+_origprice+'"/>'+formatNumber(_origprice,2)+
//						'</td>'+
//						'<td align="right" style="color:'+_adjColor+'">'+
//							'<input id="price_'+srcRefItem+'" name="price_'+srcRef+'[]" srcDept="'+src+'" refNo="'+src+'" itemID="'+_id+'" type="hidden" value="'+_price+'"/>'+formatNumber(_price,2)+
//						'</td>'+
//						'<td align="center">'+
//							'<input id="qty_'+srcRefItem+'" name="qty_'+srcRef+'[]" srcDept="'+src+'" refNo="'+ref+'" itemID="'+_id+'" type="hidden" value="'+_qty+'"/>'+(_qty>1 ? _qty : (src=="ph" ? "1" : "&nbsp;"))+
//						'</td>'+
//						'<td align="right">'+
//							'<input id="totalorig_'+srcRefItem+'" name="totalorig_'+srcRef+'[]" srcDept="'+src+'" refNo="'+ref+'" itemID="'+_id+'" type="hidden" value="'+_origtotal+'"/>'+formatNumber(_origtotal,2)+
//						'</td>'+
//						'<td align="right" style="color:'+_adjColor+'">'+
//							'<input id="total_'+srcRefItem+'" name="total_'+srcRef+'[]" srcDept="'+src+'" refNo="'+ref+'" itemID="'+_id+'" type="hidden" value="'+_total+'"/>'+formatNumber(_total,2)+
//						'</td>';
//					}
//					else {
//						rowSrc += '				<td align="right">'+
//							'<input id="priceorig_'+srcRefItem+'" name="priceorig_'+srcRef+'[]" srcDept="'+src+'" refNo="'+ref+'" itemID="'+_id+'" type="hidden" value="'+_origprice+'"/>'+formatNumber(_origprice,2)+
//						'</td>'+
//						'<td align="right" style="color:'+_adjColor+'">'+
//							'<input id="price_'+srcRefItem+'" name="price_'+srcRef+'[]" srcDept="'+src+'" refNo="'+ref+'" itemID="'+_id+'" type="hidden" value="'+_price+'"/>'+
//							'<img src="../../images/charity.gif" align="absmiddle" />'+
//						'</td>'+
//						'<td align="center">'+
//							'<input id="qty_'+srcRefItem+'" name="qty_'+srcRef+'[]" srcDept="'+src+'" refNo="'+ref+'" itemID="'+_id+'" type="hidden" value="'+_qty+'"/>'+(_qty>1 ? (_qty) : (src=="ph" ? "1" : "&nbsp;"))+
//						'</td>'+
//						'<td align="right">'+
//							'<input id="totalorig_'+srcRefItem+'" name="totalorig_'+srcRef+'[]" srcDept="'+src+'" refNo="'+ref+'" itemID="'+_id+'" type="hidden" value="'+_origtotal+'"/>'+formatNumber(_origtotal,2)+
//						'</td>'+
//						'<td align="right" style="color:'+_adjColor+'">'+
//							'<input id="total_'+srcRefItem+'" name="total_'+srcRef+'[]" srcDept="'+src+'" refNo="'+ref+'" itemID="'+_id+'" type="hidden" value="'+_total+'"/>'+
//							'<img src="../../images/charity.gif" align="absmiddle" />'+
//						'</td>';
//					}
//				}
				rowSrc +=	'<td align="center">'+
						(_showdel ?
							'<img class="segSimulatedLink" src="../../images/cashier_delete_small.gif" align="absmiddle" border="0" onclick="if (confirm(\'Delete this item?\')) removeServiceFromList(\''+src+'\',\''+ref+'\',\''+_id+'\')" />' :
							'')+
					'</td>';
				rowSrc += '<input id="vat_'+srcRefItem+'" name="vat_'+srcRef+'[]" srcDept="'+src+'"  type="hidden" value="'+_is_vat+'"/>';
				if (!doReplace) rowSrc += '</tr>';

				if (items.length==0){
					dBody.innerHTML = rowSrc;
					if(_is_vat)
						vat_amount += _total;
				}
				else {
					if (doReplace) {
						var node = $('row_'+srcRefItem);
						node.innerHTML = rowSrc;
					}
					else{
						dBody.innerHTML += rowSrc;
						if(_is_vat)
							vat_amount += _total;
					}
				}

				if (_calculate) {
					calcSubTotal(src, ref, _is_vat)
					refreshTotal()
				}
				
				return true;
			}
			else  { // Row already exists
				return false;
			}
		}
		else {
			rowSrc = "<tr><td colspan=\"10\">List is currently empty...</td></tr>";
			dBody.innerHTML = rowSrc;
			calcSubTotal(src, ref, _is_vat)
			refreshTotal()
		}
	}

	return false;
}

function _setValue(id, value) {
	var obj = $(id);
	if (obj) {
		if (obj.value==null) obj.innerHTML = value;
		else obj.value = value;
		return true;
	}
	else return false;
}

function removeRequest(src,ref) {
	var requests = $('request_dashlet');
	var target = $(src+ref);
	if(src == 'ph')
		vat_amount -= $('subtotal_'+src+ref).value;
	if ( requests && target ) {
		requests.removeChild(target);
	}
}

function addRequestFromTray(src, ref, is_vatable) {
	if ($(src+ref)) {
		alert('This request is already added...')
	}
	else {
		cClick();
		startLoading();
		xajax_addReference(src,ref,checkedItems,1,theORNo,is_vatable);
	}
}

function refreshRequest(src, ref, details) {
	var i, srcRef=src.toLowerCase()+ref;
	var name=details.name,
			limit=details.limit,
			populate=(details.populate==1 ? true : false);

	if ($('request_dashlet')) {
		if (!$('list_'+srcRef) && !populate) {

			var htmlHeader = '<div id="'+srcRef+'" class="dashlet">'+
	'<table class="dashletHeader" border="0" cellpadding="0" cellspacing="0">'+
		'<tr>'+
			'<td width="*">'+
				'<h1 id="name_'+srcRef+'">'+name+'</h1>'+
			'</td>'+
			'<td width="10%" align="right" nowrap="nowrap" style="">'+
				'<!-- <img title="Edit" class="segSimulatedLink" src="../../images/cashier_edit.gif" align="absmiddle" border="0" style="margin:1px"/> -->'+
				'<img title="Refresh" class="segSimulatedLink" src="../../images/cashier_refresh.gif" align="absmiddle" border="0" style="margin:1px" onclick="refreshRequest(\''+src+'\',\''+ref+'\',{populate:1})"/>'+
				'<img title="Remove" class="segSimulatedLink" src="../../images/cashier_delete.gif" align="absmiddle" border="0" style="margin:1px" onclick="if (confirm(\'Remove this request?\')) { removeRequest(\''+src+'\',\''+ref+'\'); refreshTotal(); }"/>'+
			'</td>'+
	'</table>'+
	'<input name="requests[]" type="hidden" srcDept="'+src+'" refNo="'+ref+'" value="'+srcRef+'"/>'+
	'<input name="iscash[]" type="hidden" srcDept="'+src+'" refNo="'+ref+'" value="1"/>'+
	'<table id="list_'+srcRef+'" class="segList" border="0" cellpadding="0" cellspacing="0" style="width:100%;margin-bottom:10px">'+
		'<thead>'+
			'<tr id=\"row_'+srcRef+'">'+
				'<th width="3%" class="centerAlign">'+
					'<input type="checkbox" onchange="flagCheckBoxesByName(\''+srcRef+'[]\',this.checked); calcSubTotal(\''+src+'\',\''+ref+'\', false)" checked="checked">'+
				'</th>'+
				'<th align="left" width="10%" nowrap>Item No</th>'+
				'<th align="left" width="*" nowrap=\"nowrap\">Item Description</th>'+
				'<th align="right" width="9%" nowrap="nowrap" style="font-size:90%">Price/item (Orig)</th>'+
				'<th align="right" width="9%" nowrap="nowrap" style="font-size:90%">Price/item (Adj)</th>'+
				'<th align="right" width="9%" nowrap="nowrap">Quantity</th>'+
				'<th align="right" width="9%" nowrap="nowrap">Price (Orig)</th>'+
				'<th align="right" width="9%" nowrap="nowrap" >Price (Adj)</th>'+
				'<th class="centerAlign" width="1"></th>'+
			'</tr>'+
		'</thead>'+
		'<tbody id="body_'+srcRef+'">';
			var htmlFooter = '</tbody>'+
		'<tfoot>'+
			'<tr>'+
				'<th align="center" nowrap="nowrap">'+
					'<img class="segSimulatedLink" src="../../images/cashier_up_small.gif" align="absmiddle" onclick="toggleTBody(\'list_'+srcRef+'\')" />'+
				'</th>'+
				'<th align="left" colspan="3" nowrap="nowrap">Items (<span id="items_'+srcRef+'">'+'</span>)</th>'+
				'<th align="right" nowrap="nowrap">Orig Subtotal:</th>'+
				'<th align="left" nowrap="nowrap" style="font-weight:normal">'+
					'<input type="hidden" id="subtotal_orig_'+srcRef+'" name="subtotal_orig_'+srcRef+'" value="'+'"/>'+
					'<span id="show_subtotal_orig_'+srcRef+'" >'+
						//.money_format("%(!.2i",$subtotalorig)."
					'</span>'+
				'</th>'+
				'<th align="right" nowrap="nowrap">Adj Subtotal:</th>'+
				'<th align="left" nowrap="nowrap" style="font-weight:normal" colspan="2">'+
					'<input type="hidden" id="charity_'+srcRef+'" name="charity_'+srcRef+'" value="'+limit+'"/>'+
					'<input type="hidden" id="subtotal_'+srcRef+'" name="subtotal_'+srcRef+'" value="'+'"/>'+
					'<span style="'+(limit >= 0 ? 'color:yellow' : '')+'" id="show_subtotal_'+srcRef+'" >'+
						// money_format("%(!.2i",$subtotal).'
					'</span>'+
					'<img id="charity_icon_'+srcRef+'" src="../../images/charity.gif" align="absmiddle" style="'+(isNaN(parseFloat(limit)) || (parseFloat(limit)<0) ? 'display:none' : '')+'"/>'+
				'</th>'+
			'</tr></tfoot></table></div>';
			//alert(htmlHeader + htmlFooter);
			$('request_dashlet').innerHTML += htmlHeader + htmlFooter;
			return true;
		}
		else {
			if (populate) {
				$('body_'+srcRef).innerHTML = "";
				var notLoading = isLoading ? 0:1;
				if (notLoading)	startLoading();
				xajax_populateDetails(src,ref,notLoading,checkedItems,theORNo);
				return true;
			}
			else {
				alert("Reference already added...");
				return false;
			}
		}
	}

	return false;
}

function calcSubTotal(src, ref, is_vat) {
	var totals = document.getElementsByName('total_'+src+ref+'[]');
	var totals_orig = document.getElementsByName('totalorig_'+src+ref+'[]');
	var vat = document.getElementsByName('vat_'+src+ref+'[]');
	var limit = parseFloatEx($('charity_'+src+ref).value);
	var subtotal = 0, subtotalorig = 0;
	var id;
	var count=0;

	$('charity_icon_'+src+ref).style.display = (limit<0 ? "none" : "");
	$('show_subtotal_'+src+ref).style.color = (limit<0 ? "" : "yellow");
	if (totals) {
		if (limit >= 0) {
			subtotal = limit
			count = totals.length;
		}
		else {
			for (var i=0; i<totals.length; i++) {
				id = totals[i].getAttribute("itemID")
				if ($(src+ref+id).checked) {
					count++;
					subtotal += parseFloatEx(totals[i].value);
					subtotalorig += parseFloatEx(totals_orig[i].value);
					if(is_vat && vat[i].value == 'true')
						vat_amount += parseFloatEx(totals[i].value);
				}
				else{
					if(is_vat && vat[i].value == 'true')
						vat_amount -= parseFloatEx(totals[i].value);
				}
			}
		}
		_setValue('items_'+src+ref,count)

		_setValue('subtotal_'+src+ref,subtotal)
		_setValue('subtotal_orig_'+src+ref,subtotalorig)

		_setValue('show_subtotal_'+src+ref,formatNumber(subtotal,2))
		_setValue('show_subtotal_orig_'+src+ref,formatNumber(subtotalorig,2))
		return subtotal
	}
	else return false
}

function refreshTotal() {
	var requests = document.getElementsByName('requests[]')
	var nettotal = 0, subtotal = 0, vat_amount1 = 0, vatable_amount = vat_amount
	var src, ref, srcrefid
	var totals
	if (requests) {
		for (var i=0; i<requests.length; i++) {
			src = requests[i].getAttribute("srcDept")
			ref = requests[i].getAttribute("refNo")
			totals = document.getElementsByName('total_'+src+ref+'[]')

			if (totals) {
				for (var j=0; j<totals.length; j++) {
					id = totals[j].getAttribute("itemID")
					srcrefid = src+ref+id
					if ($(srcrefid).checked) {
						subtotal += parseFloatEx($('qty_'+srcrefid).value) * parseFloatEx($('priceorig_'+srcrefid).value)
						nettotal += parseFloatEx(totals[j].value)
					}
				}
			}
		}
		// var hstotal = calcSubTotal('hs', '0000000000')
		// subtotal += hstotal
		// nettotal += hstotal
		var discount = $('discount_tendered').value;
		if(discount)
			nettotal = nettotal - discount;

		var discountTotal = nettotal-subtotal
		if(vatable=='yes'){
			if(discount && vat_amount != 0){
				vat_amount1 = vatable_amount - discount;
				var vat = vat_amount1 - (vat_amount1/1.12);
				vatable_amount = vat_amount1 - vat;
				$('vat_amount').setAttribute('value', vat);
			}
			else{
				var vat = vatable_amount - (vatable_amount/1.12);
				vatable_amount = vatable_amount - vat;
				$('vat_amount').setAttribute('value', vat);
			}
		}
/*
		if ($('show-sub-total')) $('show-sub-total').innerHTML = formatNumber(subtotal, 2)
		if ($('show-discount-total')) $('show-discount-total').innerHTML = (discountTotal <= 0) ? '('+formatNumber(Math.abs(discountTotal), 2)+')' : '<span style="color:red">'+formatNumber(discountTotal,2)+'</span>'
		if ($('show-net-total')) $('show-net-total').innerHTML = formatNumber(nettotal, 2)
*/

		if ($('show-sub-total')) $('show-sub-total').innerHTML = formatNumber(subtotal, 2)
		if ($('show-sub-total')) $('show-sub-total').setAttribute('value',subtotal)

		if ($('show-vat-total')) $('show-vat-total').innerHTML = formatNumber(vat, 2)
		if ($('show-vat-total')) $('show-vat-total').setAttribute('value',vat)

		if ($('show-vatable-total')) $('show-vatable-total').innerHTML = formatNumber(vatable_amount, 2)
		if ($('show-vatable-total')) $('show-vatable-total').setAttribute('value',vatable_amount)

		if ($('show-discount-total')) $('show-discount-total').innerHTML = (discountTotal <= 0) ? '('+formatNumber(Math.abs(discountTotal), 2)+')' : '<span style="color:red">'+formatNumber(discountTotal,2)+'</span>'
		if ($('show-discount-total')) $('show-discount-total').setAttribute('value',discountTotal)


		if ($('show-net-total')) $('show-net-total').innerHTML = formatNumber(nettotal, 2)
		if ($('show-net-total')) $('show-net-total').setAttribute('value',nettotal)

		refreshAmountChange()

		return subtotal
	}
	else return false
}

function displayVat(vat){
	vatable = vat;
	if(vat == 'yes'){
		$("vat_container").style.display = '';
		$("vat_container1").style.display = '';
		$("vatable_container").style.display = '';
		$("vatable_container1").style.display = '';
	}
}

function amtTenderedOnBlurFocusHandle(obj) {
	obj.value = parseFloatEx(obj.value)
	if (isNaN(obj.value)) obj.value = 0.0;
	$("show-amt-tendered").setAttribute('value',obj.value)
	$("show-amt-tendered").innerHTML = formatNumber(obj.value,2)
	refreshAmountChange();
	return true
}

function discountOnBlurFocusHandle(obj) {
	obj.value = parseFloatEx(obj.value)
	if (isNaN(obj.value)) obj.value = 0.0;
	$("show-discount-total").setAttribute('value',obj.value)
	$("show-discount-total").innerHTML = formatNumber(obj.value,2)
	refreshTotal();
	return true
}

function refreshAmountChange() {
	var change
	var total = parseFloatEx($('show-net-total').getAttribute('value'))
	if (isNaN(total)) total=0
	var tendered = parseFloatEx($('amount_tendered').value)
	if (isNaN(tendered)) tendered=0
	var change = tendered-total
	color = (change>=0) ? 'black' : 'red'

	$('show-amt-tendered').setAttribute('value',tendered)
	$('show-amt-tendered').innerHTML = formatNumber(tendered,2)
	$('show-change').style.color = color

	$('show-change').setAttribute('value',formatNumber(change,2))
	$('show-change').innerHTML = formatNumber(change,2)
}

function refreshTotalEx() {
	var requests = document.getElementsByName('requests[]')
	var subtotal = 0
	var src, ref
	if (requests) {
		for (var i=0; i<requests.length; i++) {
			src = requests[i].getAttribute("srcDept")
			ref = requests[i].getAttribute("refNo")
			subtotal += calcSubTotal(src, ref)
		}
		subtotal += calcSubTotal('hs', '0000000000', false)
		if ($('show-net-total')) $('show-net-total').innerHTML = formatNumber(subtotal, 2)
		return subtotal
	}
	else return false
}

function addParticularRow(details) {
	list = $("pay");
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			var lastRowNum = null,
					id = details["code"];
					dRows = dBody.getElementsByTagName("tr");
			if (details["FLAG"]=="1") {
				alt = (dRows.length%2)+1
				src =
				'<tr'+((dRows.length%2>0)?' class="alt"':'')+'>' +
					'<td class="centerAlign" style="color:#660000">'+details["code"]+'</td>'+
					'<td class="centerAlign">'+details["source"]+'</td>'+
					'<td>'+details["service"]+'</td>'+
					'<td class="rightAlign">'+details["price"]+'</td>'+
					'<td class="centerAlign">'+details["quantity"]+'</td>'+
					'<td class="rightAlign">'+details["total"]+'</td>'+
				'</tr>';
			}
			else {
				src = "<tr><td colspan=\"6\">List is currently empty...</td></tr>";
			}
			dBody.innerHTML += src;
			return true;
		}
	}
	return false;
}


//added by daryl

function autoSuggestWalkin(element) {

		$('oraddress').readOnly = false;
		if (!$F('oraddress')) $('oraddress').value = "NOT PROVIDED";
		var script = "ajax/walkin_cashier.php";
		var options = {
			delay: 5,
			timeout: 2000,
			script: function (input) { return ( script + '?s='+input); },
			callback: function (obj) {
				//$('xml_info').update('you have selected: '+obj.id + ' ' + obj.value + ' (' + obj.info + ')');
				$('orname').readOnly = true;
				$('oraddress').readOnly = true;
				$('orname').value = obj.value;
				$('oraddress').value = obj.info;
				$('pid').value = obj.id;
				$('clear-enc').disabled = false;
			}
		};
		var xml=new AutoComplete(element.id,options);
		return true;

}