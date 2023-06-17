
var ViewMode = false;
var items_arr = new Array();

function jsRqstngAreaOptionChngIss(obj, value){
    if(obj.id == 'area_issued') {        
        //$('opw_nr').value  = value;   
        js_ClearOptions('area_dest');
        //xajax_getRequestedAreasIss(value);
    }
}

function resetRefNo(newRefNo,error) {
    $("adjrefno").style.color = error ? "#ff0000" : "";
    $("adjrefno").value=newRefNo;
}

function showRequestedAreasIss(options) {
    $('destinationIss_area').innerHTML = options;
}

function jsAreaSRCOptionChngIss(value){ 
    js_ClearOptions('area_issued');
    xajax_getSourceAreasbypidIss(value);   
}

function showRequestedSRCAreasIss(options) {
    $('sourceIss_area').innerHTML = options;
}


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
    else return confirm('Performing this action will clear the tray of items for adjustment. Do you wish to continue?');
}

function updateAthand(expiry, serial, item, rowno, unitid) {
    if (expiry != '') {
        $("rowexpiry"+item+rowno).value = expiry;
    }
    if (serial != '') {
        $('rowserial'+item+rowno).value = serial;        
    }
    area = $('area_adj').value;
    //xajax_updateValueAtHand(expiry, serial, area, item, rowno, unitid);
}

function changeValueAtHand(qty, id, expiry, serial, rowno) {
    $("athanddata"+id+rowno).innerHTML = Number(qty);    
    $("rowpending"+id+rowno).value = Number(qty); 
    if (expiry == '')    
        $("rowserial"+id+rowno).value = serial;
    else
        $("rowexpiry"+id+rowno).value = expiry;
}  

function updateReasons(reason, id, rowno){
    $("rowreasons"+id+rowno).value = reason;
}

function updateAdjQty(id, value, rowno) {
    $("rowadjqty"+id+rowno).value = value;
    var curqty = $('rowpending'+id+rowno).value;    
    var adjqty = $('rowadjqty'+id+rowno).value;
    curqty = Number(curqty.replace(',',''));
    adjqty = Number(adjqty.replace(',',''));   
    $("rowpmqty"+id+rowno).value = adjqty - curqty;
    $("plusminqty"+id+rowno).value = adjqty - curqty;
}

function plusminusQty(id, value, rowno) {
    $("rowpmqty"+id+rowno).value = value;
    var curqty = $('rowpending'+id+rowno).value;
    curqty = Number(curqty.replace(',',''));
    value  = Number(value.replace(',',''));   
    $('rowadjqty'+id+rowno).value = curqty + value;
    $('adjqty'+id+rowno).value = curqty + value;
}

function generateTab(elem) {     
//    var e = jQuery.Event("keypress");
//    e.which = 9; // # TAB
//    $j("input").trigger(e);
    $j("#"+elem).focus();
}

function applyChngdQty(e, id, value, rowno, isadjqty) {
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

    if ( characterCode == 13 ) {
        if (isadjqty) {
            updateAdjQty(id, value, rowno);
            generateTab("plusminqty"+id+rowno);
        }
        else {
            plusminusQty(id, value, rowno);
            generateTab("reason"+id+rowno);
        }
    }
}
    
function emptyTray() {
    clearOrder($('order-list'));
    appendOrder($('order-list'),null);
    items_arr.length = 0;
    refreshDiscount();
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
    return false;
}

