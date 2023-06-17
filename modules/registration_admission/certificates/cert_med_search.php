<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

#added by VAN 06-25-08
require($root_path."modules/registration_admission/ajax/clinics.common.php");

/**
* CARE2X Integrated Hospital Information System beta 2.0.1 - 2004-07-04
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org, 
*
* See the file "copy_notice.txt" for the licence notice
*/

# Default value for the maximum nr of rows per block displayed, define this to the value you wish
# In normal cases this value is derived from the db table "care_config_global" using the "pagin_insurance_list_max_block_rows" element.

define('MAX_BLOCK_ROWS',30); 


define('LANG_FILE','aufnahme.php');

$local_user='aufnahme_user';
require($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'include/inc_date_format_functions.php');

?>
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
<?php
$thisfile=basename(__FILE__);
$toggle=0;

if($HTTP_COOKIE_VARS['ck_login_logged'.$sid]) 
  $breakfile=$root_path.'main/startframe.php'.URL_APPEND;
else 
  $breakfile='aufnahme_pass.php'.URL_APPEND.'&target=entry';

# burn added: March 9, 2007
require_once($root_path.'include/care_api_classes/class_department.php');
$dept_obj=new Department;

if (!empty($HTTP_SESSION_VARS['sess_login_userid']))
    $seg_user_name = $HTTP_SESSION_VARS['sess_login_userid'];
else
    $seg_user_name = $HTTP_SESSION_VARS['sess_temp_userid'];

$user_dept_info = $dept_obj->getUserDeptInfo($seg_user_name);

if ($_GET['ptype'])
    $ptype = $_GET['ptype'];
elseif ($HTTP_SESSION_VARS['ptype'])
    $ptype = $HTTP_SESSION_VARS['ptype'];
    
$HTTP_SESSION_VARS['ptype'] = $ptype;    
    #echo "ptype = ".$ptype;    

if($mode=='paginate'){
    if (!empty($HTTP_SESSION_VARS['ptype']))
        $ptype = $HTTP_SESSION_VARS['ptype'];

}    

if (stristr($user_dept_info['job_function_title'], 'doctor'))
    $is_doctor = 1;
else    
    $is_doctor = 0;

#echo $allow_opd_user."<br>".$ptype;
#echo "doc = ".$is_doctor;
#if(($allow_medocs_user)||($is_doctor )){
if (($allow_opd_user)&&($ptype=='opd')){
    $encounter_type_search='2';   # search under OPD Triage 
#}elseif($user_dept_info['dept_nr']==149){
}elseif(($allow_er_user)&&($ptype=='er')){
    $encounter_type_search='1';   # search under ER Triage
#}elseif(($user_dept_info['dept_nr']==148)||($user_dept_info['dept_nr']==151)){
}elseif(($allow_ipd_user)&&($ptype=='ipd')){
    $encounter_type_search='3,4';   # search under Admitting Section or Medical Records
}elseif(($allow_medocs_user)||($allow_opd_user)||($allow_er_user)||($allow_ipd_user)||($is_doctor)){
    #$encounter_type_search=0;   # User has no permission to use Admission Search
    $encounter_type_search='1,2,3,4';   # User has no permission to use Admission Search
    
    //if ($user_dept_info['dept_nr'])
      //  $sql_ext = " AND enc.current_dept_nr='".$user_dept_info['dept_nr']."' ";
}

# Set value for the search mask
#$searchprompt=$LDEntryPrompt;   # transferred below

# Special case for direct access from patient listings
# If forward nr ok, use it as searchkey
if(isset($fwd_nr)&&$fwd_nr&&is_numeric($fwd_nr)){
    $searchkey=$fwd_nr;
    $mode='search';
}else{
    if(!isset($searchkey)) $searchkey='';
}

if(!isset($mode)) $mode='';

