<?php
/*------begin------ This protection code was suggested by Luki R. luki@karet.org ---- */
if (eregi('inc_passcheck.php',$PHP_SELF))
	die('<meta http-equiv="refresh" content="0; url=../">');
/*------end------*/

/**
* CARE 2002 Integrated Hospital Information System
* GNU General Public License
* Copyright 2002 Elpidio Latorilla
* elpidio@care2x.org,
*
* See the file "copy_notice.txt" for the licence notice
*/


//function validarea(&$zeile2, $permit_type_all = 1){
//    global $allowedarea;
//		global $level2_permission;
//
//#	echo "inc_passcheck.php : validarea : allowedarea : "; print_r($allowedarea); echo " <br> \n";
//#	echo "inc_passcheck.php : validarea : zeile2 = '".$zeile2."' <br> \n";   //permission of user from care_user table
//
//	if(ereg('System_Admin', $zeile2)){  // if System_admin return true
//	   return 1;
//	}elseif(in_array('no_allow_type_all', $allowedarea)){ // check if the type "all" is blocked, if so return false
//	     return 0;
//	}elseif($permit_type_all && ereg('_a_0_all', $zeile2)){ // if type "all" , return true
//		return 1;
//	}else{                                                                  // else scan the permission
//		# Modified by AJMQ (04/02/08)
//
//		if (is_array($level2_permission) && $level2_permission) {
//			#for($j=0;$j<sizeof($level_2_permission);$j++){
//			$lvl2access_ok=0;
//			foreach($level2_permission as $j=>$v) {
//				if(ereg($v,$zeile2)) {
//					$lvl2access_ok=1;
//					break;
//				}
//			}
//		}
//		else
//			$lvl2access_ok=1;
//#		print_r($allowedarea);
//		for($j=0;$j<sizeof($allowedarea);$j++){
//#			var_dump(ereg($allowedarea[$j],$zeile2));
//			if(ereg($allowedarea[$j],$zeile2)) {
//#				print_r($zeile2);
//#				echo "lvl2access_ok=$lvl2access_ok";
//				return $lvl2access_ok;
//			}
//		}
//	}
//	return 0;           // otherwise the user has no access permission in the area, return false
//}

function logentry(&$userid,$key,$report,&$remark1,&$remark2)
{
	 global $passtag, $root_path;

	if($passtag) $logpath=$root_path.'logs/access_fail/'.date('Y').'/';
		 else $logpath=$root_path.'logs/access/'.date('Y').'/';
#echo "inc_passcheck.php : logentry : passtag = '".$passtag."' <br> \n";   //permission of user from care_user table
#echo "inc_passcheck.php : 1 logentry : logpath = '".$logpath."' <br> \n";   //permission of user from care_user table
	if (file_exists($logpath))
	{
		$logpath=$logpath.date('Y-m-d').'.log';
#echo "inc_passcheck.php : logentry : 2 logpath = '".$logpath."' <br> \n";   //permission of user from care_user table
		$file=fopen($logpath,'a');
		if ($file)
		{
				if ($userid=='') $userid='blank';
			$line=date('Y-m-d').'/'.date('H:i').' '.$report.'  Username='.$key.'  Userid='.$userid.'  Fileaccess='.$remark1.'  Fileforward='.$remark2;
			fputs($file,$line);fputs($file,"\r\n");
			fclose($file);
		}
	}
}

/*if(!isset($db) || !$db || !$dblink_ok) include_once($root_path.'include/inc_db_makelink.php');

if($dblink_ok)
{*/
		# modified burn, October 4, 2007
		$sql='SELECT u.name, u.login_id, u.password, u.permission, u.personell_nr, u.lockflag,
					p.pid, fn_get_personell_name(u.personell_nr) AS fullname
				FROM care_users AS u
					LEFT JOIN care_personell AS pl ON pl.nr=u.personell_nr
						LEFT JOIN care_person AS p ON p.pid=pl.pid
				WHERE login_id=\''.addslashes($userid).'\'
		';
		#--------comment 02-26-07-------------
