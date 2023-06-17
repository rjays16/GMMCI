var ViewMode = false;
var nAvg_Cost = 0;
var bGettingCost = false;

function display(str) {
    document.write(str);
}

var totalDiscount = 0;

function parseFloatEx(x) {
    var str = x.toString().replace(/\,|\s/,'')          
    return parseFloat(str)
} 

function warnClear() {
    var items = document.getElementsByName('items[]');
    if (items.length == 0) return true;
    else return confirm('Performing this action will clear the delivery details. Do you wish to continue?');
}

function formatNumber(num,dec) {
    var nf = new NumberFormat(num);
    if (isNaN(dec)) dec = nf.NO_ROUNDING;
    nf.setPlaces(dec);
    return nf.toFormatted();
}

function resetRefNo(newRefNo,error) {
    $("refno").style.color = error ? "#ff0000" : "";
    $("refno").value=newRefNo;
}

function pSearchClose() {
    var nr = $('encounter_nr').value;
    cClick();
}
    
function emptyTray() {
    warnClear();
    clearDelivery($('delivery-list'));
    addItemInDelivery($('delivery-list'),null);
     $('add-item').style.display = '';
     $('supplier').value = '';
    //refreshDiscount();
}

function reclassRows(list,startIndex) {
    if (list) {
        var dBody=list.getElementsByTagName("tbody")[0];
        if (dBody) {
            var dRows = dBody.getElementsByTagName("tr");
            if (dRows) {
                for (i=startIndex;i<dRows.length;i++) {
                    dRows[i].className = "wardlistrow"+(i%2+1);
                    updateSubtotal();
                }
            }
        }
    }
}

function clearDelivery(list) {    
    if (!list) list = $('delivery-list')
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

function jsRqstngAreaOptionChng(obj, value){
    if(obj.id == 'ori_area') {        
        //$('opw_nr').value  = value;   
        js_ClearOptions('des_area');
        xajax_getRequestedAreas(value);
    }
}

//added by maimai 12-12-2014
function updateSubtotal(){
   var total = document.getElementsByName('total_amount[]');
   var sum = 0;

   for(var i=0; i<total.length; i++){
       sum += parseFloatEx(total[i].value);
   }
   $('count').innerHTML = formatNumber(total.length);
   $('show_subtotal').innerHTML = formatNumber(sum, 2);
   
}
//end maimai

function showRequestedAreas(options) {
    $('requested_area').innerHTML = options;
}

function getItemAvgCost(id) {
    bGettingCost = true;
    xajax_getItemAvgCost(id);
}

function assignAvgCost(ncost) {
    nAvg_Cost = ncost;  
    bGettingCost = false;  
}

function editUPrice(id, srow) {
    $("edituprice_"+id+srow).style.display = "";
    $("rowuprice_"+id+srow).style.display = "none";
    $("edituprice_"+id+srow).focus();    
}

function isESCPressed(e) {
    var kC  = (window.event) ?    // MSIE or Firefox?
             event.keyCode : e.keyCode;
    var Esc = (window.event) ?   
            27 : e.DOM_VK_ESCAPE // MSIE : Firefox
    return (kC==Esc);
}

function cancelUPrice(id, srow) {
    $("edituprice_"+id+srow).style.display = "none";
    $("rowuprice_"+id+srow).style.display = "";
}

function applyUPrice(e, id, srow) {
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
        var uprice = $("edituprice_"+id+srow).value;
        if ( !(isNaN(parseInt(uprice))) && (parseInt(uprice)>=0) ) {
            var qty = $("qty_"+id+srow).value;
            
            var qty_per_pck = $("qty_per_pck_"+id+srow).value;

            $("rowtotal_"+id+srow).innerHTML = formatNumber(Number(qty) * Number(uprice) * Number(qty_per_pck), 2);
            $("rowuprice_"+id+srow).innerHTML  = '<input name="uprices[]" id="uprice_'+id+srow+'" type="hidden" value="'+uprice+'">'+formatNumber(Number(uprice),2);
            $("total_"+id+srow).value =  $("rowtotal_"+id+srow).innerHTML.replace(',','');
        }    
        
        $("edituprice_"+id+srow).style.display = "none";
        $("rowuprice_"+id+srow).style.display = "";
//        $("op_selected"+id).focus();       
        updateSubtotal(); 
    }            
}

