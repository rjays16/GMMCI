<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
//require($root_path."modules/pharmacy/ajax/order-psearch.common.php");
require($root_path."modules/billing/ajax/billing-psearch.common.php");
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
//$local_user='ck_prod_db_user';
$local_user='aufnahme_user';

require_once($root_path.'include/inc_front_chain_lang.php');

//$db->debug=1;

$thisfile=basename(__FILE__);
$breakfile = $root_path."modules/billing/billing-main.php".URL_APPEND."&userck=$userck";
$imgpath=$root_path."modules/pharma/img/";

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
 //$smarty->assign('sOnLoadJs','onLoad="document.getElementById(\'search\').focus();DisabledSearch();"');
 // $smarty->assign('sOnLoadJs','onLoad="document.search.select(); DisabledSearch();"');
   $smarty->assign('sOnLoadJs','onLoad="document.getElementById(\'searchkey\'); DisabledSearch();"');
 # Collect javascript code
 ob_start()

?>
<script language="javascript" >
<!--
var AJAXTimerID = 0;
//var lastSearch="";

function startAJAXSearch(searchID, page, e) {
    //var includeEnc = window.parent.$("iscash1").checked ? '0' : '1';
    var includeEnc = '1';
    var searchEL = $(searchID);
    var searchLastname = $('firstname-too').checked ? '1' : '0';
    var characterCode;

    var scase_no = $('search_caseno').value;
    if (!scase_no) scase_no = '';

    if (searchEL.value == '') {
        if (scase_no != '') searchEL.value = '*';
    }

    if (typeof e == 'undefined')
        characterCode = 13
    else {
        if(e && e.which) { //if which property of event object is supported (NN4)
            characterCode = e.which; //character code is contained in NN4's which property
        }
        else {
            characterCode = e.keyCode; //character code is contained in IE's keyCode property
        }
    }

    if (searchEL && (characterCode == 13)) {
        searchEL.style.color = "#0000ff";
        if (AJAXTimerID) clearTimeout(AJAXTimerID);
        $("ajax-loading").style.display = "";
        $("person-list-body").style.display = "none";

        if (scase_no == '')
            AJAXTimerID = setTimeout("xajax_populatePersonList('"+searchID+"','"+searchEL.value+"',"+page+","+searchLastname+","+includeEnc+",'"+''+"', true)", 100);
        else
            AJAXTimerID = setTimeout("xajax_populatePersonList('"+searchID+"','"+searchEL.value+"',"+page+","+searchLastname+","+includeEnc+","+scase_no+", true)", 100);
//        lastSearch = searchEL.value;
    }
}

function endAJAXSearch(searchID) {
    var searchEL = $(searchID);
    if (searchEL) {
        $("ajax-loading").style.display = "none";
        $("person-list-body").style.display = "";
        searchEL.style.color = "";
    }
}

function isValidSearch(key) {
	if (typeof(key)=='undefined') return false;
	var s=key.toUpperCase();
	var skey =$('searchkey').value;
	if (skey=='')
		{return (
		/^\d{1,2}\/\d{1,2}\/\d{4}$/.test(s) ||
		/^\d{1,2}\-\d{1,2}\-\d{4}$/.test(s) ||
		/^\d{10,}$/.test(s)
		);}
	return (
	/^[A-Z0-9?\-\.]{2}[A-Z0-9?\-\. ]*\s*,\s*[A-Z0-9?\-\.]{2}[A-Z0-9?\-\. ]*$/.test(s) ||
	/^\d{1,2}\/\d{1,2}\/\d{4}$/.test(s) ||
	/^\d{1,2}\-\d{1,2}\-\d{4}$/.test(s) ||
	/^\d+$/.test(s)
	);
}

function DisabledSearch(){
	var scase_no = $('search_caseno').value;
	var skey =$('searchkey').value;
    if (scase_no == '')
    	{var b=isValidSearch(document.getElementById('searchkey').value);}
    else if (skey == '')
		{var b=isValidSearch(document.getElementById('search_caseno').value);}

	document.getElementById("search-btn").style.cursor=(b?"pointer":"default");
	document.getElementById("search-btn").disabled = !b;
}

// -->
</script>
<script type="text/javascript" src="<?=$root_path?>modules/billing/js/billing-person-search-gui.js?t=<?=time()?>"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/datefuncs.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<?php
$xajax->printJavascript($root_path.'classes/xajax-0.2.5');
$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

# Buffer page output

ob_start();

?>
    <table width="98%" cellspacing="2" cellpadding="2" style="margin:1%">
        <tbody>
            <tr>
                <td style="font:bold 12px Arial; background-color:#e5e5e5; color: #2d2d2d" >
                    <table width="95%" border="0" cellpadding="0" cellspacing="0" style="font:bold 12px Arial; color:#2d2d2d; margin:1%">
                        <tr>
                            <td width="15%">
                                Search person<br />
                                <a href="javascript:gethelp('person_search_tips.php')" style="text-decoration:underline">Tips & tricks</a>
                            </td>
                            <td valign="middle" width="*">
                                <input id="searchkey" class="segInput" type="text" style="width:55%; font: bold 12px Arial" align="absmiddle" onBlur="DisabledSearch();" onKeyUp="DisabledSearch(); if ((event.keyCode == 13)&&(isValidSearch(document.getElementById('searchkey').value))) startAJAXSearch('searchkey',0);return false;; " />&nbsp;Case No.:
                                <input id="search_caseno" class="segInput" type="text" style="width:20%; font: bold 12px Arial" align="absmiddle" onBlur="DisabledSearch();" onKeyUp="DisabledSearch(); if ((event.keyCode == 13)&&(isValidSearch(document.getElementById('search_caseno').value))) startAJAXSearch('searchkey',0);return false;;"/>
                                <input type="image" id="search-btn" src="<?= $root_path ?>images/his_searchbtn.gif" onclick="startAJAXSearch('searchkey',0);return false;" align="absmiddle" /><br />
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><input type="checkbox" id="firstname-too" checked> Search for first names too.</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <div style="display:block; border:1px solid #8cadc0; overflow-y:scroll; height:290px; width:100%; background-color:#e5e5e5">
                        <table id="person-list" class="segList" cellpadding="1" cellspacing="1" width="100%">
                            <thead>
                                <tr class="nav">
                                    <th colspan="10">
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
                                    <th width="6%">HRN</th>
                                    <th width="3%">Sex</th>
                                    <th width="25%">Patient's Name</th>
                                    <th width="8%" style="font-size:11px" nowrap="nowrap">Date of Birth</th>
                                    <th width="26%">Confinement</th>
                                    <th width="4%">PHIC?</th>
                                    <th width="7%">Confine Type</th>
                                    <th width="6%">Case No.</th>
<!--                                    <th width="8%">Status</th>
                                    <th width="8%">Type</th>  -->
                                    <th width="1%"></th>
                                    <th width="1%"></th>
                                </tr>
                            </thead>
                            <tbody id="person-list-body">
                                <tr>
                                    <td colspan="10" style="font-weight:normal">No such person exists...</td>
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
    <input type="hidden" name="bill_type" id='bill_type' value="<?= $_GET['bill_type']?>">
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