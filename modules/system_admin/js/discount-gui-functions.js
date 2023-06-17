var timeoutHandle=0;
var discountHColor="#F57A74";
var discountRowColor="#E6EFF7";
var lastEditID="",
	lastEditRow=-1,
	lastEditDesc="",
	lastEditDisc=-1;
		
function js_recolorRow() {
	var destTableBody, destRows;
	destTableBody=destTable.getElementsByTagName("tbody")[0];
	destRows=destTableBody.getElementsByTagName("tr");
	cntr=0;
	for (i in destRows) {
		destRows[i].className=((cntr%2==0)?"wardlistrow1":"wardlistrow2");
		cntr++;
	}	
}

function js_clearFields() {
	var desc, disc;
	desc=$("inputDesc");
	disc=$("inputDiscount");
	desc.value="";
	disc.value="";
}

function js_clearDiscounts() {
/*
 * GUI function for dynamically clearing the contents of the discount table
 */
	var srcTable, srcRows, srcTableBody;
	var iterator;
	if (srcTable=document.getElementById("discountTable")) {
		srcTableBody=srcTable.getElementsByTagName("tbody")[0];
		srcRows=srcTableBody.childNodes;
		// check if srcRows is valid and has more than 1 element	
		if (srcRows) {
			while (srcRows.length>0) {
				srcTableBody.removeChild(srcRows[0]);
			}
			//for (iterator=srcTable.) {
			//	srcTable.childNodes[1].removeChild(srcRows[1]);
			//}
			return true;	// success
		}
		else return false;	// fail
	}
	else return false;	// fail
}

function js_addDiscount(id, desc, discount, area_used, bill_codedareas, bill_areas, flash) {
/*
 * GUI function for dynamically appending a row to the discount table
 */
	var destRows, destTableBody, newRowDest, lastRowNo;
	var i, cntr;

	// Check if the table exists
	if (destTable=document.getElementById("discountTable")) {
		
		destTableBody=destTable.getElementsByTagName("tbody")[0];
		destRows=destTableBody.getElementsByTagName("tr");

		// Get the last row id and extract the current row no.
		if (destRows.length>0) lastRowNo=destRows[destRows.length-1].id.replace("discountRow","");
		lastRowNo=isNaN(lastRowNo)?0:(lastRowNo-0)+1;
		
		var select_area;
		
		if (id) {
			//added by VAN 06-18-08
			//document.getElementById("area_used"+lastRowNo).value = area_used;
			var selected;
			if (area_used=='P'){
				area_used_name = 'Pharmacy';
			}else if (area_used=='O'){
				area_used_name = 'Operating Room';
			}else if (area_used=='B'){
				area_used_name = 'Billing';
			}else if (area_used=='L'){
				area_used_name = 'Laboratory';
			}else if (area_used=='R'){
				area_used_name = 'Radiology';
			}else{
				area_used_name = 'All Areas';	
			}
			
			select_area = '<select id="area_used'+lastRowNo+'" name="area_used'+lastRowNo+'" style="display:none;">'+
						'		<option value="A">All</option>'+
						'		<option value="B">Billing</option>'+
						'		<option value="L">Laboratory</option>'+
						'		<option value="O">Operating Room</option>'+
						'		<option value="P">Pharmacy</option>'+
						'		<option value="R">Radiology</option>'+
						'</select>';
			
//			if (!bill_areas) bill_areas = "All Bill Areas";
			if (!bill_areas) bill_areas = "";
			
			// Prints out the HTML code of the new row
			newRowDest = '<tr class="wardlistrow1" id="discountRow'+lastRowNo+'" style="padding:1px">' +
				// Discount ID cell
				'<td align="center"><input style="width:95%;display:none" type="text" id="discountID'+lastRowNo+'" value="'+id+'"><div id="discountTID'+lastRowNo+'">'+id+'</div></td>'+
				// Description cell
				'<td align="center" align="center"><input style="width:95%;display:none;" type="text" id="discountDesc'+lastRowNo+'" value="'+desc+'"><div id="discountTDesc'+lastRowNo+'">'+desc+'</div></td>'+
				// Discount% cell
				'<td align="center"><input type="text" id="discountDiscount'+lastRowNo+'" value="'+discount+'" style="width:95%;display:none;" onblur="trimString(this); genChkDecimal(this);"><div id="discountTDiscount'+lastRowNo+'">'+(discount*100)+'%</div></td>'+
				//added by VAN 06-18-08
				// Area Used
				//'<td align="center"><input type="text" id="area_used'+lastRowNo+'" value="'+area_used+'" style="width:40px;display:none;"><div id="Tarea_used'+lastRowNo+'">'+area_used_name+'</div></td>'+
				'<td align="center">'+select_area+'<input type="hidden" name="area_usedH'+lastRowNo+'" id="area_usedH'+lastRowNo+'" value="'+area_used+'""><div id="Tarea_used'+lastRowNo+'">'+area_used_name+'</div></td>'+
				// Cell containing the list of billable areas where this discount is applied ...				
				'<td align="center"><input type="hidden" name="bill_codedareas_'+id+'" id="bill_codedareas_'+id+'" value="'+bill_codedareas+'"><div id="bill_areas_'+id+'" style="display:none">'+bill_areas+'</div><a id="billareas_label_'+id+'" name="billareas_label_'+id+'" '+(bill_areas == '' ? 'style="display:none" ' : '')+'href="javascript:void(0);" onmouseover="return overlib($(\'bill_areas_'+id+'\').innerHTML, LEFT);" onmouseout="return nd();">View Areas</a></td>'+
				// Cell containing the Update/Save button
				'<td align="center"><img id="btnSetAreas_'+lastRowNo+'" onclick="js_DiscountsApplication(\''+id+'\', \'bill_codedareas_'+id+'\',\'bill_areas_'+id+'\',\'billareas_label_'+id+'\')" title="Apply to bill areas!" style="cursor:pointer;display:none" src="../../images/selection_img_big.png" border="0" ><span style="vertical-align:top">&nbsp;<img id="discountUpdate'+lastRowNo+'" style="cursor:pointer" title="Edit!" onclick="js_prepareEdit('+lastRowNo+')" src="../../images/cashier_edit.gif" border="0" align="absmiddle" /><img id="discountSave'+lastRowNo+'" title="Save!" style="cursor:pointer;display:none" onclick="js_prepareSave(\''+lastRowNo+'\')" src="../../images/save_img.png" border="0" align="absmiddle" />&nbsp;<img id="discountDelete'+lastRowNo+'" style="cursor:pointer" title="Delete!" src="../../images/cashier_delete.gif" border="0" align="absmiddle" onclick="js_prepareDelete('+lastRowNo+')"/></span></td>'+						
				'</tr>';
		}
		else {
			newRowDest = '<tr class="wardlistrow1" id="discountRow'+lastRowNo+'">' +
				'<td colspan="6">No discount information was found...</td>' +
			 '</tr>';
		}
		
		destTableBody.innerHTML += newRowDest;
		if (flash) Fat.fade_element("discountRow"+lastRowNo, 0, 1000, discountHColor, discountRowColor);
	}
}

