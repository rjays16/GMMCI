<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path."modules/registration_admission/ajax/seg_insurance.common.php");
/**
* CARE2X Integrated Hospital Information System beta 2.0.1 - 2004-07-04
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org,
*
* See the file "copy_notice.txt" for the licence notice
*/
#echo "start";

#--------------- EDITED BY VANESSA -----------------------
$lang_tables[]='departments.php';
$lang_tables[]='prompt.php';
$lang_tables[]='help.php';
$lang_tables[]='person.php';
define('LANG_FILE','aufnahme.php');

#commented by VAN 01-25-08
$local_user='aufnahme_user';
#added by VAN 01-25-08

require($root_path.'include/inc_front_chain_lang.php');

require_once($root_path.'include/care_api_classes/class_person.php');

require_once($root_path.'include/care_api_classes/class_insurance.php');
require_once($root_path.'include/care_api_classes/class_encounter.php');
include_once($root_path.'include/care_api_classes/class_department.php');

$xajax->printJavascript($root_path.'classes/xajax_0.5');

$dept_obj=new Department;

#-------added 03-07-07------------
global $db;

	if (!empty($HTTP_SESSION_VARS['sess_login_userid']))
		$seg_user_name = $HTTP_SESSION_VARS['sess_login_userid'];
	else
		$seg_user_name = $HTTP_SESSION_VARS['sess_temp_userid'];

	$dept_belong = $dept_obj->getUserDeptInfo($seg_user_name);

	$frombilling = $_GET['frombilling'];

	#Added by Jarel 11-27/2013 For All Caserates PHIC
	$bill_type = $_GET['bill_type'];

	if($bill_type)
		$url = '../../modules/billing_new/seg-reg-insurance-tray.php?';
	else
		$url = 'seg-reg-insurance-tray.php?';

	#added by VAN 01-25-08

	if ($frombilling)
		$dept_belong['id'] = "Admission";


$thisfile=basename(__FILE__);

if($origin=='patreg_reg') $breakfile = 'patient_register_show.php'.URL_APPEND.'&pid='.$pid;
	#elseif($HTTP_COOKIE_VARS["ck_login_logged".$sid]) $breakfile = $root_path.'main/startframe.php'.URL_APPEND;
	elseif($HTTP_COOKIE_VARS["ck_login_logged".$sid]) $breakfile = $root_path.'modules/registration_admission/aufnahme_daten_such.php'.URL_APPEND;
		elseif(!empty($HTTP_SESSION_VARS['sess_path_referer'])) $breakfile=$root_path.$HTTP_SESSION_VARS['sess_path_referer'].URL_APPEND.'&pid='.$pid;
			else $breakfile = "aufnahme_pass.php".URL_APPEND."&target=entry";

$newdata=1;

$error=0;

if(!isset($pid)) $pid=0;
if(!isset($encounter_nr)) $encounter_nr=0;
if(!isset($mode)) $mode='';
if(!isset($forcesave)) $forcesave=0;
if(!isset($update)) $update=0;

if(!session_is_registered('sess_pid')) session_register('sess_pid');
if(!session_is_registered('sess_full_pid')) session_register('sess_full_pid');
if(!session_is_registered('sess_en')) session_register('sess_en');
if(!session_is_registered('sess_full_en')) session_register('sess_full_en');

$patregtable='care_person';  // The table of the patient registration data

$dbtable='care_encounter'; // The table of admission data

/* Create new person's insurance object */
$pinsure_obj=new PersonInsurance($pid);
/* Get the insurance classes */
$insurance_classes=&$pinsure_obj->getInsuranceClassInfoObject('class_nr, name, LD_var AS "LD_var"');

/* Create new person object */
$person_obj=new Person($pid);

// Get address of patient ...
$paddr = $person_obj->getPrincipalAddr($pid);
if (!isset($brgynr)) $brgynr = $paddr['brgy_nr'];
if (!isset($munnr)) $munnr = $paddr['mun_nr'];

/* Create encounter object */
$encounter_obj=new Encounter($encounter_nr);
/* Get all encounter classes */

