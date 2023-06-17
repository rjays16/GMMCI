<?php

#created by Cherry 11-11-09
include("roots.php");
include_once($root_path."/classes/fpdf/fpdf.php");
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path.'include/inc_date_format_functions.php');

#added by VAN 02-18-08
define('NO_2LEVEL_CHK',1);
require($root_path.'include/inc_front_chain_lang.php');

require_once($root_path.'/include/care_api_classes/class_drg.php');
$objDRG= new DRG;

include_once($root_path.'include/care_api_classes/class_encounter.php');
$enc_obj=new Encounter;

require_once($root_path.'include/care_api_classes/class_personell.php');
require_once($root_path.'include/care_api_classes/class_department.php');

$dept_obj=new Department;
$pers_obj=new Personell;

if (isset($_GET['encounter_nr']) && $_GET['encounter_nr']){
    $encounter_nr = $_GET['encounter_nr'];
}
if (isset($_POST['encounter_nr']) && $_POST['encounter_nr']){
    $encounter_nr = $_POST['encounter_nr'];
}

include_once($root_path.'include/care_api_classes/class_cert_med.php');
$obj_medCert = new MedCertificate($encounter_nr);


$errorMsg='';


#----------------------------------------------
$HTTP_POST_VARS['dr_nr'] = $_POST['doctors'];      