function appendOrderPopulate(list, details, disabled) {
    if (!list) list = $('order-list');
    if (list) {
        var dBody=list.getElementsByTagName("tbody")[0];
        
        if (dBody) {
            var src;
            var items = document.getElementsByName('items[]');
            dRows = dBody.getElementsByTagName("tr");
            
            if (details) {
            alert(details.unit_name);
                var id = details.id;                
//                var rowno = dRows.length; 
                var rowno;
                
                if (items.length == 0) {
                    clearOrder(list);
                    rowno = String(1);
                }            
                else
                    rowno = String(items.length + 1);                 
                          
                if (items) {
                    if ($('rowID'+id)) {
 
//                        var itemRow = $('row'+id+rowno),

                        itemQty = $('rowQty'+id)
                        itemQty.value = parseFloatEx(itemQty.value) + parseFloatEx(details.qty)
                        itemQty.setAttribute('prevValue',itemQty.value)
                        qty = parseFloatEx(itemQty.value)
                        tot = netPrice*qty
                        //$('rowid'+id).value     =   details.id
                        $('rowname'+id+rowno).value    = details.name
                        $('rowathand'+id+rowno).value        = details.athand
                        $('rowunitid'+id+rowno).value        = details.unitid
                        $('rowpmquan'+id+rowno).value        = details.pmquan
                        $('rowadjquan'+id+rowno).value        = details.adjquan
                        $('rowperpc'+id+rowno).value        = details.perpc
                        $('rowexpiry'+id+rowno).value        = details.expiry
                        $('rowserial'+id+rowno).value        = details.serial
                        $('reasons'+id+rowno).value        = details.reasons
                        
                        return true;
                    }
                    if (items.length == 0) clearOrder(list)
                }
                
                alt = (dRows.length%2)+1         
                
//                var disabledAttrib = disabled ? 'disabled="disabled"' : ""
                
                src = 
                    '<tr class="wardlistrow'+alt+'" id="row'+id+rowno+'">' +
                    '<input type="hidden" name="athand[]" id="rowpending'+id+rowno+'" value="'+details.athand+'" />'+
                    '<input type="hidden" name="desc[]" id="rowdesc'+id+rowno+'" value="'+details.desc+'" />'+
                    '<input type="hidden" name="name[]" id="rowname'+id+rowno+'" value="'+details.name+'" />'+
                    '<input type="hidden" name="unitid[]" id="rowunitid'+id+rowno+'" value="'+details.unitid+'" />'+
                    '<input type="hidden" name="unitdesc[]" id="rowunitdesc'+id+rowno+'" value="'+details.unitdesc+'" />'+
                    '<input type="hidden" name="perpc[]" id="rowperpc'+id+rowno+'" value="'+details.perpc+'" />'+
                    '<input type="hidden" name="serial[]" id="rowserial'+id+rowno+'" value="'+details.serial+'" />'+ 
                    '<input type="hidden" name="reasons[]" id="rowreasons'+id+rowno+'" value="'+details.reasons+'" />'+ 
                    '<input type="hidden" name="expiry[]" id="rowexpiry'+id+rowno+'" value="'+details.expiry+'" />'+
                    '<input type="hidden" name="unit_ids[]" id="unit_'+id+rowno+'" value="'+details.unitid+'" />'+
                    '<input type="hidden" name="is_unitpcs[]" id="is_pc_'+id+rowno+'" value="'+details.perpc+'" />'+
                    '<input type="hidden" name="items[]" id="rowis'+id+rowno+'" value="'+details.id+'" />';
                
                if (disabled)
                    src+='<td></td>'
                else
                    src+='<td class="centerAlign" width="5%"><img class="segSimulatedLink" src="../../images/close_small.gif" border="0" onclick="removeItem(\''+id+'\',\''+rowno+'\')"/></td>'
                
                src+=
                    '<td>'+details.id+'</td>'+
                    '<td ><span style="color:#660000">'+details.name+'</span></td>'+
                    '<td  align="center"><span style="color:#660000" id="athanddata'+id+'">'+details.athand+'</span></td>'+
                    '<td align="center"><span style="color:#660000">'+details.unitdesc+'</span></td>'+
                    '<td align="center" colspan="2"><span id="serial_'+id+'">'+details.serial+'</span></td>'+
                    '<td align="center" colspan="2"><span id="expiry_'+id+'">'+details.expiry+'</span></td>'+
                    '<td align="center"><span id="uadjqty'+id+'" style="color:#660000"><input type="hidden" name="adjquan[]" id="rowadjqty'+details.id+'" value="'+details.adjquan+'" />'+details.adjquan+'</span></td>'+
                    '<td align="center"><span id="upmqty'+id+'" style="color:#660000"><input type="hidden" name="pmquan[]" id="rowpmqty'+details.id+'" value="'+details.pmquan+'" />'+details.pmquan+'</span></td>'+                    
                    '<td align="center">'+details.reasons_name+'</td>'+
                    '</tr>';
                
                trayItems++;
            }
            else {
                src = "<tr><td colspan=\"12\">Adjustment list is currently empty...</td></tr>";    
            }
            dBody.innerHTML += src;
            return true;
        }
    }
    return false;
}


