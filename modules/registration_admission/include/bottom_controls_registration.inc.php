<a href="patient_register.php<?php echo URL_APPEND ?>&pid=<?php echo $pid ?>&update=1"><img 
<?php echo createLDImgSrc($root_path,'update_data.gif','0','absmiddle') ?>></a>
<!--- commented 03-14-07 by Vanessa -->
<!--
<a href="<?php echo $admissionfile ?>&pid=<?php echo $pid ?>&origin=patreg_reg&encounter_class_nr=1"><img <?php echo createLDImgSrc($root_path,'admit_inpatient.gif','0','absmiddle') ?>></a>
<a href="<?php echo $admissionfile ?>&pid=<?php echo $pid ?>&origin=patreg_reg&encounter_class_nr=2"><img <?php echo createLDImgSrc($root_path,'admit_outpatient.gif','0','absmiddle') ?>></a>
-->
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


if (($dept_belong['id']=="OPD-Triage")||($dept_belong['id']=="ER")||(($dept_belong['id']=="HRD"))){

?>
	<input type=submit value="<?php echo $LDNewForm ?>">
<?php } ?>	
<input type=hidden name="sid" value=<?php echo $sid; ?>>
<input type=hidden name="lang" value="<?php echo $lang; ?>">
</form>
