<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
/*
CARE2X Integrated Information System Deployment 2.1 - 2004-10-02 for Hospitals and Health Care Organizations and Services
Copyright (C) 2002,2003,2004,2005  Elpidio Latorilla & Intellin.org

GNU GPL. For details read file "copy_notice.txt".
*/
$lang_tables=array('personell.php');
define('LANG_FILE','aufnahme.php');
$local_user='aufnahme_user';
require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'include/care_api_classes/class_personell.php');
require_once($root_path.'include/care_api_classes/class_department.php');
//require_once($root_path.'include/care_api_classes/class_person.php');
//require_once($root_path.'include/care_api_classes/class_insurance.php');
//require_once($root_path.'include/care_api_classes/class_ward.php');
require_once($root_path.'include/care_api_classes/class_globalconfig.php');

$GLOBAL_CONFIG=array();

$thisfile=basename(__FILE__);
if($HTTP_COOKIE_VARS['ck_login_logged'.$sid]) $breakfile=$root_path.'main/spediens.php'.URL_APPEND;
	else $breakfile='personell_admin_pass.php'.URL_APPEND.'&target='.$target;

$personell_obj=new Personell();
$dept_obj=new Department;
//$person_obj=new Person();
//$insurance_obj=new Insurance;
//$ward_obj=new Ward;
/* Get the personell  global configs */
$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
$glob_obj->getConfig('personell_%');
$glob_obj->getConfig('person_foto_path');

$updatefile='personell_register.php';

/* Default path for fotos. Make sure that this directory exists! */
$default_photo_path=$root_path.'fotos/registration';
$photo_filename='nopic';

#Check whether the origin is phone directory and if session personnel nr. is ok
if($HTTP_SESSION_VARS['sess_user_origin']=='phonedir'&&$HTTP_SESSION_VARS['sess_personell_nr']){
	$personell_nr=$HTTP_SESSION_VARS['sess_personell_nr'];
}else{
	$HTTP_SESSION_VARS['sess_personell_nr']=$personell_nr;
}

	//if(!empty($GLOBAL_CONFIG['patient_financial_class_single_result'])) $encounter_obj->setSingleResult(true);
	$personell_obj->loadPersonellData($personell_nr);
	if($personell_obj->is_loaded) {
		$row=&$personell_obj->personell_data;
#echo "personell_register_show.php : row : <br> \n"; print_r($row); echo" <br> \n";
		//load data
		//while(list($x,$v)=each($row)) {$$x=$v;}
		extract($row);
		$deptOfDoc = $dept_obj->getDeptofDoctor($personell_nr);   # burn added: May 28, 2007
#echo "personell_register_show.php : deptOfDoc : <br> \n"; print_r($deptOfDoc); echo" <br> \n";
		//$insurance_class=&$encounter_obj->getInsuranceClassInfo($insurance_class_nr);
		//$encounter_class=&$encounter_obj->getEncounterClassInfo($encounter_class_nr);

		//if($data_obj=&$person_obj->getAllInfoObject($pid))
/*		$list='title,name_first,name_last,name_2,name_3,name_middle,name_maiden,name_others,date_birth,
						 sex,addr_str,addr_str_nr,addr_zip,addr_citytown_nr,photo_filename';

		$person_obj->setPID($pid);
		if($row=&$person_obj->getValueByList($list))
		{
			while(list($x,$v)=each($row))	$$x=$v;
		}

		$addr_citytown_name=$person_obj->CityTownName($addr_citytown_nr);
		$encoder=$encounter_obj->RecordModifierID();
*/	}

	include_once($root_path.'include/inc_date_format_functions.php');

	/* Update History */
	//if(!$newdata) $encounter_obj->setHistorySeen($HTTP_SESSION_VARS['sess_user_name'],$encounter_nr);
	/* Get insurance firm name*/
	//$insurance_firm_name=$insurance_obj->getFirmName($insurance_firm_id);
	/* Get ward name */
	//$current_ward_name=$ward_obj->WardName($current_ward_nr);
	/* Check whether config path exists, else use default path */
	$photo_path = (is_dir($root_path.$GLOBAL_CONFIG['person_foto_path'])) ? $GLOBAL_CONFIG['person_foto_path'] : $default_photo_path;


