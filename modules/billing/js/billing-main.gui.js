/*
*   Added startComputing and doneComputing progress indicator while billing is being computed ...
*   @modified:      LST
*   @modify date:   11.04.2010
*/
var isComputing=0;
var isAddingOp=0;
var isDeletingOp=0;
var oldcClick = cClick;
var acc_computed;
var med_computed;
var xlo_computed;
var ops_computed;
var pfs_computed;
var msc_computed;
var ld_computed;
var HSM = "HOSPITAL SPONSORED MEMBER";
var NBB = "SPONSORED MEMBER";

function jsBilling() {
    var nr = $('encounter_nr').value;
    if(nr!=''){
//      xajax_mainBilling(nr);
        clearSessionVars(nr);
    }else{
        $('select-enc').click();        // Added by LST ... 03.23.2009
    }
}

function startComputing() {
    doneComputing(0);
    if (!isComputing) {
        isComputing = 1;

        // Initialize flags ...
        acc_computed = 0;
        med_computed = 0;
        xlo_computed = 0;
        ops_computed = 0;
        pfs_computed = 0;
        msc_computed = 0;
        ld_computed = 0;

        return overlib('Retrieving encounter information ...<br><img src="../../images/ajax_bar.gif">',
            WIDTH,300, TEXTPADDING,5, BORDER,0,
            STICKY, SCROLL, CLOSECLICK, MODAL,
            NOCLOSE, CAPTION,'Retrieving encounter information',
            MIDX,0, MIDY,0,
            STATUS,'Please wait ...');
    }
    else
        return null;
}

function doneComputing(isfinal) {
    if (isComputing) {
        //setTimeout('cClick()', 500);
        cClick();
        isComputing = 0;
    }
    if (Number(isfinal)) {

        ld_computed = 1;
    }
}

function startAddOp() {
    doneAddOp();
    if (!isAddingOp) {
        isAddingOp = 1;

        return overlib2('Please wait ...<br><img src="../../images/ajax_bar.gif">',
            WIDTH,300, TEXTPADDING,5, BORDER,0,
            STICKY, SCROLL, CLOSECLICK, MODAL,
            NOCLOSE, CAPTION,'Adding Procedure',
            MIDX,0, MIDY,0,
            STATUS,'Adding Procedure');
    }
    else
        return null;
}

function doneAddOp() {
    if (isAddingOp) {
        cClick2();
        isAddingOp = 0;
    }
}

function startDelOp() {
    doneDelOp();
    if (!isDeletingOp) {
        isDeletingOp = 1;

        return overlib2('Please wait ...<br><img src="../../images/ajax_bar.gif">',
            WIDTH,300, TEXTPADDING,5, BORDER,0,
            STICKY, SCROLL, CLOSECLICK, MODAL,
            NOCLOSE, CAPTION,'Deleting Procedure',
            MIDX,0, MIDY,0,
            STATUS,'Deleting Procedure');
    }
}

function doneDelOp() {
    if (isDeletingOp) {
        cClick2();
        isDeletingOp = 0;
    }
}

function cvrgEditcClick() {
    oldcClick();
    setorigcClick();
    js_Recalculate();
    return false;
}

function setorigcClick() {
    cClick = oldcClick;
}

function overridecClick() {
    cClick = cvrgEditcClick;
}

function init(e){
    var billnr = $('old_bill_nr').value;

    YAHOO.pbill.container.bBody = new YAHOO.widget.Module("bBody", {visible:false});
    YAHOO.pbill.container.bBody.render();

//    YAHOO.pbill.container.bBody. beforeShowEvent = function() {
//        alert("Testing this event!");
//        return true;
//        doneComputing();
//    };

    YAHOO.util.Event.addListener("select-enc", "click",YAHOO.pbill.container.bBody.hide, YAHOO.pbill.container.bBody, true);
    YAHOO.util.Event.addListener("showMedDetails", "click", showMedDetails);
    YAHOO.util.Event.addListener("confineTypeOption", "change", jsOnchangeConfineType);
    YAHOO.util.Event.addListener("caseTypeOption", "change", jsOnchangeCaseType);

    YAHOO.util.Event.addListener("charge", "keypress", keyPressHandler);

    YAHOO.util.Event.addListener("btnPrevCoverage", "click", js_PrevCoverage);
    YAHOO.util.Event.addListener("btnNew", "click", js_NewBilling);
    YAHOO.util.Event.addListener("btnDelete", "click", js_DeleteBilling);
    YAHOO.util.Event.addListener("btnSave", "click", js_SaveBilling);
    YAHOO.util.Event.addListener("btnRecalc", "click", js_Recalculate);
    YAHOO.util.Event.addListener("btnPrint", "click",js_btnHandler);
    //added by VAN 04-15-08
    YAHOO.util.Event.addListener("btnDraft", "click",js_btnHandler2);
    YAHOO.util.Event.addListener("btnmedsandsupplies", "click", js_MoreMedsandSupplies);

    //added by VAN 08-13-08
    YAHOO.util.Event.addListener("btnInsurance", "click", js_EditInsurance);
    YAHOO.util.Event.addListener("btnDiagnosis", "click", js_ViewDiagnosis);

    //added by pol 10/05/2013
    YAHOO.util.Event.addListener("btnPrevPack", "click", js_displayPrevPack);
    //end by pol 10/05/2013

    shortcut.add("Ctrl+F", function(){ searchPatient(); }, {
            'type':'keypress',
            'propagate':false});
    shortcut.add("ESC", function(){ cClick(); });
    shortcut.add("Ctrl+P", function(){ js_btnHandler(); }, {
            'type':'keypress',
            'propagate':false});
    shortcut.add("Ctrl+S", function(){ js_SaveBilling(); }, {
            'type':'keypress',
            'propagate':false});

    if (billnr != "") {
        xajax_showBilling(billnr);
    }
}//end function init

function clickHandler(enc, bill_dt) {
    cClick();
    startComputing();
    clearSessionVars(enc, bill_dt);
//  xajax_mainBilling(enc, bill_dt);
}// end of function clickhandler

function computeAccommodation(b_all) {                // fix for HISSPMC-115
    var cb = setupCallback('AC', b_all);                // fix for HISSPMC-115
    xajax.call('computeAccommodation', { asynchronous: false, callback: cb });   // fix for HISSPMC-115
//  xajax_computeAccommodation();
}

function computeXLO(b_all) {                         // fix for HISSPMC-115
    var cb = setupCallback('HS', b_all);               // fix for HISSPMC-115
    xajax.call('computeXLO', { asynchronous: false, callback: cb });             // fix for HISSPMC-115
//  xajax_computeXLO();
}

function computeDrugsMeds(b_all) {                   // fix for HISSPMC-115
    var cb = setupCallback('MD', b_all);               // fix for HISSPMC-115
    xajax.call('computeDrugsMeds', { asynchronous: false, callback: cb });       // fix for HISSPMC-115
//  xajax_computeDrugsMeds();
}

function computePF(b_all) {                         // fix for HISSPMC-115
    var cb = setupCallback('PF', b_all);              // fix for HISSPMC-115
    xajax.call('computePF', { asynchronous: false, callback: cb });              // fix for HISSPMC-115
//  xajax_computePF();
}

function computeOP(b_all) {                        // fix for HISSPMC-115
    var cb = setupCallback('OP', b_all);             // fix for HISSPMC-115
    xajax.call('computeOP', { asynchronous: false, callback: cb });              // fix for HISSPMC-115
//  xajax_computeOP();
}

function computeMisc(b_all) {                     // fix for HISSPMC-115
    var cb = setupCallback('XC', b_all);            // fix for HISSPMC-115
    xajax.call('computeMisc', { asynchronous: false, callback: cb });            // fix for HISSPMC-115
//  xajax_computeMisc();
}

function computeLastPart() {
//  xajax_computeLastPart();

    xajax.call('computeLastPart', {
        asynchronous: false
    });



}

function calcPrevBill(enc, bill_dt, old_bill_nr) {
    $('buttons_bar').innerHTML = '<tr>'+
            '<td width="*">&nbsp;</td>'+
            '<td width="8" valign="bottom" align="center"><img id="btnNew" style="cursor:pointer" src="../../images/btn_newbill.gif" border=0 ></td>'+
            '<td width="8" valign="bottom" align="center"><img id="btnDelete" style="cursor:pointer" src="../../images/btn_delete.gif" border=0 ></td>'+
            '<td width="8" valign="bottom" align="center"><img id="btnPrint" style="cursor:pointer" src="../../images/btn_printpdf.gif" border=0 ></td>'+
            '<td width="14%" align="center" valign="middle"><input type="checkbox" name="IsDetailed" id="IsDetailed" style="vertical-align:middle">Detailed?</td>'+
        '</tr>';

    $('confineTypeOption').disabled = 'disabled';
    $('caseTypeOption').disabled = 'disabled';
    $('select-enc').disabled = 'disabled';
    $('btnaccommodation').disabled = 'disabled';
    $('btnOPaccommodation').disabled = 'disabled';
    $('btnmedsandsupplies').disabled = 'disabled';
    $('btnadddoctors').disabled = 'disabled';
    $('btnaddmisc_ops').disabled = 'disabled';
    $('btnaddmisc_srvc').disabled = 'disabled';
    $('btnaddmisc_chrg').disabled = 'disabled';
    $('btnadd_discount').disabled = 'disabled';

    YAHOO.util.Event.addListener("btnNew", "click", js_NewBilling);
    YAHOO.util.Event.addListener("btnDelete", "click", js_DeleteBilling);
    YAHOO.util.Event.addListener("btnPrint", "click",js_btnHandler);
    YAHOO.util.Event.addListener("btnDraft", "click",js_btnHandler2);

    shortcut.add("Ctrl+P", function(){ js_btnHandler(); }, {
            'type':'keypress',
            'propagate':false});

    $('categ_col').innerHTML = '<a title="Edit" href="#"></a>&nbsp;MEMBERSHIP CATEGORY:&nbsp;&nbsp;&nbsp;<span id="mcategdesc" name="mcategdesc"></span>';

    cClick();
    startComputing();

//  xajax_mainBilling(enc, bill_dt, '0000-00-00 00:00:00', old_bill_nr);
    clearSessionVars(enc, bill_dt, '0000-00-00 00:00:00', old_bill_nr);
}

function assignMemCategDesc(categ_desc) {
    $('billcol_01').colspan = "1";
    $('categ_col').style.display = "inline";
    categ_desc = (categ_desc == "" ? "NONE ASSIGNED" : categ_desc);
    $('mcategdesc').innerHTML = categ_desc;
}

function assignFromDte(frm_dte) {
    $('bill_frmdte').value = frm_dte;
}

function assignBillDte(bill_dte, show_dte) {
    $('billdate').value = bill_dte;
    $('show_billdate').innerHTML = show_dte;
}

function assignLastBillDte(dte) {
    if (dte) {
        $('lastbill_label').style.display = "";
        $('lastbill_actualdate').style.display = "";

        $('lastbill_date').value = dte;
    }
    else {
        $('lastbill_label').style.display = "none";
        $('lastbill_actualdate').style.display = "none";
    }
}

function assignAdmitDte(dte) {
    if (dte) {
        $('admit_label').style.display = "";
        $('admit_date').style.display = "";

        $('date_admitted').value = dte;
    }
    else {
        $('admit_label').style.display = "none";
        $('admit_date').style.display = "none";
    }
}

function showAccommodationList(bshow) {
    if (bshow)
        $('accommodation_div').style.display = "";
    else
        $('accommodation_div').style.display = "none";
}

function showIPDBillAreas(bshow) {
    showAccommodationList(bshow);
    if (bshow) {
        $('confine_label').style.display = "";
        $('confine_cbobox').style.display = "";
        $('op_div').style.display = "";
        $('pf_div').style.display = "";
    }
    else {
        $('confine_label').style.display = "none";
        $('confine_cbobox').style.display = "none";
        $('op_div').style.display = "none";
        $('pf_div').style.display = "none";
    }
}

//Edited by Jarel To Set Death Date if patient is already died
function assignBillingHeaderInfo(bill_nr, enc_nr, p_id, bill_dt, bill_frmdt, f_bill_dt, admission_dt, p_name, p_addr, f_ddate, ddate) {
    $('pid').value = p_id;
    $('encounter_nr').value = enc_nr;
    xajax_isTransmitted(enc_nr);
    $('pname').value = p_name;
    $('paddress').value = p_addr;
    $('admission_date').value = admission_dt;
    $('enc').value = enc_nr;

    $('billdate').value = bill_dt;
    $('show_billdate').innerHTML = f_bill_dt;
    if(ddate!=''){
        $('isdied').checked = true;
        $('deathdate').value = ddate;
        $('death_date').innerHTML = f_ddate;
        toggleDeathDate();
    }

    startComputing();
//  xajax_mainBilling(enc_nr, bill_dt, bill_frmdt, bill_nr);
    clearSessionVars(enc_nr, bill_dt, bill_frmdt, bill_nr);
}

