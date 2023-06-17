 var HEMO = '90935';
 var CHEMO = 'CHEMOTHERAPY';
 var DEB = 'DEBRIDEMENT';
// added by art 02/21/15
function GetURLParameter(sParam){
    var sPageURL = window.location.search.substring(1);
    var sURLVariables = sPageURL.split('&');
    for (var i = 0; i < sURLVariables.length; i++)
    {
        var sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] == sParam)
        {
            return sParameterName[1];
        }
    } 
}


var caserate1 = GetURLParameter('caserate1');
var caserate2 = GetURLParameter('caserate2');
var finalbill = GetURLParameter('finalbill');
//end art

jQuery(function($) {

        $j('#btnAddIcpCode').click(function() {

            var icpCode = $j('#icpCode').val();
            var icdDesc = $j('#icpDesc').val();

            // if(icpCode== HEMO || icdDesc.toUpperCase().indexOf(CHEMO)>=0){
            //     while (cnt) {
            //     }
            //     while (isNaN(parseFloat(cnt)) || parseFloat(cnt)<=0) {
            //         var cnt = prompt('Enter number of Sessions:');
            //         if (cnt === null) return false;
            //     }
            //     //$j("#is_special").val(1);
            //     $j("#num_sess").val(((icpCode==HEMO)?cnt:1));
            //     generateOpsDate(cnt);
            // } else if(icdDesc.toUpperCase().indexOf(DEB) >=0 && $j("#is_special").val()==1) {
            //     while (cnt) {
            //     }
            //     while (isNaN(parseFloat(cnt)) || parseFloat(cnt)<=0) {
            //         var cnt = prompt('Enter number of Operation:');
            //         if (cnt === null) return false;
            //     }

            //     $j("#num_sess").val(cnt);
            //     generateOpsDate(cnt);
            // } else {
            //     $j("#num_sess").val(1);
            //     generateOpsDate(1);
            // }

            $j("#num_sess").val(1);
                generateOpsDate(1);

            if(!!icpCode){
                if(!!icdDesc){

                // Added by James 1/6/2014
                var code = $j('#icpCode').val();

                for (var key in globalcode) {

                    //added by Nick 05-07-2014
                    var hasDeb = (icdDesc.toUpperCase().indexOf(DEB) >= 0) ? true:false ;
                    var hasChemo = (icdDesc.toUpperCase().indexOf(CHEMO) >= 0) ? true:false ;
                    var hasHemo = (icpCode == HEMO) ? true:false;

                    if (globalcode.hasOwnProperty(key)){
                        if (globalcode[key] == code){
                            if(hasHemo || hasDeb || hasChemo){//added by Nick 05-07-2014 - allow multiple special procedures
                                continue;
                            }else{
                                alert("Procedure already added.  " + hasDeb + "  " + hasChemo);
                            return;
                        }
                } 
                    }
                } 

                    $j( "#opDateBox").dialog({
                        autoOpen: true,
                        modal:true,
                        height: "auto",
                        width: "auto",
                        resizable: false,
                        show: "fade",
                        hide: "explode",
                        title: "Date of Operation",
                        position: "top", 
                        buttons: {
                            "Save": function() 
                            {
                                // Edited by James 1/6/2014
                                if($("#laterality_option").val() == 0 && $("#laterality").val() == 1){
                                    alert("Please select a laterality!");
                                    return;
                                }else if($("#laterality").val() == 0){
                                    chkDate();
                                    return;
                                }
                                chkDate();
                            },
                            "Cancel": function() 
                            {
                                $( this ).dialog( "close" );
                            }
                        },
                        open: function(){
                            $j('.ui-button').focus();   
                            $j.each($j('#opDate-body :input').serializeArray(), function(i, field){ 
                                $j( '#'+field.name ).datepicker({
                                    dateFormat: 'yy-mm-dd',
                                    maxDate: 0
                                });
                            });
                        }
                    });
                    
                  
                }else{
                    alert("Please indicate procedure description.");
                }
            }else{
                alert("Please indicate procedure code.");
            }
            
            return false;
        });

        if ($j( "#icdCode" )){
            $j( "#icdCode" ).autocomplete({
                minLength: 2,
                source: function( request, response ) {
                    $j.getJSON( "ajax/ajax_ICD10.php?iscode=true", request, function( data, status, xhr ) {
                        response( data );
                    });
                },
                select: function( event, ui ) {
                    // alert(ui.item.label);
                    $j("#icdCode").val(ui.item.id);
                    $j("#icdDesc").val(ui.item.description);              
                }
            });
        }

        if ($j( "#icdDesc" )){
            $j( "#icdDesc" ).autocomplete({
                minLength: 2,
                source: function( request, response ) {
                    $j.getJSON( "ajax/ajax_ICD10.php?iscode=false", request, function( data, status, xhr ) {
                        response( data );
                    });
                },
                select: function( event, ui ) {
                    // alert(ui.item.label);
                    $j("#icdCode").val(ui.item.id);
                    $j("#icdDesc").val(ui.item.description);              
                }
            });            
        }

        if ($j( "#icpCode" )){
            $j( "#icpCode" ).autocomplete({
                minLength: 2,
                source: function( request, response ) {
                    $j.getJSON( "ajax/ajax_ICPM.php?iscode=true", request, function( data, status, xhr ) {
                        response( data );
                    });
                },
                select: function( event, ui ) {
                    $j("#icpCode").val(ui.item.id);
                    $j("#icpDesc").val(ui.item.description);
                    $j("#rvu").val(ui.item.rvu);
                    $j("#multiplier").val(ui.item.multiplier); 
                    $j("#laterality").val(ui.item.laterality);
                    $j("#is_special").val(ui.item.special_case);
                }
            });
        }

        if ($j( "#icpDesc" )){
            $j( "#icpDesc" ).autocomplete({
                minLength: 2,
                source: function( request, response ) {
                    $j.getJSON( "ajax/ajax_ICPM.php?iscode=false", request, function( data, status, xhr ) {
                        response( data );
                    });
                },
                select: function( event, ui ) {
                    $j("#icpCode").val(ui.item.id);
                    $j("#icpDesc").val(ui.item.description);
                    $j("#rvu").val(ui.item.rvu);
                    $j("#multiplier").val(ui.item.multiplier);
                    $j("#laterality").val(ui.item.laterality);
                    $j("#is_special").val(ui.item.special_case);
                }
            });
        }

        $j('#icdCode').click(function(){
            $j("#icdDesc").val("");
            $j('#icdCode').focus();    
        });

        $j('#icdDesc').click(function(){
            $j("#icdCode").val("");
            $j('#icdDesc').focus();    
        });

        $j('#icpCode').click(function(){
            $j("#icpDesc").val("");
            $j('#icpCode').focus();    
        });

        $j('#icpDesc').click(function(){
            $j("#icpCode").val("");
            $j('#icpDesc').focus();    
        });
    });

