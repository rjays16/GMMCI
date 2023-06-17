<?php

//include_once($root_path."include/care_api_classes/pharmacy/class_return.php");
//$rc = new PharmacyReturn();
require $root_path . 'include/api/bootstrap.php';
global $db;

$returnID = $_GET['nr'];
$isRefund = ($_GET['refund'] != "no");
$readOnly = false;

// Load smarty template
require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

require_once($root_path . 'include/care_api_classes/inventory/class_inventory_helper.php');
$invHelper = new InventoryHelper();

include_once($root_path."include/care_api_classes/class_order.php");
$order_obj = new SegOrder("pharma");

#added by janken 10/14/2014 for including the curl class
require_once($root_path.'include/care_api_classes/curl/class_curl.php');
$curl_obj = new Rest_Curl;

Loader::import('db.mappers.AdoMapper');
Loader::import('request.pharmacy.return.PharmacyReturn');
$mapper = new AdoMapper(Environment::getConnection('core'));
$return = new PharmacyReturn(array(
    'return_nr' => $returnID
));
$return->setMapper($mapper);

// Process data submission
if (isset($_POST["submitted"]) && !$_REQUEST['viewonly']) {
    $mapper->startTransaction();
    $return->set(array(
        'return_date' => $_POST['return_date'],
        'pid' => $_POST['pid'],
        'encounter_nr' => $_POST['encounter_nr'],
        'return_name' => $_POST['return_name'],
        'return_address' => $_POST['return_address'],
        'pharma_area' => $_POST['area'] ?
            strtoupper($_POST['area']) :
            strtoupper($_POST['area2']),
        'comments' => $_POST['comments'],
        'modify_id'=>$_SESSION['sess_temp_userid'],
        'modify_time'=>date('YmdHis')
    ));

    #added by janken 10/14/2014 for patient info in return meds
    $patient_info = array('refno' => $_POST['return_nr'],
                            'pid' => $_POST['pid'],
                            'date' => $_POST['return_date']);


    foreach ($_POST['items'] as $i=>$val)
    {
        $return->addItem(new PharmacyReturnItem(array(
            'ref_no' => $_POST["ref"][$i],
            'bestellnum' => $_POST["items"][$i],
            'quantity' => $_POST["returned"][$i]
        )));

        //added by EJ 09/24/2014
        $area = $order_obj->getTransactionArea($_POST["ref"][$i]);
        $invHelper->addStock($_POST["items"][$i], $area, $_POST["returned"][$i], '', $_POST["ref"][$i], $_POST["ref"][$i], PHRETURN);
    
        #added by janken 10/14/2014 for item info in return meds
        $item_info[] = array('ref_no' => $_POST["ref"][$i],
                            'bestellnum' => $_POST["items"][$i],
                            'quantity' => $_POST["returned"][$i]);
    }


    try {
        if ($return->save()) {
            $returnID = $return->return_nr;
            $readOnly = true;
            #added by janken 10/14/2014 for passing the data into fis
            $curl_obj->returnPharmaItem($item_info, $patient_info);
        } else {
            $mapper->failTransaction();
            $smarty->assign('sysErrorMessage',
                '<strong>Saving failed...</strong>'
            );
        }
    } catch (SqlException $e) {
        $mapper->failTransaction();
        $smarty->assign('sysErrorMessage',
            sprintf(
                '<strong>%s</strong>',
                $e->getMessage()
            )
        );
    }

    if ($mapper->hasFailedTransaction()) {
        $mapper->rollbackTransaction();
    } else {
        $mapper->completeTransaction();
        $smarty->assign('sysInfoMessage','<div style="margin:6px">Return information successfully saved!</div>');
    }
}

# href for the help button
$smarty->assign('pbHelp',"javascript:gethelp('products_db.php','input','$mode','$cat')");

# href for the close button
$smarty->assign('breakfile',$breakfile);

