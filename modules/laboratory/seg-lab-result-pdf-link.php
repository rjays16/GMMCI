<?php
	# created by VAN 01-12-2012
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require('./roots.php');
    require($root_path.'include/inc_environment_global.php');
    require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
    $objInfo = new Hospital_Admin();
    
    require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
    $srvObj=new SegLab();
    
    global $db;
    
    $row_hosp = $objInfo->getAllHospitalInfo();
    $details = (object) 'details';
    
    $refno = $_GET['refno'];
    $refNoBasicInfo = $srvObj->getBasicPatientInfo($refno);
    extract($refNoBasicInfo);
    
    $details->POH_PAT_ID = trim($pid);
    $row_order = $srvObj->getLabOrderNoLIMIT($refno);
    $details->POH_ORDER_NO = $row_order['lis_order_no'];
     
    #pid_orderno
    $file_pdf = $details->POH_PAT_ID."_".$details->POH_ORDER_NO.".pdf";
        
    #viewing from database
    $sql = "SELECT * FROM seg_hl7_pdffile_received WHERE filename='".$file_pdf."'  
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
        #$text2 = "Error : ".$sql;
        #echo "<html><head></head><body>".$text2."</body></html>";
        echo '<em class="warn">Sorry but the page cannot be displayed! There is no result at all. Pending Status..</em>';
    }   
    
?>