var globalcode = {};

function addProcedure(opDate,special_dates) {

    var details = new Object();
    var mul = $j('#multiplier').val();

    details.encNr = $j('#encounter_nr').val();
    details.bDate = $j('#billdate').val();
    details.code = $j('#icpCode').val();
    details.desc = $j('#icpDesc').val();
    details.opDate = opDate;
    details.user = $j('#create_id').val();
    details.multiplier = parseInt(mul);
    details.rvu = $j('#rvu').val();
    details.charge = details.multiplier * details.rvu;
    details.laterality =  $j("#laterality_option").val();
    details.sess_num = $j("#num_sess").val();
    details.special_dates = special_dates;
    details.icpDesc = $j('#icdDesc').val();
    xajax_addProcedure(details);

}

function chkDate(){
    var checker = true;
    var special_dates = '';
    $j.each($j('#opDate-body :input').serializeArray(), function(i, field){ 
        if(field.value==''){
            checker = false;
        }else{
            if(i==0){
                opDate = field.value
            }
            if($j("#is_special").val()==1){
                special_dates += field.value+',';
            }
        }
    });

    if(checker){
        $j( "#opDateBox").dialog( "close" );
        addProcedure(opDate,special_dates);
    }else{
        alert("Please enter a valid date!");
    }    

}

