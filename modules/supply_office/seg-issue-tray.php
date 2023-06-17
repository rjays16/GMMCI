<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path."modules/supply_office/ajax/issue-tray-common.php");
require($root_path.'include/inc_environment_global.php');
/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org, 
*
* See the file "copy_notice.txt" for the licence notice
*/
define('LANG_FILE','products.php');
define('NO_2LEVEL_CHK',1);
define('LANG_FILE','products.php');
$local_user='ck_prod_db_user';
require_once($root_path.'include/inc_front_chain_lang.php');

global $db;

//$db->debug=1;

$thisfile=basename(__FILE__);

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

include_once($root_path."include/care_api_classes/class_pharma_product.php");
$prod_obj = new SegPharmaProduct();

$optionstypes = $prod_obj->getProdClassOption();
 
 $smarty->assign('bHideTitleBar',TRUE);
 $smarty->assign('bHideCopyright',TRUE);

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"$title $LDPharmaDb $LDSearch");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('products_db.php','search','$from','$cat')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$title $LDPharmaDb $LDSearch");

 # Assign Body Onload javascript code
    $onLoadJS="onload=\"init();\"";
    $smarty->assign('sOnLoadJs',$onLoadJS);

 $varphp="";
 # Collect javascript code
 ob_start()
 
?>
<script language="javascript" >
<!--
var AJAXTimerID=0;
var lastSearch="", lastSearchPage=-1;
var disableInput = <?= $_GET['noinput'] ? "1" : "0" ?>

/*
function init() {
    alert('dito');
     shortcut.add('ESC', closeMe,
        {
            'type':'keydown',
            'propagate':false,
        }
    );

    setTimeout("$('search').focus()",100);
    
    xajax_populateTypesComboIss(); 
}
*/
function closeMe() {
    window.parent.cClick();
}

function prepareAddEx() {
    var prod = document.getElementsByName('prod[]');
    var qty = document.getElementsByName('qty[]');
    var nm = document.getElementsByName('pname[]');
    
    var details = new Object();
    var list = window.opener.document.getElementById('order-list');
    var result=false;
    var msg = "";
    for (var i=0;i<prod.length;i++) {
        result = false;
        if (prod[i].checked) {
            details.id = prod[i].value;
            details.name = nm[i].value;
            details.pending = pending[i].value;
            details.qty = qty[i].value;
            result = window.opener.appendOrder(list,details);
            msg += "     x" + qty[i].value + " " + nm[i].value + "\n";
            qty[i].value = 0;
            prod[i].checked = false;
        }
    }
    window.opener.refreshTotal();
    if (msg)
        msg = "The following items were added to the order tray:\n" + msg;
    else
        msg = "An error has occurred! The selected items were not added...";    
    alert(msg);
}

function startAJAXSearch(searchID, page) {
    var searchEL = $(searchID);
    if (!page) page = 0;

    var last_page;
    var val;
    var i;

    //val = $('item_type_list').value;
    val = $('type_nr').value;
    
    var filter = val;

    var areaSelected = "<?= $_REQUEST['arealimit'] ?>";
    var areaSelectedDest = "<?= $_REQUEST['arealimitdest'] ?>";
    var discountID = <?= $_GET['d'] ? ("'".$_GET['d']."'") : "null" ?>;     

    if (true) {
        searchEL.style.color = "#0000ff";
        if (AJAXTimerID) clearTimeout(AJAXTimerID);
        $("ajax-loading").style.display = "";
        var script = "xajax_populateIssueProductList('"+searchID+"',"+page+",'"+searchEL.value+"'" +
            ",'"+discountID+"'" +
            ",'"+areaSelected+"'"+
            ", "+disableInput+
            ",'"+filter+"'"+
            ",'"+areaSelectedDest+"')";
        AJAXTimerID = setTimeout(script,200);
        lastSearch = searchEL.value;
        lastSearchPage = page;
    }
}

