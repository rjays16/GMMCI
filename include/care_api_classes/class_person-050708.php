<?php
/**
* @package care_api
*/
/**
*/
require_once($root_path.'include/care_api_classes/class_core.php');
/**
*  Person methods. 
*
* Note this class should be instantiated only after a "$db" adodb  connector object  has been established by an adodb instance
* @author Elpidio Latorilla
* @version beta 2.0.1
* @copyright 2002,2003,2004,2005,2005 Elpidio Latorilla
* @package care_api
*/
class Person extends Core {
	/**#@+
	* @access private
	*/
	/**
	* Table name for person registration data.
	* @var string
	*/
    var $tb_person='care_person';
	/**
	* Table name for city town name.
	* @var string
	*/
	var $tb_citytown='care_address_citytown';
	/**
	* Table name for ethnic origin.
	* Add by Jean-Philippe LIOT 13/05/2004
	* @var string
	*/
	var $tb_ethnic_orig='care_type_ethnic_orig';
	/**
	* Table name for encounter data.
	* @var string
	*/
	var $tb_enc='care_encounter';
	/**
	* Table name for employee data.
	* @var string
	*/
	var $tb_employ='care_personell';
	/**
	* Table name for religion data.
	* @var string
	*  burn added: March 14, 2007
	*/
	var $tb_religion='seg_religion';
	/**
	* Table name for occupation data.
	* @var string
	*  burn added: March 14, 2007
	*/
	var $tb_occupation='seg_occupation';
	/**
	* Table name for country data.
	* @var string
	*  burn added: March 14, 2007
	*/
	var $tb_country='seg_country';
	/**
	* SQL query
	*/
	var $sql;
	/**#@-*/
	/**
	* PID number
	* @var int
	*/
	var $pid;
	/**
	* Sql query result buffer
	* @var adodb record object
	*/
	var $result;
	/**
	* Universal flag
	* @var boolean
	*/
	var $ok;
	/**
	* Internal data buffer
	* @var array
	*/
	var $data_array;
	/**
	* Universal buffer
	* @var mixed
	*/
	var $buffer;
	/**
	* Returned row buffer
	* @var array
	*/
	var $row;
	/**
	* Returned person data buffer
	* @var array
	*/
	var $person=array();
	/**
	* Preloaded data flag
	* @var boolean
	*/
	var $is_preloaded=false;
	/**
	* Valid number flag
	* @var boolean
	*/
	var $is_nr=false;
	
	var $ageMonth;
	var $ageDay;	

	/**
	* Field names of basic registration data to be returned.
	* @var array
	*/
	var $basic_list='pid,title,name_first,name_last,name_2,name_3,name_middle,name_maiden,name_others,date_birth,
				           sex,addr_str,addr_str_nr,addr_zip,addr_citytown_nr,street_name,brgy_nr,photo_filename';
							  # burn added: 'street_name' and 'brgy_nr' March 2, 2007
	/**
	* Field names of table care_person
	* @var array
	*/
	var  $elems_array=array(
				'pid',
				 'title',
				 'date_reg',
				 'name_last',
				 'name_first',
				 'date_birth',
				 'sex',
				 'name_2',
				 'name_3',
				 'name_middle',
				 'name_maiden',
				 'name_others',
				 'place_birth',   # burn added: March 2, 2007
				 'blood_group',
				 'addr_str',
				 'addr_str_nr',
				 'addr_zip',
				 'addr_citytown_nr',
				 'street_name',   # burn added: March 2, 2007
				 'brgy_nr',   # burn added: March 2, 2007
				 'citizenship',   # burn added: March 2, 2007
				 'occupation',   # burn added: March 2, 2007
				 'employer',		#added by VAN 05-01-08
				 'phone_1_code',
				 'phone_1_nr',
				 'phone_2_code',
				 'phone_2_nr',
				 'cellphone_1_nr',
				 'cellphone_2_nr',
				 'fax',
				 'email',
				 'civil_status',
				 'photo_filename',
				 'ethnic_orig',
				 'org_id',
				 'sss_nr',
				 'nat_id_nr',
				 'religion',
				 'mother_pid',
				 'mother_name',   # burn added: March 9, 2007
				 'father_pid',
				 'father_name',   # burn added: March 9, 2007
				 'spouse_name',   # burn added: March 9, 2007
				 'guardian_name',   # burn added: March 9, 2007
				 'contact_person',
				 'contact_pid',
				 'contact_relation',
				 'death_date',
				 'death_encounter_nr',
				 'death_cause',
				 'death_cause_code',
				 'status',
				 'history',
				 'modify_id',
				 'modify_time',
				 'create_id',
				 'create_time',
				 'fromtemp',
				 'senior_ID');
	/**
	* Constructor
	* @param int PID number
	*/
	function Person ($pid='') {
	    $this->pid=$pid;
		$this->ref_array=$this->elems_array;
		$this->coretable=$this->tb_person;
	}
	/**
	* Sets the PID number.
	* @access public
	* @param int PID number
	*/
	function setPID($pid) {
	    $this->pid=$pid;
		 #echo "this->pid = ".$this->pid;
	}
	/**
	* Resolves the PID number to used in the methods.
	* @access public
	* @param int PID number
	* @return boolean
	*/
	function internResolvePID($pid) {
	    if (empty($pid)) {
		    if(empty($this->pid)) {
			    return false;
			} else { return true; }
		} else {
		     $this->pid=$pid;
			return true;
		}
	}
	/**
	* Checks if PID number exists in the database.
	* @access public
	* @param int PID number
	* @return boolean
	*/
	function InitPIDExists($init_nr){
		global $db;
		// Patch for db where the pid does not start with the predefined init
		//$this->sql="SELECT pid FROM $this->tb_person WHERE pid=$init_nr";
		$this->sql="SELECT pid FROM $this->tb_person";
		if($this->result=$db->Execute($this->sql)){
			if($this->result->RecordCount()){
				return true;
			} else { return false; }
		} else { return false; }
	}
	/**
	* Gets all religions
	* Returns ADODB record object or boolean.
	* @access public
	* @return mixed
	* burn added: March 14, 2007
	*/
	function getReligion($cond='',$oitem='',$sort=''){
		global $db;

		if (!empty($cond))
			$where=" WHERE $cond ";
		$order=" ORDER BY religion_name ";
		if (!empty($oitem))
			$order=" ORDER BY ".$oitem." ".$sort;
		$this->sql="SELECT * FROM $this->tb_religion $where $order";
		if($this->res['gr']=$db->Execute($this->sql)){
			if($this->res['gr']->RecordCount()){
				return $this->res['gr'];
			}else{ return FALSE; }
		}else{ return FALSE; }
	}
	/**
	* Gets all religions
	* Returns ADODB record object or boolean.
	* @access public
	* @return mixed
	* burn added: March 14, 2007
	*/
	function getOccupation($cond='',$oitem='',$sort=''){
		global $db;

		if (!empty($cond))
			$where=" WHERE $cond ";
		$order=" ORDER BY occupation_name ";
		if (!empty($oitem))
			$order=" ORDER BY ".$oitem." ".$sort;
		$this->sql="SELECT * FROM $this->tb_occupation $where $order";
#echo "getOccupation : this->sql = '".$this->sql."' <br> \n";
		if($this->res['go']=$db->Execute($this->sql)){
			if($this->res['go']->RecordCount()){
				return $this->res['go'];
			}else{ return FALSE; }
		}else{ return FALSE; }
	}
	/**
	* Gets a new TEMPORARY patient number (pid).
	*
	* A reference number must be passed as parameter. The returned number will the highest number above the reference number PLUS 1.
	* @param int Reference PID number
	* @return integer
	*	burn added: March 5, 2007
	*/			
	function getNewPIDNr($ref_nr){
		global $db;
		$row=array();
		$this->sql="SELECT pid FROM $this->tb_person WHERE pid>=$ref_nr ORDER BY pid DESC";
		if($this->res['gnpn']=$db->SelectLimit($this->sql,1)){
			if($this->res['gnpn']->RecordCount()){
				$row=$this->res['gnpn']->FetchRow();
				return $row['pid']+1;
			}else{/*echo $this->sql.'no xount';*/return $ref_nr;}
		}else{/*echo $this->sql.'no sql';*/return $ref_nr;}
	}

	/**
	* Gets a new TEMPORARY patient number (pid).
	*
	* A reference number must be passed as parameter. The returned number will the highest number above the reference number PLUS 1.
	* @param int Reference PID number
	* @return integer
	*	burn added: July 25, 2007
	*/			
	function getNewTempPIDNr($ref_nr){
		global $db;
		$temp_ref_nr = "T%";   # NOTE : T??????? would be the format of temporary patient number
		$row=array();
		$this->sql="SELECT pid FROM $this->tb_person WHERE pid LIKE '$temp_ref_nr' ORDER BY pid DESC";
		if($this->res['gnpn']=$db->SelectLimit($this->sql,1)){
			if($this->res['gnpn']->RecordCount()){
				$row=$this->res['gnpn']->FetchRow();
				$ref_nr_new = intval(substr($row['pid'],1))+1;
				$ref_nr_new = substr_replace($ref_nr, $ref_nr_new, (-1)*strlen($ref_nr_new));
				return $ref_nr_new;
			}else{/*echo $this->sql.'no xount';*/return $ref_nr;}
		}else{/*echo $this->sql.'no sql';*/return $ref_nr;}
	}

	/**
	* Computes the current age given a birthdate
	* @param string, birthdate in mm/dd/yyyy format
	* @param boolean, formatted return value (default is 2 decimal places)
	* @param string, deathdate in mm/dd/yyyy format
	* @return age, two decimal places
	* burn added: March 26, 2007	
	*/
	function getAge($bdate,$formatted=true,$ddate=''){
#		echo "class_person.php getAge : bdate = '$bdate' <br> \n";
				#  mm/dd/yyyy
			list($bdateMonth,$bdateDay,$bdateYear) = explode("/",$bdate);

			if (!checkdate($bdateMonth, $bdateDay, $bdateYear)){
#				echo "invalid birthdate! <br> \n";
				return FALSE;
			}
			if (!empty($ddate)){
#				echo " ddate is true <br> \n";
					#  mm/dd/yyyy
				list($ddateMonth,$ddateDay,$ddateYear) = explode("/",$ddate);
#echo " ddateMonth = '".$ddateMonth."' <br>\n ddateDay = '".$ddateDay."' <br>\n ddateYear = '".$ddateYear."' <br>\n  ";
				if (!checkdate($ddateMonth, $ddateDay, $ddateYear)){
#					echo "invalid deathdate! <br> \n";
					return FALSE;
				}				
			}			

			$pastDate = mktime(0, 0, 0, $bdateMonth  , $bdateDay, 2000);
			if (!empty($ddate)){
					# compute birthdate to deathdate
				$presentDate = mktime(0, 0, 0, $ddateMonth  , $ddateDay, 2000);
				$age = $ddateYear - $bdateYear;
				$ageM = $ddateMonth - $bdateMonth;
				$ageD = $ddateDay - $bdateDay;					
			}else{
					# compute birthdate to present day
				$presentDate = mktime(0, 0, 0, date("m")  , date("d"), 2000);
				$age = date("Y") - $bdateYear;
				$ageM = date("m") - $bdateMonth;
				$ageD = date("d") - $bdateDay;
			}			
			$this->setAgeMonth($ageM);
			$this->setAgeDay($ageD);
			
			$ageYear = ($presentDate - $pastDate)/31536000;
			$msg = " dob = '".$bdate."' \n bdateMonth = '".$bdateMonth."' <br>\n".
			" bdateDay = '".$bdateDay."' \n bdateYear = '".$bdateYear."' \n pastDate = '".$pastDate."' <br>\n".
			" presentDate = '".$presentDate."' \n age = '".$age."' \n ageMonth = '".$ageM."' \n  ageDay= '".$ageD."'";
#echo "msg :  <br>\n $msg <br>\n ";
			$age = $age + $ageYear;
			if ($formatted)
				return number_format($age, 2);
			else
				return $age;
	}
	
