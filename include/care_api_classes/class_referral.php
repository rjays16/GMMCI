<?php
/**
* @package care_api
*/

/**
*/
require_once($root_path.'include/care_api_classes/class_notes.php');
/**
*  Patient referral.
*  Note this class should be instantiated only after a "$db" adodb  connector object  has been established by an adodb instance.
* @author Elpidio Latorilla
* @version beta 2.0.1
* @copyright 2002,2003,2004,2005,2005 Elpidio Latorilla
* @package care_api
*/
class Referral extends Notes {
    
    /**
    * Current referral number
    * @var int
    */
    var $ref_nr;
    /**
    * SQL Query
    * @var string
    */
    var $sql;
    /**
    * SQL Result Set
    * @var array
    */
    var $result;
    
    /**
    * Constructor
    * @param int Referral number
    */            
    function Referral($ref_nr='') {
        $this->ref_nr=$ref_nr;
    }
    
    /* function SearchAdmissionList
    *  @author Raissa 12/15/08
    *  @access public
    *  @internal Function for retrieving the list of uncancelled Admission History for a patient
    *  @param String pid, searchkey
    *  @param Integer maxcount, offset
    *  @return Array resultset
    *  @return Boolean false to indicate failure in the query   
    */
    function SearchAdmissionList($pid='', $searchkey='',$maxcount=100,$offset=0){
        global $db, $sql_LIKE, $root_path, $date_format;
        if(empty($maxcount)) $maxcount=100;
        if(empty($offset)) $offset=0;
        
        # convert * and ? to % and &
        $searchkey=strtr($searchkey,'*?','%_');
        $searchkey=trim($searchkey);
        #$suchwort=$searchkey;
        $searchkey = str_replace("^","'",$searchkey);
        $keyword=addslashes($searchkey);
        
        $this->sql = "SELECT sr.referral_nr, sr.encounter_nr, sr.referrer_diagnosis, sr.referrer_dr, 
                      sr.referrer_dept, sr.referrer_notes, sr.is_referral, 
                      sr.create_id, sr.create_time, sr.referral_date, sr.status from seg_referral as sr
                      LEFT JOIN care_encounter as ce ON ce.encounter_nr = sr.encounter_nr 
                      WHERE ce.pid= $pid 
                      AND (sr.create_time LIKE '%".$keyword."%'
                      OR sr.referral_nr LIKE '%".$keyword."%' )
                      AND sr.status!='deleted'
                      ORDER BY ce.encounter_date DESC";
                                
        if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset)){
            if($this->rec_count=$this->res['ssl']->RecordCount()) {
                return $this->res['ssl'];
            }else{return false;}
        }else{return false;}
    }
    
    /* function countSearchAdmissionList
    *  @author Raissa 12/15/08
    *  @access public
    *  @internal Function for counting the list of uncancelled Admission History for a patient
    *  @param String pid, searchkey
    *  @param Integer maxcount, offset
    *  @return Array resultset
    *  @return Boolean false to indicate failure in the query   
    */
    function countSearchAdmissionList($pid='', $searchkey='',$maxcount=100,$offset=0) {
        global $db, $sql_LIKE, $root_path, $date_format;
        if(empty($maxcount)) $maxcount=100;
        if(empty($offset)) $offset=0;
        
        # convert * and ? to % and &
        $searchkey=strtr($searchkey,'*?','%_');
        $searchkey=trim($searchkey);
        #$suchwort=$searchkey;
        $searchkey = str_replace("^","'",$searchkey);
        $keyword=addslashes($searchkey);
        
        $this->sql = "SELECT sr.referral_nr, sr.encounter_nr, sr.referrer_diagnosis, sr.referrer_dr, 
                      sr.referrer_dept, sr.referrer_notes, sr.is_referral, 
                      sr.create_id, sr.create_time, sr.referral_date, sr.status from seg_referral as sr
                      LEFT JOIN care_encounter as ce ON ce.encounter_nr = sr.encounter_nr 
                      WHERE ce.pid= $pid 
                      AND (sr.create_time LIKE '%".$keyword."%'
                      OR sr.referral_nr LIKE '%".$keyword."%' )
                      AND sr.status!='deleted'
                      ORDER BY ce.encounter_date DESC";             
        
        if ($this->result=$db->Execute($this->sql)) {
            if ($this->count=$this->result->RecordCount()) {
                return $this->result;
            }
            else{return FALSE;}
        }else{return FALSE;}
    }
    
    /* function countSearchAllAdmissionList
    *  @author Raissa 12/15/08
    *  @access public
    *  @internal Function for counting all Admission History for a patient
    *  @param String pid, searchkey
    *  @param Integer maxcount, offset
    *  @return Array resultset
    *  @return Boolean false to indicate failure in the query   
    */
    function countSearchAllAdmissionList($pid='', $searchkey='',$maxcount=100,$offset=0) {
        global $db, $sql_LIKE, $root_path, $date_format;
        if(empty($maxcount)) $maxcount=100;
        if(empty($offset)) $offset=0;
        
        # convert * and ? to % and &
        $searchkey=strtr($searchkey,'*?','%_');
        $searchkey=trim($searchkey);
        #$suchwort=$searchkey;
        $searchkey = str_replace("^","'",$searchkey);
        $keyword=addslashes($searchkey);
        
        $this->sql = "SELECT sr.referral_nr, sr.encounter_nr, sr.referrer_diagnosis, sr.referrer_dr, 
                      sr.referrer_dept, sr.referrer_notes, sr.is_referral, 
                      sr.create_id, sr.create_time, sr.referral_date, sr.status from seg_referral as sr
                      LEFT JOIN care_encounter as ce ON ce.encounter_nr = sr.encounter_nr 
                      WHERE ce.pid= $pid 
                      AND (sr.create_time LIKE '%".$keyword."%'
                      OR sr.referral_nr LIKE '%".$keyword."%' )
                      ORDER BY ce.encounter_date DESC";             
        
        if ($this->result=$db->Execute($this->sql)) {
            if ($this->count=$this->result->RecordCount()) {
                return $this->result;
            }
            else{return FALSE;}
        }else{return FALSE;}
    }
    
    /* function addReferral
    *  @author Raissa 01/05/09
    *  @access public
    *  @internal Function for adding a transfer or referral admission to database
    *  @param String encounter_nr, refer, date, referral_nr, doctor, dept, diagnosis, notes, creator
    *  @return Boolean returns a success or fail in the query   
    */
    function addReferral($encounter_nr, $refer, $date, $referral_nr, $doctor, $dept, $diagnosis, $notes, $creator) {
        global $db;
        $today = date('Y-m-d H:i:s');
        $date = date("Y-m-d",strtotime($date));
        $history = "Added: " .$today;
        $this->sql = "INSERT INTO seg_referral VALUES(
                      $referral_nr, $encounter_nr, '".$diagnosis."', '".$doctor."', '".$dept."', '".$notes."', '".$history."', '".$creator."', '".$today."', '".$creator."', '".$today."', $refer, '".$date."', 'ok', ''
                      );";             
        
        //echo "sql = ".$this->sql;
        if ($this->result=$db->Execute($this->sql)) {
            return TRUE;
        }
        else{
            return FALSE;
        }
    }
    
    /* function editReferral
    *  @author Raissa 01/05/09
    *  @access public
    *  @internal Function for editing a transfer or referral admission
    *  @param String encounter_nr, refer, date, referral_nr, doctor, dept, diagnosis, notes, creator
    *  @return Boolean returns a success or fail in the query   
    */
    function editReferral($encounter_nr, $refer, $date, $referral_nr, $doctor, $dept, $diagnosis, $notes, $creator) {
        global $db;
        $this->sql = "SELECT history FROM seg_referral WHERE referral_nr='".$referral_nr."';";
        if ($this->result=$db->Execute($this->sql)) {
            $a = $this->result->FetchRow();
            $history = $a["history"];
        }
        $today = date('Y-m-d H:i:s');
        $date = date("Y-m-d",strtotime($date));
        $history = $history ."\nUpdated: " .$today;
        $this->sql = "UPDATE seg_referral SET
                       referrer_diagnosis = '".$diagnosis."',
                       referrer_dr = '".$doctor."',
                       referrer_dept = '".$dept."',
                       referrer_notes = '".$notes."',
                       history = '".$history."',
                       modify_id = '".$creator."',
                       modify_time = '".$today."', 
                       is_referral = ".$refer.",
                       referral_date = '".$date."'
                      WHERE referral_nr='".$referral_nr."';";
        
        //echo "sql = ".$this->sql;
        if ($this->result=$db->Execute($this->sql)) {
            return TRUE;
        }
        else{
            return FALSE;
        }
    }
    
    /* function cancelReferral
    *  @author Raissa 01/10/09
    *  @access public
    *  @internal Function for cancelling a transfer or referral admission
    *  @param String referral_nr, reason
    *  @return Boolean returns a success or fail in the query   
    */
    function cancelReferral($referral_nr, $reason, $creator) {
        global $db;
        $this->sql = "SELECT history FROM seg_referral WHERE referral_nr='".$referral_nr."';";
        if ($this->result=$db->Execute($this->sql)) {
            $a = $this->result->FetchRow();
            $history = $a["history"];
        }
        $today = date('Y-m-d H:i:s');
        $history = $history ."\nDeleted: " .$today;
        $this->sql = "UPDATE seg_referral SET
                       status = 'deleted',
                       cancel_reason = '".$reason."',
                       history = '".$history."',
                       modify_id = '".$creator."',
                       modify_time = '".$today."'
                      WHERE referral_nr='".$referral_nr."';";
        
        //echo "sql = ".$this->sql;
        if ($this->result=$db->Execute($this->sql)) {
            return TRUE;
        }
        else{
            return FALSE;
        }
    }
?>