function toggleFinalBill() {
    var is_finalbill = ($('isFinalBill').checked) ? 1 : 0;
    var enc_nr  = $('encounter_nr').value;
    var hasBloodTrans = $('hasbloodborrowed').value;

    if (hasBloodTrans=='1'){ 
        alert('This Patient has a pending transaction in Blood Bank. \n Please advice the patient to settle this transaction.');
        $('isFinalBill').checked = false;
        return false;
    }else{
    if (enc_nr != "") {
        if (is_finalbill) {
                var bill_dt = $('billdate').value;
                xajax_toggleMGH(enc_nr, bill_dt, 1);
            //xajax_displayUnservedRequest(enc_nr, 1);
        }
        else {
            xajax_toggleMGH(enc_nr);
        }
        showBillingStatus(((is_finalbill) ? "1" : "0"));
    }
    else {
        $('isFinalBill').checked = false;
        alert("Please specify patient!");
    }
}

}

function js_Recalculate() {
    var enc_nr  = $('encounter_nr').value;
    var pid = $('pid').value;
    var bill_dt = $('billdate').value;
    var fbill_dt = $('show_billdate').innerHTML;
    var admission_dt = $('admission_date').value;
    var bill_nr = $('old_bill_nr').value;

    //added by jasper 04/01/2103
    $('nobalance').style.display= "none";
    $('infirmary').style.display= "none";
    $('prevbill').style.display= "none";
    $('poc').style.display= "none";//Added By Jarel For POC Changes

    if ((enc_nr != "") && (pid != "")) {
        if (compareDates(admission_dt, "MMM dd, yyyy hh:mma", fbill_dt, "MMM dd, yyyy hh:mma") > 0)
            alert("You cannot bill earlier than the admission date!");
        else {
            startComputing();
            if ($('is_dialysis').value == '1') xajax_delPostedItemsForDialysisPkg(enc_nr);
//          xajax_mainBilling(enc_nr, bill_dt, '0000-00-00 00:00:00', bill_nr);
            clearSessionVars(enc_nr, bill_dt, '0000-00-00 00:00:00', bill_nr);
        }
    }
    else
        alert("Please specify patient!");
}

function js_RecalcDiscount() {
    var enc_nr  = $('encounter_nr').value;
    var bill_dt = $('billdate').value;

    xajax_recalcDiscount(enc_nr, bill_dt);
}

function js_PrevCoverage() {
    var enc = $('encounter_nr').value;
    var pid = $('pid').value;
    var seg_URL_APPEND = $F('seg_URL_APPEND');
    var frm_dte = $('bill_frmdte').value;

    if (enc != "" && pid != "") {
        return overlib(OLiframeContent('billing-prev-coverage.php'+seg_URL_APPEND+'&nr='+enc+'&frmdte='+(getDateFromFormat(frm_dte, 'yyyy-MM-dd HH:mm:ss')/1000), 700, 395, 'fPrevCoverage', 0, 'auto'),
                        WIDTH, 700, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL, CLOSETEXT,
                        '<img src=../../images/close.gif border=0>', CAPTIONPADDING, 4, CAPTION, 'Previous Coverage',
                        MIDX, 0, MIDY, 0, STATUS, 'Post Previous Coverage');
    }
    else {
        alert("Please specify patient!");
    }
}

function js_MoreMedsandSupplies() {
    var enc = $('encounter_nr').value;
    var seg_URL_APPEND = $F('seg_URL_APPEND');
    var frm_dte = $('bill_frmdte').value;
    var bill_dt = $('billdate').value;
    var userck = $('userck').value;

//  var pharma_link = '../../modules/pharmacy/apotheke-pass.php'+seg_URL_APPEND+'&userck='+userck+'&target=orderbilling&encounter_nr='+enc+'&from_dt='+(getDateFromFormat(frm_dte, 'yyyy-MM-dd HH:mm:ss')/1000)+'&to_dt='+(getDateFromFormat(bill_dt, 'yyyy-MM-dd HH:mm:ss')/1000);
    var pharma_link = 'billing-more-pharmaorder.php';
//pol
    if (FinalBillCheck==1){
    return overlib(OLiframeContent(pharma_link, 800, 380, 'fSelEnc', 0, 'auto'),
                    WIDTH, 800, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL, CLOSETEXT,
                    '<img src=../../images/close.gif border=0>', CAPTIONPADDING, 4, CAPTION, 'More Drugs, Meds or Supplies',
                    MIDX, 0, MIDY, 0, STATUS, 'More Drugs, Meds or Supplies');
    }else{
        alert("Cannot add more med and supplies because this case no. has final bill saved.");
    }
}

function searchPatient(){
//  var rpath = $('rpath').value;
    return overlib(OLiframeContent('billing-select-enc.php', 800, 410, 'fSelEnc', 0, 'auto'),
                    WIDTH, 800, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL, CLOSETEXT,
                    '<img src=../../images/close.gif border=0>', CAPTIONPADDING, 4, CAPTION, 'Select registered person',
                    MIDX, 0, MIDY, 0, STATUS, 'Select registered person');
            //window.open("seg-lab-request.php?is_cash="+is_cash+"&pid="+pid+"&refno="+refno+"&showBrowser=1","viewPatientRequest","width=620,height=440,menubar=no,resizable=yes,scrollbars=yes")
    /*return overlib(OLiframeContent(rpath+'modules/billing/billing_print.php', 400,100, 'flab-request', 1, 'auto'),
                        WIDTH , 400, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL ,DRAGGABLE, CLOSETEXT,
                        '<img src=../../images/close.gif border=0>', CAPTIONPADDING, 4, CAPTION, 'Print Billing',
                        MIDX, 0, MIDY, 0, STATUS,'Print Billing');
    */
}

function showSaveIndicator() {
    return overlib(OLiframeContent('billing-save-status.php', 480, 70, 'fsavestat', 0, 'no'),
                    WIDTH, 480, BORDER, 0, STICKY, MODAL, MIDX, 0, MIDY, 0);
}

/*
function printRadioReport(){

        var w=window.screen.width;
        var h=window.screen.height;
        var ww=500;
        var wh=500;
        var rpath=$F('rpath');
        var pid=$F('pid');
        var batch_nr=$F('batch_nr');
        var seg_URL_APPEND=$F('seg_URL_APPEND');
//      alert("printRadioReport :: rpath = '"+rpath+"' \nbatch_nr = '"+batch_nr+"' \seg_URL_APPEND = '"+seg_URL_APPEND+"'");
//      AUGUST 9, 2007
//          kuya mark :
//              change nyo po ang   urlholder="PHP file that will handle the report generation of ROENTGENOLOGICAL REPORT"
        urlholder=rpath+"modules/radiology/certificates/seg-radio-report-pdf.php"+seg_URL_APPEND+"&pid="+pid+"&batch_nr="+batch_nr;
//      alert("printRadioReport :: urlholder = '"+urlholder+"'");
//      var fso = new ActiveXObject("Scripting.FileSystemObject");
//      fileBool = fso.FileExists(rpath+"radiology/modules/certificates/seg-radio-report-pdf.php");
//      alert("printRadioReport :: urlholder = '"+urlholder+"' \nfileBool = '"+fileBool+"'");
//      alert("printRadioReport :: ROENTGENOLOGICAL REPORT in pdf format");

        if (window.showModalDialog){  //for IE
            window.showModalDialog(urlholder,"width=" + ww + ",height=" + wh + ",menubar=no,resizable=yes,scrollbars=yes,dependent=yes");
        }else{
//          window.open("createCampus.php?i="+id,"createCampus","modal, width=480,height=320,menubar=no,resizable=no,scrollbars=no");
            popWindowEditFinding=window.open(urlholder,"Print Report","width=" + ww + ",height=" + wh + ",menubar=no,resizable=yes,scrollbars=yes,dependent=yes");
            window.popWindowEditFinding.moveTo((w/2)+80,(h/2)-(wh/2));
        }

    }
*/

function closeSaveIndicator() {
    //removed by jasper 04/03/2013
    //YAHOO.pbill.container.bBody = new YAHOO.widget.Module("bBody");
    //YAHOO.pbill.container.bBody.hide();
    //removed by jasper 04/03/2013
    cClick();
    switchToViewMode();
}

function js_NewBilling() {
    window.location.href="billing-main.php";
}

function clearBillingHeaderInfo(bill_dt, f_bill_dt) {
    $('pid').value = '';
    $('encounter_nr').value = '';
    $('pname').value = '';
    $('paddress').value = '';
    $('admission_date').value = '';
    $('enc').value = '';
    $('old_bill_nr').value = '';
    $('confineTypeOption').disabled = false;

    $('billdate').value = bill_dt;
    $('show_billdate').innerHTML = f_bill_dt;
}

function js_DeleteBilling() {
    var old_billnr = $('old_bill_nr').value;
    var enc_nr = $('encounter_nr').value; //added by jasper 04/23/2013
    //alert(old_billnr + " " + enc_nr);
    var message = "Do you really want to delete this billing?\nClick OK to delete, CANCEL otherwise!";
    var ret_val = false;
    if (old_billnr != "") {
        ret_val = confirm(message);
        if (ret_val == true) {
            //alert(old_billnr);
            xajax_deleteBilling(old_billnr, enc_nr);
            //js_NewBilling(); //removed by jasper 04/04/2013
            xajax_clearBilling();
            //js_NewBilling()
//          closeSaveIndicator();
        }
    }
    else {
        alert("No billing to delete!");
    }
}


function js_SaveBilling()
{
    var is_finalbill = ($('isFinalBill').checked) ? 1 : 0;
    var enc = $('encounter_nr').value;
    var pid = $('pid').value;
    var bill_dt = $('billdate').value;

    var message = "Do you really want to SAVE this billing?\nClick OK to SAVE, otherwise CANCEL!";
    var ret_val = false;
    //edtied by jasper 04/02/2013
    var msgPartialBill = "This is not the final bill yet! PARTIAL BILL MUST NOT BE SAVED!";
    //var is_finalbill = ($('isFinalBill').checked) ? 1 : 0;
    //if (is_finalbill) {
        if (enc != "" && pid != "")
        {
            if (ld_computed && acc_computed && med_computed && xlo_computed && ops_computed && pfs_computed && msc_computed)
            {
              // fix for HISSPMC-115
              // e['btnSave'].disabled = isNotValid;
            ret_val = confirm(message);
                if (ret_val == true)
                    {
                            if($('isdied').checked){
                                var deathdate = $('deathdate').value;
                            }else{
                                var deathdate = '';
                            }
                        showSaveIndicator();
                        //added by jasper 06/05/2013
                        if (is_finalbill) {
                            //var bill_dt = $('billdate').value;
                            xajax_toggleMGH(enc, bill_dt, 1);
                        }
                        else {
                            xajax_toggleMGH(enc);
                        }
                        //added by jasper 06/05/2013
                //          xajax_saveThisBilling(enc, bill_dt, $('billobject').innerHTML);
                        xajax_saveThisBilling(enc, bill_dt, pid, deathdate);
                    }
            }
            else
                alert("Please wait for billing computation to complete!");
                // fix for HISSPMC-115
        }
        else
        {
            alert("Please specify patient!");
        }
    //} else {
    //    alert(msgPartialBill);
    //}
}

