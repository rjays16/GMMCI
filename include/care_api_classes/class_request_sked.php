<?php
// Class for updating `seg_test_request_sked` table.
// Created: 9-19-2006 (Bernard Klinch S. Clarito II)

require('./roots.php');	
require_once($root_path.'include/care_api_classes/class_core.php');

# echo "hello, this is from class SegRequestSked <br>";
class SegRequestSked extends Core{
	/**
	* Database table for the medical depot transaction price
	* @var string
	*/
	var $tb_request_sked='seg_test_request_sked';
	/**
	* Table name for encounter (admission) data
	* @var string
	*/
    var $tb_enc='care_encounter';
	/**
	* Table name for person (registration) data
	* @var string
	*/
	var $tb_person='care_person';
	/**
	* Table name for personnel data
	* @var string
	*/
	var $tb_personell='care_personell';
	/**
	* Fieldnames of the 'seg_test_request_sked' table.
	* @var array
	*/	
	var $fld_request_sked=array(
		"batch_nr",
		"personell_nr",
		"status"
		);

	/**
	* SQL query result. Resulting ADODB record object.
	* @var object
	*/
	var $requests;

	/**
	* Resulting record count
	* @var int
	*/
	var $record_count;
	
	/**
	* Sets the core object to point to seg_med_retail and corresponding field names.
	*/
	function useRequestSked(){
		$this->coretable=$this->tb_request_sked;
		$this->ref_array=$this->fld_request_sked;
	}

        /*  where to put?      ???.php
        *   Get the doctor's information given the 'batch_nr' of a request
        *   
	    *   @access public
        *   @param int Batch number
        *   @return boolean OR the array of information of a doctor assigned in a request containing            
        *                        personell_nr, lastname, firstname, title, sex, pid
        */
function test_print($item2, $key) 
{
    echo "$key. $item2<br />\n";
}
        function getDoctorName($batch_nr){
	    global $db;

           $this->record_count = 0;

           $sql="SELECT r.personell_nr, p.name_last, p.name_first, p.title, p.sex, p.pid
                    FROM $this->tb_request_sked AS r, $this->tb_personell AS q, $this->tb_person AS p
		            WHERE r.batch_nr = ".$batch_nr." AND
					      r.personell_nr = q.nr AND
						  q.pid=p.pid";
#		            WHERE status='pending' OR status='received' ORDER BY  send_date ASC";
#           echo "getDoctorName : sql = $sql <br> ";
           if($this->requests=$db->Execute($sql)){
	          if($this->record_count=$this->requests->RecordCount()){
                 echo "getDoctorName TRUE <br>";
				 #array_walk($this->requests->FetchRow(), 'test_print');
				 return $this->requests;
              }else{
                 echo "getDoctorName FALSE 01 <br>";
                 return FALSE;
              }
	       }else{
              echo "getDoctorName FALSE 02 <br>";
              return FALSE;
           }
        }/* end of function getPendingRequest */

        /**
        *   Get the pending test requests 
        *  
	    *   @access public
        *   @param string Table name
        *   @return boolean OR the list of undone (Pending) requests containing            
        *                        batch_nr,encounter_nr,send_date,dept_nr, status,
		*                        lastname, firstname, date of birth, sex, pid
        *                        in ASCENDING order i.e. from least recent to most 
        *                        recent :-) para sabot-able!
        */
        function getPendingRequest($db_request_table){
	    global $db;

           $this->record_count = 0;
		   $my_table = "care_test_request_".$db_request_table;
           $sql="SELECT r.batch_nr, r.encounter_nr, r.send_date, r.dept_nr, r.status,
		                p.name_last, p.name_first, p.date_birth, p.sex, p.pid
                    FROM ".$my_table." AS r, $this->tb_enc AS e, $this->tb_person AS p
		            WHERE r.status<>'done' AND
					      r.encounter_nr=e.encounter_nr AND
						  e.pid=p.pid
					ORDER BY r.send_date ASC";
#		            WHERE status='pending' OR status='received' ORDER BY  send_date ASC";
           echo "sql BURN = $sql <br> ";
           if($this->requests=$db->Execute($sql)){
	          if($this->record_count=$this->requests->RecordCount()){
                 echo "getPendingRequest TRUE <br>";
				 return $this->requests;
              }else{
                 echo "getPendingRequest FALSE 01 <br>";
                 return FALSE;
              }
	       }else{
              echo "getPendingRequest FALSE 02 <br>";
              return FALSE;
           }
        }/* end of function getPendingRequest */