function getOriginalNewDataEntered(){
    data = [];
    jQuery('#order-list tbody tr').each(function(){
        data.push({
            inputserial_ : jQuery(this).find('[id*="inputserial_"]').val(),
            lotadd_ : jQuery(this).find('[id*="lotadd_"]').val(),
            inputexpiry_ : jQuery(this).find('[id*="inputexpiry_"]').val(),
            unitcostadd_ : jQuery(this).find('[id*="unitcostadd_"]').val(),
            adjqty : jQuery(this).find('[id*="adjqty"]').val(),
            plusminqty : jQuery(this).find('[id*="plusminqty"]').val(),
            reason : jQuery(this).find('[id*="reason"]').val(),
        });
    });
    return data;
}

function returnOriginalDataEntered(data){
    len = jQuery('#order-list tbody tr').length;
    jQuery('#order-list tbody tr').each(function(i,n){
        if(len-1 == i)
            return;
        jQuery(this).find('[id*="inputserial_"]').val(data[i].inputserial_);
        jQuery(this).find('[id*="lotadd_"]').val(data[i].lotadd_);
        jQuery(this).find('[id*="inputexpiry_"]').val(data[i].inputexpiry_);
        jQuery(this).find('[id*="unitcostadd_"]').val(data[i].unitcostadd_);
        jQuery(this).find('[id*="adjqty"]').val(data[i].adjqty);
        jQuery(this).find('[id*="plusminqty"]').val(data[i].plusminqty);
        jQuery(this).find('[id*="reason"]').val(data[i].reason);
    });
}

