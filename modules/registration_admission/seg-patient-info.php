<?php
#created by VAN 06-21-08
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path."modules/registration_admission/ajax/comp_search_icd.common.php");
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
$title="Patient Records::History";
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
 $smarty->assign('sOnLoadJs','onLoad="preSet();"');

 require_once($root_path.'include/care_api_classes/class_encounter.php');
 $enc_obj=new Encounter;
	
 require_once($root_path.'include/care_api_classes/class_personell.php');
 $pers_obj=new Personell;

 require_once($root_path.'include/care_api_classes/class_department.php');
 $dept_obj=new Department;
	
 require_once($root_path.'include/care_api_classes/class_person.php');
 $person_obj=new Person();
	
 require_once($root_path.'include/care_api_classes/class_ward.php');
 $ward_obj = new Ward;	

 $pid = $_GET['pid'];
 $encounter_nr = $_GET['encounter_nr'];
 
 #$person = $person_obj->getAllInfoArray($pid);
 $person = $enc_obj->getPatientEncInfo($encounter_nr);
# echo "sql = ".$person_obj->sql;
 extract($person);
 
 $name = $name_first." ".$name_2." ".$name_middle." ".$name_last;
 
 #age
    if (stristr($age,'years')){
        $age = substr($age,0,-5);
        if ($age>1)
            $labelyear = "years";
        else
            $labelyear = "year";
                
        $age = floor($age)." ".$labelyear;
    }elseif (stristr($age,'year')){    
        $age = substr($age,0,-4);
        if ($age>1)
            $labelyear = "years";
        else
            $labelyear = "year";
            
        $age = floor($age)." ".$labelyear;
        
    }elseif (stristr($age,'months')){    
        $age = substr($age,0,-6);
        if ($age>1)
            $labelmonth = "months";
        else
            $labelmonth = "month";
            
        $age = floor($age)." ".$labelmonth;    
        
    }elseif (stristr($age,'month')){    
        $age = substr($age,0,-5);
        
        if ($age>1)
            $labelmonth = "months";
        else
            $labelmonth = "month";
            
        $age = floor($age)." ".$labelmonth;        
        
    }elseif (stristr($age,'days')){    
        $age = substr($age,0,-4);
                    
        if ($age>30){
            $age = $age/30;
            if ($age>1)
                $label = "months";
            else
                $label = "month";
            
        }else{
            if ($age>1)
                $label = "days";
            else
                $label = "day";
        }
                        
        $age = floor($age).' '.$label;
      }elseif (stristr($age,'day')){    
        $age = substr($age,0,-3);
        
        if ($age>1)
            $labelday = "days";
        else
            $labelday = "day";
            
        $age = floor($age)." ".$labelday;        
    }else{
        if ($age){
            if ($age>1)
                $labelyear = "years";
            else
                $labelyear = "year";
            
            $age = $age." ".$labelyear;
        }else
            $age = "0 day";    
    }
    
    $p_age = $age." old";
    
    #sex
    if ($sex=='f')
        $gender = 'FEMALE';
    elseif ($sex=='m')
        $gender = 'MALE';  
 
    #address
    if ($street_name){
        if ($brgy_name!="NOT PROVIDED")
            $street_name = $street_name.", ";
        else
            $street_name = $street_name.", ";    
    }else
        $street_name = "";    
        
    if ((!($brgy_name)) || ($brgy_name=="NOT PROVIDED"))
        $brgy_name = "";
    else 
        $brgy_name  = $brgy_name.", ";    
                    
    if ((!($mun_name)) || ($mun_name=="NOT PROVIDED"))
        $mun_name = "";        
    else{    
        if ($brgy_name)
            $mun_name = $mun_name;    
        #else
            #$mun_name = $mun_name;        
    }            
    
    if ((!($prov_name)) || ($prov_name=="NOT PROVIDED"))
        $prov_name = "";        
    #else
    #    $prov_name = $prov_name;            
                
    if(stristr(trim($mun_name), 'city') === FALSE){
        if ((!empty($mun_name))&&(!empty($prov_name))){
            if ($prov_name!="NOT PROVIDED")    
                $prov_name = ", ".trim($prov_name);
            else
                $prov_name = "";    
        }else{
            #$province = trim($prov_name);
            $prov_name = "";
        }
    }else
        $prov_name = " ";    
                
    $address = $street_name.$brgy_name.$mun_name.$prov_name;
    
    if (($date_birth!='0000-00-00') || !($date_birth))
        $date_birth = date("m/d/Y",strtotime($date_birth));
    else    
        $date_birth = "Unspecified";
        
    if (($admission_date!='0000-00-00 00:00:00') || ($admission_date==''))
        $admission_date = date("m/d/Y h:i A",strtotime($admission_date));    
    else    
        $admission_date = "Not Admitted";    
        
    if (($encounter_date!='0000-00-00 00:00:00') || !($encounter_date))
        $encounter_date = date("m/d/Y h:i A",strtotime($encounter_date));    
    else    
        $encounter_date = "Unspecified";        
    
    if (($discharged_date!='0000-00-00') || !($discharged_date))
        $discharged_date = date("m/d/Y",strtotime($discharged_date));
    else{    
        if ($encounter_type==2)
            $discharged_date = date("m/d/Y",strtotime($encounter_date));
        else
            $discharged_date = "Still-in";            
    }    
    
    if (($death_date!='0000-00-00') || !($death_date))
        $death_date = date("m/d/Y",strtotime($death_date));    
    else    
        $death_date = "No death date";    
    
 # Collect javascript code
 ob_start()

