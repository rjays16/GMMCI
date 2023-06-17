var totalDiscount = 0, totalDiscountedAmount=0, totalNet=0, totalNONSocializedAmount=0;
var HSM = "HOSPITAL SPONSORED MEMBER";
var NBB = "SPONSORED MEMBER";

function formatNumber(num,dec) {
	var nf = new NumberFormat(num);
	if (isNaN(dec)) dec = nf.NO_ROUNDING;
	nf.setPlaces(dec);
	return nf.toFormatted();
}

function getTotalServCharges(){ //added by maimai 11-21-2014
	var encounter_nr = $('encounter_nr').value;
	if(encounter_nr){
		xajax_getTotalCharge(encounter_nr);
	}
}

function clearEncounter() {
	var iscash = $("iscash1").checked;
	$('ordername').value="";
	$('orderaddress').value="";
	$('pid').value="";
	$('encounter_nr').value="";
	$('clear-enc').disabled = true;
	$('discount').value = '';
	$('discountid').value = '';

	$('refno').value = '';
	$('mode').value="save";

	$('btndiscount').disabled = false;
	$('sw-class').innerHTML = '';
	$('patient_enctype').innerHTML = '';
		
	$('patient_location').innerHTML = '';
	$('patient_medico_legal').innerHTML = '';

	$('current_att_dr_nr').value = '';
	$('current_dept_nr').value = '';
	$('impression').value = '';
	$('hrn').innerHTML = '';
	$('dob').innerHTML = '';
	$('sex').innerHTML = '';
	$('age').innerHTML = '';
	$('is_walkin').checked = false;

	if (iscash==true)
		$('is_cash').value = 1;
	else
		$('is_cash').value = 0;

	$('btnHistory').style.display = "none";
    $('btn-coverage').style.display = "none";
	$('ptype').value = "";
	$('orig_discountid').value = "";
	$('discount2').value = "";
	$('gender').value = "";
	$('date_birth').value = "";
	$('adm_diagnosis').innerHTML = "";

	$('ic_row').style.display = "none";
	$('is_charge2comp').checked = false;
	$('compName').value = "";
	$('compID').value = "";
	//$('source_req').value = "";

    $('iscash1').checked = true; 
    $('iscash0').checked = false; 
    $('is_cash').value = 1;
    $('grant_type').value = "";
    $('type_charge').style.display='none';
    $('for_manual').disabled = false;
    $('btn-coverage').style.display = "none";
    $('cov_type').update('');
    $('cov_amount').update('');
    $('coverage').setAttribute('value',-1);
    $('phic_ajax').hide();

    
    $('warningcaption').innerHTML = '';
    
    
    if (!iscash) {
        updateCoverage(['']);
    }

	setPriority(0);
}

function pSearchClose() {
    var nr = $('encounter_nr').value;
    //updatePHICCoverage([nr]);
    if (!$("iscash1").checked) {
        updateCoverage([nr]);
    }    
	cClick();  //function in 'overlibmws.js'
}

function emptyTray() {
	
	//added by BORJ 10-25-2013
	//block combobox change
	var prev_selected = $('grant_type').value;
	if ($('area').value=='clinic')
		{	
			if($('ptype').value==2 && $('phic_nr').length!=1) 
				{
			if($('grant_type').innerHTML == 'personal')
			return;
				if($('grant_type').value == 'phic' || $('grant_type').value == 'mission')
				{
  		    }
		else
			{
				$('grant_type').value = prev_selected; //edited by mai 07/25/2014
		}
	}
}	
	//end
		
	var items = document.getElementsByName('items[]');
	var id;
	var source = $('dept_area');
	var isERIP = $('isERIP').value;
	var ptype = $('ptype').value;
	var area_type = $('area_type').value;

	for (var i=0;i<items.length;i++) {
		id = items[i].value;
		$('rowID'+id).parentNode.removeChild($('rowID'+id));
		$('rowPrcCash'+id).parentNode.removeChild($('rowPrcCash'+id));
		$('rowPrcCharge'+id).parentNode.removeChild($('rowPrcCharge'+id));
		$('rowPrcNet'+id).parentNode.removeChild($('rowPrcNet'+id));
		$('rowQty'+id).parentNode.removeChild($('rowQty'+id));
		$('sservice'+id).parentNode.removeChild($('sservice'+id));
	}

	if (isERIP){
		if (source.value=='lab'){
			if (((ptype==3)||(ptype==4))&&(area_type=='ch')){
				enableSubmitButton(0);
			}
		}else{
			enableSubmitButton(1);
		}

	}else{
			enableSubmitButton(1);
	}

	clearOrder($('order-list'));
	appendOrder($('order-list'),null);
	refreshDiscount();
}


function reclassRows(list,startIndex) {
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			var dRows = dBody.getElementsByTagName("tr");
			if (dRows) {
				for (i=startIndex;i<dRows.length;i++) {
					dRows[i].className = "wardlistrow"+(i%2+1);
				}
			}
		}
	}
}

function clearOrder(list) {
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0];
		$('socialServiceNotes').style.display='none';
		if (dBody) {
			trayItems = 0;
			dBody.innerHTML = "";
			return true;
		}
	}
	return false;
}

function showSocialNotes() {
	var isShow='none';
	var sservice = document.getElementsByName('sservice[]');
	for (var i=0;i<sservice.length;i++) {
		if (sservice[i].value == 0) {
			isShow='';   //there is still a nonsocialized item in the list
		}
	}
	$('socialServiceNotes').style.display=isShow;
}

//added by VAN 07-26-2013
//in_array function
Array.prototype.in_array = function(p_val) {
    for(var i = 0, l = this.length; i < l; i++) {
        if(this[i] == p_val) {
            return true;
        }
    }
    return false;
}

//added by VAN 07-30-2013
//array intersection
Array.prototype.contains = function(elem) {
    return(this.indexOf(elem) > -1);
};

Array.prototype.intersect = function( array ) {
    // this is naive--could use some optimization
    var result = [];
    for ( var i = 0; i < this.length; i++ ) {
        if ( array.contains(this[i]) && !result.contains(this[i]) )
            result.push( this[i] );
    }
    return result;
}

