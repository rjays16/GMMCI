var currentPage=0, lastPage=0;
var FIRST_PAGE=1, PREV_PAGE=2, NEXT_PAGE=3, LAST_PAGE=4, SET_PAGE=0;

function display(str) {
    if($('ajax_display')) $('ajax_display').innerHTML = str.replace('\n','<br>');
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

//price_add parameter added by bryan 102809
function prepareAdd(id, unit, unit_name, isperpc, qty, expiry_dt, serial_no, lot_no, manufacturer, price_add, perpack) {
    var details = new Object();
    details.id = $('id'+id).innerHTML;
    details.name = $('name'+id).innerHTML;
    details.desc = $('desc'+id).innerHTML;
    details.expiry = expiry_dt; 
    details.serial_no = serial_no
    details.lot_no = lot_no
    details.manufacturer = manufacturer
    details.unit = unit;
    details.qty = qty;
    details.unit_name = unit_name;
    details.is_perpc = Number(isperpc);    
    //added by bryan by request of phs102809
    if(price_add != '') details.price_add = price_add;
    else details.price_add = 0;
    details.qty_per_pck = perpack;
    
    var list = window.parent.document.getElementById('delivery-list');
    result = window.parent.addItemInDelivery(list,details);
    if (result){
        //alert("unit = "+unit);
        alert('Item added to delivery ...');
    }else
        alert('Failed to add item ...');
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

function addProductToList(listID, details) {
    // ,id, name, desc, cash, charge, cashsc, chargesc, d, soc
    var list=$(listID), dRows, dBody, rowSrc;
    var i;
    if (list) {
        dBody=list.getElementsByTagName("tbody")[0];
        dRows=dBody.getElementsByTagName("tr");

        // get the last row id and extract the current row no.
          
        if (typeof(details)=="object") {
            var id = details.id,
                name = details.name,
                desc = details.desc,
                pack = (details.pck_unitid == '' ? 'pack(s)' : details.pck_unitid),
                piece = (details.pc_unitid == '' ? 'piece(s)' : details.pc_unitid),
                packname = details.pck_unitname,
                piecename = details.pc_unitname,
                avg = details.avg;
                perpack =details.qtypack,  
                supplier = details.supplier_price;  
                
                //bigunitprce = parseFloatEx(avg) * parseFloatEx(perpack);

            var tmpid = id.replace("-", "");                      
                                  
            if (id)  
                rowSrc = "<tr>"+
                            '<td>'+
                                '<span id="id'+tmpid+'" style="font:bold 11px Arial;color:#660000">'+id+'</span><br />'+
                            '</td>'+
                            '<td align="left">'+
                                //'<input id="soc'+id+'" type="hidden" value="'+soc+'"/>'+
                                '<span id="name'+tmpid+'" style="font:bold 12px Arial;color:#000066">'+name+'</span><br />'+
                                '<div style=""><div id="desc'+tmpid+'" style="font:normal 11px Arial; color:#404040">'+desc+'</div></div></td>'+
                            '<td align="center">'+
                                '<span id="perpack'+tmpid+'" style="font:bold 11px Arial;color:#660000">'+perpack+'</span><br />'+
                            '</td>'+
                             '<td>'+
                                '<input type="button" name="nem[]" id="nem'+tmpid+'" value="'+packname+'" style="cursor:pointer; color:#000066; font-family:verdana; font-weight:bold; padding:0px 2px" '+
                                    'onclick="showAdditionalInfo(\''+tmpid+'\',\''+pack+'\',\''+packname+'\', 0,\''+escape(name)+'\',\''+avg+'\',\''+perpack+'\')" '+
                                    //'onclick="showAdditionalInfo(\''+tmpid+'\',\''+pack+'\',\''+packname+'\', 0)" '+
                                '/>'+
                            '</td>'+
                            '<td>'+
                                '<input type="button" name="nem[]" id="nem'+tmpid+'" value="'+piecename+'" style="cursor:pointer; color:#000066; font-family:verdana; font-weight:bold; padding:0px 2px" '+
                                    'onclick="showAdditionalInfo(\''+tmpid+'\',\''+piece+'\',\''+piecename+'\', 1,\''+escape(name)+'\',\''+avg+'\',1)" '+ //set quantity default to 1 for all small unit pieces
                                    //'onclick="showAdditionalInfo(\''+tmpid+'\',\''+piece+'\',\''+piecename+'\', 1)" '+
                                '/>'+
                            '</td>'+
                        '</tr>';
        }
        else {
            rowSrc = '<tr><td colspan=\"5\" style="">No such product exists...</td></tr>';
        }
        dBody.innerHTML += rowSrc;
    }
}

//added last parameter for request to display name in popup
function showAdditionalInfo(id, unit, unit_name, isperpc,name,supplier,perpack) {  
    var seg_URL_APPEND = $F('seg_URL_APPEND');
    return overlib(OLiframeContent('seg-deliveryitem-params.php'+seg_URL_APPEND+'&id='+escape(id)+'&unit='+unit+'&name='+escape(unit_name)+'&perpc='+escape(isperpc)+'&itemname='+name+'&supplier='+escape(supplier)+'&perpack='+escape(perpack), 460, 260, 'fRelatedInfo', 0, 'no'),
                    WIDTH, 450, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL, CLOSETEXT,
                    '<img src=../../images/close.gif border=0>', CAPTIONPADDING, 4, CAPTION, 'Related Item Information',
                    MIDX, 0, MIDY, 0, STATUS, 'Related Item Information');              
}