#added by VAN 04-29-08
if($pid!='' || $encounter_nr!=''){

				if ($pid != '')
				{
				 $p_insurance=&$pinsure_obj->getPersonInsuranceObject($pid);
			 if($p_insurance==false) {
				$insurance_show=true;
			 } else {
				if(!$p_insurance->RecordCount()) {
						$insurance_show=true;

				} elseif ($p_insurance->RecordCount()>=1){
						$buffer= $p_insurance->FetchRow();

					extract($buffer);

										if (!isset($insurance_class_nr)) $insurance_class_nr = $class_nr;
						$insurance_show=true;
						$insurance_firm_name=$pinsure_obj->getFirmName($insurance_firm_id);
								} else { $insurance_show=false; }
			 }
			if (($mode=='save') || ($forcesave!=''))
						{
							 if(!$forcesave)
							 {
										//clean and check input data variables
						/**
						*  $error = 1 will cause to show the "save anyway" override button to save the incomplete data
						*  $error = 2 will cause to force the user to enter a data in an input element (no override allowed)
						*/
										$encoder=trim($encoder);
						if($encoder=='') $encoder=$HTTP_SESSION_VARS['sess_user_name'];


								}

								 if(!$error)
							 {
						if($update || $encounter_nr)
						{
							$itemno=$itemname;
							$HTTP_POST_VARS['modify_id']=$encoder;
//									if($dbtype == 'mysql'){
//										$HTTP_POST_VARS['history']= "CONCAT(history,\"\n Update: ".date('Y-m-d H:i:s')." = $encoder\")";
//									}else{
//										$HTTP_POST_VARS['history']= "(history || '\n Update: ".date('Y-m-d H:i:s')." = $encoder')";
//									}
							$HTTP_POST_VARS['history'] = $encounter_obj->ConcatHistory("\n Update: ".date('Y-m-d H:i:s')." = $encoder");

														if(isset($HTTP_POST_VARS['encounter_nr'])) unset($HTTP_POST_VARS['encounter_nr']);
							if(isset($HTTP_POST_VARS['pid'])) unset($HTTP_POST_VARS['pid']);

							#-------added by VAN 09-07-07------------
							if ($_POST["items"]==NULL){
								$HTTP_POST_VARS['insurance_class_nr']=3;
							}
							#-------------------------------

							$encounter_obj->setDataArray($HTTP_POST_VARS);

							$pinsure_obj->startTrans();
							$ok = true;

							if($encounter_obj->updateEncounterFromInternalArray($encounter_nr))
							{
								#if ($dept_belong['id']=="Admission"){
									#---------added by VAN 090107------
									#----------INSURANCE--------
										if ($insurance_class_nr!=3){
											#-----with insurance---
											if ($_POST["items"]!=NULL){
												$bulk_hcare = array();
												$bulk_insurance_nr = array();
												foreach (array_unique($_POST["items"]) as $i=>$v) {
													#------------------hcare_id, insurance_nr, is principal holder-----
													$bulk[] = array($_POST["items"][$i],$_POST["nr"][$i],(($_POST["is_principal"][$i] == '' || is_null($_POST["is_principal"][$i])) ? 0 : $_POST["is_principal"][$i]));
													$bulk_hcare[] = array($_POST["items"][$i]);
													$current_array .= $_POST["items"][$i].",";
												}

												$current_array = substr($current_array,0,strlen($current_array)-1);

												$ok = $pinsure_obj->clearInsuranceList($encounter_nr);	#clear seg_encounter_insurance table
												if ($ok) {
													$delete_result="DELETE FROM care_person_insurance WHERE hcare_id IN (".$current_array.") AND pid = '$pid'";
													$ok = $db->Execute($delete_result);

												}
												else {
													$sWarning = $pinsure_obj->getErrorMsg();
												}

												if ($ok) {
														foreach($bulk_hcare as $k => $v) {
																$ok = $pinsure_obj->addInsurance($encounter_nr, $v , $encoder, date('YmdHis'));
																if ($ok) $ok = $pinsure_obj->addInsurance_reg($pid, $bulk[$k], $encoder, date('YmdHis'), $insurance_class_nr);

																// added by LST ... 05.14.2012 ----------------
																if (($_POST['is_updated'][$k] == "0") && ($bulk[$k][2] == '0') && ($_POST['orig_isprincipal'][$k] != $bulk[$k][2])) {
																		// If not updated and current person is not principal holder, force error ...
																		$ok = false;
																		$sWarning = "Principal holder's name not specified!";
																		break;
																}

																if ($bulk[$k][2] != '0') {
																    // ... meaning set as principal holder ...
																    $ok = $pinsure_obj->clearOtherPrincipalHolder($pid, $bulk[$k][1]);
																}
																// --------------------------------------------

																if (!$ok) {
																	$sWarning = $pinsure_obj->getErrorMsg();
																	break;
																}

																if (!empty($_POST['fnr'][$k]) && !empty($_POST['inr'][$k]) && ($bulk[$k][2] == '0')) {
//																		if ($bulk[$k][2] == '0') {

																		// Check the lastname, firstname and middlename of principal holder if already in person registry ...
																		$tmppid = $pinsure_obj->getPrincipalHolderPID($_POST['last_name'][$k], $_POST['first_name'][$k], $_POST['middle_name'][$k], $_POST['inr'][$k]);
																		if ($tmppid) {
																				$_POST['infosrc'][$k] = '0';
																				$ok = $pinsure_obj->updateFoundPrincipalHolderByName($tmppid, $_POST['inr'][$k]);
																				if (!$ok) {
																					$sWarning = $pinsure_obj->ErrorMsg();
																					break;
																				}
																		}

//																		}

																		/*if ( ($_POST['infosrc'][$k] == '2') || ($pid == $_POST['principal'][$k]) ) {
																			// Update name of principal holder in 'seg_insurance_member_info' table ...
																			$details_array = array('hcare_id'=>$_POST['fnr'][$k],
																														 'insurance_nr'=>$_POST['inr'][$k],
																														 'member_lname'=>$_POST['last_name'][$k],
																														 'member_fname'=>$_POST['first_name'][$k],
																														 'member_mname'=>$_POST['middle_name'][$k],
																														 'street_name'=>$_POST['street'][$k],
																														 'brgy_nr'=>(isset($_POST['barangay'][$k]) && ($_POST['barangay'][$k] != "") ? $_POST['barangay'][$k] : "NULL"),
																														 'mun_nr'=>(isset($_POST['municipality'][$k]) && ($_POST['municipality'][$k] != "") ? $_POST['municipality'][$k] : "NULL"));
																			$ok = $pinsure_obj->save_member_details_info($details_array, $pid);
																			if (!$ok) {
																				$sWarning = $pinsure_obj->sql."\n".$pinsure_obj->getErrorMsg();
																				break;
																			}
																		}*/
																		else {
																			$pholder = $pinsure_obj->getPrincipalHolder($_POST['inr'][$k], $_POST['fnr'][$k]);
																			$holderpid = ($pholder) ? $pholder['pid'] : '';
//																			if (($_POST['infosrc'][$k] == '1') && ($pid != $_POST['principal'][$k])) {
																			if ( ($_POST['infosrc'][$k] == '1') && ($pid != $holderpid) && ($holderpid != '') ) {
																				// Update name of principal holder in 'care_person' table ...
																				$details_array = array('name_last'=>$_POST['last_name'][$k],
	                                                             'name_first'=>$_POST['first_name'][$k],
	                                                             'name_middle'=>$_POST['middle_name'][$k]);
//																				$ok = $person_obj->updateNameofPerson($_POST['principal'][$k], $details_array);
																				$ok = $person_obj->updateNameofPerson($holderpid, $details_array);
																				if (!$ok) {
																					$sWarning = $person_obj->db_error_msg;
																					break;
																				}
																			}
																		} // ... else part ...
																}
														}
												}
//                                                        if ($ok) {
//                                                            /** added by Omick **/
//                                                            foreach ($_POST['fnr'] as $key => $value) {
//                                                                $details_array = array('hcare_id'=>$_POST['fnr'][$key],
//                                                                                       'insurance_nr'=>$_POST['inr'][$key],
//                                                                                       'member_lname'=>$_POST['last_name'][$key],
//                                                                                       'member_fname'=>$_POST['first_name'][$key],
//                                                                                       'member_mname'=>$_POST['middle_name'][$key],
//                                                                                       'street_name'=>$_POST['street'][$key],
//                                                                                       'brgy_nr'=>$_POST['barangay'][$key],
//                                                                                       'mun_nr'=>$_POST['municipality'][$key]);
//                                                                $pinsure_obj->save_member_details_info($details_array, $pid);
//                                                            }
//                                                        }
											} else {
												$ok = $pinsure_obj->clearInsuranceList($encounter_nr); #clear seg_encounter_insurance table
												if ($ok) {
													$delete_result="DELETE FROM care_person_insurance WHERE hcare_id IN (".$insurance_array_prev.") AND pid = '".$pid."'";
													$ok = $db->Execute($delete_result);
												}
											}
										}elseif ($insurance_class_nr==3){
											#-----self-pay------
											$ok = $pinsure_obj->clearInsuranceList($encounter_nr);		#clear seg_encounter_insurance table
											#$pinsure_obj->clearInsuranceList_reg($pid);         #clear care_person_insurance table
											#$delete_result="DELETE FROM care_person_insurance WHERE hcare_id IN (".$insurance_array_prev.") AND pid = ".$pid;
											#$ok = $db->Execute($delete_result);
										}
								#}
									#---------------------------

										if (!$ok) $pinsure_obj->failTrans();
										$pinsure_obj->completeTrans();

									#header("Location: show_opd_clinical_form.php".URL_REDIRECT_APPEND."&encounter_nr=$encounter_nr&target=_blank");

								 # header("Location: aufnahme_daten_zeigen.php".URL_REDIRECT_APPEND."&encounter_nr=$encounter_nr&origin=admit&target=entry&newdata=$newdata&update=1&cond_code=$cond_code&disp_code=$disp_code&result_code=$result_code&enc_type=".$patient_enc['encounter_type']);
								 		if ($bill_type&&$ok) {
								 			echo "<script type=\"text/javascript\">window.parent.assignInsurance('".$_POST['inr'][$k]."');</script>";
											exit;
								 		}elseif ($ok) {
											echo "<script type=\"text/javascript\">window.parent.myClick();</script>";
											exit;
								 		}
								}
								else {
									$ok = false;
								}

								if (!$ok) $pinsure_obj->failTrans();
								$pinsure_obj->completeTrans();

						}else{ #if($update || $encounter_nr)
								#nothing to do
														#die("sql = ".$encounter_obj->sql);
					 }// end of if(update) else()
									}	// end of if($error)
						 } // end of if($mode)
				#echo "sql = ".$encounter_obj->sql;
				}elseif($encounter_nr!='') {
				/* Load encounter data */

				$encounter_obj->loadEncounterData();
				if($encounter_obj->is_loaded) {
							$zeile=&$encounter_obj->encounter;

					//load data
					extract($zeile);

							 // Get insurance firm name
						$insurance_firm_name=$pinsure_obj->getFirmName($insurance_firm_id);
					}

		}

	#----OPD-----------------
		/*
	$person_obj->setPID($pid);
	if($data=&$person_obj->BasicDataArray($pid)){
		extract($data);
	}
	*/
}

