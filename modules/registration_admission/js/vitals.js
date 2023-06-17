function formatNumber(num,dec) {
		var nf = new NumberFormat(num);
		if (isNaN(dec)) dec = nf.NO_ROUNDING;
		nf.setPlaces(dec);
		return nf.toFormatted();
}

function key_check(e, value) {
	 var character = String.fromCharCode(e.keyCode);
	 var number = /^\d+$/;
	 var reg = /^[-+]?[0-9]+((\.)|(\.[0-9]+))?$/;
	 if (character=='¾') {
		 character = '.';
	 }
	 //alert('e = '+e.keyCode);
	 var text_value = value+character;
	 if ((e.keyCode==190 && text_value.match(reg)!=null) || (e.keyCode==46 || e.keyCode==8 || e.keyCode==16 || e.keyCode==9 || e.keyCode==110 || (e.keyCode>=36 && e.keyCode<=40) || (e.keyCode>=96 && e.keyCode<=105))) {
			return true;
	 }
	 if (character.match(number)==null) {
		 return false;
	 }
}

function convertNumberValue(id, val){
		if ($(id)) $(id).value = formatNumber(val, 2)
		if ($(id)) $(id).setAttribute('value',val)
}

function clearIssue(list) {
		if (!list) list = $('vital-list')
		if (list) {
				var dBody=list.getElementsByTagName("tbody")[0]
				if (dBody) {
						trayItems = 0
						dBody.innerHTML = ""
						return true
				}
		}
		return false
}


function appendTheVitalList(list, details, disabled) {
		if (!list) list = $('vital-list');
		if (list) {
				var dBody=list.getElementsByTagName("tbody")[0];

				if (dBody) {
						var src;
						var lastRowNum = null,
										refno = document.getElementsByName('vitalno[]'),
										dRows = dBody.getElementsByTagName("tr");
						if (details) {

								var trayItems = 0,
										date = details.date,
										vitalno = details.vitalno,
										encnr = details.encnr,
										bp = details.bp,
										temp = details.temp,
										weight = details.weight,
										resprate = details.resprate,
										pid = details.pid,
										pulserate = details.pulserate;


								 if (vitalno) {
												if ($('rowvitalno'+vitalno)) {

														$('rowvitalno'+vitalno).value     =   details.vitalno
														$('rowencnr'+vitalno).value     =   details.encnr
														$('rowbp'+vitalno).value    = details.bp
														$('rowtemp'+vitalno).value            = details.temp
														$('rowweight'+vitalno).value        = details.weight
														$('rowresprate'+vitalno).value        = details.resprate
														$('rowpulserate'+vitalno).value        = details.pulserate
														$('rowpid'+vitalno).value        = details.pid
														$('rowdate'+vitalno).value     =   details.date

														return true
												}
												if (refno.length == 0) clearIssue(list)
								 }

								alt = (dRows.length%2)+1

								var disabledAttrib = disabled ? 'disabled="disabled"' : ""

								src =
										'<tr class="wardlistrow'+alt+'" id="row'+vitalno+'">' +
										'<input type="hidden" name="vitalno[]" id="rowvitalno'+vitalno+'" value="'+details.vitalno+'" />'+
										'<input type="hidden" name="encnr[]" id="rowencnr'+vitalno+'" value="'+details.encnr+'" />'+
										'<input type="hidden" name="date[]" id="rowdate'+vitalno+'" value="'+details.date+'" />'+
										'<input type="hidden" name="bp[]" id="rowbp'+vitalno+'" value="'+details.bp+'" />'+
										'<input type="hidden" name="temp[]" id="rowtemp'+vitalno+'" value="'+details.temp+'" />'+
										'<input type="hidden" name="weight[]" id="rowweight'+vitalno+'" value="'+details.weight+'" />'+
										'<input type="hidden" name="resprate[]" id="rowresprate'+vitalno+'" value="'+details.resprate+'" />'+
										'<input type="hidden" name="pid[]" id="rowpid'+vitalno+'" value="'+details.pid+'" />'+
										'<input type="hidden" name="pulserate[]" id="rowpulserate'+vitalno+'" value="'+details.pulserate+'" />';


								src+=
										'<td align="center">'+details.encnr+'</td>'+
										'<td align="center"><span style="color:#660000">'+details.date+'</span></td>'+
										'<td align="center"><span style="color:#660000">'+details.bp+'</span></td>'+
										'<td align="center"><span style="color:#660000">'+details.temp+'</span></td>'+
										'<td align="center"><span style="color:#660000">'+details.weight+'</span></td>'+
										'<td align="center"><span style="color:#660000">'+details.resprate+'</span></td>'+
										'<td align="center"><span style="color:#660000">'+details.pulserate+'</span></td>'+
										'<td>'+
												'<input type="button" id="editvital'+vitalno+'" value="Edit" style="color:#000066; font-weight:bold; padding:0px 2px" '+
														'onclick="editVital(\''+vitalno+'\',\''+pid+'\',\''+encnr+'\')" '+
												'/>'+
										'</td>'+
										'<td>'+
												'<input type="button" id="deletevital'+vitalno+'" value="Delete" style="color:#000066; font-weight:bold; padding:0px 2px" '+
														'onclick="deleteVital(\''+vitalno+'\',\''+pid+'\',\''+encnr+'\')" '+
												'/>'+
										'</td>'+

										'</tr>';

								trayItems++;
						}
						else {
								src = "<tr><td colspan=\"10\">Vital sign list is currently empty...</td></tr>";
						}
						dBody.innerHTML += src;
						return true;
				}
		}
		return false;
}
