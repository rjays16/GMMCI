<?php
    #include("roots.php");
    require('./roots.php');
    
    #include_once($root_path."/classes/fpdf/fpdf.php");
    include_once($root_path."/classes/fpdf/pdf.class.php");
    require_once($root_path.'include/inc_environment_global.php');
    include_once($root_path.'include/inc_date_format_functions.php');
    
    require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
    $srvObj=new SegLab;
    require_once($root_path.'include/care_api_classes/class_department.php');
    $dept_obj=new Department;
    require_once($root_path.'include/care_api_classes/class_person.php');
    $person_obj=new Person;
    require_once($root_path.'include/care_api_classes/class_encounter.php');
    $enc_obj=new Encounter;
    require_once($root_path.'include/care_api_classes/class_personell.php');
    $pers_obj=new Personell;
    require_once($root_path.'include/care_api_classes/class_ward.php');
    $ward_obj=new Ward;
    
    require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
    $objInfo = new Hospital_Admin();
    
    require($root_path.'classes/adodb/adodb.inc.php');
    include($root_path.'include/inc_init_hclab_main.php');
    #include($root_path.'include/inc_seg_mylib.php');
    
    require_once($root_path.'include/care_api_classes/class_hclab_oracle.php');
    $hclabObj = new HCLAB;
    
    global $db;
    
    $pdf = new PDF("P",'mm','Letter');
    $pdf->AliasNbPages();   #--added
    $pdf->AddPage("P");
        
    $borderYes="1";
    $borderNo="0";
    $newLineYes="1";
    $newLineNo="0";
    $space=2;
    
    //$pdf->Image($root_path.'gui/img/logos/dmc_logo.jpg',20,10,20,20);
  $pdf->Image($root_path.'gui/img/logos/dmc_logo.jpg',30,10,20,20);
  #$pdf->Image($root_path.'gui/img/logos/hospital_logo.jpg',164,10,22,20);
    
    if ($row = $objInfo->getAllHospitalInfo()) {            
        $row['hosp_agency'] = strtoupper($row['hosp_agency']);
        $row['hosp_name']   = strtoupper($row['hosp_name']);
    }
    else {
        $row['hosp_country'] = "Republic of the Philippines";
        $row['hosp_agency']  = "DEPARTMENT OF HEALTH";
        $row['hosp_name']    = "Gonzales Maranan Medical Center Incorporated";
        $row['hosp_addr1']   = "Quezon Ave., Digos City, Davao del Sur";            
    }
        
    $pdf->SetFont("Times","B","10");
   #$pdf->Cell(0,4,'Republic of the Philippines',$borderNo,$newLineYes,'C');
    $pdf->Cell(0,4,$row['hosp_country'],$borderNo,$newLineYes,'C');
    $pdf->Ln(1);
    #$pdf->Cell(0,4,'DEPARTMENT OF HEALTH', $border_0,1,'C');
    $pdf->Cell(0,4,$row['hosp_agency'], $border_0,1,'C');
    $pdf->Ln(2);
    #$pdf->Cell(0,4,'Gonzales Maranan Medical Center Incorporated',$borderNo,$newLineYes,'C');
    $pdf->Cell(0,4,$row['hosp_name'],$borderNo,$newLineYes,'C');
    #$pdf->Cell(0,4,'OUTPATIENT and PREVENTIVE CARE CENTER',$borderNo,$newLineYes,'C');
    $pdf->Ln(2);
    $pdf->SetFont("Times","B","8");
   #$pdf->Cell(0,4,'JICA Bldg., JP Laurel Avenue, Davao City',$borderNo,$newLineYes,'C');
    $pdf->Cell(0,4,$row['hosp_addr1'],$borderNo,$newLineYes,'C');
   $pdf->Ln(2);
    $pdf->SetFont("Times","B","10");
   $pdf->Cell(0,4,'LIST OF CURRENT HOSPITAL SERVICE CHARGES',$borderNo,$newLineYes,'C');
    $pdf->Ln(2);
    
    $report_info = $srvObj->getAllLabServiceChargesByGroup();
    $totalcount = $srvObj->count;
    
    $pdf->Ln($space*4);
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(10,4,'',"",0,'L');
    $pdf->Cell(130,4,'PROCEDURES',"TBLR",0,'C');
    $pdf->Cell(50,4,'RATE',"TBLR",0,'C');
    $pdf->Ln($space*2);
    if ($totalcount){
            $i=1;
            $pdf->SetFont('Times','',9);    
            $grp_code ='';
            while ($row=$report_info->FetchRow()){
                if($grp_code!=$row['group_code']){
                    $grp_code = $row['group_code'];
                    $pdf->Cell(10,4,'',"",0,'L');
                    $pdf->Cell(180,4,'',"TBLR",0,'L');
                    $pdf->Ln($space*2);
                    $pdf->Cell(10,4,'',"",0,'L');
                    $pdf->Cell(180,4,strtoupper($row['grp_name']),"TBLR",0,'L');
                    $pdf->Ln($space*2);
                }
                $pdf->Cell(10,4,'',"",0,'L');
                $pdf->Cell(130,4,$row['name'],"TBLR",0,'C');
                $pdf->Cell(50,4,$row['price_cash'],"TBLR",0,'C');
                $pdf->Ln($space*2);
            }    
          
        
    }else{
        $pdf->SetFont('Times','',10);    
        $pdf->Ln($space*4);
        $pdf->Cell(190,4,'No lab service charges available at this time...',"",0,'C');
    }
    
    $pdf->Output();    
?>