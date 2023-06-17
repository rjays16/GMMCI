var bDone = false;

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
		var dBody=document.getElementById("order-list").getElementsByTagName("tbody")[0];
		if (dBody) {
			trayItems = 0;
			dBody.innerHTML = "";
			return true;
		}
	}
	return false;
}

function addInsuranceRow(_data) 
{
	clearOrder($('order-list'));
    var $J=jQuery.noConflict(),
        list = $J('#order-list'),
        data = $J.extend({
            providerID: '',
            providerName: '',
            insuranceNo: '',
            isPrincipal : false
        }, _data),
        body = list.find('tbody'),
        row = body.find('#row'+data['providerID']);
        console.log(row)
        
    
    if (!row.size()) {
        row = $J('<tr/>').attr('id', 'row'+data['providerID']);
        body.append(row);
    } else {
        row.html('');
    }

    row.append($J('<input/>').attr({
    	id: 'rowID'+data['providerID'],
    	name: 'items[]',
		type: 'hidden',
		value: data['providerID']
	})).append($J('<input/>').attr({
    	id: 'rowNr'+data['providerID'],
    	name: 'nr[]',
		type: 'hidden',
		value: data['insuranceNo']
	})).append($J('<input/>').attr({
    	id: 'rowis_principal'+data['providerID'],
    	name: 'is_principal[]',
		type: 'hidden',
		value: (data['isPrincipal'] == true) ? 1 : 0
	}));
    
    row.append(
        $J('<td/>')
    ).append(
        $J('<td/>').text(data.providerName)
    ).append(
        $J('<td/>').text(data.insuranceNo)
    ).append(
        $J('<td/>').text(data.isPrincipal ? 'YES' : 'NO')
    ).append(
        $J('<td/>')
    );
}    
            
            

function appendOrder(list,details, ballowedit) {

	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			var src;
			var lastRowNum = null,
					items = document.getElementsByName('items[]');
					dRows = dBody.getElementsByTagName("tr");

			if (details) {
				var id = details.id;
				if (items) {

					for (var i=0;i<items.length;i++) {
						if (items[i].value == details.id) {
							var itemRow = $('row'+items[i].value);
							document.getElementById('rowNr'+id).value = details.nr;
							document.getElementById('rowis_principal'+id).value = details.isPrincipal;
							document.getElementById('name'+id).innnerHTML = details.name;
							document.getElementById('inspin'+id).innerHTML = details.nr;
							document.getElementById('insprincipal'+id).innerHTML = details.isPrincipal2;

							var name_serv = details.name;
							alert('"'+name_serv.toUpperCase()+'" is already in the list & has been UPDATED!');
							return true;
						}
					}
					if (items.length == 0)
	 					clearOrder(list);
				}

				alt = (dRows.length%2)+1;
				//alert(details.isPrincipal);
//                var edit_html = '';
//                if (ballowedit) {

				var edit_html = '<span id="edit_memberinfo_'+id+'" style="display:'+(ballowedit ? '' : 'none')+'">';
				//edit_html += '<img id="edit_insurance_'+id+'" style="cursor:pointer" src="../../images/edit.gif" onclick="edit_member_details_info('+id+');" border="0"/>';
				edit_html += '</span>';

//                }
				var brgynr = document.getElementById('brgynr').value;
				var munnr = document.getElementById('munnr').value;

				var hidden_div = '<div id="insurance_'+id+'"><input type="hidden" name="last_name[]" value="'+details.last_name+'" id="ln_'+id+'" /><input type="hidden" name="first_name[]" value="'+details.first_name+'" id="fn_'+id+'" /><input type="hidden" name="middle_name[]" value="'+details.middle_name+'" id="mn_'+id+'" />'+
  					             '<input type="hidden" name="street[]" value="'+details.street+'" id="st_'+id+'" /><input type="hidden" name="barangay[]" value="'+brgynr+'" id="ba_'+id+'" /><input type="hidden" name="municipality[]" value="'+munnr+'" id="mu_'+id+'" /><input type="hidden" name="fnr[]" value="'+id+'" id="fnr_'+id+'" />'+
								         '<input type="hidden" name="inr[]" value="'+details.nr+'" id="inr_'+id+'"><input type="hidden" name="infosrc[]" value="'+details.infosrc+'" id="infosrc_'+id+'"><input type="hidden" name="principal[]" value="'+details.principal_id+'" id="principal_'+id+'"><input type="hidden" name="is_updated[]" value="'+details.is_updated+'" id="is_updated_'+id+'"></div>';

				src =
					'<tr class="wardlistrow'+alt+'" id="row'+id+'">' +
						'<input type="hidden" name="items[]" id="rowID'+id+'" value="'+id+'" />'+
						'<input type="hidden" name="nr[]" id="rowNr'+id+'" value="'+details.nr+'" />'+
						'<input type="hidden" name="is_principal[]" id="rowis_principal'+id+'" value="'+details.isPrincipal+'" />'+
						'<td class="centerAlign" id="firm_'+id+'" align="left"><a href="javascript:removeItem(\''+id+'\')"><img src="../../images/btn_delitem.gif" border="0"/></a>&nbsp;'+edit_html+'</td>'+
						'<td width="*" id="name'+id+'">'+details.name+'</td>'+
						'<td width="25%" align="right" id="inspin'+id+'">'+details.nr+'</td>'+
						'<td width="18%" class="centerAlign" id="insprincipal'+id+'">'+details.isPrincipal2+'</td>'+
						'<td id="row_column'+id+'">'+hidden_div+'</td>'+
					'</tr>';
								//'<td width="1">'+id+'</td>'+

				trayItems++;
			}
			else {
				src = "<tr><td colspan=\"10\">Insurance list is currently empty...</td></tr>";
			}
			dBody.innerHTML += src;
			return true;
		}
	}
	return false;
}

