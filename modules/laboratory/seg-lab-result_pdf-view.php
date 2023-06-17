<?php
    #for cron schedule
    #per minute
	# created by VAN 01-12-2012
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require('./roots.php');
    require($root_path.'include/inc_environment_global.php');
    require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
    $objInfo = new Hospital_Admin();
    
    require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
    $srvObj=new SegLab();
    
    $row_hosp = $objInfo->getAllHospitalInfo();
    $details = (object) 'details';
    $details->address = $row_hosp['LIS_address'];
    $details->directory_inbox = "\\\\".$details->address.$row_hosp['LIS_folder_path_inbox'];
    $details->directory_pdf = "\\\\".$details->address.$row_hosp['LIS_folder_path_pdf'];
    
    ##getting the binary file and viewing
    global $db;
    
    $pid = $_GET['pid'];
    $lis_order_no = $_GET['lis'];
    $filename = $pid.'_'.$lis_order_no.'.pdf';
    $sql = "SELECT * FROM seg_hl7_pdffile_received WHERE filename='".$filename."'  
            ORDER BY date_received DESC
            LIMIT 1";
    
    $rs = $db->execute($sql);
    if ($db->Affected_Rows()) {
        $row = $rs->FetchRow();
        $message = $row['hl7_msg'];
        $filename = $row['filename'];
        
        header("Content-type: application/pdf");    
        echo $message;
    }else{
        $text2 = "Error : ".$sql;
        echo "<html><head></head><body>".$text2."</body></html>";
    }    
    
?>