# Assign Body Onload javascript code
if ($readOnly) {
    //$onLoadJS='onload="'.($returnID ? 'xajax_populate_items(\''.$returnID.'\',1)' : '').'"';
    $onLoadJS='onload="init(\''.$returnID.'\',1)"';
}
else {
    //$onLoadJS='onload="'.($returnID ? 'xajax_populate_items(\''.$returnID.'\')' : '').'"';
    $onLoadJS='onload="init(\''.$returnID.'\')"';
}
$smarty->assign('sOnLoadJs',$onLoadJS);

# Collect javascript code

ob_start();
 # Load the javascript code
?>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/seg_utils.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="js/return-gui.js?t=<?=time()?>"></script>
<script type="text/javascript" language="javascript">
var trayItems = 0;
var returnNr = '<?= $returnID ?>';
var ViewMode = false;

function init(nr, disabled) {
    xajax_populate_items(nr, disabled);
}

var totalDiscount = 0;

function parseFloatEx(x) {
    var str = x.toString().replace(/\,|\s/,'')
    return parseFloat(str)
}

function warnClear() {
    var items = document.getElementsByName('items[]');
    if (items.length == 0) return true;
    else return confirm('Performing this action will clear the item list. Do you wish to continue?');
}

function formatNumber(num,dec) {
    var nf = new NumberFormat(num);
    if (isNaN(dec)) dec = nf.NO_ROUNDING;
    nf.setPlaces(dec);
    return nf.toFormatted();
}

function resetNr(newRefNo,error) {
    $("return_nr").style.color = error ? "#ff0000" : "";
    $("return_nr").value=newRefNo;
}

function pSearchClose() {
    var pid = $('pid').value;
    emptyList();
    cClick();
}

function emptyList() {
    clearList($('return-list'));
    appendItem(null);
}

function reclassRows(startIndex) {
    var list = $('return-list');
    if (list) {
        var dBody=list.select("tbody").first();
        if (dBody) {
            var dRows = dBody.select('tr');
            if (dRows) {
                for (i=startIndex;i<dRows.length;i++) {
                    if (i%2!=0) {
                        dRows[i].addClassName('alt');
                    }
                    else {
                        dRows[i].removeClassName('alt');
                    }
                }
            }
        }
    }
}

function clearList(list) {
    if (!list) list = $('return-list')
    if (list) {
        var dBody=list.select("tbody").first();
        if (dBody) {
            trayItems = 0
            dBody.update();
            return true
        }
    }
    return false
}

function prepareDelete(rowid) {
    if (confirm('Remove this item from the list?')) {
        if (removeItem(rowid)) {
        }
        else "Unable to remove the item from the list...";
    }
}

function returnItem(id, qty) {
    var pid=$('pid').value;
    var enc=$('encounter_nr').value;
    if (!id || !enc) return false;
    var existing = $$('[itemCode="'+id+'"]');
    if ( existing.length ) {
        if (confirm('This item already exists in the current return list. Replace the existing entries using the new data?')) {
            existing.each( function(obj) {
                var rowId = obj.readAttribute('itemId');
                if ( rowId ) {
                    removeItem( rowId );
                }
            })
        }
        else {
            return false;
        }
    }
    xajax.call('returnItem',{
        parameters: [enc, id, qty, '<?=$returnID?>']
    });
}

function removeItem(id) {
    var rmvRow=$("row_"+id);
    if (rmvRow) {
        var rndx = rmvRow.rowIndex-1;
        rmvRow.remove();
        if (!$$('[name="items[]"]') || $$('[name="items[]"]').length <= 0) {
            appendItem(null);
        }
        reclassRows(rndx);
        return true;
    }
    return false;
}


