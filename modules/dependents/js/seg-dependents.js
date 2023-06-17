
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

function clearDependents(list) {	
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

function appendDependents(list,details) {
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0];
		//alert('dBody = '+dBody.innerHTML);
		
		if (dBody) {
				
			var lastRowNum = null,
					deps = document.getElementsByName('deps[]');
					dRows = dBody.getElementsByTagName("tr");
			
			if (details.id) {
            //if (details) {
				var id = details.id,
					name = details.name,
					sex = details.sex;
//alert('details = '+details.sex);
				alt = (dRows.length%2)+1;
				
				if (deps) {
					for (var i=0;i<deps.length;i++) {
						if (deps[i].value == details.id) {
							document.getElementById('relationship'+id).innerHTML = details.relationship.toUpperCase();
							document.getElementById('relation'+id).value = details.relationship.toUpperCase();
							alert('"'+details.name.toUpperCase()+'" is already in the list & has been UPDATED!');
							return true;
						}
					}
					if (deps.length == 0)
	 					clearDependents(list);
				}

			delitemImg = '<a href="javascript: nd(); removeItem(\''+id+'\');">'+
							 '	<img src="../../images/btn_delitem.gif" border="0"/></a>';		
							 
			if (sex=='m')
				sexImg = '<img src="../../gui/img/common/default/spm.gif" border="0" />';
			else if (sex=='f')
				sexImg = '<img src="../../gui/img/common/default/spf.gif" border="0" />';
			else
				sexImg = '';				 
			
			src = 
					'<tr class="wardlistrow'+alt+'" id="row'+id+'"> '+
					'<input type="hidden" name="dep_id[]" id="depid'+id+'" value="'+details.id+'" />'+
					'<input type="hidden" name="relation[]" id="relation'+id+'" value="'+details.relationship+'" />'+
					'<td width="4%"><input type="hidden" name="deps[]" id="rowID'+id+'" value="'+id+'" />'+delitemImg+'</td>'+
					'<td width="0.5%">&nbsp;</td>'+
					'<td width="10%"><span id="id'+id+'">'+details.id+'</span></td>'+
					'<td width="*">'+details.name.toUpperCase()+'</td>'+
					'<td width="15%"><span id="relationship'+id+'">'+details.relationship.toUpperCase()+'</span></td>'+
					'<td width="10%">'+details.bdate+'</td>'+
					'<td width="10%">'+details.age+'</td>'+
					'<td width="4%">'+sexImg+'</td>'+
					'<td width="10%" >'+details.status.toUpperCase()+'</td>'+
					/*'<td width="5%" align="center" >'+delitemImg+'</td>'+*/
					'</tr>';
				trayItems++;
				
			}else {
				src = "<tr><td colspan=\"11\">Dependent's list is currently empty...</td></tr>";	
				
			}

			dBody.innerHTML += src;
			document.getElementById('counter').innerHTML = deps.length;
			return true;
		}
	}
	return false;
}

function removeItem(id) {
	var destTable, destRows;
	var table = $('dep-list');
	var rmvRow=document.getElementById("row"+id);
	if (table && rmvRow) {
		$('id'+id).parentNode.removeChild($('id'+id));
		var rndx = rmvRow.rowIndex-1;
		table.deleteRow(rmvRow.rowIndex);
		reclassRows(table,rndx);
	}
		//burn added : September 13, 2007
	var deps = document.getElementsByName('deps[]');
	if (deps.length == 0){
		emptyIntialRequestList();
	}
	document.getElementById('counter').innerHTML = deps.length;
}

function emptyTray() {
	var deps = document.getElementsByName('deps[]');
	var id, i;
    var details = new Object();
		
	for (i=deps.length-1; i>=0;i--){	
		id = deps[i].value;
		$('row'+id).parentNode.removeChild($('row'+id));
	}
	
	clearDependents($('dep-list'));
	details.id=null; 
    appendDependents($('dep-list'),details);
}

function emptyIntialRequestList(){
    var details = new Object(); 
    clearDependents($('dep-list'));
    details.id=null; 
    appendDependents($('dep-list'),details);
}

function initialDependentList(dependent_pid,dependent_name,relationship,date_birth,age,sex,civil_status) {
	var details = new Object();

		details.id= dependent_pid;
		details.name= dependent_name;
		details.relationship= relationship;
		details.bdate= date_birth;
		details.age = age;
		
		details.status = civil_status;
		details.sex = sex;
		
		var list = document.getElementById('dep-list');
		
		//alert('details = '+details);
		if (details.id)
			$('mode').value = "update";
		else
			$('mode').value = "save";

		result = appendDependents(list,details);
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
	var deps = document.getElementsByName('deps[]');
		/*
        if (deps.length==0){
			alert("Please add a dependents first.");
			$('btnAdd').focus();
			return false;	
		}
       */
		$('inputform').submit();
		return true;
	}

function warnClear() {
	if ($('pid').value == "") return true;
	else return confirm('Performing this action will clear the request. Do you wish to continue?');
	
}