/* Prepare text and resolve the numbers */
require_once($root_path.'include/inc_patient_encounter_type.php');

if(!session_is_registered('sess_parent_mod')) session_register('sess_parent_mod');
if(!session_is_registered('sess_user_origin')) session_register('sess_user_origin');

/* Save encounter nrs to session */
$HTTP_SESSION_VARS['sess_pid']=$pid;
//$HTTP_SESSION_VARS['sess_en']=$encounter_nr;
//$HTTP_SESSION_VARS['sess_full_en']=$full_en;
$HTTP_SESSION_VARS['sess_parent_mod']='admission';
$HTTP_SESSION_VARS['sess_pnr']=$personell_nr;
//$full_pnr=$personell_nr+$GLOBAL_CONFIG['personell_nr_adder'];
$full_pnr=$personell_nr;
$HTTP_SESSION_VARS['sess_full_pnr']=$full_pnr;
$HTTP_SESSION_VARS['sess_user_origin']='personell_admin';

/* Prepare the photo filename */
require_once($root_path.'include/inc_photo_filename_resolve.php');

#added by VAN 11-04-09
#$sql = "SELECT p.* FROM care_personell_assignment as p where personell_nr='$personell_nr'
#				ORDER BY modify_time DESC LIMIT 1";
$sql = "SELECT p.* FROM care_personell as p where nr='$personell_nr'
				ORDER BY modify_time DESC LIMIT 1";

$rs = $db->Execute($sql);

$row_per = $rs->FetchRow();


$personnel_type = substr($short_id,0,1);

require($root_path.'modules/personell_admin/ajax/accre-insurance.common.php');
$xajax->printJavascript($root_path.'classes/xajax-0.2.5');

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
<link type="text/css" href="<?=$root_path?>js/jquery/css/jquery-ui.css" rel="stylesheet">
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery-1.8.2.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/ui/jquery-ui-1.9.1.js"></script>

<script type="text/javascript">
<!--
OLpageDefaults(BGCLASS,'olbg', CGCLASS,'olcg', FGCLASS,'olfg',
 CAPTIONFONTCLASS,'olcap', CLOSEFONTCLASS,'olclo', TEXTFONTCLASS,'oltxt');
//-->
</script>

