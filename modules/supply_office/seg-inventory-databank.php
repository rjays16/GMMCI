<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

define('NO_2LEVEL_CHK',1);
define('LANG_FILE','products.php');
global $db;

$local_user='ck_prod_db_user';
require_once($root_path.'include/inc_front_chain_lang.php');
# Create products object
$dbtable='care_config_global'; // Table name for global configurations
$GLOBAL_CONFIG=array();
$new_date_ok=0;
# Create global config object

require_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/inc_date_format_functions.php');

//include xajax common file . .
require($root_path.'modules/supply_office/ajax/databank.common.php');

//LISTGEN YEHEY
require_once($root_path.'modules/listgen/listgen.php');
$listgen = new ListGen($root_path);

$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
if($glob_obj->getConfig('date_format')) $date_format=$GLOBAL_CONFIG['date_format'];
$date_format=$GLOBAL_CONFIG['date_format'];
$phpfd=$date_format;
$phpfd=str_replace("dd", "%d", strtolower($phpfd));
$phpfd=str_replace("mm", "%m", strtolower($phpfd));
$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
$phpfd=str_replace("yy","%y", strtolower($phpfd));

if($_GET['from']=='phs' || $_POST['from']=='phs')
    $breakfile=$root_path."modules/phs/seg-phs-function.php".URL_APPEND."&userck=$userck";
else
    $breakfile=$root_path."modules/supply_office/seg-supply-functions.php".URL_APPEND."&userck=$userck";

$imgpath=$root_path."pharma/img/";
$thisfile='seg-inventory-databank.php';


# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme
include_once($root_path."include/care_api_classes/class_pharma_product.php");
require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

$prod_obj = new SegPharmaProduct();

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"Inventory::Product databank");

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('products_db.php','input','$mode','$cat')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"Inventory::Product databank");

 # Assign Body Onload javascript code
 $onLoadJS='onload="plst.reload()"';
 $smarty->assign('sOnLoadJs',$onLoadJS);

 # Collect javascript code

ob_start();
     # Load the javascript code
?>

<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<link rel="stylesheet" type="text/css" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript">
function deleteItem(id, flag) {
        //var dform = document.forms[0]
        //$('delete').value = id
        //dform.submit()
	xajax.call('deleteProduct',{parameters:[id, flag], context:this});
}

function validate() {
        return true;
}

function addProductRow(details) {
        list = $("plst");
	details = Object.extend({
		id: '',
		bestellnum: '',
		prod_class: '',
		artikelname: '',
		generic: '',
		price_cash: 0,
		sc_price: 0,
		stock: 0,
		is_deleted: 0
	},details);
        var icon;
        if (list) {
		var dBody=list.select("tbody").first();
            if (dBody) {
                var lastRowNum = null,
					id = details["bestellnum"],
					dRows = dBody.select("tr"),
					isDeleted = parseInt(details['is_deleted'])
                if (details["FLAG"]=="1") {
                    alt = (dRows.length%2)+1
                    switch (details['prod_class'].toString().toUpperCase()) {
                        case "S" : icon = "pharma_supplies.png"; break;
                        case "M" : icon = "pharma_meds.png"; break;
                        case "NS":
                        case "DS":
                        case "LS":
                        case "OS": icon = "pharma_nonmeds.png"; break;
                        case "E" : icon = "pharma_equip.png"; break;
                        case "B" : icon = "pharma_blood.png"; break;
                        case "HS": icon = "pharma_housekeeping.png"; break;
                        default  : icon = "blank_tag.png";
                    }
				var sclass = "";
				if (isDeleted ) {
					sclass = 'red';
				}
				else {
					if (dRows.length%2>0)
						sclass = 'alt';
					else
						sclass='';
				}
				var src='<tr class="'+sclass+'">' +
                        '<td style="padding:4px" align="center"><img src="<?= $root_path ?>gui/img/common/default/'+icon+'" align="absmiddle"/></td>'+
                        '<td  style="color:800000;font:bold 11px Tahoma">'+id+'</td>'+
                        '<td>'+
                            details["artikelname"]+'<br/>'+
                            '<span style="color:#000066; font:normal 11px Arial">'+details['generic']+'</span>'+
                        '</td>'+
                        '<td align="right">'+_lgformatNumber(parseFloat(details["price_cash"]),2)+'</td>'+
                        '<td align="right">'+_lgformatNumber(parseFloat(details["supplier_price"]),2)+'</td>'+
                        '<td align="right">'+_lgformatNumber(parseFloat(details["sc_price"]),2)+'</td>'+
                        '<td align="center">'+_lgformatNumber(parseFloat(details["stock"]))+'</td>'+
                        '<td class="centerAlign" nowrap="nowrap">'+
                            '<img class="segSimulatedLink" src="<?=$root_path?>images/cashier_edit.gif" border="0" align="absmiddle" onclick="editProduct(\''+id+'\')"/>\n'+
							(
							isDeleted ?
							('<img class="segSimulatedLink" src="<?=$root_path?>images/cashier_check.png" border="0" align="absmiddle" onclick="if (confirm(\'Restore this deleted item?\')) deleteItem(\''+id+'\',0)"/>'):
							('<img class="segSimulatedLink" src="<?=$root_path?>images/cashier_delete.gif" border="0" align="absmiddle" onclick="if (confirm(\'Delete this item?\')) deleteItem(\''+id+'\',1)"/>')
							)+
                        '</td>'+
                    '</tr>';
                }
                else {
                    src = "<tr><td colspan=\"8\">List is currently empty...</td></tr>";
                }
                dBody.innerHTML += src;
                return true;
            }
        }
        return false;
}

