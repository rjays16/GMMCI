<?php
  #created by Cherry 01-11-11
  #OB Admission List
  
  error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
  require('./roots.php');
  require($root_path.'include/inc_environment_global.php');
  require_once($root_path."/classes/excel/Writer.php");
  require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
  require_once($root_path.'include/care_api_classes/class_personell.php');  
  
  class ExcelGen_Stat_ICD_Encoded extends Spreadsheet_Excel_Writer
  {
      var $worksheet;
      var $Headers;
      var $format1, $format2, $format3;
      
      var $fromdate;
      var $todate;
      var $lrow = 4;
     
      function ExcelGen_Stat_ICD_Encoded($fromdate, $todate, $ptype, $dept, $encoder)
      {
          $this->Spreadsheet_Excel_Writer();
          $this->worksheet = & $this->addWorksheet();
          $this->worksheet->setPaper(1);      // Letter
          $this->worksheet->setPortrait();
          $this->worksheet->setMarginTop(1.9);
          $this->worksheet->setMarginLeft(0.9);
          $this->worksheet->setMarginRight(0.5);
          $this->worksheet->setMarginBottom(0.5);
          $this->Headers = array(
             'DEPARTMENT', 'NPHIC', 'PHIC'
            );
          $this->ColumnWidth = array(45, 20, 20);
          $this->Caption = "STATISTICS FOR ICD ENCODED";
          
          $this->format1=& $this->addFormat();
          $this->format1->setSize(9);
          $this->format1->setBold();
          $this->format1->setAlign('center');
          
          $this->format1_total=& $this->addFormat();
          $this->format1_total->setSize(9);
          $this->format1_total->setBold();
          $this->format1_total->setAlign('right');
          
          $this->format2=& $this->addFormat();
          $this->format2->setSize(8);
          $this->format2->setAlign('left');
          $this->format2->setTextWrap(1);
          
          $this->format3=& $this->addFormat();
          $this->format3->setSize(9);
          $this->format3->setAlign('center');
          $this->format3->setTextWrap(1);
          
          $this->format4=& $this->addFormat();
          $this->format4->setSize(9);
          $this->format4->setBold();
          $this->format4->setAlign('left');
          
          if ($fromdate) $this->fromdate=date("Y-m-d",strtotime($fromdate));
          if ($todate) $this->todate=date("Y-m-d",strtotime($todate));
          
          $this->encoder = $encoder;
          $this->dept = $dept;
          $this->ptype = $ptype;
          
      }
      
      function ExcelHeader()
      {
          $pers_obj=new Personell; 
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
         
          if ($this->fromdate==$this->todate)
            $text = "For ".date("F j, Y",strtotime($this->fromdate));
          else
            $text = "From ".date("F j, Y",strtotime($this->fromdate))." To ".date("F j, Y",strtotime($this->todate));
                      
          $this->worksheet->setHeader("".$row['hosp_country']."\n".$row['hosp_agency']."\n".$row['hosp_name']."\n".$row['hosp_addr1']."\n\n".$this->Caption."\n".$text."\n",0.5);
          
           $rownum1 = 0;
           $rownum2 = 3;
           $this->len_header = count($this->Headers);   
           for($cnt = 0; $cnt < $this->len_header; $cnt++){
            $this->worksheet->setColumn($rownum1, $cnt, $this->ColumnWidth[$cnt]); 
            $this->worksheet->write($rownum2, $cnt, $this->Headers[$cnt], $this->format1); 
           }         
          
          if ($this->encoder!='all'){
            $row = $pers_obj->getPersonellInfo($this->encoder);
            $string = mb_strtoupper($row["name_last"]).", ".mb_strtoupper($row["name_first"])." ".mb_strtoupper($row["name_middle"]);
          }else{
            $string = "ALL MEDICAL RECORDS PERSONNEL";
          }
          
          $this->worksheet->write(0, 0, "Encoder :"."  ".$string, $this->format4);
          
      }
               
      function FetchData()
      {
       global $db;
       $pers_obj=new Personell;
       $this->total_nphic = 0;
       $this->total_phic = 0;
       
       if ($this->encoder!='all'){
        $enc = $pers_obj->getPersonellInfo($this->encoder);
        $encoder_fullname = ucwords(strtolower($enc["name_first"]))." ".ucwords(strtolower($enc["name_last"]));
        $this->encoder_fullname = "AND ced.create_id = '".$encoder_fullname."'";
        //$this->Cell($total_w,4,'SOCIAL WORKER : '.$this->encoder_fullname,$border2,1,'C');
      }else{
        //$this->Cell($total_w,4,'ALL SOCIAL WORKERS',$border2,1,'C');
        $this->encoder_fullname = "";
      }
      
      if($this->dept){
        $sql_dept = " AND ce.current_dept_nr=".$this->dept_nr;
      }
      
      if($this->ptype == 'all'){
        $include_ptype = "";
      }elseif($this->ptype == '1' || $this->ptype == '2'){
        $include_ptype = "AND e.encounter_type = '".$this->ptype."'";
      }else{
        $include_ptype = "AND e.encounter_type IN ('".$this->ptype."')";
      }
       
       $sql = "SELECT d.name_formal AS department ,SUM(CASE WHEN cpi.hcare_id=18 then 1 else 0 end) AS PHIC,
            SUM(CASE WHEN cpi.hcare_id!=18 then 1 else 0 end) AS NPHIC
            FROM care_encounter_diagnosis AS ced
            INNER JOIN care_encounter AS e ON e.encounter_nr = ced.encounter_nr
            LEFT JOIN care_person_insurance AS cpi ON cpi.pid = e.pid
            INNER JOIN care_department AS d ON d.nr = e.current_dept_nr
            WHERE ced.code IS NOT NULL
            AND ced.status NOT IN('deleted', 'void', 'hidden', 'cancelled')
            AND e.status NOT IN ('deleted', 'void', 'hidden', 'cancelled')
            $include_ptype
            $sql_dept
            $this->encoder_fullname
            AND DATE(ced.create_time) BETWEEN '".$this->fromdate."' AND '".$this->todate."'
            GROUP BY d.name_formal;";
                    
        $result=$db->Execute($sql);
        if ($result)
        {      
          $this->count = $result->RecordCount(); 
          $i = 1;
          $newrow=4;
          while ($row=$result->FetchRow())
          {       
             $this->total_nphic += $row['NPHIC'];
             $this->total_phic += $row['PHIC']; 
 
             $col=0;
             $this->worksheet->write($newrow, $col, mb_strtoupper($row['department']), $this->format2);
             $this->worksheet->write($newrow, $col+1, $row['NPHIC'], $this->format3);
             $this->worksheet->write($newrow, $col+2, $row['PHIC'], $this->format3);
             
             $newrow++; 
            $i++;
          }
          
            /*$col=0;
            $this->worksheet->write($newrow, $col, "GRAND TOTAL", $this->format1_total);
            $this->worksheet->write($newrow, $col+1, $this->total_nphic, $this->format3);
            $this->worksheet->write($newrow, $col+2, $this->total_phic, $this->format3);
            $newrow++;                                                  */
               
        }
        $this->worksheet->write(1, 0, "# of Records : ".$this->count, $this->format4); 
        $this->lrow = $newrow;
      }
      
      function AfterData()
      {
          if (!$this->count) 
          {
            $this->worksheet->write($this->lrow, 0, "No records found for this report...");
          }else{
            $col=0;
            $this->worksheet->write($this->lrow, $col, "GRAND TOTAL", $this->format1_total);
            $this->worksheet->write($this->lrow, $col+1, $this->total_nphic, $this->format3);
            $this->worksheet->write($this->lrow, $col+2, $this->total_phic, $this->format3);
          }
      } 
  }
  $fromdate = $_GET['fromdate'];
  $todate = $_GET['todate'];
  $ptype = $_GET['ptype'];
  $dept = $_GET['dept_nr_sub'];
  $encoder = $_GET['encoder'];
  
  $rep = new ExcelGen_Stat_ICD_Encoded($fromdate, $todate, $ptype, $dept, $encoder);
  $rep->ExcelHeader();
  $rep->FetchData();
  $rep->AfterData(); 
  $rep->send('rep_stat_icd_encoded.xls');
  $rep->close();
?>