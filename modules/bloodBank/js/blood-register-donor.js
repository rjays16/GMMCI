
var currentPage=0, lastPage=0;
var FIRST_PAGE=1, PREV_PAGE=2, NEXT_PAGE=3, LAST_PAGE=4, SET_PAGE=0;
var AJAXTimerID=0;
var editDonorDetails = new Array();
function tabClick(el)
{
		if (el.className!='segActiveTab') {
			$('mode').value = el.getAttribute('segSetMode');
			var dList = $(el.parentNode);
			if (dList) {
				var listItems = dList.getElementsByTagName("LI");
				for (var i=0;i<listItems.length;i++) {
					if (listItems[i] != el) {
						listItems[i].className = "";
						if ($(listItems[i].getAttribute('segTab'))) $(listItems[i].getAttribute('segTab')).style.display = "none";
					}
				}
				if ($(el.getAttribute('segTab'))) 
					$(el.getAttribute('segTab')).style.display = "block";
				el.className = "segActiveTab";
			}
		}
}

function validate()
{
		switch($('mode').value) {
			case 'date':
				if ($F('seldate') == "specificdate") {
					if (!$('specificdate').value) {
						alert('Please input a date');
						$('specificdate').focus();
						return false;
					}
				}
				else if ($F('seldate') == "between") {
					if (!$('between1').value) {
						alert('Please input a date');
						$('between1').focus();
						return false;
					}
					if (!$('between2').value) {
						alert('Please input a date');
						$('between2').focus();
						return false;
					}
				}
			break;
			
			default:
			break;
		}
}

function tooltip(text) 
{
				return overlib('<span style="font:bold 11px Tahoma">'+text+'</span>',
						TEXTPADDING,4, BORDER,0,
						VAUTO, WRAP);
}
		
function startAJAXSave()
{
	var sex, civilstat, blood;
			//save gender
			for(i=0;i<document.suchform.donor_sex.length;i++)
			{
					if(document.suchform.donor_sex[i].checked)
					{
						 //alert('sex='+document.suchform.donor_sex[i].value); 
						 sex=document.suchform.donor_sex[i].value
					}
			}
			//save civil status
			for(i=0;i<document.suchform.donor_civilstat.length;i++)
			{
					if(document.suchform.donor_civilstat[i].checked)
					{
						 //alert('sex='+document.suchform.donor_sex[i].value); 
						 civilstat=document.suchform.donor_civilstat[i].value
					}
			}
			//save blood type
			for(i=0;i<document.suchform.donor_bloodtype.length;i++)
			{
					if(document.suchform.donor_bloodtype[i].checked)
					{
						 //alert('sex='+document.suchform.donor_sex[i].value); 
						 blood=document.suchform.donor_bloodtype[i].value
					}
			}
			
			//save to ajax
			var donor_details=new Array($('donor_lname').value,$('donor_fname').value, $('donor_mname').value, $('donor_bdate').value,$('donor_age').value,$('donor_street').value,$('donor_brgy').value,$('donor_mun').value,sex,blood,civilstat);
			xajax_registerBloodDonor(donor_details);
	}

function computeAge()
{
				birthdate=$('donor_bdate').value;
				xajax_computeAge(birthdate);
}

function printAge(age)
{
		//document.getElementById('bdonor_age').style.display='';
		document.getElementById('donor_age').value=age;
} 

function startAJAXSearch(page)
{
		var searchID=$('donor_search').value;
		//alert('search id='+searchID);
		if (page)    
				document.getElementById('pagekey').value = page;
		else
				document.getElementById('pagekey').value = '0';
				
		if (AJAXTimerID) clearTimeout(AJAXTimerID);
		//$('donorlist-body').style.display = "";
		//$("ajax-loading").style.display = "";
		AJAXTimerID = setTimeout("xajax_populateDonorList('"+searchID+"',"+page+")",50);
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
				//$("ajax-loading").style.display = "none";
				$("donorlist-body").style.display = "";
				searchEL.style.color = "";
		}
}


