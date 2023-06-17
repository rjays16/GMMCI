function initialize()
{
	
var pid = window.parent.document.getElementById('pid').value;

ListGen.create( $('Form_list'), {
		id: 'test_srv',
		url: '../../modules/laboratory/ajax/lab-manual-result.php',
		params: {'search':$('Group_Form_search').value, 'pid':pid, 'view-mode': 'Group_Form'},
		width: 675,
		height: 290,
		autoLoad: true,
		columnModel: [
			{
				name: 'param_id',
				label: 'SELECT',
				width: 75

			},
			{
				name: 'param_name',
				label: 'Form Name',
				width: 200,
				sorting: ListGen.SORTING.asc,
				sortable: true
			},
			{
				name: 'param_gender',
				label: 'Gender',
				width: 100,
				//sorting: ListGen.SORTING.asc,
				sortable: false
			},
			{
				name: 'param_type',
				label: 'Type',
				width: 100,
				sortable: false
			},
			{
				name: 'SI_range',
				label: 'SI Range',
				width: 100,
				sortable: false
			},
			{
				name: 'CU_range',
				label: 'CU Range',
				width: 100,
				sortable: false
			}
		]
	});
}

function search(btnId)
{
	var id = "";
	var pid = window.parent.document.getElementById('pid').value;

	if(btnId=="Group_Form_search")
	{
		id="Form_list";
		$(id).list.params={'search_service':$('Group_Form_search').value, 'pid':pid, 'view-mode':'Group_Form'}
	}

	$(id).list.refresh();
}



function openGroupTray(mode, caption, id, name)
{
	var params="mode="+mode;
	if(mode=="edit")
		params+="&group_id="+id+"&group_name="+name;

	return overlib(
		OLiframeContent('../../modules/laboratory/test_manager/seg_lab_test_group_tray.php?'+params,
		650, 400, 'fWizard', 0, 'auto'),
		WIDTH,650, TEXTPADDING,0, BORDER,0,
		STICKY, SCROLL, CLOSECLICK, MODAL,
		CLOSETEXT, '<img src="../../../images/close_red.gif" border=0 >',
		CAPTIONPADDING,2,DRAGGABLE,
		CAPTION,caption,
		MIDX,0, MIDY,0,
		STATUS, caption);
}

function openAddParamTray(code, group_id, group_name)
{
	return overlib(
		OLiframeContent('../../modules/laboratory/test_manager/seg_lab_test_service_tray.php?service_code='+code+'&group_id='+group_id+'&group_name='+group_name,
		650, 350, 'fWizard', 0, 'no'),
		WIDTH,550, TEXTPADDING,0, BORDER,0,
		STICKY, SCROLL, CLOSECLICK, MODAL,
		CLOSETEXT, '<img src="../../../images/close_red.gif" border=0 >',
		CAPTIONPADDING,2,DRAGGABLE,
		CAPTION, 'View Service Parameter',
		MIDX,0, MIDY,0,
		STATUS, 'View Service Parameter');
}

function deleteGroup(id)
{
	var reply = confirm("Delete this laboratory group test?");
	if(reply)
	{
		xajax_deleteTestGroup(id);
	}else
	{
		return false;
	}
}

function outputResponse(rep)
{
	alert(rep);
	$('test_grp_list').list.refresh();
}

function tooltip (text) {
	return overlib(text,WRAP,0,HAUTO,VAUTO, BGCLASS,'olTooltipBG', FGCLASS,'olTooltipFG', TEXTFONTCLASS,'olTooltipTxt', SHADOW,0, SHADOWX,2, SHADOWY,2, SHADOWOPACITY, 25);
}
function AddForm(value){
	if(value=="Add_Form")
	{
		id="Form_list";
	}
	var FormName = $('Group_Form_search').value;
	
	xajax_addNewFormGroup(FormName,id);
}