	function getAgeDay(){
		return number_format($this->ageDay);
	}
	
	function getAgeMonth(){
		return number_format($this->ageMonth);
	}
	
	function setAgeDay($Day){
		$this->ageDay = $Day;
	}
	function setAgeMonth($Month){
		$this->ageMonth = $Month;
	}
	
	/**
	* Prepares the internal buffer array for insertion routine.
	* @access private
	*/
	function prepInsertArray(){
        global $HTTP_POST_VARS;
		  
		$x='';
		$v='';
		$this->data_array=NULL;
		if(!isset($HTTP_POST_VARS['create_time'])||empty($HTTP_POST_VARS['create_time'])) $HTTP_POST_VARS['create_time']=date('YmdHis');
		while(list($x,$v)=each($this->elems_array)) {
	    	if(isset($HTTP_POST_VARS[$v])&&!empty($HTTP_POST_VARS[$v])) $this->data_array[$v]=$HTTP_POST_VARS[$v];
	    }
    }	
	/**
	* Database transaction. Uses the adodb transaction method.
	* @access private
	*/
	function Transact($sql='') {

	    global $db;
	    //$db->debug=true;
        if(!empty($sql)) $this->sql=$sql;

        $db->BeginTrans();
        $this->ok=$db->Execute($this->sql);
        if($this->ok) {
            $db->CommitTrans();
			return true;
        } else {
	        $db->RollbackTrans();
			return false;
	    }
    }	
	/**
	* Inserts the data into the care_person table.
	* @access private
	* @param int PID number
	* @return boolean
	*/
    function insertDataFromArray(&$array) {
		$x='';
		$v='';
		$index='';
		$values='';
		if(!is_array($array)) return false;
		while(list($x,$v)=each($array)) {
		    $index.="$x,";
		    $values.="'$v',";
		}
		$index=substr_replace($index,'',(strlen($index))-1);
		$values=substr_replace($values,'',(strlen($values))-1);

		$this->sql="INSERT INTO $this->tb_person ($index) VALUES ($values)";
		#echo "sql = ".$this->sql;
		return $this->Transact();
	}
	/**
	* Inserts the data from the internal buffer array into the care_person table.
	*
	* The data must be packed in the buffer array with index keys as outlined in the <var>$elems_array</var> array.
	* @access public
	* @return boolean
	*/
	function insertDataFromInternalArray() {
	    //$this->data_array=NULL;
		$this->prepInsertArray();
		# Check if  "create_time" key has a value, if no, create a new value
		if(!isset($this->buffer_array['create_time'])||empty($this->buffer_array['create_time'])) $this->buffer_array['create_time']=date('YmdHis');
#		echo "insertDataFromInternalArray() this->data_array: <br>\n";
		#print_r($this->data_array);
#		echo "<br><br>\n";
		return $this->insertDataFromArray($this->data_array);
	}

/*    function updateDataFromArray(&$array,$item_nr='') {
	    
		$x='';
		$v='';
		$sql='';
		
		if(!is_array($array)) return false;
		if(empty($item_nr)||!is_numeric($item_nr)) return false;
		while(list($x,$v)=each($array)) {
			if(stristr($v,'concat')||stristr($v,'null')) $sql.="$x= $v,";
		    	else $sql.="$x='$v',";
		}
		$sql=substr_replace($sql,'',(strlen($sql))-1);
		
        $this->sql="UPDATE $this->tb_person SET $sql WHERE pid=$item_nr";
		
		return $this->Transact();
	}
*/

	/**
	* Gets all person registration information based on its PID number key.
	*
	* The returned adodb record object contains a row or array.
	* This array contains data with the following index keys:
	* - all index keys as outlined in the <var>$elems_array</var> array
	* - addr_citytown_name = name of the city or town
	*
	* @access public
	* @param int PID number
	* @return mixed adodb object or boolean
	*/
	function getAllInfoObject($pid='') {
	    global $db;
		 
		if(!$this->internResolvePID($pid)) return false;
			# burn added : July 26, 2007
		if (intval($pid))
			$pid_format = " (p.pid='$this->pid' OR p.pid=$this->pid) ";
		else
			$pid_format = " p.pid='$this->pid' ";

				# burn commented: March 14, 2007
	    $this->sql="SELECT p.*, addr.name AS addr_citytown_name,ethnic.name AS ethnic_orig_txt
					FROM $this->tb_person AS p
					LEFT JOIN  $this->tb_citytown AS addr ON p.addr_citytown_nr=addr.nr
					LEFT JOIN  $this->tb_ethnic_orig AS ethnic ON p.ethnic_orig=ethnic.nr
					WHERE $pid_format ";
					
				# burn added: March 14, 2007
/*	    $this->sql="SELECT p.*, addr.name AS addr_citytown_name,ethnic.name AS ethnic_orig_txt
		 			, c.country_name AS citizenship, r.religion_name AS religion, o.occupation_name AS occupation
					FROM $this->tb_person AS p
					LEFT JOIN  $this->tb_citytown AS addr ON p.addr_citytown_nr=addr.nr
					LEFT JOIN  $this->tb_ethnic_orig AS ethnic ON p.ethnic_orig=ethnic.nr
					LEFT JOIN  $this->tb_country AS c ON p.citizenship=c.country_code
					LEFT JOIN  $this->tb_religion AS r ON p.religion=r.religion_nr
					LEFT JOIN  $this->tb_occupation AS o ON p.occupation=o.occupation_nr
					WHERE p.pid='$this->pid' ";
*/
        #echo "getAllInfoObject :  this->sql = '".$this->sql."' <br> \n";
        if($this->result=$db->Execute($this->sql)) {
            if($this->result->RecordCount()) {
				 return $this->result;	 
			} else { return false; }
		} else { return false; }
	}
	/**
	* Same as getAllInfoObject() but returns a 2 dimensional array.
	*
	* The returned  data in the array have the following index keys:
	* - all index keys as outlined in the <var>$elems_array</var> array
	* - citytown = name of the city or town
	*
	* @access public
	* @param int PID number
	* @return mixed array or boolean
	*/
	function getAllInfoArray($pid='') {
		global $db;
		$x='';
		$v='';
		if(!$this->internResolvePID($pid)) return false;
		
			# burn added : July 25, 2007
		if (intval($pid))
			$pid_format = " (p.pid='$this->pid' OR p.pid=$this->pid) ";
		else
			$pid_format = " p.pid='$this->pid' ";

			# burn added: October 19, 2007
		$this->sql= "SELECT p.*, sb.brgy_name, sm.zipcode, sm.mun_name, sp.prov_name, sr.region_name,
							sc.country_name AS country_citizenship,
							so.occupation_name AS occupation_name,
							sreli.religion_name AS religion_name,
							IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),'') AS age,
							ethnic.name AS ethnic_orig_txt
						FROM  $this->tb_person AS p
								LEFT JOIN seg_country AS sc ON p.citizenship= sc.country_code
								LEFT JOIN seg_occupation AS so ON p.occupation= so.occupation_nr
								LEFT JOIN seg_religion AS sreli ON p.religion= sreli.religion_nr
								LEFT JOIN $this->tb_ethnic_orig AS ethnic ON p.ethnic_orig=ethnic.nr
								LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=p.brgy_nr 
									LEFT JOIN seg_municity AS sm ON sm.mun_nr=sb.mun_nr 
										LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr 
											LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr 
						WHERE $pid_format ";

/*
			# burn commented: October 19, 2007
			# burn added: July 5, 2007
		$this->sql= "SELECT p.*, sb.brgy_name, sm.zipcode, sm.mun_name, sp.prov_name, sr.region_name,
							sc.country_name AS country_citizenship,
							so.occupation_name AS occupation_name,
							sreli.religion_name AS religion_name,
							IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),'') AS age,
							ethnic.name AS ethnic_orig_txt
						FROM seg_barangays AS sb, seg_municity AS sm, 
							seg_provinces AS sp, seg_regions AS sr,
							$this->tb_person AS p
								LEFT JOIN seg_country AS sc ON p.citizenship= sc.country_code
								LEFT JOIN seg_occupation AS so ON p.occupation= so.occupation_nr
								LEFT JOIN seg_religion AS sreli ON p.religion= sreli.religion_nr
								LEFT JOIN $this->tb_ethnic_orig AS ethnic ON p.ethnic_orig=ethnic.nr
						WHERE $pid_format
							AND sr.region_nr=sp.region_nr AND sp.prov_nr=sm.prov_nr 
							AND sm.mun_nr=sb.mun_nr AND sb.brgy_nr=p.brgy_nr ";
*/
				# burn commented : July 5, 2007
