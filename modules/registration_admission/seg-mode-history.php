<?php
# Start Smarty templating here
 /**
 * LOAD Smarty
 */
    
 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme
     error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require('./roots.php');
    
    require($root_path.'include/inc_environment_global.php');
    
    require($root_path."modules/registration_admission/ajax/mode-history.common.php");
    $xajax->printJavascript($root_path.'classes/xajax');
/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org, 
*
* See the file "copy_notice.txt" for the licence notice
*/

define('LANG_FILE','lab.php');
define('NO_2LEVEL_CHK',1);

$local_user='ck_ipd_user';
require_once($root_path.'include/inc_front_chain_lang.php');
$breakfile = "aufnahme_daten_zeigen.php";

//$db->debug=1;

$thisfile=basename(__FILE__);
$encounter_nr = $_GET['encounterset'];

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"History of Requests ($encounter_nr)");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

 # href for the close button
 #$smarty->assign('breakfile',$breakfile);
 #edited by VAN 07-28-08
 $popUp = $_GET['popUp'];
 $is_doctor = $_GET['is_doctor'];
 
# CLOSE button for pop-ups
$smarty->assign('breakfile','javascript:window.parent.cClick();');
$smarty->assign('pbBack','');
 #-------------------
 
 # Window bar title
 $smarty->assign('sWindowTitle',"History of Requests ($encounter_nr)");

 # Assign Body Onload javascript code
 #$smarty->assign('sOnLoadJs','onLoad="document.suchform.keyword.select()"');
 #$smarty->assign('sOnLoadJs','');
 $smarty->assign('sOnLoadJs','onLoad="preSet(); startAJAXSearch(\'search\', 0, 1);"');

#added by VAN 07-02-08
#echo "done = ".$_GET['done'];
#$done = $_GET['done'];    

#added by VAN 07-28-08

require_once($root_path.'include/care_api_classes/class_encounter.php');
$encObj=new Encounter();
     
$personInfo = $encObj->getEncounterInfo($encounter_nr);
#echo $personInfo['name_first'];
#echo $encounter_nr;

 # Collect javascript code
 ob_start()

?>

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script language="javascript" >
<!--

function preSet(){
    
}

//--------------added by VAN 09-12-07------------------
var currentPage=0, lastPage=0;
var FIRST_PAGE=1, PREV_PAGE=2, NEXT_PAGE=3, LAST_PAGE=4, SET_PAGE=0;
var AJAXTimerID=0;
var lastSearch="";

function startAJAXSearch(searchID, page, mod) {
    var encounter_nr = $F('encounter_nr');
    if (AJAXTimerID) clearTimeout(AJAXTimerID);
    $("ajax-loading").style.display = "";
    $("RequestList-body").style.display = "none";
    AJAXTimerID = setTimeout("xajax_populateRequestsList("+encounter_nr+","+page+")",100);
}