function js_rmvDiscount(rowNum) {
/*
 * GUI function used to dynamically remove a row from the discount table.
 */ 
	var destTable, destRows, rmvRow;
	rmvRow=document.getElementById("discountRow"+rowNum);
	if (destTable=document.getElementById("discountTable")) {
		destRows=destTable.getElementsByTagName("tbody")[0];
		// check if srcRows is valid and has more than 1 element
		if (destRows) {
			destRows.removeChild(rmvRow);
			return true;	// success
		}
		else return false;	// fail
	}
	else return false;	// fail
}

function js_prepareEdit(rowNum) {
/*
 * GUI function to display the hidden input forms for the discount and hide the
 * text portion, allowing the user to edit the data for the discount type. Called
 * when the user clicks on the Update button
 */
	var row,
			id, idt,
			desc, disc,
			tdesc, tdisc,
			upd, save,
			cmd_areas, cmd_del;
	
	//added by VAN 06-18-08
	var area, areat;
	
	row = $("discountRow"+rowNum);
	row.style.backgroundColor = discountHColor;
	row.className = "wardlistrow2";
	
	id   =$("discountID"+rowNum);
	idt  =$("discountTID"+rowNum);
	desc =$("discountDesc"+rowNum);
	desct=$("discountTDesc"+rowNum);
	disc =$("discountDiscount"+rowNum);
	disct=$("discountTDiscount"+rowNum);
	
	//added by VAN 06-18-08
	area =$("area_used"+rowNum);
	areaT=$("Tarea_used"+rowNum);
	areaH=$("area_usedH"+rowNum)
	//-----------------
	
	cmd_areas = $("btnSetAreas_"+rowNum);
	upd=$("discountUpdate"+rowNum);
	save=$("discountSave"+rowNum);
	cmd_del=$("discountDelete"+rowNum);
	
	//If there is another discount entry currently being edited, close that one first...
	if (lastEditRow != -1) {
		js_cancelUpdate(lastEditRow)
	}
	
	lastEditRow = rowNum;
	lastEditID   = id.value;
	lastEditDesc = desc.value;
	lastEditDisc = disc.value;
	
	id.style.display="";
	idt.style.display="none";	
	desc.style.display="";
	desct.style.display="none";
	disc.style.display="";
	disct.style.display="none";
	
	//added by VAN 06-18-08
	//alert('here = '+rowNum);
	area.value = areaH.value;
	area.style.display="";
	areaT.style.display="none";

	cmd_areas.style.display="";
	save.style.display="";
	upd.style.display="none";
	cmd_del.style.display="none";
	desc.focus();
}

