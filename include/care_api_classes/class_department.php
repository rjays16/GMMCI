<?php
/**
* @package care_api
*/

/**
*/
require_once($root_path.'include/care_api_classes/class_core.php');
/**
*  Department methods.
*  Note this class should be instantiated only after a "$db" adodb  connector object  has been established by an adodb instance.
* @author Elpidio Latorilla
* @version beta 1.0.08
* @copyright 2002,2003,2004,2005,2005 Elpidio Latorilla
* @package care_api
*/
class Department extends Core {
	/**
	* Table name for department data
	* @var string
	*/
	var $tb='care_department';
	/**
	* Table name for personell assignment data
	* @var string
	*/
	var $tb_assign='care_personell_assignment';
	/**
	* Table name for personell data
	* @var string
	*/
	var $tb_personell='care_personell';
	/**
	* Table name for users data
	* @var string
	*/
	var $tb_users='care_users';
	/**
	* Table name for department types data
	* @var string
	*/
	var $tb_types='care_type_department';
	/**
	* Table name for phone and contact data
	* @var string
	*/
	var $tb_cphone='care_phone';
	/**
	* Table name for rooms
	* @var string
	*/
	var $tb_room='care_room';
	/**
	* Holder for sql query results.
	* @var object adodb record object
	*/
	var $result;
	/**
	* Holder for preloaded dept data
	* @var object adodb record object
	*/
	var $preload_dept;
	/**
	* Preloaded flag
	* @var boolean
	*/
	var $is_preloaded=FALSE;
	/**
	* Holder for departments count
	* @var int
	*/
	var $dept_count;
	/**
	* Holder for current department number
	* @var int
	*/
	var $dept_nr;
	/**
	* Field names of care_department table
	* @var array
	*/
	var $tabfields=array('nr',
									'id',
									'type',
									'name_formal',
									'name_short',
									'name_alternate',
									'LD_var',
									'description',
									'admit_inpatient',
									'admit_outpatient',
									'has_oncall_doc',
									'has_oncall_nurse',
									'does_surgery',
									'this_institution',
									'is_sub_dept',
									'parent_dept_nr',
									'work_hours',
									'consult_hours',
									'is_inactive',
									'sort_order',
									'address',
									'sig_line',
									'sig_stamp',
									'logo_mime_type',
									'status',
									'history',
									'modify_id',
									'modify_time',
									'create_id',
									'create_time');
                                    
    
    var $tb_area='seg_areas'; 
    var $fld_area=array('area_code',
                        'area_name',
                        'allow_socialized',
                        'lockflag',
                        'dept_nr',
                        'ward_nr');
    