/*	    $this->sql="SELECT p.* , addr.name AS citytown 
					FROM $this->tb_person AS p LEFT JOIN $this->tb_citytown AS addr ON p.addr_citytown_nr=addr.nr
					WHERE p.pid=$this->pid";
*/
		if($this->result=$db->Execute($this->sql)) {
			if($this->result->RecordCount()) {
				return $this->row=$this->result->FetchRow();
			} else { return false; }
		} else { return false; }
	}
	/**
	* Gets a particular registration item based on its PID number.
	*
	* Use this preferably after the person registration data was successfully preloaded in the internal buffer with the <var>preloadPersonInfo()</var> method.
	* For details on field names of items that can be fetched, see the <var>$elems_array</var> array.
	* @access private
	* @param string Field name of the item to be fetched
	* @param int PID number
	* @return mixed string, integer, or boolean
	*/
	function getValue($item,$pid='') {
	    global $db;

	    if($this->is_preloaded) {
		    if(isset($this->person[$item])) return $this->person[$item];
		        else  return false;
		} else {
		    if(!$this->internResolvePID($pid)) return false;
				# burn added : July 26, 2007
			if (intval($pid))
				$pid_format = " (pid='$this->pid' OR pid=$this->pid) ";
			else
				$pid_format = " pid='$this->pid' ";

		    $this->sql="SELECT $item FROM $this->tb_person WHERE $pid_format";
		    //return $this->sql;
           		 if($this->result=$db->Execute($this->sql)) {
                		if($this->result->RecordCount()) {
				     $this->person=$this->result->FetchRow();
				     return $this->person[$item];
			    } else { return false; }
		    } else { return false; }
		}
	}
	/**
	* Gets registration items based on an item list and PID number.
	*
	* For details on field names of items that can be fetched, see the <var>$elems_array</var> array.
	* Several items can be fetched at once but their field names must be separated by comma.
	* @access public
	* @param string Field names of items to be fetched separated by comma.
	* @param int PID number
	* @return mixed
	*/
	function getValueByList($list,$pid='') {
	    global $db;
	
		$list="cp.pid,cp.title, cp.blood_group, cp.senior_ID,
					cp.name_first,cp.name_last,cp.name_2,cp.name_3,cp.name_middle,cp.name_maiden,cp.name_others,
					cp.date_birth,cp.sex,cp.addr_str,cp.addr_str_nr,cp.addr_zip,cp.addr_citytown_nr,
					cp.street_name,cp.brgy_nr,cp.photo_filename
					, sb.brgy_name, sm.zipcode, sm.mun_name, sp.prov_name, sr.region_name ";   # burn added: March 8, 2007
		#$from = " AS cp, seg_barangays AS sb, seg_municity AS sm, ".
		#		  " seg_provinces AS sp, seg_regions AS sr ";   # burn added: March 8, 2007
		
		#edited by VAN 04-28-08
		$from = " AS cp 
		          LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=cp.brgy_nr
					 LEFT JOIN seg_municity AS sm ON sm.mun_nr=sb.mun_nr 
					 LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr 
					 LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr";   
		
		#commented by VAN 04-28-08
		#$where=" AND sr.region_nr=sp.region_nr AND sp.prov_nr=sm.prov_nr 
		#			AND sm.mun_nr=sb.mun_nr AND sb.brgy_nr=cp.brgy_nr ";   # burn added: March 8, 2007
		
		if(empty($list)) return false;
		if(!$this->internResolvePID($pid)) return false;

			# burn added : July 26, 2007
		if (intval($pid))
			$pid_format = " (cp.pid='$this->pid' OR cp.pid=$this->pid) ";
		else
			$pid_format = " cp.pid='$this->pid' ";

#		$this->sql="SELECT $list FROM $this->tb_person WHERE pid=$this->pid";   # burn commented: March 12, 2007
		#$this->sql="SELECT $list FROM $this->tb_person $from WHERE $pid_format $where";   # burn added: March 8, 2007
		
		#edited by VAN 04-28-08
		$this->sql="SELECT $list FROM $this->tb_person $from WHERE $pid_format";   # burn added: March 8, 2007
		
#echo"getValueByList : this->sql = '".$this->sql."' <br> \n";
        if($this->result=$db->Execute($this->sql)) {
            if($this->result->RecordCount()) {
				$this->person=$this->result->FetchRow();	 
				return $this->person;
			} else { return false; }
		} else { return false; }
	}
	/**
	* Preloads the person registration data in the internal buffer <var>$person</var>.
	*
	* The preload success status is stored in the <var>$is_preloaded</var> variable.
	* The buffered adodb record object contains a row or array.
	* This array contains data with index keys as outlined in the <var>$elems_array</var> array
	*
	* @access public
	* @param int PID number
	* @return boolean
	*/
	function preloadPersonInfo($pid) {
	    global $db;
	    
		if(!$this->internResolvePID($pid)) return false;

			# burn added : July 26, 2007
		if (intval($pid))
			$pid_format = " (pid='$this->pid' OR pid=$this->pid) ";
		else
			$pid_format = " pid='$this->pid' ";

		$this->sql="SELECT * FROM $this->tb_person WHERE $pid_format";
        if($this->result=$db->Execute($this->sql)) {
            if($this->result->RecordCount()) {
				 $this->person=$this->result->FetchRow();	
				 $this->is_preloaded=true; 
				 return true;
			} else { return false; }
		} else { return false; }
	}
	/**#@+
	*
	* Use this preferably after the person registration data was successfully preloaded in the internal buffer with the <var>preloadPersonInfo()</var> method.
	* @access public
	* @return string
	*/
	/**
	* Returns person's first name.
	*/
	function FirstName() {
        return $this->getValue('name_first');
	}
	/**
	* Returns person's last or family name.
	*/
	function LastName() {
        return  $this->getValue('name_last');
	}
	/**
	* Returns person's second name.
	*/
	function SecondName() {
        return  $this->getValue('name_2');
	}
	/**
	* Returns person's third name.
	*/
	function ThirdName() {
        return  $this->getValue('name_3');
	}
	/**
	* Returns person's middle name.
	*/
	function MiddleName() {
        return  $this->getValue('name_middle');
	}
	/**
	* Returns person's maiden (unmarried) name.
	*/
	function MaidenName() {
        return  $this->getValue('name_maiden');
	}
	/**
	* Returns person's other name(s).
	*/
	function OtherName() {
        return  $this->getValue('name_others');
	}
	/**
	* Returns person's date of birth.
	*/
	function BirthDate() {
        return  $this->getValue('date_birth');
	}
	/**
	* Returns street number. Not stricly numeric. Could be alphanumeric.
	*/
	function StreetNr() {
        return  $this->getValue('addr_str_nr');
	}
	/**
	* Returns street name.
	*/
	function StreetName() {
        return  $this->getValue('addr_str');
	}
	/**
	* Returns ZIP code.
	*/
	function ZIPCode() {
        return  $this->getValue('addr_zip');
	}
	/**
	* Returns the valid address status. Returns 1 or 0.
	*/
	function isValidAddress() {
        return  $this->getValue('addr_is_valid');
	}
	/**
	* Returns the city or town code number. Reserved.
	*/
	function CityTownCode() {
        return  $this->getValue('addr_citytown_nr');
	}
	/**
	* Returns citizenship.
	*/
	function Citizenship() {
        return  $this->getValue('citizenship');
	}
	/**
	* Returns first phone area code.
	*/
	function FirstPhoneAreaCode() {
        return  $this->getValue('phone_1_code');
	}
	/**
	* Returns first phone number. Can be used as private phone number.
	*/
	function FirstPhoneNumber() {
        return  $this->getValue('phone_1_nr');
	}
	/**
	* Returns second phone area code.
	*/
	function SecondPhoneAreaCode() {
        return  $this->getValue('phone_2_code');
	}
	/**
	* Returns second phone number. Can be used as business phone number.
	*/
	function SecondPhoneNumber() {
        return  $this->getValue('phone_2_nr');
	}
	/**
	* Returns first cellphone number. Can be used as private cellphone number.
	*/
	function FirstCellphoneNumber() {
        return  $this->getValue('cellphone_1_nr');
	}
	/**
	* Returns second cellphone number.Can be used as business cellphone number
	*/
	function SecondCellphoneNumber() {
        return  $this->getValue('cellphone_2_nr');
	}
	/**
	* Returns fax number.
	*/
	function FaxNumber() {
        return  $this->getValue('fax');
	}
	/**
	* Returns email address.
	*/
	function EmailAddress() {
        return  $this->getValue('email');
	}
	/**
	* Returns sex.
	*/
	function Sex() {
        return  $this->getValue('sex');
	}
	/**
	* Returns title.
	*/
	function Title() {
        return  $this->getValue('title');
	}
	/**
	* Returns filename of stored id photo.
	*/
	function PhotoFilename() {
        return  $this->getValue('photo_filename');
	}
	/**
	* Returns ethnic origin.
	*/
	function EthnicOrigin() {
        return  $this->getValue('ethnic_origin');
	}
	/**
	* Returns organization id.
	*/
	function OrgID() {
        return  $this->getValue('org_id');
	}
	/**
	* Returns social security (system) number.
	*/
	function SSSNumber() {
        return  $this->getValue('sss_nr');
	}
	/**
	* Returns national id number.
	*/
	function NationalIDNumber() {
        return  $this->getValue('nat_id_nr');
	}
	/**
	* Returns religion.
	*/
	function Religion() {
        return  $this->getValue('religion');
	}
	/**
	* Returns pid number of mother.
	*/
	function MotherPID() {
        return  $this->getValue('mother_pid');
	}
	/**
	* Returns pid number of father.
	*/
	function FatherPID() {
        return  $this->getValue('father_pid');
	}
	/**
	* Returns date of death. In case person is deceased.
	*/
	function DeathDate() {
        return  $this->getValue('death_date');
	}
	/**
	* Returns case of death. In case person is deceased.
	*/
	function DeathCause() {
        return  $this->getValue('death_cause');
	}
	/**
	 * returns a list of other hospital numbers
	 *
	 * Added by Kurt Brauchli
	 * @access public
	 * @return Associative array
	 */
	function OtherHospNrList(){
		global $db;
		if($this->pid){

				# burn added : July 26, 2007
			if (intval($this->pid))
				$pid_format = " (pid='$this->pid' OR pid=$this->pid) ";
			else
				$pid_format = " pid='$this->pid' ";

			$sql = "SELECT * FROM care_person_other_number WHERE $pid_format AND status NOT IN ($this->dead_stat)";
			$result = $db->Execute($sql);
			if( !$result )
				return false;

			unset($other_hosp_no);
			while( $row = $result->FetchRow() ){
				$other_hosp_no[$row['org']] = $row['other_nr'];
			}
			return $other_hosp_no;
		}else{
			return FALSE;
		}
	}
	/**
	 * Sets the number for other hospitals (orgs)
	 *
	 * Added by Kurt Brauchli. Enhanced by Elpidio Latorilla 2004-05-23
	 * @access public
	 * @param string The other hospital, org , or institution
	 * @param int The other number
	 * @param string User id
	 * @return Boolean
	 */
	function OtherHospNrSet($org='',$other_nr='',$user='system'){
		global $db;

		if(empty($org)) return FALSE;

			# burn added : July 26, 2007
		if (intval($this->pid))
			$pid_format = " (pid='$this->pid' OR pid=$this->pid) ";
		else
			$pid_format = " pid='$this->pid' ";

		if(empty($other_nr)){
			// if number field is empty, delete other number
			//$this->sql = "DELETE FROM care_person_other_number  WHERE org='$org' AND pid=".$this->pid;
			// We do not delete the record but instead set its status to "deleted"
			$this->sql = "UPDATE care_person_other_number
							SET status='deleted',
								history=".$this->ConcatHistory("Deleted ".date('Y-m-d H:i:s')." ".$user."\n").",
								modify_id='$user',
								modify_time='".date('YmdHis')."'
							WHERE org='$org' AND $pid_format";
		}else{
			$this->sql = "SELECT other_nr FROM care_person_other_number  WHERE org='$org' AND $pid_format";

			if($result = $db->Execute( $this->sql )){
				if( $row = $result->FetchRow() ){
					$this->sql = "UPDATE care_person_other_number ";

					# If old number equals new number, we just set the status to "normal"
					# else change the number but document the old number in history

					if($row['other_nr']==$other_nr){
						$this->sql.="SET status='normal',
									history=".$this->ConcatHistory("Reactivated ".date('Y-m-d H:i:s')." ".$user."\n").", ";
					}else{
						$this->sql.="SET other_nr='$other_nr',
									status='normal',
									history=".$this->ConcatHistory("Changed (".$row['other_nr'].") -> ($other_nr) ".date('Y-m-d H:i:s')." ".$user."\n").", ";
					}

					$this->sql.=" modify_id='$user', modify_time='".date('YmdHis')."' WHERE org='$org' AND $pid_format";

				}else{
					$this->sql = "INSERT INTO care_person_other_number (pid,other_nr,org,status,history,create_id,create_time) ".
								" VALUES( '".$this->pid."',
										'$other_nr',
										'$org',
										'normal',
										'Created ".date('Y-m-d H:i:s')." ".$user."\n',
										'$user',
										'".date('YmdHis')."'
										)";
				}
			}
		}
		//$db->Execute($sql);
		return $this->Transact($this->sql);
	}
	/**
	* Returns table record's technical status.
	*/
	function RecordStatus() {
        return  $this->getValue('status');
	}
	/**
	* Returns table record's history.
	*/
	function RecordHistory() {
        return  $this->getValue('history');
	}
	/**#@-*/
	/**
	* Returns encounter number in case person died during that encounter.
	* @access public
	* @return int
	*/
	function DeathEncounterNumber() {
        return  $this->getValue('death_encounter_nr');
	}
	/**
	* Returns city or town name based on its "nr" key.
	* @access public
	* @return mixed string or boolean
	*/
	function CityTownName($code_nr=''){
	    global $db;
		if(!$this->is_preloaded) $this->sql="SELECT name FROM $this->tb_citytown WHERE nr=$code_nr";
            else $this->sql="SELECT name FROM $this->tb_citytown WHERE nr=".$this->CityTownCode();
			
		//echo $this->sql;exit;
        if($this->result=$db->Execute($this->sql)) {
            if($this->result->RecordCount()) {
				 $this->row=$this->result->FetchRow();	 
				 return $this->row['name'];
			} else { return false; }
		} else { return false; }
    }
	/**
	* Returns person registration items as listed in the <var>$basic_list</var> array based on pid key.
	*
	* The data is returned as associative array.
	* @access public
	* @param int PID number
	* @return mixed string or boolean
	*/
	function BasicDataArray($pid){
        if(!$this->internResolvePID($pid)) return false;
		return $this->getValueByList($this->basic_list,$this->pid);
	}
	/**
	* Adds a "View" note in the history field of the person's registration record.
	*
	* @access public
	* @param string Name of viewing person
	* @param int PID number
	* @return mixed string or boolean
	*/
	function setHistorySeen($encoder='',$pid=''){
	    global $db, $dbtype;
	    //$db->debug=true;
		if(empty($encoder)) return false;
		if(!$this->internResolvePID($pid)) return false;
        /*
		if($dbtype=='mysql')
			$this->sql="UPDATE $this->tb_person SET history= CONCAT(history,'\nView ".date('Y-m-d H:i:s')." = $encoder') WHERE pid=$this->pid";
		else
			$this->sql="UPDATE $this->tb_person SET history= history || '\nView ".date('Y-m-d H:i:s')." = $encoder' WHERE pid=$this->pid";
		*/
			# burn added : July 26, 2007
		if (intval($pid))
			$pid_format = " (pid='$this->pid' OR pid=$this->pid) ";
		else
			$pid_format = " pid='$this->pid' ";

		$this->sql="UPDATE $this->tb_person SET history=".$this->ConcatHistory("\nView ".date('Y-m-d H:i:s')." = $encoder")." WHERE $pid_format";

		if($this->Transact($this->sql)) {return true;}
		   else  {return false;}
			
	}
	/**
	* Checks if a person is currently admitted (either inpatient & outpatient).
	*
	* If person is currently admitted, his current encounter number is returned, else FALSE.
	* @access public
	* @param int PID number
	* @return mixed integer or boolean
	*/
	function CurrentEncounter($pid){
	    global $db;
		if(!$pid) return false;

			# burn added : July 26, 2007
		if (intval($pid))
			$pid_format = " (pid='$pid' OR pid=$pid) ";
		else
			$pid_format = " pid='$pid' ";

		$this->sql="SELECT encounter_nr FROM $this->tb_enc WHERE $pid_format AND is_discharged=0 AND encounter_status <> 'cancelled' AND status NOT IN ($this->dead_stat)";
		if($buf=$db->Execute($this->sql)){
		    if($buf->RecordCount()) {
				$buf2=$buf->FetchRow();
				//echo $this->sql;
				return $buf2['encounter_nr'];
			}else{return false;}
		}else{return false;}
	}
	/**
	* Gets all encounters of a person based on its pid key.
	*
	* The returned adodb record object contains rows of arrays.
	* Each array contains the encounter data with the following index keys:
	* - encounter_nr = the encounter number
	* - encounter_class_nr = encountr class number, contains 1 (inpatient) or 2 (outpatient), etc.
	* - is_discharged = discharge flag, contains 0 (not discharged) or  1 (discharged)
	* - discharge_date = date of discharge (end of encounter)
	*
	* @access public
	* @param int PID number
	* @return mixed integer or boolean
	*/
	function EncounterList($pid){
	    global $db;
		if(!$pid) return false;

			# burn added : July 26, 2007
		if (intval($pid))
			$pid_format = " (pid='$pid' OR pid=$pid) ";
		else
			$pid_format = " pid='$pid' ";

		$this->sql="SELECT encounter_nr,encounter_date,encounter_class_nr,encounter_type,is_discharged,discharge_date FROM $this->tb_enc WHERE $pid_format AND status NOT IN ($this->dead_stat)";
		if($this->res['_enl']=$db->Execute($this->sql)){
		    if($this->rec_count=$this->res['_enl']->RecordCount()) {
				return $this->res['_enl'];
			}else{return false;}
		}else{return false;}
	}
	/**
	* Searches and returns a list of persons based on search key.
	*
	* The returned adodb record object contains rows of arrays.
	* Each array contains the encounter data with the following index keys:
	* - pid = the PID number
	* - name_last = person's last or family name
	* - name_first = person's first or given name
	* - date_birth = date of birth
	* - sex = sex
	*
	* @access public
	* @param string Search keyword
	* @param string Sort by the item name, default = name_last (last/family name)
	*�@param string Sort direction, default = ASC (ascending)
	* @return mixed integer or boolean
	*/
	function Persons($searchkey='',$order_item='name_last',$order_dir='ASC'){
	    global $db, $sql_LIKE;
		$searchkey=trim($searchkey);
		$searchkey=strtr($searchkey,'*?','%_');
		if(is_numeric($searchkey)) {
			$searchkey=(int) $searchkey;
			$this->is_nr=true;
			$order_item='pid';
			if(empty($order_dir)) $order_dir='DESC';
		} else {
			if(empty($order_item)) $order_item='name_last';
			if(empty($order_dir)) $order_dir='ASC';
			$this->is_nr=false;
		}
		
		return $this->SearchSelect($searchkey,'','',$order_item,$order_dir);
/*
		$this->sql="SELECT pid, name_last, name_first, date_birth, sex FROM $this->tb_person WHERE status NOT IN ($this->dead_stat) ";
		if(!empty($searchkey)){
			$this->sql.=" AND (name_last $sql_LIKE '$searchkey%'
			                		OR name_first $sql_LIKE '$searchkey%'
			                		OR pid $sql_LIKE '$searchkey' )";
		}
		$this->sql.="  ORDER BY $order_item $oder_dir";
		if($this->res['pers']=$db->Execute($this->sql)){
		    if($this->rec_count=$this->res['pers']->RecordCount()) {
				return $this->res['pers'];
			}else{return false;}
		}else{return false;}
*/
	}

	/**
	* Searches and returns a block list of persons based on search key. 
	*
	* The following can be set:
	* - maximum number of rows in the returned list
	* - beginning row offset
	* - Field name for sorting
	* - Sort direction
	* - A boolean flag to include the first name in searching
	*
	* The returned adodb record object contains rows of arrays.
	* Each array contains the encounter data with the following index keys:
	* - pid = the PID number
	* - name_last = person's last or family name
	* - name_first = person's first or given name
	* - date_birth = date of birth in YYYY-mm-dd format
	* - sex = sex
	* - death_date = The date the person died (if applicable)
	* - addr_zip = Address zip code
	* - status = Record status
	*
	* @access public
	* @param string Search keyword
	* @param string Sort by the item name, default = name_last (last/family name)
	*�@param string Sort direction, default = ASC (ascending)
	* @return mixed integer or boolean
	* @burn's NOTE: $searchkey is assumed to be in this format ==> lastname, firstname
	*/
	function SearchSelectDuplicatePerson($searchkey='',$maxcount=100,$offset=0,$oitem='name_last',$odir='ASC',$fname=FALSE){
		global $db, $sql_LIKE, $root_path;
		//$db->debug=true;
		if(empty($maxcount)) $maxcount=100;
		if(empty($offset)) $offset=0;

		include_once($root_path.'include/inc_date_format_functions.php');

		# convert * and ? to % and &
		$searchkey=strtr($searchkey,'*?','%_');
		$searchkey=trim($searchkey);
		$suchwort=$searchkey;

		if(is_numeric($suchwort)) {
			$suchwort=(int) $suchwort;
			//$numeric=1;
			$this->is_nr=TRUE;

			//if($suchwort<$GLOBAL_CONFIG['person_id_nr_adder']){
			//	   $suchbuffer=(int) ($suchwort + $GLOBAL_CONFIG['person_id_nr_adder']) ;
			//}

			if(empty($oitem)) $oitem='pid';
			if(empty($odir)) $odir='DESC'; # default, latest pid at top

			$sql2="	WHERE pid=$suchwort ";

		} else {
			# Try to detect if searchkey is composite of first name + last name
			if(stristr($searchkey,',')){
				$lastnamefirst=TRUE;
			}else{
				$lastnamefirst=FALSE;
			}

#			$searchkey=strtr($searchkey,',',' ');
#			$cbuffer=explode(' ',$searchkey);
			$cbuffer=explode(',',$searchkey);

			# Remove empty variables
			for($x=0;$x<sizeof($cbuffer);$x++){
				$cbuffer[$x]=trim($cbuffer[$x]);
				if($cbuffer[$x]!='') $comp[]=$cbuffer[$x];
			}

			# Arrange the values, ln= lastname, fn=first name, bd = birthday
			if($lastnamefirst){
				$fn=$comp[1];
				$ln=$comp[0];
				$bd=$comp[2];
			}else{
				$fn=$comp[0];
				$ln=$comp[1];
				$bd=$comp[2];
			}
			# Check the size of the comp
			if(sizeof($comp)>1){
				$sql2=" WHERE (name_last $sql_LIKE '".strtr($ln,'+',' ')."%' AND name_first $sql_LIKE '".strtr($fn,'+',' ')."%')";
				if(!empty($bd)){
					$DOB=@formatDate2STD($bd,$date_format);
					if($DOB=='') {
						$sql2.=" AND date_birth $sql_LIKE '$bd%' ";
					}else{
						$sql2.=" AND date_birth = '$DOB' ";
					}
				}
			}else{
				# Check if * or %
				if($suchwort=='%'||$suchwort=='%%'){
					$sql2=" WHERE status NOT IN ($this->dead_stat)";
				}else{
					# Check if it is a complete DOB
					$DOB=@formatDate2STD($suchwort,$date_format);
					if($DOB=='') {
						if(defined('SHOW_FIRSTNAME_CONTROLLER')&&SHOW_FIRSTNAME_CONTROLLER){
							if($fname){
								$sql2=" WHERE (name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%' OR name_first $sql_LIKE '".strtr($suchwort,'+',' ')."%')";
							}else{
								$sql2=" WHERE name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%' ";
							}
						}else{
							$sql2=" WHERE name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%' ";
						}
					}else{
						$sql2=" WHERE date_birth = '$DOB'";
					}

					$sql2.=" AND status NOT IN ($this->dead_stat) ";
				}
			}
		 }

		$sql2.=" AND sr.region_nr=sp.region_nr AND sp.prov_nr=sm.prov_nr AND sm.mun_nr=sb.mun_nr AND sb.brgy_nr=cp.brgy_nr ";   # burn added: March 8, 2007
		$sql2 = " AS cp, seg_barangays AS sb, seg_municity AS sm, ".
				  " seg_provinces AS sp, seg_regions AS sr ".$sql2;   # burn added: March 8, 2007
		$this->buffer=$this->tb_person.$sql2;

		# Save the query in buffer for pagination
		//$this->buffer=$fromwhere;
		//$sql2.=' AND status NOT IN ("void","hidden","deleted","inactive")  ORDER BY '.$oitem.' '.$odir;
		# Set the sorting directive
		if(isset($oitem)&&!empty($oitem)) $sql3 =" ORDER BY $oitem $odir";

#		$this->sql='SELECT pid, name_last, name_first, date_birth, addr_zip, sex, death_date, status FROM '.$this->buffer.$sql3;   # burn commented: March 8, 2007
		$this->sql= " SELECT pid, name_last, name_first, date_birth, addr_zip, sex, death_date, status ".
						" , sb.brgy_name, sm.zipcode, sm.mun_name, sp.prov_name, sr.region_name ".
						" FROM ".$this->buffer.$sql3;
#echo "SearchSelect : this->sql = '".$this->sql."' <br> \n";

		if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset)){
			if($this->rec_count=$this->res['ssl']->RecordCount()) {
				return $this->res['ssl'];
			}else{return false;}
		}else{return false;}
	}# end of function SearchSelectDuplicatePerson


	/**
	* Searches and returns a block list of persons based on search key. 
	*
	* The following can be set:
	* - maximum number of rows in the returned list
	* - beginning row offset
	* - Field name for sorting
	* - Sort direction
	* - A boolean flag to include the first name in searching
	*
	* The returned adodb record object contains rows of arrays.
	* Each array contains the encounter data with the following index keys:
	* - pid = the PID number
	* - name_last = person's last or family name
	* - name_first = person's first or given name
	* - date_birth = date of birth in YYYY-mm-dd format
	* - sex = sex
	* - death_date = The date the person died (if applicable)
	* - addr_zip = Address zip code
	* - status = Record status
	*
	* @access public
	* @param string Search keyword
	* @param string Sort by the item name, default = name_last (last/family name)
	*�@param string Sort direction, default = ASC (ascending)
	* @return mixed integer or boolean
	*/

	function SearchSelect($searchkey='',$maxcount=100,$offset=0,$oitem='name_last',$odir='ASC',$fname=FALSE){
		global $db, $sql_LIKE, $root_path;
		//$db->debug=true;
		#$_SESSION['DEBUG'] = "fname=".print_r($fname,TRUE);
		if(empty($maxcount)) $maxcount=100;
		if(empty($offset)) $offset=0;

		include_once($root_path.'include/inc_date_format_functions.php');
		$date_format=getDateFormat();   # burn added, October 11, 2007

		$searchkey = $db->qstr($searchkey);
		$searchkey = substr($searchkey, 1, strlen($searchkey)-2);

		# convert * and ? to % and &
		$searchkey=strtr($searchkey,'*?','%_');
		$searchkey=trim($searchkey);
		$suchwort=$searchkey;
		
		#echo "key = ".$suchwort;
		
		#added by VAN 02-15-08
		if ($suchwort{0}=='T'){
			$suchwort = str_replace('T','',$suchwort);
			$isPid = 1;
		}	
				
		if(is_numeric($suchwort)) {
			$suchwort=(int) $suchwort;
			//$numeric=1;
			$this->is_nr=TRUE;

			//if($suchwort<$GLOBAL_CONFIG['person_id_nr_adder']){
			//	   $suchbuffer=(int) ($suchwort + $GLOBAL_CONFIG['person_id_nr_adder']) ;
			//}

			if(empty($oitem)) $oitem='cp.pid';
			if(empty($odir)) $odir='DESC'; # default, latest pid at top
			
			if($isPid){
				$sql2="	WHERE cp.pid='$searchkey'";
			}else{
				$sql2="	WHERE cp.pid=$suchwort ";
			}
			

		} else {
			# Try to detect if searchkey is composite of first name + last name
			if(stristr($searchkey,',')){
				$lastnamefirst=TRUE;
			}else{
				$lastnamefirst=FALSE;
			}

			$searchkey=strtr($searchkey,',',' ');
			$cbuffer=explode(' ',$searchkey);

			# Remove empty variables
			for($x=0;$x<sizeof($cbuffer);$x++){
				$cbuffer[$x]=trim($cbuffer[$x]);
				if($cbuffer[$x]!='') $comp[]=$cbuffer[$x];
			}

			# Arrange the values, ln= lastname, fn=first name, bd = birthday
			if($lastnamefirst){
				$fn=$comp[1];
				$ln=$comp[0];
				$bd=$comp[2];
			}else{
				$fn=$comp[0];
				$ln=$comp[1];
				$bd=$comp[2];
			}
			# Check the size of the comp
			if(sizeof($comp)>1){
				#edit by VAN 02-15-08
				#$sql2=" WHERE (name_last $sql_LIKE '".strtr($ln,'+',' ')."%' AND name_first $sql_LIKE '".strtr($fn,'+',' ')."%')";
				$cntlast = sizeof($cbuffer)-1;
				if (sizeof($cbuffer) > 2){
					$sql2=" WHERE (((name_last $sql_LIKE '%".strtr($ln,'+',' ')."%' OR name_last $sql_LIKE '".strtr($comp[$cntlast],'+',' ')."%') AND name_first $sql_LIKE '%".strtr($fn,'+',' ')."%') OR (name_last $sql_LIKE '".$searchkey."%' OR name_first $sql_LIKE '".$searchkey."%'))";
					$bd=$comp[sizeof($cbuffer)];
				}else
					$sql2=" WHERE ((name_last $sql_LIKE '%".strtr($ln,'+',' ')."%' AND name_first $sql_LIKE '%".strtr($fn,'+',' ')."%') OR (name_last $sql_LIKE '%".$searchkey."%' OR name_first $sql_LIKE '%".$searchkey."%'))";
						
				if(!empty($bd)){
					$DOB=@formatDate2STD($bd,$date_format);
					if($DOB=='') {
						$sql2.=" AND date_birth $sql_LIKE '$bd%' ";
					}else{
						$sql2.=" AND date_birth = '$DOB' ";
					}
				}
			}else{
				# Check if * or %
				if($suchwort=='%'||$suchwort=='%%'){
					#edited by VAN 03-04-08
					#$sql2=" WHERE cp.status NOT IN ($this->dead_stat)";
					$sql2=" WHERE cp.status NOT IN ($this->dead_stat) AND (death_date in (null,'0000-00-00',''))";
				}else{
					# Check if it is a complete DOB
					$DOB=@formatDate2STD($suchwort,$date_format);
					if($DOB=='') {
#						if(defined('SHOW_FIRSTNAME_CONTROLLER')&&SHOW_FIRSTNAME_CONTROLLER){
						if(TRUE){
							if($fname){
								$sql2=" WHERE (name_last $sql_LIKE '%".strtr($suchwort,'+',' ')."%' OR name_first $sql_LIKE '%".strtr($suchwort,'+',' ')."%')";
							}else{
								$sql2=" WHERE name_last $sql_LIKE '%".strtr($suchwort,'+',' ')."%' ";
							}
						}else{
							$sql2=" WHERE name_last $sql_LIKE '%".strtr($suchwort,'+',' ')."%' ";
						}
					}else{
						$sql2=" WHERE date_birth = '$DOB'";
					}

					#edited by VAN 03-04-08
					#$sql2.=" AND cp.status NOT IN ($this->dead_stat) ";
					$sql2.=" AND cp.status NOT IN ($this->dead_stat) AND (death_date in (null,'0000-00-00','')) ";
				}
			}
		 }

#		$sql2.=" AND sr.region_nr=sp.region_nr AND sp.prov_nr=sm.prov_nr AND sm.mun_nr=sb.mun_nr AND sb.brgy_nr=cp.brgy_nr ";   # burn added: March 8, 2007
#		$sql2 = " AS cp, seg_barangays AS sb, seg_municity AS sm, ".
#				  " seg_provinces AS sp, seg_regions AS sr ".$sql2;   # burn added: March 8, 2007

		if (empty($suchwort)){
			$sql_pay = " sc.*, pr.service_code, ";
			#$sql_pay_join = " LEFT JOIN seg_pay AS sc ON (sc.or_name=CONCAT(cp.name_first,' ',cp.name_last) OR sc.pid=cp.pid)
			#						LEFT JOIN seg_pay_request AS pr ON sc.or_no=pr.or_no ";
			$sql_pay_join = " LEFT JOIN seg_pay AS sc ON sc.pid=cp.pid
									LEFT JOIN seg_pay_request AS pr ON sc.or_no=pr.or_no ";
			/*
			$sql2 .= " AND sc.cancel_date IS NULL
						  AND DATE(sc.or_date)=DATE(NOW())
						  AND pr.ref_source = 'OTHER'
						  AND sc.account_type='33' ";
			*/
			/*$sql2 .= " AND ((sc.cancel_date IS NULL 
			                 AND DATE(sc.or_date)=DATE(NOW()) 
                          AND pr.ref_source = 'OTHER' 
								  AND sc.account_type='33') 
								  OR cp.senior_ID IS NOT NULL) ";			  
			*/
			$sql2 .= " AND ((sc.cancel_date IS NULL 
			                 AND DATE(sc.or_date)=DATE(NOW()) 
                          AND pr.ref_source = 'OTHER' 
								  AND sc.account_type='33') 
								  OR cp.senior_ID!='') ";	
								  
								  		  					  
			
		}else{
			$sql_pay  = " ";
			$sql_pay_join = " ";
		}

		$sql2 = " AS cp\n".
					"LEFT JOIN seg_radio_id AS sri ON sri.pid=cp.pid\n".
					"LEFT JOIN seg_charity_grants AS scg ON scg.encounter_nr=(\n".
					"SELECT encounter_nr FROM care_encounter AS enc\n".
					"WHERE cp.pid=enc.pid AND enc.is_discharged=0 AND enc.encounter_status <> 'cancelled' AND enc.status NOT IN ('deleted','hidden','inactive','void')\n".
    			"ORDER BY enc.encounter_nr DESC LIMIT 1)\n".
					"LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=cp.brgy_nr\n".
					"LEFT JOIN seg_municity AS sm ON sm.mun_nr=sb.mun_nr\n".
					"LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr\n".
					"LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr\n".
					$sql_pay_join." ".$sql2.
					"\nGROUP BY cp.pid,scg.encounter_nr\n";

		$this->buffer=$this->tb_person.$sql2;

		# Save the query in buffer for pagination


		# Set the sorting directive
		if(isset($oitem)&&!empty($oitem)) $sql3 =" ORDER BY $oitem $odir";
		#edited by VAN 04-16-08
		
		#if(isset($oitem)&&!empty($oitem)) $sql3 =" ORDER BY cp.pid DESC,cp.name_last ASC, $oitem $odir";
		
			$this->sql= "SELECT SQL_CALC_FOUND_ROWS sri.rid, ".$sql_pay." cp.senior_ID, cp.pid,cp.name_last,cp.name_first,cp.date_birth,cp.addr_zip, cp.sex,cp.death_date,cp.status,cp.street_name,\n".
						"sb.brgy_name, sm.zipcode, sm.mun_name, sp.prov_name, sr.region_name,".
						"(SELECT encounter_type FROM $this->tb_enc AS enc WHERE cp.pid=enc.pid AND enc.is_discharged=0 AND enc.encounter_status <> 'cancelled' AND enc.status NOT IN ($this->dead_stat) ORDER BY enc.encounter_nr ASC LIMIT 1) AS encounter_type,\n".   # AJMQ: 08/16/07
						"	(SELECT encounter_nr 
								FROM care_encounter AS enc 
								WHERE cp.pid=enc.pid AND enc.is_discharged=0 AND enc.encounter_status <> 'cancelled' 
									AND enc.status NOT IN ('deleted','hidden','inactive','void') 
								ORDER BY enc.encounter_nr DESC LIMIT 1) AS encounter_nr,".
						"SUBSTRING(MAX(CONCAT(scg.grant_dte,scg.discountid)),20) AS discountid,\n".
				  	"SUBSTRING(MAX(CONCAT(scg.grant_dte,scg.discount)),20) AS discount\n".
						" FROM ".$this->buffer.$sql3;
						

