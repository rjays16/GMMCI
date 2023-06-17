<?php
/**
* SegHIS  ....
*/
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

define('NO_2LEVEL_CHK',1);
define('LANG_FILE','products.php');
$local_user='ck_transmittal_db_user';

require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'modules/billing/ajax/bill-list.common.php');

$GLOBAL_CONFIG=array();
# Create global config object
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/inc_date_format_functions.php');

$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
if($glob_obj->getConfig('date_format')) $date_format=$GLOBAL_CONFIG['date_format'];
$date_format=$GLOBAL_CONFIG['date_format'];
$phpfd=$date_format;
$phpfd=str_replace("dd", "%d", strtolower($phpfd));
$phpfd=str_replace("mm", "%m", strtolower($phpfd));
$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
$phpfd=str_replace("yy","%y", strtolower($phpfd));

$php_date_format = strtolower($date_format);
$php_date_format = str_replace("dd","d",$php_date_format);
$php_date_format = str_replace("mm","m",$php_date_format);
$php_date_format = str_replace("yyyy","Y",$php_date_format);
$php_date_format = str_replace("yy","y",$php_date_format);

$breakfile=$root_path.'modules/system_admin/edv.php'.URL_APPEND."&userck=$userck";

//$db->debug=1;

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme
 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

// $smarty->assign('bHideTitleBar',TRUE);
// $smarty->assign('bHideCopyright',TRUE);

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"Audit Trail Reports");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
# $smarty->assign('pbHelp',"javascript:gethelp('products_db.php','search','$from','$cat')");
 $smarty->assign('pbHelp',"javascript:gethelp('billing-transmittal-hist.php')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"Audit Trail");

 # Assign Body Onload javascript code
if (($_REQUEST["seldate"] == "between") || ($_REQUEST["seldate"] == "thismonth")) {
    $smarty->assign('sOnLoadJs','onLoad="keepFilters(2);"');
}
else
    $smarty->assign('sOnLoadJs','');

 # Collect javascript code
 ob_start()



?>
<!-- OLiframeContent(src, width, height) script:
 (include WIDTH with its parameter equal to width, and TEXTPADDING,0, in the overlib call)
-->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>

<!-- Core module and plugins:
-->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<style type="text/css">
/*margin and padding on body element
    can introduce errors in determining
    element position and are not recommended;
    we turn them off as a foundation for YUI
    CSS treatments. */
body {
        margin:0;
        padding:0;
}
</style>

<script type="text/javascript" src="<?= $root_path ?>js/yui-2.7/yahoo/yahoo.js"></script>
<link rel="stylesheet" type="text/css" href="<?= $root_path ?>js/yui-2.7/fonts/fonts-min.css" />
<link rel="stylesheet" type="text/css" href="<?= $root_path ?>js/yui-2.7/autocomplete/assets/skins/sam/autocomplete.css" />
<script type="text/javascript" src="<?= $root_path ?>js/yui-2.7/yahoo-dom-event/yahoo-dom-event.js"></script>

<script type="text/javascript" src="<?= $root_path ?>js/yui-2.7/connection/connection-min.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/yui-2.7/animation/animation-min.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/yui-2.7/datasource/datasource-min.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/yui-2.7/autocomplete/autocomplete-min.js"></script>

<!--begin custom header content for this example-->
<style type="text/css">
#hcAutoComplete {
        width:25em; /* set width here or else widget will expand to fit its container */
        padding-bottom:1.75em;
}
</style>

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/datefuncs.js"></script>

<!--added by VAN -->
<link rel="stylesheet" href="<?= $root_path ?>js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css" />
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/ui/jquery-ui.js"></script>
<!---->