function js_btnHandler(){
    var rpath = $('rpath').value;
    var enc = $('encounter_nr').value;
    var pid = $('pid').value;
    var bill_dt = $('billdate').value;
    var bill_nr = $('old_bill_nr').value;
    var frm_dte = $('bill_frmdte').value;
    var seg_URL_APPEND = $F('seg_URL_APPEND');

    //Added by Jarel 05/24/2013
    if($('isdied').checked){
        var deathdate = $('deathdate').value;
    }else{
        var deathdate = '';
    }

    //alert(enc + "--" + pid + acc_computed + med_computed + xlo_computed + ops_computed + pfs_computed + msc_computed);

    //added by jasper 04/02/2013
    if (bill_nr == '') {
            var is_finalbill = ($('isFinalBill').checked) ? 1 : 0;
            if (is_finalbill) {
                alert("This bill has not been saved yet! Please SAVE this final bill before printing!");
                return;
            }
    }

    //added by VAN 02-13-08
    var detailed;
    if ($('IsDetailed').checked)
        detailed = 1;
    else
        detailed = 0;

    //urlholder = rpath+'modules/billing/billing_print.php?pid='+pid+'&encounter_nr='+enc;
    //urlholder = rpath+'modules/billing/billing-pdf.php'+seg_URL_APPEND+'&pid='+pid+'&encounter_nr='+enc;
    //urlholder = rpath+'modules/billing/bill-pdf-summary.php'+seg_URL_APPEND+'&pid='+pid+'&encounter_nr='+enc;
    //urlholder = rpath+'modules/billing/bill-pdf-summary.php'+seg_URL_APPEND+'&pid='+pid+'&encounter_nr='+enc+'&IsDetailed='+detailed;
        urlholder = rpath+'modules/billing/bill-pdf-summary.php'+seg_URL_APPEND+'&pid='+pid+'&encounter_nr='+enc+'&from_dt='+(getDateFromFormat(frm_dte, 'yyyy-MM-dd HH:mm:ss')/1000)+'&bill_dt='+(getDateFromFormat(bill_dt, 'yyyy-MM-dd HH:mm:ss')/1000)+'&nr='+bill_nr+'&IsDetailed='+detailed+'&deathdate='+deathdate;

    nleft = (screen.width - 680)/2;
    //nleft = window.screen.width;
    //ntop = window.screen.height;
    ntop = (screen.height - 520)/2;
    if(enc != "" && pid != "")
    {
        if (ld_computed && acc_computed && med_computed && xlo_computed && ops_computed && pfs_computed && msc_computed)
        {
                //printwin = window.open(urlholder, "Billing", "toolbar=no, status=no, menubar=no, width=500, height=600, location=center, directories=no, resizeble=no, scrollbars=no, top=" + ntop + ", left=" + nleft);
                printwin = window.open(urlholder, "Print Billing", "toolbar=no, status=no, menubar=no, width=700, height=500, location=center, dependent=yes, resizable=yes, scrollbars=yes, top=" + ntop + ",left=" + nleft);
        }
        else
            //e['btnSave'].disabled = isNotValid;
            alert('Please wait for the billing calculation to complete!');
    } else {
        alert("Please specify patient!");
    }

    return true;
}// end of function js_btnHandler

//added by VAN 04-15-08
function js_btnHandler2(){
    var rpath = $('rpath').value;
    var enc = $('encounter_nr').value;
    var pid = $('pid').value;
    var bill_dt = $('billdate').value;
    var seg_URL_APPEND = $F('seg_URL_APPEND');

    //added by VAN 02-13-08
    var detailed;
    if ($('IsDetailed').checked)
        detailed = 1;
    else
        detailed = 0;

    urlholder = rpath+'modules/billing/billing_print.php?pid='+pid+'&encounter_nr='+enc;
    //urlholder = rpath+'modules/billing/billing-pdf.php'+seg_URL_APPEND+'&pid='+pid+'&encounter_nr='+enc;
    //urlholder = rpath+'modules/billing/bill-pdf-summary.php'+seg_URL_APPEND+'&pid='+pid+'&encounter_nr='+enc;
    //urlholder = rpath+'modules/billing/bill-pdf-summary.php'+seg_URL_APPEND+'&pid='+pid+'&encounter_nr='+enc+'&IsDetailed='+detailed;
    //urlholder = rpath+'modules/billing/bill-pdf-summary.php'+seg_URL_APPEND+'&pid='+pid+'&encounter_nr='+enc+'&bill_dt='+bill_dt+'&IsDetailed='+detailed;

    nleft = (screen.width - 680)/2;
    //nleft = window.screen.width;
    //ntop = window.screen.height;
    ntop = (screen.height - 520)/2;

    if(enc != "" && pid != ""){
        //printwin = window.open(urlholder, "Billing", "toolbar=no, status=no, menubar=no, width=500, height=600, location=center, directories=no, resizeble=no, scrollbars=no, top=" + ntop + ", left=" + nleft);
        printwin = window.open(urlholder, "Print Billing", "toolbar=no, status=no, menubar=no, width=700, height=500, location=center, dependent=yes, resizable=yes, scrollbars=yes, top=" + ntop + ",left=" + nleft);
    }else{
        alert("Please specify patient!");
    }

    return true;
}// end of function js_btnHandler

//--------------------------------------

/*function remLink(){
    if(window.printwin  && window.printwin.open && !window.printwin.closed)
        window.printwin.opener = null;
}*/

function initDialogBox(){
    // Define various event handlers for Dialog
    var handleSubmit = function() {
        this.submit();
    };
    var handleCancel = function() {
        this.cancel();
    };

    var handleFailure = function(o) {
        alert("Submission failed: " + o.status);
    };

    // Instantiate the Dialog
    //YAHOO.pbill.container.dialog1 = new YAHOO.widget.Dialog("profDialogbox",
    //                                                          { width : "390px",
    //                                                              fixedcenter : true,
    //                                                              visible : false,
    //                                                              constraintoviewport : true,
    //                                                              buttons : [ { text:"Verify Accreditation", handler:handleVerify }, //added by jasper
    //                                                                           { text:"Add", handler:handleSubmit, isDefault:true },
    //                                                                          { text:"Cancel", handler:handleCancel }
    //                                                                         ]
    //                                                           } );
    //by jasper
    YAHOO.pbill.container.dialog1 = new YAHOO.widget.Dialog("profDialogbox",
                                                                { width : "520px", height : "265px",
                                                                    fixedcenter : true,
                                                                    visible : false,
                                                                    constraintoviewport : true
                                                                 } );

    YAHOO.pbill.container.dialog1.validate = function(){
        var data  = this.getData();

        if(data.doclist == '0')
            alert("Please select a doctor");
        else if(data.rolearea == '0')
            alert("Specify doctor's role");
        else if(data.charge=='')
            alert("Enter doctor's charge");
        else {
            var ops = $('opstaken').innerHTML;
            xajax_ProcessPrivateDrCharge(data, data.bill_dte, ops);
            clearProfDialog();
            return false;
        }

        return false;
    };

    YAHOO.util.Event.addListener("btnadddoctors", "click", showDialog);
}

//added by jasper 03/12/2013
function jsCloseWindow() {
    YAHOO.pbill.container.dialog1.hide();
}
//added by jasper 03/12/2013

function initCategoryPrompt(){
    // Define various event handlers for Dialog
    var handleSubmit = function() {
        this.submit();
    };
    var handleCancel = function() {
        this.cancel();
    };

    // Instantiate the Dialog
    YAHOO.categprompt.container.memcategdialogbox = new YAHOO.widget.Dialog("memcategdialogbox",
                                                                             { width : "390px",
                                                                                fixedcenter : true,
                                                                                visible : false,
                                                                                constraintoviewport : true,
                                                                                buttons : [ { text:"Save", handler:handleSubmit, isDefault:true },
                                                                                            { text:"Cancel", handler:handleCancel } ]
                                                                             } );

    YAHOO.categprompt.container.memcategdialogbox.validate = function(){
        var data  = this.getData();

        if(data.category_list == '0') {
            alert("Please select membership category!");
            return false;
        }
        else {
            xajax_setMemCategory(data);
            return true;
        }
    };

    YAHOO.util.Event.addListener("memcateg", "click", ShowCategoryAlert); //added by pol
}

//added by pol
function ShowCategoryAlert() {
    var enc = $('encounter_nr').value;
    xajax_checkPHIC(enc);

}

//end by pol

function showCategoryPrompt() {
    $('memcategdialogbox').style.display = "";
    $('category_list').style.visibility = "";

    YAHOO.categprompt.container.memcategdialogbox.render();
    YAHOO.categprompt.container.memcategdialogbox.show();

    xajax_setMemCategoryOptions();
}

function initAccomPrompt(){
    // Define various event handlers for Dialog
    var handleSubmit = function() {
        this.submit();
    };
    var handleCancel = function() {
        this.cancel();
    };

    // Instantiate the Dialog
    YAHOO.pbill.container.accAddDialogBox = new YAHOO.widget.Dialog("accAddDialogBox",
                                                                             { width : "680px",
                                                                                fixedcenter : true,
                                                                                visible : false,
                                                                                constraintoviewport : true,
                                                                                buttons : [ { text:"Save", handler:handleSubmit, isDefault:true },
                                                                                            { text:"Cancel", handler:handleCancel } ]
                                                                             } );

    YAHOO.pbill.container.accAddDialogBox.validate = function(){
        var data  = this.getData();

        if (data.wardlist == '0') {
            alert("Please select a ward!");
            return false;
        }
        else if (data.roomlist == '0') {
            alert("Please select a room!");
            return false;
        }
        //added condition by jane 10/29/2013
        else if(Number(data.days_stay) <= 0)
        {
            alert("Specify number of days");
            $('days_stay').focus();
            return false;
        }
        else if (Number(data.rate) == 0) {
            alert("Accommodation charge must be nonzero!");
            return false;
        }
        else {
            xajax_saveAccommodation(data, $('bill_dte').value);
            return true;
        }
    };

    YAHOO.util.Event.addListener("btnaccommodation", "click", showMoreAccomPrompt);
}

function showMoreAccomPrompt() {
    $('accAddDialogBox').style.display = "";
    $('wardlist').style.visibility = "";
    $('roomlist').style.visibility = "";

    YAHOO.pbill.container.accAddDialogBox.render();
    YAHOO.pbill.container.accAddDialogBox.show();

    $('days_stay').value = '';
    $('hrs_stay').value = '';
    $('rate').value = '';
    $('occupydate').value = ''; //added by jane 10/30/2013

    xajax_setWardOptions();
}

function initOpAccomChrgPrompt(){
    // Define various event handlers for Dialog
    var handleSubmit = function() {
        this.submit();
    };
    var handleCancel = function() {
        this.cancel();
    };

    // Instantiate the Dialog
    YAHOO.pbill.container.opAccChrgBox = new YAHOO.widget.Dialog("opAccChrgBox",
                                                                    { width : "520px",
                                                                    fixedcenter : true,
                                                                    visible : false,
                                                                    constraintoviewport : true,
                                                                    buttons : [ { text:"Save", handler:handleSubmit, isDefault:true },
                                                                                { text:"Cancel", handler:handleCancel } ]
                                                                    } );

    YAHOO.pbill.container.opAccChrgBox.validate = function(){
        var data  = this.getData();

        if (data.opwardlist == '0') {
            alert("Please select a O.R. ward!");
            return false;
        }
        else if (data.orlist == '0') {
            alert("Please select an operating room!");
            return false;
        }
        else if (Number(data.oprm_chrg) == 0) {
            alert("O.R. accommodation charge must be nonzero!");
            return false;
        }
        else if ((data.total_rvu == '') || (data.multiplier == '')) {
            alert("You must select the procedure(s) associated with this accommodation!\nClick the icon at the right of total RVU!");
            return false;
        }
        else {
            var ops = $('opstaken').innerHTML;
            alert(data);
            xajax_saveORAccommodation(data, $('bill_dte').value, ops);
            return true;
        }
    };

    YAHOO.util.Event.addListener("btnOPaccommodation", "click", showOpAccomChrgPrompt);
}

function showOpAccomChrgPrompt() {
    $('opAccChrgBox').style.display = "";
    $('opwardlist').style.visibility = "";
    $('orlist').style.visibility = "";

    YAHOO.pbill.container.opAccChrgBox.render();
    YAHOO.pbill.container.opAccChrgBox.show();

    $('total_rvu').value = '';
    $('multiplier').value = '';
    $('oprm_chrg').value = '';
    $('opstaken').innerHTML = '';

    xajax_setORWardOptions();
}

//added by jasper 11/16/12
//function jsVerifyDoctor() {
//    //var arrDoctor = xajax_getDoctorInfo($('doclist').value);
//    //alert($('admission_dte').value);
//    var disc = new Date();
//    var adm = $('admission_dte').value;
//    var arrAdm = adm.split("-");
//    var disch_date = disc.getMonth() + "-" + disc.getDate() + "-" + disc.getFullYear();
//    var adm_date = arrAdm[1] + "-" + arrAdm[2] + "-" + arrAdm[0];
//    xajax_getDoctorInfo($('doclist').value, adm_date, disch_date);
//}
//added by jasper 11/16/12

function showDialog(){
    // Enforce visibility of select tags in html ...
    //added by jasper 11/16/12
//    $('btnVerify').style.marginLeft = "-5px";
//    $('btnAdd').style.marginLeft = "69px";
    //added by jasper 11/16/12
//    $('btnVerify').disabled = true;
    $('profDialogbox').style.display = "";
    $('doclist').style.visibility = "";
    $('rolearea').style.visibility = "";
    $('role_level').style.visibility = "";

    YAHOO.pbill.container.dialog1.render();
    YAHOO.pbill.container.dialog1.show();

    xajax_setDoctors(1,0,0);
    xajax_setRoleArea(1);
    xajax_setOptionRoleLevel();

    $('charge').value = '';
    $('dr_nr').value = '';
    $('bill_dte').value = $('billdate').value;
    $('is_excluded').checked = '';
    $('ndays').value = '';
    $('excluded').value = '0';
    $('opstaken').innerHTML = ''; //added by jasper 05/17/2013
}

