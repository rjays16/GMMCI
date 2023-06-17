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
define('LANG_FILE','products.php');
define('NO_2LEVEL_CHK',1);
$local_user='ck_prod_db_user';
require_once($root_path.'include/inc_front_chain_lang.php');

#$breakfile='apotheke.php'.URL_APPEND;
$breakfile=$root_path.'main/startframe.php'.URL_APPEND;

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');
 ?>
 <!--added by VAN 09-20-08 -->
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

<script type="text/javascript" src="<?=$root_path?>js/shortcut.js"></script>

<script type="text/javascript">

	ShortcutKeys();
	function ShortcutKeys(){
		//new person
		shortcut.add('Alt+P', NewRegFxn,
								{
									'type':'keydown',
									'propagate':false,
								}
						);
		
		//new born registration
		shortcut.add('Alt+N', NewbornFxn,
								{
									'type':'keydown',
									'propagate':false,
								}
						);
		
		//search
		shortcut.add('Alt+Z', SearchFxn,
								{
									'type':'keydown',
									'propagate':false,
								}
						);		
		
		//advance search
		shortcut.add('Alt+X', AdvanceSearchFxn,
								{
									'type':'keydown',
									'propagate':false,
								}
						);				
		
		//comprehensive search
		shortcut.add('Alt+C', CompreSearchFxn,
								{
									'type':'keydown',
									'propagate':false,
								}
						);
						
		//consultation
		shortcut.add('Alt+A', ConsultationFxn,
								{
									'type':'keydown',
									'propagate':false,
								}
						);
		
		//icd, icpm
		shortcut.add('Alt+M', MedocsFxn,
								{
									'type':'keydown',
									'propagate':false,
								}
						);	
						
		//reports
		shortcut.add('Alt+R', ReportsFxn,
								{
									'type':'keydown',
									'propagate':false,
								}
						);											
 	}
	
	//new person
	function NewRegFxn(){
		urlholder="<?=$root_path?>modules/ipd/seg-ipd-pass.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=ipd_reg&from=ipd";
		window.location.href=urlholder;
	}
	
	//new born registration
	function NewbornFxn(){
		urlholder="<?=$root_path?>modules/ipd/seg-ipd-pass.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=ipd_newbornreg&from=ipd";
		window.location.href=urlholder;
	}
	
	//search
	function SearchFxn(){
		urlholder="<?=$root_path?>modules/ipd/seg-ipd-pass.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=ipd_searchpatient&from=ipd";
		window.location.href=urlholder;
	}
	
	//advance search
	function AdvanceSearchFxn(){
		urlholder="<?=$root_path?>modules/ipd/seg-ipd-pass.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=ipd_searchadv&from=ipd";
		window.location.href=urlholder;
	}
	
	//comprehensive search
	function CompreSearchFxn(){
		urlholder="<?=$root_path?>modules/ipd/seg-ipd-pass.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=ipd_searchcompre&from=ipd";
		window.location.href=urlholder;
	}
	
	//Added by Genesis D. Ortiz list of patients
	function PatientSearchFxn(){
		urlholder="<?=$root_path?>modules/registration_admission/list_of_patients.php?sid=$sid&lang=$lang&target=seg_billing_list_patients&user_origin=ipd";
		window.location.href=urlholder;
	}
	//end Added by Genesis D. Ortiz list of patients

	//consultation
	function ConsultationFxn(){
		urlholder="<?=$root_path?>modules/ipd/seg-ipd-pass.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=ipd_consultation&from=ipd";
		window.location.href=urlholder;
	}
	
	//icd, icpm
	function MedocsFxn(){
		urlholder="<?=$root_path?>modules/ipd/seg-ipd-pass.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=ipd_icdicpm&from=ipd";
		window.location.href=urlholder;
	}
	
    //Medical Certificate
    function MedCertFxn(){
        //urlholder="<?=$root_path?>modules/registration_admission/cert_med_search.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=er_icdicpm&from=er";
        urlholder="<?=$root_path?>modules/ipd/seg-ipd-pass.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=ipd_medcert&from=ipd";
        window.location.href=urlholder;
    }
    
	//reports
	function ReportsFxn(){
		urlholder="<?=$root_path?>modules/ipd/seg-ipd-pass.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=reports&from=ipd";
		window.location.href=urlholder;
	}
</script>
<?php
 
 # Create a helper smarty object without reinitializing the GUI
 $smarty2 = new smarty_care('common', FALSE);

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"Admitting Department");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDPharmacy $LDPharmaDb')");
 
 $smarty->assign('sOnLoadJs','onLoad="if (window.focus) window.focus();"');

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('title',"Admitting Department");

 # Prepare the submenu icons