<script language="javascript" type="text/javascript">
<!--
        //added by VAN 11-19-2012
        var $J = jQuery.noConflict();

        function pSearchClose() {
                cClick();
        }

        function disableNav() {
                with ($('pageFirst')) {
                        className = 'segDisabledLink'
                        setAttribute('onclick','')
                }
                with ($('pagePrev')) {
                        className = 'segDisabledLink'
                        setAttribute('onclick','')
                }
                with ($('pageNext')) {
                        className = 'segDisabledLink'
                        setAttribute('onclick','')
                }
                with ($('pageLast')) {
                        className = 'segDisabledLink'
                        setAttribute('onclick','')
                }
        }

        var djConfig = { isDebug: true };
        var FIRST_PAGE=1, PREV_PAGE=2, NEXT_PAGE=3, LAST_PAGE=4, SET_PAGE=0;

        function jumpToPage(jumptype, page) {
                var form1 = document.forms[0];

                switch (jumptype) {
                        case FIRST_PAGE:
                                $('jump').value = 'first';
                        break;
                        case PREV_PAGE:
                                $('jump').value = 'prev';
                        break;
                        case NEXT_PAGE:
                                $('jump').value = 'next';
                        break;
                        case LAST_PAGE:
                                $('jump').value = 'last';
                        break;
                        case SET_PAGE:
                                $('jump').value = page;
                        break;
                }

                form1.submit();
        }

        function deleteItem(id) {
                var dform = document.forms[0]
                $('delete').value = id
                dform.submit()
        }

        function validate() {
                return true;
        }

        function keepFilters(noption) {
                var filter = '';

                //$('btnPrint').style.display = "";
                switch (noption) {
                        case 0:
                                xajax_updateFilterOption(0, ($('chkinsurance').checked) ? true : false);
                                filter_array = [];
                                filter_array[0] = $('insurance').value;
                                filter_array[1] = $('hcare_name').value;
                                if ($('chkinsurance').checked) xajax_updateFilterTrackers('insurance', filter_array);
                                break;

                        case 1:
                                if ($('chkspecific').checked) {
                                        var opt = $('selrecord').options[$('selrecord').selectedIndex];
                                        filter = $(opt.value).value;
                                        xajax_updateFilterOption(1, true);
                                        xajax_updateFilterTrackers($('selrecord').value, filter);
                                }
                                else
                                        xajax_updateFilterOption(1, false);
                                break;

                        case 2:
                                if ($('chkdate').checked) {
                                        if ($('seldate').value == 'specificdate') {
                                                filter = $('specificdate').value;
                                        }
                                        if ($('seldate').value == 'between') {
                                                filter = new Array($('between1').value, $('between2').value);
                                                //$('btnPrint').style.display = "";
                                        }
                                        if ($('seldate').value == 'thismonth') //$('btnPrint').style.display = "";

                                        xajax_updateFilterOption(2, true);
                                        xajax_updateFilterTrackers($('seldate').value, filter);
                                }
                                else
                                        xajax_updateFilterOption(2, false);
                }
                clearPageTracker();
        }

        function keepPage() {
                var pg = $('page').value;
                xajax_updatePageTracker(pg);
        }

        function clearPageTracker() {
                xajax_clearPageTracker();
        }

        function toggleStatus(transmit_no, enc_nr, reject) {
                xajax_toggleTransmittal(transmit_no, enc_nr, reject);
        }

        function refreshStatus(transmit_no, enc_nr, is_rejected) {
                $('status').innerHTML = '<a title="Toggle Audit status!" href="#">'+
                                                                        '<img class="segSimulatedLink" src="../../images/'+(is_rejected ? 'claim_notok.gif' : 'claim_ok.gif')+'" border="0" align="absmiddle" onclick="toggleStatus(\''+transmit_no+'\', \''+enc_nr+'\', '+((is_rejected) ? '0': '1')+');"/>'+
                                                                '</a>';
        }

        function printTransmittalRep() {
            var rpath = $('root_path').value;
            var seg_URL_APPEND = $F('seg_URL_APPEND');
            var dteobj;
            var date1='';
            var date2='';
            var specValue='';
            var specDate='';
            var specific = '';
           // var show = data;



            if ($('chkspecific').checked) {
                                        if ($('selrecord').value == 'name'){
                                            specific = 'name';
                                            specValue = $('name').value;
                                        }
                                        else if ($('selrecord').value == 'case_no'){
                                            specific = 'case_no';
                                            specValue = $('case_no').value;

                                        }

            }



            if ($('chkdate').checked) {
                                        if ($('seldate').value == 'specificdate') {
                                                specDate = $('specificdate').value;
                                        }
                                        if ($('seldate').value == 'between') {
                                                specDate = 'between';
                                                date1 = $('between1').value;
                                                date2 = $('between2').value;

                                        }
                                        if ($('seldate').value == 'today')
                                                specDate = 'today';
                                        if ($('seldate').value == 'thisweek')
                                                specDate = 'thisweek';
                                        if ($('seldate').value == 'thismonth')
                                                specDate = 'thismonth';
            }

            
               // urlholder = rpath+'modules/repgen/pdf_audit_trail.php'+seg_URL_APPEND+'&detailed=0&fromdte='+(getDateFromFormat(date1, 'MM/dd/yyyy')/1000)+'&todte='+(getDateFromFormat(date2, 'MM/dd/yyyy')/1000);
                  urlholder = rpath+'modules/repgen/pdf_audit_trail.php'+seg_URL_APPEND+'&spec='+specific+'&sValue='+specValue+'&sDate='+specDate+'&sDate1='+date1+'&sDate2='+date2;

                nleft = (screen.width - 680)/2;
                ntop = (screen.height - 520)/2;
                printwin = window.open(urlholder, "Audit Report", "toolbar=no, status=no, menubar=no, width=700, height=500, location=center, dependent=yes, resizable=yes, scrollbars=yes, top=" + ntop + ",left=" + nleft);

            return true;
        }

        //added by VAN 11-19-2012

        function viewClaimStatusPDF(obj){
            window.open("../../modules/billing/pdf/batch_claim_status.php?series="+obj+"&showBrowser=1","viewClaimStatus","left=150, top=100, width=950,height=700,fullscreen=yes,menubar=no,resizable=yes,scrollbars=yes");
        }

        function viewClaimStatus(transmit_no, enc_nr){
             //alert('viewClaimStatus = '+transmit_no+' == '+enc_nr);
             $J.ajax({
                url: '../../modules/billing/seg-getClaimLHIO.php',
                type: 'GET',
                data: ({'encounter_nr':enc_nr,'transmit_no':transmit_no}),
                datatype: "json",
                beforeSend: function() {},
                success: function(data) {
                   var obj = jQuery.parseJSON(data);
                   viewClaimStatusPDF(obj.pSeriesLhioNos);
                },
                error: function() {}
            });
        }