function toggleEditMemberInfoIcon(id) {
	$('addinsurance').click();
}

function removeItem(id) {
	//commented by ken 2/12/2014
	//enc = $('encounter_nr').value;
	// xajax_clearEncCategory(enc);
	var destTable, destRows;
	var table = $('order-list');
	var rmvRow=document.getElementById("row"+id);
	if (table && rmvRow) {
		$('rowID'+id).parentNode.removeChild($('rowID'+id));
		$('rowNr'+id).parentNode.removeChild($('rowNr'+id));
		var rndx = rmvRow.rowIndex;
		table.deleteRow(rmvRow.rowIndex);
		reclassRows(table,rndx);
	}
	var items = document.getElementsByName('items[]');
	if (items.length == 0){
		emptyIntialRequestList();
	}
}

function emptyIntialRequestList(){
	clearOrder($('order-list'));
	appendOrder($('order-list'),null);
}

function emptyTray() {
	clearOrder($('order-list'));
	appendOrder($('order-list'),null);
	refreshDiscount();
}

//added by VAN 08-15-08
function prepareAdd(id) {
	var details = new Object();
	details.id = $('rowID2'+id).value;
	details.name = $('name2'+id).innerHTML;
	details.nr= $('rowNr2'+id).value;

	//alert($('isPrincipal'+id).checked);
	if ($('rowis_principal2'+id).value=='1'){
		details.isPrincipal2 = "YES";
		details.isPrincipal = 1;
	}else{
		details.isPrincipal2 = "NO";
		details.isPrincipal = 0;
	}
	//alert("details.isPrincipal2 = "+details.isPrincipal2);
	var list = document.getElementById('order-list');

//	result = appendOrder(list,details,($('noPrincipal').value == '1') ? 1 : 0); -- Replaced by LST with line below ... 06.13.2012
		result = appendOrder(list,details,($('rowis_principal2'+id).value == '1') ? 0 : 1);
    bDone = false;
}

function setNoPrincipalFlag(nflag) {
    $('noPrincipal').value = nflag;
    bDone = true;
}
//---------------------