function appendOrder(list, details, disabled) {

    if (!list) list = $('order-list');
    if (list) {
        var dBody=list.getElementsByTagName("tbody")[0];
        
        if (dBody) {            
            var src;
            var items = document.getElementsByName('items[]');
            dRows = dBody.getElementsByTagName("tr");
            data = getOriginalNewDataEntered();
            if (details) {
                var unique_id = details.id + details.unitid;
              
                if(items_arr.indexOf(unique_id)<0){ //checks if item already exists
                     items_arr.push(unique_id);

                    var id = details.id,
                        expiry = details.expiry,
                        serial = details.serial;
                        if (items.length == 0) {
                            clearOrder(list);
                            rowno = String(1);
                        }            
                        else
                            rowno = String(items.length + 1); 
                    
                    if (items) {
                        if ($('rowID'+id)) {
     
    //                        var itemRow = $('row'+id+rowno),
                            itemQty = $('rowQty'+id)
                            itemQty.value = parseFloatEx(itemQty.value) + parseFloatEx(details.qty)
                            itemQty.setAttribute('prevValue',itemQty.value)
                            qty = parseFloatEx(itemQty.value)
                            tot = netPrice*qty
                            //$('rowid'+id).value     =   details.id
                            $('rowname'+id+rowno).value     = details.name
                            $('rowdesc'+id+rowno).value     = details.desc
                            $('rowathand'+id+rowno).value   = details.athand
                            $('rowunitid'+id+rowno).value   = details.unitid
                            $('rowunitdesc'+id+rowno).value = details.unitdesc
                            $('rowperpc'+id+rowno).value    = details.is_perpc
                            $('rowexpiry'+id+rowno).value   = details.expiry
                            $('rowserial'+id+rowno).value   = details.serial
                            $('reasons'+id+rowno).value     = details.reasons
                            
                            return true                        
                        }
                        if (items.length == 0) clearOrder(list)
                    }
                    
                    
                    alt = (dRows.length%2)+1;                
                    src = 
                        '<tr class="wardlistrow'+alt+'" id="row'+id+rowno+'" data-item=1>' +
                        '<input type="hidden" name="athand[]" id="rowpending'+id+rowno+'" value="'+details.athand+'" />'+
                        '<input type="hidden" name="desc[]" id="rowdesc'+id+rowno+'" value="'+details.desc+'" />'+
                        '<input type="hidden" name="name[]" id="rowname'+id+rowno+'" value="'+details.name+'" />'+
                        '<input type="hidden" name="unitid[]" id="rowunitid'+id+rowno+'" value="'+details.unitid+'" />'+
                        '<input type="hidden" name="unitdesc[]" id="rowunitdesc'+id+rowno+'" value="'+details.unitdesc+'" />'+
                        '<input type="hidden" name="perpc[]" id="rowperpc'+id+rowno+'" value="'+details.is_perpc+'" />'+
                        '<input type="hidden" name="serial[]" id="rowserial'+id+rowno+'" value="'+details.serial+'" />'+ 
                        '<input type="hidden" name="reasons[]" id="rowreasons'+id+rowno+'" value="" />'+ 
                        '<input type="hidden" name="expiry[]" id="rowexpiry'+id+rowno+'" value="'+'0000-00-00'+'" />'+
                        '<input type="hidden" name="unit_ids[]" id="unit_'+details.id+rowno+'" value="'+details.unitid+'" />'+
                        '<input type="hidden" name="is_unitpcs[]" id="is_pc_'+details.id+rowno+'" value="'+details.is_perpc+'" />'+
                        '<input type="hidden" name="items[]" id="rowis'+id+rowno+'" value="'+details.id+'" />';
                    
                    if (disabled)
                        src+='<td></td>';
                    else
                        src+='<td class="centerAlign" width="3%"><img class="segSimulatedLink" src="../../images/close_small.gif" border="0" onclick="removeItem(\''+id+'\',\''+rowno+'\',\''+unique_id+'\')"/></td>';
                    
                    src+=
                        '<td>'+details.id+'</td>'+
                        '<td ><span style="color:#660000">'+details.name+'</span></td>'+
                        '<td  align="center"><span style="color:#660000" id="athanddata'+id+rowno+'">'+details.athand+'</span></td>'+
                        '<td align="center"><span style="color:#660000">'+details.unitdesc+'</span></td>'+
                        '<td align="center">';                
                                    
                     src += '<span id="newserial_'+id+rowno+'" style="display:none"><input type="text" id="inputserial_'+id+rowno+'" size="10" onblur="updateAthand(\'\', this.value, \''+details.id+'\', \''+rowno+'\', '+details.unitid+');" value="" /></span>';
                     if(serial!='-')   
                         src += '<span id="serial_'+id+rowno+'"><select id="serial'+id+rowno+'" onchange="updateAthand(\'\', this.options[this.selectedIndex].value,\''+details.id+'\',\''+rowno+'\', '+details.unitid+');"/>'+details.serial+'</select></span>';
                     else   
                         src += '<span id="serial_'+id+rowno+'" class="alignmid">'+serial+'</span>';
                     src += '</td><td align="right" width="3px"><span id="serialadd_'+id+rowno+'"><img class="segSimulatedLink" src="../../images/inventory/add.png" border="0" onclick="showSerialInput(\''+details.id+'\', \''+rowno+'\');"/></span></td>';
                     src+= '<td align="right" width="3px"><input type="text" name = "lot[]" id="lotadd_'+id+rowno+'"></td>' +

                         '<td align="center">';
                     src += '<span id="newexpiry_'+id+rowno+'" style="display:none"><input type="text" id="inputexpiry_'+id+rowno+'" size="10" onfocus="setDateMask(this);" onblur="updateAthand(this.value, \'\', \''+details.id+'\', \''+rowno+'\', '+details.unitid+');" value="" /></span>';
                     if (expiry!='-')
                         src += '<span id="expiry_'+id+rowno+'"><select id="expiries'+id+rowno+'" onchange="updateAthand(this.options[this.selectedIndex].value, \'\', \''+details.id+'\',\''+rowno+'\', '+details.unitid+');"/>'+details.expiry+'</select></span>';                                          
                     else   
                         src += '<span id="expiry_'+id+rowno+'" class="alignmid">'+expiry+'</span>';
                     src += '</td><td align="right" width="3px"><span id="expiryadd_'+id+rowno+'"><img class="segSimulatedLink" src="../../images/inventory/add.png" border="0" onclick="showExpiryInput(\''+details.id+'\', \''+rowno+'\');" /></span></td>';
                    src +=  '<td align="right" width="3px"><input type="text" name = "unitcost[]" id="unitcostadd_'+id+rowno+'" value="'+details.unit_cost+'"></td>';
                        src+=
                        '<td align="center"><input type="text" name="adjqty[]" id="adjqty'+id+rowno+'" style="text-align:right" size="4" onblur="updateAdjQty(\''+details.id+'\',this.value,\''+rowno+'\');" onkeyup="applyChngdQty(event, \''+details.id+'\',this.value,\''+rowno+'\', 1);" /><span id="uadjqty'+id+rowno+'" style="color:#660000"><input type="hidden" name="adjquan[]" id="rowadjqty'+details.id+rowno+'" value="" /></span></td>'+
                        '<td align="center"><input type="text" name="plusminqty[]" id="plusminqty'+id+rowno+'" style="text-align:right" size="4" onblur="plusminusQty(\''+details.id+'\',this.value,\''+rowno+'\');" onkeyup="applyChngdQty(event, \''+details.id+'\',this.value,\''+rowno+'\', 0);" /><span id="upmqty'+id+rowno+'" style="color:#660000"><input type="hidden" name="pmquan[]" id="rowpmqty'+details.id+rowno+'" value="" /></span></td>'+
                        '<td align="center">'+
                            '<select id="reason'+id+rowno+'" onchange="updateReasons(this.options[this.selectedIndex].value,\''+details.id+'\',\''+rowno+'\');"/>'+
                            details.reasons+'</select>'+
                        '</td>'+
                        '</tr>';
                    
                    trayItems++;
                }else{
                    alert("Item is already in the list.");
                    return false;
                }
            }
            else {
                src = "<tr><td colspan=\"14\">Adjustment list is currently empty...</td></tr>";
            }
            dBody.innerHTML += src;
            returnOriginalDataEntered(data);
            return true;
        }
    }
    return false;
}

