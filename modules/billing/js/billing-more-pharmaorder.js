var currentPage=0, lastPage=0;
var FIRST_PAGE=1, PREV_PAGE=2, NEXT_PAGE=3, LAST_PAGE=4, SET_PAGE=0;

var AJAXTimerID=0;
var lastSearch="";

function display(str) {
    if($('ajax_display')) $('ajax_display').innerHTML = str.replace('\n','<br>');
}

function startAJAXSearch(searchID, page) {
    var searchEL = $(searchID);
    if (!page) page = 0;
    var last_page;

    if (true) {
        searchEL.style.color = "#0000ff";
        if (AJAXTimerID) clearTimeout(AJAXTimerID);
        $("ajax-loading").style.display = "";
        var script = "xajax_populateMedandSupplyList('"+searchID+"',"+page+",'"+searchEL.value+"')";
        AJAXTimerID = setTimeout(script,200);
        lastSearch = searchEL.value;
        lastSearchPage = page;
    }
}

function endAJAXSearch(searchID) {
    var searchEL = $(searchID);
    if (searchEL) {
        $("ajax-loading").style.display = "none";
        searchEL.style.color = "";
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

function checkEnter(e,searchID){
    //alert('e = '+e);    
    var characterCode; //literal character code will be stored in this variable

    if(e && e.which){ //if which property of event object is supported (NN4)
        e = e;
        characterCode = e.which; //character code is contained in NN4's which property
    }else{
        e = event;
        characterCode = e.keyCode; //character code is contained in IE's keyCode property
    }

    if(characterCode == 13){ //if generated character code is equal to ascii 13 (if enter key)
        startAJAXSearch(searchID,0);
    }else{
        return true;
    }        
}

function prepareAdd(code) {
    var details = new Object(), qty = 0;
        
    var enc = window.parent.document.getElementById('encounter_nr').value;
    var bill_dt = window.parent.document.getElementById('billdate').value;
    var area_code = document.getElementById('area_code').value;    
    
    if ((area_code == '-') || (area_code == ''))
        alert('You must select the pharmacy area!');  
    else {        
        details.code = code;
        details.uprice = $('uprice_'+code).value;

        while (isNaN(parseFloat(qty)) || parseFloat(qty)<=0) {
            qty = prompt("Enter quantity:")
            if (qty === null) return false;
        }        
        details.quantity = qty;            
    
        window.parent.addMoreMedorSupply(bill_dt, enc, details.code, area_code, details.uprice, details.quantity);
    }        
}

function addPharmaItemtoList(listID, details) {
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
                prodclass = details.prodclass,
                uprice = details.uprice,
                qty = details.qty;
                                            
            if (id)            
                rowSrc = "<tr>"+
                            '<td>'+
                                '<span id="name_'+id+'" style="font:bold 12px Arial;color:#000066">'+name+'</span><br />'+
                                '<div style=""><div id="desc_'+id+'" style="font:normal 11px Arial; color:#404040">'+desc+'</div></div>'+
                            '</td>'+
                            '<td align="center">'+
                                '<span id="id_'+id+'" style="font:bold 11px Arial;color:#660000">'+id+'</span></td>'+
                            '<td align="center">'+
                                '<span id="prodclass_'+id+'" style="font:bold 11px Arial;color:#660000">'+prodclass+'</span></td>'+
                            '<td align="right">'+
                                '<input style="text-align:right"'+( Number(uprice)-0 ? ' ' : ' ')+'name="uprice_'+id+'" id="uprice_'+id+'" type="text" size="15" maxlength="15"'+
                                ' onblur="trimString(this); chkDecimal(this,\''+id+'\');" onFocus="this.select();" value="'+formatNumber(Number(uprice),2)+'">'+
                            '</td>'+                                                                
                            '<td>'+                                
                                '<input type="button" value=">" style="color:#000066; font-weight:bold; padding:0px 2px" '+
                                    'onclick="prepareAdd(\''+id+'\')" '+
                                '/>'+
                            '</td>'+
                        '</tr>';
        }
        else {
            rowSrc = '<tr><td colspan="5" style="">No such drug, medicine or supply exists...</td></tr>';
        }
        dBody.innerHTML += rowSrc;
    }
}

function js_setOption(tagId, value){
    $(tagId).value = value;    
}// end of function js_setOption

function js_AddOptions(tagId, text, value){
    var elTarget = $(tagId);
    if(elTarget){
        var opt = new Option(text, value);
        //var opt = new Option(value, value);
        opt.id = value;
        elTarget.appendChild(opt);
    }
    var optionsList = elTarget.getElementsByTagName('OPTION');
}//end of function js_AddOption

function js_ClearOptions(tagId){
    var optionsList, el=$(tagId);
    if(el){
        optionsList = el.getElementsByTagName('OPTION');
        for(var i=optionsList.length-1; i >=0 ; i--){
            optionsList[i].parentNode.removeChild(optionsList[i]);    
        }
    }
}//end of function js_ClearOptions

function jsOptionChange(obj, value){
    //alert("tagid = " + obj.id + "value = " + value);    
    if(obj.id== 'area_combo'){
        $('area_code').value  = value;    
    }
}

function getPharma_Areas() {
    xajax_getPharma_Areas();    
}