function endAJAXSearch(searchID) {
    $("ajax-loading").style.display = "none";
    $("RequestList-body").style.display = "";
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

function formatNumber(num,dec) {
    var nf = new NumberFormat(num);
    if (isNaN(dec)) dec = nf.NO_ROUNDING;
    nf.setPlaces(dec);
    return nf.toFormatted();
}

function setPagination(pageno, lastpage, pagen, total) {
    currentPage=parseInt(pageno);
    lastPage=parseInt(lastpage);    
    firstRec = (parseInt(pageno)*pagen)+1;
    if (currentPage==lastPage)
        lastRec = total;
    else
        lastRec = (parseInt(pageno)+1)*pagen;
        
    //$("pageShow").innerHTML = '<span>Showing '+formatNumber((firstRec),0)+'-'+formatNumber((lastRec),0)+' out of '+formatNumber((parseInt(total)),0)+' record(s).</span>';
    if (parseInt(total)==0)
        $("pageShow").innerHTML = '<span>Showing '+(lastRec)+'-'+(lastRec)+' out of '+(parseInt(total))+' record(s).</span>';
    else
        $("pageShow").innerHTML = '<span>Showing '+(firstRec)+'-'+(lastRec)+' out of '+(parseInt(total))+' record(s).</span>';    
    
    $("pageFirst").className = (currentPage>0 && lastPage>0 && total>10) ? "segSimulatedLink" : "segDisabledLink";
    $("pagePrev").className = (currentPage>0 && lastPage>0 && total>10) ? "segSimulatedLink" : "segDisabledLink";
    $("pageNext").className = (currentPage<lastPage && total>10) ? "segSimulatedLink" : "segDisabledLink";
    $("pageLast").className = (currentPage<lastPage && total>10) ? "segSimulatedLink" : "segDisabledLink";
}

function jumpToPage(el, jumpType, set) {
    if (el.className=="segDisabledLink") return false;
    if (lastPage==0) return false;
    switch(jumpType) {
        case FIRST_PAGE:
            if (currentPage==0) return false;
            startAJAXSearch('search',0,0);
        break;
        case PREV_PAGE:
            if (currentPage==0) return false;
            startAJAXSearch('search',currentPage-1,0);
        break;
        case NEXT_PAGE:
            if (currentPage >= lastPage) return false;
            startAJAXSearch('search',parseInt(currentPage)+1,0);
        break;
        case LAST_PAGE:
            if (currentPage >= lastPage) return false;
            startAJAXSearch('search',lastPage,0);
        break;
    }
}

function refreshWindow(){
    //window.location.href=window.location.href;
}
                            
//function addPerson(listID, refno, name, req_date, urgency, status, service, code, repeat, pid,age,sex,location,enctype) {
function addPerson(listID, refno, request_date, req_type, requestor, details) {
    var list=$(listID), dRows, dBody, rowSrc;
    var i, mode, editlink, ornum;
    if (list) {
        dBody=list.getElementsByTagName("tbody")[0];
        dRows=dBody.getElementsByTagName("tr");
        if (refno) {
            alt = (dRows.length%2)+1;
            rowSrc = '<tr class="wardlistrow'+alt+'" id="row'+refno+'">'+
                        '<td align="center">'+refno+'</td>'+
                   '<td align="center">'+request_date+'</td>'+
                      '<td align="center">'+req_type+'</td>'+
                     '<td align="center">'+requestor+'</td>'+
                         '<td align="center">'+details+'</td>'+
                     '</tr>';                
                     }
        else {
            rowSrc = '<tr><td colspan="6">No requests made for this patient...</td></tr>';
        }
        dBody.innerHTML += rowSrc;
    }
}

function ReloadWindow(){
    window.location.href=window.location.href;
}
//------------------------------------------
// -->
</script> 

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

<script type="text/javascript">
<!--
OLpageDefaults(BGCLASS,'olbg', CGCLASS,'olcg', FGCLASS,'olfg',
 CAPTIONFONTCLASS,'olcap', CLOSEFONTCLASS,'olclo', TEXTFONTCLASS,'oltxt');
//-->
</script>

<style type="text/css">
<!--
.olbg {
    background-image:url("<?= $root_path ?>images/bar_05.gif");
    background-color:#0000ff;
    border:1px solid #4d4d4d;
}
.olcg {
    background-color:#aa00aa; 
    background-image:url("<?= $root_path ?>images/bar_05.gif");
    text-align:center;
}
.olcgif {background-color:#333399; text-align:center;}
.olfg {
    background-color:#ffffcc; 
    text-align:center;
}
.olfgif {background-color:#bbddff; text-align:center;}
.olcap {
    font-family:Arial; font-size:13px; 
    font-weight:bold; 
    color:#708088;
}
a.olclo {font-family:Verdana; font-size:11px; font-weight:bold; color:#ddddff;}
a.olclo:hover {color:#ffffff;}
.oltxt {font-family:Arial; font-size:12px; color:#000000;}
.olfgright {text-align: right;}
.olfgjustify {background-color:#cceecc; text-align: justify;}
.olfgleft {background-color:#cceecc; text-align: left;}

a {color:#338855;font-weight:bold;}
a:hover {color:#FF00FF;}
.text12 {font-family:Verdana,Arial,sans-serif; font-size:12px;}
.text14 {font-family:Verdana,Arial,sans-serif; font-size:14px;}
.text16 {font-family:Verdana,Arial,sans-serif; font-size:16px;}
.text18 {font-family:Verdana,Arial,sans-serif; font-size:18px;}

.myHeader {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:22px;}
.mySubHead {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:18px;}
.mySpacer {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:4px;}
.myText {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:13px;color:#000000;}
.snip {font-family:Verdana,Arial,Helvetica;font-size:10px;}
.purple14 {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:14px;color:purple;
 font-weight:bold;}
.purple18 {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:18px;color:purple;
 font-weight:bold;font-style:italic;}
.yellow {color:#ffff00;}
.red {color:#cc0000;}
.blue {color:#0000cc;}
-->
</style> 


<?php

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

ob_start();
?>

<a name="pagetop"></a>
<br>
<div style="padding-left:10px">
<form action="<?php echo $thisfile?>" method="post" name="suchform" onSubmit="">
    <div id="tabFpanel">
        <table border="0" cellspacing="2" cellpadding="2" width="100%" align="center">
            <tr>
                <td width="35%" class="segPanel"><strong>Patient Name</strong></td>
                <td class="segPanel"><?=mb_strtoupper($personInfo['name_last']).", ".mb_strtoupper($personInfo['name_first'])." ".mb_strtoupper($personInfo['name_middle'])?> (<?=$personInfo['pid']?>)</td>
            </tr>
        </table>
    </div>
</div>
    <input type="hidden" name="sid" value="<?php echo $sid?>">
    <input type="hidden" name="lang" value="<?php echo $lang?>">
    <input type="hidden" name="cat" value="<?php echo $cat?>">
    <input type="hidden" name="userck" value="<?php echo $userck ?>">
</form>

<div style="display:block; border:1px solid #8cadc0; overflow-y:hidden; overflow-x:hidden; width:90%; background-color:#e5e5e5">
    <table class="segList" width="100%" border="1" cellpadding="0" cellspacing="0">
        <thead>
            <tr class="nav">
            <th colspan="14">
                <div id="pageFirst" class="segDisabledLink" style="float:left" onclick="jumpToPage(this,FIRST_PAGE)">
                    <img title="First" src="<?= $root_path ?>images/start.gif" border="0" align="absmiddle"/>
                    <span title="First">First</span>
                </div>
                <div id="pagePrev" class="segDisabledLink" style="float:left" onclick="jumpToPage(this,PREV_PAGE)">
                    <img title="Previous" src="<?= $root_path ?>images/previous.gif" border="0" align="absmiddle"/>
                    <span title="Previous">Previous</span>
                </div>
                <div id="pageShow" style="float:left; margin-left:10px">
                    <span></span>
                </div>
                <div id="pageLast" class="segDisabledLink" style="float:right" onclick="jumpToPage(this,LAST_PAGE)">
                    <span title="Last">Last</span>
                    <img title="Last" src="<?= $root_path ?>images/end.gif" border="0" align="absmiddle"/>
                </div>
                <div id="pageNext" class="segDisabledLink" style="float:right" onclick="jumpToPage(this,NEXT_PAGE)">
                    <span title="Next">Next</span>
                    <img title="Next" src="<?= $root_path ?>images/next.gif" border="0" align="absmiddle"/>
                </div>
            </th>
        </tr>
        </thead>
    </table>
</div>

<div style="display:block; border:1px solid #8cadc0; overflow-y:scroll; overflow-x:hidden; height:280px; width:90%; background-color:#e5e5e5">
<table id="RequestList" class="segList" width="100%" border="0" cellpadding="0" cellspacing="0">
    
    <thead>
        <tr>
            
            <th width="12%" align="center">Reference #</th>
            <th width="20%" align="center">Date/Time Requested</th>
            <th width="13%" align="center">Request Type</th>
            <th width="15%" align="center">Requested by</th>
            <th width="*" align="center">Details</th>
        </tr>
    </thead>
    
    <tbody id="RequestList-body">
        <?= $rows ?>
    </tbody>
</table>
<img id="ajax-loading" src="<?= $root_path ?>images/loading6.gif" align="absmiddle" border="0" style="display:none"/>
</div>
<hr>

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

<form action="<?php echo $breakfile?>" method="post">
<input type="hidden" name="sid" value="<?php echo $sid ?>">
<input type="hidden" name="lang" value="<?php echo $lang ?>">
<input type="hidden" name="userck" value="<?php echo $userck ?>">
<input type="hidden" name="done" id="done" value="<?=$_GET['done']?>" />

<!-- added by VAN 07-28-08 -->
<input type="hidden" name="is_doctor" id="is_doctor" value="<?=($is_doctor)?1:0?>">
<input type="hidden" name="encounter_nr" id="encounter_nr" value="<?=$encounter_nr?>">

</form>



<?php

$sTemp = ob_get_contents();
ob_end_clean();

# Assign the form template to mainframe

 $smarty->assign('sMainFrameBlockData',$sTemp);

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');
?>