function endAJAXSearch(searchID) {
    var searchEL = $(searchID);
    if (searchEL) {
        $("ajax-loading").style.display = "none";
        searchEL.style.color = "";
    }
}

function hidesearch(value) {
    if(value=="") $('searchbtntray').disabled="disabled";
    else $('searchbtntray').disabled="";
}

// -->
</script> 
<script type="text/javascript" src="<?=$root_path?>js/gen_routines.js"></script> 
<script type="text/javascript" src="<?=$root_path?>modules/supply_office/js/issue-tray-gui.js?t=<?=time()?>"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/shortcut.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>

<!-- YU Library -->
<script type="text/javascript" src="<?=$root_path?>js/yui/yahoo/yahoo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/event/event.js" ></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/dom/dom.js" ></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/connection/connection.js" ></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/dragdrop/dragdrop.js" ></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/container/container_core.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/container/container.js"></script>
<link type="text/css" rel="stylesheet" href="<?=$root_path?>js/yui/container/assets/container.css">

<script>
//equip = pbill

    YAHOO.namespace("equipprompt.container");
    //YAHOO.namespace("medprompt.container");
    YAHOO.util.Event.onDOMReady(initEquipmentPrompt);
    YAHOO.util.Event.onDOMReady(initEquipmentPcPrompt);
    YAHOO.util.Event.onDOMReady(initMedicinePrompt);
    YAHOO.util.Event.onDOMReady(initMedicinePcPrompt);
    YAHOO.util.Event.onDOMReady(initOtherPrompt);
    YAHOO.util.Event.onDOMReady(initOtherPcPrompt);
    //YAHOO.util.Event.onDOMReady(initMedicinePrompt);    
    YAHOO.util.Event.addListener(window, "load", init);
       
    function myClick() {
        js_Recalculate();
        cClick();
    }    
</script>

<script type="text/javascript" src="js/issue-gui.js?t=<?=time()?>"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script type="text/javascript" src="<?=$root_path?>js/shortcut.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js?t=<?= time() ?>"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>

<?php
$xajax->printJavascript($root_path.'classes/xajax');
$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

# Buffer page output

ob_start();

?>
  
  <span id="ajax_display"></span>
    <table width="98%" cellspacing="1" cellpadding="1" style="margin:0.7%">
        <tbody>
            <!-- <tr style="background-color:#e5e5e5; color: #2d2d2d" >
                
                <td align="right" width="120px">
                    <div style="padding:0px 0px; padding-left:10px; ">
                        <p><strong>Item Type:</strong></p>
                    </div>
                </td>
                <td valign="middle">
                    <div style="padding:0px 0px; padding-left:2px; ">
                        <select style="width:350px" id="iss_item_type" name="iss_item_type" onchange="$('type_nr').value=this.options[this.selectedIndex].value;startAJAXSearch('search',0);return false;" onblur="hidesearch(this.options[this.selectedIndex].value);">
                            
                            <?php
                                //echo $optionstypes;
                            ?>
                        </select>                                       
                    </div>
                </td>
            </tr> -->
            <tr style="background-color:#e5e5e5; color: #2d2d2d" >
              <!--  <td style="background-color:#e5e5e5; color: #2d2d2d" > -->
                <!-- <td align="right">
                    <div style="padding:0px 0px; padding-left:10px; ">
                        <p><strong>Search Product:</strong></p>
                    </div>  
                </td> -->
                <td valign="middle">
                    <div style="padding:0px 0px; padding-left:2px; "> 
                        <input id="search" class="segInput" type="hidden" style="width:60%; margin-left:1px; font: bold 12px Verdana" align="absmiddle" onkeyup="if (event.keyCode==13) startAJAXSearch(this.id)" />
                        <input id="searchbtntray" type="hidden" src="<?= $root_path ?>images/his_searchbtn.gif" onclick="startAJAXSearch('search');return false;" align="absmiddle" disabled=""/>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div style="display:block; border:1px solid #8cadc0; overflow-y:scroll; height:285px; width:100%; background-color:#e5e5e5">
                        <table id="item-list" class="jedList" cellpadding="1" cellspacing="1" width="100%">
                            <thead>
                                <tr class="nav">
                                    <th colspan="9">
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
                                <tr>
                                    <!-- <th width="*">Name/Description</th>
                                    <th width="10%" align="center">Code</th>
                                    <th width="10%" align="center">Expiry</th>  
                                    <th width="10%" align="center">Qty Per Pack</th>
                                    <th width="15%" style="" colspan="2" nowrap="nowrap">Available Qty</th> 
                                    <th width="15%" style="" colspan="2" nowrap="nowrap">Requested Qty</th>
                                    <th width="5%">Big Unit</th>
                                    <th width="5%">Small Unit</th> -->

                                    <th width="10%">Ref No.</th>
                                    <th width="15%">Date</th>
                                    <th width="15%">Requestor</th>
                                    <th width="*">Particulars</th>
                                    <th width="10%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="9">No such product exists...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <img id="ajax-loading" src="<?= $root_path ?>images/loading6.gif" align="absmiddle" border="0" style="display:none"/> 
                </td>
            </tr>
        </tbody>
    </table>
    
