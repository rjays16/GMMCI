var bClickedHCare = true;
var NUM_COL = 13;
var tax_percent = 0.02;

function init(e) {
    shortcut.add("Ctrl+F", function(){ searchInsurance(); }, {
            'type':'keypress',
            'propagate':false});
    shortcut.add("ESC", function(){ cClick(); });
    
    xajax_setMemCategoryOptions($('categ_id').value);

    if($('ref_no').value == ''){
        xajax_resetRefNo();
    }
//    shortcut.add("Ctrl+S", function(){ js_SaveBilling(); }, {
//            'type':'keypress',
//            'propagate':false});        
}//end function init

function searchInsurance() {
    return overlib(OLiframeContent('billing-select-hcare.php', 700, 400, 'fSelHCare', 0, 'auto'),
                    WIDTH,700, TEXTPADDING,0, BORDER,0, STICKY, SCROLL, CLOSECLICK, MODAL, DRAGGABLE,
                    CLOSETEXT, '<img src=../../images/close.gif border=0 >',
                    CAPTIONPADDING, 4, CAPTION,'Select Health Insurance',
                    MIDX, 0, MIDY, 0, STATUS,'Select health insurance');    
}

//function jsCategoryOptionChange(obj, value, sdesc){
//    if (obj.id== 'category_list') {
//        $('categ_id').value   = value;    
//        $('categ_desc').value = sdesc;
//    }
//}

function assignHCareID(hcare_id) {
    $('hcare_id').value = hcare_id;
}

function resetRefNo(newRefNo, error) {
    $("ref_no").style.color = error ? "#ff0000" : "";
    $("ref_no").value=newRefNo;
}

function js_AddOptions(tagId, text, value, bselected){
    var elTarget = $(tagId);
    if(elTarget){
        var opt = new Option(text, value);
        //var opt = new Option(value, value);
        opt.id = value;
        if (bselected) opt.selected = true;
        elTarget.appendChild(opt);
    }
    var optionsList = elTarget.getElementsByTagName('OPTION');
}//end of function js_AddOption

function jsInitDetailsSection() {
    $('tbl_claim_details_hdr').style.display = '';
    jsClearSection('tbl_claim_details_body');
}

function jsClearSection(elementID) {
    $(elementID).style.display = '';
    //$(elementID).innerHTML = '';
}

function clearClaimPostDetails(list, i) {    
    if (!list) list = $('claim_details')
    if (list) {
        var dBody=list.getElementsByTagName("tbody")[i]
        if (dBody) {
            trayItems = 0;
            dBody.innerHTML = "";
            return true
        }        
    }
    return false
}

function editAmnt(id, orig_id) {
    if (orig_id) {
        if (Number($(orig_id).value) == Number(0)) {
            alert('Cannot be edited!');
            return true;
        }
    }
    
    $("edit"+id).style.display = "";
    $("row"+id).style.display = "none";
    $("edit"+id).value = Number($(id).value);
    $("edit"+id).focus();    
}

function isESCPressed(e) {
    var kC  = (window.event) ?    // MSIE or Firefox?
             event.keyCode : e.keyCode;
    var Esc = (window.event) ?   
            27 : e.DOM_VK_ESCAPE // MSIE : Firefox
    return (kC==Esc);
}

function cancelAmnt(id) {
    $("edit"+id).style.display = "none";
    $("row"+id).style.display = "";
}

function applyAmnt(e, id, id2, orig_id) {
    var characterCode;

    if (e) {
        if(e && e.which) { //if which property of event object is supported (NN4)
            characterCode = e.which; //character code is contained in NN4's which property
        }
        else {
            characterCode = e.keyCode; //character code is contained in IE's keyCode property
        }
    }
    else
        characterCode = 13;

    if ( (characterCode == 13) || (isESCPressed(e)) ) {        
        var amnt = Number($("edit"+id).value);
        
        if (orig_id) {
            var orig_amnt = Number($(orig_id).value);
            
            if (!(isNaN(parseInt(amnt))) && (parseInt(amnt)>=0) && (orig_amnt >= amnt)) {
    //            var acc_pay = $("qty_"+id+srow).value;        
    //            $("rowtotal_"+id).innerHTML = '<input id="total_'+id+'" type="hidden" value="'+formatNumber(Number(qty) * Number(uprice), 2)+'">'+formatNumber(Number(qty) * Number(uprice), 2);            
                $("row"+id).innerHTML  = '<input name="'+id2+'[]" id="'+id+'" type="hidden" value="'+amnt+'">'+formatNumber(Number(amnt),2);
            }
            else
                alert('Amount cannot be more than P'+formatNumber(Number(orig_amnt),2)+'!');
        }
        else
            $("row"+id).innerHTML  = '<input name="'+id2+'[]" id="'+id+'" type="hidden" value="'+amnt+'">'+formatNumber(Number(amnt),2); 
                                
        $("edit"+id).style.display = "none";
        $("row"+id).style.display = "";
//        $("op_selected"+id).focus();        
    }            
}