	/**
	* Constructor
	* @param int Department number
	*/
	function Department($nr=0){
		$this->setTable($this->tb);
		$this->setRefArray($this->tabfields);
		$this->dept_nr=$nr;
	}
	/**
	* Gets all data from the care_department table
	* @access private
	* @param string WHERE condition of the sql query
	* @param string Sort item
	* @param string  Determines the return type whether adodb object (_OBJECT) or assoc array (_ARRAY, '', empty)
	* @return mixed boolean or adodb record object or assoc array, determined by param $ret_type
	*/
	function _getalldata($cond='1',$sort='',$ret_type=''){
			global $db;
		if(empty($sort)) $sort='name_formal';
		$this->sql="SELECT *, LD_var AS \"LD_var\" FROM $this->tb WHERE $cond AND status NOT IN ($this->dead_stat) ORDER BY $sort";

#echo "class_department.php : _getalldata : this->sql = '".$this->sql."' <br> \n";
		# echo "ano = ".$this->res['_gald'];
		 if ($this->res['_gald']=$db->Execute($this->sql)) {
				if ($this->dept_count=$this->res['_gald']->RecordCount()){
								$this->count=$this->dept_count;
				$this->rec_count=$this->dept_count;
						if($ret_type=='_OBJECT'){
#echo "class_department.php : _getalldata : this->sql = '".$this->sql."' <br> \n";
#echo "class_department.php : _getalldata : this->res['_gald'] = '".$this->res['_gald']."' <br> \n";
						 return $this->res['_gald'];
					}
					else return $this->res['_gald']->GetArray();
			}else{
				return FALSE;
			}
		}else{
				return FALSE;
		}
	}
	/**
	* Gets all departments without condition
	* @access public
	* @param string  Sort condition which includes the field name and the sort direction e.g. "ORDER BY name_formal DESC"
	* @return mixed boolean or 2 dimensional array
	*/
	function getAllNoCondition($sort=''){
			global $db;

		if(!empty($sort)) $sort=" ORDER BY $sort";
		$this->sql="SELECT *, LD_var AS \"LD_var\" FROM $this->tb $sort";
			if ($this->result=$db->Execute($this->sql)) {
				if ($this->dept_count=$this->result->RecordCount()) {
						return $this->result->GetArray();
			}else{
				return FALSE;
			}
		}else{
				return FALSE;
		}
	}
	/**
	* Gets all departments without condition. The result is assoc array sorted by departments formal name
	* @access public
	* @return mixed boolean or adodb record object or assoc array
	*/
	function getAll() {
		return $this->_getalldata('1');
	}
	/**
	* Gets all ACTIVE departments. The result is assoc array sorted by departments formal name
	* @access public
	* @return mixed boolean or adodb record object or assoc array
	*/
	function getAllActive() {
		return $this->_getalldata("is_inactive='0'");
	}
	/**
	* Gets all ACTIVE departments. The result is adodb record object sorted by departments formal name
	* @access public
	* @return mixed boolean or adodb record object or assoc array
	*/
	function getAllActiveObject() {
		return $this->_getalldata("is_inactive='0'",'','_OBJECT');
	}
	/**
	* Gets all departments without condition
	* @access public
	* @param string Sort item e.g. "name_formal"
	* @return mixed boolean or adodb record object or assoc array
	*/
	function getAllSort($sort='') {
		return $this->_getalldata('1',$sort);
	}
	/**
	* Gets all ACTIVE departments. The result is assoc array sorted by param $sort
	* @access public
	* @return mixed boolean or adodb record object or assoc array
	*/
	function getAllActiveSort($sort='') {
		return $this->_getalldata("is_inactive='0'",$sort);
	}
	/**
	* Gets all ACTIVE medical departments. The result is assoc array sorted by departments formal name
	* @access public
	* @return  mixed assoc array (sorted by param $sort) or boolean or adodb record object
	*/
	function getAllMedical() {
		return $this->_getalldata("type=1 AND is_inactive='0'");
	}

	/**
	* Gets all ACTIVE common medical departments. The result is assoc array sorted by departments formal name
	* @access public
	* @return  mixed assoc array (sorted by param $sort) or boolean or adodb record object
	*/
	function getAllCommonMedical() {
		return $this->_getalldata("type=1 AND is_inactive='0' AND parent_dept_nr = '0' AND has_oncall_doc=1",'','_OBJECT');
	}
	/*
	*  @return sub-departments of the specified parent department
	*  @ burn created : August 6, 2007
	*/
	function getSubDept($parent_dept_nr) {
		return $this->_getalldata("type=1 AND is_inactive='0' AND parent_dept_nr = '$parent_dept_nr' AND has_oncall_doc=1",'','_OBJECT');
	}

