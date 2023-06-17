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
//define('LANG_FILE','doctors.php');
if($HTTP_SESSION_VARS['sess_user_origin']=='personell_admin'){
	$local_user='aufnahme_user';
}else{
	$local_user='ck_doctors_dienstplan_user';
}
require_once($root_path.'include/inc_front_chain_lang.php');

//$db->debug=true;

require_once($root_path.'include/care_api_classes/class_personell.php');
#require_once($root_path.'include/care_api_classes/class_department.php');
$pers_obj=new Personell;
#$dept_obj=new Department;
$pers_obj->useAssignmentTable();
$data=array();

#$dept_cur = $dept_obj->FormalName();

#echo "van -".$nr;
#exit();

if($mode!='delete'){
	
   $data['personell_nr']=$nr;
	
	#-----------add 02-24-07-----------
	$role_nr = $pers_obj->getRole_type($nr, $job_fxn);
	$loc_type = $pers_obj->getDeptInfo($dept_nr);
	#----------------------------------
	
	$data['role_nr'] = $role_nr['nr'];      //17; // 17 = doctor (role person)  -- edited 02-24-07
	#$data['role_nr'] = 17;
	$data['location_type_nr'] = $loc_type['type'];  // 1 = dept (location type)  --- edited 02-24-07
	#$data['location_type_nr'] = 1;
	$data['location_nr']=$dept_nr;
	$data['date_start']=date('Y-m-d');
}

$data['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];
#echo "mode = $mode <br>";
switch($mode){

	case 'save':
					#echo "save"; exit();
	          		$data['history']="Add: ".date('Y-m-d H:i:s')." = ".$HTTP_SESSION_VARS['sess_user_name']."\n";
					$data['create_id']=$HTTP_SESSION_VARS['sess_user_name'];
					$data['create_time']=date('YmdHis');
					$pers_obj->setDataArray($data);
				  	if(!$pers_obj->insertDataFromInternalArray())  echo "$obj->sql<br>$LDDbNoSave";
					
					break;
	case 'update':
					#echo "update"; exit();
					/*
					#------------------------
					$dept_name = $pers_obj->get_Dept_name($nr);
					$person_name = $pers_obj->get_Person_name($nr);
					$title_name = $person_name['title'];
					$first_name = $person_name['name_first'];
					$second_name = $person_name['name_2'];
					$last_name = $person_name['name_last'];
						
					echo " \n <script type=\"text/javascript\">alert(\"$title_name $first_name $second_name $last_name is already assigned in a $dept_name department!\")</script>";
						
						#---------- confirmation if the personell will be transfer to another department---
					$dept_cur = $pers_obj->get_Dept_cur($dept_nr);
					echo " \n <script type=\"text/javascript\">if (confirm(\"Do you want to transfer $title_name $first_name $second_name $last_name to another department which is $dept_cur?!\")) {updateAssign(1);} else alert(\"false!\")</script>";
					
					#------------------------
					*/
					$data['date_end']='0000-00-00';
					$data['history']=$pers_obj->ConcatHistory("Update: ".date('Y-m-d H:i:s')." = ".$HTTP_SESSION_VARS['sess_user_name']."\n");
					$data['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];
					$data['modify_time']=date('YmdHis');
					#-------------------
					$data['status']=" ";
					$personell_nr = $pers_obj->get_Person_name($nr);
					#echo $pers_obj->sql;
					$assign_nr = $personell_nr['nr'];     
					#-------------------
					$pers_obj->setDataArray($data);
					
					#------------------
					if(!$pers_obj->updateDataFromInternalArray($assign_nr))  echo "$obj->sql<br>$LDDbNoUpdate";
					
					#if(!$pers_obj->updateDataFromInternalArray($item_nr))  echo "$obj->sql<br>$LDDbNoUpdate";
					break;
	case 'delete':
					$data['status']='deleted';
					$data['date_end']=date('Y-m-d');
					$data['history']=$pers_obj->ConcatHistory("Deleted: ".date('Y-m-d H:i:s')." = ".$HTTP_SESSION_VARS['sess_user_name']."\n");
					$data['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];
					$data['modify_time']=date('YmdHis');
					$pers_obj->setDataArray($data);
					if(!$pers_obj->updateDataFromInternalArray($item_nr))  echo "$obj->sql<br>$LDDbNoUpdate";
					break;
}

#header("location:doctors-dienst-personalliste.php".URL_REDIRECT_APPEND."&saved=1&retpath=$retpath&ipath=$ipath&dept_nr=$dept_nr&user_origin=$user_origin&nr=$nr");
header("location:doctors-dienst-personalliste.php".URL_REDIRECT_APPEND."&saved=1&retpath=$retpath&ipath=$ipath&dept_nr=$dept_nr&user_origin=$user_origin&nr=$nr&item_nr=$item_nr");
exit;
?>