function editProduct(nr) {
    return overlib(
    OLiframeContent('seg-inventory-product-edit.php?nr='+nr, 670, 400, 'fProduct', 0, 'no'),
    WIDTH,670, TEXTPADDING,0, BORDER,0,
    STICKY, SCROLL, CLOSECLICK, MODAL,
    CLOSETEXT, '<img src=<?= $root_path ?>/images/close_red.gif border=0 >',
    CAPTIONPADDING,2,
    CAPTION,'Product Editor',
    MIDX,0, MIDY,0,
    STATUS,'Product editor');
}



function search() {
	plst.fetcherParams = {
		codename:$('codename').value,
		generic:$('generic').value,
		prodclass:$('prodclass').value,
		showdeleted: $('show_deleted').value,
        //added by monmon
        area : $('inventoryAreas').value
	};
//	alert($('show_deleted').value)
    document.getElementById('btnsearch').disabled = true;
    plst.reload();
    setTimeout(function(){
        document.getElementById('btnsearch').disabled = false;
    }, 500);
}

function tabClick(listID, index) {
    var dList = $(listID);
    if (dList) {
      var listItems = dList.getElementsByTagName("LI");
      if (listItems[index]) {
        for (var i=0;i<listItems.length;i++) {
          if (i!=index) {
              listItems[i].className = "";
              if ($("tab"+i)) $("tab"+i).style.display = "none";
          }
        }
        if ($("tab"+index)) $("tab"+index).style.display = "block";
        listItems[index].className = "segActiveTab";
      }
    }
}

function toggleTBody(list) {
    var dTable = $(list);
    if (dTable) {
      var dBody = dTable.getElementsByTagName("TBODY")[0];
      if (dBody) dBody.style.display = (dBody.style.display=="none") ? "" : "none";
    }
}

function enableInputChildren(id, enable) {
    var el=$(id);
    if (el) {
      var children = el.getElementsByTagName("INPUT");
      if (children) {
        for (i=0;i<children.length;i++) {
          children[i].disabled = !enable;
        }
        return true;
      }
    }
    return false;
}

document.observe("dom:loaded", function() {

	$('codename').observe('keypress', function(event) {
		if (event.keyCode == Event.KEY_RETURN) {
			search();
		}
	});

	$('generic').observe('keypress', function(event) {
		if (event.keyCode == Event.KEY_RETURN) {
			search();
		}
	});

	$('prodclass').observe('keypress', function(event) {
		if (event.keyCode == Event.KEY_RETURN) {
			search();
  }
	});
});
</script>
<?php

$xajax->printJavascript($root_path.'classes/xajax_0.5');
$listgen->printJavascript($root_path);

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

$listgen->setListSettings('MAX_ROWS','10');
$plst = &$listgen->createList('plst',array('Type','Item Code','Item name/Generic name','Sell Price', 'Unit Cost','Senior', 'Hosp. stock', ''),array(0,0,1,0,0,0,0,NULL),'populateProducts');
$plst->addMethod = 'addProductRow';
$plst->fetcherParams = array();
$plst->columnWidths = array("6%", "17%", "*", "11%", "9%", "9%", "7%", "8%");
$smarty->assign('sProductList',$plst->getHTML());

$smarty->assign('sSearchResults',$rows);
$smarty->assign('sRootPath',$root_path);

