<?php
   require('./roots.php'); 
   require($root_path."modules/laboratory/ajax/lab-new.common.php"); 
   require_once($root_path.'include/care_api_classes/class_lab_results.php');
   
   #added by VAN 12-10-08
   require_once($root_path.'include/care_api_classes/class_department.php');
   require_once($root_path.'include/care_api_classes/class_personell.php');
   require_once($root_path.'include/care_api_classes/class_ward.php');
   $dept_obj=new Department;
   $ward_obj = new Ward;
   $pers_obj=new Personell; 
    
   //require_once($root_path.'modules/repgen/pdf_lab_results.php');
   $xajax->printJavascript($root_path.'classes/xajax');
   
   $lab_results = new Lab_Results();
   
   $submit = $_REQUEST["submit"];
   #$status = $_REQUEST["status"]; //"edit"; //$_REQUEST["status"]; 
   
   $service_code = $_REQUEST["service_code"]; //"CBC"; //$_REQUEST["service_code"];
   $refno = $_REQUEST["refno"]; //"2008000534"; //$_REQUEST["refno"];
   $pid = $_REQUEST["pid"]; //"10000834"; //$_REQUEST["pid"];
   $done = $_REQUEST["done"];
   $med_tech_pid = $HTTP_SESSION_VARS["sess_user_personell_nr"];
   
   $system = $lab_results->get_system();
   
  # echo "submit = ".$submit;
   
   if($submit=="DELETE")
   {
       $lab_results->delete_lab_resultdata($refno, $service_code);
       $lab_results->delete_lab_results($refno, $service_code);
       echo "<script type='text/javascript'>window.location = 'seg-lab-request-order-list.php?sid=657924a2f1d88889da0c852eaaff83aa&lang=en&user_origin=lab&done=0&checkintern=1';</script>";
   }
   else if($submit=="SAVE")
   {
       #echo "<br>status = ".$status;
       $lab_result = array(array(), array());
       $sql = "SELECT name from seg_lab_result_params WHERE service_code='$service_code';";
       $result = $lab_results->exec_query($sql);
       for($i=0; $val=$result->FetchRow();$i++)
       {
           $tmp = $val["name"];
           $lab_result[0][$i] = $val["name"];
           $tmp = str_replace(" ", "_", $tmp);
           $tmp = str_replace(".", "_", $tmp);
           $rai = $_POST[$tmp];
           $lab_result[1][$i] = $_POST[$tmp];
       }
       if($status=="add")
       {
           $lab_results->add_lab_resultdata($refno, $service_code, $_POST["date"], $med_tech_pid, $_POST["pathologist"]);
           $lab_results->add_lab_results($lab_result, $refno, $service_code, $system);
           $status="edit";
       }
       else if($status=="edit")
       {
           $lab_results->update_lab_resultdata($refno, $service_code, $_POST["date"], $med_tech_pid, $_POST["pathologist"]);
           $lab_results->update_lab_results($lab_result, $refno, $service_code);
       }
   }
   else if($submit=="SAVE AND DONE")
   {
       $lab_result = array(array(), array());
       $sql = "SELECT name from seg_lab_result_params WHERE service_code='$service_code';";
       $result = $lab_results->exec_query($sql);
       for($i=0; $val=$result->FetchRow();$i++)
       {
           $tmp = $val["name"];
           $lab_result[0][$i] = $val["name"];
           $tmp = str_replace(" ", "_", $tmp);
           $tmp = str_replace(".", "_", $tmp);
           $rai = $_POST[$tmp];
           $lab_result[1][$i] = $_POST[$tmp];
       }
       if($status=="add")
       {
           $lab_results->add_lab_resultdata($refno, $service_code, $_POST["date"], $med_tech_pid, $_POST["pathologist"]);
           $lab_results->add_lab_results($lab_result, $refno, $service_code, $system);
           $status="edit";
       }
       else if($status=="edit")
       {
           $lab_results->update_lab_resultdata($refno, $service_code, $_POST["date"], $med_tech_pid, $_POST["pathologist"]);
           $lab_results->update_lab_results($lab_result, $refno, $service_code);
       }
       
       /*echo "<script type='text/javascript'>window.location = 'seg-lab-request-order-list.php?sid=657924a2f1d88889da0c852eaaff83aa&lang=en&user_origin=lab&done=0&checkintern=1';</script>";*/
       echo "<script type='text/javascript'>window.parent.location = 'seg-lab-request-order-list.php?sid=657924a2f1d88889da0c852eaaff83aa&lang=en&user_origin=lab&done=1&checkintern=1&searhkey=".$pid."';</script>";
   }
   else if($submit=="VIEW PDF")
   {
       $x = $root_path.'modules/repgen/pdf_lab_results.php?pid='.$pid.'&refno='.$refno.'&service_code='.$service_code;
        echo "<script type='text/javascript'>window.open('$x','Rep_Gen','menubar=no,directories=no');</script>";
   }
   
   #echo "<br>pid, refno, service_code = ". $pid.", ".$refno.", ".$service_code;
   
   $res = $lab_results->getLabResult($refno,$service_code);
   #echo "count = ".$lab_results->count;
    #edited by VAN 12-10-08
    /*
   if ($lab_results->count) 
    $status = "edit"; 
   else
    $status = $_GET["status"];   
    */
   #echo "status get =".$status;
   
   #$patient = $lab_results->get_patient_data($pid, $refno, $service_code);
   #edited by VAN 12-09-2008
   $patient = $lab_results->get_patient_data($refno, $service_code);
   #echo "sql = ".$lab_results->sql;
   extract($patient);
   
   if ($pid) 
    $name_patient = mb_strtoupper($name_last).", ".mb_strtoupper($name_first)." ".mb_strtoupper($name_middle);
   else
    $name_patient = ""; 
   
   if ($street_name){
        if ($brgy_name!="NOT PROVIDED")
            $street_name = $street_name.", ";
        else
            $street_name = $street_name.", ";    
    }#else
        #$street_name = "";    
                
                
        
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

    if (empty($age))
        $age = "unknown";
        
     if ($encounter_type==1){
        $enctype = "ERPx";
        $location = "EMERGENCY ROOM";
     }elseif ($encounter_type==2){
         #$enctype = "OUTPATIENT (OPD)";
         $enctype = "OPDx";
         $dept = $dept_obj->getDeptAllInfo($current_dept_nr);
         $location = strtoupper(strtolower(stripslashes($dept['name_formal'])));
     }elseif (($encounter_type==3)||($encounter_type==4)){
         if ($result['encounter_type']==3)
            $enctype = "INPx (ER)";
         elseif ($encounter_type==4)
            $enctype = "INPx (OPD)";
                
         $ward = $ward_obj->getWardInfo($current_ward_nr);
         $location = strtoupper(strtolower(stripslashes($ward['ward_id'])))." Rm # : ".$current_room_nr;
      }else{
          $enctype = "WPx";
                    #$dept = $dept_obj->getDeptAllInfo($result['current_dept_nr']);
                    #$location = strtoupper(strtolower(stripslashes($dept['name_formal'])));
           $location = 'WALK-IN';
      }  
      
      $result = $pers_obj->getPersonellInfo($request_doctor);
      if (trim($result["name_middle"]))
         $dot  = ".";
                    
      #$doctor = trim($result["name_last"]).", ".trim($result["name_first"])." ".substr(trim($result["name_middle"]),0,1).$dot;
      #$doctor = ucwords(strtolower($doctor)).", MD";
      
      $doctor = "DR. ".trim($result["name_first"])." ".substr(trim($result["name_middle"]),0,1).$dot." ".trim($result["name_last"]);          
      $doctor = htmlspecialchars(mb_strtoupper($doctor)); 
      
            
    #-------------    
   #echo "".$lab_results->sql;
  # print_r($patient);
   $date = date('Y-m-d');
   $pathologist = 0;
   $med_tech = "";
   if($status=="edit")
   {
       $sql = "select service_date, med_tech_pid, pathologist_pid FROM seg_lab_resultdata WHERE refno='$refno' AND service_code='$service_code'  AND (ISNULL(`status`) OR `status`!='deleted');";
       $result = $lab_results->exec_query($sql);
       if($result!="" && $resdata = $result->FetchRow())
       {
           $date = substr($resdata["service_date"], 0, -9);
           $pathologist = $resdata["pathologist_pid"];
           $med_tech_pid = $resdata["med_tech_pid"];
       }
       else
           $status = "add";
   }
  
   $sql = "select CONCAT(name_first, ' ', name_middle, ' ', name_last) as name from care_person, care_personell WHERE nr='$med_tech_pid' AND care_person.pid=care_personell.pid  AND (care_personell.job_function_title LIKE '%med%tech%' OR care_personell.job_position LIKE '%med%tech%');";
  /*
   $sql = "SELECT pr.pid, 
			CONCAT(IF(ISNULL(cp.name_first), '', CONCAT(cp.name_first, ' ')), IF(ISNULL(cp.name_middle), '', CONCAT(substring(cp.name_middle,1,1), '. ')), IF(ISNULL(cp.name_last), '', cp.name_last)) as name 
			FROM care_person AS cp
			INNER JOIN care_personell AS pr ON cp.pid = pr.pid 
			WHERE (pr.job_function_title LIKE '%medical technologist%' 
			OR pr.job_position LIKE '%medical technologist%')";
   */
   #echo "sql = ".$sql;
   $result = $lab_results->exec_query($sql);
   if($result!="" && $resdata = $result->FetchRow())
       $med_tech = $resdata["name"];
   else
       $med_tech = " ";
	
	   
   $rd = "";
   $rd2 = "";
   if($done==1)
   {
        $rd="readonly='readonly'";
        $rd2="disabled='disabled'";
   }
    
