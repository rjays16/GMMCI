<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path."modules/registration_admission/ajax/order-psearch.common.php");
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
        'var_age'=>''
        #end omick
    );
    
    /*if ($_GET['var_include_enc']=='1') {
        $_GET['var_include_walkin']='0';
        $_REQUEST['var_include_walkin']='0';
        $_GET['var_reg_walkin']='0';
        $_REQUEST['var_reg_walkin']='0';
    } */
    
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
        $("ajax-loading").style.display = "";
        $("person-list-body").style.display = "none";
        searchText = searchText.replace("'","\\'");
        AJAXTimerID = setTimeout("xajax_populatePersonList('"+searchID+"','"+searchText+"',"+page+","+searchLastname+","+includeEnc+","+includeWalkin+")",100);
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

function validate(r) {
  /*
  if (!r.lastname) {    
    alert('Lastname required ...');
    $('lastname').select();
    $('lastname').focus();
    return false;
  }
  if (!r.firstname) {
    alert('Firstname required ...');
    $('lastname').select();
    $('lastname').focus();    
    return false;
  }
  if (!r.middlename) {
    alert('Middlename required ...');
    $('middlename').select();
    $('middlename').focus();
    return false;
  }
  */
  if (r.dob) {
   // if (!r.dob.match(/^\d{4}\-\d{2}\-\d{2}$/)) {
    if (!r.dob.match(/^\d{2}\/\d{2}\/\d{4}$/)) {
      alert('Invalid date of birth found ...');
      $('date_birth').select();
      $('date_birth').focus();
      return false;
    }
  }
  if (!r.address) {
    alert('Address required ...');
    $('lastname').select();
    $('lastname').focus();
    return false;
  }
  return true;
}

function prepareRegister() {
  reg = {
    pid : "",
    lastname : $('lastname').value,
    firstname : $('firstname').value,
    middlename : $('middlename').value,
    dob : $('date_birth').value,
        sex : $('sex').value,
        age : $('age').value,
    address : $('address').value
  };
  
  if (validate(reg)) {
    //$('btn-reg').disabled = true;
    xajax.call("checkWalkin",{parameters:[reg]});
  }
}

function resolveWalkinEntry(entry) {
  pre = entry;
  if (confirm('A walk-in entry with the same Lastname, Firstname and Middlename already exists in the records.\nDo you wish to register and select this entry anyway?')) {
    xajax.call("registerWalkin",{parameters:[reg]});
  }
  else {
    //selectWalkin(pre);
  }
}

function selectWalkin(reg) {
  var nr = '';
  var discountid = '';
  var orig_discountid = '';
  var discount = '';  
  var id = reg.pid;
  var rid = '';
  var lname = reg.lastname.toUpperCase();
  var fname = reg.firstname.toUpperCase();
  var mname = reg.middlename.toUpperCase();
  var addr = reg.address.toUpperCase();
  
  var sex = reg.sex;
  var age = reg.age;
  var dob = reg.dob; 
   
  var type = '0';
  
  //var enctype = 'WPx';
  var enctype = 'WALK-IN';
  //var location = 'WALK-IN';  
  var location = 'None';  
  var is_medico = '';
  var senior_citizen = '';
  
  if (var_pid) 
    //window.parent.$(var_pid).value = 'W'+id;
    window.parent.$(var_pid).value = id;
  if (var_rid) 
    window.parent.$(var_rid).value = rid;
  if (var_encounter_nr)
    window.parent.$(var_encounter_nr).value = nr;
 
  if (var_name) {
    if (mname)
      mname = mname.substring(0,1)+".";    
    window.parent.$(var_name).value = lname+", "+fname+ " " + mname;
    window.parent.$(var_name).readOnly = true;
  }
  
  if (var_addr) {
    window.parent.$(var_addr).value = addr;
    window.parent.$(var_addr).readOnly = true;
  }
  if (var_clear)
    window.parent.$(var_clear).disabled=false;  
   
   var showPatientType = window.parent.$('patient_enctype');
    //alert(enctype);
    if (showPatientType) {
        if (enctype){
            showPatientType.innerHTML = enctype;
        }else
            showPatientType.innerHTML = "None";
    }
     
   var showPatientLoc = window.parent.$('patient_location');
    if (showPatientLoc) {
        if (location)
            showPatientLoc.innerHTML = location;
        else
            showPatientLoc.innerHTML = "None";
    }
    
    var showPatientMedico = window.parent.$('patient_medico_legal');
    if (showPatientMedico) {
        if (is_medico==1)
            showPatientMedico.innerHTML = "YES";
        else if (is_medico==0)
            showPatientMedico.innerHTML = "NO";
    }
    
    var showAge = window.parent.$('age');
    if (showAge) {
        if (age)
            showAge.innerHTML = age;
        else
            showAge.innerHTML = "unknown";
    }
    
    var showSex = window.parent.$('sex');
    var gender;
    if (showSex) {
        if (sex=='m')
            gender = 'Male';
        else if (sex=='f')    
            gender = 'Female';
    
        if (sex)
            showSex.innerHTML = gender;
        else
            showSex.innerHTML = "unknown";
    }
    
    var showHRN = window.parent.$('hrn');
    if (showHRN) {
        showHRN.innerHTML = id;
    }
    
    var showWalkin = window.parent.$('iswalkin');
    if (showWalkin) {
        showWalkin.value = 1;
    }
    
    var showDob = window.parent.$('dob');
    if (showDob) {
        if (dob)
            showDob.innerHTML = dob;
        else
            showDob.innerHTML = "unknown";
    }
  
  if (nr) {
    if (window.parent.pSearchClose) window.parent.pSearchClose();
    else if (window.parent.cClick) window.parent.cClick();
  }
  else {
    if (window.parent.cClick) window.parent.cClick();
  }
 
  
}

