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

function tooltip(text) {
	return overlib(text,WRAP,0,HAUTO,VAUTO, BGCLASS,'olTooltipBG', FGCLASS,'olTooltipFG', TEXTFONTCLASS,'olTooltipTxt', SHADOW,0, SHADOWX,2, SHADOWY,2, SHADOWOPACITY, 25);
}

function resetControls() {
	$('patientname').value="";
	$('pid').value="";
	$('encounter_nr').value="";
	$('clear-enc').disabled = false;
	$('sw-class').innerHTML = 'None';
	$('encounter_type_show').innerHTML = 'WALK-IN';
	$('encounter_type').value = '';
	$('select-enc').className = 'segSimulatedLink';

	//alert('msg:'+rlst.initialMessage)
	if (typeof(flst) == 'object') {
		rlst.clear({message:flst.initialMessage});
	}
	if (typeof(rlst) == 'object') {
		rlst.clear({message:rlst.initialMessage});
	}
	if (typeof(alst) == 'object') {
		rlst.clear({message:alst.initialMessage});
	}
	new Effect.Appear($('rqsearch'),{ duration:0.5 });
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

function addBillingStatementRow(details) {
	list = $('rlst');
	if (list) {
		var dBody=list.select("tbody")[0];
		if (dBody) {
			if (!details) details = { FLAG: false};
			if (details['FLAG']) {
				var id=details["nr"],
					nr=details["nr"],
					ward=details["ward"],
					date=details["date"],
					due=parseFloatEx(details["due"]),
					grant=parseFloatEx(details["grant"]),
					disabled=(details["disabled"]=='1');

				var dRows = dBody.select("tr");
				var alt = (dRows.length%2>0) ? 'alt':'';
				var disabledAttrib = disabled ? 'disabled="disabled"' : "";

				var row = new Element('tr', { class: alt, id:'ri_'+id , style:'height:26px' } ).update(
					new Element('td', { class:'centerAlign' } ).update(
						new Element('img', { id: 'ri_expand_'+id, class: 'expand_row' } )
					)
				).insert(
					new Element('td', { class:'centerAlign' } ).update(
						new Element('span', { id: 'ri_date_'+id }).update(ward)
					)
				).insert(
					new Element('td', { class:'centerAlign' } ).update(
						new Element('span', { id: 'ri_ward_'+id }).update(ward)
					)
				).insert(
					new Element('td', { class:'centerAlign' } ).update(
						new Element('span', { id: 'ri_nr_'+id }).update(nr)
					)
				).insert(
					new Element('td', { class:'rightAlign' } ).update(
						new Element('span', { id: 'ri_due_'+id}).update( formatNumber(due,2) )
					)
				).insert(
					new Element('td', { class:'rightAlign' } ).update(
						new Element('span', { id: 'ri_grant_'+id}).update( formatNumber(grant,2) )
					)
				).insert(
					new Element('td', { class:'centerAlign' } ).update(
						new Element('img',{ id:'ri_print_'+id, class:'disabled', src:'../../images/cashier_view.gif' }
						).setStyle( { margin:'1px' }
						).observe( 'click',
							function(event) {
							}
						).observe( 'mouseover',
							function(event) {
								tooltip('View printout for this grant');
							}
						).observe( 'mouseout',
							function(event) {
								nd();
							}
						)
					).insert(
						new Element('img',{ id:'ri_delete_'+id, class:'segSimulatedLink', src:'../../images/cashier_delete.gif' }
						).setStyle( { margin:'1px' }
						).observe( 'click',
							function(event) {
								confirmDelete(source, entry);
							}
						).observe( 'mouseover',
							function(event) {
								tooltip('Cancel this grant');
							}
						).observe( 'mouseout',
							function(event) {
								nd();
							}
						)
					)
				);
				dBody.insert(row);
			}
			else {
				dBody.update('<tr><td colspan="4">List is currently empty...</td></tr>');
			}
			return true;
		}
	}
	return false;
}


function removeItem(id) {
	var destTable, destRows;
	var table = $('order-list');
	var rmvRow=$('row'+id);
	if (table && rmvRow) {
		var rndx = rmvRow.rowIndex-1;
		rmvRow.remove();
		if (!document.getElementsByName("items[]") || document.getElementsByName("items[]").length <= 0)
			appendOrder(table, null);
		reclassRows(table,rndx);
	}
}

function changeTransactionType() {
	clearEncounter();
	refreshDiscount();
}

function clickGrant() {
	if (parseFloatEx($('grant-amount').value)==0) {
		alert('Please enter an amount...');
		return false;
	}
	xajax.call('addGrant', { parameters:[ iSrc, iNr, iCode, iArea, $('grant-account').value, parseFloatEx($('grant-amount').value) ] });
}