<style type="text/css">
<!--
.olbg {
	background-image:url("<?= $root_path ?>images/bar_05.gif");
	background-color:#0000ff;
	border:1px solid #4d4d4d;
}
.olcg {
	background-color:#aa00aa;
	background-image:url("<?= $root_path ?>images/bar_05.gif");
	text-align:center;
}
.olcgif {background-color:#333399; text-align:center;}
.olfg {
	background-color:#ffffcc;
	text-align:center;
}
.olfgif {background-color:#bbddff; text-align:center;}
.olcap {
	font-family:Arial; font-size:13px;
	font-weight:bold;
	color:#708088;
}
a.olclo {font-family:Verdana; font-size:11px; font-weight:bold; color:#ddddff;}
a.olclo:hover {color:#ffffff;}
.oltxt {font-family:Arial; font-size:12px; color:#000000;}

/*a {color:#338855;font-weight:bold;}*/
a {color:#338855;}
a:hover {color:#FF00FF;}
.text12 {font-family:Verdana,Arial,sans-serif; font-size:12px;}
.text14 {font-family:Verdana,Arial,sans-serif; font-size:14px;}
.text16 {font-family:Verdana,Arial,sans-serif; font-size:16px;}
.text18 {font-family:Verdana,Arial,sans-serif; font-size:18px;}

.myHeader {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:22px;}
.mySubHead {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:18px;}
.mySpacer {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:4px;}
.myText {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:13px;color:#000000;}
.snip {font-family:Verdana,Arial,Helvetica;font-size:10px;}
.purple14 {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:14px;color:purple;
 font-weight:bold;}
.purple18 {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:18px;color:purple;
 font-weight:bold;font-style:italic;}
.yellow {color:#ffff00;}
.red {color:#cc0000;}
.blue {color:#0000cc;}
-->
</style>


<script  language="javascript">
	function Dependents(){
		/*
		return overlib(
					OLiframeContent('../../modules/dependents/seg-dependents.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&popUp=1&pid=<?=$pid?>',
									800, 440, 'fGroupTray', 0, 'auto'),
											WIDTH,800, TEXTPADDING,0, BORDER,0,
									STICKY, SCROLL, CLOSECLICK, MODAL,
									CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0 onClick="">',
										 CAPTIONPADDING,2, CAPTION,'Dependents',
										 MIDX,0, MIDY,0,
										 STATUS,'Dependents');
						*/
		return overlib(
					OLiframeContent('../../modules/dependents/seg-dependent-pass.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&target=dependents&popUp=1&pid=<?=$pid?>',
									800, 440, 'fGroupTray', 0, 'auto'),
											WIDTH,800, TEXTPADDING,0, BORDER,0,
									STICKY, SCROLL, CLOSECLICK, MODAL,
									CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0 onClick="">',
										 CAPTIONPADDING,2, CAPTION,'Dependents',
										 MIDX,0, MIDY,0,
										 STATUS,'Dependents');
	}

	function deactivatePersonnel(personell_nr,deactivate){
		var prompt;
		if (deactivate==1)
				prompt = 'deactivate';
		else
				prompt = 'activate';

		res = confirm('Are you really sure to '+prompt+' the personnel\'s employment status?');

		if (res)
			xajax_setDeactivatePersonnel(personell_nr,deactivate);
	}

	function changePassword(personell_nr){
			var password;
			res = confirm('Are you really sure to change the user\'s password?'); 
               if (res){
                    $( "#passwordDialog" ).dialog({
                        autoOpen: true,
                        modal:true,
                        show: "blind",
                        hide: "explode",
                        title: "Change password",
                        position: "top", //added by VAN 12-19-2012 
                        buttons: {
                                OK: function() {
                                    password = $("#password").val()
                                    xajax_setChangePassword(personell_nr,password);
                                },
                                Cancel: function() {
                                    $( this ).dialog( "close" );
                                }
                        },
                        close: function() {
                         $( this ).dialog( "close" );
                    }
                        
                    });
			        
					        
		        }
	}

	function showPermission(personell_nr,with_access,userid,username){
			var location;

			if (with_access==1){
				location ='../../modules/system_admin/edv_user_access_edit.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&popUp=1&mode=edit&userid='+userid;
			}else{
				location ='../../modules/system_admin/edv_user_access_edit.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&popUp=1&is_employee=1&personell_nr='+personell_nr+'&username='+username+'&userid='+userid;
			}

			return overlib(
					OLiframeContent(location,
																	800, 440, 'fGroupTray', 0, 'auto'),
																	WIDTH,800, TEXTPADDING,0, BORDER,0,
																		STICKY, SCROLL, CLOSECLICK, MODAL,
																		CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0 onClick="ReloadWindow();">',
																 CAPTIONPADDING,2, CAPTION,'User Permission',
																 MIDX,0, MIDY,0,
																 STATUS,'User Permission');

	}

	function ReloadWindow(){
			window.location.href=window.location.href;
	}
//-->
</script>

<?php
/* Load the GUI page */
#echo "van = $thisfile";
require('./gui_bridge/default/gui_'.$thisfile);
?>
<!--Added by jarel    -->
<div class="segPanel" id="passwordDialog" style="display:none" align="left">
   <h3><span>Enter New Password</span></h3>
    <div align="center" style="overflow:hidden">
       <input type="password" name="password" id="password" value="">
         <br/>
         <br/>
    </div>
</div>
