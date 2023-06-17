<?php
		/*
		*	sets the list of consulting doctors (consulting_dr_nr)
		*	burn added : May 16, 2007
		*/
	function setDoctorsEROPD($sex='', $age='',$admit_inpatient=0, $dept_nr=0, $personell_nr=0) {
		global $pers_obj, $dept_obj;

		$objResponse = new xajaxResponse();
# $objResponse->addAlert("setDoctorsEROPD : admit_inpatientr ='$admit_inpatient'");
# $objResponse->addAlert("setDoctorsEROPD : dept_nr ='$dept_nr'");
# $objResponse->addAlert("setDoctorsEROPD : personell_nr ='$personell_nr'");
		 $is_accepted = 0;
				if ($dept_nr)
						$deptInfo = $dept_obj->getDeptAllInfo($dept_nr);
				else
						$is_accepted = 0;

				#$objResponse->addAlert($dept_obj->sql);
				$msgforfemale = "The ".mb_strtoupper($deptInfo['name_formal'])." department is for female only..";
				$msgformale = "This ".mb_strtoupper($deptInfo['name_formal'])." department is for male only..";
				$msgforchild = "The ".mb_strtoupper($deptInfo['name_formal'])." department is for children only (0-".$deptInfo['child_age_limit']." yrs old)..";

				if (($deptInfo['for_male_only']==1)&&($deptInfo['for_female_only']==0)&&($sex=='m')){
						$is_accepted = 1;
				}elseif (($deptInfo['for_male_only']==0)&&($deptInfo['for_female_only']==1)&&($sex=='f')){
						$is_accepted = 1;
				}elseif (($deptInfo['for_male_only']==0)&&($deptInfo['for_female_only']==0)){
						$is_accepted = 1;
				}

				$a = explode(' ', $age);
				 if ($a[1] == 'days') {
					 $age = $a[0]/365;
				 }
				 else {
					 $age = $a[0];
				 }
				#$objResponse->alert($deptInfo['for_child_only']." - ".$age);
				if ($deptInfo['for_child_only']==1){
						if ($age > $deptInfo['child_age_limit']){
								$is_accepted = 0;
								$forchild = 1;
						}
				}

				if (($deptInfo['for_male_only']==1)&&($deptInfo['for_female_only']==0)&&($sex=='f')){
						$formale = 1;
						$forfemale = 0;
				}elseif (($deptInfo['for_male_only']==0)&&($deptInfo['for_female_only']==1)&&($sex=='m')){
						$formale = 0;
						$forfemale = 1;
				}

				if ($personell_nr!=0){
						$is_resident_dr = 0;
						$result_resident=$dept_obj->getDeptofDoctor($personell_nr);

						if ($result_resident["is_resident_dr"])
								$is_resident_dr = 1;
				}

				#$objResponse->addAlert($personell_nr);
				#if (($is_accepted)&&(!$is_resident_dr)){
				if ($is_accepted){

								#if ($dept_nr)
								if (($dept_nr)&&(!$is_resident_dr))
							$rs=$pers_obj->getDoctorByDept($dept_nr, $admit_inpatient);
						else
							$rs=$pers_obj->getDoctors($admit_inpatient);

				#		$objResponse->addAlert("setDoctorsEROPD : rs ='$rs'");
				#		$objResponse->addAlert("setDoctorsEROPD : pers_obj->sql ='$pers_obj->sql'");
				#		$objResponse->addAlert("setDoctorsEROPD : admit_inpatient = '$admit_inpatient'");
				#		$objResponse->addAlert("setDoctorsEROPD : pers_obj->count = '$pers_obj->count'");
				#		$objResponse->addAlert("setDoctorsEROPD : personell_nr = '$personell_nr'");

						$objResponse->addScriptCall("ajxClearOptionEROPDDrDept",0); # 2nd arg == 0, reset consulting doctors
						if ($rs) {
							if ($pers_obj->count > 0){
								$objResponse->addScriptCall("ajxAddOptionEROPDDrDept",0,"-Select a Doctor-",0);
							}else{
								#if ($dept_nr)
												if (($dept_nr)&&(!$is_resident_dr))
									$objResponse->addScriptCall("ajxAddOptionEROPDDrDept",0,"-No Doctor Available-",0);
								else
									$objResponse->addScriptCall("ajxAddOptionEROPDDrDept",0,"-Select a Doctor-",0);
							}

							while ($result=$rs->FetchRow()) {
									#$doctor_name = $result["name_first"]." ".$result["name_2"]." ".$result["name_last"];
								#$doctor_name = "Dr. ".ucwords(strtolower($doctor_name));
								if (trim($result["name_middle"]))
									$dot  = ".";

								$doctor_name = trim($result["name_last"]).", ".trim($result["name_first"])." ".substr(trim($result["name_middle"]),0,1).$dot;
								$doctor_name = ucwords(strtolower($doctor_name)).", MD";

								$objResponse->addScriptCall("ajxAddOptionEROPDDrDept",0,$doctor_name,$result["personell_nr"]);
				#				$objResponse->addAlert("setDoctorsER : result['personell_nr'] = '".$result['personell_nr']."'");
							}# end of while loop
							if($personell_nr)
								$objResponse->addScriptCall("ajxSetEROPDDrDept",0,$personell_nr); # 2nd arg == 0, set consulting doctor
												#$objResponse->addAlert("here");
						} else {
							$objResponse->addAlert("setDoctorsEROPD : Error retrieving consulting doctors information...");
						}

								if ($is_resident_dr){
												if($personell_nr)
																$objResponse->addScriptCall("ajxSetEROPDConsultDoctor",$personell_nr);
																#$objResponse->alert($dept_nr);
												if($dept_nr)
														$objResponse->addScriptCall("ajxSetEROPDDepartment",$dept_nr);
								}else{
										if($dept_nr)
												$objResponse->addScriptCall("ajxSetEROPDDepartment",$dept_nr);
								}
						 }else{
									 if ($formale)
												$objResponse->alert($msgformale);
									 elseif ($forfemale)
												$objResponse->alert($msgforfemale);
									 elseif ($forchild)
												$objResponse->alert($msgforchild);

									 $objResponse->addScriptCall("ajxSetEROPDDepartment",0);
									 $objResponse->addScriptCall("ajxSetEROPDDoctor",0,0);

									 #if($dept_nr)
											#$objResponse->addScriptCall("ajxSetEROPDDepartment",$dept_nr);
						 }
		return $objResponse;
	}/* end of function setDoctorsEROPD */

		/*
		*	sets the list of consulting departments (consulting_dept_nr)
		*	burn added : May 24, 2007
		*/
	function setAllDepartmentEROPD($admit_inpatient=0,$dept_nr=0){
		global $dept_obj;

		$objResponse = new xajaxResponse();

		$rs=$dept_obj->getAllOPDMedicalObject($admit_inpatient);

#		$objResponse->addAlert("setAllDepartmentEROPD : dept_obj->sql = '".$dept_obj->sql."'");

		$objResponse->addScriptCall("ajxClearOptionEROPDDrDept",1); # 2nd arg == 1, reset consulting departments
		if ($rs) {
			$objResponse->addScriptCall("ajxAddOptionEROPDDrDept",1,"-Select a Department-",0);
			while ($result=$rs->FetchRow()) {
				 $objResponse->addScriptCall("ajxAddOptionEROPDDrDept",1,$result["name_formal"],$result["nr"]);
			}
			if($dept_nr)
				$objResponse->addScriptCall("ajxSetEROPDDrDept",1,$dept_nr); # 2nd arg == 1, set consulting department
		}
		else {
			$objResponse->addAlert("setAllDepartmentEROPD : Error retrieving consulting departments information...");
		}
		return $objResponse;
	}/* end of function setAllDepartmentEROPD */

		/*
		*	sets the consulting department (consulting_dept_nr) based on the
		*	selected consulting doctor (consulting_dr_nr)
		*	burn added : May 24, 2007
		*/
	function setDepartmentEROPD($personell_nr=0, $sex='', $age='', $encnr=0){
		global $dept_obj;

		$objResponse = new xajaxResponse();
		#$objResponse->addAlert("setDepartmentEROPD");
		#$encnr here is department_nr
		$dept_nr = $encnr;
		if ($personell_nr!=0){
			$result=$dept_obj->getDeptofDoctor($personell_nr);

						if ($result["is_resident_dr"])
								$is_resident_dr = 1;

#			$objResponse->addAlert("setDepartmentEROPD : sql ='".$dept_obj->sql."'");
#			$objResponse->addAlert("setDepartmentEROPD : result ='".$result."'");
#			$objResponse->addAlert("setDepartmentEROPD : result['nr'] ='".$result['nr']."'");
			#$objResponse->addAlert("name_formal = ".$result["name_formal"]." - ".$result["nr"]);
			if ($result) {
								$is_accepted = 0;
								#$objResponse->alert('dept = '.$result["nr"]);
								#$objResponse->alert($is_accepted." - ".$sex." - ".$age);
							 $deptInfo = $dept_obj->getDeptAllInfo($result["nr"]);
								$accepted_age = 0;
							 #if (stristr($age,'years')){
							 if ((stristr($age,'years'))||(stristr($age,'year'))){
										$age = substr($age,0,-5);
										$age = floor($age);

										if ($age <= $deptInfo['child_age_limit'])
												$accepted_age = 1;
										else
												$accepted_age = 0;
							 }else{
										if ($age <= $deptInfo['child_age_limit'])
												$accepted_age = 1;
										else
												$accepted_age = 0;
							 }
								#$objResponse->alert($dept_obj->sql);
							# $objResponse->alert($age ." - ". $accepted_age);

							 $msgforfemale = "The ".mb_strtoupper($deptInfo['name_formal'])." department is for female only..";
							 $msgformale = "The ".mb_strtoupper($deptInfo['name_formal'])." department is for male only..";
							 $msgforchild = "The ".mb_strtoupper($deptInfo['name_formal'])." department is for children only (0-".$deptInfo['child_age_limit']." yrs old)..";

							 if (($deptInfo['for_male_only']==1)&&($deptInfo['for_female_only']==0)&&($sex=='m')){
									$is_accepted = 1;
							 }elseif (($deptInfo['for_male_only']==0)&&($deptInfo['for_female_only']==1)&&($sex=='f')){
									 $is_accepted = 1;
							 }elseif (($deptInfo['for_male_only']==0)&&($deptInfo['for_female_only']==0)){
									 $is_accepted = 1;
							 }

							 $a = explode(' ', $age);
							 if ($a[1] == 'days') {
								 $age = $a[0]/365;
							 }
							 else {
								 $age = $a[0];
							 }

							 if ($deptInfo['for_child_only']==1){
									 if (!$dept_nr){
										 if ($age > $deptInfo['child_age_limit']){
												$is_accepted = 0;
												$forchild = 1;
									 }
							 }
							 }

							 if (($deptInfo['for_male_only']==1)&&($deptInfo['for_female_only']==0)&&($sex=='f')){
										$formale = 1;
										$forfemale = 0;
							 }elseif (($deptInfo['for_male_only']==0)&&($deptInfo['for_female_only']==1)&&($sex=='m')){
										$formale = 0;
										$forfemale = 1;
							 }

								#added by VAN 06-18-09
								$row_rs  = $dept_obj->isAParentDept($result["nr"]);

								$dept_array = array();
								while ($row_dept=$row_rs->FetchRow()) {
									 $dept_array[] =  $row_dept['nr'];
								}

								$ischild = 0;
								if (in_array($encnr,$dept_array))
										$ischild = 1;

								$isparentdept = 0;
								if ($dept_obj->count)
										$isparentdept = 1;

								#----------

								#$objResponse->alert($encnr);
								#if ($is_accepted){
								if (($is_accepted)||($is_resident_dr)){
						#$objResponse->addScriptCall("ajxSetEROPDDrDept",1,$result["nr"]); # 2nd arg == 1, set consulting department
										if (!$is_resident_dr){
												if ((!$isparentdept)||(!$ischild)||($encnr==0))
														$objResponse->addScriptCall("ajxSetEROPDDepartment",$result["nr"]);
												if($personell_nr)
														$objResponse->addScriptCall("ajxSetEROPDConsultDoctor",$personell_nr);
										}
					}else{
									 if ($formale)
												$objResponse->alert($msgforfemale);
									 elseif ($forfemale)
												$objResponse->alert($msgforfemale);
									 elseif ($forchild)
												$objResponse->alert($msgforchild);

									 $objResponse->addScriptCall("ajxSetEROPDDepartment",0);
									 $objResponse->addScriptCall("ajxSetEROPDDoctor",0,0);
								}
						} else {
				$objResponse->addAlert("setDepartmentEROPD : Error retrieving consulting doctor's department information...");
			}
		}
		return $objResponse;
	}/* end of function setDepartmentEROPD */

