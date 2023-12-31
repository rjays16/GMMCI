<?php
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

class GuiDuplicatePerson {

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


	# smarty template
	var $smarty;

	# Flag for output or returning form data
	var $bReturnOnly = FALSE;

	/**
	* Constructor
	*/
	function GuiDuplicatePerson($target='',$filename='',$cancelfile=''){
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


#echo "class_gui_duplicate_person.php : A searchkey = '".$searchkey."' <br> \n";

#echo "class_gui_duplicate_person.php : basename(HTTP_SERVER_VARS['PHP_SELF']) <br> ";
#echo basename($HTTP_SERVER_VARS['PHP_SELF'])." <br> \n";
#echo "class_gui_duplicate_person.php : HTTP_SERVER_VARS['PHP_SELF'] = '".$HTTP_SERVER_VARS['PHP_SELF']."' <br> \n";
#echo "class_gui_duplicate_person.php : thisfile = '".$thisfile."' <br> \n";
#echo "class_gui_duplicate_person.php : filename = '".$filename."' <br> \n";
#echo "class_gui_duplicate_person.php : global mode = '".$mode."' <br> \n";
		$this->thisfile = $filename;
		$this->searchkey = $skey;
		$this->mode = $mode;
#echo "class_gui_duplicate_person.php : this->thisfile = '".$this->thisfile."' <br> \n";
		if(empty($this->targetfile)){
			$withtarget = FALSE;
			$navcolspan = 5;
		}else{
			$withtarget = TRUE;
			$navcolspan = 6;
		}

		if(!empty($skey)) $searchkey = $skey;

		# Load the language tables
		$lang_tables =$this->langfile;
		include($root_path.'include/inc_load_lang_tables.php');
#echo "class_gui_duplicate_person.php : ABC mode = '".$mode."' <br> \n";
#echo "class_gui_duplicate_person.php : ABC HTTP_GET_VARS['dup_mode'] = '".$HTTP_GET_VARS['dup_mode']."' <br> \n";
		# Initialize pages control variables
#		if($mode=='paginate'){
		if( ($mode=='paginate') || ($HTTP_GET_VARS['dup_mode']=='paginate')){
#echo "class_gui_duplicate_person.php : inside <br> \n";
#echo "class_gui_duplicate_person.php : inside HTTP_SESSION_VARS['sess_searchkey'] = '".$HTTP_SESSION_VARS['sess_searchkey']."' <br> \n";
#echo "class_gui_duplicate_person.php : inside b4 searchkey = '".$searchkey."' <br> \n";
			$searchkey=$HTTP_SESSION_VARS['sess_searchkey'];
#echo "class_gui_duplicate_person.php : inside after searchkey = '".$searchkey."' <br> \n";
			//$searchkey='USE_SESSION_SEARCHKEY';
			//$mode='search';
		}else{
			# Reset paginator variables
			$pgx=0;
			$totalcount=0;
			$odir='';
			$oitem='';
		}

		# Create an array to hold the config values
		$GLOBAL_CONFIG=array();

		#Load and create paginator object
		include_once($root_path.'include/care_api_classes/class_paginator.php');
		$pagen=new Paginator($pgx,$this->thisfile,$HTTP_SESSION_VARS['sess_searchkey'],$root_path);
		$pagen->duplicate_mode = TRUE;  # sets the duplicate mode to TRUE

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

#echo "class_gui_duplicate_person.php : 1 searchkey = '".$searchkey."'; this->mode = '".$this->mode."' <br> \n";
		if(($this->mode=='search' || $this->mode=='paginate' || $HTTP_GET_VARS['dup_mode']=='paginate') && !empty($searchkey)){

			# Translate *? wildcards
			$searchkey=strtr($searchkey,'*?','%_');

			include_once($root_path.'include/inc_date_format_functions.php');

			include_once($root_path.'include/care_api_classes/class_person.php');
			$person=& new Person();

			# Set the sorting directive
			if(isset($oitem)&&!empty($oitem)) $sql3 =" ORDER BY $oitem $odir";

			//$sql='SELECT * FROM '.$dbtable.$sql2;

#echo "class_gui_duplicate_person.php : HTTP_SESSION_VARS['sess_searchkey'] = '".$HTTP_SESSION_VARS['sess_searchkey']."'; mode = '".$mode."' <br> \n";
#			if($mode=='paginate'){
			if( ($mode=='paginate') || ($HTTP_GET_VARS['dup_mode']=='paginate')){
				$fromwhere=$HTTP_SESSION_VARS['sess_searchkey'];

#				$sql='SELECT pid, name_last, name_first, date_birth, addr_zip, sex, death_date, status FROM '.$fromwhere.$sql3;   # burn commented: March 8, 2007
				$sql="SELECT pid, name_last, name_first, name_middle, date_birth, addr_zip, sex, death_date, status ".
						" , sb.brgy_name, sm.zipcode, sm.mun_name, sp.prov_name, sr.region_name ".
						" FROM ".$fromwhere.$sql3;   # burn added: March 8, 2007
#echo "class_gui_duplicate_person.php : paginate sql = '".$sql."' <br> \n";
				$ergebnis=$db->SelectLimit($sql,$pagen->MaxCount(),$pagen->BlockStartIndex());
				$linecount=$ergebnis->RecordCount();
			}else{
#echo "class_gui_duplicate_person.php : 2 searchkey = '".$searchkey."'; mode = '".$mode."' <br> \n";
				$ergebnis=$person->SearchSelectDuplicatePerson($searchkey,$pagen->MaxCount(),$pagen->BlockStartIndex(),$oitem,$odir,$firstname_too);
#echo "class_gui_duplicate_person.php : ergebnis : <br> "; print_r($ergebnis); echo " <br> \n";
				#Retrieve the sql fromwhere portion
				$fromwhere=$person->buffer;
#echo "class_gui_duplicate_person.php : fromwhere = '".$fromwhere."' <br> \n";
				$HTTP_SESSION_VARS['sess_searchkey']=$fromwhere;
				$sql=$person->getLastQuery();
#echo "class_gui_duplicate_person.php : NOT(paginate) sql = '".$sql."' <br> \n";
				$linecount=$person->LastRecordCount();
			}

			if($ergebnis){
/*					# burn comment; March 13, 2007
				if($linecount==1){
					if(( $this->auto_show_bynumeric && $person->is_nr) || $this->auto_show_byalphanumeric  ){
						$zeile=$ergebnis->FetchRow();
						header("location:".$this->targetfile."?sid=".$sid."&lang=".$lang."&pid=".$zeile['pid']."&edit=1&status=".$status."&user_origin=".$user_origin."&noresize=1&mode=");
						exit;
					}
				}
*/
				$pagen->setTotalBlockCount($linecount);

				# If more than one count all available
				if(isset($totalcount)&&$totalcount){
					$pagen->setTotalDataCount($totalcount);
				}else{
					# Count total available data
					$sql='SELECT COUNT(pid) AS maxnr FROM '.$fromwhere;
					if($result=$db->Execute($sql)){
						if ($result->RecordCount()) {
							$rescount=$result->FetchRow();
							$totalcount=$rescount['maxnr'];
						}
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
		if(!isset($searchform_count) || !$searchform_count){
			$this->smarty->assign('sJSFormCheck','<script language="javascript">
			<!--
				function chkSearch(d){
					if((d.searchkey.value=="") || (d.searchkey.value==" ")){
						d.searchkey.focus();
						return false;
					}else	{
						return true;
					}
				}
			// -->
			</script>');
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
		//$searchprompt=$LDEnterEmployeeSearchKey;
		$this->smarty->assign('searchprompt',$searchprompt);

		#
		# Prepare the checkbox input
		#
		if(defined('SHOW_FIRSTNAME_CONTROLLER')&&SHOW_FIRSTNAME_CONTROLLER){
			if(isset($firstname_too)&&$firstname_too) $sTemp= 'checked';
			$this->smarty->assign('sCheckBoxFirstName','<input type="checkbox" name="firstname_too" '.$sTemp.'>');
			$this->smarty->assign('LDIncludeFirstName',$LDIncludeFirstName);
		}

		#
		# Prepare the hidden inputs
		#
		$this->smarty->assign('sHiddenInputs','<input type="image" '.createLDImgSrc($root_path,'searchlamp.gif','0','absmiddle').'>
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

		$this->smarty->assign('sCancelButton','<a href="'.$this->cancelfile.URL_APPEND.'"><img '.createLDImgSrc($root_path,'cancel.gif','0').'></a>');

		//include($root_path.'include/inc_patient_searchmask.php');
		#
		# Create append data for previous and next page links
		#
#echo "class_gui_duplicate_person.php : this->targetappend 1 = '".$this->targetappend."' <br> \n";
		$this->targetappend.="&firstname_too=$firstname_too&origin=$origin";
#echo "class_gui_duplicate_person.php : this->targetappend 2 = '".$this->targetappend."' <br> \n";
		//echo $mode;
		if($parent_admit) $bgimg='tableHeaderbg3.gif';
			else $bgimg='tableHeader_gr.gif';
		$tbg= 'background="'.$root_path.'gui/img/common/'.$theme_com_icon.'/'.$bgimg.'"';

#echo "if($mode=='search'||$mode=='paginate') line 457 : mode = '".$mode."' <br> \n";
#echo "if($mode=='search'||$mode=='paginate') line 457 : _GET['dup_mode'] = '".$_GET['dup_mode']."' <br> \n";
		if($mode=='search'||$mode=='paginate' || $_GET['dup_mode']=='paginate'){
			if ($linecount) $this->smarty->assign('LDSearchFound',str_replace("~no.~",$totalcount,$LDSearchFound).' '.$LDShowing.' '.$pagen->BlockStartNr().' '.$LDTo.' '.$pagen->BlockEndNr().'.');
				else $this->smarty->assign('LDSearchFound',str_replace('~no.~','0',$LDSearchFound));
		}

#echo "seg_user_name = '".$seg_user_name."' <br> \n";
		if ($linecount){

			$this->smarty->assign('bShowResult',TRUE);

			$img_male=createComIcon($root_path,'spm.gif','0');
			$img_female=createComIcon($root_path,'spf.gif','0');

			$this->smarty->assign('LDRegistryNr',$pagen->makeSortLink($LDRegistryNr,'pid',$oitem,$odir,$this->targetappend));
			$this->smarty->assign('LDSex',$pagen->makeSortLink($LDSex,'sex',$oitem,$odir,$this->targetappend));
			$this->smarty->assign('LDLastName',$pagen->makeSortLink($LDLastName,'name_last',$oitem,$odir,$this->targetappend));
			$this->smarty->assign('LDFirstName',$pagen->makeSortLink($LDFirstName,'name_first',$oitem,$odir,$this->targetappend));
			$this->smarty->assign('LDMiddleName',$pagen->makeSortLink('Middle Name','name_middle',$oitem,$odir,$this->targetappend));
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
#echo "URL_APPEND = '".URL_APPEND."' <br> \n";

			$sTemp = '';
			$toggle=0;
				# sets the date in 'MM/dd/yyyy' format
			$date_format = getDateFormat();   # burn added: May 19, 2007
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
					#echo "<br>mname = ".$zeile['name_middle'];
					$this->smarty->assign('sLastName',ucfirst($zeile['name_last']));
					$this->smarty->assign('sFirstName',ucfirst($zeile['name_first']));
					$this->smarty->assign('sMiddleName',ucfirst($zeile['name_middle']));
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

					if ($_GET['target']=='person_reg')
						$direct_location = 'person_register.php';
					else
						$direct_location = 'patient_register.php';


					$sTarget = ':: <a href="person_reg_showdetail.php'.URL_APPEND.'&pid='.$zeile['pid'].'&from=entry&newdata=1&target=entry" target="_blank">'.$LDShowDetails.'</a> ::'.
									' <a href="'.$direct_location.URL_APPEND.'&pid='.$zeile['pid'].'&update=1">'.$LDUpdate.'</a>';
					$this->smarty->assign('sOptions',$sTarget);

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
				$this->smarty->display('registration_admission/reg_duplicate_main.tpl');
				$sTemp=ob_get_contents();
			ob_end_clean();
			return $sTemp;
		}else{
			# show Template
			$this->smarty->display('registration_admission/reg_duplicate_main.tpl');
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
