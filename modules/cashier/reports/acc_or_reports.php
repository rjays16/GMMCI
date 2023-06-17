<?php
#created by daryl
#Accomodation and OR income report
require_once('roots.php');
require_once($root_path.'include/inc_jasperReporting.php');
require_once($root_path.'include/inc_environment_global.php');

include_once($root_path.'include/inc_date_format_functions.php');

require_once($root_path.'include/care_api_classes/class_hospital_admin.php');

include_once($root_path."include/care_api_classes/class_cashier_service.php");


class accOR{

    function assignGlobal($type,$encoder,$datestart,$dateend){
        global $HTTP_SESSION_VARS;
        $this->type = $type;
        $this->encoder = $encoder;
        $this->datestart = $datestart;
        $this->dateend = $dateend;
        $this->prepared_by = $HTTP_SESSION_VARS['sess_user_fullname'];

        $this->setHospInfo();

        switch ($type) {
            case '0':
                echo "PLEASE SELECT TYPE OF REPORT";die;
                break;
            case 'acc':
                $this->type_lbl = "Accomodation Income";
                break;
            case 'or':
                $this->type_lbl = "Operating Room Income";
                break;
        }
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
            "type"=>$this->type_lbl,
            "encoder"=>$this->type_lbl,
            "date_"=>date("F d, Y",strtotime($this->datestart))."       to          ".date("F d, Y",strtotime($this->dateend)),
        );
        return $params;
    }#end setParams function

    function fetchdata(){
        $cashier_obj=new SegCashierService;
        $result = $cashier_obj->Accomodation_OR_data($this->type, $this->encoder, $this->datestart, $this->dateend);
        // echo $cashier_obj->sql;die;
        for ($i=0; $i < count($result)  ; $i++) { 
            if($result[$i]['or_amount'] > 0){
                    if($result[$i]['or_no']){
                          $data[$i] = array(
                                             "or_no"=>$result[$i]['or_no'],
                                             "or_date"=>$result[$i]['or_date'],
                                             "or_name"=>$result[$i]['or_name'],
                                             "or_amount"=>(double)$result[$i]['or_amount'],
                                             "or_encoder"=>$result[$i]['or_encoder'],
                                                        );
                    }else{
                         $data[$i] = array(
                                             "or_no"=>"NO DATA",
                                             "or_date"=>"NO DATA",
                                             "or_name"=>"NO DATA",
                                             "or_amount"=>"NO DATA",
                                             "or_encoder"=>"NO DATA",
                                                        );
                    }
            }
    
        }

        return $data;
    }
}#end class

$type = $_GET['type'];
$encoder = $_GET['encoder'];
$datestart = date("Y-m-d", strtotime($_GET['datestart'] ));
$dateend = date("Y-m-d", strtotime($_GET['dateend'] ));

$class_accOR = new accOR();

$class_accOR->assignGlobal($type,$encoder,$datestart,$dateend);
$params = $class_accOR->setParams();
$data = $class_accOR->fetchdata();

showReport('acc_or_income',$params,$data,"PDF");


?>