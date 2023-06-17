<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org, 
*
* See the file "copy_notice.txt" for the licence notice
*/
$lang_tables[]='departments.php';
define('LANG_FILE','doctors.php');
$local_user='ck_doctors_dienstplan_user';
require_once($root_path.'include/inc_front_chain_lang.php');

if(!isset($dept_nr)||!$dept_nr){
	header('Location:doctors-select-dept.php'.URL_REDIRECT_APPEND.'&retpath='.$retpath);
	exit;
}

//$db->debug=1;

$thisfile=basename(__FILE__);
$breakfile="doctors-dienstplan.php".URL_APPEND."&dept_nr=$dept_nr&pmonth=$pmonth&pyear=$pyear&retpath=$retpath";

require_once($root_path.'include/care_api_classes/class_department.php');
$dept_obj=new Department;
$dept_obj->preloadDept($dept_nr);

require_once($root_path.'include/care_api_classes/class_personell.php');
$pers_obj=new Personell;
$pers_obj->useDutyplanTable();

#echo "hello, this is b4 class SegRequestSked mode=$mode <br>";
require_once($root_path.'include/care_api_classes/class_request_sked.php');
$sked_obj=new SegRequestSked;


if ($pmonth=='') $pmonth=date('n');
if ($pyear=='') $pyear=date('Y');

