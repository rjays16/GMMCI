<?php
/**
* @package care_api
*/

/**
*/
require_once($root_path.'include/care_api_classes/class_core.php');

/**
*  Immunization methods. 
*  Note this class should be instantiated only after a "$db" adodb  connector object  has been established by an adodb instance
* @author Elpidio Latorilla
* @version beta 2.0.1
* @copyright 2002,2003,2004,2005,2005 Elpidio Latorilla
* @package care_api
*/
class Immunization extends Core {
	/**
	* Database table for encounter's immunization data
	* @var string
	*/
	var $tb='care_encounter_immunization'; // table name
	/**
	* Database table for appication types
	* @var string
	*/
	var $tb_types='care_type_application';
	/**
	* Preloaded data flag
	* @var adodb record object
	*/
	var $result;
	/**
	* Preloaded department data
	* @var adodb record object
	*/
	var $preload_dept;
	/**
	* Preloaded data flag
	* @var boolean
	*/
	var $is_preloaded=false;
	/**
	* Number or departments
	* @var int
	*/
	var $dept_count;
	/**
	* immunization's id
	* @var string
	* burn added: August 23, 2006
	*/
	var $immunization_id;
	/**
	* Buffer for row returned by adodb's FetchRow() method
	* @var array
	* burn added: August 23, 2006
	*/
	var $row;
	/**
	* Fieldnames of care_encounter_immunization table. Primary key is "nr".
	* @var array
	*/
	var $tabfields=array('nr',
									'encounter_nr',
									'date',
									'type',
									'medicine',
									'dosage',
									'application_type_nr',
									'application_by',
									'titer',
									'refresh_date',
									'notes',
									'status',
									'history',
									'modify_id',
									'modify_time',
									'create_id',
									'create_time');
	/**
	* Constructor
	*/			
	function Immunization(){
		$this->setTable($this->tb);
		$this->setRefArray($this->tabfields);
	}
	/**
	* Constructor
	* 2nd constructor with parameter
	* @param string Immunization id
	* burn added: August 23, 2006
	*/			
	function Immunization($nr=''){
        $this->$immunization_id=$nr;   
		$this->setTable($this->tb);
		$this->setRefArray($this->tabfields);
	}