//function addItemToRequest(list, details) {
function addItemInDelivery(list, details) {
    var srow;
   
    if (!list) list = $('delivery-list');
    if (list) {

        var dBody=list.getElementsByTagName("tbody")[0];
        if (dBody) {
            var src;            
            var dRows = dBody.getElementsByTagName("tr");
            
            var items = document.getElementsByName('items[]');
            if (items.length == 0) {
                clearDelivery(list);
                srow = String(1);
            }            
            else
                srow = String(items.length + 1);
                               
            alt = (dRows.length%2)+1  
             
            if (details) {       
                //alert('Row is '+lastRow);
//                if (details.uprice) 
//                    nAvg_Cost = details.uprice;
//                else
//                    getItemAvgCost(details.id);
//                while (bGettingCost) {                
//                }
            // added by ken for supplying data in suppliers and area that assign FIS PO.,

                //$('supplier').value = details.supp_id; //remove by julz 
                
                if(details.loc == 'CSR')
                    $('rcv_area').value = 'SUP';
                else if(details.loc == 'PHARM')
                    $('rcv_area').value = 'IP';

                //ended by ken

                var qtyslashcommas = details.qty;
                qtyslashcommas = qtyslashcommas.replace(',', '');
                details.qty = parseFloat(qtyslashcommas);
                                
                var nprice = details.price_add;
                nprice = nprice.replace(',','');
                details.price_add = parseFloat(nprice);
                
                var expiry_dt = (details.expiry && (details.expiry != '')) ? formatDate(new Date(getDateFromFormat(details.expiry, 'yyyy-MM-dd')), 'NNN d, yyyy') : '&nbsp;';
                       if (expiry_dt == 'Jan 1, 1970') {
                        expiry_dt = '';
                       };  

                //modified by ken : added some field to get the data from FIS in HIS when saving the item                                       
                     
                src = '<tr class="wardlistrow'+alt+'" id="row_'+details.id+srow+'">' +                        
                          '<input type="hidden" name="items[]" id="item_'+details.id+srow+'" value="'+details.id+'" />'+
                          '<input type="hidden" name="order_nos[]" id="order_no_'+details.id+srow+'" value="'+details.order+'" />'+
                          '<input type="hidden" name="pos[]" id="po_detail_'+details.id+srow+'" value="'+details.po_detail+'" />'+
                          '<input type="hidden" name="supps[]" id="supp_id_'+details.id+srow+'" value="'+details.supp_id+'" />'+
                          '<input type="hidden" name="locs[]" id="location_'+details.id+srow+'" value="'+details.loc+'" />'+
                          '<input type="hidden" name="unit_ids[]" id="unit_'+details.id+srow+'" value="'+details.unit+'" />'+
                          '<input type="hidden" name="dates[]" id="del_date_'+details.id+srow+'" value="'+details.del_date+'" />'+
                          '<input type="hidden" name="names[]" id="description_'+details.id+srow+'" value="'+details.name+'" />'+
                          '<input type="hidden" name="expiry_dts[]" id="expiry_'+details.id+srow+'" value="'+details.expiry+'" />'+
                          '<input type="hidden" name="serial_nos[]" id="serial_'+details.id+srow+'" value="'+details.serial_no+'" />'+
                          '<input type="hidden" name="lot_nos[]" id="lot_'+details.id+srow+'" value="'+details.lot_no+'" />'+
                          '<input type="hidden" name="manufacturer[]" id="manufacturer_'+details.id+srow+'" value="'+details.man+'" />'+
                          '<input type="hidden" name="is_unitpcs[]" id="is_pc_'+details.id+srow+'" value="'+details.is_perpc+'" />'+
                          '<input type="hidden" name="oqtys[]" id="oqty_'+details.id+srow+'" value="'+details.oqty+'" />'+
                          '<input type="hidden" name="qtys[]" id="qty_'+details.id+srow+'" value="'+details.qty+'" />'+
                          '<input type="hidden" name="check[]" id="check_'+details.id+'_'+details.order+'" value="'+details.order+'" />'+
                          '<input name="uprices[]" id="uprice_'+details.id+srow+'" type="hidden" value="'+details.price_add+'">'+
                          '<input name="small_unit[]" type="hidden" value="'+details.small_unit+'">'+
                          '<input name="eqty[]" id="eqty_'+details.id+srow+'" type="hidden" value="'+details.qty+'">'+
                          '<input name="fg_items[]" id="fg_'+details.id+srow+'" type="hidden" value="'+details.is_fg+'">'+
                          '<input id="total_'+details.id+srow+'" name="total_amount[]" type="hidden" value="'+formatNumber(Number(details.qty) * Number(details.price_add) * Number(details.qty_per_pck), 2)+'">'+
                          '<td width="4%"><img class="segSimulatedLink" src="../../images/close_small.gif" border="0" onclick="removeItem(\''+details.id+'\', '+srow+')"/>  <img class="segSimulatedLink" src="../../images/cashier_edit_small.gif" border="0" onclick="editItem(\''+details.id+'\', '+srow+', \''+escapeHtml(details.name)+'\', '+details.qty+', '+details.price_add+', '+details.order+')"/></td>'+
                          '<td width="*">'+details.id+'</td>'+
                          '<td width="1%"><input type="checkbox" onclick="setFG('+details.id+srow+','+details.qty+','+details.price_add+','+details.qty_per_pck+');" id="chkfg_'+details.id+srow+'" '+((details.is_fg=='1') ? 'checked="checked"' : '') +' name="fg_item[]" ></td>'+
                          '<td width="*">'+details.name+'</td>'+
                          // '<td width="4%" align="left"><input size="1" type="text" class="segInput" name="qty_per_pck[]" '+((parseInt(details.is_perpc) == 1) ? 'readOnly': '' )+' id="qty_per_pck_'+details.id+srow+'" value="'+((details.qty_per_pck) ? details.qty_per_pck : '1') +'" onblur="applyQtyPerPck(\''+details.id+'\','+srow+')"></td>'+
                          '<td width="4%" align="left"><input size="1" type="text" class="segInput" name="qty_per_pck[]" '+((parseInt(details.is_perpc) == 1) ? 'readOnly': '' )+' id="qty_per_pck_'+details.id+srow+'" value="'+((details.qty_per_pck) ? details.qty_per_pck : '1') +'"></td>'+
                          '<td width="12%" align="center"><span id="expiry_date_'+details.id+srow+'">'+expiry_dt+'</span></td>'+
                          '<td width="8%" align="center"><span id="serial_no_'+details.id+srow+'">'+(details.serial_no && details.serial_no != '' ? details.serial_no : '&nbsp;')+'</span></td>'+
                          '<td width="4%"><span id="lot_no_'+details.id+srow+'">'+(details.lot_no && details.lot_no != '' ? details.lot_no : '&nbsp;')+'</span></td>'+
                          '<td width="4%"><span id="manufacturer_s'+details.id+srow+'">'+(details.man && details.man != '' ? details.man : '&nbsp;')+'</span></td>'+
                          '<td width="4%"><input style="display:none;" type="text" id="editqty_'+details.id+srow+'" value="'+formatNumber(Number(details.qty),0)+'" onFocus="this.select();" onblur="cancelQty(\''+details.id+'\', '+srow+');" onkeyup="applyQty(event,\''+details.id+'\','+srow+');">'+
                          '<span style="cursor:pointer" id="rowqty_'+details.id+srow+'" onclick="editQty(\''+details.id+'\','+srow+');">'+formatNumber(Number(details.qty),0)+'</span></td>'+
                          '<td width="6%" align="center">'+details.unit_name+'</td>'+
                          '<td width="10%" align="right"><input style="width:95%;display:none;text-align:right" type="text" id="edituprice_'+details.id+srow+'" value="'+formatNumber(Number(details.price_add),2)+'" onFocus="this.select();" onblur="cancelUPrice(\''+details.id+'\', '+srow+');" onkeyup="applyUPrice(event,\''+details.id+'\','+srow+');">'+
                            //'<input name="uprices[]" id="uprice_'+details.id+srow+'" type="hidden" value="'+nAvg_Cost+'">'+formatNumber(Number(nAvg_Cost),2)+'</span></td>'+
                            //modified by bryan for phs 102809
                            '<span style="cursor:pointer" id="rowuprice_'+details.id+srow+'" onclick="editUPrice(\''+details.id+'\','+srow+');">'+formatNumber(Number(details.price_add),2)+'</span></td>'+                          
                          '<td width="12%" align="right"><span id="rowtotal_'+details.id+srow+'">'+formatNumber(Number(details.qty) * Number(details.price_add)* Number(details.qty_per_pck), 2)+'</span></td>'+ 
                      '</tr>';                
            }
            else {
                src = "<tr><td colspan=\"12\">Delivery is currently empty ...</td></tr>";
            }
            if(items.length != 0){
                    dBody.innerHTML += src; 
            }
            else
            dBody.innerHTML += src;

            updateSubtotal();
            return true;
        }
    }   
    return false;
}