function viewDonorList(listID,donorID,name,address,age,reg_date,blood_type)
{
		var list=$(listID), dRows, dBody, rowSrc;
		var i;
		var classified, mode, editlink;
		 //alert("hello");
		if (list) {
		//alert("hi");
				dBody=list.getElementsByTagName("tbody")[0];
				dRows=dBody.getElementsByTagName("tr");
				if (donorID) {
				//alert("donor id="+donorID);
						alt = (dRows.length%2)+1;
						 text1="Edit Item";
						 text2="Delete Item";
						 text3="Add Blood";
						 
						rowSrc = '<tr class="wardlistrow'+alt+'" id="row'+addslashes(donorID)+'" value="'+donorID+'">'+
														'<td width="10%" align="center"><span style="font:bold 11px Arial;color:#660000">'+donorID+'</span></td>'+ 
														'<td width="*%" align="left">'+name+'</td>'+
														'<td width="*%" align="left">'+address+'</td>'+
														'<td width="5%" align="center">'+age+'</td>'+
														'<td width="10%" align="center">'+reg_date+'</td>'+
														'<td width="5%" align="center">'+blood_type+'</td>'+
														'<td align="right">'+
						'<input class="jedButton" type="image" id="add_blood" border="0" src="../../images/laboratory/blood_note.png" onclick="AddBlood(\''+donorID+'\'); return false;" onmouseover="tooltip(\''+text3+'\');" onMouseout="return nd();"/> '+ 
						'<input class="jedButton" type="image" id="edit" border="0" src="../../images/edit.gif" onclick="EditItem(\''+donorID+'\'); return false;" onmouseover="tooltip(\''+text1+'\');" onMouseout="return nd();"/> '+
						'<input class="jedButton" type="image" id="delete" border="0" src="../../images/delete.gif" onclick="DeleteItem(\''+donorID+'\'); return false;" onmouseover="tooltip(\''+text2+'\');" onMouseout="return nd();"/> '+  
														'</td>'+ 
											'</tr>';        
				} 
				else {
						rowSrc = '<tr><td colspan="10" style="">No Donor ID or Donor Name searched...</td></tr>';
				}
				dBody.innerHTML += rowSrc;
				//alert("dBody="+dBody.innerHTML); 
		}
		 
}

function AddBlood(addID)
{
			return overlib(
					OLiframeContent('seg_blood_donor_add_tray.php?donorID='+addID, 485, 300, 'fOrderTray', 1, 'auto'),
																	WIDTH,440, TEXTPADDING,0, BORDER,0, 
																		STICKY, SCROLL, CLOSECLICK, MODAL, 
																		CLOSETEXT, '<img src=../../images/close.gif border=0 >',
																 CAPTIONPADDING,4, CAPTION,'Donate Blood',
																 MIDX,0, MIDY,0, 
																 STATUS,'Donate Blood');
}

function setDonorDetails(details_array)
{
		editDonorDetails = Array(details_array);    
}

function EditItem(editID)
{
		return overlib(
					OLiframeContent('seg_blood_donor_tray.php?donorID='+editID, 685, 350, 'fOrderTray', 1, 'auto'),
																	WIDTH,440, TEXTPADDING,0, BORDER,0, 
																		STICKY, SCROLL, CLOSECLICK, MODAL, 
																		CLOSETEXT, '<img src=../../images/close.gif border=0 >',
																 CAPTIONPADDING,4, CAPTION,'Edit Blood Donor Identification',
																 MIDX,0, MIDY,0, 
																 STATUS,'Edit Blood Donor Identification');
}

function DeleteItem(delID)
{
		var reply = confirm("Are you sure you want to delete blood donor record #"+delID+"?");
		if(reply)
		{
			 xajax_deleteBloodDonor(delID);
		}
}

function mouseOver(id)
{
						return overlib( id, CAPTION,"Donor ID",  
													 TEXTPADDING, 8, CAPTIONPADDING, 4, TEXTFONTCLASS, 'oltxt', CAPTIONFONTCLASS, 'olcap', 
													WIDTH, 550,FGCLASS,'olfgjustify',FGCOLOR, '#bbddff',FIXX, 20,FIXY, 20);    
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
}

function checkEnter(e){
		//alert('e = '+e);    
		var characterCode; //literal character code will be stored in this variable

		if(e && e.which){ //if which property of event object is supported (NN4)
				e = e;
				characterCode = e.which; //character code is contained in NN4's which property
		}else{
				//e = event;
				characterCode = e.keyCode; //character code is contained in IE's keyCode property
		}

		if(characterCode == 13){ //if generated character code is equal to ascii 13 (if enter key)
				startAJAXSearch(0);
		}else{
				return true;
		}        
}

function registerDonor()
{
	return overlib(
		OLiframeContent('seg_blood_donor_register_tray.php', 720, 400, 'fOrderTray', 1, 'auto'),
			WIDTH,440, TEXTPADDING,0, BORDER,0, 
			STICKY, SCROLL, CLOSECLICK, MODAL,
			CLOSETEXT, '<img src=../../images/close_red.gif border=0 >', 
			CAPTIONPADDING,4, 
			CAPTION,'Blood Donor Registration',
			MIDX,0, MIDY,0, 
			STATUS,'Blood Donor Registration'
		);
}