-->
</script>

<?php

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

$xajax->printJavascript($root_path.'classes/xajax');

# Buffer page output
include("./class/class_audit_trail.php");
include($root_path."include/care_api_classes/class_person.php");

$objtransmit = new auditTrail();

if (!$_POST["applied"]) {
        if (isset($_SESSION["filteroption"])) {
                if (isset($_SESSION["filteroption"][0])) $_REQUEST["chkinsurance"] = strcmp($_SESSION["filteroption"][0], 'true') == 0;
                if (isset($_SESSION["filteroption"][1])) $_REQUEST["chkspecific"] = strcmp($_SESSION["filteroption"][1], 'true') == 0;
                if (isset($_SESSION["filteroption"][2])) $_REQUEST["chkdate"] = strcmp($_SESSION["filteroption"][2], 'true') == 0;
        }

        if (isset($_SESSION["filtertype"])) {
                switch (strtolower($_SESSION["filtertype"])) {
                        case "insurance":
                                $_REQUEST["insurance"] = $_SESSION["filter"][0];
                                $_REQUEST["hcare_name"] = $_SESSION["filter"][1];
                                break;
                        case "name":
                        case "case_no":
                                $_REQUEST["selrecord"] = $_SESSION["filtertype"];
                                $_REQUEST[strtolower($_SESSION["filtertype"])] = $_SESSION["filter"];
                                break;

                        default:
                                $_REQUEST["seldate"] = $_SESSION["filtertype"];
                                if (is_array($_SESSION["filter"])) {
                                        $_REQUEST["between1"] = $_SESSION["filter"][0];
                                        $_REQUEST["between2"] = $_SESSION["filter"][1];
                                }
                                else
                                        if ($_SESSION["filter"] != "")
                                                $_REQUEST["specificdate"] = $_SESSION["filter"];
                }
        }
        else {
                if (is_null($_SESSION["filteroption"])) $_REQUEST['chkdate'] = true;
                $_REQUEST["seldate"] = "today";
        }
}

if (isset($_SESSION["current_page"])) {
        $_REQUEST['page'] = $_SESSION["current_page"];
}

//echo var_export(strcmp($_REQUEST["chkspecific"], 'true') == 0 ? 'true' : 'false', true);

#}
#else
#    $_REQUEST["seldate"] = "today";

$title_sufx = 'Audit Trail';