function appendItem(details, disabled) {
    var list = $('return-list');
    if (list) {
        var dBody=list.select("tbody").first();
        if (dBody) {
            var totalCash, totalCharge;
            var src;
            var lastRowNum = null,
                    items = $$('[name="items[]"]');
                    dRows = dBody.select('tr');
            if ( details ) {
                details = Object.extend({
                    id: '',
                    ref: '',
                    previous: 0,
                    returned: 0,
                    price: 0.0,
                    name: '',
                    generic: ''
                }, details);

                var id = details.id,
                    ref = details.ref,
                    qty = details.qty,
                    previous = details.previous,
                    returned = details.returned,
                    price = details.price,
                    name = details.name,
                    generic = details.generic,
                    rowid = ref+'_'+id;

                tot = price*qty;
                if (items) {
                    if ($('id_'+rowid)) {
                        return false
                    }
                }
                if (items.length == 0) {
                    clearList(list)
                }

                var disabledAttrib = disabled ? 'disabled="disabled"' : ""

                src =
                    '<tr class="'+((dRows.length%2!=0) ? 'alt': '')+'" id="row_'+rowid+'">';

                refund = returned * price;
                src+=
                    '<td>'+
                        '<input type="hidden" name="ref[]" id="ref_'+rowid+'" itemID="'+rowid+'" value="'+ref+'" />'+
                        '<span id="ref2_'+rowid+'" style="color:#000060">'+ref+'</span>'+
                    '</td>'+
                    '<td align="left">'+
                        '<input type="hidden" name="items[]" itemID="'+rowid+'" id="id_'+rowid+'" value="'+id+'" itemCode="'+id+'"/>'+
                        '<span id="id2_'+rowid+'" style="color:#000060">'+id+'</span>'+
                    '</td>'+
                    '<td>'+
                        '<span style="color:#660000">'+name+'</span><br/>'+
                        '<span style="font-size:11px;font-weight:normal">'+generic+'</span>'+
                    '</td>'+
                    '<td class="centerAlign">'+
                        '<input type="hidden" name="qty[]" id="qty_'+rowid+'" itemID="'+rowid+'" value="'+qty+'" />'+
                        '<span style="">'+formatNumber(qty)+'</span>'+
                    '</td>'+
                    '<td class="centerAlign">'+
                        '<input type="hidden" name="previous[]" id="previous_'+rowid+'" itemID="'+rowid+'" value="'+previous+'" />'+
                        '<span id="previous2_'+rowid+'" style="color:#008000">'+formatNumber(previous)+'</span>'+
                    '</td>'+
                    '<td class="rightAlign">'+
                        '<input type="hidden" name="price[]" id="price_'+rowid+'" itemID="'+rowid+'" value="'+price+'" />'+
                        '<span id="price2_'+rowid+'" style="">'+formatNumber(price,2)+'</span>'+
                    '</td>'+
                    '<td class="centerAlign">'+
                        '<input class="segInput" type="text" name="returned[]" id="returned_'+rowid+'" value="'+returned+'" prevValue="'+returned+'" itemID="'+rowid+'" style="width:85%;text-align:right" onchange="adjustQty(this)" onkeyup="if (event.keyCode==13) this.blur()"/>'+
                    '</td>'+
                    '<td class="rightAlign">'+
                        '<input type="hidden" name="refund[]" id="refund_'+rowid+'" itemID="'+rowid+'" value="'+refund+'" />'+
                        '<span id="refund2_'+rowid+'" style="">'+formatNumber(refund,2)+'</span>'+
                    '</td>'+
                    '<td class="centerAlign"><img class="segSimulatedLink" src="../../images/cashier_delete.gif" border="0" onclick="prepareDelete(\''+rowid+'\');"/></td>'
                '</tr>';
                dBody.insert(src);
                trayItems++;
            }
            else {
                dBody.update( "<tr><td colspan=\"15\">Item list is currently empty...</td></tr>" );
            }

            return true;
        }
    }
    return false;
}