/* Establish db connection */
if(!isset($db)||!$db) include($root_path.'include/inc_db_makelink.php');
if($dblink_ok)
	{	
		if($mode=='save')
		{
					
					$arr_1_txt=array();
					$arr_2_txt=array();
					$arr_1_pnr=array();
					$arr_2_pnr=array();

					for($i=0;$i<$maxelement;$i++)
					{
						$tdx="ha".$i;
						$ddx="hr".$i;
						$tdx2="fa".$i;  # burn added: Sept 13, 2006
						$ddx2="fr".$i;  # burn added: Sept 13, 2006
						$ax="a".$i;
						$rx="r".$i;
						
						if(!empty($$ax)) $arr_1_txt[$ax]=$$ax;
						if(!empty($$rx)) $arr_2_txt[$rx]=$$rx;
						if(!empty($$tdx)) {
						   $arr_1_pnr[$tdx]=$$tdx;
						   $arr_1_pnr[$tdx2]=$$tdx2;   # burn added: Sept 13, 2006
						}
						if(!empty($$ddx)){
						   $arr_2_pnr[$ddx]=$$ddx;
						   $arr_2_pnr[$ddx2]=$$ddx2;   # burn added: Sept 13, 2006
						}
						
					}
					
					$ref_buffer=array();
					// Serialize the data
					$ref_buffer['duty_1_txt']=serialize($arr_1_txt);
					$ref_buffer['duty_2_txt']=serialize($arr_2_txt);
					$ref_buffer['duty_1_pnr']=serialize($arr_1_pnr);
					$ref_buffer['duty_2_pnr']=serialize($arr_2_pnr);
					
					$ref_buffer['dept_nr']=$dept_nr;
					$ref_buffer['role_nr']=15;
					$ref_buffer['year']=$pyear;
					$ref_buffer['month']=$pmonth;
					$ref_buffer['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];

					if($dpoc_nr=$pers_obj->DOCDutyplanExists($dept_nr,$pyear,$pmonth)){
						$ref_buffer['history']=$pers_obj->ConcatHistory("Update: ".date('Y-m-d H:i:s')." = ".$HTTP_SESSION_VARS['sess_user_name']."\n");
						$ref_buffer['modify_time']=date('YmdHis');
						// Point to the internal data array
						$pers_obj->setDataArray($ref_buffer);
															
						if($pers_obj->updateDataFromInternalArray($dpoc_nr)){

							# Remove the cache plan
							if(date('Yn')=="$pyear$pmonth"){
								$pers_obj->deleteDBCache('DOCS_'.date('Y-m-d')); 
							}
							header("location:$thisfile?sid=$sid&lang=$lang&saved=1&dept_nr=$dept_nr&pyear=$pyear&pmonth=$pmonth&retpath=$retpath");
							exit;
						}else echo "<p>".$pers_obj->getLastQuery."<p>$LDDbNoSave"; 
					} // else create new entry
					else
					{
						$ref_buffer['history']="Create: ".date('Y-m-d H:i:s')." = ".$HTTP_SESSION_VARS['sess_user_name']."\n";
						$ref_buffer['create_id']=$HTTP_SESSION_VARS['sess_user_name'];
						$ref_buffer['create_time']=date('YmdHis');
						// Point to the internal data array
						$pers_obj->setDataArray($ref_buffer);

						//echo "create";

							if($pers_obj->insertDataFromInternalArray()){
								# Remove the cache plan
								if(date('Yn')=="$pyear$pmonth"){
									$pers_obj->deleteDBCache('DOCS_'.date('Y-m-d'));
								}
								header("location:$thisfile?sid=$sid&lang=$lang&saved=1&dept_nr=$dept_nr&pyear=$pyear&pmonth=$pmonth&retpath=$retpath");
								exit;
							}else{
								echo "<p>".$pers_obj->getLastQuery."<p>$LDDbNoSave";
							} 
					}//end of else
						
		 }// end of if(mode==save)
		 else
		 {
		 	if($dutyplan=&$pers_obj->getDOCDutyplan($dept_nr,$pyear,$pmonth)){
			
				$aelems=unserialize($dutyplan['duty_1_txt']);
				$relems=unserialize($dutyplan['duty_2_txt']);
				$a_pnr=unserialize($dutyplan['duty_1_pnr']);
				$r_pnr=unserialize($dutyplan['duty_2_pnr']);
			}
	 	}
}
  else { echo "$LDDbNoLink<br>"; } 


$maxdays=date("t",mktime(0,0,0,$pmonth,1,$pyear));

$firstday=date("w",mktime(0,0,0,$pmonth,1,$pyear));

function makefwdpath($path,$dpt,$mo,$yr,$saved)
{
	if ($path==1)
	{	
		$fwdpath='doctors-dienstplan.php?';
		if($saved!="1") 
		{  
			if ($mo==1) {$mo=12; $yr--;}
				else $mo--;
		}
		return $fwdpath.'dept='.$dpt.'&pmonth='.$mo.'&pyear='.$yr;
	}
	else return "doctors-dienstplan-checkpoint.php";
}

# Prepare page title
 $sTitle = "$LDMakeDutyPlan :: ";
 $LDvar=$dept_obj->LDvar();
 if(isset($$LDvar)&&$$LDvar) $sTitle = $sTitle.$$LDvar;
   else $sTitle = $sTitle.$dept_obj->FormalName();

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

# Title in toolbar
 $smarty->assign('sToolbarTitle',$sTitle);

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('docs_dutyplan_edit.php','$mode','$rows')");

# href for return button
 $smarty->assign('pbBack','javascript:history.back();killchild();');

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Body onLoad javascript
 $smarty->assign('sOnLoadJs','onUnload="killchild()"');

 # Window bar title
 $smarty->assign('sWindowTitle',$sTitle);

 # Collect extra javascript

 ob_start();
?>

<script language="javascript">

  var urlholder;
  var infowinflag=0;

function popselect(elem,mode)
{
	w=window.screen.width;
	h=window.screen.height;
	ww=300;
	wh=500;
	var tmonth=document.dienstplan.month.value;
	var tyear=document.dienstplan.jahr.value;
	urlholder="doctors-dienstplan-poppersonselect.php?elemid="+elem + "&dept_nr=<?php echo $dept_nr ?>&month="+tmonth+"&year="+tyear+ "&mode=" + mode + "&retpath=<?php echo $retpath ?>&user=<?php echo $ck_doctors_dienstplan_user."&lang=$lang&sid=$sid"; ?>";
	
	popselectwin=window.open(urlholder,"pop","width=" + ww + ",height=" + wh + ",menubar=no,resizable=yes,scrollbars=yes,dependent=yes");
	window.popselectwin.moveTo((w/2)+80,(h/2)-(wh/2));
}

function popselectDoctor(elem,mode)
{
	w=window.screen.width;
	h=window.screen.height;
	ww=300;
	wh=500;
	var tmonth=document.dienstplan.month.value;
	var tyear=document.dienstplan.jahr.value;
	urlholder="doctors-dienstplan-poppersonselectDoctor.php?elemid="+elem + "&dept_nr=<?php echo $dept_nr ?>&month="+tmonth+"&year="+tyear+ "&mode=" + mode + "&retpath=<?php echo $retpath ?>&user=<?php echo $ck_doctors_dienstplan_user."&lang=$lang&sid=$sid"; ?>";
	
	popselectwin=window.open(urlholder,"pop","width=" + ww + ",height=" + wh + ",menubar=no,resizable=yes,scrollbars=yes,dependent=yes");
	window.popselectwin.moveTo((w/2)+80,(h/2)-(wh/2));
}


function killchild()
{
 if (window.popselectwin) if(!window.popselectwin.closed) window.popselectwin.close();
}

function cal_update()
{
	var filename="doctors-dienstplan-planen.php?<?php echo "sid=$sid&lang=$lang" ?>&retpath=<?php echo $retpath ?>&dept_nr=<?php echo $dept_nr; ?>&pmonth="+document.dienstplan.month.value+"&pyear="+document.dienstplan.jahr.value;
	window.location.replace(filename);
}
</script>
<?php 

 $sTemp=ob_get_contents();
 ob_end_clean();
 $smarty->append('JavaScript',$sTemp);

  $smarty->assign('LDStandbyPerson',$LDDoc1);
 $smarty->assign('LDOnCall',$LDDoc2);

# Prepare the date selectors
$smarty->assign('LDMonth',$LDMonth);
$sBuffer = '<select name="month" size="1" onChange="cal_update()">';

for ($i=1;$i<13;$i++){
	 $sBuffer = $sBuffer.'<option  value="'.$i.'" ';
	 if (($pmonth)==$i)  $sBuffer = $sBuffer.'selected';
	  $sBuffer = $sBuffer.'>'.$monat[$i].'</option>';
	  $sBuffer = $sBuffer."\n";
}
$sBuffer = $sBuffer.'</select>';
$smarty->assign('sMonthSelect',$sBuffer);

$smarty->assign('LDYear',$LDYear);
$sBuffer = '<select name="jahr" size="1" onChange="cal_update()">';

for ($i=2000;$i<2016;$i++){
	 $sBuffer = $sBuffer.'<option  value="'.$i.'" ';
	 if ($pyear==$i) $sBuffer = $sBuffer.'selected';
	 $sBuffer = $sBuffer.'>'.$i.'</option>';
  	 $sBuffer = $sBuffer."\n";
}
$sBuffer = $sBuffer.'</select>';
$smarty->assign('sYearSelect',$sBuffer);

$smarty->assign('sFormAction','action="doctors-dienstplan-planen.php"');

 # collect hidden inputs

 ob_start();
?>

<input type="hidden" name="mode" value="save">
<input type="hidden" name="dept" value="<?php echo $dept_obj->ID(); ?>">
<input type="hidden" name="dept_nr" value="<?php echo $dept_nr; ?>">
<input type="hidden" name="pmonth" value="<?php echo $pmonth; ?>">
<input type="hidden" name="pyear" value="<?php echo $pyear; ?>">
<input type="hidden" name="planid" value="<?php echo $ck_plan; ?>">
<input type="hidden" name="maxelement" value="<?php echo $maxdays; ?>">
<input type="hidden" name="encoder" value="<?php echo $ck_doctors_dienstplan_user; ?>">
<input type="hidden" name="retpath" value="<?php echo $retpath; ?>">
<input type="hidden" name="lang" value="<?php echo $lang; ?>">
<input type="hidden" name="sid" value="<?php echo $sid; ?>">

<?php

 $sTemp=ob_get_contents();
 ob_end_clean();
 $smarty->assign('sHiddenInputs',$sTemp);

 if($saved) $sBuffer = createLDImgSrc($root_path,'close2.gif','0');
 	else $sBuffer = createLDImgSrc($root_path,'cancel.gif','0');

 # Assign control links
$smarty->assign('sSave','<input type="image" '.createLDImgSrc($root_path,'savedisc.gif','0').'"></a>');
$smarty->assign('sClose',"<a href=\"$breakfile\" onUnload=\"killchild()\"><img ".$sBuffer." alt=\"$LDClosePlan\"></a>");

$sTemp='';

   if ($pmonth=='') $pmonth=date('n');
   if ($pyear=='') $pyear=date('Y');
   if ($pday=='') $pday=date('d');
   
   echo " year = $pyear <br>";
   echo " month = $pmonth <br>";
   echo " day = $pday <br>";
   echo " dept_nr = $dept_nr <br>";

   /**
   *   SQL query result. Resulting ADODB record object.
   *   @var object
   */
   $PRequest;
   /**
   *   SQL query result. Resulting ADODB record object.
   *   @var object
   */
   $doctorInfo;
   
   /**
   *   Contains the (1)names, (2)personell number, and 
   *      (3)frequency or the number of xray films interpreted by the Doctors on-call (DOC)
   *   @var array
   */
   $DOCDuty = array();

      /* queue (object) all pending requests sorted accrdng 2 time process */
   $this->PRequest = $sked_obj->getPendingRequest("radio");
    
   $this->DOCDuty = $pers_obj->getDOCDutyplanForTheDay($dept_nr,$pyear,$pmonth,$pday-1);

   print_r(array_values($this->DOCDuty)); echo " <br> ";

function test_print($item2, $key) 
{
    echo "$key. $item2<br />\n";
}

 echo "Before ...: <br>";
 array_walk($this->DOCDuty, 'test_print');
 echo "<br>";

   if ($this->PRequest){
         /* there is/are pending request/s */
      $this->DOCDuty = $pers_obj->getDOCDutyplanForTheDay($dept_nr,$pyear,$pmonth,$pday-1);
         /* list (object) of all DOC/radiologists for the day */
      if (!empty($this->DOCDuty['count'])){
	     echo "this->DOCDuty[count] is not empty <br> <br>";
            /* DOC exists */
         $rowRequest="";
		 $i=0;
         $encoder = $HTTP_SESSION_VARS['sess_user_name'];
		 echo "encoder = ".$encoder." <br> ";
         while ($rowRequest = $this->PRequest->FetchRow())
         {
           /*
           * waiting mode until further notice from DMC...hehe :-)
           */
             $i++;
			 echo "Entry[".$i."] : batch_nr = ".$rowRequest['batch_nr']." encounter_nr=".
			       $rowRequest['encounter_nr']." send_date=".$rowRequest['send_date'].
				   " dept_nr=".$rowRequest['dept_nr']." status=".$rowRequest['status']. 
				   " Lastname=".$rowRequest['name_last']." Firstname=".$rowRequest['name_first'].
				   " DOB=".$rowRequest['date_birth']." sex=".$rowRequest['sex'].
				   " pid=".$rowRequest['pid']." <br> ";
             $doctorID = $sked_obj->getDOC2BeAssigned($this->DOCDuty);
             
#            $sked_obj->assignRequest2DOC($rowRequest,$this->DOCDuty);
#            if ($sked_obj->assignRequest2DOC($rowRequest,$this->DOCDuty,$encoder)){
            if ($sked_obj->assignRequest2DOC_manual($rowRequest,$doctorID,$encoder)){
			   array_walk($this->DOCDuty, 'test_print');		   
               echo "Successful assigning of a request to a DOC! <br> <br>";

#			   $pers_obj->updateFrequencyDOC($this->DOCDuty, $dept_nr, $pyear, $pmonth, $pday-1, $encoder);
                  
				  /* update the 'frequency' field in 'care_dutyplan_oncall' table */
               if ($pers_obj->updateFrequencyDOC($this->DOCDuty, $dept_nr, $pyear, $pmonth, $pday-1, $encoder)){
			      echo "Successful updateFrequencyDOC! <br>";
                     /* this is coppied from lines 94-99 of 'doctors-dienstplan-planen.php' */
                     # Remove the cache plan
/*
                  if (date('Yn')=="$pyear$pmonth"){
                     $pers_obj->deleteDBCache('DOCS_'.date('Y-m-d'));
                  }
                  header("<insert here the new location after the scheduling is done!>");
                  exit;
*/               }else{ /* unsuccessfully updated the 'care_dutyplan_oncall' table */
                    echo "<p>".$pers_obj->getLastQuery."<p>$LDDbNoSave"; 
               }
		    }else{
		       echo "error in assigning request #$i ! <br>";
			   break;
		    }
         }/* end of while loop */   
	  }/* end of if (!empty($this->DOCDuty['count'])) */
   }/* end of if ($this->PRequest) */

	# Preload  common icon images
	$img_male=createComIcon($root_path,'spm.gif','0','',TRUE);
	$img_female=createComIcon($root_path,'spf.gif','0','',TRUE);
	$tbg= 'class="adm_list_titlebar"';

   echo "<!-- \n";
?>
<form name="dienstplanAssignDoctor" action="doctors-dienstplan-planen.php" method="post">
<table border=0 cellpadding=2 cellspacing=1> 
	<tr bgcolor="#abcdef">				

      <td <?php echo $tbg; ?>><b>
	  <?php echo "No.";  ?></b></td>
      <td <?php echo $tbg; ?>><b>
	  <?php echo "Batch No.";  ?></b></td>
      <td <?php echo $tbg; ?>><b>
	  <?php echo "Date Send";  ?></b></td>
      <td <?php echo $tbg; ?>><b>
	  <?php echo "Encounter No.";  ?></b></td>
      <td <?php echo $tbg; ?>><b>
	  <?php echo "Patient No.";  ?></b></td>
      <td <?php echo $tbg; ?>><b>
	  <?php echo "Sex";  ?></b></td>
      <td <?php echo $tbg; ?>><b>
	  <?php echo "Family Name";  ?></b></td>
      <td <?php echo $tbg; ?>><b>
	  <?php echo "Name"; ?></b></td>
      <td <?php echo $tbg; ?>><b>
	  <?php echo "Birthdate"; ?></b></td>
      <td <?php echo $tbg; ?>><b>
	  <?php echo "Request Status";  ?></b></td>
      <td <?php echo $tbg; ?>><b>
	  <?php echo "Doctor";  ?></b></td>
	</tr>

<?php
	include_once($root_path.'include/inc_date_format_functions.php');

      /* queue (object) all pending requests sorted accrdng 2 time process */
   $this->PRequest = $sked_obj->getPendingRequest("radio");
   $toggle=0;
   $my_count=1;
   $rowRequest="";
   while ($rowRequest = $this->PRequest->FetchRow())
   {
#      $my_count, $rowRequest['batch_nr'], $rowRequest['send_date'], $rowRequest['encounter_nr'], 
#      $rowRequest['pid'], $rowRequest['sex'], $rowRequest['name_last'], $rowRequest['name_first'], 
#      $rowRequest['date_birth'], $rowRequest['status'], doctor??

      echo "\n <tr class=";
	  if($toggle) { echo '"wardlistrow2">'; $toggle=0;} 
	  else {echo '"wardlistrow1">'; $toggle=1;};
	  echo "<td>&nbsp;".$my_count."&nbsp;</td> \n";
	  echo "<td>&nbsp;".$rowRequest['batch_nr']."</td> \n";
	  echo "<td>&nbsp;".formatDate2Local($rowRequest['send_date'],$date_format)."</td> \n";
	  echo "<td>&nbsp;".$rowRequest['encounter_nr']."</td> \n";
	  echo "<td>&nbsp;".$rowRequest['pid']."</td> \n";
	  echo "<td>";
            switch($rowRequest['sex']){
               case 'f': echo '<img '.$img_female.'>'; break;
               case 'm': echo '<img '.$img_male.'>'; break;
               default: echo '&nbsp;'; break;
            }	
	  echo "</td> \n";
	  echo "<td>&nbsp;".$rowRequest['name_last']."</td> \n";
	  echo "<td>&nbsp;".$rowRequest['name_first']."</td> \n";
	  echo "<td>&nbsp;".formatDate2Local($rowRequest['date_birth'],$date_format)."</td> \n";
	  echo "<td>&nbsp;".$rowRequest['status']."</td> \n";
	  echo '<td>&nbsp; ';
         /* get the assign doctor, if any */
#	  $doctorInfo="";
      $doctorInfo;
      if ($this->doctorInfo = $sked_obj->getDoctorName($rowRequest['batch_nr'])){
		 /* personell_nr, name_last, name_first, title, sex, pid */
		 $temp_row = $this->doctorInfo->FetchRow();
		 #array_walk($temp_row, 'test_print');
	     echo ' <input type="hidden" name="docID_p'.$my_count.'" value="'.$temp_row['personell_nr'].'"> 
		      <input type="text" size="50" name="p'.$my_count.'" onFocus=this.select() value="'.$temp_row['title'].'&nbsp;'.$temp_row['name_first'].'&nbsp;'.$temp_row['name_last'].'"> 
              <a href="javascript:popselectDoctor(\''.$my_count.'\',\'p\')"> 
	          <button onclick="javascript:popselectDoctor(\''.$my_count.'\',\'p\')"><img '.createComIcon($root_path,'patdata.gif','0').' alt="'.$LDClk2Plan.'"></button></a> ';
      }
      else{
	     echo ' <input type="hidden" name="docID_p'.$my_count.'" value=""> 
		      <input type="text" size="50" name="p'.$my_count.'" onFocus=this.select() value=""> 
              <a href="javascript:popselectDoctor(\''.$my_count.'\',\'p\')"> 
	          <button onclick="javascript:popselectDoctor(\''.$my_count.'\',\'p\')"><img '.createComIcon($root_path,'patdata.gif','0').' alt="'.$LDClk2Plan.'"></button></a> ';
      }
	  echo '</td> \n';
      echo "</tr> \n";
	  $my_count++;
   }/* end of while loop */

   echo "</table> \n";
   echo "</form> \n";
   echo "--> \n";


for ($i=1,$n=0,$wd=$firstday;$i<=$maxdays;$i++,$n++,$wd++)
{
	switch ($wd){
		//case 6: $backcolor="bgcolor=#ffffcc";break;
		//case 0: $backcolor="bgcolor=#ffff00";break;
		//default: $backcolor="bgcolor=white";
		case 6: $smarty->assign('sRowClass','class="saturday"');break;
		case 0: $smarty->assign('sRowClass','class="sunday"');break;
		default: $smarty->assign('sRowClass','class="weekday"');
		}

	$smarty->assign('iDayNr',$i);
	$smarty->assign('LDShortDay',$LDShortDay[$wd]);

	if ($aelems['a'.$n]==""){
	   $smarty->assign('sIcon1','<img '.createComIcon($root_path,'warn.gif','0').'>');
	   $frequency = 0;   # burn added: Sept 13, 2006
	}else{
	   $smarty->assign('sIcon1','<img '.createComIcon($root_path,'mans-gr.gif','0').'>');
	   $frequency = $a_pnr['fa'.$n];   # burn added: Sept 13, 2006
       echo " aelem[a".$n."] = ". $aelems['a'.$n]. " <br>" ;
       echo " a_pnr[fa".$n."] = ". $a_pnr['fa'.$n]. " <br> ";
	}
	$smarty->assign('sInput1','<input type="hidden" name="ha'.$n.'" value="'.$a_pnr['ha'.$n].'">
	    <input type="hidden" name="fa'.$n.'" value="'.$frequency.'">
		<input type="text" name="a'.$n.'" size="15" onFocus=this.select() value="'.$aelems['a'.$n].'">');   # burn modified: Sept 13, 2006

	$smarty->assign('sPopWin1','<a href="javascript:popselect(\''.$n.'\',\'a\')">
	<button onclick="javascript:popselect(\''.$n.'\',\'a\')"><img '.createComIcon($root_path,'patdata.gif','0').' alt="'.$LDClk2Plan.'"></button></a>');

	if ($relems['r'.$n]==""){
	   $smarty->assign('sIcon2','<img '.createComIcon($root_path,'warn.gif','0').'>');
	   $frequency = 0;   # burn added: Sept 13, 2006
	}else{
	   $smarty->assign('sIcon2','<img '.createComIcon($root_path,'mans-red.gif','0').'>');
	   $frequency = $r_pnr['fr'.$n];   # burn added: Sept 13, 2006
       echo " relem[r".$n."] = ". $relems['r'.$n]. " <br>" ;
       echo " r_pnr[fr".$n."] = ". $r_pnr['fr'.$n]. " <br> ";
	}
	$smarty->assign('sInput2','<input type="hidden" name="hr'.$n.'" value="'.$r_pnr['hr'.$n].'">
   	    <input type="hidden" name="fr'.$n.'" value="'.$frequency.'">
		<input type="text" size="15" name="r'.$n.'" onFocus=this.select() value="'.$relems['r'.$n].'">');   # burn modified: Sept 13, 2006

	$smarty->assign('sPopWin2','<a href="javascript:popselect(\''.$n.'\',\'r\')">
	<button onclick="javascript:popselect(\''.$n.'\',\'r\')"><img '.createComIcon($root_path,'patdata.gif','0').' alt="'.$LDClk2Plan.'"></button></a>');
	if($wd==6) $wd=-1;
	
	# Buffer each row and collect to a string
	
	ob_start();
		$smarty->display('common/duty_plan_entry_row.tpl');
		$sTemp = $sTemp.ob_get_contents();
	ob_end_clean();
}

# Assign the duty entry rows to the subframe template

 $smarty->assign('sDutyRows',$sTemp);


$smarty->assign('sMainBlockIncludeFile','common/duty_plan_entry_frame.tpl');
 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');

?>
