
function init() {
	
	// Define various event handlers for Dialog
	var handleSubmit = function() {
		this.submit();
		this.cancel();
	};
	var handleCancel = function() {
		this.cancel();
	};
	/*var handleSuccess = function(o) {
	//	var response = o.responseText;
		response = response.split("<!")[0];
		document.getElementById("resp").innerHTML = response;
		eval(response);
	};*/
	
	var handleFailure = function(o) {
		alert("Submission failed: " + o.status);
	};

	// Instantiate the Dialog
	YAHOO.example.container.dialog1 = new YAHOO.widget.Dialog("dialog1", 
																{ width : "390px",
																  fixedcenter : true,
																  visible : false, 
																  constraintoviewport : true,
																  buttons : [ { text:"Submit", handler:handleSubmit, isDefault:true },
																			  { text:"Cancel", handler:handleCancel } ]
																 } );
	
	// Validate the entries in the form to require that both first and last name are entered
	/*YAHOO.example.container.dialog1.validate = function() {
		var data = this.getData();
		if (data.firstname == "" || data.lastname == "") {
			alert("Please enter your first and last names.");
			return false;
		} else {
			return true;
		}
	};*/
	
	YAHOO.example.container.dialog1.validate = function(){
		var data  = this.getData();
		xajax_ProcessAddSScForm(data);
		return false;
	};

	// Wire up the success and failure handlers
	//YAHOO.example.container.dialog1.callback = { success: handleSuccess,
	//											 failure: handleFailure };
												 
	// Render the Dialog
	YAHOO.example.container.dialog1.render();
	
	YAHOO.util.Event.addListener("show", "click", YAHOO.example.container.dialog1.show, YAHOO.example.container.dialog1, true);
	//YAHOO.util.Event.addListener("hide", "click", YAHOO.example.container.dialog1.hide, YAHOO.example.container.dialog1, true);
}

function js_prepareAddRow(){
	var encounter_nr = $('encounter_nr').value;
} // end of function js_prepareAddRow()

// this function use for xajax 
function js_addRow(tableId, code, note, clsfby, grant_dte, listname){
	var dTable=$(tableId), dBody, dRows, rowSrc;
	var rowno;
	
	if(dTable){
		dBody = dTable.getElementsByTagName('tbody')[0];
		dRows = dBody.getElementsByTagName('tr');
		if(dRows.legnth > 0) rowno = dRows[dRows.length-1].id.replace("ssc","" ); 
		
		if(code){
			switch(listname) {		
				case 'ssl':				
					rowSrc = '<tr id="ssc'+rowno+'">'+
								'<td>'+
									'<input type="hidden" id="nr'+code+'" value="'+code+'">'+
									'<span style="color:#660000">'+code+'</span>'+
								'</td>'+
								'<td><span>'+note+'</span></td>'+
								'<td align="center"><span>'+clsfby+'</span></td>'+
								'<td align="center"><span>'+grant_dte+'</span></td>'+
							 '</tr>';
				break;
				
				//if case lcr = (refno, date_request, price_cash, discount)		
				case 'lcr':
					var inputbtn, hddn;
									
					inputbtn =  '<input type="text" id="discountlcr'+rowno+'" size="6" readonly /> &nbsp;'+
								'<input type="button" value="discount">';
					
					rowSrc = '<tr id="lcr'+rowno+'">'+
								'<td align="center"><span style="color:#660000">'+code+'</span></td>'+    //refno
								'<td align="center"><span>'+note+'</span></td>'+    		// date_request
								'<td align="center"><span>'+clsfby+'</span></td>'+   		//price_cash
								'<td align="center"><span>'+grant_dte+'</span></td>'+   		//price_cash
								'<td align="right">'+inputbtn+'</td>'+
							 '</tr>';
					
				break;
			}
		}else{
			rowSrc = '<tr><td style="">No classification yet..</td></tr>';
		}	
		dBody.innerHTML += rowSrc;
	}
} // end of function js_addRow


function js_clearRow(tableId){
	// Search for the source row table element
	var list=$(tableId),dRows, dBody;
	if (list) {
		dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			dBody.innerHTML = "";
			return true;	// success
		}
		else return false;	// fail
	}
	else return false;	// fail
}// end of fucntion js_clearRow()


function js_AddOptions(tagId,text, value){
	var elTarget = $(tagId);
	if(elTarget){
		//var opt = new Option(text, value);
		var opt = new Option(value, value);
		opt.id = value;
		elTarget.appendChild(opt);
	}
	var optionsList = elTarget.getElementsByTagName('OPTION');
}//end of function js_AddOption

function js_SetOptionDesc(tagId,value){
	$(tagId).innerHTML = value;
}

//clear ajax Options social service classification
function js_ClearOptions(tagId){
	var optionsList, el =$(tagId);
	if(el){
		optionsList = el.getElementsByTagName('OPTION');
		for(var i=optionsList.length-1; i >=0 ; i--){
			optionsList[i].parentNode.removeChild(optionsList[i]);	
		}
	}
}//end of function js_ClearOptions