#-------------added by VAN 02-01-08
	function setRooms($ward_nr, $room_nr=0){
		global $ward_obj;

		$objResponse = new xajaxResponse();

		if ($ward_nr){
			$rs = $ward_obj->getRoomsData($ward_nr);
			$no_room = $ward_obj->count;

			#added by VAN 12-17-08
			$ward_info=&$ward_obj->getWardInfo($ward_nr);

			#$objResponse->addAlert("sql : ward_nr = ".$ward_info['accomodation_type']);
			#if charity, area will be display
			if ($ward_info['accomodation_type']==1){
				$objResponse->addAssign("area_row","style.display","");
				$objResponse->addAssign("accomodation_type","value",$ward_info['accomodation_type']);
			}else{
				$objResponse->addAssign("area_row","style.display","none");
				$objResponse->addAssign("accomodation_type","value",$ward_info['accomodation_type']);
			}
			#---------
		}

		#$objResponse->addAlert("sql : ward_nr, room_nr = ".$ward_nr." - ".$room_nr);
		#$objResponse->addAlert("sql : ward_nr = ".$ward_obj->sql);

		if ($rs) {
			$objResponse->addScriptCall("ajxClearOptionsRoom");
			if ($no_room > 0){
				$objResponse->addScriptCall("ajxAddOptionRoom","-Select a Room-",0);
			}else{
				$objResponse->addScriptCall("ajxAddOptionRoom","-No Room Available-",0);
			}

			while ($result=$rs->FetchRow()) {
					#$objResponse->addScriptCall("ajxAddOptionRoom",$result["room_nr"], $result["room_nr"]);
					#modified by VAN 05-15-2010
					if ($result["info"])
						$room_nr =$result["room_nr"]." : ".$result["info"];
					else
						$room_nr =$result["room_nr"];

					$rm_nr = $result["room_nr"];
					#$objResponse->alert($result["room_nr"]);
					#modified by VAN 05-15-2010
					$objResponse->addScriptCall("ajxAddOptionRoom",$room_nr, $result["room_nr"]);     // modified by LST per Malaybalay's request ... 05.13.2010
			}

			#if($room_nr){
			if ($rm_nr){
				#$objResponse->addScriptCall("ajxSetRoom",$room_nr);
				#$objResponse->alert($rm_nr);
				$objResponse->addScriptCall("ajxSetRoom",$rm_nr);
				#$objResponse->addAssign("bed_assignment","style.display","");
			}#else{
				#$objResponse->addAssign("bed_assignment","style.display","none");
			#}
			$objResponse->addScriptCall("chckRoomModeStatus");
		}
		else {
			#$objResponse->addAlert("setRooms : Error retrieving Rooms information...");
			#added by VAN 03-10-08
			$objResponse->addScriptCall("ajxClearOptionsRoom");
			$objResponse->addScriptCall("ajxAddOptionRoom","-Select a Ward first-",0);
		}

		return $objResponse;
	}

	function setBeds($ward_nr, $room_nr){
		global $ward_obj;

		$objResponse = new xajaxResponse();

		#if charity and no room, area will be display but read only
			if ($room_nr){
				$objResponse->addAssign("area_row","style.display","none");
				$objResponse->addAssign("area","value","");
			}else{
				$objResponse->addAssign("area_row","style.display","");
				#$objResponse->addAssign("area","value","");
			}

		if (($ward_nr)&&($room_nr)){

			$rs = $ward_obj->getActiveRoomInfo($room_nr,$ward_nr);
			#$objResponse->addAlert("sql = ".$ward_obj->sql);
			if ($rs){
				#$objResponse->addAlert("ajax ward, rm  = ".$ward_nr." - ".$room_nr);
				if ($ward_nr){
					$patients_obj=&$ward_obj->getDayWardOccupants($ward_nr, $room_nr);
					#$objResponse->addAlert("sql = ".$ward_obj->sql);
					#$objResponse->addAlert("count = ".$ward_obj->rec_count);
					if(is_object($patients_obj)){
						# Prepare patients data into array matrix
						$patient = array();
						$patient2 = array();
						while($buf=$patients_obj->FetchRow()){
							$patient[$buf['room_nr']][$buf['bed_nr']]=$buf['sex'];
							$patient2[$buf['room_nr']][$buf['bed_nr']]=$buf['bed_nr'];
						}
					}
				}

				#$objResponse->addAlert("ajax ward, rm  = ".$ward_nr." - ".$room_nr);

				$result=$rs->FetchRow();

				#if ($bed_nr!=$result["nr_of_beds"]){
				if ($ward_obj->rec_count==0){
					#$objResponse->addAlert("sulod");
					for($i=$bed_nr+1; $i<=$result["nr_of_beds"];$i++){
						#$objResponse->addAlert("i, bd = ".$i." = ".$result["nr_of_beds"]);
						$patient[$room_nr][$i]='n';
						$patient2[$room_nr][$i]='0';
					}
				}

				#$objResponse->addAlert("patients_ok = ".print_r($patient));
				#$objResponse->addAlert("patients_ok2 = ".print_r($patient2));
				#$objResponse->addAlert("bed = ".$result["nr_of_beds"]);

				$objResponse->addScriptCall("ajxGetBedRoom",$result["nr_of_beds"], $patient, $patient2);
				$objResponse->addScriptCall("chckRoomModeStatus");
			}
		}else{
			$objResponse->addScriptCall("ajxGetBedRoom",0);
		}

		return $objResponse;
	}

