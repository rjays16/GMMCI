var currentPage=0, lastPage=0;
var FIRST_PAGE=1, PREV_PAGE=2, NEXT_PAGE=3, LAST_PAGE=4, SET_PAGE=0;
var bEncountersList = 0;
var curpid = '';

function prepareSelect(id, billdate, billnr, ddate, f_ddate, age, phic_nr, type, totalbill, bill_dte) {
		var nr = $('nr'+id).value;
		var pid = $('id'+id).innerHTML;
		var pname = $('pname'+id).innerHTML;
		var addr = $('addr'+id).innerHTML;
		var adm_dt = $('admission_dt'+id).value;
		var bill_dt;
		var prev_enc = 0;
		var bill_type = $('bill_type').value;//Added By Jarel 11/14/2013 for PHIC new circular
		var promi_refno;

		if($('promi_refno'+id)){
			promi_refno = $('promi_refno'+id).value;
		}

		if(window.parent.$('billdate')){
		if ( billdate == '')
				bill_dt = window.parent.$('billdate').value;
		else {
				bill_dt = billdate;
				window.parent.$('billdate').value = bill_dt;
					if(window.parent.$('show_billdate')){
				window.parent.$('show_billdate').innerHTML = formatDate(new Date(getDateFromFormat(bill_dt, 'yyyy-MM-dd HH:mm:ss')), 'NNN d, yyyy hh:mma');
				prev_enc = 1;
		}
			}
		}

		window.parent.$('pid').value = pid;
		window.parent.$('encounter_nr').value = nr;
		window.parent.$('pname').value = pname;
		window.parent.$('paddress').value = addr;
		window.parent.$('admission_date').value = adm_dt;
		window.parent.$('age').value = age;
        window.parent.$('admission_dte').value = formatDate(new Date(getDateFromFormat(adm_dt, 'NNN d, yyyy hh:mma')), 'yyyy-MM-dd');

		window.parent.$('enc').value = nr;
		
		if(window.parent.$('phic')){
			window.parent.$('phic').value = phic_nr;
		}

		if(window.parent.$('memcateg_enc')){
		window.parent.$('memcateg_enc').value = nr;
		}
		
		if(window.parent.$('acc_enc_nr')){
		window.parent.$('acc_enc_nr').value = nr;
		}

		if(window.parent.$('opacc_enc_nr')){
		window.parent.$('opacc_enc_nr').value = nr;
	        }
		
		if(window.parent.$('total_bill')){
			window.parent.$('total_bill').value = totalbill;
		}

		if(window.parent.$('mode_of_promi')){
			window.parent.$('mode_of_promi').value = 'add';
		}

		if(window.parent.$('billdate_promi')){
			window.parent.$('billdate_promi').value = formatDate(new Date(getDateFromFormat(bill_dte, 'yyyy-MM-dd HH:mm:ss')), 'NNN d, yyyy');
		}

		if(window.parent.$('refno_promi')){
			window.parent.getData(nr);
		}
		
		if(window.parent.$('confine_type')){
			var confine_type = '';
			switch(type){
				case 1:
					confine_type = 'ER Consult';
					break;
				case 2:
					confine_type = 'OPD Consult';
					break;
				case 3:
					confine_type = 'ER Inpatient';
					break;
				case 4:
					confine_type = 'OPD Inpatient';
					break;
				case 12:
					confine_type = 'Well Baby';
					break;
			}

			window.parent.$('confine_type').value = confine_type;
		}

		if(window.parent.$('phic')){
			window.parent.$('phic').value = phic_nr;
		}
	
		if (nr) {
				//window.parent.pSearchClose();
				if (prev_enc) {
					if(bill_type!='phic')
					{	
						if (billnr != ''){
							setDeathDate(ddate,f_ddate,bill_type);
							window.parent.calcPrevBill(nr, bill_dt, billnr);
						}
						else
							alert('No billing saved for this encounter!');
					}
					else
					{
						setDeathDate(ddate,f_ddate,bill_type);
						window.parent.closeSelEncDiaglog();
					}
				}
				else
				{
					if(bill_type!='phic')
					{
						setDeathDate(ddate,f_ddate,bill_type);
						window.parent.clickHandler(nr, bill_dt);
					}
					else
					{
						setDeathDate(ddate,f_ddate,bill_type);
						window.parent.closeSelEncDiaglog();
						
					}
					
				}
		}
		else
				window.parent.cClick();

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

function setPagination(pageno, lastpage, pagen, total) {
		currentPage=parseInt(pageno);
		lastPage=parseInt(lastpage);
		firstRec = (parseInt(pageno)*pagen)+1;
		if (currentPage==lastPage)
				lastRec = total;
		else
				lastRec = (parseInt(pageno)+1)*pagen;
		$("pageShow").innerHTML = '<span>Showing '+(firstRec)+'-'+(lastRec)+' out of '+(parseInt(total))+' record(s).</span>';
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
						if (bEncountersList)
							showAllEncounters(curpid, 0);
						else
							startAJAXSearch('search',0);
				break;
				case PREV_PAGE:
						if (currentPage==0) return false;
						if (bEncountersList)
							showAllEncounters(curpid, currentPage-1);
						else
							startAJAXSearch('search',currentPage-1);
				break;
				case NEXT_PAGE:
						if (currentPage >= lastPage) return false;
						if (bEncountersList)
							showAllEncounters(curpid,parseInt(currentPage)+1);
						else
							startAJAXSearch('search',parseInt(currentPage)+1);
				break;
				case LAST_PAGE:
						if (currentPage >= lastPage) return false;
						if (bEncountersList)
							showAllEncounters(curpid, lastPage);
						else
							startAJAXSearch('search',lastPage);
				break;
		}
}