	function getAllRadiologyDept() {
		return $this->_getalldata("type=1 AND is_inactive='0' AND parent_dept_nr = '158' AND has_oncall_doc=1",'','_OBJECT');
	}
	/**
	*	Returns the department's classification where a user belongs
	* 	@access public
	*	@param string, user name
	*	@return int, 0=n/a; 1=OPD; 2=IPD; 3=Non-medical; 4=News
	*/
	function getDepartmentClass($user){
		global $db;

		$this->sql = "SELECT U.name, U.login_id, U.personell_nr, P.nr, P.pid, P.job_function_title,
						PA.personell_nr, PA.role_nr, PA.location_type_nr, PA.location_nr,
						D.nr AS dept_nr, D.type, D.name_formal, D.admit_inpatient
					FROM $this->tb_users as U, $this->tb_personell as P,
						$this->tb_assign as PA, $this->tb as D
					WHERE U.personell_nr = P.nr and P.nr=PA.personell_nr
						and PA.location_nr=D.nr and name='$user'";
			#echo "getDepartmentClass : this->sql = '".$this->sql."' <br> \n";
		if ($this->result=$db->Execute($this->sql)) {
			if ($this->result->RecordCount()){
				$rs = $this->result->FetchRow();
				if (($rs['type']==1) && ($rs['admit_inpatient']==0))
					return  1;   # user is under OPD
				elseif (($rs['type']==1) && ($rs['admit_inpatient']==1))
					return  2;   # user is under IPD
				elseif ($rs['type']==2)
					return  3;   # user is under Non-medical Department
				elseif ($rs['type']==3)
					return  4;   # user is under News
				else
					return FALSE;
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}/* end of function getDepartmentClass */

	#---------add 02-26-07-------------------
	function getAllNonMedical() {
		return $this->_getalldata("type!=1 AND is_inactive='0'");

	}

	/**
	*	Returns the personnel and department's information where a user belongs
	* 	@access public
	*	@param string, user name
	*	return mixed adodb record object or boolean FALSE
	*/
	function getUserDeptInfo($user){
		global $db;

		#edited by VAN 01-25-10
		$this->sql ="SELECT U.name, U.login_id, U.personell_nr, P.nr, P.pid, P.job_function_title, PA.personell_nr, PA.role_nr, PA.location_type_nr, PA.location_nr, D.parent_dept_nr, D.nr AS dept_nr, D.id, D.type, D.name_formal, D.admit_inpatient, D.admit_outpatient
									FROM care_users as U
									INNER JOIN care_personell_assignment as PA ON U.personell_nr=PA.personell_nr
									INNER JOIN care_personell as P ON P.nr = PA.personell_nr
									INNER JOIN care_department as D ON PA.location_nr=D.nr
									WHERE login_id='".$user."'
									AND ((date_end NOT IN (DATE(NOW())) AND date_end > DATE(NOW())) OR date_end='0000-00-00' OR date_end IS NULL)
									AND PA.status NOT IN ('deleted','hidden','inactive','void')" ;
#echo "getUserDeptInfo : this->sql = '".$this->sql."' <br> \n";
		if ($this->result=$db->Execute($this->sql)){
			if ($this->result->RecordCount())
				return $this->result->FetchRow();
			else
				return FALSE;
		}else{
			return FALSE;
		}
	}

	function getAllOPDMedicalObject($admit_patient,$cond='') {
		if ($admit_patient){
#			$temp = $this->_getalldata("type=1 AND is_inactive='0' AND admit_inpatient='$admit_patient' $cond",'','_OBJECT');
			//echo "getAllOPDMedicalObject : temp = '".$temp."'; \n cond ='$cond' \n this-sql ='$this->sql'";
#			return $temp;
			return $this->_getalldata("type=1 AND is_inactive='0' AND admit_inpatient='$admit_patient' $cond",'','_OBJECT');
#			return $this->_getalldata("type=1 AND is_inactive='0' AND admit_inpatient='$admit_patient'",'','_OBJECT');
		}else{
			return $this->_getalldata("type=1 AND is_inactive='0' AND admit_outpatient=1 $cond",'','_OBJECT');
#			return $this->_getalldata("type=1 AND is_inactive='0' AND admit_outpatient=1",'','_OBJECT');
		}
#		return $this->_getalldata("type=1 AND is_inactive='0' AND admit_inpatient='$admit_patient'",'','_OBJECT');
	}

	function getAllOPDMedicalObject1($admit_patient, $dept_nr,$cond=''){
		if ($admit_patient){
			return $this->_getalldata("type=1 AND is_inactive='0' AND admit_inpatient='$admit_patient' AND nr='$dept_nr' $cond",'','_OBJECT');
#			return $this->_getalldata("type=1 AND is_inactive='0' AND admit_inpatient='$admit_patient' AND nr='$dept_nr'",'','_OBJECT');
		}else{
			return $this->_getalldata("type=1 AND is_inactive='0' AND admit_outpatient=1 AND nr='$dept_nr' $cond",'','_OBJECT');
#			return $this->_getalldata("type=1 AND is_inactive='0' AND admit_outpatient=1 AND nr='$dept_nr'",'','_OBJECT');
		}
#		return $this->_getalldata("type=1 AND is_inactive='0' AND admit_inpatient='$admit_patient' AND nr='$dept_nr'",'','_OBJECT');
	}


	function getDeptofDoctor($personell_nr=0){
	global $db;

		#echo "SELECT * FROM care_department WHERE nr='$dept_nr'";
		$this->sql = "SELECT pa.personell_nr, pa.location_nr, d.nr, d.id, d.name_formal
								FROM $this->tb_assign AS pa, $this->tb AS d
								WHERE pa.location_nr = d.nr AND personell_nr = '$personell_nr'
							AND pa.status NOT IN ($this->dead_stat)
							LIMIT 1";

		//$this->sql = "SELECT * FROM $this->tb_dept WHERE type=1 AND nr = '$nr' LIMIT 1";
#echo "class-department.php : getDeptofDoctor : this->sql = '".$this->sql."' <br> \n";
		if ($this->result=$db->Execute($this->sql)) {
				 $this->count=$this->result->RecordCount();
				 return $this->result->FetchRow();
			} else{
				 return FALSE;
			}
	}

	/*
	function getDepartment($dept_nr=0){
	global $db;
		$rs =$db->Execute("SELECT nr, name_formal from $this->tb where nr = '$dept_nr'");

		while ($field = $rs->FetchRow()) {
				return $field['name_formal'];
		}
	}
	*/
	#----------------------------------------

    function getAllDeptObject() {
        return $this->_getalldata("is_inactive='0'",'','_OBJECT');
    }
    
	/**
	* Gets all ACTIVE medical departments. The result is adodb record object sorted by departments formal name
	* Returns adodb record object sorted by departments formal name
	* @access public
	* @return mixed assoc array (sorted by department formal name) or boolean or adodb record object
	*/
	function getAllMedicalObject() {
		return $this->_getalldata("type=1 AND is_inactive='0'",'','_OBJECT');
	}

	function getAllMedicalObject2() {
		return $this->_getalldata("type=1",'','_OBJECT');
	}
	/**
	* Gets all ACTIVE medical departments with doctors-on-call  or nurse-on-call assigned. The result is assoc array sorted by departments formal name
	* @access public
	* @return  mixed assoc array (sorted by department formal name) or boolean or adodb record object
	*/
	function getAllMedicalWithOnCall() {
		return $this->_getalldata("type=1 AND is_inactive='0' AND (has_oncall_doc=1 OR has_oncall_nurse=1)");
	}
	/**
	* Gets all ACTIVE NON-MEDICAL departments. The result is assoc array sorted by departments formal name
	* @access public
	* @return  mixed assoc array (sorted by department formal name) or boolean or adodb record object
	*/
	function getAllSupporting() {
		return $this->_getalldata("type=2 AND is_inactive='0'");
	}
	/**
	* Gets all ACTIVE NEWS departments. The result is assoc array sorted by departments formal name
	* @access public
	* @return  mixed assoc array (sorted by department formal name) or boolean or adodb record object
	*/
	function getAllNewsGroup() {
		return $this->_getalldata("type=3 AND is_inactive='0'");
	}
	/**
	* Gets all ACTIVE medical departments with doctors-on-call  assigned. The result is assoc array sorted by departments formal name
	* @access public
	* @return  assoc array sorted by departments formal name
	*/
	function getAllActiveWithDOC(){
		return $this->_getalldata("type=1 AND is_inactive='0' AND has_oncall_doc=1");
	}

	/**
	* Gets all ACTIVE non-medical departments. The result is assoc array sorted by departments formal name
	* @access public
	* @return  mixed assoc array (sorted by department formal name) or boolean or adodb record object
	*/
	function getAllActiveWithStaff(){
		return $this->_getalldata("type!=1 AND is_inactive='0'");
	}

	/**
	* Gets all ACTIVE medical departments with nurse-on-call  assigned. The result is assoc array sorted by departments formal name
	* @access public
	* @return  mixed assoc array (sorted by department formal name) or boolean or adodb record object
	*/
	function getAllActiveWithNOC(){
		return $this->_getalldata("type=1 AND is_inactive='0' AND has_oncall_nurse=1");
	}
	/**
	* Gets all ACTIVE medical departments that does surgery. The result is assoc array sorted by departments formal name
	* @access public
	* @return mixed assoc array (sorted by department formal name) or boolean or adodb record object
	*/
	function getAllActiveWithSurgery(){
		return $this->_getalldata("type=1 AND is_inactive='0' AND does_surgery=1");
	}

	/**
	* Gets all department types information. The result is assoc array unsorted.
	* @access public
	* @return  mixed 2 dimensional array unsorted or boolean
	*/
	function getTypes(){
			global $db;

			if ($this->result=$db->Execute("SELECT nr,type,name,LD_var AS \"LD_var\", description FROM $this->tb_types")) {
				if ($this->result->RecordCount()) {
						return $this->result->GetArray();
			} else {
				return FALSE;
			}
		}
		else {
				return FALSE;
		}
	}
	/**
	* Gets a department type information. The result is 2 dimensional associative array.
	* @access public
	* @return mixed 1 dimensional assoc array or boolean
	*/
	function getTypeInfo($type_nr){
			global $db;

			if ($this->result=$db->Execute("SELECT type,name,LD_var AS \"LD_var\", description FROM $this->tb_types WHERE nr=$type_nr")) {
				if ($this->result->RecordCount()) {
						return $this->result->FetchRow();
			} else {
				return FALSE;
			}
		} else {
				return FALSE;
		}
	}

	/*
	*	burn added: July 19, 2007
	*/
	function	getAncestorDept($dept_nr=0){
		global $db;
#echo "ANCESTORS : <br> \n";
			# get the ancestor department/s
		$sql ="SELECT fn_get_parent_dept($dept_nr) AS ancestors";

#echo "class_personell.php : getAncestorDept : sql = '".$sql."' <br> \n";

		if ($result=$db->Execute($sql)) {
#echo "class_personell.php : getAncestorChildrenDept : result :<br>\n"; print_r($result); echo" <br> \n";
			if ($record_count=$result->RecordCount()) {
				$ancestors = $result->FetchRow();
#echo "class_personell.php : getAncestorDept : ancestors :<br>\n"; print_r($ancestors); echo" <br> \n";
#echo "class_personell.php : getAncestorDept : ancestors = '".$ancestors."' <br> \n";
			}
		}
		return str_replace(' ',',',trim($ancestors['ancestors']));
	}/* end of function getAncestorDept */

	/*
	*	burn added: July 19, 2007
	*/
	function	getChildrenDept($dept_nr=0){
		global $db;
#echo "DESCENDANTS : <br> \n";
			# get the descendant department/s
		$sql ="SELECT fn_get_children_dept($dept_nr) AS descendants";

#echo "class_personell.php : getChildrenDept : sql = '".$sql."' <br> \n";

		if ($result=$db->Execute($sql)) {
#echo "class_personell.php : getChildrenDept : result :<br>\n"; print_r($result); echo" <br> \n";
			if ($record_count=$result->RecordCount()) {
				$decendants = $result->FetchRow();
#echo "class_personell.php : getChildrenDept : decendants :<br>\n"; print_r($decendants); echo" <br> \n";
#echo "class_personell.php : getChildrenDept : decendants = '".$decendants."' <br> \n";
			}
		}
		return str_replace(' ',',',trim($decendants['descendants']));
	}/* end of function getChildrenDept */

	 /*
	 *   Added by Cherry 06-25-10
	 * 	Gets departments who does surgeries
	 */
	function getDeptDoesSurgery(){
		global $db;
		$listDept[0]= "-Select Department-";
		$sql = "SELECT distinct nr, name_formal FROM care_department WHERE does_surgery=1";
		/*if ($result=$db->Execute($sql)) {
				if ($result->RecordCount()) {
						return $result->GetArray();
			}else{
				return FALSE;
			}
		}else{
				return FALSE;
		}*/
		if ($result=$db->Execute($sql)) {
			if ($result->RecordCount()) {
				#$row = $result->GetArray();
				while ($row = $result->FetchRow()) {
					 #echo "row[nr]= ".$row['nr']."row[name_formal]= ".$row['name_formal']."<br>";
					 #$listDept.= "[".$row['nr']."]=>".$row['name_formal'];
					 $listDept[$row['nr']] = $row['name_formal'];
				}
				#print_r($listDept);
				return $listDept;
			}else{
				return FALSE;
			}
		}else{
			return FALSE;
		}
	}

	/*
	*	burn added: May 31, 2007
	*/
	function	getAncestorChildrenDept($dept_nr=0){
		global $db;
#echo "class_personell.php : getAncestorChildrenDept : dept_nr = '".$dept_nr."' <br> \n";

		if (!$dept_nr) return FALSE;

#echo "ANCESTORS : <br> \n";
		$ancestors = $this->getAncestorDept($dept_nr);
		#echo "<br> ancestors sql = ".$this->sql;

#echo "DESCENDANTS : <br> \n";
		$decendants = $this->getChildrenDept($dept_nr);
		#echo "<br> desc sql = ".$this->sql;

#		return str_replace(' ',',',trim($ancestors['ancestors'].' '.$decendants['descendants']));
		return str_replace(' ',',',trim($ancestors.' '.$decendants));
	}/* end of function getAncestorChildrenDept */

	/**
	* Gets all information  of one department. The result is 2 dimensional associative array.
	* @access public
	* @param int Department number
	* @return mixed 1 dimensional associative array or boolean
	*/
	function getDeptAllInfo($nr){
			global $db;
        $this->sql="SELECT *, LD_var AS \"LD_var\" FROM $this->tb WHERE nr=$nr";
			if ($this->result=$db->Execute($this->sql)) {
				if ($this->result->RecordCount()) {
						return $this->result->FetchRow();
			} else {
				return FALSE;
			}
		} else {
				return FALSE;
		}
	}
	/**
	* Preloads all information  of one department into the objects internal buffer $preload_dept and sets the internal flag $is_preloaded.
	* The information is returned individually through the appropriate methods
	* @access public
	* @param int Department number
	* @return  boolean
	*/
	function preloadDept($nr=0){
			global $db;
		if(!$nr) return FALSE;
		$this->sql="SELECT *, LD_var AS \"LD_var\" FROM $this->tb WHERE nr=$nr";
			if ($this->result=$db->Execute($this->sql)) {
				if ($this->dept_count=$this->result->RecordCount()) {
						$this->preload_dept=$this->result->FetchRow();
				$this->is_preloaded=TRUE;
				return TRUE;
			} else {
				return FALSE;
			}
		} else {
				return FALSE;
		}
	}
	/**
	* Empties the internal buffer $preload_dept and resets the internal flag $is_preloaded
	* @access public
	* @return  boolean
	*/
	function unloadDept(){
		if($this->is_preloaded){
			$this->preload_dept=NULL;
			$this->is_preloaded=FALSE;
		}
		return TRUE;
	}
	/**
	* Returns the department number. Use preferably after the department was successfully preloaded by the preloadDept() method.
	* @return mixed integer or boolean
	*/
	function Nr(){
		if(!$this->is_preloaded) return FALSE;
		return $this->preload_dept['nr'];
	}
	/**
	* Returns the department ID. Use preferably after the department was successfully preloaded by the preloadDept() method.
	* @return mixed string or boolean
	*/
	function ID(){
		if(!$this->is_preloaded) return FALSE;
		return $this->preload_dept['id'];
	}
	/**
	* Returns the department type. Use preferably after the department was successfully preloaded by the preloadDept() method.
	* @return mixed integer or boolean
	*/
	function Type(){
		if(!$this->is_preloaded) return FALSE;
		return $this->preload_dept['type'];
	}
	/**
	* Returns the department formal name. Use preferably after the department was successfully preloaded by the preloadDept() method.
	* @return mixed string or boolean
	*/
	function FormalName($nr=0){
		if($this->is_preloaded){
			return $this->preload_dept['name_formal'];
		}elseif($nr){
			$this->dept_nr=$nr;
			return $this->_getItem('name_formal');
		}else{
			return FALSE;
		}
	}


	#----------added by VAN------

	function getDepartmentInfo($cond="1", $sort='') {
		global $db;

		$this->sql="SELECT * FROM $this->coretable WHERE $cond ORDER BY $sort";
			#echo "sql = ".$this->sql;
		if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()){
				return $this->result->FetchRow();
			}else{
				return FALSE;
			}
		}else{
			return FALSE;
		}
	}

