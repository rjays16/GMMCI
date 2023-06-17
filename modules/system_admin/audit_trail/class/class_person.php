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

define('DIALYSIS_ENCOUNTER_TYPE', 5);
define('IC_ENCOUNTER_TYPE', 6);

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
        var $basic_list='pid
        ,title
        ,name_first
        ,name_last
        ,suffix
        ,name_2
        ,name_3
        ,name_middle
        ,name_maiden
        ,name_others
        ,date_birth
        ,sex
        ,addr_str
        ,addr_str_nr
        ,addr_zip
        ,addr_citytown_nr
        ,street_name
        ,brgy_nr
        ,photo_filename';
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
                                 'suffix',
                                 'date_birth',
                                 'birth_time',
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
                                 'mun_nr',   # VAN added: 09-27-08
                                 'citizenship',   # burn added: March 2, 2007
                                 'occupation',   # burn added: March 2, 2007
                                 'employer',        #added by VAN 05-01-08
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
                                 'fpimage_filename',  #added by LST --- 09.01.2009
                                 'ethnic_orig',
                                 'org_id',
                                 'sss_nr',
                                 'nat_id_nr',
                                 'religion',
                                 'mother_pid',
                                 'mother_fname',
                                 'mother_maidenname',
                                 'mother_mname',
                                 'mother_lname',
                                 'father_pid',
                                 'father_fname',
                                 'father_mname',
                                 'father_lname',
                                 'spouse_name',
                                 'guardian_name',
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
                                 'admitted_baby',
                                 'senior_ID',
                                 'veteran_ID',
                                 'is_indigent',
                                 'DOH_ID',
                                 'age');


        #added by VAN 09-08-08
        #var $tb_occupation='seg_occupation';
        var $fld_occupation=array('occupation_name',
                                                        'modify_id',
                                                        'modify_date',
                                                        'create_id',
                                                        'create_date');
        #---------------------

        /**
        * Constructor
        * @param int PID number
        */
        function Person ($pid='') {
            global $db;
            $db->SetFetchMode(ADODB_FETCH_ASSOC);
            
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
                #edited by VAN 06-10-09
                $this->sql="SELECT pid FROM $this->tb_person WHERE pid='$init_nr'";
                #$this->sql="SELECT pid FROM $this->tb_person";
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

        #added by VAN 05-08-08
        function getEducationalAttainment($cond='',$oitem='',$sort=''){
                global $db;

                if (!empty($cond))
                        $where=" WHERE $cond ";

                #$order=" ORDER BY educ_attain_name ";
                $order=" ORDER BY educ_attain_nr ";
                if (!empty($oitem))
                        $order=" ORDER BY ".$oitem." ".$sort;
                $this->sql="SELECT * FROM seg_educational_attainment $where $order";
#echo "getOccupation : this->sql = '".$this->sql."' <br> \n";
                if($this->res['go']=$db->Execute($this->sql)){
                        if($this->res['go']->RecordCount()){
                                return $this->res['go'];
                        }else{ return FALSE; }
                }else{ return FALSE; }
        }

        function getEthnic_orig($cond='',$oitem='',$sort=''){
                global $db;

                if (!empty($cond))
                        $where=" WHERE $cond ";
                $order=" ORDER BY name ";
                if (!empty($oitem))
                        $order=" ORDER BY ".$oitem." ".$sort;
                $this->sql="SELECT * FROM $this->tb_ethnic_orig $where $order";
                /*
                if($this->res['gr']=$db->Execute($this->sql)){
                        if($this->res['gr']->RecordCount()){
                                return $this->res['gr'];
                        }else{ return FALSE; }
                }else{ return FALSE; }
                */
                if ($this->result=$db->Execute($this->sql)){
                        #$this->count=$this->result->RecordCount();
                        if ($this->result->RecordCount())
                                return $this->result;
                        else
                                return FALSE;
                }else{
                        return FALSE;
                }
        }
        #---------------------------------------
        /**
        * Gets a new TEMPORARY patient number (pid).
        *
        * A reference number must be passed as parameter. The returned number will the highest number above the reference number PLUS 1.
        * @param int Reference PID number
        * @return integer
        *    burn added: March 5, 2007
        */
        function getNewPIDNr($ref_nr){
                global $db;
                $row=array();
                #edited by VAN 01-25-10
                $this->sql="SELECT pid FROM $this->tb_person WHERE pid>='$ref_nr' ORDER BY CAST(pid as UNSIGNED) DESC";
                
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
        *    burn added: July 25, 2007
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
#        echo "class_person.php getAge : bdate = '$bdate' <br> \n";
                                #  mm/dd/yyyy
                        list($bdateMonth,$bdateDay,$bdateYear) = explode("/",$bdate);

                        if (!checkdate($bdateMonth, $bdateDay, $bdateYear)){
#                echo "invalid birthdate! <br> \n";
                                return FALSE;
                        }
                        if (!empty($ddate)){
#                echo " ddate is true <br> \n";
                                        #  mm/dd/yyyy
                                list($ddateMonth,$ddateDay,$ddateYear) = explode("/",$ddate);
#echo " ddateMonth = '".$ddateMonth."' <br>\n ddateDay = '".$ddateDay."' <br>\n ddateYear = '".$ddateYear."' <br>\n  ";
                                if (!checkdate($ddateMonth, $ddateDay, $ddateYear)){
#                    echo "invalid deathdate! <br> \n";
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
#        echo "insertDataFromInternalArray() this->data_array: <br>\n";
                #print_r($this->data_array);
#        echo "<br><br>\n";
                return $this->insertDataFromArray($this->data_array);
        }

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
                        $pid_format = " p.pid='".$this->pid."' ";

                                # burn commented: March 14, 2007
                #edited by VAN 07-19-2010
                $this->sql="SELECT p.*, addr.name AS addr_citytown_name,ethnic.name AS ethnic_orig_txt,
                                     IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age) AS age,
                                     r.religion_name,p.street_name, sb.brgy_name, sm.zipcode, sm.mun_name, sp.prov_name,
                                     sr.region_name, oc.occupation_name
                                        FROM $this->tb_person AS p
                                        LEFT JOIN  $this->tb_citytown AS addr ON p.addr_citytown_nr=addr.nr
                                        LEFT JOIN  $this->tb_ethnic_orig AS ethnic ON p.ethnic_orig=ethnic.nr
                                        LEFT JOIN seg_religion AS r ON r.religion_nr=p.religion
                                        LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=p.brgy_nr
                                        LEFT JOIN seg_municity AS sm ON sm.mun_nr=p.mun_nr
                                        LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr
                                        LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr
                                        LEFT JOIN seg_occupation AS oc ON oc.occupation_nr=p.occupation
                                    WHERE $pid_format ";

                #echo "getAllInfoObject :  this->sql = '".$this->sql."' <br> \n";
                if($this->result=$db->Execute($this->sql)) {
                        if($this->result->RecordCount()) {
                                 return $this->result;
                        } else { return false; }
                } else { return false; }
        }

        function getFPImage($pid='') {
                global $db;

                $this->sql = "select hex(fpimage) as fpcode
                                                 from seg_fingerprint
                                                 where pid = $pid";

                if($this->result=$db->Execute($this->sql)) {
                        if($this->result->RecordCount()) {
                                $row = $this->result->FetchRow();
                                return $row["fpcode"];
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
                /*
                if (intval($pid))
                        $pid_format = " (p.pid='$this->pid' OR p.pid=$this->pid) ";
                else
                */
                        $pid_format = " p.pid='".$this->pid."' ";

                        # burn added: October 19, 2007
                        #line 687 is added by VAN 05-27-08
                $this->sql= "SELECT p.*, sb.brgy_name, sm.zipcode, sm.mun_name, sp.prov_name, sr.region_name,
                                                        sc.country_name AS country_citizenship,
                                                        so.occupation_name AS occupation_name,
                                                        sreli.religion_name AS religion_name,
                                                        IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age) AS age,
                                                        ethnic.name AS ethnic_orig_txt,
                                                        (SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.admission_dt)),20)) AS admission_dt,
                                                        (SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.encounter_date)),20)) AS encounter_date/*,
                                                        (SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.encounter_nr)),20)) AS encounter_nr  */
                                                FROM  $this->tb_person AS p
                                                                LEFT JOIN care_encounter AS enc ON enc.pid=p.pid
                                                                LEFT JOIN seg_country AS sc ON p.citizenship= sc.country_code
                                                                LEFT JOIN seg_occupation AS so ON p.occupation= so.occupation_nr
                                                                LEFT JOIN seg_religion AS sreli ON p.religion= sreli.religion_nr
                                                                LEFT JOIN $this->tb_ethnic_orig AS ethnic ON p.ethnic_orig=ethnic.nr
                                                                LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=p.brgy_nr
                                                                        LEFT JOIN seg_municity AS sm ON sm.mun_nr=p.mun_nr
                                                                                LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr
                                                                                        LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr
                                                WHERE $pid_format  GROUP BY pid ";

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
                        #if (intval($pid))
                        #    $pid_format = " (pid='$this->pid' OR pid=$this->pid) ";
                        #else
                                $pid_format = " pid='".$this->pid."' ";

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
                $list="cp.*, IF(fn_calculate_age(NOW(),cp.date_birth),fn_get_age(NOW(),cp.date_birth),age) AS age, ps.nr AS personnelID, sb.brgy_name, sm.zipcode, sm.mun_name, sp.prov_name, sr.region_name ";   # burn added: March 8, 2007

                #$from = " AS cp, seg_barangays AS sb, seg_municity AS sm, ".
                #          " seg_provinces AS sp, seg_regions AS sr ";   # burn added: March 8, 2007

                #edited by VAN 04-28-08
                $from = " AS cp
                                    LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=cp.brgy_nr
                                         LEFT JOIN seg_municity AS sm ON sm.mun_nr=cp.mun_nr
                                         LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr
                                         LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr
                                         LEFT JOIN care_personell AS ps ON cp.pid=ps.pid
                                                 /* AND date_exit NOT IN ('0000-00-00', DATE(NOW()))*/";

                #commented by VAN 04-28-08
                if(empty($list)) return false;
                if(!$this->internResolvePID($pid)) return false;

                        # burn added : July 26, 2007
                        $pid_format = " cp.pid='".$this->pid."' ";

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
                $this->sql="SELECT * FROM $this->tb_person WHERE pid=".$db->qstr($this->pid);
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
        
        function getZipCode() {
            global $db;
            
            $zipcode = '';
            $this->sql = "SELECT zipcode FROM seg_municity WHERE mun_nr = ".$this->getValue('mun_nr');
            $this->result = $db->Execute($this->sql);
            if ($this->result) {
                if ($this->result->RecordCount()) {
                    $row = $this->result->FetchRow();
                    $zipcode = $row['zipcode'];
                }
            }             
            return $zipcode;
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
                #    if (intval($this->pid))
                #        $pid_format = " (pid='$this->pid' OR pid=$this->pid) ";
                #    else
                                $pid_format = " pid='".$this->pid."' ";

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
                #if (intval($this->pid))
                #    $pid_format = " (pid='$this->pid' OR pid=$this->pid) ";
                #else
                        $pid_format = " pid='".$this->pid."' ";

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
                if(!$this->is_preloaded) $this->sql="SELECT name FROM $this->tb_citytown WHERE nr='$code_nr'";
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
                #if (intval($pid))
                #    $pid_format = " (pid='$this->pid' OR pid=$this->pid) ";
                #else
                        $pid_format = " pid='".$this->pid."' ";

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
                #if (intval($pid))
                #    $pid_format = " (pid='$pid' OR pid=$pid) ";
                #else
                        $pid_format = " pid='$pid' ";

                $this->sql="SELECT encounter_nr FROM $this->tb_enc
                                        WHERE $pid_format AND is_discharged=0
                                        AND encounter_status <> 'cancelled'
                                        AND status NOT IN ($this->dead_stat)
                                        ORDER BY encounter_date DESC";
                #echo "sql2 = ".$this->sql;
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
        #    if (intval($pid))
        #        $pid_format = " (pid='$pid' OR pid=$pid) ";
        #    else
                        $pid_format = " pid='$pid' ";

                $this->sql="SELECT encounter_nr,encounter_date,encounter_class_nr,encounter_type,is_discharged,discharge_date
                                         FROM $this->tb_enc WHERE $pid_format
                                         AND status NOT IN ($this->dead_stat)
                                         ORDER BY encounter_date DESC ";
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
        * @param string Sort direction, default = ASC (ascending)
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
        * @param string Sort direction, default = ASC (ascending)
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
                        #$suchwort=(int) $suchwort;
                        //$numeric=1;
                        $this->is_nr=TRUE;

                        //if($suchwort<$GLOBAL_CONFIG['person_id_nr_adder']){
                        //       $suchbuffer=(int) ($suchwort + $GLOBAL_CONFIG['person_id_nr_adder']) ;
                        //}

                        if(empty($oitem)) $oitem='pid';
                        if(empty($odir)) $odir='DESC'; # default, latest pid at top

                        $sql2="    WHERE pid='$suchwort' ";

                } else {
                        # Try to detect if searchkey is composite of first name + last name
                        if(stristr($searchkey,',')){
                                $lastnamefirst=TRUE;
                        }else{
                                $lastnamefirst=FALSE;
                        }

#            $searchkey=strtr($searchkey,',',' ');
#            $cbuffer=explode(' ',$searchkey);
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
                                #edited by VAN 06-14-2011
//                                $sql2=" WHERE (SOUNDEX(name_last) = SOUNDEX('".strtr($ln,'+',' ')."') AND SOUNDEX(name_first) = SOUNDEX('".strtr($fn,'+',' ')."'))";
                                #$sql2=" WHERE (soundex_namelast = SOUNDEX('".strtr($ln,'+',' ')."') AND soundex_namefirst = SOUNDEX('".strtr($fn,'+',' ')."'))";
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
                                                                #$sql2=" WHERE (name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%' OR name_first $sql_LIKE '".strtr($suchwort,'+',' ')."%')";
                                                                #edited by VAN 06-14-2011
//                                                                $sql2=" WHERE (SOUNDEX(name_last) = SOUNDEX('".strtr($suchwort,'+',' ')."') AND SOUNDEX(name_first) = SOUNDEX('".strtr($suchwort,'+',' ')."'))";
                                                                $sql2=" WHERE (soundex_namelast = SOUNDEX('".strtr($suchwort,'+',' ')."') AND soundex_namefirst = SOUNDEX('".strtr($suchwort,'+',' ')."'))";
                                                        }else{
                                                                #$sql2=" WHERE name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%' ";
                                                                #edited by VAN 06-14-2011
//                                                                $sql2=" WHERE SOUNDEX(name_last) = SOUNDEX('".strtr($suchwort,'+',' ')."') ";
                                                                $sql2=" WHERE soundex_namelast = SOUNDEX('".strtr($suchwort,'+',' ')."') ";
                                                        }
                                                }else{
                                                        #$sql2=" WHERE name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%' ";
                                                        #edited by VAN 06-14-2011
//                                                        $sql2=" WHERE SOUNDEX(name_last) = SOUNDEX('".strtr($suchwort,'+',' ')."') ";
                                                        $sql2=" WHERE soundex_namelast = SOUNDEX('".strtr($suchwort,'+',' ')."') ";
                                                }
                                        }else{
                                                $sql2=" WHERE date_birth = '$DOB'";
                                        }

                                        $sql2.=" AND status NOT IN ($this->dead_stat) ";
                                }
                        }
                 }

                $sql2 = " AS cp
                                 LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=cp.brgy_nr
                                 LEFT JOIN seg_municity AS sm ON sm.mun_nr=cp.mun_nr
                                 LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr
                                 LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr  ".$sql2;   # edited by VAN 12-11-2008

                $this->buffer=$this->tb_person.$sql2;

                # Save the query in buffer for pagination
                //$this->buffer=$fromwhere;
                //$sql2.=' AND status NOT IN ("void","hidden","deleted","inactive")  ORDER BY '.$oitem.' '.$odir;
                # Set the sorting directive
                if(isset($oitem)&&!empty($oitem)) $sql3 =" ORDER BY $oitem $odir";

