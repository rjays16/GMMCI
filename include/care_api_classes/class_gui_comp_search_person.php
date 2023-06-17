<?php
#created/edited by VAN 06-20-08
/**
* @package care_api
*/

/**
*/
//require_once($root_path.'include/care_api_classes/class_core.php');
/**
*  GUI person search methods.
* Dependencies:
* assumes the following files are in the given path
* /include/care_api_classes/class_person.php
* /include/care_api_classes/class_paginator.php
* /include/care_api_classes/class_globalconfig.php
* /include/inc_date_format_functions.php
*  Note this class should be instantiated only after a "$db" adodb  connector object  has been established by an adodb instance
* @author Elpidio Latorilla
* @version beta 2.0.1
* @copyright 2002,2003,2004,2005,2005 Elpidio Latorilla
* @package care_api
*/

$thisfile = basename($HTTP_SERVER_VARS['PHP_SELF']);

class GuiSearchPerson {

	# Default value for the maximum nr of rows per block displayed, define this to the value you wish
	# In normal cases the value is derived from the db table "care_config_global" using the "pagin_insurance_list_max_block_rows" element.
	var $max_block_rows =30 ;
	
	# Set to TRUE if you want to show the option to select  inclusion of the first name in universal searches
	# This would give the user a chance to shut the search for first names and makes the search faster, but the user has one element more to consider
	# If set to FALSE the option will be hidden and both last name and first names will be searched, resulting to slower search
	var $show_firstname_controller = TRUE;

	# Set to TRUE if you want the sql query to be displayed
	# Useful for debugging or optimizing the query
	var $show_sqlquery = FALSE;

	# Set to TRUE to automatically show data if result count is only 1
	var $auto_show_bynumeric = FALSE;
	var $auto_show_byalphanumeric = FALSE;

	# The language tables
	var $langfile = array( 'aufnahme.php', 'personell.php');

	# Initialize some flags
	var $toggle = 0;
	var $mode = '';


	# Set color values for the search mask
	# Default search mask background color
	var $searchmask_bgcolor='#f3f3f3';

	# Default block background color
	var $entry_block_bgcolor='#fff3f3';

	# Default border color
	var $entry_border_bgcolor='#66ee66';

	# Defaut body border color
	var $entry_body_bgcolor='#ffffff';

	# Search key buffer
	var $searchkey='';
	
	# Optional url parameter to append to target url
	var $targetappend ='';

	# The text holder in front of output block
	var $pretext='';

	# The text holder after the output block
	var $posttext='';

	# script parameters buffer
	var $script_vars = array();
	
	# Tipps tricks flag
	var $showtips = TRUE;
	
	# Segworks Addon : sendtoinput
	# added by AJMQ - April 24, 2006
	var $seg_send_to_input = FALSE;
	var $seg_sti_target_window = '';
	var $seg_sti_control_id = '';
	var $seg_sti_close_onclick = TRUE;
	
	# the type of search (person or personnel)
	# burn added: March 16, 2007
	var $seg_search_type;

	var $closefile='main/startframe.php';
	var $thisfile ='' ;
	var $cancelfile = 'main/startframe.php';
	var $targetfile = '';
	var $searchfile = '';
    
    var $ptype;
	
	
	# smarty template
	var $smarty;

	# Flag for output or returning form data
	var $bReturnOnly = FALSE;

	/**
	* Constructor
	*/
	function GuiSearchPerson($target='',$filename='',$cancelfile=''){
		global $thisfile, $root_path;
		if(empty($filename)) $this->thisfile = $thisfile;
			else $this->thisfile = $filename;
		if(!empty($cancelfile)) $this->cancelfile = $cancelfile;
			else $this->cancelfile =$root_path.$this->cancelfile;
		if(!empty($target)){
			$this->targetfile = $target;
			$this->withtarget=TRUE;
		}
	}

	/**
	*	SendToInput Addon
	*/
	function issetSendToInput() {
		return $seg_send_to_input == TRUE;
	}
	
	function prepareSendToInput($target='', $control='') {
		$this->seg_send_to_input = TRUE;
		$this->seg_sti_target_window = $target;
		$this->seg_sti_control_id = $control;
	}	