//added by Nick, 3/4/2014
function edit_icp(code){
    $('icp_desc_input'+code).style.display = '';
    $('description'+code).style.display = 'none';
    $('icp_desc_input'+code).focus();
}

function cancel_icp(code,pDesc){
    $('description'+code).style.display = '';
    $('icp_desc_input'+code).style.display = 'none';
}

function updateIcpAltDesc(e, code, codeOnly){
    var characterCode;
    var enc_nr   = $('encounter_nr').value;
    var user_id  = $('create_id').value;

    if (e) {
        if(e && e.which) {
            characterCode = e.which;
        }
        else {
            characterCode = e.keyCode;
        }
    }
    else
        characterCode = 13;

    if ( (characterCode == 13) || (isESCPressed(e)) ) {
        var refno = $('icp_refno'+code).value;
        var desc = $('icp_desc_input'+code).value;
        xajax_updateIcpDesc(refno,codeOnly,desc);
        $('description'+code).innerHTML = '<a id="description'+code+'" style="font:bold 12px Arial" onclick="edit_icp('+code+')">'+desc+'</a>';
        $('description'+code).style.display = '';
        $('icp_desc_input'+code).style.display = 'none';
    }
}
//end nick

//added by Nick 05-07-2014
function incrementOpCount(code){
    elem = $j("#"+code);
    if(typeof elem.html() != 'undefined'){
        opCount = $j('#'+code+' td:nth-child(3)').html();
        opCount++;
        $j('#'+code+' td:nth-child(3)').html(opCount);
        return true;
    }else{
        return false;
    }
}

function addProcedureToList(data,isFromAdd='',in_case_rate_list=array()) {
    var rowSrc;
    var isDelivery; //added  by art 02/03/15 for spmc145
    var elTarget = '#'+data.target;
    var code = data.code;
    var encounter_nr = $j('#encounter_nr').val();
    globalcode[code] = code; // Addded by James 1/6/2014
    var filter_case = '';
    for (var i = in_case_rate_list.length - 1; i >= 0; i--) {
        if (in_case_rate_list[i] == data.code) {
           filter_case = 'visibility: hidden;';
        }
    }
    
    //added by Nick 05-07-2014 - for multiple special procedures
    // if(isFromAdd){
    //     if(incrementOpCount(code)){
    //         return;
    //     }
    // }

    if (data) {
        new_code = "'"+data.code +"'";
isDelivery = data.isDelivery == true ? '<img src="../../images/btn_edit_small.gif" style="border-right:hidden; cursor:pointer; margin-right:5px;" onclick="editLMP('+encounter_nr+','+new_code+','+data.opRefno+');" title="View/Edit">' : ''; //added by art 02/07/15
        opEntry = "'"+data.opEntry+"'";
        var passcode = data.code+''+data.opEntry+''+data.opRefno;
        var passcode2 = "'"+data.code+''+data.opEntry+''+data.opRefno+"'";
        rowSrc = '<tr id='+data.code+''+data.opEntry+'>'+
                        '<td>'+
                            '<span style="font:bold 12px Arial;color:#660000">'+data.code+'</span>'+
                        '</td>'+
                        '<td onclick="edit_icp('+passcode2+')">'+
                            '<input id="icp_refno'+passcode+'" type="hidden" value="'+data.opRefno+'" />'+
                            '<input id="icp_desc_input'+passcode+'" style="font:bold 12px Arial; display:none; width:100%;" value="'+data.opDesc+'" onblur="cancel_icp('+passcode2+')" onFocus="this.select();" onkeyup="updateIcpAltDesc(event,'+passcode2+','+data.code+')">'+ // //added by Nick, 3/4/2014
                            '<a id="description'+passcode+'" style="font:bold 12px Arial" >'+data.opDesc+'</a><br/>'+
                            '<input id="description'+passcode+'" type="hidden" value="'+data.opDesc+'" />'+
                        '</td>'+
                        '<td align="center">'+data.opCount+'</td>'+
                        '<td align="center">'+data.opDate+'</td>'+
                        '<td align="center">'+'<input id="rvu'+data.code+'" type="hidden" value="'+data.opRVU+'">'+data.opRVU+'</td>'+
                        '<td align="center">'+'<input id="multiplier'+data.code+'" type="hidden" value="'+data.opMultiplier+'">'+data.opMultiplier+'</td>'+
                        '<td align="right">'+'<input id="charge'+data.charge+'" type="hidden" value="'+data.charge+'">'+data.charge+'</td>'+
                        // '<td align="center"><img src="../../images/btn_delitem.gif" style="border-right:hidden; cursor:pointer" onclick="xajax_delICP(\''+id+'\')" ></td></tr>';
                        '<td align="center">'+isDelivery+'<img src="../../images/btn_delitem.gif" style="border-right:hidden; cursor:pointer;'+filter_case+'" onclick="prepDelProc('+new_code+','+opEntry+', '+data.opRefno+');"></td>'+
                 '</tr>';
    }
    else {
        rowSrc = '<tr><td colspan="9" style="">No procedure encoded yet ...</td></tr>';
    }

    $j( elTarget ).prepend( rowSrc );
    clearICPFields();

}

