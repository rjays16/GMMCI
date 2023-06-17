<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'modules/billing_new/ajax/billing_new-more-pharmaorder.common.php');

require($root_path.'include/inc_environment_global.php');

define('LANG_FILE','lab.php');
define('NO_2LEVEL_CHK',1);
$local_user='ck_prod_db_user';
require_once($root_path.'include/inc_front_chain_lang.php');
$thisfile=basename(__FILE__);
$title = "More Drugs & Medicines or Supplies";
$breakfile="";

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
 $smarty->assign('sToolbarTitle',"$title $LDLabDb $LDSearch");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$title");

 # Assign Body Onload javascript code
 $smarty->assign('sOnLoadJs','onLoad="getPharma_Areas();document.getElementById(\'search\').focus();"');

 # Collect javascript code
 ob_start()

?>
<script language="javascript" >
<!--

// -->
</script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/gen_routines.js"></script>
<script type="text/javascript" src="<?=$root_path?>modules/billing_new/js/billing-more-pharmaorder-new.js?t=<?=time()?>"></script>
<?php
$xajax->printJavascript($root_path.'classes/xajax_0.5');
$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

# Buffer page output

ob_start();

?>
    <span id="ajax_display"></span> 
    <table width="98%" cellspacing="2" cellpadding="2" style="margin:0.7%">
        <tbody>
            <tr>
                <td style="font:bold 12px Arial; background-color:#e5e5e5; color: #2d2d2d">
                    <div style="padding:4px 2px; padding-left:10px; ">
                    <table width="100%">
                        <tr><td width="70%" style="font:bold 12px Arial; color: #2d2d2d">
                        Search Drug, Med or Supply
                        <input id="search" name="search" class="segInput" type="text" style="width:48%; margin-left:10px; font: bold 12px Arial" align="absmiddle" onkeyup="if (this.value.length >= 3) startAJAXSearch(this.id,0)" onKeyPress="checkEnter(event,this.id)"/>
                        <input type="image" id="search_img" name="search_img" src="<?= $root_path ?>images/his_searchbtn.gif" onclick="startAJAXSearch('search',0);return false;" align="absmiddle" /></td>
                        <td align="right">
                        <select id="area_combo" style="font:bold 12px Arial" name="area_combo" onchange="jsOptionChange(this, this.options[this.selectedIndex].value)">
                            <option value="-">- Select Pharmacy Area -</option>
                        </select></td></tr></table>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div style="display:block; border:1px solid #8cadc0; overflow-y:scroll; height:305px; width:100%; background-color:#e5e5e5">
                        <table id="pharma_items-list" class="segList" cellpadding="1" cellspacing="1" width="100%">
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
                                    <th width="*">Name/Description</th>
                                    <th width="15%" align="center">Code</th>
                                    <th width="10%" style="" nowrap="nowrap">Type</th> 
                                    <th width="20%" style="" nowrap="nowrap">Unit Price</th>                                    
                                    <th width="2%">&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="5" style="font-weight:normal">No such drug, medicine or supply exists...</td>
                                </tr>
                            </tbody>
                        </table>
                        <img id="ajax-loading" src="<?= $root_path ?>images/loading6.gif" align="absmiddle" border="0" style="display:none"/>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>


    <input type="hidden" name="sid" value="<?php echo $sid?>">
    <input type="hidden" name="lang" value="<?php echo $lang?>">
    <input type="hidden" name="cat" value="<?php echo $cat?>">
    <input type="hidden" name="userck" value="<?php echo $userck ?>">
    <input type="hidden" name="mode" value="search">
    <input type="hidden" id="area_code" name="area_code" value="" />
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