#echo "class_person.php : SearchSelect here : this->sql = '".$this->sql."' <br> \n";
		
		if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset)){
			/*
			if($this->rec_count=$this->res['ssl']->RecordCount()) {
				return $this->res['ssl'];
			}else{return false;}
			*/
			$this->rec_count=$this->res['ssl']->RecordCount();
			return $this->res['ssl'];
		}else{return false;}
	}
	
	function SearchSelectWithCurrentEncounter($searchkey='',$maxcount=100,$offset=0,$oitem='name_last',$odir='ASC',$fname=FALSE){
		global $db, $sql_LIKE, $root_path;
		//$db->debug=true;
		if(empty($maxcount)) $maxcount=100;
		if(empty($offset)) $offset=0;
		#echo "searchkey = $searchkey";
		include_once($root_path.'include/inc_date_format_functions.php');

		$searchkey = $db->qstr($searchkey);
		$searchkey = substr($searchkey, 1, strlen($searchkey)-2);

		# convert * and ? to % and &
		$searchkey=strtr($searchkey,'*?','%_');
		$searchkey=trim($searchkey);
		$suchwort=$searchkey;

		if(is_numeric($suchwort)) {
			$suchwort=(int) $suchwort;
			//$numeric=1;
			$this->is_nr=TRUE;

			//if($suchwort<$GLOBAL_CONFIG['person_id_nr_adder']){
			//	   $suchbuffer=(int) ($suchwort + $GLOBAL_CONFIG['person_id_nr_adder']) ;
			//}

			if(empty($oitem)) $oitem='tbperson.pid';
			if(empty($odir)) $odir='DESC'; # default, latest pid at top

			$sql2="	WHERE tbperson.pid=$suchwort ";

		} else {
			# Try to detect if searchkey is composite of first name + last name
			if(stristr($searchkey,',')){
				$lastnamefirst=TRUE;
			}else{
				$lastnamefirst=FALSE;
			}

			$searchkey=strtr($searchkey,',',' ');
			$cbuffer=explode(' ',$searchkey);

			# Remove empty variables
			for($x=0;$x<sizeof($cbuffer);$x++){
				$cbuffer[$x]=trim($cbuffer[$x]);
				if($cbuffer[$x]!='') $comp[]=$cbuffer[$x];
			}

			# Arrange the values, ln= lastname, fn=first name, bd = birthday
			if($lastnamefirst){
				$fn=$comp[1];
				$ln=$comp[0];
				$bd=$comp[2];
			}else{
				$fn=$comp[0];
				$ln=$comp[1];
				$bd=$comp[2];
			}
			# Check the size of the comp
			if(sizeof($comp)>1){
				$sql2=" WHERE (tbperson.name_last $sql_LIKE '".strtr($ln,'+',' ')."%' AND tbperson.name_first $sql_LIKE '".strtr($fn,'+',' ')."%')";
				if(!empty($bd)){
					$DOB=@formatDate2STD($bd,$date_format);
					if($DOB=='') {
						$sql2.=" AND tbperson.date_birth $sql_LIKE '$bd%' ";
					}else{
						$sql2.=" AND tbperson.date_birth = '$DOB' ";
					}
				}
			}else{
				# Check if * or %
				if($suchwort=='%'||$suchwort=='%%'){
					$sql2=" WHERE tbperson.status NOT IN ($this->dead_stat)";
				}else{
					# Check if it is a complete DOB
					$DOB=@formatDate2STD($suchwort,$date_format);
					if($DOB=='') {
						#if(defined('SHOW_FIRSTNAME_CONTROLLER')&&SHOW_FIRSTNAME_CONTROLLER){
						if(TRUE){
							if($fname){
								$sql2=" WHERE (tbperson.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%' OR tbperson.name_first $sql_LIKE '".strtr($suchwort,'+',' ')."%')";
							}else{
								$sql2=" WHERE tbperson.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%' ";
							}
						}else{
							$sql2=" WHERE tbperson.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%' ";
						}
					}else{
						$sql2=" WHERE tbperson.date_birth = '$DOB'";
					}

					$sql2.=" AND tbperson.status NOT IN ($this->dead_stat) ";
				}
			}
		 }
		#$sql2	.=" AND tbenc.pid=tbperson.pid AND tbenc.is_discharged=0 AND tbenc.encounter_status <> 'cancelled' AND tbenc.status NOT IN ($this->dead_stat)";
		#edited by VAN 03-04-08
		$sql2	.=" AND tbenc.is_discharged=0 AND tbenc.encounter_status <> 'cancelled' AND tbenc.status NOT IN ($this->dead_stat) AND (death_date in (null,'0000-00-00','')) ";
		$this->buffer=$this->tb_person.$sql2;
		
		# Save the query in buffer for pagination
		//$this->buffer=$fromwhere;
		//$sql2.=' AND status NOT IN ("void","hidden","deleted","inactive")  ORDER BY '.$oitem.' '.$odir;
		# Set the sorting directive
		$sql3 = "\nGROUP BY tbperson.pid,scg.encounter_nr\n";
		if(isset($oitem)&&!empty($oitem)) $sql3 .= "ORDER BY $oitem $odir";
/*
		$this->sql='SELECT tbenc.encounter_nr, tbperson.pid, tbperson.name_last, tbperson.name_first, '.
			'tbperson.date_birth, tbperson.addr_zip, tbperson.sex, tbperson.death_date, '.
			'tbperson.status, tbenc.encounter_nr, tbenc.encounter_type FROM '.
			$this->tb_person.' AS tbperson,'.
			$this->tb_enc.' AS tbenc '.$sql2.
			$sql3;
*/		
				# burn modified : September 11, 2007, November 22, 2007
		$this->sql='SELECT SQL_CALC_FOUND_ROWS tbperson.pid, sri.rid, tbenc.encounter_nr, tbperson.pid, tbperson.name_last, tbperson.name_first, '.
			'tbperson.date_birth, tbperson.sex, tbperson.death_date, '.
			'tbperson.street_name, sb.brgy_name, sm.zipcode, sm.mun_name, sp.prov_name, sr.region_name, '.   # burn added : September 11, 2007
			"SUBSTRING(MAX(CONCAT(scg.grant_dte,scg.discountid)),20) AS discountid,\n".
		  "SUBSTRING(MAX(CONCAT(scg.grant_dte,scg.discount)),20) AS discount,\n".
			"tbperson.status, tbenc.encounter_nr, tbenc.encounter_type FROM\n".
			$this->tb_enc." AS tbenc\n".
			"LEFT JOIN ".$this->tb_person." AS tbperson ON tbenc.pid=tbperson.pid\n".
			"LEFT JOIN seg_radio_id AS sri ON sri.pid=tbperson.pid\n".
			"LEFT JOIN seg_charity_grants AS scg ON scg.encounter_nr=tbenc.encounter_nr\n".
			"LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=tbperson.brgy_nr\n".
			"LEFT JOIN seg_municity AS sm ON sm.mun_nr=sb.mun_nr\n".
			"LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr\n".
			"LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr\n".
			$sql2.' '.$sql3;
		
		//ob_end_clean();
#		print_r($this->sql);
		//exit();
		
#		echo "class_person.php : SearchSelectWithCurrentEncounter : this->sql = ".$this->sql;
		if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset)){
			$this->rec_count=$this->res['ssl']->RecordCount();
			/*
			if($this->rec_count=$this->res['ssl']->RecordCount()) {
				return $this->res['ssl'];
			}else{return false;}
			*/
			return $this->res['ssl'];
		}else{return false;}

	}
	
	

	/**
	* Searches and returns a block list of persons based on search key. 
	*
	* The following can be set:
	* - maximum number of rows in the returned list
	* - beginning row offset
	* - Field name for sorting
	* - Sort direction
	* - A boolean flag to include the first name in searching
	*
	* The returned adodb record object contains rows of arrays.
	* Each array contains the encounter data with the following index keys:
	* - pid = the PID number
	* - name_last = person's last or family name
	* - name_first = person's first or given name
	* - date_birth = date of birth in YYYY-mm-dd format
	* - sex = sex
	* - death_date = The date the person died (if applicable)
	* - addr_zip = Address zip code
	* - status = Record status
	*
	* @access public
	* @param string Search keyword
	* @param string Sort by the item name, default = name_last (last/family name)
	*�@param string Sort direction, default = ASC (ascending)
	* @return mixed integer or boolean
	*/
	function SearchSelect_ORIG($searchkey='',$maxcount=100,$offset=0,$oitem='name_last',$odir='ASC',$fname=FALSE){
		global $db, $sql_LIKE, $root_path;
		//$db->debug=true;
		if(empty($maxcount)) $maxcount=100;
		if(empty($offset)) $offset=0;

		include_once($root_path.'include/inc_date_format_functions.php');

		# convert * and ? to % and &
		$searchkey=strtr($searchkey,'*?','%_');
		$searchkey=trim($searchkey);
		$suchwort=$searchkey;

		if(is_numeric($suchwort)) {
			$suchwort=(int) $suchwort;
			//$numeric=1;
			$this->is_nr=TRUE;

			//if($suchwort<$GLOBAL_CONFIG['person_id_nr_adder']){
			//	   $suchbuffer=(int) ($suchwort + $GLOBAL_CONFIG['person_id_nr_adder']) ;
			//}

			if(empty($oitem)) $oitem='pid';
			if(empty($odir)) $odir='DESC'; # default, latest pid at top

			$sql2="	WHERE pid=$suchwort ";

		} else {
			# Try to detect if searchkey is composite of first name + last name
			if(stristr($searchkey,',')){
				$lastnamefirst=TRUE;
			}else{
				$lastnamefirst=FALSE;
			}

			$searchkey=strtr($searchkey,',',' ');
			$cbuffer=explode(' ',$searchkey);

			# Remove empty variables
			for($x=0;$x<sizeof($cbuffer);$x++){
				$cbuffer[$x]=trim($cbuffer[$x]);
				if($cbuffer[$x]!='') $comp[]=$cbuffer[$x];
			}

			# Arrange the values, ln= lastname, fn=first name, bd = birthday
			if($lastnamefirst){
				$fn=$comp[1];
				$ln=$comp[0];
				$bd=$comp[2];
			}else{
				$fn=$comp[0];
				$ln=$comp[1];
				$bd=$comp[2];
			}
			# Check the size of the comp
			if(sizeof($comp)>1){
				$sql2=" WHERE (name_last $sql_LIKE '".strtr($ln,'+',' ')."%' AND name_first $sql_LIKE '".strtr($fn,'+',' ')."%')";
				if(!empty($bd)){
					$DOB=@formatDate2STD($bd,$date_format);
					if($DOB=='') {
						$sql2.=" AND date_birth $sql_LIKE '$bd%' ";
					}else{
						$sql2.=" AND date_birth = '$DOB' ";
					}
				}
			}else{
				# Check if * or %
				if($suchwort=='%'||$suchwort=='%%'){
					$sql2=" WHERE status NOT IN ($this->dead_stat)";
				}else{
					# Check if it is a complete DOB
					$DOB=@formatDate2STD($suchwort,$date_format);
					if($DOB=='') {
						if(defined('SHOW_FIRSTNAME_CONTROLLER')&&SHOW_FIRSTNAME_CONTROLLER){
							if($fname){
								$sql2=" WHERE name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%' OR name_first $sql_LIKE '".strtr($suchwort,'+',' ')."%'";
							}else{
								$sql2=" WHERE name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%' ";
							}
						}else{
							$sql2=" WHERE name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%' ";
						}
					}else{
						$sql2=" WHERE date_birth = '$DOB'";
					}

					$sql2.=" AND status NOT IN ($this->dead_stat) ";
				}
			}
		 }


		$this->buffer=$this->tb_person.$sql2;
		# Save the query in buffer for pagination
		//$this->buffer=$fromwhere;
		//$sql2.=' AND status NOT IN ("void","hidden","deleted","inactive")  ORDER BY '.$oitem.' '.$odir;
		# Set the sorting directive
		if(isset($oitem)&&!empty($oitem)) $sql3 =" ORDER BY $oitem $odir";
		$this->sql='SELECT pid, name_last, name_first, date_birth, addr_zip, sex, death_date, status FROM '.$this->buffer.$sql3;

		if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset)){
			if($this->rec_count=$this->res['ssl']->RecordCount()) {
				return $this->res['ssl'];
			}else{return false;}
		}else{return false;}
	}

	
	function countSearchSelectWithCurrentEncounter($searchkey='',$maxcount=100,$offset=0,$oitem='name_last',$odir='ASC',$fname=FALSE){
		global $db, $sql_LIKE, $root_path;
		if(empty($maxcount)) $maxcount=100;
		if(empty($offset)) $offset=0;
		include_once($root_path.'include/inc_date_format_functions.php');

		# convert * and ? to % and &
		$searchkey=strtr($searchkey,'*?','%_');
		$searchkey=trim($searchkey);
		$suchwort=$searchkey;

		if(is_numeric($suchwort)) {
			$suchwort=(int) $suchwort;
			$this->is_nr=TRUE;
			if(empty($oitem)) $oitem='tbperson.pid';
			if(empty($odir)) $odir='DESC'; # default, latest pid at top

			$sql2="	WHERE tbperson.pid=$suchwort ";

		} else {
			# Try to detect if searchkey is composite of first name + last name
			if(stristr($searchkey,',')){
				$lastnamefirst=TRUE;
			}else{
				$lastnamefirst=FALSE;
			}

			$searchkey=strtr($searchkey,',',' ');
			$cbuffer=explode(' ',$searchkey);

			# Remove empty variables
			for($x=0;$x<sizeof($cbuffer);$x++){
				$cbuffer[$x]=trim($cbuffer[$x]);
				if($cbuffer[$x]!='') $comp[]=$cbuffer[$x];
			}

			# Arrange the values, ln= lastname, fn=first name, bd = birthday
			if($lastnamefirst){
				$fn=$comp[1];
				$ln=$comp[0];
				$bd=$comp[2];
			}else{
				$fn=$comp[0];
				$ln=$comp[1];
				$bd=$comp[2];
			}
			# Check the size of the comp
			if(sizeof($comp)>1){
				$sql2=" WHERE (tbperson.name_last $sql_LIKE '".strtr($ln,'+',' ')."%' AND tbperson.name_first $sql_LIKE '".strtr($fn,'+',' ')."%')";
				if(!empty($bd)){
					$DOB=@formatDate2STD($bd,$date_format);
					if($DOB=='') {
						$sql2.=" AND tbperson.date_birth $sql_LIKE '$bd%' ";
					}else{
						$sql2.=" AND tbperson.date_birth = '$DOB' ";
					}
				}
			}else{
				# Check if * or %
				if($suchwort=='%'||$suchwort=='%%'){
					$sql2=" WHERE tbperson.status NOT IN ($this->dead_stat)";
				}else{
					# Check if it is a complete DOB
					$DOB=@formatDate2STD($suchwort,$date_format);
					if($DOB=='') {
						if(defined('SHOW_FIRSTNAME_CONTROLLER')&&SHOW_FIRSTNAME_CONTROLLER){
							if($fname){
								$sql2=" WHERE (tbperson.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%' OR tbperson.name_first $sql_LIKE '".strtr($suchwort,'+',' ')."%')";
							}else{
								$sql2=" WHERE tbperson.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%' ";
							}
						}else{
							$sql2=" WHERE tbperson.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%' ";
						}
					}else{
						$sql2=" WHERE tbperson.date_birth = '$DOB'";
					}

					$sql2.=" AND tbperson.status NOT IN ($this->dead_stat) ";
				}
			}
		 }
		$sql2	.=" AND tbenc.pid=tbperson.pid AND tbenc.is_discharged=0 AND tbenc.encounter_status <> 'cancelled' AND tbenc.status NOT IN ($this->dead_stat)";
		
		$this->buffer=$this->tb_person.$sql2;
		$this->sql='SELECT COUNT(*) FROM '.
			$this->tb_person.' AS tbperson,'.
			$this->tb_enc.' AS tbenc '.$sql2.
			$sql3;

		return $db->GetOne($this->sql);
	}
	

