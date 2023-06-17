<?php
  #created by Cherry 01-10-11
  #List of Babies with Birth Certificate
  
  error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
  require('./roots.php');
  require($root_path.'include/inc_environment_global.php');
  require_once($root_path."/classes/excel/Writer.php");
  require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
  require_once($root_path.'include/care_api_classes/class_department.php'); 
  
  class ExcelGen_BirthCertList extends Spreadsheet_Excel_Writer
  {
      var $worksheet;
      var $Headers;
      var $format1, $format2, $format3;
      
      var $from, $to;  
      var $lrow = 2;
      
      function ExcelGen_BirthCertList($from, $to)
      {
          $this->Spreadsheet_Excel_Writer();
          $this->worksheet = & $this->addWorksheet();
          $this->worksheet->setPaper(1);      // Letter
          $this->worksheet->setPortrait();
          $this->worksheet->setMarginTop(2.2);
          $this->worksheet->setMarginLeft(0.8);
          $this->worksheet->setMarginRight(0.5);
          $this->worksheet->setMarginBottom(0.5);
          $this->Headers = array(
             '', 'Patient Name', "Mother's Name", 'Birthdate', 'Birth Cert. Created'
            );
          $this->ColumnWidth = array(5, 26, 26, 12, 20);
          $this->Caption = "LIST OF BABIES WITH BIRTH CERTIFICATE";
          
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
          $text = "For ".date("F j, Y",strtotime($this->from));
        else
            #$text = "Full History";
          $text = "From ".date("F j, Y",strtotime($this->from))." To ".date("F j, Y",strtotime($this->to));
                      
          $this->worksheet->setHeader("".$row['hosp_country']."\n".$row['hosp_agency']."\n".$row['hosp_name']."\n".$row['hosp_addr1']."\n\n".$this->Caption."\n".$text."\n",0.5);
          
           $rownum1 = 0;
           $rownum2 = 1;
           $this->len_header = count($this->Headers);   
           for($cnt = 0; $cnt < $this->len_header; $cnt++){
            $this->worksheet->setColumn($rownum1, $cnt, $this->ColumnWidth[$cnt]); 
            $this->worksheet->write($rownum2, $cnt, $this->Headers[$cnt], $this->format1); 
           }         
          
      }
               
      function FetchData()
      {
       global $db;
          
       $sql = "SELECT CONCAT(IFNULL(p.name_last,''),IFNULL(CONCAT(', ', p.name_first),''),IFNULL(CONCAT(' ', p.name_middle),'')) AS patient_name,
        DATE(p.date_birth) AS birthday,
        /*CONCAT(IFNULL(p.mother_lname,''),IFNULL(CONCAT(', ', p.mother_fname),''),IFNULL(CONCAT(' ', p.mother_maidenname),'')) AS mother_name, */
        p.mother_lname, p.mother_fname, p.mother_maidenname, p.mother_mname,
        DATE(scb.create_dt) birth_cert_dt
        from care_person AS p
        INNER JOIN seg_cert_birth AS scb ON scb.pid = p.pid
        WHERE DATE(scb.create_dt) BETWEEN '$this->from' AND '$this->to'
        AND p.status NOT IN ('deleted', 'void', 'hidden')
        ORDER BY patient_name ASC";
                    
        $result=$db->Execute($sql);
        if ($result)
        {      
          $this->count = $result->RecordCount(); 
          $i = 1;
          $newrow=2;
          while ($row=$result->FetchRow())
          {       
          
             if($row['birthday']!='0000-00-00'){
              $bday = date("d M Y", strtotime($row['birthday']));
             }else{
              $bday = "NOT PROVIDED";
             }
             $bcert_dt = date("d M Y", strtotime($row['birth_cert_dt']));

             if($row['mother_lname']=='' || $row['mother_lname']==NULL){
               if($row['mother_maidenname'])
                $mother_name = strtoupper($row['mother_maidenname'].", ".$row['mother_fname']." ".$row['mother_mname']);
               else
                $mother_name = strtoupper($row['mother_fname']." ".$row['mother_mname']);
             }else{
               $mother_name = strtoupper($row['mother_lname'].", ".$row['mother_fname']." ".$row['mother_maidenname']);
             }
 
             $col=0;
             $this->worksheet->write($newrow, $col, $i, $this->format2);
             $this->worksheet->write($newrow, $col+1, mb_strtoupper($row['patient_name']), $this->format2);
             $this->worksheet->write($newrow, $col+2, mb_strtoupper($mother_name), $this->format3);
             $this->worksheet->write($newrow, $col+3, $bday, $this->format3);
             $this->worksheet->write($newrow, $col+4, $bcert_dt, $this->format3);
             $newrow++; 
            $i++;
          }     
        }
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
  
  $rep = new ExcelGen_BirthCertList($_GET['from'], $_GET['to']);
  $rep->ExcelHeader();
  $rep->FetchData();
  $rep->AfterData(); 
  $rep->send('rep_birth_cert_list.xls');
  $rep->close();
?>