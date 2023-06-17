//created by CHa, Feb 23, 2010
var currentPage=0, lastPage=0;
var FIRST_PAGE=1, PREV_PAGE=2, NEXT_PAGE=3, LAST_PAGE=4, SET_PAGE=0;
var AJAXTimerID=0;
//var arrID=new Array();		//added code by angelo m. 07/23/2010
var numRows;
var deletedRows;
var nextRowID;


function list_sections(val)
{
	//xajax_populateSections(val);
	if(val=="LD")
	{
		$('lab_section_row').style.display="";
		$('radio_specific_row').style.display="none";
		$('radio_section_row').style.display="none";
	}
	else if("RD")
	{
		$('radio_section_row').style.display="";
		$('lab_section_row').style.display="none";
		$('radio_specific_row').style.display="none";
	}
	$("service_list").innerHTML="";
	$("control_buttons").style.display="none";
}

function view_lab_sections(options)
{
	$('lab_section').innerHTML = options;
	$('lab_section_row').style.display = '';
}

function view_radio_areas(options)
{
	$('radio_area').innerHTML = options;
	$('radio_section_row').style.display = '';
}

function list_radio_sections(val)
{
	xajax_populateRadioSections(val);
}

function add_rows_cols()
{

	var rows = $('num_rows').value;
	var cols = $('num_cols').value;
	var lsection = $('lab_section').value;
	var rsection = $('radio_section').value;
	var radarea = $('radio_area').value;
	var section = "";
	if(lsection!=0)
	{
		section = lsection;
		//alert("lab-"+$('lab_section').value)
	}
	else if(rsection!=0)
	{
		section = rsection;
	}
	var cost_center = $('cost_center').value;

	var table_body = '';
	var tr = '';
	var td = '';

	if(!rows || !cols || !cost_center || section==0)
	{
		return false;
	}
	else if(cost_center=="RD" && radarea==0)
	{
		 return false;
	}
	else
	{
		fn_init_rowValues(rows);
		table_body+='<ul id="sortable">';
		for(i=0;i<rows;i++)
		{
				table_body+='<li class="sortable1" id="row'+i+'">';
				for(j=0;j<cols;j++)
				{
					table_body+=
					'<div>'+
					'<table width="100%" border="0" cellpadding="2" cellspacing="0">'+
						'<tr>'+
							'<td width="12%" align="left">'+
								'<select class="segInput" id="data_type'+i+j+'" name="data_type'+i+j+'" onchange="set_datatype(this.value,\''+i+'\',\''+j+'\')">'+
									'<option value="0">-Select-</option>'+
									'<option value="header">Header</option>'+
									'<option value="data">Data</option>'+
								'</select>'+
							'</td>'+
							'<td width="73%" align="left">'+
								'<span id="header'+i+j+'" style="display:none"></span>'+
								'<span id="data'+i+j+'" style="display:none"></span>'+
								'<span id="hidden_data'+i+j+'">'+
									'<input type="hidden" id="datatype'+i+j+'[]" name="datatype[]" value=""/>'+
									'<input type="hidden" id="cell_id[]" name="cell_id[]" value="'+i+'/'+j+'"/>'+
								'</span>'+
							'</td>'+
							'<td width="*">'+
								'<img src="../../../images/cost_center_insert.png" title="Insert Below" onclick="fn_InsertBelow(this,\''+i+'\','+j+');"/>&nbsp;'+
								'<img src="../../../images/cashier_delete_small.gif" title="Delete"  onclick="fn_Delete(this,\''+i+'\','+j+');"/>&nbsp;'+
							'</td>'+
						'</tr>'+
					'</table>'
					'</div>';
				}
				table_body+='</li>';

		}
		table_body+='</ul>';
		$('service_list').innerHTML = table_body;

		J("#sortable").sortable({
		containment: 'parent'
		})
		.disableSelection();
		$('control_buttons').style.display='';
	}
}





function set_datatype(val, row, col)
{

	$('datatype'+row+col+'[]').value=val;
	if(val=="header")
	{
		$('header'+row+col).innerHTML='<input type="text" size="30" id="data_values'+row+col+'" name="data_values[]" class="segInput"/>';
		$('header'+row+col).style.display='';
		$('data'+row+col).style.display='none';
		$('data'+row+col).innerHTML='';
	}
	else if(val=="data")
	{
		var cost_center = $('cost_center').value;
		if(cost_center=='LD')
			var section = $('lab_section').value;
		else if(cost_center=='RD')
			var section = $('radio_section').value;

		$('hidden_data'+row+col).innerHTML+='<input type="hidden" id="dataservices'+row+col+'[]" name="dataservices[]" value=""/>';
		$('header'+row+col).style.display='none';
		$('header'+row+col).innerHTML='';
		$('data'+row+col).style.display='';
		xajax_populateServices(cost_center,section,row,col);
	}
}

