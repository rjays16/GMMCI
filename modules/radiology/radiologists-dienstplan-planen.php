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
#$local_user='ck_doctors_dienstplan_user';
$local_user='ck_lab_user';
#echo"radiologists-dienstplan-planen.php : 1 _GET : <br> \n"; print_r($_GET); echo" <br> \n";
require_once($root_path.'include/inc_front_chain_lang.php');
/*
	# burn commented : July 18, 2007
if(!isset($dept_nr)||!$dept_nr){
	header('Location:doctors-select-dept.php'.URL_REDIRECT_APPEND.'&retpath='.$retpath);
	exit;
}
*/
//$db->debug=1;

#echo"radiologists-dienstplan-planen.php : 2 _GET : <br> \n"; print_r($_GET); echo" <br> \n";

$thisfile=basename(__FILE__);
#$breakfile="doctors-dienstplan.php".URL_APPEND."&dept_nr=$dept_nr&pmonth=$pmonth&pyear=$pyear&retpath=$retpath";	# burn commented : July 18, 2007
#$breakfile=$root_path."modules/radiology/radiologists-dienstplan.php".URL_APPEND."&dept_nr=$dept_nr&pmonth=$pmonth&pyear=$pyear&retpath=$retpath";	# burn added : July 19, 2007
$breakfile="radiolog.php".URL_APPEND;
#echo"radiologists-dienstplan-planen.php : breakfile = '".$breakfile."' <br> \n";

require_once($root_path.'include/care_api_classes/class_department.php');
$dept_obj=new Department;
$dept_obj->preloadDept($dept_nr);

require_once($root_path.'include/care_api_classes/class_personell.php');
$pers_obj=new Personell;
$pers_obj->useDutyplanTable();

#echo "hello, this is b4 class SegRequestSked mode=$mode <br>";
require_once($root_path.'include/care_api_classes/class_request_sked.php');
$sked_obj=new SegRequestSked;

	# the maximum resident-in-charged that be be assigned in a month
$maxDoctors=3;   # burn added : July 19, 2007

if ($pmonth=='') $pmonth=date('n');
if ($pyear=='') $pyear=date('Y');

/* Establish db connection */
if(!isset($db)||!$db) include($root_path.'include/inc_db_makelink.php');
if($dblink_ok)
	{	
		if($mode=='save')
		{

					$list = $dept_obj->getAncestorChildrenDept($dept_nr);
					#echo "sql = ".$dept_obj->sql;
#						echo "list = '".$list."'<br> \n";
					$radiology_sections = split(",",$list);
#						echo "radiology_sections : <br> \n"; print_r($radiology_sections); echo "<br> \n";

#echo "_POST : <br> \n"; print_r($_POST); echo "<br> \n";

					$arr_radio_txt=array();
					
					foreach($radiology_sections as $r_section_nr){
						for($i=0;$i<$maxDoctors;$i++){
							$columnLetter = chr(65+$i);
							$doc_id="ID_doc".$columnLetter.$r_section_nr;
							$doc_name="name_doc".$columnLetter.$r_section_nr;
/*
echo "i = '".$i."'; columnLetter = '".$columnLetter."'; ";
echo " doc_id = '".$doc_id."'; _POST[doc_id] = '".$_POST[$doc_id]."';";
echo " doc_name = '".$doc_name."'; _POST[doc_name] = '".$_POST[$doc_name]."' <br> \n";
*/							
							if( (!empty($_POST[$doc_id])) && (!empty($_POST[$doc_name])) ) 
								$arr_radio_txt[$doc_id]=$_POST[$doc_id];
						}						
					}

#echo "arr_radio_txt = '".$arr_radio_txt."' <br> \n";
#echo "arr_radio_txt : <br> \n"; print_r($arr_radio_txt); echo " <br> \n";
#$temp=serialize($arr_radio_txt);
#echo "temp = '".$temp."' <br> \n";
#exit();

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
#					$ref_buffer['duty_1_txt']=serialize($arr_1_txt);   # burn commented : July 19, 2007
					$ref_buffer['duty_1_txt']=serialize($arr_radio_txt);
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
			
				$radioElems=unserialize($dutyplan['duty_1_txt']);   # burn added : July 19, 2007

				$aelems=unserialize($dutyplan['duty_1_txt']);
				$relems=unserialize($dutyplan['duty_2_txt']);
				$a_pnr=unserialize($dutyplan['duty_1_pnr']);
				$r_pnr=unserialize($dutyplan['duty_2_pnr']);
			}
	 	}
}
  else { echo "$LDDbNoLink<br>"; } 