# Prepare onLoad JS code
#if(!$encounter_nr && !$pid) $sOnLoadJs ='onLoad="if(document.searchform.searchkey.focus) document.searchform.searchkey.focus();"';

# Start Smarty templating here
 /**
 * LOAD Smarty
 */
 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

# Title in the toolbar
 $smarty->assign('sToolbarTitle',$headframe_title);

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('admission_how2new.php')");

# $smarty->assign('breakfile',$breakfile);
	if ($popUp!='1'){
		 # href for the close button
		 $smarty->assign('breakfile',$breakfile);
	}else{
		# CLOSE button for pop-ups
		 $smarty->assign('breakfile','javascript:window.parent.cClick();');
		$smarty->assign('pbBack','');
	}

 # Window bar title
 $smarty->assign('title',$headframe_title);

 # Onload Javascript code
 $smarty->assign('sOnLoadJs',$sOnLoadJs);

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('person_admit.php')");

 # Hide the return button
 $smarty->assign('pbBack',FALSE);

 # Start collectiong extra Javascript code
 ob_start();

# If  pid exists, output the form checker javascript
if(isset($pid) && $pid){
?>

<!---------added by VAN----------->
<!-- OLiframeContent(src, width, height) script:
 (include WIDTH with its parameter equal to width, and TEXTPADDING,0, in the overlib call)
-->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>

<!-- Core module and plugins:
-->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery-1.8.2.js"></script>
<script type="text/javascript" src="js/reg-insurance-gui.js?t=<?=time()?>"></script>

<!-------------------------------------------->

<script  language="javascript">
<!--

/*
	This will trim the string i.e. no whitespaces in the
	beginning and end of a string AND only a single
	whitespace appears in between tokens/words
	input: object
	output: object (string) value is trimmed
*/
function trimString(objct){
	objct.value = objct.value.replace(/^\s+|\s+$/g,"");
	objct.value = objct.value.replace(/\s+/g,"");
}/* end of function trimString */

//------------added by van 03-16-07----------

function valButton(btn) {
	var cnt = -1;
	var temp = document.getElementsByName(btn);
	if (!$(btn))	{
		return null;
	}

	for (var i=temp.length-1; i > -1; i--) {
		if (temp[i].checked) {
			cnt = i;
			i = -1;
		}
		}

	if (cnt > -1) return temp[cnt].value;
		else return null;
}


function chkform(d) {

}


function preset(){
	//alert("preset");
	var d = document.aufnahmeform;
	var update = <?php echo $update; ?>;
	var dept_belong = "<?php echo $dept_belong['id']; ?>";

	if ((update == 1)&&(dept_belong=="Admission")&&(d)){
			if (d.insurance_class_nr[2].checked == true){
				document.getElementById('iconIns').style.display = 'none';
			}else if((d.insurance_class_nr[0].checked == true)||(d.insurance_class_nr[1].checked == true)){
				document.getElementById('iconIns').style.display = '';
			}else{
				d.insurance_class_nr[2].checked = true;
				document.getElementById('iconIns').style.display = 'none';
			}
	}
	DisableInsurance();
}


//added by VAN------

var trayItems = 0;

//-----------EDITED BY VAN
function DisableInsurance(){
	var d = document.aufnahmeform;
	var rowSrc, i;
	var list = document.getElementById('order-list');
	var dBody=list.getElementsByTagName("tbody")[0];

	var cnt = $('cnt').value;

	if((d) && (d.insurance_class_nr[2].checked==true)) {
		document.getElementById('iconIns').style.display = 'none';
		rowSrc = '<tr><td colspan="10" style="">No such insurance firm exists...</td></tr>'
		dBody.innerHTML = rowSrc;

		//added by VAN 08-15-08
		if (cnt){
			for (i=1;i<=cnt;i++){
				$('add_insurance'+i).disabled = true;
				$('add_insurance'+i).style.cursor = "";
			}
		}
	}else{

		//added by VAN 08-15-08
		if (cnt){
			for (i=1;i<=cnt;i++){
				$('add_insurance'+i).disabled = false;
				$('add_insurance'+i).style.cursor = "pointer";
			}
		}

		document.getElementById('iconIns').style.display = '';
		rowSrc = " ";
		//-----added by VAN---------------------
		<?php
			$result = $encounter_obj->getPersonInsuranceItems($encounter_nr);
			$rows=array();
			while ($row=$result->FetchRow()) {
				$rows[] = $row;
			}
			foreach ($rows as $i=>$row) {
				if ($row) {
					$count++;
					$alt = ($count%2)+1;

					$sql2 = "SELECT ci.* FROM care_person_insurance AS ci
                    WHERE ci.pid = '".$pid."'
                    AND ci.hcare_id = ".$row['hcare_id'];
					$res=$db->Execute($sql2);

					$row2=$res->RecordCount();

					if ($row2!=0) {
							while($rsObj=$res->FetchRow()) {
								$ins_nr = $rsObj["insurance_nr"];
								$is_principal = $rsObj["is_principal"];
								if ($is_principal){
									$principal = "YES";
								}else{
									$principal = "NO";
								}

								$info_src = 0;
								$info = $pinsure_obj->is_member_info_editable($pid, $row['hcare_id'], $ins_nr);
								if ($info) {
									$info['pid'] = "";
									$info_src = 2;
                }
                else {
									// Get the name of the principal member, if exists, in registry of patients ...
									$info = $pinsure_obj->getPrincipalHolder($ins_nr, $row['hcare_id']);
									if ($info) {
                    $info_src = 1;
                  }
                  else {
										$info = array();
										$info['pid'] = "";
										$info['last_name'] = "";
										$info['first_name'] = "";
										$info['middle_name'] = "";

										// Get the values for the following fields from patient's profile ...
										$paddr = $person_obj->getPrincipalAddr($pid);
										if ($paddr) {
											$info['street'] = $paddr['Street'];
											$info['barangay'] = $paddr['brgy_nr'];
											$info['municipality'] = $paddr['mun_nr'];
										}
										else {
											$info['street'] = null;
											$info['barangay'] = null;
											$info['municipality'] = null;
										}

										$info_src = 2;
									}
								}

								//$member_editable_info_button = '<span id="edit_memberinfo_'.$row['hcare_id'].'" style="display:'.($is_principal ? 'none' : '').'"><img id="edit_insurance_'.$row['hcare_id'].'" src="../../images/edit.gif" onclick="edit_member_details_info('.$row['hcare_id'].')" style="cursor:pointer" /></span>';
								$hidden_div = '<div id="insurance_'.$row['hcare_id'].'"><input type="hidden" name="last_name[]" value="'.$info['last_name'].'" id="ln_'.$row['hcare_id'].'" /><input type="hidden" name="first_name[]" value="'.$info['first_name'].'" id="fn_'.$row['hcare_id'].'" /><input type="hidden" name="middle_name[]" value="'.$info['middle_name'].'" id="mn_'.$row['hcare_id'].'" />'.
								              '<input type="hidden" name="street[]" value="'.$info['street'].'" id="st_'.$row['hcare_id'].'" /><input type="hidden" name="barangay[]" value="'.$info['barangay'].'" id="ba_'.$row['hcare_id'].'" /><input type="hidden" name="municipality[]" value="'.$info['municipality'].'" id="mu_'.$row['hcare_id'].'" /><input type="hidden" name="fnr[]" value="'.$row['hcare_id'].'" id="fnr_'.$row['hcare_id'].'" />'.
								              '<input type="hidden" name="inr[]" value="'.$ins_nr.'" id="inr_'.$row['hcare_id'].'"><input type="hidden" name="infosrc[]" value="'.$info_src.'" id="infosrc_'.$row['hcare_id'].'"><input type="hidden" name="principal[]" value="'.$info['pid'].'" id="principal_'.$row['hcare_id'].'"><input type="hidden" name="is_updated[]" value="0" id="is_updated_'.$row['hcare_id'].'"></div>';

//								}
//								else {
//										if ($pinsure_obj->hasNoPrincipal($pid, $row['hcare_id'])) {
//												$member_editable_info_button = '<img id="edit_insurance_'.$row['hcare_id'].'" src="../../images/edit.gif" onclick="edit_member_details_info('.$row['hcare_id'].')" style="cursor:pointer" />';
//												$hidden_div = '<div id="insurance_'.$row['hcare_id'].'"><input type="hidden" name="last_name[]" value="" id="ln_'.$row['hcare_id'].'" /><input type="hidden" name="first_name[]" value="" id="fn_'.$row['hcare_id'].'" /><input type="hidden" name="middle_name[]" value="" id="mn_'.$row['hcare_id'].'" /><input type="hidden" name="street[]" value="" id="st_'.$row['hcare_id'].'" /><input type="hidden" name="barangay[]" value="" id="ba_'.$row['hcare_id'].'" /><input type="hidden" name="municipality[]" value="" id="mu_'.$row['hcare_id'].'" /><input type="hidden" name="fnr[]" value="'.$row['hcare_id'].'" id="fnr_'.$row['hcare_id'].'" /><input type="hidden" name="inr[]" value="'.$ins_nr.'" id="inr_'.$row['hcare_id'].'"></div>';
//										}
//										else {
//											$member_editable_info_button = '';
//											$hidden_div = '';
//										}
//								}
						  }
				  }
			?>
					rowSrc +='<tr class="wardlistrow<?= $alt; ?>" id="row<?= $row['hcare_id'];?>">' +
									'<input type="hidden" name="items[]" id="rowID<?=$row['hcare_id'];?>" value="<?=$row['hcare_id'];?>" />'+
									'<input type="hidden" name="nr[]" id="rowNr<?=$row['hcare_id'];?>" value="<?=addslashes($ins_nr);?>" />'+
									'<input type="hidden" name="is_principal[]" id="rowis_principal<?=$row['hcare_id'];?>" value="<?=$is_principal;?>" />'+
									'<input type="hidden" name="orig_isprincipal[]" id="orig_isprincipal<?=$row['hcare_id'];?>" value="<?=$is_principal;?>" />'+
									'<td align="left" id="firm_<?=$row['hcare_id']?>"><a href="javascript:removeItem(\'<?= $row['hcare_id'];?>\')"><img src="../../images/btn_delitem.gif" border="0"/></a>&nbsp;<?=$member_editable_info_button?></td>'+
									'<td width="*" id="name<?= $row['hcare_id'];?>"><?= $row['firm_id'];?></td>'+
									'<td width="25%" align="right" id="inspin<?= $row['hcare_id'];?>"><?= addslashes($ins_nr) ?></td>'+
									'<td width="18%" class="centerAlign" id="insprincipal<?= $row['hcare_id'];?>"><?= $principal; ?></td>'+
									'<td id="row_column<?=$row['hcare_id']?>"><?=$hidden_div?></td>'+
							'</tr>';

		<?php }
			}
		?>
		if (rowSrc==" "){
			rowSrc = '<tr><td colspan="10" style="">No such insurance firm exists...</td></tr>'
		}
		dBody.innerHTML = rowSrc;

		//----------------------------------------
	}

	//d.insurance_firm_id.value = " ";
}


<?php require($root_path.'include/inc_checkdate_lang.php'); ?>

-->
</script>
<script language="javascript" src="<?php echo $root_path; ?>js/setdatetime.js"></script>
<script language="javascript" src="<?php echo $root_path; ?>js/checkdate.js"></script>
<script language="javascript" src="<?php echo $root_path; ?>js/dtpick_care2x.js"></script>
<script language="javascript" src="<?php echo $root_path; ?>js/jsprototype/prototype.js"></script>

<body onLoad="preset();">
<!-- <div style="margin:5px;font-weight:bold;color:#660000"><?= $sWarning ?></div> -->
<?php
} // End of if(isset(pid))
?>
<div style="margin:5px;font-weight:bold;color:#660000"><?= $sWarning ?></div>
<?php
#added by VAN 06-12-08
echo '<link rel="stylesheet" type="text/css" media="all" href="' .$root_path.'js/jscalendar/calendar-win2k-cold-1.css">';
echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar.js"></script>';
echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/lang/calendar-en.js"></script>';
echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar-setup_3.js"></script>';
/*echo '<script type="text/javascript" src="'.$root_path.'js/jsprototype/prototype.js"></script>';*/
echo '<script type="text/javascript" src="'.$root_path.'js/shortcuts.js"></script>';
#-------------------