#----------------added by VAN ----------------------------
function SearchSelectWithCurrentEncounter2($searchkey='',$maxcount=100,$offset=0,$oitem='name_last',$odir='ASC',$fname=FALSE){
		global $db, $sql_LIKE, $root_path;
		//$db->debug=true;
		if(empty($maxcount)) $maxcount=100;
		if(empty($offset)) $offset=0;
		
		include_once($root_path.'include/inc_date_format_functions.php');

		# convert * and ? to % and &
		$searchkey=strtr($searchkey,'*?','%_');
		$searchkey=trim($searchkey);
		$suchwort=$searchkey;
		#echo "<br>searchkey = $suchwort<br>";
		if(is_numeric($suchwort)) {
			$suchwort=(int) $suchwort;
			//$numeric=1;
			$this->is_nr=TRUE;

			//if($suchwort<$GLOBAL_CONFIG['person_id_nr_adder']){
			//	   $suchbuffer=(int) ($suchwort + $GLOBAL_CONFIG['person_id_nr_adder']) ;
			//}

			if(empty($oitem)) $oitem='tbperson.pid';
			if(empty($odir)) $odir='DESC'; # default, latest pid at top

			$sql2="	WHERE tbperson.pid=$suchwort ";

		} else {
			# Try to detect if searchkey is composite of first name + last name
			if(stristr($searchkey,',')){
				$lastnamefirst=TRUE;
			}else{
				$lastnamefirst=FALSE;
			}

			$searchkey=strtr($searchkey,',',' ');
			$cbuffer=explode(' ',$searchkey);

			# Remove empty variables
			for($x=0;$x<sizeof($cbuffer);$x++){
				$cbuffer[$x]=trim($cbuffer[$x]);
				if($cbuffer[$x]!='') $comp[]=$cbuffer[$x];
			}

			# Arrange the values, ln= lastname, fn=first name, bd = birthday
			if($lastnamefirst){
				$fn=$comp[1];
				$ln=$comp[0];
				$bd=$comp[2];
			}else{
				$fn=$comp[0];
				$ln=$comp[1];
				$bd=$comp[2];
			}
			# Check the size of the comp
			if(sizeof($comp)>1){
				$sql2=" WHERE (tbperson.name_last $sql_LIKE '".strtr($ln,'+',' ')."%' AND tbperson.name_first $sql_LIKE '".strtr($fn,'+',' ')."%')";
				if(!empty($bd)){
					$DOB=@formatDate2STD($bd,$date_format);
					if($DOB=='') {
						$sql2.=" AND tbperson.date_birth $sql_LIKE '$bd%' ";
					}else{
						$sql2.=" AND tbperson.date_birth = '$DOB' ";
					}
				}
			}else{
				# Check if * or %
				if($suchwort=='%'||$suchwort=='%%'){
					$sql2=" WHERE tbperson.status NOT IN ($this->dead_stat)";
				}else{
					# Check if it is a complete DOB
					$DOB=@formatDate2STD($suchwort,$date_format);
					if($DOB=='') {
						if(defined('SHOW_FIRSTNAME_CONTROLLER')&&SHOW_FIRSTNAME_CONTROLLER){
							if($fname){
								$sql2=" WHERE (tbperson.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%' OR tbperson.name_first $sql_LIKE '".strtr($suchwort,'+',' ')."%')";
							}else{
								$sql2=" WHERE tbperson.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%' ";
							}
						}else{
							$sql2=" WHERE tbperson.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%' ";
						}
					}else{
						$sql2=" WHERE tbperson.date_birth = '$DOB'";
					}

					$sql2.=" AND tbperson.status NOT IN ($this->dead_stat) ";
				}
			}
		 }
		
		if ((stristr($suchwort,"%") === FALSE) && (stristr($suchwort,"_") === FALSE)){
			$sql2	.=" AND tbenc.pid=tbperson.pid AND tbenc.is_discharged=0 AND tbenc.encounter_status <> 'cancelled' AND tbenc.status NOT IN ($this->dead_stat)";
		}else{
			$sql2	.=" AND tbenc.is_discharged=0 AND tbenc.encounter_status <> 'cancelled' AND tbenc.status NOT IN ($this->dead_stat)";
			$sql2 = "ON tbenc.pid=tbperson.pid ".$sql2;
		}
			
		$this->buffer=$this->tb_person.$sql2;
		
		# Save the query in buffer for pagination
		//$this->buffer=$fromwhere;
		//$sql2.=' AND status NOT IN ("void","hidden","deleted","inactive")  ORDER BY '.$oitem.' '.$odir;
		# Set the sorting directive
		if(isset($oitem)&&!empty($oitem)) 
			$sql3 =" ORDER BY $oitem $odir";
		else
			$sql3 = " ORDER by name_last, name_first ";	

	if ((stristr($suchwort,"%") === FALSE) && (stristr($suchwort,"_") === FALSE)){
		$this->sql='SELECT tbenc.encounter_nr, tbperson.pid, tbperson.name_last, tbperson.name_first, '.
			'tbperson.date_birth, tbperson.addr_zip, tbperson.sex, tbperson.death_date, '.
			'tbperson.status, tbenc.encounter_nr FROM '.
			$this->tb_person.' AS tbperson,'.
			$this->tb_enc.' AS tbenc '.$sql2.
			$sql3;
		
	}else{	
		#echo "sql2 = ".$sql3;
				
		$this->sql='SELECT tbperson.pid, tbenc.encounter_nr, tbperson.name_last, tbperson.name_first, '.
			'tbperson.date_birth, tbperson.addr_zip, tbperson.sex, tbperson.death_date, '.
			'tbperson.status, tbenc.encounter_nr FROM '.
			$this->tb_person.' AS tbperson LEFT JOIN '.
			$this->tb_enc.' AS tbenc '.$sql2.
			$sql3;
	}		
		//ob_end_clean();
		//print_r($this->sql);
		//exit();
		
		#echo "sql = ".$this->sql;
		if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset)){
			if($this->rec_count=$this->res['ssl']->RecordCount()) {
				return $this->res['ssl'];
			}else{return false;}
		}else{return false;}

	}

	function getValue2($item,$pid='') {
	    global $db;

	    if($this->is_preloaded) {
		    if(isset($this->person[$item])) return $this->person[$item];
		        else  return false;
		} else {
		    if(!$this->internResolvePID($pid)) return false;
			
			 $pid_format = " pid='$this->pid' ";

		    $this->sql="SELECT $item FROM $this->tb_person WHERE $pid_format";
		    //return $this->sql;
           		 if($this->result=$db->Execute($this->sql)) {
                		if($this->result->RecordCount()) {
				     $this->person=$this->result->FetchRow();
				     return $this->person[$item];
			    } else { return false; }
		    } else { return false; }
		}
	}