	/**
	* Gets all immunization data based on passed condition.
	*
	* The returned  array contains the data with the index keys as set in the <var>$tabfields</var> array.
	* @param string Query constraint
	* @param string Item for sorting basis
	* @return mixed 2 dimensional array or boolean
	*/			
	function _getalldata($cond='1',$sort=''){
	    global $db;
		
		if(!empty($sort)) $sort=" ORDER BY $sort";
	    if ($this->result=$db->Execute("SELECT * FROM $this->tb WHERE $cond AND status NOT IN ($this->dead_stat) $sort")) {
		    if ($this->dept_count=$this->result->RecordCount()) {
		        return $this->result->GetArray();
			} else {
				return false;
			}
		}
		else {
		    return false;
		}
	}
	/**
	* Gets all immunization data without any condition.
	*
	* The returned  array contains the data with the index keys as set in the <var>$tabfields</var> array.
	* @param string Query constraint
	* @param string Item for sorting basis
	* @return mixed 2 dimensional array or boolean
	*/			
	function getAllNoCondition($sort=''){
	    global $db;
		
		if(!empty($sort)) $sort=" ORDER BY $sort";
	    if ($this->result=$db->Execute("SELECT * FROM $this->tb  $sort")) {
		    if ($this->dept_count=$this->result->RecordCount()) {
		        return $this->result->GetArray();
			} else {
				return false;
			}
		}
		else {
		    return false;
		}
	}
	/**
	* Gets all immunization data without any condition. Unsorted.
	*
	* The returned  array contains the data with the index keys as set in the <var>$tabfields</var> array.
	* @return mixed 2 dimensional array or boolean
	*/			
	function getAll() {
		return $this->_getalldata('1');
	}
	/**
	* Gets all immunization data without any condition. Sorted.
	*
	* The returned  array contains the data with the index keys as set in the <var>$tabfields</var> array.
	* @param string Item for sorting basis
	* @return mixed 2 dimensional array or boolean
	*/			
	function getAllSort($sort='') {
		return $this->_getalldata('1',$sort);
	}
	/**
	* Gets only ACTIVE immunization data. Sorted.
	*
	* The returned  array contains the data with the index keys as set in the <var>$tabfields</var> array.
	* @param string Item for sorting basis
	* @return mixed 2 dimensional array or boolean
	*/			
	function getAllActiveSort($sort='') {
		return $this->_getalldata("is_inactive=0",$sort);
	}
	/**
	* Gets all application types.
	*
	* The returned  2 dimensional array contains the data with the following index keys:
	* - nr = primary key number
	* - group_nr = group number
	* - type = application type
	* - name = default application type's name
	* - LD_var = the variable's name for foreign language version of the type's name
	* - description = description of the application type
	*
	* @param string Item for sorting basis
	* @return mixed 2 dimensional array or boolean
	*/			
	function getAppTypes(){
	    global $db;
	
	    if ($this->result=$db->Execute("SELECT nr,group_nr,type,name,LD_var AS \"LD_var\",description FROM $this->tb_types")) {
		    if ($this->result->RecordCount()) {
		        return $this->result->GetArray();
			} else {
				return false;
			}
		}
		else {
		    return false;
		}
	}
	/**
	* Gets application type information based on its type number.
	*
	* The returned  array contains the data with the following index keys:
	* - type = application type
	* - group_nr = group number
	* - name = default application type's name
	* - LD_var = the variable's name for foreign language version of the type's name
	* - description = description of the application type
	*
	* @param int Application type number
	* @return mixed or boolean
	*/			
	function getTypeInfo($type_nr){
	    global $db;
	
	    if ($this->result=$db->Execute("SELECT type,group_nr,name,LD_var AS \"LD_var\",description FROM $this->tb_types WHERE nr=$type_nr")) {
		    if ($this->result->RecordCount()) {
		        return $this->result->FetchRow();
			} else {
				return false;
			}
		}
		else {
		    return false;
		}
	}
	/**
	* Resolves the city/town's id.
	* @access private
	* @param string city/town id
	* @return boolean
	* burn added: August 23, 2006
	*/
	function _internResolveCityTownID($immunization_id='') {
	    if (empty($immunization_id)) {
		    if(empty($this->immunization_id)) {
			    return FALSE;
			} else { return TRUE; }
		} else {
		     $this->immunization_id=$immunization_id;
			return TRUE;
		}
	}

	/**
	* Gets the usage frequency of a city/town based on its primary key "nr".
	*
	* @access public
	* @param string city/town id
	* @return mixed integer or boolean
	* burn added: August 23, 2006
	*/

    function getUseFrequency($immunization_id='') {
	
        global $db;
        
	    if(!$this->_internResolveCityTownID($immunization_id)) return FALSE;
	    if($this->result=$db->Execute("SELECT use_frequency FROM $this->tb WHERE nr=$this->immunization_id")) {
	        if($this->result->RecordCount()) {
		        $this->row=$this->result->FetchRow();
			    return $this->row['use_frequency'];
		    } else { return FALSE; }
	   } else { return FALSE; }
	   
    }

    /**
	* Increases usage frequency of a city/town.
	*
	* @access public
	* @param string city/town id
	* @param int Increase step
	* @return boolean
	* burn added: August 23, 2006
	*/

	function updateUseFrequency($immunization_id='',$step=1) {

		if(!$this->_internResolveCityTownID($immunization_id)) return FALSE;
		# Get last usage frequency value
		//$this->buffer=getUseFrequency($this->immunization_id);
		//$this->sql="UPDATE $this->tb SET use_frequency=".($this->buffer+$step)." WHERE nr=$this->immunization_id";

		$this->sql="UPDATE $this->tb SET use_frequency=(use_frequency + 1) WHERE nr=$this->immunization_id";
		if($this->result=$this->Transact($this->sql)) {
			if($this->result->Affected_Rows()) {
				return TRUE;
			} else { return FALSE; }
		} else { return FALSE; }
   }
 	
} # end of class Immunization
?>