	function getShortNameOne($nr) {
		if($nr) {
			$this->dept_nr=$nr;
			return $this->_getItem('name_short');
		}
	}

	#--------------------------

	/**
	* Returns the department short name. Use preferably after the department was successfully preloaded by the preloadDept() method.
	* @return mixed string or boolean
	*/
	function ShortName(){
		if(!$this->is_preloaded) return FALSE;
		return $this->preload_dept['name_short'];
	}
	/**
	* Returns the department address. Use preferably after the department was successfully preloaded by the preloadDept() method.
	* @return string
	*/
	function Address($nr=0){
		if(!$this->is_preloaded){
			if($nr) $this->dept_nr=$nr;
			return $this->_getItem('address');
		}
		return $this->preload_dept['address'];
	}
	/**
	* Returns the department signature stamp. Use preferably after the department was successfully preloaded by the preloadDept() method.
	* @return string
	*/
	function SignatureStamp($nr=0){
		if(!$this->is_preloaded){
			if($nr) $this->dept_nr=$nr;
			return $this->_getItem('sig_stamp');
		}
		return $this->preload_dept['sig_stamp'];
	}
	/**
	* Returns the department's language dependent variable name. Use preferably after the department was successfully preloaded by the preloadDept() method.
	* @return string
	*/
	function LDvar($nr=0){
		if(!$this->is_preloaded){
			if($nr) $this->dept_nr=$nr;
			return $this->_getItem('LD_var AS "LD_var"','LD_var');
		}
		return $this->preload_dept['LD_var'];
	}
	/**
	* Gets the item information of a department from the care_department table.
	* Use only if the department number was previously set with the constructor or with the setDeptNr() method.
	* @access private
	* @param string Item or field name for extracting including name aliasing  with AS
	* @param string actual return name of the item (optional: used if the first param has an aliasing)
	* @return mixed 1 dimensional array or FALSE
	*/
	function _getItem($item='', $retname=''){
			global $db;
		$row='';
		if(empty($item)) return FALSE;
			if ($this->result=$db->Execute("SELECT $item FROM $this->tb WHERE nr=$this->dept_nr")) {
				if ($this->result->RecordCount()) {
						$row=$this->result->FetchRow();
				if(!empty($retname)) return $row[$retname];
					else return $row[$item];
			} else {
				return FALSE;
			}
		}
		else {
				return FALSE;
		}
	}
	/**
	* Gets the contact (phone, beeper, etc) of a department from the care_department table.
	* @access public
	* @param int Department number
	* @return array Associative array
	*/
	function getPhoneInfo($nr){
		global $db;
		$sql="SELECT * FROM $this->tb_cphone WHERE dept_nr=$nr";

			if ($this->res['gpi']=$db->Execute($sql)) {
				if ($this->record_count=$this->res['gpi']->RecordCount()) {
				return $this->res['gpi']->FetchRow();
			} else {
				return FALSE;
			}
		}else {
			return FALSE;
		}
	}
	/**
	* Sets the department number used by the object on run time
	* @param int Department number
	*/
	function setDeptNr($nr){
		$this->dept_nr=$nr;
	}
	/**
	* Gets all active OR Room numbers
	* return mixed adodb record object or boolean FALSE
	*/
	function getAllActiveORNrs(){
		global $db;
		/*
		$this->sql="SELECT nr, room_nr,info FROM $this->tb_room
						WHERE type_nr=2
							AND is_temp_closed IN ('',0)
							AND status NOT IN ('closed',$this->dead_stat)
						ORDER BY room_nr";
		*/
		#edited by VAN 06-24-08
		$this->sql="SELECT r.nr, r.room_nr,info, t.type
					FROM $this->tb_room AS r
					INNER JOIN care_type_room AS t ON t.nr=r.type_nr
					WHERE t.type='or' AND is_temp_closed IN ('',0)
					AND r.status NOT IN ('closed',$this->dead_stat)
					ORDER BY r.room_nr";

			if ($this->res['gaaon']=$db->Execute($this->sql)) {
				if ($this->res['gaaon']->RecordCount()) {
				return $this->res['gaaon'];
			}else{return FALSE;}
		}else{return FALSE;}
	}

