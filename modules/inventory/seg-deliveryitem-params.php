<?php 
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
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

//$db->debug=1;

$thisfile=basename(__FILE__);

require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

$smarty->assign('bHideTitleBar',TRUE);
$smarty->assign('bHideCopyright',TRUE);

# Assign Body Onload javascript code
$smarty->assign('sOnLoadJs','onLoad="document.getElementById(\'item_qty\').focus();"');
 
ob_start();                                                                     
?>
<!-- Calendar -->
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/gen_routines.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="js/seg-deliveryitem-params.js"></script>
<script type="text/javascript">

// added by ken: to initialize the fields in pop up tray.,
function initialize() {
    id = <?= $_GET['id']?>;
    srow = <?= $_GET['srow'] ? $_GET['srow'] : 'null' ?>;
    if(<?=$_GET['update'] ? $_GET['update'] : '0'?>){
        $('item_qty').value = window.parent.document.getElementById("qty_"+id+srow).value;
        $('price_add').value = window.parent.document.getElementById("uprice_"+id+srow).value;
        if(window.parent.document.getElementById("expiry_"+id+srow).value){
            // $('chk_expiry').checked = 1;
            $('expiry_date').value = window.parent.document.getElementById("expiry_"+id+srow).value;
            $('show_expirydate').innerHTML = window.parent.document.getElementById("expiry_"+id+srow).value;
        }
        if(window.parent.document.getElementById("serial_"+id+srow).value){
            $('chk_serial').checked = 1;
            $('serial_no').value = window.parent.document.getElementById("serial_"+id+srow).value;
        }
        if(window.parent.document.getElementById("lot_"+id+srow).value){
            // $('chk_lot').checked = 1;
            $('lot_no').value = window.parent.document.getElementById("lot_"+id+srow).value;
        }
        if(window.parent.document.getElementById("manufacturer_"+id+srow).value){
            // $('chk_manufacturer').checked = 1;
            $('manufacturer').value = window.parent.document.getElementById("manufacturer_"+id+srow).value;
        }
    }
}

//ended by ken

document.observe('dom:loaded', initialize);
</script>
<?php
$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

$curTme  = strftime("%Y-%m-%d");
$curDate = strftime("%b %d, %Y", strtotime($curTme));

ob_start();

$price = $_GET["price"] ? $_GET["price"] : $_GET["supplier"];

?>
<div>
    <form id="fprof" method="post" action="document.location.href">
        <table width="100%" class="segPanel">
            <tbody>
            <tr>
                <td width="15%" align="right"><b>Unit Name:</b></td>
                <td align="left" colspan="2"><?php echo stripslashes($_GET["name"]) . " of " . stripslashes($_GET["itemname"]); ?></td>
            </tr>
            <tr>
                <td width="15%" align="right"><b>Qty:</b></td>
                    <td align="left"><input style="text-align:right" onblur="trimString(this); checkQty(<?=$_GET["order"]?>,<?=$_GET["qty"]?>); genChkDecimaln(this,0); " onFocus="this.select();" id="item_qty" name="item_qty" value="<?=$_GET["qty"]?>" size="10"/></td>
            </tr>
            <!-- added by bryan 102709 -->
            <tr>
                <td width="15%" align="right"><b>Unit Price:</b></td>
                    <td align="left"><input style="text-align:right" onblur="trimString(this); genChkDecimal(this,2);" onFocus="this.select();" id="price_add" name="price_add" value="<?=$price?>" size="10"/></td>
                <td width="45%" align="center"><b>Click boxes below if related info is included.</b></td>
            </tr>
            <!-- -->
            <tr>
                <td width="15%" align="right"><b>Expiry:</b></td>
                <td align="left" valign="middle">
                    <span id="show_expirydate" class="segInput"
                          style="font-weight:bold; color:#0000c0; padding:0px 2px;width:200px; height:24px; vertical-align:middle"><?= $curDate ?></span>
                    <input class="segInput" name="expiry_date" id="expiry_date" type="hidden" value="<?= $curTme ?>"
                           style="font:bold 12px Arial">
                    <img <?= createComIcon($root_path, 'show-calendar.gif', '0') ?> id="deliverydate_trigger"
                                                                                    class="segSimulatedLink"
                                                                                    align="absmiddle"
                                                                                    style="margin-left:2px;cursor:pointer">
                </td>
                <!-- <td width="45%" align="center"><input type="checkbox" id="chk_expiry" name="chk_expiry" value=""></td> -->
            </tr>
            <tr>
                <td align="right"><b>Lot No:</b></td>
                <td align="left">
                    <input size="10" onblur="trimString(this);" onFocus="this.select();" id="lot_no" name="lot_no" value=""/>
                </td>
                <!-- <td width="45%" align="center">
                    <input type="checkbox" id="chk_lot" name="chk_lot" value="">
                </td> -->
            </tr>
            <tr>
                <td width="20%" align="right"><b>Serial No:</b></td>
                <td align="left">
                    <input size="10" onblur="trimString(this);" onFocus="this.select();" id="serial_no" name="serial_no" value=""/>
                </td>
                <td width="45%" align="center">
                    <input type="checkbox" id="chk_serial" name="chk_serial" value="">
                </td>
            </tr>
            <tr>
                <td width="20%" align="right"><b>Supplier:</b></td>
                <td align="left">
                    <input size="10" onblur="trimString(this);" onFocus="this.select();" id="manufacturer" name="manufacturer" value=""/>
                </td>
                <!-- <td width="45%" align="center">
                    <input type="checkbox" id="chk_manufacturer" name="chk_manufacturer" value="">
                </td> -->
            </tr>

            <input type="hidden" id="item_code" name="item_code" value="<?= $_GET["id"] ?>"/>
            <input type="hidden" id="unit" name="unit" value="<?= $_GET["unit"] ?>"/>
            <input type="hidden" id="unit_name" name="unit_name" value="<?= $_GET["name"] ?>"/>
            <input type="hidden" id="isperpc" name="isperpc" value="<?= $_GET["perpc"] ?>"/>
            <input type="hidden" id="perpack" name="perpack" value="<?= $_GET["perpack"] ?>"/>
                <input type="hidden" id="srow" name="srow" value="<?=$_GET["srow"]?>"/>                             
            </tbody>
        </table>
        <br>
        <table width="100%">
            <tr>
                <!-- added by ken: to have an update button when editing an item -->
                <td width="80%" colspan="2" align="right">
                    <?php if($_GET['update']){?>
                        <img id="update_item" onclick="updateSelectedItem();" class="segSimulatedLink" src="<?=$root_path?>images/btn_update_item.gif" border="0" align="absmiddle" >
                    <?php }else{?>
                    <img id="submit_item" onclick="addSelectedItem();" class="segSimulatedLink" src="<?=$root_path?>images/btn_add_item.gif" border="0" align="absmiddle" >
                    <?php }?>
                </td>
                <!-- ended by ken -->
                <td>
                    <img id="cancel_submit" onclick="closeRelInfoPrompt();" class="segSimulatedLink" src="<?=$root_path?>images/his_cancel_button.gif" border="0" align="absmiddle" >
                </td>                
            </tr>        
        </table>
    </form>
</div>
<script type="text/javascript">
    Calendar.setup ({
        displayArea : "show_expirydate",
        inputField : "expiry_date",
        ifFormat : "%Y-%m-%d", 
        daFormat : "%b %d, %Y", 
        showsTime : false, 
        align: "bl",
        button : "deliverydate_trigger", 
        singleClick : true,
        step : 1
    });
</script>
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