if ($_REQUEST['chkdate']) {
        switch(strtolower($_REQUEST["seldate"])) {
                case "today":
                        $search_title = "Today's $title_sufx";
                        $filters['DATETODAY'] = "";
                break;
                case "thisweek":
                        $search_title = "This Week's $title_sufx";
                        $filters['DATETHISWEEK'] = "";
                break;
                case "thismonth":
                        $search_title = "This Month's $title_sufx";
                        $filters['DATETHISMONTH'] = "";
                break;
                case "specificdate":
                        $search_title = "$title_sufx On " . date("F j, Y",strtotime($_REQUEST["specificdate"]));
                        $dDate = date("Y-m-d",strtotime($_REQUEST["specificdate"]));
                        $filters['DATE'] = $dDate;
                break;
                case "between":
                        $search_title = "$title_sufx From " . date("F j, Y",strtotime($_REQUEST["between1"])) . " To " . date("F j, Y",strtotime($_REQUEST["between2"]));
                        $dDate1 = date("Y-m-d",strtotime($_REQUEST["between1"]));
                        $dDate2 = date("Y-m-d",strtotime($_REQUEST["between2"]));
                        $filters['DATEBETWEEN'] = array($dDate1,$dDate2);
                break;
        }
}

if ($_REQUEST['chkspecific']) {
        switch(strtolower($_REQUEST["selrecord"])) {
                case "name":
                        $search_title = "Audit trail with patient's name having ".$_REQUEST['name'];
                        $filters["NAME"] = $_REQUEST["name"];
                break;
                case "case_no":
                        $search_title = "Audit Trail having case no. ".$_REQUEST['case_no'];
                        $filters["CASE_NO"] = $_REQUEST["case_no"];
                break;
        }
}

//if ($_REQUEST['chkarea']) {
//    $filters["AREA"] = $_REQUEST["selarea"];
//}

$current_page = $_REQUEST['page'];
if (!$current_page) $current_page = 0;
$list_rows = 10;
switch (strtolower($_REQUEST['jump'])) {
        case 'last':
                $current_page = $_REQUEST['lastpage'];
        break;
        case 'prev':
                if ($current_page > 0) $current_page--;
        break;
        case 'next':
                if ($current_page < $_REQUEST['lastpage']) $current_page++;
        break;
        case 'first':
                $current_page=0;
        break;
}

$_SESSION["current_page"] = $current_page;

$show = $objtransmit->getETransmittalDetails($filters, $list_rows * $current_page, $list_rows);

$rows = "";
$last_page = 0;
$count=0;
if ($show) {
        $rows_found = $objtransmit->getETransmittalDetailsCount();
        if ($rows_found) {
                $last_page = floor($rows_found / $list_rows);
                $first_item = $current_page * $list_rows + 1;
                $last_item = ($current_page+1) * $list_rows;
                if ($last_item > $rows_found) $last_item = $rows_found;
                $nav_caption = "Showing ".number_format($first_item)."-".number_format($last_item)." out of ".number_format($rows_found)." record(s)";
        }

        $objperson = new Person();
        while ($row = $show->FetchRow()) {
                if (!$records_found) $records_found = TRUE;

                $user = $row["name"];
                
                $Fname = $row["Fname"];
                $Lname = $row["Lname"];
                $date = date("M d,Y h:ia",strtotime($row["date_changed"]));
                $pkValue = $row["pk_value"];
                $pkfield = $row["pk_field"];
                $encounter_nr = $row["encounter_nr"];
                
                $fields = explode('+',$row['field_c']);
                $newValues = explode('+', $row["new_value"]);
                $oldValues = explode('+', $row["old_value"]);
                
//                $fieldc = count($fields);
//                $newValuesc = count($newValues);
//                $oldValuesc = count($oldValues);

                //for($i=0;$i<=$fieldc;$i++){
//                    $fields_v .= "<hr />".$fields[$i]."";
//                }

  //
//                for($i=0;$i<=$newValuesc;$i++){
//                    $newValues_v .= "<hr />".$newValues[$i]."";
//                }
//
//                for($i=0;$i<=$oldValuesc;$i++){
//                    $oldValues_v .= "<hr />".$oldValues[$i]."";
//                }


                
               
               $rows .= "<tr class=\"$class\">
                                            <td align=\"center\">".$user."</td>
                                            <td align=\"center\">".$Fname." ".$Lname."</td>
                                            <td align=\"left\">";
               $counter=0;
               $religion=0;
               $counter_rel=0;
               $country = 0;
               $cCountry = 0;
               while($fields[$counter]){
                    $rows .= $fields[$counter]."<br />";
                    if($fields[$counter]=="Religion"){
                        $religion=1;
                        $counter_rel=$counter;
                    }
                    
                    if($fields[$counter] == "Country of Nationality"){
                        $country = 1;
                        $cCountry = $counter;
                    }
                    $counter++;
               }
               
               $rows .="</td><td align=\"left\">";
               $rows .= $objtransmit->getRows($religion,$country,$counter_rel,$cCountry,$oldValues);
               $rows .="</td><td align=\"left\">";
               $rows .= $objtransmit->getRows($religion,$country,$counter_rel,$cCountry,$newValues);
               $rows .="</td><td align=\"center\">".$date."</td>
                        <td align=\"center\">".$pkValue."</td>
                        <td align=\"center\">".$encounter_nr."</td>
                        ".$btns."</tr>";

        }
}