function adjustQty(obj) {
    var id = obj.getAttribute("itemID");
    if (isNaN(obj.value) || obj.value<0) {
        alert("Invalid returned quantity entered...");
        obj.focus();
        obj.value = obj.getAttribute("prevValue");
        return false;
    }
    if ((parseFloatEx(obj.value)+parseFloatEx($('previous_'+id).value)) > parseFloatEx($('qty_'+id).value)) {
        alert("Quantity returned exceeded the maximum returnable...");
        obj.focus();
        obj.value = obj.getAttribute("prevValue");
        return false;
    }
    if (parseFloatEx(obj.value) != parseFloatEx(obj.getAttribute("prevValue"))) {
        var refund = parseFloatEx($('price_'+id).value)*parseFloatEx($('returned_'+id).value);
        $('refund_'+id).value = refund;
        $('refund2_'+id).innerHTML = formatNumber(refund,2);
    }
    refreshRefund();
    obj.setAttribute("prevValue",parseFloatEx(obj.value));
    return true;
}

function refreshRefund() {
    var items = document.getElementsByName('items[]');
    var refund = document.getElementsByName('refund[]');

    var id
    var total = 0
    for (var i=0;i<items.length;i++) {
        id = items[i].getAttribute('itemID');
        total+=parseFloatEx(refund[i].value);
    }
}

function validate() {
    return confirm('Do you wish to submit this return/refund entry?');
}

function openReturnTray(is_refund) {
    is_refund = is_refund || false;
    var pid=$('pid').value;
    var enc_nr=$('encounter_nr').value;
    if (!enc_nr) enc_nr='';
    if (pid) {
        if(is_refund)
        {
            return overlib(
            OLiframeContent('seg-return-tray.php?nr=<?= $returnID ?>&pid='+pid+'&enc='+enc_nr+'&refund='+is_refund, 600, 380, 'fReturnTray', 0, 'no'),
            WIDTH,600, TEXTPADDING,0, BORDER,0,
            STICKY, SCROLL, CLOSECLICK, MODAL,
            CLOSETEXT, '<img src=<?=$root_path?>images/close_red.gif border=0 >',
            CAPTIONPADDING,2,
            CAPTION,'Add refund item',
            MIDX,0, MIDY,0,
            STATUS,'Add refund item');
        }else
        {
            return overlib(
                OLiframeContent('seg-return-tray.php?nr=<?= $returnID ?>&pid='+pid+'&enc='+enc_nr+'&refund='+is_refund, 600, 380, 'fReturnTray', 0, 'no'),
                WIDTH,600, TEXTPADDING,0, BORDER,0,
                STICKY, SCROLL, CLOSECLICK, MODAL,
                CLOSETEXT, '<img src=<?=$root_path?>images/close_red.gif border=0 >',
                CAPTIONPADDING,2,
                CAPTION,'Add return item',
                MIDX,0, MIDY,0,
                STATUS,'Add return item');
        }
    }
    else {
        alert('Please select a patient first...');
        return false;
    }
}

function keyF9() {
<?php
$var_arr = array(
"var_pid"=>"pid",
"var_encounter_nr"=>"encounter_nr",
"var_name"=>"return_name",
"var_addr"=>"return_address",
"var_clear"=>"clear-enc",
"var_include_walkin"=>"0",
/*"var_reg_walkin"=>"0",
"var_enctype"=>"encounter_type",
"var_enctype_show"=>"encounter_type_show",
"var_include_walkin"=>"1", */
"var_reg_walkin"=>"0"
);
//$vars = array();
//foreach($var_arr as $i=>$v) {
//	$vars[] = "$i=$v";
//}
//$var_qry = implode("&",$vars);

# (!) Not compatible with PHP4
$var_qry = http_build_query( $var_arr );
?>
    if (warnClear()) {
        overlib(
            OLiframeContent('<?= $root_path ?>modules/registration_admission/seg-select-enc.php?<?=$var_qry?>&var_include_enc=<?= $isRefund ? 0:1?>',
            700, 400, 'fSelEnc', 0, 'no'),
            WIDTH,700, TEXTPADDING,0, BORDER,0,
            STICKY, SCROLL, CLOSECLICK, MODAL,
            CLOSETEXT, '<img src=<?= $root_path ?>/images/close_red.gif border=0 >',
            CAPTIONPADDING,2,
            CAPTION,'Select registered person',
            MIDX,0, MIDY,0,
            STATUS,'Select registered person');
    }
    return false;
}