# Initialize page´s control variables
if($mode=='paginate'){
    $searchkey=$HTTP_SESSION_VARS['sess_searchkey'];
}else{
    # Reset paginator variables
    $pgx=0;
    $totalcount=0;
    $odir='';
    $oitem='';
}
#added by VAN 06-11-08
//if (empty($searchkey))
    //$searchkey = date("m/d/Y");

if (empty($mode))
    $mode = 'search';

#Load and create paginator object
require_once($root_path.'include/care_api_classes/class_paginator.php');
$pagen=new Paginator($pgx,$thisfile,$HTTP_SESSION_VARS['sess_searchkey'],$root_path);

if(isset($mode)&&($mode=='search'||$mode=='paginate')&&isset($searchkey)&&($searchkey)){
    
    include_once($root_path.'include/inc_date_format_functions.php');
    
    //$db->debug=true;

    if($mode!='paginate'){
        $HTTP_SESSION_VARS['sess_searchkey']=$searchkey;
    }    
        # convert * and ? to % and &
        $searchkey=strtr($searchkey,'*?','%_');
        
        $GLOBAL_CONFIG=array();

        include_once($root_path.'include/care_api_classes/class_globalconfig.php');
        $glob_obj=new GlobalConfig($GLOBAL_CONFIG);

        # Get the max nr of rows from global config
        $glob_obj->getConfig('pagin_patient_search_max_block_rows');
        if(empty($GLOBAL_CONFIG['pagin_patient_search_max_block_rows'])) $pagen->setMaxCount(MAX_BLOCK_ROWS); # Last resort, use the default defined at the start of this page
            else $pagen->setMaxCount($GLOBAL_CONFIG['pagin_patient_search_max_block_rows']);
        
        $searchkey=trim($searchkey);
        $suchwort=$searchkey;

        $suchwort = str_replace("T","",$suchwort);
        if(is_numeric($suchwort)) {
            $numeric=1;
            $sql2=" WHERE (care_person.pid='$searchkey')";
            
        } else {
            
            # Try to detect if searchkey is composite of first name + last name
            if(stristr($searchkey,',')){
                $lastnamefirst=TRUE;
            }else{
                $lastnamefirst=FALSE;
            }
            
            if(stristr($searchkey, ',') === FALSE){
                $cbuffer=explode(' ',$searchkey);
                $lnameOnly = 1;
            }else{
                $cbuffer=explode(',',$searchkey);    
                $newquery = "";
                $lnameOnly = 0;
            }

            # Remove empty variables
            for($x=0;$x<sizeof($cbuffer);$x++){
                $cbuffer[$x]=trim($cbuffer[$x]);
                if($cbuffer[$x]!='') $comp[]=$cbuffer[$x];
            }
            
            # Arrange the values, ln= lastname, fn=first name, bd = birthday
            if($lastnamefirst){
                $fn=$comp[1];
                $ln=$comp[0];
                $bd=$comp[2];
            }else{
                $fn=$comp[0];
                $ln=$comp[1];
                $bd=$comp[2];
            }
            
            # Check the size of the comp
            if(sizeof($comp)>1){
                $cntlast = sizeof($cbuffer)-1;
                if (sizeof($cbuffer) > 2){
                    if ($lnameOnly)
                        $sql2=" WHERE (name_last $sql_LIKE '".$searchkey."%')";
                    else
                        $sql2=" WHERE (((name_last $sql_LIKE '".strtr($ln,'+',' ')."%' OR name_last $sql_LIKE '".strtr($comp[$cntlast],'+',' ')."%') AND name_first $sql_LIKE '".strtr($fn,'+',' ')."%'))";
                    $bd=$comp[sizeof($cbuffer)];                
                                    
                }else{
                        if ($lnameOnly)
                            $sql2=" WHERE (name_last $sql_LIKE '".$searchkey."%')";
                        else
                            $sql2=" WHERE ((name_last $sql_LIKE '".strtr($ln,'+',' ')."%' AND name_first $sql_LIKE '".strtr($fn,'+',' ')."%'))";
                }                    
                if($bd){ 
                    $stddate=formatDate2STD($bd,$date_format);
                    if(!empty($stddate)){
                        #$sql2.=" AND (reg.date_birth = '$stddate' OR reg.date_birth $sql_LIKE '%$bd%')";
                        $sql2.=" AND (date_birth = '$stddate' OR date_birth $sql_LIKE '%$bd%')";
                    }
                }
            }else{
                if ($lnameOnly){
                    $sql2=" WHERE (name_last $sql_LIKE '".$searchkey."%'";
                }
                else{
                    $sql2=" WHERE (name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%'";}
                    
                $bufdate=formatDate2STD($suchwort,$date_format);
                if(!empty($bufdate)){
                    $sql2.= " OR date_birth = '$bufdate'";
                }
                $sql2.=")";
            }
        }

            #edited by VAN 05-13-08
            $sql2.=" AND death_date='0000-00-00'
                        AND care_encounter.encounter_type IN ($encounter_type_search) 
                        GROUP BY care_person.pid
                        ORDER BY name_last ASC";
            $dbtable=" FROM care_person
                          LEFT JOIN care_encounter ON care_encounter.pid = care_person.pid ";
            $sql=" SELECT care_person.pid as HRN,".
                    "CONCAT(IF(ISNULL(name_first),'',CONCAT(name_first, ' ')), IF(ISNULL(name_2),'',CONCAT(name_2, ' ')), IF(ISNULL(name_3),'',name_3)) as name_first, ".
                    "name_middle, name_last, sex, DATE_FORMAT(FROM_DAYS(TO_DAYS(NOW())-TO_DAYS(date_birth)), '%Y')+0 AS age, date_birth, encounter_type ".
                    $dbtable.$sql2;
            //echo $sql;  
            if($ergebnis=$db->SelectLimit($sql,$pagen->MaxCount(),$pagen->BlockStartIndex()))
               {
                if ($linecount=$ergebnis->RecordCount()) 
                {
                    if(($linecount==1)&&$numeric&&$mode=='search')
                    {
                        $zeile=$ergebnis->FetchRow();
                        header('Location:aufnahme_daten_zeigen.php'.URL_REDIRECT_APPEND.'&from=such&encounter_nr='.$zeile['encounter_nr'].'&target=search&ptype='.$ptype);
                        exit;
                    }
                    
                    $pagen->setTotalBlockCount($linecount);
                    
                    # If more than one count all available
                    if(isset($totalcount)&&$totalcount){
                        $pagen->setTotalDataCount($totalcount);
                    }else{
                        # Count total available data
                        if($dbtype=='mysql'){
                            $sql='SELECT COUNT(care_person.pid) AS "count" '.$dbtable.$sql2;
                        }else{
                            $sql='SELECT * '.$dbtable.$sql2;
                        }
                        //echo $sql;

                        if($result=$db->Execute($sql)){
                            if ($totalcount=$result->RecordCount()) {
                                if($dbtype=='mysql'){
                                    $rescount=$result->FetchRow();
                                        $totalcount=$result->RecordCount();
                                }
                                }
                        }
                        $pagen->setTotalDataCount($totalcount);
                    }
                    # Set the sort parameters
                    $pagen->setSortItem($oitem);
                    $pagen->setSortDirection($odir);
                }
                
            }
             else {echo "<p>".$sql."<p>$LDDbNoRead";};
}