function clearProfDialog() {
    $('doclist').value = "0";
    $('rolearea').value = "0";
    $('role_level').value = "0";

    $('charge').value = '';
    $('dr_nr').value = '';
    $('role_nr').value = '';
    $('tier_nr').value = '';
    $('opstaken').innerHTML = '';  //added by jasper 05/17/2013

    $('doclist').focus();
}

function goDelMiscOps(enc_nr, bill_dt, bill_frmdte, code) {
//  $('del_stat').value = '0';
    startDelOp();
    xajax_delMiscOp(enc_nr, bill_dt, bill_frmdte, code);
//  if ($('del_stat').value == '1') {
//      alert("Procedure successfully deleted!");
//  }
//  else
//      alert("No miscellaneous operation deleted!");

}

//function setDeleteFlag(status) {
//  if (typeof(status) == 'undefined') status = '0';
//  $('del_stat').value = status;
    //window.focus();
//}

//function goSetDeleteFlag() {
//    window.parent.setDeleteFlag('1');
//  $('del_stat').value = '1';
//}

function showBillingStatus(bShow) {
    var elem = $('bill_status');

    if (bShow == "1") {
        elem.style.visibility = "";
        elem.innerHTML = "[FINAL BILLING]";
        $('isFinalBill').checked = true;
    }
    else {
        elem.style.visibility = "none";
        elem.innerHTML = "";
        $('isFinalBill').checked = false;
    }
}

function promptDelMiscOps(bill_frmdte, code){
    var enc = $('encounter_nr').value;
    var elTarget = 'op_code'+code;

    //Define various event handlers for dialog
    var handleYes = function (){
        xajax_delMiscOp(enc, $('billdate').value, bill_frmdte, code);
        this.hide();
    };

    var handleNo = function(){
        this.hide();
    };

    // Instantiate the Dialog
    YAHOO.dbill.container.simpledialog1 = new YAHOO.widget.SimpleDialog("simpledialog1",
                                                                         { width: "400px",
                                                                             fixedcenter: true,
                                                                             visible: false,
                                                                             draggable: false,
                                                                             close: true,
                                                                             text: "Do you really want to remove procedure "+code+"?<br>NOTE: Deletion will remove only 1 instance of the procedure.",
                                                                             icon: YAHOO.widget.SimpleDialog.ICON_HELP,
                                                                             constraintoviewport: true,
                                                                             buttons: [ { text:"Yes", handler:handleYes, isDefault:true },
                                                                                        { text:"No", handler:handleNo } ]
                                                                         } );

    YAHOO.dbill.container.simpledialog1.setHeader("Delete miscellaneous procedure");
    YAHOO.dbill.container.simpledialog1.render(document.body);
    YAHOO.util.Event.addListener(elTarget, "click", YAHOO.dbill.container.simpledialog1.show, YAHOO.dbill.container.simpledialog1, true);
}// end of function promptDelMiscOps

function promptDelOpAccom(bill_frmdte, code, n){
    var enc = $('encounter_nr').value;
    var elTarget = 'op_code'+n+'_'+code;

    //Define various event handlers for dialog
    var handleYes = function (){
//        xajax_delMiscOp(enc, $('billdate').value, bill_frmdte, code);
        xajax_delOpAccommodation(enc, $('billdate').value, bill_frmdte, code);
        this.hide();
    };

    var handleNo = function(){
        this.hide();
    };

    // Instantiate the Dialog
    YAHOO.dbill.container.simpledialog1 = new YAHOO.widget.SimpleDialog("simpledialog1",
                                                                         { width: "400px",
                                                                             fixedcenter: true,
                                                                             visible: false,
                                                                             draggable: false,
                                                                             close: true,
                                                                             text: "Do you really want to remove this OR accommodation?<br>NOTE: Deletion will remove only 1 instance of the use of OR.",
                                                                             icon: YAHOO.widget.SimpleDialog.ICON_HELP,
                                                                             constraintoviewport: true,
                                                                             buttons: [ { text:"Yes", handler:handleYes, isDefault:true },
                                                                                        { text:"No", handler:handleNo } ]
                                                                         } );

    YAHOO.dbill.container.simpledialog1.setHeader("Delete OR Accommodation");
    YAHOO.dbill.container.simpledialog1.render(document.body);
    YAHOO.util.Event.addListener(elTarget, "click", YAHOO.dbill.container.simpledialog1.show, YAHOO.dbill.container.simpledialog1, true);
}// end of function promptDelOpAccom

function promptDelMiscChrg(bill_frmdte, code, sname) {
    var enc = $('encounter_nr').value;
    var elTarget = 'code_'+code;

    //Define various event handlers for dialog
    var handleYes = function (){
        xajax_delMiscChrg(enc, $('billdate').value, bill_frmdte, code);
        this.hide();
    };

    var handleNo = function(){
        this.hide();
    };

    // Instantiate the Dialog
    YAHOO.dbill.container.simpledialog1 = new YAHOO.widget.SimpleDialog("simpledialog1",
                                                                         { width: "450px",
                                                                             fixedcenter: true,
                                                                             visible: false,
                                                                             draggable: false,
                                                                             close: true,
                                                                             text: "Do you really want to remove "+sname+"?<br>NOTE: Deletion will remove only 1 instance of the miscellaneous charge.",
                                                                             icon: YAHOO.widget.SimpleDialog.ICON_HELP,
                                                                             constraintoviewport: true,
                                                                             buttons: [ { text:"Yes", handler:handleYes, isDefault:true },
                                                                                        { text:"No", handler:handleNo } ]
                                                                         } );

    YAHOO.dbill.container.simpledialog1.setHeader("Delete miscellaneous charge");
    YAHOO.dbill.container.simpledialog1.render(document.body);
    YAHOO.util.Event.addListener(elTarget, "click", YAHOO.dbill.container.simpledialog1.show, YAHOO.dbill.container.simpledialog1, true);
}

function promptDelMiscService(bill_frmdte, code, sname) {
    var enc = $('encounter_nr').value;
    var elTarget = 'code_'+code;

    sname = unescape(sname);

    //Define various event handlers for dialog
    var handleYes = function (){
        xajax_delMiscService(enc, $('billdate').value, bill_frmdte, code);
        this.hide();
    };

    var handleNo = function(){
        this.hide();
    };

    // Instantiate the Dialog
    YAHOO.dbill.container.simpledialog1 = new YAHOO.widget.SimpleDialog("simpledialog1",
                                                                         { width: "450px",
                                                                             fixedcenter: true,
                                                                             visible: false,
                                                                             draggable: false,
                                                                             close: true,
                                                                             text: "Do you really want to remove "+sname+"?<br>NOTE: Deletion will remove only 1 instance of the miscellaneous service.",
                                                                             icon: YAHOO.widget.SimpleDialog.ICON_HELP,
                                                                             constraintoviewport: true,
                                                                             buttons: [ { text:"Yes", handler:handleYes, isDefault:true },
                                                                                        { text:"No", handler:handleNo } ]
                                                                         } );

    YAHOO.dbill.container.simpledialog1.setHeader("Delete miscellaneous service");
    YAHOO.dbill.container.simpledialog1.render(document.body);
    YAHOO.util.Event.addListener(elTarget, "click", YAHOO.dbill.container.simpledialog1.show, YAHOO.dbill.container.simpledialog1, true);
}

function promptDelSupply(bill_frmdte, code, sname, bill_area) {
    var enc = $('encounter_nr').value;
    var elTarget = 'code_'+code;

    sname = unescape(sname);

    //Define various event handlers for dialog
    var handleYes = function (){
        xajax_delSupply(enc, $('billdate').value, bill_frmdte, code, bill_area);
        this.hide();
    };

    var handleNo = function(){
        this.hide();
    };

    // Instantiate the Dialog
    YAHOO.dbill.container.simpledialog1 = new YAHOO.widget.SimpleDialog("simpledialog1",
                                                                         { width: "450px",
                                                                             fixedcenter: true,
                                                                             visible: false,
                                                                             draggable: false,
                                                                             close: true,
                                                                             text: "Do you really want to remove "+sname+"?<br>NOTE: Deletion will remove only 1 instance of the charge.",
                                                                             icon: YAHOO.widget.SimpleDialog.ICON_HELP,
                                                                             constraintoviewport: true,
                                                                             buttons: [ { text:"Yes", handler:handleYes, isDefault:true },
                                                                                        { text:"No", handler:handleNo } ]
                                                                         } );

    YAHOO.dbill.container.simpledialog1.setHeader("Delete Charge");
    YAHOO.dbill.container.simpledialog1.render(document.body);
    YAHOO.util.Event.addListener(elTarget, "click", YAHOO.dbill.container.simpledialog1.show, YAHOO.dbill.container.simpledialog1, true);
}

function promptDelAccommodation(rmtyp, src) {
    var enc_nr = $('encounter_nr').value;
    var elTarget = 'type_'+rmtyp+'_'+src;

    //Define various event handlers for dialog
    var handleYes = function (){
        xajax_delAccommodation(enc_nr, $('billdate').value);
        this.hide();
    };

    var handleNo = function(){
        this.hide();
    };

    // Instantiate the Dialog
    YAHOO.dbill.container.simpledialog1 = new YAHOO.widget.SimpleDialog("simpledialog1",
                                                                         { width: "450px",
                                                                             fixedcenter: true,
                                                                             visible: false,
                                                                             draggable: false,
                                                                             close: true,
                                                                             text: "Do you really want to remove this accommodation?<br>NOTE: Deletion will remove the most recent posted accommodation.",
                                                                             icon: YAHOO.widget.SimpleDialog.ICON_HELP,
                                                                             constraintoviewport: true,
                                                                             buttons: [ { text:"Yes", handler:handleYes, isDefault:true },
                                                                                        { text:"No", handler:handleNo } ]
                                                                         } );

    YAHOO.dbill.container.simpledialog1.setHeader("Delete accommodation");
    YAHOO.dbill.container.simpledialog1.render(document.body);
    YAHOO.util.Event.addListener(elTarget, "click", YAHOO.dbill.container.simpledialog1.show, YAHOO.dbill.container.simpledialog1, true);
}

function promptDelAccom(rmtyp, src) {
    var enc_nr = $('encounter_nr').value;
    var elTarget = 'type_'+rmtyp+'_'+src;

    //Define various event handlers for dialog
    var handleYes = function (){
        xajax_delAccom(enc_nr, $('billdate').value);
        this.hide();
    };

    var handleNo = function(){
        this.hide();
    };

    // Instantiate the Dialog
    YAHOO.dbill.container.simpledialog1 = new YAHOO.widget.SimpleDialog("simpledialog1",
                                                                         { width: "450px",
                                                                             fixedcenter: true,
                                                                             visible: false,
                                                                             draggable: false,
                                                                             close: true,
                                                                             text: "Do you really want to remove this accommodation?<br>NOTE: Deletion will remove the accommodation posted at<br>the admission or ward departments.",
                                                                             icon: YAHOO.widget.SimpleDialog.ICON_HELP,
                                                                             constraintoviewport: true,
                                                                             buttons: [ { text:"Yes", handler:handleYes, isDefault:true },
                                                                                        { text:"No", handler:handleNo } ]
                                                                         } );

    YAHOO.dbill.container.simpledialog1.setHeader("Delete accommodation");
    YAHOO.dbill.container.simpledialog1.render(document.body);
    YAHOO.util.Event.addListener(elTarget, "click", YAHOO.dbill.container.simpledialog1.show, YAHOO.dbill.container.simpledialog1, true);
}

function initMsgDialog(id, role_nr){
    var enc = $('encounter_nr').value;
    var elTarget = 'dr'+id+'-'+role_nr;
    //Define various event handlers for dialog
    var handleYes = function (){
        //alert("You clicked yes!");
        xajax_rmPrivateDr(enc, id, role_nr, $('billdate').value);
        this.hide();
    };

    var handleNo = function(){
        this.hide();
    };

    // Instantiate the Dialog
    YAHOO.dbill.container.simpledialog1 = new YAHOO.widget.SimpleDialog("simpledialog1",
                                                                             { width: "300px",
                                                                                 fixedcenter: true,
                                                                                 visible: false,
                                                                                 draggable: false,
                                                                                 close: true,
                                                                                 text: "Do you want to continue?",
                                                                                 icon: YAHOO.widget.SimpleDialog.ICON_HELP,
                                                                                 constraintoviewport: true,
                                                                                 buttons: [ { text:"Yes", handler:handleYes, isDefault:true },
                                                                                            { text:"No", handler:handleNo } ]
                                                                             } );

    YAHOO.dbill.container.simpledialog1.setHeader("Delete PF ... ");
    YAHOO.dbill.container.simpledialog1.render(document.body);
    YAHOO.util.Event.addListener(elTarget, "click", YAHOO.dbill.container.simpledialog1.show, YAHOO.dbill.container.simpledialog1, true);
}// end of function initMsgDialog