function key_check(e, value)
{
	var number = /^\d+$/;
	if((e.keyCode>= 48 && e.keyCode<=57) || (e.keyCode>= 96 && e.keyCode<=105))
	{
		return true;
	}
	else
	{
		return false;
	}
}

function startAJAXSearch(page)
{
		if (page)
				document.getElementById('pagekey').value = page;
		else
				document.getElementById('pagekey').value = '0';
		//alert(document.getElementById('pagekey').value);
		if (AJAXTimerID) clearTimeout(AJAXTimerID);
		//alert(page);
		AJAXTimerID = setTimeout("xajax_populateGuiList("+page+")",50);
}

function clearList(listID) {
		// Search for the source row table element
		var list=$(listID),dRows, dBody;
		if (list) {
				dBody=list.getElementsByTagName("tbody")[0];
				if (dBody) {
						dBody.innerHTML = "";
						return true;    // success
				}
				else return false;    // fail
		}
		else return false;    // fail
}

function endAJAXList(listID) {
		var listEL = $(listID);
		if (listEL) {
				$("guilist-body").style.display = "";
				searchEL.style.color = "";
		}
}

function setPagination(pageno, lastpage, pagen, total) {
		currentPage=parseInt(pageno);
		lastPage=parseInt(lastpage);
		firstRec=(parseInt(pageno)*pagen)+1;
		totalRows=total;

		if (currentPage==lastPage)
				lastRec = total;
		else
				lastRec = (parseInt(pageno)+1)*pagen;

		if (parseInt(total)==0)
		{
				$("pageShow").innerHTML = '<span>Showing '+(lastRec)+'-'+(lastRec)+' out of '+(parseInt(total))+' record(s).</span>';
		}
		else if(parseInt(total)>0)
		{
				$("pageShow").innerHTML = '<span>Showing '+(firstRec)+'-'+(lastRec)+' out of '+(parseInt(total))+' record(s).</span>';

				$("pageFirst").className = (currentPage>0 && lastPage>0 && total>10) ? "segSimulatedLink" : "segDisabledLink";
				$("pagePrev").className = (currentPage>0 && lastPage>0 && total>10) ? "segSimulatedLink" : "segDisabledLink";
				$("pageNext").className = (currentPage<lastPage && total>10) ? "segSimulatedLink" : "segDisabledLink";
				$("pageLast").className = (currentPage<lastPage && total>10) ? "segSimulatedLink" : "segDisabledLink";
		}
		else
		{
				 $("pageShow").innerHTML = '<span>Showing 0 out of 0 record(s).</span>';
		}
}

function jumpToPage(el, jumpType, set) {
		if (el.className=="segDisabledLink") return false;
		if (lastPage==0) return false;
		switch(jumpType) {
				case FIRST_PAGE:
						if (currentPage==0) return false;
						startAJAXSearch(0);
						document.getElementById('pagekey').value=0;
				break;
				case PREV_PAGE:
						if (currentPage==0) return false;
						startAJAXSearch(parseInt(currentPage)-1);
						document.getElementById('pagekey').value=currentPage-1;
				break;
				case NEXT_PAGE:
						if (currentPage >= lastPage) return false;
						startAJAXSearch(parseInt(currentPage)+1);
						document.getElementById('pagekey').value=parseInt(currentPage)+1;
				break;
				case LAST_PAGE:
						if (currentPage >= lastPage) return false;
						startAJAXSearch(parseInt(lastPage));
						document.getElementById('pagekey').value=parseInt(lastPage);
				break;
		}
}

function addslashes(str) {
		str=str.replace("'","\\'");
		return str;
}

function refreshFrame(outputResponse)
{
		alert(""+outputResponse);
		window.location.reload();
		//ReloadWindow();
}

