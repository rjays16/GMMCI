<?php
/**
* SegHIS Delivery History ....
*/
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

define('LANG_FILE','order.php');
define('NO_2LEVEL_CHK',1);
$local_user='ck_prod_db_user';

require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'modules/cashier/ajax/or-assignment.common.php');

$GLOBAL_CONFIG=array();
# Create global config object
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/inc_date_format_functions.php');

require_once($root_path.'include/care_api_classes/class_cashier.php');

$cash_obj = new SegCashier();


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

$title="";
$breakfile=$root_path."modules/cashier/seg-cashier-functions.php".URL_APPEND;
$imgpath=$root_path."pharma/img/";  

//$db->debug=1;

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');
 

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"Cashier::Setup Printer");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
# $smarty->assign('pbHelp',"javascript:gethelp('products_db.php','search','$from','$cat')");
 #$smarty->assign('pbHelp',"javascript:gethelp('billing_main.php')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"Cashier::Setup Printer");

 # Assign Body Onload javascript code
 $smarty->assign('sOnLoadJs','onLoad=""'); 
     
 # Collect javascript code
 ob_start();

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
    background-color:#ffffff;
    border:1px outset #3d3d3d;
}
.olcg {
    background-color:#ffffff; 
    background-image:url("<?= $root_path ?>images/bar_05.gif");
    text-align:center;
}
.olcgif {background-color:#333399; text-align:center;}
.olfg {
    background-color:#ffffff; 
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

.tabFrame {
    margin:5px;
}
-->
</style> 

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script language="javascript" type="text/javascript">
<!--
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

    function validate() {
        return true;
    }
    
    function keepFilters(noption) {
        var filter = '';        
        
        if (noption == 0) {
            if ($('chkspecific').checked) {
                var opt = $('selrecord').options[$('selrecord').selectedIndex];
                filter = $(opt.value).value;                
                xajax_updateFilterOption(0, true);
                xajax_updateFilterTrackers($('selrecord').value, filter);
            }
            else
                xajax_updateFilterOption(0, false);
        }
        else {                    
            if ($('chkdate').checked) {
                if ($('seldate').value == 'specificdate') {
                    filter = $('specificdate').value;
                }
                if ($('seldate').value == 'between') {
                    filter = new Array($('between1').value, $('between2').value);
                }        
                    
                xajax_updateFilterOption(1, true);
                xajax_updateFilterTrackers($('seldate').value, filter);    
            }
            else
                xajax_updateFilterOption(1, false);
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
    
    function lock_item(login_id, from_date, to_date){
        var dform = document.forms[0];
        var answer = confirm("Are you sure you want to lock user?");
        if(answer){
            xajax_lockitem(login_id, from_date, to_date);
            dform.submit();
        }
    }
    
    function unlock_item(login_id, from_date, to_date){
        var dform = document.forms[0];
        var answer = confirm("Are you sure you want to unlock user?");
        if(answer){
            xajax_unlockitem(login_id, from_date, to_date);
            dform.submit();
        }
    }
    
    function deleteRecord(ip,port) {
        //var dform = document.forms[0];
        xajax_deleteprintersetup(ip, port);
        //dform.submit();
        //refreshWindow();
    }

    function addPrinter(){
        var ip = $("ipadd").value;
        var printer = $("printer").value;
        if(ip&&printer){
            xajax_addPrinter(ip, printer);
        }else{
            alert("Please fill in the IP Address and Shared printer name.");
        }
        
    }

    function refreshWindow(){
    //alert('refresh = '+window.location.href);
    window.location.href=window.location.href;
}
    
    function updateRecord(ip,sName) {
        return overlib(
        OLiframeContent('seg-cashier-setup-printer-update.php?ip='+ip+'&sName='+sName, 
                          550, 150, 'fDiagnosis', 1, 'auto'),
                          WIDTH,400, TEXTPADDING,0, BORDER,0, 
                            STICKY, SCROLL, CLOSECLICK, MODAL, 
                            CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0 onClick="window.location=window.location">',
                         CAPTIONPADDING,4, CAPTION,'Update Printer Setup',
                         MIDX,0, MIDY,0, 
                         STATUS,'Update Printer Setup');
    }
    
        
-->
</script>

<?php

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

$xajax->printJavascript($root_path.'classes/xajax_0.5');


$errorMsg = "";

if (isset($_SESSION["current_page"])) {
    $_REQUEST['page'] = $_SESSION["current_page"];
}

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

$offset = $list_rows * $current_page;
if($offset==NULL)
    $offset=0;
$row_count = $list_rows;
if($row_count==NULL)
    $row_count=10;

//$sql = "";

$sql_all = "SELECT * FROM seg_print_default";

$sql = "SELECT * FROM seg_print_default";
$sql.=" LIMIT $offset, $row_count";

$result_all = $db->Execute($sql_all);
$result = $db->Execute($sql);

$rows = "";
$last_page = 0;
$count=0;    
if ($result) {

    if($result_all){
        $rows_found = $result_all->RecordCount();
    }

    if ($rows_found) {
        $last_page = floor($rows_found / $list_rows);
        $first_item = $current_page * $list_rows + 1;
        $last_item = ($current_page+1) * $list_rows;
        if ($last_item > $rows_found) $last_item = $rows_found;
        $nav_caption = "Showing ".number_format($first_item)."-".number_format($last_item)." out of ".number_format($rows_found)." record(s)";
    }
    
    while ($row = $result->FetchRow()) {       
        $records_found = TRUE;        
        preg_match("/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/", $row["printer_port"], $ip);
        $ipAd = '\\\\'.$ip[0].'\\';
        $sharedname = str_replace($ipAd, "", $row["printer_port"]);

        $btns="<img class=\"segSimulatedLink\" src=\"".$root_path."images/cashier_edit.gif\" border=\"0\" align=\"absmiddle\" onclick=\"updateRecord('".$row["ip_address"]."','".$sharedname."')\"/>
                <img class=\"segSimulatedLink\" src=\"".$root_path."images/cashier_delete.gif\" border=\"0\" align=\"absmiddle\" onclick=\"if (confirm('Delete this printer setup?')) deleteRecord('".$row["ip_address"]."','".$row["printer_port"]."')\"/>";

        $rows .= "<tr class=\"$class\">
                      <td width=\"35%\" align=\"left\">&nbsp;&nbsp;".$row["ip_address"]."</td>
                      <td width=\"40%\" align=\"left\">&nbsp;&nbsp;".$sharedname."</td>
                      <td width=\"25%\" align=\"center\">&nbsp;&nbsp;".$btns."</td></tr>\n";                      
        $count++;                                                                            
    }    
}
else {
    //print_r($result);
    $rows .= '        <tr><td colspan="7">'.$objdelivery->sql.'</td></tr>';
}

if (!$rows) {
    $records_found = FALSE;
    $rows .= '        <tr><td colspan="6">No printers registered.</td></tr>';
}

ob_start();
?>
<form action="<?= $thisfile.URL_APPEND."&target=list&clear_ck_sid=".$clear_ck_sid.$src_link ?>" method="post" name="suchform" onSubmit="return validate()">
<div style="margin:5px;font-weight:bold;color:#660000"><?= $sWarning ?></div>
<div style="width:70%">
    <table width="100%" border="0" style="font-size: 12px; margin-top:5px" cellspacing="2" cellpadding="2">    
        <tbody>

            <tr>
                <td align="left" class="jedPanelHeader" ><strong>Add printer</strong></td>
            </tr>
            <tr>
                <td nowrap="nowrap" align="left" class="jedPanel">

                    <table width="100%" border="0" cellpadding="2" cellspacing="0">
                        <tr><td></td><td style="color:red"><?php echo $errorMsg;?></td></tr>
                        <tr>
                            <td width="15%" nowrap="nowrap" align="left">IP address :</td>
                                <td>
                                    <input class="jedInput" name="ipadd" id="ipadd" type="text" size="40"/>
                            </td>
                        </tr>
                        <tr>
                            <td width="15%" nowrap="nowrap" align="left">Shared printer name :</td>
                                <td>
                                    <input class="jedInput" name="printer" id="printer" type="text" size="40"/>
                                    max character : 10
                            </td>
                        </tr>                     
                        
                        <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
                        <tr>
                            <td></td>
                            <td colspan="2">
                                <input type="button" style="cursor:pointer" value="Add Printer"  class="jedButton" onclick="addPrinter()"/>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<div style="width:95%">
    <div class="segContentPane">
    <table><tr><td></td></tr></table>
    </div>
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
                    <th width="35%" align="center">IP Address</th>
                    <th width="40%" align="center">Shared Printer Name</th>
                    <th width="25%" align="center">Options</th>
                    
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
<input type="hidden" id="list" name="list" value="<?= $_GET["list"] ?>">
<!--<input type="hidden" id="fill_up" name="fill_up" value="">-->
<!--<div style="display:none" id="cases_selected">
    <table id="cases">
        <tbody>
        </tbody>
    </table>
</div>
<div style="display:none" id="cases_list"></div>-->
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