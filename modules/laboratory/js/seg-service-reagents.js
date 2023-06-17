
function pSearchClose() {
	cClick(); 
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

function clearReagents(list) {	
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			trayItems = 0;
			dBody.innerHTML = "";
			return true;
		}
	}
	return false;
}

function appendReagents(list,details) {
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0];
		//alert('dBody = '+dBody.innerHTML);
		
		if (dBody) {
				
			var lastRowNum = null,
					reagents = document.getElementsByName('reagents[]');
					dRows = dBody.getElementsByTagName("tr");
			
			if (details.reagent_code) {
			//if (details) {
				
				//alert('here');
				var reagent_code = details.reagent_code,
					reagent_name = details.reagent_name,
					item_qty = details.item_qty,
					unit_id = details.unit_id,
					is_unitperpc = details.is_unitperpc;

				alt = (dRows.length%2)+1;
				
				if (reagents) {
					for (var i=0;i<reagents.length;i++) {
						if (reagents[i].value == details.reagent_code) {
							//document.getElementById('item_qty'+reagent_code).innerHTML = details.item_qty.toUpperCase();
							document.getElementById('item_qty'+reagent_code).value = details.item_qty;
							//document.getElementById('unit_id'+reagent_code).innerHTML = details.unit_id.toUpperCase();
							document.getElementById('unit_id'+reagent_code).value = details.unit_id;
							//document.getElementById('is_unitperpc'+reagent_code).innerHTML = details.is_unitperpc;
							document.getElementById('is_unitperpc'+reagent_code).value = details.is_unitperpc;
							alert('"'+details.reagent_name.toUpperCase()+'" is already in the list & has been UPDATED!');
							return true;
						}
					}
					if (reagents.length == 0)
	 					clearReagents(list);
				}

			delitemImg = '<a href="javascript: nd(); removeItem(\''+reagent_code+'\');">'+
							 '	<img src="../../images/btn_delitem.gif" border="0"/></a>';		
							 
			
			src = 
					'<tr class="wardlistrow'+alt+'" id="row'+reagent_code+'"> '+
					'<input type="hidden" name="reagent_code[]" id="reagent_code'+reagent_code+'" value="'+details.reagent_code+'" />'+
					'<td width="4%"><input type="hidden" name="reagents[]" id="rowID'+reagent_code+'" value="'+reagent_code+'" />'+delitemImg+'</td>'+
					'<td width="0.5%">&nbsp;</td>'+
					'<td width="10%"><span id="id'+reagent_code+'">'+details.reagent_code+'</span></td>'+
					'<td width="*">'+details.reagent_name.toUpperCase()+'</td>'+
					'<td width="15%"><input type="text" name="item_qty[]" id="item_qty'+reagent_code+'" size="5" value="'+details.item_qty+'"></td>'+
					'<td width="15%"><input type="text" name="unit_id[]" id="unit_id'+reagent_code+'" size="5" value="'+details.unit_id+'"></td>'+
					'<td width="15%"><input type="text" name="is_unitperpc[]" id="is_unitperpc'+reagent_code+'" size="5" value="'+details.is_unitperpc+'"></td>'+
					'</tr>';
				trayItems++;
			}else {
				src = "<tr><td colspan=\"11\">Reagent's list is currently empty...</td></tr>";	
			}

		
			dBody.innerHTML += src;
			document.getElementById('counter').innerHTML = reagents.length;
			return true;
		}
	}
	return false;
}

function removeItem(id) {
	var destTable, destRows;
	var table = $('reagent-list');
	var rmvRow=document.getElementById("row"+id);
	if (table && rmvRow) {
		$('id'+id).parentNode.removeChild($('id'+id));
		var rndx = rmvRow.rowIndex-1;
		table.deleteRow(rmvRow.rowIndex);
		reclassRows(table,rndx);
	}
		//burn added : September 13, 2007
	var reagents = document.getElementsByName('reagents[]');
	if (reagents.length == 0){
		emptyIntialRequestList();
	}
	document.getElementById('counter').innerHTML = reagents.length;
}

function emptyTray() {
	var items = document.getElementsByName('reagents[]');
	var id, i;
		
	for (i=items.length-1; i>=0;i--){	
		id = items[i].value;
		$('row'+id).parentNode.removeChild($('row'+id));
	}
	
	clearReagents($('reagent-list'));
	//appendReagents($('reagent-list'),null);
	appendReagents($('reagent-list'),0);
}

function emptyIntialRequestList(){
	clearReagents($('reagent-list'));
	//appendReagents($('reagent-list'),null);
	appendReagents($('reagent-list'),0);
}

function initialServiceReagentList(reagent_code,reagent_name,item_qty,unit_id,is_unitperpc) {
	var details = new Object();

		details.reagent_code= reagent_code;
		details.reagent_name= reagent_name;
		details.item_qty= item_qty;
		details.unit_id= unit_id;
		details.is_unitperpc = is_unitperpc;
		
		var list = document.getElementById('reagent-list');
		
		//alert('details = '+details);
		if (details.reagent_code)
			$('mode').value = "update";
		else
			$('mode').value = "save";
//alert('initial = '+details);
		result = appendReagents(list,details);
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
	var reagents = document.getElementsByName('reagents[]');
		if (reagents.length==0){
			alert("Please add a reagent first.");
			$('btnAdd').focus();
			return false;	
		}

		$('inputform').submit();
		return true;
	}

function warnClear() {
	if ($('pid').value == "") return true;
	else return confirm('Performing this action will clear the transaction. Do you wish to continue?');
	
}

