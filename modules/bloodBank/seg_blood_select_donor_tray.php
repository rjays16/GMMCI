<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path."modules/bloodBank/ajax/blood-donor-register.common.php");
require($root_path.'include/inc_environment_global.php');
/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org, 
*
* See the file "copy_notice.txt" for the licence notice
*/
define('NO_2LEVEL_CHK',1);
define('LANG_FILE','products.php');
$local_user='ck_prod_db_user';
require_once($root_path.'include/inc_front_chain_lang.php');

//$db->debug=1;

$thisfile=basename(__FILE__);

$cat = "pharma";
$title="Patient Records::Select patient";
$breakfile=$root_path."modules/registration_admission/seg-close-window.php".URL_APPEND."&userck=$userck";
$imgpath=$root_path."pharma/img/";

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
$smarty->assign('sToolbarTitle',"$title");

# href for the back button
// $smarty->assign('pbBack',$returnfile);

# href for the help button
$smarty->assign('pbHelp',"javascript:gethelp('products_db.php','search','$from','$cat')");

# href for the close button
$smarty->assign('breakfile',$breakfile);

# Window bar title
$smarty->assign('sWindowTitle',"$title");

# Assign Body Onload javascript code
$smarty->assign('sOnLoadJs','onLoad="init()"');

# Collect javascript code
ob_start();

global $lang;
?>

<script type="text/javascript" language="javascript">
<?php
    require_once($root_path.'include/inc_checkdate_lang.php'); 
?>
</script>
<script language="javascript" src="<?=$root_path?>js/setdatetime.js"></script>
<script language="javascript" src="<?=$root_path?>js/checkdate.js"></script>
<script language="javascript" src="<?=$root_path?>js/dtpick_care2x.js"></script>
<script language="javascript" >
<!--
var AJAXTimerID=0;
var lastSearch="";

var reg = {};
var pre = {};

function init() {
     shortcut.add('ESC', closeMe,
        {
            'type':'keydown',
            'propagate':false,
        }
    );
    
    setTimeout("$('search').focus()",100);
}

function closeMe() {
    window.parent.cClick();
}

<?php
    $varArray = array(
        'var_pid'=>'',
        'var_rid'=>'',
        'var_encounter_nr'=>'',
        'var_discountid'=>'',
        'var_discount'=>'',
        'var_name'=>'',
        'var_addr'=>'',
        'var_clear'=>'',
        'var_enctype'=>'',
        'var_enctype_show'=>'',
        'var_include_enc'=>'0',
        'var_include_walkin'=>'1',
        #added by VAN
        'var_location'=>'',
        'var_medico'=>'0',
    #added by Omick, January 15, 2009
    'var_gender'=>'',
    'var_age'=>'',
    #end omick
    #added by Omick, May 26, 2009
    'var_date_admitted'=>'',
    'var_room_ward'=>''
    #end omick    
    );
    
    if ($_GET['var_include_enc']=='1') {
        $_GET['var_include_walkin']='0';
        $_REQUEST['var_include_walkin']='0';
        $_GET['var_reg_walkin']='0';
        $_REQUEST['var_reg_walkin']='0';
    }
    
    foreach ($varArray as $i=>$v) {
        $value = $_REQUEST[$i];
        if (!$value) $value = $v;
        if (!is_numeric($value)) $value = "'$value'";
        echo "var $i=$value;\n";
    }
?>

function startAJAXSearch(searchID, page) {     
    var includeEnc = var_include_enc ? 1 : 0;
    var includeWalkin = var_include_walkin ? 1 : 0;
    var searchEL = $(searchID);
    
    //var searchLastname = $('firstname-too').checked ? '1' : '0'; 
    //commented out in accordance with search code changes; aug.5,2008; pet
    var searchLastname = 0;
    var searchText = searchEL.value;
    if (searchEL && searchEL.value.length >= 3) {
        searchEL.style.color = "#0000ff";
        if (AJAXTimerID) clearTimeout(AJAXTimerID);
        //$("ajax-loading").style.display = "";
        //$("person-list-body").style.display = "none";
        searchText = searchText.replace("'","\\'");
        AJAXTimerID = setTimeout("xajax_selectDonorList('"+searchText+"',"+page+")",100);
        lastSearch = searchEL.value;
    }
}

function updateControls() {
    var s = $('search').value;
    $('search-btn').disabled = (s.length < 3);
}

function endAJAXSearch(searchID) {
    var searchEL = $(searchID);
    if (searchEL) {
        $("ajax-loading").style.display = "none";
        $("person-list-body").style.display = "";
        searchEL.style.color = "";
    }
}

function tabClick(obj) {
  if (obj.className=='segActiveTab') return false;    
  var dList = obj.parentNode;
  var tab;
  if (dList) {
    var listItems = dList.getElementsByTagName("LI");
    if (obj) {
      for (var i=0;i<listItems.length;i++) {
        if (obj!=listItems[i]) {
          listItems[i].className = "";
          tab = listItems[i].getAttribute('segTab');
          if ($(tab))
            $(tab).style.display = "none";
        }
      }
      tab = obj.getAttribute('segTab');
      if ($(tab))  $(tab).style.display = "block";
      obj.className = "segActiveTab";
    }
  }
}

//added by VAN 03-03-08
function checkEnter(e,searchID){
    //alert('e = '+e);    
    var characterCode; //literal character code will be stored in this variable

    if(e && e.which){ //if which property of event object is supported (NN4)
        e = e;
        characterCode = e.which; //character code is contained in NN4's which property
    }else{
        characterCode = e.keyCode; //character code is contained in IE's keyCode property
    }

    if(characterCode == 13){ //if generated character code is equal to ascii 13 (if enter key)
        startAJAXSearch(searchID,0);
    }else{
        return true;
    }        
}