function updateTotal(id, enc_nr, ncol) {
    var total = 0;
    
    total = Number($('acc_pay_'+id).value) + Number($('med_pay_'+id).value) + Number($('srv_pay_'+id).value) + Number($('ops_pay_'+id).value) + Number($('msc_pay_'+id).value) +
            Number($('d1_pay_'+id).value) + Number($('d2_pay_'+id).value) + Number($('d3_pay_'+id).value) + Number($('d4_pay_'+id).value);
            
    $("rowtotal_pay_"+id).innerHTML  = '<input name="total_pay_'+enc_nr+'[]" id="total_pay_'+id+'" type="hidden" value="'+total+'">'+formatNumber(Number(total),2);    
    total -= Number($('tax_wheld_'+id).value);
    $("rownet_pay_"+id).innerHTML  = '<input name="net_pay_'+enc_nr+'[]" id="net_pay_'+id+'" type="hidden" value="'+total+'">'+formatNumber(Number(total),2);
    
    updateSubTotal(enc_nr, ncol);
}

function updateSubTotal(enc_nr, ncol) {
    var colnm = '', i, dRows, lastIndex;
    var subnm = '';
    var totalnm = '';

    switch (ncol) {               
        case 1: colnm = 'acc_pay_';
                subnm = 'acc_sub';
                totalnm = 'total_acc';
                break;
        case 2: colnm = 'med_pay_';
                subnm = 'med_sub';
                totalnm = 'total_med'; 
                break;
        case 3: colnm = 'srv_pay_';
                subnm = 'srv_sub';
                totalnm = 'total_srv'; 
                break;
        case 4: colnm = 'ops_pay_';
                subnm = 'ops_sub';
                totalnm = 'total_ops'; 
                break;
        case 5: colnm = 'msc_pay_';
                subnm = 'msc_sub';
                totalnm = 'total_msc'; 
                break;
        case 6: colnm = 'd1_pay_';
                subnm = 'd1_sub';
                totalnm = 'total_d1'; 
                break;
        case 7: colnm = 'd2_pay_';
                subnm = 'd2_sub';
                totalnm = 'total_d2'; 
                break;        
        case 8: colnm = 'd3_pay_';
                subnm = 'd3_sub';
                totalnm = 'total_d3'; 
                break;               
        case 9: colnm = 'd4_pay_';
                subnm = 'd4_sub';
                totalnm = 'total_d4'; 
                break;
        case 10: colnm = 'tax_wheld_';
                subnm = 'twheld_sub';
                totalnm = 'total_twheld'; 
                break;             
    }                                    
    
       
    var total = Number(0.00);    
    if (!document.getElementsByName(colnm+enc_nr+"[]") || document.getElementsByName(colnm+enc_nr+"[]").length > 0) {
        dRows = document.getElementsByName(colnm+enc_nr+"[]");
        lastIndex = document.getElementsByName(colnm+enc_nr+"[]").length;        
        for (i=0; i<lastIndex; i++) {
            total += Number(dRows[i].value.replace(",",""));
        }        
    }       
    
    $(subnm+'_'+enc_nr).value = total;
    
    total = Number(0.00); 
    dRows = document.getElementsByName(subnm+"[]");
    lastIndex = document.getElementsByName(subnm+"[]").length;
    for (i=0; i<lastIndex; i++) {
        total += Number(dRows[i].value);
    }
    
    $(totalnm).innerHTML = formatNumber(Number(total),2);
    
    updateGrandTotal();  
}

