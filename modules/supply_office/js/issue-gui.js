
var ViewMode = false;

function jsRqstngAreaOptionChngIss(obj, value){
    if(obj.id == 'area_issued') {        
        //$('opw_nr').value  = value;   
        js_ClearOptions('area_dest');
        xajax_getRequestedAreasIss(value);
    }
}

function resetRefNo(newRefNo,error) {
    $("refno").style.color = error ? "#ff0000" : "";
    $("refno").value=newRefNo;
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
    else return confirm('Performing this action will clear the order tray. Do you wish to continue?');
}

    
function emptyTray() {
    clearOrder($('order-list'));
    appendOrder($('order-list'),null);
    refreshDiscount();
}

function clearOrder(list) {    
    if (!list) list = $('order-list')
    if (list) {
        var dBody=list.getElementsByTagName("tbody")[0]
        if (dBody) {
            dBody.innerHTML = ""
            return true
        }
    }
    return false
}

//Created by EJ 10/30/2014
function validateRequestQTy(id) {

    var athand = document.getElementById('athand'+id).value;
    var request_pending = document.getElementById('request_pending'+id).value;
    var avg = document.getElementById('avg_cost'+id).innerHTML;
    var tot = document.getElementById('total_cost'+id);

    var default_pending =  $('request_pending_hidden'+id).value;

    // request_pending = parseInt(request_pending);
    // athand = parseInt(athand);

    tot.innerHTML = formatNumber((parseFloatEx(avg) * request_pending), 2);

    if (parseFloatEx(request_pending) > parseFloatEx(athand)) {
     var ans = confirm('Request Quantity is greater than Available Quantity, do you want to continue?');
        if(!ans)
        {
            // alert("Requested Quantity is greater than Available Quantity");
            $('request_pending'+id).value = default_pending;
        }
    };
}

//Created by EJ 11/04/2014
function getAvailableStock(id , area) {
    xajax_getAvailableQty(id, area);   
}

//Created by EJ 11/04/2014
function getPendingQty(id, refno, area, area_dest) {
    xajax_getPendingQty(id, refno, area, area_dest);   
}

//Created by EJ 11/04/2014
function getAvgCost(id, area, rowno) {
    xajax_getAvgCost(id, area, rowno);   
}

//Created by EJ 11/04/2014
function getTotalCost(id, refno, area, area_dest) {
    xajax_getTotalCost(id, refno, area, area_dest);   
}

//Created by EJ 11/04/2014
function displayAvailableStock(id, athand) {
    $('athand'+id).value = athand;
}

//Created by EJ 11/04/2014
function displayPendingStock(id, pending) {
    $('request_pending'+id).value = pending;
    $('request_pending_hidden'+id).value = pending;
}

//Created by EJ 11/04/2014
function displayAvgCost(id, rowno, avg)  {
    $('avg_cost'+id+rowno).value = formatNumber(Number(avg),2);
    $('avg_cost'+id).innerHTML = formatNumber(Number(avg),2);
}

//Created by EJ 11/04/2014
function displayTotalCost(id, total) {
    $('total_cost'+id).innerHTML = formatNumber(Number(total),2);
}