function appendOrder(list,details) {
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0];

		if (dBody) {
			var isCash = $("iscash1").checked;

			var totalNetCash;
			var src, toolTipText;
			var btnicon;
			var paidcnt = 0;
			var lastRowNum = null,
					items = document.getElementsByName('items[]');
					dRows = dBody.getElementsByTagName("tr");
			var nf = new NumberFormat();
			var ptype = $('ptype').value;
			var forwarding, monitor, everyhour;

			nf.setPlaces(2);
			//alert('details = '+details);
			if (details) {
				var id = details.id,
					idGrp = details.idGrp,
					qty = parseFloat(details.qty),
					prcCash = parseFloat(details.prcCash),
					prcCharge = parseFloat(details.prcCharge);
					net_price = parseFloat(details.net_price);

					totalNetCash = net_price*qty;
					//alert('1 totalNetCash = '+totalNetCash);
					alt = (dRows.length%2)+1;
					nf.setNumber(qty);
					nf.setPlaces(nf.NO_ROUNDING);
					qty = isNaN(qty) ? '0' : ''+nf.toFormatted();

					nf.setPlaces(2);
					nf.setNumber(prcCash);
					prcCash = isNaN(prcCash) ? 'N/A' : nf.toFormatted();
					nf.setNumber(prcCharge);
					prcCharge = isNaN(prcCharge) ? 'N/A' : nf.toFormatted();

					nf.setNumber(totalNetCash);
					totalNetCash = isNaN(totalNetCash) ? 'N/A' : nf.toFormatted();

				if (isCash) {
					prc=prcCash;
				}
				else {
					prc=prcCharge;
				}

				tot = totalNetCash;
				//alert('js= '+tot);
				//var person_discountid = $("discountid").value;

				toolTipText = "Requesting doctor: <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"+details.requestDocName+" <br>"+
									"Clinical Impression: <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"+details.clinicInfo;

                //added by VAS 03-21-2012
                // Check coverage limit
                    if($('grant_type').value=="phic") {
                        //var coverageLimit = parseFloatEx($('cov_amount').innerHTML);
                        var coverageLimit = parseFloatEx($('coverage').value); 
                        total= parseFloatEx(total) + parseFloatEx(tot.replace(",","")); 
                        if (coverageLimit!=-1) {
                            if (coverageLimit < total) {
                                alert("Coverage limit exceeded for this item...");
                                return true;
                            }
                        }
                    }

				if (items) {
					for (var i=0;i<items.length;i++) {
                        //if encoded test is already on the request tray
						if (items[i].value == details.id) {
							$('toolTipText'+id).value = toolTipText;
							$('rowPrcCash'+id).value = details.prcCash;
							$('rowPrcCharge'+id).value = details.prcCharge;
							$('rowPrcNet'+id).value = details.net_price;
							$('rowDoc'+id).value = details.requestDoc;
							$('rowDocName'+id).value = details.requestDocName;
							$('rowDept'+id).value = details.requestDept;
							$('rowHouse'+id).value = details.is_in_house;
							$('rowInfo'+id).value = details.clinicInfo;
							$('rowQty'+id).value = details.qty;
							document.getElementById('idGrp'+id).innerHTML = id;
							document.getElementById('name'+id).innerHTML = details.name;
							document.getElementById('prc'+id).innerHTML = prc;
							document.getElementById('tot'+id).innerHTML = tot;
							//alert('update = '+tot);

							if (($("iscash0").checked)||($('hasPaid').value==1))
								disabled = "";
							else
								disabled = "disabled";

							serial_icon = '<img name="is_serial'+id+'" id="is_serial'+id+'" src="../../images/cashier_edit_3.gif" border="0" onClick="post_serialTest(\''+id+'\');" style="cursor:pointer" title="Test is a SERIAL">';
                            inLIS_icon = '<img name="in_LIS'+id+'" id="in_LIS'+id+'" src="../../images/charity.gif" border="0" title="The Test is already in LIS!">';;
                            notinLIS_icon = '<img name="not_inLIS'+id+'" id="not_inLIS'+id+'" src="../../images/notok.gif" border="0" onClick="post_LISTest(\''+id+'\');" style="cursor:pointer" title="Click the icon to post the Test to LIS">';;
                
                            if (details.is_serial){
                                forwarding = serial_icon;
                            }else{
                                forwarding = '<input type="checkbox" name="withsampleID[]" id="withsampleID'+id+'" '+disabled+' value="1" />'+
                                             '<input type="hidden" name="group[]" id="group'+id+'" value="'+details.group+'">';
                            } 
                           
                            if (details.inLIS)            
                                inLIS_icon = inLIS_icon;
                            else
                                inLIS_icon = notinLIS_icon;    
                                 
							monitor = '<input type="checkbox" name="monitor[]" id="monitor'+id+'" value="1" onClick=formonitoring(\''+id+'\'); />';
							everyhour = '<input type="text" name="hour[]" id="hour'+id+'" size="3" maxlength="2" disabled value="" oncClick=checkValidity(this.value); onkeydown="return key_check(event, this.value)" />';

							//document.getElementById('is_forward-row'+id).innerHTML = forwarding+' '+inLIS_icon;
                            document.getElementById('is_forward-row'+id).innerHTML = forwarding;
							document.getElementById('is_monitor-row'+id).innerHTML = monitor;
							document.getElementById('everyhour-row'+id).innerHTML = everyhour;

							var name_serv = details.name;
							alert('"'+name_serv.toUpperCase()+'" is already in the list & has been UPDATED!');
							return true;
						}else{
                            //if the encoded test is already in the requested profile test
                            //if a profile test is already in the tray, ex. LIPID and then CHOL
                            if (details.is_profile==0){
                                if ($('is_profile'+items[i].value).value==1){
                                    var string = $('child_test'+items[i].value).value;
                                    var arr_test = new Array();
                                    arr_test = string.split(",");
                                    
                                    if(arr_test.in_array(details.id)){
                                        var name_serv = details.name;
                                        var name_serv_parent = $('nameitems'+items[i].value).value;
                                        alert('"'+name_serv.toUpperCase()+'" is already in the list. It is already in the "'+name_serv_parent.toUpperCase()+'"!');
                                        return true;
                                    }
                                }    
                            }else{
                                //if a single test is already in the tray and a profile is encoded, 
                                //ex. CHOL and then LIPID
                                //check if a certain test is already in the encoded profile
                                
                                if ((details.is_package==1)&&(details.is_profile==1)){
                                    if ($('is_profile'+items[i].value).value==0){
                                        var string = details.child_test;
                                        var arr_test = new Array();
                                        arr_test = string.split(",");
                                        
                                        if(arr_test.in_array(items[i].value)){
                                            var name_serv_parent = details.name;
                                            var name_serv = $('nameitems'+items[i].value).value;
                                            alert('"'+name_serv.toUpperCase()+'" is already in the "'+name_serv_parent.toUpperCase()+'"! Please remove the "'+name_serv.toUpperCase()+'" in the request tray and add the "'+name_serv_parent.toUpperCase()+'" again.');
                                            return true;
                                        }
                                    }else{
                                        //profile
                                        var string = $('child_test'+items[i].value).value;
                                        var arr_test = new Array();
                                        arr_test = string.split(",");
                                        
                                        var string2 = details.child_test;
                                        var arr_test2 = new Array();
                                        arr_test2 = string2.split(",");
                                        
                                        var arr_result = new Array();
                                        //if there is an intersection
                                        arr_result = arr_test.intersect(arr_test2);
                                        
                                        if(arr_result.length>0){
                                            var name_serv = details.name;
                                            var name_serv_parent = $('nameitems'+items[i].value).value;
                                            alert('"'+name_serv.toUpperCase()+'" is already in the list. It is already in the "'+name_serv_parent.toUpperCase()+'"!');
                                            return true;
                                        }else{
                                            if(arr_test.in_array(details.id)){
                                                var name_serv = details.name;
                                                var name_serv_parent = $('nameitems'+items[i].value).value;
                                                alert('"'+name_serv.toUpperCase()+'" is already in the list. It is already in the "'+name_serv_parent.toUpperCase()+'"!');
                                                return true;
                                            }
                                        }    
                                    }    
                                }  //for not package        
                            }
                        }    
                        
                        
					}
					if (items.length == 0)
						clearOrder(list);
				}

			delitemImg = '<a href="javascript: nd(); removeItem(\''+id+'\');">'+
							 '	<img src="../../images/btn_delitem.gif" border="0"/></a>';
			paiditemImg = '<img src="../../images/btn_paiditem.gif" border="0" onClick="">';
			unpaiditemImg = '<img src="../../images/btn_unpaiditem.gif" border="0" onClick="">';

			charityImg = '<img src="../../images/btn_charity.gif" border="0" onClick="">';
			cmapImg = '<img src="../../images/btn_cmap.gif" border="0" onClick="">';
			lingapImg = '<img src="../../images/btn_lingap.gif" border="0" onClick="">';
			missionImg = '<img src="../../images/btn_mission.gif" border="0" onClick="">';

			// added by VAN 01-15-08
			repeatitemImg = '<img src="../../images/btn_repeat.gif" border="0" onClick="">';

			refno_hasPaid = $F('hasPaid');
			view_mode = 0;
			if ($F('view_from')!='')
				view_mode = 1;
			toolTipTextHandler = ' onMouseOver="return overlib($(\'toolTipText'+id+'\').value, CAPTION,\'Details\',  '+
							'  TEXTPADDING, 8, CAPTIONPADDING, 4, TEXTFONTCLASS, \'oltxt\', CAPTIONFONTCLASS, \'olcap\', '+
							'  WIDTH, 250,FGCLASS,\'olfgjustify\',FGCOLOR, \'#bbddff\');" onmouseout="nd();"';
			nonSocialized='';
			if (details.sservice==0){
				nonSocialized='<img src="../../images/btn_nonsocialized.gif" border="0" onClick=""'+
								' onMouseOver="return overlib(\'This is a non-socialized service which means..secret!\', CAPTION,\'Non-socialized Service\',  '+
								'  TEXTPADDING, 8, CAPTIONPADDING, 4, TEXTFONTCLASS, \'oltxt\', CAPTIONFONTCLASS, \'olcap\', '+
								'  WIDTH, 250,FGCLASS,\'olfgjustify\',FGCOLOR, \'#bbddff\');" onmouseout="nd();">';
				$('socialServiceNotes').style.display='';

			}

			if (view_mode==1)
				btnicon = ((details.hasPaid==1)?paiditemImg:unpaiditemImg);
			else{
				 if ((($('parent_refno').value!=null)||($('parent_refno').value!=""))&&($('repeat').checked)){
						btnicon = repeatitemImg;
				 }else if ($('is_cash').value==1){
						if (($('hasPaid').value==1)||(details.hasPaid)){
							if (details.pay_type!=""){
								if (details.pay_type=='paid')
									 btnicon = paiditemImg;
								else if ((details.pay_type=='lingap') || (details.pay_type=='cmap')
													|| (details.pay_type=='mission') || (details.pay_type=='charity'))
									btnicon = '<img src="../../images/btn_'+details.pay_type+'.gif" border="0" onClick="">';
								else
									btnicon = delitemImg;

								paidcnt =+ 1;
								disabled = "";
							}else{
									if (paidcnt>=1)
										btnicon = unpaiditemImg;
									else
										btnicon = delitemImg;
									disabled = "disabled";
							}

						}else{
								btnicon = delitemImg;
								disabled = "disabled";
						}
				}else{
					if ($('grant_type').value!=""){
						if ($('mode').value=='update'){
							//btnicon = '<img src="../../images/btn_'+$('grant_type').value+'.gif" border="0" onClick="">';
							if ((details.pay_type=='lingap') || (details.pay_type=='cmap')
													|| (details.pay_type=='mission') || (details.pay_type=='charity'))
									btnicon = '<img src="../../images/btn_'+details.pay_type+'.gif" border="0" onClick="">';
							else
									btnicon = delitemImg;

							disabled = "";
						}else{
							btnicon = delitemImg;
							disabled = "disabled";
						}
					}else{
						btnicon = delitemImg;
						disabled = "";
					}
				}
			}

				if (details.is_monitor==1)
					disabled2 = "";
				else
					disabled2 = "disabled";

				if ($('mode').value=='save')
					details.every_hour = '';

				if (($F('view_from')=='ssview') || ($F('view_from')=='override')){
					disabled3 = "disabled";
					disabled = "disabled";
				}else{
					disabled3 = "";
				}

                //commented by VAN 01-17-2013
				//if ($F('ischecklist')==1)
				/*if (($("iscash0").checked)||($('hasPaid').value==1))
					disabled = "";
				else
					disabled = "disabled";*/
                
                serial_icon = '<img name="is_serial'+id+'" id="is_serial'+id+'" src="../../images/cashier_edit_3.gif" border="0" onClick="post_serialTest(\''+id+'\');" style="cursor:pointer" title="Test is a SERIAL">';
                inLIS_icon = '<img name="in_LIS'+id+'" id="in_LIS'+id+'" src="../../images/charity.gif" border="0" title="The Test is already in LIS!">';;
                notinLIS_icon = '<img name="not_inLIS'+id+'" id="not_inLIS'+id+'" src="../../images/notok.gif" border="0" onClick="post_LISTest(\''+id+'\');" style="cursor:pointer" title="Click the icon to post the Test to LIS">';;
                
				//forwarding = '<input type="checkbox" name="withsampleID'+id+'" id="withsampleID'+id+'" '+disabled+' '+((details.is_forward==1)?"'checked'":'')+' value="1" />'+
				//			   '<input type="hidden" name="group'+id+'" id="group'+id+'" value="'+details.group+'">';
                
                if ((details.is_serial==1)&&(details.changed_icon==1)){
                    forwarding = serial_icon;
                
                }else{
                    forwarding = '<input type="checkbox" name="withsampleID'+id+'" id="withsampleID'+id+'" '+disabled+' '+((details.is_forward==1)?'checked':'')+' value="1" />'+
				             '<input type="hidden" name="group'+id+'" id="group'+id+'" value="'+details.group+'">&nbsp;';
                } 
                
                if (($('bill_nr').value!='')&&($("iscash0").checked)){
                    forwarding =  '<img src="../../images/cashier_lock.gif" border="0" title="Can\'t UNDO or DO the specimen check-in. This patient has a saved billing. Please call Billing to delete the BILLING.">';
                }
    
                if (details.inLIS)            
                    inLIS_icon = inLIS_icon;
                else
                    inLIS_icon = notinLIS_icon;    
                    
				//if (($F('hasPaid')==1)||($F('view_from')!='ssview'))
				//monitor = '<input type="checkbox" name="monitor'+id+'" id="monitor'+id+'" '+disabled3+' value="1" '+((details.is_monitor==1)?"'checked'":'')+' onClick=formonitoring(\''+id+'\'); />';
                monitor = '<input type="checkbox" name="monitor'+id+'" id="monitor'+id+'" '+disabled3+' value="1" '+((details.is_monitor==1)?'checked':'')+' onClick=formonitoring(\''+id+'\'); />';
				everyhour = '<input type="text" name="hour'+id+'" id="hour'+id+'" size="3" maxlength="2" '+disabled2+' value="'+details.every_hour+'" oncClick=checkValidity(this.value); onkeydown="return key_check(event, this.value)" />';
                
                //------added by CHA, Feb 1, 2010-----
				//no_takes = '<input type="text" name="numtake[]" id="numtake'+id+'" size="3" maxlength="2" disabled value="" oncClick=checkValidity(this.value); onkeydown="return key_check(event, this.value)" />';
			 //'<input type="text" name="pay_type[]" id="rowPay_type'+id+'" value="'+details.pay_type+'" />'+
			//alert('tot = '+tot);
			src =
					'<tr class="wardlistrow'+alt+'" id="row'+id+'"> '+
					'<input type="hidden" name="toolTipText'+id+'" id="toolTipText'+id+'" value="'+toolTipText+'" />'+
					'<input type="hidden" name="sservice[]" id="sservice'+id+'" value="'+details.sservice+'" />'+
					'<input type="hidden" name="pcash[]" id="rowPrcCash'+id+'" value="'+details.prcCash+'" />'+
					'<input type="hidden" name="pcharge[]" id="rowPrcCharge'+id+'" value="'+details.prcCharge+'" />'+
					'<input type="hidden" name="items[]" id="rowID'+id+'" value="'+id+'" />'+
					'<input type="hidden" name="nameitems'+id+'" id="nameitems'+id+'" value="'+details.name+'" />'+
					'<input type="hidden" name="requestDoc[]" id="rowDoc'+id+'" value="'+details.requestDoc+'" />'+
					'<input type="hidden" name="requestDept[]" id="rowDept'+id+'" value="'+details.requestDept+'" />'+
					'<input type="hidden" name="requestDocName[]" id="rowDocName'+id+'" value="'+details.requestDocName+'" />'+
					'<input type="hidden" name="isInHouse[]" id="rowHouse'+id+'" value="'+details.is_in_house+'" />'+
					'<input type="hidden" name="clinicInfo[]" id="rowInfo'+id+'" value="'+details.clinicInfo+'" />'+
					'<input type="hidden" name="pnet[]" id="rowPrcNet'+id+'" value="'+details.net_price+'" />'+
					'<input type="hidden" name="pnetbc[]" id="rowPrcNetbc'+id+'" value="'+details.net_price+'" />'+
					'<input type="hidden"  name="qty[]" id="rowQty'+id+'" itemID="'+id+'" value="'+details.qty+'">'+
					'<input type="hidden"  name="inLIS'+id+'" id="inLIS'+id+'" itemID="'+id+'" value="'+details.in_lis+'">'+
					'<input type="hidden"  name="oservice_code'+id+'" id="oservice_code'+id+'" itemID="'+id+'" value="'+details.oservice_code+'">'+
                    '<input type="hidden"  name="ipdservice_code'+id+'" id="ipdservice_code'+id+'" itemID="'+id+'" value="'+details.ipdservice_code+'">'+
                    '<input type="hidden"  name="is_package'+id+'" id="is_package'+id+'" itemID="'+id+'" value="'+details.is_package+'">'+
                    '<input type="hidden"  name="is_profile'+id+'" id="is_profile'+id+'" itemID="'+id+'" value="'+details.is_profile+'">'+
                    '<input type="hidden"  name="child_test'+id+'" id="child_test'+id+'" itemID="'+id+'" value="'+details.child_test+'">'+
					'<td class="centerAlign">'+
					btnicon
					+'</td>'+
					'<td align="centerAlign">'+nonSocialized+'</td>'+
					'<td id="idGrp'+id+'"'+toolTipTextHandler+'>'+id+'</td>'+
					'<td id="name'+id+'"'+toolTipTextHandler+'>'+details.name+'</td>'+
					'<td width="5%" id="is_monitor-row'+id+'" align="center">'+monitor+'</td>'+
					'<td width="5%" id="everyhour-row'+id+'" align="center">'+everyhour+'</td>'+
					/*'<td width="5%" id="no_takes-row'+id+'" '+toolTipTextHandler+' align="center">'+no_takes+'</td>'+*/
					//'<td width="5%" id="is_forward-row'+id+'" align="center">'+forwarding+'&nbsp;'+inLIS_icon+'</td>'+
                    '<td width="5%" id="is_forward-row'+id+'" align="center">'+forwarding+'</td>'+
					'<td class="rightAlign" id="prc'+id+'">'+prc+'</td>'+
					'<td class="rightAlign" id="tot'+id+'">'+tot+'</td>'+
					'</tr>';
				trayItems++;
			}
			else {
				src = "<tr><td colspan=\"10\">Request list is currently empty...</td></tr>";
			}
			dBody.innerHTML += src;
			document.getElementById('counter').innerHTML = items.length;

			return true;
		}
	}
	return false;
}

