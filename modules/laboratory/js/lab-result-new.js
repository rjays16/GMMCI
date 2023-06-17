function populate(data){
	if(data){
		$('cancel_btn').style.display = '';
		$('print_btn').style.display = '';
		CreateTableHeader(data);
		document.getElementById('medtech').value = data.medtech;
		document.getElementById('pathologist').value = data.patho;
		if(data.confidential == 1)
			document.getElementById('is_confidential').checked = true;
	}
}

function CreateTableHeader(data){
	var divId = 'form-lab-result';
	var tableId = 'lab-result-list'+data.form_id;
	var div = document.getElementById(divId);

	if(document.getElementById(tableId)){
		printRequestlist(divId,tableId, data);
	}
	else{
		var tableSrc =
			'<table width="100%" class="segList" id="'+tableId+'" cellpadding="0" cellspacing="0">'+
				'<thead>'+
					'<tr class="nav">'+
						'<th colspan="7" style="text-align: left;">Form Name: '+data.form_name+' ('+data.form_name+')</th>'+
					'</tr>'+
				'</thead>'+
				'<thead>'+
					'<tr>'+
						'<th width="18"></th>'+
						'<th align="center">Name</th>'+
						'<th align="center">Input Amount</th>'+
						'<th align="center">Status</th>'+
						'<th align="center">SI Range/CU Range</th>'+
					'</tr>'+
				'</thead>'+
				'<tbody style="max-height:100px; overflow-y:auto; overflow-x:hidden">'+
				'</tbody>'+
			'</table><br/>';
		div.insert(tableSrc);

		printRequestlist(divId,tableId, data);
	}
}

function printRequestlist(divId, tableId, details)
{
	var div = document.getElementById(divId);
	var table = document.getElementById(tableId);
	var dBody = table.select("tbody")[0];

	var dRows = dBody.getElementsByTagName("tr");

	if(details.si)
		var range = details.si;
	
	else if(details.cu)
		var range = details.cu;
	
	else
		var range = '';

	if(details.param_type == 'Text')
		var field = '<input type=text name="'+details.param_id+'"  id="'+details.param_id+'" value="'+details.value+'" align="MIDDLE" size=30>';
	
	if(details.param_type == 'Numeric'){
		var field = '<input type=text name="'+details.param_id+'"  id="'+details.param_id+'" value="'+details.value+'" align="MIDDLE" '+
						'onblur="checkStatus(this.value, \''+details.cu_unit+'\', \''+details.si_unit+'\', \''+details.cu_low+'\', '+ 
							'\''+details.cu_high+'\', \''+details.si_low+'\', \''+details.si_high+'\', \''+details.param_name+'\')" size=30>';
		field += '<select name="'+details.param_id+'_unit" id="'+details.param_id+'_unit">';
		if(details.si_unit){
			field += "<option value='"+details.si_unit+"'>"+details.si_unit+"</option>";
		}
		if(details.cu_unit)
		{
			field += "<option value='"+details.cu_unit+"'>"+details.cu_unit+"</option>";
		}
		field += '</select>';
	}
	
	if(details.param_type == 'Checkbox')
		var field = '<input type=checkbox checked name="'+details.param_id+'" id="'+details.param_id+'">';
	
	if(details.param_type == 'Long Text')
		var field = '<textarea cols=30 name="'+details.param_id+'"  id="'+details.param_id+'" value="'+details.value+'"></textarea>';
	
	var delete_btn = '<img id="delete'+details.param_id+'" name="delete'+details.param_id+'" src="../../images/btn_delitem.gif" style="cursor:pointer" border="0" onClick="deleteItem('+details.param_id+', '+details.form_id+');">';
	
	if(details){
		if(!document.getElementById('ip_row'+details.param_id)){
			alt = (dRows.length%2>0) ? ' class="alt"':''
			rowSrc =
				'<input type="hidden" name="items[]" id="rowID'+details.param_id+'" value="'+details.param_id+'" />'+
				'<tr class="'+alt+'" id="ip_row'+details.param_id+'">'+
					'<td class="centerAlign" width="1%">'+delete_btn+'</td>'+
					'<td class="centerAlign" width="10%">'+details.param_name+'</td>'+
					'<td class="leftAlign" width="40%">'+field+'</td>'+
					'<td class="centerAlign" width="15%"><span name="status_'+details.param_name+'"  id="status_'+details.param_name+'" ></span></td>'+
					'<td class="centerAlign" width="8%">'+range+'</td>'+
				'</tr>';
			dBody.insert(rowSrc);
		}
	}
}
