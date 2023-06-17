function clicky(){
    alert("yeah");
}

function clearIssue(list) {    
    if (!list) list = $('issue-list')
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

function appendIssuance(list, details, disabled) {
    if (!list) list = $('issue-list');
    if (list) {
        var dBody=list.getElementsByTagName("tbody")[0];
        
        if (dBody) {
            var src;
            var lastRowNum = null,
//                    refno = document.getElementsByName('refno[]');
                    dRows = dBody.getElementsByTagName("tr");
            if (details) {
                var refno = details.refno,
                    issdate = details.issdate,
                    srcarea = details.srcarea,
                    area = details.area,
                    authid = details.authid,
                    issid = details.issid;
                    
             if (refno) {       
                    if ($('rowrefno'+refno)) {
 
                        var itemRow = $('row'+refno),
                                itemQty = $('rowQty'+refno)
                        itemQty.value = parseFloatEx(itemQty.value) + parseFloatEx(details.qty)
                        itemQty.setAttribute('prevValue',itemQty.value)
                        qty = parseFloatEx(itemQty.value)
                        tot = netPrice*qty
                        $('rowrefno'+refno).value     =   details.refno
                        $('rowissdate'+refno).value    = details.issdate
                        $('rowsrcarea'+refno).value            = details.srcarea
                        $('rowarea'+refno).value        = details.area
                        $('rowauthid'+refno).value        = details.authid
                        $('rowissid'+refno).value        = details.issid
                        
                        return true                        
                    }
                    if (items.length == 0) clearIssue(list)
             }    

                alt = (dRows.length%2)+1         
                
                var disabledAttrib = disabled ? 'disabled="disabled"' : ""
                
                src = 
                    '<tr class="wardlistrow'+alt+'" id="row'+refno+'">' +
                    '<input type="hidden" name="issdate[]" id="rowissdate'+refno+'" value="'+details.issdate+'" />'+
                    '<input type="hidden" name="srcarea[]" id="rowsrcarea'+refno+'" value="'+details.srcarea+'" />'+
                    '<input type="hidden" name="area[]" id="rowarea'+refno+'" value="'+details.area+'" />'+
                    '<input type="hidden" name="authid[]" id="rowauthid'+refno+'" value="'+details.authid+'" />'+
                    '<input type="hidden" name="issid[]" id="rowissid'+refno+'" value="'+details.issid+'" />'+
                    '<input type="hidden" name="refno[]" id="rowrefno'+refno+'" value="'+details.refno+'" />';
                /*
                if (disabled)
                    src+='<td></td>'
                else
                    src+='<td class="centerAlign" width="5%"><img class="segSimulatedLink" src="../../images/close_small.gif" border="0" onclick="removeItem(\''+id+'\')"/></td>'
                */

                src+=
                    '<td>'+details.refno+'</td>'+
                    '<td ><span style="color:#660000">'+details.issdate+'</span></td>'+
                    '<td ><span style="color:#660000">'+details.srcarea+'</span></td>'+
                    '<td align="center"><span style="color:#660000">'+details.authid+'</span></td>'+
                    '<td align="center"><span style="color:#660000">'+details.issid+'</span></td></tr>';
                
                trayItems++;
            }
            else {
                src = "<tr><td colspan=\"8\">Issue list is currently empty...</td></tr>";    
            }
            dBody.innerHTML += src;
            return true;
        }
    }
    return false;
}
/*
function addProductToList(listID, details ) {
    // ,id, name, desc, cash, charge, cashsc, chargesc, d, soc
    var list=$(listID), dRows, dBody, rowSrc;
    var i,val;
    
    for( i = 0; i < document.forms['radioButtonForm'].elements['item_type'].length; i++ )
    {
        if(document.forms['radioButtonForm'].elements['item_type'][i].checked) {
            val = document.forms['radioButtonForm'].elements['item_type'][i].value;
        }
    }
    
    var filter = val;
    
    if (list) {
        dBody=list.getElementsByTagName("tbody")[0];
        dRows=dBody.getElementsByTagName("tr");

        // get the last row id and extract the current row no.
            
        if (typeof(details)=="object") {
            var id = details.id,
                name = details.name,
                desc = details.desc,
                pending = details.pending,
                unitid = details.unitid,
                perpc = details.perpc,
                d = details.d,
                unitdesc = details.unitdesc,
                soc = details.soc;
                
            var cashHTML, chargeHTML;
            var cashSeniorHTML, chargeSeniorHTML;
            var pugee,pangit;
            
            //alert("hi"); 
            
            //if (d>=0)
            //$('temporaryid').value = id;
            
            rowSrc = "<tr>"+
                                    '<td>'+
                                        '<span id="name'+id+'" style="font:bold 12px Arial;color:#000066">'+name+'</span><br />'+
                                        '<div style=""><div id="desc'+id+'" style="font:normal 11px Arial; color:#404040">'+desc+'</div></div>'+
                                    '</td>'+
                                    '<td align="center">'+
                                        '<input id="soc'+id+'" type="hidden" value="'+soc+'"/>'+
                                        '<span id="id'+id+'" style="font:bold 11px Arial;color:#660000">'+id+'</span></td>'+
                                         '<input id="idpo" type="hidden" value="'+id+'"/>'+
                                    '<td align="right" colspan="2">'+
                                        '<input id="pending'+id+'" type="hidden" value="'+pending+'"/>'+ 
                                        '<input id="unitid'+id+'" type="hidden" value="'+unitid+'"/>'+
                                        '<input id="unitdesc'+id+'" type="hidden" value="'+unitdesc+'"/>'+
                                        '<input id="perpc'+id+'" type="hidden" value="'+perpc+'"/><span style="color:#008000">'+pending+'</span></td>'+
                                    '<td colspan="2"></td>'+
                                    '<td>'+
                                        '<input type="button" id="packadd'+id+'" value="Pck" style="color:#000066; font-weight:bold; padding:0px 2px" '+
                                            //'onclick="prepareAdd(\''+id+'\')" '+
                                        '/>'+
                                    '</td>'+
                                    '<td>'+
                                        '<input type="button" id="pcadd'+id+'" value="Pc" style="color:#000066; font-weight:bold; padding:0px 2px" '+
                                            //'onclick="prepareAddPc(\''+id+'\')" '+
                                        '/>'+
                                    '</td>'+
                                '</tr>';
        }
    }
}
*/
