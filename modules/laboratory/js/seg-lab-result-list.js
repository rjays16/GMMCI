/*function preset(){
  
    $J('#Search').bind('keyup', function() {
      //if ((event.keyCode == 13)&&(isValidSearch($J('#Search').val()))) getReports();
      getReports();
    });
    
}*/

function isValidSearch(key) {

        if (typeof(key)=='undefined') return false;
        var s=key.toUpperCase();
        return (
                        /^[A-Z0-9Ñ\-\.]{2}[A-Z0-9Ñ\-\. ]*\s*,\s*[A-Z0-9Ñ\-\.]{2}[A-Z0-9Ñ\-\. ]*$/.test(s) ||
                        /^\d{1,2}\/\d{1,2}\/\d{4}$/.test(s) ||
                        /^\d{1,2}\-\d{1,2}\-\d{4}$/.test(s) ||
                        /^\d+$/.test(s)
        );
}

function DisabledSearch(){
        var b=isValidSearch($('Search').value);
        $("searchButton").style.cursor=(b?"pointer":"default");
        $("searchButton").disabled = !b;
}

function viewResult(pid,refno,group_id,code){
    // window.open("seg-lab-result-view.php?filename="+filename+"&showBrowser=1","viewPatientResult","left=150, top=100, width=800,height=600,menubar=no,resizable=yes,scrollbars=yes");
    window.open("../../modules/repgen/pdf_lab_results.php?pid="+pid+"&refno="+refno+"&group_id="+group_id+"&service_code="+code,"viewPatientResult","left=150, top=100, width=620,height=440,menubar=no,resizable=yes,scrollbars=yes");
}

function viewParsedResult(pid, lis_order_no){
    //window.open("seg-lab-parsedresult-view.php?pid="+pid+"&lis_order_no="+lis_order_no+"&showBrowser=1","viewPatientResult","left=150, top=100, width=800,height=600,menubar=no,resizable=yes,scrollbars=yes");
    return overlib(
                    OLiframeContent("seg-lab-parsedresult-view.php?pid="+pid+"&lis_order_no="+lis_order_no, 1000, 450, "fOrderTray", 1, "auto"),
                                                                    WIDTH,450, TEXTPADDING,0, BORDER,0,
                                                                        STICKY, SCROLL, CLOSECLICK, MODAL,
                                                                        CLOSETEXT, "<img src=../../images/close.gif border=0 >",
                                                                 CAPTIONPADDING,4, CAPTION,"Laboratory Result",
                                                                 MIDX,0, MIDY,0,
                                                                 STATUS,"Laboratory Result");
}

function getReports() {
    searchSource();
}
