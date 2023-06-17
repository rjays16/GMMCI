var currentPage=0, lastPage=0;
var FIRST_PAGE=1, PREV_PAGE=2, NEXT_PAGE=3, LAST_PAGE=4, SET_PAGE=0;

function display(str) {
	document.write(str);
}

function formatNumber(num,dec) {
	var nf = new NumberFormat(num);
	if (isNaN(dec)) dec = nf.NO_ROUNDING;
	nf.setPlaces(dec);
	return nf.toFormatted();
}

function parseFloatEx(x) {
	var str = x.toString().replace(/\,|\s/,'')
	return parseFloat(str)	
}

function setPagination(pageno, lastpage, pagen, total) {
	currentPage=parseInt(pageno);
	lastPage=parseInt(lastpage);	
	firstRec = (parseInt(pageno)*pagen)+1;
	if (currentPage==lastPage)
		lastRec = total;
	else
		lastRec = (parseInt(pageno)+1)*pagen;
	if (parseInt(total))
		$("pageShow").innerHTML = '<span>Showing '+(formatNumber(firstRec))+'-'+(formatNumber(lastRec))+' out of '+(formatNumber(parseInt(total)))+' record(s)</span>'
	else
		$("pageShow").innerHTML = ''
	$("pageFirst").className = (currentPage>0 && lastPage>0) ? "segSimulatedLink" : "segDisabledLink";
	$("pagePrev").className = (currentPage>0 && lastPage>0) ? "segSimulatedLink" : "segDisabledLink";
	$("pageNext").className = (currentPage<lastPage) ? "segSimulatedLink" : "segDisabledLink";
	$("pageLast").className = (currentPage<lastPage) ? "segSimulatedLink" : "segDisabledLink";
}

function jumpToPage(el, jumpType, set) {
	if (el.className=="segDisabledLink") return false;
	if (lastPage==0) return false;
	switch(jumpType) {
		case FIRST_PAGE:
			if (currentPage==0) return false;
			startAJAXSearch('search',0);
		break;
		case PREV_PAGE:
			if (currentPage==0) return false;
			startAJAXSearch('search',currentPage-1);
		break;
		case NEXT_PAGE:
			if (currentPage >= lastPage) return false;
			startAJAXSearch('search',parseInt(currentPage)+1);
		break;
		case LAST_PAGE:
			if (currentPage >= lastPage) return false;
			startAJAXSearch('search',lastPage);
		break;
	}
}

