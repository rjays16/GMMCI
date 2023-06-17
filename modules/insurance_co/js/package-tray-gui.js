function formatNumber(num,dec) {
    var nf = new NumberFormat(num);
    if (isNaN(dec)) dec = nf.NO_ROUNDING;
    nf.setPlaces(dec);
    return nf.toFormatted();
}

function prepareAdd(id) {
    var details = new Object();
    details.id = $('id_'+id).value;
    details.name = $('name'+id).innerHTML;
    details.amtlimit= $('amtlimit'+id).value;
    var list = window.parent.document.getElementById('package-list');
    result = window.parent.appendPkg(list,details);
}

function enableButtonAdd(id){
    document.getElementById('add_pkg'+id).disabled=false;
}

function disableButtonAdd(id){
    document.getElementById('add_pkg'+id).disabled=true;    
}

function preset(){
    document.getElementById('benefit').value = window.parent.$('benefit_id').value;
    document.getElementById('search').focus();
//    document.getElementById('area').value = window.parent.$('area').value;
//    document.getElementById('serv_areas').focus();
}

function clearList(listID, bForce) {
    // Search for the source row table element
    var list=$(listID),dRows, dBody;
    if (list) {
        dBody=list.getElementsByTagName("tbody")[0];
        if (dBody) {
            
            if ($('search').value=="") {
                if (bForce) 
                    dBody.innerHTML = "";
                else
                    dBody.innerHTML = '<tr>'+
                                                 '<td colspan="3" style="font-weight:normal">No such package exists...</td>'+
                                             '</tr>';
            }else{
                dBody.innerHTML = "";
            }
            
            return true;    // success
        }
        else return false;    // fail
    }
    else return false;    // fail
}

function addPackageToList(listID, id, name, price) {
    var list=$(listID), dRows, dBody, rowSrc;
    var i;
    if (list) {
        dBody=list.getElementsByTagName("tbody")[0];
//        dRows=dBody.getElementsByTagName("tr");
        // get the last row id and extract the current row no.
        if (id) {
            rowSrc = "<tr>"+
                        '<td>'+
                            '<span id="name'+id+'" style="font:bold 12px Arial">'+name+'</span>'+
                            '<input type="hidden" id="id_'+id+'" name="id_'+id+'" value="'+id+'">'+ 
                        '</td>'+
                        '<td id="price'+id+'" style="font:normal 11px Arial; color:#003366;" align="right">'+price+
                        '</td>'+                        
                        '<td align="center"><input id="amtlimit'+id+'" type="text" style="width:100%; text-align:right" value="" onKeyUp="if ((this.value.length >= 1)&& !isNaN(this.value)) enableButtonAdd(\''+id+'\'); else disableButtonAdd(\''+id+'\');" style="text-align:right" onblur="this.value = isNaN(parseFloat(this.value))? \'\' : formatNumber(parseFloat(this.value),2);"/></td>'+
                        '<td>'+
                            '<input name="add_pkg'+id+'" id="add_pkg'+id+'" type="button" disabled value=">" style="color:#000066; font-weight:bold; padding:0px 2px" '+
                                'onclick="prepareAdd(\''+id+'\')" '+
                            '/>'+
                        '</td>'+
                    '</tr>';                
        }
        else {
            rowSrc = '<tr><td colspan="4" style="">No such package exists...</td></tr>';
        }
        dBody.innerHTML += rowSrc;
    }
}