#-------------------------------------------------------
	
	/**
	* Checks if the person is currently employed in this hospital.
	*
	* If currently employed the employee number is returned, else FALSE.
	* @access public
	* @param int PID number
	* @return mixed integer or boolean
	*/
	function CurrentEmployment($pid){
	    global $db;
		if(!$pid) return false;

			# burn added : July 26, 2007
		if (intval($pid))
			$pid_format = " (pid='$pid' OR pid=$pid) ";
		else
			$pid_format = " pid='$pid' ";

		$this->sql="SELECT nr FROM $this->tb_employ
							WHERE $pid_format AND is_discharged IN ('',0) AND status NOT IN ($this->dead_stat)";
		if($buf=$db->Execute($this->sql)){
			if($buf->RecordCount()){
				$buf2=$buf->FetchRow();
				return $buf2['nr'];
			}else{return false;}
		}else{return false;}
	}
	/**
	* Sets death information.
	* 
	* The data must be passed by reference with associative array.
	* Data array must have the following index keys.
	* - 'death_date' = date of death
	* - 'death_encounter_nr' = encounter number in case person died during that encounter
	* - 'death_cause' = text of death cause
	* - 'death_cause_code' = code of death cause (if available)
	* - 'history' = text to be appended to "history" item
	* - 'modify_id' = name of user
	* - 'modify_time' = time of this modification in yyyymmddhhMMss format
	*
	* @access public
	* @param int PID number
	* @param array Death information.
	* @return mixed integer or boolean
	*/
	function setDeathInfo($pid,&$data){
		$this->setDataArray($data);
			# burn added : July 26, 2007
		if (intval($pid))
			$pid_format = " (pid='$pid' OR pid=$pid) ";
		else
			$pid_format = " pid='$pid' ";

		$this->setWhereCondition($pid_format);
		return $this->updateDataFromInternalArray($pid);
	}
	/**
	* Returns the PID ('nr' of a column) based on OID key
	*
	* Special for postgresql or dbms that returns an OID key after an insert
	*
	* @access public
	* @param int OID return insert key of a column
	* @return mixed integer or boolean
	*/
	function postgre_PIDbyOID($oid=0){
		if(!$oid) return false;
		else return $this->postgre_Insert_ID($this->tb_person,'pid',$oid);
	}
	
	/**
	* returns basic data of living person(s) based on family name, first name & b-day
	*
	* @access public
	* @param array The data keys
	* @param boolean Flags if non-living persons are also returned. Default = FALSE
	* @return mixed array or boolean
	*/
	function PIDbyData(&$data,$deadtoo=FALSE){
		global $db, $sql_LIKE, $dbf_nodate;
			
			# burn addded: March 28, 2007
		$cond='';
		if (!empty($data['date_birth'])){
#			$cond.=" AND date_birth='".$data['date_birth']."' ";
		}
#		if (!empty($data['sex'])){
#			$cond.=" AND sex $sql_LIKE '".$data['sex']."' ";
#		}		
		$this->sql="SELECT pid,name_last,name_first,date_birth,sex FROM $this->tb_person 
					WHERE name_last $sql_LIKE '%".$data['name_last']."%'
						AND name_first $sql_LIKE '%".$data['name_first']."%'
						$cond ";
#						AND date_birth='".$data['date_birth']."'
#						AND sex $sql_LIKE '".$data['sex']."'";
		if(!$deadtoo) $this->sql.=" AND death_date='$dbf_nodate'";
#echo "PIDbyData: this->sql = '".$this->sql."' <br> \n";
#exit();
		if($res['pbd']=$db->Execute($this->sql)){
		    if($res['pbd']->RecordCount()) { 
				return $res['pbd'];//
			}else{return false;}
		}else{return false;}
	}
	/**
	* Sets the  filename if the person in the databank
	*
	* @access public
	* @param int PID number
	* @param string Filename
	* @return mixed string or boolean
	*/
	function setPhotoFilename($pid='',$fn=''){
	    global $db, $HTTP_SESSION_VARS;
		if(empty($pid)||empty($fn)) return false;
		if(!$this->internResolvePID($pid)) return false;

			# burn added : July 26, 2007
		if (intval($pid))
			$pid_format = " (pid='$this->pid' OR pid=$this->pid) ";
		else
			$pid_format = " pid='$this->pid' ";

		$this->sql="UPDATE $this->tb_person SET photo_filename='$fn', 
		 			history=".$this->ConcatHistory("\nPhoto set ".date('Y-m-d H:i:s')." = ".$HTTP_SESSION_VARS['sess_user_name'])." WHERE $pid_format";
		return $this->Transact($this->sql);
	}
	
	#added by VAN 02-29-08
	function changeTemptoPermanentPID($oldpid,$newpid)
    {
	    global $db, $HTTP_SESSION_VARS;
	     
		if(empty($newpid)) return FALSE;
	    $this->sql="UPDATE $this->tb_person SET
				   pid = '".$newpid."',
				   history =".$this->ConcatHistory("Update : Change Temporary to Permanent ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n").",
				   modify_id = '".$HTTP_SESSION_VARS['sess_user_name']."',
					modify_time = '".date('Y-m-d H:i:s')."'
			WHERE pid = '".$oldpid."'";
		return $this->Transact();
    }	
	 
	 function getPersonInfo($key){
	 	global $db;
		
		if (is_numeric($key)){
			$this->sql="SELECT * from care_person
								WHERE pid LIKE '%".$key."'";
		}else{
			$this->sql="SELECT * from care_person
								WHERE name_last LIKE '%".$key."%' OR name_first LIKE '%".$key."%'";			
		}				
		if ($this->result=$db->Execute($this->sql)){
			if ($this->count=$this->result->RecordCount())
				return $this->result->FetchRow();
			else
				return FALSE;
		}else{
			return FALSE;
		}		
	 }
	 
	#----------added by VAN 05-06-08
	 function searchByName($fname, $lname){
	 	global $db;
		
		$this->sql="SELECT * FROM care_person
								WHERE name_last LIKE '".$lname."' AND name_first LIKE '".$fname."'";
		
		if ($this->result=$db->Execute($this->sql)){
			if ($this->count=$this->result->RecordCount())
				return $this->result->FetchRow();
			else
				return FALSE;
		}else{
			return FALSE;
		}		
	 }
	 #-------------------------------------
}
?>