function clearList(listID) {
	// Search for the source row table element
	if (!listID) listID = 'or-list';
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


function insertAfter(newElement,targetElement) {
	//target is what you want it to go after. Look for this elements parent.
	var parent = targetElement.parentNode;

	//if the parents lastchild is the targetElement...
	if(parent.lastchild == targetElement) {
		
		//add the newElement after the target element.
		parent.appendChild(newElement);
	} else {

		// else the target has siblings, insert the new element between the target and it's next sibling.
		parent.insertBefore(newElement, targetElement.nextSibling);
	}
}

/*
function myBlindUp(id) {
	var element = 'd'+id;
  element = $(element);
  element.makeClipping();
	element.style.overflowX = 'hidden';
	element.style.overflowY = 'scroll';
  return new Effect.Scale(element, 0,
    Object.extend({ scaleContent: false, 
      scaleX: false, 
      restoreAfterFinish: true,
      afterFinishInternal: function(effect) {
        effect.element.hide().undoClipping();
				$('details-'+id).style.display = 'none';
      } 
    }, arguments[1] || { })
  );
};

function myBlindDown (id) {
	var element = 'd'+id;
  element = $(element);
	$('details-'+id).style.display = '';
  var elementDimensions = element.getDimensions();
	elementDimensions.height = 180;
  return new Effect.Scale(element, 100, Object.extend({ 
    scaleContent: false, 
    scaleX: false,
    scaleFrom: 0,
    scaleMode: {originalHeight: elementDimensions.height, originalWidth: elementDimensions.width},
    restoreAfterFinish: false,
    afterSetup: function(effect) {
      effect.element.makeClipping().setStyle({height: '0px'}).show();
			element.style.overflowX = 'hidden';
			element.style.overflowY = 'scroll';
    },  
    afterFinishInternal: function(effect) {
      effect.element.undoClipping();
			element.style.overflowX = 'hidden';
			element.style.overflowY = 'scroll';
    }
  }, arguments[1] || { }));
};
*/

function showDetails(id, show) {
	var or_no = $('or_'+id).value;
	if ($('details-'+id)) { 
		$('details-'+id).style.display = show ? '' : 'none';
	}
	else { }
	
	var dt;
	dt = $('d'+id);
	if (dt) {
		if (!dt.src || show) setTimeout("$('d"+id+"').src='seg-cashier-cm-paylist-view.php?or="+or_no+"&row="+id+"'",10);
		/*
		if (show) {
			myBlindDown(id, { duration:0.2 });
		}
		else {
			myBlindUp(id, { duration:0.2 });
		}
		*/
		if ($('expand-'+id)) $('expand-'+id).style.display = show ? "none" : "";
		if ($('collapse-'+id)) $('collapse-'+id).style.display = show ? "" : "none";
	}
}

function addToList(listID, details) {
	if (!listID) listID = 'or-list';
	var list=$(listID), dRows, dBody, rowSrc;
	var i;
	if (list) {
		dBody=list.getElementsByTagName("tbody")[0];
		dRows=dBody.getElementsByTagName("tr");

		// get the last row id and extract the current row no.
		if (details.id) {
			var height = 180;
			if (details.paid == 0) {
				if (details.iscash!=1) status = '<span style="color:#000080">Charge</span>';
				else {
					if (parseFloat(details.amount)==0) status = '<span style="color:#008000">No charge</span>';
					else if (parseFloat(details.ssamount)==0) status = '<span style="color:#008000">Social Worker</span>';
					else if (details.istpl==1) status = '<img title="TPL" src="../../images/tpl_item.gif" align="absmiddle"/>';
				}
			}
			else
				status = '<img title="Paid" src="../../images/paid_item.gif" align="absmiddle"/>';
			rowSrc = '<tr id="row-'+details.id+'">'+
									'<td>'+
										'<input type="hidden" id="mode-'+details.id+'" value="'+details.mode+'" />'+
										'<span id="date-'+details.id+'" style="color:#000066">'+details.date+'</span><br />'+
									'</td>'+
									'<td>'+
										'<span id="id-'+details.id+'" style="color:#660000">'+details.id+'</span>'+
									'</td>'+
									'<td>'+
										'<span id="name-'+details.id+'" style="">'+details.name+'</span>'+
									'</td>'+
									'<td align="center">'+
										'<span id="type-'+details.id+'" style="">'+details.type_sub+'</span>'+
									'</td>'+
									'<td align="center">'+
										'<span id="count-'+details.id+'" style="">'+details.item_count+'</span>'+
									'</td>'+
									'<td align="right">'+
										'<span id="amount-'+details.id+'" style="color:#000080">'+formatNumber(details.amount,2)+'</span>'+
									'</td>'+
									'<td align="right">'+
										status+
									'</td>'+
									'<td class="centerAlign">'+
										'<img id="expand-'+details.id+'" title="Expand" src="../../images/cashier_down.gif" class="segSimulatedLink" onclick="showDetails(\''+details.id+'\',true)">'+
										'<img id="collapse-'+details.id+'" title="Collapse" src="../../images/cashier_up.gif" class="segSimulatedLink" style="display:none" onclick="showDetails(\''+details.id+'\',false)">'+
									'</td>'+
								'</tr>'+
								'<tr id="details-'+details.id+'" style="display:none;">'+
									'<td id="detailstd-'+details.id+'" style="padding:8px;" class="plain" colspan="8" align="center">'+
										'<div>'+
											'<iframe id="d'+details.id+'" frameborder="0" scrolling="yes" style="height:0px;overflow-x:hidden;overflow-y:scroll;border:3px solid #003366;width:98%;background-color:white;display:block">\n'+'</iframe>'+
										'</div>'+
									'</td>'+
								'</tr>';
								
								
		}
		else {
			rowSrc = '<tr><td colspan="8" style="">No payment entries found...</td></tr>';
		}
		dBody.innerHTML += rowSrc;
	}
}