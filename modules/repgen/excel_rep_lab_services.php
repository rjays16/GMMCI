<?php
  #created by Cherry 01-11-11
  #OB Admission List
  
  error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
  require('./roots.php');
  require($root_path.'include/inc_environment_global.php');
  require_once($root_path."/classes/excel/Writer.php");
  require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
  require_once($root_path.'include/care_api_classes/class_department.php'); 
  
  class ExcelGen_LabServices extends Spreadsheet_Excel_Writer
  {
      var $worksheet;
      var $Headers;
      var $format1, $format2, $format3;
      
      var $from_date;
      var $to_date;
      var $lrow = 4;
     
      function ExcelGen_LabServices($from, $to)
      {
          $this->Spreadsheet_Excel_Writer();
          $this->worksheet = & $this->addWorksheet();
          $this->worksheet->setPaper(1);      // Letter
          $this->worksheet->setLandscape();
          $this->worksheet->setMarginTop(1);
          $this->worksheet->setMarginLeft(0.5);
          $this->worksheet->setMarginRight(0.5);
          $this->worksheet->setMarginBottom(0.5);
          $this->Headers = array(
             'Number of Patients', 'Procedures', 'In-Patient', 'Out-Patient',
             'Total', '% of Grand Total' 
            );
          $this->ColumnWidth = array(51, 17, 17, 20, 20);
          $this->Caption = "LABORATORY SERVICES";
          
          $this->format1=& $this->addFormat();
          $this->format1->setSize(9);
          $this->format1->setBold();
          $this->format1->setAlign('center');
          
          $this->format2=& $this->addFormat();
          $this->format2->setSize(8);
          $this->format2->setAlign('left');
          $this->format2->setTextWrap(1);
          
          $this->format3=& $this->addFormat();
          $this->format3->setSize(9);
          $this->format3->setAlign('center');
          
          $this->format4=& $this->addFormat();
          $this->format4->setSize(9);
          $this->format4->setAlign('left');
          $this->format4->setTextWrap(1); 
          
          if ($from) $this->from=date("Y-m-d",strtotime($from));
          if ($to) $this->to=date("Y-m-d",strtotime($to)); 
      }
      
      function ExcelHeader()
      {
          $dept_obj = new Department();
          $objInfo = new Hospital_Admin();
    
          if ($row = $objInfo->getAllHospitalInfo()) {      
            $row['hosp_agency'] = strtoupper($row['hosp_agency']);
            $row['hosp_name']   = strtoupper($row['hosp_name']);
          }
          else {
            $row['hosp_country'] = "Republic of the Philippines";
            $row['hosp_agency']  = "DEPARTMENT OF HEALTH";
            $row['hosp_name']    = "Gonzales Maranan Medical Center Incorporated";
            $row['hosp_addr1']   = "Quezon Ave., Digos City";      
          }
         
          if ($this->from==$this->to)
            $text = date("F j, Y",strtotime($this->from));
          else
            $text = date("F j, Y",strtotime($this->from))." - ".date("F j, Y",strtotime($this->to));
                      
          #$this->worksheet->setHeader("".$row['hosp_country']."\n".$row['hosp_agency']."\n".$row['hosp_name']."\n".$row['hosp_addr1']."\n\n".$this->Caption."\n".$text."\n",0.5);
          
           $rownum1 = 0;
           $rownum2 = 3;
           $this->len_header = count($this->Headers);   
           for($cnt = 0; $cnt < $this->len_header; $cnt++){
            $this->worksheet->setColumn($rownum1, $cnt, $this->ColumnWidth[$cnt]); 
            $this->worksheet->write($rownum2, $cnt, $this->Headers[$cnt+1], $this->format1); 
           } 
           
           $center = ceil($this->len_header / 2); 
           $this->worksheet->write(0, $center-1, $this->Caption, $this->format3); 
           $this->worksheet->write(1, $center-1, date("m/d/Y",strtotime($this->from))."  ".date("h:i A",strtotime($this->from_time))." - ".date("m/d/Y",strtotime($this->to))."  ".date("h:i A",strtotime($this->to_time)), $this->format3);        
           $this->worksheet->write(2, 1, $this->Headers[0], $this->format1);
          
      }
               
      function FetchData()
      {
       global $db;
       
       if (empty($this->to)) $end_date="NOW()";
       else $end_date=$this->to;
       if (empty($this->from)) $start_date="NOW()";
       else $start_date=$this->from;
          
       $sql = "SELECT * FROM seg_lab_services;";
                    
        $result=$db->Execute($sql);
        $max = $result->RecordCount();
        $ctr = 0;
        $total = 0;
        $temp_array[$max][5];
        $sql = "SELECT * FROM seg_lab_service_groups;";
        $result=$db->Execute($sql);
        $this->_count = $result->RecordCount(); 
        
        if ($result)
        {      
          $this->count = $result->RecordCount(); 
          $i = 1;
          $this->worksheet->write(4, 0, "Laboratory Examination", $this->format4);  
          $newrow=5;
          while ($row=$result->FetchRow())
          {       
            $service_group = $row["name"];
            $group_code = $row["group_code"];
            $temp_array[$ctr][0] = $i .". ".$service_group;
            $temp_array[$ctr][3] = "x";
            $ctr++;
            $j=0;
            $sql = "SELECT * FROM seg_lab_services WHERE group_code='".$group_code."';";
            $result2=$db->Execute($sql);
            $this->_count = $this->_count + $result2->RecordCount(); 
            if($result2){
              while ($row2=$result2->FetchRow()) {
                        $service = $row2["name"];
                        $service_code = $row2["service_code"];
                        //$sql = "SELECT COUNT(refno) as in_patient FROM seg_lab_servdetails WHERE service_code='".$service_code."' AND is_in_house=1;";
                        $sql = "SELECT COUNT(d.refno) as in_patient FROM seg_lab_servdetails as d INNER JOIN seg_lab_serv as s ON s.refno=d.refno WHERE d.service_code='".$service_code."' AND d.is_in_house=1 AND s.serv_dt <'".$this->to_date."' AND s.serv_dt>'".$this->from_date."';";
                        $result3=$db->Execute($sql);
                        if($result3)
                        {
                            $row3=$result3->FetchRow();
                            $inpatient = $row3["in_patient"];
                        }
                        else
                            $inpatient = 0;
                        //$sql = "SELECT COUNT(refno) as out_patient FROM seg_lab_servdetails WHERE service_code='".$service_code."' AND is_in_house=0;";
                        $sql = "SELECT COUNT(d.refno) as in_patient FROM seg_lab_servdetails as d INNER JOIN seg_lab_serv as s ON s.refno=d.refno WHERE d.service_code='".$service_code."' AND d.is_in_house=0 AND s.serv_dt <'".$this->to_date."' AND s.serv_dt>'".$this->from_date."';";
                        $result3=$db->Execute($sql);
                        if($result3)
                        {
                            $row3=$result3->FetchRow();
                            $outpatient = $row3["out_patient"];
                        }
                        else
                            $outpatient = 0;
                        $temp_array[$ctr][0] = "     ".$i.".".$j.". ".$service;
                        //$temp_array[$ctr][0] = $sql;
                        if($inpatient==0)
                            $temp_array[$ctr][1] = "z";
                        else
                            $temp_array[$ctr][1] = $inpatient;
                        if($outpatient==0)
                            $temp_array[$ctr][2] = "z";
                        else
                            $temp_array[$ctr][2] = $outpatient;
                        if($inpatient==0 && $outpatient==0)
                            $temp_array[$ctr][3] = "z";
                        else
                            $temp_array[$ctr][3] = $inpatient + $outpatient;
                        $in_total += $inpatient;
                        $out_total += $outpatient;
                        $ctr++;
                        $j++;
                    }
                 }
                 $i++; 
              }
            }
            
            $total = $in_total + $out_total;
            $per_total = 0;
            for($z=0; $z<$ctr; $z++){
              $prnt_total = $temp_array[$z][3];
              $prnt_inpatient = $temp_array[$z][1];
              $prnt_outpatient = $temp_array[$z][2];
              $temp_array[$z][4] = ($temp_array[$z][3] / $total)*100;
              $per_total += $temp_array[$z][4];
              $prnt_pertotal = number_format($per_total,2)."%";
              if($temp_array[$z][3]=="x")
              {
                  $prnt_total='';
                  $prnt_pertotal='';
              }
              if($temp_array[$z][2]=="z")    
              {
                  $prnt_outpatient="0";
              }
              if($temp_array[$z][1]=="z")    
              {
                  $prnt_inpatient="0";
              }
              if($temp_array[$z][3]=="z")    
              {
                  $prnt_total="0";
                  $prnt_pertotal="0.00%";
              }
              
               $col=0;
               $this->worksheet->write($newrow, $col, $temp_array[$z][0], $this->format2);
               $this->worksheet->write($newrow, $col+1, $prnt_inpatient, $this->format3);
               $this->worksheet->write($newrow, $col+2, $prnt_outpatient, $this->format3);
               $this->worksheet->write($newrow, $col+3, $prnt_total, $this->format3);
               $this->worksheet->write($newrow, $col+4, $prnt_pertotal, $this->format3);
               $newrow++; 
                
            }
            
        $col=0;
        $this->worksheet->write($newrow, $col, 'Grand Total =>', $this->format2);
        $this->worksheet->write($newrow, $col+1, number_format($in_total), $this->format3);
        $this->worksheet->write($newrow, $col+2, number_format($out_total), $this->format3);
        $this->worksheet->write($newrow, $col+3, number_format($total), $this->format3);
        $this->worksheet->write($newrow, $col+4, $per_total."%", $this->format3);
        $newrow++; 
             
          
        
        $this->lrow = $newrow;
      }
      
      function AfterData()
      {
          if (!$this->count) 
          {
            $this->worksheet->write($this->lrow, 0, "No records found for this report...");
          }
      } 
  }
  
  $rep = new ExcelGen_LabServices($_GET['from'], $_GET['to']);
  $rep->ExcelHeader();
  $rep->FetchData();
  $rep->AfterData(); 
  $rep->send('rep_lab_services.xls');
  $rep->close();
?>