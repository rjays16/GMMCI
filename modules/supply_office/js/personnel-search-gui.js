var currentPage=0, lastPage=0;
var FIRST_PAGE=1, PREV_PAGE=2, NEXT_PAGE=3, LAST_PAGE=4, SET_PAGE=0;

function formatNumber(num,dec) {
    var nf = new NumberFormat(num);
    if (isNaN(dec)) dec = nf.NO_ROUNDING;
    nf.setPlaces(dec);
    return nf.toFormatted();
}

function display(str) {
    document.write(str);
}

function prepareSelect(id) {
 
    var id = $('id'+id).innerHTML;
    var lname = $('lname'+id).innerHTML;
    var fname = $('fname'+id).innerHTML;  
    

        window.parent.$('authorizing_id').value = fname + " " + lname;
        window.parent.$('authorizing_id').readOnly = true;
        window.parent.$('authorizing_id_hidden').value = id;   

    if (id) {
        if (window.parent.pSearchClose) window.parent.pSearchClose();
        else if (window.parent.cClick) window.parent.cClick();
    }
    else {
        if (window.parent.cClick) window.parent.cClick();
    }
}

function prepareSelect2(id) {
 
    var id = $('id'+id).innerHTML;
    var lname = $('lname'+id).innerHTML;
    var fname = $('fname'+id).innerHTML;  
    
    
        window.parent.$('issuing_id').value = fname + " " + lname;
        window.parent.$('issuing_id').readOnly = true;   
        window.parent.$('issuing_id_hidden').value = id;

    if (id) {
        if (window.parent.pSearchClose) window.parent.pSearchClose();
        else if (window.parent.cClick) window.parent.cClick();
    }
    else {
        if (window.parent.cClick) window.parent.cClick();
    }
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
    //$("pageShow").innerHTML = '<span>Showing '+(firstRec)+'-'+(lastRec)+' out of '+(parseInt(total))+' record(s)</span>';
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

function addPerson(listID, details) {
    var list=$(listID), dRows, dBody, rowSrc;
    var i

    var id=details.pid, 
            lname=details.lname, 
            fname=details.fname,
            sex=details.sex,
            edate=details.edate;

    if (list) {
        dBody=list.getElementsByTagName("tbody")[0];
        dRows=dBody.getElementsByTagName("tr");
        // get the last row id and extract the current row no.
        if (id) {
            if (sex=='m')
                sexImg = '<img src="../../gui/img/common/default/spm.gif" border="0" />';
            else if (sex=='f')
                sexImg = '<img src="../../gui/img/common/default/spf.gif" border="0" />';
            else
                sexImg = '';            

            rowSrc = '<tr>'+
                                    '<td>'+
                                        '<span id="id'+id+'" style="color:#660000">'+id+'</span>'+
                                    '</td>'+
                                    '<td>'+sexImg+'</td>'+
                                    '<td align="center"><span id="lname'+id+'">'+lname+'</span></td>'+
                                    '<td align="center"><span id="fname'+id+'">'+fname+'</span></td>'+
                                    '<td align="center"><span id="edate'+id+'">'+edate+'</span></td>'+
                                    '<td>'+
                                        '<input type="button" value="Select" style="color:#000066; font-weight:bold; padding:0px 2px" '+
                                            'onclick="prepareSelect(\''+id+'\')" '+
                                        '/>'+
                                    '</td>'+
                                '</tr>';
        }
        else {
            if (!details.error) details.error = 'No such person exists...';
            

                                
            rowSrc = '<tr><td colspan="9" style="">'+details.error+'</td></tr>';
        }
        dBody.innerHTML += rowSrc;
    }
}

function addPerson2(listID, details) {
    var list=$(listID), dRows, dBody, rowSrc;
    var i

    var id=details.pid, 
            lname=details.lname, 
            fname=details.fname,
            sex=details.sex,
            edate=details.edate;

    if (list) {
        dBody=list.getElementsByTagName("tbody")[0];
        dRows=dBody.getElementsByTagName("tr");
        // get the last row id and extract the current row no.
        if (id) {
            if (sex=='m')
                sexImg = '<img src="../../gui/img/common/default/spm.gif" border="0" />';
            else if (sex=='f')
                sexImg = '<img src="../../gui/img/common/default/spf.gif" border="0" />';
            else
                sexImg = '';            

            rowSrc = '<tr>'+
                                    '<td>'+
                                        '<span id="id'+id+'" style="color:#660000">'+id+'</span>'+
                                    '</td>'+
                                    '<td>'+sexImg+'</td>'+
                                    '<td align="center"><span id="lname'+id+'">'+lname+'</span></td>'+
                                    '<td align="center"><span id="fname'+id+'">'+fname+'</span></td>'+
                                    '<td align="center"><span id="edate'+id+'">'+edate+'</span></td>'+
                                    '<td>'+
                                        '<input type="button" value="Select" style="color:#000066; font-weight:bold; padding:0px 2px" '+
                                            'onclick="prepareSelect2(\''+id+'\')" '+
                                        '/>'+
                                    '</td>'+
                                '</tr>';
        }
        else {
            if (!details.error) details.error = 'No such person exists...';
            

                                
            rowSrc = '<tr><td colspan="9" style="">'+details.error+'</td></tr>';
        }
        dBody.innerHTML += rowSrc;
    }
}