function clearProcList(){
    $j('#ProcedureList-body').empty();
}

function rmvProcRow(id, opEntry, refno){
    // $j('#'+id).remove();
    //added by Nick 05-07-2014
    var enc_nr = $j('#encounter_nr').val();

    var description = $('description'+id+''+opEntry+''+refno).innerHTML;
    var encdr = $j('#create_id').val();
    var code = id;

    // opCount = $j('#'+id+''+opEntry+' td:nth-child(3)').html();
    // if(opCount>1){
    //     opCount--;
    //     $j('#'+id+''+opEntry+' td:nth-child(3)').html(opCount);
    // }else{
        $j('#'+code+''+opEntry).remove();
    // }

    //end nick
    alert("Procedure successfully deleted!");
}

function prepDelProc(code,opEntry, refno){

    var details = new Object();
    var msg = '';
    details.enc = $j('#encounter_nr').val();
    details.bdate = $j('#billdate').val();
    details.fdate = $j('#admission_dt').val();
    details.code = code;
    details.opEntry = opEntry;
    details.refno = refno;

    var description = $('description'+code+''+opEntry+''+refno).innerHTML;
    var encdr = $j('#create_id').val();
    // added by art 02/21/15
    if ((finalbill == 1) && (code == caserate1 || code == caserate2)) {
        msg = code == caserate1 ? 'First caserate' : 'Second caserate';
        alert('Delete Failed! Code is used in '+msg);
    }else{
    for (var key in globalcode) {
        if (globalcode.hasOwnProperty(key))
            if (globalcode[key] == code){
                delete globalcode[key];
            return;
        }
    }

    xajax_deleteProcedure(details);
}

}

function clearICPFields() {
    $j('#icpCode').val("");
    $j('#icpDesc').val("");
}


function generateOpsDate(cnt)
{   
    var rowSrc = '';
    var elTarget = '#opDate-body';
    $j('#opDate-body').empty();
    for (var i=0;i<cnt;i++) {
        rowSrc +='<tr id="opDateBox-date-'+i+'">'+
                    '<td width="*" align="left">'+                            
                    '    <strong> Date '+((cnt>1)? parseInt(i+1) : '')+'</strong>'+
                    '</td>'+
                    '<td width="*" align="left">'+  
                    '    <input type="text" id="op_date_'+i+'" name="op_date_'+i+'" maxlength="10" size="10" />'+
                    '</td>'+
                '</tr>';

        if($j("#laterality").val() == 1){
            rowSrc +=   '<tr id="opDateBox-laterality">'+
                        '    <td width="*" align="left">'+                            
                        '        <strong> Laterality </strong>'+
                        '    </td>'+
                        '    <td width="*" align="left">'+  
                        '        <select id="laterality_option">'+
                        '            <option value="0">-Select-</option>'+
                        '            <option value="L">Left</option>'+
                        '            <option value="R">Right</option>'+
                        '            <option value="B">Both</option>'+
                        '        </select>'+
                        '    </td>'+
                        '</tr>'
        }
    }

    $j( elTarget ).prepend( rowSrc );
}