<div id="equipmentBox">
<div class="hd" align="left">Equipment Issuance Details (per unit)</div>
<div class="bd">
    <form id="eperunitbox" method="post" action="document.location.href">
        <table width="100%" class="segPanel">
            <tr><td>
                <table width="100%" border="0">
                    <tbody>                        
                        <tr align="center">
                            <td width="50%" align="right"><b>Quantity :</b></td>
                            <td width="*" align="left">
                                <input style="text-align:right" onFocus="this.select();" id="eunit_qty" name="eunit_qty" size="15" value="1" disabled="disabled"/>
                            </td>
                            <!--
                            <td width="25%" ><b>Serial number :</b></td>
                            <td width="*">
                                <input style="text-align:right" onFocus="this.select();" id="eunit_serial" name="eunit_serial" size="15" value="" />
                            </td>
                            -->
                        </tr>
                    </tbody>
                </table>
            </td></tr>
            <tr><td>
                <table width="100%" border="0">
                    <tbody>                        
                        <tr align="center">
                            <td width="25%" ><b>Property No :</b></td>
                            <td width="*">
                                <input style="text-align:right" onFocus="this.select();" id="eunit_propno" name="eunit_propno" size="15" value="" />
                            </td>
                            
                            <td width="35%" ><b>Estimated Life :</b></td>
                            <td width="*">
                                <input style="text-align:right" onFocus="this.select();" id="eunit_estlife" name="eunit_estlife" size="15" value="" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td></tr>
        </table>
    </form>
</div>
</div>

<div id="equipmentBoxPc">
<div class="hd" align="left">Equipment Issuance Details (per piece)</div>
<div class="bd">
    <form id="eperpcbox" method="post" action="document.location.href">
        <table width="100%" class="segPanel">
            <tr><td>
                <table width="100%" border="0">
                    <tbody>                        
                        <tr align="center">
                            <td width="50%" align="right"><b>Quantity :</b></td>
                            <td width="*" align="left">
                                <input style="text-align:right" onFocus="this.select();" id="epc_qty" name="epc_qty" size="15" value="1" disabled="disabled"/>
                            </td>
                            <!--
                            <td width="25%" ><b>Serial number :</b></td>
                            <td width="*">
                                <input style="text-align:right" onFocus="this.select();" id="epc_serial" name="epc_serial" size="15" value="" />
                            </td>
                            -->
                        </tr>
                    </tbody>
                </table>
            </td></tr>
            <tr><td>
                <table width="100%" border="0">
                    <tbody>                        
                        <tr align="center">
                            <td width="25%" ><b>Property No :</b></td>
                            <td width="*">
                                <input style="text-align:right" onFocus="this.select();" id="epc_propno" name="epc_propno" size="15" value="" />
                            </td>
                            
                            <td width="35%" ><b>Estimated Life :</b></td>
                            <td width="*">
                                <input style="text-align:right" onFocus="this.select();" id="epc_estlife" name="epc_estlife" size="15" value="" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td></tr>
        </table>
    </form>