function parseFloatEx(x) {
	var str = x.toString().replace(/\,|\s/,'')
	return parseFloat(str)
}
//borj
function transactionType()
{
/*if ($('area').value=='clinic')
{
	if ($('ptype').value==2)
		{
		
			if($('phic_nr').innerHTML!="None")
			{
				
				if($('iscash1').checked==true)
				{
					$("iscash0").checked = false;
					$("iscash1").checked = true;				
				}
					else if($('iscash1').checked==false)
				{
					$("iscash0").checked = true;
					$("iscash1").checked = false;
					$('grant_type').value = "phic";
					$('grant_type').show();
			
				return;
				}
			}
		else
			{
				//alert('Charging is only allowed for current hospital patients..')
				$("iscash0").checked = false;
				$("iscash1").checked = true;
			    $('grant_type').hide();
			}
		}
		
	}	*/
}
//end

function removeItem(id) {
	var destTable, destRows;
	var table = $('order-list');
	var rmvRow=document.getElementById("row"+id);
	var source = $('dept_area');
	var isERIP = $('isERIP').value;
	var ptype = $('ptype').value;
	var area_type = $('area_type').value;

	if (table && rmvRow) {
		$('rowID'+id).parentNode.removeChild($('rowID'+id));
		$('rowPrcCash'+id).parentNode.removeChild($('rowPrcCash'+id));
		$('rowPrcCharge'+id).parentNode.removeChild($('rowPrcCharge'+id));
		$('rowPrcNet'+id).parentNode.removeChild($('rowPrcNet'+id));
		$('rowQty'+id).parentNode.removeChild($('rowQty'+id));
		$('sservice'+id).parentNode.removeChild($('sservice'+id));
		var rndx = rmvRow.rowIndex-1;
		table.deleteRow(rmvRow.rowIndex);
		reclassRows(table,rndx);

		if (isERIP){
			if (source.value=='lab'){
				if (((ptype==3)||(ptype==4))&&(area_type=='ch')){
					xajax_checkTestERLab(id);
				}
			}else{
				enableSubmitButton(0);
			}

		}else{
			enableSubmitButton(1);
		}
	}

	var items = document.getElementsByName('items[]');
	if (items.length == 0){
		emptyIntialRequestList();
	}

	document.getElementById('counter').innerHTML = items.length;
	showSocialNotes();
	refreshDiscount();
}

