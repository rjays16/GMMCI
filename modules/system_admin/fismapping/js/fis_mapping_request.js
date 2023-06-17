function initialize()
{

    ListGen.create($('request-list'),{
        id: 'requests',
        url: 'ajax/ajax_fis_request_list.php',
        params: {
                'cost_center':$('service_area').value,
                 'search_name':$('name').value,
                 'transctioncode':$('accountTransaction').value
                },
        width: 'auto',
        height: 'auto',
        rowHeight: 20,
        effects: true,
        autoload: true,
        layout: [
            //['<h1>List of Requests</h1>'],
            ['#pagestat', '#first', '#prev', '#next', '#last', '#refresh'],
            ['#thead'],
            ['#tbody']
        ],
        columnModel: [
            {
                name: 'service_code',
                label: 'Item Code',
                width: 80,
                sortable: false,
                sorting: ListGen.SORTING.asc,
                styles:{
                    font: 'Tahoma',
                    fontSize: '11'
                }
            },
            {
                name: 'item_name',
                label: 'Item Name',
                width: 120,
                sortable: false,
                sorting: ListGen.SORTING.none,
                styles:{
                    font: 'Tahoma',
                    fontSize: '11'
                }
            },
             {
                name: 'group_name',
                label: 'Item Group',
                width: 120,
                sortable: false,
                sorting: ListGen.SORTING.none,
                styles: {
                    textAlign: 'center',
                    font: 'Tahoma',
                    fontSize: '11'
                }
            },
            {
                name: 'setup',
                label: 'Setup',
                width: 80,
                sortable: false,
                sorting: ListGen.SORTING.none,
                styles: {
                    textAlign: 'center',
                    color: '#660000',
                    font: 'Tahoma',
                    fontSize: '11',
                    fontWeight: 'bold'
                }
            },
            {
                name: 'options',
                label: 'Options',
                width: 80,
                styles: {
                    textAlign: 'center'
                }
                
            }
        ]
    });
}

function checklist(){
    var TransactionCode = $('accountTransaction').value;
    xajax_CheckIfHasAreas(TransactionCode, 'Search');    
}

function listRequests()
{
    $('request-list').list.params = {
            'cost_center':$('service_area').value,
            'search_name':$('name').value,
            'search_pid':$('pid').value,
            'search_encounter':$('encounter_nr').value,
            'transctioncode':$('accountTransaction').value
    };
    $('request-list').list.refresh();
}

function changePatientOptions(val)
{
    switch(val)
    {
        case 'p_name':
            $(val).style.display="";
            $('p_pid').style.display="none";
            $('p_enc').style.display="none";
            break;
        case 'p_pid':
            $(val).style.display="";
            $('p_name').style.display="none";
            $('p_enc').style.display="none";
            break;
        case 'p_enc':
            $(val).style.display="";
            $('p_pid').style.display="none";
            $('p_name').style.display="none";
            break;
    }
    $('name').value="";
    $('pid').value="";
    $('encounter_nr').value="";
}


function cancelFlag(area, refno, item_code, flag)
{
    if(flag=="") {
        alert("No request flag");
        return false;
    }
    var rep = confirm("Cancel charge type of this request item?")
    if(rep) {
        var reason = prompt("Enter reason for cancellation.")
        if(reason) {
            xajax_cancelRequestFlag(area, refno, item_code, reason);
        } else {
            alert("Invalid reason");
            return false;
        }
    }
}

function cancelStatus(area, refno, item_code)
{
    var rep = confirm("Change status of this request item to pending?")
    if(rep) {
        var reason = prompt("Enter reason for cancellation.")
        if(reason) {
            xajax_cancelStatus(area, refno, item_code, reason);
        } else {
            alert("Invalid reason");
            return false;
        }
    }
}



function deleteRequest(area, refno, item_code)
{
    var rep = confirm("Delete this request item?")
    if(rep) {
        if(area.toLowerCase()=='ph' || area.toLowerCase()=='ot') {
            xajax_deleteRequestItem(area, refno, item_code, reason);
            return false;
        }
        var reason = prompt("Enter reason for deletion.")
        if(reason) {
            xajax_deleteRequestItem(area, refno, item_code, reason);
        } else {
            alert("Invalid reason");
            return false;
        }
    }
}

function alertFlag(rep)
{
    alert(rep)
    return false;
}

function checkfunction(item, Trans, area, id, code_area){
    
        return overlib(
                 OLiframeContent('seg_fis_mapping_update.php?sid=<?echo $sid?>&itemcode='+item+'&acctype='+Trans+'&area='+area+'&set='+id+'&TranCode='+code_area, 800, 400, 'fGroupTray', 1, 'auto'),
                                    WIDTH,800, TEXTPADDING,0, BORDER,0,
                                    STICKY, SCROLL, CLOSECLICK, MODAL,
                                    CLOSETEXT, 
                                    '<img src=../../../images/close.gif border=0 ;">',
                                         CAPTIONPADDING,4, CAPTION,'Create Update Account',
                                         MIDX,0, MIDY,0,
                                         STATUS,' Create Update Account');
     
}

function addaccount()
{
 var TransactionCode = $('accountTransaction').value;
    return overlib(
                 OLiframeContent('seg_fis_mapping_update.php?sid=<?echo $sid?>&itemcode='+TransactionCode+'&acctype='+TransactionCode, 800, 400, 'fGroupTray', 1, 'auto'),
                                    WIDTH,800, TEXTPADDING,0, BORDER,0,
                                    STICKY, SCROLL, CLOSECLICK, MODAL,
                                    CLOSETEXT, 
                                    '<img src=../../../images/close.gif border=0 ;">',
                                         CAPTIONPADDING,4, CAPTION,'Create Update Account',
                                         MIDX,0, MIDY,0,
                                         STATUS,' Create Update Account');
}

function ChangeTransaction(){
    var TransactionCode = $('accountTransaction').value;
    xajax_CheckIfHasAreas(TransactionCode, "Transaction");
}

function DisableCostCenterArea(Area){
    if(Area[0][2] == 0){
        $('service_area').disabled = true;
        $('service_area').value = 0;
        
    }else{
        $('service_area').disabled = false;
    }

    if(Area[0][4] == 1){
        $('search').style.display = "";
        $('AddAccount').style.display = "none";
    }else{
        $('search').style.display ="none";
        $('AddAccount').style.display = "";
    }
    
}

function CheckSearchTransaction(Area){
    if(Area == 0){
        listRequests();    
    }else{
        if($('service_area').value != 0){
            listRequests();
        }else{
            alert("Please Select Area!");
             $('request-list').list.params = {
                'cost_center':0,
                'search_name':'',
                'search_pid':0,
                'search_encounter':0,
                'transctioncode':$('accountTransaction').value
            };
            $('request-list').list.refresh();
        }
    }

}


document.observe('dom:loaded', initialize);