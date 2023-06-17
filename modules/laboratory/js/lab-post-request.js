var trayItems = 0;

function pSearchClose() {
	cClick();  //function in 'overlibmws.js'
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

function preset(refno, service_code){
	//alert(refno+", "+service_code);
    var pid = $('pid').value;
    
	if (refno)
		xajax_populateRequestListSerialItem(refno, service_code, pid);
}

//function initializeTable(service_code, no_serial, index, in_lis, no_repeat){
function initializeTable(data){
	var details = new Object();

	var list = document.getElementById('order-list');
	 //alert(service_code+", "+no_serial+", "+index+", "+in_lis);
	details.no_serial = data['no_serial'];
	details.index = data['index'];
	details.id = data['service_code'];
	details.in_lis = data['in_lis'];
	details.no_repeat = data['no_repeat'];
    
    details.dateserved = data['dateserved'];
    details.nth_take_catered = data['nth_take_catered'];
    details.lis_order_no = data['lis_order_no'];

    details.has_result = data['has_result'];


	result = appendListItem(list,details);
}

function level_label(index){
	switch(index){
		case '1' :  label_index = 'First'; break;
		case '2' :  label_index = 'Second'; break;
		case '3' :  label_index = 'Third'; break;
		case '4' :  label_index = 'Fourth'; break;
		case '5' :  label_index = 'Fift'; break;
		case '6' :  label_index = 'Sixth'; break;
		case '7' :  label_index = 'Seventh'; break;
		case '8' :  label_index = 'Eighth'; break;
		case '9' :  label_index = 'Ninth'; break;
		case '10' :  label_index = 'Tenth'; break;
	}

	return label_index;
}

function setEnable(id, no_repeat){
	//alert(id);
	if ($('is_served'+id).checked){
			$('date_served_trigger'+id).disabled = false;
			$('date_served_clear'+id).disabled = false;

			if (no_repeat>0){
				$('submit_btn'+id).disabled = true;
				$('repeat_btn'+id).disabled = false;
			}else{
				$('submit_btn'+id).disabled = false;
				$('repeat_btn'+id).disabled = true;
			}
            
            $('date_served'+id).value = $('currentdate').value;

	}else{
			$('date_served_trigger'+id).disabled = true;
			$('date_served_clear'+id).disabled = true;
			$('submit_btn'+id).disabled = true;
			$('repeat_btn'+id).disabled = true;
            $('date_served'+id).value = '';
	}
}

function submitRequest(id, service_code, index){
	 //alert('submit = '+id);
	 var refno = $('refno').innerHTML;

	 if ($('date_served'+id).value==''){
			alert('Please indicate the date of service.');
			$('date_served'+id).focus();
			//$('date_served_trigger'+id).focus();
			return false;
	 }
	 //return true;
	 //alert(refno+", "+$('date_served'+id).value+", "+service_code+", "+index);
     xajax_submitrequest(refno, service_code, $('date_served'+id).value, index);
}

function deleteRequest(id, service_code, index, lis_order_no){
     //alert('submit = '+id);
     var refno = $('refno').innerHTML;
     
     //alert('DELETE === '+refno+", "+$('date_served'+id).value+", "+service_code+", "+index);
     var answer = confirm("Are you sure you want to delete the laboratory item with a \n reference no. "+(refno)+", service code '"+service_code+"' and with the "+(level_label(index)).toLowerCase()+" take?");
     if (answer){
        xajax_deleterequest(refno, service_code, index, lis_order_no);
     }
}

function reloadPage(){
    window.location.reload();
}

function repeatRequest(id, service_code){
	 alert('repeat = '+id);
	 var refno = $('refno').innerHTML;

	 if ($('date_served'+id).value==''){
			alert('Please indicate the date of repeat service.');
			$('date_served'+id).focus();
			//$('date_served_trigger'+id).focus();
			return false;
	 }

	 //alert(refno+", "+$('date_served'+id).value+", "+service_code);
}

function reset(id){
	$('date_served'+id).value='';
}


/*function updateFields(cal) {
	var date = cal.selection.get();
	if (date) {
		date = Calendar.intToDate(date);
		document.getElementById("f_date").value = Calendar.printDate(date, "%Y-%m-%d");
	}
	document.getElementById("f_hour").value = cal.getHours();
	document.getElementById("f_minute").value = cal.getMinutes();
};*/

function set_calendar(id){
			var datenow = $('datenow').value;

			// disable from day after current day and onward
			Calendar.setup ({
				inputField: 'date_served'+id,
				//dateFormat: '%B %e, %Y',
				dateFormat: '%m/%d/%Y %I:%M%P',
				trigger: 'date_served_trigger'+id,
				showTime: true,
				onSelect: function() {this.hide() },
				/*disabled: function(date) {
						if (date.getDay() == 5) {
								return true;
						} else {
								return false;
						}
				} */
                fdow : 0,
                min: eval(datenow)
				//max: eval(datenow)
			});

			// onSelect     : updateFields,
			// onTimeChange : updateFields
}

function appendListItem(list,details) {
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0];

		if (dBody) {
			var lastRowNum = null,
					items = document.getElementsByName('items[]');
					dRows = dBody.getElementsByTagName("tr");
			var checkox, label, date_served, submit_col, repeat_col, no_repeat_col, index_label;
			var alt = (dRows.length%2)+1;
			var id = details.id;

			var no_repeat = details.no_repeat;
			var javascript_calc, prev_no_repeat;

			if (!no_repeat)
				no_repeat = 0;

			//temp.. no_repeat should be came from the db
			/*if ((details.index >= 0) && (details.index <= 3))
				 no_repeat = 1;
			else
				 no_repeat = 0;*/

			var disabled_chk = "disabled";

			if (no_repeat >= 1){
				disabled_chk = "";
			}else{
				/*if (details.index==1)
					disabled_chk = "";
				else{
					if (details.index > 1 )
						prevIndex = parseInt(details.index) - 1;
					else
						prevIndex = 1;

					if ($('no_repeat_col'+id+prevIndex).innerHTML==1)
						disabled_chk = "";
					else
						disabled_chk = "disabled";
				}*/
                var showsubmit = 1;
                if (details.nth_take_catered==details.index){
                    disabled_chk = "disabled";
                    ischecked = "checked";    
                    showsubmit = 0;
                }else{
                    disabled_chk = "";
                    ischecked = "";    
                }    
			}

			checkox = '<input '+disabled_chk+' '+ischecked+' type="checkbox" id="is_served'+id+details.index+'" name="is_served'+id+details.index+'" onClick="setEnable(\''+id+details.index+'\',\''+no_repeat+'\');">';
			index_label = level_label(details.index);
			label =  index_label+" Test";

			var disabled = "disabled";

			date_served = '<input type="text" readonly="readonly" size="20" value="'+details.dateserved+'" class="segInput" id="date_served'+id+details.index+'" name="date_served'+id+details.index+'">'+
										'<button '+disabled+' class="segButton" id="date_served_trigger'+id+details.index+'"><img height="16" width="16" border="0" src="../../gui/img/common/default/calendar.png">Set</button>'+
										'<button '+disabled+' onclick="reset(\''+id+details.index+'\'); return false;" class="segButton" id="date_served_clear'+id+details.index+'">'+
										'<img height="16" width="16" border="0" src="../../gui/img/common/default/delete.png">Clear</button>';

            if (showsubmit==1)
			    submit_col = '<button '+disabled+' class="segButton" id="submit_btn'+id+details.index+'" onclick="submitRequest(\''+id+details.index+'\',\''+id+'\',\''+details.index+'\')"><img height="16" width="16" border="0" src="../../images/button_split_small.png">Submit</button>';
            else{
                disabled_delete = "";
                if (details.has_result==1)
                    disabled_delete = "disabled";
           
                submit_col = '<button class="segButton" '+disabled_delete+' id="delete_btn'+id+details.index+'" onclick="deleteRequest(\''+id+details.index+'\',\''+id+'\',\''+details.index+'\',\''+details.lis_order_no+'\')"><img height="16" width="16" border="0" src="../../images/cashier_delete_small.gif">Delete</button>';
            }
           if (details.has_result==1){
                var pid = $('pid').value;
                result = '<img name="serial_result'+id+'" id="serial_result'+id+'" src="../../images/cashier_view.gif" border="0" onClick="view_serialResult(\''+pid+'\',\''+details.lis_order_no+'\');" style="cursor:pointer" title="SERIAL RESULT">';
           }else{
                result = '<img name="serial_result'+id+'" id="serial_result'+id+'" src="../../images/cashier_lock.gif" border="0" title="NO RESULT YET!">';    
           } 
            
			//repeat_col = '<button '+disabled+' class="segButton" id="repeat_btn'+id+details.index+'" onclick="repeatRequest(\''+id+details.index+'\',\''+id+'\')"><img height="16" width="16" border="0" src="../../images/cashier_refresh.gif">Repeat</button>';
            repeat_col = '<button disabled class="segButton" id="repeat_btn'+id+details.index+'" onclick="repeatRequest(\''+id+details.index+'\',\''+id+'\')"><img height="16" width="16" border="0" src="../../images/cashier_refresh.gif">Repeat</button>';

			if (details) {
				 src =
						'<tr class="wardlistrow'+alt+'" id="row'+id+details.index+'"> '+
						'<td align="center">'+checkox+'</td>'+
						'<td align="centerAlign">'+label+'</td>'+
						'<td align="center" id="dateserved'+id+details.index+'">'+date_served+'<br \>'+'</td>'+
						'<td width="5%" id="submitbtn'+id+details.index+'" align="center">'+submit_col+'</td>'+
						/*'<td width="5%" id="repeat_col'+id+details.index+'" align="center">'+repeat_col+'</td>'+*/
						/*'<td width="5%" id="lis_order_no'+id+details.index+'" align="center">'+details.lis_order_no+'</td>'+*/
                        /*'<td width="1%" id="result'+id+details.index+'" align="center">'+result+'</td>'+*/
						'</tr>';
					trayItems++;

			}
			else {
					src = "<tr><td colspan=\"10\">Request list is currently empty...</td></tr>";
			}

			$(dBody).insert(src);
			set_calendar(id+details.index);

			document.getElementById('counter').innerHTML = items.length;

			return true;
		}
	}

	return false;
}

function emptyIntialRequestList(){
	clearOrder($('order-list'));
	appendListItem($('order-list'),null);
}

function clearOrder(list) {
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

function view_serialResult(pid, lis_order_no){
    window.open("seg-lab-result_pdf-view.php?pid="+pid+"&lis="+lis_order_no+"&showBrowser=1","viewPatientResult","left=150, top=100, width=950,height=700,fullscreen=yes,menubar=no,resizable=yes,scrollbars=yes");
}