function viewGuiList(listID, id, ref_src, section)
{
	var list=$(listID), dRows, dBody, rowSrc;
	var i;
	var classified, mode, editlink;
		 //alert("hello");
	if (list) {
		//alert("hi");
			dBody=list.getElementsByTagName("tbody")[0];
			dRows=dBody.getElementsByTagName("tr");
			if (id) {
				//alert("id="+id);
				alt="";
				if (dRows.length%2 != 0) alt = "alt";
				 mode="edit";

					rowSrc = '<tr class="'+alt+'" id="row'+addslashes(id)+'" value="'+id+'">'+
						'<td width="2%" align="center" nowrap="nowrap">'+id+'</td>'+
						'<td width="10%" align="center" nowrap="nowrap">'+ref_src+'</td>'+
						'<td width="10%" align="center" nowrap="nowrap">'+section+'</td>'+
						'<td width="1%" align="center">'+
							'<img src="../../../images/cashier_edit_3.gif" onclick="edit_gui(\''+id+'\')"/> '+
							'<img src="../../../images/cashier_delete_small.gif" onclick="delete_gui(\''+id+'\')"/> '+
						'</td>'+
					'</tr>';
			}
			else {
					rowSrc = '<tr><td colspan="4" style="">No GUI in the list...</td></tr>';
			}
			dBody.innerHTML += rowSrc;
				//alert("dBody="+dBody.innerHTML);
	}
}

function delete_gui(id)
{
	var reply = confirm("Are you sure you want to delete this GUI item #"+id+"?");
	if(reply)
	{
		xajax_deleteGuiItem(id);
	}
	else
	{
		return false;
	}
}

function edit_gui(id)
{
		return overlib(
		OLiframeContent('edit_cost_center_gui.php?id='+id, 800, 350, 'fOrderTray', 0, 'auto'),
		WIDTH,350, TEXTPADDING,0, BORDER,0,
		STICKY, SCROLL, CLOSECLICK, MODAL,
		CLOSETEXT, '<img src=../../../images/close_red.gif border=0" >',
		CAPTIONPADDING,2,DRAGGABLE,
		CAPTION,'Edit Cost Center GUI',
		MIDX,0, MIDY,0,
		STATUS,'Edit Cost Center GUI');
}

function check_datavalues(val)
{
	var data_form = document.guimgr_form.elements["data_values[]"];
	rep=0;
	for(i=0;i<data_form.length;i++)
	{
		if(val==data_form[i].value)
			rep++;
	}
	if(rep>1)
	{
		alert("repeated data")
		$('control_buttons').style.display = "none";
	}
	else
	{
		$('control_buttons').style.display = "";
	}
}

function fn_init_rowValues(size){
	var i;

	numRows=size;
	nextRowID=numRows;
}

function fn_InsertBelow(obj,id,col){

			var i,j;
			var strItem='';
		//	var str='cell_id_'+id+'_'+col;
			var current=$('row'+id);

			numRows++;
			i=nextRowID;
			j=col;
			strItem+='<li class="ui-state-highlight"  id="row'+i+'">';
			strItem+=
				'<div>'+
				'<table width="100%" border="0" cellpadding="2" cellspacing="0">'+
					'<tr>'+
						'<td width="25%">'+
							'<select class="segInput" id="data_type'+i+j+'" name="data_type'+i+j+'" onchange="set_datatype(this.value,\''+i+'\',\''+j+'\')">'+
								'<option value="0">-Select-</option>'+
								'<option value="header">Header</option>'+
								'<option value="data">Data</option>'+
							'</select>'+
						'</td>'+
						'<td width="60%">'+
							'<span id="header'+i+j+'" style="display:none"></span>'+
							'<span id="data'+i+j+'" style="display:none"></span>'+
							'<span id="hidden_data'+i+j+'">'+
								'<input type="hidden" id="datatype'+i+j+'[]" name="datatype[]" value=""/>'+
								'<input type="hidden" id="cell_id[]" name="cell_id[]" value="'+i+'/'+j+'"/>'+
							'</span>'+
						'</td>'+
						'<td width="*">'+
							'<img src="../../../images/cost_center_insert.png" title="Insert Below" onclick="fn_InsertBelow(this,\''+i+'\','+j+');"/>&nbsp;'+
							'<img src="../../../images/cashier_delete_small.gif" title="Delete"  onclick="fn_Delete(this,\''+i+'\','+j+');"/>&nbsp;'+
						'</td>'+
					'</tr>'+
				'</table>'
				'</div>';
			strItem+='</li>';
			//strItem+='</ul>';
			nextRowID++;
			new Insertion.After(current, strItem);
			$('num_rows').value=numRows;
}

function fn_Delete(obj,id,col){
	var i;
	var current = "row"+id;
	$(current).remove();
	numRows--;
	$('num_rows').value=numRows;
}
