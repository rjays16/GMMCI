<?php
/**
* @package care_api
*/

/**
*/
/**
*  GUI person registration data show methods.
* Dependencies:
* assumes the following files are in the given path
*
*  Note this class should be instantiated only after a "$db" adodb  connector object  has been established by an adodb instance
* @author Elpidio Latorilla
* @version beta 2.0.1
* @copyright 2002,2003,2004,2005,2005 Elpidio Latorilla
* @package care_api
*/

$thisfile = basename($HTTP_SERVER_VARS['PHP_SELF']);

class GuiPersonShow {
	# Language files to be loaded
	var $langfiles=array('prompt.php','person.php','aufnahme.php');

	# Filename of script to run in fallback state (when something goes wrong)
	var $fallbackfile = '';

	# Default path for fotos. Make sure that this directory exists!
	var $default_photo_path='fotos/registration';
	var $photo_filename='nopic';

	# The PID
	var $pid=0;

	# The text holder in front of output block
	var $pretext='';

	# The text holder after the output block
	var $posttext='';

	# Current encounter number
	var $current_encounter=0;

	# Person data in array
	var $data = array();

	# Person data object
	var $data_obj;

	# Person object
	var $person_obj;

	# Flag if data is loaded
	var $is_loaded;

	# Flag if the data is to be returned only as string
	var $bReturnOnly = FALSE;

	# Internal smarty object
	var $smarty;

	/**
	* Constructor
	*/
	function GuiPersonShow($pid=0, $filename='', $fallbackfile=''){
		global $thisfile, $root_path;
		if(empty($filename)) $this->thisfile = $thisfile;
			else $this->thisfile = $filename;
		if(!empty($fallbackfile)) $this->$fallbackfile = $fallbackfile;
		if(!empty($this->default_photo_path)) $this->default_photo_path = $root_path.$this->default_photo_path;

		include_once($root_path.'include/care_api_classes/class_person.php');
		$this->person_obj=& new Person($pid);

		if($pid){
			$this->pid =$pid;
			return $this->_load();
		}
	}
	/**
	* Sets the file name of the script to run when something goes wrong
	*/
	function setFallbackFile($target){
		$this->fallbackfile = $target;
	}
	/**
	* Sets the PID value
	*/
	function setPID($nr){
		$this->pid = $nr;
		$this->person_obj->setPID($nr);
		return $this->_load();
	}
	/**
	* Returns the death data
	*/
	function DeathDate(){
		if($this->data['death_date']) return $this->data['death_date'];
			else return $this->person_obj->DeathDate();
	}
	/**
	*  Gets the encounter number if person is currently admitted
	*/
	function CurrentEncounter(){
		global $root_path;
		if($this->current_encounter){
			return $this->current_encounter;
		}else{
			return $this->current_encounter=$this->person_obj->CurrentEncounter($this->pid);
		}
	}
	/**
	* (pre)Loads the person registration data.
	*
	* Can be checked if data is loaded with the $this->is_loaded variable
	* @access private
	*/
	function _load(){
		if($this->data_obj=&$this->person_obj->getAllInfoObject()){
				$this->data=$this->data_obj->FetchRow();
			return $this->is_loaded = TRUE;
		}else{
			return $this->is_loaded = FALSE;
		}
	}


	/**
	* Function to generate demographic data dynamically depending on the global config
	*/
	function createTR($ld_text, $input_val, $colspan = 1, $vidata = FALSE){

		ob_start();
			if($vidata) $input_val="<div class=\"vi_data\">$input_val</div>";
			$sBuffer=$ld_text;
			$this->smarty->assign('sItem',$sBuffer);
			$this->smarty->assign('sColSpan2',"colspan=$colspan");
			$this->smarty->assign('sInput',$input_val);
			$this->smarty->display('registration_admission/reg_row.tpl');
			$sBuffer = ob_get_contents();
		ob_end_clean();

		//$this->toggle=!$this->toggle;

		return $sBuffer;
/*
		echo '<tr>
				<td bgColor="#eeeeee" ><FONT SIZE=-1  FACE="Arial,verdana,sans serif">'.$ld_text.':
				</td>
				<td colspan='.$colspan.' bgcolor="#ffffee"><FONT SIZE=-1  FACE="Arial,verdana,sans serif">'.$input_val.'
				</td>
			</tr>';*/
	}