?>
 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?php echo $lab_results->get_service_name($service_code); ?></title>
<style type="text/css">
<!--
.style2 {
    font-size: 12px;
    font-family: Verdana, Arial, Helvetica, sans-serif;
}
-->
</style>

 <script type="text/javascript" src="datepickercontrol.js"></script>
<link type="text/css" rel="stylesheet" href="datepickercontrol.css">
<link rel="stylesheet" href="labresult.css" type="text/css">
<style type="text/css">
<!--
body {
    margin-top: 40px;
}
.style7 {color: #51622F}
.style8 {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 12px;
}
-->
</style>
<script type="text/javascript" src="<?=$root_path?>js/shortcut.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script language="javascript" >
<!--
function ToBeServed(scode){
    var is_served, refno;
    is_served = 1;
    refno = <?php echo $_REQUEST["refno"];?>;
    
    var answer = confirm("Are you sure that the request is already done? It can't be undone. \n Click OK if YES, otherwise CANCEL.");        

    if (answer)
        xajax_savedServedPatient(refno, scode,is_served);
}
-->
</script>
</head>

<body>
<form action="lab_results.php" method="post">    
<?php

    $res = $lab_results->getLabResult($refno,$service_code);
   #echo "count = ".$lab_results->count;
    #edited by VAN 12-10-08
   if ($lab_results->count) 
    $status = "edit"; 
   else
    $status = $_GET["status"];       
?>
<input type="hidden" name="status" id="status" value="<?= $status ?>" >
<input type="hidden" name="pid" value="<?= $pid ?>" >
<input type="hidden" name="refno" value="<?= $refno ?>" >
<input type="hidden" name="service_code" value="<?= $service_code ?>" >
<input type="hidden" name="med_tech_pid" value="<?= $med_tech_pid ?>" >
<table width="80%" border="0" align="center" cellpadding="1" cellspacing="0" class="carlpanel">
  <tr>
    <td><table width="100%" border="0" align="center" cellpadding="1" cellspacing="0">
      <tr>
        <td width="51%" class="carlPanelHeader"><div align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $lab_results->get_service_name($service_code); ?></div></td>
      </tr>
      <tr>
        <td valign="top" bgcolor="#FFFFFF"><table width="100%" border="0" cellspacing="0" cellpadding="1" >
            <tr>
              <td height="149" valign="top" bgcolor="#FFFFFF" class="carlpanel"><table width="100%" border="0" cellpadding="0" cellspacing="2">
                  <tr>
                    <td width="54%"><table width="100%" border="0" cellspacing="4" cellpadding="1" class="carlpanel">
                        <tr>
                          <td class="carlPanelHeader">Name</td>
                          <td><input name="patient_name" id="patient_name" size="60%" type="text" value="<?= $name_patient ?>" readonly="readonly"/>                          </td>
                        </tr>
                        <tr>
                          <td class="carlPanelHeader" width="40%">Address</td>
                          <td width="60%"><input name="address" id="address" type="text"  value="<?= $address ?>" readonly="readonly" size="60%" /></td>
                        </tr>
                    </table></td>
                    <td width="46%"><table height="64" border="0" cellpadding="1" cellspacing="4" class="carlpanel">
                        <tr>
                          <td height="26">&nbsp;</td>
                          <td>&nbsp;</td>
                          <td>Date</td>
                          <td><input name="date" type="text" size="15" value="<?= $date ?>" readonly="readonly"/></td>
                        </tr>
                        <tr>
                          <td width="33%" height="24" >Age </td>
                          <td width="34%"><input name="age" id="age" type="text" size="5"  value="<?= $age ?>" readonly="readonly"/>                          </td>
                          <td width="16%">Sex</td>
                          <td width="17%"><select name="select" disabled="disabled">
                              <option value="Male" <? if($sex="m") echo "selected='selected'"?>>Male</option>
                              <option value="Female"<? if($sex="f") echo "selected='selected'"?>>Female</option>
                            </select>                          </td>
                        </tr>
                    </table></td>
                  </tr>
                  <tr>
                    <td><table width="100%" border="0" cellpadding="2" cellspacing="2" class="style8">
                        <tr>
                          <td width="10%" class="carlPanelHeader">Ward</td>
                          <td width="90%" class="carlpanel"><input name="location" id="location" type="text" class="style2"  value="<?= $location ?>" readonly="readonly" size="63%"/></td>
                        </tr>
                    </table></td>
                    <td><table width="100%" border="0" cellpadding="2" cellspacing="2" class="style8">
                        <tr>
                          <td width="10%" class="carlPanelHeader">Physician</td>
                          <td width="90%" class="carlpanel"><input name="textfield252" type="text" class="style2"  value="<?= $doctor ?>" readonly="readonly"  size="46%"/></td>
                        </tr>
                    </table></td>
                  </tr>
                  <tr>
                    <td colspan="2">&nbsp;</td>
                  </tr>
                  <tr  >
                    <td colspan="2" bgcolor="#FFFFFF"  >
					
					<table border="0"><tr><td>&nbsp;</td><td>&nbsp;</td></tr></table>
					<table width="100%" border="0" cellpadding="1" cellspacing="2" >
                        <?php
                    $group_id ="";
                    $fld_value="";
                    $sql = "SELECT is_boolean, is_numeric, is_longtext, param_id, SI_lo_normal, SI_hi_normal, SI_unit, CU_lo_normal, CU_hi_normal, CU_unit, seg_lab_result_params.param_group_id as group_id, seg_lab_result_params.name as param_name, seg_lab_result_paramgroups.name as group_name FROM seg_lab_result_params LEFT JOIN seg_lab_result_paramgroups ON seg_lab_result_params.param_group_id=seg_lab_result_paramgroups.param_group_id WHERE service_code='$service_code' ORDER BY order_nr ASC;";
                    $result = $lab_results->exec_query($sql);
                    while($result!=NULL && $val = $result->FetchRow())
                    {
                        echo "<tr>";
                        $str="";
                        $fld_value = "";
                        if($status=="edit")
                        {
                            $res = $lab_results->get_lab_results($refno, $service_code, $val["param_id"]);
                            if($res != "")
                            {
                                if($rvalue = $res->FetchRow())
                                   $fld_value = $rvalue["result_value"];
                            }
                        }
                        if($group_id != $val["group_id"])
                        {
                            $group_id = $val["group_id"];
                            if($group_id!="")
                            {
                                echo "<td colspan=5 bgcolor='#F4F4EA' class='carlPanelHeader'><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;". $val["group_name"] ."</strong></td></tr><tr>";
                            }
                        }
                        if($group_id!="")
                            $str="<td width='10%'></td>";
                        if($val["is_boolean"]=="1")
                        {
                            if($fld_value=="on")
                                echo $str. "<td colspan=2 class='carlpanel'><input type=checkbox name='". $val["param_name"] ."' checked='true' $rd2>". $val["param_name"] ."</td>";
                            else
                                echo $str. "<td colspan=2 class='carlpanel'><input type=checkbox name='". $val["param_name"] ."' $rd2>". $val["param_name"] ."</td>";
                        }   
                        else
                        {
                            $unit = "";
                            $lo_normal="";
                            $hi_normal="";
                            if($system="SI")
                            {
                                if($val["SI_unit"]=="" && $val["SI_lo_normal"]=="" && $val["SI_hi_normal"]=="")
                                {
                                    $unit = $val["CU_unit"];
                                    $lo_normal = $val["CU_lo_normal"];
                                    $hi_normal = $val["CU_hi_normal"];
                                }
                                else
                                {
                                    $unit = $val["SI_unit"];
                                    $lo_normal = $val["SI_lo_normal"];
                                    $hi_normal = $val["SI_hi_normal"];
                                }
                            }
                            else if($system="CU")
                            {
                                if($val["CU_unit"]=="" && $val["CU_lo_normal"]=="" && $val["CU_hi_normal"]=="")
                                {
                                    $unit = $val["SI_unit"];
                                    $lo_normal = $val["SI_lo_normal"];
                                    $hi_normal = $val["SI_hi_normal"];
                                }
                                else
                                {
                                    $unit = $val["CU_unit"];
                                    $lo_normal = $val["CU_lo_normal"];
                                    $hi_normal = $val["CU_hi_normal"];
                                }
                            }
                            if($group_id!="")
                                $str=$str. "<td  class='carlpanel'>";
                            else
                                $str="<td colspan=2 class='carlpanel'>&nbsp;&nbsp;&nbsp;&nbsp;";
                            echo $str. $val["param_name"] ."</td>";
                            $level = "<td width='20%'></td>";
                            if($lo_normal!="" OR $hi_normal!="")
                            {
                                if($lo_normal=="")
                                {
                                    $str = "<td> < ". $hi_normal ." ". $unit ."</td>";
                                    if($fld_value < $hi_normal)
                                        $level = "<td width='20%' align=center><font color=blue>NORMAL</font></td>";
                                    else
                                        $level = "<td width='20%' align=center><font color=red>HIGH</font></td>";
                                }
                                else if($hi_normal=="")
                                {
                                    $str = "<td> >= ". $lo_normal ." ". $unit ."</td>";
                                    if($fld_value >= $lo_normal)
                                        $level = "<td width='20%' align=center><font color=blue>NORMAL</font></td>";
                                    else
                                        $level = "<td width='20%' align=center><font color=red>LOW</font></td>";
                                }
                                else
                                {
                                    $str = "<td>". $lo_normal ." - ". $hi_normal ." ". $unit ."</td>";
                                    if($fld_value < $lo_normal)
                                        $level = "<td width='20%' align=center><font color=red>LOW</font></td>";
                                    else if($fld_value > $hi_normal)    
                                        $level = "<td width='20%' align=center><font color=red>HIGH</font></td>";
                                    else
                                        $level = "<td width='20%' align=center><font color=blue>NORMAL</font></td>";
                                }
                            }
                            if($val["is_longtext"]=="1")
                                echo "<td><input type=textarea name='". $val["param_name"] ."' value='$fld_value' $rd> $unit </td>";
                            else
                                echo "<td width='*'><input type=text name='". $val["param_name"] ."' value='$fld_value' $rd> $unit </td>";
                            if($status=="edit")
                                echo $level;
                            if($lo_normal!="" OR $hi_normal!="")
                                echo $str;
                        }
                    }
                  ?>
                        <tr>
                          <td width="34%" colspan=2 class="carlPanelHeader">Medical Technologist </td>
                          <td width="66%" class="carlpanel" colspan=3>
						  	<!--<input name="textfield23" type="text" class="style2" size="50" value="<?= $med_tech?>" readonly="readonly"/>-->
							<input name="textfield23" type="text" class="style2" size="50" value="<?= $med_tech?>" />
							<!--
							<select name="medtech" id="medtech" <?php echo $rd2;?>>
                              <?php
							  #edited by VAN 12-06-08
							  /*
                           $sql = "SELECT care_personell.pid, CONCAT(IF(ISNULL(name_first), '', CONCAT(name_first, ' ')), IF(ISNULL(name_middle), '', CONCAT(name_middle, '. ')), IF(ISNULL(name_last), '', name_last)) as name from care_personell, care_person 
						           WHERE care_person.pid = care_personell.pid 
								   AND (care_personell.job_function_title LIKE '%pathologist%' OR care_personell.job_position LIKE '%pathologist%');";
                          */
						  $sql = "SELECT pr.pid, 
									CONCAT(IF(ISNULL(cp.name_first), '', CONCAT(cp.name_first, ' ')), IF(ISNULL(cp.name_middle), '', CONCAT(substring(cp.name_middle,1,1), '. ')), IF(ISNULL(cp.name_last), '', cp.name_last)) as name 
									FROM care_person AS cp
									INNER JOIN care_personell AS pr ON cp.pid = pr.pid 
									WHERE (pr.job_function_title LIKE '%medical technologist%' 
									OR pr.job_position LIKE '%medical technologist%')";
						  
						   $result = $lab_results->exec_query($sql);
                           while($result!=NULL && $x = $result->FetchRow())
                           {
                               if($x["pid"]==$pathologist)
                                   $tmp = "selected='selected'";
                               else
                                   $tmp="";
								   
                               echo "<option value='". $x["pid"] ."' ". $tmp .">". $x["name"] ."</option>";
                           }
                        ?>
                            </select>-->
						</td>
                        </tr>
                        <tr>
                          <td colspan=2 class="carlPanelHeader">Pathologist</td>
                          <td class="carlpanel" colspan=3>
						  <select name="pathologist" id="pathologist" <?php echo $rd2;?>>
                              <?php
							  #edited by VAN 12-06-08
							  /*
                           $sql = "SELECT care_personell.pid, CONCAT(IF(ISNULL(name_first), '', CONCAT(name_first, ' ')), IF(ISNULL(name_middle), '', CONCAT(name_middle, '. ')), IF(ISNULL(name_last), '', name_last)) as name from care_personell, care_person 
						           WHERE care_person.pid = care_personell.pid 
								   AND (care_personell.job_function_title LIKE '%pathologist%' OR care_personell.job_position LIKE '%pathologist%');";
                          */
						  $sql = "SELECT pr.pid, 
									CONCAT(IF(ISNULL(cp.name_first), '', CONCAT(cp.name_first, ' ')), IF(ISNULL(cp.name_middle), '', CONCAT(substring(cp.name_middle,1,1), '. ')), IF(ISNULL(cp.name_last), '', cp.name_last)) as name 
									FROM care_person AS cp
									INNER JOIN care_personell AS pr ON cp.pid = pr.pid 
									WHERE (pr.job_function_title LIKE '%pathologist%' 
									OR pr.job_position LIKE '%pathologist%')";
						  
						   $result = $lab_results->exec_query($sql);
                           while($result!=NULL && $x = $result->FetchRow())
                           {
                               if($x["pid"]==$pathologist)
                                   $tmp = "selected='selected'";
                               else
                                   $tmp="";
								   
                               echo "<option value='". $x["pid"] ."' ". $tmp .">". $x["name"] ."</option>";
                           }
                        ?>
                            </select></td>
                        </tr>
                    </table></td>
                  </tr>
              </table></td>
            </tr>
        </table></td>
      </tr>
      <tr>
        <td height="26" align=center class="carlPanelHeader">  
        <?php
            if($done==0)
            {?>
            &nbsp;<input type="image" value="SAVE" src="../../images/btn_save.gif" name="submit" />
          &nbsp;&nbsp;
          <input type="image" value="SAVE AND DONE" src="../../images/btn_done.gif" name="submit" onclick="javascript: return ToBeServed('<?php echo $service_code; ?>');"/>
          <!--<input type="Submit" value="SAVE AND DONE" name="submit" onclick="javascript: return ToBeServed('<?php echo $service_code; ?>');"/>-->
          &nbsp;&nbsp;
          <a href='seg-lab-request-order-list.php?sid=657924a2f1d88889da0c852eaaff83aa&lang=en&user_origin=lab&done=0&checkintern=1'><img src="../../images/his_cancel_button.gif" border="0"></img></a>
          &nbsp;&nbsp;
          <input type="image" value="DELETE" src="../../images/btn_delete.gif" name="submit" />
          &nbsp;<?php } ?>&nbsp;
          <input type="image" value="VIEW PDF" src="../../images/btn_printpdf.gif" name="submit" />
      </tr>
    </table></td>
  </tr>
  <tr>
</table>
</form>
</body>
</html>