	/**
	* Sets the target file of each listed item
	*/
	function setTargetFile($target){
		$this->targetfile = $target;
	}
	/**
	* Sets the file name of the script where this gui is  being displayed
	*/
	function setThisFile($target){
		$this->targetfile = $target;
	}
	/**
	* Sets the file name of the script to run when the search button is pressed
	*/
	function setSearchFile($target){
		$this->searchfile = $target;
	}
	/**
	* Sets the file name of the script to run when the cancel button is pressed
	*/
	function setCancelFile($target){
		$this->cancelfile = $target;
	}
	/**
	* Appends a string of url parameters to the target url
	*/
	function appendTargetUrl($str){
		$this->targetappend = $this->targetappend.$str;
	}
	/**
	* Sets the prompt text string
	*/
	function setPrompt($str){
		$this->prompt = $str;
	}

	/**
	* Sets the type of search (person or personnel)
	* burn added: March 16, 2007
	*/
	function setSearchType($str){
		$this->seg_search_type = $str;
	}

	/**
	* Displaying the GUI
	*/

	function display($skey=''){
		global 	$db, $searchkey, $root_path,  $firstname_too, $HTTP_POST_VARS, $HTTP_GET_VARS,
				$sid, $lang, $mode,$totalcount, $pgx, $odir, $oitem, $HTTP_SESSION_VARS,
				$dbf_nodate,  $user_origin, $parent_admit, $status, $target, $origin;
		

		#added by VAN 05-06-08
		include_once($root_path.'include/care_api_classes/class_encounter.php');
		# Create encounter object
		$encounter_obj=new Encounter(); 
		
		#added by VAN 05-13-08
		require_once($root_path.'include/care_api_classes/class_social_service.php');
		$objSS = new SocialService;
		
		#added by VAN 06-20-08
		require_once($root_path.'include/care_api_classes/class_ward.php');
		$ward_obj=new Ward;
		#-------------------------

		require_once($root_path.'include/care_api_classes/class_department.php');
		$dept_obj=new Department;
		
		global $ptype, $allow_patient_register, $allow_newborn_register, $allow_er_user, $allow_opd_user, $allow_ipd_user, $allow_medocs_user, $allow_update;
		
		if (!empty($HTTP_SESSION_VARS['sess_login_userid']))
			$seg_user_name = $HTTP_SESSION_VARS['sess_login_userid'];
		else
			$seg_user_name = $HTTP_SESSION_VARS['sess_temp_userid'];
		$user_dept_info = $dept_obj->getUserDeptInfo($seg_user_name);
		
		$HTTP_SESSION_VARS['dept'] = $user_dept_info['id'];
	
		$this->thisfile = $filename;
		$this->searchkey = $skey;
		$this->mode = $mode;
        
        $this->ptype = $_GET['ptype'];
        
        if(empty($this->targetfile)){
			$withtarget = FALSE;
			$navcolspan = 5;
		}else{
			$withtarget = TRUE;
			$navcolspan = 6;
		}
#echo "here";
		if(!empty($skey)) $searchkey = $skey;
		
		#print_r($HTTP_SESSION_VARS);
		#echo "<br>";
		#print_r($HTTP_POST_VARS);
		
		# Load the language tables
		$lang_tables =$this->langfile;
		include($root_path.'include/inc_load_lang_tables.php');

		# Initialize pages control variables
		if($mode=='paginate'){
			$searchkey=$HTTP_SESSION_VARS['sess_searchkey'];
			//$searchkey='USE_SESSION_SEARCHKEY';
			//$mode='search';
		}else{
			# Reset paginator variables
			$pgx=0;
			$totalcount=0;
			$odir='';
			$oitem='';
		}
#echo "search = ".$searchkey;
		# Create an array to hold the config values
		$GLOBAL_CONFIG=array();

		#Load and create paginator object
		include_once($root_path.'include/care_api_classes/class_paginator.php');
		$pagen=new Paginator($pgx,$this->thisfile,$HTTP_SESSION_VARS['sess_searchkey'],$root_path);

		include_once($root_path.'include/care_api_classes/class_globalconfig.php');
		$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
		$glob_obj->getConfig('person_id_%');

		# Get the max nr of rows from global config
		$glob_obj->getConfig('pagin_person_search_max_block_rows');
		if(empty($GLOBAL_CONFIG['pagin_person_search_max_block_rows'])){
			# Last resort, use the default defined at the start of this page
			$pagen->setMaxCount($max_block_rows);
		}else{
			$pagen->setMaxCount($GLOBAL_CONFIG['pagin_person_search_max_block_rows']);
		}

		//$db->debug=true;
			
		if(!defined('SHOW_FIRSTNAME_CONTROLLER')) define('SHOW_FIRSTNAME_CONTROLLER',$this->show_firstname_controller);

		if(SHOW_FIRSTNAME_CONTROLLER){
			if(isset($HTTP_POST_VARS['firstname_too'])){
				if($HTTP_POST_VARS['firstname_too']){
					$firstname_too=1;
				}elseif($mode=='paginate'&&isset($HTTP_GET_VARS['firstname_too'])&&$HTTP_GET_VARS['firstname_too']){
					$firstname_too=1;
				}
			}elseif($mode!='search'){
				$firstname_too=TRUE;
			}
		}
		#echo "id = ".$user_dept_info['id'];
		#added by VAN 05-06-08
		#if ($user_dept_info['id']=='OPD-Triage'){
			if (empty($this->mode))
				$this->mode='search';

			if (empty($mode))	
				$mode ='search';
		#}		
	 #echo "s = ".$searchkey;
		if((!empty($searchkey))&&($this->mode=='search' || $this->mode=='paginate')){
			# Translate *? wildcards
			$searchkey=strtr($searchkey,'*?','%_');
			include_once($root_path.'include/inc_date_format_functions.php');

			include_once($root_path.'include/care_api_classes/class_person.php');
			$person=& new Person();
			/*
			if (empty($oitem))
				$oitem = 'date_reg';
			if (empty($odir))
				$odir = 'DESC';	
			*/	
			
			$enctype = $HTTP_POST_VARS['enctype'];
			/*
			if ($enctype==1)
				$enctype_cond = "AND enc.encounter_type IN (1)";	
			elseif ($enctype==2)
				$enctype_cond = "AND enc.encounter_type IN (2)";		
			else if ($enctype==3)
				$enctype_cond = "AND enc.encounter_type IN (3,4)";
			else
				$enctype_cond = "";	
			*/	
			#echo "<br>enctype = ".$HTTP_SESSION_VARS['enctype'];
			#echo "<br>search = ".$searchkey;
			#print_r($HTTP_SESSION_VARS);
			# Set the sorting directive
			if(isset($oitem)&&!empty($oitem)) $sql3 =" ORDER BY $oitem $odir";
			
			if($mode=='paginate'){
				$fromwhere=$HTTP_SESSION_VARS['sess_searchkey'];
				#echo "<br>fromwhere = ".$fromwhere;
				
				$sql="SELECT SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.encounter_nr)),20) AS encounter_nr, 
							cp.pid,cp.name_last,cp.name_first,cp.date_birth, cp.sex,cp.death_date, cp.death_encounter_nr, 
							SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.encounter_type)),20) AS encounter_type, 
							SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.area)),20) AS area, 
							SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.current_ward_nr)),20) AS current_ward_nr, 
							SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.current_room_nr)),20) AS current_room_nr, 
							SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.current_dept_nr)),20) AS current_dept_nr, 
							SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.is_medico)),20) AS is_medico, 
							SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.encounter_date)),20) AS encounter_date, 
							SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.admission_dt)),20) AS admission_dt, 
							SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.discharge_date)),20) AS discharge_date, 
							SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.is_confidential)),20) AS is_confidential, 
							SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.is_discharged)),20) AS is_discharged 
						 FROM ".$fromwhere.$sql3;   # burn added: March 8, 2007
				
				$ergebnis=$db->SelectLimit($sql,$pagen->MaxCount(),$pagen->BlockStartIndex());
				$linecount=$ergebnis->RecordCount();
			}else{
				
				$ergebnis=$person->CompSearchSelect($searchkey,$pagen->MaxCount(),$pagen->BlockStartIndex(),$oitem,$odir,$firstname_too,$enctype);
				#echo $person->sql;
				#Retrieve the sql fromwhere portion
				$fromwhere=$person->buffer;
				
				#added by VAN 05-07-08
				$fromwhere2=$person->buffer2;
				
				$HTTP_SESSION_VARS['sess_searchkey']=$fromwhere;
				
				$sql=$person->getLastQuery();
				$linecount=$person->LastRecordCount();
				#echo "count = ".$linecount;
				
			}

			if($ergebnis){

				$pagen->setTotalBlockCount($linecount);

				# If more than one count all available
				if(isset($totalcount)&&$totalcount){
					$pagen->setTotalDataCount($totalcount);
				}else{
					# Count total available data
					$sql='SELECT COUNT(cp.pid) AS maxnr FROM '.$fromwhere;
					if($result=$db->Execute($sql)){
						$totalcount=$result->RecordCount();   #burn added, October 18, 2007
					}
					$pagen->setTotalDataCount($totalcount);
				}
				# Set the sort parameters
				$pagen->setSortItem($oitem);
				$pagen->setSortDirection($odir);
			}else{
				if($show_sqlquery) echo $sql;
			}

		} else {
			$mode='';
		}

		$entry_block_bgcolor=$this->entry_block_bgcolor;
		$entry_border_bgcolor=$this->entry_border_bgcolor;
		$entry_body_bgcolor=$this->entry_body_bgcolor;
		$searchmask_bgcolor= $this->searchmask_bgcolor;


		##############  Here starts the html output
		
		# Start Smarty templating here
		# Create smarty object without initiliazing the GUI (2nd param = FALSE)

		include_once($root_path.'gui/smarty_template/smarty_care.class.php');
		$this->smarty = new smarty_care('common',FALSE);

		# Output any existing text before the search block
		if(!empty($this->pretext)) $this->smarty->assign('sPretext',$this->pretext);

		# Show tips and tricks link and the javascript
		if($this->showtips){
			ob_start();
				include_once($root_path.'include/inc_js_gethelp.php');
				$sTemp = ob_get_contents();
				$this->smarty->assign('sJSGetHelp',$sTemp);
			ob_end_clean();

			$this->smarty->assign('LDTipsTricks','<a href="javascript:gethelp(\'person_search_tips.php\')">'.$LDTipsTricks.'</a>');
		
		}
		

		#
		# Prepare the javascript validator
		#
		
	/*if ($user_dept_info['id']=='OPD-Triage'){
		if(!isset($searchform_count) || !$searchform_count){
			$this->smarty->assign('sJSFormCheck','<script language="javascript">
				
				function isValidSearch(key) {          

					if (typeof(key)==\'undefined\') return false;
					var s=key.toUpperCase();
					return (
						/^[A-Z0-9Ñ\-\.]{2}[A-Z0-9Ñ\-\. ]*\s*,\s*[A-Z0-9Ñ\-\.]{2}[A-Z0-9Ñ\-\. ]*$/.test(s) ||
						/^\d{1,2}\/\d{1,2}\/\d{4}$/.test(s) ||
						/^\d{1,2}\-\d{1,2}\-\d{4}$/.test(s) ||
						/^\d+$/.test(s)
					);
				}
				
				function chkSearch(d){
					
					if((d.searchkey.value=="") || (d.searchkey.value==" ") || (d.searchkey.value.length < 3)){
						d.searchkey.focus();
						return false;
					}else	{
						return true;
					}
					
					return true;
				}
				
				function DisabledSearch(){
					var b=isValidSearch(document.getElementById(\'searchkey\').value);
					document.getElementById("searchButton").style.cursor=(b?"pointer":"default");
					document.getElementById("searchButton").disabled = !b;
				}
			
			</script>');
		}
	}else{*/
		if(!isset($searchform_count) || !$searchform_count){
			$this->smarty->assign('sJSFormCheck','<script language="javascript">
				
				function isValidSearch(key) {          

					if (typeof(key)==\'undefined\') return false;
					var s=key.toUpperCase();
					return (
						/^[A-Z0-9Ñ\-\.]{2}[A-Z0-9Ñ\-\. ]*\s*,\s*[A-Z0-9Ñ\-\.]{2}[A-Z0-9Ñ\-\. ]*$/.test(s) ||
						/^\d{1,2}\/\d{1,2}\/\d{4}$/.test(s) ||
						/^\d{1,2}\-\d{1,2}\-\d{4}$/.test(s) ||
						/^\d+$/.test(s)
					);
					/*return (
						/^[A-Z0-9Ñ\-\.]{2}[A-Z0-9Ñ\-\. ]*\s*,\s*[A-Z0-9Ñ\-\.]{2}[A-Z0-9Ñ\-\. ]*$/.test(s)
					);*/
				}
				
				function chkSearch(d){
					
					if((d.searchkey.value=="") || (d.searchkey.value==" ") || (d.searchkey.value.length < 3)){
						d.searchkey.focus();
						return false;
					}else	{
						return true;
					}
					
					return true;
				}
				
				/*function DisabledSearch(){
					if (document.getElementById("searchkey").value.length < 3){
						document.getElementById("searchButton").style.cursor="default";
						document.getElementById("searchButton").disabled = true;
					}else{
						document.getElementById("searchButton").style.cursor="pointer";
						document.getElementById("searchButton").disabled = false;
					}	
				}*/
				
				function DisabledSearch(){
					var b=isValidSearch(document.getElementById(\'searchkey\').value);
					document.getElementById("searchButton").style.cursor=(b?"pointer":"default");
					document.getElementById("searchButton").disabled = !b;
				}
			</script>');
	//	}
	}	

		#
		# Prepare the search file
		#
		if(empty($this->searchfile)) $search_script = $this->thisfile;
			else $search_script = $this->searchfile;

		#
		# Prepare the form params
		#
		$sTemp = 'method="post" name="searchform';
		if($searchform_count) $sTemp = $sTemp."_".$searchform_count;
		$sTemp = $sTemp.'" onSubmit="return chkSearch(this)"';
		 if(isset($search_script) && $search_script!='') $sTemp = $sTemp.' action="'.$search_script.'"';
		$this->smarty->assign('sFormParams',$sTemp);

		if(empty($this->prompt)) $searchprompt=$LDEntryPrompt;
			else $searchprompt=$this->prompt;

		$this->smarty->assign('searchprompt',$searchprompt);

		#
		# Prepare the checkbox input
		#
		if(defined('SHOW_FIRSTNAME_CONTROLLER')&&SHOW_FIRSTNAME_CONTROLLER){
			if(isset($firstname_too)&&$firstname_too) $sTemp= 'checked';
			$this->smarty->assign('sCheckBoxFirstName','<input type="checkbox" name="firstname_too" '.$sTemp.'>');
			$this->smarty->assign('LDIncludeFirstName',$LDIncludeFirstName);
			
            #added by VAN 06-21-08
			if ($this->ptype=='ipd'){
                $enctype = 3;
            }elseif ($this->ptype=='er'){
                $enctype = 1;
            }elseif (($this->ptype=='opd')||($this->ptype=='phs')){
                $enctype = 2;
            }else{
                $enctype = 0;    
            }
            #echo "en = ".$HTTP_POST_VARS['enctype'];            
			if ($HTTP_POST_VARS['enctype']!='')
                $enctype = $HTTP_POST_VARS['enctype'];
            
            $this->smarty->assign('sCheckAll','<input type="radio" name="enctype" id="enctype" value="0" '.(!($enctype)?'checked="checked" ':'').' onClick="UpdateQuery(this.value);" >');
			$this->smarty->assign('LDCheckAll',"All");
			
			$this->smarty->assign('sCheckER','<input type="radio" name="enctype" id="enctype" value="1" '.(($enctype==1)?'checked="checked" ':'').' onClick="UpdateQuery(this.value);" >');
			$this->smarty->assign('LDCheckER',"ER");
			
			$this->smarty->assign('sCheckOPD','<input type="radio" name="enctype" id="enctype" value="2" '.(($enctype==2)?'checked="checked" ':'').' onClick="UpdateQuery(this.value);" >');
			$this->smarty->assign('LDCheckOPD',"OPD");
			
			$this->smarty->assign('sCheckIPD','<input type="radio" name="enctype" id="enctype" value="3" '.(($enctype==3)?'checked="checked" ':'').' onClick="UpdateQuery(this.value);" >');
			$this->smarty->assign('LDCheckIPD',"IPD");
			#print_r($HTTP_POST_VARS);
			$this->smarty->assign('sSearchKey','<input type="text" name="searchkey" id="searchkey" value="'.$HTTP_POST_VARS['searchkey'].'" size=40 maxlength=80 onKeyUp="DisabledSearch();" onBlur="DisabledSearch();">');
			#$this->smarty->assign('sKeyPost','<input type="text" name="key" id="key" value="'.$HTTP_POST_VARS['searchkey'].'">');
			#--------------
		}

		#
		# Prepare the hidden inputs
		#
		$this->smarty->assign('sHiddenInputs','<input name="searchButton" id="searchButton" type="image" '.createLDImgSrc($root_path,'searchlamp.gif','0','absmiddle').'>
				<input type="hidden" name="sid" value="'.$sid.'">
				<input type="hidden" name="lang" value="'.$lang.'">
				<input type="hidden" name="noresize" value="'.$noresize.'">
				<input type="hidden" name="target" value="'.$target.'">
				<input type="hidden" name="user_origin" value="'.$user_origin.'">
				<input type="hidden" name="origin" value="'.$origin.'">
				<input type="hidden" name="retpath" value="'.$retpath.'">
				<input type="hidden" name="aux1" value="'.$aux1.'">
				<input type="hidden" name="ipath" value="'.$ipath.'">
				<input type="hidden" name="mode" value="search">');

		#
		# Create append data for previous and next page links
		#
		$this->targetappend.="&firstname_too=$firstname_too&origin=$origin";
		#echo "target = ".$this->targetappend;
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
			
			/*
			$this->smarty->assign('LDRegistryNr',$pagen->makeSortLink($LDRegistryNr,'pid',$oitem,$odir,$this->targetappend));
			$this->smarty->assign('LDSex',$pagen->makeSortLink($LDSex,'sex',$oitem,$odir,$this->targetappend));
			$this->smarty->assign('LDLastName',$pagen->makeSortLink($LDLastName,'name_last',$oitem,$odir,$this->targetappend));
			$this->smarty->assign('LDFirstName',$pagen->makeSortLink($LDFirstName,'name_first',$oitem,$odir,$this->targetappend));
			$this->smarty->assign('LDBday',$pagen->makeSortLink($LDBday,'date_birth',$oitem,$odir,$this->targetappend));
			#added by VAN 06-20-08
			$this->smarty->assign('LDAdmission',$pagen->makeSortLink($LDAdmission,'admission_dt',$oitem,$odir,$this->targetappend));  
			$this->smarty->assign('LDDischarge',$pagen->makeSortLink("Discharge Date",'discharge_date',$oitem,$odir,$this->targetappend));   
			$this->smarty->assign('LDLocation',$pagen->makeSortLink($LDLocation,'current_ward_nr',$oitem,$odir,$this->targetappend));   
			*/
            $this->smarty->assign('LDCaseNr','Case Number');
			$this->smarty->assign('LDRegistryNr',$LDRegistryNr);
			$this->smarty->assign('LDSex',$LDSex);
			$this->smarty->assign('LDLastName',$LDLastName);
			$this->smarty->assign('LDFirstName',$LDFirstName);
			$this->smarty->assign('LDBday',$LDBday);
			#added by VAN 06-20-08
			$this->smarty->assign('LDAdmission',$LDAdmission);  
			$this->smarty->assign('LDDischarge',"Discharge Date");   
			$this->smarty->assign('LDLocation',$LDLocation);   
			#--------------
			if(!empty($this->targetfile)){
				$this->smarty->assign('LDOptions',$LDDetails);
			}
            $this->smarty->assign('LDOptions2','View');   

			#
			# Generate the resulting list rows using the reg_comp_search_list_row.tpl template
			#

			$sTemp = '';
			$toggle=0;
				# sets the date in 'MM/dd/yyyy' format
			$date_format = getDateFormat();   # burn added: May 19, 2007
			while($zeile=$ergebnis->FetchRow()){
				#print_r($zeile);		
				if($zeile['status']=='' || $zeile['status']=='normal'){

					$this->smarty->assign('toggle',$toggle);
					$toggle = !$toggle;
					
                    $this->smarty->assign('sCaseNr',$zeile['encounter_nr']);
                    	
						# burn added: March 15, 2007
					$label='';
					#edited by VAN 02-20-2012
                    /*
                    if ( $encounter_obj->isCurrentlyAdmitted($zeile['pid'],'_PID') &&
							($enc_row = $encounter_obj->getLatestEncounter($zeile['pid'])) ){
						
						if($enc_row['encounter_type']==1){
							$label =	'<img '.createComIcon($root_path,'flag_red.gif').'>'.
										'<font size=1 color="red">ER</font>';
						}elseif($enc_row['encounter_type']==2){
							$label =	'<img '.createComIcon($root_path,'flag_blue.gif').'>'.
										'<font size=1 color="blue">OPD</font>';
						}else{
							$label =	'<img '.createComIcon($root_path,'flag_green.gif').'>'.
										'<font size=1 color="green">IPD</font>';
						}
					#added by VAN 12-30-08
					}elseif($enc_row = $encounter_obj->getLatestEncounter($zeile['pid'])){
						if($enc_row['encounter_type']==1){
							$label =	'<img '.createComIcon($root_path,'flag_red.gif').'>'.
										'<font size=1 color="red">ER</font>';
						}elseif($enc_row['encounter_type']==2){
							$label =	'<img '.createComIcon($root_path,'flag_blue.gif').'>'.
										'<font size=1 color="blue">OPD</font>';
						}else{
							$label =	'<img '.createComIcon($root_path,'flag_green.gif').'>'.
										'<font size=1 color="green">IPD</font>';
						}	
						
					}else{
						$enc_row['encounter_type']=0;   # no ACTIVE encounter
					}
                    */
                    if($zeile['encounter_type']==1){
                        $label =    '<img '.createComIcon($root_path,'flag_red.gif').'>'.
                                    '<font size=1 color="red">ER</font>';
                    }elseif($zeile['encounter_type']==2){
                        $label =    '<img '.createComIcon($root_path,'flag_blue.gif').'>'.
                                    '<font size=1 color="blue">OPD</font>';
                    }elseif(($zeile['encounter_type']==3)||($zeile['encounter_type']==4)){
                        $label =    '<img '.createComIcon($root_path,'flag_green.gif').'>'.
                                    '<font size=1 color="green">IPD</font>';
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
					if($zeile['death_date']&&$zeile['death_date']!=$dbf_nodate) $this->smarty->assign('sCrossIcon','<img '.createComIcon($root_path,'blackcross_sm.gif','0','absmiddle').'>&nbsp;<span style="font-size:9px">'.$zeile['death_date'].'</span>');
						else $this->smarty->assign('sCrossIcon','');
					
					$date_birth = @formatDate2Local($zeile['date_birth'],$date_format);
					$bdateMonth = substr($date_birth,0,2);
					$bdateDay = substr($date_birth,3,2);
					$bdateYear = substr($date_birth,6,4);
					if (!checkdate($bdateMonth, $bdateDay, $bdateYear)){
						$date_birth='';
					}
					$this->smarty->assign('sBday',$date_birth);   # burn added: March 26, 2007
					#echo "<br>".$zeile['encounter_date'];
					#added by VAN 06-21-08
					if (($zeile['encounter_type']==1)||($zeile['encounter_type']==2)){
						$admission_date = date("m/d/Y",strtotime($zeile['encounter_date']));
						
						if ($zeile['encounter_type']==1)
							$location = "ER";
						elseif ($zeile['encounter_type']==2){
							$dept = $dept_obj->getDeptAllInfo($zeile['current_dept_nr']);
							$location = $dept['id'];
						}
						
					}elseif (($zeile['encounter_type']==3)||($zeile['encounter_type']==4)){
						$admission_date = date("m/d/Y",strtotime($zeile['admission_dt']));
						
						$ward = $ward_obj->getWardInfo($zeile['current_ward_nr']);
						if ($zeile['current_room_nr'])
							$room = $zeile['current_room_nr'];
						else{
							if ($zeile['area'])
								$room = $zeile['area'];	
							else
								$room = '0';		
						}
						$location = $ward['ward_id']." : Rm.#".$room;
					}	
					#echo "<BR>si = ".$zeile['is_discharged'];
					if($zeile['is_discharged']==1){
						$discharge_date = date("m/d/Y",strtotime($zeile['discharge_date']));
						$location = '<font size=1 color="blue"><strong>DISCHARGED</strong></font>';
					}else{
						$discharge_date = "STILL IN";
					}
					
					if ($zeile['is_confidential']==1){					
						$admission_date = '<font size=1 color="red"><strong>CONFIDENTIAL</strong></font>';
						$location = '<font size=1 color="red"><strong>CONFIDENTIAL</strong></font>';
						$discharge_date = '<font size=1 color="red"><strong>CONFIDENTIAL</strong></font>';
					}	
					
					$this->smarty->assign('sAdmissionDate',$admission_date);
					$this->smarty->assign('sLocation',$location);
					
					$this->smarty->assign('sDischargeDate',$discharge_date);

						# burn added: March 16, 2007
					/*if ( ($user_dept_info['dept_nr']==150) &&
							(($enc_row['encounter_type']==0) || $enc_row['encounter_type']==2)
						){
						
						#added by VAN 05-06-08
						$name = trim($zeile['name_first'])." ".trim($zeile['name_last']);
						$encounter_obj->getPatientOPDORNoforADay($zeile['pid'],$name);
						
						$patSS = $objSS->getPatientSocialClass($zeile['pid']);

						#if paid or senior citizen
						if (($encounter_obj->count)||($zeile['senior_ID'])||($zeile['personnelID'])||($patSS['parentid']=='D')){
							$allow_show_details=TRUE;   # search under OPD Triage and patient is already paid for current day
						}else{
							$allow_show_details=FALSE;   # search under OPD Triage
						}	
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
					*/
					/*
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
						$sTarget=$sTarget.'<img '.createLDImgSrc($root_path,'detail.gif','0').' title="'.$LDShowDetails.'"></a>';
						$this->smarty->assign('sOptions',$sTarget);
					}
					elseif ($withtarget) {
						
						$sTarget='';
						if ($allow_show_details){
							$sTarget = "<a href=\"$this->targetfile".URL_APPEND."&pid=".$zeile['pid']."&edit=1&status=".$status."&target=".$target."&user_origin=".$user_origin."&noresize=1&mode=\">";
							$sTarget=$sTarget.'<img '.createLDImgSrc($root_path,'detail.gif','0').' title="'.$LDShowDetails.'"></a>';
						}
						$this->smarty->assign('sOptions',$sTarget);
					}
					*/
					#edited by VAN 06-21-08
					/*
					$sTarget='<img '.createLDImgSrc($root_path,'detail.gif','0').' title="'.$LDShowDetails.'"></a>';
						$this->smarty->assign('sOptions',$sTarget);
					*/
					
					$this->smarty->assign('sOptions','<img type="image" name="select-enc" id="select-enc" '.createLDImgSrc($root_path,'detail.gif','0').' title="'.$LDShowDetails.'" border="0" style="cursor:pointer;"
										onclick="return overlib(
										OLiframeContent(\''.$root_path.'modules/registration_admission/seg-patient-history.php?pid='.$zeile['pid'].'\', 800, 400, \'fSelEnc\', 1, \'auto\'),
										WIDTH,800, TEXTPADDING,0, BORDER,0, 
										STICKY, SCROLL, CLOSECLICK, MODAL, 
										CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
										CAPTIONPADDING,4, 
										CAPTION,\'Patient Hospital History\',
										MIDX,0, MIDY,0, 
										STATUS,\'Patient Hospital History\');"
									onmouseout="nd();">');
                                    
                    #added by VAN 02-20-2012
                    $this->smarty->assign('sOptions2','<img type="image" name="select-enc" id="select-enc" '.createLDImgSrc($root_path,'information.png','0').' title="View Information" border="0" style="cursor:pointer;"
                                        onclick="return overlib(
                                        OLiframeContent(\''.$root_path.'modules/registration_admission/seg-patient-info.php?pid='.$zeile['pid'].'&encounter_nr='.$zeile['encounter_nr'].'\', 820, 490, \'fSelEnc\', 1, \'auto\'),
                                        WIDTH,820, TEXTPADDING,0, BORDER,0, 
                                        STICKY, SCROLL, CLOSECLICK, MODAL, 
                                        CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
                                        CAPTIONPADDING,4, 
                                        CAPTION,\'Patient Information Details\',
                                        MIDX,0, MIDY,0, 
                                        STATUS,\'Patient Information Details\');"
                                    onmouseout="nd();">');                
                                    
					
					if(!file_exists($root_path.'cache/barcodes/pn_'.$zeile['pid'].'.png')){
						$this->smarty->assign('sHiddenBarcode',"<img src='".$root_path."classes/barcode/image.php?code=".$zeile['pid']."&style=68&type=I25&width=180&height=50&xres=2&font=5&label=2' border=0 width=0 height=0>");
					}
					#
					# Generate the row in buffer and append as string
					#
					ob_start();
						$this->smarty->display('registration_admission/reg_comp_search_list_row.tpl');
						$sTemp = $sTemp.ob_get_contents();
					ob_end_clean();
				}
			}
			#
			# Assign the rows string to template
			#
			$this->smarty->assign('sResultListRows',$sTemp);
			$this->smarty->assign('sPreviousPage',$pagen->makePrevLink($LDPrevious,$this->targetappend));
				$yhPrev = $this->thisfile.$pagen->yhPgPrev;
			
			$this->smarty->assign('sNextPage',$pagen->makeNextLink($LDNext,$this->targetappend));
				$yhNext = $this->thisfile.$pagen->yhPgNext;
		}
		
		ob_start();
			include_once($root_path.'modules/registration_admission/include/yh_page.php');
			$tmp1 = ob_get_contents();
		ob_end_clean();		
		$this->smarty->assign('yhPrevNext',$tmp1);
		#
		# Add eventual appending text block
		#
		if(!empty($this->posttext)) $this->smarty->assign('sPostText',$this->posttext);
		
		#
		# Displays the search page
		#
		if($this->bReturnOnly){
			ob_start();
				$this->smarty->display('registration_admission/reg_comp_search_main.tpl');
				$sTemp=ob_get_contents();
			ob_end_clean();
			return $sTemp;
		}else{
			# show Template
			$this->smarty->display('registration_admission/reg_comp_search_main.tpl');
		}
	} // end of function display()

	/**
	* Generates the search page contents but will not output it. Instead it will buffer the output and return it as a string.
	*/
	function create($skey=''){
		$this->bReturnOnly = TRUE;
		return $this->display($skey);
	}
} // end of class
?>