function removeItem(id, srow) {
    var destTable, destRows;
    var table = $('delivery-list');
    var rmvRow=document.getElementById("row_"+id+srow);
    if (table && rmvRow) {        
        var rndx = rmvRow.rowIndex-1; 
        table.deleteRow(rmvRow.rowIndex);        
        if (!document.getElementsByName("items[]") || document.getElementsByName("items[]").length <= 0){
            addItemInDelivery(table, null);           
            $('add-item').style.display = '';
            $('supplier').value = '';
        }       
        reclassRows(table,rndx);
    }
    else
        alert(table+' and '+rmvRow);
//    refreshTotal();
}

//added by Nikko 9/23/2014 for escaping html special characters
function escapeHtml(text) {
  return text
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;")
      .replace(/#/g, "&#35;");
}

//added by ken 7/16/2014 for qty editable field
function cancelQty(id, srow) {
    $("editqty_"+id+srow).style.display = "none";
    $("rowqty_"+id+srow).style.display = "";
}

function applyQty(e, id, srow) {
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
        var qty = $("editqty_"+id+srow).value;  
        var uprice = $("edituprice_"+id+srow).value;
        var qty_per_pck = $("qty_per_pck_"+id+srow).value;

        $("rowqty_"+id+srow).innerHTML  = '<input name="eqty[]" id="eqty_'+id+srow+'" type="hidden" value="'+qty+'">'+formatNumber(Number(qty),0);
        $("qty_"+id+srow).value = qty;   

        $("rowtotal_"+id+srow).innerHTML = formatNumber(Number(qty) * Number(uprice) * Number(qty_per_pck), 2);
        $("rowuprice_"+id+srow).innerHTML  = '<input name="uprices[]" id="uprice_'+id+srow+'" type="hidden" value="'+uprice+'">'+formatNumber(Number(uprice),2);
        $("total_"+id+srow).value =  $("rowtotal_"+id+srow).innerHTML.replace(',','');

        $("editqty_"+id+srow).style.display = "none";
        $("rowqty_"+id+srow).style.display = "";
//        $("op_selected"+id).focus();
updateSubtotal();         
    }            
}