function initMsgDialog2(id, role_nr){
        var enc = $('encounter_nr').value;
        var elTarget = 'dr'+id+'-'+role_nr;
        //Define various event handlers for dialog
        var handleYes = function (){
                //alert("You clicked yes!");
                xajax_rmDr(enc, id, role_nr, $('billdate').value);
                this.hide();
        };

        var handleNo = function(){
                this.hide();
        };

        // Instantiate the Dialog
        YAHOO.dbill.container.simpledialog1 = new YAHOO.widget.SimpleDialog("simpledialog1",
                                                                                                                                                         { width: "300px",
                                                                                                                                                                 fixedcenter: true,
                                                                                                                                                                 visible: false,
                                                                                                                                                                 draggable: false,
                                                                                                                                                                 close: true,
                                                                                                                                                                 text: "Do you want to continue?<br>NOTE: Deletion will remove the selected attending doctor.",
                                                                                                                                                                 icon: YAHOO.widget.SimpleDialog.ICON_HELP,
                                                                                                                                                                 constraintoviewport: true,
                                                                                                                                                                 buttons: [ { text:"Yes", handler:handleYes, isDefault:true },
                                                                                                                                                                                        { text:"No", handler:handleNo } ]
                                                                                                                                                         } );

        YAHOO.dbill.container.simpledialog1.setHeader("Delete PF ... ");
        YAHOO.dbill.container.simpledialog1.render(document.body);
        YAHOO.util.Event.addListener(elTarget, "click", YAHOO.dbill.container.simpledialog1.show, YAHOO.dbill.container.simpledialog1, true);
}// end of function initMsgDialog2

function showClassification(s_classification) {
    if (s_classification == '') s_classification = "NO CLASSIFICATION";
    $('sclassification').innerHTML = s_classification;
}

function addMiscOp(bill_dt, enc, code, rvu, multiplier, ops_charge, op_date) {
    startAddOp();
    xajax_chargeMiscProcedure(bill_dt, enc, code, rvu, multiplier, ops_charge, op_date);
}

function addMiscChrg(bill_dt, enc, code, acct_typ, misc_charge, qty) {
    xajax_chargeMiscChrg(bill_dt, enc, code, acct_typ, misc_charge, qty);
}

function addMoreMedorSupply(bill_dt, enc, code, area_code, uprice, qty) {
    xajax_chargeMedorSupply(bill_dt, enc, code, area_code, uprice, qty);
}

function addMiscService(bill_dt, enc, code, acct_typ, misc_charge, qty) {
    xajax_chargeMiscService(bill_dt, enc, code, acct_typ, misc_charge, qty);
}

function jsOnchangeConfineType() {
    var enc = $('encounter_nr').value;
    var type = $('confineTypeOption').options[$('confineTypeOption').selectedIndex].value;
    var bill_dte = $('bill_dte').value;
    var classify_id, create_id;
    var mem_category = $('mcategdesc').innerHTML;

    classify_id = $('classify_id').value;
    create_id =  $('classify_id').value;


    if(enc != '') {
        if (mem_category!=NBB && type==7) {
            alert('Please change Member Category first to SPONSORED MEMBER before selecting this case type');
            $('confineTypeOption').selectedIndex = 0;
        }else if(mem_category!=HSM && type==8) {
            alert('Please change Member Category first to HOSPITAL SPONSORED MEMBER before selecting this case type');
             $('confineTypeOption').selectedIndex = 0;
        }else{
            xajax_setConfinementType(enc, type, classify_id, create_id, bill_dte);
        }

        
    } else {
        alert('Please select patient first');
    }
}

function jsOnchangeCaseType() {
    var enc = $('encounter_nr').value;
    var type = $('caseTypeOption').options[$('caseTypeOption').selectedIndex].value;
    var bill_dte = $('bill_dte').value;
    var modify_id, create_id;

    modify_id = $('classify_id').value;
    create_id =  $('classify_id').value;

    if(enc != '') {
        //alert("selected options ="+ $('confineTypeOption').selectedIndex+ "\n selected value = "+ $('confineTypeOption').value );
        if (type == '0') {
          if (!confirm('WARNING: Case types set later than current bill date may be deleted!\nDo you want to continue?')) return;
        }

        xajax_setCaseType(enc, type, modify_id, create_id, bill_dte);
    } else {
        alert('Please select patient first');
    }
}

function getAccommodation(accAP, accDiscount, accHC, accEX){
    //alert("js getAccValue ="+ rm_nr + " type"+rm_type);
    $('accAP').innerHTML = accAP; // Display actual price charge
    $('accDiscount').innerHTML = accDiscount;
    $('accHC').innerHTML = accHC;
    $('accEX').innerHTML = accEX;
}//end of getAccValue

function getMedicine(medAP, medDiscount, medHC, medEX){
    $('medAP').innerHTML = medAP;
    $('medDiscount').innerHTML = medDiscount;
    $('medHC').innerHTML = medHC;
    $('medEX').innerHTML = medEX;
}// end of getMedicine

function getSupplies(supAP, supDiscount, supHC, supEX){
    $('supAP').innerHTML = supAP;
    $('supDiscount').innerHTML = supDiscount;
    $('supHC').innerHTML = supHC;
    $('supEX').innerHTML = supEX;
}

function showMiscSummary(mscAP, mscDiscount, mscHC, mscEX) {
    $('mscAP').innerHTML = mscAP;
    $('mscDiscount').innerHTML = mscDiscount;
    $('mscHC').innerHTML = mscHC;
    $('mscEX').innerHTML = mscEX;
}

function getHospitalServices(hsAP, hsDiscount, hsHC, hsEX){
    $('hsAP').innerHTML = hsAP;
    $('hsDiscount').innerHTML = hsDiscount;
    $('hsHC').innerHTML = hsHC;
    $('hsEX').innerHTML = hsEX;
}

function showOpsTotals(opsAP, opsDiscount, opsHC, opsEX) {
    $('opsAP').innerHTML = opsAP;
    $('opsDiscount').innerHTML = opsDiscount;
    $('opsHC').innerHTML = opsHC;
    $('opsEX').innerHTML = opsEX;
}

function showMedDetails(){
    $('showMedDetails').style.display = '';
    $('showMedDetails').innerHTML = 'Hide Details <-';
}

function showPFTotals(pfAP, pfDiscount, pfHC, pfEX) {
    $('pfAP').innerHTML = pfAP;
    $('pfDiscount').innerHTML = pfDiscount;
    $('pfHC').innerHTML = pfHC;
    $('pfEX').innerHTML = pfEX;
}

function getDiscount(discountAmnt){
    if (Number(discountAmnt) != 0) {
        $('classification_discount_row1').style.display = "";
        $('classification_discount_row2').style.display = "";

        $('td01').rowSpan = "5";
        $('td02').rowSpan = "5";
        $('td03').rowSpan = "5";
    }
    else {
        $('classification_discount_row1').style.display = "none";
        $('classification_discount_row2').style.display = "none";

        $('td01').rowSpan = "3";
        $('td02').rowSpan = "3";
        $('td03').rowSpan = "3";
    }
    $('bdiscount').innerHTML =  discountAmnt;
//  $('sclassification').innerHTML = discountid;
}

//added by jasper 04/01/2013
function showNoBalanceBilling(netamt) {
    if (Number(netamt) != 0) {
        //alert (netamt);
        $('td01').rowSpan = "6";
        $('td02').rowSpan = "6";
        $('td03').rowSpan = "6";
        $('nobalance').style.display= "";
        $('sponsored_amount').innerHTML = netamt;
    } else {
        $('nobalance').style.display= "none";
    }
}


//Added By Jarel for POC
function showPointOfCare(netamt) {
    if (Number(netamt) != 0) {
        $('td01').rowSpan = "6";
        $('td02').rowSpan = "6";
        $('td03').rowSpan = "6";
        $('poc').style.display= "";
        $('poc_amount').innerHTML = netamt;
    } else {
        $('poc').style.display= "none";
    }
}

function assignPreviousBilledAmount(prevamt) {
    //alert(prevamt);
    $('prev_billed_amt').value = prevamt;
}

function showPreviousBilledAmount() {
    //alert($('prev_billed_amt').value);
    if ($('prev_billed_amt').value != 0) {
        $('td01').rowSpan = "6";
        $('td02').rowSpan = "6";
        $('td03').rowSpan = "6";
        $('prevbill').style.display= "";
        $('prevbillamt').innerHTML = $('prev_billed_amt').value;
    } else {
        $('prevbill').style.display= "none";
    }
}
//added by jasper 04/01/2013

function getPreviousPayment(prevpayment){
    $('bdeposit').innerHTML = prevpayment;
}

function getTotalBillAmnt(amnt){
    $('netbill').innerHTML = amnt;
}

//'<td align="right" style="'+style+'">'+totalcoverage+'</td>'+
//                      '<td align="right" style="'+style+'">'+excess+'</td>'+
function jsMedicineList(tagId, bestellnum, artikelname,itemqty, itemprice, acPrice, flag, bill_frmdte, b_new) {
    var srcRow, sMsg;
    if (bestellnum) {
        if (flag)
            srcRow = '<tr id="code_'+bestellnum+'"><td align="center" width="3%"><img src="../../images/btn_delitem.gif" style="border-right:hidden; cursor:pointer; display:'+(b_new ? '' : 'none')+'" onclick="promptDelSupply(\''+bill_frmdte+'\', \''+bestellnum+'\', \''+artikelname+'\', \'MS\')"></td>'+
                        '<td style="border-left:hidden" width="52%">'+artikelname+'</td>';
        else
            srcRow = '<tr><td colspan="2" width="55%">'+artikelname+'</td>';

        srcRow += '<td width="15%" align="center">'+itemqty+'</td>'+
                    '<td width="15%" align="right">'+itemprice+'</td>'+
                    '<td width="15%" align="right">'+acPrice+'</td>'+
                    '</tr>';
    } else {
        if (tagId == 'body_mdListDetails')
            sMsg = "No medicines charged!";
        else
            sMsg = "No supplies charged!";
        srcRow = '<tr>'+
                    '<td colspan="2" width="55%">'+sMsg+'</td>'+
                    '<td width="15%">&nbsp;</td>'+
                    '<td width="15%">&nbsp;</td>'+
                    '<td width="15%">&nbsp;</td>'+
                '</tr>';
    }
    $(tagId).innerHTML += srcRow;
}

function jsClearList(tagId){
    $(tagId).innerHTML = '';
}

//("jsAccommodationList",$type_nr, $rm_nr, $type_desc,$days_count, $excess_hr , $rm_rate,$total_charge, $total_coverage, $excess);
function jsAccommodationList(type_nr, rm_nr, type_desc,days_count, excess_hr, rm_rate, total_charge, source, b_new){
    var srcRow, prefx = '';

    if (type_nr) {
        if (source == 'BL') {
            prefx = '<td align="center" width="3%"><img src="../../images/btn_delitem.gif" style="border-right:hidden; cursor:pointer; display:'+(b_new ? '' : 'none')+'" onclick="promptDelAccommodation('+type_nr+',\''+source+'\')"></td>'+
                    '<td style="border-left:hidden" width="52%">'+
                        'Room No. :'+rm_nr+'<br>'+
                        'Room Type:'+type_desc+
                    '</td>';
        }
        else {
            prefx = '<td align="center" width="3%"><img src="../../images/btn_delitem.gif" style="border-right:hidden; cursor:pointer; display:'+(b_new ? '' : 'none')+'" onclick="promptDelAccom('+type_nr+',\''+source+'\')"></td>'+
                    '<td style="border-left:hidden" width="52%">'+
                        'Room No. :'+rm_nr+'<br>'+
                        'Room Type:'+type_desc+
                    '</td>';
        }

        srcRow = '<tr id="type_'+type_nr+'_'+source+'">'+prefx+
                    '<td width="15%" align="center">'+
                        days_count+' Days <br>'+
                        excess_hr+' Hrs'+
                    '</td >'+
                    '<td width="15%" align="right">'+rm_rate+'</td>'+
                    '<td width="15%" align="right">'+total_charge+'</td>'+
                '</tr>';
    }else{
        srcRow = '<tr>'+
                    '<td colspan="2" width="55%">No accommodation charged!</td>'+
                    '<td width="15%">&nbsp;</td >'+
                    '<td width="15%">&nbsp;</td>'+
                    '<td width="15%">&nbsp;</td>'+
                '</tr>';
    }
    $('body_accListDetails').innerHTML += srcRow;
}