</div>
</div>

<div id="medicineBox">
<div class="hd" align="left">Medicine Issuance Details (per pack)</div>
<div class="bd">
    <form id="mperunitbox" method="post" action="document.location.href">
        <table width="100%" class="segPanel">
            <tr><td>
                <table width="100%" border="0">
                    <tbody>                        
                        <tr align="center">
                            <td width="45%" align="right"><b>Quantity :</b></td>
                            <td width="*">
                                <input type="hidden" id="temporaryexp" name="temporaryexp" value="" disabled="disabled">
                                <input align="left" style="text-align:right" onFocus="this.select();" id="munit_qty" name="munit_qty" size="15" value="" />
                            </td>
                            <!--
                            <td width="25%" ><b>Expiration Date :</b></td>
                            <td width="*">                                
                                <?php
                                echo "<div id='med_pck'>
                                <select id='munit_expdate_hidden' name='munit_expdate_hidden'>
                                <option value=''>Select Expiry</option>";
                                
                                echo "</select>
                                </div>
                                ";
                                /*
                                $dbtime_format = "Y-m-d H:i";
                                $fulltime_format = "F j, Y g:ia";
                                if ($_REQUEST['dateset']) {
                                    $curDate = date($dbtime_format,$_REQUEST['dateset']);
                                    $curDate_show = date($fulltime_format, $_REQUEST['dateset']);
                                }
                                else {
                                    $curDate = date($dbtime_format);
                                    $curDate_show = date($fulltime_format);
                                }
                                
                                echo '<span id="munit_expdate" class="jedInput" style="font-weight:bold; color:#0000c0; padding:0px 2px;width:200px; height:24px">'.($submitted ? date($fulltime_format,strtotime(NOW())) : $curDate_show).'</span><input class="jedInput" name="munit_expdate_hidden" id="munit_expdate_hidden" type="hidden" value="'.($submitted ? date($dbtime_format,strtotime($_POST['orderdate'])) : $curDate).'" style="font:bold 12px Arial">';
                                echo '<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="munit_exp_trigger" class="segSimulatedLink" align="absmiddle" style="margin-left:2px;cursor:pointer">';
                                echo "<script type=\"text/javascript\">
            Calendar.setup ({
                displayArea : \"munit_expdate\",
                inputField : \"munit_expdate_hidden\",
                ifFormat : \"%Y-%m-%d %H:%M\", 
                daFormat : \"    %B %e, %Y %I:%M%P\", 
                showsTime : true, 
                button : \"munit_exp_trigger\", 
                singleClick : true,
                step : 1
            });
        </script>";              
                                */
                                ?>
                            </td>
                            -->
                        </tr>
                    </tbody>
                </table>
            </td></tr>
        </table>
    </form>
</div>
</div>

