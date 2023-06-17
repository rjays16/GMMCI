<?php
	global $ptype, $allow_patient_register, $allow_newborn_register, $allow_er_user, $allow_opd_user, $allow_ipd_user, $allow_medocs_user, $allow_update;
	
	if ($_GET['ptype'])
		$ptype = $_GET['ptype'];
	elseif ($HTTP_SESSION_VARS['ptype'])
		$ptype = $HTTP_SESSION_VARS['ptype'];
?>
<a href="patient_register_show.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&update=1&target=<?php echo $target ?>&ptype=<?=$ptype?>"><img 
<?php echo createLDImgSrc($root_path,'reg_data.gif','0','absmiddle') ?>></a>

<?php


//if($current_encounter){ #---------commented 03-14-07-----
if(($current_encounter)&&($dept_belong['id']=="Admission")){
	
?>
<!--<a href="aufnahme_daten_zeigen.php<?php echo URL_APPEND ?>&encounter_nr=<?php echo $current_encounter ?>&origin=patreg_reg"><img <?php echo createLDImgSrc($root_path,'admission_data.gif','0','absmiddle') ?>></a>-->
<?php
}elseif(!$death_date||$death_date=='0000-00-00'){
?>
<!--- commented 03-14-07 by vanessa -->
<!--
<a href="<?php echo $admissionfile ?>&pid=<?php echo $pid ?>&origin=patreg_reg&encounter_class_nr=1"><img <?php echo createLDImgSrc($root_path,'admit_inpatient.gif','0','absmiddle') ?>></a>
<a href="<?php echo $admissionfile ?>&pid=<?php echo $pid ?>&origin=patreg_reg&encounter_class_nr=2"><img <?php echo createLDImgSrc($root_path,'admit_outpatient.gif','0','absmiddle') ?>></a>
-->
<?php
}
?>

<form action="patient_register.php" method=post>
<?php
#------------edited by vanessa 03-26-07-------------
include_once($root_path.'include/care_api_classes/class_department.php');

$dept_obj=new Department;
#$dept_belong = $dept_obj->getUserDeptInfo($HTTP_SESSION_VARS['sess_user_name']);
#echo "dept_belong = ".$dept_belong['id'];
	if (!empty($HTTP_SESSION_VARS['sess_login_userid']))
		$seg_user_name = $HTTP_SESSION_VARS['sess_login_userid'];
	else
		$seg_user_name = $HTTP_SESSION_VARS['sess_temp_userid'];
	$dept_belong = $dept_obj->getUserDeptInfo($seg_user_name);


#if (($dept_belong['id']=="OPD-Triage")||($dept_belong['id']=="ER")||(($dept_belong['id']=="HRD"))){

if (($allow_opd_user)||($allow_er_user)||(($allow_hrd_user))){

?>
	<!--<input type=submit value="<?php echo $LDNewForm ?>">-->
<?php } ?>	
<input type=hidden name="sid" value=<?php echo $sid; ?>>
<input type=hidden name="lang" value="<?php echo $lang; ?>">
</form>
