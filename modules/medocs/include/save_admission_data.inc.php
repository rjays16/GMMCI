<?php
/*------begin------ This protection code was suggested by Luki R. luki@karet.org ---- */
if (eregi('save_admission_data.inc.php',$PHP_SELF)) 
	die('<meta http-equiv="refresh" content="0; url=../">');	

	
$obj->setDataArray($HTTP_POST_VARS);
	
switch($mode)
{	
	case 'create': 
					if($obj->insertDataFromInternalArray() ) {
						if(isset($redirect)&&$redirect){
							#header("location:".$thisfile.URL_REDIRECT_APPEND."&target=$target&mode=details&encounter_nr=".$HTTP_SESSION_VARS['sess_en']."&nr=".$HTTP_POST_VARS['ref_notes_nr']);
							//<a href=show_medocs.php'.URL_APPEND.'&from=such&pid='.$zeile['pid'].'&encounter_nr='.$zeile['encounter_nr'].'&target=entry&tabs='.$tabs.'>
							//show_medocs.php?sid=a94c5f9b17e1fe69a214159fb6e71978&lang=en&pid=10000000&encounter_nr=2007500007&target=entry&mode=show&type_nr=1&encounter_class_nr = 2">
							//<a href="'.$thisfile.URL_APPEND.'&pid='.$HTTP_SESSION_VARS['sess_pid'].'&encounter_nr='.$HTTP_SESSION_VARS['sess_en'].'&target='.$target.'&mode=show&type_nr='.$type_nr.'&encounter_class_nr = '.$encounter_class_nr.'">
							
							header("location:".$thisfile.URL_APPEND."&pid=".$HTTP_SESSION_VARS['sess_pid']."&encounter_nr=".$HTTP_SESSION_VARS['sess_en']."&target=entry&mode=show&type_nr=".$type_nr."&encounter_class_nr=".$encounter_class_nr);
							#header("location:".$thisfile.URL_REDIRECT_APPEND."&from=such&pid=".$_POST['sess_pid']."&encounter_nr=".$encounter_nr."&target=entry");
								
							exit;
						}
					
					} else echo "$obj->sql<br>$LDDbNoSave";
					break;
								
	case 'update': 
					$obj->where=' nr='.$nr;
					if($obj->updateDataFromInternalArray($nr)) {
						if($redirect){
							header("location:".$thisfile.URL_REDIRECT_APPEND."&target=$target&encounter_nr=".$HTTP_SESSION_VARS['sess_en']);
							echo "$obj->sql<br>$LDDbNoUpdate";
							exit;
						}
					} else echo "$obj->sql<br>$LDDbNoUpdate";
					break;
								
}// end of switch

?>