<div id="medicineBoxPc">
<div class="hd" align="left">Medicine Issuance Details (per piece)</div>
<div class="bd">
    <form id="mperpcbox" method="post" action="document.location.href">
        <table width="100%" class="segPanel">
            <tr><td>
                <table width="100%" border="0">
                    <tbody>                        
                        <tr align="center">
                            <td width="45%" align="right"><b>Quantity :</b></td>
                            <td width="*" align="left">
                                <input style="text-align:right" onFocus="this.select();" id="mpc_qty" name="mpc_qty" size="15" value="" />
                            </td>
                            <!--
                            <td width="25%" ><b>Expiration Date :</b></td>
                            <td width="*">
                            <?php
                            echo "<div id='med_pc'>
                                <select id='mpc_expdate_hidden' name='mpc_expdate_hidden' onchange='alert(this.options[this.selectedIndex].value);'>
                                <option value=''>Select Expiry</option>";
                                
                                echo "</select>
                                </div>
                                ";
                            /*    
                                $dbtime_format = "Y-m-d H:i";
                                $fulltime_format = "F j, Y g:ia";
                                if ($_REQUEST['dateset']) {
                                    $curDate = date($dbtime_format,$_REQUEST['dateset']);
                                    $curDate_show = date($fulltime_format, $_REQUEST['dateset']);
                                }
                                else {
                                    $curDate = date($dbtime_format);
                                    $curDate_show = date($fulltime_format);
                                }
                                
                                echo '<span id="mpc_expdate" class="jedInput" style="font-weight:bold; color:#0000c0; padding:0px 2px;width:200px; height:24px">'.($submitted ? date($fulltime_format,strtotime(NOW())) : $curDate_show).'</span><input class="jedInput" name="mpc_expdate_hidden" id="mpc_expdate_hidden" type="hidden" value="'.($submitted ? date($dbtime_format,strtotime($_POST['orderdate'])) : $curDate).'" style="font:bold 12px Arial">';
                                echo '<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="mpc_exp_trigger" class="segSimulatedLink" align="absmiddle" style="margin-left:2px;cursor:pointer">';
                                echo "<script type=\"text/javascript\">
            Calendar.setup ({
                displayArea : \"mpc_expdate\",
                inputField : \"mpc_expdate_hidden\",
                ifFormat : \"%Y-%m-%d %H:%M\", 
                daFormat : \"    %B %e, %Y %I:%M%P\", 
                showsTime : true, 
                button : \"mpc_exp_trigger\", 
                singleClick : true,
                step : 1
            });
        </script>";
                                */
                                ?>
                                
                            </td>
                            -->
                        </tr>
                    </tbody>
                </table>
            </td></tr>
        </table>
    </form>
</div>
</div>

<div id="otherBox">
<div class="hd" align="left">Supply Details (per unit)</div>
<div class="bd">
    <form id="eperunitbox" method="post" action="document.location.href">
        <table width="100%" class="segPanel">
            <tr><td>
                <table width="100%" border="0">
                    <tbody>                        
                        <tr align="center">
                            <td width="*">
                                <b>Quantity :</b> <input style="text-align:right" onFocus="this.select();" id="other_qty" name="other_qty" size="15" value="" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td></tr>
        </table>
    </form>
</div>
</div>

<div id="otherBoxPc">
<div class="hd" align="left">Supply Details (per piece)</div>
<div class="bd">
    <form id="eperpcbox" method="post" action="document.location.href">
        <table width="100%" class="segPanel">
            <tr><td>
                <table width="100%" border="0">
                    <tbody>                        
                        <tr align="center">
                            <td width="*">
                                <b>Quantity :</b> <input style="text-align:right" onFocus="this.select();" id="otherpc_qty" name="otherpc_qty" size="15" value="" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td></tr>
        </table>
    </form>
</div>
</div>

<div id="segCalendar"></div>
    <input type="hidden" name="temporaryid" value="">
    <input type="hidden" name="sid" value="<?php echo $sid?>">
    <input type="hidden" name="lang" value="<?php echo $lang?>">
    <input type="hidden" name="cat" value="<?php echo $cat?>">
    <input type="hidden" name="userck" value="<?php echo $userck ?>">
    <input type="hidden" name="arealimit" value="<?php echo $_REQUEST['arealimit'] ?>">
    <input type="hidden" name="arealimitdest" value="<?php echo $_REQUEST['arealimitdest'] ?>">
    <input type="hidden" name="mode" value="search">
    <input type="hidden" id="type_nr" name="type_nr" value="">


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
</form>
<?php if ($from=="multiple")
echo '
<form name=backbut onSubmit="return false">
<input type="hidden" name="sid" value="'.$sid.'">
<input type="hidden" name="lang" value="'.$lang.'">
<input type="hidden" name="userck" value="'.$userck.'">
</form>
';
?>
</div>
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
