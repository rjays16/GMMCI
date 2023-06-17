function add_specific_anesthesia(mode)
{  
	if(mode=="new"){
		if(validate("add"))
		{
			$('row_specific_null').style.display = "none";
			var rowSrc = "";
			var spec_id = $('specific_id').value;
			var spec_name = $('specific_name').value;
			
			rowSrc = "<tr id='row_specific"+spec_id+"'>"+
				"<td id='rowspec_id"+spec_id+"'><span id='spec_id"+spec_id+"'>"+spec_id+"</span></td>"+
				"<td id='rowspec_name"+spec_id+"'><span id='spec_name"+spec_id+"'>"+spec_name+"</span></td>"+
				"<td><img src='../../../images/cashier_edit.gif' name='edit' class='link' onclick='edit_specific(\""+spec_id+"\")'/>&nbsp;"+
				"<img src='../../../images/cashier_delete_small.gif' name='delete' class='link' onclick='delete_specific(\""+spec_id+"\")'/></td>"+
				"<td></td>"+
				"<input type='hidden' name='specific_id_hidden' id='spec_idhid"+spec_id+"' value='"+spec_id+"'/>"+
				"<input type='hidden' name='specific_name_hidden' id='spec_namehid"+spec_id+"' value='"+spec_name+"'/>"+
			"</tr>";
			
			$('specific_list-body').innerHTML+=rowSrc;
		}
		else
		{
			return false;
		}
	}
	else if(mode=="edit"){                       
	 var spec_id=$('specific_id').value;
	 var spec_name=$('specific_name').value;      	 
	 var cat_id=$('category_id').value;      
	 xajax_anesthesia_new_specific_save(spec_id, spec_name,cat_id);
	 
	}                   
}

function edit_specific(id)
{
	var type="id";
	$('rowspec_id'+id).innerHTML = "<input type='text' class='segInput' id='spec_id"+id+"' size='20' value='"+$('spec_id'+id).innerHTML+"' onblur='save_part_specific(\""+type+"\",this.id,\""+id+"\")'/>";
	type="name";
	$('rowspec_name'+id).innerHTML = "<input type='text' class='segInput' id='spec_name"+id+"' size='20' value='"+$('spec_name'+id).innerHTML+"' onblur='save_part_specific(\""+type+"\",this.id,\""+id+"\")'/>";
}

function save_part_specific(type, text_id, row_id)
{
	var val = $(text_id).value;
	if(type=='id')
	{
		$('rowspec_id'+row_id).innerHTML = "<span id='"+text_id+"'>"+val+"</span>";
		$('spec_idhid'+row_id).value = val;
	}
	else if(type=='name')
	{
		$('rowspec_name'+row_id).innerHTML = "<span id='"+text_id+"'>"+val+"</span>";
		$('spec_namehid'+row_id).value = val;
	}
}

function delete_specific(id)
{
	var tbname = $('specific_list').getElementsByTagName('tbody').item(0);
	var child = $('row_specific'+id);
	tbname.removeChild(child)
	if(tbname.getElementsByTagName('tr').length <= 1)
	{
		$('specific_list-body').innerHTML = '<tr id="row_specific_null" style="display"><td colspan="5" style="">No specific anesthesia added...</td></tr>';
	}
}

function save_new_procedure()
{
	var spec_ids = document.getElementsByName('specific_id_hidden');
	var spec_names = document.getElementsByName('specific_name_hidden');
	var id_array = new Array();
	var name_array = new Array();
	var category_id = $('category_id').value;
	var category_name = $('category_name').value;
	for(i=0;i<spec_ids.length;i++)
	{
		id_array[i] = spec_ids[i].value;
		name_array[i] = spec_names[i].value;
	}
	
	var ans = confirm("Save this item?");
	if(ans)
	{
		xajax_anesthesia_procedure_save(id_array, name_array, category_id, category_name);
	}
	else
	{
		return false;
	}
}



function validate(mode)
{
	if($('category_id').value=='')
	{
		alert("Please specify the anesthesia category id.");
		return false;
	}
	else if($('category_name').value=='')
	{
		alert("Please specify the anesthesia category name.");
		return false;
	}
	else if($('specific_id').value=='')
	{
		alert("Please specify the anesthesia specific id.");
		return false;
	}
	else if($('specific_name').value=='')
	{
		alert("Please specify the anesthesia specific name.");
		return false;
	}
	else
	{
		if(mode=="add")
		{
			var spec_ids = document.getElementsByName('specific_id_hidden');
			for(i=0;i<spec_ids.length;i++)
			{
				if((spec_ids[i].value==$('specific_id').value))
				{
					alert("Anesthesia specific id is already in the list.");
					return false;
				}
			}
			return true;
		}
		else if(mode=="new")
		{
			return true;
		}
	}
}


//start celsy------------
			 
function update_procedure(mode)
{
	if(mode=="edit")
	{                      
		if($('category_name').value=='')
		{
			alert("Please specify the anesthesia category name.");    
		}
		else
		{
			xajax_anesthesia_edit_category_name($('category_name').value, $('category_id').value);         			
		}
	}
}

function delete_category(id)
{  
	xajax_anesthesia_category_delete(id);        
}

function delete_specific(spec_id, spec_name)
{                          
	var cat_id = $('category_id').value; 
	xajax_anesthesia_specific_delete(spec_id, spec_name, cat_id);     
}

function edit_specific_data(specific_id, specific_name)
{
	var new_spec_id = $('new_specific_id').value;
	var new_spec_name = $('new_specific_name').value;
	var cat_id = $('category_id').value; 
	//alert("spec id: "+new_spec_id+"!  cat_id: "+cat_id+"!   old spec id: "+specific_id+"!  spec name: "+specific_name+"!");         
	xajax_anesthesia_specific_edit(specific_id, specific_name, new_spec_id, new_spec_name,cat_id);    
}             