function updateGrandTotal() {
    var grtotal = [], s, tmp;
    
    s = $('total_acc').innerHTML;
    tmp = s.replace(',','');
    grtotal[0] = Number(tmp);
    
    s = $('total_med').innerHTML;
    tmp = s.replace(',','');
    grtotal[1] = Number(tmp);
    
    s = $('total_srv').innerHTML;
    tmp = s.replace(',','');
    grtotal[2] = Number(tmp);
    
    s = $('total_ops').innerHTML;
    tmp = s.replace(',','');
    grtotal[3] = Number(tmp);

    s = $('total_msc').innerHTML;
    tmp = s.replace(',','');
    grtotal[4] = Number(tmp);       
    
    s = $('total_d1').innerHTML;
    tmp = s.replace(',','');
    grtotal[5] = Number(tmp);
    
    s = $('total_d2').innerHTML;
    tmp = s.replace(',','');
    grtotal[6] = Number(tmp);
    
    s = $('total_d3').innerHTML;
    tmp = s.replace(',','');
    grtotal[7] = Number(tmp);
    
    s = $('total_d4').innerHTML;
    tmp = s.replace(',','');
    grtotal[8] = Number(tmp);    
    
    s = $('total_twheld').innerHTML;
    tmp = s.replace(',','');
    grtotal[9] = Number(tmp);         
    
    total = Number(0.00); 
    for (var i=0; i<grtotal.length-1; i++) {
        total += Number(grtotal[i]);
    }
    
    $('total_gross').innerHTML = formatNumber(Number(total),2);
    $('total_twheld').innerHTML = formatNumber(Number(grtotal[grtotal.length-1]), 2);
    $('total_net').innerHTML = formatNumber(Number(total) - Number(grtotal[grtotal.length-1]), 2);            
}

function js_addclaimdet(details) {
    var srcRow = '';    
    var root_path = $('root_path').value;            
    var list = $('claim_details');
    var dRows, cases;
    
    if (list) {
        var dBody=list.getElementsByTagName("tbody")[0];
        if (dBody) {         
            dRows = dBody.getElementsByTagName("tr");
            
            cases = document.getElementsByName('cases[]');
            if (cases.length == 0) {
                clearClaimPostDetails(list, 0);
            }                           
            alt = (dRows.length%2)+1;
                
            if (details) {            
                var status  = (details.status) ? details.status : 'NONE';  
                
                var statcolor;
                switch (Number(details.statcode)) {
                    case 1: statcolor = "#00CA00"; break;
                    case 2: statcolor = "#FFFF00"; break;
                    case 3: statcolor = "#FF0000"; break;
                    default:
                        statcolor = "#000000";                
                }                
            
                var denied = 0;
                var returned = 0;

                if(status.toUpperCase() == 'DENIED'){
                    denied = 1;
                }else if(status.toUpperCase() == 'RETURNED'){
                    returned = 1;
                }

                srcRow = '<tr class="wardlistrow'+alt+'" id="row_'+details.enc_nr+'">'+
                         '<input type="hidden" name="cases[]" id="case_'+details.enc_nr+'" value="'+details.enc_nr+'" />'+    
                         '<input type="hidden" name="acc_sub[]" id="acc_sub_'+details.enc_nr+'" value="0" />'+ 
                         '<input type="hidden" name="med_sub[]" id="med_sub_'+details.enc_nr+'" value="0" />'+ 
                         '<input type="hidden" name="srv_sub[]" id="srv_sub_'+details.enc_nr+'" value="0" />'+ 
                         '<input type="hidden" name="ops_sub[]" id="ops_sub_'+details.enc_nr+'" value="0" />'+
                         '<input type="hidden" name="msc_sub[]" id="msc_sub_'+details.enc_nr+'" value="0" />'+  
                         '<input type="hidden" name="d1_sub[]" id="d1_sub_'+details.enc_nr+'" value="0" />'+ 
                         '<input type="hidden" name="d2_sub[]" id="d2_sub_'+details.enc_nr+'" value="0" />'+ 
                         '<input type="hidden" name="d3_sub[]" id="d3_sub_'+details.enc_nr+'" value="0" />'+ 
                         '<input type="hidden" name="d4_sub[]" id="d4_sub_'+details.enc_nr+'" value="0" />'+                                                  
                         '<input type="hidden" name="twheld_sub[]" id="twheld_sub_'+details.enc_nr+'" value="0" />'+
                         '<input type="hidden" name="is_denied[]" id="is_denied_'+details.enc_nr+'" value="'+denied+'" />'+
                         '<input type="hidden" name="is_ret[]" id="is_ret_'+details.enc_nr+'" value="'+returned+'" />'+     
                         '<td width="2%" align="right" nowrap="nowrap">'+
                                 '<a title="Remove" href="#">'+
                                    '<img class="segSimulatedLink" src="'+root_path+'images/cashier_delete.gif" border="0" align="absmiddle" '+
                                        'onclick="if (confirm(\'Remove this claim?\')) removeClaim(\''+details.enc_nr+'\')"/>'+
                                '</a>'+
                         '</td>'+         
                         '<td width="15.5%" align="left">'+details.patient+'</td>'+
                         '<td width="10%" align="center"><span id="status_'+details.enc_nr+'" style="color:'+statcolor+'">'+status+'</span></td>'+
                         '<td colspan="2" width="25%" align="center">'+details.prd+'</td>'+
                         '<td colspan="2" width="12%" align="center">'+details.insurance_nr+'</td>'+  
                         '<td colspan="2" width="15%">'+details.member+'</td>'+ 
                         '<td colspan="2" width="12%">'+details.categ_desc+'</td>'+
                         '<td colspan="2" align="center" width="6%" valign="middle"><a title="Deny claim!" href="javascript:void(0);"><img class="segSimulatedLink" id="btndeny" style="cursor:pointer" src="'+root_path+'/images/claim_deny.gif" border=0 '+
                            'onclick="if (confirm(\'Is this claim denied by the insurance firm?\')) markDenied(\''+details.enc_nr+'\')"/></a>&nbsp;'+
                            '<a title="Return claim!" href="javascript:void(0);"><img class="segSimulatedLink" id="btnreturn" style="cursor:pointer" src="'+root_path+'/images/claim_ret.gif" border=0 '+
                               'onclick="if (confirm(\'Is this claim returned by the insurance firm?\')) markReturned(\''+details.enc_nr+'\')"/></a>&nbsp;'+
                            '<a title="Post claim!" href="javascript:void(0);"><img class="segSimulatedLink" id="btnpost" style="cursor:pointer" src="'+root_path+'/images/claim_ok.png" border=0 '+
                               'onclick="if (confirm(\'Post claim payments?\')) postPayment(\''+details.enc_nr+'\')"/></a>'+                            
                            '</td>'+ 
                            '<td colspan="2" width="12%"></td>'+                        
                         '</tr><tr id="row_'+details.enc_nr+'_claim" style="display:none"><td colspan="15">'+
                         '<table id="post_claim_details_'+details.enc_nr+'" class="segList" border="0" cellpadding="0" cellspacing="0" width="100%">'+
                         '   <thead>'+
                         '      <tr style="border:1px solid #000000">'+
                         '           <th colspan="2" width="17.5%"><span style="color:#64FFB1">Pay To</span></th>'+
                         '           <th width="7.5%"><span style="color:#64FFB1">RM&BD</span></th>'+
                         '           <th width="7.5%"><span style="color:#64FFB1">DR&MD</span></th>'+
                         '           <th width="7.5%"><span style="color:#64FFB1">XRAY/<br>LAB</span></th>'+
                         '           <th width="7.5%"><span style="color:#64FFB1">OR<br>FEE</span></th>'+
                         '           <th width="7.5%"><span style="color:#64FFB1">MISC.</span></th>'+
                         '           <th width="7.5%"><span style="color:#64FFB1">GEN.<br>PRAC</span></th>'+
                         '           <th width="7.5%"><span style="color:#64FFB1">SPEC</span></th>'+
                         '           <th width="7.5%"><span style="color:#64FFB1">SRGN</span></th>'+
                         '           <th width="7.5%"><span style="color:#64FFB1">ANEST</span></th>'+
                         '           <th width="7.5%"><span style="color:#64FFB1">CLAIM</span></th>'+
                         '           <th width="7.5%"><span style="color:#64FFB1">GROSS</span></th>'+
                         '           <th width="5%"><span style="color:#64FFB1">W/TAX</span></th>'+
                         '           <th width="10%"><span style="color:#64FFB1">NET</span></th>'+
                         '      </tr>'+
                         '   </thead>'+
                         '   <tbody id="tbl_claim_payment_'+details.enc_nr+'">'+
                         '   </tbody>'+
                         '</table>'+
                         '</td></tr>';                        
            }
            else
                srcRow = "<tr><td colspan=\""+NUM_COL+"\"> Claim list is currently empty ...</td></tr>"; 
    
            $('tbl_claim_details_body').innerHTML += srcRow;              
            return true;
        }        
    }
    return false;
}