function js_trim(s) {
/*
 * Generalized function for stripping leading ang trailing spaces
 */
  while (s.substring(0,1)==' ') {
    s = s.substring(1,s.length);
  }
  while (s.substring(s.length-1,s.length)==' ') {
    s = s.substring(0,s.length-1);
  }
  return s;
}

function js_prepareSave(rowNum) {
/*
 * Function for validating the data inputted by the user before submitting the data
 * to the database sever. Called when the user clicks on the Save.
 */
	var id, desc, disc, area, bill_areas;

	id = $("discountID"+rowNum);
	desc = $("discountDesc"+rowNum);
	disc = $("discountDiscount"+rowNum);
	
	area = $("area_used"+rowNum);
	bill_areas = $('bill_codedareas_'+id.value+'');
	
	if (js_trim(id.value)=="") {
		alert("Enter a Discount ID for this discount type...");
		id.focus();
		return(false);
	}

	if (js_trim(desc.value)=="") {
		alert("Enter a Description for this discount type...");
		desc.focus();
		return(false);
	}
	
	if (js_trim(disc.value)=="") {
		alert("Enter the Discount in decimal for this discount type...");
		disc.focus();
		return(false);
	}
	else 
		if ((Number(disc.value) <= 0) || (Number(disc.value) > 1)) {
			alert("You have to indicate a valid discount in decimal.\n(Greater than 0 and less than or equal to 1)");
			disc.focus();
			return(false);			
		}

	// Xajax method, for updating the discount
	xajax_updDiscount(lastEditID, id.value, desc.value, disc.value, area.value, bill_areas.value, userid, rowNum);
}

function js_saveUpdate(rowNum) {
/*
 * This function will be called by xajax_updDiscount upon successfully updating
 * the discount entry. This function will update the entry text based on the values
 * entered by the user on the entry's input forms.
 */
	var row, 
		desc, disc,
		tdesc, tdisc,
		upd, save,
		cmd_areas, cmd_del;
	
	row = $("discountRow"+rowNum);
	id=$("discountID"+rowNum);
	tid=$("discountTID"+rowNum);
	desc=$("discountDesc"+rowNum);
	tdesc=$("discountTDesc"+rowNum);
	disc=$("discountDiscount"+rowNum);
	tdisc=$("discountTDiscount"+rowNum);
	
	cmd_areas = $("btnSetAreas_"+rowNum);
	upd=$("discountUpdate"+rowNum);
	save=$("discountSave"+rowNum);
	cmd_del=$("discountDelete"+rowNum);
	
	//added by VAN 06-18-08
	area=$("area_used"+rowNum);
	tarea=$("Tarea_used"+rowNum);
	areaH=$("area_usedH"+rowNum);
	
	// Update the discount's description text and discount
	tid.innerHTML=id.value;
	tdesc.innerHTML=desc.value;
	tdisc.innerHTML=disc.value*100+"%";
	
	//added by VAN 06-18-08
	if (area.value=='P'){
		area_used_name = 'Pharmacy';
	}else if (area.value=='O'){
		area_used_name = 'Operating Room';
	}else if (area.value=='B'){
		area_used_name = 'Billing';
	}else if (area.value=='L'){
		area_used_name = 'Laboratory';
	}else if (area.value=='R'){
		area_used_name = 'Radiology';
	}else{
		area_used_name = 'All Areas';	
	}
	tarea.innerHTML=area_used_name;
	//---------------
	
	// Hide the input forms
	id.style.display="none";
	desc.style.display="none";	
	disc.style.display="none";
	
	//added by VAN 06-18-08
	area.style.display="none";
	areaH.value = area.value;
	
	// Display the text
	tid.style.display="";
	tdesc.style.display="";
	tdisc.style.display="";
	
	//added by VAN 06-18-08
	tarea.style.display="";
	
	// Hide the Save button
	cmd_areas.style.display="none";
	save.style.display="none";	
	
	// Show the Update button
	upd.style.display="";
	cmd_del.style.display="";
	
	// Clean up
	lastEditRow = -1;
	lastEditID = '';
	lastEditDesc = '';
	lastEditDisc = -1;
	
	// ...and some flashy effect!
	Fat.fade_element("discountRow"+rowNum, 0, 1000, discountHColor, discountRowColor);
}