#	 $sql='SELECT name, login_id, password, permission, personell_nr, lockflag FROM care_users WHERE login_id=\''.addslashes($userid).'\'';

	/*
	$sql = 'SELECT U.name, U.login_id, U.password, U.personell_nr, U.permission, U.lockflag, P.nr, P.pid, P.job_function_title,
									PA.personell_nr, PA.role_nr, PA.location_type_nr, PA.location_nr,
									D.nr, D.type, D.name_formal
					 FROM care_users as U, care_personell as P, care_personell_assignment as PA,
						 care_department as D
					 WHERE U.personell_nr = P.nr AND P.nr=PA.personell_nr
				AND PA.location_nr=D.nr AND login_id=\''.addslashes($userid).'\'';
	*/
	if($ergebnis=$db->Execute($sql))
	{
			$zeile=$ergebnis->FetchRow();
		if(isset($checkintern)&&$checkintern)
		{
			$dec_login = new Crypt_HCEMD5($key_login,'');
			//$keyword = $dec_login->DecodeMimeSelfRand($HTTP_COOKIE_VARS['ck_login_pw'.$sid]);
			$keyword = $dec_login->DecodeMimeSelfRand($HTTP_SESSION_VARS['sess_login_pw']);
			}else{
			$checkintern=false;
		}
/*
echo "inc_passcheck.php : zeile['password'] = '".$zeile['password']."' <br> \n";
echo "inc_passcheck.php : keyword = '".$keyword."' <br> \n";
echo "inc_passcheck.php : md5(keyword) = '".md5($keyword)."' <br> \n";
echo "inc_passcheck.php : zeile['login_id'] = '".$zeile['login_id']."' <br> \n";
echo "inc_passcheck.php : userid = '".$userid."' <br> \n";
echo "inc_passcheck.php : (zeile['password']==md5(keyword)) = '".($zeile['password']==md5($keyword))."' <br> \n";
echo "inc_passcheck.php : (zeile['login_id']==userid) = '".($zeile['login_id']==$userid)."' <br> \n";
*/
		if (($zeile['password']==md5($keyword))&&($zeile['login_id']==$userid))
		{
			if (!($zeile['lockflag']))
			{
				if ((isset($screenall)&&$screenall) || validarea($zeile['permission']))
				{
						if(empty($zeile['name'])) $zeile['name']=' ';

						logentry($userid,$zeile['name'],"IP:".$REMOTE_ADDR." $lognote ",$thisfile,$fileforward);

					/**
					* Init crypt to use 2nd level key and encrypt the sid.
					* Store to cookie the "$ck_2level_sid.$sid"
					* There is no need to call another include of the inc_init_crypt.php since it is already included at the start
					* of the script that called this script.
					*/
						$enc_2level = new Crypt_HCEMD5($key_2level, makeRand());
#	echo "inc_passcheck.php : enc_2level = '".$enc_2level."' <br> \n";
#	echo "inc_passcheck.php :enc_2level : "; print_r($enc_2level); echo " <br> \n";
					$ciphersid=$enc_2level->encodeMimeSelfRand($sid);
#	echo "inc_passcheck.php : ciphersid = '".$ciphersid."' <br> \n";
#	echo "inc_passcheck.php : ciphersid : "; print_r($ciphersid); echo " <br> \n";
					//setcookie('ck_2level_sid'.$sid,$ciphersid,time()+3600,'/');
					//setcookie($userck.$sid,$zeile['name'],time()+3600,'/');
					setcookie('ck_2level_sid'.$sid,$ciphersid,0,'/');
					setcookie($userck.$sid,$zeile['name'],0,'/');
					//setcookie('ck_2level_sid'.$sid,$ciphersid);
					//setcookie($userck.$sid,$zeile['name']);
					//echo $fileforward;
					$HTTP_SESSION_VARS['sess_user_name']=$zeile['name'];
					$HTTP_SESSION_VARS['sess_temp_userid']=$zeile['login_id']; 	// SEGWORKS: August 3, 2006 2:56 pm, added by AJMQ
					$HTTP_SESSION_VARS['sess_user_personell_nr']=$zeile['personell_nr']; 	// SEGWORKS: September 14, 2007 4:13 pm, added by burn
					$HTTP_SESSION_VARS['sess_user_pid']=$zeile['pid']; 	// SEGWORKS: October 4, 2007 4:57 pm, added by burn
					$HTTP_SESSION_VARS['sess_user_fullname']=$zeile['fullname']; 	// SEGWORKS: October 4, 2007 4:57 pm, added by burn
					$HTTP_SESSION_VARS['sess_temp_personell_nr']=$zeile['personell_nr']; 	// SEGWORKS: September 14, 2007 4:13 pm, added by burn
					$HTTP_SESSION_VARS['sess_temp_pid']=$zeile['pid']; 	// SEGWORKS: October 4, 2007 4:57 pm, added by burn
					$HTTP_SESSION_VARS['sess_temp_fullname']=$zeile['fullname']; 	// SEGWORKS: October 4, 2007 4:57 pm, added by burn
					$HTTP_SESSION_VARS['sess_permission']=$zeile['permission']; 	// SEGWORKS: October 4, 2007 4:57 pm, added by burn

#echo "inc_passcheck.php : HTTP_SESSION_VARS : "; print_r($HTTP_SESSION_VARS); echo " <br><br> \n";

					#echo "inc_passcheck.php : before header : ".strtr($fileforward,' ','+').'&checkintern='.$checkintern;
#					echo "inc_passcheck.php : before header : ".'Location:'.strtr($fileforward,' ','+').'&checkintern='.$checkintern."' <br>\n";
					header('Location:'.strtr($fileforward,' ','+').'&checkintern='.$checkintern);
					#echo "van = ".$zeile['name_formal'];
					#header('Location:'.strtr($fileforward,' ','+').'&checkintern='.$checkintern.'&dept='.$zeile['name_formal']);
					exit;
				}else {$passtag=2;};
			}else $passtag=3;
		}else {$passtag=1;};
	}
	else {
		die("Ouch" . $db->ErrorMsg());
		$passtag=1;
	};
/*}
else  print "$LDDbNoLink<br>";*/
?>