	#added by VAN 06-24-08
	function getAllActiveORNrsByDept($dept_nr){
		global $db;

		$this->sql="SELECT r.nr, r.room_nr,info, t.type
					FROM $this->tb_room AS r
					INNER JOIN care_type_room AS t ON t.nr=r.type_nr
					WHERE t.type='or' AND is_temp_closed IN ('',0)
					AND r.status NOT IN ('closed',$this->dead_stat)
					AND r.dept_nr = '$dept_nr'
					ORDER BY r.room_nr";

			if ($this->res['gaaon']=$db->Execute($this->sql)) {
				if ($this->res['gaaon']->RecordCount()) {
				return $this->res['gaaon'];
			}else{return FALSE;}
		}else{return FALSE;}
	}

	#--------------------------

	/**
	* Checks if department does surgery
	* @access public
	* @param int Department number
	* @return boolean
	*/
	function isSurgery($dept_nr=0){
		global $db;
		if(!$dept_nr) return FALSE;
		$this->sql="SELECT nr FROM care_department WHERE nr=$dept_nr AND does_surgery=1";

		if($this->result=$db->Execute($this->sql)){
			if($this->result->RecordCount()){
				return TRUE;
			}else{return FALSE;}
		}else{return FALSE;}
	}
	/**
	* Checks if room number is an operating room (OR)
	* @access public
	* @param int Room number
	* @return boolean
	*/
	function isOR($room_nr=0){
		global $db;
		if(!$room_nr) return FALSE;
		#$this->sql="SELECT nr FROM care_room WHERE room_nr=$room_nr AND type_nr=2"; // 2=  op room type
		#edited by VAN 06-24-08
		$this->sql="SELECT r.nr
					FROM care_room AS r
					INNER JOIN care_type_room AS t ON t.nr=r.type_nr
					WHERE room_nr=$room_nr
					AND t.type='or'";
		#echo $this->sql;
		if($this->result=$db->Execute($this->sql)){
			if($this->result->RecordCount()){
				return TRUE;
			}else{return FALSE;}
		}else{return FALSE;}

	}