function postPayment(enc_nr) {    
    var hcare_id = $('hcare_id').value;      
    
    $('is_ret_'+enc_nr).value = 0;  
    $('is_denied_'+enc_nr).value = 0;
    xajax_showClaimsPayment(enc_nr, hcare_id);
    $('row_'+enc_nr+'_claim').style.display = '';
    
    $('status_'+enc_nr).style.color = "#00CA00";
    $('status_'+enc_nr).innerHTML = "PAID";

    displaySaveBtn();             
}


function displaySaveBtn(){
    $('btnSave').style.display = '';
}

function markDenied(enc_nr) {
    $('is_denied_'+enc_nr).value = 1;
    $('is_ret_'+enc_nr).value = 0;
    
    removeClaimPay(enc_nr);
    
    $('status_'+enc_nr).style.color = "#FF0000";
    $('status_'+enc_nr).innerHTML = "DENIED";     

    displaySaveBtn();
}
                                               
function markReturned(enc_nr) {
    $('is_ret_'+enc_nr).value = 1;  
    $('is_denied_'+enc_nr).value = 0;
    
    removeClaimPay(enc_nr);
    
    $('status_'+enc_nr).style.color = "#FFFF00";
    $('status_'+enc_nr).innerHTML = "RETURNED";   

    displaySaveBtn();  
}