        function getDOC2BeAssigned(&$DOCDuty){

              /* Attending doctor gets the 1st undone request. */
           if ($DOCDuty['fr'] < $DOCDuty['fa']){
              /* assign to the Resident Doctor */
              $doctorID = $DOCDuty['hr'];
              $DOCDuty['fr'] = $DOCDuty['fr'] + 1;
			  echo "assign to the Resident Doctor <br>";
           }else{
              /* assign to the Attending Doctor */
              $doctorID = $DOCDuty['ha'];
              $DOCDuty['fa']= $DOCDuty['fa'] + 1;
			  echo "assign to the Attending Doctor <br>";			 
           }
           return $doctorID;
        }/* end of updateFrequency */
        
        /**
	    *   Assigns an undone request to a DOC or doctor
	    *      - i think a new table is needed for this; storage of the assignments
        *      - uses the 'seg_test_request_sked' table
	    *   @access public
	    *   @param object info of a Request
	    *   @param array of DOC information
        *   @return boolean:
        *         TRUE, successfully assigned an undone request to a DOC or doctor;
        *         otherwise, FALSE.
    	*/
        function assignRequest2DOC($request,&$DOCDuty,$encoder_id){
              /*  take note of the '&' prepend! This is 2 get any changes 
              *   in the frequency field of the array. 
              */
           
              /* $request: batch_nr,encounter_nr,send_date,dept_nr, status */
           $this->useRequestSked();
              /* Attending doctor gets the 1st undone request. */
           if ($DOCDuty['fr'] < $DOCDuty['fa']){
              /* assign to the Resident Doctor */
              $doctorID = $DOCDuty['hr'];
              $DOCDuty['fr'] = $DOCDuty['fr'] + 1;
			  echo "assign to the Resident Doctor <br>";
           }else{
              /* assign to the Attending Doctor */
              $doctorID = $DOCDuty['ha'];
              $DOCDuty['fa']= $DOCDuty['fa'] + 1;
			  echo "assign to the Attending Doctor <br>";
           }
           $temp_nr = $request['batch_nr'];
		   $temp_stat = $request['status'];

		   $this->sql="SELECT 1 FROM $this->coretable WHERE batch_nr=$temp_nr AND personell_nr=$doctorID AND status='$temp_stat'";
           if($buf=$db->Execute($this->sql)) {
              if($buf->RecordCount()) {				
                    # The entry is ALREADY in the table. NO need to update! 
                 echo "The entry is already in the table! <br>";
                 return TRUE;
              } else {
                 $this->sql="SELECT 1 FROM $this->coretable WHERE batch_nr=$temp_nr";
                 if(!$buf=$db->Execute($this->sql)) {
                    if($buf->RecordCount()) {
                          # The entry is ALREADY existing but needs to be UPDATED in the table.                       
                       $this_history=$this->ConcatHistory("Updated : ".date('Y-m-d H:i:s')." = ".$encoder_id."\n");
                       $this->sql = "UPDATE $this->coretable SET personell_nr=$doctorID, status='$temp_stat', history='$this_history',modify_id='$encoder_id',modify_dt=NOW() WHERE batch_nr=$temp_nr";
                       echo "The entry is UPDATED in the table! <br>";
                    }else{
                          # The entry is NOT yet in the table.
                       $this_history="Created: ".date('Y-m-d H:i:s')." = ".$encoder_id."\n";
                       $this->sql = "INSERT INTO $this->coretable (batch_nr, personell_nr, status, history, modify_id, modify_dt, create_id, create_dt) 
                                            VALUES ($temp_nr,$doctorID,'$temp_stat','$this_history','$encoder_id',NOW(),'$encoder_id',NOW())";
                       echo "The entry is NOT yet in the table! <br>";
                    }
                       // return the SQL Transaction result 
                    return $this->Transact();
                 } else { 
                       // SQL Error occurred
                    return FALSE; 
                 }
              }
           } else { 
                 // SQL Error occurred
              return FALSE; 
           }
        }/* end of function assignRequest2DOC */