		#added by VAN 06-18-09
		function isAParentDept($dept_nr=0){
				global $db;

				$this->sql = "SELECT nr,id,name_formal FROM care_department WHERE parent_dept_nr='".$dept_nr."'";

				if ($this->result=$db->Execute($this->sql)) {
				 $this->count=$this->result->RecordCount();
				 return $this->result;
			} else{
				 return FALSE;
			}
		}
		#------

    // Added by LST - 12.08.2008
function getAreasInDept($dept_nr) {
        global $db;
        
		#if ( !(empty($dept_nr)) )
		if (!$dept_nr)
            $this->sql = "SELECT * FROM seg_areas ".
                         "   WHERE dept_nr = $dept_nr ".
                         "   order by area_name";
        else
            $this->sql = "SELECT * FROM seg_areas ".
                         "   order by area_name";        
        if ($this->result=$db->Execute($this->sql)){
            if ($this->result->RecordCount())
                return $this->result;
            else
                return FALSE;
        }else
            return FALSE;        
    }

function getAllAreas($area_code='') {
        global $db;
        
        if ($area_code != '') 
            $this->sql = "SELECT * FROM seg_areas ".
                         "   WHERE area_code <> '$area_code' ".
                         "   order by area_name";
        else
            $this->sql = "SELECT * FROM seg_areas ".
                         "   order by area_name";                                                          
        if ($this->result=$db->Execute($this->sql)){            
            if ($this->result->RecordCount())
                return $this->result;
            else
                return FALSE;
        }else
            return FALSE;        
    }    
	    # added by VAN 04-30-2010
	    /*function getAllRadiologyDeptWithAccess($dept_nr) {
				if ($dept_nr){
					if ($dept_nr!='158')
						$dept_cond = "AND nr IN (".$dept_nr.")";
					else
						$dept_cond = "";
				}else
					#$dept_cond = "AND nr IN ('164,166')";
					$dept_cond = "";

				return $this->_getalldata("type=1 AND is_inactive='0' AND parent_dept_nr = '158' AND has_oncall_doc=1 $dept_cond ",'','_OBJECT');
		}*/
        
