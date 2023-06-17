YAHOO.namespace("ssadmin.container");

// Instantiate the Dialog
function ssDeleteDialogBox(code, mod){
	//alert('code = '+code);
	//alert('mod = '+mod);
	var elTarget = 'ssRow'+code;
	var handleYes = function() {
		//alert("elTarget="+elTarget+ "code = "+ code+ "rowno ="+ rowno);
		//alert('code = '+code);
		//alert('mod = '+mod);
		xajax_deleteData(code, mod);
		//xajax_listModifierRow(mod);
		//xajax_refresh();
		//self.location.href=self.location.href;
		this.hide();
	};
	var handleNo = function() {
		this.hide();
	};
	
	YAHOO.ssadmin.container.simpledialog1 = new YAHOO.widget.SimpleDialog("simpledialog1", 
			 { width: "310px",
			   fixedcenter: true,
			   visible: false,
			   draggable: true,
			   close: true,
			   text: "Do you want to delete the '"+code+"' modifier?" ,
			   icon: YAHOO.widget.SimpleDialog.ICON_HELP,
			   constraintoviewport: true,
			   buttons: [ { text:"Yes", handler:handleYes, isDefault:true },
						  { text:"No",  handler:handleNo } ]
			 } );
	
	
	YAHOO.ssadmin.container.simpledialog1.setHeader("Deleting modifier..");
	// Render the Dialog
	YAHOO.ssadmin.container.simpledialog1.render("container"+mod);
		
	YAHOO.util.Event.addListener(elTarget, "click", YAHOO.ssadmin.container.simpledialog1.show, YAHOO.ssadmin.container.simpledialog1, true);

} //end of function ssDialogBox


function btnClickHandler(e){
	var elTargetBtn = YAHOO.util.Event.getTarget(e);
	var create_id, sid, lang;

	sid = $("sid").value;
	lang =$("lang").value;
	create_id = $("create_id").value;
	
	switch (elTargetBtn.id){
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

function updateBtnClickHandler(code,mod){
	var sid, create_id, lang;
	var elTarget, param;
	
	var sdesc = $('desc'+code).value;
	var subcode = $('subcode'+code).value;
		
	sid= $("sid").value;
	lang= $("lang").value;
	create_id= $("create_id").value;
	
	//alert($('fAddTray'));
	
	//if(elTarget = document.getElementById("ssRow"+rowno)){
		param = 'sid='+sid+'&lang='+lang+'&create_id='+create_id+'&mode=update&modi='+mod+'&desc='+sdesc+'&subcode='+subcode;
		//alert(param);
		return overlib(OLiframeContent('social_service_admin_addmod.php?'+param, 500,185, 'fAddTray', 1, 'auto'),
						WIDTH , 300, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL , CLOSETEXT, 
						'<img src=../../../images/close.gif border=0>', CAPTIONPADDING, 4, CAPTION, 'Edit Social Service Classification\'s Modifiers',
						MIDX, 0, MIDY, 0, STATUS,'Edit Social Service Classification\'s Modifiers');
	//}else{
	//	return false;	
	//}
}//end of function updateBtnClickHandler

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
		//alert(dRows.length);
		dBodyId = dBody.id.substr(0,11)+mod;
		//alert(dBodyId+" -  "+code+" - "+desc);
		if(code){
			editImg = '<input type="image" src="../../../images/edit.gif" onclick="updateBtnClickHandler(\''+code+'\',\''+mod+'\')" />';
			delitemImg = '<img src="../../../images/btn_delitem.gif" style="cursor:pointer" border="0" onclick="ssDeleteDialogBox(\''+code+'\',\''+mod+'\')">';
			//alert(delitemImg);
			alt = (dRows.length%2)+1;
			//alert(alt)
			
			rowSrc = '<tr class="wardlistrow'+alt+'" id="ssRow'+code+'">'+
						'<td >'+code+'<input type="hidden" name="subcode'+code+'" id="subcode'+code+'" value="'+code+'"></td>'+
						'<td >'+desc+'<input type="hidden" name="desc'+code+'" id="desc'+code+'" value="'+desc+'"></td>'+
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