?>
<script language="javascript" >

function preSet(){
    startAJAXSearch('search',0);
}

var currentPage=0, lastPage=0;
var FIRST_PAGE=1, PREV_PAGE=2, NEXT_PAGE=3, LAST_PAGE=4, SET_PAGE=0;
var AJAXTimerID=0;
var lastSearch="";

function startAJAXSearch(searchID, page) {
    var searchEL = $(searchID);
    var encounter;

    encounter_nr = document.getElementById('encounter_nr').value;

    if (searchEL) {
        searchEL.style.color = "#0000ff";
        if (AJAXTimerID) clearTimeout(AJAXTimerID);
        $("ajax-loading").style.display = "";
        $("DiagnosisList-body").style.display = "none";
        AJAXTimerID = setTimeout("xajax_populateDiagnosisList('"+encounter_nr+"','"+searchID+"',"+page+")",100);
        lastSearch = searchEL.value;
    }
}

function endAJAXSearch(searchID) {
    var searchEL = $(searchID);
    if (searchEL) {
        $("ajax-loading").style.display = "none";
        $("DiagnosisList-body").style.display = "";
        searchEL.style.color = "";
    }
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

function addslashes(str) {
    str=str.replace("'","\\'");
    return str;
}

function trimString(objct){
//    alert("inside frunction trimString: objct = '"+objct+"'");
    objct.value.replace(/^\s+|\s+$/g,"");
    objct.value = objct.value.replace(/\s+/g,"");
}


function addDiagnosisToList(listID, diagnosis_nr, code, description, doctor, bAddedByBilling, altdesc) {
    var list=$(listID), dRows, dBody, rowSrc;
    var i, stmp;
   
    if (list) {
        dBody=list.getElementsByTagName("tbody")[0];
        dRows=dBody.getElementsByTagName("tr");

        var rows = document.getElementsByName('rows[]');
        if (rows.length == 0) {
            clearList(list);
        }
        
        if (diagnosis_nr) {
            alt = (dRows.length%2)+1;
            
            rowSrc = '<tr class="wardlistrow'+alt+'" id="row'+addslashes(diagnosis_nr)+'">'+
                                '<input type="hidden" name="rows[]" id="index_'+addslashes(diagnosis_nr)+'" value="'+addslashes(diagnosis_nr)+'" />'+
                                '<td align="center">'+code+'</td>'+
                                '<td>'+description+'</td>'+
                                '<td>'+doctor+'</td>'+
                     '</tr>';
        }
        else {
            rowSrc = '<tr><td colspan="7">No diagnosis history available ...</td></tr>';
        }

        dBody.innerHTML += rowSrc;
    }
}

function reclassRows(list,startIndex) {
    if (list) {
        var dBody=list.getElementsByTagName("tbody")[0];
        if (dBody) {
            var dRows = dBody.getElementsByTagName("tr");
            if (dRows) {
                for (i=startIndex;i<dRows.length;i++) {
                    dRows[i].className = "wardlistrow"+(i%2+1);
                }
            }
        }
    }
}

function setPagination(pageno, lastpage, pagen, total) {
    currentPage=parseInt(pageno);
    lastPage=parseInt(lastpage);
    firstRec = (parseInt(pageno)*pagen)+1;

    if (currentPage==lastPage)
        lastRec = total;
    else
        lastRec = (parseInt(pageno)+1)*pagen;

    if (parseInt(total)==0)
        $("pageShow").innerHTML = '<span>Showing '+(lastRec)+'-'+(lastRec)+' out of '+(parseInt(total))+' record(s).</span>';
    else
        $("pageShow").innerHTML = '<span>Showing '+(firstRec)+'-'+(lastRec)+' out of '+(parseInt(total))+' record(s).</span>';

    $("pageFirst").className = (currentPage>0 && lastPage>0 && total>10) ? "segSimulatedLink" : "segDisabledLink";
    $("pagePrev").className = (currentPage>0 && lastPage>0 && total>10) ? "segSimulatedLink" : "segDisabledLink";
    $("pageNext").className = (currentPage<lastPage && total>10) ? "segSimulatedLink" : "segDisabledLink";
    $("pageLast").className = (currentPage<lastPage && total>10) ? "segSimulatedLink" : "segDisabledLink";
}

function jumpToPage(el, jumpType, set) {
    if (el.className=="segDisabledLink") return false;
    if (lastPage==0) return false;
    switch(jumpType) {
        case FIRST_PAGE:
            if (currentPage==0) return false;
            startAJAXSearch('search',0);
        break;
        case PREV_PAGE:
            if (currentPage==0) return false;
            startAJAXSearch('search',parseInt(currentPage)-1);
        break;
        case NEXT_PAGE:
            if (currentPage >= lastPage) return false;
            startAJAXSearch('search',parseInt(currentPage)+1);
        break;
        case LAST_PAGE:
            if (currentPage >= lastPage) return false;
            startAJAXSearch('search',parseInt(lastPage));
        break;
    }
}

// -->
</script>


<!---------added by VAN----------->
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

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<?php
$xajax->printJavascript($root_path.'classes/xajax');
$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

# Buffer page output

ob_start();

?>
<div style="display:block; border:1px solid #8cadc0; overflow-y:hidden;overflow-x:hidden; height:485px; width:99%; background-color:#e5e5e5">
	<table border="0" class="segPanel" cellspacing="2" cellpadding="2" width="100%" align="center">
		<tr>
            <td width="15%">Case Number</td>
            <td width="35%"><strong><font size="4"><?=$encounter_nr?></font></strong></td>
            <td>&nbsp;</td>
            <td width="15%">Hospital Number</td>
            <td width="35%"><strong><font size="4"><?=$pid?></font></strong></td>
        </tr>
        <tr>
			<td>Patient's Name</td>
			<td colspan="4"><strong><?=mb_strtoupper($name)?></strong></td>
		</tr>
        <tr>
            <td>Birth Date</td>
            <td><strong><?=$date_birth?></strong></td>
            <td>&nbsp;</td>
            <td>Age</td>
            <td><strong><?=$p_age?></strong></td>
        </tr>
        <tr>
            <td>Sex</td>
            <td><strong><?=$gender?></strong></td>
            <td>&nbsp;</td>
            <td>Civil Status</td>
            <td><strong><?=mb_strtoupper($civil_status)?></strong></td>
        </tr>
        <tr>
            <td>Occupation</td>
            <td><strong><?=mb_strtoupper($occupation)?></strong></td>
            <td>&nbsp;</td>
            <td>Religion</td>
            <td><strong><?=mb_strtoupper($religion)?></strong></td>
        </tr>
        <tr>
            <td>Address</td>
            <td colspan="4"><textarea style="width:100%; overflow-x:hidden;" wrap="physical" readonly="readonly" cols="53" rows="2" ><?=mb_strtoupper($address)?></textarea></td>
        </tr>
        <tr>
            <td>Patient Type</td>
            <td><strong><?=mb_strtoupper($patient_type)?></strong></td>
            <td>&nbsp;</td>
            <td>Location</td>
            <td><strong><?=$location?></strong></td>
        </tr>
        <tr>
            <td>Encounter Date</td>
            <td><strong><?=mb_strtoupper($encounter_date)?></strong></td>
            <td>&nbsp;</td>
            <td>Date Admitted</td>
            <td><strong><?=$admission_date?></strong></td>
        </tr>
        <tr>
            <td>Date Discharged</td>
            <td><strong><?=$discharged_date?></strong></td>
            <td>&nbsp;</td>
            <td>Death Date</td>
            <td><strong><?=$death_date?></strong></td>
        </tr>
        <tr>
            <td>Disposition</td>
            <td><strong><?=mb_strtoupper($disposition)?></strong></td>
            <td>&nbsp;</td>
            <td>Outcome</td>
            <td><strong><?=mb_strtoupper($outcome)?></strong></td>
        </tr>
        <tr>
            <td>Admitting Diagnosis</td>
            <td colspan="4"><textarea style="width:100%; overflow-x:hidden;" wrap="physical" readonly="readonly" cols="53" rows="2" ><?=$er_opd_diagnosis?></textarea></td>
        </tr>
        <tr>
            <td colspan="5">ICD 10 Code and Description :</td>
        </tr>
	</table>
    <div style="display:block; border:1px solid #8cadc0; overflow-y:hidden; overflow-x:hidden; width:100%; background-color:#e5e5e5">
    <table class="segList" width="100%" border="0" cellpadding="0" cellspacing="0">
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
                <input id="search" name="search" type="hidden" />
            </th>
        </tr>
        </thead>
    </table>
    </div>

    <div style="display:block; border:1px solid #8cadc0; overflow-y:scroll;overflow-x:hidden; height:150px; width:100%; background-color:#e5e5e5">
        <table id="DiagnosisList" class="segList" width="100%" border="0" cellpadding="0" cellspacing="0" style="overflow:auto">
            <thead>
                <tr>
                    <th width="10%" align="left">ICD 10</th>
                    <th width="60%" align="left">Diagnosis</th>
                    <th colspan="2" width="*" align="left">Clinician</th>
                </tr>
            </thead>
            <tbody id="DiagnosisList-body">
            </tbody>
        </table>
        <img id="ajax-loading" src="<?= $root_path ?>images/loading6.gif" align="absmiddle" border="0" style="display:none"/>
    </div>
	
    <input type="hidden" name="sid" value="<?php echo $sid?>">
	<input type="hidden" name="lang" value="<?php echo $lang?>">
	<input type="hidden" name="cat" value="<?php echo $cat?>">
	<input type="hidden" name="userck" value="<?php echo $userck ?>">
	<input type="hidden" name="mode" value="search">
	<input type="hidden" name="pid" id="pid" value="<?=$pid?>"/>
    <input type="hidden" name="encounter_nr" id="encounter_nr" value="<?=$encounter_nr?>"/>

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