function jsHospitalServices(tagId, bill_frmdte, servCode, servDesc, servQty, servPrice, servCharge, servProvider, grpDesc, b_new){
    var prefx = '';

    var srcRow, provider ='';
    if (servCode) {
        switch (servProvider) {
            case 'LB':
                provider = 'LAB - '+ grpDesc;
                prefx = '<td colspan="2" width="38%">'+servDesc+'</td>';
                break;
            case 'RD':
                provider = 'RAD - '+ grpDesc;
                prefx = '<td colspan="2" width="38%">'+servDesc+'</td>';
                break;
            case 'SU':
                provider = grpDesc;
                prefx = '<td colspan="2" width="38%">'+servDesc+'</td>';
                break;
            case 'MS':
                provider = grpDesc;
                prefx = '<td align="center" width="3%"><img src="../../images/btn_delitem.gif" style="border-right:hidden; cursor:pointer; display:'+(b_new ? '' : 'none')+'" onclick="promptDelSupply(\''+bill_frmdte+'\', \''+servCode+'\', \''+escape(servDesc)+'\', \'HS\')"></td>'+
                        '<td style="border-left:hidden" width="35%">'+servDesc+'</td>';
                break;
            default:
                provider = 'Others'
                prefx = '<td align="center" width="3%"><img src="../../images/btn_delitem.gif" style="border-right:hidden; cursor:pointer; display:'+(b_new ? '' : 'none')+'" onclick="promptDelMiscService(\''+bill_frmdte+'\', \''+servCode+'\', \''+escape(servDesc)+'\')"></td>'+
                        '<td style="border-left:hidden" width="35%">'+servDesc+'</td>';
                break;
        }

        srcRow = '<tr id="code_'+servCode+'">'+prefx+
                    '<td width="17%" align="left">'+provider+'</td>'+
                    '<td width="15%" align="center">'+servQty+'</td>'+
                    '<td width="15%" align="right">'+servPrice+'</td>'+
                    '<td width="15%" align="right">'+servCharge+'</td>'+
                 '</tr>';
    } else {
        srcRow = '<tr>'+
                    '<td colspan="2" width="*">No hospital services charged!</td>'+
                    '<td width="17%">&nbsp;</td>'+
                    '<td width="15%">&nbsp;</td>'+
                    '<td width="15%">&nbsp;</td>'+
                    '<td width="15%">&nbsp;</td>'+
                 '</tr>';
    }

    $(tagId).innerHTML += srcRow;
}//end of getHSLab

function jsMiscellaneousList(bill_frmdte, code, name, description, qty, misc_chrg, b_new) {
    var srcRow;
    if (code) {
        var m = misc_chrg.replace(',', '');
        var n = qty.replace(',','');

        srcRow = '<tr id="code_'+code+'">'+
                    '<td align="center" width="3%"><img src="../../images/btn_delitem.gif" style="border-right:hidden; cursor:pointer; display:'+(b_new ? '' : 'none')+'" onclick="promptDelMiscChrg(\''+bill_frmdte+'\', \''+code+'\', \''+name+'\')"></td>'+
                    '<td style="border-left:hidden" width="52%"><span>'+name+'</span><br/><span class="description">'+description+'</span></td>'+
                    '<td width="15%" align="center">'+qty+'</td>'+
                    '<td width="15%" align="right">'+formatNumber(misc_chrg,2)+'</td>'+
                    '<td width="15%" align="right">'+formatNumber(Number(m) * Number(n),2)+'</td>'+
                 '</tr>';
    } else {
        srcRow = '<tr>'+
                    '<td colspan="2" width="*">No miscellaneous charges!</td>'+
                    '<td width="15%">&nbsp;</td>'+
                    '<td width="15%">&nbsp;</td>'+
                    '<td width="15%">&nbsp;</td>'+
                 '</tr>';
    }

    $('body_mscListDetails').innerHTML += srcRow;
}

function jsOpsList(bill_frmdte, scode, sdescription, n_rvu, n_multiplier, n_total, s_provider, b_new) {
    var srcRow, sMsg, sFunc;
    if (scode) {
        if (s_provider == 'RU') {
            var dBody=$("body_opsListDetails");
            var n = dBody.getElementsByTagName("tr").length+1;
            srcRow = '<tr id="op_code'+n+'_'+scode+'">';
        }
        else
            srcRow = '<tr id="op_code'+scode+'">';

        if (s_provider != 'OR') {
            switch (s_provider) {
                case 'OA':
                    sFunc = 'onclick="promptDelMiscOps(\''+bill_frmdte+'\', \''+scode+'\')">';
                    break;

                case 'RU':
                    sFunc = 'onclick="promptDelOpAccom(\''+bill_frmdte+'\', \''+scode+'\', '+n+')">';
                    break;
            }

            srcRow += '<td align="center" width="3%"><img src="../../images/btn_delitem.gif" style="border-right:hidden; cursor:pointer; display:'+(b_new ? '' : 'none')+'" '+sFunc+'</td>'+
                        '<td style="border-left:hidden" width="52%">'+sdescription+'</td>';
        }
        else
            srcRow += '<td colspan="2" width="55%">'+sdescription+'</td>';

        srcRow += '<td width="15%" align="center">'+n_rvu+'</td>'+
                    '<td width="15%" align="right">'+n_multiplier+'</td>'+
                    '<td width="15%" align="right">'+n_total+'</td>'+
                    '</tr>';
    }
    else {
        sMsg = "No O.R. accommodation charged!";
        srcRow = '<tr>'+
                    '<td colspan="2">'+sMsg+'</td>'+
                    '<td width="15%">&nbsp;</td>'+
                    '<td width="15%">&nbsp;</td>'+
                    '<td width="15%">&nbsp;</td>'+
                '</tr>';
    }
    $('body_opsListDetails').innerHTML += srcRow;
}

function jsDoctorsFees(tblId, roleId, roleDesc, totalCharge, Coverage){
    var srcRow;
    if(roleId){
        srcRow = '<tr>'+
                    '<td colspan="2" style="font-weight:bold">'+roleDesc+
                        '<table id="'+roleId+'" width="95%" border="0" cellpadding="1" cellspacing="0" align="right">'+
                        '</table>'+
                    '</td>'+
                    '<td align="right">'+totalCharge+'</td>'+
                    '<td align="right" id="coverage_'+roleId+'">'+Coverage+'</td>'+
                 '</tr>';

/*      srcRow = '<tr>'+
                    '<td>'+roleDesc+
                        '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                        '<table id="'+roleId+'" width="90%" border="1" cellpadding="1" cellspacing="0" align="right">'+
                        '</table>'+
                    '</td>'+
                    '<td><br>'+
                        '<table id="val'+roleId+'" width="100%" border="1" cellpadding="1" cellspacing="0" align="right">'+
                        '</table>'+
                    '</td>'+
                    '<td align="right">'+totalCharge+'</td>'+
                    '<td align="right">'+Coverage+'</td>'+
                 '</tr>';
*/
    }else{
        srcRow = '<tr>'+
                    '<td colspan="2">No professional fees charged!</td>'+
                    '<td align="right">&nbsp;</td>'+
                    '<td align="right">&nbsp;</td>'+
                 '</tr>';
    }
    $(tblId).innerHTML += srcRow;
}// end of jsDoctorsFee

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

//function jsCloseWindow() {
//    YAHOO.pbill.container.dialog1.hide();
//}
//added by jasper 11/16/12

function jsOptionChange(obj, value){
    //alert("tagid = " + obj.id + "value = " + value);
    switch (obj.id) {
        case 'doclist':
            //added by jasper 11/16/12
//            if (value==0) {
//               $('btnVerify').disabled = true;
//            }
//            else {
//               $('btnVerify').disabled = false;
//            }
            //added by jasper 11/16/12
            $('dr_nr').value  = value;
            assignDrCharge();
            break;

        case 'rolearea':
            $('role_nr').value = value;
            assignDrCharge();
            break;

        case 'role_level':
            $('tier_nr').value = value;
            assignDrCharge();
            break;
    }
}

function assignDrCharge() {
    var role_nr = $('role_nr').value;
    var enc = $('encounter_nr').value;
    var bill_dt = $('billdate').value;
    var ispkg = $('is_coveredbypkg').value;
    if (ispkg != '1') {
        var dr_nr = $('dr_nr').value;
        var tier_nr = $('tier_nr').value;
        var type = $('confineTypeOption').options[$('confineTypeOption').selectedIndex].value;
        var ndays = $('ndays').value;
        ndays = ndays.replace(',', '');

        xajax_assignDefaultCharge(dr_nr, role_nr, tier_nr, enc, bill_dt, type, ndays);
    }
    else {
        var pkgid = $('this_pkg').options[$('this_pkg').selectedIndex].value;
        xajax_assignDefaultPkgPF(pkgid, enc, role_nr, bill_dt, $('excluded').value);
    }
}

function setDrCharge(dr_charge) {
    $('charge').value = formatNumber(dr_charge, 2);
}

function setDaysAttended(ndays) {
    $('ndays').value = formatNumber(ndays, 0);
}

function jsAccOptionsChange(obj, value){
    if(obj.id == 'wardlist') {
        $('ward_nr').value  = value;
        //xajax_setWardRooms(value);
        //added condition by jane 10/29/2013
        if(Number(value)>0)
        xajax_setWardRooms(value);
        else{
            js_ClearOptions("roomlist");
            js_AddOptions("roomlist","- Select Room -", 0);
            assignRmRate('');
            $('days_stay').value = '';
            $('hrs_stay').value = '';
            $('occupydate').value = '';
        }
    }
    else {
        $('rm_nr').value = value;
        xajax_getRoomRate($('ward_nr').value, $('rm_nr').value);
    }
}

function jsOpAccChrgOptionsChange(obj, value){
    if(obj.id == 'opwardlist') {
        $('opw_nr').value  = value;
        xajax_setORWardRooms(value);
    }
    else {
        $('opr_nr').value = value;
        //xajax_getRoomRate($('ward_nr').value, $('rm_nr').value);
    }
}

function assignRmRate(rmrate) {
    $('rate').value = rmrate;
}

function jsCategoryOptionChange(obj, value, sdesc){
    if (obj.id== 'category_list') {
        $('categ_id').value   = value;
        $('categ_desc').value = sdesc;
    }
}

function keyPressHandler(event){
    var key = YAHOO.util.Event.getCharCode(event);

    if(key > 31 && (key <48 || key > 57)){
        Event.stop(event);
    }

    return true;
}

function genChkDecimal(obj, n){
    var objValue = obj.value;
    objValue = objValue.replace(',', '');

    if (objValue=="")
        return false;

    if (isNaN(objValue)) {
        alert("Invalid amount!");
        obj.value="0.00";
        obj.focus();
        return false;
    }

    n = n || 2;

    var nf = new NumberFormat();
    nf.setPlaces(n);
    nf.setNumber(objValue);

    obj.value = nf.toFormatted();
    return true;
}// end of function genChkDecimal

function genChkInteger(obj){
    var objValue = obj.value;

    if (objValue=="")
        return false;

    if (isNaN(objValue)) {
        alert("Invalid whole number!");
        obj.value="0";
        obj.focus();
        return false;
    }

    var nf = new NumberFormat();
    nf.setPlaces(0);
    nf.setNumber(objValue);

    obj.value = nf.toFormatted();
    return true;
}// end of function genChkDecimal

//added by VAN 02-19-09
function js_ViewDiagnosis(){
    var enc = $('encounter_nr').value;
    var pid = $('pid').value;
    var seg_URL_APPEND = $F('seg_URL_APPEND');

    if (enc != "" && pid != "") {
        return overlib(OLiframeContent('../../modules/registration_admission/seg-patient-icd-history.php'+seg_URL_APPEND+'&pid='+pid+'&encounter_nr='+enc+'&frombilling=1', 710, 395, 'fPrevCoverage', 0, 'auto'),
                        WIDTH, 710, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL, CLOSETEXT,
                        '<img src=../../images/close.gif border=0>', CAPTIONPADDING, 4, CAPTION, 'Diagnosis',
                        MIDX, 0, MIDY, 0, STATUS, 'View Diagnosis');
//      return overlib(OLiframeContent('../../modules/billing/seg-patient-diagnosis.php'+seg_URL_APPEND+'&pid='+pid+'&encounter_nr='+enc+'&frombilling=1', 710, 395, 'fPrevCoverage', 0, 'auto'),
//                      WIDTH, 710, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL, CLOSETEXT,
//                      '<img src=../../images/close.gif border=0>', CAPTIONPADDING, 4, CAPTION, 'Diagnosis',
//                      MIDX, 0, MIDY, 0, STATUS, 'View Diagnosis');
    }
    else {
        alert("Please specify patient!");
    }

}
//---------------------