        /**
	    *   Assigns an undone request to a DOC or doctor
	    *      - i think a new table is needed for this; storage of the assignments
        *      - uses the 'seg_test_request_sked' table
	    *   @access public
	    *   @param object info of a Request
	    *   @param array of DOC information
		*   @param string Encoder ID
        *   @return boolean:
        *         TRUE, successfully assigned an undone request to a DOC or doctor;
        *         otherwise, FALSE.
    	*/
        function assignRequest2DOC_manual($request,$doctorID,$encoder_id){
           global $db;
           $temp_nr = $request['batch_nr'];
		   $temp_stat = $request['status'];
           
           $this->useRequestSked();

           /* error trapping. if the entry is ALREADY existing */
/*
           $this_history="Created: ".date('Y-m-d H:i:s')." = ".$encoder_id."\n";
           $this->sql = "INSERT INTO $this->coretable (batch_nr, personell_nr, status, history, modify_id, modify_dt, create_id, create_dt) 
                                VALUES ($temp_nr,$doctorID,'$temp_stat','$this_history','$encoder_id',NOW(),'$encoder_id',NOW())";
*/
           echo "Entered function 'assignRequest2DOC_manual'! <br>";        	
		   /*
		   *  TO-DO : 
		   *     <1> if the oldDoctorID==newDoctorID AND oldStatus==newStatus, do not increment frequency
		   *     <2> if the oldDoctorID==newDoctorID AND oldStatus!=newStatus, increment frequency
		   *     <3> if the oldDoctorID!=newDoctorID AND oldStatus==newStatus, increment frequency of newDoctorID
		   *               decrement frequency of oldDoctorID
		   *     <4> if the oldDoctorID!=newDoctorID AND oldStatus!=newStatus, increment frequency of newDoctorID
		   *               decrement frequency of oldDoctorID
		   */

		   $sql="SELECT 1 FROM $this->coretable WHERE batch_nr=$temp_nr AND personell_nr=$doctorID AND status='$temp_stat'";
		   echo " The 'sql-01'  in  function 'assignRequest2DOC_manual' ".$sql." <br> ";
		   # echo " burn :-) this->Transact() = ".$this->Transact()." <br> ";
           if($buf=$db->Execute($sql)) {
		      echo " burn :-) this far? buf->RecordCount()".$buf->RecordCount()." <br> ";
              if($buf->RecordCount()) {				
                    # The entry is ALREADY in the table. NO need to update! 
                 echo "The entry is already in the table! <br>";
                 return TRUE;
              } else {
                 $sql="SELECT 1 FROM $this->coretable WHERE batch_nr=$temp_nr";
		         echo " The 'sql-02'  in  function 'assignRequest2DOC_manual' ".$sql." <br> ";
                 if($buf=$db->Execute($sql)) {
		            echo " burn :-) this far? -02 buf->RecordCount()".$buf->RecordCount()." <br> ";
                    if($buf->RecordCount()) {
                       echo "entering  UPDATE <br> ";
					   /*
					   *  TO-DO : 
					   *     <2> if the oldDoctorID==newDoctorID AND oldStatus!=newStatus, increment frequency
					   *     <3> if the oldDoctorID!=newDoctorID AND oldStatus==newStatus, increment frequency of newDoctorID
					   *               decrement frequency of oldDoctorID
					   *     <4> if the oldDoctorID!=newDoctorID AND oldStatus!=newStatus, increment frequency of newDoctorID
					   *               decrement frequency of oldDoctorID
					   */
                          # The entry is ALREADY existing but needs to be UPDATED in the table.                       
					   if ($this->updateRequestSkedStatus($temp_nr, $doctorID, $temp_stat, $encoder_id)){
                          echo "The entry has been successfully UPDATED in the table! <br>";
						  return TRUE;
					   }else{ 
					      echo "The entry has NOT been successfully UPDATED in the table! <br>";
					      return FALSE; 
					   }
					   # return updateRequestSkedStatus($temp_nr, $doctorID, $temp_stat, $encoder_id);
                    }else{
                       echo "entering  INSERT <br> ";
					   echo " temp_nr = $temp_nr, doctorID=$doctorID, temp_stat=$temp_stat, encoder_id=$encoder_id <br> ";
					      # The entry is NOT yet in the table.						   
					   if ($this->insertRequestSkedStatus($temp_nr, $doctorID, $temp_stat, $encoder_id)){
                          echo "The entry has been successfully INSERTED in the table! <br>";
						  return TRUE;
					   }else{ 
					      echo "The entry has NOT been successfully INSERTED in the table! <br>";
					      return FALSE; 
					   }
					   # return insertRequestSkedStatus($temp_nr, $doctorID, $temp_stat, $encoder_id);
                    }
                 } else { 
                       // SQL Error occurred
                    return FALSE; 
                 }
              }
           } else { 
                 // SQL Error occurred
			  echo "SQL Error occurred outer";
              return FALSE; 
           }
#           return $this->Transact();
        }/* end of function assignRequest2DOC_manual */