#line 55 was added by VAN 09-20-08 , add a link for new request for all cost centers
 $aSubMenuIcon=array(
  createComIcon($root_path,'newpatient.gif','0'),
  createComIcon($root_path,'requests.gif','0'),
  createComIcon($root_path,'search.gif','0'),
  createComIcon($root_path,'search_plus.gif','0'),
  createComIcon($root_path,'patdata.gif','0'),
  //added by Genesis D. Ortiz (05-27-2014)
  createComIcon($root_path,'disc_unrd.gif','0'),
  //end added by Genesis D. Ortiz (05-27-2014)
  createComIcon($root_path,'consultation.gif','0'),
  createComIcon($root_path,'icd10.gif','0'),
  createComIcon($root_path,'disc_unrd.gif','0'),
  createComIcon($root_path,'chart.gif','0')
);

# Prepare the submenu item descriptions
$aSubMenuText=array(
  "Register new patient data",
  "Register new born data",
  "Search patient information", 
  "Full-featured patient searching", 
  "Comprehensive patient information", 
  //added by Genesis D. Ortiz (05-27-2014)
  "List of patients corresponds with admission date.", 
  //end added by Genesis D. Ortiz (05-27-2014)
  "Register patient admission", 
  "Patient ICD/ICPM encoding",
  "View and Generate Medical Certificates", 
	"Generate Admission reports"
);

# Prepare the submenu item links indexed by their template tags
/*
$aSubMenuItem=array(
  'LDRegPatient' => '<a href="'.$root_path.'modules/ipd/seg-ipd-pass.php'. URL_APPEND."&userck=$userck".'&target=ipd_reg&from=ipd">Register patient</a>',
  'LDRegNewBorn' => '<a href="'.$root_path.'modules/ipd/seg-ipd-pass.php'. URL_APPEND."&userck=$userck".'&target=ipd_newbornreg&from=ipd">Register new born</a>',
  'LDSearch' => '<a href="'.$root_path.'modules/ipd/seg-ipd-pass.php'. URL_APPEND."&userck=$userck".'&target=ipd_searchpatient&from=ipd">Search patients</a>',
  'LDAdvSearch' => '<a href="'.$root_path.'modules/ipd/seg-ipd-pass.php'. URL_APPEND."&userck=$userck".'&target=ipd_searchadv&from=ipd">Advanced search</a>',
  'LDComprehensive' => '<a href="'.$root_path.'modules/ipd/seg-ipd-pass.php'. URL_APPEND."&userck=$userck".'&target=ipd_searchcompre&from=ipd">Comprehensive</a>',  
  'LDConsultation' => '<a href="'.$root_path.'modules/ipd/seg-ipd-pass.php'. URL_APPEND."&userck=$userck".'&target=ipd_consultation&from=ipd">Admission</a>',
  'LDIcdIcpm' => '<a href="'.$root_path.'modules/ipd/seg-ipd-pass.php'. URL_APPEND."&userck=$userck".'&target=ipd_icdicpm&from=ipd">ICD/ICPM</a>',
	'LDGenerateOPDReport' => '<a href="'.$root_path.'modules/ipd/seg-ipd-pass.php'. URL_APPEND."&userck=$userck".'&target=reports&from=ipd">Reports</a>',
);
*/
$aSubMenuItem=array(
  'LDRegPatient' => '<a href="javascript:NewRegFxn();">Register patient</a>',
  'LDRegNewBorn' => '<a href="javascript:NewbornFxn();">Register new born</a>',
  'LDSearch' => '<a href="javascript:SearchFxn();">Search patients</a>',
  'LDAdvSearch' => '<a href="javascript:AdvanceSearchFxn();">Advanced search</a>',
  'LDComprehensive' => '<a href="javascript:CompreSearchFxn();">Comprehensive</a>',  
  //added by Genesis D. Ortiz (05-27-2014)
  'LDListOfPatient' => '<a href="javascript:PatientSearchFxn();">List of Patient</a>',
  //end added by Genesis D. Ortiz (05-27-2014)  
  'LDConsultation' => '<a href="javascript:ConsultationFxn();">Admission</a>',
  'LDIcdIcpm' => '<a href="javascript:MedocsFxn();">ICD/ICPM</a>',
  'LDIcdMedCert' => '<a href="javascript:MedCertFxn();">Medical Certificates</a>',
	'LDGenerateOPDReport' => '<a href="javascript:ReportsFxn();">Reports</a>',
);


# Create the submenu rows
/*
print_r($aSubMenuIcon);
echo "<br>";
print_r($aSubMenuText);
echo "<br>";
print_r($aSubMenuItem);
*/
$iRunner = 0;

while(list($x,$v)=each($aSubMenuItem)){
	$sTemp='';                   
	ob_start();
	if($cfg['icons'] != 'no_icon') $smarty2->assign('sIconImg','<img '.$aSubMenuIcon[$iRunner].'>');
	$smarty2->assign('sSubMenuItem',$v);
	$smarty2->assign('sSubMenuText',$aSubMenuText[$iRunner]);
	$smarty2->display('common/seg_submenu_row.tpl');
	$sTemp = ob_get_contents();
 	ob_end_clean();
	$iRunner++;
	$smarty->assign($x,$sTemp);
}

# Assign the submenu items table to the subframe

# Assign the subframe to the mainframe center block
$smarty->assign('sMainBlockIncludeFile','ipd/submenu_ipd.tpl');

  /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');

?>
