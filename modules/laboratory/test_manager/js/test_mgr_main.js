function initialize()
{
	//list gen for test groups
	ListGen.create( $('test_grp_list'), {
		id: 'test_grp',
		//url: '../../../modules/laboratory/test_manager/ajax/ajax_list_groups.php',
		url: '../../../modules/laboratory/test_manager/ajax/ajax_list_services.php',
		params: {'search':$('group_search').value, 'view-mode': 'with_group'},
		width: 710,
		height: 270,
		autoLoad: true,
		columnModel: [
			{
				name: 'group_name',
				label: 'Group',
				width: 100,
				sorting: ListGen.SORTING.asc,
				sortable: true
			},
			{
				name: 'srv_code',
				label: 'Service Code',
				width: 100,
				sorting: ListGen.SORTING.asc,
				sortable: true
			},
			{
				name: 'srv_name',
				label: 'Test Service',
				width: 200,
				sorting: ListGen.SORTING.asc,
				sortable: true
			},
			/*
				name: 'srv_charge',
				label: 'Charge',
				width: 100,
				styles: {
					textAlign: 'right'
				},
				sortable: false
			},
			{
				name: 'srv_stat_grp',
				label: 'Has Group Assigned',
				width: 120,
				//sorting: ListGen.SORTING.asc,
				sortable: false,
				styles:{
					textAlign: 'center',
					color: '#DD0000'
				}
			},*/
			{
				name: 'srv_stat_param',
				label: 'Has Parameter',
				width: 100,
				//sorting: ListGen.SORTING.asc,
				sortable: false,
				styles:{
					textAlign: 'center',
					color: '#DD0000'
				}
			},
			{
				name: 'options',
				label: 'Options',
				width: 200,
				sortable: false
			}
		]
		/*columnModel: [
			{
				name: 'grp_code',
				label: 'Group Code',
				width: 130,
				sortable: false
			},
			{
				name: 'grp_name',
				label: 'Test Group',
				width: 200,
				sorting: ListGen.SORTING.asc,
				sortable: true
			},
			{
				name: 'options',
				label: 'Options',
				width: 100,
				styles: {
					textAlign: 'center'
				},
				sortable: false
			}
		]*/
	});

	//list gen for test services
	ListGen.create( $('test_srv_list'), {
		id: 'test_srv',
		url: '../../../modules/laboratory/test_manager/ajax/ajax_list_services.php',
		params: {'search':$('service_search').value, 'view-mode': 'without_group'},
		width: 620,
		height: 290,
		autoLoad: true,
		columnModel: [
			{
				name: 'srv_code',
				label: 'Service Code',
				width: 100,
				sorting: ListGen.SORTING.asc,
				sortable: true
			},
			{
				name: 'srv_name',
				label: 'Test Service',
				width: 200,
				sorting: ListGen.SORTING.asc,
				sortable: true
			},
			/*{
				name: 'srv_charge',
				label: 'Charge',
				width: 100,
				styles: {
					textAlign: 'right'
				},
				sortable: false
			},
			{
				name: 'srv_stat_grp',
				label: 'Has Group Assigned',
				width: 120,
				//sorting: ListGen.SORTING.asc,
				sortable: false,
				styles:{
					textAlign: 'center',
					color: '#DD0000'
				}
			},*/
			{
				name: 'srv_stat_param',
				label: 'Has Parameter',
				width: 100,
				//sorting: ListGen.SORTING.asc,
				sortable: false,
				styles:{
					textAlign: 'center',
					color: '#DD0000'
				}
			},
			{
				name: 'options',
				label: 'Options',
				width: 200,
				sortable: false
			}
		]
	});
ListGen.create( $('Form_list'), {
		id: 'test_srv',
		url: '../../../modules/laboratory/test_manager/ajax/ajax_list_resultforms.php',
		params: {'search':$('service_search').value, 'view-mode': 'Group_Form'},
		width: 620,
		height: 290,
		autoLoad: true,
		columnModel: [
			{
				name: 'form_id',
				label: 'Form Id',
				width: 100,
				sorting: ListGen.SORTING.asc,
				sortable: true
			},
			{
				name: 'form_name',
				label: 'Form Name',
				width: 200,
				sorting: ListGen.SORTING.asc,
				sortable: true
			},
			/*{
				name: 'srv_charge',
				label: 'Charge',
				width: 100,
				styles: {
					textAlign: 'right'
				},
				sortable: false
			},
			{
				name: 'srv_stat_grp',
				label: 'Has Group Assigned',
				width: 120,
				//sorting: ListGen.SORTING.asc,
				sortable: false,
				styles:{
					textAlign: 'center',
					color: '#DD0000'
				}
			},*/
			{
				name: 'form_status',
				label: 'Status',
				width: 100,
				//sorting: ListGen.SORTING.asc,
				sortable: false,
				styles:{
					textAlign: 'center',
					color: '#DD0000'
				}
			},
			{
				name: 'options',
				label: 'Options',
				width: 200,
				sortable: false
			}
		]
	});
}

function search(btnId)
{
	var id = "";
	if(btnId=="search_grp")
	{
		id="test_grp_list";
		$(id).list.params={'search_service':$('group_search').value,'search_section':$('section_with_grp').value, 'view-mode':'with_group'};
	}
	if(btnId=="search_srv")
	{
		id="test_srv_list";
		$(id).list.params={'search_service':$('service_search').value,'search_section':$('section_witho_grp').value, 'view-mode':'without_group'}
	}
	if(btnId=="Group_Form_Search_btn")
	{
		id="Form_list";
		$(id).list.params={'search_service':$('Group_Form_search').value, 'view-mode':'Group_Form'}
	}

	$(id).list.refresh();
}

function searchBySection(id)
{
	var listid="";
	if(id=="section_with_grp")
	{
		listid='test_grp_list';
		$(listid).list.params={'search_section':$(id).value, 'view-mode':'with_group'};
	}
	if(id=="section_witho_grp")
	{
		listid='test_srv_list';
		$(listid).list.params={'search_section':$(id).value, 'view-mode':'without_group'};
	}
	if(id=="section_witho_grp2")
	{
		listid='Form_list';
		$(listid).list.params={'search_section':$(id).value, 'view-mode':'Group_Form'};
	}
	$(listid).list.refresh();
}

function openGroupTray(mode, caption, id, name)
{
	var params="mode="+mode;
	if(mode=="edit")
		params+="&group_id="+id+"&group_name="+name;

	return overlib(
		OLiframeContent('../../../modules/laboratory/test_manager/seg_lab_test_group_tray.php?'+params,
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
		OLiframeContent('../../../modules/laboratory/test_manager/seg_lab_test_service_tray.php?service_code='+code+'&group_id='+group_id+'&group_name='+group_name,
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
}function AddForm(value){
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