function changeTransactionType() {
	var iscash = $("iscash1").checked;
	var prcList, id, total=0;
	var pid = $('pid').value;
	var ifwalk = $('ifwalk').value;
	var encounter_nr = $('encounter_nr').value;
	//clearEncounter();
    
    var mgh = $('is_maygohome').value;
    var bill_nr = $('bill_nr').value;
    var warning = $('warningcaption').innerHTML;
	
//added by daryl
	if ((pid == "") || (ifwalk == 1)){
		alert('Walk-in Patients cannot be Charge!');
		$("iscash1").checked = true;
		iscash = true;
	}


    if ((pid)&&(!encounter_nr)&&(!iscash)){
		alert('Charging is only allowed for current hospital patients...');
		$("iscash1").checked = true;
		iscash = true;
    }else if ((mgh==1) && (bill_nr!='') &&(!iscash)){
        //mgh or have save billing
        alert('Charging is NOT allowed to this patient. '+warning);
        $("iscash1").checked = true;
        iscash = true;    
    }else{
		if (iscash){
			$('sw-class').innerHTML = $F('discountid');
			prcList = document.getElementsByName("pcash[]");
			$('is_walkin').checked = false;
			$('is_walkin').disabled = false;

		}else {
			$('sw-class').innerHTML = 'None';
			prcList = document.getElementsByName("pcharge[]");
			$('is_walkin').checked = false;
			$('is_walkin').disabled = true;
		}
		//borj
		transactionType();
		//end
		if (iscash==true){
			$('is_cash').value = 1;
			$('type_charge').style.display='none';

			$('for_manual').disabled = false;

            $('btn-coverage').style.display = "none";
            $('check_all').disabled = true;
		}else{
			$('is_cash').value = 0;
		$('type_charge').style.display='';

			$('for_manual').disabled = true;
			$('for_manual').checked = false;
            
            $('btn-coverage').style.display = "";
            
            $('check_all').disabled = false;
            
			//added by VAN 06-02-2011
			setManualPayment();

		}
		//$('type_charge').style.display='';

		for (var i=0;i<prcList.length;i++) {
			if (iscash)
				id = prcList[i].id.substring(10);
			else
				id = prcList[i].id.substring(12);
			$('prc'+id).innerHTML = formatNumber(prcList[i].value,2);
			$('tot'+id).innerHTML = formatNumber(parseFloat($('rowQty'+id).value)*parseFloat(prcList[i].value),2);
		}

        if ($('encounter_nr').value && !$("iscash1").checked){ 
            updateCoverage([$('encounter_nr').value])
        }else{
            $('cov_type').update('');
            $('cov_amount').update('');
            $('coverage').setAttribute('value',-1);
            $('phic_ajax').hide();
        }

		refreshDiscount();
	}
}