function keyESC() {
    cClick();
}

document.observe("dom:loaded", function() {
    // capture keypresses
    document.observe('keypress', function(event) {
        switch(event.keyCode) {
            case Event.KEY_ESC:
                keyESC();
                break;
            case 120: // F9
                keyF9();
                break;
        }
    });
});
</script>

<?php
$xajax->printJavascript($root_path.'classes/xajax_0.5');
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

# Load form values
if ($returnID) {
    $assignArray = array(
        'return_nr' => $return->return_nr,
        'return_date' => $return->return_date,
        'area' => $return->pharma_area,

        'pid' => $return->pid,
        'encounter_nr' => $return->encounter_nr,
        'return_name' => $return->return_name,
        'return_address' => $return->return_address,

        'comments' => $return->comments

    );

    $_POST = array_merge($_POST, $assignArray);
//    $info = $rc->GetReturnInfo($returnID);
//    $_POST['area'] = $info['pharma_area'];
//    $_POST['return_nr'] = $info['return_nr'];
//    $_POST['return_date'] = $info['return_date'];
//
//    $_POST['pid'] = $info['pid'];
//    $_POST['encounter_nr'] = $info['encounter_nr'];
//
//    $_POST['return_name'] = $info['return_name'];
//    $_POST['return_address'] = $info['return_address'];
//
//    $_POST['comments'] = $info['comments'];
//
//    $isRefund = is_numeric($info['refund_amount']);
//    if ($isRefund) {
//        $_POST['refund_amount'] = number_format((float)$info['refund_amount'],2);
//        $_POST['refund_amount_fixed'] = (float) $info['refund_amount_fixed'];
//        if (!$_POST['refund_amount_fixed']) $_POST['refund_amount_fixed'] = (float)$_POST['refund_amount'];
//    }
}

# Title in the title bar
$title2 = $isRefund ? 'refund' : 'return';
$title = $returnID ? "Pharmacy::Edit $title2 entry" : "Pharmacy::New $title2 entry";

$smarty->assign('sToolbarTitle', $title);
# Window bar title
$smarty->assign('sWindowTitle', $title);

# Render form values
$smarty->assign('sReturnNr', '<input class="segInput" type="text" id="return_nr" name="return_nr" size="15" value="'.($returnID ? $returnID : $return->generateReturnId()).'" readonly="readonly"/>');
$smarty->assign('sReturnNrReset', '<button class="segButton" type="button" onclick="xajax_reset_returnNr()"'.($returnID ? ' disabled="disabled"':'').'><img src="'.$root_path.'gui/img/common/default/arrow_refresh.png"/>Reset</button>');

$smarty->assign('sReturnName','<input class="segInput" id="return_name" name="return_name" type="text" size="40" '.$readOnly.' value="'.$_POST["return_name"].'" readonly="readonly" />');
$smarty->assign('sReturnEncID','<input id="pid" name="pid" type="hidden" value="'.$_POST["pid"].'"/>');
$smarty->assign('sReturnEncNr','<input id="encounter_nr" name="encounter_nr" type="hidden" value="'.$_POST["encounter_nr"].'"/>');
$smarty->assign('sClearEnc','<input class="segButton" id="clear-enc" type="button" value="Clear" onclick="clearEncounter()"  />');
$smarty->assign('sReturnAddress','<textarea class="segInput" id="return_address" name="return_address" cols="40" rows="3" readonly="readonly">'.$_POST["return_address"].'</textarea>');
$smarty->assign('sSelectEnc','<button type="button" id="select-enc" class="segButton" onclick="keyF9(); return false;" '.($returnID ? ' disabled="disabled"':'').'><img src="'.$root_path.'gui/img/common/default/cursor.png"/>Select</button>');