#echo "sql = ".$pers_obj->sql;

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

function popselectRadiologists(elem,mode)
{
	w=window.screen.width;
	h=window.screen.height;
	ww=300;
	wh=500;
	var tmonth=document.dienstplan.month.value;
	var tyear=document.dienstplan.jahr.value;
	urlholder="radiologists-dienstplan-poppersonselect.php?elemid="+elem + "&dept_nr=<?php echo $dept_nr ?>&month="+tmonth+"&year="+tyear+ "&mode=" + mode + "&retpath=<?php echo $retpath ?>&user=<?php echo $ck_doctors_dienstplan_user."&lang=$lang&sid=$sid"; ?>";
	
	popselectwin=window.open(urlholder,"pop","width=" + ww + ",height=" + wh + ",menubar=no,resizable=yes,scrollbars=yes,dependent=yes");
	window.popselectwin.moveTo((w/2)+80,(h/2)-(wh/2));
}


function killchild()
{
 if (window.popselectwin) if(!window.popselectwin.closed) window.popselectwin.close();
}

function cal_update()
{
	var filename="radiologists-dienstplan-planen.php?<?php echo "sid=$sid&lang=$lang" ?>&retpath=<?php echo $retpath ?>&dept_nr=<?php echo $dept_nr; ?>&pmonth="+document.dienstplan.month.value+"&pyear="+document.dienstplan.jahr.value;
	window.location.replace(filename);
}
</script>
<?php 

 $sTemp=ob_get_contents();
 ob_end_clean();
 $smarty->append('JavaScript',$sTemp);

# $smarty->assign('LDStandbyPerson',$LDDoc1);   # burn commented : July 18, 2007
# $smarty->assign('LDOnCall',$LDDoc2);   # burn commented : July 18, 2007
 $smarty->assign('LDStandbyPerson',"Resident-in-Charge 1");   # burn added : July 18, 2007
 $smarty->assign('LDOnCall',"Resident-in-Charge 2");   # burn added : July 18, 2007

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

$smarty->assign('sFormAction','action="radiologists-dienstplan-planen.php"');

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
<input type="hidden" name="maxDoctors" value="<?php echo $maxDoctors; ?>">
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
$smarty->assign('sSave','<input type="image" '.createLDImgSrc($root_path,'savedisc.gif','0').'></a>');
$smarty->assign('sClose',"<a href=\"$breakfile\" onUnload=\"killchild()\"><img ".$sBuffer." alt=\"$LDClosePlan\"></a>");

$sTemp='';

   if ($pmonth=='') $pmonth=date('n');
   if ($pyear=='') $pyear=date('Y');
   if ($pday=='') $pday=date('d');
   
#   echo " year = $pyear <br>";
#   echo " month = $pmonth <br>";
#   echo " day = $pday <br>";
#   echo " dept_nr = $dept_nr <br>";

/*
	$list = $dept_obj->getAncestorChildrenDept($dept_nr);   # burn added : July 19, 2007
echo "list = '".$list."'<br> \n";
	$sub_depts = split(",",$list);
echo "sub_depts = '".$sub_depts."'<br> \n";
echo "sub_depts : <br> \n"; print_r($sub_depts); echo "<br> \n";
foreach($sub_depts as $sub_dept_nr){
	echo "sub_dept_nr = '".$sub_dept_nr."' <br>\n";
	$temp01 = $dept_obj->getDeptAllInfo($sub_dept_nr);
	echo "dept_obj->sql = '".$dept_obj->sql."' <br>\n";
}
$pers_obj->loadPersonellData('100097');
*/
#commented by VAN 03-24-08
/*
if($pers_obj->loadPersonellData('100297')){
	echo " pers_obj->sql = '".$pers_obj->sql."' <br> \n";
	echo "pers_obj->personell_data = '".$pers_obj->personell_data."' <br> \n";
	echo "pers_obj->personell_data : <br> \n"; print_r($pers_obj->personell_data); echo " <br> \n";
}
*/
#echo $pers_obj->sql;
	# burn added : July 18, 2007