#require('./include/js_popsearchwindow.inc.php');

$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

# Load tabs
$target='entry';

$parent_admit = TRUE;

#include('./gui_bridge/default/gui_tabs_patadmit.php');
# If the origin is admission link, show the search prompt
if(!isset($pid) || !$pid){

	# Set color values for the search mask
	$searchmask_bgcolor="#f3f3f3";
	$searchprompt=$LDEntryPrompt;
	$entry_block_bgcolor='#fff3f3';
	$entry_body_bgcolor='#ffffff';

	$smarty->assign('entry_border_bgcolor','#6666ee');

}else{

	$smarty->assign('bSetAsForm',TRUE);


	# Set a row span counter, initialize with 6
	$iRowSpan = 6;

		#--------added condition 03-14-07 by vanessa ---------
		if ($dept_belong['id']!="OPD-Triage"){
			if($dept_belong['id']=="Admission"){
				if ($errorinsclass) $smarty->assign('LDBillType',"<font color=red>$LDBillType</font>");
					else  $smarty->assign('LDBillType',$LDBillType);
//	echo "class = ".$insurance_class_nr;
				$sTemp = '';
				if(is_object($insurance_classes)){
					while($result=$insurance_classes->FetchRow()) {
						$sTemp = $sTemp.'<input name="insurance_class_nr" id="insurance_class_nr" type="radio" onChange="DisableInsurance();"  value="'.$result['class_nr'].'" ';
						if($insurance_class_nr==$result['class_nr']) $sTemp = $sTemp.'checked';
						$sTemp = $sTemp.'>';

						$LD=$result['LD_var'];
						#if(isset($$LD)&&!empty($$LD)) $sTemp = $sTemp.$$LD;
						if(isset($$LD)&&!empty($$LD)) $sTemp.=$$LD;
							#else $sTemp = $sTemp.$result['name'];
							else $sTemp .= $result['name'];
					}
				}
				$smarty->assign('sBillTypeInput',$sTemp);

				#-----added by VAN 08-30-07-----

				$smarty->assign('sBtnAddItem','<a href="javascript:void(0);" id="addinsurance"
										 onclick="return overlib(
													 OLiframeContent(\''.$url.'pid='.$pid.'&encounter_nr='.$encounter_nr.'&frombilling=1\', 600, 500, \'fOrderTray\', 1, \'auto\'),
													 WIDTH,600, TEXTPADDING,0, BORDER,0,
												 STICKY, SCROLL, CLOSECLICK, MODAL,
												 CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
																		 CAPTIONPADDING,4,
																 CAPTION,\'Add insurance from Admission Insurance tray\',
														 MIDX,0, MIDY,0,
													 STATUS,\'Add insurance from Admission Insurance tray\');"
									 onmouseout="nd();">
							<img name="btninsurance" id="btninsurance" src="'.$root_path.'images/btn_additems.gif" border="0"></a>');

				if ($error_ins_nr) $smarty->assign('LDInsuranceNr',"<font color=red>$LDInsuranceList</font>");
					else  $smarty->assign('LDInsuranceNr',$LDInsuranceList);

				#$smarty->assign('sOrderItems',"
				#<tr>
				#	<td colspan=\"10\">Insurance list is currently empty...</td>
				#</tr>");
				$smarty->assign('sOrderItems',"
				<tr>
					<td colspan=\"10\">Insurance list is currently empty...</td>
				</tr>");

				# Note: make a class function for this part later
				$result = $encounter_obj->getPersonInsuranceItems($encounter_nr);
				#echo "sql = ".$encounter_obj->sql;
				$rows=array();
				while ($row=$result->FetchRow()) {
					$rows[] = $row;
				}
				#echo "rows = ";
				#print_r($rows);
				#echo "pid = ".$pid;

				foreach ($rows as $i=>$row) {
					if ($row) {
						$count++;
						$alt = ($count%2)+1;

						$bulk_array_prev[] = array($row['hcare_id'],$row['insurance_nr'],$row["is_principal"]);
						$insurance_array_prev .= $row['hcare_id'].",";

						$sql2 = "SELECT ci.* FROM care_person_insurance AS ci
									WHERE ci.pid =".$pid."
									AND ci.hcare_id = '".$row['hcare_id']."'";
						#echo "sql = ".$sql2;
						$res=$db->Execute($sql2);

						$row2=$res->RecordCount();

						if ($row2!=0){
							while($rsObj=$res->FetchRow()) {
									$ins_nr = $rsObj["insurance_nr"];
									$is_principal = $rsObj["is_principal"];
									if ($is_principal){
										$principal = "YES";
									}else{
										$principal = "NO";
									}
							}
						}

//                        $bNoPrincipal = $pinsure_obj->hasNoPrincipal($pid, $row['hcare_id']);
//                        if ($bNoPrincipal) {
//                            $edit_html = '<img id="edit_insurance_'.$row['hcare_id'].'" style="cursor:pointer" src="../../images/edit.gif" onclick="edit_member_details_info('.$row['hcare_id'].');" border="0"/>';
//                        }
//                        else
														$edit_html = '';

						$src .= '
									<tr class="wardlistrow'.$alt.'" id="row'.$row['hcare_id'].'">
										<input type="hidden" name="items[]" id="rowID'.$row['hcare_id'].'" value="'.$row['hcare_id'].'" />
										<input type="hidden" name="nr[]" id="rowNr'.$row['hcare_id'].'" value="'.addslashes($ins_nr).'" />
										<input type="hidden" name="is_principal[]" id="rowis_principal'.$row['hcare_id'].'" value="'.$is_principal.'" />
																				<td class="centerAlign"><a href="javascript:removeItem(\''.$row['hcare_id'].'\')"><img src="../../images/btn_delitem.gif" border="0"/></a>&nbsp;'.$edit_html.'</td>
										<td id="name'.$row['hcare_id'].'">'.$row['firm_id'].'</td>
										<td width="25%" align="right" id="inspin'.$row['hcare_id'].'">'.addslashes($ins_nr).'</td>
										<td width="18%" class="centerAlign" id="insprincipal'.$row['hcare_id'].'">'.$principal.'</td>
										<td></td>
									</tr>
						';
					}
				}

				$insurance_array_prev = substr($insurance_array_prev,0,strlen($insurance_array_prev)-1);
				#echo "<br>insurance class = ".$insurance_class_nr;
				#if ($src) $smarty->assign('sOrderItems',$src);
				if (($src) && ($insurance_class_nr!=3))
					$smarty->assign('sOrderItems',$src);


				#added by VAN 08-15-08
				$smarty->assign('sOrderItemsreg',"
				<tr>
					<td colspan=\"10\">Insurance list is currently empty...</td>
				</tr>");

				# Note: make a class function for this part later
				$result2 = $pinsure_obj->getPersonInsuranceItems($pid);
				#echo $pinsure_obj->sql;
				if ($result2) {
					$i=1;
					while ($row2=$result2->FetchRow()) {
							$count++;
							$alt = ($count%2)+1;

							if ($row2['is_principal']){
								$principal = "YES";
							}else{
								$principal = "NO";
							}
										#<a href="javascript:prepareAdd(\''.$row2['hcare_id'].'\')"><img src="../../images/play_one.gif" border="0"/></a>

							# Replaced the following line below ... by LST 06.13.2012
							# <td class="centerAlign"><input type="button" name="add_insurance'.$i.'" id="add_insurance'.$i.'" value=">" style="color:#000066; font-weight:bold; padding:0px 2px; cursor:pointer" onclick="xajax_setFlagForPrincipalNmFromTmp(\''.$pid.'\', '.$row2['hcare_id'].', bDone, '.$frombilling.'); if (bDone) prepareAdd(\''.$row2['hcare_id'].'\');"/></td>
							$src2 .= '
										<tr class="wardlistrow'.$alt.'" id="row2'.$row2['hcare_id'].'">
											<input type="hidden" name="items2[]" id="rowID2'.$row2['hcare_id'].'" value="'.$row2['hcare_id'].'" />
											<input type="hidden" name="nr2[]" id="rowNr2'.$row2['hcare_id'].'" value="'.$row2['insurance_nr'].'" />
											<input type="hidden" name="is_principal2[]" id="rowis_principal2'.$row2['hcare_id'].'" value="'.$row2['is_principal'].'" />
										  <td class="centerAlign"><input type="button" name="add_insurance'.$i.'" id="add_insurance'.$i.'" value=">" style="color:#000066; font-weight:bold; padding:0px 2px; cursor:pointer" onclick="xajax_setFlagForPrincipalNmFromTmp(\''.$pid.'\', '.$row2['hcare_id'].'); prepareAdd(\''.$row2['hcare_id'].'\');"/></td>
											<td id="name2'.$row2['hcare_id'].'">'.$row2['firm_id'].'</td>
											<td width="25%" align="right" id="inspin2'.$row2['hcare_id'].'">'.$row2['insurance_nr'].'</td>
											<td width="18%" class="centerAlign" id="insprincipal2'.$row2['hcare_id'].'"><a title="Toggle between principal or beneficiary!" href="#"><span style="cursor:pointer" id="insprincipal_'.$row2['hcare_id'].'" onclick="toggleEditMemberInfoIcon('.$row2['hcare_id'].');">'.$principal.'</span></a></td>
											<td></td>
										</tr>
							';

							$cnt = $i;
							$i++;
					}
				}

				$insurance_array_prev = substr($insurance_array_prev,0,strlen($insurance_array_prev)-1);
				#if (($src) && ($insurance_class_nr!=3))
				if ($src2)
					$smarty->assign('sOrderItemsreg',$src2);
				#--------------------

			} # end if Admission

		} # end if not OPD-Triage


$sTemp = '<input type="hidden" id="pid" name="pid" value="'.$pid.'">
				  <input type="hidden" name="encounter_nr" id="encounter_nr" value="'.$encounter_nr.'">
				  <input type="hidden" name="sid" value="'.$sid.'">
				  <input type="hidden" name="lang" value="'.$lang.'">
				  <input type="hidden" name="mode" value="save">
				  <input type="hidden" name="cnt" id="cnt" value="'.$cnt.'">
				  <input type="hidden" name="insurance_array_prev" id="insurance_array_prev" value="'.$insurance_array_prev.'" size="100">
				  <input type="hidden" name="insurance_show" value="'.$insurance_show.'">
					<input type="hidden" id="noPrincipal" name="noPrincipal" value="0">
					<input type="hidden" name="brgynr" id="brgynr" value="'.$brgynr.'">
					<input type="hidden" name="munnr" id="munnr" value="'.$munnr.'">
			';

			if($update) $sTemp = $sTemp."\n<input type='hidden' name=update value=1>";

			$smarty->assign('sHiddenInputs',$sTemp);

			$smarty->assign('pbSave','<input  type="image" '.createLDImgSrc($root_path,'savedisc.gif','0').' title="'.$LDSaveData.'" align="absmiddle">');

			$smarty->assign('pbRegData','<a href="patient_register_show.php'.URL_APPEND.'&pid='.$pid.'"><img '.createLDImgSrc($root_path,'reg_data.gif','0').'  title="'.$LDRegistration.'"  align="absmiddle"></a>');

#			$smarty->assign('pbCancel','<a href="aufnahme_daten_zeigen.php'.URL_REDIRECT_APPEND.'&encounter_nr='.$encounter_nr.'&origin=admit&sem=isadmitted&target=entry"><img '.createLDImgSrc($root_path,'cancel.gif','0').'  title="'.$LDCancel.'"  align="absmiddle"></a>');   # burn commented: March 12, 2007

				# burn added: March 12, 2007
			if($origin=='patreg_reg') {
				$smarty->assign('pbCancel','<a href="'.$breakfile.'"><img '.createLDImgSrc($root_path,'cancel.gif','0').'  title="'.$LDCancel.'"  align="absmiddle"></a>');
			}else{
				if ($frombilling&&$bill_type)
					$smarty->assign('pbCancel','');
				elseif ($frombilling)
					$smarty->assign('pbCancel','<img '.createLDImgSrc($root_path,'cancel.gif','0').'  title="'.$LDCancel.'"  align="absmiddle" onClick="window.parent.cClick();" style="cursor:pointer">');
				else
					$smarty->assign('pbCancel','<a href="aufnahme_daten_zeigen.php'.URL_REDIRECT_APPEND.'&encounter_nr='.$encounter_nr.'&origin=admit&sem=isadmitted&target=entry"><img '.createLDImgSrc($root_path,'cancel.gif','0').'  title="'.$LDCancel.'"  align="absmiddle"></a>');
			}
			//<!-- Note: uncomment the ff: line if you want to have a reset button  -->
			/*<!--
			$smarty->assign('pbRefresh','<a href="javascript:document.aufnahmeform.reset()"><img '.createLDImgSrc($root_path,'reset.gif','0').' alt="'.$LDResetData.'"  align="absmiddle"></a>');
			-->
			*/

			if($error==1)
				$smarty->assign('sErrorHidInputs','<input type="hidden" name="forcesave" value="1">
				<input  type="submit" value="'.$LDForceSave.'">');

}  // end of if !isset($pid...

# Prepare shortcut links to other functions

#$smarty->assign('sMainBlockIncludeFile','registration_admission/insurance_input.tpl');
$smarty->assign('sMainBlockIncludeFile','registration_admission/insurance_form.tpl');

$smarty->display('common/mainframe2.tpl');
?>
</body>

<script>
/** added by omick, june 13, 2009 **/
function edit_member_details_info(id) {
	var pid = <?=$pid?>;
	var infosrc = $('infosrc_'+id).value;
	var url = '<?=$root_path?>modules/registration_admission/member_insurance_details_edit.php?fnr='+id+'&pid='+pid+'&src='+infosrc;
				overlib(
						OLiframeContent(url, 570, 220, 'fOrderTray', 0, 'no'),
						WIDTH,570, TEXTPADDING,0, BORDER,0,
						STICKY, SCROLL, CLOSECLICK, MODAL,
						CLOSETEXT, '<img src=<?=$root_path?>images/close_red.gif border=0 >',
						CAPTIONPADDING,2,
						CAPTION,'Member Details Insurance Info',
						MIDX,0, MIDY,0,
						STATUS,'Member Details Insurance Info');
				return false;
}
</script>