else {
//    print_r($result);
//    $rows .= '        <tr><td colspan="9">'.$objtransmit->sql.'</td></tr>';
    $sWarning = $objtransmit->error_msg;
}

if (!$rows) {
        $records_found = FALSE;
        $rows .= '        <tr><td colspan="9">No Audit Trail History Found...</td></tr>';
}

ob_start();
?>
<form action="<?= $thisfile.URL_APPEND."&target=list&clear_ck_sid=".$clear_ck_sid.$src_link ?>" method="post" name="suchform" onSubmit="return validate()">
<div style="margin:5px;font-weight:bold;color:#660000"><?= $sWarning ?></div>
<div style="width:70%">
        <table width="100%" border="0" style="font-size: 12px; margin-top:5px" cellspacing="2" cellpadding="2">
                <tbody>
                        <tr>
                                <td align="left" class="jedPanelHeader" ><strong>Search options</strong></td>
                        </tr>
                        <tr>
                                <td nowrap="nowrap" align="left" class="jedPanel">
                                        <table width="100%" border="0" cellpadding="2" cellspacing="0">
                                                <!--<tr>
                                                        <td width="50" align="right">
                                                                <input type="checkbox" id="chkinsurance" name="chkinsurance" onclick="insuranceOnChange(); keepFilters(0);" <?= ($_REQUEST['chkinsurance'] ? 'checked' : '') ?>/>
                                                        </td>
                                                        <td width="5%" align="right" nowrap="nowrap">Health Insurance</td>
                                                        <td colspan="2">  -->
<script language="javascript" type="text/javascript">
//<!--
//        function insuranceOnChange() {
//                var span = document.getElementsByName('insurance_name');
//                span[0].style.display = $('chkinsurance').checked ? "" : "none";
//                disableNav()
//        }
//-->
</script>
                                                <!--                <span name="insurance_name" segOption="insurance" <?= $_REQUEST['chkinsurance'] ? '' : 'style="display:none"' ?>>
                                                                     <div id="hcAutoComplete">
                                                                                <input class="jedInput" type="text" size="25" onblur="keepFilters(0);" value="<?= $_REQUEST['hcare_name'] ?>" id="hcare_name" name="hcare_name"/>
                                                                                <div id="hcContainer" style="width:25em"></div>
                                                                     </div>
                                                                     <input type="hidden" name="insurance" id="insurance" type="text" size="30" value="<?= $_REQUEST['insurance'] ?>"/>
                                                                </span>
                                                        </td>
                                                </tr>-->
                                                <tr>
                                                        <td width="50" align="right">
                                                                <input type="checkbox" id="chkspecific" name="chkspecific" onclick="selrecordOnChange();keepFilters(1);disableSearch();" <?= ($_REQUEST['chkspecific'] ? 'checked' : '') ?>/>
                                                        </td>
                                                        <td width="5%" align="left" nowrap="nowrap">Patient Name/HRN/Case No.</td>
                                                        <td>
<script language="javascript" type="text/javascript">
<!--
        function selrecordOnChange() {
                var optSelected = $('selrecord').options[$('selrecord').selectedIndex];
                var spans = document.getElementsByName('selrecordoptions');

                for (var i=0; i<spans.length; i++) {
                        if (optSelected) {
                                if (spans[i].getAttribute("segOption") == optSelected.value) {
                                        spans[i].style.display = $('chkspecific').checked ? "" : "none";
                                }
                                else
                                        spans[i].style.display = "none";
                        }
                }

                disableNav()
        }


