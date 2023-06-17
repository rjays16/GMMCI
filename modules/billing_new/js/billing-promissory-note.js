var dialogSelEnc;

function preset(){
    var enc_nr = $('encounter_nr').value;
    if(enc_nr){
        xajax_getPromiDetails(enc_nr);
    }else{
    	var pageSelEnc = "../../modules/billing_new/billing-search-final-bill.php?";
        dialogSelEnc = $j('<div></div>')
                        .html('<iframe style="border: 0px; " src="' + pageSelEnc + '" width="100%" height=400px></iframe>')
                        .dialog({
                        autoOpen: false,
                        modal: true,
                        show: 'fade',
                        hide: 'fade',
                        height: 'auto',
                        width: '800',
                        title: 'Select Registered Person',
                        position: 'top',
                      });
         dialogSelEnc.dialog('open');
    }
}

function closeSelEncDiaglog(){
    dialogSelEnc.dialog('close');
}

function clickHandler(enc, bill_dt) {
   dialogSelEnc.dialog('close');
}

function checkFields(){

    if($('pid').value){
        var r = confirm("Are you sure you want to save this promissory note?");

        if(r == true){  
           
            if(!($('amount').value).trim() || isNaN($('amount').value)){
                alert('Unable to save note. Please indicate a proper amount.');
                $('amount').focus();
                return false;
            }

            xajax_savePromi($('mode_of_promi').value, $('duedate').value, $('encounter_nr').value, $('amount').value, $('remarks').value, $('refno_promi').value, $('name_g').value,
                                $('address_g').value, $('rel_g').value);
        }    
    }   
}


function resetData(due_date, name_g, address_g, rel_g, amount, remarks, format_duedate){
    $('show_duedate').innerHTML = format_duedate;
    $('duedate').value = due_date;
    $('name_g').value = name_g;
    $('address_g').value = address_g;
    $('rel_g').value = rel_g;
    $('amount').value = amount;
    $('remarks').value = remarks;
}

function getData(encounter_nr){
    xajax_getPromiDetails(encounter_nr);
}

function printPromi(){
    if($('mode_of_promi').value == 'edit'){
        var refno = $('refno_promi').value;
        window.open('billing-promissory-print.php?refno='+refno, "_blank", "width=800, height=800");
    }else{
        alert("Please save the promissory note details first!");
    }
}