function js_addclaimpayment(enc_nr, paydetails) {
    var srcRow = '';        
    var list = $('post_claim_details_'+enc_nr);        
    
    if (list) {
        var dBody=list.getElementsByTagName("tbody")[0];
        if (dBody) {                
            var dRows = dBody.getElementsByTagName("tr");                                       
            alt = (dRows.length%2)+1;    
            var cnt = paydetails.cnt;
            no_edit = ';color:#64FFB1';   
            if (paydetails) {

                srcRow = '<tr class="wardlistrow'+alt+'" id="row2_'+paydetails.priority+paydetails.enc_nr+'">'+
                         '<input type="hidden" name="cases2_'+paydetails.enc_nr+'[]" id="case2_'+paydetails.priority+paydetails.enc_nr+'" value="'+paydetails.enc_nr+'" />'+ 
                         '<input type="hidden" name="priority_'+paydetails.enc_nr+'[]" id="priority_'+paydetails.priority+paydetails.enc_nr+'" value="'+paydetails.priority+'" />'+
                         '<input type="hidden" name="pid_'+paydetails.enc_nr+'[]" id="pid_'+paydetails.priority+paydetails.enc_nr+'" value="'+paydetails.pid+'" />'+ 
                         '<input type="hidden" name="pname_'+paydetails.enc_nr+'[]" id="pname_'+paydetails.priority+paydetails.enc_nr+'" value="'+paydetails.full_name+'" />'+ 
                         '<input type="hidden" name="acc_'+paydetails.enc_nr+'[]" id="acc_'+paydetails.priority+paydetails.enc_nr+'" value="'+paydetails.acc_claim+'" />'+ 
                         '<input type="hidden" name="med_'+paydetails.enc_nr+'[]" id="med_'+paydetails.priority+paydetails.enc_nr+'" value="'+paydetails.med_claim+'" />'+
                         '<input type="hidden" name="sup_'+paydetails.enc_nr+'[]" id="sup_'+paydetails.priority+paydetails.enc_nr+'" value="'+paydetails.sup_claim+'" />'+
                         '<input type="hidden" name="srv_'+paydetails.enc_nr+'[]" id="srv_'+paydetails.priority+paydetails.enc_nr+'" value="'+paydetails.srv_claim+'" />'+ 
                         '<input type="hidden" name="ops_'+paydetails.enc_nr+'[]" id="ops_'+paydetails.priority+paydetails.enc_nr+'" value="'+paydetails.ops_claim+'" />'+
                         '<input type="hidden" name="msc_'+paydetails.enc_nr+'[]" id="msc_'+paydetails.priority+paydetails.enc_nr+'" value="'+paydetails.msc_claim+'" />'+
                         '<input type="hidden" name="d1_'+paydetails.enc_nr+'[]" id="d1_'+paydetails.priority+paydetails.enc_nr+'" value="'+paydetails.d1_claim+'" />'+ 
                         '<input type="hidden" name="d2_'+paydetails.enc_nr+'[]" id="d2_'+paydetails.priority+paydetails.enc_nr+'" value="'+paydetails.d2_claim+'" />'+  
                         '<input type="hidden" name="d3_'+paydetails.enc_nr+'[]" id="d3_'+paydetails.priority+paydetails.enc_nr+'" value="'+paydetails.d3_claim+'" />'+
                         '<input type="hidden" name="d4_'+paydetails.enc_nr+'[]" id="d4_'+paydetails.priority+paydetails.enc_nr+'" value="'+paydetails.d4_claim+'" />'+
                         '<td width="2%" align="left">&nbsp;</td>'+
                         '<td width="15.5%" align="left">'+paydetails.full_name+'</td>'+

                        '<td width="7.5%" align="right">'+
                                '<input class="segInput calc-gross" col="'+cnt+'" enc="'+paydetails.enc_nr+'" onFocus="this.select(); bClickedHCare = false;" onChange="calculateDetails('+paydetails.enc_nr+','+cnt+');" size="10" claim="'+paydetails.acc_claim+'" " style="'+((paydetails.acc_claim == 0) ? 'display:none' : '')+'" name="acc_pay_'+paydetails.enc_nr+'[]" id="acc_pay_'+paydetails.enc_nr+cnt+'" type="" value="'+(( paydetails.acc_pay == 0 ) ? formatNumber(paydetails.acc_claim,2) : formatNumber(paydetails.acc_pay,2) )+'">'+
                        '</td>'+

                        '<td width="7.5%" align="right">'+
                                 '<input  class="segInput calc-gross" col="'+cnt+'" enc="'+paydetails.enc_nr+'" size="10" onFocus="this.select(); bClickedHCare = false;" onChange="calculateDetails('+paydetails.enc_nr+','+cnt+');" claim="'+paydetails.med_claim+'" style="'+((paydetails.med_claim == 0) ? 'display:none' : '')+'" name="med_pay_'+paydetails.enc_nr+'[]" id="med_pay_'+paydetails.enc_nr+cnt+'" type="" value="'+(( paydetails.med_pay == 0 ) ? formatNumber(paydetails.med_claim,2) : formatNumber(paydetails.med_pay,2) )+'">'+
                        '</td>'+

                        '<td width="7.5%" align="right">'+
                                 '<input  class="segInput calc-gross" col="'+cnt+'" enc="'+paydetails.enc_nr+'" size="10" onFocus="this.select(); bClickedHCare = false;" onChange="calculateDetails('+paydetails.enc_nr+','+cnt+');" claim="'+paydetails.srv_claim+'" style="'+((paydetails.srv_claim == 0) ? 'display:none' : '')+'" name="srv_pay_'+paydetails.enc_nr+'[]" id="srv_pay_'+paydetails.enc_nr+cnt+'" type="" value="'+(( paydetails.srv_pay == 0 ) ? formatNumber(paydetails.srv_claim,2) : formatNumber(paydetails.srv_pay,2) )+'">'+
                        '</td>'+
                        
                        '<td width="7.5%" align="right">'+
                                 '<input  class="segInput calc-gross" col="'+cnt+'" enc="'+paydetails.enc_nr+'" size="10" onFocus="this.select(); bClickedHCare = false;" onChange="calculateDetails('+paydetails.enc_nr+','+cnt+');" claim="'+paydetails.ops_claim+'" style="'+((paydetails.ops_claim == 0) ? 'display:none' : '')+'" name="ops_pay_'+paydetails.enc_nr+'[]" id="ops_pay_'+paydetails.enc_nr+cnt+'" type="" value="'+(( paydetails.ops_pay == 0 ) ? formatNumber(paydetails.ops_claim,2) : formatNumber(paydetails.ops_pay,2) )+'">'+
                        '</td>'+
                        
                        '<td width="7.5%" align="right">'+
                                 '<input  class="segInput calc-gross" col="'+cnt+'" enc="'+paydetails.enc_nr+'" size="10" onFocus="this.select(); bClickedHCare = false;" onChange="calculateDetails('+paydetails.enc_nr+','+cnt+');" claim="'+paydetails.msc_claim+'" style="'+((paydetails.msc_claim == 0) ? 'display:none' : '')+'" name="msc_pay_'+paydetails.enc_nr+'[]" id="msc_pay_'+paydetails.enc_nr+cnt+'" type="" value="'+(( paydetails.msc_pay == 0 ) ? formatNumber(paydetails.msc_claim,2) : formatNumber(paydetails.msc_pay,2) )+'">'+
                        '</td>'+
                        
                        '<td width="7.5%" align="right">'+
                                 '<input  class="segInput calc-gross" col="'+cnt+'" enc="'+paydetails.enc_nr+'" size="10" onFocus="this.select(); bClickedHCare = false;" onChange="calculateDetails('+paydetails.enc_nr+','+cnt+');" claim="'+paydetails.d1_claim+'" style="'+((paydetails.d1_claim == 0) ? 'display:none' : '')+'" name="d1_pay_'+paydetails.enc_nr+'[]" id="d1_pay_'+paydetails.enc_nr+cnt+'" type="" value="'+(( paydetails.d1_pay == 0 ) ? formatNumber(paydetails.d1_claim,2) : formatNumber(paydetails.d1_pay,2) )+'">'+
                        '</td>'+
                        
                        '<td width="7.5%" align="right">'+
                                 '<input  class="segInput calc-gross" col="'+cnt+'" enc="'+paydetails.enc_nr+'" size="10" onFocus="this.select(); bClickedHCare = false;" onChange="calculateDetails('+paydetails.enc_nr+','+cnt+');" claim="'+paydetails.d2_claim+'" style="'+((paydetails.d2_claim == 0) ? 'display:none' : '')+'" name="d2_pay_'+paydetails.enc_nr+'[]" id="d2_pay_'+paydetails.enc_nr+cnt+'" type="" value="'+(( paydetails.d2_pay == 0 ) ? formatNumber(paydetails.d2_claim,2) : formatNumber(paydetails.d2_pay,2) )+'">'+
                        '</td>'+
                        
                        '<td width="7.5%" align="right">'+
                                 '<input  class="segInput calc-gross" col="'+cnt+'" enc="'+paydetails.enc_nr+'" size="10" onFocus="this.select(); bClickedHCare = false;" onChange="calculateDetails('+paydetails.enc_nr+','+cnt+');" claim="'+paydetails.d3_claim+'" style="'+((paydetails.d3_claim == 0) ? 'display:none' : '')+'" name="d3_pay_'+paydetails.enc_nr+'[]" id="d3_pay_'+paydetails.enc_nr+cnt+'" type="" value="'+(( paydetails.d3_pay == 0 ) ? formatNumber(paydetails.d3_claim,2) : formatNumber(paydetails.d3_pay,2) )+'">'+
                        '</td>'+
                        
                        '<td width="7.5%" align="right">'+
                                 '<input class="segInput calc-gross"  col="'+cnt+'" enc="'+paydetails.enc_nr+'" size="10" onFocus="this.select(); bClickedHCare = false;" onChange="calculateDetails('+paydetails.enc_nr+','+cnt+');" claim="'+paydetails.d4_claim+'" style="'+((paydetails.d4_claim == 0) ? 'display:none' : '')+'" name="d4_pay_'+paydetails.enc_nr+'[]" id="d4_pay_'+paydetails.enc_nr+cnt+'" type="" value="'+(( paydetails.d4_pay == 0 ) ? formatNumber(paydetails.d4_claim,2) : formatNumber(paydetails.d4_pay,2) )+'">'+
                        '</td>'+

                        '<td width="7.5%" align="right">'+
                                 '<input col="'+cnt+'" enc="'+paydetails.enc_nr+'" size="10" disabled type="text" style="color:#647DB8;" id="total_claim_'+paydetails.enc_nr+cnt+'" value="'+formatNumber(paydetails.total_claim,2)+'">'+
                        '</td>'+

                        '<td width="7.5%" align="right">'+
                                 '<input  class="segInput calc-net calc-grand-gross" col="'+cnt+'" enc="'+paydetails.enc_nr+'" size="10" onChange="proRate('+paydetails.enc_nr+','+cnt+','+paydetails.total_pay+');" onFocus="this.select(); bClickedHCare = false;" name="gross_pay_'+paydetails.enc_nr+'[]" id="gross_pay_'+paydetails.enc_nr+cnt+'" type="" value="'+formatNumber(paydetails.total_pay,2)+'">'+
                        '</td>'+
                        
                        '<td width="7.5%" align="right">'+
                            //'<input class="segInput disabled calc-net calc-grand-tax" type="" col="'+cnt+'" size="10">'+
                            '<input class="segInput calc-net calc-grand-tax"  onChange="calculateDetails('+paydetails.enc_nr+','+cnt+');" col="'+cnt+'" enc="'+paydetails.enc_nr+'" size="10"  name="tax_wheld_'+paydetails.enc_nr+'[]" id="tax_wheld_'+paydetails.enc_nr+cnt+'"  type="" value="'+formatNumber(paydetails.tax_wheld,2)+'">'+
                        '</td>'+
                        
                        '<td width="7.5%" align="right">'+
                                 //'<input class="segInput disabled calc-grand-net" type="" col="'+cnt+'" size="10" disabled>'+
                                 '<input  class="segInput calc-grand-net" readOnly onFocus="this.select(); bClickedHCare = false;" col="'+cnt+'" enc="'+paydetails.enc_nr+'" size="10" name="net_pay_'+paydetails.enc_nr+'[]" id="net_pay_'+paydetails.enc_nr+cnt+'" value="'+formatNumber(paydetails.net_pay,2)+'">'+
                        '</td>'+                                                        
                         '</tr>';                                                                         
            }
            else
                srcRow = "<tr><td colspan=\""+NUM_COL+"\">No claims for payment ...</td></tr>";
                
            $('tbl_claim_payment_'+enc_nr).innerHTML += srcRow;  
            
            for (var i=1;i<=10;i++) {
                updateSubTotal(enc_nr,i);
            }                                        
              
            return true;
        }
        else
            alert('No body for table post_claim_details_'+enc_nr);
    } 
    
    return false;
}