#        $this->sql='SELECT pid, name_last, name_first, date_birth, addr_zip, sex, death_date, status FROM '.$this->buffer.$sql3;   # burn commented: March 8, 2007
                $this->sql= " SELECT pid, name_last, name_first,name_middle, date_birth, addr_zip, sex, death_date,death_encounter_nr, status ".
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
        * @param string Sort direction, default = ASC (ascending)
        * @return mixed integer or boolean
        */
        #edited by VAN 07-07-2010
        #optimize query and add personnel/dependent status
        function SearchSelect($searchkey='',$maxcount=100,$offset=0,$oitem='name_last',$odir='ASC',$fname=FALSE,$from='',$includeOldBabyNames=''){
                global $db, $sql_LIKE, $root_path, $HTTP_SESSION_VARS;
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

                #added by VAN 02-15-08
                if ($suchwort{0}=='T'){
                        $suchwort = str_replace('T','',$suchwort);
                        $isPid = 1;
                }

                if(is_numeric($suchwort)) {
                        $this->is_nr=TRUE;

                        if(empty($oitem)) $oitem='cp.pid';
                        if(empty($odir)) $odir='DESC'; # default, latest pid at top

                        if($isPid){
                                $sql2="    WHERE cp.pid='$searchkey' ";
                        }else{
                                $sql2="    WHERE cp.pid='$suchwort' ";
                        }


                } else {
                        # Try to detect if searchkey is composite of first name + last name
                        if(stristr($searchkey,',')){
                                $lastnamefirst=TRUE;
                        }else{
                                $lastnamefirst=FALSE;
                        }

                        if(stristr($searchkey, ',') === FALSE){
                                $cbuffer=explode(' ',$searchkey);
                                #$newsearchkey = $searchkey;
                                $lnameOnly = 1;
                                #$newquery = " OR name_last $sql_LIKE '".$newsearchkey."%'";
                        }else{
                                $cbuffer=explode(',',$searchkey);
                                $newquery = "";
                                $lnameOnly = 0;
                        }

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
                                $cntlast = sizeof($cbuffer)-1;
                                if (sizeof($cbuffer) > 2){
                                        if ($lnameOnly){
                                                #added code by angelo m. 09.28.2010
                                                #start------------------------------
                                                if($includeOldBabyNames==1){
                                                    $old_select = "spnh.hrn, spnh.old_fname, spnh.old_lname, spnh.old_mname, ";
                                                    $sqlOldWhere = " (cp.fromtemp=1 AND cp.pid=spnh.hrn) OR ";
                                                    $sqlOld = "LEFT JOIN seg_person_name_history AS spnh
                                                                        ON spnh.old_lname $sql_LIKE '".strtr($ln,'+',' ')."%'
                                                                        AND spnh.hrn=cp.pid
                                                                         ";
                                                }
                                                #end------------------------------
                                                $sql2=" WHERE ($sqlOldWhere(name_last $sql_LIKE '".$searchkey."%'))";
                                        }
                                        else{

                                                if($includeOldBabyNames==1){
                                                    $old_select = "spnh.hrn, spnh.old_fname, spnh.old_lname, spnh.old_mname, ";
                                                    $sqlOldWhere = " (cp.fromtemp=1 AND cp.pid=spnh.hrn) OR ";
                                                    $sqlOld = "LEFT JOIN seg_person_name_history AS spnh
                                                                        ON spnh.old_lname $sql_LIKE '".strtr($ln,'+',' ')."%'
                                                                         AND spnh.old_fname $sql_LIKE '".strtr($fn,'+',' ')."%'
                                                                         AND spnh.hrn=cp.pid
                                                                         ";
                                                }
                                                $sql2=" WHERE ($sqlOldWhere(name_last $sql_LIKE '".strtr($ln,'+',' ')."%' AND name_first $sql_LIKE '".strtr($fn,'+',' ')."%'))";

                                        }

                                        $bd=$comp[sizeof($cbuffer)];
                                }else
                                        if ($lnameOnly){
                                                if($includeOldBabyNames==1){
                                                    $old_select = "spnh.hrn, spnh.old_fname, spnh.old_lname, spnh.old_mname, ";
                                                    $sqlOldWhere = " (cp.fromtemp=1 AND cp.pid=spnh.hrn) OR ";
                                                    $sqlOld = "LEFT JOIN seg_person_name_history AS spnh
                                                                        ON spnh.old_lname $sql_LIKE '".strtr($ln,'+',' ')."%'
                                                                        AND spnh.hrn=cp.pid
                                                                         ";
                                                }
                                                $sql2=" WHERE ($sqlOldWhere(name_last $sql_LIKE '".$searchkey."%'))";
                                        }
                                        else{
                                                if($includeOldBabyNames==1){
                                                    $old_select = "spnh.hrn, spnh.old_fname, spnh.old_lname, spnh.old_mname, ";
                                                    $sqlOldWhere = " (cp.fromtemp=1 AND cp.pid=spnh.hrn) OR ";
                                                    $sqlOld = "LEFT JOIN seg_person_name_history AS spnh
                                                                        ON spnh.old_lname $sql_LIKE '".strtr($ln,'+',' ')."%'
                                                                         AND spnh.old_fname $sql_LIKE '".strtr($fn,'+',' ')."%'
                                                                         AND spnh.hrn=cp.pid
                                                                         ";
                                                }
                                                $sql2=" WHERE ($sqlOldWhere(name_last $sql_LIKE '".strtr($ln,'+',' ')."%' AND name_first $sql_LIKE '".strtr($fn,'+',' ')."%'))";


                                        }

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
                                        $sql2=" WHERE cp.status NOT IN ($this->dead_stat)";
                                }else{
                                        # Check if it is a complete DOB
                                        $DOB=@formatDate2STD($suchwort,$date_format);
                                        if($DOB=='') {
#                        if(defined('SHOW_FIRSTNAME_CONTROLLER')&&SHOW_FIRSTNAME_CONTROLLER){
                                                if(TRUE){
                                                        if($fname){
                                                                #$sql2=" WHERE (name_last $sql_LIKE '%".strtr($suchwort,'+',' ')."%' OR name_first $sql_LIKE '%".strtr($suchwort,'+',' ')."%') ";
                                                                if ($lnameOnly)
                                                                        $sql2=" WHERE (name_last $sql_LIKE '".$searchkey."%')";
                                                                else
                                                                        $sql2=" WHERE (name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%')";
                                                        }else{
                                                                if ($lnameOnly)
                                                                        $sql2=" WHERE (name_last $sql_LIKE '".$searchkey."%' )";
                                                                else
                                                                        $sql2=" WHERE (name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%' )";
                                                        }
                                                }else{
                                                        if ($lnameOnly)
                                                                $sql2=" WHERE (name_last $sql_LIKE '".$searchkey."%' )";
                                                        else
                                                                $sql2=" WHERE (name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%' )";
                                                }
                                        }else{
                                                $sql2=" WHERE date_birth = '$DOB' ";
                                        }

                                }
                        }
                 }

                /*if (empty($suchwort)){
                        #$sql_pay = " sc.*, pr.service_code, ps.nr AS personnelID , cp.date_reg, ";
                        $sql_pay = " sc.*, pr.service_code, ";
                        #edited by VAN 06-27-08
                        $sql_pay_join = " LEFT JOIN seg_pay AS sc ON sc.pid=cp.pid
                                                            LEFT JOIN seg_pay_request AS pr ON sc.or_no=pr.or_no
                                                            INNER JOIN seg_other_services AS so ON so.service_code=SUBSTRING(pr.service_code,1,length(pr.service_code)-1)";

                         #edited by VAN 06-27-08
                         $sqlA = " AND ((sc.cancel_date IS NULL
                                                         AND DATE(sc.or_date)=DATE(NOW())
                                                    AND pr.ref_source = 'OTHER'
                                                                    AND so.account_type='33')
                                                    OR (DATE(cp.date_reg)=DATE(NOW()) AND (cp.senior_ID!=''
                                                                OR scgp.discountid='D' OR d.parentid='D')))";

                }else{
                        $sql_pay  = " ";
                        $sql_pay_join = " ";
                }*/

                #if personnel or dependent
                $phs_cond = "";
                if ($from=='phs')
                        $phs_cond = " AND ((dep.dependent_pid IS NOT NULL) OR (ps.nr IS NOT NULL)) ";

                $sql_enc    =" AND enc.encounter_type <>'5' ";
                #added by angelo m. 09.14.2010 excluded dialysis

                $sql2 = " AS cp
                                        LEFT JOIN care_encounter AS enc ON enc.pid=cp.pid AND enc.is_discharged=0 AND enc.encounter_status <> 'cancelled' AND enc.status NOT IN ($this->dead_stat) $sql_enc
                                        LEFT JOIN seg_charity_grants_pid AS scp ON scp.pid=cp.pid AND scp.status='valid' AND scp.discountid NOT IN ('LINGAP')
                                        LEFT JOIN seg_charity_grants AS se ON se.encounter_nr=enc.encounter_nr AND se.status='valid' AND se.discountid NOT IN ('LINGAP')
                                        LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=cp.brgy_nr
                                        LEFT JOIN seg_municity AS sm ON sm.mun_nr=cp.mun_nr
                                        LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr
                                        LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr
                                        LEFT JOIN seg_radio_id AS rd ON rd.pid=cp.pid
                                        LEFT JOIN care_personell AS ps ON cp.pid=ps.pid
                                            AND ((date_exit NOT IN (DATE(NOW())) AND date_exit > DATE(NOW())) OR date_exit='0000-00-00' OR date_exit IS NULL)
                                            AND ((contract_end NOT IN (DATE(NOW())) AND contract_end > DATE(NOW()))
                                            OR contract_end='0000-00-00' OR contract_end IS NULL)

                                        LEFT JOIN seg_dependents AS dep ON dep.dependent_pid=cp.pid AND dep.status='member'

                                        $sqlOld
                                        ".
                                        $sql2.$sqlA." ".$phs_cond.
                                        "\nGROUP BY cp.pid\n";

                $this->buffer=$this->tb_person.$sql2;

                # Save the query in buffer for pagination


                # Set the sorting directive
                if(isset($oitem)&&!empty($oitem)) $sql3 =" ORDER BY name_last ASC, name_first ASC";
                #edited by VAN 04-16-08

                        //$this->sql= " SELECT SQL_CALC_FOUND_ROWS dep.dependent_pid, ps.nr AS personnelID, cp.civil_status, cp.senior_ID, cp.fromtemp, cp.pid,cp.name_last,cp.name_first,cp.name_middle,cp.date_birth,cp.addr_zip, cp.sex,cp.death_date,cp.death_encounter_nr, cp.status,cp.street_name,
                        #modified by cha, july 21, 2010 (*added cp.photo_filename)
                        $this->sql= " SELECT SQL_CALC_FOUND_ROWS rd.rid, cp.photo_filename,dep.dependent_pid, ps.nr AS personnelID, cp.civil_status, cp.senior_ID, cp.fromtemp, cp.pid, $old_select cp.name_last,cp.name_first,cp.name_middle,cp.date_birth,cp.addr_zip, cp.sex,cp.death_date,cp.death_encounter_nr, cp.status,cp.street_name,
                                                        sb.brgy_name, sm.zipcode, sm.mun_name, sp.prov_name, sr.region_name,IF(fn_calculate_age(NOW(),cp.date_birth),fn_get_age(NOW(),cp.date_birth),age) AS age, cp.sex,
                                                        SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.encounter_nr)),20) AS encounter_nr,
                                                        SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.er_opd_diagnosis)),20) AS er_opd_diagnosis,
                                                        SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.encounter_type)),20) AS encounter_type,
                                                        SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.current_ward_nr)),20) AS current_ward_nr,
                                                        SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.current_room_nr)),20) AS current_room_nr,
                                                        SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.current_dept_nr)),20) AS current_dept_nr,
                                                        SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.current_att_dr_nr)),20) AS current_att_dr_nr,
                                                        SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.er_opd_diagnosis)),20) AS er_opd_diagnosis,
                                                        SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.chief_complaint)),20) AS chief_complaint,
                                                        SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.is_medico)),20) AS is_medico,
                                                        SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.admission_dt)),20) AS admission_dt,
                                                        SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.discharge_date)),20) AS discharge_date,

                                                        IF(((dep.dependent_pid IS NOT NULL) OR (ps.nr IS NOT NULL)),
                                                                 IF(SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.encounter_nr)),20),
                                                                        'PHS',
                                                                        ''
                                                                 ),
                                                                 IF(SUBSTRING(MAX(CONCAT(scp.grant_dte,scp.discountid)),20)='SC',
                                                                        IF(SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.encounter_nr)),20),
                                                                             'SC',
                                                                             'SC'
                                                                        ),
                                                                        IF(SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.encounter_nr)),20) IS NULL,
                                                                             '',
                                                                             IF(SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.encounter_type)),20)=2,
                                                                                    SUBSTRING(MAX(CONCAT(scp.grant_dte,scp.discountid)),20),
                                                                                    SUBSTRING(MAX(CONCAT(se.grant_dte,se.discountid)),20)
                                                                             )
                                                                        )
                                                                 )
                                                        ) AS discountid,

                                                        IF(((dep.dependent_pid IS NOT NULL) OR (ps.nr IS NOT NULL)),
                                                                 IF(SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.encounter_nr)),20),
                                                                        '1',
                                                                        ''
                                                                 ),
                                                                 IF(SUBSTRING(MAX(CONCAT(scp.grant_dte,scp.discountid)),20)='SC',
                                                                        IF(SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.encounter_nr)),20),
                                                                             '1',
                                                                             '0.20'
                                                                        ),
                                                                        IF(SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.encounter_nr)),20) IS NULL,
                                                                             '',
                                                                             IF(SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.encounter_type)),20)=2,
                                                                                    SUBSTRING(MAX(CONCAT(scp.grant_dte,scp.discount)),20),
                                                                                    SUBSTRING(MAX(CONCAT(se.grant_dte,se.discount)),20)
                                                                             )
                                                                        )
                                                                 )
                                                        ) AS discount, ".
                //added by cha, august 17, 2010
                #"fn_get_phic_number(enc.encounter_nr) AS `phic_nr` \n".
                #edited by VAN 03-28-2012
                "fn_get_phic_number(SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.encounter_nr)),20)) AS `phic_nr` \n".
                //end cha
                                                " FROM ".$this->buffer.$sql3;
     #echo $this->sql;
                if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset)){
                        $this->rec_count=$this->res['ssl']->RecordCount();
                        #echo "count = ".$this->rec_count;
                        return $this->res['ssl'];
                }else{return false;}
        }

        #-----------added by VAN 06-20-08
        function CompSearchSelect($searchkey='',$maxcount=100,$offset=0,$oitem='name_last',$odir='ASC',$fname=FALSE, $enctype=''){
                global $db, $sql_LIKE, $root_path, $HTTP_SESSION_VARS;

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

                #added by VAN 02-15-08
                if ($suchwort{0}=='T'){
                        $suchwort = str_replace('T','',$suchwort);
                        $isPid = 1;
                }

                if(is_numeric($suchwort)) {
                        #$suchwort=(int) $suchwort;
                        //$numeric=1;
                        $this->is_nr=TRUE;

                        if(empty($oitem)) $oitem='cp.pid';
                        if(empty($odir)) $odir='DESC'; # default, latest pid at top

                        if($isPid){
                                $sql2="    AND (cp.pid='$searchkey'/* OR ((SELECT encounter_nr FROM care_encounter AS enc WHERE cp.pid=enc.pid AND enc.encounter_status <> 'cancelled' AND enc.status NOT IN ('deleted','hidden','inactive','void') ORDER BY enc.encounter_nr DESC LIMIT 1) = '$searchkey')*/)";
                        }else{
                                $sql2="    AND (cp.pid='$suchwort' /*OR ((SELECT encounter_nr FROM care_encounter AS enc WHERE cp.pid=enc.pid AND enc.encounter_status <> 'cancelled' AND enc.status NOT IN ('deleted','hidden','inactive','void') ORDER BY enc.encounter_nr DESC LIMIT 1) = '$searchkey')*/)";
                        }


                } else {
                        # Try to detect if searchkey is composite of first name + last name
                        if(stristr($searchkey,',')){
                                $lastnamefirst=TRUE;
                        }else{
                                $lastnamefirst=FALSE;
                        }

                        if(stristr($searchkey, ',') === FALSE){
                                $cbuffer=explode(' ',$searchkey);
                                #$newsearchkey = $searchkey;
                                $lnameOnly = 1;
                                #$newquery = " OR name_last $sql_LIKE '".$newsearchkey."%'";
                        }else{
                                $cbuffer=explode(',',$searchkey);
                                $newquery = "";
                                $lnameOnly = 0;
                        }

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
                                $cntlast = sizeof($cbuffer)-1;
                                if (sizeof($cbuffer) > 2){
                                        #$sql2=" AND (((name_last $sql_LIKE '%".strtr($ln,'+',' ')."%' OR name_last $sql_LIKE '".strtr($comp[$cntlast],'+',' ')."%') AND name_first $sql_LIKE '%".strtr($fn,'+',' ')."%') OR (name_last $sql_LIKE '".$searchkey."%' OR name_first $sql_LIKE '".$searchkey."%'))";
                                        if ($lnameOnly)
                                                $sql2=" AND (name_last $sql_LIKE '".$searchkey."%')";
                                        else
                                                $sql2=" AND (name_last $sql_LIKE '".strtr($ln,'+',' ')."%' AND name_first $sql_LIKE '".strtr($fn,'+',' ')."%'))";
                                        $bd=$comp[sizeof($cbuffer)];
                                }else
                                        #$sql2=" AND ((name_last $sql_LIKE '%".strtr($ln,'+',' ')."%' AND name_first $sql_LIKE '%".strtr($fn,'+',' ')."%') OR (name_last $sql_LIKE '%".$searchkey."%' OR name_first $sql_LIKE '%".$searchkey."%'))";
                                        if ($lnameOnly)
                                                $sql2=" AND (name_last $sql_LIKE '".$searchkey."%')";
                                        else
                                                $sql2=" AND ((name_last $sql_LIKE '".strtr($ln,'+',' ')."%' AND name_first $sql_LIKE '".strtr($fn,'+',' ')."%'))";

                                if(!empty($bd)){
                                        $DOB=@formatDate2STD($bd,$date_format);
                                        if($DOB=='') {
                                                #$sql2.=" AND date_birth $sql_LIKE '$bd%' ";
                                                $sql2.=" AND (DATE(encounter_date) = '$DOB')";
                                        }else{
                                                #$sql2.=" AND date_birth = '$DOB' ";
                                                $sql2.=" AND (DATE(encounter_date) = '$DOB')";
                                        }
                                }
                        }else{
                        #die('here');
                                # Check if * or %
                                if($suchwort=='%'||$suchwort=='%%'){

                                        $sql2 = "";
                                }else{

                                        # Check if it is a complete DOB
                                        $DOB=@formatDate2STD($suchwort,$date_format);
                                        if($DOB=='') {
#                        if(defined('SHOW_FIRSTNAME_CONTROLLER')&&SHOW_FIRSTNAME_CONTROLLER){
                                                if(TRUE){
                                                        if($fname){
                                                                #$sql2=" AND (name_last $sql_LIKE '%".strtr($suchwort,'+',' ')."%' OR name_first $sql_LIKE '%".strtr($suchwort,'+',' ')."%') ";
                                                                if ($lnameOnly)
                                                                        $sql2=" AND (name_last $sql_LIKE '".$searchkey."%') ";
                                                                else
                                                                        $sql2=" AND (name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%') ";
                                                        }else{
                                                                #$sql2=" AND name_last $sql_LIKE '%".strtr($suchwort,'+',' ')."%' ";
                                                                if ($lnameOnly)
                                                                        $sql2=" AND (name_last $sql_LIKE '".$searchkey."%') ";
                                                                else
                                                                        $sql2=" AND name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%' ";
                                                        }
                                                }else{
                                                        #$sql2=" AND name_last $sql_LIKE '%".strtr($suchwort,'+',' ')."%' ";
                                                        if ($lnameOnly)
                                                                $sql2=" AND (name_last $sql_LIKE '".$searchkey."%') ";
                                                        else
                                                                $sql2=" AND name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%' ";
                                                }
                                        }else{

                                                #$sql2=" AND date_birth = '$DOB' ";
                                                $sql2=" AND (date_birth = '$DOB' OR DATE(encounter_date) = '$DOB')";
                                        }
                                }
                        }
                 }

                if (empty($suchwort)){
                        #current date
                        #$sql_now = " AND ((DATE((SELECT encounter_date FROM care_encounter AS enc WHERE cp.pid=enc.pid AND enc.is_discharged=0 AND enc.encounter_status <> 'cancelled' AND enc.status NOT IN ('deleted','hidden','inactive','void') ORDER BY enc.encounter_nr DESC LIMIT 1)) = DATE(NOW()))
                        #             OR (DATE((SELECT admission_dt FROM care_encounter AS enc WHERE cp.pid=enc.pid AND enc.is_discharged=0 AND enc.encounter_status <> 'cancelled' AND enc.status NOT IN ('deleted','hidden','inactive','void') ORDER BY enc.encounter_nr DESC LIMIT 1)) = DATE(NOW())))";
                        $sql_now = "AND encounter_type IN (0)";
                }else{
                        #base on the search key
                }

                # Save the query in buffer for pagination
                #added by VAN 06-23-08
                if ($enctype==1){
                        #$enctype_cond = "AND (SELECT encounter_type FROM care_encounter AS enc WHERE cp.pid=enc.pid AND enc.encounter_status <> 'cancelled' AND enc.status NOT IN ('deleted','hidden','inactive','void') ORDER BY enc.encounter_nr DESC LIMIT 1) IN (1)
                        #                 AND (SELECT is_discharged FROM care_encounter AS enc WHERE cp.pid=enc.pid AND enc.encounter_status <> 'cancelled' AND enc.status NOT IN ('deleted','hidden','inactive','void') ORDER BY enc.encounter_nr DESC LIMIT 1)=0";
                        $enctype_cond = " AND encounter_type IN (1) /*AND is_discharged=0*/";
                }elseif ($enctype==2){
                        #$enctype_cond = "AND (SELECT encounter_type FROM care_encounter AS enc WHERE cp.pid=enc.pid AND enc.encounter_status <> 'cancelled' AND enc.status NOT IN ('deleted','hidden','inactive','void') ORDER BY enc.encounter_nr DESC LIMIT 1) IN (2)
                        #                AND (SELECT is_discharged FROM care_encounter AS enc WHERE cp.pid=enc.pid AND enc.encounter_status <> 'cancelled' AND enc.status NOT IN ('deleted','hidden','inactive','void') ORDER BY enc.encounter_nr DESC LIMIT 1)=0";
                        $enctype_cond = " AND encounter_type IN (2) /*AND is_discharged=0*/";
                }else if ($enctype==3){
                        #$enctype_cond = "AND (SELECT encounter_type FROM care_encounter AS enc WHERE cp.pid=enc.pid AND enc.encounter_status <> 'cancelled' AND enc.status NOT IN ('deleted','hidden','inactive','void') ORDER BY enc.encounter_nr DESC LIMIT 1) IN (3,4)
                        #                AND (SELECT is_discharged FROM care_encounter AS enc WHERE cp.pid=enc.pid AND enc.encounter_status <> 'cancelled' AND enc.status NOT IN ('deleted','hidden','inactive','void') ORDER BY enc.encounter_nr DESC LIMIT 1)=0";
                        $enctype_cond = " AND encounter_type IN (3,4) /*AND is_discharged=0*/";
                }else{
                        $enctype_cond = "";
                }

                $this->buffer = "care_person AS cp
                                                 INNER JOIN care_encounter AS enc ON enc.pid=cp.pid  $enctype_cond
                                                            AND enc.status NOT IN ($this->dead_stat)
                                                            AND enc.encounter_status <> 'cancelled'
                                                 WHERE cp.status NOT IN ($this->dead_stat)

                                                 AND cp.pid=enc.pid
                                                 $sql2
                                                 /*GROUP BY enc.pid*/
                                                 GROUP BY enc.encounter_nr
                                                 ORDER BY encounter_date DESC";


                # Set the sorting directive
                if(isset($oitem)&&!empty($oitem)) $sql3 =" ORDER BY $oitem $odir";

                        if (empty($suchwort)){
                                $this->sql= "SELECT
                                                cp.pid,cp.name_last,cp.name_first,cp.date_birth,
                                                cp.sex,cp.death_date, cp.death_encounter_nr
                                                FROM ".$this->buffer;
                        }else{
                            $this->sql= "SELECT SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.encounter_nr)),20) AS encounter_nr,
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
                                                        FROM ".$this->buffer;
                                }
                #echo $this->sql;
                if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset)){
                #die( "<br>sql = ".$this->sql);
                        $this->rec_count=$this->res['ssl']->RecordCount();
                        return $this->res['ssl'];
                }else{return false;}
        }
        #----------------------------------

        #edited by VAN 07-07-2010
        #optimize query and add personnel/dependent status
        function SearchSelectWithCurrentEncounter($searchkey='',$maxcount=100,$offset=0,$oitem='name_last',$odir='ASC',$fname=FALSE){
                global $db, $sql_LIKE, $root_path;

                if(empty($maxcount)) $maxcount=100;
                if(empty($offset)) $offset=0;

                include_once($root_path.'include/inc_date_format_functions.php');

                $searchkey = $db->qstr($searchkey);
                $searchkey = substr($searchkey, 1, strlen($searchkey)-2);

                # convert * and ? to % and &
                $searchkey=strtr($searchkey,'*?','%_');
                $searchkey=trim($searchkey);
                $suchwort=$searchkey;

                if(is_numeric($suchwort)) {
                        $this->is_nr=TRUE;

                        if(empty($oitem)) $oitem='tbperson.pid';
                        if(empty($odir)) $odir='DESC'; # default, latest pid at top

                        $sql2="    WHERE tbperson.pid='$suchwort' ";

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
                #$sql2    .=" AND tbenc.pid=tbperson.pid AND tbenc.is_discharged=0 AND tbenc.encounter_status <> 'cancelled' AND tbenc.status NOT IN ($this->dead_stat)";
                #added by cha, june 1,2010
                if($exlude_mgh)
                {
                     $sql_enc    =" AND tbenc.encounter_type IN (1,2,3,4,6) AND tbenc.is_discharged=0 AND tbenc.encounter_status <> 'cancelled' AND tbenc.status NOT IN ($this->dead_stat) AND (death_date in (null,'0000-00-00','')) ";
                }
                else{
                    #edited by VAN 03-04-08
                    $sql_enc    =" AND tbenc.encounter_type IN (1,2,3,4,6) AND tbenc.is_discharged=0 AND tbenc.is_maygohome=0 AND tbenc.encounter_status <> 'cancelled' AND tbenc.status NOT IN ($this->dead_stat) AND (death_date in (null,'0000-00-00','')) ";
                }
                #end cha
                $this->buffer=$this->tb_person.$sql2;

                # Save the query in buffer for pagination

                # Set the sorting directive

                $sql3 = "\nGROUP BY tbperson.pid\n";

                if(isset($oitem)&&!empty($oitem)) $sql3 .= "ORDER BY $oitem $odir";
                #modified by VAN 07-07-2010, the whole query
                $this->sql="SELECT SQL_CALC_FOUND_ROWS
                                                        rd.rid, tbperson.pid,
                                                        SUBSTRING(MAX(CONCAT(tbenc.encounter_date,tbenc.encounter_nr)),20) AS encounter_nr,
                                                        SUBSTRING(MAX(CONCAT(tbenc.encounter_date,tbenc.admission_dt)),20) AS admission_dt,
                                                        SUBSTRING(MAX(CONCAT(tbenc.encounter_date,tbenc.encounter_date)),20) AS encounter_date,
                                                        SUBSTRING(MAX(CONCAT(tbenc.encounter_date,tbenc.discharge_date)),20) AS discharge_date,
                                                        SUBSTRING(MAX(CONCAT(tbenc.encounter_date,tbenc.current_ward_nr)),20) AS current_ward_nr,
                                                        SUBSTRING(MAX(CONCAT(tbenc.encounter_date,tbenc.current_room_nr)),20) AS current_room_nr,
                                                        SUBSTRING(MAX(CONCAT(tbenc.encounter_date,tbenc.current_dept_nr)),20) AS current_dept_nr,
                                                        SUBSTRING(MAX(CONCAT(tbenc.encounter_date,tbenc.current_att_dr_nr)),20) AS current_att_dr_nr,
                                                        SUBSTRING(MAX(CONCAT(tbenc.encounter_date,tbenc.er_opd_diagnosis)),20) AS er_opd_diagnosis,
                                                        SUBSTRING(MAX(CONCAT(tbenc.encounter_date,tbenc.chief_complaint)),20) AS chief_complaint,
                                                        SUBSTRING(MAX(CONCAT(tbenc.encounter_date,tbenc.encounter_type)),20) AS encounter_type,
                                                        SUBSTRING(MAX(CONCAT(tbenc.encounter_date,tbenc.is_medico)),20) AS is_medico,

                                                        tbperson.name_last,
                                                        tbperson.name_first, tbperson.senior_ID,
                                                        tbperson.date_birth,
                                                        tbperson.sex, tbperson.death_date, tbperson.death_encounter_nr, tbperson.street_name,
                                                        sb.brgy_name, sm.zipcode, sm.mun_name, sp.prov_name, sr.region_name, tbperson.age, tbperson.status,

                                                        IF(((dep.dependent_pid IS NOT NULL) OR (ps.nr IS NOT NULL)),
                                                                 IF(SUBSTRING(MAX(CONCAT(tbenc.encounter_date,tbenc.encounter_nr)),20),
                                                                        'PHS',
                                                                        ''
                                                                 ),
                                                                 IF(SUBSTRING(MAX(CONCAT(scp.grant_dte,scp.discountid)),20)='SC',
                                                                        IF(SUBSTRING(MAX(CONCAT(tbenc.encounter_date,tbenc.encounter_nr)),20),
                                                                             'SC',
                                                                             'SC'
                                                                        ),
                                                                        IF(SUBSTRING(MAX(CONCAT(tbenc.encounter_date,tbenc.encounter_nr)),20) IS NULL,
                                                                             '',
                                                                             IF(SUBSTRING(MAX(CONCAT(tbenc.encounter_date,tbenc.encounter_type)),20)=2,
                                                                                    SUBSTRING(MAX(CONCAT(scp.grant_dte,scp.discountid)),20),
                                                                                    SUBSTRING(MAX(CONCAT(se.grant_dte,se.discountid)),20)
                                                                             )
                                                                        )
                                                                 )
                                                        ) AS discountid,

                                                        IF(((dep.dependent_pid IS NOT NULL) OR (ps.nr IS NOT NULL)),
                                                                 IF(SUBSTRING(MAX(CONCAT(tbenc.encounter_date,tbenc.encounter_nr)),20),
                                                                        '1',
                                                                        ''
                                                                 ),
                                                                 IF(SUBSTRING(MAX(CONCAT(scp.grant_dte,scp.discountid)),20)='SC',
                                                                        IF(SUBSTRING(MAX(CONCAT(tbenc.encounter_date,tbenc.encounter_nr)),20),
                                                                             '1',
                                                                             '0.20'
                                                                        ),
                                                                        IF(SUBSTRING(MAX(CONCAT(tbenc.encounter_date,tbenc.encounter_nr)),20) IS NULL,
                                                                             '',
                                                                             IF(SUBSTRING(MAX(CONCAT(tbenc.encounter_date,tbenc.encounter_type)),20)=2,
                                                                                    SUBSTRING(MAX(CONCAT(scp.grant_dte,scp.discount)),20),
                                                                                    SUBSTRING(MAX(CONCAT(se.grant_dte,se.discount)),20)
                                                                             )
                                                                        )
                                                                 )
                                                        ) AS discount, \n".
                                //added by cha, august 17, 2010
                                #"fn_get_phic_number(tbenc.encounter_nr) AS `phic_nr` \n".
                                #edited by VAN 03-28-2012
                                "fn_get_phic_number(SUBSTRING(MAX(CONCAT(tbenc.encounter_date,tbenc.encounter_nr)),20)) AS `phic_nr` \n".
                                //end cha
                                                "FROM care_person AS tbperson
                                                LEFT JOIN care_encounter AS tbenc ON tbenc.pid=tbperson.pid

                                                LEFT JOIN seg_charity_grants_pid AS scp ON scp.pid=tbperson.pid AND scp.status='valid' AND scp.discountid NOT IN ('LINGAP')
                                                LEFT JOIN seg_charity_grants AS se ON se.encounter_nr=tbenc.encounter_nr AND se.status='valid' AND se.discountid NOT IN ('LINGAP')

                                                LEFT JOIN care_personell AS ps ON tbperson.pid=ps.pid
                                                    AND ((date_exit NOT IN (DATE(NOW())) AND date_exit > DATE(NOW())) OR date_exit='0000-00-00' OR date_exit IS NULL)
                                                    AND ((contract_end NOT IN (DATE(NOW())) AND contract_end > DATE(NOW()))
                                                    OR contract_end='0000-00-00' OR contract_end IS NULL)
                                                LEFT JOIN seg_dependents AS dep ON dep.dependent_pid=tbperson.pid AND dep.status='valid'

                                                LEFT JOIN seg_radio_id AS rd ON rd.pid=tbperson.pid
                                                LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=tbperson.brgy_nr
                                                LEFT JOIN seg_municity AS sm ON sm.mun_nr=tbperson.mun_nr
                                                LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr
                                                LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr ".$sql2." ".$sql_enc." ".$sql3;

                if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset)){
                        $this->rec_count=$this->res['ssl']->RecordCount();
                        return $this->res['ssl'];
                }else{return false;}

        }

