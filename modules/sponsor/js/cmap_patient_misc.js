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

function resetControls() {
	$('name').value="";
	$('address').value="";
	$('pid').value="";
	$('encounter_nr').value="";
	$('clear-enc').disabled = false;
	$('sw-class').update('None');
	$('encounter_type_show').update('WALK-IN');
	$('encounter_type').value = '';
	$('select-enc').className = 'link';

	//alert('msg:'+rlst.initialMessage)
	if (typeof(rlst) == 'object') {
		rlst.clear({message:rlst.initialMessage});
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

function addPatientRequest(details) {
	list = $('rlst');
	if (list) {
		var dBody=list.select("tbody")[0];
		if (dBody) {
			if (typeof(details)=='object') {
				var source=details["source"],
					nr=details["refno"],
					item=details["itemno"],
					id=source+nr+item,
					date=details["date"],
					name=details["name"],
					qty=details["qty"],
					total=details["total"],
					status=details["status"],
					discounted=details["discounted"],
					balance=details["due"],
					flag=details["flag"],
					disabled=(details["disabled"]=='1');

				var dRows = dBody.select("tr");
				var alt = (dRows.length%2>0) ? 'alt':'';
				var disabledAttrib = disabled ? 'disabled="disabled"' : "";

				var options;
				if (flag == 'PAID') {
					options = new Element('img',
						{ src:'../../images/paid_item.gif', title: 'Paid', align:'absmiddle' }
					);
				}
				else if (flag == 'LINGAP') {
					options = new Element('img',
						{ src:'../../images/lingap_item.gif', title: 'Lingap', align:'absmiddle' }
					);
				}
				else if (flag == 'CMAP') {
					options = new Element('img',
						{ src:'../../images/cmap_item.gif', title: 'CMAP', align:'absmiddle' }
					);
				}
				else {
					options = new Element('img',
						{ id:'ri_add_'+id, class:'link', src:'../../images/cashier_edit.gif', align:'absmiddle' }
					).observe( 'click',
						function(event) {
							openGrant( { src: source, nr: nr, code: item} )
						}
					);

				}
				var row = new Element('tr', { class: alt, id:'ri_'+id , style:'height:26px' } ).update(
					new Element('td', { class:'centerAlign' } ).update(
						new Element('span', { id: 'ri_date_'+id}).update(date)
					)
				).insert(
					new Element('td', { class:'centerAlign' } ).update(
						new Element('span', { id: 'ri_source_'+id }).update(source)
					)
				).insert(
					new Element('td', { class:'centerAlign' } ).update(
						new Element('span', { id: 'ri_nr_'+id}).update(nr)
					)
				).insert(
					new Element('td', { class:'leftAlign' } ).update(
						new Element('span', { id: 'ri_name_'+id }).update(name)
					).insert(
						new Element('input', { id:'ri_itemno_'+id, type:'hidden', value:item } )
					)
				).insert(
					new Element('td', { class:'centerAlign' } ).update(
						new Element('span', { id: 'ri_qty_show_'+id }).update(qty)
					).insert(
						new Element('input', { id:'ri_qty_'+id, type:'hidden', value:qty } )
					)
				).insert(
					new Element('td', { class:'rightAlign' } ).update(
						new Element('span', { id: 'ri_discounted_'+id}).update( formatNumber(discounted,2) )
					)
//        ).insert(
//          new Element('td', { class:'rightAlign' } ).update(
//            new Element('span', { id: 'ri_balance_'+id}).update( formatNumber(balance,2) ).setStyle({color:(discounted>balance?'#00c000':'')})
//          )
				).insert(
					new Element('td', { class:'centerAlign' }).update( options )
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

function calculateTotals() {
	var totals=$$('[name="amount[]"]');
	var total=0;
	if (totals) {
		totals.each( function (x) { total+=parseFloatEx(x.value) } );
	}

	var bal = parseFloatEx($('show-account').getAttribute('value'));
	$('show-total').update(formatNumber(total,2) ).setAttribute('value', total);
	$('show-balance').update(formatNumber(bal-total,2) ).setAttribute('value', bal-total);
}

function addCMAPItem(details) {
	var container = $('hidden-inputs');
	if (container && typeof(details)=='object') {
		var source=details["source"],
				nr=details["nr"],
				item=details["item"],
				name=details["name"],
				qty=details["qty"],
				amount=details["amount"],
				id=source+nr+item,
				disabled=(details["disabled"]=='1');

		var row = new Element('fieldset', { id:'li_'+id , style:'display:none' } ).update(
				new Element('input', { id:'li_itemno_'+id, name:'item[]', type:'hidden', value:item } )
			).insert(
				new Element('input', { id:'li_source_'+id, name:'src[]', type:'hidden', value:source } )
			).insert(
				new Element('input', { id:'li_nr_'+id, name:'ref[]', type:'hidden', value:nr } )
			).insert(
				new Element('input', { id:'li_qty_'+id, name:'qty[]', type:'hidden', value:qty } )
			).insert(
				new Element('input', { id:'li_amount_'+id, name:'amount[]', type:'hidden', value:amount } )
			).insert(
				new Element('input', { id:'li_name_'+id, name:'service[]', type:'hidden', value:name } )
			);

		container.insert(row);
		return true;
	}
	else
		return false;
}

function removeItem(id) {
	var removeObj=$('li_'+id);
	if (removeObj) {
		removeObj.remove();
		return true;
	}
	else
		return false;
}