if (isset($_POST['mode'])){
    switch($_POST['mode']) {
        case 'save':
            $HTTP_POST_VARS['history'] = "Create ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']." \n";
            $HTTP_POST_VARS['create_id']=$HTTP_SESSION_VARS['sess_user_name'];
            $HTTP_POST_VARS['create_dt']=date('Y-m-d H:i:s');
            
            if ($obj_medCert->saveDriverCertificateInfoFromArray($HTTP_POST_VARS)){
               
                                    
                $cases = array();
             
                $errorMsg='<font style="color:#FF0000;font-style:italic">'."Saved sucessfully!".'</font>';
            }else{
                $errorMsg='<font style="color:#FF0000">'.$obj_medCert->getErrorMsg().'</font>';            
            }
        break;
        case 'update':
            $HTTP_POST_VARS['history'] = "Update ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']." \n";
            $HTTP_POST_VARS['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];
            $HTTP_POST_VARS['modify_dt']=date('Y-m-d H:i:s');
            #print_r($HTTP_POST_VARS);
            if ($obj_medCert->updateDriverCertificateInfoFromArray($HTTP_POST_VARS)){
             
                $cases = array();
               
                $errorMsg='<font style="color:#FF0000;font-style:italic">'."Updated sucessfully!".'</font>';
            }else{
                $errorMsg='<font style="color:#FF0000">'.$obj_medCert->getErrorMsg().'</font>';            
            }
            #echo "sql = ".$obj_medCert->sql;
        break;
    }# end of switch statement
}


if($encounter_nr){
#    if(!($encInfo = $enc_obj->getEncounterInfo($_GET['encounter_nr']))){
    if(!($encInfo=$enc_obj->getEncounterInfo($encounter_nr))){
        echo '<em class="warn"> Sorry but the page cannot be displayed!</em>';
        exit();
    }
    #extract($encInfo);
}else{
    echo '<em class="warn">Sorry but the page cannot be displayed! <br> Invalid Case Number!</em>';
    exit();
}

//$confCertInfo = $obj_medCert->getConfCertRecord($encounter_nr);
$driverCertInfo = $obj_medCert->getDriverCertRecord($encounter_nr);
//$medico_cases = $enc_obj->getMedicoCases();

//print_r($encInfo);

#echo "encInfo['encounter_type'] = '".$encInfo['encounter_type']."' <br> \n";
$listDoctors=array();

#echo "encInfo['current_dept_nr'] = '".$encInfo['current_dept_nr']."' <br> \n";
#added by VAN 06-28-08
  if ($encInfo['current_dept_nr'])    
    $dept_nr = $encInfo['current_dept_nr'];
  else    
    $dept_nr = $encInfo['consulting_dept_nr'];

    
       $doctors = $pers_obj->getDoctors(1);     

  if (is_object($doctors)){    
    while($drInfo=$doctors->FetchRow()){
    
        $middleInitial = "";
        if (trim($drInfo['name_middle'])!=""){
            $thisMI=split(" ",$drInfo['name_middle']);    
            foreach($thisMI as $value){
                if (!trim($value)=="")
                    $middleInitial .= $value[0];
            }
            if (trim($middleInitial)!="")
                $middleInitial .= ". ";
        }
            
        #$name_doctor = trim($drInfo["name_first"])." ".trim($drInfo["name_2"])." ".$middleInitial.trim($drInfo["name_last"]);
        #$name_doctor = "Dr. ".$name_doctor;
        $name_doctor = trim($drInfo["name_last"]).", ".trim($drInfo["name_first"])." ".$middleInitial; #substr(trim($drInfo["name_middle"]),0,1).$dot;
        $name_doctor = ucwords(strtolower($name_doctor)).", MD";
        
        #echo "<br> dr = ".$name_doctor;
        #$listDoctors['doctor_name']=$name_doctor;
        
        $listDoctors[$drInfo["personell_nr"]]=$name_doctor;
        #$listDoctors['doctor_nr']=$drInfo["personell_nr"];
            
        #print_r($listDoctors);
    }    
 }    
    
#----------------------

?>
<html>
<head>
<style type="text/css">
<!--
body {
    margin-left: 0px;
    margin-top: 0px;
    margin-right: 0px;
    margin-bottom: 0px;
    background-color: #F8F9FA;
}
.style2 {
    font-family: Geneva, Arial, Helvetica, sans-serif;
    font-size: 12px;
    font-weight: bold;
}

.style3 {
    font-family: Geneva, Arial, Helvetica, sans-serif;
    font-size: 12px;
    font-weight: normal;
}

-->
</style>
<?php
echo '<script type="text/javascript" language="javascript" src="'.$root_path.'js/jsprototype/prototype.js"></script>';

#added by VAN 06-13-08
#require($root_path.'include/inc_checkdate_lang.php');
echo '<link rel="stylesheet" type="text/css" media="all" href="' .$root_path.'js/jscalendar/calendar-win2k-cold-1.css">'."\n";
echo '<script language="javascript" src="'.$root_path.'js/setdatetime.js"></script>'."\n";
echo '<script language="javascript" src="'.$root_path.'js/checkdate.js"></script>'."\n";
/*echo '<script language="javascript" src="'.$root_path.'js/dtpick_care2x.js"></script>'."\n";*/
echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar.js"></script>'."\n";
echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/lang/calendar-en.js"></script>'."\n";
echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar-setup_3.js"></script>'."\n";
?>

<script language="javascript">
<?php
    require_once($root_path.'include/inc_checkdate_lang.php'); 
?>
</script>

<script language="javascript">
    String.prototype.trim = function() { return this.replace(/^\s+|\s+$/, ''); };

    function chkForm(){
       
        if ($F('purpose')==''){
            alert(" Please enter the purpose of requesting the certificate.");
            $('purpose').focus();
            return false;
        }
        
        return true;
    }

    
    <?php if (isset($_POST['comment_type'])) echo "'".$_POST['comment_type']."'"; else echo "'NML'"; ?>;    
    var type=<?php if (isset($_POST['comment_type'])) echo "'".$_POST['comment_type']."'"; else echo "0"; ?>;    
    function checkType(thisType){
        //alert('checkType');
        //alert($('cert_type').value);  
        type = thisType;
        //alert(type);
    }
   var header=<?php if (isset($_POST['show_header'])) echo "'".$_POST['show_header']."'"; else echo "0"; ?>;     
    function checkType2(thisType){
        //alert('checkType');
        //alert($('cert_type').value);  
        header = thisType;
        //alert(type);
    }
    
    function printMedCert(id){
       
      // window.open("cert_medical_exam_pdf.php?id="+id,"medicalCertificate","modal, width=600,height=500,menubar=no,resizable=yes,scrollbars=no");
      //      window.open("cert_medical_exam_pdf.php?id="+id+"&show_header="+header,"medicalCertificate","modal, width=600,height=500,menubar=no,resizable=yes,scrollbars=no");
    
    if (id==0) 
            id="";
        if (window.showModalDialog){  //for IE
            window.showModalDialog("cert_medical_exam_pdf.php?id="+id+"&show_header="+header,"width=600,height=400,menubar=no,resizable=yes,scrollbars=no");
        }else{
            window.open("cert_medical_exam_pdf.php?id="+id+"&show_header="+header,"medicalCertificate","modal, width=600,height=400,menubar=no,resizable=yes,scrollbars=no");
        }
    
    }
    
    function checkComment(){
        //alert('checkComment');
        var d = document.med_certificate;
        
        
        /*if (d.cert_type[0].checked){
            //show medico info
            document.getElementById('with_cond').style.display = 'none';
            
            
        }else if(d.cert_type[1].checked){
            //hide medico info
            document.getElementById('with_cond').style.display = 'none';    
            
        }else if(d.cert_type[2].checked){
            //hide medico info
            document.getElementById('with_cond').style.display = 'none';    
            
        }else if(d.cert_type[3].checked){
            //hide medico info
            document.getElementById('with_cond').style.display = '';    
        } */
        if (d.comment_nr[0].checked){
            //show medico info
            document.getElementById('with_cond').style.display = 'none';
            
            
        }else if(d.comment_nr[1].checked){
            //hide medico info
            document.getElementById('with_cond').style.display = 'none';    
            
        }else if(d.comment_nr[2].checked){
            //hide medico info
            document.getElementById('with_cond').style.display = 'none';    
            
        }else if(d.comment_nr[3].checked){
            //hide medico info
            document.getElementById('with_cond').style.display = '';    
            
        }
    }
    
    function getUnit_weight(){
   
  document.getElementById('weight_unit').value;
  
  }
    
  function getUnit_height(){
    
  
  document.getElementById('height_unit').value;
  
  }  
    
    function preset(){
        //alert('preset');
        var d = document.med_certificate;
        
        checkComment();
        
       /* if (d.signatory[0].checked){
            //show doctor as signatory
            document.getElementById('doc_sig').style.display = '';
            document.getElementById('medrec_sig').style.display = 'none';
            
        }else if(d.signatory[1].checked){
            //hide doctor as signatory
            document.getElementById('doc_sig').style.display = 'none';    
            document.getElementById('medrec_sig').style.display = '';
        } */
        
    }
    
       
    function trimString(objct){
    objct.value = objct.value.replace(/^\s+|\s+$/g,"");
    objct.value = objct.value.replace(/\s+/g,""); 
}/* end of function trimString */

var js_time = "";
function js_setTime(jstime){
    js_time = jstime;    
}

function js_getTime(){
    return js_time;    
}

function validateTime(S) {
    return /^([01]?[0-9])(:[0-5][0-9])?$/.test(S);
}

var seg_validDate=true;
//var seg_validTime=false;

function seg_setValidDate(bol){
    seg_validDate=bol;
//    alert("seg_setValidDate : seg_validDate ='"+seg_validDate+"'");    
}

var seg_validTime=false;
function setFormatTime(thisTime,AMPM){
//    var time = $('time_text_d');
    var stime = thisTime.value;
    var hour, minute;
    var ftime ="";
    var f1 = /^[01]?[0-9]\:[0-5][0-9]$/;
    var f2 = /^[0-9]\:[0-5][0-9]$/;
    var jtime = "";
    
    trimString(thisTime);

    if (thisTime.value==''){
        seg_validTime=false;
        return;
    }
    
    stime = stime.replace(':', '');
    
    if (stime.length == 3){
        hour = stime.substring(0,1);
        minute = stime.substring(1,3);
    } else if (stime.length == 4){
        hour = stime.substring(0,2);
        minute = stime.substring(2,4);
    }else{
        alert("Invalid time format.");
        thisTime.value = "";
        seg_validTime=false;
        thisTime.focus();
        return;
    }
    
    jtime = hour + ":" + minute;
    js_setTime(jtime);
    
    if (hour==0){
         hour = 12;
         document.getElementById(AMPM).value = "A.M.";        
    }else    if((hour > 12)&&(hour < 24)){
         hour -= 12;
         document.getElementById(AMPM).value = "P.M.";
    }

    ftime =  hour + ":" + minute;
    
    if(!ftime.match(f1) && !ftime.match(f2)){
        thisTime.value = "";
        alert("Invalid time format.");
        seg_validTime=false;   
        thisTime.focus();
    }else{
        thisTime.value = ftime;
        seg_validTime=true;   
    }
}// end of function setFormatTime

    //-------------------
                //<body onLoad="preset();"
</script>
</head>

<body onLoad="preset();">
<table width="520" height="236" border="0" align="center" cellpadding="0" cellspacing="0" bordercolor="#BAD0FC" class="style2">
    <tr>
        <td colspan="*"><?= $errorMsg ?></td>
    </tr>
    <tr>        
        <td colspan="*" height="23" bgcolor="#FFFFFF" >
            <table width="100%" border="0" bgcolor="#F8F9FA"class="style3">
                <tr>
                    <td width="18%" ></td>
                    <td width="37%" >&nbsp;</td>
                    <td width="28%" align="right" ><? echo 'Case No. '?></td>
                    <td width="17%" align="left"><? echo $encounter_nr; ?></td>
                </tr>
                <tr>
                    <td><span class="style3"><? echo "Name :"?> </span></td>
                    <td colspan="2" class="style2">
                        <? 
                            #edited by VAN 02-28-08 
                            $name = stripslashes(strtoupper($encInfo['name_first'])).' '.stripslashes(strtoupper($encInfo['name_middle'])).' '.stripslashes(strtoupper($encInfo['name_last']));
                            echo $name;
                        
                        ?>  <? #echo stripslashes(strtoupper($encInfo['name_last']));?>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
                        <span class="style3"><? echo "Age :  "?></span><? echo $encInfo['age'].' old';?>
                    </td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span class="style3"><? echo "Address :"?></span></td>
                    <td colspan="2" class="style2">
                        <? echo stripslashes(strtoupper($encInfo['street_name']))."&nbsp;&nbsp; ".stripslashes(strtoupper($encInfo['brgy_name']))."&nbsp;&nbsp; ".stripslashes(strtoupper($encInfo['mun_name'])). ", ".stripslashes(strtoupper($encInfo['prov_name'])). "&nbsp;&nbsp;".stripslashes(strtoupper($encInfo['zipcode']));?>
                    </td>
                    <td>&nbsp;</td>
                </tr>

            </table>
        </td>
    </tr>
    <tr>
        <td colspan="*" align="left" valign="top" bgcolor="#F8F9FA">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="*" width="467" height="23" bgcolor="#FFFFFF">
            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="15" align="left" background="images/top_05.jpg" bgcolor="#FFFFFF">&nbsp;</td>
                    <td width="442" background="images/top_05.jpg">&nbsp;</td>
                    <td width="10" background="images/top_05.jpg" bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
            </table>
        </td>
    </tr>
    <form id="med_certificate" name="med_certificate" method="post" action="" onSubmit="return chkForm()">
    <?php 
            
            if ($result_diagnosis = $objDRG->getDiagnosisCodes($encounter_nr,$encInfo['encounter_type'])){
                $rowsDiagnosis = $result_diagnosis->RecordCount();
                while($temp=$result_diagnosis->FetchRow()){
                   
                }
            }

            if ($result_therapy = $objDRG->getProcedureCodes($encounter_nr,$encInfo['encounter_type'])){
                $rowsTherapy = $result_therapy->RecordCount();
                while($temp=$result_therapy->FetchRow()){
                  
                
                }
            }
            
            $patientEncInfo = $enc_obj->getEncounterInfo($encounter_nr);
            $consulting_dr = $pers_obj->getPersonellInfo($patientEncInfo['consulting_dr_nr']);
    
            $consulting_dr_middleInitial = "";
            if (trim($consulting_dr['name_middle'])!=""){
                $thisMI=split(" ",$consulting_dr['name_middle']);    
                foreach($thisMI as $value){
                    if (!trim($value)=="")
                        $consulting_dr_middleInitial .= $value[0];
                }        
                if (trim($consulting_dr_middleInitial)!="")
                    $consulting_dr_middleInitial = " ".$consulting_dr_middleInitial.".";
            }
    
            $attending_dr = $pers_obj->getPersonellInfo($patientEncInfo['current_att_dr_nr']);
    
            $attending_dr_middleInitial = "";
            if (trim($attending_dr['name_middle'])!=""){
                $thisMI=split(" ",$attending_dr['name_middle']);    
        
                foreach($thisMI as $value){
                    if (!trim($value)=="")
                        $attending_dr_middleInitial .= $value[0];
                }        
                    if (trim($attending_dr_middleInitial)!=""){
                        $attending_dr_middleInitial = " ".$attending_dr_middleInitial.".";
                    }    
            }    
        
            $consulting_dr_name = "Dr. ".$consulting_dr['name_first']." ".$consulting_dr['name_2']." ".$consulting_dr_middleInitial." ".$consulting_dr['name_last'];
            $attending_dr_name = "Dr. ".$attending_dr['name_first']." ".$attending_dr['name_2']." ".$attending_dr_middleInitial." ".$attending_dr['name_last'];
            
           

    ?>
  
    
    <tr id="space5">
        <td>&nbsp;</td>
    </tr>
    <tr id="doc_sig">
        <!--<td valign="top" bgcolor="#F8F9FA">
            Consulting Doctor :  &nbsp;&nbsp;&nbsp;
            <select name="doctors" id="doctors">
                <option value='0'>-Select a doctor-</option>-
<?php
   
        
        $listDoctors = array_unique($listDoctors);
        foreach($listDoctors as $key=>$value){
            if ($driverCertInfo['dr_nr']==$key){
                echo "                <option value='".$key."' selected=\"selected\">".$value."</option> \n";
            }else{
                echo "                <option value='".$key."'>".$value."</option> \n";
            }    
        }
        
       
?>
           </select> 
            
        </td>--> 
     <tr>
        <td valign="top" bgcolor="#F8F9FA">
            <font style="color:Red"> COMMENTS: </font>
        </td>
     </tr>
     <tr>
        <td>
        <?php
        //$checked1 = "checked";
        //$checked2 = "";
        if($driverCertInfo){
            switch($driverCertInfo['comment_nr']){
                case 0: 
                    $checked0 = "checked";
                    break;
                case 1:
                    $checked1 = "checked";
                    break;
                case 2:
                    $checked2 = "checked";
                    break;
                case 3:
                    $checked3 = "checked";
                    break;
                
            }
        }else{
            $checked0 = checked;
        }
             ?>
            
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="comment_nr" id="comment_nr" type="radio" value="0" onClick="checkType(this.value);checkComment();" <?php echo $checked0; ?>>Fit to Drive   
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="comment_nr" id="comment_nr" type="radio" value="1" onClick="checkType(this.value);checkComment();" <?php echo $checked1; ?>>Unfit to Drive    
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="comment_nr" id="comment_nr" type="radio" value="2" onClick="checkType(this.value);checkComment();"<?php echo $checked2; ?>>w/o Condition    
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="comment_nr" id="comment_nr" type="radio" value="3" onClick="checkType(this.value);checkComment();" <?php echo $checked3; ?>>w/ Condition
        </td>
     </tr>   
     <!--<tr>
        <td>&nbsp;</td>
     </tr> -->
     <tr id="with_cond">
        <td>
           <?php
            if($driverCertInfo['comment_nr']==3){
                switch($driverCertInfo['with_cond_type']){
                    case '3.a':
                            $checkeda = "checked";
                            break;
                    case '3.b':
                            $checkedb = "checked";
                            break;
                    case '3.c':
                            $checkedc = "checked";
                            break;
                    case '3.d':
                            $checkedd = "checked";
                            break;
                    case '3.e':
                            $checkede = "checked";
                            break;   
                }
            }else{
                $checkeda = "checked";
            }
            
           ?>
        
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input name="with_cond_type" id="with_cond_type" type="radio" value="3.a" onClick="checkType(this.value);" <?php echo $checkeda; ?>>
            A. Wear corrective lenses
             <br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input name="with_cond_type" id="with_cond_type" type="radio" value="3.b" onClick="checkType(this.value);" <?php echo $checkedb; ?>>
            B. Drive only w/ special equipment for upper limbs
             <br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input name="with_cond_type" id="with_cond_type" type="radio" value="3.c" onClick="checkType(this.value);" <?php echo $checkedc; ?>>
            C. Drive only w/ special equipment for lower limbs
             <br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input name="with_cond_type" id="with_cond_type" type="radio" value="3.d" onClick="checkType(this.value);" <?php echo $checkedd; ?>>
            D. Daylight driving only
            <br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input name="with_cond_type" id="with_cond_type" type="radio" value="3.e" onClick="checkType(this.value);" <?php echo $checkede; ?>>
            E. Must be accompanied by a person with normal hearing    
        
        </td>
     </tr>
        
     <tr>
        <td valign="top" bgcolor="#F8F9FA">
            Visual Acuity &nbsp;
        </td>
    </tr>
    <tr>
        <td >
            
            <!--&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:Navy">Right Ear : </font> &nbsp;&nbsp;&nbsp; <input name="right_ear" id="right_ear" type="text" size="3" value="<?=ucwords(strtolower(trim($driverCertInfo['right_ear'])))?>">
            &nbsp;&nbsp;&nbsp;&nbsp;<font style="color:Navy">Left Ear : </font>  &nbsp;&nbsp; <input name="left_ear" id="left_ear" type="text" size="3" value="<?=ucwords(strtolower(trim($driverCertInfo['left_ear'])))?>"> 
            &nbsp;&nbsp;&nbsp;&nbsp;<font style="color:Navy">Right Ear : </font> &nbsp;&nbsp;&nbsp; <input name="right_ear" id="right_ear" type="text" size="3" >                                                            -->
        
            <!--$sREarJS= '<input name="DOI" type="text" size="15" maxlength=10 value="'.$DOI_val.'"'. 
                            'onFocus="this.select();" 
                            id = "right_ear"
                            onKeyUp="javascript:this.value=this.value.replace(/[^0-9]/g, '')">
               -->
            
            &nbsp;&nbsp;&nbsp;&nbsp;<font style="color:Navy">Right Eye : </font> &nbsp;&nbsp;&nbsp; <input name="right_eye" id="right_eye" type="text" size="3" onkeyup="javascript:this.value=this.value.replace(/[^0-9\/]/g, '')"  value="<?=ucwords(strtolower(trim($driverCertInfo['right_eye'])))?>"> 
            &nbsp;&nbsp;&nbsp;&nbsp;<font style="color:Navy">Left Eye : </font>  &nbsp;&nbsp; <input name="left_eye" id="left_eye" type="text" size="3" onkeyup="javascript:this.value=this.value.replace(/[^0-9\/]/g, '')" value="<?=ucwords(strtolower(trim($driverCertInfo['left_eye'])))?>">
        
        </td>
    </tr> 
    <tr>
        <td>&nbsp;</td>
    </tr>    
    
    <tr>
        <td valign="top" bgcolor="#F8F9FA">
            Hearing &nbsp;
        </td>
    </tr>
    <tr>
        <td >
            &nbsp;&nbsp;&nbsp;&nbsp;<font style="color:Navy">Right Ear :</font>  &nbsp;&nbsp;&nbsp; <input name="right_ear" id="right_ear" type="text" size="15" onkeyup="javascript:this.value=this.value.replace(/[^A-Z0-9 + A-Z0-9]/g, '')" value="<?=ucwords(strtolower(trim($driverCertInfo['right_ear'])))?>">
            &nbsp;&nbsp;&nbsp;&nbsp;<font style="color:Navy">Left Ear : </font>  &nbsp;&nbsp; <input name="left_ear" id="left_ear" type="text" size="15" onkeyup="javascript:this.value=this.value.replace(/[^A-Z0-9 + A-Z0-9]/g, '')" value="<?=ucwords(strtolower(trim($driverCertInfo['left_ear'])))?>">
        </td>
    </tr> 
    <tr>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td valign="top" bgcolor="#F8F9FA">
            BP :  &nbsp;&nbsp;&nbsp; <input name="systole" id="systole" type="text" size="3" onkeyup="javascript:this.value=this.value.replace(/[^0-9]/g, '')" value="<?=ucwords(strtolower(trim($driverCertInfo['systole'])))?>">
                  &nbsp; / &nbsp; <input name="diastole" id="diastole" type="text" size="3" onkeyup="javascript:this.value=this.value.replace(/[^0-9]/g, '')" value="<?=ucwords(strtolower(trim($driverCertInfo['diastole'])))?>">
        </td>
    </tr>
    
    <tr>
        <td>&nbsp;</td>
    </tr>
    
    <tr>
        <td valign="top" bgcolor="#F8F9FA">
             With Corrective Lenses?
        </td>
     </tr>
    <tr>
        <td>
            <?php
                if(!$driverCertInfo){
                    $checked1 = "checked";
                    $checked2 = "";
                }else{
                    if($driverCertInfo['is_with_corrective_lenses']==0){
                        $checked1 = "checked";
                        $checked2 = "";
                    }else if($driverCertInfo['is_with_corrective_lenses']==1){
                        $checked1 = "";
                        $checked2 = "checked";
                    }
                }
                
             ?>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="is_with_corrective_lenses" id="is_with_corrective_lenses" type="radio" value="0" onClick="checkType(this.value);" <?php echo $checked1; ?>>No   
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="is_with_corrective_lenses" id="is_with_corrective_lenses" type="radio" value="1" onClick="checkType(this.value);" <?php echo $checked2; ?>>Yes    
           
        </td>
     </tr> 
     
     <tr>
        <td>&nbsp;</td>
    </tr>
    
    <tr>
        <td valign="top" bgcolor="#F8F9FA">
             With Hearing Aid?
        </td>
     </tr>
    <tr>
        <td>
            <?php
                if(!$driverCertInfo){
                    $checked1 = "checked";
                    $checked2 = "";
                }else{
                    if($driverCertInfo['is_with_hearing_aid']==0){
                        $checked1 = "checked";
                        $checked2 = "";
                    }else if($driverCertInfo['is_with_hearing_aid']==1){
                        $checked1 = "";
                        $checked2 = "checked";
                    }
                }
                
             ?>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="is_with_hearing_aid" id="is_with_hearing_aid" type="radio" value="0" onClick="checkType(this.value);" <?php echo $checked1; ?>>No   
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="is_with_hearing_aid" id="is_with_hearing_aid" type="radio" value="1" onClick="checkType(this.value);" <?php echo $checked2; ?>>Yes    
           
        </td>
     </tr>     
        
    <tr>
        <td>&nbsp;</td>
    </tr>
    
    <tr>
        <td >
            Ishihara Plate :  &nbsp;&nbsp;&nbsp; <input name="i_plate" id="i_plate" type="text" size="20" onkeyup="javascript:this.value=this.value.replace(/[^A-Z0-9 + A-Z0-9]/g, '')" value="<?=ucwords(strtolower(trim($driverCertInfo['i_plate'])))?>">
        </td>
    </tr> 
    
    <tr>
        <td>&nbsp;</td>
    </tr>
    
    <tr>
        <td valign="top" bgcolor="#F8F9FA">
            General Physique &nbsp;:
        </td>
    </tr>
    <tr>
            <td>
                <textarea cols="60" rows="5" name="gen_physique" id="gen_physique" wrap="physical"><?php echo $driverCertInfo['gen_physique']; ?></textarea>            </td>
    </tr>
    <tr>
        <td valign="top" bgcolor="#F8F9FA">
            Contagious Diseases &nbsp;: </td>
    </tr>
    <tr>
            <td>
                <textarea cols="60" rows="5" name="diseases" id="diseases" wrap="physical"><?php echo $driverCertInfo['diseases']; ?></textarea>            </td>
    </tr>
    <tr>
        <td valign="top" bgcolor="#F8F9FA">
            Remarks &nbsp;:
        </td>
    </tr>
    <tr>
            <td>
                <textarea cols="60" rows="5" name="remarks" id="remarks" wrap="physical"><?php echo $driverCertInfo['remarks']; ?></textarea>            </td>
    </tr>
    <tr>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td valign="top" bgcolor="#F8F9FA">     
            Height :  &nbsp;&nbsp;&nbsp; <input name="height" id="height" type="text" size="10" onkeyup="javascript:this.value=this.value.replace(/[^0-9\'\.]/g, '')" value="<?=ucwords(strtolower(trim($driverCertInfo['height'])))?>">
            <select name = "height_unit" id="height_unit" onchange="getUnit_height()">
                    <?
                      $result=$db->Execute("SELECT cu.id, cu.name FROM care_unit_measurement AS cu where cu.unit_type_nr=3 AND cu.id IN('cm', 'in', 'ft')
                                            ORDER BY cu.id DESC");
                        while ($row=$result->FetchRow() ) {
                          if($driverCertInfo['height_unit']==$row['id']){
                            echo "        <option value='".$row['id']."' selected=\"selected\">".$row['name']."</option> \n";
                          }
                          else{
                            echo $options ='<option value="'.$row['id'].'">'.$row['name'].'</option>';
                          }
                        }
                    ?>
                </select>
        </td>
    </tr> 
    
    <tr>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td valign="top" bgcolor="#F8F9FA">     
            Weight :  &nbsp;&nbsp; <input name="weight" id="weight" type="text" size="10" onkeyup="javascript:this.value=this.value.replace(/[^0-9\.]/g, '')" value="<?=ucwords(strtolower(trim($driverCertInfo['weight'])))?>">
             <select name = "weight_unit" id="weight_unit" onchange="getUnit_weight()">
                    <?
                      $result=$db->Execute("SELECT cu.id, cu.name FROM care_unit_measurement AS cu where cu.unit_type_nr=2 AND cu.id IN ('kg', 'gm')
                                            ORDER BY cu.id DESC");
                        while ($row=$result->FetchRow() ) {
                          if($driverCertInfo['weight_unit']==$row['id']){
                            echo "        <option value='".$row['id']."' selected=\"selected\">".$row['name']."</option> \n";
                          }
                          else{
                            echo $options ='<option value="'.$row['id'].'">'.$row['name'].'</option>';
                          }
                        }
                    ?>
                </select>
        </td>
    </tr> 
    
    <tr>
        <td>&nbsp;</td>
    </tr>
    
    <tr>
        <td valign="top" bgcolor="#F8F9FA">
            Consulting/Attending Doctor :  &nbsp;&nbsp;&nbsp;
            <select name="doctors" id="doctors">
                <!--<option value='0'>-Select a doctor-</option>-->
<?php
        
        $listDoctors = array_unique($listDoctors);
        foreach($listDoctors as $key=>$value){
            if ($driverCertInfo['dr_nr']==$key){
                echo "                <option value='".$key."' selected=\"selected\">".$value."</option> \n";
            }else{
                echo "                <option value='".$key."'>".$value."</option> \n";
            }    
        }
    
?>
            </select>
        </td>
        
    </tr>
    
    <tr>
        <td>&nbsp;</td>
    </tr>
    
    <tr>
        <td valign="top" bgcolor="#F8F9FA">
            <font style="color:Red"> Header Option </font>
        </td>
     </tr>
    <tr>
        <td>
        <?php
        $checked1 = "";
        $checked2 = "checked";
             ?>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="show_header" id="show_header" type="radio" value="1" onClick="checkType2(this.value);" <?php echo $checked1; ?>>With Header   
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="show_header" id="show_header" type="radio" value="0" onClick="checkType2(this.value);" <?php echo $checked2; ?>>Without Header    
           
        </td>
     </tr>   
    
     <tr>
        <td>&nbsp;</td>
     </tr>
     <tr>
        <td>&nbsp;</td>
     </tr>
    
    <tr>
        <td align="center" background="images/top_05.jpg" bgcolor="#EDF2FE">
<?php
            if (!$driverCertInfo || empty($driverCertInfo)){
                echo '            <input type="hidden" name="mode" id="mode" value="save">'."\n";
                echo '            <input type="submit" name="Submit" value="Save">'."\n";
            }else{
                echo '            <input type="hidden" name="mode" id="mode" value="update">'."\n";
                echo '            <input type="button" name="Print" value="Print" onClick="printMedCert('.$encounter_nr.')">'."\n &nbsp; &nbsp;";
                echo '            <input type="submit" name="Submit" value="Update">'."\n";
            }
            echo '            <input type="hidden" name="encounter_nr" id="encounter_nr" value="'.$encounter_nr.'">'."\n";
?>
            &nbsp; &nbsp;
            <input type="button" name="Cancel" value="Cancel"  onclick="window.close()">
            <input type="hidden" name="pid" id="pid" value="<?=$encInfo['pid']?>">
        </td>
    </tr>
    
    </form>
</table>

</body>
</html>
