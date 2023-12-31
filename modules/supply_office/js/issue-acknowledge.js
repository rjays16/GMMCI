function clicky(msg){
    alert(msg);
}

function parseFloatEx(x) {
    var str = x.toString().replace(/\,|\s/,'')          
    return parseFloat(str)
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

function appendTheIssuanceList(list, details, disabled) {
    if (!list) list = $('issue-list');
    if (list) {      
        var dBody=list.getElementsByTagName("tbody")[0];
        
        if (dBody) {
            var src;
            var lastRowNum = null,
                    refno = document.getElementsByName('refno[]'),
                    dRows = dBody.getElementsByTagName("tr");
            if (details) {
            
                var trayItems = 0,
                    refno = details.refno,
                    issdate = details.issdate,
                    srcarea = details.srcarea,
                    area = details.area,
                    authid = details.authid,
                    issid = details.issid;
                    
                var srcareaname = details.srcareaname;
                var areaname = details.areaname;
                var authidname = details.authidname;
                var issidname = details.issidname;    
                 
                 if (refno) {       
                        if ($('rowrefno'+refno)) {
     
                            $('rowrefno'+refno).value     =   details.refno
                            $('rowissdate'+refno).value    = details.issdate
                            $('rowsrcarea'+refno).value            = details.srcarea
                            $('rowarea'+refno).value        = details.area
                            $('rowauthid'+refno).value        = details.authid
                            $('rowissid'+refno).value        = details.issid
                                                   
                            return true
                        }
                        if (refno.length == 0) clearIssue(list)
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
                    '<input type="hidden" name="srcareaname[]" id="rowsrcareaname'+refno+'" value="'+details.srcareaname+'" />'+
                    '<input type="hidden" name="areaname[]" id="rowareaname'+refno+'" value="'+details.areaname+'" />'+
                    '<input type="hidden" name="authidname[]" id="rowauthidname'+refno+'" value="'+details.authidname+'" />'+
                    '<input type="hidden" name="issidname[]" id="rowissidname'+refno+'" value="'+details.issidname+'" />'+
                    '<input type="hidden" name="refno[]" id="rowrefno'+refno+'" value="'+details.refno+'" />';
                

                src+=
                    '<td align="center">'+details.refno+'</td>'+
                    '<td align="center"><span style="color:#660000">'+details.issdate+'</span></td>'+
                    '<td align="center"><span style="color:#660000">'+details.srcareaname+'</span></td>'+
                    '<td align="center"><span style="color:#660000">'+details.areaname+'</span></td>'+
                    '<td align="center"><span style="color:#660000">'+details.authidname+'</span></td>'+
                    '<td align="center"><span style="color:#660000">'+details.issidname+'</span></td>'+
                    //'<td align="center"><span style="color:#660000">'+details.+'</span></td>'+
                    
                    '<td align="center">'+
                        '<input type="button" id="button'+refno+'" value="Details" style="color:#000066; font-weight:bold; padding:0px 2px" '+
                            //'onclick="prepareAddPc(\''+id+'\')" '+
                            'onclick="openIssueDetails(\''+refno+'\',\''+area+'\',\''+srcarea+'\')" '+
                        '/>'+
                    '</td>'+
                    
                    '</tr>';
                
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


function appendTheIssuanceDetailsList(list, details, disabled) {
    if (!list) list = $('issue-list');
    if (list) {
        var dBody=list.getElementsByTagName("tbody")[0];

        if (dBody) {
            var src;
            var lastRowNum = null,
                    itemcode = document.getElementsByName('itemcode[]'),
                    dRows = dBody.getElementsByTagName("tr");
            var rowCount = $$('input[type="radio"]').length / 2;
            if (details) {
            

                    var itemcode = details.itemcode,
                    qty = details.qty,
                    
                    unitid = details.unitid,
                    perpc = details.perpc,
                    serial = details.serial,
                    expiry = details.expiry;
                
                 var itemname = details.itemname;
                 var unitname = details.unitname;
                     
                    
                 if (itemcode) {       
                        if ($('rowitemcode'+itemcode)) {
     
                            $('rowitemcode'+itemcode).value     =   details.itemcode
                            $('rowqty'+itemcode).value    = details.qty
                            
                            $('rowunitid'+itemcode).value            = details.unitid
                            $('rowperpc'+itemcode).value        = details.perpc
                            $('rowserial'+itemcode).value        = details.serial
                            $('rowexpiry'+itemcode).value        = details.expiry
                                                   
                            return true
                        }
                        if (itemcode.length == 0) clearIssue(list)
                 }    

                alt = (dRows.length%2)+1         
                
                var disabledAttrib = disabled ? 'disabled="disabled"' : "";
                
                src = 
                    '<tr class="isslistrow'+alt+'" id="row'+itemcode+'">' +
                    '<input type="hidden" name="qty[]" id="rowqty'+itemcode+'" value="'+details.qty+'" />'+
                    '<input type="hidden" name="unitid[]" id="rowunitid'+itemcode+'" value="'+details.unitid+'" />'+
                    '<input type="hidden" name="perpc[]" id="rowperpc'+itemcode+'" value="'+details.perpc+'" />'+
                    '<input type="hidden" name="serial[]" id="rowserial'+itemcode+'" value="'+details.serial+'" />'+
                    '<input type="hidden" name="expiry[]" id="rowexpiry'+itemcode+'" value="'+details.expiry+'" />'+
                    '<input type="hidden" name="itemname[]" id="rowitemname'+itemcode+'" value="'+details.itemname+'" />'+
                    '<input type="hidden" name="unitname[]" id="rowunitname'+itemcode+'" value="'+details.unitname+'" />'+
                    '<input type="hidden" name="itemcode[]" id="rowitemcode'+itemcode+'" value="'+details.itemcode+'" />';
                

                src+=
                    '<td align="left">'+details.itemcode+'</td>'+ 
                    
                    '<td align="left">'+details.itemname+'</td>'+
                    '<td align="center"><span style="color:#660000"><input class="segInput" type="text" name="editqty[]" id="editrowqty'+itemcode+'" itemID="'+itemcode+'" value="'+details.qty+'" prevValue="'+details.qty+'" style="width:80%;text-align:center" onblur="adjustQty(this);" onkeyup="editQuantity(\''+itemcode+'\')"/></span></td>'+
                    '<td align="center"><span style="color:#660000">'+details.unitname+'</span></td>'+
                    //'<td align="center"><span style="color:#660000">'+details.perpc+'</span></td>'+
                    '<td align="center"><span style="color:#660000">'+details.serial+'</span></td>'+
                    '<td align="center"><span style="color:#660000">'+details.expiry+'</span></td>'+
                    '<td align="center">'+
                        '<input type="radio" id="check'+itemcode+'" name="acknowledge[' +rowCount +']" value="approve" style="color:#000066; font-weight:bold; padding:0px 2px" '+
                            
                        '/>'+
                    '</td>'+

                    '<td align="center">'+
                        '<input type="radio" id="cancel'+itemcode+'" name="acknowledge[' + rowCount+ ']" value="cancel" style="color:#000066; font-weight:bold; padding:0px 2px" '+
                        '/>'+
                    '</td>'+
                    '</tr>';

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

function selectAll(val){
    var radioButtons = document.getElementsByTagName("input");
    
    for(i=0; i<radioButtons.length; i++){
        if(radioButtons[i].type == "radio" && radioButtons[i].value == val){
            radioButtons[i].checked = true;
        }
    }
    
    return false;
}

function checkingBox(itemcode,value){

    alert(itemcode);
    
    if(value=="approve"){
        alert(value);
        //document.forms['"form"+itemcode'].check[1].checked=false; 
    }
    else {
        //document.forms[itemcode].check[0].checked=false; 
    }
    return true;
    
}

// added by Sarah 9/28/2015
function editQuantity(id) {
   
   var newqty = parseFloat($('editrowqty'+id).value);
   if (!isNaN(newqty) && newqty!=null){
        document.getElementById("editrowqty"+id).setAttribute("value",newqty);
   }else{
        document.getElementById("editrowqty"+id).setAttribute("value",0);    
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
        alert('Unable to acknowledge negative quantity');
        obj.setAttribute("value", obj.getAttribute("prevValue"));
        obj.value = obj.getAttribute("prevValue");
        return false;
    }

    if (parseFloatEx(obj.value) != parseFloatEx(obj.getAttribute("prevValue"))) {
        obj.setAttribute("prevValue",parseFloatEx(obj.value));
        $('rowqty'+id).value = parseFloatEx(obj.value);
        // $('tot_'+id).value = parseFloatEx($('avg_'+id).value)*parseFloatEx(obj.value);
        // $('tot_disp'+id).innerHTML = formatNumber(parseFloatEx($('avg_'+id).value)*parseFloatEx(obj.value),2);
        // refreshDiscount();
    }
    
    //obj.value = formatNumber(obj.value,2);
    return true;
}

// end Sarah 9/28/2015