	/**
	* Displays the GUI showing the data
	*/
	function display($pid=0){
		global $HTTP_SESSION_VARS, $root_path, $dbf_nodate, $newdata, $kb_other_his_array, $lang;

		$validdata = TRUE;

		if(!empty($pid)) $this->pid=$pid;

		# Load the language tables
		$lang_tables =$this->langfiles;
		include($root_path.'include/inc_load_lang_tables.php');

		# Load the other hospitals array
		include_once($root_path.'global_conf/other_hospitals.php');

		include_once($root_path.'include/inc_date_format_functions.php');

		include_once($root_path.'include/care_api_classes/class_insurance.php');
		$pinsure_obj=new PersonInsurance($this->pid);

		# Get the global config for person�s registration form
		include_once($root_path.'include/care_api_classes/class_globalconfig.php');

		$GLOBAL_CONFIG = array();

		$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
		$glob_obj->getConfig('person_%');

		//extract($GLOBAL_CONFIG);

		# Start Smarty templating here
		# Create smarty object without initiliazing the GUI (2nd param = FALSE)
		include_once($root_path.'gui/smarty_template/smarty_care.class.php');
		$this->smarty = new smarty_care('common',FALSE);

#echo "pid = ".$this->pid;
		if(empty($this->pid)) {

			$validdata = FALSE;

		}else{

//			if($data_obj=&$this->person_obj->getAllInfoObject()){
//				$this->data=$data_obj->FetchRow();
				if($this->is_loaded){
					extract($this->data);

					# Get related insurance data
					$p_insurance=&$pinsure_obj->getPersonInsuranceObject($this->pid);
					//echo "sql = ".$pinsure_obj->sql;
					if($p_insurance==FALSE) {
						$insurance_show=true;
					} else {
						if(!$p_insurance->RecordCount()) {
							$insurance_show=true;
						} elseif ($p_insurance->RecordCount()==1){
							$buffer= $p_insurance->FetchRow();
							extract($buffer);

							$insurance_show=true;

							#echo "<br>hcare_id = ".$hcare_id;
							# Get insurace firm name
							#$insurance_firm_name=$pinsure_obj->getFirmName($insurance_firm_id);
							#---comment by VAN 090507
							$insurance_firm_name=$pinsure_obj->getFirmName($insurance_firm_id);
						} else {
							$insurance_show=FALSE;
						}
					}

					#--------added by VAN-----
					global $db;
					$sql = "SELECT ci.* FROM care_person_insurance AS ci
								WHERE ci.pid ='".$pid."' LIMIT 1";
					$res=$db->Execute($sql);
					$rsObj=$res->FetchRow();
					$insurance_class_nr = $rsObj['class_nr'];
					$insurance_class_info=$pinsure_obj->getInsuranceClassInfo($insurance_class_nr);

					# Check if person is currently admitted
					$this->current_encounter=$this->person_obj->CurrentEncounter($this->pid);

					# update the record�s history
					if(empty($newdata)) @$this->person_obj->setHistorySeen($HTTP_SESSION_VARS['sess_user_name']);

					# Check whether config foto path exists, else use default path
					$photo_path = (is_dir($root_path.$GLOBAL_CONFIG['person_foto_path'])) ? $GLOBAL_CONFIG['person_foto_path'] : $this->default_photo_path;

				}else{
					$validdata = FALSE;
				}

//			}
		}
		if($validdata){

			include_once($root_path.'include/inc_photo_filename_resolve.php');

			############ Here starts the GUI output ##################

			# Start Smarty templating here
			# Create smarty object without initiliazing the GUI (2nd param = FALSE)

//			include_once($root_path.'gui/smarty_template/smarty_care.class.php');
//			$this->smarty = new smarty_care('common',FALSE);
		#echo " 1fromtemp = ".$fromtemp;

			# Set from width
			$this->smarty->assign('sFormWidth','width="100%"');

			$img_male=createComIcon($root_path,'spm.gif','0');
			$img_female=createComIcon($root_path,'spf.gif','0');

			if(!empty($this->pretext)) $this->smarty->assign('pretext',$this->pretext);

			$this->smarty->assign('LDRegistryNr',$LDRegistryNr);
			$this->smarty->assign('pid',$pid);

			if(file_exists($root_path.'cache/barcodes/pn_'.$pid.'.png')){
				$this->smarty->assign('sBarcodeImg','<img src="'.$root_path.'cache/barcodes/pn_'.$pid.'.png" border=0 width=180 height=35>');
			}else{
				$this->smarty->assign('sBarcodeImg',"<img src='".$root_path."classes/barcode/image.php?code=".$pid."&style=68&type=I25&width=180&height=50&xres=2&font=5&label=2&form_file=pn' border=0 width=0 height=0>");
				$this->smarty->assign('sBarcodeImg',"<img src='".$root_path."classes/barcode/image.php?code=".$pid."&style=68&type=I25&width=180&height=50&xres=2&font=5' border=0 width=180  height=35>");
			}

			$this->smarty->assign('img_source',$img_source);

			# iRowSpanCount counts the rows on the left of the photo image. Begin with 5 because there are 5 static rows.
			$iRowSpanCount = 5;

			$this->smarty->assign('LDRegDate',$LDRegDate);
			$this->smarty->assign('sRegDate',@formatDate2Local($date_reg,$date_format).'<input name="date_reg" type="hidden" value="'.$date_reg.'"');

//			$iRowSpanCount++;
			$this->smarty->assign('LDRegTime',$LDRegTime);
#			$this->smarty->assign('sRegTime',convertTimeToLocal(@formatDate2Local($date_reg,$date_format,0,1)));   # burn commented: April 17, 2007
			$this->smarty->assign('sRegTime',@formatDate2Local($date_reg,$date_format,0,1));   # burn added: April 17, 2007

			if (!$GLOBAL_CONFIG['person_title_hide']){
				$this->smarty->assign('sPersonTitle',$this->createTR( $LDTitle, strtoupper($title)));
				$iRowSpanCount++;
			}

			$this->smarty->assign('sNameLast',$this->createTR($LDLastName,strtoupper($name_last),1,TRUE));


			$this->smarty->assign('sSuffix',$this->createTR('Suffix',strtoupper($suffix),1,TRUE));


			//$iRowSpanCount++;

			# If person is dead show a black cross
			if($death_date&&$death_date!=$dbf_nodate) $sCross = '&nbsp;<img '.createComIcon($root_path,'blackcross_sm.gif','0','',TRUE).'>';
				else $sCross ='';

			$this->smarty->assign('sNameFirst',$this->createTR($LDFirstName,strtoupper($name_first).$sCross,1,TRUE));

			if (!$GLOBAL_CONFIG['person_name_2_hide']&&$name_2){
				$this->smarty->assign('sName2',$this->createTR($LDName2,strtoupper($name_2)));
				$iRowSpanCount++;
			}
			if (!$GLOBAL_CONFIG['person_name_3_hide']&&$name_3){
				$this->smarty->assign('sName3',$this->createTR($LDName3,strtoupper($name_3)));
				$iRowSpanCount++;
			}
			if (!$GLOBAL_CONFIG['person_name_middle_hide']&&$name_middle){
				$this->smarty->assign('sNameMiddle',$this->createTR($LDNameMid,strtoupper($name_middle)));
				$iRowSpanCount++;
			}
			if (!$GLOBAL_CONFIG['person_name_maiden_hide']&&$name_maiden){
				$this->smarty->assign('sNameMaiden',$this->createTR($LDNameMaiden,strtoupper($name_maiden)));
				$iRowSpanCount++;
			}
			if (!$GLOBAL_CONFIG['person_name_others_hide']&&$name_others){
				$this->smarty->assign('sNameOthers',$this->createTR($LDNameOthers,strtoupper($name_others)));
				$iRowSpanCount++;
			}

			# Set the rowspan value for the photo image <td>
			$this->smarty->assign('sPicTdRowSpan',"rowspan=$iRowSpanCount");

			$this->smarty->assign('LDBday',"$LDBday");

			if($death_date&&$death_date!=$dbf_nodate){
				$this->smarty->assign('sCrossImg',$sCross);
				$this->smarty->assign('sDeathDate','<font color="#000000">'.@formatDate2Local($death_date,$date_format).'</font>');
			}

			# burn added : March 26, 2007
			$agewobday = $age;
			if($date_birth){
				$sBdayBuffer = @formatDate2Local($date_birth,$date_format);
				if (!($age = $this->person_obj->getAge($sBdayBuffer))){
					#$age = '';
					$this->smarty->assign('segAge','<span class="vi_data">'. $agewobday.' </span> YEAR(S) OLD');
					$sBdayBuffer = 'Not Available';
				}else{
						#$this->smarty->assign('segAge','<span class="vi_data">'.$age.' </span> year(s) old');
						$this->smarty->assign('segAge','<span class="vi_data">'.$age.' </span> YEAR(S) OLD');
				}
			}


#			$this->smarty->assign('sBdayInput','<div class="vi_data">'.@formatDate2Local($date_birth,$date_format).'</div>');   # burn commented: March 26, 2007
			$this->smarty->assign('sBdayInput','<span class="vi_data">'.strtoupper($sBdayBuffer).'</span>');   # burn added: March 26, 2007

			if (!$GLOBAL_CONFIG['person_place_birth_hide'] && trim($place_birth)) {
				$this->smarty->assign('LDBirthplace',"$segBirthplace");
				$this->smarty->assign('sBirthplace',strtoupper($place_birth));
			}

			//added by genz

            if (!$GLOBAL_CONFIG['Contact_No_hide'] && trim($contactNo)) {
				$this->smarty->assign('LDContactNo',"$segContactNo");
				$this->smarty->assign('sContactNo',strtoupper($contactNo));
			}
			//ended by genz
            
            #added by VAN 06-06-2013
            if ($is_temp_bdate)
                $sTempBday = 'Temp Birthday**';
            else
                $sTempBday = '';    
                
            $this->smarty->assign('sTempBday',$sTempBday);
            
            if ($fromtemp){
                $showBirth=1;
            }else{
                $showBirth=0;
            }

        $this->smarty->assign('sIsNewborn',$showBirth);
            
            $this->smarty->assign('LDBirthTime',"Birth TIme");
            $birth_time = date("h:i A", strtotime($birth_time));
            $this->smarty->assign('sBirthTime',$birth_time);
            
			$this->smarty->assign('LDSex', "$LDSex");

			#added by VAN 05-10-08
			if ($senior_ID) {
				$this->smarty->assign('LDSenior','Senior Citizen ID No.');
				$this->smarty->assign('sSenior',trim($senior_ID));
			}

			if ($veteran_ID) {
				$this->smarty->assign('LDVeterans','Veteran\'s ID No.');
				$this->smarty->assign('sVeterans',trim($veteran_ID));
			}
			#--------------------

			if($sex=="m") $this->smarty->assign('LDMale','<div class="vi_data">'.strtoupper($LDMale).'</div>');
				elseif($sex=="f") $this->smarty->assign('LDFemale','<div class="vi_data">'.strtoupper($LDFemale).'</div>');

			if (!$GLOBAL_CONFIG['person_bloodgroup_hide'] && trim($blood_group)) {
				// KB: make blood group hideable
				$this->smarty->assign('LDBloodGroup',$LDBloodGroup);
				$buf='LD'.trim($blood_group);
				$this->smarty->assign('sBGAInput',$$buf);
			}

			if (!$GLOBAL_CONFIG['person_civilstatus_hide'] && trim($civil_status)) {
				$this->smarty->assign('LDCivilStatus',$LDCivilStatus);

				if($civil_status=="child") $sCSBuffer =  $LDChild;
					elseif($civil_status=="single") $sCSBuffer =  $LDSingle;
						elseif($civil_status=="married") $sCSBuffer =  $LDMarried;
							elseif($civil_status=="divorced") $sCSBuffer =  $LDDivorced;
								elseif($civil_status=="widowed") $sCSBuffer = $LDWidowed;
									elseif($civil_status=="separated") $sCSBuffer =  $LDSeparated;

				$this->smarty->assign('sCSSingleInput',$sCSBuffer);
			}

			$this->smarty->assign('LDAddress',"$LDAddress:");

			$this->smarty->assign('LDStreet',"$LDStreet");

			$this->smarty->assign('sStreetInput',strtoupper($addr_str));

			$this->smarty->assign('LDStreetNr',"$LDStreetNr");

			$this->smarty->assign('sStreetNrInput',strtoupper($addr_str_nr));

			$this->smarty->assign('LDTownCity',"$LDTownCity");
			$this->smarty->assign('sTownCityInput',strtoupper($addr_citytown_name));

			$this->smarty->assign('LDZipCode',"$LDZipCode");
			$this->smarty->assign('sZipCodeInput',$addr_zip);
					$this->smarty->assign('LDInsuranceBurn',"$LDInsuranceBurn");   # burn added: August 30, 2006
					$this->smarty->assign('LDInsuranceClass',"$LDInsuranceClass");   # burn added: August 30, 2006

			if (!$GLOBAL_CONFIG['person_insurance_hide']) {
				#if (!$GLOBAL_CONFIG['person_insurance_1_nr_hide']&&$insurance_show&&$insurance_nr){
				#if (!$GLOBAL_CONFIG['person_insurance_1_nr_hide']&&$insurance_show){
					 /*
					$this->smarty->assign('bShowInsurance',TRUE);

					$this->smarty->assign('sInsuranceNr',$this->createTR($LDInsuranceNr,$insurance_nr,2));

					$buffer=$insurance_class_info['LD_var'];
					if(isset($$buffer)&&!empty($$buffer)) $this->smarty->append('sInsClasses',$$buffer);
							else $this->smarty->append('sInsClasses',$insurance_class_info['name']);

					$this->smarty->assign('LDInsuranceCo',$LDInsuranceCo);
					$this->smarty->assign('sInsCoNameInput',$insurance_firm_name);

					$this->createTR($LDInsuranceCo.' 1',$insurance_firm_name,2);
					*/
				#}
					#-------------added by VAN------------
				#else{
					$sql2 = "SELECT ci.* FROM care_person_insurance AS ci
								WHERE ci.pid ='".$pid."' LIMIT 1";
					$res2=$db->Execute($sql2);
					$rsObj=$res2->FetchRow();
					$row = $res2->RecordCount();

					if ($row!=0){
						$this->smarty->assign('bShowInsurance',TRUE);
						$buffer=$insurance_class_info['LD_var'];
						if(isset($$buffer)&&!empty($$buffer)) $this->smarty->append('sInsClasses',$$buffer);
								else $this->smarty->append('sInsClasses',$insurance_class_info['name']);

						if ($errorinsuranceclass) $this->smarty->assign('LDInsuranceNr',"<font color=red>$LDInsuranceList</font>");
						else  $this->smarty->assign('LDInsuranceNr',$LDInsuranceList);
						$this->smarty->assign('sOrderItems',"
							<tr>
								<td colspan=\"10\">Insurance list is currently empty...</td>
							</tr>");

						# Note: make a class function for this part later
						$result = $pinsure_obj->getPersonInsuranceItems($pid);
						$rows=array();
						while ($row=$result->FetchRow()) {
							$rows[] = $row;
						}
						foreach ($rows as $i=>$row) {
							if ($row) {
								$count++;
								$alt = ($count%2)+1;

								$src .= '
									<tr class="wardlistrow'.$alt.'" id="row'.$row['hcare_id'].'">
										<input type="hidden" name="items[]" id="rowID'.$row['hcare_id'].'" value="'.$row['hcare_id'].'" />
										<input type="hidden" name="nr[]" id="rowNr'.$row['hcare_id'].'" value="'.$row['insurance_nr'].'" />
										<input type="hidden" name="is_principal[]" id="rowis_principal'.$row['hcare_id'].'" value="'.$row["is_principal"].'" />
										<td class="centerAlign"><img src="../../images/insurance.gif" border="0"/>&nbsp;'.$count.'</td>
										<td id="name'.$row['hcare_id'].'">'.$row['firm_id'].'</td>
										<td width="25%" align="right" id="inspin'.$row['hcare_id'].'">'.$row['insurance_nr'].'</td>
										<td width="18%" class="centerAlign" id="insprincipal'.$row['hcare_id'].'">'.(($row["is_principal"])?'YES':'NO').'</td>
										<td></td>
									</tr>
								';
							}
						}
						if (($src) && ($insurance_class_nr!=3))
							$this->smarty->assign('sOrderItems',$src);
						#--------------------------------------------
					#}
				}
			}

			include_once($root_path.'include/care_api_classes/class_person.php');
			# Create the new person object
			$person_obj=& new Person($pid);
			/* Create the helper class for the country table */
			include_once($root_path.'include/care_api_classes/class_address.php');
			# Create the new address object
			$address_country = new Address('country');

			if (!$GLOBAL_CONFIG['person_phone_1_nr_hide']&&$phone_1_nr){
				$this->smarty->assign('sPhone1',$this->createTR($LDPhone.' 1',$phone_1_nr,2));
			}
			if (!$GLOBAL_CONFIG['person_phone_2_nr_hide']&&$phone_2_nr){
				$this->smarty->assign('sPhone2',$this->createTR($LDPhone.' 2',$phone_2_nr,2));
			}
			if (!$GLOBAL_CONFIG['person_cellphone_1_nr_hide']&&$cellphone_1_nr){
				$this->smarty->assign('sCellPhone1',$this->createTR($LDCellPhone.' 1',$cellphone_1_nr,2));
			}
			if (!$GLOBAL_CONFIG['person_cellphone_2_nr_hide']&&$cellphone_2_nr){
				$this->smarty->assign('sCellPhone2',$this->createTR($LDCellPhone.' 2',$cellphone_2_nr,2));
			}
			if (!$GLOBAL_CONFIG['person_fax_hide']&&$fax){
				$this->smarty->assign('sFax',$this->createTR($LDFax,$fax,2));
			}
			if (!$GLOBAL_CONFIG['person_email_hide']&&$email){
				$this->smarty->assign('sEmail',$this->createTR($LDEmail,"<a href=\"mailto:$email\">$email</a>",2));
			}

			# Retrieves record set for country of citizenship
			if ($country_obj = $address_country->getAllAddress("WHERE country_code='$citizenship'")){
				if ($country_row = $country_obj->FetchRow())
					$citizenship = $country_row['country_name'];
			}
			if (!$GLOBAL_CONFIG['person_citizenship_hide']&&$citizenship){
				$this->smarty->assign('sCitizenship',$this->createTR($LDCitizenship,strtoupper($citizenship),2));
			}
			if (!$GLOBAL_CONFIG['person_sss_nr_hide']&&$sss_nr){
				$this->smarty->assign('sSSSNr',$this->createTR($LDSSSNr,$sss_nr,2));
			}
			if (!$GLOBAL_CONFIG['person_nat_id_nr_hide']&&$nat_id_nr){
				$this->smarty->assign('sNatIdNr',$this->createTR($LDNatIdNr,$nat_id_nr,2));
			}

			# Retrieves record set for religion
			if ($religion_obj=$person_obj->getReligion("religion_nr=$religion")){
				if ($religion_row = $religion_obj->FetchRow())
					$religion = $religion_row['religion_name'];
			}
			if (!$GLOBAL_CONFIG['person_religion_hide']&&$religion){
				$this->smarty->assign('sReligion',$this->createTR($LDReligion,strtoupper($religion),2));
			}

			#edited by VAN 05-09-08
			/*
			if (!$GLOBAL_CONFIG['person_ethnic_orig_hide']&&$ethnic_orig){
				$this->smarty->assign('LDEthnicOrig',$LDEthnicOrigin);
				$this->smarty->assign('sEthnicOrigInput',$ethnic_orig_txt);
			}
			*/

			#if (!$GLOBAL_CONFIG['person_ethnic_orig_hide']&&$is_indigent){
				#$this->smarty->assign('sEthnicOrig',$this->createTR($LDIndigent,strtoupper($is_indigent),2));
			#}

			# Retrieves record set for ethnic origin
			if ($ethnic_orig_obj=$person_obj->getEthnic_orig("nr=$ethnic_orig")){
				if ($ethnic_orig_row = $ethnic_orig_obj->FetchRow())
					$ethnic_orig = $ethnic_orig_row['name'];
			}
			if (!$GLOBAL_CONFIG['person_ethnic_orig_hide']&&$ethnic_orig){
				$this->smarty->assign('sEthnicOrig',$this->createTR($LDEthnicOrigin,strtoupper($ethnic_orig),2));
			}

			# Retrieves record set of occupation
			if ($occupation_obj = $person_obj->getOccupation("occupation_nr=$occupation")){
				if ($occupation_row = $occupation_obj->FetchRow())
					$occupation = $occupation_row['occupation_name'];
			}
			if (!$GLOBAL_CONFIG['person_occupation_hide'] && $occupation){
				$this->smarty->assign('sOccupation',$this->createTR($LDOccupation,strtoupper($occupation),2));

				#$this->smarty->assign('sEmployer',$this->createTR($segEmployer,strtoupper($employer),2));
			}

			if (!$GLOBAL_CONFIG['person_employer_hide'] && $employer){
				$this->smarty->assign('sEmployer',$this->createTR($segEmployer,strtoupper($employer),2));
			}
			/*
			if ( (!$GLOBAL_CONFIG['person_mother_name_hide'] && $mother_name) ||
					(!$GLOBAL_CONFIG['person_father_name_hide'] && $father_name) ||
					(!$GLOBAL_CONFIG['person_spouse_name_hide'] && $spouse_name) ||
					(!$GLOBAL_CONFIG['person_guardian_name_hide'] && $guardian_name) ){
						$this->smarty->assign('sFamilyBackground',$segFamilyBackground);
			}
			*/
			#edited by VAN 05-20-08
			if ( (!$GLOBAL_CONFIG['person_mother_name_hide'] && ($mother_fname || $mother_maidenname || $mother_mname || $mother_lname)) ||
					(!$GLOBAL_CONFIG['person_father_name_hide'] && ($father_fname || $father_mname || $father_lname)) ||
					(!$GLOBAL_CONFIG['person_spouse_name_hide'] && $spouse_name) ||
					(!$GLOBAL_CONFIG['person_guardian_name_hide'] && $guardian_name) ){
						$this->smarty->assign('sFamilyBackground',$segFamilyBackground);
			}

			#if (!$GLOBAL_CONFIG['person_mother_name_hide'] && $mother_name){
			#added by VAN 05-19-08
			#$this->smarty->assign('segPersonInput',"false");
			if (!$GLOBAL_CONFIG['person_mother_name_hide'] && ($mother_fname || $mother_maidenname || $mother_mname || $mother_lname)){
				#$this->smarty->assign('sMother',$this->createTR($segMotherName,strtoupper($mother_name),2));
				#edited by VAN 05-19-08
				$this->smarty->assign('sMother',$segMotherName);
				$mother_name = strtoupper($mother_fname)." ".strtoupper(trim($mother_maidenname))." ".strtoupper(trim($mother_mname))." ".strtoupper(trim($mother_lname));
				$this->smarty->assign('sMother_name',$mother_name);
			}
			#if (!$GLOBAL_CONFIG['person_father_name_hide'] && $father_name){
			#if (!$GLOBAL_CONFIG['person_father_name_hide'] && (($father_fname || $father_mname || $father_lname)|| (($father_fname!=" ") || ($father_mname!=" ") || ($father_lname!=" ")))){
			if (!$GLOBAL_CONFIG['person_father_name_hide'] && (($father_fname || $father_mname || $father_lname))){
				#edited by VAN 05-19-08
				#$this->smarty->assign('sFather',$this->createTR($segFatherName,strtoupper($father_name),2));
				$this->smarty->assign('sFather',$segFatherName);
				$father_name = strtoupper(trim($father_fname))." ".strtoupper(trim($father_mname))." ".strtoupper(trim($father_lname));

				$father_name = trim($father_name);
				if (empty($father_name))
					$father_name = "NOT INDICATED";

				$this->smarty->assign('sFather_name',$father_name);

			}
			if (!$GLOBAL_CONFIG['person_spouse_name_hide'] && $spouse_name){
				$this->smarty->assign('sSpouse',$this->createTR($segSpouseName,strtoupper($spouse_name),2));
			}
			if (!$GLOBAL_CONFIG['person_guardian_name_hide'] && $guardian_name){
				$this->smarty->assign('sGuardian',$this->createTR($segGuardianName,strtoupper($guardian_name),2));
			}

			if (!$GLOBAL_CONFIG['person_other_his_nr_hide']){
				$other_hosp_list = $this->person_obj->OtherHospNrList();
				$iHospCount = sizeof($other_hosp_list);

				if($iHospCount) {
					$this->smarty->assign('bShowOtherHospNr',TRUE);

					$this->smarty->assign('LDOtherHospitalNr',$LDOtherHospitalNr);

					$sOtherNrBuffer='';
					if(is_array($other_hosp_list) && $iHospCount){

						foreach( $other_hosp_list as $k=>$v ){
							$sOtherNrBuffer.="<b>".$kb_other_his_array[$k].":</b> ".$v."<br />\n";
						}
					}
					$this->smarty->assign('sOtherNr',$sOtherNrBuffer);
				}
			}

			//added by genz
			$this->smarty->assign('LDContactNo',"$segContactNo"); #CDE\
			$contactNo = $person_obj->getcontactno($pid);
			if($contactNo){
				$this->smarty->assign('sContactNo',$contactNo);
			}
			//ended by genz

			$this->smarty->assign('LDRegBy',$LDRegBy);
			if(empty($modify_id)) $buffer=$create_id; else $buffer=$modify_id;

			$this->smarty->assign('sRegByInput',$buffer);

		}else{
			$this->smarty->assign('pretext','Invalid PID number or the data is not available from the databank! Please report this to <a  href="mailto:info@care2x.org">info@care2x.org</a>. Thank you.');
		}

		#require_once('address_view.php');
		require_once($root_path.'modules/registration_admission/address_view.php');
		$this->smarty->assign('segAddressNew',"$segAddressNew");

		# If data is to be returned only, buffer output, get the buffer contents, end and clean buffer and return contents.
		if($this->bReturnOnly){
			ob_start();
				$this->smarty->display('registration_admission/reg_form.tpl');
			$sTemp = ob_get_contents();
			ob_end_clean();
			return $sTemp;
		}else{
			$this->smarty->display('registration_admission/reg_form.tpl');
			return TRUE;
		}
	} // end of function

	/**
	* Creates the  data but returns it as a string instead of outputting it
	*/
	function create(){
		$this->bReturnOnly=TRUE;
		return $this->display();
	}
} // end of class
?>