//Modified by EJ 11/04/2014
function appendOrder(list, details, disabled, cnt) {

    list = $('order-list');
    if (list) {
        var dBody=list.getElementsByTagName("tbody")[0];

        if (dBody) {
            var src;
            var totalqty;
            var lastRowNum = null,
                    items = document.getElementsByName('items[]');
                    dRows = dBody.getElementsByTagName("tr");
            if (details) {
                    /*details.unitid = 2;
                    details.unitdesc = "piece(s)";
                    details.perpc = 1;*/
                var req_refno = details.refno,
                    item_codes = details.item_codes,
                    area = details.area,
                    areadest = details.areadest,
                    id = details.id+details.unitid,
                    name = details.name,
                    desc = details.desc,
                    pending = details.pending,
                    d = details.d,
                    soc = details.soc,
                    unitid = details.unitid,
                    unitdesc = details.unitdesc,
                    perpc = details.perpc,
                    type = details.type,
                    expdate = details.expdate,
                    avg = ((typeof(details.avg) == 'undefined') ? 0 : details.avg),
                    qtyperpack = details.qtyperpack,
                    serial = details.serial,
                    epropno = details.epropno,
                    eestlife = details.eestlife;
                    athand = details.athand;

                details.avg = ((typeof(details.avg) == 'undefined') ? 0 : details.avg)
                //prototype js selectors
                var row = $$("tr[data-item='" + id + "']");
                if(row.length > 0) {

                    /*var confirmed = confirm('Item Exists on Tray. Do you want to overwrite?');
                    if(!confirmed)*/
                        return false;

                    var rowNo = row[0].dataset['row'];
                    var itemCode = row[0].dataset['item'];
                    //remove item row if exists
                    removeItem(itemCode, rowNo);
                }

                if (items.length == 0) {
                        clearOrder(list);
                        rowno = String(1);
                    }
                    else
                        rowno = String(items.length + 1);

                if (details) {
                    if ($('rowID'+id)) {

                        var itemRow = $('row'+id),
                                itemQty = $('rowQty'+id)
                        itemQty.value = parseFloatEx(itemQty.value) + parseFloatEx(details.qty)
                        itemQty.setAttribute('prevValue',itemQty.value)
                        qty = parseFloatEx(itemQty.value)
                        tot = netPrice*qty
                        $('rowid'+id).value     =   details.id
                        $('rowname'+id+rowno).value    = details.name
                        $('rowrefno'+id+rowno).value            = details.refno
                        $('rowdesc'+id+rowno).value            = details.desc
                        $('rowpending'+id+rowno).value        = details.pending
                        $('rowunitid'+id+rowno).value        = details.unitid
                        $('rowunitdesc'+id+rowno).value        = details.unitdesc
                        $('rowperpc'+id+rowno).value        = details.perpc
                        $('rowexpdate'+id+rowno).value        = details.expdate
                        $('rowserial'+id+rowno).value        = details.serial
                        $('rowd'+id+rowno).value        = details.d
                        $('rowsoc'+id+rowno).value        = details.soc

                        return true;
                    }
                    if (items.length == 0) clearOrder(list)
                }

                src = '';

            

               // rowno = i+1;

                src +=  '<tr class="wardlistrow'+rowno+'" id="row'+id+rowno+'" data-item="' + id + '" data-row = "' + rowno + '">';

                //getAvailableStock(id[i],area);
                //getPendingQty(id[i], req_refno, area, areadest);
                //getAvgCost(id[i], area, rowno);
                var totalCost = parseFloatEx(pending) * parseFloatEx(avg);
         
                src+=

                    '<input type="hidden" name="desc[]" id="rowdesc'+id+rowno+'" value="'+name+'" />'+
                    '<input type="hidden" name="name[]" id="rowname'+id+rowno+'" value="'+name+'" />'+
                    '<input type="hidden" name="unitid[]" id="rowunitid'+id+rowno+'" value="'+details.unitid+'" />'+
                    '<input type="hidden" name="unitdesc[]" id="rowunitdesc'+id+rowno+'" value="'+details.unitdesc+'" />'+
                    '<input type="hidden" name="items[]" id="rowis'+id+rowno+'" value="'+details.id+'" />'+
                    '<input type="hidden" name="reqrefno[]" id="reqrefno'+id+rowno+'" value="'+details.refno+'" />'+
                    '<input type="hidden" name="avg_cost[]" id="avg_cost'+id+rowno+'" value="'+formatNumber(avg,2)+'" />'+
                    '<input type="hidden" name="perpc[]" value="'+details.perpc+'" />'+
                    '<input type="hidden" name="request_pending_hidden[]" id="request_pending_hidden'+id+'" value="'+pending+'" />'+

                    '<td class="centerAlign" width="5%"><img class="segSimulatedLink" src="../../images/close_small.gif" border="0" onclick="removeItem(\''+id+'\',\''+rowno+'\')"/></td>'+
                    '<td><span style="color:#660000">'+details.id+'</spam></td>'+
                    '<td ><span style="color:#660000">'+name+'</span></td>'+
                    '<td align="center"><input disabled type="text" class="segInput" id="athand'+id+'" name="athand[]"  style="width:80%;text-align:center;color:#660000" value="'+athand+'"/></td>'+
                    '<td align="center"><input type="text" class="segInput" id="request_pending'+id+'" name="request_pending[]" style="width:80%;text-align:center;color:#660000" value="'+pending+'" onblur="validateRequestQTy('+ id+')"/></td>'+
                    '<td align="center"><span style="color:#660000">'+details.unitdesc+'</span></td>'+
                    '<td align="right"><span style="color:#660000" id="avg_cost'+ id+'" name="avg_cost[]">'+formatNumber(avg,2)+'</span></td>'+
                    '<td align="right"><span style="color:#660000" id="total_cost'+ id+'" name="total_cost[]">'+formatNumber(totalCost,2)+'</span></td></tr>';


            }
            else {
                src = "<tr><td colspan=\"10\">Issue list is currently empty...</td></tr>";
            }

            dBody.innerHTML += src;
            return true;
        }
    }
    return false;
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
    var nr = $('encounter_nr').value;

    cClick();
}

function removeItem(id,rowno) {
    var destTable, destRows;
    var table = $('order-list');
    var rmvRow=document.getElementById("row"+id+rowno);
    if (table && rmvRow) {
        var rndx = rmvRow.rowIndex-1;
        table.deleteRow(rmvRow.rowIndex);
        if (!document.getElementsByName("items[]") || document.getElementsByName("items[]").length <= 0)
            appendOrder(table, null);
        reclassRows(table,rndx);
    }
    refreshTotal();
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

/**
* Added By Jarel
* 1/28/2015
*/
function populateIssueProductList(refno, area, areadest){
    xajax_populateIssueProduct(refno, area, areadest);
}