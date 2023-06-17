YAHOO.namespace("ssadmin.container");

// Instantiate the Dialog
function ssDeleteDialogBox(code, rowno){
	var elTarget = 'ssRow'+rowno;
	var handleYes = function() {
		//alert("elTarget="+elTarget+ "code = "+ code+ "rowno ="+ rowno);
		xajax_deleteData(code, rowno);
		xajax_listRow();
		this.hide();
	};
	var handleNo = function() {
		this.hide();
	};
	
	YAHOO.ssadmin.container.simpledialog1 = new YAHOO.widget.SimpleDialog("simpledialog1", 
			 { width: "310px",
			   fixedcenter: true,
			   visible: false,
			   draggable: false,
			   close: true,
			   text: "Do you want to delete the '"+code+"' classification?" ,
			   icon: YAHOO.widget.SimpleDialog.ICON_HELP,
			   constraintoviewport: true,
			   buttons: [ { text:"Yes", handler:handleYes, isDefault:true },
						  { text:"No",  handler:handleNo } ]
			 } );
	
	
	YAHOO.ssadmin.container.simpledialog1.setHeader("Deleting service..");
	// Render the Dialog
	YAHOO.ssadmin.container.simpledialog1.render("container");
		
	YAHOO.util.Event.addListener(elTarget, "click", YAHOO.ssadmin.container.simpledialog1.show, YAHOO.ssadmin.container.simpledialog1, true);

} //end of function ssDialogBox


function btnClickHandler(e){
	var elTargetBtn = YAHOO.util.Event.getTarget(e);
	var create_id, sid, lang;

	sid = $("sid").value;
	lang =$("lang").value;
	create_id = $("create_id").value;
	
	switch (elTargetBtn.id){
		case "btnAdd":
			return overlib(OLiframeContent('social_service_admin_add.php?sid='+sid+'&lang='+lang+'&create_id='+create_id+'&mode=add&code=', 500,185, 'fAddTray', 1, 'auto'),
						WIDTH , 300, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL , CLOSETEXT, 
						'<img src=../../../images/close.gif border=0>', CAPTIONPADDING, 4, CAPTION, 'Add Social Service Classification',
						MIDX, 0, MIDY, 0, STATUS,'Add Social Service Classification');
		break;
		//added by VAN 07-05-08
		case "btnAddMod":
			return overlib(OLiframeContent('social_service_admin_addmod.php?sid='+sid+'&lang='+lang+'&create_id='+create_id+'&mode=add&code=', 500,185, 'fAddTray', 1, 'auto'),
						WIDTH , 300, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL , CLOSETEXT, 
						'<img src=../../../images/close.gif border=0>', CAPTIONPADDING, 4, CAPTION, 'Add Social Service Classification\'s Modifiers',
						MIDX, 0, MIDY, 0, STATUS,'Add Social Service Classification\'s Modifiers');
		break;
		//-----------------------
		case "btnClose":
			//alert("close");
			return false;//window.close;
		break;
	}
} // end of function btnClickHandler

function updateBtnClickHandler(code,rowno){
	var sid, create_id, lang;
	var elTarget, param;
	
	var sdesc = $('desc'+rowno).value;
	var sdiscount = $('discount'+rowno).value;
	var is_forall = $('is_forall'+rowno).value;
	
	sid= $("sid").value;
	lang= $("lang").value;
	create_id= $("create_id").value;
	
	//alert($('fAddTray'));
	
	if(elTarget = document.getElementById("ssRow"+rowno)){
		param = 'sid='+sid+'&lang='+lang+'&create_id='+create_id+'&mode=update&code='+code+'&desc='+sdesc+'&discount='+sdiscount+'&forall='+is_forall;
		//alert(param);
		return overlib(OLiframeContent('social_service_admin_add.php?'+param, 500,185, 'fAddTray', 1, 'auto'),
						WIDTH , 300, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL , CLOSETEXT, 
						'<img src=../../../images/close.gif border=0>', CAPTIONPADDING, 4, CAPTION, 'Edit Social Service Classification',
						MIDX, 0, MIDY, 0, STATUS,'Edit Social Service Classification');
	}else{
		return false;	
	}
}//end of function updateBtnClickHandler