function showExpiryInput(id, rowno) {
    document.getElementById("expiry_"+id+rowno).style.display = 'none';
    document.getElementById("expiryadd_"+id+rowno).style.display = 'none';
    document.getElementById("newexpiry_"+id+rowno).style.display = '';   
}

function showSerialInput(id, rowno) {   
    document.getElementById("serial_"+id+rowno).style.display = 'none';
    document.getElementById("serialadd_"+id+rowno).style.display = 'none';
    document.getElementById("newserial_"+id+rowno).style.display = '';   
}

function refreshDiscount() {
    var nodes;
    var nr = $('encounter_nr').value;
    if (nr)
        nodes = document.getElementsByName("charity[]");
    else
        nodes=document.getElementsByName("discount[]");
    totalDiscount = 0;
    if (nodes) {
        for (var i=0;i<nodes.length;i++) {
            if (nodes[i].value) totalDiscount += parseFloatEx(nodes[i].getAttribute('discount'));
        }
    }
    var dItem = $("show-discount");
    if (dItem) {
        dItem.value = parseFloatEx(totalDiscount * 100).toFixed(2);
    }
    refreshTotal();
}

function refreshTotal() {
    
}

function formatNumber(num,dec) {
    var nf = new NumberFormat(num);
    if (isNaN(dec)) dec = nf.NO_ROUNDING;
    nf.setPlaces(dec);
    return nf.toFormatted();
}

function pSearchClose() {
    cClick();
}

function removeItem(id,rowno,unique_id) {
    var table = $('order-list');
    var rmvRow=document.getElementById("row"+id+rowno);
    if (table && rmvRow) {
        var rndx = rmvRow.rowIndex-1;
        table.deleteRow(rmvRow.rowIndex);
        items_arr.splice(items_arr.indexOf(unique_id), 1); //delete item in array
        if (!document.getElementsByName("items[]") || document.getElementsByName("items[]").length <= 0)
            appendOrder(table, null);
        reclassRows(table,rndx);
    }
    //refreshTotal();
    else
        alert(table+' and '+rmvRow);
}