//added by VAN 08-13-08
function js_EditInsurance() {
    var enc = $('encounter_nr').value;
    var pid = $('pid').value;
    var seg_URL_APPEND = $F('seg_URL_APPEND');
    var frm_dte = $('bill_frmdte').value;

    if (enc != "" && pid != "") {
        return overlib(OLiframeContent('../../modules/registration_admission/seg_insurance.php'+seg_URL_APPEND+'&encounter_nr='+enc+'&update=1&target=search&popUp=1&frombilling=1&pid='+pid, 720, 520, 'fPrevCoverage', 0, 'auto'),
            WIDTH, 720, HEIGHT,520, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL, CLOSETEXT,
                        '<img src=../../images/close.gif border=0>', CAPTIONPADDING, 4, CAPTION, 'Insurance',
                        MIDX, 0, MIDY, 0, STATUS, 'Update Insurance');
    }
    else {
        alert("Please specify patient!");
    }
}
//added by pol

function js_AddMiscService() {

    var enc = $('encounter_nr').value;
    var pid = $('pid').value;

    if (enc != "" && pid != "") {
        if (FinalBillCheck == 1){
                return overlib(OLiframeContent('billing-misc-services-tray.php', 725, 380, 'fMiscSrvTray', 0, 'auto'),
                WIDTH, 380, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL, CLOSETEXT,
                '<img src=../../images/close.gif border=0 >', CAPTIONPADDING, 4, CAPTION, 'Add Miscellaneous Service(s)',
                MIDX, 0, MIDY, 0, STATUS,'Add Miscellaneous Service(s)');
        }else{
            alert("Cannot add miscellaneous services because this case no. has final bill saved.");
        }
    }
    else {
        alert("Please specify patient!");
    }



}

function js_AddMiscOps() {
    var enc = $('encounter_nr').value;
    var pid = $('pid').value;

    if (enc != "" && pid != "") {
        if (FinalBillCheck == 1) {
                    return overlib(OLiframeContent('billing-misc-ops-tray.php', 725, 380, 'fOrderTray', 0, 'auto'),
                    WIDTH, 380, TEXTPADDING,0, BORDER,0,STICKY, SCROLL, CLOSECLICK, MODAL,CLOSETEXT,
                    '<img src=../../images/close.gif border=0 >',CAPTIONPADDING,4,CAPTION,'Add procedure codes from ICPM tray',
                    MIDX,0, MIDY,0,STATUS,'Add procedure codes from ICPM tray');
        }else{
            alert("Cannot add miscellaneous procedure because this case no. has final bill saved.");
        }
    }else {
        alert("Please specify patient!");
    }



}

function js_AddMiscChrg() {
    var enc = $('encounter_nr').value;
    var pid = $('pid').value;

    if (enc != "" && pid != "") {
        if (FinalBillCheck == 1) {
                    return overlib(OLiframeContent('billing-misc-chrgs-tray.php', 725, 380, 'fMiscChrgTray', 0, 'auto'),
                WIDTH, 380, TEXTPADDING,0, BORDER,0,STICKY, SCROLL, CLOSECLICK, MODAL,CLOSETEXT,
                '<img src=../../images/close.gif border=0 >',CAPTIONPADDING,4,CAPTION,'Add Miscellaneous Charge(s)',
                MIDX,0, MIDY,0,STATUS,'Add Miscellaneous Charge(s)');
        }else{
            alert("Cannot add miscellaneous charges because this case no. has final bill saved.");
        }
    }else {
        alert("Please specify patient!");
    }

}

function js_DiscountDetails() {
    var enc = $('encounter_nr').value;
    var pid = $('pid').value;

    if (enc != "" && pid != "") {
        if (FinalBillCheck == 1){
            return overlib(OLiframeContent('billing-discounts.php', 725, 380, 'fDiscTray', 0, 'auto'),
            WIDTH, 380, TEXTPADDING,0, BORDER,0,STICKY, SCROLL, CLOSECLICK, MODAL,CLOSETEXT,
            '<img src=../../images/close.gif border=0 >',CAPTIONPADDING,4,CAPTION,'Applicable Discounts',
            MIDX,0, MIDY,0,STATUS,'Applicable Discounts');
        }else{
            alert("Cannot add or delete discount because this case no. has final bill saved.");
        }
    }else {
        alert("Please specify patient!");
    }

}

//ended by pol
function applyRVUandMult(r, m, c) {
    var n = 0;
    if (Number(m) != Number(0)) {
        n = Number($('total_rvu').value);
        $('total_rvu').value = formatNumber(n + Number(r),2);
        $('multiplier').value = formatNumber(Number(m),2);
        $('oprm_chrg').value = formatNumber(Number(c),2);
    }
    else {
        $('total_rvu').value = '';
        $('multiplier').value = '';
        $('oprm_chrg').value = '';
    }
}

function remRVUandMult(r) {
    var n = 0;

    n = Number($('total_rvu').value);
    $('total_rvu').value = formatNumber(n- Number(r),2);
}

function initOPsTakenArray() {
    $('opstaken').innerHTML = '';
}

function saveOPTaken(rowval) {
    $('opstaken').innerHTML += rowval;
}

function updateRVUTotal() {
    var ops = $('opstaken').innerHTML;
    var enc_nr = $('encounter_nr').value;
    var bill_dt = $('billdate').value;
    var type = $('confineTypeOption').options[$('confineTypeOption').selectedIndex].value;

    xajax_updateRVUTotal(ops, enc_nr, bill_dt, type);
}

function openCoverages(mode) {
    var enc_nr = $('encounter_nr').value;
    var seg_URL_APPEND = $F('seg_URL_APPEND');
    var bill_nr = $('old_bill_nr').value;
    var bill_dt = $('billdate').value;

    if (enc_nr) {
        var url = '../../modules/insurance_co/seg_coverage_editor.php'+seg_URL_APPEND+'&mode='+mode+'&userck=aufnahme_user&encounter_nr='+enc_nr+'&bill_nr='+bill_nr+'&bill_dt='+(getDateFromFormat(bill_dt, 'yyyy-MM-dd HH:mm:ss')/1000)+'&from=CLOSE_WINDOW';
        overlib(
            OLiframeContent(url, 740, 400, 'fCoverages', 0, 'auto'),
            WIDTH,600, TEXTPADDING,0, BORDER,0,
            STICKY, SCROLL, CLOSECLICK, MODAL,
            CLOSETEXT, '<img src=../../images/close_red.gif border=0 >',
            CAPTIONPADDING,2,
            CAPTION,'Insurance coverages',
            MIDX,0, MIDY,0,
            STATUS,'Insurance coverages');
    }
    else {
        alert('No patient with confinement case selected...');
    }
    return false;
}

function setExcludedFlag() {
    $('excluded').value = ($('is_excluded').checked) ? '1' : '0';
}

function toggleCvrgAdjust(bClicked) {
    var enc_nr = $('encounter_nr').value;
    var p;

    if (typeof(bClicked) == 'undefined') bClicked = '0';

    if ($('is_cvrgadjusted').checked) {
        $('is_coveredbypkg').disabled = 'disabled';
        $('is_coveredbypkg').checked = false;

        $('cvrgadjusted_label').innerHTML = '<div id="cvrgtooltip" style="display:none">Edit distribution of insurance coverage</div><a style="cursor:pointer" onclick="openCvrgAdjustment();" onmouseover="return overlib($(\'cvrgtooltip\').innerHTML, LEFT);" onmouseout="return nd();"><u>Adjusted Coverage?</u></a>';
    }
    else {
        if ((bClicked == '1') && ($('is_adjusted').value == '1'))
            p = window.confirm("WARNING: Unchecking this will delete saved coverage adjustment.\nDo you really want to uncheck this?");
        else
            p = 1;
        if (p) {
            if ((bClicked == '1') && ($('is_adjusted').value == '1')) {
                xajax_removeCoverageAdjustments(enc_nr);
            }

            $('is_coveredbypkg').disabled = '';
            $('cvrgadjusted_label').innerHTML = 'Adjusted Coverage?';
        }
        else
            if ($('is_cvrgadjusted').value == '1') $('is_cvrgadjusted').checked = true;

    }
    $('is_cvrgadjusted').value = ($('is_cvrgadjusted').checked) ? '1' : '0';
}

function togglePkgControls(bClicked) {
    var enc_nr = $('encounter_nr').value;
    var bill_dt = $('billdate').value;
    var pkg_id  = $('bill_pkgid').value;
    var p;

    if (typeof(bClicked) == 'undefined') bClicked = '0';

    $('is_coveredbypkg').value = ($('is_coveredbypkg').checked) ? '1' : '0';
    if ($('is_coveredbypkg').checked) {
        $('is_cvrgadjusted').disabled = 'disabled';
        $('is_cvrgadjusted').checked = false;

        $('pkg_label').style.visibility = "";
        $('cvg_label').style.visibility = "";
        $('pkgcbo').style.visibility = "";
        $('pkgamnt').style.visibility = "";

        xajax_populatePkgCbo(pkg_id, enc_nr, bill_dt);
        xajax_showPkgCoveredAmount(pkg_id, enc_nr, bill_dt);
    }
    else {
        if ((bClicked == '1') && (Number(pkg_id) != 0))
            p = window.confirm("WARNING: Unchecking this will delete saved coverage distribution.\nDo you really want to uncheck this?");
        else
            p = 1;
        if (p || (bClicked == '0')) {
            $('pkg_label').style.visibility = "hidden";
            $('cvg_label').style.visibility = "hidden";
            $('pkgcbo').style.visibility = "hidden";
            $('pkgamnt').style.visibility = "hidden";
            if ((bClicked == '1') && (Number(pkg_id) != 0)) {
                xajax_removePkgDist(enc_nr);
            }
        }

        $('is_cvrgadjusted').disabled = '';
    }
}

function assignPkgOption(option) {
    var pkgcbo = '<select style="cursor:pointer" class="segInput" name="this_pkg" id="this_pkg" onchange="getPkgCoverageAmount();">\n'+option+'</select>\n';
    $('pkgcbo').innerHTML = pkgcbo;
}

function getPkgCoverageAmount() {
    var enc_nr = $('encounter_nr').value;
    var bill_dt = $('billdate').value;
    var pkg_id = $('this_pkg').options[$('this_pkg').selectedIndex].value;

    xajax_showPkgCoveredAmount(pkg_id, enc_nr, bill_dt);
}

function showPkgCoveredAmount(pkgamnt) {
    $('pkgamnt').innerHTML = formatNumber(pkgamnt, 2);
}

function assignPkgID(pkg_id) {
    if (typeof(pkg_id) == 'undefined') pkg_id = $('this_pkg').options[$('this_pkg').selectedIndex].value;
    if (pkg_id > 0)
        $('is_coveredbypkg').checked = 'checked';
    else
        $('is_coveredbypkg').checked = '';
    $('bill_pkgid').value = pkg_id;
}

function setCoverageAdjustedFlag(bflag) {
    if (bflag) {
        $('is_cvrgadjusted').checked = 'checked';
        $('is_adjusted').value = '1';
    }
    else {
        $('is_cvrgadjusted').checked = '';
        $('is_adjusted').value = '';
    }
    toggleCvrgAdjust('0');
}

function openPkgCoverage() {
    var enc_nr = $('encounter_nr').value;
    var seg_URL_APPEND = $F('seg_URL_APPEND');
    var bill_nr = $('old_bill_nr').value;
    var bill_dt = $('billdate').value;
    var pkg_id = $('this_pkg').options[$('this_pkg').selectedIndex].value;

    if (enc_nr) {
        var url = 'seg-pkgcoverage-editor.php'+seg_URL_APPEND+'&userck=aufnahme_user&encounter_nr='+enc_nr+'&bill_nr='+bill_nr+'&bill_dt='+(getDateFromFormat(bill_dt, 'yyyy-MM-dd HH:mm:ss')/1000)+'&from=CLOSE_WINDOW&pkg='+pkg_id;
        overlib(
            OLiframeContent(url, 740, 400, 'fCoverages', 0, 'auto'),
            WIDTH,600, TEXTPADDING,0, BORDER,0,
            STICKY, SCROLL, CLOSECLICK, MODAL,
            CLOSETEXT, '<img src=../../images/close_red.gif border=0 >',
            CAPTIONPADDING,2,
            CAPTION,'Package Coverages',
            MIDX,0, MIDY,0,
            STATUS,'Package Coverages');
    }
    else {
        alert('No patient selected yet!');
    }
    return false;
}

function openCvrgAdjustment() {
    var enc_nr = $('encounter_nr').value;
    var seg_URL_APPEND = $F('seg_URL_APPEND');
    var bill_nr = $('old_bill_nr').value;
    var bill_dt = $('billdate').value;

    if (enc_nr) {
        var url = 'seg-coverage-adjustment-editor.php'+seg_URL_APPEND+'&userck=aufnahme_user&encounter_nr='+enc_nr+'&bill_nr='+bill_nr+'&bill_dt='+(getDateFromFormat(bill_dt, 'yyyy-MM-dd HH:mm:ss')/1000)+'&from=CLOSE_WINDOW';
        overlib(
            OLiframeContent(url, 740, 400, 'fCoverages', 0, 'auto'),
            WIDTH,600, TEXTPADDING,0, BORDER,0,
            STICKY, SCROLL, CLOSECLICK, MODAL,
            CLOSETEXT, '<img src=../../images/close_red.gif border=0 >',
            CAPTIONPADDING,2,
            CAPTION,'Coverage Adjustment',
            MIDX,0, MIDY,0,
            STATUS,'Coverage Adjustment');
    }
    else {
        alert('No patient selected yet!');
    }
    return false;
}