function gui_AddRow(code, desc, discount, is_forall){
	var list,dRows, dBody, rowSrc, lastRowNo;
	var delitemImg, editImg, hiddenValue;
	
	if(list = document.getElementById("sslistTable")){
		dBody = list.getElementsByTagName("tbody")[0];
		dRows = dBody.getElementsByTagName("tr");
		
		//updateImg = '<img src="../../../images/edit.gif" border="0">';
		//editImg = '<input type="image" src="../../../images/edit.gif" id="edit'+code+'"/>'
		if(dRows.length>0) lastRowNo=dRows[dRows.length-1].id.replace("ssRow", "");
		lastRowNo= isNaN(lastRowNo)? 0:(lastRowNo-0)+1;
		
		if(code){
			
			editImg = '<input type="image" src="../../../images/edit.gif" id="edit'+lastRowNo+'" onclick="updateBtnClickHandler(\''+code+'\',\''+lastRowNo+'\')" />';
			delitemImg = '<img src="../../../images/btn_delitem.gif" style="cursor:pointer" border="0" onclick="ssDeleteDialogBox(\''+code+'\',\''+lastRowNo+'\')">';
			
			hiddenValue = '<input type="hidden" id="code'+lastRowNo+'" value="'+code+'">'+
						  '<input type="hidden" id="desc'+lastRowNo+'" value="'+desc+'">'+
						  '<input type="hidden" id="discount'+lastRowNo+'" value="'+discount+'">'+
						  '<input type="hidden" id="is_forall'+lastRowNo+'" value="'+is_forall+'">';
			//alert(hiddenValue);
			
			rowSrc = '<tr id="ssRow'+lastRowNo+'">'+
						'<td >'+code+'</td>'+
						'<td >'+desc+'</td>'+
						'<td align="center">'+discount+'&nbsp;%</td>'+
						'<td align="center">'+editImg+hiddenValue+'</td>'+
						'<td align="center">'+delitemImg+'</td>'+
					'</tr>';

		}else{
			rowSrc = '<tr><td colspan="5" style="">No such person exists</td></tr>';	
		}
		dBody.innerHTML += rowSrc;	
	}	
}

//Clear List table
function clearList(listID) {
	var list=$(listID),dRows, dBody;
	if (list) {
		dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			dBody.innerHTML = "";
			return true;	// success
		}
		else return false;	// fail
	}
	else return false;	// fail
}

//remove row from the List table
/*
function removeRow(rowNo){
	var dTable, dRow, rmvRow;
	//alert("removeRow");
	if(rmvRow = document.getElementById("ssRow"+rowNo)){
		if(dTable = document.getElementById("sslistTable")){
			dRow = dTable.getElementsByTagName("tbody")[0];
			if(dRow){
				dRow.removeChild(rowNo);
				return true;
			}
			else return false; //fail
		}
		else return false;
	}
}//end of function removeRow()
*/

//edited by VAN 04-07-08
function removeRow(rowNo){
	//alert('remove = '+rowNo);	
	var table = document.getElementById("sslistTable");
	var rowno;
	var rmvRow=document.getElementById("ssRow"+rowNo);
	//alert(table.innerHTML);
	if (table && rmvRow) {
		rowno = 'ssRow'+rowNo;
		var rndx = rmvRow.rowIndex;
		table.deleteRow(rmvRow.rowIndex);	
	}
}

//added by VAN 07-05-08
function clearList2(listID, mod) {
	var list=$(listID),dRows, dBody, dBodyId;
	if (list) {
		dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			//alert(dBody.id.substr(0,11));	
			dBodyId = dBody.id.substr(0,11)+mod;
			//dBody.innerHTML = "";
			document.getElementById(dBodyId).innerHTML = "";
			return true;	// success
		}
		else return false;	// fail
	}
	else return false;	// fail
}


function gui_AddRow2(code, desc, mod){
	var list,dRows, dBody, rowSrc, lastRowNo;
	var delitemImg, editImg;
	
	if(list = document.getElementById("sslistTable")){
		dBody = list.getElementsByTagName("tbody")[0];
		dRows = dBody.getElementsByTagName("tr");
		dBodyId = dBody.id.substr(0,11)+mod;
		//alert(dBodyId+" -  "+code+" - "+desc);
		if(code){
			editImg = '<input type="image" src="../../../images/edit.gif" onclick="updateBtnClickHandler(\''+code+'\',\''+mod+'\')" />';
			delitemImg = '<img src="../../../images/btn_delitem.gif" style="cursor:pointer" border="0" onclick="ssDeleteDialogBox(\''+code+'\',\''+mod+'\')">';
			//alert(dRows.length);
			alt = (dRows.length%2)+1;
			//alert(alt)
			
			rowSrc = '<tr class="wardlistrow'+alt+'">'+
						'<td >'+code+'</td>'+
						'<td >'+desc+'</td>'+
						'<td >'+editImg+'</td>'+
						'<td align="center">'+delitemImg+'</td>'+
					'</tr>';

		}else{
			rowSrc = '<tr><td colspan="5" style="">No such modifiers</td></tr>';	
		}
		
		//dBody.innerHTML += rowSrc;	
		document.getElementById(dBodyId).innerHTML += rowSrc;
		
	}	
}

//---------------------