function js_cancelUpdate(rowNum) {
/*
 * This function will be called by xajax_updDiscount upon successfully updating
 * the discount entry. This function will update the entry text based on the values
 * entered by the user on the entry's input forms.
 */
	var row, 
		id,tid,
		desc, disc,
		tdesc, tdisc,
		upd, save;
	
	row = $("discountRow"+rowNum);
	id=$("discountID"+rowNum);
	tid=$("discountTID"+rowNum);
	desc=$("discountDesc"+rowNum);
	tdesc=$("discountTDesc"+rowNum);
	disc=$("discountDiscount"+rowNum);
	tdisc=$("discountTDiscount"+rowNum);
	upd=$("discountUpdate"+rowNum);
	save=$("discountSave"+rowNum);
	
	// Update the discount's description text and discount
	id.value=lastEditID;
	desc.value=lastEditDesc;
	disc.value=lastEditDisc;
	
	// Hide the input forms
	id.style.display="none";	
	desc.style.display="none";	
	disc.style.display="none";
	
	// Display the text
	tid.style.display="block";
	tdesc.style.display="block";
	tdisc.style.display="block";
	
	// Hide the Save button
	save.style.display="none";
	
	// Show the Update button
	upd.style.display="inline";
	
	// Clean up	
	lastEditRow = -1;
	lastEditID = '';
	lastEditDesc = '';
	lastEditDisc = -1;
	
	// ...and some flashy effect!
	Fat.fade_element("discountRow"+rowNum, 0, 1000, discountHColor, discountRowColor);
	row.className = "wardlistrow1";
}

function js_prepareDelete(rowNum) {
	// Close the last edited discount entry if still open
	var id;
	if (lastEditRow != -1) {
		js_cancelUpdate(lastEditRow)
	}
	row = $("discountRow"+rowNum);
	row.className = "wardlistrow2";
	if (confirm("Do you wish to delete this entry?") == true) {
		id=$("discountID"+rowNum);
		xajax_delDiscount(id.value,rowNum);
	}
	else {
		row.className = "wardlistrow1";
	}
	return false;
}

function js_prepareAdd() {
	var desc, disc, id;

	id=$("inputID");
	desc=$("inputDesc");
	disc=$("inputDiscount");	
	area=$("area_used");	
	areas_id = $("billareas_id");
	areas_desc = $("billareas_appplied").innerHTML;

	if (id.value=="") {		
		alert("Please enter the discount ID...");
		desc.focus();
		return false;
	}

	if (desc.value=="") {		
		alert("Please enter the description...");
		desc.focus();
		return false;
	}
	
	if (isNaN(disc.value=="")) {
		alert("Please enter a valid value for the discount amount...");
		disc.focus();
		return false;
	}
	
	if (disc.value<0||disc.value>1.0) {
		alert("Please enter a value between 0 and 1.00 for the discount amount...");
		disc.focus();
		return false;
	}

	xajax_newDiscount(id.value, desc.value, disc.value, area.value, areas_id.value, areas_desc, userid);
	
	// Clear the values ...
	id.value   = '';
	desc.value = '';
	disc.value = '';	
	area.value = 'A';	
	areas_id.value = '';
	$("billareas_appplied").innerHTML = '';	
	$("billareas_label").style.display = "none";
}

function js_goSave(elem1, elem2, elem3) {
	xajax_saveBillAreas(elem1, elem2, elem3);
}

function js_saveBillAreas(elem1, elem2, elem3, sbill_areas, sbillareas_desc) {
	$(elem1).value = sbill_areas;
	$(elem2).innerHTML = sbillareas_desc;
	if (sbill_areas != '') 
		$(elem3).style.display = "";
	else
		$(elem3).style.display = "none";
	cClick();
}

function js_DiscountsApplication(discount_id, elem1, elem2, elem3) {
	var URL_APPEND = $('seg_URL_APPEND').value;
	
	return overlib(
        OLiframeContent('edv_discounts_application.php'+URL_APPEND+'&id='+discount_id+'&obj1='+elem1+'&obj2='+elem2+'&obj3='+elem3, 640, 350, 'fSelBAreas', 0, 'auto'),
        WIDTH,640, TEXTPADDING,0, BORDER,0, 
				STICKY, SCROLL, CLOSECLICK, MODAL, DRAGGABLE,
				CLOSETEXT, '<img src=../../images/close.gif border=0 >',
        CAPTIONPADDING,4, 
				CAPTION,'Select Bill Areas Where to Apply Discount',
        MIDX,0, MIDY,0, 
        STATUS,'Select Bill Areas Where to Apply Discount');
}