function applyQtyPerPck(id, srow){
    var qty = $("editqty_"+id+srow).value;  
    var uprice = $("edituprice_"+id+srow).value;
    var qty_per_pck = $("qty_per_pck_"+id+srow).value;

    $("qty_"+id+srow).value = qty;   
    $("qty_per_pck_"+id+srow).setAttribute('value', qty_per_pck);

    $("rowtotal_"+id+srow).innerHTML = formatNumber(Number(qty) * Number(uprice) * Number(qty_per_pck), 2);
    //$("rowuprice_"+id+srow).innerHTML  = '<input name="uprices[]" id="uprice_'+id+srow+'" type="hidden" value="'+uprice+'">'+formatNumber(Number(uprice),2);
    $("total_"+id+srow).value =  $("rowtotal_"+id+srow).innerHTML.replace(',','');

    $("editqty_"+id+srow).style.display = "none";
    $("rowqty_"+id+srow).style.display = "";
  
    updateSubtotal();
}

function editQty(id, srow) {
    $("editqty_"+id+srow).style.display = "";
    $("rowqty_"+id+srow).style.display = "none";
    $("editqty_"+id+srow).focus();    
}

function jsSetSupplierName(value){
    value = value.options[value.selectedIndex].text;
    $("supplier_name").value = value;
}