//number only and decimal point is allowed
function keyPressHandler(e, modtime){
    var unicode=e.charCode? e.charCode : e.keyCode
    //alert(unicode);
    //if (unicode>31 && (unicode<46 || unicode == 47 ||unicode>57)) //if not a number
    if (modtime){
        if (unicode>31 && (unicode<47 || unicode == 47 ||unicode>57)) //if not a number
            return false //disable key press
    }else{
        if (unicode>31 && (unicode<48 ||unicode>57)) //if not a number
            return false //disable key press
    }        
}
      

//---------------
 function prepareSelect(id, name) {
     //alert(id+' '+name);
     window.parent.$('donor_name').value = name;
     window.parent.$('donor_id').value = id;
     if (window.parent.pSearchClose) window.parent.pSearchClose();
        else if (window.parent.cClick) window.parent.cClick();
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

function selectDonorList(listID,donorID,name,address,age,reg_date,blood_type)
{
    var list=$(listID), dRows, dBody, rowSrc;
    var i;
    var classified, mode, editlink;
     //alert("hello");
    if (list) {
    //alert("hi");
        dBody=list.getElementsByTagName("tbody")[0];
        dRows=dBody.getElementsByTagName("tr");
        if (donorID) {
       // alert("donor id="+donorID);
            alt = (dRows.length%2)+1;
            
            rowSrc = '<tr><td width="10%" align="center"><span style="font:bold 11px Arial;color:#660000">'+donorID+'</span></td>'+ 
                            '<td width="15%" align="left">'+name+'</td>'+
                            '<td width="20%" align="left">'+address+'</td>'+
                            '<td width="5%" align="center">'+age+'</td>'+
                            '<td width="10%" align="center">'+reg_date+'</td>'+
                            '<td width="5%" align="center">'+blood_type+'</td>'+
                            '<td align="right">'+
                            '<input class="segButton" type="button" value="Select" style="color:#000066;" '+
                                                            'onclick="prepareSelect(\''+donorID+'\',\''+name+'\')"/>'+
                            '</td>'+ 
                            '<td>&nbsp;</td>'+ 
                      '</tr>';        
        } 
        else {
            rowSrc = '<tr><td colspan="10" style="">No such person exists...</td></tr>';
        }
        dBody.innerHTML += rowSrc;
       // alert("dBody="+dBody.innerHTML); 
    }
     
}
// -->
</script> 

<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/shortcut.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>


<?php
$xajax->printJavascript($root_path.'classes/xajax_0.5');
#$xajax->printJavascript($root_path.'classes/xajax');
$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

# Buffer page output

ob_start();

?>
  <ul id="cashier-tabs" class="segTab" style="padding-left:10px">
    <li class="segActiveTab" onclick="tabClick(this)" segTab="tab0">
      <h2 class="segTabText">Search</h2>
    </li>
    <li segTab="tab1" <?= $_GET['var_reg_walkin'] ? 'onclick="tabClick(this)"' : 'class="segDisabledTab"' ?>>
      <h2 class="segTabText">Register</h2>
    </li>
    &nbsp;
  </ul>
  <div class="segTabPanel" style="width:98%">
    <div id="tab0" class="tabFrame" style="width:98.5%;display:block; padding:0.5%" >
        <table width="100%" cellspacing="0" cellpadding="2" border="0">
            <tbody>
                <tr>
                    <td style="font:bold 12px Arial; color: #2d2d2d" >
                        <table width="95%" border="0" cellpadding="0" cellspacing="0" style="font:bold 12px Arial; color:#2d2d2d; margin:5px">
                            <tr>
                                <td width="15%">
                                    Search person<br />
                                    <a href="javascript:gethelp('person_search_tips.php')" style="text-decoration:underline">Tips & tricks</a>
                                </td>
                                <td valign="middle" width="40%">                                
                                    <input id="search" class="segInput" type="text" style="width:98%" align="absmiddle" onkeyup="updateControls(); if (event.keyCode == 13) startAJAXSearch(this.id,0)" onclick="updateControls()"/>
                                </td>
                  <td valign="middle" width="*">
                    <input class="segInput" id="search-btn" type="image" src="<?= $root_path ?>images/his_searchbtn.gif" onclick="startAJAXSearch('search',0);return false;" align="absmiddle" disabled="disabled" />
                  </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div style="display:block; border:1px solid #0d4688; height:300px; width:100%; background-color:#e5e5e5; overflow-x:hidden; overflow-y:scroll">
                            <table id="person-list" class="segList" cellpadding="0" cellspacing="0" width="100%">
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
                                        <th width="8%" nowrap="nowrap">Donor ID</th>
                                        <th width="15%">Name</th>
                                        <th width="20%">Address</th>
                                        <th width="8%">Age</th>
                                        <th width="10%">Member Date</th>
                                        <th width="8%">Blood Type</th>
                                        <th width="8%"></th>
                                        <th width="1%">&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody id="person-list-body">
                                    <tr>
                                        <td colspan="9">No such person exists...</td>
                                    </tr>
                                </tbody>
                            </table>
                            <img id="ajax-loading" src="<?= $root_path ?>images/loading6.gif" align="absmiddle" border="0" style="display:none"/>
                        </div>
                    </td>
                </tr>
            </tbody>
      </table>
    </div>
    
   

    <input type="hidden" name="sid" value="<?php echo $sid?>">
    <input type="hidden" name="lang" value="<?php echo $lang?>">
    <input type="hidden" name="cat" value="<?php echo $cat?>">
    <input type="hidden" name="userck" value="<?php echo $userck ?>">
    <input type="hidden" name="mode" value="search">

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