function SearchForBilling($searchkey='',$maxcount=100,$offset=0,$oitem='name_last',$odir='ASC',$fname=FALSE, $senc_nr=''){
                global $db, $sql_LIKE, $root_path;

                if(empty($maxcount)) $maxcount=100;
                if(empty($offset)) $offset=0;

                include_once($root_path.'include/inc_date_format_functions.php');

                $searchkey = $db->qstr($searchkey);
                $searchkey = substr($searchkey, 1, strlen($searchkey)-2);

                # convert * and ? to % and &
                $searchkey=strtr($searchkey,'*?','%_');
                $searchkey=trim($searchkey);
                $suchwort=$searchkey;

                if(is_numeric($suchwort)) {
                        #$suchwort=(int) $suchwort;
                        $this->is_nr=TRUE;

                        if(empty($oitem)) $oitem='tbperson.pid';
                        if(empty($odir)) $odir='DESC'; # default, latest pid at top

                        $sql2="    WHERE tbperson.pid='$suchwort' ";

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

                 // Modified filter to allow billing of dead patients. Modified 11.27.2008 (LST)
                #$sql2    .=" AND tbenc.is_discharged=0 AND tbenc.encounter_status <> 'cancelled' AND tbenc.status NOT IN ($this->dead_stat) AND (death_date in (null,'0000-00-00','')) ";
                $sql2 .=" AND ((tbenc.is_discharged=0 AND death_date in (null,'0000-00-00','')) OR (tbenc.is_discharged = 0 AND death_date > '0000-00-00') OR (tbenc.is_discharged=1 AND death_date >= '0000-00-00')) AND tbenc.encounter_type IN (1,2,3,4) AND tbenc.encounter_status <> 'cancelled' AND tbenc.status NOT IN ($this->dead_stat) ";

             # $sql2 .=" AND ((tbenc.is_discharged=0) OR (tbenc.is_discharged=1) AND tbenc.encounter_status <> 'cancelled' AND tbenc.status NOT IN ($this->dead_stat) ";

#----------- added by LST 06-26-2008 to allow search using encounter_nr --- SOB ---
                if (strcmp($senc_nr, "") != 0) {
                        $sql2 .= " AND tbenc.encounter_nr LIKE '".$senc_nr."%'";
                }
#----------- added by LST 06-26-2008 to allow search using encounter_nr ---- EOB ----

                $sql2 .= " AND tbenc.encounter_type IN (1,2,3,4) ";

                $this->buffer=$this->tb_person.$sql2;

                # Set the sorting directive
                $sql3 = "\nGROUP BY tbperson.pid\n";
                if(isset($oitem)&&!empty($oitem)) $sql3 .= "ORDER BY $oitem $odir";

                     #edited by VAN 07-08-09
                     $this->sql="SELECT SQL_CALC_FOUND_ROWS tbperson.pid,
                        SUBSTRING(MAX(CONCAT(tbenc.encounter_date,tbenc.encounter_nr)),20) AS encounter_nr, tbperson.name_last, tbperson.name_first, tbperson.name_middle, ".
                        "tbperson.date_birth, tbperson.sex, MAX(tbenc.encounter_date) AS er_opd_datetime, ".
                        "CONCAT(MAX(IF(admission_dt is null or admission_dt = '', date_format(encounter_date, '%b %e, %Y %l:%i%p'), date_format(admission_dt, '%b %e, %Y %l:%i%p'))), ' to present') as confine_period, ".
                        "/* fn_getconfinetypedesc( SUBSTRING(MAX(CONCAT(tbenc.encounter_date,tbenc.encounter_nr)),20) ) */ '' as confine_type, ".
                        "fn_get_discount_name(SUBSTRING(MAX(CONCAT(scg.grant_dte,scg.discountid)),20)) as class_desc,  ".
                        "/* fn_isPHIC( SUBSTRING(MAX(CONCAT(tbenc.encounter_date,tbenc.encounter_nr)),20) ) */ '' as is_phic, ".
                        "tbperson.death_date, tbperson.death_encounter_nr,tbperson.street_name, sb.brgy_name, sm.zipcode, sm.mun_name, sp.prov_name, sr.region_name, ".   # burn added : September 11, 2007
                        "SUBSTRING(MAX(CONCAT(scg.grant_dte,scg.discountid)),20) AS discountid,\n".
                        "SUBSTRING(MAX(CONCAT(scg.grant_dte,scg.discount)),20) AS discount,\n".
                        "tbperson.status, SUBSTRING(MAX(CONCAT(tbenc.encounter_date,tbenc.encounter_type)),20) AS encounter_type \n".
                        " FROM care_person AS tbperson
                            INNER JOIN care_encounter AS tbenc ON tbenc.pid = tbperson.pid
                            AND tbenc.encounter_type NOT IN (".DIALYSIS_ENCOUNTER_TYPE.",".IC_ENCOUNTER_TYPE.") AND tbenc.encounter_status <> 'cancelled' AND tbenc.status NOT IN ('deleted','hidden','inactive','void')\n".
                        "/* LEFT JOIN seg_charity_grants_pid AS scg ON scg.pid=tbenc.pid\n */".
                        "LEFT JOIN seg_charity_grants scg ON scg.encounter_nr = tbenc.encounter_nr\n".
                        "LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=tbperson.brgy_nr\n".
                        "LEFT JOIN seg_municity AS sm ON sm.mun_nr=tbperson.mun_nr\n".
                        "LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr\n".
                        "LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr\n".
                        $sql2.' '.$sql3;

                if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset)){
                        $this->rec_count=$this->res['ssl']->RecordCount();
                        return $this->res['ssl'];
                }else{
                        return false;
                }
        }

        /**
        * @internal     SearchEncountersForBilling - searches all encounters of a particular person given pid.
        * @access       public
        * @author       Bong S. Trazo
        * @package      include
        * @subpackage   care_api_classes
        * @global       db - database object
        *
        * @param        $pid,
        * @return       rows from query.
        */
        function SearchEncountersForBilling($pid, $maxcount=100, $offset=0) {
                global $db, $sql_LIKE, $root_path;

                if(empty($maxcount)) $maxcount=100;
                if(empty($offset)) $offset=0;

                include_once($root_path.'include/inc_date_format_functions.php');

//        $searchkey = $db->qstr($searchkey);
//        $searchkey = substr($searchkey, 1, strlen($searchkey)-2);

                # convert * and ? to % and &
//        $searchkey=strtr($searchkey,'*?','%_');
//        $searchkey=trim($searchkey);
                $suchwort=$pid;

                if(is_numeric($suchwort)) {
                        #$suchwort=(int) $suchwort;
                        $this->is_nr=TRUE;

                        if(empty($oitem)) $oitem='tbperson.pid';
                        if(empty($odir)) $odir='DESC'; # default, latest pid at top

                        $sql2="    WHERE tbperson.pid='$suchwort' ";
                }

                 // Modified filter to allow billing of dead patients. Modified 11.27.2008 (LST)
                $sql2 .=" AND ((tbenc.is_discharged=0 AND death_date in (null,'0000-00-00','')) OR (tbenc.is_discharged = 0 AND death_date > '0000-00-00') OR (tbenc.is_discharged=1 AND death_date >= '0000-00-00')) AND tbenc.encounter_type IN (1,2,3,4) AND tbenc.encounter_status <> 'cancelled' AND tbenc.status NOT IN ($this->dead_stat) ";

                $this->buffer=$this->tb_person.$sql2;

                # Set the sorting directive
                $sql3 = "\nGROUP BY encounter_nr\n";
                $sql3 .= "ORDER BY tbenc.encounter_date DESC";

                $this->sql="SELECT SQL_CALC_FOUND_ROWS tbperson.pid, sri.rid, tbenc.encounter_nr, admission_dt, tbperson.name_last, tbperson.name_first, tbperson.name_middle, ".
                                "tbperson.date_birth, tbperson.sex, tbenc.encounter_date AS er_opd_datetime, tbenc.discharge_date, tbenc.discharge_time, ".
                                "(select concat(date_format((case when admission_dt is null or admission_dt = '' then encounter_date else admission_dt end), '%b %e, %Y %l:%i%p'), ' to ', (case when tbenc.discharge_date is null or tbenc.discharge_date = '' then 'present' else date_format(str_to_date(concat(date_format(tbenc.discharge_date, '%Y-%m-%d'), ' ', date_format(tbenc.discharge_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s'), '%b %e, %Y %l:%i%p') end)) as prd
                                        from care_encounter as ce1 where ce1.encounter_nr = tbenc.encounter_nr) as confine_period, ".
                                "(select confinetypedesc from seg_type_confinement as stc inner join seg_encounter_confinement as sec on stc.confinetype_id = sec.confinetype_id ".
                                "   where encounter_nr = tbenc.encounter_nr order by classify_dte desc limit 1) as confine_type, ".
                                "(select discountdesc from seg_charity_grants_pid as scg2 inner join seg_discount as sd on scg2.discountid = sd.discountid ".
                                "   where scg2.pid = tbenc.pid order by grant_dte desc limit 1) as class_desc, ".
                                "(select 'PHIC' as c_desc from seg_encounter_insurance as sei ".
                                "   where encounter_nr = tbenc.encounter_nr and ".
                                "      exists (select * from care_insurance_firm as cif where (firm_id like '%PHILHEALTH%' or firm_id like '%PHIC%') ".
                                "      and cif.hcare_id = sei.hcare_id)) as is_phic, ".
                                "tbperson.death_date, tbperson.death_encounter_nr,tbperson.street_name, sb.brgy_name, sm.zipcode, sm.mun_name, sp.prov_name, sr.region_name, ".   # burn added : September 11, 2007
                                "SUBSTRING(MAX(CONCAT(scg.grant_dte,scg.discountid)),20) AS discountid,\n".
                                "SUBSTRING(MAX(CONCAT(scg.grant_dte,scg.discount)),20) AS discount,\n".
                                "tbperson.status, tbenc.encounter_type, sbe.bill_dte, sbe.bill_nr \n".
                                " FROM care_person AS tbperson
                                    LEFT JOIN (care_encounter AS tbenc LEFT JOIN seg_billing_encounter sbe ON tbenc.encounter_nr = sbe.encounter_nr) ON tbenc.pid = tbperson.pid \n".
                                " LEFT JOIN seg_radio_id AS sri ON sri.pid=tbperson.pid\n".
                                " LEFT JOIN seg_charity_grants_pid AS scg ON scg.pid=tbenc.pid\n".
                                " LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=tbperson.brgy_nr\n".
                                " LEFT JOIN seg_municity AS sm ON sm.mun_nr=tbperson.mun_nr\n".
                                " LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr\n".
                                " LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr\n".
                                $sql2.' '.$sql3;

                if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset)){
                        $this->rec_count=$this->res['ssl']->RecordCount();
                        return $this->res['ssl'];
                }else{
                        return false;
                }
        }

        function countSearchSelectWithCurrentEncounter($searchkey='',$maxcount=100,$offset=0,$oitem='name_last',$odir='ASC',$fname=FALSE, $senc_nr = ''){
                global $db, $sql_LIKE, $root_path;
                if(empty($maxcount)) $maxcount=100;
                if(empty($offset)) $offset=0;
                include_once($root_path.'include/inc_date_format_functions.php');

                # convert * and ? to % and &
                $searchkey=strtr($searchkey,'*?','%_');
                $searchkey=trim($searchkey);
                $suchwort=$searchkey;

                if(is_numeric($suchwort)) {
                        #$suchwort=(int) $suchwort;
                        $this->is_nr=TRUE;
                        if(empty($oitem)) $oitem='tbperson.pid';
                        if(empty($odir)) $odir='DESC'; # default, latest pid at top

                        $sql2="    WHERE tbperson.pid='$suchwort' ";

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
                $sql2    .=" AND tbenc.pid=tbperson.pid AND tbenc.is_discharged=0 AND tbenc.encounter_status <> 'cancelled' AND tbenc.status NOT IN ($this->dead_stat)";

#----------------- added by LST 06-26-2008 to allow search using encounter_nr ---- SOB ----
                if (strcmp($senc_nr, "") != 0) {
                        $sql2 .= " AND tbenc.encounter_nr REGEXP ".$db->qstr($senc_nr);
                }
#----------------- added by LST 06-26-2008 to allow search using encounter_nr ---- EOB ----

                $this->buffer=$this->tb_person.$sql2;
                $this->sql='SELECT COUNT(*) FROM '.
                        $this->tb_person.' AS tbperson,'.
                        $this->tb_enc.' AS tbenc '.$sql2;

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
                        #$suchwort=(int) $suchwort;
                        //$numeric=1;
                        $this->is_nr=TRUE;

                        //if($suchwort<$GLOBAL_CONFIG['person_id_nr_adder']){
                        //       $suchbuffer=(int) ($suchwort + $GLOBAL_CONFIG['person_id_nr_adder']) ;
                        //}

                        if(empty($oitem)) $oitem='tbperson.pid';
                        if(empty($odir)) $odir='DESC'; # default, latest pid at top

                        $sql2="    WHERE tbperson.pid='$suchwort' ";

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
                        $sql2    .=" AND tbenc.pid=tbperson.pid AND tbenc.is_discharged=0 AND tbenc.encounter_status <> 'cancelled' AND tbenc.status NOT IN ($this->dead_stat)";
                }else{
                        $sql2    .=" AND tbenc.is_discharged=0 AND tbenc.encounter_status <> 'cancelled' AND tbenc.status NOT IN ($this->dead_stat)";
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
                        'tbperson.date_birth, tbperson.addr_zip, tbperson.sex, tbperson.death_date,tbperson.death_encounter_nr, '.
                        'tbperson.status, tbenc.encounter_nr FROM '.
                        $this->tb_person.' AS tbperson,'.
                        $this->tb_enc.' AS tbenc '.$sql2.
                        $sql3;

        }else{
                #echo "sql2 = ".$sql3;

                $this->sql='SELECT tbperson.pid, tbenc.encounter_nr, tbperson.name_last, tbperson.name_first, '.
                        'tbperson.date_birth, tbperson.addr_zip, tbperson.sex, tbperson.death_date, tbperson.death_encounter_nr, '.
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

                         $pid_format = " pid='".$this->pid."' ";

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
                #if (intval($pid))
                #    $pid_format = " (pid='$pid' OR pid=$pid) ";
                #else
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
                #if (intval($pid))
                #    $pid_format = " (pid='$pid' OR pid=$pid) ";
                #else
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
#            $cond.=" AND date_birth='".$data['date_birth']."' ";
                }
#        if (!empty($data['sex'])){
#            $cond.=" AND sex $sql_LIKE '".$data['sex']."' ";
#        }
        /*
                $this->sql="SELECT pid,name_last,name_first,date_birth,sex FROM $this->tb_person
                                        WHERE name_last $sql_LIKE '%".$data['name_last']."%'
                                                AND name_first $sql_LIKE '%".$data['name_first']."%'
                                                $cond ";
        */

        #edited by VAN 09-23-08
        $this->sql="SELECT pid,name_last,name_first,name_middle,date_birth,sex FROM $this->tb_person
                                        WHERE name_last $sql_LIKE '".$data['name_last']."'
                                        AND name_first $sql_LIKE '%".$data['name_first']."%'
                                        AND name_middle $sql_LIKE '".$data['name_middle']."'
                                        $cond
                                        UNION
                                        SELECT pid,name_last,name_first,name_middle,date_birth,sex FROM $this->tb_person
                                        WHERE name_last $sql_LIKE '".$data['name_last']."'
                                        AND name_first $sql_LIKE '%".$data['name_first']."%'
                                        $cond ";

#                        AND date_birth='".$data['date_birth']."'
#                        AND sex $sql_LIKE '".$data['sex']."'";
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
                #if (intval($pid))
                #    $pid_format = " (pid='$this->pid' OR pid=$this->pid) ";
                #else
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
                                                                WHERE pid = '".$key."'";
                }else{
                        $this->sql="SELECT * from care_person
                                                                WHERE name_last LIKE '".$key."%' OR name_first LIKE '".$key."%'";
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

         function searchIfEmployee($pid){
                 global $db;

                $this->sql="SELECT ps.nr,ps.date_exit, ps.contract_end, cp.*
                                                FROM care_personell AS ps
                                                INNER JOIN care_person AS cp ON cp.pid=ps.pid
                                                AND (ps.date_exit IN ('0000-00-00',date(NOW()))
                                                OR ps.contract_end IN ('0000-00-00',date(NOW())))
                                                AND cp.pid='$pid'";

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

        function concatname($slast, $sfirst, $smid) {
                $stmp = "";

                if (!empty($slast)) $stmp .= $slast;
                if (!empty($sfirst)) {
                        if (!empty($stmp)) $stmp .= ", ";
                        $stmp .= $sfirst;
                }
                if (!empty($smid)) {
                        if (!empty($stmp)) $stmp .= " ";
                        $stmp .= $smid;
                }
                return($stmp);
        }

        #----------added by VAN 05-19-08
        function updatePersonInfo($pid,&$data){
                global $HTTP_SESSION_VARS;

             $this->data_array=$data;
             #print_r($this->data_array);
                #print_r($HTTP_SESSION_VARS);
                #echo "user = ".$HTTP_SESSION_VARS['sess_user_name'];

                // remove probable existing array data to avoid replacing the stored data
             if(isset($this->data_array['pid'])) unset($this->data_array['pid']);
             if(isset($this->data_array['create_id'])) unset($this->data_array['create_id']);
                if(isset($this->data_array['create_time'])) unset($this->data_array['create_time']);

                // set the where condition
             $this->where="pid='$pid'";
             #$this->data_array['history']=$this->ConcatHistory("Update: ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
             $this->data_array['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];
             $this->data_array['modify_time']=date('Y-m-d H:i:s');

             if ($data['birth_time']!='00:00:00')
                $this->data_array['birth_time']=$data['birth_time'];
                # print_r($this->data_array);

                return $this->updateDataFromInternalArray($pid);

        }

        /**
        * Update the name of the person given the pid.  Used by updating of name of principal holder of insurance in billing section.
        *
        * @access public
        * @param string pid used in the "where" condition
        * @param array data containing name_last, name_first, name_middle
        * @return boolean
        * @author LST - 05.10.2012
        */
        function updateNameofPerson($pid, $data) {
            $flds = array('name_last',
                                        'name_first',
                                        'name_middle',
                                        'history',
                                        'modify_id',
                                        'modify_time');

          $tmp = $this->ref_array;
          $this->ref_array = $flds;
            $this->data_array = $data;
            $this->data_array['history'] = $this->ConcatHistory("\nUpdated name_last, name_first, name_middle: ".date('Y-m-d H:i:s')." by ".$_SESSION['sess_user_name']);
           $this->data_array['modify_id'] = $_SESSION['sess_user_name'];
          $this->data_array['modify_time'] = date('Y-m-d H:i:s');

          $this->where="pid='$pid'";
          $bsuccess = $this->updateDataFromInternalArray($pid);
          $this->ref_array = $tmp;
            return $bsuccess;
        }

        function updateDeathDate($pid='', $death_date='0000-00-00', $encounter_nr=0, $death_time='00:00:00'){
                if ($death_date=='0000-00-00')
            $encounter_nr = 0;

        $this->sql="UPDATE care_person SET
                                        death_date= '$death_date',
                                        death_time= '$death_time',
                                        death_encounter_nr = '$encounter_nr'
                                        WHERE pid='$pid'";

                return $this->Transact($this->sql);
        }

        #-------------------------------

        #added by VAN 08-06-08
        function getRelationship($cond='',$oitem='',$sort=''){
                global $db;

                if (!empty($cond))
                        $where=" WHERE $cond ";
                $order=" ORDER BY child_relation ";
                if (!empty($oitem))
                        $order=" ORDER BY ".$oitem." ".$sort;
                $this->sql="SELECT * FROM seg_relationship $where $order";

                if($this->res['go']=$db->Execute($this->sql)){
                        if($this->res['go']->RecordCount()){
                                return $this->res['go'];
                        }else{ return FALSE; }
                }else{ return FALSE; }
        }

        #added by VAN 09-08-08
        function getOccupationInfo($occupation_nr=''){
                global $db;

                if ($occupation_nr)
                                $this->sql="SELECT * FROM seg_occupation WHERE occupation_nr='$occupation_nr'";
                else
                        $this->sql="SELECT * FROM seg_occupation ORDER BY occupation_name";

                if ($this->result=$db->Execute($this->sql)){
                        if ($this->count=$this->result->RecordCount())
                                #if ($occupation_nr)
                                #    return $this->result->FetchRow();
                                #else
                                        return $this->result;
                        else
                                return FALSE;
                }else{
                        return FALSE;
                }
        }

        function saveOccupation(&$data){
                global $db;
                global $HTTP_SESSION_VARS;
                $ret=FALSE;

                extract($data);

                $this->sql="INSERT INTO seg_occupation(occupation_name, modify_id, modify_date, create_id, create_date)
                                                                VALUES('$occupation_name', '$modify_id', '$modify_date', '$create_id', '$create_date')";

                if ($db->Execute($this->sql)) {
                        if ($db->Affected_Rows()) {
                                $ret=TRUE;
                        }
                }
                if ($ret)    return TRUE;
                else return FALSE;
        }

        function updateOccupation($nr,&$data){
                global $db;
                global $HTTP_SESSION_VARS;
                $ret=FALSE;

                extract($data);

                $this->sql = "UPDATE seg_occupation
                                                             SET occupation_name='$occupation_name',
                                                                                modify_id='$modify_id',
                                                                                modify_date='$modify_date'
                                                                WHERE occupation_nr='".$nr."'";

                #echo "update = ".$this->sql;

                if ($db->Execute($this->sql)) {
                        if ($db->Affected_Rows()) {
                                $ret=TRUE;
                        }
                }
                if ($ret)    return TRUE;
                else return FALSE;    }

        function deleteOccupationItem($occupation_nr) {
                global $db,$HTTP_SESSION_VARS;

                if(empty($occupation_nr) || (!$occupation_nr))
                        return FALSE;

                $this->sql="DELETE FROM seg_occupation WHERE occupation_nr='$occupation_nr'";
                return $this->Transact();
        }

        function getReligionInfo($religion_nr=''){
                global $db;


                if ($religion_nr)
                                $this->sql="SELECT * FROM seg_religion WHERE religion_nr='$religion_nr'";
                else
                        $this->sql="SELECT * FROM seg_religion ORDER BY religion_name";

                if ($this->result=$db->Execute($this->sql)){
                        if ($this->count=$this->result->RecordCount())
                                #if ($religion_nr)
                                #    return $this->result->FetchRow();
                                #else
                                        return $this->result;
                        else
                                return FALSE;
                }else{
                        return FALSE;
                }
        }

        function saveReligion(&$data){
                global $db;
                global $HTTP_SESSION_VARS;
                $ret=FALSE;

                extract($data);

                $this->sql="INSERT INTO seg_religion(religion_name, modify_id, modify_date, create_id, create_date)
                                                                VALUES('$religion_name', '$modify_id', '$modify_date', '$create_id', '$create_date')";

                if ($db->Execute($this->sql)) {
                        if ($db->Affected_Rows()) {
                                $ret=TRUE;
                        }
                }
                if ($ret)    return TRUE;
                else return FALSE;
        }

        function updateReligion($nr,&$data){
                global $db;
                global $HTTP_SESSION_VARS;
                $ret=FALSE;

                extract($data);

                $this->sql = "UPDATE seg_religion
                                                             SET religion_name='$religion_name',
                                                                                modify_id='$modify_id',
                                                                                modify_date='$modify_date'
                                                                WHERE religion_nr='".$nr."'";

                if ($db->Execute($this->sql)) {
                        if ($db->Affected_Rows()) {
                                $ret=TRUE;
                        }
                }
                if ($ret)    return TRUE;
                else return FALSE;    }

        function deleteReligionItem($religion_nr) {
                global $db,$HTTP_SESSION_VARS;

                if(empty($religion_nr) || (!$religion_nr))
                        return FALSE;

                $this->sql="DELETE FROM seg_religion WHERE religion_nr='$religion_nr'";
                return $this->Transact();
        }

        function getLimitOccupationInfo($len=30,$so=0,$sortby='occupation_name',$sortdir='ASC'){
                global $db;

                $this->sql="SELECT * FROM seg_occupation ORDER BY $sortby $sortdir";
                #echo "sql = ".$this->sql;

                if($this->res['occupation_nr']=$db->SelectLimit($this->sql,$len,$so)){
                        if($this->rec_count=$this->res['occupation_nr']->RecordCount()){
                                return $this->res['occupation_nr'];
                        }else{ return FALSE; }
                }else{ return FALSE; }
        }//end function getLimitOccupationInfo

        function countAllOccupation(){
                global $db;
                //$this->sql="SELECT procedure_code FROM $this->tb_seg_icpm";
                $this->sql="SELECT occupation_nr FROM seg_occupation";
                if($buffer=$db->Execute($this->sql)){
                        return $buffer->RecordCount();
                } else { return 0; }
        }

        function searchLimitActiveOccupation($key,$len=30,$so=0,$oitem='occupation_name',$odir='ASC'){
                global $db, $sql_LIKE;
                if(empty($key)) return FALSE;

                $select="SELECT * FROM seg_occupation ";

                $append="ORDER BY $oitem $odir";
                $this->sql="$select WHERE (occupation_name $sql_LIKE'$key%') ".$append;

                #echo "sql = ".$this->sql;
                if($this->res['codec']=$db->SelectLimit($this->sql,$len,$so)){
                        if($this->rec_count=$this->res['codec']->RecordCount()){
                                return $this->res['codec'];
                        }else{
                                $this->sql="$select WHERE (occupation_name $sql_LIKE '$key%') ".$append;
                                if($this->res['codec']=$db->SelectLimit($this->sql,$len,$so)){
                                        if($this->rec_count=$this->res['codec']->RecordCount()){
                                                return $this->res['codec'];
                                        }else{
                                                $this->sql="$select WHERE (occupation_name $sql_LIKE '$key% ) ".$append;
                                                if($this->res['codec']=$db->SelectLimit($this->sql,$len,$so)){
                                                        if($this->rec_count=$this->res['codec']->RecordCount()){
                                                                return $this->res['codec'];
                                                        }else{return FALSE; }
                                                }else {return FALSE; }
                                        }
                                }else{return FALSE; }
                        }
                }else{return FALSE; }
        }

        function searchCountActiveOccupation($key){
                global $db, $sql_LIKE;

                if(empty($key)) return FALSE;
                        $select="SELECT occupation_name FROM seg_occupation";

                $this->sql="$select WHERE occupation_name $sql_LIKE '$key%'";

                if($this->res['scaf']=$db->Execute($this->sql)){
                        if($this->rec_count=$this->res['scaf']->RecordCount()){
                                return $this->rec_count;
                        }else{
                                $this->sql="$select WHERE occupation_name $sql_LIKE '$key%'";
                                if($this->res['scaf']=$db->Execute($this->sql)){
                                        if($this->rec_count=$this->res['scaf']->RecordCount()){
                                                return $this->rec_count;
                                        }else{
                                                //$this->sql="$select WHERE (procedure_code $sql_LIKE '%$key%' OR description '%$key%')";
                                                //Note: change procedure_code -> code
                                                $this->sql="$select WHERE (occupation_name '$key%')";
                                                if($this->res['scaf']=$db->Execute($this->sql)){
                                                        if($this->rec_count=$this->rec['scaf']->RecordCount()){
                                                                return $this->rec_count;
                                                        }else{return 0;}
                                                }else{return 0;}
                                        }
                                }else{return 0;}
                        }
                }else{return 0;}
        }//end function searchCountActiveIcd10


        function getLimitReligionInfo($len=30,$so=0,$sortby='religion_name',$sortdir='ASC'){
                global $db;

                $this->sql="SELECT * FROM seg_religion ORDER BY $sortby $sortdir";
                #echo "sql = ".$this->sql;

                if($this->res['occupation_nr']=$db->SelectLimit($this->sql,$len,$so)){
                        if($this->rec_count=$this->res['occupation_nr']->RecordCount()){
                                return $this->res['occupation_nr'];
                        }else{ return FALSE; }
                }else{ return FALSE; }
        }//end function getLimitOccupationInfo

        function countAllReligion(){
                global $db;
                $this->sql="SELECT religion_nr FROM seg_religion";
                if($buffer=$db->Execute($this->sql)){
                        return $buffer->RecordCount();
                } else { return 0; }
        }

        function searchLimitActiveReligion($key,$len=30,$so=0,$oitem='religion_name',$odir='ASC'){
                global $db, $sql_LIKE;
                if(empty($key)) return FALSE;

                $select="SELECT * FROM seg_religion ";

                $append="ORDER BY $oitem $odir";
                $this->sql="$select WHERE (religion_name $sql_LIKE'$key%') ".$append;

                #echo "sql = ".$this->sql;
                if($this->res['codec']=$db->SelectLimit($this->sql,$len,$so)){
                        if($this->rec_count=$this->res['codec']->RecordCount()){
                                return $this->res['codec'];
                        }else{
                                $this->sql="$select WHERE (religion_name $sql_LIKE '$key%') ".$append;
                                if($this->res['codec']=$db->SelectLimit($this->sql,$len,$so)){
                                        if($this->rec_count=$this->res['codec']->RecordCount()){
                                                return $this->res['codec'];
                                        }else{
                                                $this->sql="$select WHERE (religion_name $sql_LIKE '$key% ) ".$append;
                                                if($this->res['codec']=$db->SelectLimit($this->sql,$len,$so)){
                                                        if($this->rec_count=$this->res['codec']->RecordCount()){
                                                                return $this->res['codec'];
                                                        }else{return FALSE; }
                                                }else {return FALSE; }
                                        }
                                }else{return FALSE; }
                        }
                }else{return FALSE; }
        }

        function searchCountActiveReligion($key){
                global $db, $sql_LIKE;

                if(empty($key)) return FALSE;
                        $select="SELECT religion_name FROM seg_religion";

                $this->sql="$select WHERE religion_name $sql_LIKE '$key%'";

                if($this->res['scaf']=$db->Execute($this->sql)){
                        if($this->rec_count=$this->res['scaf']->RecordCount()){
                                return $this->rec_count;
                        }else{
                                $this->sql="$select WHERE religion_name $sql_LIKE '$key%'";
                                if($this->res['scaf']=$db->Execute($this->sql)){
                                        if($this->rec_count=$this->res['scaf']->RecordCount()){
                                                return $this->rec_count;
                                        }else{
                                                $this->sql="$select WHERE (religion_name '$key%')";
                                                if($this->res['scaf']=$db->Execute($this->sql)){
                                                        if($this->rec_count=$this->rec['scaf']->RecordCount()){
                                                                return $this->rec_count;
                                                        }else{return 0;}
                                                }else{return 0;}
                                        }
                                }else{return 0;}
                        }
                }else{return 0;}
        }//end function searchCountActiveIcd10

        #------
        function getEthnicInfo($ethnic_nr=''){
                global $db;


                if ($ethnic_nr)
                                $this->sql="SELECT * FROM care_type_ethnic_orig WHERE nr='$ethnic_nr'";
                else
                        $this->sql="SELECT * FROM care_type_ethnic_orig ORDER BY name";

                if ($this->result=$db->Execute($this->sql)){
                        if ($this->count=$this->result->RecordCount())
                                #if ($religion_nr)
                                #    return $this->result->FetchRow();
                                #else
                                        return $this->result;
                        else
                                return FALSE;
                }else{
                        return FALSE;
                }
        }

        function saveEthnic(&$data){
                global $db;
                global $HTTP_SESSION_VARS;
                $ret=FALSE;

                extract($data);

                $this->sql="INSERT INTO care_type_ethnic_orig
                                                                        (class_nr, name, LD_var, status,use_frequency,
                                                                         modify_id, modify_time, create_id, create_time)
                                                                VALUES('1','$ethnic_name','$LD_var','','0',
                                                                             '$modify_id', '$modify_time', '$create_id', '$create_time')";

                if ($db->Execute($this->sql)) {
                        if ($db->Affected_Rows()) {
                                $ret=TRUE;
                        }
                }
                if ($ret)    return TRUE;
                else return FALSE;
        }

        function updateEthnic($nr,&$data){
                global $db;
                global $HTTP_SESSION_VARS;
                $ret=FALSE;

                extract($data);

                $this->sql = "UPDATE care_type_ethnic_orig
                                                             SET name='$ethnic_name',
                                                                                 LD_var='$LD_var',
                                                                                modify_id='$modify_id',
                                                                                modify_time='$modify_time'
                                                                WHERE nr='".$nr."'";

                if ($db->Execute($this->sql)) {
                        if ($db->Affected_Rows()) {
                                $ret=TRUE;
                        }
                }
                if ($ret)    return TRUE;
                else return FALSE;    }

        function deleteEthnicItem($ethnic_nr) {
                global $db,$HTTP_SESSION_VARS;

                if(empty($ethnic_nr) || (!$ethnic_nr))
                        return FALSE;

                $this->sql="DELETE FROM care_type_ethnic_orig WHERE nr='$ethnic_nr'";
                return $this->Transact();
        }

        function getLimitEthnicInfo($len=30,$so=0,$sortby='name',$sortdir='ASC'){
                global $db;

                $this->sql="SELECT * FROM care_type_ethnic_orig ORDER BY $sortby $sortdir";
                #echo "sql = ".$this->sql;

                if($this->res['nr']=$db->SelectLimit($this->sql,$len,$so)){
                        if($this->rec_count=$this->res['nr']->RecordCount()){
                                return $this->res['nr'];
                        }else{ return FALSE; }
                }else{ return FALSE; }
        }//end function getLimitOccupationInfo

        function countAllEthnic(){
                global $db;
                $this->sql="SELECT nr FROM care_type_ethnic_orig";
                if($buffer=$db->Execute($this->sql)){
                        return $buffer->RecordCount();
                } else { return 0; }
        }

        function searchLimitActiveEthnic($key,$len=30,$so=0,$oitem='name',$odir='ASC'){
                global $db, $sql_LIKE;
                if(empty($key)) return FALSE;

                $select="SELECT * FROM care_type_ethnic_orig ";

                $append="ORDER BY $oitem $odir";
                $this->sql="$select WHERE (name $sql_LIKE'$key%') ".$append;

                #echo "sql = ".$this->sql;
                if($this->res['codec']=$db->SelectLimit($this->sql,$len,$so)){
                        if($this->rec_count=$this->res['codec']->RecordCount()){
                                return $this->res['codec'];
                        }else{
                                $this->sql="$select WHERE (name $sql_LIKE '$key%') ".$append;
                                if($this->res['codec']=$db->SelectLimit($this->sql,$len,$so)){
                                        if($this->rec_count=$this->res['codec']->RecordCount()){
                                                return $this->res['codec'];
                                        }else{
                                                $this->sql="$select WHERE (name $sql_LIKE '$key% ) ".$append;
                                                if($this->res['codec']=$db->SelectLimit($this->sql,$len,$so)){
                                                        if($this->rec_count=$this->res['codec']->RecordCount()){
                                                                return $this->res['codec'];
                                                        }else{return FALSE; }
                                                }else {return FALSE; }
                                        }
                                }else{return FALSE; }
                        }
                }else{return FALSE; }
        }

        function searchCountActiveEthnic($key){
                global $db, $sql_LIKE;

                if(empty($key)) return FALSE;
                        $select="SELECT name FROM care_type_ethnic_orig";

                $this->sql="$select WHERE name $sql_LIKE '$key%'";

                if($this->res['scaf']=$db->Execute($this->sql)){
                        if($this->rec_count=$this->res['scaf']->RecordCount()){
                                return $this->rec_count;
                        }else{
                                $this->sql="$select WHERE name $sql_LIKE '$key%'";
                                if($this->res['scaf']=$db->Execute($this->sql)){
                                        if($this->rec_count=$this->res['scaf']->RecordCount()){
                                                return $this->rec_count;
                                        }else{
                                                $this->sql="$select WHERE (name '$key%')";
                                                if($this->res['scaf']=$db->Execute($this->sql)){
                                                        if($this->rec_count=$this->rec['scaf']->RecordCount()){
                                                                return $this->rec_count;
                                                        }else{return 0;}
                                                }else{return 0;}
                                        }
                                }else{return 0;}
                        }
                }else{return 0;}
        }//end function searchCountActiveIcd10
        #--------------------------

        #added by VAN 01-21-09
        function getInsurance_nr($pid){
                global $db;

                $this->sql ="SELECT pin.insurance_nr
                                                FROM care_person_insurance AS pin
                                                INNER JOIN care_insurance_firm AS f ON f.hcare_id=pin.hcare_id
                                                WHERE pin.pid='".$pid."' AND f.default_classification='D'
                                                AND pin.is_void=0
                                        UNION
                                        SELECT pin.insurance_nr
                                                FROM seg_dependents AS d
                                                LEFT JOIN care_person_insurance AS pin ON d.parent_pid=pin.pid
                                                LEFT JOIN care_insurance_firm AS f ON f.hcare_id=pin.hcare_id
                                                WHERE d.dependent_pid='".$pid."' AND f.default_classification='D'
                                                AND d.status NOT IN ('cancelled','deleted','expired')";

                if ($this->result=$db->Execute($this->sql)){
                        #$this->count=$this->result->RecordCount();
                        if ($this->result->RecordCount())
                                return $this->result->FetchRow();
                        else
                                return FALSE;
                }else{
                        return FALSE;
                }
        }
        #----------------

    #added by VAN 03-24-09
    function updatePatientID($pid='', $id_number, $type){
        if ($type=='SC')
            $id_field = " senior_ID = '$id_number' ";
        elseif ($type=='VET')
            $id_field = " veteran_ID = '$id_number' ";

                $this->sql="UPDATE care_person SET
                                                $id_field
                                        WHERE pid='$pid'";

                return $this->Transact($this->sql);
        }

    #added by VAN 01-25-10
    function CurrentEncounter2($encounter_nr){
                global $db;
                if(!$encounter_nr) return false;

                $this->sql="SELECT encounter_nr FROM $this->tb_enc
                                        WHERE encounter_nr='".$encounter_nr."' AND is_discharged=0
                                        AND encounter_status <> 'cancelled'
                                        AND status NOT IN ($this->dead_stat)
                                        ORDER BY encounter_date DESC";
                #echo "sql2 = ".$this->sql;
                if($buf=$db->Execute($this->sql)){
                        if($buf->RecordCount()) {
                                $buf2=$buf->FetchRow();
                                //echo $this->sql;
                                return $buf2['encounter_nr'];
                        }else{return false;}
                }else{return false;}
     }

     function is_principal($pid){
             global $db;

                $this->sql = "SELECT pid, hcare_id, is_principal from care_person_insurance
                                            WHERE pid='$pid'";

                if ($this->result=$db->Execute($this->sql)){
                        #$this->count=$this->result->RecordCount();
                        if ($this->result->RecordCount())
                                return TRUE;
                        else
                                return FALSE;
                }else{
                        return FALSE;
                }
        }

        #extracted from sir bong's code in pdf_philhealth_form2.php'
        function getPrincipalNm($pid) {
                global $db;

                $this->sql = "SELECT p.name_last AS LastName, p.name_first AS FirstName, p.name_2 AS SecondName, \n
                                            p.name_3 AS ThirdName, p.name_middle AS MiddleName, i.insurance_nr AS IdNum     \n
                                     FROM care_person AS p LEFT JOIN care_person_insurance AS i ON i.pid = p.pid        \n
                                     WHERE /*i.hcare_id = $this->hcare_id AND*/ i.is_principal = 1 AND p.pid = '$pid'";

                if ($this->result=$db->Execute($this->sql)){
                        #$this->count=$this->result->RecordCount();
                        if ($this->result->RecordCount())
                                return $this->result->FetchRow();
                        else
                                return FALSE;
                }else{
                        return FALSE;
                }
        }

        function getPrincipalNmFromTmp($enc_nr) {
                global $db;

                $this->sql = "SELECT member_lname as LastName, member_fname as FirstName, '' as SecondName, \n
                             '' as ThirdName, member_mname as MiddleName, mi.insurance_nr as IdNum   \n
                                from seg_insurance_member_info as mi inner join care_encounter as ce       \n
                                     on mi.pid = ce.pid                                                      \n
                                where ce.encounter_nr = '$enc_nr'";

                if ($this->result=$db->Execute($this->sql)){
                        #$this->count=$this->result->RecordCount();
                        if ($this->result->RecordCount())
                                return $this->result;
                        else
                                return FALSE;
                }else{
                        return FALSE;
                }
        }

        function getPrincipalAddr($pid=null) {
            global $db;
            if (empty($pid)) {
                $pid = $this->pid;
            }
            $this->sql = "SELECT p.street_name AS Street, sb.brgy_nr, sb.brgy_name AS Barangay,\n".
                    "sg.mun_nr, sg.mun_name AS Municity, sg.zipcode AS Zipcode, sp.prov_name AS Province\n".
                "FROM care_person AS p\n".
                    "LEFT JOIN seg_barangays AS sb ON sb.brgy_nr = p.brgy_nr\n".
                    "LEFT JOIN seg_municity AS sg ON sg.mun_nr = p.mun_nr\n".
                    "LEFT JOIN seg_provinces AS sp ON sp.prov_nr = sg.prov_nr\n".
                "WHERE p.pid =".$db->qstr($pid);
            $this->result=$db->GetRow($this->sql);
            return $this->result;
        }

        function getPrincipalAddrFromTmp($enc_nr) {
            global $db;

            $this->sql = "select street_name as Street, brgy_name as Barangay, mun_name as Municity,              \n
                                             zipcode as Zipcode, prov_name as Province                                            \n
                                        from (seg_insurance_member_info as mi                                                   \n
                                             left join ((seg_barangays as b inner join seg_municity as m on m.mun_nr = b.mun_nr)  \n
                                                    inner join seg_provinces as p on m.prov_nr = p.prov_nr)                           \n
                                                    on mi.brgy_nr = b.brgy_nr) inner join care_encounter as ce on mi.pid = ce.pid     \n
                                        where ce.encounter_nr = '$enc_nr'                                                       \n
                                        order by create_dt desc limit 1";

            if ($this->result=$db->Execute($this->sql)){
                    #$this->count=$this->result->RecordCount();
                    if ($this->result->RecordCount())
                            return $this->result;
                    else
                            return FALSE;
            }else{
                    return FALSE;
            }
        }
        
        /***
         *  This function will return the rowset of the information of PHIC's member given patient's HRN or PID.
         * 
         * @@param 
         * 
         *  Author:  LST - 11/11/2012
         */
        
        /**
         * @internal     Return the rowset of the information of PHIC's member given patient's HRN or PID.
         * @access       public
         * @author       LST
         * @package      include
         * @subpackage   care_api_classes
         * @global       db - database object
         * @created      11.11.2012
         * 
         * @param        pid (HRN), hcareid, insurance_nr, string of fields in rowset
         * @return       rowset (array)
         */
        function getMemberInsuranceInfo($pid, $hcareid, $insurance_nr=null, $fldsstr='*') {
            global $db;
            if (empty($pid)) {
                $pid = $this->pid;
            }
            $this->sql = "SELECT $fldsstr FROM seg_insurance_member_info WHERE pid=".$db->qstr($pid)." AND hcare_id=".$db->qstr($hcareid);
            $this->result=$db->GetRow($this->sql);
            return $this->result;
        }
        
        /**
         * @internal     Return the membership type of PHIC's member given the encounter no.
         * @access       public
         * @author       LST
         * @package      include
         * @subpackage   care_api_classes
         * @global       db - database object
         * @created      11.11.2012
         * 
         * @param        encounter no.
         * @return       membership type code or false
         */        
        function getMemberType($encounter_nr) {
            global $db;
            
            $this->sql = "SELECT 
                            memcategory_code 
                          FROM
                            seg_memcategory sm 
                            INNER JOIN seg_encounter_memcategory sem 
                              ON sm.`memcategory_id` = sem.`memcategory_id` 
                          WHERE sem.`encounter_nr` = '$encounter_nr'";
            $this->result=$db->Execute($this->sql);
            if ($this->result) {
                if ($this->result->RecordCount()) {
                    $row = $this->result->FetchRow();
                    return $row["memcategory_code"];
                }
                else
                    return FALSE;
            } else {
                return FALSE;
            }
        }
        
        #-----------------------------
        #-----------------------------
         #--added by CHa, Feb 17, 2010---
        function getRelationshipItems($key, $multiple=0, $maxcount=100, $offset=0)
        {
            global $db;
            if(empty($maxcount)) $maxcount=100;
            if(empty($offset)) $offset=0;
            if($key!='*' || $key!='')
            {
                $this->sql = "SELECT * FROM seg_relationship WHERE (child_relation!='' AND (child_relation='".$key.
                "' OR child_relation like '".$key."%')) ORDER BY child_relation";
            }
            else
            {
                   $this->sql = "SELECT * FROM seg_relationship WHERE (child_relation!='') ORDER BY child_relation";
            }
            #echo "max=".$maxcount." offset=".$offset;
            if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset))
                           {
                               #echo $this->sql;
                                   if($this->rec_count=$this->res['ssl']->RecordCount())
                                   {
                                           return $this->res['ssl'];
                                   }
                                   else{ return false; }
                           }
                           else{ return false; }
        }

     function countRelationshipItems($key, $multiple=0, $maxcount=100, $offset=0)
     {
         global $db;
         if(empty($maxcount)) $maxcount=100;
         if(empty($offset)) $offset=0;
         if($key!='*' || $key!='')
         {
             $this->sql = "SELECT * FROM seg_relationship WHERE (child_relation!='' AND (child_relation='".$key.
             "' OR child_relation like '".$key."%')) ORDER BY child_relation";
         }
         else
         {
                $this->sql = "SELECT * FROM seg_relationship WHERE (child_relation!='') BY child_relation";
         }
         if ($this->result=$db->Execute($this->sql)) {
                        if ($this->count=$this->result->RecordCount()) {
                                return $this->result;
                        }
                        else{return FALSE;}
                }else{return FALSE;}
     }

     function saveNewRelationship($value)
     {
         global $db;
         $this->sql = "INSERT INTO seg_relationship (child_relation) VALUES (".$db->qstr($value).")";
         $this->result = $db->Execute($this->sql);
         if($db->Affected_Rows())
         {
             return true;
         }
         else
         {
             print_r($db->ErrorMsg());
             return false;
         }
     }

     function deleteRelationship($id)
     {
         global $db;
         $this->sql = "DELETE FROM seg_relationship WHERE id=".$db->qstr($id);
         $this->result = $db->Execute($this->sql);
         if($db->Affected_Rows())
         {
             return true;
         }
         else
         {
             print_r($db->ErrorMsg());
             return false;
         }
     }

     function updateRelationship($id,$newval)
     {
         global $db;
         $this->sql = "UPDATE seg_relationship SET child_relation=".$db->qstr($newval)." WHERE id=".$db->qstr($id);
         $this->result = $db->Execute($this->sql);
         if($db->Affected_Rows())
         {
             return true;
         }
         else
         {
             print_r($db->ErrorMsg());
             return false;
         }
     }
     #--end CHa----------------------     
     
     /***
      * Returns the PHIC insurance no. of patient identified by param 'pid'
      */
     function getPHICInsuranceNo($pid) {
         global $db;
         
         $insurancenr = '';
         $this->sql = "SELECT 
                        insurance_nr 
                      FROM
                        care_person_insurance 
                      WHERE pid = '{$pid}' 
                        AND hcare_id = ".PHIC_ID;
        if ($result=$db->Execute($this->sql)) {
            if ($result->RecordCount()) {
                if ($row = $result->FetchRow()) {
                    if (!is_null($row["insurance_nr"])) {
                        $insurancenr = $row["insurance_nr"];
                    }
                }
            }
        }
        return $insurancenr;
     }
     
     
    /**
     * STUPID METHOD!!!!!
     * 
     * @param array $params
     * @todo REFACTOR REFACTOR REFACTOR
     */
    public function saveInsuranceMembershipInfo($params) 
    {
        global $db;
        $db->StartTrans();
        // saving in care_person_insurance
        $data = array(
            'pid' => $db->qstr(@$params['pid']),
            'hcare_id' => $db->qstr($params['hcare_id']),
            'class_nr' => $db->qstr(@$params['class_nr']),
            'insurance_nr' => $db->qstr(@$params['insurance_nr']),
            'is_principal' => (@$params['relation']=='M') ? 1 : 0,
            'modify_id' => $db->qstr($_SESSION['sess_temp_userid']),
            'modify_time' => 'NOW()',
        );
        
        $ok = $db->Replace('care_person_insurance', $data,
            array('pid','hcare_id'),
            $autoQuote = false
        );

        unset($params['class_nr']);
        
        if ($ok && !empty($params['encounter_nr'])) {
            // saving in seg_encounter_insurance
            $data = array(
                'hcare_id' => $db->qstr($params['hcare_id']),
                'encounter_nr' => $db->qstr(@$params['encounter_nr']),
                'modify_id' => $db->qstr($_SESSION['sess_temp_userid']),
                'modify_dt' => 'NOW()',
            );
            $ok = $db->Replace('seg_encounter_insurance', $data,
                array('encounter_nr','hcare_id'),
                $autoQuote = false
            );
            
            // saving memcategory
            if ($ok) {
                global $root_path;
                require_once $root_path . 'include/care_api_classes/class_insurance.php';
                $insurance = new PersonInsurance;
                $ok = $insurance->setEncounterInsuranceCategory(
                    $params['encounter_nr'], 
                    $params['member_type']
                );
            }
        }
        
        unset($params['encounter_nr']);
        
        // saving details
        if ($ok) {
            $ok = $db->Replace('seg_insurance_member_info', 
                $params,
                array('pid','hcare_id'),
                $autoQuote = true
            );
        }
        
        if ($ok) {
            $db->CompleteTrans();
        } else {
            $db->FailTrans();
        }

        return $ok!=0;
    }
}