function RefreshList(msg, id){
	alert(msg);
	$(id).list.refresh();
}

function DeleteFormGroup(id, name){
	var poliam = confirm("Are you sure you wanna delete "+name+" Form Group?");
	var table_id = "Form_list";
	if(poliam)
		xajax_DeleteFormGroup(id, name, table_id);
	
}

function updateFormGroup(id, name){
	var table_id = "Form_list";
	var person=prompt("Update this Form", name);
	alert(person);
	if(person == name && person == "")
		$(table_id).list.refresh();
	else
		xajax_UpdateFormName(id, person, table_id);
}

function CreateTableHeader(data){
	var divId = 'form-lab-result';
	var tableId = 'lab-result-list'+data.form_id;
	var div = window.parent.document.getElementById(divId);

	if(window.parent.document.getElementById(tableId)){
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
	var div = window.parent.document.getElementById(divId);
	var table = window.parent.document.getElementById(tableId);
	var dBody = table.select("tbody")[0];

	var dRows = dBody.getElementsByTagName("tr");

	if(details.si)
		var range = details.si;
	
	else if(details.cu)
		var range = details.cu;
	
	else
		var range = '';

	if(details.param_type == 'Text')
		var field = '<input type=text name="'+details.param_id+'"  id="'+details.param_id+'" value="" align="MIDDLE" size=30>';
	
	if(details.param_type == 'Numeric'){
		var field = '<input type=text name="'+details.param_id+'"  id="'+details.param_id+'" value="" align="MIDDLE" '+
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
		var field = '<input type=checkbox name="'+details.param_id+'"  id="'+details.param_id+'">';
	
	if(details.param_type == 'Long Text')
		var field = '<textarea cols=30 name="'+details.param_id+'"  id="'+details.param_id+'"></textarea>';
	
	var delete_btn = '<img id="delete'+details.param_id+'" name="delete'+details.param_id+'" src="../../images/btn_delitem.gif" style="cursor:pointer" border="0" onClick="deleteItem('+details.param_id+', '+details.form_id+');">';
	
	if(details){
		if(!window.parent.document.getElementById('ip_row'+details.param_id)){
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

function removeRow(data){
	var table = window.parent.document.getElementById('lab-result-list'+data.form_id);
	var rmvRow = window.parent.document.getElementById('ip_row'+data.param_id);
	var dBody = table.select("tbody")[0];

	if (table && rmvRow) {
		var rndx = rmvRow.rowIndex-1;
		table.deleteRow(rmvRow.rowIndex);
	}

	var items = dBody.getElementsByTagName('tr');
	if (items.length == 0){
		emptyIntialRequestList(data);
	}
}

function emptyIntialRequestList(data){
	var table = window.parent.document.getElementById('lab-result-list'+data.form_id);
	if(table){
		table.parentNode.removeChild(table);
	}
}

function selectFromGroup(id, form_id, name, gender, type, si_range, cu_range, form_name, SI_unit, CU_unit, SI_low, SI_high, CU_low, CU_high, sex){
	data = new Object();
	data.param_name = name;
	data.param_id = id;
	data.gender = gender;
	data.param_type = type;
	data.si = si_range;
	data.cu = cu_range;
	data.form_id = form_id;
	data.form_name = form_name;
	data.cu_unit = CU_unit;
	data.si_unit = SI_unit;
	data.cu_low = CU_low;
	data.cu_high = CU_high;
	data.si_low = SI_low;
	data.si_high = SI_high;
	if(sex == gender || gender == 'Both'){
	if($('selectfrom_'+id).checked){
		CreateTableHeader(data);
	}
	else
		removeRow(data);
}	else if(sex == ''){
		if($('selectfrom_'+id).checked){
			CreateTableHeader(data);
		}
		else
			removeRow(data);
	}
	else{
		$('selectfrom_'+id).checked = false; 
		alert('Parameter cannot be added. The gender is mismatch.');
	}
}