/*
*Pro-rate gross payment based on PHIC allocation in billing
*
*/
function proRate(enc, row, gross){
    var gross_pay = parseFloat($j("#gross_pay_"+enc+row).val().replace(',',''));
    var total_claim = parseFloat($j("#total_claim_"+enc+row).val().replace(',',''));

    if(!isNaN(gross_pay)){
        var field = $j('.calc-gross[col="'+row+'"]').serializeArray();
        
        $j.each(field, function(){
            var a = parseFloat($j("#"+this.name.slice(0,-2)+row).attr('claim').replace(',',''));

            var percentage = ((a/total_claim)*100).toFixed(1); //get percentage from total claim
            var b = (gross_pay * (percentage/100)).toFixed(1); //prorate area

            $j("#"+this.name.slice(0,-2)+row).val(formatNumber(b,2)); //set value
        });     

    }else{
        alert("Please input a proper amount.");
        $j("#gross_pay_"+enc+row).val(formatNumber(gross,2)); //set default value
        proRate(enc, row, gross); //recalculate proration
    }

    calculateDetails(enc, row);
   
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

function removeClaim(id) {
    var table = $('claim_details');
    var rmvRow=document.getElementById("row_"+id);
    if (table && rmvRow) {        
        var rndx = rmvRow.rowIndex-1;
        if (document.getElementsByName('cases2_'+id+'[]') && document.getElementsByName('cases2_'+id+'[]').length > 0) {
            table.deleteRow(rmvRow.rowIndex + 1);
            for (var i=1;i<=9;i++) {
                updateSubTotal(id,i);
            }    
        }
        table.deleteRow(rmvRow.rowIndex);
        
        if (!document.getElementsByName("cases[]") || document.getElementsByName("cases[]").length <= 0)
            js_addclaimdet(null);   
                    
        reclassRows(table,rndx);
    }
//    else
//        alert(table+' and '+rmvRow);    
}

function removeClaimPay(id) {
    var table = $('claim_details');
    var rmvRow=document.getElementById("row_"+id);
    if (table && rmvRow) {        
        var rndx = rmvRow.rowIndex;
        if (document.getElementsByName('cases2_'+id+'[]') && document.getElementsByName('cases2_'+id+'[]').length > 0) {
//            table.deleteRow(rmvRow.rowIndex + 1);
            $('tbl_claim_payment_'+id).innerHTML = '';
            for (var i=1;i<=9;i++) {
                updateSubTotal(id,i);
            }              
            $('row_'+id+'_claim').style.display = 'none';   
        }
    }
}

function validate() {    
    if (!$('ref_no').value) {
        alert("Please enter the reference no.");
        $('ref_no').focus();
        return false;
    }
    
    if (document.getElementsByName('cases[]').length==0) {
        alert("Warning: The claims list is empty...");
        return false;
    }
    return confirm('Process this claim?');
}

function gotoBreakFile(breakfile) {
    window.location = breakfile; 
}

function getEncounterNosWithClaims() {
    xajax_assignToClaimsSessionVar($('cases').innerHTML);
}


function calculateDetails(enc,row)
{
    var amount_field = $j('.calc-gross[col="'+row+'"]').serializeArray();
    var total = 0, col, id, new_enc;
    var tax_amount = $j("#tax_wheld_"+enc+row).val().replace(",","");
    
    $j.each(amount_field, function(i, field){
        new_enc = $j("#"+field.name.slice(0,-2)+row).attr("enc");
        if(enc == new_enc)
            total += parseFloat(field.value.replace(",",""));
    });

    //var tax_amount = parseFloat(total) * parseFloat(tax_percent);
    $j("#gross_pay_"+enc+row).val(formatNumber(total,2));
    $j("#tax_wheld_"+enc+row).val(formatNumber(tax_amount,2));
    $j("#net_pay_"+enc+row).val(formatNumber(parseFloat(total) - parseFloat(tax_amount),2))

    calculateGrandTotal();

}


function calculateGrandTotal()
{
    var gross_field = $j('.calc-grand-gross').serializeArray();
    var net_field = $j('.calc-grand-net').serializeArray();
    var tax_field = $j('.calc-grand-tax').serializeArray();
    var total_gross = 0;
    var total_tax = 0;
    var total_net = 0;
    
    $j.each(gross_field, function(i, field){
        total_gross += parseFloat(field.value.replace(",",""));
    });
    
    $j.each(net_field, function(i, field){
        total_net += parseFloat(field.value.replace(",",""));
    });

    $j.each(tax_field, function(i, field){
        total_tax += parseFloat(field.value.replace(",",""));
    });

    $j("#total_gross").html(formatNumber(total_gross,2));
    $j("#total_twheld").html(formatNumber(total_tax,2));
    $j("#total_net").html(formatNumber(total_net,2));
}
function printStatus(){
    var status = $j("#status_list").val();
    var refno = $j("#ref_no").val();
    var type = $j("#category_list").val(); 
    var transmittal_no = $j("#transmittal_no").val();

    var url = "../../modules/billing/posted-claims-pdf.php";
  
    window.open(url+"?refno="+refno+"&status="+status+"&class="+type+"&nr="+transmittal_no,
                        "width=700, height=700, top=100, left=500");
}