$smarty->assign('is_refund', $isRefund);
if ($isRefund) {
    $smarty->assign('sRefundAmount', '<input class="segInput" type="text" id="refund_amount" name="refund_amount" size="15" readonly="readonly" style="text-align:right" value="' . $_POST['refund_amount'] . '"/>');
    $chk_adjust = ($_POST['refund_amount_fixed'] && $_POST['refund_amount'] != $_POST['refund_amount_fixed']);
    $smarty->assign('sCheckAdjust', '<input type="checkbox" id="chk_adjust" name="chk_adjust" class="segInput" value="1" onclick="$(\'refund_amount_fixed\').disabled=!this.checked" ' . ( $chk_adjust ? 'checked="checked"' : '' ) . '/><label class="segInput" for="chk_adjust">Adjust amount</label>');
    $smarty->assign('sAdjustAmount', '<input class="segInput" type="text" id="refund_amount_fixed" name="refund_amount_fixed" size="15" style="text-align:right"' .
            ($chk_adjust ? '' : ' disabled="disabled"') . ' value="' . $_POST['refund_amount_fixed'] . '"/>');
}

/*require_once($root_path.'include/care_api_classes/class_area.php');
$ac=new SegArea;
if ($returnID)
$select_area = '
    <input type="hidden" name="area" value="'.$_POST['area'].'" />
    <input class="segInput" type="text" disabled="disabled" value="'.addslashes($ac->getAreaName($_POST['area'])).'">
';
else
$select_area = '
    <input type="hidden" name="area" value="'.$_GET['area'].'" />
    <input class="segInput" type="text" disabled="disabled" value="'.addslashes($ac->getAreaName($_GET['area'])).'">
';
$smarty->assign('sSelectArea',$select_area); */
$pharma_area = $_POST["area"] ? $_POST["area"]: $_GET["area"];
require_once($root_path.'include/care_api_classes/class_product.php');
$prod_obj=new Product;
$prod=$prod_obj->getAllPharmaAreas();

$select_area = '';
while($row=$prod->FetchRow()){
    if (strtolower($pharma_area) == "all") {
        $select_area .= "	<option value=\"" . $row['area_code'] . "\">" . $row['area_name'] . "</option>\n";
    } else if (strtolower($pharma_area) == strtolower($row['area_code'])) {
        $select_area .= "	<option value=\"" . $row['area_code'] . "\" selected='selected'>" . $row['area_name'] . "</option>\n";
    }
}

if (strtolower($pharma_area) == "all") {
    $selected_area = '<select class="segInput" name="area" id="area" onchange="if (warnClear()) { emptyList();}">' . "\n" . $select_area . "</select>\n";
} else {
    $selected_area = '<select class="segInput" name="area" id="area" disabled="disabled" onchange="if(warnClear()){ emptyList();}">' . "\n" . $select_area . "</select>\n";
    $smarty->assign('sHiddenArea', '<input type="hidden" id="area2" name="area2" value="' . $pharma_area . '"/>');
}
$smarty->assign('sSelectArea', $selected_area);

# Stock date
$dbtime_format = "Y-m-d H:i";
$fulltime_format = "F j, Y g:ia";
if ($_POST['return_date']) {
    $dStockDate = strtotime($_POST['return_date']);
    $curDate = date($dbtime_format, $dStockDate);
    $curDate_show = date($fulltime_format, $dStockDate);
} else {
    $curDate = date($dbtime_format, time());
    $curDate_show = date($fulltime_format, time());
}