# Start Smarty templating here
 /**
 * LOAD Smarty
 */
 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

    if (empty($user_dept_info['name_formal']))
        $smarty->assign('sToolbarTitle',"Medical Certificate :: $LDSearch");   # burn added : May 15, 2007
    else
        $smarty->assign('sToolbarTitle',"Medical Certificate :: $LDSearch (".strtoupper($user_dept_info['name_formal']).")");   # burn added : May 15, 2007

 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('title',$LDPatientSearch);

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('admission_how2search.php','$from')");

  # Onload Javascript code
 $smarty->assign('sOnLoadJs','onLoad="if(window.focus) window.focus();document.searchform.searchkey.select();"');

 # Hide the return button
 $smarty->assign('pbBack',FALSE);
 
 #added by VAN 06-25-08
 ob_start();
 $xajax->printJavascript($root_path.'classes/xajax');
 $sTemp = ob_get_contents();
 ob_end_clean();

 $smarty->append('JavaScript',$sTemp);
 #----------------

#
# Load the tabs
#
$target='search';
$parent_admit = TRUE;
include('./gui_bridge/default/gui_tabs_patadmit.php');

#
# Prepare the javascript validator
#
if(!isset($searchform_count) || !$searchform_count){
    $smarty->assign('sJSFormCheck','<script language="javascript">
    <!--
        function chkSearch(d){
            /*
            if((d.searchkey.value=="") || (d.searchkey.value==" ")){
                d.searchkey.focus();
                return false;
            }else    {
                return true;
            }
            */
            return true;
        }
        //added by VAN 06-25-08
        function ToBeServed(objID, encounter_nr, dept){
            var is_served;
            if (document.getElementById(objID).checked==true)
                is_served = 1;
            else
                is_served = 0;
            
            xajax_savedServedPatient(encounter_nr, is_served, dept);
        }
        
        function refreshWindow(){
            window.location.href=window.location.href;
        }
        
        function UpdateQuery(objVal){
            var isServeDcond;
            xajax_populatePatientList();
            document.getElementById("isServed").value = objVal;    
        }
        
        function onsubmitForm(){
            searchform.submit();
        }
        
        function MedCertHistory(pid){
        return overlib(
          OLiframeContent("med_cert_history.php?pid="+pid, 850, 460, "fOrderTray", 1, "auto"),
                                  WIDTH,440, TEXTPADDING,0, BORDER,0, 
                                    STICKY, SCROLL, CLOSECLICK, MODAL, 
                                    CLOSETEXT, "<img src='.$root_path.'/images/close.gif border=0 >",
                                 CAPTIONPADDING,4, CAPTION,"MEDICAL CERTIFICATE HISTORY",
                                 MIDX,0, MIDY,0, 
                                 STATUS,"MEDICAL CERTIFICATE HISTORY");
    }
        
        //---------------------
    // -->
    </script>');
}

#
# Prepare the form params
#
# Set value for the search mask
#$searchprompt=$LDEntryPrompt;   # transferred from above; burn commented : May 18, 2007
#$searchprompt="Enter the search keyword. For example: encounter number, or lastname, or firstname, or date of birth, etc.";   # burn added : May 18, 2007
$searchprompt=$LDSearchPromptCons; #added by pet, april 18, 2008, in replacement of the above text

$sTemp = 'method="post" name="searchform';
if($searchform_count) $sTemp = $sTemp."_".$searchform_count;
$sTemp = $sTemp.'" onSubmit="return chkSearch(this)"';
if(isset($search_script) && $search_script!='') $sTemp = $sTemp.' action="'.$search_script.'"';
$smarty->assign('sFormParams',$sTemp);
$smarty->assign('searchprompt',$searchprompt);

#added by VAN 06-25-08
#if (($user_dept_info['dept_nr']!=148)&&($user_dept_info['dept_nr']!=149)&&($user_dept_info['dept_nr']!=150)&&($user_dept_info['dept_nr']!=151)){
if ((!$allow_ipd_user)&&(!$allow_er_user)&&(!$allow_opd_user)&&(!$allow_medocs_user)){
    
  if ($is_doctor){
    $smarty->assign('sClinics',false);        
  }else{
    if (!($isServed))
        $isServed = 3;
        
    $smarty->assign('sClinics',true);    
            
    $smarty->assign('sCheckAll','<input type="radio" name="served" id="served" value="1" '.(($isServed==1)?'checked="checked" ':'').' onClick="UpdateQuery(this.value);" >');
    $smarty->assign('LDCheckAll',"All");
            
    $smarty->assign('sCheckYes','<input type="radio" name="served" id="served" value="2" '.(($isServed==2)?'checked="checked" ':'').' onClick="UpdateQuery(this.value);" >');
    $smarty->assign('LDCheckYes',"Served");
            
    $smarty->assign('sCheckNo','<input type="radio" name="served" id="served" value="3" '.(($isServed==3)?'checked="checked" ':'').' onClick="UpdateQuery(this.value);" >');
    $smarty->assign('LDCheckNo',"Not Yet Served");
  }    
}else{
    $smarty->assign('sClinics',false);    
}    
#--------------------------

#
# Prepare the hidden inputs
#
$smarty->assign('sHiddenInputs','<input type="image" '.createLDImgSrc($root_path,'searchlamp.gif','0','absmiddle').'>
        <input type="hidden" name="sid" value="'.$sid.'">
        <input type="hidden" name="lang" value="'.$lang.'">
        <input type="hidden" name="noresize" value="'.$noresize.'">
        <input type="hidden" name="target" value="'.$target.'">
        <input type="hidden" name="user_origin" value="'.$user_origin.'">
        <input type="hidden" name="origin" value="'.$origin.'">
        <input type="hidden" name="retpath" value="'.$retpath.'">
        <input type="hidden" name="aux1" value="'.$aux1.'">
        <input type="hidden" name="ipath" value="'.$ipath.'">
        <input type="hidden" name="isServed" id="isServed" value="'.(($isServed)?$isServed:3).'">
        <input type="hidden" name="mode" value="search">');
#commented by VAN 04-17-08
#$smarty->assign('sCancelButton','<a href="patient.php'.URL_APPEND.'&target=search"><img '.createLDImgSrc($root_path,'cancel.gif','0').'></a>');
$smarty->assign('sAllButton','<img '.createLDImgSrc($root_path,'all.gif','0','absmiddle').' style="cursor:pointer" onClick="document.getElementById(\'searchkey\').value=\'*\'; searchform.submit();">');

if($mode=='search'||$mode=='paginate'){
    
    if ($linecount) $smarty->assign('LDSearchFound',str_replace("~no.~",$totalcount,$LDSearchFound).' '.$LDShowing.' '.$pagen->BlockStartNr().' '.$LDTo.' '.$pagen->BlockEndNr().'.');
        else $smarty->assign('LDSearchFound',str_replace('~no.~','0',$LDSearchFound));

    if ($linecount) {

        $smarty->assign('bShowResult',TRUE);

        # Load the common icons and images
        $img_options=createComIcon($root_path,'pdata.gif','0');
        $img_male=createComIcon($root_path,'spm.gif','0');
        $img_female=createComIcon($root_path,'spf.gif','0');

        $smarty->assign('LDCaseNr',$pagen->makeSortLink("HRN",'HRN',$oitem,$odir,$targetappend));
        $smarty->assign('LDLastName',$pagen->makeSortLink($LDLastName,'name_last',$oitem,$odir,$targetappend));
        $smarty->assign('LDFirstName',$pagen->makeSortLink($LDFirstName,'name_first',$oitem,$odir,$targetappend));
        $smarty->assign('LDMiddleName',$pagen->makeSortLink("Middle Name",'name_middle',$oitem,$odir,$targetappend));
        $smarty->assign('LDSex',$pagen->makeSortLink("Sex",'sex',$oitem,$odir,$targetappend));
        $smarty->assign('LDBday',$pagen->makeSortLink("Birth Date",'date_birth',$oitem,$odir,$targetappend));
        $smarty->assign('LDAge',$pagen->makeSortLink("Age",'age',$oitem,$odir,$targetappend));
        $smarty->assign('LDOptions',"Details");
        
        $sTemp = '';
        while($zeile=$ergebnis->FetchRow()){

            $full_en=$zeile['HRN'];
            
            $smarty->assign('toggle',$toggle);
            $toggle = !$toggle;

            $smarty->assign('sCaseNr',$full_en);
            if($zeile['encounter_type']==1){
                $smarty->assign('sOutpatientIcon','<img '.createComIcon($root_path,'flag_red.gif').'>');
                $smarty->assign('LDAmbulant','<font size=1 color="red">ER</font>');
            }elseif($zeile['encounter_type']==2){
                $smarty->assign('sOutpatientIcon','<img '.createComIcon($root_path,'flag_blue.gif').'>');
                #$smarty->assign('LDAmbulant','<font size=1 color="blue">Outpatient</font>');
                $smarty->assign('LDAmbulant','<font size=1 color="blue">OPD</font>');
            }else{
                $smarty->assign('sOutpatientIcon','<img '.createComIcon($root_path,'flag_green.gif').'>');
                #$smarty->assign('LDAmbulant','<font size=1 color="green">Inpatient</font>');
                $smarty->assign('LDAmbulant','<font size=1 color="green">IPD</font>');
            }

            switch(strtolower($zeile['sex'])){
                case 'f': $smarty->assign('sSex','<img '.$img_female.'>'); break;
                case 'm': $smarty->assign('sSex','<img '.$img_male.'>'); break;
                default: $smarty->assign('sSex','&nbsp;'); break;
            }
            $smarty->assign('sAge',ucfirst($zeile['age']));
            $smarty->assign('sLastName',ucfirst($zeile['name_last']));
            $smarty->assign('sFirstName',ucfirst($zeile['name_first']));
            
            $smarty->assign('sMiddleName',ucfirst($zeile['name_middle']));

            $date_birth = @formatDate2Local($zeile['date_birth'],$date_format);            
            $bdateMonth = substr($date_birth,0,2);
            $bdateDay = substr($date_birth,3,2);
            $bdateYear = substr($date_birth,6,4);
            if (!checkdate($bdateMonth, $bdateDay, $bdateYear)){
                $date_birth='';
            }

            $smarty->assign('sBday',$date_birth);
            $sTarget = "<a href='javascript:void(0);' onclick='MedCertHistory(\"".$full_en."\");' onmouseout='nd();'>";
            $sTarget=$sTarget.'<img height="14" border="0" width="69" title="Show details" src="../../gui/img/control/default/en/en_ok_small.gif"/></a>';
            $smarty->assign('sOptions',$sTarget);

            if(!file_exists($root_path.'cache/barcodes/en_'.$full_en.'.png')){
                $smarty->assign('sHiddenBarcode',"<img src='".$root_path."classes/barcode/image.php?code=".$full_en."&style=68&type=I25&width=180&height=50&xres=2&font=5&label=2' border=0 width=0 height=0>");
            }
            #
            # Generate the row in buffer and append as string
            #
            ob_start();
                $smarty->display('registration_admission/med_cert_search_list_row.tpl');
                $sTemp = $sTemp.ob_get_contents();
            ob_end_clean();
        }

        #
        # Assign the rows string to template
        #
        $smarty->assign('sResultListRows',$sTemp);

        $smarty->assign('sPreviousPage',$pagen->makePrevLink($LDPrevious));
        $smarty->assign('sNextPage',$pagen->makeNextLink($LDNext));
    }
}
$smarty->assign('sPostText','<a href="aufnahme_list.php'.URL_APPEND.'">'.$LDAdmWantArchive.'</a>');

# Stop buffering, assign contents and display template

$smarty->assign('sMainIncludeFile','registration_admission/med_search_main.tpl');

$smarty->assign('sMainBlockIncludeFile','registration_admission/med_cert_search_main.tpl');

$smarty->display('common/mainframe.tpl');               

?>