	function getAreasInADept($dept_nr) {
        global $db;
        
        if (!(empty($dept_nr))) 
            $this->sql = "SELECT * FROM seg_areas ".
                         "   WHERE dept_nr IN ($dept_nr) ".
                         "   order by area_name";
        else
            $this->sql = "SELECT * FROM seg_areas ".
                         "   order by area_name";        
        if ($this->result=$db->Execute($this->sql)){
            if ($this->result->RecordCount())
                return $this->result;
            else
                return FALSE;
        }else
            return FALSE;        
    }
        function getAllRadiologyDeptWithAccess($dept_nr,$costcenter=0) {
                /*if ($dept_nr)
                    $dept_cond = "AND nr IN (".$dept_nr.")";
                else
                    $dept_cond = "AND nr NOT IN ('165')";

                return $this->_getalldata("type=1 AND is_inactive='0' AND parent_dept_nr = '158' AND has_oncall_doc=1 $dept_cond ",'','_OBJECT');
                */
                #edited by VAN 02-24-2011
                if ($dept_nr){
                    if ($costcenter){
                        if ($dept_nr!='158')
                            $dept_cond = "AND nr IN (".$dept_nr.")";
                        else
                            $dept_cond = "AND nr NOT IN ('165')";
                    }else
                         $dept_cond = "";
                }else
                    #$dept_cond = "AND nr IN ('164,166')";
                    if ($costcenter)
                        $dept_cond = "AND nr NOT IN ('165')";
                    else
                        $dept_cond = "";

                return $this->_getalldata("type=1 AND is_inactive='0' AND parent_dept_nr = '158' AND has_oncall_doc=1 $dept_cond ",'','_OBJECT');
        }    

	    function getSubDept2($parent_dept_nr, $dept_nr_list) {
				if ($dept_nr_list)
					$dept_cond = "AND nr IN (".$dept_nr_list.")";
				else
					$dept_cond = "AND nr IN ('164,166')";

				return $this->_getalldata("type=1 AND is_inactive='0' AND parent_dept_nr = '$parent_dept_nr' AND has_oncall_doc=1 $dept_cond ",'','_OBJECT');
	    }

}
?>
