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


function prepareAdd(id,unit,unit_name, isperpc, avg, perpack) {
    var details = new Object();
    
    qty=0;    
    if ($('name'+id).value != '1') {
        while (qty) {
        }
        while (isNaN(parseFloat(qty)) || parseFloat(qty)<=0) {
            qty = prompt("Enter quantity:")
            if (qty === null) return false;
        }
    }    
    details.id = $('id'+id).innerHTML;
    details.name = $('name'+id).innerHTML;
    details.desc = $('desc'+id).innerHTML;
    details.unit = unit;
    details.qty = qty;
    details.unit_name = unit_name;
    details.is_perpc = Number(isperpc);
    details.avg = avg;
    details.perpack = perpack;
    
    //edite by julz

    // if(parseFloat(qty) > parseFloat(perpack)){
    //       alert('Requested quantity exceed the available quantity.');
    // }
    // else{
        var list = window.parent.document.getElementById('order-list');
        result = window.parent.addItemToRequest(list,details);
        if (result){
            //alert("unit = "+unit);     
            alert('Item added to request list here...');
        }else
            alert('Item Already in the list...');
    //}
    
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
                avg = details.avg!=''?details.avg:0,
                qtyperpack = details.qtypack,
                perpack = details.perpack,
                pack = (details.pck_unitid == '' ? 'pack(s)' : details.pck_unitid),
                piece = (details.pc_unitid == '' ? 'piece(s)' : details.pc_unitid),
                packname = details.pck_unitname,
                piecename = details.pc_unitname;               
                                  
            if (id)            
                rowSrc = "<tr>"+
                            '<td>'+
                                '<span id="id'+id+'" style="font:bold 11px Arial;color:#660000">'+id+'</span><br />'+
                            '</td>'+
                            '<td align="left">'+
                                //'<input id="soc'+id+'" type="hidden" value="'+soc+'"/>'+
                                '<span id="name'+id+'" style="font:bold 12px Arial;color:#000066">'+name+'</span><br />'+
                                '<div style=""><div id="desc'+id+'" style="font:normal 11px Arial; color:#404040">'+desc+'</div></div></td>'+
                             '<td align="center">'+
                                '<span id="perpack'+id+'" style="font:bold 11px Arial;color:#660000">'+perpack+'</span><br />'+
                             '</td>'+
                             '<td align="center">'+
                                '<span id="qtyperpack'+id+'" style="font:bold 11px Arial;color:#660000">'+qtyperpack+'</span><br />'+
                             '</td>'+
                               /*  '<td>'+
                                    '<input type="button" name="nem[]" disabled id="nem'+id+'" value="'+packname+'" style="color:#000066; font-family:verdana; font-weight:bold; padding:0px 2px" '+
                                        'onclick="prepareAdd(\''+id+'\',\''+pack+'\',\''+packname+'\', 0,\''+avg+'\',\''+perpack+'\')" '+
                                    '/>'+
                                '</td>'+*/
                            '<td>'+
                                '<input type="button" name="nem[]" id="nem'+id+'" value="'+piecename+'" style="color:#000066; font-family:verdana; font-weight:bold; padding:0px 2px" '+
                                    'onclick="prepareAdd(\''+id+'\',\''+piece+'\',\''+piecename+'\', 1,\''+avg+'\',\''+perpack+'\')" '+
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
//added by julz
function validateQty(id){
    var quantity = parseFloat($('perpack'+id).innerHTML);
    if(quantity != "" || quantity != "Nan"){
        if(quantity < 0 ){
            alert('Requested quantity exceed the available quantity.');
            return false;
        }else{
            return true;
        }
    }

}
//end