function refreshDiscount() {
	//var nodes = $("discount");
	totalDiscount = 0;
	totalNet = 0;
	totalDiscountedAmount = 0;
	totalNONSocializedAmount = 0;

	var items = document.getElementsByName('items[]');
	var cash = document.getElementsByName('pcash[]');
	var charge = document.getElementsByName('pcharge[]');
	var net = document.getElementsByName('pnet[]');

	var sservice = document.getElementsByName('sservice[]');
	var prcCash, prcCharge, prcNet, id, isCash = $("iscash1").checked;
	var qty = document.getElementsByName('qty[]');
	//var person_discountid = $("discountid").value;

	for (var i=0;i<items.length;i++) {
		id = items[i].value;
		prcCash = parseFloat(cash[i].value);
		//totalCash = prcCash*parseFloat(qty[i].value);
		prcCharge = parseFloat(charge[i].value);
		//totalCharge = prcCharge*parseFloat(qty[i].value);
		prcNet = parseFloat(net[i].value);

		if (isCash)
			totalPrice = prcCash*parseFloat(qty[i].value);
		else
			totalPrice = prcCharge*parseFloat(qty[i].value);

		totalNet = prcNet*parseFloat(qty[i].value);
		totalDiscount = totalPrice - totalNet;
		totalDiscountedAmount += totalDiscount;
	}
	//alert('totalDiscountedAmount = '+totalDiscountedAmount);
	refreshTotal();
}

function refreshTotal() {
	var items = document.getElementsByName('items[]');
	var cash = document.getElementsByName('pcash[]');
	var charge = document.getElementsByName('pcharge[]');
	var qty = document.getElementsByName('qty[]');
	var isCash = $("iscash1").checked;
	var nf = new NumberFormat();
	var NetTotal = 0;

	total = 0.0;
	for (var i=0;i<items.length;i++) {
		if (isCash)
			total+=parseFloat(cash[i].value)*parseFloat(qty[i].value);
		else
			total+=parseFloat(charge[i].value)*parseFloat(qty[i].value);
	}

	var subTotal = $("show-sub-total");
	var discountTotal = $("show-discount-total");
	var netTotal = $("show-net-total");

		//var dAdjAmount = $("show-discount");
		NetTotal =  total - totalDiscountedAmount;

		subTotal.innerHTML = formatNumber(total.toFixed(2),2);
		discountTotal.innerHTML = "-"+formatNumber(totalDiscountedAmount.toFixed(2),2);
		netTotal.innerHTML = formatNumber(NetTotal.toFixed(2),2);

   if ($('coverage').value!=-1 && !$("iscash1").checked) {
        var coverage=parseFloatEx($('coverage').value)
        if($('mem_category').innerHTML == HSM){
			$('cov_amount').update('HSM');
		}else if ($('mem_category').innerHTML == NBB){
			$('cov_amount').update('NBB');
		} else{
			$('cov_amount').update(formatNumber(coverage-total,2));
		}

    }     
}

Array.prototype.max = function() {
var max = this[0];
var len = this.length;
for (var i = 1; i < len; i++) if (this[i] > max) max = this[i];
return max;
}

Array.prototype.min = function() {
var min = this[0];
var len = this.length;
for (var i = 1; i < len; i++) if (this[i] < min) min = this[i];
return min;
}

function preset(iscash){
	//var view_from = window.parent.$('view_from');

	//var source = $('source').value;
	var popup = $('popUp').value;
	var isERIP = $('isERIP').value;
	var ptype = $('ptype').value;
	//if (view_from)
		//$('view_from').value =  view_from.value;
	//alert($F('view_from'));

	$("iscash1").focus();

	if ($('discountid').value=='SC')
		$('issc').checked = true;

	if (($('view_from').value=='ssview') || ($('view_from').value=='override'))
		$('btndiscount').style.display = "";
	else
		$('btndiscount').style.display = "none";

	iscash = $("iscash1").checked;

	if (iscash==true){
		$('is_cash').value = 1;
		$('type_charge').style.display='none';
        $('check_all').disabled = true;
	}else{
		$('is_cash').value = 0;
	$('type_charge').style.display='';
        $('check_all').disabled = false;
	}
	//$('type_charge').style.display='';

	if ($F('ordername')){
		$('clear-enc').disabled = false;
		$('btnHistory').style.display = "";
        
        if (iscash==true){  
            $('btn-coverage').style.display = "none";
        }else{
            $('btn-coverage').style.display = "";
        }
		//$('btnOther').style.display = "";
	}else{
		$('clear-enc').disabled = true;
		$('btnHistory').style.display = "none";
        $('btn-coverage').style.display = "none";
		//$('btnOther').style.display = "none";
	}

	if (($F('view_from')=='ssview')||($F('view_from')=='override')){
		$('btndiscount').style.display='';

		if ($F('view_from')=='override')
			$('override_row').style.display = "";
		else
			$('override_row').style.display = "none";
	}else{
		$('override_row').style.display = "none";
		//enableSubmitButton(1);
	}


	if (($F('hasPaid')==1)||($('repeat').checked)||($F('view_from')=='ssview')||($F('view_from')=='override')||($F('viewonly')==1)){
		$('ordername').readOnly=true;
		$('orderaddress').readOnly=true;
		$('ordername').readOnly=true;

		$('select-enc').setAttribute("onclick","");
		$('select-enc').setAttribute("class","disabled");
		$('select-enc').style.cursor='default';

		$('clear-enc').disabled = true;
		$('clear-enc').style.cursor='default';

		$('btnAdd').setAttribute("onclick","");
		$('btnAdd').setAttribute("class","disabled");
		$('btnAdd').style.cursor='default';

		$('btnEmpty').setAttribute("onclick","");
		$('btnEmpty').setAttribute("class","disabled");
		$('btnEmpty').style.cursor='default';

		$('btndiscount').disabled = true;

		//$('btnCancel').setAttribute("onclick","");
		//$('btnCancel').setAttribute("class","disabled");
		//$('btnCancel').style.cursor='default';

		$('iscash0').disabled=true;
		$('iscash1').disabled=true;

		$('comments').readOnly=true;

        $('check_all').disabled = false;

		document.getElementsByName('btnRefreshDiscount').disabled = true;
		document.getElementsByName('btnRefreshTotal').disabled = true;
	}

	var refno = document.getElementById('refno').value;
	var area_type = $('area_type').value;

	if ($('mode').value=='save'){
		// er patient and payward patient
		if ((area_type=='pw')||(ptype==1)||($('is_charge2comp').checked)||($('source_req').value=='RDU')){
			$("iscash1").checked = false;
			$("iscash0").checked = true;
			$('type_charge').style.display='';
		}else if (area_type=='ch'){
			$("iscash1").checked = true;
			$("iscash0").checked = false;
		}else{
			$("iscash1").checked = true;
			$("iscash0").checked = false;
		}

		setPriority(0);
	}

	// for ER LAB restriction
	var pid = $('pid').value;
	if (isERIP){
		if (pid!=""){
			if ((ptype==1)||(ptype==3)||(ptype==4)){
					if (((ptype==3)||(ptype==4))&&($('priority1').checked)){
						enableSubmitButton(1);
					}else if (((ptype==3)||(ptype==4))&&($('priority0').checked)){
						if (area_type=='pw')
							enableSubmitButton(1);
						else
							enableSubmitButton(0);
					}else if ((ptype==1)||(area_type=='pw'))
						enableSubmitButton(1);
			}else{
					enableSubmitButton(0);
			}
		}else
			enableSubmitButton(0);
	}else{
		if (($F('view_from')=='ssview')||($F('view_from')=='override')||($F('viewonly')==1)){
			enableSubmitButton(0);
		}else{
			enableSubmitButton(1);
		}
	}

	//for Industrial Clinic
	if ($('source_req').value=='IC'){
		 if ($('is_charge2comp').checked){
				$('ic_row').style.display = '';
		 }else
				$('ic_row').style.display = 'none';
	}else{
		 $('ic_row').style.display = 'none';
	}

	CheckRepeatInfo();

	//added by VAN 06-02-2011
	setManualPayment();
    
    if (($('bill_nr').value!='')&&(iscash==false)){
        $('check_all').disabled = true;
        $('btnSubmit').style.cursor = ""; 
        $('btnSubmit').onclick = ""; 
        $('btnAdd').style.cursor = ""; 
        $('btnAdd').onclick = ""; 
    }
}