-->
</script>
                                                                <select class="jedInput" name="selrecord" id="selrecord" onchange="selrecordOnChange(); keepFilters(1);"/>
                                                                        <option value="name" <?= $_REQUEST["selrecord"]=="name" ? 'selected="selected"' : '' ?>>Patient Name</option>
                                                                        <option value="case_no" <?= $_REQUEST["selrecord"]=="case_no" ? 'selected="selected"' : '' ?>>HRN</option>
                                                                </select>
                                                                <td>
                                                                <span name="selrecordoptions" segOption="name" <?= ($_REQUEST["selrecord"]=="name") && $_REQUEST['chkspecific'] ? '' : 'style="display:none"' ?>>
                                                                        <input class="jedInput" name="name" id="name" onkeyup="disableSearch();" onblur="keepFilters(1);" type="text" size="30" value="<?= $_REQUEST['name'] ?>"/>
                                                                        <input type="hidden" name="name_old" value="<?= $_REQUEST['name'] ?>" />
                                                                </span>
                                                                <span name="selrecordoptions" segOption="case_no" <?= ($_REQUEST["selrecord"]=="case_no") && $_REQUEST['chkspecific'] ? '' : 'style="display:none"' ?>>
                                                                        <input class="jedInput" name="case_no" id="case_no" onkeyup="disableSearch();" onblur="keepFilters(1);" type="text" size="30" value="<?= $_REQUEST['case_no'] ?>"/>
                                                                </span></td>
                                                        </td>
                                                </tr>
                                                <tr>
                                                        <td width="5%" align="right"><input type="checkbox" id="chkdate" name="chkdate" <?= ($_REQUEST['chkdate'] ? 'checked' : '') ?> onclick="seldateOnChange();keepFilters(2);"/></td>
                                                        <td width="15%" nowrap="nowrap" align="left">Date Changed</td>
                                                        <td width="20%" align="left">
<script language="javascript" type="text/javascript">
<!--
        function seldateOnChange() {
                var filter = '';

                var optSelected = $('seldate').options[$('seldate').selectedIndex]
                var spans = document.getElementsByName('seldateoptions')
                //$('btnPrint').style.display = "";
                for (var i=0; i<spans.length; i++) {
                        if (optSelected) {
                                if (spans[i].getAttribute("segOption") == optSelected.value) {
                                        spans[i].style.display = $('chkdate').checked ? "" : "none";

                                        if (optSelected.value == "specificdate")
                                                filter = $(optSelected.value).value
                                        else {
                                                filter = new Array($('between1').value, $('between2').value);
                                                //$('btnPrint').style.display = "";
                                        }
                                }
                                else
                                        spans[i].style.display = "none"
                        }
                }

                disableNav()
        }

        function isValidSearch(key) {
        if (typeof(key)=='undefined') return false;
        var s=key.toUpperCase();
        var skey =$('name').value;
        var skey1 = $('case_no').value;
        if (skey=='')
        {return (
        /^\d{1,2}\/\d{1,2}\/\d{4}$/.test(s) ||
        /^\d{1,2}\-\d{1,2}\-\d{4}$/.test(s) ||
        /^\d{10,}$/.test(s)
        );}
        else if (skey1=='')
        { return (
        /^\d{1,2}\/\d{1,2}\/\d{4}$/.test(s) ||
        /^\d{1,2}\-\d{1,2}\-\d{4}$/.test(s) ||
        /^\d{5,}$/.test(s)
        );}
        return (
        /^[A-Z0-9?\-\.]{3,}$/.test(s)
        );
        }

        function disableSearch(){
            if( $('chkspecific').checked ){
                if ($('name').value != '' || $('case_no').value != '')
                     {
                    if ($('name').value != '')
                        b=isValidSearch(document.getElementById('name').value);
                    else ($('case_no').value != '')
                        b=isValidSearch(document.getElementById('case_no').value);
                        document.getElementById("search-btn").disabled = b?false:true;
                    }
               else
                       document.getElementById("search-btn").disabled = true;
            }
            else if($('chkdate').checked && !$('chkspecific').checked)
                    document.getElementById("search-btn").disabled = false;
            else
                 document.getElementById("search-btn").disabled = true;
        }

        function emptier(){
            document.getElementById('name').value='';
            document.getElementById('case_no').value='';
            document.getElementById('hrn').value='';
        }