# Render form values
$smarty->assign('sCodeName', '<input class="segInput" type="text" name="codename" id="codename" size="20" value="'.$_REQUEST['codename'].'">');
$smarty->assign('sGenericName', '<input class="segInput" type="text" size="20" name="generic" id="generic" value="'.$_REQUEST['generic'].'"/>');

# Product classification
$classifcationHTML = "<select class=\"segInput\" id=\"classification\" name=\"classification\">
<option style=\"font-style:italic\" value=\"\">Any</option>\n";
$result = $db->Execute("SELECT * FROM seg_product_classification ORDER BY class_name");
if ($result) {
    while ($row=$result->FetchRow()) {
        $checked = ($row["class_code"]==$_REQUEST['classification']) ? 'selected="selected"' : "";
        $classifcationHTML.="                        <option value=\"".$row["class_code"]."\" $checked>".$row['class_name']."</option>\n";
    }
}
$classifcationHTML .= "                    </select>";
$smarty->assign('sSelectClassification',$classifcationHTML);

# Product class
$optionstype = $prod_obj->getProdClassOption();
$smarty->assign('sProdClass',"<select class='segInput' name='prodclass' id='prodclass' >
                   $optionstype </select>");

#added by monmon : Inventory Area
$areas = $prod_obj->getInventoryAreas();
$smarty->assign('sAreaList',"<select class='segInput' name='inventoryAreas' id='inventoryAreas'>$areas</select>");

$smarty->assign('sCreateProduct','<button class="segButton"
	onclick="overlib(
	OLiframeContent(\'seg-inventory-product-edit.php?stat=new\', 670, 400, \'fProduct\', 0, \'no\'),
        WIDTH,670, TEXTPADDING,0, BORDER,0,
                STICKY, SCROLL, CLOSECLICK, MODAL,
                CLOSETEXT, \'<img src='.$root_path.'/images/close_red.gif border=0 >\',
        CAPTIONPADDING,2,
                CAPTION,\'Product Editor\',
        MIDX,0, MIDY,0,
	STATUS,\'Product editor\'); return false;"><img src="'.$root_path.'gui/img/common/default/pill_add.png"/>New product</button>');

$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.URL_APPEND."&clear_ck_sid=".$clear_ck_sid.'&ref='.$sRefNo.'&dept='.$sDept.'" method="POST" id="orderForm" name="inputform" onSubmit="return validate()">');
$smarty->assign('sFormEnd','</form>');


ob_start();
$sTemp='';

?>
<input type="hidden" name="submitted" value="1" />
<input type="hidden" name="refno" value="<?php echo $sRefNo?>">
<input type="hidden" name="dept" value="<?php echo $sDept?>">
<input type="hidden" name="sid" value="<?php echo $sid?>">
<input type="hidden" name="lang" value="<?php echo $lang?>">
<input type="hidden" name="cat" value="<?php echo $cat?>">
<input type="hidden" name="userck" value="<?php echo $userck?>">
<input type="hidden" name="mode" id="modeval" value="<?php if($saveok) echo "update"; else echo "save"; ?>">
<input type="hidden" name="encoder" value="<?php echo  str_replace(" ","+",$HTTP_COOKIES_VARS[$local_user.$sid])?>">
<input type="hidden" name="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>">
<input type="hidden" name="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>">
<input type="hidden" name="lockflag" value="<?php echo  $lockflag?>">
<input type="hidden" name="update" value="<?php if($saveok) echo "1"; else echo $update;?>">

<!--added by bryan-->
<input type="hidden" id="from" name="from" value="<?= $_REQUEST['from'] ?>" />

<input type="hidden" id="delete" name="delete" value="" />
<input type="hidden" id="page" name="page" value="<?= $current_page ?>" />
<input type="hidden" id="lastpage" name="lastpage"  value="<?= $last_page ?>" />
<input type="hidden" id="jump" name="jump">
<?php

$sTemp = ob_get_contents();
ob_end_clean();

$sBreakImg ='close2.gif';
$sBreakImg ='cancel.gif';
$smarty->assign('sHiddenInputs',$sTemp);
$smarty->assign('sBreakButton','<input class="segInput" type="button" align="center" value="Cancel payment">');
$smarty->assign('sContinueButton','<input class="segInput" type="submit" src="'.$root_path.'images/btn_submitorder" align="center" value="Process payment">');

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','supply_office/inventory-databank.tpl');
$smarty->display('common/mainframe.tpl');

?>