        /** 
 	    *   Creates an assigning of a request to a DOC
        *      - uses the 'seg_test_request_sked' table
        *   @access public
        *   @param int Batch number
        *   @param int Person/Doctor's ID number
        *   @param string New status
		*   @param string Encoder ID
        *   @return boolean:
        *         TRUE, successfully inserted a new assignment;
        *         otherwise, FALSE.
	    */
        function insertRequestSkedStatus($nr, $docID, $status, $encoder_id){
           echo "entering function 'insertRequestSkedStatus'  <br> ";
           $this->useRequestSked();
           $this_history="Created: ".date('Y-m-d H:i:s')." = ".$encoder_id."\n";
           $this->sql = "INSERT INTO $this->coretable (batch_nr, personell_nr, status, history, modify_id, modify_dt, create_id, create_dt) 
                                            VALUES ($nr,$docID,'$status','$this_history','$encoder_id',NOW(),'$encoder_id',NOW())";
           echo "exiting function 'insertRequestSkedStatus'  <br> ";
           return $this->Transact();
        }

        /**
 	    *   Updates the status and/or assigned DOC or doctor of a request
        *      - uses the 'seg_test_request_sked' table
        *   @access public
        *   @param int Batch number
        *   @param int Person/Doctor's ID number
        *   @param string New status
		*   @param string Encoder ID
        *   @return boolean:
        *         TRUE, successfully updated the status and/or assigned DOC or doctor of a request;
        *         otherwise, FALSE.
	    */
        function updateRequestSkedStatus($nr, $docID, $newStatus, $encoder_id){
           echo "entering function 'updateRequestSkedStatus'  <br> ";
           $this->useRequestSked();
           $this_history=$this->ConcatHistory("Updated : ".date('Y-m-d H:i:s')." = ".$encoder_id."\n");
           $this->sql = "UPDATE $this->coretable SET personell_nr=$docID, status='$newStatus',history=$this_history,modify_id='$encoder_id',modify_dt=NOW() WHERE batch_nr=$nr";
           echo "exiting function 'updateRequestSkedStatus'  <br> this->sql=$this->sql <br>";
           return $this->Transact();
        }

}/* end of class SegRequestSked */
?>