-->
</script>
                                                                <select class="jedInput" id="seldate" name="seldate" onchange="seldateOnChange(); keepFilters(2);">
                                                                        <option value="today" <?= $_REQUEST["seldate"]=="today" ? 'selected="selected"' : '' ?>>Today</option>
                                                                        <option value="thisweek" <?= $_REQUEST["seldate"]=="thisweek" ? 'selected="selected"' : '' ?>>This week</option>
                                                                        <option value="thismonth" <?= $_REQUEST["seldate"]=="thismonth" ? 'selected="selected"' : '' ?>>This month</option>
                                                                        <option value="specificdate" <?= $_REQUEST["seldate"]=="specificdate" ? 'selected="selected"' : '' ?>>Specific date</option>
                                                                        <option value="between" <?= $_REQUEST["seldate"]=="between" ? 'selected="selected"' : '' ?>>Between</option>
                                                                </select>
                                                                </td>
                                                                <td>
                                                                <span name="seldateoptions" segOption="specificdate" <?= ($_REQUEST["seldate"]=="specificdate") && $_REQUEST['chkdate'] ? '' : 'style="display:none"' ?>>
                                                                        <input onchange="keepFilters(2);" class="jedInput" name="specificdate" id="specificdate" type="text" size="8" value="<?= $_REQUEST['specificdate'] ?>"/>
                                                                        <img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_specificdate" align="absmiddle" style="cursor:pointer"  />
                                                                        <script type="text/javascript">
                                                                                Calendar.setup ({
                                                                                        inputField : "specificdate", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_specificdate", singleClick : true, step : 1
                                                                                });
                                                                        </script>
                                                                </span>
                                                                <span name="seldateoptions" segOption="between" <?= ($_REQUEST["seldate"]=="between") && $_REQUEST['chkdate'] ? '' : 'style="display:none"' ?>>
                                                                        <input onchange="keepFilters(2);" class="jedInput" name="between1" id="between1" type="text" size="8" value="<?= $_REQUEST['between1'] ?>"/>
                                                                        <img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_between1" align="absmiddle" style="cursor:pointer;"  />
                                                                        <script type="text/javascript">
                                                                                Calendar.setup ({
                                                                                        inputField : "between1", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_between1", singleClick : true, step : 1
                                                                                });
                                                                        </script>
                                                                        to
                                                                        <input onchange="keepFilters(2);" class="jedInput" name="between2" id="between2" type="text" size="8" value="<?= $_REQUEST['between2'] ?>"/>
                                                                        <img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_between2" align="absmiddle" style="cursor:pointer"  />
                                                                        <script type="text/javascript">
                                                                                Calendar.setup ({
                                                                                        inputField : "between2", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_between2", singleClick : true, step : 1
                                                                                });
                                                                        </script>
                                                                </span>
                                                        </td>
                                                </tr>
                                                <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
                                                <tr>
                                                        <td></td>
                                                        <td colspan="3">
                                                                <input type="submit" style="cursor:pointer" value="Search"  class="jedButton"/>&nbsp;
                                                                <input type="button" style="cursor:pointer;" value="Print Summary Report of Audit Trail"  class="jedButton" onclick="printTransmittalRep();"/>
                                                        </td>
                                                </tr>
                                        </table>
                                </td>
                        </tr>
                </tbody>
        </table>
</div>

<div style="width:100%">
        <table width="100%" class="segContentPaneHeader" style="margin-top:10px">
        <tr><td>
                <h1>
                        Search result:


<?php
//contcat............
        if($show) echo $search_title;  ?></h1></td>
        </tr>
        </table>
        <div class="segContentPane">
                <table id="" class="jedList" width="100%" border="0" cellpadding="0" cellspacing="0">
                        <thead>
                                <tr class="nav">
                                        <th colspan="9">
                                                <div id="pageFirst" class="<?= ($current_page > 0) ? 'segSimulatedLink' : 'segDisabledLink' ?>" style="float:left" onclick="jumpToPage(FIRST_PAGE)">
                                                        <img title="First" src="<?= $root_path ?>images/start.gif" border="0" align="absmiddle"/>
                                                        <span title="First">First</span>
                                                </div>
                                                <div id="pagePrev" class="<?= ($current_page > 0) ? 'segSimulatedLink' : 'segDisabledLink' ?>" style="float:left" onclick="jumpToPage(PREV_PAGE)">
                                                        <img title="Previous" src="<?= $root_path ?>images/previous.gif" border="0" align="absmiddle"/>
                                                        <span title="Previous">Previous</span>
                                                </div>
                                                <div id="pageShow" style="float:left; margin-left:10px">
                                                        <span><?= $nav_caption ?></span>
                                                </div>
                                                <div id="pageLast" class="<?= ($current_page < $last_page) ? 'segSimulatedLink' : 'segDisabledLink' ?>" style="float:right" onclick="jumpToPage(LAST_PAGE)">
                                                        <span title="Last">Last</span>
                                                        <img title="Last" src="<?= $root_path ?>images/end.gif" border="0" align="absmiddle"/>
                                                </div>
                                                <div id="pageNext" class="<?= ($current_page < $last_page) ? 'segSimulatedLink' : 'segDisabledLink' ?>" style="float:right" onclick="jumpToPage(NEXT_PAGE)">
                                                        <span title="Next">Next</span>
                                                        <img title="Next" src="<?= $root_path ?>images/next.gif" border="0" align="absmiddle"/>
                                                </div>
                                        </th>
                                </tr>
                                <tr>
                                        <th width="10%" align="center">User Personnel</th>
                                        <th width="10%" align="center">Patient's Name</th>
                                        <th width="10%" align="center">Field</th>
                                        <th width="10%" align="center">Old Value</th>
                                        <th width="10%" align="center">New Value</th>
                                        <th width="8%" align="center">Change Date</th>
                                        <th width="6%" align="center">HRN</th>
                                        <th width="6%" align="center">Case No</th>
                                </tr>
                        </thead>
                        <tbody>
