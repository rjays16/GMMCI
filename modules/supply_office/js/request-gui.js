var ViewMode = false;

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
    else return confirm('Performing this action will clear the order tray. Do you wish to continue?');
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
    clearOrder($('order-list'));
    addItemToRequest($('order-list'),null);
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
                }
            }
        }
    }
}

function clearOrder(list) {    
    if (!list) list = $('order-list')
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

function showRequestedAreas(options) {
    $('requested_area').innerHTML = options;
}

function addItemToRequest(list, details) {
    if (!list) list = $('order-list');
    if (list) {
        var dBody=list.getElementsByTagName("tbody")[0];
        if (dBody) {
            var src;       
            var totalqty;     
            var dRows = dBody.getElementsByTagName("tr");
            
            var items = document.getElementsByName('items[]');
            if (items.length == 0) {
                clearOrder(list);
            }                           
            alt = (dRows.length%2)+1   
                              
            if (details) {       
               
                if(details.is_perpc == 0){
                    totalqty = details.qty * details.perpack * details.avg;
                }
                else
                    totalqty = details.qty * details.avg;

             
                if(items){
                    if($('item_'+details.id)){
                       return false;
                    }
                }

                   src = '<tr class="wardlistrow'+alt+'" id="row_'+details.id+'">' +                        
                          '<input type="hidden" name="items[]" id="item_'+details.id+'" value="'+details.id+'" />'+
                          '<input type="hidden" name="descs[]" id="desc_'+details.id+'" value="'+details.desc+'" />'+
                          '<input type="hidden" name="unit_ids[]" id="unit_'+details.id+'" value="'+details.unit+'" />'+
                          '<input type="hidden" name="is_unitpcs[]" id="is_pc_'+details.id+'" value="'+details.is_perpc+'" />'+
                          '<input type="hidden" name="is_unitname[]" id="is_name_'+details.id+'" value="'+details.unit_name+'" />'+
                          '<input type="hidden" name="itemName[]" id="is_itemname_'+details.id+'" value="'+details.name+'" />'+ 

                          '<input type="hidden" name="perpacks[]" id="perpack_'+details.id+'" value="'+details.perpack+'" />'+
                          // '<input type="hidden" name="qtys[]" id="qty_'+details.id+'" value="'+details.qty+'" />'+
                          '<input type="hidden" name="avg_[]" id="avg_'+details.id+'" value="'+details.avg+'" />'+
                          '<input type="hidden" name="tot_[]" id="tot_'+details.id+'" value="'+totalqty+'" />'+
                          '<td width="4%"><img class="segSimulatedLink" src="../../images/close_small.gif" border="0" onclick="removeItem(\''+details.id+'\')"/></td>'+
                          '<td width="10%">'+details.id+'</td>'+
                          '<td width="*">'+details.name+'</td>'+
                          '<td width="5%" align="right">'+formatNumber(Number(details.avg),2)+'</td>'+

                          /* edited by Sarah to edit the no. of quantity 9/28/2015 */
                          // '<td width="4%" align="center">'+formatNumber(Number(details.qty),0)+'</td>'+
                          '<td class="centerAlign" nowrap="nowrap" id="qty_'+details.id+'">'+
                          //'<input type="text" class="segInput" name="qty[]" id="rowQty'+id+'" itemID="'+id+'" value="'+details.qty+'" prevValue="'+details.qty+'" style="width:80%;text-align:right"'+(disabled ? ' disabled="disabled"' : '')+' onfocus="this.value=this.getAttribute(\'prevValue\')" onchange="adjustQty(this)"/>'+
                          '<input type="text" class="segInput" name="qtys[]" id="rowQty'+details.id+'" itemID="'+details.id+'" value="'+details.qty+'" prevValue="'+details.qty+'" style="width:80%;text-align:center" onblur="adjustQty(this);" onkeyup="editQuantity(\''+details.id+'\')"  onkeypress="return event.charCode >= 48 && event.charCode <= 57" />'+
                         
                           '<input type="hidden" name="PrevRows[]" id="preQtyrows'+details.id+'" value="'+details.qty+'" />'+
                          '</td>'+
                          /* end Sarah to edit the no. of quantity 9/28/2015 */

                          '<td width="4%">'+details.unit_name+'</td>'+
                          '<td width="5%" align="right"><span name="tot_disp[]" id="tot_disp'+details.id+'">'+formatNumber(Number(totalqty),2)+'</span></td>'+
                      '</tr>';
                                         
             
            }
            else {
                src = "<tr><td colspan=\"7\">Request list is currently empty ...</td></tr>";
            }
                
            dBody.innerHTML += src;
            return true;
        }
    }   
    return false
}

function removeItem(id) {
    var destTable, destRows;
    var table = $('order-list');
    var rmvRow=document.getElementById("row_"+id);
    if (table && rmvRow) {        
        var rndx = rmvRow.rowIndex-1;
        table.deleteRow(rmvRow.rowIndex);        
        if (!document.getElementsByName("items[]") || document.getElementsByName("items[]").length <= 0)
            addItemToRequest(table, null);           
        reclassRows(table,rndx);
    }
    else
        alert(table+' and '+rmvRow);
//    refreshTotal();
}

// added by Sarah 9/28/2015
function editQuantity(id) {
   
   var remainingqty = parseFloat($('perpack_'+id).value);
   var newqty = parseFloat($('rowQty'+id).value);
   var prevQtyissue = parseFloat($('preQtyrows'+id).value);

    if(newqty > remainingqty){
        alert("Requested quantity exceed the available quantity..");
        $('rowQty'+id).value = prevQtyissue;
    }else{
        if (!isNaN(newqty) && newqty!=null){
            document.getElementById("rowQty"+id).setAttribute("value",newqty);
       }else{
            document.getElementById("rowQty"+id).setAttribute("value",0);    
       }
       refreshDiscount(); 
    }
   
}

function adjustQty(obj) {
    var id = obj.getAttribute("itemID");
    if (isNaN(obj.value)) {
        obj.setAttribute("value", obj.getAttribute("prevValue"));
        obj.value = obj.getAttribute("prevValue");
        return false;
    }

    if(parseFloatEx(obj.value) <= 0){
        alert('Unable to request negative quantity');
        obj.setAttribute("value", obj.getAttribute("prevValue"));
        obj.value = obj.getAttribute("prevValue");
        return false;
    }

    if (parseFloatEx(obj.value) != parseFloatEx(obj.getAttribute("prevValue"))) {
        obj.setAttribute("prevValue",parseFloatEx(obj.value));
        $('tot_'+id).value = parseFloatEx($('avg_'+id).value)*parseFloatEx(obj.value);
        $('tot_disp'+id).innerHTML = formatNumber(parseFloatEx($('avg_'+id).value)*parseFloatEx(obj.value),2);
        refreshDiscount();
    }
    
    //obj.value = formatNumber(obj.value,2);
    return true;
}

// end Sarah 9/28/2015