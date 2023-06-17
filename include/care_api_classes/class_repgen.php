<?php
	require('./roots.php');
	require_once($root_path.'include/care_api_classes/class_core.php');

	class RepGen extends Core{
		var $tb_report='seg_rep_templates_registry';
        var $tb_rep_category='seg_reptbl_category';
		var $tb_params='seg_rep_params';
		var $tb_temp_params='seg_rep_template_params';

		/**
		* SQL query
		*/
		var $sql;

		/**
		* Constructor
		**/
		function RepGen(){
			$this->bIsConnected = false;
		}

		function getReportCategory(){
			 global $db;

			 $this->sql="SELECT * FROM $this->tb_rep_category ORDER BY name";

			 if ($this->result=$db->Execute($this->sql)) {
					if ($this->count=$this->result->RecordCount()){
						return $this->result;
					}else{
						return FALSE;
					}
			 }else{
					return FALSE;
			 }
		}
        
        function getReportParameter(){
             global $db;

             #edited by VAN 03-02-2013
             $this->sql="SELECT * FROM $this->tb_params WHERE is_active=1 
                         ORDER BY ordering, param_type, parameter";

             if ($this->result=$db->Execute($this->sql)) {
                    if ($this->count=$this->result->RecordCount()){
                        return $this->result;
                    }else{
                        return FALSE;
                    }
             }else{
                    return FALSE;
             }
        }

        #Added by Jarel 05-03-2013
        function getReportParameter2($param_id){
             global $db;

             $this->sql="SELECT * FROM $this->tb_params WHERE is_active=1 
                         AND param_id='$param_id' ORDER BY ordering, param_type, parameter";

             if ($this->result=$db->Execute($this->sql)) {
                    if ($this->count=$this->result->RecordCount()){
                        return $this->result;
                    }else{
                        return FALSE;
                    }
             }else{
                    return FALSE;
             }
        }

        #Added by Jarel 05-03-2013
        function getReportParamById($report_id){
             global $db;

             $this->sql="SELECT * FROM $this->tb_temp_params as tp INNER JOIN $this->tb_params as p ON 
             tp.param_id=p.param_id WHERE tp.report_id='$report_id' ORDER BY p.ordering, p.param_type, p.parameter";

             if ($this->result=$db->Execute($this->sql)) {
                    if ($this->count=$this->result->RecordCount()){
                        return $this->result;
                    }else{
                        return FALSE;
                    }
             }else{
                    return FALSE;
             }
        }

}
?>