#$radiology_sections = array('General Radiography','Ultrasound','Special Procedures','Computed Tomography');
$smarty->assign('segDutyPlanRadiologyMode',TRUE);

	$list = $dept_obj->getAncestorChildrenDept($dept_nr);   # burn added : July 19, 2007
#echo "list = '".$list."'<br> \n";
	$radiology_sections = split(",",$list);
#echo "radiology_sections : <br> \n"; print_r($radiology_sections); echo "<br> \n";

foreach($radiology_sections as $r_section_nr){
	$r_sub_section = $dept_obj->getDeptAllInfo($r_section_nr);
	ob_start();
		echo "
					<tr bgcolor='#FFFFFF' style='font-size:16px'>
						<td colspan='6' bgcolor='#88B9EE'>
						<span style='font-family: Arial, Helvetica, sans-serif;font-weight: bold;color: #FFFFFF;'>
							".$r_sub_section['name_formal']."
						</span></td>
					</tr>
		";
		echo "
					<tr class='submenu2_titlebar' style='font-size:16px' align='center'>
						<td colspan='2' bgcolor='#C3DCF8'>
							<span style='font-family: Arial, Helvetica, sans-serif; font-weight: bold; color: #003366; font-size: 12px;'>
								Resident-in-Charge 1
							</span>
						</td>
						<td colspan='2' bgcolor='#C3DCF8'>
							<span style='font-family: Arial, Helvetica, sans-serif; font-weight: bold; color: #003366; font-size: 12px;'>
								Resident-in-Charge 2
							</span>
						</td>
						<td colspan='2' bgcolor='#C3DCF8'>
							<span style='font-family: Arial, Helvetica, sans-serif; font-weight: bold; color: #003366; font-size: 12px;'>
								Resident-in-Charge 3
							</span>
						</td>

					</tr>
		";
		$sTemp = $sTemp.ob_get_contents();
	ob_end_clean();

	if ($pers_obj->loadPersonellData($radioElems["ID_docA".$r_section_nr])){
		$f_name = $pers_obj->personell_data['name_first'];
		$l_name = $pers_obj->personell_data['name_last'];
	   $smarty->assign('sIcon1','<img '.createComIcon($root_path,'mans-gr.gif','0').'>');
		$smarty->assign('sInput1','<input type="hidden" name="ID_docA'.$r_section_nr.'" value="'.$radioElems["ID_docA".$r_section_nr].'"> 
					<input type="text" size="25" name="name_docA'.$r_section_nr.'" onFocus=this.select() value="'.$l_name.', '.$f_name.'">'); 
	}else{
	   $smarty->assign('sIcon1','<img '.createComIcon($root_path,'warn.gif','0').'>');
		$smarty->assign('sInput1','<input type="hidden" name="ID_docA'.$r_section_nr.'" value=""> 
					<input type="text" size="25" name="name_docA'.$r_section_nr.'" onFocus=this.select() value="">'); 	
	}
/*
	     echo ' <input type="hidden" name="ID_docA'.$my_count.'" value="'.$temp_row['personell_nr'].'"> 
		      <input type="text" size="50" name="docA'.$my_count.'" onFocus=this.select() value="'.$temp_row['title'].'&nbsp;'.$temp_row['name_first'].'&nbsp;'.$temp_row['name_last'].'"> 
              <a href="javascript:popselectRadiologists(\''.$my_count.'\',\'docA\')"> 
	          <button onclick="javascript:popselectRadiologists(\''.$my_count.'\',\'docA\')"><img '.createComIcon($root_path,'patdata.gif','0').' alt="'.$LDClk2Plan.'"></button></a> ';
*/

	$smarty->assign('sPopWin1','<a href="javascript:popselectRadiologists(\''.$r_section_nr.'\',\'docA\')"> 
	          <button onclick="javascript:popselectRadiologists(\''.$r_section_nr.'\',\'docA\')"><img '.createComIcon($root_path,'patdata.gif','0').' alt="'.$LDClk2Plan.'"></button></a>');

	if ($pers_obj->loadPersonellData($radioElems["ID_docB".$r_section_nr])){
		$f_name = $pers_obj->personell_data['name_first'];
		$l_name = $pers_obj->personell_data['name_last'];
	   $smarty->assign('sIcon2','<img '.createComIcon($root_path,'mans-red.gif','0').'>');
		$smarty->assign('sInput2','<input type="hidden" name="ID_docB'.$r_section_nr.'" value="'.$radioElems["ID_docB".$r_section_nr].'"> 
			      <input type="text" size="25" name="name_docB'.$r_section_nr.'" onFocus=this.select() value="'.$l_name.', '.$f_name.'">');
	}else{
	   $smarty->assign('sIcon2','<img '.createComIcon($root_path,'warn.gif','0').'>');
		$smarty->assign('sInput2','<input type="hidden" name="ID_docB'.$r_section_nr.'" value=""> 
			      <input type="text" size="25" name="name_docB'.$r_section_nr.'" onFocus=this.select() value="">');	
	}

	$smarty->assign('sPopWin2','<a href="javascript:popselectRadiologists(\''.$r_section_nr.'\',\'docB\')"> 
	          <button onclick="javascript:popselectRadiologists(\''.$r_section_nr.'\',\'docB\')"><img '.createComIcon($root_path,'patdata.gif','0').' alt="'.$LDClk2Plan.'"></button></a>');

	if ($pers_obj->loadPersonellData($radioElems["ID_docC".$r_section_nr])){
		$f_name = $pers_obj->personell_data['name_first'];
		$l_name = $pers_obj->personell_data['name_last'];
	   $smarty->assign('sIcon3','<img '.createComIcon($root_path,'mans-red.gif','0').'>');
		$smarty->assign('sInput3','<input type="hidden" name="ID_docC'.$r_section_nr.'" value="'.$radioElems["ID_docC".$r_section_nr].'"> 
					<input type="text" size="25" name="name_docC'.$r_section_nr.'" onFocus=this.select() value="'.$l_name.', '.$f_name.'">'); 
	}else{
	   $smarty->assign('sIcon3','<img '.createComIcon($root_path,'warn.gif','0').'>');
		$smarty->assign('sInput3','<input type="hidden" name="ID_docC'.$r_section_nr.'" value=""> 
					<input type="text" size="25" name="name_docC'.$r_section_nr.'" onFocus=this.select() value="">'); 	
	}
	
	$smarty->assign('sPopWin3','<a href="javascript:popselectRadiologists(\''.$r_section_nr.'\',\'docC\')"> 
	          <button onclick="javascript:popselectRadiologists(\''.$r_section_nr.'\',\'docC\')"><img '.createComIcon($root_path,'patdata.gif','0').' alt="'.$LDClk2Plan.'"></button></a>');
	
	# Buffer each row and collect to a string
	ob_start();
		$smarty->display('common/duty_plan_entry_row_radio.tpl');
		echo"
							<tr bgcolor='#FFFFFF'>
								<td colspan='6'>&nbsp;</td>
							</tr>
		";
		$sTemp = $sTemp.ob_get_contents();
	ob_end_clean();

}# end of foreach loop

# Assign the duty entry rows to the subframe template

 $smarty->assign('sDutyRows',$sTemp);

#edited by VAN 03-24-08
#$smarty->assign('sMainBlockIncludeFile','common/duty_plan_entry_frame.tpl');
$smarty->assign('sMainBlockIncludeFile','radiology/duty_plan_entry_frame.tpl');

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');

?>