function showAllEncounters(searchID, page) {
		bEncountersList = true;
		var searchEL = $('searchkey');
		if (searchID) {
				curpid = searchID;
				searchEL.style.color = "#0000ff";
				if (AJAXTimerID) clearTimeout(AJAXTimerID);
				$("ajax-loading").style.display = "";
				$("person-list-body").style.display = "none";
				AJAXTimerID = setTimeout("xajax_populateEncountersList('searchkey',"+page+",'"+searchID+"')", 100);
//        lastSearch = searchEL.value;
		}
}

function addPerson(listID, phic_nr, id, pname, dob, ddate, f_ddate, c_prd, c_class, c_type, sex, addr, status, nr, type, ad, bill_dte, totalbill, promi_refno, age, billdate, billnr, indx) {
		var list=$(listID), dRows, dBody, rowSrc, deathfxn, encfxn;
		var i, hddn='', bhddn='';

//		var bnoshow = ((typeof dschrg_dt == 'undefined') || (typeof dschrg_tm == 'undefined')) ? 0 : 1;
		var bnoshow = (typeof billdate == 'undefined') ? 0 : 1;

		if (list) {
				dBody=list.getElementsByTagName("tbody")[0];
				dRows=dBody.getElementsByTagName("tr");
				// get the last row id and extract the current row no.
				if (id) {
						pid = id;
						if (bnoshow) id += indx;

						if (sex=='m')
								sexImg = '<img src="../../gui/img/common/default/spm.gif" border="0" />';
						else if (sex=='f')
								sexImg = '<img src="../../gui/img/common/default/spf.gif" border="0" />';
						else
								sexImg = '';
							
						if (type==0) typ="Walkin";
						else if (type==1) typ='<a title="'+nr+'" href="#">ER Consult</a>';
						else if (type==2) typ='<a title="'+nr+'" href="#">OPD Consult</a>';
						else if (type==3) typ='<a title="'+nr+'" href="#">ER Inpatient</a>';
						else if (type==4) typ='<a title="'+nr+'" href="#">OPD Inpatient</a>';
						else if (type==12) typ='<a title="'+nr+'" href="#">Well Baby</a>';
						else typ='typ="Walkin"';
						//hidden inputs
						hddn =  '<input type="hidden" id="admission_dt'+id+'" value="'+ad+'">'+
										'<input type="hidden" id="type'+id+'" value="'+type+'">'+
										'<input type="hidden" id="nr'+id+'" value="'+nr+'">';

						if(bnoshow){ 
							encfxn =  " prepareSelect('"+id+"', '"+billdate+"', '"+billnr+"','"+ddate+"','"+f_ddate+"','"+age+"','"+phic_nr+"',"+type+",'"+totalbill+"','"+bill_dte+"');";
						}else{
							encfxn =  " prepareSelect('"+id+"','','','"+ddate+"','"+f_ddate+"','"+age+"','"+phic_nr+"',"+type+",'"+totalbill+"','"+bill_dte+"')";
						}
						
						rowSrc = '<tr>'+
														'<td>'+hddn+bhddn+
																		'<span id="id'+id+'" style="color:#660000">'+pid+'</span>'+
														'</td>'+
														'<td>'+sexImg+'</td>'+
														'<td><span id="pname'+id+'">'+pname+'</span></td>'+
														'<td><span id="addr'+id+'" style="display:none">'+addr+'</span><span>'+dob+'</span></td>'+
														'<td align="center"><span id="confine_prd'+id+'">'+c_prd+'</span></td>'+
														'<td><span id="confine_class'+id+'">'+c_class+'</span></td>'+
														'<td align="center"><span id="confine_type'+id+'">'+typ+'</span></td>'+
														'<td><span id="enc_nr'+id+'">'+nr+'</span></td>'+
														(bnoshow ? '<td>&nbsp;</td>' : '<td><img name="enc_list'+id+'" id="enc_list'+id+'" src="../../images/encounters_list.gif" style="cursor:pointer" border="0" onClick="showAllEncounters('+id+',0);"/></td>') +
														'<td>'+
																'<input type="button" value="Select" style="cursor:pointer; color:#000066; font-weight:bold; padding:0px 2px" '+
																		'onclick="'+encfxn+'"'+
																'/>'+
																'<input type= "hidden" id="promi_refno'+id+'" value="'+promi_refno+'">'+
														'</td>'+
										'</tr>';

//                            '<td><span id="fname'+id+'">'+fname+'</span></td>'+
//                            '<td><span id="mname'+id+'">'+midname+'</span></td>'+
//                            '<td><span id="addr'+id+'" style="display:none">'+addr+'</span>'+zip+'</td>'+
//                            '<td><span>'+status+'</span></td>'+
//                            '<td align="center"><span>'+typ+'</span></td>'+
//                            '<td>'+
//                                '<input type="button" value="Select" style="cursor:pointer; color:#000066; font-weight:bold; padding:0px 2px" '+
//                                    'onclick="prepareSelect(\''+id+'\')" '+
//                                '/>'+
//                            '</td>'+
//                    '</tr>';
				}
				else {
						rowSrc = '<tr><td colspan="10" style="">No such person exists...</td></tr>';
				}
				dBody.innerHTML += rowSrc;
		}
}

//added by Jarel 05/22/2013
function setDeathDate(ddate,f_ddate,bill_type){
	if(f_ddate!=''){
		if(window.parent.$('isdied')){
		window.parent.$('isdied').checked = true;
		window.parent.$('deathdate').value = ddate;
		if(bill_type=='phic'){
		window.parent.$('death_date').value = f_ddate;
		}else{
			window.parent.$('death_date').innerHTML = f_ddate;
		}
			
		window.parent.toggleDeathDate(0);
		}
	}else{
		if(window.parent.$('isdied')){
		window.parent.$('isdied').checked = false;
		window.parent.toggleDeathDate(0);
	}
}
}