//added by VAN 02-27-09
/*
function keyPressHandler(evt){
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57)){
        return false;
    }
    return true;
 }
*/
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
      
function getAge(){
    
     var dob;
     var dateNow = new Date();
     var valid;
     var obj = document.getElementById('date_birth');
    
     try{
        
         valid = IsValidDate(obj,'MM/dd/yyyy');
         
         dob = obj.value;
           
         var dobMonth = dob.substring(0,2);
         var dobDay = dob.substring(3,5);
         var dobYear = dob.substring(6,10);
         var pastDate = new Date(2000,dobMonth-1,dobDay);
         var presentDate = new Date(2000,dateNow.getMonth(),dateNow.getDate());
         var age = dateNow.getFullYear() - parseInt(dobYear);
         var ageYear = (presentDate.getTime()-pastDate.getTime())/31536000000;///86400000;
         age = age + ageYear;
         age = Math.floor(age); 
         if ((isNaN(age)) ||(dobMonth==0)||(dobDay==0)||(dobYear==0)){
            document.getElementById('age').value='';
         }else{
            document.getElementById('age').value=age;
            document.getElementById('date_birth').focus();
         }
     }catch(e){
         document.getElementById('age').value='';                
     }
   
}
//---------------

// -->
</script> 
<script type="text/javascript" src="<?=$root_path?>modules/registration_admission/js/person-search-gui.js?t=<?=time()?>"></script>
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
    <li segTab="tab1">
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
                                        <th width="8%">HRN</th>
                                        <th width="4%"></th>
                                        <th width="18%">Lastname</th>
                                        <th width="18%">Firstname</th>
                                        <th width="18%">Middlename</th>
                                        <th width="10%" style="font-size:11px" nowrap="nowrap">Date of Birth</th>
                                        <th width="10%" nowrap="nowrap">Patient type</th>
                                        <th width="10%">Class</th>
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
    
    <div id="tab1" class="tabFrame" style="display:none;padding:5px" >
      <h1>Register new walk-in</h1>
      <table width="100%" cellpadding="2" cellspacing="0" border="0" style="font-family:Arial, Helvetica, sans-serif">
        <tr valign="valign">
          <td width="100" nowrap="nowrap" align="right"><strong>Lastname</strong></td>
          <td align="left" valign="middle" nowrap="nowrap" width="*">
             <input class="segInput" id="lastname" name="lastname" type="text" size="35">
          </td>
        </tr>
        <tr valign="top">
          <td nowrap="nowrap" align="right"><strong>Firstname</strong></td>
          <td align="left" valign="middle" nowrap="nowrap">
            <input class="segInput" id="firstname" name="firstname" type="text" size="35">
          </td>
        </tr>
        <tr valign="top">
          <td nowrap="nowrap" align="right"><strong>Middlename</strong></td>
          <td align="left" valign="middle" nowrap="nowrap">
            <input class="segInput" id="middlename" name="middlename" type="text" size="35">
          </td>
        </tr>
        <tr valign="top">
          <td nowrap="nowrap" align="right"><strong>Sex</strong></td>
          <td align="left" valign="middle" nowrap="nowrap">
            <select class="segInput" name="sex" id="sex">
                            <option value=""></option>
                            <option value="m">M</option>
                            <option value="f">F</option>
                        </select>
          </td>
        </tr>
        <tr valign="top">
          <td nowrap="nowrap" align="right"><strong>Birthdate</strong></td>
          <td align="left" valign="middle" nowrap="nowrap">
            <input class="segInput" name="date_birth" id="date_birth" type="text" size="10" maxlength=10 value="" onKeyPress="return keyPressHandler(event,1);" onblur="getAge(); IsValidDate(this,'MM/dd/yyyy');" onKeyUp="setDate(this,'MM/dd/yyyy',<?=$lang?>)" onfocus="this.select();"> 
            <img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" border=0 align="absmiddle" width="26" height="22" id="date_birth_trigger" style="cursor:pointer ">
            <span style="font:normal 10px Arial">[mm/dd/YYYY]</span>
            <script type="text/javascript">
              Calendar.setup ({
                inputField : "date_birth", ifFormat : "%m/%d/%Y", showsTime : false, button : "date_birth_trigger", singleClick : true, onClose: function(cal) { cal.hide(); getAge();}, step : 1
              });
            </script>
          </td>
        </tr>
        <tr valign="top">
          <td nowrap="nowrap" align="right"><strong>Age</strong></td>
          <td align="left" valign="middle" nowrap="nowrap">
          <!--  onkeypress="return isNumberKey(event);"-->
            <input class="segInput" name="age" id="age" type="text" size="2" maxlength=3 style="text-align:right" onKeyPress="return keyPressHandler(event,0);"> 
          </td>
        </tr>
        <tr valign="top">
          <td nowrap="nowrap" align="right"><strong>Address</strong></td>
          <td align="left" valign="middle" nowrap="nowrap">
            <textarea class="segInput" id="address" name="address" cols="45" rows="3"></textarea>
          </td>
        </tr>
        <tr valign="top">
          <td></td>
          <td align="left" valign="middle" nowrap="nowrap">
            <input id="btn-reg" type="button" class="segButton" value="Register" onclick="prepareRegister()" />
            <input id="btn-reset" type="reset" class="segButton" value="Reset" />
          </td>
        </tr>
      </table>      
    </div>
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