$smarty->assign('sReturnDate', '<span id="show_return_date" class="segInput" style="font-weight:bold; color:#0000c0; padding:0px 2px;width:200px; height:24px">' . $curDate_show . '</span><input class="segInput" name="return_date" id="return_date" type="hidden" value="' . $curDate . '" style="font:bold 12px Arial">');
if ($readOnly) {
//$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="return_date_trigger" class="segSimulatedLink" align="absmiddle" style="margin-left:2px;opacity:0.5">');
    $smarty->assign('sCalendarIcon', '<button class="segButton" id="return_date_trigger" disabled="disabled"><img ' . createComIcon($root_path, 'calendar.png', '0') . 'style="margin-left:2px;opacity:0.5">Select date</button>');
} else {
//$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="return_date_trigger" class="segSimulatedLink" align="absmiddle" style="margin-left:2px;cursor:pointer">');
    $smarty->assign('sCalendarIcon', '<button class="segButton" id="return_date_trigger"><img ' . createComIcon($root_path, 'calendar.png', '0') . '>Select date</button>');
    $jsCalScript = "<script type=\"text/javascript\">
    Calendar.setup ({
        displayArea : \"show_return_date\",
        inputField : \"return_date\",
        ifFormat : \"%Y-%m-%d %H:%M\",
        daFormat : \"	%B %e, %Y %I:%M%P\",
        showsTime : true,
        button : \"return_date_trigger\",
        singleClick : true,
        step : 1
    });
</script>";
    $smarty->assign('jsCalendarSetup', $jsCalScript);
}
$smarty->assign('sComments', '<textarea class="segInput" name="comments" style="margin-left:5px; width:98%; height:60px">' . $_POST['comments'] . '</textarea>');
$smarty->assign('sReturnItems', "<tr><td colspan=\"10\">Item list is currently empty...</td></tr>");

if ($readOnly) {
    $smarty->assign('sAddItem', '<button class="segButton" type="button" id="add-item" disabled><img src="' . $root_path . 'gui/img/common/default/add.png"/>Select items</button>');
    $smarty->assign('sEmptyList', '<button class="segButton" type="button" id="clear-list" disabled><img src="' . $root_path . 'gui/img/common/default/box.png"/>Empty list</button>');
} else {
    $smarty->assign('sAddItem', '<button class="segButton" id="add-item" type="button" onclick="openReturnTray(\'' . $isRefund . '\'); return false;"><img src="' . $root_path . 'gui/img/common/default/add.png"/>Select items</button>');
    $smarty->assign('sEmptyList', '<button class="segButton" id="clear-list" type="button" onclick="if (confirm(\'Clear the return list?\')) emptyList(); return false;"><img src="' . $root_path . 'gui/img/common/default/box.png"/>Empty list</button>');
}

$smarty->assign('sBreakButton','<button class="segButton" onclick="window.location=\''.$breakfile.'\'; return false;" onsubmit="return false;"><img src="'.$root_path.'gui/img/common/default/cancel.png"/>Close</button>');
if ($readOnly) {
    $smarty->assign('sContinueButton','<button class="segButton" type="button" disabled="disabled"><img src="'.$root_path.'gui/img/common/default/accept.png"/>Submit</button>');
} else {
    $smarty->assign('sContinueButton','<button class="segButton" onsubmit="return validate();"><img src="'.$root_path.'gui/img/common/default/accept.png"/>Submit</button>');
}

$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.URL_APPEND."&clear_ck_sid=".$clear_ck_sid.'&target=edit&nr='.$returnID.'&from='.$_GET['from'].'&refund='.$_GET['refund'].'" method="POST" id="orderForm" name="inputform" onSubmit="return validate()">');
$smarty->assign('sFormEnd','</form>');

ob_start();
$sTemp='';

?>

<input type="hidden" name="submitted" value="1" />

<?php

$sTemp = ob_get_contents();
ob_end_clean();

$sBreakImg ='close2.gif';
#$sBreakImg ='cancel.gif';
$smarty->assign('sHiddenInputs',$sTemp);
# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','order/return.tpl');
$smarty->display('common/mainframe.tpl');

