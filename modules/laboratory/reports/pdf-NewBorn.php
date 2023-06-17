<?php
#created by daryl
#NEW BORN
require_once('roots.php');
require_once($root_path.'include/inc_jasperReporting.php');
require_once($root_path.'include/inc_environment_global.php');

include_once($root_path.'include/inc_date_format_functions.php');

require_once($root_path.'include/care_api_classes/class_hospital_admin.php');

require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');



class NewBorn{

    function assignGlobal($quarter,$year){
        global $HTTP_SESSION_VARS;
        $this->quarter = $quarter;
        switch ($quarter) {
            case '0':
               $this->quarter_lbl = "ALL Quarter of ".$year;
                break;
             case '1':
               $this->quarter_lbl = "1st Quarter of ".$year;
                break;
             case '2':
               $this->quarter_lbl = "2nd Quarter of ".$year;
                break;
             case '3':
               $this->quarter_lbl = "3rd Quarter of ".$year;
                break;
             case '4':
               $this->quarter_lbl = "4th Quarter of ".$year;
                break;
        }
        $this->year = $year;
        $this->prepared_by = $HTTP_SESSION_VARS['sess_user_fullname'];

        $this->setHospInfo();

    }

    function setHospInfo(){
        $objInfo = new Hospital_Admin();
        if ($row = $objInfo->getAllHospitalInfo()) {
            $this->hosp_name  = strtoupper($row['hosp_name']);
            $this->hosp_address   = strtoupper($row['hosp_addr1']);
        }
    }#end setHospInfo function


    function setParams(){
        $params = array(
            "hosp_name"=>$this->hosp_name,
            "hosp_address"=>$this->hosp_address,
            "image_path"=>$this->image_path,
            "prepared_by"=>$this->prepared_by,
            "quarter_lbl"=>$this->quarter_lbl,
        );
        return $params;
    }#end setParams function

    function fetchdata(){
        $srvObj=new SegLab;
        $result = $srvObj->getNewBornData($this->quarter, $this->year);
        // echo $srvObj->sql;die;
       
       if($result){
            for ($i=0; $i < count($result)  ; $i++) { 
                if($result[$i]['months']){
                      $data[$i] = array(
                                         "month"=>$result[$i]['months'],
                                         "live_birth"=>(int)$result[$i]['live_birth'],
                                         "neonatal"=>(int)$result[$i]['neonatal'],
                                         "inborn"=>(int)$result[$i]['inborn'],
                                         "outborn"=>(int)$result[$i]['outborn'],
                                         "delivery"=>(int)$result[$i]['delivery'],
                                                    );
                }
    
            }
        }
        else{
                     $data[0] = array(
                                        "month"=>"NO DATA",
                                         "live_birth"=>0,
                                         "neonatal"=>0,
                                         "inborn"=>0,
                                         "outborn"=>0,
                                         "delivery"=>0,
                                                    );
            }
        return $data;
    }
}#end class

$NB_quarter = $_GET['newborn_quarter'];
$NB_year = $_GET['newborn_year'];

$class_NewBorn = new NewBorn();

$class_NewBorn->assignGlobal($NB_quarter,$NB_year);
$params = $class_NewBorn->setParams();
$data = $class_NewBorn->fetchdata();

showReport('LB_NB_screening',$params,$data,"PDF");


?>