function openDepositDistribution() {
    var enc_nr = $('encounter_nr').value;
    var seg_URL_APPEND = $F('seg_URL_APPEND');
    var bill_nr = $('old_bill_nr').value;
    var bill_dt = $('billdate').value;

    if (enc_nr) {
        var url = 'seg-deposit-editor.php'+seg_URL_APPEND+'&userck=aufnahme_user&encounter_nr='+enc_nr+'&bill_nr='+bill_nr+'&bill_dt='+(getDateFromFormat(bill_dt, 'yyyy-MM-dd HH:mm:ss')/1000)+'&from=CLOSE_WINDOW';
        overlib(
            OLiframeContent(url, 740, 400, 'fDistribution', 0, 'auto'),
            WIDTH,600, TEXTPADDING,0, BORDER,0,
            STICKY, SCROLL, CLOSECLICK, MODAL,
            CLOSETEXT, '<img src=../../images/close_red.gif border=0 >',
            CAPTIONPADDING,2,
            CAPTION,'Deposit Distribution',
            MIDX,0, MIDY,0,
            STATUS,'Deposit Distribution');
    }
    else {
        alert('No patient selected yet!');
    }
    return false;
}

function setDialysisFlag(flag) {
    $('is_dialysis').value = flag;
}

function setupCallback(area, b_all) {         // fix for HISSPMC-115
    var cb = xajax.callback.create();
    if (typeof(b_all) == 'undefined') b_all = 0;     // fix for HISSPMC-115
    switch (area) {
        case 'AC':
            cb.onComplete = function() {
                setDoneStatus('AC');
                toggleBillAreaStatus('AC',0);
                if (b_all) toggleBillAreaStatus('HS', 1, b_all);         // fix for HISSPMC-115
            }
            break;

        case 'HS':
            cb.onComplete = function() {
                setDoneStatus('HS');
                toggleBillAreaStatus('HS',0);
                if (b_all) toggleBillAreaStatus('MD', 1, b_all);        // fix for HISSPMC-115
            }
            break;

        case 'MD':
            cb.onComplete = function() {
                setDoneStatus('MD');
                toggleBillAreaStatus('MD',0);
                if (b_all) toggleBillAreaStatus('OP', 1, b_all);        // fix for HISSPMC-115
            }
            break;

        case 'OP':
            cb.onComplete = function() {
                setDoneStatus('OP');
                toggleBillAreaStatus('OP',0);
                if (b_all) toggleBillAreaStatus('PF', 1, b_all);        // fix for HISSPMC-115
            }
            break;

        case 'PF':
            cb.onComplete = function() {
                setDoneStatus('PF');
                toggleBillAreaStatus('PF',0);
                if (b_all) toggleBillAreaStatus('XC', 1, b_all);        // fix for HISSPMC-115
            }
            break;

        case 'XC':
            cb.onComplete = function() {
                setDoneStatus('XC');
                toggleBillAreaStatus('XC',0);
            }
            break;
    }
    return cb;
}

function toggleBillAreaStatus(area, bShow, b_all) {         // fix for HISSPMC-115
    tmp = '';
    switch (area) {
        case 'AC':
                    tmp = 'acc';
                    break;
        case 'HS':
                    tmp = 'hs';
                    break;
        case 'MD':
                    tmp = 'md';
                    break;
        case 'OP':
                    tmp = 'ops';
                    break;
        case 'PF':
                    tmp = 'pf';
                    break;
        case 'XC':
                    tmp = 'msc';
                    break;
    }

    if (typeof(b_all) == 'undefined')                        // fix for HISSPMC-115
        b_all = 0;                                             // fix for HISSPMC-115
    else
        b_all = Number(b_all);                                 // fix for HISSPMC-115
    if (Number(bShow)) {
        $(tmp+'ProgStatus').style.display = '';
        $(tmp+'ProgBar').style.display = '';
        switch (area) {
            case 'AC':
                        computeAccommodation(b_all);                  // fix for HISSPMC-115
                        break;
            case 'HS':
                        computeXLO(b_all);                            // fix for HISSPMC-115
                        break;
            case 'MD':
                        computeDrugsMeds(b_all);                     // fix for HISSPMC-115
                        break;
            case 'OP':
                        computeOP(b_all);                            // fix for HISSPMC-115
                        break;
            case 'PF':
                        computePF(b_all);                            // fix for HISSPMC-115
                        break;
            case 'XC':
                        computeMisc(b_all);                         // fix for HISSPMC-115
                        break;
        }
    }
    else {
        $(tmp+'ProgStatus').style.display = 'none';
        $(tmp+'ProgBar').style.display = 'none';
//      setDoneStatus(area);
    }
}

function setDoneStatus(area) {
    switch (area) {
        case 'AC':
//                  xajax_setActivityFlag('AC');
                    xajax.call('setActivityFlag', {
                        parameters: ['AC'],
                        asynchronous: false,
                        onComplete: function() {
                            acc_computed = 1;
//                          alert('Accommodation computed!');
                            showLastPart();
                        }
                    });
                    break;
        case 'HS':
//                  xajax_setActivityFlag('HS');
                    xajax.call('setActivityFlag', {
                        parameters: ['HS'],
                        asynchronous: false,
                        onComplete: function() {
                            xlo_computed = 1;
//                          alert('XLO computed!');
                            showLastPart();
                        }
                    });
                    break;
        case 'MD':
//                  xajax_setActivityFlag('MD');
                    xajax.call('setActivityFlag', {
                        parameters: ['MD'],
                        asynchronous: false,
                        onComplete: function() {
                            med_computed = 1;
//                          alert('Drugs and meds computed!');
                            showLastPart();
                        }
                    });
                    break;
        case 'OP':
//                  xajax_setActivityFlag('OP');
                    xajax.call('setActivityFlag', {
                        parameters: ['OP'],
                        asynchronous: false,
                        onComplete: function() {
                            ops_computed = 1;
//                          alert('Operation computed!');
                            showLastPart();
                        }
                    });
                    break;
        case 'PF':
//                  xajax_setActivityFlag('PF');
                    xajax.call('setActivityFlag', {
                        parameters: ['PF'],
                        asynchronous: false,
                        onComplete: function() {
                            pfs_computed = 1;
//                          alert('Professional fees computed!');
                            showLastPart();
                        }
                    });
                    break;
        case 'XC':
//                  xajax_setActivityFlag('XC');
                    xajax.call('setActivityFlag', {
                        parameters: ['XC'],
                        asynchronous: false,
                        onComplete: function() {
                            msc_computed = 1;
//                          alert('Miscellaneous computed!');
                            showLastPart();
                        }
                    });
                    break;
    }

//  showLastPart();
}

function showLastPart() {
    if (acc_computed && med_computed && xlo_computed && ops_computed && pfs_computed && msc_computed) {
//      xajax_doLastPartComputation();
        toggleLastPartStatus(1);

    }
}
//added by poliam
function FinalBill(final_B) {
    if (final_B==1){

         FinalBillCheck = 0;
         $('btnaccommodation').disabled = 'disabled';
         $('btnOPaccommodation').disabled = 'disabled';
         $('btnmedsandsupplies').disabled = 'disabled';
         $('btnadddoctors').disabled = 'disabled';
         $('btnaddmisc_ops').disabled = 'disabled';
         $('btnaddmisc_srvc').disabled = 'disabled';
         $('btnaddmisc_chrg').disabled = 'disabled';
         $('btnadd_discount').disabled = 'disabled';
    }else {

         FinalBillCheck = 1;
    }

}
//ended by poliam
function toggleLastPartStatus(bShow) {
    if (Number(bShow)) {

        $('amntlabel').style.display = 'none';
        $('lastProgBar').style.display = '';
        computeLastPart();


    }
    else {
        $('lastProgBar').style.display = 'none';
        $('amntlabel').style.display = '';

    }
}

function clearSessionVars(enc_nr, bill_dt, bill_frmdt, bill_nr) {
    if($('isdied').checked){
        var deathdate = $('deathdate').value;
    }else{
        var deathdate = '';
    }
    changeBilldate();
    xajax.call('clearSessionVars', {
        asynchronous: false,
        onComplete: function() {
            if (typeof(bill_frmdt) == 'undefined') bill_frmdt = '0000-00-00 00:00:00';
            if (typeof(bill_nr) == 'undefined') bill_nr = '';
            xajax_mainBilling(enc_nr, bill_dt, bill_frmdt, bill_nr,deathdate);
        }
    });
}

//added by jasper 04/25/2013
function showMedicoLegal(show_medico, medicoDesc) {
    if (show_medico==1) {
        $('medicolegal').style.display = "";
        $('ShowMedicoCases').value = medicoDesc;
    } else {
        $('medicolegal').style.display = "none";
    }
}
//added by jasper 04/25/2013
//added by pol 07/24/2013
function GetPhicNo(phic) {
    $('phic').value = phic;

}
//end by pol
//added by jasper 04/26/2013
function showInfirmaryDiscount(netamt) {
    if (Number(netamt) != 0) {
        //alert (netamt);
        $('td01').rowSpan = "6";
        $('td02').rowSpan = "6";
        $('td03').rowSpan = "6";
        $('infirmary').style.display= "";
        $('infirmary_amount').innerHTML = netamt;
    } else {
        $('infirmary').style.display= "none";
    }
}

//added by jasper 04/03/2013
function setBillNo(billnr) {
    //alert (billnr);
    $('old_bill_nr').value = billnr;
    xajax_showBilling(billnr);
}

function switchToViewMode() {
        var allInputs = $j(":input");
        for (var i = 0; i < allInputs.length; i++) {
            if (allInputs[i].id != 'IsDetailed')
                allInputs[i].disabled = "disabled";
        }

        //$j("#chrgslip").hide();
        $j("#chkboxrow").hide();

        $j('#bBody img').each(function(index) {
            this.hide();
        });

        YAHOO.util.Event.removeListener("btnSave", "click", js_SaveBilling);
        YAHOO.util.Event.removeListener("btnRecalc", "click", js_Recalculate);

        $j("#btnSave").attr('src', '../../images/btn_newbill.gif');
        $j("#btnSave").attr('id','btnNew');

        $j("#btnRecalc").attr('src', '../../images/btn_delete.gif');
        $j("#btnRecalc").attr('id','btnDelete');

        $j("#btnInsurance").hide();
        $j("#btnPrevCoverage").hide();
        $j("#btnDiagnosis").hide();
        //added by pol 10/05/2013
        $j("#btnPrevPack").hide();
        $j("#memcateg").hide();

        YAHOO.util.Event.addListener("btnNew", "click", js_NewBilling);
        YAHOO.util.Event.addListener("btnDelete", "click", js_DeleteBilling);

        $j("#medcvrg").attr('onclick','return false;');
        $j("#xlocvrg").attr('onclick','return false;');
}
//added by jasper 04/03/2013

//Added by Jarel 05/16/2013 Toggle Death Date
function toggleDeathDate() {
    if($('isdied').checked){
        $('label_deathdate').style.display = "";
        $('input_deathdate').style.display = "";
    }else if(!$('isdied').checked && $(encounter_nr).value){
        $('label_deathdate').style.display = "none";
        $('input_deathdate').style.display = "none";
        xajax_UnsetDeathDate($('pid').value,$('encounter_nr').value)
        js_Recalculate();
    }
    changeBilldate();
}

function changeBilldate(){
    if($('isdied').checked){
        $('bill_dte').value = $('deathdate').value;
    }else{
        $('bill_dte').value = $('billdate').value;
    }
}

//added by pol 10/05/2013
function js_displayPrevPack(){
    var encnr = $('encounter_nr').value;
    var p_id = $('pid').value;

    if (!encnr) {
        alert('No patient selected...');
        return false;
    }
    else {
        return overlib(OLiframeContent('seg_previews_package.php?enc_nr='+encnr+'&pid='+p_id, 640, 380, 'fMiscFees', 0, 'auto'),
            WIDTH,600, TEXTPADDING,0, BORDER,0,
            STICKY, SCROLL, CLOSECLICK, MODAL,
            CLOSETEXT, '<img src=../../images/close_red.gif border=0 >',
            CAPTION,'Previous Billing Package',
            MIDX,0, MIDY,0,
            STATUS,'Previous Billing Package');
    }
}
//end by pol 10/05/2013

//Added by Jarel 12/12/13
function setHasBloodTrans(bool)
{
    $('hasbloodborrowed').value = bool;
}
