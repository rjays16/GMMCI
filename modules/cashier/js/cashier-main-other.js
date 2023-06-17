function clearOtherList() {
	// Search for the source row table element
	var list=$('list_hs0000000000'),dRows, dBody;
	if (list) {
		dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			dBody.innerHTML = "";
			addServiceToOtherList();
			return true;	// success
		}
		else return false;	// fail
	}
	else return false;	// fail
}

function removeServiceFromOtherList(id) {
	var row = $('row_'+id);
	if (row) {
		row.parentNode.removeChild(row)
		reclassRows(row.rowIndex)
		var items = document.getElementsByName('items[]')
		if (!items.length) {
			addServiceToOtherList()
		}
	}
}

function addServiceToOtherList(details) {
	var i;
	var list=$('list_hs0000000000'), dRows, dBody, rowSrc;

	if (list) {
		dBody=list.getElementsByTagName("tbody")[0];
		dRows=dBody.getElementsByTagName("tr");

		if (details) {
			var id = details.id, 
					desc = details.desc,
					price = parseFloatEx(details.price),
					qty = parseFloatEx(details.qty);
			
			if (isNaN(price)) price = 0;
			if (isNaN(qty)) qty= 0;
			var	total = price*qty;
			

			var items = document.getElementsByName('items[]');
			var suffix = 'a', suffix_new;
			for (var i=0;i<items.length;i++) {
				if (items[i].getAttribute('itemID') == id) {
					suffix_new = items[i].getAttribute('suffix');
					suffix = String.fromCharCode(suffix_new.charCodeAt(0)+1);
				}
			}
			
			var rowid = id.concat(suffix);
			var class=((items.length%2)>0) ? 'alt':'';
			rowSrc = 
				'<tr id="row_'+rowid+'" class="'+class+'">'+
					'<td style="">'+
						'<input id="'+rowid+'" name="items[]" type="hidden" suffix="'+suffix+'" itemID="'+id+'" value="'+rowid+'"/>'+
						'<img src="../../images/cashier_delete_small.gif" class="segSimulatedLink" onclick="if (confirm(\'Remove this item from the list?\')) { removeServiceFromList(\''+rowid+'\'); reclassRows(0); }">'+
					'</td>'+
					'<td align="left"><span id="id_'+rowid+'" style="font:bold 11px Arial;color:#660000">'+id+'</span></td>'+
					'<td align="left"><span id="desc_'+rowid+'" style="font:bold 12px Arial">'+desc+'</span></td>'+
					'<td align="right">'+
						'<input class="jedInput" id="price_'+rowid+'" name="price[]" itemID="'+id+'" type="'+(price?'hidden':'text')+'" value="'+formatNumber(price,2)+'" style="font-size:12px; width:80px; text-align:right" onblur="this.value=formatNumber(this.value,2);refreshItemTotal(\''+rowid+'\')" />'+(price?formatNumber(price,2):'')+
					'</td>'+
					'<td align="right">'+
						'<input class="jedInput" id="qty_'+rowid+'" name="qty[]" itemID="'+id+'" type="text" value="'+qty+'" style="font-size:12px; width:99%; text-align:right" onblur="refreshItemTotal(\''+rowid+'\')"/>'+
					'</td>'+
					'<td align="right">'+
						'<input id="total_'+rowid+'" name="total[]" itemID="'+id+'" type="hidden" value="'+total+'"/>'+
						'<span id="total_disp_'+rowid+'">'+formatNumber(total,2)+'</span>'+
					'</td>'+
				'</tr>';
			if (items.length==0)
				dBody.innerHTML = rowSrc;
			else
				dBody.innerHTML += rowSrc;				
			refreshTotal()
			return true;
		}
		else {
			rowSrc = '<tr><td colspan="10">Service list is currently empty...</td></tr>';
			dBody.innerHTML = rowSrc;
			return true;
		}
	}
	
	return false;
}