<?= $rows ?>
                        </tbody>
                </table>
                <br />
        </div>
</div>

<?php

# Workaround to force display of results  form
$bShowThisForm = TRUE;

# If smarty object is not available create one
if(!isset($smarty)){
/**
 * LOAD Smarty
 * param 2 = FALSE = dont initialize
 * param 3 = FALSE = show no copyright
 * param 4 = FALSE = load no javascript code
 */
        include_once($root_path.'gui/smarty_template/smarty_care.class.php');
        $smarty = new smarty_care('common',FALSE,FALSE,FALSE);

        # Set a flag to display this page as standalone
        $bShowThisForm=TRUE;
}

?>

<input type="hidden" name="sid" value="<?php echo $sid ?>">
<input type="hidden" name="lang" value="<?php echo $lang ?>">
<input type="hidden" name="userck" value="<?php echo $userck ?>">
<input type="hidden" name="cat" value="<?php echo $cat?>">
<input type="hidden" name="userck" value="<?php echo $userck?>">
<input type="hidden" name="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>">
<input type="hidden" name="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>">
<input type="hidden" name="lockflag" value="<?php echo  $lockflag?>">

<input type="hidden" id="delete" name="delete" value="" />
<input type="hidden" id="page" name="page" value="<?= $current_page ?>" />
<input type="hidden" id="lastpage" name="lastpage"  value="<?= $last_page ?>" />
<input type="hidden" id="jump" name="jump">
<input type="hidden" id="applied" name="applied" value="1">
<input type="hidden" id="root_path" name="root_path" value="<?php echo $root_path ?>" />
<input type="hidden" id="seg_URL_APPEND" name="seg_URL_APPEND" value="<?=URL_APPEND?>"  />
<input type="hidden" id="fill_up" name="fill_up" value="">
</form>
<script type="text/javascript">
YAHOO.example.BasicRemote = function() {
        // Use an XHRDataSource
        var hcDS = new YAHOO.util.XHRDataSource("<?= $root_path ?>modules/billing/ajax/healthinsurance-query.php");
        // Set the responseType
        hcDS.responseType = YAHOO.util.XHRDataSource.TYPE_TEXT;
        // Define the schema of the delimited results
        hcDS.responseSchema = {
                recordDelim: "\n",
                fieldDelim: "\t"
        };
        // Enable caching
        hcDS.maxCacheEntries = 5;

        // Instantiate the AutoComplete
        var hcAC = new YAHOO.widget.AutoComplete("hcare_name", "hcContainer", hcDS);
//    hcAC.formatResult = function(oResultData, sQuery, sResultMatch) {
//        return "<span style=\"float:left;width:15%\">"+oResultData[0]+"</span><span style\"float:left;\">"+oResultData[1]+"</span>";
//    };

        // Define an event handler to populate a hidden form field
        // when an item gets selected
        var myhcareid = YAHOO.util.Dom.get("insurance");
        var myhcarehandler = function(sType, aArgs) {
                var myAC = aArgs[0]; // reference back to the AC instance
                var elLI = aArgs[1]; // reference to the selected LI element
                var oData = aArgs[2]; // object literal of selected item's result data

                // update text input control ...
                myhcareid.value = oData[1];
        };
        hcAC.itemSelectEvent.subscribe(myhcarehandler);

        return {
                hcDS: hcDS,
                hcAC: hcAC
        };
}();
</script>
<?php

$sTemp = ob_get_contents();
ob_end_clean();

# Assign the form template to mainframe
 $smarty->assign('class',"class=\"yui-skin-sam\"");
 $smarty->assign('sMainFrameBlockData',$sTemp);

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');
?>
