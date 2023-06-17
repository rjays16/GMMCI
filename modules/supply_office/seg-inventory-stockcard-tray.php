<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php'); 
require($root_path."modules/supply_office/ajax/seg-supply-office-request-tray.common.php");

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

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

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


 # Collect javascript code
 ob_start()

?>
<script language="javascript" >
<!--
var AJAXTimerID=0;
var lastSearch="", lastSearchPage=-1;

function init() {
     shortcut.add('ESC', closeMe,
        {
            'type':'keydown',
            'propagate':false
        }
    );

    setTimeout("$('search').focus()",100);
    
    xajax_populateTypesCombo();
}

function closeMe() {
    window.parent.cClick();
}

function startAJAXSearch(searchID, page) {

    var searchEL = $(searchID);
    if (!page) page = 0;
    var val = $('type_nr').value;
        
    if (true) {
        searchEL.style.color = "#0000ff";
        if (AJAXTimerID) clearTimeout(AJAXTimerID);
        $("ajax-loading").style.display = "";
        var script = "xajax_populateProductList('"+searchID+"',"+page+",'"+searchEL.value+"','" +val+"')";
 
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

// -->
</script> 
<script type="text/javascript" src="<?=$root_path?>js/gen_routines.js"></script> 
<script type="text/javascript" src="<?=$root_path?>modules/supply_office/js/stockcard-tray-gui.js?t=<?=time()?>"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/shortcut.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>

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
            <tr style="background-color:#e5e5e5; color: #2d2d2d" >
                <!-- <td style="background-color:#e5e5e5; color: #2d2d2d" valign="middle"> -->
                <td align="right" width="120px">
                    <div style="padding:0px 0px; padding-left:10px; ">
                        <p><strong>Item Type:</strong></p>
                    </div>
                </td>
                <td valign="middle">
                    <div style="padding:0px 0px; padding-left:2px; ">
                        <select style="width:350px" id="item_type" name="item_type" onchange="$('type_nr').value=this.options[this.selectedIndex].value;startAJAXSearch('search',0);return false;">
                            <option value="0">- All Types -</option>
                        </select>                                        
                    </div>
                </td>
            </tr>
            <tr style="background-color:#e5e5e5; color: #2d2d2d" >
              <!--  <td style="background-color:#e5e5e5; color: #2d2d2d" > -->
                <td align="right">
                    <div style="padding:0px 0px; padding-left:10px; ">
                        <p><strong>Search Product:</strong></p>
                    </div>  
                </td>
                <td valign="middle">
                    <div style="padding:0px 0px; padding-left:2px; "> 
                        <input id="search" class="segInput" type="text" style="width:60%; margin-left:1px; font: bold 12px Verdana" align="absmiddle" onkeyup="if (event.keyCode==13) startAJAXSearch(this.id,0)" />
                        <input type="image" src="<?= $root_path ?>images/his_searchbtn.gif" onclick="startAJAXSearch('search',0);return false;" align="absmiddle" />
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div style="display:block; border:1px solid #8cadc0; overflow-y:scroll; height:285px; width:100%; background-color:#e5e5e5">
                        <table id="product-list" class="jedList" cellpadding="1" cellspacing="1" width="100%">
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
                                    <th width="15%">Code</th>
                                    <th width="*" align="center">Item Description</th>
                                    <th width="10%">Select Item</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="4">No such product exists...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <img id="ajax-loading" src="<?= $root_path ?>images/loading6.gif" align="absmiddle" border="0" style="display:none"/>
                </td>
            </tr>
        </tbody>
    </table>

    <input type="hidden" name="sid" value="<?php echo $sid?>">
    <input type="hidden" name="lang" value="<?php echo $lang?>">
    <input type="hidden" name="cat" value="<?php echo $cat?>">
    <input type="hidden" name="userck" value="<?php echo $userck ?>">
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