#---------------------------------------

#--------------- CREATED BY VANESSA -----------------------

	function setDoctors($sex='', $age='', $admit_inpatient=0, $dept_nr=0, $personell_nr=0, $ptype='',$change=0) {
		global $pers_obj, $dept_obj;

		$objResponse = new xajaxResponse();
		#$objResponse->addAlert("dept : $personell_nr");

				$cond = "";
		if ($ptype=='phs')
			$cond = " AND personell_nr IN (SELECT dr_nr FROM seg_phs_dr)";

				$is_accepted = 0;
				if ($dept_nr)
						$deptInfo = $dept_obj->getDeptAllInfo($dept_nr);
				else
						$is_accepted = 0;

				#$objResponse->addAlert($dept_obj->sql);
				$msgforfemale = "The ".mb_strtoupper($deptInfo['name_formal'])." department is for female only..";
				$msgformale = "This ".mb_strtoupper($deptInfo['name_formal'])." department is for male only..";
				$msgforchild = "The ".mb_strtoupper($deptInfo['name_formal'])." department is for children only (0-".$deptInfo['child_age_limit']." yrs old)..";

				if (($deptInfo['for_male_only']==1)&&($deptInfo['for_female_only']==0)&&($sex=='m')){
						$is_accepted = 1;
				}elseif (($deptInfo['for_male_only']==0)&&($deptInfo['for_female_only']==1)&&($sex=='f')){
						$is_accepted = 1;
				}elseif (($deptInfo['for_male_only']==0)&&($deptInfo['for_female_only']==0)){
						$is_accepted = 1;
				}

				 //added by omick for pediatrics problem, 12/11/2009
				 $a = explode(' ', $age);
				 if ($a[1] == 'days') {
					 $age = $a[0]/365;
				 }
				 else {
					 $age = $a[0];
				 }

				if ($deptInfo['for_child_only']==1){
						if ($age > $deptInfo['child_age_limit']){
								$is_accepted = 0;
								$forchild = 1;
						}
				}

				if (($deptInfo['for_male_only']==1)&&($deptInfo['for_female_only']==0)&&($sex=='f')){
						$formale = 1;
						$forfemale = 0;
				}elseif (($deptInfo['for_male_only']==0)&&($deptInfo['for_female_only']==1)&&($sex=='m')){
						$formale = 0;
						$forfemale = 1;
				}
			 #$objResponse->alert($personell_nr);
			 $is_resident_dr = 0;
				if ($personell_nr!=0){
						$result_resident=$dept_obj->getDeptofDoctor($personell_nr);
						#$objResponse->alert($dept_obj->sql);
						// if ($result_resident["is_resident_dr"]) //commented by ken 5/15/2014 - temporary commented it to show the attending doctors in updating the patient data
								$is_resident_dr = 1;
				}

				#$objResponse->alert($is_accepted." - ".$is_resident_dr);
				#if (($is_accepted)&&(!$is_resident_dr)){
				#if (($is_accepted)&&(!$is_resident_dr)){
				if ($is_accepted){

						if (($dept_nr)&&(!$is_resident_dr))
							$rs=$pers_obj->getDoctorByDept($dept_nr, $admit_inpatient);
						else
							$rs=$pers_obj->getDoctors($admit_inpatient);

						#$objResponse->addAlert("setDoctors : dept_nr = '".$dept_nr."'");
						#$objResponse->addAlert("setDoctors : pers_obj->sql = '".$pers_obj->sql."'");
				#		$objResponse->addAlert("setDoctors".$admit_inpatient."=".$dept_nr);

						if ($rs) {
							$objResponse->addScriptCall("ajxClearOptions",0);
							$count_rec = $pers_obj->count;
							#$objResponse->alert($pers_obj->count);
							if ($pers_obj->count > 0){
								$objResponse->addScriptCall("ajxAddOption",0,"-Select a Doctor-",0);
							}else{
								#if ($dept_nr)
								if (($dept_nr)&&(!$is_resident_dr))
									$objResponse->addScriptCall("ajxAddOption",0,"-No Doctor Available-",0);
								else
									$objResponse->addScriptCall("ajxAddOption",0,"-Select a Doctor-",0);
							}

							while ($result=$rs->FetchRow()) {
									#$doctor_name = $result["name_first"]." ".$result["name_2"]." ".$result["name_last"];
								#$doctor_name = "Dr. ".ucwords(strtolower($doctor_name));
								if (trim($result["name_middle"]))
									$dot  = ".";

								$doctor_name = trim($result["name_last"]).", ".trim($result["name_first"])." ".substr(trim($result["name_middle"]),0,1).$dot;
							 # $doctor_name = ucwords(strtolower($doctor_name)).", MD";
								#$objResponse->addAlert($doctor_name." - ".$result["personell_nr"]);
								$objResponse->addScriptCall("ajxAddOption",0,$doctor_name,$result["personell_nr"]);
							}

										if ($is_resident_dr){

												if($personell_nr)
																$objResponse->addScriptCall("ajxSetDoctor",$personell_nr);
																#$objResponse->alert($dept_nr);
												if($dept_nr)
														$objResponse->addScriptCall("ajxSetDepartment",$dept_nr);
										}
						}
						else {
							$objResponse->addAlert("setDoctors : Error retrieving Doctors information...");
						}
						/*}elseif ($is_resident_dr) {
								 #$objResponse->addAlert('set');
								 #$objResponse->addScriptCall("jsGetDoctors");
								 #$objResponse->addScriptCall("ajxSetDoctor",$result["personell_nr"]);
								 $update = 1;
								 if ($update){
											 #$objResponse->addScriptCall("jsGetDoctors");
											 $rs=$pers_obj->getDoctors($admit_inpatient);

											 if($personell_nr)
														$objResponse->addScriptCall("ajxSetDoctor",$personell_nr);

											 if($dept_nr)
												$objResponse->addScriptCall("ajxSetDepartment",$dept_nr);
								 }*/
						 }else{
									 if ($formale)
												$objResponse->alert($msgformale);
									 elseif ($forfemale)
												$objResponse->alert($msgforfemale);
									 elseif ($forchild)
												$objResponse->alert($msgforchild);

									 $objResponse->addScriptCall("ajxSetDepartment",0);
									 $objResponse->addScriptCall("jsGetDoctors");
						 }

		return $objResponse;
	}

	function setDepartments($personell_nr=0, $encnr, $sex='', $age='') {
		global $dept_obj;

		$objResponse = new xajaxResponse();
				#$age
				#$objResponse->addAlert("setDepartments : personell_nr ='$personell_nr'");
			#$encnr here is department_nr
			$dept_nr = $encnr;

			if ($personell_nr!=0){
						$is_resident_dr = 0;
			$result=$dept_obj->getDeptofDoctor($personell_nr);

						if ($result["is_resident_dr"])
								$is_resident_dr = 1;
						#$objResponse->addAlert("setDepartments : dept_obj->sql = '$dept_obj->sql'");
			#$objResponse->addAlert("name_formal = ".$result["name_formal"]." - ".$result["is_resident_dr"]);
			if ($result) {
								$is_accepted = 0;
								#$objResponse->alert('dept = '.$result["nr"]);
							 $deptInfo = $dept_obj->getDeptAllInfo($result["nr"]);
							 $accepted_age = 0;
							 #if (stristr($age,'years')){
							 if ((stristr($age,'years'))||(stristr($age,'year'))){
										$age = substr($age,0,-5);
										$age = floor($age);

										if ($age <= $deptInfo['child_age_limit'])
												$accepted_age = 1;
										else
												$accepted_age = 0;
							 }else{
				//added by omick 12/11/2009
								 $a = explode(' ', $age);
								 if ($a[1] == 'days') {
									$age = $a[0]/365;
									}
									else {
									$age = $a[0];
									}
										if ($age <= $deptInfo['child_age_limit'])
												$accepted_age = 1;
										else
												$accepted_age = 0;
							 }

							# $objResponse->alert($age ." - ". $accepted_age);

							 $msgforfemale = "The ".mb_strtoupper($deptInfo['name_formal'])." department is for female only..";
							 $msgformale = "The ".mb_strtoupper($deptInfo['name_formal'])." department is for male only..";
							 $msgforchild = "The ".mb_strtoupper($deptInfo['name_formal'])." department is for children only (0-".$deptInfo['child_age_limit']." yrs old)..";

							 if (($deptInfo['for_male_only']==1)&&($deptInfo['for_female_only']==0)&&($sex=='m')){
									$is_accepted = 1;
							 }elseif (($deptInfo['for_male_only']==0)&&($deptInfo['for_female_only']==1)&&($sex=='f')){
									 $is_accepted = 1;
							 }elseif (($deptInfo['for_male_only']==0)&&($deptInfo['for_female_only']==0)){
									 $is_accepted = 1;
							 }

							 $a = explode(' ', $age);
							 if ($a[1] == 'days') {
								 $age = $a[0]/365;
							 }
							 else {
								 $age = $a[0];
							 }

							 if ($deptInfo['for_child_only']==1){
									 if (!$dept_nr){
										 if ($age > $deptInfo['child_age_limit']){
												$is_accepted = 0;
												$forchild = 1;
									 }
							 }
							 }

							 if (($deptInfo['for_male_only']==1)&&($deptInfo['for_female_only']==0)&&($sex=='f')){
										$formale = 1;
										$forfemale = 0;
							 }elseif (($deptInfo['for_male_only']==0)&&($deptInfo['for_female_only']==1)&&($sex=='m')){
										$formale = 0;
										$forfemale = 1;
							 }

								#added by VAN 06-18-09
								$row_rs  = $dept_obj->isAParentDept($result["nr"]);

								$dept_array = array();
								while ($row_dept=$row_rs->FetchRow()) {
									 $dept_array[] =  $row_dept['nr'];
								}

								$ischild = 0;
								if (in_array($encnr,$dept_array))
										$ischild = 1;

								$isparentdept = 0;
								if ($dept_obj->count)
										$isparentdept = 1;
								#-----------------

								#$objResponse->alert('dept = '.$is_resident_dr);
								if (($is_accepted)||($is_resident_dr)){

										if (!$is_resident_dr){
												if ((!$isparentdept)||(!$ischild)||($encnr==0)){
														$objResponse->addScriptCall("ajxSetDepartment",$result["nr"]);
												}
												if($personell_nr)
													$objResponse->addScriptCall("ajxSetDoctor",$personell_nr);
										}else{
												# $objResponse->alert($encnr);
												 $objResponse->addScriptCall("ajxSetDepartment",$encnr);
										}

								}else{
									 if ($formale)
												$objResponse->alert($msgformale);
									 elseif ($forfemale)
												$objResponse->alert($msgforfemale);
									 elseif ($forchild)
												$objResponse->alert($msgforchild);

									 #$objResponse->addScriptCall("ajxSetDepartment",$encnr);
									 $objResponse->addScriptCall("ajxSetDepartment",0);
									 $objResponse->addScriptCall("jsGetDoctors");
								}
			}

			#else{
			#	$objResponse->addAlert("setDepartments : Error retrieving Department information...");
			#}
		}else{
			#$objResponse->addAlert("sulod");
			$objResponse->addScriptCall("ajxSetDepartment",$encnr);
		}
		return $objResponse;
	}


	function setALLDepartment($admit_inpatient, $ptype=''){
	global $dept_obj;

		$objResponse = new xajaxResponse();
		#$objResponse->addAlert("setALLDepartment");
		if ($ptype=='phs')
			$cond = " AND nr IN (SELECT dept_nr FROM seg_phs_dr)";

		$rs=$dept_obj->getAllOPDMedicalObject($admit_inpatient, $cond);
		#$objResponse->addAlert("setALLDepartment = ".$dept_obj->sql);
		if ($rs) {
			$objResponse->addScriptCall("ajxClearOptions",1);
			if ($dept_obj->count > 0){
				$objResponse->addScriptCall("ajxAddOption",1,"-Select a Department-",0);
			}else{
				$objResponse->addScriptCall("ajxAddOption",1,"-Select a Department-",0);
			}
			while ($result=$rs->FetchRow()) {
				 $objResponse->addScriptCall("ajxAddOption",1,$result["name_formal"],$result["nr"]);
			}
		}
		else {
			$objResponse->addAlert("setALLDepartment : Error retrieving Department information...");
		}
		return $objResponse;
	}

	#added by VAN 04-18-2010
	function setConsultingDoctors($sex='', $age='', $admit_inpatient=0, $dept_nr=0, $personell_nr=0) {
		global $pers_obj, $dept_obj;

		$objResponse = new xajaxResponse();

		$is_accepted = 0;
		if ($dept_nr)
			 $deptInfo = $dept_obj->getDeptAllInfo($dept_nr);
		else
			 $is_accepted = 0;

		$msgforfemale = "The ".mb_strtoupper($deptInfo['name_formal'])." department is for female only..";
		$msgformale = "This ".mb_strtoupper($deptInfo['name_formal'])." department is for male only..";
		$msgforchild = "The ".mb_strtoupper($deptInfo['name_formal'])." department is for children only (0-".$deptInfo['child_age_limit']." yrs old)..";

		if (($deptInfo['for_male_only']==1)&&($deptInfo['for_female_only']==0)&&($sex=='m')){
			 $is_accepted = 1;
		}elseif (($deptInfo['for_male_only']==0)&&($deptInfo['for_female_only']==1)&&($sex=='f')){
			 $is_accepted = 1;
		}elseif (($deptInfo['for_male_only']==0)&&($deptInfo['for_female_only']==0)){
			 $is_accepted = 1;
		}

		$a = explode(' ', $age);
		if ($a[1] == 'days') {
			$age = $a[0]/365;
		}else {
			$age = $a[0];
		}

		if ($deptInfo['for_child_only']==1){
			 if ($age > $deptInfo['child_age_limit']){
					$is_accepted = 0;
					$forchild = 1;
			 }
		}

		if (($deptInfo['for_male_only']==1)&&($deptInfo['for_female_only']==0)&&($sex=='f')){
			 $formale = 1;
			 $forfemale = 0;
		}elseif (($deptInfo['for_male_only']==0)&&($deptInfo['for_female_only']==1)&&($sex=='m')){
			 $formale = 0;
			 $forfemale = 1;
		}

		$is_resident_dr = 0;
		if ($personell_nr!=0){
			 $result_resident=$dept_obj->getDeptofDoctor($personell_nr);
			 if ($result_resident["is_resident_dr"])
				 $is_resident_dr = 1;
			 }

			 if ($is_accepted){

			 if (($dept_nr)&&(!$is_resident_dr))
				 $rs=$pers_obj->getDoctorByDept($dept_nr, $admit_inpatient);
			 else
				 $rs=$pers_obj->getDoctors($admit_inpatient);
			 #$objResponse->alert($pers_obj->sql);
			 if ($rs) {
				 $objResponse->addScriptCall("ajxClearOptionsConsultant",0);
				 $count_rec = $pers_obj->count;
				 if ($pers_obj->count > 0){
					 $objResponse->addScriptCall("ajxAddOptionConsultant",0,"-Select a Doctor-",0);
				 }else{
					 if (($dept_nr)&&(!$is_resident_dr))
							$objResponse->addScriptCall("ajxAddOptionConsultant",0,"-No Doctor Available-",0);
					 else
							$objResponse->addScriptCall("ajxAddOptionConsultant",0,"-Select a Doctor-",0);
				 }

				 while ($result=$rs->FetchRow()) {
						if (trim($result["name_middle"]))
							 $dot  = ".";

						$doctor_name = trim($result["name_last"]).", ".trim($result["name_first"])." ".substr(trim($result["name_middle"]),0,1).$dot;
						$objResponse->addScriptCall("ajxAddOptionConsultant",0,$doctor_name,$result["personell_nr"]);
				 }

				 if ($is_resident_dr){
						if($personell_nr)
							 $objResponse->addScriptCall("ajxSetDoctorConsultant",$personell_nr);
						if($dept_nr)
							 $objResponse->addScriptCall("ajxSetDepartmentConsultant",$dept_nr);
				 }
			}else {
				 $objResponse->addAlert("setConsultantDoctors : Error retrieving Doctors information...");
			}
		}else{
				 if ($formale)
						$objResponse->alert($msgformale);
				 elseif ($forfemale)
						$objResponse->alert($msgforfemale);
				 elseif ($forchild)
						$objResponse->alert($msgforchild);

				 $objResponse->addScriptCall("ajxSetDepartmentConsultant",0);
				 $objResponse->addScriptCall("jsGetDoctorsConsultant");
		}

		return $objResponse;
	}

	function setConsultingDepartments($personell_nr=0, $encnr, $sex='', $age='') {
		global $dept_obj;

		$objResponse = new xajaxResponse();
		if ($personell_nr!=0){
			 $is_resident_dr = 0;
			 $result=$dept_obj->getDeptofDoctor($personell_nr);

			 if ($result["is_resident_dr"])
				 $is_resident_dr = 1;
			 if ($result) {
				 $is_accepted = 0;
				 $deptInfo = $dept_obj->getDeptAllInfo($result["nr"]);
				 $accepted_age = 0;
				 if ((stristr($age,'years'))||(stristr($age,'year'))){
						$age = substr($age,0,-5);
						$age = floor($age);

						if ($age <= $deptInfo['child_age_limit'])
							$accepted_age = 1;
						else
							$accepted_age = 0;
				 }else{
						$a = explode(' ', $age);
						if ($a[1] == 'days') {
							$age = $a[0]/365;
						}else {
							$age = $a[0];
						}
						if ($age <= $deptInfo['child_age_limit'])
							 $accepted_age = 1;
						else
							 $accepted_age = 0;
				 }

				 $msgforfemale = "The ".mb_strtoupper($deptInfo['name_formal'])." department is for female only..";
				 $msgformale = "The ".mb_strtoupper($deptInfo['name_formal'])." department is for male only..";
				 $msgforchild = "The ".mb_strtoupper($deptInfo['name_formal'])." department is for children only (0-".$deptInfo['child_age_limit']." yrs old)..";

				 if (($deptInfo['for_male_only']==1)&&($deptInfo['for_female_only']==0)&&($sex=='m')){
					 $is_accepted = 1;
				 }elseif (($deptInfo['for_male_only']==0)&&($deptInfo['for_female_only']==1)&&($sex=='f')){
					 $is_accepted = 1;
				 }elseif (($deptInfo['for_male_only']==0)&&($deptInfo['for_female_only']==0)){
					 $is_accepted = 1;
				 }

				 $a = explode(' ', $age);
				 if ($a[1] == 'days') {
						$age = $a[0]/365;
				 }else {
						$age = $a[0];
				 }

				 if ($deptInfo['for_child_only']==1){
						if ($age > $deptInfo['child_age_limit']){
							 $is_accepted = 0;
							 $forchild = 1;
						}
				 }

				 if (($deptInfo['for_male_only']==1)&&($deptInfo['for_female_only']==0)&&($sex=='f')){
						$formale = 1;
						$forfemale = 0;
				 }elseif (($deptInfo['for_male_only']==0)&&($deptInfo['for_female_only']==1)&&($sex=='m')){
						$formale = 0;
						$forfemale = 1;
				 }

				 $row_rs  = $dept_obj->isAParentDept($result["nr"]);

				 $dept_array = array();
				 while ($row_dept=$row_rs->FetchRow()) {
						$dept_array[] =  $row_dept['nr'];
				 }

				 $ischild = 0;
				 if (in_array($encnr,$dept_array))
						$ischild = 1;

				 $isparentdept = 0;
				 if ($dept_obj->count)
					 $isparentdept = 1;

				 if (($is_accepted)||($is_resident_dr)){

					 if (!$is_resident_dr){
							if ((!$isparentdept)||(!$ischild)||($encnr==0)){
									$objResponse->addScriptCall("ajxSetDepartmentConsultant",$result["nr"]);
							}
							if($personell_nr)
									$objResponse->addScriptCall("ajxSetDoctorConsultant",$personell_nr);
							}else{
								 $objResponse->addScriptCall("ajxSetDepartmentConsultant",$encnr);
							}

					 }else{
							if ($formale)
								 $objResponse->alert($msgformale);
							elseif ($forfemale)
								 $objResponse->alert($msgforfemale);
							elseif ($forchild)
								 $objResponse->alert($msgforchild);

							$objResponse->addScriptCall("ajxSetDepartmentConsultant",0);
							$objResponse->addScriptCall("jsGetDoctorsConsultant");
					 }
			}

		}else{
			$objResponse->addScriptCall("ajxSetDepartmentConsultant",$encnr);
		}
		return $objResponse;
	}

	function setALLConsultingDepartment($admit_inpatient){
	global $dept_obj;

		$objResponse = new xajaxResponse();
		$rs=$dept_obj->getAllOPDMedicalObject($admit_inpatient);
		#$objResponse->alert("s= ".$dept_obj->count);
		if ($rs) {
			$objResponse->addScriptCall("ajxClearOptionsConsultant",1);
			if ($dept_obj->count > 0){
				$objResponse->addScriptCall("ajxAddOptionConsultant",1,"-Select a Department-",0);
			}else{
				$objResponse->addScriptCall("ajxAddOptionConsultant",1,"-No Department Available-",0);
			}
			while ($result=$rs->FetchRow()) {
				 $objResponse->addScriptCall("ajxAddOptionConsultant",1,$result["name_formal"],$result["nr"]);
			}
		}
		else {
			$objResponse->addAlert("setALLConsultantDepartment : Error retrieving Department information...");
		}
		return $objResponse;
	}

	function setALLConsultingDoctor($admit_inpatient){
		global $pers_obj;

		$objResponse = new xajaxResponse();
		$rs=$pers_obj->getDoctors($admit_inpatient);
		#$objResponse->alert("dd = ".$admit_inpatient." - ".$pers_obj->sql);
		#$objResponse->alert($pers_obj->count);
		if ($rs) {
			$objResponse->addScriptCall("ajxAddOptionConsultant",0);
			if ($pers_obj->count > 0){
				$objResponse->addScriptCall("ajxAddOptionConsultant",0,"-Select a Doctor-",0);
			}else{
				$objResponse->addScriptCall("ajxAddOptionConsultant",0,"-No Doctor Available-",0);
			}

			while ($result=$rs->FetchRow()) {
				if (trim($result["name_middle"]))
							 $dot  = ".";

				 $doctor_name = trim($result["name_last"]).", ".trim($result["name_first"])." ".substr(trim($result["name_middle"]),0,1).$dot;

				 $objResponse->addScriptCall("ajxAddOptionConsultant",0,$doctor_name,$result["personell_nr"]);
			}
		}
		else {
			$objResponse->addAlert("setALLConsultantDoctor : Error retrieving Doctor information...");
		}
		return $objResponse;
	}
	#----------------------

    #edited by VAN 02-03-2012
	function checkPreviousTrxn($pid, $current_dept_nr, $patient_type, $encounter_date, $is_update,$encounter_nr, $issubmit){
		global $enc_obj;

		$objResponse = new xajaxResponse();

		if (($patient_type=='opd') || ($patient_type=='phs') || ($patient_type=='ic'))
			 $encounter_type = '2';
		elseif ($patient_type=='er')
			 $encounter_type = '1';
		elseif (($patient_type=='ipd') || ($patient_type=='newborn'))
			 $encounter_type = '3,4';

		$encounter_date = date("Y-m-d H:i:s",strtotime($encounter_date));
        #$objResponse->alert($is_update." - ".$encounter_nr);
        #edited by VAN 02-03-2012
        $hasSameTrxn=$enc_obj->hasSameConsultation($pid, $current_dept_nr, $encounter_type, $encounter_date, $is_update, $encounter_nr);
        if (($is_update==1)&&($encounter_nr==$enc_obj->rowDup['encounter_nr'])){
           $hasSameTrxn = 0;     
        }

		#$objResponse->alert("dd = ".$enc_obj->sql);
		$encounter_nr = $enc_obj->rowDup['encounter_nr'];
		if ($hasSameTrxn) {
			 $objResponse->addAlert("This patient had a previous consultation/admission with the same date and clinics. \n The case # is ".$encounter_nr.". Please check the said case #. Thank you.");
			 $objResponse->assign("current_dept_nr","value","0");
			 $objResponse->assign("current_att_dr_nr","value","0");

		}else{
			if ($issubmit==1){
                $objResponse->addScriptCall("submitForm");    
            }else 
			 $objResponse->addScriptCall("jsGetDoctors");
		}
		return $objResponse;
	}

	/**
	* Created by Jarel
	* Created On 11/07/2013
	* OR Validation
	* @param string ornum
	* @param string pid
	* @return Boolean
	*/
	function validateOR($ornum,$pid){
		global $db;
		$objResponse = new xajaxResponse();

		$sql = "SELECT p.or_no, DATEDIFF(DATE(NOW()),DATE(p.or_date)) as `validity`
					FROM seg_pay AS p 
					INNER JOIN seg_pay_request AS pr ON p.or_no=pr.or_no 
					AND pr.ref_source = 'OTHER' 
					INNER JOIN seg_other_services AS so ON so.service_code=SUBSTRING(pr.service_code,1,LENGTH(pr.service_code)-1) 
					WHERE p.pid= " .$db->qstr($pid). " 
					AND p.or_no= ".$db->qstr($ornum). "
					AND (ISNULL(p.cancel_date) 
					OR p.cancel_date='0000-00-00 00:00:00') 
					AND so.account_type='33' LIMIT 1
				UNION
				SELECT p.`or_no`, DATEDIFF(DATE(NOW()),DATE(p.or_date)) as `validity`
					FROM seg_pay AS p
					INNER JOIN seg_pay_request AS pr ON p.or_no=pr.or_no AND pr.ref_source = 'MISC'
					INNER JOIN seg_other_services AS so ON so.service_code='00002338'
					WHERE p.pid= " .$db->qstr($pid). " 
					AND p.or_no= " .$db->qstr($ornum). "
					AND (ISNULL(p.cancel_date) 
					OR p.cancel_date='0000-00-00 00:00:00') LIMIT 1";
		
		if ($result=$db->Execute($sql)){
			if ($result->RecordCount()){
				$row = $result->FetchRow();
				$OR = $row['or_no'];
				$validity = $row['validity'];
			}
		}

		$sql = "SELECT or_no FROM seg_doctors_co_manage
				WHERE or_no = " .$db->qstr($ornum). "
				LIMIT 1";
		
		if ($result=$db->Execute($sql)){
			if ($result->RecordCount()){
				$row = $result->FetchRow();
				$OR1 = $row['or_no'];
			}
		}

		if(!empty($OR1)){
			$objResponse->alert('This OR is already used.');
		}elseif (empty($OR)){
			$objResponse->alert('This OR is not intended to this patient');
		}elseif ($validity>7){
			$objResponse->alert('This OR exceeds the 7 days validity.');
		}else {
			$objResponse->assign("official_receipt_nr","value",$ornum);
		}
		
		return $objResponse;
	}


	/**
	* Created by Jarel
	* Created On 03/10/2015
	* Populate Doctor list in Encounter Data
	* @param string ornum
	* @param string pid
	* @return Boolean
	*/
	function populateEncounterDRAddList($encounter_nr)
	{
		global $db;
		global $enc_obj;
		$objResponse = new xajaxResponse();

		$objResponse->addScriptCall('clearDocTray','order-list-adm');
		$objResponse->addScriptCall('clearDocTray','order-list-att');


		if($enc_obj->hasRecordEncounterDRAdd($encounter_nr))
		{
			$items = $enc_obj->getEncounterDRAddList($encounter_nr);
			if($items){
				foreach($items AS $row){
					$details->dr_nr = $row['dr_nr'];
					$details->name = $row['name'];
					$details->is_admitting = $row['is_admitting'];
					$details->is_attending = $row['is_attending'];
					$details->is_consulting = $row['is_consulting'];
					if($row['is_admitting'])
						$objResponse->addScriptCall("addDoctorToList","order-list-adm",$details);
					else
						$objResponse->addScriptCall("addDoctorToList","order-list-att",$details);
				}//End Foreach
			}//End If
		}else{
			if($encounter_nr!=''){
				$data = $enc_obj->getEncounterDRDetails($encounter_nr);
				if($data){
					$details->dr_nr = $data['current_att_dr_nr'];
					$details->name = $data['att_name'];
					$details->is_admitting = 0;
					$details->is_attending = 1;
					$details->is_consulting = 0;
					$objResponse->addScriptCall("addDoctorToList","order-list-att",$details);

					$details1->dr_nr = $data['consulting_dr_nr'];
					$details1->name = $data['adm_name'];
					$details1->is_admitting = 1;
					$details1->is_attending = 0;
					$details1->is_consulting = 0;
					$objResponse->addScriptCall("addDoctorToList","order-list-adm",$details1);

				}
			}
		}


		
		return $objResponse;

	}

	function setReferralReason($encounter_nr) {
		global $enc_obj;

		$objResponse = new xajaxResponse();

		$referral_reason = $enc_obj->getReferralReason();

		if ($referral_reason) {
			array_unshift($referral_reason, "-Select referral reason-");

			foreach($referral_reason as $key => $result) {
				$objResponse->addScriptCall("ajxAddOptionReferralReason", $result, $key);
			}
		} else {
			$objResponse->addScriptCall("ajxAddOptionReferralReason", "-Select referral reason-", 0);
		}

		$patient_referral_reason = $enc_obj->getPatientReferralReason($encounter_nr);

		if ($patient_referral_reason) {
			$objResponse->addScriptCall("ajxSetPatientReferralReason", $patient_referral_reason['referral_reason']);
		} else {
			$objResponse->addScriptCall("ajxSetPatientReferralReason", 0);
		}

		return $objResponse;
	}


	$root_path="../../";
	require($root_path.'include/inc_environment_global.php');

	/* Create the helper class for the personell table */
	include_once($root_path.'include/care_api_classes/class_personell.php');
	include_once($root_path.'include/care_api_classes/class_department.php');

	#added by VAN 02-01-08
	require_once($root_path.'include/care_api_classes/class_ward.php');
	$ward_obj=new Ward;

	include_once($root_path.'include/care_api_classes/class_encounter.php');
	$enc_obj=new Encounter;

	$dept_obj=new Department;
	$pers_obj=new Personell;

	require("doctor-dept.common.php");
	$xajax->processRequests();
?>