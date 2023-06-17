<?php


		#
		# Create append data for previous and next page links
		#
		$this->targetappend.="&firstname_too=$firstname_too&origin=$origin";

		//echo $mode;
		if($parent_admit) $bgimg='tableHeaderbg3.gif';
			else $bgimg='tableHeader_gr.gif';
		$tbg= 'background="'.$root_path.'gui/img/common/'.$theme_com_icon.'/'.$bgimg.'"';

		if($mode=='search'||$mode=='paginate'){
			if ($linecount) $this->smarty->assign('LDSearchFound',str_replace("~no.~",$totalcount,$LDSearchFound).' '.$LDShowing.' '.$pagen->BlockStartNr().' '.$LDTo.' '.$pagen->BlockEndNr().'.');
				else $this->smarty->assign('LDSearchFound',str_replace('~no.~','0',$LDSearchFound));
		}

		if ($linecount){
			
			$this->smarty->assign('bShowResult',TRUE);

			$img_male=createComIcon($root_path,'spm.gif','0');
			$img_female=createComIcon($root_path,'spf.gif','0');

			$this->smarty->assign('LDRegistryNr',$pagen->makeSortLink($LDRegistryNr,'pid',$oitem,$odir,$this->targetappend));
			$this->smarty->assign('LDSex',$pagen->makeSortLink($LDSex,'sex',$oitem,$odir,$this->targetappend));
			$this->smarty->assign('LDLastName',$pagen->makeSortLink($LDLastName,'name_last',$oitem,$odir,$this->targetappend));
			$this->smarty->assign('LDFirstName',$pagen->makeSortLink($LDFirstName,'name_first',$oitem,$odir,$this->targetappend));
			$this->smarty->assign('LDBday',$pagen->makeSortLink($LDBday,'date_birth',$oitem,$odir,$this->targetappend));
			$this->smarty->assign('segBrgy',$pagen->makeSortLink("Barangay",'brgy_name',$oitem,$odir,$this->targetappend));   # burn added: March 8, 2007
			$this->smarty->assign('segMuni',$pagen->makeSortLink("Muni/City",'mun_name',$oitem,$odir,$this->targetappend));   # burn added: March 8, 2007
#			$this->smarty->assign('LDZipCode',$pagen->makeSortLink($LDZipCode,'addr_zip',$oitem,$odir,$this->targetappend));   # burn commented: March 8, 2007
			$this->smarty->assign('LDZipCode',$pagen->makeSortLink($LDZipCode,'zipcode',$oitem,$odir,$this->targetappend));   # burn added: March 8, 2007
			if(!empty($this->targetfile)){
				$this->smarty->assign('LDOptions',$LDOptions);
			}

			#
			# Generate the resulting list rows using the reg_search_list_row.tpl template
			#

			include_once($root_path.'include/care_api_classes/class_encounter.php');
			# Create encounter object
			$encounter_obj=new Encounter();   # burn added: March 15, 2007
					# burn added: March 15, 2007
			require_once($root_path.'include/care_api_classes/class_department.php');
			$dept_obj=new Department;
			if (!empty($HTTP_SESSION_VARS['sess_login_userid']))
				$seg_user_name = $HTTP_SESSION_VARS['sess_login_userid'];
			else
				$seg_user_name = $HTTP_SESSION_VARS['sess_temp_userid'];
			$user_dept_info = $dept_obj->getUserDeptInfo($seg_user_name);

#echo "seg_user_name = '".$seg_user_name."' <br> \n";
#echo "HTTP_SESSION_VARS['sess_login_username'] = '".$HTTP_SESSION_VARS['sess_login_username']."' <br> \n";
#echo "HTTP_SESSION_VARS['sess_user_name'] = '".$HTTP_SESSION_VARS['sess_user_name']."' <br> \n";
#echo "user_dept_info['dept_nr'] = '".$user_dept_info['dept_nr']."' <br> \n";

			$sTemp = '';
			$toggle=0;
			while($zeile=$ergebnis->FetchRow()){
						
				if($zeile['status']=='' || $zeile['status']=='normal'){

					$this->smarty->assign('toggle',$toggle);
					$toggle = !$toggle;
						
#	echo " zeile['pid'] = '".$zeile['pid']."' ; admitted = '".$encounter_obj->isCurrentlyAdmitted($zeile['pid'],'_PID')."' <br> \n";
#	echo " encounter_obj->sql = '".$encounter_obj->sql."' <br> \n";
						# burn added: March 15, 2007
					$label='';
					if ( $encounter_obj->isCurrentlyAdmitted($zeile['pid'],'_PID') &&
						  ($enc_row = $encounter_obj->getLastestEncounter($zeile['pid'])) ){
#					if ($enc_row = $encounter_obj->getLastestEncounter($zeile['pid'])){
						if($enc_row['encounter_type']==1){
							$label =	'<img '.createComIcon($root_path,'flag_red.gif').'>'.
										'<font size=1 color="red">ER</font>';
						}elseif($enc_row['encounter_type']==2){
							$label =	'<img '.createComIcon($root_path,'flag_blue.gif').'>'.
										'<font size=1 color="blue">Outpatient</font>';
						}else{
							$label =	'<img '.createComIcon($root_path,'flag_green.gif').'>'.
										'<font size=1 color="green">Inpatient</font>';
						}
					}else{
						$enc_row['encounter_type']=0;   # no ACTIVE encounter
					}

					$this->smarty->assign('sRegistryNr',$zeile['pid']." ".$label);

					switch(strtolower($zeile['sex'])){
						case 'f': $this->smarty->assign('sSex','<img '.$img_female.'>'); break;
						case 'm': $this->smarty->assign('sSex','<img '.$img_male.'>'); break;
						default: $this->smarty->assign('sSex','&nbsp;'); break;
					}
					$this->smarty->assign('sLastName',ucfirst($zeile['name_last']));
					$this->smarty->assign('sFirstName',ucfirst($zeile['name_first']));
					#
					# If person is dead show a black cross
					#
					if($zeile['death_date']&&$zeile['death_date']!=$dbf_nodate) $this->smarty->assign('sCrossIcon','<img '.createComIcon($root_path,'blackcross_sm.gif','0','absmiddle').'>');
						else $this->smarty->assign('sCrossIcon','');
					
					$date_birth = @formatDate2Local($zeile['date_birth'],$date_format);			
					$bdateMonth = substr($date_birth,0,2);
					$bdateDay = substr($date_birth,3,2);
					$bdateYear = substr($date_birth,6,4);
					if (!checkdate($bdateMonth, $bdateDay, $bdateYear)){
						//echo "invalid birthdate! <br> \n";
						$date_birth='';
					}
#					$this->smarty->assign('sBday',formatDate2Local($zeile['date_birth'],$date_format));   # burn commented: March 26, 2007
					$this->smarty->assign('sBday',$date_birth);   # burn added: March 26, 2007
					$this->smarty->assign('sBrgy',$zeile['brgy_name']);   # burn added: March 8, 2007
					$this->smarty->assign('sMuni',$zeile['mun_name']);   # burn added: March 8, 2007

#					$this->smarty->assign('sZipCode',$zeile['addr_zip']);   # burn commented: March 8, 2007
					$this->smarty->assign('sZipCode',$zeile['zipcode']);   # burn added: March 8, 2007

						# burn added: March 16, 2007
					if ( ($user_dept_info['dept_nr']==150) &&
						  (($enc_row['encounter_type']==0) || $enc_row['encounter_type']==2)
						){
						$allow_show_details=TRUE;   # search under OPD Triage
					}elseif( ($user_dept_info['dept_nr']==149) &&
								(($enc_row['encounter_type']==0) || $enc_row['encounter_type']==1)
							 ){
						$allow_show_details=TRUE;   # search under ER Triage
					}elseif(($user_dept_info['dept_nr']==148)||($user_dept_info['dept_nr']==151)){
						$allow_show_details=TRUE;   # search under Admitting section or Medical Records
					}else{
						$allow_show_details=FALSE;   # User has no permission to VIEW person's details
					}
														  
					if ($this->seg_search_type == 'personnel'){
						$allow_show_details=TRUE;   # search under Personnel Management
					}
					
					if ($this->seg_send_to_input) {
						$control_id = $this->seg_sti_control_id;
						if ($this->seg_sti_target_window == "parent") 
							$docTarget = "window.parent.document.";
						elseif ($this->seg_sti_target_window == "opener") 
							$docTarget = "window.opener.document.";
						elseif ($this->seg_sti_target_window == "") 
							$docTarget = "document.";
						else
							$docTarget = $this->seg_sti_target_window.".document.";
						$sTarget = "<a href=\"#\" onclick=\"" . $docTarget . "getElementById('".$control_id."_text').value='".$zeile['name_first']." ".$zeile['name_last']."';";
						$sTarget .= $docTarget . "getElementById('".$control_id."_id').value='".$zeile['pid'] . "';";
						if ($this->seg_sti_close_onclick)	$sTarget .= "window.close();";

						$sTarget .= "\">";						
						//$sTarget = "<a href=\"$this->targetfile".URL_APPEND."&pid=".$zeile['pid']."&edit=1&status=".$status."&target=".$target."&user_origin=".$user_origin."&noresize=1&mode=\">";
						$sTarget=$sTarget.'<img '.createLDImgSrc($root_path,'ok_small.gif','0').' title="'.$LDShowDetails.'"></a>';
						$this->smarty->assign('sOptions',$sTarget);
					}
					elseif ($withtarget) {
#echo "enc_row['encounter_type'] = '".$enc_row['encounter_type']."' allow_show_details = '$allow_show_details' <br> \n";
						$sTarget='';
						if ($allow_show_details){
							$sTarget = "<a href=\"$this->targetfile".URL_APPEND."&pid=".$zeile['pid']."&edit=1&status=".$status."&target=".$target."&user_origin=".$user_origin."&noresize=1&mode=\">";
							$sTarget=$sTarget.'<img '.createLDImgSrc($root_path,'ok_small.gif','0').' title="'.$LDShowDetails.'"></a>';
						}
						$this->smarty->assign('sOptions',$sTarget);
					}
					if(!file_exists($root_path.'cache/barcodes/pn_'.$zeile['pid'].'.png')){
						$this->smarty->assign('sHiddenBarcode',"<img src='".$root_path."classes/barcode/image.php?code=".$zeile['pid']."&style=68&type=I25&width=180&height=50&xres=2&font=5&label=2' border=0 width=0 height=0>");
					}
					#
					# Generate the row in buffer and append as string
					#
					ob_start();
						$this->smarty->display('registration_admission/reg_search_list_row.tpl');
						$sTemp = $sTemp.ob_get_contents();
					ob_end_clean();
				}
			}
			#
			# Assign the rows string to template
			#
			$this->smarty->assign('sResultListRows',$sTemp);

			$this->smarty->assign('sPreviousPage',$pagen->makePrevLink($LDPrevious,$this->targetappend));
			$this->smarty->assign('sNextPage',$pagen->makeNextLink($LDNext,$this->targetappend));
		}
		#
		# Add eventual appending text block
		#
		if(!empty($this->posttext)) $this->smarty->assign('sPostText',$this->posttext);
		
		#
		# Displays the search page
		#
		if($this->bReturnOnly){
			ob_start();
				$this->smarty->display('registration_admission/reg_search_main.tpl');
				$sTemp=ob_get_contents();
			ob_end_clean();
			return $sTemp;
		}else{
			# show Template
			$this->smarty->display('registration_admission/reg_search_main.tpl');
		}

?>

