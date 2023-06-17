<?php
	error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
	require('./roots.php');
	
	require($root_path.'include/inc_environment_global.php');	
		
	define('LANG_FILE','billing.php');
	define('NO_2LEVEL_CHK',1);
//	$local_user='aufnahme_user';
	require_once($root_path.'include/inc_front_chain_lang.php');
			
	// Erase all cookies used for 2nd level script locking, all following scripst will be locked
	// reset all 2nd level lock cookies
	require($root_path.'include/inc_2level_reset.php');
	
	if(!session_is_registered('sess_path_referer')) session_register('sess_path_referer');
	if(!session_is_registered('sess_user_origin')) session_register('sess_user_origin');
	
	$breakfile=$root_path.'main/startframe.php'.URL_APPEND;
	
	$HTTP_SESSION_VARS['sess_path_referer']=$top_dir.basename(__FILE__);
	$HTTP_SESSION_VARS['sess_user_origin']='insurance_co';	

	$breakfile=$root_path."main/spediens.php".URL_APPEND;
	
	
	
$userck='aufnahme_user';

//reset cookie;
// reset all 2nd level lock cookies
setcookie($userck.$sid,'');
require($root_path.'include/inc_2level_reset.php'); 
setcookie('ck_2level_sid'.$sid,'',0,'/');

require($root_path.'include/inc_passcheck_internchk.php');
if ($pass=='check') include($root_path.'include/inc_passcheck.php');

$errbuf=$LDNursingManage;

require($root_path.'include/inc_passcheck_head.php');	
	
	
	
	
	require_once($root_path.'include/care_api_classes/class_tabview.php');			
	
	$objTab = & new GuiTabView;
	$objTab->setTabViewWinTitle("Health Care Information");
	$objTab->setTabViewTitle("Health Care and Benefits Setup");
	$objTab->setTabViewSubtitle("Edit health care information and related benefits.");
	$objTab->setTabViewName("hcareentry");
	$objTab->setTabViewRoot($root_path);	
		
	require_once($root_path.'gui/smarty_template/smarty_care.class.php');
	$smarty = new smarty_care('billing');
			
	$smarty->assign('sTabViewHeader', $objTab->getTabViewHeader());		
	
	$sjssrc = "<script language=\"javascript\" src=\"".$root_path."js/browsers.js\"></script>";
	$sjssrc .= "<script language=\"javascript\" src=\"".$root_path."js/masking/masking.js\"></script>";
	$sjssrc .= "<script language=\"javascript\">					
					function moveFocus(objEvent, srcObj, nextObj) {
						if (is_ie) {
							iKeyCode = objEvent.keyCode;
					  	} 
					  	else {
							iKeyCode = objEvent.which; 
					  	}					
						
						strKey = String.fromCharCode(iKeyCode);
						
						if (!reKeyboardChars.test(strKey)) {
							if (document.getElementById(srcObj).maxLength == document.getElementById(srcObj).value.length + 1)
								document.getElementById(nextObj).focus();											
						}
					}
				</script>";	
	$sjssrc .= "<script type=\"text/javascript\" src=\"".$root_path."js/masking/html-form-input-mask.js\"></script>";
	$sjssrc .= "<script type=\"text/javascript\" src=\"".$root_path."js/jsprototype/prototype1.5.js\"></script>";
	$sjssrc .= "<script type=\"text/javascript\">
			    <!--
				function addLoadEvent(func) {
					var oldonload = window.onload;
  					if (typeof window.onload != 'function') {
    					window.onload = func;
  					} else {
    					window.onload = function() {
      						if (oldonload) {
        						oldonload();
      						}
      						func();
    					}
  					}
				}

				addLoadEvent(function() {
  					Xaprb.InputMask.setupElementMasks();
				});
				//-->
				</script>";
	$sjssrc .= $objTab->getJSSource();
	$smarty->assign('sTabViewJSSource', $sjssrc);
	
	$sTabContents = array(array("plan", "Health Plan", "<fieldset>
                        									<legend>Plan Information</legend>
															<br>
															<table border=0>
															<tr><td>Description:</td><td><input id=\"hcare_desc\" type=\"text\" name=\"hcare_desc\" onkeypress=\"doTab(event, 'hcare_company');\" size=80></td></tr>
															<tr><td>Company:</td><td><input id=\"hcare_company\" type=\"text\" name=\"hcare_company\" onkeypress=\"doTab(event, 'hcare_contact_person');\" size=80></td></tr>
															<tr><td>Contact Person:</td><td><input id=\"hcare_contact_person\" type=\"text\" name=\"hcare_contact_person\" onkeypress=\"doTab(event, 'hcare_addr1');\" size=80></td></tr>
															<tr><td>Address:</td><td><input id=\"hcare_addr1\" type=\"text\" name=\"hcare_addr1\" onkeypress=\"doTab(event, 'hcare_addr2');\" size=40></td></tr>
															<tr><td>&nbsp;</td><td><input id=\"hcare_addr2\" type=\"text\" name=\"hcare_addr2\" onkeypress=\"doTab(event, 'hcare_contactno');\" size=40></td></tr>
															<tr><td>Contact No.:</td><td><input id=\"hcare_contactno\" type=\"text\" name=\"hcare_contactno\" class=\"text input_mask mask_phone\" onkeypress=\"doTab(event, 'cmd_save');\" size=20></td></tr>																																													
															</table><br><br>																													
                    									</fieldset>"),
						  array("benefits", "Benefits", "<fieldset>
                        									<legend>Benefits</legend>
                        									<label for=\"foo\"> <input id=\"foo\" name=\"foo\"></label>
                        									<input type=\"submit\" value=\"submit\">
                    									 </fieldset>"));														
	
	$smarty->assign('sTabMainBody', $objTab->getConstructedTab($sTabContents));
	
	$sTmp = "<table border=0><tr><td>".
			"<input id=\"cmd_save\" type=\"image\" src=\"".$root_path."gui/img/control/default/en/en_savedisc.gif\" border=0 width=\"72\" height=\"23\"  alt=\"SAVE\" align=\"absmiddle\"></td><td>&nbsp;</td><td><input type=\"image\" src=\"".$root_path."gui/img/control/default/en/en_cancel.gif\" border=0 width=\"87\" height=\"23\"></td></tr></table>";
	
	$smarty->assign('sTabViewFooter', $sTmp);
	$smarty->display('billing/tabformhdr.tpl');

//	require($root_path.'include/inc_environment_global.php');
//	
//	require($root_path.'include/inc_front_chain_lang.php');
//	
//	session_register("hcare_updatetyp");			// update type
//	session_register("hcare_id");					// health care id.
//	
//	if ($_SESSION["hcare_updatetyp"] == 1) {			// ... in edit mode.
//		// ... get the information of selected health care insurance.
//		
//		
//		
//		
//	}			
?>