function editItem(id, srow, name, qty, price, order){
        var url = 'seg-deliveryitem-params.php?id='+id+'&itemname='+name+'&unit=2&name=piece(s)&update=1&qty='+qty+'&price='+price+'&srow='+srow+'&order='+order;
                overlib(
                        OLiframeContent(url, 465, 260, 'fOrderTray', 0, 'no'),
                        WIDTH,450, TEXTPADDING,0, BORDER,0,
                        STICKY, SCROLL, CLOSECLICK, MODAL,
                        CLOSETEXT, '<img src=../../images/close_red.gif border=0 >',
                        CAPTIONPADDING,2,
                        CAPTION,'Edit '+name,
                        MIDX,0, MIDY,0,
                        STATUS,'Edit '+name);
                return false
}

function updateItem(id, qty, expiry_dt, serial_no, price_add, srow, lot_no, manufacturer){
    var qty_per_pck = $("qty_per_pck_"+id+srow).value;


    $('qty_'+id+srow).value = qty;
    $('editqty_'+id+srow).value = qty;
    $('rowqty_'+id+srow).innerHTML = qty;

    $('expiry_'+id+srow).value = expiry_dt;
    $('expiry_date_'+id+srow).innerHTML = expiry_dt;

    $('serial_'+id+srow).value = serial_no;
    $('serial_no_'+id+srow).innerHTML = serial_no;

    $('lot_'+id+srow).value = lot_no;
    $('lot_no_'+id+srow).innerHTML = lot_no;

    $('manufacturer_'+id+srow).value = manufacturer;
    $('manufacturer_s'+id+srow).innerHTML = manufacturer;

 
    $('uprice_'+id+srow).value = formatNumber(price_add, 2);
    $('rowuprice_'+id+srow).innerHTML = formatNumber(price_add, 2);

    $('total_'+id+srow).value = formatNumber(Number(qty) * Number(price_add) * Number(qty_per_pck), 2);
    $('rowtotal_'+id+srow).innerHTML = formatNumber(qty * price_add * qty_per_pck, 2);


    updateSubtotal();

}

function disabledAdd_item(){
    $('add-item').style.display = 'none';
}

function add_po_no_data(no){
    $('po_no').value = no;
}

//Added by Jarel 06172015
function setFG(id, qty, price_add, qty_per_pck){
  var zero = 0;
  if($('chkfg_'+id).checked) {
    $('fg_'+id).value = 1;
    $('rowtotal_'+id).innerHTML = formatNumber(Number(zero ), 2);
    $('total_'+id).value = formatNumber(Number(zero ), 2);
  }  else {
    $('fg_'+id).value = 0;
    $('rowtotal_'+id).innerHTML = formatNumber(Number(qty) * Number(price_add) * Number(qty_per_pck), 2);
    $('total_'+id).value = formatNumber(Number(qty) * Number(price_add) * Number(qty_per_pck), 2);
  }
  updateSubtotal(); 
}