function CheckRepeatInfo(id){
		var isrepeat = '<?=$_GET["repeat"];?>';
		if (($('repeat').checked)||(isrepeat==1)){
			$('grant_type').value="";
			document.getElementById('repeatinfo').style.display = '';
		}else	{
			document.getElementById('repeat').disabled = true;
			document.getElementById('repeatinfo').style.display = 'none';
		}
	}

function emptyIntialRequestList(){
	clearOrder($('order-list'));
	appendOrder($('order-list'),null);
}

function initialRequestList(serv_code,grp_code,name,c_info,r_doc,r_doc_name,n_house,cash,charge,hasPaid,sservice,
                           head,remarks,qty,discounted_price,doc_dept,pay_type, is_forward, is_monitor, every_hour, 
                           in_lis, oservice_code, ipdservice_code, is_serial, changed_icon, is_package, is_profile, child_test) {
	var details = new Object();
	var withpaid = 0;
	var isrepeat = '<?=$_GET["repeat"];?>';

		details.requestDoc= r_doc;
		details.requestDocName= r_doc_name;
		details.is_in_house= n_house;
		details.clinicInfo= c_info;
		details.idGrp = grp_code;
		details.id = serv_code;
		details.qty = qty;
		details.name = name;

		details.prcCash = cash;
		details.prcCharge= charge;
		details.hasPaid = hasPaid;
		details.sservice = sservice;
		details.discounted_price = discounted_price;

		details.requestDept = doc_dept;
		details.pay_type = pay_type;
		details.net_price = discounted_price;
		details.pay_type = pay_type;
		//alert(details.pay_type);
		//details.parent_batch = parent_batch;
		details.head = head;
		details.remarks = remarks;

		details.is_forward = is_forward;
		details.is_monitor = is_monitor;
		details.every_hour = every_hour;

		details.in_lis = in_lis;
		details.oservice_code = oservice_code;
        details.ipdservice_code = ipdservice_code;
        
        details.is_serial = is_serial;
        details.changed_icon = changed_icon;
        details.is_package = is_package;
        details.is_profile = is_profile;
        details.child_test = child_test;
        
        if (($('repeat').checked)||(isrepeat==1)){
			details.discounted_price = 0;
			details.net_price = 0;
		}

		var list = document.getElementById('order-list');

		result = appendOrder(list,details);
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

function checkRequestForm(){
	var items = document.getElementsByName('items[]');
	var iscash = $("iscash1").checked;
	var isERIP = $('isERIP').value;
	var ptype = $('ptype').value;

	if (iscash)
		$('is_cash').value=1;
	else
		$('is_cash').value=0;


	if (items.length==0){
		alert("Please add a request first.");
		$('btnAdd').focus();
		return false;
	}else if($F('ordername') == ''){
		alert("Please indicate the patient's name's.");
		if (iscash)
			$('ordername').focus();
		else
			$('select-enc').focus();
		return false;
	}else if($F('orderdate') == ''){
		alert("Please indicate the date of request.");
		$('orderdate').focus();
		return false;
	}

	if ($('repeat').checked){
		if ($('remarks').value=='') {
			alert("Enter a remarks why the request should be repeated.");
			$('remarks').focus();
			return false;
		}else if ($('approved_by_head').value=='') {
			alert("Enter a name who approved .");
			$('approved_by_head').focus();
			return false;
		}else if ($('headID').value=='') {
			alert("Enter a user ID who approved .");
			$('headID').focus();
			return false;
		}else if ($('headpasswd').value=='') {
			alert("Enter a password who approved .");
			$('headpasswd').focus();
			return false;
		}
	}

	//added by VAN 06-02-2011
	if ($('for_manual').checked){
		var btntype = valButton("for_manual_type");

		//if (btntype == null){
		if ((!$('for_manual_type1').checked)&&(!$('for_manual_type2').checked)&&
				(!$('for_manual_type3').checked)&&(!$('for_manual_type4').checked)){
			alert("Select the grant type.");
			$('for_manual_type1').focus();
			return false;
		}else if ($('manual_control_no').value=='') {
			alert("Enter the control numberm, OR number or PHIC insurance number.");
			$('manual_control_no').focus();
			return false;
		}else if ($('manual_approved').value=='') {
			alert("Enter a name who approved.");
			$('manual_approved').focus();
			return false;
		}else if ($('manual_reason').value=='') {
			alert("Enter a reason why the request payment should be manually encoded.");
			$('manual_reason').focus();
			return false;
		}
	}
	//-----------------

		//if ER LAB and Stat
		if (isERIP){
			 if ((ptype==1)||(ptype==3)||(ptype==4)){
					// urgent or stat case
					if (($('priority1').checked)&&(!$('priority0').checked)&&($('comments').value=='')){
						 alert("Enter a remarks why the request should be a stat case or urgent.");
						 $('comments').focus();
						 return false;
					}
			 }
		}else{
				if (($('priority1').checked)&&(!$('priority0').checked)&&($('comments').value=='')){
						 alert("Enter a remarks why the request should be a stat case or urgent.");
						 $('comments').focus();
						 return false;
				}
		}

	$('inputform').submit();
	return true;
}

function warnClear() {
	var items = document.getElementsByName('items[]');
	if (items.length == 0) return true;
	else return confirm('Performing this action will clear the order tray. Do you wish to continue?');
}

function viewPatientRequest(is_cash,pid,refno){
	//alert("viewPatientRequest is_cash,pid,refno = "+is_cash+" - "+pid+" - "+refno);
	//var no_of_group = document.getElementById('no_of_group').value;
	//var ispaid = document.getElementById('ispaid').value;
	var ispaid;
	if (($("iscash0").checked)||($('hasPaid').value==1))
		ispaid = 1;
	else
		ispaid = 0;
	var withclaimstub = document.getElementById('withclaimstub').value;

	//if (no_of_group>1)
		window.open("seg-lab-request_print.php?is_cash="+is_cash+"&pid="+pid+"&refno="+refno+"&ispaid="+ispaid+"&withclaimstub="+withclaimstub+"&showBrowser=1","viewPatientRequest2","left=150, top=100, width=620,height=440,menubar=no,resizable=yes,scrollbars=yes");
}

//added by VAN 10-09-08
function viewClaimStub(is_cash,refno){
		window.open("seg-claimstub.php?refno="+refno+"&is_cash="+is_cash+"&showBrowser=1","viewClaimStab","width=620,height=440,menubar=no,resizable=yes,scrollbars=yes");

}
//------------------------

//added by VAN 10-16-09
function viewHistory(pid,encounter_nr){
	window.open("seg-lab-request-history.php?pid="+pid+"&encounter_nr="+encounter_nr+"&ref_source=LB&showBrowser=1","viewRequestHistory","width=620,height=440,menubar=no,resizable=yes,scrollbars=yes");
}

function viewPatientResult(refno, code){
	var status = document.getElementById('billstatus').value;
	window.open("seg-lab-request-result-pdf.php?refno="+refno+"&service_code="+code+"&status="+status+"&showBrowser=1","viewPatientResult","width=620,height=440,menubar=no,resizable=yes,scrollbars=yes")
}

function viewPatientResult_Summary(refno, code){
	var status = document.getElementById('billstatus').value;
	window.open("seg-lab-request-result-summary-pdf.php?refno="+refno+"&service_code="+code+"&status="+status+"&showBrowser=1","viewPatientResult_Summary","width=620,height=440,menubar=no,resizable=yes,scrollbars=yes")
}

function getBill_Status(status){
	document.getElementById('billstatus').value = status;
}

//added by VAN 01-10-10
function formonitoring(id){

	if (document.getElementById('monitor'+id).checked){
		document.getElementById('hour'+id).disabled = false;
		//document.getElementById('numtake'+id).disabled= false;
	}else{
		document.getElementById('hour'+id).disabled = true;
		document.getElementById('hour'+id).value = '';
		//document.getElementById('numtake'+id).disabled= true;
		//document.getElementById('numtake'+id).value= '';
	}
}

//added by VAN 01-11-10
//only digit is allowed
function key_check(e, value){
	if((e.keyCode>=48 && e.keyCode<=57) || (e.keyCode==8) || ((e.keyCode==110)||(e.keyCode==190)) || (e.keyCode>=96 && e.keyCode<=105)){
		return true;
	}else
		return false;
}

function checkIfWalkin(){
	if (warnClear()) {
		emptyTray();
		if ($('is_walkin').checked){
			$('discount2').value = $('discount').value;
			$('orig_discountid').value = $('discountid').value;
			$('discountid').value = '';

			if ($('issc').checked){
				$('discountid').value = 'SC';
				//should be taken from the database temporary only
				$('discount').value = 0.20;
			}else
				$('discount').value = 0;
			refreshDiscount();
		}else{
			$('discount').value = $('discount2').value;
			$('discountid').value = $('orig_discountid').value;
		}
	}else{
        $('is_walkin').checked = false;
	}
}

function resetValue(){
	var items = document.getElementsByName('items[]');
	var net = document.getElementsByName('pnet[]');
	var netbc = document.getElementsByName('pnetbc[]');

	nettotal = 0;
	if ($('show-discount').value!=""){
		$('show-discount').value = formatNumber(nettotal,2);
		for (var i=0;i<items.length;i++) {
			id = items[i].value;
			net[i].value = netbc[i].value;
			amount = $('rowPrcNetbc'+id).value;
			$('tot'+id).innerHTML = formatNumber(Math.round(amount).toFixed(2),2);
		}
		refreshDiscount();
	}
}

function clearValue(){
	$('show-discount').value= "";
	$('is_free').checked = false;

	resetValue();
}

function computeDiscount(discount_amt){
	 var items = document.getElementsByName('items[]');
	 var net = document.getElementsByName('pnet[]');
	 var netbc = document.getElementsByName('pnetbc[]');
	 var val, nettotal, price_per_service, final_net, netamt, pricelist, discount_given, amount;
	 var no_item=0;

	 if (!($('is_free').checked)&&(($('show-discount').value=="")||($('show-discount').value==0.00))){
		$('show-discount').value = formatNumber(nettotal,2);
		for (var i=0;i<items.length;i++) {
			id = items[i].value;
			net[i].value = netbc[i].value;
			amount = $('rowPrcNetbc'+id).value;
			$('tot'+id).innerHTML = formatNumber(Math.round(amount).toFixed(2),2);
		}
		refreshDiscount();
	}else{
		 discount_given = discount_amt.replace(",","");
		 discount_given = parseFloat(discount_given);
		 nettotal = $('show-net-total').innerHTML;
		 nettotal = parseFloat(nettotal);

		 if (items){
			 pricelist = new Array();
			 netamt = 0;
			 for (var i=0;i<items.length;i++) {
					pricelist[i] = parseFloat(netbc[i].value);

					if (netbc[i].value > 0){
						no_item += 1;
						netamt = netamt + parseFloat(netbc[i].value);
					}
			 }

			 netamt = parseFloat(netamt);

			 if (discount_given > netamt){
					 alert('The discount given is MORE than the Net Total (OR payable amount)');
					 for (var i=0;i<items.length;i++) {
							id = items[i].value;
							net[i].value = netbc[i].value;
							amount = $('rowPrcNetbc'+id).value;
							$('tot'+id).innerHTML = formatNumber(Math.round(amount).toFixed(2),2);
					 }
					 $('show-discount').value = '0.00';
					 $('show-discount').focus();
					 refreshDiscount();
			 }else{
					 if (discount_given > 0){
							 price_per_service =  (netamt - discount_given/parseInt(no_item));

							 if (price_per_service > pricelist.min()){
									final_net = (netamt- discount_given) / netamt;
									withdis = 1;
							 }else{
									withdis = 0;
							 }

							 for (var i=0;i<items.length;i++) {
									 id = items[i].value;

									 if (withdis==0){
											price_per_service = Math.round(parseFloat(price_per_service)*100)/100;
											price_per_service = formatNumber(price_per_service.toFixed(2),2);
											net[i].value = price_per_service.replace(",","");
											$('tot'+id).innerHTML = price_per_service
									 }else{
											discountprice = netbc[i].value * final_net;
											discountprice = Math.round(parseFloat(discountprice)*100)/100;
											discountprice = formatNumber(discountprice.toFixed(2),2);
											net[i].value = discountprice.replace(",","");
											$('tot'+id).innerHTML = discountprice;
									 }
							 }
							 refreshDiscount();
					 }
			 }
		 }

		 //refreshDiscount();
	}
}

function formatDiscount(valamount){
		document.getElementById('show-discount').value = formatNumber(valamount,2);
}

function setDiscount(){
	var nettotal=0;
	var items = document.getElementsByName('items[]');
	var net = document.getElementsByName('pnet[]');
	var netbc = document.getElementsByName('pnetbc[]');

	if ($('is_free').checked){
		for (var i=0;i<items.length;i++) {
			id = items[i].value;
			nettotal = nettotal + parseFloat(netbc[i].value);
		}

		$('show-discount').value = nettotal;
		computeDiscount($('show-discount').value);
		formatDiscount($('show-discount').value);
	}else{
		nettotal = 0;
		$('show-discount').value = formatNumber(nettotal,2);
		resetValue();
	}
}

function print_true(){
	 enableSubmitButton(1);
}

function print_false(){
	 enableSubmitButton(0);
	 setPriority(0);
	 alert('Your login or password is wrong');
}

function enableSubmitButton(isenable){
		if (isenable){
			$('btnSubmit').setAttribute("class","");
			$('btnSubmit').style.cursor='pointer';
			$('btnSubmit').setAttribute("onclick","if (confirm(\'Process this request?\')) if (checkRequestForm()) document.inputform.submit()");
		}else{
			$('btnSubmit').setAttribute("class","disabled");
			$('btnSubmit').style.cursor='default';
			$('btnSubmit').setAttribute("onclick","");
		}
}

function setPriority(isUrgent){
		if (isUrgent){
			 $('priority1').checked = true;
			 $('priority0').checked = false;
		}else{
			 $('priority1').checked = false;
			 $('priority0').checked = true;
		}
}

function checkERIP(isSTAT){
	var isERIP = $('isERIP').value;
	var ptype = $('ptype').value;
	var area_type = $('area_type').value;

	if (isERIP){
		if (isSTAT){
			 if ((ptype==3)||(ptype==4)){
					if (area_type=='ch'){
						if(confirm("Is this really a STAT case?")){
									usr=prompt("Please enter your username.","");
									if(usr&&usr!=""){
										pw=prompt("Please enter your password.","");
										if(pw&&pw!=""){
											xajax_checkAccess(usr, pw);
										}else{
											setPriority(0);
										}
									}else{
										setPriority(0);
									}
						}else{
							setPriority(0);
						}
					}else if (area_type=='pw'){
						enableSubmitButton(1);
					}
			 }else if ((ptype==1)||(area_type=='pw')){
					enableSubmitButton(1);
			 }else{
					enableSubmitButton(0);
			 }
		}else{
				if ((ptype==1)||(area_type=='pw')){
					enableSubmitButton(1);
			 }else{
					enableSubmitButton(0);
			 }
		}
	}
}

function checkCharge(chargeType){
	var isERIP = $('isERIP').value;
	var ptype = $('ptype').value;
	var area_type = $('area_type').value;

	if (isERIP){
		if (((ptype==3)||(ptype==4))&&(area_type=='ch')){
			if ((chargeType=='oplanob')||($('is_rdu').checked)){
			enableSubmitButton(1);
		}else{
			enableSubmitButton(0);
		}
		}else
				enableSubmitButton(1);
	}else{
		enableSubmitButton(1);
	}
}

function checkPriority(){
	var area_type = $('area_type').value;

	//if (area_type!='pw'){
		if (warnClear()) {
			emptyTray();
		}
	//}
}

function enableButtonClear(isenable){
	 var items = document.getElementsByName('items[]');

	 if (isenable==1){
		if (items.length > 1)
			enableSubmitButton(0);
		else
			enableSubmitButton(0);
	 }else{
			enableSubmitButton(1);
	 }
	}

function validateRDU(){
		var isERIP = $('isERIP').value;
		var ptype = $('ptype').value;
		var area_type = $('area_type').value;

		if (isERIP){
			if (((ptype==3)||(ptype==4))&&(area_type=='ch')){
				if (($('grant_type').value=='oplanob')||($('is_rdu').checked))
					enableSubmitButton(1);
				else
					enableSubmitButton(0);
			}else
					enableSubmitButton(1);
		}else
			enableSubmitButton(1);
}

//added by VAN 06-02-2011
// for temporary workaround
function enablePhic(){
	if ($('is_rdu').checked){
		$('for_manual_type4').disabled = false;
	}else{
		$('for_manual_type4').disabled = true;
		$('for_manual_type4').checked = false;
	}
}

function setManualPayment(){

	if ($('for_manual').checked){
		$('manual').style.display = '';
		$('for_manual_payment').value = 1;
	}else{
		$('manual').style.display = 'none';
		$('for_manual_payment').value = 0;
	}

   if (!(($F('hasPaid')==1)||($('repeat').checked)||($F('view_from')=='ssview')||($F('view_from')=='override')||($F('viewonly')==1))){
       if ($('iscash0').checked){
		$('for_manual').disabled = true;
       }else{
		$('for_manual').disabled = false;
       }
   }            

	enablePhic();
}

function setLabel(){
	 var or_label = "OR Number";
	 var control_label = "Control Number";
	 var phic_label = "PHIC Number";


	 if ($('for_manual_type1').checked)
			$('label_manual').innerHTML = or_label;
	 else if ($('for_manual_type2').checked)
			$('label_manual').innerHTML = control_label;
	 else if ($('for_manual_type3').checked)
			$('label_manual').innerHTML = control_label;
	 else if ($('for_manual_type4').checked)
			$('label_manual').innerHTML = phic_label;
	 else
			$('label_manual').innerHTML = control_label;
}

function valButton(btn) {
	var cnt = -1;
	var temp = document.getElementsByName(btn);
	if (!$(btn))	{
		return null;
	}

	for (var i=temp.length-1; i > -1; i--) {
		if (temp[i].checked) {
			cnt = i;
			i = -1;
		}
		}

	if (cnt > -1) return temp[cnt].value;
		else return null;
}

//--------------------------

//added by VAN 01-16-2012
function setSampleCheckInStatus(){
   var items = document.getElementsByName('items[]');
   
   for (var i=0;i<items.length;i++) {
       id = items[i].value; 
       if($('check_all').checked){
           if (!$('withsampleID'+id).disabled){
                $('withsampleID'+id).checked = true;
           }    
           //$J('input:checkbox:not(:disabled)').attr('checked','checked');
        }else{
           $('withsampleID'+id).checked = false;
        }
   } 
  
}

//added by VAS 03-21-2012
function changeChargeType() {
    if (!$("iscash1").checked) {
        updateCoverage([$('encounter_nr').value]);
    }    
    refreshDiscount();
}

function updateCoverage( param ) {
    if (!param[0]) {
        //$('cov_type').update('Coverage:');
        $('cov_amount').update('');
        $('coverage').setAttribute('value',-1);
        return false;
    }
    
    var ctype = $('grant_type').value;
    var nr = $('refno').value;
    param.push(ctype);
    param.push(nr);
    
    if (ctype=='phic') {  //phic
        $('cov_type').hide();
        $('cov_amount').hide();
        $('phic_ajax').show();
        $('cov_type').update('PHIC Coverage:');
        xajax.call('updateCoverage', {
            parameters : param,
            onError: function(transport) {
                $('phic_ajax').hide();
                $('cov_type').show();
                $('cov_amount').show();
            },
            onSuccess : function(transport) {
                $('phic_ajax').hide();
                $('cov_type').show();
                $('cov_amount').show();
            }
        });
    }
    else {  //other charge type 
        $('cov_type').update('');
        $('cov_amount').update('');
        $('coverage').setAttribute('value',-1);
        $('phic_ajax').hide();
        //$('cov_type').hide();
        //$('cov_amount').hide();
    }
}

function updatePHICCoverage( param ) {
    $('phic_cov').hide();
    $('phic_ajax').show();
    xajax.call('updatePHICCoverage', {
        parameters : param,
        onError: function(transport) {
            $('phic_ajax').hide();
            $('phic_cov').show();
        },
        onSuccess : function(transport) {
            $('phic_ajax').hide();
            $('phic_cov').show();
        }
    });
}

function openCoverages() {
        var enc_nr = $('encounter_nr').value;
        var userck = '<?= $userck; ?>';
        if (enc_nr) {
            var url = '../../modules/insurance_co/seg_coverage_editor.php?userck='+userck+'&encounter_nr='+enc_nr+'&from=CLOSE_WINDOW&force=1';
            overlib(
                OLiframeContent(url, 740, 400, 'fCoverages', 0, 'auto'),
                WIDTH,600, TEXTPADDING,0, BORDER,0,
                STICKY, SCROLL, CLOSECLICK, MODAL,
                CLOSETEXT, '<img src=../../images/close_red.gif border=0 >',
                CAPTIONPADDING,2,
                CAPTION,'Insurance coverages',
                MIDX,0, MIDY,0,
                STATUS,'Insurance coverages');
        }
        else {
            alert('No patient with confinement case selected...');
        }
        return false
    }
//-------------

function post_serialTest(service_code){
   //display serial window
   var url;
   var refno = $('refno').value;
   var encounter_nr = $('encounter_nr').value;
   var pid = $('pid').value;
   var is_serial = 1;
   
   url = "seg-lab-post-request.php?user_origin=lab&popUp=1&viewonly=1&refno="+refno+"&pid="+pid+"&encounter_nr="+encounter_nr+"&service_code="+service_code;
   
   return overlib(
                    OLiframeContent(url, 800, 400, "fOrderTray", 1, "auto"),
                    WIDTH,400, TEXTPADDING,0, BORDER,0,
                    STICKY, SCROLL, CLOSECLICK, MODAL,
                    CLOSETEXT, "<img src=../../images/close.gif border=0 >",
                    CAPTIONPADDING,4, CAPTION,"Specimen Check-in for Serial",
                    MIDX,0, MIDY,0,
                    STATUS,"Specimen Check-in for Serial");
}


//added by daryl

function autoSuggestWalkin(element) {
	if ($("iscash1").checked && !element.readOnly) {
		$('orderaddress').readOnly = false;
		if (!$F('orderaddress')) $('orderaddress').value = "NOT PROVIDED";
		var script = "ajax/walkin_lab.php";
		var options = {
			delay: 5,
			timeout: 2000,
			script: function (input) { return ( script + '?s='+input); },
			callback: function (obj) {
				//$('xml_info').update('you have selected: '+obj.id + ' ' + obj.value + ' (' + obj.info + ')');
				$('ordername').readOnly = true;
				$('orderaddress').readOnly = true;
				$('ordername').value = obj.value;
				$('orderaddress').value = obj.info;
				$('pid').value = obj.id;
				$('clear-enc').disabled = false;
			}
		};
		var xml=new AutoComplete(element.id,options);
		return true;
	}
	else {
		$('orderaddress').readOnly = true;
		return false;
	}
}

//maimai 03-03-2015
function setInsurances(){
	var pid = $('pid').value;
	if(pid){
		xajax_getInsurances(pid);
	}
}

function appendInsurance(details){
	var x = document.getElementById("grant_type");
	var option = document.createElement("option");
	option.text = details.name;
	option.value = details.id;
	x.add(option);
}